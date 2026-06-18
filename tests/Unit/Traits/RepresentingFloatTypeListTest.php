<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Traits;

use ComponoKit\Types\FloatType;
use ComponoKit\Types\Lists\Traits\RepresentingFloatTypeList;
use PHPUnit\Framework\TestCase;

class RepresentingFloatTypeListTest extends TestCase
{
	public function testConstruct(): void
	{
		$testData = [ new FloatType( 1.1 ), new FloatType( 2.2 ) ];
		$test     = new class(...$testData) {
			use RepresentingFloatTypeList;
		};

		self::assertSame( $testData, $test->toNativeType() );
	}
}
