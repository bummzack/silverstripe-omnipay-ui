<?php

namespace Bummzack\SsOmnipayUI\Admin;

use Bummzack\SsOmnipayUI\GridField\GridFieldCaptureAction;
use Bummzack\SsOmnipayUI\GridField\GridFieldPaymentStatusIndicator;
use Bummzack\SsOmnipayUI\GridField\GridFieldRefundAction;
use Bummzack\SsOmnipayUI\GridField\GridFieldVoidAction;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Omnipay\Model\Payment;

/**
 * Model admin administration of payments.
 *
 * @package payment
 */
class PaymentAdmin extends ModelAdmin
{

    private static $menu_title = 'Payments';
    private static $url_segment = 'payments';
    private static $menu_icon = 'bummzack/silverstripe-omnipay-ui: client/dist/images/payment-admin.png';
    private static $menu_priority = 1;

    public $showImportForm = false;

    private static $managed_models = array(
        Payment::class
    );

    public function alternateAccessCheck()
    {
        return !$this->config()->hidden;
    }

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);
        if ($this->modelClass == Payment::class) {
            /** @var GridFieldConfig $cfg */
            if ($cfg = $form->Fields()->fieldByName($this->sanitiseClassName(Payment::class))->getConfig()) {
                $cfg->addComponent(new GridFieldCaptureAction(), 'GridFieldEditButton')
                    ->addComponent(new GridFieldRefundAction(), 'GridFieldEditButton')
                    ->addComponent(new GridFieldVoidAction(), 'GridFieldEditButton')
                    ->addComponent(new GridFieldPaymentStatusIndicator(), 'GridFieldEditButton');
            }
        }

        return $form;
    }
}
