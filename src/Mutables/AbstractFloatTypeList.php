<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsFloat;
use ComponoKit\Types\Lists\Exceptions\InvalidFloatTypeListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableFloatTypeList;

abstract class AbstractFloatTypeList implements RepresentsMutableFloatTypeList
{
	use RepresentingMutableFloatTypeList;

	public function __construct( RepresentsFloat ...$floatTypes )
	{
		$this->validate( ...$floatTypes );

		$this->floatTypes = $floatTypes;
	}

	abstract protected static function isValueValid( RepresentsFloat $type ): bool;

	public function replace( int $index, RepresentsFloat $type ): void
	{
		$this->guardIndexExists( $index );
		$this->validate( $type );

		$this->floatTypes[ $index ] = $type;
	}

	public function append( RepresentsFloat ...$types ): void
	{
		$this->validate( ...$types );

		array_push( $this->floatTypes, ...$types );
	}

	public function prepend( RepresentsFloat ...$types ): void
	{
		$this->validate( ...$types );

		array_unshift( $this->floatTypes, ...$types );
	}

	public function appendList( RepresentsFloatTypeList $list ): void
	{
		$types = $list->toNativeType();
		$this->validate( ...$types );

		array_push( $this->floatTypes, ...$types );
	}

	public function prependList( RepresentsFloatTypeList $list ): void
	{
		$types = $list->toNativeType();
		$this->validate( ...$types );

		array_unshift( $this->floatTypes, ...$types );
	}

	public function diff( RepresentsFloatTypeList $list ): static
	{
		$types = [];
		foreach ( $this->floatTypes as $type )
		{
			if ( !$this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	public function intersect( RepresentsFloatTypeList $list ): static
	{
		$types = [];
		foreach ( $this->floatTypes as $type )
		{
			if ( $this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	/**
	 * @return \Iterator<int, RepresentsFloatTypeList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->floatTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->floatTypes = array_slice( $this->floatTypes, $index, $count );
	}

	protected function validate( RepresentsFloat ...$types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !static::isValueValid( $type ) )
			{
				throw (new InvalidFloatTypeListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
