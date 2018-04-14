<tr class="input-{{ $num }}" id="weizhi">
    <td rowspan="2">{{ $num }}</td>
    <td rowspan="2">1</td>
    <td>
        <div class="sheet-item">
            <span>{{ $type == 1 ? '收入_招标':'收入_咨询' }}</span>
            <input type="hidden" name="type[]" value="{{ $type }}" readonly>
        </div>
    </td>
    <td>
        <div class="sheet-item">
            <input  style="width: 60%;float: left;" type="text" class="form-control" name="income[]" data-num="{{ $num }}" readonly>
            <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-type="income" data-num="{{ $num }}">编辑</button>
        </div>
    </td>
    <td>专家评审费_收取</td>
    <td>
        <div class="sheet-item">
            <input style="width: 60%;float: left;" type="text" class="form-control" name="assess_income[]" data-num="{{ $num }}" readonly>
            <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-num="{{ $num }}" data-type="assess_income">编辑</button>
        </div>
    </td>
    <td>专家评审费_支出</td>
    <td>
        <div class="sheet-item">
            <input style="width: 60%;float: left;" type="text" class="form-control" name="assess_pay[]" data-num="{{ $num }}" readonly>
            <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-num="{{ $num }}" data-type="assess_pay" >编辑</button>
        </div>
    </td>
</tr>

<tr class="input-{{ $num }}">
    <td>标书工本费_收取</td>
    <td>
        <div class="sheet-item">
            <input style="width: 60%;float: left;"  type="text" class="form-control" name="flat_pay[]" data-num="{{ $num }}" readonly>
            <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-num="{{ $num }}" data-type="flat_pay">编辑</button>
        </div>
    </td>
    <td>其他</td>
    <td>
        <div class="sheet-item">
            <input style="width: 60%;float: left;" type="text" class="form-control" name="other_pay[]" data-num="{{ $num }}" readonly>
            <button style="width: 40%;float: right;margin-top: 5px;" type="button" class="btn btn-primary money-edit" data-num="{{ $num }}" data-type="other_pay">编辑</button>
        </div>
    </td>
    <td><strong>小计</strong></td>
    <td>
        <div class="sheet-item">
            <strong class="subtotal" data-num="{{ $num }}"></strong>
            <input type="hidden" data-num="{{ $num }}" name="subtotal[]" value="">
        </div>
    </td>
</tr>

{{--<script src="http://code.jquery.com/jquery-2.0.0.min.js"></script>--}}
{{--<script src="{{asset('layer/layer.js')}}"></script>--}}
{{--<script>--}}
    {{--$(document).on('click','.money-edit',function () {--}}
        {{--var type = $(this).data('type');--}}
        {{--var num = $(this).data('num');--}}
        {{--var pid = "{{$id}}";--}}
        {{--layer.open({--}}
            {{--type: 2,--}}
            {{--title: '收支记录',--}}
            {{--shadeClose: true,--}}
            {{--shade: 0.8,--}}
            {{--area: ['90%', '90%'],--}}
            {{--content: '{{url('/logs?')}}'+'num='+num+'&type='+type+'&pid='+pid+'&is_end=false', //iframe的url--}}
        {{--});--}}
    {{--})--}}
{{--</script>--}}
