<head>
    <link rel="stylesheet" href="{{env('CDN_DOMAIN')}}/static/home/public/assets/css/foot_button.css">
</head>

<div data-v-1bcf673d="" class="detail-btn" id="foot-kefu-show-order" style="display: none">
    <div data-v-1bcf673d="" class="detail-btn-box">
        <uni-button data-v-1bcf673d="" class="detail-btn-show">
            <uni-text data-v-1bcf673d="" class="icon service">
                <span></span>
            </uni-text><a href="#kefu">联系客服</a>
        </uni-button>
        <uni-button data-v-1bcf673d="" class="detail-btn-home" onclick="$('.buttonBox').click()">
            <a href="#立即申请" >立即申请</a>
        </uni-button>
    </div>
</div>

<script>
    setTimeout(
        function () {
            $('#foot-kefu-show-order').slideDown("slow");
        }, (typeof delayed_time == "undefined") ? 2000 : delayed_time);
    setTimeout(
        function () {
            document.getElementById("foot-kefu-show-order").style.display="";
        }, (typeof delayed_time == "undefined") ? 2000 : delayed_time+3000);
</script>
