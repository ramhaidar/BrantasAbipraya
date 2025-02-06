@extends('layouts.app')

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
    </style>
@endpush

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

        <div class="row mt-4">
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

    </div>
@endsection

@push('scripts_2')
    <script src="https://cdn.jsdelivr.net/npm/d3@7"></script>

    @include('dashboard.dashboard.scripts.VerticalBarChart')
    @if (!request('id_proyek'))
        @include('dashboard.dashboard.scripts.HorizontalBarChart')
    @else
        @include('dashboard.dashboard.scripts.CategoryHorizontalChart')
    @endif
    @include('dashboard.dashboard.scripts.PieChart')
@endpush
