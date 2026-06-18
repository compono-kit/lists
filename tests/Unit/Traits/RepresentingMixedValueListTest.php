<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Traits;

use ComponoKit\Types\Lists\Traits\RepresentingMixedValueList;
use PHPUnit\Framework\TestCase;

class RepresentingMixedValueListTest extends TestCase
{
	public function testConstruct(): void
	{
		$testData = [ true, 2, 3.3, 'foo' ];
		$test     = new class(...$testData) {
			use RepresentingMixedValueList;
		};

		self::assertSame( $testData, $test->toNativeType() );
	}
}
