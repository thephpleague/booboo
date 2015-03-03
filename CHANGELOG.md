# BooBoo Changelog

## Future
* Added a CallableHandler and CallableFormatter for infinite extension options.
* Added functionality that will make a pretty page handler easier in the future.
* Documentation, test and code cleanup.

## 1.0.0
* Changing the namespace to the final namespace (League) and tagging a 1.0 release.

## 0.4.2
* Added E_PARSE to list of fatal errors, since it is one.
* Moved the ini_set() call to the register function, so that we don't silence errors until we register BooBoo.

## 0.4.1
* Added a handler for passing messages to Sentry with Raven.
* Fixed bug that caused exceptions to be thrown in shutdown function.
* Unknown errors (errors that don't match a predefined constant) are now considered "Unknown Errors".
* Moved Raven to a dev dependency, and suggested it for installation in Composer.

## 0.4.0
* Changed text of error message from "Error" to "Fatal Error".
* Added handling for E_PARSE errors.
* Set display_errors = Off if they are on, to ensure that fatal errors are handled by BooBoo.
* Documentation improvements.

## 0.3.0
* Added a shutdown handler to deal with fatal conditions.

## 0.2.0

* Changed project name to BooBoo.
* Adding support for nested exceptions.
* Restoring support for PHP 5.4.
* Included new formatter for generating Xdebug-style tables for exceptions and errors, including error stack traces.
* Internal changes for stack trace frame evaluation (Thanks to filp/whoops)

## 0.1.0

* Adding initial runner.
* Adding initial formatters.
* Adding PSR-3 compliant logging component.