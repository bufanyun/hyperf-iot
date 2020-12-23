
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>号卡中心</title>
    <meta name="title" content="号卡中心"/>
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
    <link rel="stylesheet" type="text/css" href="/static/home/public/assets/css/reset.css"/>
    <link rel="stylesheet" type="text/css" href="/static/home/public/assets/css/yonhugoumai.css"/>
    <!--控制屏幕适配的JS-->
    <script type="text/javascript" src="/static/home/public/assets/js/adaptive.js"></script>
    <script>
        window['adaptive'].desinWidth = 750; //设计图宽度
        window['adaptive'].baseFont = 24; //没有缩放时的字体大小
        window['adaptive'].maxWidth = 480; // 页面最大宽度 默认540
        //window['adaptive'].scaleType = 2; // iphone下缩放，retina显示屏下能精确还原1px;
        window['adaptive'].init();
    </script>
    <!--常用库-->
    <script type="text/javascript" src="/static/home/public/assets/js/jquery-1.10.1.min.js"></script>
    <script type="text/javascript" src="/static/home/public/assets/js/slider.js"></script>
</head>

<body>
<!-- banner -->
<div class="m-slider" data-ydui-slider>
    <div class="slider-wrapper">
        <div class="slider-item">
            <a href="https://lh.dianruikj.com/?exn=bz">
                <img src="http://dhk-cdn.nyxiecheng.com/uploads/2020091116002122e712859.png" alt=""/>
            </a>
        </div>
    </div>
    <div class="slider-pagination"></div>
</div>

<!-- 号卡 -->
<div class="haoka_box">
    <div class="haoka_margin">
        @forelse ($classifys as $ck => $classify)
            <div class="move kaxinghao @php echo  ($ck===0 ? 'kaxinghao_er' : '');@endphp" data-k="{{ $classify->id }}">
                {{ $classify->name }}
            </div>
        @empty
            <p>暂时没有分类</p>
        @endforelse

    </div>
</div>
<!-- 套餐 -->
<div id="tab1" class="tab_list" style="display: none;">
    @forelse ($sales as $sk => $sale)
        @if ($sale->cid == 1)
            <div class="wangka_taocan">
                <div class="wangka_box">
                    <div class="wk_display">
                        <img src="{{ $sale->icon }}" alt="">
                        <div>
                            <div class="wk_biaoti">{{ $sale->name }}</div>
                            <div class="wk_wenan">{{ $sale->titile }}</div>
                        </div>
                    </div>
                    <button class="wk_banli">
                        <a href="/home/spread/product_show?sid={{ $sale->id }}&job_number={{$job_number}}">
                            <div>立即办理</div>
                        </a>
                    </button>
                </div>
            </div>
        @endif
    @empty
        <p>暂时没有商品</p>
    @endforelse
</div>

<div id="tab2" class="tab_list" style="display: none;">
    @foreach ($sales as $sk => $sale)
        @if ($sale->cid == 2)
            {{--            @continue--}}
            <div class="wangka_taocan">
                <div class="wangka_box">
                    <div class="wk_display">
                        <img src="{{ $sale->icon }}" alt="">
                        <div>
                            <div class="wk_biaoti">{{ $sale->name }}</div>
                            <div class="wk_wenan">{{ $sale->titile }}</div>
                        </div>
                    </div>
                    <button class="wk_banli">
                        <a href="/home/spread/product_show?sid={{ $sale->id }}&job_number={{$job_number}}">
                            <div>立即办理</div>
                        </a>
                    </button>
                </div>
            </div>
        @endif
    @endforeach
</div>
<div id="tab3" class="tab_list" style="display: none;">
    @foreach ($sales as $sk => $sale)
        @if ($sale->cid == 3)
            {{--            @continue--}}
            <div class="wangka_taocan">
                <div class="wangka_box">
                    <div class="wk_display">
                        <img src="{{ $sale->icon }}" alt="">
                        <div>
                            <div class="wk_biaoti">{{ $sale->name }}</div>
                            <div class="wk_wenan">{{ $sale->titile }}</div>
                        </div>
                    </div>
                    <button class="wk_banli">
                        <a href="/home/spread/product_show?sid={{ $sale->id }}&job_number={{$job_number}}">
                            <div>立即办理</div>
                        </a>
                    </button>
                </div>
            </div>
        @endif
    @endforeach
</div>
<div id="tab4" class="tab_list" style="display: none;">
    @foreach ($sales as $sk => $sale)
        @if ($sale->cid == 4)
            <div class="wangka_taocan">
                <div class="wangka_box">
                    <div class="wk_display">
                        <img src="{{ $sale->icon }}" alt="">
                        <div>
                            <div class="wk_biaoti">{{ $sale->name }}</div>
                            <div class="wk_wenan">{{ $sale->titile }}</div>
                        </div>
                    </div>
                    <button class="wk_banli">
                        <a href="/home/spread/product_show?sid={{ $sale->id }}&job_number={{$job_number}}">
                            <div>立即办理</div>
                        </a>
                    </button>
                </div>
            </div>
        @endif
    @endforeach
</div>
</body>
</html>
<script>
    $(document).ready(function () {
        $('#tab1').show();
        //切换tab
        $('.move').click(function () {
            let k = $(this).data('k');
            $(this).addClass("kaxinghao_er").siblings().removeClass("kaxinghao_er");
            $('.tab_list').hide();
            $('#tab' + k).show();
        });
    })
</script>