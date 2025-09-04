<?php

use twentyfouronline\OS;

if (! $os instanceof OS) {
    $os = OS::make($device);
}
(new \twentyfouronline\Modules\Ospfv3())->poll($os, app('Datastore'));




