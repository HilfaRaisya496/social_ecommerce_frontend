@extends('layouts.admin')

@section('header_title', 'System Monitoring')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">System Status</p>
                    <i data-lucide="check-circle" class="w-5 h-5 text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-400">-</h3>
                <p class="text-sm text-gray-500 mt-1 italic">Waiting for API...</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">CPU Usage</p>
                    <i data-lucide="activity" class="w-5 h-5 text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">0%</h3>
                <p class="text-sm text-gray-500 mt-1">-</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">Memory Usage</p>
                    <i data-lucide="activity" class="w-5 h-5 text-amber-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">0%</h3>
                <p class="text-sm text-gray-500 mt-1">-</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm text-gray-600">Active Alerts</p>
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">0</h3>
                <p class="text-sm text-gray-500 mt-1">-</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-6">System Performance (Last 24 Hours)</h3>
            <div class="h-[300px]">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">System Health</h3>
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <div class="space-y-4">
                    <p class="text-center py-12 text-sm text-gray-500 italic">Reading health status...</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Activity Logs</h3>
                    <i data-lucide="clock" class="w-5 h-5 text-gray-400"></i>
                </div>
                <div class="space-y-4 max-h-[400px] overflow-y-auto">
                    <p class="text-center py-12 text-sm text-gray-500 italic">No activity logged.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                    datasets: [{
                        label: 'CPU %',
                        data: [0, 0, 0, 0, 0, 0],
                        borderColor: '#3b82f6',
                        tension: 0.4
                    }, {
                        label: 'Memory %',
                        data: [0, 0, 0, 0, 0, 0],
                        borderColor: '#d97706',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100 },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
@endsection