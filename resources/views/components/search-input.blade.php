<div class="p-0 m-0 row mb-3">
    <div class="col-md-12 p-0 m-0">
        <form class="d-flex gap-2" action="{{ $route ?? url()->current() }}" method="GET">
            {{-- Preserve existing query parameters --}}
            @foreach (request()->except(['search', 'per_page']) as $key => $value)
                <input name="{{ $key }}" type="hidden" value="{{ $value }}">
            @endforeach

            <div class="input-group">
                <input class="form-control" name="search" type="text" value="{{ request('search') }}" placeholder="{{ $placeholder ?? 'Search items...' }}">
                @if (request('search'))
                    <button class="btn btn-secondary" type="button" onclick="this.form.querySelector('[name=search]').value = ''; this.form.submit();">
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
