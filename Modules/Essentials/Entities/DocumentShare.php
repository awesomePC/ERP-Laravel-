<?php

namespace Modules\Essentials\Entities;

use Illuminate\Database\Eloquent\Model;

class DocumentShare extends Model
{

 /**
 * The attributes that aren't mass assignable.
 *
 * @var array
 */
    protected $guarded = ['id'];

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'essentials_document_shares';

    public static function documentShareNotificationData($data) {
    	return [
    		'msg' => __('essentials::lang.document_share_notification', ['document_name' => $data['document_name'], 'shared_by' => $data['shared_by_name']]),
    		'title' => __('essentials::lang.document_shared'),
    		'link' => $data['document_type'] != 'memos' ? action('\Modules\Essentials\Http\Controllers\DocumentController@index') :
            action('\Modules\Essentials\Http\Controllers\DocumentController@index') .'?type=memos',
            'icon' => $data['document_type'] != 'memos' ? "fas fa-file bg-green" : "fas fa-envelope-open bg-green"
    	];
    }
}
