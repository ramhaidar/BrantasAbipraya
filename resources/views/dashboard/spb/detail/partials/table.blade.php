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
    </style>
@endpush

<div class="ibox-body ms-0 ps-0 table-responsive">
    <form action="#" method="POST">
        @csrf
        <table class="m-0 table table-bordered table-striped" id="table-data">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">Nama Alat</th>
                    <th class="text-center">Kode Alat</th>
                    <th class="text-center">Sparepart</th>
                    <th class="text-center">Part Number</th>
                    <th class="text-center">Merk</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Jumlah Harga</th>
                    <th class="text-center">Supplier</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sparepartGroups = [];
                    foreach ($rkb->linkAlatDetailRkbs as $detail1) {
                        foreach ($detail1->linkRkbDetails as $detail2) {
                            $sparepartName = $detail2->detailRkbUrgent->masterDataSparepart->nama ?? $detail2->detailRkbGeneral->masterDataSparepart->nama;
                            $satuan = $detail2->detailRkbUrgent->satuan ?? $detail2->detailRkbGeneral->satuan;

                            // Kunci grup berdasarkan nama sparepart dan satuan
                            $groupKey = $sparepartName . '_' . $satuan;

                            if (!isset($sparepartGroups[$groupKey])) {
                                $sparepartGroups[$groupKey] = [];
                            }

                            $sparepartGroups[$groupKey][] = [
                                'alat' => $detail1->masterDataAlat,
                                'detail' => $detail2,
                                'satuan' => $satuan,
                            ];
                        }
                    }
                @endphp

                @foreach ($sparepartGroups as $sparepartName => $group)
                    @php
                        $rowCount = count($group);
                    @endphp
                    @foreach ($group as $index => $data)
                        <tr>
                            <td class="text-center">{{ $data['alat']->jenis_alat }}</td>
                            <td class="text-center">{{ $data['alat']->kode_alat }}</td>
                            <td class="text-center">{{ $sparepartName }}</td>
                            <td class="text-center">
                                {{ $data['detail']->detailRkbUrgent->masterDataSparepart->part_number ?? $data['detail']->detailRkbGeneral->masterDataSparepart->part_number }}
                            </td>
                            <td class="text-center">
                                {{ $data['detail']->detailRkbUrgent->masterDataSparepart->merk ?? $data['detail']->detailRkbGeneral->masterDataSparepart->merk }}
                            </td>
                            <td class="text-center">
                                {{ $data['detail']->detailRkbUrgent->quantity_approved ?? $data['detail']->detailRkbGeneral->quantity_approved }}
                            </td>
                            @if ($index === 0)
                                <td class="text-center" rowspan="{{ $rowCount }}">
                                    {{ $data['detail']->detailRkbUrgent->satuan ?? $data['detail']->detailRkbGeneral->satuan }}
                                </td>
                                <td class="text-center" rowspan="{{ $rowCount }}">
                                    <input class="form-control text-center bg-warning-subtle h-100 d-flex align-items-center justify-content-center" name="harga[{{ $data['detail']->id }}]" type="text" value="Rp 0" maxlength="-1" required>
                                </td>
                                <td class="text-center" rowspan="{{ $rowCount }}">
                                    <input class="form-control text-center h-100 d-flex align-items-center justify-content-center" type="text" value="Rp 0" readonly>
                                </td>
                                <td class="text-center" rowspan="{{ $rowCount }}">
                                    <select class="form-select supplier-select" name="supplier[{{ $data['detail']->id }}]">
                                        <option value="" disabled selected>Pilih Supplier</option>
                                        @foreach ($supplier as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            @else
                                <!-- Tambahkan kolom dummy untuk mengisi tempat kolom rowspan -->
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>

        </table>
    </form>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            var table = $('#table-data').DataTable({
                paginate: false,
                ordering: false,
                order: [],
                searching: false,
            });

            // Inisialisasi Select2 untuk dropdown supplier
            $('.supplier-select').select2({
                placeholder: "Pilih Supplier",
                width: '100%',
            });

            // Fungsi untuk format Rupiah
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            // Fungsi untuk menghapus format Rupiah menjadi angka biasa
            function unformatRupiah(rupiah) {
                return parseInt(rupiah.replace(/[^\d]/g, '')) || 0;
            }

            $(document).on('focus', 'input[name^="harga"]', function() {
                // Ketika user fokus pada inputan, ubah kembali ke format angka
                var value = $(this).val();
                $(this).val(unformatRupiah(value));
            });

            $(document).on('blur', 'input[name^="harga"]', function() {
                // Ketika user keluar dari inputan, ubah ke format Rupiah
                var hargaInput = $(this);
                var harga = unformatRupiah(hargaInput.val());

                // Temukan baris induk (baris yang memiliki rowspan)
                var row = hargaInput.closest('tr');

                // Ambil semua baris dalam grup yang memiliki rowspan
                var groupRows = [];
                var startRow = row.index(); // Indeks baris awal
                var rowSpan = parseInt(row.find('td:nth-child(7)').attr('rowspan')) || 1; // Ambil rowspan (kolom Satuan)
                for (var i = 0; i < rowSpan; i++) {
                    groupRows.push($('#table-data tbody tr').eq(startRow + i)); // Tambahkan baris ke grup
                }

                // Hitung total quantity dan jumlah harga
                var totalQuantity = 0;
                groupRows.forEach(function(groupRow) {
                    var quantity = parseFloat(groupRow.find('td:nth-child(6)').text()) || 0; // Kolom Quantity
                    totalQuantity += quantity;
                });

                // Hitung jumlah harga total
                var jumlahHarga = harga * totalQuantity;

                // Update nilai harga dan jumlah harga
                hargaInput.val(formatRupiah(harga)); // Format harga
                row.find('td:nth-child(9) input').val(formatRupiah(jumlahHarga)); // Format jumlah harga
            });

        });
    </script>
@endpush
