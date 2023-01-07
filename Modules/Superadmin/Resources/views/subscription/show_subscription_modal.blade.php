<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header no-print">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title"></h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-xs-6">
          <div class="well well-sm">
            <strong>@lang('business.business_name'): </strong> {{$system["invoice_business_name"]}} <br>
            <strong>@lang('business.email'): </strong> {{$system["email"]}} <br>
            <strong>@lang('business.landmark'): </strong> {{$system["invoice_business_landmark"]}} <br>
            <strong>@lang('business.city'): </strong> {{$system["invoice_business_city"]}}
            <strong>@lang('business.zip_code'): </strong> {{$system["invoice_business_zip"]}} <br>
            <strong>@lang('business.state'): </strong> {{$system["invoice_business_state"]}}
            <strong>@lang('business.country'): </strong> {{$system["invoice_business_country"]}}
          </div>
        </div>
        <div class="col-xs-6">
          <div class="well well-sm">
            <strong>@lang('business.business_name'): </strong> {{$subscription->business->name}} <br>
            @if(!empty($subscription->business->tax_number_1) && !empty($subscription->business->tax_label_1))
              <strong>{{$subscription->business->tax_label_1}}: </strong> {{$subscription->business->tax_number_1}} <br>
            @endif
            
            @if(!empty($subscription->business->tax_number_2) && !empty($subscription->business->tax_label_2))
              <strong>{{$subscription->business->tax_label_2}}: </strong> {{$subscription->business->tax_number_2}} <br>
            @endif
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <table class="table subscription-details">
            <thead>
              <tr>
                <th>Package</th>
                <th>Quantity</th>
                <th>Price</th>
              </tr>
            </thead>
            <body>
              <tr>
                <td>{{$subscription->package->name}}</td>
                <td>1</td>
                <td><span class="display_currency" data-currency_symbol="true" data-use_page_currency="true">{{ $subscription->package_price }}</span> </td>
              </tr>
            </body>
          </table>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-xs-12">
          <table class="table">
            <tr>
              <th>Created At:</th>
              <td>{{@format_date($subscription->created_at)}}</td>
              <th>Payment Transaction ID:</th>
              <td>{{$subscription->payment_transaction_id}}</td>
            </tr>
            <tr>
              <th>Created By:</th>
              <td>{{$subscription->created_user->user_full_name}}</td>
              <th>Paid Via:</th>
              <td>{{$subscription->paid_via}}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <div class="modal-footer no-print">
      <button type="button" class="btn btn-primary" aria-label="Print" 
      onclick="$(this).closest('div.modal-content').printThis();"><i class="fa fa-print"></i> @lang( 'messages.print' )
      </button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script type="text/javascript">
  $(document).ready(function(){
    __currency_convert_recursively($('.subscription-details'));
  })
</script>