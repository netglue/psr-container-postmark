<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\Postmark;

use Netglue\PsrContainer\Postmark\Exception\BadMethodCall;
use Postmark\PostmarkAdminClient;
use Postmark\PostmarkClient;
use Psr\Container\ContainerInterface;

use function array_key_exists;
use function assert;
use function is_array;

/** @internal */
abstract class BaseFactory
{
    public const DEFAULT_CONFIG_SECTION = 'postmark';
    public const DEFAULT_API_TIMEOUT    = 30;

    final public function __construct(protected string $section = self::DEFAULT_CONFIG_SECTION)
    {
    }

    abstract public function __invoke(ContainerInterface $container): PostmarkClient|PostmarkAdminClient;

    /** @param array<array-key, mixed> $arguments */
    final public static function __callStatic(string $name, array $arguments): PostmarkClient|PostmarkAdminClient
    {
        if (! array_key_exists(0, $arguments) || ! $arguments[0] instanceof ContainerInterface) {
            throw new BadMethodCall(
                'The first argument to __callStatic must be an instance of ContainerInterface',
            );
        }

        return (new static($name))($arguments[0]);
    }

    /** @return array<array-key, mixed> */
    protected function retrieveConfig(ContainerInterface $container): array
    {
        $config = $container->has('config') ? $container->get('config') : [];
        assert(is_array($config));

        $options = $config[$this->section] ?? [];
        assert(is_array($options));

        return $options;
    }
}
