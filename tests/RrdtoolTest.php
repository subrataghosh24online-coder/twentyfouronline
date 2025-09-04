<?php

/**
 * RrdtoolTest.php
 *
 * Tests functionality of our rrdtool wrapper
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
 * @copyright  2016 Tony Murray
 * @author     Tony Murray <murraytony@gmail.com>
 */

namespace twentyfouronline\Tests;

use App\Facades\twentyfouronlineConfig;
use twentyfouronline\Data\Store\Rrd;

class RrdtoolTest extends TestCase
{
    public function testBuildCommandLocal(): void
    {
        twentyfouronlineConfig::set('rrdcached', '');
        twentyfouronlineConfig::set('rrdtool_version', '1.4');
        twentyfouronlineConfig::set('rrd_dir', '/opt/twentyfouronline/rrd');

        $cmd = $this->buildCommandProxy('create', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('create /opt/twentyfouronline/rrd/f o', $cmd);

        $cmd = $this->buildCommandProxy('tune', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('tune /opt/twentyfouronline/rrd/f o', $cmd);

        $cmd = $this->buildCommandProxy('update', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('update /opt/twentyfouronline/rrd/f o', $cmd);

        twentyfouronlineConfig::set('rrdtool_version', '1.6');

        $cmd = $this->buildCommandProxy('create', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('create /opt/twentyfouronline/rrd/f o -O', $cmd);

        $cmd = $this->buildCommandProxy('tune', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('tune /opt/twentyfouronline/rrd/f o', $cmd);

        $cmd = $this->buildCommandProxy('update', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('update /opt/twentyfouronline/rrd/f o', $cmd);
    }

    public function testBuildCommandRemote(): void
    {
        twentyfouronlineConfig::set('rrdcached', 'server:42217');
        twentyfouronlineConfig::set('rrdtool_version', '1.4');
        twentyfouronlineConfig::set('rrd_dir', '/opt/twentyfouronline/rrd');

        $cmd = $this->buildCommandProxy('create', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('create /opt/twentyfouronline/rrd/f o', $cmd);

        $cmd = $this->buildCommandProxy('tune', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('tune /opt/twentyfouronline/rrd/f o', $cmd);

        $cmd = $this->buildCommandProxy('update', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('update f o --daemon server:42217', $cmd);

        twentyfouronlineConfig::set('rrdtool_version', '1.6');

        $cmd = $this->buildCommandProxy('create', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('create f o -O --daemon server:42217', $cmd);

        $cmd = $this->buildCommandProxy('tune', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('tune f o --daemon server:42217', $cmd);

        $cmd = $this->buildCommandProxy('update', '/opt/twentyfouronline/rrd/f', 'o');
        $this->assertEquals('update f o --daemon server:42217', $cmd);
    }

    public function testBuildCommandException(): void
    {
        twentyfouronlineConfig::set('rrdcached', '');
        twentyfouronlineConfig::set('rrdtool_version', '1.4');

        $this->expectException('twentyfouronline\Exceptions\FileExistsException');
        // use this file, since it is guaranteed to exist
        $this->buildCommandProxy('create', __FILE__, 'o');
    }

    private function buildCommandProxy($command, $filename, $options)
    {
        $mock = $this->mock(Rrd::class)->makePartial(); // avoid constructor
        // @phpstan-ignore method.protected
        $mock->loadConfig(); // load config every time to clear cached settings

        return $mock->buildCommand($command, $filename, $options);
    }
}




