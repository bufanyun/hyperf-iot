<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{$product->name}} | {{$product->titile}}</title>
    <meta name="title" content="商家买单"/>
    <meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,minimal-ui" name="viewport">
    <!--允许苹果浏览器全屏浏览-->
    <meta content="yes" name="apple-mobile-web-app-capable">
    <!--指定的iphone中safari顶端的状态条的样式-->
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <!--设备忽略将页面中的数字识别为电话号码-->
    <meta content="telephone=no" name="format-detection">
    <!--去除Android平台中对邮箱地址的识别-->
    <meta content="email=no" name="format-detection"/>
    <!--解决iOS 4.3版本中safari对页面中5位数字的自动识别和自动添加样式-->
    <meta name="format-detection" content="telphone=no"/>
    <!--重置样式表-->
    <link rel="stylesheet" type="text/css" href="{{env('CDN_DOMAIN')}}/static/home/public/assets/css/reset.css"/>
    <link rel="stylesheet" type="text/css" href="{{env('CDN_DOMAIN')}}/static/home/public/assets/css/dashengka.css"/>
    <!--控制屏幕适配的JS-->
    <script type="text/javascript" src="{{env('CDN_DOMAIN')}}/static/home/public/assets/js/adaptive.js"></script>
    <script>
        window['adaptive'].desinWidth = 750; //设计图宽度
        window['adaptive'].baseFont = 24; //没有缩放时的字体大小
        window['adaptive'].maxWidth = 480; // 页面最大宽度 默认540
        //window['adaptive'].scaleType = 2; // iphone下缩放，retina显示屏下能精确还原1px;
        window['adaptive'].init();
    </script>
    <!--常用库-->
    <script type="text/javascript" src="{{env('CDN_DOMAIN')}}/static/home/public/assets/js/jquery-1.10.1.min.js"></script>

    <style>
        .btnImg {
            position: fixed;
            bottom: 0px;
            width: 100%;
            height: 60px;
            text-align: center;
            margin-bottom: 0.65rem;
        }
        .btnImg img {
            display: inline-block;
            width: 259px;
            height: 60px;
            pointer-events: none;
        }
        img {
            vertical-align: middle;
        }
        img {
            border: 0;
        }
        .detail-btn[data-v-1bcf673d] {
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -webkit-align-items: center;
            align-items: center;
            position: fixed;
            bottom: 0;
            /*left: 0;*/
            width: 100vw;
            height: 64px;
            background-color: #fff;
        }
        .detail-btn .detail-btn-box[data-v-1bcf673d] {
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            width: 359px;
            bottom: 38px;
        }
        .detail-btn .detail-btn-show[data-v-1bcf673d] {
            width: 180px;
            height: 47px;
            line-height: 47px;
            color: #fff;
            background: -webkit-gradient(linear,right top,left top,from(#f8a06a),to(#f7c76b));
            background: -webkit-linear-gradient(right,#f8a06a,#f7c76b);
            background: linear-gradient(270deg,#f8a06a,#f7c76b);
            -webkit-border-radius: 23px 0 0 23px;
            border-radius: 23px 0 0 23px;
            font-size: 17px;
            border: none;
        }
        uni-button {
            position: relative;
            display: block;
            margin-left: auto;
            margin-right: auto;
            padding-left: 14px;
            padding-right: 14px;
            box-sizing: border-box;
            font-size: 18px;
            text-align: center;
            text-decoration: none;
            line-height: 2.55555556;
            border-radius: 5px;
            -webkit-tap-highlight-color: transparent;
            overflow: hidden;
            color: #000;
            background-color: #f8f8f8;
            cursor: pointer;
        }
        .detail-btn .service[data-v-1bcf673d] {
            margin-right: 5px;
        }
        .icon {
            font-family: icon!important;
            font-size: 16px;
            font-style: normal;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .detail-btn .detail-btn-home[data-v-1bcf673d] {
            width: 180px;
            height: 47px;
            line-height: 47px;
            color: #fff;
            background: -webkit-gradient(linear,left top,right top,from(#2392ff),to(#014afb));
            background: -webkit-linear-gradient(left,#2392ff,#014afb);
            background: linear-gradient(90deg,#2392ff,#014afb);
            -webkit-border-radius: 0 23px 23px 0;
            border-radius: 0 23px 23px 0;
            font-size: 17px;
            border: none;
        }

    </style>
</head>

<body>
<img src="https://static.91haoka.cn/1607747146iPM.jpg" alt="">
<img src="https://static.91haoka.cn/1607920162TFt.jpg" alt="" class="">

@include('Home.common.kefu-order-show')

</body>
</html>