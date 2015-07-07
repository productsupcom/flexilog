# Logger

```php
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

```php
$context = array(
    'fullMessage' => 'Blablablabla bla blaaaa blaaaa {foo} blaa',
    'foo' => 'bar',
    //'exception' => new \Exception('wut', 0, new \Exception('Previous')),
    'someArray' => array('yo, sup', 'nm nm', 'a' => array('foo', 'bar' => 'baz')),
    'date' => new \DateTime()
);
$logger->message('default message', $context);
$logger->message('critical message', $context, 'critical');
```

The above will output to the Shell:

```
NOTICE: default message
Full Message: Blablablabla bla blaaaa blaaaa bar blaa
Extra Variables: 
	foo: bar
	someArray: {"0":"yo, sup","1":"nm nm","a":{"0":"foo","bar":"baz"}}
	site: 397
	process: somepid
	_date: 2015-07-07T16:39:55+02:00

CRITICAL: critical message
Full Message: Blablablabla bla blaaaa blaaaa bar blaa
Extra Variables: 
	foo: bar
	someArray: {"0":"yo, sup","1":"nm nm","a":{"0":"foo","bar":"baz"}}
	site: 397
	process: somepid
	_date: 2015-07-07T16:39:55+02:00
```

Or to Graylog: http://***REMOVED***:9000/messages/graylog2_312/23a5e2b0-24b6-11e5-b0b9-001e67b4d4d0 (example might have some other sample data).


or PSR-3 compatible:

```php
$logger->critical('critical message', $context);
```
