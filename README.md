Reflection
==========

Reflection is a [YAPB]-based theme for WordPress photoblogs. It is designed to
be clean, simple and easy to present your images.

Note that, given the age of this project, this theme has been poorly maintained
over the years but (I think) looks quite acceptable even today. General goals
are:

- Some form of mobile compatibility via bootstrap or some other framework;
- Port JS over to jQuery;
- Possibly migrate away from YAPB given the improvement in WordPress' handling
  of themes.

Installation
------------

Before you begin, make sure that:

- The YAPB plugin is installed and works properly. Reflection will not work
  without it!
- Go into the YAPB control panel inside the WordPress admin console and make
  sure that _all_ of the automatic image insertion options at the bottom are
  turned off. **Reflection will not display properly unless this is done**.
- All of your posts contain a YAPB image. If this is not the case you will get
  strange errors and things will break.

Then,

- Unzip the theme (or, better, clone this repository) and upload it to your
  WordPress themes directory.
- Select the theme in the Presentation tab of the admin console.

Fixing problems
---------------

Given I have very little time for implementation, I can sometimes be extremely
slow to respond to problems by e-mail. Also, it's not much help for those that
have problems which have already been solved. So please raise an issue on GitHub
if you're having problems, or better, fork this repository and fix it via a pull
request!

Customization
-------------

Reflection comes with some basic administration options which you can control
through the WordPress control panel. The panel is available under the
_Appearance_ tab. This section briefly outlines all of the settings which are
available.

###General Settings

- **Copyright holder/year:** to change the copyright notice at the bottom, enter
  your name in this field. The results appear at the bottom of every Reflection
  page.
- **Image width:** Some people commented that they'd like portrait images displayed
  using a larger width, so you can now change that here. Don't enter more than
  800px here otherwise the display will break on the front page!
- **Show Random page:** If you want a link on the navigation bar to random photos,
  then check this box.

### Mosaic configuration

- Taxonomy display: If you want to browse photos by tags through the archive,
  then you need to select the _tags_ radio button here.
- Mosaic image size: This controls the size of images found in the archive.
- Show mosaic tooltips: By default, when you hover over each image, it displays
  the name and date of the post. To turn this off, deselect the tick box.
- Post order: This allows you to order posts either by ascending or descending date.

Handling pages
--------------

In Reflection 1.1, any pages you create which are published will automatically
appear in the navigation menu at the top. Since WordPress 2.5ish, you can change
the order in which the pages are displayed, and Reflection observes this
order. So you can now customise the navigation menu any which way you want.

Reflection also includes a template for your post archives, which is a nice way
of showing your visitors your archive of photos and a bit more interesting than
just a list of the posts. Here's how to create it:

- In your administration console, create a new page.
- On the right hand side, select the <em>Archives</em> template from the
  _Template_ attribute.
- Save and check to make sure it works.

Any content you add to the text body will, in this case, be ignored for design
purposes, but this may change in the future. Depending on the speed and
processing power of your webserver, the first time you load the mosaic it may
take a while to generate the thumbnails.

Changes
-------

**v1.1.2**

- Minor release to confirm I'm still alive and update to GitHub address.
- Fix some browser CSS.
- Add photoshop template for logo.

**v1.1.1**

- Fixed bug where image dimensions less than the desired width would cause a
  misdisplay of image.
- Added `json_encode` function for those that don't have PHP 5.2 or greater.

**v1.1**

- New admin interface to allow for easy customisation of basic features -
  copyright info, image sizes, and more.
- New Mosaic page for browsing pictures by date and tag (finally!)
- Fixed top navigation menu so hard-coding inside the template file is not
  necessary.
- Upgraded to mootools 1.1 for better/faster JavaScript performance.
- Simplified CSS and fixed many animation bugs.
- Added a theme image.
- Many other bug fixes, including the extremely irritating PHP4 bug fix!

[YAPB]:http://wordpress.org/plugins/yet-another-photoblog/
