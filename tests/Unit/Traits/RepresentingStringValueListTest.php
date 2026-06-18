<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Traits;

use ComponoKit\Types\Lists\Traits\RepresentingStringValueList;
use PHPUnit\Framework\TestCase;

class RepresentingStringValueListTest extends TestCase
{
	public function testConstruct(): void
	{
		$testData = [ 'foo', 'bar' ];
		$test     = new class(...$testData) {
			use RepresentingStringValueList;
		};

		self::assertSame( $testData, $test->toNativeType() );
	}
}
