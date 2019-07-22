**ResponsivePicture** is a PHP library that enables WordPress theme authors to automatically resize images* in responsive layouts. Just upload a high-res image in your media library, and let ResponsivePics take care of the rest. Supports art-directed crops, background images and respects aspect ratios.

ResponsivePicture is useful when you have a responsive grid layout (like Bootstrap, but can be any framework) and need images to adapt to responsive designs. ResponsivePics automatically resizes and / or crops your uploaded pictures to fit your layouts.

ResponsivePicture saves bandwidth and lets your site load faster.

*ReponsivePics does not handle images in the WordPress wysiwig editor, it’s only useful for theme authors that use images or photos in their themes. It automatically handles retina or hdpi images via media queries.*

# Documentation
For full documentation and examples visit: [responsive.pics](https://responsive.pics)

# Table of contents
1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Usage](#usage)
4. [Sizes](#sizes)
5. [Features](#features)

## Installation <a name="installation"></a>

### Composer
Run this command in your wordpress theme folder:
`$ composer require "booreiland/responsive-picture"`
And make sure to load Composer’s **autoloader** file by adding this line to your theme’s **functions.php**:
`require_once (__DIR__.'/vendor/autoload.php');`

### Browser Support
Currently the `<picture>` element is supported in all the modern browsers except **Internet Explorer 11**.

In order to enable support for the picture element and associated features in browsers that do not yet support them, you can use a polyfill. We recommend using [Picturefill](http://scottjehl.github.io/picturefill/).

To install **Picturefill** in your wordpress theme as a node module, run the following command from your theme directory:

#### npm
`npm install --save picturefill`
#### Yarn
`yarn add picturefill`

And import the package in your theme’s global javascript file:
`import 'picturefill';`

## Configuration <a name="configuration"></a>
By default, Responsive Picture will use the Bootstrap 4 SCSS variables for defining:

The amount of **grid columns**:
`$grid-columns: 12;`
The **grid gutter width** in pixels:
`$grid-gutter-width: 30px;`
The **grid breakpoints** in pixels:
```php
$grid-breakpoints: (
 xs: 0,
 sm: 576px,
 md: 768px,
 lg: 992px,
 xl: 1200px
);
```
And the **maximum widths of the containers** in pixels:
```php
$container-max-widths: (
 sm: 540px,
 md: 720px,
 lg: 960px,
 xl: 1140px
);
```
*Note: Responsive Picture will add the xs container max width for you (= 576), based upon the default sm grid breakpoint (= 576px).*

If you have customized the bootstrap defaults or if you’re using a different grid system ([Foundation](https://foundation.zurb.com), [Materialize](https://materializecss.com) etc.), or even if you want to add extra breakpoints & container widths, you can pass your own grid variables to the Responsive Pics library.

Add these lines to your theme’s **functions.php** and make sure to check if the `ResponsivePicture` class exists:
```php
/*
 * Set Responsive Picture variables
 */
if (class_exists('ResponsivePicture')) {
	ResponsivePicture::setColumns(12);
	ResponsivePicture::setGutter(30);
	ResponsivePicture::setBreakPoints([
		'xs'    => 0,
		'sm'    => 576,
		'md'    => 768,
		'lg'    => 992,
		'xl'    => 1200,
		'xxl'   => 1400,
		'xxxl'  => 1600,
		'xxxxl' => 1920
	]);
	ResponsivePicture::setGridWidths([
		'xs'    => 576,
		'sm'    => 768,
		'md'    => 992,
		'lg'    => 1200,
		'xl'    => 1400,
		'xxl'   => 1600,
		'xxxl'  => 1920
	]);
}
```

## Usage <a name="usage"></a>

### Picture Element
For inserting a responsive <picture> element in your template, use the `get` function:
`ResponsivePicture::get();`
with the following parameters:

| Parameter  | Type            | Required | Default  | Definition
| -----------|:---------------:| --------:|---------:|---------------------------------
| id         | number          | yes      |          | The WordPress image id (e.g. 1).
| sizes      | string          | yes      |          | A comma-separated string of preferred image sizes (e.g. `'xs-12, sm-6, md-4, lg-3'`). See the Sizes section for more information.
| classes    | string or array | optional | `null`   | Additional CSS classes you want to add to the picture element (e.g. `'my_picture_class'` or `['my_picture_class', 'my_second_picture_class']`).
| lazyload   | boolean         | optional | `false`  | When `true` enables `lazyload` classes and data-srcset attributes. See the [Lazyloading section]() for more information.
| intrinsic  | boolean         | optional | `false`  | When `true` enables `intrinsic` classes and data-aspectratio attributes. See the [Intrinsic Aspectratio section]() for more information.

### Background Image
For inserting a responsive background image in your template, use the `get_background` function:
`ResponsivePicture::get_background();`
with the following parameters:

## Sizes <a name="sizes"></a>
The second paragraph text

## Features <a name="features"></a>
The second paragraph text


© 2017—2019 Booreiland, all rights reserved.