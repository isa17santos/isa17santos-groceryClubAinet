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

        <button onclick="downloadUsersTypeChartPDF()" class="bg-lime-600 text-white px-4 py-2 rounded hover:bg-lime-700 mt-4">
            Export as PDF
        </button>
    </div>

    <h1 class="text-4xl font-bold text-center text-yellow-700 dark:text-yellow-700 mb-10">Users by Type</h1>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        @php
            $labels = $usersByType->pluck('type');
            $totals = $usersByType->pluck('total');

            $pastelColors = [
                '#A3D5FF', '#FFBCBC', '#C4F0C5', '#FFF3B0', '#D9D7F1',
                '#FFDAC1', '#B5EAD7', '#E2F0CB', '#FFD6E0', '#D0F4DE',
            ];

            $backgroundColors = [];
            foreach ($labels as $index => $label) {
                $backgroundColors[] = $pastelColors[$index % count($pastelColors)];
            }

            $chartData = [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Total Users',
                    'data' => $totals,
                    'backgroundColor' => $backgroundColors,
                ]],
            ];
        @endphp

        <canvas id="usersTypeChart" width="600" height="400" data-chart='@json($chartData)'></canvas>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('usersTypeChart');
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
                        text: 'Total Users Grouped by Type'
                    }
                    // No datalabels here — only in the export
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Users'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'User Type'
                        }
                    }
                }
            }
        });
    });

    async function downloadUsersTypeChartPDF() {
        const chartData = JSON.parse(document.getElementById('usersTypeChart').dataset.chart);

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
                        font: { weight: 'bold', size: 12 },
                        formatter: value => value
                    },
                    title: {
                        display: true,
                        text: 'Users by Type'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Users',
                            font: { size: 14, weight: 'bold' }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'User Type',
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
        pdf.text('Users by Type', pdf.internal.pageSize.getWidth() / 2, 40, { align: 'center' });

        const imgWidth = 380;
        const imgHeight = 320;
        const x = (pdf.internal.pageSize.getWidth() - imgWidth) / 2;
        const y = 100;

        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

        pdf.save('users_by_type_chart.pdf');

        exportChart.destroy();
        document.body.removeChild(exportCanvas);
    }
</script>
@endsection
