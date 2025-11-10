<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;

trait RepresentingMixedTypeList
{
	/**
	 * @param RepresentsType[] $mixedTypes
	 */
	public function __construct( private array $mixedTypes = [] )
	{
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsMixedTypeList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsMixedTypeList $list ): bool
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

	/**
	 * @return array<int, RepresentsType>
	 */
	public function toNativeType(): array
	{
		return $this->mixedTypes;
	}

	/**
	 * @return array<int, int|float|string|bool|array>
	 */
	public function toNativeTypes(): array
	{
		return iterator_to_array( $this->iterateNativeTypes() );
	}

	/**
	 * @return \Iterator<int, int|float|string|bool|array>
	 */
	public function iterateNativeTypes(): \Iterator
	{
		foreach ( $this->mixedTypes as $key => $type )
		{
			yield (string)$key => $type->toNativeType();
		}
	}

	public function current(): RepresentsType
	{
		return current( $this->mixedTypes );
	}

	public function next(): void
	{
		next( $this->mixedTypes );
	}

	public function key(): int|null
	{
		return key( $this->mixedTypes );
	}

	public function valid(): bool
	{
		return isset( $this->mixedTypes[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->mixedTypes );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->mixedTypes[ $index ] );
	}

	public function get( int $index ): RepresentsType
	{
		$this->guardIndexExists( $index );

		return $this->mixedTypes[ $index ];
	}

	public function count(): int
	{
		return count( $this->mixedTypes );
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->mixedTypes, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->mixedTypes;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( $this->exists( $index ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
