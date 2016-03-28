@extends('layouts.graph')

@section('content')
    <div class="jumbotron meterpost-background">
        <div class="container">
            <h1>{{ $dataseries->name }} <small>({{ $dataseries->label }})</small></h1>
            <p>{{ $dataseries->description }}</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="breadcrumbs">
                <ul>
                    <li><a href="{{ URL::action('DashboardController@showDashboard') }}">{{ trans('messages.dashboard') }}</a></li>
                    <li>{{ $dataseries->name }}</li>
                </ul>
            </div>
        </div>

        <div class="row">
            <ul class="nav nav-pills graph-content-buttons">
                <li role="presentation" class="active">
                    <a href="{{ URL::action('dataseries.reading.index', $dataseries->id) }}" class="graph-content-selector">{{ trans('messages.time.series') }}</a>
                </li>
                <li role="presentation">
                    <a href="{{ URL::action('ReadingController@getDataseriesAverages', $dataseries->id) }}" class="graph-content-selector">{{ trans('messages.daily.averages') }}</a>
                </li>
            </ul>
            <a class="btn btn-primary pull-right hidden-xs" href="{{ URL::action('ReadingController@getDataseriesAsCsv', $dataseries->id) }}">{{ trans('messages.download.dataseries.csv') }}</a>
        </div>

        @if (!isset($dataseries))
            <div class="row">
                <p>{{ trans('messages.no.data') }}</p>
            </div>
        @else
            <div class="row">
                <div id="chart" class="dataseries-chart">
                    <svg></svg>
                </div>
            </div>
        @endif
        </div>
@stop

@section('scripts')
    <script>
        var yAxisMinimum = parseFloat('{{ $dataseries->min_value }}');
        var yAxisMaximum = parseFloat('{{ $dataseries->max_value }}');
        var chart = null;

        var changeGraphContent = function(event) {
            event.preventDefault();

            var resourceUrl = $(this).attr('href');
            setChartData(chart, resourceUrl);

            $('.graph-content-buttons li').removeClass('active');
            $(this).closest('li').addClass('active');
        };

        var setChartData = function(chart, resourceUrl) {
            $.getJSON(resourceUrl, function(data) {
                var datum = [{
                    values: data.readings,
                    key: '{{ $dataseries->name }} ({{ $dataseries->label }})',
                    color: '#ff7f0e'
                }];

                d3.select('#chart svg')
                        .datum(datum)
                        .call(chart);

                nv.utils.windowResize(function() { chart.update() });
            });
        };

        var createChart = function() {
            chart = nv.models.lineChart()
                    .noData('{{ trans("messages.no.data") }}')
                    .height(460)
                    .margin({'left': 70, 'right': 60})
                    .useInteractiveGuideline(true)
                    .showLegend(true)
                    .showYAxis(true)
                    .showXAxis(true)
                    .forceY([yAxisMinimum, yAxisMaximum]);

            chart.xAxis
                    .axisLabel('{{ trans("messages.reading.time") }}')
                    .axisLabelDistance(15)
                    .tickFormat(function(stamp) {
                        var format = d3.time.format('%Y-%m-%d %X');
                        if (typeof(stamp) === 'string') {
                            stamp = parseInt(stamp);
                        }
                        return format(new Date(stamp));
                    })
                    .staggerLabels(true);

            chart.yAxis
                    .axisLabel('{{ $dataseries->name }} ({{ $dataseries->label }})')
                    .tickFormat(d3.format('.02f'));

            setChartData(chart, "{{ URL::action('dataseries.reading.index', $dataseries->id) }}");

            return chart;
        };

        chart = createChart();

        $('.graph-content-selector').click(changeGraphContent);

    </script>
@stop
