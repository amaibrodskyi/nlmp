<?php declare(strict_types=1);

require_once __DIR__ . '/../src/Calculator.php';

use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * @dataProvider addProvider
     */
    public function testCanAdd($a, $b, $expected): void
    {
        $calculator = new Calculator();
        $this->assertEquals(
            $expected,
            $calculator->add($a, $b)
        );
    }

    public function addProvider()
    {
        return [
            [1, 2, 3],
            [-5, 4, -1],
        ];
    }
}
