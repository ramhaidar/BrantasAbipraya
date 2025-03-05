<!doctype html>
<html lang=en>

<head>
    <meta charset=UTF-8>
    <meta name=viewport content="width=device-width,initial-scale=1">
    <title>Login | DDP Digital Logistic</title>
    <link type=image/x-icon href=favicon.ico rel=icon>
    <link href=https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css rel=stylesheet integrity=sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH crossorigin=anonymous>
    <link href=https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css rel=stylesheet>
    <link href=https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css rel=stylesheet>
    <link href=https://fonts.googleapis.com rel=preconnect>
    <link href=https://fonts.gstatic.com rel=preconnect crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel=stylesheet>
    <link href=https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css rel=stylesheet>
    <script src=https://code.jquery.com/jquery-3.6.0.min.js></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.3/sweetalert2.min.css" rel="stylesheet" integrity="sha512-P2bn4p1rF+R7elZYDXsk2A3Vq4IlJqUhsk0i9PhuPGTBO43Z8nWaYiVcNidcMu8eVjUGUmyFQbVWf5bVDznjTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url(https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap);

        * {
            margin: 0;
            padding: 0
        }

        body {
            font-family: Poppins, sans-serif
        }

        .carousel-item {
            height: 100vh;
            position: relative
        }

        .carousel-item img {
            height: 100vh;
            object-fit: cover;
            width: 100%
        }

        .carousel-control-next-icon,
        .carousel-control-prev-icon {
            width: 0;
            height: 0;
            overflow: hidden
        }

        .carousel .carousel-indicators button {
            width: 10px;
            height: 10px;
            border-radius: 100%
        }

        .login form .form-control {
            background: 0 0;
            border: none;
            border-bottom: 1px solid #b4b4b4;
            border-radius: 0;
            box-shadow: none;
            outline: 0;
            color: inherit
        }

        .form-control::placeholder {
            font-size: 1.3rem;
            color: #939393
        }

        .form-control {
            font-size: 1.3rem;
            padding-bottom: 1.5rem
        }
    </style>

    <!-- Cloudflare Turnstile -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>

<body>
    <div class="container-fluid text-center position-relative vh-100">
        <div class="row align-items-center vh-100">
            <div class="col-lg-8 p-0 position-relative d-flex flex-column">
                <div class="position-absolute bottom-0 start-50 translate-middle-x text-center text-white pb-5" style="z-index: 10; width: 100%; margin-bottom: 2rem;">
                    <p class="fs-1 fw-semibold">The Best Partner for Heavy Equipment</p>
                    <p class="fs-6">Meningkatkan Performa Alat Berat untuk Hasil Kerja yang Maksimal</p>
                </div>
                <div class="carousel slide vh-100" id="carouselExample" data-bs-ride="carousel">
                    <div class="carousel-indicators" style="margin-bottom: 2rem;">
                        <button class="active" data-bs-target="#carouselExample" data-bs-slide-to="0" type="button" aria-current="true" aria-label="Slide 1"></button>
                        <button data-bs-target="#carouselExample" data-bs-slide-to="1" type="button" aria-label="Slide 2"></button>
                        <button data-bs-target="#carouselExample" data-bs-slide-to="2" type="button" aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner vh-100">
                        <div class="carousel-item active">
                            <img class="d-block w-100" src="imageFour.jpg" alt="">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="imageFive.jpg" alt="">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="imageSix.jpg" alt="">
                        </div>
                    </div>
                    <button class="carousel-control-prev" data-bs-target="#carouselExample" data-bs-slide="prev" type="button">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" data-bs-target="#carouselExample" data-bs-slide="next" type="button">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <div class="col-lg-4 align-items-center login d-flex flex-column justify-content-center">
                <form id="loginForm" method="post" action="{{ route('login.post') }}">
                    <div class="container-fluid w-100 h-100">
                        @csrf
                        <p class="fs-1 mt-5 fw-semibold">Selamat datang di
                            <br>DPP DIGITAL LOGISTIC
                        </p>
                        <p class="fs-5 text-dark text-opacity-50 mb-4">Masukkan detail login</p>
                        <div class="form-group mb-3">
                            <input class="form-control" id="username" name="username" placeholder="Nama Pengguna">
                            <div class="invalid-feedback">Nama Pengguna diperlukan.</div>
                        </div>
                        <div class="form-group mb-3 position-relative">
                            <input class="form-control" id="password" name="password" type="password" placeholder="Kata sandi">
                            <div class="invalid-feedback">Kata sandi diperlukan.</div>
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3">
                                <i class="fa fa-eye" id="togglePassword" style="cursor:pointer"></i>
                            </span>
                        </div>

                        <!-- CloudFlare Turnstile Component -->
                        <div class="pt-2 w-100">
                            <x-turnstile data-size="flexible" data-language="id" />
                            @error('turnstile')
                                <div class="text-danger">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="form-group pb-3">
                            <button class="btn btn-warning text-light fs-5 fw-medium my-3 w-100" id="loginButton" type="submit" style="--bs-btn-bg: #E99D02;">Masuk</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.3/sweetalert2.min.js" integrity="sha512-zK+mEmgBJpVrlnQXcbEfs6Ao4e+ESmepuHso+2UpRwMJbfhPGYNxAZz+IqsiK6/hGn8S1nx1mFOVBoJXJGx8PQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            // Flag to prevent multiple form submissions
            let isSubmitting = false;

            // Refresh captcha functionality
            $("#btn-recaptcha").click(function() {
                $.ajax({
                    url: "recaptcha",
                    type: "get",
                    success: function(e) {
                        $(".RefreshCaptcha").html(e.captcha);
                    },
                    error: function(e) {
                        alert("Try Again.");
                    }
                });
            });

            // Toggle password visibility using jQuery
            $("#togglePassword").click(function() {
                var passwordField = $("#password");
                var passwordFieldType = passwordField.attr("type");

                if (passwordFieldType === "password") {
                    passwordField.attr("type", "text");
                    $(this).removeClass("fa-eye").addClass("fa-eye-slash");
                } else {
                    passwordField.attr("type", "password");
                    $(this).removeClass("fa-eye-slash").addClass("fa-eye");
                }
            });

            // Add validation feedback to inputs
            $('#username, #password').on('input', function() {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                }
            });

            // Function to validate login form
            function validateLoginForm() {
                let isValid = true;

                // Reset previous validation states
                $('.is-invalid').removeClass('is-invalid');

                // Check if username is empty
                if (!$('#username').val().trim()) {
                    $('#username').addClass('is-invalid');
                    isValid = false;
                }

                // Check if password is empty
                if (!$('#password').val().trim()) {
                    $('#password').addClass('is-invalid');
                    isValid = false;
                }

                return isValid;
            }

            // Function to display a spinner and disable the submit button
            function showSpinner() {
                $('#loginButton').prop('disabled', true).html('<span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>');
            }

            // Handle form submission with validation and anti-spam protection
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                // Prevent multiple submissions
                if (isSubmitting) {
                    return false;
                }

                if (validateLoginForm()) {
                    // Set flag to prevent multiple submissions
                    isSubmitting = true;

                    // Show spinner and disable button
                    showSpinner();

                    // Submit the form after a short delay to ensure spinner is visible
                    setTimeout(() => {
                        this.submit();
                    }, 50);
                }
            });

            // Handle Enter key press in input fields with anti-spam protection
            $('#username, #password').on('keypress', function(e) {
                if (e.which == 13) { // Enter key code
                    e.preventDefault();

                    // Prevent multiple submissions
                    if (isSubmitting) {
                        return false;
                    }

                    if (validateLoginForm()) {
                        // Set flag to prevent multiple submissions
                        isSubmitting = true;

                        // Show spinner and disable button
                        showSpinner();

                        // Submit the form after a short delay
                        setTimeout(() => {
                            $('#loginForm')[0].submit();
                        }, 50);
                    }
                    return false;
                }
            });
        });
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            console.log("TEST");
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('deleted'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Dihapus',
                text: '{{ session('deleted') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Validasi',
                html: `<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
            });
        </script>
    @endif
</body>

</html>
