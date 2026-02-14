<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidStringTypeListException;
use ComponoKit\Types\Lists\Immutables\StringTypeList;
use ComponoKit\Types\Lists\Tests\Unit\fakes\AnotherStringType;
use ComponoKit\Types\Lists\Tests\Unit\fakes\IntegerType;
use ComponoKit\Types\Lists\Tests\Unit\fakes\StringType;
use ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes\AnotherStringTypeList;
use PHPUnit\Framework\TestCase;

class StringListTest extends TestCase
{
	public function testIfInvalidKeyThrowsException(): void
	{
		$this->expectExceptionObject( new InvalidStringTypeListException( 'Invalid index in ' . StringTypeList::class ) );

		/** @noinspection PhpParamsInspection */
		new StringTypeList( [ 'test-1' => new IntegerType( 1 ), 'test-2' => new IntegerType( 2 ) ] );
	}

	public function testIfInvalidValueThrowsException(): void
	{
		$this->expectExceptionObject( new InvalidStringTypeListException( 'Invalid value in ' . StringTypeList::class ) );

		/** @noinspection PhpParamsInspection */
		new StringTypeList( [ new IntegerType( 1 ), new IntegerType( 2 ) ] );
	}

	public function testEquals(): void
	{
		$this->assertTrue( (new StringTypeList( [] ))->equals( new StringTypeList( [] ) ) );
		$this->assertTrue(
			(new StringTypeList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equals(
				new StringTypeList(
					[
						new StringType( 'test-value-1' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-3' ),
					]
				)
			)
		);

		$this->assertFalse(
			(new StringTypeList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equals(
				new StringTypeList(
					[
						new StringType( 'test-value-3' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-1' ),
					]
				)
			)
		);
		$this->assertFalse( (new StringTypeList( [ new StringType( 'test-value-1' ) ] ))->equals( new StringTypeList( [ new AnotherStringType( 'test-value-1' ), ] ) ) );
		$this->assertFalse( (new StringTypeList( [] ))->equals( new StringTypeList( [ new StringType( 'test-value-1' ), ] ) ) );
		$this->assertFalse( (new StringTypeList( [ new StringType( 'test-value-1' ) ] ))->equals( new AnotherStringTypeList( [ new StringType( 'test-value-1' ), ] ) ) );
		$this->assertFalse( (new StringTypeList( [] ))->equals( new AnotherStringTypeList( [] ) ) );
		$this->assertFalse(
			(new StringTypeList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equals(
				new StringTypeList(
					[
						new StringType( 'test-value-3' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-1' ),
					]
				)
			)
		);
	}

	public function testEqualValues(): void
	{
		$this->assertTrue(
			(new StringTypeList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringTypeList(
					[
						new StringType( 'test-value-1' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-3' ),
					]
				)
			)
		);

		$this->assertFalse(
			(new StringTypeList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringTypeList(
					[
						new AnotherStringType( 'test-value-1' ),
						new AnotherStringType( 'test-value-2' ),
						new AnotherStringType( 'test-value-3' ),
					]
				)
			)
		);
		$this->assertFalse(
			(new StringTypeList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringTypeList(
					[
						new StringType( 'test-value-4' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-3' ),
					]
				)
			)
		);

		$this->assertFalse(
			(new StringTypeList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringTypeList(
					[
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-3' ),
					]
				)
			)
		);

		$this->assertFalse(
			(new StringTypeList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringTypeList(
					[
						new StringType( 'test-value-1' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-3' ),
					]
				)
			)
		);

		$this->assertFalse(
			(new StringTypeList(
				[
					0 => new StringType( 'test-value-1' ),
					1 => new StringType( 'test-value-2' ),
					2 => new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringTypeList(
					[
						1 => new StringType( 'test-value-1' ),
						2 => new StringType( 'test-value-2' ),
						3 => new StringType( 'test-value-3' ),
					]
				)
			)
		);
	}

	public function testFirst(): void
	{
		$this->assertEquals(
			new StringType( 'test-value-1' ),
			(new StringTypeList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->first()
		);
	}

	public function testLast(): void
	{
		$this->assertEquals(
			new StringType( 'test-value-3' ),
			(new StringTypeList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->last()
		);
	}

	public static function DiffTestDataProvider(): \Iterator
	{
		yield [
			[
				0 => new StringType( 'test-value-1' ),
				1 => new StringType( 'test-value-2' ),
				2 => new StringType( 'test-value-3' ),
			],
			[
				0 => new StringType( 'test-value-1' ),
				1 => new StringType( 'test-value-2' ),
				2 => new StringType( 'test-value-2.5' ),
				3 => new StringType( 'test-value-3.5' ),
			],
			[
				2 => new StringType( 'test-value-3' ),
			],
		];

		yield [
			[
				0 => new StringType( 'test-value-1' ),
				1 => new StringType( 'test-value-2' ),
				2 => new StringType( 'test-value-3' ),
			],
			[
				0 => new StringType( 'test-value-1' ),
				1 => new StringType( 'test-value-2' ),
				2 => new StringType( 'test-value-2' ),
				3 => new StringType( 'test-value-3.5' ),
			],
			[
				2 => new StringType( 'test-value-3' ),
			],
		];

		yield [
			[
				0 => new StringType( 'test-value-1' ),
				1 => new StringType( 'test-value-2' ),
				2 => new StringType( 'test-value-2' ),
				3 => new StringType( 'test-value-3' ),
			],
			[
				0 => new StringType( 'test-value-1' ),
				1 => new StringType( 'test-value-2' ),
				2 => new StringType( 'test-value-3.5' ),
			],
			[
				3 => new StringType( 'test-value-3' ),
			],
		];
	}

	/**
	 * @dataProvider DiffTestDataProvider
	 */
	public function testDiff( array $firstList, array $secondList, array $expectedList ): void
	{
		$this->assertEquals( new StringTypeList( $expectedList ), (new StringTypeList( $firstList ))->diff( new StringTypeList( $secondList ) ) );
	}
}
