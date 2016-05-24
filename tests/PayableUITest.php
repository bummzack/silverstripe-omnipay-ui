<?php

/**
 * Test the Payable extension
 */
class PayableUITest extends SapphireTest
{
    protected $extraDataObjects = array('PayableUITest_Order');

    public function setUpOnce()
    {
        Payment::add_extension('PayableUITest_PaymentExtension');

        parent::setUpOnce();
    }

    public function tearDownOnce()
    {
        parent::tearDownOnce();

        Payment::remove_extension('PayableUITest_PaymentExtension');
    }

    /**
     * Test the CMS fields added via extension
     */
    public function testCMSFields()
    {
        // Add the payable UI extension to the Test_Order (which us part of the tests from the omnopay module)
        $order = new PayableUITest_Order();
        $fields = $order->getCMSFields();

        $this->assertTrue($fields->hasTabSet());

        /** @var GridField $gridField */
        $gridField = $fields->fieldByName('Root.Payments.Payments');

        $this->assertInstanceOf('GridField', $gridField);

        // Check the actions/buttons that should be in place
        $this->assertNotNull($gridField->getConfig()->getComponentByType('GridFieldEditButton'));
        $this->assertNotNull($gridField->getConfig()->getComponentByType(
            'SilverStripe\Omnipay\UI\GridField\GridFieldCaptureAction'
        ));
        $this->assertNotNull($gridField->getConfig()->getComponentByType(
            'SilverStripe\Omnipay\UI\GridField\GridFieldRefundAction'
        ));
        $this->assertNotNull($gridField->getConfig()->getComponentByType(
            'SilverStripe\Omnipay\UI\GridField\GridFieldVoidAction'
        ));

        // check the actions buttons that should be removed
        $this->assertNull($gridField->getConfig()->getComponentByType('GridFieldAddNewButton'));
        $this->assertNull($gridField->getConfig()->getComponentByType('GridFieldDeleteAction'));
        $this->assertNull($gridField->getConfig()->getComponentByType('GridFieldFilterHeader'));
        $this->assertNull($gridField->getConfig()->getComponentByType('GridFieldPageCount'));
    }

}

class PayableUITest_Order extends DataObject implements TestOnly
{
    private static $extensions = array(
        'Payable',
        'PayableUIExtension'
    );
}

class PayableUITest_PaymentExtension extends DataExtension implements TestOnly
{
    private static $has_one = array(
        'PayableUITest_Order' => 'PayableUITest_Order'
    );
}
