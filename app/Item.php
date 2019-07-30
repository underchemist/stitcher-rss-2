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

    protected function asDateTime($value)
    {
        try {
            return parent::asDateTime($value);
        } catch (\InvalidArgumentException $e) {
            return Carbon::make('@0');
        }
    }
}
