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

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Online Store Visitors</h3>
                            <a href="javascript:void(0);">View Report</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg">820</span>
                                <span>Visitors Over Time</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right">
                                <span class="text-success">
                                    <i class="fas fa-arrow-up"></i> 12.5%
                                </span>
                                <span class="text-muted">Since last week</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <div class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand">
                                    <div class=""></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink">
                                    <div class=""></div>
                                </div>
                            </div>
                            <canvas class="chartjs-render-monitor" id="visitors-chart" style="display: block; width: 561px; height: 200px;" height="400" width="1122"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                                <i class="fas fa-square text-primary"></i> This Week
                            </span>

                            <span>
                                <i class="fas fa-square text-gray"></i> Last Week
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Sales</h3>
                            <a href="javascript:void(0);">View Report</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg">$18,230.00</span>
                                <span>Sales Over Time</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right">
                                <span class="text-success">
                                    <i class="fas fa-arrow-up"></i> 33.1%
                                </span>
                                <span class="text-muted">Since last month</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <div class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand">
                                    <div class=""></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink">
                                    <div class=""></div>
                                </div>
                            </div>
                            <canvas class="chartjs-render-monitor" id="sales-chart" style="display: block; width: 561px; height: 200px;" height="400" width="1122"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                                <i class="fas fa-square text-primary"></i> This year
                            </span>

                            <span>
                                <i class="fas fa-square text-gray"></i> Last year
                            </span>
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
            // Search functionality
            $('#table_search').on('keyup', function() {
                let searchValue = $(this).val().toLowerCase();
                $('#tabelDataBarangHabis tbody tr').each(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
                });
            });

            // Initialize select2
            $('#projectSelect').select2({
                placeholder: 'Pilih Proyek',
                width: '100%'
            });
        });
    </script>
@endpush
