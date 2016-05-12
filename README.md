# o80-i18n

**o80-i18n** is a small PHP tool to manage i18n (internationalization). By default it uses `json` format for language files, you can define your own provider if you prefer another format.
See below how the usage is simple.

[![Build Status](https://travis-ci.org/olivierperez/o80-i18n.svg)](https://travis-ci.org/olivierperez/o80-i18n)
[![Latest Unstable Version](https://poser.pugx.org/o80/i18n/v/unstable.svg)](https://packagist.org/packages/o80/i18n)
[![License](https://poser.pugx.org/o80/i18n/license.svg)](https://tldrlegal.com/l/apache2)

# How-to

## Installation

With [Composer](http://getcomposer.org/), you simply need to require [`o80/i18n`](https://packagist.org/packages/o80/i18n):

```json
{
...
    "require": {
        "o80/i18n": "~0.2"
    }
...
}
```

## Usage

### Dictionaries

For instance, put your language files in 'lang' directory :

* `langs`
    * `en.json`
    * `en_US.json`
    * `fr.json`

Example of language file `en.json` :

```json
{
    "Generic" : {
        "Welcome": "Welcome",
        "Hello": "Hello %s!",
        "submit": "submit"
    }
}
```

Example of language file `fr.json` :

```json
{
    "Generic" : {
        "Welcome": "Bienvenue",
        "Hello": "Bonjour %s !",
        "submit": "valider"
    }
}
```

### Configure the i18n instance

#### Way 1 - Singleton

```php
$i18n = I18N::instance();
$i18n->setPath(__DIR__ . '/langs');
$i18n->setDefaultLang('en');
```

#### Way 2 - New instance

```php
$i18n = new I18N();
$i18n->setPath(__DIR__ . '/langs');
$i18n->setDefaultLang('en');
```

### Usage

#### Way 1 - Singleton

```php
<h1><?php echo __('Generic', 'Welcome'); ?></h1>
<!-- Result : <h1>Welcome</h1> -->
<h1><?php echo __('Generic', 'NotExistingText'); ?></h1>
<!-- Result : <h1>[missing key: Generic.NotExistingText]</h1> -->
<span><?php echo __f('Generic', 'Hello', 'Olivier'); ?></span>
<!-- Result : <span>Hello Olivier!</span> -->
<span><?php echo I18N::instance()->getLoadedLang(); ?></span>
<!-- Result : <span>en</span> -->
```

#### Way 2 - With the instance

```php
<h1><?php echo $i18n->get('Generic', 'Welcome'); ?></h1>
<!-- Result : <h1>Welcome</h1> -->
<h1><?php echo $i18n->get('Generic', 'NotExistingText'); ?></h1>
<!-- Result : <h1>[missing key: Generic.NotExistingText]</h1> -->
<span><?php echo $i18n->format('Generic', 'Hello', array('Olivier')); ?></span>
<!-- Result : <span>Hello Olivier!</span> -->
<span><?php echo $i18n->getLoadedLang(); ?></span>
<!-- Result : <span>en</span> -->
```

### How to set the language to use

The system look into serverals variables to find the language file to load. Look below to understand it.

* Use `$_GET['lang']` if it's defined and if it matches to a language file;
* Use `$_SESSION['lang']` if it's defined and if it matches to a language file;
* Use `$_SERVER['HTTP_ACCEPT_LANGUAGE']` if it's defined and if it matches to a language file;
    * It checks for all languages found in this variable
* Use the `$defaultLang` you defined in the I18N instance
* If no file found, don't do load anything

# Contribution

Just fork the project, make your changes, ask for pull request ;-).
