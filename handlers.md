---
layout: default
permalink: handlers/
title: Using Handers
---

# Handlers

Regardless of whether or not you want to format the error (or even output it to the screen), you may want to handle it
in some way, such as logging it. BooBoo provides a way to handle errors, and provides a built-in PSR-3 compatible
logging handler.

BooBoo also provides a Raven-compatible handler for use with Sentry. 

You can implement the HandlerInterface to create your own handlers. Handlers are run regardless of whether or not
display_errors is true. Unlike formatters, you cannot direct handlers to ignore certain errors; it's assumed that you
can handle this with the services that handlers work through.