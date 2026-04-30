<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
        <title>{{ $title }}</title>
    </head>
    <body>
        <footer>
            <div>{{ $formatVersion }}</div>
            <div>
                Página <span class="page-number"></span> de <span class="total-pages"></span>
            </div>
        </footer>
        {{ $slot }}
    </body>
</html>
