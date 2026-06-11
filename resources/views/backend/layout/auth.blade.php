<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Auth') - G8 Events</title>
    @include('backend.assets.css.css')
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-10">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-6">
                                <div class="welcome-content">
                                    <div class="brand-logo">
                                        <a href="{{ route('home') }}">G8 Events</a>
                                    </div>
                                    <h3 style="padding: 0 0 20px 0;" class="welcome-title">@yield('welcome-title', 'Welcome to Event Management')</h3>
                                    @yield('welcome-content')
                                    <div class="intro-social">
                                        <ul>
                                            <li><a href="https://www.facebook.com/rawcig"><i class="fa fa-facebook"></i></a></li>
                                            <li><a href="https://github.com/rawcig"><i class="fa fa-github"></i></a></li>
                                            <li><a href="https://t.me/rawcig"><i class="fa fa-telegram"></i></a></li>
                                            <li><a href="https://www.outlook.com/sokdara.work@gmail.com"><i class="fa fa-envelope" aria-hidden="true"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                @yield('auth-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.assets.js.js')
</body>
</html>
