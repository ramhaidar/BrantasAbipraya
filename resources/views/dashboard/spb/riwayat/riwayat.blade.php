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

                    <div class="ms-auto d-flex gap-2 pe-2">
                        <!-- Tambahkan Tombol Modal Preview -->
                        <div class="ms-auto pe-2">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPreview">
                                <i class="fa fa-eye"></i> <span class="ms-2">Preview</span>
                            </button>
                        </div>

                        <!-- Tambahkan Tombol Export PDF di Ujung Kanan -->
                        <div class="ms-auto pe-2">
                            <a class="btn btn-primary" href="{{ route('spb.detail.riwayat.export-pdf', ['id' => $spb->id]) }}" target="_blank">
                                <i class="fa fa-file-pdf"></i> <span class="ms-2">Export PDF</span>
                            </a>
                        </div>
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
    @include('dashboard.spb.riwayat.partials.modal-preview')

    <!-- Modal for Deleting Data -->
    {{-- @include('dashboard.rkb.urgent.partials.modal-delete') --}}

    <!-- Modal for Editing Data -->
    {{-- @include('dashboard.rkb.urgent.partials.modal-edit') --}}
@endsection

@push('scripts_2')
@endpush
