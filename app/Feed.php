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
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'last_refresh'
    ];

    public function dueForRefresh()
    {
        if ($this->last_refresh === null) {
            return true;
        }

        return $this->last_refresh < Carbon::now()->modify('-5 minutes');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
