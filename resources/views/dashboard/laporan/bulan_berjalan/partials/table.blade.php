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

        .currency-value {
            text-align: right !important;
            padding-right: 10px !important;
        }

        .collapse {
            display: none;
        }

        .bg-penerimaan {
            background-color: #e6ffe6 !important;
            /* Light green */
        }

        .bg-pengeluaran {
            background-color: #fff2e6 !important;
            /* Light orange */
        }

        .bg-saldo {
            background-color: #e6f3ff !important;
            /* Light blue */
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0 table-responsive overflow-auto">
    <table class="m-0 table table-bordered table-hover" id="table-data">
        <thead class="table-primary border-dark">
            <tr>
                <th class="border-dark" rowspan="2">NO.</th>
                <th class="border-dark" rowspan="2">U R A I A N</th>
                <th class="border-dark" colspan="4">PENERIMAAN (RP)</th>
                <th class="border-dark" rowspan="2">T O T A L<br>PENERIMAAN<br>(RP)</th>
                <th class="border-dark" colspan="4">PENGELUARAN (RP)</th>
                <th class="border-dark" rowspan="2">T O T A L<br>PENGELUARAN<br>(RP)</th>
                <th class="border-dark" colspan="4">SALDO (RP)</th>
                <th class="border-dark" rowspan="2">TOTAL SALDO<br>(RP)</th>
            </tr>
            <tr>
                <th class="border-dark">HUTANG UNIT ALAT</th>
                <th class="border-dark">PANJAR UNIT ALAT</th>
                <th class="border-dark">MUTASI PROYEK</th>
                <th class="border-dark">PANJAR PROYEK</th>

                <th class="border-dark">HUTANG UNIT ALAT</th>
                <th class="border-dark">PANJAR UNIT ALAT</th>
                <th class="border-dark">MUTASI PROYEK</th>
                <th class="border-dark">PANJAR PROYEK</th>

                <th class="border-dark">HUTANG UNIT ALAT</th>
                <th class="border-dark">PANJAR UNIT ALAT</th>
                <th class="border-dark">MUTASI PROYEK</th>
                <th class="border-dark">PANJAR PROYEK</th>
            </tr>
        </thead>

        <tbody class="border-dark">
            <!-- Calculate SUKU CADANG totals -->
            @php
                $sc_total_atb_hutang = 0;
                $sc_total_atb_panjar = 0;
                $sc_total_atb_mutasi = 0;
                $sc_total_atb_panjar_proyek = 0;
                $sc_total_atb = 0;
                $sc_total_apb_hutang = 0;
                $sc_total_apb_panjar = 0;
                $sc_total_apb_mutasi = 0;
                $sc_total_apb_panjar_proyek = 0;
                $sc_total_apb = 0;
                $sc_total_saldo_hutang = 0;
                $sc_total_saldo_panjar = 0;
                $sc_total_saldo_mutasi = 0;
                $sc_total_saldo_panjar_proyek = 0;
                $sc_total_saldo = 0;

                foreach ($sums as $key => $category) {
                    if ($category['jenis'] == 'Perbaikan' || $category['jenis'] == 'Pemeliharaan') {
                        $sc_total_atb_hutang += $category['atb']['hutang-unit-alat'];
                        $sc_total_atb_panjar += $category['atb']['panjar-unit-alat'];
                        $sc_total_atb_mutasi += $category['atb']['mutasi-proyek'];
                        $sc_total_atb_panjar_proyek += $category['atb']['panjar-proyek'];
                        $sc_total_atb += $category['atb']['total'];

                        $sc_total_apb_hutang += $category['apb']['hutang-unit-alat'];
                        $sc_total_apb_panjar += $category['apb']['panjar-unit-alat'];
                        $sc_total_apb_mutasi += $category['apb']['mutasi-proyek'];
                        $sc_total_apb_panjar_proyek += $category['apb']['panjar-proyek'];
                        $sc_total_apb += $category['apb']['total'];

                        $sc_total_saldo_hutang += $category['saldo']['hutang-unit-alat'];
                        $sc_total_saldo_panjar += $category['saldo']['panjar-unit-alat'];
                        $sc_total_saldo_mutasi += $category['saldo']['mutasi-proyek'];
                        $sc_total_saldo_panjar_proyek += $category['saldo']['panjar-proyek'];
                        $sc_total_saldo += $category['saldo']['total'];
                    }
                }
            @endphp

            <tr class="header-row" onclick="toggleAccordion('suku-cadang')">
                <td><strong>I</strong></td>
                <td><strong>SUKU CADANG</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_atb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_atb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_atb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_atb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_atb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_apb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_apb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_apb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_apb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_apb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_saldo_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_saldo_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_saldo_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_saldo_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($sc_total_saldo, 0, ',', '.') }}</strong></td>
            </tr>

            <!-- Hitung total untuk bagian Perbaikan -->
            @php
                $perbaikan_total_atb_hutang = 0;
                $perbaikan_total_atb_panjar = 0;
                $perbaikan_total_atb_mutasi = 0;
                $perbaikan_total_atb_panjar_proyek = 0;
                $perbaikan_total_atb = 0;
                $perbaikan_total_apb_hutang = 0;
                $perbaikan_total_apb_panjar = 0;
                $perbaikan_total_apb_mutasi = 0;
                $perbaikan_total_apb_panjar_proyek = 0;
                $perbaikan_total_apb = 0;
                $perbaikan_total_saldo_hutang = 0;
                $perbaikan_total_saldo_panjar = 0;
                $perbaikan_total_saldo_mutasi = 0;
                $perbaikan_total_saldo_panjar_proyek = 0;
                $perbaikan_total_saldo = 0;

                foreach ($sums as $key => $category) {
                    if ($category['jenis'] == 'Perbaikan') {
                        $perbaikan_total_atb_hutang += $category['atb']['hutang-unit-alat'];
                        $perbaikan_total_atb_panjar += $category['atb']['panjar-unit-alat'];
                        $perbaikan_total_atb_mutasi += $category['atb']['mutasi-proyek'];
                        $perbaikan_total_atb_panjar_proyek += $category['atb']['panjar-proyek'];
                        $perbaikan_total_atb += $category['atb']['total'];

                        $perbaikan_total_apb_hutang += $category['apb']['hutang-unit-alat'];
                        $perbaikan_total_apb_panjar += $category['apb']['panjar-unit-alat'];
                        $perbaikan_total_apb_mutasi += $category['apb']['mutasi-proyek'];
                        $perbaikan_total_apb_panjar_proyek += $category['apb']['panjar-proyek'];
                        $perbaikan_total_apb += $category['apb']['total'];

                        $perbaikan_total_saldo_hutang += $category['saldo']['hutang-unit-alat'];
                        $perbaikan_total_saldo_panjar += $category['saldo']['panjar-unit-alat'];
                        $perbaikan_total_saldo_mutasi += $category['saldo']['mutasi-proyek'];
                        $perbaikan_total_saldo_panjar_proyek += $category['saldo']['panjar-proyek'];
                        $perbaikan_total_saldo += $category['saldo']['total'];
                    }
                }
            @endphp

            <tr class="collapse suku-cadang" onclick="toggleAccordion('perbaikan')">
                <td><strong>A.</strong></td>
                <td><strong>PERBAIKAN</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_atb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_atb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_atb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_atb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_atb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_apb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_apb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_apb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_apb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_apb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_saldo_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_saldo_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_saldo_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_saldo_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($perbaikan_total_saldo, 0, ',', '.') }}</strong></td>
            </tr>

            @foreach ($sums as $key => $category)
                @if ($category['jenis'] == 'Perbaikan')
                    <tr class="collapse perbaikan">
                        <td>{{ preg_replace('/(\d+)/', '.$1', $key) }}</td>
                        <td>{{ ucwords(strtolower($category['nama'])) }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['total'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['total'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['total'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            @php
                $pemeliharaan_total_atb_hutang = 0;
                $pemeliharaan_total_atb_panjar = 0;
                $pemeliharaan_total_atb_mutasi = 0;
                $pemeliharaan_total_atb_panjar_proyek = 0;
                $pemeliharaan_total_atb = 0;
                $pemeliharaan_total_apb_hutang = 0;
                $pemeliharaan_total_apb_panjar = 0;
                $pemeliharaan_total_apb_mutasi = 0;
                $pemeliharaan_total_apb_panjar_proyek = 0;
                $pemeliharaan_total_apb = 0;
                $pemeliharaan_total_saldo_hutang = 0;
                $pemeliharaan_total_saldo_panjar = 0;
                $pemeliharaan_total_saldo_mutasi = 0;
                $pemeliharaan_total_saldo_panjar_proyek = 0;
                $pemeliharaan_total_saldo = 0;

                foreach ($sums as $key => $category) {
                    if ($category['jenis'] == 'Pemeliharaan') {
                        $pemeliharaan_total_atb_hutang += $category['atb']['hutang-unit-alat'];
                        $pemeliharaan_total_atb_panjar += $category['atb']['panjar-unit-alat'];
                        $pemeliharaan_total_atb_mutasi += $category['atb']['mutasi-proyek'];
                        $pemeliharaan_total_atb_panjar_proyek += $category['atb']['panjar-proyek'];
                        $pemeliharaan_total_atb += $category['atb']['total'];

                        $pemeliharaan_total_apb_hutang += $category['apb']['hutang-unit-alat'];
                        $pemeliharaan_total_apb_panjar += $category['apb']['panjar-unit-alat'];
                        $pemeliharaan_total_apb_mutasi += $category['apb']['mutasi-proyek'];
                        $pemeliharaan_total_apb_panjar_proyek += $category['apb']['panjar-proyek'];
                        $pemeliharaan_total_apb += $category['apb']['total'];

                        $pemeliharaan_total_saldo_hutang += $category['saldo']['hutang-unit-alat'];
                        $pemeliharaan_total_saldo_panjar += $category['saldo']['panjar-unit-alat'];
                        $pemeliharaan_total_saldo_mutasi += $category['saldo']['mutasi-proyek'];
                        $pemeliharaan_total_saldo_panjar_proyek += $category['saldo']['panjar-proyek'];
                        $pemeliharaan_total_saldo += $category['saldo']['total'];
                    }
                }
            @endphp

            <tr class="collapse suku-cadang" onclick="toggleAccordion('pemeliharaan')">
                <td><strong>B.</strong></td>
                <td><strong>PEMELIHARAAN</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_atb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_atb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_atb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_atb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_atb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_apb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_apb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_apb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_apb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_apb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_saldo_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_saldo_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_saldo_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_saldo_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($pemeliharaan_total_saldo, 0, ',', '.') }}</strong></td>
            </tr>

            <!-- Calculate Maintenance Kit totals -->
            @php
                $mk_total_atb_hutang = 0;
                $mk_total_atb_panjar = 0;
                $mk_total_atb_mutasi = 0;
                $mk_total_atb_panjar_proyek = 0;
                $mk_total_atb = 0;
                $mk_total_apb_hutang = 0;
                $mk_total_apb_panjar = 0;
                $mk_total_apb_mutasi = 0;
                $mk_total_apb_panjar_proyek = 0;
                $mk_total_apb = 0;
                $mk_total_saldo_hutang = 0;
                $mk_total_saldo_panjar = 0;
                $mk_total_saldo_mutasi = 0;
                $mk_total_saldo_panjar_proyek = 0;
                $mk_total_saldo = 0;

                foreach ($sums as $key => $category) {
                    if ($category['jenis'] == 'Pemeliharaan' && $category['subJenis'] == 'MAINTENANCE KIT') {
                        $mk_total_atb_hutang += $category['atb']['hutang-unit-alat'];
                        $mk_total_atb_panjar += $category['atb']['panjar-unit-alat'];
                        $mk_total_atb_mutasi += $category['atb']['mutasi-proyek'];
                        $mk_total_atb_panjar_proyek += $category['atb']['panjar-proyek'];
                        $mk_total_atb += $category['atb']['total'];

                        $mk_total_apb_hutang += $category['apb']['hutang-unit-alat'];
                        $mk_total_apb_panjar += $category['apb']['panjar-unit-alat'];
                        $mk_total_apb_mutasi += $category['apb']['mutasi-proyek'];
                        $mk_total_apb_panjar_proyek += $category['apb']['panjar-proyek'];
                        $mk_total_apb += $category['apb']['total'];

                        $mk_total_saldo_hutang += $category['saldo']['hutang-unit-alat'];
                        $mk_total_saldo_panjar += $category['saldo']['panjar-unit-alat'];
                        $mk_total_saldo_mutasi += $category['saldo']['mutasi-proyek'];
                        $mk_total_saldo_panjar_proyek += $category['saldo']['panjar-proyek'];
                        $mk_total_saldo += $category['saldo']['total'];
                    }
                }
            @endphp

            <tr class="collapse pemeliharaan" onclick="toggleAccordion('maintenance-kit')">
                <td><strong>B.1</strong></td>
                <td><strong>Maintenance Kit</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_atb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_atb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_atb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_atb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_atb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_apb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_apb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_apb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_apb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_apb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_saldo_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_saldo_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_saldo_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_saldo_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($mk_total_saldo, 0, ',', '.') }}</strong></td>
            </tr>

            @foreach ($sums as $key => $category)
                @if ($category['jenis'] == 'Pemeliharaan' && $category['subJenis'] == 'MAINTENANCE KIT')
                    <tr class="collapse maintenance-kit">
                        <td>{{ implode('.', str_split($key)) }}</td>
                        <td>{{ ucwords(strtolower($category['nama'])) }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['total'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['total'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['total'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            <!-- Calculate Oil & Lubricants totals -->
            @php
                $oil_total_atb_hutang = 0;
                $oil_total_atb_panjar = 0;
                $oil_total_atb_mutasi = 0;
                $oil_total_atb_panjar_proyek = 0;
                $oil_total_atb = 0;
                $oil_total_apb_hutang = 0;
                $oil_total_apb_panjar = 0;
                $oil_total_apb_mutasi = 0;
                $oil_total_apb_panjar_proyek = 0;
                $oil_total_apb = 0;
                $oil_total_saldo_hutang = 0;
                $oil_total_saldo_panjar = 0;
                $oil_total_saldo_mutasi = 0;
                $oil_total_saldo_panjar_proyek = 0;
                $oil_total_saldo = 0;

                foreach ($sums as $key => $category) {
                    if ($category['jenis'] == 'Pemeliharaan' && $category['subJenis'] == 'OIL & LUBRICANTS') {
                        $oil_total_atb_hutang += $category['atb']['hutang-unit-alat'];
                        $oil_total_atb_panjar += $category['atb']['panjar-unit-alat'];
                        $oil_total_atb_mutasi += $category['atb']['mutasi-proyek'];
                        $oil_total_atb_panjar_proyek += $category['atb']['panjar-proyek'];
                        $oil_total_atb += $category['atb']['total'];

                        $oil_total_apb_hutang += $category['apb']['hutang-unit-alat'];
                        $oil_total_apb_panjar += $category['apb']['panjar-unit-alat'];
                        $oil_total_apb_mutasi += $category['apb']['mutasi-proyek'];
                        $oil_total_apb_panjar_proyek += $category['apb']['panjar-proyek'];
                        $oil_total_apb += $category['apb']['total'];

                        $oil_total_saldo_hutang += $category['saldo']['hutang-unit-alat'];
                        $oil_total_saldo_panjar += $category['saldo']['panjar-unit-alat'];
                        $oil_total_saldo_mutasi += $category['saldo']['mutasi-proyek'];
                        $oil_total_saldo_panjar_proyek += $category['saldo']['panjar-proyek'];
                        $oil_total_saldo += $category['saldo']['total'];
                    }
                }
            @endphp

            <tr class="collapse pemeliharaan" onclick="toggleAccordion('oil-lubricants')">
                <td><strong>B.2</strong></td>
                <td><strong>Oil & Lubricants</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_atb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_atb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_atb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_atb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_atb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_apb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_apb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_apb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_apb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_apb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_saldo_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_saldo_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_saldo_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_saldo_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($oil_total_saldo, 0, ',', '.') }}</strong></td>
            </tr>

            @foreach ($sums as $key => $category)
                @if ($category['jenis'] == 'Pemeliharaan' && $category['subJenis'] == 'OIL & LUBRICANTS')
                    <tr class="collapse oil-lubricants">
                        <td>{{ implode('.', str_split($key)) }}</td>
                        <td>{{ ucwords(strtolower($category['nama'])) }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['total'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['total'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['total'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            @foreach ($sums as $key => $category)
                @if ($key == 'B3')
                    <tr class="collapse pemeliharaan">
                        <td><strong>{{ $key }}</strong></td>
                        <td><strong>{{ $category['nama'] }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['atb']['hutang-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['atb']['panjar-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['atb']['mutasi-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['atb']['panjar-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['atb']['total'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['apb']['hutang-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['apb']['panjar-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['apb']['mutasi-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['apb']['panjar-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['apb']['total'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['saldo']['hutang-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['saldo']['panjar-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['saldo']['mutasi-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['saldo']['panjar-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['saldo']['total'], 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            @endforeach

            @foreach ($sums as $key => $category)
                @if ($key == 'C1')
                    <tr class="header-row" onclick="toggleAccordion('material')">
                        <td><strong>II</strong></td>
                        <td><strong>MATERIAL</strong></td>

                        <td class="currency-value"><strong>{{ number_format($category['atb']['hutang-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['atb']['panjar-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['atb']['mutasi-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['atb']['panjar-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['atb']['total'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['apb']['hutang-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['apb']['panjar-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['apb']['mutasi-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['apb']['panjar-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['apb']['total'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['saldo']['hutang-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['saldo']['panjar-unit-alat'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['saldo']['mutasi-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['saldo']['panjar-proyek'], 0, ',', '.') }}</strong></td>
                        <td class="currency-value"><strong>{{ number_format($category['saldo']['total'], 0, ',', '.') }}</strong></td>
                    </tr>

                    <tr class="collapse material">
                        <td>{{ implode('.', str_split($key)) }}</td>
                        <td>{{ ucwords(strtolower($category['nama'])) }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['atb']['total'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['apb']['total'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['hutang-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['panjar-unit-alat'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['mutasi-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['panjar-proyek'], 0, ',', '.') }}</td>
                        <td class="currency-value">{{ number_format($category['saldo']['total'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

        </tbody>

        <tfoot>
            @php
                $grand_total_atb_hutang = 0;
                $grand_total_atb_panjar = 0;
                $grand_total_atb_mutasi = 0;
                $grand_total_atb_panjar_proyek = 0;
                $grand_total_atb = 0;
                $grand_total_apb_hutang = 0;
                $grand_total_apb_panjar = 0;
                $grand_total_apb_mutasi = 0;
                $grand_total_apb_panjar_proyek = 0;
                $grand_total_apb = 0;
                $grand_total_saldo_hutang = 0;
                $grand_total_saldo_panjar = 0;
                $grand_total_saldo_mutasi = 0;
                $grand_total_saldo_panjar_proyek = 0;
                $grand_total_saldo = 0;

                foreach ($sums as $category) {
                    $grand_total_atb_hutang += $category['atb']['hutang-unit-alat'];
                    $grand_total_atb_panjar += $category['atb']['panjar-unit-alat'];
                    $grand_total_atb_mutasi += $category['atb']['mutasi-proyek'];
                    $grand_total_atb_panjar_proyek += $category['atb']['panjar-proyek'];
                    $grand_total_atb += $category['atb']['total'];

                    $grand_total_apb_hutang += $category['apb']['hutang-unit-alat'];
                    $grand_total_apb_panjar += $category['apb']['panjar-unit-alat'];
                    $grand_total_apb_mutasi += $category['apb']['mutasi-proyek'];
                    $grand_total_apb_panjar_proyek += $category['apb']['panjar-proyek'];
                    $grand_total_apb += $category['apb']['total'];

                    $grand_total_saldo_hutang += $category['saldo']['hutang-unit-alat'];
                    $grand_total_saldo_panjar += $category['saldo']['panjar-unit-alat'];
                    $grand_total_saldo_mutasi += $category['saldo']['mutasi-proyek'];
                    $grand_total_saldo_panjar_proyek += $category['saldo']['panjar-proyek'];
                    $grand_total_saldo += $category['saldo']['total'];
                }
            @endphp

            <tr class="total-row border-dark table-bordered table-striped">
                <td colspan="2"><strong>TOTAL</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_atb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_atb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_atb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_atb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_atb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_apb_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_apb_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_apb_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_apb_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_apb, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_saldo_hutang, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_saldo_panjar, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_saldo_mutasi, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_saldo_panjar_proyek, 0, ',', '.') }}</strong></td>
                <td class="currency-value"><strong>{{ number_format($grand_total_saldo, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>

@push('scripts_3')
    <script></script>
    <script>
        // Add these variables at the top with the state object
        let isAllExpanded = false;

        function toggleAll() {
            isAllExpanded = !isAllExpanded;
            const icon = document.getElementById('toggleAllIcon');
            const text = document.getElementById('toggleAllText');

            icon.className = isAllExpanded ? 'fa fa-compress' : 'fa fa-expand';
            text.textContent = isAllExpanded ? 'Collapse All' : 'Expand All';

            // Toggle main sections
            const mainSections = ['suku-cadang', 'material'];
            mainSections.forEach(section => {
                const rows = document.querySelectorAll(`.${section}`);
                rows.forEach(row => {
                    row.style.display = isAllExpanded ? 'table-row' : 'none';
                });
                state[section].isOpen = isAllExpanded;

                // Toggle subsections
                if (state[section].children) {
                    toggleChildren(section, isAllExpanded);
                }
            });
        }

        function toggleChildren(section, isExpanded) {
            const children = state[section].children;
            children.forEach(child => {
                const rows = document.querySelectorAll(`.${child}`);
                rows.forEach(row => {
                    row.style.display = isExpanded ? 'table-row' : 'none';
                });
                state[child].isOpen = isExpanded;

                // Recursively toggle nested children
                if (state[child].children) {
                    toggleChildren(child, isExpanded);
                }
            });
        }

        let state = {
            'suku-cadang': {
                isOpen: false,
                children: ['perbaikan', 'pemeliharaan']
            },
            'perbaikan': {
                isOpen: false,
                children: []
            },
            'pemeliharaan': {
                isOpen: false,
                children: ['maintenance-kit', 'oil-lubricants']
            },
            'maintenance-kit': {
                isOpen: false,
                children: []
            },
            'oil-lubricants': {
                isOpen: false,
                children: []
            },
            'material': {
                isOpen: false,
                children: []
            },
        };

        function toggleAccordion(section) {
            const rows = document.querySelectorAll(`.${section}`);
            const isVisible = rows[0].style.display === 'table-row';
            state[section].isOpen = !isVisible;

            rows.forEach(row => {
                row.style.display = isVisible ? 'none' : 'table-row';
            });

            if (!isVisible) {
                restoreChildrenState(section);
            } else {
                collapseChildren(section);
            }

            // Check if all sections are expanded/collapsed and update button accordingly
            updateExpandAllButtonState();
        }

        function collapseChildren(section) {
            const children = state[section].children;
            children.forEach(child => {
                const rows = document.querySelectorAll(`.${child}`);
                rows.forEach(row => {
                    row.style.display = 'none';
                });
                collapseChildren(child);
            });
        }

        function restoreChildrenState(section) {
            const children = state[section].children;
            children.forEach(child => {
                if (state[child].isOpen) {
                    const rows = document.querySelectorAll(`.${child}`);
                    rows.forEach(row => {
                        row.style.display = 'table-row';
                    });
                    restoreChildrenState(child);
                }
            });
        }

        function updateExpandAllButtonState() {
            const button = document.getElementById('toggleAllButton');
            const icon = document.getElementById('toggleAllIcon');
            const text = document.getElementById('toggleAllText');

            const allExpanded = Object.keys(state).every(section =>
                !document.querySelectorAll(`.${section}`).length ||
                state[section].isOpen
            );

            isAllExpanded = allExpanded;
            icon.className = allExpanded ? 'fa fa-compress' : 'fa fa-expand';
            text.textContent = allExpanded ? 'Collapse All' : 'Expand All';
        }

        // Update the setBackgroundForColumns function to handle colspan in tfoot
        function setBackgroundForColumns() {
            const table = document.getElementById('table-data');
            const tbody = table.getElementsByTagName('tbody')[0];
            const tfoot = table.getElementsByTagName('tfoot')[0];

            // Process tbody rows
            const bodyRows = tbody.getElementsByTagName('tr');
            for (let row of bodyRows) {
                const cells = row.cells;
                if (cells.length > 0) {
                    // Add background for Penerimaan (columns 3-6)
                    for (let i = 2; i <= 5; i++) {
                        if (cells[i]) {
                            cells[i].classList.add('bg-penerimaan');
                        }
                    }
                    // Add background for Pengeluaran (columns 8-11)
                    for (let i = 7; i <= 10; i++) {
                        if (cells[i]) {
                            cells[i].classList.add('bg-pengeluaran');
                        }
                    }
                    // Add background for Saldo (columns 13-16)
                    for (let i = 12; i <= 15; i++) {
                        if (cells[i]) {
                            cells[i].classList.add('bg-saldo');
                        }
                    }
                }
            }

            // Process tfoot rows - special handling for colspan
            const footRows = tfoot.getElementsByTagName('tr');
            for (let row of footRows) {
                const cells = row.cells;
                if (cells.length > 0) {
                    // Skip first cell (index 0) which has colspan=2
                    // Penerimaan columns
                    for (let i = 1; i <= 4; i++) {
                        if (cells[i]) {
                            cells[i].classList.add('bg-penerimaan');
                        }
                    }
                    // Pengeluaran columns
                    for (let i = 6; i <= 9; i++) {
                        if (cells[i]) {
                            cells[i].classList.add('bg-pengeluaran');
                        }
                    }
                    // Saldo columns
                    for (let i = 11; i <= 14; i++) {
                        if (cells[i]) {
                            cells[i].classList.add('bg-saldo');
                        }
                    }
                }
            }
        }

        // Call the function when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            setBackgroundForColumns();
        });
    </script>
@endpush
