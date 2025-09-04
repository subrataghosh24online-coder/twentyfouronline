<?php

use twentyfouronline\OS;
use twentyfouronline\OS\Generic;

// start assuming no os
(new \twentyfouronline\Modules\Core())->discover(Generic::make($device));

// then create with actual OS
$os = OS::make($device);




