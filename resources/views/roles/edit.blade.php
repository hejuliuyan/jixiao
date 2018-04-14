@extends('layouts.default')

@section('title')
    编辑角色_@parent
@stop

@section('content')
    <div class="crumb-wrap">
        <div class="crumb-list">
            <span><i class="fa fa-cog"></i>系统管理</span>
            <span class="crumb-step">&gt;</span>
            <span>编辑角色</span>
        </div>
    </div>

    <div class="area-wrap">
        @include('error')

        <form method="post" class="form-horizontal insert-form" action="{{ route('role.update', ['id' => $role->id]) }}" accept-charset="UTF-8">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="name" class="col-sm-2 control-label"><i>*</i>角色简称：</label>
                <div class="col-sm-4">
                    <input type="text" name="name" id="name" class="form-control" value="{{ $role->name }}"/>
                </div>
            </div>

            <div class="form-group">
                <label for="display_name" class="col-sm-2 control-label"><i>*</i>角色名称：</label>
                <div class="col-sm-4">
                    <input type="text" name="display_name" id="display_name" class="form-control" value="{{ $role->display_name }}"/>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="col-sm-2 control-label"><i>*</i>角色说明：</label>
                <div class="col-sm-4">
                    <input type="text" name="description" id="description" class="form-control"  value="{{ $role->description }}"/>
                </div>
            </div>

            <div class="form-group">
                <label for="permission" class="col-sm-2 control-label">权限分配：</label>
                <div class="col-sm-10">
                    @foreach($perms_data as $value)
                        <div class="menu">
                            <div class="checkbox">
                                <label title="{{ $value['parent']['description'] }}"><input type="checkbox" name="permission[]" value="{{ $value['parent']['id'] }}" @if($value['parent']['check']) checked @endif/>{{ $value['parent']['display_name'] }}</label>
                            </div>
                            <ul class="checkbox">
                                @foreach($value['child'] as $child_data)
                                    <li>
                                        @foreach($child_data as $child)
                                            <label title="{{ $child['description'] }}"><input type="checkbox" name="permission[]" value="{{ $child['id'] }}" @if($child['check']) checked @endif/>{{ $child['display_name'] }}</label>
                                        @endforeach
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
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