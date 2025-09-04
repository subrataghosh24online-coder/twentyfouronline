<?php

$check_cmd = 'sudo ' . \App\Facades\twentyfouronlineConfig::get('nagios_plugins') . '/check_postfix ' . $service['service_param'];




