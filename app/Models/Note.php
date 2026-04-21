<?php
// Note model - one note belongs to one user and one tag
// fillable = only these fields can be saved in db

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/* CLASS */
class Note extends Model
{
    use HasFactory;

    // like props - only these fields allowed for mass assignment
    protected $fillable = [
        'user_id',
        'tag_id',
        'text',
    ];

    /* PUBLIC METHOD */
    /* user */
    public function user()
    {
        // like a join - get the user who owns this note
        return $this->belongsTo(User::class);
    }

    /* PUBLIC METHOD */
    /* tag */
    public function tag()
    {
        // like a join - get the tag linked to this note
        return $this->belongsTo(Tag::class);
    }
}
