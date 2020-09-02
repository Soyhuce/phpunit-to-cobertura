# Generates a Cobertura xml report from phpunit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soyhuce/phpunit-to-cobertura.svg?style=flat-square)](https://packagist.org/packages/soyhuce/phpunit-to-cobertura)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/soyhuce/phpunit-to-cobertura/run-tests?label=tests&style=flat-square)
[![Total Downloads](https://img.shields.io/packagist/dt/soyhuce/phpunit-to-cobertura.svg?style=flat-square)](https://packagist.org/packages/soyhuce/phpunit-to-cobertura)

Some modern workflows need test coverage report to be generated with [Cobertura](http://cobertura.github.io/cobertura/). Phpunit does not support (yet ?) Cobertura report generation.

This project aims to solve this.

# Installation

Via composer :
```shell script
composer require --dev soyhuce/phpunit-to-cobertura
```

That's all !

# Generating a Cobertura coverage report

First, you need to run your phpunit tests with code coverage enabled. This needs to generate (at least) the code coverage in php format.
```xml
<coverage processUncoveredFiles="true">
    <include>
        <directory suffix=".php">src</directory>
    </include>
    <report>
        <php outputFile="./phpunit/codeCoverage.php"/>
    </report>
</coverage>
```

Once done, you can convert the php code coverage into a Cobertura coverage.
```
./vendor/bin/phpunit-to-cobertura ./phpunit/codeCoverage.php ./phpunit/coberturaCoverage.xml
```

# Support

Fow now, only PHPUnit 9.3 is supported.

# Contributing

You are welcome to contribute to this project ! Please see [CONTRIBUTING.md](CONTRIBUTING.md).

# License

This package is provided under the [MIT License](LICENSE.md)
