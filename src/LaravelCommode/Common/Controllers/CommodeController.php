<?php
namespace LaravelCommode\Common\Controllers;

use Illuminate\Routing\Controller;
use LaravelCommode\Common\Constants\ServiceShortCuts;

/**
 * Class CommodeController
 *
 * CommodeController is a laravel controller wrapper with some extended possibilities.
 * It can resolve methods if protected $resolveMethods = true (true is the default value)
 * and separate ajax calls like calling ajaxgetIndex() if method
 * getIndex() exists, request is ajax and protected $separateRequests = true (false is the default value)
 *
 * @author Volynov Andrey
 * @package LaravelCommode\Common\Controllers
 */
class CommodeController extends Controller
{
    /**
     * Determines if called methods need to be resolved.
     * @var bool
     */
    protected $resolveMethods = true;

    /**
     * Determines if all ajax methods are prefixed with 'ajax'
     * @var bool
     */
    protected $separateRequests = false;

    /**
     * Determines resolving is enabled. If it's enabled then
     * it returns method resolved parameters, otherwise it
     * returns parameters as they are.
     *
     * @param string $method Method name.
     * @param array $params Array of known parameters.
     * @return mixed
     */
    private function checkParametersResolving($method, array $params = [])
    {
        if (!$this->resolveMethods) {
            return $params;
        }

        return app()->make(ServiceShortCuts::RESOLVER_SERVICE)->resolveMethodParameters($this, $method, $params);
    }

    /**
     * Determines ajax separation is enabled. If it's enabled then
     * it returns method's name prefixed with 'ajax', otherwise it
     * returns method's name as it is.
     *
     * @param string $method Method name.
     * @param bool|null $isAjax Determines if request is ajax.
     * @return string
     */
    private function checkAjaxMethod($method, $isAjax = null)
    {
        if ($this->separateRequests && $isAjax) {
            return 'ajax'.$method;
        }

        return $method;
    }

    /**
     * Calls controller action.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function callAction($method, array $parameters = [])
    {
        $isAjax = app()->make('request')->ajax();

        $method = $this->checkAjaxMethod($method, $isAjax);
        $parameters = $this->checkParametersResolving($method, $parameters);

        return parent::callAction($method, $parameters);
    }
}
