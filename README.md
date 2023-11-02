# phpunit-speedgun

**SpeedGun** detects slow tests executed with PHPUnit and displays them in the console.

The execution times of tests can vary for various reasons, such as system load, disk access, etc. This tool can identify
tests that are considered slow but may not provide an explanation for why they are slow.

![Screenshot of terminal using SpeedGun](https://repository-images.githubusercontent.com/667299256/4c7f0268-7e86-446b-8773-5d2f175fd961)

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

## Configuring the extension

SpeedGun supports these configuration parameters:

* ```slow_threshold``` - Number of milliseconds when a test is considered "slow" (Default: 100ms)
* ```min_report_length``` - Minimum number of slow tests. If the number of slow tests does not exceed the value of
  ```min_report_length```, no results will be displayed. (Default: 0 tests)
* ```max_report_length``` - Max number of slow tests included in the report. Must be greater
  than ```min_report_length``` (Default: all tests)
* ```pattern_thresholds``` - Regex expression to apply special conditions to a set of tests.

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
                        <integer>3</integer>
                    </element>
                    <element key="max_report_length">
                        <integer>5</integer>
                    </element>
                    <element key="pattern_thresholds">
                        <array>
                            <element key="(.*)PatternSlow(.*)">
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

There are some tests that, due to their implementation, are slower (or faster) than the average of the tests in our
suite. For this reason, there are different ways to customize the threshold.

### Annotation

The `@slowThreshold` annotation can set a threshold. This value can be higher or lower than the general threshold. The
set threshold will only be used for the test in which we are using it.

```php
class SlowTestCase extends PHPUnit\Framework\TestCase
{
    /**
     * @slowThreshold 5000
     */
    public function testSlow()
    {
        // Code that takes a longer time to execute
    }
}
```

Setting `@slowThreshold 0` will never report that test as slow.

### Regex expression

Another alternative is to use regular expressions to set a threshold for a set of tests. Tests are identified
by `class::method`. For example: `App\SlowTestCase::test_slow`.

Suppose we have integration tests with Doctrine, and we want to increase the threshold for these tests as they are
slower than the unit tests. We can go test by test and update the threshold using annotations.

This process is slow, and furthermore, we could end up with tests lacking the correct annotation. If we know that all
tests with Doctrine contain the word `Doctrine`, we can configure a different threshold as follows:

```xml

<phpunit bootstrap="vendor/autoload.php">
    <!-- ... other suite configuration here ... -->

    <extensions>
        <extension class="Oscmarb\SpeedGun\SpeedGunPHPUnitExtension">
            <arguments>
                <array>
                    <element key="pattern_thresholds">
                        <array>
                            <element key="(.*)Doctrine(.*)">
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

**SpeedGun** profiles for slow tests when enabled in phpunit.xml. But using an environment variable
named `PHPUNIT_SPEED_GUN`
can enable or disable the extension:

    PHPUNIT_SPEED_GUN="0" ./vendor/bin/phpunit****

or

    PHPUNIT_SPEED_GUN="disabled" ./vendor/bin/phpunit****

The value of the environment variable can also be set in the `phpunit.xml` file:

```xml

<phpunit bootstrap="vendor/autoload.php">
    <php>
        <env name="PHPUNIT_SPEED_GUN" value="0"/>
    </php>

    <extensions>
        <extension class="Oscmarb\SpeedGun\SpeedGunPHPUnitExtension"/>
    </extensions>
</phpunit>
```

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

## PHP Version Support Policy

This package supports PHP versions with [active support](https://www.php.net/supported-versions.php).

The maintainers of this package add support for a PHP version following its initial release and drop support for a PHP
version when it has reached its end of active support.

## License

**phpunit-speedgun** is available under the MIT License.

## Credits

This package is inspired by [`johnkary/phpunit-speedtrap`](https://github.com/johnkary/phpunit-speedtrap), originally
licensed under MIT by [John Kary](https://github.com/johnkary)

## Social

Follow [@oscmarb](https://twitter.com/intent/follow?screen_name=oscmarb) on Twitter.
