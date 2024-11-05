<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";

	echo create_drop_down( "cbo_floor_id", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
  exit();
}

$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$order_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
$machine_arr=return_library_array( "select id,$field_concat from  lib_machine_name",'id','machine_name');
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );

//--------------------------------------------------------------------------------------------------------------------
if($action=="machine_no_popup")
{
	echo load_html_head_contents("Machine Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$im_data=explode('_',$data);
	//print_r ($im_data);
?>
	<script>

		var selected_id = new Array; var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {

			if (str!="") str=str.split("_");

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hid_machine_id').val( id );
			$('#hid_machine_name').val( name );
		}

		function hidden_field_reset()
		{
			$('#hid_machine_id').val('');
			$('#hid_machine_name').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
    </script>
</head>
<input type="hidden" name="hid_machine_id" id="hid_machine_id" />
<input type="hidden" name="hid_machine_name" id="hid_machine_name" />
<?
if($im_data[1]) $floor_cnd="and a.floor_id in($im_data[1]) ";else $floor_cnd="";
	$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name  from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id=2 and a.company_id=$im_data[0] and a.status_active=1 and a.is_deleted=0 and a.is_locked=0 $floor_cnd order by a.machine_no, b.floor_name ";
	//echo  $sql;

	echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "200,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;

   exit();
}


// Booking Search end

if($action=="batch_no_search_popup")
{
	echo load_html_head_contents("Batch No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			//alert(str);
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
    </script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:760px;">
	            <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table">
	            	<thead>
	                  
	                    <th>Batch No </th>
	                    <th>Batch Date</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
	                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
	                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td align="center">
	                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
	                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
	                        </td>	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_batch_no_search_list_view', 'search_div', 'dyeing_prod_focus_report_v2_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" />
	                    	</td>
	                    </tr>
	                    <tr>
	                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}
if($action=="create_batch_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//echo $data[1];
	
	$batch_no=$data[1];
	$search_string="%".trim($data[3])."%";
	//if($batch_no=='') $search_field="b.po_number";  else  $search_field="b.po_number";
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') "; 
		
	
	$start_date =trim($data[2]);
	$end_date =trim($data[3]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	//if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	//else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	
	$sql="select a.id,a.batch_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a where a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 $date_cond $batch_no_cond";	
	$arr=array(1=>$color_library,3=>$batch_for);
	echo  create_list_view("tbl_list", "Batch no,Color,Booking no, Batch for,Batch weight ", "150,100,150,100,70","700","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,batch_for,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "",'','0','',1) ;
	exit();
}//Batch Search End
$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$working_company= str_replace("'","",$cbo_working_company);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$batch_no=str_replace("'","",$txt_batch_no);
	$txt_machine_id= str_replace("'","",$txt_machine_id);
	$exchange_rate= str_replace("'","",$exchange_rate);
	
	
	
	/*$prod_sql= sql_select("select id,gsm,product_name_details from product_details_master");
	foreach($prod_sql as $row)
	{
		$prod_detail_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$prod_detail_gsm_arr[$row[csf("id")]]=$row[csf("gsm")];
	}*/
	
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		
		  if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
				$date_cond=" and e.production_date between '$start_date' and '$end_date'";
				//$date_cond_dyeing=" and a.process_end_date between '$start_date' and '$end_date'";
				//$ship_date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		
	
	}
	if($db_type==0)
	{
	//$year_field_by="and YEAR(a.insert_date)"; 
	$year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
	if($cbo_year!=0) $year_cond=" and year(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";	
	}

	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') ";
//dyes B2

	if ($working_company==0) $workingCompany_cond=""; else $workingCompany_cond="  and a.working_company_id=".$working_company." ";
	if ($working_company==0) $workingCompany_cond2=""; else $workingCompany_cond2="  and c.service_company=".$working_company." ";
	if ($working_company==0) $knit_company_cond=""; else $knit_company_cond="  and a.knitting_company=".$working_company." ";
	if ($cbo_floor_id==0) $floor_cond=""; else $floor_cond="  and c.floor_id=".$cbo_floor_id." ";
	if ($txt_machine_id==0) $mc_cond=""; else $mc_cond="  and c.machine_id=".$txt_machine_id." ";
	ob_start();
	// Only for Self Batch -- Following Batch Progress Report
	  $sql_data_sub="SELECT a.id,a.batch_no,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, c.process_end_date as prod_end_date,e.production_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,pro_fab_subprocess_focus e
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and  e.batch_no=a.batch_no and c.batch_no=e.batch_no and c.entry_form in(38) and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 $batch_no_cond $date_cond  $workingCompany_cond2  $mc_cond $floor_cond
		order by a.id";		
	//echo $sql_data;
	$sub_nameArray=sql_select($sql_data_sub);
	foreach($sub_nameArray as $row)
    {
		$sub_po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		$sub_batch_id_array[$row[csf('id')]] = $row[csf('id')];
	}
	//print_r($sub_po_id_array);
	$sub_poIds = implode(",", array_unique($sub_po_id_array));
	if($sub_poIds !="")
	{
		$sub_po_cond="";
		if(count($sub_po_id_array)>999)
		{
			$chunk_arr=array_chunk($sub_po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($sub_po_cond=="") $sub_po_cond.=" and ( id in ($ids) ";
				else
					$sub_po_cond.=" or  id in ($ids) "; 
			}
			$sub_po_cond.=") ";

		}
		else
		{
			$sub_po_cond.=" and id in ($sub_poIds) ";
		}
	}
	//$order_wise_rate_arr = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0 and rate>0","id","rate");
	 $sql_subcon="SELECT main_process_id,id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0 and rate>0 and main_process_id in(4,3) $sub_po_cond";
	$sql_subcon_res=sql_select($sql_subcon);
	foreach($sql_subcon_res as $row)
    {
		$sub_order_wise_arr[$row[csf('id')]]['process']  = $row[csf('main_process_id')];
		//if($row[csf('main_process_id')]==3)
		//{ 
		$sub_order_wise_arr[$row[csf('id')]]['rate'] = $row[csf('rate')]/$exchange_rate;
		//}
		 
		$sub_order_wise_process_arr[$row[csf('id')]][$row[csf('main_process_id')]]['fin_rate'] = $row[csf('rate')]/$exchange_rate;
		
	}
	
	
    $sql_data="SELECT a.id,a.batch_no,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,a.process_id, c.process_end_date as prod_end_date, e.production_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d, pro_fab_subprocess_focus e
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and  e.batch_no=a.batch_no and c.batch_no=e.batch_no and c.entry_form in(35) and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
  and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and b.po_id>0   $batch_no_cond $date_cond $mc_cond $floor_cond $workingCompany_cond 
		order by a.id";		
	//echo $sql_data;
	$nameArray=sql_select($sql_data);
	foreach($nameArray as $row)
    {
		$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		$batch_id_array[$row[csf('id')]] = $row[csf('id')];
		
	}
	//print_r($po_id_array);
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( b.id in ($ids) ";
				else
					$po_cond.=" or  b.id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and b.id in ($poIds) ";
		}
	}
	
	
	 $sql_po="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.lib_yarn_count_deter_id as deter_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and f.cons_process not in(1,30,35) and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $po_cond order by b.id";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$color_break_down=$val[csf('color_break_down')];
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		if($val[csf('cons_process')]==31 && $color_break_down!='')
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $color_break_down;

			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
			$po_color_fab_array[$val[csf('id')]][$arr_2[1]][$arr_2[3]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] =$arr_2[1];;
			}
		}
		else
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
		}
		
	}
	$process_arr_chk=array(1,30,35);
	foreach($nameArray as $row)
    {
		//$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
		$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
		$all_prod_date_arr[$row[csf('prod_date')]].=$row[csf('id')].',';
		$process_idArr=array_unique(explode(",",$row[csf('process_id')]));
		//print_r($process_idArr);
		$tot_amt=0;
		foreach ($process_idArr as $key => $key_id) //conversion_cost_head_array
		{
			$color_break_down=$po_color_fab_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('deter_id')]][$key_id]['color_break_down'];
				$conv_rate=0;
				if($key_id==31)
				{
					
					$arr_1=explode("__",$color_break_down);
					for($ci=0;$ci<count($arr_1);$ci++)
					{
					$arr_2=explode("_",$arr_1[$ci]);
					$conv_rate=$po_color_fab_array[$row[csf('po_id')]][$arr_2[1]][$arr_2[3]][$row[csf('deter_id')]][$key_id]['rate'];
					//echo $conv_rate.'='.$row[csf('po_id')].'<br>';
					}
					
				}
				else
				{
					$conv_rate=$po_color_fab_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('deter_id')]][$key_id]['rate'];
					
				}
				if($row[csf('entry_form')]==35 && $conv_rate>0)
						{
							//echo $row[csf('prod_date')].'='.$row[csf('po_id')].'='.$key_id.'='.$row[csf('batch_qty')]*$conv_rate.'<br>,';
							//$prod_date_qty_arr[$row[csf('prod_date')]]['self_amount']+=$row[csf('batch_qty')]*$conv_rate;
							$tot_amt+=$row[csf('batch_qty')]*$conv_rate;
							//	echo $row[csf('id')].'='.$row[csf('po_id')].'='.$tot_amt.'='.$row[csf('batch_qty')].'**'.$conv_rate.'<br>';
						}
				
				
		}
	
		
		$prod_date_qty_arr[$row[csf('prod_date')]]['self_amount']+=$tot_amt;
		$prod_date_qty_arr[$row[csf('prod_date')]]['self_qty']+=$row[csf('batch_qty')];
		$prod_date_qty_arr[$row[csf('prod_date')]]['batch_id'].=$row[csf('id')].',';
		
	}
	//--------Subcon----
	foreach($sub_nameArray as $row)
    {
		//$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
	//	if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
			$sub_rate=$sub_order_wise_arr[$row[csf('po_id')]]['rate'];
			$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
			$all_prod_date_arr[$row[csf('prod_date')]].=$row[csf('id')].',';
			$prod_date_qty_arr[$row[csf('prod_date')]]['sub_batch_id'].=$row[csf('id')].',';
			$prod_date_qty_arr[$row[csf('prod_date')]]['subcon_qty']+=$row[csf('batch_qty')];
			$prod_date_qty_arr[$row[csf('prod_date')]]['subcon_amount']+=$row[csf('batch_qty')]*$sub_rate;
		
	}
	// echo "<pre>";
	// print_r($color_id_rowspan);die;

	// =======================Total Fabric Booking Qty (Fin.Fab.) with order Start ==============
	//$poIds=chop($self_po_id,','); 
	$batchIds = implode(",", array_unique($batch_id_array));
	if($batchIds !="")
	{
		$batch_cond="";
		if(count($batch_id_array)>999)
		{
			$chunk_arr=array_chunk($batch_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($batch_cond=="") $batch_cond.=" and ( b.batch_id in ($ids) ";
				else
					$batch_cond.=" or  b.batch_id in ($ids) "; 
			}
			$batch_cond.=") ";

		}
		else
		{
			$batch_cond.=" and b.batch_id in ($batchIds) ";
		}
	}
	//
// ========================================Start===========================================
	$finish_data_arr=array();
	$sql_dtls=sql_select("SELECT a.receive_date,b.batch_id,(b.receive_qnty) as finish_qty	
	from inv_receive_master a,pro_finish_fabric_rcv_dtls b 
	where a.id=b.mst_id  and a.entry_form=7  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_company_cond 
	$batch_cond order by b.batch_id");
	/*echo "SELECT a.receive_date,b.batch_id,(b.receive_qnty) as finish_qty	
	from inv_receive_master a,pro_finish_fabric_rcv_dtls b 
	where a.id=b.mst_id  and a.entry_form=7  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_company_cond 
	$batch_cond order by b.batch_id";*/
	
	
	foreach($sql_dtls as $row_fin)// for Finish Production
	{
		$finish_data_arr[$row_fin[csf('batch_id')]]['finish_qty']+=$row_fin[csf('finish_qty')];
	}
	
	//=====SubCon Fab Fin. Page====
	$sub_batchIds = implode(",", array_unique($sub_batch_id_array));
	if($sub_batchIds !="")
	{
		$sub_batch_cond="";
		if(count($sub_batch_id_array)>999)
		{
			$chunk_arr=array_chunk($sub_batch_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($sub_batch_cond=="") $sub_batch_cond.=" and ( b.batch_id in ($ids) ";
				else
					$sub_batch_cond.=" or  b.batch_id in ($ids) "; 
			}
			$sub_batch_cond.=") ";

		}
		else
		{
			$sub_batch_cond.=" and b.batch_id in ($sub_batchIds) ";
		}
	}
	
	$sub_finish_data_arr=array();
	
	
  $sql_sub_fin=" SELECT  b.order_id,b.batch_id,b.product_qnty as fin_qty,a.product_type as process from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292  and a.product_type=4 and a.company_id=$working_company";
	//$sql_sub_dye_res = sql_select($sql_sub_dye);
	$sql_sub_fin_result = sql_select($sql_sub_fin);
	
	foreach($sql_sub_fin_result as $row)// for Finish Production
	{
		$finish_data_arr[$row[csf('batch_id')]]['sub_finish_qty']+=$row[csf('fin_qty')];
		$finish_data_arr[$row[csf('batch_id')]]['sub_finish_amt']+=$row[csf('fin_qty')]*$sub_order_wise_process_arr[$row[csf('order_id')]][$row[csf('process')]]['fin_rate'];
	}
//	print_r($finish_data_arr);
	unset($sql_sub_fin_result);
	

	// ========================================End============================================
	
	 $sql_dyes_cost =sql_select("select a.batch_no,b.item_category,(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b
    where a.id=b.mst_id and b.transaction_type=2  and a.company_id=$working_company  and a.entry_form=5 and a.batch_no  is not null  and   b.item_category in (5,6,7) "); 
	$dyes_chemical_arr=array();
	foreach($sql_dyes_cost as $val)
	{
		$batchArr=array_unique(explode(",",$val[csf("batch_no")]));
		foreach($batchArr as $bid)
		{
		$dyes_chemical_arr[$bid]['chemical_cost']+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
		}
	}
	
	/*echo "<pre>";
	print_r($fab_booking_qty_arr);*/
	// =========================== Total Fabric Booking Qty (Fin.Fab.) with order End ===================


	?>
	<div style="width:1690px">	
    <style type="text/css">
				.alignment_css
				{
					word-break: break-all;
					word-wrap: break-word;
				}
			</style>
            
		
        <table width="1670" cellspacing="0" cellpadding="0" border="0" rules="all" >
		    <tr class="form_caption">
		        <td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		    </tr>
		    <tr class="form_caption">
		        <td colspan="17" align="center"><?   echo $company_library[$working_company]; ?><br>
		        <b>
		        <?
				echo ($start_date == '0000-00-00' || $start_date == '' ? '' : change_date_format($start_date)).' To ';echo  ($end_date == '0000-00-00' || $end_date == '' ? '' : change_date_format($end_date));
		        ?> </b>
		        </td>
		    </tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1670" class="rpt_table">
			<thead>
             <tr>
				<th width="20" style="word-wrap: break-word; width: 20px;">SL</th>
				<th width="150" style="word-wrap: break-word; width: 150px;">Date</th>
				<th width="100"  style="word-wrap: break-word; width: 100px;">In House<br>Dyeing  Kg</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">InHouse<br>Earn$</th>	
				<th width="100"  style="word-wrap: break-word; width: 100px;" >Inbound<br>SubCon<br>Dyeing Kg</th>			
				<th width="100" style="word-wrap: break-word; width: 100px;">Inbound<br>SubCon<br>Earn$</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Total <br>DyeingKg</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Total<br>Dyeing / Earn$</th>
                
				<th width="100"  style="word-wrap: break-word; width: 100px;">InHouse<br> FinishKg</th>				
				<th width="100" style="word-wrap: break-word; width: 100px;">Inbound <br>Subcon<br> Finish Kg</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Total FinishKg</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Inbound <br>Subcon<br>Finish Earn$</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Grand Total<br>Dyeing+Finish <br> Earn$</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Total Dyes<br>Chemical<br>Cost$</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Income $</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Connjumption <br>%</th>
				<th width="" style="word-wrap: break-word; width: 100px;">Average<br>costPer kg($)</th>
                </tr>
				
			</thead>
		</table>

	
         <div style="max-height:300px; overflow-y:scroll; width:1688px" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1670" class="rpt_table" id="table_body">
				<?

			    $i=1;
			    $total_self_qty_inhouse=$total_self_earn_inhouse=$total_subcon_qty_inbound=$total_subcon_earn_inbound=$total_tot_dying_qty_kg=$total_tot_dying_earning=$total_inhouse_finish_qty=$total_inbound_finish_qty=$total_tot_finishing_kg=$total_inbound_sub_finish_amt=$total_tot_dyeing_fin_earn=$total_tot_dye_chemical_cost=$total_tot_income=$total_avg_cost_per=0;
				ksort($prod_date_arr);
				foreach($prod_date_arr as $date_key=>$row)
				{
					//$total_booking_qty=$total_batch_qty=$total_finish_qty=$total_delivery_qty=$total_process_loss_qty=0;
					
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$po_id=rtrim($row[('po_id')],',');
							    $job_no=""; $buyer="";
							    $po_id=array_unique(explode(",",$po_id));
							    foreach($po_id as $id)
							    {
									if($row[('entry_form')]==36) //SubCon
									{

									}
									else
									{
										//if($job_no=="") $job_no=$job_array[$id]['job']; else $job_no.=",".$job_array[$id]['job'];
										//if($buyer=="") $buyer=$buyer_library[$job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$job_array[$id]['buyer']];
										//if($style_no=="") $style_no=$job_array[$id]['style']; else $style_no.=",".$job_array[$id]['style'];
									}
							    }
								$self_qty_inhouse=	$prod_date_qty_arr[$date_key]['self_qty'];
								$batch_ids=rtrim($prod_date_qty_arr[$date_key]['batch_id'],',');
								$sub_batch_ids=rtrim($prod_date_qty_arr[$date_key]['sub_batch_id'],',');
								$all_batch_ids=rtrim($all_prod_date_arr[$date_key],',');//$all_prod_date_arr[$row[csf('prod_date')]]
								$all_batch_idsArr=array_unique(explode(",",$all_batch_ids));
								$all_batch_idspopup=implode(",",array_unique(explode(",",$all_batch_ids)));
								$subcon_qty_inbound=	$prod_date_qty_arr[$date_key]['subcon_qty'];
								$self_earn_inhouse=	$prod_date_qty_arr[$date_key]['self_amount'];
								$subcon_earn_inbound=	$prod_date_qty_arr[$date_key]['subcon_amount'];
								$tot_dying_qty_kg=$self_qty_inhouse+$subcon_qty_inbound;
								$tot_dying_earning=$self_earn_inhouse+$subcon_earn_inbound;
								$batch_idsArr=array_unique(explode(",",$batch_ids));
								$inhouse_finish_kg=0;
								foreach($batch_idsArr as $bid)
								{
									$inhouse_finish_kg+=$finish_data_arr[$bid]['finish_qty'];
								}
								$sub_batch_idsArr=array_unique(explode(",",$sub_batch_ids));
								$all_sub_batch_idsArr=implode(",",array_unique(explode(",",$sub_batch_ids)));
								$inbound_sub_finish_kg=$inbound_sub_finish_amt=0;
								foreach($sub_batch_idsArr as $bid)
								{
									$inbound_sub_finish_kg+=$finish_data_arr[$bid]['sub_finish_qty'];
									$inbound_sub_finish_amt+=$finish_data_arr[$bid]['sub_finish_amt'];
								}
								$tot_finishing_kg=$inhouse_finish_kg+$inbound_sub_finish_kg;
								$tot_dyeing_fin_earn=$tot_dying_earning+$inbound_sub_finish_amt;
								
								$tot_dye_chemical_cost=0;$chemical_batch_ids='';
								foreach($all_batch_idsArr as $bid)
								{
									$tot_dye_chemical_cost+=$dyes_chemical_arr[$bid]['chemical_cost'];
									if($chemical_batch_ids=='') $chemical_batch_ids=$bid;else $chemical_batch_ids.=",".$bid;
									//$inbound_sub_finish_amt+=$finish_data_arr[$bid]['sub_finish_amt'];
								}
							
								$finish_qty=$finish_data_arr[$batch_id]['finish_qty'];
								$delivery_qty=$delivery_data_arr[$batch_id]['delivery'];

								$booking_rowspan=$booking_rowspan_arr[$booking_key];
								$color_rowspan=$color_rowspan_arr[$booking_key][$color_id];
								$day_name=date('l',strtotime($date_key));
								$day_mon=date('F',strtotime($date_key));
								$day=date('d',strtotime($date_key));
								$year=date('Y',strtotime($date_key));
								$full_date=$day_name.', '.$day_mon.' '.$day.', '.$year;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
									
									<td width="20"><? echo $i; ?></td>
									<td width="150"  style="word-break: break-all; word-wrap: break-word;"><p><? echo $full_date; ?></p></td>
									<td width="100" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $all_batch_idspopup; ?>','<? echo $working_company; ?>','dyeing_earn_inhouse_popup',1)"><? echo number_format($self_qty_inhouse,0); ?></a></p></td>
									<td width="100"  style="word-break: break-all; word-wrap: break-word;" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $all_batch_idspopup; ?>','<? echo $working_company; ?>','dyeing_earn_inhouse_popup',2)"><? echo number_format($self_earn_inhouse,2); ?></a></p></td>
									<td width="100" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $all_sub_batch_idsArr; ?>','<? echo $working_company; ?>','dyeing_earn_inboundSub_popup',3)"><? echo number_format($subcon_qty_inbound,0); ?></a></p></td>
                                    <td width="100" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $all_sub_batch_idsArr; ?>','<? echo $working_company; ?>','dyeing_earn_inboundSub_popup',4)"><? echo number_format($subcon_earn_inbound,2); ?></a></p></td>
									<td width="100" align="right" title="Inhouse Dyeing+Subcon Inbound Qty"><p><? echo number_format($tot_dying_qty_kg,0); ?></p></td>
                                    <td width="100" align="right" title="Inhouse Dyeing+Subcon Inbound Earn"><p><? echo number_format($tot_dying_earning,2); ?></p></td>
								
									
									<td width="100" align="right" ><p><? echo number_format($inhouse_finish_kg,0); ?></p></td>
									<td width="100" align="right" title="sub Batch ID=<? echo $sub_batch_ids;?>"><p><? echo number_format($inbound_sub_finish_kg,0); ?></p></td>
									<td width="100" align="right" title="Inhouse FinKg+Inbound Sub. FinKg"><p><? echo number_format($tot_finishing_kg,0); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($inbound_sub_finish_amt,2);?></p></td>
									<td width="100" align="right" title="Tot Dyeing Earn+Tot SubCon Finish Earn"><p><?  echo number_format($tot_dyeing_fin_earn,2); ?></p></td>
									<td width="100" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $chemical_batch_ids; ?>','<? echo $working_company; ?>','dyes_chemical_cost_earn_popup',5)"><? echo number_format($tot_dye_chemical_cost,2); ?></a></p></td>
									<td width="100" align="right" title="Tot Dye Fin -Tot Dyes Chemical Cost"><p><? $tot_income=$tot_dyeing_fin_earn-$tot_dye_chemical_cost; echo number_format($tot_income,2); ?></p></td>
									<td width="100" align="right"  title="Tot Dyes Chemical Cost/Tot Dyeing Fin Earn*100"><p><?  $conj_per=$tot_dye_chemical_cost/$tot_dyeing_fin_earn*100;  if($tot_dye_chemical_cost) echo number_format($conj_per,2);else echo 0;?>	</p></td>
									<td width="100" align="right" title="Tot Dyes Chemical Cost/Tot Dyeing Kg"><?  $avg_cost_per=$tot_dye_chemical_cost/$tot_dying_qty_kg*100;  echo number_format($avg_cost_per,2);?>  </td>
									
									
							    </tr>
							    <?
								
								$total_self_qty_inhouse+=$self_qty_inhouse;
								$total_self_earn_inhouse+=$self_earn_inhouse;
								$total_subcon_qty_inbound+=$subcon_qty_inbound;
								$total_subcon_earn_inbound+=$subcon_earn_inbound;
								$total_tot_dying_qty_kg+=$tot_dying_qty_kg;
								$total_tot_dying_earning+=$tot_dying_earning;
								$total_inhouse_finish_qty+=$inhouse_finish_kg;
								$total_inbound_finish_qty+=$inbound_sub_finish_kg;
								$total_tot_finishing_kg+=$tot_finishing_kg;
								$total_inbound_sub_finish_amt+=$inbound_sub_finish_amt;
								$total_tot_dyeing_fin_earn+=$tot_dyeing_fin_earn;
								$total_tot_dye_chemical_cost+=$tot_dye_chemical_cost;
								$total_tot_income+=$tot_income;
								$total_avg_cost_per+=$avg_cost_per;
							
							    $i++;
							
				}
			    ?>
                <tfoot>
                <tr style="background:#999999">
                	<th width="20">&nbsp;</th>
					<th width="150" align="right">Total</th>
					<th width="100" align="right"><? echo number_format($total_self_qty_inhouse,0,'.',''); ?></th>
					<th width="100" align="right"><? echo number_format($total_self_earn_inhouse,2,'.',''); ?></th>
					<th width="100" align="right"><? echo number_format($total_subcon_qty_inbound,0,'.',''); ?></th>
					<th width="100" align="right"><? echo number_format($total_subcon_earn_inbound,2,'.',''); ?></th>
					<th width="100" align="right"><strong><? echo number_format($total_tot_dying_qty_kg,0,'.',''); ?></strong></th>
					<th width="100" align="right"><strong><? echo number_format($total_tot_dying_earning,2,'.',''); ?></strong></th>  
					<th width="100"  align="right"><? echo number_format($total_inhouse_finish_qty,0,'.',''); ?></th>
					<th width="100"  align="right"><? echo number_format($total_inbound_finish_qty,0,'.',''); ?></th>    
					<th width="100"  align="right"><? echo number_format($total_tot_finishing_kg,0,'.',''); ?>&nbsp;</th>
					<th width="100" align="right"><strong><? echo number_format($total_inbound_sub_finish_amt,2); ?></strong></th> 
					<th width="100" align="right"><strong><? echo number_format($total_tot_dyeing_fin_earn,2); ?></strong></th>
					<th width="100" align="right"><strong><? echo number_format($total_tot_dye_chemical_cost,2); ?></strong></th>
					<th width="100" align="right"><strong><? echo  number_format($total_tot_income,2); ?></strong></th>       
					<th width="100"><? echo  number_format($total_tot_dye_chemical_cost/$total_tot_dying_earning*100,2); ?></strong></th>
					<th width=""><? echo  number_format($total_avg_cost_per,2); ?></strong> </th>
                </tr>
                </tfoot>
			</table>
            </div>
			
			

    </div>
    
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if ($action=="dyeing_earn_inhouse_popup")  // All Production Data popup dyeing_earn_inboundSub_popup
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Dyeing inhouse poppup Info","../../../", 1, 1, $unicode,'','');
	
	$date_key=str_replace("'","",$date_key);
	$batch_id=str_replace("'","",$batch_id);
	$company_id=str_replace("'","",$companyID);
	$type_id=str_replace("'","",$type);
	//echo $date_key.'dd';die;
	 if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$date_key),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$date_key),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$date_key),"","",1);
				$end_date=change_date_format(str_replace("'","",$date_key),"","",1);
			}
				$date_cond=" and e.production_date between '$start_date' and '$end_date'";
	
	 $sql_data="SELECT a.id,a.batch_no,a.batch_date,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,a.process_id, e.production_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d,pro_fab_subprocess_focus e
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and  e.batch_no=a.batch_no and c.batch_no=e.batch_no and c.entry_form in(35) and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 and c.status_active=1 and c.is_deleted=0   and d.status_active=1 and d.is_deleted=0 and a.id in($batch_id) and a.working_company_id=$company_id  $date_cond 
		order by a.id";		 
	//echo $sql_data;
	$nameArray=sql_select($sql_data);
	foreach($nameArray as $row)
    {
		$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		$batch_id_array[$row[csf('id')]] = $row[csf('id')];
		
	}
	//print_r($po_id_array);
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( b.id in ($ids) ";
				else
					$po_cond.=" or  b.id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and b.id in ($poIds) ";
		}
	}
	
	
	  $sql_po="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.lib_yarn_count_deter_id as deter_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0    and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and f.cons_process not in(1,30,35) and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $po_cond order by b.id";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$color_break_down=$val[csf('color_break_down')];
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		
		if($val[csf('cons_process')]==31 && $color_break_down!='')
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $val[csf('color_break_down')];

			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
			$po_color_fab_array[$val[csf('id')]][$arr_2[1]][$arr_2[3]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] =$arr_2[1];;
			//echo $arr_2[1].', ';
			}
		}
		else
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
			//echo $val[csf('charge_unit')].',';
		}
		
	}
	 $process_array=array(1,30,35);
	foreach($nameArray as $row)
    {
		//$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
		$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
		$process_idArr=array_unique(explode(",",$row[csf('process_id')]));
		$all_prod_date_arr[$row[csf('prod_date')]].=$row[csf('id')].',';
		//$process_idArr=array_unique(explode(",",$row[csf('process_id')]));
		$dying_amt=0;
		foreach ($process_idArr as $key => $key_id) 
		{
			//echo $color_break_down.',';
			//if(!in_array($key, $process_array ))
			//{

			if($key_id==31)
			{
				$color_break_down=$po_color_fab_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('deter_id')]][$key_id]['color_break_down'];
	
				$arr_1=explode("__",$color_break_down);
				$conv_rate=0;
				for($ci=0;$ci<count($arr_1);$ci++)
				{
				$arr_2=explode("_",$arr_1[$ci]);
				$conv_rate=$po_color_fab_array[$row[csf('po_id')]][$arr_2[1]][$arr_2[3]][$row[csf('deter_id')]][$key_id]['rate'];
				//echo $conv_rate.'='.$row[csf('po_id')].'='.$arr_2[1].'='.$arr_2[3].'='.$val[csf('deter_id')].'='.$key.'<br>';
				}
				
			}
			else
			{
				$conv_rate=$po_color_fab_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('deter_id')]][$key_id]['rate'];
				//echo "A,";
			}
			
		 //}
				if($row[csf('entry_form')]==35 && $conv_rate>0)
			 {
				$dying_amt+=$row[csf('batch_qty')]*$conv_rate;
				//echo $conv_rate.'='.$row[csf('batch_qty')].'<br>';
				
			
			 }
		}
		//if($row[csf('entry_form')]==35 && $row[csf('batch_qty')]>0)
				//{
					//echo $conv_rate.'='.$row[csf('batch_qty')]*$conv_rate.'<br>';
					
					//echo $row[csf('id')].'='.$row[csf('po_id')].'='.$key_id.'='.$dying_amt.'<br>';
					
					$prod_date_qty_arr[$row[csf('id')]]['self_amount']+=$dying_amt;//$row[csf('batch_qty')]*$conv_rate;
					$prod_date_qty_arr[$row[csf('id')]]['self_qty']+=$row[csf('batch_qty')];
					$prod_date_qty_arr[$row[csf('id')]]['prod_date']=$row[csf('prod_date')];
					$prod_date_qty_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
					$prod_date_qty_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
					$prod_date_qty_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
					
					
				//}
		//echo $color_break_down.'dd';
		//$po_color_fab_array[$row[csf('po_id')]][$arr_2[1]][$arr_2[3]][$row[csf('deter_id')]][$row[csf('cons_process')]]['rate'];
		
		
		
	}
	
	?>
	<div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
        <?
        $table_width=550;
		?>
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
         <caption> <b>Inhouse Details</b></caption>
            <thead>
                <tr>
                    <th width="20" >SL</th>
                    <th width="80">Focus Prod Date</th>
                    <th width="80">Batch No</th>
                    <th width="80">Batch Date</th>
                    <th width="60">Batch ID</th>
                    <th width="70">Batch Qty</th>
                    <?
                    if($type_id==2)
					{
					?>
                    <th width="80">Rate$</th>
                    <th width="">Amount$</th>
                    <?
					}
					?>
                    
                </tr>
            </thead>
            <tbody>
                 <?
                        $i=1;$tot_dyeing_qty=$tot_dyeing_amount=0;
                        foreach($prod_date_qty_arr as $bId=>$row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                         ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
                                <td  width="20"><? echo $i; ?></td>
                                <td  width="80"><? echo change_date_format($row[('prod_date')]); ?></td>
                             
                                <td  width="80" title="Color_id=<? echo $row[('color_id')];?>"><? echo $row[('batch_no')]; ?></td>
                                <td  width="80"><? echo change_date_format($row[('batch_date')]); ?></td>
                                 <td  width="60"><? echo $bId; ?></td>
                                <td  width="70" align="right"><? echo number_format($row[('self_qty')],0);?></td>
                                 <?
								// $col_spna=5;
							if($type_id==2)
							{
							?>
                                <td  width="80" align="right" title="<? echo $row[('self_amount')]/$row[('self_qty')];?>"><? echo number_format($row[('self_amount')]/$row[('self_qty')],3); ?></td>
                                <td  width="" align="right"><? echo number_format($row[('self_amount')],2); ?></td>
                                <?
								$col_spna=3;
							}
								?>

                            </tr>
                        <?
                        $i++;
						$tot_dyeing_qty+=$row[('self_qty')];
						$tot_dyeing_amount+=$row[('self_amount')];
                        }
                        ?>
                        <tr bgcolor="#666666"> 
                        <td align="right" colspan="5"><strong>Total</strong></td>
                        <td align="right"><? echo $tot_dyeing_qty; ?>&nbsp;</td>
                         <?
								// $col_spna=5;
						if($type_id==2)
						{
							?>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_dyeing_amount,2,'.',''); ?>&nbsp;</td>
                        <?
						}
						?>
                    </tr>
            </tbody>
		</table>
        </div>
        <?
}
if ($action=="dyeing_earn_inboundSub_popup")  // All Production Data popup 
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Dyeing inhouse poppup Info","../../../", 1, 1, $unicode,'','');
	
	$date_key=str_replace("'","",$date_key);
	$batch_id=str_replace("'","",$batch_id);
	$company_id=str_replace("'","",$companyID);
	$type_id=str_replace("'","",$type);
	//echo $date_key.'dd';die;
	 if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$date_key),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$date_key),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$date_key),"","",1);
				$end_date=change_date_format(str_replace("'","",$date_key),"","",1);
			}
				$date_cond=" and e.production_date between '$start_date' and '$end_date'";
	
/*	$sql_data="SELECT a.id,a.batch_no,a.batch_date,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,c.process_id, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and c.entry_form in(35) and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 and a.working_company_id=$company_id  $date_cond 
		order by a.id";	*/
		 $sql_data_sub="SELECT a.id,a.batch_no,a.batch_date,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, e.production_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c, pro_fab_subprocess_focus e
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and  e.batch_no=a.batch_no and c.batch_no=e.batch_no and c.entry_form in(38) and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 and a.id in($batch_id) and a.company_id=$company_id $date_cond
		order by a.id";		
	//echo $sql_data;
	$sub_nameArray=sql_select($sql_data_sub);
	foreach($sub_nameArray as $row)
    {
		$sub_po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		$sub_batch_id_array[$row[csf('id')]] = $row[csf('id')];
	}
	//print_r($sub_po_id_array);
	$sub_poIds = implode(",", array_unique($sub_po_id_array));
	if($sub_poIds !="")
	{
		$sub_po_cond="";
		if(count($sub_po_id_array)>999)
		{
			$chunk_arr=array_chunk($sub_po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($sub_po_cond=="") $sub_po_cond.=" and ( id in ($ids) ";
				else
					$sub_po_cond.=" or  id in ($ids) "; 
			}
			$sub_po_cond.=") ";

		}
		else
		{
			$sub_po_cond.=" and id in ($sub_poIds) ";
		}
	}
	//$order_wise_rate_arr = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0 and rate>0","id","rate");
	$sql_subcon="SELECT main_process_id,id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0 and rate>0 and main_process_id in(4,3) $sub_po_cond";
	$sql_subcon_res=sql_select($sql_subcon);
	foreach($sql_subcon_res as $row)
    {
		$sub_order_wise_arr[$row[csf('id')]]['process']  = $row[csf('main_process_id')];
		//if($row[csf('main_process_id')]==3) //dying
		//{ 
		$sub_order_wise_arr[$row[csf('id')]]['rate'] = $row[csf('rate')]/$exchange_rate;
		//}
		 
		$sub_order_wise_process_arr[$row[csf('id')]][$row[csf('main_process_id')]]['fin_rate'] = $row[csf('rate')]/$exchange_rate;
		
	}
		 
	//echo $sql_data;
	$nameArray=sql_select($sql_data);
	foreach($sub_nameArray as $row)
    {
		$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		$batch_id_array[$row[csf('id')]] = $row[csf('id')];
		
	}
	//print_r($po_id_array);
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( b.id in ($ids) ";
				else
					$po_cond.=" or  b.id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and b.id in ($poIds) ";
		}
	}
	
	
	
	foreach($sub_nameArray as $row)
    {
		//$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
		$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
		$all_prod_date_arr[$row[csf('prod_date')]].=$row[csf('id')].',';
		$sub_order_wise_process_arr[$row[csf('id')]][$row[csf('main_process_id')]]['fin_rate'];
		
		//echo $color_break_down.'dd';
		//$po_color_fab_array[$row[csf('po_id')]][$arr_2[1]][$arr_2[3]][$row[csf('deter_id')]][$row[csf('cons_process')]]['rate'];
		$sub_rate=$sub_order_wise_arr[$row[csf('po_id')]]['rate'];
			/*$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
			$all_prod_date_arr[$row[csf('prod_date')]].=$row[csf('id')].',';
			$prod_date_qty_arr[$row[csf('prod_date')]]['sub_batch_id'].=$row[csf('id')].',';
			$prod_date_qty_arr[$row[csf('prod_date')]]['subcon_qty']+=$row[csf('batch_qty')];
			$prod_date_qty_arr[$row[csf('prod_date')]]['subcon_amount']+=$row[csf('batch_qty')]*$sub_rate;*/
		
		
			$prod_date_qty_arr[$row[csf('id')]]['self_qty']+=$row[csf('batch_qty')];
			$prod_date_qty_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$prod_date_qty_arr[$row[csf('id')]]['prod_date']=$row[csf('prod_date')];
			$prod_date_qty_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$prod_date_qty_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			$prod_date_qty_arr[$row[csf('id')]]['self_amount']+=$row[csf('batch_qty')]*$sub_rate;
		
	}
	
	?>
	<div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
        <?
        $table_width=580;
		?>
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
        <caption> <b>SubCon Details</b></caption>
            <thead>
                <tr>
                    <th width="20" >SL</th>
                    <th width="80">Focus Prod Date</th>
                    <th width="80">Batch No</th>
                    <th width="80">Batch Date</th>
                     <th width="80">Batch ID</th>
                    <th width="80">Batch Qty</th>
                    <?
                    if($type_id==4)
					{
					?>
                    <th width="80">Rate$</th>
                    <th width="">Amount$</th>
                    <?
					}
					?>
                    
                </tr>
            </thead>
            <tbody>
                 <?
                        $i=1;$tot_dyeing_qty=$tot_dyeing_amount=0;
                        foreach($prod_date_qty_arr as $batch_id=>$row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                         ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
                                <td  width="20"><? echo $i; ?></td>
                                <td  width="80"><? echo change_date_format($row[('prod_date')]); ?></td>
                                <td  width="80"><? echo $row[('batch_no')]; ?></td>
                                <td  width="80"><? echo change_date_format($row[('batch_date')]); ?></td>
                                <td  width="80"><? echo $batch_id; ?></td>
                             
                                <td  width="80" align="right"><? echo number_format($row[('self_qty')],0);?></td>
                                 <?
								// $col_spna=5;
							if($type_id==4)
							{
							?>
                                <td  width="80" align="right"><? echo number_format($row[('self_amount')]/$row[('self_qty')],3); ?></td>
                                <td  width="" align="right"><? echo number_format($row[('self_amount')],2); ?></td>
                                <?
								$col_spna=3;
							}
								?>
                            </tr>
                        <?
                        $i++;
						$tot_dyeing_qty+=$row[('self_qty')];
						$tot_dyeing_amount+=$row[('self_amount')];
                        }
                        ?>
                        <tr bgcolor="#666666"> 
                        <td align="right" colspan="5"><strong>Total</strong></td>
                        <td align="right"><? echo $tot_dyeing_qty; ?>&nbsp;</td>
                         <?
								// $col_spna=5;
						if($type_id==4)
						{
							?>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_dyeing_amount,2,'.',''); ?>&nbsp;</td>
                        <?
						}
						?>
                    </tr>
            </tbody>
		</table>
        </div>
        <?
}

if ($action=="dyes_chemical_cost_earn_popup")  // All Production Data popup 
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Dyeing inhouse poppup Info","../../../", 1, 1, $unicode,'','');
	
	$date_key=str_replace("'","",$date_key);
	$batch_id=str_replace("'","",$batch_id);
	$company_id=str_replace("'","",$companyID);
	$type_id=str_replace("'","",$type);
	$exchange_rate=str_replace("'","",$exchange_rate);
//	echo $batch_id.'dd';die;
	 if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$date_key),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$date_key),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$date_key),"","",1);
				$end_date=change_date_format(str_replace("'","",$date_key),"","",1);
			}
				$date_cond=" and e.production_date between '$start_date' and '$end_date'";
	
	 $sql_dyes_cost =sql_select("select a.batch_no,b.item_category,(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b
    where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=5  and a.company_id=$company_id  and a.batch_no  is not null  and   b.item_category in (5,6,7) ");
	foreach($sql_dyes_cost as $val)
	{
		$batchArr=array_unique(explode(",",$val[csf("batch_no")]));
		foreach($batchArr as $bid)
		{
		$dyes_chemical_arr[$bid]['chemical_cost']+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
		}
  }
	
	$sql_data="SELECT a.id,a.batch_no,a.batch_date,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,c.process_id, e.production_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d,pro_fab_subprocess_focus e
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and  e.batch_no=a.batch_no and c.batch_no=e.batch_no and c.entry_form in(35) and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 and a.id in($batch_id) and a.working_company_id=$company_id  $date_cond 
		order by a.id";		 
	//echo $sql_data;
	$nameArray=sql_select($sql_data);
	
	

	foreach($nameArray as $row)
    {
		//$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
		$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
		$all_prod_date_arr[$row[csf('prod_date')]].=$row[csf('id')].',';
		
			$chemical_cost=$dyes_chemical_arr[$row[csf('id')]]['chemical_cost'];
		
		if($chemical_cost>0)
		{
			$prod_date_qty_arr[$row[csf('id')]]['self_qty']+=$row[csf('batch_qty')];
			$prod_date_qty_arr[$row[csf('id')]]['prod_date']=$row[csf('prod_date')];
			$prod_date_qty_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$prod_date_qty_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			$prod_date_qty_arr[$row[csf('id')]]['self_amount']+=$row[csf('batch_qty')]*$conv_rate;
			$prod_date_qty_arr[$row[csf('id')]]['dyes_cost']=$chemical_cost;
		}
		
		
	}
	
	?>
	<div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
        <?
        $table_width=550;
		?>
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
        <caption><b> Issue Details:</b> </caption>
            <thead>
                <tr>
                    <th width="20" >SL</th>
                    <th width="80">Focus Prod Date</th>
                    <th width="80">Batch No</th>
                      <th width="80">Batch Date</th>
                    <th width="70">Batch ID</th>
                  
                    <th width="70">Batch Qty</th>
                    <th width="70">Issue Amount$</th>
                    <th width="">AVG Rate$</th>
                   
                   
                    
                </tr>
            </thead>
            <tbody>
                 <?
                        $i=1;$tot_dyeing_qty=$tot_dyeing_amount=$tot_chemical_cost_amount=0;
                        foreach($prod_date_qty_arr as $bId=>$row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$chemical_cost=$row[('dyes_cost')];
							//$chemical_cost=$dyes_chemical_arr[$bId]['chemical_cost'];dyes_cost
							if($chemical_cost>0)
							{
                         ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
                                <td  width="20"><? echo $i; ?></td>
                                <td  width="80"><? echo change_date_format($row[('prod_date')]); ?></td>
                               
                                <td  width="80"><? echo $row[('batch_no')]; ?></td>
                                <td  width="80"><? echo change_date_format($row[('batch_date')]); ?></td>
                                 <td  width="70"><? echo $bId; ?></td>
                                <td  width="70" align="right"><? echo number_format($row[('self_qty')],0);?></td>
                                <td  width="70" align="right"><? echo number_format($chemical_cost,2); ?></td>
                                <td  width="" align="right" title="Issue Amount/Batch Qty"><? echo number_format($chemical_cost/$row[('self_qty')],3); ?></td>
                              
								 
                            </tr>
                        <?
                        $i++;
						$tot_dyeing_qty+=$row[('self_qty')];
						$tot_dyeing_amount+=$chemical_cost/$row[('self_qty')];
						$tot_chemical_cost_amount+=$chemical_cost;
                        }
                        ?>
                      
                    <?
					 }
					?>
                      <tr bgcolor="#666666"> 
                        <td align="right" colspan="5"><strong>Total</strong></td>
                        <td align="right"><? echo number_format($tot_dyeing_qty,2); ?>&nbsp;</td> 
                        <td align="right"><? echo number_format($tot_chemical_cost_amount,2); ?>&nbsp;</td>
                      
                        <td align="right"><? //echo number_format($tot_chemical_cost_amount/$tot_dyeing_qty,2,'.',''); ?>&nbsp;</td>
                       
                      </tr>
            </tbody>
		</table>
        </div>
        <?
		exit();
}
?>	
