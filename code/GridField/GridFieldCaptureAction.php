<?php
namespace SilverStripe\Omnipay\UI\GridField;

use SilverStripe\Omnipay\GatewayInfo;
use SilverStripe\Omnipay\Service\ServiceFactory;
use SilverStripe\Omnipay\Exception\Exception;

/**
 * A GridField button that can be used to capture an authorized payment
 *
 * @package SilverStripe\Omnipay\Admin\GridField
 */
class GridFieldCaptureAction extends GridFieldPaymentAction implements \GridField_ActionProvider
{
    /**
     * Which GridField actions are this component handling
     *
     * @param \GridField $gridField
     * @return array
     */
    public function getActions($gridField)
    {
        return array('capturepayment');
    }

    /**
     *
     * @param \GridField $gridField
     * @param \DataObject $record
     * @param string $columnName
     * @return string|null - the HTML for the column
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if (!($record instanceof \Payment)) {
            return null;
        }

        if (!$record->canCapture()) {
            return null;
        }

        \Requirements::css('omnipay-ui/css/omnipay-ui-cms.css');
        \Requirements::javascript('omnipay-ui/javascript/omnipay-ui-cms.js');
        \Requirements::add_i18n_javascript('omnipay-ui/javascript/lang');

        $infoText = '';
        switch (GatewayInfo::captureMode($record->Gateway)) {
            case GatewayInfo::MULTIPLE:
                $infoText = 'MultiCaptureInfo';
                break;
            case GatewayInfo::PARTIAL:
                $infoText = 'SingleCaptureInfo';
                break;
            case GatewayInfo::FULL:
                $infoText = 'FullCaptureInfo';
                break;
        }

        /** @var \Money $money */
        $money = $record->dbObject('Money');
        $money->setAmount($record->getMaxCaptureAmount());

        /** @var \GridField_FormAction $field */
        $field = \GridField_FormAction::create(
            $gridField,
            'CapturePayment' . $record->ID,
            false,
            'capturepayment',
            array('RecordID' => $record->ID)
        )
            ->addExtraClass('gridfield-button-capture payment-dialog-button')
            ->setAttribute('title', _t('GridFieldCaptureAction.Title', 'Capture Payment'))
            ->setAttribute('data-icon', 'button-capture')
            ->setAttribute('data-dialog', json_encode(array(
                'maxAmount' => $money->Nice(),
                'hasAmountField' => $record->canCapture(null, true),
                'infoTextKey' => $infoText,
                'buttonTextKey' => 'CaptureAmount'
            )))
            ->setDescription(_t('GridFieldCaptureAction.Description', 'Capture authorized Payment'));

        return $field->Field();
    }

    /**
     * Handle the actions and apply any changes to the GridField
     *
     * @param \GridField $gridField
     * @param string $actionName
     * @param mixed $arguments
     * @param array $data - form data
     * @return void
     * @throws \ValidationException when there was an error
     */
    public function handleAction(\GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'capturepayment') {
            $item = $gridField->getList()->byID($arguments['RecordID']);
            if (!($item instanceof \Payment)) {
                return;
            }

            $serviceData = array_intersect_key($data, array('amount' => null));

            /** @var ServiceFactory $factory */
            $factory = ServiceFactory::create();
            $captureService = $factory->getService($item, ServiceFactory::INTENT_CAPTURE);

            try {
                $serviceResponse = $captureService->initiate($serviceData);
            } catch (Exception $ex) {
                throw new \ValidationException($ex->getMessage(), 0);
            }

            if ($serviceResponse->isError()) {
                throw new \ValidationException(
                    _t('GridFieldCaptureAction.CaptureError', 'Unable to capture payment. An error occurred.'), 0);
            }
        }
    }
}
