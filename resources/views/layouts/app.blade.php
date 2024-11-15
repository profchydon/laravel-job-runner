<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'Job Logger' }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    {{-- <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet"> --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@100;300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>

        body {
            margin: 0px;
        }

        .login-header {
            font-weight: 600;
        }

        .form-div {
            text-align: left;
        }


        .margin-tb {
            margin: 20px 0px;
        }

        .login-header-2 {
            font-size: 16px;
            font-weight: 400;
        }

        .py-4 {
            padding-top: 1.5rem!important;
            padding-bottom: 1.5rem!important;
            background: #F9FAFB !important;
        }

        .form-div {
            background: white;
            padding: 20px 40px;
            margin: 40px 40px 20px 40px;
            box-shadow: rgba(16, 24, 40, 0.06) 0px 1px 2px 0px, rgba(16, 24, 40, 0.1) 0px 1px 3px 0px;
            border-radius: 12px;
        }

        input {
            height: 45px;
        }

        nav {
            color: white;
        }

    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light" style="background: #101828; padding: 12px 0px;">
            <div class="container">
                <h4>Job Logger</h4>
            </div>
        </nav>

        <main class="py-5">
            @yield('content')
        </main>
    </div>
</body>
</html>
