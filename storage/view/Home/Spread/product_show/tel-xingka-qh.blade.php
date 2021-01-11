
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>电信星卡，星卡19元日租版免费申请，全国包邮，电信星卡助手免费领取19元星卡</title>
    <link rel="stylesheet" href="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/reset.min.css" />
    <link rel="stylesheet" href="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/index.min.css" />
    <link rel="stylesheet" href="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/style.css" />
    <link rel="stylesheet" href="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/jquery-weui.min.css" />

</head>
<body>
<div id="app" style="background-color:#07091f" >
    <mt-popup v-model="dialogVisible" style="display: none;" position="center" modal="true" >
        <div data-v-023b33fe="" data-v-2cdd6ec8="" class="select-phone">
            <div data-v-023b33fe="" class="select-inner"><p data-v-023b33fe="">请选择</p>
                <div data-v-023b33fe="" class="box-search">
                    <input data-v-023b33fe="" maxlength="4" v-model="ruleForm.searchValue" pattern="\d*" placeholder="生日、幸运数字等(4位)" type="text" class="input-search">
                    <img @click="selectPhone" data-v-023b33fe="" alt="" src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/icon-search.png"></div>
                <ul data-v-023b33fe="" class="box-number">
                    <div style="padding: 2rem;text-align: center;color: #333;font-size: 0.7rem" v-if="noDataMsg">哎呀，这地儿号码不够了，换个大城市归属地,靓号更多哦!</div>
                    <li v-for="item in lianghao" @click="confirmPhone(item.phoneNumber, item.phoneid)"  data-v-023b33fe="" :class="item.lh?'is-good-num':''" v-html="formatNum(item)">
                    </li>
                </ul> <div data-v-023b33fe="" class="select-btn" @click="selectPhone">换一批</div></div></div>
    </mt-popup>
    <div class="img1">
        <img src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/gif.gif" alt="" />
        <img src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/bj.jpg?v=1" alt="" />
    </div>
    <div class="formBox" style="display: block;">
        <form name="form1" id="form1" action="order_submit.php?code=lcfx" >
            <ul>
                <li class="bm-form-content um-arrow">
                    <span>归属地</span>
                    <i class="province-warning">选择归属地</i>
                    <input type="text" id="gsd" placeholder="请选择省份/地市" readonly="readonly" v-model="ruleForm.gsd">
                </li>
                <li class="bm-form-content um-arrow">
                    <span style="color:red;font-weight: 500">免费选号</span>
                    <i class="phoneNum-warning">请选择电话号码</i>
                    <input name="inpPhone" v-model="ruleForm.phoneNum" class="inpPhone" @click="selectPhone" type="text" placeholder="亲，快挑一个靓号吧，免费的哦" readonly />
                    <input name="inpPhoneId" v-model="ruleForm.phoneNumId" type="hidden" />
                </li>

                <li>
                    <span>姓名【已加密】</span>
                    <i class="name-warning">请填写办理人真实姓名</i>
                    <input name="inpName" v-model="ruleForm.name" class="inpName" @blur="nameId()" type="text"  placeholder="请填写办理人真实姓名" />
                </li>
                <li v-show="ruleForm.name.length">
                    <span>身份证号码【已加密】</span>
                    <i class="identity-warning">请输入真实身份证号码</i>
                    <i class="identity-warning1">年龄必须在16,60周岁之间才能下单</i>
                    <input v-model="ruleForm.identity" name="inpIdentity" class="inpIdentity" @blur="identityId()" type="text" placeholder="请输入真实身份证号码，未满16岁请勿下单" />
                </li>
                <li>
                    <span>联系电话【已加密,请保持手机畅通 】</span>
                    <i class="phone-warning">请输入正确的收件人手机号码</i>
                    <input name="inpPhone" v-model="ruleForm.phone" class="inpPhone" @blur="phoneId()" type="text"
                           placeholder="请准确输入收件人手机号码，并保持畅通" />
                </li>
                <li class="um-arrow">
                    <span>收货省市</span>
                    <i class="province-warning">请选择收件的省市区</i>
                    <input type="text" id="txtCity" placeholder="请选择省份/地市/区县" readonly="readonly" v-model="ruleForm.province">
                </li>
                <li>
                    <span>详细地址</span>
                    <i class="address-warning">请输入详细准确的地址，字数不少于6字(例如:**街道...)，且不能包含特殊符号(例如:？！/ ()等，可以输入小写-号)</i>
                    <div style="display: flex;flex-direction:row;width:100%;">
                        <input name="inpAddress" v-model="ruleForm.shippingAddress" class="inpAddress" @blur="addressId()" type="text" placeholder="请输入详细地址（*街道*门牌号*小区）" />
                    </div>
                </li>

            </ul>
            <button type="button" id="submit" @click="save()">0元领取，包邮到家</button>
        </form>
    </div>
    <div class="img2">
        <img src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/zifei2.jpg?v=3" alt="" />
        <img src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/zifei.jpg" alt="" />
    </div>
    <br>
    <div class="lk_index_box">
        <div class="text">
            <span class="text_a">温馨提示</span>
            <span class="text_b"><img class="text_b_img" src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/xia.png" alt=""></span>
        </div>
        <div class="wenzi" id="aa" style="display: none;">
                <span class="wenzi_a">
				一、温馨提示<BR>
1、首月体验金特权：星卡（19元）套餐当月可获得20元话费，订购立即到账，激活即可使用，有效期至激活当月月底，不可结转次月，不退返现金，不可抵扣国际业务、港澳台业务通信费和SP/CP等代收费用。<BR>
2、星卡（19元）套餐，定向流量当月累计达到30GB后当月可继续使用，按套外1元1G/日（不足1GB按1元收费），当日有效，自动续订，不使用不收费。当月套餐外流量费用达到600元暂停手机上网服务，次月初自动恢复，如果您当月申请继续使用，则继续提供服务，按照套外资费收费。当月套餐外流量费用再次达到600元时，同上述规则处理。套餐外流量费用包含按套餐外资费收取的上网流量费，不含您订购的定向、闲时等各种流量包功能费。<BR>
3、首月月费计费方式：首月执行过渡期资费，订购当月套餐月基本费按日计扣（激活当日到月底），费用四舍五入到分。套餐内包含的国内通用流量首月按天折算，向上取整。<BR>
4、国内语音及国内流量仅限大陆地区使用，不包含港澳台。<BR>
5、除套餐包含内容外，可自愿加装套外可选收费加装包，加装后月基本费增加相应费用（具体以当地省内为准）。<BR>
6、套餐迁转规则：本套餐可迁转其他4G套餐，其他4G套餐也迁转至本套餐，次月生效。（注：具体迁转规则以当地为准）。<BR>
7、免流量范围查询：咨询10000。<BR>
8、未满16周岁的用户不能在网络渠道办理入网，请勿将已登记您本人身份证信息的电话卡随意提供给他人使用，以免被诈骗等违法犯罪人员利用。<BR>
9、一个身份证号同另一个卡品套餐仅限办理一张，如名下超出5张号卡，在任何渠道都不能办理激活。<BR>
10、本套餐仅支持二代居民身份证办理入网，军人、保密单位等在国政通系统中无数据用户，无法办理入网，敬请谅解。<BR>
11、请您在收到货后及时进行激活操作，激活有效期为发货后20天内，逾期未激活号码将自动销户。<BR>
12、线下配送：除新疆用户外，其他地区用户物流配送号卡时，用户需提供身份证原件，快递员现场激活号卡（由专员持激活设备上门为您面对面激活，您需要提供身份证原件及本人在场即可）。<BR>
13、星卡（19 元）用户可订购亲情网专属优惠，基本功能费10元/月亲情网主卡功能费包月基本费减免优惠，订购立即生效，连续享受  24  个月，到期自动退订。具体亲情网规则以办理页面展示为准。<BR>
14、定向免流服务为中国电信联合合作方提供，若合作方单方面终止合作，具体的定向应用以页面为准。<BR>
<BR>

二、免流范围<BR>
百度系定向流量包：<BR>
应用包含：手机百度、百度贴吧、百度地图、百度网盘（仅限超级会员使用）、百度手机助手、百度百科、秒懂百科、百度知道、百度文库、百度输入法、地图淘金、百度糯米、千千音乐、百度翻译、百度新闻、百度浏览器、好看视频（其中手机百度、百度网盘、百度手机助手、百度新闻、百度浏览器、好看视频享受免流服务前需先至对应APP进行绑定激活）。<BR>
网易系定向流量包：<BR>
网易系非游戏应用：网易云音乐、网易新闻、网易公开课、网易云阅读、网易云课堂、易信、网易蜗牛阅读、网易100分<BR>
网易系游戏应用包含：梦幻西游、大话西游、阴阳师、荒野行动、终结者2、倩女幽魂、率土之滨、镇魔曲、三国如龙传、坦克连、元气战姬学院、迷雾世界、大航海之路、汉王纷争、决战平安京、我的世界、天下、光明大陆、玩具大乱斗、格罗娅传奇<BR>
头条系定向流量包：<BR>
应用包含：今日头条、今日头条lite版、西瓜视频、火山小视频、抖音短视频、皮皮虾、懂车帝、半次元<BR>
其他应用：<BR>
快手、优酷、腾讯、爱奇艺、易信<BR>
                </span>
        </div>
    </div>
    <div style="text-align: center"><a href="#form1" class="endButton"> 0元领取，包邮到家</a></div>



    <div class="window2" style="z-index: 2;">
        <div>
            <p>提示</p>
            <span>你好！请阅读并同意本产品下单规则才能成功提交！</span>
            <a @click="confirm1">确定</a>
        </div>
    </div>
    <div class="alertTip" style="z-index: 2;">
        <div class="wrap">
            <p>提示</p>
            <span id="tipContent_span"></span>
            <a @click="confirm" class="clearfix">确定</a>
        </div>
    </div>
    <div class="loading">
        <p>提交中...</p>
    </div>
</div>
<div class="mask"></div>
<section id="areaGsd" class="city" style="width: 10.875rem">
    <ul id="gsd-province" class="first-list" style="padding: 0 2.25rem 0 .625rem"></ul>
    <ul id="gsd-city" class="second-list" style="padding-right: 20px"></ul>
</section>
<section id="area" class="city">
    <ul id="post-province" class="first-list"></ul>
    <ul id="post-city" class="second-list"></ul>
    <ul id="post-district" class="third-list"></ul>
</section>
<div style="visibility:hidden;">
</div>
</body>

<script src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/vue.min.js"></script>
<script src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/index.js"></script>
<script src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/jquery-1.11.1.min.js"></script>
<script src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/jquery-weui.min.js"></script>
<script src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/dgliantongunicom.min.js"></script>
<script src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/dgliantongunicom2.min.js"></script>
<!--生产环境-->
<script src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/ajax.min.js"></script>
<script src="{{env('CDN_DOMAIN')}}/static/home/spread/product_show/xingka-qh/area2.js"></script>


<script>
    var app = new Vue({
        el: "#app",
        data: function (){
            return {
                page: 1,
                size: 20,
                total: 0,
                show:true,
                count: '',
                timer: null,
                file: "",
                lianghao:[],
                alllianghao:[],
                noDataMsg:false,
                lastSearch:'',
                products:{
                    'TT89773':['联通大王卡','今日头条']
                },
                producer:'',
                dialogVisible: false,
                detailVisible: false,
                ruleForm: {
                    name: "",
                    phone: "",
                    phoneNum: "",
                    gsd:'',
                    smsCode:"",
                    identity: "",
                    province: "",
                    city: "",
                    country: "",
                    shippingAddress: "",
                    regionCode: "",
                    regionId: 0,
                },
                rule: {
                    name: false,
                    phone: false,
                    identity: false,
                    smsCode: false,
                    province: false,
                    shippingAddress: false,
                },
                img1: 'v2/img/top.jpg',
                img2: 'v2/img/2.jpg',
                img3: 'v2/img/back.jpg',
                advId: 0,
                operationStatus: 0,
                advInfo: {},
                proValue: [],
                values: "",
                checkImg: 1,
                jsHead: '',
                jsFoot: '',
                backgroundColour: '',
                tipContent: '',
                success: false,
                proEcss: '',
                townEcss: '',
                gsdPcode: '',
                gsdCcode:'',
                cityId: 0,
                msg: [
                    {
                        name: "张**",
                        phone: "188****7542",
                        time: "1分钟前",
                        name2: "李**",
                        phone2: "136****7345",
                        time2: "1分钟前",
                    },
                    {
                        name: "王**",
                        phone: "182****7482",
                        time: "1分钟前",
                        name2: "宋**",
                        phone2: "135****4852",
                        time2: "1分钟前",
                    },
                    {
                        name: "钟**",
                        phone: "135****6352",
                        time: "2分钟前",
                        name2: "陈**",
                        phone2: "159****4685",
                        time2: "2分钟前",
                    },
                    {
                        name: "黄**",
                        phone: "137****3666",
                        time: "2分钟前",
                        name2: "李**",
                        phone2: "183****5522",
                        time2: "2分钟前",
                    },
                    {
                        name: "潘**",
                        phone: "182****2457",
                        time: "3分钟前",
                        name2: "江**",
                        phone2: "185****3335",
                        time2: "3分钟前",
                    },
                    {
                        name: "田**",
                        phone: "165****9699",
                        time: "1分钟前",
                        name2: "龚**",
                        phone2: "184****3626",
                        time2: "1分钟前",
                    },
                    {
                        name: "郑**",
                        phone: "188****3635",
                        time: "1分钟前",
                        name2: "吴**",
                        phone2: "153****6628",
                        time2: "1分钟前",
                    },
                    {
                        name: "周**",
                        phone: "159****3326",
                        time: "2分钟前",
                        name2: "王**",
                        phone2: "186****2295",
                        time2: "2分钟前",
                    },
                    {
                        name: "孙**",
                        phone: "188****3625",
                        time: "3分钟前",
                        name2: "冯**",
                        phone2: "186****3426",
                        time2: "3分钟前",
                    },
                    {
                        name: "陈**",
                        phone: "135****3625",
                        time: "1分钟前",
                        name2: "卫**",
                        phone2: "176****8876",
                        time2: "1分钟前",
                    },
                    {
                        name: "姜**",
                        phone: "169****3336",
                        time: "2分钟前",
                        name2: "韩**",
                        phone2: "182****9459",
                        time2: "2分钟前",
                    },
                ],
                animate: false,
                nums:15690,
                defaultVal: '',//地址默认值
                isShow: 1,//显示隐藏表单
            };
        },
        mounted: function () {
            this.init();
            setInterval(this.showMarquee, 3000)
        },
        methods: {
            init() {
                var code = "TT89773";
                this.ruleForm.regionCode = code;
                this.info(code);
                this.cityPicker();
                this.gsdPicker();
            },
            confirmPhone(num, phoneid){
                this.dialogVisible= false;
                this.ruleForm.phoneNum = num;
                this.ruleForm.phoneNumId= phoneid;
            },
            selectPhone(){

                if( !this.gsdPcode.length||!this.gsdCcode.length){
                    this.$toast('请先选择归属地!')
                    return;
                }

                var self = this;
                var searchValue = this.ruleForm.searchValue;
                var param = {
                    cuccProvinceEcss: self.gsdPcode,
                    cuccCityEcss: self.gsdCcode,
                    searchValue:searchValue,
                    sid:'{{$product->id}}',
                };
                var self = this;
                this.dialogVisible=true
                this.noDataMsg = false
                self.$indicator.open();
                Api.post(Api.queryPhone,JSON.stringify(param),function (res) {
                    if(res.code !== 20000){
                        self.$messagebox.alert(res.msg);
                        return;
                    }
                    self.$indicator.close();
                    let result = res.data;
                    if(!result||result.length<1){

                        // self.$toast("");
                        self.noDataMsg = true;
                        self.lianghao =[]
                        return;
                    }
                    if((result[0] instanceof Array)){
                        self.lianghao = result[0];
                        result.splice(0,1);
                        self.alllianghao = result;
                    }else{
                        self.lianghao = result;
                    }

                },function () {
                    console.info(arguments)
                    self.$indicator.close();
                    if(arguments[2]=="timeout"){
                        self.$messagebox.alert("亲，抢购太火爆了，休息以下再试哦！","温馨提示");
                    }else{
                        self.$messagebox.alert("亲，网络出错了，再刷下吧","温馨提示");
                    }
                })
            },
            formatNum(item){
                if(item.lh){
                    var redIndex = item.match[0].redIndex
                    var val ="";
                    var phoneNum =item.phoneNumber;
                    for(var i=0;i<phoneNum.length;i++){
                        var red = redIndex[i];
                        if(red !=0){
                            var color = red==1?"red":"blue";
                            val +="<span style=\"color: "+color+"\">";
                            val +=phoneNum[i];
                            val +="</span>";
                        }else{
                            val +=phoneNum[i];
                        }

                    }
                    return val;
                }else{
                    var item = '<span>'+item.phoneNumber+'</span>'
                    return item;
                }
            },
            clear() {
                var self = this;
                localStorage.clear();
                self.ruleForm.name = "";
                self.ruleForm.phone = "";
                self.ruleForm.identity = "";
                self.ruleForm.province = "";
                self.ruleForm.shippingAddress = "";
            },
            //禁止点击省市县时弹出本地软键盘
            provinceFocus() {
                document.activeElement.blur();
            },
            //确定
            confirm() {
                var self = this;
                var alertTip = document.querySelector('.alertTip');
                alertTip.style.display = 'none';
                if (self.success == true) {
                    self.clear();
                    window.location.reload();
                    // if (self.card == true) {
                    //     self.clear();
                    //     window.location.href = "success.php?bd=&code=lcfx";
                    // } else {
                    //     self.clear();
                    //     window.location.href = "card.php?bd=&code=lcfx";
                    // }
                }
                self.success = false;
                $('.loading').hide();
            },
            //点击勾选
            imgClick() {
                var self = this;
                var select = document.getElementById('select');
                if (select.style.opacity == 1) {
                    select.style.opacity = 0;
                    self.checkImg = 0;
                } else {
                    select.style.opacity = 1;
                    self.checkImg = 1;
                }
            },
            //资费详情
            priceOrder: function () {
                var window1 = document.querySelector('.window1');
                window1.style.display = "block";
            },
            //关闭详情
            close: function () {
                var window1 = document.querySelector('.window1');
                window1.style.display = "none";
            },
            //确定
            confirm1: function () {
                var window2 = document.querySelector('.window2');
                window2.style.display = "none";
            },
            //姓名
            nameId: function () {
                let nameReg = /^([\u4e00-\u9fa5\·]{2,10})$/;
                if (this.ruleForm.name == "" || nameReg.test(this.ruleForm.name) != true) {
                    $(".name-warning").show();
                    this.rule.name = false;
                } else if (this.ruleForm.name != "") {
                    $(".name-warning").hide();
                    this.rule.name = true;
                }
            },
            //手机
            phoneId: function () {
                let phoneReg = /^1(3|4|5|6|7|8|9)\d{9}$/;
                if (this.ruleForm.phone == "" || phoneReg.test(this.ruleForm.phone) != true) {
                    $(".phone-warning").show();
                    this.rule.phone = false;
                } else {
                    $(".phone-warning").hide();
                    this.rule.phone = true;
                }
                if(this.ruleForm.phoneNum == this.ruleForm.phone){
                    $(".phone-warning").show();
                    this.$toast('联系人号码不能和靓号一致！')
                    this.rule.phone = false;
                }
            },
            smsId: function () {
                let phoneReg = /^[0-9]+$/;
                if (this.ruleForm.smsCode == ""||phoneReg.test(this.ruleForm.smsCode) != true) {
                    $(".sms-warning").show();
                    this.rule.smsCode = false;
                } else {
                    $(".sms-warning").hide();
                    this.rule.smsCode = true;
                }
            },
            //身份证
            identityId() {
                var ageRange = [16,60];
                var ageMin = Number(ageRange[0])<16?16:ageRange[0];
                var ageMax = ageRange.length>1?ageRange[1]:90;
                this.changeFivteenToEighteen(this.ruleForm.identity)
                var birthYear = this.ruleForm.identity.substring(6, 10);
                var birthMonDay = this.ruleForm.identity.substring(10, 14);
                var birthMon = this.ruleForm.identity.substring(10, 12);
                var birthDay = this.ruleForm.identity.substring(12, 14);
                var nowYear = new Date().getFullYear();
                var nowMon = new Date().getMonth() + 1;
                var nowDay = new Date().getDate();
                var birtdh_16 = new Date(Number(birthYear)+Number(ageMin),Number(birthMon)-1,Number(birthDay));
                var birth_max = new Date(Number(birthYear)+Number(ageMax),Number(birthMon)-1,Number(birthDay));
                var daydiff= new Date(nowYear,nowMon-1,nowDay).getTime() + 60*24*3600*1000;
                var nowdiff= new Date(nowYear,nowMon-1,nowDay).getTime() ;
                if (this.ruleForm.identity == "" || this.IdCodeValid(this.ruleForm.identity) == false) {
                    $(".identity-warning").show();
                    $(".identity-warning1").hide();
                    this.rule.identity = false;
                } else {
                    $(".identity-warning").hide();
                    this.rule.identity = true;
                    //16周岁生日 必须 小于60天后的时间，当前时间，不能高过最大生日值
                    if (birtdh_16.getTime()<=daydiff&&birth_max.getTime()>=nowdiff) {
                        $(".identity-warning1").hide();
                        this.rule.identity = true;
                    }
                    else {
                        $(".identity-warning1").show();
                        this.rule.identity = false;
                    }
                }

            },
            //15位转18位身份证号
            changeFivteenToEighteen(card) {
                if (card.length == '15') {
                    var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                    var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                    var cardTemp = 0, i;
                    card = card.substr(0, 6) + '19' + card.substr(6, card.length - 6);
                    for (i = 0; i < 17; i++) {
                        cardTemp += card.substr(i, 1) * arrInt[i];
                    }
                    card += arrCh[cardTemp % 11];
                    return card;
                }
                return card;
            },
            //地址
            addressId: function () {
                let addressReg1 = /^[A-Za-z]{6,50}$/
                let addressReg2 = /^[A-Za-z0-9]{6,50}$/
                let addressReg3 = /^[0-9]{6,50}$/
                let addressReg4 = /^[a-z0-9\u4e00-\u9fa5\-]{6,50}$/
                if (this.ruleForm.shippingAddress == "" || addressReg1.test(this.ruleForm.shippingAddress) == true) {
                    $(".address-warning").show();
                    this.rule.shippingAddress = false;
                } else {
                    $(".address-warning").hide();
                    this.rule.shippingAddress = true;
                }
            },
            info: function (code) {
                var self = this;
                if (code == "") {
                    alert("无效code");
                }
                self.advInfo.buttonContent = "0元领取"
                self.advInfo.code = code
            },
            getSecond(){
                let _this=this;
                if(_this.count == 0) {
                    // this.btnText="获取验证码"
                    _this.show = true;
                } else {
                    // this.btnDisabled=true;
                    _this.show = false;
                    // this.btnText="验证码(" + wait + "s)"
                    _this.count--;
                    setTimeout(function() {
                            _this.getSecond();
                        },
                        1000);
                }
            },
            getCode(){
                var self = this;
                if (self.ruleForm.phone == "") {
                    $(".phone-warning").show();
                    self.rule.phone = false;
                    return;
                }

                Api.get(Api.smsUrl,{phoneNumber:self.ruleForm.phone},function (resultS) {
                    if(resultS['rspCode'] == "1000"){
                        $('.loading').hide();
                        self.tipContent = resultS['rspDesc'];
                        $('#tipContent_span').text(self.tipContent);
                        $('.alertTip').show();
                    } else {
                        self.count = 60;
                        self.getSecond();
                    }

                },function () {
                    $('.loading').hide();
                    self.tipContent = "一分钟内请不要重复发送短信！";
                    $('#tipContent_span').text(self.tipContent);
                    $('.alertTip').show();
                })

            },
            save: function () {
                var self = this;
                self.success = false;
                self.card = false;

                if (self.ruleForm.phone == "") {
                    $(".phone-warning").show();
                    self.rule.phone = false;
                }
                if (self.ruleForm.phoneNum == "") {
                    $(".phoneNum-warning").show();
                    self.rule.phoneNum = false;
                }
                if(self.ruleForm.phoneNum == self.ruleForm.phone){
                    this.$toast('联系人号码不能和靓号一致！')
                    self.rule.phoneNum = false;
                }
                if (self.ruleForm.smsCode == ""&&false) {
                    $(".sms-warning").show();
                    self.rule.smsCode = false;
                }

                if (self.ruleForm.name == "") {
                    $(".name-warning").show();
                    self.rule.name = false;
                }
                if (self.ruleForm.identity == "") {
                    $(".identity-warning").show();
                    self.rule.identity = false;
                }
                if (this.proValue.length == 0) {
                    $(".province-warning").show();
                    self.rule.province = false;
                } else {
                    $(".province-warning").hide();
                    self.rule.province = true;
                }
                if (self.ruleForm.shippingAddress == "") {
                    $(".address-warning").show();
                    self.rule.shippingAddress = false;
                }
                if (self.rule.name == false || self.rule.phone == false || self.rule.phoneNum == false || self.rule.identity == false ||
                    self.rule.province == false || self.rule.shippingAddress == false) {
                    $('.loading').hide();
                    return false;
                } else if (self.checkImg == 0) {
                    $('.window2').show();
                    $('.loading').hide();
                } else {
                    $('.loading').show();
                    var param = {
                        template:'qh',
                        job_number:'{{$reqParam['job_number'] ?? ''}}',
                        channel:'{{$reqParam['channel'] ?? ''}}',
                        sale_channel:'{{$reqParam['sale_channel'] ?? ''}}',
                        sub_agent:'{{$reqParam['sub_agent'] ?? ''}}',
                        source:'{{$reqParam['source'] ?? ''}}',
                        sid:'{{$product->id}}',
                        format_province:$('#gsd').val(),
                        name: self.ruleForm.name,
                        phone: self.ruleForm.phone,
                        phoneNum: self.ruleForm.phoneNum,
                        phoneNumId: self.ruleForm.phoneNumId,
                        cardNumber: self.ruleForm.identity,
                        province: self.proValue[0],
                        city: self.proValue[1],
                        country: self.proValue[2],
                        shippingAddress: self.ruleForm.shippingAddress,
                        shippingRegionCode: self.townEcss,
                        shippingRegionId: self.cityId,
                        adminUserId: self.advInfo.userId,
                        adminUserName: self.advInfo.userName,
                        advertisingCode: self.advInfo.code,
                        advertisingId: self.advInfo.id,
                        channelId: self.advInfo.channelId,
                        distributorId: self.advInfo.distributorId,
                        distributorName: self.advInfo.distributorName,
                        goodsId: self.advInfo.goodsId,
                        goodsName: self.advInfo.goodsName,
                        supplierId: self.advInfo.supplierId,
                        supplierName: self.advInfo.supplierName,
                        touchspotId: self.advInfo.touchspotId,
                        touchspotName: self.advInfo.touchspotName,
                        cuccProvinceEcss: self.proEcss,
                        cuccCityEcss: self.cityEcss,
                        producer: self.producer,
                        gsdPcode: self.gsdPcode,
                        gsdCcode: self.gsdCcode,
                        identityType: 1
                    };

                    var send_order = "/home/api/uniform";
                    Api.post(send_order, JSON.stringify(param), function (res) {
                        if(res.code !== 20000){
                            $('#tipContent_span').text(res.msg);
                            $('.alertTip').show();
                            return;
                        }
                        let resultOrder = res.data;
                        console.log(resultOrder);
                        if(resultOrder['rspCode'] == "1000"){
                            $('.loading').hide();
                            self.tipContent = resultOrder['rspDesc'];
                            $('#tipContent_span').text(self.tipContent);
                            $('.alertTip').show();
                        } else if(resultOrder['rspCode'] == "2000"){
                            $('.loading').hide();
                            self.tipContent = "恭喜活动参加成功！我们会快马加鞭为您送达，尽情享用百款APP免流！";
                            $('#tipContent_span').text(self.tipContent);
                            $('.alertTip').show();
                            var formBox = document.querySelector(".formBox");
                            formBox.style.display = 'none';
                            self.success = true;
                            self.card = true;
                        } else {
                            console.log(111);
                            $('.loading').hide();
                            // self.tipContent = "恭喜活动参加成功！我们会快马加鞭为您送达，尽情享用百款APP免流！";
                            $('#tipContent_span').text(res.msg);
                            $('.alertTip').show();
                            var formBox = document.querySelector(".formBox");
                            formBox.style.display = 'none';
                            self.success = true;
                        }
                    },function(n){
                        $('.loading').hide();
                        if(n.responseJSON && n.responseJSON.message){
                            self.tipContent = n.responseJSON.message;
                        }else{
                            self.tipContent ="请求失败，请重新提交！";
                        }
                        $('#tipContent_span').text(self.tipContent);
                        $('.alertTip').show();
                    });
                }
            },
            showMarquee: function () {
                var x = 10;
                var y = 1;
                var nonceNum = parseInt(Math.random() * (x - y + 1) + y);//1-10随机数
                this.animate = true;
                setTimeout(() => {
                    this.nums += nonceNum;
                    this.msg.push(this.msg[0]);
                    this.msg.shift();
                    this.animate = false;
                }, 500)
            },
            cityPicker: function () {
                var self = this;
                initProvince(function (values) {
                    var vals  = values.split("|");
                    var names = vals[0].split(",");
                    var codes = vals[1].split(",");
                    $(".province-warning").hide();
                    self.ruleForm.province = names;
                    self.proValue = names;
                    self.proEcss = codes[0];
                    self.cityEcss = codes[1];
                    self.townEcss = codes[2];
                });

            }, gsdPicker: function () {
                var self = this;
                initGsd(function (values) {
                    var vals  = values.split("|");
                    var names = vals[0].split(",");
                    var codes = vals[1].split(",");
                    self.ruleForm.gsd = names;
                    //self.proEcss = codes[0];
                    //self.cityEcss = codes[1];
                    self.gsdPcode = codes[0];
                    self.gsdCcode =codes[1];
                    self.ruleForm.phoneNum='';
                    self.alllianghao =[];
                });
            },
            //根据code获取ecss
            acquire(provinceCode, townCode, cityCode) {
                var self = this;
                //遍历出省级
                var pro = cityData.map(function (item) {
                    if (provinceCode == item.name) {
                        return item.code;
                    }
                })
                var proLength = pro.length;
                for (var i = 0; i < proLength; i++) {
                    if (pro[i] != undefined) {
                        self.proEcss = pro[i];
                    }
                }
                //所有市级
                var townList = cityData.map(function (item) {
                    return item.sub;
                })
                var townAll = townList.flat(1);
                var townLength = townAll.length;
                for (var i = 0; i < townLength; i++) {
                    if (townAll[i].name == townCode) {
                        self.townEcss = townAll[i].code;
                    }
                }
                //县级
                var cityList = townAll.map(function (item) {
                    return item.sub
                })
                var cityAll = cityList.flat(1);
                var cityLength = cityAll.length;
                for (var i = 0; i < cityLength; i++) {
                    if (cityAll[i].name == cityCode) {
                        self.values = cityAll[i].code;
                    }
                }
            },
            HandleFootJs: function () {
                var self = this;
                eval(self.jsFoot)
            },

            getUrlKey: function (name) {
                return (
                    decodeURIComponent(
                        (new RegExp("[?|&]" + name + "=" + "([^&;]+?)(&|#|;|$)").exec(
                            location.href
                        ) || [, ""])[1].replace(/\+/g, "%20")
                    ) || null
                );
            },

            //身份证号合法性验证
            //支持15位和18位身份证号
            //支持地址编码、出生日期、校验位验证
            IdCodeValid(code) {
                var city = {
                    11: "北京",
                    12: "天津",
                    13: "河北",
                    14: "山西",
                    15: "内蒙古",
                    21: "辽宁",
                    22: "吉林",
                    23: "黑龙江 ",
                    31: "上海",
                    32: "江苏",
                    33: "浙江",
                    34: "安徽",
                    35: "福建",
                    36: "江西",
                    37: "山东",
                    41: "河南",
                    42: "湖北 ",
                    43: "湖南",
                    44: "广东",
                    45: "广西",
                    46: "海南",
                    50: "重庆",
                    51: "四川",
                    52: "贵州",
                    53: "云南",
                    54: "西藏 ",
                    61: "陕西",
                    62: "甘肃",
                    63: "青海",
                    64: "宁夏",
                    65: "新疆",
                    71: "台湾",
                    81: "香港",
                    82: "澳门",
                    91: "国外 "
                };
                var row = {
                    'pass': true,
                    'msg': '验证成功'
                };
                if (!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|[xX])$/.test(
                    code)) {
                    row = {
                        'pass': false,
                        'msg': '身份证号格式错误'
                    };
                } else if (!city[code.substr(0, 2)]) {
                    row = {
                        'pass': false,
                        'msg': '身份证号地址编码错误'
                    };
                } else {
                    //18位身份证需要验证最后一位校验位
                    if (code.length == 18) {
                        code = code.split('');
                        //∑(ai×Wi)(mod 11)
                        //加权因子
                        var factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                        //校验位
                        var parity = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
                        var sum = 0;
                        var ai = 0;
                        var wi = 0;
                        for (var i = 0; i < 17; i++) {
                            ai = code[i];
                            wi = factor[i];
                            sum += ai * wi;
                        }
                        if (parity[sum % 11] != code[17].toUpperCase()) {
                            row = {
                                'pass': false,
                                'msg': '身份证号校验位错误'
                            };
                        }
                    }
                }
                return row.pass;
            }
        }
    });

    $(function () {

        $(".lk_index_box .text").click(function () {
            if ($("#aa").is(":hidden")) {
                $("#aa").slideDown();
                $("html,body").animate({
                    scrollTop: $(this).offset().top
                }, 500);
                $(this).children(".text_b").css("transform", "rotate(180deg)")
            } else {
                $("#aa").slideUp();
                $(".lk_index_box .text").animate({
                    scrollTop: 0
                }, 500);
                $(this).children(".text_b").css("transform", "rotate(0deg)")
            };
        });
        $(".lk_index_box .zifei .zifei_span").click(function () {
            if ($("#aa").is(":hidden")) {
                $("#aa").slideDown();
                $("html,body").animate({
                    scrollTop: $("#dian").offset().top
                }, 500);
                $(this).children(".text_b").css("transform", "rotate(180deg)")
            } else {
                $("#aa").slideUp();
                $(".lk_index_box .text").animate({
                    scrollTop: 0
                }, 500);
                $(this).children(".text_b").css("transform", "rotate(0deg)")
            };
        });
    });

</script>
@include('Home.common.uinapp-sdk')
</html>
