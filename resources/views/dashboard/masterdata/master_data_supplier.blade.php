@extends('component.sidebar')
@section('css')
    <link href=/css/random-css-datatable.css rel=stylesheet>
    <style>
        .alert-custom-css {
            max-width: 400px;
            width: 90%
        }

        #form-group {
            width: 90%
        }

        .space-nowrap {
            white-space: nowrap
        }

        .center {
            text-align: center !important
        }

        .custom-confirm-delete {
            margin-right: 5%
        }

        .custom-cancel-delete {
            margin-left: 5%
        }

        .custom-action-delete {
            width: 100% !important;
            justify-content: space-between
        }

        @media screen and (max-width:500px) {
            #button-for-modal-add span {
                display: none
            }

            #button-for-modal-add {
                font-size: 20px
            }
        }
    </style>
@endsection

@section('content')
    <div class="fade-in-up page-content">
        <div class="ibox">
            {{-- Cek apakah user role adalah Pegawai --}}
            @if (Auth::user()->role == 'Pegawai')
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</div>
                    <a class="btn btn-primary btn-sm" id="button-for-modal-add" onclick="showModalAdd()">
                        <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                    </a>
                </div>
            @endif
            <div class="ibox-body mt-0 table-responsive">
                <table class="border-dark m-0 table table-bordered table-striped" id="table-data" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th>Supplier</th>
                            <th>Sparepart</th>
                            <th>Part Number</th>
                            <th>Buffer Stock</th>
                            @if (Auth::user()->role == 'Pegawai')
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($masterData as $data)
                            <tr>
                                <td>{{ $data->supplier }}</td>
                                <td>{{ $data->sparepart }}</td>
                                <td>{{ $data->part_number }}</td>
                                <td>{{ $data->buffer_stock }}</td>
                                @if (Auth::user()->role == 'Pegawai')
                                    <td class="center space-nowrap">
                                        <button class="btn btn-danger deleteBtn" data-id="{{ $data->id }}"><i class="bi bi-trash"></i></button>
                                        <button class="btn btn-warning ms-3 ubahBtn" data-id="{{ $data->id }}" onclick='fillFormEdit("{{ $data->id }}")'><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal untuk Tambah Data -->
    <div class="fade modal" id="modalForAdd" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title" id="modalForAddLabel">Tambah Data Master</h1>
                    <button class="btn-close" type="button" onclick="closeModalAdd()"></button>
                </div>
                <form class="d-flex w-100 align-items-center flex-column gap-3" style="overflow-y:auto" method="POST" action="{{ route('master_data.store') }}">
                    @csrf
                    <div class="d-flex w-100 align-items-center flex-column modal-body">
                        <div id="form-group">
                            <label class="form-label mb-0" for="supplier">Supplier</label>
                            <input class="form-control" id="supplier" name="supplier" type="text" placeholder="Supplier" required>
                        </div>
                    </div>
                    <div class="d-flex w-100 justify-content-between modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                        <button class="btn btn-primary" id="add-data" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal untuk Edit Data -->
    <div class="fade modal" id="modalForEdit" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true" aria-labelledby="staticBackdropLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="fs-5 modal-title" id="modalForEditLabel">Edit Buyer Data Master</h1>
                    <button class="btn-close" type="button" onclick="closeModalEdit()"></button>
                </div>
                <form class="d-flex w-100 align-items-center flex-column gap-3" style="overflow-y:auto" method="POST" action="{{ route('master_data.update') }}">
                    @csrf
                    @method('PUT') <!-- Tambahkan PUT method untuk update -->
                    <div class="d-flex w-100 align-items-center flex-column modal-body">
                        <div id="form-group">
                            <label class="form-label mb-0" for="supplier">Supplier</label>
                            <input class="form-control" id="supplier" name="supplier" type="text" placeholder="Supplier" required>
                        </div>
                    </div>
                    <div class="d-flex w-100 justify-content-between modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                        <button class="btn btn-primary" id="update-data" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
