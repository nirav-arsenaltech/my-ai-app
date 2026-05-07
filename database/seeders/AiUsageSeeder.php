<?php

namespace Database\Seeders;

use App\Models\AiUsage;
use App\Models\User;
use Illuminate\Database\Seeder;

class AiUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $now = now();
        for ($i = 0; $i < 100; $i++) {
            $date = $now->copy()->subHours(rand(0, 24 * 30));
            $prompt = rand(100, 5000);
            $completion = rand(50, 1000);

            $source = rand(0, 100) > 30 ? 'web' : 'telegram';

            AiUsage::create([
                'user_id' => $users->random()->id,
                'provider' => 'google',
                'model' => 'gemini-1.5-flash',
                'type' => 'chat',
                'prompt_tokens' => $prompt,
                'completion_tokens' => $completion,
                'total_tokens' => $prompt + $completion,
                'latency_ms' => rand(800, 5000),
                'metadata' => ['source' => $source],
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
