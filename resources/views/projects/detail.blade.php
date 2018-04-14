@extends('layouts.default')

@section('title')
    @yield('subtitle')项目详情_@parent
@stop

@section('content')
    <div class="crumb-wrap">
        <div class="crumb-list">
            <span><i class="fa fa-th-list"></i>项目管理</span>
            <span class="crumb-step">&gt;</span>
            <span>项目详情</span>
        </div>
    </div>

    <div class="area-wrap">
        @include('error')
        @include('flash::message')

        <div class="sheet">
            <div class="sheet-top">
                <ul class="nav nav-pills">
                    {{--<li class="{{ navViewActive('project.detail.base') }}"><a href="{{ route('project.detail.base', $project->id) }}">基本信息</a></li>--}}
                    {{--<li class="{{ navViewActive('project.detail.payment') }}"><a href="{{ route('project.detail.payment', $project->id) }}">收款信息</a></li>--}}
                    {{--<li class="{{ navViewActive('project.detail.distribution') }}"><a href="{{ route('project.detail.distribution', $project->id) }}">分配信息</a></li>--}}
                    {{--<li class="{{ navViewActive('project.detail.flow') }}"><a href="{{ route('project.detail.flow', $project->id) }}">流转信息</a></li>--}}

                    @foreach($nav_menu as $nav)
                        <li class="{{ navViewActive($nav['route']) }}"><a href="{{ route($nav['route'], $project->id) }}">{{ $nav['title'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="sheet-middle">
                @yield('middle')
            </div>
        </div>
    </div>
@stop