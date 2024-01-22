<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\Postmark;

use Netglue\PsrContainer\Postmark\Exception\MissingServerKey;
use Postmark\PostmarkClient;
use Psr\Container\ContainerInterface;

use function assert;
use function is_numeric;
use function is_string;
use function sprintf;

final class ClientFactory extends BaseFactory
{
    public function __invoke(ContainerInterface $container): PostmarkClient
    {
        $config = $this->retrieveConfig($container);
        $token  = $config['server_token'] ?? null;
        if (! is_string($token) || $token === '') {
            throw MissingServerKey::withConfigPath(sprintf('[%s][server_token]', $this->section));
        }

        $timeout = $config['server_timeout'] ?? self::DEFAULT_API_TIMEOUT;
        assert(is_numeric($timeout));

        return new PostmarkClient($token, (int) $timeout);
    }
}
