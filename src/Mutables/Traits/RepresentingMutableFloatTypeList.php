<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Traits\RepresentingFloatTypeList;

trait RepresentingMutableFloatTypeList
{
	use RepresentingFloatTypeList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->floatTypes, $index, 1 );
	}

	public function reverse(): void
	{
		$this->floatTypes = array_reverse( $this->floatTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->floatTypes );
	}

	public function unique(): void
	{
		$uniqueTypes = [];

		foreach ( $this->floatTypes as $type )
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

		$this->floatTypes = $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): void
	{
		usort( $this->floatTypes, [ $comparer, 'compare' ] );
	}
}
