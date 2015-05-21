<?php
namespace LaravelCommode\Common\GhostService;

/**
 * Class GhostServices
 *
 * GhostServices manager. Simple container for storing or looking
 * up all registered service providers from GhostService::uses().
 *
 * @author Volynov Andrey
 * @package LaravelCommode\Common\GhostService
 */
class GhostServices
{
    /**
     * Contains registered services
     * @var array|string[]
     */
    private $enabled = [];

    /**
     * Marks service provider as launched.
     *
     * @param string $serviceName Service provider classname
     */
    public function register($serviceName)
    {
        if (!$this->isRegistered($serviceName)) {
            $this->enabled[] = $serviceName;
        }
    }

    /**
     * Marks service providers as launched.
     *
     * @param string[]|array $serviceNames Array of service providers' classnames
     */
    public function registers(array $serviceNames)
    {
        foreach ($serviceNames as $serviceName) {
            $this->register($serviceName);
        }
    }

    /**
     * Determins if service provider has been already
     * registered.
     *
     * @param string $serviceName Service provides classname.
     * @return bool
     */
    public function isRegistered($serviceName)
    {
        return in_array($serviceName, $this->getRegistered());
    }

    /**
     * Filters unique(unregistered) service provider classnames.
     * Unique service provider classnames can be registered
     * automatically if $autoRegister equals true
     * (false value is the default).
     *
     * @param string[] $serviceNames Array of service provider names
     * @param bool $autoRegister Defines if unique service
     * provider names must be registered automatically
     *
     * @return array
     */
    public function differUnique(array $serviceNames, $autoRegister = false)
    {
        $serviceNames = array_diff($serviceNames, $this->getRegistered());

        if ($autoRegister) {
            $this->registers($serviceNames);
        }

        return $serviceNames;
    }

    /**
     * Returns array of service providers' classnames that
     * are already registered by manager.
     *
     * @return array|string[]
     */
    public function getRegistered()
    {
        return $this->enabled;
    }
}
