<?php

namespace App\Http\Controllers;

use App\Feed;
use App\Stitcher\Api;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ConnectException;

class ShowController extends Controller
{
    public function shows(Request $request, Api $client)
    {
        $feeds = null;
        $notice = null;
        $term = $request->input('q');

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
            if (!(int)$feed['premium']) {
                continue;
            }

            $feeds[] = Feed::make([
                'id' => (integer)$feed['id'],
                'title' => (string)$feed->name,
                'description' => (string)$feed->description,
                'image_url' => (string)$feed['imageURL'],
                'is_premium' => (bool)$feed['premium'],
            ]);
        }

        return $feeds;
    }

    public function feed()
    {
        throw new \Exception("To Implement");
    }

    public function episode()
    {
        throw new \Exception("To Implement");
    }
}
