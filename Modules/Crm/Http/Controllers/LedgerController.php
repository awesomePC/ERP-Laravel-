<?php

namespace Modules\Crm\Http\Controllers;

use App\Contact;
use App\Http\Controllers\Controller;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    protected $transactionUtil;
    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Shows ledger for contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $crm_contact_id = auth()->user()->crm_contact_id;
        $contact = Contact::where('business_id', $business_id)
                    ->find($crm_contact_id);

        return view('crm::ledger.index')
               ->with(compact('contact'));
    }

    public function getLedger()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        $start_date = request()->start_date;
        $end_date =  request()->end_date;

        $crm_contact_id = auth()->user()->crm_contact_id;
        $contact = Contact::where('business_id', $business_id)
                    ->find($crm_contact_id);
                    
        $ledger_details = $this->transactionUtil->getLedgerDetails($crm_contact_id, $start_date, $end_date);

        if (request()->input('action') == 'pdf') {
            $for_pdf = true;
            $html = view('contact.ledger')
                    ->with(compact('ledger_details', 'contact', 'for_pdf'))->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }

        return view('contact.ledger')
            ->with(compact('ledger_details', 'contact'));
    }
}
