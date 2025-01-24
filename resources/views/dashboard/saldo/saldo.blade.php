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
                        <a class="btn btn-warning btn-sm me-2" href="{{ route('export.saldo', ['id' => $proyek->id]) }}">
                            <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export</span>
                        </a>
                    </div>
                </div>

                @include('dashboard.saldo.partials.table')

            </div>
        </div>
    </div>

    <!-- Modal for Adding Data -->
    {{-- @include('dashboard.atb.partials.modal-add') --}}

    <!-- Modal for STT -->
    {{-- @include('dashboard.atb.partials.modal-stt') --}}

    <!-- Modal for Delete -->
    {{-- @include('dashboard.atb.partials.modal-delete') --}}

    <!-- Modal for Dokumentasi is already included in table partial -->
@endsection

@push('scripts_2')
@endpush
