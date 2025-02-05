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

        #table-data th:nth-child(9),
        #table-data th:nth-child(10),
        #table-data td:nth-child(9),
        #table-data td:nth-child(10) {
            min-width: 10dvw;
            width: 10dvw;
        }

        .currency-value {
            text-align: right !important;
            padding-right: 10px !important;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0">
    <form id="detailSpbForm" action="{{ route('spb.detail.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input name="id_rkb" type="hidden" value="{{ $TableData->first()->id }}">
        <input id="spb_addendum_input" name="spb_addendum_id" type="hidden">

        @php
            $sparepartGroups = [];
            $totalItems = 0;
            $rkb = $TableData->first(); // Get the RKB model from paginator
            foreach ($rkb->linkAlatDetailRkbs as $detail1) {
                foreach ($detail1->linkRkbDetails as $detail2) {
                    $remainder = $detail2->detailRkbUrgent?->quantity_remainder ?? ($detail2->detailRkbGeneral?->quantity_remainder ?? 0);

                    if ($remainder <= 0) {
                        continue;
                    }
                    $totalItems++;

                    $sparepartName = $detail2->detailRkbUrgent->masterDataSparepart->nama ?? $detail2->detailRkbGeneral->masterDataSparepart->nama;
                    $satuan = $detail2->detailRkbUrgent->satuan ?? $detail2->detailRkbGeneral->satuan;
                    if (isset($detail2->detailRkbGeneral->kategoriSparepart)) {
                        $kategori = $detail2->detailRkbGeneral->kategoriSparepart->kode . ': ' . $detail2->detailRkbGeneral->kategoriSparepart->nama;
                    }
                    if (isset($detail2->detailRkbUrgent->kategoriSparepart)) {
                        $kategori = $detail2->detailRkbUrgent->kategoriSparepart->kode . ': ' . $detail2->detailRkbUrgent->kategoriSparepart->nama;
                    }

                    $groupKey = $sparepartName;

                    if (!isset($sparepartGroups[$groupKey])) {
                        $sparepartGroups[$groupKey] = [];
                    }

                    $sparepartGroups[$groupKey][] = [
                        'alat' => $detail1->masterDataAlat,
                        'detail' => $detail2,
                        'satuan' => $satuan,
                        'kategori' => $kategori,
                        'alat_detail_id' => $detail1->id,
                    ];
                }
            }
        @endphp

        {{-- <div class="row mb-3 ps-3"> --}}
        <div class="row p-0 m-0">
            <div class="col-md-4 p-0 m-0 pb-3">
                <label class="form-label">Pilih Supplier</label>
                <select class="form-select supplier-select-main" id="supplier_main" name="supplier_main" {{ $totalItems === 0 ? 'disabled' : '' }}>
                    <option value="" disabled selected>Pilih Supplier</option>
                    @foreach ($supplier as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
                @if ($totalItems === 0)
                    <small class="text-danger">Tidak ada item yang tersedia untuk pembuatan SPB</small>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="m-0 table table-bordered table-hover" id="table-data">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">Nama Alat</th>
                        <th class="text-center">Kode Alat</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Sparepart Requested</th>
                        <th class="text-center">Sparepart PO</th>
                        <th class="text-center">Quantity<br>Sisa</th>
                        <th class="text-center">Quantity<br>PO</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Jumlah Harga</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($sparepartGroups as $sparepartName => $group)
                        @foreach ($group as $index => $data)
                            <tr>
                                <td class="text-center">{{ $data['alat']->jenis_alat }}</td>
                                <td class="text-center">{{ $data['alat']->kode_alat }}</td>
                                <td class="text-center">{{ $data['kategori'] }}</td>
                                <td class="text-center">
                                    @php
                                        $sparepart = $data['detail']->detailRkbUrgent?->masterDataSparepart ?? $data['detail']->detailRkbGeneral?->masterDataSparepart;
                                    @endphp
                                    {{ $sparepartName }} - {{ $sparepart->part_number ?? '-' }} -
                                    {{ $sparepart->merk ?? '-' }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $kategoriId = $data['detail']->detailRkbUrgent ? $data['detail']->detailRkbUrgent->id_kategori_sparepart_sparepart : $data['detail']->detailRkbGeneral->id_kategori_sparepart_sparepart;
                                    @endphp
                                    <select class="form-select sparepart-select" id="sparepart-{{ $data['detail']->id }}" name="sparepart[{{ $data['detail']->id }}]" data-kategori="{{ $kategoriId }}" required disabled>
                                        <option value="" selected disabled>Pilih Sparepart</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    {{ $data['detail']->detailRkbUrgent->quantity_remainder ?? $data['detail']->detailRkbGeneral->quantity_remainder }}
                                </td>
                                <td class="text-center">
                                    <input class="form-control text-center" id="qty-{{ $data['detail']->id }}" name="qty[{{ $data['detail']->id }}]" type="number" value="0" min="0" max="{{ $data['detail']->detailRkbUrgent->quantity_remainder ?? $data['detail']->detailRkbGeneral->quantity_remainder }}" required disabled> <!-- Add disabled here -->
                                </td>
                                <td class="text-center">
                                    {{ $data['detail']->detailRkbUrgent->satuan ?? $data['detail']->detailRkbGeneral->satuan }}
                                    <input name="satuan[{{ $data['detail']->id }}]" type="hidden" value="{{ $data['detail']->detailRkbUrgent->satuan ?? $data['detail']->detailRkbGeneral->satuan }}">
                                </td>
                                <td class="text-center">
                                    <input class="form-control text-center h-100 d-flex align-items-center justify-content-center" name="harga[{{ $data['detail']->id }}]" type="text" value="0" maxlength="-1" required disabled>
                                </td>
                                <td class="text-center">
                                    <input class="form-control text-center bg-secondary h-100 d-flex align-items-center justify-content-center" type="text" value="0" readonly>
                                </td>
                                <input name="alat_detail_id[{{ $data['detail']->id }}]" type="hidden" value="{{ $data['alat_detail_id'] }}">
                                <input name="link_rkb_detail_id[{{ $data['detail']->id }}]" type="hidden" value="{{ $data['detail']->id }}">
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="10">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada sparepart yang tersedia untuk pembuatan PO
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-primary">
                    <tr>
                        <th class="ps-4" style="text-align: left;" colspan="8">Jumlah</th>
                        <th class="currency-value" id="totalHarga">0</th>
                        <th class="currency-value" id="totalJumlahHarga">0</th>
                        {{-- <th></th> --}}
                    </tr>
                    <tr>
                        <th class="ps-4" style="text-align: left;" colspan="9">PPN 11%</th>
                        <th class="currency-value" id="ppn11">0</th>
                        {{-- <th></th> --}}
                    </tr>
                    <tr>
                        <th class="ps-4" style="text-align: left;" colspan="9">Grand Total</th>
                        <th class="currency-value" id="grandTotal">0</th>
                        {{-- <th></th> --}}
                    </tr>
                </tfoot>
            </table>
    </form>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            // Select2 initialization
            $('#supplier_main, .sparepart-select').select2({
                placeholder: 'Pilih Supplier',
                width: '100%',
                allowClear: true
            });

            // Set column widths for specific columns
            const $table = $('#table-data');
            const $headers = $table.find('thead th');
            const textsToCheck = ['Quantity PO', 'Satuan', 'Harga', 'Jumlah Harga'];

            $headers.each(function(index) {
                const headerText = $(this).text().trim();
                if (textsToCheck.includes(headerText)) {
                    $table.find('tbody tr').each(function() {
                        $(this).find('td').eq(index).css('width', '1%');
                    });
                }
            });

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            function unformatRupiah(rupiah) {
                return parseInt(rupiah.replace(/[^\d]/g, '')) || 0;
            }

            function updateJumlahHarga(row) {
                const harga = unformatRupiah(row.find('input[name^="harga"]').val());
                const quantity = parseFloat(row.find('input[name^="qty"]').val()) || 0;
                const jumlahHarga = harga * quantity;
                row.find('td:nth-child(10) input').val(formatRupiah(jumlahHarga));
                updateTotalFooter();
            }

            function updateTotalFooter() {
                let totalHarga = 0;
                let totalJumlahHarga = 0;

                $('input[name^="harga"]').each(function() {
                    const row = $(this).closest('tr');
                    const harga = unformatRupiah($(this).val());
                    const quantity = parseFloat(row.find('input[name^="qty"]').val()) || 0;
                    const jumlahHarga = harga * quantity;

                    totalHarga += harga;
                    totalJumlahHarga += jumlahHarga;
                });

                const ppn11 = totalJumlahHarga * 0.11;
                const grandTotal = totalJumlahHarga + ppn11;

                $('#totalHarga').text(formatRupiah(totalHarga));
                $('#totalJumlahHarga').text(formatRupiah(totalJumlahHarga));
                $('#ppn11').text(formatRupiah(ppn11));
                $('#grandTotal').text(formatRupiah(grandTotal));
            }

            // Event handler for harga input
            $(document).on('blur', 'input[name^="harga"]', function() {
                const row = $(this).closest('tr');
                const harga = unformatRupiah($(this).val());
                $(this).val(formatRupiah(harga));
                updateJumlahHarga(row);
            });

            // Event handler for quantity input
            $(document).on('input', 'input[name^="qty"]', function() {
                const row = $(this).closest('tr');
                const max = parseInt($(this).attr('max'));
                let val = parseInt($(this).val()) || 0;

                if (val > max) {
                    alert('Quantity PO tidak boleh melebihi Quantity Sisa');
                    $(this).val(max);
                    val = max;
                }

                if (val < 0) {
                    $(this).val(0);
                    val = 0;
                }

                updateJumlahHarga(row);
            });

            // Add event handler for sparepart select change
            $(document).on('change', '.sparepart-select', function() {
                const row = $(this).closest('tr');
                const qtyInput = row.find('input[name^="qty"]');
                const hargaInput = row.find('input[name^="harga"]');

                if ($(this).val()) {
                    qtyInput.prop('disabled', false);
                    hargaInput.prop('disabled', false);
                } else {
                    qtyInput.prop('disabled', true).val(0);
                    hargaInput.prop('disabled', true).val('0');
                    updateJumlahHarga(row);
                }
            });

            // Initialize sparepart selects with Select2
            $('.sparepart-select').select2({
                placeholder: 'Pilih Sparepart',
                width: '100%',
                allowClear: true
            });

            updateTotalFooter();
        });
    </script>

    <script>
        $('.supplier-select-main').on('change', function() {
            let supplierId = $(this).val();

            if (!supplierId) {
                // Clear semua sparepart dropdown jika tidak ada supplier dipilih
                $('.sparepart-select').each(function() {
                    $(this).empty().append('<option value="" selected disabled>Pilih Sparepart</option>');
                    $(this).prop('disabled', true).trigger('change');
                });
                return;
            }

            // Enable sparepart dropdowns
            $('.sparepart-select').prop('disabled', false);

            // Add loading overlay to body instead of table 
            $('body').append(`
<div class="loading-overlay">
<div class="spinner-border text-primary" role="status">
<span class="visually-hidden">Loading...</span>
</div>
</div>
`);

            // Fetch spareparts dari supplier yang dipilih
            $.ajax({
                url: "{{ route('spb.detail.getSparepart', ':supplierId') }}".replace(':supplierId',
                    supplierId),
                type: 'GET',
                success: function(response) {
                    // console.log(response);
                    // Loop setiap dropdown sparepart
                    $('.sparepart-select').each(function() {
                        let $select = $(this);
                        let kategoriId = $select.data('kategori');

                        $select.empty().append(
                            '<option value="" selected disabled>Pilih Sparepart</option>');

                        // Filter sparepart berdasarkan kategori
                        let filteredSpareparts = response.master_data_spareparts.filter(function(
                            sparepart) {
                            return sparepart.id_kategori_sparepart == kategoriId;
                        });

                        // Tambahkan opsi yang sudah difilter
                        filteredSpareparts.forEach(function(sparepart) {
                            $select.append(new Option(
                                `${sparepart.nama} - ${sparepart.merk}`,
                                sparepart.id
                            ));
                        });

                        $select.trigger('change');
                    });
                },
                error: function() {
                    alert('Gagal memuat data sparepart');
                },
                complete: function() {
                    $('.loading-overlay').remove();
                }
            });
        });
    </script>

    @include('scripts.adjustTableColumnWidthByHeaderText')
@endpush
