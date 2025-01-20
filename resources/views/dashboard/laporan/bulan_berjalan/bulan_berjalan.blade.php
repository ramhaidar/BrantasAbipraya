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
                    {{-- <a class="btn btn-primary btn-sm" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                        <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                    </a> --}}

                    <p>Periode {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>

                    <button class="btn btn-primary btn-sm" id="toggleAllButton" type="button" onclick="toggleAll()">
                        <i class="fa fa-expand" id="toggleAllIcon"></i>
                        <span class="ms-2" id="toggleAllText">Expand All</span>
                    </button>
                </div>

                @include('dashboard.laporan.bulan_berjalan.partials.table')

            </div>
        </div>
    </div>

    <!-- Modal for Adding Data -->
    {{-- @include('dashboard.masterdata.alat.partials.modal-add') --}}

    <!-- Modal for Editing Data -->
    {{-- @include('dashboard.masterdata.alat.partials.modal-edit') --}}

    <!-- Modal for Delete Confirmation -->
    {{-- @include('dashboard.masterdata.alat.partials.modal-delete') --}}
@endsection

@push('scripts_2')
@endpush
