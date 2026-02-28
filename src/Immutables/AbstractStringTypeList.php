<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\Factories\BuildsStringTypes;
use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Exceptions\InvalidStringTypeListException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableStringTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringValueList;
use ComponoKit\Types\Lists\Traits\RepresentingStringList;

abstract class AbstractStringTypeList implements RepresentsImmutableStringTypeList
{
	use RepresentingStringList;

	public function __construct( RepresentsString ...$stringTypes )
	{
		$this->validate( $stringTypes );

		$this->stringTypes = $stringTypes;
	}

	abstract protected static function isValueValid( RepresentsString $type ): bool;

	public static function fromValueList( RepresentsStringValueList $list, BuildsStringTypes $factory ): static
	{
		$types = [];
		foreach ( $list as $value )
		{
			$types[] = $factory->build( $value );
		}

		return new static( ...$types );
	}

	public function remove( int $index ): static
	{
		$this->guardIndexExists( $index );

		$types = $this->stringTypes;
		array_splice( $types, $index, 1 );

		return new static( ...$types );
	}

	public function replace( int $index, RepresentsString $type ): static
	{
		$this->guardIndexExists( $index );

		$types           = $this->stringTypes;
		$types[ $index ] = $type;

		return new static( ...$types );
	}

	public function append( RepresentsString...$types ): static
	{
		return new static( ...$this->stringTypes, ...$types );
	}

	public function prepend( RepresentsString...$types ): static
	{
		return new static( ...$types, ...$this->stringTypes );
	}

	public function appendList( RepresentsStringTypeList $list ): static
	{
		return new static( ...$this->toNativeType(), ...$list->toNativeType() );
	}

	public function prependList( RepresentsStringTypeList $list ): static
	{
		return new static( ...$list->toNativeType(), ...$this->toNativeType() );
	}

	public function reverse(): static
	{
		return new static( ...array_reverse( $this->stringTypes ) );
	}

	public function shuffle(): static
	{
		$types = $this->stringTypes;
		shuffle( $types );

		return new static( ...$types );
	}

	public function unique(): static
	{
		$uniqueTypes = new static();

		foreach ( $this->stringTypes as $type )
		{
			if ( !$this->existsIn( $type, $uniqueTypes ) )
			{
				$uniqueTypes = $uniqueTypes->append( $type );
			}
		}

		return $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): static
	{
		$types = $this->stringTypes;
		usort(
			$types,
			function ( RepresentsString $firstType, RepresentsString $secondType ) use ( $comparer ): int
			{
				return $comparer->compare( $firstType, $secondType );
			}
		);

		return new static( ...$types );
	}

	public function diff( RepresentsStringTypeList $list ): static
	{
		return new static(
			...array_udiff(
				$this->stringTypes,
				$list->toNativeType(),
				function ( RepresentsString $firstType, RepresentsString $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	public function intersect( RepresentsStringTypeList $list ): static
	{
		return new static(
			...array_uintersect(
				$this->stringTypes,
				$list->toNativeType(),
				function ( RepresentsString $firstType, RepresentsString $secondType ): int
				{
					return $firstType->equals( $secondType ) ? 0 : 1;
				}
			)
		);
	}

	/**
	 * @param int $count
	 *
	 * @return \Iterator<int, static>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->stringTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): static
	{
		return new static( ...array_slice( $this->stringTypes, $index, $count ) );
	}

	protected function validate( array $types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) )
			{
				throw (new InvalidStringTypeListException( 'Invalid index in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}

			if ( !($type instanceof RepresentsString) || !static::isValueValid( $type ) )
			{
				throw (new InvalidStringTypeListException( 'Invalid value in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}

	private function existsIn( RepresentsString $typeToCheck, RepresentsList $list ): bool
	{
		foreach ( $list as $type )
		{
			if ( $type->equals( $typeToCheck ) )
			{
				return true;
			}
		}

		return false;
	}
}
