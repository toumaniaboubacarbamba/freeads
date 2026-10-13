<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'title', 'description', 'price', 'location', 'condition'];

    protected function casts(): array
    {
        return ['price' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AdPhoto::class)->orderBy('position');
    }

    protected function coverUrl(): Attribute
    {
        return Attribute::get(fn () => $this->photos->first()?->url);
    }

    protected function priceFormatted(): Attribute
    {
        return Attribute::get(fn () => number_format($this->price, 0, ',', "\u{00A0}")."\u{00A0}FCFA");
    }

    protected function conditionLabel(): Attribute
    {
        return Attribute::get(fn () => config('akwaba.conditions')[$this->condition] ?? $this->condition);
    }
}
