<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\Postmark;

use Laminas\ServiceManager\ConfigInterface;
use Postmark\PostmarkAdminClient;
use Postmark\PostmarkClient;

/**
 * @psalm-import-type ServiceManagerConfigurationType from ConfigInterface
 * @final
 */
class ConfigProvider
{
    /** @return ServiceManagerConfigurationType */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    PostmarkClient::class => ClientFactory::class,
                    PostmarkAdminClient::class => AdminClientFactory::class,
                ],
            ],
        ];
    }
}
