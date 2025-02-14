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
                    @if (isset($type) && $type === 'number')
                        <div class="mb-2">
                            <label class="form-label small">Sama Dengan "="</label>
                            <input class="form-control form-control-sm" id="{{ $paramName }}-exact" type="number" placeholder="Masukkan nilai tepat">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Lebih Besar Dari Sama Dengan ">="</label>
                            <input class="form-control form-control-sm" id="{{ $paramName }}-gt" type="number" placeholder="Lebih besar dari...">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Lebih Kecil Dari Sama Dengan "<="</label>
                            <input class="form-control form-control-sm" id="{{ $paramName }}-lt" type="number" placeholder="Lebih kecil dari...">
                        </div>
                        <hr>
                    @endif

                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search {{ strtolower($title) }}..." onkeyup="filterCheckboxes('{{ $paramName }}', event)">
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
