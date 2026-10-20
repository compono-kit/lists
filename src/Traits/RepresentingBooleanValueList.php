<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;

trait RepresentingBooleanValueList
{
	/** @var bool[] */
	private array $booleans;

	public function __construct( bool ...$booleans )
	{
		$this->booleans = $booleans;
	}

	public function exists( int $index ): bool
	{
		return isset( $this->booleans[ $index ] );
	}

	public function get( int $index ): bool
	{
		$this->guardIndexExists( $index );

		return $this->booleans[ $index ];
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
		 * @var RepresentsBooleanValueList $type
		 */
		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsBooleanValueList $list ): bool
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
	 * @return array<int, bool>
	 */
	public function toNativeType(): array
	{
		return $this->booleans;
	}

	public function next(): void
	{
		next( $this->booleans );
	}

	public function key(): int|null
	{
		return key( $this->booleans );
	}

	public function valid(): bool
	{
		return null !== key( $this->booleans );
	}

	public function rewind(): void
	{
		reset( $this->booleans );
	}

	public function current(): bool
	{
		$index = key( $this->booleans );

		if ( null === $index )
		{
			throw new MissingIndexException( 'Current index does not exist' );
		}

		return $this->booleans[ $index ];
	}

	public function first(): bool
	{
		$index = array_key_first( $this->booleans );

		if ( null === $index )
		{
			throw new MissingIndexException( 'List is empty' );
		}

		return $this->booleans[ $index ];
	}

	public function last(): bool
	{
		$index = array_key_last( $this->booleans );

		if ( null === $index )
		{
			throw new MissingIndexException( 'List is empty' );
		}

		return $this->booleans[ $index ];
	}

	public function findIndex( MatchesValue $matcher ): ?int
	{
		foreach ( $this->booleans as $index => $value )
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
		return count( $this->booleans );
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->booleans, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->booleans;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( !isset( $this->booleans[ $index ] ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}

	private function existsIn( bool $valueToCheck, RepresentsList $list ): bool
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
