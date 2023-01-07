<?php
namespace Modules\Manufacturing\Utils;

use App\Business;
use App\Transaction;
use App\TransactionSellLinesPurchaseLines;
use App\Utils\Util;
use App\Variation;
use DB;
use Modules\Manufacturing\Entities\MfgRecipeIngredient;

class ManufacturingUtil extends Util
{
    /**
     * Retrives ingredients details.
     * @return array
     */
    public function getIngredientDetails($recipe, $business_id, $location_id = null)
    {
        $ingredients_array = [];
        $with = ['variation', 'variation.product', 'variation.product_variation', 'variation.product.unit'];

        //If location given retrieve variation location details
        if (!empty($location_id)) {
            $with = ['variation', 'variation.product', 'variation.product_variation', 'ingredient_group', 'variation.product.unit',
            'variation.variation_location_details' => function ($q) use ($location_id) {
                $q->where('location_id', $location_id);
            }];
        }

        $ingredient_variations = MfgRecipeIngredient::where('mfg_recipe_id', $recipe->id)
                            ->with($with)
                            ->orderBy('sort_order', 'asc')
                            ->get();

        //Format variation data
        foreach ($ingredient_variations as $ingredient_variation) {
            $variation = $ingredient_variation->variation;
            //If base unit has sub_units get details
            $sub_units = $this->getSubUnits($business_id, $variation->product->unit->id);
            $unit_name = $variation->product->unit->short_name;
            $is_sub_unit = false;
            $sub_unit_id = null;
            $multiplier = 1;
            if (!empty($sub_units)) {
                foreach ($sub_units as $key => $value) {
                    if (!empty($ingredient_variation->sub_unit_id) && $ingredient_variation->sub_unit_id == $key) {
                        $unit_name = $value['name'];
                        $sub_unit_id = $ingredient_variation->sub_unit_id;
                        $multiplier = $value['multiplier'];
                    }
                }
                $is_sub_unit = true;
            }

            $line_total_quantity = $ingredient_variation->quantity;
            $unit_qty = $line_total_quantity * $multiplier;

            if (!empty($recipe)) {
                $recipe_base_unit_multiplier = !empty($recipe->sub_unit) ? $recipe->sub_unit->base_unit_multiplier : 1;
                $total_recipe_qty = !empty($recipe_base_unit_multiplier) ? $recipe->total_quantity * $recipe_base_unit_multiplier : $recipe->total_quantity;
                $unit_qty = $total_recipe_qty > 0 ? $unit_qty / $total_recipe_qty : 0;
            }
            $total_price = $variation->dpp_inc_tax * $line_total_quantity * $multiplier;
            $waste_percent = !empty($ingredient_variation->waste_percent) ? $ingredient_variation->waste_percent : 0;

            $wasted_qty = $this->calc_percentage($line_total_quantity, $waste_percent);
            $final_quantity = $line_total_quantity - $wasted_qty;
            $ingredients[] = [
                'dpp_inc_tax' => $variation->dpp_inc_tax,
                'quantity' => $line_total_quantity,
                'full_name' => $variation->full_name,
                'variation_id' => $variation->id,
                'id' => $ingredient_variation->id,
                'unit' => $unit_name,
                'allow_decimal' => $variation->product->unit->allow_decimal,
                'variation' => $variation,
                'enable_stock' => $variation->product->enable_stock,
                'is_sub_unit' => $is_sub_unit,
                'sub_units' => $sub_units,
                'sub_unit_id' => $sub_unit_id,
                'multiplier' => $multiplier,
                'unit_quantity' => $unit_qty,
                'total_price' => $total_price,
                'waste_percent' =>  $waste_percent,
                'final_quantity' => $final_quantity,
                'mfg_ingredient_group_id' => $ingredient_variation->mfg_ingredient_group_id,
                'ingredient_group_name' => !empty($ingredient_variation->ingredient_group->name) ? $ingredient_variation->ingredient_group->name : '',
                'ig_description' => !empty($ingredient_variation->ingredient_group->description) ? $ingredient_variation->ingredient_group->description : '',
           ];
        }

        return $ingredients;
    }

    /**
     * Retrives manufacturing settings.
     * @return array
     */
    public function getSettings($business_id)
    {
        $business = Business::findOrFail($business_id);

        $settings = !empty($business->manufacturing_settings) ? json_decode($business->manufacturing_settings, true) : [];

        return $settings;
    }

    /**
     * Calculates production totals.
     * @param int $business_id
     * @param int location_id = null
     * @param string $start_date = null
     * @param string $end_date = null
     */
    public function getProductionTotals(
        $business_id,
        $location_id = null,
        $start_date = null,
        $end_date = null,
        $user_id = null
    ) {
        $query = Transaction::where('business_id', $business_id)
                    ->where('type', 'production_purchase')
                    ->where('mfg_is_final', 1)
                    ->leftJoin('purchase_lines as pl', 'transactions.id', '=', 'pl.transaction_id')
                    ->select(
                        DB::raw('SUM(final_total) as total_production'),
                        DB::raw("SUM( 
                                IF(
                                    mfg_production_cost_type='percentage', 
                                    final_total - ((final_total * 100) / (mfg_production_cost + 100) ),
                                    IF(
                                        mfg_production_cost_type='per_unit',
                                        mfg_production_cost*pl.quantity,
                                        mfg_production_cost
                                    )
                                )
                            ) as total_production_cost")
                    )
                    ->groupBy('transactions.id');
    
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('transaction_date', '<=', $end_date);
        }

        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }

        if (!empty($user_id)) {
            $query->where('transactions.created_by', $user_id);
        }

        $production_details = $query->get();

        $output = [
            'total_production' => $production_details->SUM('total_production'),
            'total_production_cost' => $production_details->SUM('total_production_cost'),
        ];
        return $output;
    }

    /**
     * Calculates sum of total production sells.
     * @param int $business_id
     * @param int location_id = null
     * @param string $start_date = null
     * @param string $end_date = null
     */
    public function getTotalSold(
        $business_id,
        $location_id = null,
        $start_date = null,
        $end_date = null
    ) {
        $query = TransactionSellLinesPurchaseLines::join('purchase_lines as pl', 'pl.id', '=', 'transaction_sell_lines_purchase_lines.purchase_line_id')
                            ->join('transaction_sell_lines as tsl', 'tsl.id', '=', 'transaction_sell_lines_purchase_lines.sell_line_id')
                                ->join('transactions as t', 'pl.transaction_id', '=', 't.id')
                                ->where('t.business_id', $business_id)
                                ->where('t.type', 'production_purchase')
                                ->select(DB::raw('SUM((tsl.quantity - tsl.quantity_returned) * tsl.unit_price_inc_tax) as total_sold'));

        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('t.location_id', $permitted_locations);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }

        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('transaction_date', '<=', $end_date);
        }

        //Filter by the location
        if (!empty($location_id)) {
            $query->where('t.location_id', $location_id);
        }

        $sell_details = $query->get();

        $total_sold = $sell_details->sum('total_sold');
        return $total_sold;
    }

    /**
     * Function to calculate recipe total dynamically for each row on
     * Recipe list
     *
     * @return decimal
     */
    public function getRecipeTotal($row)
    {
        $price = 0;
        foreach ($row->ingredients as $ingredient) {
            if (!empty($ingredient->variation)) {
                $ingredient_total = $ingredient->variation->dpp_inc_tax * $ingredient->quantity;
                if (!empty($ingredient->sub_unit)) {
                    $multiplier = !empty($ingredient->sub_unit->base_unit_multiplier) ? $ingredient->sub_unit->base_unit_multiplier : 1;
                    $ingredient_total = $ingredient_total * $multiplier;
                }
                $price += $ingredient_total;
            }
        }

        $production_cost = $row->extra_cost;
        if ($row->production_cost_type == 'percentage') {
            $production_cost = ($price * $row->extra_cost) / 100;
        } elseif ($row->production_cost_type == 'per_unit') {
            $production_cost = $row->extra_cost * $row->total_quantity;
        }
        $price = $price + $production_cost;

        return $price;
    }

    public function getProductionCost($recipe)
    {

        $total_production_cost = 0;
        if (!empty($recipe->extra_cost)) {
            $total_production_cost = $recipe->extra_cost;
            if ($recipe->production_cost_type == 'percentage') {
                $total_production_cost = $this->calc_percentage($recipe->ingredients_cost, $recipe->extra_cost);
            } elseif ($recipe->production_cost_type == 'per_unit') {
                $total_production_cost = $recipe->extra_cost * $recipe->total_quantity;
            }
        }

        return $total_production_cost;

    }
}
