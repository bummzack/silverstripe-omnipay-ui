<?php

use SilverStripe\Omnipay\UI\GridField\GridFieldCaptureAction;
use SilverStripe\Omnipay\UI\GridField\GridFieldRefundAction;
use SilverStripe\Omnipay\UI\GridField\GridFieldVoidAction;

/**
 */
class PayableUIExtension extends DataExtension
{
    public function updateCMSFields(FieldList $fields)
    {
        /** @var GridField $gf */
        if($gf = $fields->fieldByName('Root.Payments.Payments')){
            /** @var GridFieldConfig $gc */
            if($gc = $gf->getConfig()){
                $gc
                    ->addComponent(new GridFieldCaptureAction(), 'GridFieldEditButton')
                    ->addComponent(new GridFieldRefundAction(), 'GridFieldEditButton')
                    ->addComponent(new GridFieldVoidAction(), 'GridFieldEditButton');

            }
        }
    }

}
