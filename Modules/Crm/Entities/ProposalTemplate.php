<?php

namespace Modules\Crm\Entities;

use Illuminate\Database\Eloquent\Model;

class ProposalTemplate extends Model
{
   	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crm_proposal_templates';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }
}
