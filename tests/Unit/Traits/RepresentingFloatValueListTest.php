<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Traits;

use ComponoKit\Types\Lists\Traits\RepresentingFloatValueList;
use PHPUnit\Framework\TestCase;

class RepresentingFloatValueListTest extends TestCase
{
	public function testConstruct(): void
	{
		$testData = [ 1.1, 2.2 ];
		$test     = new class(...$testData) {
			use RepresentingFloatValueList;
		};

		self::assertSame( $testData, $test->toNativeType() );
	}
}
