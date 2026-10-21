<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidIntegerValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableIntegerValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsIntegerValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableIntegerValueList;

abstract class AbstractIntegerValueList implements RepresentsMutableIntegerValueList
{
	use RepresentingMutableIntegerValueList;

	public function __construct( int ...$integers )
	{
		$this->validate( ...$integers );

		$this->integers = $integers;
	}

	abstract protected static function isValueValid( int $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function replace( int $index, int $value ): void
	{
		$this->guardIndexExists( $index );
		$this->validate( $value );

		$this->integers[ $index ] = $value;
	}

	public function append( int ...$values ): void
	{
		$this->validate( ...$values );

		array_push( $this->integers, ...$values );
	}

	public function prepend( int ...$values ): void
	{
		$this->validate( ...$values );

		array_unshift( $this->integers, ...$values );
	}

	public function appendList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): void
	{
		$values = $list instanceof RepresentsIntegerTypeList ? $list->toIntegers() : $list->toNativeType();
		$this->validate( ...$values );

		array_push( $this->integers, ...$values );
	}

	public function prependList( RepresentsIntegerValueList|RepresentsIntegerTypeList $list ): void
	{
		$values = $list instanceof RepresentsIntegerTypeList ? $list->toIntegers() : $list->toNativeType();
		$this->validate( ...$values );

		array_unshift( $this->integers, ...$values );
	}

	public function diff( RepresentsIntegerValueList $list ): static
	{
		$values = [];
		foreach ( $this->integers as $value )
		{
			if ( !$this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	public function intersect( RepresentsIntegerValueList $list ): static
	{
		$values = [];
		foreach ( $this->integers as $value )
		{
			if ( $this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	/**
	 * @return \Iterator<int, RepresentsIntegerValueList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->integers, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->integers = array_slice( $this->integers, $index, $count );
	}

	protected function validate( int ...$values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidIntegerValueListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
