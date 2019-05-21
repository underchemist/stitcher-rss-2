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

        return $_SESSION['user'] ?? null;
    }

    public function resolveFromBasicAuth(Request $request): ?User
    {
        $user = $request->getUser();
        $pass = $request->getPassword();

        return $this->getUser($user, $pass);
    }

    public function resolveFromRoute(Request $request): ?User
    {
        $user = $request->route()[2]['rss_user'] ?? null;
        $pass = $request->route()[2]['rss_pass'] ?? null;

        return $this->getUser($user, $pass);
    }

    protected function getUser($user, $pass): ?User
    {
        if (!$user || !$pass) {
            return null;
        }

        $user = User::where([
            'rss_user' => $user,
            'rss_password' => $pass,
        ])->first();

        return $user;
    }
}
