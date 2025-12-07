<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Exceptions\InvalidIntegerListException;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableIntegerList;
use ComponoKit\Types\Lists\Mutables\Traits\RepresentingMutableIntegerList;

abstract class AbstractIntegerList implements RepresentsMutableIntegerList
{
	use RepresentingMutableIntegerList;

	/**
	 * @param RepresentsInteger[] $integerTypes
	 */
	public function __construct( private array $integerTypes )
	{
		$this->validate( $integerTypes );
	}

	abstract protected static function isValueValid( RepresentsInteger $type ): bool;

	/**
	 * @throws \JsonException
	 */
	public static function fromJson( string $json ): static
	{
		return new static( json_decode( $json, true, 512, JSON_THROW_ON_ERROR ) );
	}

	protected function validate( array $types ): void
	{
		foreach ( $types as $index => $type )
		{
			if ( !is_int( $index ) || !($type instanceof RepresentsInteger) || !static::isValueValid( $type ) )
			{
				throw (new InvalidIntegerListException( 'Invalid ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
