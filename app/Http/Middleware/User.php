<?php declare(strict_types=1);

namespace App\Http\Middleware;

use App\Auth\UserManager;
use Illuminate\Http\Request;

class User
{
    /** @var UserManager */
    protected $manager;

    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $request->setUserResolver(function () use ($request) {
            return $this->manager->resolve($request);
        });

        return $next($request);
    }
}
