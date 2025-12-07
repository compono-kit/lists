<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanList;

trait RepresentingBooleanList
{
	/**
	 * @param RepresentsBoolean[] $booleanTypes
	 */
	public function __construct( private array $booleanTypes = [] )
	{
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsBooleanList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsBooleanList $list ): bool
	{
		if ( $list->count() !== $this->count() )
		{
			return false;
		}

		foreach ( $list as $index => $type )
		{
			if ( !isset( $this->booleanTypes[ $index ] ) )
			{
				return false;
			}

			if ( !$type->equals( $this->booleanTypes[ $index ] ) )
			{
				return false;
			}
		}

		return true;
	}

	public function toNativeType(): array
	{
		return $this->booleanTypes;
	}

	/**
	 * @return array<int, bool>
	 */
	public function toBooleans(): array
	{
		return iterator_to_array( $this->iterateBooleans() );
	}

	/**
	 * @return \Iterator<int, bool>
	 */
	public function iterateBooleans(): \Iterator
	{
		foreach ( $this->booleanTypes as $key => $type )
		{
			yield (string)$key => $type->toBoolean();
		}
	}

	public function current(): RepresentsBoolean
	{
		return current( $this->booleanTypes );
	}

	public function next(): void
	{
		next( $this->booleanTypes );
	}

	public function key(): int|null
	{
		return key( $this->booleanTypes );
	}

	public function valid(): bool
	{
		return isset( $this->booleanTypes[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->booleanTypes );
	}

	public function count(): int
	{
		return count( $this->booleanTypes );
	}

	public function jsonSerialize(): array
	{
		return $this->booleanTypes;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( !isset( $this->booleanTypes[ $index ] ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
