<?php declare(strict_types=1);

namespace App\Auth;

use App\User;
use Illuminate\Http\Request;

class UserManager
{
    public function resolve(Request $request): ?User
    {
        $user
            = $this->resolveFromBasicAuth($request)
            ?? $this->resolveFromRoute($request)
            ?? $this->resolveFromSession();

        return $user;
    }

    public function resolveFromSession(): ?User
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['user'])) {
            return null;
        }

        return $_SESSION['user'];
    }

    public function resolveFromBasicAuth(Request $request): ?array
    {
        $user = $request->getUser();
        $pass = $request->getPassword();

        return $this->getUser($user, $pass);
    }

    public function resolveFromRoute(Request $request): ?array
    {
        $user = $request->route()[2]['rss_user'] ?? null;
        $pass = $request->route()[2]['rss_pass'] ?? null;

        return $this->getUser($user, $pass);
    }

    protected function getUser($user, $pass): ?array
    {
        if (!$user || !$pass) {
            return null;
        }

        throw new \Exception("Todo");

        $users = app('db')->select(
            'select * from users where rss_user = ?',
            [$user]
        );

        if (!$users || $users[0]->rss_password != $pass) {
            return null;
        }

        return get_object_vars($users[0]);
    }
}
