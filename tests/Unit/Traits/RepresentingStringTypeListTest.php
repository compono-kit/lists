<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Traits;

use ComponoKit\Types\Lists\Traits\RepresentingStringTypeList;
use ComponoKit\Types\StringType;
use PHPUnit\Framework\TestCase;

class RepresentingStringTypeListTest extends TestCase
{
	public function testConstruct(): void
	{
		$testData = [ new StringType( 'foo' ), new StringType( 'bar' ) ];
		$test     = new class(...$testData) {
			use RepresentingStringTypeList;
		};

		self::assertSame( $testData, $test->toNativeType() );
	}
}
