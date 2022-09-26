<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="icon" type="image/png" href="<?= base_url() ?>assets/img/favicon.png">
	<title>Care Equity Clinical Tool</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/materialize.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/sweetalert.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/jquery-ui.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/style.css?v=<?php echo time() ?>">

	<script type="text/javascript">
		var base_url = "<?php echo base_url() ?>";
	</script>
</head>

<body>
	<?php
	if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
		include('nav-admin.php');
	} else {
		include('nav-user.php');
	}
	?>
	<div class="report-add-area">
		<div class="container">
			<h3 class="area-title">Clinical Trials Reports</h3>
			<div class="report-input-row">
				<div class="report-input-col">
					<div class="report-input-col-row report-input-col-row--title">
						<div class="report-input-col-row-50">
							<input type="text" class="report-input" id="title" placeholder="+ Enter report title">
						</div>
						<div class="report-input-col-row-50">
							<input type="text" class="report-input" id="terms" placeholder="+ Enter search terms">
						</div>
					</div>
					<div class="report-input-col-row">
						<div class="report-input-col-row-50 report-input-col-row--country">
							<div class="report-input-col-row-50">
								<select class="report-input" id="study">
									<?php
									if (isset($studies)) {
										foreach ($studies as $study) {
									?>
											<option value="<?php echo $study['value'] ?>"><?php echo $study['text'] ?></option>
									<?php
										}
									}
									?>
								</select>
							</div>
							<div class="report-input-col-row-50">
								<select class="report-input" id="country">
									<?php
									if (isset($countries)) {
										foreach ($countries as $country) {
									?>
											<option value="<?php echo $country['value'] ?>"><?php echo $country['text'] ?></option>
									<?php
										}
									}
									?>
								</select>
							</div>
						</div>
						<div class="report-input-col-row-50 report-input-col-row--conditions">
							<input type="text" class="report-input" id="conditions" placeholder="+ Enter condition or disease">
						</div>

					</div>
				</div>
				<div class="report-btn-col">
					<span class="btn-main" id="report_add_btn">+ Add</span>
				</div>
			</div>
		</div>
	</div>
	<div class="report-list-area">
		<div class="container">
			<div class="report-list-head">
				<div class="report-list-head-sort">
					<span class="sort-btn" id="sort_btn1" sort="ASC">
						<i class="material-icons">sort</i>Sort by:
					</span>
					<select class="" id="sort">
						<option value="az">Sort AZ</option>
						<option value="newold">Sort New to Old</option>
						<option value="oldnew">Sort Old to New</option>
					</select>
				</div>
				<div class="report-list-head-search">
					<div class="search-input-wrap">
						<input type="text" placeholder="Search" id="report_search_input">
						<i class="search-input-icon material-icons">search</i>
					</div>
				</div>
			</div>
			<div class="report-list-body" id="report_list">
				<?php
				if (isset($reports)) {
					foreach ($reports as $report) {

						$data = array();
						$data['report'] = $report;

						$this->view('admin/template/report-template', $data);
					}
				}
				?>
			</div>

			<!-- Trigger the modal with a button -->
			<button type="button" class="btn btn-info btn-lg show_modal_btn" data-toggle="modal" data-target="#myModal">Open Modal</button>

			<!-- Modal -->
			<div id="myModal" class="modal fade" role="dialog">
				<div class="modal-dialog">

					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Modal Header</h4>
						</div>
						<div class="modal-body">
							<div class="row graph-songs_wrap">
								<div class="col s-12 m-6">
									<p class="showing_line_title">Number of Reports per Week</p>
									<div class="graph-songs"></div>
								</div>

							</div>

							<div class="row graph-songs_total_wrap">
								<div class="col s-12 m-6">
									<p class="showing_line_title">Number of Reports per Week</p>
									<div class="graph-songs_total"></div>
								</div>

							</div>

							<div class="showing_line_bottom_wrap">
								<div>Older</div>
								<div>Last week</div>
							</div>

							<div class="change_display_method_wrap">
								<div class="change_display_cumulative active">Total Reports</div>
								<div class="change_display_daily">Daily</div>
							</div>

							<div class="date_picker_wrap">
								<div class="modal_header">Select custom date range</div>

								<div>
									<?php $attributes = 'id="start_date" placeholder="Select Start Date"';
									echo form_input('start_date', set_value('start_date'), $attributes); ?>
									-
									<?php $attributes = 'id="last_date" placeholder="Select Last Date"';
									echo form_input('last_date', set_value('last_date'), $attributes); ?>
								</div>

							</div>

							<div class="set_third_days_btn">Last 30 Days</div>

							<div class="report_date-download-btn">
								<div class="modal_header">DOWNLOAD CSV</div>
								<div class="report-list-download-btn__icon-wrap"><i class="material-icons report_date-downlaod">file_download</i></div>
							</div>

						</div>

					</div>

				</div>
			</div>



			<!-- Trigger the modal with a button -->
			<button type="button" class="btn btn-info btn-lg show_popup_btn" data-toggle="modal" data-target="#myPopup">Open Modal</button>

			<!-- Modal -->
			<div id="myPopup" class="modal fade" role="dialog">
				<div class="modal-dialog">

					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="popup-title">Modal Header</h4>
						</div>
						<div class="modal-body">
							<div class="popup_updates_count">update</div>

							<table class="popup-table">
								<thead>
									<tr>
										<th style="width: 2%"></th>
										<th style="width: 10%">NCT number</th>
										<th style="width: 30%">Title</th>
										<th style="width: 45%">Description</th>
										<th style="width: 8%">Date</th>
										<th style="width: 5%"></th>
									</tr>
								</thead>

								<tbody class="popup_body">

								</tbody>

							</table>
						</div>

					</div>

				</div>
			</div>

		</div>
	</div>

</body>
<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/sweetalert.min.js"></script>


<!-- Bootstrap 3.3.6 -->
<script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery-ui.js"></script>

<script src="<?= base_url() ?>assets/js/app.js?v=<?php echo time() ?>"></script>

<script>
	// For example on how to intiate graphs, or if you want to mess around with the data / style of these graphs, check the bottom of this panel.

	(function($) {

		$.fn.graphiq = function(options) {

			// Default options
			var settings = $.extend({
				data: {},
				colorLine: "#000000",
				colorDot: "#000",
				colorXGrid: "#7f7f7f",
				colorYGrid: "#7f7f7f",
				colorLabels: "#000000",
				colorUnits: "#000000",
				colorRange: "#000000",
				colorFill: "#B4B4B4",
				colorDotStroke: "#000000",
				dotStrokeWeight: 0,
				fillOpacity: 0.25,
				rangeOpacity: 0.5,
				dotRadius: 3,
				lineWeight: 2,
				yLines: true,
				dots: true,
				xLines: true,
				xLineCount: 10,
				fill: true,
				height: 200,
				fluidParent: null
			}, options);

			var values = [];
			var entryDivision;
			var dataRange = settings.height + settings.dotRadius;
			var parent = this;
			var maxVal;
			var scaleFactor = settings.height / 100;
			var pathPoints = "";
			for (var key in settings.data) {
				values.push(settings.data[key]);
			}

			parent.append(
				'<div class="graphiq__graph-values"></div><div class="graphiq__graph-layout"><svg class="graphiq__graph" viewBox="0 0 960 ' + (settings.height + 10) + '" shape-rendering="geometricPrecision"><path fill="' + settings.colorFill + '" style="opacity: ' + settings.fillOpacity + '" class="graphiq__fill-path" d="" stroke-width="0" stroke="#000" fill="cyan"/></svg><div class="graphiq__graph-key"></div></div>'
			);
			if (settings.fluidParent) {
				this.closest(".col").css("overflow", "auto");
			}
			parent.addClass('graphiq');

			var graph = this.find(".graphiq__graph");

			// Get data from table
			for (var key in settings.data) {
				this.find(".graphiq__graph-key").append('<div class="key" style="color: ' + settings.colorLabels + '">' + key + "</div>");
			}

			maxVal = Math.max.apply(Math, values);


			this.find('.graphiq__graph-values').append('<span style="color: ' + settings.colorRange + '; opacity: ' + settings.rangeOpacity + '">' + maxVal + '</span><span style="color: ' + settings.colorRange + '; opacity: ' + settings.rangeOpacity + '" >0</span>');



			// Set even spacing in the graph depending on amount of data

			var width = parent.find(".graphiq__graph-layout").width();

			var window_width = $(window).width();
			if (window_width > 768) {
				width = 695;
			} else {
				//width = window_width - 55;
				width = 695;
			}


			if (settings.xLines) {
				unitLines(width, maxVal);
			}

			layoutGraph(width, true);

			$(window).on("resize", function() {
				pathPoints = "";
				width = parent.find(".graphiq__graph-layout").width();
				var window_width = $(window).width();
				if (window_width > 768) {
					width = 695;
				} else {
					//width = window_width - 55;
					width = 695;
				}

				layoutGraph(width, false);
			});

			// buildFillPath();

			function percentageOf(max, current) {
				return (current / max * 100) * scaleFactor;
			}

			function layoutGraph(width, initial) {
				graph.attr({
					viewBox: "0 0 " + width + " " + (settings.height + 10),
					width: width
				});
				entryDivision = width / (values.length - 1);
				getCoordinates(initial, entryDivision);
			}

			function getCoordinates(initial, entryDivision) {


				for (i = 0; i < values.length; i++) {

					var offset;

					if (i == 0) {
						offset = (settings.dotRadius + (settings.dotStrokeWeight)) + 1;
					} else if (i == values.length - 1) {
						offset = ((settings.dotRadius + (settings.dotStrokeWeight)) * -1) - 1;
					} else {
						offset = 0;
					}

					var lineOffset = i == values.length - 2 ? (settings.dotRadius + (settings.dotStrokeWeight)) / 2 : 0;

					let nextI = i + 1;
					let xAxis = (entryDivision * i) + offset;
					let xAxis2 = entryDivision * nextI;

					//console.log(offset);


					let yAxis = dataRange - percentageOf(maxVal, values[i]);

					let yAxis2 = dataRange - percentageOf(maxVal, values[nextI]);

					if (i == values.length - 1) {
						yAxis2 = yAxis;
						xAxis2 = xAxis;
					}

					pathPoints += " L " + xAxis + " " + yAxis;


					if (i == values.length - 1 && settings.fill) {
						buildFillPath(pathPoints);
					}

					if (initial) {

						if (settings.yLines) {

							$(document.createElementNS("http://www.w3.org/2000/svg", "line"))
								.attr({
									class: "graphiq__y-division",
									x1: xAxis,
									y1: yAxis,
									x2: xAxis,
									y2: settings.height + 5,
									stroke: settings.colorYGrid,
									"stroke-dasharray": "5 6",
									"stroke-width": 1
								})
								.prependTo(graph);

						}

						// Draw the line


						$(document.createElementNS("http://www.w3.org/2000/svg", "line"))
							.attr({
								class: "graphiq__line",
								x1: xAxis,
								y1: yAxis,
								x2: xAxis2 - lineOffset,
								y2: yAxis2 + (settings.dotStrokeWeight / 2),
								stroke: settings.colorLine,
								"stroke-width": settings.lineWeight,
								"vector-effect": "non-scaling-stroke"
							}).appendTo(graph);

						// Draw the circle


						$(document.createElementNS("http://www.w3.org/2000/svg", "circle"))
							.attr({
								class: "graphiq__graph-dot",
								cx: xAxis,
								cy: yAxis + (settings.dotStrokeWeight / 2),
								r: settings.dots ? settings.dotRadius : 0,
								fill: settings.colorDot,
								stroke: settings.colorDotStroke,
								"stroke-width": settings.dotStrokeWeight,
								"data-value": values[i],
								"vector-effect": "non-scaling-stroke",
								"data-date": Object.keys(settings.data)[i]
							})
							.appendTo(graph);


						// Resize instead of draw, used in resize
					} else {

						parent.find(".graphiq__graph-dot")
							.eq(i)
							.attr({
								cx: xAxis,
							});
						parent.find(".graphiq__line")
							.eq(i)
							.attr({
								x1: xAxis,
								x2: xAxis2 - lineOffset,
							});
						parent.find(".graphiq__y-division")
							.eq(values.length - i - 1)
							.attr({
								x1: xAxis,
								x2: xAxis
							});
						parent.find(".graphiq__x-line").each(function() {
							$(this).attr({
								x2: width
							});
						});
					}
				}
			}

			function buildFillPath(pathPoints) {

				parent.find('.graphiq__fill-path').attr("d", "M  " + (4 + settings.dotStrokeWeight) + " " + (settings.height + 5 + settings.dotStrokeWeight) + pathPoints + " L " + (width - settings.dotRadius - settings.dotStrokeWeight) + " " + (settings.height + 5 + settings.dotStrokeWeight))
			}

			function unitLines(width, maxVal) {
				// Draw the max line

				var iteration = 200 / (settings.xLineCount);


				for (i = 0; i < settings.xLineCount; i++) {

					$(document.createElementNS("http://www.w3.org/2000/svg", "line"))
						.attr({
							class: "graphiq__x-line",
							y1: iteration * i + (settings.dotRadius + settings.dotStrokeWeight),
							x2: width,
							y2: iteration * i + (settings.dotRadius + settings.dotStrokeWeight),
							stroke: settings.colorXGrid,
							// "stroke-dasharray": "5 6",
							"stroke-width": 1
						})
						.prependTo(graph);

				}

			}
			/*
						parent.hover(function() {

							var total_length = $(this).find('.graphiq__graph-dot').length;
							$(this).find('.graphiq__graph-dot').each(function(index) {
								

								if (parent.attr('class') == "graph-songs graphiq"){
									$('body').append('<span style="color: ' + settings.colorUnits + '" class="graphiq__value-dialog value-' + index + '">' + $(this).attr("data-value") + '</span>');
									$('.value-' + index).css({
										top: $(this).position().top - 20,
										left: $(this).position().left - ($('.value-' + index).outerWidth() / 2) + 3,
										"z-index": 9999
									})
								}else{
									if (index == 0 | index == total_length-1  ) {
										$('body').append('<span style="color: ' + settings.colorUnits + '" class="graphiq__value-dialog value-' + index + '">' + $(this).attr("data-value") + '</span>');
										$('.value-' + index).css({
											top: $(this).position().top - 20,
											left: $(this).position().left - ($('.value-' + index).outerWidth() / 2) + 3,
											"z-index": 9999
										})
									}
								}

								
							})
						}, function() {
							$('.graphiq__value-dialog').remove();
						})
			*/
			//dot hover with date
			$('.graphiq__graph-dot').hover(function() {

				$('body').append('<span style="color: ' + settings.colorUnits + '" class="graphiq__value-dialog value">' + $(this).attr("data-date") + " : " + $(this).attr("data-value") + '</span>');
				$('.value').css({
					top: $(this).position().top - 35,
					left: $(this).position().left - ($('.value').outerWidth() / 2) + 3,
					"z-index": 9999
				})


			}, function() {
				$('.graphiq__value-dialog').remove();
			})
		};

	}(jQuery));
</script>

</html>