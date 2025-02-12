@push('styles_3')
    @include('styles.tables')

    <style>
        #table-data th:nth-child(9),
        #table-data th:nth-child(10),
        #table-data td:nth-child(9),
        #table-data td:nth-child(10) {
            min-width: 10dvw;
            width: 10dvw;
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0">
    <form id="detailSpbForm" action="{{ route('spb.detail.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input name="id_rkb" type="hidden" value="{{ $TableData->first()->id }}">
        <input id="spb_addendum_input" name="spb_addendum_id" type="hidden">
        <input id="selected-jenis-alat" name="selected_jenis_alat" type="hidden" value="{{ request('selected_jenis_alat') }}">
        <input id="selected-kode-alat" name="selected_kode_alat" type="hidden" value="{{ request('selected_kode_alat') }}">
        <input id="selected-kategori" name="selected_kategori" type="hidden" value="{{ request('selected_kategori') }}">
        <input id="selected-sparepart" name="selected_sparepart" type="hidden" value="{{ request('selected_sparepart') }}">
        <input id="selected-quantity" name="selected_quantity" type="hidden" value="{{ request('selected_quantity') }}">
        <input id="selected-satuan" name="selected_satuan" type="hidden" value="{{ request('selected_satuan') }}">

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

        @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
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
        @endif

        <div class="table-responsive">
            <table class="m-0 table table-bordered table-hover" id="table-data">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Nama Alat
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('jenis-alat-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_jenis_alat'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('jenis_alat')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="jenis-alat-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jenis alat..." onkeyup="filterCheckboxes('jenis_alat', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedJenisAlat) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueJenisAlat as $jenisAlat)
                                            <div class="form-check">
                                                <input class="form-check-input jenis_alat-checkbox" type="checkbox" value="{{ $jenisAlat }}" style="cursor: pointer" {{ in_array($jenisAlat, $selectedJenisAlat) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $jenisAlat }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('jenis_alat')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th class="text-center">
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Kode Alat
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('kode-alat-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_kode_alat'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('kode_alat')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="kode-alat-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kode alat..." onkeyup="filterCheckboxes('kode_alat', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kode_alat-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedKodeAlat) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueKodeAlat as $kodeAlat)
                                            <div class="form-check">
                                                <input class="form-check-input kode_alat-checkbox" type="checkbox" value="{{ $kodeAlat }}" style="cursor: pointer" {{ in_array($kodeAlat, $selectedKodeAlat) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kodeAlat }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kode_alat')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th class="text-center">
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Kategori
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('kategori-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_kategori'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('kategori')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="kategori-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kategori..." onkeyup="filterCheckboxes('kategori', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kategori-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedKategori) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueKategori as $kategori)
                                            <div class="form-check">
                                                <input class="form-check-input kategori-checkbox" type="checkbox" value="{{ $kategori }}" style="cursor: pointer" {{ in_array($kategori, $selectedKategori) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $kategori }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kategori')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th class="text-center">
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Sparepart Requested
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('sparepart-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_sparepart'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('sparepart')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="sparepart-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search sparepart..." onkeyup="filterCheckboxes('sparepart', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input sparepart-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedSparepart) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueSpareparts as $sparepart)
                                            <div class="form-check">
                                                <input class="form-check-input sparepart-checkbox" type="checkbox" value="{{ $sparepart }}" style="cursor: pointer" {{ in_array($sparepart, $selectedSparepart) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $sparepart }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('sparepart')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th class="text-center">Sparepart PO</th>
                        <th class="text-center">
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Quantity<br>Sisa
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('quantity-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_quantity'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('quantity')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="quantity-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search quantity..." onkeyup="filterCheckboxes('quantity', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input quantity-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedQuantity) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input quantity-checkbox" type="checkbox" value="0" style="cursor: pointer" {{ in_array('0', $selectedQuantity) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">0</label>
                                        </div>
                                        @foreach ($uniqueQuantities->filter(fn($q) => $q > 0) as $quantity)
                                            <div class="form-check">
                                                <input class="form-check-input quantity-checkbox" type="checkbox" value="{{ $quantity }}" style="cursor: pointer" {{ in_array((string) $quantity, $selectedQuantity) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $quantity }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('quantity')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th class="text-center">Quantity<br>PO</th>
                        <th class="text-center">
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Satuan
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('satuan-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_satuan'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('satuan')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="satuan-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search satuan..." onkeyup="filterCheckboxes('satuan', event)">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input satuan-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', $selectedSatuan) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueSatuan as $satuan)
                                            <div class="form-check">
                                                <input class="form-check-input satuan-checkbox" type="checkbox" value="{{ $satuan }}" style="cursor: pointer" {{ in_array($satuan, $selectedSatuan) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $satuan }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('satuan')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
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
                                    <input class="form-control text-center h-100 d-flex align-items-center justify-content-center" name="harga[{{ $data['detail']->id }}]" type="text" value="Rp 0" placeholder="Rp 0" maxlength="-1" required disabled>
                                </td>
                                <td class="text-center">
                                    <input class="form-control text-center bg-secondary h-100 d-flex align-items-center justify-content-center" type="text" value="Rp 0" readonly>
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
                    hargaInput.prop('disabled', true).val('Rp 0');
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
    @include('scripts.filterPopupManager')
@endpush
