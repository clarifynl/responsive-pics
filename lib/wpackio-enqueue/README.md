# [WPACK.IO](http://wpack.io) Enqueue API

[![Build Status](https://travis-ci.com/swashata/wpackio-enqueue.svg?branch=master)](https://travis-ci.com/swashata/wpackio-enqueue) [![codecov](https://codecov.io/gh/swashata/wpackio-enqueue/branch/master/graph/badge.svg)](https://codecov.io/gh/swashata/wpackio-enqueue) [![Latest Stable Version](https://poser.pugx.org/wpackio/enqueue/v/stable)](https://packagist.org/packages/wpackio/enqueue)

This is the PHP companion of [`@wpackio/scripts`](https://github.com/swashata/wp-webpack-script).

It gives you all the APIs you will need to properly consume assets generated from
`@wpackio/scripts` from your WordPress plugins or themes.

## Detailed Documentation

This README only covers the very basics and a quick start guide, without explaining
the overall usage.

Please visit our [official documentation](https://wpack.io) site for detailed
instruction.

## Installation

### Using Composer

We recommend using [composer](https://getcomposer.org/) for using this [library](https://packagist.org/packages/wpackio/enqueue).

```bash
composer require wpackio/enqueue
```

Then in your plugin main file or `functions.php` file of your theme, load
composer auto-loader.

```php
<?php

// Require the composer autoload for getting conflict-free access to enqueue
require_once __DIR__ . '/vendor/autoload.php';

// Instantiate
$enqueue = new \WPackio\Enqueue( 'appName', 'outputPath', '1.0.0', 'plugin', __FILE__ );
```

### Manual

If you do not wish to use composer, then download the file [`Enqueue.php`](inc/Enqueue.php).

Remove the namespace line `namespace WPackio;` and rename the classname from
`Enqueue` to something less generic, like `MyPluginEnqueue`. This ensures
conflict-free loading.

Then require the file in your plugin entry-point or `functions.php` file of your theme.

```php
<?php

// Require the file yourself
require_once __DIR__ . '/inc/MyPluginEnqueue.php';

// Instantiate
$enqueue = new MyPluginEnqueue( 'appName', 'outputPath', '1.0.0', 'plugin', __FILE__ );
```

## Getting Started

Which ever way, you choose to install, you have to make sure to instantiate the
class early during the entry-point of your plugin or theme.

This ensures that we hava necessary javascript in our website frontend and admin end
to make webpack code-splitting and dynamic import work.

A common pattern may look like this.

```php
<?php
// Assuming this is the main plugin file.

// Require the composer autoload for getting conflict-free access to enqueue
require_once __DIR__ . '/vendor/autoload.php';

// Do stuff through this plugin
class MyPluginInit {
	/**
	 * @var \WPackio\Enqueue
	 */
	public $enqueue;

	public function __construct() {
		// It is important that we init the Enqueue class right at the plugin/theme load time
		$this->enqueue = new \WPackio\Enqueue( 'wpackplugin', 'dist', '1.0.0', 'plugin', __FILE__ );
		// Enqueue a few of our entry points
		add_action( 'wp_enqueue_scripts', [ $this, 'plugin_enqueue' ] );
	}


	public function plugin_enqueue() {
		$this->enqueue->enqueue( 'app', 'main', [] );
		$this->enqueue->enqueue( 'app', 'mobile', [] );
		$this->enqueue->enqueue( 'foo', 'main', [] );
	}
}


// Init
new MyPluginInit();
```

## Default configuration when calling `enqueue`

```php
[
	'js' => true,
	'css' => true,
	'js_dep' => [],
	'css_dep' => [],
	'in_footer' => true,
	'media' => 'all',
	'main_js_handle' => null,
	'runtime_js_handle' => null,
];
```

`main_js_handle` is added in 3.3 and can predictably set the handle of primary
JavaScript file. Useful for translations etc.

`runtime_js_handle` is added in 3.4 and can predictably set the handle of the
common runtime JavaScript. This is useful to localize/translate dependent script
handles in the same files entry. By calling `wp_set_script_translations` on the
runtime you can collectively enqueue translate json for all the dependencies on
the entries.

For information on usage and API, please visit official documentation site
[wpack.io](https://wpack.io).

## Avoid conflict in multiple WordPress Plugins

Always require the latest version of `Wpackio\Enqueue`. The autoloader is set
to load only one instance and will not conflict with existing class.

However, if you want to load conflict free, kindly use [Strauss](https://github.com/BrianHenryIE/strauss).
