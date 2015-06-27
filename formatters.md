---
layout: default
permalink: formatters/
title: Using Formatters
---

#Using Formatters

BooBoo makes use of objects called Formatters to format error messages in a variety of ways. This makes them easy to read during the development process.

## Formatters are very important!

When you're developing, you want to view errors in the browser. In order to do this, you must provide a formatter.
Without a formatter, the system won't intelligently know how to display the errors. As a result, the Runner will throw
an exception and won't register the error handlers.

The library ships with several formatters for your convenience:

* HtmlFormatter - Formats errors just like PHP's error formatting.
* HtmlTableFormatter - Formats errors and exceptions similar to Xdebug, wth a full stack trace, even for errors.
* JsonFormatter - Perfect for displaying errors to an API. (It's recommended to use this in conjunction with blocking errors.)
* CommandLineFormatter - Working with the command line? This will produce pretty command-line errors.
* NullFormatter - This formatter simply silences all errors. You can pass this when display_errors = Off.

Registering a formatter is easy:

~~~ php
$runner->pushFormatter(new League\BooBoo\Formatter\HtmlFormatter);
~~~

## Controlling which formatter does the formatting

There may be times that you want certain formatters to handle the formatting for particular errors, and others to handle
the formatting for other error types. Formatters support this.

For example, if you want all errors of warning or higher to show in the browser, but errors that are below this level
to be ignored, you can configure the formatters to handle this scenario as such:

~~~ php
$html = new League\BooBoo\Formatter\HtmlFormatter;
$null = new League\BooBoo\Formatter\NullFormatter;

$html->setErrorLimit(E_ERROR | E_WARNING | E_USER_ERROR | E_USER_WARNING);
$null->setErrorLimit(E_ALL);

$runner->pushFormatter($null);
$runner->pushFormatter($html);
~~~

## Formatters and handlers are a stack

Formatters and handlers are treated as a stack. This means that the last item in will be the first item out. This is
very important when dealing with formatters that only handle certain errors!

For example, in the example above, we have one formatter limited to errors and warnings, and the other formatting all
error types. If we insert the HTML handler first, it will be run last; this would cause the NullFormatter to format all
errors, and we would get no output.
