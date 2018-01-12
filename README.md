# Responsive Picture

Responsive Picture is a Wordpress tool for resizing images on the fly.
It uses a concise syntax for determining the image sizes you need in your template.
You can define number of columns, aspect ratios and crop settings.
It handles @2x images and missing breakpoints automatically.

syntax    : ResponsivePicture::get[_background](id, 'breakpoint:width [/factor|height]|crop_x crop_y, …', 'class-name', lazyload, intrinsic);

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

examples  : ResponsivePicture::get(1, 'xs-12, sm-6, md-4');
			ResponsivePicture::get(1, '400:200 300, 800:400 600', 'my-picture');
			ResponsivePicture::get(1, '400:200 200|c, 800:400 400|l t');
			ResponsivePicture::get(1, 'xs-full|c, sm-12/0.5|c, md-12/0.25|c');

			ResponsivePicture::get_background(1, 'xs:200 200|c, lg:400 400');


Javascript dependencies:

			A responsive image polyfill such as Picturefill:
			http://scottjehl.github.io/picturefill/

			A lazy loader for images such as Lazysizes:
			https://github.com/aFarkas/lazysizes

			import 'picturefill';
			import 'lazysizes';
			import 'lazysizes/plugins/aspectratio/ls.aspectratio.js';

TO DO'S:
* If you want to resize and/or crop with a fixed heigth, but the width is not sufficient, it skips the resize alltogether.
  Better would be if you can resize with only a fixed width or height and the 2nd dimension is calculated based upon original dimensions
* Support for multiple background images

© 2017 Booreiland