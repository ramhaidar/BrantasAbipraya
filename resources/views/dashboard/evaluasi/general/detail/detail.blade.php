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
                            <p class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</p>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <!-- Button to trigger the modal -->
                            <button class="btn btn-success btn-sm approveBtn" id="approveRkbButton" data-bs-toggle="modal" data-bs-target="#modalForApprove" {{ $rkb->is_approved ? 'disabled' : '' }}>
                                <i class="fa fa-check"></i> <span class="ms-2">Approve RKB</span>
                            </button>
                        </div>
                    </div>

                    @include('dashboard.evaluasi.general.detail.partials.table')

                </div>
            </div>
        </div>

        <!-- Modal for Adding Data -->
        @include('dashboard.evaluasi.general.detail.partials.modal-add')

        <!-- Modal for Deleting Data -->
        @include('dashboard.evaluasi.general.detail.partials.modal-delete')

        <!-- Modal for Editing Data -->
        @include('dashboard.evaluasi.general.detail.partials.modal-edit')

        <!-- Modal for Finalization Data -->
        @include('dashboard.evaluasi.general.detail.partials.modal-approve')
    @endsection

    @push('scripts_2')
    @endpush
@endif
