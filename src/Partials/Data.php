<?php

namespace SeBuDesign\BuckarooJson\Partials;

use stdClass;

trait Data
{
    /**
     * @var stdClass
     */
    public $oData;

    /**
     * Dynamically set and get values
     *
     * @param string $sName      The name of the called function
     * @param array $aArguments The arguments passed to the function
     *
     * @return $this|mixed
     */
    public function __call(string $sName, array $aArguments)
    {
        $this->ensureDataObject();

        // Check if the method exists in the class
        if (!method_exists($this, $sName)) {
            if (strpos($sName, 'set') === 0 && count($aArguments) == 1) {
                // With a method that starts with set, set the data

                $sName = str_replace('set', '', $sName);
                $this->oData->{$sName} = $aArguments[ 0 ];

                return $this;
            } elseif (strpos($sName, 'get') === 0) {
                // With a method that starts with get, get the data

                $sName = str_replace('get', '', $sName);

                return ($this->oData->{$sName} ?? false);
            }
        }
    }

    /**
     * Create the data object if it does not exists
     */
    protected function ensureDataObject()
    {
        if (!isset($this->oData)) {
            $this->oData = new stdClass();
        }
    }

    /**
     * Generate the JSON from the data
     *
     * @return string
     */
    public function __toString()
    {
        $sBody = get_object_vars($this->oData);
        foreach ($sBody as $sName => $mValue) {
            if (is_object($mValue)) {
                if (method_exists($this, "toString{$sName}Modifier")) {
                    $sBody[ $sName ] = $this->{"toString{$sName}Modifier"}();
                }
                if (!method_exists($this, "toString{$sName}Modifier")) {
                    $sBody[ $sName ] = json_decode((string) $mValue, true);
                }
            }
        }

        return json_encode($sBody);
    }
}
