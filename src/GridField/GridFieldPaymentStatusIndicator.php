<?php

namespace Bummzack\SsOmnipayUI\GridField;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_URLHandler;
use SilverStripe\Omnipay\Model\Payment;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;

class GridFieldPaymentStatusIndicator extends GridFieldPaymentAction implements GridField_URLHandler
{
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

        Requirements::css('bummzack/silverstripe-omnipay-ui: client/dist/css/omnipay-ui-cms.css');
        Requirements::javascript('bummzack/silverstripe-omnipay-ui: client/dist/javascript/omnipay-ui-cms.js');

        if (preg_match('/Pending(Capture|Void|Refund)/', $record->Status)) {
            return SSViewer::execute_template('PaymentPendingIndicator', ArrayData::create(array(
                'StatusLink' => Controller::join_links($gridField->Link('checkPaymentPending')),
                'PaymentID' => $record->ID,
                'Timeout' => 2000
            )));
        }

        return null;
    }

    public function getURLHandlers($gridField)
    {
        return array(
            'checkPaymentPending' => 'handleCheckPaymentPending'
        );
    }

    /**
     * Accepts a list of ids in form of comma separated string via GET parameter. If any of these payments is no longer
     * pending, this method returns true, false otherwise.
     * @param $gridField
     * @param HTTPRequest|null $request
     * @return bool
     */
    public function handleCheckPaymentPending($gridField, HTTPRequest $request = null)
    {
        if (!$request) {
            return false;
        }

        $ids = preg_split('/[^\d]+/', $request->getVar('ids'));
        return Payment::get()
            ->filter('ID', $ids)
            ->exclude('Status', array('PendingVoid', 'PendingCapture', 'PendingRefund'))
            ->count() > 0;
    }
}
