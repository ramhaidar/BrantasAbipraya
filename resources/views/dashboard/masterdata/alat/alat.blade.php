@extends('layouts.app')

@push('styles_2')
    @stack('styles_3')
@endpush

@section('content')
    <link href="{{ asset('css/dashboard_content.css') }}" rel="stylesheet">

    <div class="h-100">
        <div class="fade-in-up page-content" style="max-height: 50%">
            <div class="ibox" style="border: 1px solid grey; box-shadow: 0px 0px 0px black;">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title ps-2">
                        <p class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</p>
                    </div>
                    <a class="btn btn-primary btn-sm" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                        <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                    </a>
                </div>

                @include('dashboard.masterdata.alat.partials.table')

            </div>
        </div>
    </div>

    <!-- Modal for Adding Data -->
    @include('dashboard.masterdata.alat.partials.modal-add')

    <!-- Modal for Editing Data -->
    @include('dashboard.masterdata.alat.partials.modal-edit')

    <!-- Modal for Delete Confirmation -->
    @include('dashboard.masterdata.alat.partials.modal-delete')
@endsection

@push('scripts_2')
    @stack('scripts_3')
@endpush
