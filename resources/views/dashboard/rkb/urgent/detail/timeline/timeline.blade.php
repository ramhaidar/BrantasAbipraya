@extends('layouts.app')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title ps-2 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('rkb_urgent.detail.index', ['id' => $data->rkb->id]) }}">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <h5 class="mb-0">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }} - {{ $data->rkb->nomor }} - {{ $data->masterDataAlat->jenis_alat }} : {{ $data->masterDataAlat->kode_alat }}</h5>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <!-- Button to trigger the modal -->
                        {{-- <a class="btn btn-success btn-sm finalizeBtn {{ $rkb->is_finalized ? 'disabled' : '' }}" id="button-for-modal-add" data-bs-toggle="modal" data-id="{{ $rkb->id }}" data-bs-target="#modalForFinalize">
                            <i class="fa fa-check"></i> <span class="ms-2">Finalisasi Data</span>
                        </a> --}}
                        <a class="btn btn-primary btn-sm {{ $rkb->is_finalized ? 'disabled' : '' }}" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                            <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                        </a>
                    </div>
                </div>

                @include('dashboard.rkb.urgent.detail.timeline.partials.table')

            </div>
        </div>
    </div>

    <!-- Modal for Adding Data -->
    @include('dashboard.rkb.urgent.detail.timeline.partials.modal-add')

    <!-- Modal for Deleting Data -->
    @include('dashboard.rkb.urgent.detail.timeline.partials.modal-delete')

    <!-- Modal for Editing Data -->
    @include('dashboard.rkb.urgent.detail.timeline.partials.modal-edit')

    <!-- Modal for Finalization Data -->
    {{-- @include('dashboard.rkb.urgent.detail.partials.modal-finalization') --}}
@endsection

@push('scripts_2')
@endpush
