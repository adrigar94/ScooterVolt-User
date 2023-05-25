<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use DomainException;
use JsonSerializable;
use Stringable;

class StringValueObject implements Stringable, JsonSerializable
{
    protected $min_length = null;
    protected $max_length = null;

    public function __construct(protected string $value)
    {
        $this->ensureIsValid($value);
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    protected function ensureIsValid(string $string): void
    {
        $this->ensureIsMinLengthIsValid($string);
        $this->ensureIsMaxLengthIsValid($string);
    }

    final protected function ensureIsMinLengthIsValid(string $string): void
    {
        if(!is_null($this->min_length) AND strlen($string) < $this->min_length){
            throw new DomainException("String does not have the required minimum length", 500);
        }
    }
    final protected function ensureIsMaxLengthIsValid(string $string): void
    {
        if(!is_null($this->max_length) AND strlen($string) > $this->max_length){
            throw new DomainException("String exceeds maximum allowed length", 500);
        }
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function jsonSerialize(): string
    {
        return $this->value();
    }
}