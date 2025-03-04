@extends('layouts.app')

@section('title')
    @php
        function formatTipeTitle($tipe)
        {
            // Split by hyphens and capitalize each word
            $words = explode('-', $tipe);
            $formattedWords = array_map(function ($word) {
                return ucfirst($word);
            }, $words);
            // Join with spaces
            return implode(' ', $formattedWords);
        }
    @endphp
    {{ $proyek->nama . ' - Saldo - ' . formatTipeTitle($tipe) }}
@endsection

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
                        <a class="btn btn-warning btn-sm btn-hide-text-mobile" href="{{ route('export.saldo', ['id' => $proyek->id, 'type' => $tipe]) }}">
                            <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export</span>
                        </a>
                    </div>
                </div>

                {{-- @include('dashboard.saldo.partials.table') --}}

                <div class="p-0 m-0 py-3">
                    @include('components.search-input', [
                        'route' => url()->current(),
                        'placeholder' => 'Search items...',
                    ])
                    @include('dashboard.saldo.partials.table', [
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
