<?php
declare(strict_types=1);

namespace Netglue\PsrContainer\PostmarkTest;

use Netglue\PsrContainer\Postmark\ClientFactory;
use Netglue\PsrContainer\Postmark\Exception\BadMethodCall;
use Netglue\PsrContainer\Postmark\Exception\MissingServerKey;
use PHPUnit\Framework\TestCase;
use Postmark\PostmarkClient;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;

class ClientFactoryTest extends TestCase
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
            'Token Empty String' => [['postmark' => ['server_token' => '']]],
            'Token null' => [['postmark' => ['server_token' => null]]],
        ];
    }

    /**
     * @param mixed[] $config
     *
     * @dataProvider configResultingInException
     */
    public function testThatAMissingServerTokenWillCauseAnException(array $config) : void
    {
        $this->container->has('config')->willReturn(true)->shouldBeCalled();
        $this->container->get('config')->willReturn($config)->shouldBeCalled();
        $factory = new ClientFactory();
        $this->expectException(MissingServerKey::class);
        $this->expectExceptionMessage('Expected a non-empty string to use as the server api key at [postmark][server_token]');

        $factory->__invoke($this->container->reveal());
    }

    public function testThatGivenAServerTokenConstructionWillBePossible() : void
    {
        $config = [
            'postmark' => ['server_token' => 'Whatever'],
        ];
        $this->container->has('config')->willReturn(true)->shouldBeCalled();
        $this->container->get('config')->willReturn($config)->shouldBeCalled();
        $factory = new ClientFactory();
        $factory->__invoke($this->container->reveal());
        $this->addToAssertionCount(1);
    }

    public function testThatADifferentTopLevelConfigKeyCanBeUsed() : void
    {
        $config = [
            'something_else' => ['server_token' => 'Whatever'],
        ];
        $this->container->has('config')->willReturn(true)->shouldBeCalled();
        $this->container->get('config')->willReturn($config)->shouldBeCalled();
        $factory = new ClientFactory('something_else');
        $factory->__invoke($this->container->reveal());
        $this->addToAssertionCount(1);
    }

    public function testThatCallStaticWillThrowAnExceptionIfAContainerIsNotTheFirstArgument() : void
    {
        $this->expectException(BadMethodCall::class);
        $this->expectExceptionMessage('The first argument to __callStatic must be an instance of ContainerInterface');

        ClientFactory::whatever();
    }

    public function testThatCallStaticCanSuccessfullyReturnAClientWhenConfigurationIsSatisfied() : void
    {
        $config = [
            'something_else' => ['server_token' => 'Whatever'],
        ];
        $this->container->has('config')->willReturn(true)->shouldBeCalled();
        $this->container->get('config')->willReturn($config)->shouldBeCalled();
        $object = ClientFactory::something_else($this->container->reveal());
        $this->assertInstanceOf(PostmarkClient::class, $object);
    }
}
