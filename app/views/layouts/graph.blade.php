@extends('layouts.master')

@section('header-links')
    {{ HTML::style('css/nv.d3.min.css') }}
@stop

@section('header-scripts')
    {{ HTML::script('js/d3.v3.min.js') }}
    {{ HTML::script('js/nv.d3.min.js') }}
@stop

