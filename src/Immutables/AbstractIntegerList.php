<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsInteger;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableIntegerList;
use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableIntegerList;
use ComponoKit\Types\Lists\Exceptions\InvalidIntegerListException;

abstract class AbstractIntegerList implements RepresentsImmutableIntegerList
{
	use RepresentingImmutableIntegerList;

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
		foreach ( $types as $type )
		{
			if ( !($type instanceof RepresentsInteger) || !static::isValueValid( $type ) )
			{
				throw (new InvalidIntegerListException( 'Invalid ' . static::class ))->setContext( [ 'value' => $type, ] );
			}
		}
	}
}
