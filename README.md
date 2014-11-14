#Commode: Common

_laravel-commode/common_ is common utils/services package for all commode packages. Some of them are supposed to
use only inside _laravel-commode/common_ and some of them might be useful for development.

<br />
##GhostService

_GhostService_ is a wrapper for <code>Illuminate\Support\ServiceProvider</code> with some useful features.
To create a ghost service you simply need to extend <code>LaravelCommode\Common\GhostService</code> class
and implement 2 methods: <code>LaravelCommode\Common\GhostService:registering()</code> and
<code>LaravelCommode\Common\GhostService::launching()</code>.<br /><br />

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


    class YourServiceProvider extends \LaravelCommode\Common\GhostService\GhostService
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


##Resolver

Resolver is a small, but useful class for building something flexible or for something that requires resolving.
Can be instantiated as <code>new \LaravelCommode\Common\Resolver\Resolver()</code> or can be grabbed from
application IoC container <code>app('commode.resolver')</code>.

For example, let's say that you have some structure for your security module like ISecurityUser and it's bound
to your configured eloquent auth model.

        namespace App\Security\Abstracts;

        interface ISecurityUser
        {
            public function hasPermission($permission);
            public function hasPermissions(array $permissions);
        }
<br />

        namespace App\ServiceProviders;

        class ACLServiceProvider extends GhostService
        {
            public function launching() {}

            public function registering()
            {
                $this->app->bind(\App\Security\Abstracts\ISecurityUser::class, function ($app)
                {
                    return app('auth')->user();
                });
            }
        }



