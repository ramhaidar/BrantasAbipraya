@extends('layouts.app')

@section('title', 'Master Data Supplier')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content" style="max-height: 50%">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title ps-2">
                        <h5 class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</h5>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
                            <a class="btn btn-primary btn-sm btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="p-0 m-0 py-3">
                    @include('components.search-input', [
                        'route' => url()->current(),
                        'placeholder' => 'Search items...',
                    ])
                    @include('dashboard.masterdata.supplier.partials.table', [
                        'TableData' => $TableData,
                    ])
                    @if ($TableData->hasPages())
                        @include('components.pagination', [
                            'paginator' => $TableData,
                        ])
                    @endif
                </div>

                {{-- @include('dashboard.masterdata.supplier.partials.table') --}}

            </div>
        </div>
    </div>

    @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
        <!-- Modal for Adding Data -->
        @include('dashboard.masterdata.supplier.partials.modal-add')

        <!-- Modal for Editing Data -->
        @include('dashboard.masterdata.supplier.partials.modal-edit')

        <!-- Modal for Delete Confirmation -->
        @include('dashboard.masterdata.supplier.partials.modal-delete')
    @endif

    <!-- Modal for Detail Data -->
    @include('dashboard.masterdata.supplier.partials.modal-detail')
@endsection

@push('scripts_2')
    @include('components.form-submit-handler')
@endpush
