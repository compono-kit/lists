<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatValueList;

trait RepresentingFloatValueList
{
	/** @var float[] */
	private array $floats;

	public function __construct( float ...$floats )
	{
		$this->floats = $floats;
	}

	public function exists( int $index ): bool
	{
		return isset( $this->floats[ $index ] );
	}

	public function get( int $index ): float
	{
		$this->guardIndexExists( $index );

		return $this->floats[ $index ];
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
		 * @var RepresentsFloatValueList $type
		 */
		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsFloatValueList $list ): bool
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
	 * @return array<int, float>
	 */
	public function toNativeType(): array
	{
		return $this->floats;
	}

	public function next(): void
	{
		next( $this->floats );
	}

	public function key(): int|null
	{
		return key( $this->floats );
	}

	public function valid(): bool
	{
		return null !== key( $this->floats );
	}

	public function rewind(): void
	{
		reset( $this->floats );
	}

	public function current(): float
	{
		$index = key( $this->floats );

		if ( null === $index )
		{
			throw new MissingIndexException( 'Current index does not exist' );
		}

		return $this->floats[ $index ];
	}

	public function first(): float
	{
		$index = array_key_first( $this->floats );

		if ( null === $index )
		{
			throw new MissingIndexException( 'List is empty' );
		}

		return $this->floats[ $index ];
	}

	public function last(): float
	{
		$index = array_key_last( $this->floats );

		if ( null === $index )
		{
			throw new MissingIndexException( 'List is empty' );
		}

		return $this->floats[ $index ];
	}

	public function findIndex( MatchesValue $matcher ): ?int
	{
		foreach ( $this->floats as $index => $value )
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
		return count( $this->floats );
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->floats, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->floats;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( !isset( $this->floats[ $index ] ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}

	private function existsIn( float $valueToCheck, RepresentsList $list ): bool
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
