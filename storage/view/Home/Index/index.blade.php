<?php

$codepay_path = '/static/home/public/assets';
$typeName = '微信';
?>
        <!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
    <meta http-equiv="Content-Language" content="zh-cn">
    <meta name="apple-mobile-web-app-capable" content="no"/>
    <meta name="apple-touch-fullscreen" content="yes"/>
    <meta name="format-detection" content="telephone=no,email=no"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>扫码支付</title>
    <link href="<?php
    echo $codepay_path ?>/css/wechat_pay.css" rel="stylesheet" media="screen">

    <style>
        .ico_log2 {
            display: inline-block;
            vertical-align: middle;
            margin-right: 7px;
        }
    </style>
</head>

<body>
<div class="body">
    <h1 class="mod-title">
        <span id="orderid" class="ico_log2 ico-wxpay"></span>
    </h1>

    <div class="mod-ct">
        <div class="order" style="color:red;font-size:16px"></div>

        <div class="amount"  style="position: relative;" >
            <span id="money">￥ </span>
            <div style="position: absolute;font-size: 10px;top: 29px;left: 75%;"></div></div>
        <div class="qrcode-img-wrapper" data-role="qrPayImgWrapper">
            <div data-role="qrPayImg" class="qrcode-img-area" style="margin-top: 10px;">
                <div class="ui-loading qrcode-loading" data-role="qrPayImgLoading" style="display: none;">加载中</div>
                <div style="position: relative;display: inline-block;">
                    <img id='show_qrcode' alt="加载中..." src="<?php
                    echo $codepay_path ?>/img/no.png" width="210" height="210" style="display: block;">
                </div>
            </div>
        </div>

        <div class="time-item" id="msg" >
            <h1>二维码过期时间</h1>
            <strong id="hour_show">0时</strong>
            <strong id="minute_show">0分</strong>
            <strong id="second_show">0秒</strong>
        </div>

        <div class="tip">
            <div class="ico-scan"></div>
            <div class="tip-text">
                <p>请使用<?php
                    echo $typeName ?>扫一扫</p>
                <p>扫描二维码完成支付</p>
                <p><div id="kf" style="display:none;"></div></p>
            </div>
        </div>

        <div class="detail" id="orderDetail">
            <dl class="detail-ct" id="desc" style="display: none;">
            </dl>
            <a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
        </div>

        <div class="tip-text">
        </div>

    </div>
    <div class="foot">
        <div class="inner">
            <p>手机用户可保存上方二维码到手机中</p>
            <p>在<?php
                echo $typeName ?>扫一扫中选择“相册”即可</p>
            <p><div id="kfqq"></div></p>
        </div>
    </div>

</div>
<div id="qrcode" style="display: none"></div>
<div class="copyRight"></div>
<script src="<?php
echo $codepay_path ?>/js/jquery-1.10.2.min.js"></script>
<script src="<?php
echo $codepay_path ?>/js/qrcode.min.js"></script>

<script>
    var overtime = -1; //订单超时时间（秒），超过这个时间的二维码会隐藏收款码，继续监听订单
    var payok = false; //是否已完成支付
    var restartProcess = false; //是否正在支付操作
    var timeConnect = 0;
    var lockReconnect = false;
    var ws = null;
    var Noworder = {};  //当前订单

    const releatime = 5 * 60; //无任务状态下5分钟刷新一次页面，防止卡死
    const runtime = Date.now();
    const wsUrl = "ws://118.190.152.37:1888";
    const proto = { //子协议
        imei : "868019047358743",
        type : "wxpay",
        idk  : '<?php echo 'JC'.time()?>',
    };
    const protocol = encodeURIComponent('access=1&imei='+proto.imei+'&type='+proto.type+'&idk='+proto.idk);

    webSocketInit(wsUrl, protocol);
    function webSocketInit(wsUrl,protocol) {
        ws = new WebSocket(wsUrl, [protocol]);

        ws.onopen = function () {
            timeConnect = 0;
        };

        ws.onmessage = function (evt) {
            // console.log("数据已接收: \r\n" + evt.data+"\r\n");
            handleMessage(evt.data);
        };

        ws.onclose = function () {
            // 关闭 websocket
            console.log("连接已关闭...");
            ws = null;
            reconnect(wsUrl,protocol);
        };
    }
    /**
     * 重连
     */
    function reconnect(wsUrl,protocol) {
        if (lockReconnect) return;
        if (ws !== null) return;
        lockReconnect = true;
        timeConnect ++;
        console.log("第"+timeConnect+"次重连");
        // 进行重连
        setTimeout(function(){
            log('protocol:'+protocol);
            webSocketInit(wsUrl,protocol);
            lockReconnect = false;
        },2000);
    }

    //无任务状态下定时释放页面
    var release = setInterval (function () {
        if((Date.now() > (runtime+ (releatime*1000))) && overtime === -1){
            clearInterval(release);
            setTimeout(function () {
                location.reload();
            }, 1000);
        }
    }, 5000);

    //连续ping
    var pong = setInterval (function () {
        if(ws.send(JSON.stringify({method:'AgentDevice/ping','data':{}})) === false){
            clearInterval(pong);
            ws.close();
        }
    }, 5000);

    function log(d) {console.log(d);}
    function p(d) {log(d);}

    function randomString(len) {
        len = len || 32;
        var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
        var maxPos = $chars.length;
        var pwd = '';
        for (i = 0; i < len; i++) {
            pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
        }
        return pwd;
    }
    /**
     * 倒计时
     */
    function inverted(order_id){
        // var interval = setInterval (function () {
        //     if(order_id !== Noworder.orderid){
        //         return;
        //     }
        //     if (restartProcess === false) {
        //         clearInterval(interval);
        //         $('#second_show').text('0秒');
        //         return;
        //     }
        //     overtime--;
        //     if (overtime > 0) {
        //         $('#second_show').text(overtime + '秒');
        //     }else{
        //         clean();
        //         log('二维码等待超时');
        //         clearInterval(interval);
        //     }
        // }, 1000);
    }
    /**
     * 订单支付
     */
    orderPayment = function(data) {
        restartProcess = true;
        // Noworder = data;
        p('新订单ID：' +data.id+ "--"+ JSON.stringify(data));

        updateOrder(data, {status: 31, message: '正在填码'});
        new QRCode(document.getElementById("qrcode"),data.qrurl);
        setTimeout(function () {
            $("#show_qrcode").attr("src", $("#qrcode img").attr("src"));
            $("#money").text('￥'+data.price);
            $("#orderid").text(data.orderid);

            // overtime = data.validtime+10;  //获取剩余有效期 //+10
            // $('#second_show').text(overtime +'秒');
            // inverted(data.orderid); //倒计时

            splicNotice(data);
        }, 50);
    };
    /**
     * 确认已接码，并通知扫码手机
     */
    splicNotice = function(data){
        ws.send(JSON.stringify({method:'AgentTrade/splicNotice',data:data}));
    };
    /**
     * 更新订单状态
     */
    updateOrder = function(data, update){
        Object.assign(data, update);
        ws.send(JSON.stringify({method:'AgentTrade/updateOrder',data:data}));
    };
    /**
     * 更新设备任务状态
     */
    updateTaskStatus = function(data){
        ws.send(JSON.stringify({method:'AgentDevice/updateTaskStatus',data:data}));
    };
    /**
     * 更新设备任务状态
     */
    updateTaskStatus = function(data){
        ws.send(JSON.stringify({method:'AgentDevice/updateTaskStatus',data:data}));
    };
    /**
     * 记录调试器
     */
    recordLogger = function(data){
        ws.send(JSON.stringify({method:'AgentTradeLog/record',data:data}));
    };
    /**
     * 消息统一处理
     */
    handleMessage = function(string_data) {
        let data = JSON.parse(string_data);
        let code = data.code;
        let msg = data.msg;
        let method = data.data.method;
        let res = data.data;
        let msgTip = 'socket';

        switch (method) {
            case 'orderPayment': {
                orderPayment(res);
                break;
            }
            case 'AgentTrade/updateOrder': {
                log(msgTip + '订单状态更新' + (code == 0 ? '成功' : '失败'));
                break;
            }
            case 'ideScan': {
                log(msgTip + '确认扫码');
                $("#show_qrcode").attr("src", "<?php
                    echo $codepay_path ?>/img/saook.png");
                break;
            }
            case 'payOff': {
                payok = true;
                log(msgTip + '支付完成');
                $("#show_qrcode").attr("src", "<?php
                    echo $codepay_path ?>/img/wxpayok.png");
                setTimeout(function () {
                    clean();
                    updateTaskStatus({activity:0});
                    restartProcess =  false;
                }, 1200);
                break;
            }
            case 'payOver': {
                log(msgTip + '支付超时');
                clean();
                updateTaskStatus({activity:0});
                restartProcess =  false;
                break;
            }
            case 'AgentDevice/updateTaskStatus': {
                log(msgTip + '设备任务状态更新' + (code === 0 ? '成功' : '失败'));
                if(code !== 0){
                    log(msg);
                }
                break;
            }
            case 'AgentTrade/splicNotice': {
                log(msgTip + '确认接码' + (code === 0 ? '成功' : '失败'));
                break;
            }
            case 'AgentTradeLog/record': {
                log(msgTip + '日志更新' + (code === 0 ? '成功' : '失败'));
                break;
            }
            case 'AgentDevice/ping': {
                // log(msgTip + '连接' + (code === 0 ? '正常' : '异常'));
                // if (code !== 0) {
                //     p(msgTip + 'ping异常，即将重试');
                //     // ws.close();
                // }
                break;
            }
            case 'error': {
                p(msgTip + '错误提示:' + msg);
                break;
            }
            case 'kick': {
                p(msgTip + '错误提示:' + msg);
                ws.close();
                break;
            }
            case 'connect': {
                if (code !== 0) {
                    p(msgTip + '连接失败:' + msg);
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                    break;
                }
                config = res.config;
                log(msgTip + '连接成功');
                break;
            }
            default : {
                p(msgTip + '处理方法不存在:' + JSON.stringify(res));
                break;
            }
        }
    };
    /**
     * 清空二维码
     */
    clean = function () {
        $("#show_qrcode").attr("src","<?php
            echo $codepay_path ?>/img/no.png");
        $("#money").text('￥');
        $("#orderid").text('');
        $('#second_show').text('0秒');
        // Noworder = {};
        payok = false;
    };
</script>

</body>
</html>
