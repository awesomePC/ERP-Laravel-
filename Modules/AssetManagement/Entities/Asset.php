<?php

namespace Modules\AssetManagement\Entities;
use DB;
use Illuminate\Database\Eloquent\Model;
use Modules\AssetManagement\Entities\AssetTransaction;
class Asset extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * user added asset.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * get business location for asset
     */
    public function businessLocation()
    {
        return $this->belongsTo('App\BusinessLocation', 'location_id');
    }

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    public static function forDropdown($business_id, $include_attributes = false, $check_qty = true)
    {   
        $allocation = AssetTransaction::where('business_id', $business_id)
                        ->where('transaction_type', 'allocate')
                        ->select(DB::raw('SUM(COALESCE(quantity, 0)) as allocated'), 'asset_id')
                        ->groupBy('asset_id');

        $revocation = AssetTransaction::where('business_id', $business_id)
                        ->where('transaction_type', 'revoke')
                        ->select(DB::raw('SUM(COALESCE(quantity, 0)) as revoked'), 'asset_id')
                        ->groupBy('asset_id');

        $query = Asset::leftJoinSub($allocation, 'allocation', function ($join) {
                        $join->on('assets.id', '=', 'allocation.asset_id');
                    })
                    ->leftJoinSub($revocation, 'revocation', function ($join) {
                        $join->on('assets.id', '=', 'revocation.asset_id');
                    })
                    ->where('assets.business_id', $business_id)
                    ->where('is_allocatable', 1)
                    ->select('assets.name as name', 'assets.id as id', DB::raw('assets.quantity - COALESCE(allocated, 0) + COALESCE(revoked, 0) as quantity'));

        if ($check_qty) {
            $query->havingRaw('quantity > 0');
        }

        $query = $query->groupBy('assets.id')
                    ->get();

        $assets = [];
        foreach ($query as $key => $asset) {
            $assets[$asset->id] = $asset->name .'('. (int)$asset->quantity .')';

        }

        //Add quantity as attribute
        $quantity_attr = null;
        if ($include_attributes) {
            $quantity_attr = collect($query)->mapWithKeys(function($asset){
                return [$asset->id => ['data-quantity' => $asset->quantity, 'disabled' => ($asset->quantity < 1) ? true : false]];
            })->all();
        }

        $output = ['assets' => $assets, 'asset_quantity' => $quantity_attr];

        return $output;
    }

    public function warranties()
    {
        return $this->hasMany(\Modules\AssetManagement\Entities\AssetWarranty::class);
    }

    public function maintenances()
    {
        return $this->hasMany(\Modules\AssetManagement\Entities\AssetMaintenance::class);
    }

    public function getIsInWarrantyAttribute()
    {
        $now = \Carbon::now();
        foreach ($this->warranties as $w) {
            $start_date = \Carbon::parse($w->start_date);
            $end_date = \Carbon::parse($w->end_date);
            
            if ($now->between($start_date, $end_date)) {
                return $w;
            }
        }

        return null;
    }
}
