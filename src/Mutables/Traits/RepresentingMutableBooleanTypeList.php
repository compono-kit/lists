<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanTypeList;

trait RepresentingMutableBooleanTypeList
{
	use RepresentingBooleanTypeList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->booleanTypes, $index, 1 );
	}

	public function reverse(): void
	{
		$this->booleanTypes = array_reverse( $this->booleanTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->booleanTypes );
	}

	public function unique(): void
	{
		$uniqueTypes = [];

		foreach ( $this->booleanTypes as $type )
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

		$this->booleanTypes = $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): void
	{
		usort( $this->booleanTypes, [ $comparer, 'compare' ] );
	}
}
