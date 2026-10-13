<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

$fillable = ['name', 'slug'];
class Category extends Model
{
    //
    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }
}
