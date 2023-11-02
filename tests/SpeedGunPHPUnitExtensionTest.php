<?php

namespace Oscmarb\SpeedGun\Tests;

use PHPUnit\Framework\TestCase;

final class SpeedGunPHPUnitExtensionTest extends TestCase
{
    // Slow tests

    public function testSlowTestOverOneSecond(): void
    {
        $this->extendTime(1010);

        $this->assertTrue(true);
    }

    /**
     * @slowThreshold 500
     */
    public function testSlowCanSetCustomThreshold(): void
    {
        $this->extendTime(550);
        $this->assertTrue(true);
    }

    public function testSlowTestWithPatternSlowTest(): void
    {
        $this->extendTime(550);
        $this->assertTrue(true);
    }

    public function testAnotherSlowTest(): void
    {
        $this->extendTime(110);

        $this->assertTrue(true);
    }

    // Fast tests

    public function testFastTest(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @slowThreshold 500
     */
    public function testFastCanSetCustomThreshold(): void
    {
        $this->extendTime(400);
        $this->assertTrue(true);
    }

    /**
     * @slowThreshold 0
     */
    public function testFastCanDisableSlowThreshold(): void
    {
        $this->extendTime(600);
        $this->assertTrue(true);
    }

    public function testFastTestWithPatternSlowTest(): void
    {
        $this->extendTime(400);
        $this->assertTrue(true);
    }

    // Other tests

    public function testExceptionCanBeThrownInTest(): void
    {
        $this->expectException(\RuntimeException::class);

        throw new \RuntimeException();
    }

    public function testSkippedTest(): void
    {
        $this->markTestSkipped('Skipped tests do not cause Exceptions in SpeedGun extension');
    }

    public function testIncompleteTest(): void
    {
        $this->markTestIncomplete('Incomplete tests do not cause Exceptions in SpeedGun extension');
    }

    // Utils

    private function extendTime(int $ms): void
    {
        usleep($ms * 1000);
    }
}
