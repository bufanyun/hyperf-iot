<style>
    #progress {
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        /*left: 0;*/
        /*z-index: 10;*/
        background-color: #ffffff;
    }
    #progress .loading {
        width: 53px;
        height: 53px;
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        right: 0;
        margin: auto;
        overflow: hidden;
        text-align: center;
    }
    .loading img {
        width: 100%;
        height: 100%;
    }
    @keyframes loading {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    @-webkit-keyframes loading {
        0% {
            -webkit-transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
        }
    }
</style>
<div id="progress">
    <div class="loading">
        <img src="{{env('CDN_DOMAIN')}}/static/home/public/assets/images/loading.gif" alt="loading">
    </div>
</div>
<script>
    window.onload = function () {
        document.getElementById("progress").style.display = "none";
    };
</script>