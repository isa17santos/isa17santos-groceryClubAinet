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

    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">
        Top Selling Products
    </h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        @php
            $labels = $topProducts->pluck('name');
            $totals = $topProducts->pluck('total_sales');

            $backgroundColors = [
                '#FFADAD', '#FFD6A5', '#FDFFB6', '#A0C4FF', '#D0A3FF',
                '#C2F0C2', '#FDCFE8', '#A0E7E5', '#FFDAC1', '#EAC4A3',
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

        <canvas id="topProductsChart" width="600" height="400" data-chart='@json($chartData)'></canvas>
    </div>
</div>

{{-- Chart.js + PDF export libs --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    let chartInstance;

    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('topProductsChart');
        const ctx = canvas.getContext('2d');
        const chartData = JSON.parse(canvas.dataset.chart);

        chartInstance = new Chart(ctx, {
            type: 'pie',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            padding: 20
                        }
                    },
                    title: {
                        display: true,
                        text: 'Top Selling Products'
                    },
                    
                    datalabels: {
                        display: false
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    });

    async function downloadChartAsPDF() {
        const chartData = JSON.parse(document.getElementById('topProductsChart').dataset.chart);

        // Cria canvas invisível para exportação
        const exportCanvas = document.createElement('canvas');
        exportCanvas.width = 400;
        exportCanvas.height = 400;
        exportCanvas.style.position = 'absolute';
        exportCanvas.style.top = '-9999px';
        document.body.appendChild(exportCanvas);

        const exportCtx = exportCanvas.getContext('2d');

        // Regista o plugin
        Chart.register(ChartDataLabels);

        // Cria gráfico com datalabels
        const exportChart = new Chart(exportCtx, {
            type: 'pie',
            data: chartData,
            options: {
                responsive: false,
                animation: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        color: '#000',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: (value) => value
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Espera renderização
        await new Promise(resolve => setTimeout(resolve, 300));

        // Captura imagem do gráfico
        const chartImage = await html2canvas(exportCanvas, {
            scale: 3,
            backgroundColor: '#ffffff'
        });

        const imgData = chartImage.toDataURL('image/png');
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'px',
            format: [600, 700]
        });

        // Título no topo
        pdf.setFontSize(18);
        pdf.setTextColor(34, 34, 34);
        pdf.text('Top Selling Products', 300, 30, { align: 'center' });

        // Tamanho do gráfico no PDF (reduzido)
        const imgWidth = 300;
        const imgHeight = 300;
        const x = (pdf.internal.pageSize.getWidth() - imgWidth) / 2;
        const y = 60;

        // Adiciona o gráfico
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        // Gera legenda manualmente com cor + label
        const labels = chartData.labels;
        const colors = chartData.datasets[0].backgroundColor;

        let legendY = y + imgHeight + 30;
        pdf.setFontSize(12);
        labels.forEach((label, index) => {
            const color = colors[index];

            // Caixa de cor
            pdf.setFillColor(color);
            pdf.rect(70, legendY - 8, 10, 10, 'F');

            // Nome do produto
            pdf.setTextColor(0, 0, 0);
            pdf.text(label, 85, legendY);

            legendY += 20;
        });

        pdf.save('top_products_chart.pdf');

        // Limpa
        exportChart.destroy();
        document.body.removeChild(exportCanvas);
    }

</script>

@endsection
