@extends('layouts.master')

@section('header-scripts')
    {{ HTML::script('js/raphael-2.1.4.min.js') }}
    {{ HTML::script('js/justgage.js') }}
@stop

@section('content')
    <div class="jumbotron meterpost-background">
        <div class="container">
            <h1>{{ trans('messages.welcome.greeting') }}</h1>
            <p>{{ trans('messages.welcome.message') }}</p>
        </div>
    </div>
    <div class="container">
        @if (!isset($dashboardSummaries) || (count($dashboardSummaries) < 1))
            <p>{{ trans('messages.no.dataseries') }}</p>
        @else
            <div class="row">
            @for($i = 0; $i < count($dashboardSummaries); $i++)
                <div class="col-md-4">
                    <a href="dataseries/{{ $dashboardSummaries[$i]->id }}" class="text-center">
                        <div class="dataseries-gauge-container" style="min-width: 300px;">
                            <div class="gauge-container text-center">
                                <div id="gauge-{{ $dashboardSummaries[$i]->id }}" class="gauge text-center"
                                        data-value="{{ $dashboardSummaries[$i]->current_value }}"
                                        data-min="{{ $dashboardSummaries[$i]->min_value }}"
                                        data-max="{{ $dashboardSummaries[$i]->max_value }}"
                                        data-title="{{ $dashboardSummaries[$i]->name }}"
                                        data-label="{{ $dashboardSummaries[$i]->label }}">
                                </div>
                            </div>
                            <p class="dataseries-description">{{ $dashboardSummaries[$i]->description }}</p>
                        </div>
                    </a>
                </div>
                @if ((($i+1) % 3 === 0) || ($i === count($dashboardSummaries)-1))
                    </div>
                    <div class="row">
                @endif
            @endfor
            </div>
        @endif
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.gauge').each(function() {
                var elem = $(this);
                var gauge = new JustGage({
                    id: $(elem).attr('id')
                });
            });
        });
    </script>
@stop
