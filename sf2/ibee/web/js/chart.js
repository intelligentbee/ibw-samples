$(function () {
	var chart;
	$(document).ready(function() {
		chart = new Highcharts.Chart({
			chart: {
				renderTo: 'container',
				type: 'column'
			},
			title: {
				text: 'Amount of stairs climbed over time'
			},
			xAxis: {
				type: 'datetime',
				title: {
					text: 'Date'
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Amount'
				}
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			series: [{
				showInLegend: false,        
				name: 'Amount',
				data: [
					{% for activity in activities %}
						[Date.UTC(  {{activity.date|date('Y')}},
									{{activity.date|date('m')-1}},
									{{activity.date|date('d')}}
								 ),
								{{activity.total}}], 
					{%endfor%}
				]
			}]
		});
	});
});