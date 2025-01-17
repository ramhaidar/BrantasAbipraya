@extends('layouts.app')

@push('styles_2')
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class=ibox-title>
                        <p class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</p>
                    </div>
                    <a class="btn btn-primary btn-sm" id=button-for-modal-add onclick=showModalAdd()>
                        <i class="fa fa-plus"></i> <span class=ms-2>Tambah Data</span>
                    </a>
                </div>

                @include('dashboard.users.partials.table')

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
    <script></script>
@endpush
