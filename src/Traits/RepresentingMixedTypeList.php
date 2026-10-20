<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;

trait RepresentingMixedTypeList
{
	/** @var RepresentsType[] */
	private array $mixedTypes;

	public function __construct( RepresentsType ...$mixedTypes )
	{
		$this->mixedTypes = $mixedTypes;
	}

	public function exists( int $index ): bool
	{
		return isset( $this->mixedTypes[ $index ] );
	}

	public function get( int $index ): RepresentsType
	{
		$this->guardIndexExists( $index );

		return $this->mixedTypes[ $index ];
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
		 * @var RepresentsMixedTypeList $type
		 */
		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsMixedTypeList $list ): bool
	{
		if ( $list->count() !== $this->count() )
		{
			return false;
		}

		foreach ( $list as $index => $type )
		{
			if ( !$type->equals( $this->mixedTypes[ $index ] ) )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @return array<int, RepresentsType>
	 */
	public function toNativeType(): array
	{
		return $this->mixedTypes;
	}

	/**
	 * @return array<int, int|float|string|bool|array>
	 */
	public function toNativeTypes(): array
	{
		return iterator_to_array( $this->iterateNativeTypes() );
	}

	/**
	 * @return \Iterator<int, int|float|string|bool|array>
	 */
	public function iterateNativeTypes(): \Iterator
	{
		foreach ( $this->mixedTypes as $index => $type )
		{
			yield $index => $type->toNativeType();
		}
	}

	public function next(): void
	{
		next( $this->mixedTypes );
	}

	public function key(): int|null
	{
		return key( $this->mixedTypes );
	}

	public function valid(): bool
	{
		return null !== key( $this->mixedTypes );
	}

	public function rewind(): void
	{
		reset( $this->mixedTypes );
	}

	public function current(): RepresentsType
	{
		$index = key( $this->mixedTypes );

		if ( null === $index )
		{
			throw new MissingIndexException( 'Current index does not exist' );
		}

		return $this->mixedTypes[ $index ];
	}

	public function first(): RepresentsType
	{
		$index = array_key_first( $this->mixedTypes );

		if ( null === $index )
		{
			throw new MissingIndexException( 'List is empty' );
		}

		return $this->mixedTypes[ $index ];
	}

	public function last(): RepresentsType
	{
		$index = array_key_last( $this->mixedTypes );

		if ( null === $index )
		{
			throw new MissingIndexException( 'List is empty' );
		}

		return $this->mixedTypes[ $index ];
	}

	public function findIndex( MatchesType $matcher ): ?int
	{
		foreach ( $this->mixedTypes as $index => $type )
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
		return count( $this->mixedTypes );
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->mixedTypes, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->mixedTypes;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( !isset( $this->mixedTypes[ $index ] ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}

	private function existsIn( RepresentsType $typeToCheck, RepresentsList $list ): bool
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
