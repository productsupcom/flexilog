## Table of contents

- [\Productsup\Logger](#class-productsuplogger)
- [\Productsup\LogInfo](#class-productsuploginfo)
- [\Productsup\Handler\AbstractHandler (abstract)](#class-productsuphandlerabstracthandler-abstract)
- [\Productsup\Handler\GelfHandler](#class-productsuphandlergelfhandler)
- [\Productsup\Handler\HandlerInterface (interface)](#interface-productsuphandlerhandlerinterface)
- [\Productsup\Handler\ShellHandler](#class-productsuphandlershellhandler)
- [\Productsup\Handler\TestHandler](#class-productsuphandlertesthandler)

<hr /> 
### Class: \Productsup\Logger

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>array</em> <strong>$handlers=array()</strong>, <em>[\Productsup\LogInfo](#class-productsuploginfo)</em> <strong>$logInfo=null</strong>)</strong> : <em>void</em> |
| public | <strong>addHandler(</strong><em>mixed</em> <strong>$handlerName</strong>, <em>[\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)</em> <strong>$handler</strong>)</strong> : <em>void</em> |
| public | <strong>getHandler(</strong><em>mixed</em> <strong>$handlerName</strong>)</strong> : <em>mixed</em> |
| public | <strong>getName()</strong> : <em>mixed</em> |
| public | <strong>log(</strong><em>mixed</em> <strong>$level</strong>, <em>string</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>null</em><br /><em>Logs with an arbitrary level.</em> |
| public | <strong>message(</strong><em>mixed</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>, <em>mixed</em> <strong>$level=null</strong>)</strong> : <em>void</em> |
| public | <strong>removeHandler(</strong><em>mixed</em> <strong>$handlerName</strong>)</strong> : <em>void</em> |
| public | <strong>setLogInfo(</strong><em>[\Productsup\LogInfo](#class-productsuploginfo)</em> <strong>$logInfo</strong>)</strong> : <em>void</em> |
| public | <strong>setProcessId(</strong><em>mixed</em> <strong>$pid</strong>)</strong> : <em>void</em> |
| public | <strong>setSiteId(</strong><em>mixed</em> <strong>$siteId</strong>)</strong> : <em>void</em> |

*This class extends \Psr\Log\AbstractLogger*

*This class implements \Psr\Log\LoggerInterface*

<hr /> 
### Class: \Productsup\LogInfo

| Visibility | Function |
|:-----------|:---------|

<hr /> 
### Class: \Productsup\Handler\AbstractHandler (abstract)

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string</em> <strong>$minimalLevel=`'debug'`</strong>, <em>mixed</em> <strong>$verbose</strong>)</strong> : <em>void</em> |
| public | <strong>interpolate(</strong><em>mixed</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em><br /><em>Interpolates context values into the message placeholders.</em> |
| public | <strong>prepare(</strong><em>mixed</em> <strong>$level</strong>, <em>mixed</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em> |
| public | <strong>prepareContext(</strong><em>mixed</em> <strong>$context</strong>)</strong> : <em>void</em> |
| public | <strong>setLogger(</strong><em>[\Productsup\Logger](#class-productsuplogger)</em> <strong>$logger</strong>)</strong> : <em>void</em> |
| public | <strong>splitMessage(</strong><em>mixed</em> <strong>$fullMessage</strong>)</strong> : <em>void</em> |

*This class implements [\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)*

<hr /> 
### Class: \Productsup\Handler\GelfHandler

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string</em> <strong>$minimalLevel=`'debug'`</strong>, <em>mixed</em> <strong>$verbose</strong>)</strong> : <em>void</em> |
| public | <strong>write(</strong><em>mixed</em> <strong>$level</strong>, <em>mixed</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em> |

*This class extends [\Productsup\Handler\AbstractHandler](#class-productsuphandlerabstracthandler-abstract)*

*This class implements [\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)*

<hr /> 
### Interface: \Productsup\Handler\HandlerInterface

| Visibility | Function |
|:-----------|:---------|
| public | <strong>abstract write(</strong><em>mixed</em> <strong>$level</strong>, <em>mixed</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em> |

<hr /> 
### Class: \Productsup\Handler\ShellHandler

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string</em> <strong>$minimalLevel=`'debug'`</strong>, <em>mixed</em> <strong>$verbose</strong>)</strong> : <em>void</em> |
| public | <strong>write(</strong><em>mixed</em> <strong>$level</strong>, <em>mixed</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em> |

*This class extends [\Productsup\Handler\AbstractHandler](#class-productsuphandlerabstracthandler-abstract)*

*This class implements [\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)*

<hr /> 
### Class: \Productsup\Handler\TestHandler

| Visibility | Function |
|:-----------|:---------|
| public | <strong>write(</strong><em>mixed</em> <strong>$level</strong>, <em>mixed</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em> |

*This class extends [\Productsup\Handler\AbstractHandler](#class-productsuphandlerabstracthandler-abstract)*

*This class implements [\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)*

