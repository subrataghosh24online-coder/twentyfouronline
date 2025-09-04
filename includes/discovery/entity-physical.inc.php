<?php

use twentyfouronline\Modules\EntityPhysical;
use twentyfouronline\OS;

if (! isset($os) || ! $os instanceof OS) {
    $os = OS::make($device);
}
(new EntityPhysical())->discover($os);




