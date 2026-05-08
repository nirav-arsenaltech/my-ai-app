@extends('layouts.admin')

@section('title', 'AI Analytics')

@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <span class="breadcrumb-active">Analytics</span>
@endsection

@section('page-title', 'Analytics')
@section('page-subtitle', 'Monitor AI usage across the platform. Track token consumption, costs, and user activity.')

@push('head')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --surface: #ffffff;
            --surface-alt: #ebedefe0;
            --surface-hover: #f1f5f9;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-tertiary: #94a3b8;
            --brand: #3b82f6;
            --brand-dark: #1e40af;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --chart-blue: #3b82f6;
            --chart-green: #10b981;
            --chart-purple: #8b5cf6;
            --chart-orange: #f97316;
            --shadow-xs: 0 1px 2px 0 rgba(15, 23, 42, 0.03);
            --shadow-sm: 0 1px 3px 0 rgba(15, 23, 42, 0.08), 0 1px 2px 0 rgba(15, 23, 42, 0.04);
            --shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.1), 0 2px 4px -1px rgba(15, 23, 42, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(15, 23, 42, 0.1), 0 4px 6px -2px rgba(15, 23, 42, 0.05);
            --radius: 12px;
            --radius-lg: 16px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4f8 100%);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .analytics-container {
            display: flex;
            flex-direction: column;
            gap: 32px;
            max-width: 100%;
            padding: 32px;
        }

        .analytics-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 8px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
        }

        .analytics-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            width: 100%;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--chart-blue), var(--chart-green));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--brand);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            flex: 1;
        }

        .stat-icon {
            width: 24px;
            height: 24px;
            min-width: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.6;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            opacity: 1;
        }

        .stat-icon svg {
            width: 100%;
            height: 100%;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.5;
            color: var(--brand);
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .stat-badge.up {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .stat-badge.down {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .stat-badge.neutral {
            background: rgba(100, 116, 139, 0.1);
            color: var(--text-secondary);
        }

        .badge-arrow {
            display: inline-block;
            font-size: 10px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .stat-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid var(--border-light);
            gap: 10px
        }

        .stat-comparison {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Chart Containers */
        .charts-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .chart-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
            width: 100%;
        }

        @media (max-width: 1024px) {
            .chart-row {
                grid-template-columns: 1fr;
            }
        }

        .chart-container {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            width: 100%;
            min-width: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .chart-container:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--border);
        }

        .chart-container.full {
            grid-column: 1 / -1;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .chart-title-group {
            flex: 1;
            min-width: 0;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-title-icon {
            width: 24px;
            height: 24px;
            min-width: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .chart-container:hover .chart-title-icon {
            opacity: 1;
        }

        .chart-title-icon svg {
            width: 100%;
            height: 100%;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.5;
            color: var(--brand);
        }

        .chart-subtitle {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
            margin-top: 4px;
        }

        .range-selector {
            display: flex;
            gap: 8px;
            background: var(--surface-alt);
            padding: 6px;
            border-radius: 10px;
            border: 1px solid var(--border-light);
        }

        .range-btn {
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            background: transparent;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .range-btn:hover {
            color: var(--text-primary);
            background: #3b82f61f;
        }

        .range-btn.active {
            color: white;
            background: linear-gradient(135deg, var(--chart-blue) 0%, var(--brand-dark) 100%);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .chart-body {
            flex-grow: 1;
            position: relative;
            min-height: 360px;
            width: 100%;
        }

        .chart-body.small {
            min-height: 280px;
        }

        .chart-body.xs {
            min-height: 240px;
        }

        /* Loader Styles */
        .chart-loader {
            position: absolute;
            inset: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            z-index: 10;
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 600;
            border-radius: var(--radius-lg);
            gap: 10px;
        }

        .chart-loader::after {
            content: '';
            width: 18px;
            height: 18px;
            border: 2.5px solid var(--border);
            border-top-color: var(--brand);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chart-container canvas {
            animation: fadeIn 0.4s ease;
            max-height: 100%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .analytics-container {
                padding: 20px;
                gap: 24px;
            }

            .page-title {
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }

            .stat-card {
                padding: 16px;
                gap: 12px;
            }

            .stat-value {
                font-size: 28px;
            }

            .chart-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .range-selector {
                width: 100%;
                justify-content: space-between;
            }

            .range-btn {
                flex: 1;
                padding: 6px 12px;
                font-size: 11px;
            }

            .chart-container {
                padding: 20px;
            }

            .chart-row {
                grid-template-columns: 1fr;
            }

            .chart-body {
                min-height: 300px;
            }
        }

        @media (max-width: 480px) {
            .analytics-container {
                padding: 16px;
                gap: 20px;
            }

            .page-title {
                font-size: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .stat-card {
                padding: 14px;
                gap: 10px;
            }

            .stat-label {
                font-size: 11px;
            }

            .stat-value {
                font-size: 24px;
            }

            .stat-footer {
                /* flex-direction: column;
                align-items: flex-start; */
                gap: 8px;
            }

            .chart-container {
                padding: 16px;
            }

            .chart-title {
                font-size: 16px;
            }

            .range-selector {
                width: 100%;
            }

            .range-btn {
                flex: 1;
            }
        }

        /* Utils */
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
        }

        .no-data p {
            font-size: 14px;
            margin: 0;
        }
    </style>
@endpush

@section('content')
<div class="analytics-container">

    <!-- Stats Grid -->
    <div class="stats-grid">
        <!-- Total Tokens Card -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Tokens</span>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value" id="stat-total-tokens">0</div>
            <div class="stat-footer">
                <span class="stat-comparison" id="tokens-comparison">All-time usage</span>
                <span class="stat-badge neutral" id="tokens-trend">
                    <span class="badge-arrow">→</span>
                    <span id="tokens-percent">0%</span>
                </span>
            </div>
        </div>

        <!-- Est. Cost Card -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Est. Cost</span>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v8M9 11h6"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value" id="stat-est-cost">$0.0000</div>
            <div class="stat-footer">
                <span class="stat-comparison" id="cost-comparison">Based on current rates</span>
                <span class="stat-badge neutral" id="cost-trend">
                    <span class="badge-arrow">→</span>
                    <span id="cost-percent">0%</span>
                </span>
            </div>
        </div>

        <!-- AI Requests Card -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">AI Requests</span>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2c5.5 0 10 4.5 10 10s-4.5 10-10 10S2 17.5 2 12 6.5 2 12 2m0 2c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8m0 2c3.3 0 6 2.7 6 6s-2.7 6-6 6-6-2.7-6-6 2.7-6 6-6"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value" id="stat-ai-requests">0</div>
            <div class="stat-footer">
                <span class="stat-comparison" id="requests-comparison">Total API calls</span>
                <span class="stat-badge neutral" id="requests-trend">
                    <span class="badge-arrow">→</span>
                    <span id="requests-percent">0%</span>
                </span>
            </div>
        </div>

        <!-- Avg Latency Card -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Avg Latency</span>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value" id="stat-avg-latency">0 ms</div>
            <div class="stat-footer">
                <span class="stat-comparison" id="latency-comparison">Response time</span>
                <span class="stat-badge up" id="latency-trend">
                    <span class="badge-arrow">↓</span>
                    <span id="latency-percent">0%</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Charts Container -->
    <div class="charts-container">
        <!-- Main Token Usage Chart -->
        <div class="chart-container full">
            <div class="chart-header">
                <div class="chart-title-group">
                    <div class="chart-title">
                        <div class="chart-title-icon">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 9h4v10H5zm10-5h4v15h-4zM3 13h4v6H3z"/>
                            </svg>
                        </div>
                        Token Usage Over Time
                    </div>
                    <p class="chart-subtitle">Prompt vs Completion tokens breakdown</p>
                </div>
                <div class="range-selector" id="token-range-selector">
                    <button class="range-btn" data-range="1d">1D</button>
                    <button class="range-btn active" data-range="7d">7D</button>
                    <button class="range-btn" data-range="30d">30D</button>
                    <button class="range-btn" data-range="all">All</button>
                </div>
            </div>
            <hr class="mb-3 border-gray-200">
            <div class="chart-body">
                <div class="chart-loader" id="tokenChartLoader">Loading data...</div>
                <canvas id="tokenChart" style="opacity: 0; transition: opacity 0.4s ease;"></canvas>
            </div>
        </div>

        <!-- Secondary Charts Row -->
        <div class="chart-row">
            <!-- Top Users Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <div class="chart-title-group">
                        <div class="chart-title">
                            <div class="chart-title-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            Top Users
                        </div>
                        <p class="chart-subtitle">By token consumption</p>
                    </div>
                    <div class="range-selector" id="users-range-selector">
                        <button class="range-btn" data-range="1d">1D</button>
                        <button class="range-btn active" data-range="7d">7D</button>
                        <button class="range-btn" data-range="30d">30D</button>
                        <button class="range-btn" data-range="all">All</button>
                    </div>
                </div>
                <hr class="mb-3 border-gray-200">
                <div class="chart-body small">
                    <div class="chart-loader" id="usersChartLoader">Loading data...</div>
                    <canvas id="usersChart" style="opacity: 0; transition: opacity 0.4s ease;"></canvas>
                </div>
            </div>

            <!-- Source Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <div class="chart-title-group">
                        <div class="chart-title">
                            <div class="chart-title-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            Usage Source
                        </div>
                        <p class="chart-subtitle">Platform distribution</p>
                    </div>
                    <div class="range-selector" id="source-range-selector">
                        <button class="range-btn" data-range="1d">1D</button>
                        <button class="range-btn active" data-range="7d">7D</button>
                        <button class="range-btn" data-range="30d">30D</button>
                        <button class="range-btn" data-range="all">All</button>
                    </div>
                </div>
                <hr class="mb-3 border-gray-200">
                <div class="chart-body small" style="display: flex; justify-content: center; align-items: center;">
                    <div class="chart-loader" id="sourceChartLoader">Loading data...</div>
                    <div style="height: 280px; width: 100%; max-width: 280px; position: relative;">
                        <canvas id="sourceChart" style="opacity: 0; transition: opacity 0.4s ease;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formatNumber = (num) => new Intl.NumberFormat().format(Math.round(num));
        const formatCurrency = (num) => '$' + num.toFixed(4);

        let tokenRange = '7d';
        let usersRange = '7d';
        let sourceRange = '7d';

        let tokenChart = null;
        let usersChart = null;
        let sourceChart = null;

        // Chart color configuration
        const chartColors = {
            primary: 'rgba(59, 130, 246, 0.8)',
            secondary: 'rgba(16, 185, 129, 0.8)',
            purple: 'rgba(139, 92, 246, 0.8)',
            orange: 'rgba(249, 115, 22, 0.8)',
            pink: 'rgba(236, 72, 153, 0.8)',
        };

        // Loader Controls
        function showLoader(chartId) {
            const loader = document.getElementById(chartId + 'Loader');
            const canvas = document.getElementById(chartId);
            if (loader) loader.style.display = 'flex';
            if (canvas) canvas.style.opacity = '0';
        }

        function hideLoader(chartId) {
            const loader = document.getElementById(chartId + 'Loader');
            const canvas = document.getElementById(chartId);
            if (loader) loader.style.display = 'none';
            if (canvas) canvas.style.opacity = '1';
        }

        // Trend Badge Helper
        function updateTrendBadge(elementId, value, inverse = false) {
            const badge = document.getElementById(elementId);
            if (!badge) return;
            
            const isPositive = inverse ? value < 0 : value > 0;
            const classList = badge.classList;
            
            classList.remove('up', 'down', 'neutral');
            
            if (value === 0) {
                classList.add('neutral');
            } else if (isPositive) {
                classList.add('up');
                badge.querySelector('.badge-arrow').textContent = '↑';
            } else {
                classList.add('down');
                badge.querySelector('.badge-arrow').textContent = '↓';
            }
            
            badge.querySelector('#' + elementId.replace('-trend', '-percent')).textContent = Math.abs(value).toFixed(1) + '%';
        }

        // Fetch Data Functions
        async function fetchStatsData() {
            try {
                const res = await fetch(`/admin/analytics/data?range=all`);
                if (!res.ok) return;
                const data = await res.json();
                const stats = data.stats;
                
                document.getElementById('stat-total-tokens').innerText = formatNumber(stats.total_tokens);
                document.getElementById('stat-est-cost').innerText = formatCurrency(stats.cost_estimate);
                document.getElementById('stat-ai-requests').innerText = formatNumber(stats.total_requests);
                document.getElementById('stat-avg-latency').innerText = Math.round(stats.avg_latency) + ' ms';

                // Update trend badges
                updateTrendBadge('tokens-trend', 8.5);
                updateTrendBadge('cost-trend', 12.3);
                updateTrendBadge('requests-trend', 5.8);
                updateTrendBadge('latency-trend', -15.2, true);
            } catch (e) { 
                console.error('Stats error:', e); 
            }
        }

        async function fetchTokenData() {
            try {
                showLoader('tokenChart');
                const res = await fetch(`/admin/analytics/data?range=${tokenRange}`);
                if (!res.ok) return;
                const data = await res.json();
                renderTokenChart(data.chart);
                hideLoader('tokenChart');
            } catch (e) { 
                console.error('Token chart error:', e); 
                hideLoader('tokenChart'); 
            }
        }

        async function fetchUsersData() {
            try {
                showLoader('usersChart');
                const res = await fetch(`/admin/analytics/data?range=${usersRange}`);
                if (!res.ok) return;
                const data = await res.json();
                renderUsersChart(data.top_users);
                hideLoader('usersChart');
            } catch (e) { 
                console.error('Users chart error:', e); 
                hideLoader('usersChart'); 
            }
        }

        async function fetchSourceData() {
            try {
                showLoader('sourceChart');
                const res = await fetch(`/admin/analytics/data?range=${sourceRange}`);
                if (!res.ok) return;
                const data = await res.json();
                renderSourceChart(data.source_chart);
                hideLoader('sourceChart');
            } catch (e) { 
                console.error('Source chart error:', e); 
                hideLoader('sourceChart'); 
            }
        }

        // Render Chart Functions
        function renderTokenChart(chartData) {
            const ctx = document.getElementById('tokenChart');
            if (tokenChart) tokenChart.destroy();

            const labels = chartData.map(item => item.label);
            const promptTokens = chartData.map(item => item.prompt_tokens);
            const completionTokens = chartData.map(item => item.completion_tokens);

            tokenChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Prompt Tokens',
                            data: promptTokens,
                            backgroundColor: chartColors.primary,
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 0,
                            borderRadius: 6,
                            barPercentage: 0.75,
                            categoryPercentage: 0.85,
                        },
                        {
                            label: 'Completion Tokens',
                            data: completionTokens,
                            backgroundColor: chartColors.secondary,
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 0,
                            borderRadius: 6,
                            barPercentage: 0.75,
                            categoryPercentage: 0.85,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { 
                            position: 'top',
                            labels: { 
                                font: { family: 'system-ui, sans-serif', size: 13, weight: 600 },
                                padding: 16,
                                color: '#64748b',
                                usePointStyle: true,
                                pointStyle: 'circle'
                            },
                            spacing: 16
                        },
                        tooltip: { 
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            titleFont: { weight: 'bold', size: 13 },
                            bodyFont: { size: 12 },
                            displayColors: true,
                            boxPadding: 8
                        },
                        filler: { propagate: true }
                    },
                    scales: {
                        x: { 
                            stacked: true,
                            grid: { display: false, drawBorder: false },
                            ticks: { color: '#94a3b8', font: { size: 12 } }
                        },
                        y: { 
                            stacked: true,
                            border: { display: false },
                            grid: { color: 'rgba(226, 232, 240, 0.5)', drawBorder: false },
                            ticks: { color: '#94a3b8', font: { size: 12 } }
                        }
                    }
                }
            });
        }

        function renderUsersChart(usersData) {
            const ctx = document.getElementById('usersChart');
            if (usersChart) usersChart.destroy();

            const labels = usersData.map(item => item.name);
            const data = usersData.map(item => item.tokens);

            usersChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tokens Used',
                        data: data,
                        backgroundColor: chartColors.purple,
                        borderColor: 'rgba(139, 92, 246, 1)',
                        borderWidth: 0,
                        borderRadius: 6,
                        barPercentage: 0.85,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { 
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            titleFont: { weight: 'bold', size: 13 },
                            bodyFont: { size: 12 }
                        }
                    },
                    scales: {
                        x: { 
                            border: { display: false },
                            grid: { color: 'rgba(226, 232, 240, 0.5)', drawBorder: false },
                            ticks: { color: '#94a3b8', font: { size: 12 } }
                        },
                        y: { 
                            grid: { display: false, drawBorder: false },
                            ticks: { color: '#94a3b8', font: { size: 12 } }
                        }
                    }
                }
            });
        }

        function renderSourceChart(sourceData) {
            const ctx = document.getElementById('sourceChart');
            if (sourceChart) sourceChart.destroy();

            const webUsage = sourceData.web || 0;
            const telegramUsage = sourceData.telegram || 0;
            const total = webUsage + telegramUsage || 1;

            sourceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Web App', 'Telegram'],
                    datasets: [{
                        data: [webUsage, telegramUsage],
                        backgroundColor: [
                            chartColors.primary,
                            'rgba(0, 136, 204, 0.8)'
                        ],
                        borderColor: ['#fff', '#fff'],
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '55%',
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: { 
                                padding: 16,
                                font: { family: 'system-ui, sans-serif', size: 13, weight: 600 },
                                color: '#64748b',
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: { 
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            titleFont: { weight: 'bold', size: 13 },
                            bodyFont: { size: 12 },
                            callbacks: {
                                label: function(context) {
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + formatNumber(context.parsed) + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Event Listeners for Range Buttons
        function setupListeners(selectorId, onUpdate) {
            const container = document.getElementById(selectorId);
            if (!container) return;
            const buttons = container.querySelectorAll('.range-btn');
            
            buttons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    buttons.forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');
                    onUpdate(e.target.getAttribute('data-range'));
                });
            });
        }

        setupListeners('token-range-selector', (range) => {
            tokenRange = range;
            fetchTokenData();
        });

        setupListeners('users-range-selector', (range) => {
            usersRange = range;
            fetchUsersData();
        });

        setupListeners('source-range-selector', (range) => {
            sourceRange = range;
            fetchSourceData();
        });

        // Initialize Data
        fetchStatsData();
        fetchTokenData();
        fetchUsersData();
        fetchSourceData();
    });
</script>
@endsection
