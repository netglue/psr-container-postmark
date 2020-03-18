<?php
declare(strict_types=1);

namespace Netglue\PsrContainer\PostmarkTest;

use Netglue\PsrContainer\Postmark\AdminClientFactory;
use Netglue\PsrContainer\Postmark\Exception\MissingAccountKey;
use PHPUnit\Framework\TestCase;
use Postmark\PostmarkAdminClient;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;

class AdminClientFactoryTest extends TestCase
{
    /** @var ObjectProphecy|ContainerInterface */
    private $container;

    protected function setUp() : void
    {
        parent::setUp();
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    /** @return mixed[][] */
    public function configResultingInException() : iterable
    {
        return [
            'Empty Array' => [[]],
            'Token Empty String' => [['postmark' => ['account_token' => '']]],
            'Token null' => [['postmark' => ['account_token' => null]]],
        ];
    }

    /**
     * @param mixed[] $config
     *
     * @dataProvider configResultingInException
     */
    public function testThatAMissingAccountTokenWillCauseAnException(array $config) : void
    {
        $this->container->has('config')->willReturn(true)->shouldBeCalled();
        $this->container->get('config')->willReturn($config)->shouldBeCalled();
        $factory = new AdminClientFactory();
        $this->expectException(MissingAccountKey::class);
        $this->expectExceptionMessage('Expected a non-empty string to use as the account api key at [postmark][account_token]');

        $factory->__invoke($this->container->reveal());
    }

    public function testThatAnAdminClientCanBeConstructedWithValidConfiguration() : void
    {
        $config = [
            'postmark' => ['account_token' => 'Whatever'],
        ];
        $this->container->has('config')->willReturn(true)->shouldBeCalled();
        $this->container->get('config')->willReturn($config)->shouldBeCalled();
        $factory = new AdminClientFactory();
        $factory->__invoke($this->container->reveal());
        $this->addToAssertionCount(1);
    }

    public function testThatCallStaticWillUseTheCorrectConfiguration() : void
    {
        $config = [
            'something_else' => ['account_token' => 'Whatever'],
        ];
        $this->container->has('config')->willReturn(true)->shouldBeCalled();
        $this->container->get('config')->willReturn($config)->shouldBeCalled();
        $object = AdminClientFactory::something_else($this->container->reveal());
        $this->assertInstanceOf(PostmarkAdminClient::class, $object);
    }
}
