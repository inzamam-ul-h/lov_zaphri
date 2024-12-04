@extends('frontend.layouts.guest')

@section('content')
<?php
$SITE_URL = env('APP_URL');
?>

<div class="ritekhela-subheader">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h1>Checkout</h1>

                <ul class="ritekhela-breadcrumb">

                    <li><a href="{{url('/home')}}">Home</a></li>

                    <li>Checkout</li>


                </ul>

            </div>

        </div>

    </div>

</div>


<div class="ritekhela-main-content">

    <div class="ritekhela-main-section ritekhela-fixture-list-full">

        <div class="container">

            <div class="row">

                <div class="col-md-12">
                    <div id="payment-box">
                        <form action="<?php echo $paypal_url; ?>" id="check_out_form" method="post" target="_top">
                            <input type='hidden' name='business' value='<?php echo $paypal_account; ?>'>
                            <input type='hidden' name='item_name' value='Payment for Booking of Sessions'>
                            <input type='hidden' name='item_number' value='<?php echo $payment_id; ?>'>
                            <input type='hidden' name='amount' value='<?php echo $total_payment; ?>'>
                            <input type='hidden' name='no_shipping' value='1'>
                            <input type='hidden' name='currency_code' value='USD'>
                            <input type='hidden' name='notify_url' value='<?php echo $SITE_URL; ?>/pay_notify'>
                            <input type='hidden' name='cancel_return' value='<?php echo $SITE_URL; ?>/pay_cancel'>
                            <input type='hidden' name='return' value='<?php echo $SITE_URL; ?>/manage/payments/history'>
                            <input type="hidden" name="cmd" value="_xclick">
                            <input type="submit" name="pay_now" id="pay_now" Value="Pay Now">
                        </form>
                    </div>
                    <script>
                        document.getElementById('check_out_form').submit();
                    </script>
                </div>
            </div>

        </div>

    </div>

</div>


@endsection