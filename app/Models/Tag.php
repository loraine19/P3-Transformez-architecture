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

    // only name is allowed for mass assignment
    protected $fillable = [
        'name',
    ];

    /* PUBLIC METHOD */
    /* notes */
    public function notes()
    {
        // one tag has many notes - like a one-to-many in sql
        return $this->hasMany(Note::class);
    }
}
