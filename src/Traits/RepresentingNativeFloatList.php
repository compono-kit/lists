<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeFloatList;

trait RepresentingNativeFloatList
{
	/**
	 * @param float[] $floats
	 */
	public function __construct( private array $floats = [] )
	{
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsNativeFloatList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsNativeFloatList $list ): bool
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

	public function exists( int $index ): bool
	{
		return isset( $this->floats[ $index ] );
	}

	public function get( int $index ): float
	{
		$this->guardIndexExists( $index );

		return $this->floats[ $index ];
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
		if ( $this->exists( $index ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
