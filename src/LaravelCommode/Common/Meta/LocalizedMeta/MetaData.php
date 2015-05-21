<?php

namespace LaravelCommode\Common\Meta\LocalizedMeta;

abstract class MetaData
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $lookUpLocation = 'validation.attributes';

    public function __construct($locale = '_', $lookUpLocation = 'validation.attributes')
    {
        $this->setLocale($locale === '_' ? \App::getLocale() : $locale);
        $this->setLookUpLocation($lookUpLocation);
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function __get($field)
    {
        $requiredField = $this->getLocale()."_".$field;

        if (property_exists($this, $requiredField)) {
            return $this->{$requiredField};
        } else {
            $fieldKey = $this->getLookUpLocation().'.'.$field;
            if (($res = trans($fieldKey)) !== $fieldKey) {
                return $res;
            }
        }

        return $field;
    }

    /**
     * @return string
     */
    public function getLookUpLocation()
    {
        return $this->lookUpLocation;
    }

    /**
     * @param string $lookUpLocation
     */
    public function setLookUpLocation($lookUpLocation)
    {
        $this->lookUpLocation = $lookUpLocation;
    }
}
