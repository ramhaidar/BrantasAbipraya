@push('styles_3')
    @include('styles.tables')
@endpush

@php
    $headers = [
        [
            'title' => 'Nama Supplier',
            'filterId' => 'nama',
            'paramName' => 'nama',
            'filter' => true,
        ],
        [
            'title' => 'Alamat Supplier',
            'filterId' => 'alamat',
            'paramName' => 'alamat',
            'filter' => true,
        ],
        [
            'title' => 'Contact Person',
            'filterId' => 'contact-person',
            'paramName' => 'contact_person',
            'filter' => true,
        ],
        [
            'title' => 'Detail',
            'filter' => false,
        ],
        [
            'title' => 'Aksi',
            'filter' => false,
            'roles' => ['admin_divisi', 'superadmin'],
        ],
    ];

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
                        @include(
                            'components.table-header-filter',
                            array_merge($header, [
                                'uniqueValues' => $uniqueValues ?? [],
                            ]))
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="text-center">{{ $supplier->nama }}</td>
                        <td class="text-center">{{ $supplier->alamat }}</td>
                        <td class="text-center">{{ $supplier->contact_person }}</td>
                        <td class="text-center">
                            <button class="btn btn-primary" type="button" onclick="fillFormDetail({{ $supplier->id }})">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                        @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
                            <td class="text-center">
                                <button class="btn btn-warning mx-1" data-bs-target=#modalForEdit data-bs-toggle=modal type="button" onclick="fillFormEdit({{ $supplier->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $supplier->id }}" type="button">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td class="text-center py-3 text-muted" colspan="5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No suppliers found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
