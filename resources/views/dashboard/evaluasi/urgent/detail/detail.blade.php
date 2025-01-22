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
                            @if (!$rkb->is_approved_vp && !$rkb->is_approved_svp)
                                @if ($rkb->is_evaluated)
                                    <button class="btn btn-danger btn-sm" id="evaluateBtnButton" 
                                            data-bs-toggle="modal" data-bs-target="#modalForEvaluate"
                                            data-action="{{ route('evaluasi_rkb_urgent.detail.evaluate', $rkb->id) }}"
                                            data-message="Apakah Anda yakin ingin membatalkan hasil Evaluasi RKB ini?">
                                        <i class="fa fa-times"></i> <span class="ms-2">Batalkan Evaluasi RKB</span>
                                    </button>
                                @else
                                    <button class="btn btn-success btn-sm" id="evaluateBtnButton"
                                            data-bs-toggle="modal" data-bs-target="#modalForEvaluate"
                                            data-action="{{ route('evaluasi_rkb_urgent.detail.evaluate', $rkb->id) }}"
                                            data-message="Apakah Anda yakin ingin menyimpan hasil Evaluasi RKB ini?">
                                        <i class="fa fa-check"></i> <span class="ms-2">Simpan Evaluasi RKB</span>
                                    </button>
                                @endif
                            @endif

                            <!-- Tombol Approve VP -->
                            <button class="btn btn-primary btn-sm" id="approveVpButton"
                                    data-bs-toggle="modal" data-bs-target="#modalForApproveVp"
                                    data-action="{{ route('evaluasi_rkb_urgent.detail.approve.vp', $rkb->id) }}"
                                    data-message="Apakah Anda yakin ingin Approve RKB ini sebagai VP?"
                                    {{ !$rkb->is_evaluated ? 'disabled' : '' }}
                                    {{ $rkb->is_approved_vp ? 'disabled' : '' }}>
                                <i class="fa fa-check"></i> <span class="ms-2">Approve RKB (VP)</span>
                            </button>

                            <!-- Tombol Approve SVP -->
                            <button class="btn btn-primary btn-sm" id="approveSvpButton"
                                    data-bs-toggle="modal" data-bs-target="#modalForApproveSvp"
                                    data-action="{{ route('evaluasi_rkb_urgent.detail.approve.svp', $rkb->id) }}"
                                    data-message="Apakah Anda yakin ingin Approve RKB ini sebagai SVP?"
                                    {{ !$rkb->is_approved_vp ? 'disabled' : '' }}
                                    {{ $rkb->is_approved_svp ? 'disabled' : '' }}>
                                <i class="fa fa-check"></i> <span class="ms-2">Approve RKB (SVP)</span>
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

        <!-- Modal for Evaluation Data -->
        @include('dashboard.evaluasi.urgent.detail.partials.modal-evaluate')

        <!-- Modal for VP Approval -->
        @include('dashboard.evaluasi.general.detail.partials.modal-approve-vp')

        <!-- Modal for SVP Approval -->
        @include('dashboard.evaluasi.general.detail.partials.modal-approve-svp')
    @endsection

    @push('scripts_2')
    @endpush
@endif
