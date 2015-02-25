layout: default
permalink: test/
title: Why BooBoo?
---

# Why BooBoo?

BooBoo is designed to help make development easier while providing an integrated solution that can be deployed to
your production environment. BooBoo offers the following advantages.

### Errors are non-blocking by default

Some solutions throw exceptions for all errors, causing every notice to become a fatal error. BooBoo doesn't do this.
Rather than raise an exception for non-fatal errors, we display the error to you in a way that makes sense and lets the
program continue running. An E_NOTICE shouldn't become an exception.

The Runner object offers a method for forcing all errors to be blocking, should you wish to throw exceptions for more minor errors. This is turned off by default.

### BooBoo won't end up in your stack trace

Because we don't throw an exception by default, we don't generate a stack trace for minor errors. This means BooBoo won't show up in your logs, and when it handles an exception generated elsewhere, it isn't appended there, either.

### BooBoo is built for logging

This solution is designed with logging in mind, so that you can plug and play a PSR-3 compliant logging solution in and
go. BooBoo is sensitive enough to log errors, warnings and notices as such; exceptions are logged as critical, and
E_STRICT/E_DEPRECATED warnings are logged as info. Handlers run even if formatting is disabled, so your logging will
always be on, even in production.

### BooBoo is designed for extension

We can't possibly think through all your use cases, but we know you can. That's why we use standard interfaces to make
it easy to extend and enhance the BooBoo functionality. (We also love pull requests; please share your
innovations!)

### BooBoo is actively maintained

PHP is changing every year, and BooBoo will change along with it.