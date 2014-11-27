#Commode: Common

[![Build Status](https://travis-ci.org/laravel-commode/common.svg?branch=master)](https://travis-ci.org/laravel-commode/common)
[![Code Climate](https://d3s6mut3hikguw.cloudfront.net/github/laravel-commode/common/badges/gpa.svg)](https://codeclimate.com/github/laravel-commode/common)
[![Coverage Status](https://coveralls.io/repos/laravel-commode/common/badge.png?branch=master)](https://coveralls.io/r/laravel-commode/common?branch=master)


>**_laravel-commode/common_** is common utils/services package for all commode packages. Some of them are supposed to
use only inside _laravel-commode_ and some of them might be useful for development.

<br />
####Contents

+ <a href="#installing">Installing</a>
+ <a href="#service">GhostService</a>
+ <a href="#resolver">Resolver</a>
+ <a href="#controller">Controller</a>

<hr />

##<a name="service">Installing</a>

You can install ___laravel-commode/common___ using composer:

    "require": {
        "laravel-commode/common": "dev-master"
    }

To enable package you need to register ``LaravelCommode\Common\CommodeCommonServiceProvider`` service provider.
Actually, there are two ways of registering ``CommodeCommonServiceProvider`` - first one is common for all
service providers - you can simply add it into laravel application config providers list:

    <?php
        // ./yourLaravelApplication/app/config/app.php
        return [
            // ... config code
            'providers' => [
                // ... providers
                'LaravelCommode\Common\CommodeCommonServiceProvider'
            ]
        ];

Or you could start using _GhostService_ service providers and just implement one, because every _GhostService_
service provider checks if ``CommodeCommonServiceProvider`` was registered, if it was not _GhostService_
registers it automatically. For example it could be your central application service provider, that would load
dependent service providers:

    <?php namespace MyApp\ServiceProviders;

        use LaravelCommode\Common\GhostService;

        class ApplicationServiceProvider extends GhostService
        {

            // your ghost service code


            public function uses()
            {
                return [
                    'MyApp\ServiceProviders\DomainLogicServiceProvider', // your domain logic service provider,
                    'MyApp\ServiceProviders\DALServiceProvider', // your data access layer service provider,
                    // ... e.t.c.
                ];
            }
        }

And later in your config you could simply add only ``ApplicationServiceProvider`` without any mention of
`CommodeCommonServiceProvider`, `DomainLogicServiceProvider` and others...:

    <?php
        // ./yourLaravelApplication/app/config/app.php
        return [
            // ... config code
            'providers' => [
                // ... providers,
                /**
                /* will load all dependent services returned from uses()
                /* method and CommodeCommonServiceProvider as well
                **/
                'MyApp\ServiceProviders\ApplicationServiceProvider'
            ]
        ];

<hr />

##<a name="service">GhostService</a>

__GhostService__ is a descendant of an ``Illuminate\Support\ServiceProvider`` with some useful features.
To create a ghost service you simply need to extend ``LaravelCommode\Common\GhostService`` class
and implement 2 basic protected methods: ``registering()`` and ``launching()``.

    <?php namespace MyApp\ServiceProviders;

        use LaravelCommode\Common\GhostService;

        class YourServiceProvider extends GhostService
        {
            /**
            *   Will be triggered when the app is booting
            **/
            protected function launching() { }

            /**
            *   Triggered when service is being registered
            **/
            protected function registering() { }
        }


One of the most useful features of _GhostService_ is that it load dependent service providers. It might be
useful when your main app config file is a large headache of when you are developing custom package and you \
don't want to abuse next developer/user with implementation of dependent service providers. For service
automatic loading you can override method ``public function uses()`` that must return array of service
providers' names. If dependent service has been loaded it will ignored in loading chain.

    <?php namespace MyApp\ServiceProviders;

        use LaravelCommode\Common\GhostService;

        class YourServiceProvider extends GhostService
        {
            public function uses()
            {
                return [
                    'MyApp\ServiceProviders\DALServiceProvider',
                    'Illuminate\Hashing\HashServiceProvider',
                    'Vendor\Package\UsefulServiceProvider'
                ];
            }

            /**
            *   Will be triggered when the app is booting
            **/
            protected function launching() { }

            /**
            *   Triggered when service is being registered
            **/
            protected function registering() { }
        }

For those laravel users, who brought a lot of critics into 'Facades in Laravel' topic _GhostService_ providers
resolving method ``protected function with($resolvable, callable $do)``. Method expects ``$resolvable`` argument
to be ``string`` or ``array`` of strings, that would contain bindings that are already registered in IoC
container or can be resolved in runtime and ``$do`` argument to be ``callable``:

    <?php namespace MyApp\ServiceProviders;

        use LaravelCommode\Common\GhostService;

        class YourServiceProvider extends GhostService
        {
            /**
            *   @var Illuminate\View\Factory|null
            */
            protected $viewFactory = null;

            public function uses()
            {
                return [
                    'MyApp\ServiceProviders\DALServiceProvider',
                    'Illuminate\Hashing\HashServiceProvider',
                    'Vendor\Package\UsefulServiceProvider'
                ];
            }

            /**
            *   Will be triggered when the app is booting
            **/
            protected function launching() { }

            private function doSomethingWithHash(\Illuminate\Hashing\BcryptHasher $hash)
            {
                //do something with $hash
            }

            private function doSomethingWithUseful(\Vendor\UsefulPackage\IUsefulBoundInterface $useful)
            {
                //do something with $useful
            }

            /**
            *   Triggered when service is being registered
            **/
            protected function registering()
            {
                $usings = ['hash', \Vendor\UsefulPackage\IUsefulBoundInterface::class, 'view'];

                $this->with($usings, function ($hash, $useful, $view)
                {
                    $this->doSomethingWithHash($hash);
                    $this->doSomethingWithUseful($useful);
                    $this->viewFactory = $view;
                });
            }
        }

If your service provider needs to register a facade or list of facades, you can simply do it by overriding
``protected $aliases = [];`` and assigning it an array where keys are facade names and values are bound
facades' class names:

        protected $aliases = [
            'MyFacade' => \MyVendor\MyPackage\Facades\MyFacade::class
        ];


## <a name="resolver">Resolver</a>

Resolver is a small, but useful class for building something flexible or for something that requires resolving.
It is available through ``CommodeResolver`` facade, or - if you are a facade hater you can find it registered in
IoC container through alias "commode.common.resolver" or can initialize new instance as
``new \LaravelCommode\Common\Resolver\Resolver($laravelApplication)``.

For example, let's say that you have some structure for your security module like ISecurityUser and it's bound
to your configured eloquent auth model.

    <?php namespace App\System\Security\Abstractions;

        interface ISecurityUser
        {
            public function hasPermission($permission);
            public function hasPermissions(array $permissions);
        }
<br />

    <?php namespace App\DAL\Concrete\Eloquent\Models;

        use Illuminate\Database\Eloquent\Model;

        class Account extends Model implements ISecurityUser
        {
            /* your eloquent model code */
        }
<br />

    <?php namespace App\ServiceProviders;

        use LaravelCommode\Common\GhostService\GhostService;
        use MyApp\System\Security\Abstractions\ISecurityUser;

        class ACLServiceProvider extends GhostService
        {
            public function launching() {}

            public function registering()
            {
                $this->app->bind(ISecurityUser::class, function ($app)
                {
                    return app('auth')->user(); // note that returned value might be null
                });
            }
        }

``Resolver`` can resolve closures and class methods or turn them into resolvable closures. Here's an example
of using it.

###Resolver and closures:

    <?php
        use App\System\Security\Abstractions\ISecurityUser;

        $closureThatNeedsToBeResolved = function ($knownParameter1, $knownParameterN, ISecurityUser $needsToBeResolved = null)
        {
            return func_get_args();
        };

        $resolver = new \LaravelCommode\Common\Resolver\Resolver(); // or app('commode.resolver');

        $knownParameter1 = 'Known';
        $knownParameter2 = 'Parameter';

        /**
        *   Resolving closure and running it
        **/
        $result = $resolver->closure($closureThatNeedsToBeResolved, [$knownParameter1, $knownParameter2]);
        $resultClosure = $resolver->makeClosure($closureThatNeedsToBeResolved);

        var_dump(
            $result, $resultClosure($knownParameter1, $knownParameter2),
            $result === $resultClosure($knownParameter1, $knownParameter2)
        );

        // outputs
        //  array (size=3)
        //      0 => string 'Known' (length=5)
        //      1 => string 'Parameter' (length=9)
        //      2 =>  object(MyApp\DAL\Concrete\Eloquent\Models\Account)
        //  array (size=3)
        //      0 => string 'Known' (length=5)
        //      1 => string 'Parameter' (length=9)
        //      2 =>  object(MyApp\DAL\Concrete\Eloquent\Models\Account)
        //  boolean true

###Resolver and class methods:

    <?php
        use App\System\Security\Abstractions\ISecurityUser;

        class SomeClass
        {
            public function methodThatNeedsToBeResolved($knownParameter1, $knownParameterN, ISecurityUser $needsToBeResolved = null)
            {
                return func_get_args();
            }
        }

        $resolver = new \LaravelCommode\Common\Resolver\Resolver(); // or app('commode.resolver');
        $someClass = new SomeClass();

        $knownParameter1 = 'Known';
        $knownParameter2 = 'Parameter';

        $result = $resolver->method($someClass, 'methodThatNeedsToBeResolved', [$knownParameter1, $knownParameter2]);
                    //  or ->method(SomeClass::class, ..., ...) for calling static method or resolving class through
                    //                                          app IOC

        $resultClosure = $resolver->methodToClosure($someClass, 'methodThatNeedsToBeResolved');
                    //  or ->method(SomeClass::class, ..., ...) for calling static method or resolving class through
                    //                                          app IOC

        var_dump(
            $result, $resultClosure($knownParameter1, $knownParameter2),
            $result === $resultClosure($knownParameter1, $knownParameter2)
        );

        // outputs
        //  array (size=3)
        //      0 => string 'Known' (length=5)
        //      1 => string 'Parameter' (length=9)
        //      2 =>  object(MyApp\DAL\Concrete\Eloquent\Models\Account)
        //  array (size=3)
        //      0 => string 'Known' (length=5)
        //      1 => string 'Parameter' (length=9)
        //      2 =>  object(MyApp\DAL\Concrete\Eloquent\Models\Account)
        //  boolean true


## <a name="#controller">Controller</a>

__laravel-commode/common__ provides simple controller that doesn't change default controller that much, but it
provides functionality that often being questioned on StackOverflow and resources like that on Laravel 4.0-4.2.

``LaravelCommode\Common\Controllers\CommodeController`` provides resolvable method calls, can separate ajax
calls into different methods or disallow ajax calls at all.

Methods resolver is enabled by default, but you can disable it by overriding ``protected $resolveMethods``
and setting it to ``protected $resolveMethods = false;``. This functionality is extremely useful with
___laravel-commode/viewmodel___ package installed. Example:

    <?php namespace MyApp\Domain\Areas\Administrator\Controllers;

        use LaravelCommode\Common\Controllers\CommodeController;
        use LaravelCommode\ViewModel\Interfaces\IRequestBag;

        class AccountController extends CommodeController
        {
            /* some controller code */

            protected function returnBack()
            {
                return \Redirect::to(\URL::current())->withInput();
            }

            public function getSearch(IRequestBag $searchParams)
            {
                $collection = $this->accountService->search($searchParams->toArray());
                return \View::make('Administrator::account.search', compact('collection'));
            }

            public function postEdit($id, AccountViewModel $viewModel)
            {
                if (!$viewModel->isValid())
                {
                    return $this->returnBack()->withErrors($viewModel->getValidator());
                }

                if (!$this->accountStrategy->updateFromViewModel($viewMode))
                {
                    return $this->returnBack()->with('error.strategy', $this->accountStrategy->getState());
                }

                return \Redirect::action(__CLASS__.'@getIndex');
            }
        }

Ajax separation calls are disabled by default, but you can enable it by overriding
``protected $separateRequests`` and setting it to ``protected $separateRequests = true;``. To define an ajax
method simply adding 'ajax_' prefix to your method name. Example:

    <?php namespace MyApp\Domain\Areas\Site\Controllers;

        use LaravelCommode\ViewModel\Interfaces\IRequestBag;

        class PostsController extends CommodeController
        {
            protected $separateRequests = true;

            /* some controller code */

            /**
            *   Returns JSONable results when ajax is triggered
            **/
            public function ajax_getLatest($lastId = null)
            {
                return $this->postService->getLatest($lastId);
            }

            /**
            *   Returns same collection as ::ajax_getLatest() method,
            *   but wraps it with view
            **/
            public function getLatest()
            {
                return \View::make('Site::posts.list', [
                    'posts' => $this->ajax_getLatest()
                ]);
            }
        }

You can disallow ajax calls by overriding ``protected $allowAjax`` and  setting it to
``protected $allowAjax = false;`` - would return  404 http status code.