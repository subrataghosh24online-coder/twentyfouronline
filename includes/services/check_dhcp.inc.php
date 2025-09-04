<?php

$check_cmd = \App\Facades\twentyfouronlineConfig::get('nagios_plugins') . '/check_dhcp ' . $service['service_param'];




