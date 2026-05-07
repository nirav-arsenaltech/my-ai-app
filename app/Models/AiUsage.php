<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsage extends Model
{
    protected $fillable = [
        'user_id',
        'message_id',
        'provider',
        'model',
        'type',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'latency_ms',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
        'latency_ms' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Calculate cost based on model and token usage.
     */
    public static function calculateCost(string $model, int $promptTokens, int $completionTokens): float
    {
        // Pricing per 1M tokens (based on provided data, using <=200K rates for simplicity where applicable)
        $pricing = [
            // Gemini 3.x Series (Preview)
            'gemini-3.1-pro' => ['prompt' => 2.00, 'completion' => 12.00],
            'gemini-3-pro' => ['prompt' => 2.00, 'completion' => 12.00],
            'gemini-3-flash' => ['prompt' => 0.50, 'completion' => 3.00],
            'gemini-3.1-flash-lite' => ['prompt' => 0.25, 'completion' => 1.50],

            // Gemini 2.5 Series
            'gemini-2.5-pro' => ['prompt' => 1.25, 'completion' => 10.00],
            'gemini-2.5-flash-lite' => ['prompt' => 0.10, 'completion' => 0.40],
            'gemini-2.5-flash' => ['prompt' => 0.30, 'completion' => 2.50],

            // Gemini 2.0 Series
            'gemini-2.0-flash-lite' => ['prompt' => 0.075, 'completion' => 0.30],
            'gemini-flash-lite-latest' => ['prompt' => 0.075, 'completion' => 0.30], // Alias for 2.0 lite
            'gemini-2.0-flash' => ['prompt' => 0.10, 'completion' => 0.40],

            // Legacy / 1.5 Series
            'gemini-1.5-pro' => ['prompt' => 1.25, 'completion' => 5.00],
            'gemini-1.5-flash-8b' => ['prompt' => 0.0375, 'completion' => 0.15],
            'gemini-1.5-flash' => ['prompt' => 0.075, 'completion' => 0.30],

            // Specialized/Other
            'text-embedding' => ['prompt' => 0.15, 'completion' => 0.00],
            'gemini-pro' => ['prompt' => 0.50, 'completion' => 1.50],
        ];
        // Extract base model name if it has extra version suffixes or default to fallback
        $baseModel = $model;
        foreach (array_keys($pricing) as $knownModel) {
            if (str_contains($model, $knownModel)) {
                $baseModel = $knownModel;
                break;
            }
        }

        $rates = $pricing[$baseModel] ?? ['prompt' => 0.15, 'completion' => 0.15]; // Default fallback

        $promptCost = ($promptTokens / 1_000_000) * $rates['prompt'];
        $completionCost = ($completionTokens / 1_000_000) * $rates['completion'];

        return $promptCost + $completionCost;
    }

    public function getEstimatedCostAttribute(): float
    {
        return self::calculateCost($this->model, $this->prompt_tokens, $this->completion_tokens);
    }
}
