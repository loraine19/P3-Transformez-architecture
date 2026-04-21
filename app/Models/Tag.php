<?php
// Tag model - category for notes, one tag can have many notes
// only name field, no color or other fields

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/* CLASS */
class Tag extends Model
{
    use HasFactory;

    // DONE: Added user ownership field for tag security.
    // only these fields are allowed for mass assignment
    protected $fillable = [
        'user_id',
        'name',
    ];

    /* PUBLIC METHOD */
    /* user */
    public function user()
    {
        // DONE: Tag now belongs to one user.
        return $this->belongsTo(User::class);
    }

    /* PUBLIC METHOD */
    /* notes */
    public function notes()
    {
        // one tag has many notes - like a one-to-many in sql
        return $this->hasMany(Note::class);
    }
}
