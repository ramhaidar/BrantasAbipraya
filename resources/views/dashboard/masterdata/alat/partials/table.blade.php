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
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 7px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 3000;
            max-height: 300px;
            min-width: 200px;
            margin: 10px;
            /* Increased margin around popup */
        }

        /* Add new styles for right-aligned popups */
        .filter-popup.right-aligned {
            right: 25px;
            /* Increased padding from window edge */
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
            @if (request('selected_jenis') || request('selected_merek') || request('selected_kode') || request('selected_tipe') || request('selected_serial') || request('selected_proyek'))
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
                                Jenis Alat
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
                                Kode Alat
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
                                Merek Alat
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('merek-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_merek'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('merek')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="merek-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search merek..." onkeyup="filterCheckboxes('merek')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input merek-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_merek', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['merek'] as $merek)
                                            <div class="form-check">
                                                <input class="form-check-input merek-checkbox" type="checkbox" value="{{ $merek }}" {{ in_array($merek, explode(',', request('selected_merek', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $merek }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('merek')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Tipe Alat
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('tipe-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_tipe'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('tipe')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="tipe-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search tipe..." onkeyup="filterCheckboxes('tipe')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input tipe-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_tipe', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['tipe'] as $tipe)
                                            <div class="form-check">
                                                <input class="form-check-input tipe-checkbox" type="checkbox" value="{{ $tipe }}" {{ in_array($tipe, explode(',', request('selected_tipe', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $tipe }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('tipe')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Serial Number
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('serial-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_serial'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('serial')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="serial-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search serial..." onkeyup="filterCheckboxes('serial')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input serial-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_serial', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Empty/Null</label>
                                        </div>
                                        @foreach ($uniqueValues['serial'] as $serial)
                                            <div class="form-check">
                                                <input class="form-check-input serial-checkbox" type="checkbox" value="{{ $serial }}" {{ in_array($serial, explode(',', request('selected_serial', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $serial }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('serial')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Lokasi Proyek
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('proyek-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_proyek'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('proyek')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="proyek-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search proyek..." onkeyup="filterCheckboxes('proyek')">
                                    <div class="checkbox-list text-start">
                                        <div class="form-check">
                                            <input class="form-check-input proyek-checkbox" type="checkbox" value="null" {{ in_array('null', explode(',', request('selected_proyek', ''))) ? 'checked' : '' }}>
                                            <label class="form-check-label" onclick="toggleCheckbox(this)">Belum Ditugaskan</label>
                                        </div>
                                        @foreach ($uniqueValues['proyek'] as $proyek)
                                            <div class="form-check">
                                                <input class="form-check-input proyek-checkbox" type="checkbox" value="{{ $proyek }}" {{ in_array($proyek, explode(',', request('selected_proyek', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" onclick="toggleCheckbox(this)">{{ $proyek }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('proyek')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
                                </div>
                            </div>
                        </th>
                        <th>Riwayat</th>
                        @if (auth()->user()->role == 'admin_divisi' || auth()->user()->role == 'superadmin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $item)
                        <tr>
                            <td>{{ $item->jenis_alat }}</td>
                            <td>{{ $item->kode_alat }}</td>
                            <td>{{ $item->merek_alat }}</td>
                            <td>{{ $item->tipe_alat }}</td>
                            <td>{{ $item->serial_number }}</td>
                            <td>{{ isset($item->current_project) ? $item->current_project->nama : 'Belum Ditugaskan' }}</td>
                            <td>
                                <button class="btn btn-info historyBtn" data-id="{{ $item->id }}" title="Lihat Riwayat">
                                    <i class="bi bi-clock-history"></i>
                                </button>
                            </td>
                            @if (auth()->user()->role == 'admin_divisi' || auth()->user()->role == 'superadmin')
                                <td class="text-center">
                                    <button class="btn btn-warning mx-1 ubahBtn" data-id="{{ $item->id }}" onclick="fillFormEdit({{ $item->id }})">
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
                            <td class="text-center py-3 text-muted" colspan="{{ auth()->user()->role == 'admin_divisi' || auth()->user()->role == 'superadmin' ? '8' : '6' }}">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak Ada Data Master Data Alat
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-jenis" name="selected_jenis" type="hidden" value="{{ request('selected_jenis') }}">
        <input id="selected-merek" name="selected_merek" type="hidden" value="{{ request('selected_merek') }}">
        <input id="selected-kode" name="selected_kode" type="hidden" value="{{ request('selected_kode') }}">
        <input id="selected-tipe" name="selected_tipe" type="hidden" value="{{ request('selected_tipe') }}">
        <input id="selected-serial" name="selected_serial" type="hidden" value="{{ request('selected_serial') }}">
        <input id="selected-proyek" name="selected_proyek" type="hidden" value="{{ request('selected_proyek') }}">
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
