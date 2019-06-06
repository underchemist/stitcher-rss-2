<?php declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Feed extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'is_premium',
        'title',
        'description',
        'image_url',
        'premium_id',
        'last_refresh',
        'last_change',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'last_refresh',
        'last_change',
    ];

    public function dueForRefresh()
    {
        if ($this->last_refresh === null) {
            return true;
        }

        return $this->last_refresh < Carbon::now()->modify('-60 minutes');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public static function isPremium(
        \SimpleXMLElement $element,
        ?Feed $feed = null
    ): bool {

        $bypassed_feeds = explode(',', env('FEED_BYPASS', ''));

        if (in_array((int)$element['id'], $bypassed_feeds)) {
            $is_premium = true;
        } elseif ($feed !== null && (int)$element['id'] != $feed->id) {
            $is_premium = false;
        } elseif ($element['authRequired'] && $element['authRequired']->__toString()) {
            $is_premium = false;
        } else {
            $is_premium = (bool)$element['premium']->__toString();
        }

        return $is_premium;
    }

    public function setImageUrlAttribute($image_url)
    {
        $this->attributes['image_url'] = str_replace(
            'https://secureimg.stitcher.com/',
            'https://s3.amazonaws.com/stitcher.assets/',
            $image_url
        );
    }
}
