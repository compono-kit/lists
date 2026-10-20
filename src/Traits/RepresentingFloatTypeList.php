<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;

trait RepresentingFloatTypeList
{
	/** @var RepresentsFloat[] */
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
		if ( $this::class !== $type::class )
		{
			return false;
		}

		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		/**
		 * @var RepresentsFloatTypeList $type
		 */
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
			if ( !$type->equals( $this->floatTypes[ $index ] ) )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @return array<int, RepresentsFloat>
	 */
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
		return null !== key( $this->floatTypes );
	}

	public function rewind(): void
	{
		reset( $this->floatTypes );
	}

	public function current(): RepresentsFloat
	{
		$index = key( $this->floatTypes );

		if ( null === $index )
		{
			throw new MissingIndexException( 'Current index does not exist' );
		}

		return $this->floatTypes[ $index ];
	}

	public function first(): RepresentsFloat
	{
		$index = array_key_first( $this->floatTypes );

		if ( null === $index )
		{
			throw new MissingIndexException( 'List is empty' );
		}

		return $this->floatTypes[ $index ];
	}

	public function last(): RepresentsFloat
	{
		$index = array_key_last( $this->floatTypes );

		if ( null === $index )
		{
			throw new MissingIndexException( 'List is empty' );
		}

		return $this->floatTypes[ $index ];
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

	private function existsIn( RepresentsFloat $typeToCheck, RepresentsList $list ): bool
	{
		foreach ( $list as $item )
		{
			if ( $item->equals( $typeToCheck ) )
			{
				return true;
			}
		}

		return false;
	}
}
