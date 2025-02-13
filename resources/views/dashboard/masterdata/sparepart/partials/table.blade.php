@push('styles_3')
    @include('styles.tables')
@endpush

@php
    $headers = [
        [
            'title' => 'Nama Sparepart',
            'filterId' => 'nama',
            'paramName' => 'nama',
            'filter' => true,
        ],
        [
            'title' => 'Part Number',
            'filterId' => 'part-number',
            'paramName' => 'part_number',
            'filter' => true,
        ],
        [
            'title' => 'Merk Sparepart',
            'filterId' => 'merk',
            'paramName' => 'merk',
            'filter' => true,
        ],
        [
            'title' => 'Kode',
            'filterId' => 'kode',
            'paramName' => 'kode',
            'filter' => true,
        ],
        [
            'title' => 'Jenis',
            'filterId' => 'jenis',
            'paramName' => 'jenis',
            'filter' => true,
        ],
        [
            'title' => 'Sub Jenis',
            'filterId' => 'sub-jenis',
            'paramName' => 'sub_jenis',
            'filter' => true,
        ],
        [
            'title' => 'Kategori',
            'filterId' => 'kategori',
            'paramName' => 'kategori',
            'filter' => true,
        ],
        [
            'title' => 'Supplier',
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
                            @include(
                                'components.table-header-filter',
                                array_merge($header, [
                                    'uniqueValues' => $uniqueValues ?? [],
                                ]))
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($TableData as $sparepart)
                        <tr>
                            <td class="text-center">{{ $sparepart['nama'] }}</td>
                            <td class="text-center">{{ $sparepart['part_number'] }}</td>
                            <td class="text-center">{{ $sparepart['merk'] }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->kode }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->jenis }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->sub_jenis ?? '-' }}</td>
                            <td class="text-center">{{ $sparepart->kategoriSparepart->nama }}</td>
                            <td class="text-center">
                                <button class="btn btn-primary detailBtn" data-id="{{ $sparepart['id'] }}" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                            @if (Auth::user()->role === 'admin_divisi' || Auth::user()->role === 'superadmin')
                                <td class="text-center">
                                    <button class="btn btn-warning mx-1" data-bs-target=#modalForEdit data-bs-toggle=modal type="button" onclick="fillFormEdit({{ $sparepart['id'] }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger mx-1 deleteBtn" data-id="{{ $sparepart['id'] }}" type="button">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-3 text-muted" colspan="9">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No spareparts found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input id="selected-nama" name="selected_nama" type="hidden" value="{{ request('selected_nama') }}">
        <input id="selected-part-number" name="selected_part_number" type="hidden" value="{{ request('selected_part_number') }}">
        <input id="selected-merk" name="selected_merk" type="hidden" value="{{ request('selected_merk') }}">
        <input id="selected-kode" name="selected_kode" type="hidden" value="{{ request('selected_kode') }}">
        <input id="selected-jenis" name="selected_jenis" type="hidden" value="{{ request('selected_jenis') }}">
        <input id="selected-sub-jenis" name="selected_sub_jenis" type="hidden" value="{{ request('selected_sub_jenis') }}">
        <input id="selected-kategori" name="selected_kategori" type="hidden" value="{{ request('selected_kategori') }}">
    </form>
</div>

@push('scripts_3')
    @include('scripts.adjustTableColumnWidthByHeaderText')
    @include('scripts.filterPopupManager')
@endpush
