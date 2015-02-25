---
layout: default
permalink: instantiation/
title: Instantiation
---

#Instantiating BooBoo

The main object that you need to instantiate is Savage\BooBoo\Runner. This object takes care of setting the error
handler, as well as handling errors and exceptions. It takes optional arguments during construction for handlers and
formatters.

```$runner = new League\BooBoo\Runner();
$runner->register(); // Registers the handlers
```

It's very important to call Runner::register() or the object won't register itself as PHP's error handler.

**NOTE:** A formatter is required in order to register the error handler. You can use the included Null handler to ignore all errors.