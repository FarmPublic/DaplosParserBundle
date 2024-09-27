# DaplosParserBundle

[![Static code analysis](https://github.com/FarmPublic/DaplosParserBundle/actions/workflows/code_analysis.yml/badge.svg)](https://github.com/FarmPublic/DaplosParserBundle/actions/workflows/code_analysis.yml) [![Testing](https://github.com/FarmPublic/DaplosParserBundle/actions/workflows/testing.yml/badge.svg)](https://github.com/FarmPublic/DaplosParserBundle/actions/workflows/testing.yml) [![emoji-log](https://cdn.rawgit.com/ahmadawais/stuff/ca97874/emoji-log/flat.svg)](https://github.com/ahmadawais/Emoji-Log/)

A Symfony bundle to parse Daplos flat files.

## Requirements

* PHP 8.3+
* Symfony 7.1+
* Composer

## Installation

```bash
composer require farmpublic/daplos-parser-bundle
```

## Usage

To use our bundle, you need to import `FarmPublic\DaplosParserBundle\DaplosParserInterface` in your controller/service/etc.

- In a controller:

```php
use FarmPublic\DaplosParserBundle\DaplosParserInterface;

class MyController
{
    public function myAction(DaplosParserInterface $daplosParser)
    {
        $daplosParser->parse('/path/to/file.dap');
    }
}
```

- In a service:

```php
use FarmPublic\DaplosParserBundle\DaplosParserInterface;

class MyService
{
    public function __construct(
        private DaplosParserInterface $daplosParser
    ) {
    }

    public function myAction()
    {
        $daplosParser->parse('/path/to/file.dap');
    }
}
```

- In a command:

```php
use FarmPublic\DaplosParserBundle\DaplosParserInterface;

class MyCommand extends Command
{
    public function __construct(
        private DaplosParserInterface $daplosParser
    ) {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $daplosParser->parse('/path/to/file.dap');
    }
}
```

## Testing

To run the *phpunit*, *phpstan* and *php-cs-fixer* tests, run:

```bash
composer test:all
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/farmpublic/daplos-parser-bundle/tags).

## Authors

* **Yoan Bernabeu** - *Initial work* - [GitHub](https://github.com/yoanbernabeu)

See also the list of [contributors](https://github.com/farmpublic/daplos-parser-bundle/contributors) who participated in this project.