<?php

namespace Bummzack\SsOmnipayUI\Extensions;

use Bummzack\SsOmnipayUI\GridField\GridFieldCaptureAction;
use Bummzack\SsOmnipayUI\GridField\GridFieldPaymentStatusIndicator;
use Bummzack\SsOmnipayUI\GridField\GridFieldRefundAction;
use Bummzack\SsOmnipayUI\GridField\GridFieldVoidAction;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\ORM\DataExtension;

/**
 * Data extension to be used in conjunction with the Payable extension from the omnipay module.
 * Make sure to apply this extension to the same model as the Payable extension.
 */
class PayableUIExtension extends DataExtension
{

    public function updateCMSFields(FieldList $fields)
    {
        $gridConfig = GridFieldConfig_RecordEditor::create()
            ->removeComponentsByType(GridFieldAddNewButton::class)
            ->removeComponentsByType(GridFieldDeleteAction::class)
            ->removeComponentsByType(GridFieldFilterHeader::class)
            ->removeComponentsByType(GridFieldPageCount::class)
            ->addComponent(new GridFieldCaptureAction(), 'GridFieldEditButton')
            ->addComponent(new GridFieldRefundAction(), 'GridFieldEditButton')
            ->addComponent(new GridFieldVoidAction(), 'GridFieldEditButton')
            ->addComponent(new GridFieldPaymentStatusIndicator(), 'GridFieldEditButton');

        $fields->findOrMakeTab('Root.Payments', _t('PayableUIExtension.PaymentsTab', 'Payments'));

        $fields->addFieldToTab(
            'Root.Payments',
            GridField::create(
                'Payments',
                _t('SilverStripe\Omnipay\Model\Payment.PLURALNAME', 'Payments'),
                $this->owner->Payments(),
                $gridConfig
            )
        );
    }
}
