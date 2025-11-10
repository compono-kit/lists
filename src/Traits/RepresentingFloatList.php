<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatList;

trait RepresentingFloatList
{
	/**
	 * @param RepresentsFloat[] $floatTypes
	 */
	public function __construct( private array $floatTypes = [] )
	{
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsFloatList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsFloatList $list ): bool
	{
		if ( $list->count() !== $this->count() )
		{
			return false;
		}

		foreach ( $list as $index => $type )
		{
			if ( !$this->exists( $index ) )
			{
				return false;
			}

			if ( !$type->equals( $this->get( $index ) ) )
			{
				return false;
			}
		}

		return true;
	}

	public function toNativeType(): array
	{
		return $this->floatTypes;
	}

	/**
	 * @return array<int, float>
	 */
	public function toFloats(): array
	{
		return iterator_to_array( $this->iterateFloats() );
	}

	/**
	 * @return \Iterator<int, float>
	 */
	public function iterateFloats(): \Iterator
	{
		foreach ( $this->floatTypes as $key => $type )
		{
			yield (string)$key => $type->toFloat();
		}
	}

	public function current(): RepresentsFloat
	{
		return current( $this->floatTypes );
	}

	public function next(): void
	{
		next( $this->floatTypes );
	}

	public function key(): int|null
	{
		return key( $this->floatTypes );
	}

	public function valid(): bool
	{
		return isset( $this->floatTypes[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->floatTypes );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->floatTypes[ $index ] );
	}

	public function get( int $index ): RepresentsFloat
	{
		$this->guardIndexExists( $index );

		return $this->floatTypes[ $index ];
	}

	public function count(): int
	{
		return count( $this->floatTypes );
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->floatTypes, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->floatTypes;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( $this->exists( $index ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
