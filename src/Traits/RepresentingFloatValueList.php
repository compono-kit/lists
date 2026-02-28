<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
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
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsFloatValueList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

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
			if ( !isset( $this->floats[ $index ] ) )
			{
				return false;
			}

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

	public function current(): float
	{
		return current( $this->floats );
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
		return isset( $this->floats[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->floats );
	}

	public function first(): float
	{
		return $this->floats[ array_keys( $this->floats )[0] ];
	}

	public function last(): float
	{
		return $this->floats[ array_keys( $this->floats )[ count( $this->floats ) - 1 ] ];
	}

	public function count(): int
	{
		return count( $this->floats );
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
}
