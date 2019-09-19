# ASP Laravel Repository

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/asp/laravel-repository.svg?style=flat-square)](https://packagist.org/packages/asp/laravel-repository)

This is a library created to provide an easy way to develop a Model's CRUD in laravel.<br>
It provides several traits and classes to allow you to create Controllers that handle automaticaly pagination and 
create JSON responses in a standard format. Also, it provides a set of traits to implement what we call Model driven 
Repositories, which allows to have your Models act as data Repositories that can handle filtering, pagination, CRUD 
operations as well as provide an easy way to create your own custom operations and validations.

## Requirements
This package requires:
- PHP __7.0__+
- Laravel __5.1__+ or Lumen __5.1__+

## Install
`composer require asp/laravel-repository`

### Laravel

#### Register Service Provider

Append the following line to the `providers` key in `config/app.php` to register the package:

```php
ASP\Repository\RepositoryServiceProvider::class,
```

***
_The package supports auto-discovery, so if you use Laravel 5.5 or later you may skip registering the service 
provider and facades as they will be registered automatically._
***

#### Publishing resources

To publish the available translations and config to your application, for customization, just run:

```shell
php artisan vendor:publish --tag=repository.translations
php artisan vendor:publish --tag=repository.config
```

## Usage
Write a few lines about the usage of this package.

This documentation assumes some knowledge of how [Fractal](https://github.com/thephpleague/fractal) works.

### Extending ASP\Repository\Base\Controller

The package has a `Controller` class, which implements a middleware that handles pagination on index:

```php
use ASP\Repository\Base\Controller;

class YourController extends Controller
{
}
```

#### Using Model Repository
##### Option 1: Using the provided Repository
To use the Repository you can use it in your model class:
```php
use ASP\Repository\Traits\Repository;

class YourModel extends Model
{
    use Repository;
}
```
This will make available to you several methods:

* `getAllRecords(Filter $filters = null, array $pagination = null)`
* `getRecordById($id)`
* `createRecord(Request $request)`
* `updateRecordById($id, Request $request)`
* `deleteRecordById($id, Request $request)`

##### Option 2: Creating your own Repository
You can also extend the Repository and add your own methods, this also allows you to use Model Validators:

```php
use ASP\Repository\Traits\Repository;
...
trait YourRepository
{
    use Repository;
  
  	
}
```

```php
use ASP\Repository\Traits\Repository;

class YourModel extends Model
{
    use YourRepository, YourModelValidator;
}
```

#### Using Validators

```php
use ASP\Repository\Traits\Validator;

trait YourModelValidator
{
    use Validator;

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::setBaseRules(
            [
                'name' => [
                    'laravel rules here'
                ],
            ]
        );
    }

    /**
     * @return array
     */
    protected static function getCustomRules()
    {
        $rules = self::getBaseRules();

        return array_merge(
            $rules,
            [
              'new rules'
            ]
        );
    }
}
```

#### Using Filters
To filter your Model queries you can extend `ASP\Repository\Filter`:

```php
use ASP\Repository\Filter;

class PetFilters extends Filter
{
  public function <filterName>(<$parameters>)
```

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security-related issues, please email asp-devteam@alter-solutions.com instead of using the issue tracker.

## License
The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.