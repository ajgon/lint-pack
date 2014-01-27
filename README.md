lint-pack
=========

[![](https://api.travis-ci.org/ajgon/lint-pack.png)](https://travis-ci.org/ajgon/lint-pack)

# Overview

This Bundle provides various linters for app/console command.

# Installation

## 1. Using Composer (recommended)

To install `LintPackBundle` with [Composer][1] just add the following to
your `composer.json` file:

```json
{
    // ...
    "require": {
        // ...
        "ajgon/lint-pack": "1.0.*@dev"
        // ...
    }
    // ...
}
```

Then, you can install the new dependencies by running Composer's update command
from the directory where your `composer.json` file is located:

```sh
$ php composer.phar update ajgon/lint-pack
```

Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your `AppKernel.php` file, and
register the new bundle:

```php
<?php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Ajgon\LintPackBundle\LintPackBundle(),
    // ...
);
```

## Configuration

This bundle configured under the `lint_pack` key in your application configuration. This includes settings related every linter available in package. Below are example configurations with descriptions.

### JSHint

```yml
lint_pack:
    # Section for jshint options
    jshint:
        # Path to the jshint binary.
        # required: yes, default: "jshint"
        bin: "/somewhere/something/jshint"
        # Path to the .jshintrc configuration file.
        # required: no, default: ""
        jshintrc: "/tmp/.jshintrc"
        # Path to jshintignore file, if set, disables "ignores" directive.
        # required: no, default: ""
        jshintignore: "/tmp/.jshintignore"
        # List of extensions which will be included for parsing.
        # required: no, default: ["js"]
        extensions:
            - js
            - javascript
        # List of regular expressions which will be tested against files found in locations.
        # Every file matching patterns will be ignored. Files are absolute paths.
        # required: no, default: []
        ignores:
            - "@r.js$@"
            - "@s[^/]+/jquery.js@"
        # List of locations scanned for files.
        # required: no, default: ["%kernel.root_dir%", "%kernel.root_dir%/../src"]
        locations:
            - "%kernel.root_dir%/my-assets"
```

### phpcs

```yml
lint_pack:
    phpcs:
        # Path to the phpcs binary.
        # required: yes, default: "phpcs"
        bin: "vendor/bin/phpcs"
        # Display warnings?
        # required: no, default: false
        warnings: false
        # Enable recursion over directories?
        # required: no, default: true
        recursion: false
        # Coding standard against which files will be checked.
        # Can contain ruleset path.
        # required: false, default: PSR2
        standard: PEAR
        # List of extensions which will be included for parsing
        # required: no, default: ["php"]
        extensions:
            - php
            - php5
        # List of filespaths to ignore.
        # required: no, default: []
        ignores:
            - "ignore.php"
        # List of locations scanned for files.
        # required: no, default: ["%kernel.root_dir%/../src"]
        locations:
            - "%kernel.root_dir%/my-source"
```

### phpcpd
```yml
lint_pack:
    phpcpd:
        # Path to the phpcs binary.
        # required: yes, default: "phpcs"
        bin: "vendor/bin/phpcpd"
        # Minimum number of identical lines.
        # required: no, default: 5
        min_lines: 4
        # Minimum number of identical tokens.
        # required: no, default: 70
        min_tokens: 60
        # List of extensions which will be included for parsing
        # required: no, default: ["php"]
        extensions:
            - php
            - php5
        # List of filespaths to ignore.
        # required: no, default: []
        ignores:
            - ignore.php
        # List of locations scanned for files.
        # required: no, default: ["%kernel.root_dir%/../src"]
        locations:
            - "%kernel.root_dir%/my-source"
```

### phpmd

```yml
lint_pack:
    phpmd:
        # Path to the phpcs binary.
        # required: yes, default: "phpmd"
        bin: "vendor/bin/phpmd"
        # List of rulesets passed to the phpmd.
        # required: yes, default: ["codesize", "controversial", "design", "naming", "unusedcode"]
        rulesets:
            - naming
            - controversial
        # List of extensions which will be included for parsing
        # required: no, default: ["php"]
        extensions:
            - php
            - php5
        # List of filespaths to ignore.
        # required: no, default: []
        ignores:
            - "ignore.php"
        # List of locations scanned for files.
        # required: no, default: ["%kernel.root_dir%/../src"]
        locations:
            - "%kernel.root_dir%/my-source"
```

## Usage

This extension will add a new group `lint` to `app/console` task list:

```sh
$ php app/console
...
lint
  lint:all                              Lint all files with all linters
  lint:jshint                           Lint all files with jshint
  lint:phpcs                            Lint all files with phpcs
  lint:phpcpd                           Lint all files with phpcpd
  lint:phpmd                            Lint all files with phpmd
```

To use specific linter, just launch it, e.g. `php app/console lint:jshint`.

## Testing

    php vendor/bin/phpcs --standard=PSR2 --extensions=php src/ -p
    php vendor/bin/phpmd --suffixes php src/* text codesize,controversial,design,unusedcode,naming
    php vendor/bin/phpcpd src
    php vendor/bin/phpunit --coverage-text

## Credits

- [Igor Rzegocki][2] ([GitHub][3])

## License

This bundle is released under the [MIT license][4].

[1]: https://github.com/composer/composer
[2]: http://rzegocki.pl/
[3]: https://github.com/ajgon
[4]: https://github.com/ajgon/lint-pack/blob/master/LICENSE
