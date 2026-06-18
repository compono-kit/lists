<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Traits;

use ComponoKit\Types\Lists\Traits\RepresentingIntegerValueList;
use PHPUnit\Framework\TestCase;

class RepresentingIntegerValueListTest extends TestCase
{
	public function testConstruct(): void
	{
		$testData = [ 1, 2 ];
		$test     = new class(...$testData) {
			use RepresentingIntegerValueList;
		};

		self::assertSame( $testData, $test->toNativeType() );
	}
}
