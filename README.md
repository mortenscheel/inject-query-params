# Laravel query param injector

I learned about the [Symfony feature](https://symfony.com/doc/current/controller.html#automatic-mapping-of-the-request) earlier today, and wondered if it could be implemented in Laravel.

This package is a proof of concept. It should definitely not be used in production.

The implementation is not beautiful, and there are probably all kinds of problems with how I've done it. 
Adding extra functionality to Laravel's dependency injection logic from a package can't be done without extending some core framework services,
which is obviously not ideal.

Since Symfony's http kernel is already included in Laravel, I've just used their `QueryParameterValueResolver` to resolve, validate and cast the values.

If you have comments, please post them on this [discussion in the laravel/framework repo](https://github.com/laravel/framework/discussions/52185)

## How it works.

By adding the `Symfony\Component\HttpKernel\Attribute\MapQueryParameter` attribute to arguments in your route actions, 
they will be automatically injected from query params.

## Example
Controller method
```php
public function demo(
    #[MapQueryParameter] Type $enum,
    #[MapQueryParameter] string $string,
    #[MapQueryParameter] int $int,
    #[MapQueryParameter] float $float,
    #[MapQueryParameter] bool $bool,
    #[MapQueryParameter] array $strings,
    #[MapQueryParameter] int $optional = 42,
) {
    dump(compact('enum', 'string', 'int', 'float', 'bool', 'strings', 'optional'));
}
```
URL
```
http://127.0.0.1:8000/?enum=2&string=foo&int=42&float=3.14&bool=0&strings[]=a&strings[]=b
```
Response
```
array:7 [▼
  "enum" => App\Enum\Type {#257 ▼
    +name: "Two"
    +value: 2
  }
  "string" => "foo"
  "int" => 42
  "float" => 3.14
  "bool" => false
  "strings" => array:2 [▼
    0 => "a"
    1 => "b"
  ]
  "optional" => 42
]
```
## Notice
- If a required argument (without a default value) can't be found in the query params, a ValidationException is thrown.
- Types are optional. If no type is hinted, the original string values are injected.

## Installation
I won't publish this package on Packagist, but if you want to try it out, you can install it like this:

Add to composer.json
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mortenscheel/inject-query-params"
        }
    ],
}
```
Then run
```bash
composer require --dev mortenscheel/inject-query-params=dev-master
```
