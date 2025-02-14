@php
    $showHeader = true;
    if (isset($roles)) {
        $showHeader = in_array(auth()->user()->role, $roles);
    }

    // Ensure $paramName is defined with a default value if not set
    $paramName = $paramName ?? '';

    // Format values for different types of filters
    $formattedValues = [];

    if ($paramName === 'periode' && isset($uniqueValues['periode'])) {
        // Format periode values
        $formattedValues = collect($uniqueValues['periode'])
            ->map(function ($periode) {
                return \Carbon\Carbon::parse($periode)->isoFormat('MMMM Y');
            })
            ->toArray();
    } elseif ($paramName === 'tanggal' && isset($uniqueValues['tanggal'])) {
        // Format tanggal values to Indonesian format
        $formattedValues = collect($uniqueValues['tanggal'])
            ->map(function ($tanggal) {
                setlocale(LC_TIME, 'id_ID');
                return \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y');
            })
            ->toArray();
    } elseif ($paramName === 'tipe' && isset($uniqueValues['tipe'])) {
        // Format tipe values
        $formattedValues = collect($uniqueValues['tipe'])
            ->map(function ($tipe) {
                return ucwords($tipe);
            })
            ->toArray();
    } elseif (in_array($paramName, ['durasi_rencana', 'durasi_actual']) && isset($uniqueValues[$paramName])) {
        // Format duration values by adding 'Hari' suffix
        $formattedValues = collect($uniqueValues[$paramName])
            ->map(function ($value) {
                return $value . ' Hari';
            })
            ->toArray();
    } elseif (in_array($paramName, ['harga', 'jumlah_harga', 'ppn', 'bruto']) && isset($uniqueValues[$paramName])) {
        // Format currency values including ppn and bruto
        $formattedValues = collect($uniqueValues[$paramName])
            ->map(function ($value) {
                return number_format($value, 0, ',', '.');
            })
            ->toArray();
    }

    // Get previously selected numeric values
    $exactValue = request("selected_{$paramName}") ? collect(explode('||', base64_decode(request("selected_{$paramName}"))))->first(fn($value) => strpos($value, 'exact:') === 0) : null;
    $gtValue = request("selected_{$paramName}") ? collect(explode('||', base64_decode(request("selected_{$paramName}"))))->first(fn($value) => strpos($value, 'gt:') === 0) : null;
    $ltValue = request("selected_{$paramName}") ? collect(explode('||', base64_decode(request("selected_{$paramName}"))))->first(fn($value) => strpos($value, 'lt:') === 0) : null;

    $exactValue = $exactValue ? substr($exactValue, 6) : '';
    $gtValue = $gtValue ? substr($gtValue, 3) : '';
    $ltValue = $ltValue ? substr($ltValue, 3) : '';

    // Get previously selected date values
    if (isset($type) && $type === 'date') {
        $exactValue = request("selected_{$paramName}") ? collect(explode('||', base64_decode(request("selected_{$paramName}"))))->first(fn($value) => strpos($value, 'exact:') === 0) : null;
        $gtValue = request("selected_{$paramName}") ? collect(explode('||', base64_decode(request("selected_{$paramName}"))))->first(fn($value) => strpos($value, 'gt:') === 0) : null;
        $ltValue = request("selected_{$paramName}") ? collect(explode('||', base64_decode(request("selected_{$paramName}"))))->first(fn($value) => strpos($value, 'lt:') === 0) : null;

        $exactValue = $exactValue ? substr($exactValue, 6) : '';
        $gtValue = $gtValue ? substr($gtValue, 3) : '';
        $ltValue = $ltValue ? substr($ltValue, 3) : '';
    }
@endphp

@if ($showHeader)
    <th>
        <div class="d-flex align-items-center gap-2 justify-content-center">
            {{ $title }}
            @if ($filter ?? true)
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('{{ $filterId }}-filter')">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                    @if (request("selected_$paramName"))
                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('{{ $paramName }}')">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    @endif
                </div>
            @endif
        </div>
        @if ($filter ?? true)
            <div class="filter-popup" id="{{ $filterId }}-filter" style="display: none;">
                <div class="p-2">
                    @if (isset($type) && $type === 'date')
                        <div class="mb-2">
                            <label class="form-label small">Sama Dengan "="</label>
                            <div class="input-group input-group-sm date-input-group">
                                <input class="form-control datepicker" id="{{ $paramName }}-exact" type="text" value="{{ $exactValue }}" placeholder="Pilih tanggal" autocomplete="off" onchange="clearRelatedFields('{{ $paramName }}', 'exact')">
                                <span class="input-group-text" style="cursor: pointer;" onclick="showDatepicker('{{ $paramName }}-exact')">
                                    <i class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <label class="form-label small">Setelah atau Sama Dengan ">="</label>
                            <div class="input-group input-group-sm date-input-group">
                                <input class="form-control datepicker" id="{{ $paramName }}-gt" type="text" value="{{ $gtValue }}" placeholder="Setelah tanggal..." autocomplete="off" onchange="clearRelatedFields('{{ $paramName }}', 'gt')">
                                <span class="input-group-text" style="cursor: pointer;" onclick="showDatepicker('{{ $paramName }}-gt')">
                                    <i class="fa fa-calendar"></i>
                                </span>
                        </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Sebelum atau Sama Dengan "<="</label>
                            <div class="input-group input-group-sm date-input-group">
                        <input class="form-control datepicker" id="{{ $paramName }}-lt" type="text" value="{{ $ltValue }}" placeholder="Sebelum tanggal..." autocomplete="off" onchange="clearRelatedFields('{{ $paramName }}', 'lt')">
                        <span class="input-group-text" style="cursor: pointer;" onclick="showDatepicker('{{ $paramName }}-lt')">
                            <i class="fa fa-calendar"></i>
                        </span>
                </div>
            </div>
            <hr>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var dateFormat = 'yy-mm-dd';
                    var options = {
                        dateFormat: dateFormat,
                        changeMonth: true,
                        changeYear: true,
                        regional: 'id',
                        autoOpen: false,
                        showOn: false,
                        beforeShow: function(input, inst) {
                            // Prevent the filter popup from closing when datepicker opens
                            inst.dpDiv.on('click', function(e) {
                                e.stopPropagation();
                            });
                            return true;
                        }
                    };

                    // Initialize datepickers
                    $('.datepicker').each(function() {
                        $(this).datepicker(options);
                    });

                    // Add click handler to prevent popup closing
                    $(document).on('click', '.ui-datepicker, .ui-datepicker *', function(e) {
                        e.stopPropagation();
                    });

                    // Set regional settings
                    $.datepicker.setDefaults($.datepicker.regional['id']);
                });

                function showDatepicker(inputId) {
                    $('#' + inputId).datepicker('show');
                }

                function clearRelatedFields(paramName, type) {
                    if (type === 'exact') {
                        if ($('#' + paramName + '-exact').val()) {
                            $('#' + paramName + '-gt').val('');
                            $('#' + paramName + '-lt').val('');
                        }
                    } else {
                        $('#' + paramName + '-exact').val('');
                    }
                }
            </script>
        @elseif (isset($type) && in_array($type, ['number', 'number_of_days']))
            <div class="mb-2">
                <label class="form-label small">Sama Dengan "="</label>
                <input class="form-control form-control-sm" id="{{ $paramName }}-exact" type="number" value="{{ $exactValue }}" placeholder="Masukkan nilai tepat">
            </div>
            <hr>
            <div class="mb-2">
                <label class="form-label small">Lebih Besar Dari Sama Dengan ">="</label>
                            <input class="form-control form-control-sm" id="{{ $paramName }}-gt" type="number" value="{{ $gtValue }}" placeholder="Lebih besar dari...">
            </div>
            <div class="mb-2">
                <label class="form-label small">Lebih Kecil Dari Sama Dengan "<="</label>
                            <input class="form-control form-control-sm" id="{{ $paramName }}-lt" type="number" value="{{ $ltValue }}" placeholder="Lebih kecil dari...">
            </div>
            <hr>
        @endif

        <input class="form-control form-control-sm mb-2" type="text" placeholder="Cari {{ strtolower($title) }}..." onkeyup="filterCheckboxes('{{ $paramName }}', event)">
        @if (isset($uniqueValues[(string) $paramName]) || isset($customUniqueValues))
            <div class="checkbox-list text-start">
                <div class="form-check">
                    <input class="form-check-input {{ $paramName }}-checkbox" type="checkbox" value="Empty/Null" style="cursor: pointer" {{ in_array('Empty/Null', explode(',', request("selected_$paramName", ''))) ? 'checked' : '' }}>
                    <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                </div>
                @if (isset($customUniqueValues))
                    @foreach ($customUniqueValues as $value)
                        <div class="form-check">
                            <input class="form-check-input {{ $paramName }}-checkbox" type="checkbox" value="{{ $value }}" style="cursor: pointer" {{ in_array($value, explode(',', request("selected_$paramName", ''))) ? 'checked' : '' }}>
                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $value }}</label>
                        </div>
                    @endforeach
                @else
                    @foreach ($uniqueValues[(string) $paramName] as $key => $value)
                        <div class="form-check">
                            <input class="form-check-input {{ $paramName }}-checkbox" type="checkbox" value="{{ $value }}" style="cursor: pointer" {{ in_array((string) $value, explode(',', request("selected_$paramName", ''))) ? 'checked' : '' }}>
                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">
                                @if ($paramName === 'periode')
                                    {{ $formattedValues[$key] }}
                                @elseif ($paramName === 'tanggal')
                                    {{ $formattedValues[$key] }}
                                @elseif ($paramName === 'tipe')
                                    {{ $formattedValues[$key] }}
                                @elseif (in_array($paramName, ['durasi_rencana', 'durasi_actual']))
                                    {{ $formattedValues[$key] }}
                                @elseif (in_array($paramName, ['harga', 'jumlah_harga', 'ppn', 'bruto']))
                                    {{ $formattedValues[$key] }}
                                @else
                                    {{ $value }}
                                @endif
                            </label>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
        <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('{{ $paramName }}')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
        </div>
        </div>
@endif
</th>
@endif
