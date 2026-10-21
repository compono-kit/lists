<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesValues;
use ComponoKit\Types\Lists\Traits\RepresentingFloatValueList;

trait RepresentingMutableFloatValueList
{
	use RepresentingFloatValueList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->floats, $index, 1 );
	}

	public function reverse(): void
	{
		$this->floats = array_reverse( $this->floats );
	}

	public function shuffle(): void
	{
		shuffle( $this->floats );
	}

	public function unique(): void
	{
		$uniqueValues = [];

		foreach ( $this->floats as $value )
		{
			foreach ( $uniqueValues as $uniqueValue )
			{
				if ( $uniqueValue === $value )
				{
					continue 2;
				}
			}

			$uniqueValues[] = $value;
		}

		$this->floats = $uniqueValues;
	}

	public function sort( ComparesValues $comparer ): void
	{
		usort( $this->floats, [ $comparer, 'compare' ] );
	}
}
