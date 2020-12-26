<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
    <script type="text/javascript"
            src="{{env('CDN_DOMAIN')}}/static/home/public/assets/js/jquery-1.10.1.min.js"></script>
</head>
<body>

<!--产品描述-->
{!!$product->first_desc!!}
<div class="buttonBox" style="display: none"></div>
<script>
    var delayed_time = 10; //直接加载立即申请
    $('.buttonBox').on('click', function () {
        location.href = '/home/spread/plat_apply?sid={{$product->id}}&{!!http_build_query($reqParam)!!}';
    });
</script>

@include('Home.common.kefu-order-show')
</body>
</html>