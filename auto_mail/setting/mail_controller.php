<?php
session_start();
$user_id = $_SESSION['logic_erp']['user_id'];
extract($_REQUEST);

date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');



if ($action == 'menual_mail_address_popup') 
{
	echo load_html_head_contents("Consumption Entry", "../../", 1, 1, $unicode, 1, '');
	?>
	<script>
		function js_set_value() 
		{
			var cc_mail = getMultiSelectData();
			var mail_address = myForm.txt_mail_address.value;
			
			if(cc_mail != undefined){cc_mail_arr = cc_mail.split(',');}
			if(mail_address != ''){mail_address_arr = mail_address.split(',');}

			if(cc_mail != undefined && mail_address != ''){mail_address_arr = mail_address_arr.concat(cc_mail_arr);}
			else if(cc_mail != undefined){mail_address_arr = cc_mail_arr;}

			//  console.log(mail_address);
			//  alert(mail_address)

			var flag = 1;
			var valid_mail_address_arr = Array();
			if (mail_address != '' || cc_mail != undefined) {
				for (i = 0; i < mail_address_arr.length; i++) {
					

					if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(mail_address_arr[i])) {
						flag = 1;
						valid_mail_address_arr.push(mail_address_arr[i]);
					} else {
						alert("Invalid Email.");
						flag = 0;
						return;
					}

				}

				$("#txt_mail_address").val(valid_mail_address_arr.join(','));
			}


			if (flag == 1) {
				parent.emailwindow.hide();
			}



		}

		function setAditionalMailAddress() {
			let settings = '<?= $extra_data; ?>';
			var returnDataStr = return_global_ajax_value(settings, 'get_additional_mail', '',
				"../../auto_mail/setting/mail_controller");
			$("#txt_mail_address").val(returnDataStr);
		}
	</script>


	</head>

	<body>
		<form id="myForm">
			<table width="98%">
				<tr>
					<td>
						<input ondblclick="setAditionalMailAddress()" type="text" id="txt_mail_address" class="text_boxes" style="width:475px" placeholder="Write/Double Click for Aditional Mail Address">
					</td>
				</tr>
				<tr>
					<td>
					 		
						<multi-input>
							<input list="email_ist" class="text_boxes" placeholder="CC:">	
							<datalist id="email_ist">
							<?
								$sqlResult=sql_select("select EMAIL_ADDRESS from USER_MAIL_ADDRESS group by EMAIL_ADDRESS");
								foreach($sqlResult as $row){
							?>
								<option value="<?= $row['EMAIL_ADDRESS'];?>"></option>
							<?
								}
							?>
							</datalist>
						</multi-input>
						
					</td>
				</tr>
				<tr>
					<td>
						<textarea id="txt_mail_body" class="text_boxes" style="height:140px; width:475px" placeholder="Add Mail Body"></textarea>
					</td>
				</tr>
				<tr>
					<td align="center"><input type="button" class="formbutton" value="Send" onClick="js_set_value()" style="width:120px;" /> </td>
				</tr>
			</table>
		</form>

		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script src="multi-input.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}



if ($action == 'create_mail_log') {

	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}

	list($mst_id, $is_log_update) = explode(',', $data);


	$sql = "select id,MST_ID,SENDER_ID,SEND_TIME,SEND_DATE,NUMBER_OF_SEND from MAIL_SEND_LOG where MST_ID='$mst_id' order by id DESC";
	$sql_result = sql_select($sql);
	$number_of_send = count($sql_result) + 1;

	$id = return_next_id("id", "MAIL_SEND_LOG", 1);
	$field_array = "id,MST_ID,SENDER_ID,SEND_TIME,SEND_DATE,NUMBER_OF_SEND";
	$data_array = "(" . $id . ",'" . $mst_id . "'," . $user_id . ",'" . $pc_date_time . "','" . date('d-M-Y') . "'," . $number_of_send . ")";
	if ($is_log_update == 1) {
		$rID = sql_insert("MAIL_SEND_LOG", $field_array, $data_array, 1);
	}

	if ($db_type == 0) {
		if ($rID) {
			mysql_query("COMMIT");
			echo "0**" . $id . "**" . $sql_result[0]['SEND_TIME'] . "**" . $sql_result[0]['NUMBER_OF_SEND'];
		} else {
			mysql_query("ROLLBACK");
			echo "10**" . $id . "**" . $sql_result[0]['SEND_TIME'] . "**" . $sql_result[0]['NUMBER_OF_SEND'];
		}
	}

	if ($db_type == 1 || $db_type == 2) {
		if ($rID) {
			oci_commit($con);
			echo "0**" . $id . "**" . $sql_result[0]['SEND_TIME'] . "**" . $sql_result[0]['NUMBER_OF_SEND'];
		} else {
			oci_rollback($con);
			echo "10**" . $id . "**" . $sql_result[0]['SEND_TIME'] . "**" . $sql_result[0]['NUMBER_OF_SEND'];
		}
	}

	disconnect($con);

	exit();
}

if ($action == "mail_template") {
	list($company, $mail_item) = explode('_', $data);
	$templateId = sql_select("select mail_template from mail_group_mst where mail_item=$mail_item and company_id=$company and mail_type=2 and  status_active=1 and is_deleted=0");
	echo $templateId[0]['MAIL_TEMPLATE'];
	exit();
}


if ($action == "get_additional_mail") {
	list($company, $mail_item, $template) = explode('_', $data);
	$mailArr = return_library_array("select c.ID,c.EMAIL_ADDRESS from mail_group_mst a,MAIL_GROUP_CHILD b,USER_MAIL_ADDRESS c where a.id=b.MAIL_GROUP_MST_ID and b.MAIL_USER_SETUP_ID=c.id and mail_item=$mail_item and a.company_id=$company and a.mail_type=5 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0  and  c.status_active=1 and c.is_deleted=0", "ID", "EMAIL_ADDRESS"); // and a.mail_template=$template
	echo implode(',', $mailArr);
	exit();
}



?>