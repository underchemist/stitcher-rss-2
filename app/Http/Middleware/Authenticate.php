<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, string $type)
    {
        if ($request->user() && $request->user()->hasPremium()) {
            return $next($request);
        }

        if (in_array($type, ['basic', 'route'])) {
            $headers = [
                'WWW-Authenticate' => 'Basic realm="Unofficial RSS Feeds for Stitcher Premium"'
            ];

            return response("Unauthorized\n", 401, $headers);
        } else {
            return redirect('/login', 302);
        }
    }
}
