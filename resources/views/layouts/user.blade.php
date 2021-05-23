<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name', 'Brokerlive') }}</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/font-awesome.css" rel="stylesheet">

    <link href="/css/animate.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/jquery-ui.css" rel="stylesheet">
    <link href="/css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <link href="/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
</head>

<body class="gray-bg">
    <div class="loginColumns animated fadeInDown">
        <div class="row">
            <div class="col-md-6">
                <h2 class="font-bold">{{ __('Welcome to Brokerlive') }}</h2>
            </div>
            <div class="col-md-6">
                @yield('content')
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6">
                {{ __('Copyright Brokerlive') }}
            </div>
            <div class="col-md-6 text-right">
               <small>© 2020</small>
            </div>
        </div>
    </div>
    <?php if(isset($_SESSION["login"])){echo $_SESSION["login"];unset($_SESSION["login"]);}?>
    <script src="/js/jquery-2.1.1.js" ></script>
    <script src="/js/jquery-ui-1.10.4.min.js" ></script>
    <script src="/js/bootstrap.min.js" ></script>
    <script src="/js/plugins/toastr/toastr.min.js" ></script>
    <script src="/js/brokerlive/brokerlive.notification.js" ></script>
    @include('common.notification.notification')
</body>

</html>
