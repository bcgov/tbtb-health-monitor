<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>TBTB Services Status Monitor</title>


        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="/css/app.css" type="text/css" />

        <style>
            .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
                color: #fff;
                background-color: #0d9efd;
            }
        </style>
    </head>
    <body class="d-flex flex-column min-vh-100">
        <header>
            <nav class="navbar fixed-top" id="header-main">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">
                        <img src="https://www2.gov.bc.ca/StaticWebResources/static/gov3/images/gov_bc_logo.svg" alt="Government of B.C." width="175px" height="47" class="d-inline-block align-text-top" title="Government of B.C." />
                        <span class="ms-3 mt-3 text-white float-end">TBTB Services Status Monitor</span>
                    </a>

                    @if (Route::has('login'))
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            @auth
                                <li class="nav-item">
                                    <a href="{{ url('/home') }}" class="nav-link">Home</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a href="{{ route('login') }}" class="nav-link">Log in</a>
                                </li>

                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a href="{{ route('register') }}" class="nav-link">Register</a>
                                    </li>
                                @endif
                            @endauth

                        </ul>
                    </div>
                    @endif
                </div>
            </nav>
        </header>
        <main id="app" class="flex-shrink-0 mt-5 pt-5">
            <div class="container">
                <div class="row mb-3">
                        <div class="col-12">
                            <ul class="nav justify-content-end nav-pills">
                                @if(Auth::check())
                                <li class="nav-item">
                                    <a class="nav-link" href="/">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/services">Services Control</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/shots">Screenshots</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="/service-contacts">Services Contacts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/accounts">Contacts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/logout">Logout</a>
                                </li>
                                @else
                                <li class="nav-item">
                                    <a class="nav-link" href="/login">Login</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                </div>
                <service-contacts-component></service-contacts-component>



            </div>
        </main>

        <footer class="footer mt-auto py-3 bg-light">
            <div class="container">
                <span class="text-muted d-none d-sm-inline-block float-right">Crafted with
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 471.701 471.701" style="enable-background:new 0 0 471.701 471.701;width: 16px;fill: red;" xml:space="preserve"><g><path d="M433.601,67.001c-24.7-24.7-57.4-38.2-92.3-38.2s-67.7,13.6-92.4,38.3l-12.9,12.9l-13.1-13.1   c-24.7-24.7-57.6-38.4-92.5-38.4c-34.8,0-67.6,13.6-92.2,38.2c-24.7,24.7-38.3,57.5-38.2,92.4c0,34.9,13.7,67.6,38.4,92.3   l187.8,187.8c2.6,2.6,6.1,4,9.5,4c3.4,0,6.9-1.3,9.5-3.9l188.2-187.5c24.7-24.7,38.3-57.5,38.3-92.4   C471.801,124.501,458.301,91.701,433.601,67.001z M414.401,232.701l-178.7,178l-178.3-178.3c-19.6-19.6-30.4-45.6-30.4-73.3   s10.7-53.7,30.3-73.2c19.5-19.5,45.5-30.3,73.1-30.3c27.7,0,53.8,10.8,73.4,30.4l22.6,22.6c5.3,5.3,13.8,5.3,19.1,0l22.4-22.4   c19.6-19.6,45.7-30.4,73.3-30.4c27.6,0,53.6,10.8,73.2,30.3c19.6,19.6,30.3,45.6,30.3,73.3   C444.801,187.101,434.001,213.101,414.401,232.701z"></path></g></svg>
                    by the Student Portofolio @ TBTB</span>

                {{--                    <span class="text-muted">Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</span>--}}
            </div>
        </footer>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="/js/app.js?v=1.5"></script>

    </body>
</html>
