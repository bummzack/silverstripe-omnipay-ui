# silverstripe-omnipay-ui

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bummzack/silverstripe-omnipay-ui/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bummzack/silverstripe-omnipay-ui/?branch=master)
[![Code Coverage](https://codecov.io/gh/bummzack/silverstripe-omnipay-ui/branch/master/graph/badge.svg)](https://codecov.io/gh/bummzack/silverstripe-omnipay-ui)
[![Build Status](https://travis-ci.org/bummzack/silverstripe-omnipay-ui.svg?branch=master)](https://travis-ci.org/bummzack/silverstripe-omnipay-ui)
[![Latest Stable Version](https://poser.pugx.org/bummzack/silverstripe-omnipay-ui/v/stable)](https://packagist.org/packages/bummzack/silverstripe-omnipay-ui)

UI components for SilverStripe Omnipay Module.

This module contains the "Payments" ModelAdmin that was originally part of the omnipay module. It also adds buttons to payment GridFields that allow you to capture, refund or void payments.

## Version

1.0 (in development) for SilverStripe 4

For a SilverStripe 3 compatible version, please use 0.1.x

## Requirements

 * [silverstripe-omnipay](https://github.com/silverstripe/silverstripe-omnipay) 3.0+ including its dependencies.


## Installation

[Composer](http://doc.silverstripe.org/framework/en/installation/composer) is currently the only supported way to set up this module:

```
composer require bummzack/silverstripe-omnipay-ui ^1@dev
```

### Adding the PayableUIExtension (optional)

If you have a `Payable` DataObject, eg. you added the `Payable` extension from the Omnipay module to some of your classes, you might also want to add the `PayableUIExtension`, which adds a GridField component to manipulate Payments.

So if you're running [SilverShop](https://packagist.org/packages/silvershop/core), you should also add the following to your `config.yml`

```yaml
SilverShop\Model\Order:
  extensions:
    - Bummzack\SsOmnipayUI\Extensions\PayableUIExtension
```

## Payment administration

Read the [Payment administration guide](docs/en/userdoc.md)

## Attributions

 - Icons used in the CMS are part of the [Silk Icon set 1.3](http://www.famfamfam.com/lab/icons/silk/). [Creative Commons Attribution 2.5 License](http://creativecommons.org/licenses/by/2.5/)
