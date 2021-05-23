<!--
*
*  INSPINIA - Responsive Admin Theme
*  version 2.9.3
*
-->

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="_token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Brokerlive') }}</title>

    @include('common.css.css')

    @include($cssPath)

</head>

<body>
    <div id="wrapper">

        @include('common.navbar.navbar')

        <div id="page-wrapper" class="gray-bg dashbard-1">

            @include('common.header.header')

            @include('common.header.title')

            @yield('content')

            @include('common.footer.footer')

        </div>

        @include('common.panel.chat')

        @include('common.navbar.sidebar')

    </div>

    @include('common.js.js')

    @include($jsPath)

    <script>
        var _hide = localStorage.getItem("_hide");
        $('.navbar-minimalize').click(function(){
            if(_hide == null || _hide == 'false') localStorage.setItem("_hide","true");
            if(_hide == 'true') localStorage.setItem("_hide","false");
        })

        if(_hide == 'true')document.body.className += ' mini-navbar';
    </script>

</body>
</html>
