<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\Postmark;

use Netglue\PsrContainer\Postmark\Exception\MissingAccountKey;
use Postmark\PostmarkAdminClient;
use Psr\Container\ContainerInterface;

use function assert;
use function is_numeric;
use function is_string;
use function sprintf;

class AdminClientFactory extends BaseFactory
{
    public function __invoke(ContainerInterface $container): PostmarkAdminClient
    {
        $config = $this->retrieveConfig($container);
        $token  = $config['account_token'] ?? null;
        if (empty($token) || ! is_string($token)) {
            throw MissingAccountKey::withConfigPath(sprintf('[%s][account_token]', $this->section));
        }

        $timeout = $config['server_timeout'] ?? self::DEFAULT_API_TIMEOUT;
        assert(is_numeric($timeout));

        return new PostmarkAdminClient($token, (int) $timeout);
    }
}
