<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\Postmark;

use Postmark\PostmarkAdminClient;
use Postmark\PostmarkClient;

/** @final */
class ConfigProvider
{
    /** @return array{dependencies: array{factories: array<class-string, class-string>}} */
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
