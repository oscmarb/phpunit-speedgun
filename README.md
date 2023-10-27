# phpunit-speedgun


**SpeedGun** looks for the slowest tests in your suite and displays them in the console.

There are many reasons why the execution time of a test can vary. With this tool, you will be able to identify which tests are slowing down your suite. Additionally, since each test is different, various options are provided to customize the threshold that determines if a test is slow or not.

![Screenshot of terminal using SpeedGun](https://user-images.githubusercontent.com/135607/196077193-ba9e5f95-91ef-4655-88a5-93bb49007a67.png)

## Installation

SpeedGun is installed using [Composer](http://getcomposer.org). Add it as a `require-dev` dependency:

    composer require --dev oscmarb/phpunit-speedgun

## Usage

Enable with all defaults by adding the following code to your project's `phpunit.xml` file:

```xml

<phpunit bootstrap="vendor/autoload.php">
    ...
    <extensions>
        <extension class="Oscmarb\SpeedGun\SpeedGunPHPUnitExtension"/>
    </extensions>
</phpunit>
```

Now run the test suite. If one or more test executions exceed the slowness threshold (100ms by default), SpeedGun will
report on those tests in the console after all tests have completed.

## Config Parameters

SpeedGun also supports these parameters:

* **slow_threshold** - Number of milliseconds when a test is considered "slow" (Default: 100ms)
* **min_report_length** - Minimum number of slow tests. If the number of slow tests does not exceed the value of
  min_report_length, no results will be displayed. (Default: 0 tests)
* **max_report_length** - Max number of slow tests included in the report. Must be greater than **min_report_length** (
  Default: all tests)
* **pattern_thresholds** - Regular expression to apply special conditions to a set of tests.

Each parameter is set in `phpunit.xml`:

```xml

<phpunit bootstrap="vendor/autoload.php">
    <!-- ... other suite configuration here ... -->

    <extensions>
        <extension class="Oscmarb\SpeedGun\SpeedGunPHPUnitExtension">
            <arguments>
                <array>
                    <element key="slow_threshold">
                        <integer>100</integer>
                    </element>
                    <element key="min_report_length">
                        <integer>5</integer>
                    </element>
                    <element key="max_report_length">
                        <integer>10</integer>
                    </element>
                    <element key="pattern_thresholds">
                        <array>
                            <element key="(.*)pattern_slow_test(.*)">
                                <integer>500</integer>
                            </element>
                        </array>
                    </element>
                </array>
            </arguments>
        </extension>
    </extensions>
</phpunit>
```

## Custom slowness threshold per-test case

There are some tests that, due to their implementation, are slower (or faster) than the average of the tests in our suite. For this reason, there are different ways to customize the threshold.

### Annotation

The `@slowThreshold` annotation can set a threshold. This value can be higher or lower than the general threshold. The set threshold will only be used for the test in which we are using it.

```php
class SlowTestCase extends PHPUnit\Framework\TestCase
{
    /**
     * @slowThreshold 5000
     */
    public function test_slow()
    {
        // Code that takes a longer time to execute
    }
}
```

Setting `@slowThreshold 0` will never report that test as slow.

### Regex expression

Another alternative is to use regular expressions to set a threshold for a set of tests. Tests are identified by class::method. For example: `App\SlowTestCase::test_slow`.

Suppose we have integration tests with Doctrine, and we want to increase the threshold for these tests as they are slower than the unit tests. We can go test by test and update the threshold using notation.

```xml

<phpunit bootstrap="vendor/autoload.php">
    <!-- ... other suite configuration here ... -->

    <extensions>
        <extension class="Oscmarb\SpeedGun\SpeedGunPHPUnitExtension">
            <arguments>
                <array>
                    <element key="pattern_thresholds">
                        <array>
                            <element key="Doctrine(.*)">
                                <integer>500</integer>
                            </element>
                        </array>
                    </element>
                </array>
            </arguments>
        </extension>
    </extensions>
</phpunit>
```

## Disable slowness profiling using an environment variable

TODO

## Setup

### Requirements

* Docker

### Instructions

Follow the steps below to set up the environment that allows you to modify and test **SpeedGun**:

```
# Clone the project (or fork it)
$ git clone git@github.com:oscmarb/phpunit-speedgun.git

# Build docker image and install dependencies
$ make

# Run test suite to verify code runs as expected
$ make phpunit
```

Makefile file contains all available commands.

## Inspiration

**SpeedGun** was inspired by [SpeedTrap](https://github.com/johnkary/phpunit-speedtrap).

## License

**phpunit-speedgun** is available under the MIT License.
