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
                        <a class="btn btn-warning btn-sm me-2" href="{{ route('export.atb', ['id' => $proyek->id]) }}">
                            <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export</span>
                        </a>
                        @if ($tipe !== 'mutasi-proyek')
                            <a class="btn btn-primary btn-sm" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data ATB</span>
                            </a>
                        @endif
                    </div>
                </div>

                @if ($tipe === 'hutang-unit-alat')
                    @include('dashboard.atb.partials.table')
                @elseif ($tipe === 'mutasi-proyek')
                    @include('dashboard.atb.partials.mutasi-proyek.table')
                @elseif ($tipe === 'panjar-unit-alat' || $tipe === 'panjar-proyek')
                    @include('dashboard.atb.partials.panjar-unit-alat_panjar_proyek.table')
                @endif

            </div>
        </div>
    </div>

    @if ($tipe === 'hutang-unit-alat')
        <!-- Modal for Adding Data -->
        @include('dashboard.atb.partials.hutang-unit-alat.modal-add')
    @elseif ($tipe === 'panjar-unit-alat' || $tipe === 'panjar-proyek')
        <!-- Modal for Adding Data -->
        @include('dashboard.atb.partials.panjar-unit-alat_panjar_proyek.modal-add')
    @endif

    @if ($tipe === 'hutang-unit-alat')
        <!-- Modal for STT -->
        @include('dashboard.atb.partials.modal-stt')
    @endif

    @include('dashboard.atb.partials.modal-delete')

    @if ($tipe === 'mutasi-proyek')
        <!-- Modal for Accept -->
        @include('dashboard.atb.partials.mutasi-proyek.modal-accept')

        <!-- Modal for Reject -->
        @include('dashboard.atb.partials.mutasi-proyek.modal-reject')
    @endif
@endsection

@push('scripts_2')
@endpush
