<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidMixedValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableMixedValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsMixedValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableMixedValueList;

abstract class AbstractMixedValueList implements RepresentsMutableMixedValueList
{
	use RepresentingMutableMixedValueList;

	public function __construct( int|float|string|bool|array ...$mixedValues )
	{
		$this->validate( ...$mixedValues );

		$this->mixedValues = $mixedValues;
	}

	abstract protected static function isValueValid( int|float|string|bool|array $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function replace( int $index, int|float|string|bool|array $value ): void
	{
		$this->guardIndexExists( $index );
		$this->validate( $value );

		$this->mixedValues[ $index ] = $value;
	}

	public function append( int|float|string|bool|array ...$values ): void
	{
		$this->validate( ...$values );

		array_push( $this->mixedValues, ...$values );
	}

	public function prepend( int|float|string|bool|array ...$values ): void
	{
		$this->validate( ...$values );

		array_unshift( $this->mixedValues, ...$values );
	}

	public function appendList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): void
	{
		$values = $list instanceof RepresentsMixedTypeList ? $list->toNativeTypes() : $list->toNativeType();
		$this->validate( ...$values );

		array_push( $this->mixedValues, ...$values );
	}

	public function prependList( RepresentsMixedValueList|RepresentsMixedTypeList $list ): void
	{
		$values = $list instanceof RepresentsMixedTypeList ? $list->toNativeTypes() : $list->toNativeType();
		$this->validate( ...$values );

		array_unshift( $this->mixedValues, ...$values );
	}

	public function diff( RepresentsMixedValueList $list ): static
	{
		$values = [];
		foreach ( $this->mixedValues as $value )
		{
			if ( !$this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	public function intersect( RepresentsMixedValueList $list ): static
	{
		$values = [];
		foreach ( $this->mixedValues as $value )
		{
			if ( $this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	/**
	 * @return \Iterator<int, RepresentsMixedValueList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->mixedValues, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->mixedValues = array_slice( $this->mixedValues, $index, $count );
	}

	protected function validate( int|float|string|bool|array ...$values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidMixedValueListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
