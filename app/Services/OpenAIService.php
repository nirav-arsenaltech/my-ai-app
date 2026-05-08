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
        $instructions = <<<PROMPT
            You are a knowledge base assistant.

            Rules:
            - Answer ONLY from the provided context.
            - If the answer is missing, say:
            "The information is not available in the knowledge base."
            - Keep answers short, clear, and direct.
            - Do not explain unnecessary details.
            - Do not repeat the question.
            - Do not make assumptions or invent information.
            - Use plain text formatting only.
            - Mention the source only if explicitly available in the context.

            Context:
            {$context}
            PROMPT;

        $response = agent(
            instructions: $instructions,
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

                // Truncate long history turns to avoid bloating the prompt
                if (mb_strlen($text) > 400) {
                    $text = mb_substr($text, 0, 400).'…';
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
