var adaptive = {};
(function (win, lib) {
    var doc = win.document;
    var docEl = doc.documentElement;
    // 设备像素比
    var devicePixelRatio = win.devicePixelRatio;
    // 我们设置的布局视口与理想视口的像素比
    var dpr = 1; 
    // viewport缩放值
    var scale = 1; 
    // 设置viewport
    function setViewport() {
        // 判断IOS
        var isIPhone = /iphone/gi.test(win.navigator.appVersion);
        if (lib.scaleType === 2 && isIPhone || lib.scaleType === 3) {
            dpr = devicePixelRatio;
        }
        // window对象上增加一个属性，提供对外的布局视口与理想视口的值
        win.devicePixelRatioValue = dpr;
        // viewport缩放值，布局视口缩放后刚好显示成理想视口的宽度，页面就不会过长或过短了
        scale = 1 / dpr;
        // 获取已有的viewport
        var hasMetaEl = doc.querySelector('meta[name="viewport"]');
        var metaStr = 'initial-scale=' + scale + ', maximum-scale=' + scale + ', minimum-scale=' + scale + ', user-scalable=no';
        if (dpr === 1) {
            metaStr = 'width=device-width, '.concat(metaStr);
        }
        if (!isIPhone && dpr !== 1) {
            metaStr = metaStr.concat(', target-densitydpi=device-dpi');
        }
        // 如果有，改变之
        if (hasMetaEl) {
            hasMetaEl.setAttribute('content', metaStr);
        }
        // 如果没有，添加之
        else {
            var metaEl = doc.createElement('meta');
            metaEl.setAttribute('name', 'viewport');
            metaEl.setAttribute('content', metaStr);
            
            if (docEl.firstElementChild) {
                docEl.firstElementChild.appendChild(metaEl);
            }
            else {
                var containDiv = doc.createElement('div');
                containDiv.appendChild(metaEl);
                docEl.appendChild(containDiv);
            }
        }
    }
    
    var newBase = 100;
    lib.errDpr = 1;

    function setRem() {
        // 布局视口
        // var layoutView = docEl.clientWidth; 也可以 获取布局视口的宽度
        var layoutView;
        if (lib.maxWidth) {
            layoutView = Math.min(docEl.getBoundingClientRect().width, lib.maxWidth * dpr);
        }
        else {
            layoutView = docEl.getBoundingClientRect().width;
        }
        // 为了计算方便，我们规定 1rem === 100px设计图像素，我们切图的时候就能快速转换
        // 有人问，为什么不让1rem === 1px设计像素呢？
        // 设计图一般是640或者750px
        // 布局视口一般是320到1440
        // 计算一个值，使layout的总宽度为 (desinWidth/100) rem
        // 那么有计算公式：layoutView / newBase = desinWidth / 100
        // newBase = 100 * layoutView / desinWidth
        // newBase = 介于50到200之间
        // 如果 1rem === 1px 设计像素，newBase就介于0.5到2之间，由于很多浏览器有最小12px限制，这个时候就不能自适应了
        newBase = 100 * layoutView / lib.desinWidth * (lib.errDpr || 1);
        docEl.style.fontSize = newBase + 'px';
        // rem基准值改变后，手动reflow一下，避免旋转手机后页面自适应问题
        doc.body&&(doc.body.style.fontSize = lib.baseFont / 100 + 'rem');
        // 重新设置rem后的回调方法
        lib.setRemCallback&&lib.setRemCallback();
        lib.newBase = newBase;
    }
    var tid;
    lib.desinWidth = 750;
    lib.baseFont = 28;
    // 局部刷新的时候部分chrome版本字体过大的问题
    lib.reflow = function() {
        docEl.clientWidth;
    };
    // 检查安卓下rem值是否显示正确
    function checkRem() {
        if (/android/ig.test(window.navigator.appVersion)) {
            var hideDiv = document.createElement('p');
            hideDiv.style.height = '1px';
            hideDiv.style.width = '2.5rem';
            hideDiv.style.visibility = 'hidden';
            document.body.appendChild(hideDiv);
            var now = hideDiv.offsetWidth;
            var right = window.adaptive.newBase * 2.5; 
            if (Math.abs(right / now - 1) > 0.05) {
                lib.errDpr = right / now;
                setRem();
            }
            document.body.removeChild(hideDiv);
        }
    }
    lib.init = function () {
        // resize的时候重新设置rem基准值
        // 触发orientationchange 事件时也会触发resize，故不需要再添加此事件了
        win.addEventListener('resize', function () {
            clearTimeout(tid);
            tid = setTimeout(setRem, 300);
        }, false);
        // 浏览器缓存中读取时也需要重新设置rem基准值
        win.addEventListener('pageshow', function (e) {
            if (e.persisted) {
                clearTimeout(tid);
                tid = setTimeout(setRem, 300);
            }
        }, false);
        // 设置body上的字体大小
        if (doc.readyState === 'complete') {
            doc.body.style.fontSize = lib.baseFont / 100 + 'rem';
            checkRem();
        }
        else {
            doc.addEventListener('DOMContentLoaded', function (e) {
                doc.body.style.fontSize = lib.baseFont / 100 + 'rem';
                checkRem();
            }, false);
        }
        setViewport();
        // 设置rem值
        setRem();
        // html节点设置布局视口与理想视口的像素比
        docEl.setAttribute('data-dpr', dpr);
    };
    // 有些html元素只能以px为单位，所以需要提供一个接口，把rem单位换算成px
    lib.remToPx = function (remValue) {
        return remValue * newBase;
    };
})(window, adaptive);
if (typeof module != 'undefined' && module.exports) {
    module.exports = adaptive;
} else if (typeof define == 'function' && define.amd) {
    define(function() {
        return adaptive;
    });
} else {
    window.adaptive = adaptive;
}
