<?php

namespace Oscmarb\SpeedGun\Tests;

use PHPUnit\Framework\TestCase;

final class SpeedGunPHPUnitExtensionTest extends TestCase
{
    // Slow tests

    public function test_slow_test_over_one_second(): void
    {
        $this->extendTime(1010);

        $this->assertTrue(true);
    }

    /**
     * @slowThreshold 500
     */
    public function test_slow_can_set_custom_threshold(): void
    {
        $this->extendTime(550);
        $this->assertTrue(true);
    }

    public function test_slow_test_with_pattern_slow_test(): void
    {
        $this->extendTime(550);
        $this->assertTrue(true);
    }

    public function test_another_slow_test(): void
    {
        $this->extendTime(500);

        $this->assertTrue(true);
    }

    public function test_slow_test(): void
    {
        $this->extendTime(150);

        $this->assertTrue(true);
    }


    // Fast tests

    public function test_fast_test(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @slowThreshold 500
     */
    public function test_fast_can_set_custom_threshold(): void
    {
        $this->extendTime(400);
        $this->assertTrue(true);
    }

    /**
     * @slowThreshold 0
     */
    public function test_fast_can_disable_slow_threshold(): void
    {
        $this->extendTime(600);
        $this->assertTrue(true);
    }

    public function test_fast_test_with_pattern_slow_test(): void
    {
        $this->extendTime(300);
        $this->assertTrue(true);
    }

    /**
     * @slowThreshold 700
     */
    public function test_fast_test_set_higher_threshold_with_pattern_slow_test(): void
    {
        $this->extendTime(600);
        $this->assertTrue(true);
    }


    // Other tests

    public function test_exception_can_be_thrown_in_test(): void
    {
        $this->expectException(\RuntimeException::class);

        throw new \RuntimeException();
    }

    public function test_skipped_test(): void
    {
        $this->markTestSkipped('Skipped tests do not cause Exceptions in SpeedGun extension');
    }

    public function test_incomplete_test(): void
    {
        $this->markTestIncomplete('Incomplete tests do not cause Exceptions in SpeedGun extension');
    }


    // Utils

    private function extendTime(int $ms): void
    {
        usleep($ms * 1000);
    }
}
