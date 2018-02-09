<?php

namespace Bummzack\SsOmnipayUI\Tests;

use Bummzack\SsOmnipayUI\GridField\GridFieldCaptureAction;
use Bummzack\SsOmnipayUI\GridField\GridFieldRefundAction;
use Bummzack\SsOmnipayUI\GridField\GridFieldVoidAction;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Omnipay\Model\Payment;

/**
 * Test the Payable extension
 */
class PayableUITest extends SapphireTest
{
    protected $extraDataObjects = [PayableUITestOrder::class];

    public function setUp()
    {
        parent::setUp();
        Config::modify()->set(Payment::class, 'extensions', PayableUITestPaymentExtension::class);
    }

    /**
     * Test the CMS fields added via extension
     */
    public function testCMSFields()
    {
        // Add the payable UI extension to the Test_Order (which us part of the tests from the omnipay module)
        $order = new PayableUITestOrder();
        $fields = $order->getCMSFields();

        $this->assertTrue($fields->hasTabSet());

        /** @var GridField $gridField */
        $gridField = $fields->fieldByName('Root.Payments.Payments');

        $this->assertInstanceOf(GridField::class, $gridField);

        // Check the actions/buttons that should be in place
        $this->assertNotNull($gridField->getConfig()->getComponentByType(GridFieldEditButton::class));
        $this->assertNotNull($gridField->getConfig()->getComponentByType(
            GridFieldCaptureAction::class
        ));
        $this->assertNotNull($gridField->getConfig()->getComponentByType(
            GridFieldRefundAction::class
        ));
        $this->assertNotNull($gridField->getConfig()->getComponentByType(
            GridFieldVoidAction::class
        ));

        // check the actions buttons that should be removed
        $this->assertNull($gridField->getConfig()->getComponentByType(GridFieldAddNewButton::class));
        $this->assertNull($gridField->getConfig()->getComponentByType(GridFieldDeleteAction::class));
        $this->assertNull($gridField->getConfig()->getComponentByType(GridFieldFilterHeader::class));
        $this->assertNull($gridField->getConfig()->getComponentByType(GridFieldPageCount::class));
    }
}
