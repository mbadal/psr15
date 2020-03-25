# PSR 15
Lightweight implementation of PSR-15 using Chain of Responsibility pattern.

## Table of contents
Library consists of 2 abstract classes:
* [AbstractMiddlewareChainItem](https://github.com/mbadal/psr15/blob/master/src/Psr15/Middleware/AbstractMiddlewareChainItem.php) - Abstract base class for Middleware chain item
    * Middleware chain item can be prepended, or appended
    * Chain can be created via [MiddlewareChainFactory](https://github.com/mbadal/psr15/blob/master/src/Psr15/Middleware/Factory/MiddlewareChainFactory.php)
* [AbstractRequestHandler](https://github.com/mbadal/psr15/blob/master/src/Psr15/RequestHandler/AbstractRequestHandler.php) - Abstract base class for Request Handler
    * can be created from `callable`

## Installation
```
composer require delvesoft/psr15
```

**Disclaimer**: highly inspired by package https://github.com/noglitchyo/middleware-collection-request-handler
