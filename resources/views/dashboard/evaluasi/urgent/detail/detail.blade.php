@extends('layouts.app')

@push('styles_2')
@endpush

@if (isset($rkb))
    @section('content')
        <div class="h-100">
            <div class="fade-in-up page-content">
                <div class="ibox">
                    <div class="ibox-head pe-0 ps-0">
                        <div class="ibox-title ps-2">
                            <p class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }} - {{ $rkb->nomor }}</p>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <!-- Tombol Evaluasi -->
                            <button class="btn btn-success btn-sm" id="evaluateBtnButton" data-bs-toggle="modal" data-bs-target="#modalForEvaluate" data-action="{{ route('evaluasi_rkb_urgent.detail.evaluate', $rkb->id) }}" data-message="Apakah Anda yakin ingin menyimpan hasil Evaluasi RKB ini?" {{ $rkb->is_evaluated ? 'disabled' : '' }}>
                                <i class="fa fa-check"></i> <span class="ms-2">Simpan Evaluasi RKB</span>
                            </button>

                            <!-- Tombol Approve -->
                            <button class="btn btn-primary btn-sm" id="approveBtnButton" data-bs-toggle="modal" data-bs-target="#modalForApprove" data-action="{{ route('evaluasi_rkb_urgent.detail.approve', $rkb->id) }}" data-message="Apakah Anda yakin ingin Approve RKB ini?" {{ !$rkb->is_evaluated ? 'disabled' : '' }} {{ $rkb->is_approved ? 'disabled' : '' }}>
                                <i class="fa fa-check"></i> <span class="ms-2">Approve RKB</span>
                            </button>
                        </div>

                    </div>

                    @include('dashboard.evaluasi.urgent.detail.partials.table')

                </div>
            </div>
        </div>

        <!-- Modal for Adding Data -->
        @include('dashboard.evaluasi.urgent.detail.partials.modal-add')

        <!-- Modal for Deleting Data -->
        @include('dashboard.evaluasi.urgent.detail.partials.modal-delete')

        <!-- Modal for Editing Data -->
        @include('dashboard.evaluasi.urgent.detail.partials.modal-edit')

        <!-- Modal for Finalization Data -->
        @include('dashboard.evaluasi.urgent.detail.partials.modal-approve')

        <!-- Modal for Evaluation Data -->
        @include('dashboard.evaluasi.urgent.detail.partials.modal-evaluate')
    @endsection

    @push('scripts_2')
    @endpush
@endif
