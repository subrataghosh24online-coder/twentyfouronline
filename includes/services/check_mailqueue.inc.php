<?php

$check_cmd = 'sudo ' . \App\Facades\twentyfouronlineConfig::get('nagios_plugins') . '/check_mailqueue ' . $service['service_param'];




