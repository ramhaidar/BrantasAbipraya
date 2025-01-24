@extends('layouts.app')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title ps-2">
                        <h5 class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</h5>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <a class="btn btn-warning btn-sm me-2" href="{{ route('export.apb', ['id' => $proyek->id]) }}">
                            <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export</span>
                        </a>
                        @if ($tipe === 'mutasi-proyek')
                            <a class="btn btn-success btn-sm me-2" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForMutasi">
                                <i class="fa fa-exchange"></i> <span class="ms-2">Mutasi Proyek</span>
                            </a>
                            <a class="btn btn-primary btn-sm" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                <i class="fa fa-wrench"></i> <span class="ms-2">Gunakan Sparepart</span>
                            </a>
                        @else
                            <a class="btn btn-primary btn-sm me-2" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data APB</span>
                            </a>
                        @endif
                    </div>
                </div>

                @if ($tipe === 'mutasi-proyek')
                    @include('dashboard.apb.partials.mutasi-proyek.table')
                @else
                    @include('dashboard.apb.partials.table')
                @endif

            </div>
        </div>
    </div>

    @if ($tipe === 'mutasi-proyek')
        <!-- Modal for Adding Data -->
        @include('dashboard.apb.partials.mutasi-proyek.modal-add')

        <!-- Modal for Mutasi Proyek -->
        @include('dashboard.apb.partials.mutasi-proyek.modal-mutasi')
    @else
        <!-- Modal for Adding Data -->
        @include('dashboard.apb.partials.modal-add')
    @endif

    @if ($tipe === 'mutasi-proyek')
        <!-- Modal for Delete -->
        @include('dashboard.apb.partials.mutasi-proyek.modal-delete')
    @else
        <!-- Modal for Delete -->
        @include('dashboard.apb.partials.modal-delete')
    @endif
@endsection

@push('scripts_2')
@endpush
