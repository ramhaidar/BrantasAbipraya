@extends('dashboard')

@section('css')
    <link href="/css/random-css-datatable.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" rel="stylesheet" crossorigin="anonymous" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" referrerpolicy="no-referrer">
    <style>
        .accordion-header {
            cursor: pointer;
            color: rgb(0, 0, 0);
            font-weight: bold;
        }

        .header-row {
            background-color: #ffffcc;
        }

        .header-row:hover {
            background-color: #fff9a1;
        }

        .subheader-row {
            background-color: #dcdcdc;
            font-weight: bold;
            cursor: pointer;
        }

        .subheader-row:hover {
            background-color: #cfcfcf;
        }

        .data-row {
            background-color: white;
        }

        .total-row {
            background-color: #ffffcc;
            font-weight: bold;
        }

        .alert-custom-css {
            max-width: 400px;
            width: 90%;
        }

        #form-group {
            width: 90%;
        }

        .space-nowrap {
            white-space: nowrap;
        }

        .center {
            text-align: center !important;
        }

        .custom-confirm-delete {
            margin-right: 5%;
        }

        .custom-cancel-delete {
            margin-left: 5%;
        }

        .custom-action-delete {
            width: 100% !important;
            justify-content: space-between;
        }

        .no-border {
            border: none !important;
        }

        @media screen and (max-width:500px) {
            #button-for-modal-add span {
                display: none;
            }

            #button-for-modal-add {
                font-size: 20px;
            }
        }

        @media screen and (max-width:500px) {
            .button-export-import-addData span {
                display: none;
            }

            .button-export-import-addData {
                font-size: 20px;
            }
        }

        #table-data td,
        #table-data th {
            vertical-align: middle !important;
            text-align: center !important;
        }

        #table-data td div.text-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
@endsection

@section('content')
    @php
        $detailDataATB = collect($detailDataATB);
        $detailDataAPB = collect($detailDataAPB);
        $detailDataSaldo = collect($detailDataSaldo);
    @endphp

    <div class="fade-in-up page-content">
        <div class="ibox">
            <div class="ibox-head pe-0 ps-0 d-flex justify-content-between align-items-center">
                <!-- Judul -->
                <div class="ibox-title flex-grow-1 text-start">
                    {{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}
                </div>

                <!-- Filter Tanggal -->
                <div class="d-flex flex-grow-1 justify-content-center">
                    <div class="row align-items-center w-100">
                        <div class="col">
                            <input class="form-control datetimepicker" id="start_date" name="start_date" required placeholder="Tanggal Awal" autocomplete="off">
                        </div>
                        <div class="col-auto mx-2">
                            <span>—</span>
                        </div>
                        <div class="col">
                            <input class="form-control datetimepicker" id="end_date" name="end_date" required placeholder="Tanggal Akhir" autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <!-- Add filter button -->
                            <button class="btn btn-primary btn-sm button-export-import-addData" id="filterButton" type="button">Filter</button>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-grow-1 justify-content-end">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <a class="btn btn-success btn-sm button-export-import-addData">
                                <i class="fa fa-file-excel"></i>
                                <span class="ms-2">Export Data</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="table-container">
                <div class="mt-0 ibox-body table-responsive" style="overflow-x:auto;">

                    @php
                        // Penerimaan
                        $total_net_atb_hutang_unit_alat = $detailDataATB->where('tipe', 'Hutang Unit Alat')->where('kode', '!=', 'C1')->sum('total_net');
                        $total_net_atb_mutasi_proyek = $detailDataATB->where('tipe', 'Mutasi Proyek')->where('kode', '!=', 'C1')->sum('total_net');
                        $total_net_atb_panjar_unit_alat = $detailDataATB->where('tipe', 'Panjar Unit Alat')->where('kode', '!=', 'C1')->sum('total_net');
                        $total_net_atb_panjar_panjar_proyek = $detailDataATB->where('tipe', 'Panjar Proyek')->where('kode', '!=', 'C1')->sum('total_net');
                        $totalPenerimaan = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_panjar_proyek;

                        // Pengeluaran
                        $total_net_apb_ex_unit_alat = $detailDataAPB->where('tipe', 'Hutang Unit Alat')->where('kode', '!=', 'C1')->sum('total_net');
                        $total_net_apb_ex_panjar_unit_alat = $detailDataAPB->where('tipe', 'Panjar Unit Alat')->where('kode', '!=', 'C1')->sum('total_net');
                        $total_net_apb_ex_panjar_proyek = $detailDataAPB->where('tipe', 'Panjar Proyek')->where('kode', '!=', 'C1')->sum('total_net');
                        $total_net_apb_ex_mutasi_saldo = $detailDataAPB->where('tipe', 'Mutasi Proyek')->where('kode', '!=', 'C1')->sum('total_net');
                        $totalPengeluaran = $total_net_apb_ex_unit_alat + $total_net_apb_ex_mutasi_saldo + $total_net_apb_ex_panjar_unit_alat + $total_net_apb_ex_panjar_proyek;

                        // Saldo
                        $total_net_saldo_ex_unit_alat = $detailDataSaldo->where('tipe', 'Hutang Unit Alat')->where('kode', '!=', 'C1')->sum('total_net') + $detailDataSaldo->where('tipe', 'Mutasi Proyek')->where('kode', '!=', 'C1')->sum('total_net');
                        $total_net_saldo_ex_panjar_unit_alat = $detailDataSaldo->where('tipe', 'Panjar Unit Alat')->where('kode', '!=', 'C1')->sum('total_net');
                        $total_net_saldo_ex_panjar_proyek = $detailDataSaldo->where('tipe', 'Panjar Proyek')->where('kode', '!=', 'C1')->sum('total_net');
                        $totalSaldo = $total_net_saldo_ex_unit_alat + $total_net_saldo_ex_panjar_unit_alat + $total_net_saldo_ex_panjar_proyek;
                    @endphp

                    <table class="m-0 border-dark table table-bordered table-striped" id="table-data" style="width:100%">
                        {{-- HEADER --}}
                        <thead class="table-primary">
                            <tr>
                                <th class="align-middle text-center" rowspan="2">NO.</th>
                                <th class="align-middle text-center" rowspan="2">U R A I A N</th>
                                <th class="text-center" style="border-bottom: 1px solid black;" colspan="4">PENERIMAAN</th>
                                <th class="align-middle text-center" rowspan="2">TOTAL PENERIMAAN</th>
                                <th class="text-center" style="border-bottom: 1px solid black;" colspan="4">PENGELUARAN</th>
                                <th class="align-middle text-center" rowspan="2">TOTAL PENGELUARAN</th>
                                <th class="text-center" style="border-bottom: 1px solid black;" colspan="3">SALDO</th>
                                <th class="align-middle text-center" rowspan="2">TOTAL SALDO</th>
                            </tr>
                            <tr>
                                <th class="align-middle text-center">PEMBEL. HUTANG UNIT ALAT (PO)</th>
                                <th class="align-middle text-center">MUTASI DARI PROYEK</th>
                                <th class="align-middle text-center">PEMBEL. PANJAR UNIT ALAT</th>
                                <th class="align-middle text-center">PEMBEL. PANJAR PROYEK</th>
                                <th class="align-middle text-center">EX UNIT ALAT</th>
                                <th class="align-middle text-center">EX PANJAR UNIT ALAT</th>
                                <th class="align-middle text-center">EX PANJAR PROYEK</th>
                                <th class="align-middle text-center">MUTASI SALDO EX</th>
                                <th class="align-middle text-center">EX UNIT ALAT</th>
                                <th class="align-middle text-center">EX PANJAR UNIT ALAT</th>
                                <th class="align-middle text-center">EX PANJAR PROYEK</th>
                            </tr>
                        </thead>

                        {{-- SUKU CADANG --}}
                        <tbody class="border-dark">
                            <tr class="header-row accordion-header" onclick="toggleAccordion('suku-cadang')">
                                <td><strong>I</strong></td>
                                <td><strong>SUKU CADANG</strong></td>
                                <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_atb_panjar_panjar_proyek, 0, ',', '.') }}</td>

                                <td class="text-center">Rp{{ number_format($totalPenerimaan, 0, ',', '.') }}</td>

                                <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_proyek, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>

                                <td class="text-center">Rp{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>

                                <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>

                                <td class="text-center">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</td>
                            </tr>

                            <!-- PERBAIKAN SECTION -->
                            @php
                                $perbaikanDataATB = $detailDataATB->where('suku_cadang', 'PERBAIKAN');

                                $perbaikanDataAPB = $detailDataAPB->where('suku_cadang', 'PERBAIKAN');

                                $perbaikanDataSaldo = $detailDataSaldo->where('suku_cadang', 'PERBAIKAN');
                            @endphp
                            <tr class="collapse suku-cadang subheader-row" onclick="toggleAccordion('perbaikan')">
                                <td><strong>A</strong></td>
                                <td><strong>PERBAIKAN</strong></td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataATB->where('tipe', 'Panjar Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataATB->sum('total_net'), 0, ',', '.') }}</td>

                                <td class="text-center">Rp{{ number_format($perbaikanDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataAPB->sum('total_net'), 0, ',', '.') }}</td>

                                <td class="text-center">Rp{{ number_format($perbaikanDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $perbaikanDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($perbaikanDataSaldo->sum('total_net'), 0, ',', '.') }}</td>
                            </tr>

                            @php
                                $itemsPerbaikan = [
                                    'A1' => 'Cabin',
                                    'A2' => 'Engine System',
                                    'A3' => 'Transmission System',
                                    'A4' => 'Chassis & Swing Machinery',
                                    'A5' => 'Differential System',
                                    'A6' => 'Electrical System',
                                    'A7' => 'Hydraulic / Pneumatic System',
                                    'A8' => 'Steering System',
                                    'A9' => 'Brake System',
                                    'A10' => 'Suspension',
                                    'A11' => 'Attachment',
                                    'A12' => 'Undercarriage',
                                    'A13' => 'Final Drive',
                                    'A14' => 'Freight Cost',
                                ];
                            @endphp

                            @foreach ($itemsPerbaikan as $kode => $description)
                                @php
                                    $subDataATB = $detailDataATB->where('suku_cadang', 'PERBAIKAN')->where('kode', $kode);

                                    $total_net_atb_hutang_unit_alat = $subDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                                    $total_net_atb_mutasi_proyek = $subDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_atb_panjar_unit_alat = $subDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_atb_panjar_proyek = $subDataATB->where('tipe', 'Panjar Proyek')->sum('total_net');
                                    $total_net_atb_sub_total = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_proyek;

                                    $subDataAPB = $detailDataAPB->where('suku_cadang', 'PERBAIKAN')->where('kode', $kode);

                                    $total_net_apb_ex_unit_alat = $subDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                                    $total_net_apb_ex_panjar_unit_alat = $subDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_apb_ex_panjar_proyek = $subDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net');
                                    $total_net_apb_ex_mutasi_saldo = $subDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_apb_ex_sub_total = $total_net_apb_ex_unit_alat + $total_net_apb_ex_mutasi_saldo + $total_net_apb_ex_panjar_unit_alat + $total_net_apb_ex_panjar_proyek;

                                    $subDataSaldo = $detailDataSaldo->where('suku_cadang', 'PERBAIKAN')->where('kode', $kode);

                                    $total_net_saldo_ex_unit_alat = $subDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $subDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_saldo_ex_panjar_unit_alat = $subDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_saldo_ex_panjar_proyek = $subDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net');

                                    $total_net_saldo_ex_sub_total = $total_net_saldo_ex_unit_alat + $total_net_saldo_ex_panjar_unit_alat + $total_net_saldo_ex_panjar_proyek;
                                @endphp

                                <tr class="collapse perbaikan">
                                    <td>{{ $kode }}</td>
                                    <td>{{ $description }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_sub_total, 0, ',', '.') }}</td>

                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_sub_total, 0, ',', '.') }}</td>

                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_sub_total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach

                            <!-- PEMELIHARAAN SECTION -->
                            <tr class="collapse suku-cadang subheader-row" onclick="toggleAccordion('pemeliharaan')">
                                <td><strong>B</strong></td>
                                <td><strong>PEMELIHARAAN</strong></td>

                                @php
                                    $total_net_atb_perbaikan_hutang_unit_alat = 0;
                                    $total_net_atb_perbaikan_mutasi_proyek = 0;
                                    $total_net_atb_perbaikan_panjar_unit_alat = 0;
                                    $total_net_atb_perbaikan_panjar_panjar_proyek = 0;

                                    foreach ($detailDataATB as $data) {
                                        if ($data['suku_cadang'] == 'PEMELIHARAAN') {
                                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                                $total_net_atb_perbaikan_hutang_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_atb_perbaikan_mutasi_proyek += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_atb_perbaikan_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_atb_perbaikan_panjar_panjar_proyek += $data['total_net'];
                                            }
                                        }
                                    }

                                    $total_net_apb_perbaikan_ex_unit_alat = 0;
                                    $total_net_apb_perbaikan_ex_panjar_unit_alat = 0;
                                    $total_net_apb_perbaikan_ex_panjar_proyek = 0;
                                    $total_net_apb_perbaikan_ex_mutasi_saldo = 0;

                                    foreach ($detailDataAPB as $data) {
                                        if ($data['suku_cadang'] == 'PEMELIHARAAN') {
                                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                                $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_apb_perbaikan_ex_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_apb_perbaikan_ex_panjar_proyek += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_apb_perbaikan_ex_mutasi_saldo += $data['total_net'];
                                            }
                                        }
                                    }

                                    $total_net_saldo_perbaikan_ex_unit_alat = 0;
                                    $total_net_saldo_perbaikan_ex_panjar_unit_alat = 0;
                                    $total_net_saldo_perbaikan_ex_panjar_proyek = 0;

                                    foreach ($detailDataSaldo as $data) {
                                        if ($data['suku_cadang'] == 'PEMELIHARAAN') {
                                            if ($data['tipe'] == 'Hutang Unit Alat' || $data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_saldo_perbaikan_ex_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_saldo_perbaikan_ex_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_saldo_perbaikan_ex_panjar_proyek += $data['total_net'];
                                            }
                                        }
                                    }
                                @endphp

                                <td colspan="">Rp{{ number_format($total_net_atb_perbaikan_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_perbaikan_mutasi_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_perbaikan_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_perbaikan_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_perbaikan_hutang_unit_alat + $total_net_atb_perbaikan_mutasi_proyek + $total_net_atb_perbaikan_panjar_unit_alat + $total_net_atb_perbaikan_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                                <td colspan="">Rp{{ number_format($total_net_apb_perbaikan_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_perbaikan_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_perbaikan_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_perbaikan_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_perbaikan_ex_unit_alat + $total_net_apb_perbaikan_ex_panjar_proyek + $total_net_apb_perbaikan_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->

                                <td colspan="">Rp{{ number_format($total_net_saldo_perbaikan_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_perbaikan_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_perbaikan_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_perbaikan_ex_unit_alat + $total_net_saldo_perbaikan_ex_panjar_unit_alat + $total_net_saldo_perbaikan_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                            </tr>

                            @php
                                $maintenanceKitMapping = [
                                    'B11' => 'Oil Filter',
                                    'B12' => 'Fuel Filter',
                                    'B13' => 'Air Filter',
                                    'B14' => 'Hydraulic Filter',
                                    'B15' => 'Transmission Filter',
                                    'B16' => 'Differential Filter',
                                ];

                                $oilLubricantsMapping = [
                                    'B21' => 'Engine Oil',
                                    'B22' => 'Hydraulic Oil',
                                    'B23' => 'Transmission Oil',
                                    'B24' => 'Final Drive Oil',
                                    'B25' => 'Swing & Damper Oil',
                                    'B26' => 'Differential Oil',
                                    'B27' => 'Grease',
                                    'B28' => 'Brake & Power Steering Fluid',
                                    'B29' => 'Coolant',
                                ];
                            @endphp

                            <!-- Section for Maintenance Kit -->
                            <tr class="collapse pemeliharaan subheader-row" onclick="toggleAccordion('maintenance-kit')">
                                <td>B1</td>
                                <td>Maintenance Kit</td>

                                @php
                                    $total_net_atb_maintenance_kit_hutang_unit_alat = 0;
                                    $total_net_atb_maintenance_kit_mutasi_proyek = 0;
                                    $total_net_atb_maintenance_kit_panjar_unit_alat = 0;
                                    $total_net_atb_maintenance_kit_panjar_panjar_proyek = 0;

                                    foreach ($detailDataATB as $data) {
                                        if ($data['sumber'] == 'MAINTENANCE KIT') {
                                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                                $total_net_atb_maintenance_kit_hutang_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_atb_maintenance_kit_mutasi_proyek += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_atb_maintenance_kit_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_atb_maintenance_kit_panjar_panjar_proyek += $data['total_net'];
                                            }
                                        }
                                    }

                                    $total_net_apb_maintenance_kit_ex_unit_alat = 0;
                                    $total_net_apb_maintenance_kit_ex_panjar_unit_alat = 0;
                                    $total_net_apb_maintenance_kit_ex_panjar_proyek = 0;
                                    $total_net_apb_maintenance_kit_ex_mutasi_saldo = 0;

                                    foreach ($detailDataAPB as $data) {
                                        if ($data['sumber'] == 'MAINTENANCE KIT') {
                                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                                $total_net_apb_maintenance_kit_ex_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_apb_maintenance_kit_ex_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_apb_maintenance_kit_ex_panjar_proyek += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_apb_maintenance_kit_ex_mutasi_saldo += $data['total_net'];
                                            }
                                        }
                                    }

                                    $total_net_saldo_maintenance_kit_ex_unit_alat = 0;
                                    $total_net_saldo_maintenance_kit_ex_panjar_unit_alat = 0;
                                    $total_net_saldo_maintenance_kit_ex_panjar_proyek = 0;

                                    foreach ($detailDataSaldo as $data) {
                                        if ($data['sumber'] == 'MAINTENANCE KIT') {
                                            if ($data['tipe'] == 'Hutang Unit Alat' || $data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_saldo_maintenance_kit_ex_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_saldo_maintenance_kit_ex_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_saldo_maintenance_kit_ex_panjar_proyek += $data['total_net'];
                                            }
                                        }
                                    }
                                @endphp

                                <td colspan="">Rp{{ number_format($total_net_atb_maintenance_kit_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_maintenance_kit_mutasi_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_maintenance_kit_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_maintenance_kit_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_maintenance_kit_hutang_unit_alat + $total_net_atb_maintenance_kit_mutasi_proyek + $total_net_atb_maintenance_kit_panjar_unit_alat + $total_net_atb_maintenance_kit_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                                <td colspan="">Rp{{ number_format($total_net_apb_maintenance_kit_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_maintenance_kit_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_maintenance_kit_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_maintenance_kit_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_maintenance_kit_ex_unit_alat + $total_net_apb_maintenance_kit_ex_panjar_unit_alat + $total_net_apb_maintenance_kit_ex_panjar_proyek + $total_net_apb_maintenance_kit_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->

                                <td colspan="">Rp{{ number_format($total_net_saldo_maintenance_kit_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_maintenance_kit_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_maintenance_kit_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_maintenance_kit_ex_unit_alat + $total_net_saldo_maintenance_kit_ex_panjar_unit_alat + $total_net_saldo_maintenance_kit_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                            </tr>

                            @foreach ($maintenanceKitMapping as $kode => $description)
                                @php
                                    $subDataATB = $detailDataATB->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                                    $total_net_atb_hutang_unit_alat = $subDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                                    $total_net_atb_mutasi_proyek = $subDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_atb_panjar_unit_alat = $subDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_atb_panjar_proyek = $subDataATB->where('tipe', 'Panjar Proyek')->sum('total_net');
                                    $total_net_atb_sub_total = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_proyek;

                                    $subDataAPB = $detailDataAPB->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                                    $total_net_apb_ex_unit_alat = $subDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                                    $total_net_apb_ex_panjar_unit_alat = $subDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_apb_ex_panjar_proyek = $subDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net');
                                    $total_net_apb_ex_mutasi_saldo = $subDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_apb_ex_sub_total = $total_net_apb_ex_unit_alat + $total_net_apb_ex_panjar_unit_alat + $total_net_apb_ex_panjar_proyek + $total_net_apb_ex_mutasi_saldo;

                                    $subDataSaldo = $detailDataSaldo->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                                    $total_net_saldo_ex_unit_alat = $subDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $subDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_saldo_ex_panjar_unit_alat = $subDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_saldo_ex_panjar_proyek = $subDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net');

                                    $total_net_saldo_ex_sub_total = $total_net_saldo_ex_unit_alat + $total_net_saldo_ex_panjar_unit_alat + $total_net_saldo_ex_panjar_proyek;

                                @endphp

                                <tr class="collapse maintenance-kit">
                                    <td>{{ $kode }}</td>
                                    <td>{{ $description }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_sub_total, 0, ',', '.') }}</td>

                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_sub_total, 0, ',', '.') }}</td>

                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_sub_total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach

                            <!-- Section for Oil & Lubricants -->
                            <tr class="collapse pemeliharaan subheader-row" onclick="toggleAccordion('oil-lubricants')">
                                <td>B2</td>
                                <td>Oil & Lubricants</td>

                                @php
                                    $total_net_atb_oil_n_lubricants_hutang_unit_alat = 0;
                                    $total_net_atb_oil_n_lubricants_mutasi_proyek = 0;
                                    $total_net_atb_oil_n_lubricants_panjar_unit_alat = 0;
                                    $total_net_atb_oil_n_lubricants_panjar_proyek = 0;

                                    foreach ($detailDataATB as $data) {
                                        if ($data['sumber'] == 'OIL & LUBRICANTS') {
                                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                                $total_net_atb_oil_n_lubricants_hutang_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_atb_oil_n_lubricants_mutasi_proyek += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_atb_oil_n_lubricants_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_atb_oil_n_lubricants_panjar_proyek += $data['total_net'];
                                            }
                                        }
                                    }

                                    $total_net_apb_oil_n_lubricants_ex_unit_alat = 0;
                                    $total_net_apb_oil_n_lubricants_ex_panjar_unit_alat = 0;
                                    $total_net_apb_oil_n_lubricants_ex_panjar_proyek = 0;
                                    $total_net_apb_oil_n_lubricants_ex_mutasi_saldo = 0;

                                    foreach ($detailDataAPB as $data) {
                                        if ($data['sumber'] == 'OIL & LUBRICANTS') {
                                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                                $total_net_apb_oil_n_lubricants_ex_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_apb_oil_n_lubricants_ex_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_apb_oil_n_lubricants_ex_panjar_proyek += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_apb_oil_n_lubricants_ex_mutasi_saldo += $data['total_net'];
                                            }
                                        }
                                    }

                                    $total_net_saldo_oil_n_lubricants_ex_unit_alat = 0;
                                    $total_net_saldo_oil_n_lubricants_ex_panjar_unit_alat = 0;
                                    $total_net_saldo_oil_n_lubricants_ex_panjar_proyek = 0;

                                    foreach ($detailDataSaldo as $data) {
                                        if ($data['sumber'] == 'OIL & LUBRICANTS') {
                                            if ($data['tipe'] == 'Hutang Unit Alat' || $data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_saldo_oil_n_lubricants_ex_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_saldo_oil_n_lubricants_ex_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_saldo_oil_n_lubricants_ex_panjar_proyek += $data['total_net'];
                                            }
                                        }
                                    }
                                @endphp

                                <td colspan="">Rp{{ number_format($total_net_atb_oil_n_lubricants_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_oil_n_lubricants_mutasi_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_oil_n_lubricants_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_oil_n_lubricants_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_oil_n_lubricants_hutang_unit_alat + $total_net_atb_oil_n_lubricants_mutasi_proyek + $total_net_atb_oil_n_lubricants_panjar_unit_alat + $total_net_atb_oil_n_lubricants_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                                <td colspan="">Rp{{ number_format($total_net_apb_oil_n_lubricants_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_oil_n_lubricants_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_oil_n_lubricants_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_oil_n_lubricants_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_oil_n_lubricants_ex_unit_alat + $total_net_apb_oil_n_lubricants_ex_panjar_unit_alat + $total_net_apb_oil_n_lubricants_ex_panjar_proyek + $total_net_apb_oil_n_lubricants_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->

                                <td colspan="">Rp{{ number_format($total_net_saldo_oil_n_lubricants_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_oil_n_lubricants_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_oil_n_lubricants_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_oil_n_lubricants_ex_unit_alat + $total_net_saldo_oil_n_lubricants_ex_panjar_unit_alat + $total_net_saldo_oil_n_lubricants_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                            </tr>

                            @foreach ($oilLubricantsMapping as $kode => $description)
                                @php
                                    $subDataATB = $detailDataATB->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                                    $total_net_atb_hutang_unit_alat = $subDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                                    $total_net_atb_mutasi_proyek = $subDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_atb_panjar_unit_alat = $subDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_atb_panjar_proyek = $subDataATB->where('tipe', 'Panjar Proyek')->sum('total_net');
                                    $total_net_atb_sub_total = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_proyek;

                                    $subDataAPB = $detailDataAPB->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                                    $total_net_apb_ex_unit_alat = $subDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                                    $total_net_apb_ex_mutasi_saldo = $subDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_apb_ex_panjar_unit_alat = $subDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_apb_ex_panjar_proyek = $subDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net');
                                    $total_net_apb_sub_total = $total_net_apb_ex_unit_alat + $total_net_apb_ex_panjar_unit_alat + $total_net_apb_ex_panjar_proyek + $total_net_apb_ex_mutasi_saldo;

                                    $subDataSaldo = $detailDataSaldo->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                                    $total_net_saldo_ex_unit_alat = $subDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $subDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_saldo_ex_panjar_unit_alat = $subDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_saldo_ex_panjar_proyek = $subDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net');

                                    $total_net_saldo_sub_total = $total_net_saldo_ex_unit_alat + $total_net_saldo_ex_panjar_unit_alat + $total_net_saldo_ex_panjar_proyek;
                                @endphp

                                <tr class="collapse oil-lubricants">
                                    <td>{{ $kode }}</td>
                                    <td>{{ $description }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_sub_total, 0, ',', '.') }}</td>

                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_sub_total, 0, ',', '.') }}</td>

                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_sub_total, 0, ',', '.') }}</td>

                                </tr>
                            @endforeach

                            <!-- Section for Tyre -->
                            <tr class="collapse pemeliharaan subheader-row" onclick="#">
                                <td>B3</td>
                                <td>Tyre</td>

                                @php
                                    $total_net_atb_tyre_hutang_unit_alat = 0;
                                    $total_net_atb_tyre_mutasi_proyek = 0;
                                    $total_net_atb_tyre_panjar_unit_alat = 0;
                                    $total_net_atb_tyre_panjar_proyek = 0;

                                    foreach ($detailDataATB as $data) {
                                        if ($data['kode'] == 'B3') {
                                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                                $total_net_atb_tyre_hutang_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_atb_tyre_mutasi_proyek += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_atb_tyre_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_atb_tyre_panjar_proyek += $data['total_net'];
                                            }
                                        }
                                    }

                                    $total_net_apb_tyre_hutang_unit_alat = 0;
                                    $total_net_apb_tyre_panjar_unit_alat = 0;
                                    $total_net_apb_tyre_panjar_proyek = 0;
                                    $total_net_apb_tyre_mutasi_saldo = 0;

                                    foreach ($detailDataAPB as $data) {
                                        if ($data['kode'] == 'B3') {
                                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                                $total_net_apb_tyre_hutang_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_apb_tyre_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_apb_tyre_panjar_proyek += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Mutasi Saldo') {
                                                $total_net_apb_tyre_mutasi_saldo += $data['total_net'];
                                            }
                                        }
                                    }

                                    $total_net_saldo_tyre_hutang_unit_alat = 0;
                                    $total_net_saldo_tyre_panjar_unit_alat = 0;
                                    $total_net_saldo_tyre_panjar_proyek = 0;

                                    foreach ($detailDataSaldo as $data) {
                                        if ($data['kode'] == 'B3') {
                                            if ($data['tipe'] == 'Hutang Unit Alat' || $data['tipe'] == 'Mutasi Saldo') {
                                                $total_net_saldo_tyre_hutang_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_saldo_tyre_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_saldo_tyre_panjar_proyek += $data['total_net'];
                                            }
                                        }
                                    }
                                @endphp

                                <td colspan="">Rp{{ number_format($total_net_atb_tyre_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_tyre_mutasi_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_tyre_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_tyre_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_tyre_hutang_unit_alat + $total_net_atb_tyre_mutasi_proyek + $total_net_atb_tyre_panjar_unit_alat + $total_net_atb_tyre_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                                <td colspan="">Rp{{ number_format($total_net_apb_tyre_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_tyre_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_tyre_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_tyre_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_tyre_hutang_unit_alat + $total_net_apb_tyre_panjar_unit_alat + $total_net_apb_tyre_panjar_proyek + $total_net_apb_tyre_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->

                                <td colspan="">Rp{{ number_format($total_net_saldo_tyre_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_tyre_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_tyre_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_tyre_hutang_unit_alat + $total_net_saldo_tyre_panjar_unit_alat + $total_net_saldo_tyre_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                            </tr>

                            <!-- MATERIAL SECTION -->
                            @php
                                $materialMapping = [
                                    'C1' => 'Workshop Material',
                                ];
                            @endphp

                            <tr class="header-row accordion-header" onclick="toggleAccordion('material')">
                                <td><strong>II</strong></td>
                                <td><strong>MATERIAL</strong></td>

                                @php
                                    $total_net_atb_workshop_hutang_unit_alat = 0;
                                    $total_net_atb_workshop_mutasi_proyek = 0;
                                    $total_net_atb_workshop_panjar_unit_alat = 0;
                                    $total_net_atb_workshop_panjar_panjar_proyek = 0;

                                    foreach ($detailDataATB as $data) {
                                        if ($data['kode'] == 'C1') {
                                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                                $total_net_atb_workshop_hutang_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_atb_workshop_mutasi_proyek += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_atb_workshop_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_atb_workshop_panjar_panjar_proyek += $data['total_net'];
                                            }
                                        }
                                    }

                                    $total_net_apb_workshop_ex_unit_alat = 0;
                                    $total_net_apb_workshop_ex_panjar_unit_alat = 0;
                                    $total_net_apb_workshop_ex_panjar_proyek = 0;
                                    $total_net_apb_workshop_ex_mutasi_saldo = 0;

                                    foreach ($detailDataAPB as $data) {
                                        if ($data['kode'] == 'C1') {
                                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                                $total_net_apb_workshop_ex_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_apb_workshop_ex_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_apb_workshop_ex_panjar_proyek += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Mutasi Saldo') {
                                                $total_net_apb_workshop_ex_mutasi_saldo += $data['total_net'];
                                            }
                                        }
                                    }

                                    $total_net_saldo_workshop_ex_unit_alat = 0;
                                    $total_net_saldo_workshop_ex_panjar_unit_alat = 0;
                                    $total_net_saldo_workshop_ex_panjar_proyek = 0;

                                    foreach ($detailDataSaldo as $data) {
                                        if ($data['kode'] == 'C1') {
                                            if ($data['tipe'] == 'Hutang Unit Alat' || $data['tipe'] == 'Mutasi Proyek') {
                                                $total_net_saldo_workshop_ex_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                                $total_net_saldo_workshop_ex_panjar_unit_alat += $data['total_net'];
                                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                                $total_net_saldo_workshop_ex_panjar_proyek += $data['total_net'];
                                            }
                                        }
                                    }
                                @endphp

                                <td colspan="">Rp{{ number_format($total_net_atb_workshop_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_workshop_mutasi_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_workshop_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_workshop_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_atb_workshop_hutang_unit_alat + $total_net_atb_workshop_mutasi_proyek + $total_net_atb_workshop_panjar_unit_alat + $total_net_atb_workshop_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                                <td colspan="">Rp{{ number_format($total_net_apb_workshop_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_workshop_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_workshop_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_workshop_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_apb_workshop_ex_unit_alat + $total_net_apb_workshop_ex_panjar_unit_alat + $total_net_apb_workshop_ex_panjar_proyek + $total_net_apb_workshop_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->

                                <td colspan="">Rp{{ number_format($total_net_saldo_workshop_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_workshop_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_workshop_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                                <td colspan="">Rp{{ number_format($total_net_saldo_workshop_ex_unit_alat + $total_net_saldo_workshop_ex_panjar_unit_alat + $total_net_saldo_workshop_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                            </tr>

                            @foreach ($materialMapping as $kode => $description)
                                @php
                                    $subDataATB = $detailDataATB->where('kode', $kode);

                                    $total_net_atb_hutang_unit_alat = $subDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                                    $total_net_atb_mutasi_proyek = $subDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_atb_panjar_unit_alat = $subDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_atb_panjar_proyek = $subDataATB->where('tipe', 'Panjar Proyek')->sum('total_net');

                                    $subDataAPB = $detailDataAPB->where('kode', $kode);

                                    $total_net_apb_ex_unit_alat = $subDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                                    $total_net_apb_ex_panjar_unit_alat = $subDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_apb_ex_panjar_proyek = $subDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net');
                                    $total_net_apb_ex_mutasi_saldo = $subDataAPB->where('tipe', 'Mutasi Saldo')->sum('total_net');

                                    $subDataSaldo = $detailDataSaldo->where('kode', $kode);

                                    // $total_net_saldo_ex_unit_alat = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek - $total_net_apb_ex_unit_alat;
                                    $total_net_saldo_ex_unit_alat = $subDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $subDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                    $total_net_saldo_ex_panjar_unit_alat = $subDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                    $total_net_saldo_ex_panjar_proyek = $subDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net');

                                @endphp

                                <tr class="collapse material">
                                    <td>{{ $kode }}</td>
                                    <td>{{ $description }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_proyek, 0, ',', '.') }}</td>

                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat + $total_net_apb_ex_panjar_unit_alat + $total_net_apb_ex_panjar_proyek + $total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>

                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat + $total_net_saldo_ex_panjar_unit_alat + $total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach

                            @php
                                $total_net_atb_hutang_unit_alat = $detailDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                                $total_net_atb_mutasi_proyek = $detailDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                $total_net_atb_panjar_unit_alat = $detailDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                $total_net_atb_panjar_panjar_proyek = $detailDataATB->where('tipe', 'Panjar Proyek')->sum('total_net');

                                $totalPenerimaan = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_panjar_proyek;

                                $total_pengeluaran_ex_unit_alat = $detailDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                                $total_pengeluaran_ex_panjar_unit_alat = $detailDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                $total_pengeluaran_ex_panjar_proyek = $detailDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net');
                                $total_pengeluaran_ex_mutasi_proyek = $detailDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net');

                                $totalPengeluaran = $total_pengeluaran_ex_unit_alat + $total_pengeluaran_ex_mutasi_proyek + $total_pengeluaran_ex_panjar_unit_alat + $total_pengeluaran_ex_panjar_proyek;

                                $total_saldo_ex_unit_alat = $detailDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $detailDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net');
                                $total_saldo_ex_panjar_unit_alat = $detailDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                                $total_saldo_ex_panjar_proyek = $detailDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net');

                                // $total_saldo_ex_unit_alat = $total_net_atb_hutang_unit_alat - $total_pengeluaran_ex_unit_alat;
                                // $total_saldo_ex_panjar_unit_alat = $total_net_atb_panjar_unit_alat - $total_pengeluaran_ex_panjar_unit_alat;
                                // $total_saldo_ex_panjar_proyek = $total_net_atb_panjar_panjar_proyek - $total_pengeluaran_ex_panjar_proyek;

                                $totalSaldo = $total_saldo_ex_unit_alat + $total_saldo_ex_panjar_unit_alat + $total_saldo_ex_panjar_proyek;
                            @endphp

                            <style>
                                .total-row td {
                                    background-color: lightblue;
                                }
                            </style>
                            <!-- Footer TOTAL -->
                            <tr class="total-row border-dark table-bordered table-striped">
                                <td></td>
                                <td>TOTAL</td>
                                <!-- Tampilkan total penerimaan -->
                                <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_net_atb_panjar_panjar_proyek, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                                <!-- Tampilkan total pengeluaran -->
                                <td class="text-center">Rp{{ number_format($total_pengeluaran_ex_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_pengeluaran_ex_mutasi_proyek, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_pengeluaran_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_pengeluaran_ex_panjar_proyek, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                                <!-- Tampilkan total saldo -->
                                <td class="text-center">Rp{{ number_format($total_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($total_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                                <td class="text-center">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script crossorigin="anonymous" integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw==" referrerpolicy="no-referrer" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
    <script>
        let state = {
            'suku-cadang': {
                isOpen: false,
                children: ['perbaikan', 'pemeliharaan']
            },
            'perbaikan': {
                isOpen: false,
                children: []
            },
            'pemeliharaan': {
                isOpen: false,
                children: ['maintenance-kit', 'oil-lubricants']
            },
            'maintenance-kit': {
                isOpen: false,
                children: []
            },
            'oil-lubricants': {
                isOpen: false,
                children: []
            },
            'material': {
                isOpen: false,
                children: []
            },
        };

        function toggleAccordion(section) {
            const rows = document.querySelectorAll(`.${section}`);
            const isVisible = rows[0].style.display === 'table-row';
            state[section].isOpen = !isVisible;

            rows.forEach(row => {
                row.style.display = isVisible ? 'none' : 'table-row';
            });

            if (!isVisible) {
                restoreChildrenState(section);
            } else {
                collapseChildren(section);
            }
        }

        function collapseChildren(section) {
            const children = state[section].children;
            children.forEach(child => {
                const rows = document.querySelectorAll(`.${child}`);
                rows.forEach(row => {
                    row.style.display = 'none';
                });
                collapseChildren(child);
            });
        }

        function restoreChildrenState(section) {
            const children = state[section].children;
            children.forEach(child => {
                if (state[child].isOpen) {
                    const rows = document.querySelectorAll(`.${child}`);
                    rows.forEach(row => {
                        row.style.display = 'table-row';
                    });
                    restoreChildrenState(child);
                }
            });
        }

        $(document).ready(function() {
            $('.datetimepicker').datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
                closeOnDateSelect: true,
            });

            // JavaScript for handling the filter button click
            $('#filterButton').on('click', function() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();

                $.ajax({
                    url: "{{ route('summary.fetchData') }}", // The updated route name
                    method: 'GET', // Ensure the method is GET
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                        id_proyek: {{ $proyek->id }},
                    },
                    success: function(response) {
                        // Update the table content with the filtered data
                        $('#table-container').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });
        });
    </script>
@endsection
