<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\Postmark;

use Netglue\PsrContainer\Postmark\Exception\MissingAccountKey;
use Postmark\PostmarkAdminClient;
use Psr\Container\ContainerInterface;

use function sprintf;

class AdminClientFactory extends BaseFactory
{
    public function __invoke(ContainerInterface $container): PostmarkAdminClient
    {
        $config = $this->retrieveConfig($container);
        $token  = $config['account_token'] ?? null;
        if (empty($token)) {
            throw MissingAccountKey::withConfigPath(sprintf('[%s][account_token]', $this->section));
        }

        $timeout = $config['server_timeout'] ?? self::DEFAULT_API_TIMEOUT;

        return new PostmarkAdminClient($token, (int) $timeout);
    }
}
