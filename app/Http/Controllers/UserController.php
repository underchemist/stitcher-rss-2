<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Stitcher\Api;
use Adduc\Stitcher\Password;
use App\User as User;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
    public function login(Request $request, Api $api, Password $password)
    {
        $result = null;

        if ($request->isMethod('post')) {
            $result = $this->attempt($request, $api, $password);

            if ($result === true) {
                return redirect('shows');
            }
        }

        return view('login', ['notice' => $result]);
    }

    protected function attempt(Request $request, Api $api, Password $password)
    {
        $device_id = config('services.stitcher.device');

        $email = $request->input('email');
        $password = $password->encrypt($device_id, $request->input('password'));

        if (!$email || !$password) {
            return "Both email and password is required.";
        }

        try {
            $response = $api->post('CheckAuthentication.php', [
                'form_params' => [
                    'email' => $email,
                    'epx' => $password,
                ]
            ]);
        } catch (RequestException | ConnectException $ex) {
            Log::notice("CheckAuthentication issue: " . $ex->getMessage());
            return "Stitcher appears to be having trouble. Please try again.";
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->getBody()->__toString());
        libxml_clear_errors();

        if ($xml === false) {
            Log::notice("CheckAuthentication invalid XML: " . $response->getBody()->__toString());
            return "Stitcher appears to be having trouble. Please try again.";
        }

        if ($xml['error'] ?? null) {
            return "Could not login. Check that you entered correct credentials";
        }

        if ($xml['subscriptionState'] != 3) {
            return "You do not appear to be a subscriber to Stitcher Premium.";
        }

        \Illuminate\Support\Facades\DB::connection()->enableQueryLog();

        $user = User::where('stitcher_id', (int)$xml['id'])->first();

        if ($user === null) {
            $user = User::create([
                'stitcher_id' => $xml['id'],
                'rss_user' => random_int(10000, 99999),
                'rss_password' => random_int(10000, 99999),
                'expiration' => new \DateTime((string)$xml['subscriptionExpiration']),
            ]);
        } else {
            $user->expiration = new \DateTime((string)$xml['subscriptionExpiration']);
            // Ensure we always save on login
            $user->updated_at = new \DateTime();
            $user->save();
        }

        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['user'] = $user;
        return true;
    }

    public function logout()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_destroy();
        return redirect('/');
    }
}
