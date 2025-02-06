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
                    <div class="d-flex justify-content-end gap-2">
                        <!-- Button Placeholder -->
                        <a class="btn btn-success btn-sm btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#modalForFinalize">
                            <i class="fa fa-check"></i> <span class="ms-2">Finalisasi Data</span>
                        </a>
                    </div>
                </div>

                {{-- @include('dashboard.evaluasi.general.partials.table') --}}

                <div class="p-0 m-0 py-3">
                    @include('components.search-input', [
                        'route' => url()->current(),
                        'placeholder' => 'Search items...',
                    ])
                    @include('dashboard.evaluasi.general.partials.table', [
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
    @include('dashboard.evaluasi.general.partials.modal-add')

    <!-- Modal for Deleting Data -->
    @include('dashboard.evaluasi.general.partials.modal-delete')

    <!-- Modal for Editing Data -->
    @include('dashboard.evaluasi.general.partials.modal-edit')
@endsection

@push('scripts_2')
@endpush
