@extends('projects.detail')

@section('middle')
    @if($project->state < 4 && $currentUser->division == $project->division && $currentUser->hasRole('division_manager'))
        <form class="project-form" method="post" action="{{ route('project.detail.base_update', $project->id) }}" accept-charset="UTF-8">
            {{ csrf_field() }}
            <input type="hidden" name="max_num" value="{{ $project->max_num }}">

            <div class="sheet-head">
                <div class="pull-left sheet-btn">
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </div>

            <div class="sheet-table">
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
                                <input type="text" name="contract_num" class="form-control" title="请填写咨询合同编号" maxlength="50" value="{{ $project->contract_num }}"/>
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
                                <input type="text" name="depute_phone" class="form-control" title="请填写委托单位联系电话" placeholder="请输入11位手机号或区号-座机号码" value="{{ $project->depute_phone }}"/>
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
                                                <label><input type="checkbox" name="category[]" value="{{ $key }}" checked disabled>&nbsp;{{ $value }}</label>

                                                @if($key == 13)
                                                    <input type="text" name="category_text" placeholder="请输入类别" value="{{ $project->category_text }}">
                                                @endif
                                            </li>
                                        @else
                                            <li><label><input type="checkbox" name="category[]" value="{{ $key }}" disabled>&nbsp;{{ $value }}</label></li>
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
                                    {{--<li><i class="fa fa-circle"></i>提取项目信息：--}}
                                        {{--单项工程--}}
                                        {{--（--}}
                                        {{--<select name="single" title="单项工程">--}}
                                            {{--<option value="1" @if($project->single == 1) selected @endif>是</option>--}}
                                            {{--<option value="0" @if($project->single == 0) selected @endif>否</option>--}}
                                        {{--</select>--}}
                                        {{--）；--}}
                                        {{--单位工程--}}
                                        {{--（--}}
                                        {{--<select name="unit" title="单位工程">--}}
                                            {{--<option value="1" @if($project->unit == 1) selected @endif>是</option>--}}
                                            {{--<option value="0" @if($project->unit == 0) selected @endif>否</option>--}}
                                        {{--</select>--}}
                                        {{--）；--}}
                                        {{--不提取--}}
                                        {{--（--}}
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
                                            <option value="1" @if($project->record == 1) selected @endif>是</option>
                                            <option value="0" @if($project->record == 0) selected @endif>否</option>
                                        </select>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="sheet-footer">
                <a href="{{ back_url('project.index_flow') }}" class="btn btn-primary">返回</a>
            </div>
        </form>
        <script type="text/javascript">
            var hasSaved = false;
            window.onbeforeunload = function(event) {
                if(hasSaved == false){
                    return '请保存信息后再离开页面';
                }
            };

            $('form').on('submit', function () {
                hasSaved = true;
            });

            $('input[name="cost"]').on('change', function () {
                var value = $(this).val();

                if(!isEmpty(value) &&!checkNum(value)) {
                    $(this).val('');
                    alert('工程造价只能为数字，最高保留4位小数');
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
                    alert('合同费用只能为数字，最高保留4位小数');
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
        </script>
    @elseif($project->state < 8 && $is_filed == 0 && $currentUser->ability('file', 'project.flow.file'))
        <form class="project-form" method="post" accept-charset="UTF-8">
            {{ csrf_field() }}
            <input type="hidden" name="max_num" value="{{ $project->max_num }}">

            <div class="sheet-head"></div>

            <div class="sheet-table">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td width="20%">编号</td>
                        <td width="30%">{{ $project->num }}</td>
                        <td width="20%">发起日期</td>
                        <td width="30%">{{ date('Y年m月d日', strtotime($project->created_at)) }}</td>
                    </tr>

                    <tr>
                        <td>项目名称</td>
                        <td colspan="3">{{ $project->name }}</td>
                    </tr>

                    <tr>
                        <td>咨询合同编号</td>
                        <td colspan="3">
                            <div class="sheet-item" style="width: 22%">
                                <input type="text" name="contract_num" class="form-control" title="请填写咨询合同编号" maxlength="50" value="{{ $project->contract_num }}" autofocus="autofocus"/>
                            </div>
                            <div class="sheet-item" style="width: 17%">工程造价（万元）</div>
                            <div class="sheet-item" style="width: 22%">{{ $project->cost }}</div>
                            <div class="sheet-item" style="width: 17%">合同费用（万元）</div>
                            <div class="sheet-item" style="width: 22%">{{ $project->contract_price }}</div>
                        </td>
                    </tr>

                    <tr>
                        <td rowspan="2">委托单位</td>
                        <td colspan="3">{{ $project->depute_name }}</td>
                    </tr>

                    <tr>
                        <td colspan="3">
                            <div class="sheet-item" style="width: 20%">联系人</div>
                            <div class="sheet-item" style="width: 30%">{{ $project->depute_user }}</div>
                            <div class="sheet-item" style="width: 20%">联系电话</div>
                            <div class="sheet-item" style="width: 30%">{{ $project->depute_phone }}</div>
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
                                                <label><input type="checkbox" name="category[]" value="{{ $key }}" checked disabled>&nbsp;{{ $value }}</label>

                                                @if($key == 13)
                                                    <input type="text" name="category_text" placeholder="请输入类别" value="{{ $project->category_text }}">
                                                @endif
                                            </li>
                                        @else
                                            <li><label><input type="checkbox" name="category[]" value="{{ $key }}" disabled>&nbsp;{{ $value }}</label></li>
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
                                    <li>
                                        <i class="fa fa-circle"></i>提取项目信息：
                                        单项工程（@if($project->single == 1) 是 @else 否 @endif）；
                                        单位工程（@if($project->unit == 1) 是 @else 否 @endif）；
                                        不提取（@if($project->extract == 1) 是 @else 否 @endif）
                                    </li>
                                    <li>
                                        <i class="fa fa-circle"></i>是否进行咨询合同备案： @if($project->record == 1) 是 @else 否 @endif
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="sheet-footer">
                <button type="button" class="btn btn-success" onclick="RHA.order(this)" data-action="{{ route('project.detail.base_file', $project->id) }}">提交</button>
                <a href="{{ back_url('project.index_flow') }}" class="btn btn-primary">返回</a>
            </div>
        </form>
    @else
        <div class="sheet-head"></div>

        <div class="sheet-table">
            <table width="100%">
                <tbody>
                <tr>
                    <td width="20%">编号</td>
                    <td width="30%">{{ $project->num }}</td>
                    <td width="20%">发起日期</td>
                    <td width="30%">{{ date('Y年m月d日', strtotime($project->created_at)) }}</td>
                </tr>

                <tr>
                    <td>项目名称</td>
                    <td colspan="3">{{ $project->name }}</td>
                </tr>

                <tr>
                    <td>咨询合同编号</td>
                    <td colspan="3">
                        <div class="sheet-item" style="width: 22%">{{ $project->contract_num }}</div>
                        <div class="sheet-item" style="width: 17%">工程造价（万元）</div>
                        <div class="sheet-item" style="width: 22%">{{ $project->cost }}</div>
                        <div class="sheet-item" style="width: 17%">合同费用（万元）</div>
                        <div class="sheet-item" style="width: 22%">{{ $project->contract_price }}</div>
                    </td>
                </tr>

                <tr>
                    <td rowspan="2">委托单位</td>
                    <td colspan="3">{{ $project->depute_name }}</td>
                </tr>

                <tr>
                    <td colspan="3">
                        <div class="sheet-item" style="width: 20%">联系人</div>
                        <div class="sheet-item" style="width: 30%">{{ $project->depute_user }}</div>
                        <div class="sheet-item" style="width: 20%">联系电话</div>
                        <div class="sheet-item" style="width: 30%">{{ $project->depute_phone }}</div>
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
                                            <label><input type="checkbox" name="category[]" value="{{ $key }}" checked disabled>&nbsp;{{ $value }}</label>

                                            @if($key == 13)
                                                <input type="text" name="category_text" placeholder="请输入类别" value="{{ $project->category_text }}">
                                            @endif
                                        </li>
                                    @else
                                        <li><label><input type="checkbox" name="category[]" value="{{ $key }}" disabled>&nbsp;{{ $value }}</label></li>
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
                                <li>
                                    <i class="fa fa-circle"></i>提取项目信息：
                                    单项工程（@if($project->single == 1) 是 @else 否 @endif）；
                                    单位工程（@if($project->unit == 1) 是 @else 否 @endif）；
                                    不提取（@if($project->extract == 1) 是 @else 否 @endif）
                                </li>
                                <li>
                                    <i class="fa fa-circle"></i>是否进行咨询合同备案： @if($project->record == 1) 是 @else 否 @endif
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="sheet-footer">
            <a href="{{ back_url('project.index_flow') }}" class="btn btn-primary">返回</a>
        </div>
    @endif
@stop
