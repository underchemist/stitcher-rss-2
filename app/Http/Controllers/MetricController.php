<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use App\User;
use App\Item;
use App\Feed;
use Illuminate\Support\Carbon;

class MetricController extends Controller
{
    public function index(Request $request)
    {
        $is_user = $request->getUser() === env('METRIC_USER', '');
        $is_pass = $request->getPassword() === env('METRIC_PASS', '');

        if (!$is_user || !$is_pass) {
            $headers = ['WWW-Authenticate' => 'Basic realm="Metrics"'];
            return response("Unauthorized\n", 401, $headers);
        }

        $user_total = User::count();
        $user_active = User::whereRaw('expiration > NOW()')->count();

        $feed_total = Feed::count();
        $feed_premium = Feed::where('is_premium', 1)->count();
        $feed_expired = Feed::where('last_refresh', '<', Carbon::make('-1 hour'))->count();
        $feed_oldest = Feed::select('last_refresh')
            ->orderBy('last_refresh', 'asc')
            ->first();

        $expiration = Carbon::make('-1 hour');

        if ($feed_oldest->last_refresh < $expiration) {
            $feed_oldest = $feed_oldest->last_refresh->diffInSeconds($expiration);
        } else {
            $feed_oldest = 0;
        }

        $item_total = Item::count();
        $item_premium = Item::join('feeds', 'feeds.id', '=', 'items.feed_id')
            ->where('is_premium', 1)
            ->count();


        $host_labels = [
            'site' => 'stitcher_rss'
        ];

        $metrics = [
            [
                'name' => 'users',
                'labels' => ['type' => 'all'],
                'value' => $user_total,
            ],
            [
                'name' => 'users',
                'labels' => ['type' => 'active'],
                'value' => $user_active,
            ],
            [
                'name' => 'feeds',
                'labels' => ['type' => 'all'],
                'value' => $feed_total,
            ],
            [
                'name' => 'feeds',
                'labels' => ['type' => 'premium'],
                'value' => $feed_premium,
            ],
            [
                'name' => 'feeds',
                'labels' => ['type' => 'expired'],
                'value' => $feed_expired,
            ],
            [
                'name' => 'feeds',
                'labels' => ['type' => 'expired_age'],
                'value' => $feed_oldest,
            ],
            [
                'name' => 'items',
                'labels' => ['type' => 'all'],
                'value' => $item_total,
            ],
            [
                'name' => 'items',
                'labels' => ['type' => 'premium'],
                'value' => $item_premium,
            ],
        ];

        $view = view('metrics', [
            'host_labels' => $host_labels,
            'metrics' => $metrics
        ]);

        return response($view, 200, [
            'Content-Type' => 'text/plain; version=0.0.4'
        ]);
    }
}
