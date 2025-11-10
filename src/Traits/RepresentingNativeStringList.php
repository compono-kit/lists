<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeStringList;

trait RepresentingNativeStringList
{
	/**
	 * @param string[] $strings
	 */
	public function __construct( private array $strings = [] )
	{
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsNativeStringList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsNativeStringList $list ): bool
	{
		if ( $list->count() !== $this->count() )
		{
			return false;
		}

		foreach ( $list as $index => $value )
		{
			if ( !$this->exists( $index ) )
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

	public function exists( int $index ): bool
	{
		return isset( $this->strings[ $index ] );
	}

	public function get( int $index ): string
	{
		$this->guardIndexExists( $index );

		return $this->strings[ $index ];
	}

	public function count(): int
	{
		return count( $this->strings );
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
		if ( $this->exists( $index ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
