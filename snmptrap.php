#!/usr/bin/env php
<?php

/**
 * twentyfouronline
 *
 *   This file is part of twentyfouronline.
 *
 * @copyright  (C) 2006 - 2012 Adam Armstrong
 * @copyright  (C) 2018 twentyfouronline
 * Adapted from old snmptrap.php handler
 */

use twentyfouronline\Util\Debug;

$init_modules = [];
require __DIR__ . '/includes/init.php';

$options = getopt('d::');

if (Debug::set(isset($options['d']))) {
    echo "DEBUG!\n";
}

$text = stream_get_contents(STDIN);

// create handle and send it this trap
\twentyfouronline\Snmptrap\Dispatcher::handle(new \twentyfouronline\Snmptrap\Trap($text));




