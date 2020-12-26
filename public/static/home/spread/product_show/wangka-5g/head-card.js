webpackJsonp([51], {
    0 : function(e, t, o) { (function(e) {
        "use strict";
        function t(e) {
            return e && e.__esModule ? e: {
                default:
                e
            }
        }
        var a = o(6),
            i = t(a);
        o(119);
        var s = o(9);
        e(function() {
            i.
            default.attach(document.body);
            var t = {
                    "08-2278-3099-9999": "1110352853",
                    "08-2278-3098-9999": "1110430540"
                },
                o = (0, s.getUrlParam)(); (0, s.resize)(375);
            var a = function() {
                    e("html, body").addClass("no-scroll")
                },
                c = function() {
                    e("html, body").removeClass("no-scroll")
                };
            t[o.channel] && (0, s.gdtScript)(t[o.channel]);
            var d = {
                performanceMonitor: 1
            };
            if ("28" === o.channel && "1" === o.channel2) {
                console.log(d);
                var n = document.createElement("script");
                n.src = "//pingjs.qq.com/h5/stats.js?v2.0.4",
                    n.setAttribute("name", "MTAH5"),
                    n.setAttribute("sid", "500510322"),
                    n.setAttribute("cid", "500510343");
                var l = document.getElementsByTagName("script")[0];
                l.parentNode.insertBefore(n, l)
            }
            var r = "极速5G选王卡！邀您立即领取腾讯王卡",
                m = "超多款5G会员权益任你选，看视频、听音乐、刷快手，多家权益享不停！",
                h = location.href.split("#")[0],
                p = "https://m.10010.com/queen/static/images/tencent/heroes-invincible/images/share-img.jpg"; (0, s.setShare)(r, m, h, p),
                e(".meal-desc .close").on("click",
                    function() {
                        e(".mask, .meal-desc").hide(),
                            c()
                    }),
                e(".submit").on("click",
                    function() {
                        if (a(), e(".mask, .meal-desc").show(), e(".name-item li").removeClass("selected"), 0 === e(".checked").index()) {
                            e(".name-item li").eq("0").addClass("selected"),
                                e(".show-when-5g").css({
                                    visibility: "hidden"
                                }),
                                e(".prod-4g").show(),
                                e(".prod-5g").hide();
                            var t = "";
                            e(".meal-name img").removeClass().addClass("card" + e(".prod-4g .selected").data("prod")),
                                t = void 0 !== e(".prod-4g .selected").text().split("：")[1] ? e(".prod-4g .selected").text().split("：")[1] : e(".prod-4g .selected").text(),
                                e(".fee-intro").text(t),
                                e(".price").text(e(".prod-4g .selected").data("prod")),
                                e(".fuhao").hide()
                        } else {
                            var o = "";
                            e(".meal-name img").removeClass().addClass("card" + e(".prod-5g .selected").data("prod")),
                                o = void 0 !== e(".prod-5g .selected").text().split("：")[1] ? e(".prod-5g .selected").text().split("：")[1] : e(".prod-5g .selected").text(),
                                e(".fee-intro").text(o),
                                e(".price").text(e(".prod-5g .selected").data("prod")),
                                e(".cost").text(e(".prod-5g .selected").data("cost")),
                                e(".name-item li").eq("1").addClass("selected"),
                                e(".show-when-5g").css({
                                    visibility: "visible"
                                }),
                                e(".prod-4g").hide(),
                                e(".prod-5g").show(),
                                e(".fuhao").show(),
                                e(".fuhao").text("￥" + e(".prod-5g .selected").data("cost"))
                        }
                    }),
                e(".name-item").on("click", "li",
                    function(t) {
                        var o = e(t.currentTarget);
                        o.addClass("selected").siblings("li").removeClass("selected");
                        var a = o.data("type");
                        1 === a ? (e(".show-when-5g").css({
                            visibility: "visible"
                        }), e(".prod-4g").hide(), e(".prod-5g").show(), e(".fuhao").show()) : (e(".show-when-5g").css({
                            visibility: "hidden"
                        }), e(".prod-4g").show(), e(".prod-5g").hide(), e(".fuhao").hide()),
                            e(".product-item ul:visible li").first().click()
                    }),
                e(".product-item").on("click", "li",
                    function(t) {
                        var o = e(t.currentTarget);
                        o.addClass("selected").siblings("li").removeClass("selected"),
                            e(".meal-name img").removeClass().addClass("card" + o.data("prod"));
                        var a = "";
                        a = void 0 !== o.text().split("：")[1] ? o.text().split("：")[1] : o.text(),
                            e(".fee-intro").text(a),
                            e(".price").text(o.data("prod")),
                            e(".cost").text("￥" + o.data("cost")),
                            e(".fuhao").text("￥" + o.data("cost"))
                    }),
                e(".first-fee").on("click", "li",
                    function(t) {
                        var o = e(t.currentTarget);
                        o.addClass("selected").siblings("li").removeClass("selected")
                    }),
                e("#toTop").on("click",
                    function() {
                        e("body").animate({
                                scrollTop: "0px"
                            },
                            800)
                    }),
                e(".meal-desc .btn").on("click",
                    function() {

                        // var t = e(".product-item ul:visible li.selected");
                        // window.MtaH5 && window.MtaH5.clickStat("morder_apply", {
                        //     openid: o.openid
                        // });
                        // var a = location.host.indexOf("demo.mall.10010") > -1 ? 1 : 0;
                        // a ? location.href = "https://demo.mall.10010.com:8108/queen/common-fill/delay-common-fill.html?goodsid=" + t.data("testid") + "&productName=" + t.data("name") + (0, s.buttonUrlParam)(o) : location.href = "https://card.10010.com/queen/common-fill/delay-common-fill.html?goodsid=" + t.data("id") + "&productName=" + t.data("name") + (0, s.buttonUrlParam)(o)
                    }),
                e("#planetmap1").on("click",
                    function() {
                        location.href = "https://m.10010.com/queen/tencent/flow-range.html"
                    }),
                e("#planetmap2").on("click",
                    function() {
                        location.href = "https://txwk.10010.com/kcardorder/rules/index.html#/5GFeeRule"
                    }),
                e("#planetmap3").on("click",
                    function() {
                        location.href = "https://txwk.10010.com/kcardorder/rules/index.html#/5GVipRule"
                    }),
                e("#planetmap4").on("click",
                    function() {
                        location.href = "https://txwk.10010.com/kcardorder/rules/index.html#/5GFeeRule"
                    }),
                e(".upgrade").on("click",
                    function() {
                        var e = navigator.userAgent.toLowerCase();
                        "micromessenger" === e.match(/MicroMessenger/i) ? location.href = "https://txwk.10010.com/KCard/wxCommon/goto?state=KCARD_WK5GZONE_UPGRADE&ADTAG=wk5g.1": location.href = "https://txwk.10010.com/KCard/wxCommon/goto_direct?state=KCARD_WK5GZONE_UPGRADE&ADTAG=wk5g.1"
                    }),
                e(".cards, .tab span").on("click",
                    function(t) {
                        var o = e(t.currentTarget);
                        e(".cards").removeClass("checked"),
                            e(".tab span").removeClass("tab-checked"),
                            e(".charges-info").hide(),
                            0 === o.index() ? (e(".left1, .left").show(), e(".cards").eq("0").addClass("checked"), e(".tab .left-tab").addClass("tab-checked"), e(".left2, .left3").hide(), e(".charges-info").eq("0").show()) : (e(".cards").eq("1").addClass("checked"), e(".tab .right-tab").addClass("tab-checked"), e(".left1, .left").hide(), e(".left2, .left3").show(), e(".charges-info").eq("1").show())
                    }),
                window.addEventListener("scroll",
                    function() {
                        var t = e(".btn-section").height();
                        e(".btn-section")[0].getBoundingClientRect().top < -t ? e(".tab").show() : e(".tab").hide()
                    });
            var u = 750,
                f = [[488, 388, 717, 432]],
                g = [[539, 826, 717, 876]],
                v = [[546, 312, 715, 358]],
                w = [[537, 508, 713, 541]],
                b = function(e, t) {
                    for (var o = "",
                             a = 0; a < e.length; a += 1) a === e.length - 1 ? o += e[a] * t: (o += e[a] * t, o += ",");
                    return o
                },
                k = function(t, o, a, i) {
                    e(i).find("area").each(function(i, s) {
                        e(s).attr("coords", b(a[i], o / t))
                    })
                };
            k(u, e(document.body).width(), f, "#planetmap1"),
                k(u, e(document.body).width(), g, "#planetmap2"),
                k(u, e(document.body).width(), v, "#planetmap3"),
                k(u, e(document.body).width(), w, "#planetmap4"),
                e(window).resize(function() { (0, s.resize)(375)
                })
        })
    }).call(t, o(1))
    },
    119 : function(e, t) {}
});
//# sourceMappingURL=head-card.js.map
