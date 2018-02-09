<?php

namespace Bummzack\SsOmnipayUI\Tests\GridField;

use Bummzack\SsOmnipayUI\GridField\GridFieldCaptureAction;
use Bummzack\SsOmnipayUI\GridField\GridFieldRefundAction;
use Bummzack\SsOmnipayUI\GridField\GridFieldVoidAction;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\CSSContentParser;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Omnipay\GatewayInfo;
use SilverStripe\Omnipay\Model\Payment;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;

/**
 * Test payment actions in GridFields
 */
class GridFieldPaymentActionsTest extends SapphireTest
{

    /** @var ArrayList */
    protected $list;

    /** @var GridField */
    protected $gridField;

    /** @var Form */
    protected $form;

    /** @var string */
    protected static $fixture_file = 'GridFieldTestPayments.yml';

    public function setUp()
    {
        parent::setUp();
        $this->list = new DataList(Payment::class);
        $config = GridFieldConfig::create()
            ->addComponent(new GridFieldCaptureAction())
            ->addComponent(new GridFieldRefundAction())
            ->addComponent(new GridFieldVoidAction());
        $this->gridField = new GridField('testfield', 'testfield', $this->list, $config);


        $ctrl = new Controller();
        $ctrl::config()->set('url_segment', 'mock');
        $this->form = new Form(
            $ctrl,
            'mockform',
            new FieldList(array($this->gridField)),
            new FieldList()
        );
    }

    public function testButtons()
    {
        $content = new CSSContentParser($this->gridField->FieldHolder());

        // There should be a total of 5 items in the GridField (see fixture file)
        $this->assertEquals(5, count($content->getBySelector('tr.ss-gridfield-item')));

        // Two of the payments should have capture buttons
        $this->assertEquals(2, count($content->getBySelector('.gridfield-button-capture')));

        // Two payments should have a refund button
        $this->assertEquals(2, count($content->getBySelector('.gridfield-button-refund')));

        // Two payments should have a void button
        $this->assertEquals(2, count($content->getBySelector('.gridfield-button-void')));

        // Disallow actions for Manual Gateway
        Config::modify()->merge(GatewayInfo::class, 'Manual', array(
           'can_capture' => false,
           'can_refund' => false,
           'can_void' => false
        ));

        $content = new CSSContentParser($this->gridField->FieldHolder());

        // Now only one payment should have a capture button
        $this->assertEquals(1, count($content->getBySelector('.gridfield-button-capture')));

        // Now only one payment should have a refund button
        $this->assertEquals(1, count($content->getBySelector('.gridfield-button-refund')));

        // Now only one payment should have a void button
        $this->assertEquals(1, count($content->getBySelector('.gridfield-button-void')));

        // Update the authorized PaymentExpress_PxPay payment to Void
        $payment = $this->objFromFixture(Payment::class, 'payment3');
        $payment->Status = 'Void';
        $payment->write();

        $content = new CSSContentParser($this->gridField->FieldHolder());
        // Now no payment should have a capture button
        $this->assertEquals(0, count($content->getBySelector('.gridfield-button-capture')));

        // One payment should still have a refund button
        $this->assertEquals(1, count($content->getBySelector('.gridfield-button-refund')));

        // Now no payment should have a void button
        $this->assertEquals(0, count($content->getBySelector('.gridfield-button-void')));
    }
}
