<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Reference\Measurement\Type\Tests;

use BaksDev\Reference\Measurement\Type\Measurement;
use BaksDev\Reference\Measurement\Type\Measurements\Collection\MeasurementCollection;
use BaksDev\Reference\Measurement\Type\MeasurementType;
use BaksDev\Wildberries\Orders\Type\WildberriesStatus\Status\Collection\WildberriesStatusInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-measurement
 */
#[When(env: 'test')]
final class MeasurementTest extends KernelTestCase
{
    public function testUseCase(): void
    {
        /** @var MeasurementCollection $MeasurementCollection */
        $MeasurementCollection = self::getContainer()->get(MeasurementCollection::class);

        /** @var WildberriesStatusInterface $case */
        foreach($MeasurementCollection->cases() as $case)
        {
            $Measurement = new Measurement($case->getValue());

            self::assertTrue($Measurement->equals($case::class)); // немспейс интерфейса
            self::assertTrue($Measurement->equals($case)); // объект интерфейса
            self::assertTrue($Measurement->equals($case->getValue())); // срока
            self::assertTrue($Measurement->equals($Measurement)); // объект класса


            $MeasurementType = new MeasurementType();
            $platform = $this->getMockForAbstractClass(AbstractPlatform::class);

            $convertToDatabase = $MeasurementType->convertToDatabaseValue($Measurement, $platform);
            self::assertEquals($Measurement->getMeasurementValue(), $convertToDatabase);

            $convertToPHP = $MeasurementType->convertToPHPValue($convertToDatabase, $platform);
            self::assertInstanceOf(Measurement::class, $convertToPHP);
            self::assertEquals($case, $convertToPHP->getMeasurement());

        }

    }
}