
<script type="text/javascript" src="https://js.cdn.aliyun.dcloud.net.cn/dev/uni-app/uni.webview.1.5.2.js"></script>
<script type="text/javascript">
    //js SDK  加载成功
    document.addEventListener('UniAppJSBridgeReady', function() {
        $('#order_query').click(function () {
            uni.postMessage({  //跳转订单查询
                data: {
                    action: 'jumpOrderQuery',
                    data :{},
                }
            });
        });
        $('#liang_shop').click(function () {
            uni.postMessage({  //跳转订单查询
                data: {
                    action: 'uToast',
                    data :{
                        msg:'暂未开放，敬请期待~',
                    },
                }
            });
        });
        $('#action_card').click(function () {
            uni.postMessage({  //跳转订单查询
                data: {
                    action: 'uToast',
                    data :{
                        msg:'请前往营运商微信公众号进行号卡激活',
                    },
                }
            });
        });

        /**
         * 更新title
         */
        uni.postMessage({
            data: {
                action: 'setNavigationBarTitle',
                data :{
                    title : document.title
                },
            }
        });
        /**
         * 记录历史路由
         */
        uni.postMessage({
            data: {
                action: 'setUrlHistory',
                data :{
                    data : 'r={{$routePath}}&{!!http_build_query($reqParam)!!}'
                },
            }
        });

        // // .vue 可以接收的事件
        // window.parent.postMessage(data_shiming, '*')
    });

    //接收来自 .vue  的数据 和 参数  间接 调用 函数
    window.addEventListener('message', function(event) {
        // event.origin --发送者的源
        // event.source --发送者的window对象
        // event.data --数据
        // if (event.data) {
        //     console.log("接收到uin发来的消息--event.data:", JSON.stringify(event.data)+'---event:'+JSON.stringify(event.origin))
        // }

    });

</script>
