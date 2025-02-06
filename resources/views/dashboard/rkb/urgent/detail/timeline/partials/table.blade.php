@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            vertical-align: middle;
            text-align: center;
        }

        .img-thumbnail {
            width: 120px;
            height: 120px;
            object-fit: cover;
            cursor: pointer;
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0">
    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th>Uraian Pekerjaan</th>
                    <th>Waktu Penyelesaian (Rencana)</th>
                    <th>Tanggal Awal Rencana</th>
                    <th>Tanggal Akhir Rencana</th>
                    <th>Waktu Penyelesaian (Actual)</th>
                    <th>Tanggal Awal Actual</th>
                    <th>Tanggal Akhir Actual</th>
                    <th>Status</th>
                    @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    <tr>
                        <td class="text-center">{{ $item->nama_rencana }}</td>
                        <td class="text-center">{{ $item->diff_in_days_rencana ? $item->diff_in_days_rencana . ' Hari' : '-' }}</td>
                        <td class="text-center">{{ $item->tanggal_awal_rencana ? $item->tanggal_awal_rencana->format('Y-m-d') : '-' }}</td>
                        <td class="text-center">{{ $item->tanggal_akhir_rencana ? $item->tanggal_akhir_rencana->format('Y-m-d') : '-' }}</td>
                        <td class="text-center">{{ $item->diff_in_days_actual ? $item->diff_in_days_actual . ' Hari' : '-' }}</td>
                        <td class="text-center">{{ $item->tanggal_awal_actual ? $item->tanggal_awal_actual->format('Y-m-d') : '-' }}</td>
                        <td class="text-center">{{ $item->tanggal_akhir_actual ? $item->tanggal_akhir_actual->format('Y-m-d') : '-' }}</td>
                        <td class="text-center"><span class="badge {{ $item->is_done ? 'bg-success' : 'bg-warning' }} w-100">{{ $item->is_done ? 'Sudah Selesai' : 'Belum Selesai' }}</span></td>
                        @if (Auth::user()->role === 'koordinator_proyek' || Auth::user()->role === 'superadmin')
                            <td class="text-center">
                                <button class="btn btn-warning mx-1 editBtn" data-id="{{ $item->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $item->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3" colspan="9">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <span class="text-muted">No data found</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
@endpush
