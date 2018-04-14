@extends('layouts.base')

@section('title')
   修改密码_@parent
@stop

@section('body')
    @include('layouts.partials.nav')

    <div class="main-wrap">
        <div class="widget-wrap">
            @include('error')

            <form class="form-horizontal insert-form" role="form" method="post" action="{{ route('user.update_password') }}" accept-charset="UTF-8">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="old_password" class="control-label col-sm-3"><i>*</i>原密码：</label>
                    <div class="col-sm-9">
                        <input type="password" name="old_password" id="old_password" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="control-label col-sm-3"><i>*</i>新密码：</label>
                    <div class="col-sm-9">
                        <input type="password" name="password" id="password" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="control-label col-sm-3"><i>*</i>重输新密码：</label>
                    <div class="col-sm-9">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <button type="submit" class="btn btn-primary">提交</button>
                        <a href="{{ back_url('index') }}" class="btn btn-primary">返回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop