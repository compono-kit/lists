<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit\Mutables;

use ComponoKit\Types\BooleanType;
use ComponoKit\Types\Interfaces\Factories\BuildsBooleanTypes;
use ComponoKit\Types\Interfaces\RepresentsType;
use ComponoKit\Types\Lists\Exceptions\InvalidBooleanTypeListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Interfaces\ComparesTypes;
use ComponoKit\Types\Lists\Interfaces\MatchesType;
use ComponoKit\Types\Lists\Interfaces\Mutables\RepresentsMutableBooleanTypeList;
use ComponoKit\Types\Lists\Interfaces\RepresentsBooleanValueList;
use ComponoKit\Types\Lists\Mutables\BooleanTypeList;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\OnlyTrueBooleanTypeList;
use PHPUnit\Framework\TestCase;

class BooleanTypeListTest extends TestCase
{
	public static function EqualsDataProvider(): \Iterator
	{
		yield 'same order' => [
			new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) ),
			new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) ),
			true,
		];

		yield 'different order' => [
			new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) ),
			new BooleanTypeList( new BooleanType( false ), new BooleanType( true ) ),
			false,
		];

		yield 'different count' => [
			new BooleanTypeList( new BooleanType( true ) ),
			new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) ),
			false,
		];

		yield 'unequal types' => [
			new BooleanTypeList( new BooleanType( true ) ),
			new OnlyTrueBooleanTypeList( new BooleanType( true ), ),
			false,
		];
	}

	/**
	 * @dataProvider EqualsDataProvider
	 */
	public function testEquals( RepresentsMutableBooleanTypeList $list1, RepresentsMutableBooleanTypeList $list2, bool $expected ): void
	{
		self::assertSame( $expected, $list1->equals( $list2 ) );
	}

	public static function EqualsValuesDataProvider(): \Iterator
	{
		yield 'same values same order' => [
			[ new BooleanType( true ), new BooleanType( false ) ],
			[ new BooleanType( true ), new BooleanType( false ) ],
			true,
		];

		yield 'same values different order' => [
			[ new BooleanType( true ), new BooleanType( false ) ],
			[ new BooleanType( false ), new BooleanType( true ) ],
			false,
		];

		yield 'different values' => [
			[ new BooleanType( true ) ],
			[ new BooleanType( false ) ],
			false,
		];

		yield 'different count' => [
			[ new BooleanType( true ) ],
			[ new BooleanType( true ), new BooleanType( true ), ],
			false,
		];
	}

	/**
	 * @dataProvider EqualsValuesDataProvider
	 */
	public function testEqualsValues( array $values1, array $values2, bool $expected ): void
	{
		$list1 = new BooleanTypeList( ...$values1 );
		$list2 = new BooleanTypeList( ...$values2 );

		self::assertSame( $expected, $list1->equalsValues( $list2 ) );
	}

	public function testRemove(): void
	{
		$list = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list->remove( 0 );

		self::assertCount( 1, $list );
	}

	public function testAppend(): void
	{
		$list = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list->append( new BooleanType( true ) );

		self::assertCount( 3, $list );

		self::assertEquals( new BooleanType( true ), $list->get( 0 ) );
		self::assertEquals( new BooleanType( false ), $list->get( 1 ) );
		self::assertEquals( new BooleanType( true ), $list->get( 2 ) );
	}

	public function testPrepend(): void
	{
		$list = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list->prepend( new BooleanType( true ) );

		self::assertCount( 3, $list );

		self::assertEquals( new BooleanType( true ), $list->get( 0 ) );
		self::assertEquals( new BooleanType( true ), $list->get( 1 ) );
		self::assertEquals( new BooleanType( false ), $list->get( 2 ) );
	}

	public function testAppendList(): void
	{
		$list = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list->appendList( new BooleanTypeList( new BooleanType( true ) ) );

		self::assertCount( 3, $list );

		self::assertEquals( new BooleanType( true ), $list->get( 0 ) );
		self::assertEquals( new BooleanType( false ), $list->get( 1 ) );
		self::assertEquals( new BooleanType( true ), $list->get( 2 ) );
	}

	public function testPrependList(): void
	{
		$list = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list->prependList( new BooleanTypeList( new BooleanType( true ) ) );

		self::assertCount( 3, $list );

		self::assertEquals( new BooleanType( true ), $list->get( 0 ) );
		self::assertEquals( new BooleanType( true ), $list->get( 1 ) );
		self::assertEquals( new BooleanType( false ), $list->get( 2 ) );
	}

	public function testUnique(): void
	{
		$list = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ), new BooleanType( true ) );
		$list->unique();

		self::assertCount( 2, $list );
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
		$list = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ), new BooleanType( true ) );
		$list->slice( 1, 1 );

		self::assertCount( 1, $list );
		self::assertEquals( new BooleanTypeList( new BooleanType( false ) ), $list );
	}

	public function testShuffle(): void
	{
		$list = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );
		$list->shuffle();

		self::assertCount( 2, $list );

		$values = $list->toBooleans();
		sort( $values );

		self::assertSame( [ false, true ], $values );
	}

	public function testSort(): void
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		$list = new BooleanTypeList( $type1, $type2 );

		$comparer = new class implements ComparesTypes {
			public function compare( RepresentsType $firstType, RepresentsType $secondType ): int
			{
				return $firstType->toNativeType() <=> $secondType->toNativeType();
			}
		};

		$list->sort( $comparer );

		self::assertSame( [ false, true ], $list->toBooleans() );
	}

	public function testReverse(): void
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		$list = new BooleanTypeList( $type1, $type2 );
		$list->reverse();

		self::assertSame( [ false, true ], $list->toBooleans() );
	}

	public static function ReplaceDataProvider(): \Iterator
	{
		yield 'replace first' => [
			[ new BooleanType( false ), new BooleanType( true ) ],
			0,
			true,
		];

		yield 'replace second' => [
			[ new BooleanType( true ), new BooleanType( false ) ],
			1,
			true,
		];
	}

	/**
	 * @dataProvider ReplaceDataProvider
	 */
	public function testReplace( array $values, int $index, bool $expected ): void
	{
		$list  = new BooleanTypeList( ...$values );
		$type1 = new BooleanType( $expected );

		$list->replace( $index, $type1 );

		self::assertSame( $expected, $list->get( $index )->toBoolean() );
	}

	public function testReplaceThrowsException(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new BooleanTypeList( new BooleanType( true ) );
		$list->replace( 99, new BooleanType( false ) );
	}

	public static function ExistsDataProvider(): \Iterator
	{
		yield 'index exists' => [
			[ new BooleanType( true ) ],
			0,
			true,
		];

		yield 'index does not exist' => [
			[ new BooleanType( true ) ],
			1,
			false,
		];
	}

	/**
	 * @dataProvider ExistsDataProvider
	 */
	public function testExists( array $values, int $index, bool $expected ): void
	{
		$list = new BooleanTypeList( ...$values );

		self::assertSame( $expected, $list->exists( $index ) );
	}

	public function testGet(): void
	{
		$list = new BooleanTypeList(
			new BooleanType( true ),
			new BooleanType( false ),
			new BooleanType( true ),
			new BooleanType( false ),
		);

		self::assertEquals( new BooleanType( false ), $list->get( 3 ) );
	}

	public function testGetThrowsExceptionIfIndexNotExists(): void
	{
		$this->expectException( MissingIndexException::class );

		$list = new BooleanTypeList( new BooleanType( true ) );
		$list->get( 42 );
	}

	public static function fromValueList( RepresentsBooleanValueList $list, BuildsBooleanTypes $factory ): static
	{
		$types = [];
		foreach ( $list as $value )
		{
			$types[] = $factory->build( $value );
		}

		return new static( ...$types );
	}

	public function testToJson(): void
	{
		$list = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );

		$json    = $list->toJson();
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ true, false ], $decoded );
	}

	public function testJsonSerialize(): void
	{
		$list = new BooleanTypeList( new BooleanType( true ), new BooleanType( false ) );

		$json    = json_encode( $list );
		$decoded = json_decode( $json, true, 512, JSON_THROW_ON_ERROR );

		self::assertSame( [ true, false ], $decoded );
	}

	public function testFirst(): void
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		$list = new BooleanTypeList( $type1, $type2 );

		self::assertEquals( $type1, $list->first() );
	}

	public function testLast(): void
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		$list = new BooleanTypeList( $type1, $type2 );

		self::assertEquals( $type2, $list->last() );
	}

	public function testFindIndex(): void
	{
		$type1 = new BooleanType( true );
		$type2 = new BooleanType( false );

		$list = new BooleanTypeList( $type1, $type2 );

		$matcher = new class implements MatchesType {
			public function matches( RepresentsType $type ): bool
			{
				return $type->toNativeType() === false;
			}
		};

		self::assertSame( 1, $list->findIndex( $matcher ) );
	}

	public function testFindIndexReturnsNullIfNothingMatched(): void
	{
		$list = new BooleanTypeList( new BooleanType( true ) );

		$matcher = new class implements MatchesType {
			public function matches( RepresentsType $type ): bool
			{
				return false;
			}
		};

		self::assertNull( $list->findIndex( $matcher ) );
	}

	public function testThrowsInvalidTypeExceptionIfValueInvalid(): void
	{
		$this->expectExceptionObject( new InvalidBooleanTypeListException( 'Invalid ' . OnlyTrueBooleanTypeList::class ) );

		new OnlyTrueBooleanTypeList( new BooleanType( false ) );
	}
}
