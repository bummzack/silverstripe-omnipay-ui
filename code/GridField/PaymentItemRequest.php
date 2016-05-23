<?php

namespace SilverStripe\Omnipay\UI\GridField;

class PaymentItemRequest extends \GridFieldDetailForm_ItemRequest
{
    private static $allowed_actions = array(
        // Repeat the actions from GridFieldDetailForm_ItemRequest
        'edit',
        'view',
        'ItemEditForm',
        // add payment specific actions
        'paymentstatus'
    );

    public function paymentstatus(\SS_HTTPRequest $request)
    {
        if (!($this->record instanceof \Payment)) {
            return $this->httpError(403, 'Data-record is not a payment');
        }

        return $this->record->Status;
    }

}
