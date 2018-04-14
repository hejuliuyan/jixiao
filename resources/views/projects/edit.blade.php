@extends('layouts.default')

@section('title')
    编辑项目_@parent
@stop

@section('content')
    <div class="crumb-wrap">
        <div class="crumb-list">
            <span><i class="fa fa-th-list"></i>项目管理</span>
            <span class="crumb-step">&gt;</span>
            <span>编辑项目</span>
        </div>
    </div>

    <div class="area-wrap">
        @include('error')
        @include('flash::message')

        <div class="sheet">
            <div class="sheet-top">
                <ul class="nav nav-pills">
                    <li class="active"><a href="#base" data-toggle="tab">基本信息</a></li>
                    <li><a href="#distribution" data-toggle="tab">分配信息</a></li>
                </ul>
            </div>

            <div class="sheet-middle">
                <form class="project-form" method="post" action="{{ route('project.update', $project->id) }}" accept-charset="UTF-8">
                    {{ csrf_field() }}

                    <div class="sheet-head">
                        <div class="pull-left sheet-btn">
                            <button type="submit" class="btn btn-primary">保存</button>
                        </div>
                    </div>

                    <div class="sheet-table">
                        <div class="tab-content">
                            <div class="tab-pane active" id="base">
                                <table width="100%">
                                    <tbody>
                                    <tr>
                                        <td width="20%">编号</td>
                                        <td width="30%">
                                            <div class="sheet-item">{{ $project->num }}</div>
                                        </td>
                                        <td width="20%">发起日期</td>
                                        <td width="30%">{{ date('Y年m月d日', strtotime($project->created_at)) }}</td>
                                    </tr>

                                    <tr>
                                        <td>项目名称</td>
                                        <td colspan="3">
                                            <div class="sheet-item">
                                                <input type="text" name="name" class="form-control" title="请填写项目名称" value="{{ $project->name }}"/>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>咨询合同编号</td>
                                        <td colspan="3">
                                            <div class="sheet-item" style="width: 22%">
                                                <input type="text" name="contract_num" class="form-control" maxlength="50" title="请填写咨询合同编号" value="{{ $project->contract_num }}"/>
                                            </div>
                                            <div class="sheet-item" style="width: 17%">工程造价（万元）</div>
                                            <div class="sheet-item" style="width: 22%">
                                                <input type="text" name="cost" class="form-control" title="请填写工程造价" value="{{ $project->cost }}"/>
                                            </div>
                                            <div class="sheet-item" style="width: 17%">合同费用（万元）</div>
                                            <div class="sheet-item" style="width: 22%">
                                                <input type="text" name="contract_price" class="form-control" title="请填写合同费用" value="{{ $project->contract_price }}"/>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td rowspan="2">委托单位</td>
                                        <td colspan="3">
                                            <div class="sheet-item">
                                                <input type="text" name="depute_name" class="form-control" title="请填写委托单位名称" value="{{ $project->depute_name }}"/>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="3">
                                            <div class="sheet-item" style="width: 20%">联系人</div>
                                            <div class="sheet-item" style="width: 30%">
                                                <input type="text" name="depute_user" class="form-control" maxlength="50" title="请填写委托联系人" value="{{ $project->depute_user }}"/>
                                            </div>
                                            <div class="sheet-item" style="width: 20%">联系电话</div>
                                            <div class="sheet-item" style="width: 30%">
                                                <input type="text" name="depute_phone" class="form-control" title="请填写委托单位联系电话"  placeholder="请输入11位手机号或区号-座机号码" value="{{ $project->depute_phone }}"/>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>项目类别</td>
                                        <td colspan="3">
                                            <div class="sheet-box">
                                                <ul>
                                                    @foreach(config('admin.projects.category') as $key => $value)
                                                        @if(in_array($key, $project->category_arr))
                                                            <li>
                                                                <label><input type="checkbox" name="category[]" value="{{ $key }}" checked>&nbsp;{{ $value }}</label>

                                                                @if($key == 13)
                                                                    <input type="text" name="category_text" placeholder="请输入类别" value="{{ $project->category_text }}">
                                                                @endif
                                                            </li>
                                                        @else
                                                            <li><label><input type="checkbox" name="category[]" value="{{ $key }}">&nbsp;{{ $value }}</label></li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>项目要求</td>
                                        <td colspan="3">
                                            <div class="sheet-item">
                                                <ul>
                                                    <li><i class="fa fa-circle"></i>要求完成日期（未按时完成需说明原因）：根据业主要求</li>
                                                    {{--<li><i class="fa fa-circle"></i>提取项目信息：单项工程（--}}
                                                        {{--<select name="single" title="单项工程">--}}
                                                            {{--<option value="0" @if($project->single == 0) selected @endif>否</option>--}}
                                                            {{--<option value="1" @if($project->single == 1) selected @endif>是</option>--}}
                                                        {{--</select>--}}
                                                        {{--）；单位工程（--}}
                                                        {{--<select name="unit" title="单位工程">--}}
                                                            {{--<option value="0" @if($project->unit == 0) selected @endif>否</option>--}}
                                                            {{--<option value="1" @if($project->unit == 1) selected @endif>是</option>--}}
                                                        {{--</select>--}}
                                                        {{--）；不提取（--}}
                                                        {{--<select name="extract" title="不提取">--}}
                                                            {{--<option value="1" @if($project->extract == 1) selected @endif>是</option>--}}
                                                            {{--<option value="0" @if($project->extract == 0) selected @endif>否</option>--}}
                                                        {{--</select>--}}
                                                        {{--）--}}
                                                    {{--</li>--}}
                                                    <li><i class="fa fa-circle"></i>提取项目信息：
                                                        <input type="checkbox" name="single" @if($project->single == 1) checked @endif value="1">单项工程&nbsp;&nbsp;
                                                        <input type="checkbox" name="unit" @if($project->unit == 1) checked @endif value="1">单位工程&nbsp;&nbsp;
                                                        {{--<input type="checkbox" name="extract" value="1">单位工程--}}
                                                        提取（
                                                        <select name="extract" title="提取">
                                                            <option value="1" @if(old('extract') === '1') selected @endif>是</option>
                                                            <option value="0" @if(old('extract') === '0') selected @endif>否</option>
                                                        </select>
                                                        ）
                                                    </li>
                                                    <li><i class="fa fa-circle"></i>是否进行咨询合同备案：
                                                        <select name="record" title="是否进行咨询合同备案">
                                                            <option value="0" @if($project->record == 0) selected @endif>否</option>
                                                            <option value="1" @if($project->record == 1) selected @endif>是</option>
                                                        </select>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane" id="distribution">
                                <table width="80%">
                                    <thead>
                                    <tr>
                                        <th colspan="8">
                                            <div class="sheet-item">
                                                <span>项目组人员分配</span>
                                                <div class="sheet-out">
                                                    <button type="button" class="btn btn-primary member-add">添加人员</button>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($distributions as $distribution)
                                        <tr>
                                            <td width="10%">项目分工</td>
                                            <td width="18%">
                                                <div class="sheet-item">
                                                    <select name="dis_position[]" class="form-control" title="请选择分工类别">
                                                        <option value="">请选择</option>
                                                        @foreach(config('admin.projects.position') as $key => $value)
                                                            <option value="{{ $key }}" @if($key == $distribution['position']) selected @endif>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td width="6%">备注</td>
                                            <td width="14%">
                                                <div class="sheet-item">
                                                    <input type="text" name="dis_remark[]" class="form-control" placeholder="10个字符" maxlength="10" title="请填写备注信息" value="{{ $distribution['remark'] }}">
                                                </div>
                                            </td>
                                            <td width="10%">部门人员</td>
                                            <td width="18%">
                                                <div class="sheet-item">
                                                    <select name="dis_member[]" class="form-control" title="请选择部门人员">
                                                        <option value="">请选择</option>
                                                        @foreach($members as $member)
                                                            <option value="{{ $member->id }}" @if($member->id == $distribution['user_id']) selected @endif>{{ $member->fullname }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <td width="10%">奖金（元）</td>
                                            <td width="14%">
                                                <div class="sheet-item">
                                                    <input type="text" name="dis_bonus[]" class="form-control text-right" title="请填写奖金金额" value="0" readonly/>
                                                    <div class="sheet-out sheet-out-lg">
                                                        <button type="button" class="btn btn-primary member-del">删除人员</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="sheet-footer">
                <button type="button" class="btn btn-success" onclick="RHA.order(this)" data-action="{{ route('project.order', ['id' => $project->id]) }}">下单</button>
                <a href="{{ back_url('project.index_flow') }}" class="btn btn-primary">返回</a>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('input[name="cost"]').on('change', function () {
            var value = $(this).val();

            if(!isEmpty(value) &&!checkNum(value)) {
                $(this).val('');
                alert('工程造价只能为数字，最高保留四位小数');
            }

            if(value >= 1000000) {
                $(this).val('');
                alert('工程造价不能超过1000000万元');
            }
        });

        $('input[name="contract_price"]').on('change', function () {
            var value = $(this).val();

            if(!isEmpty(value) &&!checkNum(value)) {
                $(this).val('');
                alert('合同费用只能为数字，最高保留四位小数');
            }

            if(value >= 1000000) {
                $(this).val('');
                alert('合同费用不能超过10000万元');
            }
        });

        $('input[name="depute_phone"]').on('change', function () {
            var value = $(this).val();

            if(!isEmpty(value) && !isTelOrMobile(value)) {
                $(this).val('');
                alert('委托联系电话格式不正确');
            }
        });

        $(document).ready(function(){
            var categoryInput = $('input[name="category[]"]');
            //项目类别控制
            categoryInput.each(function(){
                var _this = $(this);
                var tenderArr = [1,2,3,4,12];

                if(_this.is(':checked')) {
                    if(inArray(tenderArr, _this.val())) {
                        categoryInput.each(function(){
                            if($(this).val() == 10) {
                                $(this).attr('disabled', true);
                            }
                        });
                    }

                    if(_this.val() == 10) {
                        categoryInput.each(function(){
                            if(inArray(tenderArr, $(this).val()) ) {
                                $(this).attr('disabled', true);
                            }
                        });
                    }
                }
            });
        })
    </script>

    <script type="text/javascript">
        var pastType = parseInt('{{ $project->type }}');

        //切换提示
        $('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
            var _this = $(this);
            var chooseType = true;

            if(_this.attr('href') == '#base') {
                $('input[name="category[]"]').each(function(){
                    if($(this).is(':checked')) {
                        chooseType = false;
                    }
                });

                if(chooseType) {
                    alert('请先选择项目类别');
                    return false;
                }
            }
        });

        //项目类别
        $('input[name="category[]"]').on('click', function(){
            var _this = $(this);
            var categoryInput = $('input[name="category[]"]');

            var tenderArr = [1,2,3,4,12]; //招标
            var consultArr = [5,6,7,8,9,10,11,13]; //咨询

            //项目类型之间的控制
            if(_this.is(':checked')) {
                if(_this.val() == 13){
                    _this.parent().after('<input type="text" name="category_text" placeholder="请输入类别">');
                }else if(_this.val() == 10) { //全过程造价
                    categoryInput.each(function(){
                        if(inArray(tenderArr, $(this).val())) {
                            $(this).attr('disabled', true);
                        }
                    });
                }else {
                    if(inArray(tenderArr, _this.val())) {
                        categoryInput.each(function(){
                            if($(this).val() == 10) {
                                $(this).attr('disabled', true);
                            }
                        });
                    }
                }
            }else {
                if(_this.val() == 13){
                    _this.parent().next().remove();
                }else if(_this.val() == 10) {
                    categoryInput.each(function(){
                        if(inArray(tenderArr, $(this).val())) {
                            $(this).removeAttr('disabled').prop('disabled', false);
                        }
                    });
                }else { //招标类
                    var flag = true;
                    categoryInput.each(function(){
                        if($(this).is(':checked') && inArray(tenderArr, $(this).val())) {
                            flag = false;
                        }
                    });

                    if(flag) {
                        categoryInput.each(function(){
                            if($(this).val() == 10) {
                                $(this).removeAttr('disabled').prop('disabled', false);
                            }
                        });
                    }
                }
            }

            //获取初始项目类型
            var selectArr = [];
            var currentType = 0;
            var tender_flag = false;
            var consult_flag = false;

            categoryInput.each(function(){
                if($(this).is(':checked')) {
                    selectArr.push($(this).val());
                }
            });

            if(selectArr.length != 0) {
                for (var i = 0; i < selectArr.length; i++) {
                    if(inArray(tenderArr, selectArr[i])) {
                        tender_flag = true;
                    }

                    if(inArray(consultArr, selectArr[i])) {
                        consult_flag = true;
                    }
                }

                if(tender_flag && !consult_flag) {
                    currentType = 1;
                }else if(!tender_flag && consult_flag){
                    currentType = 2;
                }else{
                    currentType = 3;
                }
            }

            if(pastType != 0 && currentType != pastType) {
                swal({
                    html: '<strong>项目类型改变，会删除旧分配信息，确定继续吗</strong>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    showLoaderOnConfirm: true
                }).then(function(){
                    memberByType(currentType);
                },function(dismiss){
                    if (dismiss === 'cancel') {
                        if(_this.is(':checked')) {
                            _this.attr('checked', false);
                        }else {
                            _this.attr('checked', true);
                        }
                    }
                }).catch(swal.noop);
            }

            if(pastType == 0 && currentType != 0) {
                memberByType(currentType);
            }
        });

        function memberByType(type) {
            var strHtml = '';
            if(type == 1) {
                strHtml = tenderReqHtml();
            }else if(type == 2) {
                strHtml = consultReqHtml();
            }else {
                strHtml = tenderReqHtml() + consultReqHtml();
            }
            $('#distribution').find('tbody').html(strHtml);

            pastType = type;
        }

        //添加人员
        $(document).on('click', '.member-add', function () {
            var _this = $(this);
            var table_body = _this.parents('table').find('tbody');

            if(table_body.children().length >= 20) {
                alert('人员不能超过20个');
                return false;
            }

            var strHtml = memberHtml();
            table_body.append(strHtml);
        });

        //删除人员
        $(document).on('click', '.member-del', function () {
            var _this = $(this);

            _this.attr('disabled', true);

            swal({
                html: '<strong>是否确定删除吗</strong>',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                showLoaderOnConfirm: true
            }).then(function(){
                _this.parents('tr').remove();
            },function(dismiss){
                if (dismiss === 'cancel') {
                    _this.attr('disabled', false);
                }
            }).catch(swal.noop);
        });

        function tenderReqHtml() {
            return  '<tr>'+
                    '    <td width="10%">项目分工</td>'+
                    '    <td width="18%">'+
                    '        <div class="sheet-item">'+
                    '            <select name="dis_position[]" class="form-control" title="请选择分工类别">'+
                    '               <option value="">请选择</option>'+
                    @foreach(config('admin.projects.position') as $key => $value)
                            '               <option value="{{ $key }}" @if($key == 1) selected @endif>{{ $value }}</option>'+
                    @endforeach
                            '            </select>'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="6%">备注</td>'+
                    '    <td width="14%">'+
                    '        <div class="sheet-item">'+
                    '            <input type="text" name="dis_remark[]" class="form-control" placeholder="10个字符" maxlength="10" title="请填写备注信息" >'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="10%">部门人员</td>'+
                    '    <td width="18%">'+
                    '        <div class="sheet-item">'+
                    '            <select name="dis_member[]" class="form-control" title="请选择部门人员">'+
                    '               <option value="">请选择</option>'+
                    @foreach($members as $member)
                            '               <option value="{{ $member->id }}">{{ $member->fullname }}</option>'+
                    @endforeach
                            '            </select>'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="10%">奖金（元）</td>'+
                    '    <td width="14%">'+
                    '        <div class="sheet-item">'+
                    '            <input type="text" name="dis_bonus[]" class="form-control text-right" title="请填写奖金金额" value="0" readonly/>'+
                    '           <div class="sheet-out sheet-out-lg">'+
                    '               <button type="button" class="btn btn-primary member-del">删除人员</button>'+
                    '           </div>'+
                    '        </div>'+
                    '    </td>'+
                    '</tr>'+
                    '<tr>'+
                    '    <td width="10%">项目分工</td>'+
                    '    <td width="18%">'+
                    '        <div class="sheet-item">'+
                    '            <select name="dis_position[]" class="form-control" title="请选择分工类别">'+
                    '               <option value="">请选择</option>'+
                    @foreach(config('admin.projects.position') as $key => $value)
                            '               <option value="{{ $key }}" @if($key == 5) selected @endif>{{ $value }}</option>'+
                    @endforeach
                            '            </select>'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="6%">备注</td>'+
                    '    <td width="14%">'+
                    '        <div class="sheet-item">'+
                    '            <input type="text" name="dis_remark[]" class="form-control" placeholder="10个字符" maxlength="10" title="请填写备注信息" >'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="10%">部门人员</td>'+
                    '    <td width="18%">'+
                    '        <div class="sheet-item">'+
                    '            <select name="dis_member[]" class="form-control" title="请选择部门人员">'+
                    '               <option value="">请选择</option>'+
                    @foreach($members as $member)
                            '               <option value="{{ $member->id }}">{{ $member->fullname }}</option>'+
                    @endforeach
                            '            </select>'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="10%">奖金（元）</td>'+
                    '    <td width="14%">'+
                    '        <div class="sheet-item">'+
                    '            <input type="text" name="dis_bonus[]" class="form-control text-right" title="请填写奖金金额" value="0" readonly/>'+
                    '           <div class="sheet-out sheet-out-lg">'+
                    '               <button type="button" class="btn btn-primary member-del">删除人员</button>'+
                    '           </div>'+
                    '        </div>'+
                    '    </td>'+
                    '</tr>';

        }

        function consultReqHtml() {
            return  '<tr>'+
                    '    <td width="10%">项目分工</td>'+
                    '    <td width="18%">'+
                    '        <div class="sheet-item">'+
                    '            <select name="dis_position[]" class="form-control" title="请选择分工类别">'+
                    '               <option value="">请选择</option>'+
                    @foreach(config('admin.projects.position') as $key => $value)
                            '               <option value="{{ $key }}" @if($key == 2) selected @endif>{{ $value }}</option>'+
                    @endforeach
                            '            </select>'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="6%">备注</td>'+
                    '    <td width="14%">'+
                    '        <div class="sheet-item">'+
                    '            <input type="text" name="dis_remark[]" class="form-control" placeholder="10个字符" maxlength="10" title="请填写备注信息" >'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="10%">部门人员</td>'+
                    '    <td width="18%">'+
                    '        <div class="sheet-item">'+
                    '            <select name="dis_member[]" class="form-control" title="请选择部门人员">'+
                    '               <option value="">请选择</option>'+
                    @foreach($members as $member)
                            '               <option value="{{ $member->id }}">{{ $member->fullname }}</option>'+
                    @endforeach
                            '            </select>'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="10%">奖金（元）</td>'+
                    '    <td width="14%">'+
                    '        <div class="sheet-item">'+
                    '            <input type="text" name="dis_bonus[]" class="form-control text-right" title="请填写奖金金额" value="0" readonly/>'+
                    '           <div class="sheet-out sheet-out-lg">'+
                    '               <button type="button" class="btn btn-primary member-del">删除人员</button>'+
                    '           </div>'+
                    '        </div>'+
                    '    </td>'+
                    '</tr>'+
                    '<tr>'+
                    '    <td width="10%">项目分工</td>'+
                    '    <td width="18%">'+
                    '        <div class="sheet-item">'+
                    '            <select name="dis_position[]" class="form-control" title="请选择分工类别">'+
                    '               <option value="">请选择</option>'+
                    @foreach(config('admin.projects.position') as $key => $value)
                            '               <option value="{{ $key }}" @if($key == 6) selected @endif>{{ $value }}</option>'+
                    @endforeach
                            '            </select>'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="6%">备注</td>'+
                    '    <td width="14%">'+
                    '        <div class="sheet-item">'+
                    '            <input type="text" name="dis_remark[]" class="form-control" placeholder="10个字符" maxlength="10" title="请填写备注信息" >'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="10%">部门人员</td>'+
                    '    <td width="18%">'+
                    '        <div class="sheet-item">'+
                    '            <select name="dis_member[]" class="form-control" title="请选择部门人员">'+
                    '               <option value="">请选择</option>'+
                    @foreach($members as $member)
                            '               <option value="{{ $member->id }}">{{ $member->fullname }}</option>'+
                    @endforeach
                            '            </select>'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="10%">奖金（元）</td>'+
                    '    <td width="14%">'+
                    '        <div class="sheet-item">'+
                    '            <input type="text" name="dis_bonus[]" class="form-control text-right" title="请填写奖金金额" value="0" readonly/>'+
                    '           <div class="sheet-out sheet-out-lg">'+
                    '               <button type="button" class="btn btn-primary member-del">删除人员</button>'+
                    '           </div>'+
                    '        </div>'+
                    '    </td>'+
                    '</tr>';

        }

        function memberHtml() {
            return  '<tr>'+
                    '    <td width="10%">项目分工</td>'+
                    '    <td width="18%">'+
                    '        <div class="sheet-item">'+
                    '            <select name="dis_position[]" class="form-control" title="请选择分工类别">'+
                    '               <option value="">请选择</option>'+
                    @foreach(config('admin.projects.position') as $key => $value)
                            '               <option value="{{ $key }}">{{ $value }}</option>'+
                    @endforeach
                            '            </select>'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="6%">备注</td>'+
                    '    <td width="14%">'+
                    '        <div class="sheet-item">'+
                    '            <input type="text" name="dis_remark[]" class="form-control" placeholder="10个字符" maxlength="10" title="请填写备注信息" >'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="10%">部门人员</td>'+
                    '    <td width="18%">'+
                    '        <div class="sheet-item">'+
                    '            <select name="dis_member[]" class="form-control" title="请选择部门人员">'+
                    '               <option value="">请选择</option>'+
                    @foreach($members as $member)
                            '               <option value="{{ $member->id }}">{{ $member->fullname }}</option>'+
                    @endforeach
                            '            </select>'+
                    '        </div>'+
                    '    </td>'+
                    '    <td width="10%">奖金（元）</td>'+
                    '    <td width="14%">'+
                    '        <div class="sheet-item">'+
                    '            <input type="text" name="dis_bonus[]" class="form-control text-right" title="请填写奖金金额" value="0" readonly/>'+
                    '           <div class="sheet-out sheet-out-lg">'+
                    '               <button type="button" class="btn btn-primary member-del">删除人员</button>'+
                    '           </div>'+
                    '        </div>'+
                    '    </td>'+
                    '</tr>';
        }
    </script>
@stop
