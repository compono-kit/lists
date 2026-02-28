<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;

trait RepresentingIntegerTypeList
{
	/** @var RepresentsInteger[] */
	private array $integerTypes;

	public function __construct( RepresentsInteger ...$integerTypes )
	{
		$this->integerTypes = $integerTypes;
	}

	public function exists( int $index ): bool
	{
		return isset( $this->integerTypes[ $index ] );
	}

	public function get( int $index ): RepresentsInteger
	{
		$this->guardIndexExists( $index );

		return $this->integerTypes[ $index ];
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsIntegerTypeList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsIntegerTypeList $list ): bool
	{
		if ( $list->count() !== $this->count() )
		{
			return false;
		}

		foreach ( $list as $index => $type )
		{
			if ( !isset( $this->integerTypes[ $index ] ) )
			{
				return false;
			}

			if ( !$type->equals( $this->integerTypes[ $index ] ) )
			{
				return false;
			}
		}

		return true;
	}

	public function toNativeType(): array
	{
		return $this->integerTypes;
	}

	/**
	 * @return array<int, int>
	 */
	public function toIntegers(): array
	{
		return iterator_to_array( $this->iterateIntegers() );
	}

	/**
	 * @return \Iterator<int, int>
	 */
	public function iterateIntegers(): \Iterator
	{
		foreach ( $this->integerTypes as $key => $type )
		{
			yield (string)$key => $type->toInteger();
		}
	}

	public function next(): void
	{
		next( $this->integerTypes );
	}

	public function key(): int|null
	{
		return key( $this->integerTypes );
	}

	public function valid(): bool
	{
		return isset( $this->integerTypes[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->integerTypes );
	}

	public function current(): RepresentsInteger
	{
		return current( $this->integerTypes );
	}

	public function first(): RepresentsInteger
	{
		return $this->integerTypes[ array_keys( $this->integerTypes )[0] ];
	}

	public function last(): RepresentsInteger
	{
		return $this->integerTypes[ array_keys( $this->integerTypes )[ count( $this->integerTypes ) - 1 ] ];
	}

	public function count(): int
	{
		return count( $this->integerTypes );
	}

	public function findIndex( MatchesType $matcher ): ?int
	{
		foreach ( $this->integerTypes as $index => $type )
		{
			if ( $matcher->matches( $type ) )
			{
				return $index;
			}
		}

		return null;
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->integerTypes, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->integerTypes;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( !isset( $this->integerTypes[ $index ] ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
