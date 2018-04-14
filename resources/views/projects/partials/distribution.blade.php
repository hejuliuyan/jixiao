@extends('projects.detail')

@section('middle')
    @if($project->state < 4 && $currentUser->division == $project->division && $currentUser->hasRole('division_manager'))
        <form class="project-form" method="post" action="{{ route('project.detail.distribution_update', $project->id) }}" accept-charset="UTF-8">
            {{ csrf_field() }}
            <input type="hidden" name="max_num" value="{{ $project->max_num }}">
            <input type="hidden" name="type" value="{{ $project->type }}">

            <div class="sheet-head">
                <div class="pull-left sheet-btn">
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </div>

            <div class="sheet-table">
                @foreach($projectDistributions as $num => $projectDistribution)
                    <table width="80%">
                        <thead>
                        <tr>
                            <th colspan="8">
                                <div class="sheet-item">
                                    @if($project->is_whole)
                                        <span>项目组人员分配--第{{ $num+1 }}次</span>
                                    @else
                                        <span>项目组人员分配</span>
                                    @endif

                                    @if($projectDistribution->status == 0)
                                        <div class="sheet-out">
                                            <button type="button" class="btn btn-primary member-add">添加人员</button>
                                        </div>
                                    @endif
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projectDistribution->distribution as $index => $distribution)
                            @if($projectDistribution->status == 0)
                                <tr>
                                    <td width="10%">项目分工</td>
                                    <td width="18%">
                                        <div class="sheet-item">
                                            <select name="dis_position[]" class="form-control" title="请选择分工类别">
                                                <option value="">请选择</option>
                                                @foreach(config('admin.projects.position') as $key => $value)
                                                    <option value="{{ $key }}" @if($distribution->position == $key) selected @endif>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td width="6%">备注</td>
                                    <td width="14%">
                                        <div class="sheet-item">
                                            <input type="text" name="dis_remark[]" class="form-control" placeholder="10个字符" maxlength="10" title="请填写备注信息" value="{{ $distribution->remark }}">
                                        </div>
                                    </td>
                                    <td width="10%">部门人员</td>
                                    <td width="18%">
                                        <div class="sheet-item">
                                            <select name="dis_member[]" class="form-control" title="请选择部门人员">
                                                <option value="">请选择</option>
                                                @foreach($members as $member)
                                                    <option value="{{ $member->id }}" @if($distribution->user_id == $member->id) selected @endif>{{ $member->fullname }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td width="10%">奖金（元）</td>
                                    <td width="14%">
                                        <div class="sheet-item">
                                            <input type="text" name="dis_bonus[]" class="form-control text-right" title="请填写奖金金额" value="0" readonly/>
                                            <div class="sheet-out sheet-out-lg">
                                                <input type="hidden" name="dis_formula[]" value=""/>
                                                <button type="button" class="btn btn-primary member-del">删除人员</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td width="10%">项目分工</td>
                                    <td width="18%">{{ config('admin.projects.position')[$distribution->position] }}</td>
                                    <td width="6%">备注</td>
                                    <td width="14%">{{ $distribution->remark }}</td>
                                    <td width="10%">部门人员</td>
                                    <td width="18%">{{ $distribution->user->fullname }}</td>
                                    <td width="10%">奖金（元）</td>
                                    <td width="14%">
                                        <div class="sheet-item">
                                            <span>{{ $distribution->bonus }}</span>
                                            @if($distribution->position != 7)
                                                <div class="sheet-out sheet-out-lg">
                                                    <i class="fa fa-info-circle fa-lg" title="公式：{{ $distribution->formula }}"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>

            <div class="sheet-footer">
                <a href="{{ back_url('project.index_flow') }}" class="btn btn-primary">返回</a>
            </div>
        </form>
        <script type="text/javascript">
            //离开提示
            var hasSaved = false;
            window.onbeforeunload = function(event) {
                if(hasSaved == false){
                    return '请保存信息后再离开页面';
                }
            };

            $('form').on('submit', function () {
                hasSaved = true;
            });
        </script>
        <script type="text/javascript">
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
                        '               <input type="hidden" name="dis_formula[]" value=""/>'+
                        '               <button type="button" class="btn btn-primary member-del">删除人员</button>'+
                        '           </div>'+
                        '        </div>'+
                        '    </td>'+
                        '</tr>';
            }
        </script>
    @elseif($project->state == 4 && $currentUser->ability('division_manager', 'project.flow.bonus'))

        <form class="project-form" method="post" action="{{ route('project.detail.distribution_update', $project->id) }}" accept-charset="UTF-8">
            {{ csrf_field() }}
            <input type="hidden" name="type" value="{{ $project->type }}">
            <input type="hidden" name="max_num" value="{{ $project->max_num }}">
            <input type="hidden" name="tender_total" value="{{ $projectPayment->tender_total ?: 0 }}">
            <input type="hidden" name="consult_total" value="{{ $projectPayment->consult_total ?: 0  }}">

            <div class="sheet-head">
                <div class="pull-left sheet-btn">
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </div>

            <div class="sheet-table">
                @foreach($projectDistributions as $num => $projectDistribution)
                    <table width="80%">
                        <thead>
                        <tr>
                            <th colspan="8">
                                <div class="sheet-item">
                                    @if($project->is_whole)
                                        <span>项目组人员分配--第{{ $num+1 }}次</span>
                                    @else
                                        <span>项目组人员分配</span>
                                    @endif

                                    @if($projectDistribution->status == 0)
                                        <div class="sheet-out">
                                            <button type="button" class="btn btn-primary member-add">添加人员</button>
                                        </div>
                                    @endif
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projectDistribution->distribution as $index => $distribution)
                            @if($projectDistribution->status == 0)
                                <tr>
                                    <td width="10%">项目分工</td>
                                    <td width="18%">
                                        <div class="sheet-item">
                                            <select name="dis_position[]" class="form-control" title="请选择分工类别">
                                                <option value="">请选择</option>
                                                @foreach(config('admin.projects.position') as $key => $value)
                                                    <option value="{{ $key }}" @if($distribution->position == $key) selected @endif>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td width="6%">备注</td>
                                    <td width="14%">
                                        <div class="sheet-item">
                                            <input type="text" name="dis_remark[]" class="form-control" placeholder="10个字符" maxlength="10" title="请填写备注信息" value="{{ $distribution->remark }}">
                                        </div>
                                    </td>
                                    <td width="10%">部门人员</td>
                                    <td width="18%">
                                        <div class="sheet-item">
                                            <select name="dis_member[]" class="form-control" title="请选择部门人员">
                                                <option value="">请选择</option>
                                                @foreach($members as $member)
                                                    <option value="{{ $member->id }}" @if($distribution->user_id == $member->id) selected @endif>{{ $member->fullname }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td width="10%">奖金（元）</td>
                                    <td width="14%">
                                        <div class="sheet-item">
                                            <input type="text" name="dis_bonus[]" class="form-control text-right" title="请填写奖金金额" value="{{ $distribution->bonus }}" readonly/>
                                            <div class="sheet-out sheet-out-lg">
                                                <input type="hidden" name="dis_formula[]" value="{{ $distribution->formula }}"/>
                                                <button type="button" class="btn btn-primary member-del">删除人员</button>
                                                <button type="button" class="btn btn-primary formula-edit">编辑公式</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td width="10%">项目分工</td>
                                    <td width="18%">{{ config('admin.projects.position')[$distribution->position] }}</td>
                                    <td width="6%">备注</td>
                                    <td width="14%">{{ $distribution->remark }}</td>
                                    <td width="10%">部门人员</td>
                                    <td width="18%">{{ $distribution->user->fullname }}</td>
                                    <td width="10%">奖金（元）</td>
                                    <td width="14%">
                                        <div class="sheet-item">
                                            <span>{{ $distribution->bonus }}</span>
                                            @if($distribution->position != 7)
                                                <div class="sheet-out sheet-out-lg">
                                                    <i class="fa fa-info-circle fa-lg" title="公式：{{ $distribution->formula }}"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    @if($distribution->status != 0)
                        <div style="width:80%;height: 90px">
                            <?php $jingying = 0 ?>
                            <?php $zhuanye = 0 ?>
                            <?php $heji = 0 ?>
                            @foreach($projectDistribution->distribution as $index => $distribution)

                                @if(config('admin.projects.position')[$distribution->position] == '项目经营人')
                                    <?php $jingying = $jingying + $distribution->bonus ?>
                                @else
                                    <?php $zhuanye = $zhuanye + $distribution->bonus ?>
                                @endif
                                <?php $heji = $heji + $distribution->bonus ?>
                            @endforeach
                            <div style="float: right">
                                <span>专业人员奖金小计:{{$zhuanye}}元</span><br>
                                <span>经营人员奖金小计:{{$jingying}}元</span><br>
                                <span>合计:{{$heji}}元</span><br>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="sheet-footer">
                <button type="button" class="btn btn-success" onclick="RHA.order(this)" data-action="{{ route('project.detail.distribution_submit', $project->id) }}">提交</button>
                <a href="{{ back_url('project.index_flow') }}" class="btn btn-primary">返回</a>
            </div>
        </form>
        <script type="text/javascript">
            //离开提示
            var hasSaved = false;
            window.onbeforeunload = function(event) {
                if(hasSaved == false){
                    return '请保存信息后再离开页面';
                }
            };

            $('form').on('submit', function () {
                hasSaved = true;
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('input[name="dis_formula[]"]').each(function(){
                    if(isEmpty($(this).val())) {
                        initBonusData(this)
                    }
                });
            });

            $(document).on('change',
            'select[name="dis_position[]"]', function () {
                initBonusData(this)
            });

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

            $(document).on('click', '.formula-edit', function(){
                var _this = $(this);

                swal({
                    html: '<strong>输入公式</strong><input type="text" name="formula-input" class="swal2-input" placeholder="请输入计算公式"/>',
                    confirmButtonText: '确定',
                    showCancelButton: true,
                    cancelButtonText: '取消',
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            var formula_input = $('input[name="formula-input"]').val();

                            try {
                                var formula_res = math_compute(formula_input);
                                _this.parents('tr').find('input[name="dis_bonus[]"]').val(formula_res);
                                _this.siblings('input[name="dis_formula[]"]').val(formula_input);
                                resolve();
                            }catch (e) {
                                reject('公式格式有误，请重新输入');
                            }
                        })
                    },
                    allowOutsideClick: false
                }).catch(swal.noop);

                //初始化数据
                var formula_data = _this.siblings('input[name="dis_formula[]"]').val();
                $('input[name="formula-input"]').val(formula_data);
            });

            //初始化奖金和公式信息
            function initBonusData(obj) {
                var _this = $(obj);
                var _parent = _this.parents('tr');

                _parent.find('.formula-edit').prop('disabled', false);

                var position = _parent.find('select[name="dis_position[]"]').val();
                var tenderTotal = $('input[name="tender_total"]').val();
                var consultTotal = $('input[name="consult_total"]').val();

                var formula = '';
                var bonus = 0;
                var currentTotal = 0;

                //招标或是咨询
                if(inArray([1,3,5,7,9], position)) {
                    currentTotal = tenderTotal;
                }else {
                    currentTotal = consultTotal;
                }

                //计算奖金
                if(position == 5 || position == 6) {
                    formula = currentTotal + '*2%';
                    bonus = math_compute(formula);
                }else if(position == 7 || position == '') {
                    formula = '';
                    bonus = 0;

                    _parent.find('.formula-edit').prop('disabled', true);
                }else {
                    formula = currentTotal + '*14%';
                    bonus = math_compute(formula);
                }

                _parent.find('input[name="dis_formula[]"]').val(formula);
                _parent.find('input[name="dis_bonus[]"]').val(bonus);
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
                        '               <input type="hidden" name="dis_formula[]" value=""/>'+
                        '               <button type="button" class="btn btn-primary member-del">删除人员</button>'+
                        '               <button type="button" class="btn btn-primary formula-edit">编辑公式</button>'+
                        '           </div>'+
                        '        </div>'+
                        '    </td>'+
                        '</tr>';
            }
        </script>
    @elseif($project->state == 6 && $currentUser->ability('chief_engineer', 'project.flow.examine'))

        <form class="project-form" method="post" accept-charset="UTF-8">
            {{ csrf_field() }}
            <input type="hidden" name="max_num" value="{{ $project->max_num }}">

            <div class="sheet-head"></div>

            <div class="sheet-table">
                @foreach($projectDistributions as $num => $projectDistribution)
                    <table width="80%">
                        <thead>
                        <tr>
                            <th colspan="8">
                                <div class="sheet-item">
                                    @if($project->is_whole)
                                        <span>项目组人员分配--第{{ $num+1 }}次</span>
                                    @else
                                        <span>项目组人员分配</span>
                                    @endif

                                    @if($projectDistribution->status == 0)
                                        <div class="sheet-out">
                                            <button type="button" class="btn btn-primary member-add">添加人员</button>
                                        </div>
                                    @endif
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($projectDistribution->distribution as $index => $distribution)
                            @if($projectDistribution->num != $project->max_num)
                                <tr>
                                    <td width="10%">项目分工</td>
                                    <td width="18%">{{ config('admin.projects.position')[$distribution->position] }}</td>
                                    <td width="6%">备注</td>
                                    <td width="14%">{{ $distribution->remark }}</td>
                                    <td width="10%">部门人员</td>
                                    <td width="18%">{{ $distribution->user->fullname }}</td>
                                    <td width="10%">奖金（元）</td>
                                    <td width="14%">
                                        <div class="sheet-item">
                                            <span>{{ $distribution->bonus }}</span>
                                            <div class="sheet-out sheet-out-lg">
                                                <i class="fa fa-info-circle fa-lg" title="公式：{{ $distribution->formula }}"></i>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td width="10%">项目分工</td>
                                    <td width="18%">{{ config('admin.projects.position')[$distribution->position] }}</td>
                                    <td width="6%">备注</td>
                                    <td width="14%">{{ $distribution->remark }}</td>
                                    <td width="10%">部门人员</td>
                                    <td width="18%">{{ $distribution->user->fullname }}</td>
                                    <td width="10%">奖金（元）</td>
                                    <td width="14%">
                                        <div class="sheet-item">
                                            @if($distribution->position == 7)
                                                <input type="hidden" name="dis_member[]" value="{{ $distribution->user_id }}"/>
                                                <input type="text" name="dis_bonus[]" class="form-control text-right" title="请填写奖金金额" value="{{ $distribution->bonus }}" autofocus="autofocus"/>
                                            @else
                                                <span>{{ $distribution->bonus }}</span>
                                                <div class="sheet-out sheet-out-lg">
                                                    <button type="button" class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="right" title="{{ $distribution->formula }}">公式</button>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                @if($distribution->position != 7)
                    <div style="width:80%;height: 90px">
                        <?php $jingying = 0 ?>
                        <?php $zhuanye = 0 ?>
                        <?php $heji = 0 ?>
                        @foreach($projectDistribution->distribution as $index => $distribution)

                            @if(config('admin.projects.position')[$distribution->position] == '项目经营人')
                                <?php $jingying = $jingying + $distribution->bonus ?>
                            @else
                                <?php $zhuanye = $zhuanye + $distribution->bonus ?>
                            @endif
                            <?php $heji = $heji + $distribution->bonus ?>
                        @endforeach
                        <div style="float: right">
                            <span>专业人员奖金小计:{{$zhuanye}}元</span><br>
                            <span>经营人员奖金小计:{{$jingying}}元</span><br>
                            <span>合计:{{$heji}}元</span><br>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            <div class="sheet-footer">
                <button type="button" class="btn btn-success" onclick="RHA.order(this)" data-action="{{ route('project.detail.distribution_operate', $project->id) }}">提交</button>
                <a href="{{ back_url('project.index_flow') }}" class="btn btn-primary">返回</a>
            </div>
        </form>
        <script type="text/javascript">
            //离开提示
            var hasSaved = false;
            window.onbeforeunload = function(event) {
                if(hasSaved == false){
                    return '请保存信息后再离开页面';
                }
            };

            //公式显示
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $('form').on('submit', function () {
                hasSaved = true;
            });
        </script>
    @else

        <div class="sheet-head"></div>

        <div class="sheet-table">
            @foreach($projectDistributions as $num => $projectDistribution)
                @if($currentUser->hasRole(['general_manager', 'personnel_manager', 'chief_engineer', 'finance','file']) ||
                                   !$current_leaders->isEmpty()||
                                   !$current_assist->isEmpty() ||
                                    ($currentUser->hasRole('division_manager') && $currentUser->division == $project->division) )
                    <table width="80%">
                        <thead>
                        <tr>
                            <th colspan="8">
                                <div class="sheet-item">
                                    @if($project->is_whole)
                                        <span>项目组人员分配--第{{ $num+1 }}次</span>
                                    @else
                                        <span>项目组人员分配</span>
                                    @endif
                                </div>
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($projectDistribution->distribution as $index => $distribution)
                            <tr>
                                <td width="10%">项目分工</td>
                                <td width="18%">{{ config('admin.projects.position')[$distribution->position] }}</td>
                                <td width="6%">备注</td>
                                <td width="14%">{{ $distribution->remark }}</td>
                                <td width="10%">部门人员</td>
                                <td width="18%">{{ $distribution->user->fullname }}</td>
                                <td width="10%">奖金（元）</td>
                                <td width="14%">
                                    <div class="sheet-item">
                                        <span>{{ $distribution->bonus }}</span>
                                        @if($distribution->position != 7)
                                            <div class="sheet-out sheet-out-lg">
                                                <button type="button" class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="right" title="{{ $distribution->formula }}">公式</button>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    <div style="width:80%;height: 90px">
                        <?php $jingying = 0 ?>
                        <?php $zhuanye = 0 ?>
                        <?php $heji = 0 ?>
                        @foreach($projectDistribution->distribution as $index => $distribution)

                            @if(config('admin.projects.position')[$distribution->position] == '项目经营人')
                                <?php $jingying = $jingying + $distribution->bonus ?>
                                @else
                                    <?php $zhuanye = $zhuanye + $distribution->bonus ?>
                                @endif
                                <?php $heji = $heji + $distribution->bonus ?>
                            @endforeach
                        <div style="float: right">
                        <span>专业人员奖金小计:{{$zhuanye}}元</span><br>
                        <span>经营人员奖金小计:{{$jingying}}元</span><br>
                        <span>合计:{{$heji}}元</span><br>
                        </div>
                    </div>
                @else
                    <table width="60%">
                        <thead>
                        <tr>
                            <th colspan="6">
                                <div class="sheet-item">
                                    @if($project->is_whole)
                                        <span>项目组人员分配--第{{ $num+1 }}次</span>
                                    @else
                                        <span>项目组人员分配</span>
                                    @endif
                                </div>
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($projectDistribution->distribution as $index => $distribution)
                            <tr>
                                <td width="10%">项目分工</td>
                                <td width="18%">{{ config('admin.projects.position')[$distribution->position] }}</td>
                                <td width="6%">备注</td>
                                <td width="14%">{{ $distribution->remark }}</td>
                                <td width="10%">部门人员</td>
                                <td width="18%">{{ $distribution->user->fullname }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                @endif
            @endforeach
        </div>

        <div class="sheet-footer">
            <a href="{{ back_url('project.index_flow') }}" class="btn btn-primary">返回</a>
        </div>

        <script type="text/javascript">
            //公式显示
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    @endif
@stop
