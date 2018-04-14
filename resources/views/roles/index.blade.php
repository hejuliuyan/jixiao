@extends('layouts.default')

@section('title')
    角色列表_@parent
@stop

@section('content')
    <div class="crumb-wrap">
        <div class="crumb-list">
            <span><i class="fa fa-cog"></i>系统管理</span>
            <span class="crumb-step">&gt;</span>
            <span>角色列表</span>
        </div>
    </div>

    <div class="area-wrap">
        @include('flash::message')

        <div class="top-content">
            @permission('role.add')
            <div class="top-item">
                <a href="{{ route('role.add') }}" class="btn btn-primary" title="新增角色">新增</a>
            </div>
            @endpermission
        </div>

        <div class="middle-content">
            <div class="mi-table">
                <div class="mi-head">
                    <ul class="mi-list-inline">
                        <li class="item-w10">序号</li>
                        <li class="item-w15">角色</li>
                        <li class="item-w15">说明</li>
                        <li class="item-w20">修改时间</li>
                        <li class="item-w20">创建时间</li>
                        <li class="item-w20">操作</li>
                    </ul>
                </div>

                <div class="mi-content">
                    @forelse($roles as $key => $value)
                    <div class="mi-item">
                        <ul class="mi-list-inline">
                            <li class="item-w10">{{ ($roles->currentPage() - 1) * $roles->perPage() + $key + 1 }}</li>
                            <li class="item-w15">{{ $value->display_name }}</li>
                            <li class="item-w15">{{ $value->description }}</li>
                            <li class="item-w20">{{ $value->updated_at }}</li>
                            <li class="item-w20">{{ $value->created_at }}</li>
                            <li class="item-w20">
                                @if($value->name != 'admin')
                                    @permission('role.edit')
                                    <a href="{{ route('role.edit', ['id' => $value->id]) }}">编辑</a>
                                    @endpermission

                                    @permission('role.del')
                                    <a href="javascript:void(0)" onclick="RHA.delete(this)" data-url="{{ route('role.del', ['id' => $value->id]) }}">删除</a>
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
                    {!! $roles->links() !!}
                </div>
            </div>
        </div>
    </div>
@stop