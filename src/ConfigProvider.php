<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\Postmark;

use Laminas\ServiceManager\ServiceManager;
use Postmark\PostmarkAdminClient;
use Postmark\PostmarkClient;

/**
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 * @final
 */
class ConfigProvider
{
    /** @return ServiceManagerConfiguration */
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
