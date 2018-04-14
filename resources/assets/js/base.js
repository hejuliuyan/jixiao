(function($){
    var original_title = document.title;

    var StarHub = {
        init: function(){
            var self = this;

            self.siteBootUp();

            //隐藏提示信息
            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);

            //sweetAlert2默认设置
            swal.setDefaults({allowOutsideClick: false,confirmButtonColor: '#39C'});
        },

        /*
         * Things to be execute when normal page load
         * and pjax page load.
         */
        siteBootUp: function(){
            var self = this;
            self.initAjax();
            self.initTime();
            self.initSideClick();
        },

        initAjax: function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
        },

        initTime: function() {
            //每秒刷新时间显示
            setInterval(function () {
                var nowTime = moment().format('YYYY年MM月DD日 HH:mm:ss');
                $('#navTime').html(nowTime);
            }, 1000);
        },

        initSideClick: function () {
            $('.openPopover').on('click', function () {
                var subMenu = $(this).siblings('.sub-menu');

                if(subMenu.hasClass('hidden')) {
                    subMenu.removeClass('hidden');
                }else {
                    subMenu.addClass('hidden');
                }
            });
        }
    };

    window.StarHub = StarHub;
})(jQuery);

$(document).ready(function()
{
    StarHub.init();
});

/**
 * 身份证验证函数
 *
 * @param obj
 * @returns {boolean}
 */
function card(obj){
    var reg=/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X|x)$/;
    if(!reg.test(obj)){
        return false;
    }else{
        return true;
    }
}

/**
 * 解析url参数(正则匹配)
 *
 * @param name
 * @returns {*}
 */
function urlParam(name) {
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}

/**
 * 移动端判断
 *
 * @returns {boolean}
 */
function isMobile() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Kindle|Silk|Opera Mini/i.test(navigator.userAgent);
}

/**
 * 检查输入是否正确的电话和手机号
 *
 * @param telephone
 * @returns {boolean}
 */
function isTelOrMobile(telephone){
    var telReg = /^((0\d{2,3})-)(\d{7,8})$/;
    var mobileReg = /^1[34578]\d{9}$/;

    return telReg.test(telephone) || mobileReg.test(telephone);
}

/**
 * 时间数字补0
 *
 * @param time
 * @returns {string}
 */
function timeFormat(time) {
    return time < 10 ? '0'+time : time;
}

/**
 * 数值数字判断，最高保留4位小数
 *
 * @param value
 * @returns {boolean}
 */
function checkNum(value) {
    var rule = /^[0-9]+([.]\d{1,4})?$/;
    return rule.test(value);
}

/**
 * 限制输入数字和小数点
 *
 * @param obj
 */
function clearNoNum(obj) {
    obj.value = obj.value.replace(/[^\d.]/g, "");//清除“数字”和“.”以外的字符
    obj.value = obj.value.replace(/^\./g, "");//验证第一个字符是数字而不是.
    obj.value = obj.value.replace(/\.{2,}/g, ".");//只保留第一个. 清除多余的.
    obj.value = obj.value.replace(".", "$#$").replace(/\./g,"").replace("$#$", ".");
}

/**
 * 是否整数
 *
 * @param obj
 * @returns {boolean}
 */
function isInteger(obj) {
    return obj%1 === 0;
}

/**
 * 是否小数
 *
 * @param obj
 * @returns {boolean}
 */
function isDecimal(obj) {
    var reg = "^([0-9]*[.0-9])$"; // 小数测试
    var re = new RegExp(reg);
    if (obj.search(re) != -1)
        return true;
    else
        return false;
}

/**
 * 数字前面补零输出
 *
 * @param num
 * @param length
 * @returns {*}
 */
function prefixInteger(num, length) {
    return (Array(length).join('0') + num).slice(-length);
}

/**
 * 判断字符串是否为空
 *
 * @param obj
 * @returns {boolean}
 */
function isEmpty(obj) {
    if(obj == '' || obj == null || obj == undefined) {
        return true;
    }else {
        return obj.replace(/(^\s+)|(\s+$)/g,"").length == 0;
    }
}

/**
 * 使用循环的方式判断一个元素是否存在于一个数组中
 *
 * @param arr 数组
 * @param needle 元素值
 */
function inArray(arr, needle){
    var count = arr.length;
    for(var i = 0; i < count; i++){
        if(needle == arr[i]){ return true; }
    }
    return false;
}

/**
 * 操作提示信息（自动消失）
 *
 * @param type 类型
 * @param message 信息
 */
function promptBox1(type, message) {
    var type_name = type ? 'success':'error';
    //调用模态框
    swal({ type: type_name, text: message, timer: 1000 }).catch(swal.noop);
}

/**
 * 操作提示信息
 *
 * @param type 类型
 * @param message 信息
 */
function promptBox2(type, message) {
    var type_name = type ? 'success':'error';
    //调用模态框
    swal({ type: type_name, text: message}).catch(swal.noop);
}

/**
 * input加载图片显示
 *
 * @param docObj input控件
 * @param imgObj 图片显示元素
 * @param localObj 父亲节点元素
 * @returns {boolean}
 */
function imagePreview(docObj, imgObj, localObj) {
    var imgObjPreview=document.getElementById(imgObj);

    if(docObj.files &&docObj.files[0])
    {
        //火狐下，直接设img属性
        imgObjPreview.style.display = 'block';
        imgObjPreview.style.width = '100%';
        imgObjPreview.style.height = '100%';
        //imgObjPreview.src = docObj.files[0].getAsDataURL();

        //火狐7以上版本不能用上面的getAsDataURL()方式获取，需要一下方式
        imgObjPreview.src = window.URL.createObjectURL(docObj.files[0]);
    }
    else
    {
        //IE下，使用滤镜
        docObj.select();
        var imgSrc = document.selection.createRange().text;
        var localImagId = document.getElementById(localObj);
        //必须设置初始大小
        localImagId.style.width = "100%";
        localImagId.style.height = "100%";
        //图片异常的捕捉，防止用户修改后缀来伪造图片
        try{
            localImagId.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
            localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
        }
        catch(e)
        {
            alert("您上传的图片格式不正确，请重新选择!");
            return false;
        }
        imgObjPreview.style.display = 'none';
        document.selection.empty();
    }
    return true;
}

/**
 * 修改url中某个指定的参数的值
 *
 * @param url 目标url
 * @param arg 需要替换的参数名称
 * @param arg_val 替换后的参数的值
 * @returns {string} 参数替换后的url
 */
function changeURLArg(url,arg,arg_val) {
    var pattern=arg+'=([^&]*)';
    var replaceText=arg+'='+arg_val;
    if(url.match(pattern)){
        var tmp='/('+ arg+'=)([^&]*)/gi';
        tmp=url.replace(eval(tmp),replaceText);
        return tmp;
    }else{
        if(url.match('[\?]')){
            return url+'&'+replaceText;
        }else{
            return url+'?'+replaceText;
        }
    }
}

/**
 * 公式计算扩展（百分比转换为小数，并四舍五入）
 *
 * @param obj 公式字符串
 * @returns {*} 计算结果
 */
function math_compute(obj) {
    var result = '';
    var length = obj.length;
    var index = 0;
    var num = '';

    if(length == 0) {
        return obj;
    }

    while (index < length) {
        var str = obj.substr(index, 1);

        if(str == '(' || str == ')' || str == '+' || str == '-' || str == '*' || str == '/') {
            if(num.indexOf('%') > 0) {
                num = num.replace('%', '')/100;
            }

            result += num + str;
            num = '';
        }else {
            num += str;
        }

        index++;
    }

    if(num.indexOf('%') > 0) {
        num = num.replace('%', '')/100;
    }

    result += num;
    result = math.eval(result);

    return math.round(result);
}