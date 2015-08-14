@extends('layouts.main')

@section('content')
    <br><br><br>
    @if (Session::has('flash_notification.message'))
        <div class="alert alert-{{ Session::get('flash_notification.level') }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ Session::get('flash_notification.message') }}
        </div>
    @endif
    @if (Session::has('errors'))

        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <ul>
                @foreach(Session::get('errors')->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (Session::has('paypalErrors'))

        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <ul>
                @foreach(Session::get('paypalErrors')->details as $error)
                    <li>{{ ucfirst(str_replace('_',' ',str_replace('payer.funding_instruments[0].credit_card.','',$error->field))." ".$error->issue) }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h3 contenteditable="true" class="text-primary">Subscription</h3>
    <div class="row">
        <div class="col-md-6">
            {!! Form::open(['url'=>'subscribe/'.$plan->id,'role'=>'form']) !!}
            <div class="form-group">
                <label for="type">type</label>
                {!! Form::select('type',['visa'=>'visa','mastercard'=>'mastercard','discover'=>'discover','amex'=>'amex'],null,['class'=>'form-control']) !!}
            </div>
            <div class="form-group">
                <label for="credit_card_number">Credit Card Cumber</label>
                {!! Form::text('credit_card_number',null,['class'=>'form-control']) !!}
            </div>
            <div class="form-group">
                <label for="expiration_month">Expiration month</label>
                {!! Form::text('expiration_month',null,['class'=>'form-control']) !!}
            </div>
            <div class="form-group">
                <label for="expiration_year">Expiration year</label>
                {!! Form::text('expiration_year',null,['class'=>'form-control']) !!}
            </div>
            <div class="form-group">
                <label for="cvv">CVV Number</label>
                {!! Form::text('cvv',null,['class'=>'form-control']) !!}
            </div>

            {!! Form::submit('Subscribe',['class'=>'btn btn-md btn-success']) !!} OR  {!! Form::submit('Subscribe using PAYPAL',['class'=>'btn btn-md btn-primary','name'=>'btnPaypal']) !!}
            {!! Form::close() !!}
        </div>
        <div class="col-md-6">

            <div class="col-md-12 bg-primary">
                <br>
                <div class="thumbnail">
                    <img alt="Bootstrap Thumbnail First" src="http://lorempixel.com/output/people-q-c-600-200-1.jpg">
                    <div class="caption">
                        <h3>
                            {{ $plan->title }}
                        </h3>
                        <p>
                            {{ $plan->description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection