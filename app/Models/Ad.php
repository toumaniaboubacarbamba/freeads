<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeFilter(Builder $query, array $f): Builder
{
    return $query
        ->when($f['q'] ?? null, fn ($q, $term) => $q->where(
            fn ($w) => $w->where('title', 'like', "%{$term}%")
                         ->orWhere('description', 'like', "%{$term}%")
        ))
        ->when($f['categories'] ?? null, fn ($q, $ids) => $q->whereIn('category_id', $ids))
        ->when($f['location'] ?? null, fn ($q, $loc) => $q->where('location', $loc))
        ->when(($f['price_min'] ?? '') !== '', fn ($q) => $q->where('price', '>=', (int) $f['price_min']))
        ->when(($f['price_max'] ?? '') !== '', fn ($q) => $q->where('price', '<=', (int) $f['price_max']))
        ->when($f['condition'] ?? null, fn ($q, $c) => $q->where('condition', $c));
}
}
