@extends('dashboard')
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

@section('css')
    <link href=/css/random-css-datatable.css rel=stylesheet>
    <link href=https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css rel=stylesheet crossorigin=anonymous integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" referrerpolicy=no-referrer>
    <style>
        .alert-custom-css {
            max-width: 400px;
            width: 90%
        }

        #form-group {
            width: 90%
        }

        .space-nowrap {
            white-space: nowrap
        }

        .center {
            text-align: center !important
        }

        .custom-confirm-delete {
            margin-right: 5%
        }

        .custom-cancel-delete {
            margin-left: 5%
        }

        .custom-action-delete {
            width: 100% !important;
            justify-content: space-between
        }

        .no-border {
            border: none !important;
        }

        @media screen and (max-width:500px) {
            #button-for-modal-add span {
                display: none;
            }

            #button-for-modal-add {
                font-size: 20px;
            }
        }

        @media screen and (max-width:500px) {
            .button-export-import-addData span {
                display: none;
            }

            .button-export-import-addData {
                font-size: 20px;
            }
        }

        #table-data td,
        #table-data th {
            vertical-align: middle !important;
            text-align: center !important;
        }

        #table-data td div.text-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
@endsection

@section('content')
    <div class="fade-in-up page-content">
        <div class=ibox>
            <div class="ibox-head pe-0 ps-0">
                {{-- <div class=ibox-title>{{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}</div> --}}

                <!-- Judul -->
                <div class="ibox-title flex-grow-1 text-start">
                    {{ $page ?? 'Buat variabel $page di controller sesuai nama halaman' }}
                </div>

                <!-- Filter Tanggal -->
                <div class="d-flex flex-grow-1 justify-content-center">
                    <div class="row align-items-center w-100">
                        <div class="col">
                            <input class="form-control datetimepicker" id="start_date" name="start_date" required placeholder="Tanggal Awal" autocomplete="off">
                        </div>
                        <div class="col-auto mx-2">
                            <span>—</span>
                        </div>
                        <div class="col">
                            <input class="form-control datetimepicker" id="end_date" name="end_date" required placeholder="Tanggal Akhir" autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <!-- Tambahkan tombol filter -->
                            <button class="btn btn-primary btn-sm button-export-import-addData" id="filterButton" type="button">Filter</button>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-grow-1 justify-content-end">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <a class="btn btn-success btn-sm button-export-import-addData" href="{{ route('saldo.export', ['id_proyek' => $proyek->id, 'tipe' => $tipe ?? 'default']) }}">
                                <i class="fa fa-file-excel"></i>
                                <span class="ms-2">Export Data</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-0 ibox-body table-responsive">
                <table class="m-0 border-dark table table-bordered table-striped"id=table-data style=width:100%>
                    <thead class=table-primary>
                        <tr class="">
                            {{-- <th class="align-middle text-center" hidden> --}}
                            {{-- <th class="align-middle text-center">Nama Proyek --}}
                            <th class="align-middle text-center">Tanggal Penerimaan
                            <th class="align-middle text-center">Kode
                            <th class="align-middle text-center">Supplier
                            <th class="align-middle text-center">Sparepart
                            <th class="align-middle text-center">Part Number
                            <th class="align-middle text-center">Quantity
                                {{-- <th class="align-middle text-center">Current Quantity
                            <th class="align-middle text-center">Quantity --}}
                            <th class="align-middle text-center">Satuan
                                {{-- <th class="align-middle">Nilai
                            <th class="align-middle">Perbaikan
                            <th class="align-middle">Pemeliharaan --}}
                            <th class="align-middle">Harga Satuan
                            <th class="align-middle">Net
                    <tbody>
                        @foreach ($saldoList as $saldo)
                            <tr class="text-center">
                                {{-- <td hidden> --}}
                                {{-- <td class="align-middle">{{ $saldo->atb->komponen->nama_proyek }} --}}
                                <td class="align-middle">{{ Carbon::parse($saldo->atb->tanggal)->translatedFormat('d F Y') }}
                                <td class="align-middle">{{ $saldo->atb->komponen->kode }}
                                <td class="align-middle">{{ $saldo->atb->masterData->supplier ?? '-' }}
                                <td class="align-middle">{{ $saldo->atb->masterData->sparepart ?? '-' }}
                                <td class="align-middle">{{ $saldo->atb->masterData->part_number ?? '-' }}
                                    {{-- <td class="align-middle">{{ $saldo->current_quantity }}
                                <td class="align-middle">{{ $saldo->atb->quantity }} --}}
                                <td class="align-middle">{{ $saldo->current_quantity }}
                                <td class="align-middle">{{ $saldo->atb->satuan }}
                                <td class="align-middle">Rp{{ number_format($saldo->atb->harga, 0, ',', '.') }}</td>
                                <td class="align-middle">
                                    @if ($saldo->id_apb != null)
                                        -
                                    @else
                                        Rp{{ number_format($saldo->atb->harga * $saldo->current_quantity, 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    <tfoot>
                        @php
                            $totalHarga = 0;
                            foreach ($saldoList as $saldo) {
                                $totalHarga += $saldo->atb->harga;
                            }
                        @endphp
                        <tr class="table-primary">
                            <td class="align-middle" colspan="7"><strong>Total</strong></td>
                            <td class="align-middle text-center"><strong>Rp{{ number_format($totalHarga, 0, ',', '.') }}</strong></td>
                            <td class="align-middle text-center"><strong>{{ $totalNet }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="fade modal"aria-hidden=true id=modalForDelete data-bs-backdrop=static data-bs-keyboard=false aria-labelledby=staticBackdropLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered">
            <div class=modal-content>
                <div class=modal-header>
                    <h1 class="fs-5 modal-title"id=staticBackdropLabel>Form Konfirmasi</h1><button class=btn-close type=button onclick=closeModalDelete()></button>
                </div>
                <form method=POST>@csrf @method('DELETE')<div class=modal-body>
                        <div class=form-group>
                            <div class="mb-3 mt-3">
                                <p class="form-label fw-bold gap-0"for=name required>Ketik Ulang "
                                <p class="m-0 text-primary"id=model-konfirmasi></p>"</p><input class="form-control border-dark"id=name name=name required>
                            </div>
                        </div>
                    </div>
                    <div class=modal-footer><a class="btn btn-secondary"onclick=closeModalDelete()>Batal</a> <button class="btn btn-danger"type=submit>Hapus</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="fade modal"aria-hidden=true id=modalDetailAlat data-bs-backdrop=static data-bs-keyboard=false aria-labelledby=modalDetailAlatLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class=modal-content>
                <div class=modal-header>
                    <h1 class="fs-5 modal-title fw-bold"id=modalDetailAlatLabel>Alat</h1><button class=btn-close type=button onclick=closeModalAlat()></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0"style=overflow-y:auto method=POST>@csrf<div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1"id=nama_atb>
                            <p class="m-0 fs-5 fw-medium mb-1"id=pembuat_atb>
                        </div>
                        {{-- <div class=col-md-6>
                            <div class=mb-3><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=proyek>Proyek</label> <input class="form-control bg-transparent no-border p-0"id=proyek name=proyek required readonly></div>
                        </div> --}}
                        <div class=col-md-6>
                            <div class=mb-3><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=jenis_alat>Jenis Alat</label> <input class="form-control bg-transparent no-border p-0"id=jenis_alat name=jenis_alat required readonly></div>
                        </div>
                        <div class=col-md-6>
                            <div class=mb-3><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=tipe_alat>Tipe Alat</label> <input class="form-control bg-transparent no-border p-0"id=tipe_alat name=tipe_alat required readonly></div>
                        </div>
                        <div class=col-md-6>
                            <div><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=kode_alat>Kode Alat</label> <input class="form-control bg-transparent no-border p-0"id=kode_alat name=kode_alat required readonly></div>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer"><button class="btn btn-secondary"type=button data-bs-dismiss=modal>Keluar</button> {{-- <button class="btn btn-primary"type=button id=edit-alat>Ubah</button> --}} <button class="btn btn-primary"type=submit hidden>Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="fade modal"aria-hidden=true id=modalDetailNilai data-bs-backdrop=static data-bs-keyboard=false aria-labelledby=modalDetailNilaiLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class=modal-content>
                <div class=modal-header>
                    <h1 class="fs-5 modal-title fw-bold"id=modalDetailNilaiLabel>Nilai</h1><button class=btn-close type=button onclick=closeModalNilai()></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0"style=overflow-y:auto method=POST>@csrf<div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1"id=nama_atb>
                            <p class="m-0 fs-5 fw-medium mb-1"id=pembuat_atb>
                        </div>
                        <div class=col-md-6>
                            <div class=mb-3><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=harga>Harga</label> <input class="form-control bg-transparent no-border p-0"id=harga name=harga required readonly></div>
                        </div>
                        <div class=col-md-6>
                            <div class=mb-3><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=net>Net</label> <input class="form-control bg-transparent no-border p-0"id=net name=net required readonly></div>
                        </div>
                        <div class=col-md-6>
                            <div class=mb-3><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=ppn>PPN</label> <input class="form-control bg-transparent no-border p-0"id=ppn name=ppn required readonly></div>
                        </div>
                        <div class=col-md-6>
                            <div><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=bruto>Bruto</label> <input class="form-control bg-transparent no-border p-0"id=bruto name=bruto required readonly></div>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer"><button class="btn btn-secondary"type=button data-bs-dismiss=modal>Keluar</button> {{-- <button class="btn btn-primary"type=button id=edit-nilai>Ubah</button> --}} <button class="btn btn-primary"type=submit hidden>Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="fade modal"aria-hidden=true id=modalDetailPerbaikan data-bs-backdrop=static data-bs-keyboard=false aria-labelledby=modalDetailPerbaikanLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class=modal-content>
                <div class=modal-header>
                    <h1 class="fs-5 modal-title fw-bold"id=modalDetailPerbaikanLabel>Perbaikan</h1><button class=btn-close type=button onclick=closeModalPerbaikan()></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0"style=overflow-y:auto method=POST>@csrf<div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1"id=nama_atb>
                            <p class="m-0 fs-5 fw-medium mb-1"id=pembuat_atb>
                        </div>
                        <div class=col-md-6>
                            <div class=mb-3><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=kode>Kode</label> <input class="form-control bg-transparent no-border p-0"id=kode name=kode required readonly></div>
                        </div>
                        <div class=col-md-6>
                            <div class=mb-3><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=value>Value</label> <input class="form-control bg-transparent no-border p-0"id=value name=value required readonly></div>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer"><button class="btn btn-secondary"type=button data-bs-dismiss=modal>Keluar</button> {{-- <button class="btn btn-primary"type=button id=edit-perbaikan>Ubah</button> --}} <button class="btn btn-primary"type=submit hidden>Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="fade modal"aria-hidden=true id=modalDetailPemeliharaan data-bs-backdrop=static data-bs-keyboard=false aria-labelledby=modalDetailPemeliharaanLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class=modal-content>
                <div class=modal-header>
                    <h1 class="fs-5 modal-title fw-bold"id=modalDetailPemeliharaanLabel>Pemeliharaan</h1><button class=btn-close type=button onclick=closeModalPemeliharaan()></button>
                </div>
                <form class="w-100 d-flex align-items-center flex-column gap-0"style=overflow-y:auto method=POST>@csrf<div class="w-100 modal-body row">
                        <div class="mb-3 pe-0 ps-0 w-100">
                            <p class="m-0 fs-5 fw-medium mb-1"id=nama_atb>
                            <p class="m-0 fs-5 fw-medium mb-1"id=pembuat_atb>
                        </div>
                        <div class=col-md-6>
                            <div class=mb-3><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=kode>Kode</label> <input class="form-control bg-transparent no-border p-0"id=kode name=kode required readonly></div>
                        </div>
                        <div class=col-md-6>
                            <div class=mb-3><label class="m-0 container-fluid fw-semibold ps-0 text-start"for=value>Value</label> <input class="form-control bg-transparent no-border p-0"id=value name=value required readonly></div>
                        </div>
                    </div>
                    <div class="w-100 d-flex modal-footer"><button class="btn btn-secondary"type=button data-bs-dismiss=modal>Keluar</button> {{-- <button class="btn btn-primary"type=button id=edit-pemeliharaan>Ubah</button> --}} <button class="btn btn-primary"type=submit hidden>Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="fade modal"aria-hidden=true id=modalForAdd data-bs-backdrop=static aria-labelledby=modalForEditLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class=modal-content>
                <div class=modal-header>
                    <h5 class="fs-5 modal-title fw-bold"id=modalForAddLabel>Tambah Data ATB Baru</h5><button class=btn-close type=button onclick=closeModalAdd()></button>
                </div>
                <div class="w-100 modal-body row">
                    <form class="w-100 align-items-center flex-column gap-0"id=addDataForm method=POST action="{{ route('atb.store') }}">@csrf<div class=mb-4><label class=form-label for=tipe_atb>Tipe ATB</label>
                            <div class=input-group><select class=form-control id=pilihan-proyek1 name=tipe_atb>
                                    <option value=hutang-unit-alat>Hutang Unit Alat
                                    <option value=panjar-unit-alat>Panjar Unit Alat
                                    <option value=mutasi-proyek>Mutasi Proyek
                                    <option value=panjar-proyek>Panjar Proyek
                                </select></div>
                        </div>
                        <div class=mb-3><label class=form-label for=nama_proyek>Nama Proyek</label> <input class=form-control id=nama_proyek name=nama_proyek required></div>
                        <div class=mb-3><label class=form-label for=tanggal>Tanggal</label> <input class="form-control datetimepicker"id=tanggal name=tanggal required autocomplete=off></div>
                        <div class=mb-4><label class=form-label for=pilihan-kode1>Kode</label>
                            <div class=input-group><select class=form-control id=pilihan-kode1 name=kode>
                                    <option>
                                    <option value=A1>A1: CABIN
                                    <option value=A2>A2: ENGINE SYSTEM
                                    <option value=A3>A3: TRANSMISSION SYSTEM
                                    <option value=A4>A4: CHASSIS & SWING MACHINERY
                                    <option value=A5>A5: DIFFERENTIAL SYSTEM
                                    <option value=A6>A6: ELECTRICAL SYSTEM
                                    <option value=A7>A7: HYDRAULIC/PNEUMATIC SYSTEM
                                    <option value=A8>A8: STEERING SYSTEM
                                    <option value=A9>A9: BRAKE SYSTEM
                                    <option value=A10>A10: SUSPENSION
                                    <option value=A11>A11: ATTACHMENT
                                    <option value=A12>A12: UNDERCARRIAGE
                                    <option value=A13>A13: FINAL DRIVE
                                    <option value=A14>A14: FREIGHT COST
                                    <option value=B11>B11: Oil Filter
                                    <option value=B12>B12: Fuel Filter
                                    <option value=B13>B13: Air Filter
                                    <option value=B14>B14: Hydraulic Filter
                                    <option value=B15>B15: Transmission Filter
                                    <option value=B16>B16: Differential Filter
                                    <option value=B21>B21: Engine Oil
                                    <option value=B22>B22: Hydraulic Oil
                                    <option value=B23>B23: Transmission Oil
                                    <option value=B24>B24: Final Drive Oil
                                    <option value=B25>B25: Swing & Damper Oil
                                    <option value=B26>B26: Differential Oil
                                    <option value=B27>B27: Grease
                                    <option value=B28>B28: Brake & Power Steering Fluid
                                    <option value=B29>B29: Coolant
                                    <option value=B3>B3: Tyre
                                    <option value=C1>C1: Workshop
                                </select></div>
                        </div>
                        <div class=mb-3><label class=form-label for=supplier>Supplier</label> <input class=form-control id=supplier name=supplier required></div>
                        <div class=mb-3><label class=form-label for=sparepart>Sparepart</label> <input class=form-control id=sparepart name=sparepart required></div>
                        <div class=mb-3><label class=form-label for=part_number>Part Number</label> <input class=form-control id=part_number name=part_number required></div>
                        <div class=mb-3><label class=form-label for=quantity>Quantity</label> <input class=form-control id=quantity name=quantity type=number required></div>
                        <div class=mb-4><label class=form-label for=satuan>Satuan</label>
                            <div class=input-group><select class=form-control id=pilihan-satuan1 name=satuan>
                                    <option value=PCS>PCS
                                    <option value=SET>SET
                                    <option value=BTL>BTL
                                    <option value=LTR>LTR
                                </select></div>
                        </div>
                        <div class=mb-3><label class=form-label for=harga>Harga</label> <input class=form-control id=harga name=harga type=number required></div>{{-- <div class="m-0 container-fluid p-0 pt-3 text-center"><div class="row gx-5"><div class=col><button class="btn btn-success container-fluid"type=submit>Submit</button></div></div></div> --}}<div class="m-0 p-0 d-flex modal-footer pt-4 w-100"><button class="m-0 container-fluid p-0 btn btn-success py-2"type=submit>Tambah Data</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="fade modal"aria-hidden=true id=modalForEdit data-bs-backdrop=static aria-labelledby=modalForEditLabel tabindex=-1>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class=modal-content>
                <div class=modal-header>
                    <h5 class="fs-5 modal-title fw-bold"id=modalForEditLabel>Ubah Data ATB</h5><button class=btn-close type=button onclick=closeModalEdit()></button>
                </div>
                <div class="w-100 modal-body row">
                    <form class="w-100 align-items-center flex-column gap-0"id=editDataForm method=POST>@csrf<div class=mb-4><label class=form-label for=tipe_atb>Tipe ATB</label>
                            <div class=input-group><select class=form-control id=pilihan-proyek2 name=tipe_atb>
                                    <option value=hutang-unit-alat>Hutang Unit Alat
                                    <option value=panjar-unit-alat>Panjar Unit Alat
                                    <option value=mutasi-proyek>Mutasi Proyek
                                    <option value=panjar-proyek>Panjar Proyek
                                </select></div>
                        </div>
                        <div class=mb-3><label class=form-label for=nama_proyek>Nama Proyek</label> <input class=form-control id=nama_proyek name=nama_proyek required></div>
                        <div class=mb-3><label class=form-label for=tanggal>Tanggal</label> <input class="form-control datetimepicker"id=tanggal name=tanggal required autocomplete=off></div>
                        <div class=mb-4><label class=form-label for=pilihan-kode1>Kode</label>
                            <div class=input-group><select class=form-control id=pilihan-kode2 name=kode>
                                    <option>
                                    <option value=A1>A1: CABIN
                                    <option value=A2>A2: ENGINE SYSTEM
                                    <option value=A3>A3: TRANSMISSION SYSTEM
                                    <option value=A4>A4: CHASSIS & SWING MACHINERY
                                    <option value=A5>A5: DIFFERENTIAL SYSTEM
                                    <option value=A6>A6: ELECTRICAL SYSTEM
                                    <option value=A7>A7: HYDRAULIC/PNEUMATIC SYSTEM
                                    <option value=A8>A8: STEERING SYSTEM
                                    <option value=A9>A9: BRAKE SYSTEM
                                    <option value=A10>A10: SUSPENSION
                                    <option value=A11>A11: ATTACHMENT
                                    <option value=A12>A12: UNDERCARRIAGE
                                    <option value=A13>A13: FINAL DRIVE
                                    <option value=A14>A14: FREIGHT COST
                                    <option value=B11>B11: Oil Filter
                                    <option value=B12>B12: Fuel Filter
                                    <option value=B13>B13: Air Filter
                                    <option value=B14>B14: Hydraulic Filter
                                    <option value=B15>B15: Transmission Filter
                                    <option value=B16>B16: Differential Filter
                                    <option value=B21>B21: Engine Oil
                                    <option value=B22>B22: Hydraulic Oil
                                    <option value=B23>B23: Transmission Oil
                                    <option value=B24>B24: Final Drive Oil
                                    <option value=B25>B25: Swing & Damper Oil
                                    <option value=B26>B26: Differential Oil
                                    <option value=B27>B27: Grease
                                    <option value=B28>B28: Brake & Power Steering Fluid
                                    <option value=B29>B29: Coolant
                                    <option value=B3>B3: Tyre
                                    <option value=C1>C1: Workshop
                                </select></div>
                        </div>
                        <div class=mb-3><label class=form-label for=supplier>Supplier</label> <input class=form-control id=supplier name=supplier required></div>
                        <div class=mb-3><label class=form-label for=sparepart>Sparepart</label> <input class=form-control id=sparepart name=sparepart required></div>
                        <div class=mb-3><label class=form-label for=part_number>Part Number</label> <input class=form-control id=part_number name=part_number required></div>
                        <div class=mb-3><label class=form-label for=quantity>Quantity</label> <input class=form-control id=quantity name=quantity type=number required></div>
                        <div class=mb-4><label class=form-label for=satuan>Satuan</label>
                            <div class=input-group><select class=form-control id=pilihan-satuan2 name=satuan>
                                    <option value=PCS>PCS
                                    <option value=SET>SET
                                    <option value=BTL>BTL
                                    <option value=LTR>LTR
                                </select></div>
                        </div>
                        <div class=mb-3><label class=form-label for=harga>Harga</label> <input class=form-control id=harga name=harga type=number required></div>{{-- <div class="m-0 container-fluid p-0 pt-3 text-center"><div class="row gx-5"><div class=col><button class="btn btn-success container-fluid"type=submit>Submit</button></div></div></div> --}}<div class="m-0 p-0 d-flex modal-footer pt-4 w-100"><button class="m-0 container-fluid p-0 btn btn-success py-2"type=submit>Ubah Data</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection()

@section('script')
    <script crossorigin=anonymous integrity="sha512-AIOTidJAcHBH2G/oZv9viEGXRqDNmfdPVPYOYKGy3fti0xIplnlgMHUGfuNRzC6FkzIo0iIxgFnr9RikFxK+sw==" referrerpolicy=no-referrer src=https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js></script>
    <script>
        $(".nav-tabs a").click(function() {
            $(this).tab('show');
        });

        function closeModalAlat() {
            $('#modalDetailAlat').modal('hide');
        }

        function closeModalNilai() {
            $('#modalDetailNilai').modal('hide');
        }

        function closeModalPerbaikan() {
            $('#modalDetailPerbaikan').modal('hide');
        }

        function closeModalPemeliharaan() {
            $('#modalDetailPemeliharaan').modal('hide');
        }

        function closeModalAdd() {
            $('#modalForAdd').modal('hide');
        }

        function closeModalEdit() {
            $('#modalForEdit').modal('hide');
        }

        function closeModalDelete() {
            $('#modalForDelete').modal('hide');
        }

        const validationSecond = (id, name) => {
            document.querySelector('#model-konfirmasi').innerText = name;
            document.querySelector('#modalForDelete form').action = `/atb/ex_panjar_unit_alat/${id}`;
        };

        document.querySelector('#modalForDelete form').addEventListener('submit', function(event) {
            var confirmationText = document.getElementById('model-konfirmasi').innerText.trim();
            var inputName = document.querySelector('#modalForDelete #name');
            if (inputName.value.trim() !== confirmationText) {
                event.preventDefault();
                showSweetAlert2('Masukkan tidak sesuai. Silakan coba lagi!', 'error')
            }
        });

        function showSweetAlert2(msg, icon) {
            let title = '';
            if (icon == 'success') {
                title = 'Transaksi Berhasil!';
                msg = `Berhasil ${msg}.`;
            }
            Swal.fire({
                html: msg,
                icon: icon,
                title: title,
                confirmButtonText: 'Oke',
                customClass: {
                    popup: 'alert-custom-css'
                }
            });
        }

        function fillFormEdit(params) {
            document.querySelector('#modalForEdit form').action = `/atb/ex_panjar_unit_alat/edit/${params}`;
            getATB(params)
                .then(data => {
                    console.log(data);

                    var selectElementTipe = $('#pilihan-proyek2');
                    var selectElementKode = $('#pilihan-kode2');
                    var selectElementSatuan = $('#pilihan-satuan2');

                    var tipeValue;
                    switch (data.atb_type) {
                        case 'Hutang Unit Alat':
                            tipeValue = 'hutang-unit-alat';
                            break;
                        case 'Panjar Unit Alat':
                            tipeValue = 'panjar-unit-alat';
                            break;
                        case 'Mutasi Proyek':
                            tipeValue = 'mutasi-proyek';
                            break;
                        case 'Panjar Proyek':
                            tipeValue = 'panjar-proyek';
                            break;
                        default:
                            tipeValue = '';
                    }
                    selectElementTipe.val(tipeValue).trigger('change');

                    var kodeValue = data.komponen.kode.split(':')[0].trim();
                    selectElementKode.val(kodeValue).trigger('change');

                    var satuanValue = data.satuan;
                    selectElementSatuan.val(satuanValue).trigger('change');

                    document.querySelector('#modalForEdit #nama_proyek').value = data.komponen.nama_proyek;
                    document.querySelector('#modalForEdit #tanggal').value = data.tanggal;
                    document.querySelector('#modalForEdit #supplier').value = data.supplier;
                    document.querySelector('#modalForEdit #sparepart').value = data.sparepart;
                    document.querySelector('#modalForEdit #part_number').value = data.part_number;
                    document.querySelector('#modalForEdit #quantity').value = data.quantity;
                    document.querySelector('#modalForEdit #harga').value = data.harga;

                }).catch(error => {
                    showSweetAlert2(error, 'error');
                });
        }

        function getATB(params) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: "/saldo/ex_panjar_unit_alat/" + params,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        resolve(response.data);
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }

        function getDetailAlat(params) {
            getATB(params)
                .then(data => {
                    document.querySelector('#modalDetailAlat #nama_atb').innerText = "Nama Proyek: " + data.komponen.nama_proyek;
                    document.querySelector('#modalDetailAlat #jenis_alat').value = data.saldo[0].apb.alat.jenis_alat;
                    document.querySelector('#modalDetailAlat #tipe_alat').value = data.saldo[0].apb.alat.tipe_alat;
                    document.querySelector('#modalDetailAlat #kode_alat').value = data.saldo[0].apb.alat.jenis_alat;
                    $('#modalDetailAlat').modal('show');
                })
                .catch(error => {
                    $('#modalDetailAlat').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        function getDetailNilai(params) {
            getATB(params)
                .then(data => {
                    var formattedHarga = 'Rp' + data.harga.toLocaleString('id-ID');
                    var formattedNet = 'Rp' + data.net.toLocaleString('id-ID');
                    var formattedPpn = 'Rp' + data.ppn.toLocaleString('id-ID');
                    var formattedBruto = 'Rp' + data.bruto.toLocaleString('id-ID');

                    document.querySelector('#modalDetailNilai #nama_atb').innerText = "Nama Proyek: " + data.komponen.nama_proyek;
                    document.querySelector('#modalDetailNilai #harga').value = formattedHarga;
                    document.querySelector('#modalDetailNilai #net').value = formattedNet;
                    document.querySelector('#modalDetailNilai #ppn').value = formattedPpn;
                    document.querySelector('#modalDetailNilai #bruto').value = formattedBruto;
                    $('#modalDetailNilai').modal('show');
                })
                .catch(error => {
                    $('#modalDetailNilai').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        function getDetailPerbaikan(params) {
            getATB(params)
                .then(data => {
                    var formattedValue = 'Rp' + data.net.toLocaleString('id-ID');

                    document.querySelector('#modalDetailPerbaikan #nama_atb').innerText = "Nama Proyek: " + data.komponen.nama_proyek;
                    document.querySelector('#modalDetailPerbaikan #kode').value = data.komponen.kode;
                    document.querySelector('#modalDetailPerbaikan #value').value = formattedValue;
                    $('#modalDetailPerbaikan').modal('show');
                })
                .catch(error => {
                    $('#modalDetailPerbaikan').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        function getDetailPemeliharaan(params) {
            getATB(params)
                .then(data => {
                    var formattedValue = 'Rp' + data.net.toLocaleString('id-ID');

                    document.querySelector('#modalDetailPemeliharaan #nama_atb').innerText = "Nama Proyek: " + data.komponen.nama_proyek;
                    document.querySelector('#modalDetailPemeliharaan #kode').value = data.komponen.kode;
                    document.querySelector('#modalDetailPemeliharaan #value').value = formattedValue;
                    $('#modalDetailPemeliharaan').modal('show');
                })
                .catch(error => {
                    $('#modalDetailPemeliharaan').modal('hide');
                    showSweetAlert2(error, 'error');
                });
        }

        $(document).ready(function() {
            var table = $('#table-data').DataTable({
                language: {
                    paginate: {
                        previous: '<i class="bi bi-caret-left"></i>',
                        next: '<i class="bi bi-caret-right"></i>'
                    }
                },
                pageLength: -1,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                ordering: false
            });

            $('#pilihan-proyek1').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#pilihan-kode1').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#pilihan-satuan1').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForAdd')
            });

            $('#pilihan-proyek2').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit')
            });

            $('#pilihan-kode2').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit')
            });

            $('#pilihan-satuan2').select2({
                placeholder: "Pilih opsi",
                width: '100%',
                dropdownParent: $('#modalForEdit')
            });

            $('.datetimepicker').datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
                closeOnDateSelect: true,
            });

            function checkDatesAndFetchData() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var pageTitle = "{{ $page }}"; // Ambil judul halaman

                // Mapping $page ke tipe
                var tipeATB;
                switch (pageTitle) {
                    case 'Data Saldo EX Panjar Unit Alat':
                        tipeATB = 'Panjar Unit Alat';
                        break;
                    case 'Data Saldo EX Unit Alat':
                        tipeATB = 'Hutang Unit Alat';
                        break;
                    case 'Data Saldo EX Mutasi Saldo':
                        tipeATB = 'Mutasi Proyek';
                        break;
                    case 'Data Saldo EX Panjar Proyek':
                        tipeATB = 'Panjar Proyek';
                        break;
                    default:
                        tipeATB = ''; // Default jika tidak ada kecocokan
                }

                if (startDate && endDate) {
                    $.ajax({
                        url: '{{ route('saldo.fetchData') }}', // Sesuaikan URL dengan route yang benar untuk Saldo
                        method: 'GET',
                        data: {
                            start_date: startDate,
                            end_date: endDate,
                            id_proyek: {{ $proyek->id }},
                            tipe: tipeATB // Mengirim tipe yang dimapping ke backend
                        },
                        success: function(response) {
                            var table = $('#table-data').DataTable(); // Inisialisasi datatable
                            table.clear();

                            let totalNet = 0;
                            let totalHarga = 0; // Tambahkan variabel untuk menghitung total harga

                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(atb) {
                                    var row = [];

                                    row.push(
                                        atb.tanggal ? moment(atb.tanggal).format('DD MMMM YYYY') : '', // Format tanggal menjadi "03 September 2024"
                                        atb.komponen && atb.komponen.kode ? atb.komponen.kode : '',
                                        atb.master_data.supplier || '',
                                        atb.master_data.sparepart || '',
                                        atb.master_data.part_number || '',
                                        atb.saldo ? atb.saldo.current_quantity : '',
                                        atb.satuan || '',
                                        'Rp' + (atb.harga ? atb.harga.toLocaleString('id-ID') : ''),
                                        atb.saldo && atb.saldo.current_quantity ? 'Rp' + (atb.harga * atb.saldo.current_quantity).toLocaleString('id-ID') : '-'
                                    );

                                    // Update total harga dan net
                                    totalHarga += atb.harga ? atb.harga : 0; // Hitung total harga
                                    totalNet += atb.saldo && atb.saldo.current_quantity ? (atb.harga * atb.saldo.current_quantity) : 0;

                                    table.row.add(row);
                                });
                            } else {
                                showSweetAlert2('Tidak ada data yang ditemukan dalam rentang tanggal yang dipilih', 'info');
                            }

                            // Update the totals in the footer
                            $('#table-data tfoot tr').find('td').eq(1).html('<strong>Rp' + totalHarga.toLocaleString('id-ID') + '</strong>'); // Total Harga
                            $('#table-data tfoot tr').find('td').eq(2).html('<strong>Rp' + totalNet.toLocaleString('id-ID') + '</strong>'); // Total Net

                            table.draw();
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to fetch data:', error);
                            showSweetAlert2('Terjadi kesalahan saat mengambil data. Silakan coba lagi.', 'error');
                        }
                    });
                }
            }

            // Ubah dari on-change ke click pada tombol filter
            $('#filterButton').on('click', function() {
                checkDatesAndFetchData();
            });
        });
    </script>
    @if (session()->has('success'))
        <script>
            showSweetAlert2('{{ session('success') }}', 'success');
        </script>
    @endif
    @if (session()->has('error'))
        <script>
            showSweetAlert2('{{ session('error') }}', 'error');
        </script>
    @endif
    @if ($errors->any())
        <script>
            let errInput = '<ul class="m-0 no-bullet">';
        </script>
        @foreach ($errors->all() as $error)
            <script>
                errInput += "<li>{{ $error }}</li>"
            </script>
        @endforeach
        <script>
            errInput += "</ul>", showSweetAlert2(errInput, "error")
        </script>
    @endif
@endsection
