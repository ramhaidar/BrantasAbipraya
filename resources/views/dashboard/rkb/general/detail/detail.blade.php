@extends('layouts.app')

@section('title', 'Detail RKB General - ' . $rkb->proyek->nama)

@push('styles_2')
@endpush

@if (isset($rkb))
    @section('content')
        <div class="h-100">
            <div class="fade-in-up page-content">
                <div class="ibox">
                    <div class="ibox-head pe-0 ps-0">
                        <div class="ibox-title d-flex align-items-center gap-3">
                            <a class="btn btn-outline-primary btn-sm btn-hide-text-mobile" href="{{ route('rkb_general.index') }}">
                                <i class="fa fa-arrow-left pe-1"></i> <span class="ms-2">Kembali</span>
                            </a>
                            <h5 class="fw-medium mb-0">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</h5>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a class="btn btn-warning btn-sm btn-hide-text-mobile {{ $rkb->is_finalized ? '' : 'disabled' }}" href="{{ route('export.detail_rkb_general', ['id' => $rkb->id]) }}">
                                <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export</span>
                            </a>
                            @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                                <a class="btn btn-success btn-sm btn-hide-text-mobile finalizeBtn {{ $rkb->is_finalized ? 'disabled' : '' }}" data-bs-toggle="modal" data-id="{{ $rkb->id }}" data-bs-target="#modalForFinalize">
                                    <i class="fa fa-check"></i> <span class="ms-2">Finalisasi Data</span>
                                </a>
                                <a class="btn btn-primary btn-sm btn-hide-text-mobile {{ $rkb->is_finalized ? 'disabled' : '' }}" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                    <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="p-0 m-0 py-3">
                        @include('components.search-input', [
                            'route' => url()->current(),
                            'placeholder' => 'Search items...',
                            'show_all' => true,
                        ])
                        @include('dashboard.rkb.general.detail.partials.table', [
                            'TableData' => $TableData,
                        ])
                        @if ($TableData->hasPages())
                            @include('components.pagination', [
                                'paginator' => $TableData,
                            ])
                        @endif
                    </div>

                    {{-- @include('dashboard.rkb.general.detail.partials.table') --}}

                </div>
            </div>
        </div>

        @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
            <!-- Modal for Adding Data -->
            @include('dashboard.rkb.general.detail.partials.modal-add')

            <!-- Modal for Deleting Data -->
            @include('dashboard.rkb.general.detail.partials.modal-delete')

            <!-- Modal for Editing Data -->
            @include('dashboard.rkb.general.detail.partials.modal-edit')

            <!-- Modal for Finalization Data -->
            @include('dashboard.rkb.general.detail.partials.modal-finalization')
        @endif
    @endsection

    @push('scripts_2')
        @include('components.form-submit-handler')
    @endpush
@endif
