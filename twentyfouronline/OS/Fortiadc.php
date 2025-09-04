<?php

namespace twentyfouronline\OS;

use App\Models\Device;
use twentyfouronline\Interfaces\Discovery\OSDiscovery;
use twentyfouronline\OS\Shared\Fortinet;

class Fortiadc extends Fortinet implements OSDiscovery
{
    public function discoverOS(Device $device): void
    {
        parent::discoverOS($device); // yaml

        $device->hardware = $device->hardware ?: $this->getHardwareName();
    }
}




