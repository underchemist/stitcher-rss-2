<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Action\RefreshShow;
use App\Feed;
use App\Stitcher\Api;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller;
use App\Item;

class ShowController extends Controller
{
    public function shows(Request $request, Api $client)
    {
        $feeds = null;
        $notice = null;
        $term = $request->input('term');

        if (is_string($term) && $term) {
            $feeds = $this->search($term, $client);

            if ($feeds === null) {
                $notice = "We had an issue reaching Stitcher. Here's a list of known shows with premium content.";
            }
        }

        if ($feeds === null) {
            $feeds = Feed::all();
        }

        return view('shows', [
            'notice' => $notice,
            'feeds' => $feeds,
            'term' => $term,
        ]);
    }

    protected function search(string $term, Api $client): ?array
    {
        try {
            $response = $client->get('Search.php', [
                'query' => [
                    'term' => $term,
                    'c' => 50, // count
                ]
            ]);
        } catch (RequestException | ConnectException $ex) {
            Log::notice("Search issue: " . $ex->getMessage());
            return null;
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->getBody()->__toString());
        libxml_clear_errors();

        if ($xml === false) {
            Log::notice("Search invalid XML: " . $response->getBody()->__toString());
            return null;
        }

        if ($xml->getName() == 'error') {
            return null;
        }

        $feeds = [];

        foreach ($xml->feed as $feed) {
            if (!(bool)$feed['premium']->__toString()) {
                continue;
            }

            $feeds[] = Feed::make([
                'id' => (integer)$feed['id'],
                'title' => (string)$feed->name,
                'description' => (string)$feed->description,
                'image_url' => (string)$feed['imageURL'],
            ]);
        }

        return $feeds;
    }

    public function feed(int $feed_id, RefreshShow $refresh)
    {
        if (!$feed_id) {
            abort(404);
        }

        $feed = Feed::where('id', $feed_id)->first();

        if (!$feed) {
            $feed = Feed::make(['id' => $feed_id]);

            try {
                $refresh->refresh($feed);
            } catch (RequestException | ConnectException $ex) {
                Log::notice("Refresh issue: " . $ex->getMessage());
                return response("We had an issue reaching Stitcher.", 503);
            }
        }

        if (!$feed->is_premium) {
            return response("Show is not premium. Please fetch from the original provider.", 404);
        }

        $feed->load('items');

        return response(
            view('feed', ['feed' => $feed]),
            200,
            ['Content-Type' => 'text/xml']
        );
    }

    public function episode(int $feed_id, int $item_id)
    {
        $item = Item::where([
            'id' => $item_id,
            'feed_id' => $feed_id,
        ])->first();

        if (!$item) {
            abort(404);
        }

        return redirect($item->enclosure_url);
    }
}
