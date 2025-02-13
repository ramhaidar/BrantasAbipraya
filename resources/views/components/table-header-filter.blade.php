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
                <input class="form-control form-control-sm mb-2" type="text" placeholder="Search {{ strtolower($title) }}..." onkeyup="filterCheckboxes('{{ $paramName }}')">
                <div class="checkbox-list text-start">
                    <div class="form-check">
                        <input class="form-check-input {{ $paramName }}-checkbox" type="checkbox" value="null" style="cursor: pointer" {{ in_array('null', explode('||', base64_decode(request("selected_$paramName") ?? ''))) ? 'checked' : '' }}>
                        <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">Empty/Null</label>
                    </div>
                    @foreach ($uniqueValues[(string) $paramName] as $value)
                        <div class="form-check">
                            <input class="form-check-input {{ $paramName }}-checkbox" type="checkbox" value="{{ $value }}" style="cursor: pointer" {{ in_array($value, explode('||', base64_decode(request("selected_$paramName") ?? ''))) ? 'checked' : '' }}>
                            <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $value }}</label>
                        </div>
                    @endforeach
                </div>
                <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('{{ $paramName }}')"><i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span></button>
            </div>
        </div>
    @endif
</th>
