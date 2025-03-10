@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles_2')
    <style>
        .axis-label {
            font-size: 14px;
            font-weight: bold;
        }

        .tooltip {
            position: absolute;
            padding: 8px;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            border-radius: 4px;
            font-size: 12px;
            pointer-events: none;
            z-index: 9999;
            max-width: 300px;
            word-wrap: break-word;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .bar-overlay {
            fill: transparent;
            cursor: pointer;
        }

        .chart-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        #currentMonthHorizontalChart,
        #totalHorizontalChart {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: inherit;
        }

        .horizontal-chart-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: inherit;
            margin-left: 20px;
            /* Add some space on the left */
        }

        .card-body {
            padding: 1.5rem;
        }

        .y-axis text {
            font-size: 12px;
            fill: white;
        }

        /* Only keep necessary date picker specific styles */
        .date-input-container {
            position: relative;
        }

        .date-input-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        /* Add new styles for card headers with centered titles and right-aligned buttons */
        .card-header-with-button {
            position: relative;
            padding-right: 100px;
            /* Make space for the absolute positioned button */
        }

        .card-header-with-button h5 {
            width: 100%;
            text-align: center;
            margin: 0;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .card-header-with-button .btn {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Use Bootstrap classes for the filters row instead of custom CSS -->
        <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3 mb-3">
            <!-- Project selection with responsive width -->
            <div class="flex-lg-fill" style="min-width: 200px;">
                <select class="form-select" id="projectSelect">
                    <option value="" {{ !request('id_proyek') ? 'selected' : '' }}>Semua Proyek</option>
                    @foreach ($proyeks as $proyekOne)
                        <option value="{{ $proyekOne->id }}" {{ request('id_proyek') == $proyekOne->id ? 'selected' : '' }}>
                            {{ $proyekOne->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Period filters with responsive layout -->
            <div class="d-flex align-items-center gap-2 flex-lg-fill" style="flex: 2;">
                <div class="date-input-container flex-fill">
                    <input class="form-control" id="startDate" name="startDate" type="month" value="{{ request('startDate') ? \Carbon\Carbon::parse($startDate)->format('Y-m') : '' }}">
                    <div class="date-input-overlay" onclick="document.getElementById('startDate').showPicker()"></div>
                </div>
                <span class="text-nowrap">s/d</span>
                <div class="date-input-container flex-fill">
                    <input class="form-control" id="endDate" name="endDate" type="month" value="{{ request('endDate') ? \Carbon\Carbon::parse($endDate)->format('Y-m') : '' }}">
                    <div class="date-input-overlay" onclick="document.getElementById('endDate').showPicker()"></div>
                </div>
                <button class="btn btn-primary btn-hide-text-mobile" onclick="applyFilters()">
                    <i class="fa fa-filter"></i> <span class="ms-2">Filter</span>
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Display Jumlah Barang Masuk -->
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-sign-in-alt"></i></span>
                    <div class="info-box-content" id="totalBarangMasuk">
                        <span class="info-box-text">Total ATB</span>
                        <span class="info-box-number">Rp{{ number_format($totalATB, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Display Jumlah Barang Keluar -->
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-sign-out-alt"></i></span>
                    <div class="info-box-content" id="totalBarangKeluar">
                        <span class="info-box-text">Total APB</span>
                        <span class="info-box-number">Rp{{ number_format($totalAPB, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Display Total Semua Barang with parentheses for negative values -->
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-balance-scale"></i></span>
                    <div class="info-box-content" id="totalSemuaBarang">
                        <span class="info-box-text">Total Saldo</span>
                        <span class="info-box-number">
                            @php
                                // Check if saldo is negative and format appropriately
                                if ($totalSaldo < 0) {
                                    echo 'Rp(' . number_format(abs($totalSaldo), 2, ',', '.') . ')';
                                } else {
                                    echo 'Rp' . number_format($totalSaldo, 2, ',', '.');
                                }
                            @endphp
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-xl-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h5 class="text-center pt-2 ps-1">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Statistik Bulan Ini
                        </h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="background-color: #353a50; min-height: 300px;">
                        <div class="flex-grow-1 text-center" id="currentMonthVerticalChart"></div>
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
                    <div class="card-body d-flex justify-content-center align-items-center" style="background-color: #353a50; min-height: 300px;">
                        <div class="flex-grow-1 text-center" id="totalVerticalChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-xl-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h5 class="text-center pt-2 ps-1">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Persentase Distribusi Bulan Ini
                        </h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="background-color: #353a50; min-height: 300px;">
                        <div class="chart">
                            <div class="flex-grow-1 text-center" id="pieChartCurrent"></div>
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
                    <div class="card-body d-flex justify-content-center align-items-center" style="background-color: #353a50; min-height: 300px;">
                        <div class="chart">
                            <div class="flex-grow-1 text-center" id="pieChartTotal"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-md-4">
                <div class="card card-primary">
                    <div class="card-header card-header-with-button">
                        <h5>
                            <i class="fas fa-chart-bar mr-1"></i>
                            ATB per Bulan ({{ date('Y') }})
                        </h5>
                        <button class="btn btn-sm btn-light btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#atbChartModal" type="button">
                            <i class="fas fa-expand-alt"></i> <span class="ms-1">View Larger</span>
                        </button>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="background-color: #353a50; min-height: 300px;">
                        <div class="flex-grow-1 text-center" id="monthlyAtbChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card card-primary">
                    <div class="card-header card-header-with-button">
                        <h5>
                            <i class="fas fa-chart-bar mr-1"></i>
                            APB per Bulan ({{ date('Y') }})
                        </h5>
                        <button class="btn btn-sm btn-light btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#apbChartModal" type="button">
                            <i class="fas fa-expand-alt"></i> <span class="ms-1">View Larger</span>
                        </button>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="background-color: #353a50; min-height: 300px;">
                        <div class="flex-grow-1 text-center" id="monthlyApbChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card card-primary">
                    <div class="card-header card-header-with-button">
                        <h5>
                            <i class="fas fa-chart-bar mr-1"></i>
                            Saldo S/D Bulan ({{ date('Y') }})
                        </h5>
                        <button class="btn btn-sm btn-light btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#saldoChartModal" type="button">
                            <i class="fas fa-expand-alt"></i> <span class="ms-1">View Larger</span>
                        </button>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="background-color: #353a50; min-height: 300px;">
                        <div class="flex-grow-1 text-center" id="monthlySaldoChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                @if (!request('id_proyek'))
                    <div class="card card-primary">
                        <div class="card-header">
                            <h5 class="text-center pt-2 ps-1">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Statistik Per Proyek Bulan Ini
                            </h5>
                        </div>
                        <div class="card-body" style="background-color: #353a50; height: {{ max(400, count($proyeks) * 80) }}px;">
                            <div class="horizontal-chart-wrapper">
                                <div id="currentMonthHorizontalChart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h5 class="text-center pt-2 ps-1">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Statistik Per Proyek S/D Bulan Ini
                            </h5>
                        </div>
                        <div class="card-body" style="background-color: #353a50; height: {{ max(400, count($proyeks) * 80) }}px;">
                            <div class="horizontal-chart-wrapper">
                                <div id="totalHorizontalChart"></div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card card-primary">
                        <div class="card-header">
                            <h5 class="text-center pt-2 ps-1">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Statistik Kategori Sparepart Bulan Ini
                            </h5>
                        </div>
                        <div class="card-body" style="background-color: #353a50; height: {{ max(400, count($categoryData['current']) * 80) }}px;">
                            <div class="horizontal-chart-wrapper">
                                <div id="categoryChartCurrent"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h5 class="text-center pt-2 ps-1">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Statistik Kategori Sparepart S/D Bulan Ini
                            </h5>
                        </div>
                        <div class="card-body" style="background-color: #353a50; height: {{ max(400, count($categoryData['current']) * 80) }}px;">
                            <div class="horizontal-chart-wrapper">
                                <div id="categoryChartTotal"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Modal for ATB Chart -->
    <div class="modal fade" id="atbChartModal" aria-labelledby="atbChartModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center" id="atbChartModalLabel"><i class="fas fa-chart-bar mr-2"></i>ATB per Bulan ({{ date('Y') }})</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #353a50; min-height: 500px;">
                    <div class="d-flex justify-content-center align-items-center" style="background-color: #353a50; min-height: 500px;">
                        <div class="flex-grow-1 text-center" id="monthlyAtbChartModal"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for APB Chart -->
    <div class="modal fade" id="apbChartModal" aria-labelledby="apbChartModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center" id="atbChartModalLabel"><i class="fas fa-chart-bar mr-2"></i>APB per Bulan ({{ date('Y') }})</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #353a50; min-height: 500px;">
                    <div class="d-flex justify-content-center align-items-center" style="background-color: #353a50; min-height: 500px;">
                        <div class="flex-grow-1 text-center" id="monthlyApbChartModal"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Saldo Chart -->
    <div class="modal fade" id="saldoChartModal" aria-labelledby="saldoChartModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center" id="atbChartModalLabel"><i class="fas fa-chart-bar mr-2"></i>Saldo S/D Bulan ({{ date('Y') }})</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #353a50; min-height: 500px;">
                    <div class="d-flex justify-content-center align-items-center" style="background-color: #353a50; min-height: 500px;">
                        <div class="flex-grow-1 text-center" id="monthlyCumulativeSaldoChartModal"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_2')
    <script src="https://cdn.jsdelivr.net/npm/d3@7"></script>

    <script>
        // Initialize select2
        $('#projectSelect').select2({
            placeholder: 'Semua Proyek',
            width: '100%',
            allowClear: true
        });

        // Initialize date constraints when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');

            // Set initial constraints if values exist
            if (startDateInput.value) {
                endDateInput.min = startDateInput.value;
            }

            if (endDateInput.value) {
                startDateInput.max = endDateInput.value;
            }

            // Add event listeners to update constraints when dates change
            startDateInput.addEventListener('change', function() {
                if (this.value) {
                    endDateInput.min = this.value;

                    // If end date is now less than start date, reset it
                    if (endDateInput.value && endDateInput.value < this.value) {
                        endDateInput.value = this.value;
                    }
                } else {
                    endDateInput.min = "";
                }
            });

            endDateInput.addEventListener('change', function() {
                if (this.value) {
                    startDateInput.max = this.value;

                    // If start date is now greater than end date, reset it
                    if (startDateInput.value && startDateInput.value > this.value) {
                        startDateInput.value = this.value;
                    }
                } else {
                    startDateInput.max = "";
                }
            });
        });

        function applyFilters() {
            const projectId = document.getElementById('projectSelect').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const currentUrl = new URL(window.location.href);

            // Handle project filter
            if (projectId) {
                currentUrl.searchParams.set('id_proyek', projectId);
            } else {
                currentUrl.searchParams.delete('id_proyek');
            }

            // Handle date filters - only add if they have values
            if (startDate) {
                currentUrl.searchParams.set('startDate', startDate);
            } else {
                currentUrl.searchParams.delete('startDate');
            }

            if (endDate) {
                currentUrl.searchParams.set('endDate', endDate);
            } else {
                currentUrl.searchParams.delete('endDate');
            }

            // Navigate to the new URL
            window.location.href = currentUrl.toString();
        }

        // Add helper function to format saldo values with parentheses for negative numbers
        function formatSaldoValue(value) {
            if (value < 0) {
                return '(Rp' + Math.abs(value).toLocaleString('de-DE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ')';
            } else {
                return 'Rp' + value.toLocaleString('de-DE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        }
    </script>

    @include('dashboard.dashboard.scripts.VerticalBarChart')
    @if (!request('id_proyek'))
        @include('dashboard.dashboard.scripts.HorizontalBarChart')
    @else
        @include('dashboard.dashboard.scripts.CategoryHorizontalChart')
    @endif
    @include('dashboard.dashboard.scripts.PieChart')
    @include('dashboard.dashboard.scripts.MonthlyFinancialChart')
@endpush
