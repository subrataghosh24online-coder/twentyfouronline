<?php

use twentyfouronline\OS;

if (empty($os) || ! $os instanceof OS) {
    $os = OS::make($device);
}

(new \twentyfouronline\Modules\Routes())->discover($os);




