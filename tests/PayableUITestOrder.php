<?php

namespace Bummzack\SsOmnipayUI\Tests;

use Bummzack\SsOmnipayUI\Extensions\PayableUIExtension;
use SilverStripe\Dev\TestOnly;
use SilverStripe\Omnipay\Extensions\Payable;
use SilverStripe\ORM\DataObject;

class PayableUITestOrder extends DataObject implements TestOnly
{
    private static $extensions = array(
        Payable::class,
        PayableUIExtension::class
    );
}
