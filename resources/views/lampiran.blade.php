<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ABSN | {{ $filename }}</title>
        <link rel="icon" href="{{ asset('img/logo-mak-text.png') }}">
        <style>
            body,
            html {
                margin: 0;
                max-width: 100vw;
                overflow-x: hidden;
                overflow-y: auto;
            }

            iframe {
                width: 100%;
                height: 100vh;
                border: none;
                display: block;
            }

            /* tambahkan CSS untuk menampilkan gambar atau PDF dengan fullscreen */
            .fullscreen {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                border: none;
                display: block;
                max-width: 100%;
                max-height: 100vh;
                object-fit: contain;
            }

            .image-fullheight {
                position: relative;
                width: 100vw;
                height: 100vh;
                object-fit: cover;
                overflow-y: auto;
            }
        </style>
    </head>

    <body>
        @if ($type == 'image')
            <!-- menampilkan gambar dengan fullscreen -->
            <div class="image-fullheight">
                <img src="{{ asset($path) }}" alt="Gambar">
            </div>
        @elseif($type == 'pdf')
            <!-- menampilkan PDF dengan fullscreen menggunakan iframe -->
            <iframe class="fullscreen" src="{{ asset($path) }}" frameborder="0" allowfullscreen></iframe>
        @else
            <!-- menampilkan pesan error jika tipe file tidak diketahui -->
            <p>Error: Tipe file tidak diketahui.</p>
        @endif
    </body>

</html>
