@extends('dashboard')

@php
    use Carbon\Carbon;
    Carbon::setLocale('id');

    $tipeMap = [
        'Data ATB Hutang Unit Alat' => 'Hutang Unit Alat',
        'Data ATB Panjar Unit Alat' => 'Panjar Unit Alat',
        'Data ATB Mutasi Proyek' => 'Mutasi Proyek',
        'Data ATB Panjar Proyek' => 'Panjar Proyek',
    ];
    $tipe = $tipeMap[$page] ?? 'default_tipe';
@endphp

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
                <div class="ibox-title flex-grow-1 text-start">
                    {{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}
                </div>

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
                            <button class="btn btn-primary btn-sm button-export-import-addData" id="filterButton" type="button">Filter</button>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-grow-1 justify-content-end">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <a class="btn btn-success btn-sm button-export-import-addData" href="{{ route('atb.export', ['proyek' => $proyek->id, 'tipe' => $tipe]) }}">
                                <i class="fa fa-file-excel"></i>
                                <span class="ms-2">Export Data</span>
                            </a>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-info btn-sm button-export-import-addData" data-bs-toggle="modal" data-bs-target="#staticBackdrop" href="#">
                                <i class="fa fa-file-import"></i>
                                <span class="ms-2">Import Data</span>
                            </a>
                        </div>
                        @if (Auth::user()->role == 'Pegawai' || Auth::user()->role == 'Admin')
                            @if ($page != 'Data ATB Mutasi Proyek')
                                <div class="col-auto">
                                    <a class="btn btn-primary btn-sm button-export-import-addData" data-bs-target="#modalForAdd" data-bs-toggle="modal" type="button">
                                        <i class="fa fa-plus"></i>
                                        <span class="ms-2">Tambah Data</span>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-0 ibox-body table-responsive">
                <table class="m-0 border-dark table table-bordered table-striped" id="table-data" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            @if ($page == 'Data ATB Mutasi Proyek')
                                <th class="align-middle text-center">Asal Proyek</th>
                            @endif
                            <th class="align-middle text-center">Tanggal</th>
                            <th class="align-middle text-center">Kode</th>
                            <th class="align-middle text-center">Supplier</th>
                            <th class="align-middle text-center">Sparepart</th>
                            <th class="align-middle text-center">Part Number</th>
                            <th class="align-middle text-center">Quantity</th>
                            <th class="align-middle text-center">Satuan</th>
                            <th class="align-middle text-center">Nilai</th>
                            <th class="align-middle text-center">Perbaikan</th>
                            <th class="align-middle text-center">Pemeliharaan</th>
                            <th class="align-middle text-center">Workshop</th>
                            <th class="align-middle text-center">Dokumentasi</th>

                            @if (Auth::user()->role == 'Pegawai' || Auth::user()->role == 'Admin')
                                <th class="align-middle text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($atbList as $atb)
                            <tr class="text-center">
                                @if ($page == 'Data ATB Mutasi Proyek')
                                    <td class="align-middle">{{ $atb->asalProyek->nama_proyek ?? '' }}</td>
                                @endif
                                <td class="align-middle">{{ Carbon::parse($atb->tanggal)->translatedFormat('d F Y') }}</td>
                                <td class="align-middle">{{ $atb->komponen->kode }}</td>
                                <td class="align-middle">{{ $atb->supplier ?? ($atb->masterData->supplier ?? '') }}</td>
                                <td class="align-middle">{{ $atb->sparepart ?? ($atb->masterData->sparepart ?? '') }}</td>
                                <td class="align-middle">{{ $atb->part_number ?? ($atb->masterData->part_number ?? '') }}</td>
                                <td class="align-middle">{{ $atb->quantity }}</td>
                                <td class="align-middle">{{ $atb->satuan }}</td>
                                <td class="align-middle">
                                    <button class="btn text-primary" onclick='getDetailNilai("{{ $atb->id }}")'>Rp{{ number_format($atb->net, 0, ',', '.') }}</button>
                                </td>
                                <td class="align-middle">
                                    @if ($atb->komponen->first_group->name == 'PERBAIKAN')
                                        <button class="btn text-primary" onclick='getDetailPerbaikan("{{ $atb->id }}")'>Perbaikan</button>
                                    @else
                                        <button class="btn text-primary border-0 text-secondary" disabled>Perbaikan</button>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if ($atb->komponen->first_group->name == 'PEMELIHARAAN')
                                        <button class="btn text-primary" onclick='getDetailPemeliharaan("{{ $atb->id }}")'>Pemeliharaan</button>
                                    @else
                                        <button class="btn text-primary border-0 text-secondary" disabled>Pemeliharaan</button>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if ($atb->komponen->first_group->name == 'WAREHOUSE')
                                        <button class="btn text-primary" onclick='getDetailWorkshop("{{ $atb->id }}")'>Workshop</button>
                                    @else
                                        <button class="btn text-primary border-0 text-secondary" disabled>Workshop</button>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if ($atb->dokumentasi)
                                        <button class="btn btn-link text-primary" onclick='showDokumentasi("{{ route('atb.dokumentasi', basename($atb->dokumentasi)) }}")'>
                                            Dokumentasi
                                        </button>
                                    @else
                                        <button class="btn btn-link text-secondary" disabled>
                                            Dokumentasi
                                        </button>
                                    @endif
                                </td>

                                @if (Auth::user()->role == 'Pegawai' || Auth::user()->role == 'Admin')
                                    <td class="align-middle center space-nowrap">
                                        <button class="btn btn-danger {{ $page == 'Data ATB Mutasi Proyek' ? 'disabled' : '' }}" data-bs-target="#modalForDelete" data-bs-toggle="modal" onclick="validationSecond({{ $atb->id }}, '{{ $atb->proyek->nama_proyek }}')">
                                            <i class="bi bi-trash3"></i>
                                        </button>

                                        <a class="btn btn-warning ms-3 {{ $page == 'Data ATB Mutasi Proyek' ? 'disabled' : '' }}" data-bs-target="#modalForEdit" data-bs-toggle="modal" onclick="fillFormEdit({{ $atb->id }})">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="table-primary">
                            @if ($page == 'Data ATB Mutasi Proyek')
                                <td class="align-middle text" colspan="8"><strong>Total</strong></td>
                            @else
                                <td class="align-middle text" colspan="7"><strong>Total</strong></td>
                            @endif
                            <td class="align-middle text-center"><strong>{{ $totalNilai }}</strong></td>
                            <td class="align-middle text-center"><strong>{{ $totalPerbaikan }}</strong></td>
                            <td class="align-middle text-center"><strong>{{ $totalPemeliharaan }}</strong></td>
                            <td class="align-middle text-center"><strong>{{ $totalWorkshop }}</strong></td>

                            @if (Auth::user()->role == 'Pegawai' || Auth::user()->role == 'Admin')
                                @if ($page == 'Data ATB Mutasi Proyek')
                                    <td class="align-middle text-center" colspan="2"></td>
                                @else
                                    <td class="align-middle text-center" colspan="2"></td>
                                @endif
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="fade modal" id="modalForDelete" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title">Form Konfirmasi</h1>
                    <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <form method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="mb-3 mt-3">
                                <p class="form-label fw-bold gap-0">Ketik Ulang "<span class="m-0 text-primary" id="model-konfirmasi"></span>"</p>
                                <input class="form-control border-dark" id="name" name="name" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-secondary" data-bs-dismiss="modal">Batal</a>
                        <button class="btn btn-danger" type="submit">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="fade modal" id="modalDetailNilai" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title fw-bold">Nilai</h1>
                    <button class="btn-close" type="button" onclick="closeModalNilai()"></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0" style="overflow-y:auto" method="POST">
                    @csrf
                    <div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1" id="nama_atb"></p>
                            <p class="m-0 fs-5 fw-medium mb-1" id="pembuat_atb"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="harga">Harga (dalam Satuan)</label>
                            <input class="form-control bg-transparent no-border p-0" id="harga" name="harga" required readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="net">Net</label>
                            <input class="form-control bg-transparent no-border p-0" id="net" name="net" required readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="ppn">PPN</label>
                            <input class="form-control bg-transparent no-border p-0" id="ppn" name="ppn" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="bruto">Bruto</label>
                            <input class="form-control bg-transparent no-border p-0" id="bruto" name="bruto" required readonly>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="fade modal" id="modalDetailPerbaikan" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title fw-bold">Perbaikan</h1>
                    <button class="btn-close" type="button" onclick="closeModalPerbaikan()"></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0" style="overflow-y:auto" method="POST">
                    @csrf
                    <div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1" id="nama_atb"></p>
                            <p class="m-0 fs-5 fw-medium mb-1" id="pembuat_atb"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="kode">Kode</label>
                            <input class="form-control bg-transparent no-border p-0" id="kode" name="kode" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="value">Value</label>
                            <input class="form-control bg-transparent no-border p-0" id="value" name="value" required readonly>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="fade modal" id="modalDetailPemeliharaan" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title fw-bold">Pemeliharaan</h1>
                    <button class="btn-close" type="button" onclick="closeModalPemeliharaan()"></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0" style="overflow-y:auto" method="POST">
                    @csrf
                    <div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1" id="nama_atb"></p>
                            <p class="m-0 fs-5 fw-medium mb-1" id="pembuat_atb"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="kode">Kode</label>
                            <input class="form-control bg-transparent no-border p-0" id="kode" name="kode" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="value">Value</label>
                            <input class="form-control bg-transparent no-border p-0" id="value" name="value" required readonly>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="fade modal" id="modalDetailWorkshop" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title fw-bold">Workshop</h1>
                    <button class="btn-close" type="button" onclick="closeModalWorkshop()"></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0" style="overflow-y:auto" method="POST">
                    @csrf
                    <div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1" id="nama_atb_workshop"></p>
                            <p class="m-0 fs-5 fw-medium mb-1" id="pembuat_atb_workshop"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="kode_workshop">Kode</label>
                            <input class="form-control bg-transparent no-border p-0" id="kode_workshop" name="kode" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="value_workshop">Value</label>
                            <input class="form-control bg-transparent no-border p-0" id="value_workshop" name="value" required readonly>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="fade modal" id="modalForAdd" data-bs-backdrop="static" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fs-5 modal-title fw-bold">Tambah Data ATB Baru</h5>
                    <button class="btn-close" type="button" onclick="closeModalAdd()"></button>
                </div>
                <div class="w-100 modal-body row">
                    <form class="w-100 align-items-center flex-column gap-0" id="addDataForm" method="POST" action="{{ route('atb.post.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4" hidden>
                            <label class="form-label" for="tipe">Tipe ATB</label>
                            <div class="input-group">
                                <select class="form-control" id="pilihan-proyek1" name="tipe">
                                    <option value="hutang-unit-alat" {{ $page == 'Data ATB Hutang Unit Alat' ? 'selected' : '' }}>Hutang Unit Alat</option>
                                    <option value="panjar-unit-alat" {{ $page == 'Data ATB Panjar Unit Alat' ? 'selected' : '' }}>Panjar Unit Alat</option>
                                    <option value="mutasi-proyek" {{ $page == 'Data ATB Mutasi Proyek' ? 'selected' : '' }}>Mutasi Proyek</option>
                                    <option value="panjar-proyek" {{ $page == 'Data ATB Panjar Proyek' ? 'selected' : '' }}>Panjar Proyek</option>
                                </select>
                            </div>
                        </div>
                        @if ($page == 'Data ATB Mutasi Proyek')
                            <div class="mb-3">
                                <label class="form-label" for="asal_proyek">Asal Proyek</label>
                                <input class="form-control" id="asal_proyek_display" value="{{ $proyek->nama_proyek }}" readonly>
                                <input id="asal_proyek" name="asal_proyek" type="hidden" value="{{ $proyek->id }}">
                            </div>
                        @endif
                        <input class="form-control" id="id_proyek" name="id_proyek" value="{{ $proyek->id }}" hidden required>
                        <div class="mb-3">
                            <label class="form-label" for="tanggal">Tanggal</label>
                            <input class="form-control datetimepicker" id="tanggal" name="tanggal" required autocomplete="off">
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="pilihan-kode1">Kode</label>
                            <div class="input-group">
                                <select class="form-control" id="pilihan-kode1" name="kode">
                                    <option value="A1">A1: CABIN</option>
                                    <option value="A2">A2: ENGINE SYSTEM</option>
                                    <option value="A3">A3: TRANSMISSION SYSTEM</option>
                                    <option value="A4">A4: CHASSIS & SWING MACHINERY</option>
                                    <option value="A5">A5: DIFFERENTIAL SYSTEM</option>
                                    <option value="A6">A6: ELECTRICAL SYSTEM</option>
                                    <option value="A7">A7: HYDRAULIC/PNEUMATIC SYSTEM</option>
                                    <option value="A8">A8: STEERING SYSTEM</option>
                                    <option value="A9">A9: BRAKE SYSTEM</option>
                                    <option value="A10">A10: SUSPENSION</option>
                                    <option value="A11">A11: ATTACHMENT</option>
                                    <option value="A12">A12: UNDERCARRIAGE</option>
                                    <option value="A13">A13: FINAL DRIVE</option>
                                    <option value="A14">A14: FREIGHT COST</option>
                                    <option value="B11">B11: Oil Filter</option>
                                    <option value="B12">B12: Fuel Filter</option>
                                    <option value="B13">B13: Air Filter</option>
                                    <option value="B14">B14: Hydraulic Filter</option>
                                    <option value="B15">B15: Transmission Filter</option>
                                    <option value="B16">B16: Differential Filter</option>
                                    <option value="B21">B21: Engine Oil</option>
                                    <option value="B22">B22: Hydraulic Oil</option>
                                    <option value="B23">B23: Transmission Oil</option>
                                    <option value="B24">B24: Final Drive Oil</option>
                                    <option value="B25">B25: Swing & Damper Oil</option>
                                    <option value="B26">B26: Differential Oil</option>
                                    <option value="B27">B27: Grease</option>
                                    <option value="B28">B28: Brake & Power Steering Fluid</option>
                                    <option value="B29">B29: Coolant</option>
                                    <option value="B3">B3: Tyre</option>
                                    <option value="C1">C1: Workshop</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="master_data_select">Pilih Item dari Master Data</label>
                            <select class="form-control" id="master_data_select" name="master_data">
                                <option value="">-- Pilih Supplier, Sparepart, dan Part Number --</option>
                                @foreach ($masterDataList as $masterData)
                                    <option data-supplier="{{ $masterData->supplier }}" data-sparepart="{{ $masterData->sparepart }}" data-part_number="{{ $masterData->part_number }}" value="{{ $masterData->id }}">
                                        {{ $masterData->supplier }} - {{ $masterData->sparepart }} - {{ $masterData->part_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="quantity">Quantity</label>
                            <input class="form-control" id="quantity" name="quantity" type="number" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="pilihan-satuan1">Satuan</label>
                            <div class="input-group">
                                <select class="form-control" id="pilihan-satuan1" name="satuan">
                                    <option value="PCS">PCS</option>
                                    <option value="SET">SET</option>
                                    <option value="BTL">BTL</option>
                                    <option value="LTR">LTR</option>
                                    <option value="KG">KG</option>
                                    <option value="BTG">BTG</option>
                                    <option value="PAIL">PAIL</option>
                                    <option value="MTR">MTR</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="harga">Harga (dalam Satuan)</label>
                            <input class="form-control" id="harga" name="harga" type="number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="dokumentasi">Upload Dokumentasi</label>
                            <input class="form-control" id="dokumentasi" name="dokumentasi" type="file" accept="image/jpeg,image/jpg,image/png,image/heic,image/heif">
                        </div>
                        <div class="m-0 p-0 d-flex modal-footer pt-4 w-100">
                            <button class="btn btn-success container-fluid m-0 p-0 py-2" type="submit">Tambah Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="fade modal" id="modalForEdit" data-bs-backdrop="static" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fs-5 modal-title fw-bold">Ubah Data ATB</h5>
                    <button class="btn-close" type="button" onclick="closeModalEdit()"></button>
                </div>
                <div class="w-100 modal-body row">
                    <form class="w-100 align-items-center flex-column gap-0" id="editDataForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input id="edit_atb_id" name="id" type="hidden">

                        <div class="mb-4" hidden>
                            <label class="form-label" for="tipe">Tipe ATB</label>
                            <select class="form-control" id="edit_tipe" name="tipe">
                                <option value="hutang-unit-alat" {{ $page == 'Data ATB Hutang Unit Alat' ? 'selected' : '' }}>Hutang Unit Alat</option>
                                <option value="panjar-unit-alat" {{ $page == 'Data ATB Panjar Unit Alat' ? 'selected' : '' }}>Panjar Unit Alat</option>
                                <option value="mutasi-proyek" {{ $page == 'Data ATB Mutasi Proyek' ? 'selected' : '' }}>Mutasi Proyek</option>
                                <option value="panjar-proyek" {{ $page == 'Data ATB Panjar Proyek' ? 'selected' : '' }}>Panjar Proyek</option>
                            </select>
                        </div>

                        @if ($page == 'Data ATB Mutasi Proyek')
                            <div class="mb-3">
                                <label class="form-label" for="edit_asal_proyek">Asal Proyek</label>
                                <input class="form-control" id="edit_asal_proyek" name="asal_proyek">
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label" for="edit_tanggal">Tanggal</label>
                            <input class="form-control datetimepicker" id="edit_tanggal" name="tanggal" required autocomplete="off">
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="pilihan-kode2">Kode</label>
                            <div class="input-group">
                                <select class="form-control" id="pilihan-kode2" name="kode">
                                    <option value="A1">A1: CABIN</option>
                                    <option value="A2">A2: ENGINE SYSTEM</option>
                                    <option value="A3">A3: TRANSMISSION SYSTEM</option>
                                    <option value="A4">A4: CHASSIS & SWING MACHINERY</option>
                                    <option value="A5">A5: DIFFERENTIAL SYSTEM</option>
                                    <option value="A6">A6: ELECTRICAL SYSTEM</option>
                                    <option value="A7">A7: HYDRAULIC/PNEUMATIC SYSTEM</option>
                                    <option value="A8">A8: STEERING SYSTEM</option>
                                    <option value="A9">A9: BRAKE SYSTEM</option>
                                    <option value="A10">A10: SUSPENSION</option>
                                    <option value="A11">A11: ATTACHMENT</option>
                                    <option value="A12">A12: UNDERCARRIAGE</option>
                                    <option value="A13">A13: FINAL DRIVE</option>
                                    <option value="A14">A14: FREIGHT COST</option>
                                    <option value="B11">B11: Oil Filter</option>
                                    <option value="B12">B12: Fuel Filter</option>
                                    <option value="B13">B13: Air Filter</option>
                                    <option value="B14">B14: Hydraulic Filter</option>
                                    <option value="B15">B15: Transmission Filter</option>
                                    <option value="B16">B16: Differential Filter</option>
                                    <option value="B21">B21: Engine Oil</option>
                                    <option value="B22">B22: Hydraulic Oil</option>
                                    <option value="B23">B23: Transmission Oil</option>
                                    <option value="B24">B24: Final Drive Oil</option>
                                    <option value="B25">B25: Swing & Damper Oil</option>
                                    <option value="B26">B26: Differential Oil</option>
                                    <option value="B27">B27: Grease</option>
                                    <option value="B28">B28: Brake & Power Steering Fluid</option>
                                    <option value="B29">B29: Coolant</option>
                                    <option value="B3">B3: Tyre</option>
                                    <option value="C1">C1: Workshop</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="edit_master_data">Pilih Item dari Master Data</label>
                            <select class="form-control" id="edit_master_data" name="master_data">
                                <option value="">-- Pilih Supplier, Sparepart, dan Part Number --</option>
                                @foreach ($masterDataList as $masterData)
                                    <option data-supplier="{{ $masterData->supplier }}" data-sparepart="{{ $masterData->sparepart }}" data-part_number="{{ $masterData->part_number }}" value="{{ $masterData->id }}">
                                        {{ $masterData->supplier }} - {{ $masterData->sparepart }} - {{ $masterData->part_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="edit_quantity">Quantity</label>
                            <input class="form-control" id="edit_quantity" name="quantity" type="number" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="edit_satuan">Satuan</label>
                            <select class="form-control" id="edit_satuan" name="satuan">
                                <option value="PCS">PCS</option>
                                <option value="SET">SET</option>
                                <option value="BTL">BTL</option>
                                <option value="LTR">LTR</option>
                                <option value="KG">KG</option>
                                <option value="BTG">BTG</option>
                                <option value="PAIL">PAIL</option>
                                <option value="MTR">MTR</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="edit_harga">Harga (dalam Satuan)</label>
                            <input class="form-control" id="edit_harga" name="harga" type="number" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="edit_dokumentasi">Upload Dokumentasi</label>
                            <input class="form-control" id="edit_dokumentasi" name="dokumentasi" type="file" accept="image/jpeg,image/jpg,image/png,image/heic,image/heif">
                        </div>

                        <div class="m-0 p-0 d-flex modal-footer pt-4 w-100">
                            <button class="btn btn-success container-fluid m-0 p-0 py-2" type="submit">Ubah Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Import Data</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <form action="{{ route('atb.import.post') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file">Upload Excel File</label>
                            <input class="form-control" name="file" type="file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                        <button class="btn btn-primary" type="submit">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDokumentasi" aria-labelledby="modalDokumentasiLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDokumentasiLabel">Dokumentasi ATB</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img class="img-fluid" id="dokumentasiImage" src="" alt="Dokumentasi ATB">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script crossorigin="anonymous" integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw==" referrerpolicy="no-referrer" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
    <script>
        $(".nav-tabs a").click(function() {
            $(this).tab('show');
        });

        function closeModalNilai() {
            $('#modalDetailNilai').modal('hide');
        }

        function closeModalPerbaikan() {
            $('#modalDetailPerbaikan').modal('hide');
        }

        function closeModalPemeliharaan() {
            $('#modalDetailPemeliharaan').modal('hide');
        }

        function closeModalWorkshop() {
            $('#modalDetailWorkshop').modal('hide');
        }

        function closeModalAdd() {
            $('#modalForAdd').modal('hide');
        }

        function closeModalEdit() {
            $('#modalForEdit').modal('hide');
        }

        function closeModalDelete() {
            $('#modalForDelete').modal('hide');
        }

        function showDokumentasi(imageUrl) {
            document.getElementById('dokumentasiImage').src = imageUrl;
            new bootstrap.Modal(document.getElementById('modalDokumentasi')).show();
        }

        const validationSecond = (id, name) => {
            document.querySelector('#model-konfirmasi').innerText = name;
            document.querySelector('#modalForDelete form').action = `/atb/hutang_unit_alat/${id}`;
        };

        document.querySelector('#modalForDelete form').addEventListener('submit', function(event) {
            var confirmationText = document.getElementById('model-konfirmasi').innerText.trim();
            var inputName = document.querySelector('#modalForDelete #name');
            if (inputName.value.trim() !== confirmationText) {
                event.preventDefault();
                showSweetAlert2('Masukkan tidak sesuai. Silakan coba lagi!', 'error');
            }
        });

        function showSweetAlert2(msg, icon) {
            let title = '';
            if (icon == 'success') {
                title = 'Transaksi Berhasil!';
                msg = `Berhasil ${msg}.`;
            }
            Swal.fire({
                html: msg,
                icon: icon,
                title: title,
                confirmButtonText: 'Oke',
                customClass: {
                    popup: 'alert-custom-css'
                }
            });
        }

        function fillFormEdit(atbId) {
            document.querySelector('#modalForEdit form').action = `/atb/hutang_unit_alat/edit/${atbId}`;

            $.ajax({
                url: `/atb/hutang_unit_alat/${atbId}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const atb = response.data;

                    $('#edit_atb_id').val(atb.id);

                    $('#edit_tanggal').val(atb.tanggal);

                    let tipeValue = '';
                    switch (atb.tipe) {
                        case 'Hutang Unit Alat':
                            tipeValue = 'hutang-unit-alat';
                            break;
                        case 'Panjar Unit Alat':
                            tipeValue = 'panjar-unit-alat';
                            break;
                        case 'Mutasi Proyek':
                            tipeValue = 'mutasi-proyek';
                            break;
                        case 'Panjar Proyek':
                            tipeValue = 'panjar-proyek';
                            break;
                        default:
                            tipeValue = '';
                    }
                    $('#edit_tipe').val(tipeValue).trigger('change');

                    if (tipeValue === 'mutasi-proyek') {
                        $('#edit_asal_proyek').val(atb.asal_proyek.nama_proyek || '');
                    }

                    var selectElementKode = $('#pilihan-kode2');
                    var kodeValue = atb.komponen.kode.split(':')[0].trim();
                    selectElementKode.val(kodeValue).trigger('change');

                    if (atb.id_master_data) {
                        $('#edit_master_data').val(atb.id_master_data).trigger('change');
                    } else {
                        $('#edit_master_data').val('').trigger('change');
                    }

                    $('#edit_quantity').val(atb.quantity);
                    $('#edit_harga').val(atb.harga);

                    $('#edit_satuan').val(atb.satuan).trigger('change');

                    $('#modalForEdit').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch ATB data:', error);
                    showSweetAlert2('Terjadi kesalahan saat mengambil data. Silakan coba lagi.', 'error');
                }
            });
        }

        function getATB(params) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: "/atb/hutang_unit_alat/" + params,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        resolve(response.data);
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }

        function getDetailNilai(params) {
            getATB(params)
                .then(data => {
                    var formattedHarga = 'Rp' + data.harga.toLocaleString('id-ID');
                    var formattedNet = 'Rp' + data.net.toLocaleString('id-ID');
                    var formattedPpn = 'Rp' + data.ppn.toLocaleString('id-ID');
                    var formattedBruto = 'Rp' + data.bruto.toLocaleString('id-ID');

                    document.querySelector('#modalDetailNilai #nama_atb').innerText = "Nama Proyek: " + data.proyek.nama_proyek;
                    document.querySelector('#modalDetailNilai #harga').value = formattedHarga;
                    document.querySelector('#modalDetailNilai #net').value = formattedNet;
                    document.querySelector('#modalDetailNilai #ppn').value = formattedPpn;
                    document.querySelector('#modalDetailNilai #bruto').value = formattedBruto;
                    $('#modalDetailNilai').modal('show');
                })
                .catch(error => {
                    $('#modalDetailNilai').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        function getDetailPerbaikan(params) {
            getATB(params)
                .then(data => {
                    var formattedValue = 'Rp' + data.net.toLocaleString('id-ID');

                    document.querySelector('#modalDetailPerbaikan #nama_atb').innerText = "Nama Proyek: " + data.proyek.nama_proyek;
                    document.querySelector('#modalDetailPerbaikan #kode').value = data.komponen.kode;
                    document.querySelector('#modalDetailPerbaikan #value').value = formattedValue;
                    $('#modalDetailPerbaikan').modal('show');
                })
                .catch(error => {
                    $('#modalDetailPerbaikan').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        function getDetailPemeliharaan(params) {
            getATB(params)
                .then(data => {
                    var formattedValue = 'Rp' + data.net.toLocaleString('id-ID');

                    document.querySelector('#modalDetailPemeliharaan #nama_atb').innerText = "Nama Proyek: " + data.proyek.nama_proyek;
                    document.querySelector('#modalDetailPemeliharaan #kode').value = data.komponen.kode;
                    document.querySelector('#modalDetailPemeliharaan #value').value = formattedValue;
                    $('#modalDetailPemeliharaan').modal('show');
                })
                .catch(error => {
                    $('#modalDetailPemeliharaan').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        function getDetailWorkshop(params) {
            getATB(params)
                .then(data => {
                    var formattedValue = 'Rp' + data.net.toLocaleString('id-ID');

                    document.querySelector('#modalDetailWorkshop #nama_atb_workshop').innerText = "Nama Proyek: " + data.proyek.nama_proyek;
                    document.querySelector('#modalDetailWorkshop #kode_workshop').value = data.komponen.kode;
                    document.querySelector('#modalDetailWorkshop #value_workshop').value = formattedValue;
                    $('#modalDetailWorkshop').modal('show');
                })
                .catch(error => {
                    $('#modalDetailWorkshop').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        $(document).ready(function() {
            var table = $('#table-data').DataTable({
                language: {
                    paginate: {
                        previous: '<i class="bi bi-caret-left"></i>',
                        next: '<i class="bi bi-caret-right"></i>'
                    }
                },
                pageLength: -1,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                ordering: false
            });

            $('#pilihan-kode1').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#pilihan-kode2').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit')
            });

            $('#pilihan-satuan1').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#pilihan-satuan2').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit')
            });

            $('.datetimepicker').datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
                closeOnDateSelect: true,
            });

            $('#master_data_select').select2({
                placeholder: "Pilih Supplier, Sparepart, atau Part Number",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            function checkDatesAndFetchData() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var tipeATB = "{{ $tipe }}";

                if (startDate && endDate) {
                    $.ajax({
                        url: '{{ route('atb.fetchData') }}',
                        method: 'GET',
                        data: {
                            start_date: startDate,
                            end_date: endDate,
                            id_proyek: {{ $proyek->id }},
                            tipe: tipeATB,
                        },
                        success: function(response) {
                            table.clear().draw();

                            let totalNilai = 0;
                            let totalPerbaikan = 0;
                            let totalPemeliharaan = 0;
                            let totalWorkshop = 0;

                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(atb) {
                                    // console.log(atb);
                                    var row = [];

                                    @if ($page == 'Data ATB Mutasi Proyek')
                                        row.push(atb.asal_proyek.nama_proyek || '');
                                    @endif

                                    row.push(
                                        atb.tanggal ? moment(atb.tanggal).format('DD MMMM YYYY') : '',
                                        atb.komponen.kode || '',
                                        atb.master_data.supplier || '',
                                        atb.master_data.sparepart || '',
                                        atb.master_data.part_number || '',
                                        atb.quantity || '',
                                        atb.satuan || '',
                                        '<button class="btn text-primary" onclick="getDetailNilai(\'' + atb.id + '\')">Rp' + atb.bruto.toLocaleString('id-ID') + '</button>',
                                        atb.komponen && atb.komponen.first_group.name === 'PERBAIKAN' ?
                                        '<button class="btn text-primary" onclick="getDetailPerbaikan(\'' + atb.id + '\')">Perbaikan</button>' :
                                        '<button class="btn text-primary border-0 text-secondary" disabled>Perbaikan</button>',
                                        atb.komponen && atb.komponen.first_group.name === 'PEMELIHARAAN' ?
                                        '<button class="btn text-primary" onclick="getDetailPemeliharaan(\'' + atb.id + '\')">Pemeliharaan</button>' :
                                        '<button class="btn text-primary border-0 text-secondary" disabled>Pemeliharaan</button>',
                                        atb.komponen && atb.komponen.first_group.name === 'WAREHOUSE' ?
                                        '<button class="btn text-primary" onclick="getDetailWorkshop(\'' + atb.id + '\')">Workshop</button>' :
                                        '<button class="btn text-primary border-0 text-secondary" disabled>Workshop</button>',
                                        atb.dokumentasi ?
                                        '<button class="btn btn-link text-primary" onclick="showDokumentasi(\'' + '{{ route('atb.dokumentasi', '') }}' + '/' + atb.dokumentasi.split('/').pop() + '\')">Dokumentasi</button>' :
                                        '<button class="btn btn-link text-secondary" disabled>Dokumentasi</button>'
                                    );

                                    @if (Auth::user()->role == 'Pegawai' || Auth::user()->role == 'Admin')
                                        row.push(
                                            '<div class="d-flex justify-content-center">' +
                                            '<button class="btn btn-danger me-2 {{ $page == 'Data ATB Mutasi Proyek' ? 'disabled' : '' }}" onclick="validationSecond(' + atb.id + ', \'' + atb.proyek.nama_proyek + '\')" data-bs-target="#modalForDelete" data-bs-toggle="modal"><i class="bi bi-trash3"></i></button>' +
                                            '<a class="btn btn-warning {{ $page == 'Data ATB Mutasi Proyek' ? 'disabled' : '' }}" onclick="fillFormEdit(' + atb.id + ')" data-bs-target="#modalForEdit" data-bs-toggle="modal"><i class="bi bi-pencil-square"></i></a>' +
                                            '</div>'
                                        );
                                    @endif

                                    table.row.add(row);

                                    totalNilai += atb.bruto || 0;
                                    if (atb.komponen && atb.komponen.first_group.name === 'PERBAIKAN') {
                                        totalPerbaikan += atb.net || 0;
                                    }
                                    if (atb.komponen && atb.komponen.first_group.name === 'PEMELIHARAAN') {
                                        totalPemeliharaan += atb.net || 0;
                                    }
                                    if (atb.komponen && atb.komponen.first_group.name === 'WAREHOUSE') {
                                        totalWorkshop += atb.net || 0;
                                    }
                                });

                                table.draw();

                                @if ($page == 'Data ATB Mutasi Proyek')
                                    var footerCells = $('#table-data tfoot tr').find('td').eq(8);
                                @else
                                    var footerCells = $('#table-data tfoot tr').find('td').eq(8);
                                @endif
                                footerCells.html('<strong>Rp' + totalNilai.toLocaleString('id-ID') + '</strong>');
                                $('#table-data tfoot tr').find('td').eq(9).html('<strong>Rp' + totalPerbaikan.toLocaleString('id-ID') + '</strong>');
                                $('#table-data tfoot tr').find('td').eq(10).html('<strong>Rp' + totalPemeliharaan.toLocaleString('id-ID') + '</strong>');
                                $('#table-data tfoot tr').find('td').eq(11).html('<strong>Rp' + totalWorkshop.toLocaleString('id-ID') + '</strong>');
                            } else {
                                showSweetAlert2('Tidak ada data yang ditemukan dalam rentang tanggal yang dipilih', 'info');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to fetch data:', error);
                            showSweetAlert2('Terjadi kesalahan saat mengambil data. Silakan coba lagi.', 'error');
                        }
                    });
                }
            }

            $('#filterButton').on('click', function() {
                checkDatesAndFetchData();
            });

        });
    </script>

    @if (session()->has('success'))
        <script>
            showSweetAlert2('{{ session('success') }}', 'success');
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            showSweetAlert2('{{ session('error') }}', 'error');
        </script>
    @endif

    @if ($errors->any())
        <script>
            let errInput = '<ul class="m-0 no-bullet">';
            @foreach ($errors->all() as $error)
                errInput += "<li>{{ $error }}</li>";
            @endforeach
            errInput += "</ul>", showSweetAlert2(errInput, "error");
        </script>
    @endif
@endsection
