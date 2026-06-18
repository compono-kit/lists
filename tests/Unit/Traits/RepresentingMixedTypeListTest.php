<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Traits;

use ComponoKit\Types\BooleanType;
use ComponoKit\Types\FloatType;
use ComponoKit\Types\IntegerType;
use ComponoKit\Types\Lists\Traits\RepresentingMixedTypeList;
use ComponoKit\Types\StringType;
use PHPUnit\Framework\TestCase;

class RepresentingMixedTypeListTest extends TestCase
{
	public function testConstruct(): void
	{
		$testData = [ new BooleanType( true ), new IntegerType( 2 ), new FloatType( 3.3 ), new StringType( 'foo' ) ];
		$test     = new class(...$testData) {
			use RepresentingMixedTypeList;
		};

		self::assertSame( $testData, $test->toNativeType() );
	}
}
