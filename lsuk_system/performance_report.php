<?php
// include "userhaspage.php";
// SysPermiss::UserHasPage(__FILE__);
// if (session_id() == '' || !isset($_SESSION)) {
// 	session_start();
// }
echo "<center><br><br><br><br><br><br><h1>We are out for some upgrades! Will be back soon</h1><center>";die;
$today = new DateTime(date('Y-m-d'));
$can_celebrate = date('H') >= 13 ? 1 : 0;
include 'actions.php';
$get_users = $obj->read_all("*", "login", "user_status=1 AND prv='Operator' AND is_allocation_member=1");
?>

<!doctype html>
<html lang="en">

<head>
	<title>Performance Report</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<style>
		.contain {
			height: 600px;
			overflow: hidden;
		}

		.table_summary thead th {
			position: sticky;
			top: -1px;
			background: #2c6fbb;
			border-top: 0px;
			color: white;
		}

		.table_summary {
			font-size: 15px;
			text-align: center;
		}

		.table_summary th {
			background-color: blue;
		}

		.carousel-indicators li {
			border: 1px solid;
		}

		.label {
			font-size: 15px;
		}

		.traffic-light {
			width: 50px;
			align-items: center;
		}

		.light {
			width: 35px;
			margin-top: -2px;
			margin-left: 18px;
			height: 35px;
			border-radius: 50%;
			box-shadow: 0 0 10px #fff;
			animation-duration: 1s;
			animation-timing-function: ease-in-out;
			animation-iteration-count: infinite;
		}

		.red {
			background-color: red;
			animation-name: blink-red;
			border: 1px solid lightgray;
		}

		.yellow {
			background-color: yellow;
			animation-name: blink-yellow;
			border: 1px solid lightgray;
		}

		.green {
			background-color: green;
			animation-name: blink-green;
			border: 1px solid lightgray;
		}

		@keyframes blink-red {
			0% {
				box-shadow: 0 0 10px red;
			}

			50% {
				background-color: transparent;
			}

			100% {
				box-shadow: 0 0 10px red;
			}
		}

		/* @keyframes blink-yellow {
			0% {
				box-shadow: 0 0 10px yellow;
			}
			50% {
				background-color: transparent;
			}
			100% {
				box-shadow: 0 0 10px yellow;
			}
		}

		@keyframes blink-green {
			0% {
				box-shadow: 0 0 10px green;
			}
			50% {
				box-shadow: none;
			}
			100% {
				box-shadow: 0 0 10px green;
			}
		} */
	</style>
</head>

<body id="body">
	<section class="container-fluid">
		<div class="notification_modal hidden">
			<div class="row">
					<div class="bg-danger" style="font-size: 20px;padding: 2px;">
						<span class="notification_modal_body text-center"></span>
					</div>
			</div>
		</div>
		<div id="myCarousel" class="carousel slide" data-ride="carousel">
			<!-- Indicators -->
			<ol class="carousel-indicators hidden">
				<?php for ($i = 0; $i < $get_users->num_rows; $i++) { ?>
					<li data-target="#myCarousel" data-slide-to="<?= $i ?>" <?= $i == 0 ? 'class="active"' : '' ?>></li>
				<?php } ?>
			</ol>
			<!-- Wrapper for slides -->
			<div class="carousel-inner">
				<?php $user_counter = 0;
				while ($row_user = $get_users->fetch_assoc()) { ?>
					<div class="item <?= $user_counter == 0 ? 'active' : '' ?>" data-user-id="<?= $row_user['id'] ?>" data-text="<?= ucwords($row_user['name']) ?>" data-celebrate="<?= $row_user['celebrate'] ?>">
						<div class="col-md-12">
							<h4>Summary : <b><?= ucwords($row_user['name']) ?></b> <span class="pull-right">Priority List (Not Yet Allocated) : <b><?= ucwords($row_user['name']) ?></b></span></h4>
						</div>
						<div class="col-md-5 contain table-responsive" style="padding: 0px;">
							<table class="table table-bordered table_summary" cellspacing="0" width="100%">
								<thead class="bg-primary">
									<tr>
										<th width="22%">Date</th>
										<th>Assigned</th>
										<th>Allocated</th>
										<th>Non-Allocated</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody class="tbody_summary">
									<?php $get_summary = $obj->read_all("DATE(created_date) AS date, COUNT(*) AS jobs", "assigned_jobs_users", "user_id = " . $row_user['id'] . " AND assigned_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) GROUP BY DATE(assigned_date)");
									$array_summary = array();
									while ($row = $get_summary->fetch_assoc()) {
										$array_summary[] = $row;
									}
									$array_summary = count($array_summary) ? array_column($array_summary, 'jobs', 'date') : array();
									$lastDayOfMonth = new DateTime(); // today's date
									$firstDayOfMonth = (new DateTime())->modify('-10 days'); // 10 days ago

									$currentDate = clone $lastDayOfMonth; // start with the current date
									while ($currentDate >= $firstDayOfMonth) { // reverse loop
										$formattedDate = $currentDate->format('Y-m-d');
										$class = $formattedDate == $lastDayOfMonth->format('Y-m-d') ? 'style="border-bottom:2px solid;background:lightgreen;font-weight: bold;"' : '';

										if (count($array_summary) && in_array($formattedDate, array_keys($array_summary))) {
											$get_allocated = $obj->read_specific("COUNT(*) AS allocated", "daily_logs", "action_id=5 AND DATE(dated) = '" . $formattedDate . "' AND user_id=" . $row_user['id'])['allocated'];
											$net_result = ($array_summary[$formattedDate] - $get_allocated);
											$net_result = $net_result 	< 0 ? 0 : $net_result;
											$bad_result = $net_result > 0 ? 'class="bg-danger"' : '';
											$result_text = $net_result > 0 ? '<span class="label label-danger">' . $net_result . '</span>' : $net_result;
											if ($net_result == 0) {
												$status_label = "<span class='label label-success'>Excellent<span>";
											} else {
												if ($net_result < ($array_summary[$formattedDate] / 2)) {
													$status_label = "<span class='label label-primary'>Good<span>";
												} else {
													$status_label = "<span class='label label-danger'>Attention!<span>";
												}
											}
											echo "<tr $class $bad_result>
													<td>" . $misc->dated($formattedDate) . "</td>
													<td><b>" . $array_summary[$formattedDate] . "</b></td>
													<td>" . $get_allocated . "</td>
													<td>" . $result_text . "</td>
													<td>" . $status_label . "</td>";
										} else {
											echo "<tr $class><td>" . $misc->dated($formattedDate) . "</td><td>0</td><td>0</td><td>0</td><td>Ok</td>";
										}
										echo "</tr>";
										$currentDate->modify('-1 day'); // go to the previous day
									} ?>
								</tbody>
							</table>
						</div>
						<div class="col-md-7 contain table-responsive">
							<!-- <div class="col-md-12">
								<h4></h4>
							</div> -->
							<table class="table table-bordered table_summary" cellspacing="0" width="100%">
								<thead class="bg-primary">
									<tr>
										<th>Job ID</th>
										<th>Assigned On</th>
										<th>Duration</th>
										<th>Type</th>
										<th>Language</th>
										<th>Priority</th>
									</tr>
								</thead>
								<tbody class="tbody_summary">
									<?php $get_priority = $obj->read_all("a.*,i.source,i.assignDate", 
									"assigned_jobs_users a JOIN interpreter i ON a.order_id = i.id AND a.order_type = 1", 
									"i.intrpName = '' AND i.order_cancel_flag=0 AND i.deleted_flag=0 AND a.user_id = " . $row_user['id'] . "
									UNION
									SELECT a.*,tp.source,tp.assignDate FROM assigned_jobs_users a JOIN telephone tp ON a.order_id = tp.id AND a.order_type = 2 WHERE tp.intrpName = '' AND tp.order_cancel_flag=0 AND tp.deleted_flag=0 AND a.user_id = " . $row_user['id'] . "
									UNION
									SELECT a.*,tr.source,tr.asignDate as assignDate FROM assigned_jobs_users a JOIN translation tr ON a.order_id = tr.id AND a.order_type = 3 WHERE tr.intrpName = '' AND tr.order_cancel_flag=0 AND tr.deleted_flag=0 AND a.user_id = " . $row_user['id']);
									if ($get_priority->num_rows > 0) {
										$array_types = array(1 => "<span class='label label-success' style='font-size: 100%;'>Face To Face</span>", 2 => "<span class='label label-primary' style='font-size: 100%;'>Telephone</span>", 3 => "<span class='label label-info' style='font-size: 100%;'>Translation</span>");
										$un_allocated_counter = 0;
										while ($row_priority = $get_priority->fetch_assoc()) {
											if (empty($row_priority['source'])) {
												continue;
											}
											$un_allocated_counter++;
											$date1 = new DateTime($row_priority['assignDate']);
											$interval = $date1->diff($today);
											$days = $interval->days;
											if ($days < 3) {
												$priority = '<div class="traffic-light"><div class="light red"></div></div>';
											} elseif ($days >= 3 && $days < 4) {
												$priority = '<div class="traffic-light"><div class="light yellow"></div></div>';
											} else {
												$priority = '<div class="traffic-light"><div class="light green"></div></div>';
											}
											echo "<tr>
												<td>" . $row_priority['order_id'] .  "</td>
												<td>" . $misc->dated($row_priority['assigned_date']) . "</td>
												<td><span class='btn_speak'>" . $misc->time_elapsed_string($row_priority['assigned_date']) .  "</span></td>
												<td>" . $array_types[$row_priority['order_type']] . "</td>
												<td>" . $row_priority['source'] . "</td>
												<td>" . $priority . "</td>
												</tr>";
										}
										if ($un_allocated_counter == 0) {
											echo "<tr><td class='text-danger' align='center' colspan='6'><h4><b>" . ucwords($row_user['name']) . " don't has any non-allocated job in the list</b></h4></td></tr>";
										}
									} else {
										echo "<tr><td class='text-danger' align='center' colspan='6'><h4><b>" . ucwords($row_user['name']) . " don't has any non-allocated job in the list</b></h4></td></tr>";
									} ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php $user_counter++;
				} ?>
			</div>
		</div>
	</section>
</body>
<script src="https://cdn.jsdelivr.net/npm/party-js@latest/bundle/party.min.js"></script>
<script>
	$(document).ready(function() {
		var notificationSound = new Audio('img/clapping.wav');
		var synth = window.speechSynthesis;
		var utterance = new SpeechSynthesisUtterance();
		utterance.lang = 'en-US';

		jQuery.fn.extend({
			div_scroll: function() {
				$(this).each(function() {
					var _this = $(this);
					var ul = _this.find(".table_summary");
					var li = ul.find(".tbody_summary");
					var w = li.size() * li.outerHeight();
					// li.clone().prependTo(ul);
					var i = 1,
						l;
					_this.hover(function() {
						i = 0
					}, function() {
						i = 1
					});

					function autoScroll() {
						setTimeout(function() {
							l = _this.scrollTop();
							if (l >= w) {
								_this.scrollTop(0);
							} else {
								_this.scrollTop(l + i);
							}
						}, 3000);

					}
					var scrolling = setInterval(autoScroll, 60);
					_this.find(".table_summary").scrollTop(0);
				})
			}
		});

		$('.item:visible').find('.contain').div_scroll();

		// Activate the carousel
		$("#myCarousel").carousel({
			interval: 40000, // Set the interval to 40 seconds (40000 milliseconds)
			pause: 'false'
		});

		var count_slides = <?= $get_users->num_rows ?>;
		var counter = 0;
		var celebrate_already = 0;
		var celebrate_default = $(document).find('.item.active').attr('data-celebrate');
		var operatorName = $(document).find('.item.active').attr('data-text');
		
		if (celebrate_default == 1) {
			if (<?=$can_celebrate ?>) {
				update_celebration($(document).find('.item.active').attr('data-user-id'));
				party_celebrate();
				//Let's clap after voice over speaking completed
				celebrate_already = 1;
				utterance.text = celebrate_default == 1 ? operatorName + " has done great job. Thank you" + operatorName : "";
				synth.speak(utterance);
				notificationSound.play();
			}
		}

		$("#myCarousel").on("slid.bs.carousel", function() {
			counter++;
			$('.item:visible').find('.contain').div_scroll();
			if (celebrate_already == 0) {
				var celebrate = $(document).find('.item.active').next('.item').attr('data-celebrate');
				var operatorName = $(document).find('.item.active').next('.item').attr('data-text');
				if (celebrate == 1) {
					if (<?=$can_celebrate ?>) {
						update_celebration($(document).find('.item.active').next('.item').attr('data-user-id'));
						party_celebrate();
						//Let's clap after voice over speaking completed
						celebrate_already = 1;
						utterance.text = celebrate == 1 ? operatorName + " has done great job. Thank you " + operatorName : "";
						synth.speak(utterance);
						notificationSound.play();
					}
				}
			}

			if (counter == count_slides) {
				location.reload();
			} else {
				var currentDate = new Date();
      			var currentMinute = currentDate.getMinutes();
      			var currentSecond = currentDate.getSeconds();
				if (currentMinute % 5 === 0) {
					$.ajax({
						url: 'ajax_add_interp_data.php',
						type: 'POST',
						dataType: 'json',
						data: {
							get_non_connected_calls: 1
						},
						success: function(response) {
							if (response['calls_count'] > 0) {
								var notify_text = "We have " + response['calls_count'] + " calls to connect in upcoming 15 minutes. Please check booking list. Thank you";
								$('.notification_modal_body').html("<h3 style='line-height:1.5' class='text-danger text-center'><b>Important! LSUK has <span class='label label-danger' style='font-size:18px'>" + response['calls_count'] + "</span> calls to connect in upcoming 15 minutes. Please check booking list. Thank you</b></h3>");
								$('.notification_modal').removeClass("hidden");
								utterance.text = notify_text;
								synth.speak(utterance);
								setTimeout(function() {
									$('.notification_modal').addClass("hidden");
								}, 5000);
							}
						},
						error: function() {
							console.log("Error fetching data!");
						}
					});
				}
			}
		});

	});

	function update_celebration(user_id) {
		$.ajax({
			url: 'ajax_add_interp_data.php',
			type: 'POST',
			dataType: 'json',
			data: {
				update_celebration: user_id
			},
			success: function(data) {
				if (data['status'] == 1) {
					$('.item[data-user-id="' + user_id + '"]').attr("data-celebrate", 0);
				}
			},
			error: function() {
				console.log("Error fetching data!");
			}
		});
	}

	function party_celebrate() {
		var counter_confetti = 50;
		setInterval(function() {
			counter_confetti--;
			if (counter_confetti > 0) {
				party.confetti(document.getElementById('body'), {
					angle: 90,
					spread: 50,
					startVelocity: 50,
					particleCount: 5 * counter_confetti,
					size: 2,
				});
			}
		}, 100);
	}
</script>

</html>