@extends('layouts.default')

@section('title')
    新增角色_@parent
@stop

@section('content')
    <div class="crumb-wrap">
        <div class="crumb-list">
            <span><i class="fa fa-cog"></i>系统管理</span>
            <span class="crumb-step">&gt;</span>
            <span>新增角色</span>
        </div>
    </div>

    <div class="area-wrap">
        @include('error')

        <form method="post" class="form-horizontal insert-form" action="{{ route('role.create') }}" accept-charset="UTF-8">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="name" class="col-sm-2 control-label"><i>*</i>角色简称：</label>
                <div class="col-sm-4">
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"/>
                </div>
            </div>

            <div class="form-group">
                <label for="display_name" class="col-sm-2 control-label"><i>*</i>角色名称：</label>
                <div class="col-sm-4">
                    <input type="text" name="display_name" id="display_name" class="form-control" value="{{ old('display_name') }}"/>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="col-sm-2 control-label"><i>*</i>角色说明：</label>
                <div class="col-sm-4">
                    <input type="text" name="description" id="description" class="form-control" value="{{ old('description') }}"/>
                </div>
            </div>

            <div class="form-group">
                <label for="permission" class="col-sm-2 control-label">权限分配：</label>
                <div class="col-sm-10">
                    <div class="checkbox">
                        @foreach($perms_data as $value)
                            <div class="checkbox">
                                <label title="{{ $value['parent']['description'] }}"><input type="checkbox" name="permission[]" value="{{ $value['parent']['id'] }}"/>{{ $value['parent']['display_name'] }}</label>
                            </div>
                            <ul class="checkbox">
                                @foreach($value['child'] as $child_data)
                                    <li>
                                        @foreach($child_data as $child)
                                            <label title="{{ $child['description'] }}"><input type="checkbox" name="permission[]" value="{{ $child['id'] }}"/>{{ $child['display_name'] }}</label>
                                        @endforeach
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-4">
                    <button type="submit" class="btn btn-primary">提交</button>
                    <a href="{{ back_url('role') }}" class="btn btn-primary" title="返回角色列表">返回</a>
                </div>
            </div>
        </form>
    </div>
@stop