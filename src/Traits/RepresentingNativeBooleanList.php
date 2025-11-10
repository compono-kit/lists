<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Interfaces\RepresentsNativeBooleanList;

trait RepresentingNativeBooleanList
{
	/**
	 * @param bool[] $booleans
	 */
	public function __construct( private array $booleans = [] )
	{
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsNativeBooleanList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsNativeBooleanList $list ): bool
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
	 * @return array<int, bool>
	 */
	public function toNativeType(): array
	{
		return $this->booleans;
	}

	public function current(): bool
	{
		return current( $this->booleans );
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
		return isset( $this->booleans[ $this->key() ] );
	}

	public function rewind(): void
	{
		reset( $this->booleans );
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
		if ( $this->exists( $index ) )
		{
			throw new MissingIndexException( 'Index does not exist: ' . $index );
		}
	}
}
