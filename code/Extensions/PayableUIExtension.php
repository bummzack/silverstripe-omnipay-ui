<?php

use SilverStripe\Omnipay\UI\GridField\GridFieldCaptureAction;
use SilverStripe\Omnipay\UI\GridField\GridFieldRefundAction;
use SilverStripe\Omnipay\UI\GridField\GridFieldVoidAction;
use SilverStripe\Omnipay\UI\GridField\GridFieldPaymentStatusIndicator;

/**
 * Data extension to be used in conjunction with the Payable extension from the omnipay module.
 * Make sure to apply this extension to the same model as the Payable extension.
 */
class PayableUIExtension extends DataExtension
{

    public function updateCMSFields(FieldList $fields)
    {
        $gridConfig = GridFieldConfig_RecordEditor::create()
            ->removeComponentsByType('GridFieldAddNewButton')
            ->removeComponentsByType('GridFieldDeleteAction')
            ->removeComponentsByType('GridFieldFilterHeader')
            ->removeComponentsByType('GridFieldPageCount')
            ->addComponent(new GridFieldCaptureAction(), 'GridFieldEditButton')
            ->addComponent(new GridFieldRefundAction(), 'GridFieldEditButton')
            ->addComponent(new GridFieldVoidAction(), 'GridFieldEditButton')
            ->addComponent(new GridFieldPaymentStatusIndicator(), 'GridFieldEditButton');

        $fields->addFieldToTab('Root.Payments',
            GridField::create('Payments', _t('Payment.PLURALNAME', 'Payments'), $this->owner->Payments(), $gridConfig)
        );
    }
}
