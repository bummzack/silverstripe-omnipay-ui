<?php
namespace Bummzack\SsOmnipayUI\GridField;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Omnipay\Model\Payment;
use SilverStripe\Omnipay\Service\ServiceFactory;
use SilverStripe\Omnipay\Exception\Exception;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationException;
use SilverStripe\View\Requirements;

/**
 * A GridField button that can be used to void a payment
 *
 * @package SilverStripe\Omnipay\Admin\GridField
 */
class GridFieldVoidAction extends GridFieldPaymentAction implements GridField_ActionProvider
{
    /**
     * Which GridField actions are this component handling
     *
     * @param GridField $gridField
     * @return array
     */
    public function getActions($gridField)
    {
        return array('voidpayment');
    }

    /**
     *
     * @param GridField $gridField
     * @param DataObject $record
     * @param string $columnName
     * @return string|null - the HTML for the column
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if (!($record instanceof Payment)) {
            return null;
        }

        if (!$record->canVoid()) {
            return null;
        }

        Requirements::css('bummzack/silverstripe-omnipay-ui: client/dist/css/omnipay-ui-cms.css');
        Requirements::javascript('bummzack/silverstripe-omnipay-ui: client/dist/javascript/omnipay-ui-cms.js');
        Requirements::add_i18n_javascript('bummzack/silverstripe-omnipay-ui: client/dist/javascript/lang');

        /** @var GridField_FormAction $field */
        $field = GridField_FormAction::create(
            $gridField,
            'VoidPayment' . $record->ID,
            false,
            'voidpayment',
            array('RecordID' => $record->ID)
        )
            ->addExtraClass('gridfield-button-void payment-dialog-button')
            ->setAttribute('title', _t('GridFieldVoidAction.Title', 'Void Payment'))
            ->setAttribute('data-icon', 'button-void')
            ->setAttribute('data-dialog', json_encode(array(
                'maxAmount' => $record->dbObject('Money')->Nice(),
                'hasAmountField' => false,
                'infoTextKey' => 'VoidInfo',
                'buttonTextKey' => 'VoidPayment'
            )))
            ->setDescription(_t('GridFieldVoidAction.Description', 'Void an authorized payment'));

        return $field->Field();
    }

    /**
     * Handle the actions and apply any changes to the GridField
     *
     * @param GridField $gridField
     * @param string $actionName
     * @param mixed $arguments
     * @param array $data - form data
     * @return void
     * @throws ValidationException when there was an error
     * @throws \SilverStripe\Omnipay\Exception\InvalidConfigurationException
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'voidpayment') {
            $item = $gridField->getList()->byID($arguments['RecordID']);
            if (!($item instanceof Payment)) {
                return;
            }

            /** @var ServiceFactory $factory */
            $factory = ServiceFactory::create();
            $voidService = $factory->getService($item, ServiceFactory::INTENT_VOID);

            try {
                $serviceResponse = $voidService->initiate();
            } catch (Exception $ex) {
                throw new ValidationException($ex->getMessage(), 0);
            }

            if ($serviceResponse->isError()) {
                throw new ValidationException(
                    _t('GridFieldVoidAction.VoidError', 'Unable to void payment. An error occurred.'),
                    0
                );
            }
        }
    }
}
