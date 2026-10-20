<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerTypeList;

trait RepresentingMutableIntegerTypeList
{
	use RepresentingIntegerTypeList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->integerTypes, $index, 1 );
	}

	public function reverse(): void
	{
		$this->integerTypes = array_reverse( $this->integerTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->integerTypes );
	}

	public function unique(): void
	{
		$uniqueTypes = [];

		foreach ( $this->integerTypes as $type )
		{
			foreach ( $uniqueTypes as $uniqueType )
			{
				if ( $uniqueType->equals( $type ) )
				{
					continue 2;
				}
			}

			$uniqueTypes[] = $type;
		}

		$this->integerTypes = $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): void
	{
		usort( $this->integerTypes, $comparer->compare( ... ) );
	}
}
