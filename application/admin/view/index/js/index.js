
$.ajax({
    method: 'get',
    url: "{:url('admin/index/index')}",
    headers: {
        'authorization': 'Bearer '+getAdminToken()
    },
    //数据返回格式
    success:function (data) {
        console.log(data);
    },
    error:function (data) {
        window.location.href = "{:url('admin/login/login')}";
    }
})