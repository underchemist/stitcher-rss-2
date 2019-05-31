<?php declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Stitcher\Api;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stitcher_id',
        'rss_user',
        'rss_password',
        'expiration',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expiration'
    ];

    const PREMIUM_REFRESH_INTERVAL = '2 hours';

    public function hasPremium(): bool
    {
        if (!$this->stitcher_id) {
            return false;
        }

        if ($this->expiration > new \DateTime()) {
            return true;
        }

        // Check against Stitcher's API if it's been more than threshold
        $threshold = new \DateTime('-' . self::PREMIUM_REFRESH_INTERVAL);
        if ($this->updated_at < $threshold) {
            return $this->checkPremiumStatus();
        }

        return false;
    }

    protected function checkPremiumStatus(): bool
    {
        $client = app(Api::class);

        $response = $client->get('GetSubscriptionStatus.php');

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response->getBody()->__toString());
        libxml_clear_errors();

        $sub_state = (int)$xml['subscriptionState'];
        $sub_expiration = (string)$xml['subscriptionExpiration'];

        if ($sub_state != 3 || !$sub_expiration) {
            $this->touch();
            return false;
        }

        $expiration = new \DateTime($sub_expiration);

        if ($expiration == $this->expiration) {
            $this->touch();
            return ($this->expiration >= new \DateTime());
        }

        $this->expiration = $expiration;
        $this->save();

        return ($this->expiration >= new \DateTime());
    }
}
