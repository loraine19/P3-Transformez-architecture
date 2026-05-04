<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/* CLASS */
class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
    ];

    /* PUBLIC METHOD */
    /* user */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* PUBLIC METHOD */
    /* notes */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
