//省市区选择

function initProvince(onchange) {
  var area1 = $("#post-province");
  area1.html("");
  var num1 = cityData.length;
  for (var i = 0; i < num1; i++) {
    if (i == 0) {
      area1.append('<li class="selected" index="#2">#1</li>'.replace("#1", cityData[i].name).replace("#2",cityData[i].code));
    } else {
      area1.append('<li index="#2">#1</li>'.replace("#1", cityData[i].name).replace("#2",cityData[i].code));
    }
  }

  var area2 = $("#post-city");
  area2.html("");
  var num2 = cityData[0].sub.length;
  for (var j = 0; j < num2; j++) {
    var area2Obj = cityData[0].sub[j];
    if (j == 0) {
      area2.append('<li class="selected" index="#2">#1</li>'.replace("#1", area2Obj.name).replace("#2",area2Obj.code));
    } else {
      area2.append('<li index="#2">#1</li>'.replace("#1", area2Obj.name).replace("#2",area2Obj.code));
    }
  }

  var area3 = $("#post-district");
  area3.html("");
  var num3 = cityData[0].sub[0].sub.length;
  for (var k = 0; k < num3; k++){
    var areaObj = cityData[0].sub[0].sub[k];
    area3.append('<li index="#2">#1</li>'.replace("#1", areaObj.name).replace("#2", areaObj.code));
  }

  area1.on("click", "li", function (t) {
    $(this).addClass("selected").siblings("li").removeClass("selected");
    var provinceName = $(this).text();
    var citylists = getCityList(provinceName);

    area2.html('')
    var len = citylists.length;
    var temp = ""
    for (var i = 0; i < len; i++) {
      temp += '<li index="#2">#1</li>'.replace("#1", citylists[i]["name"]).replace("#2", citylists[i]["code"])
    }
    area2.append(temp);
    //获取第一个城镇
    var allAreaList = getAreaList(provinceName, citylists[0])
    len = allAreaList.length;
    temp = ""
    area3.html('')
    for (var j = 0; j < len; j++) {
      temp += '<li index="#2">#1</li>'.replace("#1", allAreaList[j].name).replace("#2", allAreaList[j].code)
    }
    area3.append(temp);
  });
  area2.on("click", "li", function (t) {
    $(this).addClass("selected").siblings("li").removeClass("selected");
    var provinceName = area1.find("li.selected").text();
    //获取第一个城镇
    var cityName = $(this).text();
    var allAreaList = getAreaList(provinceName, cityName);
    len = allAreaList.length;
    temp = ""
    area3.html('')
    for (var j = 0; j < len; j++) {
      temp += '<li index="#2">#1</li>'.replace("#1", allAreaList[j].name).replace("#2", allAreaList[j].code)
    }
    area3.append(temp);
  });
  area3.on("click", "li", function (t) {
    var n = area1.find("li.selected"),
      s = area2.find("li.selected"),
      o = $(this);
    0 !== s.length && (o.addClass("selected").siblings("li").removeClass("selected"));
    if (0 !== area3.find("li.selected").length) {
      $("#area").hide();
      $(".mask").hide();
      var str = n.text() + "," + s.text() + "," + o.text();
      var codes = n.attr("index") + "," + s.attr("index") + "," + o.attr("index");
      console.info(codes)
      $("#txtCity").val(str);
      // var values =str.split(",").push(codes.split(","))
      onchange(str+"|"+codes);
      reScroll();
    }
  });
  $("#txtCity").focus(function () {
    $(".mask").show();
    $("#area").show();
    $(".mask").bind("click", function () {
      $(this).hide();
      $("#area").hide();
      reScroll();
      $(this).unbind("click");
    })
    noScroll(true);
    $(this).blur();
  });
}
function getCityList(provinceName) {
  var len = cityData.length;
  var cityList = []
  for (var i = 0; i < len; i++) {
    var cityItem = cityData[i];
    if (provinceName == cityItem.name) {
      var sub = cityItem['sub'];
      for (var j = 0; j < sub.length; j++) {
        cityList.push(sub[j]);
      }
      break;
    }
  }
  return cityList;
};

function getCityList2(provinceName) {
  var len = cityData2.length;
  var cityList = []
  for (var i = 0; i < len; i++) {
    var cityItem = cityData2[i];
    if (provinceName == cityItem.name) {
      var sub = cityItem['sub'];
      for (var j = 0; j < sub.length; j++) {
        cityList.push(sub[j]);
      }
      break;
    }
  }
  return cityList;
};


function getAreaList(provinceName, cityName) {
  var len = cityData.length;
  var allAreaList = []
  for (var i = 0; i < len; i++) {
    var cityItem = cityData[i];
    if (provinceName == cityItem.name) {
      var sub = cityItem['sub'];
      for (var j = 0; j < sub.length; j++) {
        if (cityName == sub[j]['name']) {
          var areaList = sub[j]['sub'];
          for (var k = 0; k < areaList.length; k++) {
            allAreaList.push(areaList[k]);
          }
          break;
        }
      }
      break;
    }
  }

  return allAreaList;

};
function noScroll(state) {
  $('.mask').css('height', $(document).height() + 'px');
  if (state) {
    var scrollTop = $(document).scrollTop();
    $('.mask').on('touchmove', function (e) { e.preventDefault(); e.stopPropagation(); });
    $(document).on('scroll', function (e) { $(document).scrollTop(scrollTop); })
  } else {
    $(document).scrollTop(0);
    $('html, body').addClass('no-scroll');
  }
}
function reScroll() {
  $('.mask').unbind('touchmove');
  $(document).unbind('scroll');
  $('html, body').removeClass('no-scroll');
}



function  initGsd(onchange) {
  var area1 = $("#gsd-province");
  area1.html("");
  var selectArea = $("#gsd");
  var num1 = cityData2.length;
  for (var i = 0; i < num1; i++) {
    if (i == 0) {
      area1.append('<li class="selected" index="#2">#1</li>'.replace("#1", cityData2[i].name).replace("#2",cityData2[i].code));
    } else {
      area1.append('<li index="#2">#1</li>'.replace("#1", cityData2[i].name).replace("#2",cityData2[i].code));
    }
  }

  var area2 = $("#gsd-city");
  area2.html("");
  var num2 = cityData2[0].sub.length;
  for (var j = 0; j < num2; j++) {
    var area2Obj = cityData2[0].sub[j];
    if (j == 0) {
      area2.append('<li class="selected" index="#2">#1</li>'.replace("#1", area2Obj.name).replace("#2",area2Obj.code));
    } else {
      area2.append('<li index="#2">#1</li>'.replace("#1", area2Obj.name).replace("#2",area2Obj.code));
    }
  }
  area1.off("click","li").on("click", "li", function (t) {
    $(this).addClass("selected").siblings("li").removeClass("selected");
    var provinceName = $(this).text();
    var citylists = getCityList2(provinceName);

    area2.html('')
    var len = citylists.length;
    var temp = ""
    for (var i = 0; i < len; i++) {
      temp += '<li index="#2">#1</li>'.replace("#1", citylists[i]["name"]).replace("#2", citylists[i]["code"])
    }
    area2.append(temp);
  });
  area2.off("click","li").on("click", "li", function (t) {
    $(this).addClass("selected").siblings("li").removeClass("selected");

    var n = area1.find("li.selected"),
      s = area2.find("li.selected")

    if (0 !== area2.find("li.selected").length) {
      $("#areaGsd").hide();
      $(".mask").hide();
      var str = n.text() + "," + s.text() ;
      var codes = n.attr("index") + "," + s.attr("index") ;
      console.info(codes)
      // var values =str.split(",").push(codes.split(","))
      onchange(str+"|"+codes);
      reScroll();
    }
  });


  selectArea.focus(function () {
    $(".mask").show();
    var originId = $(this).attr("id");
    $("#areaGsd").attr("originId",originId).show();
    $(".mask").bind("click", function () {
      $(this).hide();
      $("#area").hide();
      reScroll();
      $(this).unbind("click");
    })
    noScroll(true);
    $(this).blur();
  });
}

