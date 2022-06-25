# SONA - 欶那

- [SONA - 欶那](#sona---欶那)
	- [简介](#简介)
	- [测试网址](#测试网址)
	- [安装方法](#安装方法)
- [工具原理](#工具原理)
	- [什么是反向代理](#什么是反向代理)
	- [反向代理有什么好处](#反向代理有什么好处)
	- [常见的在线代理方式](#常见的在线代理方式)
		- [1. 通过url参数](#1-通过url参数)
			- [示例](#示例)
			- [优点](#优点)
			- [缺点](#缺点)
		- [2. 方法1的伪静态升级版](#2-方法1的伪静态升级版)
			- [什么是伪静态](#什么是伪静态)
			- [示例](#示例-1)
			- [优点](#优点-1)
			- [缺点](#缺点-1)
		- [3. 伪装成其他网站](#3-伪装成其他网站)
			- [什么是hosts](#什么是hosts)
			- [示例](#示例-2)
			- [优点](#优点-2)
			- [缺点](#缺点-2)
		- [4. 通过二级域名传递源域名](#4-通过二级域名传递源域名)
			- [示例](#示例-3)
			- [优点](#优点-3)
			- [缺点](#缺点-3)
		- [5. 混合式](#5-混合式)
			- [示例](#示例-4)
			- [优点](#优点-4)
			- [缺点](#缺点-4)
	- [具体如何实现反向代理](#具体如何实现反向代理)

## 简介

**苏拉 - 在线代理**是一个由php编写的轻量级网络反向代理工具。

## 测试网址

[sona.seventop.top](http://sona.seventop.top)

（由于本人资金较为紧缺，服务器性能过烂，本网站仅供展示，请勿用其进行访问。请勿传播。）（访问pixiv这种的就跟拉稀一样，很难受，请求成功率极低，还会让网站暂时陷入瘫痪，请勿用其进行访问）

## 安装方法

1. 克隆本仓库到你的网站文件夹下。比如`/website/sona`。
2. 假如你的网站一级域名为`xxx.com`，修改apache的配置，增加以下语句
   ```
   <VirtualHost *:80>
   	ServerName sona.xxx.com
   	DocumentRoot /website/sona
   </VirtualHost>
   <VirtualHost *:80>
   	ServerName sona.x.xxx.com
   	DocumentRoot /website/sona/gooooo
   	ServerAlias sona.*.xxx.com
   </VirtualHost>
   ```
3. 访问`sona.xxx.com`，愉快进行网上冲浪。


# 工具原理

本说明将详细解析**馊辣 - 在线代理**的原理。

本说明争取使不了解网络的人也能看懂，故其中穿插一些对概念的普及，可选择性阅读。

## 什么是反向代理

代理，即代理上网。从字面也能看出来，就是别人替你上网的意思。本项目是一个反向代理工具。意思就是服务器给你获取网络资源，之后再发给你。

一般来说反向代理有几种方式。最常见的就是虚拟专用网络（VPN），也有通过浏览器访问的在线代理。本工具属于在线代理，优点是你不用额外安装任何软件，缺点是基本上只支持http和https协议的连接。

## 反向代理有什么好处

由于是服务器给你获取资源，反向代理最大的好处就是可以匿名访问互联网，保证了你的隐私。

其次根据服务器所在地不同，所获取的资源也不一定相同，可以根据这一特点在国外或港澳台搭建服务器，就可在国内利用反向代理访问外网。

而且由于仅通过一个服务器就可以浏览整个网络，在对外访问有限制的时候非常有用。比如某些学校内网仅允许访问一个域名，就可以利用反向代替通过这一个域名在网上冲浪。（不过本工具的二级域名并不固定，所以不适合用作此用途）

## 常见的在线代理方式

在线代理常见的有5种方式。

在举例时，我们统一假设代理服务器域名为`sona.xxx.top`。

### 1. 通过url参数

#### 示例

所谓url参数其实就是网址中问号后面的部分。比如我如果想访问`http://baidu.com/`，我就要在浏览器中输入`http://sona.xxx.top/?url=http://baidu.com`。这个url看起来有点长，我们可以把它拆成两个部分。

1. `http://sona.xxx.top/`。这是在访问代理服务器。
2. `?url=http://baidu.com`，就是问号后面的部分。这个部分给了代理服务器一个叫`url`的参数，值是`http://baidu.com`。代理服务器通过读取这个参数的值就可以获取对应的资源，发给用户。

于是这样，这个方法就必须需要处理两个问题：

1. 相对路径的问题。比如网页需要加载`./img/hh.jpg`，实际上加载的是`http://sona.xxx.top/img/hh.jpg`，而不是我们希望它加载的`http://sona.xxx.top/?url=http://baidu.com/img/hh.jpg`。对于这个问题，服务器需要进行较为麻烦的处理。
2. 根路径的问题。对于根路径，就是类似`/img/hh.jpg`的路径，也是和相对路径同样的问题。
3. 绝对路径的问题。比如网页需要加载`http://img.com/hh.jpg`，实际上是直接访问的`img.com`进行加载，绕开了我们的代理服务器。所以我们要想办法让它变成`http://sona.xxx.top/?url=http://img.com/hh.jpg`。

#### 优点

- 服务器获取参数很简单
- 用户输入也很简单，大概

#### 缺点

- 需要处理相对路径
- 需要处理根路径
- 需要处理绝对路径

### 2. 方法1的伪静态升级版

方法1我们发现，缺点有点多了。为了改进，我们可以使用伪静态。

#### 什么是伪静态

伪静态顾名思义，就是假的静态，可以让你的网站看起来是静态页面。它的实现方法叫url重写。

举例来说，假如我们的网站有个`api`文件夹，文件夹里头还有个`img`文件夹，然后`img`文件夹里头有很多图片，但是我们觉得访问`//sona.xxx.top/api/img/hh.jpg`太麻烦了，于是我们可以在`api`文件夹下搞一个php脚本叫`get-img.php`，传入url参数来从`img`文件夹中获取图片发给你。我们就可以访问类似`//sona.xxx.top/api/get-img.php?name=hh.jpg`的url来获取图片啦！

真是荒唐！新的url比之前长太多了，好像根本没有用处。这个时候就可以用url重写这一伟大的工具。只需要在`api`文件夹下创建文件`.htaccess`，然后添加几句话：

```
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ get-img.php?name=$1 [QSA,PT,L]
</IfModule>
```

这个文件就描述了url重写的规则。重点在倒数第二句：`get-img.php?name=$1`，意思就是如果url访问下级文件或目录，就在url的本级目录后面插上`get-img.php?name=`。

有点绕，举个例子。比如`//sona.xxx.top/api/hh.jpg`。这是访问下级文件的url对吧，因为访问了`hh.jpg`。那么就在本级目录后面，也就是`api/`后面，插上`get-img.php?name=`。于是url就变成了`//sona.xxx.top/api/get-img.php?name=hh.jpg`。不过这是对服务器而言的。实际上不论是对于用户还是浏览器，看见的都还是重写之前的url，没人知道你在url中间插上了一串字。真够“伪”的！

那么这就是我们想要的效果了。现在我们访问`//sona.xxx.top/api/hh.jpg`，就能让`get-img.php`给我们把`hh.jpg`发过来。这只是一个例子，伪静态带来的可能性实在太多了！这里就不详细描述了。

#### 示例

我们可以发现，方法1对于相对路径的处理十分的麻烦。但是现在我们知道世界上有个神奇的东西叫做伪静态，能够欺骗浏览器，让浏览器以为我们实际上在某个文件夹中，这样子就可以得到正确的相对路径。

举个例子，比如我们要访问`https://pixiv.net/artworks/76724192/`，我们就可以在浏览器中输入`http://sona.xxx.top/https/pixiv.net/artworks/76724192/`。分析一下这个url，不难看出来，url访问的的第一个“文件夹”`/https/`是协议类型，第二个“文件夹”`/https/pixiv.net/`则是要访问的源网站。再之后的文件夹就都是源网站的目录了。

这样子对于服务器来说，只需要进行一些简单的处理：把`https/pixiv.net`中的`/`替换成`://`，就能获取到正确的源网址，请求到`https://pixiv.net`。

于是我们就这样把相对路径问题给解决了。比如网页需要加载`./img/hh.jpg`，那补全了url，就是在加载`http://sona.xxx.top/https/pixiv.net/artworks/76724192/img/hh.jpg`。没错，域名还是我们的域名，走的还是代理路线。并没有什么问题。

不过根路径问题依然没有解决。比如加载`/img/hh.jpg`，实际上加载的就是`http://sona.xxx.top/img/hh.jpg`。这就是在让代理服务器去获取`img://hh.jpg/`！非常明显是错误的，代理网站的根目录不是源网站的根目录。服务器需要让它变成`http://sona.xxx.top/https/pixiv.net/img/hh.jpg`。

而且有些相对路径并不标准。比如`../../../../../../img/hh.jpg`。这么多个上级目录叠到一块，最后就回到根目录了，还是会出现根目录问题。

#### 优点

- 服务器获取参数很简单
- 解决了相对路径

#### 缺点

- 需要处理根路径
- 需要处理绝对路径

### 3. 伪装成其他网站

方法2还是不够好。于是我们可以通过伪装成其他网站的方式解决根路径和相对路径的一切问题。

#### 什么是hosts

这个方法使用了hosts来改变用户网址的服务器。那么什么是hosts呢？

其实我们现在所说什么域名，都并不是服务器真正的名称，计算机其实根本就不认字，计算机只认ip地址。那么我们是怎么输入域名连接服务器的呢？这其中有个服务叫dns，给我们做了域名转ip的工作。每次浏览器访问网址的时候，都会去向dns服务器请求域名的ip，之后再去连接那个ip，实现通讯。

而hosts简单理解就是本地的dns服务。浏览器在向dns服务器请求前，会先检查一下本地hosts有没有相关配置。如果有就使用本地的配置，绕开dns服务器；如果没有就照旧连接dns服务器获取ip。

通过配置本地hosts，你可以把源网站的ip改成代理服务器的ip，这样子你浏览网站的时候其实一直都连接的是代理服务器，就做到了代理伪装成原网站的效果。

#### 示例

伪装成其他网站需要用户在hosts上动手脚。假如我们想访问`https://pixiv.net/artworks/76724192/`，而且代理服务器的ip是`123.456.789.10`，那么我们需要在hosts中加上这么一句：
```
123.456.789.10 pixiv.net
```
这样子我们去访问`https://pixiv.net/artworks/76724192/`时，连接的就是代理服务器了。代理服务器如何请求源网站呢？可以通过获取用户当前的主机名，在本例中就是`pixiv.net`；再通过伪静态获取用户访问的目录，本例中就是`artworks/76724192/`，然后拼到一起，就获得了源网址。

这个方法完美地解决了方法2的根路径问题：看起来你就是在访问源网站！但是这个方法也有弊端。

首先最大的弊端就是这样只能访问有限的网址。如果想要访问`pixiv.net`，我们要在hosts中增加一行配置；那如果想要访问`www.pixiv.net`呢？`s.pximg.net`呢？很明显，我们都要为其配置hosts。然而世界上的网站是无穷无尽的，我们没办法给所有网址都配置hosts。解决方法是开发一个特制的浏览器，不论连接什么网站都使用代理服务器的ip。可是这样子就又偏离了在线代理“不安装任何额外软件”的初衷。

其次就是这样子只能建立不安全的连接，用户不能使用安全的https协议。因为我们是在伪装其他网站，不论再怎么像也只是伪装而已，我们并不是源网站。这意味着如果使用https协议，我们就没法通过浏览器的安全验证。这同时也导致一个问题：如果服务器在国外，不安全的连接过多会导致ip被墙——来自本人痛苦的亲身经历。

#### 优点

- 服务器获取参数很简单
- 用户输入超级简单
- 解决了相对路径
- 解决了根路径

#### 缺点

- 用户配置hosts可能会出现问题
- 无法建立安全连接
- 可用网址有限

### 4. 通过二级域名传递源域名

为了解决方法3的各种问题，我们可以想到一种折中的方法：我们可以不用把域名改成源域名，我们把域名中的一部分改成源域名就好了。一般来说这个“一部分”可以放在域名的倒数第二个点前面，也就是二级域名。

#### 示例

使用这个方法，假如我们想访问`https://baidu.com/`，我们就需要访问`http://sona.https.baidu.com.xxx.top`。这个域名有点长，不过仔细看看是能看出这个域名是怎么出来的：我们把协议`https`和域名`baidu.com`插到了我们之前的域名`sona.xxx.top`的前半部分`sona`的后面了！变成了`sona.https.baidu.com.xxx.top`。这样子既访问了我们的代理服务器，又传递了源域名！

这里有几个问题。
1. 为什么我们要把源域名插入到第一部分的后头，而不是直接挂在第一部分的前头呢？其实都可以，哈哈。我之所以喜欢插在中间，其实就是因为我喜欢而已。
2. 源域名里有句点，我们的域名里也有句点，难道不会混淆吗？其实不会。我们虽然不知道整个域名一共有几个句点，但是我们知道头上的第一个句点是我们的`sona`，第二个句点是协议类型，尾巴上还有两个句点也是我们的，其他都是源域名的。掐头去尾，得到的就是源域名。
3. 如果源网站对端口有要求呢？比如`baidu.com:8080`，众所周知域名中间是不能加冒号的，我们要如何传递端口参数呢？其实解决方法也很简单：我们只需要把冒号变成句点就行了，就是`sona.https.baidu.com.8080.xxx.top`。这个时候你可能会问“变成句点会不会把端口误认成顶级域名？”，其实简单想一想就知道是不会的：因为没有顶级域名是纯数字的。如果我们发现顶级域名全是数字，那我们就能知道这肯定是端口。

于是，我们已经基本上解决了所有困难的问题了。除了——绝对路径的问题。我们发现这个样子没法简单替换字符串了，我们要通过正则表达式才能实现绝对路径的转换，这对于服务器的性能以及我购买服务器的钱包来说有极大的考验。

#### 优点

- 解决了相对路径
- 解决了根路径

#### 缺点

- 服务器获取参数较为困难
- 用户也较为困难，基本上没法自己输网址了！
- 需要处理绝对路径，而且只能通过正则表达式

### 5. 混合式

我们发现前面4种方法各有各的好处。为了最方便的实现代理，我们可以将其混合使用。

#### 示例

本工具将方法4和方法1进行混合。

我们发现方法4已经足够优秀了！看看这些缺点：

> 服务器获取参数较为困难

呵呵，没有较为困难的服务器，只有写不出程序的程序员。我们难不成还会叫这个给难倒？（其实还有占用的资源方面的问题，不过没办法了……）

> 用户也较为困难，基本上没法自己输网址了！

~~呵呵，能给他们代理就是最大的恩惠了！~~我们可以编写辅助性工具，帮助用户进行转换。比如在输入框里输一个网址，按一个回车，就可以跳转到代理后的页面之类的，不也挺方便的吗。

> 需要处理绝对路径，而且只能通过正则表达式

没错。这是最大的缺点。我们需要想办法解决这个问题。看一看上面的那三个方法，我们惊奇的发现，只有方法4需要正则表达式！这不就太好了？我们把那三个方法和方法4混合起来用不就好了吗。最简单的是和方法1进行混合。我们可以另外搞一个使用url参数的通道，访问此通道也可以实现代理，访问后重定向到方法4的网址。

比如访问`http://sona.xxx.top/turn.php?url=https://pixiv.net/artworks/76724192/`之后，就301重定向到`http://sona.https.pixiv.net.xxx.top/artworks/76724192/`。这样子我们就不用把源网页的url变成方法4的形式，我们只需要把url变成方法1的形式就行了。于是我们绕开了正则表达式这一性能大老虎。

#### 优点

- 解决了相对路径
- 解决了根路径

#### 缺点

- 需要处理绝对路径

## 具体如何实现反向代理

基本的原理我们现在是确定了，那么如何实现呢？

我不知道啊。这个README的大小已经比整个项目其他所有文件加起来还大了，源代码没多少点，还带注释，去读源代码吧。
