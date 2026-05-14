<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content_heading',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function contents()
    {
        return $this->hasMany(PageContent::class)->orderBy('active_from');
    }

    public function getContentHeadingAttribute()
    {
        return $this->content_heading ?? $this->title;
    }

    public function getCurrentContent()
    {
        $now = now();

        return $this->contents()
            ->where(function ($query) use ($now) {
                $query->whereNull('active_from')
                    ->orWhere('active_from', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('active_to')
                    ->orWhere('active_to', '>=', $now);
            })
            ->first();
    }
}
