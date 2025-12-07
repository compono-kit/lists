<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\fakes;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Interfaces\RepresentsType;

trait FakingStringType
{
	public function __construct( private string $value )
	{
	}

	public function __toString(): string
	{
		return $this->value;
	}

	public function equals( RepresentsType $type ): bool
	{
		/** @var RepresentsString $type */
		return $type instanceof static && $this->equalsValue( $type );
	}

	public function equalsValue( \Stringable|string $value ): bool
	{
		return $this->value === (string)$value;
	}

	public function toString(): string
	{
		return $this->value;
	}

	public function isEmpty(): bool
	{
		return $this->value === '';
	}

	public function getByteLength(): int
	{
		return 0;
	}

	public function countChars(): int
	{
		return 0;
	}

	public function trim( string $characters = " \n\r\t\v\x00" ): static
	{
		return new static( $this->value );
	}

	public function replace( \Stringable|array|string $search, \Stringable|array|string $replace ): static
	{
		return new static( $this->value );
	}

	public function substring( int $offset, ?int $length = null ): static
	{
		return new static( $this->value );
	}

	public function toLowerCase(): static
	{
		return new static( $this->value );
	}

	public function toUpperCase(): static
	{
		return new static( $this->value );
	}

	public function capitalizeFirst(): static
	{
		return new static( $this->value );
	}

	public function deCapitalizeFirst(): static
	{
		return new static( $this->value );
	}

	public function split( string $delimiter ): \Iterator
	{
		return new \ArrayIterator();
	}

	public function splitNative( string $delimiter ): array
	{
		return [];
	}

	public function matchRegularExpression( \Stringable|string $pattern, &$matches, int $flags, int $offset ): bool
	{
		return false;
	}

	public function contains( \Stringable|string $needle ): bool
	{
		return false;
	}

	public function containsOneOf( string|\Stringable ...$values ): bool
	{
		return false;
	}

	public function isOneOf( string|\Stringable ...$values ): bool
	{
		return false;
	}

	public function toNativeType(): int|float|string|bool|array
	{
		return $this->value;
	}

	public function jsonSerialize(): string
	{
		return $this->value;
	}
}
