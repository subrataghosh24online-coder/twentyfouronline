<?php

namespace twentyfouronline\Interfaces\Polling\Netstats;

interface UdpNetstatsPolling
{
    public function pollUdpNetstats(array $oids): array;
}




