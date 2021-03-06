<?php
/**
 * This file is part of the daikon-cqrs/data-structure project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\DataStructure;

use Daikon\Tests\DataStructure\Fixture\DatetimeList;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use OutOfRangeException;
use PHPUnit\Framework\TestCase;

final class TypedListTraitTest extends TestCase
{
    public function testConstructNoParamsWorks()
    {
        $this->assertInstanceOf(DatetimeList::CLASS, new DatetimeList);
    }

    public function testConstructWithParamsWorks()
    {
        $list = new DatetimeList([ new DateTime, new DateTimeImmutable]);
        $this->assertInstanceOf(DatetimeList::CLASS, $list);
    }

    public function testConstructWithIndexedParamsWorks()
    {
        $list = new DatetimeList([ 1337 => new DateTime, -7 => new DateTimeImmutable]);
        $this->assertInstanceOf(DatetimeList::CLASS, $list);
        $this->assertEquals(2, $list->count());
        $this->assertTrue($list->has(0));
        $this->assertTrue($list->has(1));
        $this->assertFalse($list->has(1337));
    }

    public function testConstructFailsOnInvalidIndex()
    {
        $d0 = new DateTime;
        $this->expectException(InvalidArgumentException::CLASS);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Invalid item key given to Daikon\Tests\DataStructure\Fixture\DatetimeList. '.
            'Expected int but was given string.'
        );
        $list = new DatetimeList([ 'a' => $d0 ]);
    } // @codeCoverageIgnore

    public function testGetItemFqcnWorks()
    {
        $this->assertEquals(DateTimeInterface::CLASS, (new DatetimeList())->getItemFqcn());
    }

    public function testCountWorks()
    {
        $list = new DatetimeList([ new DateTime, new DateTimeImmutable]);
        $this->assertEquals(2, $list->count());
    }

    public function testIsEmptyWorks()
    {
        $list = new DatetimeList([ new DateTime, new DateTimeImmutable]);
        $this->assertFalse($list->isEmpty());
        $list = new DatetimeList;
        $this->assertTrue($list->isEmpty());
        $list = new DatetimeList([ $d = new DateTime ]);
        $this->assertTrue($list->remove($d)->isEmpty());
    }

    public function testHasWorks()
    {
        $list = new DatetimeList([ new DateTime, new DateTimeImmutable]);
        $this->assertTrue($list->has(0));
        $this->assertTrue($list->has(1));
        $this->assertFalse($list->has(3));
        $this->assertFalse($list->has(-1));
    }

    public function testGetIteratorWorks()
    {
        $list = new DatetimeList([ new DateTime, new DateTimeImmutable]);
        $this->assertInstanceOf(\Iterator::CLASS, $list->getIterator());
    }

    public function testGetWorks()
    {
        $d1 = new DateTime;
        $list = new DatetimeList([ $d1 ]);
        $this->assertTrue($list->has(0));
        $this->assertSame($d1, $list->get(0));
        $this->assertTrue($d1 === $list->get(0));
    }

    public function testGetThrowsForNonExistantKey()
    {
        $map = new DatetimeList([ new Datetime ]);
        $this->expectException(OutOfRangeException::CLASS);
        $this->expectExceptionCode(0);
        $map->get(1);
    } // @codeCoverageIgnore

    public function testPushWorks()
    {
        $d0 = new DateTime;
        $d1 = new DateTimeImmutable;
        $list = new DatetimeList([ $d0 ]);
        $list = $list->push($d1);
        $this->assertSame($d0, $list->get(0));
        $this->assertSame($d1, $list->get(1));
        $this->assertEquals(2, $list->count());
    }

    public function testPushFailsOnUnacceptableType()
    {
        $d0 = new DateTime;
        $d1 = new \stdClass;
        $list = new DatetimeList([ $d0 ]);
        $this->expectException(InvalidArgumentException::CLASS);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Invalid item type given to Daikon\Tests\DataStructure\Fixture\DatetimeList. '.
            'Expected DateTimeInterface but was given stdClass.'
        );
        $list = $list->push($d1);
    } // @codeCoverageIgnore

    public function testPrependWorks()
    {
        $d0 = new DateTime;
        $d1 = new DateTimeImmutable;
        $list = new DatetimeList([ $d1 ]);
        $list = $list->prepend($d0);
        $this->assertSame($d0, $list->get(0));
        $this->assertTrue($d0 === $list->get(0));
        $this->assertSame($d1, $list->get(1));
        $this->assertTrue($d1 === $list->get(1));
        $this->assertEquals(2, $list->count());
    }

    public function testPrependFailsOnUnacceptableType()
    {
        $d0 = new DateTime;
        $d1 = new \stdClass;
        $list = new DatetimeList([ $d0 ]);
        $this->expectException(InvalidArgumentException::CLASS);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Invalid item type given to Daikon\Tests\DataStructure\Fixture\DatetimeList. '.
            'Expected DateTimeInterface but was given stdClass.'
        );
        $list = $list->prepend($d1);
    } // @codeCoverageIgnore

    public function testReverseWorks()
    {
        $d0 = new DateTime;
        $d1 = new DateTimeImmutable;
        $list = new DatetimeList([ $d0, $d1 ]);
        $list_reversed = $list->reverse();
        $this->assertTrue($d0 === $list->get(0));
        $this->assertTrue($d1 === $list->get(1));
        $this->assertEquals(2, $list->count());
        $this->assertTrue($d1 === $list_reversed->get(0));
        $this->assertSame($d1, $list_reversed->get(0));
        $this->assertTrue($d0 === $list_reversed->get(1));
        $this->assertSame($d0, $list_reversed->get(1));
        $this->assertEquals(2, $list_reversed->count());
    }

    public function testRemoveWorks()
    {
        $d0 = new DateTimeImmutable;
        $d1 = new DateTimeImmutable;
        $d2 = new DateTime;
        $list = new DatetimeList([ $d0, $d1, $d2 ]);
        $this->assertSame($d0, $list->get(0));
        $this->assertSame($d1, $list->get(1));
        $this->assertSame($d2, $list->get(2));
        $this->assertEquals(3, $list->count());
        $list = $list->remove($d1);
        $this->assertEquals(2, $list->count());
        $this->assertTrue($d2 === $list->get(1));
        $this->assertTrue($d0 === $list->get(0));
    }

    public function testRemoveFailsOnUnacceptableType()
    {
        $d0 = new DateTime;
        $list = new DatetimeList([ $d0 ]);
        $this->expectException(InvalidArgumentException::CLASS);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Invalid item type given to Daikon\Tests\DataStructure\Fixture\DatetimeList. '.
            'Expected DateTimeInterface but was given stdClass.'
        );
        $list = $list->remove(new \stdClass);
    } // @codeCoverageIgnore

    public function testGetFirstWorks()
    {
        $d0 = new DateTime;
        $d1 = new DateTimeImmutable;
        $list = new DatetimeList([ $d0, $d1 ]);
        $this->assertTrue($d0 === $list->getFirst());
        $this->assertTrue($list->getFirst() === $list->get(0));
    }

    public function testGetLastWorks()
    {
        $d0 = new DateTime;
        $d1 = new DateTimeImmutable;
        $list = new DatetimeList([ $d0, $d1 ]);
        $this->assertTrue($d1 === $list->getLast());
        $this->assertTrue($list->getLast() === $list->get(1));
    }

    public function testToArrayWorks()
    {
        $d0 = new DateTime;
        $d1 = new DateTimeImmutable;
        $a = [ $d0, $d1 ];
        $list = new DatetimeList($a);
        $b = $list->toArray();
        $this->assertTrue($a === $b);
        $this->assertTrue($a[0] === $b[0]);
        $this->assertTrue($a[1] === $b[1]);
    }
}
