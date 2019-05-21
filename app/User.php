<?php declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Stitcher\Api;

class User extends Model
{
    use \Spiritix\LadaCache\Database\LadaCacheTrait;

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

    const PREMIUM_REFRESH_TIME = '6 hours';

    public function hasPremium(): bool
    {
        if (!$this->stitcher_id) {
            return false;
        }

        if ($this->expiration > new \DateTime()) {
            return true;
        }

        // Check against Stitcher's API if it's been more than threshold
        $threshold = new \DateTime('-' . self::PREMIUM_REFRESH_TIME);
        if ($this->updated_at < $threshold) {
            return $this->checkPremiumStatus();
        }

        return false;
    }

    protected function checkPremiumStatus(): bool
    {
        throw new \Exception('Todo');

        $client = app(Api::class);

        $result = $client->get('GetSubscriptionStatus.php', [
            'uid' => $this->stitcher_id
        ]);

        if (!$result->subscriptionExpiration) {
            return false;
        }

        $expiration = new \DateTime(
            $result->subscriptionExpiration,
            new \DateTimeZone('America/Los_Angeles')
        );

        if ($expiration == $this->expiration) {
            return false;
        }

        $this->expiration = $expiration;

        $user = get_object_vars($this);
        unset($user['client']);

        app('db')
            ->table('users')
            ->where('id', $this->id)
            ->update($user);

        $now = new \DateTime();

        return ($this->expiration >= $now);
    }
}
