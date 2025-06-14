@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('statistics.index') }}" class="inline-flex items-center text-lime-700 hover:underline dark:text-lime-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            ← Back to Statistics
        </a>

        <button onclick="downloadChartAsPDF()" class="bg-lime-600 text-white px-4 py-2 rounded hover:bg-lime-700 mt-4">
            Export as PDF
        </button>
    </div>

    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">Sales by Month</h1>

    <div class="overflow-x-auto shadow rounded-lg bg-white dark:bg-gray-800 p-6">
        @php
            $pastelColors = [
                '#A3D5FF', '#FFBCBC', '#C4F0C5', '#FFF3B0', '#D9D7F1',
                '#FFDAC1', '#B5EAD7', '#E2F0CB', '#FFD6E0', '#D0F4DE',
            ];

            $salesData = [
                'labels' => range(1, 12),
                'datasets' => [],
            ];

            $colorIndex = 0;

            foreach ($groupedSales as $year => $monthly) {
                $monthlyTotals = [];

                for ($m = 1; $m <= 12; $m++) {
                    $monthlyTotals[] = $monthly->firstWhere('month', $m)?->total ?? 0;
                }

                $salesData['datasets'][] = [
                    'label' => (string) $year,
                    'data' => $monthlyTotals,
                    'borderColor' => $pastelColors[$colorIndex % count($pastelColors)],
                    'borderWidth' => 2,
                    'fill' => false,
                    'tension' => 0.3,
                ];

                $colorIndex++;
            }
        @endphp

        <canvas id="salesChart" width="600" height="300" data-sales='@json($salesData)'></canvas>
    </div>
</div>

{{-- Bibliotecas JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

{{-- Renderiza o gráfico --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('salesChart');
        const ctx = canvas.getContext('2d');
        const salesData = JSON.parse(canvas.dataset.sales);

        new Chart(ctx, {
            type: 'line',
            data: salesData,
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Sales by Month (per Year)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });
    });

    async function downloadChartAsPDF() {
        const canvas = document.getElementById('salesChart');
        const imageData = canvas.toDataURL('image/png');

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();

        pdf.setFontSize(16);
        pdf.text('Sales by Month Chart', 15, 15);
        pdf.addImage(imageData, 'PNG', 15, 25, 180, 90);

        pdf.save('sales_chart.pdf');
    }
</script>
@endsection
