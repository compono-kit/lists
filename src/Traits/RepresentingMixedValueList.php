<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedValueList;

trait RepresentingMixedValueList
{
	/** @var array<int, int|float|string|bool|array> */
	private array $mixedValues;

	public function __construct( int|float|string|bool|array ...$mixedValues )
	{
		$this->mixedValues = $mixedValues;
	}

	public function exists( int $index ): bool
	{
		return isset( $this->mixedValues[ $index ] );
	}

	public function get( int $index ): int|float|string|bool|array
	{
		$this->guardIndexExists( $index );

		return $this->mixedValues[ $index ];
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsMixedValueList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsMixedValueList $list ): bool
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
	 * @return array<int, int|float|string|bool|array>
	 */
	public function toNativeType(): array
	{
		return $this->mixedValues;
	}

	public function first(): int|float|string|bool|array
	{
		return $this->mixedValues[ array_keys( $this->mixedValues )[0] ];
	}

	public function last(): int|float|string|bool|array
	{
		return $this->mixedValues[ array_keys( $this->mixedValues )[ count( $this->mixedValues ) - 1 ] ];
	}

	public function current(): int|float|string|bool|array
	{
		return current( $this->mixedValues );
	}

	public function next(): void
	{
		next( $this->mixedValues );
	}

	public function key(): int|null
	{
		return key( $this->mixedValues );
	}

	public function valid(): bool
	{
		return isset( $this->mixedValues[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->mixedValues );
	}

	public function count(): int
	{
		return count( $this->mixedValues );
	}

	public function findIndex( MatchesValue $matcher ): ?int
	{
		foreach ( $this->mixedValues as $index => $value )
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

		return json_encode( $this->mixedValues, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->mixedValues;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( !isset( $this->mixedValues[ $index ] ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
