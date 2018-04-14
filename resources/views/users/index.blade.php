@extends('layouts.default')

@section('title')
    用户列表_@parent
@stop

@section('content')
    <div class="crumb-wrap">
        <div class="crumb-list">
            <span><i class="fa fa-cog"></i>系统管理</span>
            <span class="crumb-step">&gt;</span>
            <span>用户列表</span>
        </div>
    </div>

    <div class="area-wrap">
        @include('flash::message')

        <div class="top-content">
            <form class="form-inline" method="post" action="{{ route('user.search') }}" accept-charset="UTF-8">
                {{ csrf_field() }}

                @permission('user.search')
                <div class="form-group top-item">
                    <label for="s_division" class="control-label">部门</label>
                    <select name="s_division" id="s_division" class="form-control">
                        @foreach( config('admin.sites.division') as $key => $value)
                            <option value="{{ $key }}" @if($info['s_division'] == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group top-item">
                    <label for="s_name" class="control-label">姓名</label>
                    <input type="text" name="s_name" id="s_name" class="form-control" value="{{ $info['s_name'] }}"/>
                </div>

                <div class="form-group top-item">
                    <button type="submit" class="btn btn-primary">检索</button>
                </div>
                @endpermission

                @permission('user.add')
                <div class="form-group top-item">
                    <a href="{{ route('user.add') }}" class="btn btn-primary" title="新增用户">新增</a>
                </div>
                @endpermission
            </form>
        </div>

        <div class="middle-content">
            <div class="mi-table">
                <div class="mi-head">
                    <ul class="mi-list-inline">
                        <li class="item-w10">序号</li>
                        <li class="item-w15">姓名</li>
                        <li class="item-w15">部门</li>
                        <li class="item-w15">角色</li>
                        <li class="item-w15">修改时间</li>
                        <li class="item-w15">创建时间</li>
                        <li class="item-w15">操作</li>
                    </ul>
                </div>

                <div class="mi-content">
                    @forelse($users as $key => $value)
                        <div class="mi-item">
                            <ul class="mi-list-inline">
                                <li class="item-w10">{{ ($users->currentPage() - 1) * $users->perPage() + $key + 1 }}</li>
                                <li class="item-w15">{{ $value->name }}</li>
                                <li class="item-w15">{{ $value->division_text }}</li>
                                <li class="item-w15">{{ $value->roles_name }}</li>
                                <li class="item-w15">{{ $value->updated_at }}</li>
                                <li class="item-w15">{{ $value->created_at }}</li>
                                <li class="item-w15">
                                    @permission('user.edit')
                                    <a href="{{ route('user.edit', ['id' => $value->id]) }}">编辑</a>
                                    @endpermission

                                    @if($value->is_super == 0)
                                        @permission('user.del')
                                        <a href="javascript:void(0)" onclick="RHA.delete(this)" data-url="{{ route('user.del', ['id' => $value->id]) }}">删除</a>
                                        @endpermission
                                    @endif
                                </li>
                            </ul>
                        </div>
                    @empty
                        <div class="mi-item">
                            <ul class="mi-list-inline">
                                <li class="item-w100">暂无数据</li>
                            </ul>
                        </div>
                    @endforelse
                </div>

                <!-- 分页 -->
                <div class="mi-footer">
                    {!! $users->appends($info)->links() !!}
                </div>
            </div>
        </div>
    </div>
@stop