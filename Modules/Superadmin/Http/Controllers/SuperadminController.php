<?php

namespace Modules\Superadmin\Http\Controllers;

use Modules\Superadmin\Entities\Subscription;
use App\Business;
use App\System;
use \Carbon;
use Charts;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Charts\CommonChart;

class SuperadminController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $date_filters['this_yr'] = ['start' => Carbon::today()->startOfYear()->toDateString(),
                'end' => Carbon::today()->endOfYear()->toDateString()
            ];
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $currency = System::getCurrency();

        //Count all busineses not subscribed.
        $not_subscribed = Business::leftjoin('subscriptions AS s', 'business.id', '=', 's.business_id')
            ->whereNull('s.id')
            ->count();

        $subscriptions = $this->_monthly_sell_data();

        $monthly_sells_chart = new CommonChart;
        $monthly_sells_chart->labels(array_keys($subscriptions))
            ->dataset(__('superadmin::lang.total_subscriptions', ['currency' => $currency->currency]), 'column', array_values($subscriptions));

        return view('superadmin::superadmin.index')
            ->with(compact(
                'date_filters',
                'not_subscribed',
                'monthly_sells_chart'
            ));
    }

    /**
     * Returns the monthly sell data for chart
     * @return array
     */
    protected function _monthly_sell_data()
    {
        $start = Carbon::today()->subYear();
        $end = Carbon::today();
        $subscriptions = Subscription::whereRaw('DATE(created_at) BETWEEN ? AND ?', [$start, $end])
            ->select('package_price', 'created_at')
            ->orderBy('created_at')
            ->get();
        $subscription_formatted = [];
        foreach ($subscriptions as $value) {
            $month_year = Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->format('M-Y');
            if (!isset($subscription_formatted[$month_year])) {
                $subscription_formatted[$month_year] = 0;
            }
            $subscription_formatted[$month_year] += (float) $value->package_price;
        }

        return $subscription_formatted;
    }

    /**
     * Returns the stats for superadmin
     *
     * @param $start date
     * @param $end date
     *
     * @return json
     */
    public function stats(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $start_date = $request->get('start');
        $end_date = $request->get('end');

        $subscription = Subscription::whereRaw('DATE(created_at) BETWEEN ? AND ?', [$start_date, $end_date])
            ->where('status', 'approved')
            ->select(DB::raw('SUM(package_price) as total'))
            ->first()
            ->total;

        $registrations = Business::whereRaw('DATE(created_at) BETWEEN ? AND ?', [$start_date, $end_date])
            ->select(DB::raw('COUNT(id) as total'))
            ->first()
            ->total;

        return ['new_subscriptions' => $subscription,
                'new_registrations' => $registrations
            ];
    }
}
