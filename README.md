# Shit Happens

## Quality

[![Build Status](https://travis-ci.org/brandonsavage/shithappens.svg?branch=master)](https://travis-ci.org/brandonsavage/shithappens)
[![Code Climate](https://codeclimate.com/repos/5480ea63695680519000027b/badges/2e9b4213f676a17b0f83/gpa.svg)](https://codeclimate.com/repos/5480ea63695680519000027b/feed)
[![Test Coverage](https://codeclimate.com/repos/5480ea63695680519000027b/badges/2e9b4213f676a17b0f83/coverage.svg)](https://codeclimate.com/repos/5480ea63695680519000027b/feed)


## Purpose
An error handler for PHP that allows for the execution of handlers and formatters for viewing and managing errors in
development and production. Because "shit happens."

## The Name

When production goes down, every developer I've ever worked with has exclaimed, "oh shit!" The package derrives its name from that phenomenom.

For the more "stuffy" companies in the world, you can rest assured that ShitHappens will not "expose itself" to your customers. Errors are converted to exceptions but their stack traces aren't shown (unless you write a formatter that shows them), and exceptions are handled with their own stack trace, so you'll never see the package name on the web or even in your logs. 

## Installation

This library requires PHP 5.4 or later.

It is recommended that you install this library using Composer.

```
$ composer require brandonsavage/shithappens
```

ShitHappens is compliant with [PSR-1][], [PSR-2][], [PSR-3][] and [PSR-4][]. If you notice compliance oversights, please
send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-3]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

## Dependencies

ShitHappens relies upon and requires the following dependencies:

* psr/log - A PSR-3 compliant interface for logging.

No other dependencies are required. The maintainer recommends installing [monolog][] for logging.

[monolog]: https://github.com/Seldaek/monolog

## Advantages Over Existing Solutions

ShitHappens is designed to help make development easier while providing an integrated solution that can be deployed to
your production environment. ShitHappens offers the following advantages.

### Errors are non-blocking by default

Some solutions throw exceptions for all errors, causing every notice to become a fatal error. ShitHappens doesn't do this.
Rather than raise an exception for non-fatal errors, we display the error to you in a way that makes sense and lets the
program continue running. An E_NOTICE shouldn't become an exception.

The Rummer object offers a method for forcing all errors to be blocking, should you wish to throw exceptions for more minor errors. This is turned off by default.

### ShitHappens won't end up in your stack trace

Because we don't throw an exception by default, we don't generate a stack trace for minor errors. This means ShitHappens won't show up in your logs, and when it handles an exception generated elsewhere, it isn't appended there, either.

### ShitHappens is built for logging

This solution is designed with logging in mind, so that you can plug and play a PSR-3 compliant logging solution in and
go. ShitHappens is sensitive enough to log errors, warnings and notices as such; exceptions are logged as critical, and
E_STRICT/E_DEPRECATED warnings are logged as info. Handlers run even if formatting is disabled, so your logging will
always be on, even in production.

### ShitHappens is designed for extension

We can't possibly think through all your use cases, but we know you can. That's why we use standard interfaces to make
it easy to extend and enhance the ShitHappens functionality. (We also love pull requests; please share your
innovations!)

## Getting Started

### Instantiation

The main object that you need to instantiate is Savage\ShitHappens\Runner. This object takes care of setting the error
handler, as well as handling errors and exceptions. It takes optional arguments during construction for handlers and
formatters.

```php
<?php

$runner = new Savage\ShitHappens\Runner();
$runner->register(); // Registers the handlers
```

It's very important to call Runner::register() or the object won't register itself as PHP's error handler.

### Formatters are very important!

When you're developing, you want to view errors in the browser. In order to do this, you must provide a formatter.
Without a formatter, the system won't intelligently know how to display the errors. As a result, the Runner will throw
an exception and won't register the error handlers.

The library ships with four formatters for your convenience:

* HtmlFormatter - Formats errors just like PHP's error formatting.
* JsonFormatter - Perfect for displaying errors to an API.
* CommandLineFormatter - Working with the command line? This will produce pretty command-line errors.
* NullFormatter - This formatter simply silences all errors. You can pass this when display_errors = Off.

Adding a formatter is easy:

```php
<?php

$runner->pushFormatter(new Savage\ShitHappens\Formatter\HtmlFormatter);
```

### Controlling which formater does the formatting

There may be times that you want certain formatters to handle the formatting for particular errors, and others to handle
the formatting for other error types. Formatters support this.

For example, if you want all errors of warning or higher to show in the browser, but errors that are below this level
to be ignored, you can configure the formatters to handle this scenario as such:

```php
<?php

$html = new Savage\ShitHappens\Formatter\HtmlFormatter;
$null = new Savage\ShitHappens\Formatter\NullFormatter;

$html->setErrorLimit(E_ERROR | E_WARNING | E_USER_ERROR | E_USER_WARNING);
$null->setErrorLimit(E_ALL);

$runner->pushFormatter($null);
$runner->pushFormatter($html);
```

### Formatters and handlers are a stack

Formatters and handlers are treated as a stack. This means that the last item in will be the first item out. This is
very important when dealing with formatters that only handle certain errors!

For example, in the example above, we have one formatter limited to errors and warnings, and the other formatting all
error types. If we insert the HTML handler first, it will be run last; this would cause the NullFormatter to format all
errors, and we would get no output.

### Handlers

Regardless of whether or not you want to format the error (or even output it to the screen), you may want to handle it
in some way, such as logging it. ShitHappens provides a way to handle errors, and provides a built-in PSR-3 compatible
logging handler.

You can implement the HandlerInterface to create your own handlers. Handlers are run regardless of whether or not
display_errors is true. Unlike formatters, you cannot direct handlers to ignore certain errors; it's assumed that you
can handle this with the services that handlers work through.
