<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id=$_SESSION['logic_erp']['user_id'];

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$subprocessForWashIn=implode(",",$subprocessForWashArr);

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if($action=="company_wise_report_button_setting")
{

	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=7 and report_id=305 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#btn_recipe_calc').hide();\n";
	echo "$('#btn_recipe_calc_5').hide();\n";
	echo "$('#btn_recipe_calc_4').hide();\n";
	echo "$('#btn_recipe_calc_9').hide();\n";
	echo "$('#btn_recipe_calc_10').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==66){echo "$('#btn_recipe_calc').show();\n";}
			if($id==129){echo "$('#btn_recipe_calc_5').show();\n";}
			if($id==137){echo "$('#btn_recipe_calc_4').show();\n";}
			if($id==235){echo "$('#btn_recipe_calc_9').show();\n";}
			if($id==274){echo "$('#btn_recipe_calc_10').show();\n";}
			
		}
	}
	else
	{
		echo "$('#btn_recipe_calc').show();\n";
		echo "$('#btn_recipe_calc_5').show();\n";
		echo "$('#btn_recipe_calc_4').show();\n";
		echo "$('#btn_recipe_calc_9').show();\n";
		echo "$('#btn_recipe_calc_10').show();\n";
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,2,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1);
	exit();
}

if ($action == "load_drop_machine")
{
	if($db_type==2)
	{
		echo create_drop_down( "cbo_machine_name", 144, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=2 and company_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );
		 
	}
	else if($db_type==0)
	{
		echo create_drop_down( "cbo_machine_name", 144, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and company_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
	}
}
if ($action == "incharge_name_popup") {
	echo load_html_head_contents("In Charge Name Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//echo $cbo_company_id.'SSSSSSS'; 
	?>
	<script>

		function js_set_value(id) {
			$('#incharge_hdn').val(id);
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" name="incharge_hdn" id="incharge_hdn" value=""/>
	<?
		
		 $sql = "select id, first_name from lib_employee where  company_id=$cbo_company_id and in_charge like '%5%' and status_active=1 and is_deleted=0 order by first_name";

	echo create_list_view("tbl_list_search", "In Charge Name", "320", "280", "150", 0, $sql, "js_set_value", "id,first_name", "", 1, "0", $arr, "first_name", "", 'setFilterGrid("tbl_list_search",-1);', '0', '', 0);
	exit();
}


if($action=="ratio_data_from_dtls")
{
	$ex_data=explode('**',$data);
	$company=$ex_data[0];
	$sub_process=$ex_data[1];
	$recipe_no=$ex_data[2];
	$update_id=$ex_data[3];
		//echo $sql_rec="select b.total_liquor from  pro_recipe_entry_mst a,pro_recipe_entry_dtls b  where a.id=b.mst_id and a.entry_form=59 and b.sub_process_id=$sub_process and b.liquor_ratio!=0  and a.id=$recipe_no";
			//if ($sub_process == 93 || $sub_process == 94 || $sub_process == 95 || $sub_process == 96 || $sub_process == 97 || $sub_process == 98 || $sub_process ==140 || $sub_process ==141 || $sub_process ==142 || $sub_process ==143 ) {
			if(in_array($sub_process, $subprocessForWashArr))
			{
				$ratio_cond="";
			}
			else
			{
				//$ratio_cond="and a.ratio>0";
			}
		$actual_total_liquor=return_field_value("b.total_liquor as total_liquor","pro_recipe_entry_mst a,pro_recipe_entry_dtls b","a.id=b.mst_id and a.entry_form=59 and b.sub_process_id=$sub_process and b.liquor_ratio!=0  and a.id=$recipe_no ","total_liquor");

		$mst_id=return_field_value("b.mst_id as mst_id","pro_recipe_entry_mst a,pro_recipe_entry_dtls b","a.id=b.mst_id and a.entry_form=60 and b.sub_process_id=$sub_process and b.liquor_ratio!=0 and a.id=$update_id ","mst_id");
		if($mst_id!=0)
		{
			 $sql_rec="select liquor_ratio,total_liquor,check_id from pro_recipe_entry_dtls where mst_id=".$mst_id." and sub_process_id=$sub_process and liquor_ratio!=0";
		}
		else
		{
			 $sql_rec="select liquor_ratio,total_liquor,check_id from pro_recipe_entry_dtls where mst_id=".$recipe_no." and sub_process_id=$sub_process ";
		}

	$result_dtl=sql_select($sql_rec);
	foreach ($result_dtl as $row)
	{
	//txt_total_liquor_ratio*txt_liquor_ratio_dtls
			if($row[csf("check_id")]==0 || $row[csf("check_id")]==2)  
			{
				$check_id=2;
				echo "document.getElementById('check_id').value= '" . $check_id . "';\n";
				echo "$('#check_id').attr('checked', false);\n";
				
			}
			else
			{
				$check_id=$row[csf("check_id")];
				 echo "document.getElementById('check_id').value= '" . $check_id . "';\n";
				echo "$('#check_id').attr('checked', true);\n";
			}
			
			echo "document.getElementById('txt_liquor_ratio_dtls').value 		= '".$row[csf("liquor_ratio")]."';\n";
			echo "document.getElementById('txt_total_liquor_ratio').value 			= '".$row[csf("total_liquor")]."';\n";
			echo "document.getElementById('hide_actual_liquor').value 		= '".$actual_total_liquor."';\n";
			echo "caculate_tot_liquor();\n";
	}
	if($update_id!='')
		{
			//echo "select max(a.sub_seq) as sub_seq from  pro_recipe_entry_dtls a where  a.mst_id=" . $update_id . "  and a.status_active=1 and a.is_deleted=0 $ratio_cond";
		$sub_seq_no =return_field_value("max(a.sub_seq) as sub_seq","pro_recipe_entry_dtls a"," a.mst_id=" . $update_id . "  and a.status_active=1 and a.is_deleted=0 $ratio_cond", "sub_seq");
		}
		$sub_seq=$sub_seq_no+1;
		echo "document.getElementById('txt_subprocess_seq').value 			= '" . $sub_seq . "';\n";
		

		$sub_seq_no =return_field_value("a.sub_seq as sub_seq","pro_recipe_entry_dtls a"," a.mst_id=" . $update_id . " and a.sub_process_id=$sub_process and a.status_active=1 and a.is_deleted=0 $ratio_cond", "sub_seq");
		//echo "select max(a.sub_seq) as sub_seq from  pro_recipe_entry_dtls a where  a.mst_id=" . $update_id . "  and a.sub_process_id=$sub_process and a.status_active=1 and a.is_deleted=0 $ratio_cond"; 
		if($sub_seq_no!="")
		{
		echo "document.getElementById('txt_subprocess_seq').value 			= '" . $sub_seq_no . "';\n";
		}

	exit();
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/chemical_dyes_receive_controller",$data);
}

if ($action=="recipe_popup")
{
	echo load_html_head_contents("Recipe No Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{
		//alert(id);
			$('#hidden_recipe_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center" style="width:100%;">
    <form name="searchfrm" id="searchfrm">
        <fieldset style="width:930px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="900" class="rpt_table">
                <thead>
                 	<tr>
                        <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th>Recipe Date Range</th>
                        <th>System ID</th>
                        <th>Batch No</th>
                        <th width="130">Labdip No</th>
                        <th width="130">Recipe Description</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" name="hidden_recipe_id" id="hidden_recipe_id" class="text_boxes" value="">
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
                    </td>
                    <td>
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_sysId" id="txt_search_sysId" placeholder="Search" />
                    </td>
                    <td>
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_batch_no" id="txt_search_batch_no" placeholder="Search" />
                    </td>
                    <td>
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_labdip" id="txt_search_labdip" placeholder="Search" />
                    </td>
                    <td>
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_recDes" id="txt_search_recDes" placeholder="Search" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_labdip').value+'_'+document.getElementById('txt_search_sysId').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_search_recDes').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $cbo_dyeing_re_process; ?>+'_'+document.getElementById('txt_search_batch_no').value, 'create_recipe_search_list_view', 'search_div', 'dyeing_re_process_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div style="width:100%; margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
    	</fieldset>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_recipe_search_list_view")
{
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');

	$data = explode("_",$data);
	$labdip=$data[0];
	$sysid=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$rec_des =trim($data[5]);
	$search_type =$data[6];
	$dyeing_re_process =$data[7];
	$batch_no =$data[8];
	if($start_date=="" && $end_date=="" && $batch_no=="" && $sysid=="" && $rec_des=="")
	{
		echo "<b>Please select any one from Search panel.</b>";die;
	}

	// if($batch_no=="")
	// {
	// 	$batch_name_arr=array(); $batch_ext_arr=array();
	// 	$batchData=sql_select( "select id, batch_no,booking_no, extention_no from pro_batch_create_mst where status_active=1");
	// 	foreach($batchData as $bRow)
	// 	{
	// 		$batch_name_arr[$bRow[csf('id')]]=$bRow[csf('batch_no')];
	// 		$batch_ext_arr[$bRow[csf('id')]]=$bRow[csf('extention_no')];
	// 		$batch_booking_arr[$bRow[csf('id')]]=$bRow[csf('booking_no')];
	// 	}
	// }

	if($start_date!="" && $end_date!="")
	{
		 if($db_type==0)
		 {
			$date_cond="and a.recipe_date between '".change_date_format(trim($start_date),"yyyy-mm-dd","-",1)."' and '".change_date_format(trim($end_date),"yyyy-mm-dd","-",1)."'";
		 }
		 else if($db_type==2)
		 {
			$date_cond="and a.recipe_date between '".change_date_format(trim($start_date),"mm-dd-yyyy","/",1)."' and '".change_date_format(trim($end_date),"mm-dd-yyyy","/",1)."'";
		 }
	}
	else
	{
		$date_cond="";
	}


	if($search_type==1)
	{
		if ($labdip!='') $labdip_cond=" and a.labdip_no='$labdip'"; else $labdip_cond="";
		if ($sysid!='') $sysid_cond=" and a.id=$sysid"; else $sysid_cond="";
		if ($rec_des!='') $rec_des_cond=" and a.recipe_description='$rec_des'"; else $rec_des_cond="";

		if($batch_no!="")
		{
			$batch_no_cond=" and b.batch_no='$batch_no'";

			$batch_name_arr=array(); $batch_ext_arr=array();
			//$batchData=sql_select( "select id, batch_no, extention_no from pro_batch_create_mst where status_active=1 and batch_no='$batch_no'");
				$batchData=sql_select("select max(a.id) as recipe_id,b.id, b.batch_no, b.extention_no,b.booking_no from pro_batch_create_mst b left join pro_recipe_entry_mst a on  a.batch_id=b.id   where b.status_active=1 and b.batch_no like '%$batch_no%'  group by  b.id,b.booking_no, b.batch_no, b.extention_no");
				//echo "select max(a.id) as recipe_id,b.id, b.batch_no, b.extention_no,b.booking_no from pro_batch_create_mst b left join pro_recipe_entry_mst a on  a.batch_id=b.id   where b.status_active=1 and b.batch_no like '%$batch_no%'  group by  b.id,b.booking_no, b.batch_no, b.extention_no";
			foreach($batchData as $bRow)
			{
				if($dyeing_re_process!=1)
				{
					$batch_id .= $bRow[csf('id')].",";
				}

				$batch_name_arr[$bRow[csf('id')]]=$bRow[csf('batch_no')];
				$batch_ext_arr[$bRow[csf('id')]]=$bRow[csf('extention_no')];
				$batch_booking_arr[$bRow[csf('id')]]=$bRow[csf('booking_no')];
				if($bRow[csf('recipe_id')]>0)
				{
					$last_recipe_arr[$bRow[csf('recipe_id')]]=$bRow[csf('recipe_id')];
					$last_recipe_arr2[$bRow[csf('batch_no')]]=$bRow[csf('recipe_id')];
					//echo $bRow[csf('recipe_id')].', ';
				}
			}
		}else{
			$batch_no_cond="";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($labdip!='') $labdip_cond=" and a.labdip_no like '%$labdip%'"; else $labdip_cond="";
		if ($sysid!='') $sysid_cond=" and a.id like '%$sysid%' "; else $sysid_cond="";
		if ($rec_des!='') $rec_des_cond=" and a.recipe_description like '%$rec_des%'"; else $rec_des_cond="";

		if($batch_no!="")
		{
			$batch_no_cond=" and b.batch_no like '%$batch_no%'";

			$batch_name_arr=array(); $batch_ext_arr=array();
			//$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where status_active=1 and batch_no like '%$batch_no%'");
			$batchData=sql_select("select max(a.id) as recipe_id,b.id, b.batch_no, b.extention_no,b.booking_no from pro_batch_create_mst b left join pro_recipe_entry_mst a on  a.batch_id=b.id   where b.status_active=1 and b.batch_no like '%$batch_no%'  group by  b.id,b.booking_no, b.batch_no, b.extention_no order by recipe_id desc");
			//echo "select max(a.id) as recipe_id,b.id, b.batch_no, b.extention_no from pro_batch_create_mst b left join pro_recipe_entry_mst a on  a.batch_id=b.id   where b.status_active=1 and b.batch_no like '%$batch_no%'  group by  b.id, b.batch_no, b.extention_no";

			foreach($batchData as $bRow)
			{
				if($dyeing_re_process!=1)
				{
					$batch_id .= $bRow[csf('id')].",";
				}

				$batch_name_arr[$bRow[csf('id')]]=$bRow[csf('batch_no')];
				$batch_ext_arr[$bRow[csf('id')]]=$bRow[csf('extention_no')];
				//echo $bRow[csf('id')].'DD';
				$batch_booking_arr[$bRow[csf('id')]]=$bRow[csf('booking_no')];
				if($bRow[csf('recipe_id')]>0)
				{
					//echo $bRow[csf('batch_no')].'='.$bRow[csf('extention_no')].'<br>';
					$last_recipe_arr[$bRow[csf('recipe_id')]]=$bRow[csf('recipe_id')];
					$last_recipe_arr2[$bRow[csf('batch_no')]]=$bRow[csf('recipe_id')];
					//echo $bRow[csf('recipe_id')].', ';

				}
			}
		}else {
			$batch_no_cond="";
		}

	}
	else if($search_type==2)
	{
		if ($labdip!='') $labdip_cond=" and a.labdip_no like '$labdip%'"; else $labdip_cond="";
		if ($sysid!='') $sysid_cond=" and a.id like '$sysid%' "; else $sysid_cond="";
		if ($rec_des!='') $rec_des_cond=" and a.recipe_description like '$rec_des%'"; else $rec_des_cond="";

		if($batch_no!="")
		{
			$batch_no_cond=" and b.batch_no like '$batch_no%'";

			$batch_name_arr=array(); $batch_ext_arr=array();
			//$batchData=sql_select( "select id, batch_no, extention_no from pro_batch_create_mst where status_active=1 and batch_no like '$batch_no%'");
			$batchData=sql_select("select max(a.id) as recipe_id,b.id, b.booking_no,b.batch_no, b.extention_no from pro_batch_create_mst b left join pro_recipe_entry_mst a on  a.batch_id=b.id   where b.status_active=1 and b.batch_no like '%$batch_no%'  group by  b.id,b.booking_no, b.batch_no, b.extention_no");
			foreach($batchData as $bRow)
			{
				if($dyeing_re_process!=1)
				{
					$batch_id .= $bRow[csf('id')].",";
				}
				$batch_name_arr[$bRow[csf('id')]]=$bRow[csf('batch_no')];
				$batch_ext_arr[$bRow[csf('batch_no')]]=$bRow[csf('extention_no')];
				$batch_booking_arr[$bRow[csf('id')]]=$bRow[csf('booking_no')];
				if($bRow[csf('recipe_id')]>0)
				{
					$last_recipe_arr[$bRow[csf('recipe_id')]]=$bRow[csf('recipe_id')];
					$last_recipe_arr2[$bRow[csf('batch_no')]]=$bRow[csf('recipe_id')];
					//echo $bRow[csf('recipe_id')].', ';
				}
			}
		}else {
			$batch_no_cond="";
		}
	}
	else if($search_type==3)
	{
		if ($labdip!='') $labdip_cond=" and a.labdip_no like '%$labdip'"; else $labdip_cond="";
		if ($sysid!='') $sysid_cond=" and a.id like '%$sysid' "; else $sysid_cond="";
		if ($rec_des!='') $rec_des_cond=" and a.recipe_description like '%$rec_des'"; else $rec_des_cond="";

		if($batch_no!="")
		{
			$batch_no_cond=" and b.batch_no like '%$batch_no'";

			$batch_name_arr=array(); $batch_ext_arr=array();
			//$batchData=sql_select( "select id, batch_no, extention_no from pro_batch_create_mst where status_active=1 and batch_no like '%$batch_no'");
			$batchData=sql_select("select max(a.id) as recipe_id,b.id, b.batch_no,b.booking_no, b.extention_no from pro_batch_create_mst b left join pro_recipe_entry_mst a on  a.batch_id=b.id   where b.status_active=1 and b.batch_no like '%$batch_no%'  group by  b.id,b.booking_no, b.batch_no, b.extention_no");
			//echo "select max(a.id) as recipe_id,b.id, b.batch_no,b.booking_no, b.extention_no from pro_batch_create_mst b left join pro_recipe_entry_mst a on  a.batch_id=b.id   where b.status_active=1 and b.batch_no like '%$batch_no%'  group by  b.id,b.booking_no, b.batch_no, b.extention_no";
			foreach($batchData as $bRow)
			{
				if($dyeing_re_process!=1)
				{
					$batch_id .= $bRow[csf('id')].",";
				}

				$batch_name_arr[$bRow[csf('id')]]=$bRow[csf('batch_no')];
				$batch_ext_arr[$bRow[csf('batch_no')]]=$bRow[csf('extention_no')];
				$batch_booking_arr[$bRow[csf('id')]]=$bRow[csf('booking_no')];
				if($bRow[csf('recipe_id')]>0)
				{
					$last_recipe_arr[$bRow[csf('recipe_id')]]=$bRow[csf('recipe_id')];
					$last_recipe_arr2[$bRow[csf('batch_no')]]=$bRow[csf('recipe_id')];
					//echo $bRow[csf('recipe_id')].', ';
				}
			}
		}else {
			$batch_no_cond="";
		}

	}

	if($company_id!=0) $com_cond="and a.working_company_id=$company_id";
	else $com_cond='';

	// if($dyeing_re_process==2)
	// {
	// 	$sql_dyeing="select batch_id,result from pro_fab_subprocess where  load_unload_id=2 and result in(1,11) and status_active=1 and is_deleted=0";
	// 	$result_dyeing=sql_select($sql_dyeing);
	// 	foreach($result_dyeing as $row)
	// 	{

	// 		$dyeing_shade_match_arr[$row[csf("batch_id")]]=$row[csf("result")];

	// 	}
	// }

	if($dyeing_re_process==1 || $dyeing_re_process==3) //For Prevoius Recipe
	{
		// $sql = "select a.id,a.working_company_id,a.company_id ,a.labdip_no, a.recipe_description, a.recipe_date, a.order_source, a.style_or_order, a.buyer_id, a.color_id, a.color_range, a.batch_id,b.batch_no,b.extention_no,b.booking_no from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id   and a.entry_form=59 and a.status_active=1 and a.is_deleted=0 $com_cond $labdip_cond $sysid_cond $rec_des_cond $date_cond $batch_no_cond order by a.id DESC";
	 $sql = "select a.id,a.working_company_id,a.company_id ,a.labdip_no, a.recipe_description, a.recipe_date, a.order_source, a.style_or_order, a.buyer_id, b.color_id, b.color_range_id, b.re_dyeing_from as batch_id,b.batch_no,b.extention_no from  pro_batch_create_mst b left join pro_recipe_entry_mst a on b.id=a.batch_id and  a.entry_form=59 $com_cond $labdip_cond  where  b.status_active=1 and b.is_deleted=0  and b.extention_no>0  $sysid_cond $rec_des_cond $date_cond $batch_no_cond order by a.id DESC";
	}
	else
	{

		$batch_ids = chop($batch_id,',');
		if($batch_ids!="")
		{
			$batch_no_cond="and a.batch_id in($batch_ids)";
		}

		 $sql = "select a.id,a.working_company_id,a.company_id, a.labdip_no, a.recipe_description, a.recipe_date, a.order_source, a.style_or_order, a.buyer_id,a.batch_id, a.color_id, a.color_range, a.batch_id from pro_recipe_entry_mst a where  a.entry_form=59 and a.status_active=1 and a.is_deleted=0 $com_cond $labdip_cond $sysid_cond $rec_des_cond $date_cond $batch_no_cond order by a.id DESC";

	}
	// echo $sql; 
	$result=sql_select($sql);
	//echo $sql;

	//$arr=array(2=>$batch_name_arr,3=>$batch_ext_arr,6=>$knitting_source,8=>$buyer_arr,9=>$color_arr,10=>$color_range);

	//echo create_list_view("tbl_list_search", "Rcp. ID,Labdip No,Batch No,Ext. No,Recipe Description,Recipe Date,Order Source,Booking,Buyer,Color,Color Range", "50,70,90,55,100,70,80,100,60,85","920","220",0, $sql, "js_set_value", "id,batch_id", "", 1, "0,0,0,0,0,0,order_source,0,buyer_id,color_id,color_range", $arr, "id,labdip_no,batch_no,extention_no,recipe_description,recipe_date,order_source,style_or_order,buyer_id,color_id,color_range","","",'0,0,0,0,0,3,0,0,0,0,0','');
	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="80">Rcp. ID</th>
                <th width="80">Labdip No</th>
                <th width="100">Batch No</th>
                <th width="60">Ext. No</th>
                <th width="100">Recipe Description</th>
                <th width="70">Recipe Date</th>
                <th width="70">Order Source</th>
                <th width="100">Booking</th>
                <th width="100">Buyer</th>
                <th width="100">Color</th>
                <th width="">Color Range</th>

            </thead>
			<tbody>
				<?
				foreach($result  as $row)
				{
					$batch_idArr[$row[csf("batch_id")]]=$row[csf("batch_id")];
				}
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=64");
				oci_commit($con);
				disconnect($con);
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 64, 1, $batch_idArr, $empty_arr);//Batch ID Ref from=1

				if($dyeing_re_process==2)
				{
					$sql_dyeing="select b.batch_id,b.result from pro_fab_subprocess b,gbl_temp_engine g where   b.batch_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=64  and b.load_unload_id=2 and b.result in(1,11) and b.status_active=1 and b.is_deleted=0";
					$result_dyeing=sql_select($sql_dyeing);
					foreach($result_dyeing as $row)
					{

						$dyeing_shade_match_arr[$row[csf("batch_id")]]=$row[csf("result")];
					}
				}
				
				
				if($batch_no=="")
				{
					$batch_name_arr=array(); $batch_ext_arr=array();
					 $batchData=sql_select( "select b.id, b.batch_no,b.booking_no, b.extention_no from pro_batch_create_mst b,gbl_temp_engine g  where b.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=64 and b.status_active=1");
					foreach($batchData as $bRow)
					{
						$batch_name_arr[$bRow[csf('id')]]=$bRow[csf('batch_no')];
						$batch_ext_arr[$bRow[csf('id')]]=$bRow[csf('extention_no')];
						$batch_booking_arr[$bRow[csf('id')]]=$bRow[csf('booking_no')];
					}
				}
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=64");
				oci_commit($con);
				disconnect($con);

				$i=1;$result_chk_arr=array(1,11);
				foreach($result  as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($row[csf("id")]!='')
					{
						$last_recipe=$row[csf("id")];
					}
					else
					{

						$last_recipe=$last_recipe_arr[$row[csf("id")]];
						//echo "B";
						if($last_recipe=='')
						{
						$last_recipe=$last_recipe_arr2[$row[csf("batch_no")]];
						}
					}
					//echo $row[csf("batch_no")].'='.$row[csf("extention_no")].'<br>';
					//echo $last_recipe_arr[$row[csf("id")]].'DD';
					//echo $dyeing_shade_match_arr[$row[csf("batch_id")]].'D';
					//if($dyeing_shade_match_arr[$row[csf("batch_id")]]!=1)
					$dyeing_result_id=$dyeing_shade_match_arr[$row[csf("batch_id")]];
					if(!in_array($dyeing_result_id,$result_chk_arr))
					{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>');js_set_value('<? echo $last_recipe.'_'.$row[csf("batch_id")].'_'.$row[csf("batch_no")]; ?>');"  id="tr_<? echo $i; ?>" style="cursor:pointer;">
					<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
					<td width="80"><p><? echo $last_recipe; ?>&nbsp;</p></td>
					<td width="80" align="center"><p><? echo $row[csf("labdip_no")]; ?>&nbsp;</p></td>
					<td width="100" align="center" title="<?=$row[csf("batch_id")];?>"><p><? echo $batch_name_arr[$row[csf("batch_id")]]; ?>&nbsp;</p></td>
					<td width="60"><p><? echo $row[csf("extention_no")]; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $row[csf("recipe_description")]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo change_date_format($row[csf("recipe_date")]); ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? echo $order_source[$row[csf("order_source")]];?></p></td>
					<td width="100" align="center"><p><?  echo $batch_booking_arr[$row[csf("batch_id")]]; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
					<td width=""><p><? echo $color_range[$row[csf("color_range")]]; ?>&nbsp;</p></td>


				</tr>
				<?
					$i++;
					}
				}
				?>
			</tbody>
        </table>
	<?

	exit();
}

if($action=="load_batch")
{
	$batch_data='[';
	//$sql = "select b.id, b.batch_no, b.extention_no from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.re_dyeing_from and b.re_dyeing_from>0 and a.id='$data' and a.entry_form=59 and a.status_active=1 and a.is_deleted=0 group by b.id, b.batch_no, b.extention_no order by b.extention_no";
	//echo  "select max(b.id) as id, b.batch_no,max(b.extention_no) as extention_no from  pro_batch_create_mst b where  b.re_dyeing_from>0 and b.batch_no='$data'  and b.status_active=1 and b.is_deleted=0 group by  b.batch_no order by b.extention_no";
	$sql = "select max(b.id) as id, b.batch_no,max(b.extention_no) as extention_no from  pro_batch_create_mst b where  b.re_dyeing_from>0 and b.batch_no='$data'  and b.status_active=1 and b.is_deleted=0 group by  b.batch_no order by b.batch_no";
	$data_array=sql_select($sql);
	//echo $sql;die;
	foreach ($data_array as $row)
	{
		$batch_data.= "{id:".$row[csf('id')].",name:'".$row[csf('batch_no')]."-".$row[csf('extention_no')]."'},";
	}
	$batch_data=chop($batch_data,",");
	$batch_data.=']';
	echo $batch_data;
	exit();
}

if($action=='populate_data_from_recipe_search_popup')
{
	$data_array=sql_select("select id, labdip_no, company_id,working_company_id, location_id, recipe_description, batch_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range from pro_recipe_entry_mst where id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_recipe_no').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_labdip_no').value 				= '".$row[csf("labdip_no")]."';\n";

		echo "document.getElementById('txt_recipe_date').value 				= '".change_date_format($row[csf("recipe_date")])."';\n";
		echo "document.getElementById('cbo_order_source').value 			= '".$row[csf("order_source")]."';\n";
		echo "document.getElementById('txt_recipe_des').value 				= '".$row[csf("recipe_description")]."';\n";
		echo "document.getElementById('cbo_method').value 					= '".$row[csf("method")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "load_drop_down('requires/dyeing_re_process_entry_controller', ".$row[csf("working_company_id")].", 'load_drop_down_location', 'location_td' );\n";
		echo "load_drop_down('requires/dyeing_re_process_entry_controller', ".$row[csf("working_company_id")].", 'load_drop_down_buyer', 'buyer_td_id' );\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_lc_company_id').value 			= '".$row[csf("company_id")]."';\n";
		$actual_liquor_ratio=return_field_value("total_liquor","pro_recipe_entry_dtls","mst_id='".$row[csf("id")]."'");
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("working_company_id")] . "';\n";
		//load_drop_down('requires/dyeing_re_process_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );
		echo "document.getElementById('txt_liquor').value 					= '".$row[csf("total_liquor")]."';\n";
		echo "document.getElementById('hide_actual_liquor').value 			= '".$actual_liquor_ratio."';\n";
		echo "document.getElementById('txt_batch_ratio').value 				= '".$row[csf("batch_ratio")]."';\n";
		echo "document.getElementById('txt_liquor_ratio').value 			= '".$row[csf("liquor_ratio")]."';\n";
		echo "document.getElementById('cbo_machine_name').value 			= '" . $row[csf("machine_id")] . "';\n";
		exit();
	}
}

if($action=='populate_data_from_batch')
{
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color where b.status_active=1 and b.is_deleted=0",'id','color_name');

	if($db_type==0)
	{
		$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight,a.is_sales,a.sales_order_no, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=".$data." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no";
	}
	else if($db_type==2)
	{
		$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight,a.is_sales,a.sales_order_no, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=".$data." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.batch_weight,a.is_sales,a.sales_order_no, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form";
	}
	//echo $sql;
	$order_no=''; $buyer='';
	$result=sql_select($sql);
	$is_sales=$result[0][csf("is_sales")];
	$booking_no=$result[0][csf("booking_no")];
	$sales_order_no=$result[0][csf("sales_order_no")];
	
	$po_id=implode(",",array_unique(explode(",",$result[0][csf("po_id")])));
	$prod_id=implode(",",array_unique(explode(",",$result[0][csf("prod_id")])));

	if($result[0][csf("entry_form")]==36)
	{
		$batch_type="<b> SUBCONTRACT ORDER </b>";
		$orderData=sql_select("select b.id,b.order_no,a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.id in(".$po_id.")");
	}
	else
	{
		$batch_type="<b> SELF ORDER </b>";
		$orderData=sql_select("select b.id,b.po_number as order_no, a.buyer_name as party_id from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and b.id in(".$po_id.")");
	}

	foreach($orderData as $row)
	{
		if($order_no=="") $order_no=$row[csf('order_no')]; else $order_no.=", ".$row[csf('order_no')];
		$buyer=$row[csf('party_id')];
	}
	if($is_sales==1)
	{
		
		$booking_orderData=sql_select("select buyer_id from wo_booking_mst a where  booking_no='$booking_no' and status_active=1 ");
		foreach($booking_orderData as $row)
		{
			$buyer=$row[csf('buyer_id')];
			$order_no=$sales_order_no;
		}
	}


	echo "document.getElementById('txt_batch_id').value 			= '".$result[0][csf("id")]."';\n";
	//echo "document.getElementById('txt_batch_no').value 			= '".$result[0][csf("batch_no")]."';\n";
	echo "document.getElementById('txt_actual_batch_wgt').value 	= '".$result[0][csf("batch_weight")]."';\n";
	echo "document.getElementById('txt_batch_weight').value 		= '".$result[0][csf("batch_weight")]."';\n";
	echo "document.getElementById('txt_booking_no').value 			= '".$result[0][csf("booking_no")]."';\n";
	echo "document.getElementById('txt_booking_id').value 			= '".$result[0][csf("booking_no_id")]."';\n";
	echo "document.getElementById('cbo_buyer_name').value 			= '".$buyer."';\n";
	echo "document.getElementById('txt_color').value 				= '".$color_arr[$result[0][csf("color_id")]]."';\n";
	echo "document.getElementById('txt_color_id').value 			= '".$result[0][csf("color_id")]."';\n";
	echo "document.getElementById('cbo_color_range').value 			= '".$result[0][csf("color_range_id")]."';\n";
	echo "document.getElementById('txt_trims_weight').value 		= '".$result[0][csf("total_trims_weight")]."';\n";
	echo "document.getElementById('txt_order').value 				= '".$order_no."';\n";
	echo "document.getElementById('batch_type').innerHTML 			= '".$batch_type."';\n";

	if($db_type==0)
	{
		$sql_prod="Select group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	else
	{
		// $sql_prod="Select listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(cast(a.brand_id as varchar2(4000)),',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		  $sql_prod="Select a.yarn_lot as yarn_lot, a.brand_id  as brand_id,a.yarn_count as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		
	}

	$result_prod=sql_select($sql_prod);
	foreach($result_prod as $row)
	{
		if($row[csf("yarn_lot")])
		{
			$yarn_lotArr[$row[csf("yarn_lot")]]=$row[csf("yarn_lot")];
		}
		if($row[csf("brand_id")])
		{
			$brandArr[$row[csf("brand_id")]]=$row[csf("brand_id")];
		}
		if($row[csf("yarn_count")])
		{
			$yarn_countArr[$row[csf("yarn_count")]]=$row[csf("yarn_count")];
		}
	}

	$yarn_lot=implode(",",$yarn_lotArr);
	$brandArrB=implode(",",$brandArr);
	$brand_id=array_unique(explode(",",$brandArrB));
	$brand_name=""; $count_name="";
	foreach($brand_id as $val)
	{
		if($val>0)
		{
			if($brand_name=="") $brand_name=$brand_arr[$val]; else $brand_name.=", ".$brand_arr[$val];
		}
	}
	$yarn_counts=implode(",",$yarn_countArr);
	$yarn_count=array_unique(explode(",",$yarn_counts));
	foreach($yarn_count as $val)
	{
		if($val>0)
		{
			if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
		}
	}
	echo "document.getElementById('txt_yarn_lot').value 			= '".$yarn_lot."';\n";
	echo "document.getElementById('txt_brand').value 				= '".$brand_name."';\n";
	echo "document.getElementById('txt_count').value 				= '".$count_name."';\n";

	exit();
}

if($action=='populate_data_from_search_popup')
{
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$batch_arr=return_library_array( "select b.id, b.batch_no from pro_batch_create_mst b,pro_recipe_entry_mst a where b.id=a.batch_id and a.id='$data'",'id','batch_no');
	$data_array=sql_select("select id, labdip_no, company_id,working_company_id, location_id, recipe_description, batch_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio,machine_id, liquor_ratio, remarks, buyer_id, color_id,pickup, surplus_solution, color_range from pro_recipe_entry_mst where id='$data'");
	foreach ($data_array as $row)
	{
		$batch_id=$row[csf("batch_id")];

		echo "document.getElementById('txt_recipe_no').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_labdip_no').value 				= '".$row[csf("labdip_no")]."';\n";
		
		echo "document.getElementById('txt_recipe_date').value 				= '".change_date_format($row[csf("recipe_date")])."';\n";
		echo "document.getElementById('cbo_order_source').value 			= '".$row[csf("order_source")]."';\n";
		echo "document.getElementById('txt_recipe_des').value 				= '".$row[csf("recipe_description")]."';\n";
		echo "document.getElementById('txt_batch_id').value 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_arr[$row[csf("batch_id")]]."';\n";
		echo "document.getElementById('cbo_method').value 					= '".$row[csf("method")]."';\n";
		echo "document.getElementById('txt_liquor').value 					= '".$row[csf("total_liquor")]."';\n";
		echo "document.getElementById('txt_pick_up').value 			= '" . $row[csf("pickup")] . "';\n";
		echo "document.getElementById('surpls_solution').value 			= '" . $row[csf("surplus_solution")] . "';\n";
		echo "document.getElementById('txt_batch_ratio').value 				= '".$row[csf("batch_ratio")]."';\n";
		echo "load_drop_down('requires/dyeing_re_process_entry_controller', ".$row[csf("working_company_id")].", 'load_drop_down_location', 'location_td' );\n";
		echo "load_drop_down('requires/dyeing_re_process_entry_controller', ".$row[csf("working_company_id")].", 'load_drop_down_buyer', 'buyer_td_id' );\n";

		echo "document.getElementById('cbo_lc_company_id').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("working_company_id")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";



		echo "document.getElementById('txt_liquor_ratio').value 			= '".$row[csf("liquor_ratio")]."';\n";
		echo "document.getElementById('hide_actual_liquor').value 			= '".$row[csf("total_liquor")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "load_drop_down('requires/dyeing_re_process_entry_controller', ".$row[csf('working_company_id')].", 'load_drop_machine', 'td_dyeing_machine' );\n";
		echo "document.getElementById('cbo_machine_name').value 			= '" . $row[csf("machine_id")] . "';\n";

		if($row[csf("order_source")] != 4)
		{
			if($db_type==0)
			{
				$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=".$row[csf("batch_id")]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no";
			}
			else if($db_type==2)
			{
				$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=".$row[csf("batch_id")]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form";
			}
			//echo $sql;
			$order_no=''; //$buyer='';
			$result=sql_select($sql);
			$po_id=implode(",",array_unique(explode(",",$result[0][csf("po_id")])));
			$prod_id=implode(",",array_unique(explode(",",$result[0][csf("prod_id")])));
			$batch_weight=$result[0][csf("batch_weight")];

			if($result[0][csf("entry_form")]==36)
			{
				$batch_type="<b> SUBCONTRACT ORDER </b>";
				$orderData=sql_select("select b.id,b.order_no,a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.id in(".$po_id.")");
			}
			else
			{
				$batch_type="<b> SELF ORDER </b>";
				$orderData=sql_select("select b.id,b.po_number as order_no, a.buyer_name as party_id from wo_po_details_master a,  wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".$po_id.")");
			}

			foreach($orderData as $rowOrd)
			{
				if($order_no=="") $order_no=$rowOrd[csf('order_no')]; else $order_no.=", ".$rowOrd[csf('order_no')];
				//$buyer=$row[csf('party_id')];
			}
			$buyer=$row[csf('buyer_id')];
			if($result[0][csf("color_range_id")]==0) 
			{
				echo "$('#cbo_color_range').removeAttr('disabled',true);\n";
			}
			else 
			{
				echo "$('#cbo_color_range').attr('disabled',true);\n";
			}

			echo "document.getElementById('txt_batch_no').value 			= '".$result[0][csf("batch_no")]."';\n";
			echo "document.getElementById('txt_actual_batch_wgt').value 	= '".trim($batch_weight)."';\n";
			echo "document.getElementById('txt_batch_weight').value 		= '".trim($batch_weight)."';\n";
			echo "document.getElementById('txt_booking_no').value 			= '".$result[0][csf("booking_no")]."';\n";
			echo "document.getElementById('txt_booking_id').value 			= '".$result[0][csf("booking_no_id")]."';\n";
			echo "document.getElementById('cbo_buyer_name').value 			= '".$buyer."';\n";
			echo "document.getElementById('txt_color').value 				= '".$color_arr[$result[0][csf("color_id")]]."';\n";
			echo "document.getElementById('txt_color_id').value 			= '".$result[0][csf("color_id")]."';\n";
			echo "document.getElementById('cbo_color_range').value 			= '".$result[0][csf("color_range_id")]."';\n";
			echo "document.getElementById('txt_trims_weight').value 		= '".$result[0][csf("total_trims_weight")]."';\n";
			echo "document.getElementById('txt_order').value 				= '".$order_no."';\n";
			echo "document.getElementById('batch_type').innerHTML 			= '".$batch_type."';\n";

			if($db_type==0)
			{
				$sql_prod="Select group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				// $sql_prod="Select listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(cast(a.brand_id as varchar2(4000)),',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				//$sql_prod="Select a.yarn_lot as yarn_lot, a.brand_id  as brand_id,a.yarn_count as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				$sql_prod="Select a.yarn_lot as yarn_lot, a.brand_id  as brand_id,a.yarn_count as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b,pro_batch_create_dtls c  where a.id=b.dtls_id and c.prod_id=a.prod_id and c.prod_id=b.prod_id  and c.mst_id=$batch_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}

			$result_prod=sql_select($sql_prod);
			foreach($result_prod as $row)
			{
				if($row[csf("yarn_lot")])
				{
					$yarn_lotArr[$row[csf("yarn_lot")]]=$row[csf("yarn_lot")];
				}
				if($row[csf("brand_id")])
				{
					$brandArr[$row[csf("brand_id")]]=$row[csf("brand_id")];
				}
				if($row[csf("yarn_count")])
				{
					$yarn_countArr[$row[csf("yarn_count")]]=$row[csf("yarn_count")];
				}
			}

			$yarn_lot=implode(",",$yarn_lotArr);
			$brandArrB=implode(",",$brandArr);
			$brand_id=array_unique(explode(",",$brandArrB));
			$brand_name=""; $count_name="";
			foreach($brand_id as $val)
			{
				if($val>0)
				{
					if($brand_name=="") $brand_name=$brand_arr[$val]; else $brand_name.=", ".$brand_arr[$val];
				}
			}

			$yarn_counts=implode(",",$yarn_countArr);
			$yarn_count=array_unique(explode(",",$yarn_counts));
			foreach($yarn_count as $val)
			{
				if($val>0)
				{
					if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
				}
			}
			echo "document.getElementById('txt_yarn_lot').value 			= '".$yarn_lot."';\n";
			echo "document.getElementById('txt_brand').value 				= '".$brand_name."';\n";
			echo "document.getElementById('txt_count').value 				= '".$count_name."';\n";
		}
		else
		{
			$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form from pro_batch_create_mst a, pro_batch_trims_dtls b where a.id=b.mst_id and a.id=".$row[csf("batch_id")]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form";
			$result=sql_select($sql);

			if($result[0][csf("entry_form")]==36)
			{
				$batch_type="<b> SUBCONTRACT ORDER </b>";
				$orderData=sql_select("select b.id,b.order_no,a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.id in(".$po_id.")");
			}
			else
			{
				$batch_type="<b> SELF ORDER </b>";
				$orderData=sql_select("select b.id,b.po_number as order_no, a.buyer_name as party_id from wo_po_details_master a,  wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".$po_id.")");
			}
			
			if($result[0][csf("color_range_id")]==0) 
			{
				echo "$('#cbo_color_range').removeAttr('disabled',true);\n";
			}
			else 
			{
				echo "$('#cbo_color_range').attr('disabled',true);\n";
			}

			echo "document.getElementById('txt_batch_no').value 			= '".$result[0][csf("batch_no")]."';\n";
			echo "document.getElementById('txt_actual_batch_wgt').value 	= '".$result[0][csf("batch_weight")]."';\n";
			echo "document.getElementById('txt_batch_weight').value 		= '".$result[0][csf("batch_weight")]."';\n";
			echo "document.getElementById('txt_booking_no').value 			= '".$result[0][csf("booking_no")]."';\n";
			echo "document.getElementById('txt_booking_id').value 			= '".$result[0][csf("booking_no_id")]."';\n";
			echo "document.getElementById('cbo_buyer_name').value 			= '".$buyer."';\n";
			echo "document.getElementById('txt_color').value 				= '".$color_arr[$result[0][csf("color_id")]]."';\n";
			echo "document.getElementById('txt_color_id').value 			= '".$result[0][csf("color_id")]."';\n";
			echo "document.getElementById('cbo_color_range').value 			= '".$result[0][csf("color_range_id")]."';\n";
			echo "document.getElementById('txt_trims_weight').value 		= '".$result[0][csf("total_trims_weight")]."';\n";
			echo "document.getElementById('txt_order').value 				= '".$order_no."';\n";
			echo "document.getElementById('batch_type').innerHTML 			= '".$batch_type."';\n";

		}


		exit();
	}
}

if($action=='populate_data_from_recipe')
{
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$data_array=sql_select("select id, dyeing_re_process,copy_from, recipe_id, labdip_no, company_id,working_company_id, location_id, recipe_description, batch_id, new_batch_weight, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id,pickup, surplus_solution, color_range, ready_to_approve,approved,machine_id,in_charge_id from pro_recipe_entry_mst where id='$data'");
	$batch_id=$data_array[0][csf('batch_id')];

	$in_charge_arr = return_library_array("select b.id, b.first_name from lib_employee b,pro_recipe_entry_mst f where f.in_charge_id=b.id  and f.batch_id=$batch_id and f.status_active=1 and f.is_deleted=0 and b.status_active=1 and b.is_deleted=0", 'id', 'first_name');

	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_sys_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_dyeing_re_process').value 		= '".$row[csf("dyeing_re_process")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("working_company_id")]."';\n";
		echo "document.getElementById('cbo_lc_company_id').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_recipe_no').value 				= '".$row[csf("recipe_id")]."';\n";
		echo "document.getElementById('txt_labdip_no').value 				= '".$row[csf("labdip_no")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_recipe_date').value 				= '".change_date_format($row[csf("recipe_date")])."';\n";
		echo "document.getElementById('cbo_order_source').value 			= '".$row[csf("order_source")]."';\n";
		echo "document.getElementById('txt_recipe_des').value 				= '".$row[csf("recipe_description")]."';\n";
		echo "document.getElementById('txt_pick_up').value 			= '" . $row[csf("pickup")] . "';\n";
		echo "document.getElementById('surpls_solution').value 			= '" . $row[csf("surplus_solution")] . "';\n";
		echo "document.getElementById('cbo_ready_to_approve').value 			= '" . $row[csf("ready_to_approve")] . "';\n";
		echo "document.getElementById('txt_in_charge_id').value	= '" . $row[csf("in_charge_id")] . "';\n";
		echo "document.getElementById('txt_in_charge').value	= '" . $in_charge_arr[$row[csf("in_charge_id")]] . "';\n";


		echo "load_drop_down('requires/recipe_entry_controller', ".$row[csf('working_company_id')].", 'load_drop_machine', 'td_dyeing_machine' );\n";
		echo "document.getElementById('cbo_machine_name').value 				= '" . $row[csf("machine_id")] . "';\n";
		if($row[csf("approved")]==1)
		{
			$approv_msg="<b>Apporved</b>";
		}
		else if($row[csf("approved")]==3)
		{
			$approv_msg="<b>Partial Apporved</b>";
		}
		else $approv_msg="";
		echo "document.getElementById('approved_mst').innerHTML 			= '".$approv_msg."';\n";
		
		if($row[csf("dyeing_re_process")]==1)
		{
			$bacth_array=array();
			  $query = "select b.id, b.batch_no, b.extention_no from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id   and a.id='".$row[csf("recipe_id")]."' and a.entry_form=59 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.extention_no>0 group by b.id, b.batch_no, b.extention_no";
			$batch_data_array=sql_select($query);
			//echo $sql;die;
			foreach ($batch_data_array as $rowB)
			{
				$bacth_array[$rowB[csf('id')]]= $rowB[csf('batch_no')]."-".$rowB[csf('extention_no')];
			}

			$dropdown=create_drop_down("txt_batch_no", 80, $bacth_array,"", 1,"-- Select --", 0,"load_batch_data(this.value);");
			echo "document.getElementById('batch_td').innerHTML 			= '".$dropdown."';\n";
		}
		else
		{
			echo "document.getElementById('batch_td').innerHTML 			= '".'<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px;" placeholder="Display" disabled />'."';\n";
		}

		//$actual_liquor_ratio=return_field_value("total_liquor","pro_recipe_entry_mst","id='".$row[csf("recipe_id")]."'");
		$actual_liquor_ratio=return_field_value("total_liquor","pro_recipe_entry_dtls","mst_id='".$row[csf("recipe_id")]."'");
		echo "document.getElementById('txt_batch_id').value 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_weight').value 			= '".$row[csf("new_batch_weight")]."';\n";
		echo "document.getElementById('cbo_method').value 					= '".$row[csf("method")]."';\n";
		echo "document.getElementById('txt_liquor').value 					= '".$row[csf("total_liquor")]."';\n";
		echo "document.getElementById('txt_batch_ratio').value 				= '".$row[csf("batch_ratio")]."';\n";
		echo "document.getElementById('txt_liquor_ratio').value 			= '".$row[csf("liquor_ratio")]."';\n";
		echo "document.getElementById('hide_actual_liquor').value 			= '".$actual_liquor_ratio."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_id_check').value 				= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_copy_from').value 				= '" . $row[csf("copy_from")] . "';\n";

		if($db_type==0)
		{
			$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight,a.is_sales,a.sales_order_no, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=".$row[csf("batch_id")]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no";
		}
		else if($db_type==2)
		{
			$sql = "select a.id, a.batch_no, a.extention_no, a.batch_weight,a.is_sales,a.sales_order_no, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id=".$row[csf("batch_id")]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.batch_weight,a.is_sales,a.sales_order_no, a.total_trims_weight, a.batch_date, a.color_id, a.color_range_id, a.booking_no, a.booking_no_id, a.entry_form";
		}

		$order_no=''; //$buyer='';
		$result=sql_select($sql);
		$is_sales=$result[0][csf("is_sales")];
		$sales_order_no=$result[0][csf("sales_order_no")];
		$po_id=implode(",",array_unique(explode(",",$result[0][csf("po_id")])));
		$prod_id=implode(",",array_unique(explode(",",$result[0][csf("prod_id")])));

		if($result[0][csf("entry_form")]==36)
		{
			$batch_type="<b> SUBCONTRACT ORDER </b>";
			$orderData=sql_select("select b.id,b.order_no,a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.id in(".$po_id.")");
		}
		else
		{
			$batch_type="<b> SELF ORDER </b>";
			$orderData=sql_select("select b.id,b.po_number as order_no, a.buyer_name as party_id from wo_po_details_master a,  wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".$po_id.")");
		}

		foreach($orderData as $rowOrd)
		{
			if($order_no=="") $order_no=$rowOrd[csf('order_no')]; else $order_no.=", ".$rowOrd[csf('order_no')];
			//$buyer=$rowOrd[csf('party_id')];
		}

		$buyer=$row[csf('buyer_id')];
		
		if($is_sales==1)
		{
			$order_no=$sales_order_no;
		}
		
		if($row[csf("dyeing_re_process")]==1)
		{
			echo "document.getElementById('txt_batch_no').value 			= '".$row[csf("batch_id")]."';\n";
		}
		else
		{
			echo "document.getElementById('txt_batch_no').value 			= '".$result[0][csf("batch_no")]."';\n";
		}

		echo "document.getElementById('txt_actual_batch_wgt').value 	= '".$result[0][csf("batch_weight")]."';\n";
		echo "document.getElementById('txt_booking_no').value 			= '".$result[0][csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_id').value 			= '".$result[0][csf("booking_no_id")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 			= '".$buyer."';\n";
		echo "document.getElementById('txt_color').value 				= '".$color_arr[$result[0][csf("color_id")]]."';\n";
		echo "document.getElementById('txt_color_id').value 			= '".$result[0][csf("color_id")]."';\n";
		echo "document.getElementById('cbo_color_range').value 			= '".$result[0][csf("color_range_id")]."';\n";
		echo "document.getElementById('txt_trims_weight').value 		= '".$result[0][csf("total_trims_weight")]."';\n";
		echo "document.getElementById('txt_order').value 				= '".$order_no."';\n";
		echo "document.getElementById('batch_type').innerHTML 			= '".$batch_type."';\n";

		if($db_type==0)
		{
			$sql_prod="Select group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		}
		else
		{
			// $sql_prod="Select listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(cast(a.brand_id as varchar2(4000)),',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$sql_prod="Select a.yarn_lot as yarn_lot, a.brand_id  as brand_id,a.yarn_count as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in ($po_id) and b.prod_id in ($prod_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		}

		$result_prod=sql_select($sql_prod);
		foreach($result_prod as $row)
		{
			if($row[csf("yarn_lot")])
			{
				$yarn_lotArr[$row[csf("yarn_lot")]]=$row[csf("yarn_lot")];
			}
			if($row[csf("brand_id")])
			{
				$brandArr[$row[csf("brand_id")]]=$row[csf("brand_id")];
			}
			if($row[csf("yarn_count")])
			{
				$yarn_countArr[$row[csf("yarn_count")]]=$row[csf("yarn_count")];
			}
		}
		
		$yarn_lot=implode(",",$yarn_lotArr);
		$brandArrB=implode(",",$brandArr);
		$brand_id=array_unique(explode(",",$brandArrB));
		$brand_name=""; $count_name="";
		foreach($brand_id as $val)
		{
			if($val>0)
			{
				if($brand_name=="") $brand_name=$brand_arr[$val]; else $brand_name.=", ".$brand_arr[$val];
			}
		}

		$yarn_counts=implode(",",$yarn_countArr);
		$yarn_count=array_unique(explode(",",$yarn_counts));
		foreach($yarn_count as $val)
		{
			if($val>0)
			{
				if($count_name=="") $count_name=$count_arr[$val]; else $count_name.=", ".$count_arr[$val];
			}
		}
		echo "document.getElementById('txt_yarn_lot').value 			= '".$yarn_lot."';\n";
		echo "document.getElementById('txt_brand').value 				= '".$brand_name."';\n";
		echo "document.getElementById('txt_count').value 				= '".$count_name."';\n";

		echo "disable_enable_fields('cbo_dyeing_re_process*cbo_company_id*txt_recipe_no',1);\n";

		exit();
	}
}

if($action=="recipe_item_details")
{
	$data=explode("**",$data);
	$recipe_id=$data[0];
	$dyeing_re_process=$data[1];
	//echo $dyeing_re_process.'dd';
	$process_array=array();$process_array_remark=array();
	/*if($dyeing_re_process==2) // Issue ID=9810
	{
		$sql="select a.id, a.sub_process_id as sub_process_id, a.process_remark, a.store_id from pro_recipe_entry_dtls a, product_details_master b where a.prod_id=b.id and a.mst_id='$recipe_id' and a.status_active=1 and a.is_deleted=0 and a.ratio>0 and b.item_category_id=6 order by a.id";
	}
	else
	{
		$sql="select id, sub_process_id as sub_process_id, process_remark, store_id from pro_recipe_entry_dtls where mst_id='$recipe_id' and status_active=1 and is_deleted=0 order by id";
	}*/
	$sql="select id, sub_process_id as sub_process_id, process_remark, store_id from pro_recipe_entry_dtls where mst_id='$recipe_id' and status_active=1 and is_deleted=0 order by id";
	$nameArray=sql_select( $sql );
	foreach($nameArray as $row)
	{
		if (!in_array( $row[csf("sub_process_id")],$process_array) )
		{
			$process_array[]=$row[csf("sub_process_id")];
			$process_array_remark[$row[csf("sub_process_id")]]=$row[csf("process_remark")]."**".$row[csf("store_id")];
		}
	}
    foreach($process_array as $sub_provcess_id)
	{
		$process_ref = explode("**",$process_array_remark[$sub_provcess_id]);
		$process_remark=$process_ref[0];
		$store_id=$process_ref[1];
		//$process_remark=$process_array_remark[$sub_provcess_id];
		?>
        <h3 align="left" id="accordion_h<? echo $sub_provcess_id; ?>" style="width:910px" class="accordion_h" onClick="fnc_item_details(<? echo $sub_provcess_id; ?>,'1','<? echo $process_remark; ?>','<? echo $store_id; ?>')"><span id="accordion_h<? echo $sub_provcess_id; ?>span">+</span><? echo $dyeing_sub_process[$sub_provcess_id]; ?></h3>
		<?
	}
	exit();
}

if($action=="recipe_items")
{
	$data=explode("**",$data);
	$update_id=$data[0];
	$recipe_id=$data[1];
	$dyeing_re_process=$data[2];

	$prev_process_array=return_library_array("select sub_process_id from pro_recipe_entry_dtls where mst_id='$update_id' and status_active=1 and is_deleted=0 and ratio>0 group by sub_process_id",'sub_process_id','sub_process_id');
	/*if($dyeing_re_process==2)
	{
		$sql="select a.id, a.sub_process_id as sub_process_id,process_remark from pro_recipe_entry_dtls a, product_details_master b where a.prod_id=b.id and a.mst_id='$recipe_id' and a.status_active=1 and a.is_deleted=0 and a.ratio>0 and b.item_category_id=6 order by a.id";
	}
	else
	{
		$sql="select id, sub_process_id as sub_process_id,process_remark from pro_recipe_entry_dtls where mst_id='$recipe_id' and status_active=1 and is_deleted=0 order by id";
	}*/
	$sql="select id, sub_process_id as sub_process_id, process_remark, store_id from pro_recipe_entry_dtls where mst_id='$recipe_id' and status_active=1 and is_deleted=0 order by id";
	$process_array=array();
	$nameArray=sql_select( $sql );
	foreach($nameArray as $row)
	{
		if (!in_array( $row[csf("sub_process_id")],$prev_process_array) )
		{
			$prev_process_array[$row[csf("sub_process_id")]]=$row[csf("sub_process_id")];
			$process_array[]=$row[csf("sub_process_id")];
			$process_array_remark[$row[csf("sub_process_id")]]=$row[csf("process_remark")]."**".$row[csf("store_id")];
		}
	}
    foreach($process_array as $sub_provcess_id)
	{
		//$process_remark=$process_array_remark[$sub_provcess_id];
		$process_ref = explode("**",$process_array_remark[$sub_provcess_id]);
		$process_remark=$process_ref[0];
		$store_id=$process_ref[1];
		?>
        <h3 align="left" id="accordion_h<? echo $sub_provcess_id; ?>" style="width:180px" class="accordion_h" onClick="fnc_item_details(<? echo $sub_provcess_id; ?>, '0','<? echo $process_remark; ?>','<? echo $store_id; ?>')"><span id="accordion_h<? echo $sub_provcess_id; ?>span">+</span><? echo $dyeing_sub_process[$sub_provcess_id]; ?></h3>
	<?
	}
	exit();
}

if($action=="item_details")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$sub_process_id=$data[1];
	$update_id=$data[2];
	$recipe_id=$data[3];
	$dyeing_re_process=$data[4];
	$new_process=$data[5];
	//if($dyeing_re_process==2) $item_catg="6"; else $item_catg="5,6,7";
	$item_catg="5,6,7,23";
	$batch_weight=$data[6];
	$total_liquor=$data[7];
	$actual_batch_weight=$data[8];
	$actual_total_liquor=$data[9];
	$store_id=$data[10];
	$from_lib_check_id=$data[11];

	//if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98 || $sub_process_id ==140 || $sub_process_id ==141 || $sub_process_id ==142 || $sub_process_id ==143)
	if(in_array($sub_process_id, $subprocessForWashArr))
	{
		$ration_cond="";
	}
	else
	{
		$ration_cond=" and ratio>0 ";
	}

	/*$recipeData=sql_select("select a.total_liquor, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
	$total_liquor=$recipeData[0][csf('total_liquor')];
	$batch_weight=$recipeData[0][csf('batch_weight')];*/

	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');

	if($update_id!="")
	{
		//echo "select prod_id,ratio from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id order by id";
		$ratio_arr=return_library_array("select prod_id,ratio from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and ratio>0 order by id",'prod_id','ratio');
		$iss_arr=return_library_array("select b.product_id, sum(b.required_qnty) as qnty from inv_issue_master a, dyes_chem_issue_dtls b, dyes_chem_requ_recipe_att c where a.req_no=c.mst_id and a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and c.recipe_id=$update_id and b.sub_process=$sub_process_id group by b.product_id",'product_id','qnty');

	}
	$sql_lot = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $company_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$variable_lot=$sql_lot[0][csf("auto_transfer_rcv")];

	$recipe_data_arr=array(); $recipe_prod_id_arr=array(); $product_data_arr=array();
	if($update_id=="")
	{
		if($new_process==1)
		{
			$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.store_id, b.lot, b.cons_qty as store_stock
			from product_details_master a, inv_store_wise_qty_dtls b
			where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$store_id and a.item_category_id in($item_catg) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
		}
		else
		{

			//echo "select prod_id, item_lot, dose_base, ratio, seq_no from pro_recipe_entry_dtls where mst_id=$recipe_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no";
			$recipeData=sql_select("select prod_id, item_lot, dose_base, ratio, seq_no, store_id, item_lot from pro_recipe_entry_dtls where mst_id=$recipe_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no");
			foreach($recipeData as $row)
			{
			//	if($variable_lot==1) $item_lot=$row[csf('item_lot')]; else $item_lot="";
			$item_lot=$row[csf('item_lot')];
				$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')]."_".$item_lot;
				$recipe_data_arr[$prod_key][1]=$row[csf('item_lot')];
				$recipe_data_arr[$prod_key][2]=$row[csf('dose_base')];
				$recipe_data_arr[$prod_key][3]=$row[csf('ratio')];
				$recipe_data_arr[$prod_key][4]=$row[csf('seq_no')];
				$recipe_prod_id_arr[$prod_key]=$prod_key;
				$recipe_lotArr[$row[csf('prod_id')]]=$item_lot;
			}
			// Dyes And Chemical Store>>Dyes And Chemical Receive, Transfer, Issue page>> item_category_id in(5,6,7,23)
			$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.store_id, b.lot, b.cons_qty as store_stock
			from product_details_master a, inv_store_wise_qty_dtls b
			where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$store_id and a.item_category_id in($item_catg) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";

			/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.item_lot, b.dose_base, b.ratio, b.seq_no from product_details_master a left join pro_recipe_entry_dtls b on a.id=b.prod_id and b.mst_id=$recipe_id and b.sub_process_id=$sub_process_id and b.status_active=1 and b.is_deleted=0 and b.ratio>0 where a.company_id='$company_id' and a.item_category_id in ($item_catg) and a.status_active=1 and a.is_deleted=0";*/
		}
		// echo "<pre>"; print_r($recipe_prod_id_arr);
	}
	else
	{
		/*"select prod_id, mst_id, id as dtls_id, item_lot, dose_base, ratio, seq_no, adj_type, adj_ratio, adj_perc, adj_qnty, new_item, comments,is_checked  from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no";*/
		$recipeData=sql_select("select prod_id, mst_id, id as dtls_id, item_lot, dose_base, ratio, seq_no, adj_type, adj_ratio, adj_perc, adj_qnty, new_item, comments, is_checked, store_id, item_lot
		from pro_recipe_entry_dtls where mst_id=$update_id and sub_process_id=$sub_process_id and status_active=1 and is_deleted=0 $ration_cond order by seq_no");
		foreach($recipeData as $row)
		{
			//if($variable_lot==1) $item_lot=$row[csf('item_lot')]; else $item_lot="";
			$item_lot=$row[csf('item_lot')];
			$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')]."_".$item_lot;
			$recipe_data_arr[$prod_key][1]=$row[csf('item_lot')];
			$recipe_data_arr[$prod_key][2]=$row[csf('dose_base')];
			$recipe_data_arr[$prod_key][3]=$row[csf('ratio')];
			$recipe_data_arr[$prod_key][4]=$row[csf('seq_no')];
			$recipe_data_arr[$prod_key][5]=$row[csf('mst_id')];
			$recipe_data_arr[$prod_key][6]=$row[csf('dtls_id')];
			$recipe_data_arr[$prod_key][7]=$row[csf('adj_type')];
			$recipe_data_arr[$prod_key][8]=$row[csf('adj_perc')];
			$recipe_data_arr[$prod_key][9]=$row[csf('adj_qnty')];
			$recipe_data_arr[$prod_key][10]=$row[csf('new_item')];
			$recipe_data_arr[$prod_key][11]=$row[csf('adj_ratio')];
			$recipe_data_arr[$prod_key][12]=$row[csf('comments')];
			$recipe_data_arr[$prod_key][13]=$row[csf('is_checked')];
			$recipe_prod_id_arr[$prod_key]=$prod_key;
		}

		/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.mst_id, b.id as dtls_id, b.item_lot, b.dose_base, b.ratio, b.seq_no, b.adj_type, b.adj_perc, b.adj_qnty, b.new_item from product_details_master a left join pro_recipe_entry_dtls b on a.id=b.prod_id and b.mst_id=$update_id and b.sub_process_id=$sub_process_id and b.status_active=1 and b.is_deleted=0 and b.ratio>0 where a.company_id='$company_id' and a.item_category_id in ($item_catg) and a.status_active=1 and a.is_deleted=0 order by b.seq_no, b.id";*/
		 //$sql="select id, item_category_id, item_group_id, sub_group_name, item_description, item_size, unit_of_measure, current_stock from product_details_master where company_id='$company_id' and item_category_id in($item_catg)  and status_active=1 and is_deleted=0";
		 $sql="select a.id, a.item_category_id, a.subprocess_id,a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.store_id, b.lot, b.cons_qty as store_stock
		from product_details_master a, inv_store_wise_qty_dtls b
		where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$store_id and a.item_category_id in($item_catg) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
	}
	 //echo $sql;
	$nameArray=sql_select( $sql );
	foreach($nameArray as $row)
	{
		$recipe_lot=$recipe_lotArr[$row[csf('id')]];
		if($recipe_lot) $prev_recipe_lot=$recipe_lot;else $prev_recipe_lot=$row[csf('lot')];
		$prod_key=$row[csf('id')]."_".$row[csf('store_id')]."_".$prev_recipe_lot;
		$subprocess_Arr=array_unique(explode(",",$row[csf('subprocess_id')]));
		 //echo $from_lib_check_id.'D';
		if($from_lib_check_id==1 && in_array($sub_process_id,$subprocess_Arr)) //CheckBox Checked //Lib-> Item Account Creation -Sub Process:: Issue Id=8876
		{
		$product_data_arr[$prod_key]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('current_stock')]."**".$row[csf('store_stock')];
		}
		else //When uncheck the box then data come as usual business
		{
			$product_data_arr[$prod_key]=$row[csf('item_category_id')]."**".$row[csf('item_group_id')]."**".$row[csf('sub_group_name')]."**".$row[csf('item_description')]."**".$row[csf('item_size')]."**".$row[csf('unit_of_measure')]."**".$row[csf('current_stock')]."**".$row[csf('store_stock')];
		}
	}
	// echo "<pre>"; print_r($recipe_prod_id_arr);
//echo $variable_lot.'SSSSSS';
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="80">Item Category</th>
                <th width="80">Item Group</th>
                <th width="100">Item Description</th>
                <th width="80">Item Lot</th>
                <th width="40">UOM</th>
                <th width="70">Dose Base</th>
                <th width="55">Ratio</th>
                <th width="70">Pv. Recp Qty</th>
                <th width="70">Recipe Qty</th>
                <th width="75">Adj. Type</th>
                <th width="57">New Ratio</th>
                <th width="53">Req. %</th>
                <th width="72">Req. Qnty.</th>
                <th width="45">Seq. No</th>
                <th width="90">Sub Process</th>
                <th width="50">Prod. ID</th>
                <th width="70">Stock Qty</th>
                <th>Remarks</th>
                <th align="center"> Check </th>
            </thead>
        </table>
        <div style="width:1350px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1330" class="rpt_table" id="tbl_list_search">
            	<tbody>
					<?

				//if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98 || $sub_process_id==140 || $sub_process_id==141 || $sub_process_id==142 || $sub_process_id==143) //Wash start...//140,141,142,143
				if(in_array($sub_process_id, $subprocessForWashArr))
				{
					$i=1;
					if($variable_lot==1)
					{
						$lot_popup='';
						$place_holder='';
					}
					else
					{
						$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
						$place_holder='Browse';
					}
					foreach($recipe_prod_id_arr as $prodId)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$prodId_ref=explode("_",$prodId);
						$product_id=$prodId_ref[0];
						$str_id=$prodId_ref[1];
						$product_lot=$prodId_ref[2];
						$prodData=explode("**",$product_data_arr[$prodId]);

						$item_category_id=$prodData[0];
						$item_group_id=$prodData[1];
						$sub_group_name=$prodData[2];
						$item_description=$prodData[3];
						$item_size=$prodData[4];
						$unit_of_measure=$prodData[5];
						$current_stock=$prodData[6];
						$store_stock=$prodData[7];

						$disbled=""; $disbled2="disabled='disabled'"; $disbled3="disabled='disabled'"; $disbledDropdown=1; $disbledDropdown2=0;
						$prev_recipe_qty=0; $recipe_qty=0; $seq_no='';
						//$ratio=$selectResult[csf('ratio')];
						$item_lot=$recipe_data_arr[$prodId][1];
						$selected_dose=$recipe_data_arr[$prodId][2];
						$ratio=$recipe_data_arr[$prodId][3];
						$seq_no=$recipe_data_arr[$prodId][4];
						$mst_id=$recipe_data_arr[$prodId][5];
						$dtls_id=$recipe_data_arr[$prodId][6];
						$adj_type=$recipe_data_arr[$prodId][7];
						$adj_perc=$recipe_data_arr[$prodId][8];
						$adj_qnty=$recipe_data_arr[$prodId][9];
						$new_item=$recipe_data_arr[$prodId][10];
						$adj_ratio=$recipe_data_arr[$prodId][11];
						$txt_remarks=$recipe_data_arr[$prodId][12];
						$checked=$recipe_data_arr[$prodId][13];
						$bgcolor="yellow";
						$iss_qty=$iss_arr[$product_id];
						if($update_id!="" && $ratio>0 && $iss_qty>0)
						{
							$disbled="disabled='disabled'";
						}
						//is_checked value
						if($checked==1)
						{
							$check_sts='checked';
						}
						else
						{
							$check_sts="";
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                            <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                            <td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
                            <td width="80" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                            <td width="100" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
                            <td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" <? echo $lot_popup; ?>  placeholder="<? echo $place_holder; ?>" value="<? echo $product_lot; ?>" readonly>
                            </td>
                            <td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                            <td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose,"",$disbledDropdown2); ?></td>
                            <td width="55" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>);" <? echo $disbled; ?>></td>
                            <td width="68" style="padding-right:2px" align="right" id="prev_recipe_qty_<? echo $i; ?>"><? echo $prev_recipe_qty; ?></td>
                            <td width="68" style="padding-right:2px" align="right" id="recipe_qty_<? echo $i; ?>"><? echo $recipe_qty; ?></td>
                            <td width="75" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 70, $increase_decrease, "", 1, "- Select -",0,"",1);//calculate($i,'cbo_adj_type_')$disbledDropdown $selectResult[csf('adj_type')] ?></td>
                            <td width="57" align="center" id="adj_ratio_<? echo $i; ?>"><input type="text" name="txt_adj_ratio[]" id="txt_adj_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $adj_ratio; ?>" onKeyUp="calculate(<? echo $i; ?>,'txt_adj_ratio_')" <? echo $disbled3; ?>><input type="hidden" name="txt_hiddadj_ratio[]" id="txt_hiddadj_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $adj_ratio; ?>"/></td>
                            <td width="53" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $adj_perc; ?>" onKeyUp="calculate(<? echo $i; ?>,'txt_adj_per_')" <? echo $disbled2; ?>></td>
                            <td width="72"><input type="text" name="adj_qnty[]" id="adj_qnty_<? echo $i;?>" class="text_boxes_numeric" value="<? echo number_format($adj_qnty,6,'.',''); ?>" onKeyUp="calculate(<? echo $i; ?>,'adj_qnty_')" style="width:58px" <? echo $disbled2; ?>><input type="hidden" name="hiddenadj_qnty[]" id="hiddenadj_qnty_<? echo $i;?>" class="text_boxes_numeric" value="<? echo number_format($adj_qnty,6,'.',''); ?>"  style="width:20px"/> </td>
                            <td width="45" align="center" id="seqno_<? echo $i; ?>">
                                <input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);">
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>">
                                <input type="hidden" name="product_id[]" id="product_id_<? echo $i; ?>" value="<? echo $product_id; ?>">
                            </td>
                            <td width="90" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p></td>
                            <td width="50" align="center" id="prod_id_<? echo $i; ?>"><? echo $product_id; ?></td>
                            <td align="right" title="<? echo $current_stock;?>" id="stock_qty_<? echo $i; ?>" width="70"><? echo number_format($store_stock,2,'.',''); ?></td>
                            <td><input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" value="<? echo $txt_remarks; ?>" style="width:60px"></td>

                            <td align="center"><input type="checkbox" id="chek_<? echo $i;?>" value="<? echo $checked; ?>" <? echo $check_sts; ?> onChange="fnc_chk_status(<? echo $i;?>);">
                            <input type="hidden" id="chek_id_<? echo $i;?>" name="chek_id_[]"/ value="<? echo $checked; ?>">
                            </td>
                        </tr>
						<?
						//$max_seq_no[]=$selectResult[csf('seq_no')];
						$i++;
					}
					//}
				}
				else 				//Wash End....
				    {
                        $i=1;
                        if(count($recipe_prod_id_arr)>0)
						{
							foreach($recipe_prod_id_arr as $prodId)
							{
								if($variable_lot==1)
								{
									$lot_popup='';
									$place_holder='';
								}
								else
								{
									$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
									$place_holder='Browse';
								}
								$prodId_ref=explode("_",$prodId);
								$product_id=$prodId_ref[0];
								$str_id=$prodId_ref[1];
								$product_lot=$prodId_ref[2];
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$prodData=explode("**",$product_data_arr[$prodId]);
								$item_category_id=$prodData[0];
								$item_group_id=$prodData[1];
								$sub_group_name=$prodData[2];
								$item_description=$prodData[3];
								$item_size=$prodData[4];
								$unit_of_measure=$prodData[5];
								$current_stock=$prodData[6];
								$store_stock=$prodData[7];

								$disbled=""; $disbled2="disabled='disabled'"; $disbled3="disabled='disabled'"; $disbledDropdown=1; $disbledDropdown2=0;
								$prev_recipe_qty=0; $recipe_qty=0; $seq_no='';
								//$ratio=$selectResult[csf('ratio')];
								$item_lot=$recipe_data_arr[$prodId][1];
								$ratio=$recipe_data_arr[$prodId][3];
								$mst_id=$recipe_data_arr[$prodId][5];
								$dtls_id=$recipe_data_arr[$prodId][6];
								$adj_type=$recipe_data_arr[$prodId][7];
								$adj_perc=$recipe_data_arr[$prodId][8];
								$adj_qnty=$recipe_data_arr[$prodId][9];
								$new_item=$recipe_data_arr[$prodId][10];
								$adj_ratio=$recipe_data_arr[$prodId][11];
								$txt_remarks=$recipe_data_arr[$prodId][12];
								$checked=$recipe_data_arr[$prodId][13];

								//if($selectResult[csf('new_item')]==0 && $selectResult[csf('mst_id')]>0)
								if($new_item==0 && $mst_id>0)
								{
									$ratio=$ratio_arr[$product_id];
									//echo "A".',';
								}

								if($ratio>0)
								{
									//$selected_dose=$selectResult[csf('dose_base')];
									//$seq_no=$selectResult[csf('seq_no')];
									$selected_dose=$recipe_data_arr[$prodId][2];
									$seq_no=$recipe_data_arr[$prodId][4];
									$bgcolor="yellow";

									$disbled="disabled='disabled'";
									$disbledDropdown=0;
									$disbledDropdown2=1;
									$disbled2="";
									$disbled3="";

									//if($selectResult[csf('new_item')]==0 || $selectResult[csf('new_item')]=="")
									if($new_item==0 || $new_item=="")
									{
										if($selected_dose==1)
										{
											$recipe_qty=number_format(($total_liquor*$ratio)/1000,6,".","");
											$prev_recipe_qty=number_format(($actual_total_liquor*$ratio)/1000,6,".","");
											//echo $prev_recipe_qty.'A';
										}
										else if($selected_dose==2)
										{
											$recipe_qty=number_format(($batch_weight*$ratio)/100,6,".","");
											$prev_recipe_qty=number_format(($actual_batch_weight*$ratio)/100,6,".","");
											$disbled3="disabled='disabled'";
											//echo $prev_recipe_qty.'B,';
										}
									}
									else
									{
										$disbledDropdown2=0;
										$disbledDropdown=1;
										$disbled2="disabled='disabled'";
										$disbled="";
										$recipe_qty=0;
										$recipe_qty=number_format(($total_liquor*$ratio)/1000,6,".","");
										$prev_recipe_qty=number_format(($actual_total_liquor*$ratio)/1000,6,".","");
										//echo $recipe_qty.'C,';
									}
									//echo 'a';
								}
								else
								{
									if($item_category_id==6) $selected_dose=2;
									else $selected_dose=1;
								}

								$iss_qty=$iss_arr[$product_id];
								if($update_id!="" && $ratio>0 && $iss_qty>0)
								{
									$disbled="disabled='disabled'";
								}
								//is_checked value
								if($checked==1)
								{
									$check_sts='checked';
								}
								else
								{
									$check_sts="";
								}
								//echo $ratio.'FF';
								//if($ratio==0 || $ratio=='')
								//{
									$ratio_fnc="calculate($i,'txt_ratio_')";
								//}
								//else
								//{
								//	$ratio_fnc="";
								//}
								$title_ratio="GPLL=Adj Qty*1000/Total Liquor(ltr)".' and '."% On BW==Adj Qty*100/Batch Weight";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
									<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
									<td width="80" title="<? echo $item_category_id;?>" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
									<td width="80" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
									<td width="100" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
									<td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" style="width:68px" <? echo $lot_popup; ?>  placeholder="<? echo $place_holder; ?>" value="<? echo $product_lot; ?>" readonly>
									</td>
									<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
									<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose,"",$disbledDropdown2); ?></td>
									<td width="55" align="center" id="ratio_<? echo $i; ?>" title="<? echo 'Ratio='.$ratio.', '.$title_ratio;?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo number_format($ratio,6,".",""); ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>);auto_put_check(<? echo $i; ?>);<? echo $ratio_fnc;?>  " <? echo $disbled; ?>></td>
									<td width="68" style="padding-right:2px" align="right" id="prev_recipe_qty_<? echo $i; ?>"><? echo $prev_recipe_qty; ?></td>
									<td width="68" style="padding-right:2px" align="right" id="recipe_qty_<? echo $i; ?>"><? echo $recipe_qty; ?></td>
									<td width="75" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 70, $increase_decrease, "", 1, "- Select -",0,"",1);//calculate($i,'cbo_adj_type_')$disbledDropdown $selectResult[csf('adj_type')] ?></td>
									<td width="57" align="center" id="adj_ratio_<? echo $i; ?>"><input type="text" name="txt_adj_ratio[]" id="txt_adj_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $adj_ratio; ?>" onKeyUp="calculate(<? echo $i; ?>,'txt_adj_ratio_')" <? echo $disbled3; ?>> <input type="hidden" name="txt_hiddadj_ratio[]" id="txt_hiddadj_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $adj_ratio; ?>"/></td>
									<td width="53" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $adj_perc; ?>" onKeyUp="calculate(<? echo $i; ?>,'txt_adj_per_')" <? echo $disbled2; ?>></td>
									<td width="72"><input type="text" name="adj_qnty[]" id="adj_qnty_<? echo $i;?>" class="text_boxes_numeric" value="<? echo number_format($adj_qnty,6,'.',''); ?>" onKeyUp="calculate(<? echo $i; ?>,'adj_qnty_')" style="width:62px" <? echo $disbled2; ?> ><input type="hidden" name="hiddenadj_qnty[]" id="hiddenadj_qnty_<? echo $i;?>" class="text_boxes_numeric" value="<? echo number_format($adj_qnty,6,'.',''); ?>"   style="width:62px"/></td>
									<td width="45" align="center" id="seqno_<? echo $i; ?>">
                                    	<input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);">
                                    	<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>">
										<input type="hidden" name="product_id[]" id="product_id_<? echo $i; ?>" value="<? echo $product_id; ?>">
                                    </td>
									<td width="90" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p></td>
									<td width="50" align="center" id="prod_id_<? echo $i; ?>"><? echo $product_id; ?></td>
									<td align="right" title="<? echo $current_stock;?>" id="stock_qty_<? echo $i; ?>" width="70"><? echo number_format($store_stock,2,'.',''); ?></td>
                                    <td><input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" value="<? echo $txt_remarks; ?>" style="width:60px"></td>
                                    <td align="center"><input type="checkbox" id="chek_<? echo $i;?>" value="<? echo $checked; ?>" <? echo $check_sts; ?> onChange="fnc_chk_status(<? echo $i;?>);">
                                    <input type="hidden" id="chek_id_<? echo $i;?>" name="chek_id_[]"/ value="<? echo $checked; ?>">
                                    </td>
								</tr>
								<?
								$i++;
							}
						}

						foreach($product_data_arr as $prodId=>$data)
						{
							if(!in_array($prodId,$recipe_prod_id_arr))
							{
								if($variable_lot==1)
								{
									$lot_popup='';
									$place_holder='';
								}
								else
								{
									$lot_popup='onDblClick="openmypage_itemLot('.$i.')"';
									$place_holder='Browse';
								}

								$prodId_ref=explode("_",$prodId);
								$product_id=$prodId_ref[0];
								$str_id=$prodId_ref[1];
								$product_lot=$prodId_ref[2];
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$prodData=explode("**",$product_data_arr[$prodId]);
								$item_category_id=$prodData[0];
								$item_group_id=$prodData[1];
								$sub_group_name=$prodData[2];
								$item_description=$prodData[3];
								$item_size=$prodData[4];
								$unit_of_measure=$prodData[5];
								$current_stock=$prodData[6];
								$store_stock=$prodData[7];

								$disbled=""; $disbled2="disabled='disabled'"; $disbledDropdown=1; $disbledDropdown2=0; $prev_recipe_qty=0; $recipe_qty=0; $seq_no='';
								//$ratio=$selectResult[csf('ratio')];
								$item_lot=$recipe_data_arr[$prodId][1];
								$ratio=$recipe_data_arr[$prodId][3];
								$mst_id=$recipe_data_arr[$prodId][5];
								$dtls_id=$recipe_data_arr[$prodId][6];
								$adj_type=$recipe_data_arr[$prodId][7];
								$adj_perc=$recipe_data_arr[$prodId][8];
								$adj_qnty=$recipe_data_arr[$prodId][9];
								$new_item=$recipe_data_arr[$prodId][10];
								$adj_ratio=$recipe_data_arr[$prodId][11];
								$txt_remarks=$recipe_data_arr[$prodId][12];
								$checked=$recipe_data_arr[$prodId][13];

								//if($selectResult[csf('new_item')]==0 && $selectResult[csf('mst_id')]>0)
								if($new_item=="" || $new_item==0) //for Cotton Club,Issue id-1625, for New subprocess add
								{
									$disbled2="";
								}
								if($new_item==0 && $mst_id>0)
								{
									$ratio=$ratio_arr[$product_id];
								}
							 // echo $disbled2."=A=".$ratio;
								if($ratio>0)
								{
									//$selected_dose=$selectResult[csf('dose_base')];
									//$seq_no=$selectResult[csf('seq_no')];
									$selected_dose=$recipe_data_arr[$prodId][2];
									$seq_no=$recipe_data_arr[$prodId][4];
									$bgcolor="yellow";

									$disbled="disabled='disabled'";
									$disbledDropdown=0;
									$disbledDropdown2=1;
									$disbled2="";

									//if($selectResult[csf('new_item')]==0 || $selectResult[csf('new_item')]=="")
									if($new_item==0 || $new_item=="")
									{
										if($selected_dose==1)
										{
											$recipe_qty=number_format(($total_liquor*$ratio)/1000,6,".","");
											$prev_recipe_qty=number_format(($actual_total_liquor*$ratio)/1000,6,".","");
										}
										else if($selected_dose==2)
										{
											$recipe_qty=number_format(($batch_weight*$ratio)/100,6);
											$prev_recipe_qty=number_format(($actual_batch_weight*$ratio)/100,6,".","");
										}
									}
									else
									{
										$disbledDropdown2=0;
										$disbledDropdown=1;
										$disbled2="disabled='disabled'";
										$disbled="";

									}
								}
								else
								{
									if($item_category_id==6) $selected_dose=2;
									else $selected_dose=1;
								}
								$iss_qty=$iss_arr[$product_id];
								if($update_id!="" && $ratio>0 && $iss_qty>0)
								{
									$disbled="disabled='disabled'";
								}
								
								$ratio_fnc="calculate($i,'txt_ratio_')";
								if( number_format($store_stock,2,'.','')>0)
								{
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
										<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
										<td width="80" id="item_category_<? echo $i; ?>"><p><? echo $item_category[$item_category_id]; ?></p></td>
										<td width="80" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
										<td width="100" id="item_description_<? echo $i; ?>"><p><? echo $item_description." ".$item_size; ?></p></td>
										<td width="80" id="item_lot_<? echo $i; ?>"><input type="text" name="txt_item_lot[]" id="txt_item_lot_<? echo $i; ?>" class="text_boxes" <? echo $lot_popup; ?> style="width:68px"  placeholder="<? echo $place_holder; ?>" value="<? echo $product_lot; ?>" readonly>
										</td>
										<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
										<td width="70" align="center" id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 68, $dose_base, "", 1, "-Select Dose Base-",$selected_dose,"",$disbledDropdown2); ?></td>
										<td width="55" align="center" id="ratio_<? echo $i; ?>"><input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $ratio; ?>" onBlur="color_row(<? echo $i; ?>); seq_no_val(<? echo $i; ?>);auto_put_check(<? echo $i; ?>); <? echo $ratio_fnc;?> " <? echo $disbled; ?>></td>
										<td width="68" style="padding-right:2px" align="right" id="prev_recipe_qty_<? echo $i; ?>"><? echo $prev_recipe_qty; ?></td>
										<td width="68" style="padding-right:2px" align="right" id="recipe_qty_<? echo $i; ?>"><? echo $recipe_qty; ?></td>
										<td width="75" align="center" id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 70, $increase_decrease, "", 1, "- Select -",0,"",1);//calculate($i,'cbo_adj_type_')$disbledDropdown $selectResult[csf('adj_type')] ?></td>
										<td width="57" align="center" id="adj_ratio_<? echo $i; ?>"><input type="text" name="txt_adj_ratio[]" id="txt_adj_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $adj_ratio; ?>" onKeyUp="calculate(<? echo $i; ?>,'txt_adj_ratio_')" <? echo $disbled2; ?>><input type="text" name="txt_hiddadj_ratio[]" id="txt_hiddadj_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $adj_ratio; ?>"  /></td>
										<td width="53" align="center" id="adj_per_<? echo $i; ?>"><input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $adj_perc; ?>" onKeyUp="calculate(<? echo $i; ?>,'txt_adj_per_')" <? echo $disbled2; ?>></td>
										<td width="72"><input type="text" name="adj_qnty[]" id="adj_qnty_<? echo $i;?>" class="text_boxes_numeric" value="<? echo $adj_qnty; ?>" onKeyUp="calculate(<? echo $i; ?>,'adj_qnty_')" style="width:62px" <? echo $disbled2; ?>>
										<input type="text" name="hiddenadj_qnty[]" id="hiddenadj_qnty_<? echo $i;?>" class="text_boxes_numeric" value="<? echo $adj_qnty; ?>"  style="width:62px" /></td>
										<td width="45" align="center" id="seqno_<? echo $i; ?>">
											<input type="text" name="txt_seqno[]" id="txt_seqno_<? echo $i; ?>" class="text_boxes_numeric" style="width:30px" value="<? echo $seq_no; ?>" onBlur="row_sequence(<? echo $i; ?>);">
											<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>">
											<input type="hidden" name="product_id[]" id="product_id_<? echo $i; ?>" value="<? echo $product_id; ?>">
										</td>
										<td width="90" id="sub_process_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$sub_process_id]; ?></p></td>
										<td width="50" align="center" id="prod_id_<? echo $i; ?>"><? echo $product_id; ?></td>
										<td align="right" width="70" title="<? echo $current_stock;?>" id="stock_qty_<? echo $i; ?>"><? echo number_format($store_stock,2,'.',''); ?></td>
										<td><input type="text" name="txt_remarks[]" id="txt_remarks_<? echo $i;?>" class="text_boxes" value="<? echo $txt_remarks; ?>" style="width:60px"></td>
										 <td align="center"><input type="checkbox" id="chek_<? echo $i;?>" value="<? echo $checked; ?>">
										<input type="hidden" id="chek_id_<? echo $i;?>" name="chek_id_[]"  value="1" />
										</td>
									</tr>
									<?
									$i++;
								}
							}
						}
				  }
                    ?>
           		</tbody>
            </table>
        </div>
	</div>
<?
	exit();
}

if ($action=="itemLot_popup")
{
	echo load_html_head_contents("Item Lot Info", "../../", 1, 1,'',1,'');
	extract($_REQUEST);
?>
	<script>
	var selected_id = new Array();

	function toggle( x, origColor )
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		str=str[1];

		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
		}
		var id = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		$('#item_lot').val( id );
	}
    </script>
    <input type="hidden" id="item_lot" />
    <?
	if($db_type==0)
	{
		$sql="SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot!=''";
	}
	else if($db_type==2)
	{
		$sql="SELECT distinct batch_lot from inv_transaction where prod_id='$txt_prod_id' and status_active=1 and is_deleted=0 and batch_lot is not null";
	}
	//echo $sql;

	echo create_list_view("list_view", "Item Lot", "200","330","250",0, $sql , "js_set_value", "batch_lot", "", 1, "", 0 , "batch_lot", "recipe_entry_controller",'setFilterGrid("list_view",-1);','0','',1) ;
	die;
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$recipe_update_id='';
		$subprocess=str_replace("'","",$cbo_sub_process);
		$cbo_machine_name=str_replace("'","",$cbo_machine_name);
		$dyeing_re_process=str_replace("'","",$cbo_dyeing_re_process);
		if($dyeing_re_process==2 || $dyeing_re_process==1) //Adding Topping
		{
			if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=" . $txt_batch_id . " and load_unload_id=2  and status_active=1 and is_deleted=0 and result in(1,11)") == 1)
			{
				echo "13**0**".str_replace("'","",$txt_batch_id);
				disconnect($con);
				die;
			}
		}

		if(str_replace("'","",$update_id)=="")
		{
			$id=return_next_id( "id","pro_recipe_entry_mst", 1 ) ;

			$field_array="id, entry_form, dyeing_re_process, recipe_id,copy_from, labdip_no, company_id,working_company_id, location_id, recipe_description, batch_id, new_batch_weight, method, recipe_date, order_source, style_or_order, booking_id, color_id, buyer_id, color_range, total_liquor, batch_ratio, liquor_ratio, remarks, pickup, surplus_solution, sub_tank, ready_to_approve,machine_id,in_charge_id, inserted_by, insert_date";
			//echo $txt_liquor;
			$data_array="(".$id.",60,".$cbo_dyeing_re_process.",".$txt_recipe_no.",".$txt_copy_from.",".$txt_labdip_no.",".$cbo_lc_company_id.",".$cbo_company_id.",".$cbo_location.",".$txt_recipe_des.",".$txt_batch_id.",".$txt_batch_weight.",".$cbo_method.",".$txt_recipe_date.",".$cbo_order_source.",".$txt_booking_no.",".$txt_booking_id.",".$txt_color_id.",".$cbo_buyer_name.",".$cbo_color_range.",".$txt_liquor.",".$txt_batch_ratio.",".$txt_liquor_ratio.",".$txt_remarks.",".$txt_pick_up.",".$surpls_solution.",".$txt_sub_tank.",".$cbo_ready_to_approve.",".$cbo_machine_name.",".$txt_in_charge_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$recipe_update_id=$id;//copy_from
		}
		else
		{
			if(is_duplicate_field( "sub_process_id", "pro_recipe_entry_dtls", "mst_id=$update_id and sub_process_id=$cbo_sub_process and status_active=1" )==1)
			{
				echo "11**0";
				disconnect($con);
				die;
			}

			$field_array_update="recipe_id*labdip_no*location_id*recipe_description*batch_id*new_batch_weight*method*recipe_date*order_source*style_or_order*color_id*buyer_id*color_range*booking_id*total_liquor*batch_ratio*liquor_ratio*pickup*surplus_solution*sub_tank*remarks*in_charge_id*updated_by*update_date";

			$data_array_update=$txt_recipe_no."*".$txt_labdip_no."*".$cbo_location."*".$txt_recipe_des."*".$txt_batch_id."*".$txt_batch_weight."*".$cbo_method."*".$txt_recipe_date."*".$cbo_order_source."*".$txt_booking_no."*".$txt_color_id."*".$cbo_buyer_name."*".$cbo_color_range."*".$txt_booking_id."*".$txt_liquor."*".$txt_batch_ratio."*".$txt_liquor_ratio."*".$txt_pick_up."*".$surpls_solution."*".$txt_sub_tank."*".$txt_remarks."*".$txt_in_charge_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$recipe_update_id=str_replace("'","",$update_id);
		}


			/*$sub_seq_no =return_field_value("max(a.sub_seq) as sub_seq","pro_recipe_entry_dtls a"," a.mst_id=" . $update_id . " and a.status_active=1 and a.is_deleted=0", "sub_seq");
			$sub_seq=$sub_seq_no+1;*/


	//	if($subprocess==93 || $subprocess==94 || $subprocess==95 || $subprocess==96 || $subprocess==97 || $subprocess==98 || $subprocess ==140 || $subprocess ==141 || $subprocess ==142 || $subprocess ==143 )
if (str_replace("'", "", $copy_id) == 2)
{
		if(in_array($subprocess, $subprocessForWashArr))
		{
				$txt_remarks_1=str_replace("'","",$txt_remarks_1);
				$txt_ratio_1=str_replace("'","",$txt_ratio_1);
				$txt_seqno_1=1;
				$cbo_dose_base_1=str_replace("'","",$cbo_dose_base_1);

				$field_array_dtls="id,mst_id,sub_process_id,store_id,process_remark,comments,liquor_ratio,total_liquor,ratio,seq_no,sub_seq,dose_base,check_id,inserted_by,insert_date";
				$dtls_id=return_next_id( "id","pro_recipe_entry_dtls", 1 ) ;
				$data_array_dtls="(".$dtls_id.",".$recipe_update_id.",".$cbo_sub_process.",".$cbo_store_name.",".$txt_subprocess_remarks.",'".$txt_remarks_1."',".$txt_liquor_ratio_dtls.",".$txt_total_liquor_ratio.",'".$txt_ratio_1."','".$txt_seqno_1."',".$txt_subprocess_seq.",'".$cbo_dose_base_1."','".str_replace("'","",$check_id)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
		}
		else
		{
				$field_array_dtls="id,mst_id,sub_process_id,store_id,prod_id,item_lot,dose_base,ratio,seq_no,sub_seq,adj_type,adj_ratio,adj_perc,adj_qnty,new_item,new_batch_weight,new_total_liquor,comments,process_remark,liquor_ratio,total_liquor,is_checked,check_id,inserted_by,insert_date";
				 
				$dtls_id=return_next_id( "id","pro_recipe_entry_dtls", 1 ) ;

				$txt_batch_weight=str_replace("'","",$txt_batch_weight);
				$txt_liquor=str_replace("'","",$txt_liquor);
				$total_liquor_ratio=str_replace("'","",$txt_total_liquor_ratio);

				for($i=1;$i<=$total_row;$i++)
				{
					$product_id="product_id_".$i;
					$txt_item_lot="txt_item_lot_".$i;
					$cbo_dose_base="cbo_dose_base_".$i;
					$txt_ratio="txt_ratio_".$i;
					$txt_seqno="txt_seqno_".$i;
					$recipe_qty="recipe_qty_".$i;
					$cbo_adj_type="cbo_adj_type_".$i;
					$txt_adj_per="txt_adj_per_".$i;
					$adj_qnty="adj_qnty_".$i;
					$adj_ratio="txt_adj_ratio_".$i;
					$txt_remarks="txt_remarks_".$i;
					$checked="chek_id_".$i;
					$tot_qty=0; $new_item=1;

					if(str_replace("'","",$$adj_qnty)>0)
					{
						/*if(str_replace("'","",$$cbo_adj_type)==1) $tot_qty=str_replace("'","",$$recipe_qty)+str_replace("'","",$$adj_qnty);
						else if(str_replace("'","",$$cbo_adj_type)==2) $tot_qty=str_replace("'","",$$recipe_qty)-str_replace("'","",$$adj_qnty);
						else $tot_qty=str_replace("'","",$$recipe_qty);*/

						$tot_qty=str_replace("'","",$$adj_qnty);
						if(str_replace("'","",$$cbo_dose_base)==1)
						{
							$ratio=number_format(($tot_qty*1000)/$total_liquor_ratio,6,'.','');
						}
						else if(str_replace("'","",$$cbo_dose_base)==2)
						{
							$ratio=number_format(($tot_qty*100)/$txt_batch_weight,6,'.','');
						}
						$new_item=0;
					}
					else $ratio=str_replace("'","",$$txt_ratio);

					if ($data_array_dtls!="") $data_array_dtls .=",";
					$data_array_dtls.="(".$dtls_id.",".$recipe_update_id.",".$cbo_sub_process.",".$cbo_store_name.",'".str_replace("'","",$$product_id)."','".str_replace("'","",$$txt_item_lot)."','".str_replace("'","",$$cbo_dose_base)."','".$ratio."','".str_replace("'","",$$txt_seqno)."',".$txt_subprocess_seq.",'".str_replace("'","",$$cbo_adj_type)."','".str_replace("'","",$$adj_ratio)."','".str_replace("'","",$$txt_adj_per)."','".str_replace("'","",$$adj_qnty)."',".$new_item.",".$txt_batch_weight.",".$txt_liquor.",'".str_replace("'","",$$txt_remarks)."','".str_replace("'","",$txt_subprocess_remarks)."',".$txt_liquor_ratio_dtls.",".$txt_total_liquor_ratio.",'".str_replace("'","",$$checked)."','" . str_replace("'", "", $check_id) . "',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					$dtls_id=$dtls_id+1;
		}
	}
}
else
{
	
	//$field_array_dtls = "id,mst_id,sub_process_id,store_id,process_remark,prod_id,item_lot,comments,liquor_ratio,total_liquor,dose_base,check_id,ratio,seq_no,sub_seq,inserted_by,insert_date";
	
//	$field_array_dtls="id,mst_id,sub_process_id,store_id,prod_id,item_lot,dose_base,ratio,seq_no,sub_seq,adj_type,adj_ratio,adj_perc,adj_qnty,new_item,new_batch_weight,new_total_liquor,comments,process_remark,liquor_ratio,total_liquor,is_checked,check_id,inserted_by,insert_date";
	$field_array_dtls="id,mst_id,sub_process_id,store_id,prod_id,item_lot,comments,dose_base,ratio,seq_no,sub_seq,adj_type,adj_ratio,adj_perc,adj_qnty,new_item,new_batch_weight,new_total_liquor,process_remark,liquor_ratio,total_liquor,is_checked,check_id,inserted_by,insert_date";

	$dtls_id = return_next_id("id", "pro_recipe_entry_dtls", 1);
	$sql = "select id,mst_id,sub_process_id,store_id,prod_id,item_lot,dose_base,ratio,seq_no,sub_seq,adj_type,adj_ratio,adj_perc,adj_qnty,new_item,new_batch_weight,new_total_liquor,comments,process_remark,liquor_ratio,total_liquor,is_checked,check_id from pro_recipe_entry_dtls where mst_id=$update_id_check  and status_active=1  order by id";
	//echo "10**".$sql;die;
	$nameArray = sql_select($sql);
	$tot_row = count($nameArray);
	$i = 1;

	foreach ($nameArray as $row)
	{
		 
		if($row[csf('is_checked')]=='' || $row[csf('is_checked')]==0 ) $row[csf('is_checked')]=1;

		$process_remark=str_replace("'", "", $row[csf('process_remark')]);
		if ($i != 1) $data_array_dtls .= ",";
		$data_array_dtls .= "(" . $dtls_id . "," . $recipe_update_id . ",'" . $row[csf('sub_process_id')] . "','" . $row[csf('store_id')] . "','" . $row[csf('prod_id')] . "','" . $row[csf('item_lot')] . "','" . $row[csf('comments')] . "','" . $row[csf('dose_base')] . "','" . $row[csf('ratio')] . "','" . $row[csf('seq_no')] . "','" . $row[csf('sub_seq')] . "','" . $row[csf('adj_type')] . "','" . $row[csf('adj_ratio')] . "','" . $row[csf('adj_perc')] . "','" . $row[csf('adj_qnty')] . "','" . $row[csf('new_item')] . "','" . $row[csf('new_batch_weight')] . "','" . $row[csf('new_total_liquor')] . "','" . $process_remark . "','" . $row[csf('liquor_ratio')] . "','" . $row[csf('total_liquor')] . "','" . $row[csf('is_checked')] . "','" . $row[csf('check_id')] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
		$dtls_id = $dtls_id + 1;
		$i++;
	}

}
		 //echo "10**insert into pro_recipe_entry_mst (".$field_array.") values ".$data_array;die;

		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("pro_recipe_entry_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		//oci_rollback($con);
		//echo "10**".$sql;die;
		//echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID2=sql_insert("pro_recipe_entry_dtls",$field_array_dtls,$data_array_dtls,1);

		//  echo "10**".$rID."&&".$rID2;die;
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");
				echo "0**".$recipe_update_id."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**".$recipe_update_id."**0";
			}
			else
			{	oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$dyeing_re_process=str_replace("'","",$cbo_dyeing_re_process);
		if($dyeing_re_process==2 || $dyeing_re_process==1) //Adding Topping
		{
			if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=" . $txt_batch_id . " and load_unload_id=2  and status_active=1 and is_deleted=0 and result in(1,11)") == 1)
			{
				echo "13**0**".str_replace("'","",$txt_batch_id);
				disconnect($con);
				die;
			}
		}
		$sql_app=sql_select("select approved from pro_recipe_entry_mst where id=$update_id and status_active=1");
		$approved_id=$sql_app[0][csf("approved")];
		if($approved_id==1 || $approved_id==3)
		{
			if($approved_id==1)
			{
				$app_msg="Approved";
			}
			else if($approved_id==3)
			{
				$app_msg="Partial Approved";
			}
				echo "15**0**".$app_msg;
				disconnect($con);
				die;
			 
		}
		 
		else $app_msg="";

		$field_array_update="recipe_id*labdip_no*location_id*recipe_description*batch_id*new_batch_weight*method*recipe_date*order_source*style_or_order*color_id*buyer_id*color_range*booking_id*total_liquor*batch_ratio*liquor_ratio*remarks*pickup*surplus_solution*sub_tank*ready_to_approve*machine_id*in_charge_id*updated_by*update_date";

		$data_array_update=$txt_recipe_no."*".$txt_labdip_no."*".$cbo_location."*".$txt_recipe_des."*".$txt_batch_id."*".$txt_batch_weight."*".$cbo_method."*".$txt_recipe_date."*".$cbo_order_source."*".$txt_booking_no."*".$txt_color_id."*".$cbo_buyer_name."*".$cbo_color_range."*".$txt_booking_id."*".$txt_liquor."*".$txt_batch_ratio."*".$txt_liquor_ratio."*".$txt_remarks."*".$txt_pick_up."*".$surpls_solution."*".$txt_sub_tank."*".$cbo_ready_to_approve."*".$cbo_machine_name."*".$txt_in_charge_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$subprocess=str_replace("'","",$cbo_sub_process);

	// if($subprocess==93 || $subprocess==94 || $subprocess==95 || $subprocess==96 || $subprocess==97 || $subprocess==98 || $subprocess ==140 || $subprocess ==141 || $subprocess ==142 || $subprocess ==143)
	if(in_array($subprocess, $subprocessForWashArr))
	  {

		$field_array_dtls_update2="sub_process_id*process_remark*comments*liquor_ratio*total_liquor*is_checked*check_id*ratio*seq_no*dose_base*updated_by*update_date"; //,".$txt_liquor_ratio_dtls.",".$txt_total_liquor_ratio."
		$data_array_dtls_update2=$subprocess."*".$txt_subprocess_remarks."*".$txt_remarks_1."*".$txt_liquor_ratio_dtls."*".$txt_total_liquor_ratio."*".$chek_id_1."*'" . str_replace("'", "", $check_id) . "'*".$txt_ratio_1."*".$txt_seqno_1."*".$cbo_dose_base_1."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$update_dtls_id=str_replace("'","",$updateIdDtls_1);

	  }
	  else
	  {

		$field_array_dtls="id,mst_id,sub_process_id,store_id,prod_id,item_lot,dose_base,ratio,seq_no,sub_seq,adj_type,adj_ratio,adj_perc,adj_qnty,new_item,new_batch_weight,new_total_liquor,comments,process_remark,liquor_ratio,total_liquor,is_checked,check_id,inserted_by,insert_date";
		$field_array_dtls_update="prod_id*item_lot*dose_base*ratio*seq_no*sub_seq*sub_process_id*adj_type*adj_ratio*adj_perc*adj_qnty*new_batch_weight*new_total_liquor*comments*liquor_ratio*total_liquor*is_checked*check_id*process_remark*updated_by*update_date";
		$dtls_id=return_next_id( "id","pro_recipe_entry_dtls", 1 ) ;

		$txt_batch_weight=str_replace("'","",$txt_batch_weight);
		$txt_liquor=str_replace("'","",$txt_liquor);
		$total_liquor_ratio=str_replace("'","",$txt_total_liquor_ratio);

		for($i=1;$i<=$total_row;$i++)
		{
			$product_id="product_id_".$i;
			$txt_item_lot="txt_item_lot_".$i;
			$cbo_dose_base="cbo_dose_base_".$i;
			$txt_ratio="txt_ratio_".$i;
			$txt_seqno="txt_seqno_".$i;
			$recipe_qty="recipe_qty_".$i;
			$cbo_adj_type="cbo_adj_type_".$i;
			$txt_adj_per="txt_adj_per_".$i;
			$adj_qnty="adj_qnty_".$i;
			$adj_ratio="txt_adj_ratio_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$txt_remarks="txt_remarks_".$i;
			$checked="chek_id_".$i;

			$tot_qty=0; $new_item=1;
			if(str_replace("'","",$$adj_qnty)>0)
			{
				/*if(str_replace("'","",$$cbo_adj_type)==1) $tot_qty=str_replace("'","",$$recipe_qty)+str_replace("'","",$$adj_qnty);
				else if(str_replace("'","",$$cbo_adj_type)==2) $tot_qty=str_replace("'","",$$recipe_qty)-str_replace("'","",$$adj_qnty);
				else $tot_qty=str_replace("'","",$$recipe_qty);*/

				$tot_qty=str_replace("'","",$$adj_qnty);
				if(str_replace("'","",$$cbo_dose_base)==1)
				{
					$ratio=number_format(($tot_qty*1000)/$total_liquor_ratio,6,'.','');
				}
				else if(str_replace("'","",$$cbo_dose_base)==2)
				{
					$ratio=number_format(($tot_qty*100)/$txt_batch_weight,6,'.','');
				}
				$new_item=0;
			}
			else $ratio=str_replace("'","",$$txt_ratio);$checkID=str_replace("'","",$$checked);

			if(str_replace("'","",$$updateIdDtls)!="") //*".$txt_liquor_ratio_dtls."*".$txt_total_liquor_ratio."
			{
				$id_arr[]=str_replace("'",'',$$updateIdDtls);
				$data_array_dtls_update[str_replace("'",'',$$updateIdDtls)] = explode("*",(str_replace("'","",$$product_id)."*'".str_replace("'","",$$txt_item_lot)."'*'".str_replace("'","",$$cbo_dose_base)."'*'".$ratio."'*'".str_replace("'","",$$txt_seqno)."'*".$txt_subprocess_seq."*".$cbo_sub_process."*'".str_replace("'","",$$cbo_adj_type)."'*'".str_replace("'","",$$adj_ratio)."'*'".str_replace("'","",$$txt_adj_per)."'*'".str_replace("'","",$$adj_qnty)."'*'".$txt_batch_weight."'*'".$txt_liquor."'*'".str_replace("'","",$$txt_remarks)."'*".$txt_liquor_ratio_dtls."*".$txt_total_liquor_ratio."*'".str_replace("'","",$$checked)."'*'" . str_replace("'", "", $check_id) . "'*".$txt_subprocess_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'")); //chek_id_
			}
			else
			{
				if($data_array_dtls!="") $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$cbo_sub_process.",".$cbo_store_name.",'".str_replace("'","",$$product_id)."','".str_replace("'","",$$txt_item_lot)."','".str_replace("'","",$$cbo_dose_base)."','".$ratio."','".str_replace("'","",$$txt_seqno)."',".$txt_subprocess_seq.",'".str_replace("'","",$$cbo_adj_type)."','".str_replace("'","",$$adj_ratio)."','".str_replace("'","",$$txt_adj_per)."','".str_replace("'","",$$adj_qnty)."',".$new_item.",".$txt_batch_weight.",".$txt_liquor.",'".str_replace("'","",$$txt_remarks)."','".str_replace("'","",$txt_subprocess_remarks)."',".$txt_liquor_ratio_dtls.",".$txt_total_liquor_ratio.",'".$checkID."','" . str_replace("'", "", $check_id) . "',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				$dtls_id=$dtls_id+1;
			}
		}
	  }
		//echo "10**";die;
		// Update
		$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		if($data_array_dtls_update2!="")
		{
			$rID=sql_update("pro_recipe_entry_dtls",$field_array_dtls_update2,$data_array_dtls_update2,"id",$update_dtls_id,1);
			if($rID) $flag=1; else $flag=0;
		}
		if(count($data_array_dtls_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ),1);
			//echo bulk_update_sql_statement( "pro_recipe_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		if($data_array_dtls!="")
		{
			//echo "10**insert into pro_recipe_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
			$rID2=sql_insert("pro_recipe_entry_dtls",$field_array_dtls,$data_array_dtls,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		if($db_type==0)
		{
			$reqsn_update=execute_query("update dyes_chem_issue_requ_mst a, dyes_chem_requ_recipe_att b set a.is_apply_last_update=2, a.updated_by=".$_SESSION['logic_erp']['user_id'].", a.update_date='".$pc_date_time."' where a.id=b.mst_id and b.recipe_id=".$update_id);
		}
		else
		{
			$reqsn_update=execute_query("update dyes_chem_issue_requ_mst a set a.is_apply_last_update=2, a.updated_by=".$_SESSION['logic_erp']['user_id'].", a.update_date='".$pc_date_time."' where exists( select b.mst_id from dyes_chem_requ_recipe_att b where a.id=b.mst_id and b.recipe_id=".$update_id.")");
		}

		$reqsn_update_att=execute_query("update dyes_chem_requ_recipe_att set is_apply_last_update=2 where recipe_id=".$update_id);

		if($flag==1)
		{
			if($reqsn_update && $reqsn_update_att)
			{
				$flag=1;
			}
			else
			{
				$flag=0;
			}
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$dyeing_re_process=str_replace("'","",$cbo_dyeing_re_process);
		if($dyeing_re_process==2)
		{
			if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=" . $txt_batch_id . " and load_unload_id=2  and status_active=1 and is_deleted=0") == 1)
			{
				echo "13**0**".str_replace("'","",$txt_batch_id);
				disconnect($con);
				die;
			}
		}

		$rID = sql_delete("pro_recipe_entry_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id*sub_process_id',$update_id."*".$cbo_sub_process,1);

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "7**".str_replace("'", '', $update_id)."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "7**".str_replace("'", '', $update_id)."**0";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="systemid_popup")
{
	echo load_html_head_contents("Recipe Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{
			$('#hidden_update_id').val(id);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center" style="width:100%;">
    <form name="searchlabdipfrm" id="searchlabdipfrm">
        <fieldset style="width:920px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="800" class="rpt_table">
                <thead>
                 	<tr>
                        <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th>Recipe Date Range</th>
                        <th>System ID</th>
                        <th width="100">Batch No</th>
						<th width="100">Labdip No</th>
                        <th width="150">Recipe Description</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:60px;" class="formbutton" />
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" name="hidden_update_id" id="hidden_update_id" class="text_boxes" value="">
                             <input type="hidden" name="cbo_dyeing_re_process" id="cbo_dyeing_re_process" class="text_boxes" value="<? echo $cbo_dyeing_re_process; ?>">
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px;">
                    </td>
                    <td>
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_sysId" id="txt_search_sysId" placeholder="Search" />
                    </td>
					<td>
                        <input type="text" style="width:100px;" class="text_boxes"  name="txt_search_batch" id="txt_search_batch" placeholder="Search" />
                    </td>
                    <td>
                        <input type="text" style="width:100px;" class="text_boxes"  name="txt_search_labdip" id="txt_search_labdip" placeholder="Search" />
                    </td>
                    <td>
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_recDes" id="txt_search_recDes" placeholder="Search" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_labdip').value+'_'+document.getElementById('txt_search_sysId').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_search_recDes').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_dyeing_re_process').value+'_'+document.getElementById('txt_search_batch').value, 'recipe_search_list_view', 'search_div', 'dyeing_re_process_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:60px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div style="width:100%; margin-top:5px; margin-left:3px;" id="search_div" align="left"></div>
    	</fieldset>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="recipe_search_list_view")
{
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$data = explode("_",$data);
	$labdip=$data[0];
	$sysid=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$rec_des =trim($data[5]);
	$search_type =$data[6];
	$cbo_dyeing_re_process =$data[7];
	$batch_no =$data[8];
	//echo $batch_no.'D';
	if($start_date=="" && $end_date=="" && $labdip=="" && $sysid=="" && $rec_des=="")
	{
		echo "<b>Please select any one from Search panel.</b>";die;
	}
	$batch_no_cond="";
	if($batch_no!="")
	{
		$batch_no_cond=" and a.batch_no='$batch_no' ";
	}
	if($start_date!="" && $end_date!="")
	{
		 if($db_type==0)
		 {
			$date_cond="and b.recipe_date between '".change_date_format(trim($start_date), "yyyy-mm-dd","-",1)."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-",1)."'";
		 }
		 else if($db_type==2)
		 {
			$date_cond="and b.recipe_date between '".change_date_format(trim($start_date), "mm-dd-yyyy","/",1)."' and '".change_date_format(trim($end_date), "mm-dd-yyyy", "/",1)."'";
		 }
	}
	else
	{
		$date_cond="";
	}

	if($search_type==1)
	{
		if ($labdip!='') $labdip_cond=" and b.labdip_no='$labdip'"; else $labdip_cond="";
		if ($sysid!='') $sysid_cond=" and b.id=$sysid"; else $sysid_cond="";
		if ($rec_des!='') $rec_des_cond=" and b.recipe_description='$rec_des'"; else $rec_des_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($labdip!='') $labdip_cond=" and b.labdip_no like '%$labdip%'"; else $labdip_cond="";
		if ($sysid!='') $sysid_cond=" and b.id like '%$sysid%' "; else $sysid_cond="";
		if ($rec_des!='') $rec_des_cond=" and b.recipe_description like '%$rec_des%'"; else $rec_des_cond="";
	}
	else if($search_type==2)
	{
		if ($labdip!='') $labdip_cond=" and b.labdip_no like '$labdip%'"; else $labdip_cond="";
		if ($sysid!='') $sysid_cond=" and b.id like '$sysid%' "; else $sysid_cond="";
		if ($rec_des!='') $rec_des_cond=" and b.recipe_description like '$rec_des%'"; else $rec_des_cond="";
	}
	else if($search_type==3)
	{
		if ($labdip!='') $labdip_cond=" and b.labdip_no like '%$labdip'"; else $labdip_cond="";
		if ($sysid!='') $sysid_cond=" and id like '%$sysid' "; else $sysid_cond="";
		if ($rec_des!='') $rec_des_cond=" and b.recipe_description like '%$rec_des'"; else $rec_des_cond="";
	}
	if ($cbo_dyeing_re_process>0) $re_process_cond=" and b.dyeing_re_process = $cbo_dyeing_re_process "; else $re_process_cond="";

	$sql = "select a.batch_no,b.id, b.labdip_no, b.recipe_description, b.recipe_date, b.order_source, b.style_or_order, b.buyer_id, b.color_id, b.color_range, b.recipe_id from pro_recipe_entry_mst b,pro_batch_create_mst a where a.id=b.batch_id and b.working_company_id='$company_id' and b.entry_form=60 and a.status_active=1 and a.is_deleted=0   and b.status_active=1 and b.is_deleted=0 $batch_no_cond $labdip_cond $sysid_cond $re_process_cond $rec_des_cond $date_cond order by b.id DESC";
	// echo $sql;

	$arr=array(6=>$order_source,8=>$buyer_arr,9=>$color_arr,10=>$color_range);

	echo create_list_view("tbl_list_search", "ID,From Recipe No,Batch No,Labdip No,Recipe Description,Recipe Date,Order Source,Booking,Buyer,Color,Color Range", "50,90,100,90,130,70,80,110,70,70,90","1010","220",0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,0,0,order_source,0,buyer_id,color_id,color_range", $arr, "id,recipe_id,batch_no,labdip_no,recipe_description,recipe_date,order_source,style_or_order,buyer_id,color_id,color_range","","",'0,0,0,0,3,0,0,0,0,0','');

	exit();
}

if($action=="recipe_entry_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id","buyer_name");
	// $order_array=return_library_array( "select id, order_no from subcon_ord_dtls", "id","order_no");
	// $po_arr=return_library_array( "select id,po_number from wo_po_break_down",'id','po_number');
	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$batch_array=array();
	if($db_type==0)
	{
		$sql = "select a.id, a.batch_no, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.working_company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	}
	elseif($db_type==2)
	{
		// $sql = "select a.id, a.batch_no,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.working_company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.entry_form, a.total_trims_weight order by a.id DESC";
		$sql = "select a.id, a.batch_no,a.entry_form, a.total_trims_weight,  b.po_id  as po_id, b.prod_id  as prod_id,   (b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b,pro_recipe_entry_mst c where a.id=b.mst_id and a.id=c.batch_id  and  c.id='$data[1]' and a.working_company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   order by a.id DESC";
	}
	//echo $sql;
	$result_sql=sql_select($sql);
	foreach ($result_sql as $row)
	{
		$po_idArr[$row[csf("po_id")]]=$row[csf("po_id")];
	}

	foreach ($result_sql as $row)
	{
		$order_no='';
		$order_id=array_unique(explode(",",$row[csf("po_id")]));
		if($row[csf("entry_form")]==36)
		{
			$batch_type="<b> SUBCONTRACT ORDER </b>";
			$batch_type="<b> SUBCONTRACT ORDER </b>";
			$order_no=$order_array[$row[csf("po_id")]];
			// foreach($order_id as $val)
			// {
			// 	if($order_no=="") $order_no=$order_array[$val]; else $order_no.=", ".$order_array[$val];
			// }
		}
		else
		{
			$batch_type="<b> SELF ORDER </b>";
			$order_no=$order_array[$row[csf("po_id")]];
			// foreach($order_id as $val)
			// {
			// 	if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=", ".$po_arr[$val];
			// }
		}
		$batch_array[$row[csf("id")]]['batch_no']=$row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['total_trims_weight']=$row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty']+=$row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['order'].=$order_no.',';
		$batch_array[$row[csf("id")]]['batch_type']=$batch_type;
	}

	/*$recipeData=sql_select("select a.total_liquor, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
	$total_liquor=$recipeData[0][csf('total_liquor')];
	$batch_weight=$recipeData[0][csf('batch_weight')];

	$ratio_arr=return_library_array( "select prod_id, ratio from pro_recipe_entry_dtls where mst_id=$recipe_id and sub_process_id=$sub_process_id order by id",'prod_id','ratio');*/

	$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$data[4]");
	$owner_company_id=$recipe_arr[0][csf('company_id')];

	$sql_mst="select id, recipe_id, labdip_no, company_id, location_id, recipe_description, batch_id, new_batch_weight, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, ready_to_approve from pro_recipe_entry_mst where id='$data[1]'";
	$dataArray=sql_select($sql_mst);
	$recipe_id=$dataArray[0][csf('recipe_id')];
	$total_liquor=$dataArray[0][csf('total_liquor')];
	$batch_weight=$dataArray[0][csf('new_batch_weight')];
	$readyToApprove = '';

	if($dataArray[0][csf('ready_to_approve')] == 1) {
		$readyToApprove = 'Yes';
	}

	if($dataArray[0][csf('ready_to_approve')] == 2) {
		$readyToApprove = 'No';
	}

	/*$recipeData=sql_select("select a.total_liquor, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
	$total_liquor=$recipeData[0][csf('total_liquor')];
	$batch_weight=$recipeData[0][csf('batch_weight')];*/
	$ratio_arr=array();
	$prevRatioData=sql_select( "select prod_id, sub_process_id, ratio,total_liquor from pro_recipe_entry_dtls where mst_id=$recipe_id");
	foreach($prevRatioData as $prevRow)
	{
		$ratio_arr[$prevRow[csf('sub_process_id')]][$prevRow[csf('prod_id')]]=$prevRow[csf('ratio')];


	}
	$nameArray=sql_select( "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
	$group_id=$nameArray[0][csf('group_id')];
	//print_r($ratio_arr);

	?>
    <div style="width:930px; font-size:6px">
         <table width="930" cellspacing="0" align="right" border="0">
            <tr>
                <td colspan="6" align="center" style="font-size:x-large"><strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$data[0]] ;//$company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center">
                    <?

                        foreach ($nameArray as $result)
                        {
                        ?>
                            Plot No: <? echo $result[csf('plot_no')]; ?>
							Level No: <? echo $result[csf('level_no')] ?>
							Road No: <? echo $result[csf('road_no')]; ?>
							Block No: <? echo $result[csf('block_no')]; ?>
							City No: <? echo $result[csf('city')]; ?>
							Zip Code: <? echo $result[csf('zip_code')]; ?>
							Province No: <?php echo $result[csf('province')]; ?>
							Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
							Email Address: <? echo $result[csf('email')]; ?>
							Website No: <? echo $result[csf('website')];
                        }
                    ?>
                </td>
            </tr>
        	<tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$owner_company_id].'<br>'.$data[3]; ?></strong></u></td>
            </tr>
            <tr>
                <td width="140"><strong>System ID:</strong></td> <td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
                <td width="130"><strong>Labdip No: </strong></td><td width="175"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
                <td width="130"><strong>Recipe Desc.:</strong></td> <td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
            </tr>
            <tr>
                <td><strong>Batch No:</strong></td> <td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
                <td><strong>Recipe Date:</strong></td><td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
                <td><strong>Order Source:</strong></td> <td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Buyer Name:</strong></td> <td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
                <td><strong>Booking:</strong></td> <td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
                <td><strong>Color:</strong></td><td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Color Range:</strong></td> <td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
                <!--<td><strong>B/L Ratio:</strong></td> <td><?echo $dataArray[0][csf('batch_ratio')].':'.$dataArray[0][csf('liquor_ratio')]; ?></td>
                <td><strong>Total Liquor:</strong></td><td> <?echo $dataArray[0][csf('total_liquor')]; ?></td>-->
                <td><strong>Batch Weight:</strong></td><td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_qty']; ?></td>
                <td><strong>Trims Weight:</strong></td> <td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
            </tr>
            <tr>

                <td><strong>Order No:</strong></td> <td><? echo chop($batch_array[$dataArray[0][csf('batch_id')]]['order'],','); ?></td>
                 <td><strong>New Batch Weight:</strong></td><td><? echo $batch_weight; ?></td>
                <td><strong>Method:</strong></td><td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
            </tr>

            <tr>
                <td><strong>Remarks:</strong></td> <td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
                <td><strong>Ready to Approve:</strong></td> <td><?php echo $readyToApprove; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
		<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="110">Item Category</th>
                <th width="110">Item Group</th>
                <th width="130">Item Description</th>
                <th width="80">Item Lot</th>
                <th width="50">UOM</th>
                <th width="70">Dose Base</th>
                <th width="80">Prev Ratio</th>
                <th width="70">Recipe Qty.</th>
                <th width="70">Req. Qty.</th>
                <th>Ratio</th>
            </thead>
         <?
			$i=1;  $j=1;
			$mst_id=$data[1];
			$com_id=$data[0];
			$template_id=$data[5];

			$process_array=array(); $sub_process_data_array=array();
			$sql="select id, sub_process_id as sub_process_id,total_liquor from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
			$nameArray=sql_select( $sql );
			foreach($nameArray as $row)
			{
				if (!in_array( $row[csf("sub_process_id")],$process_array) )
				{
					$process_array[]=$row[csf("sub_process_id")];
				}
				$total_ratio_arr[$row[csf('sub_process_id')]]['total_liquor']=$row[csf('total_liquor')];
			}

			if($db_type==2)
			{
				 $sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id, b.prod_id, b.item_lot, b.dose_base, b.ratio, b.adj_type, b.adj_perc, b.adj_qnty, b.new_item, b.new_batch_weight, b.new_total_liquor from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and b.is_checked=1 and a.company_id='$data[0]' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no";
			}
			else if($db_type==0)
			{
				$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id, b.prod_id, b.item_lot, b.dose_base, b.ratio, b.adj_type, b.adj_perc, b.adj_qnty, b.new_item, b.new_batch_weight, b.new_total_liquor from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and b.is_checked=1 and a.company_id='$data[0]' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no DESC";
			}
			//echo $sql;
			$sql_result =sql_select($sql);
			foreach($sql_result as $row)
			{
				$sub_process_data_array[$row[csf("sub_process_id")]].=$row[csf("id")]."**".$row[csf("item_category_id")]."**".$row[csf("item_group_id")]."**".$row[csf("sub_group_name")]."**".$row[csf("item_description")]."**".$row[csf("item_size")]."**".$row[csf("unit_of_measure")]."**".$row[csf("dtls_id")]."**".$row[csf("sub_process_id")]."**".$row[csf("item_lot")]."**".$row[csf("dose_base")]."**".$row[csf("ratio")]."**".$row[csf("adj_type")]."**".$row[csf("adj_perc")]."**".$row[csf("adj_qnty")]."**".$row[csf("new_item")]."**".$row[csf("prod_id")]."**".$row[csf("new_batch_weight")]."**".$row[csf("new_total_liquor")].",";
			}

			foreach($process_array as $process_id)
			{
				$total_liquor=$total_ratio_arr[$process_id]['total_liquor'];



			?>
                <tr bgcolor="#EEEFF0">
                    <td colspan="11" align="left" ><b>Sub Process Name:- <? echo $dyeing_sub_process[$process_id].', '.number_format($total_liquor,4); ?></b></td>
                </tr>
            <?
				$tot_ratio=0;
				$sub_process_data=explode(",",substr($sub_process_data_array[$process_id],0,-1));
				foreach($sub_process_data as $data)
				{
					$data=explode("**",$data);
					$id=$data[0];
					$item_category_id=$data[1];
					$item_group_id=$data[2];
					$sub_group_name=$data[3];
					$item_description=$data[4];
					$item_size=$data[5];
					$unit_of_measure=$data[6];
					$dtls_id=$data[7];
					$sub_process_id=$data[8];
					$item_lot=$data[9];
					$dose_base_id=$data[10];
					$ratio=$data[11];
					$adj_type=$data[12];
					$adj_perc=$data[13];
					$adj_qnty=$data[14];
					$new_item=$data[15];
					$prod_id=$data[16];
					$batch_weight=$data[17];
					$total_liquor=$data[18];

					if($new_item==1)
					{
						$prev_ratio='';
						$recipe_qty='';
						$adj_type='';
					}
					else
					{
						$prev_ratio=$ratio_arr[$sub_process_id][$prod_id];
						if($dose_base_id==1) $recipe_qty=number_format(($total_liquor*$prev_ratio)/1000,4);
						else if($dose_base_id==2) $recipe_qty=number_format(($batch_weight*$prev_ratio)/100,4);
						$adj_type=$increase_decrease[$adj_type];
						//$ratio=$adj_perc;
					}

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><p><? echo $item_category[$item_category_id]; ?></p></td>
						<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
						<td><p><? echo $item_description; ?></p></td>
						<td><p><? echo $item_lot; ?></p></td>
						<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
						<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
						<td align="right"><? echo $prev_ratio; ?>&nbsp;</td>
                        <td align="right"><? echo $recipe_qty; ?>&nbsp;</td>
                        <td align="right"><? echo $adj_qnty; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($ratio,6,'.',''); ?>&nbsp;</td>
					</tr>
				<?
					$tot_ratio+=$ratio;
					$grand_tot_ratio+=$ratio;
					$i++;
				}
				?>
				<tr class="tbl_bottom">
                    <td align="right" colspan="10"><strong>Sub Process Total</strong></td>
                    <td align="right"><? echo number_format($tot_ratio,6,'.',''); ?>&nbsp;</td>
                </tr>
				<?
			}
			?>

        	<tr class="tbl_bottom">
                <td align="right" colspan="10"><strong>Grand Total</strong></td>
                <td align="right"><? echo number_format($grand_tot_ratio,6,'.',''); ?>&nbsp;</td>
			</tr>
        </table>
        <br>
		 <?
            echo signature_table(165, $com_id, "930px",$template_id);
         ?>
   </div>
   </div>
	<?
}
if($action == "recipe_entry_print_2")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$update_id = $data[1];
	$txt_labdip_no = $data[2];
	$txt_yarn_lot = $data[3];
	$txt_brand= $data[4];
	$txt_count= $data[5];
	$txt_pick_up= $data[6];
	$surpls_solution= $data[7];
	$batch_id= $data[8];
	$sub_process_id = $data[9];
	$report_title = $data[10];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
//	$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	//$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$batch_array = array();
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.batch_weight, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$company_id'  and a.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} else if ($db_type == 2) {
		 $sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.batch_weight, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form, a.batch_weight, a.total_trims_weight order by a.id DESC";
	}
	//echo $sql;

	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) 
	{
		$poIdarr[$row[csf("po_id")]]=$row[csf("po_id")];
	}
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls where id in(".implode(",",$poIdarr).") ", "id", "order_no");
	$po_arr = return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",",$poIdarr).")", 'id', 'po_number');


	foreach ($result_sql as $row) {
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				//$order_array = return_library_array("select id, order_no from subcon_ord_dtls where id=$val", "id", "order_no");
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} else {
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) {
				//$po_arr = return_library_array("select id,po_number from wo_po_break_down where id=$val", 'id', 'po_number');
				if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['batch_weight'] = $row[csf("batch_weight")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
		$batch_nos=$row[csf("batch_no")];
	}

	$sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, ready_to_approve from pro_recipe_entry_mst where id='$update_id'";

	$dataArray = sql_select($sql_recipe_mst);
	//var_dump( $dataArray);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$readyToApprove = '';

	if($dataArray[0][csf('ready_to_approve')] == 1) {
		$readyToApprove = 'Yes';
	}

	if($dataArray[0][csf('ready_to_approve')] == 2) {
		$readyToApprove = 'No';
	}

	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");

	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];

	//total_solution = batch_weight*pickup/100+surplus
	//total_solution = batch_weight+surplus ==> modified by shehab
	//$total_solution = ($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']+$surpls_solution);
	 $total_solution = ((($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']*$txt_pick_up)/100)+$surpls_solution); 

	//solution_amount = (total_solution/5)*4;
	$solution_amount = ($total_solution/5)*4;

	//Alkali_Solution = total_solution/5;
	$alkali_solution_amount = $total_solution/5;

	$construction_sql = "
	select a.id,
	a.mst_id,
	a.prod_id,
	a.item_description,
	a.gsm,
	b.detarmination_id,
	b.item_category_id,
	b.unit_of_measure,
	c.construction
	from pro_batch_create_dtls a,
	product_details_master b,
	lib_yarn_count_determina_mst c
	where   a.prod_id = b.id
	and b.detarmination_id = c.id
	and b.item_category_id = 13
	and a.is_deleted = 0
	and a.status_active = 1
	and b.is_deleted = 0
	and b.status_active = 1
	and c.is_deleted = 0
	and c.status_active = 1
	and a.mst_id = $batch_id

	order by a.id";
	  //echo $construction_sql;
	$const_result = sql_select($construction_sql);
	  //var_dump ($const_result);
	foreach ($const_result as $row) {
		$construction_data["mst_id"] = $row[csf("mst_id")];
		$construction_data["prod_id"] = $row[csf("prod_id")];
		$construction_data["item_description"] = $row[csf("item_description")];
		$construction_data["uom"] = $row[csf("unit_of_measure")];
		$construction_data["fabric_type"] = $row[csf("construction")];
		$construction_data["gsm"] = $row[csf("gsm")];

	}

	$composition_arra = explode(",",$construction_data["item_description"]);

		//Total Length = (Batch Weight/GSM/width)*1000;
	//$total_length = ((($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']/$composition_arra[2])/$composition_arra[3])*1000);
	$width_new=$composition_arra[3]/39.37;
	$total_length = ($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']*1000)/($width_new*$composition_arra[2]);
	//echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_weight'].'='.$width_new.'='.$composition_arra[2];
	?>
	<div style="width:1000px; font-size:6px">
		<table width="1000" cellspacing="0" align="center" border="0" role="all">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
				</strong></td>
			</tr>
			<tr>
				<td colspan="6" style="font-size:x-large; text-align:center;"><strong><u><? echo $company_library[$data[0]]; //.data[3]; ?></u></strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?

					foreach ($nameArray as $result) {
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						Zip Code: <? echo $result[csf('zip_code')];
					}
					?>
				</td>
			</tr>
		</table>
		<table cellspacing="0" align="center" border="1" width="1000" style="margin-top:20px;" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th colspan="8" align="center" style="font-size:20px"><u><strong>Recipe Calculation for CPB Bulk</strong></u></th>
				</tr>
			</thead>
			<tr>
				<td width="180" align="left"><strong>Labdip No </strong></td>
				<td width="220px" align="center"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td width="150" align="left"><strong>Color Name</strong></td>
				<td colspan="2" align="center"> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				<td width="130" align="left"><strong>Date</strong></td>
				<td colspan="2" align="center"> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
			</tr>
			<tr bgcolor="#dddddd" >
				<td width="180" align="left"><strong>Buyer</strong></td>
				<td width="220" align="center"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				<td width="150" align="left"><strong>Shade Type</strong></td>
				<td colspan="2" align="center"><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
				<td width="130" rowspan="3" align="left"><strong>PH</strong></td>
				<td width="150" align="left"><strong>Dyes Solution</strong></td>
				<td width="60" align="center"></td>
			</tr>
			<tr>
				<td width="180" align="left"><strong>Order No</strong></td>
				<td width="220" align="center">
					<? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order";
					else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
					<td colspan="3" align="center"><strong>Fabric Specifications</strong></td>
					<td width="150" align="left"><strong>Alkali Solution</strong></td>
					<td width="60" align="center"></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Fabric Type</strong></td>
					<td width="220" align="center"> <? echo $construction_data["fabric_type"];//need fabric type data ?></td>
					<td width="150" align="left"><strong>Width</strong></td>
					<td width="90" align="center" title="Width/39.37"><? echo number_format($composition_arra[3]/39.37,4);?></td>
					<td width="90" align="center">M<? //echo $unit_of_measurement[$construction_data["uom"]]?></td>
					<td width="150" align="left"><strong>Dye Lqour</strong></td>
					<td width="60" align="center"></td>

				</tr>
				<tr>
					<td width="180" align="left"><strong>Composition</strong></td>
					<td width="220" align="center"> <? echo $composition_arra[1]; ?></td>
					<td width="150" align="left"><strong>GSM</strong></td>
					<td width="90" align="center"><? echo $composition_arra[2];?></td>
					<td width="90" align="center">G<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
					<td width="150" align="left"><strong>Yarn Lot</strong></td>
					<td colspan="2" align="center"><? echo $txt_yarn_lot;?></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Padder Pressure:</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left" title="Batch Weight*1000/(Width*GSM)"><strong>Total Length</strong></td>
					<td width="90" align="center"><? echo number_format($total_length,4); ?></td>
					<td width="90" align="center">M<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
					<td width="150" align="left"><strong>Yarn Brand</strong></td>
					<td colspan="2" align="center"><? echo $txt_brand;?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>M/M For Body Fabric</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Batch weight</strong></td>
					<td width="90" align="center"><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']; ?></td>
					<td width="90" align="center"><? echo $unit_of_measurement[$construction_data["uom"]]; ?></td>
					<td width="150" align="left"><strong>Dye Lot No</strong></td>
					<td colspan="2" align="center"></td>
                   
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>M/M For Rib Fabric</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Pick Up</strong></td>
					<td width="90" align="center"><? echo $txt_pick_up;?></td>
					<td width="90" align="center">%</td>
                    <td width="150" align="left"><strong>Batch No</strong></td>
					<td colspan="2" align="center"><? echo $batch_nos;?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>Rotation Hours</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Surplus Solution</strong></td>
					<td width="90" align="center"><? echo $surpls_solution;?></td>
					<td width="90" align="center">L<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Padding Complition Time</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Total Solution</strong></td> 
					<td width="90" align="center" title="(Batch Wgt*PickUp/100)+Surplus  Solution"><? echo number_format($total_solution,4); ?></td>
					<td width="90" align="center">L<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>Washing Time</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Ready to Approve</strong></td>
					<td colspan="2" align="center"><?php echo $readyToApprove; ?></td>
                    
				</tr>
			</table>
			<br/><br/>
			<div style="width: 600px; float: left; margin-top:15px; margin-right:10px;">
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table">
					<thead bgcolor="#dddddd" align="center">
						<tr>
							<th colspan="5">Dyes Solution</th>
						</tr>
					</thead>
					<tr>
						<td width="130" align="center">Solution Amount</td>
						 
                        <td colspan="3"  title="<? echo $solution_amount;?>" align="center"><? echo number_format($solution_amount,0);?></td>
						<td align="center"><? echo $unit_of_measurement[$construction_data["uom"]]; ?></td>
					</tr>
					<tr bgcolor="#dddddd" >
						<td width="130" align="center">Particulars</td>
						<td width="170" align="center">Brand Name</td>
						<td width="50" align="center">GPL</td>
						<td width="90" align="center">Amount</td>
						<td width="60" align="center"></td>
					</tr>
					<?

				//	if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98 || $sub_process_id ==140 || $sub_process_id ==141 || $sub_process_id ==142 || $sub_process_id ==143)
				if(in_array($sub_process_id, $subprocessForWashArr))
					{
						$ratio_cond="";
					}
					else
					{
						$ratio_cond=" and ratio>0 ";
					}
					//AND a.sub_process_id = $sub_process_id //Remove it Faisal-30-01-2020
					
					$recipeData=sql_select("SELECT
						a.id,
						a.prod_id,
						a.ratio,
						b.item_category_id,
						b.item_description,
						b.unit_of_measure
						FROM pro_recipe_entry_dtls a, product_details_master b
						WHERE     a.prod_id = b.id
						AND a.mst_id = $update_id
						
						AND a.status_active = 1
						AND a.is_deleted = 0
						AND b.status_active = 1
						AND b.is_deleted = 0
						$ratio_cond
						ORDER BY seq_no");
					$tot_dyes_soluton_item_amount=0;
					$recipe_prod_id_arr=array();
					$prod_id_chk_arr=array(9714,89815,80530,9716,9704,100889,16274,16863);
					foreach($recipeData as $row)
					{
						if((in_array($row[csf('prod_id')],$prod_id_chk_arr)) ||  $row[csf('item_category_id')] == 6) {
							$recipe_data_arr[$row[csf('prod_id')]]['item_category_id'] = $row[csf('item_category_id')];
							$recipe_data_arr[$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
							$recipe_data_arr[$row[csf('prod_id')]]['uom']=$row[csf('unit_of_measure')];
							$recipe_data_arr[$row[csf('prod_id')]]['ratio']=$row[csf('ratio')];
						}
						if((!in_array($row[csf('prod_id')],$prod_id_chk_arr)) && $row[csf('item_category_id')] == 5 || $row[csf('item_category_id')] == 7 ){
							$recipe_data_arr[$row[csf('prod_id')]]['item_category_id']=$row[csf('item_category_id')];
							$recipe_data_arr[$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
							$recipe_data_arr[$row[csf('prod_id')]]['uom']=$row[csf('unit_of_measure')];
							$recipe_data_arr[$row[csf('prod_id')]]['ratio']=$row[csf('ratio')];

						}
						$recipe_prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];

						//Dyes Solution total amount = (Total Solution * GPL)/1000 {if uom is kg else if uom is % then divided by 100}
						if($recipe_data_arr[$row[csf('prod_id')]]['uom'] == 12){
							$dyes_soluton_item_amount = ($total_solution * $recipe_data_arr[$row[csf('prod_id')]]['ratio'])/1000;
							$dyes_soluton_item_total += $dyes_soluton_item_amount;
						}else{
							$dyes_soluton_item_amount = (($total_solution * $recipe_data_arr[$row[csf('prod_id')]]['ratio'])/100);
							$dyes_soluton_item_total += $dyes_soluton_item_amount;
						}



						if((in_array($row[csf('prod_id')],$prod_id_chk_arr)) || $recipe_data_arr[$row[csf('prod_id')]]['item_category_id'] == 6){
							//Required_water = solution_amount - (sum_of_particulars_amount)
							//$required_water = $solution_amount - $dyes_soluton_item_total;
							
							$tot_dyes_soluton_item_amount+=$dyes_soluton_item_amount;
							?>
							<tr>
								<td align="center"  width="130"><? echo $item_category[$recipe_data_arr[$row[csf('prod_id')]]['item_category_id']];?></td>
								<td align="center"  width="170"><? echo $recipe_data_arr[$row[csf('prod_id')]]['item_description'];?></td>
								<td  align="center" title="ProdID=<? echo $row[csf('prod_id')]; ?>" width="50"><? echo $recipe_data_arr[$row[csf('prod_id')]]['ratio'];?></td>
								<td align="center"  width="90"><? echo $dyes_soluton_item_amount; ?></td>
								<td align="center"  width="60"><? echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
							</tr>
							<?
						}
					}
					?>
					<tr bgcolor="#dddddd">
						<td  align="center" colspan="2">Required Water</td>
						<td align="center"  colspan="2" title="Dyes Solution/Amount"><? $required_water=$solution_amount-$tot_dyes_soluton_item_amount; echo number_format($required_water,4);?></td>
						<td align="center" >L<? //echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					</tr>

				</table>
			</div>
			<div style="width: 370px; float: left; margin-top:15px; margin-left:3px;">
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="360" class="rpt_table">
					<thead>
						<tr bgcolor="#dddddd" align="center">
							<th colspan="4">Alkali Solution</th>
						</tr>
					</thead>
					<tr>
						<td width="110"></td>
                       <td align="center" width="110" title="<? echo $alkali_solution_amount;?>" align="center"><? echo number_format($alkali_solution_amount,0); ?></td>
						<td width="80" align="center"><? echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
						<td width="60"></td>
					</tr>
					<tr bgcolor="#dddddd" >
						<td align="center"  width="110"><strong>Chemicals</strong></td>
						<td align="center"  width="110"><strong>Recipe GPL</strong></td>
						<td align="center"  width="110"><strong>Amount</strong></td>
						<td align="center"  width="70"></td>
					</tr>
					<?
			//var_dump($recipe_data_arr);
			$tot_alkali_item_soluton_amount=0;
					foreach ($recipe_data_arr as $key => $value) {
						$recipe_alkali_data_arr[$key]['item_category_id'] = $value['item_category_id'];
						$recipe_alkali_data_arr[$key]['item_description'] = $value['item_description'];
						$recipe_alkali_data_arr[$key]['uom'] = $value['unit_of_measure'];
						$recipe_alkali_data_arr[$key]['ratio'] = $value['ratio'];

						if($recipe_alkali_data_arr[$key]['uom'] == 12){
							$alkali_item_soluton_amount = ($total_solution * $recipe_alkali_data_arr[$key]['ratio'])/1000;
							$alkali_item_soluton_total += $alkali_item_soluton_amount;
						}else{
							$alkali_item_soluton_amount = ($total_solution * $recipe_alkali_data_arr[$key]['ratio'])/100;
							$alkali_item_soluton_total += $alkali_item_soluton_amount;
						}
					//alkali_water = alkali_solution_amount - (sum_of_chemicals_amount)
						//$water = $alkali_solution_amount - $alkali_item_soluton_total;
						

				if((!in_array($key,$prod_id_chk_arr)) && ($recipe_alkali_data_arr[$key]['item_category_id'] == 5 || $recipe_alkali_data_arr[$key]['item_category_id'] == 7))
						{
							
							$tot_alkali_item_soluton_amount+=$alkali_item_soluton_amount;
							?>
							<tr>
								<td width="110"><? echo $recipe_alkali_data_arr[$key]['item_description'];?></td>
								<td align="center" title="ProdID=<? echo $key; ?>" width="110"><? echo $recipe_alkali_data_arr[$key]['ratio'];?></td>
								<td align="right"  width="110"><? echo $alkali_item_soluton_amount; ?></td>
								<td align="center"  width="70"><? echo $unit_of_measurement[$recipe_alkali_data_arr[$key]['uom']]; ?></td>
							</tr>
							<?
						}
					}
					?>
					<tr bgcolor="#dddddd">
						<td align="center">Water</td>
						<td align="center" colspan="2" title="Alkali Solution/Amount"><? $water = $alkali_solution_amount - $tot_alkali_item_soluton_amount;echo number_format($water,4);?></td>
						<td align="center">L<? //echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<?
		exit();
	}
	
if($action == "recipe_entry_print_2_not")//not used
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$update_id = $data[1];
	$txt_labdip_no = $data[2];
	$txt_yarn_lot = $data[3];
	$txt_brand= $data[4];
	$txt_count= $data[5];
	$txt_pick_up= $data[6];
	$surpls_solution= $data[7];
	$batch_id= $data[8];
	$sub_process_id = $data[9];
	$report_title = $data[10];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
//	$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	//$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$batch_array = array();
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.batch_weight, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$company_id'  and a.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} else if ($db_type == 2) {
		 $sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.batch_weight, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form, a.batch_weight, a.total_trims_weight order by a.id DESC";
	}
	//echo $sql;

	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) {
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				$order_array = return_library_array("select id, order_no from subcon_ord_dtls where id=$val", "id", "order_no");
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} else {
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) {
				$po_arr = return_library_array("select id,po_number from wo_po_break_down where id=$val", 'id', 'po_number');
				if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['batch_weight'] = $row[csf("batch_weight")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
		$batch_nos=$row[csf("batch_no")];
	}

	$sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$update_id'";

	$dataArray = sql_select($sql_recipe_mst);
	//var_dump( $dataArray);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];

	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");

	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];

	//total_solution = batch_weight*pickup/100+surplus
	//total_solution = batch_weight+surplus ==> modified by shehab
	//$total_solution = ($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']+$surpls_solution);
	 $total_solution = ((($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']*$txt_pick_up)/100)+$surpls_solution); 

	//solution_amount = (total_solution/5)*4;
	$solution_amount = ($total_solution/5)*4;

	//Alkali_Solution = total_solution/5;
	$alkali_solution_amount = $total_solution/5;

	$construction_sql = "
	select a.id,
	a.mst_id,
	a.prod_id,
	a.item_description,
	a.gsm,
	b.detarmination_id,
	b.item_category_id,
	b.unit_of_measure,
	c.construction
	from pro_batch_create_dtls a,
	product_details_master b,
	lib_yarn_count_determina_mst c
	where   a.prod_id = b.id
	and b.detarmination_id = c.id
	and b.item_category_id = 13
	and a.is_deleted = 0
	and a.status_active = 1
	and b.is_deleted = 0
	and b.status_active = 1
	and c.is_deleted = 0
	and c.status_active = 1
	and a.mst_id = $batch_id

	order by a.id";
	  //echo $construction_sql;
	$const_result = sql_select($construction_sql);
	  //var_dump ($const_result);
	foreach ($const_result as $row) {
		$construction_data["mst_id"] = $row[csf("mst_id")];
		$construction_data["prod_id"] = $row[csf("prod_id")];
		$construction_data["item_description"] = $row[csf("item_description")];
		$construction_data["uom"] = $row[csf("unit_of_measure")];
		$construction_data["fabric_type"] = $row[csf("construction")];
		$construction_data["gsm"] = $row[csf("gsm")];

	}

	$composition_arra = explode(",",$construction_data["item_description"]);

		//Total Length = (Batch Weight/GSM/width)*1000;
	//$total_length = ((($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']/$composition_arra[2])/$composition_arra[3])*1000);
	$width_new=$composition_arra[3]/39.37;
	$total_length = ($batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']*1000)/($width_new*$composition_arra[2]);
	//echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_weight'].'='.$width_new.'='.$composition_arra[2];
	?>
	<div style="width:1000px; font-size:6px">
		<table width="1000" cellspacing="0" align="center" border="0" role="all">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
				</strong></td>
			</tr>
			<tr>
				<td colspan="6" style="font-size:x-large; text-align:center;"><strong><u><? echo $company_library[$data[0]]; //.data[3]; ?></u></strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?

					foreach ($nameArray as $result) {
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						Zip Code: <? echo $result[csf('zip_code')];
					}
					?>
				</td>
			</tr>
		</table>
		<table cellspacing="0" align="center" border="1" width="1000" style="margin-top:20px;" rules="all" class="rpt_table">
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th colspan="8" align="center" style="font-size:20px"><u><strong>Recipe Calculation for CPB Bulk</strong></u></th>
				</tr>
			</thead>
			<tr>
				<td width="180" align="left"><strong>Labdip No </strong></td>
				<td width="220px" align="center"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td width="150" align="left"><strong>Color Name</strong></td>
				<td colspan="2" align="center"> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				<td width="130" align="left"><strong>Date</strong></td>
				<td colspan="2" align="center"> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
			</tr>
			<tr bgcolor="#dddddd" >
				<td width="180" align="left"><strong>Buyer</strong></td>
				<td width="220" align="center"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				<td width="150" align="left"><strong>Shade Type</strong></td>
				<td colspan="2" align="center"><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
				<td width="130" rowspan="3" align="left"><strong>PH</strong></td>
				<td width="150" align="left"><strong>Dyes Solution</strong></td>
				<td width="60" align="center"></td>
			</tr>
			<tr>
				<td width="180" align="left"><strong>Order No</strong></td>
				<td width="220" align="center">
					<? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order";
					else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
					<td colspan="3" align="center"><strong>Fabric Specifications</strong></td>
					<td width="150" align="left"><strong>Alkali Solution</strong></td>
					<td width="60" align="center"></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Fabric Type</strong></td>
					<td width="220" align="center"> <? echo $construction_data["fabric_type"];//need fabric type data ?></td>
					<td width="150" align="left"><strong>Width</strong></td>
					<td width="90" align="center" title="Width/39.37"><? echo number_format($composition_arra[3]/39.37,4);?></td>
					<td width="90" align="center">M<? //echo $unit_of_measurement[$construction_data["uom"]]?></td>
					<td width="150" align="left"><strong>Dye Lqour</strong></td>
					<td width="60" align="center"></td>

				</tr>
				<tr>
					<td width="180" align="left"><strong>Composition</strong></td>
					<td width="220" align="center"> <? echo $composition_arra[1]; ?></td>
					<td width="150" align="left"><strong>GSM</strong></td>
					<td width="90" align="center"><? echo $composition_arra[2];?></td>
					<td width="90" align="center">G<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
					<td width="150" align="left"><strong>Yarn Lot</strong></td>
					<td colspan="2" align="center"><? echo $txt_yarn_lot;?></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Padder Pressure:</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left" title="Batch Weight*1000/(Width*GSM)"><strong>Total Length</strong></td>
					<td width="90" align="center"><? echo number_format($total_length,4); ?></td>
					<td width="90" align="center">M<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
					<td width="150" align="left"><strong>Yarn Brand</strong></td>
					<td colspan="2" align="center"><? echo $txt_brand;?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>M/M For Body Fabric</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Batch weight</strong></td>
					<td width="90" align="center"><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_weight']; ?></td>
					<td width="90" align="center"><? echo $unit_of_measurement[$construction_data["uom"]]; ?></td>
					<td width="150" align="left"><strong>Dye Lot No</strong></td>
					<td colspan="2" align="center"></td>
                   
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>M/M For Rib Fabric</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Pick Up</strong></td>
					<td width="90" align="center"><? echo $txt_pick_up;?></td>
					<td width="90" align="center">%</td>
                    <td width="150" align="left"><strong>Batch No</strong></td>
					<td colspan="2" align="center"><? echo $batch_nos;?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>Rotation Hours</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Surplus Solution</strong></td>
					<td width="90" align="center"><? echo $surpls_solution;?></td>
					<td width="90" align="center">L<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
				</tr>
				<tr bgcolor="#dddddd" >
					<td width="180" align="left"><strong>Padding Complition Time</strong></td>
					<td width="220" align="center"> </td>
					<td width="150" align="left"><strong>Total Solution</strong></td> 
					<td width="90" align="center" title="(Batch Wgt*PickUp/100)+Surplus  Solution"><? echo number_format($total_solution,4); ?></td>
					<td width="90" align="center">L<? //echo $unit_of_measurement[$construction_data["uom"]];?></td>
				</tr>
				<tr>
					<td width="180" align="left"><strong>Washing Time</strong></td>
					<td width="220" align="center"> </td>
                    
				</tr>
			</table>
			<br/><br/>
			<div style="width: 600px; float: left; margin-top:15px; margin-right:10px;">
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table">
					<thead bgcolor="#dddddd" align="center">
						<tr>
							<th colspan="5">Dyes Solution</th>
						</tr>
					</thead>
					<tr>
						<td width="130" align="center">Solution Amount</td>
						<td colspan="3" align="center"><? echo number_format($solution_amount,0);?></td>
						<td align="center"><? echo $unit_of_measurement[$construction_data["uom"]]; ?></td>
					</tr>
					<tr bgcolor="#dddddd" >
						<td width="130" align="center">Particulars</td>
						<td width="170" align="center">Brand Name</td>
						<td width="50" align="center">GPL</td>
						<td width="90" align="center">Amount</td>
						<td width="60" align="center"></td>
					</tr>
					<?

					if($sub_process_id==93 || $sub_process_id==94 || $sub_process_id==95 || $sub_process_id==96 || $sub_process_id==97 || $sub_process_id==98 || $sub_process_id ==140 || $sub_process_id ==141 || $sub_process_id ==142 || $sub_process_id ==143)
					if(in_array($sub_process_id, $subprocessForWashArr))
					{
						$ratio_cond="";
					}
					else
					{
						$ratio_cond=" and ratio>0 ";
					}
					//AND a.sub_process_id = $sub_process_id //Remove it Faisal-30-01-2020
					
					$recipeData=sql_select("SELECT
						a.id,
						a.prod_id,
						a.ratio,
						b.item_category_id,
						b.item_description,
						b.unit_of_measure
						FROM pro_recipe_entry_dtls a, product_details_master b
						WHERE     a.prod_id = b.id
						AND a.mst_id = $update_id
						
						AND a.status_active = 1
						AND a.is_deleted = 0
						AND b.status_active = 1
						AND b.is_deleted = 0
						$ratio_cond
						ORDER BY seq_no");
						
					$tot_dyes_soluton_item_amount=0;
					$recipe_prod_id_arr=array();
					foreach($recipeData as $row)
					{
						if($row[csf('item_category_id')] == 6){
							$recipe_data_arr[$row[csf('prod_id')]]['item_category_id'] = $row[csf('item_category_id')];
							$recipe_data_arr[$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
							$recipe_data_arr[$row[csf('prod_id')]]['uom']=$row[csf('unit_of_measure')];
							$recipe_data_arr[$row[csf('prod_id')]]['ratio']=$row[csf('ratio')];
						}
						else if($row[csf('item_category_id')] == 5 || $row[csf('item_category_id')] == 7){
							$recipe_data_arr[$row[csf('prod_id')]]['item_category_id']=$row[csf('item_category_id')];
							$recipe_data_arr[$row[csf('prod_id')]]['item_description']=$row[csf('item_description')];
							$recipe_data_arr[$row[csf('prod_id')]]['uom']=$row[csf('unit_of_measure')];
							$recipe_data_arr[$row[csf('prod_id')]]['ratio']=$row[csf('ratio')];

						}
						$recipe_prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];

						//Dyes Solution total amount = (Total Solution * GPL)/1000 {if uom is kg else if uom is % then divided by 100}
						if($recipe_data_arr[$row[csf('prod_id')]]['uom'] == 12){
							$dyes_soluton_item_amount = ($total_solution * $recipe_data_arr[$row[csf('prod_id')]]['ratio'])/1000;
							$dyes_soluton_item_total += $dyes_soluton_item_amount;
						}else{
							$dyes_soluton_item_amount = (($total_solution * $recipe_data_arr[$row[csf('prod_id')]]['ratio'])/100);
							$dyes_soluton_item_total += $dyes_soluton_item_amount;
						}



						if($recipe_data_arr[$row[csf('prod_id')]]['item_category_id'] == 6){
							//Required_water = solution_amount - (sum_of_particulars_amount)
							//$required_water = $solution_amount - $dyes_soluton_item_total;
							
							$tot_dyes_soluton_item_amount+=$dyes_soluton_item_amount;
							?>
							<tr>
								<td align="center"  width="130"><? echo $item_category[$recipe_data_arr[$row[csf('prod_id')]]['item_category_id']];?></td>
								<td align="center"  width="170"><? echo $recipe_data_arr[$row[csf('prod_id')]]['item_description'];?></td>
								<td  align="center" width="50"><? echo $recipe_data_arr[$row[csf('prod_id')]]['ratio'];?></td>
								<td align="center"  width="90"><? echo $dyes_soluton_item_amount; ?></td>
								<td align="center"  width="60"><? echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
							</tr>
							<?
						}
					}
					?>
					<tr bgcolor="#dddddd">
						<td  align="center" colspan="2">Required Water</td>
						<td align="center"  colspan="2" title="Dyes Solution/Amount"><? $required_water=$solution_amount-$tot_dyes_soluton_item_amount; echo number_format($required_water,4);?></td>
						<td align="center" >L<? //echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					</tr>

				</table>
			</div>
			<div style="width: 370px; float: left; margin-top:15px; margin-left:3px;">
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="360" class="rpt_table">
					<thead>
						<tr bgcolor="#dddddd" align="center">
							<th colspan="4">Alkali Solution</th>
						</tr>
					</thead>
					<tr>
						<td width="110"></td>
						<td width="110" align="center"><? echo number_format($alkali_solution_amount,0); ?></td>
						<td width="80" align="center"><? echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
						<td width="60"></td>
					</tr>
					<tr bgcolor="#dddddd" >
						<td align="center"  width="110"><strong>Chemicals</strong></td>
						<td align="center"  width="110"><strong>Recipe GPL</strong></td>
						<td align="center"  width="110"><strong>Amount</strong></td>
						<td align="center"  width="70"></td>
					</tr>
					<?
			//var_dump($recipe_data_arr);
			$tot_alkali_item_soluton_amount=0;
					foreach ($recipe_data_arr as $key => $value) {
						$recipe_alkali_data_arr[$key]['item_category_id'] = $value['item_category_id'];
						$recipe_alkali_data_arr[$key]['item_description'] = $value['item_description'];
						$recipe_alkali_data_arr[$key]['uom'] = $value['unit_of_measure'];
						$recipe_alkali_data_arr[$key]['ratio'] = $value['ratio'];

						if($recipe_alkali_data_arr[$key]['uom'] == 12){
							$alkali_item_soluton_amount = ($total_solution * $recipe_alkali_data_arr[$key]['ratio'])/1000;
							$alkali_item_soluton_total += $alkali_item_soluton_amount;
						}else{
							$alkali_item_soluton_amount = ($total_solution * $recipe_alkali_data_arr[$key]['ratio'])/100;
							$alkali_item_soluton_total += $alkali_item_soluton_amount;
						}
					//alkali_water = alkali_solution_amount - (sum_of_chemicals_amount)
						//$water = $alkali_solution_amount - $alkali_item_soluton_total;
						

						if($recipe_alkali_data_arr[$key]['item_category_id'] == 5 || $recipe_alkali_data_arr[$key]['item_category_id'] == 7){
							
							$tot_alkali_item_soluton_amount+=$alkali_item_soluton_amount;
							?>
							<tr>
								<td width="110"><? echo $recipe_alkali_data_arr[$key]['item_description'];?></td>
								<td align="center" width="110"><? echo $recipe_alkali_data_arr[$key]['ratio'];?></td>
								<td align="right"  width="110"><? echo $alkali_item_soluton_amount; ?></td>
								<td align="center"  width="70"><? echo $unit_of_measurement[$recipe_alkali_data_arr[$key]['uom']]; ?></td>
							</tr>
							<?
						}
					}
					?>
					<tr bgcolor="#dddddd">
						<td align="center">Water</td>
						<td align="center" colspan="2" title="Alkali Solution/Amount"><? $water = $alkali_solution_amount - $tot_alkali_item_soluton_amount;echo number_format($water,4);?></td>
						<td align="center">L<? //echo $unit_of_measurement[$recipe_data_arr[$row[csf('prod_id')]]['uom']]; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<?
		exit();
	}

if ($action == "recipe_entry_print_4") 
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	//	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

	$batch_array = array();
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} else if ($db_type == 2) {
		$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id, c.new_batch_weight, sum(b.batch_qnty) as batch_qnty,a.is_sales,a.sales_order_no  from pro_batch_create_mst a, pro_batch_create_dtls b,pro_recipe_entry_mst c where a.id=b.mst_id and a.id=c.batch_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against,c.new_batch_weight, a.batch_no,a.entry_form,a.total_trims_weight,a.is_sales,a.sales_order_no order by a.id DESC";
	}
	// echo $sql;
	$result_sql = sql_select($sql);
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) 
	{
		$poIdarr[$row[csf("po_id")]]=$row[csf("po_id")];
		$is_sales=$row[csf("is_sales")];
		$sales_order_no=$row[csf("sales_order_no")];

	}
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls where id in(".implode(",",$poIdarr).") ", "id", "order_no");
	$po_arr = return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",",$poIdarr).")", 'id', 'po_number');

	
	foreach ($result_sql as $row) {
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} else {
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['is_sales'] = $row[csf("is_sales")];
		$batch_array[$row[csf("id")]]['sales_order_no'] = $row[csf("sales_order_no")];
	}
	// echo "<pre>";
	// print_r($batch_array);


	$sql_recipe_mst = "select id, labdip_no, company_id,working_company_id as w_com_id,location_id,new_batch_weight, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks, batch_qty from pro_recipe_entry_mst where id='$data[1]'";
	$dataArray = sql_select($sql_recipe_mst);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$new_batch_weight=$dataArray[0][csf('new_batch_weight')];


	$sql_po_arr = sql_select("select c.mst_id as batch_id, b.grouping from wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where  a.id=b.job_id and b.id=c.po_id and b.status_active=1 and b.is_deleted=0");
	$internal_ref_arr = array();

	foreach ($sql_po_arr as $row) {

		$internal_ref_arr[$row[csf('batch_id')]] = $row[csf('grouping')];

	}
	unset($sql_po_arr);

	$total_batch_weight =  $dataArray[0][csf('batch_qty')];

	$mst_id = $dataArray[0][csf('id')];
	$total_liquor_ratio_sql = sql_select("select liquor_ratio, total_liquor, check_id from pro_recipe_entry_dtls where mst_id=" . $mst_id . " and ratio>0 group by liquor_ratio, total_liquor, check_id");
	$total_liquor_ratio = 0;
	foreach($total_liquor_ratio_sql as $value){

		$total_liquor_ratio += $value[csf('liquor_ratio')];
	}
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];

	print_r($batch_array[$dataArray[0][csf('batch_id')]]['order']);
	
	?>
	<div style="width:1400px; font-size:6px">
		<table width="1400" cellspacing="0" align="right" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
				</strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?

					foreach ($nameArray as $result) {
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						Zip Code: <? echo $result[csf('zip_code')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.$data[3]; ?></strong></u></td>
			</tr>
			<tr>
				<td width="130"><strong>System ID:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
				<td width="130"><strong>Labdip No: </strong></td>
				<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td width="130"><strong>Recipe Des.:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
			</tr>
			<tr>
				<td><strong>Batch No:</strong></td>
				<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
				<td><strong>Recipe Date:</strong></td>
				<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
				<td><strong>Order Source:</strong></td>
				<td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Buyer Name:</strong></td>
				<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				<td><strong>Booking:</strong></td>
				<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
				<td><strong>Color:</strong></td>
				<td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Color Range:</strong></td>
				<td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
            <!-- <td><strong>B/L Ratio:</strong></td> <td><? echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
            	<td><strong>Total Liq.:</strong></td><td> <? echo $dataArray[0][csf('total_liquor')]; ?></td>-->
            	<td><strong>Batch Weight:</strong></td>
            	<td><? $batch_weight=$new_batch_weight;//$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];
				echo $batch_weight; ?></td>
            	<td><strong>Trims Weight:</strong></td>
            	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
            </tr>
            <tr>
            	<td><strong>Order No.:</strong></td>
            	<td>
            		<? 
					if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3){ 
					echo "Sample Without Order";
					}
            		else{
						if($batch_array[$dataArray[0][csf('batch_id')]]['is_sales']==1)
						{
							echo $batch_array[$dataArray[0][csf('batch_id')]]['sales_order_no'] ;
							//echo $batch_array[$dataArray[0][csf('batch_id')]]['order'];
						}
						else
						{
							echo $batch_array[$dataArray[0][csf('batch_id')]]['order'];
					}
						
					} 
					?></td>
            		<td><strong>Method:</strong></td>
            		<td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                    <td><strong>Machine no:</strong></td>
            		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
            	</tr>
            	<tr>
            		<td><strong>Remarks:</strong></td>
            		<td ><? echo $dataArray[0][csf('remarks')]; ?></td>
            		<td><strong>Liquor Ratio:</strong></td>
            		<td ><? echo $total_liquor_ratio; ?></td>
            		<td><strong>Internal Ref:</strong></td>
            		<td ><? echo $internal_ref_arr[$dataArray[0][csf('batch_id')]]; ?></td>
            	</tr>
            </table>
            <br>
            <?
			$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
			$j = 1;
			$entryForm = $entry_form_arr[$batch_id_qry[0]];
		?>
            <table width="1400" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
			<thead bgcolor="#dddddd" align="center">
				
					<tr bgcolor="#CCCCFF">
						<th colspan="5" align="center"><strong>Fabrication</strong></th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="80">Dia/ W. Type</th>
						<th width="200">Constrution & Composition</th>
						<th width="70">Gsm</th>
						<th width="70">Dia</th>
					</tr>
			</thead>
			<tbody>
				<?
					foreach ($batch_id_qry as $b_id) {
						 $batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
						$result_batch_query = sql_select($batch_query);
						foreach ($result_batch_query as $rows) {
							if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							$fabrication_full = $rows[csf("item_description")];
							$fabrication = explode(',', $fabrication_full);
				?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $j; ?></td>
								<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>			
								<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
								<td align="center"><? echo $fabrication[2]; ?></td>
								<td align="center"><? echo $fabrication[3]; ?></td>
							</tr>
				<?
							$j++;
						}
					}
				?>
			</tbody>
		</table>
        <br> <br> <br>
            <div style="width:100%;">
            	<table align="right" style="margin:5px;" cellspacing="0" width="1400" border="1" rules="all" class="rpt_table">
            		<thead bgcolor="#dddddd" align="center">
            			<tr>
            				<th rowspan="2" width="30">SL</th>
                			<th rowspan="2" width="110">Item Cat.</th>
                			<th rowspan="2" width="110">Product ID</th>
                			<th rowspan="2" width="220">Function Name</th>
                			<th rowspan="2" width="150">Item Group</th>
							<th rowspan="2" width="150">Item Description</th>
                			<th rowspan="2" width="80">Item Lot</th>
                			<th rowspan="2" width="50">UOM</th>
                			<th rowspan="2" width="100">Dose Base</th>
                			<th rowspan="2" width="100">Ratio/Dose</th>
                			<th colspan="3" width="200">Amount/KG</th>
                			<th rowspan="2" width="">Comments</th>
            			</tr>
            			<tr>
            				<th>KG</th>
            				<th>Gram</th>
            				<th>Miligram</th>
            			</tr>
            			
            		</thead>
            		<?
            		$i = 1;
            		$j = 1;
            		$mst_id = $data[1];
            		$com_id = $data[0];


            		$process_array = array();
            		$sub_process_data_array = array();
            		$sub_process_remark_array = array();
            		$sql_ratio = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
            		$nameArray = sql_select($sql_ratio);
            		foreach ($nameArray as $row) {
            			if (!in_array($row[csf("sub_process_id")], $process_array)) {
            				$process_array[] = $row[csf("sub_process_id")];
            				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
            			}
            			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
            			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];


            		}
			
            		if ($db_type == 2) {
            			/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no";*/
						
            			$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no,b.adj_perc,b.adj_qnty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
            			union
            			(
            			select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no,b.adj_perc,b.adj_qnty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
            		)  order by seq_no,sub_process_id";
            	} else if ($db_type == 0) {
            		$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no,b.adj_perc,b.adj_qnty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
            		union
            		(
            		select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no,b.adj_perc,b.adj_qnty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
            	)  ";
            }
			// echo $sql; //
            $sql_result = sql_select($sql);

            foreach ($sql_result as $row) {
            	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")]."**".$row[csf("adj_perc")]."**".$row[csf("adj_qnty")] . "$$$";

            }
			//var_dump($sub_process_data_array);
            foreach ($process_array as $process_id) {
            	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];
            	$l_ratio = $sub_process_remark_array[$process_id]['liquor_ratio'];

            	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
            	$remark = $sub_process_remark_array[$process_id]['remark'];
            	if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
            	$tot_ratio=1.5;
            	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;


            	?>
            	<tr bgcolor="#EEEFF0">
            		<td colspan="15" align="left"><b>Sub Process
            			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark; ?>
						<!--  .' Total Liquor(ltr): '.number_format($liquor_ratio,2,'.','');
						, Liquor Ratio: <?php echo number_format($l_ratio,2,'.',''); 
						?> -->
					</b>
            		</td>
            	</tr>
            	<?
            	$tot_ratio = 0;
            	$tot_kg = 0;
            	$tot_gm = 0;
            	$tot_mgm = 0;
            	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
				//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
				$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
				$sub_process_data = explode("$$$", $sub_process_dataArr);
				//echo count($sub_process_data).'='.$process_id.'<br>';
            	foreach ($sub_process_data as $data) {
            		$data = explode("**", $data);
            		$current_stock = $data[13];
            		$current_stock_check=number_format($current_stock,7,'.','');
                         		
            		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id ==140 || $sub_process_id ==141 || $process_id ==142 || $process_id ==143)
					if(in_array($process_id,$subprocessForWashArr))
					{
						$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];

                			if($dose_base_id==1 && $item_category_id==5){
                				
                				$amount = number_format($ratio*$total_batch_weight*$l_ratio/1000,6, '.', '');

                				$amount = explode('.',$amount);
                			}
                			else{
                				
                				$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');

                				$amount = explode('.',$amount);
                			}

                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
                			<tr bgcolor="<? echo $bgcolor; ?>">
                				<td><? echo $i; ?></td>
                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
                				<td><p><? echo $prod_id; ?></p></td>
                				<td><p><? echo $sub_group_name; ?></p></td>
                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
								<td><p><? echo $item_description ?></p></td>
                				<td><p><? echo $item_lot; ?></p></td>
                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
                				<td align="right"><? echo $amount[0]; ?>&nbsp;</td>
                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
                				<td align="right"><? echo $comments; ?>&nbsp;</td>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
                			$grand_tot_kg += $amount[0];
                			$grand_tot_gm += substr($amount[1], 0, 3);
                			$grand_tot_mgm += substr($amount[1], 3, 6);
                			$tot_kg += $amount[0];
		                	$tot_gm += substr($amount[1], 0, 3);
		                	$tot_mgm += substr($amount[1], 0, 3);
                			$i++;
					}
					else
					{
						
					
                		if($current_stock_check>0)
                		{
                			$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];
							$adj_perc = $data[15];
							$adj_qnty = explode('.',$data[16]);
						
                			if($dose_base_id==1 && $item_category_id==5){
                				
                				$amount = number_format($ratio*$total_batch_weight*$l_ratio/1000,6, '.', '');

                				$amount = explode('.',$amount);
                			}
                			else{
                				
                				$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');

                				$amount = explode('.',$amount);
                			}

							//echo number_format($data[16],6,'.','').'ddddddddddddddddddzzz';
							$adjQty=number_format($data[16],6,'.','');
							$adjQty_str=explode('.',$adjQty);
							$adjQty_decimal_cal=$adjQty_str[1];
							$gm=substr($adjQty_decimal_cal, 0, 3);
							$mili_gm=substr($adjQty_decimal_cal, 3, 2);

                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
                			<tr bgcolor="<? echo $bgcolor; ?>">
                				<td><? echo $i; ?></td>
                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
                				<td><p><? echo $prod_id; ?></p></td>
                				<td><p><? echo $sub_group_name; ?></p></td>
                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
								<td><p><? echo $item_description ?></p></td>
                				<td><p><? echo $item_lot; ?></p></td>
                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
                				<td align="right" title="<?=$adjQty_decimal_cal;?>"><? echo number_format($adj_qnty[0]); ?>&nbsp;</td>
                				<td align="right">
								 	<? 
										// if(number_format(substr($adj_qnty[1], 0, 3))>0){
										// 	//$day=str_pad($j, 2, '0', STR_PAD_LEFT);   
										// 	$aj_gm=substr($adj_qnty[1], 0, 3);
										// 	$aj_mg=str_pad($aj_gm, 3, '0', STR_PAD_LEFT); 
										// 	echo $aj_mg;//number_format(substr($adj_qnty[1], 0, 3));
										// }else echo "000";//$amount[1]
										echo $gm;
										?>&nbsp;
								</td>
                				<td align="right">
									<?
									// if(substr($adj_qnty[1], 3)>0) 
									// {
									// 	$aj_mili_g=substr($adj_qnty[1], 3, 6);
									// 	$aj_mili_gram=str_pad($aj_mili_g, 3, '0', STR_PAD_RIGHT); 
									// 	echo $aj_mili_gram;
									// }
									// else echo "000";
									//$amount[1] 
									echo $mili_gm;
									?>&nbsp;</td>
                				<td align="right"><? echo $comments; ?>&nbsp;</td>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
                			$grand_tot_kg += $adj_qnty[0];
                			$grand_tot_gm += $gm;//substr($adj_qnty[1], 0, 3);
                			$grand_tot_mgm += $mili_gm;//substr($adj_qnty[1], 3, 6);
                			$tot_kg += $adj_qnty[0];
		                	$tot_gm += $gm;//number_format(substr($adj_qnty[1], 0, 3));
		                	$tot_mgm += $mili_gm;//number_format(substr($adj_qnty[1], 3, 6));;
                			$i++;
                		}
            		}
            	}

            	if($tot_mgm>=1000){
            		$tot_gm += intdiv($tot_mgm, 1000);
            		$tot_mgm = $tot_mgm%1000;
            	}

            	if($tot_gm>=1000){
            		$tot_kg += intdiv($tot_gm, 1000);
            		$tot_gm = $tot_gm%1000;	
            	}
				
            	?>
            	<tr class="tbl_bottom">
            		<td align="right" colspan="9"><strong>Sub Process Total</strong></td>
            		<td align="right"><? echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? echo $tot_kg; ?>&nbsp;</td>
                    <td align="right"><? if($tot_gm>0){echo str_pad($tot_gm, 3, '0', STR_PAD_LEFT);}else echo "000";?>&nbsp;</td>
                    <td align="right">
						<? if($tot_mgm>0){echo str_pad($tot_mgm, 3, '0', STR_PAD_LEFT);}else echo "000"; ?>&nbsp;</td>
                    <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
            	</tr>
            	<?
            }
            	if($grand_tot_mgm>=1000){
            		$grand_tot_gm += intdiv($grand_tot_mgm, 1000);
            		$grand_tot_mgm = $grand_tot_mgm%1000;
            	}

            	if($grand_tot_gm>=1000){
            		$grand_tot_kg += intdiv($grand_tot_gm, 1000);
            		$grand_tot_gm = $grand_tot_gm%1000;
            	}
            ?>

            <tr class="tbl_bottom">
            	<td align="right" colspan="9"><strong>Grand Total</strong></td>
            	<td align="right"><? echo number_format($grand_tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                <td align="right"><? echo $grand_tot_kg; ?>&nbsp;</td>	 
                <td align="right"><? if($grand_tot_gm){echo str_pad($grand_tot_gm, 3, '0', STR_PAD_LEFT);} else{echo "000";} ?>&nbsp;</td>
                <td align="right"><? if($grand_tot_mgm>0){echo str_pad($grand_tot_mgm, 3, '0', STR_PAD_LEFT);}else{ echo "000";} ?>&nbsp;</td>
                <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
            </tr>
        </table>
        <br>
        <?
        echo signature_table(62, $com_id, "1030px");
        ?>
    </div>
	</div>
    <?
}

if ($action == "recipe_entry_print_5___") 
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	//$order_array = return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
	//	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

	$batch_array = array();
	if ($db_type == 0) {
		$sql = "select a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} else if ($db_type == 2) {
		$sql = "select a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id, c.new_batch_weight, sum(b.batch_qnty) as batch_qnty,a.is_sales,a.sales_order_no  from pro_batch_create_mst a, pro_batch_create_dtls b,pro_recipe_entry_mst c where a.id=b.mst_id and a.id=c.batch_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against,c.new_batch_weight, a.batch_no,a.entry_form,a.total_trims_weight,a.is_sales,a.sales_order_no order by a.id DESC";
	}
	// echo $sql;
	$result_sql = sql_select($sql);
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) 
	{
		$poIdarr[$row[csf("po_id")]]=$row[csf("po_id")];
		$is_sales=$row[csf("is_sales")];
		$sales_order_no=$row[csf("sales_order_no")];

	}
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls where id in(".implode(",",$poIdarr).") ", "id", "order_no");
	$po_arr = return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",",$poIdarr).")", 'id', 'po_number');

	
	foreach ($result_sql as $row) 
	{
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) {
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} else {
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['is_sales'] = $row[csf("is_sales")];
		$batch_array[$row[csf("id")]]['sales_order_no'] = $row[csf("sales_order_no")];
	}
	// echo "<pre>";
	// print_r($batch_array);


	$sql_recipe_mst = "SELECT id, labdip_no, company_id,working_company_id as w_com_id,location_id,new_batch_weight, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks, batch_qty from pro_recipe_entry_mst where id='$data[1]'";
	$dataArray = sql_select($sql_recipe_mst);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$new_batch_weight=$dataArray[0][csf('new_batch_weight')];


	$sql_po_arr = sql_select("select c.mst_id as batch_id, b.grouping from wo_po_break_down b,wo_po_details_master a,pro_batch_create_dtls c where  a.id=b.job_id and b.id=c.po_id and b.status_active=1 and b.is_deleted=0");
	$internal_ref_arr = array();

	foreach ($sql_po_arr as $row) {

		$internal_ref_arr[$row[csf('batch_id')]] = $row[csf('grouping')];

	}
	unset($sql_po_arr);

	$total_batch_weight =  $dataArray[0][csf('batch_qty')];

	$mst_id = $dataArray[0][csf('id')];
	$total_liquor_ratio_sql = sql_select("select liquor_ratio, total_liquor, check_id from pro_recipe_entry_dtls where mst_id=" . $mst_id . " and ratio>0 group by liquor_ratio, total_liquor, check_id");
	$total_liquor_ratio = 0;
	foreach($total_liquor_ratio_sql as $value){

		$total_liquor_ratio += $value[csf('liquor_ratio')];
	}
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];

	print_r($batch_array[$dataArray[0][csf('batch_id')]]['order']);
	
	?>
	<div style="width:1400px; font-size:6px">
		<table width="1400" cellspacing="0" align="right" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$w_com_id]; ?>
				</strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?

					foreach ($nameArray as $result) {
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]; ?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')]; ?>
						Zip Code: <? echo $result[csf('zip_code')];
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.$data[3]; ?></strong></u></td>
			</tr>
			<tr>
				<td width="130"><strong>System ID:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
				<td width="130"><strong>Labdip No: </strong></td>
				<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td width="130"><strong>Recipe Des.:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
			</tr>
			<tr>
				<td><strong>Batch No:</strong></td>
				<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
				<td><strong>Recipe Date:</strong></td>
				<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
				<td><strong>Order Source:</strong></td>
				<td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Buyer Name:</strong></td>
				<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				<td><strong>Booking:</strong></td>
				<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
				<td><strong>Color:</strong></td>
				<td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Color Range:</strong></td>
				<td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
            <!-- <td><strong>B/L Ratio:</strong></td> <td><? echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
            	<td><strong>Total Liq.:</strong></td><td> <? echo $dataArray[0][csf('total_liquor')]; ?></td>-->
            	<td><strong>Batch Weight:</strong></td>
            	<td><? $batch_weight=$new_batch_weight;//$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];
				echo $batch_weight; ?></td>
            	<td><strong>Trims Weight:</strong></td>
            	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
            </tr>
            <tr>
            	<td><strong>Order No.:</strong></td>
            	<td>
            		<? 
					if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3){ 
					echo "Sample Without Order";
					}
            		else{
						if($batch_array[$dataArray[0][csf('batch_id')]]['is_sales']==1)
						{
							echo $batch_array[$dataArray[0][csf('batch_id')]]['sales_order_no'] ;
							//echo $batch_array[$dataArray[0][csf('batch_id')]]['order'];
						}
						else
						{
							echo $batch_array[$dataArray[0][csf('batch_id')]]['order'];
					}
						
					} 
					?></td>
            		<td><strong>Method:</strong></td>
            		<td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                    <td><strong>Machine no:</strong></td>
            		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
            	</tr>
            	<tr>
            		<td><strong>Remarks:</strong></td>
            		<td ><? echo $dataArray[0][csf('remarks')]; ?></td>
            		<td><strong>Liquor Ratio:</strong></td>
            		<td ><? echo $total_liquor_ratio; ?></td>
            		<td><strong>Internal Ref:</strong></td>
            		<td ><? echo $internal_ref_arr[$dataArray[0][csf('batch_id')]]; ?></td>
            	</tr>
            </table>
            <br>
            <?
			$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
			$j = 1;
			$entryForm = $entry_form_arr[$batch_id_qry[0]];
		?>
            <table width="1400" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin:5px;" rules="all">
			<thead bgcolor="#dddddd" align="center">
				
					<tr bgcolor="#CCCCFF">
						<th colspan="5" align="center"><strong>Fabrication</strong></th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="80">Dia/ W. Type</th>
						<th width="200">Constrution & Composition</th>
						<th width="70">Gsm</th>
						<th width="70">Dia</th>
					</tr>
			</thead>
			<tbody>
				<?
					foreach ($batch_id_qry as $b_id) {
						 $batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
						$result_batch_query = sql_select($batch_query);
						foreach ($result_batch_query as $rows) {
							if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							$fabrication_full = $rows[csf("item_description")];
							$fabrication = explode(',', $fabrication_full);
				?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $j; ?></td>
								<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>			
								<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
								<td align="center"><? echo $fabrication[2]; ?></td>
								<td align="center"><? echo $fabrication[3]; ?></td>
							</tr>
				<?
							$j++;
						}
					}
				?>
			</tbody>
		</table>
        <br> <br> <br>
            <div style="width:100%;">
            	<table align="right" style="margin:5px;" cellspacing="0" width="1400" border="1" rules="all" class="rpt_table">
            		<thead bgcolor="#dddddd" align="center">
            			<tr>
            				<th rowspan="2" width="30">SL</th>
                			<th rowspan="2" width="110">Item Cat.</th>
                			<th rowspan="2" width="110">Product ID</th>
                			<th rowspan="2" width="220">Function Name</th>
                			<th rowspan="2" width="150">Item Group</th>
							<th rowspan="2" width="150">Item Description</th>
                			<th rowspan="2" width="80">Item Lot</th>
                			<th rowspan="2" width="50">UOM</th>
                			<th rowspan="2" width="100">Dose Base</th>
                			<th rowspan="2" width="100">Ratio/Dose</th>
                			<th colspan="3" width="200">Amount/KG</th>
                			<th rowspan="2" width="">Comments</th>
            			</tr>
            			<tr>
            				<th>KG</th>
            				<th>Gram</th>
            				<th>Miligram</th>
            			</tr>
            			
            		</thead>
            		<?
            		$i = 1;
            		$j = 1;
            		$mst_id = $data[1];
            		$com_id = $data[0];


            		$process_array = array();
            		$sub_process_data_array = array();
            		$sub_process_remark_array = array();
            		$sql_ratio = "select id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
            		$nameArray = sql_select($sql_ratio);
            		foreach ($nameArray as $row) {
            			if (!in_array($row[csf("sub_process_id")], $process_array)) {
            				$process_array[] = $row[csf("sub_process_id")];
            				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
            			}
            			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
            			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];


            		}
			
            		if ($db_type == 2) {
            			/*$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, b.id as dtls_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio from product_details_master a, pro_recipe_entry_dtls b where a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 order by b.sub_process_id, b.seq_no";*/
						
            			$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no,b.adj_perc,b.adj_qnty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
            			union
            			(
            			select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no,b.adj_perc,b.adj_qnty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
            		)  order by seq_no,sub_process_id";
            	} else if ($db_type == 0) {
            		$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no,b.adj_perc,b.adj_qnty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
            		union
            		(
            		select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no,b.adj_perc,b.adj_qnty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
            	)  ";
            }
			// echo $sql; //
            $sql_result = sql_select($sql);

            foreach ($sql_result as $row) {
            	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")]."**".$row[csf("adj_perc")]."**".$row[csf("adj_qnty")] . "$$$";

            }
			//var_dump($sub_process_data_array);
            foreach ($process_array as $process_id) {
            	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];
            	$l_ratio = $sub_process_remark_array[$process_id]['liquor_ratio'];

            	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
            	$remark = $sub_process_remark_array[$process_id]['remark'];
            	if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
            	$tot_ratio=1.5;
            	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;


            	?>
            	<tr bgcolor="#EEEFF0">
            		<td colspan="15" align="left"><b>Sub Process
            			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark; ?>
						<!--  .' Total Liquor(ltr): '.number_format($liquor_ratio,2,'.','');
						, Liquor Ratio: <?php echo number_format($l_ratio,2,'.',''); 
						?> -->
					</b>
            		</td>
            	</tr>
            	<?
            	$tot_ratio = 0;
            	$tot_kg = 0;
            	$tot_gm = 0;
            	$tot_mgm = 0;
            	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
				//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
				$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
				$sub_process_data = explode("$$$", $sub_process_dataArr);
				//echo count($sub_process_data).'='.$process_id.'<br>';
            	foreach ($sub_process_data as $data) {
            		$data = explode("**", $data);
            		$current_stock = $data[13];
            		$current_stock_check=number_format($current_stock,7,'.','');
                         		
            		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id ==140 || $sub_process_id ==141 || $process_id ==142 || $process_id ==143)
					if(in_array($process_id,$subprocessForWashArr))
					{
						$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];

                			if($dose_base_id==1 && $item_category_id==5){
                				
                				$amount = number_format($ratio*$total_batch_weight*$l_ratio/1000,6, '.', '');

                				$amount = explode('.',$amount);
                			}
                			else{
                				
                				$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');

                				$amount = explode('.',$amount);
                			}

                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
                			<tr bgcolor="<? echo $bgcolor; ?>">
                				<td><? echo $i; ?></td>
                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
                				<td><p><? echo $prod_id; ?></p></td>
                				<td><p><? echo $sub_group_name; ?></p></td>
                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
								<td><p><? echo $item_description ?></p></td>
                				<td><p><? echo $item_lot; ?></p></td>
                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
                				<td align="right"><? echo $amount[0]; ?>&nbsp;</td>
                				<td align="right"><? echo number_format(substr($amount[1], 0, 3)); ?>&nbsp;</td>
                				<td align="right"><? echo number_format(substr($amount[1], 3, 6)); ?>&nbsp;</td>
                				<td align="right"><? echo $comments; ?>&nbsp;</td>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
                			$grand_tot_kg += $amount[0];
                			$grand_tot_gm += substr($amount[1], 0, 3);
                			$grand_tot_mgm += substr($amount[1], 3, 6);
                			$tot_kg += $amount[0];
		                	$tot_gm += substr($amount[1], 0, 3);
		                	$tot_mgm += substr($amount[1], 0, 3);
                			$i++;
					}
					else
					{
						
					
                		if($current_stock_check>0)
                		{
                			$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];
							$adj_perc = $data[15];
							$adj_qnty = explode('.',$data[16]);
						
                			if($dose_base_id==1 && $item_category_id==5){
                				
                				$amount = number_format($ratio*$total_batch_weight*$l_ratio/1000,6, '.', '');

                				$amount = explode('.',$amount);
                			}
                			else{
                				
                				$amount = number_format($ratio*$total_batch_weight/100,6, '.', '');

                				$amount = explode('.',$amount);
                			}
							

                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
                			<tr bgcolor="<? echo $bgcolor; ?>">
                				<td><? echo $i; ?></td>
                				<td><p><? echo $item_category[$item_category_id]; ?></p></td>
                				<td><p><? echo $prod_id; ?></p></td>
                				<td><p><? echo $sub_group_name; ?></p></td>
                				<td><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
								<td><p><? echo $item_description ?></p></td>
                				<td><p><? echo $item_lot; ?></p></td>
                				<td align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                				<td><p><? echo $dose_base[$dose_base_id]; ?></p></td>
                				<td align="right"><? echo number_format($ratio, 6, '.', ''); ?>&nbsp;</td>
                				<td align="right"><? echo number_format($adj_qnty[0]);//$amount[0]; ?>&nbsp;</td>
                				<td align="right">
								 	<? 
										if(number_format(substr($adj_qnty[1], 0, 3))>0){
											//$day=str_pad($j, 2, '0', STR_PAD_LEFT);   
											$aj_gm=substr($adj_qnty[1], 0, 3);
											$aj_mg=str_pad($aj_gm, 3, '0', STR_PAD_LEFT); 
											echo $aj_mg;//number_format(substr($adj_qnty[1], 0, 3));
										}else echo "000";//$amount[1] ?>&nbsp;
								</td>
                				<td align="right">
									<?
									if(substr($adj_qnty[1], 3)>0) 
									{
										$aj_mili_g=substr($adj_qnty[1], 3, 6);
										$aj_mili_gram=str_pad($aj_mili_g, 3, '0', STR_PAD_RIGHT); 
										echo $aj_mili_gram;
									}
									else echo "000";
									//$amount[1] 
									?>&nbsp;</td>
                				<td align="right"><? echo $comments; ?>&nbsp;</td>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
                			$grand_tot_kg += $adj_qnty[0];
                			$grand_tot_gm += substr($adj_qnty[1], 0, 3);
                			$grand_tot_mgm += substr($adj_qnty[1], 3, 6);
                			$tot_kg += $adj_qnty[0];
		                	$tot_gm += number_format(substr($adj_qnty[1], 0, 3));
		                	$tot_mgm += number_format(substr($adj_qnty[1], 3, 6));;
                			$i++;
                		}
            		}
            	}

            	if($tot_mgm>=1000){
            		$tot_gm += intdiv($tot_mgm, 1000);
            		$tot_mgm = $tot_mgm%1000;
            	}

            	if($tot_gm>=1000){
            		$tot_kg += intdiv($tot_gm, 1000);
            		$tot_gm = $tot_gm%1000;	
            	}
				
            	?>
            	<tr class="tbl_bottom">
            		<td align="right" colspan="9"><strong>Sub Process Total</strong></td>
            		<td align="right"><? echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                    <td align="right"><? echo $tot_kg; ?>&nbsp;</td>
                    <td align="right"><? if($tot_gm>0){echo str_pad($tot_gm, 3, '0', STR_PAD_LEFT);}else echo "000";?>&nbsp;</td>
                    <td align="right">
						<? if($tot_mgm>0){echo str_pad($tot_mgm, 3, '0', STR_PAD_LEFT);}else echo "000"; ?>&nbsp;</td>
                    <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
            	</tr>
            	<?
            }
            	if($grand_tot_mgm>=1000){
            		$grand_tot_gm += intdiv($grand_tot_mgm, 1000);
            		$grand_tot_mgm = $grand_tot_mgm%1000;
            	}

            	if($grand_tot_gm>=1000){
            		$grand_tot_kg += intdiv($grand_tot_gm, 1000);
            		$grand_tot_gm = $grand_tot_gm%1000;
            	}
            ?>

            <tr class="tbl_bottom">
            	<td align="right" colspan="9"><strong>Grand Total</strong></td>
            	<td align="right"><? echo number_format($grand_tot_ratio, 6, '.', ''); ?>&nbsp;</td>
                <td align="right"><? echo $grand_tot_kg; ?>&nbsp;</td>	$aj_mg=str_pad($aj_gm, 3, '0', STR_PAD_LEFT);
                <td align="right"><? if($grand_tot_gm){echo str_pad($grand_tot_gm, 3, '0', STR_PAD_LEFT);} else{echo "000";} ?>&nbsp;</td>
                <td align="right"><? if($grand_tot_mgm>0){echo str_pad($grand_tot_mgm, 3, '0', STR_PAD_LEFT);}else{ echo "000";} ?>&nbsp;</td>
                <td align="right"><? //echo number_format($tot_ratio, 6, '.', ''); ?>&nbsp;</td>
            </tr>
        </table>
        <br>
        <?
        echo signature_table(62, $com_id, "1030px");
        ?>
    </div>
	</div>
    <?
}

if ($action == "recipe_entry_print_5")
{
	extract($_REQUEST);
	$data = explode('*', $data);

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id,location_name from lib_location","id", "location_name"); 
	$party_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');

	$sales_order_result = sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	$sales_arr = array();
	foreach ($sales_order_result as $sales_row) {
		$sales_arr[$sales_row[csf("id")]]["sales_order_no"] 	= $sales_row[csf("job_no")];
	}

	$batch_array = array();
	if ($db_type == 0) 
	{
		$sql = "SELECT a.id, a.batch_no,a.batch_against, a.entry_form, a.total_trims_weight, group_concat(b.po_id) as po_id, group_concat(b.prod_id) as prod_id, sum(b.batch_qnty) as batch_qnty,b.is_sales 
		from pro_batch_create_mst a,  pro_batch_create_dtls b 
		where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id order by a.id DESC";
	} 
	else if ($db_type == 2) 
	{
		$sql = "SELECT a.id, a.batch_no,a.batch_against,a.entry_form, a.total_trims_weight, listagg(b.po_id,',') within group (order by b.po_id) as po_id, listagg(b.prod_id,',') within group (order by b.prod_id) as prod_id,  sum(b.batch_qnty) as batch_qnty,b.is_sales  
		from pro_batch_create_mst a, pro_batch_create_dtls b 
		where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.batch_against, a.batch_no,a.entry_form,a.total_trims_weight,b.is_sales order by a.id DESC";
	}
	// echo $sql;
	$result_sql = sql_select($sql);
	foreach ($result_sql as $row) 
	{
		$order_no = '';
		$order_id = array_unique(explode(",", $row[csf("po_id")]));
		if ($row[csf("entry_form")] == 36) 
		{
			$batch_type = "<b> SUBCONTRACT ORDER </b>";
			foreach ($order_id as $val) {
				if ($order_no == "") $order_no = $order_array[$val]; else $order_no .= ", " . $order_array[$val];
			}
		} 
		else 
		{
			$batch_type = "<b> SELF ORDER </b>";
			foreach ($order_id as $val) 
			{
				if($row[csf("is_sales")] == 1){
					$order_no = $sales_arr[$val]["sales_order_no"];
				}
				else
				{
					if ($order_no == "") $order_no = $po_arr[$val]; else $order_no .= ", " . $po_arr[$val];
				}
			}
		}
		$batch_array[$row[csf("id")]]['batch_no'] = $row[csf("batch_no")];
		$batch_array[$row[csf("id")]]['batch_against'] = $row[csf("batch_against")];
		$batch_array[$row[csf("id")]]['total_trims_weight'] = $row[csf("total_trims_weight")];
		$batch_array[$row[csf("id")]]['batch_qty'] = $row[csf("batch_qnty")];
		$batch_array[$row[csf("id")]]['order'] = $order_no;
		$batch_array[$row[csf("id")]]['batch_type'] = $batch_type;
	}


	$sql_recipe_mst = "SELECT id, labdip_no, company_id,working_company_id as w_com_id,location_id, recipe_description, batch_id,machine_id, method, recipe_date, order_source, style_or_order, booking_id, total_liquor, batch_ratio, liquor_ratio, remarks, buyer_id, color_id, color_range, remarks from pro_recipe_entry_mst where id='$data[1]'";
	//echo $sql_recipe_mst;
	$dataArray = sql_select($sql_recipe_mst);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$batch_id=$dataArray[0][csf('batch_id')];
	$location_id=$dataArray[0][csf('location_id')];
	$booking_no=$dataArray[0][csf('style_or_order')];
	$order_array = return_library_array("select id, order_no from subcon_ord_dtls b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id' ", "id", "order_no");
	$po_arr = return_library_array("select b.id,b.po_number from wo_po_break_down b,pro_batch_create_dtls c where c.po_id=b.id and c.mst_id='$batch_id'", 'id', 'po_number');

	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$w_com_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";
	$group_id=$nameArray[0][csf('group_id')];

	$groupingArray = sql_select("SELECT grouping from wo_booking_mst a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$booking_no'");
	$internal_ref=$groupingArray[0][csf('grouping')];

	$sales_order_result = sql_select("SELECT b.id,b.job_no,b.within_group,b.sales_booking_no,b.style_ref_no from fabric_sales_order_mst b,pro_batch_create_mst c where b.id=c.sales_order_id and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.id='$batch_id' and c.company_id='$data[0]'");

	$sales_arr = array();
	foreach ($sales_order_result as $sales_row) {
		$style_ref	= $sales_row[csf("style_ref_no")];
	}
	?>
	<div style="width:930px; font-size:9px">
		<table width="930" cellspacing="0" align="right" border="0">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large">
					<strong><? echo 'Working Company : '.$company_library[$w_com_id]; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center">
					<?

						echo "Location Name: ".$location_arr[$location_id];
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u><strong><? echo 'Owner Company : '.$company_library[$data[0]].'<br>'.$data[3]; ?></strong></u></td>
			</tr>
			<tr>
				<td width="130"><strong>System ID:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('id')]; ?></td>
				<td width="130"><strong>Labdip No: </strong></td>
				<td width="175px"> <? echo $dataArray[0][csf('labdip_no')]; ?></td>
				<td width="130"><strong>Recipe Des.:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('recipe_description')]; ?></td>
			</tr>
			<tr>
				<td><strong>Batch No:</strong></td>
				<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['batch_no']; ?></td>
				<td><strong>Recipe Date:</strong></td>
				<td> <? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
				<td><strong>Order Source:</strong></td>
				<td><? echo $order_source[$dataArray[0][csf('order_source')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Buyer Name:</strong></td>
				<td><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				<td><strong>Booking:</strong></td>
				<td><? echo $dataArray[0][csf('style_or_order')]; ?></td>
				<td><strong>Color:</strong></td>
				<td> <? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Color Range:</strong></td>
				<td><? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
            	<!-- <td><strong>B/L Ratio:</strong></td> <td><? echo $dataArray[0][csf('batch_ratio')] . ':' . $dataArray[0][csf('liquor_ratio')]; ?></td>
            	<td><strong>Total Liq.:</strong></td><td> <? echo $dataArray[0][csf('total_liquor')]; ?></td>-->
            	<td><strong>Batch Weight:</strong></td>
            	<td><? $batch_weight=$batch_array[$dataArray[0][csf('batch_id')]]['batch_qty'];echo $batch_weight; ?></td>
            	<td><strong>Trims Weight:</strong></td>
            	<td><? echo $batch_array[$dataArray[0][csf('batch_id')]]['total_trims_weight']; ?></td>
            </tr>
            <tr>
            	<td><strong>Order No.:</strong></td>
            	<td><? if ($batch_array[$dataArray[0][csf('batch_id')]]['batch_against'] == 3) echo "Sample Without Order"; else echo $batch_array[$dataArray[0][csf('batch_id')]]['order']; ?></td>
        		<td><strong>Method:</strong></td>
        		<td><? echo $dyeing_method[$dataArray[0][csf('method')]]; ?></td>
                <td><strong>Machine No:</strong></td>
        		<td><? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
            </tr>
        	<tr>
				<td><strong>Style Reff:</strong></td>
        		<td colspan="1"><? echo $style_ref; ?></td>
				<td><strong>Internal Ref No:</strong></td>
        		<td><? echo $internal_ref; ?></td>
        	</tr>
			<tr>
        		<td><strong>Remarks:</strong></td>
        		<td colspan="4"><? echo $dataArray[0][csf('remarks')]; ?></td>
        	</tr>
        </table>
        <br>
        <?
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$j = 1;
		$entryForm = $entry_form_arr[$batch_id_qry[0]];
		?>
		<!-- Fabrication Part Start -->
        <table width="930" cellspacing="0"  class="rpt_table" border="1" style="font-size:20px;" rules="all">
			<thead bgcolor="#dddddd" align="center">
				<tr bgcolor="#CCCCFF">
					<th colspan="5" align="center"><strong>Fabrication</strong></th>
				</tr>
				<tr>
					<th width="30" height="40">SL</th>
					<th width="80" height="40">Dia/ W. Type</th>
					<th width="200" height="40">Constrution & Composition</th>
					<th width="70" height="40">Gsm</th>
					<th width="70" height="40">Dia</th>
				</tr>
			</thead>
			<tbody>
				<?
				foreach ($batch_id_qry as $b_id)
				{
					$batch_query = "SELECT  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 
					group by po_id, prod_id, item_description, width_dia_type";
					$result_batch_query = sql_select($batch_query);
					foreach ($result_batch_query as $rows)
					{
						if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$fabrication_full = $rows[csf("item_description")];
						$fabrication = explode(',', $fabrication_full);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center" height="40"><? echo $j; ?></td>
							<td align="center" height="40"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
							<td align="left" height="40"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
							<td align="center" height="40"><? echo $fabrication[2]; ?></td>
							<td align="center" height="40"><? echo $fabrication[3]; ?></td>
						</tr>
						<?
						$j++;
					}
				}
				?>
			</tbody>
		</table>
		<!-- Fabrication Part End -->
        <br> <br>
        <!-- Details Part Start -->
        <div style="width:100%;">
        	<table  cellspacing="0" width="1300" border="1" rules="all" class="rpt_table" style="font-size:20px;">
        		<thead bgcolor="#dddddd" align="center">
        			<th width="30" height="40" >SL</th>
        			<th width="110" height="40">Item Cat.</th>
        			<th width="110" height="40">Product ID</th>
        			<th width="200" height="40">Item Group</th>
        			<th width="220" height="40">Item Description</th>
        			<th width="80" height="40">Item Lot</th>
        			<th width="50" height="40">UOM</th>
        			<th width="100" height="40">Dose Base</th>
        			<th width="100" height="40">Ratio</th>
        			<th width="80" height="40">Recipe Qty</th>
        			<th width="" height="40">Comments</th>
        		</thead>
        		<?
        		$i = 1;
        		$j = 1;
        		$mst_id = $data[1];
        		$com_id = $data[0];


        		$process_array = array();
        		$sub_process_data_array = array();
        		$sub_process_remark_array = array();
        		$sql_ratio = "SELECT id, sub_process_id as sub_process_id, process_remark, total_liquor, liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id";
        		$nameArray = sql_select($sql_ratio);
        		foreach ($nameArray as $row) 
        		{
        			if (!in_array($row[csf("sub_process_id")], $process_array)) 
        			{
        				$process_array[] = $row[csf("sub_process_id")];
        				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
        			}
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        		}
        		// echo "<pre>";print_r($process_array);die;

        		if ($db_type == 2)
        		{
					$sql = "SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 
					union
					SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					  order by seq_no,sub_process_id";
        		}
        		else if ($db_type == 0)
        		{
					$sql = "SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0
					union
					SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)";
        		}
				//echo $sql;
        		$sql_result = sql_select($sql);
				$prodIdChk = array();
				$prodIdArr = array();
	            foreach ($sql_result as $row)
	            {
	            	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("batch_qty")] . "$$$";

					if($prodIdChk[$row[csf('prod_id')]] == "")
					{
						$prodIdChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
						array_push($prodIdArr,$row[csf('prod_id')]);
					}
	            }

				//var_dump($prodIdArr);
				$sql_prod_info = "SELECT id, avg_rate_per_unit from product_details_master where status_active=1 and is_deleted=0 ".where_con_using_array($prodIdArr,0,'id')."";
				//echo $sql_prod_info;
        		$prodInfoArray = sql_select($sql_prod_info);
				$prod_data_array = array();
        		foreach ($prodInfoArray as $row)
				{
        			$prod_data_array[$row[csf("id")]]['avg_rate_per_unit'] = $row[csf("avg_rate_per_unit")];
        		}
				//var_dump($prod_data_array);

				$grand_tot_ratio = 0;
				$grand_tot_recipe_qty = 0;
				$grand_tot_avg_rate = 0;
				
	            foreach ($process_array as $process_id)
	            {
	            	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];

	            	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
	            	$remarks = $sub_process_remark_array[$process_id]['remark'];
		            if ($remarks == '') $remark = ''; else $remark .= $remarks . ',';	            	
	            	$tot_ratio=1.5;
	            	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;

	            	?>
	            	<tr bgcolor="#EEEFF0">
	            		<td colspan="11" align="left" height="25"><b>Sub Process
	            			Name:- <? echo $dyeing_sub_process[$process_id] . ', ' . $remark .' Total Liquor(ltr): '.$liquor_ratio.', '. 'Levelling  Water(Ltr): '.number_format($leveling_water,2,'.',''); ?></b>
	            		</td>
	            	</tr>
	            	<?
	            	$tot_ratio = 0;$tot_recipe_qty = 0;
	            	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';

	            	foreach ($sub_process_data as $data) 
	            	{
	            		$data = explode("**", $data);
	            		$current_stock = $data[13];
	            		$current_stock_check=number_format($current_stock,7,'.','');
	            		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id == 140 || $process_id == 141 || $process_id == 142 || $process_id == 143)
						if (in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];
							$batch_qty = $data[15];

							if ($dose_base_id == 1) {

								$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');

							} else {
								$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');

							}
                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
                			<tr bgcolor="<? echo $bgcolor; ?>">
                				<td height="40"><? echo $i; ?></td>
                				<td height="40"><p><? echo $item_category[$item_category_id]; ?></p></td>
                				<td height="40"><p><? echo $prod_id; ?></p></td>
                				<td height="40"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                				<td height="40"><p><? echo $item_description; ?></p></td>
                				<td height="40"><p><? echo $item_lot; ?></p></td>
                				<td height="40" align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
                				<td height="40"><p><? echo $dose_base[$dose_base_id]; ?></p></td>
                				<td height="40" align="right"><strong><? echo number_format($ratio, 6, '.', ''); ?></strong>&nbsp;</td>
                				<td height="40" align="right" title="Recipe Qty=(Total Liquor(ltr)/1000)*Ratio"><strong><? echo $recipe_qty; ?></strong>&nbsp;</td>
                				<td height="40" align="right"><? echo $comments; ?>&nbsp;</td>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
                			$tot_recipe_qty +=$recipe_qty;
                			$grand_tot_recipe_qty +=$recipe_qty;
							$grand_tot_avg_rate +=$prod_data_array[$prod_id]['avg_rate_per_unit']*$recipe_qty;
                			$i++;
						}
						else
						{
	                		if($current_stock_check>0)
	                		{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];
								$batch_qty = $data[15];

								if ($dose_base_id == 1) {

									$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');

								} else {
									$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');

								}
	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr bgcolor="<? echo $bgcolor; ?>">
	                				<td height="40"><? echo $i; ?></td>
	                				<td height="40"><p><? echo $item_category[$item_category_id]; ?></p></td>
	                				<td height="40"><p><? echo $prod_id; ?></p></td>
	                				<td height="40"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td height="40"><p><? echo $item_description; ?></p></td>
	                				<td height="40"><p><? echo $item_lot; ?></p></td>
	                				<td height="40" align="center"><? echo $unit_of_measurement[$unit_of_measure]; ?>&nbsp;</td>
	                				<td height="40"><p><? echo $dose_base[$dose_base_id]; ?></p></td>
	                				<td height="40" align="right"><strong><? echo number_format($ratio, 6, '.', ''); ?></strong>&nbsp;</td>
	                				<td height="40" align="right" title="Recipe Qty=(Total Liquor(ltr)/1000)*Ratio"><strong><? echo $recipe_qty; ?></strong>&nbsp;</td>
	                				<td height="40" align="right"><? echo $comments; ?>&nbsp;</td>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
	                			$tot_recipe_qty +=$recipe_qty;
	                			$grand_tot_recipe_qty +=$recipe_qty;
								$grand_tot_avg_rate +=$prod_data_array[$prod_id]['avg_rate_per_unit']*$recipe_qty;
	                			$i++;
	                		}
	            		}
	            	}
	            }
	            ?>
	            <tr class="tbl_bottom">
	            	<td align="right" colspan="8" height="20"><strong>Grand Total :</strong></td>
	            	<td align="right" height="20" ><strong><? echo number_format($grand_tot_ratio, 6, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right"  height="20"><strong><? echo number_format($grand_tot_recipe_qty, 4, '.', ''); ?></strong>&nbsp;</td>
	                <td align="right"  height="20">&nbsp;</td>
	            </tr>
				<tr class="tbl_bottom">
	            	<td align="right"  height="20" colspan="8"><strong>Total Cost[TK] : </strong></td>
	            	<td align="center"  height="20" colspan="2" ><strong><? echo number_format($grand_tot_avg_rate, 4, '.', ''); ?></strong>&nbsp;</td>
	                <td align="right"  height="20">&nbsp;</td>
	            </tr>
				<tr class="tbl_bottom">
	            	<td align="right"  height="20"  colspan="8"><strong>Cost Per KG[TK] : </strong></td>
	            	<td align="center"  height="20" colspan="2" ><strong><? $per_kg = ($grand_tot_avg_rate /$batch_weight); echo number_format($per_kg, 4, '.', ''); ?></strong>&nbsp;</td>
	                <td align="right"  height="20">&nbsp;</td>
	            </tr>
    		</table>
	        <br>
	        <?
	        echo signature_table(62, $com_id, "1030px","","50px");
	        ?>
		</div>
	</div>
	<?
}

if ($action == "recipe_entry_print_9")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$recipe_id=$data[1];
	$working_company=$data[4];
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');
	$user_name_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');

	$sql = "SELECT c.id as recipe_id, c.labdip_no, c.company_id,c.working_company_id as w_com_id,c.location_id, recipe_description, c.batch_id, c.machine_id, c.method, c.recipe_date, c.order_source, c.style_or_order, c.booking_id, c.total_liquor, c.batch_ratio, c.liquor_ratio, c.remarks, c.buyer_id, c.color_id, c.color_range, c.remarks as recipe_remarks, c.insert_date, c.inserted_by, c.batch_qty as qnty, a.id, a.batch_no, a.batch_date, a.booking_no_id, a.booking_no, a.booking_without_order, a.sales_order_no, a.batch_type_id, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks as batch_remarks, a.collar_qty, a.cuff_qty, a.shift_id, a.double_dyeing, a.sales_order_id,a.is_sales, a.booking_entry_form , a.save_string
	from pro_recipe_entry_mst c, pro_batch_create_mst a
	where c.batch_id=a.id and c.id=$data[1] and a.is_sales=1 and a.status_active=1 and a.is_deleted=0";
	// echo $sql;die;
	$dataArray = sql_select($sql);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$batch_id=$dataArray[0][csf('batch_id')];
	$location_id=$dataArray[0][csf('location_id')];
	$booking_no=$dataArray[0][csf('style_or_order')];
	$sales_order_id=$dataArray[0][csf('sales_order_id')];
	$color_id=$dataArray[0][csf('color_id')];
	$save_string_data = $dataArray[0][csf('save_string')];
	$sales_order_no = $dataArray[0][csf('sales_order_no')];
	$batch_weight = $dataArray[0][csf('batch_weight')];
	// $batch_weight = $dataArray[0][csf('batch_qnty')];

	// Process Loss
	$sales_sql = "SELECT b.job_no_mst as booking_no,b.color_type_id,b.body_part_id,b.gsm_weight, b.dia, b.width_dia_type,b.process_loss,sum(b.grey_qty) qnty,b.color_id from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.job_no='$sales_order_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.job_no_mst ,b.color_type_id,b.body_part_id,b.gsm_weight, b.dia, b.width_dia_type,b.process_loss,b.color_id ";
	$sales_result = sql_select($sales_sql);
	foreach ($sales_result as $row)
	{
		$sales_process_loss_array[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]]['process_loss'] = $row[csf('process_loss')];
	}

	if ($dataArray[0][csf('is_sales')] == 1)
	{
		$sales_data = sql_select("SELECT id,job_no,sales_booking_no,within_group,buyer_id,style_ref_no, po_buyer from fabric_sales_order_mst where id=$sales_order_id");

		if ($sales_data[0][csf("within_group")] == 1)
		{
			$booking_data = sql_select("SELECT a.booking_no,a.buyer_id, a.fabric_source, a.booking_type, a.is_short, b.job_no, c.pub_shipment_date, c.grouping, d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d 
			where a.booking_no=b.booking_no and b.po_break_down_id = c.id and c.job_id=d.id and  a.booking_no='$booking_no' and b.fabric_color_id='$color_id' and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 
			group by a.booking_no,a.buyer_id, a.fabric_source, a.booking_type, a.is_short, b.job_no, c.pub_shipment_date, c.grouping, d.style_ref_no");

			foreach ($booking_data as $row)
			{
				$job_number .= $row[csf('job_no')].",";
				$job_style .= $row[csf('style_ref_no')].",";
				$internal_ref .= $row[csf('grouping')].",";
				$book_fabric_source[$row[csf('booking_no')]]=$row[csf('fabric_source')];
				// $booking_type_id[$row[csf('booking_no')]]=$row[csf('booking_type')];

				if($row[csf("booking_type")]==1 && $row[csf("is_short")]==2)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Main";
	            }
	            else if($row[csf("booking_type")]==1 && $row[csf("is_short")]==1)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Short";
	            }
	            else if($row[csf("booking_type")]==4)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Sample";
	            }

	            if($min_shipment_date == ""){
					$min_shipment_date = $row[csf('pub_shipment_date')];
				}

				if($max_shipment_date == ""){
					$max_shipment_date = $row[csf('pub_shipment_date')];
				}

				if(strtotime($row[csf('pub_shipment_date')]) > strtotime($min_shipment_date))
				{
					$min_shipment_date = $min_shipment_date;
				}else{
					$min_shipment_date = $row[csf('pub_shipment_date')];
				}


				if(strtotime($row[csf('pub_shipment_date')]) > strtotime($max_shipment_date))
				{
					$max_shipment_date = $row[csf('pub_shipment_date')];
				}
				else
				{
					$max_shipment_date = $max_shipment_date;
				}
			}
			$job_number = chop($job_number,",");
			$job_style = chop($job_style,",");
			$internal_ref = chop($internal_ref,",");
			//$job_number = $booking_data[0][csf("job_no")];
			$buyer_id = $booking_data[0][csf("buyer_id")];
			$po_number = $sales_data[0][csf("job_no")];
			//$job_style = $job_array[$job_number]['style_ref_no'];
			//$internal_ref = $job_array[$job_number]['int_ref'];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("po_buyer")];
		}
		else
		{
			$po_number = $sales_data[0][csf("job_no")];
			$job_number = "";
			$buyer_id = $sales_data[0][csf("buyer_id")];
			$job_style = $sales_data[0][csf("style_ref_no")];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("buyer_id")];
		}
	}
	$job_no = implode(",", array_unique(explode(",", $job_number)));
	$jobstyle = implode(",", array_unique(explode(",", $job_style)));
	$buyer = implode(",", array_unique(explode(",", $buyer_id)));
	$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
	$file_nos = implode(",", array_unique(explode(",", $file_nos)));

	if ($job_no!="") {$job_cond="and job_no_mst in('$job_no')";}else{$job_cond="";}
	$lapdip_no=sql_select("SELECT job_no_mst,color_name_id,po_break_down_id,lapdip_no, pantone_no from wo_po_lapdip_approval_info where status_active=1 and is_deleted=0 $job_cond");
	foreach ($lapdip_no as $row)
	{
		$lapdip_no_arr[$row[csf('job_no_mst')]][$row[csf('color_name_id')]]['lapdip_no'] = $row[csf('lapdip_no')];
		$lapdip_no_arr[$row[csf('job_no_mst')]][$row[csf('color_name_id')]]['pantone_no'] = $row[csf('pantone_no')];
	}

	$chem_issue_requ=sql_select("SELECT a.REQU_PREFIX_NUM, c.RECIPE_ID from DYES_CHEM_ISSUE_REQU_MST a, DYES_CHEM_ISSUE_REQU_DTLS_CHILD c
	where a.id=c.mst_id and c.RECIPE_ID=$recipe_id and a.ENTRY_FORM=156 and a.status_active=1 and a.is_deleted=0");
	// echo $chem_issue_requ;
	foreach ($chem_issue_requ as $row) 
	{
		$recipe_requ_no_arr[$row['RECIPE_ID']] .= $row['REQU_PREFIX_NUM'].',';
	}
	$recipe_requ_no= implode(",", array_unique(explode(",", implode(",", $recipe_requ_no_arr)))) ;

	$groupingArray = sql_select("SELECT grouping from wo_booking_mst a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$booking_no'");
	$internal_ref=$groupingArray[0][csf('grouping')];

	$path = '../';
	?>
	<div style="width:1200px; font-size:9px">
		<table width="1200" cellspacing="0" align="center" border="0" style="font-size: 20px; display: inline; ">
			<tr width="1200">
				<td width="20%" rowspan="2"  colspan="2" align="center" style="font-size:50px"><img align="left" src='<? echo $path.$imge_arr[$working_company]; ?>' height='60'  /></td>
				
				<td width="45%" colspan="8" align="center" style="font-size:30px;padding-left: 80px; ">
					<strong><? echo $company_library[$w_com_id]; ?></strong>
					<p style="margin-top: 0px; font-size:18px;"><i>
						<?
						$nameArray=sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$working_company");
						foreach ($nameArray as $result)
						{
						    ?>
						    <? echo $result[csf('plot_no')]; ?>
						    <? echo $result[csf('level_no')]?>
						    <? echo $result[csf('road_no')]; ?>
						    <? echo $result[csf('block_no')];?>
						    <? echo $result[csf('city')];?>
						    <? echo $result[csf('zip_code')]; ?>
						    <?php echo $result[csf('province')]; ?>
						    <? echo $country_arr[$result[csf('country_id')]];
						}
						?>
					</i></p>
				</td>
				<td width="10%"  colspan="1" align="center" style="font-size:30px"></td>
				<td width="25%" colspan="2" style="padding-left: 1px;font-size:1rem; float: center" align="right"></td>
			</tr>
			<tr width="1200">
				<td width="50%" colspan="6" align="center" style="font-size:17px; padding-left: 200px;"> <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Order Owned By: <? echo $company_library[$data[0]]; ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
				<td width="10%"  colspan="1" align="center" style="font-size:30px">&nbsp;</td>
				<td colspan="1" width="30%" id="barcode_img_id" align="right" style="font-size:24px padding-left: 10px; float: right;"></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="font-size:30px"></td>
				<td colspan="8" align="center" style="font-size:18px; padding-left: 100px;"><strong><u>Topping Adding Stripping Recipe Entry</u></strong></td>
			</tr>
			<tr>
				<td colspan="9"></td>
			</tr>
		</table>
		<br><br>

		<table width="1200" cellspacing="0" align="center"  border="1" rules="all" class="rpt_table" style="font-size: 17px;">
			<tr>
				<td width="160"><strong>Buyer</strong></td>
				<td width="220px">:&nbsp;<?
					if($dataArray[0][csf('is_sales')] ==1)
					{

						echo $buyer_library[$sales_buyer_id];
					}
					else if ($dataArray[0][csf('batch_against')] == 3)
					{
						echo $buyer_library[$buyer_id_booking];
					}
					else
					{
						echo $buyer_library[$buyer];
					}
					?>
				</td>
				<td width="150"><strong>Batch No</strong></td>
				<td width="200px">:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
				<td width="150"><strong>Recipe Creation Date</strong></td>
				<td width="200px">:&nbsp;<? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
			</tr>	
			<tr>
				<td><strong>System Recipe ID</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('recipe_id')]; ?></td>
				<td><strong>Colour</strong></td>
				<td>:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
				<td><strong>Recipe Creation Time</strong></td>
				<td>:&nbsp;<? 
					$dateString = $dataArray[0][csf('insert_date')];
					// Create a DateTime object from the string
					$dateTime = DateTime::createFromFormat('d-M-y h.i.s A', $dateString);
					// Get the time part
					$timeOnly = $dateTime->format('h:i:s A');
					// Output the result
					echo $timeOnly; 
					?>
				</td>
			</tr>
			<tr>
				<td><strong>System Rquisition ID.</strong></td>
				<td>:&nbsp;<? echo chop($recipe_requ_no,","); ?></td>
				<td><strong>Batch Qty (KG)</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
				<td><strong>Recipe Creator</strong></td>
				<td>:&nbsp;<? echo $user_name_library[$dataArray[0][csf('inserted_by')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Internal No</strong></td>
				<td>:&nbsp;<? echo $internal_ref; ?></td>
				<td><strong>Pantone No</strong></td>
				<td>:&nbsp;<? echo $lapdip_no_arr[$job_no][$dataArray[0][csf('color_id')]]['pantone_no']; ?></td>
				<td><strong>Batch Against</strong></td>
				<td>:&nbsp;<? echo $batch_against[$dataArray[0][csf('batch_against')]];; ?></td>
			</tr>
			<tr>
				<td><strong>Sys. Booking No.</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('style_or_order')]; ?></td>				
				<td><strong>Labdip No</strong></td>
				<td>:&nbsp;<? echo $lapdip_no_arr[$job_no][$dataArray[0][csf('color_id')]]['lapdip_no']; ?></td>
				<td><strong>Batch Type</strong></td>
				<td>:&nbsp;<? echo $batch_type_arr[$dataArray[0][csf('batch_type_id')]]; ?></td>
			</tr>			
			<tr>
				<td><strong>FSO No</strong></td>
				<td>:&nbsp;<? echo $po_number; ?></td>
				<td><strong>Colour Range</strong></td>
				<td>:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
				<td><strong>Fabric Source</strong></td>
				<td>:&nbsp;<? echo $fabric_source[$book_fabric_source[$dataArray[0][csf('booking_no')]]]; ?></td>
			</tr>			
			<tr>
				<td><strong>Job No.</strong></td>
				<td>:&nbsp;<? echo $job_no; ?></td>
				<td width="150"><strong>Dyeing Part</strong></td>
				<td width="200px">:&nbsp;<?
					if ($dataArray[0][csf('double_dyeing')]==1)
					{
						echo "Duble Part";
					}
					if ($dataArray[0][csf('double_dyeing')]==2)
					{
						echo "Single Part";
					}
					?></td>
				<td><strong>Shipment Date</strong></td>
				<td>:&nbsp;<?
					if (trim($ship_date) != "0000-00-00" && trim($ship_date) != "") echo implode(",", array_unique(explode(",", $ship_date))); else echo "&nbsp;";
					if($min_shipment_date != "")
					{
						echo " ".change_date_format($min_shipment_date)." To ".change_date_format($max_shipment_date);
					}
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Style Refernce No.</strong></td>
				<td>:&nbsp;<? echo $jobstyle; ?></td>
				<td><strong>Match With</strong></td>
				<td>:&nbsp;</td>
				<td><strong>M/C No.</strong></td>
				<td>:&nbsp;<?
					if ($db_type == 2) {
						$dyeing_machine = return_field_value("(machine_no || ': ' || prod_capacity || '(kg)') as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
					} else if ($db_type == 0) {
						$dyeing_machine = return_field_value("concat(machine_no,': ',prod_capacity,'(kg)') as machine_name", "lib_machine_name", "id=" . $dataArray[0][csf('dyeing_machine')], "machine_name");
					}
					echo $dyeing_machine;
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Booking Type</strong></td>
				<td>:&nbsp;<? echo $booking_type_arr[$dataArray[0][csf('booking_no')]]; ?></td>
				<td><strong>Recipe Description</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('recipe_description')]; ?></td>
				<td><strong>Trolly No. </strong></td>
				<td>:&nbsp;<? ?></td>
			</tr>
			<tr>
				<td><strong>Remarks</strong></td>
				<td colspan="5">:&nbsp;<? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
        <br>
        <?
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$j = 1;
		$entryForm = $entry_form_arr[$batch_id_qry[0]];

		// Fabric Description
		$sql_dtls = "SELECT e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, b.batch_qnty AS batch_qnty, b.roll_no as roll_no, b.item_description,  b.prod_id, b.body_part_id, b.width_dia_type, b.remarks, b.item_size, b.batch_qty_pcs, b.color_type, d.machine_dia,d.machine_gg, d.stitch_length as stitch_length,d.febric_description_id, d.yarn_lot,d.yarn_count, d.brand_id, c.barcode_no, e.knitting_source,a.color_id, f.dia_width, f.gsm
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e, product_details_master f 
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and b.prod_id=f.id and a.working_company_id=$data[0] and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)";

		//echo $sql_dtls;die;
		$sql_result = sql_select($sql_dtls);
		foreach ($sql_result as $key => $row)
		{
			$str_ref=$row[csf('width_dia_type')]."*".$row[csf('dia_width')]."*".$row[csf('gsm')]."*".$row[csf('color_type')];

			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['receive_basis']=$row[csf('receive_basis')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['booking_without_order']=$row[csf('booking_without_order')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['is_sales']=$row[csf('is_sales')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['batch_qnty']+=$row[csf('batch_qnty')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['batch_qty_pcs']+=$row[csf('batch_qty_pcs')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['roll_no']+=$row[csf('roll_no')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['item_description']=$row[csf('item_description')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['remarks'].=$row[csf('remarks')].',';
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['febric_description_id']=$row[csf('febric_description_id')];
			//$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['gsm']=$row[csf('gsm')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['knitting_source']=$row[csf('knitting_source')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['color_id']=$row[csf('color_id')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['gsm']=$row[csf('gsm')];
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['yarn_lot'].=$row[csf('yarn_lot')].',';
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['brand_id'].=$row[csf('brand_id')].',';
			$data_arr[$row[csf('body_part_id')]][$row[csf('prod_id')]][$str_ref]['num_of_rows']++;

			// $barcode_data_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];

			if ($row[csf('item_size')]!='')
			{
				$item_size_arr[$row[csf('body_part_id')]][$row[csf('item_size')]]+=$row[csf('batch_qty_pcs')];
			}
	        $descr_id_arr[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
		}
		// echo "<pre>";print_r($data_arr);

		if (!empty($descr_id_arr))
		{
			$lib_fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");

			$determination_sql = sql_select("select a.id, a.construction, b.copmposition_id,b.type_id, b.percent, a.fabric_composition_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ".where_con_using_array($descr_id_arr,1,'a.id'));
			$f_comp_arr=array();
			foreach ($determination_sql as $d_row) {
				// $comp = $lib_fabric_composition[$d_row[csf("fabric_composition_id")]];
				$f_comp_arr[$d_row[csf("id")]]=$lib_fabric_composition[$d_row[csf("fabric_composition_id")]];
			}
		}
		// echo "<pre>";print_r($f_comp_arr);
		?>
		<div style="float:left; font-size:18px;"><strong><u>Fabric Description</u></strong></div>
		<table align="center" cellspacing="0" width="1200" border="1" rules="all" class="rpt_table" style="border-top:none;font-size: 18px;">
			<thead bgcolor="#dddddd" align="center" style="font-size: 18px;">
				<tr>
					<th width="30">SL</th>
					<th width="80">Body part</th>
					<th width="80">Color Type</th>
					<th width="200">Const. X Comp.</th>
					<th width="70">D/W Type</th>
					<th width="50">Fin. Dia</th>
					<th width="50">Fin. GSM</th>
					<th width="80">Brand</th>
					<th width="80">Yarn Lot</th>
					<th width="70">Grey Qty.</th>
					<th width="50">Roll No.</th>
					<th width="80">TTL WT</th>
					<th width="80">Process Loss</th>
					<th>Remarks</th>
				</tr>
			</thead>
			<?
			$row_count = 0;$tot_batch_qty=0;
			foreach ($data_arr as $body_part_id => $body_partIdv)
			{
				foreach ($body_partIdv as $prod_id => $prod_idv)
				{
					foreach ($prod_idv as $strRef => $row)
					{
						$row_count++;
						$tot_batch_qty += $row['batch_qnty'];
					}
				}
			}
			// echo $row_count.'=='.$tot_batch_qty;

			$i = 1;
			foreach ($data_arr as $body_part_id => $body_partIdv)
			{
				$sub_total_roll_number=0; $sub_total_batch_qty=0;$sub_total_finish_qty=0;
				foreach ($body_partIdv as $prod_id => $prod_idv)
				{
					foreach ($prod_idv as $strRef => $row)
					{
						$dataStr = explode("*", $strRef);
						$width_dia_type=$dataStr[0];
						$dia=$dataStr[1];
						$gsm=$dataStr[2];
						$color_type_id=$dataStr[3];

						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$desc = explode(",", $row['item_description']);
						$remarks = implode(",", array_filter(array_unique(explode(",", $row['remarks'])))) ;

						$yarn_lot = implode(",", array_unique(explode(",", chop($row['yarn_lot'],","))));
						$brand_id_arr = array_unique(explode(",", chop($row['brand_id'],",")));

						$brand_value = "";
						foreach ($brand_id_arr as $bid)
						{
							if ($bid > 0) {
								if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
							}
						}

						$is_sales=$row['is_sales'];
						if($is_sales==1) //Sales
						{
							$process_loss=$sales_process_loss_array[$sales_order_no][$body_part_id][$color_type_id][$gsm][$dia][$width_dia_type]['process_loss'];
						}
						else
						{
							if($row['booking_without_order']==1)
							{
								//$color_type_id=$color_type_array[$booking_no][$body_part_id]['color_type_id'];
								$process_loss=$process_loss_array[$booking_no][$body_part_id]['color_type_id'];
							}
							else
							{
								$color_id=$dataArray[0][csf('color_id')];
								//$color_type_id=$color_type_array_precost[$booking_no][$body_part_id][$row['febric_description_id']][$row['gsm']]['color_type_id'];
								$process_loss= $process_loss_array[$booking_no][$body_part_id][$row['febric_description_id']][$gsm][$row['color_id']];
							}
						}

						?>
						<tr>
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="80" title="<? echo $body_part_id; ?>" style="word-break:break-all;"><? echo $body_part[$body_part_id]; ?></td>
							<td width="80" style="word-break:break-all;"><? echo $color_type[$color_type_id]; ?></td>
							<td width="150" style="word-break:break-all;" title="<?=$row["febric_description_id"];?>"><? echo $desc[0] . "," . $f_comp_arr[$row["febric_description_id"]]; ?></td>
							<td width="70" style="word-break:break-all;"><? 
							if ($fabric_typee[$width_dia_type]=='Open Width') 
							{
								echo "Open";
							}
							else
							{
								echo $fabric_typee[$width_dia_type];
							}
							?></td>
							<td width="50" align="center" style="word-break:break-all;"><? echo $dia; ?></td>
							<td width="50" align="center" style="word-break:break-all;"><? echo $gsm; ?></td>
							<td width="80" style="word-break:break-all;"><? echo $brand_value; ?></td>
							<td width="80" style="word-break:break-all;"><? echo $yarn_lot;
							//echo implode(',', array_unique(explode(",", $yarn_lot))); ?></td>
							<td width="70" align="right" style="word-break:break-all;"><? echo number_format($row['batch_qnty'], 2); ?></td>
							<td align="center" width="50" style="word-break:break-all;"><? echo $row['num_of_rows']; ?></td>
							<?
							if ($test==0) 
							{
								?>
								<td width="80" rowspan="<?=$row_count;?>" title="<? echo $row_count; ?>" style="word-break:break-all;" align="right"><b><? echo number_format($ttl_wt=$tot_batch_qty+$dataArray[0][csf('total_trims_weight')], 2); ?></b></td>
								<?
								$test++;
							}
							?>
							

							<td width="80" align="right"><? if ($process_loss>0) 
							{
								echo $process_loss;
							}
							else{
								echo '0';
							} ?></td>
							<td><? echo $remarks;?></td>
						</tr>
						<?php
						$total_roll_number += $row['num_of_rows'];
						$total_batch_qty += $row['batch_qnty'];
						$total_ttl_wt += $ttl_wt;

						$sub_total_roll_number += $row['num_of_rows'];
						$sub_total_batch_qty += $row['batch_qnty'];
						$sub_total_ttl_wt += $ttl_wt;
						$i++;
					}
				}
			}
			if($save_string_data!=""){
			?>
			<?php
				$save_string_data_arr= explode('!!',$save_string_data);
				foreach($save_string_data_arr as $key=>$row){
					$col=explode('_',$row);
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="left"><? echo $key+1; ?></td>
						<td align="left"><? echo $col[2];?></td>
						<td colspan="7" align="center"><? echo $col[0];?></td>
						<td align="right"><? echo $col[1]; ?></td>
						<td colspan="4" align="center" style="border:none;">&nbsp;<? ?></td>

					</tr>
					<?
				}
			}
			?>
	    </table>
        <br> <br>

        <div style="width:100%;">
        	<table  cellspacing="0" width="1200" border="1" rules="all" class="rpt_table" style="font-size:20px;">
        		<thead bgcolor="#dddddd" align="center">
					<tr>
						<th rowspan="2" width="30" height="40" >SL</th>
						<th rowspan="2" width="150" height="40">Group Name</th>
						<th rowspan="2" width="200" height="40">Product Name</th>
						<th rowspan="2" width="80" height="40">Product Lot</th>						
						<th rowspan="2" width="50" height="40" title="Ratio">Conc.</th>
						<th rowspan="2" width="50" height="40">Dose Base</th>
						<th colspan="3" width="150">Weight (KG)</th>
						<th rowspan="2" width="50" height="40">Rate (TK)</th>
						<th rowspan="2" width="80" height="40">TTL Cost (TK)</th>
						<th rowspan="2" width="" height="40">Process</th>
					</tr>
					<tr>
						<th>KG</th>
						<th>GM</th>
						<th>MG</th>
                	</tr>
        		</thead>
        		<?
        		$i = 1;
        		$j = 1;
        		$mst_id = $data[1];
        		$com_id = $data[0];

        		$process_array = array();
        		$sub_process_data_array = array();
        		$sub_process_remark_array = array();
        		$sql_ratio = "SELECT id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by sub_seq";
        		// echo $sql_ratio;
        		$nameArray = sql_select($sql_ratio);
        		foreach ($nameArray as $row) {
        			if (!in_array($row[csf("sub_process_id")], $process_array)) {
        				$process_array[] = $row[csf("sub_process_id")];
        				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
        			}
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        		}

        		if ($db_type == 2)
        		{
					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.working_company_id='$com_id' and b.ADJ_RATIO>0 and b.RATIO>0 and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
					union
					(
					SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  order by seq_no,sub_process_id";
        		}
        		else if ($db_type == 0)
        		{
					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.working_company_id='$com_id' and b.ADJ_RATIO>0 and b.RATIO>0 and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
					union
					(
						SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  ";
        		}
				 echo $sql;
        		$sql_result = sql_select($sql);
				$prodIdChk = array();
				$prodIdArr = array();
				$process_count = array();
	            foreach ($sql_result as $row)
	            {
	            	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("batch_qty")] . "$$$";

					if($prodIdChk[$row[csf('prod_id')]] == "")
					{
						$prodIdChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
						array_push($prodIdArr,$row[csf('prod_id')]);
					}
					$process_count[$row[csf("sub_process_id")]]++;

					/*$key_1 = $row[csf("batch_id")] . $row[csf("order_id")] . $row[csf("gsm")] . $row[csf("fabric_description_id")];
					$gData_1[$key_1] += 1;*/
	            }
	            // echo '<pre>';print_r($process_count);
				// var_dump($process_count);
				$sql_prod_info = "SELECT id, avg_rate_per_unit from product_details_master where status_active=1 and is_deleted=0 ".where_con_using_array($prodIdArr,0,'id')."";
				//echo $sql_prod_info;
        		$prodInfoArray = sql_select($sql_prod_info);
				$prod_data_array = array();
        		foreach ($prodInfoArray as $row)
				{
        			$prod_data_array[$row[csf("id")]]['avg_rate_per_unit'] = $row[csf("avg_rate_per_unit")];
        		}
				//var_dump($prod_data_array);
        		$avg_rage_sql="SELECT a.prod_id,
				sum(case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end) as rcv_qty,
				sum(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end) as iss_qty,
				sum(case when a.transaction_type in (1,4,5) then a.CONS_AMOUNT else 0 end) as rcv_amount,
				sum(case when a.transaction_type in (2,3,6) then a.CONS_AMOUNT else 0 end) as iss_amount,
				round(sum(case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end)-sum(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end),2) as bal_qty,
				round(sum(case when a.transaction_type in (1,4,5) then a.CONS_AMOUNT else 0 end)-sum(case when a.transaction_type in (2,3,6) then a.CONS_AMOUNT else 0 end),2) as bal_amount
				from inv_transaction a
				where a.item_category in(7,5,6,23) and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($prodIdArr,0,'prod_id')." group by a.prod_id order by a.prod_id";

				/*$avg_rage_sql=" SELECT a.prod_id,a.cons_quantity, a.store_rate as cons_rate, a.store_amount as cons_amount from inv_transaction a where a.status_active=1 and a.is_deleted=0 and a.item_category in(7,5,6,23) and a.company_id=$data[0] and a.transaction_type in (1,4,5) and a.status_active=1 and a.is_deleted=0 ".where_con_using_array($prodIdArr,0,'prod_id')."";*/
				// echo $avg_rage_sql;// and a.prod_id in (77301)
				$avg_rage_sql_result = sql_select($avg_rage_sql);
				foreach ($avg_rage_sql_result as $row) 
				{
					$avg_rate_arr[$row[csf("prod_id")]]['cons_amount'] += $row[csf("bal_amount")];
					$avg_rate_arr[$row[csf("prod_id")]]['cons_quantity'] += $row[csf("bal_qty")];
				}
				// echo '<pre>';print_r($avg_rate_arr);

				$grand_tot_ratio = 0;
				$grand_tot_recipe_qty = 0;
				$grand_tot_avg_rate = 0;$tot_process_count=0;
	            foreach ($process_array as $process_id)
	            {
	            	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];

	            	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
	            	$remark = $sub_process_remark_array[$process_id]['remark'];
	            	// if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
	            	$tot_ratio=1.5;
	            	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;

	            	?>
	            	<tr bgcolor="#EEEFF0" style="font-size: 22px;">
	            		<td align="center" colspan="12" align="left" height="30" title="<?=$process_id;?>"><b><? echo $dyeing_sub_process[$process_id] .'  (Liquor Ratio: 1:'.number_format($liquor_ratio_process,1,'.','').'), '. 'Water: '.number_format($liquor_ratio,2,'.',''); ?> LTR</b>
	            		</td>
	            	</tr>
	            	<?
	            	$tot_ratio = 0;$tot_recipe_qty = 0;
	            	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';

	            	foreach ($sub_process_data as $data) 
	            	{
	            		$data = explode("**", $data);
	            		$current_stock = $data[13];
	            		$current_stock_check=number_format($current_stock,7,'.','');
	            		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id == 140 || $process_id == 141 || $process_id == 142 || $process_id == 143)
						if (in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];
							$batch_qty = $data[15];

							//echo $dose_base_id.'*'.$ratio.'*'.$batch_weight.'*'.$liquor_ratio_process.'<br>';
							if($dose_base_id==1){
								$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,6, '.', '');
								$amount = explode('.',$amount);
							}
							else{
								$amount = number_format($ratio*$batch_weight/100,6, '.', '');
								$amount = explode('.',$amount);
							}

							/*if ($dose_base_id == 1) {
								$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');
							} else {
								$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');
							}*/
                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
                			<tr>
                				<td height="30"><? echo $i; ?></td>
                				<td height="30" align="center"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
                				<td height="30" align="center" style="font-size: 18px;"><p><? echo $item_description; ?></p></td>
                				<td height="30" align="center"><p><? echo $item_lot; ?></p></td>
                				<td height="30" align="right"><strong><? echo $ratio; ?></strong>&nbsp;</td>                				
                				<td height="30"><p><? 
	                				if ($dose_base[$dose_base_id]=='% on BW') 
	                				{
	                					echo '%';
	                				}
	                				elseif ($dose_base[$dose_base_id]=='GPLL') 
	                				{
	                					echo 'GPL';
	                				}
	                				else
	                				{
	                					echo $dose_base[$dose_base_id];
	                				} ?></p>
	                			</td>
								<td width="40" align="right"><strong><? echo $amount[0]; ?></strong>&nbsp;</td>
								<td width="40" align="right"><strong><? echo number_format(substr($amount[1], 0, 3)); ?></strong>&nbsp;</td>
								<td width="40" align="right"><strong><? echo number_format(substr($amount[1], 3, 6)); ?></strong>&nbsp;</td>
								<td height="30" align="right"><?=number_format($avg_rate,2);?>&nbsp;</td>
	                			<td height="30" align="right" title="<?=$kg_gm_mg_amount;?>"><?=number_format($kg_gm_mg_amount*$avg_rate,2);?>&nbsp;</td>
                				<?
								if(!in_array($sub_process_id,$sub_process_chk))
								{
									$sub_process_chk[]=$sub_process_id;
									?>	
									<td width="150" rowspan="<? echo $tot_process_count ;?>" valign="middle"><strong><? echo $remark; ?></strong></td>
									<?
								}
								?>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
							$grand_tot_avg_rate +=$prod_data_array[$prod_id]['avg_rate_per_unit']*$recipe_qty;
							$grand_tot_kg += $amount[0];
							$grand_tot_gm += substr($amount[1], 0, 3);
							$grand_tot_mgm += substr($amount[1], 3, 6);
							$tot_kg += $amount[0];
							$tot_gm += substr($amount[1], 0, 3);
							$tot_mgm += substr($amount[1], 3, 6);
                			$i++;
						}
						else
						{
	                		//if($current_stock_check>0)
	                		//{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];
								$batch_qty = $data[15];
								$tot_process_count=$process_count[$sub_process_id];

								$cons_amount=$avg_rate_arr[$prod_id]['cons_amount'];
								$cons_quantity=$avg_rate_arr[$prod_id]['cons_quantity'];
								if ($cons_amount>0) {
									$avg_rate=$cons_amount/$cons_quantity;
								}
								else {
									$avg_rate=0;
								}
								// echo $avg_rate.'<br>';

								//echo $dose_base_id.'='.$ratio.'='.$batch_weight.'='.$liquor_ratio_process.'<br>';
								if($dose_base_id==1){
									$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,6, '.', '');
									//echo $amount.'<br>';
									$kg_gm_mg_amount=$amount;
									$amount = explode('.',$amount);
								}
								else{
									$amount = number_format($ratio*$batch_weight/100,6, '.', '');
									//echo $amount.'<br>';
									$kg_gm_mg_amount=$amount;
									$amount = explode('.',$amount);
								}

								/*if ($dose_base_id == 1) {

									$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');
								} else {
									$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');
								}*/
	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr>
	                				<td height="30"><?echo $i; ?></td>
	                				<td height="30" align="center"><p><? echo $item_group_arr[$item_group_id]; ?></p></td>
	                				<td height="30" align="center" title="<?=$prod_id;?>" style="font-size: 18px;"><p><strong><? echo $item_description; ?></strong></p></td>
	                				<td height="30" align="center"><p><? echo $item_lot; ?></p></td>
	                				<td height="30" align="right"><strong><? echo $ratio; ?></strong>&nbsp;</td>
	                				<td height="30" title="<?=$dose_base_id;?>"><p><? 
		                				if ($dose_base[$dose_base_id]=='% on BW') 
		                				{
		                					echo '%';
		                				}
		                				elseif ($dose_base[$dose_base_id]=='GPLL') 
		                				{
		                					echo 'GPL';
		                				}
		                				else
		                				{
		                					echo $dose_base[$dose_base_id];
		                				}
		                				?></p>
		                			</td>
									<td width="40" align="right"><strong><? echo $amount[0]; ?></strong>&nbsp;</td>
	                				<td width="40" align="right"><strong><? echo number_format(substr($amount[1], 0, 3)); ?></strong>&nbsp;</td>
	                				<td width="40" align="right"><strong><? echo number_format(substr($amount[1], 3, 6)); ?></strong>&nbsp;</td>
	                				<td height="30" align="right" title="avg_rate=balance amount/balance qnty. <? echo $cons_amount.'/'.$cons_quantity.'='.$avg_rate; ?>"><?=number_format($avg_rate,2);?>&nbsp;</td>
	                				<td height="30" align="right" title="<?=$kg_gm_mg_amount;?>"><?=number_format($ttl_cost=$kg_gm_mg_amount*$avg_rate,2);
	                				if ($dose_base_id==1) 
	                				{
	                					$chem_cost+=$ttl_cost;
	                				}
	                				else
	                				{
	                					$dyes_cost+=$ttl_cost;
	                				}
	                				?>&nbsp;</td>

	                				<?
									if(!in_array($sub_process_id,$sub_process_chk))
									{
										$sub_process_chk[]=$sub_process_id;
										?>	
										<td width="150" rowspan="<? echo $tot_process_count ;?>" valign="middle"><strong><? echo $remark; ?></strong></td>
										<?
									}
									?>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
								$grand_tot_avg_rate +=$prod_data_array[$prod_id]['avg_rate_per_unit']*$recipe_qty;
								$grand_tot_kg += $amount[0];
								$grand_tot_gm += substr($amount[1], 0, 3);
								$grand_tot_mgm += substr($amount[1], 3, 6);
								$tot_kg += $amount[0];
								$tot_gm += substr($amount[1], 0, 3);
								$tot_mgm += substr($amount[1], 3, 6);
	                			$i++;
	                		//}
	            		}
	            	}

	            }
				if($tot_mgm>=1000){
					$tot_gm += intdiv($tot_mgm, 1000);
					$tot_mgm = $tot_mgm%1000;
				}

				if($tot_gm>=1000){
					$tot_kg += intdiv($tot_gm, 1000);
					$tot_gm = $tot_gm%1000;
				}
	            ?>
				<tr class="tbl_bottom">
					<td align="center" colspan="4" rowspan="2"><strong>Total Cost</strong></td>
	            	<td align="right"  height="20" colspan="2"><strong>Chem Cost</strong></td>
	            	<td align="center" height="20" colspan="3"><strong><? echo number_format($chem_cost, 2, '.', ''); ?></strong>&nbsp;</td>
					<td align="center" colspan="2" rowspan="2"><strong>Total Cost (Chem.+Dyes)</strong></td>
                    <td align="center" rowspan="2"><strong><? echo number_format($chem_cost+$dyes_cost, 2, '.', ''); ?></strong></td>
	            </tr>
				<tr class="tbl_bottom">
	            	<td align="right"  height="20" colspan="2"><strong>Dyes Cost</strong></td>
	            	<td align="center" height="20" colspan="3"><strong><? echo number_format($dyes_cost, 2, '.', ''); ?></strong>&nbsp;</td>
	            </tr>
    		</table>
	        <br>
	        <?
			$path = '../';
			$image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$recipe_id' and form_name='recipe_entry' and is_deleted=0");
			if (count($image_location) > 0) 
			{
				?>
				<div style="width:850px">
					<div style="width:850px;margin-top:10px">
						<img style="padding-left: 190px;" src="<? echo $path . $image_location; ?>"/>
					</div>
				</div>
				<? 
			} 

	        echo signature_table(62, $com_id, "1030px","","50px");
	        ?>
		</div>
	</div>
	<?
}

if ($action == "recipe_entry_print_10") // for NZ Group
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$recipe_id=$data[1];
	$working_company=$data[4];
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$item_group_arr = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0", 'id', 'machine_no');
	$user_name_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");
	$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("select id, brand_name from  lib_brand", 'id', 'brand_name');

	$sql = "SELECT c.id as recipe_id, c.labdip_no, c.company_id,c.working_company_id as w_com_id,c.location_id, recipe_description, c.batch_id, c.machine_id, c.method, c.recipe_date, c.order_source, c.style_or_order, c.booking_id, c.total_liquor, c.batch_ratio, c.liquor_ratio, c.remarks, c.buyer_id, c.color_id, c.color_range, c.remarks as recipe_remarks, c.insert_date, c.inserted_by, c.batch_qty as qnty, c.cycle_time, c.pump, c.surplus_solution, c.sub_tank,c.pickup, a.id, a.batch_no, a.batch_date, a.booking_no_id, a.booking_no, a.booking_without_order, a.sales_order_no, a.batch_type_id, a.color_id, a.batch_against, a.color_range_id, a.organic, a.extention_no, a.total_trims_weight, a.process_id as process_id, a.batch_for, a.batch_weight, a.dyeing_machine, a.remarks as batch_remarks, a.collar_qty, a.cuff_qty, a.shift_id, a.double_dyeing, a.sales_order_id,a.is_sales, a.booking_entry_form , a.save_string
	from pro_recipe_entry_mst c, pro_batch_create_mst a
	where c.batch_id=a.id and c.id=$data[1] and a.is_sales=1 and a.status_active=1 and a.is_deleted=0";
	// echo $sql;die;
	$dataArray = sql_select($sql);
	$w_com_id=$dataArray[0][csf('w_com_id')];
	$company_id=$dataArray[0][csf('company_id')];
	$batch_id=$dataArray[0][csf('batch_id')];
	$location_id=$dataArray[0][csf('location_id')];
	$booking_no=$dataArray[0][csf('style_or_order')];
	$sales_order_id=$dataArray[0][csf('sales_order_id')];
	$color_id=$dataArray[0][csf('color_id')];
	$save_string_data = $dataArray[0][csf('save_string')];
	$sales_order_no = $dataArray[0][csf('sales_order_no')];
	$batch_weight = $dataArray[0][csf('batch_weight')];
	// $batch_weight = $dataArray[0][csf('batch_qnty')];

	if ($dataArray[0][csf('is_sales')] == 1)
	{
		$sales_data = sql_select("SELECT id,job_no,sales_booking_no,within_group,buyer_id,style_ref_no, po_buyer from fabric_sales_order_mst where id=$sales_order_id");

		if ($sales_data[0][csf("within_group")] == 1)
		{
			$booking_data = sql_select("SELECT a.booking_no,a.buyer_id, a.fabric_source, a.booking_type, a.is_short, b.job_no, c.pub_shipment_date, c.grouping, d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d 
			where a.booking_no=b.booking_no and b.po_break_down_id = c.id and c.job_id=d.id and  a.booking_no='$booking_no' and b.fabric_color_id='$color_id' and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 
			group by a.booking_no,a.buyer_id, a.fabric_source, a.booking_type, a.is_short, b.job_no, c.pub_shipment_date, c.grouping, d.style_ref_no");

			foreach ($booking_data as $row)
			{
				$job_number .= $row[csf('job_no')].",";
				$job_style .= $row[csf('style_ref_no')].",";
				$internal_ref .= $row[csf('grouping')].",";
				$book_fabric_source[$row[csf('booking_no')]]=$row[csf('fabric_source')];
				// $booking_type_id[$row[csf('booking_no')]]=$row[csf('booking_type')];

				if($row[csf("booking_type")]==1 && $row[csf("is_short")]==2)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Main";
	            }
	            else if($row[csf("booking_type")]==1 && $row[csf("is_short")]==1)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Short";
	            }
	            else if($row[csf("booking_type")]==4)
	            {
	                $booking_type_arr[$row[csf("booking_no")]]="Sample";
	            }

	            if($min_shipment_date == ""){
					$min_shipment_date = $row[csf('pub_shipment_date')];
				}

				if($max_shipment_date == ""){
					$max_shipment_date = $row[csf('pub_shipment_date')];
				}

				if(strtotime($row[csf('pub_shipment_date')]) > strtotime($min_shipment_date))
				{
					$min_shipment_date = $min_shipment_date;
				}else{
					$min_shipment_date = $row[csf('pub_shipment_date')];
				}


				if(strtotime($row[csf('pub_shipment_date')]) > strtotime($max_shipment_date))
				{
					$max_shipment_date = $row[csf('pub_shipment_date')];
				}
				else
				{
					$max_shipment_date = $max_shipment_date;
				}
			}
			$job_number = chop($job_number,",");
			$job_style = chop($job_style,",");
			$internal_ref = chop($internal_ref,",");
			$buyer_id = $booking_data[0][csf("buyer_id")];
			$po_number = $sales_data[0][csf("job_no")];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("po_buyer")];
			$style_ref_no = $sales_data[0][csf("style_ref_no")];
		}
		else
		{
			$po_number = $sales_data[0][csf("job_no")];
			$job_number = "";
			$buyer_id = $sales_data[0][csf("buyer_id")];
			$job_style = $sales_data[0][csf("style_ref_no")];
			$ship_date = "";
			$sales_buyer_id = $sales_data[0][csf("buyer_id")];
		}
	}
	$job_no = implode(",", array_unique(explode(",", $job_number)));
	$jobstyle = implode(",", array_unique(explode(",", $job_style)));
	$buyer = implode(",", array_unique(explode(",", $buyer_id)));
	$internal_ref = implode(",", array_unique(array_filter(explode(",", $internal_ref))));
	$file_nos = implode(",", array_unique(explode(",", $file_nos)));

	$sql_recipe_dtl = "SELECT mst_id, liquor_ratio,total_liquor from pro_recipe_entry_dtls where mst_id='$data[1]' and status_active=1 and liquor_ratio>0  and  RATIO>0";
	//echo $sql_recipe_dtl;
	$ratio_dataArray = sql_select($sql_recipe_dtl);
	$liquor_ratio=$ratio_dataArray[0][csf('liquor_ratio')];
	//$total_liquor=$ratio_dataArray[0][csf('total_liquor')];
	$tot_liquor=array();
	foreach ($ratio_dataArray as $row) 
	{
		$tot_liquor[$row[csf('total_liquor')]]=$row[csf('total_liquor')];
	}
	$total_liquor = max($tot_liquor);

	/*$total_liquor = 0;
	foreach ($tot_liquor as $key=>$val) {
	    if ($val > $total_liquor) {
	        $total_liquor = $val;
	    }
	}
	echo $total_liquor;*/

	$path = '../';
	?>
	<div style="width:930px; font-size:9px">
		<table width="930" cellspacing="0" align="center" border="0" style="font-size: 20px; display: inline; ">
			<tr width="930">
				<td width="20%" rowspan="2"  colspan="2" align="center" style="font-size:50px"><img align="left" src='<? echo $path.$imge_arr[$working_company]; ?>' height='60'  /></td>
				
				<td width="45%" colspan="8" align="center" style="font-size:30px;padding-left: 80px; ">
					<strong><? echo $company_library[$w_com_id]; ?></strong>
					<p style="margin-top: 0px; font-size:18px;"><i>
						<?
						$nameArray=sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$working_company");
						foreach ($nameArray as $result)
						{
						    ?>
						    <? echo $result[csf('plot_no')]; ?>
						    <? echo $result[csf('level_no')]?>
						    <? echo $result[csf('road_no')]; ?>
						    <? echo $result[csf('block_no')];?>
						    <? echo $result[csf('city')];?>
						    <? echo $result[csf('zip_code')]; ?>
						    <?php echo $result[csf('province')]; ?>
						    <? echo $country_arr[$result[csf('country_id')]];
						}
						?>
					</i></p>
				</td>
				<td width="10%"  colspan="1" align="center" style="font-size:30px"></td>
				<td width="25%" colspan="2" style="padding-left: 1px;font-size:1rem; float: center" align="right"></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="font-size:18px; padding-left: 140px;""><strong><u>Dyes & Chemical Requisition</u></strong></td>
				<td colspan="8" align="center" style="font-size:18px; padding-left: 100px;"></td>
			</tr>
			<tr>
				<td colspan="9"></td>
			</tr>
		</table>
		<br><br>

		<table width="930" cellspacing="0" align="center"  border="1" rules="all" class="rpt_table" style="font-size: 17px;">
			<tr>
				<td width="160"><strong>Shade Type</strong></td>
				<td width="220px">:&nbsp;<? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
				<td width="150"><strong>Serial No.</strong></td>
				<td width="200px">:&nbsp;<? echo $dataArray[0][csf('recipe_id')]; ?></td>
				<td width="150"><strong>LD</strong></td>
				<td width="200px">:&nbsp;<? echo $dataArray[0][csf('labdip_no')]; ?></td>
			</tr>	
			<tr>
				<td><strong>Recipe Date</strong></td>
				<td>:&nbsp;<? echo change_date_format($dataArray[0][csf('recipe_date')]); ?></td>
				<td><strong>Buyer</strong></td>
				<td>:&nbsp;<?
					if($dataArray[0][csf('is_sales')] ==1)
					{

						echo $buyer_library[$sales_buyer_id];
					}
					else if ($dataArray[0][csf('batch_against')] == 3)
					{
						echo $buyer_library[$buyer_id_booking];
					}
					else
					{
						echo $buyer_library[$buyer];
					}
					?>
				</td>
				<td><strong>MC NO</strong></td>
				<td>:&nbsp;<? echo $machine_arr[$dataArray[0][csf('machine_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Order</strong></td>
				<td>:&nbsp;<? echo $style_ref_no; ?></td>
				<td><strong>Batch No</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('batch_no')]; ?></td>
				<td><strong>Color</strong></td>
				<td>:&nbsp;<? echo $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Reel SP</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('surplus_solution')]; ?></td>				
				<td><strong>P/Press</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('pump')]; ?></td>
				<td><strong>Cycle Time</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('cycle_time')]; ?></td>
			</tr>			
			<tr>
				<td><strong>Total QTY (Kg)</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]; ?></td>
				<td><strong>Fabric Weight</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('batch_weight')]-$dataArray[0][csf('total_trims_weight')]; ?></td>
				<td><strong>Trims Weight</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('total_trims_weight')]; ?></td>
			</tr>			
			<tr>
				<td><strong>Water</strong></td>
				<td>:&nbsp;<? echo number_format($total_liquor,2,'.',''); ?> LT</td>
				<td><strong>Sub Tank</strong></td>
				<td>:&nbsp;<? echo $dataArray[0][csf('sub_tank')]; ?> LT</td>
			</tr>
		</table>
        <br>
        <?
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$j = 1;
		$entryForm = $entry_form_arr[$batch_id_qry[0]];
		?>

		<!-- Fabrication -->
        <table width="930" cellspacing="0" align="right" class="rpt_table" border="1" style="font-size:16px; margin-bottom:5px;" rules="all">
			<thead bgcolor="#dddddd" align="center">
				<tr bgcolor="#CCCCFF">
					<th colspan="6" align="center"><strong>Fabrication</strong></th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="80">Dia/ W. Type</th>
					<th width="200">Constrution & Composition</th>
					<th width="70">Gsm</th>
					<th width="70">Dia</th>
                    <th width="70">Yarn Lot</th>
				</tr>
			</thead>
			<tbody>
				<?
				 $sql_dtls_knit = "SELECT a.booking_no_id,e.receive_basis,e.booking_id,a.booking_without_order,a.is_sales, b.batch_qnty AS batch_qnty, b.roll_no as roll_no, b.item_description, b.program_no, b.prod_id, b.body_part_id, b.width_dia_type, b.width_dia_type as num_of_rows, d.machine_no_id,d.machine_dia,d.machine_gg, d.yarn_lot,d.id as dtls_id,d.yarn_count,d.brand_id,c.barcode_no, d.stitch_length as stitch_length,  e.knitting_source, e.knitting_company,c.qc_pass_qnty_pcs ,c.coller_cuff_size
				from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
				where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id='$data[0]' and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
				order by b.program_no";
				$sql_result_knit = sql_select($sql_dtls_knit);
				foreach ($sql_result_knit as $row)
				{
					$knittin_data_arr2[$row[csf('item_description')]]["yarn_lot"]= $row[csf('yarn_lot')];
				}

				foreach ($batch_id_qry as $b_id)
				{
					$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
					$result_batch_query = sql_select($batch_query);
					foreach ($result_batch_query as $rows)
					{
						if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$yarn_lot=$knittin_data_arr2[$rows[csf('item_description')]]["yarn_lot"];

						$fabrication_full = $rows[csf("item_description")];
						$fabrication = explode(',', $fabrication_full);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $j; ?></td>
							<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
							<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
							<td align="center"><? echo $fabrication[2]; ?></td>
							<td align="center"><? echo $fabrication[3]; ?></td>
                            <td align="center"><? echo $yarn_lot; ?></td>
						</tr>
						<?
						$j++;
					}
				}
				?>
			</tbody>
		</table>
        <br> <br> <br>
        <!-- Fabrication End -->

        <div style="width:100%;">
        	<table  cellspacing="0" width="930" border="1" rules="all" class="rpt_table" style="font-size:20px;">
        		<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" height="40" >SL</th>
						<th width="200" height="40" title="Item Description">Chemical Name</th>
						<th width="80" height="40" title="Ratio">g/l</th>
						<th width="50" height="40" title="Ratio">%</th>
						<th width="50" height="40">+</th>
						<th width="50">Total qty (gm)</th>
						<th width="" height="40">Comments</th>
					</tr>
        		</thead>
        		<?
        		$i = 1;
        		$j = 1;
        		$mst_id = $data[1];
        		$com_id = $data[0];

        		$process_array = array();
        		$sub_process_data_array = array();
        		$sub_process_remark_array = array();
        		$sql_ratio = "SELECT id, sub_process_id as sub_process_id,process_remark,total_liquor,liquor_ratio from pro_recipe_entry_dtls where mst_id=$mst_id and ratio is not null and status_active=1 and is_deleted=0 order by sub_seq";
        		// echo $sql_ratio;
        		$nameArray = sql_select($sql_ratio);
        		foreach ($nameArray as $row) 
        		{
        			if (!in_array($row[csf("sub_process_id")], $process_array)) 
        			{
        				$process_array[] = $row[csf("sub_process_id")];
        				$sub_process_remark_array[$row[csf("sub_process_id")]]['remark'] = $row[csf("process_remark")];
        			}
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
        			$sub_process_remark_array[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        		}

        		if ($db_type == 2)
        		{
					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0  )
					union
					(
					SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  order by seq_no,sub_process_id";
        		}
        		else if ($db_type == 0)
        		{
					$sql = "(SELECT a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio, b.seq_no as seq_no, c.batch_qty from product_details_master a, pro_recipe_entry_dtls b,pro_recipe_entry_mst c where c.id=b.mst_id and a.id=b.prod_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and c.company_id='$com_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.ratio>0 )
					union
					(
						SELECT null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.id as dtls_id, b.prod_id, b.sub_process_id,b.process_remark,b.comments, b.item_lot, b.dose_base, b.ratio,b.total_liquor,b.liquor_ratio,b.seq_no as seq_no, null as batch_qty from  pro_recipe_entry_dtls b where b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0  and b.sub_process_id in($subprocessForWashIn)
					)  ";
        		}
				//echo $sql;
        		$sql_result = sql_select($sql);
				$prodIdChk = array();
				$prodIdArr = array();
				$process_count = array();
	            foreach ($sql_result as $row)
	            {
	            	$sub_process_data_array[$row[csf("sub_process_id")]] .= $row[csf("id")] . "**" . $row[csf("item_category_id")] . "**" . $row[csf("item_group_id")] . "**" . $row[csf("sub_group_name")] . "**" . $row[csf("item_description")] . "**" . $row[csf("item_size")] . "**" . $row[csf("unit_of_measure")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("sub_process_id")] . "**" . $row[csf("item_lot")] . "**" . $row[csf("dose_base")] . "**" . $row[csf("ratio")] . "**" . $row[csf("comments")]. "**" . $row[csf("current_stock")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("batch_qty")] . "$$$";

					if($prodIdChk[$row[csf('prod_id')]] == "")
					{
						$prodIdChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
						array_push($prodIdArr,$row[csf('prod_id')]);
					}
					$process_count[$row[csf("sub_process_id")]]++;

					/*$key_1 = $row[csf("batch_id")] . $row[csf("order_id")] . $row[csf("gsm")] . $row[csf("fabric_description_id")];
					$gData_1[$key_1] += 1;*/
	            }

				$grand_tot_ratio = 0;
				$grand_tot_recipe_qty = 0;
				$grand_tot_avg_rate = 0;$tot_process_count=0;$gpll_ratio=0;$bw_ratio=0;
	            foreach ($process_array as $process_id)
	            {
	            	$liquor_ratio = $sub_process_remark_array[$process_id]['total_liquor'];

	            	$liquor_ratio_process = $sub_process_remark_array[$process_id]['liquor_ratio'];
	            	$remark = $sub_process_remark_array[$process_id]['remark'];
	            	// if ($remark == '') $remark = ''; else $remark .= $remark . ', ';
	            	$tot_ratio=1.5;
	            	$leveling_water=$batch_weight*($liquor_ratio_process-$tot_ratio) ;

	            	?>
	            	<tr bgcolor="#EEEFF0" style="font-size: 22px;">
	            		<td align="center" colspan="9" align="left" height="30" title="<?=$process_id;?>"><b><? echo $dyeing_sub_process[$process_id] .'  (Liquor Ratio:'.number_format($liquor_ratio_process,1,'.','').'), '. 'Water: '.number_format($liquor_ratio,2,'.',''); ?> LTR</b>
	            		</td>
	            	</tr>
	            	<?
	            	$tot_ratio = 0;$tot_recipe_qty = 0;
	            	//$sub_process_data = array_filter(explode("@@@",$sub_process_data_array2[$process_id]));
					//$sub_process_data = explode("$$$", substr($sub_process_data_array[$process_id], 0, -1));
					$sub_process_dataArr=rtrim($sub_process_data_array[$process_id],'$$$');
					$sub_process_data = explode("$$$", $sub_process_dataArr);
					//echo count($sub_process_data).'='.$process_id.'<br>';

	            	foreach ($sub_process_data as $data) 
	            	{
	            		$data = explode("**", $data);
	            		$current_stock = $data[13];
	            		$current_stock_check=number_format($current_stock,7,'.','');
	            		//if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id == 140 || $process_id == 141 || $process_id == 142 || $process_id == 143)
						if (in_array($process_id, $subprocessForWashArr))
						{
							$id = $data[0];
                			$item_category_id = $data[1];
                			$item_group_id = $data[2];
                			$sub_group_name = $data[3];
                			$item_description = $data[4];
                			$item_size = $data[5];
                			$unit_of_measure = $data[6];
                			$dtls_id = $data[7];
                			$sub_process_id = $data[8];
                			$item_lot = $data[9];
                			$dose_base_id = $data[10];
                			$ratio = $data[11];
                			$comments = $data[12];
                			$prod_id = $data[14];
							$batch_qty = $data[15];

							//echo $dose_base_id.'*'.$ratio.'*'.$batch_weight.'*'.$liquor_ratio_process.'<br>';
							if($dose_base_id==1){
								$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,3, '.', '');
								$amount = explode('.',$amount);
							}
							else{
								$amount = number_format($ratio*$batch_weight/100,3, '.', '');
								$amount = explode('.',$amount);
							}

							if ($dose_base_id == 1) {
								$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');
							} else {
								$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');
							}
							
                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                			?>
                			<tr>
                				<td height="30"><? echo $i; ?></td>
                				<td height="30" align="center" style="font-size: 18px;"><p><? echo $item_description; ?></p></td>
                				<td height="30" align="right" title="<?=$dose_base_id;?>"><p><? echo ($dose_base_id == 1) ? number_format($ratio, 3, '.', '') : '0.000' ?></p>
		                			</td>
		                		<td height="30" align="right"><strong><? echo ($dose_base_id == 2) ? number_format($ratio, 3, '.', '') : '0.000' ?></strong>&nbsp;</td>
		                		<td height="30"></td>
								<td width="50" align="right"><strong><? echo number_format($recipe_qty, 3, '.', ''); //$amount; ?></strong>&nbsp;</td>
                				<?
								if(!in_array($sub_process_id,$sub_process_chk))
								{
									$sub_process_chk[]=$sub_process_id;
									?>	
									<td width="150" rowspan="<? echo $tot_process_count ;?>" valign="middle"><strong><? echo $remark; ?></strong></td>
									<?
								}
								?>
                			</tr>
                			<?
                			$tot_ratio += $ratio;
                			$grand_tot_ratio += $ratio;
							$grand_tot_kg += $amount;
                			$i++;
						}
						else
						{
	                		//if($current_stock_check>0)
	                		//{
	                			$id = $data[0];
	                			$item_category_id = $data[1];
	                			$item_group_id = $data[2];
	                			$sub_group_name = $data[3];
	                			$item_description = $data[4];
	                			$item_size = $data[5];
	                			$unit_of_measure = $data[6];
	                			$dtls_id = $data[7];
	                			$sub_process_id = $data[8];
	                			$item_lot = $data[9];
	                			$dose_base_id = $data[10];
	                			$ratio = $data[11];
	                			$comments = $data[12];
	                			$prod_id = $data[14];
								$batch_qty = $data[15];
								$tot_process_count=$process_count[$sub_process_id];

								$cons_amount=$avg_rate_arr[$prod_id]['cons_amount'];
								$cons_quantity=$avg_rate_arr[$prod_id]['cons_quantity'];
								if ($cons_amount>0) {
									$avg_rate=$cons_amount/$cons_quantity;
								}
								else {
									$avg_rate=0;
								}
								// echo $avg_rate.'<br>';

								//echo $dose_base_id.'='.$ratio.'='.$batch_weight.'='.$liquor_ratio_process.'<br>';
								if($dose_base_id==1)
								{
									$amount = number_format($ratio*$batch_weight*$liquor_ratio_process/1000,3, '.', '');
									// $amount = explode('.',$amount);
								}
								else{
									$amount = number_format($ratio*$batch_weight/100,3, '.', '');
									// $amount = explode('.',$amount);
								}

								if ($dose_base_id == 1) {
									$recipe_qty = number_format(($liquor_ratio/1000)*$ratio, 4, '.', '');
								} else {
									$recipe_qty = number_format(($batch_qty*$ratio)/100, 4, '.', '');
								}

	                			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	                			?>
	                			<tr>
	                				<td height="30"><?echo $i; ?></td>
	                				<td height="30" align="center" title="<?=$prod_id;?>" style="font-size: 18px;"><p><strong><? echo $item_description; ?></strong></p></td>
	                				<td height="30" title="<?=$dose_base_id;?>"><strong><?
	                				echo ($dose_base_id == 1) ? number_format($ratio, 3, '.', '') : '0.000' ?></strong></td>
		                			<td height="30" align="right" title="<?=$dose_base_id;?>" align="right"><strong><? 
									echo ($dose_base_id == 2) ? number_format($ratio, 3, '.', '') : '0.000'; ?></strong>&nbsp;</td>
		                			<td height="30"></td>
									<td width="50" align="right"><strong><? echo number_format($recipe_qty, 3, '.', ''); //$amount; ?></strong>&nbsp;</td>
	                				<?
									if(!in_array($sub_process_id,$sub_process_chk))
									{
										$sub_process_chk[]=$sub_process_id;
										?>	
										<td width="150" rowspan="<? echo $tot_process_count ;?>" valign="middle"><strong><? echo $remark; ?></strong></td>
										<?
									}
									?>
	                			</tr>
	                			<?
	                			$tot_ratio += $ratio;
	                			$grand_tot_ratio += $ratio;
								$grand_tot_kg += $amount;
	                			$i++;
	                		//}
	            		}
	            	}

	            }
				if($tot_mgm>=1000){
					$tot_gm += intdiv($tot_mgm, 1000);
					$tot_mgm = $tot_mgm%1000;
				}

				if($tot_gm>=1000){
					$tot_kg += intdiv($tot_gm, 1000);
					$tot_gm = $tot_gm%1000;
				}
	            ?>
				<tr class="tbl_bottom">
	            	<td></td>
	            	<td align="right" height="20"><strong>ALL OVER</strong></td>
	            	<td align="right" height="20"><strong><? echo number_format($dataArray[0][csf('pickup')], 2, '.', ''); ?>%</strong>&nbsp;</td>
	            	<td></td>
	            	<td></td>
	            	<td></td>
	            	<td></td>
	            </tr>
				<tr class="tbl_bottom">
	            	<td></td>
	            	<td align="right" height="20"><strong>SALT=</strong></td>
	            	<td align="right" height="20"><strong><? echo number_format($salt=$dataArray[0][csf('batch_weight')]*0.4, 2, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right" height="20"><strong><?=number_format($salt/$dataArray[0][csf('sub_tank')]*100, 2, '.', '');?>%</strong></td>
	            	<td align="right" height="20"><strong>Levelling Water</strong>&nbsp;</td>
	            	<td align="right" height="20"><strong><?
	            	$levelling_water=$total_liquor-($dataArray[0][csf('batch_weight')]*0.4+$dataArray[0][csf('batch_weight')]*0.4+$dataArray[0][csf('batch_weight')]*0.4+$dataArray[0][csf('batch_weight')]*0.4);
	            	echo number_format($levelling_water, 2, '.', '');?></strong></td>
	            	<td><strong>LT</strong></td>
	            </tr>
	            <tr class="tbl_bottom">
	            	<td></td>
	            	<td align="right"  height="20"><strong>COLOUR=</strong></td>
	            	<td align="right" height="20"><strong><? echo number_format($colour=$dataArray[0][csf('batch_weight')]*0.4, 2, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right" height="20"><strong><?=number_format($colour/$dataArray[0][csf('sub_tank')]*100, 2, '.', '');?>%</strong></td>
	            	<td></td>
	            	<td></td>
	            	<td></td>
	            </tr>
	            <tr class="tbl_bottom">
	            	<td></td>
	            	<td align="right"  height="20"><strong>SODA ASH=</strong></td>
	            	<td align="right" height="20"><strong><? echo number_format($soda_ash=$dataArray[0][csf('batch_weight')]*0.4, 2, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right" height="20"><strong><?=number_format($soda_ash/$dataArray[0][csf('sub_tank')]*100, 2, '.', '');?>%</strong></td>
	            	<td></td>
	            	<td></td>
	            	<td></td>
	            </tr>
	            <tr class="tbl_bottom">
	            	<td></td>
	            	<td align="right"  height="20"><strong>SODA ASH=</strong></td>
	            	<td align="right" height="20"><strong><? echo number_format($soda_ash2=$dataArray[0][csf('batch_weight')]*0.4, 2, '.', ''); ?></strong>&nbsp;</td>
	            	<td align="right" height="20"><strong><?=number_format($soda_ash2/$dataArray[0][csf('sub_tank')]*100, 2, '.', '');?>%</strong></td>
	            	<td></td>
	            	<td></td>
	            	<td></td>
	            </tr>
    		</table>
	        <br>
	        <?
	        echo signature_table(62, $com_id, "1030px","","50px");
	        ?>
		</div>
	</div>
	<?
}
?>