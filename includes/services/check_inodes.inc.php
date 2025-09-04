<?php

$check_cmd = \App\Facades\twentyfouronlineConfig::get('nagios_plugins') . '/check_inodes ' . $service['service_param'];




