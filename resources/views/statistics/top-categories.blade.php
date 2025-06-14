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

        <button onclick="downloadCategoryChartPDF()" class="bg-lime-600 text-white px-4 py-2 rounded hover:bg-lime-700 mt-4">
            Export as PDF
        </button>
    </div>

    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">
        Top Selling Categories
    </h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        @php
            $labels = $topCategories->pluck('name');
            $totals = $topCategories->pluck('total_sales');

            $backgroundColors = [
                '#FFB3BA', '#FFDAC1', '#FAF3A0', '#B5EAD7', '#C7CEEA',
                '#FFDFD3', '#E2F0CB', '#A2D2FF', '#E4C1F9', '#FBE7C6',
            ];

            $chartData = [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Total Sales',
                    'data' => $totals,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($labels)),
                ]],
            ];
        @endphp

        <canvas id="topCategoriesChart" width="600" height="500" data-chart='@json($chartData)'></canvas>
    </div>
</div>

{{-- Chart.js + Plugins + Export --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('topCategoriesChart');
        const ctx = canvas.getContext('2d');
        const chartData = JSON.parse(canvas.dataset.chart);

        new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Top Selling Categories'
                    }
                    // datalabels removido
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Sales Quantity' }
                    },
                    x: {
                        title: { display: true, text: 'Category' }
                    }
                }
            }
            // plugins: [ChartDataLabels] removido
        });
    });

    async function downloadCategoryChartPDF() {
        const chartData = JSON.parse(document.getElementById('topCategoriesChart').dataset.chart);

        const exportCanvas = document.createElement('canvas');
        exportCanvas.width = 800;
        exportCanvas.height = 600;
        exportCanvas.style.position = 'absolute';
        exportCanvas.style.top = '-9999px';
        document.body.appendChild(exportCanvas);

        const exportCtx = exportCanvas.getContext('2d');
        Chart.register(ChartDataLabels);

        const exportChart = new Chart(exportCtx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: false,
                animation: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        color: '#000',
                        font: { weight: 'bold', size: 10 },
                        formatter: value => value
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Quantity',
                            font: { size: 14, weight: 'bold' }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Category',
                            font: { size: 14, weight: 'bold' }
                        },
                        ticks: {
                            font: { size: 12 }
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        await new Promise(resolve => setTimeout(resolve, 300));

        const chartImage = await html2canvas(exportCanvas, {
            scale: 3,
            backgroundColor: '#ffffff'
        });

        const imgData = chartImage.toDataURL('image/png');
        const { jsPDF } = window.jspdf;

        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'px',
            format: 'a4'
        });

        pdf.setFont('helvetica', 'bold');
        pdf.setFontSize(18);
        pdf.setTextColor(51, 51, 51);
        pdf.text('Top Selling Categories', pdf.internal.pageSize.getWidth() / 2, 40, { align: 'center' });

        const imgWidth = 380;
        const imgHeight = 320;
        const x = (pdf.internal.pageSize.getWidth() - imgWidth) / 2;
        const y = 100;

        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        pdf.save('top_categories_chart.pdf');

        exportChart.destroy();
        document.body.removeChild(exportCanvas);
    }
</script>
@endsection
