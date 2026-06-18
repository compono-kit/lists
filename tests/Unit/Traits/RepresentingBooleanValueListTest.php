<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Traits;

use ComponoKit\Types\Lists\Traits\RepresentingBooleanValueList;
use PHPUnit\Framework\TestCase;

class RepresentingBooleanValueListTest extends TestCase
{
	public function testConstruct(): void
	{
		$testData = [ true, false ];
		$test     = new class(...$testData) {
			use RepresentingBooleanValueList;
		};

		self::assertSame( $testData, $test->toNativeType() );
	}
}
