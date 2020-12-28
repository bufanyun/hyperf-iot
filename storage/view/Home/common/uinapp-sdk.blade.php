<script type="text/javascript" src="https://js.cdn.aliyun.dcloud.net.cn/dev/uni-app/uni.webview.1.5.2.js"></script>
<script type="text/javascript">
    //js SDK  加载成功
    document.addEventListener('UniAppJSBridgeReady', function() {
        // 监听按钮事件
        // document.getElementById('to_shiming').addEventListener('click', function() {
            let data_shiming = {
                action: '1'
            }
            // // .nvue 可以接收的事件
            uni.postMessage({
                data: data_shiming
            });
            //
            // // .vue 可以接收的事件
            // window.parent.postMessage(data_shiming, '*')

        // });
    });

    //接收来自 .vue  的数据 和 参数  间接 调用 函数
    window.addEventListener('message', function(event) {
        // event.origin --发送者的源
        // event.source --发送者的window对象
        // event.data --数据
        if (event.data) {
            console.log("接收到uin发来的消息--event.data:", JSON.stringify(event.data)+'---event:'+JSON.stringify(event.origin))
        }

    })

    // .nvue 直接调用函数
    function setBirDay(data) {

    }

    // .nvue 直接调用函数
    function setBirDayInit(day) {

    }
</script>
