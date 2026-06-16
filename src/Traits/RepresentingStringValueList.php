<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\MatchesValue;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringValueList;

trait RepresentingStringValueList
{
	/** @var string[] */
	private array $strings;

	public function __construct( string ...$strings )
	{
		$this->strings = $strings;
	}

	public function exists( int $index ): bool
	{
		return isset( $this->strings[ $index ] );
	}

	public function get( int $index ): string
	{
		$this->guardIndexExists( $index );

		return $this->strings[ $index ];
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsStringValueList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsStringValueList $list ): bool
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
	 * @return array<int, string>
	 */
	public function toNativeType(): array
	{
		return $this->strings;
	}

	public function current(): string
	{
		return current( $this->strings );
	}

	public function next(): void
	{
		next( $this->strings );
	}

	public function key(): int|null
	{
		return key( $this->strings );
	}

	public function valid(): bool
	{
		return isset( $this->strings[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->strings );
	}

	public function first(): string
	{
		return $this->strings[ array_keys( $this->strings )[0] ];
	}

	public function last(): string
	{
		return $this->strings[ array_keys( $this->strings )[ count( $this->strings ) - 1 ] ];
	}

	public function count(): int
	{
		return count( $this->strings );
	}

	public function findIndex( MatchesValue $matcher ): ?int
	{
		foreach ( $this->strings as $index => $value )
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

		return json_encode( $this->strings, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->strings;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( !isset( $this->strings[ $index ] ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
