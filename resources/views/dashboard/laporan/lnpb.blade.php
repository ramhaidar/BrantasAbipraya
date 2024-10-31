@extends('home')

@section('css')
    <link href="/css/random-css-datatable.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" rel="stylesheet" crossorigin="anonymous" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" referrerpolicy="no-referrer">
    <style>
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
                    </div>
                </div>

                <!-- Tiga Tombol -->
                <div class="d-flex flex-grow-1 justify-content-end">
                    <div class="row align-items-center">
                        {{-- <div class="col-auto">
                            <a class="btn btn-success btn-sm button-export-import-addData"
                                href="{{ route('atb.export', ['proyek' => $proyek->id, 'tipe' => $tipe]) }}">
                                <i class="fa fa-file-excel"></i>
                                <span class="ms-2">Export Data</span>
                            </a>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-info btn-sm button-export-import-addData" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop" href="#">
                                <i class="fa fa-file-import"></i>
                                <span class="ms-2">Import Data</span>
                            </a>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-primary btn-sm button-export-import-addData" data-bs-target="#modalForAdd"
                                data-bs-toggle="modal" type="button">
                                <i class="fa fa-plus"></i>
                                <span class="ms-2">Tambah Data</span>
                            </a>
                        </div> --}}
                    </div>
                </div>
            </div>

            <div class="mt-3 ibox-body table-responsive" style="overflow-x: auto">
                <table class="m-0 border-dark table table-bordered table-striped" id="table-data" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="align-middle text-center" rowspan="2">NO.</th>
                            <th class="align-middle text-center" rowspan="2">U R A I A N</th>
                            <th class="text-center" style="border-bottom: 1px solid black;" colspan="3">S/D BULAN LALU</th>
                            <th class="text-center" style="border-bottom: 1px solid black;" colspan="3">BULAN INI</th>
                            <th class="text-center" style="border-bottom: 1px solid black;" colspan="3">S/D BULAN INI</th>
                        </tr>
                        <tr>
                            <th class="align-middle text-center">PENERIMAAN</th>
                            <th class="align-middle text-center">PENGELUARAN</th>
                            <th class="align-middle text-center">SALDO AKHIR</th>
                            <th class="align-middle text-center">PENERIMAAN</th>
                            <th class="align-middle text-center">PENGELUARAN</th>
                            <th class="align-middle text-center">SALDO AKHIR</th>
                            <th class="align-middle text-center">PENERIMAAN</th>
                            <th class="align-middle text-center">PENGELUARAN</th>
                            <th class="align-middle text-center">SALDO AKHIR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>I</strong></td>
                            <td><strong>SUKU CADANG</strong></td>
                            <td><strong>100.000.000</strong></td>
                            <td><strong>100.000.000</strong></td>
                            <td><strong>100.000.000</strong></td>
                            <td><strong>100.000.000</strong></td>
                            <td><strong>100.000.000</strong></td>
                            <td><strong>100.000.000</strong></td>
                            <td><strong>100.000.000</strong></td>
                            <td><strong>100.000.000</strong></td>
                            <td><strong>100.000.000</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
