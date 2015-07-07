# Logger

```
$logInfo = new LogInfo();
$logInfo->site = 397;
$logInfo->process = 'somepid';

$logger = new Logger('foo', 
    array(
        'Shell' => new Handler\ShellHandler($logInfo, 'debug', 2),
        'Gelf' => new Handler\GelfHandler($logInfo)
    )
);
```

```
$context = array(
    'fullMessage' => 'Blablablabla bla blaaaa blaaaa {foo} blaa',
    'foo' => 'bar',
    'exception' => new \Exception('wut', 0, new \Exception('Previous')),
    'someArray' => array('yo, sup', 'nm nm', 'a' => array('foo', 'bar' => 'baz')),
    'date' => new \DateTime()
);
$logger->message('default message', $context);
$logger->message('critical message', $context, 'critical');
```

or PSR-3 compatible:

```
$logger->critical('critical message', $context);
```
