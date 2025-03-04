@extends('layouts.app')

@section('title', 'Riwayat SPB - ' . $TableData->first()->linkRkbSpbs[0]->rkb->proyek->nama)

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title d-flex align-items-center gap-3">
                        <a class="btn btn-outline-primary btn-sm btn-hide-text-mobile" href="{{ route('spb.detail.index', ['id' => $TableData->first()->linkRkbSpbs[0]->rkb->id]) }}">
                            <i class="fa fa-arrow-left pe-1"></i> <span class="ms-2">Kembali</span>
                        </a>
                        <h5 class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</h5>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ route('spb.detail.riwayat.export-pdf', ['id' => $TableData->first()->id]) }}">
                            <i class="fa-solid fa-file-pdf"></i> <span class="ms-2">Export PDF</span>
                        </a>
                        <a class="btn btn-success btn-sm btn-hide-text-mobile" href="{{ route('export.spb', ['id' => $TableData->first()->id]) }}">
                            <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export Excel</span>
                        </a>
                    </div>
                </div>

                {{-- @include('dashboard.spb.riwayat.partials.table') --}}

                <div class="p-0 m-0 py-3">
                    @include('dashboard.spb.riwayat.partials.table', [
                        'TableData' => $TableData,
                    ])
                    @if ($TableData->hasPages())
                        @include('components.pagination', [
                            'paginator' => $TableData,
                        ])
                    @endif
                </div>

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
    @include('components.form-submit-handler')
@endpush
