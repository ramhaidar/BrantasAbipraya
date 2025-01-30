@extends('layouts.app')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title d-flex align-items-center gap-3">
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('evaluasi_rkb_urgent.detail.index', ['id' => $data->rkb->id]) }}">
                            <i class="fa fa-arrow-left pe-1"></i> Kembali
                        </a>
                        <h5 class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</h5>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <!-- Button to trigger the modal -->
                        {{-- <a class="btn btn-success btn-sm finalizeBtn {{ $rkb->is_finalized ? 'disabled' : '' }}" id="button-for-modal-add" data-bs-toggle="modal" data-id="{{ $rkb->id }}" data-bs-target="#modalForFinalize">
                            <i class="fa fa-check"></i> <span class="ms-2">Finalisasi Data</span>
                        </a> --}}
                        {{-- <a class="btn btn-primary btn-sm {{ $rkb->is_finalized ? 'disabled' : '' }}" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                            <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                        </a> --}}
                    </div>
                </div>

                {{-- @include('dashboard.evaluasi.urgent.detail.timeline.partials.table') --}}

                <div class="p-0 m-0 py-3">
                    @include('components.search-input', [
                        'route' => url()->current(),
                        'placeholder' => 'Search items...',
                        'show_all' => true,
                    ])
                    @include('dashboard.evaluasi.urgent.detail.timeline.partials.table', [
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
    @include('dashboard.evaluasi.urgent.detail.timeline.partials.modal-add')

    <!-- Modal for Deleting Data -->
    @include('dashboard.evaluasi.urgent.detail.timeline.partials.modal-delete')

    <!-- Modal for Editing Data -->
    @include('dashboard.evaluasi.urgent.detail.timeline.partials.modal-edit')

    <!-- Modal for Finalization Data -->
    {{-- @include('dashboard.evaluasi.urgent.detail.partials.modal-finalization') --}}
@endsection

@push('scripts_2')
@endpush
