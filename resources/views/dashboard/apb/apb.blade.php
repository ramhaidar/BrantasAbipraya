@extends('layouts.app')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title ps-2">
                        <p class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</p>
                    </div>
                    <div class="d-flex justify-content-end">
                        @if ($tipe !== 'mutasi-proyek')
                            <a class="btn btn-primary btn-sm me-2" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data APB</span>
                            </a>
                        @else
                            <a class="btn btn-primary btn-sm me-2" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                <i class="fa fa-exchange"></i> <span class="ms-2">Mutasi Proyek</span>
                            </a>
                            <a class="btn btn-primary btn-sm" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                <i class="fa fa-wrench"></i> <span class="ms-2">Gunakan Sparepart</span>
                            </a>
                        @endif
                    </div>
                </div>

                @include('dashboard.apb.partials.table')

            </div>
        </div>
    </div>

    <!-- Modal for Adding Data -->
    @include('dashboard.apb.partials.modal-add')

    <!-- Modal for STT -->
    {{-- @include('dashboard.atb.partials.modal-stt') --}}

    <!-- Modal for Delete -->
    @include('dashboard.apb.partials.modal-delete')

    <!-- Modal for Dokumentasi is already included in table partial -->
@endsection

@push('scripts_2')
@endpush
