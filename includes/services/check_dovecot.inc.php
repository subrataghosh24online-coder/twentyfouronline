<?php

$check_cmd = \App\Facades\twentyfouronlineConfig::get('nagios_plugins') . '/check_dovecot ' . $service['service_param'];




