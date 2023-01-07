<?php

namespace Modules\Crm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Contact;
use App\Category;
use Modules\Crm\Entities\CrmContact;

class CrmDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        $contacts = Contact::where('business_id', $business_id)
                    ->Active()
                    ->get();

        $customers = $contacts->whereIn('type', ['customer', 'both']);

        $leads = $contacts->where('type', 'lead');

        $total_customers = $customers->count();

        $total_leads = $leads->count();
        $sources = Category::where('business_id', $business_id)
                                ->where('category_type', 'source')
                                ->get();
        $total_sources = $sources->count();

        $life_stages = Category::where('business_id', $business_id)
                                ->where('category_type', 'life_stage')
                                ->get();

        $total_life_stage = $life_stages->count();
        $leads_by_life_stage = $leads->groupBy('crm_life_stage');

        $contacts_count_by_source = CrmContact::getContactsCountBySourceOfGivenTyps($business_id);
        
        $leads_count_by_source = CrmContact::getContactsCountBySourceOfGivenTyps($business_id, ['lead']);

        $customers_count_by_source = CrmContact::getContactsCountBySourceOfGivenTyps($business_id, ['customer', 'both']);

        $todays_birthdays = array_merge($this->getBirthdays($customers)['todays_birthdays'], $this->getBirthdays($leads)['todays_birthdays']);

        $upcoming_birthdays = array_merge($this->getBirthdays($customers)['upcoming_birthdays'], $this->getBirthdays($leads)['upcoming_birthdays']);

        return view('crm::crm_dashboard.index')->with(compact('total_customers', 'total_leads', 'total_sources', 'total_life_stage', 'leads_by_life_stage', 'sources', 'life_stages', 'todays_birthdays', 'upcoming_birthdays', 'leads_count_by_source', 'contacts_count_by_source', 'customers_count_by_source'));
    }

    private function getBirthdays($contacts)
    {
        $todays_birthdays = [];
        $upcoming_birthdays = [];

        $today = \Carbon::now();
        $thirty_days_from_today = \Carbon::now()->addDays(30)->format('Y-m-d');
        foreach ($contacts as $contact) {
            if(empty($contact->dob)) continue;
            
            $dob = \Carbon::parse($contact->dob);
            $dob_md = $dob->format('m-d');

            $next_birthday = \Carbon::parse($today->format('Y') . '-' . $dob_md);
            if ($next_birthday->lt($today)) {
                $next_birthday->addYear();
            }

            if ($today->format('m-d') == $dob->format('m-d')) {
                $todays_birthdays[] = ['id' => $contact->id, 'name' => $contact->name];
            } else if( $next_birthday->between($today->format('Y-m-d'), $thirty_days_from_today)) {
                $upcoming_birthdays[] = ['name' => $contact->name, 'id' => $contact->id, 'dob' => $dob->format('m-d')];
            }
        }

        return [
            'todays_birthdays' => $todays_birthdays,
            'upcoming_birthdays' => $upcoming_birthdays
        ];
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('crm::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('crm::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('crm::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
