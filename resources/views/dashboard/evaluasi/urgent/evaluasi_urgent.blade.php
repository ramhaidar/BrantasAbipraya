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
                </div>

                {{-- @include('dashboard.evaluasi.urgent.partials.table') --}}

                <div class="p-0 m-0 py-3">
                    @include('components.search-input', [
                        'route' => url()->current(),
                        'placeholder' => 'Search items...',
                    ])
                    @include('dashboard.evaluasi.urgent.partials.table', [
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
    @include('dashboard.evaluasi.urgent.partials.modal-add')

    <!-- Modal for Deleting Data -->
    @include('dashboard.evaluasi.urgent.partials.modal-delete')

    <!-- Modal for Editing Data -->
    @include('dashboard.evaluasi.urgent.partials.modal-edit')
@endsection

@push('scripts_2')
@endpush
