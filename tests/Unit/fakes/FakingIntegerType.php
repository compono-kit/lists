<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\fakes;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Interfaces\RepresentsType;

trait FakingIntegerType
{
	public function __construct( private int $value )
	{
	}

	public function toNativeType(): int|float|string|bool|array
	{
		return $this->value;
	}

	public function __toString(): string
	{
		return (string)$this->value;
	}

	public function equals( RepresentsType $type ): bool
	{
		/** @var RepresentsInteger $type */
		return $type instanceof static && $this->isEqual( $type );
	}

	public function toInteger(): int
	{
		return $this->value;
	}

	public function increment( int|RepresentsInteger $value = 1 ): static
	{
		return new static( $this->value );
	}

	public function decrement( int|RepresentsInteger $value = 1 ): static
	{
		return new static( $this->value );
	}

	public function toFloat(): float
	{
		return (float)$this->value;
	}

	public function toString( int $decimals = 0 ): string
	{
		return $this->__toString();
	}

	public function isZero(): bool
	{
		return false;
	}

	public function isPositive(): bool
	{
		return false;
	}

	public function isNegative(): bool
	{
		return false;
	}

	public function isPositiveOrZero(): bool
	{
		return false;
	}

	public function isNegativeOrZero(): bool
	{
		return false;
	}

	public function isGreaterThan( int|RepresentsInteger $value ): bool
	{
		return false;
	}

	public function isGreaterThanOrEqual( int|RepresentsInteger $value ): bool
	{
		return false;
	}

	public function isLessThan( int|RepresentsInteger $value ): bool
	{
		return false;
	}

	public function isLessThanOrEqual( int|RepresentsInteger $value ): bool
	{
		return false;
	}

	public function isEqual( int|RepresentsInteger $value ): bool
	{
		return $this->value === ($value instanceof RepresentsInteger ? $value->toInteger() : $value);
	}

	public function add( int|RepresentsInteger $value ): static
	{
		return new static( $this->value );
	}

	public function subtract( int|RepresentsInteger $value ): static
	{
		return new static( $this->value );
	}

	public function multiply( int|RepresentsInteger $value ): static
	{
		return new static( $this->value );
	}

	public function jsonSerialize(): float
	{
		return $this->value;
	}
}
