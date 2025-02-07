<div class="p-0 m-0 row mb-3">
    <div class="col-md-12 p-0 m-0">
        <form class="d-flex gap-2" action="{{ $route ?? url()->current() }}" method="GET">
            {{-- Preserve all existing query parameters --}}
            @foreach (request()->query() as $key => $value)
                @if (!in_array($key, ['search', 'per_page']) && !empty($value))
                    @if (is_array($value))
                        @foreach ($value as $arrayValue)
                            <input name="{{ $key }}[]" type="hidden" value="{{ $arrayValue }}">
                        @endforeach
                    @else
                        <input name="{{ $key }}" type="hidden" value="{{ $value }}">
                    @endif
                @endif
            @endforeach

            <div class="input-group">
                <input class="form-control" name="search" type="text" value="{{ request('search') }}" placeholder="{{ $placeholder ?? 'Search items...' }}">
                @if (request('search'))
                    <button class="btn btn-secondary" type="button" onclick="clearSearch(this)">
                        <i class="fa fa-times"></i>
                    </button>
                @endif
                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </div>

            @if (($show_all ?? false) === false)
                <select class="form-select" name="per_page" style="width: auto;" onchange="this.form.submit()">
                    @foreach ([10, 25, 50, 100] as $value)
                        <option value="{{ $value }}" {{ request('per_page', 10) == $value ? 'selected' : '' }}>
                            {{ $value }} baris
                        </option>
                    @endforeach
                </select>
            @endif
        </form>
    </div>
</div>

<script>
    function clearSearch(button) {
        const form = button.closest('form');
        const searchInput = form.querySelector('[name=search]');
        searchInput.value = '';
        form.submit();
    }
</script>
