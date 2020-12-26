<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>订单提交成功</title>
    <link rel="stylesheet" href="{{env('CDN_DOMAIN')}}/static/home/public/assets/css/index.css">
    <style>
        .success-msg {
            padding: 20px 0;
            box-sizing: border-box;
            text-align: center;
        }

        .success-msg .van-icon {
            color: rgb(142, 196, 31);
            font-size: 80px;
            display: block;
            margin-bottom: 10px;
        }

        .success-msg span {
            font-weight: bold;
            color: #333333;
            font-size: 20px;
            display: inline-block;
            margin-top: 29px;
        }

        .success-msg p {
            margin: 5px 0;
            color: #444;
            font-size: 15px;
            text-align: left;
            padding: 0 20px;
            margin-top: 20px;
        }
        .van-popup {
            background-color: rgba(0,0,0,0);
        }
        .van-popup--bottom {
            background-color: rgba(0,0,0,0);
        }
        .van-modal {
            background-color: rgba(0,0,0,0.15)
        }
        .van-icon-close{
            margin-left: 10%
        }
        .active_pic {
            margin-top:90%;
        }
        .active_pic img{
            width:92%;
            display: block;
            margin: 20px auto;
        }
        /* @media screen and (max-width: 320px) and (max-height: 568px) {
				#acp {
					width: 70% !important
				}
			}
        @media screen and (max-width: 375px) and (max-height: 667px) {
				#acp {
					width: 70% !important
				}
			}
        @media screen and (min-width: 360px) and (min-height: 640px) {
				#acp {
					width: 80% !important
				}
			} */
    </style>
    <title>订单提交成功</title>
</head>

<body style="background-color:#F8F8F8;">
<div class="success-msg" id="app">
    <img src="{{env('CDN_DOMAIN')}}/static/home/public/assets/img/success.png" style="width:70px;height:70px;margin: 0 auto;margin-top:37px;"><br>
    <van-col>
        <span>订单提交成功</span><br>
        <p style="text-align: center;">订单审核通过后，我们将尽快安排邮寄</p>

        <p style="text-align: center;"> 查询物流请点击：</p>
        <div style=" padding:5px 10px; border-radius: 15px; background:#409eff; width: 120px; margin:20px auto;"><a href="query.html" style="color: #fff; font-size: 14px; position: relative; top: -2px;">订单查询</a></div>
    </van-col>
    <!-- <div class="active_pic">
        <a href="../alipay/index.html"><img src="../telRecharge/image/active.png" /></a>
    </div> -->
    <!-- <van-row style="margin-top:10%">
         <van-col span="22">
             <van-popup v-model="show" position="bottom">
                 <span @click="closed" style="margin-left: 60%"><van-icon  name="close" size="20px"/></span><a href="https://glhb.lindingtech.com/ghb2/ghb.html?source=lbk"><img src="public/img/banner.png" id="acp" style="width:100%"></a>
             </van-popup>
         </van-col>
     </van-row>
 </div>
 <script src="https://unpkg.liubangapp.com/vue/dist/vue.min.js"></script>
 <script src="https://unpkg.liubangapp.com/vant/lib/vant.min.js"></script>
 <script>
     Vue.use(vant);
     new Vue({
         data() {
             return {
                 show: false
             }
         },
         created() {
             this.show = true;
         },
         methods: {
             closed() {
                 this.show = false;
             }
         }
     }).$mount("#app");
 </script>
</body>

</html>