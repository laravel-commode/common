#Commode: Common

**_laravel-commode/common_** is common utils/services package for all commode packages. Some of them are supposed to
use only inside _laravel-commode_ and some of them might be useful for development.

<br />
####Contents

+ <a href="#service">GhostService</a>
+ <a href="#resolver">Resolver</a>
+ <a href="#controller">Controller</a>

##<a name="service">GhostService</a>

__GhostService__ is a wrapper for <code>Illuminate\Support\ServiceProvider</code> with some useful features.
To create a ghost service you simply need to extend <code>LaravelCommode\Common\GhostService</code> class
and implement 2 methods: <code>LaravelCommode\Common\GhostService:registering()</code> and
<code>LaravelCommode\Common\GhostService::launching()</code>.<br /><br />

    <?php namespace MyApp\ServiceProviders;

        use LaravelCommode\Common\GhostService;

        class YourServiceProvider extends GhostService
        {
            /**
            *   Will be triggered when the app is booting
            **/
            public function launching() { }

            /**
            *   Triggered when service is being registered
            **/
            public function registering() { }
        }

<br />
_GhostService_ can load dependent service providers. It might be useful when your main app config file is a
large headache of when you are developing custom package and you don't want to abuse next developer/user
with implementation of dependent service providers. For service automatic loading you can override method
<code>LaravelCommode\Common\GhostService::uses()</code> that must return array of service providers. If dependent
service has been loaded it won't be loaded again.

    <?php namespace MyApp\ServiceProviders;

        use LaravelCommode\Common\GhostService;

        class YourServiceProvider extends GhostService
        {
            public function uses()
            {
                return [
                    'Illuminate\Hashing\HashServiceProvider',
                    Vendor\Package\UsefulServiceProvider::class // <- comfortable refactoring
                ];
            }

            /**
            *   Will be triggered when the app is booting
            **/
            public function launching() { }

            /**
            *   Triggered when service is being registered
            **/
            public function registering() { }
        }

For those laravel users, who brought a lot of critics into 'Facades in Laravel' topic _GhostService_ providers
resolving method <code>LaravelCommode\Common\GhostService::with($resolvable, callable $do)</code>. Method expects
<code>$resolvable</code> argument to be string or array of strings, that would contain binding that are already
registered in IoC container or can be resolved in runtime:

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
                    'Illuminate\Hashing\HashServiceProvider',
                    Vendor\UsefulPackage\UsefulServiceProvider::class // <- comfortable refactoring
                ];
            }

            /**
            *   Will be triggered when the app is booting
            **/
            public function launching()
            {

            }

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
            public function registering()
            {
                $usings = ['hash', \Vendor\UsefulPackage\IUsefulBoundInterface::class, 'view'];

                $this->with($usings, function ($hash, $useful, $view)
                {
                    $this->doSomethingWithHash($hash);
                    $this->doSomethingWithUseul($useful);
                    $this->viewFactory = $view;
                });
            }
        }


## <a name="resolver">Resolver</a>

Resolver is a small, but useful class for building something flexible or for something that requires resolving.
Can be instantiated as <code>new \LaravelCommode\Common\Resolver\Resolver()</code> or can be grabbed from
application IoC container <code>app('commode.resolver')</code>.

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

<code>Resolver</code> can resolve closures and class methods or turn them into resolvable closures. Here's an example
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

_laravel-commode/common_ provides simple controller that doesn't change default controller that much, but it provides
functionality that often being questioned on StackOverflow and resources like that on Laravel 4.0-4.2.

<code>LaravelCommode\Common\Controllers\CommodeController</code> provides resolvable method calls and can separate ajax
calls into different methods or disallow ajax calls at all.

Methods resolver is enabled by default, but you can disable is by overriding <code>protected $resolveMethods</code>
and setting it to <code>protected $resolveMethods = false;</code>. This functionality is extremely useful with
__laravel-commode/viewmodel__ package installed. Example:

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

Ajax separation calls are disable by default, but you can enable it by overriding
<code>protected $separateRequests</code> and setting it to <code>protected $separateRequests = true;</code>. To
define an ajax method simply adding 'ajax_' prefix to your method name. Example:

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
                return \View::make('Site::posts.latest', [
                    'posts' => $this->ajax_getLatest()
                ]);
            }
        }

You can disallow ajax calls by overriding <code>protected $allowAjax</code>and  setting it to
<code>protected $allowAjax = false;</code>.
