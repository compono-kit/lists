<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Traits;

use ComponoKit\Types\BooleanType;
use ComponoKit\Types\Lists\Traits\RepresentingBooleanTypeList;
use PHPUnit\Framework\TestCase;

class RepresentingBooleanTypeListTest extends TestCase
{
	public function testConstruct(): void
	{
		$testData = [ new BooleanType( true ), new BooleanType( false ) ];
		$test     = new class(...$testData) {
			use RepresentingBooleanTypeList;
		};

		self::assertSame( $testData, $test->toNativeType() );
	}
}
