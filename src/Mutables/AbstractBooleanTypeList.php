<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsBoolean;
use ComponoKit\Types\Lists\Exceptions\InvalidBooleanTypeListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableBooleanTypeList;

abstract class AbstractBooleanTypeList implements RepresentsMutableBooleanTypeList
{
	use RepresentingMutableBooleanTypeList;

	public function __construct( RepresentsBoolean ...$booleanTypes )
	{
		$this->validate( ...$booleanTypes );

		$this->booleanTypes = $booleanTypes;
	}

	abstract protected static function isValueValid( RepresentsBoolean $type ): bool;

	public function replace( int $index, RepresentsBoolean $type ): void
	{
		$this->guardIndexExists( $index );
		$this->validate( $type );

		$this->booleanTypes[ $index ] = $type;
	}

	public function append( RepresentsBoolean ...$types ): void
	{
		$this->validate( ...$types );

		array_push( $this->booleanTypes, ...$types );
	}

	public function prepend( RepresentsBoolean ...$types ): void
	{
		$this->validate( ...$types );

		array_unshift( $this->booleanTypes, ...$types );
	}

	public function appendList( RepresentsBooleanTypeList $list ): void
	{
		$types = $list->toNativeType();
		$this->validate( ...$types );

		array_push( $this->booleanTypes, ...$types );
	}

	public function prependList( RepresentsBooleanTypeList $list ): void
	{
		$types = $list->toNativeType();
		$this->validate( ...$types );

		array_unshift( $this->booleanTypes, ...$types );
	}

	public function diff( RepresentsBooleanTypeList $list ): static
	{
		$types = [];
		foreach ( $this->booleanTypes as $type )
		{
			if ( !$this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	public function intersect( RepresentsBooleanTypeList $list ): static
	{
		$types = [];
		foreach ( $this->booleanTypes as $type )
		{
			if ( $this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	/**
	 * @return \Iterator<int, RepresentsBooleanTypeList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->booleanTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->booleanTypes = array_slice( $this->booleanTypes, $index, $count );
	}

	protected function validate( RepresentsBoolean ...$types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !static::isValueValid( $type ) )
			{
				throw (new InvalidBooleanTypeListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
