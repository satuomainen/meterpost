@extends('layouts.master')

@section('content')
    <div class="jumbotron meterpost-background">
        <div class="container">
            <h1>{{ trans('messages.error.title') }}</h1>
            <p>{{ trans('messages.error.not.found') }}</p>
        </div>
    </div>
    <div class="container">
        <div class="row text-center">
            <a class="btn btn-large btn-primary" href="{{ URL::action('DashboardController@showDashboard') }}">{{ trans('messages.to.dashboard') }}</a>
        </div>
    </div>
@stop
