关于
-----

API文档编写是非常重要的一项工作，但是编写维护非常麻烦，本项目的解决的问题是以最轻松的方式维护n个接口，每个接口n个版本。原理是解析固定格式的txt文件实时全自动生成文档，大量使用继承、递归、前缀、替换等等。

需求
----
PHP: >=5.4

ext-yaf: >=2.3.3

安装
----
1. 下载源码解压缩
1. composer install
1. 绑定web服务器的入口目录为本项目的public目录
1. 现在应该可以访问了，如果有问题见<b>联系我们</b>

联系我们
--------
Email: admin@phpdr.com<br>
QQ群:424844502

特性
----
1. 无需数据库。
1. 编写非常简便，所有枯燥的工作全部由机器完成。
1. 支持非常多的格式。
1. 以非常便捷的方式实现继承，加前缀，后缀等等各种高级功能。

Demo
----
[http://demo-apidoc.phpdr.net](http://demo-apidoc.phpdr.net "Demo")<br>
用户名：admin<br>
密码：admin

用法
----
语法说明文件查看[doc/Api.txt](doc/Api.txt)<br>
Demo使用的数据文件在[app/data/doc/api](app/data/doc/api)<br>
看Demo和文档基本就会用，更深入问题讨论见<b>联系我们</b>
