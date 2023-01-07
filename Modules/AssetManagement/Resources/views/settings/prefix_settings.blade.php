<div class="pos-tab-content active">
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('asset_code_prefix', __('assetmanagement::lang.asset_code_prefix') . ':') !!}
						{!! Form::text('asset_code_prefix', !empty($asset_settings['asset_code_prefix'])? $asset_settings['asset_code_prefix'] : '', ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.asset_code_prefix')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('allocation_code_prefix', __('assetmanagement::lang.allocation_code_prefix') . ':') !!}
						{!! Form::text('allocation_code_prefix', !empty($asset_settings['allocation_code_prefix'])? $asset_settings['allocation_code_prefix'] : '', ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.allocation_code_prefix')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('revoke_code_prefix', __('assetmanagement::lang.revoke_code_prefix') . ':') !!}
						{!! Form::text('revoke_code_prefix', !empty($asset_settings['revoke_code_prefix'])? $asset_settings['revoke_code_prefix'] : '', ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.revoke_code_prefix')]); !!}
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('asset_maintenance_prefix', __('assetmanagement::lang.asset_maintenance_prefix') . ':') !!}
						{!! Form::text('asset_maintenance_prefix', !empty($asset_settings['asset_maintenance_prefix'])? $asset_settings['asset_maintenance_prefix'] : '', ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.asset_maintenance_prefix')]); !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>