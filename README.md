# 基本介绍

hyperf-iot基于Hyperf v2.1、VUE+Prime Pro开发的前后分离管理后台，内容管理系统

## 主要特性

* 基于`Auth`验证的权限管理系统
    * 支持无限级父子级权限继承，父级的管理员可任意增删改子级管理员及权限设置
    * 支持单管理员多角色
    * 支持管理子级数据或个人数据
* 完善的前端功能组件开发
    * 基于`Prime Pro`二次开发
    * 基于`vue`开发，自适应手机、平板、PC
    * 基于`Less`进行样式开发
* 通用的会员模块和API模块
* 共用同一账号体系的Web端会员中心权限验证和API接口会员权限验证
* 整合第三方短信接口(阿里云、腾讯云短信)
* 无缝整合第三方云存储(七牛云、阿里云OSS)功能，支持云储存分片上传
* 第三方登录(QQ、微信)整合
* 第三方支付(微信、支付宝)无缝整合，微信支持PC端扫码支付


## 部分页面展示

* 控制台
![控制台](https://images.gitee.com/uploads/images/2021/0106/162041_4f4c0b7c_5102272.png "屏幕截图.png")

* 商品卡片
![输入图片说明](https://images.gitee.com/uploads/images/2021/0121/224122_453e8cf9_5102272.png "屏幕截图.png")

* 列表
![列表](https://images.gitee.com/uploads/images/2021/0106/162247_3a970594_5102272.png "屏幕截图.png")

* 编辑
![输入图片说明](https://images.gitee.com/uploads/images/2021/0121/223959_3bfccce8_5102272.png "屏幕截图.png")

* 系统配置
![输入图片说明](https://images.gitee.com/uploads/images/2021/0121/223906_91c88661_5102272.png "屏幕截图.png")

## 环境要求

 - PHP >= 7.2
 - Swoole PHP extension >= 4.5，and Disabled `Short Name`
 - OpenSSL PHP extension
 - JSON PHP extension
 - PDO PHP extension （If you need to use MySQL Client）
 - Redis PHP extension （If you need to use Redis Client）
 - Protobuf PHP extension （If you need to use gRPC Server of Client）
 - RabbitMQ >=3.8
 
 ## 快速开始
 一、拉取代码到你已经安装好以上环境的服务器中
 ```shell script
git clone https://gitee.com/bufanyun/hyperf-iot.git && cd hyperf-iot
 ```

二、配置你的站点信息
 - 将根目录下的`.env.example`名称改为.env，并配置相关信息，默认使用了redis和rabbitmq组件，所以不配置将无法正常使用！
 - 服务默认使用的是9609端口，请放行防火墙端口，如需修改为其他端口请到`/config/autoload/server.php`中修改！

三、更新composer包
  ```shell script
 composer update
  ```

四、 启动服务，执行下面任意一个命令即可，首次启动会自动缓存代理配置，可能需要时间久一些
   ```shell script
  php bin/hyperf.php serve:watch  #测试调试期间用这个
  php bin/hyperf.php start  #线上用这个
   ```

五、访问测试
   ```shell script
  curl http://127.0.0.1:9609
   ```
 - 如果能看到：{"code":20000,"msg":"操作成功","data":{"$method":"GET"}}则说明启动成功！
 
  ## 声明
  本项目还在持续更新中，暂不公开数据结构，仅供学习参考，遇到问题请联系作者下方微信！
  
  ![输入图片说明](https://images.gitee.com/uploads/images/2021/0121/222810_ac5b4081_5102272.png "屏幕截图.png")



  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  


  

