## Table of contents

- [\Productsup\Logger](#class-productsuplogger)
- [\Productsup\LogInfo](#class-productsuploginfo)
- [\Productsup\Handler\AbstractHandler (abstract)](#class-productsuphandlerabstracthandler-abstract)
- [\Productsup\Handler\FileHandler](#class-productsuphandlerfilehandler)
- [\Productsup\Handler\GelfHandler](#class-productsuphandlergelfhandler)
- [\Productsup\Handler\HandlerInterface (interface)](#interface-productsuphandlerhandlerinterface)
- [\Productsup\Handler\RedisHandler](#class-productsuphandlerredishandler)
- [\Productsup\Handler\ShellHandler](#class-productsuphandlershellhandler)
- [\Productsup\Handler\TestHandler](#class-productsuphandlertesthandler)

<hr /> 
### Class: \Productsup\Logger

> A PSR-3 compatible Logger that uses Handlers to output to multiple resources at the same time.

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>array</em> <strong>$handlers=array()</strong>, <em>[\Productsup\LogInfo](#class-productsuploginfo)</em> <strong>$logInfo=null</strong>)</strong> : <em>void</em><br /><em>Initialise a new Logger with specific Handlers. If no Handler is defined a default one will be initialized (Handler\GelfHandler) and the object is an initialized Handler Interface</em> |
| public | <strong>addHandler(</strong><em>string</em> <strong>$handlerName</strong>, <em>[\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)</em> <strong>$handler</strong>)</strong> : <em>Logger $this</em><br /><em>Add a new Handler to the Logger</em> |
| public | <strong>getHandler(</strong><em>string</em> <strong>$handlerName</strong>)</strong> : <em>Handler\HandlerInterface $handler Handler Interface</em><br /><em>Get a Handler by name</em> |
| public | <strong>log(</strong><em>mixed</em> <strong>$level</strong>, <em>string</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>null</em><br /><em>Logs with an arbitrary level.</em> |
| public | <strong>message(</strong><em>string</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>, <em>mixed</em> <strong>$level=null</strong>)</strong> : <em>null</em><br /><em>Logs with an arbitrary level. Convenience method, if no level is provided, Psr\Log\LogLevel::NOTICE will be used.</em> |
| public | <strong>removeHandler(</strong><em>string</em> <strong>$handlerName</strong>)</strong> : <em>Logger $this</em><br /><em>Remove a Handler from the Logger</em> |
| public | <strong>setLogInfo(</strong><em>[\Productsup\LogInfo](#class-productsuploginfo)</em> <strong>$logInfo</strong>)</strong> : <em>Logger $this</em> |
| public | <strong>setProcessId(</strong><em>string</em> <strong>$pid</strong>)</strong> : <em>Logger $this</em><br /><em>Set the Process ID for the LogInfo</em> |
| public | <strong>setSiteId(</strong><em>\Productsup\integer</em> <strong>$siteId</strong>)</strong> : <em>Logger $this</em><br /><em>Set the Site ID for the LogInfo</em> |

*This class extends \Psr\Log\AbstractLogger*

*This class implements \Psr\Log\LoggerInterface*

<hr /> 
### Class: \Productsup\LogInfo

> Log information that could be required during the output

| Visibility | Function |
|:-----------|:---------|

<hr /> 
### Class: \Productsup\Handler\AbstractHandler (abstract)

> Abstract Handler to simplify the implementation of a Handler Interface

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string/\Psr\LogLevel</em> <strong>$minimalLevel=`'debug'`</strong>, <em>\Productsup\Handler\integer</em> <strong>$verbose</strong>)</strong> : <em>void</em><br /><em>Initialize the Handler, optionally with a minimal logging level</em> |
| public | <strong>interpolate(</strong><em>string</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>string $message Message with Placeholders replaced by the context.</em><br /><em>Interpolates context values into the message placeholders.</em> |
| public | <strong>prepare(</strong><em>\Psr\LogLevel</em> <strong>$level</strong>, <em>string</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>array {</em><br /><em>Prepare the Log Message before writing</em> |
| public | <strong>prepareContext(</strong><em>array</em> <strong>$context</strong>)</strong> : <em>array $conext Cleaned context</em><br /><em>Prepare the Context before interpolation Turns Objects into String representations.</em> |
| public | <strong>process(</strong><em>\Psr\LogLevel</em> <strong>$level</strong>, <em>string</em> <strong>$message</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>null</em><br /><em>Process the Logged message</em> |
| public | <strong>setLogger(</strong><em>[\Productsup\Logger](#class-productsuplogger)</em> <strong>$logger</strong>)</strong> : <em>HandlerInterface $this</em><br /><em>Set the Logger for the Handler</em> |
| public | <strong>splitMessage(</strong><em>string</em> <strong>$fullMessage</strong>, <em>mixed/\Productsup\Handler\integer</em> <strong>$size=220000</strong>)</strong> : <em>array $splitFullMessage</em><br /><em>Split the Full Message into chunks before writing it to the Logger</em> |

*This class implements [\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)*

<hr /> 
### Class: \Productsup\Handler\FileHandler

> Write to a specified File

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string</em> <strong>$filename=`'log.log'`</strong>, <em>string</em> <strong>$minimalLevel=`'debug'`</strong>, <em>mixed</em> <strong>$verbose</strong>)</strong> : <em>void</em> |
| public | <strong>write(</strong><em>mixed</em> <strong>$level</strong>, <em>mixed</em> <strong>$message</strong>, <em>mixed</em> <strong>$splitFullMessage</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em> |
| public | <strong>writeToFile(</strong><em>mixed</em> <strong>$line</strong>)</strong> : <em>void</em> |

*This class extends [\Productsup\Handler\AbstractHandler](#class-productsuphandlerabstracthandler-abstract)*

*This class implements [\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)*

<hr /> 
### Class: \Productsup\Handler\GelfHandler

> Ouput to a Graylog server

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string</em> <strong>$minimalLevel=`'debug'`</strong>, <em>mixed</em> <strong>$verbose</strong>)</strong> : <em>void</em> |
| public | <strong>write(</strong><em>mixed</em> <strong>$level</strong>, <em>mixed</em> <strong>$message</strong>, <em>mixed</em> <strong>$splitFullMessage</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em> |

*This class extends [\Productsup\Handler\AbstractHandler](#class-productsuphandlerabstracthandler-abstract)*

*This class implements [\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)*

<hr /> 
### Interface: \Productsup\Handler\HandlerInterface

| Visibility | Function |
|:-----------|:---------|
| public | <strong>abstract write(</strong><em>\Psr\LogLevel</em> <strong>$level</strong>, <em>string</em> <strong>$message</strong>, <em>string</em> <strong>$splitFullMessage</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em><br /><em>Write received Log information through the Handlers mechanism</em> |

<hr /> 
### Class: \Productsup\Handler\RedisHandler

> Publish to a Redis channel

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>array</em> <strong>$redisConfig=array()</strong>, <em>string</em> <strong>$minimalLevel=`'debug'`</strong>, <em>mixed</em> <strong>$verbose</strong>)</strong> : <em>void</em> |
| public | <strong>publishLine(</strong><em>mixed</em> <strong>$channelName</strong>, <em>mixed</em> <strong>$lineValue</strong>)</strong> : <em>void</em> |
| public | <strong>write(</strong><em>mixed</em> <strong>$level</strong>, <em>mixed</em> <strong>$message</strong>, <em>mixed</em> <strong>$splitFullMessage</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em> |

*This class extends [\Productsup\Handler\AbstractHandler](#class-productsuphandlerabstracthandler-abstract)*

*This class implements [\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)*

<hr /> 
### Class: \Productsup\Handler\ShellHandler

> Write to the Shell/Bash STDERR output using multi-color

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>string</em> <strong>$minimalLevel=`'debug'`</strong>, <em>mixed</em> <strong>$verbose</strong>)</strong> : <em>void</em> |
| public | <strong>outputVerbose(</strong><em>mixed</em> <strong>$fullMessage</strong>, <em>mixed</em> <strong>$context</strong>)</strong> : <em>void</em> |
| public | <strong>write(</strong><em>mixed</em> <strong>$level</strong>, <em>mixed</em> <strong>$message</strong>, <em>mixed</em> <strong>$splitFullMessage</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em> |

*This class extends [\Productsup\Handler\AbstractHandler](#class-productsuphandlerabstracthandler-abstract)*

*This class implements [\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)*

<hr /> 
### Class: \Productsup\Handler\TestHandler

> Output to an internal array for PSR-3 compatibility testing

| Visibility | Function |
|:-----------|:---------|
| public | <strong>write(</strong><em>mixed</em> <strong>$level</strong>, <em>mixed</em> <strong>$message</strong>, <em>mixed</em> <strong>$splitFullMessage</strong>, <em>array</em> <strong>$context=array()</strong>)</strong> : <em>void</em> |

*This class extends [\Productsup\Handler\AbstractHandler](#class-productsuphandlerabstracthandler-abstract)*

*This class implements [\Productsup\Handler\HandlerInterface](#interface-productsuphandlerhandlerinterface)*

