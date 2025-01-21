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

        .collapse {
            display: none;
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0 table-responsive overflow-auto">
    <table class="m-0 table table-bordered table-striped" id="table-data">
        <thead class="table-primary border-dark">
            <tr>
                <th class="border-dark" rowspan="2">NO.</th>
                <th class="border-dark" rowspan="2">U R A I A N</th>
                <th class="border-dark" colspan="3">S/D BULAN LALU (RP)</th>
                <th class="border-dark" colspan="3">BULAN INI (RP)</th>
                <th class="border-dark" colspan="3">S/D BULAN INI (RP)</th>
            </tr>
            <tr>
                <th class="border-dark">PENERIMAAN</th>
                <th class="border-dark">PENGELUARAN</th>
                <th class="border-dark">SALDO AKHIR</th>
                <th class="border-dark">PENERIMAAN</th>
                <th class="border-dark">PENGELUARAN</th>
                <th class="border-dark">SALDO AKHIR</th>
                <th class="border-dark">PENERIMAAN</th>
                <th class="border-dark">PENGELUARAN</th>
                <th class="border-dark">SALDO AKHIR</th>
            </tr>
        </thead>

        <tbody class="border-dark">
            @php
                // Initialize totals
                $suku_cadang_before = ['atb' => 0, 'apb' => 0, 'saldo' => 0];
                $suku_cadang_current = ['atb' => 0, 'apb' => 0, 'saldo' => 0];

                // Calculate totals by iterating through data
                foreach ($sums_current as $key => $category) {
                    if ($category['jenis'] == 'Perbaikan' || $category['jenis'] == 'Pemeliharaan') {
                        // Add previous month values
                        $suku_cadang_before['atb'] += $sums_before[$key]['atb'];
                        $suku_cadang_before['apb'] += $sums_before[$key]['apb'];
                        $suku_cadang_before['saldo'] += $sums_before[$key]['saldo'];

                        // Add current month values
                        $suku_cadang_current['atb'] += $category['atb'];
                        $suku_cadang_current['apb'] += $category['apb'];
                        $suku_cadang_current['saldo'] += $category['saldo'];
                    }
                }
            @endphp

            <tr class="header-row accordion-header" onclick="toggleAccordion('suku-cadang')">
                <td><strong>I</strong></td>
                <td><strong>SUKU CADANG</strong></td>
                <td class="text-center"><strong>{{ number_format($suku_cadang_before['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($suku_cadang_before['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($suku_cadang_before['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($suku_cadang_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($suku_cadang_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($suku_cadang_current['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($suku_cadang_before['atb'] + $suku_cadang_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($suku_cadang_before['apb'] + $suku_cadang_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($suku_cadang_before['saldo'] + $suku_cadang_current['saldo'], 0, ',', '.') }}</strong></td>
            </tr>

            @php
                $total_perbaikan_before = ['atb' => 0, 'apb' => 0, 'saldo' => 0];
                $total_perbaikan_current = ['atb' => 0, 'apb' => 0, 'saldo' => 0];

                foreach ($sums_current as $key => $category) {
                    if ($category['jenis'] == 'Perbaikan') {
                        $total_perbaikan_before['atb'] += $sums_before[$key]['atb'];
                        $total_perbaikan_before['apb'] += $sums_before[$key]['apb'];
                        $total_perbaikan_before['saldo'] += $sums_before[$key]['saldo'];

                        $total_perbaikan_current['atb'] += $category['atb'];
                        $total_perbaikan_current['apb'] += $category['apb'];
                        $total_perbaikan_current['saldo'] += $category['saldo'];
                    }
                }
            @endphp

            <tr class="collapse suku-cadang subheader-row" onclick="toggleAccordion('perbaikan')">
                <td><strong>A.</strong></td>
                <td><strong>PERBAIKAN</strong></td>
                <td class="text-center"><strong>{{ number_format($total_perbaikan_before['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_perbaikan_before['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_perbaikan_before['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_perbaikan_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_perbaikan_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_perbaikan_current['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_perbaikan_before['atb'] + $total_perbaikan_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_perbaikan_before['apb'] + $total_perbaikan_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_perbaikan_before['saldo'] + $total_perbaikan_current['saldo'], 0, ',', '.') }}</strong></td>
            </tr>

            @foreach ($sums_current as $key => $category)
                @if ($category['jenis'] == 'Perbaikan')
                    <tr class="collapse perbaikan">
                        <td>{{ preg_replace('/(\d+)/', '.$1', $key) }}</td>
                        <td>{{ ucwords(strtolower($category['nama'])) }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['atb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['apb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['saldo'], 0, ',', '.') }}</td>

                        <td class="text-center">{{ number_format($category['atb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($category['apb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($category['saldo'], 0, ',', '.') }}</td>

                        <td class="text-center">{{ number_format($sums_before[$key]['atb'] + $category['atb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['apb'] + $category['apb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['saldo'] + $category['saldo'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            @php
                $total_pemeliharaan_before = ['atb' => 0, 'apb' => 0, 'saldo' => 0];
                $total_pemeliharaan_current = ['atb' => 0, 'apb' => 0, 'saldo' => 0];

                foreach ($sums_current as $key => $category) {
                    if ($category['jenis'] == 'Pemeliharaan') {
                        $total_pemeliharaan_before['atb'] += $sums_before[$key]['atb'];
                        $total_pemeliharaan_before['apb'] += $sums_before[$key]['apb'];
                        $total_pemeliharaan_before['saldo'] += $sums_before[$key]['saldo'];

                        $total_pemeliharaan_current['atb'] += $category['atb'];
                        $total_pemeliharaan_current['apb'] += $category['apb'];
                        $total_pemeliharaan_current['saldo'] += $category['saldo'];
                    }
                }
            @endphp

            <tr class="collapse suku-cadang subheader-row" onclick="toggleAccordion('pemeliharaan')">
                <td><strong>B.</strong></td>
                <td><strong>PEMELIHARAAN</strong></td>
                <td class="text-center"><strong>{{ number_format($total_pemeliharaan_before['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_pemeliharaan_before['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_pemeliharaan_before['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_pemeliharaan_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_pemeliharaan_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_pemeliharaan_current['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_pemeliharaan_before['atb'] + $total_pemeliharaan_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_pemeliharaan_before['apb'] + $total_pemeliharaan_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_pemeliharaan_before['saldo'] + $total_pemeliharaan_current['saldo'], 0, ',', '.') }}</strong></td>
            </tr>

            @php
                $total_maintenance_kit_before = ['atb' => 0, 'apb' => 0, 'saldo' => 0];
                $total_maintenance_kit_current = ['atb' => 0, 'apb' => 0, 'saldo' => 0];

                foreach ($sums_current as $key => $category) {
                    if ($category['jenis'] == 'Pemeliharaan' && $category['subJenis'] == 'MAINTENANCE KIT') {
                        $total_maintenance_kit_before['atb'] += $sums_before[$key]['atb'];
                        $total_maintenance_kit_before['apb'] += $sums_before[$key]['apb'];
                        $total_maintenance_kit_before['saldo'] += $sums_before[$key]['saldo'];

                        $total_maintenance_kit_current['atb'] += $category['atb'];
                        $total_maintenance_kit_current['apb'] += $category['apb'];
                        $total_maintenance_kit_current['saldo'] += $category['saldo'];
                    }
                }
            @endphp

            <tr class="collapse pemeliharaan subheader-row" onclick="toggleAccordion('maintenance-kit')">
                <td><strong>B.1</strong></td>
                <td><strong>Maintenance Kit</strong></td>
                <td class="text-center"><strong>{{ number_format($total_maintenance_kit_before['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_maintenance_kit_before['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_maintenance_kit_before['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_maintenance_kit_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_maintenance_kit_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_maintenance_kit_current['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_maintenance_kit_before['atb'] + $total_maintenance_kit_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_maintenance_kit_before['apb'] + $total_maintenance_kit_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_maintenance_kit_before['saldo'] + $total_maintenance_kit_current['saldo'], 0, ',', '.') }}</strong></td>
            </tr>

            @foreach ($sums_current as $key => $category)
                @if ($category['jenis'] == 'Pemeliharaan' && $category['subJenis'] == 'MAINTENANCE KIT')
                    <tr class="collapse maintenance-kit">
                        <td>{{ implode('.', str_split($key)) }}</td>
                        <td>{{ ucwords(strtolower($category['nama'])) }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['atb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['apb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['saldo'], 0, ',', '.') }}</td>

                        <td class="text-center">{{ number_format($category['atb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($category['apb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($category['saldo'], 0, ',', '.') }}</td>

                        <td class="text-center">{{ number_format($sums_before[$key]['atb'] + $category['atb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['apb'] + $category['apb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['saldo'] + $category['saldo'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            @php
                $total_oil_lubricants_before = ['atb' => 0, 'apb' => 0, 'saldo' => 0];
                $total_oil_lubricants_current = ['atb' => 0, 'apb' => 0, 'saldo' => 0];

                foreach ($sums_current as $key => $category) {
                    if ($category['jenis'] == 'Pemeliharaan' && $category['subJenis'] == 'OIL & LUBRICANTS') {
                        $total_oil_lubricants_before['atb'] += $sums_before[$key]['atb'];
                        $total_oil_lubricants_before['apb'] += $sums_before[$key]['apb'];
                        $total_oil_lubricants_before['saldo'] += $sums_before[$key]['saldo'];

                        $total_oil_lubricants_current['atb'] += $category['atb'];
                        $total_oil_lubricants_current['apb'] += $category['apb'];
                        $total_oil_lubricants_current['saldo'] += $category['saldo'];
                    }
                }
            @endphp

            <tr class="collapse pemeliharaan subheader-row" onclick="toggleAccordion('oil-lubricants')">
                <td><strong>B.2</strong></td>
                <td><strong>Oil & Lubricants</strong></td>
                <td class="text-center"><strong>{{ number_format($total_oil_lubricants_before['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_oil_lubricants_before['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_oil_lubricants_before['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_oil_lubricants_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_oil_lubricants_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_oil_lubricants_current['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_oil_lubricants_before['atb'] + $total_oil_lubricants_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_oil_lubricants_before['apb'] + $total_oil_lubricants_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($total_oil_lubricants_before['saldo'] + $total_oil_lubricants_current['saldo'], 0, ',', '.') }}</strong></td>
            </tr>

            @foreach ($sums_current as $key => $category)
                @if ($category['jenis'] == 'Pemeliharaan' && $category['subJenis'] == 'OIL & LUBRICANTS')
                    <tr class="collapse oil-lubricants">
                        <td>{{ implode('.', str_split($key)) }}</td>
                        <td>{{ ucwords(strtolower($category['nama'])) }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['atb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['apb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['saldo'], 0, ',', '.') }}</td>

                        <td class="text-center">{{ number_format($category['atb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($category['apb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($category['saldo'], 0, ',', '.') }}</td>

                        <td class="text-center">{{ number_format($sums_before[$key]['atb'] + $category['atb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['apb'] + $category['apb'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($sums_before[$key]['saldo'] + $category['saldo'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            @foreach ($sums_current as $key => $category)
                @if ($key == 'B3')
                    <tr class="collapse pemeliharaan">
                        <td><strong>{{ $key }}</strong></td>
                        <td><strong>{{ $category['nama'] }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['atb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['apb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['saldo'], 0, ',', '.') }}</strong></td>

                        <td class="text-center"><strong>{{ number_format($category['atb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($category['apb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($category['saldo'], 0, ',', '.') }}</strong></td>

                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['atb'] + $category['atb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['apb'] + $category['apb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['saldo'] + $category['saldo'], 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            @endforeach

            @foreach ($sums_current as $key => $category)
                @if ($key == 'C1')
                    <tr class="header-row" onclick="toggleAccordion('material')">
                        <td><strong>II</strong></td>
                        <td><strong>MATERIAL</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['atb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['apb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['saldo'], 0, ',', '.') }}</strong></td>

                        <td class="text-center"><strong>{{ number_format($category['atb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($category['apb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($category['saldo'], 0, ',', '.') }}</strong></td>

                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['atb'] + $category['atb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['apb'] + $category['apb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['saldo'] + $category['saldo'], 0, ',', '.') }}</strong></td>
                    </tr>

                    <tr class="collapse material">
                        <td>{{ $key }}</td>
                        <td>{{ ucwords(strtolower($category['nama'])) }}</td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['atb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['apb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['saldo'], 0, ',', '.') }}</strong></td>

                        <td class="text-center"><strong>{{ number_format($category['atb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($category['apb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($category['saldo'], 0, ',', '.') }}</strong></td>

                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['atb'] + $category['atb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['apb'] + $category['apb'], 0, ',', '.') }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($sums_before[$key]['saldo'] + $category['saldo'], 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            @endforeach
        </tbody>

        <tfoot>
            @php
                $grand_total_before = ['atb' => 0, 'apb' => 0, 'saldo' => 0];
                $grand_total_current = ['atb' => 0, 'apb' => 0, 'saldo' => 0];

                foreach ($sums_current as $key => $category) {
                    // Sum up all previous month values
                    $grand_total_before['atb'] += $sums_before[$key]['atb'];
                    $grand_total_before['apb'] += $sums_before[$key]['apb'];
                    $grand_total_before['saldo'] += $sums_before[$key]['saldo'];

                    // Sum up all current month values
                    $grand_total_current['atb'] += $category['atb'];
                    $grand_total_current['apb'] += $category['apb'];
                    $grand_total_current['saldo'] += $category['saldo'];
                }
            @endphp

            <tr class="border-dark">
                <td class="text-center" colspan="2"><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ number_format($grand_total_before['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grand_total_before['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grand_total_before['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grand_total_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grand_total_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grand_total_current['saldo'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grand_total_before['atb'] + $grand_total_current['atb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grand_total_before['apb'] + $grand_total_current['apb'], 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grand_total_before['saldo'] + $grand_total_current['saldo'], 0, ',', '.') }}</strong></td>
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
    </script>
@endpush
