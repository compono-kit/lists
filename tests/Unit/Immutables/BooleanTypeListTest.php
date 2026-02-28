<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Immutables;

use ComponoKit\Types\Lists\Immutables\BooleanTypeList;
use ComponoKit\Types\Lists\Tests\Unit\fakes\BooleanType;
use PHPUnit\Framework\TestCase;

class BooleanTypeListTest extends TestCase
{
	public static function equalsProvider(): iterable
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		yield 'same order' => [
			[ $type1, $type2 ],
			[ $type1, $type2 ],
			true,
		];

		yield 'different order' => [
			[ $type1, $type2 ],
			[ $type2, $type1 ],
			false,
		];

		yield 'different count' => [
			[ $type1 ],
			[ $type1, $type2 ],
			false,
		];
	}

	/**
	 * @dataProvider equalsProvider
	 */
	public function testEquals( array $types1, array $types2, bool $expected ): void
	{
		$list1 = new BooleanTypeList( ...$types1 );
		$list2 = new BooleanTypeList( ...$types2 );

		self::assertSame( $expected, $list1->equals( $list2 ) );
	}

	public function testRemove(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->remove( 0 );

		self::assertNotSame( $list1, $list2 );
		self::assertCount( 2, $list1 );
		self::assertCount( 1, $list2 );
	}

	public function testAppend(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->append( new BooleanType( true ) );

		self::assertNotSame( $list1, $list2 );
		self::assertCount( 2, $list1 );
		self::assertCount( 3, $list2 );

		self::assertEquals( new BooleanType( true ), $list2->get( 0 ) );
		self::assertEquals( new BooleanType( false ), $list2->get( 1 ) );
		self::assertEquals( new BooleanType( true ), $list2->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->prepend( new BooleanType( true ) );

		self::assertNotSame( $list1, $list2 );
		self::assertCount( 2, $list1 );
		self::assertCount( 3, $list2 );

		self::assertEquals( new BooleanType( true ), $list2->get( 0 ) );
		self::assertEquals( new BooleanType( true ), $list2->get( 1 ) );
		self::assertEquals( new BooleanType( false ), $list2->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->appendList( new BooleanTypeList( new BooleanType( true ) ) );

		self::assertNotSame( $list1, $list2 );
		self::assertCount( 2, $list1 );
		self::assertCount( 3, $list2 );

		self::assertEquals( new BooleanType( true ), $list2->get( 0 ) );
		self::assertEquals( new BooleanType( false ), $list2->get( 1 ) );
		self::assertEquals( new BooleanType( true ), $list2->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->prependList( new BooleanTypeList( new BooleanType( true ) ) );

		self::assertNotSame( $list1, $list2 );
		self::assertCount( 2, $list1 );
		self::assertCount( 3, $list2 );

		self::assertEquals( new BooleanType( true ), $list2->get( 0 ) );
		self::assertEquals( new BooleanType( true ), $list2->get( 1 ) );
		self::assertEquals( new BooleanType( false ), $list2->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list   = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ), new BooleanType( true ) );
		$unique = $list->unique();

		self::assertCount( 2, $unique );
		self::assertEquals( new BooleanType( true ), $list->get( 0 ) );
		self::assertEquals( new BooleanType( false ), $list->get( 1 ) );
	}

	public function testDiff(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->diff( new BooleanTypeList( new BooleanType( false ) ) );

		self::assertCount( 1, $list2 );
		self::assertEquals( new BooleanType( true ), $list2->get( 0 ) );
	}

	public function testIntersect(): void
	{
		$list1 = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list2 = $list1->intersect( new BooleanTypeList( new BooleanType( false ) ) );

		self::assertCount( 1, $list2 );
		self::assertEquals( new BooleanType( false ), $list2->get( 0 ) );
	}

	public function testSplit(): void
	{
		$list   = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ), new BooleanType( true ) );
		$chunks = iterator_to_array( $list->split( 2 ) );

		self::assertCount( 2, $chunks );
		self::assertCount( 2, $chunks[0] );
		self::assertCount( 1, $chunks[1] );
	}

	public function testSlice(): void
	{
		$list  = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ), new BooleanType( true ) );
		$slice = $list->slice( 1, 1 );

		self::assertCount( 1, $slice );
		self::assertEquals( new BooleanTypeList( new BooleanType( false ) ), $slice );
	}

	public function testShuffle(): void
	{
		$list     = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$shuffled = $list->shuffle();

		self::assertCount( 2, $shuffled );

		$values = $shuffled->toBooleans();
		sort( $values );

		self::assertSame( [ false, true ], $values );
	}
}
