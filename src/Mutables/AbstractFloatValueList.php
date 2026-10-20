<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidFloatValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableFloatValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsFloatValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableFloatValueList;

abstract class AbstractFloatValueList implements RepresentsMutableFloatValueList
{
	use RepresentingMutableFloatValueList;

	public function __construct( float ...$floats )
	{
		$this->validate( ...$floats );

		$this->floats = $floats;
	}

	abstract protected static function isValueValid( float $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function replace( int $index, float $value ): void
	{
		$this->guardIndexExists( $index );
		$this->validate( $value );

		$this->floats[ $index ] = $value;
	}

	public function append( float ...$values ): void
	{
		$this->validate( ...$values );

		array_push( $this->floats, ...$values );
	}

	public function prepend( float ...$values ): void
	{
		$this->validate( ...$values );

		array_unshift( $this->floats, ...$values );
	}

	public function appendList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): void
	{
		$values = $list instanceof RepresentsFloatTypeList ? $list->toFloats() : $list->toNativeType();
		$this->validate( ...$values );

		array_push( $this->floats, ...$values );
	}

	public function prependList( RepresentsFloatValueList|RepresentsFloatTypeList $list ): void
	{
		$values = $list instanceof RepresentsFloatTypeList ? $list->toFloats() : $list->toNativeType();
		$this->validate( ...$values );

		array_unshift( $this->floats, ...$values );
	}

	public function diff( RepresentsFloatValueList $list ): static
	{
		$values = [];
		foreach ( $this->floats as $value )
		{
			if ( !$this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	public function intersect( RepresentsFloatValueList $list ): static
	{
		$values = [];
		foreach ( $this->floats as $value )
		{
			if ( $this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	/**
	 * @return \Iterator<int, RepresentsFloatValueList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->floats, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->floats = array_slice( $this->floats, $index, $count );
	}

	protected function validate( float ...$values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !static::isValueValid( $value ) )
			{
				throw new InvalidFloatValueListException( 'Invalid ' . static::class )->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
