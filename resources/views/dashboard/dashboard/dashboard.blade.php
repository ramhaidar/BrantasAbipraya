@extends('layouts.app')

@push('styles_2')
    <style>
        .axis-label {
            font-size: 14px;
            font-weight: bold;
        }

        .tooltip {
            position: fixed;
            padding: 8px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            border-radius: 4px;
            font-size: 12px;
            pointer-events: none;
            z-index: 100;
            max-width: 200px;
            word-wrap: break-word;
        }

        .bar-overlay {
            fill: transparent;
            cursor: pointer;
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
                        <div class="text-center" id="currentMonthVerticalChart"></div>
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
                        <div class="text-center" id="totalVerticalChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_2')
    <script src="https://cdn.jsdelivr.net/npm/d3@7"></script>

    @include('dashboard.dashboard.scripts.VerticalBarChart')
@endpush
