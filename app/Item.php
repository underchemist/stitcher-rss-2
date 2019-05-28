<?php declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Item extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'feed_id',
        'title',
        'description',
        'pub_date',
        'itunes_duration',
        'enclosure_url',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'pub_date'
    ];

    public function setItunesDurationAttribute($duration)
    {
        if (is_numeric($duration)) {
            $HH = floor($duration / 3600);
            $MM = ($duration / 60) % 60;
            $SS = $duration % 60;
            $duration = sprintf("%02d:%02d:%02d", $HH, $MM, $SS);
        }

        $this->attributes['itunes_duration'] = $duration;
    }

    public function getPubDateAttribute($pub_date)
    {
        $date = date_create($pub_date);

        if (!$date) {
            $date = new \DateTime('@0');
        }

        return Carbon::instance($date);
    }
}
