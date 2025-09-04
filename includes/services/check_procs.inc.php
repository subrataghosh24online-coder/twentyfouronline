<?php

$check_cmd = \App\Facades\twentyfouronlineConfig::get('nagios_plugins') . '/check_procs ' . $service['service_param'];




