<?php

/**
 * ConfigTest.php
 *
 * Tests for App\Facades\Config
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
 * @copyright  2017 Tony Murray
 * @author     Tony Murray <murraytony@gmail.com>
 */

namespace twentyfouronline\Tests;

use App\ConfigRepository;
use App\Facades\twentyfouronlineConfig;

class ConfigTest extends TestCase
{
    private \ReflectionProperty $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new \ReflectionProperty(ConfigRepository::class, 'config');
    }

    public function testGetBasic(): void
    {
        $dir = realpath(__DIR__ . '/..');
        $this->assertEquals($dir, twentyfouronlineConfig::get('install_dir'));
    }

    public function testSetBasic(): void
    {
        $instance = $this->app->make('twentyfouronline-config');
        twentyfouronlineConfig::set('basics', 'first');
        $this->assertEquals('first', $this->config->getValue($instance)['basics']);
    }

    public function testGet(): void
    {
        $this->setConfig(function (&$config) {
            $config['one']['two']['three'] = 'easy';
        });

        $this->assertEquals('easy', twentyfouronlineConfig::get('one.two.three'));
    }

    public function testGetDeviceSetting(): void
    {
        $device = ['set' => true, 'null' => null];
        $this->setConfig(function (&$config) {
            $config['null'] = 'notnull!';
            $config['noprefix'] = true;
            $config['prefix']['global'] = true;
        });

        $this->assertNull(twentyfouronlineConfig::getDeviceSetting($device, 'unset'), 'Non-existing settings should return null');
        $this->assertTrue(twentyfouronlineConfig::getDeviceSetting($device, 'set'), 'Could not get setting from device array');
        $this->assertTrue(twentyfouronlineConfig::getDeviceSetting($device, 'noprefix'), 'Failed to get setting from global config');
        $this->assertEquals(
            'notnull!',
            twentyfouronlineConfig::getDeviceSetting($device, 'null'),
            'Null variables should defer to the global setting'
        );
        $this->assertTrue(
            twentyfouronlineConfig::getDeviceSetting($device, 'global', 'prefix'),
            'Failed to get setting from global config with a prefix'
        );
        $this->assertEquals(
            'default',
            twentyfouronlineConfig::getDeviceSetting($device, 'something', 'else', 'default'),
            'Failed to return the default argument'
        );
    }

    public function testGetOsSetting(): void
    {
        $this->setConfig(function (&$config) {
            $config['os']['nullos']['fancy'] = true;
            $config['fallback'] = true;
        });

        $this->assertNull(twentyfouronlineConfig::getOsSetting(null, 'unset'), '$os is null, should return null');
        $this->assertNull(twentyfouronlineConfig::getOsSetting('nullos', 'unset'), 'Non-existing settings should return null');
        $this->assertFalse(twentyfouronlineConfig::getOsSetting('nullos', 'unset', false), 'Non-existing settings should return $default');
        $this->assertTrue(twentyfouronlineConfig::getOsSetting('nullos', 'fancy'), 'Failed to get setting');
        $this->assertNull(twentyfouronlineConfig::getOsSetting('nullos', 'fallback'), 'Incorrectly loaded global setting');

        // load yaml
        $this->assertSame('ios', twentyfouronlineConfig::getOsSetting('ios', 'os'));
        $this->assertGreaterThan(500, count(twentyfouronlineConfig::get('os')), 'Not all OS were loaded from yaml');
    }

    public function testGetCombined(): void
    {
        $this->setConfig(function (&$config) {
            $config['num'] = ['one', 'two'];
            $config['withprefix']['num'] = ['four', 'five'];
            $config['os']['nullos']['num'] = ['two', 'three'];
            $config['assoc'] = ['a' => 'same', 'b' => 'same'];
            $config['withprefix']['assoc'] = ['a' => 'prefix_same', 'd' => 'prefix_same'];
            $config['os']['nullos']['assoc'] = ['b' => 'different', 'c' => 'still same'];
            $config['os']['nullos']['osset'] = 'ossetting';
            $config['gset'] = 'fallbackone';
            $config['withprefix']['gset'] = 'fallbacktwo';
        });

        $this->assertSame(['default'], twentyfouronlineConfig::getCombined('nullos', 'non-existent', '', ['default']), 'Did not return default value on non-existent key');
        $this->assertSame(['ossetting'], twentyfouronlineConfig::getCombined('nullos', 'osset', '', ['default']), 'Did not return OS value when global value is not set');
        $this->assertSame(['fallbackone'], twentyfouronlineConfig::getCombined('nullos', 'gset', '', ['default']), 'Did not return global value when OS value is not set');
        $this->assertSame(['default'], twentyfouronlineConfig::getCombined('nullos', 'non-existent', 'withprefix.', ['default']), 'Did not return default value on non-existent key');
        $this->assertSame(['ossetting'], twentyfouronlineConfig::getCombined('nullos', 'osset', 'withprefix.', ['default']), 'Did not return OS value when global value is not set');
        $this->assertSame(['fallbacktwo'], twentyfouronlineConfig::getCombined('nullos', 'gset', 'withprefix.', ['default']), 'Did not return global value when OS value is not set');

        $combined = twentyfouronlineConfig::getCombined('nullos', 'num');
        sort($combined);
        $this->assertEquals(['one', 'three', 'two'], $combined);

        $combined = twentyfouronlineConfig::getCombined('nullos', 'num', 'withprefix.');
        sort($combined);
        $this->assertEquals(['five', 'four', 'three', 'two'], $combined);

        $this->assertSame(['a' => 'same', 'b' => 'different', 'c' => 'still same'], twentyfouronlineConfig::getCombined('nullos', 'assoc'));
        // should associative not ignore same values (d=>prefix_same)?  are associative arrays actually used?
        $this->assertSame(['a' => 'prefix_same', 'b' => 'different', 'c' => 'still same'], twentyfouronlineConfig::getCombined('nullos', 'assoc', 'withprefix.'));
    }

    public function testSet(): void
    {
        $instance = $this->app->make('twentyfouronline-config');
        twentyfouronlineConfig::set('you.and.me', "I'll be there");

        $this->assertEquals("I'll be there", $this->config->getValue($instance)['you']['and']['me']);
    }

    public function testSetPersist(): void
    {
        $this->dbSetUp();

        $key = 'testing.persist';

        $query = \App\Models\Config::query()->where('config_name', $key);

        $query->delete();
        $this->assertFalse($query->exists(), "$key should not be set, clean database");
        twentyfouronlineConfig::persist($key, 'one');
        $this->assertEquals('one', $query->value('config_value'));
        twentyfouronlineConfig::persist($key, 'two');
        $this->assertEquals('two', $query->value('config_value'));

        $this->dbTearDown();
    }

    public function testHas(): void
    {
        twentyfouronlineConfig::set('long.key.setting', 'no one cares');
        twentyfouronlineConfig::set('null', null);

        $this->assertFalse(twentyfouronlineConfig::has('null'), 'Keys set to null do not count as existing');
        $this->assertTrue(twentyfouronlineConfig::has('long'), 'Top level key should exist');
        $this->assertTrue(twentyfouronlineConfig::has('long.key.setting'), 'Exact exists on value');
        $this->assertFalse(twentyfouronlineConfig::has('long.key.setting.nothing'), 'Non-existent child setting');

        $this->assertFalse(twentyfouronlineConfig::has('off.the.wall'), 'Non-existent key');
        $this->assertFalse(twentyfouronlineConfig::has('off.the'), 'Config:has() should not modify the config');
    }

    public function testGetNonExistent(): void
    {
        $this->assertNull(twentyfouronlineConfig::get('There.is.no.way.this.is.a.key'));
        $this->assertFalse(twentyfouronlineConfig::has('There.is.no'));  // should not add kes when getting
    }

    public function testGetNonExistentNested(): void
    {
        $this->assertNull(twentyfouronlineConfig::get('cheese.and.bologna'));
    }

    public function testGetSubtree(): void
    {
        twentyfouronlineConfig::set('words.top', 'August');
        twentyfouronlineConfig::set('words.mid', 'And Everything');
        twentyfouronlineConfig::set('words.bot', 'After');
        $expected = [
            'top' => 'August',
            'mid' => 'And Everything',
            'bot' => 'After',
        ];

        $this->assertEquals($expected, twentyfouronlineConfig::get('words'));
    }

    /**
     * Pass an anonymous function which will be passed the config variable to modify before it is set
     *
     * @param  callable  $function
     */
    private function setConfig($function)
    {
        $instance = $this->app->make('twentyfouronline-config');
        $config = $this->config->getValue($instance);
        $function($config);
        $this->config->setValue($instance, $config);
    }

    public function testForget(): void
    {
        twentyfouronlineConfig::set('forget.me', 'now');
        $this->assertTrue(twentyfouronlineConfig::has('forget.me'));

        twentyfouronlineConfig::forget('forget.me');
        $this->assertFalse(twentyfouronlineConfig::has('forget.me'));
    }

    public function testForgetSubtree(): void
    {
        twentyfouronlineConfig::set('forget.me.sub', 'yep');
        $this->assertTrue(twentyfouronlineConfig::has('forget.me.sub'));

        twentyfouronlineConfig::forget('forget.me');
        $this->assertFalse(twentyfouronlineConfig::has('forget.me.sub'));
    }
}




