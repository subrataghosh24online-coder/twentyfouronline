<?php

use twentyfouronline\OS;

if (! $os instanceof OS) {
    $os = OS::make($device);
}
(new \twentyfouronline\Modules\PortsStack())->discover($os);




