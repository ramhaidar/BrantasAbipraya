@extends('layouts.app')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="ibox-title ps-2">
                        <p class="p-0 m-0 fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}
                        </p>
                    </div>
                    <!-- Tambahkan Tombol Export PDF di Ujung Kanan -->
                    <div class="ms-auto pe-2">
                        <a class="btn btn-danger" href="{{ route('spb.detail.riwayat.export-pdf', ['id' => $spb->id]) }}">
                            Export PDF
                        </a>
                    </div>
                    {{-- <div class="ms-auto d-flex gap-2 pe-2">
                        <p class="text-end fw-medium">{{ $spb->nomor }}</p>
                    </div> --}}
                </div>

                @include('dashboard.spb.riwayat.partials.table')

            </div>
        </div>
    </div>

    <!-- Modal for Adding Data -->
    {{-- @include('dashboard.spb.detail.partials.modal-create') --}}

    <!-- Modal for Deleting Data -->
    {{-- @include('dashboard.rkb.urgent.partials.modal-delete') --}}

    <!-- Modal for Editing Data -->
    {{-- @include('dashboard.rkb.urgent.partials.modal-edit') --}}
@endsection

@push('scripts_2')
@endpush
