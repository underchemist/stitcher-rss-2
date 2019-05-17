<?php declare(strict_types=1);

namespace App\Providers;

use App\Application;
use App\Auth\User;
use Illuminate\Support\ServiceProvider;
use App\Auth\UserManager;
use Illuminate\Http\Request;

class UserProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(User::class, function (Application $app) {
            // @todo There's probably a better way to inject a user
            // without having to manually resolve manager and request.
            $manager = $app->make(UserManager::class);
            $request = $app->make(Request::class);
            return $manager->resolve($request);
        });
    }
}
