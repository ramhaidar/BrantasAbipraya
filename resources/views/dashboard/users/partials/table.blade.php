@push('styles_3')
    @include('styles.tables')
@endpush

@php
    $headers = [
        [
            'title' => 'Name',
            'filterId' => 'name',
            'paramName' => 'name',
            'filter' => true,
        ],
        [
            'title' => 'Username',
            'filterId' => 'username',
            'paramName' => 'username',
            'filter' => true,
        ],
        [
            'title' => 'Jenis Kelamin',
            'filterId' => 'sex',
            'paramName' => 'sex',
            'filter' => true,
        ],
        [
            'title' => 'Role',
            'filterId' => 'role',
            'paramName' => 'role',
            'filter' => true,
        ],
        [
            'title' => 'Phone',
            'filterId' => 'phone',
            'paramName' => 'phone',
            'filter' => true,
        ],
        [
            'title' => 'Email',
            'filterId' => 'email',
            'paramName' => 'email',
            'filter' => true,
        ],
    ];

    if (Auth::user()->role === 'superadmin') {
        $headers[] = [
            'title' => 'Aksi',
            'filter' => false,
        ];
    }

    $appliedFilters = false;
    foreach ($headers as $header) {
        if ($header['filter'] && request("selected_{$header['paramName']}")) {
            $appliedFilters = true;
            break;
        }
    }

    $resetUrl = request()->url();
    $queryParams = '';
    if (request()->hasAny(['search', 'id_proyek'])) {
        $queryParams = '?' . http_build_query(request()->only(['search', 'id_proyek']));
    }
@endphp

<div class="ibox-body ms-0 ps-0">
    <form class="mb-3" id="filter-form" method="GET">
        <div class="mb-3 d-flex justify-content-end">
            @if ($appliedFilters)
                <a class="btn btn-danger btn-sm btn-hide-text-mobile" href="{{ $resetUrl . $queryParams }}">
                    <i class="bi bi-x-circle"></i> <span class="ms-2">Hapus Semua Filter</span>
                </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="m-0 table table-bordered table-hover" id="table-data">
                <thead class="table-primary">
                    <tr>
                        @foreach ($headers as $header)
                            @include('components.table-header-filter', $header)
                        @endforeach
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
                                    <button class="btn btn-warning mx-1" data-bs-target=#modalForEdit data-bs-toggle=modal type="button" onclick="fillFormEdit({{ $user->id }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $user->id }}" type="button">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="{{ count($headers) }}">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @foreach ($headers as $header)
            @if ($header['filter'])
                <input id="selected-{{ $header['paramName'] }}" name="selected_{{ $header['paramName'] }}" type="hidden" value="{{ request("selected_{$header['paramName']}") }}">
            @endif
        @endforeach
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
