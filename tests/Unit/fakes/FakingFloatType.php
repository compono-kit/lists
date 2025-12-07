<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\fakes;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Interfaces\RepresentsType;

trait FakingFloatType
{
	public function __construct( private float $value )
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
		/** @var RepresentsFloat $type */
		return $type instanceof static && $this->isEqual( $type );
	}

	public function toFloat(): float
	{
		return $this->value;
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

	public function isGreaterThan( float|RepresentsFloat $value ): bool
	{
		return false;
	}

	public function isGreaterThanOrEqual( float|RepresentsFloat $value ): bool
	{
		return false;
	}

	public function isLessThan( float|RepresentsFloat $value ): bool
	{
		return false;
	}

	public function isLessThanOrEqual( float|RepresentsFloat $value ): bool
	{
		return false;
	}

	public function isEqual( float|RepresentsFloat $value ): bool
	{
		return $this->value === ($value instanceof RepresentsFloat ? $value->toFloat() : $value);
	}

	public function add( float|RepresentsFloat|int|RepresentsInteger $value ): static
	{
		return new static( $this->value );
	}

	public function subtract( float|RepresentsFloat|int|RepresentsInteger $value ): static
	{
		return new static( $this->value );
	}

	public function multiply( float|RepresentsFloat|int|RepresentsInteger $value ): static
	{
		return new static( $this->value );
	}

	public function divide( float|RepresentsFloat|int|RepresentsInteger $value ): static
	{
		return new static( $this->value );
	}

	public function jsonSerialize(): float
	{
		return $this->value;
	}
}
