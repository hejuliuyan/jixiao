@extends('layouts.base')

@section('body')
    <div class="widget-wrap">
        <h1 class="login-title">瑞和工程咨询项目绩效管理系统</h1>

        <form method="post" role="form" class="login-form" action="{{ route('auth.login') }}" accept-charset="UTF-8">
            {{ csrf_field() }}

            <div class="form-group">
                <label class="sr-only" for="name">用户名</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-user fa-lg"></i></div>
                    <input type="text" class="form-control" name="name" placeholder="用户名">
                </div>
            </div>

            <div class="form-group">
                <label class="sr-only" for="password">密码</label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-lock fa-lg"></i></div>
                    <input type="password" class="form-control" name="password" placeholder="密码">
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-btn fa-sign-in"></i>&nbsp;登录</button>
            </div>

            @include('error')
        </form>
    </div>

    @include('layouts.partials.footer')
@stop