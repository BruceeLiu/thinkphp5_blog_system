/** 通用 js
 * @author southday
 * @date 2019.02.27
 * @version v0.1
 */

/** url更改器 southday 2019.03.01
 * 1) 前端单独开发，测试时，url前面需要加http://localhost:8080
 * 2) 集成到java web项目中时，url前面不用加http://localhost:8080
 * 该方法是为了方便以上两种情况的相互转换，真正部署时，要取消该方法的调用
 */
function cookurl(url) {
    // return url;
    return 'http://localhost:8080' + url;
}

/** 更换验证码 */
function changeVerifyCode() {
    return cookurl('/idevtools/jcaptcha.jpg?r=' + (Math.random()))
}

/** code = VALID_ERROR，表单验证失败，提示消息
 * southday 2019.03.01
 */
function showValidMsgs(validMsgs) {
    for (i = 0, len = validMsgs.length; i < len; i++)
        toastr.warning(validMsgs[i].errorMsg)
}

/**
 * 从localStorage中获取adminToken
 * southday 2019.05.17
 * @returns {string}
 */
function getAdminToken() {

    let created_at = localStorage.getItem('access_token_create_at');

    let expire_in = localStorage.getItem('access_token_expire_in');

    let date_timestamp = new Date().getTime();

    created_at = parseInt(created_at+'000');

    let use_timestamp = date_timestamp - created_at;

    console.log('token创建时间：',created_at)
    console.log('当前时间戳(js)：',date_timestamp)
    console.log('使用时长：',use_timestamp)

    if (use_timestamp > expire_in){
        saveAdminToken(null,null,null);
    }

    //判断有没有小于5分钟
    if (use_timestamp <= 300){
        $.ajax({
            //数据提交方式GET/POST，默认：POST
            type:'POST',
            //数据提交路径
            url:"{:url('admin/login/check')}",
            headers: {
                'authorization': 'Bearer '+localStorage.getItem("access_token")
            },
            //数据
            data:{
                'access_token': localStorage.getItem("access_token")
            },
            //提交方式
            dataType:'json',
            //数据返回格式
            success:function (data) {
                console.log(data);
                if (data.code === 1){
                    let tokens = data.data.access_token.access_token;
                    let access_token_create_at = data.data.access_token.access_token_create_at;
                    let access_token_expire_in = data.data.access_token.access_token_expire_in;
                    saveAdminToken(tokens,access_token_create_at,access_token_expire_in);
                }
            },
            error:function (data) {
                console.log(data);
                saveAdminToken(null,null,null);
                alert('网络错误,请稍后重试...');
            }
        })
    }

    return localStorage.getItem("access_token");
}

/**
 * 将adminToken保存到localStorage中
 * @param token
 * @param token_created_at
 * @param token_expire_in
 */
function saveAdminToken(token,token_created_at,token_expire_in) {
    localStorage.setItem("access_token", token)
    localStorage.setItem("access_token_create_at", token_created_at)
    localStorage.setItem("access_token_expire_in", token_expire_in)
}

/**
 * 将admin保存到localStorage
 * southday 2019.05.17
 * @param admin
 */
function saveAdmin(admin) {
    localStorage.setItem("admin", ($.isEmptyObject(admin) ? null : JSON.stringify(admin)))
}

/**
 * 从localStorage中取user
 * southday 2019.05.17
 * @returns {admin}
 */
function getAdmin() {
    let a = localStorage.getItem("admin")
    return $.isEmptyObject(a) ? null : JSON.parse(a)
}