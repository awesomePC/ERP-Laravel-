<?php

namespace Modules\Project\Entities;

use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pjt_invoice_lines';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id'];

    /**
    * Get the tax for invoice line.
    */
    public function tax()
    {
        return $this->belongsTo(\App\TaxRate::class, 'tax_rate_id');
    }
}
