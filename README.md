# Logger

```php
<?php

require_once('vendor/autoload.php');

use Productsup\Flexilog\Logger;
use Productsup\Flexilog\Info;
use Productsup\Flexilog\Handler;

// this is optional
// a Info class allows you to set certain properties that you always
// want to include in your Log output (e.g. Gelf or Shell)
// this is an expanditure to the `$context`.
// You can define specific requiredData for the Info so you can enforce
// certain properties to be available.
$logInfo = new Info\GenericInfo();
$logInfo->setRequiredData(['foo']);
$logInfo->setProperty('foo', 'bar');

// pick a cool handler
$shellHandler = new Handler\ShellHandler('trace', 2);

// set the Handler and the optional $logInfo
$logger = new Logger([$shellHandler], $logInfo);


$logger->notice('Hello World');
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

Or to Graylog: http://yourgrayloginstance.com/messages/graylog2_312/23a5e2b0-24b6-11e5-b0b9-001e67b4d4d0 (example might have some other sample data).


or PSR-3 compatible:

```php
$logger->critical('critical message', $context);
```

Check the [generated API docs](API.md) for more info.

## Symfony Console

```
public function execute(InputInterface $input, OutputInterface $output)
{
    $logger = new \Productsup\Logger(
        array(
            'Console' => new \Productsup\Handler\SymfonyConsoleHandler('debug', 2, $output)
        )
    );

    $logger->message('message');
    $logger->error('errrooorrr');
}
```

Outputs

```
[notice] message
[error] errrooorrr
```

# Caveats
Depending on the Info object in use there might be some reserved keywords. Check the Info object you're using for the list of reserved keywords.
