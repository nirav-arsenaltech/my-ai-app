<?php

namespace App\Services;

use App\Models\AiUsage;
use App\Models\Conversation;
use App\Models\Document;
use App\Models\KnowledgeDocument;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RagService
{
    public function __construct(
        private readonly OpenAIService $openAI,
        private readonly TextChunker $chunker,
    ) {
    }

    public function ingest(
        int $userId,
        string $title,
        string $content,
        ?string $sourceName = null,
        string $sourceType = 'text',
    ): KnowledgeDocument {
        $chunks = $this->chunker->split($content);
        $embeddings = $this->openAI->embeddings($chunks);

        return DB::transaction(function () use ($userId, $title, $content, $sourceName, $sourceType, $chunks, $embeddings) {
            $document = KnowledgeDocument::create([
                'user_id' => $userId,
                'title' => $title,
                'source_name' => $sourceName,
                'source_type' => $sourceType,
                'original_content' => $content,
                'chunk_count' => count($chunks),
            ]);

            $documentData = [];
            $now = now();
            foreach ($chunks as $index => $chunk) {
                $documentData[] = [
                    'knowledge_document_id' => $document->id,
                    'content' => $chunk,
                    'embedding' => json_encode($embeddings[$index] ?? [], JSON_THROW_ON_ERROR),
                    'chunk_index' => $index,
                    'character_count' => mb_strlen($chunk),
                    'source_name' => $sourceName ?? $title,
                    'metadata' => json_encode([
                        'title' => $title,
                        'source_type' => $sourceType,
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('documents')->insert($documentData);

            return $document->loadCount('chunks');
        });
    }

    public function reindex(
        KnowledgeDocument $document,
        string $title,
        string $content,
        ?string $sourceName = null,
        string $sourceType = 'text',
    ): KnowledgeDocument {
        $chunks = $this->chunker->split($content);
        $embeddings = $this->openAI->embeddings($chunks);

        return DB::transaction(function () use ($document, $title, $content, $sourceName, $sourceType, $chunks, $embeddings) {
            $document->chunks()->delete();

            $document->update([
                'title' => $title,
                'source_name' => $sourceName,
                'source_type' => $sourceType,
                'original_content' => $content,
                'chunk_count' => count($chunks),
            ]);

            $documentData = [];
            $now = now();
            foreach ($chunks as $index => $chunk) {
                $documentData[] = [
                    'knowledge_document_id' => $document->id,
                    'content' => $chunk,
                    'embedding' => json_encode($embeddings[$index] ?? [], JSON_THROW_ON_ERROR),
                    'chunk_index' => $index,
                    'character_count' => mb_strlen($chunk),
                    'source_name' => $sourceName ?? $title,
                    'metadata' => json_encode([
                        'title' => $title,
                        'source_type' => $sourceType,
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('documents')->insert($documentData);

            return $document->fresh()->loadCount('chunks');
        });
    }

    public function answer(Conversation $conversation, string $question, string $source = 'conversation'): array
    {
        $history = $conversation->messages()
            ->latest()
            ->take(8)
            ->get(['role', 'content'])
            ->reverse()
            ->values()
            ->map(fn (Message $message) => [
                'role' => $message->role,
                'content' => $message->content,
            ])
            ->all();

        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => $question,
        ]);

        $matches = $this->retrieveRelevantChunks($conversation->user_id, $question);

        if ($matches === []) {
            $assistantMessage = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => 'I could not find relevant information in the uploaded knowledge base. Add documents or ask a question that matches the current data.',
                'citations' => [],
            ]);

            $this->touchConversation($conversation, $question);

            return [
                'conversation' => $conversation->fresh(),
                'user_message' => $userMessage,
                'assistant_message' => $assistantMessage,
            ];
        }

        $context = collect($matches)
            ->map(fn (array $match, int $index) => sprintf(
                "[Source %d | %s | chunk %d]\n%s",
                $index + 1,
                $match['title'],
                $match['chunk_index'] + 1,
                $match['content']
            ))
            ->implode("\n\n");

        $startTime = microtime(true);
        $response = $this->openAI->answerQuestion($question, $context, $history);
        $latency = (int) ((microtime(true) - $startTime) * 1000);

        $assistantMessage = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $response['content'],
            'citations' => collect($matches)->map(fn (array $match) => [
                'document_id' => $match['knowledge_document_id'],
                'title' => $match['title'],
                'source_name' => $match['source_name'],
                'chunk_index' => $match['chunk_index'],
                'score' => round($match['score'], 4),
            ])->all(),
            'meta' => [
                'usage' => $response['usage'],
            ],
        ]);

        defer(fn () => AiUsage::create([
            'user_id' => $conversation->user_id,
            'message_id' => $assistantMessage->id,
            'provider' => $response['meta']['provider'] ?? 'google',
            'model' => $response['meta']['model'] ?? config('services.gemini.chat_model', 'gemini-1.5-flash'),
            'type' => 'chat',
            'prompt_tokens' => $response['usage']['prompt_tokens'] ?? 0,
            'completion_tokens' => $response['usage']['completion_tokens'] ?? 0,
            'total_tokens' => $response['usage']['total_tokens'] ?? (($response['usage']['prompt_tokens'] ?? 0) + ($response['usage']['completion_tokens'] ?? 0)),
            'latency_ms' => $latency,
            'metadata' => ['source' => $source],
        ]));

        $this->touchConversation($conversation, $question);

        return [
            'conversation' => $conversation->fresh(),
            'user_message' => $userMessage,
            'assistant_message' => $assistantMessage,
        ];
    }

    /**
     * Generate answer without storing conversation or messages (stateless)
     */
    public function statelessAnswer(int $userId, string $question, string $source = 'telegram'): string
    {
        $matches = $this->retrieveRelevantChunks($userId, $question);

        if ($matches === []) {
            return 'I could not find relevant information in the uploaded knowledge base. Add documents or ask a question that matches the current data.';
        }

        $context = collect($matches)
            ->map(fn (array $match, int $index) => sprintf(
                "[Source %d | %s | chunk %d]\n%s",
                $index + 1,
                $match['title'],
                $match['chunk_index'] + 1,
                $match['content']
            ))
            ->implode("\n\n");

        $startTime = microtime(true);
        $response = $this->openAI->answerQuestion($question, $context, []);
        $latency = (int) ((microtime(true) - $startTime) * 1000);

        defer(fn () => AiUsage::create([
            'user_id' => $userId,
            'message_id' => null,
            'provider' => $response['meta']['provider'] ?? 'google',
            'model' => $response['meta']['model'] ?? config('services.gemini.chat_model', 'gemini-1.5-flash'),
            'type' => 'chat',
            'prompt_tokens' => $response['usage']['prompt_tokens'] ?? 0,
            'completion_tokens' => $response['usage']['completion_tokens'] ?? 0,
            'total_tokens' => $response['usage']['total_tokens'] ?? (($response['usage']['prompt_tokens'] ?? 0) + ($response['usage']['completion_tokens'] ?? 0)),
            'latency_ms' => $latency,
            'metadata' => ['source' => $source],
        ]));

        return $response['content'];
    }

    public function retrieveRelevantChunks(int $userId, string $question, int $limit = 4): array
    {
        $questionEmbedding = $this->openAI->embedding($question);
        $vectorString = '['.implode(',', $questionEmbedding).']';

        $chunks = Document::query()
            ->select(['id', 'knowledge_document_id', 'content', 'chunk_index', 'source_name'])
            ->selectRaw('(1 - (embedding <=> ?::vector)) as similarity_score', [$vectorString])
            ->whereHas('knowledgeDocument', fn ($query) => $query->where('user_id', $userId))
            ->whereRaw('(1 - (embedding <=> ?::vector)) >= ?', [$vectorString, 0.45])
            ->orderByRaw('embedding <=> ?::vector', [$vectorString])
            ->with('knowledgeDocument:id,title,source_name')
            ->limit($limit * 3)
            ->get();

        if ($chunks->isEmpty()) {
            return [];
        }

            return $chunks->map(fn (Document $chunk) => [
                'knowledge_document_id' => $chunk->knowledge_document_id,
                'title' => $chunk->knowledgeDocument?->title ?? 'Untitled document',
                'source_name' => $chunk->source_name ?? $chunk->knowledgeDocument?->source_name,
                'content' => $chunk->content,
                'chunk_index' => (int) $chunk->chunk_index,
                'score' => (float) $chunk->similarity_score,
            ])->all();
    }

    private function touchConversation(Conversation $conversation, string $question): void
    {
        $title = $conversation->messages()->where('role', 'user')->count() === 1
            ? Str::limit($question, 60, '...')
            : $conversation->title;

        $conversation->update([
            'title' => $title,
            'last_message_at' => now(),
        ]);
    }

    public function generateResponse(string $question, int $userId): string
    {
        $matches = $this->retrieveRelevantChunks($userId, $question);

        if ($matches === []) {
            return 'I could not find relevant information in your uploaded knowledge base. Please add documents or ask a question that matches your current data.';
        }

        $context = collect($matches)
            ->map(fn (array $match, int $index) => sprintf(
                "[Source %d | %s | chunk %d]\n%s",
                $index + 1,
                $match['title'],
                $match['chunk_index'] + 1,
                $match['content']
            ))
            ->implode("\n\n");

        $response = $this->openAI->answerQuestion($question, $context, []);

        return $response['content'];
    }
}
