@extends('layouts.default')

@section('title')
    编辑用户_@parent
@stop

@section('content')
    <div class="crumb-wrap">
        <div class="crumb-list">
            <span><i class="fa fa-cog"></i>系统管理</span>
            <span class="crumb-step">&gt;</span>
            <span>编辑用户</span>
        </div>
    </div>

    <div class="area-wrap">
        @include('error')

        <form method="post" class="form-horizontal insert-form" action="{{ route('user.update', ['id' => $user->id]) }}" accept-charset="UTF-8">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="name" class="col-sm-2 control-label"><i>*</i>姓名：</label>
                <div class="col-sm-4">
                    <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}"/>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="col-sm-2 control-label">邮箱：</label>
                <div class="col-sm-4">
                    <input type="text" name="email" id="email" class="form-control" value="{{ $user->email }}"/>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="col-sm-2 control-label"><i>*</i>密码：</label>
                <div class="col-sm-4">
                    <input type="password" name="password" id="password" class="form-control"/>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="col-sm-2 control-label"><i>*</i>重输密码：</label>
                <div class="col-sm-4">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"/>
                </div>
            </div>

            @if($user->is_super != 1)
                <div class="form-group">
                    <label for="is_banned" class="col-sm-2 control-label">禁用：</label>
                    <div class="col-sm-4">
                        <select name="is_banned" id="is_banned" class="form-control">
                            <option value="0" @if($user->is_banned == 0) selected @endif>否</option>
                            <option value="1" @if($user->is_banned == 1) selected @endif>是</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="division" class="col-sm-2 control-label">部门：</label>
                    <div class="col-sm-4">
                        <select name="division" id="division" class="form-control">
                            <option value="0" @if($user->division == 0) selected @endif>请选择</option>
                            @foreach( config('admin.sites.division') as $key => $value)
                                <option value="{{ $key }}" @if($user->division == $key) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="role" class="col-sm-2 control-label">角色：</label>
                    <div class="col-sm-10">
                        <div class="checkbox">
                            @foreach($role_data as $role)
                                <label title="{{ $role['description'] }}"><input type="checkbox" name="role[]" value="{{ $role['id'] }}" @if($role['check']) checked @endif/>{{ $role['display_name'] }}</label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-4">
                    <button type="submit" class="btn btn-primary">提交</button>
                    <a href="{{ back_url('user') }}" class="btn btn-primary" title="返回用户列表">返回</a>
                </div>
            </div>
        </form>
    </div>
@stop