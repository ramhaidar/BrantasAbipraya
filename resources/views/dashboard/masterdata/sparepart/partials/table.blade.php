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

        .filter-popup {
            position: fixed;
            /* Changed from absolute to fixed */
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 7px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 3000;
            max-height: 300px;
            min-width: 200px;
            /* Remove margin-top since we'll position it via JavaScript */
        }

        /* Add new styles for right-aligned popups */
        .filter-popup.right-aligned {
            right: 10px;
            /* Add some padding from window edge */
        }

        .table-responsive {
            overflow-x: visible !important;
            /* Allow popups to overflow */
        }

        .checkbox-list {
            padding: 5px;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
@endpush

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_nama') || request('selected_part_number') || request('selected_merk') || request('selected_kode') || request('selected_kategori') || request('selected_jenis') || request('selected_sub_jenis'))
                <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ request()->url() . (request('search') ? '?search=' . request('search') : '') }}">
                    <i class="bi bi-x-circle"></i> <span class="ms-2">Hapus Semua Filter</span>
                </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="m-0 table table-bordered table-hover" id="table-data">
                <thead class="table-primary">
                    <tr>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Nama Sparepart
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('nama-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_nama'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('nama')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="nama-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search nama..." onkeyup="filterCheckboxes('nama')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input nama-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_nama', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['nama'] as $nama)
                                            <div class="form-check">
                                                <input class="form-check-input nama-checkbox" type="checkbox" value="{{ $nama }}" {{ in_array($nama, explode(',', request('selected_nama', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $nama }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('nama')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Part Number
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('part-number-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_part_number'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('part_number')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="part-number-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search part number..." onkeyup="filterCheckboxes('part_number')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input part_number-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_part_number', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['part_number'] as $partNumber)
                                            <div class="form-check">
                                                <input class="form-check-input part_number-checkbox" type="checkbox" value="{{ $partNumber }}" {{ in_array($partNumber, explode(',', request('selected_part_number', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $partNumber }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('part_number')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Merk Sparepart
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('merk-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_merk'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('merk')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="merk-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search merk..." onkeyup="filterCheckboxes('merk')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input merk-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_merk', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['merk'] as $merk)
                                            <div class="form-check">
                                                <input class="form-check-input merk-checkbox" type="checkbox" value="{{ $merk }}" {{ in_array($merk, explode(',', request('selected_merk', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $merk }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('merk')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Kode
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('kode-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_kode'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('kode')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="kode-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kode..." onkeyup="filterCheckboxes('kode')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kode-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_kode', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['kode'] as $kode)
                                            <div class="form-check">
                                                <input class="form-check-input kode-checkbox" type="checkbox" value="{{ $kode }}" {{ in_array($kode, explode(',', request('selected_kode', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $kode }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kode')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Jenis
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('jenis-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_jenis'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('jenis')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="jenis-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jenis..." onkeyup="filterCheckboxes('jenis')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input jenis-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_jenis', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['jenis'] as $jenis)
                                            <div class="form-check">
                                                <input class="form-check-input jenis-checkbox" type="checkbox" value="{{ $jenis }}" {{ in_array($jenis, explode(',', request('selected_jenis', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $jenis }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('jenis')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Sub Jenis
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('sub-jenis-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_sub_jenis'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('sub_jenis')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="sub-jenis-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search sub jenis..." onkeyup="filterCheckboxes('sub-jenis')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input sub-jenis-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_sub_jenis', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['sub_jenis'] as $subJenis)
                                            <div class="form-check">
                                                <input class="form-check-input sub-jenis-checkbox" type="checkbox" value="{{ $subJenis }}" {{ in_array($subJenis, explode(',', request('selected_sub_jenis', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $subJenis }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('sub_jenis')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
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
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search kategori..." onkeyup="filterCheckboxes('kategori')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input kategori-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_kategori', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['kategori'] as $kategori)
                                            <div class="form-check">
                                                <input class="form-check-input kategori-checkbox" type="checkbox" value="{{ $kategori }}" {{ in_array($kategori, explode(',', request('selected_kategori', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $kategori }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('kategori')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>Supplier</th>
                        @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $sparepart)
                        <tr>
                            <td class="text-center">{{ $sparepart['nama'] }}</td>
                            <td class="text-center">{{ $sparepart['part_number'] }}</td>
                            <td class="text-center">{{ $sparepart['merk'] }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->kode }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->jenis }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->sub_jenis ?? '-' }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->nama }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary detailBtn" data-id="{{ $sparepart['id'] }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                            @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
                                <td class="text-center">
                                    <button class="btn btn-warning mx-1" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $sparepart['id'] }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $sparepart['id'] }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="9">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No spareparts found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-nama" name="selected_nama" type="hidden" value="{{ request('selected_nama') }}">
        <input id="selected-part-number" name="selected_part_number" type="hidden" value="{{ request('selected_part_number') }}">
        <input id="selected-merk" name="selected_merk" type="hidden" value="{{ request('selected_merk') }}">
        <input id="selected-kode" name="selected_kode" type="hidden" value="{{ request('selected_kode') }}">
        <input id="selected-jenis" name="selected_jenis" type="hidden" value="{{ request('selected_jenis') }}">
        <input id="selected-sub-jenis" name="selected_sub_jenis" type="hidden" value="{{ request('selected_sub_jenis') }}">
        <input id="selected-kategori" name="selected_kategori" type="hidden" value="{{ request('selected_kategori') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    <script>
        function toggleFilter(id) {
            $('.filter-popup').not(`#${id}`).hide();
            const popup = $(`#${id}`);
            const button = $(`button[onclick="toggleFilter('${id}')"]`);

            if (popup.is(':hidden')) {
                positionPopup(popup, button);
            }
            popup.toggle();
        }

        function positionPopup(popup, button) {
            const buttonRect = button[0].getBoundingClientRect();
            const windowWidth = $(window).width();
            const windowHeight = $(window).height();
            const popupWidth = popup.outerWidth();
            const popupHeight = popup.outerHeight();
            const safetyMargin = 25; // Increased safety margin from edges
            const verticalGap = 10; // Gap between button and popup

            // Calculate vertical position
            let top = buttonRect.bottom + verticalGap;

            // Check if popup would go below viewport
            if (top + popupHeight > windowHeight - safetyMargin) {
                top = buttonRect.top - popupHeight - verticalGap;
            }

            // Ensure top is not negative and has minimum margin from top
            top = Math.max(safetyMargin, top);

            // Calculate horizontal position
            let left = buttonRect.left;

            // Check if popup would go off right edge
            if (left + popupWidth > windowWidth - safetyMargin) {
                left = windowWidth - popupWidth - safetyMargin;
                popup.addClass('right-aligned');
            } else {
                popup.removeClass('right-aligned');
            }

            // Ensure left is not negative and has minimum margin from left
            left = Math.max(safetyMargin, left);

            // Set the position with smooth transition
            popup.css({
                top: `${top}px`,
                left: `${left}px`,
                transition: 'left 0.2s, top 0.2s' // Optional: adds smooth movement
            });
        }

        // Update popup positions on window resize
        $(window).on('resize', function() {
            $('.filter-popup:visible').each(function() {
                const id = $(this).attr('id');
                const button = $(`button[onclick="toggleFilter('${id}')"]`);
                positionPopup($(this), button);
            });
        });

        function filterCheckboxes(type) {
            const searchText = $(event.target).val().toLowerCase();
            const selector = `.${type}-checkbox`;
            $(selector).each(function() {
                const label = $(this).next('label').text().toLowerCase();
                $(this).parent().toggle(label.includes(searchText));
            });
        }

        function applyFilter(type) {
            const selector = `.${type}-checkbox:checked`;
            const selected = $(selector).map(function() {
                return $(this).val();
            }).get();

            // Dapatkan semua parameter URL saat ini
            const urlParams = new URLSearchParams(window.location.search);

            // Update atau hapus parameter filter yang diubah
            if (selected.length > 0) {
                urlParams.set(`selected_${type}`, selected.join(','));
            } else {
                urlParams.delete(`selected_${type}`);
            }

            // Redirect dengan parameter yang diupdate
            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
        }

        function clearFilter(type) {
            // Dapatkan semua parameter URL saat ini
            const urlParams = new URLSearchParams(window.location.search);

            if (type === 'price') {
                urlParams.delete('price_min');
                urlParams.delete('price_max');
                urlParams.delete('price_exact');
            } else {
                urlParams.delete(`selected_${type}`);
            }

            // Redirect dengan parameter yang diupdate
            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
        }

        // Handler untuk tombol "Clear All Filters"
        function clearAllFilters() {
            const urlParams = new URLSearchParams(window.location.search);

            // Hapus semua parameter filter tapi pertahankan parameter lain
            const paramsToKeep = ['search', 'per_page'];
            const currentParams = Array.from(urlParams.keys());

            currentParams.forEach(param => {
                if (!paramsToKeep.includes(param)) {
                    urlParams.delete(param);
                }
            });

            // Redirect dengan parameter yang tersisa
            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
        }

        function toggleCheckbox(element) {
            const checkbox = $(element).prev('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked'));
        }

        $(document).ready(function() {
            $(document).on('click', function(event) {
                if (!$(event.target).closest('.filter-popup, button').length) {
                    $('.filter-popup').hide();
                }
            });

            $(document).on('keydown', function(event) {
                if (event.key === 'Escape') {
                    $('.filter-popup').hide();
                }
            });

            $(document).on('click', '.form-check-label', function(event) {
                event.stopPropagation();
            });
        });
    </script>
@endpush
