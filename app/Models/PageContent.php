<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageContent extends Model
{
    protected $fillable = [
        'page_id',
        'content',
        'active_from',
        'active_to'
    ];

    protected function casts(): array
    {
        return [
            'active_from' => 'datetime',
            'active_to' => 'datetime',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('active_to')
              ->orWhere('active_to', '>', now());
        })->where(function ($q) {
            $q->whereNull('active_from')
              ->orWhere('active_from', '<=', now());
        });
    }
}
