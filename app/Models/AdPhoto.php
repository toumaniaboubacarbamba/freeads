<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AdPhoto extends Model
{
    protected $fillable = ['ad_id', 'path', 'position'];

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    protected function url(): Attribute
{
    return Attribute::get(function () {
        $disk = config('akwaba.photo_disk');

        return $disk === 'public'
            ? asset('storage/'.$this->path)
            : Storage::disk($disk)->url($this->path);
    });
}
}
