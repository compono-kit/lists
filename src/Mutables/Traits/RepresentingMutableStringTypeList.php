<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Traits\RepresentingStringTypeList;

trait RepresentingMutableStringTypeList
{
	use RepresentingStringTypeList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->stringTypes, $index, 1 );
	}

	public function reverse(): void
	{
		$this->stringTypes = array_reverse( $this->stringTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->stringTypes );
	}

	public function unique(): void
	{
		$uniqueTypes = [];

		foreach ( $this->stringTypes as $type )
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

		$this->stringTypes = $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): void
	{
		usort( $this->stringTypes, [ $comparer, 'compare' ] );
	}
}
