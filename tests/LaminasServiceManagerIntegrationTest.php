<?php
declare(strict_types=1);

namespace Netglue\PsrContainer\PostmarkTest;

use Laminas\ServiceManager\ServiceManager;
use Netglue\PsrContainer\Postmark\AdminClientFactory;
use Netglue\PsrContainer\Postmark\ClientFactory;
use Netglue\PsrContainer\Postmark\ConfigProvider;
use PHPUnit\Framework\TestCase;
use Postmark\PostmarkAdminClient;
use Postmark\PostmarkClient;
use function array_merge;

class LaminasServiceManagerIntegrationTest extends TestCase
{
    /** @var ServiceManager */
    private $container;

    protected function setUp() : void
    {
        parent::setUp();
        $this->container = new ServiceManager();
    }

    private function setupDependencies() : void
    {
        $this->container->setFactory(
            'RegularServerClient',
            ClientFactory::class
        );

        $this->container->setFactory(
            'ServerClientCallStatic',
            [ClientFactory::class, 'postmark_static_test']
        );

        $this->container->setFactory(
            'RegularAccountClient',
            AdminClientFactory::class
        );

        $this->container->setFactory(
            'AccountClientCallStatic',
            [AdminClientFactory::class, 'postmark_static_test']
        );

        $this->container->setService('config', [
            'postmark' => [
                'server_token' => 'Foo',
                'account_token' => 'Bar',
            ],
            'postmark_static_test' => [
                'server_token' => 'Foo',
                'account_token' => 'Bar',
            ],
        ]);
    }

    public function testServiceManagerCanCreateExpectedInstances() : void
    {
        $this->setupDependencies();
        $this->assertInstanceOf(PostmarkClient::class, $this->container->get('RegularServerClient'));
        $this->assertInstanceOf(PostmarkClient::class, $this->container->get('ServerClientCallStatic'));
        $this->assertInstanceOf(PostmarkAdminClient::class, $this->container->get('RegularAccountClient'));
        $this->assertInstanceOf(PostmarkAdminClient::class, $this->container->get('AccountClientCallStatic'));
    }

    public function testThatShippedConfigProviderWillYieldServicesUsingFqcn() : void
    {
        $config = array_merge(
            (new ConfigProvider())(),
            [
                'postmark' => [
                    'server_token' => 'Foo',
                    'account_token' => 'Bar',
                ],
            ]
        );
        $dependencies = $config['dependencies'];
        $dependencies['services']['config'] = $config;
        $container = new ServiceManager($dependencies);

        $this->assertInstanceOf(PostmarkClient::class, $container->get(PostmarkClient::class));
        $this->assertInstanceOf(PostmarkAdminClient::class, $container->get(PostmarkAdminClient::class));
    }
}
