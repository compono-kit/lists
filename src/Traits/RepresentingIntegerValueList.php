<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;

trait RepresentingIntegerValueList
{
	/** @var int[] */
	private array $integers;

	public function __construct( int ...$integers )
	{
		$this->integers = $integers;
	}

	public function exists( int $index ): bool
	{
		return isset( $this->integers[ $index ] );
	}

	public function get( int $index ): int
	{
		$this->guardIndexExists( $index );

		return $this->integers[ $index ];
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
		 * @var RepresentsIntegerValueList $type
		 */
		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsIntegerValueList $list ): bool
	{
		if ( $list->count() !== $this->count() )
		{
			return false;
		}

		foreach ( $list as $index => $value )
		{
			if ( $value !== $this->get( $index ) )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @return array<int, int>
	 */
	public function toNativeType(): array
	{
		return $this->integers;
	}

	public function next(): void
	{
		next( $this->integers );
	}

	public function key(): int|null
	{
		return key( $this->integers );
	}

	public function valid(): bool
	{
		return null !== key( $this->integers );
	}

	public function rewind(): void
	{
		reset( $this->integers );
	}

	public function current(): int
	{
		$index = key( $this->integers );

		if ( null === $index )
		{
			throw new MissingIndexException( 'Current index does not exist' );
		}

		return $this->integers[ $index ];
	}

	public function first(): int
	{
		$index = array_key_first( $this->integers );

		if ( null === $index )
		{
			throw new MissingIndexException( 'List is empty' );
		}

		return $this->integers[ $index ];
	}

	public function last(): int
	{
		$index = array_key_last( $this->integers );

		if ( null === $index )
		{
			throw new MissingIndexException( 'List is empty' );
		}

		return $this->integers[ $index ];
	}

	public function findIndex( MatchesValue $matcher ): ?int
	{
		foreach ( $this->integers as $index => $value )
		{
			if ( $matcher->matches( $value ) )
			{
				return $index;
			}
		}

		return null;
	}

	public function count(): int
	{
		return count( $this->integers );
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->integers, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->integers;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( !isset( $this->integers[ $index ] ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}

	private function existsIn( int $valueToCheck, RepresentsList $list ): bool
	{
		foreach ( $list as $item )
		{
			if ( $item === $valueToCheck )
			{
				return true;
			}
		}

		return false;
	}
}
