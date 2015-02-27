---
layout: default
permalink: dependencies/
title: Dependencies
---

## Dependencies

BooBoo relies upon and requires the following dependencies:

* psr/log - A PSR-3 compliant interface for logging.

No other dependencies are required. The PSR-3 dependency includes an interface only. The maintainer recommends installing [monolog][] for logging.

[monolog]: https://github.com/Seldaek/monolog

In addition, certain handlers may require additional dependencies. Inclusion of these dependencies is optional, and you may elect to include them only if you need them.