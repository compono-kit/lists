<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidStringValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableStringValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsStringValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableStringValueList;

abstract class AbstractStringValueList implements RepresentsMutableStringValueList
{
	use RepresentingMutableStringValueList;

	public function __construct( string ...$strings )
	{
		$this->validate( ...$strings );

		$this->strings = $strings;
	}

	abstract protected static function isValueValid( string $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function replace( int $index, string $value ): void
	{
		$this->guardIndexExists( $index );
		$this->validate( $value );

		$this->strings[ $index ] = $value;
	}

	public function append( string ...$values ): void
	{
		$this->validate( ...$values );

		array_push( $this->strings, ...$values );
	}

	public function prepend( string ...$values ): void
	{
		$this->validate( ...$values );

		array_unshift( $this->strings, ...$values );
	}

	public function appendList( RepresentsStringValueList|RepresentsStringTypeList $list ): void
	{
		$values = $list instanceof RepresentsStringTypeList ? $list->toStrings() : $list->toNativeType();
		$this->validate( ...$values );

		array_push( $this->strings, ...$values );
	}

	public function prependList( RepresentsStringValueList|RepresentsStringTypeList $list ): void
	{
		$values = $list instanceof RepresentsStringTypeList ? $list->toStrings() : $list->toNativeType();
		$this->validate( ...$values );

		array_unshift( $this->strings, ...$values );
	}

	public function diff( RepresentsStringValueList $list ): static
	{
		$values = [];
		foreach ( $this->strings as $value )
		{
			if ( !$this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	public function intersect( RepresentsStringValueList $list ): static
	{
		$values = [];
		foreach ( $this->strings as $value )
		{
			if ( $this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	/**
	 * @return \Iterator<int, RepresentsStringValueList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->strings, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->strings = array_slice( $this->strings, $index, $count );
	}

	protected function validate( string ...$values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidStringValueListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
