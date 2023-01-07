<div class="modal-dialog modal-xl no-print" role="document">
  <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> @lang('repair::lang.repair_details') (<b>@lang('sale.invoice_no'):</b> {{ $sell->invoice_no }})
    </h4>
</div>
<div class="modal-body">
    <div class="row">
      <div class="col-xs-12">
          <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($sell->transaction_date) }}</p>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <b>{{ __('sale.invoice_no') }}:</b> #{{ $sell->invoice_no }}<br>
        <b>{{ __('sale.status') }}:</b> <span class="label" style="background-color: {{$sell->repair_status_color}};">{{$sell->repair_status}}</span>
        <br>
        <b>{{ __('sale.payment_status') }}:</b> {{ ucfirst( $sell->payment_status ) }}<br>
      </div>
      <div class="col-sm-4">
        <b>{{ __('sale.customer_name') }}:</b> {{ $sell->contact->name }}<br>
        <b>{{ __('business.address') }}:</b><br>
        @if(!empty($sell->billing_address()))
          {{$sell->billing_address()}}
        @else
          {!! $sell->contact->contact_address !!}
        @endif
      </div>
      <div class="col-sm-4">
        <strong>@lang('product.brand'): </strong> {{$sell->manufacturer}}<br>
        <strong>@lang('repair::lang.device'): </strong> {{$sell->repair_device}}<br>
        <strong>@lang('repair::lang.model'): </strong> {{$sell->repair_model}}<br>
        <strong>@lang('repair::lang.serial_no'): </strong> {{$sell->repair_serial_no}}<br>
        @if(in_array('service_staff' ,$enabled_modules))
          <strong>@lang('repair::lang.technician'): </strong> {{$sell->service_staff}}<br>
        @endif

        @if(!empty($warranty_expires_in))
          <strong>@lang('repair::lang.warranty'): </strong> {{$sell->warranty_name}}
          <small class="help-block">( @lang('repair::lang.expires_in') {{$warranty_expires_in}} )</small>
        @endif
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.products') }}:</h4>
      </div>

      <div class="col-sm-12 col-xs-12">
        <div class="table-responsive">
          @include('sale_pos.partials.sale_line_details')
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <strong>{{ __('repair::lang.defect')}}:</strong><br>
        <p class="well well-sm no-shadow">
            @php
                $defects = json_decode($sell->repair_defects, true);
            @endphp
            @if(!empty($defects))
                @foreach($defects as $product_defect)
                    {{$product_defect['value']}}
                    @if(!$loop->last)
                        {{','}}
                    @endif
                @endforeach
            @endif
        </p>
      </div>
      <div class="clearfix"></div>
      <div class="col-sm-6">
        <div class="box box-default box-solid collapsed-box">
            <div class="box-header with-border collapsed-box-title" style="cursor: pointer;">
                <h3 class="box-title">{{ __('repair::lang.pre_repair_checklist') }}:</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                    </button>
                </div>
                <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="display: none;">
                @if(!empty($sell->repair_checklist))
                    @php
                        $selected_checklist = json_decode($sell->repair_checklist, true);
                    @endphp
                    <div class="row">
                        @foreach($checklists as $check)
                            <div class="col-xs-4">
                                @if($selected_checklist[$check] == 'yes')
                                    <i class="fas fa-check-square text-success"></i>
                                @elseif($selected_checklist[$check] == 'no')
                                  <i class="fas fa-window-close text-danger"></i>
                                @elseif($selected_checklist[$check] == 'not_applicable')
                                  <i class="fas fa-square"></i>
                                @endif
                                {{$check}}
                                <br>
                                <br>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <!-- /.box-body -->
        </div>
      </div>
      <div class="col-sm-6">
        <div class="box box-default box-solid collapsed-box">
            <div class="box-header with-border collapsed-box-title" style="cursor: pointer;">
                <h3 class="box-title">{{ __('sale.payment_info') }}:</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                    </button>
                </div>
                <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="display: none;">
                <div class="table-responsive">
                    <table class="table bg-gray">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('purchase.ref_no') }}</th>
                            <th>{{ __('sale.amount') }}</th>
                            <th>{{ __('sale.payment_mode') }}</th>
                            <th>{{ __('sale.payment_note') }}</th>
                        </tr>
                        @php
                          $total_paid = 0;
                        @endphp
                        @foreach($sell->payment_lines as $payment_line)
                            @php
                                if($payment_line->is_return == 1){
                                    $total_paid -= $payment_line->amount;
                                } else {
                                    $total_paid += $payment_line->amount;
                                }
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ @format_date($payment_line->paid_on) }}</td>
                                <td>{{ $payment_line->payment_ref_no }}</td>
                                <td><span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
                                <td>
                                  {{ $payment_types[$payment_line->method]}}
                                  @if($payment_line->is_return == 1)
                                    <br/>
                                    ( {{ __('lang_v1.change_return') }} )
                                  @endif
                                </td>
                                <td>@if($payment_line->note) 
                                  {{ ucfirst($payment_line->note) }}
                                  @else
                                  --
                                  @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table bg-gray">
                        <tr>
                            <th>{{ __('sale.total') }}: </th>
                            <td></td>
                            <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $sell->total_before_tax }}</span></td>
                        </tr>
                        <tr>
                            <th>{{ __('sale.discount') }}:</th>
                            <td><b>(-)</b></td>
                            <td><span class="pull-right">{{ $sell->discount_amount }} @if( $sell->discount_type == 'percentage') {{ '%'}} @endif</span></td>
                        </tr>
                        @if(session('business.enable_rp') == 1 && !empty($sell->rp_redeemed) )
                          <tr>
                            <th>{{session('business.rp_name')}}:</th>
                            <td><b>(-)</b></td>
                            <td> <span class="display_currency pull-right" data-currency_symbol="true">{{ $sell->rp_redeemed_amount }}</span></td>
                          </tr>
                        @endif
                        <tr>
                            <th>{{ __('sale.order_tax') }}:</th>
                            <td><b>(+)</b></td>
                            <td class="text-right">
                                @if(!empty($order_taxes))
                                  @foreach($order_taxes as $k => $v)
                                    <strong><small>{{$k}}</small></strong> - <span class="display_currency pull-right" data-currency_symbol="true">{{ $v }}</span><br>
                                  @endforeach
                                @else
                                0.00
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('sale.shipping') }}: @if($sell->shipping_details)({{$sell->shipping_details}}) @endif</th>
                            <td><b>(+)</b></td>
                            <td><span class="display_currency pull-right" data-currency_symbol="true">{{ $sell->shipping_charges }}</span></td>
                        </tr>
                        <tr>
                            <th>{{ __('sale.total_payable') }}: </th>
                            <td></td>
                            <td><span class="display_currency pull-right">{{ $sell->final_total }}</span></td>
                        </tr>
                        <tr>
                            <th>{{ __('sale.total_paid') }}:</th>
                            <td></td>
                            <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ $total_paid }}</span></td>
                        </tr>
                        <tr>
                            <th>{{ __('sale.total_remaining') }}:</th>
                            <td></td>
                            <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ $sell->final_total - $total_paid }}</span></td>
                        </tr>
                    </table>
                </div>

            </div>
            <!-- /.box-body -->
        </div>
      </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box box-default box-solid collapsed-box">
                <div class="box-header with-border collapsed-box-title" style="cursor: pointer;">
                    <h3 class="box-title">{{ __('repair::lang.activities') }}:</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                @includeIf('repair::repair.partials.activities')
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-default box-solid collapsed-box">
                <div class="box-header with-border collapsed-box-title" style="cursor: pointer;">
                    <h3 class="box-title">{{ __('lang_v1.documents') }}:</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="display: none;">
                    <table class="table table-condensed bg-gray">
                        <tr>
                            <th>@lang('lang_v1.name')</th>
                            <th>@lang('messages.view')</th>
                            <th>@lang('messages.delete')</th>
                        </tr>
                        @forelse($sell->media as $media)
                        <tr>
                            <td>{{$media->display_name}}</td>
                            <td><a href="{{$media->display_url}}" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-external-link"></i></a></td>
                            <td><a href="{{action('\Modules\Repair\Http\Controllers\RepairController@deleteMedia', $media->id)}}"" class="btn btn-danger btn-xs delete_media"><i class="fa fa-trash"></i></a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">@lang('purchase.no_records_found')</td>
                        </tr>
                        @endforelse
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-default box-solid collapsed-box">
                <div class="box-header with-border collapsed-box-title" style="cursor: pointer;">
                    <h3 class="box-title">{{ __('repair::lang.pass_code_of_device') }}:</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <b>@lang('lang_v1.password'):</b>
                            {{$sell->repair_security_pwd}}
                        </div>
                    </div>
                    <div class="row mt-10">
                        <div class="col-md-6">
                            <b>@lang('repair::lang.security_pattern_code'):</b>
                            <div id="security_pattern_container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="print-invoice btn btn-primary" data-href="{{route('repair.customerCopy', [$sell->id])}}">
        <i class="fa fa-print" aria-hidden="true"></i>
        @lang("repair::lang.print_customer_copy")
    </a>
    <a href="#" class="print-invoice btn btn-primary" data-href="{{route('sell.printInvoice', [$sell->id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);

    @if(!empty($sell->repair_security_pattern))
        var security_pattern =  new PatternLock("#security_pattern_container", {
                                enableSetPattern: true
                            });
        security_pattern.setPattern("{{$sell->repair_security_pattern}}");
    @endif
  });
</script>
