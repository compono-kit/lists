<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\fakes;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Interfaces\RepresentsType;

trait FakingBooleanType
{
	public function __construct( private bool $value )
	{
	}

	public function toBoolean(): bool
	{
		return $this->value;
	}

	public function toInteger(): int
	{
		return (int)$this->value;
	}

	public function isTrue(): bool
	{
		return $this->value === true;
	}

	public function isFalse(): bool
	{
		return $this->value === false;
	}

	public function equals( RepresentsType $type ): bool
	{
		/** @var RepresentsBoolean $type */
		return $type instanceof static && $type->toBoolean() === $this->value;
	}

	public function toNativeType(): int|float|string|bool|array
	{
		return $this->value;
	}

	public function jsonSerialize(): bool
	{
		return $this->value;
	}
}
