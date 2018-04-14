@extends('layouts.default')

@section('title')
    首页_@parent
@stop

@section('content')
    <div class="crumb-wrap">
        <div class="crumb-list">
            <span><i class="fa fa-home"></i>首页</span>
        </div>
    </div>

    <div class="area-wrap">
        <img src="{{ asset('assets/images/index.jpg') }}" alt="欢迎使用咨询项目绩效管理系统">
    </div>

    {{--<script>--}}
        {{--$.notify({--}}
            {{--title: 'Test',--}}
            {{--message: 'Hello World'--}}
        {{--},{--}}
            {{--type: 'danger',--}}
            {{--placement: {--}}
                {{--from: 'top',--}}
                {{--align:  'center'--}}
            {{--}--}}
        {{--});--}}
    {{--</script>--}}
@stop