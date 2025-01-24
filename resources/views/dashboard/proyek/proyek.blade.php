@extends('layouts.app')

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
                    <a class="btn btn-primary btn-sm" id="button-for-modal-add" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                        <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data</span>
                    </a>
                </div>

                <div class="p-0 m-0 py-3">
                    @include('components.search-input', [
                        'route' => url()->current(),
                        'placeholder' => 'Search items...',
                    ])
                    @include('dashboard.proyek.partials.table', [
                        'TableData' => $TableData,
                    ])
                    @include('components.pagination', [
                        'paginator' => $TableData->links()->paginator,
                    ])
                </div>

            </div>
        </div>
    </div>

    <!-- Modal for Adding Data -->
    @include('dashboard.proyek.partials.modal-add')

    <!-- Modal for Editing Data -->
    @include('dashboard.proyek.partials.modal-edit')

    <!-- Modal for Delete Confirmation -->
    @include('dashboard.proyek.partials.modal-delete')

    <!-- Modal for Detail Data -->
    @include('dashboard.proyek.partials.modal-detail')
@endsection

@push('scripts_2')
    <script></script>
@endpush
