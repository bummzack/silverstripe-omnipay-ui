<?php

namespace SilverStripe\Omnipay\UI\GridField;


use SilverStripe\Omnipay\GatewayInfo;

class PaymentItemRequest extends \GridFieldDetailForm_ItemRequest
{
    private static $allowed_actions = array(
        // Repeat the actions from GridFieldDetailForm_ItemRequest
        'edit',
        'view',
        'ItemEditForm',
        // add payment specific actions with appropriate permissions
        'capture' => 'CAPTURE_PAYMENTS',
        'CaptureForm' => 'CAPTURE_PAYMENTS',
        'refund' => 'REFUND_PAYMENTS',
        'void' => 'VOID_PAYMENTS'
    );

    public function capture($request) {
        return $this->customise(new \ArrayData(array(
            'Form' => $this->CaptureForm()
        )))->renderWith('PaymentDialogue');
    }

    public function CaptureForm()
    {
        if (!($this->record instanceof \Payment)) {
            return $this->httpError(403, 'Data-record is not a payment');
        }

        if (!$this->record->canCapture()) {
            return $this->httpError(403, 'Payment cannot be captured');
        }

        /** @var \FieldList $fields */
        $fields = \FieldList::create();


        if ($this->record->canCapture(null, true)) {
            if (GatewayInfo::captureMode($this->record->Gateway) === GatewayInfo::MULTIPLE) {
                $infoText = _t(
                    'PaymentItemRequest.MultiCaptureInfo',
                    'You can capture up to {Amount}.',
                    null,
                    array('Amount' => $this->record->getMaxCaptureAmount())
                );
            } else {
                $infoText = _t(
                    'PaymentItemRequest.SingleCaptureInfo',
                    'You can capture up to {Amount}. Further captures on this payment aren\'t possible, make sure to capture exactly the required amount!',
                    null,
                    array('Amount' => $this->record->getMaxCaptureAmount())
                );
            }

            $fields->add(\LiteralField::create('_info', $infoText));
            $fields->add(\NumericField::create('Amount', _t('PaymentItemRequest.Amount', 'Amount')));
        } else {
            $fields->add(\LiteralField::create('_info', _t(
               'PaymentItemRequest.FullCaptureInfo',
               'You\'re about to capture {Amount}. This action cannot be undone.',
               null,
               array('Amount' => $this->record->getMaxCaptureAmount())
           )));
        }

        $actions = \FieldList::create(array(
           $captureAction = \FormAction::create('doCapture', _t('PaymentItemRequest.CaptureAmount', 'Capture amount')),
           $cancelAction = \FormAction::create('doCancel', _t('PaymentItemRequest.Cancel', 'Cancel'))
        ));

        $captureAction
            ->setUseButtonTag(true)
            ->addExtraClass('ss-ui-action-constructive')
            ->setAttribute('data-icon', 'accept');

        $cancelAction
            ->setUseButtonTag(true)
            ->addExtraClass('button-cancel')
            ->setAttribute('data-icon', 'cross-circle');

        $form = \Form::create($this, 'CaptureForm', $fields, $actions);
        return $form;

    }

    public function doCapture($data, $form, $request)
    {
        \Debug::dump("YO");
        $this->getToplevelController()->redirectBack();
    }

    public function doCancel($data, $form, $request)
    {
        $this->getToplevelController()->redirectBack();
    }

}
