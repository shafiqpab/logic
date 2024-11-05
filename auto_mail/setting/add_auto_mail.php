<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Mail Settings</title>
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='0'>
	<meta http-equiv='pragma' content='no-cache'>
</head>

<body>



	<style>
		#data_grid a {
			font-size: 10px;
			padding: 1px 3px;
			color: #fff;
			text-decoration: none;
			border-radius: 3px;
		}

		#data_grid a:nth-child(even) {
			background-color: #FFA500;
		}

		#data_grid a:nth-child(odd) {
			background-color: green;
		}

		hr {
			border: 0;
			height: 1px;
			background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, .75), rgba(0, 0, 0, 0));
		}

		body {
			margin: 0;
		}


		.sticky {
			position: -webkit-sticky;
			/* Safari */
			position: sticky;
			top: 0;
			background-color: #FFF;
		}
	</style>

	<?php
	/*
	Design & Developed by : Saidul Reza

	*/

	require_once('../../includes/common.php');


	$tnaFileArr = array(
		0 => "tna/knit_tna_processed_mail.php",
		'0-1' => "tna/woven_tna_processed_mail.php",
		'0-2' => "tna/textile_tna_processed_mail.php",
		'0-3' => "tna/sweater_tna_processed_mai.php",
		'0-4' => "tna/lingerie_tna_processed_mail.php",
		'0-5' => "tna/sweater_tna_processed_mail_style_wise.php",

		1 => "daily_order_entry_auto_mail.php",
		'1-1' => "daily_order_entry_auto_mail_v3.php",
		'1-2' => "daily_order_entry_auto_mail_v4.php",
		'1-3' => "daily_order_buyer_brand_level_auto_mail.php",
		2 => "tna_auto_mail.php",
		'2-1' => "total_activities_auto_mail.php",
		'2-2' => "sweater_sample_acknowledgement_mail_notification.php",
		'2-3' => "approval_auto_mail_on_submit.php",
		'2-4' => "total_activities_auto_mail-v2.php",
		5 => "booking_mail.php",
		7 => "order_revised_auto_mail.php",
		8 => "cancelled_order_auto_mail.php",
		9 => "subcontract_dyeing_auto_mail.php",
		11 => "approval/pre_costing_approval_controller_auto_mail.php",
		'11-1' => "pre_costing_approval_mail.php",
		12 => "low_margin_approved_orders.php",
		'12-1' => "low_margin_approved_orders_v2.php",
		13 => "below_5_percent_profitability_order_list_auto_mail.php",
		14 => "total_company_activities_auto_mail.php",
		'14-1' => "total_production_activities_auto_mail.php",
		15 => "price_quotation_approval_mail.php",
		'15-1' => "price_quotation_unapproved_mail.php",
		//16 => "fabric_receive_auto_mail.php",
		17 => "fabric_receive_auto_mail.php",
		18 => "daily_production_auto_mail.php",
		'18-1' => "login_history_monthly_report.php",
		'18-2' => "login_history_weekly_report.php",
		'18-3' => "mail_ecipient_group_report.php",
		'18-4' => "monthly_report_auto_mail.php",
		'18-6' => "daily_summary_report.php",
		'18-7' => "daily_summary_report_v2.php",
		'18-8' => "urmi_daily_production_auto_mail_v3.php",
		'18-9' => "production_auto_mail.php",
		'18-10' => "daily_summary_auto_mail.php",
		20 => "yarn_issue_pending_auto_mail.php",
		19 => "import_consignment_pending_mail.php",
		21 => "bill_of_lading_delay_auto_mail.php",
		'21-1' => "bill_of_lading_submission_delay_auto_mail.php",
		22 => "monthly_capacity_vs_booked_auto_mail.php",
		'22-1' => "monthly_capacity_vs_booked_auto_mail_v2.php",
		23 => "fabric_booking_revised_auto_mail.php",
		'23-1' => "fabric_booking_revised_auto_mail_v2.php",
		24 => "job_wise_pq_and_budget_cost_wn.php",
		26 => "export_lc_sc_sewater_mail_notification.php",
		27 => "pi_list_of_goods_shipment_pending_auto_mail.php",
		'27-1' => "shipment_pending_mail_notification.php",
		28 => "pending_pi_for_approval_auto_mail.php",
		29 => "sweater_sample_delivery_pending_mail_notification.php",
		'29-1' => "sweater_sample_delivery_pending_mail_notification_v2.php",
		30 => "sample_without_order_auto_mail.php",
		31 => "machine_summary_production.php",
		32 => "sweater_garments_pre_costing_bom_auto_mail.php",
		33 => "production/unit_wise_garments_production.php",
		'33-1' => "production/unit_wise_garments_production_v2.php",
		'33-2' => "production/unit_wise_garments_production_v3.php",
		'33-3' => "production/unit_wise_garments_production_report_auto_mail.php",
		'33-4' => "production/unit_wise_garments_production_v5.php",
		34 => "daily_order_update_auto_mail.php",
		36 => "lcsc_notification_auto_mail.php",
		'36-1' => "lcsc_notification_auto_mail.php",
		37 => "btb_margin_lc_auto_mail.php",
		38 => "order_list_without_yarn_booking_auto_mail.php",
		39 => "purchase_requisition_approval_auto_mail.php",
		40 => "woven/buyer_inquiry_woven_auto_mail.php",
		41 => "woven/sample_requisition_with_booking_auto_mail.php",
		42 => "woven/sample_requisition_acknowledge_auto_mail.php",
		43 => "woven/sample_requisition_acknowledge_auto_mail.php",
		45 => "export_ci_statement_auto_mail.php",
		56 => "re-order_label_item_report.php",
		57 => "production/daily_dyeing_prod_analysis_auto_mail.php",
		59 => "monthly_report_auto_mail.php",
		60 => "buyer_inspection_mail_notification.php",
		61 => "daily_erp_report_auto_mail.php",
		62 => "order_list_without_fabric_booking.php",
		'62_1' => "order_list_without_fabric_booking_islamgroup.php",
		64 => "no_fabric_booking_auto_mail.php",
		66 => "approval/yarn_work_order_approval_auto_mail.php",
		67 => "approval/dyes_chemical_wo_approval_auto_mail.php",
		69 => "monthly_capacity_vs_booked_auto_mail_as_tna_date.php",
		71 => "bank_liability_position_as_of_today_auto_mail.php",
		72 => "btb_margin_lc_amendment_auto_mail.php",
		74 => "daily_ex_factory_schedule_auto_mail.php",
		75 => "woven/daily_erp_report_auto_mail_woven.php",
		76 => "woven/total_activities_auto_mail_woven.php",
		79 => "first_inspection_alter_and_damage_percentage.php",
		80 => "approval/pre_cost_full_approved_auto_mail.php",
		81 => "approval/purchase_requisition_full_approved_auto_mail.php",
		82 => "daily_order_entry_auto_mail_by_working_company.php",
		83 => "production/daily_production_activities_FSO.php",
		84 => "mm/shipment_date_revise_auto_mail.php",
		85 => "approval/weekly_purchase_requisition_approval_auto_mail.php",
		86 => "daily_export_information.php",
		88 => "inventory/daily_yarn_stock_auto_mail.php",
		'88-1' => "inventory/daily_yarn_stock_auto_mail-v2.php",
		91 => "scheduled_shipment_reminder_report.php",
		92 => "yarn_cost_qty_change_auto_mail.php",
		93 => "approval/re_app_pending_in_pre_cost_atuo_mail.php",
		94 => "mm/order_staus_auto_mail.php",
		95 => "production/partial_color_qty_cutting_auto_mail.php",
		100 => "daily_shipment_date_wise_schedule_auto_mail.php",
		101 => "woven/style_wise_buyer_inquiry_woven_auto_mail.php",
		102 => "fabric_sales_order_received_auto_mail.php",
		103 => "erosion_entry_automail.php",
		104 => "reject_notification_automail.php",
		105 => "inventory/inventory_stock_ageing_report.php",
		106 => "production/cutting_ageing_report_auto_mail.php",
		107 => "commercial/export_proceed_realization_notification.php",
		108 => "woven/style_wise_buyer_inquiry_woven_weekly_auto_mail.php",
		109 => "commercial/acceptance_pending_notification.php",
		110 => "fabric_booking_auto_mail.php",
		111 => "inventory/daily_yarn_stock_source_wise_auto_mail.php",
		112 => "inventory/inventory_yarn_stock_ageing_report.php",
		113 => "total_activities_auto_mail_sweater.php",
		114 => "mm/order_wise_ex_factory_balance_qty.php",
		115 => "mm/woven_style_wise_shipment_pending.php",
		116 => "tna/tna_issue_raised_atuo_mail.php",
		117 => "db_expiry_notification.php",
		'116-1' => "tna/tna_issue_closed_atuo_mail.php",
		118 => "btb_forwarding_and_lc_open_auto_mail.php",
		119 => "production/hourly_production_monitoring_report.php",
		120 => "production/bundle_wise_sewing_input.php",
		121 => "production/daily_buyer_inspection.php",
		122 => "inventory/style_wise_finish_fabric_stock_auto_mail.php",
		123 => "production/factory_monthly_production_report_auto_mail.php",
		125 => "production/floor_wise_daily_rmg_production.php",
		124 => "commercial/deleted_pi_notification.php",
		126 => "commercial/deleted_btb_margin_lc_notification.php",
		127 => "inventory/general_store/closing_stock_report_controller.php",
		128 => "inventory/dyes_and_chemical_store/closing_stock_report_controller.php",
		129 => "inventory/grey_fabric_store/grey_fabric_stock_report_for_youth_controller.php",
		130 => "commercial/pi_approval_notification.php",
		133 => "mm/daily_order_update_history.php",
		136 => "shipment_pending_report_auto_mail.php",
		138 => "production/total_production_activity_report_sales_auto_mail.php",
		140 => "mm/order_entry_for_buying_house_knit_auto_mail.php",
		141 => "approval/bom_confirmation_before_approval_auto_mail.php",
		142 => "mm/sample_requisition_with_booking_woven_auto_mail.php",
		143 => "mm/sample_requisition_with_booking_knit_auto_mail.php",
		144 => "mm/order_insert_auto_mail_facility.php",
		146 => "production/last_day_ex_factory_status.php",
	);
 

	$version_file_arr = array(
		0 => "Knit TNA Processed Mail",
		'0-1' => "Woven TNA Processed Mail",
		'0-2' => "Textile TNA Processed Mail",
		'0-3' => "Sewater TNA Processed Mail",
		'0-4' => "Lingerie TNA Processed Mail",
		'0-5' => "Sweater TNA Processed Mail Style Wise",
		'62-1' => "Order list without fabric booking islamgroup",
		'33-1' => "Unit wise garments production v2",
		'33-2' => "Unit wise garments production v3",
		'33-3' => "Unit wise garments production v4",
		'33-4' => "Unit wise garments production v5",
		'29-1' => "Sweater sample delivery pending mail notification v2",
		'18-1' => "Login history monthly report",
		'18-2' => "Login history weekly report",
		'18-3' => "Mail ecipient group report",
		'18-4' => "Monthly report auto mail",
		'18-6' => "daily summary report",
		'18-7' => "daily summary report v2",
		'18-9' => "Production auto mail",
		'36-1' => "Lcsc notification auto mail",
		'21-1' => "bill of lading_submission delay auto mail",
		'22-1' => "Monthly capacity vs booked auto mail v2",
		'27-1' => "Shipment pending mail notification",
		'23-1' => "Fabric booking revised auto mail v2",
		'11-1' => "Pre costing approval mail",
		'15-1' => "Price quotation unapproved mail",
		'14-1' => "Total production activities auto mail",
		'2-1' => "Total activities auto mail",
		'2-2' => "Sweater sample acknowledgement mail notification",
		'2-3' => "Approval auto mail on submit",
		'2-4' => "Total activities auto mail v2",
		'1-1' => "Daily order entry auto mail v3",
		'1-2' => "Daily order entry auto mail v4",
		'1-3' => "Daily order entry buyer and brand lavel",
		'88-1' => "Daily yarn stock auto mail V2",
		'18-8' => "Urmi Daily Production Auto Mail V3",
		'116-1' => "Tna Issue Closed Notification",
		'18-10' => "Daily Summary Auto mail",
		'12-1' => "Below 5% Profitability Order V2"

	);
	$form_list_for_mail = $form_list_for_mail + $version_file_arr;


	$menual_mail_item_arr = array(37 => 37, 72 => 72, 65 => 65, 96 => 96, 41 => 41, 89 => 89, 78 => 78, 40 => 40, 87 => 87, 77 => 77, 36 => 36 , 134 => 134 , 58 => 58);


	ini_set('display_errors', 'Off');
	if ($_POST['project_url'] && $_POST['project_directory'] && count($_POST['auto_mail_name']) > 0) {
		$dataStr = '';

		file_put_contents('base_url.txt', $_POST['project_url']);


		$file_list_arr = array();
		foreach ($_POST['auto_mail_name'] as $file) {
			//$dataStr .= "wget " . $_POST['project_url'] . 'auto_mail/' . $file . "\n";

			$dataStr .= "wget " . $_POST['project_url'] . 'auto_mail/' . $file . "\n";
			$fileArr = explode('?', $file);
			$dataStr .= "rm -rf " . str_replace('\\', '/', $_POST['project_directory']) . "/" . $fileArr[0] . ".*\n\n";
			if (strstr($dataStr, '../')) {
				$dataStr = str_replace(array('../', 'auto_mail/'), '', $dataStr);
			}

			if ($file != '') {
				$file_list_arr[$file] = $file;
			}
		}


		$file_list_str = implode(',', $file_list_arr);
		$file_name = "../" . str_replace('sh', 'txt', $_POST['cron_file_name']);
		file_put_contents($file_name, $file_list_str);

		$file = "../" . $_POST['cron_file_name'];
		file_put_contents($file, $dataStr);


		$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header("location:" . $base_url);
	}

	//wget http://202.164.212.3/erp/auto_mail/tna_auto_mail.php
	//rm -rf /var/www/erp/auto_mail/tna_auto_mail.php.*


	?>

	<style>
		#data_grid td {
			border: 1px dashed #666;
			padding: 3px;

		}
	</style>

	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return confirm('Do you want to change corn file?')">

		<table width="100%" class="sticky">
			<tr>
				<td align="center">
					<?php
					list($host, $serverurl) = explode("/", $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

					$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . '://' . $host . '/' . $serverurl . '/';
					$baseURL = file_get_contents('base_url.txt');
					$url = ($baseURL) ? $baseURL : $url;
					?>
					<b>View Date:</b>
					<input type="date" id="generate_date" name="generate_date" value="">
					<b>File Directory:</b>
					<input type="text" name="project_directory" value="<?= str_replace(['/setting', '\setting'], '', dirname(__FILE__)); ?>" style="width:15%" placeholder="erp" required>
					<b>Url:</b>
					<input type="text" name="project_url" value="<?= $url; ?>" style="width:15%" placeholder="http://202.164.212.3/erp" required>
					<b> Cron File Name:</b>
					<select id="cron_file_name" name="cron_file_name" value="" required onchange="loadDoc(this.value)" style="width:15%">
						<?php
						$cron_file_name_arr = array();
						foreach (glob("../*.sh") as $file) {
							$file = str_replace("../", "", $file);
							echo "<option value='$file'>$file</option>";
							$cron_file_name_arr[$file] = $file;
						}
						?>
					</select>
					<input type="submit" style="cursor:pointer;" value="Update Cron File">

				</td>
			</tr>
			<tr>
				<td align="center">
					<hr>
				</td>
			</tr>
		</table>

		<table border="1" rules="all" align="center" id="data_grid">
			<tr>
				<?php

				asort($form_list_for_mail);
				$i = 0;
				foreach ($form_list_for_mail as $key => $tex) {
					if ($tnaFileArr[$key] == '') {
						continue;
					}
					if ($menual_mail_item_arr[$key] == '') {

						list($orginal_key) = explode('-', $key);
						$i++;
						if ($i == 1) {
							echo "<td valign='top'>";
						}

				?>
						<input type="checkbox" value="<?= $tnaFileArr[$key]; ?>" name="auto_mail_name[]" id="<?= $tnaFileArr[$key]; ?>" />
						<span style="font-size:10px;"><?= $tex; ?></span>
						<a href="../<?= $tnaFileArr[$key]; ?>" target="_blank" title="Key: <?= $key; ?>">Send</a>
						<a href="javascript:newDoc('../<?= $tnaFileArr[$key]; ?>')" title="Key: <?= $key; ?>, Mail Type: <?= $form_list_for_mail[$orginal_key]; ?>">View</a>
						<br />
				<?
						if ($i == 35) {
							echo "</td>";
							$i = 0;
						}
					}
				}
				?>
			</tr>
		</table>
	</form>

	<input type="checkbox" id="all_check_uncheck" onclick="all_check_uncheck()" /> All
	<br />
	<table border="1" rules="all" id="data_grid" align="left">
		<caption>Menual Mail List</caption>
		<tr>
			<?php

			$i = 0;
			foreach ($menual_mail_item_arr as $key => $tex) {
				$i++;
				list($orginal_key) = explode('-', $key);
				if ($i == 1) {
					echo "<td valign='top'>";
				}
			?>
				&#10003; <span style="font-size:10px;" title="Key: <?= $orginal_key; ?>"><?= $form_list_for_mail[$orginal_key]; ?></span>
				<br />
			<?
				if ($i == 5) {
					echo "</td>";
					$i = 0;
				}
			}
			?>
		</tr>
	</table>


	<table border="1" rules="all" id="data_grid" align="left" width="100%" style="margin-bottom:5px;">
		<caption>Selected Source Information</caption>
		<tr>
			<td>
				<?php
				foreach ($cron_file_name_arr as $fname) {
					echo "---------------------------- " . $fname . " ----------------------------<br>";
					$file = "../" . $fname;
					$myfile = fopen($file, "r") or die("Unable to open file!");
					while (!feof($myfile)) {
						echo fgets($myfile) . "<br>";
					}
					fclose($myfile);
				}
				?>
			</td>
		</tr>
	</table>







	<table width="100%" bgcolor="#CCC" cellpadding="2" style="border-bottom:3px solid green;border-top: 1px dashed #666;">
		<tr>
			<td style="font-size: 12px;" align="left"><a href="mail_config.php">GO TO MAIL CONFIGARATION &#187; </a></td>
			<td style="font-size: 12px;"><?= 'Current PHP Version: ' . phpversion(); ?></td>
			<td style="font-size: 10px;" align="right"><?= date('Y'); ?> © <a href="www.logicsoftbd.com/" target="_blank">Logic Software Limited</a> - Copyright All Rights Reserved</td>
		</tr>
	</table>




	<script>
		function loadDoc(file) {

			var uncheck = document.getElementsByTagName('input');
			for (var i = 0; i < uncheck.length; i++) {
				if (uncheck[i].type == 'checkbox') {
					uncheck[i].checked = false;
				}
			}

			var fileArr = file.split('.');
			const xhttp = new XMLHttpRequest();
			xhttp.onload = function() {
				var res = this.responseText;
				var resArr = res.split(',');
				for (var i = 0; i < resArr.length; i++) {
					document.getElementById(resArr[i]).checked = true;
				}
			}
			xhttp.open("GET", "../" + fileArr[0] + '.txt');
			xhttp.send();
		}
		loadDoc(document.getElementById('cron_file_name').value);



		function newDoc(url) {
			//let sep = (url.search(".php?")>0)?'&':'?';
			let sep = '?';
			url = url + sep + 'isview=1&view_date=' + document.getElementById('generate_date').value;
			window.open(url, '_blank');
		}


		function all_check_uncheck() {
			var uncheck = document.getElementsByTagName('input');
			for (var i = 0; i < uncheck.length; i++) {
				if (uncheck[i].type == 'checkbox') {
					uncheck[i].checked = document.getElementById('all_check_uncheck').checked;
				}
			}
		}
	</script>


</body>

</html>