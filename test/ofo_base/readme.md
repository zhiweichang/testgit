## Lumen Framework Skeleton

This is a Lumen framework skeleton for project developments.

Install
> composer install

note:
> composer已配置为读取国内源。

### 新增以下Feature
* config目录配置文件加载
* JSON响应
* 错误处理
* QConf配置读取
* Redis
* 通用签名认证中间件
* 多语言(可选，在i18n分支)
* 集成ofo-utils，包含日志，HttpClient, MQ

#### 多语言(在i18n分支)
* 根据locale配置，返回时自动翻译响应code对应的message；
* 根据请求参数country_code自动判定所需语言
