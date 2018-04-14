var RHA = {
    delete: function (obj) {
        var _this = $(obj);
        var _url = _this.data('url');

        swal({
            html: '<strong>确定删除吗</strong>',
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

                        if(data.result == 1) {
                            promptBox1(true, data.msg);
                            window.location.reload();
                        }else {
                            promptBox2(false, data.msg);
                        }
                    })
                });
            }
        }).catch(swal.noop);
    },
    save: function (obj) {
        var _this = $(obj);
        var form = $('.project-form');
        var action = _this.data('action');

        _this.attr('disabled', true);
        form.attr('action', action);

        form.submit();
    },
    order: function (obj) {
        var _this = $(obj);
        var form = $('.project-form');
        var action = _this.data('action');

        _this.attr('disabled', true);

        swal({
            html: '<strong>备注</strong><textarea name="remark-value" class="swal2-textarea" placeholder="请填写备注"></textarea>',
            showCancelButton: true,
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            showLoaderOnConfirm: true
        }).then(function(){
            var remark_val = $('textarea[name="remark-value"]').val();
            var remark_html = '<input type="hidden" name="remark" value="'+remark_val+'"/>';
            _this.after(remark_html);

            form.attr('action', action);
            form.submit();
        },function(dismiss){
            if (dismiss === 'cancel') {
                _this.attr('disabled', false);
            }
        });
    }
};
