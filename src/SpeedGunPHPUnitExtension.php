<?php

namespace Oscmarb\SpeedGun;

use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\AfterSuccessfulTestHook;
use PHPUnit\Runner\BeforeFirstTestHook;
use PHPUnit\Util\Test;

final class SpeedGunPHPUnitExtension implements AfterSuccessfulTestHook, AfterLastTestHook, BeforeFirstTestHook
{
    private const DEFAULT_SLOW_THRESHOLD = 100;

    private const SLOW_THRESHOLD = 'slow_threshold';
    private const MAX_REPORT_LENGTH = 'max_report_length';
    private const MIN_REPORT_LENGTH = 'min_report_length';
    private const PATTERN_THRESHOLDS = 'pattern_thresholds';

    /** @var array<string, int> */
    private array $patternThresholds;

    /** @var array<string, int> */
    protected array $slowTests = [];

    private int    $suites = 0;
    private bool   $enabled;
    private int    $slowThreshold;
    protected ?int $maxReportLength;
    protected ?int $minReportLength;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(array $options = [])
    {
        $env = getenv('PHPUNIT_SPEED_GUN');
        $this->enabled = '0' !== $env && 'disabled' !== $env;
        $this->slowThreshold = $options[self::SLOW_THRESHOLD] ?? self::DEFAULT_SLOW_THRESHOLD;
        $this->maxReportLength = $options[self::MAX_REPORT_LENGTH] ?? null;
        $this->minReportLength = $options[self::MIN_REPORT_LENGTH] ?? null;

        /** @var array<string, int> $patternThresholds */
        $patternThresholds = $options[self::PATTERN_THRESHOLDS] ?? [];

        if (null !== $this->maxReportLength && $this->minReportLength > $this->maxReportLength) {
            throw new \RuntimeException('SpeedGunPHPUnitExtension error: maxReportLength can not be lower than minReportLength');
        }

        $this->patternThresholds = array_combine(
            array_map(
                static fn (string $pattern) => addslashes($pattern),
                array_keys($patternThresholds),
            ),
            array_values($patternThresholds),
        );
    }

    public function executeAfterSuccessfulTest(string $test, float $time): void
    {
        if (false === $this->enabled) {
            return;
        }

        $timeMs = $this->toMilliseconds($time);
        $testSlowThresold = $this->getSlowThreshold($test);

        if (0 < $testSlowThresold && $timeMs >= $testSlowThresold) {
            $this->addSlowTest($test, $timeMs);
        }
    }

    public function executeBeforeFirstTest(): void
    {
        ++$this->suites;
    }

    public function executeAfterLastTest(): void
    {
        --$this->suites;

        if (
            false === $this->enabled
            || 0 !== $this->suites
            || $this->minReportLength > $this->getReportLength()
        ) {
            return;
        }

        $this->printResult();
    }

    private function printResult(): void
    {
        $slowTests = $this->slowTests;

        arsort($slowTests);

        echo sprintf("\n\nThe following tests were detected as slow (>%sms)\n", $this->slowThreshold);

        $reportLength = $this->getReportLength();

        for ($i = 1; $i <= $reportLength; ++$i) {
            $label = key($slowTests);
            $time = array_shift($slowTests);
            $seconds = $time / 1000;

            echo sprintf(" %s) %.3fs to run %s\n", $i, $seconds, $label);
        }

        $hidden = count($slowTests) - $reportLength;

        if (0 < $hidden) {
            echo sprintf("\nand %s more slow tests hidden from view", $hidden);
        }
    }

    private function getReportLength(): int
    {
        $numberOfSlowTests = count($this->slowTests);

        return null === $this->maxReportLength ? $numberOfSlowTests : min($numberOfSlowTests, $this->maxReportLength);
    }

    private function toMilliseconds(float $time): int
    {
        return (int) round($time * 1000);
    }

    private function getSlowThreshold(string $test): int
    {
        [$class, $testName] = explode('::', $test);

        /** @var class-string $class */
        $ann = Test::parseTestMethodAnnotations($class, $testName);

        if (true === isset($ann['method']['slowThreshold'][0])) {
            return (int) $ann['method']['slowThreshold'][0];
        }

        foreach (array_keys($this->patternThresholds) as $pattern) {
            if (1 === preg_match("/$pattern/", $test)) {
                return $this->patternThresholds[$pattern];
            }
        }

        return $this->slowThreshold;
    }

    private function addSlowTest(string $test, int $time): void
    {
        [$class, $testName] = explode('::', $test);

        $testName = preg_replace('/\s\(.*\)$/', '', $testName);
        $label = sprintf('%s::%s', addslashes($class), $testName);

        $this->slowTests[$label] = $time;
    }
}
