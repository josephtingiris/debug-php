<!-- Markdown link definitions -->
[init-base]: https://github.com/josephtingiris/debug-php
[init-conduct]: debug-php.CODE_OF_CONDUCT.md
[init-contributing]: debug-php.CONTRIBUTING.md
[init-installation]: #Installation
[init-issue]: https://github.com/josephtingiris/debug-php/issues/new
[init-license]: debug-php.LICENSE.md
[init-support]: #Support
[init-usage]: #Usage
[init-wiki]: https://github.com/josephtingiris/debug-php/wiki

# Description

This is a structure for my PHP Debug class composer project.

## Table of Contents

* [Installation][init-installation]
* [Usage][init-usage]
* [Support][init-support]
* [License][init-license]
* [Code of Conduct][init-conduct]
* [Contributing][init-contributing]

## Installation

Download to the project directory, add, and commit.  i.e.:

```sh
composer require "josephtingiris/debug-php"
```

## Usage

1. Basic, setting global debug level via construct.

```php
<?php

require_once(dirname(__FILE__) . "/vendor/autoload.php");

$debug = new \josephtingiris\Debug(10);

$debug->out("level 1 show",1);
$debug->out("level 2 show",2);
$debug->out("level 12 no-show",12);
?>
```

## Support

Please see the [Wiki][init-wiki] or [open an issue][init-issue] for support.
