<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\PostmarkTest;

use Netglue\PsrContainer\Postmark\AdminClientFactory;
use Netglue\PsrContainer\Postmark\Exception\MissingAccountKey;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Postmark\PostmarkAdminClient;
use Psr\Container\ContainerInterface;

class AdminClientFactoryTest extends TestCase
{
    /** @var MockObject&ContainerInterface */
    private $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
    }

    /** @return array<string, array{0: mixed[]}> */
    public function configResultingInException(): array
    {
        return [
            'Empty Array' => [[]],
            'Token Empty String' => [['postmark' => ['account_token' => '']]],
            'Token null' => [['postmark' => ['account_token' => null]]],
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

    /**
     * @param array<array-key, mixed> $config
     *
     * @dataProvider configResultingInException
     */
    public function testThatAMissingAccountTokenWillCauseAnException(array $config): void
    {
        $this->containerWillReturnConfig($config);

        $factory = new AdminClientFactory();
        $this->expectException(MissingAccountKey::class);
        $this->expectExceptionMessage('Expected a non-empty string to use as the account api key at [postmark][account_token]');

        $factory->__invoke($this->container);
    }

    public function testThatAnAdminClientCanBeConstructedWithValidConfiguration(): void
    {
        $config = [
            'postmark' => ['account_token' => 'Whatever'],
        ];
        $this->containerWillReturnConfig($config);
        $factory = new AdminClientFactory();
        $factory->__invoke($this->container);
        self::assertTrue(true);
    }

    public function testThatCallStaticWillUseTheCorrectConfiguration(): void
    {
        $config = [
            'something_else' => ['account_token' => 'Whatever'],
        ];
        $this->containerWillReturnConfig($config);
        $object = AdminClientFactory::something_else($this->container);
        self::assertInstanceOf(PostmarkAdminClient::class, $object);
    }
}
