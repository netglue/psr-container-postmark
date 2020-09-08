<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\Postmark;

use Netglue\PsrContainer\Postmark\Exception\BadMethodCall;
use Psr\Container\ContainerInterface;

use function array_key_exists;

abstract class BaseFactory
{
    public const DEFAULT_CONFIG_SECTION = 'postmark';
    public const DEFAULT_API_TIMEOUT    = 30;

    /** @var string */
    protected $section;

    public function __construct(string $section = self::DEFAULT_CONFIG_SECTION)
    {
        $this->section = $section;
    }

    /** @inheritDoc */
    public static function __callStatic(string $name, array $arguments)
    {
        if (! array_key_exists(0, $arguments) || ! $arguments[0] instanceof ContainerInterface) {
            throw new BadMethodCall(
                'The first argument to __callStatic must be an instance of ContainerInterface'
            );
        }

        return (new static($name))($arguments[0]);
    }

    /** @return mixed[] */
    protected function retrieveConfig(ContainerInterface $container): array
    {
        $config = $container->has('config') ? $container->get('config') : [];

        return $config[$this->section] ?? [];
    }
}
