# ResponsivePics
Supercharge Wordpress theme authoring

ResponsivePics is a PHP library that enables Wordpress theme authors to automatically resize images* in responsive layouts. Just upload a high-res image in your media library, and let ResponsivePics take care of the rest. Supports art-directed crops, background images and respects aspect ratios.

ResponsivePics is useful when you have a responsive grid layout (like Bootstrap, but can be any framework) and need images to adapt to responsive designs. ResponsivePics automatically resizes and / or crops your uploaded pictures to fit your layouts.

ResponsivePics saves bandwidth and lets your site load faster. ResponsivePics is €25,-. It probably saves you a day of developing it yourself.

* ReponsivePics does not handle images in the Wordpress wysiwig editor, it's only useful for theme authors that use images or photos in their themes. It automatically handles retina or hdpi images via media queries.

## Examples

Let's do this thing

Throw this PHP code in your theme:

`ResponsivePics::get(1, 'xs-12, sm-6, md-4');`
This method will output a <picture> element that spans 12 columns on xs layouts, 6 columns on small layouts and 4 columns on medium and larger layouts. It will automatically resize and crop images for each state and handle hdpi images.

Depending on the maximum size of the uploaded image with ID "1", the output will be something like:

```html
<picture>
    <source media="(min-width: 768px)"
        srcset="/app/uploads/2017/01/my-image-992x1975.jpg 1x,
                /app/uploads/2017/01/my-image-992x1975@2x.jpg 2x">
    <source media="(min-width: 544px)"
        srcset="/app/uploads/2017/01/my-image-768x1529.jpg 1x,
                /app/uploads/2017/01/my-image-768x1529@2x.jpg 2x">
    <source media="(min-width: 0px)"
        srcset="/app/uploads/2017/01/my-image-544x1083.jpg 1x,
                /app/uploads/2017/01/my-image-544x1083@2x.jpg 2x">
    <img src="/app/uploads/2017/01/my-image-544x1083.jpg">
</picture>
```

## Custom dimensions

ResponsivePics::get(1, '400:200 300, 800:400 600');
This command will output a picture element to show a 200 × 300 image on layouts that are 400 pixels wide and a 400 × 600 image on layouts that are wider than 800 pixels.

## Cropping

`ResponsivePics::get(1, '400:200 300, 800:400 600', 'my-picture');`
This command will output a picture element to show a 200 × 300 image on layouts that are 400 pixels wide and a 400 × 600 image on layouts that are wider than 600 pixels. In addition, it adds the class my-picture to the picture element.

## Full syntax

`ResponsivePics::get[_background](id, 'breakpoint:width [/factor|height]|crop_x crop_y, …', 'class-name');`

breakpoint: a number or a key in $breakpoints (e.g. "xs")
			if not defined, and width is a number, breakpoint will be the same as the width
			if not defined, and width is a column definition, breakpoint will be the corresponding breakpoint
			(e.g. if width is "xs-8", breakpoint will be "xs")
width     : a number or a column definition
			a column definition is a key in $grid_widths plus a dash and a column span number (e.g. "xs-8")
			if column span number is "full", the full width of the next matching $breakpoint is used (e.g. "xs-full")
height    : a number
factor    : a factor of width
crop_x    : t(op), r(ight), b(ottom), l(eft) or c(enter),
crop_y    : t(op), r(ight), b(ottom), l(eft) or c(enter)
			if crop_y is not defined, crop_x will be treated as a shortcut:
			"c" = "center center", "t" = "top center", r = "right center", "b" = "center bottom", "l" = "left center"
class-name: a class name to add to the html element
lazyload  : (boolean, default: false) if true:
			- adds a 'lazyload' class to the picture img element
			- swaps the 'src' with 'data-src' attributes on the picture source elements
			- this will enable you to use a lazy loading plugin such as Lazysizes: https://github.com/aFarkas/lazysizes
intrinsic : (boolean, default: false) if true:
			- adds an 'intrinsic' class to the picture element and a 'intrinsic__item' class to the picture img element
			- adds 'data-aspectratio' attributes on the picture source and img elements
			- this will enable you to pre-occupy the space needed for an image by calculating the height from the image width or the width from the height
			  with an intrinsic plugin such as the lazysizes aspectratio extension

## Examples

```php
ResponsivePics::get(1, 'xs-12, sm-6, md-4');
ResponsivePics::get(1, '400:200 300, 800:400 600', 'my-picture');
ResponsivePics::get(1, '400:200 200|c, 800:400 400|l t');
ResponsivePics::get(1, 'xs-full|c, sm-12/0.5|c md-12/0.25|c');

ResponsivePics::get_background(1, 'xs:200 200|c, lg:400 400');
```

## Setup

```php
// breakpoints used for "media(min-width: x)" in picture element
private static $breakpoints = [
	'xs'    => 0,
	'sm'    => 544,
	'md'    => 768,
	'lg'    => 992,
	'xl'    => 1200
];

// grid system should match the grid used in css
private static $columns = 12;
private static $gutter  = 30;
private static $grid_widths = [
	'xs'  => 544, // ~100%
	'sm'  => 720, // ~100%
	'md'  => 720,
	'lg'  => 920,
	'xl'  => 1100
];
```

© 2017—2018 Booreiland, all rights reserved.