<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Mutables\Traits;

use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Traits\RepresentingMixedTypeList;

trait RepresentingMutableMixedTypeList
{
	use RepresentingMixedTypeList;

	public function remove( int $index ): void
	{
		$this->guardIndexExists( $index );

		array_splice( $this->mixedTypes, $index, 1 );
	}

	public function reverse(): void
	{
		$this->mixedTypes = array_reverse( $this->mixedTypes );
	}

	public function shuffle(): void
	{
		shuffle( $this->mixedTypes );
	}

	public function unique(): void
	{
		$uniqueTypes = [];

		foreach ( $this->mixedTypes as $type )
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

		$this->mixedTypes = $uniqueTypes;
	}

	public function sort( ComparesTypes $comparer ): void
	{
		usort( $this->mixedTypes, [ $comparer, 'compare' ] );
	}
}
