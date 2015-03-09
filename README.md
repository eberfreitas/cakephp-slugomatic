# Slugomatic Plugin

[![Build Status](https://travis-ci.org/eberfreitas/cakephp-slugomatic.svg?branch=master)](https://travis-ci.org/eberfreitas/cakephp-slugomatic) [![Coverage Status](https://img.shields.io/coveralls/eberfreitas/cakephp-slugomatic.svg)](https://coveralls.io/r/eberfreitas/cakephp-slugomatic?branch=master)

Slugomatic is a CakePHP plugin that automatically generate slugs based on your
database fields. That way you can easily generate user and SEO friendly URLs for
your resources. You can read more about Semantic URLs on
[Wikipedia](http://en.wikipedia.org/wiki/Semantic_URL).

**Example**

If you have a record on your database with the title "How to grow a beard", the
plugin will generate a slug like "how-to-grow-a-beard". That way you can create
links like this:

**http://example.com/post/1234/how-to-grow-a-beard**

## Requirements

* CakePHP 2.x (tested on 2.4 and 2.5 but should work on every 2.x release)
* PHP 5.3 or later (should work on 5.2 but it is not tested)

## Installation

**Using [Composer](http://getcomposer.org/)**

Add the plugin to your project's `composer.json` - something like this:

```javascript
{
    "require": {
        "eberfreitas/cakephp-slugomatic": "dev-master"
    }
}
```

Because this plugin has the type `cakephp-plugin` set in it's own
`composer.json`, composer knows to install it inside your `/Plugins` directory,
rather than in the usual vendors file. It is recommended that you add
`/Plugins/Slugomatic` to your .gitignore file.
Why? [read this](http://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).

**Manual**

* Download this: http://github.com/eberfreitas/cakephp-slugomatic/zipball/master
* Unzip that download
* Copy the resulting folder to app/Plugins
* Rename the folder you just copied to `Slugomatic`

**GIT Submodule**

In your app directory type:

```bash
git submodule add git://github.com/eberfreitas/cakephp-slugomatic.git plugins/Slugomatic
git submodule init
git submodule update
```

**GIT Clone**

In your plugin directory type:

```bash
git clone git://github.com/eberfreitas/cakephp-slugomatic.git Slugomatic
```

## Usage

First of all you need to enable the plugin on your application. On your
`app/Config/bootstrap.php` add the following line:

```php
CakePlugin::load('Slugomatic');
```

You can read more about enabling plugins and the different ways of doing it on
[Cake's Book](http://book.cakephp.org/2.0/en/plugins.html#installing-a-plugin).

After that, go to the model where you want to use the plugin and attach the
behavior like this:

```php
public $actsAs = array(
    'Slugomatic.Slugomatic'
);
```

The plugin will assume that your have a field called `title`. The slug will be
created using the data from that field. If you have a different field name, just
configure the behavior like this:

```php
public $actsAs = array(
    'Slugomatic.Slugomatic' => array(
        'fields' => 'name'
    )
);
```

The plugin will also assume that you have a `slug` field where the generated
slug will be stored. Take a look at the options below if you need to change that.

If by any means you have two records with the same title, Slugomatic will
identify those records and generate indexed slugs avoiding duplication, like
this:

* how-to-grow-a-beard
* how-to-grow-a-beard-1
* how-to-grow-a-beard-2
* how-to-grow-a-beard-x

### Options

When configuring the behavior, you have the following options:

```php
public $actsAs = array(
    'Slugomatic.Slugomatic' => array(
        'fields' => 'title',
        'scope' => false,
        'conditions' => false,
        'slugfield' => 'slug',
        'separator' => '-',
        'overwrite' => false,
        'length' => 256,
        'lower' => true
    )
);
```

* `fields`: Specify the source fields for the slug. Defaults to `title`. If you
  need, you can specify more than one field like this:

  ```php
  array('title', 'product_code');
  ```

  That way, if you have a record with the title "Strawberry Cake" and a
  product_code like "CK 073", the slug will be something like
  "strawberry-cake-ck-073".
* `slugfield`: The field on your database that will store the generated slug.
  Defaults to `slug`.
* `overwrite`: Only applied when updating a record. If `overwrite` is `true`,
  then when you update the record with a different value, the slug will also be
  updated/overwrited. Defaults to `false`.
  **Important!** Ideally, this value should always be `false`. You don't want to
  break links everywhere with a modified slug from a previously defined
  resource, so **use this with caution**.
* `length`: Defines the length of the slug. Defaults to 256.
* `lower`: When `true`, transforms the slug text to lowercase. If `false`, the
  behavior will preserve the text the way it was defined.
* `separator`: The character used to separate words. Defaults to '-'.

#### Additional options for deduplication

Like stated before, Slugomatic will identify duplicated slugs and generate a
properly indexed new slug to avoid that. The plugin also provides two different
options that enables you to control the way it identifies duplicated slugs:

* `conditions`: In this option you can define conditions to the query that will
  identify duplicates just like you define conditions to your regular queries.
  Example:

  ```php
  array(
      'conditions' => array('product_type' => 'box')
  );
  ```

  That way, Slugomatic will only find duplicates if `product_type` is `box`.
* `scope`: This option is very similar to `conditions` but it uses dynamic
  values. The value will always be the one present on the data being saved. That
  way you just need to define the field being used for the condition, like this:

  ```php
  array(
      'scope' => array('deleted')
  );
  ```

  So, if you are saving a record with the following data:

  ```php
  $data = array(
      'title' => 'How to grow a beard',
      'text' => 'Lorem ipsum...',
      'deleted' => 0
  )
  ```

  Slugomatic will only look for duplicates where the field `deleted` is also `0`.

# Credits & thanks

This plugin is heavily based on
[Mariano's SluggableBehavior from the Syrup package](https://github.com/mariano/syrup/blob/master/models/behaviors/sluggable.php).
A big thanks to [Friends of Cake](http://friendsofcake.com/) for providing the
awesome boilerplate for
[travis-ci integration](https://github.com/FriendsOfCake/travis). And finally,
thanks to [Jose Diaz-Gonzalez](http://josediazgonzalez.com/) and his
[CakeAdvent series](http://josediazgonzalez.com/2013/12/01/testing-your-cakephp-plugins-with-travis/)
which helped me to create this plugin properly.
