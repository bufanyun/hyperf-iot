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
    <link rel="stylesheet" type="text/css" href="{{env('CDN_DOMAIN')}}/static/home/public/assets/css/reset.css"/>
    <link rel="stylesheet" type="text/css" href="{{env('CDN_DOMAIN')}}/static/home/public/assets/css/pool.css"/>
    <!--控制屏幕适配的JS-->
    <script type="text/javascript" src="{{env('CDN_DOMAIN')}}/static/home/public/assets/js/adaptive.js"></script>
    <script>
        window['adaptive'].desinWidth = 750; //设计图宽度
        window['adaptive'].baseFont = 24; //没有缩放时的字体大小
        window['adaptive'].maxWidth = 480; // 页面最大宽度 默认540
        window['adaptive'].scaleType = 2; // iphone下缩放，retina显示屏下能精确还原1px;
        window['adaptive'].init();
    </script>
    <!--常用库-->
    <script type="text/javascript" src="{{env('CDN_DOMAIN')}}/static/home/public/assets/js/jquery-1.10.1.min.js"></script>
    <script type="text/javascript" src="{{env('CDN_DOMAIN')}}/static/home/public/assets/js/slider.js"></script>
</head>

@include('Home.common.loading')

<body>
<!-- banner -->
<div class="m-slider" data-ydui-slider>
    <div class="slider-wrapper">
        <div class="slider-item">
            <a href="https://lh.dianruikj.com/?exn=bz">
                <img src="http://dhk-cdn.nyxiecheng.com/uploads/2020091116002122e712859.png" alt=""/>
            </a>
        </div>
        <div class="slider-item">
            <a href="https://lh.dianruikj.com/?exn=bz">
                <img src="http://dhk-cdn.nyxiecheng.com/uploads/2020091116002122e712859.png" alt=""/>
            </a>
        </div>
    </div>
    <div class="slider-pagination"></div>
</div>

<div class="fun-entry">
    <ul class="entry-ul flex flex-pack-justify">
        <li><img class="nav_img" src="data:image/png;base64,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" alt="" /> <p class="nav_btn">靓号商城</p></li>

{{--        <li><img class="nav_img" src="data:image/png;base64,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" alt="" /> <p class="nav_btn">靓号商城</p></li>--}}
        <li><a href="http://card.wifibanlv.com/order/login"><img class="nav_img" src="data:image/png;base64,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" alt="" /> <p class="nav_btn">订单查询</p></a></li>
        <li><img class="nav_img" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHsAAAB7CAMAAABjGQ9NAAADAFBMVEUAAAD/i1X/iVP/ilT/i1P/i1T/elb/lFH/k1L/dlP/mE//dFP/mk//mU//mE//lFL/eFP/kVb/m0//mU//m0//clP/m0//dVP/kFD/mk//dFP/mU//kFX/lVP/fFP/c1T/jFH/clT/j1D/llH/d1T/llD/m0//mk//c1T/hWT/l0//mU//c1T/llD/mU//e1j/c1P/jVb/d1P/jVL/clT/jlL/jWP/jlD/hWT/klD/cFT/i1H/k1D/jFD/mU//dFP/j13/m0//hGT/l0//mk//elL/d1P/iGD/mE//gFj/dFP/l0//dlP/mU//lFL/m0//cVT/klr/jV//klr/hGD/dVX/gVz/iWP/gFL/klD/jl//klv/elT/d1P/kVz/il3/hWT/m3P/mG//jF//h1H/flP/gVv/hWb/f1f/mG3/m3H/m3H/gFf/mnH/imb/mXH/ilH/ilH/mnH/kmj/m3L/////lFD/mk//l0//j1D/h1H/e1P/kVD/jFH/flL/gVL/fFL/d1P/ilH/fFP/eVP/dFP/clT/g1L/cVT/glL/j1z/qoj/i1D/jm//lmr/g13/hVL/jWz/glX/h13/iVH/hV//iGL/iGX/imP/hFj/i2j/hmb/glr/glf/lGj/pH3/mG3/iWv/jGr/j2D/jGD/gFn/iGj/hWL/iWD/kWH/i1r/jmT/kV//hVv/hFb/kXL/jWr/lGD/mm3/h2j/lGX/hGL/i23/kWj/l2b/gVz/i1b/mW//kWT/k2L/kG//lmL/kVz/j1n/jlX/kHH/jW7/mGr/kWX/gVP/il3/k1z/fVj/m2//mmf/jGX/h1//hln/knT/n3H/gmD/hFr/jFP/+/r/nW//nWz/lGT/lWz/k2r/jmf/imX/lVz/mWT/jFz/i2b/m2n/d1b/g1T/mGD/iVX/f1X/8+3/gFz/7eb/3c3/5db/xq7/e1z/4NP/lFb/1Lv/vqT/uZ//wZ3/r47/q3n/nnf/0L//y7n/uJD/rX//9fH/6dz/5dr/klT/qopLB2lSAAAAdXRSTlMAFAsDEAcrRhcXncff0ccs+iT899bSjGllZGNeNinb2tq2tmpF/PDp5NzauKOPfDv3qJySjn1l9u/u7ePdy8K/t7OvrqSPfXtyZFNST049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" alt="" /> <p class="nav_btn">号卡激活</p> </li>
    </ul>
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
                            <div class="wk_biaoti">{{ $sale->kind_name }}</div>
                            <div class="wk_wenan">{{ $sale->titile }}</div>
                            <div class="conduct">
                                <a style="color: #ffffff;" href="/home/spread/product_show?sid={{ $sale->id }}&{{http_build_query($reqParam)}}">
                                    <div>立即办理</div>
                                </a>
                            </div>
                        </div>
                    </div>
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
            <div class="wangka_taocan">
                <div class="wangka_box">
                    <div class="wk_display">
                        <img src="{{ $sale->icon }}" alt="">
                        <div>
                            <div class="wk_biaoti">{{ $sale->kind_name }}</div>
                            <div class="wk_wenan">{{ $sale->titile }}</div>
                            <div class="conduct">
                                <a style="color: #ffffff;" href="/home/spread/product_show?sid={{ $sale->id }}&{{http_build_query($reqParam)}}">
                                    <div>立即办理</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
<div id="tab3" class="tab_list" style="display: none;">
    @foreach ($sales as $sk => $sale)
        @if ($sale->cid == 3)
            <div class="wangka_taocan">
                <div class="wangka_box">
                    <div class="wk_display">
                        <img src="{{ $sale->icon }}" alt="">
                        <div>
                            <div class="wk_biaoti">{{ $sale->kind_name }}</div>
                            <div class="wk_wenan">{{ $sale->titile }}</div>
                            <div class="conduct">
                                <a style="color: #ffffff;" href="/home/spread/product_show?sid={{ $sale->id }}&{{http_build_query($reqParam)}}">
                                    <div>立即办理</div>
                                </a>
                            </div>
                        </div>
                    </div>
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
                    <div class="wk_display_hot">
                        <img src="{{ $sale->icon }}" alt="">
                        <div>
                            <div class="wk_biaoti">{{ $sale->kind_name }}</div>
                            <div class="wk_wenan">{{ $sale->titile }}</div>
                            <div class="conduct">
                                <a style="color: #ffffff;" href="/home/spread/product_show?sid={{ $sale->id }}&{{http_build_query($reqParam)}}">
                                    <div>立即办理</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
</body>

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
@include('Home.common.uinapp-sdk')
</html>
