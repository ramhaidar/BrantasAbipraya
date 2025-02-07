@push('styles_3')
    @include('styles.tables')
@endpush

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if (request('selected_name') || request('selected_username') || request('selected_sex') || request('selected_role') || request('selected_phone') || request('selected_email'))
                <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ request()->url() . (request('search') ? '?search=' . request('search') . '&id_proyek=' . request('id_proyek') : '?id_proyek=' . request('id_proyek')) }}">
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
                                Name
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('name-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_name'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('name')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="name-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search name..." onkeyup="filterCheckboxes('name')">
                                    <div class="checkbox-list text-start">
                                        @foreach ($uniqueValues['name'] as $name)
                                            <div class="form-check">
                                                <input class="form-check-input name-checkbox" type="checkbox" value="{{ $name }}" style="cursor: pointer" {{ in_array($name, explode(',', request('selected_name', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('name')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Username
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('username-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_username'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('username')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="username-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search username..." onkeyup="filterCheckboxes('username')">
                                    <div class="checkbox-list text-start">
                                        @foreach ($uniqueValues['username'] as $username)
                                            <div class="form-check">
                                                <input class="form-check-input username-checkbox" type="checkbox" value="{{ $username }}" style="cursor: pointer" {{ in_array($username, explode(',', request('selected_username', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $username }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('username')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Jenis Kelamin
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('sex-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_sex'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('sex')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="sex-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search jenis kelamin..." onkeyup="filterCheckboxes('sex')">
                                    <div class="checkbox-list text-start">
                                        @foreach ($uniqueValues['sex'] as $sex)
                                            <div class="form-check">
                                                <input class="form-check-input sex-checkbox" type="checkbox" value="{{ $sex }}" style="cursor: pointer" {{ in_array($sex, explode(',', request('selected_sex', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $sex }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('sex')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Role
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('role-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_role'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('role')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="role-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search role..." onkeyup="filterCheckboxes('role')">
                                    <div class="checkbox-list text-start">
                                        @foreach ($uniqueValues['role'] as $role)
                                            <div class="form-check">
                                                <input class="form-check-input role-checkbox" type="checkbox" value="{{ $role }}" style="cursor: pointer" {{ in_array($role, explode(',', request('selected_role', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $role }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('role')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Phone
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('phone-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_phone'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('phone')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="phone-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search phone..." onkeyup="filterCheckboxes('phone')">
                                    <div class="checkbox-list text-start">
                                        @foreach ($uniqueValues['phone'] as $phone)
                                            <div class="form-check">
                                                <input class="form-check-input phone-checkbox" type="checkbox" value="{{ $phone }}" style="cursor: pointer" {{ in_array($phone, explode(',', request('selected_phone', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $phone }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('phone')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                Email
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFilter('email-filter')">
                                        <i class="bi bi-funnel-fill"></i>
                                    </button>
                                    @if (request('selected_email'))
                                        <button class="btn btn-outline-danger btn-sm" type="button" onclick="clearFilter('email')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-popup" id="email-filter" style="display: none;">
                                <div class="p-2">
                                    <input class="form-control form-control-sm mb-2" type="text" placeholder="Search email..." onkeyup="filterCheckboxes('email')">
                                    <div class="checkbox-list text-start">
                                        @foreach ($uniqueValues['email'] as $email)
                                            <div class="form-check">
                                                <input class="form-check-input email-checkbox" type="checkbox" value="{{ $email }}" style="cursor: pointer" {{ in_array($email, explode(',', request('selected_email', ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" style="cursor: pointer" onclick="toggleCheckbox(this)">{{ $email }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" type="button" onclick="applyFilter('email')">
                                        <i class="bi bi-check-circle"></i> <span class="ms-2">Apply</span>
                                    </button>
                                </div>
                            </div>
                        </th>
                        @if (Auth::user()->role === 'superadmin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $user)
                        <tr>
                            <td class="text-center">{{ $user->name }}</td>
                            <td class="text-center">{{ $user->username }}</td>
                            <td class="text-center">{{ $user->sex }}</td>
                            <td class="text-center">{{ $user->role }}</td>
                            <td class="text-center">{{ $user->phone }}</td>
                            <td class="text-center">{{ $user->email }}</td>
                            @if (Auth::user()->role === 'superadmin')
                                <td class="text-center">
                                    <button class="btn btn-warning mx-1" data-bs-target=#modalForEdit data-bs-toggle=modal onclick="fillFormEdit({{ $user->id }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $user->id }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="7">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-name" name="selected_name" type="hidden" value="{{ request('selected_name') }}">
        <input id="selected-username" name="selected_username" type="hidden" value="{{ request('selected_username') }}">
        <input id="selected-sex" name="selected_sex" type="hidden" value="{{ request('selected_sex') }}">
        <input id="selected-role" name="selected_role" type="hidden" value="{{ request('selected_role') }}">
        <input id="selected-phone" name="selected_phone" type="hidden" value="{{ request('selected_phone') }}">
        <input id="selected-email" name="selected_email" type="hidden" value="{{ request('selected_email') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
