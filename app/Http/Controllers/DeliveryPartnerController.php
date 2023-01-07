<?php

namespace App\Http\Controllers;

use App\DeliveryPartner;
use Illuminate\Http\Request;

class DeliveryPartnerController extends Controller
{
    // show delivery partners
    public function index()
    {
        $delivery_partners = DeliveryPartner::all();
        return view('delivery_partner.partners', compact('delivery_partners'));
    }

    // create delivery partner
    public function create(Request $request)
    {
        // delivery charges
        $delivery_charges = json_encode([
            'charge_0' => $request->charge_0,
            'charge_1' => $request->charge_1,
        ]);

        // create data to db
        DeliveryPartner::create([
            'name' => $request->name,
            'contact' => $request->contact,
            'address' => $request->address,
            'delivery_charges' => $delivery_charges,
            'customer_portal_link' => $request->customer_portal,
        ]);

        // return 
        return 1;
    }
}
