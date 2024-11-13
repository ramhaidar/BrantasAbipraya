@extends('layouts.app')

@push('styles_2')
@endpush

@section('content')
    <link href="{{ asset('css/dashboard_content.css') }}" rel="stylesheet">

    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox" style="border: 1px solid grey; box-shadow: 0px 0px 0px black;">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title ps-2">
                        <p class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</p>
                    </div>
                    <a class="btn btn-primary btn-sm" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                        <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                    </a>
                </div>

                @include('dashboard.rkb.urgent.partials.table')

            </div>
        </div>
    </div>
@endsection

@push('scripts_2')
@endpush
