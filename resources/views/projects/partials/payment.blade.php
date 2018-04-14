@extends('projects.detail')

@section('middle')

    @if(($project->state == 1 || $project->state == 3) && $currentUser->ability('finance', 'project.flow.charge'))
        {{--已下单或者已归档状态--}}

        <form class="project-form" method="post" action="{{ route('project.detail.payment_update', $project->id) }}" accept-charset="UTF-8">
            {{ csrf_field() }}
            <input type="hidden" name="max_num" value="{{ $project->max_num }}">

            <div class="sheet-head">
                <div class="pull-left sheet-btn">
                    <button type="submit" class="btn btn-primary">保存</button>
                    @if($project->is_whole)
                        <button type="button" class="btn btn-primary" id="payment-add">添加收款</button>
                    @endif
                </div>
                <div class="pull-right">
                    <ul class="sheet-head-text">
                        {{--<li>待分配奖金：<span>{{ number_format($project->income_total*0.16 - $project->distribution_total, 2) }}</span></li>--}}
                        {{--<li>已分配奖金：<span>{{ number_format($project->distribution_total, 2) }}</span></li>--}}
{{--                        <li>收款总额：<span>{{ number_format($project->income_total, 2) }}</span></li>--}}
                        <li>收款总额：<span id="zonge">@if(!$project->projectPayment->isEmpty())
                                    {{ number_format($project->projectPayment->sum('total'), 2) }}
                                           @else
                                           0.00
                                @endif</span></li>
                    </ul>
                </div>
            </div>

            <div class="sheet-table">
                <table width="100%">
                    <thead>
                    <tr>
                        <th width="6%">No</th>
                        <th width="6%">分配</th>
                        <th width="13%">收款明目</th>
                        <th width="13%">收款金额</th>
                        <th width="13%">收款明目</th>
                        <th width="13%">收款金额</th>
                        <th width="13%">收款明目</th>
                        <th width="13%">收款金额</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payments as $num => $payment)
                        @if($payment->projectPayment->status == 1)
                            {{--已经提交收款--}}
                            <tr>
                                <td rowspan="2">{{ $num+1 }}</td>
                                <td rowspan="2">{{ $payment->projectPayment->num }}</td>
                                <td>{{ $payment->type == 1 ? '收入_招标':'收入_咨询' }}</td>
                                <td>
                                    <div>
                                        <div @if(!empty($payment->accounts->income))style="width: 60%;float: left;margin-top: 10px;"@endif>{{ $payment->accounts->income }}</div>
                                        @if(!empty($payment->accounts->income))
                                        <div style="width: 40%;float: right;margin-top: 5px;" >
                                            <button type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="income">查看</button>
                                        </div>
                                            @endif
                                    </div>
                                </td>
                                <td>专家评审费_收取</td>
                                <td>
                                    <div>
                                        <div @if(!empty($payment->accounts->assess_income))style="width: 60%;float: left;margin-top: 10px;"@endif>{{ $payment->accounts->assess_income }}</div>
                                        @if(!empty($payment->accounts->assess_income))
                                        <div style="width: 40%;float: right;margin-top: 5px;" >
                                            <button type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="assess_income">查看</button>
                                        </div>
                                            @endif
                                    </div>
                                </td>
                                <td>专家评审费_支出</td>
                                <td>
                                    <div>
                                        <div @if(!empty($payment->accounts->assess_pay))style="width: 60%;float: left;margin-top: 10px;"@endif>{{ $payment->accounts->assess_pay }}</div>
                                        @if(!empty($payment->accounts->assess_pay))
                                        <div style="width: 40%;float: right;margin-top: 5px;" >
                                            <button type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="assess_pay">查看</button>
                                        </div>
                                            @endif
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>标书工本费_收取</td>
                                <td>
                                    <div>
                                        <div @if(!empty($payment->accounts->flat_pay))style="width: 60%;float: left;margin-top: 10px;"@endif>{{ $payment->accounts->flat_pay }}</div>
                                        @if(!empty($payment->accounts->flat_pay))
                                        <div style="width: 40%;float: right;margin-top: 5px;" >
                                            <button type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="flat_pay">查看</button>
                                        </div>
                                            @endif
                                    </div>
                                </td>
                                <td>其他</td>
                                <td>
                                    <div>
                                        <div @if(!empty($payment->accounts->other_pay))style="width: 60%;float: left;margin-top: 10px;"@endif> {{ $payment->accounts->other_pay }}</div>
                                        @if(!empty($payment->accounts->other_pay))
                                        <div style="width: 40%;float: right;margin-top: 5px;" >
                                            <button type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="other_pay">查看</button>
                                        </div>
                                            @endif
                                    </div>
                                </td>
                                <td><strong>小计</strong></td>
                                <td><strong>{{ $payment->subtotal }}</strong></td>
                            </tr>
                            <script>
                                var is_end = "true";
                            </script>
                        @else
                            <tr class="input-{{ $num+1 }}">
                                <td rowspan="2">{{ $num+1 }}</td>
                                <td rowspan="2">{{ $payment->projectPayment->num }}</td>
                                <td>
                                    <div class="sheet-item">
                                        <span>{{ $payment->type == 1 ? '收入_招标':'收入_咨询' }}</span>
                                        <input type="hidden" name="type[]" value="{{ $payment->type }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="sheet-item">
                                        <input style="width: 60%;float: left;" type="text" class="form-control" name="income[]" data-num="{{ $num+1 }}" value="{{ $payment->accounts->income }}" readonly>
                                        <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="income">编辑</button>

                                    </div>
                                </td>
                                <td>专家评审费_收取</td>
                                <td>
                                    <div class="sheet-item">
                                        <input style="width: 60%;float: left;" type="text" class="form-control" name="assess_income[]" data-num="{{ $num+1 }}" value="{{ $payment->accounts->assess_income }}" readonly>
                                        <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="assess_income">编辑</button>
                                    </div>
                                </td>
                                <td>专家评审费_支出</td>
                                <td>
                                    <div class="sheet-item">
                                        <input style="width: 60%;float: left;" type="text" class="form-control" name="assess_pay[]" data-num="{{ $num+1 }}" value="{{ $payment->accounts->assess_pay }}" readonly>
                                        <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="assess_pay">编辑</button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="input-{{ $num+1 }}">
                                <td>标书工本费_收取</td>
                                <input type="hidden" name="biaozhi[]" value="{{ $num+1 }}">
                                <td>
                                    <div class="sheet-item">
                                        <input style="width: 60%;float: left;" type="text" class="form-control" name="flat_pay[]" data-num="{{ $num+1 }}" value="{{ $payment->accounts->flat_pay }}" readonly>
                                        <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="flat_pay">编辑</button>

                                    </div>
                                </td>
                                <td>其他</td>
                                <td>
                                    <div class="sheet-item">
                                        <input style="width: 60%;float: left;" type="text" class="form-control" name="other_pay[]" data-num="{{ $num+1 }}" value="{{ $payment->accounts->other_pay }}" readonly>
                                        <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="other_pay">编辑</button>
                                    </div>
                                </td>
                                <td><strong>小计</strong></td>
                                <td>
                                    <div class="sheet-item">
                                        <strong class="subtotal"  data-num="{{ $num + 1 }}">{{ $payment->subtotal }}</strong>
                                        <input type="hidden" data-num="{{ $num + 1 }}" name="subtotal[]" value="{{ $payment->subtotal }}">
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        @if($project->type == 1)
                            @include('projects.partials._payment', ['num' => 1, 'type' => 1,'pid'=>$id])
                        @elseif($project->type == 2)
                            @include('projects.partials._payment', ['num' => 1, 'type' => 2,'pid'=>$id])
                        @elseif($project->type == 3)
                            @include('projects.partials._payment', ['num' => 1, 'type' => 1,'pid'=>$id])
                            @include('projects.partials._payment', ['num' => 2, 'type' => 2,'pid'=>$id])
                        @endif
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="sheet-footer">
                <button type="button" class="btn btn-success" onclick="RHA.order(this)" data-action="{{ route('project.detail.payment_submit', $project->id) }}">提交</button>
                <a href="{{ back_url('project.index_flow') }}" class="btn btn-primary">返回</a>
            </div>
        </form>
        <script src="http://code.jquery.com/jquery-2.0.0.min.js"></script>
        <script src="{{asset('layer/layer.js')}}"></script>
        <script>
            $(document).on('click','.money-edit',function () {
                var type = $(this).data('type');
                var num = $(this).data('num');
                var pid = "{{$id}}";
                layer.open({
                    type: 2,
                    title: '收支记录',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['90%', '90%'],
                    content: '{{url('/logs?')}}'+'num='+num+'&type='+type+'&pid='+pid, //iframe的url
                });
            })
        </script>
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
            //小计计算
            $(document).on('change', 'input[type="text"]', function () {
                var _this = $(this);
                var num = _this.data('num');
                var item = $('.input-'+num);

                var value = _this.val();
                if(!isEmpty(value) &&!checkNum(value)) {
                    $(this).val('');
                    alert('输入只能为数字，最高保留2位小数');
                }

                //小计
                var subtotal = 0;
                item.find('input[type="text"]').each(function () {
                    var input_value = $(this).val();
                    var input_name = $(this).attr('name');

                    if(!isEmpty(input_value) && (input_name == "income[]" || input_name == "assess_income[]" || input_name == "flat_pay[]")) {
                        subtotal += parseFloat(input_value);
                    }

                    if(!isEmpty(input_value) && (input_name == "assess_pay[]" || input_name == "other_pay[]")) {
                        subtotal -= parseFloat(input_value);
                    }
                });

//                if(subtotal < 0) {
//                    $(this).val('');
//                    alert('小计不能小于零');return false;
//                }

                item.find('.subtotal').html(subtotal);
                item.find('input[name="subtotal[]"]').val(subtotal);
            });

            //添加收款
            $(document).on('click', '#payment-add', function() {
                var _this = $(this);
                var table_body = $('.sheet-table tbody');

                var maxNum = $('input[name="max_num"]').val();
                var paymentCount = table_body.find('tr').length;
                paymentCount = paymentCount/2;

                var strHtml = paymentHtml(paymentCount + 1, maxNum);
                table_body.append(strHtml);
            });

            function paymentHtml(num, times) {
                return  '<tr class="input-'+num+'">'+
                        '    <td rowspan="2">'+num+'</td>'+
                        '<input type="hidden" name="biaozhi[]" value="'+ num +'">'+
                        '    <td rowspan="2">'+times+'</td>'+
                        '    <td>'+
                        '        <div class="sheet-item">'+
                        '           <span>收入_咨询</span>'+
                        '           <input type="hidden" name="type[]" value="2">'+
                        '        </div>'+
                        '    </td>'+
                        '    <td>'+
                        '        <div class="sheet-item">'+
                        '            <input readonly style="width: 60%;float: left;" type="text" class="form-control" name="income[]" data-num="'+num+'">'+
                        '            <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-type="income" data-num="'+num+'">编辑</button>'+
                        '        </div>'+
                        '    </td>'+
                        '    <td>专家评审费_收取</td>'+
                        '    <td>'+
                        '        <div class="sheet-item">'+
                        '            <input readonly style="width: 60%;float: left;" type="text" class="form-control" name="assess_income[]" data-num="'+num+'">'+
                        '            <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-type="assess_income" data-num="'+num+'">编辑</button>'+
                        '        </div>'+
                        '    </td>'+
                        '    <td>专家评审费_支出</td>'+
                        '    <td>'+
                        '        <div class="sheet-item">'+
                        '            <input readonly style="width: 60%;float: left;" type="text" class="form-control" name="assess_pay[]" data-num="'+num+'">'+
                        '            <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-type="assess_pay" data-num="'+num+'">编辑</button>'+
                        '        </div>'+
                        '    </td>'+
                        '</tr>'+
                        '<tr class="input-'+num+'">'+
                        '    <td>标书工本费_收取</td>'+
                        '    <td>'+
                        '        <div class="sheet-item">'+
                        '            <input readonly style="width: 60%;float: left;" type="text" class="form-control" name="flat_pay[]" data-num="'+num+'">'+
                        '            <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-type="flat_pay" data-num="'+num+'">编辑</button>'+
                        '        </div>'+
                        '    </td>'+
                        '    <td>其他</td>'+
                        '    <td>'+
                        '        <div class="sheet-item">'+
                        '            <input readonly style="width: 60%;float: left;" type="text" class="form-control" name="other_pay[]" data-num="'+num+'">'+
                        '            <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-type="other_pay" data-num="'+num+'">编辑</button>'+
                        '        </div>'+
                        '    </td>'+
                        '    <td><strong>小计</strong></td>'+
                        '    <td>'+
                        '       <div class="sheet-item">'+
                        '           <strong class="subtotal" data-num="'+num+'"></strong>'+
                        '           <input type="hidden" name="subtotal[]" value="" data-num="'+num+'">'+
                        '       </div>'+
                        '    </td>'+
                        '</tr>';
            }
        </script>
    @else
        <div class="sheet-head">
            <div class="pull-right">
                <ul class="sheet-head-text">
                    {{--<li>待分配奖金：<span>{{ number_format($project->income_total*0.16 - $project->distribution_total, 2) }}</span></li>--}}
                    {{--<li>已分配奖金：<span>{{ number_format($project->distribution_total, 2) }}</span></li>--}}
                    {{--<li>收款总额：<span id="zonge">{{ number_format($project->income_total, 2) }}</span></li>--}}
                    <li>收款总额：<span id="zonge">@if(!$project->projectPayment->isEmpty())
                                {{ number_format($project->projectPayment->sum('total'), 2) }}
                            @else
                                0.00
                            @endif</span></li>
                </ul>
            </div>
        </div>

        <div class="sheet-table">
            <table width="100%">
                <thead>
                <tr>
                    <th width="6%">序号</th>
                    <th width="6%">分配</th>
                    <th width="13%">收款明目</th>
                    <th width="13%">收款金额</th>
                    <th width="13%">收款明目</th>
                    <th width="13%">收款金额</th>
                    <th width="13%">收款明目</th>
                    <th width="13%">收款金额</th>
                </tr>
                </thead>

                <tbody>
                @forelse($payments as $num => $payment)
                    {{--已经收款--}}
                    @if($payment->projectPayment->status == 1)
                        <tr>
                            <td rowspan="2">{{ $num+1 }}</td>
                            <td rowspan="2">{{ $payment->projectPayment->num }}</td>
                            <td>{{ $payment->type == 1 ? '收入_招标':'收入_咨询' }}</td>
                            <td>
                                <div>
                                    <div @if(!empty($payment->accounts->income))style="width: 60%;float: left;margin-top: 10px;"@endif>{{ $payment->accounts->income }}</div>
                                    @if(!empty($payment->accounts->income))
                                    <div style="width: 40%;float: right;margin-top: 5px;" >
                                        <button type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="income">查看</button>
                                    </div>
                                        @endif
                                </div>
                            </td>
                            <td>专家评审费_收取</td>
                            <td>
                                <div>
                                    <div @if(!empty($payment->accounts->assess_income))style="width: 60%;float: left;margin-top: 10px;"@endif>{{ $payment->accounts->assess_income }}</div>
                                    @if(!empty($payment->accounts->assess_income))
                                    <div style="width: 40%;float: right;margin-top: 5px;" >
                                        <button type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="assess_income">查看</button>
                                    </div>
                                        @endif
                                </div>
                            </td>
                            <td>专家评审费_支出</td>
                            <td>
                            <div>
                                <div @if(!empty($payment->accounts->assess_pay))style="width: 60%;float: left;margin-top: 10px;"@endif>{{ $payment->accounts->assess_pay }}</div>
                                @if(!empty($payment->accounts->assess_pay))
                                <div style="width: 40%;float: right;margin-top: 5px;" >
                                    <button type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="assess_pay">查看</button>
                                </div>
                                    @endif
                            </div>
                            </td>
                        </tr>

                        <tr>
                            <td>标书工本费_收取</td>
                            <td>
                                <div>
                                    <div @if(!empty($payment->accounts->flat_pay))style="width: 60%;float: left;margin-top: 10px;"@endif>{{ $payment->accounts->flat_pay }}</div>
                                    @if(!empty($payment->accounts->flat_pay))
                                    <div style="width: 40%;float: right;margin-top: 5px;" >
                                        <button type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="flat_pay">查看</button>
                                    </div>
                                        @endif
                                </div>
                            </td>
                            <td>其他</td>
                            <td>
                                <div>
                                    <div @if(!empty($payment->accounts->other_pay))style="width: 60%;float: left;margin-top: 10px;"@endif>{{ $payment->accounts->other_pay }}</div>
                                    @if(!empty($payment->accounts->other_pay))
                                    <div style="width: 40%;float: right;margin-top: 5px;" >
                                        <button type="button" class="btn btn-primary money-edit" data-num="{{ $num+1 }}" data-type="other_pay">查看</button>
                                    </div>
                                        @endif
                                </div>

                            </td>
                            <td><strong>小计</strong></td>
                            <td><strong>{{ $payment->subtotal }}</strong></td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8">暂无收款数据</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="sheet-footer">
            <a href="{{ back_url('project.index_flow') }}" class="btn btn-primary">返回</a>
        </div>
        <script src="http://code.jquery.com/jquery-2.0.0.min.js"></script>
        <script src="{{asset('layer/layer.js')}}"></script>
        <script>
            $(document).on('click','.money-edit',function () {
                var type = $(this).data('type');
                var num = $(this).data('num');
                var pid = "{{$id}}";
                var is_end = "true";
                layer.open({
                    type: 2,
                    title: '收支记录',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['90%', '90%'],
                    content: '{{url('/logs?')}}'+'num='+num+'&type='+type+'&pid='+pid+'&is_end='+is_end, //iframe的url
                });
            })
        </script>
    @endif
@stop