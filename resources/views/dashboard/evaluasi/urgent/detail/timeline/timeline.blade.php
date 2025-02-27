@extends('layouts.app')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title d-flex align-items-center gap-3">
                        <a class="btn btn-outline-primary btn-sm btn-hide-text-mobile" href="{{ route('evaluasi_rkb_urgent.detail.index', ['id' => $data->rkb->id]) }}">
                            <i class="fa fa-arrow-left pe-1"></i> <span class="ms-2">Kembali</span>
                        </a>
                        <h5 class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</h5>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <!-- Button Placeholder -->
                        <a class="btn btn-warning btn-sm btn-hide-text-mobile" href="{{ route('export.evaluasi_timeline_rkb_urgent', ['id' => $data->id]) }}">
                            <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export</span>
                        </a>
                    </div>
                </div>

                {{-- @include('dashboard.evaluasi.urgent.detail.timeline.partials.table') --}}

                <div class="p-0 m-0 py-3">
                    @include('components.search-input', [
                        'route' => url()->current(),
                        'placeholder' => 'Search items...',
                        'show_all' => true,
                    ])
                    @include('dashboard.evaluasi.urgent.detail.timeline.partials.table', [
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
@endsection

@push('scripts_2')
    @include('components.form-submit-handler')
@endpush
