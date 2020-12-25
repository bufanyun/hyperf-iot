var commonCheckFill = {
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
    checkZf:function (text) {
        var flags = false;
        var spaceWord = /^[\sA-Za-z0-9\u4e00-\u9fa5()（）,，_—-]+$/;
        flags = spaceWord.test(text);
        return flags;
    },
    // 错误展示
    error: function (id, msg) {
        $('#top-desc').addClass('error').text(msg);
        $('#' + id).addClass('error');
        $(window).scrollTop(0);
    },
    // 错误展示
    viceError: function (errClass, msg) {
        $('.viceErrorDesc').addClass('error').html(msg);
        $(errClass).parent().addClass('error');
    },
    // 是否为空
    isEmpty: function (s) {
        return typeof s == 'undefined' || s == null || s == '';
    },
    // 电话号码格式校验
    checkMobiles: function (number) {
      return (/^((13|15|18|14|17|16|19)+\d{9})$/.test(number));
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
    // 校验函数
    CustCheck: {
        // 机主姓名验证
        checkReceiverName: function (receiverName) {
            if ($.trim(receiverName) === '') {
                commonCheckFill.error('apply-name', '请填写姓名');
                return false;
            }
            if ($.trim(receiverName).length > 20) {
                commonCheckFill.error('apply-name', '姓名过长，请您最多输入20个汉字');
                return false;
            }
            if (commonCheckFill.chineseLen($.trim(receiverName)) < 1) {
                commonCheckFill.error('apply-name', '姓名必须至少包含1个汉字');
                return false;
            }
            if (commonCheckFill.checkScript(receiverName)) {
                commonCheckFill.error('apply-name', '姓名包含非法字符');
                return false;
            }
            return true;
        },
        // 收货人姓名验证
        checkName: function (receiverName) {
            if ($.trim(receiverName) === '') {
                commonCheckFill.error('apply-name-receiver', '请填写收货人姓名');
                return false;
            }
            if ($.trim(receiverName).length > 20) {
                commonCheckFill.error('apply-name-receiver', '收货人姓名过长，请您最多输入20个汉字');
                return false;
            }
            if (commonCheckFill.chineseLen($.trim(receiverName)) < 1) {
                commonCheckFill.error('apply-name-receiver', '姓名必须至少包含1个汉字');
                return false;
            }
            if (commonCheckFill.checkScript(receiverName)) {
                commonCheckFill.error('apply-name-receiver', '姓名包含非法字符');
                return false;
            }
            return true;
        },
        // 证件号码校验
        checkIdCard: function (id) {
            // 身份证
            if (commonCheckFill.checkScript(id)) {
                commonCheckFill.error('apply-id', '证件号码包含非法字符');
                return false;
            } else if (!$.idCheck(id)) {
                commonCheckFill.error('apply-id', '请输入正确的身份证号');
                return false;
            } else if ((/^(81|82|83)/igm).test(id)) {
              commonCheckFill.error('apply-id', '此证件为港澳台居民居住证，暂不能在网络渠道办理业务，请用户到营业厅办理。');
              return false;
            }
            return true;
        },
        // 根据身份证判断未满16周岁
        checkAge: function (id) {
            var strBirthday = id.substr(6, 4) + "/" + id.substr(10, 2) + "/" + id.substr(12, 2);
            var birthDate = new Date(strBirthday);
            var nowDateTime = new Date();
            var age = nowDateTime.getFullYear() - birthDate.getFullYear();
            if (nowDateTime.getMonth() < birthDate.getMonth() || nowDateTime.getMonth() == birthDate.getMonth() && nowDateTime.getDate() < birthDate.getDate()) {
                age--;
            } else {
                age++;
            }
            if (age < 16) {
                commonCheckFill.error('apply-id', '抱歉，根据国家相关规定未满16周岁无法办理此业务');
                return false;
            }
            return true;
        },
        // 根据省份判断限制年龄
        checkAgeLimit:function (id, _provinceCode) {
            var flag = true;
            var myDate = new Date();
            var month = myDate.getMonth() + 1;
            var day = myDate.getDate();
            var age = myDate.getFullYear() - id.substring(6, 10) - 1;
            if (id.substring(10, 12) < month || id.substring(10, 12) == month && id.substring(12, 14) <= day) {
                age++;
            }
            $._ajaxSwitch({
                type: "POST",
                dataType: "json",
                async: false,
                url: "/king/state/queryYear",
                success: function success(data) {
                    if (data.code === '0000') {
                        for (var key in data.result) {
                            var year = data.result[key];
                            if (_provinceCode === key && year > age) {
                                commonCheckFill.error('apply-id', '抱歉，根据国家相关规定您的年龄无法办理此业务。');
                                flag = false;
                                return flag;
                            } else if (_provinceCode != key) {
                                flag = false;
                            }
                            //判断3
                            //如果需要判断的省分_provinceCode  等于当前json里的某一个省分并且这个人的年龄大于限制年龄，直接判断通过，返回true
                            else if (_provinceCode === key && year <= age) {
                                    flag = true;
                                    return true;
                                }
                        }
                        if (!flag) {
                            return true;
                        }
                    } else {
                        flag = true;
                        return flag;
                    }
                },
                error: function error() {
                    return true;
                }
            });
            return flag;
        },
        checkPhone: function (number) {
            number = $('#mobilePhone').val();
            if ($.trim(number) === '') {
                commonCheckFill.error('apply-phone', '请填写联系电话');
                return false;
            } else if (!commonCheckFill.checkMobiles(number)) {
                commonCheckFill.error('apply-phone', '您的手机号码格式有误，请重新输入');
                return false;
            }
            return true;
        },
        checkReceiverPhone: function (number) {
            if ($.trim(number) === '') {
                commonCheckFill.error('apply-phone-receiver', '请填写收货人联系电话');
                return false;
            } else if (!commonCheckFill.checkMobiles(number)) {
                commonCheckFill.error('apply-phone-receiver', '您的手机号码格式有误，请重新输入');
                return false;
            }
            return true;
        },
        checkNumber: function (number) {
            if ($.trim(number) === '') {
                commonCheckFill.error('number', '请选择号码');
                return false;
            } else if ($.trim(number) == $('#mobilePhone').val()) {
                commonCheckFill.error('number', '联系电话不能与开户号码相同！');
                return false;
          }
            return true;
        },
        // 选择首月资费
        checkFirstMonth: function (number) {
            if (commonCheckFill.isEmpty($.trim(number))) {
                commonCheckFill.error('firstMonth', '请选择首月资费');
                return false;
            }
            return true;
        },
        // 选择优惠活动
        checkActive: function (number) {
            if ($.trim(number) === '') {
                commonCheckFill.error('active', '请选择优惠活动');
                return false;
            }
            return true;
        },
        // 选择号码归属地
        checkNumAddress: function (address) {
            if ($.trim(address) === '请选择号码归属地') {
                commonCheckFill.error('location', '请选择号码归属地');
                return false;
            }
            return true;
        },
        checkAddress: function (address) {
            if ($.trim(address) === '请选择区/县') {
                commonCheckFill.error('delivery', '请选择您的配送区县');
                return false;
            }
            return true;
        },
        checkAddressInfo: function (address) {
            if ($.trim(address) === '') {
                commonCheckFill.error('delivery-desc', '请填写详细地址');
                return false;
            } else if (commonCheckFill.chineseLen(address) > 50) {
                commonCheckFill.error('delivery-desc', '详细地址过长，请您最多输入50个汉字');
                return false;
            } else if (commonCheckFill.chineseLen(address) < 4) {
                commonCheckFill.error('delivery-desc', '详细地址太短，请您最少输入4个汉字');
                return false;
            } else if (!commonCheckFill.checkZf(address)) {
                commonCheckFill.error('delivery-desc', '仅支持输入汉字、数字、字母、—、_、（）、空格、逗号');
                return false;
            }
            return true;
        },
        // 验证码校验
        checkYzm: function (yzm) {
            if ($.trim(yzm) === '' && product.captcha_switch === 1) {
                commonCheckFill.error('apply-yzm', '请填写验证码');
                return false;
            }
            return true;
        },
        // 提交时验证码-号码校验
        checkYzmPhone: function (data) {
            if (data !== $('#mobilePhone').val().trim()) {
                commonCheckFill.error('apply-yzm', '验证码错误');
                $('.rightI').hide();
                return false;
            }
            return true;
        },
    },
    // 校验副卡函数
    CustViceCheck: {
        // 副卡机主姓名验证
        checkReceiverName: function (errClass, receiverName) {
            if ($.trim(receiverName) === '') {
                commonCheckFill.viceError(errClass, '请填写姓名');
                return false;
            }
            if ($.trim(receiverName).length > 20) {
                commonCheckFill.viceError(errClass, '姓名过长，请您最多输入20个汉字');
                return false;
            }
            if (commonCheckFill.chineseLen($.trim(receiverName)) < 1) {
                commonCheckFill.viceError(errClass, '姓名必须至少包含1个汉字');
                return false;
            }
            if (commonCheckFill.checkScript(receiverName)) {
                commonCheckFill.viceError(errClass, '姓名包含非法字符');
                return false;
            }
            return true;
        },
        // 副卡证件号码校验
        checkIdCard: function (errClass, id) {
            // 身份证
            if (commonCheckFill.checkScript(id)) {
                commonCheckFill.viceError(errClass, '证件号码包含非法字符');
                return false;
            } else if (!$.idCheck(id)) {
                commonCheckFill.viceError(errClass, '请输入正确的身份证号');
                return false;
            }
            return true;
        },
        // 选择号码
        checkNum: function (errClass, address) {
            if ($.trim(address) === '') {
                commonCheckFill.viceError(errClass, '请选择号码');
                return false;
            }
            return true;
        },
    },
};