<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Exceptions\InvalidIntegerTypeListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableIntegerTypeList;

abstract class AbstractIntegerTypeList implements RepresentsMutableIntegerTypeList
{
	use RepresentingMutableIntegerTypeList;

	public function __construct( RepresentsInteger ...$integerTypes )
	{
		$this->validate( ...$integerTypes );

		$this->integerTypes = $integerTypes;
	}

	abstract protected static function isValueValid( RepresentsInteger $type ): bool;

	public function replace( int $index, RepresentsInteger $type ): void
	{
		$this->guardIndexExists( $index );
		$this->validate( $type );

		$this->integerTypes[ $index ] = $type;
	}

	public function append( RepresentsInteger ...$types ): void
	{
		$this->validate( ...$types );

		array_push( $this->integerTypes, ...$types );
	}

	public function prepend( RepresentsInteger ...$types ): void
	{
		$this->validate( ...$types );

		array_unshift( $this->integerTypes, ...$types );
	}

	public function appendList( RepresentsIntegerTypeList $list ): void
	{
		$types = $list->toNativeType();
		$this->validate( ...$types );

		array_push( $this->integerTypes, ...$types );
	}

	public function prependList( RepresentsIntegerTypeList $list ): void
	{
		$types = $list->toNativeType();
		$this->validate( ...$types );

		array_unshift( $this->integerTypes, ...$types );
	}

	public function diff( RepresentsIntegerTypeList $list ): static
	{
		$types = [];
		foreach ( $this->integerTypes as $type )
		{
			if ( !$this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	public function intersect( RepresentsIntegerTypeList $list ): static
	{
		$types = [];
		foreach ( $this->integerTypes as $type )
		{
			if ( $this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	/**
	 * @return \Iterator<int, RepresentsIntegerTypeList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->integerTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->integerTypes = array_slice( $this->integerTypes, $index, $count );
	}

	protected function validate( RepresentsInteger ...$types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !static::isValueValid( $type ) )
			{
				throw (new InvalidIntegerTypeListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
