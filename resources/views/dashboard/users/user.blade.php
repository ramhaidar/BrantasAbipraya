@extends('layouts.app')

@section('title', 'Users')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class=ibox-title>
                        <h5 class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</h5>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        @if (Auth::user()->role === 'superadmin')
                            <a class="btn btn-primary btn-sm btn-hide-text-mobile" id=btn-hide-text-mobile data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                <i class="fa fa-plus"></i> <span class=ms-2>Tambah Data</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="p-0 m-0 py-3">
                    @include('components.search-input', [
                        'route' => url()->current(),
                        'placeholder' => 'Search items...',
                    ])
                    @include('dashboard.users.partials.table', [
                        'TableData' => $TableData,
                    ])
                    @if ($TableData->hasPages())
                        @include('components.pagination', [
                            'paginator' => $TableData,
                        ])
                    @endif
                </div>

                {{-- @include('dashboard.users.partials.table') --}}

            </div>
        </div>
    </div>

    <!-- Modal for Edit Data -->
    @include('dashboard.users.partials.modal-edit')

    <!-- Modal for Add Data -->
    @include('dashboard.users.partials.modal-add')

    <!-- Modal for Delete Data -->
    @include('dashboard.users.partials.modal-delete')
@endsection

@push('scripts_2')
    @include('components.form-submit-handler')
@endpush
