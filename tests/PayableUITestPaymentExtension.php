<?php

namespace Bummzack\SsOmnipayUI\Tests;

use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataExtension;

class PayableUITestPaymentExtension extends DataExtension implements TestOnly
{
    private static $has_one = array(
        'PayableUITest_Order' => PayableUITestOrder::class
    );
}
