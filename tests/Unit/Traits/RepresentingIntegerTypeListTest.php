<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Traits;

use ComponoKit\Types\IntegerType;
use ComponoKit\Types\Lists\Traits\RepresentingIntegerTypeList;
use PHPUnit\Framework\TestCase;

class RepresentingIntegerTypeListTest extends TestCase
{
	public function testConstruct(): void
	{
		$testData = [ new IntegerType( 1 ), new IntegerType( 2 ) ];
		$test     = new class(...$testData) {
			use RepresentingIntegerTypeList;
		};

		self::assertSame( $testData, $test->toNativeType() );
	}

}
