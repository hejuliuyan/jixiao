
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>    项目详情_上海瑞和工程咨询项目绩效管理系统
    </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta name="_token" content="SFnCKqxUCkfElSHOqQST00wi5F01gShr1FX6GKhA"/>
    <!-- Core JS -->
    <script type="text/javascript" src="/build/assets/js/scripts-b1ca668033.js"></script>
    <script type="text/javascript" src="/build/assets/js/main-df65d79ca1.js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="http://study.com/assets/js/html5shiv.min.js"></script>
    <script type="text/javascript" src="http://study.com/assets/js/respond.min.js"></script>
    <![endif]-->
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/build/assets/css/styles-7ccebb95c6.css">
    <link href="{{asset('bootstrap-datetimepicker-master')}}/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
</head>

<body style="text-align: center">
<div class="main-wrap">
    @if(request('is_end') && $logs->isEmpty())
        <h2>历史数据，暂无记录</h2>
        @else
        <div class="content-wrap" style="width: 100%;text-align: center">
            <div class="area-wrap" style="text-align: center">
                {{--<div class="sheet" style="margin: 0px">--}}
                <div class="sheet-middle">
                    <div class="sheet-head">
                        <div class="pull-left sheet-btn" style="width: 55%">
                            <div class="pull-right">
                                <strong style="font-size: 20px">
                                    @if(in_array(request('type'),['income','assess_income','flat_pay','shouru']))
                                        收入记录
                                    @else
                                        支出记录
                                    @endif
                                </strong>
                            </div>
                        </div>
                        @if(!request('is_end'))
                            <div class="pull-right">
                                <button type="button" class="btn btn-primary" id="income_add">添加记录</button>
                            </div>
                        @endif
                    </div>
                    <div class="sheet-table">
                        <table width="100%">
                            <thead>
                            <tr>
                                <th width="6%">序号</th>
                                <th width="6%">
                                    @if(in_array(request('type'),['income','assess_income','flat_pay','shouru']))
                                        收入金额
                                    @else
                                        支出金额
                                    @endif
                                </th>
                                <th width="13%">
                                    @if(in_array(request('type'),['income','assess_income','flat_pay','shouru']))
                                        收入时间
                                    @else
                                        支出时间
                                    @endif
                                </th>
                                <th width="13%">发票编号</th>
                                <th width="13%">备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!$logs->isEmpty())
                                @foreach($logs as $key => $val)
                                    <tr class="input-1">
                                        <td>{{$key + 1}}</td>
                                        <td>{{$val->money}}</td>
                                        <td>{{date('Y-m-d H:i:s',$val->create_time)}}</td>
                                        <td>{{$val->invoice_num}}</td>
                                        <td>{{$val->remarks}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <div class="sheet-footer" style="text-align: right;margin-top: 10px">
                                <span style="margin-left: 10px">
                                    <strong>小计：<span id="xiaoji">{{empty($total) ?  '0.00' :$total}}</span></strong>
                                </span>
                        </div>
                        @if(!request('is_end'))
                            <div class="sheet-footer" style="text-align: center;margin-top: 20px">
                                <button type="button" disabled="disabled" class="btn btn-primary add_money">提交</button>
                            </div>
                        @endif
                    </div>
                </div>
                {{--</div>--}}
            </div>
        </div>
        @endif
</div>
</body>
<script src="http://code.jquery.com/jquery-2.0.0.min.js"></script>
{{--<script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>--}}
<script type="text/javascript" src="{{asset('bootstrap-datetimepicker-master')}}/js/bootstrap-datetimepicker.js"></script>
<script src="{{asset('bootstrap-datetimepicker-master')}}/js/locales/bootstrap-datetimepicker.fr.js"></script>
<script>
    $(function(){
        $('#income_add').click(function(){
//            var num = $('table tr:last').find('td:first').text();
            var num = $('.input-1:last').find('td:first').text();
            if(num == ''){
                num = 0;
            }
            num = Number(num) + 1;
            var inser = '<tr class="input-1">'+
                '<td>'+num+'</td>'+
                '<td>'+
                '<div class="sheet-item"> <input style="text-align: center" type="text" class="form-control" name="money" data-num="1" id="money_'+num+'"> </div>'+
                '</td>'+
                '<td>'+
                '<div class="sheet-item">'+
                '<input type="text" style="padding-left: 5px;padding-right: 0px;" class="form-control create_time'+num+'" name="create_time" id="create_time'+num+'"  data-num="1" readonly>'+
                '</div>'+
                '</td>'+
                '<td>'+
                '<div class="sheet-item">'+
                '<input type="text" class="form-control" style="text-align: center" name="invoice_num" data-num="1" >'+
                '</div>'+
                '</td>'+
                '<td>'+
                '<div class="sheet-item">'+
                '<input type="text" class="form-control" style="text-align: center" name="remarks" data-num="1" >'+
                '</div>'+
                '</td>'+
                '</tr>';
            $('tbody').append(inser);
            $(".create_time"+num).datetimepicker({format: 'yyyy-mm-dd hh:ii:ss'});
            $(this).attr('disabled',true);
            $('.add_money').attr('disabled',false);
            $('.add_money').attr('data-num',num);
        })

        function isRealNum(val){
            // isNaN()函数 把空串 空格 以及NUll 按照0来处理 所以先去除
            if(val === "" || val ==null){
                return false;
            }
            if(!isNaN(val)){
                return true;
            }else{
                return false;
            }
        }

        $(document).on('click','.add_money',function(){

            var money = $('input[name="money"]').val();
            var create_time = $('input[name="create_time"]').val();
            var invoice_num = $('input[name="invoice_num"]').val();
            var remarks = $('input[name="remarks"]').val();
                var type = '{{request('type')}}';
                if(!isRealNum(money)){
                    alert('输入金额请填入数字');
                    return false;
                }
//            if(confirm('确定是否提交本记录，提交后不可删除')){
                if(create_time && money){
                    if(confirm('确定是否提交本记录，提交后不可删除')){
                        $(this).attr('disabled',true);
                        url = "{{route('post_money_logs')}}";
                        $.post(url,{
                            money:money,
                            payment_id:"{{request('num')}}",
                            create_time:create_time,
                            type:"{{request('type','income')}}",
                            invoice_num:invoice_num,
                            pid:"{{request('pid')}}",
                            remarks:remarks
                        },function(data){
                            $('input[type=text]').attr('disabled',true);
                            $('input[type=text]').attr('name','');
                            alert(data.info);
                            //修改该次收款的小计
                            parent.$('input[name="subtotal[]"][data-num="'+"{{request('num')}}"+'"]').val(data.xiaoji);
                            parent.$('strong[class="subtotal"][data-num="'+"{{request('num')}}"+'"]').text(data.xiaoji);
                            parent.$('#zonge').text(data.zonge);
                            //修改该收费类型的总计
                            parent.$('input[name="'+"{{request('type','income')}}"+'[]"][data-num="'+"{{request('num')}}"+'"]').val(data.total);
                            $('#xiaoji').text(data.total);
                            $('#income_add').attr('disabled',false);
                        },'json')
                    }else{
                        return false;
                    }
                }else{
                    alert('收款金额和进账时间为必填项');
                    return false;
                }
            })
    })

</script>
</html>
