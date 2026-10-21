<?php declare(strict_types=1);

namespace ComponoKit\Types\Lists\Tests\Unit;

use ComponoKit\Types\BooleanType;
use ComponoKit\Types\FloatType;
use ComponoKit\Types\IntegerType;
use ComponoKit\Types\Lists\Exceptions\InvalidStringValueListException;
use ComponoKit\Types\Lists\Exceptions\MissingIndexException;
use ComponoKit\Types\Lists\Immutables;
use ComponoKit\Types\Lists\Interfaces\RepresentsList;
use ComponoKit\Types\Lists\Interfaces\RepresentsListTypeException;
use ComponoKit\Types\Lists\Mutables;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\NoTwoStringTypeList;
use ComponoKit\Types\Lists\Tests\Unit\Mutables\fakes\NoTwoStringValueList;
use ComponoKit\Types\StringType;
use PHPUnit\Framework\TestCase;

class ListBehaviourTest extends TestCase
{
	public static function EmptyListDataProvider(): \Iterator
	{
		yield 'immutable boolean type list' => [ new Immutables\BooleanTypeList() ];
		yield 'immutable boolean value list' => [ new Immutables\BooleanValueList() ];
		yield 'immutable float type list' => [ new Immutables\FloatTypeList() ];
		yield 'immutable float value list' => [ new Immutables\FloatValueList() ];
		yield 'immutable integer type list' => [ new Immutables\IntegerTypeList() ];
		yield 'immutable integer value list' => [ new Immutables\IntegerValueList() ];
		yield 'immutable mixed type list' => [ new Immutables\MixedTypeList() ];
		yield 'immutable mixed value list' => [ new Immutables\MixedValueList() ];
		yield 'immutable string type list' => [ new Immutables\StringTypeList() ];
		yield 'immutable string value list' => [ new Immutables\StringValueList() ];
		yield 'mutable boolean type list' => [ new Mutables\BooleanTypeList() ];
		yield 'mutable boolean value list' => [ new Mutables\BooleanValueList() ];
		yield 'mutable float type list' => [ new Mutables\FloatTypeList() ];
		yield 'mutable float value list' => [ new Mutables\FloatValueList() ];
		yield 'mutable integer type list' => [ new Mutables\IntegerTypeList() ];
		yield 'mutable integer value list' => [ new Mutables\IntegerValueList() ];
		yield 'mutable mixed type list' => [ new Mutables\MixedTypeList() ];
		yield 'mutable mixed value list' => [ new Mutables\MixedValueList() ];
		yield 'mutable string type list' => [ new Mutables\StringTypeList() ];
		yield 'mutable string value list' => [ new Mutables\StringValueList() ];
	}

	/**
	 * @dataProvider EmptyListDataProvider
	 */
	public function testFirstOfEmptyListThrowsException( RepresentsList $list ): void
	{
		$this->expectExceptionObject( new MissingIndexException( 'List is empty' ) );

		$list->first();
	}

	/**
	 * @dataProvider EmptyListDataProvider
	 */
	public function testLastOfEmptyListThrowsException( RepresentsList $list ): void
	{
		$this->expectExceptionObject( new MissingIndexException( 'List is empty' ) );

		$list->last();
	}

	/**
	 * @dataProvider EmptyListDataProvider
	 */
	public function testCurrentOfEmptyListThrowsException( RepresentsList $list ): void
	{
		$this->expectExceptionObject( new MissingIndexException( 'Current index does not exist' ) );

		$list->current();
	}

	/**
	 * @dataProvider EmptyListDataProvider
	 */
	public function testIteratingEmptyListDoesNotIterate( RepresentsList $list ): void
	{
		$count = 0;
		foreach ( $list as $ignored )
		{
			$count++;
		}

		self::assertSame( 0, $count );
	}

	public function testExceptionsImplementListTypeException(): void
	{
		self::assertInstanceOf( RepresentsListTypeException::class, new MissingIndexException() );
		self::assertInstanceOf( RepresentsListTypeException::class, new InvalidStringValueListException() );
	}

	public static function AppendTypeListToImmutableValueListDataProvider(): \Iterator
	{
		yield 'boolean' => [
			new Immutables\BooleanValueList( true ),
			new Immutables\BooleanTypeList( new BooleanType( false ) ),
			[ true, false ],
			[ false, true ],
		];

		yield 'float' => [
			new Immutables\FloatValueList( 1.5 ),
			new Immutables\FloatTypeList( new FloatType( 2.5 ) ),
			[ 1.5, 2.5 ],
			[ 2.5, 1.5 ],
		];

		yield 'integer' => [
			new Immutables\IntegerValueList( 1 ),
			new Immutables\IntegerTypeList( new IntegerType( 2 ) ),
			[ 1, 2 ],
			[ 2, 1 ],
		];

		yield 'mixed' => [
			new Immutables\MixedValueList( 1 ),
			new Immutables\MixedTypeList( new StringType( 'two' ) ),
			[ 1, 'two' ],
			[ 'two', 1 ],
		];

		yield 'string' => [
			new Immutables\StringValueList( 'one' ),
			new Immutables\StringTypeList( new StringType( 'two' ) ),
			[ 'one', 'two' ],
			[ 'two', 'one' ],
		];
	}

	/**
	 * @dataProvider AppendTypeListToImmutableValueListDataProvider
	 */
	public function testAppendAndPrependTypeListToImmutableValueList( RepresentsList $valueList, RepresentsList $typeList, array $expectedAppended, array $expectedPrepended ): void
	{
		self::assertSame( $expectedAppended, $valueList->appendList( $typeList )->toNativeType() );
		self::assertSame( $expectedPrepended, $valueList->prependList( $typeList )->toNativeType() );
	}

	public static function InvalidMutableWriteDataProvider(): \Iterator
	{
		yield 'append value' => [ static fn( NoTwoStringValueList $list ) => $list->append( 'two' ) ];
		yield 'prepend value' => [ static fn( NoTwoStringValueList $list ) => $list->prepend( 'two' ) ];
		yield 'replace value' => [ static fn( NoTwoStringValueList $list ) => $list->replace( 0, 'two' ) ];
		yield 'append value list' => [ static fn( NoTwoStringValueList $list ) => $list->appendList( new Mutables\StringValueList( 'two' ) ) ];
		yield 'prepend type list' => [ static fn( NoTwoStringValueList $list ) => $list->prependList( new Mutables\StringTypeList( new StringType( 'two' ) ) ) ];
	}

	/**
	 * @dataProvider InvalidMutableWriteDataProvider
	 */
	public function testMutableValueListValidatesWrites( \Closure $write ): void
	{
		$list = new NoTwoStringValueList( '1' );

		try
		{
			$write( $list );
			self::fail( 'Expected exception was not thrown' );
		}
		catch ( InvalidStringValueListException )
		{
			self::assertSame( [ '1' ], $list->toNativeType() );
		}
	}

	public function testMutableTypeListValidatesWrites(): void
	{
		$list = new NoTwoStringTypeList( new StringType( '1' ) );

		$this->expectException( RepresentsListTypeException::class );

		$list->append( new StringType( '2' ) );
	}

	public function testDiffAndIntersectWithUnsortedLists(): void
	{
		$list        = new Immutables\StringValueList( 'd', 'b', 'a', 'c', 'b' );
		$anotherList = new Immutables\StringValueList( 'c', 'a', 'x' );

		self::assertSame( [ 'd', 'b', 'b' ], $list->diff( $anotherList )->toNativeType() );
		self::assertSame( [ 'a', 'c' ], $list->intersect( $anotherList )->toNativeType() );
	}

	public function testDiffAndIntersectOfMutableTypeLists(): void
	{
		$list        = new Mutables\IntegerTypeList( new IntegerType( 4 ), new IntegerType( 2 ), new IntegerType( 1 ), new IntegerType( 3 ) );
		$anotherList = new Mutables\IntegerTypeList( new IntegerType( 3 ), new IntegerType( 1 ) );

		self::assertSame( [ 4, 2 ], $list->diff( $anotherList )->toIntegers() );
		self::assertSame( [ 1, 3 ], $list->intersect( $anotherList )->toIntegers() );
	}

	public function testUniqueOfMutableFloatListsKeepsDifferentFractions(): void
	{
		$typeList = new Mutables\FloatTypeList( new FloatType( 1.1 ), new FloatType( 1.9 ), new FloatType( 1.1 ) );
		$typeList->unique();

		$valueList = new Mutables\FloatValueList( 1.1, 1.9, 1.1 );
		$valueList->unique();

		self::assertSame( [ 1.1, 1.9 ], $typeList->toFloats() );
		self::assertSame( [ 1.1, 1.9 ], $valueList->toNativeType() );
	}

	public function testUniqueOfMutableMixedValueListComparesStrictly(): void
	{
		$list = new Mutables\MixedValueList( 1, '1', [ 1, 2 ], [ 1, 2 ], 1.0, true );
		$list->unique();

		self::assertSame( [ 1, '1', [ 1, 2 ], 1.0, true ], $list->toNativeType() );
	}
}
