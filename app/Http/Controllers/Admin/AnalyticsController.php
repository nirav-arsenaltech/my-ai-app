<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('admin.analytics.index');
    }

    public function data(Request $request): JsonResponse
    {
        $range = $request->query('range', '7d');

        $query = AiUsage::query();

        $now = now();
        switch ($range) {
            case '1d':
                $query->where('created_at', '>=', $now->copy()->subDay());
                $groupByFormat = '%H:00'; // Group by hour
                $dbFormat = '%Y-%m-%d %H:00:00';
                break;
            case '7d':
                $query->where('created_at', '>=', $now->copy()->subDays(7));
                $groupByFormat = '%b %d'; // Group by day
                $dbFormat = '%Y-%m-%d';
                break;
            case '30d':
                $query->where('created_at', '>=', $now->copy()->subDays(30));
                $groupByFormat = '%b %d'; // Group by day
                $dbFormat = '%Y-%m-%d';
                break;
            case 'all':
            default:
                $groupByFormat = '%Y-%m'; // Group by month
                $dbFormat = '%Y-%m';
                break;
        }

        // Summary Stats
        $stats = [
            'total_tokens' => (clone $query)->sum('total_tokens'),
            'total_requests' => (clone $query)->count(),
            'avg_latency' => round((clone $query)->avg('latency_ms') ?? 0),
            'cost_estimate' => 0,
        ];

        // Chart Data (Grouped by time)
        // Note: Using SQLite specific datetime formatting if sqlite, or MySQL if mysql.
        // We'll use a generic approach or check driver. Assuming SQLite/MySQL compatible or just use Carbon in PHP.
        // Actually, grouping in PHP is safer for cross-database compatibility when dealing with formatting.

        $usages = $query->with('user:id,name')->orderBy('created_at', 'asc')->get();

        $chartData = [];
        $sourceData = ['conversation' => 0, 'telegram' => 0, 'notes' => 0];
        $userStats = [];
        $totalCost = 0;

        foreach ($usages as $usage) {
            // Dynamic Cost Calculation
            $totalCost += AiUsage::calculateCost($usage->model, $usage->prompt_tokens, $usage->completion_tokens);
            $label = match ($range) {
                '1d' => $usage->created_at->format('H:00'),
                '7d', '30d' => $usage->created_at->format('M d'),
                'all', 'default' => $usage->created_at->format('M Y'),
            };

            if (! isset($chartData[$label])) {
                $chartData[$label] = [
                    'label' => $label,
                    'prompt_tokens' => 0,
                    'completion_tokens' => 0,
                    'requests' => 0,
                ];
            }

            $chartData[$label]['prompt_tokens'] += $usage->prompt_tokens;
            $chartData[$label]['completion_tokens'] += $usage->completion_tokens;
            $chartData[$label]['requests'] += 1;

            // Source (Telegram vs Conversation vs Notes)
            $source = $usage->metadata['source'] ?? 'conversation';
            if ($source === 'web') {
                $source = 'conversation';
            }
            if (! isset($sourceData[$source])) {
                $sourceData[$source] = 0;
            }
            $sourceData[$source] += $usage->total_tokens;

            // Top Users
            if ($usage->user_id) {
                if (! isset($userStats[$usage->user_id])) {
                    $userStats[$usage->user_id] = [
                        'name' => $usage->user ? $usage->user->name : 'User #'.$usage->user_id,
                        'tokens' => 0,
                    ];
                }
                $userStats[$usage->user_id]['tokens'] += $usage->total_tokens;
            }
        }

        // Sort Top 5 Users
        $stats['cost_estimate'] = round($totalCost, 4);

        usort($userStats, fn ($a, $b) => $b['tokens'] <=> $a['tokens']);
        $topUsers = array_slice($userStats, 0, 5);

        return response()->json([
            'stats' => $stats,
            'chart' => array_values($chartData),
            'source_chart' => $sourceData,
            'top_users' => $topUsers,
        ]);
    }
}
