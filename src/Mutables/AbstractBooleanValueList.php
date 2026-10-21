<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Lists\Exceptions\InvalidBooleanValueListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableBooleanValueList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableBooleanValueList;

abstract class AbstractBooleanValueList implements RepresentsMutableBooleanValueList
{
	use RepresentingMutableBooleanValueList;

	public function __construct( bool ...$booleans )
	{
		$this->validate( ...$booleans );

		$this->booleans = $booleans;
	}

	abstract protected static function isValueValid( bool $value ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( ...json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	public function replace( int $index, bool $value ): void
	{
		$this->guardIndexExists( $index );
		$this->validate( $value );

		$this->booleans[ $index ] = $value;
	}

	public function append( bool ...$values ): void
	{
		$this->validate( ...$values );

		array_push( $this->booleans, ...$values );
	}

	public function prepend( bool ...$values ): void
	{
		$this->validate( ...$values );

		array_unshift( $this->booleans, ...$values );
	}

	public function appendList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): void
	{
		$values = $list instanceof RepresentsBooleanTypeList ? $list->toBooleans() : $list->toNativeType();
		$this->validate( ...$values );

		array_push( $this->booleans, ...$values );
	}

	public function prependList( RepresentsBooleanValueList|RepresentsBooleanTypeList $list ): void
	{
		$values = $list instanceof RepresentsBooleanTypeList ? $list->toBooleans() : $list->toNativeType();
		$this->validate( ...$values );

		array_unshift( $this->booleans, ...$values );
	}

	public function diff( RepresentsBooleanValueList $list ): static
	{
		$values = [];
		foreach ( $this->booleans as $value )
		{
			if ( !$this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	public function intersect( RepresentsBooleanValueList $list ): static
	{
		$values = [];
		foreach ( $this->booleans as $value )
		{
			if ( $this->existsIn( $value, $list ) )
			{
				$values[] = $value;
			}
		}

		return new static( ...$values );
	}

	/**
	 * @return \Iterator<int, RepresentsBooleanValueList>
	 */
	public function split( int $count ): \Iterator
	{
		foreach ( array_chunk( $this->booleans, $count ) as $values )
		{
			yield new static( ...$values );
		}
	}

	public function slice( int $index, ?int $count = null ): void
	{
		$this->booleans = array_slice( $this->booleans, $index, $count );
	}

	protected function validate( bool ...$values ): void
	{
		foreach ( $values as $index => $value )
		{
			if ( !is_int( $index ) || !static::isValueValid( $value ) )
			{
				throw (new InvalidBooleanValueListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $value, ] );
			}
		}
	}
}
