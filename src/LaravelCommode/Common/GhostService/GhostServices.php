<?php
    namespace Dubpub\LaravelCommode\Common\GhostService;

    class GhostServices
    {
        private $enabled = [];

        public function register($serviceName)
        {
            if (!$this->isRegistered($serviceName)) {
                $this->enabled[] = $serviceName;
            }
        }

        public function registers(array $serviceNames)
        {
            foreach($serviceNames as $serviceName)
            {
                $this->register($serviceName);
            }
        }

        public function isRegistered($serviceName)
        {
            return in_array($serviceName, $this->getRegistered());
        }

        public function getRegistered()
        {
            return $this->enabled;
        }
    }