<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"/>
    <meta name="format-detection" content="telephone=no"/>
    <title>资料填写</title>
    <link rel="stylesheet" href="/static/home/public/assets/css/fill.css">
</head>
<body>
<section class="fill-wrapper">
    <div class="fill">
        <h2 id="fill-desc">已选择 <span>{{$product->name}}</span></h2>
        <h2 id="top-desc" hidden></h2>
        <ul class="fill-list">
            <li id="apply-name">
                <div class="p-title">姓名</div>
                <div class="p-content">
                    <input id="certName" type="text" title="姓名" placeholder="请输入身份证件姓名" maxlength="20"/>
                </div>
            </li>
            <li id="apply-id">
                <div class="p-title">身份证</div>
                <div class="p-content ">
                    <input id="certNo" type="text" title="身份证" placeholder="请输入身份证号" maxlength="18"/>
                    <i class="errorI"></i>
                </div>
            </li>
            <li id="apply-phone">
                <div class="p-title">联系电话</div>
                <div class="p-content ">
                    <input id="mobilePhone" type="tel" title="联系电话" placeholder="请输入联系电话" maxlength="11"/>
                </div>
            </li>
{{--            <li id="apply-yzm">--}}
{{--                <div class="p-title">验证码</div>--}}
{{--                <div class="p-content">--}}
{{--                    <input id="captchaText" class="yzmInput" maxlength="4" type="text" title="请输入验证码" placeholder="请输入验证码"/>--}}
{{--                    <span class="rightI" style="display: none"></span>--}}
{{--                    <span class="yzm" id="captcha">获取验证码</span>--}}
{{--                </div>--}}
{{--            </li>--}}
        </ul>
    </div>
    <div class="voiceYzmTip" hidden>我们将通过电话方式告知您验证码，请注意接听</div>
    <div class="fill fill-two" id="postDistrict">
        <div class="voiceCaptcha" hidden>*收不到验证码？试试<a href="javascript:;" id="voiceCaptcha">语音验证码&gt;</a></div>
        <h2>请填写配送地址<span>(支持全国配送，新疆、西藏仅限省内配送)</span></h2>
        <ul class="fill-list">
            <li id="delivery">
                <div class="p-title">所在地区</div>
                <div class="p-content p-select grey arr">请选择区/县</div>
            </li>
            <li class="delivery" id="delivery-desc">
                <div class="p-content p-text-area">
                    <p class="text-temp" id="address-temp"></p>
                    <textarea id="address" class="text-area" name="address" rows="1" title="delivery-desc" placeholder="街道/镇+村/小区/写字楼+门牌号"></textarea>
                </div>
            </li>
        </ul>
    </div>
    <div class="fill fill-two">
        <h2>请选择号码<span class="mainNum"></span></h2>
        <ul class="fill-list">
            <li id="location">
                <div class="p-title">号码归属</div>
                <div class="p-content p-select grey arr">请选择号码归属地</div>
            </li>
            <li id="number">
                <div class="p-title">选择号码</div>
                <div class="p-content p-select"></div>
            </li>
        </ul>
        <p class="numberTips">您已选择靓号，协议期为入网当月及后续的<i>12</i>个月。</p>
    </div>
</section>
<section class="apply">
    <div class="protocol-div">
        <p class="protocol agree" id="protocol">
            <i class="protocol-radio"></i>我已阅读并同意<a href="javascript:;" id="go-protocol">《客户入网服务协议及业务协议》</a>
        </p>
        <p class="protocol"><a href="javascript:;" id="go_notice">《关于客户个人信息收集、使用规则的公告》</a></p>
        <p class="international">国际/港澳台漫游和国际/港澳台长途功能将于号码激活时同步生效</p>
    </div>
</section>
<div class="sBtn">
    <div class="secondaryMarketing" hidden><span class="seMarkSel">推荐人信息</span>
        <p class="seMarkInput" hidden><input maxlength="11" placeholder="请填写推荐人号码" type="text" oninput="this.value=this.value.replace(/[^0-9]+/,'');"></p></div>
    <div class="btn-box">
        <a id="submit" href="javascript:;" class="btn">立即提交，免费送货上门</a>
    </div>
</div>
<div class="privacy">
    <p>请保持联系号码畅通，我们可能随时与您联系。本次为阶段性优惠活动，</p>
    <p>数量有限，联系电话无人接听或恶意下单情况，将不予发货。</p>
</div>
<section id="area" class="sidebar location">
    <ul id="province" class="first-list">
    </ul>
    <ul id="city" class="second-list">
    </ul>
</section>
<section id="post" class="sidebar location">
    <ul id="post-province" class="first-list">
    </ul>
    <ul id="post-city" class="second-list">
    </ul>
    <ul id="post-district" class="third-list">
    </ul>
</section>
<section id="TCaptcha" class="popup" hidden>
    <div class="center">下单安全验证</div>
    <input type="hidden" id="ticket" name="ticket" value="">
    <a class="popup-close" href="JavaScript:;" hidden></a>
</section>
<section id="number-popup" class="popup number" hidden>
    <div class="content">
        <div class="search">
            <p class="numTips" hidden>请您选择号码<span>(选号别纠结，以后可以免费换号)</span></p>
            <input id="search" type="tel" class="search-input" placeholder="生日、幸运数字等">
            <a id="search-btn" href="javascript:;" class="search-btn"></a>
            <a id="search-close-btn" href="javascript:;" class="search-close-btn" hidden><i><img src="http://dhk-cdn.nyxiecheng.com/assets/apply/images/number-close.png"></i></a>
        </div>
        <div class="number-wrap">
            <ul class="number-list">
            </ul>
            <div class="number-loading">正在加载，请稍后... ...</div>
            <p class="no-number" hidden>抱歉没有匹配的号码</p>
        </div>
        <a id="refresh" href="javascript:;" class="refresh">换一批</a>
        <div class="occupyTips" hidden>
            <p>正在为您预占号码... <br/>号码选定后请在30分钟内下单</p>
        </div>
    </div>
    <a class="popup-close" href="JavaScript:;" data-type="2"></a>
</section>
<section id="protocol-desc" class="popup" hidden>
    <div class="content">
        <h2 class="protocol-title"></h2>
        <div class="protocol-desc">
        </div>
    </div>
    <a class="popup-close" href="JavaScript:;" data-type="2"></a>
</section>
<section id="error" class="popup" hidden>
    <div class="content">
        <img class="popup-icon" src="http://dhk-cdn.nyxiecheng.com/assets/apply/images/error.png" alt="reserved">
        <p class="popup-title">手慢了</p>
        <p id="reserved-number" class="popup-desc center"><span></span>号码已被抢占。</p>
        <div class="btn-box">
            <a id="reselect-number" href="javascript:;" class="btn">别灰心，再选一个吧</a>
        </div>
    </div>
    <a class="popup-close" href="JavaScript:;" data-type="2"></a>
</section>
<section id="errorAll" class="popup" hidden>
    <div class="content">
        <img class="popup-icon" src="http://dhk-cdn.nyxiecheng.com/assets/apply/images/overtime.png" alt="reserved">
        <p class="popup-desc center"></p>
    </div>
    <a class="popup-close" href="JavaScript:;" data-type="3"></a>
</section>
<section id="overtime" class="popup" hidden>
    <div class="content">
        <img class="popup-icon" src="http://dhk-cdn.nyxiecheng.com/assets/apply/images/overtime.png" alt="overtime">
        <p class="popup-title">超时了</p>
        <p class="popup-desc center">抱歉，请求超时，</p>
        <p class="popup-desc center">请您再试一次吧！</p>
    </div>
    <a class="popup-close" href="JavaScript:;" data-type="2"></a>
</section>
<section id="since" class="popup" hidden>
    <div class="since-content">
        <h3 class="title">您填写的配送区域可到现场办理:</h3>
        <ul></ul>
    </div>
    <div class="SinceOrNo">
        <a class="sinceBtn" href="JavaScript:;">营业厅自提</a>
        <a class="noSince" href="JavaScript:;">不自提，物流配送</a>
    </div>
</section>
<section id="fail" class="popup" hidden>
    <div class="content">
        <img class="popup-icon" src="http://dhk-cdn.nyxiecheng.com/assets/apply/images/overtime.png" alt="fail">
        <p class="popup-title">抱歉</p>
        <p class="popup-desc center">产品销售太火爆啦，请您稍后重试</p>
    </div>
    <a class="popup-close" href="JavaScript:;" data-type="3"></a>
</section>
<section id="success" class="popup" hidden>
    <div class="content">
        <img src="http://dhk-cdn.nyxiecheng.com/assets/apply/images/successImg.png" alt="">
        <h2 class="p-title">提交成功</h2>
        <div class="point-list">
            <p class="point">我们将尽快为您配送，请在收到卡后的10天内激活使用，过期将被回收哦！</p>
        </div>

    </div>
    <a class="popup-close" id="successClose" href="JavaScript:;" data-type="4"></a>
</section>
<div class="subLoad" hidden>
    <img src="http://dhk-cdn.nyxiecheng.com/assets/apply/images/loading.gif"/>
</div>
<div class="numErrorTips" style="display: none;"> 设备未准备好</div>
<div class="mask" hidden></div>
</body>
<script>
    let u = "xwku2YAd9bm%20h/khvLl6dw==";
    let p = "51";
    let c = "510";
    let plat_num = "41";
    let g_u = "QbgpNxWaqpufnPFxGWLPMQ==";
    let g_p = "51";
    let g_c = "510";
    let g_num = "52";
    let is_other_plat = "1";
    let is_move = "1";
    let wangka = "0";
    console.log('wangka', wangka);
    //  ;(function () {
    //    var src = '//cdn.bootcss.com/eruda/1.4.2/eruda.min.js';
    //    document.write('<scr' + 'ipt src="' + src + '"></scr' + 'ipt>');
    //    document.write('<scr' + 'ipt>eruda.init();</scr' + 'ipt>');
    //  })();
</script>
<script src="/static/home/public/assets/js/jquery-2.1.4.min.js"></script>
<script src="/static/home/public/assets/js/id_check.js"></script>
<script src="/static/home/public/assets/js/commonJs.js"></script>
<!--<script src="../../common/js/areaInfo.js"></script>-->
<script src="/static/home/public/assets/js/areaInfo.js"></script>
<script src="/static/home/public/assets/js/commonCheckFill.js"></script>
<!--<script src="http://dhk-cdn.nyxiecheng.com/static/home/public/assets/js/index.js?r=1212"></script>-->
<script src="/static/home/public/assets/js/index.js?r=8752134"></script>
<script>
    $('.eruda-entry-btn').hide();
</script>
</html>