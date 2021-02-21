<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Kusikusi Dev' }}</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/atelier-heath-dark.min.css" />
        <style>
            body {
                font-family: monospace;
                background-color: #ccc;
            }
            h1, h2, h3, h4, h5 {
                font-weight: normal;
            }
            h1 {
                font-size: 1em;
            }
            main, header, footer {
                box-sizing: border-box;
                max-width: 80em;
                margin: 0 auto;
            }
            main {
                background-color: #fff;
                padding: 2em;
            }
            table {
                border: 1px solid lightgrey;
                margin-bottom: 1em;
                border-collapse: collapse;
            }
            table td, table th {
                padding: 0.25em;
                border: 1px solid lightgrey;
                text-align: left;
            }
            a {
                text-decoration-style: dotted;
            }
            label {
                display: block;
            }
        </style>
    </head>
    <body class="antialiased">
        <header>
            <h1><a href="{{ route('welcome') }}">Kusikusi Dev</a></h1>
        </header>
        <main>
             {{ $slot }}
        </main>
        <footer>
            <p>Kusikusi project for dev purposes</p>
        </footer>
    </body>
</html>
