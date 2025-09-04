<?php

/**
 * DatastoreTest.php
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
 * @copyright  2018 Tony Murray
 * @author     Tony Murray <murraytony@gmail.com>
 */

namespace twentyfouronline\Tests\Unit\Data;

use App\Facades\twentyfouronlineConfig;
use twentyfouronline\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('datastores')]
class DatastoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        twentyfouronlineConfig::forget([
            'graphite',
            'influxdb',
            'influxdbv2',
            'kafka',
            'opentsdb',
            'prometheus',
            'rrd',
        ]);
    }

    public function testDefaultInitialization(): void
    {
        $ds = $this->app->make('Datastore');
        $stores = $ds->getStores();
        $this->assertCount(1, $stores, 'Incorrect number of default stores enabled');

        $this->assertEquals('twentyfouronline\Data\Store\Rrd', get_class($stores[0]), 'The default enabled store should be Rrd');
    }

    public function testInitialization(): void
    {
        twentyfouronlineConfig::set('rrd.enable', false);
        twentyfouronlineConfig::set('graphite.enable', true);
        twentyfouronlineConfig::set('influxdb.enable', true);
        twentyfouronlineConfig::set('influxdbv2.enable', true);
        twentyfouronlineConfig::set('opentsdb.enable', true);
        twentyfouronlineConfig::set('prometheus.enable', true);
        twentyfouronlineConfig::set('kafka.enable', false);

        $ds = $this->app->make('Datastore');
        $stores = $ds->getStores();
        $this->assertCount(5, $stores, 'Incorrect number of default stores enabled');

        $enabled = array_map('get_class', $stores);

        $expected_enabled = [
            'twentyfouronline\Data\Store\Graphite',
            'twentyfouronline\Data\Store\InfluxDB',
            'twentyfouronline\Data\Store\InfluxDBv2',
            'twentyfouronline\Data\Store\OpenTSDB',
            'twentyfouronline\Data\Store\Prometheus',
        ];

        $this->assertEquals($expected_enabled, $enabled, 'Expected all non-default stores to be initialized');
    }
}




