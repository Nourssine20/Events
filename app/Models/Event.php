<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
       use HasFactory;
    protected $fillable = ['title', 'starts_at','ends_at', 'user_id'];
    protected $casts = [
    'starts_at' => 'datetime:Y-m-d H:i',
    'ends_at' => 'datetime:Y-m-d H:i',
];



    /**
     * Gets the user the event belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include events that occure between two dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  DateTime  $startsAt
     * @param  DateTime  $endsAt
     * @return \Illuminate\Database\Eloquent\Builder
     */
 public function scopeIsBetween($query, $startsAt, $endsAt)
{
    if ($startsAt) {
        $query->where('starts_at', '>=', Carbon::parse($startsAt));
    }
    if ($endsAt) {
        $query->where('starts_at', '<=', Carbon::parse($endsAt));
    }
    return $query;
}


    /**
     * Scope a query to only include events that occure between two dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  DateTime  $startsAt
     * @param  DateTime  $endsAt
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByDate($query)
    {
        return $query->orderBy('starts_at');
    }

}
