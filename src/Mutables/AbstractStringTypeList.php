<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Exceptions\InvalidStringTypeListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableStringTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableStringTypeList;

abstract class AbstractStringTypeList implements RepresentsMutableStringTypeList
{
	use RepresentingMutableStringTypeList;

	public function __construct( RepresentsString ...$stringTypes )
	{
		$this->validate( ...$stringTypes );

		$this->stringTypes = $stringTypes;
	}

	abstract protected static function isValueValid( RepresentsString $type ): bool;

	public function replace( int $index, RepresentsString $type ): void
	{
		$this->guardIndexExists( $index );
		$this->validate( $type );

		$this->stringTypes[ $index ] = $type;
	}

	public function append( RepresentsString ...$types ): void
	{
		$this->validate( ...$types );

		array_push( $this->stringTypes, ...$types );
	}

	public function prepend( RepresentsString ...$types ): void
	{
		$this->validate( ...$types );

		array_unshift( $this->stringTypes, ...$types );
	}

	public function appendList( RepresentsStringTypeList $list ): void
	{
		$types = $list->toNativeType();
		$this->validate( ...$types );

		array_push( $this->stringTypes, ...$types );
	}

	public function prependList( RepresentsStringTypeList $list ): void
	{
		$types = $list->toNativeType();
		$this->validate( ...$types );

		array_unshift( $this->stringTypes, ...$types );
	}

	public function diff( RepresentsStringTypeList $list ): static
	{
		$types = [];
		foreach ( $this->stringTypes as $type )
		{
			if ( !$this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	public function intersect( RepresentsStringTypeList $list ): static
	{
		$types = [];
		foreach ( $this->stringTypes as $type )
		{
			if ( $this->existsIn( $type, $list ) )
			{
				$types[] = $type;
			}
		}

		return new static( ...$types );
	}

	/**
	 * @return \Iterator<int, RepresentsStringTypeList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->stringTypes, $count ) as $types )
		{
			yield new static( ...$types );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->stringTypes = array_slice( $this->stringTypes, $index, $count );
	}

	protected function validate( RepresentsString ...$types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !static::isValueValid( $type ) )
			{
				throw (new InvalidStringTypeListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
