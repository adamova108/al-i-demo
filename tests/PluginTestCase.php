<?php

namespace AL_Inpsyde\Tests;

use AL_Inpsyde\Admin\Settings;
use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class PluginTestCase extends TestCase
{

   // use MockeryPHPUnitIntegration;

    protected $settings;

    /**
     * Runs before each test.
     */
    protected function setUp(): void
    {

        parent::setUp();
        Monkey\setUp();

        $this->settings = $this->getMockBuilder(Settings::class)
                               ->disableOriginalConstructor()
                               ->getMock();

        Functions\when('__')
            ->returnArg(1);
        Functions\when('_e')
            ->returnArg(1);
        Functions\when('_n')
            ->returnArg(1);

        // Mock add_option
        Functions\when('add_option')->justReturn(true);
        // Mock update_option
        Functions\when('update_option')->justReturn(true);
        // Mock get_option
        Functions\when('get_option')->justReturn(true);
        // Mock delete_option
        Functions\when('delete_option')->justReturn(true);
    }

    /**
     * Runs after each test.
     */
    protected function tearDown(): void
    {

        Monkey\tearDown();
        parent::tearDown();
    }
}
