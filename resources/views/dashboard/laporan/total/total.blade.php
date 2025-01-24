@extends('layouts.app')

@push('styles_2')
    <style>
        input[type="month"] {
            cursor: pointer;
            position: relative;
        }

        input[type="month"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            position: absolute;
            right: 0;
            padding: 5px;
            background-color: transparent;
        }

        .date-input-container {
            position: relative;
            display: inline-block;
        }

        .date-input-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="h-100">
        <div class="fade-in-up page-content">
            <div class="ibox">
                <div class="ibox-head pe-0 ps-0">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <!-- Left side - Page title -->
                        <div class="ibox-title ps-2">
                            <h5 class="fw-medium">{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</h5>
                        </div>

                        <!-- Center - Period selection -->
                        <div class="d-flex align-items-center gap-2">
                            <span>Periode {{ \Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }}</span>
                            <span>s/d</span>
                            <div class="date-input-container">
                                <input class="form-control" id="endDate" name="endDate" type="month" value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m') }}" style="width: 150px;" onchange="filterPeriode()">
                                <div class="date-input-overlay" onclick="document.getElementById('endDate').showPicker()"></div>
                            </div>
                        </div>

                        <!-- Right side - Buttons -->
                        <div class="d-flex gap-2">
                            <a class="btn btn-warning btn-sm" href="{{ isset($proyek) ? route('export.lnpb_total', ['id' => $proyek->id]) : route('export.lnpb_total') }}">
                                <i class="fa-solid fa-file-excel"></i> <span class="ms-2">Export</span>
                            </a>
                            <button class="btn btn-primary btn-sm" id="toggleAllButton" type="button" onclick="toggleAll()">
                                <i class="fa fa-expand" id="toggleAllIcon"></i>
                                <span class="ms-2" id="toggleAllText">Expand All</span>
                            </button>
                        </div>
                    </div>
                </div>

                @include('dashboard.laporan.total.partials.table')

            </div>
        </div>
    </div>

    <!-- Modal for Adding Data -->
    {{-- @include('dashboard.masterdata.alat.partials.modal-add') --}}

    <!-- Modal for Editing Data -->
    {{-- @include('dashboard.masterdata.alat.partials.modal-edit') --}}

    <!-- Modal for Delete Confirmation -->
    {{-- @include('dashboard.masterdata.alat.partials.modal-delete') --}}
@endsection

@push('scripts_2')
    <script>
        function filterPeriode() {
            let endDate = document.getElementById('endDate').value + '-25'; // End at 25th
            let startDate = document.getElementById('endDate').value.split('-');
            let startMonth = parseInt(startDate[1]) - 1;
            if (startMonth === 0) {
                startMonth = 12;
                startDate[0] = parseInt(startDate[0]) - 1;
            }
            startDate = `${startDate[0]}-${String(startMonth).padStart(2, '0')}-26`; // Start at 26th of previous month

            let currentUrl = new URL(window.location.href);
            let id_proyek = currentUrl.searchParams.get('id_proyek');
            window.location.href = `{{ url()->current() }}?id_proyek=${id_proyek}&startDate=${startDate}&endDate=${endDate}`;
        }
    </script>
@endpush
