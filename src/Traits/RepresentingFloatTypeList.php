<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;

trait RepresentingFloatTypeList
{
	/** @var RepresentsFloat[]  */
	private array $floatTypes;

	public function __construct( RepresentsFloat ...$floatTypes )
	{
		$this->floatTypes = $floatTypes;
	}

	public function exists( int $index ): bool
	{
		return isset( $this->floatTypes[ $index ] );
	}

	public function get( int $index ): RepresentsFloat
	{
		$this->guardIndexExists( $index );

		return $this->floatTypes[ $index ];
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsFloatTypeList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsFloatTypeList $list ): bool
	{
		if ( $list->count() !== $this->count() )
		{
			return false;
		}

		foreach ( $list as $index => $type )
		{
			if ( !isset( $this->floatTypes[ $index ] ) )
			{
				return false;
			}

			if ( !$type->equals( $this->floatTypes[ $index ] ) )
			{
				return false;
			}
		}

		return true;
	}

	public function toNativeType(): array
	{
		return $this->floatTypes;
	}

	/**
	 * @return array<int, float>
	 */
	public function toFloats(): array
	{
		return iterator_to_array( $this->iterateFloats() );
	}

	/**
	 * @return \Iterator<int, float>
	 */
	public function iterateFloats(): \Iterator
	{
		foreach ( $this->floatTypes as $index => $type )
		{
			yield $index => $type->toFloat();
		}
	}

	public function next(): void
	{
		next( $this->floatTypes );
	}

	public function key(): int|null
	{
		return key( $this->floatTypes );
	}

	public function valid(): bool
	{
		return isset( $this->floatTypes[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->floatTypes );
	}

	public function current(): RepresentsFloat
	{
		return current( $this->floatTypes );
	}

	public function first(): RepresentsFloat
	{
		return $this->floatTypes[ array_keys( $this->floatTypes )[0] ];
	}

	public function last(): RepresentsFloat
	{
		return $this->floatTypes[ array_keys( $this->floatTypes )[ count( $this->floatTypes ) - 1 ] ];
	}

	public function findIndex( MatchesType $matcher ): ?int
	{
		foreach ( $this->floatTypes as $index => $type )
		{
			if ( $matcher->matches( $type ) )
			{
				return $index;
			}
		}

		return null;
	}

	public function count(): int
	{
		return count( $this->floatTypes );
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->floatTypes, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->floatTypes;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( !isset( $this->floatTypes[ $index ] ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
