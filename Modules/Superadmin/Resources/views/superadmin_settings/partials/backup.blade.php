<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('BACKUP_DISK', __('superadmin::lang.backup_disk') . ':') !!}
            	{!! Form::select('BACKUP_DISK', $backup_disk, $default_values['BACKUP_DISK'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-8 @if(env('BACKUP_DISK') != 'dropbox') hide @endif" id="dropbox_access_token_div">
            <div class="form-group">
            	{!! Form::label('DROPBOX_ACCESS_TOKEN', __('superadmin::lang.dropbox_access_token') . ':') !!}
            	{!! Form::text('DROPBOX_ACCESS_TOKEN', $default_values['DROPBOX_ACCESS_TOKEN'], ['class' => 'form-control','placeholder' => __('superadmin::lang.dropbox_access_token')]); !!}
            </div>
            <p class="help-block">{!! __('superadmin::lang.dropbox_help', ['link' => 'https://www.dropbox.com/developers/apps/create']) !!}</p>
        </div>
    </div>
</div>