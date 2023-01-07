<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Manufacturing\Entities\MfgIngredientGroup;
use Modules\Manufacturing\Entities\MfgRecipe;
use Modules\Manufacturing\Entities\MfgRecipeIngredient;
use Modules\Manufacturing\Utils\ManufacturingUtil;
use Yajra\DataTables\Facades\DataTables;

class RecipeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $mfgUtil;
    protected $businessUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, ManufacturingUtil $mfgUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->mfgUtil = $mfgUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_recipe')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $recipes = MfgRecipe::join('variations as v', 'mfg_recipes.variation_id', '=', 'v.id')
                                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                                ->join('products as p', 'v.product_id', '=', 'p.id')
                                ->leftjoin('categories as c', 'p.category_id', '=', 'c.id')
                                ->leftjoin('categories as sc', 'p.sub_category_id', '=', 'sc.id')
                                ->join('units as u', 'p.unit_id', '=', 'u.id')
                                ->where('p.business_id', $business_id)
                                ->with(['ingredients', 'ingredients.variation', 'ingredients.sub_unit', 'sub_unit'])
                                ->select(
                                    'mfg_recipes.id',
                                    DB::raw('IF(
                                        p.type="variable", 
                                        CONCAT(p.name, " - ", pv.name, " - ", v.name, " (", v.sub_sku, ")"), 
                                        CONCAT(p.name, " (", v.sub_sku, ")") 
                                        ) as recipe_name'),
                                    'mfg_recipes.extra_cost',
                                    'mfg_recipes.final_price',
                                    'mfg_recipes.variation_id',
                                    'mfg_recipes.total_quantity',
                                    'mfg_recipes.production_cost_type',
                                    'mfg_recipes.waste_percent',
                                    'mfg_recipes.sub_unit_id',
                                    'u.short_name as unit_name',
                                    'c.name as category',
                                    'sc.name as sub_category'
                                )
                ;


            return Datatables::of($recipes)
                ->addColumn('action', '<button data-href="{{action(\'\Modules\Manufacturing\Http\Controllers\RecipeController@show\', [$id])}}" class="btn btn-xs btn-info btn-modal" data-container=".view_modal"><i class="fa fa-eye"></i> @lang("messages.view")</button> &nbsp; @can("manufacturing.edit_recipe") <a href="{{action(\'\Modules\Manufacturing\Http\Controllers\RecipeController@addIngredients\')}}?variation_id={{$variation_id}}" class="btn btn-xs btn-primary" ><i class="fa fa-edit"></i> @lang("messages.edit")</a>
                    &nbsp; 
                    <button data-href="{{action(\'\Modules\Manufacturing\Http\Controllers\RecipeController@destroy\',[$id])}}" class="btn btn-xs btn-danger delete_recipe"><i class="fa fa-trash"></i> @lang("messages.delete")</button> @endcan')
                ->addColumn('recipe_total', function ($row) {
                    //Recipe price is dynamically calculated from each ingredients
                    $price = $this->mfgUtil->getRecipeTotal($row);
                    
                    return '<span class="display_currency" data-currency_symbol="true">' . $price . '</span>';
                })
                ->editColumn('total_quantity', function ($row) {
                    $quantity = $row->total_quantity;

                    if (!empty($row->waste_percent)) {
                        $quantity = $quantity - ($quantity * $row->waste_percent / 100);
                    }
                    $html = '<span class="display_currency" data-currency_symbol="false" data-is_quantity="true">' . $quantity . '</span>';
                    if (!empty($row->sub_unit_id)) {
                        $html .= ' ' . $row->sub_unit->short_name;
                    } else {
                        $html .= ' ' . $row->unit_name;
                    }

                    return $html;
                })
                ->addColumn('unit_cost', function ($row) {
                    //Recipe price is dynamically calculated from each ingredients
                    $price = $this->mfgUtil->getRecipeTotal($row);

                    $unit_cost = $row->total_quantity > 0 ? $price / $row->total_quantity : 0;

                    return '<span class="display_currency unit_cost" data-unit_cost="' . $unit_cost . '" data-currency_symbol="true">' . $unit_cost . '</span>';
                })
                ->filterColumn('recipe_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(p.name, ' - ', pv.name, ' - ', v.name, ' (', v.sub_sku, ')') like ?", ["%{$keyword}%"]);
                })
                ->addColumn('row_select', function ($row) {
                    return  '<input type="checkbox" class="row-select" value="' . $row->id .'">' ;
                })
                ->rawColumns(['action', 'recipe_total', 'total_quantity', 'unit_cost', 'row_select'])
                ->make(true);
        }

        return view('manufacturing::recipe.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.add_recipe')) {
            abort(403, 'Unauthorized action.');
        }

        $recipes = MfgRecipe::forDropdown($business_id, false);

        return view('manufacturing::recipe.create')->with(compact('recipes'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.add_recipe')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['variation_id', 'ingredients', 'total', 'instructions',
                'ingredients_cost', 'waste_percent', 'total_quantity', 'extra_cost', 'production_cost_type']);
            if (!empty($input['ingredients'])) {
                $variation = Variation::findOrFail($input['variation_id']);


                $recipe = MfgRecipe::updateOrCreate(
                    [
                        'variation_id' => $input['variation_id'],
                    ],
                    [
                        'product_id' => $variation->product_id,
                        'final_price' => $this->moduleUtil->num_uf($input['total']),
                        'ingredients_cost' => $input['ingredients_cost'],
                        'waste_percent' => $this->moduleUtil->num_uf($input['waste_percent']),
                        'total_quantity' => $this->moduleUtil->num_uf($input['total_quantity']),
                        'extra_cost' => $this->moduleUtil->num_uf($input['extra_cost']),
                        'production_cost_type' => $input['production_cost_type'],
                        'instructions' => $input['instructions'],
                        'sub_unit_id' => !empty($request->input('sub_unit_id')) ? $request->input('sub_unit_id') : null
                    ]
                );

                $ingredients = [];
                $edited_ingredients = [];
                $ingredient_groups = $request->input('ingredient_groups');
                $ingredient_group_descriptions = $request->input('ingredient_group_description');
                $created_ig_groups = [];
                
                foreach ($input['ingredients'] as $key => $value) {
                    $variation = Variation::with(['product'])
                                        ->findOrFail($value['ingredient_id']);

                    if (!empty($value['ingredient_line_id'])) {
                        $ingredient = MfgRecipeIngredient::find($value['ingredient_line_id']);
                        $edited_ingredients[] = $ingredient->id;
                    } else {
                        $ingredient = new MfgRecipeIngredient(['variation_id' => $value['ingredient_id']]);
                    }

                    $ingredient->quantity = $this->moduleUtil->num_uf($value['quantity']);
                    $ingredient->waste_percent = $this->moduleUtil->num_uf($value['waste_percent']);
                    $ingredient->sort_order = $this->moduleUtil->num_uf($value['sort_order']);

                    $ingredient->sub_unit_id = !empty($value['sub_unit_id']) && $value['sub_unit_id'] != $variation->product->unit_id ? $value['sub_unit_id'] : null;

                    //Set ingredient group
                    if (isset($value['ig_index'])) {
                        $ig_name = $ingredient_groups[$value['ig_index']];
                        $ig_description = $ingredient_group_descriptions[$value['ig_index']];

                        //Create ingredient group if not created already
                        if (!empty($created_ig_groups[$value['ig_index']])) {
                            $ingredient_group = $created_ig_groups[$value['ig_index']];
                        } elseif (empty($value['mfg_ingredient_group_id'])) {
                            $ingredient_group = MfgIngredientGroup::create(
                                [
                                    'name' => $ig_name,
                                    'business_id' => $business_id,
                                    'description' => $ig_description
                                ]
                            );
                        } else {
                            $ingredient_group = MfgIngredientGroup::where('business_id', $business_id)
                                                                ->find($value['mfg_ingredient_group_id']);
                            if ($ingredient_group->name != $ig_name || $ingredient_group->description != $ig_description) {
                                $ingredient_group->name = $ig_name;
                                $ingredient_group->description = $ig_description;
                                $ingredient_group->save();
                            }

                            $ingredient_group = MfgIngredientGroup::firstOrNew(
                                ['business_id' => $business_id, 'id' => $value['mfg_ingredient_group_id']],
                                ['name' => $ig_name, 'description' => $ig_description]
                            );
                        }

                        $created_ig_groups[$value['ig_index']] = $ingredient_group;

                        $ingredient->mfg_ingredient_group_id = $ingredient_group->id;
                    }

                    $ingredients[] = $ingredient;
                }
                if (!empty($edited_ingredients)) {
                    MfgRecipeIngredient::where('mfg_recipe_id', $recipe->id)
                                                ->whereNotIn('id', $edited_ingredients)
                                                ->delete();
                }

                $recipe->ingredients()->saveMany($ingredients);
            }
            $output = ['success' => 1,
                            'msg' => __('lang_v1.added_success')
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->action('\Modules\Manufacturing\Http\Controllers\RecipeController@index')->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_recipe')) {
            abort(403, 'Unauthorized action.');
        }

        $recipe = MfgRecipe::with(['variation', 'variation.product', 'variation.product_variation', 'variation.media', 'sub_unit', 'variation.product.unit'])
                        ->findOrFail($id);

        $ingredients = $this->mfgUtil->getIngredientDetails($recipe, $business_id);
        return view('manufacturing::recipe.show', compact('recipe', 'ingredients'));
    }

    /**
     * Get ingredients row while adding recipe.
     * @return Response
     */
    public function getIngredientRow($variation_id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_recipe')) {
            abort(403, 'Unauthorized action.');
        }

        $ingredient = Variation::with('product', 'product_variation', 'product.unit')
                            ->findOrFail($variation_id);

        $sub_units = $this->moduleUtil->getSubUnits($business_id, $ingredient->product->unit->id);

        $ingredient->unit = $ingredient->product->unit->short_name;
        $ingredient->sub_units = $sub_units;

        $row_index = request()->input('row_index');

        $ig_index = request()->input('row_ig_index');

        $sort_order = request()->input('sort_order');

        return view('manufacturing::recipe.ingredient_row', compact('ingredient', 'row_index', 'ig_index', 'sort_order'));
    }

    /**
     * Shows recipe form.
     * @return Response
     */
    public function addIngredients()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.add_recipe')) {
            abort(403, 'Unauthorized action.');
        }

        $variation_id = request()->input('variation_id');

        $variation = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
                            ->join('product_variations as pv', 'pv.id', '=', 'variations.product_variation_id')
                            ->join('units as u', 'u.id', '=', 'p.unit_id')
                            ->where('p.business_id', request()->session()->get('user.business_id'))
                            ->select('p.name as product_name', 'p.type as product_type', 'variations.*', 'pv.name as product_variation_name', 'p.unit_id as unit_id', 'u.short_name as unit_name')
                            ->findOrFail($variation_id);
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $with = [
            'ingredients' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'ingredients.variation', 'ingredients.variation.product',
            'ingredients.variation.product.unit',
            'ingredients.variation.product_variation',
            'ingredients.sub_unit', 'ingredients.ingredient_group'];

        $recipe = MfgRecipe::where('variation_id', $variation_id)
                        ->with($with)
                        ->first();

        $copy_recipe = null;

        //If new recipe and copy from recipe selected get copy recipe
        if (empty($recipe) && !empty(request()->input('copy_recipe_id'))) {
            $copy_recipe = MfgRecipe::with($with)
                        ->find(request()->input('copy_recipe_id'));
        }

        $ingredients = [];
        $total_production_cost = 0;
        if (!empty($recipe) || (empty($recipe) && !empty($copy_recipe))) {
            $ingredients_obj = !empty($copy_recipe) ? $copy_recipe->ingredients : $recipe->ingredients;
            
            if (!empty($recipe)) {
                $total_production_cost = $this->mfgUtil->getProductionCost($recipe);
            }
            
            foreach ($ingredients_obj as $ingredient) {
                if (empty($ingredient->variation)) {
                    continue;
                }
                
                $ingredient_sub_units = $this->transactionUtil->getSubUnits($business_id, $ingredient->variation->product->unit->id);
                $multiplier = !empty($ingredient->sub_unit_id) ? $ingredient->sub_unit->base_unit_multiplier : 1;
                if (empty($multiplier)) {
                    $multiplier = 1;
                }
                $temp = [
                    'id' => $ingredient->variation->id,
                    'dpp_inc_tax' => $ingredient->variation->dpp_inc_tax,
                    'quantity' => $ingredient->quantity,
                    'multiplier' => $multiplier,
                    'sub_units' => $ingredient_sub_units,
                    'sub_unit_id' => $ingredient->sub_unit_id,
                    'unit' => $ingredient->variation->product->unit->short_name,
                    'full_name' => $ingredient->variation->full_name,
                    'waste_percent' => !empty($ingredient->waste_percent) ? $ingredient->waste_percent : 0,
                    'sort_order' => $ingredient->sort_order,
                    'ingredient_line_id' => empty($copy_recipe) ? $ingredient->id : null,
                    'mfg_ingredient_group_id' => $ingredient->mfg_ingredient_group_id,
                    'ingredient_group_name' => !empty($ingredient->ingredient_group->name) ? $ingredient->ingredient_group->name : '',
                    'ig_description' => !empty($ingredient->ingredient_group->description) ? $ingredient->ingredient_group->description : '',
                ];

                $ingredients[] = $temp;
            }
        }

        $sub_units = $this->moduleUtil->getSubUnits($business_id, $variation->unit_id);

        $unit_html = !empty($sub_units) ? $sub_units : $variation->unit_name;

        return view('manufacturing::recipe.add_ingredients', compact('variation', 'ingredients', 'recipe', 'unit_html', 'currency_details', 'total_production_cost'));
    }

    /**
     * Retrieves selected recipe details for production.
     * @return Response
     */
    public function getRecipeDetails()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_recipe')) {
            abort(403, 'Unauthorized action.');
        }

        $variation_id = request()->input('variation_id');
        $location_id = request()->input('location_id');

        $recipe = MfgRecipe::where('variation_id', $variation_id)
                        ->with(['variation', 'variation.product', 'variation.product.unit', 'sub_unit',
                        'ingredients' => function ($query) {
                            $query->orderBy('sort_order', 'asc');
                        }])
                        ->first();

        $ingredients = [];
        if (!empty($recipe)) {
            $ingredients_array = [];

            foreach ($recipe->ingredients as $ingredient) {
                $ingredient_quantity = $this->mfgUtil->calc_percentage($ingredient->quantity, $ingredient->waste_percent, $ingredient->quantity);
                $ingredients_array[$ingredient->variation_id] = [
                    'quantity' => $ingredient_quantity,
                    'sub_unit_id' => $ingredient->sub_unit_id,
                    'waste_percent' => $ingredient->waste_percent
                ];
            }
            $ingredients = $this->mfgUtil->getIngredientDetails($recipe, $business_id, $location_id);
        }
        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $manufacturing_settings = $this->mfgUtil->getSettings($business_id);

        $ingredient_table = view('manufacturing::recipe.ingredients_for_production', compact('ingredients', 'recipe', 'pos_settings', 'manufacturing_settings'))->render();

        $sub_units = $this->moduleUtil->getSubUnits($business_id, $recipe->variation->product->unit->id);

        $unit_html = $recipe->variation->product->unit->short_name;
        $is_sub_unit = 0;
        $unit_name = $unit_html;
        if (!empty($sub_units)) {
            $unit_html = '<select name="sub_unit_id" class="form-control" id="sub_unit_id">';
            foreach ($sub_units as $key => $value) {
                $unit_html .= '<option value="' . $key . '" data-multiplier="' . $value['multiplier'] . '" data-unit_name="' . $value['name'] . '" ';
                if (!empty($recipe->sub_unit_id) && $recipe->sub_unit_id == $key) {
                    $unit_html .= ' selected ';
                    $unit_name = $value['name'];
                }
                $unit_html .= '>' . $value['name'] . '</option>';
            }
            $unit_html .= '</select>';
            $is_sub_unit = 1;
        }

        return json_encode([
                'ingredient_table' => $ingredient_table,
                'recipe' => $recipe,
                'unit_html' => $unit_html,
                'is_sub_unit' => $is_sub_unit,
                'unit_name' => $unit_name
            ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getIngredientGroupForm()
    {
        $ig_index = request()->input('ig_index');

        return view('manufacturing::recipe.ingredient_group')
                ->with(compact('ig_index'));
    }

    /**
     * Function to update variation prices from recipe unit price.
     * @param  Request $request
     * @return Response
     */
    public function updateRecipeProductPrices(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.add_recipe')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $recipe_ids = $request->input('recipe_ids');
            $unit_prices = $request->input('unit_prices');

            if (!empty($recipe_ids)) {
                $recipes = MfgRecipe::with(['variation', 'sub_unit', 'variation.product', 'variation.product.product_tax'])
                                ->whereIn('id', $recipe_ids)
                                ->get();

                DB::beginTransaction();
                foreach ($recipes as $recipe) {
                    $variation = $recipe->variation;
                    $unit_price = $unit_prices[$recipe->id];

                    //Calculate unit price in base unit
                    if (!empty($recipe->sub_unit->base_unit_multiplier)) {
                        $unit_price = $unit_price / $recipe->sub_unit->base_unit_multiplier;
                    }

                    $unit_price_exc_tax = $unit_price;

                    if (!empty($variation->product->product_tax)) {
                        $tax_percent = $variation->product->product_tax->amount;
                        $unit_price_exc_tax = $this->transactionUtil->calc_percentage_base($unit_price, $tax_percent);
                    }
                    $variation->default_purchase_price = $unit_price_exc_tax;
                    $variation->dpp_inc_tax = $unit_price;

                    //Keep sell price constant and change profit margin
                    $profit_margin = $this->transactionUtil->get_percent($unit_price, $variation->sell_price_inc_tax);
                    $sell_price_excluding_tax =  $this->transactionUtil->calc_percentage($unit_price_exc_tax, $profit_margin, $unit_price_exc_tax);
                    
                    $variation->default_sell_price   = $sell_price_excluding_tax;
                    $variation->profit_percent = $profit_margin;
                    $variation->save();
                }
                $output = ['success' => 1,
                            'msg' => __('lang_v1.updated_succesfully')
                        ];
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.add_recipe')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $recipe = MfgRecipe::where('id', $id)
                        ->delete();

            $output = ['success' => 1,
                            'msg' => __('lang_v1.deleted_success')
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;
    }

    /**
     * Check if recipe exist.
     *
     * @param  int  $variation_id
     */
    public function isRecipeExist($variation_id)
    {
        $exists = MfgRecipe::where('variation_id', $variation_id)
                            ->exists();

        $output =  $exists ? 1 : 0;
        return $output;
    }
}
