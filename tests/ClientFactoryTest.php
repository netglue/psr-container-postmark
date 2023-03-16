<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\PostmarkTest;

use Netglue\PsrContainer\Postmark\ClientFactory;
use Netglue\PsrContainer\Postmark\Exception\BadMethodCall;
use Netglue\PsrContainer\Postmark\Exception\MissingServerKey;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Postmark\PostmarkClient;
use Psr\Container\ContainerInterface;

class ClientFactoryTest extends TestCase
{
    /** @var MockObject&ContainerInterface */
    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = $this->createMock(ContainerInterface::class);
    }

    /** @return array<string, array{0: mixed[]}> */
    public static function configResultingInException(): array
    {
        return [
            'Empty Array' => [[]],
            'Token Empty String' => [['postmark' => ['server_token' => '']]],
            'Token null' => [['postmark' => ['server_token' => null]]],
        ];
    }

    /** @param array<array-key, mixed> $config */
    private function containerWillReturnConfig(array $config): void
    {
        $this->container
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $this->container
            ->expects(self::atLeastOnce())
            ->method('get')
            ->with('config')
            ->willReturn($config);
    }

    /** @param array<array-key, mixed> $config */
    #[DataProvider('configResultingInException')]
    public function testThatAMissingServerTokenWillCauseAnException(array $config): void
    {
        $this->containerWillReturnConfig($config);
        $factory = new ClientFactory();
        $this->expectException(MissingServerKey::class);
        $this->expectExceptionMessage(
            'Expected a non-empty string to use as the server api key at [postmark][server_token]',
        );

        $factory->__invoke($this->container);
    }

    public function testThatGivenAServerTokenConstructionWillBePossible(): void
    {
        $config = [
            'postmark' => ['server_token' => 'Whatever'],
        ];
        $this->containerWillReturnConfig($config);
        $factory = new ClientFactory();
        $factory->__invoke($this->container);
        self::assertTrue(true);
    }

    public function testThatADifferentTopLevelConfigKeyCanBeUsed(): void
    {
        $config = [
            'something_else' => ['server_token' => 'Whatever'],
        ];
        $this->containerWillReturnConfig($config);
        $factory = new ClientFactory('something_else');
        $factory->__invoke($this->container);
        self::assertTrue(true);
    }

    public function testThatCallStaticWillThrowAnExceptionIfAContainerIsNotTheFirstArgument(): void
    {
        $this->expectException(BadMethodCall::class);
        $this->expectExceptionMessage('The first argument to __callStatic must be an instance of ContainerInterface');

        ClientFactory::whatever();
    }

    public function testThatCallStaticCanSuccessfullyReturnAClientWhenConfigurationIsSatisfied(): void
    {
        $config = [
            'something_else' => ['server_token' => 'Whatever'],
        ];
        $this->containerWillReturnConfig($config);
        $object = ClientFactory::something_else($this->container);
        self::assertInstanceOf(PostmarkClient::class, $object);
    }
}
