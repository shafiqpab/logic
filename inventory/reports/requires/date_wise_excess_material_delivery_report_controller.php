<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if($action == "composition_popup")
{
	echo load_html_head_contents("Composition Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	var selected_id = new Array(); var selected_name = new Array();

	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

		tbl_row_count = tbl_row_count-1;
		for( var i = 1; i <= tbl_row_count; i++ ) {
			js_set_value( i );
		}
	}

	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function set_all()
	{
		var old=document.getElementById('txt_pre_composition_row_id').value;
		if(old!="")
		{
			old=old.split(",");
			for(var k=0; k<old.length; k++)
			{
				js_set_value( old[k] )
			}
		}
	}

	function js_set_value( str )
	{

		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

		if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_individual_id' + str).val() );
			selected_name.push( $('#txt_individual' + str).val() );

		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i, 1 );
		}

		var id = ''; var name = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			name += selected_name[i] + ',';
		}

		id = id.substr( 0, id.length - 1 );
		name = name.substr( 0, name.length - 1 );

		$('#hidden_composition_id').val(id);
		$('#hidden_composition').val(name);
	}
	</script>
	</head>
	<fieldset style="width:390px">
		<legend>Yarn Receive Details</legend>
		<input type="hidden" name="hidden_composition" id="hidden_composition" value="">
		<input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="2">
						<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
					</th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="">Composition Name</th>
				</tr>
			</thead>
		</table>
		<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?
		$i = 1;

        
		$result=sql_select("select id,composition_name from  lib_composition_array where status_active in(1,2) and is_deleted=0 order by composition_name");
		$pre_composition_id_arr=explode(",",$pre_composition_id);
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";


			if(in_array($row[csf("id")],$pre_composition_id_arr))
			{
				if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
				<td width="50">
					<? echo $i; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
				</td>
				<td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		<input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
		</table>
		</div>
		<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
		set_all();
	</script>
	<?
}

if ($action == "report_generate") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
    
    $cbo_appoval_status=str_replace("'","",$cbo_appoval_status);
    $cbo_qc_status=str_replace("'","",$cbo_qc_status);
    $search_type=str_replace("'","",$search_type);
    $cbo_category=str_replace("'","",$cbo_category);
    $txt_job_id=str_replace("'","",$txt_job_id);
    $buyer_id=str_replace("'","",$cbo_buyer_id);

    // var_dump($cbo_qc_status);
   
    
    $color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
    $count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
    $cutting_unit_arr=return_library_array( "select id,floor_name from lib_prod_floor where production_process=1 and status_active=1 and is_deleted=0", "id", "floor_name"  );
    $trim_group_arr=return_library_array( "select id,item_name from lib_item_group where status_active=1 and is_deleted=0", "id", "item_name"  );


    if ($type==1) // Show Button
    { 

		$con = connect();	
        $r_id1=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1989,1990,1991)");
        if($r_id1)
        {
            oci_commit($con);
        }

		
		if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "")
		{
			$issue_date_cond = " and a.issue_date between " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
		}
		else
		{
			$issue_date_cond = "";
		}


		if($txt_job_id !='' || $buyer_id>0)
		{
			if($txt_job_id !='')
			{
				$jobIdCond = " and a.id in($txt_job_id)";
			}

			if($buyer_id>0)
			{
				$buyerCond = " and b.buyer_name in($buyer_id)";
			}
			

			$sql_stl_job_po="SELECT a.id, a.po_number,a.file_no,a.grouping,a.job_no_mst, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and b.company_name=$cbo_company_name $jobIdCond $buyerCond";

			//echo $sql_stl_job_po;die;

			$result_sql_stl_job_po=sql_select($sql_stl_job_po);
			$jobIdChk=array();
			$all_job_id_arr=array();
			foreach($result_sql_stl_job_po as $row)
			{
				if($jobIdChk[$row[csf('id')]] == "")
				{
					$jobIdChk[$row[csf('id')]] = $row[csf('id')];
					$all_job_id_arr[$row[csf('id')]] = $row[csf('id')];
				}
			}

			if(!empty($all_job_id_arr))
			{	
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1989, 1,$all_job_id_arr, $empty_arr);
				//die;
			}

			$tablefName = ", GBL_TEMP_ENGINE e";
			$tablewName = ", GBL_TEMP_ENGINE e";
			$tabletName = ", GBL_TEMP_ENGINE d";

			$tablefWhereCon = " and b.order_id = CAST(e.ref_val AS VARCHAR2(4000)) and e.user_id=$user_id and e.entry_form=1989";
			$tablewWhereCon = " and b.order_id = CAST(e.ref_val AS VARCHAR2(4000)) and e.user_id=$user_id and e.entry_form=1989";
			$tabletWhereCon = " and b.order_id = CAST(d.ref_val AS VARCHAR2(4000)) and d.user_id=$user_id and d.entry_form=1989";
		}


		/** For Knit Finish Fabric Issue */
		if($cbo_category==0 || $cbo_category==2)
		{
			$sql_f_main="SELECT a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.issue_date,a.buyer_id, b.issue_qnty,  b.prod_id, b.cutting_unit,b.uom, b.order_id, c.batch_no, d.cons_rate  
			from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c,inv_transaction d $tablefName 
			where a.id=b.mst_id and b.batch_id=c.id  and a.id=d.mst_id and b.trans_id=d.id and a.item_category=2 and a.company_id=$cbo_company_name $issue_date_cond and a.extra_status=1 and a.entry_form=18 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $tablefWhereCon ";
	
			//echo  $sql_f_main;die;
			$f_result = sql_select($sql_f_main);
			$f_data_array = array();
			$f_orderIdChk = array();
			$f_prodIdChk = array();
			$all_order_id_arr = array();
			$all_prod_id_arr = array();
			foreach($f_result as $row)
			{
				$f_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['issue_number_prefix_num']=$row[csf("issue_number_prefix_num")];
				$f_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['issue_number']=$row[csf("issue_number")];
				$f_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['issue_date']=$row[csf("issue_date")];
				$f_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['buyer_id']=$row[csf("buyer_id")];
				$f_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['uom']=$row[csf("uom")];
				$f_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['issue_qnty'] +=$row[csf("issue_qnty")];
				$f_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['order_id'] =$row[csf("order_id")];
				$f_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['cons_rate'] =$row[csf("cons_rate")];
	
				if($f_orderIdChk[$row[csf('order_id')]] == "")
				{
					$f_orderIdChk[$row[csf('order_id')]] = $row[csf('order_id')];
					$all_order_id_arr[$row[csf('order_id')]] = $row[csf('order_id')];
				}
				if($f_prodIdChk[$row[csf('prod_id')]] == "")
				{
					$f_prodIdChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
					$all_prod_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
				}
	
	
			}
			unset($f_result);
	
			//echo "<pre>"; print_r($f_data_array);echo "</pre>";
	
		}

		/** For Woven Finish Fabric Issue */
       
		if($cbo_category==0 || $cbo_category==3)
		{
			$sql_w_main="SELECT a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.issue_date, a.buyer_id, b.prod_id, b.cutting_unit, b.order_id, b.issue_qnty, c.batch_no, d.cons_rate 
			from inv_issue_master a,inv_wvn_finish_fab_iss_dtls b, pro_batch_create_mst c,inv_transaction d $tablewName where a.id=b.mst_id and b.batch_id=c.id  and a.id=d.mst_id and b.trans_id=d.id and a.item_category=3 and a.company_id=$cbo_company_name $issue_date_cond and a.extra_status=1 and a.entry_form=19 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $tablewWhereCon"; 
			
	
			//echo  $sql_w_main;
			$w_result = sql_select($sql_w_main);
			$w_data_array = array();
			$w_orderIdChk = array();
			$w_prodIdChk = array();
			foreach($w_result as $row)
			{
				$w_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['issue_number_prefix_num']=$row[csf("issue_number_prefix_num")];
				$w_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['issue_number']=$row[csf("issue_number")];
				$w_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['issue_date']=$row[csf("issue_date")];
				$w_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['buyer_id']=$row[csf("buyer_id")];
				$w_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['issue_qnty'] +=$row[csf("issue_qnty")];
				$w_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['order_id'] =$row[csf("order_id")];
				$w_data_array[$row[csf("id")]][$row[csf("batch_no")]][$row[csf("prod_id")]][$row[csf("cutting_unit")]]['cons_rate'] =$row[csf("cons_rate")];
	
				if($w_orderIdChk[$row[csf('order_id')]] == "")
				{
					$w_orderIdChk[$row[csf('order_id')]] = $row[csf('order_id')];
					$all_order_id_arr[$row[csf('order_id')]] = $row[csf('order_id')];
				}
				if($w_prodIdChk[$row[csf('prod_id')]] == "")
				{
					$w_prodIdChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
					$all_prod_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
				}
	
	
			}
			unset($w_result);
		}

		/** For Trims Issue */
       
		if($cbo_category==0 || $cbo_category==1)
		{
			$sql_trims = "SELECT a.id, a.issue_number_prefix_num, a.issue_number, a.issue_date, a.floor_id, b.floor_id as swing_floor, b.sewing_line, b.prod_id, b.trans_id, b.order_id, b.item_group_id, b.item_color_id, b.item_size, b.uom, b.issue_qnty, b.rate, c.cons_rate 
			from inv_issue_master a, inv_trims_issue_dtls b, inv_transaction c $tabletName 
			where a.id=b.mst_id and a.id=c.mst_id and b.trans_id=c.id and a.company_id=$cbo_company_name $issue_date_cond and a.extra_status=1 and a.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_multi=1 $tabletWhereCon  
			 "; 
			//echo $sql_trims;
			$trims_result = sql_select($sql_trims);
			$t_orderIdChk = array();
			$t_prodIdChk = array();
			$itemGroupIdChk = array();
			$all_item_group_id_arr = array();
			foreach($trims_result as $row)
			{
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['issue_number_prefix_num']=$row[csf("issue_number_prefix_num")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['issue_number']=$row[csf("issue_number")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['issue_date']=$row[csf("issue_date")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['order_id']=$row[csf("order_id")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['issue_qnty'] +=$row[csf("issue_qnty")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['floor_id'] =$row[csf("floor_id")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['swing_floor'] =$row[csf("swing_floor")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['sewing_line'] =$row[csf("sewing_line")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['item_group_id'] =$row[csf("item_group_id")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['item_color_id'] =$row[csf("item_color_id")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['item_size'] =$row[csf("item_size")];
				$t_data_array[$row[csf("id")]][$row[csf("trans_id")]][$row[csf("prod_id")]]['cons_rate'] =$row[csf("cons_rate")];
	
				if($t_orderIdChk[$row[csf('order_id')]] == "")
				{
					$t_orderIdChk[$row[csf('order_id')]] = $row[csf('order_id')];
					$all_order_id_arr[$row[csf('order_id')]] = $row[csf('order_id')];
				}
				if($t_prodIdChk[$row[csf('prod_id')]] == "")
				{
					$t_prodIdChk[$row[csf('prod_id')]] = $row[csf('prod_id')];
					$all_prod_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
				}
			}
			unset($trims_result);
		}

		$all_order_id_arr = array_filter(array_unique($all_order_id_arr));
		if(!empty($all_order_id_arr))
        {	
            fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1990, 1,$all_order_id_arr, $empty_arr);

			$sql_job="SELECT a.id, a.po_number,a.file_no,a.grouping,a.job_no_mst, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b, GBL_TEMP_ENGINE c where a.job_id=b.id and a.id=c.ref_val and c.user_id=$user_id and c.entry_form=1990";

			//echo $sql_job;die;

			$result_sql_job=sql_select($sql_job);
			$po_array=array();
			foreach($result_sql_job as $row)
			{
				$po_array[$row[csf("id")]]['po']=$row[csf("po_number")];
				$po_array[$row[csf("id")]]['file']=$row[csf("file_no")];
				$po_array[$row[csf("id")]]['ref']=$row[csf("grouping")];
				$po_array[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
				$po_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
			}
		}

		$all_prod_id_arr = array_filter(array_unique($all_prod_id_arr));
		if(!empty($all_prod_id_arr))
        {	
            fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1991, 1,$all_prod_id_arr, $empty_arr);

			$sql_product="select a.id,a.product_name_details, a.color, a.unit_of_measure from product_details_master a, GBL_TEMP_ENGINE b where a.id=b.ref_val and b.user_id=$user_id and b.entry_form=1991";

			//echo $sql_product;die;

			$result_sql_product=sql_select($sql_product);
			$product_array=array();
			foreach($result_sql_product as $row)
			{
				$product_array[$row[csf("id")]]['color']=$row[csf("color")];
				$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
				$product_array[$row[csf("id")]]['unit_of_measure']=$row[csf("unit_of_measure")];
			}
		}

		if ($db_type == 0)
		{
			$exchange_rate = return_field_value("conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
		}
		else if ($db_type == 2)
		{
			$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");
		}
		else
		{
			$exchange_rate = 1;
		}


		$con = connect();	
        $r_id111=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1989,1990,1991)");
        if($r_id111)
        {
            oci_commit($con);
        }

        $value_width = 1325;
        $span = 13;
       
        ob_start();
        
        ?>
        <style>
            .wrd_brk{word-break: break-all;word-wrap: break-word;}          
        </style>
        
        <fieldset style="width:<? echo $value_width + 20; ?>px;">
            <table cellpadding="0" cellspacing="0" width="<? echo $value_width; ?>" style="float: left;">
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="<? echo $span; ?>" style="font-size:16px"><strong>From Date: <? echo change_date_format(str_replace("'", "", $txt_date_from)); ?> To Date: <? echo change_date_format(str_replace("'", "", $txt_date_to)); ?></strong></td>
                </tr>
            </table>

			<?
				if($cbo_category==0 || $cbo_category==2)
				{
					?>
					<div style="width:<? echo $value_width + 20?>px;">
						<table width="<? echo $value_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="" align="left">
						
							<thead>
								<tr>
									<th colspan="3" align="left" style="font-size: 16px; font-weight:bold">Knit Finish Fabric</th>
									<th colspan="10">&nbsp;</th>
								</tr>
								<tr>
									<th width="35">SL</th>
									<th width="100">CH. Date</th>
									<th width="100">Challan No</th>
									<th width="100">Cutting Unit</th>
									<th width="100">Buyer</th>
									<th width="100">Style</th>
									<th width="100">Fab. Color</th>
									<th width="100">Batch No</th>
									<th width="200">Fabric Description</th>
									<th width="80">UOM</th>
									<th width="100">Issue Qty</th>
									<th width="100">Unit Price-($)</th>
									<th>Value-($)</th>
								</tr>
							</thead>
						</table>
						
						<div style="width:<? echo $value_width + 20?>px; float:left; max-height:400px; overflow-y:scroll" id="scroll_body" align="left">
							<table width="<? echo $value_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body" align="left">
								<tbody>
									<?
									$i = 1;
									$f_g_total_value=0;
									ksort($f_data_array);
									foreach ($f_data_array as $k_issue_id => $v_issue_id) 
									{
										foreach ($v_issue_id as $k_batch_no => $v_batch_no) 
										{
											foreach ($v_batch_no as $k_prod_id => $v_prod_id) 
											{
												foreach ($v_prod_id as $k_cutting_unit => $row) 
												{
													//var_dump($row);
													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
														<tr bgcolor="<? echo $bgcolor; ?>"> 
															<td width="35" class="wrd_brk" align="center"><? echo $i;?></td>
															<td width="100" class="wrd_brk" align="center"><? echo change_date_format($row['issue_date']);?></td>
															<td width="100" class="wrd_brk" align="center" title="<? echo $row['issue_number'];?>"><? echo $row['issue_number_prefix_num'];?></td>
															<td width="100" class="wrd_brk" align="center"><? echo $cutting_unit_arr[$k_cutting_unit];?></td>
															<td width="100" class="wrd_brk" align="center"><? echo $buyer_arr[$po_array[$row['order_id']]['buyer']]; ?></td>
															<td width="100" class="wrd_brk" align="center"><? echo $po_array[$row["order_id"]]['style_ref_no'];?></td>
															<td width="100" class="wrd_brk" align="center"><? echo $color_arr[$product_array[$k_prod_id]['color']];?></td>
															<td width="100" class="wrd_brk" align="center"><? echo $k_batch_no;?></td>
															<td width="200" class="wrd_brk"><? echo$product_array[$k_prod_id]['product_name_details'];?></td>
															<td width="80" class="wrd_brk" align="center"><? echo $unit_of_measurement[$row['uom']];?></td>
															<td width="100" class="wrd_brk" align="right"><? echo number_format($row['issue_qnty'],2);?></td>
															<td width="100" class="wrd_brk" align="right"><? $f_rate= number_format($row['cons_rate']/$exchange_rate,2); echo $f_rate;?></td>
															<td class="wrd_brk" align="right"><? echo number_format($row['issue_qnty']*$f_rate,2)?></td>
														</tr>
													<?
													$i++;
													$f_g_total_value +=$row['issue_qnty']*$f_rate;
												}
											}  
										} 
										
									} ?>
								
								</tbody>
								<tfoot>
									<th width="35"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="200"></th>
									<th width="80"></th>
									<th width="200" colspan="2"><b>Issue Value USD.	:</b></th>
									<th  id="value_total_alocation_qty" class="wrd_brk"><b><? echo number_format($f_g_total_value, 2) ?></b></th> 
									
								</tfoot>
							</table>
					</div>
					<br>
					<br>
			<?  }
				if($cbo_category==0 || $cbo_category==3)
				{ 
			?>
					<div style="width:<? echo $value_width + 20?>px;">
						<table width="<? echo $value_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="" align="left">
						
							<thead>
								<tr>
									<th colspan="3" align="left" style="font-size: 16px; font-weight:bold">Woven Fabric	</th>
									<th colspan="10">&nbsp;</th>
								</tr>
								<tr>
									<th width="35">SL</th>
									<th width="100">CH. Date</th>
									<th width="100">Challan No</th>
									<th width="100">Cutting Unit</th>
									<th width="100">Buyer</th>
									<th width="100">Style</th>
									<th width="100">Fab. Color</th>
									<th width="100">Batch No</th>
									<th width="200">Fabric Description</th>
									<th width="80">UOM</th>
									<th width="100">Issue Qty</th>
									<th width="100">Unit Price-($)</th>
									<th>Value-($)</th>
								</tr>
							</thead>
						</table>
						
						<div style="width:<? echo $value_width + 20?>px; float:left; max-height:400px; overflow-y:scroll" id="scroll_body" align="left">
							<table width="<? echo $value_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body1" align="left">
								
								<tbody>
									<?
									$i = 1;
									$w_g_total_value = 0;
									ksort($w_data_array);
									foreach ($w_data_array as $k_issue_id => $v_issue_id) 
									{
										foreach ($v_issue_id as $k_batch_no => $v_batch_no) 
										{
											foreach ($v_batch_no as $k_prod_id => $v_prod_id) 
											{
												foreach ($v_prod_id as $k_cutting_unit => $row) 
												{
													//var_dump($row);
													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
														<tr bgcolor="<? echo $bgcolor; ?>"> 
															<td width="35" class="wrd_brk" align="center"><? echo $i;?></td>
															<td width="100" class="wrd_brk" align="center"><? echo change_date_format($row['issue_date']);?></td>
															<td width="100" class="wrd_brk" align="center" title="<? echo $row['issue_number'];?>"><? echo $row['issue_number_prefix_num'];?></td>
															<td width="100" class="wrd_brk" align="center"><? echo $cutting_unit_arr[$k_cutting_unit];?></td>
															<td width="100" class="wrd_brk" align="center"><? echo $buyer_arr[$po_array[$row['order_id']]['buyer']]; ?></td>
															<td width="100" class="wrd_brk" align="center"><? echo $po_array[$row["order_id"]]['style_ref_no'];?></td>
															<td width="100" class="wrd_brk" align="center"><? echo $color_arr[$product_array[$k_prod_id]['color']];?></td>
															<td width="100" class="wrd_brk" align="center"><? echo $k_batch_no;?></td>
															<td width="200" class="wrd_brk"><? echo$product_array[$k_prod_id]['product_name_details'];?></td>
															<td width="80" class="wrd_brk" align="center">
																<? echo $unit_of_measurement[$product_array[$k_prod_id]['unit_of_measure']]; ?>
															</td>
															<td width="100" class="wrd_brk" align="right"><? echo number_format($row['issue_qnty'],2);?></td>
															<td width="100" class="wrd_brk" align="right"><? $w_rate= number_format($row['cons_rate']/$exchange_rate,2); echo $w_rate;?></td>
															<td class="wrd_brk" align="right"><? echo number_format($row['issue_qnty']*$w_rate,2)?></td>
														</tr>
													<?

													$i++;
													$w_g_total_value +=$row['issue_qnty']*$w_rate;

												}
											}  
										} 
										
									} ?>
								</tbody>
								
								<tfoot>
									<th width="35"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="200"></th>
									<th width="80"></th>
									<th width="200" colspan="2"><b>Issue Value USD.	:</b></th>
									<th class="wrd_brk"><b><? echo number_format($w_g_total_value, 2) ?></b></th> 
									
								</tfoot>
							</table>
					</div>

					<br>
					<br>
			<?  } 
				if($cbo_category==0 || $cbo_category==1)
				{
			?>
					<div style="width:<? echo $value_width + 20?>px;">
						<table width="<? echo $value_width; ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="" align="left">
						
							<thead>
								<tr>
									<th colspan="3" align="left" style="font-size: 16px; font-weight:bold">Trims</th>
									<th colspan="12">&nbsp;</th>
								</tr>
								<tr>
									<th width="35">SL</th>
									<th width="100">CH. Date</th>
									<th width="100">Challan No</th>
									<th width="100">Floor</th>
									<th width="100">Unit</th>
									<th width="100">Line</th>
									<th width="100">Buyer</th>
									<th width="100">Style</th>
									<th width="100">Trims Item</th>
									<th width="80">Item Color</th>
									<th width="50">Item Size</th>
									<th width="50">UOM</th>
									<th width="100">Issue Qty</th>
									<th width="100">Unit Price-($)</th>
									<th>Value-($)</th>
								</tr>
							</thead>
						</table>
						
						<div style="width:<? echo $value_width + 20?>px; float:left; max-height:400px; overflow-y:scroll" id="scroll_body" align="left">
							<table width="<? echo $value_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body1" align="left">
								<tbody>
								
									<?
									$i = 1;
									$t_g_total_value = 0;
									ksort($t_data_array);
									foreach ($t_data_array as $k_issue_id => $v_issue_id) 
									{
										foreach ($v_issue_id as $k_trans_id => $v_trans_id) 
										{
											foreach ($v_trans_id as $k_prod_id => $row) 
											{
													//var_dump($row);
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
													<tr bgcolor="<? echo $bgcolor; ?>"> 
														<td width="35" class="wrd_brk" align="center"><? echo $i;?></td>
														<td width="100" class="wrd_brk" align="center"><? echo change_date_format($row['issue_date']);?></td>
														<td width="100" class="wrd_brk" align="center" title="<? echo $row['issue_number'];?>"><? echo $row['issue_number_prefix_num'];?></td>
														<td width="100" class="wrd_brk" align="center"><? echo $row['floor_id'];?></td>
														<td width="100" class="wrd_brk" align="center"><? echo $row['swing_floor'];?></td>
														<td width="100" class="wrd_brk" align="center"><? echo $row['sewing_line'];?></td>
														<td width="100" class="wrd_brk" align="center"><? echo $buyer_arr[$po_array[$row["order_id"]]['buyer']];?></td>
														<td width="100" class="wrd_brk" align="center"><? echo $po_array[$row["order_id"]]['style_ref_no'];?></td>
														<td width="100" class="wrd_brk" align="center"><? echo $trim_group_arr[$row['item_group_id']];?></td>
														<td width="80" class="wrd_brk" align="center"><? echo $color_arr[$row['item_color_id']];?></td>
														<td width="50" class="wrd_brk" align="center"><? echo $row['item_size'];?></td>
														<td width="50" class="wrd_brk" align="center">
															<? echo $unit_of_measurement[$product_array[$k_prod_id]['unit_of_measure']]; ?>
														</td>
														<td width="100" class="wrd_brk" align="right" ><? echo number_format($row['issue_qnty'],2);?></td>
														<td width="100" class="wrd_brk" align="right" ><? $t_rate= number_format($row['cons_rate']/$exchange_rate,2); echo $t_rate;?></td>
														<td class="wrd_brk" align="right"><? echo number_format($row['issue_qnty']*$t_rate,2)?></td>
													</tr>
												<?

												$i++;
												$t_g_total_value +=$row['issue_qnty']*$t_rate;
											}  
										} 
										
									} ?>
								</tbody>	
								<tfoot>
									<th width="35"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="100"></th>
									<th width="80"></th>
									<th width="50"></th>
									<th width="50"></th>
									<th width="200" colspan="2"><b>Issue Value USD.	:</b></th>
									<th><b><? echo number_format($t_g_total_value, 2) ?></b></th>
									
								</tfoot>
						</table>
					</div>
			<?  } ?>
        </fieldset>   
        <?
    }

    foreach (glob("$user_id*.xls") as $filename) 
	{
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}


if($action == "job_no_popup")
{
	echo load_html_head_contents("Style,Job No and Po No Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	var selected_id = new Array(); var selected_name = new Array(); var selected_name_stl = new Array(); var selected_name_po = new Array();

	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

		tbl_row_count = tbl_row_count-1;
		for( var i = 1; i <= tbl_row_count; i++ ) {
			js_set_value( i );
		}
	}

	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function js_set_value( str )
	{

		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

		if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_individual_id' + str).val() );
			selected_name.push( $('#txt_individual' + str).val() );
			selected_name_stl.push( $('#txt_individualstl' + str).val() );
			selected_name_po.push( $('#txt_individualpo' + str).val() );

		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i, 1 );
			selected_name_stl.splice( i, 1 );
			selected_name_po.splice( i, 1 );
		}

		var id = ''; var name = '';var name_stl = '';var name_po = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			name += selected_name[i] + ',';
			name_stl += selected_name_stl[i] + ',';
			name_po += selected_name_po[i] + ',';
		}

		id = id.substr( 0, id.length - 1 );
		name = name.substr( 0, name.length - 1 );
		name_stl = name_stl.substr( 0, name_stl.length - 1 );
		name_po = name_po.substr( 0, name_po.length - 1 );

		$('#hidden_job_no_id').val(id);
		$('#hidden_job_no').val(name);
		$('#hidden_style_no').val(name_stl);
		$('#hidden_po_no').val(name_po);
	}
	</script>
	</head>
	<fieldset style="width:550px">
		<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
		<input type="hidden" name="hidden_job_no_id" id="hidden_job_no_id" value="">
		<input type="hidden" name="hidden_style_no" id="hidden_style_no" value="">
		<input type="hidden" name="hidden_po_no" id="hidden_po_no" value="">
		<table width="530" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="100">Job No</th>
					<th width="80">Job No Prefix</th>
					<th width="130">Style Ref</th>
					<th width="130">Po Number</th>
					<th >Job Year</th>
				</tr>
			</thead>
		</table>
		<div style="width:550px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="530" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?

		if($db_type==0) $year_field=" YEAR(b.insert_date) as year"; 
		else if($db_type==2) $year_field="  to_char(b.insert_date,'YYYY') as year";
		else $year_field="";

		$sql_job_info="SELECT a.id, a.po_number,a.file_no,a.grouping,a.job_no_mst, b.style_ref_no, b.buyer_name, $year_field, b.job_no_prefix_num from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and b.company_name=$companyID and to_char(b.insert_date,'YYYY')=$jobYear order by a.id desc";
		//echo $sql_job_info;

		$rslt_job_info = sql_select($sql_job_info);

		$i = 1;
		foreach ($rslt_job_info as $row)
		{
			//var_dump($val);
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
				<td width="40">
					<? echo $i; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("job_no_mst")]; ?>"/>
					<input type="hidden" name="txt_individualstl" id="txt_individualstl<?php echo $i ?>" value="<? echo $row[csf("style_ref_no")]; ?>"/>
					<input type="hidden" name="txt_individualpo" id="txt_individualpo<?php echo $i ?>" value="<? echo $row[csf("po_number")]; ?>"/>
				</td>
				<td width="100"><p><? echo $row[csf("job_no_mst")]; ?></p></td>
				<td width="80"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
				<td width="130"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
				<td width="130"><p><? echo $row[csf("po_number")]; ?></p></td>
				<td width=""><p><? echo $row[csf("year")]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		
		</table>
		</div>
		<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
	</script>
	<?
}

?>
