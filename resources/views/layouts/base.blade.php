<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@section('title')上海瑞和工程咨询项目绩效管理系统@show</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta name="_token" content="{{ csrf_token() }}"/>
    <!-- Core JS -->
    <script type="text/javascript" src="{{ elixir('assets/js/scripts.js') }}"></script>
    <script type="text/javascript" src="{{ elixir('assets/js/main.js') }}"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{asset('assets/js/html5shiv.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/respond.min.js')}}"></script>
    <![endif]-->
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ elixir('assets/css/styles.css')  }}">
    @yield('styles')
</head>

<body>
@yield('body')
@yield('scripts')
</body>

</html>