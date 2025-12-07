<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Traits;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringList;

trait RepresentingStringList
{
	/**
	 * @param RepresentsString[] $stringTypes
	 */
	public function __construct( private array $stringTypes = [] )
	{
	}

	public function equals( RepresentsType $type ): bool
	{
		if ( get_class( $this ) !== get_class( $type ) )
		{
			return false;
		}

		/**
		 * @var RepresentsStringList $type
		 */
		if ( $this->count() !== $type->count() )
		{
			return false;
		}

		return $this->equalsValues( $type );
	}

	public function equalsValues( RepresentsStringList $list ): bool
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

	public function current(): RepresentsString
	{
		return current( $this->stringTypes );
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

	public function count(): int
	{
		return count( $this->stringTypes );
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
