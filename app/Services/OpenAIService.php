<?php

namespace App\Services;

use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\Message;
use function Laravel\Ai\agent;

class OpenAIService
{
    public function embedding(string $text): array
    {
        return $this->embeddings([$text])[0] ?? [];
    }

    public function embeddings(array $inputs): array
    {
        return Embeddings::for($inputs)
            ->timeout((int) config('services.gemini.timeout', 60))
            ->generate(
                provider: Lab::Gemini,
                model: (string) config('services.gemini.embedding_model', 'gemini-embedding-001'),
            )
            ->embeddings;
    }

    public function answerQuestion(string $question, string $context, array $history = []): array
    {
        $response = agent(
            instructions: implode("\n", [
                'You are a retrieval-augmented assistant for a Laravel application.',
                'Answer only from the supplied knowledge base context.',
                'If the context is insufficient, say that the answer is not available in the uploaded knowledge base.',
                '',
                "Knowledge base context:\n{$context}",
            ]),
            messages: $this->normalizeHistory($history),
        )->prompt(
            prompt: $question,
            provider: Lab::Gemini,
            model: (string) config('services.gemini.chat_model', 'gemini-2.5-flash-lite'),
        );

        return [
            'content' => trim($response->text),
            'usage' => $response->usage->toArray(),
            'meta' => $response->meta->toArray(),
        ];
    }

    private function normalizeHistory(array $history): array
    {
        return collect($history)
            ->map(function (array $item) {
                $text = trim((string) ($item['content'] ?? ''));

                if ($text === '') {
                    return null;
                }

                return new Message(
                    role: ($item['role'] ?? '') === 'assistant' ? 'assistant' : 'user',
                    content: $text,
                );
            })
            ->filter()
            ->values()
            ->all();
    }
}
