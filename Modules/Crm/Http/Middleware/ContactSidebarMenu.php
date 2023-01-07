<?php

namespace Modules\Crm\Http\Middleware;

use App\Contact;
use Closure;
use Illuminate\Http\Request;
use Menu;
use App\Business;

class ContactSidebarMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->ajax()) {
            return $next($request);
        }

        Menu::create('contact-sidebar-menu', function ($menu) {
            //retrieve contact type
            $contact = Contact::where('business_id', auth()->user()->business_id)
                            ->findOrFail(auth()->user()->crm_contact_id);

            $crm_settings = Business::where('id', auth()->user()->business_id)
                                ->value('crm_settings');
            $crm_settings = !empty($crm_settings) ? json_decode($crm_settings, true) : [];

            $menu->url(action('\Modules\Crm\Http\Controllers\DashboardController@index'), __('home.home'), ['icon' => 'fa fas fa-tachometer-alt', 'active' => request()->segment(1) == 'contact' && request()->segment(2) == 'contact-dashboard'])->order(1);
            
            if (in_array($contact->type, ['supplier', 'both'])) {
                $menu->url(action('\Modules\Crm\Http\Controllers\PurchaseController@getPurchaseList'), __('purchase.list_purchase'), ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'contact' && request()->segment(2) == 'contact-purchases'])->order(2);
            }

            if (in_array($contact->type, ['customer', 'both'])) {
                $menu->url(action('\Modules\Crm\Http\Controllers\SellController@getSellList'), __('lang_v1.all_sales'), ['icon' => 'fa fas fa-list', 'active' => request()->segment(1) == 'contact' && request()->segment(2) == 'contact-sells'])->order(3);

                if (!empty($crm_settings['enable_order_request'])) {
                    $menu->url(action('\Modules\Crm\Http\Controllers\OrderRequestController@index'), __('crm::lang.order_request'), ['icon' => 'fa fas fa-arrow-circle-up', 'active' => request()->segment(1) == 'contact' && request()->segment(2) == 'order-request'])->order(4);
                }
            }

            $menu->url(action('\Modules\Crm\Http\Controllers\LedgerController@index'), __('lang_v1.ledger'), ['icon' => 'fas fa-scroll', 'active' => request()->segment(1) == 'contact' && request()->segment(2) == 'contact-ledger'])->order(3);

            $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];

            if (in_array('booking', $enabled_modules)) {
                $menu->url(action('\Modules\Crm\Http\Controllers\ContactBookingController@index'), __('restaurant.bookings'), ['icon' => 'fas fa fa-calendar-check', 'active' => request()->segment(1) == 'bookings'])->order(3);
            }
        });

         

        return $next($request);
    }
}
