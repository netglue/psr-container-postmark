<?php

declare(strict_types=1);

namespace Netglue\PsrContainer\Postmark\Exception;

use RuntimeException;

use function sprintf;

/** @final */
class MissingAccountKey extends RuntimeException
{
    public static function withConfigPath(string $path): self
    {
        $message = sprintf(
            'Expected a non-empty string to use as the account api key at %s',
            $path,
        );

        return new self($message, 500);
    }
}
