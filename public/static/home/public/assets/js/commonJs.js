$.extend({
    /*
     * 请求封装
     * 如何调用：$._ajax(kinds, url, param, dataType, succCallback, errorCallback);
     * 参数解释：
     *         1.kinds：请求类型 如'post' 'get'
     *         2.url: 请求地址
     *         3.param： 请求参数
     *         4.dataType：数据类型 如‘json' 'jsonp'
     *         5.succCallback：成功回调函数
     *         6.errorCallback：失败回调函数
     *         7.completeCallback：接口完成回调函数
     *         8.state：loading显示状态
     * */
    _ajax: function (state, kinds, url, param, dataType, succCallback, errorCallback, completeCallback, contentType, timeOut) {
        var ajaxUrl = '';
        if (location.href.indexOf('/gray/') > -1 && navigator.userAgent.indexOf('Mashgo-Android/') > -1) {
            ajaxUrl = '/gray' + url;
        } else {
            ajaxUrl = url;
        }
        $.ajax({
            type: kinds,
            url: ajaxUrl,
            data: param,
            dataType: dataType,
            contentType: contentType ? contentType : 'application/x-www-form-urlencoded',
            timeout: timeOut ? timeOut : 20000,
            beforeSend: function () {  // 开始loading
                if (state == 'true') {
                    $(".loading").show();
                }
            },
            success: function (data) {
                if (succCallback) {
                    succCallback(data);
                    if (data.rspCode == '1002') {
                        AGXB.logout({"message": "您的手机号已在其他终端上登录,请重新登录"});
                    }
                }
            },
            error: function (error) {
                if (errorCallback) {
                    errorCallback(error);
                }
            },
            complete: function (XMLHttpRequest, status) {   // 结束loading
                if (completeCallback) {
                    completeCallback(XMLHttpRequest, status);
                }
                if (state == 'true') {
                    $(".loading").hide();
                }
            }
        });
    },
    /*
    * 请求封装2
    * 参数解释：
    *         1.paramJson：请求参数，同ajax原封参数
    * */
    _ajaxSwitch: function (paramJson) {
        var ajaxUrl = '';
        if (location.href.indexOf('/gray/') > -1 && navigator.userAgent.indexOf('Mashgo-Android/') > -1) {
            ajaxUrl = '/gray' + paramJson.url;
        } else {
            ajaxUrl = paramJson.url;
        }
        $.ajax({
            type: paramJson.type,
            url: ajaxUrl,
            data: paramJson.data,
            dataType: paramJson.dataType,
            async: paramJson.async || false,
            jsonp: paramJson.jsonp || '',
            jsonpCallback: paramJson.jsonpCallback || '',
            contentType: paramJson.contentType || 'application/x-www-form-urlencoded',
            beforeSend: function () {
                if (paramJson.beforeSend) {
                    var fun = paramJson.beforeSend;
                    fun();
                }
            },
            ajaxError: function () {
                if (paramJson.ajaxError) {
                    var fun = paramJson.ajaxError;
                    fun();
                }
            },
            success: function (data) {
                if (paramJson.success) {
                    var fun = paramJson.success;
                    fun(data);
                    if (data.rspCode == '1002') {
                        AGXB.logout();
                    }
                }
            },
            error: function () {
                if (paramJson.error) {
                    var fun = paramJson.error;
                    fun();
                }
            },
        });
    },
    // 设置浏览器基础Fontsize
    resize: function (data) {
        var basicData = 375;
        if (data !== undefined) {
            basicData = data;
        }
        var _width = $(window).width();
        var ratio = _width / basicData;
        ratio = ratio > 2 ? 2 : ratio;
        var curWidth = 16 * ratio;
        $('html').css('font-size', curWidth + "px");
        $('.mask').css('height', $(document).height() + 'px');
        // 防止手机字体大小设置偏大，导致页面样式错乱
        var curSize = parseFloat(window.getComputedStyle(document.getElementsByTagName('html')[0], null).getPropertyValue('font-size'));
        if (curSize > curWidth) {
            $('html').css('font-size', Math.pow(curWidth, 2) / curSize + "px");
        }
    },
    // 计算汉字长度
    chineseLen: function (txt) {
        var n = 0;
        var len = txt.length;
        for (var i = 0; i < len; i += 1) {
            if (/[\u4E00-\u9FA5\u3400-\u4DB5\u9FA6-\u9FBB\uF900-\uFA2D\uFA30-\uFA6A\uFA70-\uFAD9]/.test(txt.charAt(i))) {
                n += 1;
            }
        }
        return n;
    },

    /*
     仅支持输入汉字、数字、字母和＃ （）－ 符号
     */
    checkZf: function (text) {
        var flags = false;
        var spaceWord = /^[\sA-Za-z0-9\u4e00-\u9fa5\uFF10-\uFF19\uFF00-\uFFFF()（）,，_—-]+$/;
        flags = spaceWord.test(text);
        return flags;
    },

// 是否为空
    isEmpty: function (s) {
        return typeof s === 'undefined' || s === null || s === '';
    },
// 电话号码格式校验
    checkMobiles: function (number) {
        return /^((13|15|18|14|17|16|19)+\d{9})$/.test(number)
    },
// 活动电话号码格式校验
    checkMobilesActivity: function (number) {
        return /^((130|131|132|155|156|185|186|176|175|166)+\d{8})$/.test(number)
    },
// 校验非法字符
    checkScript: function (text) {
        var flag = false;
        var scriptWord = "<|>|script|alert|{|}|#|$|'|\"|:|;|&|*|@|@|%|^|?";
        var words = scriptWord.split('|');
        for (var i = 0; i < words.length; i += 1) {
            if (text.indexOf(words[i]) !== -1) {
                flag = true;
                break;
            }
        }
        return flag;
    },
    // 有遮罩层时禁止滚动
    noScroll: function (state) {
        $('.mask').css('height', $(document).height() + 'px');
        if (state) {
            var scrollTop = $(document).scrollTop();
            $('.mask').on('touchmove', function (e) {
                e.preventDefault();
                e.stopPropagation();
            });
            $(document).on('scroll', function (e) {
                $(document).scrollTop(scrollTop);
            })
        } else {
            $(document).scrollTop(0);
            $('html, body').addClass('no-scroll');
        }
    },
    // 解除禁止滚动
    reScroll: function () {
        $('.mask').unbind('touchmove');
        $(document).unbind('scroll');
        $('html, body').removeClass('no-scroll');
    },
    // 参数解析
    getUrlParam: function () {
        var args = {};
        var query = location.search.substring(1);
        if (query.indexOf('??') != -1) {
            query = query.slice(0, query.indexOf('??'));
        }
        // console.log('u', u);
        // console.log('p', p);
        // console.log('c', c);
        let str = "&sceneFlag=03&channel=9999&p=" + p + "&c=" + c + "&u=" + u + "&s=03";
        query = query + str;
        var pairs = query.split('&');
        for (var i = 0; i < pairs.length; i += 1) {
            var pos = pairs[i].indexOf('=');
            if (pos !== -1) {
                var argName = pairs[i].substring(0, pos);
                args[argName] = pairs[i].substring(pos + 1);
            }
        }
        // console.log(args);
        return args;
    },
    // 判断是否是安卓设备
    isAndroid: function () {
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1;
        return isAndroid;
    },
    // 数组随机排序
    shuffle: function (arr) {
        var i = arr.length;
        while (i) {
            var j = Math.floor(Math.random() * i--);
            var _ref = [arr[i], arr[j]];
            arr[j] = _ref[0];
            arr[i] = _ref[1];
        }
    },
    // 提交按钮传参
    buttonUrlParam: function (initParam) {
        var urlParam = '';
        $.each(initParam, function (key, values) {
            if (values != undefined && values != 'undefined' && values != '') {
                urlParam += '&' + key + '=' + values;
            }
        });
        return urlParam;
    },
    // 协议展示
    showProtocal: function (params, url, verify, fn) {
        if (verify()) {
            // 协议请求
            $._ajaxSwitch({
                type: 'post',
                url: url,
                dataType: 'json',
                data: params,
                beforeSend: function () {
                    $('.mask').show();
                },
                success: function (protocolData) {
                    var localProtocal = [];
                    $.each(protocolData.rspBody, function (i, val) {
                        localProtocal.push(val.contentStr);
                    });
                    $('#protocol-desc .protocol-title').empty().text('中国联通客户入网协议');
                    $('#protocol-desc .protocol-desc').empty().html(localProtocal.join('<br>'));
                    $('#protocol-desc').show();
                    $('html, body').addClass('no-scroll');
                    if (fn) {
                        fn();
                    }
                },
                ajaxError: function () {
                    $('.mask').hide();
                },
            });
        }
    },
    // 判断环境
    isTest: function () {
        var origin = location.origin;
        if (origin.indexOf('demo.mall.10010.com') > -1 || origin.indexOf('dev.10010.com') > -1) {
            return true;
        }
        return false;
    },
    // 特殊字符转换
    wordChange: function (words) {
        var oldWords = words.split('');
        var newWords = '';
        if (oldWords.length > 0) {
            oldWords.forEach(function (value) {
                var spaceWord = /^[▪•●·.。`,，？?]+$/;
                var flags = spaceWord.test(value);
                if (flags) {
                    value = '·';
                }
                newWords += value;
            });
        }
        return newWords;
    },
    // 识别蓝牙设备
    discernDevice: function (input, deviceInfo) {
        if (input.startsWith('SR', 0)) {
            deviceInfo.deviceBrandCode = 'sunrise';
            deviceInfo.deviceBrandName = '森锐';
        } else if (input.startsWith('HOD-U53', 0)) {
            deviceInfo.deviceBrandCode = 'hhd';
            deviceInfo.deviceBrandName = '三元达';
        } else if (input.startsWith('ST', 0)) {
            deviceInfo.deviceBrandCode = 'senter';
            deviceInfo.deviceBrandName = '信通';
        } else if (input.startsWith('KT8003', 0)) {
            deviceInfo.deviceBrandCode = 'newkaer';
            deviceInfo.deviceBrandName = '卡尔';
        } else if (input.startsWith('KT8000', 0)) {
            deviceInfo.deviceBrandCode = 'kaer';
            deviceInfo.deviceBrandName = '卡尔';
        } else if (input.startsWith('Emini', 0)) {
            deviceInfo.deviceBrandCode = 'emini';
            deviceInfo.deviceBrandName = '忠友';
        } else if (input.startsWith('AZT', 0) || input.startsWith('HT', 0)) {
            deviceInfo.deviceBrandCode = 'azt';
            deviceInfo.deviceBrandName = '安真通';
        } else {
            deviceInfo.deviceBrandCode = null;
            deviceInfo.deviceBrandName = null;
        }
    },
    // 判断APP版本
    checkVersion: function (version) {
        var checkResult = false; // 检查结果：true 大于或等于参照版本号；false 小于参照版本号
        var cVersion = version; // 参照版本号
        var curVersion, // 当前APP版本号
            userAgent = navigator.userAgent; // 当前APP的userAgent
        if ($.isAndroid()) {
            if ($.isTest()) {
                curVersion = userAgent.substring(userAgent.indexOf('Mashgo-Android/') + 15, userAgent.indexOf('.test'));
            } else {
                curVersion = userAgent.substring(userAgent.indexOf('Mashgo-Android/') + 15);
            }
        } else {
            curVersion = userAgent.substring(userAgent.indexOf('Mashgo-iOS/') + 11, userAgent.indexOf('(Build'));
        }

        var curVersionArr = curVersion.split('.'); // 当前APP版本号集合
        var cVersionArr = cVersion.split('.'); // 当前APP版本号集合
        var curVersionMain = parseInt(curVersionArr[0]); // 当前APP版本号--大版本号
        var curVersionMidd = parseInt(curVersionArr[1]); // 当前APP版本号--中版本号
        var curVersionVice = parseInt(curVersionArr[2]); // 当前APP版本号--小版本号
        var cVersionMain = parseInt(cVersionArr[0]); // 参照版本号--大版本号
        var cVersionMidd = parseInt(cVersionArr[1]); // 参照版本号--大版本号
        var cVersionVice = parseInt(cVersionArr[2]); // 参照版本号--小版本号

        if (isNaN(curVersionMain) || isNaN(curVersionMidd) || isNaN(curVersionVice)) { // 截取的版本号若为非数字则代表老版本
            return false;
        }

        if (curVersionMain > cVersionMain) {
            checkResult = true;
        } else if (curVersionMain == cVersionMain) {
            if (curVersionMidd > cVersionMidd) {
                checkResult = true;
            } else if (curVersionMidd == cVersionMidd) {
                if (curVersionVice >= cVersionVice) {
                    checkResult = true;
                }
            }
        }

        return checkResult;
    },
    // 通用脱敏方法
    // str所需要脱敏字符串,startP正数需要展示位数,endP倒数需要展示位数,type脱敏格式
    strStar: function (str, startP, endP, type) {
        var str = str || '';
        var star = [];
        if (str == "未选号") {
            return str;
        }
        if (type === undefined) {
            type = '*';
        }
        if (str.length <= 2) {
            for (var i = 0; i < str.length - 1; i++) {
                star.push(type);
            }
            ;
            star.push(str.substr(-1));
        } else if (str.length == 15) {
            star.push(str.substr(0, startP));
            for (var i = 0; i < str.length - endP - startP + 3; i++) {
                star.push(type);
            }
            ;
            star.push(str.substr(-endP + 3));
        } else {
            star.push(str.substr(0, startP));
            for (var i = 0; i < str.length - endP - startP; i++) {
                star.push(type);
            }
            ;
            star.push(str.substr(-endP));
        }
        star = star.join('');
        return star;
    },
    // 姓名脱敏
    nameStrStar: function (str, startP, endP) {
        var str = str || '';
        var star = [];
        if (str.length <= 2) {
            star.push(str.substr(0, 1));
            star.push('*');
        } else {
            star.push(str.substr(0, startP));
            for (var i = 0; i < str.length - endP - startP; i++) {
                star.push('*');
            }
            ;
            star.push(str.substr(-endP));
        }
        star = star.join('');
        return star;
    },
    // 判断是否端内
    isClient: function () {
        var userAgent = navigator.userAgent;
        if (userAgent.indexOf('Mashgo') > -1) {
            return true;
        } else {
            return false;
        }
    },
    // 复制的手机号可能存在非文本字符格式
    decodeStr: function (str) {
        // console.log(encodeURI(str));
        return decodeURIComponent(encodeURI(str).replace(/%E2%80%AD|%E2%80%AC/igm, '')).replace(/\s|-/igm, '');
    },
    // 根据身份证判断未满指定周岁
    checkAge: function (id, initAge) {
        var strBirthday = id.substr(6, 4) + "/" + id.substr(10, 2) + "/" + id.substr(12, 2);
        var birthDate = new Date(strBirthday);
        var nowDateTime = new Date();
        var age = nowDateTime.getFullYear() - birthDate.getFullYear();
        // console.log(age)
        if (age > initAge) {
            return false;
        } else if (age == initAge) {
            if (nowDateTime.getMonth() > birthDate.getMonth()) {
                return false;
            } else if (nowDateTime.getMonth() == birthDate.getMonth()) {
                if (nowDateTime.getDate() > birthDate.getDate()) {
                    return false;
                }
            }
        }
        return true;
    },
});
(function () {
    /*if (location.href.indexOf('/gray/newMsg/replaceCard') > 0) {
      return;
    }*/
    var src = '//res.mall.10010.cn/mall/mobile/alay/eruda.min.js';
    document.write('<scr' + 'ipt src="' + src + '"></scr' + 'ipt>');
    document.write('<scr' + 'ipt>eruda.init();</scr' + 'ipt>');
})();