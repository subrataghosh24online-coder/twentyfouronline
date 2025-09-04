<?php

/*
 * twentyfouronline
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.  Please see LICENSE.txt at the top level of
 * the source code distribution for details.
 *
 * @package    twentyfouronline
 * @link       https://www.twentyfouronline.org
 * @copyright  2017 Thomas GAGNIERE
 * @author     Thomas GAGNIERE <tgagniere@reseau-concept.com>
 */

namespace twentyfouronline\OS;

use twentyfouronline\Device\WirelessSensor;
use twentyfouronline\Interfaces\Discovery\OSDiscovery;
use twentyfouronline\Interfaces\Discovery\Sensors\WirelessClientsDiscovery;
use twentyfouronline\Interfaces\Discovery\Sensors\WirelessFrequencyDiscovery;
use twentyfouronline\Interfaces\Polling\Sensors\WirelessFrequencyPolling;
use twentyfouronline\OS\Shared\Zyxel;

class Zyxelnwa extends Zyxel implements OSDiscovery, WirelessClientsDiscovery, WirelessFrequencyDiscovery, WirelessFrequencyPolling
{
    public function discoverWirelessClients()
    {
        $sensors = [];
        $base_oid = '.1.3.6.1.4.1.890.1.15.3.5.1.1.2.'; // ZYXEL-ES-WIRELESS::wlanStationCount

        foreach ($this->getWlanRadioTable() as $index => $row) {
            $radio = $this->getRadioName($row['ZYXEL-ES-WIRELESS::wlanMode']);
            $sensors[] = new WirelessSensor('clients', $this->getDeviceId(), $base_oid . $index, 'zyxelnwa', $index, $radio, $row['ZYXEL-ES-WIRELESS::wlanStationCount']);
        }

        $total = \SnmpQuery::options(['-OQXUte', '-Pu'])->get('ZYXEL-ES-WIRELESS::wlanTotalStationCount.0')->value();
        if ($total !== '') {
            $sensors[] = new WirelessSensor('clients', $this->getDeviceId(), '.1.3.6.1.4.1.890.1.15.3.5.15.0', 'zyxelnwa', 'total', 'Total', (int) $total);
        }

        return $sensors;
    }

    public function discoverWirelessFrequency()
    {
        $sensors = [];
        $base_oid = '.1.3.6.1.4.1.890.1.15.3.5.1.1.6.'; // ZYXEL-ES-WIRELESS::wlanChannel

        foreach ($this->getWlanRadioTable() as $index => $row) {
            $radio = $this->getRadioName($row['ZYXEL-ES-WIRELESS::wlanMode']);
            $frequency = WirelessSensor::channelToFrequency($row['ZYXEL-ES-WIRELESS::wlanChannel']);
            $sensors[] = new WirelessSensor('frequency', $this->getDeviceId(), $base_oid . $index, 'zyxelnwa', $index, $radio, $frequency);
        }

        return $sensors;
    }

    public function pollWirelessFrequency(array $sensors)
    {
        return $this->pollWirelessChannelAsFrequency($sensors);
    }

    private function getRadioName($value): string
    {
        return match ($value) {
            '1' => '2.4GHz',
            '2' => '5GHz',
            '3' => '6GHz',
            default => 'Unknown',
        };
    }

    private function getWlanRadioTable()
    {
        return \SnmpQuery::options(['-OQXUte', '-Pu']) // ignore underscores
            ->cache()
            ->walk('ZYXEL-ES-WIRELESS::wlanRadioTable')
            ->table(1);
    }
}




