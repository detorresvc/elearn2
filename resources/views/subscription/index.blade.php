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

    <h3 contenteditable="true" class="text-primary">Subscription</h3>
    <div class="row">
        @foreach($plans as $plan)
            <div class="col-md-4">
                <div class="thumbnail">
                    <img alt="Bootstrap Thumbnail First" src="http://lorempixel.com/output/people-q-c-600-200-1.jpg">
                    <div class="caption">
                        <h3>
                            {{ $plan->title }}
                        </h3>
                        <p>
                            {{ $plan->description }}
                        </p>
                        <p>
                            <a class="btn btn-primary" href="{{ url('payment')."/".$plan->id }}">Subscribe</a>
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection