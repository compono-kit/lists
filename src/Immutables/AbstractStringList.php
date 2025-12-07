<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Immutables;

use ComponoKit\Types\Interfaces\RepresentsString;
use ComponoKit\Types\Lists\Exceptions\InvalidStringListException;
use ComponoKit\Types\Lists\Immutables\Traits\RepresentingImmutableStringList;
use ComponoKit\Types\Lists\Interfaces\Immutables\RepresentsImmutableStringList;

abstract class AbstractStringList implements RepresentsImmutableStringList
{
	use RepresentingImmutableStringList;

	/**
	 * @param RepresentsString[] $stringTypes
	 */
	public function __construct( private array $stringTypes )
	{
		$this->validate( $stringTypes );
	}

	abstract protected static function isValueValid( RepresentsString $type ): bool;

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
			if ( !is_int( $index ) )
			{
				throw (new InvalidStringListException( 'Invalid index in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}

			if ( !($type instanceof RepresentsString) || !static::isValueValid( $type ) )
			{
				throw (new InvalidStringListException( 'Invalid value in ' . static::class ))->setContext( [ 'index' => $index, 'value' => $type, ] );
			}
		}
	}
}
