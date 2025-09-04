<?php

/**
 * RutosRutx.php
 *
 * -Description-
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @link       https://www.twentyfouronline.org
 *
 * @author     H. DAY
 */

namespace twentyfouronline\OS;

use twentyfouronline\Device\WirelessSensor;
use twentyfouronline\Interfaces\Discovery\Sensors\WirelessCellDiscovery;
use twentyfouronline\Interfaces\Discovery\Sensors\WirelessRsrpDiscovery;
use twentyfouronline\Interfaces\Discovery\Sensors\WirelessRsrqDiscovery;
use twentyfouronline\Interfaces\Discovery\Sensors\WirelessRssiDiscovery;
use twentyfouronline\Interfaces\Discovery\Sensors\WirelessSinrDiscovery;
use twentyfouronline\OS;

class RutosRutx extends OS implements
    WirelessRssiDiscovery,
    WirelessRsrpDiscovery,
    WirelessRsrqDiscovery,
    WirelessSinrDiscovery,
    WirelessCellDiscovery
{
    public function discoverWirelessRssi(): array
    {
        $data = $this->getCacheTable('TELTONIKA-RUTX-MIB::modemTable');

        $sensors = [];
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'rssi',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.48690.2.2.1.12.' . $index,
                'rutos-rutx',
                $index,
                'Modem ' . ($entry['mIndex'] ?? null) . ' RSSI',
                $entry['mSignal']
            );
        }

        return $sensors;
    }

    public function discoverWirelessRsrp(): array
    {
        $data = $this->getCacheTable('TELTONIKA-RUTX-MIB::modemTable');

        $sensors = [];
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'rsrp',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.48690.2.2.1.20.' . $index,
                'rutos-rutx',
                $index,
                'Modem ' . ($entry['mIndex'] ?? null) . ' RSRP',
                $entry['mRSRP']
            );
        }

        return $sensors;
    }

    public function discoverWirelessRsrq(): array
    {
        $data = $this->getCacheTable('TELTONIKA-RUTX-MIB::modemTable');

        $sensors = [];
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'rsrq',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.48690.2.2.1.21.' . $index,
                'rutos-rutx',
                $index,
                'Modem ' . ($entry['mIndex'] ?? null) . ' RSRQ',
                $entry['mRSRQ']
            );
        }

        return $sensors;
    }

    public function discoverWirelessSinr(): array
    {
        $data = $this->getCacheTable('TELTONIKA-RUTX-MIB::modemTable');

        $sensors = [];
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'sinr',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.48690.2.2.1.19.' . $index,
                'rutos-rutx',
                $index,
                'Modem ' . ($entry['mIndex'] ?? null) . ' SINR',
                $entry['mSINR']
            );
        }

        return $sensors;
    }

    public function discoverWirelessCell(): array
    {
        $data = $this->getCacheTable('TELTONIKA-RUTX-MIB::modemTable');

        $sensors = [];
        foreach ($data as $index => $entry) {
            $sensors[] = new WirelessSensor(
                'cell',
                $this->getDeviceId(),
                '.1.3.6.1.4.1.48690.2.2.1.18.' . $index,
                'rutos-rutx',
                $index,
                'Modem ' . ($entry['mIndex'] ?? null) . ' CELL ID',
                $entry['CELLID'] ?? null
            );
        }

        return $sensors;
    }
}




