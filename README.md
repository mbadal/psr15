# PSR 15
Lightweight implementation of PSR-15 using Chain of Responsibility pattern.

Library consists of 2 abstract classes:
* `AbstractMiddlewareChainItem` - Abstract base class for Middleware chain item
    * Middleware chain item can be prepended, or appended
    * Chain can be created via `MiddlewareChainFactory`
* `AbstractRequestHandler` - Abstract base class for Request Handler
    * can be created from `callable`

**Disclaimer**: highly inspired by package https://github.com/noglitchyo/middleware-collection-request-handler
