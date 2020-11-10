# ResponsivePics

**ResponsivePics** is a WordPress plugin that enables WordPress theme authors to automatically resize images<sup>*</sup> in responsive layouts.

* Saves bandwidth and lets your site load faster
* No need anymore for defining custom image sizes
* Adds retina support to your theme images
* Supports art-directed responsive images
* Supports image srcset & sizes attributes
* Supports aspect ratio based crops
* Supports lazyloading
* Supports intrinsic ratio boxes
* With full REST API support
* Uses background processing for resizing and cropping images

<sub><sup>*ReponsivePics does not handle images in the WordPress wysiwig editor, it’s only useful for theme authors that use images or photos in their themes. It automatically handles retina or hdpi images via media queries.</sup></sub>

# Documentation
For full documentation and examples visit: [responsive.pics](https://responsive.pics)

# Table of contents
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Usage](#usage)
5. [Sizes](#sizes)
6. [Process](#process)
7. [Features](#features)

## Requirements <a name="requirements"></a>
<table>
  <thead>
    <tr>
      <th>Prerequisite</th>
      <th>How to check</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>PHP</strong> &gt;= 5.6.x</td>
      <td><code>php -v</code></td>
    </tr>
    <tr>
      <td><strong>Wordpress</strong> &gt;= 3.5.x</td>
      <td><code>wp core version</code></td>
    </tr>
    <tr>
      <td><strong>WP-Cron</strong> enabled or a <a href="https://kinsta.com/knowledgebase/disable-wp-cron/" target="_blank">real cron job</a> set up</td>
      <td><a href="https://developer.wordpress.org/plugins/cron/simple-testing/" target="_blank"><code>test WP-Cron</code></a></td>
    </tr>
  </tbody>
</table>

## Installation <a name="installation"></a>
You can install this plugin via the command-line or the WordPress admin panel.

### via Command-line
If you're [using Composer to manage WordPress](https://roots.io/using-composer-with-wordpress/), add **ResponsivePics** to your project's dependencies.

```sh
composer require booreiland/responsive-pics
```
Then activate the plugin via [wp-cli](http://wp-cli.org/commands/plugin/activate/).

```sh
wp plugin activate responsive-pics
```

### via WordPress Admin Panel
1. Download the [latest zip](https://github.com/booreiland/responsive-pics/releases/latest) of this repo.
2. In your WordPress admin panel, navigate to **Plugins->Add New**
3. Click **Upload Plugin**
4. Upload the zip file that you downloaded.
5. **Activate** the plugin after installation.

### Browser Support
Currently the `<picture>` element and `srcset` and `sizes` attributes on the `<img>` element are supported in all modern browsers except **Internet Explorer 11**.

In order to enable support for the picture element and associated features in browsers that do not yet support them, you can use a polyfill. We recommend using [Picturefill](http://scottjehl.github.io/picturefill/).

To install **Picturefill** in your wordpress theme as a node module, run the following command from your theme directory:

#### npm
`npm install --save picturefill`
#### Yarn
`yarn add picturefill`

And import the package in your theme’s global javascript file:
`import 'picturefill';`

## Configuration <a name="configuration"></a>
ResponsivePics uses the following default variables:

| Variable                  | Type    | Default    | Definition
| ------------------------- | ------- | ---------- | ----------
| `$columns`                | number  | `12`       | The amount of columns your grid layout uses
| `$gutter`                 | number  | `30`       | The gutter width in pixels (space between grid columns)
| `$breakpoints`            | array   | `['xs' => 0, 'sm' => 576, 'md' => 768, 'lg' => 992, 'xl' => 1200, 'xxl' => 1400]` | The media query breakpoints ResponsivePics will use for creating and serving your image sources
| `$grid_widths`            | array   | `['xs' => 576, 'sm' => 540, 'md' => 720, 'lg' => 960, 'xl' => 1140, 'xxl' => 1320]` | The maximum widths of your layout in pixels ResponsivePics will use for resizing your images
| `$max_width_factor`       | number  | `2`        | The maximum factor of the width to use for resizing and cropping the height of an image source
| `$lazyload_class`         | string  | `lazyload` | The css class to be added on the picture `img` tag when `lazyload` is enabled
| `$image_quality`          | number  | `90`       | The image compression quality in percentage used in the `WP_Image_Editor` when resizing images
| `$wp_rest_cache`          | boolean | `false`    | Wether to enable cache in the WP Rest API response headers
| `$wp_rest_cache_duration` | number  | `3600`     | The cache duration (max-age) in seconds of the WP Rest API Cache-Control header

By default, ResponsivePics will use the [Bootstrap 4 SCSS variables](https://github.com/twbs/bootstrap/blob/main/scss/_variables.scss#L285/) for defining:

The amount of **grid columns**: `$grid-columns: 12;`  
The **grid gutter width** in pixels: `$grid-gutter-width: 30px;`  
The **grid breakpoints** in pixels:

```scss
$grid-breakpoints: (
 xs: 0,
 sm: 576px,
 md: 768px,
 lg: 992px,
 xl: 1200px,
 xxl: 1400px
);
```
And the **maximum widths of the containers** in pixels:

```scss
$container-max-widths: (
 sm: 540px,
 md: 720px,
 lg: 960px,
 xl: 1140px,
 xxl: 1320px
);
```

*Note: ResponsivePics will add the `xs` container max width for you (= 576), based upon the default sm grid breakpoint (= 576px).*

If you have customized the bootstrap defaults or if you’re using a different grid system ([Foundation](https://foundation.zurb.com), [Materialize](https://materializecss.com) etc.), or even if you want to add extra breakpoints & container widths, you can pass your own grid variables to the ResponsivePics library.

Add these lines to your theme’s **functions.php** and make sure to check if the `ResponsivePics` class exists:

```php
/*
 * Set ResponsivePics variables
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
	ResponsivePics::setMaxWidthFactor(4);
	ResponsivePics::setImageQuality(85);
	ResponsivePics::setRestApiCache(true);
	ResponsivePics::setRestApiCacheDuration(86400);
}
```

### Helper Functions
You can retrieve any variables used in ResponsivePics by running one of these helper functions:

```php
ResponsivePics::getColumns();              // Will return $columns
ResponsivePics::getGutter();               // Will return $gutter
ResponsivePics::getBreakpoints();          // Will return $breakpoints
ResponsivePics::getGridWidths();           // Will return $grid_widths
ResponsivePics::getMaxWidthFactor();       // Will return $max_width_factor
ResponsivePics::getLazyLoadClass();        // Will return $lazyload_class
ResponsivePics::getImageQuality();         // Will return $image_quality
ResponsivePics::getRestApiCache();         // Will return $wp_rest_cache
ResponsivePics::getRestApiCacheDuration(); // Will return $wp_rest_cache_duration
```

## Usage <a name="usage"></a>

### Picture Element

For inserting a responsive `<picture>` element in your template, use the `get_picture` function or the `get-picture` API endpoint with the available parameters.

#### PHP
```php
ResponsivePics::get_picture(id, sizes, classes, lazyload, intrinsic);
```

#### REST API
```curl
GET /wp-json/responsive-pics/v1/get-picture/<id>?sizes=<sizes>&classes=<classes>&lazyload=<lazyload>&intrinsic=<intrinsic>
```

#### Picture Parameters

| Parameter  | Type        | Required | Default  | Definition
| ---------- | ----------- | -------- | -------- | --------------------------------
| id         | number      | yes      |          | The WordPress image id (e.g. 1).
| sizes      | string      | yes      |          | A comma-separated string of preferred image sizes (e.g. `'xs-12, sm-6, md-4, lg-3'`). See the [Sizes section](#sizes) for more information.
| classes    | string      | optional | `null`   | A comma-separated string of additional CSS classes you want to add to the picture element (e.g. `'my_picture_class'` or `'my_picture_class, my_second_picture_class'`).
| lazyload   | boolean     | optional | `false`  | When `true` enables `lazyload` classes and data-srcset attributes. See the [Lazyloading section](#lazyloading) for more information.
| intrinsic  | boolean     | optional | `false`  | When `true` enables `intrinsic` classes and data-aspectratio attributes. See the [Intrinsic Aspectratio section](#intrinsic) for more information.


### Image Element
For inserting a responsive `<img>` element in your template, use the `get_image` function or the `get-image` API endpoint with the available parameters.

#### PHP
```php
ResponsivePics::get_image(id, sizes, crop, classes, lazyload);
```

#### REST API
```curl
GET /wp-json/responsive-pics/v1/get-image/<id>?sizes=<sizes>&crop=<crop>&classes=<classes>&lazyload=<lazyload>
```

#### Image Parameters

| Parameter  | Type        | Required | Default  | Definition
| ---------- | ----------- | -------- | -------- | --------------------------------
| id         | number      | yes      |          | The WordPress image id (e.g. 1).
| sizes      | string      | yes      |          | A comma-separated string of preferred image sizes (e.g. `'xs-12, sm-6, md-4, lg-3'`). See the [Sizes section](#sizes) for more information.
| crop       | string      | optional | `false`  | A crop-factor of the width for the desired height within the default range of `0-2` (e.g. `0.75`) with optional crop positions (e.g. <code>0.75&#124;c t</code>)
| classes    | string      | optional | `null`   | A comma-separated string of additional CSS classes you want to add to the img element (e.g. `'my_img_class'` or `'my_img_class, my_second_img_class'`).
| lazyload   | boolean     | optional | `false`  | When `true` enables `lazyload` classes and data-srcset attributes. See the [Lazyloading section](#lazyloading) for more information.


### Background Image
For inserting a responsive background image in your template, use the `get_background` function or the `get-background` API endpoint with the available parameters.

#### PHP
```php
ResponsivePics::get_background(id, sizes, classes);
```

#### REST API
```curl
GET /wp-json/responsive-pics/v1/get-background/<id>?sizes=<sizes>&classes=<classes>
```

#### Background Parameters

| Parameter  | Type        | Required | Default  | Definition
| -----------| ----------- | -------- | -------- | --------------------------------
| id         | number      | yes      |          | The WordPress image id (e.g. 1).
| sizes      | string      | yes      |          | A comma-separated string of preferred image sizes (e.g. `'xs-12, sm-6, md-4, lg-3'`). See the [Sizes section](#sizes) for more information.
| classes    | string      | optional | `null`   | A comma-separated string of additional CSS classes you want to add to the background element (e.g. `'my_bg_class'` or `'my_bg_class, my_second_bg_class'`).


### Supported image formats
The following image file formats are supported:

| File format | MIME Type  | Properties
| ----------- | ---------- | ---------------------------------
| jp(e)g      | image/jpeg |
| png         | image/png  | When the png contains an **alpha channel**, an extra `'has-alpha'` class will be added to the picture image element for additional styling.
| gif         | image/gif  | When the gif is **animated** (it will check for multiple header frames), no image resizing or cropping will be done to prevent discarding the animation.

Any other image formats, will not be resizes or cropped.

## Sizes <a name="sizes"></a>

### Image sizes
The following syntax is available for each image size in the `sizes` parameter:

```php
breakpoint:width
```

| Parameter  | Type             | Required | Default | Definition
| ---------- | ---------------- | -------- | ------- | --------------------------------
| breakpoint | number or string | yes      |         | If undefined, and `width` is a number, breakpoint will be the same as the width. If undefined, and `width` is a column definition, breakpoint will be the corresponding breakpoint (e.g. if width is `'xs-8'`, breakpoint will be `'xs'`).
| width      | number or string | yes      |         | A column definition is a key in `$grid_widths` plus a dash and a column span number (e.g. `'xs-8'`).<br>If the column span number is suffixed with `-full` (e.g. `'xs-8-full'`), the column width is not calculated as a percentage of the $grid_width, but of the next matching `$breakpoint` width.<br>You can also use a shorthand `full` as span number (e.g. `'xs-full'`) for full width columns (e.g. `'xs-12-full'`).

### Picture & background sizes
Since the `<picture>` element and background images support art directed images, the following full syntax is available for each image size in the `sizes` parameter:

```php
breakpoint:width [/factor|height]|crop_x crop_y
```

The following parameters are available in the sizes syntax:

| Parameter  | Type             | Required | Default | Definition
| ---------- | ---------------- | -------- | ------- | --------------------------------
| breakpoint | number or string | yes      |         | If undefined, and `width` is a number, breakpoint will be the same as the width. If undefined, and `width` is a column definition, breakpoint will be the corresponding breakpoint (e.g. if width is `'xs-8'`, breakpoint will be `'xs'`).
| width      | number or string | yes      |         | A column definition is a key in `$grid_widths` plus a dash and a column span number (e.g. `'xs-8'`).<br>If the column span number is suffixed with `-full` (e.g. `'xs-8-full'`), the column width is not calculated as a percentage of the $grid_width, but of the next matching `$breakpoint` width.<br>You can also use a shorthand `full` as span number (e.g. `'xs-full'`) for full width columns (e.g. `'xs-12-full'`).
| height     | number           | optional |         | The desired height of the image (e.g. `500`).
| factor     | number           | optional |         | A crop-factor of the width for the desired height within the default range of `0-2` (e.g. `0.75`).
| crop_x     | string           | optional | c       | Crop position in horizontal direction: `l(eft)`, `c(enter)` or `r(ight)`.
| crop_y     | string           | optional | c       | Crop position in vertical direction: `t(op), c(enter)` or `b(ottom)`. If undefined, `crop_x` will be treated as a shortcut: `'c' = 'center center', 't' = 'top center', r = 'right center', 'b' = 'center bottom', 'l' = 'left center'`.


## Process <a name="process"></a>

1. When visiting a front-end page and a `ResponsivePics` function call is made, this library will add the resize and/or crop image task as a job to the background process queue using [Action Scheduler](https://actionscheduler.org/).
2. On every page load or on the next cron interval, **Action Scheduler** will run the next batch of jobs in the background process queue. See the [Cron section](#cron) for more information.
3. When a job is up next in the queue and ready to be processed it will execute the resize and/or crop task and save the image in the same location as the original image when successful and it will remove the job from the queue.
4. Once the image variation is created, it will skip the process of that variation on the next page load.
5. When you change one of the image size parameters, it will automatically try and create the new image variation on the next page load.
6. When the original image does not meet the dimension requirements of the requested image size, it will skip that image size variation and proceed to the next image size.
7. Alt text will automatically be added on the picture img element if the original image in the media library has one.

### Background Processing <a name="background-processing"></a>

The background processing library [Action Scheduler](https://actionscheduler.org/) has a built in administration screen for monitoring, debugging and manually triggering scheduled image resize jobs. The administration interface is accesible via:
```php
Tools > Scheduled Actions
```
Every resize job will be grouped by it's wordpress image id

### Cron <a name="cron"></a>
When you are using the built-in [WP-Cron](https://developer.wordpress.org/plugins/cron/), the background process queue will only process any tasks on every page load.  
If you have disabled `WP-Cron` in your setup and you are using your own cron job on your server, Action Scheduler will use the interval set in that cron job to process the next batch of jobs.

```php
define('DISABLE_WP_CRON', true);
```

If you're using [Trellis](https://roots.io/trellis/) like us ❤️, the default cron interval is set to every [15 mins](https://github.com/roots/trellis/blob/master/roles/wordpress-setup/tasks/main.yml#L48).  
You could override this to for example 1 mins with an environment variable per wordpress site like this:

In for example **trellis/group_vars/development/wordpress_sites.yml**:

```yaml
wordpress_sites:
  example.com:
    site_hosts:
      - canonical: example.test
        redirects:
          - www.example.test
    local_path: ../site # path targeting local Bedrock site directory (relative to Ansible root)
    admin_email: admin@example.test
    multisite:
      enabled: false
    ssl:
      enabled: false
      provider: self-signed
    cache:
      enabled: false
    cron:
      interval: 1
```

In **trellis/roles/wordpress-setup/tasks/main.yml**:


```yaml
- name: Setup WP system cron
  cron:
    name: "{{ item.key }} WordPress cron"
    minute: "*/{{ item.value.cron.interval | default(15) }}"
    user: "{{ web_user }}"
    job: "cd {{ www_root }}/{{ item.key }}/{{ item.value.current_path | default('current') }} && wp cron event run --due-now > /dev/null 2>&1"
    cron_file: "wordpress-{{ item.key | replace('.', '_') }}"
    state: "{{ (cron_enabled and not item.value.multisite.enabled) | ternary('present', 'absent') }}"
  with_dict: "{{ wordpress_sites }}"
 ```

Don't forget to re-provision your server after changing this value.

### Error handling

If an error occurs during the resizing process or if there's invalid syntax, ResponsivePics will display or return an error.

#### PHP
<pre>
<b>ResponsivePics errors</b>
- breakpoint xxs is neither defined nor a number
</pre>

#### REST API
```json
{
  "code": "responsive_pics_invalid",
  "message": "breakpoint xxs is neither defined nor a number",
  "data": {
    "xs": 0,
    "sm": 576,
    "md": 768,
    "lg": 992,
    "xl": 1200,
    "xxl": 1400
  }
}
```

## Features <a name="features"></a>

### Lazyloading <a name="lazyloading"></a>
When enabling the `lazyload` option in the `get_picture` or `get_image` function, this library automatically:

* adds a `lazyload` class to the `<img>` element.
* swaps the `srcset` with `data-srcset` attribute on the picture `<source>` or the `<img>` elements.

This will enable you to use a lazy loading plugin such as Lazysizes.

You can also set your own lazyload class by passing it to **ResponsivePics** library in your theme’s **functions.php**:
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
When enabling the `intrinsic` option in the `get_picture` function, this library automatically:

* adds a intrinsic class to the `<picture>` element and a `intrinsic__item` class to the picture `<img>` element.
* adds `data-aspectratio` attributes on the picture `<source>` and `<img>` elements with the calculated source image ratio.

This will enable you to pre-occupy the space needed for an image by calculating the height from the image width or the width from the height with an intrinsic plugin such as the [lazysizes aspectratio extension](https://github.com/aFarkas/lazysizes/tree/gh-pages/plugins/aspectratio).

To use the **Lazysizes aspectratio extension** in your wordpress theme, first install **lazysizes** as a node module as described in the [Lazyloading section](#lazyloading) and import the extension in your theme’s global javascript file:
```javascript
import 'lazysizes/plugins/aspectratio/ls.aspectratio.js';
```

## Issues
Please submit any issues you experience with the **ResponsivePics** library over at [Github](https://github.com/booreiland/responsive-pics/issues).

## Todo's
* Limit api usage with sizes presets to prevent abuse.
* Enable more crop functionality by switching to `$wp_editor->crop` instead of `$wp_editor->resize`.
* Add functions `get_picture_data`, `get_image_data` and `get_background_data` to retrieve available sources and sizes as data instead of html markup.
* Add **bulk delete** functionality for all resized/cropped images.
* Add support for **multiple background images** syntax.

## Maintainers
**ResponsivePics** is developed and maintained by:

[@monokai](https://github.com/monokai) (creator)  
[@twansparant](https://github.com/Twansparant) (creator)

## Copyright
Code and documentation copyright 2017-2020 by [Booreiland](https://booreiland.amsterdam).  
Code released under the [MIT License](https://github.com/booreiland/responsive-pics/blob/master/LICENSE).  
Docs released under Creative Commons.