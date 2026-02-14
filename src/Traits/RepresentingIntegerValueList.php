<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;

trait RepresentingIntegerValueList
{
	/**
	 * @param int[] $integers
	 */
	public function __construct( private array $integers = [] )
	{
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsIntegerValueList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

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
			if ( !isset( $this->integers[ $index ] ) )
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
	 * @return array<int, int>
	 */
	public function toNativeType(): array
	{
		return $this->integers;
	}

	public function current(): int
	{
		return current( $this->integers );
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
		return isset( $this->integers[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->integers );
	}

	public function first(): int
	{
		return $this->integers[ array_keys( $this->integers )[0] ];
	}

	public function last(): int
	{
		return $this->integers[ array_keys( $this->integers )[ count( $this->integers ) - 1 ] ];
	}

	public function count(): int
	{
		return count( $this->integers );
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
}
