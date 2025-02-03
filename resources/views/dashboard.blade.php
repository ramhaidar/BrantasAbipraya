@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="dropdown-container mb-3">
            <select class="form-select" id="projectSelect" onchange="window.location.href=this.value">
                <option value="{{ route('dashboard') }}" {{ !request('id_proyek') ? 'selected' : '' }}>Semua Proyek</option>
                @foreach ($proyeks as $proyekOne)
                    <option value="{{ route('dashboard', ['id_proyek' => $proyekOne->id]) }}" {{ request('id_proyek') == $proyekOne->id ? 'selected' : '' }}>
                        {{ $proyekOne->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <!-- Display Jumlah Barang Masuk -->
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-sign-in-alt"></i></span>
                    <div class="info-box-content" id="totalBarangMasuk">
                        <span class="info-box-text">Total ATB</span>
                        <span class="info-box-number">Rp{{ number_format($totalATB, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Display Jumlah Barang Keluar -->
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-sign-out-alt"></i></span>
                    <div class="info-box-content" id="totalBarangKeluar">
                        <span class="info-box-text">Total APB</span>
                        <span class="info-box-number">Rp{{ number_format($totalAPB, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Display Total Semua Barang -->
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-balance-scale"></i></span>
                    <div class="info-box-content" id="totalSemuaBarang">
                        <span class="info-box-text">Total Saldo</span>
                        <span class="info-box-number">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 col-xl-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h5 class="text-center pt-2 ps-1">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Statistik Bulan Ini
                        </h5>
                    </div>
                    <div class="card-body" style="background-color: #353a50">
                        <div class="chart">
                            <canvas id="currentMonthVerticalChart" style="min-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h5 class="text-center pt-2 ps-1">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Statistik S/D Bulan Ini
                        </h5>
                    </div>
                    <div class="card-body" style="background-color: #353a50">
                        <div class="chart">
                            <canvas id="totalVerticalChart" style="min-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h5 class="text-center pt-2 ps-1">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Statistik Proyek Bulan Ini
                </h5>
            </div>
            <div class="card-body" style="background-color: #353a50">
                <div class="chart">
                    <canvas id="proyekCurrentChart" style="min-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="card card-primary">
            <div class="card-header">
                <h5 class="text-center pt-2 ps-1">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Statistik Proyek S/D Bulan Ini
                </h5>
            </div>
            <div class="card-body" style="background-color: #353a50">
                <div class="chart">
                    <canvas id="proyekTotalChart" style="min-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 col-xl-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h5 class="text-center pt-2 ps-1">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Persentase Distribusi Bulan Ini
                        </h5>
                    </div>
                    <div class="card-body" style="background-color: #353a50">
                        <div class="chart">
                            <canvas id="pieChartCurrent" style="min-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h5 class="text-center pt-2 ps-1">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Persentase Distribusi S/D Bulan Ini
                        </h5>
                    </div>
                    <div class="card-body" style="background-color: #353a50">
                        <div class="chart">
                            <canvas id="pieChartTotal" style="min-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts_2')
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('#projectSelect').select2({
                placeholder: 'Pilih Proyek',
                width: '100%'
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3@7"></script>

    <script>
        $(document).ready(function() {
            // Add this before formatLargeNumber function
            function formatThousands(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Fungsi untuk mengonversi angka ke format Miliar
            function formatLargeNumber(number) {
                return (number / 1000000000).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ".").replace('.', ',') + ' M';
            }

            const valueLabelsPlugin = {
                id: 'valueLabels',
                afterDraw: function(chart) {
                    var ctx = chart.ctx;
                    chart.data.datasets.forEach(function(dataset, datasetIndex) {
                        var meta = chart.getDatasetMeta(datasetIndex);
                        meta.data.forEach(function(bar, index) {
                            var data = dataset.data[index];
                            if (data > 0) {
                                // Only draw label if tooltip is not active for this bar
                                if (!chart.tooltip?.getActiveElements()?.some(e =>
                                        e.datasetIndex === datasetIndex && e.index === index
                                    )) {
                                    var formattedValue = formatLargeNumber(data);
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    ctx.font = '10px Arial';

                                    // Get bar position
                                    var position = bar.getCenterPoint();

                                    // Add background to make text more readable
                                    var textWidth = ctx.measureText(formattedValue).width;
                                    ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
                                    ctx.fillRect(
                                        position.x - (textWidth / 2) - 2,
                                        bar.y - 20, // Position higher above the bar
                                        textWidth + 4,
                                        14
                                    );

                                    // Draw text
                                    ctx.fillStyle = '#000';
                                    ctx.fillText(formattedValue, position.x, bar.y - 8);
                                }
                            }
                        });
                    });
                }
            };

            function createChart(elementId, data, title) {
                const ctx = $('#' + elementId)[0].getContext('2d');
                const categories = Object.keys(data).map(cat => cat === 'Material' ? cat : cat);

                new Chart(ctx, {
                    type: 'bar',
                    plugins: [valueLabelsPlugin],
                    data: {
                        labels: categories,
                        datasets: [{
                                label: 'ATB',
                                data: categories.map(cat => data[cat].atb),
                                backgroundColor: '#fe6d73',
                                borderColor: '#fe6d73',
                                borderWidth: 0
                            },
                            {
                                label: 'APB',
                                data: categories.map(cat => data[cat].apb),
                                backgroundColor: '#f6f6f6',
                                borderColor: '#f6f6f6',
                                borderWidth: 0
                            },
                            {
                                label: 'Saldo',
                                data: categories.map(cat => data[cat].saldo),
                                backgroundColor: '#16ce9a',
                                borderColor: '#16ce9a',
                                borderWidth: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        layout: {
                            padding: {
                                top: -30
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: title,
                                color: '#f6f6f6' // Add title color
                            },
                            tooltip: {
                                position: 'nearest',
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp' + formatThousands(context.raw);
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                                align: 'center', // Bisa 'start', 'center', atau 'end'
                                labels: {
                                    color: '#f6f6f6',
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Rupiah',
                                    color: '#f6f6f6' // Add y-axis title color
                                },
                                ticks: {
                                    color: '#f6f6f6', // Add y-axis ticks color
                                    callback: function(value) {
                                        return formatLargeNumber(value);
                                    }
                                },
                                afterDataLimits: function(scale) {
                                    if (scale._ticksLength !== 0) {
                                        console.log(scale.max, scale._ticksLength);
                                        scale.max += scale.max / scale._ticksLength;
                                    }
                                },
                                grid: {
                                    color: 'rgba(246, 246, 246, 0.1)' // Lighten grid lines
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#f6f6f6' // Add x-axis ticks color
                                },
                                grid: {
                                    color: 'rgba(246, 246, 246, 0.1)' // Lighten grid lines
                                }
                            }
                        }
                    }
                });
            }

            function createHorizontalChart(elementId, data, title) {
                const ctx = $('#' + elementId)[0].getContext('2d');
                const projects = Object.keys(data);

                // Calculate dynamic height based on number of projects
                const heightPerProject = 75; // Height per project bar
                const minHeight = 300; // Minimum height
                const dynamicHeight = Math.max(minHeight, projects.length * heightPerProject);

                // Set canvas parent height
                $(ctx.canvas).parent().css('height', `${dynamicHeight}px`);

                new Chart(ctx, {
                    type: 'bar',
                    plugins: [{
                        id: 'valueLabels',
                        afterDraw: function(chart) {
                            const ctx = chart.ctx;
                            chart.data.datasets.forEach(function(dataset, datasetIndex) {
                                const meta = chart.getDatasetMeta(datasetIndex);
                                meta.data.forEach(function(bar, index) {
                                    const data = dataset.data[index];
                                    if (data > 0) {
                                        const formattedValue = formatLargeNumber(data);
                                        ctx.textAlign = 'left';
                                        ctx.textBaseline = 'middle';
                                        ctx.font = '10px Arial';

                                        // Get bar dimensions and position
                                        const position = bar.getCenterPoint();
                                        const barWidth = bar.width;
                                        const textWidth = ctx.measureText(formattedValue).width;
                                        const minWidthForInnerLabel = textWidth + 10; // Minimum width needed for inner label

                                        // Add background to make text more readable
                                        let textX;
                                        if (barWidth < minWidthForInnerLabel) {
                                            // Place label outside the bar if bar is too small
                                            textX = bar.x + bar.width + 5;
                                        } else {
                                            // Place label inside the bar
                                            textX = position.x - (textWidth / 2);
                                            ctx.textAlign = 'center';
                                        }

                                        // Draw background
                                        ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
                                        ctx.fillRect(
                                            textX - (ctx.textAlign === 'center' ? textWidth / 2 : 0),
                                            position.y - 7,
                                            textWidth + 4,
                                            14
                                        );

                                        // Draw text
                                        ctx.fillStyle = '#000';
                                        ctx.fillText(formattedValue, textX, position.y);
                                    }
                                });
                            });
                        }
                    }],
                    data: {
                        labels: projects,
                        datasets: [{
                                label: 'Penerimaan',
                                data: projects.map(proj => data[proj].penerimaan),
                                backgroundColor: '#fe6d73',
                                borderColor: '#fe6d73',
                                borderWidth: 0
                            },
                            {
                                label: 'Pengeluaran',
                                data: projects.map(proj => data[proj].pengeluaran),
                                backgroundColor: '#f6f6f6',
                                borderColor: '#f6f6f6',
                                borderWidth: 0
                            },
                            {
                                label: 'Saldo',
                                data: projects.map(proj => data[proj].saldo),
                                backgroundColor: '#16ce9a',
                                borderColor: '#16ce9a',
                                borderWidth: 0
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false, // Allow chart to control its own height
                        layout: {
                            padding: {
                                right: 30,
                                left: 10
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            title: {
                                display: false
                            },
                            legend: {
                                position: 'top',
                                align: 'center',
                                labels: {
                                    color: '#f6f6f6'
                                }
                            },
                            tooltip: {
                                position: 'nearest',
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp' + formatThousands(context.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Rupiah',
                                    color: '#f6f6f6'
                                },
                                ticks: {
                                    color: '#f6f6f6',
                                    callback: function(value) {
                                        return formatLargeNumber(value);
                                    }
                                },
                                grid: {
                                    color: 'rgba(246, 246, 246, 0.1)'
                                }
                            },
                            y: {
                                ticks: {
                                    color: '#f6f6f6'
                                },
                                grid: {
                                    color: 'rgba(246, 246, 246, 0.1)'
                                }
                            }
                        }
                    }
                });
            }

            function createPieChart(elementId, data, title) {
                const ctx = $('#' + elementId)[0].getContext('2d');

                // Calculate totals across all projects
                const totals = Object.values(data).reduce((acc, curr) => {
                    acc.penerimaan += curr.penerimaan || 0;
                    acc.pengeluaran += curr.pengeluaran || 0;
                    acc.saldo += curr.saldo || 0;
                    return acc;
                }, {
                    penerimaan: 0,
                    pengeluaran: 0,
                    saldo: 0
                });

                new Chart(ctx, {
                    type: 'pie',
                    plugins: [{
                        id: 'pieLabels',
                        afterDraw: function(chart) {
                            const ctx = chart.ctx;
                            chart.data.datasets.forEach((dataset) => {
                                const meta = chart.getDatasetMeta(0);
                                meta.data.forEach((element, index) => {
                                    const data = dataset.data[index];
                                    if (data > 0) {
                                        const total = dataset.data.reduce((acc, val) => acc + val, 0);
                                        const percentage = ((data / total) * 100).toFixed(1);
                                        const nominal = formatLargeNumber(data);

                                        // Get label position
                                        const position = element.getCenterPoint();
                                        const radius = element.outerRadius;
                                        const angle = element.startAngle + (element.endAngle - element.startAngle) / 2;

                                        const labelRadius = radius * 0.7;
                                        const x = position.x + (Math.cos(angle) * labelRadius);
                                        const y = position.y + (Math.sin(angle) * labelRadius);

                                        // Draw label background and text
                                        ctx.save();
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        ctx.font = '10px Arial';

                                        // Show both percentage and nominal
                                        const lines = [
                                            `${percentage}%`,
                                            nominal
                                        ];

                                        lines.forEach((line, i) => {
                                            const textWidth = ctx.measureText(line).width;
                                            ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
                                            ctx.fillRect(
                                                x - (textWidth / 2) - 2,
                                                y - 10 + (i * 15),
                                                textWidth + 4,
                                                14
                                            );
                                            ctx.fillStyle = '#000';
                                            ctx.fillText(line, x, y + (i * 15) - 3);
                                        });

                                        ctx.restore();
                                    }
                                });
                            });
                        }
                    }],
                    data: {
                        labels: ['Penerimaan', 'Pengeluaran', 'Saldo'],
                        datasets: [{
                            data: [totals.penerimaan, totals.pengeluaran, totals.saldo],
                            backgroundColor: ['#fe6d73', '#f6f6f6', '#16ce9a'],
                            borderColor: ['#fe6d73', '#f6f6f6', '#16ce9a'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        aspectRatio: 2, // Make chart more compact
                        layout: {
                            padding: {
                                left: 20,
                                right: 20,
                                top: -20,
                                bottom: 50 // Increased bottom padding significantly
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: title,
                                color: '#f6f6f6',
                                font: {
                                    size: 12 // Smaller title font
                                },
                                padding: {
                                    top: 0,
                                    bottom: 10
                                }
                            },
                            legend: {
                                position: 'top', // Move legend to the right
                                align: 'center',
                                labels: {
                                    color: '#f6f6f6',
                                    boxWidth: 15, // Smaller legend boxes
                                    // padding: 10, // Less padding between items
                                    font: {
                                        size: 11 // Smaller legend font
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        const nominal = formatLargeNumber(value);
                                        return `${context.label}: Rp${formatThousands(value)} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            createChart('currentMonthVerticalChart', @json($chartDataCurrent));
            createChart('totalVerticalChart', @json($chartDataTotal));
            createHorizontalChart('proyekCurrentChart', @json($horizontalChartCurrent));
            createHorizontalChart('proyekTotalChart', @json($horizontalChartTotal));
            createPieChart('pieChartCurrent', @json($horizontalChartCurrent));
            createPieChart('pieChartTotal', @json($horizontalChartTotal));
        });
    </script>
@endpush
