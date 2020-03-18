<?php
declare(strict_types=1);

namespace Netglue\PsrContainer\Postmark;

use Postmark\PostmarkAdminClient;
use Postmark\PostmarkClient;

class ConfigProvider
{
    /** @return mixed[] */
    public function __invoke() : array
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
