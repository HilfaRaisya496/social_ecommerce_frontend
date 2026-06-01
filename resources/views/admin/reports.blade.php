@extends('layouts.admin')

@section('header_title', 'Analytics Reports')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">$0</h3>
                <p class="text-sm text-gray-500">-</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Total Transactions</p>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">0</h3>
                <p class="text-sm text-gray-500">-</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Average Order Value</p>
                <h3 class="text-3xl font-bold text-gray-900 mb-2">$0</h3>
                <p class="text-sm text-gray-500">-</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Revenue Trend</h3>
                        <p class="text-sm text-gray-500">Monthly revenue and transaction volume</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-green-600">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                        <span class="font-medium">+17% Growth</span>
                    </div>
                </div>
                <div class="h-[300px]">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Sales by Category</h3>
                <div class="h-[220px]">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="space-y-2 mt-4 italic text-sm text-gray-500 text-center">
                    Waiting for category data...
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Generated Reports</h3>
                    <p class="text-sm text-gray-500">Download and view analytics reports</p>
                </div>
                <button
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-xl font-medium hover:from-amber-600 hover:to-amber-700 transition-all shadow-sm">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    Generate Report
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-12 text-center text-gray-400 border-2 border-dashed border-gray-100 rounded-2xl col-span-2">
                    <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                    <p>No reports generated yet.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Revenue Trend
            const ctxTrend = document.getElementById('revenueTrendChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue',
                        data: [0, 0, 0, 0, 0, 0],
                        backgroundColor: '#d97706',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Category Chart
            const ctxCat = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctxCat, {
                type: 'doughnut',
                data: {
                    labels: ['Electronics', 'Fashion', 'Others'],
                    datasets: [{
                        data: [1, 1, 1],
                        backgroundColor: ['#059669', '#d97706', '#10b981'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>
@endsection