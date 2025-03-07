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
    {{ $proyek->nama . ' - ATB - ' . formatTipeTitle($tipe) }}
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
                        <a class="btn btn-warning btn-sm btn-hide-text-mobile" href="{{ route('export.atb', ['id' => $proyek->id, 'type' => $tipe]) }}">
                            <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export</span>
                        </a>
                        @if ($tipe !== 'mutasi-proyek')
                            @if (in_array(Auth::user()->role, ['koordinator_proyek', 'superadmin']))
                                <a class="btn btn-primary btn-sm btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                    <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data ATB</span>
                                </a>
                            @endif
                            @if (in_array(Auth::user()->role, env('IS_BETA') ? ['admin_divisi', 'vp', 'svp', 'superadmin', 'koordinator_proyek'] : ['admin_divisi', 'vp', 'svp', 'superadmin']) && $tipe === 'hutang-unit-alat')
                                <a class="btn btn-primary btn-sm btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#modalForAddBypass">
                                    <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data ATB [Bypass]</span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

                @if ($tipe === 'hutang-unit-alat')
                    {{-- @include('dashboard.atb.partials.table') --}}
                    <div class="p-0 m-0 py-3">
                        @include('components.search-input', [
                            'route' => url()->current(),
                            'placeholder' => 'Search items...',
                        ])
                        @include('dashboard.atb.partials.table', [
                            'TableData' => $TableData,
                        ])
                        @if ($TableData->hasPages())
                            @include('components.pagination', [
                                'paginator' => $TableData,
                            ])
                        @endif
                    </div>
                @elseif ($tipe === 'mutasi-proyek')
                    {{-- @include('dashboard.atb.partials.mutasi-proyek.table') --}}
                    <div class="p-0 m-0 py-3">
                        @include('components.search-input', [
                            'route' => url()->current(),
                            'placeholder' => 'Search items...',
                        ])
                        @include('dashboard.atb.partials.mutasi-proyek.table', [
                            'TableData' => $TableData,
                        ])
                        @if ($TableData->hasPages())
                            @include('components.pagination', [
                                'paginator' => $TableData,
                            ])
                        @endif
                    </div>
                @elseif ($tipe === 'panjar-unit-alat' || $tipe === 'panjar-proyek')
                    {{-- @include('dashboard.atb.partials.panjar-unit-alat_panjar_proyek.table') --}}
                    <div class="p-0 m-0 py-3">
                        @include('components.search-input', [
                            'route' => url()->current(),
                            'placeholder' => 'Search items...',
                        ])
                        @include('dashboard.atb.partials.panjar-unit-alat_panjar_proyek.table', [
                            'TableData' => $TableData,
                        ])
                        @if ($TableData->hasPages())
                            @include('components.pagination', [
                                'paginator' => $TableData,
                            ])
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>

    @include('dashboard.atb.partials.modal-dokumentasi')

    @if (in_array(Auth::user()->role, ['koordinator_proyek', 'superadmin']))
        @if ($tipe === 'hutang-unit-alat')
            <!-- Modal for Adding Data -->
            @include('dashboard.atb.partials.hutang-unit-alat.modal-add')
        @elseif ($tipe === 'panjar-unit-alat' || $tipe === 'panjar-proyek')
            <!-- Modal for Adding Data -->
            @include('dashboard.atb.partials.panjar-unit-alat_panjar_proyek.modal-add')
        @endif
    @endif
    @if (in_array(Auth::user()->role, env('IS_BETA') ? ['admin_divisi', 'vp', 'svp', 'superadmin', 'koordinator_proyek'] : ['admin_divisi', 'vp', 'svp', 'superadmin']) && $tipe === 'hutang-unit-alat')
        <!-- Modal for Adding Data with Bypass -->
        @include('dashboard.atb.partials.hutang-unit-alat.modal-add-bypass')
    @endif

    @if ($tipe === 'hutang-unit-alat')
        <!-- Modal for STT -->
        @include('dashboard.atb.partials.modal-stt')
    @endif

    @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
        @include('dashboard.atb.partials.modal-delete')
    @endif

    @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
        @if ($tipe === 'mutasi-proyek')
            <!-- Modal for Accept -->
            @include('dashboard.atb.partials.mutasi-proyek.modal-accept')

            <!-- Modal for Reject -->
            @include('dashboard.atb.partials.mutasi-proyek.modal-reject')
        @endif
    @endif
@endsection

@push('scripts_2')
    @include('components.form-submit-handler')
    @include('components.date-value-persistence')
    @include('components.datepicker')
@endpush
