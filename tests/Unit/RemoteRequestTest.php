<?php



use AL_Inpsyde\Tests\PluginTestCase;
use Brain\Monkey\Functions;
use AL_Inpsyde\Includes\{RemoteRequest, UserRemoteRequest};

class RemoteRequestTest extends PluginTestCase
{

    public function testRemoteRequestGet()
    {

        Functions\when('wp_parse_args')->returnArg();
        Functions\when('wp_remote_get')->justReturn(['body' => '{"test":"response"}']);
        Functions\when('is_wp_error')->justReturn(false);

        $stub = $this->getMockForAbstractClass(RemoteRequest::class);
        $result = $stub->AlRemoteRequest('', ['method' => 'GET', 'body' => []]);
        $this->assertEquals(['test' => 'response'], $result);
    }

    public function testRemoteRequestGetError()
    {

        $wp_error_mock = \Mockery::mock('\WP_Error');

        Functions\when('wp_parse_args')->returnArg();
        Functions\when('wp_remote_get')->justReturn($wp_error_mock);
        Functions\expect('is_wp_error')
            ->once()
            ->with($wp_error_mock)
            ->andReturn(true);

        $stub = $this->getMockForAbstractClass(RemoteRequest::class);
        $result = $stub->AlRemoteRequest('', ['method' => 'GET', 'body' => []]);

        $this->assertEquals($wp_error_mock, $result);
    }

    public function testUserRemoteRequestFromURL()
    {

        Functions\when('get_transient')->justReturn(false);

        Functions\when('wp_parse_args')->returnArg(2); // Return the defaults
        Functions\when('wp_remote_get')->justReturn(['body' => '{"test":"response"}']);
        Functions\when('is_wp_error')->justReturn(false);

        Functions\when('set_transient')->justReturn(true);

        $userRR = new UserRemoteRequest($this->settings);
        $result = $userRR->AlGetUserdata();

        $this->assertEquals(['test' => 'response'], $result);
    }

    public function testUserRemoteRequestFromTransient()
    {
        Functions\when('is_wp_error')->justReturn(false);
        Functions\when('get_transient')->justReturn(['test' => 'response']);

        /* $settings = Mockery::mock('settings');
         */

        /* $settings->expects('getTransientExpiry')
             ->once()
             ->andReturn(123);*/

        $userRR = new UserRemoteRequest($this->settings);

        $result = $userRR->AlGetUserdata();

        $this->assertEquals(['test' => 'response'], $result);
    }
}
