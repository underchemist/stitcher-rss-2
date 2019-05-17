<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBlockedClient
{
    protected $user_agents = [
        // podcast directories that show basic auth feeds
        'castbox',
        'Player FM',

        // Feed masking services
        'feedburner',
        'RSSMix',

        // Sharing services
        'Slackbot',
    ];

    protected $referers = [
        // podcast directories that show basic auth feeds
        'castbox',
        'player.fm',
        'zhaoji.org',
        'devonwyatt.com',
    ];

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->isUserAgentBlocked($request) || $this->isRefererBlocked($request)) {
            $msg = "Unavailable; This client appears to expose private feeds to unauthenticated users.\n";
            return response($msg, 451);
        }

        return $next($request);
    }

    protected function isUserAgentBlocked(Request $request): bool
    {
        $user_agent = urldecode($request->header('User-Agent')) ?: '';
        $regex = '/(' . implode('|', $this->user_agents) . ')/i';

        return (bool)preg_match($regex, $user_agent);
    }

    protected function isRefererBlocked(Request $request): bool
    {
        $referer = $request->header('Referer') ?: '';
        $regex = '/(' . implode('|', $this->referers) . ')/i';

        return (bool)preg_match($regex, $referer);
    }
}
