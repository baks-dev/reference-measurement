<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Measurement\Type;

use BaksDev\Reference\Measurement\Type\Measurements\Collection\MeasurementInterface;
use BaksDev\Reference\Measurement\Type\Measurements\MeasurementStunt;
use InvalidArgumentException;

/** Единицы измерения */
final class Measurement
{
    public const string TYPE = 'measurement_type';

    public const string TEST = MeasurementStunt::class;

	private MeasurementInterface $measurement;
	
	
	public function __construct(MeasurementInterface|self|string|null $measurement)
	{
        if($measurement === null)
        {
            $measurement = MeasurementStunt::class;
        }

        if(is_string($measurement) && class_exists($measurement))
        {
            $instance = new $measurement();

            if($instance instanceof MeasurementInterface)
            {
                $this->measurement = $instance;
                return;
            }
        }

        if($measurement instanceof MeasurementInterface)
        {
            $this->measurement = $measurement;
            return;
        }

        if($measurement instanceof self)
        {
            $this->measurement = $measurement->getMeasurement();
            return;
        }

        /** @var MeasurementInterface $declare */
        foreach(self::getDeclared() as $declare)
        {
            if($declare::equals($measurement))
            {
                $this->measurement = new $declare;
                return;
            }
        }

        throw new InvalidArgumentException(sprintf('Not found Measurement %s', $measurement));
		
	}
	
	
	public function __toString(): string
	{
		return $this->measurement->getValue();
	}
	
	
	public function getMeasurementValue(): string
	{
		return $this->measurement->getValue();
	}
	
	
	public function getMeasurement() : MeasurementInterface
	{
		return $this->measurement;
	}



    public static function cases(): array
    {
        $case = [];

        foreach(self::getDeclared() as $measurement)
        {
            /** @var MeasurementInterface $measurement */
            $class = new $measurement;
            $case[$class::sort()] = new self($measurement);
        }

        return $case;
    }

    public static function getDeclared(): array
    {
        return array_filter(
            get_declared_classes(),
            static function($className) {
                return in_array(MeasurementInterface::class, class_implements($className), true);
            }
        );
    }


    public function equals(mixed $status): bool
    {
        $status = new self($status);

        return $this->getMeasurementValue() === $status->getMeasurementValue();
    }
	
}