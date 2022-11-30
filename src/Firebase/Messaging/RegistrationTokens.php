<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use Countable;
use IteratorAggregate;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;
use Traversable;

use function array_map;
use function count;
use function is_array;
use function is_string;

/**
 * @implements IteratorAggregate<RegistrationToken>
 */
final class RegistrationTokens implements Countable, IteratorAggregate
{
    /** @var array<RegistrationToken> */
    private array $tokens;

    /**
     * @internal
     */
    public function __construct(RegistrationToken ...$tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @param RegistrationTokens|RegistrationToken|non-empty-array<RegistrationToken|string>|non-empty-string $values
     *
     * @throws InvalidArgument
     */
    public static function fromValue($values): self
    {
        $tokens = [];

        if ($values instanceof self) {
            $tokens = $values->values();
        } elseif ($values instanceof RegistrationToken) {
            $tokens = [$values];
        } elseif (is_string($values)) {
            $tokens = [RegistrationToken::fromValue($values)];
        } elseif (is_array($values)) {
            foreach ($values as $value) {
                if ($value instanceof RegistrationToken) {
                    $tokens[] = $value;
                } elseif (is_string($value) && $value !== '') {
                    $tokens[] = RegistrationToken::fromValue($value);
                }
            }
        }

        if (count($tokens) === 0) {
            throw new InvalidArgument('No registration tokens provided');
        }

        return new self(...$tokens);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Traversable<RegistrationToken>
     */
    public function getIterator(): Traversable
    {
        yield from $this->tokens;
    }

    public function isEmpty(): bool
    {
        return $this->tokens === [];
    }

    /**
     * @return array<RegistrationToken>
     */
    public function values(): array
    {
        return $this->tokens;
    }

    /**
     * @return array<string>
     */
    public function asStrings(): array
    {
        return array_map(strval(...), $this->tokens);
    }

    public function count(): int
    {
        return count($this->tokens);
    }

    /**
     * @param RegistrationToken|non-empty-string $token
     */
    public function has($token): bool
    {
        $token = $token instanceof RegistrationToken ? $token : RegistrationToken::fromValue($token);

        foreach ($this->tokens as $existing) {
            if ($existing->value() === $token->value()) {
                return true;
            }
        }

        return false;
    }
}
