<?php

use twentyfouronline\OS;

if (! isset($os) || ! $os instanceof OS) {
    $os = OS::make($device);
}

(new \twentyfouronline\Modules\UcdDiskio())->discover($os);




