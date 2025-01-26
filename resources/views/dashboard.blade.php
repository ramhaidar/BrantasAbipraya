@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="dropdown-container mb-3">
            <select class="form-select" id="projectSelect" style="width: 390px;">
                <option disabled selected>Pilih Proyek</option>
                <option value="all">Semua Proyek</option>
                @foreach ($proyeks as $proyekOne)
                    <option value="{{ $proyekOne->id }}">{{ $proyekOne->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <!-- Display Jumlah Barang Masuk -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-box-open"></i></span>
                    <div class="info-box-content" id="totalBarangMasuk">
                        <span class="info-box-text">Barang Masuk</span>
                        <span class="info-box-number">Rp{{ number_format($totalHargaBarangMasuk, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Display Jumlah Barang Keluar -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-box"></i></span>
                    <div class="info-box-content" id="totalBarangKeluar">
                        <span class="info-box-text">Barang Keluar</span>
                        <span class="info-box-number">Rp{{ number_format($totalHargaBarangKeluar, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Display Total Semua Barang -->
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-warehouse"></i></span>
                    <div class="info-box-content" id="totalSemuaBarang">
                        <span class="info-box-text">Total Saldo</span>
                        <span class="info-box-number">Rp{{ number_format($totalHargaSemuaBarang, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Cek Role dari User apakah Admin atau Pegawai -->
            @if (Auth::user()->role == 'Admin')
                <!-- Display Total Semua User -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                        <div class="info-box-content" id="totalSemuaUser">
                            <span class="info-box-text">User</span>
                            <span class="info-box-number">{{ $totalSemuaUser }}</span>
                        </div>
                    </div>
                </div>
            @elseif (Auth::user()->role == 'Boss')
                <!-- Display Total Semua Proyek yang dimiliki Boss -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-briefcase"></i></span>
                        <div class="info-box-content" id="totalSemuaProyek">
                            <span class="info-box-text">Jumlah Proyek</span>
                            <span class="info-box-number">{{ $totalProyek }}</span>
                        </div>
                    </div>
                </div>

                <!-- Display Total Semua Alat yang dimiliki User -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-tools"></i></span>
                        <div class="info-box-content" id="totalSemuaAlat">
                            <span class="info-box-text">Jumlah Alat</span>
                            <span class="info-box-number">{{ $totalSemuaAlat }}</span>
                        </div>
                    </div>
                </div>
            @elseif (Auth::user()->role == 'Pegawai')
                <!-- Display Total Semua Alat yang dimiliki User -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-tools"></i></span>
                        <div class="info-box-content" id="totalSemuaAlat">
                            <span class="info-box-text">Jumlah Alat</span>
                            <span class="info-box-number">{{ $totalSemuaAlat }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Barang Yang Akan Habis</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm w-100"> <!-- Menggunakan w-100 untuk lebar penuh pada parent -->
                                <input class="form-control float-right" id="table_search" type="text" placeholder="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-default" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <!-- Tambahkan ID pada tabel -->
                        <table class="table table-head-fixed text-nowrap" id="tabelDataBarangHabis">
                            <thead>
                                <tr>
                                    <th>Proyek</th>
                                    <th>Kode</th>
                                    <th>Supplier</th>
                                    <th>Sparepart</th>
                                    <th>Part Number</th>
                                    <th>Remaining Quantity</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($atbsHabis as $atb)
                                    <tr>
                                        <td>{{ $atb->proyek->nama ?? 'N/A' }}</td>
                                        <td>{{ $atb->komponen->kode }}</td>
                                        <td>{{ $atb->masterData->supplier }}</td>
                                        <td>{{ $atb->masterData->sparepart }}</td>
                                        <td>{{ $atb->masterData->part_number }}</td>
                                        <td>{{ $atb->remaining_quantity }}</td>
                                        <td>{{ $atb->satuan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
        document.getElementById('table_search').addEventListener('keyup', function() {
            // Ambil nilai input
            let searchValue = this.value.toLowerCase();
            // Ambil semua baris dari tabel dengan id 'data_table'
            let tableRows = document.querySelectorAll('#tabelDataBarangHabis tbody tr');

            // Loop melalui setiap baris
            tableRows.forEach(row => {
                // Ambil teks dari setiap kolom dalam baris
                let rowText = row.textContent.toLowerCase();
                // Periksa apakah teks baris mengandung teks pencarian
                if (rowText.includes(searchValue)) {
                    // Tampilkan baris jika cocok
                    row.style.display = '';
                } else {
                    // Sembunyikan baris jika tidak cocok
                    row.style.display = 'none';
                }
            });
        });

        $('#projectSelect').select2({
            placeholder: 'Pilih Proyek',
            width: '390px'
        });

        $(document).ready(function() {
            // Event listener untuk filter proyek
            $('#projectSelect').on('change', function() {
                let selectedProject = $(this).val(); // Ambil nilai proyek yang dipilih

                // Kirim request AJAX dengan parameter proyek di URL
                $.ajax({
                    url: "/dashboard/proyek/" + selectedProject, // Tambahkan projectId ke URL
                    method: 'GET', // Gunakan metode GET sesuai dengan route baru
                    success: function(response) {
                        // console.log(response); // Debugging, tampilkan respons di konsol

                        // Update elemen sesuai dengan respons
                        $('#totalBarangMasuk .info-box-number').text('Rp' + response.totalHargaBarangMasuk);
                        $('#totalBarangKeluar .info-box-number').text('Rp' + response.totalHargaBarangKeluar);
                        $('#totalSemuaBarang .info-box-number').text('Rp' + response.totalHargaSemuaBarang);

                        if (response.totalSemuaUser !== undefined) {
                            $('#totalSemuaUser .info-box-number').text(response.totalSemuaUser);
                        }

                        if (response.totalProyek !== undefined) {
                            $('#totalSemuaProyek .info-box-number').text(response.totalProyek);
                        }

                        if (response.totalSemuaAlat !== undefined) {
                            $('#totalSemuaAlat .info-box-number').text(response.totalSemuaAlat);
                        }

                        if (Array.isArray(response.atbsHabis)) {
                            $('#tabelDataBarangHabis tbody').empty(); // Kosongkan tabel
                            response.atbsHabis.forEach(function(atb) {
                                $('#tabelDataBarangHabis tbody').append(`
                            <tr>
                                <td>${atb.proyek.nama || 'N/A'}</td>
                                <td>${atb.komponen.kode || 'N/A'}</td>
                                <td>${atb.master_data ? atb.master_data.supplier : 'N/A'}</td>
                                <td>${atb.master_data ? atb.master_data.sparepart : 'N/A'}</td>
                                <td>${atb.master_data ? atb.master_data.part_number : 'N/A'}</td>
                                <td>${atb.remaining_quantity}</td>
                                <td>${atb.satuan}</td>
                            </tr>
                        `);
                            });
                        } else {
                            console.error("atbsHabis is not an array");
                        }
                    },
                    error: function(error) {
                        console.error(error); // Tampilkan error jika ada masalah
                    }
                });
            });
        });
    </script>
@endpush
