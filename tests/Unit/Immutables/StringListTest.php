<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables;

use ComponoKit\Types\Lists\Exceptions\InvalidStringListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Immutables\StringList;
use ComponoKit\Types\Lists\Tests\Unit\fakes\AnotherStringType;
use ComponoKit\Types\Lists\Tests\Unit\fakes\IntegerType;
use ComponoKit\Types\Lists\Tests\Unit\fakes\StringType;
use ComponoKit\Types\Lists\Tests\Unit\Immutables\fakes\AnotherStringList;
use PHPUnit\Framework\TestCase;

class StringListTest extends TestCase
{
	public function testIfInvalidKeyThrowsException(): void
	{
		$this->expectExceptionObject( new InvalidStringListException( 'Invalid index in ' . StringList::class ) );

		/** @noinspection PhpParamsInspection */
		new StringList( [ 'test-1' => new IntegerType( 1 ), 'test-2' => new IntegerType( 2 ) ] );
	}

	public function testIfInvalidValueThrowsException(): void
	{
		$this->expectExceptionObject( new InvalidStringListException( 'Invalid value in ' . StringList::class ) );

		/** @noinspection PhpParamsInspection */
		new StringList( [ new IntegerType( 1 ), new IntegerType( 2 ) ] );
	}

	public function testEquals(): void
	{
		$this->assertTrue( (new StringList( [] ))->equals( new StringList( [] ) ) );
		$this->assertTrue(
			(new StringList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equals(
				new StringList(
					[
						new StringType( 'test-value-1' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-3' ),
					]
				)
			)
		);

		$this->assertFalse(
			(new StringList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equals(
				new StringList(
					[
						new StringType( 'test-value-3' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-1' ),
					]
				)
			)
		);
		$this->assertFalse( (new StringList( [ new StringType( 'test-value-1' ) ] ))->equals( new StringList( [ new AnotherStringType( 'test-value-1' ), ] ) ) );
		$this->assertFalse( (new StringList( [] ))->equals( new StringList( [ new StringType( 'test-value-1' ), ] ) ) );
		$this->assertFalse( (new StringList( [ new StringType( 'test-value-1' ) ] ))->equals( new AnotherStringList( [ new StringType( 'test-value-1' ), ] ) ) );
		$this->assertFalse( (new StringList( [] ))->equals( new AnotherStringList( [] ) ) );
		$this->assertFalse(
			(new StringList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equals(
				new StringList(
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
			(new StringList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringList(
					[
						new StringType( 'test-value-1' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-3' ),
					]
				)
			)
		);

		$this->assertFalse(
			(new StringList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringList(
					[
						new AnotherStringType( 'test-value-1' ),
						new AnotherStringType( 'test-value-2' ),
						new AnotherStringType( 'test-value-3' ),
					]
				)
			)
		);
		$this->assertFalse(
			(new StringList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringList(
					[
						new StringType( 'test-value-4' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-3' ),
					]
				)
			)
		);

		$this->assertFalse(
			(new StringList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-2' ),
					new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringList(
					[
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-3' ),
					]
				)
			)
		);

		$this->assertFalse(
			(new StringList(
				[
					new StringType( 'test-value-1' ),
					new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringList(
					[
						new StringType( 'test-value-1' ),
						new StringType( 'test-value-2' ),
						new StringType( 'test-value-3' ),
					]
				)
			)
		);

		$this->assertFalse(
			(new StringList(
				[
					0 => new StringType( 'test-value-1' ),
					1 => new StringType( 'test-value-2' ),
					2 => new StringType( 'test-value-3' ),
				]
			))->equalsValues(
				new StringList(
					[
						1 => new StringType( 'test-value-1' ),
						2 => new StringType( 'test-value-2' ),
						3 => new StringType( 'test-value-3' ),
					]
				)
			)
		);
	}
}
