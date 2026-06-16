<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;

trait RepresentingBooleanTypeList
{
	/** @var RepresentsBoolean[] */
	private array $booleanTypes;

	public function __construct( RepresentsBoolean ...$booleanTypes )
	{
		$this->booleanTypes = $booleanTypes;
	}

	public function exists( int $index ): bool
	{
		return isset( $this->booleanTypes[ $index ] );
	}

	public function get( int $index ): RepresentsBoolean
	{
		$this->guardIndexExists( $index );

		return $this->booleanTypes[ $index ];
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsBooleanTypeList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsBooleanTypeList $list ): bool
	{
		if ( $list->count() !== $this->count() )
		{
			return false;
		}

		foreach ( $list as $index => $type )
		{
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
		foreach ( $this->booleanTypes as $index => $type )
		{
			yield $index => $type->toBoolean();
		}
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

	public function current(): RepresentsBoolean
	{
		return current( $this->booleanTypes );
	}

	public function first(): RepresentsBoolean
	{
		return $this->booleanTypes[ array_keys( $this->booleanTypes )[0] ];
	}

	public function last(): RepresentsBoolean
	{
		return $this->booleanTypes[ array_keys( $this->booleanTypes )[ count( $this->booleanTypes ) - 1 ] ];
	}

	public function findIndex( MatchesType $matcher ): ?int
	{
		foreach ( $this->booleanTypes as $index => $type )
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
		return count( $this->booleanTypes );
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->booleanTypes, $flags );
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
