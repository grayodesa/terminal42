(function($) {
	'use strict';

	/**
	 * Variables
	 */
	var $lineToggles = $("#mc4wp-graph-line-toggles :input");
	var $range = $(document.getElementById('mc4wp-graph-range'));
	var $customRangeOptions = $(document.getElementById('mc4wp-graph-custom-range-options'));
	var previousPoint;
	var $graph = $(document.getElementById('mc4wp-graph'));

	/**
	 * Functions
	 */
	function plotGraph() {
		var graphData = [];

		$lineToggles.filter(":checked").each(function() {
			graphData.push(mc4wp_statistics_data[$(this).val()]);
		});

		$.plot(
			$graph,
			graphData,
			{
				xaxis: {
					mode: "time",
					//min: startDate.getTime(),
					//max: endDate.getTime(),
					timeFormat: "%d/%b",
					minTickSize: mc4wp_statistics_settings.ticksize
				},
				yaxis: {
					min: 0, tickDecimals: 0
				},
				series: {
					lines: { show: true },
					points: { show: true }
				},
				grid: {
					hoverable: true
				}

			}
		);
	}

	function showTooltip(x, y, contents) {
		$('<div id="mc4wp-graph-tooltip">' + contents + '</div>').css( {
			position: 'absolute',
			display: 'none',
			top: y + 5,
			left: x + 5,
			border: '1px solid #fdd',
			padding: '2px',
			'background-color': '#fee',
			opacity: 0.80
		}).appendTo("body").fadeIn(200);
	}

	function toggleCustomRangeOptions() {
		var show = $(this).val() === 'custom';
		$customRangeOptions.toggle( show );
	}

	function plotHover(event, pos, item) {
		$("#x").text(pos.x.toFixed(2));
		$("#y").text(pos.y.toFixed(2));

		if (item) {
			if (previousPoint != item.dataIndex) {
				previousPoint = item.dataIndex;
				$("#mc4wp-graph-tooltip").remove();

				var x = item.datapoint[0],
					y = item.datapoint[1];

				showTooltip( item.pageX, item.pageY, item.series.label + ': ' + y );
			}
		} else {
			$("#mc4wp-graph-tooltip").remove();
			previousPoint = null;
		}
	}

	/**
	 * Bind event handlers$graph
	 */
	$lineToggles.change(plotGraph);
	$range.change(toggleCustomRangeOptions);
	$graph.bind("plothover", plotHover);

	plotGraph();


})(jQuery);