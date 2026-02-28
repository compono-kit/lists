<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;

trait RepresentingStringList
{
	/** @var RepresentsString[] */
	private array $stringTypes;

	public function __construct( RepresentsString ...$stringTypes )
	{
		$this->stringTypes = $stringTypes;
	}

	public function exists( int $index ): bool
	{
		return isset( $this->stringTypes[ $index ] );
	}

	public function get( int $index ): RepresentsString
	{
		$this->guardIndexExists( $index );

		return $this->stringTypes[ $index ];
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsStringTypeList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsStringTypeList $list ): bool
	{
		if ( $list->count() !== $this->count() )
		{
			return false;
		}

		foreach ( $list as $index => $type )
		{
			if ( !isset( $this->stringTypes[ $index ] ) )
			{
				return false;
			}

			if ( !$type->equals( $this->stringTypes[ $index ] ) )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @return array<int, RepresentsString>
	 */
	public function toNativeType(): array
	{
		return $this->stringTypes;
	}

	/**
	 * @return array<int, string>
	 */
	public function toStrings(): array
	{
		return iterator_to_array( $this->iterateStrings() );
	}

	/**
	 * @return \Iterator<int, string>
	 */
	public function iterateStrings(): \Iterator
	{
		foreach ( $this->stringTypes as $key => $type )
		{
			yield (string)$key => $type->toString();
		}
	}

	public function next(): void
	{
		next( $this->stringTypes );
	}

	public function key(): int|null
	{
		return key( $this->stringTypes );
	}

	public function valid(): bool
	{
		return isset( $this->stringTypes[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->stringTypes );
	}

	public function current(): RepresentsString
	{
		return current( $this->stringTypes );
	}

	public function first(): RepresentsString
	{
		return $this->stringTypes[ array_keys( $this->stringTypes )[0] ];
	}

	public function last(): RepresentsString
	{
		return $this->stringTypes[ array_keys( $this->stringTypes )[ count( $this->stringTypes ) - 1 ] ];
	}

	public function findIndex( MatchesType $matcher ): ?int
	{
		foreach ( $this->stringTypes as $index => $type )
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
		return count( $this->stringTypes );
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->stringTypes, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->stringTypes;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( !isset( $this->stringTypes[ $index ] ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
