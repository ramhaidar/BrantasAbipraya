<!-- Modal Delete -->
<div class="fade modal" id=modalForDelete data-bs-backdrop=static data-bs-keyboard=false aria-hidden=true aria-labelledby=staticBackdropLabel tabindex=-1>
    <div class="modal-dialog modal-dialog-centered">
        <div class=modal-content>
            <div class=modal-header>
                <h1 class="fs-5 modal-title" id=staticBackdropLabel>Form Konfirmasi</h1>
                <button class=btn-close type=button onclick=closeModalDelete()></button>
            </div>
            <form method=POST>@csrf @method('DELETE')
                <div class=modal-body>
                    <div class=form-group>
                        <div class="mb-3 mt-3">
                            <p class="fw-bold form-label gap-0" for=confirm_name required>Ketik Ulang "
                            <p class="m-0 text-primary" id=model-konfirmasi></p>"</p>
                            <input class="form-control border-dark" id=confirm_name name=name required>
                        </div>
                    </div>
                </div>
                <div class=modal-footer><a class="btn btn-secondary" onclick=closeModalDelete()>Batal</a>
                    <button class="btn btn-danger" type=submit>Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_3')
    <script>
        function closeModalDelete() {
            $('#modalForDelete').modal('hide');
        }

        function showModalDelete() {
            $('#modalForDelete').modal('show');
        }

        const validationSecond = (id, name) => {
            document.querySelector('#model-konfirmasi').innerText = name;
            document.querySelector('#modalForDelete form').action = `/users/delete/${id}`;
        };

        document.querySelector('#modalForDelete form').addEventListener('submit', function(event) {
            var confirmationText = document.getElementById('model-konfirmasi').innerText.trim();
            var inputName = document.querySelector('#confirm_name');
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
                confirmButtonText: 'Oke',
                customClass: {
                    popup: 'alert-custom-css'
                }
            });
        }
    </script>
@endpush
