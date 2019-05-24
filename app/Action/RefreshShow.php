<?php declare(strict_types=1);

namespace App\Action;

use App\Feed;
use App\Item;
use App\Stitcher\Api;
use ForceUTF8\Encoding;
use Illuminate\Support\Carbon;

class RefreshShow
{
    /** @var Api */
    protected $client;

    public function __construct(Api $client)
    {
        $this->client = $client;
    }

    public function refresh(Feed $feed, ?int $user_id = null)
    {
        $query = [
            'fid' => $feed->id,
            'id_Season' => -1,
            's' => 0,
            'c' => 100,
        ];

        if ($user_id !== null) {
            $query['uid'] = $user_id;
        }

        $response = $this->fetch($query);

        $this->processFeed($feed, $response->feed);
        $episodes = $response->episodes->episode;
        $seasons = $this->extractSeasons($response->feed->season);
        $this->processItems($feed, $episodes, $seasons);

        $count = (int)$response->feed['episodeCount'];

        if ($count > $query['c']) {
            while ($query['c'] + $query['s'] < $count) {
                $query['s'] += $query['c'];
                $response = $this->fetch($query);
                $episodes = $response->episodes->episode;
                $seasons = $this->extractSeasons($response->feed->season);
                $this->processItems($feed, $episodes, $seasons);
            }
        }
    }

    protected function fetch(array $query): \SimpleXMLElement
    {
        $response = $this->client->get('GetFeedDetailsWithEpisodes.php', [
            'query' => $query,
        ]);

        $response = new \SimpleXMLElement($response->getBody()->__toString());

        return $response;
    }

    protected function processFeed(Feed $feed, \SimpleXMLElement $response)
    {
        $feed->title = (string)$response->name;
        $feed->description = (string)$response->description;
        $feed->image_url = (string)$response['imageURL'];
        $feed->is_premium = (bool)$response['premium']->__toString();
        $feed->last_refresh = Carbon::now();

        $feed->save();

        // Laravel overwrites ID on save
        $feed->id = (int)$response['id'];
    }

    protected function extractSeasons(\SimpleXMLElement $response): array
    {
        $seasons = [];

        foreach ($response as $season) {
            $id = (int)$season['id'] ?: '';
            $title = (int)$season['title'] ?: null;
            $seasons[$id] = $title;
        }

        return $seasons;
    }

    protected function processItems(Feed $feed, \SimpleXMLElement $episodes, array $seasons)
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
            $item->enclosure_url = (string)$xml_item['url'];
            $item->itunes_duration = (int)$xml_item['duration'];

            $season_id = (int)$xml_item['id_Season'];
            $item->itunes_season = $seasons[$season_id] ?? null;
            $item->itunes_episode = (int)$xml_item['episodeNumber'] ?: null;

            // Attempt to force conversion of input into UTF8
            $item->description = str_replace(chr(194), "", $item->description ?? '');
            $item->description = Encoding::toUTF8($item->description, Encoding::ICONV_IGNORE);
            $item->description = Encoding::fixUTF8($item->description, Encoding::ICONV_IGNORE);

            $item->save();

            // Laravel overwrites ID on save
            $item->id = $id;
        }
    }
}
