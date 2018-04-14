@extends('layouts.base')

@section('body')
    @include('layouts.partials.nav')

    <div class="main-wrap">
        @include('layouts.partials.side')

        <div class="content-wrap">
            @yield('content')
        </div>
    </div>
@stop