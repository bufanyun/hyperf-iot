var commonCheck = window.commonCheckFill;
var Area = window.allAreaNew;
// 提交订单状态
var subStatus = true;
// 判断地市是否为新疆
var provinceXJ = '';
// 所在地区是否重置
var curCity = '';
// 保存初始化号码
var initNum;
// 获取成功返回后的tendencyId
var tendenceId = '';
var reqData = '';
// 主卡信息
var mainCardInfo;
// 成功弹框文案展示标识
var successFlag = true;
// 是否线上购现场购
var disFlag;
// 省份是否可切换号码归属
var changePflag;
// 是否小程序首页进入的订单
var smallRoutineFlag;
var req = {};
// 特殊省份处理：广东、湖南
var specialProvince = {
    '51': '广东'
};
$(function () {
    /*
    * 1、现场购端外下单、触点购：禁止切换号码归属地
    * 2、学习王卡、触点购：禁止切换邮寄地址
    * 3、现场购端外下单隐藏配送地址
    * 4、广东、湖南的登录账号（url:p&c）：若配送地市选择了广东、湖南，则号码归属地为登录账号归属地市
    * 5、(1)收货地址为新疆、西藏：仅可选本省归属号码
    *   (2)收货地址为非新疆、西藏：新疆、西藏号码不可选
    * 6、queryProvinceSwitch接口省份限制：禁止切换号码归属地
    * */
    // 姓名特殊字符转化
    $('#certName').keyup(function (e) {
        var _this = $(e.currentTarget);
        _this.val($.wordChange(_this.val()));
    });
    $.resize();
    // 初始化参数
    var initParam = $.getUrlParam();
    // 判断是否有参数t,来自小程序的订单,如果有则提交订单的参数增加这两者
    if (initParam.t) {
        req.t = initParam.t;
        req.openId = initParam.openId;
    }
    // 客户中心入口订单
    var custCenterFlag = initParam.ccFlag || 0;
    // true为小程序首页进入, false为码上购小程序分享
    if (initParam.miniprogram) {
        smallRoutineFlag = true;
    } else {
        smallRoutineFlag = false;
    }
    // 腾讯王卡展示：选号别纠结，以后可以免费换号 提示
    var txGoods = $.isTest() ? ['981609180703', '981801120053', '981702226311'] : ['981802085690', '981702278573', '981610241535'];
    if (txGoods.indexOf(initParam.goodsId) >= 0) {
        $('.numTips').show();
    } else {
        $('.search-btn,.search-close-btn').css({
            top: 0
        });
    }
    // 判断是否是学习王卡
    var isXuexi = ['981906267425'];

    if (isXuexi.indexOf(initParam.goodsId) >= 0) {
        isXuexi = true;
    } else {
        isXuexi = false;
    }
    // 触点购判断
    var isTouch = false;
    if (initParam.ti != undefined) {
        isTouch = true;
        $('.secondaryMarketing').hide();
    } else {
        isTouch = false;
    }
    // 是否禁止跨地市
    var isKua = false;
    if (isXuexi || isTouch) {
        isKua = true;
    } else {
        isKua = false;
    }
    // popup弹框展示
    var popupShow = function (text) {
        $('.numErrorTips').show().html(text);
        setTimeout(function () {
            $('.numErrorTips').hide();
            $.reScroll();
        }, 3000)
    };
    if (initParam.sceneFlag.substr(0, 2) == '02') {
        disFlag = false;
        req.snFlag = '0';
        $('#submit').html('立即提交');
    } else if (initParam.sceneFlag.substr(0, 2) == '03' || initParam.sceneFlag.substr(0, 2) == '04') {
        disFlag = true;
        $('#submit').html('立即提交，免费送货上门');
    }
    if (!disFlag) {
        $('#postDistrict').hide();
    }
    // 主卡信息
    if (initParam.isMainSubFlag == '1') {
        mainCardInfo = JSON.parse(localStorage.getItem('mainCardInfo'));
        /*    var mainCardInfo = {
              province:'11',
              city:'110',
              mainCardNum: ' ',
              mainCardName: 'wzb',
              mainCardId: 'wzb'
            };*/
        $('.mainNum').text('（主卡号码：' + mainCardInfo.mainCardNum + '）');
    }
    // 号码查询参数
    var numberParam = {};
    // 并发请求限制
    var requestFlag = false;
    // 有遮罩层时禁止滚动
    var noScroll = function () {
        $('html, body').addClass('no-scroll');
    };
    var reScroll = function () {
        $('html, body').removeClass('no-scroll');
    };
    // 顶部描述
    var _topText = '根据国家实名制要求, 请准确提供身份证信息';
    // var _fillDesc = initParam.productName || localStorage.getItem('product_name');
    // $('#fill-desc').find('span').text(decodeURIComponent(_fillDesc));
    // 设置产品信息
    var setProduct = function () {
        $('#top-desc').show().text(_topText);
    };

    let privacy_width = $(window).width() > 640 ? 640 : $(window).width();
    $('.privacy').css('width', (privacy_width / 0.75) + 'px');
    // 请求参数初始化
    var setReq = function () {
        req.numInfo = {};
        // 如果链接没有省份地市,则初始化的省份地市为北京
        if (initParam.p) {
            req.numInfo.essProvince = initParam.p;
        } else {
            req.numInfo.essProvince = "11";
        }
        if (initParam.c) {
            req.numInfo.essCity = initParam.c;
        } else {
            req.numInfo.essCity = "110";
        }
        req.goodInfo = {};
        req.goodInfo.goodsId = initParam.goodsId;
        req.goodInfo.sceneFlag = initParam.sceneFlag.substr(0, 2);
    };
    // 生成号码列表
    var listNumber = function () {
        if (numberParam.list.length == 0) {
            $('.number-list').html('无号码');
            return;
        }
        var _start = (numberParam.current - 1) * numberParam.size;
        var _end = _start + numberParam.size;
        if (numberParam.current == numberParam.max) {
            _end = numberParam.list.length;
        }
        var numberHtml = [];
        for (var i = _start; i < _end; i += 1) {
            var numberObj = numberParam.list[i];

            let rand  = Math.floor(Math.random() * 5);
            // if (numberObj.niceRule == 0) {
            // console.log('rand:'+rand);
            endNum = numberObj.number.charAt(numberObj.number.length-1);
            if (rand !== 0 && endNum !== 4) {
                numberHtml.push("<li><a href='javascript:;' data-niceRule='" + numberObj.niceRule + "' data-monthLimit='" + numberObj.monthLimit + "' data-advanceLimit='" + numberObj.advanceLimit + "' >" + numberObj.number + "</a></li>");
            } else {
                numberHtml.push("<li><a href='javascript:;' data-niceRule='" + numberObj.niceRule + "' data-monthLimit='" + numberObj.monthLimit + "' data-advanceLimit='" + numberObj.advanceLimit + "' ><i>靓</i>" + numberObj.number + "</a></li>");
            }
        }
        numberParam.current += 1;
        $('.number-list').html(numberHtml);
    };

    // 解析号码
    function decompress(number) {
        // console.log('number:'+JSON.stringify(number));
        if(number.code !== 20000){
            numberParam.current = 0;
            $('.number-loading').hide();
            $('.no-number').text(number.msg).show();
            $('#refresh').text('再试一次');
            return;
        }
        var mlist = ['M2', 'M3', 'M4', 'M5'];
        var _key = $('#search').data('val');
        if (number.data.flexData.length == 0) {
            // if ($.inArray(number.code, mlist) > -1) {
            //     $('.no-number').html('当前网络不给力，请在wifi或其他网络环境下重试！<span class="error-code">' + number.code + '</span>').show();
            // } else if (($.inArray(number.code, mlist) == -1)) {
            //     if (commonCheck.isEmpty(_key)) {
            //         $('.no-number').html('当前选号人数过多，请您稍后再试！<span class="error-code">' + number.code + '</span>').show();
            //         $('#refresh').text('再试一次');
            //     } else {
            //         $('.no-number').html('抱歉没有匹配的号码.<span class="error-code">' + number.code + '</span>').show();
            //         $('#refresh').text('换一批');
            //     }
            // }

            $('.no-number').html('抱歉没有匹配的号码.<span class="error-code">' + number.code + '</span>').show();
            $('#refresh').text('换一批');
            return;
        }
        $('.number-list').show();
        $('.number-loading').hide();
        numberParam.list = [];
        numberParam.current = 1;
        var numArray = number.data.flexData;
        // console.log('numArray.length:'+numArray.length);
        // for (var i = 0; i < numArray.length; i += 12) {
        for (var i = 0; i < numArray.length; i ++) {
            var numberObj = {};
            numberObj.advanceLimit = numArray[i + 1];
            numberObj.niceRule = numArray[i + 5];
            numberObj.monthLimit = numArray[i + 6];
            // console.log('_key:'+_key);
            if (commonCheck.isEmpty(_key)) {
                numberObj.number = numArray[i];
                numberParam.list.push(numberObj);
            } else {
                // var len = 11 - _key.length;
                // if (numArray[i].toString().substring(len) == _key) {
                //     console.log('ll:'+numArray[i].toString().substring(len));
                //     numberObj.number = numArray[i].toString().substring(0, len) + '<span>' + numArray[i].toString().substring(len, 11) + '</span>';
                //     numberParam.list.push(numberObj);
                // }
                numberObj.number = numArray[i].replace(_key, '<span>' + _key + '</span>');
                numberParam.list.push(numberObj);
            }
        }
        // console.log('numberParam.list:'+JSON.stringify(numberParam.list));
        numberParam.max = Math.ceil(numberParam.list.length / numberParam.size);
        $.shuffle(numberParam.list);
        listNumber();
    }

    // 初始化号码查询参数
    var setNumberParam = function () {
        numberParam.list = [];
        numberParam.current = 1;
        numberParam.size = 10;
        numberParam.max = 1;
    };
    // 初始化号码
    var setNumber = function (isSearch) {
        $('.number-list, .no-number').hide();
        $('.number-loading').show();
        if (req.numInfo.essCity === '190' && req.numInfo.essProvince === '18') {
            req.numInfo.essCity = '187';
        }
        // console.log('req:'+JSON.stringify(req));
        var param = {
            province: req.numInfo.essProvince,
            city: req.numInfo.essProvince == '50' ? '501' : req.numInfo.essCity,
            monthFeeLimit: 0,
            sid: product.id,
            searchCategory: 3,
            net: '01',
            amounts: 200,
            codeTypeCode: '',
            searchNumber: $('#search').data('val'),
            qryType: '02',
            goodsNet: 4,
            channel: 'msg-xsg'
        };
        if (isSearch) {
            param.searchType = '02';
        } else {
            param.searchValue = '';
        }
        if (!commonCheck.isEmpty(param.sid)) {
            $._ajaxSwitch({
                type: 'get',
                url: API_interface + '/home/api/selectPhones',
                data: param,
                dataType: 'json',
                async: true,
                // jsonp: 'callback',
                // jsonpCallback: 'jsonp_queryMoreNums',
                success: function (numberData) {
                    initNum = numberData;
                    decompress(numberData);
                },
            });
        } else {
            $('.no-number').text('抱歉没有匹配的号码').show();
            $('#refresh').text('换一批');
            $('.number-list, .number-loading').hide();
        }
    };
    // 点击错误提示消失
    $('.p-content').find('input').click(function (e) {
        var _this = $(e.currentTarget);
        var par = _this.parents('li');
        var isError = par.hasClass('error');
        if (isError) {
            par.removeClass('error');
            $('#top-desc').removeClass('error').text(_topText);
        }
    });
    $('.p-text-area').click(function () {
        if ($('#delivery-desc').hasClass('error')) {
            $('#delivery-desc').removeClass('error');
            $('#top-desc').removeClass('error').text(_topText);
        }
    });
    $('#delivery').click(function (e) {
        if ($(e.currentTarget).hasClass('error')) {
            $('#delivery').removeClass('error');
            $('#top-desc').removeClass('error').text(_topText);
        }
    });
    // 监听身份证号码输入框判定是否大于为18位
    $('#certNo').bind('input propertychange', function () {
        var val = $('#certNo').val();
        if (val.length == '18' && val.substring(0, 6) == '610403') {
            $('[data-code=84]').click();
            $('[data-code=841]').click();
            $('[data-ess-code=841]').click();
            $('[data-code=610127]').click();
        }
    });
    /****************************验证码操作开始******************************/
        // 提交前验证码参数
    var captchaInfo = {
            type: '',
            captcha: ''
        };
    var captchaUrl;
    var getcaptcha = function () {
        // 获取图片验证码
        $.ajax({
            type: 'get',
            url: 'https://msgo.10010.com/lsd-message/get/captcha/js/url',
            data: initParam,
            success: function (resp) {
                captchaUrl = resp.rspBody.captchaUrl;
            },
            error: function () {
                $('#overtime, .mask').show();
                noScroll();
            },
        });
    };
    // 按键前联系电话
    var telNumberBefore;
    $('#mobilePhone').on('keydown', function () {
        telNumberBefore = $('#mobilePhone').val();
    });
    // 格式化联系电话
    var numberTxtAfter;
    // 联系电话中转展示
    var numberTxt;
    var getCityArr = function (obj) {
        var cityArrJson = [];
        var cityArr = obj.match(new RegExp('\\{(.| )+?\\}', 'igm'));
        cityArr.forEach(function (value) {
            cityArrJson.push(JSON.parse(value));
        });
        return cityArrJson;
    };
    // 联系电话格式化
    $('#mobilePhone').on('keyup', function () {
        numberTxtAfter = $('#mobilePhone').val();
    });
    // 验证码开关
    var captchaSwitch = false;
    // 短信验证码防止并发
    var requestFlag1 = false;
    var captchaCode = '';
    var captchaVoiceOne = false;
    // 滑块验证码请求一次
    var captchaOne = false;
    // 语音验证码防止并发
    var voiceRequestFlag = false;
    // 获取用户输入验证码事件
    $('#captchaText').on('change', function (e) {
        var _this = $(e.currentTarget);
        captchaCode = _this.val();
    });
    // 判断语音验证码是否展示
    var showCaptchaVoiceFlag = function () {
        var paramData = {
            phoneNumber: numberTxtAfter,
        };
        $.ajax({
            type: 'GET',
            url: 'https://msgo.10010.com/lsd-message/qry/belong/phone/v1',
            timeout: 6000,
            data: paramData,
            success: function (resp) {
                if (resp.rspCode === '0000') {
                    $('.voiceCaptcha').show();
                    if (!captchaVoiceOne) {
                        getcaptcha();
                        captchaVoiceOne = true;
                    }
                } else {
                    $('.voiceYzmTip').hide();
                }
            },
            error: function () {
                console.log('网络出错');
            },
        });
    }
    // 获取短信验证码
    $('#captcha').on('click', function () {
        // console.log(numberTxtAfter);
        // 当电话号码为空时，跳出函数
        if (!commonCheck.CustCheck.checkPhone(numberTxtAfter) || numberTxtAfter === '') {
            return;
        }
        // 在定时器生效期间，不能再次点击发送验证码按钮
        if (requestFlag1) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: API_interface+'/home/api/getCode',
            timeout: 6000,
            data: {
                identity:$('#certNo').val().trim(),
                contact:$('#mobilePhone').val(),
                sid:product.id,
                sub_agent:initParam.sub_agent,
                job_number:initParam.job_number
            },
            success: function (data) {
                if(data.code !== 20000){
                    $('#errorAll, .mask').show();
                    noScroll();
                    $('#errorAll .popup-desc').text(data.msg);
                    return;
                }

                requestFlag1 = true;
                // 验证码倒计时方法
                var time = 60;
                // 设置定时器
                var setTime = setInterval(function () {
                    time -= 1;
                    $('#captcha').empty().text(time + 's后重新获取');
                    $('#captcha').addClass('grey');
                    requestFlag1 = true;
                    if (time === 0) {
                        $('#captcha').empty().text('获取验证码');
                        $('#captcha').removeClass('grey');
                        requestFlag1 = false;
                        clearInterval(setTime);
                        showCaptchaVoiceFlag();
                    }
                }, 1000);
                return;

                // captchaInfo.type = '00';
                // if (wangka === 1 || wangka === '1') {
                //     // 王卡助手验证码
                //     if (resp && resp.code && resp.code !== '0000') {
                //         $('#errorAll, .mask').show();
                //         $('#errorAll').find($('.popup-desc')).empty().text(resp.msg);
                //         noScroll();
                //     }
                // } else {
                //     // 码上购验证码
                //     if (resp && resp.rspCode && resp.rspCode !== '0000') {
                //         $('#errorAll, .mask').show();
                //         $('#errorAll').find($('.popup-desc')).empty().text(resp.rspDesc);
                //         noScroll();
                //     }
                // }
            },
            error: function () {
                requestFlag1 = false;
                console.log('网络出错');
            },
        });
    });
    // 发送语音验证码
    var voiceYzmCaptcha = function (tickets) {
        var voiceParam = {
            phoneNumber: numberTxtAfter,
            ticket: tickets
        };
        $.ajax({
            type: 'GET',
            url: 'https://msgo.10010.com/lsd-message/send/voice/captcha/v1',
            timeout: 6000,
            data: voiceParam,
            success: function (resp) {
                captchaInfo.type = '01';
                if (resp && resp.rspCode && resp.rspCode !== '0000') {
                    $('#errorAll, .mask').show();
                    $('#errorAll').find($('.popup-desc')).empty().text(resp.rspDesc);
                    noScroll();
                }
            },
            error: function () {
                requestFlag1 = false;
                console.log('网络出错');
            },
        });
    };

    // 验证码回调函数
    function cbfn(retJson) {
        if (retJson.ret === 0) {
            $('.mask').hide();
            voiceYzmCaptcha(retJson.ticket);
            var showTime = 3;
            var setShowTime = setInterval(function () {
                showTime -= 1;
                $('.voiceYzmTip').empty().html('我们将通过电话方式告知您验证码，请注意接听').show();
                if (showTime === 0) {
                    $('.voiceYzmTip').hide();
                    clearInterval(setShowTime);
                }
            }, 1000);
            $('#TCaptcha').hide();
            window.capDestroy();
        } else {
            // 用户关闭验证码页面，没有验证
        }
    }

    // 验证码回调函数2
    function cbfn2() {
        // if (retJson.ret == 0) {
        if (disFlag) {
            // 号码归属地跟邮寄地址不一致直接提交
            if ($('#city li.selected').data('code') != $('#post-city li.selected').data('ess-code')) {
                preSubmit(false);
                successFlag = true;
                submit();
            } else {
                $._ajaxSwitch({
                    type: 'post',
                    url: 'https://msgo.10010.com/scene-buy/selfFetch/qrySelfInfo',
                    data: reqSince,
                    success: function (resp) {
                        if (resp.selfFetchFlag == '1') { // 有自提点
                            $('#since').show();
                            if (provinceXJ == '安徽') {
                                $('.since-content').find('.title').remove();
                                $('.since-content').prepend('<h3 class="title">您填写的配送区域可到现场办理，<i>现场充值还有礼品赠送</i></h3>');
                            } else {
                                $('.since-content').find('.title').remove();
                                $('.since-content').prepend('<h3 class="title">您填写的配送区域可到现场办理：</h3>');
                            }
                            $('.mask').show();
                            noScroll();
                            var sinceUl = $('.since-content').find('ul');
                            sinceUl.find('li').remove();
                            var htmLi = '';
                            for (var i = 0; i < resp.selfFetchInfo.length; i += 1) {
                                if (i == 0) {
                                    htmLi = "<li><input type='radio' name='mall' id='radio" + i + "'  checked='checked' value='" +
                                        resp.selfFetchInfo[i].ADDRESS_ID + "' ><label for='radio" + i + "' class='em'>" +
                                        resp.selfFetchInfo[i].SELFGET_NAME + "：</label><label for='radio" + i + "' class='margin'>" + resp.selfFetchInfo[i].SELFGET_ADDRESS + "</label></li>";
                                } else {
                                    htmLi = "<li><input type='radio' name='mall' id='radio" + i + "'  value='" +
                                        resp.selfFetchInfo[i].ADDRESS_ID + "' ><label for='radio" + i + "' class='em'>" +
                                        resp.selfFetchInfo[i].SELFGET_NAME + "：</label><label for='radio" + i + "' class='margin'>" + resp.selfFetchInfo[i].SELFGET_ADDRESS + "</label></li>";
                                }
                                sinceUl.append(htmLi);
                            }
                        }
                        if (resp.selfFetchFlag == '0') { // 无自提点直接提交
                            preSubmit(false);
                            successFlag = true;
                            submit();
                        }
                    },
                    error: function () {
                        preSubmit(false);
                        successFlag = true;
                        submit();
                    },
                });
            }
        } else {
            preSubmit(false);
            successFlag = true;
            submit();
        }
        // }
    }

    // 引入验证码script
    function yzmShow(captcha, cbfnFun) {
        var head = document.getElementsByTagName('head')[0];
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = captcha;
        head.appendChild(script);
        script.onload = function () {
            var capOption = {callback: cbfnFun, showHeader: false};
            window.capInit(document.getElementById('TCaptcha'), capOption);
        };
    }

    $('#voiceCaptcha').click(function () {
        if (voiceRequestFlag) {
            var showTime = 3;
            var setShowTime = setInterval(function () {
                showTime -= 1;
                $('.voiceYzmTip').empty().html('请1分钟后再试').show();
                if (showTime === 0) {
                    $('.voiceYzmTip').hide();
                    clearInterval(setShowTime);
                }
            }, 1000);
            return;
        }
        voiceRequestFlag = true;
        // 验证码倒计时方法
        var time = 60;
        // 设置定时器
        var setTime = setInterval(function () {
            time -= 1;
            voiceRequestFlag = true;
            if (time === 0) {
                voiceRequestFlag = false;
                clearInterval(setTime);
            }
        }, 1000);
        yzmShow(captchaUrl, cbfn);
        $('.mask').show();
        $('#TCaptcha').show();
    });
    // 提交前校验
    var verify = function () {
        $('li.error').removeClass('error');
        if (!commonCheck.CustCheck.checkReceiverName($('#certName').val())) {
            return false;
        } else if (!commonCheck.CustCheck.checkIdCard($('#certNo').val())) {
            return false;
        } else if (!commonCheck.CustCheck.checkPhone(numberTxtAfter)) {
            return false;
        } else if (!commonCheck.CustCheck.checkAddress($('#delivery .p-content').text()) && disFlag) {
            return false;
        } else if (!commonCheck.CustCheck.checkAddressInfo($('#address').val()) && disFlag) {
            return false;
        } else if (!commonCheck.CustCheck.checkYzm($('#captchaText').val())) {
            return false;
        } else if (!commonCheck.CustCheck.checkNumAddress($('#location .p-content').text())) {
            return false;
        }
        //选号开关
        if(product.num_select_switch === 1){
            if (!commonCheck.CustCheck.checkNumber($('#number .p-content').text())) {
                return false;
            }
        }
        return true;
    };
    // 从缓存读取数据
    var loadDataFromCache = function () {
        var _cache = sessionStorage.getItem('MSG_CARD');
        if (!commonCheck.isEmpty(_cache)) {
            var _cacheObject = JSON.parse(_cache);
            if (commonCheck.isEmpty($('#certName').val())) {
                var _certInfo = _cacheObject.certInfo;
                $('#certName').val(_certInfo.certName);
                $('#certNo').val(_certInfo.certId);
                $('#mobilePhone').val(_certInfo.contractPhone);
            }
        }
    };

    // 现场受理省份地市初始化
    function sceneLocation(initReq) {
        var scenePro = [];
        scenePro = Area.PROVINCE_LIST.filter(function (p) {
            return p.ESS_PROVINCE_CODE == initReq.numInfo.essProvince;
        })[0];
        var provinceList = [];
        provinceList.push('<li data-code=' + scenePro.ESS_PROVINCE_CODE + ' pro-code=' + scenePro.PROVINCE_CODE + '>' + scenePro.PROVINCE_NAME + '</li>');
        $('#province').html(provinceList).find('li[data-code=' + initReq.numInfo.essProvince + ']').addClass('selected org');
        var sceneCity = [];
        sceneCity = Area.PROVINCE_MAP[scenePro.PROVINCE_CODE].filter(function (c) {
            return c.ESS_CITY_CODE == initReq.numInfo.essCity;
        })[0];
        var cityList = [];
        cityList.push('<li data-code=' + sceneCity.ESS_CITY_CODE + ' city-code=' + sceneCity.CITY_CODE + '>' + sceneCity.CITY_NAME + '</li>');
        $('#city').html(cityList).find('li[data-code=' + initReq.numInfo.essCity + ']').addClass('selected org');
        /*var defaultProvince = $('#province li[data-code=' + req.numInfo.essProvince + ']').text();
        var defaultCity = $('#city li[data-code=' + req.numInfo.essCity + ']').text();
        if(initParam.c == '713'){
          $('#location .p-content').text(defaultProvince +' 天门/仙桃/潜江市').removeClass('grey');
        }else{
          $('#location .p-content').text(defaultProvince + ' ' + defaultCity).removeClass('grey');
        }*/
    }

    // 邮寄信息初始化
    var setPost = function (initReq, data) {
        var postPro = [];
        if (data == undefined) {
            postPro = Area.PROVINCE_LIST.filter(function (p) {
                return p.ESS_PROVINCE_CODE == initReq.numInfo.essProvince;
            })[0];
        } else {
            postPro = Area.PROVINCE_LIST.filter(function (p) {
                return p.ESS_PROVINCE_CODE == data;
            })[0];
        }
        var _cityList = [];
        Area.PROVINCE_MAP[postPro.PROVINCE_CODE].forEach(function (c) {
            _cityList.push('<li data-province-name=' + postPro.PROVINCE_NAME + '  data-code=' + c.CITY_CODE + ' data-ess-code=' + c.ESS_CITY_CODE + '>' + c.CITY_NAME + '</li>');
        });
        $('#post-city').html(_cityList);
        var postCity = [];
        if (data == undefined) {
            postCity = Area.PROVINCE_MAP[postPro.PROVINCE_CODE].filter(
                function (c) {
                    return c.ESS_CITY_CODE == initReq.numInfo.essCity;
                })[0];
            $('#post-city').find('li[data-ess-code=' + initReq.numInfo.essCity + ']').addClass('selected');
        } else {
            postCity = Area.PROVINCE_MAP[postPro.PROVINCE_CODE][0];
            $('#post-city').find('li').eq(0).addClass('selected');
        }
        var _districtList = [];
        Area.CITY_MAP[postCity.CITY_CODE].forEach(function (d) {
            _districtList.push('<li data-code=' + d.DISTRICT_CODE + '>' + d.DISTRICT_NAME + '</li>');
        });
        $('#post-district').html(_districtList).find('li').eq(0).addClass('selected');
    };
    // 获取默认省份地市
    var getLocation = function (locations) {
        var postProvinceList = [];
        locations.PROVINCE_LIST.forEach(function (d) {
            postProvinceList.push('<li data-code=' + d.ESS_PROVINCE_CODE + '  pro-code=' + d.PROVINCE_CODE + '>' + d.PROVINCE_NAME + '</li>');
        });
        $('#post-province').html(postProvinceList).find('li[data-code=' + req.numInfo.essProvince + ']').addClass('selected');
        // 禁止跨地市配送
        if (isKua) {
            $('#post-province li').hide();
            $('#post-province .selected').show();
        }
    };
    var city = [];
    var province = [];

    // 省份切换
    function provinceChange(pCode, provinceCode, cityCode) {
        $('#province li, #city li').removeClass('selected org');
        $('#province').find('li[data-code=' + provinceCode + ']').addClass('selected org');
        var cityList = [];
        // console.log(pCode, city[pCode]);
        city[pCode].forEach(function (c) {
            cityList.push('<li data-code=' + c.ESS_CITY_CODE + '>' + c.CITY_NAME + '</li>');
        });
        $('#city').html(cityList);
        if (!commonCheck.isEmpty(cityCode)) {
            $('#city').find('li[data-code=' + cityCode + ']').addClass('selected org');
        }
        // 广东/湖南区域且配送地址已选择广东/湖南
        // 已选择配送省份
        // var selPostPro =  $('#post-province').find('li[pro-code=' + req.postInfo.webProvince + ']').attr('data-code');
        if (getProvinceFlag(provinceCode) != -1 && provinceCode == initParam.p) {
            // weight 权重  is_other_plat 1大王卡0其他

            var cityCode = initParam.c || $('#city').find('li.selected').attr('data-code');
            $('#city').html('<li data-code=' + cityCode + '>' + specialProvince[getProvinceFlag(provinceCode)] + '</li>')
                .find('li').eq(0).addClass('selected org');
        }
        // 海南默认选择海口地市
        if (provinceCode == '50') {
            $('#city').html('<li data-code="501">海口市</li>').find('li').eq(0).addClass('selected org');
        }
        // 浙江台州市专属
        /*if (provinceCode == '36') {
            $('#city').html('<li data-code="476">台州市</li>').find('li').eq(0).addClass('selected org');
        }*/
        // 广东省的话，修改参数
        // if (provinceCode == '51' && is_other_plat == 1) {
        //     console.log('change guangdong');
        //     req.u = initParam.u = g_u;
        //     req.p = initParam.p = g_p;
        //     req.c = initParam.c = g_c;
        //     req.plat_num = initParam.plat_num = g_num;
        // } else {
        //     console.log('change no guangdong');
        //     req.u = initParam.u = u;
        //     req.p = initParam.p = p;
        //     req.c = initParam.c = c;
        //     req.plat_num = initParam.plat_num = plat_num;
        // }
        // console.log(initParam, req);
    }

    // 省份地市初始化
    function setLocation(locations, initReq) {
        city = locations.PROVINCE_MAP;
        var provinceList = [];
        locations.PROVINCE_LIST.forEach(function (p) {
            provinceList.push("<li data-code='" + p.ESS_PROVINCE_CODE + "' pro-code=" + p.PROVINCE_CODE + ">" + p.PROVINCE_NAME + "</li>");
        });
        $('#province').html(provinceList).find('li[data-code=' + initReq.numInfo.essProvince + ']').addClass('selected');
        // 新疆、西藏特殊处理
        if (req.numInfo.essProvince == '89' || req.numInfo.essProvince == '79') {
            $('#province').find('li.selected').siblings().hide();
        } else {
            $('#province').find('li[data-code="89"],li[data-code="79"]').hide();
        }
        var proCode = $('#province').find('.selected').attr('pro-code');
        provinceChange(proCode, req.numInfo.essProvince, req.numInfo.essCity);
    }

    // 改变邮寄省份
    function provinceChangePost(provinceCode, cityCode) {
        $('#post-province').find('li[data-code=' + provinceCode + ']').addClass('selected');
        var postPro = Area.PROVINCE_LIST.filter(function (p) {
            return p.ESS_PROVINCE_CODE === '' + provinceCode;
        })[0];
        var cityList = [];
        Area.PROVINCE_MAP[postPro.PROVINCE_CODE].forEach(function (c) {
            cityList.push('<li data-code=' + c.CITY_CODE + ' data-ess-code=' + c.ESS_CITY_CODE + '>' + c.CITY_NAME + '</li>');
        });
        $('#post-city').empty();
        $('#post-city').html(cityList);
        if (!commonCheck.isEmpty(cityCode)) {
            $('#post-city').find('li[data-code=' + cityCode + ']').addClass('selected');
        }
        console.log('provinceCode_1212', provinceCode);
        // 广东订单
        if (provinceCode == '51' && is_other_plat == 1 && is_move == 1) {
            // console.log('change guangdong');
            req.u = initParam.u = g_u;
            req.p = initParam.p = g_p;
            req.c = initParam.c = g_c;
            req.plat_num = initParam.plat_num = g_num;
        } else {
            // console.log('change no guangdong');
            req.u = initParam.u = u;
            req.p = initParam.p = p;
            req.c = initParam.c = c;
            req.plat_num = initParam.plat_num = plat_num;
        }
        // console.log(initParam, req);
    }

    // 邮寄地市切换
    function cityChange(cityCode, districtCode) {
        $('#post-city li, #post-district li').removeClass('selected');
        $('#post-city').find('li[data-code=' + cityCode + ']').addClass('selected');
        var _districtList = [];
        Area.CITY_MAP[cityCode].forEach(function (d) {
            _districtList.push('<li data-code=' + d.DISTRICT_CODE + '>' + d.DISTRICT_NAME + '</li>');
        });
        $('#post-district').html(_districtList);
        if (!commonCheck.isEmpty(districtCode)) {
            $('#post-district').find('li[data-code=' + districtCode + ']').addClass('selected');
        }
    }

    var setProvince = function (data) {
        province = data;
    };

    // 页面初始化
    function init() {
        setReq();
        setProduct();
        setNumberParam();
        loadDataFromCache();
        setProvince(Area);
        getLocation(Area);
        setPost(req);
        /*var pText=$('#post-province').find('li.selected').text();
        var cText=$('#post-city').find('li.selected').text();
        var dText=$('#post-district').find('li').eq(0).text();
        $('#delivery .p-content').text(pText + ' ' + cText + ' '+ dText).removeClass('grey');*/
        req.postInfo = {};
        req.postInfo.webProvince = $('#post-province').find('li.selected').attr('pro-code');
        req.postInfo.webCity = $('#post-city').find('li.selected').attr('data-code');
        req.postInfo.webCounty = $('#post-district').find('li').eq(0).data('code');
        setLocation(Area, req);
        /*var defaultProvince = $('#province li[data-code=' + req.numInfo.essProvince + ']').text();
        var defaultCity = $('#city li[data-code=' + req.numInfo.essCity + ']').text();
        if(initParam.c == '713'){
          $('#location .p-content').text(defaultProvince +' 天门/仙桃/潜江市').removeClass('grey');
        }else{
          $('#location .p-content').text(defaultProvince + ' ' + defaultCity).removeClass('grey');
        }*/
        $('#location .p-content').removeClass('arr');
        if (!disFlag) {
            $('#location .p-content').addClass('arr');
            var defaultProvince = $('#province li[data-code=' + req.numInfo.essProvince + ']').text();
            var defaultCity = $('#city li[data-code=' + req.numInfo.essCity + ']').text();
            if (initParam.c == '713') {
                $('#location .p-content').text(defaultProvince + ' 天门/仙桃/潜江市').removeClass('grey');
            } else {
                $('#location .p-content').text(defaultProvince + ' ' + defaultCity).removeClass('grey');
            }
        }
        if (isTouch) {
            $('#location .p-content').addClass('arr');
        }
    }

    // 小程序填写页初始化
    function smallRoutineInit() {
        setReq();
        setProduct();
        setNumberParam();
        loadDataFromCache();
        setProvince(Area);
        req.postInfo = {};
        req.postInfo.webProvince = $('#post-province').find('li.selected').attr('pro-code');
        req.postInfo.webCity = $('#post-city').find('li.selected').attr('data-code');
        req.postInfo.webCounty = $('#post-district').find('li').eq(0).data('code');
        setLocation(Area, req);
        $('#location .p-content').removeClass('arr');
        getLocation(Area);
        setPost(req);
    }

    // 页面初始化请求
    var pageInit = function () {
        var param = {
            provinceCode: initParam.p
        };
        var succCallback = function (data) {
            if (data.rspCode == '0000') {
                if (data.rspBody.provinceSwitch == '1') {
                    changePflag = false;
                } else {
                    changePflag = true;
                }
                init();
            } else {
                $('#overtime,.mask').show();
            }
        };
        var errorCallback = function (error) {
            $('#overtime,.mask').show();
        };
        $._ajax('true', 'post', '/query-server/provinceswitch/queryProvinceSwitch', param, 'json', succCallback, errorCallback);
    };
    // 根据小程序的标记,判断页面初始化走哪个逻辑
    if (smallRoutineFlag) {
        smallRoutineInit();
    } else {
        init();
    }
    // 省份地市弹出层
    $('#location').on('click', function (e) {
        // 现场购端外下单、触点购禁止切换归属地
        if (!disFlag || isTouch) {
            return;
        }
        if (!commonCheck.CustCheck.checkAddress($('#delivery .p-content').text())) {
            return;
        }

        if ($(e.currentTarget).hasClass('error')) {
            $('#location').removeClass('error');
            $('#top-desc').removeClass('error').text(_topText);
        }
        $('#province .selected').removeClass('selected org');
        $('#province').find('li[data-code=' + req.numInfo.essProvince + ']').addClass('selected org');
        var proCode = $('#province').find('.selected').attr('pro-code');
        provinceChange(proCode, req.numInfo.essProvince, req.numInfo.essCity);
        if ($('#city li').hasClass('selected')) {
            $('#city .selected').addClass('org');
            $('#province .selected').addClass('org');
        }
        var _mask = $('.mask');
        _mask.show();
        noScroll();
        $('#area').addClass('slip');
        _mask.one('click', function () {
            $('#area').removeClass('slip');
            setTimeout(function () {
                _mask.hide();
                reScroll();
            }, 300);
        });
    });

    // 省份切换
    $('#province').on('click', 'li', function (e) {
        var _this = $(e.currentTarget);
        var _proCode = _this.attr('pro-code');
        var _code = _this.data('code');
        _this.addClass('selected org').siblings('li').removeClass('selected ord');
        // req.numInfo.essProvince = _this.data('code') + '';
        provinceChange(_proCode, _code);
    });
    // 地市切换
    $('#city').on('click', 'li', function (e) {
        var _currentP = $('#province li.selected');
        var _this = $(e.currentTarget);
        _this.addClass('selected org').siblings('li').removeClass('selected org');
        if (_this.data('code') == '713') {
            $('#location .p-content').text(_currentP.text() + ' 天门/仙桃/潜江市').removeClass('grey');
        } else {
            $('#location .p-content').text(_currentP.text() + ' ' + _this.text()).removeClass('grey');
        }
        req.numInfo.essProvince = _currentP.data('code') + '';
        req.numInfo.essCity = _this.data('code') + '';
        $('#area').removeClass('slip');
        $('#number .p-content').text('');
        provinceXJ = _currentP.text();
        $('#delivery-desc').show();
        setTimeout(function () {
            $('.mask').unbind('click').hide();
            reScroll();
        }, 300);
        $('.numberTips').hide();
        if ($(e.currentTarget).hasClass('error')) {
            $('#location').removeClass('error');
            $('#top-desc').removeClass('error').text(_topText);
        }
    });

    $('#go-protocol').on('click', function (e) {
        e.stopPropagation();
        // 查看协议
        var protocolParam = {
            city: req.numInfo.essCity,
            province: req.numInfo.essProvince,
            custName: $('#certName').val().trim(),
            goodsId: req.goodInfo.goodsId,
            number: req.numInfo.number,
            psptType: '02',
            psptTypeCode: $('#certNo').val().trim(),
            // activityType: '11',
            custAddress: $('#address').val(),
        };
        $.showProtocal(protocolParam, '/scene-buy/scene/protocol', verify);
    });

    $('#go_notice').click(function () {
        // console.log('this.attr("data-load"):'+$(this).attr("data-load"));
        $('#protocol-desc .protocol-desc').load($(this).attr("data-load")).css('maxHeight', '22rem');
        $('#protocol-desc .protocol-title').empty().text($(this).attr("data-title"));
        $('#protocol-desc .content>.protocol').hide();
        $('#protocol-desc,.mask').show();
        $('html, body').addClass('no-scroll');
        $('#protocol').click();
    });

    // 关闭弹出层
    $('.popup-close').on('click', function (e) {
        var _this = $(e.currentTarget);
        var closeType = _this.attr('data-type');
        $('.popup, .mask').hide();
        reScroll();
        if (closeType == '4') {
            if (window.AGXB != undefined) {
                if ($.isAndroid()) {
                    AGXB.pushIn({"url": location.origin + "/newMsg/sale/html/sale.html?type=1??showHomeButton=1"});
                } else {
                    AGXB.pushIn({"url": location.origin + "/newMsg/sale/html/sale.html?type=1??bleEnabled=1&facialEnabled=1&showHomeButton=1"});
                }
            } else {
                window.history.back(-1);
            }
        }
    });
    // 号码弹出层
    $('#number').on('click', function () {
        if (commonCheck.CustCheck.checkNumAddress($('#location .p-content').text())) {
            $('#search').data('val', '').val('');
            $('#search-btn').show();
            $('#search-close-btn').hide();
            // $('.number-loading').show();
            $('#number-popup, .mask').show();
            noScroll();
            setNumber();
        }
    });
    // 刷新号码
    $('#refresh').on('click', function () {
        // console.log('numberParam.current:'+numberParam.current);
        // console.log('numberParam.max:'+numberParam.max);
        if (numberParam.current > numberParam.max) {
            // 重新获取号码
            setNumber();
            return;
        }
        if (numberParam.current === 1 || numberParam.current < 1) {
            setNumber();
            return;
        }
        listNumber();
    });
    // 号码搜索
    $('#search-btn').on('click', function () {
        var _key = $('#search').val().trim();
        $('#search').data('val', _key);
        $('#search-btn').hide();
        $('#search-close-btn').show();
        if (!commonCheck.isEmpty(_key)) {
            setNumber(true);
        } else {
            setNumber();
        }
    });
    // 关闭搜索
    $('#search-close-btn').on('click', function () {
        $('#search').data('val', '').val('');
        $('#search-btn').show();
        $('#search-close-btn').hide();
        setNumber();
    });
    // 搜索框监控
    $('#search').on('keyup', function (e) {
        var _this = $(e.currentTarget);
        var _preKey = _this.data('val');
        if ('' + _preKey != _this.val().trim()) {
            $('#search-btn').show();
            $('#search-close-btn').hide();
        }
        if (_this.val().trim() == '') {
            _this.data('val', '');
            setNumber();
        }
    });
    // 号码预占
    var occupyNumber = function (number, rule, month) {
        $('.mask, #number-popup, .occupyTips').hide();
        reScroll();
        requestFlag = false;
        $('#number .p-content').text(number);
        req.numInfo.number = number;
        if (rule == '1' && month != '0') {
            $('.numberTips').show().find('i').text(month);
        } else {
            $('.numberTips').hide();
        }
        if ($('#top-desc').text() == '请选择号码') {
            $('#top-desc').removeClass('error').text(_topText);
        }
    };
    // 选择号码
    $('.number-list').on('click', 'a', function (e) {
        // var adLimit = parseInt($(this).attr('data-advancelimit')) || 0;
        // if (adLimit > 0) {
        //     popupShow('对不起，您选择的号码已被预定，<br />请重新选择号码!');
        //     return;
        // }
        var _number = $(e.currentTarget).text().replace('靓', '');
        var niceRule = $(e.currentTarget).attr('data-niceRule');
        var monthLimit = $(e.currentTarget).attr('data-monthLimit');
        if (!requestFlag) {
            occupyNumber(_number, niceRule, monthLimit);
        }
    });
    // 号码被预占,重新选择号码
    $('#reselect-number').on('click', function () {
        $('#search-btn').show();
        $('#search-close-btn').hide();
        setNumber();
        $('#error').hide();
        $('#number-popup').show();
    });
    // 配送地市弹出层
    $('#delivery').on('click', function () {
        if (!captchaOne) {
            // getcaptcha();
            captchaOne = true;
        }
        var _mask = $('.mask');
        _mask.show();
        noScroll();
        $('#post').addClass('slip');
        _mask.one('click', function () {
            $('#post').removeClass('slip');
            setTimeout(function () {
                _mask.hide();
                reScroll();
            }, 300);
        });
        if (isTouch) {
            $('#post-city').find('li').hide();
            $('#post-city').find('.selected').show()
        }
    });
    // 邮寄省份切换
    $('#post-province').on('click', 'li', function (e) {
        var _this = $(e.currentTarget);
        var _code = _this.data('code');
        _this.addClass('selected').siblings('li').removeClass('selected');
        provinceChangePost(_code);
        setPost(req, _code);
    });
    // 邮寄地市切换
    $('#post-city').on('click', 'li', function (e) {
        var _this = $(e.currentTarget);
        var _code = _this.data('code');
        _this.addClass('selected').siblings('li').removeClass('selected');
        cityChange(_code);
    });
    // 邮寄区县切换
    $('#post-district').on('click', 'li', function (e) {
        var _currentP = $('#post-province li.selected');
        var _currentC = $('#post-city li.selected');
        var _this = $(e.currentTarget);
        if (_currentC.length != 0) {
            _this.addClass('selected').siblings('li').removeClass('selected');
            $('#delivery .p-content').text(_currentP.text() + ' ' + _currentC.text() + ' ' + _this.text()).removeClass('grey');
            req.postInfo.webProvince = _currentP.attr('pro-code');
            req.postInfo.webCity = _currentC.data('code') + '';
            req.postInfo.webCounty = _this.data('code') + '';
            $('#post').removeClass('slip');
            if ($('#post-district li.selected').length != 0) {
                $('#delivery').removeClass('error');
                $('#top-desc').text(_topText).removeClass('error');
            }
            setTimeout(function () {
                $('.mask').unbind('click').hide();
                reScroll();
            }, 300);
        }
        // 如更换收货地址、号码信息已选时，已选号码不清空
        if ($('#number .p-content').text() == '' || '89,79'.indexOf(req.numInfo.essProvince) > -1 || '89,79'.indexOf(_currentP.attr('data-code')) > -1) {
            req.numInfo.essProvince = _currentP.attr('data-code');
            req.numInfo.essCity = _currentC.attr('data-ess-code');
            if (getProvinceFlag(req.numInfo.essProvince) != -1 && req.numInfo.essProvince == initParam.p) {
                req.numInfo.essCity = initParam.c;
            }
            setLocation(Area, req);
            var numProvince = $('#province li.selected');
            var numCity = $('#city li.selected');
            if (numCity.data('code') == '713') {
                $('#location .p-content').text(numProvince.text() + ' 天门/仙桃/潜江市').removeClass('grey');
            } else {
                $('#location .p-content').text(numProvince.text() + ' ' + numCity.text()).removeClass('grey');
            }
            $('#number .p-content').text('');
            provinceXJ = numProvince.text();
        }
    });
    // 同意入网协议
    $('#protocol').on('click', function (e) {
        var _this = $(e.currentTarget);
        if (_this.hasClass('agree')) {
            $('#submit').addClass('disable');
            _this.removeClass('agree');
        } else {
            $('#submit').removeClass('disable');
            _this.addClass('agree');
        }
    });
    // 自提点参数
    var reqSince = {};
    // 自提点弹框
    var getSince = function () {
        reqSince.provinceCode = req.postInfo.webProvince;
        reqSince.cityCode = req.postInfo.webCity;
        reqSince.countyCode = req.postInfo.webCounty;
        reqSince.goodsId = req.goodInfo.goodsId;
    };
    // 提交参数准备
    var preSubmit = function (state) {
        if (req.numInfo.essCity === '190' && req.numInfo.essProvince === '18') {
            req.numInfo.essCity = '187';
        }
        req.numInfo.essCity = req.numInfo.essProvince == '50' ? '501' : req.numInfo.essCity;
        if (state) {
            req.postInfo.selfFetchCode = $('.since-content').find('input:checked').val();
        }
        req.template = 'default';
        req.job_number = initParam.job_number;
        req.sub_agent = initParam.sub_agent;
        req.sid = product.id;
        //订单来源
        if (initParam.sale_channel) {
            req.sale_channel = initParam.sale_channel;
        }
        if (initParam.source) {
            req.source = initParam.source;
        }
        req.certInfo = {};
        req.certInfo.certTypeCode = '02';
        req.certInfo.certName = $('#certName').val().trim();
        req.certInfo.certId = $('#certNo').val().trim();
        req.certInfo.contractPhone = numberTxtAfter;
        req.postInfo.address = $('#address').val().trim();
        // 验证码参数
        captchaInfo.captcha = $('#captchaText').val().trim();
        req.captchaInfo = captchaInfo;
        // 触点购判断
        if (initParam.ti != undefined) {
            req.touchId = initParam.ti;
        } else {
            req.u = initParam.u;
        }
        if (initParam.channel) {
            req.channel = initParam.channel;
        }
        var _cache = {};
        _cache.certInfo = req.certInfo;
        sessionStorage.setItem('MSG_CARD', JSON.stringify(_cache));
        if (initParam.isMainSubFlag == '1') {
            req.familyCardInfo = {};
            req.familyCardInfo.mainFlag = '0';
            req.familyCardInfo.familyCardNumber = mainCardInfo.mainCardNum;
        }
        if ($('.seMarkSel').hasClass('seMarkChecked')) {
            req.marketingStatus = '1';
            req.referrerPhone = $('.seMarkInput input').val();
            if (custCenterFlag == '1') {
                req.orderFrom = '01';
            } else {
                req.orderFrom = '02';
            }
        } else {
            req.marketingStatus = '0';
            if (custCenterFlag == '1') {
                req.orderFrom = '01';
            }
        }
    };
    // 跳转照片认证
    var photoFun = function (orderId) {
        var param = {
            orderId: orderId,
            provinceCode: initParam.p
        };
        var succCallback = function (data) {
            // console.log('dataLLL'+JSON.stringify(data));
            if (data.rspCode == '0000') {
                if (data.rspBody != '') {
                    $('#successClose').css('display', 'none');
                    setTimeout(function () {
                        // console.log('5666');
                        // window.location.href = data.rspBody;
                    }, 5000);
                }
            }
        };
        var errorCallback = function (error) {
            $('#overtime,.mask').show();
        };
        $._ajax('true', 'post', '/order-server/qry/photoLink', param, 'json', succCallback, errorCallback);
    };

    // 提交请求
    function submit() {
        $('#TCaptcha,.mask').hide();
        // $('.subLoad').show();
        var loading = layer.msg("正在提交订单，请稍候..",{
            icon:16,
            time:-1
        })
        var succHtml;
        if(commonCheckFill.isEmpty($.trim(req.certInfo.contractPhone))){
            req.certInfo.contractPhone = $('#mobilePhone').val();
        }
        reqData = JSON.stringify(req);

        $.ajax({
            type: 'POST',
            url: API_interface+'/home/api/uniform',
            data: reqData,
            contentType: 'application/json',
            dataType: 'json',
            async: true,
            success: function(data) {
                // console.log('data:'+JSON.stringify(data));
                layer.close(loading);
                $('.subLoad').hide();
                $('#since').hide();
                subStatus = true;
                requestFlag = false;
                if(commonCheck.isEmpty(data.msg)){
                    $('#overtime, .mask').show();
                    noScroll();
                    return;
                }
                if(data.code !== 20000){
                    $('#errorAll, .mask').show();
                    noScroll();
                    $('#errorAll .popup-desc').text(data.msg);
                    return;
                }
                $('#success, .mask').show();
                tendenceId = data.order_id;
                noScroll();
                succHtml = '<p class="point">我们将尽快为您配送，请在收到卡后的10天内激活使用，过期将被回收哦！';
                $('#success .point-list').empty().append(succHtml);
                return;

                if (data.rspCode == '0000') {
                    $('#success, .mask').show();
                    tendenceId = data.orderId;
                    noScroll();
                    if (disFlag) {
                        if (successFlag) {
                            succHtml = '<p class="point">我们将尽快为您配送，请在收到卡后的10天内激活使用，过期将被回收哦！';
                            $('#success .point-list').empty().append(succHtml);
                        } else {
                            succHtml = '<p class="point">您的卡已在营业厅等待领取，营业员将会与您电话联系，请保持手机畅通！';
                            $('#success .point-list').empty().append(succHtml);
                        }
                    } else {
                        if (successFlag) {
                            succHtml = '<p class="point">请在现场工作人员的指导下完成激活，否则无法使用哦！如您不在现场，我们会有专人联系您，请保持电话畅通！';
                            $('#success .point-list').empty().append(succHtml);
                        } else {
                            succHtml = '<p class="point">您的卡已在营业厅等待领取，营业员将会与您电话联系，请保持手机畅通！';
                            $('#success .point-list').empty().append(succHtml);
                        }
                    }
                    if (!isTouch && disFlag && false) {
                        photoFun(tendenceId);
                    }
                } else if (data.rspCode == '0005') {
                    $('#error, .mask').show();
                    noScroll();
                    $('#reserved-number').html('<span>' + $('#number .p-content').text() + '</span>号码已被抢占。');
                } else if (data.rspCode == '0009') {
                    $.noScroll();
                    $('#errorAll,.mask').show();
                    if (data.rspDesc.length > 21) {
                        $('#errorAll .popup-desc').html('该营业厅未绑定发展人，不具备推广权限<br>暂无法下单！');
                    } else {
                        $('#errorAll .popup-desc').html('该营业厅不具备此产品的推广权限<br>暂无法下单！');
                    }
                } else {
                    $('#errorAll, .mask').show();
                    noScroll();
                    $('#errorAll .popup-desc').text(data.rspDesc);
                }
                // $('#since').hide();
                // subStatus = true;
                // requestFlag = false;
            },
            error: function(res) {
                layer.close(loading);
                $('.subLoad').hide();
                $('#overtime, .mask').show();
                noScroll();
                $('#since').hide();
                subStatus = true;
                requestFlag = false;
            }
        })
    }
    // 提交
    $('#submit').on('click', function () {
        if ($('.seMarkSel').hasClass('seMarkChecked')) {
            if (!checkSeMarkInput()) {
                $('.seMarkInput').removeClass('seMarkInputS').addClass('seMarkInputE');
                return;
            } else {
                if ($('.seMarkInput input').val() != '') {
                    $('.seMarkInput').removeClass('seMarkInputE').addClass('seMarkInputS');
                }
            }
        }
        preSubmit(false);
        if (requestFlag) {
            return;
        }
        if (!$('#protocol').hasClass('agree')) {
            return;
        }
        if (verify()) {
            getSince();
            requestFlag = true;
            $('#top-desc').text(_topText).removeClass('error');
            $('#TCaptcha, .mask').show();
            $.noScroll();

            preSubmit(false);
            successFlag = true;
            submit();
            // cbfn2();
        }


    });

    // 营业厅自提
    $('.sinceBtn').on('click', function () {
        if (subStatus) {
            preSubmit(true);
            successFlag = false;
            submit();
        }
    });
    // 不自提，物流配送
    $('.noSince').on('click', function () {
        if (subStatus) {
            preSubmit(false);
            successFlag = true;
            submit();
        }
    });
    // 自适应
    $(window).resize(function () {
        $.resize();
    });
    // 地址输入
    $('#address').on({
        keydown: function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
            }
        },
        input: function (e) {
            var _this = $(e.currentTarget);
            var _address = _this.val();
            var _height = $('#address-temp').text(_address).height();
            _this.css('height', _height);
        },
    });

    // 根据share_openid判断是否有"填写推荐人"框
    if (initParam.share_id == '' || initParam.share_id == undefined) {
        $('.referee').show();
        // 提交推荐人状态
        var refereeStatus = true;
        // 推荐信息填写
        $('.to-referee').click(function () {
            $('.to-referee').hide();
            $('.referee-message').show();
            $('.submit-referee').show();
        });

        // 校验推荐人信息
        var refereeInfo = function (info) {
            if (info.length > 25) {
                return false;
            } else if (info.length == 0) {
                return false;
            }
            return true;
        };
        $('.referee-message').keyup(function () {
            if ($('.referee-message').val().trim() == '') {
                $('.submit-referee').addClass('grey');
            } else {
                $('.submit-referee').removeClass('grey');
            }
        });

        $('.submit-referee').click(function () {
            var info = $('.referee-message').val().trim();
            if (!refereeInfo(info)) {
                return;
            }
            if (refereeStatus) {
                refereeStatus = false;
                $._ajaxSwitch({
                    url: 'https://msgo.10010.com/king/kingCard/referrerInfo',
                    type: 'POST',
                    data: {
                        kingOrderId: tendenceId,
                        referrerInfo: info,
                        type: '2',
                    },
                    success: function (data) {
                        if (data.resultCode == '0000') {
                            $('.referee-message').hide();
                            $('.submit-referee').hide();
                            $('.referee-error').hide();
                            $('.referee-success').show();
                            refereeStatus = true;
                        } else {
                            $('.referee-error').show();
                            refereeStatus = true;
                        }
                    },
                    error: function () {
                        $('.referee-error').show();
                        refereeStatus = true;
                    },
                });
            }
        });
    } else {
        $('.referee').hide();
    }
    /*----二次营销开始----*/
    // 客户中心入口订单默认勾选二次营销
    if (custCenterFlag == '1') {
        $('.seMarkSel').addClass('seMarkChecked');
        $('.seMarkInput').show();
    }
    $('.seMarkSel').click(function () {
        // 客户中心入口订单禁止取消二次营销
        // if (custCenterFlag == '1') {
        //   return;
        // }
        if ($(this).hasClass('seMarkChecked')) {
            $(this).removeClass('seMarkChecked');
            $('.seMarkInput').hide();
        } else {
            $(this).addClass('seMarkChecked');
            $('.seMarkInput').show();
        }
    });
    $('.seMarkInput input').on('input', function () {
        $('.seMarkInput').removeClass('seMarkInputS seMarkInputE');
    });

    function checkSeMarkInput() {
        var s = true;
        var val = $('.seMarkInput input').val();
        if ($.trim(val) != '' && !$.checkMobiles(val)) {
            popupShow('推荐人手机号码格式有误，请重新输入！');
            s = false;
        }
        return s;
    }

    /*----二次营销结束----*/

    //  特殊省份判断
    function getProvinceFlag(province) {
        for (var item in specialProvince) {
            if (item == province) {
                return item;
            }
        }
        return -1;
    }
});