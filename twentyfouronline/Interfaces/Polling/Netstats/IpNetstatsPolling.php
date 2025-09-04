<?php

namespace twentyfouronline\Interfaces\Polling\Netstats;

interface IpNetstatsPolling
{
    public function pollIpNetstats(array $oids): array;
}




