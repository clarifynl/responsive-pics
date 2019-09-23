**ResponsivePics** is a PHP library that enables WordPress theme authors to automatically resize images* in responsive layouts. Just upload a high-res image in your media library, and let ResponsivePics take care of the rest. Supports art-directed crops, background images and respects aspect ratios.

ResponsivePics is useful when you have a responsive grid layout (like Bootstrap, but can be any framework) and need images to adapt to responsive designs. ResponsivePics automatically resizes and / or crops your uploaded pictures to fit your layouts.

ResponsivePics saves bandwidth and lets your site load faster.

*ReponsivePics does not handle images in the WordPress wysiwig editor, it’s only useful for theme authors that use images or photos in their themes. It automatically handles retina or hdpi images via media queries.*

# Documentation
For full documentation and examples visit: [responsive.pics](https://responsive.pics)

# Table of contents
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Usage](#usage)
5. [Sizes](#sizes)
6. [Features](#features)

## Requirements <a name="requirements"></a>
* PHP 5.2+
* WordPress 3.5+
* `WP Cron` enabled or a Real Cron Job set up on your server

### Composer
Run this command in your wordpress theme folder:
`$ composer require "booreiland/responsive-pics"`
And make sure to load Composer’s **autoloader** file by adding this line to your theme’s **functions.php**:
`require_once (__DIR__.'/vendor/autoload.php');`

## Installation <a name="installation"></a>

### Composer
Run this command in your wordpress theme folder:
`$ composer require "booreiland/responsive-pics"`
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
Responsive Pics uses the following default variables:

| Variable       | Type     | Default    | Definition
| --------------:|---------:|-----------:|-----------
| columns        | number   | `12`       | The amount of columns your grid layout uses
| gutter         | number   | `30`       | The gutter width in pixels (space between grid columns)
| breakpoints    | array    | `[
  'xs'    => 0,
  'sm'    => 576,
  'md'    => 768,
  'lg'    => 992,
  'xl'    => 1200,
  'xxl'   => 1400,
  'xxxl'  => 1600
]`    | The media query breakpoints Responsive Pics will use for creating and serving your image sources
| grid_widths     | array   | `[
	'xs'    => 576,
	'sm'    => 540,
	'md'    => 720,
	'lg'    => 960,
	'xl'    => 1140,
	'xxl'   => 1140,
	'xxxl'  => 1140
]`    | The maximum widths of your layout in pixels Responsive Pics will use for resizing your images
| lazyload_class  | string   | `lazyload` | The css class to be added on the picture `img` tag when `lazyload` is enabled
| image_quality   | number   | `90`       | The image compression quality in percentage used in the `WP_Image_Editor` when resizing images
| cron_interval   | number   | `0.1`      | The time interval in minutes when WordPress will check if there are any remaining image resize tasks left in it's cron job

By default, Responsive Pics will use the Bootstrap 4 SCSS variables for defining:

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
*Note: Responsive Pics will add the xs container max width for you (= 576), based upon the default sm grid breakpoint (= 576px).*

If you have customized the bootstrap defaults or if you’re using a different grid system ([Foundation](https://foundation.zurb.com), [Materialize](https://materializecss.com) etc.), or even if you want to add extra breakpoints & container widths, you can pass your own grid variables to the Responsive Pics library.

Add these lines to your theme’s **functions.php** and make sure to check if the `ResponsivePics` class exists:
```php
/*
 * Set Responsive Pics variables
 */
if (class_exists('ResponsivePics')) {
	ResponsivePics::setColumns(12);
	ResponsivePics::setGutter(30);
	ResponsivePics::setBreakPoints([
		'xs'    => 0,
		'sm'    => 576,
		'md'    => 768,
		'lg'    => 992,
		'xl'    => 1200,
		'xxl'   => 1400,
		'xxxl'  => 1600,
		'xxxxl' => 1920
	]);
	ResponsivePics::setGridWidths([
		'xs'    => 576,
		'sm'    => 768,
		'md'    => 992,
		'lg'    => 1200,
		'xl'    => 1400,
		'xxl'   => 1600,
		'xxxl'  => 1920
	]);
	ResponsivePics::setImageQuality(85);
	ResponsivePics::setCronInterval(0.5);
}
```

### Helper Functions
You can retrieve any variables used in Responsive Pics by running one of these helper functions:

`ResponsivePics::getColumns();`       Will return `12`
`ResponsivePics::getGutter();`        Will return `30`
`ResponsivePics::getBreakpoints();`   Will return an array of breakpoints
`ResponsivePics::getGridWidths();`    Will return an array of grid widths
`ResponsivePics::getLazyLoadClass();` Will return `lazyload`
`ResponsivePics::getImageQuality();`  Will return `90`
`ResponsivePics::getCronInterval();`  Will return `0.1`

## Usage <a name="usage"></a>

### Picture Element
For inserting a responsive <picture> element in your template, use the `get` function:
`ResponsivePics::get();`
with the following parameters:

| Parameter  | Type            | Required | Default  | Definition
| -----------|:---------------:| --------:|---------:|---------------------------------
| id         | number          | yes      |          | The WordPress image id (e.g. 1).
| sizes      | string          | yes      |          | A comma-separated string of preferred image sizes (e.g. `'xs-12, sm-6, md-4, lg-3'`). See the [Sizes section](#sizes) for more information.
| classes    | string or array | optional | `null`   | Additional CSS classes you want to add to the picture element (e.g. `'my_picture_class'` or `['my_picture_class', 'my_second_picture_class']`).
| lazyload   | boolean         | optional | `false`  | When `true` enables `lazyload` classes and data-srcset attributes. See the [Lazyloading section](#lazyloading) for more information.
| intrinsic  | boolean         | optional | `false`  | When `true` enables `intrinsic` classes and data-aspectratio attributes. See the [Intrinsic Aspectratio section](#intrinsic) for more information.

### Background Image
For inserting a responsive background image in your template, use the `get_background` function:
`ResponsivePics::get_background();`
with the following parameters:

| Parameter  | Type            | Required | Default  | Definition
| -----------|:---------------:| --------:|---------:|---------------------------------
| id         | number          | yes      |          | The WordPress image id (e.g. 1).
| sizes      | string          | yes      |          | A comma-separated string of preferred image sizes (e.g. `'xs-12, sm-6, md-4, lg-3'`). See the [Sizes section](#sizes) for more information.
| classes    | string or array | optional | `null`   | Additional CSS classes you want to add to the background element (e.g. `'my_bg_class'` or `['my_bg_class', 'my_second_bg_class']`).

### Process
* When visiting the front-end page where the `ResponsivePics` function call is made, the library will try and resize and/or crop your image on the fly and save it to in the same uploads folder as the original image.
* Once the image variation is created, it will skip the creation process of that variation on the next page load. The first page load can therefore take a while.
* When you change one of the image size parameters, it will automatically try and create the new image variation on the next page load.
* When the original image does not meet the dimension requirements of the requested image size, it will skip that image size variation and proceed to the next image size.
* Alt text will automatically be added on the picture img element if the original image in the media library has one.

### Supported image formats
The following image file formats are supported:

| File format | MIME Type  | Properties
| ------------|:----------:| ---------------------------------
| jp(e)g      | image/jpeg |
| png         | image/png  | When the png contains an **alpha channel**, an extra `'has-alpha'` class will be added to the picture image element for additional styling.
| gif         | image/gif  | When the gif is **animated** (it will check for multiple header frames), no image resizing or cropping will be done to prevent discarding the animation.

Any other image formats, will not be resizes or cropped.

## Sizes <a name="sizes"></a>

### Full Syntax
For each comma-separated image size being passed to the get or get_background function, the following syntax is applied:
`breakpoint:width [/factor|height]|crop_x crop_y`
with the following parameters:

| Parameter  | Type             | Required | Default | Definition
| -----------|:----------------:| --------:|--------:|---------------------------------
| breakpoint | number or string | yes      |         | If undefined, and `width` is a number, breakpoint will be the same as the width. If undefined, and `width` is a column definition, breakpoint will be the corresponding breakpoint (e.g. if width is `'xs-8'`, breakpoint will be `'xs'`).
| width      | number or string | yes      |         | A column definition is a key in `$grid_widths` plus a dash and a column span number (e.g. `'xs-8'`). If column span number is `full`, the full width of the next matching `$breakpoint` is used (e.g. `'xs-full'`).
| height     | number           | optional |         | The desired height of the image (e.g. `500`).
| factor     | number           | optional |         | A factor of the width (e.g. `0.75`).
| crop_x     | string           | optional | c       | Crop position in horizontal direction: `t(op), r(ight), b(ottom), l(eft)` or `c(enter)`.
| crop_y     | string           | optional | c       | Crop position in vertical direction: `t(op), r(ight), b(ottom), l(eft)` or `c(enter)`. If undefined, `crop_x` will be treated as a shortcut: `'c' = 'center center', 't' = 'top center', r = 'right center', 'b' = 'center bottom', 'l' = 'left center'`.

## Features <a name="features"></a>

### Lazyloading <a name="lazyloading"></a>
When enabling the `lazyload` option in the `ResponsivePics::get()` function call, this library automatically:

* adds a `lazyload` class to the picture img element.
* swaps the srcset with data-srcset attributes on the picture source elements.

This will enable you to use a lazy loading plugin such as Lazysizes.

You can also set your own lazyload class by passing it to Responsive Pics library in your theme’s **functions.php**:
```php
if (class_exists('ResponsivePics')) {
	ResponsivePics::setLazyLoadClass('lazy');
}
```
To install **Lazysizes** in your wordpress theme as a node module, run the following command from your theme directory:
#### npm
`npm install --save lazysizes`
#### Yarn
`yarn add lazysizes`
And import the package in your theme’s global javascript file:
```javascript
import 'lazysizes';
```

### Intrinsic Aspectratio <a name="intrinsic"></a>
When enabling the `intrinsic` option in the `ResponsivePics::get()` function call, this library automatically:

* adds a intrinsic class to the picture element and a intrinsic__item class to the picture img element.
* adds data-aspectratio attributes on the picture source and img elements with the calculated source image ratio.

This will enable you to pre-occupy the space needed for an image by calculating the height from the image width or the width from the height with an intrinsic plugin such as the [lazysizes aspectratio extension](https://github.com/aFarkas/lazysizes/tree/gh-pages/plugins/aspectratio).

To use the **Lazysizes aspectratio extension** in your wordpress theme, first install **lazysizes** as a node module as described in the [Lazyloading section](#lazyloading) and import the extension in your theme’s global javascript file:
```javascript
import 'lazysizes/plugins/aspectratio/ls.aspectratio.js';
```

## Maintainers
**ResponsivePics** is developed and maintained by:

[@monokai](https://github.com/monokai) (creator)
[@twansparant](https://github.com/Twansparant) (collaborator)

## Copyright
Code and documentation copyright 2017-2019 by [Booreiland](https://booreiland.amsterdam). Code released under the [MIT License](https://github.com/booreiland/responsive-pics/blob/master/LICENSE). Docs released under Creative Commons.