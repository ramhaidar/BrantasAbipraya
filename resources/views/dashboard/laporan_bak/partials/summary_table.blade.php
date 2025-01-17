@php
    $detailDataATB = collect($detailDataATB);
    $detailDataAPB = collect($detailDataAPB);
    $detailDataSaldo = collect($detailDataSaldo);
@endphp

<div class="mt-0 ibox-body table-responsive" style="overflow-x:auto;">

    @php
        // Penerimaan
        $total_net_atb_hutang_unit_alat = $detailDataATB->where('tipe', 'Hutang Unit Alat')->where('kode', '!=', 'C1')->sum('total_net');
        $total_net_atb_mutasi_proyek = $detailDataATB->where('tipe', 'Mutasi Proyek')->where('kode', '!=', 'C1')->sum('total_net');
        $total_net_atb_panjar_unit_alat = $detailDataATB->where('tipe', 'Panjar Unit Alat')->where('kode', '!=', 'C1')->sum('total_net');
        $total_net_atb_panjar_panjar_proyek = $detailDataATB->where('tipe', 'Panjar Proyek')->where('kode', '!=', 'C1')->sum('total_net');
        $totalPenerimaan = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_panjar_proyek;

        // Pengeluaran
        $total_net_apb_ex_unit_alat = $detailDataAPB->where('tipe', 'Hutang Unit Alat')->where('kode', '!=', 'C1')->sum('total_net');
        $total_net_apb_ex_panjar_unit_alat = $detailDataAPB->where('tipe', 'Panjar Unit Alat')->where('kode', '!=', 'C1')->sum('total_net');
        $total_net_apb_ex_panjar_proyek = $detailDataAPB->where('tipe', 'Panjar Proyek')->where('kode', '!=', 'C1')->sum('total_net');
        $total_net_apb_ex_mutasi_saldo = $detailDataAPB->where('tipe', 'Mutasi Proyek')->where('kode', '!=', 'C1')->sum('total_net');
        $totalPengeluaran = $total_net_apb_ex_unit_alat + $total_net_apb_ex_mutasi_saldo + $total_net_apb_ex_panjar_unit_alat + $total_net_apb_ex_panjar_proyek;

        // Saldo
        $total_net_saldo_ex_unit_alat = $detailDataSaldo->where('tipe', 'Hutang Unit Alat')->where('kode', '!=', 'C1')->sum('total_net') + $detailDataSaldo->where('tipe', 'Mutasi Proyek')->where('kode', '!=', 'C1')->sum('total_net');
        $total_net_saldo_ex_panjar_unit_alat = $detailDataSaldo->where('tipe', 'Panjar Unit Alat')->where('kode', '!=', 'C1')->sum('total_net');
        $total_net_saldo_ex_panjar_proyek = $detailDataSaldo->where('tipe', 'Panjar Proyek')->where('kode', '!=', 'C1')->sum('total_net');
        $totalSaldo = $total_net_saldo_ex_unit_alat + $total_net_saldo_ex_panjar_unit_alat + $total_net_saldo_ex_panjar_proyek;
    @endphp

    <table class="m-0 border-dark table table-bordered table-striped" id="table-data" style="width:100%">
        {{-- HEADER --}}
        <thead class="table-primary">
            <tr>
                <th class="align-middle text-center" rowspan="2">NO.</th>
                <th class="align-middle text-center" rowspan="2">U R A I A N</th>
                <th class="text-center" style="border-bottom: 1px solid black;" colspan="4">PENERIMAAN</th>
                <th class="align-middle text-center" rowspan="2">TOTAL PENERIMAAN</th>
                <th class="text-center" style="border-bottom: 1px solid black;" colspan="4">PENGELUARAN</th>
                <th class="align-middle text-center" rowspan="2">TOTAL PENGELUARAN</th>
                <th class="text-center" style="border-bottom: 1px solid black;" colspan="3">SALDO</th>
                <th class="align-middle text-center" rowspan="2">TOTAL SALDO</th>
            </tr>
            <tr>
                <th class="align-middle text-center">PEMBEL. HUTANG UNIT ALAT (PO)</th>
                <th class="align-middle text-center">MUTASI DARI PROYEK</th>
                <th class="align-middle text-center">PEMBEL. PANJAR UNIT ALAT</th>
                <th class="align-middle text-center">PEMBEL. PANJAR PROYEK</th>
                <th class="align-middle text-center">EX UNIT ALAT</th>
                <th class="align-middle text-center">EX PANJAR UNIT ALAT</th>
                <th class="align-middle text-center">EX PANJAR PROYEK</th>
                <th class="align-middle text-center">Mutasi Proyek EX</th>
                <th class="align-middle text-center">EX UNIT ALAT</th>
                <th class="align-middle text-center">EX PANJAR UNIT ALAT</th>
                <th class="align-middle text-center">EX PANJAR PROYEK</th>
            </tr>
        </thead>

        {{-- SUKU CADANG --}}
        <tbody class="border-dark">
            <tr class="header-row accordion-header" onclick="toggleAccordion('suku-cadang')">
                <td><strong>I</strong></td>
                <td><strong>SUKU CADANG</strong></td>
                <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_atb_panjar_panjar_proyek, 0, ',', '.') }}</td>

                <td class="text-center">Rp{{ number_format($totalPenerimaan, 0, ',', '.') }}</td>

                <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_proyek, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>

                <td class="text-center">Rp{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>

                <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>

                <td class="text-center">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</td>
            </tr>

            <!-- PERBAIKAN SECTION -->
            @php
                $perbaikanDataATB = $detailDataATB->where('suku_cadang', 'PERBAIKAN');

                $perbaikanDataAPB = $detailDataAPB->where('suku_cadang', 'PERBAIKAN');

                $perbaikanDataSaldo = $detailDataSaldo->where('suku_cadang', 'PERBAIKAN');
            @endphp
            <tr class="collapse suku-cadang subheader-row" onclick="toggleAccordion('perbaikan')">
                <td><strong>A</strong></td>
                <td><strong>PERBAIKAN</strong></td>
                <td class="text-center">Rp{{ number_format($perbaikanDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataATB->where('tipe', 'Panjar Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataATB->sum('total_net'), 0, ',', '.') }}</td>

                <td class="text-center">Rp{{ number_format($perbaikanDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataAPB->sum('total_net'), 0, ',', '.') }}</td>

                <td class="text-center">Rp{{ number_format($perbaikanDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $perbaikanDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net'), 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($perbaikanDataSaldo->sum('total_net'), 0, ',', '.') }}</td>
            </tr>

            @php
                $itemsPerbaikan = [
                    'A1' => 'Cabin',
                    'A2' => 'Engine System',
                    'A3' => 'Transmission System',
                    'A4' => 'Chassis & Swing Machinery',
                    'A5' => 'Differential System',
                    'A6' => 'Electrical System',
                    'A7' => 'Hydraulic / Pneumatic System',
                    'A8' => 'Steering System',
                    'A9' => 'Brake System',
                    'A10' => 'Suspension',
                    'A11' => 'Attachment',
                    'A12' => 'Undercarriage',
                    'A13' => 'Final Drive',
                    'A14' => 'Freight Cost',
                ];
            @endphp

            @foreach ($itemsPerbaikan as $kode => $description)
                @php
                    $subDataATB = $detailDataATB->where('suku_cadang', 'PERBAIKAN')->where('kode', $kode);

                    $total_net_atb_hutang_unit_alat = $subDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                    $total_net_atb_mutasi_proyek = $subDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_atb_panjar_unit_alat = $subDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_atb_panjar_proyek = $subDataATB->where('tipe', 'Panjar Proyek')->sum('total_net');
                    $total_net_atb_sub_total = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_proyek;

                    $subDataAPB = $detailDataAPB->where('suku_cadang', 'PERBAIKAN')->where('kode', $kode);

                    $total_net_apb_ex_unit_alat = $subDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                    $total_net_apb_ex_panjar_unit_alat = $subDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_apb_ex_panjar_proyek = $subDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net');
                    $total_net_apb_ex_mutasi_saldo = $subDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_apb_ex_sub_total = $total_net_apb_ex_unit_alat + $total_net_apb_ex_mutasi_saldo + $total_net_apb_ex_panjar_unit_alat + $total_net_apb_ex_panjar_proyek;

                    $subDataSaldo = $detailDataSaldo->where('suku_cadang', 'PERBAIKAN')->where('kode', $kode);

                    $total_net_saldo_ex_unit_alat = $subDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $subDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_saldo_ex_panjar_unit_alat = $subDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_saldo_ex_panjar_proyek = $subDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net');

                    $total_net_saldo_ex_sub_total = $total_net_saldo_ex_unit_alat + $total_net_saldo_ex_panjar_unit_alat + $total_net_saldo_ex_panjar_proyek;
                @endphp

                <tr class="collapse perbaikan">
                    <td>{{ $kode }}</td>
                    <td>{{ $description }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_sub_total, 0, ',', '.') }}</td>

                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_sub_total, 0, ',', '.') }}</td>

                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_sub_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <!-- PEMELIHARAAN SECTION -->
            <tr class="collapse suku-cadang subheader-row" onclick="toggleAccordion('pemeliharaan')">
                <td><strong>B</strong></td>
                <td><strong>PEMELIHARAAN</strong></td>

                @php
                    $total_net_atb_perbaikan_hutang_unit_alat = 0;
                    $total_net_atb_perbaikan_mutasi_proyek = 0;
                    $total_net_atb_perbaikan_panjar_unit_alat = 0;
                    $total_net_atb_perbaikan_panjar_panjar_proyek = 0;

                    foreach ($detailDataATB as $data) {
                        if ($data['suku_cadang'] == 'PEMELIHARAAN') {
                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                $total_net_atb_perbaikan_hutang_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                $total_net_atb_perbaikan_mutasi_proyek += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_atb_perbaikan_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_atb_perbaikan_panjar_panjar_proyek += $data['total_net'];
                            }
                        }
                    }

                    $total_net_apb_perbaikan_ex_unit_alat = 0;
                    $total_net_apb_perbaikan_ex_panjar_unit_alat = 0;
                    $total_net_apb_perbaikan_ex_panjar_proyek = 0;
                    $total_net_apb_perbaikan_ex_mutasi_saldo = 0;

                    foreach ($detailDataAPB as $data) {
                        if ($data['suku_cadang'] == 'PEMELIHARAAN') {
                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                $ata['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_apb_perbaikan_ex_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_apb_perbaikan_ex_panjar_proyek += $data['total_net'];
                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                $total_net_apb_perbaikan_ex_mutasi_saldo += $data['total_net'];
                            }
                        }
                    }

                    $total_net_saldo_perbaikan_ex_unit_alat = 0;
                    $total_net_saldo_perbaikan_ex_panjar_unit_alat = 0;
                    $total_net_saldo_perbaikan_ex_panjar_proyek = 0;

                    foreach ($detailDataSaldo as $data) {
                        if ($data['suku_cadang'] == 'PEMELIHARAAN') {
                            if ($data['tipe'] == 'Hutang Unit Alat' || $data['tipe'] == 'Mutasi Proyek') {
                                $total_net_saldo_perbaikan_ex_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_saldo_perbaikan_ex_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_saldo_perbaikan_ex_panjar_proyek += $data['total_net'];
                            }
                        }
                    }
                @endphp

                <td colspan="">Rp{{ number_format($total_net_atb_perbaikan_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_perbaikan_mutasi_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_perbaikan_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_perbaikan_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_perbaikan_hutang_unit_alat + $total_net_atb_perbaikan_mutasi_proyek + $total_net_atb_perbaikan_panjar_unit_alat + $total_net_atb_perbaikan_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                <td colspan="">Rp{{ number_format($total_net_apb_perbaikan_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_perbaikan_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_perbaikan_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_perbaikan_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_perbaikan_ex_unit_alat + $total_net_apb_perbaikan_ex_panjar_proyek + $total_net_apb_perbaikan_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->

                <td colspan="">Rp{{ number_format($total_net_saldo_perbaikan_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_perbaikan_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_perbaikan_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_perbaikan_ex_unit_alat + $total_net_saldo_perbaikan_ex_panjar_unit_alat + $total_net_saldo_perbaikan_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
            </tr>

            @php
                $maintenanceKitMapping = [
                    'B11' => 'Oil Filter',
                    'B12' => 'Fuel Filter',
                    'B13' => 'Air Filter',
                    'B14' => 'Hydraulic Filter',
                    'B15' => 'Transmission Filter',
                    'B16' => 'Differential Filter',
                ];

                $oilLubricantsMapping = [
                    'B21' => 'Engine Oil',
                    'B22' => 'Hydraulic Oil',
                    'B23' => 'Transmission Oil',
                    'B24' => 'Final Drive Oil',
                    'B25' => 'Swing & Damper Oil',
                    'B26' => 'Differential Oil',
                    'B27' => 'Grease',
                    'B28' => 'Brake & Power Steering Fluid',
                    'B29' => 'Coolant',
                ];
            @endphp

            <!-- Section for Maintenance Kit -->
            <tr class="collapse pemeliharaan subheader-row" onclick="toggleAccordion('maintenance-kit')">
                <td>B1</td>
                <td>Maintenance Kit</td>

                @php
                    $total_net_atb_maintenance_kit_hutang_unit_alat = 0;
                    $total_net_atb_maintenance_kit_mutasi_proyek = 0;
                    $total_net_atb_maintenance_kit_panjar_unit_alat = 0;
                    $total_net_atb_maintenance_kit_panjar_panjar_proyek = 0;

                    foreach ($detailDataATB as $data) {
                        if ($data['sumber'] == 'MAINTENANCE KIT') {
                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                $total_net_atb_maintenance_kit_hutang_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                $total_net_atb_maintenance_kit_mutasi_proyek += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_atb_maintenance_kit_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_atb_maintenance_kit_panjar_panjar_proyek += $data['total_net'];
                            }
                        }
                    }

                    $total_net_apb_maintenance_kit_ex_unit_alat = 0;
                    $total_net_apb_maintenance_kit_ex_panjar_unit_alat = 0;
                    $total_net_apb_maintenance_kit_ex_panjar_proyek = 0;
                    $total_net_apb_maintenance_kit_ex_mutasi_saldo = 0;

                    foreach ($detailDataAPB as $data) {
                        if ($data['sumber'] == 'MAINTENANCE KIT') {
                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                $total_net_apb_maintenance_kit_ex_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_apb_maintenance_kit_ex_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_apb_maintenance_kit_ex_panjar_proyek += $data['total_net'];
                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                $total_net_apb_maintenance_kit_ex_mutasi_saldo += $data['total_net'];
                            }
                        }
                    }

                    $total_net_saldo_maintenance_kit_ex_unit_alat = 0;
                    $total_net_saldo_maintenance_kit_ex_panjar_unit_alat = 0;
                    $total_net_saldo_maintenance_kit_ex_panjar_proyek = 0;

                    foreach ($detailDataSaldo as $data) {
                        if ($data['sumber'] == 'MAINTENANCE KIT') {
                            if ($data['tipe'] == 'Hutang Unit Alat' || $data['tipe'] == 'Mutasi Proyek') {
                                $total_net_saldo_maintenance_kit_ex_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_saldo_maintenance_kit_ex_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_saldo_maintenance_kit_ex_panjar_proyek += $data['total_net'];
                            }
                        }
                    }
                @endphp

                <td colspan="">Rp{{ number_format($total_net_atb_maintenance_kit_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_maintenance_kit_mutasi_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_maintenance_kit_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_maintenance_kit_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_maintenance_kit_hutang_unit_alat + $total_net_atb_maintenance_kit_mutasi_proyek + $total_net_atb_maintenance_kit_panjar_unit_alat + $total_net_atb_maintenance_kit_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                <td colspan="">Rp{{ number_format($total_net_apb_maintenance_kit_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_maintenance_kit_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_maintenance_kit_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_maintenance_kit_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_maintenance_kit_ex_unit_alat + $total_net_apb_maintenance_kit_ex_panjar_unit_alat + $total_net_apb_maintenance_kit_ex_panjar_proyek + $total_net_apb_maintenance_kit_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->

                <td colspan="">Rp{{ number_format($total_net_saldo_maintenance_kit_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_maintenance_kit_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_maintenance_kit_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_maintenance_kit_ex_unit_alat + $total_net_saldo_maintenance_kit_ex_panjar_unit_alat + $total_net_saldo_maintenance_kit_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
            </tr>

            @foreach ($maintenanceKitMapping as $kode => $description)
                @php
                    $subDataATB = $detailDataATB->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                    $total_net_atb_hutang_unit_alat = $subDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                    $total_net_atb_mutasi_proyek = $subDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_atb_panjar_unit_alat = $subDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_atb_panjar_proyek = $subDataATB->where('tipe', 'Panjar Proyek')->sum('total_net');
                    $total_net_atb_sub_total = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_proyek;

                    $subDataAPB = $detailDataAPB->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                    $total_net_apb_ex_unit_alat = $subDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                    $total_net_apb_ex_panjar_unit_alat = $subDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_apb_ex_panjar_proyek = $subDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net');
                    $total_net_apb_ex_mutasi_saldo = $subDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_apb_ex_sub_total = $total_net_apb_ex_unit_alat + $total_net_apb_ex_panjar_unit_alat + $total_net_apb_ex_panjar_proyek + $total_net_apb_ex_mutasi_saldo;

                    $subDataSaldo = $detailDataSaldo->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                    $total_net_saldo_ex_unit_alat = $subDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $subDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_saldo_ex_panjar_unit_alat = $subDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_saldo_ex_panjar_proyek = $subDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net');

                    $total_net_saldo_ex_sub_total = $total_net_saldo_ex_unit_alat + $total_net_saldo_ex_panjar_unit_alat + $total_net_saldo_ex_panjar_proyek;

                @endphp

                <tr class="collapse maintenance-kit">
                    <td>{{ $kode }}</td>
                    <td>{{ $description }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_sub_total, 0, ',', '.') }}</td>

                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_sub_total, 0, ',', '.') }}</td>

                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_sub_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <!-- Section for Oil & Lubricants -->
            <tr class="collapse pemeliharaan subheader-row" onclick="toggleAccordion('oil-lubricants')">
                <td>B2</td>
                <td>Oil & Lubricants</td>

                @php
                    $total_net_atb_oil_n_lubricants_hutang_unit_alat = 0;
                    $total_net_atb_oil_n_lubricants_mutasi_proyek = 0;
                    $total_net_atb_oil_n_lubricants_panjar_unit_alat = 0;
                    $total_net_atb_oil_n_lubricants_panjar_proyek = 0;

                    foreach ($detailDataATB as $data) {
                        if ($data['sumber'] == 'OIL & LUBRICANTS') {
                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                $total_net_atb_oil_n_lubricants_hutang_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                $total_net_atb_oil_n_lubricants_mutasi_proyek += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_atb_oil_n_lubricants_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_atb_oil_n_lubricants_panjar_proyek += $data['total_net'];
                            }
                        }
                    }

                    $total_net_apb_oil_n_lubricants_ex_unit_alat = 0;
                    $total_net_apb_oil_n_lubricants_ex_panjar_unit_alat = 0;
                    $total_net_apb_oil_n_lubricants_ex_panjar_proyek = 0;
                    $total_net_apb_oil_n_lubricants_ex_mutasi_saldo = 0;

                    foreach ($detailDataAPB as $data) {
                        if ($data['sumber'] == 'OIL & LUBRICANTS') {
                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                $total_net_apb_oil_n_lubricants_ex_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_apb_oil_n_lubricants_ex_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_apb_oil_n_lubricants_ex_panjar_proyek += $data['total_net'];
                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                $total_net_apb_oil_n_lubricants_ex_mutasi_saldo += $data['total_net'];
                            }
                        }
                    }

                    $total_net_saldo_oil_n_lubricants_ex_unit_alat = 0;
                    $total_net_saldo_oil_n_lubricants_ex_panjar_unit_alat = 0;
                    $total_net_saldo_oil_n_lubricants_ex_panjar_proyek = 0;

                    foreach ($detailDataSaldo as $data) {
                        if ($data['sumber'] == 'OIL & LUBRICANTS') {
                            if ($data['tipe'] == 'Hutang Unit Alat' || $data['tipe'] == 'Mutasi Proyek') {
                                $total_net_saldo_oil_n_lubricants_ex_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_saldo_oil_n_lubricants_ex_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_saldo_oil_n_lubricants_ex_panjar_proyek += $data['total_net'];
                            }
                        }
                    }
                @endphp

                <td colspan="">Rp{{ number_format($total_net_atb_oil_n_lubricants_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_oil_n_lubricants_mutasi_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_oil_n_lubricants_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_oil_n_lubricants_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_oil_n_lubricants_hutang_unit_alat + $total_net_atb_oil_n_lubricants_mutasi_proyek + $total_net_atb_oil_n_lubricants_panjar_unit_alat + $total_net_atb_oil_n_lubricants_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                <td colspan="">Rp{{ number_format($total_net_apb_oil_n_lubricants_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_oil_n_lubricants_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_oil_n_lubricants_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_oil_n_lubricants_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_oil_n_lubricants_ex_unit_alat + $total_net_apb_oil_n_lubricants_ex_panjar_unit_alat + $total_net_apb_oil_n_lubricants_ex_panjar_proyek + $total_net_apb_oil_n_lubricants_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->

                <td colspan="">Rp{{ number_format($total_net_saldo_oil_n_lubricants_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_oil_n_lubricants_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_oil_n_lubricants_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_oil_n_lubricants_ex_unit_alat + $total_net_saldo_oil_n_lubricants_ex_panjar_unit_alat + $total_net_saldo_oil_n_lubricants_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

            </tr>

            @foreach ($oilLubricantsMapping as $kode => $description)
                @php
                    $subDataATB = $detailDataATB->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                    $total_net_atb_hutang_unit_alat = $subDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                    $total_net_atb_mutasi_proyek = $subDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_atb_panjar_unit_alat = $subDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_atb_panjar_proyek = $subDataATB->where('tipe', 'Panjar Proyek')->sum('total_net');
                    $total_net_atb_sub_total = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_proyek;

                    $subDataAPB = $detailDataAPB->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                    $total_net_apb_ex_unit_alat = $subDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                    $total_net_apb_ex_mutasi_saldo = $subDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_apb_ex_panjar_unit_alat = $subDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_apb_ex_panjar_proyek = $subDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net');
                    $total_net_apb_sub_total = $total_net_apb_ex_unit_alat + $total_net_apb_ex_panjar_unit_alat + $total_net_apb_ex_panjar_proyek + $total_net_apb_ex_mutasi_saldo;

                    $subDataSaldo = $detailDataSaldo->where('suku_cadang', 'PEMELIHARAAN')->where('kode', $kode);

                    $total_net_saldo_ex_unit_alat = $subDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $subDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_saldo_ex_panjar_unit_alat = $subDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_saldo_ex_panjar_proyek = $subDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net');

                    $total_net_saldo_sub_total = $total_net_saldo_ex_unit_alat + $total_net_saldo_ex_panjar_unit_alat + $total_net_saldo_ex_panjar_proyek;
                @endphp

                <tr class="collapse oil-lubricants">
                    <td>{{ $kode }}</td>
                    <td>{{ $description }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_sub_total, 0, ',', '.') }}</td>

                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_sub_total, 0, ',', '.') }}</td>

                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_sub_total, 0, ',', '.') }}</td>

                </tr>
            @endforeach

            <!-- Section for Tyre -->
            <tr class="collapse pemeliharaan subheader-row" onclick="#">
                <td>B3</td>
                <td>Tyre</td>

                @php
                    $total_net_atb_tyre_hutang_unit_alat = 0;
                    $total_net_atb_tyre_mutasi_proyek = 0;
                    $total_net_atb_tyre_panjar_unit_alat = 0;
                    $total_net_atb_tyre_panjar_proyek = 0;

                    foreach ($detailDataATB as $data) {
                        if ($data['kode'] == 'B3') {
                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                $total_net_atb_tyre_hutang_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                $total_net_atb_tyre_mutasi_proyek += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_atb_tyre_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_atb_tyre_panjar_proyek += $data['total_net'];
                            }
                        }
                    }

                    $total_net_apb_tyre_hutang_unit_alat = 0;
                    $total_net_apb_tyre_panjar_unit_alat = 0;
                    $total_net_apb_tyre_panjar_proyek = 0;
                    $total_net_apb_tyre_mutasi_saldo = 0;

                    foreach ($detailDataAPB as $data) {
                        if ($data['kode'] == 'B3') {
                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                $total_net_apb_tyre_hutang_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_apb_tyre_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_apb_tyre_panjar_proyek += $data['total_net'];
                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                $total_net_apb_tyre_mutasi_saldo += $data['total_net'];
                            }
                        }
                    }

                    $total_net_saldo_tyre_hutang_unit_alat = 0;
                    $total_net_saldo_tyre_panjar_unit_alat = 0;
                    $total_net_saldo_tyre_panjar_proyek = 0;

                    foreach ($detailDataSaldo as $data) {
                        if ($data['kode'] == 'B3') {
                            if ($data['tipe'] == 'Hutang Unit Alat' || $data['tipe'] == 'Mutasi Proyek') {
                                $total_net_saldo_tyre_hutang_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_saldo_tyre_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_saldo_tyre_panjar_proyek += $data['total_net'];
                            }
                        }
                    }
                @endphp

                <td colspan="">Rp{{ number_format($total_net_atb_tyre_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_tyre_mutasi_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_tyre_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_tyre_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_tyre_hutang_unit_alat + $total_net_atb_tyre_mutasi_proyek + $total_net_atb_tyre_panjar_unit_alat + $total_net_atb_tyre_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                <td colspan="">Rp{{ number_format($total_net_apb_tyre_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_tyre_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_tyre_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_tyre_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_tyre_hutang_unit_alat + $total_net_apb_tyre_panjar_unit_alat + $total_net_apb_tyre_panjar_proyek + $total_net_apb_tyre_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->

                <td colspan="">Rp{{ number_format($total_net_saldo_tyre_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_tyre_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_tyre_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_tyre_hutang_unit_alat + $total_net_saldo_tyre_panjar_unit_alat + $total_net_saldo_tyre_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

            </tr>

            <!-- MATERIAL SECTION -->
            @php
                $materialMapping = [
                    'C1' => 'Workshop Material',
                ];
            @endphp

            <tr class="header-row accordion-header" onclick="toggleAccordion('material')">
                <td><strong>II</strong></td>
                <td><strong>MATERIAL</strong></td>

                @php
                    $total_net_atb_workshop_hutang_unit_alat = 0;
                    $total_net_atb_workshop_mutasi_proyek = 0;
                    $total_net_atb_workshop_panjar_unit_alat = 0;
                    $total_net_atb_workshop_panjar_panjar_proyek = 0;

                    foreach ($detailDataATB as $data) {
                        if ($data['kode'] == 'C1') {
                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                $total_net_atb_workshop_hutang_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                $total_net_atb_workshop_mutasi_proyek += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_atb_workshop_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_atb_workshop_panjar_panjar_proyek += $data['total_net'];
                            }
                        }
                    }

                    $total_net_apb_workshop_ex_unit_alat = 0;
                    $total_net_apb_workshop_ex_panjar_unit_alat = 0;
                    $total_net_apb_workshop_ex_panjar_proyek = 0;
                    $total_net_apb_workshop_ex_mutasi_saldo = 0;

                    foreach ($detailDataAPB as $data) {
                        if ($data['kode'] == 'C1') {
                            if ($data['tipe'] == 'Hutang Unit Alat') {
                                $total_net_apb_workshop_ex_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_apb_workshop_ex_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_apb_workshop_ex_panjar_proyek += $data['total_net'];
                            } elseif ($data['tipe'] == 'Mutasi Proyek') {
                                $total_net_apb_workshop_ex_mutasi_saldo += $data['total_net'];
                            }
                        }
                    }

                    $total_net_saldo_workshop_ex_unit_alat = 0;
                    $total_net_saldo_workshop_ex_panjar_unit_alat = 0;
                    $total_net_saldo_workshop_ex_panjar_proyek = 0;

                    foreach ($detailDataSaldo as $data) {
                        if ($data['kode'] == 'C1') {
                            if ($data['tipe'] == 'Hutang Unit Alat' || $data['tipe'] == 'Mutasi Proyek') {
                                $total_net_saldo_workshop_ex_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Unit Alat') {
                                $total_net_saldo_workshop_ex_panjar_unit_alat += $data['total_net'];
                            } elseif ($data['tipe'] == 'Panjar Proyek') {
                                $total_net_saldo_workshop_ex_panjar_proyek += $data['total_net'];
                            }
                        }
                    }
                @endphp

                <td colspan="">Rp{{ number_format($total_net_atb_workshop_hutang_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_workshop_mutasi_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_workshop_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_workshop_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_atb_workshop_hutang_unit_alat + $total_net_atb_workshop_mutasi_proyek + $total_net_atb_workshop_panjar_unit_alat + $total_net_atb_workshop_panjar_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->

                <td colspan="">Rp{{ number_format($total_net_apb_workshop_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_workshop_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_workshop_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_workshop_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_apb_workshop_ex_unit_alat + $total_net_apb_workshop_ex_panjar_unit_alat + $total_net_apb_workshop_ex_panjar_proyek + $total_net_apb_workshop_ex_mutasi_saldo, 0, ',', '.') }}</td> <!-- Penerimaan -->

                <td colspan="">Rp{{ number_format($total_net_saldo_workshop_ex_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_workshop_ex_panjar_unit_alat, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_workshop_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
                <td colspan="">Rp{{ number_format($total_net_saldo_workshop_ex_unit_alat + $total_net_saldo_workshop_ex_panjar_unit_alat + $total_net_saldo_workshop_ex_panjar_proyek, 0, ',', '.') }}</td> <!-- Penerimaan -->
            </tr>

            @foreach ($materialMapping as $kode => $description)
                @php
                    $subDataATB = $detailDataATB->where('kode', $kode);

                    $total_net_atb_hutang_unit_alat = $subDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                    $total_net_atb_mutasi_proyek = $subDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_atb_panjar_unit_alat = $subDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_atb_panjar_proyek = $subDataATB->where('tipe', 'Panjar Proyek')->sum('total_net');

                    $subDataAPB = $detailDataAPB->where('kode', $kode);

                    $total_net_apb_ex_unit_alat = $subDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                    $total_net_apb_ex_panjar_unit_alat = $subDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_apb_ex_panjar_proyek = $subDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net');
                    $total_net_apb_ex_mutasi_saldo = $subDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net');

                    $subDataSaldo = $detailDataSaldo->where('kode', $kode);

                    // $total_net_saldo_ex_unit_alat = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek - $total_net_apb_ex_unit_alat;
                    $total_net_saldo_ex_unit_alat = $subDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $subDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net');
                    $total_net_saldo_ex_panjar_unit_alat = $subDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                    $total_net_saldo_ex_panjar_proyek = $subDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net');

                @endphp

                <tr class="collapse material">
                    <td>{{ $kode }}</td>
                    <td>{{ $description }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_proyek, 0, ',', '.') }}</td>

                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_apb_ex_unit_alat + $total_net_apb_ex_panjar_unit_alat + $total_net_apb_ex_panjar_proyek + $total_net_apb_ex_mutasi_saldo, 0, ',', '.') }}</td>

                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                    <td class="text-center">Rp{{ number_format($total_net_saldo_ex_unit_alat + $total_net_saldo_ex_panjar_unit_alat + $total_net_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            @php
                $total_net_atb_hutang_unit_alat = $detailDataATB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                $total_net_atb_mutasi_proyek = $detailDataATB->where('tipe', 'Mutasi Proyek')->sum('total_net');
                $total_net_atb_panjar_unit_alat = $detailDataATB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                $total_net_atb_panjar_panjar_proyek = $detailDataATB->where('tipe', 'Panjar Proyek')->sum('total_net');

                $totalPenerimaan = $total_net_atb_hutang_unit_alat + $total_net_atb_mutasi_proyek + $total_net_atb_panjar_unit_alat + $total_net_atb_panjar_panjar_proyek;

                $total_pengeluaran_ex_unit_alat = $detailDataAPB->where('tipe', 'Hutang Unit Alat')->sum('total_net');
                $total_pengeluaran_ex_panjar_unit_alat = $detailDataAPB->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                $total_pengeluaran_ex_panjar_proyek = $detailDataAPB->where('tipe', 'Panjar Proyek')->sum('total_net');
                $total_pengeluaran_ex_mutasi_proyek = $detailDataAPB->where('tipe', 'Mutasi Proyek')->sum('total_net');

                $totalPengeluaran = $total_pengeluaran_ex_unit_alat + $total_pengeluaran_ex_mutasi_proyek + $total_pengeluaran_ex_panjar_unit_alat + $total_pengeluaran_ex_panjar_proyek;

                $total_saldo_ex_unit_alat = $detailDataSaldo->where('tipe', 'Hutang Unit Alat')->sum('total_net') + $detailDataSaldo->where('tipe', 'Mutasi Proyek')->sum('total_net');
                $total_saldo_ex_panjar_unit_alat = $detailDataSaldo->where('tipe', 'Panjar Unit Alat')->sum('total_net');
                $total_saldo_ex_panjar_proyek = $detailDataSaldo->where('tipe', 'Panjar Proyek')->sum('total_net');

                // $total_saldo_ex_unit_alat = $total_net_atb_hutang_unit_alat - $total_pengeluaran_ex_unit_alat;
                // $total_saldo_ex_panjar_unit_alat = $total_net_atb_panjar_unit_alat - $total_pengeluaran_ex_panjar_unit_alat;
                // $total_saldo_ex_panjar_proyek = $total_net_atb_panjar_panjar_proyek - $total_pengeluaran_ex_panjar_proyek;

                $totalSaldo = $total_saldo_ex_unit_alat + $total_saldo_ex_panjar_unit_alat + $total_saldo_ex_panjar_proyek;
            @endphp

            <style>
                .total-row td {
                    background-color: lightblue;
                }
            </style>
            <!-- Footer TOTAL -->
            <tr class="total-row border-dark table-bordered table-striped">
                <td></td>
                <td>TOTAL</td>
                <!-- Tampilkan total penerimaan -->
                <td class="text-center">Rp{{ number_format($total_net_atb_hutang_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_atb_mutasi_proyek, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_atb_panjar_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_net_atb_panjar_panjar_proyek, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                <!-- Tampilkan total pengeluaran -->
                <td class="text-center">Rp{{ number_format($total_pengeluaran_ex_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_pengeluaran_ex_mutasi_proyek, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_pengeluaran_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_pengeluaran_ex_panjar_proyek, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                <!-- Tampilkan total saldo -->
                <td class="text-center">Rp{{ number_format($total_saldo_ex_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_saldo_ex_panjar_unit_alat, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($total_saldo_ex_panjar_proyek, 0, ',', '.') }}</td>
                <td class="text-center">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>
