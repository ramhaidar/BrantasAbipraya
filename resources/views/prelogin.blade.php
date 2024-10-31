<!doctype html>
<html lang=en>

<head>
    <meta charset=UTF-8>
    <meta name=viewport content="width=device-width,initial-scale=1">
    <title>Home | DDP Digital Logistic</title>
    <link type=image/x-icon href=favicon.ico rel=icon>
    <link href=https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css rel=stylesheet integrity=sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH crossorigin=anonymous>
    <link href=https://fonts.googleapis.com rel=preconnect>
    <link href=https://fonts.gstatic.com rel=preconnect crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel=stylesheet>
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
    </style>
</head>

<body>
    <div class="container-fluid text-center">
        <div class="row align-items-center min-vh-100">
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
                            <img class="d-block w-100" src="imageOne.jpg" alt="">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="imageTwo.jpg" alt="">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="imageThree.jpg" alt="">
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
            <div class="col-lg-4 align-items-center d-flex flex-column justify-content-center">
                <p class="fs-1 mt-5 fw-semibold">Mesin Berat, Masa Depan<br>Pembangunan</p>
                <p class="fs-5 text-dark text-opacity-50">Masuk dan nikmati pengalaman yang terbaik</p>
                <a class="btn btn-warning text-light fs-5 fw-medium my-5" href="{{ route('login') }}" role="button" style="--bs-btn-padding-x: 2rem; --bs-btn-padding-y: 1rem; --bs-btn-bg: #E99D02;">Lanjutkan dengan Nama Pengguna</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
