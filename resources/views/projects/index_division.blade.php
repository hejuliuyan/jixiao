@extends('layouts.default')

@section('title')
    分工列表_@parent
@stop

@section('scripts')
    <script type="text/javascript" src="{{asset('assets/js/laydate/laydate.js')}}"></script>
@stop

@section('content')
    <div class="crumb-wrap">
        <div class="crumb-list">
            <span><i class="fa fa-th-list"></i>项目管理</span>
            <span class="crumb-step">&gt;</span>
            <span>分工列表</span>
        </div>
    </div>

    <div class="area-wrap">
        @include('error')
        @include('flash::message')

        <div class="top-content">
            <form class="form-inline" method="post" action="{{ route('project.search', ['type' => 'division']) }}" accept-charset="UTF-8">
                {{ csrf_field() }}

                <div class="form-group top-item">
                    <label for="start_date" class="control-label">起</label>
                    <input type="text" name="start_date" id="start_date" class="form-control top-date" value="{{ $info['start_date'] }}" readonly/>

                    <label for="end_date" class="control-label">止</label>
                    <input type="text" name="end_date" id="end_date" class="form-control top-date" value="{{ $info['end_date'] }}" readonly/>
                </div>

                <div class="form-group top-item">
                    <label for="s_division" class="control-label">部门</label>
                    <select name="s_division" id="s_division" class="form-control">
                        <option value="1" @if($info['s_division'] == 1) selected @endif>{{ config('admin.sites.division')[1] }}</option>
                        <option value="2" @if($info['s_division'] == 2) selected @endif>{{ config('admin.sites.division')[2] }}</option>
                        <option value="3" @if($info['s_division'] == 3) selected @endif>{{ config('admin.sites.division')[3] }}</option>
                    </select>
                </div>

                <div class="form-group top-item">
                    <label for="s_state" class="control-label">当前状态</label>
                    <select name="s_state" id="s_state" class="form-control">
                        <option value=""  @if($info['s_state'] == null) selected @endif>全部</option>
                        <option value="1" @if($info['s_state'] == 1) selected @endif>已下单</option>
                        <option value="2" @if($info['s_state'] == 2) selected @endif>已收款</option>
                        <option value="4" @if($info['s_state'] == 4) selected @endif>已申请奖金结算</option>
                        <option value="5" @if($info['s_state'] == 5) selected @endif>已分配奖金</option>
                        <option value="6" @if($info['s_state'] == 6) selected @endif>已确认奖金</option>
                        <option value="7" @if($info['s_state'] == 7) selected @endif>已审查</option>
                        <option value="8" @if($info['s_state'] == 8) selected @endif>已终审</option>
                        <option value="3" @if($info['s_state'] == 3) selected @endif>已归档</option>
                        <option value="0" @if($info['s_state'] == 0 && $info['s_state'] != null) selected @endif>已保存</option>
                    </select>
                </div>

                @permission('project.search')
                <div class="form-group top-item">
                    <label for="s_word" class="control-label">编号或名称</label>
                    <input type="text" name="s_word" id="s_word" class="form-control" value="{{ $info['s_word'] }}"/>
                </div>

                <div class="form-group top-item">
                    <button type="submit" class="btn btn-primary">检索</button>
                </div>
                @endpermission

                @permission('project.create')
                <div class="form-group top-item">
                    <a href="javascript:void(0)" class="btn btn-primary" id="add" data-href="{{ route('project.add') }}">新增</a>
                </div>
                @endpermission

                @permission('project.export')
                <div class="form-group top-item">
                    <button type="button" class="btn btn-primary" id="export" data-href="{{ route('project.export') }}">导出已终审报表</button>
                </div>
                @endpermission
            </form>
        </div>

        <div class="middle-content">
            <div class="mi-table">
                <div class="mi-head">
                    <ul class="mi-list-inline">
                        <li class="item-w10">编号</li>
                        <li class="item-w10">部门</li>
                        <li class="item-w30">项目名称</li>
                        <li class="item-w10">当前状态</li>
                        <li class="item-w5">已归档</li>
                        <li class="item-w10">担当角色</li>
                        <li class="item-w15">更新时间</li>
                        <li class="item-w10">操作</li>
                    </ul>
                </div>

                <div class="mi-content">
                    @forelse($projects as $project)
                        <div class="mi-item">
                            <ul class="mi-list-inline">
                                <li class="item-w10">{{ $project->num }}</li>
                                <li class="item-w10">{{ config('admin.sites.division')[$project->division] }}</li>
                                <li class="item-w30">{{ $project->name }}</li>
                                <li class="item-w10">{{ config('admin.projects.state')[$project->state] }}</li>
                                <li class="item-w5">{{ $project->is_filed > 0 ? '是':'否' }}</li>
                                <li class="item-w10">{{ $project->assume }}</li>
                                <li class="item-w15">{{ $project->updated_at }}</li>
                                <li class="item-w10">
                                    @if($project->state == 1)
                                        @if($currentUser->hasRole('division_manager') && $currentUser->division == $project->division)
                                            <a href="javascript:void(0)" onclick="RHAL.update(this)" data-url="{{ route('project.del', ['id' => $project->id]) }}">撤回</a>
                                        @endif
                                    @endif
                                    @if($project->state == 0)
                                        @if($currentUser->hasRole('division_manager') && $currentUser->division == $project->division)
                                            <a href="{{ route('project.edit', ['id' => $project->id]) }}">编辑</a>
                                        @endif
                                    @else
                                        @if($currentUser->hasRole(['general_manager', 'personnel_manager', 'chief_engineer', 'finance', 'file']) ||
                                        count($project->current_leaders) > 0 ||
                                        ($currentUser->hasRole('division_manager') && $currentUser->division == $project->division))
                                            <a href="{{ route('project.detail.flow', ['id' => $project->id]) }}">详情</a>
                                        @else
                                            <a href="{{ route('project.detail.base', ['id' => $project->id]) }}">详情</a>
                                        @endif
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
                    {!! $projects->appends($info)->links() !!}
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            //绑定日期
            var start = {
                elem: '#start_date', //选择ID为START的input
                format: 'YYYY-MM-DD',
                istoday: false,  //是否是当天
                choose: function(datas){
                    end.min = datas; //开始日选好后，重置结束日的最小日期
                    end.start = datas; //将结束日的初始值设定为开始日
                }
            };

            var end = {
                elem: '#end_date',
                format: 'YYYY-MM-DD',
                istoday: false,  //是否是当天
                choose: function(datas){
                    start.max = datas; //结束日选好后，重置开始日的最大日期
                }
            };

            laydate(start);
            laydate(end);
        });

        //新增
        $('#add').on('click', function(){
            var _this = $(this);
            var href = _this.data('href');

            //年份数组
            var yearObj = [];
            var year = new Date().getFullYear();
            for(var i = 0; i < 10; i++) {
                var new_year = year - i;
                yearObj[new_year+'y'] = new_year + '年';
            }

            //模态框
            swal({
                html: '<strong>请选择年份</strong>',
                width: 350,
                input: 'select',
                inputOptions: yearObj,
                showCancelButton: true,
                confirmButtonText: '确定',
                cancelButtonText:'取消'
            }).then(function(result){
                window.location.href = href + '?year=' + result.replace(/y/, '');
            }).catch(swal.noop);
        });

        //导出
        $('#export').on('click', function () {
            var _this = $(this);
            var href = _this.data('href');

            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var division = $('#s_division').val();

            window.location.href = href + "?start_date=" + start_date + "&end_date=" + end_date + "&division=" + division;
        });

        var RHAL = {
            update: function (obj) {
                var _this = $(obj);
                var _url = _this.data('url');

                swal({
                    html: '<strong>确定撤回该订单吗</strong>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve) {
                            $.ajax({
                                url: _url,
                                method: 'POST',
                                dataType: 'json'
                            }).done(function(data) {
                                resolve();
                                alert(data.msg);
                                if(data.result == 1) {
                                    window.location.reload();
                                }
                            })
                        });
                    }
                }).catch(swal.noop);
            },
        };
    </script>
@stop