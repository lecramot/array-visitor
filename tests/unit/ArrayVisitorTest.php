<?php

declare(strict_types = 1);

namespace Lecramot\ArrayVisitor\Test;

use Lecramot\ArrayVisitor\ArrayVisitor;
use Lecramot\ArrayVisitor\ItemHandlerInterface;
use PHPUnit\Framework\TestCase;

class ArrayVisitorTest extends TestCase
{

    private const A = 1;
    private const C = 4;
    private const E = 23;
    private const F = 2;
    private const H = 1;
    private const I = 53;
    private const K = 7;
    private const L = 432;
    private const M = 432;
    private const N4 = 432;

    private const A_KEY = 'a';
    private const B_KEY = 'b';
    private const C_KEY = 'c';
    private const D_KEY = 'd';
    private const E_KEY = 'e';
    private const F_KEY = 'f';
    private const G_KEY = 'g';
    private const H_KEY = 'h';
    private const I_KEY = 'i';
    private const J_KEY = 'j';
    private const K_KEY = 'k';
    private const L_KEY = 'l';
    private const M_KEY = 'm';
    private const N_KEY = 'n';
    private const N4_KEY = 4;

    /**
     * @return mixed[]
     */
    private function getSubject(): array
    {
        return [
            self::A_KEY => self::A,
            self::B_KEY => [
                self::C_KEY => self::C,
                self::D_KEY => [
                    self::E_KEY => self::E,
                    self::F_KEY => self::F,
                    self::G_KEY => [
                        self::H_KEY => self::H
                    ],
                    self::I_KEY => self::I,
                    self::J_KEY => [
                        self::K_KEY => self::K
                    ]
                ]
            ],
            self::L_KEY => self::L,
            self::M_KEY => self::M,
            self::N_KEY => [4 => self::N4]
        ];
    }

    public function testVisit() :void
    {


        $keyCollector = new class() implements ItemHandlerInterface {

            /**
             * @var mixed[]
             */
            private array $keyCollection = [];

            /**
             * @param bool|float|int|mixed[]|object|string $value
             * @param int|string $key
             * @param mixed[] $array
             */
            public function __invoke($value, $key, array &$array): void
            {
                $this->keyCollection[$key] = $value;
            }

            /**
             * @return mixed[]
             */
            public function getKeyCollection(): array
            {
                return $this->keyCollection;
            }
        };

        $subject = $this->getSubject();
        $visitor = new ArrayVisitor();
        $visitor->visit($subject, null, $keyCollector);

        $keyCollection = $keyCollector->getKeyCollection();

        $this->assertNotEmpty($keyCollection);
        $this->assertIsArray($keyCollection);

        $this->checkKeys($keyCollection);

        //check leaf values
        $this->assertEquals($keyCollection[self::A_KEY], self::A);
        $this->assertEquals($keyCollection[self::C_KEY], self::C);
        $this->assertEquals($keyCollection[self::E_KEY], self::E);
        $this->assertEquals($keyCollection[self::F_KEY], self::F);
        $this->assertEquals($keyCollection[self::H_KEY], self::H);
        $this->assertEquals($keyCollection[self::I_KEY], self::I);
        $this->assertEquals($keyCollection[self::K_KEY], self::K);
        $this->assertEquals($keyCollection[self::L_KEY], self::L);
        $this->assertEquals($keyCollection[self::M_KEY], self::M);
        $this->assertEquals($keyCollection[4], self::N4);

    }

    public function testVisitWithMutation() : void
    {
        //gather key/vales for assertions
        $keyValueCollector = new class() implements ItemHandlerInterface {

            /** @var mixed[]  */
            private array $keyValues = [];

            /**
             * @param bool|float|int|mixed[]|object|string $value
             * @param int|string $key
             * @param mixed[] $array
             */
            public function __invoke($value, $key, array &$array): void
            {
                $this->keyValues[$key] = $value;
            }

            /**
             * @return mixed[]
             */
            public function getKeyValues(): array
            {
                return $this->keyValues;
            }

            public function clear() : void{
                $this->keyValues = [];
            }
        };

        $subject = $this->getSubject();

        $visitor = new ArrayVisitor();
        $visitor->visit($subject, $keyValueCollector);

        $originalKeyValues = $keyValueCollector->getKeyValues();
        $keyValueCollector->clear();

        //mutate values
        $valueIncrementor = function($value, $key, array &$array): void
            {
                $newValue = (!is_array($value) && is_int($value)) ? $value + 1: $value;
                $array[$key] = $newValue;
        };

        $visitor->visit($subject, $valueIncrementor);


        //gather key/vales for assertions
        $visitor->visit($subject, $keyValueCollector);
        $keyValues = $keyValueCollector->getKeyValues();;

        $this->assertNotEmpty($keyValues);
        $this->assertIsArray($keyValues);

        $this->assertEquals($originalKeyValues[self::A_KEY] + 1, $keyValues[self::A_KEY]);
        $this->assertEquals($originalKeyValues[self::C_KEY] + 1, $keyValues[self::C_KEY]);
        $this->assertEquals($originalKeyValues[self::E_KEY] + 1, $keyValues[self::E_KEY]);
        $this->assertEquals($originalKeyValues[self::F_KEY] + 1, $keyValues[self::F_KEY]);
        $this->assertEquals($originalKeyValues[self::H_KEY] + 1, $keyValues[self::H_KEY]);
        $this->assertEquals($originalKeyValues[self::I_KEY] + 1, $keyValues[self::I_KEY]);
        $this->assertEquals($originalKeyValues[self::K_KEY] + 1, $keyValues[self::K_KEY]);
        $this->assertEquals($originalKeyValues[self::L_KEY] + 1, $keyValues[self::L_KEY]);
        $this->assertEquals($originalKeyValues[self::M_KEY] + 1, $keyValues[self::M_KEY]);
        $this->assertEquals($originalKeyValues[self::N4_KEY] + 1, $keyValues[self::N4_KEY]);

        $this->checkKeys($keyValues);

    }

    /**
     * @param mixed[] $keyValues
     */
    private function checkKeys(array $keyValues) : void
    {
        //check leaves
        $this->assertArrayHasKey(self::A_KEY, $keyValues);
        $this->assertArrayHasKey(self::B_KEY, $keyValues);
        $this->assertArrayHasKey(self::C_KEY, $keyValues);
        $this->assertArrayHasKey(self::D_KEY, $keyValues);
        $this->assertArrayHasKey(self::E_KEY, $keyValues);
        $this->assertArrayHasKey(self::F_KEY, $keyValues);
        $this->assertArrayHasKey(self::G_KEY, $keyValues);
        $this->assertArrayHasKey(self::H_KEY, $keyValues);
        $this->assertArrayHasKey(self::I_KEY, $keyValues);
        $this->assertArrayHasKey(self::J_KEY, $keyValues);
        $this->assertArrayHasKey(self::K_KEY, $keyValues);
        $this->assertArrayHasKey(self::L_KEY, $keyValues);
        $this->assertArrayHasKey(self::M_KEY, $keyValues);
        $this->assertArrayHasKey(self::N_KEY, $keyValues);
        $this->assertArrayHasKey(self::N4_KEY, $keyValues);

        //check parent values
        $this->assertIsArray($keyValues[self::B_KEY]);
        $this->assertIsArray($keyValues[self::D_KEY]);
        $this->assertIsArray($keyValues[self::G_KEY]);
        $this->assertIsArray($keyValues[self::J_KEY]);
        $this->assertIsArray($keyValues[self::N_KEY]);
    }

}
