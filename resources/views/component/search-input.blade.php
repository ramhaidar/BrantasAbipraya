<div class="p-0 m-0 row mb-3">
    <div class="col-md-12 p-0 m-0">
        <form class="d-flex gap-2" action="{{ $route ?? url()->current() }}" method="GET">
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
                <input class="form-control" name="search" type="text" value="{{ request('search') }}" placeholder="Search equipment...">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
            <select class="form-select" name="per_page" style="width: auto;" onchange="this.form.submit()">
                @foreach ([10, 25, 50, 100] as $value)
                    <option value="{{ $value }}" {{ request('per_page', 10) == $value ? 'selected' : '' }}>
                        {{ $value }} baris
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>
