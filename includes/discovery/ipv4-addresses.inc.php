<?php

use twentyfouronline\OS;

if (empty($os) || ! $os instanceof OS) {
    $os = OS::make($device);
}

(new \twentyfouronline\Modules\Ipv4Addresses())->discover($os);




