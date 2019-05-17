<?php declare(strict_types=1);

namespace App\Action;

use App\Feed;
use App\Stitcher\Api;
use Illuminate\Support\Carbon;
use App\Item;

class RefreshShow
{
    /** @var Api */
    protected $client;

    public function __construct(Api $client)
    {
        $this->client = $client;
    }

    public function refresh(Feed $feed)
    {
        $response = $this->client->get('GetFeedDetailsWithEpisodes.php', [
            'query' => [
                'fid' => $feed->id,
            ]
        ]);

        $response = new \SimpleXMLElement($response->getBody()->__toString());


        $this->processFeed($feed, $response->feed);
        $this->processItems($feed, $response->episodes->episode);
    }

    protected function processFeed(Feed $feed, \SimpleXMLElement $response)
    {
        $feed->title = (string)$response->name;
        $feed->description = (string)$response->description;
        $feed->image_url = (string)$response['imageURL'];
        $feed->is_premium = (bool)$response['premium'];
        $feed->last_refresh = Carbon::now();

        $feed->save();

        // Laravel overwrites ID on save
        $feed->id = (int)$response['id'];
    }

    protected function processItems(Feed $feed, \SimpleXMLElement $episodes)
    {
        $items = $feed->items->keyBy('id');

        foreach ($episodes as $xml_item) {

            $id = (int)$xml_item['id'];
            $item = $items->get($id) ?? Item::make();

            $item->id = $id;
            $item->feed_id = $feed->id;
            $item->title = (string)$xml_item->title;
            $item->description = (string)$xml_item->description;
            $item->pub_date = Carbon::make((string)$xml_item['published']);
            $item->itunes_duration = (int)$xml_item['duration'];
            $item->enclosure_url = (string)$xml_item['url'];

            $item->save();

            // Laravel overwrites ID on save
            $item->id = $id;
        }
    }
}
