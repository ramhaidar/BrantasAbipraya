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
    {{ $proyek->nama . ' - APB - ' . formatTipeTitle($tipe) }}
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
                        <a class="btn btn-warning btn-sm btn-hide-text-mobile" href="{{ route('export.apb', ['id' => $proyek->id, 'type' => $tipe]) }}">
                            <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export</span>
                        </a>
                        @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                            @if ($tipe === 'mutasi-proyek')
                                <a class="btn btn-success btn-sm btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#modalForMutasi">
                                    <i class="fa fa-exchange"></i> <span class="ms-2">Mutasi Proyek</span>
                                </a>
                                <a class="btn btn-primary btn-sm btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                    <i class="fa fa-wrench"></i> <span class="ms-2">Gunakan Sparepart</span>
                                </a>
                            @else
                                <a class="btn btn-primary btn-sm btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#modalForAdd">
                                    <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data APB</span>
                                </a>
                                @if (in_array(Auth::user()->role, env('IS_BETA') ? ['admin_divisi', 'vp', 'svp', 'superadmin', 'koordinator_proyek'] : ['admin_divisi', 'vp', 'svp', 'superadmin']) && $tipe === 'hutang-unit-alat')
                                    <a class="btn btn-primary btn-sm btn-hide-text-mobile" data-bs-toggle="modal" data-bs-target="#modalForAddBypass">
                                        <i class="fa fa-plus"></i> <span class="ms-2">Tambah Data ATB [Bypass]</span>
                                    </a>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>

                @if ($tipe === 'mutasi-proyek')
                    {{-- @include('dashboard.apb.partials.mutasi-proyek.table') --}}
                    <div class="p-0 m-0 py-3">
                        @include('components.search-input', [
                            'route' => url()->current(),
                            'placeholder' => 'Search items...',
                        ])
                        @include('dashboard.apb.partials.mutasi-proyek.table', [
                            'TableData' => $TableData,
                        ])
                        @if ($TableData->hasPages())
                            @include('components.pagination', [
                                'paginator' => $TableData,
                            ])
                        @endif
                    </div>
                @else
                    {{-- @include('dashboard.apb.partials.table') --}}
                    <div class="p-0 m-0 py-3">
                        @include('components.search-input', [
                            'route' => url()->current(),
                            'placeholder' => 'Search items...',
                        ])
                        @include('dashboard.apb.partials.table', [
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

    @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
        @if ($tipe === 'mutasi-proyek')
            <!-- Modal for Adding Data -->
            @include('dashboard.apb.partials.mutasi-proyek.modal-add')

            <!-- Modal for Mutasi Proyek -->
            @include('dashboard.apb.partials.mutasi-proyek.modal-mutasi')
        @else
            <!-- Modal for Adding Data -->
            @include('dashboard.apb.partials.modal-add')

            <!-- Modal for Adding Bypass Data -->
            @include('dashboard.apb.partials.modal-add-bypass')
        @endif

        @if ($tipe === 'mutasi-proyek')
            <!-- Modal for Delete -->
            @include('dashboard.apb.partials.mutasi-proyek.modal-delete')
        @else
            <!-- Modal for Delete -->
            @include('dashboard.apb.partials.modal-delete')
        @endif
    @endif
@endsection

@push('scripts_2')
    @include('components.form-submit-handler')
    @include('components.date-value-persistence')
    @include('components.datepicker')
@endpush
