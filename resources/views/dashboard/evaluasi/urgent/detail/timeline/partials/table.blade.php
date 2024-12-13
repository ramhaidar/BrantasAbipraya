@push('styles_3')
    <style>
        #table-data {
            font-size: 0.9em;
            white-space: nowrap;
        }

        #table-data td,
        #table-data th {
            /* padding: 4px 8px; */
            vertical-align: middle;
        }

        .img-thumbnail {
            width: 120px;
            height: 120px;
            object-fit: cover;
            cursor: pointer;
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0 table-responsive" style="overflow-x: hidden">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary">
            <tr>
                <th class="text-center">Uraian Pekerjaan</th>
                <th class="text-center">Waktu Penyelesaian (Rencana)</th>
                <th class="text-center">Tanggal Awal Rencana</th>
                <th class="text-center">Tanggal Akhir Rencana</th>
                <th class="text-center">Waktu Penyelesaian (Actual)</th>
                <th class="text-center">Tanggal Awal Actual</th>
                <th class="text-center">Tanggal Akhir Actual</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->timelineRkbUrgents as $item)
                <tr>
                    <td class="text-center">{{ $item->nama_rencana }}</td>
                    @php
                        $diffInDaysRencana = null;

                        if (isset($item->tanggal_awal_rencana) && isset($item->tanggal_akhir_rencana)) {
                            $startDateRencana = Illuminate\Support\Carbon::parse($item->tanggal_awal_rencana);

                            $endDateRencana = Illuminate\Support\Carbon::parse($item->tanggal_akhir_rencana);

                            $diffInDaysRencana = $startDateRencana->diffInDays($endDateRencana) + 1;
                        }

                        $diffInDaysActual = null;

                        if (isset($item->tanggal_awal_actual) && isset($item->tanggal_akhir_actual)) {
                            $startDateActual = Illuminate\Support\Carbon::parse($item->tanggal_awal_actual);
                            $endDateActual = Illuminate\Support\Carbon::parse($item->tanggal_akhir_actual);

                            $diffInDaysActual = $startDateActual->diffInDays($endDateActual) + 1;
                        }
                    @endphp
                    <td class="text-center">{{ $diffInDaysRencana ? $diffInDaysRencana . ' Hari' : '-' }}</td>
                    <td class="text-center">{{ $item->tanggal_awal_rencana ? \Illuminate\Support\Carbon::parse($item->tanggal_awal_rencana)->format('Y-m-d') : '-' }}</td>
                    <td class="text-center">{{ $item->tanggal_akhir_rencana ? \Illuminate\Support\Carbon::parse($item->tanggal_akhir_rencana)->format('Y-m-d') : '-' }}</td>
                    <td class="text-center">{{ $diffInDaysActual ? $diffInDaysActual . ' Hari' : '-' }}</td>
                    <td class="text-center">{{ $item->tanggal_awal_actual ? \Illuminate\Support\Carbon::parse($item->tanggal_awal_actual)->format('Y-m-d') : '-' }}</td>
                    <td class="text-center">{{ $item->tanggal_akhir_actual ? \Illuminate\Support\Carbon::parse($item->tanggal_akhir_actual)->format('Y-m-d') : '-' }}</td>
                    <td class="text-center"><span class="badge {{ $item->is_done ? 'bg-success' : 'bg-warning' }} w-100">{{ $item->is_done ? 'Sudah Selesai' : 'Belum Selesai' }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            $('#table-data').DataTable({
                paginate: false,
                ordering: false,
            });
        });
    </script>
@endpush
