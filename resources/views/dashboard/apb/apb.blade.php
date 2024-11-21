@extends('dashboard')

@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
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

        @media screen and (max-width: 500px) {
            #button-for-modal-add span {
                display: none;
            }

            #button-for-modal-add {
                font-size: 20px;
            }

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
                        <div class="col-auto">
                            <!-- Tambahkan tombol filter -->
                            <button class="btn btn-primary btn-sm button-export-import-addData" id="filterButton" type="button">Filter</button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if (Auth::user()->role == 'Pegawai' || Auth::user()->role == 'Admin')
                    <div class="d-flex flex-grow-1 justify-content-end">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <a class="btn btn-success btn-sm button-export-import-addData">
                                    <i class="fa fa-file-excel"></i>
                                    <span class="ms-2">Export Data</span>
                                </a>
                            </div>
                            <div class="col-auto">
                                <a class="btn btn-primary btn-sm button-export-import-addData" data-bs-target="#modalForAdd" data-bs-toggle="modal" type="button">
                                    <i class="fa fa-plus"></i>
                                    <span class="ms-2">Tambah Data</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Table -->
            <div class="mt-0 ibox-body table-responsive">
                <table class="m-0 border-dark table table-bordered table-striped" id="table-data" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            {{-- <th class="align-middle text-center" hidden></th> --}}
                            @if ($page == 'Data APB EX Mutasi Saldo')
                                <th class="align-middle text-center">Tujuan Proyek</th>
                            @endif
                            <th class="align-middle text-center">Tanggal</th>
                            <th class="align-middle text-center">Kode</th>
                            <th class="align-middle text-center">Supplier</th>
                            <th class="align-middle text-center">Sparepart</th>
                            <th class="align-middle text-center">Part Number</th>
                            <th class="align-middle text-center">Quantity</th>
                            <th class="align-middle text-center">Satuan</th>
                            @if ($page != 'Data APB EX Mutasi Saldo')
                                <th class="align-middle text-center">Alat</th>
                            @endif
                            <th class="align-middle text-center">Nilai</th>
                            <th class="align-middle text-center">Perbaikan</th>
                            <th class="align-middle text-center">Pemeliharaan</th>
                            <th class="align-middle text-center">Workshop</th>
                            {{-- <th class="align-middle text-center">Dokumentasi</th> --}}
                            {{-- @if (Auth::user()->role == 'Pegawai' || Auth::user()->role == 'Admin')
                                <th class="align-middle text-center">Aksi</th>
                            @endif --}}
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalNilaiAPB = 0;
                            $totalNilaiPerbaikan = 0;
                            $totalNilaiPemeliharaan = 0;
                            $totalNilaiWorkshop = 0;
                        @endphp
                        @foreach ($atbList as $item)
                            @foreach ($item->saldo->apb as $apb)
                                <tr class="text-center">
                                    @if ($page == 'Data APB EX Mutasi Saldo')
                                        {{-- <td class="align-middle">{{ $apb->tujuanProyek->nama_proyek }}</td> --}}
                                        <td class="align-middle">{{ $apb->tujuanProyek->nama_proyek ?? '' }}</td>
                                    @endif
                                    <td class="align-middle">{{ Carbon::parse($apb->tanggal)->translatedFormat('d F Y') }}</td>
                                    <td class="align-middle">{{ $item->komponen->kode }}</td>
                                    <td class="align-middle">{{ $item->masterData->supplier }}</td>
                                    <td class="align-middle">{{ $item->masterData->sparepart }}</td>
                                    <td class="align-middle">{{ $item->masterData->part_number }}</td>
                                    <td class="align-middle">{{ $apb->quantity }}</td>
                                    <td class="align-middle">{{ $item->satuan }}</td>
                                    @if ($page != 'Data APB EX Mutasi Saldo')
                                        <td class="align-middle">
                                            <button class="btn text-primary" onclick='getDetailAlat("{{ $apb->id }}")'>
                                                {{ $apb->alat->kode_alat ?? '-' }}
                                            </button>
                                        </td>
                                    @endif
                                    <td class="align-middle">
                                        <button class="btn text-primary" onclick='getDetailNilai("{{ $item->saldo->atb->id }}")'>
                                            @php
                                                $totalNilaiAPB += $item->harga * $apb->quantity;
                                            @endphp
                                            Rp{{ number_format($item->harga * $apb->quantity, 0, ',', '.') }}
                                        </button>
                                    </td>
                                    <td class="align-middle">
                                        @if ($item->komponen->first_group->name == 'PERBAIKAN')
                                            @php
                                                $totalNilaiPerbaikan += $item->harga * $apb->quantity;
                                            @endphp
                                            <button class="btn text-primary" onclick='getDetailPerbaikan("{{ $item->id }}", "{{ $apb->quantity }}")'>
                                                Perbaikan
                                            </button>
                                        @else
                                            <button class="btn text-primary border-0 text-secondary" disabled>
                                                Perbaikan
                                            </button>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if ($item->komponen->first_group->name == 'PEMELIHARAAN')
                                            @php
                                                $totalNilaiPemeliharaan += $item->harga * $apb->quantity;
                                            @endphp
                                            <button class="btn text-primary" onclick='getDetailPemeliharaan("{{ $item->id }}", "{{ $apb->quantity }}")'>
                                                Pemeliharaan
                                            </button>
                                        @else
                                            <button class="btn text-primary border-0 text-secondary" disabled>
                                                Pemeliharaan
                                            </button>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if ($item->komponen->first_group->name == 'WAREHOUSE')
                                            @php
                                                $totalNilaiWorkshop += $item->harga * $apb->quantity;
                                            @endphp
                                            <button class="btn text-primary" onclick='getDetailWorkshop("{{ $item->id }}", "{{ $apb->quantity }}")'>
                                                Workshop
                                            </button>
                                        @else
                                            <button class="btn text-primary border-0 text-secondary" disabled>
                                                Workshop
                                            </button>
                                        @endif
                                    </td>

                                    {{-- @if (Auth::user()->role == 'Pegawai' || Auth::user()->role == 'Admin')
                                        <td class="align-middle center space-nowrap">
                                            <button class="btn btn-danger" data-bs-target="#modalForDelete" data-bs-toggle="modal" onclick="validationSecond({{ $apb->id }}, '{{ $item->proyek->nama_proyek }}')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                            <a class="btn btn-warning ms-3" data-bs-target="#modalForEdit" data-bs-toggle="modal" onclick="fillFormEdit({{ $item->id }}, {{ $apb->quantity }}, {{ $apb->alat->id }}, {{ $apb->id }})">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </td>
                                    @endif --}}
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-primary">
                            {{-- <td hidden></td> --}}
                            @if ($page == 'Data APB EX Mutasi Saldo')
                                <td class="align-middle" colspan="8"><strong class="ps-2">Total</strong></td>
                            @else
                                <td class="align-middle" colspan="8"><strong class="ps-2">Total</strong></td>
                            @endif
                            <td class="align-middle text-center"><strong>Rp{{ number_format($totalNilaiAPB, 0, ',', '.') }}</strong></td>
                            <td class="align-middle text-center"><strong>Rp{{ number_format($totalNilaiPerbaikan, 0, ',', '.') }}</strong></td>
                            <td class="align-middle text-center"><strong>Rp{{ number_format($totalNilaiPemeliharaan, 0, ',', '.') }}</strong></td>
                            <td class="align-middle text-center"><strong>Rp{{ number_format($totalNilaiWorkshop, 0, ',', '.') }}</strong></td>
                            {{-- @if (Auth::user()->role == 'Pegawai' || Auth::user()->role == 'Admin')
                                <td class="align-middle text-center" colspan="1"></td>
                            @endif --}}
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Delete -->
    <div class="fade modal" id="modalForDelete" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title" id="staticBackdropLabel">Form Konfirmasi</h1>
                    <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <form method="POST">
                    @csrf
                    @method('DELETE')
                    <input id="apb_id" name="apb_id" value="" hidden>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="mb-3 mt-3">
                                <p class="form-label fw-bold gap-0" for="name" required>
                                    Ketik Ulang "
                                    <span class="m-0 text-primary" id="model-konfirmasi"></span>"
                                </p>
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

    <!-- Modal for Detail Alat -->
    <div class="fade modal" id="modalDetailAlat" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalDetailAlatLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title fw-bold" id="modalDetailAlatLabel">Alat</h1>
                    <button class="btn-close" type="button" onclick="closeModalAlat()"></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0" style="overflow-y:auto" method="POST">
                    @csrf
                    <div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1" id="nama_apb"></p>
                            <p class="m-0 fs-5 fw-medium mb-1" id="pembuat_apb"></p>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="jenis_alat">Jenis Alat</label>
                                <input class="form-control bg-transparent no-border p-0" id="jenis_alat" name="jenis_alat" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="kode_alat">Kode Alat</label>
                                <input class="form-control bg-transparent no-border p-0" id="kode_alat" name="kode_alat" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="merek_alat">Merek Alat</label>
                                <input class="form-control bg-transparent no-border p-0" id="merek_alat" name="merek_alat" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="tipe_alat">Tipe Alat</label>
                                <input class="form-control bg-transparent no-border p-0" id="tipe_alat" name="tipe_alat" required readonly>
                            </div>
                        </div>

                    </div>
                    <div class="w-100 d-flex modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Keluar</button>
                        <button class="btn btn-primary" type="submit" hidden>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Detail Nilai -->
    <div class="fade modal" id="modalDetailNilai" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalDetailNilaiLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title fw-bold" id="modalDetailNilaiLabel">Nilai</h1>
                    <button class="btn-close" type="button" onclick="closeModalNilai()"></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0" style="overflow-y:auto" method="POST">
                    @csrf
                    <div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1" id="nama_apb"></p>
                            <p class="m-0 fs-5 fw-medium mb-1" id="pembuat_apb"></p>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="harga">Harga</label>
                                <input class="form-control bg-transparent no-border p-0" id="harga" name="harga" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="net">Net</label>
                                <input class="form-control bg-transparent no-border p-0" id="net" name="net" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="ppn">PPN</label>
                                <input class="form-control bg-transparent no-border p-0" id="ppn" name="ppn" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="bruto">Bruto</label>
                                <input class="form-control bg-transparent no-border p-0" id="bruto" name="bruto" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Keluar</button>
                        <button class="btn btn-primary" type="submit" hidden>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Detail Perbaikan -->
    <div class="fade modal" id="modalDetailPerbaikan" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalDetailPerbaikanLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title fw-bold" id="modalDetailPerbaikanLabel">Perbaikan</h1>
                    <button class="btn-close" type="button" onclick="closeModalPerbaikan()"></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0" style="overflow-y:auto" method="POST">
                    @csrf
                    <div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1" id="nama_apb"></p>
                            <p class="m-0 fs-5 fw-medium mb-1" id="pembuat_apb"></p>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="kode">Kode</label>
                                <input class="form-control bg-transparent no-border p-0" id="kode" name="kode" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="value">Value</label>
                                <input class="form-control bg-transparent no-border p-0" id="value" name="value" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Keluar</button>
                        <button class="btn btn-primary" type="submit" hidden>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Detail Pemeliharaan -->
    <div class="fade modal" id="modalDetailPemeliharaan" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalDetailPemeliharaanLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title fw-bold" id="modalDetailPemeliharaanLabel">Pemeliharaan</h1>
                    <button class="btn-close" type="button" onclick="closeModalPemeliharaan()"></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0" style="overflow-y:auto" method="POST">
                    @csrf
                    <div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1" id="nama_apb"></p>
                            <p class="m-0 fs-5 fw-medium mb-1" id="pembuat_apb"></p>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="kode">Kode</label>
                                <input class="form-control bg-transparent no-border p-0" id="kode" name="kode" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="value">Value</label>
                                <input class="form-control bg-transparent no-border p-0" id="value" name="value" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Keluar</button>
                        <button class="btn btn-primary" type="submit" hidden>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Detail Workshop -->
    <div class="fade modal" id="modalDetailWorkshop" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="modalDetailWorkshopLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title fw-bold" id="modalDetailWorkshopLabel">Workshop</h1>
                    <button class="btn-close" type="button" onclick="closeModalWorkshop()"></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0" style="overflow-y:auto" method="POST">
                    @csrf
                    <div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1" id="nama_apb"></p>
                            <p class="m-0 fs-5 fw-medium mb-1" id="pembuat_apb"></p>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="kode">Kode</label>
                                <input class="form-control bg-transparent no-border p-0" id="kode" name="kode" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="m-0 container-fluid fw-semibold ps-0 text-start" for="value">Value</label>
                                <input class="form-control bg-transparent no-border p-0" id="value" name="value" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Keluar</button>
                        <button class="btn btn-primary" type="submit" hidden>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Add -->
    <div class="fade modal" id="modalForAdd" data-bs-backdrop="static" aria-hidden="true" aria-labelledby="modalForAddLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fs-5 modal-title fw-bold" id="modalForAddLabel">Tambah Data APB Baru</h5>
                    <button class="btn-close" type="button" onclick="closeModalAdd()"></button>
                </div>
                <div class="w-100 modal-body row">
                    <form class="w-100 align-items-center flex-column gap-0" id="addDataForm" method="POST" action="{{ route('apb.post.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if ($page != 'Data APB EX Mutasi Saldo')
                            <div class="mb-3">
                                <label class="form-label" for="pilihan-proyek1">Nama ATB</label>
                                <select class="form-control" id="pilihan-proyek1" name="id_atb" required>
                                    @foreach ($atbWithQuantity as $atb)
                                        <option value="{{ $atb->id }}">{{ Carbon::parse($atb->tanggal)->translatedFormat('d F Y') }} - {{ $atb->masterData->supplier }} - {{ $atb->masterData->sparepart }} - {{ $atb->masterData->part_number }} ({{ $atb->saldo->current_quantity }} tersedia)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="pilihan-alat1">Alat</label>
                                <select class="form-control" id="pilihan-alat1" name="id_alat" required>
                                    @foreach ($alatList as $alat)
                                        <option value="{{ $alat->id }}">{{ $alat->jenis_alat }} - {{ $alat->tipe_alat }} ({{ $alat->kode_alat }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="tanggal">Tanggal</label>
                                <input class="form-control datetimepicker" id="tanggal" name="tanggal" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="quantity">Quantity</label>
                                <input class="form-control" id="quantity" name="quantity" type="number" required>
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label" for="pilihan-proyek1">Sparepart</label>
                                <select class="form-control" id="pilihan-proyek1" name="id_atb" required>
                                    @foreach ($atbWithQuantity as $atb)
                                        <option value="{{ $atb->id }}">{{ $atb->masterData->supplier }} - {{ $atb->masterData->sparepart }} - {{ $atb->masterData->part_number }} - {{ Carbon::parse($atb->tanggal)->translatedFormat('d F Y') }} ({{ $atb->saldo->current_quantity }} tersedia)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="tujuan-proyek1">Tujuan Proyek</label>
                                <select class="form-control" id="tujuan-proyek1" name="id_tujuan_proyek" required>
                                    @foreach ($allProyek as $x)
                                        {{-- @if ($x->id != $proyek->id) --}}
                                        <option value="{{ $x->id }}">{{ $x->nama_proyek }}</option>
                                        {{-- <option value="{{ $atb->id }}">{{ Carbon::parse($atb->tanggal)->translatedFormat('d F Y') }} - {{ $atb->masterData->supplier }} - {{ $atb->masterData->sparepart }} - {{ $atb->masterData->part_number }} ({{ $atb->saldo->current_quantity }} tersedia)</option> --}}
                                        {{-- @endif --}}
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="tanggal">Tanggal</label>
                                <input class="form-control datetimepicker" id="tanggal" name="tanggal" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="quantity">Quantity</label>
                                <input class="form-control" id="quantity" name="quantity" type="number" required>
                            </div>
                        @endif
                        <div class="m-0 p-0 d-flex modal-footer pt-4 w-100">
                            <button class="btn btn-success container-fluid m-0 p-0 py-2" type="submit">Tambah Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Edit -->
    <div class="fade modal" id="modalForEdit" data-bs-backdrop="static" aria-hidden="true" aria-labelledby="modalForEditLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fs-5 modal-title fw-bold" id="modalForEditLabel">Ubah Data APB</h5>
                    <button class="btn-close" type="button" onclick="closeModalEdit()"></button>
                </div>
                <div class="w-100 modal-body row">
                    <form class="w-100 align-items-center flex-column gap-0" id="editDataForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input class="form-control" id="id_atb" name="id_atb" value="" hidden required>

                        <div class="mb-3">
                            <label class="form-label" for="pilihan-proyek2">Nama ATB</label>
                            <input class="form-control" id="pilihan-proyek2-tampilan" name="pilihan-proyek2-tampilan" readonly required>
                            <input class="form-control" id="pilihan-proyek2" name="pilihan-proyek2" hidden required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="pilihan-alat2">Alat</label>
                            <select class="form-control" id="pilihan-alat2" name="id_alat" required>
                                @foreach ($alatList as $alat)
                                    <option value="{{ $alat->id }}">{{ $alat->jenis_alat }} - {{ $alat->tipe_alat }} ({{ $alat->kode_alat }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tanggal">Tanggal</label>
                            <input class="form-control datetimepicker" id="tanggal" name="tanggal" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="quantity">Quantity</label>
                            <input class="form-control" id="quantity" name="quantity" type="number" required>
                        </div>
                        {{-- <div class="mb-3">
<label class="form-label" for="dokumentasi">Upload Dokumentasi</label>
<input class="form-control" id="dokumentasi" name="dokumentasi" type="file" accept="image/jpeg,image/jpg,image/png,image/heic,image/heif">
</div> --}}
                        <div class="m-0 p-0 d-flex modal-footer pt-4 w-100">
                            <button class="btn btn-success container-fluid m-0 p-0 py-2" type="submit">Ubah Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Menampilkan Dokumentasi -->
    <div class="modal fade" id="modalDokumentasi" aria-labelledby="modalDokumentasiLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 pb-2" id="modalDokumentasiLabel">Dokumentasi</h5>
                    <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img class="img-fluid" id="dokumentasiImage" src="" alt="Dokumentasi">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js" crossorigin="anonymous" integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw==" referrerpolicy="no-referrer"></script>
    <script>
        $(".nav-tabs a").click(function() {
            $(this).tab('show');
        });

        function closeModalAlat() {
            $('#modalDetailAlat').modal('hide');
        }

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

        function showDokumentasi(url) {
            // Set gambar dokumentasi dengan URL yang diambil dari server
            document.getElementById('dokumentasiImage').src = url;
            // Tampilkan modal
            $('#modalDokumentasi').modal('show');
        }

        const validationSecond = (id, name) => {
            getAPB(id)
                .then(data => {
                    document.querySelector('#modalForDelete #apb_id').value = data.id;
                }).catch(error => {
                    showSweetAlert2(error, 'error');
                });
            document.querySelector('#model-konfirmasi').innerText = name;
            document.querySelector('#modalForDelete form').action = `/apb/ex_panjar_unit_alat/${id}`;
        };

        document.querySelector('#modalForDelete form').addEventListener('submit', function(event) {
            var confirmationText = document.getElementById('model-konfirmasi').innerText.trim();
            var inputName = document.querySelector('#modalForDelete #name');
            if (inputName.value.trim() !== confirmationText) {
                event.preventDefault();
                showSweetAlert2('Masukkan tidak sesuai. Silakan coba lagi!', 'error')
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

        function fillFormEdit(params, quantity, id_alat, id_apb) {
            document.querySelector('#modalForEdit form').action = `/apb/ex_panjar_unit_alat/edit/${params}`;
            getATB(params)
                .then(data => {
                    const apb = data.saldo.apb.find(apb => apb.id === id_apb);

                    if (!apb) {
                        showSweetAlert2('APB tidak ditemukan.', 'error');
                        return;
                    }

                    // Set data ke form modal
                    $('#pilihan-alat2').val(id_alat).trigger('change');
                    document.querySelector('#modalForEdit #id_atb').value = data.id;
                    document.querySelector('#modalForEdit #pilihan-proyek2').value = data.proyek.id;
                    document.querySelector('#modalForEdit #pilihan-proyek2-tampilan').value = data.proyek.nama_proyek + " - " + data.sparepart + " (" + data.saldo.current_quantity + " tersedia)";

                    // Set tanggal value sesuai dengan APB yang ditemukan
                    document.querySelector('#modalForEdit #tanggal').value = apb.tanggal; // Ambil tanggal dari APB yang sesuai

                    document.querySelector('#modalForEdit #quantity').value = apb.quantity; // Set quantity dari APB yang sesuai
                }).catch(error => {
                    showSweetAlert2(error, 'error');
                });
        }

        function getATB(params) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: "/apb/ex_panjar_unit_alat/" + params,
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

        function getAPB(params) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: "/apb/ex_panjar_unit_alat/apb/" + params,
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

        function getDetailAlat(params) {
            getAPB(params)
                .then(data => {
                    document.querySelector('#modalDetailAlat #nama_apb').innerText = "Nama Proyek: " + data.saldo.atb.proyek.nama_proyek;
                    document.querySelector('#modalDetailAlat #jenis_alat').value = data.alat.jenis_alat;
                    document.querySelector('#modalDetailAlat #kode_alat').value = data.alat.kode_alat;
                    document.querySelector('#modalDetailAlat #merek_alat').value = data.alat.merek_alat;
                    document.querySelector('#modalDetailAlat #tipe_alat').value = data.alat.tipe_alat;
                    $('#modalDetailAlat').modal('show');
                })
                .catch(error => {
                    $('#modalDetailAlat').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        function getDetailNilai(params) {
            getATB(params)
                .then(data => {
                    var formattedHarga = 'Rp' + data.harga.toLocaleString('id-ID');
                    var formattedNet = 'Rp' + data.net.toLocaleString('id-ID');
                    var formattedPpn = 'Rp' + data.ppn.toLocaleString('id-ID');
                    var formattedBruto = 'Rp' + data.bruto.toLocaleString('id-ID');

                    document.querySelector('#modalDetailNilai #nama_apb').innerText = "Nama Proyek: " + data.proyek.nama_proyek;
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

        function getDetailPerbaikan(params, quantity) {
            getATB(params)
                .then(data => {
                    // console.log(data, quantity);
                    var valuePerbaikan = 0
                    valuePerbaikan = data.harga * quantity;
                    var formattedValue = 'Rp' + valuePerbaikan.toLocaleString('id-ID');

                    document.querySelector('#modalDetailPerbaikan #nama_apb').innerText = "Nama Proyek: " + data.proyek.nama_proyek;
                    document.querySelector('#modalDetailPerbaikan #kode').value = data.komponen.kode;
                    document.querySelector('#modalDetailPerbaikan #value').value = formattedValue;
                    $('#modalDetailPerbaikan').modal('show');
                })
                .catch(error => {
                    $('#modalDetailPerbaikan').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        function getDetailPemeliharaan(params, quantity) {
            getATB(params)
                .then(data => {
                    // Hitung nilai pemeliharaan berdasarkan harga dan quantity
                    var valuePemeliharaan = data.harga * quantity;
                    var formattedValue = 'Rp' + valuePemeliharaan.toLocaleString('id-ID');

                    // Isi data ke dalam modal
                    document.querySelector('#modalDetailPemeliharaan #nama_apb').innerText = "Nama Proyek: " + data.proyek.nama_proyek;
                    document.querySelector('#modalDetailPemeliharaan #kode').value = data.komponen.kode;
                    document.querySelector('#modalDetailPemeliharaan #value').value = formattedValue;

                    // Tampilkan modal
                    $('#modalDetailPemeliharaan').modal('show');
                })
                .catch(error => {
                    $('#modalDetailPemeliharaan').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        function getDetailWorkshop(params, quantity) {
            getATB(params)
                .then(data => {
                    // Hitung nilai workshop berdasarkan harga dan quantity
                    var valueWorkshop = data.harga * quantity;
                    var formattedValue = 'Rp' + valueWorkshop.toLocaleString('id-ID');

                    // Isi data ke dalam modal
                    document.querySelector('#modalDetailWorkshop #nama_apb').innerText = "Nama Proyek: " + data.proyek.nama_proyek;
                    document.querySelector('#modalDetailWorkshop #kode').value = data.komponen.kode;
                    document.querySelector('#modalDetailWorkshop #value').value = formattedValue;

                    // Tampilkan modal
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

            $('#pilihan-proyek1').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#pilihan-alat1').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#pilihan-kode1').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#pilihan-satuan1').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#pilihan-alat2').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit')
            });

            $('#pilihan-kode2').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit')
            });

            $('#pilihan-satuan2').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit')
            });

            @if ($page == 'Data APB EX Mutasi Saldo')
                $('#tujuan-proyek1').select2({
                    placeholder: "Pilih opsi",
                    width: '100%',
                    dropdownParent: $('#modalForAdd')
                });
            @endif

            $('.datetimepicker').datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
                closeOnDateSelect: true,
            });

            function checkDatesAndFetchData() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();

                // Map page ke tipe ATB sesuai yang diinginkan
                var page = "{{ $page }}"; // Ambil nilai page dari blade
                var tipeATB = null;

                switch (page) {
                    case 'Data APB EX Unit Alat':
                        tipeATB = 'Hutang Unit Alat';
                        break;
                    case 'Data APB EX Panjar Unit Alat':
                        tipeATB = 'Panjar Unit Alat';
                        break;
                    case 'Data APB EX Mutasi Saldo':
                        tipeATB = 'Mutasi Proyek';
                        break;
                    case 'Data APB EX Panjar Proyek':
                        tipeATB = 'Panjar Proyek';
                        break;
                    default:
                        tipeATB = null;
                }

                if (startDate && endDate && tipeATB) {
                    $.ajax({
                        url: '{{ route('apb.fetchData') }}',
                        method: 'GET',
                        data: {
                            start_date: startDate,
                            end_date: endDate,
                            id_proyek: {{ $proyek->id }},
                            tipe: tipeATB, // Kirim tipe hasil mapping ke controller
                        },
                        success: function(response) {
                            console.log('Data fetched:', response);
                            var table = $('#table-data').DataTable();
                            table.clear();

                            let totalNilaiAPB = 0;
                            let totalNilaiPerbaikan = 0;
                            let totalNilaiPemeliharaan = 0;
                            let totalNilaiWorkshop = 0;

                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(atb) {
                                    atb.saldo.apb.forEach(function(apb) {
                                        if (apb.tanggal >= startDate && apb.tanggal <= endDate) {
                                            var alatKode = apb.alat && apb.alat.kode_alat ? apb.alat.kode_alat : '-';

                                            var nilai = atb.harga * apb.quantity;
                                            var row = [
                                                apb.tanggal ? moment(apb.tanggal).format('DD MMMM YYYY') : '',
                                                atb.komponen && atb.komponen.kode ? atb.komponen.kode : '',
                                                atb.master_data.supplier || '',
                                                atb.master_data.sparepart || '',
                                                atb.master_data.part_number || '',
                                                apb.quantity || '',
                                                atb.satuan || '',
                                                `<button class="btn text-primary" onclick="getDetailAlat('${apb.id}')">${alatKode}</button>`,
                                                `<button class="btn text-primary" onclick="getDetailNilai('${atb.id}')">Rp${nilai.toLocaleString('id-ID')}</button>`,
                                                atb.komponen && atb.komponen.first_group.name === 'PERBAIKAN' ?
                                                `<button class="btn text-primary" onclick="getDetailPerbaikan('${atb.id}', '${apb.quantity}')">Perbaikan</button>` :
                                                '<button class="btn text-primary border-0 text-secondary" disabled>Perbaikan</button>',
                                                atb.komponen && atb.komponen.first_group.name === 'PEMELIHARAAN' ?
                                                `<button class="btn text-primary" onclick="getDetailPemeliharaan('${atb.id}', '${apb.quantity}')">Pemeliharaan</button>` :
                                                '<button class="btn text-primary border-0 text-secondary" disabled>Pemeliharaan</button>',
                                                atb.komponen && atb.komponen.first_group.name === 'WAREHOUSE' ?
                                                `<button class="btn text-primary" onclick="getDetailWorkshop('${atb.id}', '${apb.quantity}')">Workshop</button>` :
                                                '<button class="btn text-primary border-0 text-secondary" disabled>Workshop</button>',
                                                `<div class="d-flex justify-content-center">
<button class="btn btn-danger me-2" onclick="validationSecond(${apb.id}, '${atb.proyek.nama_proyek}')" data-bs-target="#modalForDelete" data-bs-toggle="modal"><i class="bi bi-trash3"></i></button>
<a class="btn btn-warning" onclick="fillFormEdit(${atb.id}, ${apb.quantity}, ${apb.alat ? apb.alat.id : 'null'})" data-bs-target="#modalForEdit" data-bs-toggle="modal"><i class="bi bi-pencil-square"></i></a>
</div>`
                                            ];

                                            // Update totals
                                            totalNilaiAPB += nilai;
                                            if (atb.komponen && atb.komponen.first_group.name === 'PERBAIKAN') {
                                                totalNilaiPerbaikan += nilai;
                                            }
                                            if (atb.komponen && atb.komponen.first_group.name === 'PEMELIHARAAN') {
                                                totalNilaiPemeliharaan += nilai;
                                            }
                                            if (atb.komponen && atb.komponen.first_group.name === 'WAREHOUSE') {
                                                totalNilaiWorkshop += nilai;
                                            }

                                            table.row.add(row);
                                        }
                                    });
                                });
                            } else {
                                showSweetAlert2('Tidak ada data yang ditemukan dalam rentang tanggal yang dipilih', 'info');
                            }

                            // Update the totals in the footer
                            $('#table-data tfoot tr').find('td').eq(1).html('<strong>Rp' + totalNilaiAPB.toLocaleString('id-ID') + '</strong>');
                            $('#table-data tfoot tr').find('td').eq(2).html('<strong>Rp' + totalNilaiPerbaikan.toLocaleString('id-ID') + '</strong>');
                            $('#table-data tfoot tr').find('td').eq(3).html('<strong>Rp' + totalNilaiPemeliharaan.toLocaleString('id-ID') + '</strong>');
                            $('#table-data tfoot tr').find('td').eq(4).html('<strong>Rp' + totalNilaiWorkshop.toLocaleString('id-ID') + '</strong>');

                            table.draw();
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to fetch data:', error);
                            showSweetAlert2('Terjadi kesalahan saat mengambil data. Silakan coba lagi.', 'error');
                        }
                    });
                }
            }

            // Event listener for filter button
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
        </script>
        @foreach ($errors->all() as $error)
            <script>
                errInput += "<li>{{ $error }}</li>"
            </script>
        @endforeach
        <script>
            errInput += "</ul>", showSweetAlert2(errInput, "error")
        </script>
    @endif
@endsection
