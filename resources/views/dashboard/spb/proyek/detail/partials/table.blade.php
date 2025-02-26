@push('styles_3')
    @include('styles.tables')
@endpush

@php
    $headers = [
        [
            'title' => 'Nama Alat',
            'filterId' => 'jenis-alat',
            'paramName' => 'jenis_alat',
            'filter' => true,
        ],
        [
            'title' => 'Kode Alat',
            'filterId' => 'kode-alat',
            'paramName' => 'kode_alat',
            'filter' => true,
        ],
        [
            'title' => 'Kategori',
            'filterId' => 'kategori',
            'paramName' => 'kategori',
            'filter' => true,
        ],
        [
            'title' => 'Sparepart PO',
            'filterId' => 'sparepart',
            'paramName' => 'sparepart',
            'filter' => true,
        ],
        [
            'title' => 'Merk',
            'filterId' => 'merk',
            'paramName' => 'merk',
            'filter' => true,
        ],
        [
            'title' => 'Supplier',
            'filterId' => 'supplier',
            'paramName' => 'supplier',
            'filter' => true,
        ],
        [
            'title' => 'Quantity PO',
            'filterId' => 'quantity-po',
            'paramName' => 'quantity_po',
            'filter' => true,
        ],
        [
            'title' => 'Quantity Diterima',
            'filterId' => 'quantity-diterima',
            'paramName' => 'quantity_diterima',
            'filter' => true,
        ],
        [
            'title' => 'Satuan',
            'filterId' => 'satuan',
            'paramName' => 'satuan',
            'filter' => true,
        ],
        [
            'title' => 'Harga',
            'filterId' => 'harga',
            'paramName' => 'harga',
            'filter' => true,
        ],
        [
            'title' => 'Jumlah Harga',
            'filterId' => 'jumlah-harga',
            'paramName' => 'jumlah_harga',
            'filter' => true,
        ],
    ];

    $appliedFilters = false;
    foreach ($headers as $header) {
        if ($header['filter'] && request("selected_{$header['paramName']}")) {
            $appliedFilters = true;
            break;
        }
    }

    $resetUrl = request()->url();
    $queryParams = '';
    if (request()->hasAny(['search'])) {
        $queryParams = '?' . http_build_query(request()->only(['search']));
    }
@endphp

<div class="ibox-body ms-0 ps-0">
    <div class="mb-3 d-flex justify-content-end">
        @if ($appliedFilters)
            <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ $resetUrl . $queryParams }}">
                <i class="bi bi-x-circle"></i> <span class="ms-2">Hapus Semua Filter</span>
            </a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="m-0 table table-bordered table-hover" id="table-data">
            <thead class="table-primary">
                <tr>
                    @foreach ($headers as $header)
                        @include('components.table-header-filter', $header)
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($TableData as $item)
                    @php
                        $details = isset($item->originalSpb) ? $item->originalSpb->linkSpbDetailSpb : $item->linkSpbDetailSpb;
                    @endphp

                    @forelse ($details as $detail)
                        <tr>
                            <td class="text-center">{{ $detail->detailSPB->masterDataAlat->jenis_alat }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataAlat->kode_alat }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataSparepart->kategoriSparepart->kode }}: {{ $detail->detailSPB->masterDataSparepart->kategoriSparepart->nama }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataSparepart->nama }}</td>
                            <td class="text-center">{{ $detail->detailSPB->masterDataSparepart->merk }}</td>
                            <td class="text-center">{{ $item->masterDataSupplier->nama }}</td>
                            <td class="text-center">{{ $item->linkSpbDetailSpb[$loop->index]->detailSPB->quantity_po }}</td>
                            <td class="text-center">{{ $detail->detailSPB->atbs->sum('quantity') }}</td>
                            <td class="text-center">{{ $detail->detailSPB->satuan }}</td>
                            <td class="currency-value">{{ number_format($detail->detailSPB->harga, 2, ',', '.') }}</td>
                            <td class="currency-value">{{ number_format($detail->detailSPB->harga * $detail->detailSPB->quantity_po, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="11">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data SPB
                            </td>
                        </tr>
                    @endforelse
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="11">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data SPB
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-primary">
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="9">Jumlah</th>
                    <th class="currency-value" id="totalHarga">0</th>
                    <th class="currency-value" id="totalJumlahHarga">0</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="10">PPN 11%</th>
                    <th class="currency-value" id="ppn11">0</th>
                </tr>
                <tr>
                    <th class="ps-4" style="text-align: left;" colspan="10">Grand Total</th>
                    <th class="currency-value" id="grandTotal">0</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts_3')
    <script>
        $(document).ready(function() {
            function formatRibuan(angka) {
                // Format untuk locale Indonesia (koma untuk desimal, titik untuk ribuan)
                return angka.toFixed(2).toString().replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function unformatRibuan(rupiah) {
                // Ubah format Indonesia ke format numerik
                return parseFloat(rupiah.replace(/\./g, '').replace(',', '.')) || 0;
            }

            function updateJumlahHarga(row) {
                const harga = unformatRibuan(row.find('input[name^="harga"]').val());
                const quantity = parseFloat(row.find('input[name^="qty"]').val()) || 0;
                const jumlahHarga = harga * quantity;
                row.find('td:nth-child(10) input').val(formatRibuan(jumlahHarga));
                updateTotalFooter();
            }

            function updateTotalFooter() {
                let totalHarga = 0;
                let totalJumlahHarga = 0;

                $('#table-data tbody tr').each(function() {
                    const harga = unformatRibuan($(this).find('td:nth-child(10)').text());
                    const quantity = parseInt($(this).find('td:nth-child(7)').text()) || 0;
                    const jumlahHarga = harga * quantity;

                    totalHarga += harga;
                    totalJumlahHarga += jumlahHarga;
                });

                const ppn11 = totalJumlahHarga * 0.11;
                const grandTotal = totalJumlahHarga + ppn11;

                $('#totalHarga').text(formatRibuan(totalHarga));
                $('#totalJumlahHarga').text(formatRibuan(totalJumlahHarga));
                $('#ppn11').text(formatRibuan(ppn11));
                $('#grandTotal').text(formatRibuan(grandTotal));
            }

            // Event handler for harga input
            $(document).on('blur', 'input[name^="harga"]', function() {
                const row = $(this).closest('tr');
                const harga = unformatRibuan($(this).val());
                $(this).val(formatRibuan(harga));
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

        function toggleFilter(filterId) {
            const popup = document.getElementById(filterId);
            const allPopups = document.querySelectorAll('.filter-popup');

            allPopups.forEach(p => {
                if (p.id !== filterId) {
                    p.style.display = 'none';
                }
            });

            popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
        }

        function filterCheckboxes(filterKey, event) {
            const searchText = event.target.value.toLowerCase();
            const checkboxes = document.querySelectorAll(`[id^=${filterKey}_]`);

            checkboxes.forEach(checkbox => {
                const label = checkbox.nextElementSibling.textContent.toLowerCase();
                checkbox.parentElement.style.display =
                    label.includes(searchText) ? 'block' : 'none';
            });
        }

        function applyFilter(filterKey) {
            const checkboxes = document.querySelectorAll(`[id^=${filterKey}_]:checked`);
            const values = Array.from(checkboxes).map(cb => cb.value);
            const encodedValues = btoa(values.join('||'));

            const url = new URL(window.location.href);
            url.searchParams.set(`selected_${filterKey}`, encodedValues);
            window.location.href = url.toString();
        }

        function clearFilter(filterKey) {
            const url = new URL(window.location.href);
            url.searchParams.delete(`selected_${filterKey}`);
            window.location.href = url.toString();
        }
    </script>

    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
