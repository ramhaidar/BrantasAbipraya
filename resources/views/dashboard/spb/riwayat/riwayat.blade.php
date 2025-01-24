@extends('layouts.app')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title d-flex align-items-center gap-3">
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('spb.detail.index', ['id' => $spb->linkRkbSpbs[0]->rkb->id]) }}">
                            <i class="fa fa-arrow-left pe-1"></i> Kembali
                        </a>
                        <h5 class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</h5>
                    </div>

                    <div class="ms-auto d-flex gap-2">
                        <a class="btn btn-warning btn-sm me-2" href="{{ route('export.spb', ['id' => $spb->id]) }}">
                            <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export</span>
                        </a>
                    </div>
                    {{-- <div class="ms-auto d-flex gap-2">
                        <p class="text-end fw-medium">{{ $spb->nomor }}</p>
                    </div> --}}
                </div>

                @include('dashboard.spb.riwayat.partials.table')

            </div>
        </div>
    </div>

    <!-- Modal for Adding Data -->
    @include('dashboard.spb.riwayat.partials.modal-preview')

    <!-- Modal for Deleting Data -->
    {{-- @include('dashboard.rkb.urgent.partials.modal-delete') --}}

    <!-- Modal for Editing Data -->
    {{-- @include('dashboard.rkb.urgent.partials.modal-edit') --}}
@endsection

@push('scripts_2')
@endpush
