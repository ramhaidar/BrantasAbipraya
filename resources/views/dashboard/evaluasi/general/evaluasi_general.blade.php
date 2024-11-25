@extends('layouts.app')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title ps-2">
                        <p class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</p>
                    </div>
                </div>

                @include('dashboard.evaluasi.general.partials.table')

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
