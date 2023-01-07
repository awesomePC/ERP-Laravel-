@extends('layouts.app')
@section('title', 'Delivery Partners')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1>Delivery Partners
        </h1>
    </section>

    <!-- Main content -->
    <section class="content no-print">
        @component('components.widget', ['class' => 'box-primary', 'title' => 'Add Partner'])
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" id="name" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Contact:</label>
                        <input type="text" id="contact" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Address:</label>
                        <input type="text" id="address" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Charge (In-Dhaka):</label>
                        <input type="text" id="charge_0" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Charge (Out-Dhaka):</label>
                        <input type="text" id="charge_1" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Customer Portal:</label>
                        <input type="url" id="customer_portal" class="form-control">
                    </div>
                </div>
                <div class="col-md-12 text-right">
                    <button class="btn btn-primary" onclick="addPartner()">Add Partner</button>
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.all_sales')])
            @if (auth()->user()->can('direct_sell.view') ||
                auth()->user()->can('view_own_sell_only') ||
                auth()->user()->can('view_commission_agent_sell'))
                <table class="table table-bordered table-striped ajax_view" id="delivery_partners_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Delivery Charges</th>
                            <th>Customer Portal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($delivery_partners as $partner)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $partner->name }}</td>
                                <td>{{ $partner->contact }}</td>
                                <td>{{ $partner->address }}</td>
                                <td>
                                    @php
                                        $charges = json_decode($partner->delivery_charges);
                                    @endphp
                                    <span>In-Dhaka: <b>{{ '&#2547;' . number_format($charges->charge_0, 2) }}</b></span>
                                    <br>
                                    <span>Out-Dhaka: <b>{{ '&#2547;' . number_format($charges->charge_1, 2) }}</b></span>
                                </td>
                                <td>{{ $partner->customer_portal }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenu1"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            Actions
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                            <li><a href="#">Edit</a></li>
                                            <li><a href="#">Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endcomponent
    </section>
    <!-- /.content -->

@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {

            $('#delivery_partners_table').DataTable({
                orders: ['0', 'desc'],
            });
        });

        // function to add delivery partner
        function addPartner() {
            // initials data
            let name = document.querySelector('#name');
            let contact = document.querySelector('#contact');
            let address = document.querySelector('#address');
            let charge_0 = document.querySelector('#charge_0');
            let charge_1 = document.querySelector('#charge_1');
            let customer_portal = document.querySelector('#customer_portal');

            // ajax request
            $.ajax({
                url: '/delivery-partners/create',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name.value,
                    contact: contact.value,
                    address: address.value,
                    charge_0: charge_0.value,
                    charge_1: charge_1.value,
                    customer_portal: customer_portal.value,
                },
                success: (response) => {
                    window.location.href = window.location.href;
                }
            });
        }
    </script>
@endsection
