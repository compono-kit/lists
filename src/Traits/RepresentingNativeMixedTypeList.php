<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeMixedTypeList;

trait RepresentingNativeMixedTypeList
{
	/**
	 * @param array<int, int|float|string|bool|array> $nativeMixedTypes
	 */
	public function __construct( private array $nativeMixedTypes = [] )
	{
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsNativeMixedTypeList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsNativeMixedTypeList $list ): bool
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
	 * @return array<int, int|float|string|bool|array>
	 */
	public function toNativeType(): array
	{
		return $this->nativeMixedTypes;
	}

	public function current(): int|float|string|bool|array
	{
		return current( $this->nativeMixedTypes );
	}

	public function next(): void
	{
		next( $this->nativeMixedTypes );
	}

	public function key(): int|null
	{
		return key( $this->nativeMixedTypes );
	}

	public function valid(): bool
	{
		return isset( $this->nativeMixedTypes[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->nativeMixedTypes );
	}

	public function exists( int $index ): bool
	{
		return isset( $this->nativeMixedTypes[ $index ] );
	}

	public function get( int $index ): int|float|string|bool|array
	{
		$this->guardIndexExists( $index );

		return $this->nativeMixedTypes[ $index ];
	}

	public function count(): int
	{
		return count( $this->nativeMixedTypes );
	}

	public function toJson( bool $pretty = false ): string
	{
		$flags = JSON_THROW_ON_ERROR;
		if ( $pretty )
		{
			$flags |= JSON_PRETTY_PRINT;
		}

		return json_encode( $this->nativeMixedTypes, $flags );
	}

	public function jsonSerialize(): array
	{
		return $this->nativeMixedTypes;
	}

	private function guardIndexExists( int $index ): void
	{
		if ( $this->exists( $index ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
