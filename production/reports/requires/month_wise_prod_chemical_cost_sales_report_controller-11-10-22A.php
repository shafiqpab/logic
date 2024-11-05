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
if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0) $conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	else $conversion_date=change_date_format($data[1], "d-M-y", "-",1);

	$currency_rate=set_conversion_rate( $data[0], $conversion_date,$data[2] );
	echo "1"."_".$currency_rate;
	exit();
}
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_batch_no_search_list_view', 'search_div', 'dyeing_prod_focus_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" />
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
if($action=="report_generate") // Floor  Wise 
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	 	
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_working_name=str_replace("'","",$cbo_working_company_name);
		$txt_ref_no=str_replace("'","",$txt_ref_no);
		$batch_type=str_replace("'","",$cbo_batch_type);
		$cbo_value_with=str_replace("'","",$search_type);
		$txt_batch_no=str_replace("'","",$txt_batch_no);
		$from_date=str_replace("'","",$txt_date_from);
		$to_date=str_replace("'","",$txt_date_to);
	 
		$txt_ref_no=str_replace("'","",$txt_ref_no); 
		$cbo_year=str_replace("'","",$cbo_year_selection);
		//echo $cbo_value_with.'DDDDDDDDDD';
		 
		 
		
	if ($cbo_company_name==0) $company_id=""; else $company_id=" and a.company_id='$cbo_company_name'";
	if ($cbo_company_name==0) $company_idcond =""; else $company_idcond =" and b.company_id='$cbo_company_name'";
	if ($cbo_working_name==0) $company_idcond .=""; else $company_idcond .=" and b.working_company_id='$cbo_working_name'";
	
	if ($cbo_company_name==0) $company_idcond2 =""; else $company_idcond2 =" and a.company_id='$cbo_company_name'";
	if ($cbo_working_name==0) $company_idcond2 .=""; else $company_idcond2 .=" and a.service_company='$cbo_working_name'";
	
	//
	//echo $cbo_company_name.'='.$cbo_working_name;die;
	if($cbo_company_name) $company_ids=$cbo_company_name;
	else if($cbo_working_name) $company_ids=$cbo_working_name;
	
	if ($cbo_company_name==0 && $cbo_working_name==0) 
	{
	$w_company_idcond ="";$w_company_idcond2="";$w_company_idcond3="";
	}
	 else  {
		 if ($cbo_company_name==0 && $cbo_working_name>0)
		 {
		  	$w_company_idcond =" and c.company_id='$cbo_working_name'";
			$w_company_idcond2 =" and b.knit_dye_company='$cbo_working_name'";
			$w_company_idcond3 =" and b.working_company_id='$cbo_working_name'";
		 }
		 else  if ($cbo_company_name>0 && $cbo_working_name==0)
		 {
		  $w_company_idcond =" and c.company_id='$cbo_company_name'";
		  $w_company_idcond2 =" and b.knit_dye_company='$cbo_company_name'";
		  $w_company_idcond3 ="";
		 }
	 }
	 // echo  $w_company_idcond.'dd'.$company_idcond;die;
	
	

	if($db_type==0)
		{
			$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_year";
		}
		else if($db_type==2)
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
		
	if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	if($db_type==0) 
	{
	$from_date=change_date_format($from_date,'yyyy-mm-dd'); $to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
    if($db_type==2) 
	{
	$from_date=change_date_format($from_date,'','',1);  $to_date=change_date_format($to_date,'','',1);
	}
	//echo $from_date;
	
	
	$batch_description_arr=array();
	/* 
	if(str_replace("'","",$batch_id)!="") $batch_cond=" and b.id in(".str_replace("'","",$batch_id).")";
    else  */
	
	
	//echo $batch_cond; //die;
	//echo $batch_no; //die;
    	//echo $batch_no;
	//$batch_no=implode("','",array_unique(explode(",",$txt_batch_no)));
	
	if(str_replace("'","",$txt_batch_no)!="") $batch_cond=" and b.batch_no=trim('$txt_batch_no') "; 
	else $batch_cond="";
	//echo $batch_cond."shakil"; die;
	
	//
	/* and b.re_dyeing_from=0 13-11-16 according to siddique sir dicission when search buyer order or subcontract all batch appear */
	if($batch_type==0)
	$search_field_cond_batch="and b.entry_form in (0,36)";
	else if($batch_type==1)
	$search_field_cond_batch="and b.entry_form=0 ";
	else if($batch_type==2)
	$search_field_cond_batch="and b.entry_form=36";
	else if($batch_type==4)
	$search_field_cond_batch="and b.entry_form=136";
	else if($batch_type==5)
	$search_field_cond_batch="and b.batch_against=3";// sample
	else 
	$search_field_cond_batch="and b.entry_form in(0,36) and b.batch_against in(2) and  b.re_dyeing_from!=0 ";
	
	$search_field_cond_batch="";
	
	
	if($txt_ref_no!='')
	{
		
		if($batch_type==4) //Trim batch
		{
		 $sql_po="SELECT c.id as batch_id,a.style_ref_no,a.season_buyer_wise,b.id,b.po_number,b.file_no,b.grouping as ref_no,b.job_no_mst from wo_po_details_master a,wo_po_break_down b,pro_batch_create_mst c where a.id=b.job_id and b.job_no_mst=c.job_no  and   b.status_active=1 and b.is_deleted=0  $ref_cond ";
		}
		else
		{
			$sql_po="SELECT a.style_ref_no,a.season_buyer_wise,b.id,b.po_number,b.file_no,b.grouping as ref_no,b.job_no_mst from wo_po_details_master a,wo_po_break_down b  where a.id=b.job_id and   b.status_active=1 and b.is_deleted=0 $ref_cond ";
		}
		 //echo $sql_po;die; 
		$res_po=sql_select($sql_po);
		$all_po_id='';
		foreach($res_po as $row) //$job_no_arr
		{			
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
			
			$trim_batch_idArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
		}
		//echo $all_po_id;
		
		//
		

		if($all_po_id!="") $po_idd="and po_id in($all_po_id)";else $po_idd="and po_id in(0)";
		
		$poIds=chop($all_po_id,','); $po_cond_for_in="";
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" po_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
		}
		else
		{
			$po_cond_for_in=" and po_id in($poIds)";
			
		}
		
		if($batch_type==4) //Trim batch
		{
		 	$po_cond_for_in='';
			$po_cond_for_in=where_con_using_array($trim_batch_idArr,0,"mst_id");
			//echo "SELECT mst_id as batch_id from pro_batch_trims_dtls  where   status_active=1 and is_deleted=0 $po_cond_for_in";die;
			$sql_batch=sql_select("SELECT mst_id as batch_id from pro_batch_trims_dtls  where   status_active=1 and is_deleted=0 $po_cond_for_in");
		}
		else
		{
			$sql_batch=sql_select("SELECT mst_id as batch_id from pro_batch_create_dtls  where   status_active=1 and is_deleted=0 $po_cond_for_in");
		}
		 //echo "SELECT mst_id as batch_id from pro_batch_create_dtls  where   status_active=1 and is_deleted=0 $po_cond_for_in";die();
		 $all_batch_id='';
		foreach($sql_batch as $row) 
		{
			if($all_batch_id=="") $all_batch_id=$row[csf('batch_id')]; else $all_batch_id.=",".$row[csf('batch_id')];
		}
			$all_batch_ids=implode(",",array_unique(explode(",",$all_batch_id)));
		//echo $all_batch_ids; 
		$batch_ids_cond="";
		if($all_batch_ids!="") 
		{
			//echo $po_id=substr($po_id,0,-1);
			if($db_type==0) { $batch_ids_cond="and b.id in(".$all_batch_ids.")"; }
			else
			{
				$bat_id=array_unique(explode(",",$all_batch_ids));
				if(count($bat_id)>1000)
				{
					$batch_ids_cond="and (";
					$bat_id=array_chunk($bat_id,1000);
					$z=0;
					foreach($bat_id as $id)
					{
						$id=implode(",",$id);
						if($z==0) $batch_ids_cond.=" b.id in(".$id.")";
						else $batch_ids_cond.=" or b.id in(".$id.")";
						$z++;
					}
					$batch_ids_cond.=")";
				}
				else { 
				
				$batch_ids_cond=" and b.id in(".$all_batch_ids.")"; }
			}
		}
	}
	// print_r($batch_ids_cond);die;
	if($from_date!="" && $to_date!="")
	{
		$date_cond_samp="";
		if(str_replace("'","",$cbo_value_with)==2)
		{
			$date_cond_samp=" and b.batch_date between '".$from_date."' and '".$to_date."'";
		}
	}


	/* ================================================================================ /
	/								Main query start here								/
	/ ================================================================================ */
	$samp_non_booking=sql_select("SELECT b.id as batch_id,a.booking_no,a.entry_form_id,a.grouping,c.id as buyer_id,c.buyer_name 
	from  wo_non_ord_samp_booking_mst a, lib_buyer c,pro_batch_create_mst b where a.buyer_id=c.id and b.booking_no=a.booking_no and a.booking_type=4  $company_idcond $w_company_idcond3 $batch_cond  $date_cond $search_field_cond_batch $booking_no_cond $date_cond_samp $samp_ref_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	 
	$all_batch_id_samp="";
	foreach($samp_non_booking as $row)
	{
		$non_buyer_name_arr[$row[csf('booking_no')]]=$row[csf('buyer_name')];
		$non_buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
		$sample_booking_ref_no_arr[$row[csf('booking_no')]]=$row[csf('grouping')];
		if($txt_samp_ref_no!='')
		{
		if($all_batch_id_samp=="") $all_batch_id_samp=$row[csf('batch_id')]; else $all_batch_id_samp.=",".$row[csf('batch_id')];
		}
	}
	if($all_batch_id_samp!="") $all_batch_id_samp_cond="and b.id in($all_batch_id_samp)";else $all_batch_id_samp_cond="";
	//echo $all_batch_id_samp_cond.'X';
	
    if($from_date!="")
    {
		if(str_replace("'","",$cbo_value_with)==1)
		{
			$date_cond=" and a.process_end_date between '".$from_date."' and '".$to_date."' ";
		//and b.batch_against in(1,2,3)
			  $sql=sql_select("SELECT b.id,a.batch_no,a.fabric_type,a.floor_id,b.booking_without_order,b.job_no,b.extention_no,a.process_end_date as batch_date,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from
			from pro_fab_subprocess  a,pro_batch_create_mst b  where  a.batch_id=b.id 
			and a.entry_form in (35,38) and a.load_unload_id=2 and a.status_active=1 and a.is_deleted=0  $company_idcond2 $batch_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond $all_batch_id_samp_cond $floor_id_cond");
			 
						 
			
		}
		else if(str_replace("'","",$cbo_value_with)==2)
		{
			$date_cond=" and b.batch_date between '".$from_date."' and '".$to_date."'";
			$sql=sql_select("SELECT b.id,b.batch_no,b.booking_without_order,b.job_no,b.extention_no,b.color_range_id,b.batch_date,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from 
			from  pro_batch_create_mst b where   b.status_active=1 and b.is_deleted=0  $company_idcond $batch_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond $all_batch_id_samp_cond ");
			
		}
   	}
   	else
   	{
		 
		 
		$sql=sql_select("SELECT a.floor_id,a.process_end_date as batch_date,b.id,b.booking_without_order,b.job_no,b.extention_no,b.batch_no,b.color_range_id,b.batch_date,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from 
		from  pro_batch_create_mst b,pro_fab_subprocess a where  a.batch_id=b.id  and a.entry_form in (35,38) and a.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 $company_idcond2 $batch_cond $batch_ids_cond $search_field_cond_batch $booking_no_cond $all_batch_id_samp_cond $floor_id_cond");
		
		
	}
	 
	if(count($sql)==0)
    {
        ?>
        <div style="font-size:20px;color:red;text-align:center">Data not found!</div>
        <?
        die;
    }
  	// echo $sql;
  	// print_r($sql);die();
	
		
  	$reding_batch_id=$non_reding_batch_id=array();
    foreach($sql as $row)
	{
		$floor_id=$row[csf("floor_id")];
		$entry_formId=$row[csf("entry_form")];
		$batch_against_id=$row[csf("batch_against")];
		$batch_id_arr[]=$row[csf("id")];
		$batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
	 
 		if($batch_against_id==1 || $batch_against_id==3)
		{		
			 
			// $batch_id_arr[]=$row[csf("id")]; 
			$batch_description_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			$batch_description_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
			$batch_description_arr[$row[csf("id")]]['without_order']=$row[csf("booking_without_order")];
			$batch_description_arr[$row[csf("id")]]['batch_weight']+=$row[csf("batch_weight")];
			$batch_description_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];
			$batch_description_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			$batch_description_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			
			$batch_description_arr[$row[csf("id")]]['color_range']=$row[csf("color_range_id")];
			$batch_description_arr[$row[csf("id")]]['fabric_type']=$row[csf("fabric_type")];
			$batch_no_arr[]="'".$row[csf("batch_no")]."'";
			// $batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
			
			$batch_wise_arr[$row[csf("batch_no")]]['batch_id'].=$row[csf("id")].',';
			$batch_wise_arr[$row[csf("batch_no")]]['batch_no']=$row[csf("batch_no")];
			$batch_wise_arr[$row[csf("batch_no")]]['extention_no'].=$row[csf("extention_no")].',';
			$batch_ext_wise_arr[$row[csf("id")]]['extention_no']=$row[csf("extention_no")];
			
		}
		if($batch_against_id==2)
		{
			$batch_description_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			$batch_description_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
			$batch_description_arr[$row[csf("id")]]['without_order']=$row[csf("booking_without_order")];
			$batch_description_arr[$row[csf("id")]]['batch_weight']+=$row[csf("batch_weight")];
			$batch_description_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];
			$batch_description_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			$batch_description_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			
			$batch_description_arr[$row[csf("id")]]['color_range']=$row[csf("color_range_id")];
			$batch_description_arr[$row[csf("id")]]['fabric_type']=$row[csf("fabric_type")];
			$batch_no_arr[]="'".$row[csf("batch_no")]."'";
			// $batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
			
			$batch_wise_arr[$row[csf("batch_no")]]['batch_id'].=$row[csf("id")].',';
			$batch_wise_arr[$row[csf("batch_no")]]['batch_no']=$row[csf("batch_no")];
			$batch_wise_arr[$row[csf("batch_no")]]['extention_no'].=$row[csf("extention_no")].',';
			$batch_ext_wise_arr[$row[csf("id")]]['extention_no']=$row[csf("extention_no")];
			$batch_ext_wise_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			
 			$re_batch_wise_arr[$row[csf("batch_no")]]['batch_id'].=$row[csf("id")].',';
			
		}
		if($row[csf("batch_against")]==2)
		{
			/*if($row[csf("re_dyeing_from")]>0)
			{
				$reding_batch_id[$row[csf("re_dyeing_from")]]=$row[csf("re_dyeing_from")]; 
			}
			else
			{
				$reding_batch_id[$row[csf("id")]]=$row[csf("id")]; 
			}*/
			$reding_batch_id[$row[csf("id")]]=$row[csf("id")];
		}
		else
		{
			$non_reding_batch_id[$row[csf("id")]]=$row[csf("id")];
		}
	}
	
 	 
	      $sql_product=("SELECT a.floor_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,b.job_no,b.extention_no,b.batch_no,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.batch_qnty
		from  pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_create_dtls c where  a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id   and a.entry_form in (35)  and b.status_active=1 and a.load_unload_id=2 and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0 $company_idcond2 $batch_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond $all_batch_id_samp_cond $floor_id_cond
		union
 SELECT a.floor_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,
b.job_no,b.extention_no,b.batch_no,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,
b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.trims_wgt_qnty as batch_qnty 
from pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_trims_dtls c 
where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id 
and a.entry_form in (35,38)  and b.status_active=1 and a.load_unload_id=2
and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0  $company_idcond2 $batch_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond $all_batch_id_samp_cond $floor_id_cond order by  batch_date asc");
	
		$sql_prod=sql_select($sql_product);$tot_self_trim_qty=0;$tot_self_trimqty=0;
		$production_qty=0;$sub_trims_wgt_check_array=array();$jj=1;$self_trims_wgt_check_array=array();$jk=1;
		 foreach($sql_prod as $row)
		 {
			//$prod_arr[$row[csf("batch_date")]][$row[csf("floor_id")]]['qty']+=$row[csf("production_qty")];
			//
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
			$batch_color_range_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
			
			$batch_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			$batch_data_arr[$row[csf("id")]]['batch_against']=$row[csf("batch_against")];
		
			$batch_dying_floor_arr[$row[csf("floor_id")]]=$row[csf("floor_id")];
			//$prod_date_arr[$row[csf("batch_date")]]=$row[csf("batch_date")];
		 if($row[csf("entry_form")]==36 && $row[csf("batch_against")]==1 && $row[csf("result")]==1)
		 {
			
 			 $batch_noSub=$row[csf('id')];
			if (!in_array($batch_noSub,$sub_trims_wgt_check_array))
			{ $jj++;

				 $sub_trims_wgt_check_array[]=$batch_noSub;
				 $tot_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				 $tot_trim_qty=0;
			}
 			//$total_trims_weight_qty+=$tot_trim_qty;
			
			 $subcon_prod_arr[$row[csf("id")]]['production_qty_subcontact']+=$row[csf("batch_qnty")];
			 if($total_trims_weight_qty>0)
			 {
			 $subcon_prod_arr[$row[csf("id")]]['subcon_trimsWgt']=$tot_trim_qty;
			 }
		 }
		 
		 if($row[csf("entry_form")]==0 && $row[csf("batch_against")]==1 && $row[csf("result")]==1)
		 {
 			 
			 $batch_noSelf=$row[csf('id')];
			if (!in_array($batch_noSelf,$self_trims_wgt_check_array))
			{ $jk++;

				 $sub_trims_wgt_check_array[]=$batch_noSelf;
				 $tot_self_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				 $tot_self_trim_qty=0;
			}
		 
			// $subcon_prod_arr[$row[csf("id")]]['production_qty_subcontact']+=$row[csf("batch_qnty")];
			 if($tot_self_trim_qty>0)
			 {
			 $selfTrim_prod_arr[$row[csf("id")]]['subcon_trimsWgt']=$tot_self_trim_qty;
			  $tot_self_trimqty+=$tot_self_trim_qty;
			 }
		 }
		 
			$prod_arr[$row[csf("id")]]['prod_qty']+=$row[csf("batch_qnty")];
			$prod_arr[$row[csf("id")]]['batch_qty']+=$row[csf("batch_qnty")];
		 
			$prod_arr[$row[csf("id")]]['floor_id']=$row[csf("floor_id")];
			$prod_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			$prod_result_arr[$row[csf("id")]]['result']=$row[csf("result")];
			 
			$production_qty+=$row[csf("batch_qnty")];
			 
		 }
		// echo $tot_self_trimqty.'T';
		 $batch_cond_for_in=where_con_using_array($issue_single_batch_arr,0,"b.id");
		 
		 //$po_data_subcon=sql_select("SELECT a.mst_id,b.order_no,c.subcon_job, c.party_id,b.cust_style_ref from pro_batch_create_dtls a, subcon_ord_dtls b,
	//subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0  $batch_id_conds");
		 
	     $sql_sub_product=("SELECT a.floor_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,b.job_no,b.extention_no,b.batch_no,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.batch_qnty
		from  pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_create_dtls c,subcon_ord_dtls d,subcon_ord_mst e where  a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and c.po_id=d.id and  d.job_no_mst=e.subcon_job and a.entry_form in (38) and e.party_id not in(426,439,444,458,425,427,428,443,392,564,565,563) and b.status_active=1 and a.load_unload_id=2 and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0 and d.status_active=1 and e.status_active=1 $company_idcond2 $batch_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond $all_batch_id_samp_cond $floor_id_cond order by batch_date ");//die;
		
		$sql_sub_prod=sql_select($sql_sub_product);//$tot_self_trim_qty=0;$tot_self_trimqty=0;
		$subcon_trims_wgt_check_array=array();$jjj=1;$jkk=1;
		 foreach($sql_sub_prod as $row)
		 {
			//$prod_arr[$row[csf("batch_date")]][$row[csf("floor_id")]]['qty']+=$row[csf("production_qty")];
			//
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
			$batch_color_range_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
			$batch_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			$batch_data_arr[$row[csf("id")]]['batch_against']=$row[csf("batch_against")];
		
			$batch_dying_floor_arr[$row[csf("floor_id")]]=$row[csf("floor_id")];
			//$prod_date_arr[$row[csf("batch_date")]]=$row[csf("batch_date")];
		 if($row[csf("entry_form")]==36 && $row[csf("batch_against")]==1 && $row[csf("result")]==1)
		 {
			 $batch_noSubcon=$row[csf('id')];
			if (!in_array($batch_noSub,$subcon_trims_wgt_check_array))
			{ $jjj++;

				 $subcon_trims_wgt_check_array[]=$batch_noSubcon;
				 $tot_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				 $tot_trim_qty=0;
			}
			//$total_trims_weight_qty+=$tot_trim_qty;
			 $subcon_prod_arr[$row[csf("id")]]['production_qty_subcontact']+=$row[csf("batch_qnty")];
			 if($total_trims_weight_qty>0)
			 {
			 $subcon_prod_arr[$row[csf("id")]]['subcon_trimsWgt']=$tot_trim_qty;
			 }
		 }
		 //unset($sql_sub_prod);
		 
		   $prod_arr[$row[csf("id")]]['prod_qty']+=$row[csf("batch_qnty")];
			$prod_arr[$row[csf("id")]]['batch_qty']+=$row[csf("batch_qnty")];
		 
			$prod_arr[$row[csf("id")]]['floor_id']=$row[csf("floor_id")];
			$prod_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			$prod_result_arr[$row[csf("id")]]['result']=$row[csf("result")];
			 
			$production_qty+=$row[csf("batch_qnty")];
			 
		 }
		// echo $tot_self_trimqty.'T';
		 $batch_cond_for_in=where_con_using_array($issue_single_batch_arr,0,"b.id");
		  
		 
			$sql_qty = " (select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no,b.total_trims_weight,
			sum(case when a.service_source=1 then  b.batch_weight end) as batch_weight,
			SUM(case when a.service_source=1 and b.batch_against!=3 and c.is_sales!=1 then c.batch_qnty end) AS production_qty_inhouse,
			SUM(case when a.service_source=3 then c.batch_qnty end) AS production_qty_outbound,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=1 and b.is_sales!=1  then c.batch_qnty end) AS prod_qty_sample_without_order,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=0 and c.is_sales!=1  then c.batch_qnty end) AS prod_qty_sample_with_order,
			SUM(case WHEN c.is_sales=1 then c.batch_qnty end) AS fabric_sales_order_qty
			from pro_batch_create_dtls c, wo_po_break_down e, wo_po_details_master d, pro_fab_subprocess a,
			 lib_machine_name g, pro_batch_create_mst b 
			where a.batch_id=b.id and b.entry_form=0 and g.id=a.machine_id and b.id=c.mst_id and a.batch_id=c.mst_id and a.entry_form=35 and a.load_unload_id=2 and b.batch_against in(1)  and c.po_id=e.id and d.job_no=e.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  c.status_active=1 and c.is_deleted=0  and a.result=1  $company_idcond2 $batch_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond $all_batch_id_samp_cond $floor_id_cond
			group by b.working_company_id,b.company_id,b.color_range_id,b.id,b.batch_no,b.total_trims_weight)
			union  
			( select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no,b.total_trims_weight,
			sum(case when a.service_source=1 then  b.batch_weight end) as batch_weight,
			SUM(case when a.service_source=1 and b.batch_against in(1) and c.is_sales!=1  then c.batch_qnty end) AS production_qty_inhouse,
			SUM(case when a.service_source=3 then c.batch_qnty end) AS production_qty_outbound,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=1 and c.is_sales!=1 then c.batch_qnty end) AS prod_qty_sample_without_order,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=0 and c.is_sales!=1 then c.batch_qnty end) AS prod_qty_sample_with_order,
			SUM(case WHEN c.is_sales=1 then c.batch_qnty end) AS fabric_sales_order_qty
			from pro_batch_create_dtls c, pro_batch_create_mst b, pro_fab_subprocess a, lib_machine_name g,wo_non_ord_samp_booking_mst h
			where  h.booking_no=b.booking_no and a.batch_id=b.id and a.batch_id=c.mst_id and b.entry_form=0 and g.id=a.machine_id and b.id=c.mst_id and a.entry_form=35 and a.load_unload_id=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  c.status_active=1 and c.is_deleted=0  and a.result=1  $company_idcond2 $batch_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond $all_batch_id_samp_cond $floor_id_cond
			group by b.id,b.color_range_id,b.working_company_id,b.company_id,b.batch_no,b.total_trims_weight ) 
			union
			(select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no, SUM(c.trims_wgt_qnty) AS total_trims_weight, 0 as batch_weight, 0 AS production_qty_inhouse, 0 AS production_qty_outbound, 0 AS prod_qty_sample_without_order, 0 AS prod_qty_sample_with_order, 0 AS fabric_sales_order_qty
			from pro_batch_create_mst b,pro_batch_trims_dtls c, wo_po_details_master d, pro_fab_subprocess a, lib_machine_name g 
			where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and g.id=a.machine_id and d.job_no=b.job_no and b.entry_form=136 and a.entry_form=35 and a.load_unload_id=2 and b.batch_against in(1) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.result=1 and c.status_active=1 and c.is_deleted=0  $company_idcond2 $batch_cond $batch_ids_cond $date_cond $search_field_cond_batch $booking_no_cond $all_batch_id_samp_cond $floor_id_cond 
			GROUP BY b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no) ";

			  //echo $sql_qty;die;
			$sql_result=sql_select( $sql_qty);

		//$fabric_sales_order_qty=0;
		$total_trims_weight_qty=0;$tttm=0;
		$j=1;$self_trims_wgt_check_array=array();
		foreach($sql_result as $row)
		{
			//$production_qty_inhouse+=$row[csf('production_qty_inhouse')];
			//$production_qty_outbound+=$row[csf('production_qty_outbound')];
			//$prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
			//$prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
			//$fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
			$batch_noSelf=$row[csf('id')];
			if (!in_array($batch_noSelf,$self_trims_wgt_check_array))
			{ 
				$j++;

				$self_trims_wgt_check_array[]=$batch_noSelf;
				$tot_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				$tot_trim_qty=0;
			}
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
			$batch_color_range_arr[$row[csf('id')]]['color_range_id']=$row[csf("color_range_id")]; 
			
			// $prod_arr[$row[csf("id")]]['batch_date']=$row[csf("process_end_date")];
			
			$total_trims_weight_qty+=$tot_trim_qty;
			$tot_sample=$row[csf("prod_qty_sample_without_order")]+$row[csf('prod_qty_sample_with_order')];
			$prod_arr2[$row[csf("id")]]['self_batch_qty']+=$row[csf("production_qty_inhouse")]+$row[csf('production_qty_outbound')];
			if($tot_sample>0)
			{
				// echo $tot_sample.',';
			$prod_arr2[$row[csf("id")]]['sample_batch_qty']+=$tot_sample;
			}
			$prod_arr2[$row[csf("id")]]['fabric_sales_order_qty']+=$row[csf("fabric_sales_order_qty")];
			if($tot_trim_qty>0)
			{
			$prod_arr2[$row[csf("id")]]['tot_trim_qty']+=$tot_trim_qty;
			$prod_TrimsArr[$row[csf("id")]]=$tot_trim_qty;
			}
			
			$tot_Qty+=$row[csf("production_qty_inhouse")]+$row[csf('production_qty_outbound')]+$tot_trim_qty+$tot_sample;
			
		//$tttm+=$tot_trim_qty;  
		}
		  //echo $tttm;die;
	//	echo array_sum($prod_arr3);
		
	       //echo $total_trims_weight_qty.'D'; 
	  //  echo "<pre>"; print_r($prod_arr3);   

//die;

	//	********************************************************************************************************************************
     $batch_id_sql=implode(",",$batch_id_arr);
    if($batch_id_sql=="") $batch_id_sql=0;
	//echo  $batch_id_sql;
  	if($batch_type==3)
	{
		//$re_dying_cond2=" and b.id in(".$batch_id_sql.")";
		if(count($batch_id_arr)>0)
		{
			$re_dying_cond2=" and (";
			if(count($batch_id_arr)>999 && $db_type==2)
			{
				$prod_id_chank=array_chunk($batch_id_arr,999);
				foreach($prod_id_chank as $batch_id)
				{
					$re_dying_cond2.=" b.id in(".implode(",",$batch_id).") or";
				}
				$re_dying_cond2=chop($re_dying_cond2,"or");
			}
			else
			{
				//die("with naz");
				$re_dying_cond2.=" b.id in(".implode(",",$batch_id_arr).")";
			}
			$re_dying_cond2.=")";
		}
	}
	else
	{
		//$re_dying_cond=" and b.re_dyeing_from in(".$batch_id_sql.")";	
		//$pord_ids = explode(",",$txt_product_id);
		//echo count($pord_ids);die;
		if(count($batch_id_arr)>0)
		{
			$re_dying_cond=" and (";
			if(count($batch_id_arr)>999 && $db_type==2)
			{
				$prod_id_chank=array_chunk($batch_id_arr,999);
				foreach($prod_id_chank as $batch_id)
				{
					$re_dying_cond.=" b.re_dyeing_from in(".implode(",",$batch_id).") or";
				}
				$re_dying_cond=chop($re_dying_cond,"or");
			}
			else
			{
				//die("with naz");
				$re_dying_cond.=" b.re_dyeing_from in(".implode(",",$batch_id_arr).")";
			}
			$re_dying_cond.=")";
		}
	}

	$date_cond2=" and a.process_end_date between '".$from_date."' and '".$to_date."' ";

	// =============================================================
	$batch_all_id_array=array();
	$batch_id_conds = ($batch_type==3) ? str_replace("b.id", "a.batch_id", $re_dying_cond2) : str_replace("b.re_dyeing_from", "a.batch_id", $re_dying_cond);
	  $sql = "SELECT a.ID,b.mst_id as REQ_ID,a.batch_id as BATCH_ID,a.BATCH_QTY,a.BATCH_QTY,a.NEW_BATCH_WEIGHT,a.ENTRY_FORM,c.batch_id as RE_BATCH_ID from pro_recipe_entry_mst a, dyes_chem_requ_recipe_att b,dyes_chem_issue_requ_mst c where b.recipe_id=a.id  and c.id=b.mst_id $batch_id_conds and a.status_active=1 and a.is_deleted=0 and b.status_active=1 ";
	// echo $sql;die; 
	$res = sql_select($sql);
	$req_id_array = array();
	foreach ($res as $val) 
	{
		//$req_id_array2[$val['REQ_ID']] .= $val['REQ_ID'].',';
		$req_id_array[$val['REQ_ID']] = $val['REQ_ID'];
		//$batch_req_id_array[$val['BATCH_ID']].= $val['REQ_ID'].',';
		$batch_req_id_array2[$val['BATCH_ID']]= $val['REQ_ID'];
		$batch_all_id_array[$val['BATCH_ID']]= $val['BATCH_ID'];
		//if($batchWgtChkk[$val['REQ_ID']]=='')
		//{
			if($val['ENTRY_FORM']==60) //Adding Topping
			{
				$batch_wgt=$val['NEW_BATCH_WEIGHT'];
			}
			else
			{
				$batch_wgt=$val['BATCH_QTY'];
			}
		//$batch_wgt_array[$val['REQ_ID']] += $batch_wgt;
		$batch_wgt_array[$val['BATCH_ID']] = $batch_wgt;
		$batchWgtChkk[$val['REQ_ID']].=$val['BATCH_ID'].',';
		
		$batchIdChkkDtls[$val['REQ_ID']]=$val['BATCH_ID'];
		//}
		
	}
	unset($res);
	// print_r($req_id_array2);die;
	// $requisition_ids = implode(",", $req_id_array);
	
	
	if(count($req_id_array))
	{
		$requisition_id_cond=where_con_using_array($req_id_array,0,"a.requisition_no");
	}

	$issue_sql = sql_select("SELECT a.MST_ID ,a.REQUISITION_NO,b.BATCH_NO,a.ITEM_CATEGORY,a.PROD_ID,a.CONS_QUANTITY,a.CONS_AMOUNT from inv_transaction a,inv_issue_master b where a.mst_id=b.id and b.entry_form=5 and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.transaction_type=2 $requisition_id_cond   ");//$w_company_idcond2
	//echo "SELECT a.MST_ID ,a.REQUISITION_NO,b.BATCH_NO,a.ITEM_CATEGORY,a.PROD_ID,a.CONS_QUANTITY,a.CONS_AMOUNT from inv_transaction a,inv_issue_master b where a.mst_id=b.id and b.entry_form=5 and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.transaction_type=2 $requisition_id_cond   ";die;
	   
	 
	$issue_id_arr = array();$tot_issueQty=0;  

	foreach ($issue_sql as $val) 
	{
		 
		$batchArr=array_filter(array_unique(explode(",",$val['BATCH_NO'])));
		foreach($batchArr as $bid)
		{
			$issue_item_cat_arr[$bid][$val['PROD_ID']][$val['ITEM_CATEGORY']]=$val['ITEM_CATEGORY'];
			if($bid!='')
			{
			$batch_all_id_array[$bid]= $bid;
			$batch_req_id_array[$bid].= $val['REQUISITION_NO'].',';
			}
		}
			$issue_item_cat_cost_arr[$val['REQUISITION_NO']][$val['PROD_ID']][$val['ITEM_CATEGORY']]=$val['CONS_QUANTITY'];
			$issue_item_cat_cost_dtls_arr[$val['REQUISITION_NO']][$val['ITEM_CATEGORY']]+=$val['CONS_QUANTITY'];
			
			$issue_item_cat_amt_dtls_arr[$val['REQUISITION_NO']][$val['ITEM_CATEGORY']]+=$val['CONS_AMOUNT'];
		
		 $ReqBatch_id_arr[$val['REQUISITION_NO']] = $val['BATCH_NO'];
		$issue_id_arr[$val['MST_ID']] = $val['MST_ID'];
		$tot_issueQty+=$val['CONS_QUANTITY'];
		
	}
	// echo count($batch_all_id_array);die;
	if(count($batch_all_id_array))
	{
		$batch_ids_cond=where_con_using_array($batch_all_id_array,0,"b.id");
	}
	
	$batch_fabric_sql=sql_select("SELECT b.id,b.batch_no,b.batch_weight,b.total_trims_weight, b.batch_against
	from pro_recipe_entry_mst  a,pro_batch_create_mst b  where  a.batch_id=b.id 
	 and a.status_active=1 and a.is_deleted=0 $batch_ids_cond group by  b.id,b.batch_no,b.batch_weight,b.total_trims_weight, b.batch_against" );
	
	
	 foreach ($batch_fabric_sql as $row) 
	{
		//$batch_req_id=$batch_req_id_array2[$row[csf("id")]];
		//echo $batch_req_id.'d';
		$batch_wgt_arr[$row[csf("id")]]['batchWgt']=$row[csf("batch_weight")];
		if($row[csf("total_trims_weight")]>0)
		{
			$batch_wgt_arr[$row[csf("id")]]['trims_weight']=$row[csf("total_trims_weight")]; 
			
			//echo $row[csf("total_trims_weight")].','; 
		}
	}
	//========+++++=Re Calculation++++++++++++========
	
	 
	 
	//echo $tot_issueQty.'D';
	// echo "<pre>";print_r($batch_req_id_array);die;
	// =====================================================================================
	$batch_id_conds = ($batch_type==3) ? str_replace("b.id", "b.id", $re_dying_cond2) : str_replace("b.re_dyeing_from", "b.id", $re_dying_cond);
	$fabric_sql=sql_select("SELECT b.id,a.batch_no,a.fabric_type,b.booking_without_order,a.process_end_date as batch_date,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from
	from pro_fab_subprocess  a,pro_batch_create_mst b  where  a.batch_id=b.id 
	and a.entry_form in (35,38) and a.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 $company_idcond $batch_id_conds");
	 	
  	$fabric_type_arr=array();
    foreach($fabric_sql as $row)
	{
	 	$fabric_type_arr[$row[csf("id")]]['fabric_type']=$row[csf("fabric_type")];
	}

	// ========================================================================================
//$issue_single_batch_arr[$row[csf("id")]]
$batch_idCond1 = where_con_using_array($issue_single_batch_arr,0,"a.batch_id");
	$floor_cond_arr=array();
	$batch_id_conds = ($batch_type==3) ? str_replace("b.id", "a.batch_id", $re_dying_cond2) : str_replace("b.re_dyeing_from", "a.batch_id", $re_dying_cond);
	if(!empty($location_id))
	{
		if(!empty($floor_id))
		{
			$sql="SELECT  a.batch_id ,b.floor_name from   pro_fab_subprocess a, lib_prod_floor b  where a.floor_id=b.id and a.entry_form in(35,38) and b.status_active=1 and b.is_deleted=0  and b.location_id=$location_id and b.production_process=3 and b.id=$floor_id $batch_idCond1";
			
			$sql_floor=sql_select($sql);
			foreach ($sql_floor as $row) 
			{
				
				array_push($floor_cond_arr, $row[csf('batch_id')]);
			}
		}
		else
		{
			$sql="SELECT  a.batch_id ,b.floor_name from   pro_fab_subprocess a, lib_prod_floor b  where a.floor_id=b.id and a.entry_form in(35,38) and b.status_active=1 and b.is_deleted=0  and b.location_id=$location_id and b.production_process=3 $batch_idCond1";
			
			$sql_floor=sql_select($sql);
			foreach ($sql_floor as $row) {
				
				array_push($floor_cond_arr, $row[csf('batch_id')]);
			}
		}
		$floor_cond_arr=array_unique($floor_cond_arr);
	}
	 // print_r($floor_cond_arr);die();

	// ==========================================================================

	$batch_id_conds = ($batch_type==3) ? str_replace("b.id", "a.batch_id", $re_dying_cond2) : str_replace("b.re_dyeing_from", "a.batch_id", $re_dying_cond);
  //  $floor_arr=return_library_array("SELECT  a.batch_id ,b.floor_name from   pro_fab_subprocess a, lib_prod_floor b  where a.floor_id=b.id and a.entry_form=35 and b.status_active=1 and b.is_deleted=0 $batch_id_conds", "batch_id", "floor_name" );
	//echo "SELECT  a.batch_id ,b.floor_name from   pro_fab_subprocess a, lib_prod_floor b  where a.floor_id=b.id and a.entry_form=35 and b.status_active=1 and b.is_deleted=0 $batch_id_conds";die;
	
	//$floor_arr=return_library_array("SELECT  b.id ,b.floor_name from   pro_fab_subprocess a, lib_prod_floor b  where a.floor_id=b.id and a.entry_form=35 and b.status_active=1 and b.is_deleted=0 $batch_id_conds", "id", "floor_name" );
		
	//$floor_arr=return_library_array("SELECT  b.id ,b.floor_name from    lib_prod_floor b  where  b.status_active=1 and b.is_deleted=0 ", "id", "floor_name" );
	
	//echo "SELECT  b.id ,b.floor_name from   pro_fab_subprocess a, lib_prod_floor b  where a.floor_id=b.id and a.entry_form=35 and b.status_active=1 and b.is_deleted=0 $batch_id_conds";

	//$machine_name_arr=return_library_array("SELECT  a.batch_id ,b.machine_no from   pro_fab_subprocess a, lib_machine_name b  where a.machine_id=b.id and a.entry_form=35 and  b.status_active=1 and b.is_deleted=0 $batch_id_conds ", "batch_id", "machine_no" );

	$batch_id_conds = ($batch_type==3) ? str_replace("b.id", "c.id", $re_dying_cond2) : str_replace("b.re_dyeing_from", "c.id", $re_dying_cond);
	$sql_book=sql_select("SELECT a.booking_no,a.po_break_down_id,b.id as buyer_id, b.buyer_name from  wo_booking_mst a, lib_buyer b,pro_batch_create_mst c where a.buyer_id=b.id and a.id=c.booking_no_id $batch_id_conds");
	//echo "SELECT a.booking_no,a.po_break_down_id,b.id as buyer_id, b.buyer_name from  wo_booking_mst a, lib_buyer b,pro_batch_create_mst c where a.buyer_id=b.id and a.id=c.booking_no_id $batch_id_conds";die;
	//echo "SELECT a.booking_no,a.po_break_down_id,b.id as buyer_id, b.buyer_name from  wo_booking_mst a, lib_buyer b,pro_batch_create_mst c where a.buyer_id=b.id and a.id=c.booking_no_id $batch_id_conds";die;
	$po_id_arr = array();
	foreach($sql_book as $row)
	{
		$buyer_name_arr[$row[csf('booking_no')]]=$row[csf('buyer_name')];
		$buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
		
		if($row[csf('po_break_down_id')])
		{
		$po_id_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		$po_break_down_id_arr[$row[csf('booking_no')]]=$row[csf('po_break_down_id')];
		}
	}

	// =========================================================
	// $poIdss = implode(",", $po_id_arr);
	if(count($po_id_arr))
	{
		$po_id_cond = where_con_using_array($po_id_arr,0,"b.id");
	}
	

	// echo $poIdCond;die();
	$sql_po="SELECT a.style_ref_no,a.buyer_name,a.season_buyer_wise,b.id,b.po_number,b.file_no,b.grouping as ref_no,b.job_no_mst from wo_po_details_master a,wo_po_break_down b  where a.id=b.job_id and   b.status_active=1 and b.is_deleted=0 $file_cond $ref_cond $job_cond $season_cond $po_cond $buyer_id_cond $po_id_cond";
	// echo $sql_po;die(); 
	$res_po=sql_select($sql_po);
	$all_po_id='';
	foreach($res_po as $row) //$job_no_arr
	{
		$po_number_arr[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$po_number_arr[$row[csf('id')]]['ref_no']=$row[csf('ref_no')];
		$po_number_arr[$row[csf('id')]]['file_no']=$row[csf('file_no')]; 
		$po_number_arr[$row[csf('id')]]['season']=$row[csf('season_buyer_wise')]; 
		$po_number_arr[$row[csf('id')]]['job']=$row[csf('job_no_mst')];
		
		$job_no_arr[$row[csf('id')]]=$row[csf('job_no_mst')];
		$style_library[$row[csf('job_no_mst')]]=$row[csf('style_ref_no')];
		
		$trim_batch_job_arr[$row[csf('job_no_mst')]]['buyer']=$row[csf('buyer_name')];
		$trim_batch_job_arr[$row[csf('job_no_mst')]]['season']=$row[csf('season_buyer_wise')];
		$trim_batch_job_arr[$row[csf('job_no_mst')]]['style']=$row[csf('style_ref_no')];
		$trim_batch_job_arr[$row[csf('job_no_mst')]]['po_number'].=$row[csf('po_number')].',';
		$trim_batch_job_arr[$row[csf('job_no_mst')]]['ref_no'].=$row[csf('ref_no')].',';
		$trim_batch_job_arr[$row[csf('job_no_mst')]]['file_no'].=$row[csf('file_no')].',';
	}

	
    //$buyer_library=return_library_array( "SELECT id,buyer_name from   lib_buyer", "id","buyer_name" );
   // $style_library=return_library_array( "SELECT job_no,style_ref_no from   wo_po_details_master", "job_no","style_ref_no" );

    $batch_id_conds = ($batch_type==3) ? str_replace("b.id", "a.mst_id", $re_dying_cond2) : str_replace("b.re_dyeing_from", "a.mst_id", $re_dying_cond);
	$po_data_subcon=sql_select("SELECT a.mst_id,b.order_no,c.subcon_job, c.party_id,b.cust_style_ref from pro_batch_create_dtls a, subcon_ord_dtls b,
	subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0  $batch_id_conds");
	
	$sub_con_arr=array();
	foreach($po_data_subcon as $row)
	{
		if($sub_con_arr[$row[csf('mst_id')]][cust_style_ref]!="")
		{
			$sub_con_arr[$row[csf('mst_id')]][cust_style_ref].=",".$row[csf('cust_style_ref')];
		}
		else
		{
			$sub_con_arr[$row[csf('mst_id')]][cust_style_ref]=$row[csf('cust_style_ref')];	
		}
		
		if($sub_con_arr[$row[csf('mst_id')]][order_no]!="")
		{
			$sub_con_arr[$row[csf('mst_id')]][order_no].=",".$row[csf('order_no')];
		}
		else
		{
			$sub_con_arr[$row[csf('mst_id')]][order_no]=$row[csf('order_no')];	
		}
		
		if($sub_con_arr[$row[csf('mst_id')]][subcon_job]!="")
		{
			$sub_con_arr[$row[csf('mst_id')]][subcon_job].=",".$row[csf('subcon_job')];
		}
		else
		{
			$sub_con_arr[$row[csf('mst_id')]][subcon_job]=$row[csf('subcon_job')];	
		}
		if($sub_con_arr[$row[csf('mst_id')]][party_id]!="")
		{
			$sub_con_arr[$row[csf('mst_id')]][party_id].=",".$buyer_library[$row[csf('party_id')]];
			$sub_con_id_arr[$row[csf('mst_id')]][party_id].=",".$row[csf('party_id')];
		}
		else
		{
			$sub_con_arr[$row[csf('mst_id')]][party_id]=$buyer_library[$row[csf('party_id')]];
			$sub_con_id_arr[$row[csf('mst_id')]][party_id]=$row[csf('party_id')];	
		}
	}
	//print_r($sub_con_arr[646]);die;
	// ==================================================================================

	if($db_type==0)
	{
		if($batch_type==3)
		{
		 $sql_redying=sql_select("SELECT b.id as id,group_concat(b.batch_date)  as batch_date,group_concat(b.extention_no)  as extention_no,b.re_dyeing_from from  pro_batch_create_mst b where b.re_dyeing_from!=0 $company_idcond $re_dying_cond2 and b.status_active=1 and b.is_deleted=0 group by  b.id");
		}
		else
		{
			 $sql_redying=sql_select("SELECT group_concat(b.id) as id,group_concat(b.batch_date)  as batch_date,group_concat(b.extention_no)  as extention_no,b.re_dyeing_from from  pro_batch_create_mst b where b.re_dyeing_from!=0  $company_idcond $re_dying_cond and b.status_active=1 and b.is_deleted=0 group by  b.re_dyeing_from");	
		}
	}
	else
	{
	
		if($batch_type==3)
		{	
			 $sql_redying=sql_select("SELECT b.id as id,listagg((b.batch_date),',') within group (order by b.batch_date) as batch_date,listagg((b.extention_no),',') within group (order by b.extention_no) as extention_no
			  from  pro_batch_create_mst  b 
			  where b.re_dyeing_from!=0 $company_idcond $re_dying_cond2 and b.status_active=1 and b.is_deleted=0  group by  b.id");
		}
		else
		{
			 $sql_redying=sql_select("SELECT listagg((b.id),',') within group (order by b.id) as id,listagg((b.batch_date),',') within group (order by b.batch_date) as batch_date,listagg((b.extention_no),',') within group (order by b.extention_no) as extention_no,b.re_dyeing_from 
			 from  pro_batch_create_mst b 
			 where b.re_dyeing_from!=0   $company_idcond $re_dying_cond and b.status_active=1 and b.is_deleted=0  group by  b.re_dyeing_from");
			// echo "select listagg((b.id),',') within group (order by b.id) as id,listagg((b.batch_date),',') within group (order by b.batch_date) as batch_date,listagg((b.extention_no),',') within group (order by b.extention_no) as extention_no,b.re_dyeing_from 
			// from  pro_batch_create_mst b 
			// where b.re_dyeing_from!=0   $company_idcond $re_dying_cond and b.status_active=1 and b.is_deleted=0  group by  b.re_dyeing_from";
		}
	}
	
   	//and b.batch_against=2
    $redying_details_arr=array();
    foreach($sql_redying as $re_row)
    {
		if($batch_type==3)
		{
			$redying_details_arr[$re_row[csf("id")]]['id']=$re_row[csf("id")];
			$redying_details_arr[$re_row[csf("id")]]['batch_date']=$re_row[csf("batch_date")];
			$redying_details_arr[$re_row[csf("id")]]['extention_no']=$re_row[csf("extention_no")];
		}
		else
		{
			$redying_details_arr[$re_row[csf("re_dyeing_from")]]['id']=$re_row[csf("id")];
			$redying_details_arr[$re_row[csf("re_dyeing_from")]]['batch_date']=$re_row[csf("batch_date")];
			$redying_details_arr[$re_row[csf("re_dyeing_from")]]['extention_no']=$re_row[csf("extention_no")];
		}
    }
	
	//echo "<pre>"; print_r($redying_details_arr);die;
	if($batch_type==3)
	{
		//$all_dying_cond2=" and b.id in(".$batch_id_sql.")";
		if(count($batch_id_arr)>0)
		{
			$all_dying_cond2=" and (";
			if(count($batch_id_arr)>999 && $db_type==2)
			{
				$prod_id_chank=array_chunk($batch_id_arr,999);
				foreach($prod_id_chank as $batch_id)
				{
					$all_dying_cond2.=" b.id in(".implode(",",$batch_id).") or";
				}
				$all_dying_cond2=chop($all_dying_cond2,"or");
			}
			else
			{
				//die("with naz");
				$all_dying_cond2.=" b.id in(".implode(",",$batch_id_arr).")";
			}
			$all_dying_cond2.=")";
		}
	}
	else
	{
		if(count($batch_id_arr)>0)
		{
			$all_dying_cond=" and (";
			if(count($batch_id_arr)>999 && $db_type==2)
			{
				$prod_id_chank=array_chunk($batch_id_arr,999);
				foreach($prod_id_chank as $batch_id)
				{
					$all_dying_cond.=" b.id in(".implode(",",$batch_id).") or";
				}
				$all_dying_cond=chop($all_dying_cond,"or");
			}
			else
			{
				//die("with naz");
				$all_dying_cond.=" b.id in(".implode(",",$batch_id_arr).")";
			}
			$all_dying_cond.=")";
		}
	}
	
	if($batch_type==3)
	{
		$result_sql=sql_select("SELECT a.result,a.batch_id 
		from pro_fab_subprocess a, pro_batch_create_mst b where b.id=a.batch_id and a.entry_form in (35,38) and a.load_unload_id=2   $company_idcond $all_dying_cond2 and a.status_active=1 and a.is_deleted=0");	
	}
	else
	{
		$result_sql=sql_select("SELECT a.result,a.batch_id 
		from pro_fab_subprocess a, pro_batch_create_mst b where b.id=a.batch_id and a.entry_form in (35,38) and a.load_unload_id=2  $company_idcond $all_dying_cond and a.status_active=1 and a.is_deleted=0");
	}
	
	/*$sql_result=sql_select("select a.result,a.batch_id from pro_fab_subprocess a, pro_batch_create_mst b where b.id=a.batch_id and a.entry_form in (35,38) and a.load_unload_id=2  and a.company_id=$cbo_company_name and b.re_dyeing_from in (".$batch_id_sql.") and a.status_active=1 and a.is_deleted=0");*/
	
	$result_arr=array();
	foreach($result_sql as $value)
	{
		$result_arr[$value[csf('batch_id')]]=$value[csf('result')];	
	}
	
	// print_r($issue_single_batch_arr); echo "Test";die;
	
	// ====================================== batch wise cost =====================================
	$batchIdArr = array();
	foreach(array_unique($issue_single_batch_arr) as $b_id)
	{
		$batchIdArr[$b_id] = $b_id;
	}
	
	$batchCond=where_con_using_array($batchIdArr,0,"c.id");

	// print_r($batchIdArr);die();
	// echo $company_id;die();
	$company_cond = str_replace("a.company_id", "c.company_id", $company_id);	
	if ($cbo_working_name==0) $wo_company_idcond =""; else $wo_company_idcond =" and c.working_company_id='$cbo_working_name'";

	// $batchCond = implode(",", $batchIdArr);
	   $sql = "(SELECT p.id as recipe_id,p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup,a.avg_rate_per_unit, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure,b.prod_id, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
		from pro_recipe_entry_mst p, product_details_master a, pro_recipe_entry_dtls b,pro_batch_create_mst c  
		where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.ratio>0  and b.status_active=1 and b.is_deleted=0 $company_cond $wo_company_idcond and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 $batchCond and p.entry_form in(59,60)) 
		union 
		(
		SELECT p.id as recipe_id,p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup,0 as avg_rate_per_unit, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, b.prod_id as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure,b.prod_id, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
			from pro_recipe_entry_mst p, pro_recipe_entry_dtls b,pro_batch_create_mst c  
			where p.id=b.mst_id and p.batch_id=c.id and b.status_active=1 and b.is_deleted=0 $wo_company_idcond  and b.status_active=1 and b.is_deleted=0 and b.prod_id=0 and b.sub_process_id in(93,94,95,96,97,98) and p.status_active=1 and p.is_deleted=0 $batchCond and p.entry_form in(59,60))  order by sub_seq,seq_no";
	//echo $sql;//die();
	$costDataArr = array();
	$res = sql_select($sql);
	$tot_recCost=0;$tot_issue_qty=0;
	foreach ($res as $row) 
	{
		$current_stock = $row[csf('current_stock')];
		$recipe_type = $row[csf('recipe_type')];
		$surplus_solution = $row[csf('surplus_solution')];
		$pickup = $row[csf('pickup')];
		$batch_wgt = $row[csf('batch_qty')];//pickup,p.batch_qty
		 
		$batch_req_id=rtrim($batch_req_id_array[$row[csf('batch_id')]],',');
		$batch_req_idArr=array_unique(explode(",",$batch_req_id_array[$row[csf('batch_id')]]));
		//echo $batch_req_id.'D';
		$tot_batch_wgt=$batch_wgt_array[$row[csf('batch_id')]];
		//echo  $tot_batch_wgt.'D';
		$batch_qty=$row[csf('batch_qty')];
		if($issue_item_cat_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('item_category_id')]])
		{
			//echo $recipe_qnty.'='.$row[csf('avg_rate_per_unit')].'<br>';
			foreach($batch_req_idArr as $req_id)
			{
				if($issue_reqId_chk_arr[$row[csf('batch_id')]][$row[csf('recipe_id')]][$row[csf('prod_id')]]=='')
				{	
					$issue_qty+=$issue_item_cat_cost_arr[$req_id][$row[csf('prod_id')]][$row[csf('item_category_id')]];
					//$reqIdArr[$req_id]=$req_id;
					$tot_issue_qty+=$issue_qty;
					$issue_reqId_chk_arr[$row[csf('batch_id')]][$row[csf('recipe_id')]][$row[csf('prod_id')]]=111;
				 	//	echo "SDDD";
				}
			}
			//$issue_qty=$issue_item_cat_cost_arr[$req_id][$row[csf('prod_id')]][$row[csf('item_category_id')]];
			$issue_perKg=($issue_qty/$tot_batch_wgt);
			$issue_wgt_per=$issue_perKg*$batch_qty;
		//	echo $tot_batch_wgt.'='.$issue_qty.'='.$issue_wgt_per.'='.$issue_wgt_per*$row[csf('avg_rate_per_unit')].'<br>';
		$costDataArr[$row[csf('batch_id')]][$row[csf('item_category_id')]] +=$issue_wgt_per*$row[csf('avg_rate_per_unit')];
			//echo "AA";
		$ReqIdArr[$row[csf('batch_id')]].=$batch_req_id.',';
		//$recipe_qnty*$row[csf('avg_rate_per_unit')];
		$categryDataArr[$row[csf('batch_id')]].=$row[csf('item_category_id')].',';
		$tot_recCost+=$issue_wgt_per*$row[csf('avg_rate_per_unit')];
		}
	}
	//echo implode(",",$reqIdArr);die;
	//echo $tot_issue_qty.'Dx'.'='.$tot_recCost;
	// echo "<pre>";print_r($costDataArr);

	$i=1;$j=1;
	ob_start();	
	 
	//print_r($issue_single_batch_arr);
	//echo count($issue_single_batch_arr);die;
				$batch_ids_cond=where_con_using_array($issue_single_batch_arr,0,"b.id");
			/*	 $batch_sub_sql=sql_select("SELECT c.bill_no,c.party_id,c.party_source,b.id,a.delivery_qty,a.batch_id,b.batch_no,a.challan_no,a.amount
			from subcon_inbound_bill_dtls  a,subcon_inbound_bill_mst c,pro_batch_create_mst b  where  c.id=a.mst_id and a.batch_id=b.id 
			 and a.status_active=1 and a.is_deleted=0 $batch_ids_cond " );*/
			 
				 $batch_sub_sql=sql_select("SELECT c.bill_no,c.party_id,c.party_source,b.id,a.delivery_qty,a.batch_id,b.batch_no,a.challan_no,a.amount
			from subcon_inbound_bill_dtls  a,subcon_inbound_bill_mst c,pro_batch_create_mst b  where  c.id=a.mst_id and a.batch_id=b.id 
			 and a.status_active=1 and a.is_deleted=0 $batch_ids_cond "  );
			 
			 
				 foreach ($batch_sub_sql as $row) 
				{
					//$batch_req_id=$batch_req_id_array2[$row[csf("id")]];
					//echo $batch_req_id.'d';
					$dye_fin_bill_arr[$row[csf("id")]]['bill_sales']+=$row[csf("amount")];
				}
				unset($batch_sub_sql);
				
				 $batch_id_cond_for_in=where_con_using_array($issue_single_batch_arr,0,"b.id");
				$sales_data = sql_select("SELECT b.id as batch_id,a.id,a.job_no,a.sales_booking_no,a.within_group,b.batch_weight,a.buyer_id,a.style_ref_no, a.po_buyer from fabric_sales_order_mst a,pro_batch_create_mst b where a.id=b.sales_order_id and a.status_active=1  and b.status_active=1  $batch_id_cond_for_in");
					 
					foreach ($sales_data as $row)
					{
						//$sales_batch_arr[$row[csf('batch_id')]]=$row[csf('job_no')];
						$sales_batchQty_Arr[$row[csf('batch_id')]]['sales_batch_weight']=$row[csf('batch_weight')];
						
					}
					unset($sales_data);
					
		
					$tot_main_batch_weight=0;$prod_dateCostArr=array();
					$white_wash_colorArr=array(4,7,19);
					$avg_not_white_wash_colorArr=array(3,4,7,19,13,14,15,16,17,18,25,26,27,28);
					$double_dyeing_colorArr=array(13,14,15,16,17,18,25,26,27,28);
					$prod_date_qty_arr=array();$tot_trimsBatchQty=0;
					$prod_dateArr=array();$prod_date_arr=array();$totselfTrimQty=0;$tot_prod_qty=0;
			       	foreach(array_unique($issue_single_batch_arr) as $b_id)
					{
						//if(in_array($b_id, $floor_cond_arr) || empty($location_id)) 
						//{
							//batch_description_arr  
							$color_range_id= $batch_color_range_arr[$b_id]['color_range_id']; 
							//$dye_fin_bill_arr[$b_id]['bill_sales'];
						
							
							$result_id=$prod_result_arr[$b_id]['result'];
							if($result_id==1)
							{
							$prodQty=$batch_description_arr[$b_id]["batch_weight"];
							$production_qty_subcontact= $subcon_prod_arr[$b_id]['production_qty_subcontact'];
							$subcon_trimsWgt= $subcon_prod_arr[$b_id]['subcon_trimsWgt'];//$subcon_prod_arr[$row[csf("id")]]['subcon_trimsWgt']
							$batch_qty=$prod_arr[$b_id]['batch_qty'];//$prod_arr[$b_id]['prod_qty'];
							//echo $subcon_trimsWgt.'d';
							}
							$floor_id=$prod_arr[$b_id]['floor_id'];
							$prodDate=$prod_arr[$b_id]['batch_date'];
							
							$prodDate=date('M-Y',strtotime($prodDate));
							$batch_mon_arr[$b_id]=$b_id;
							//echo $prodDate.'=='.$floor_id.'<br>';
							//echo strtotime($prodDate).', ';   
							if(strtotime($prodDate)>0)
							{
							$prod_batchIdarr[$prodDate].=$b_id.',';
							$prod_date_arr[$prodDate]=$prodDate;
							$prod_date_count_arr2[$prodDate].=$prod_arr[$b_id]['batch_date'].',';
							$trims_weight=$batch_wgt_arr[$b_id]['trims_weight'];
							}
							
							$entry_formId=$batch_data_arr[$b_id]['entry_form'];
							$batch_againstId=$batch_data_arr[$b_id]['batch_against'];
							$trimsBatchQty=0;
							if($batch_againstId==1 && $prod_TrimsArr[$b_id]>0)
							 {
								$trimsBatchQty=$prod_TrimsArr[$b_id];
							 }
							$sales_batch_weight=$sales_batchQty_Arr[$b_id]['sales_batch_weight'];
							 //$AlltrimsBatchQty+=$trimsBatchQty;
						// echo $batch_qty.'='.$trimsBatchQty.', ';
						 $tot_prodQty=$prod_arr2[$b_id]['self_batch_qty']+$prod_arr2[$b_id]['sample_batch_qty']+$production_qty_subcontact+$trimsBatchQty;	
						 $prod_date_qty_arr[$prodDate]['prod_qty']+=$tot_prodQty; 
						 
						  $prod_date_qty_arr[$prodDate]['sales_batch_weight']+=$sales_batch_weight;
						 $tot_trimsBatchQty+=$trimsBatchQty; 
						 
						 	$mon_dye_fin_bill_arr[$prodDate]['dye_fin_bill']+=$dye_fin_bill_arr[$b_id]['bill_sales'];
							
							 if(in_array($color_range_id,$white_wash_colorArr))
							 {
									$prod_date_qty_arr[$prodDate]['white_wash_qty']+=$tot_prodQty+$sales_batch_weight; 
							 }
							 if(!in_array($color_range_id,$avg_not_white_wash_colorArr)) 
							 {
									$prod_date_qty_arr[$prodDate]['avg_color_qty']+=$tot_prodQty+$sales_batch_weight; 
							 }
							 if($color_range_id==3)
							 {
									$prod_date_qty_arr[$prodDate]['black_color_qty']+=$tot_prodQty; 
							 }
							 if(in_array($color_range_id,$double_dyeing_colorArr))
							 {
									$prod_date_qty_arr[$prodDate]['double_dyeing_qty']+=$tot_prodQty+$sales_batch_weight; 
							 }
		 
							/*	$prod_dateArr[$prodDate]['selfQty']+=$prodQty;
								$prod_dateArr[$prodDate]['selfBatchQty']+=$prod_arr2[$b_id]['self_batch_qty'];//$batch_qty;	
								//$prod_dateArr[$prodDate][$floor_id]['subconQty']+=$prodQty;	
								$prod_dateArr[$prodDate]['subconBatchQty']+=$production_qty_subcontact;
							
								$prod_dateArr[$prodDate]['sampleQty']+=$prodQty;//$prod_arr2[$row[csf("id")]]['sample_batch_qty']
								$prod_dateArr[$prodDate]['sampleBatchQty']+=$prod_arr2[$b_id]['sample_batch_qty'];;	
							
								 
								$prod_dateArr[$prodDate]['trimsQty']+=$prodQty;
								$prod_dateArr[$prodDate]['trimsBatchQty']+=$batch_qty+$trims_weight;*/	
							
							 //$prod_arr3[$row[csf("id")]]['tot_trim_batch_qty']
							 
							 
							
							
							
								$ReqId=rtrim($ReqIdArr[$b_id],',');
								$sum_ReqIdsArr=array_unique(explode(",",$ReqId));
			                	$first_chemi_cost=$first_dyeing_cost=0;
			               
			                    $sum_issue_amt_deys=0;$sum_issue_amt_chemical=0;
								$batch_weight=$batch_description_arr[$b_id]["batch_weight"];
								$sum_batchIdArr='';$tot_sum_chemical_cost=$tot_sum_deys_cost=0;
									foreach($sum_ReqIdsArr as $reqId)
									{
										
										$sum_issue_amt_chemical=$issue_item_cat_amt_dtls_arr[$reqId][5]+$issue_item_cat_amt_dtls_arr[$reqId][7];
										$sum_issue_amt_deys=$issue_item_cat_amt_dtls_arr[$reqId][6];
									   $batch_idsAll=$ReqBatch_id_arr[$reqId]; 
										$sum_batchIds=rtrim($batch_idsAll,',');
										$sumbatIdArr=array_unique(explode(",",$sum_batchIds)); 
										$sum_tot_batch_wgt=0;
										foreach($sumbatIdArr as $batchId)
										{
										//$tot_batch_wgt+=$batch_description_arr[$batchId]['batch_weight'];
										//echo $batch_wgt_arr[$batchId]['batchWgt'].'p';
										$sum_tot_batch_wgt+=$batch_wgt_arr[$batchId]['batchWgt']; 
										}
										//echo $sum_tot_batch_wgt.'='.$batch_weight.'='.$sum_issue_amt_chemical.'<br>';
										if($sum_issue_amt_chemical>0)
										{
										$tot_sum_chemical_cost+=($sum_issue_amt_chemical/$sum_tot_batch_wgt)*$batch_weight;
										}
										if($sum_issue_amt_deys>0)
										{
										$tot_sum_deys_cost+=($sum_issue_amt_deys/$sum_tot_batch_wgt)*$batch_weight;
										}
									}
									// echo  $tot_sum_chemical_cost.',';
									
									if($tot_sum_chemical_cost)
									{
									//$issue_amt_perKg_chemical=($sum_issue_amt_chemical/$sum_tot_batch_wgt);
									$summissue_amt_cal_chemical=$tot_sum_chemical_cost;
									}
									else
									 {$summissue_amt_cal_chemical=0;}
									//echo $sum_issue_amt_chemical.'='.$sum_tot_batch_wgt.'='.$batch_weight.',';
									if($tot_sum_deys_cost)
									{
									//$sum_issue_amt_perKg_dyes=($sum_issue_amt_deys/$sum_tot_batch_wgt);
									$sum_issue_amt_cal_dyes=$tot_sum_deys_cost;
									} 
									else
									{$sum_issue_amt_cal_dyes=0;}
									
									$che_cost_arr[$prodDate]['tot_dyes_chemical_cost']+=$sum_issue_amt_cal_dyes+$summissue_amt_cal_chemical;	
									
									/*if($entry_formId==0)
									{
										$prod_dateCostArr[$prodDate]['self']+=$sum_issue_amt_cal_dyes+$summissue_amt_cal_chemical;	
									}
									else if($entry_formId==36)
									{
										$prod_dateCostArr[$prodDate]['subcon']+=$sum_issue_amt_cal_dyes+$summissue_amt_cal_chemical;	
									}
									else if($batch_againstId==3 && $entry_formId==0)
									{
										$prod_dateCostArr[$prodDate]['sample']+=$sum_issue_amt_cal_dyes+$summissue_amt_cal_chemical;	
									}
									else if($entry_formId==136)
									{
										$prod_dateCostArr[$prodDate]['trims']+=$sum_issue_amt_cal_dyes+$summissue_amt_cal_chemical;	
									}*/
									//echo $entry_formId.'='.$batch_againstId.'<br>';
								$first_chemi_cost=$summissue_amt_cal_chemical;//$costDataArr[$b_id][5]+$costDataArr[$b_id][7];
			                    $buyer_wise_summary_arr[$buyerName]["first_chemi_cost"]+=$first_chemi_cost;

			                    $first_dyeing_cost=$sum_issue_amt_cal_dyes;//$costDataArr[$b_id][6];
			                    $buyer_wise_summary_arr[$buyerName]["first_dyeing_cost"]+=$first_dyeing_cost;
								$tot_main_batch_weight=$tot_main_batch_weight+=$batch_description_arr[$b_id]["batch_weight"];
								$buyer_wise_summary_arr[$buyerName]["tot_main_batch_weight"]+=$tot_main_batch_weight;
								// echo $first_dyeing_cost."<br>";
			               
			        
						//}	//Location // Floor End
					}
					//print_r($prod_dateTrimArr);
					  // echo $tot_trimsBatchQty.'SD';
					
					$table_width =420+count($batch_dying_floor_arr)*700;
					//echo implode(",",$dattarr);
	?>

 	 <div style="width:1160px">	
    <fieldset style="width:1160px;">
    <style type="text/css">
				.alignment_css
				{
					word-break: break-all;
					word-wrap: break-word;
				}
			</style>
            
		
        <table width="1260" cellspacing="0" cellpadding="0" border="0" rules="all" >
		    <tr class="form_caption">
		        <td colspan="11" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		    </tr>
		    <tr class="form_caption">
		        <td colspan="11" align="center"><?   echo $company_library[$working_company]; ?><br>
		        <b>
		        <?
				echo ($from_date == '0000-00-00' || $from_date == '' ? '' : change_date_format($from_date)).' To ';echo  ($to_date == '0000-00-00' || $to_date == '' ? '' : change_date_format($to_date));
		        ?> </b>
		        </td>
		    </tr>
		</table>
         <div align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1240" class="rpt_table">
			<thead>
			 <?
             $avg_per="Without->Black Color,White Color,Wash,Peroxide Wash,Average-Double Dyeing,Dark - Double Dyeing,Black-Double Dyeing,Light-Double Dyeing,Medium-Double Dyeing,Extra Dark-Double Dyeing,Green / Turquoise Color- Double Dyeing,Reactive Black- Double Dyeing,Light Royal - Double Dyeing,Dark Royal - Double Dyeing";
$double_per=" Average-Double Dyeing,Dark - Double Dyeing,Black-Double Dyeing,Light-Double Dyeing,Medium-Double Dyeing,Extra Dark-Double Dyeing,
Green / Turquoise Color- Double Dyeing,Reactive Black- Double Dyeing,Light Royal - Double Dyeing,Dark Royal - Double Dyeing";
			 ?>
             <tr>
				 
				<th width="100" style="word-wrap: break-word; width: 100px;">Month</th>

				<th width="100"  style="word-wrap: break-word; width: 100px;">Total Dyeing <br>Production Kg</th>
                <th width="100"  style="word-wrap: break-word; width: 100px;">Sales Order Qty.</th>
				<th width="100" title="White Color,Wash,Peroxide Wash" style="word-wrap: break-word; width: 100px;">White Color/<br>Wash/Peroxide<br> wash %</th>	
                <th width="100"  title="<? echo $avg_per;?>"  style="word-wrap: break-word; width: 100px;">Avg Color %</th>			
				<th width="100"  title="Black color" style="word-wrap: break-word; width: 100px;">Black Color %</th>
				<th width="100"  title="<? echo $double_per;?>" style="word-wrap: break-word; width: 100px;">Double Dyeing %</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Total Dyes<br>Chemical <br>Cost Tk</th>
				<th width="100"  style="word-wrap: break-word; width: 100px;">Cost /Kg</th>				
				<th width="100" style="word-wrap: break-word; width: 100px;">Total Sales<br> Bill Tk</th>
                <th width="100" style="word-wrap: break-word; width: 100px;">Sales/Kg</th>	
				<th width="">Dyes & Chemical <br>Cost on Sales%</th>
				 
                </tr>
				
			</thead>
		</table>

	
         <div style="max-height:350px; width:1258px; overflow-y:scroll;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1240" class="rpt_table" id="table_body">
		 <tbody>
		<?
		
	
		

			    $i=1;
			    $total_self_qty_inhouse=$total_white_wash_qty=$total_avg_color_qty=$total_black_color_qty=$total_double_dyeing_qty=$total_tot_dyes_chemical_cost=$total_tot_dye_fin_bill_amount=$total_inbound_finish_qty=$total_tot_finishing_kg=$total_inbound_sub_finish_amt=$total_tot_dyeing_fin_earn=$total_tot_dye_chemical_cost=$total_tot_income=$total_avg_cost_per=0;$total_special_finish_amt=$total_special_finish_qty=0;$total_self_qty_sales=0;
				//ksort($prod_date_arr);
				// print_r($prod_date_arr);die;
				foreach($prod_date_arr as $date_key=>$row)
				{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$prod_batchId=$prod_batchIdarr[$date_key];
								$date_key_count=rtrim($prod_date_count_arr2[$date_key],',');
								$date_key_countArr=array_unique(explode(",",$date_key_count));
								//print_r($date_key_countArr);
								$date_key_max=max($date_key_countArr);
								$date_key_min=min($date_key_countArr);
								$prod_date_range=$date_key_min.'__'.$date_key_max;
								//echo $date_key_min.'='.$date_key_max.', ';
								//$batchId=rtrim($prod_batchId,',');
								$batch_id=rtrim($prod_batchId,',');
								$batch_ids=array_unique(explode(",",$batch_id));
								$batch_ids_all=implode(",",array_unique(explode(",",$batch_id)));
								//echo $batch_ids_all.'D';
							  
								$tot_dye_fin_bill_amount=$mon_dye_fin_bill_arr[$date_key]['dye_fin_bill'];
								//$tot_dyes_chemical_cost=$tot_sum_chemical_cost+$tot_sum_deys_cost; 
								$tot_dyes_chemical_cost=$che_cost_arr[$date_key]['tot_dyes_chemical_cost'];
								// echo $tot_dyes_chemical_cost.'='.$tot_sum_chemical_cost.'='.$tot_sum_deys_cost.'<br>';
							 if($tot_dyes_chemical_cost>0) $tot_dyes_chemical_cost=$tot_dyes_chemical_cost;else $tot_dyes_chemical_cost=0;
								$self_qty_inhouse=	$prod_date_qty_arr[$date_key]['prod_qty']; 
								$sales_batch_weight=	$prod_date_qty_arr[$date_key]['sales_batch_weight']; 
								$white_wash_qty=	$prod_date_qty_arr[$date_key]['white_wash_qty'];
								if($white_wash_qty)
								{
								$white_wash_qty_per=	($white_wash_qty*100)/($self_qty_inhouse+$sales_batch_weight);
								}
								else $white_wash_qty=0;
								
								$avg_color_qty=	$prod_date_qty_arr[$date_key]['avg_color_qty'];
								if($avg_color_qty)
								{
								$avg_color_qty_per=	($avg_color_qty*100)/($self_qty_inhouse+$sales_batch_weight);
								} else $avg_color_qty_per=0;
								
								$black_color_qty=	$prod_date_qty_arr[$date_key]['black_color_qty'];
								if($black_color_qty)
								{
								$black_color_qty_per=	($black_color_qty*100)/($self_qty_inhouse+$sales_batch_weight);
								}
								 else $black_color_qty_per=0;
								$double_dyeing_qty=	$prod_date_qty_arr[$date_key]['double_dyeing_qty'];
								if($double_dyeing_qty)
								{
								$double_dyeing_qty_per=	($double_dyeing_qty*100)/($self_qty_inhouse+$sales_batch_weight);
								}
								
								
								//echo $tot_dyes_chemical_cost.'DDD';
								 //1st_chemi_dyes_batch_dtls_popup
								?>
								
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
									
									<td width="100"  style="word-break: break-all; word-wrap: break-word;"><p><? echo $date_key; ?></p></td>
								
									<td width="100" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $prod_date_range; ?>','<? echo $batch_ids_all; ?>','<? echo $working_company; ?>','dyeing_earn_inhouse_popup',1)"><? echo number_format($self_qty_inhouse,2); ?></a></p></td>
                                    <td width="100" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $prod_date_range; ?>','<? echo $batch_ids_all; ?>','<? echo $working_company; ?>','dyeing_earn_inhouse_popup',1)"></a><? echo number_format($sales_batch_weight,2); ?></p></td>
									<td width="100" title="Qty=<? echo $white_wash_qty;?>*100/Prod Qty"  style="word-break: break-all; word-wrap: break-word;" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $batch_ids_all; ?>','<? echo $working_company; ?>','dyeing_earn_inhouse_popup',2)"></a><? echo number_format($white_wash_qty_per,2); ?></p></td>
                                   
									<td width="100" align="right" title="Qty=<? echo $avg_color_qty;?>*100/Prod Qty"><p><? echo number_format($avg_color_qty_per,2); ?></p></td>
                                    <td width="100" align="right" title="<? echo $black_color_qty;?>*100/Prod Qty"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $prod_date_range; ?>','<? echo $batch_ids_all; ?>','<? echo $working_company; ?>','dyeing_earn_inboundSub_popup',4)"></a><? echo number_format($black_color_qty_per,2); ?></p></td>
									<td width="100" align="right" title="<? echo $double_dyeing_qty;?>*100/Prod Qty"><p><? echo number_format($double_dyeing_qty_per,2); ?></p></td>
                                    
                                    <td width="100" align="right" title="<? //echo $tot_sum_chemical_cost.'='.$tot_sum_deys_cost;?>"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $prod_date_range; ?>','<? echo $batch_ids_all; ?>','<? echo $working_company; ?>','1st_chemi_dyes_batch_dtls_popup',2)"><? echo number_format($tot_dyes_chemical_cost,2); ?></a><? //echo number_format($tot_dyes_chemical_cost,2); ?></p></td>
								
									<td width="100" align="right" title="Tot Dyes Chemical Cost/Prod Qty" ><p><? if($tot_dyes_chemical_cost>0 && $self_qty_inhouse>0) echo number_format($tot_dyes_chemical_cost/($self_qty_inhouse+$sales_batch_weight),2);else echo ""; ?></p></td>
									<td width="100" align="right" title=""><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $prod_date_range; ?>','<? echo $batch_ids_all; ?>','<? echo $working_company; ?>','total_sales_bill_popup',3)"><? echo number_format($tot_dye_fin_bill_amount,2); ?></a><? //echo number_format($tot_dye_fin_bill_amount,2); ?></p></td>
                					 <td width="100" title="Total Sales Bill/Total Prod Qty"  style="word-break: break-all; word-wrap: break-word;" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $prod_date_range; ?>','<? echo $special_batch_idArr; ?>','<? echo $working_company; ?>','special_qty_inhouse_popup',9)"></a><? if($self_qty_inhouse && $tot_dye_fin_bill_amount>0) echo number_format($tot_dye_fin_bill_amount/($self_qty_inhouse+$sales_batch_weight),2);else echo ""; ?></p></td>
								
									<td width="" align="right" title="Total Dyes Chemical Cost*100/Total Sales Bill"><p><? if($tot_dyes_chemical_cost>0 && $tot_dye_fin_bill_amount>0) echo number_format($tot_dyes_chemical_cost*100/$tot_dye_fin_bill_amount,2);else echo ""; ?></p></td>

									
							    </tr>
							    <?
								
								$total_self_qty_inhouse+=$self_qty_inhouse;$total_self_qty_sales+=$sales_batch_weight;
								$total_white_wash_qty+=$white_wash_qty;
								$total_avg_color_qty+=$avg_color_qty;
								$total_black_color_qty+=$black_color_qty;
								$total_double_dyeing_qty+=$double_dyeing_qty;
								$total_tot_dyes_chemical_cost+=$tot_dyes_chemical_cost;
								$total_tot_dye_fin_bill_amount+=$tot_dye_fin_bill_amount;
								 
							    $i++;
							
				}
			 
			    ?>
                </tbody>
                
			</table>
            </div>
            <table class="rpt_table" width="1240" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                      <th width="100" align="right">Total</th>
					<th width="100" id="tot_dyeing_prod_qty" align="right"><? echo number_format($total_self_qty_inhouse,2); ?></th>
                    <th width="100" id="tot_dyeing_prod_qty_sales" align="right"><? echo number_format($total_self_qty_sales,2); ?></th>
					<th width="100" align="right"><? if($total_white_wash_qty && $total_self_qty_inhouse) echo number_format(($total_white_wash_qty*100)/($total_self_qty_inhouse+$total_self_qty_sales),2,'.','');else echo ""; ?></th>
					<th width="100" align="right"><?  if($total_avg_color_qty && $total_self_qty_inhouse) echo number_format(($total_avg_color_qty*100)/($total_self_qty_inhouse+$total_self_qty_sales),2,'.','');else echo "";?></th>
					<th width="100" align="right"><? if($total_black_color_qty && $total_self_qty_inhouse) echo number_format(($total_black_color_qty*100)/($total_self_qty_inhouse+$total_self_qty_sales),2,'.','');else echo ""; ?></th>

					<th width="100" align="right"><strong><? if($total_double_dyeing_qty && $total_self_qty_inhouse) echo number_format(($total_double_dyeing_qty*100)/($total_self_qty_inhouse+$total_self_qty_sales),2,'.','');else echo ""; ?></strong></th>
					<th width="100" align="right"><strong><? if($total_tot_dyes_chemical_cost && $total_self_qty_inhouse) echo number_format($total_tot_dyes_chemical_cost,2);else echo ""; ?></strong></th>  

					<th width="100"  align="right"><? if($total_tot_dyes_chemical_cost && $total_self_qty_inhouse)  echo number_format($total_tot_dyes_chemical_cost/($total_self_qty_inhouse+$total_self_qty_sales),2,'.','');else echo ""; ?></th>
                    
					<th width="100"  align="right"><? echo number_format($total_tot_dye_fin_bill_amount,2); ?></th>    

                    <th width="100" align="right"><? if($total_tot_dye_fin_bill_amount && $total_self_qty_inhouse) echo number_format($total_tot_dye_fin_bill_amount/($total_self_qty_inhouse+$total_self_qty_sales),2,'.',''); ?></th>
					<th width=""  align="right"><? if($total_tot_dyes_chemical_cost && $total_tot_dye_fin_bill_amount) echo number_format($total_tot_dyes_chemical_cost*100/$total_tot_dye_fin_bill_amount,2,'.',''); else echo 0;?>&nbsp;</th>
                        
                     </tr>
                    </tfoot>
                </table>
			 </div>
		</fieldset>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);


    $filename2=$user_id."_summary_".$name.".xls";
    $create_new_doc2 = fopen($filename2, 'w');	
    $is_created2 = fwrite($create_new_doc2, $summaryHTML);

    echo "$html****$filename****$filename2****$report_type"; 
    exit();
}
//total_sales_bill_popup
if($action=="total_sales_bill_popup") //  
{
	extract($_REQUEST);
 	echo load_html_head_contents("Month Wise Color Range wise Cost Prod. poppup Info","../../../", 1, 1, $unicode,'','');
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  ); 
	
	$date_key=explode("__",$date_key);
	$start_date=$date_key[0];
	$end_date=$date_key[1];
	$batch_id=str_replace("'","",$batch_id);
	$w_companyID=str_replace("'","",$w_companyID);
	$company_id=str_replace("'","",$company_id);
	
	$w_companyIDCond="";$companyIDCond="";$w_companyIDCond2="";
	//if($w_companyID) $w_companyIDCond="and a.working_company_id=$w_companyID";
	if($w_companyID) $w_companyIDCond2="and a.service_company=$w_companyID";
	if($company_id) $companyIDCond="and a.company_id=$company_id";
	
	 		if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$start_date),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$end_date),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$start_date),"","",1);
				$end_date=change_date_format(str_replace("'","",$end_date),"","",1);
			}
			
			if($start_date!="" && $end_date!="")
			{
			//$date_cond= "and to_char(c.process_end_date,'MON-YYYY')='$date_key'";
			$date_cond= "and a.process_end_date between '$start_date' and '$end_date'";
			}
			else $date_cond= "";
			
		$sql_batch_prod=("SELECT  b.id
		from  pro_batch_create_mst b,pro_fab_subprocess a where  a.batch_id=b.id   and a.status_active=1 and a.load_unload_id=2 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and a.entry_form in (35,38) $date_cond $companyIDCond $w_companyIDCond2");
		$sql_batch_prod_result=sql_select( $sql_batch_prod);
		foreach($sql_batch_prod_result as $row)
		{
			//$issue_single_batch_arr[$row[csf('id')]]=$row[csf('id')];
			$batch_id_arr[]=$row[csf("id")];
		}
		
		
		 $sql_product=("SELECT a.floor_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,b.job_no,b.extention_no,b.batch_no,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.batch_qnty
		from  pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_create_dtls c where  a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id   and a.entry_form in (35)  and b.status_active=1 and a.load_unload_id=2 and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0 $date_cond $companyIDCond $w_companyIDCond2
		union
 SELECT a.floor_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,
b.job_no,b.extention_no,b.batch_no,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,
b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.trims_wgt_qnty as batch_qnty 
from pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_trims_dtls c 
where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id 
and a.entry_form in (35,38)  and b.status_active=1 and a.load_unload_id=2
and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0 $date_cond $companyIDCond $w_companyIDCond2  order by  batch_date asc");
		
		$sql_prod=sql_select($sql_product);$tot_self_trim_qty=0;$tot_self_trimqty=0;
		$production_qty=0;$sub_trims_wgt_check_array=array();$jj=1;$self_trims_wgt_check_array=array();$jk=1;
		 foreach($sql_prod as $row)
		 {
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
			 
		 }
		 
		 $sql_sub_product=("SELECT a.floor_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,b.job_no,b.extention_no,b.batch_no,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.batch_qnty,e.party_id
		from  pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_create_dtls c,subcon_ord_dtls d,subcon_ord_mst e where  a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and c.po_id=d.id and  d.job_no_mst=e.subcon_job and a.entry_form in (38) and e.party_id not in(426,439,444,458,425,427,428,443,392,564,565,563) and b.status_active=1 and a.load_unload_id=2 and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0 and d.status_active=1 and e.status_active=1  $date_cond $companyIDCond $w_companyIDCond2 order by batch_date ");//die;
		
		$sql_sub_prod=sql_select($sql_sub_product);//$tot_self_trim_qty=0;$tot_self_trimqty=0;
		$subcon_trims_wgt_check_array=array();$jjj=1;$jkk=1;
		 foreach($sql_sub_prod as $row)
		 {
		
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
			$sub_buyer_batch_arr[$row[csf("batch_no")]]=$row[csf("party_id")]; 
		
			 if($row[csf("entry_form")]==36 && $row[csf("batch_against")]==1 && $row[csf("result")]==1)
			 {
				 $batch_noSubcon=$row[csf('id')];
				if (!in_array($batch_noSub,$subcon_trims_wgt_check_array))
				{ $jjj++;
	
					 $subcon_trims_wgt_check_array[]=$batch_noSubcon;
					 $tot_trim_qty=$row[csf('total_trims_weight')];
				}
				else
				{
					 $tot_trim_qty=0;
				}
				//$total_trims_weight_qty+=$tot_trim_qty;
				 $subcon_prod_arr[$row[csf("id")]]['production_qty_subcontact']+=$row[csf("batch_qnty")];
				 if($total_trims_weight_qty>0)
				 {
				 $subcon_prod_arr[$row[csf("id")]]['subcon_trimsWgt']=$tot_trim_qty;
				 }
			 }
		 }
		 
		 	$sql_qty = " (select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no,b.total_trims_weight,
			sum(case when a.service_source=1 then  b.batch_weight end) as batch_weight,
			SUM(case when a.service_source=1 and b.batch_against!=3 and c.is_sales!=1 then c.batch_qnty end) AS production_qty_inhouse,
			SUM(case when a.service_source=3 then c.batch_qnty end) AS production_qty_outbound,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=1 and b.is_sales!=1  then c.batch_qnty end) AS prod_qty_sample_without_order,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=0 and c.is_sales!=1  then c.batch_qnty end) AS prod_qty_sample_with_order,
			SUM(case WHEN c.is_sales=1 then c.batch_qnty end) AS fabric_sales_order_qty
			from pro_batch_create_dtls c, wo_po_break_down e, wo_po_details_master d, pro_fab_subprocess a,
			 lib_machine_name g, pro_batch_create_mst b 
			where a.batch_id=b.id and b.entry_form=0 and g.id=a.machine_id and b.id=c.mst_id and a.batch_id=c.mst_id and a.entry_form=35 and a.load_unload_id=2 and b.batch_against in(1)  and c.po_id=e.id and d.job_no=e.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  c.status_active=1 and c.is_deleted=0  and a.result=1  $date_cond $companyIDCond $w_companyIDCond2
			group by b.working_company_id,b.company_id,b.color_range_id,b.id,b.batch_no,b.total_trims_weight)
			union  
			( select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no,b.total_trims_weight,
			sum(case when a.service_source=1 then  b.batch_weight end) as batch_weight,
			SUM(case when a.service_source=1 and b.batch_against in(1) and c.is_sales!=1  then c.batch_qnty end) AS production_qty_inhouse,
			SUM(case when a.service_source=3 then c.batch_qnty end) AS production_qty_outbound,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=1 and c.is_sales!=1 then c.batch_qnty end) AS prod_qty_sample_without_order,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=0 and c.is_sales!=1 then c.batch_qnty end) AS prod_qty_sample_with_order,
			SUM(case WHEN c.is_sales=1 then c.batch_qnty end) AS fabric_sales_order_qty
			from pro_batch_create_dtls c, pro_batch_create_mst b, pro_fab_subprocess a, lib_machine_name g,wo_non_ord_samp_booking_mst h
			where  h.booking_no=b.booking_no and a.batch_id=b.id and a.batch_id=c.mst_id and b.entry_form=0 and g.id=a.machine_id and b.id=c.mst_id and a.entry_form=35 and a.load_unload_id=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  c.status_active=1 and c.is_deleted=0  and a.result=1  $date_cond $companyIDCond $w_companyIDCond2
			group by b.id,b.color_range_id,b.working_company_id,b.company_id,b.batch_no,b.total_trims_weight ) 
			union
			(select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no, SUM(c.trims_wgt_qnty) AS total_trims_weight, 0 as batch_weight, 0 AS production_qty_inhouse, 0 AS production_qty_outbound, 0 AS prod_qty_sample_without_order, 0 AS prod_qty_sample_with_order, 0 AS fabric_sales_order_qty
			from pro_batch_create_mst b,pro_batch_trims_dtls c, wo_po_details_master d, pro_fab_subprocess a, lib_machine_name g 
			where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and g.id=a.machine_id and d.job_no=b.job_no and b.entry_form=136 and a.entry_form=35 and a.load_unload_id=2 and b.batch_against in(1) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.result=1 and c.status_active=1 and c.is_deleted=0  $date_cond $companyIDCond $w_companyIDCond2 
			GROUP BY b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no) ";

			// echo $sql_qty;
			$sql_result=sql_select( $sql_qty);

		//$fabric_sales_order_qty=0;
		$total_trims_weight_qty=0;$tttm=0;
		$j=1;$self_trims_wgt_check_array=array();
		foreach($sql_result as $row)
		{
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
		}
		 
		
		 
	
	?>
    
    
      <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<div style="width:960px; margin-left:30px" id="report_div">
     <!--<div style="width:870px;" align="center"><input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
    <div style="width:960px; font-family:'Arial Narrow'; font-size:14px;">Bill Dtls</div>
    <table align="center" cellspacing="0" width="960" border="1" rules="all" class="rpt_table" >
        <thead align="center">
    	   <tr>
                <th width="20">SL</th>
                <th width="100">Bill No</th> 
                <th width="60">Bill Date</th> 
                <th width="100">Party</th>
                <th width="70">System Challan</th>
                <th width="100">Buyer</th>
                <th width="70">Int. Ref. No</th>
                <th width="100">Fabric Booking</th>
                
                <th width="100">Batch No</th>
                <th width="70">Batch Weight</th>
                <th width="70">Bill Quantity</th>
                <th width="">Bill Amount (Tk)</th>
                 
           </tr>
		</thead>
		<?  
	
		//$batch_idArr=explode(",",$batch_id);
		$batch_ids_cond=where_con_using_array($issue_single_batch_arr,0,"b.id");
		$po_fabric_sql=sql_select("SELECT b.batch_no as BATCH_NO,a.grouping as REF_NO
		from wo_po_break_down  a,pro_batch_create_dtls c ,pro_batch_create_mst b where  c.po_id=a.id and b.id=c.mst_id  
		 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.is_sales=0 and a.grouping is not null $batch_ids_cond" );
		 foreach ($po_fabric_sql as $row) 
		{
			if($row["REF_NO"])
			{
			$batch_ref_no_arr[$row["BATCH_NO"]]['ref_no'].=$row["REF_NO"].',';
			}
		}
		unset($po_fabric_sql);
		
		$non_fabric_sql=sql_select("SELECT a.GROUPING,a.booking_no as BOOKING_NO,a.buyer_id as BUYER
		from wo_non_ord_samp_booking_mst  a,pro_batch_create_mst b where  b.booking_no_id=a.id  
		 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $batch_ids_cond" );
		 foreach ($non_fabric_sql as $row) 
		{
			if($row["BOOKING_NO"])
			{
			$batch_booking_buyer_arr[$row["BOOKING_NO"]]['BUYER']=$row["BUYER"];
			$batch_booking_buyer_arr[$row["BOOKING_NO"]]['GROUPING']=$row["GROUPING"];
			}
		}
		unset($non_fabric_sql);
		
		$fabric_booking_sql=sql_select("SELECT a.booking_no as BOOKING_NO,a.buyer_id as BUYER
		from wo_booking_mst  a,pro_batch_create_mst b where  b.booking_no_id=a.id  
		 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.booking_type=1   $batch_ids_cond" );
		 foreach ($fabric_booking_sql as $row) 
		{
			if($row["BOOKING_NO"])
			{
			$batch_booking_buyer_arr[$row["BOOKING_NO"]]['BUYER']=$row["BUYER"];
			}
		}
		unset($fabric_booking_sql);
				
		$po_fabric_sql=sql_select("SELECT b.batch_no as BATCH_NO,a.grouping as REF_NO
		from wo_po_break_down  a,pro_batch_create_dtls c ,pro_batch_create_mst b where  c.po_id=a.id and b.id=c.mst_id  
		 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.is_sales=0 and a.grouping is not null $batch_ids_cond" );
		 foreach ($po_fabric_sql as $row) 
		{
			if($row["REF_NO"])
			{
			$batch_ref_no_arr[$row["BATCH_NO"]]['ref_no'].=$row["REF_NO"].',';
			}
		}
		unset($po_fabric_sql);
		
				 $batch_sub_sql=sql_select("SELECT c.bill_no,c.party_id,c.bill_date,c.party_source,b.batch_weight,b.booking_no,b.booking_without_order,b.id,a.delivery_qty,a.batch_id,b.batch_no,a.challan_no,a.amount
			from subcon_inbound_bill_dtls  a,subcon_inbound_bill_mst c,pro_batch_create_mst b  where  c.id=a.mst_id and a.batch_id=b.id 
			 and a.status_active=1 and a.is_deleted=0 $batch_ids_cond " );
			 
				foreach ($batch_sub_sql as $row) 
				{
					$dye_fin_bill_arr[$row[csf("bill_no")]][$row[csf("batch_no")]]['bill_sales']+=$row[csf("amount")];
					$dye_fin_bill_arr[$row[csf("bill_no")]][$row[csf("batch_no")]]['delivery_qty']+=$row[csf("delivery_qty")];
					$dye_fin_bill_arr[$row[csf("bill_no")]][$row[csf("batch_no")]]['party_id']=$row[csf("party_id")];
					$dye_fin_bill_arr[$row[csf("bill_no")]][$row[csf("batch_no")]]['bill_date']=$row[csf("bill_date")];
					$dye_fin_bill_arr[$row[csf("bill_no")]][$row[csf("batch_no")]]['challan_no']=$row[csf("challan_no")];
					$dye_fin_bill_arr[$row[csf("bill_no")]][$row[csf("batch_no")]]['batch_no']=$row[csf("batch_no")];
					$dye_fin_bill_arr[$row[csf("bill_no")]][$row[csf("batch_no")]]['booking_without_order']=$row[csf("booking_without_order")];
					$dye_fin_bill_arr[$row[csf("bill_no")]][$row[csf("batch_no")]]['booking_no']=$row[csf("booking_no")];
					$dye_fin_bill_arr[$row[csf("bill_no")]][$row[csf("batch_no")]]['batch_weight']=$row[csf("batch_weight")];
					$dye_fin_bill_arr[$row[csf("bill_no")]][$row[csf("batch_no")]]['party_source']=$row[csf("party_source")];
					 
				}
				
				
		//echo $req_id.'D';
		//$req_idArr=array_unique(explode(",",$req_id));
		//print_r($req_idArr);
		$i=1;$total_delivery_qty=$total_bill_amount=$total_batch_wgt=0;
		foreach($dye_fin_bill_arr as $bill_no=>$bill_data)
		{
		 foreach($bill_data as $batch_no=>$row)
		  {
			 
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
				
				$party_id=$row['party_id'];
				$challan_no=$row['challan_no'];
				$booking_without_order=$row['booking_without_order'];
				$booking_no=$row['booking_no'];
				
				if($booking_without_order==1)
				{ 
					$buyer_id=$batch_booking_buyer_arr[$booking_no]['BUYER'];
					$ref_no=$batch_booking_buyer_arr[$booking_no]['GROUPING'];
					//$ref_no=rtrim($batch_ref_no_arr[$batch_no]['ref_no'],",");
					$ref_nos=implode(", ",array_unique(explode(",",$ref_no)));
				}
				else
				{
					$buyer_id=$batch_booking_buyer_arr[$booking_no]['BUYER'];
					
					$ref_no=rtrim($batch_ref_no_arr[$batch_no]['ref_no'],",");
					$ref_nos=implode(", ",array_unique(explode(",",$ref_no)));
				}
				$batch_weight=$row['batch_weight'];
				
				$bill_date=$row['bill_date'];
				$delivery_qty=$row['delivery_qty'];
				$amount=$row['bill_sales'];
				$party_source=$row['party_source'];
				if($party_source==1)
				{
					$party_name=$company_library[$row['party_id']];	
				}
				else {
					$party_name=$buyer_library[$row['party_id']];	
					}
					if($buyer_id=="") $buyer_id=$sub_buyer_batch_arr[$batch_no];
					
					
				  
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trDys_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trDys_<? echo $i; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><p><? echo $bill_no; ?></p></td>
                    <td align="center"><? echo $bill_date; ?></td>
                    
                    <td align="center" title="<?=$row['party_id'];?>"><p><? echo $party_name; ?></p></td>
                    <td align="center"><p><? echo $challan_no; ?></td>
                    <td align="center" title="<? echo $buyer_id;?>"><p><? echo $buyer_library[$buyer_id]; ?></p></td>
                    <td align="center"><p><? echo $ref_nos; ?></p></td>
                    <td align="center"><p><? echo $booking_no; ?></p></td>
                    
					<td><? echo $batch_no; ?></td>
                    <td align="center"><? echo number_format($batch_weight,0);$total_batch_wgt+=$batch_weight; ?></td>
					 
					<td align="right"><? echo number_format($delivery_qty,2); $total_delivery_qty+=$delivery_qty; ?></td>
					 
					<td align="right"><?  echo  number_format($amount,2); $total_bill_amount+=$amount; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
				 
			}
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="9" align="right">Total:</th>
                 <th><? echo number_format($total_batch_wgt,2); ?></th>
                <th align="right"><? echo number_format($total_delivery_qty,2); ?></th>
               
                <th align="right"><? echo number_format($total_bill_amount,2); ?></th>
            </tr>
        </tfoot>
      </table>
      
    <?
exit();
}
if($action=="1st_chemi_dyes_batch_dtls_popup") // batch Wise 2
{
	extract($_REQUEST);
 	echo load_html_head_contents("Month Wise Color Range wise Cost Prod. poppup Info","../../../", 1, 1, $unicode,'','');
	//echo $batch_id;die;
	//$batch_non_redyeing_id=return_field_value("id","pro_batch_create_mst","batch_against<>2 and status_active=1 and id in($batch_id)","id");
	//if($batch_non_redyeing_id=="")die;
 
	 
	
	$date_key=explode("__",$date_key);
	$start_date=$date_key[0];
	$end_date=$date_key[1];
	$batch_id=str_replace("'","",$batch_id);
	$w_companyID=str_replace("'","",$w_companyID);
	$company_id=str_replace("'","",$company_id);
	
	$w_companyIDCond="";$companyIDCond="";$w_companyIDCond2="";
	//if($w_companyID) $w_companyIDCond="and a.working_company_id=$w_companyID";
	if($w_companyID) $w_companyIDCond2="and a.service_company=$w_companyID";
	if($company_id) $companyIDCond="and a.company_id=$company_id";
	
	 		if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$start_date),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$end_date),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$start_date),"","",1);
				$end_date=change_date_format(str_replace("'","",$end_date),"","",1);
			}
			
			if($start_date!="" && $end_date!="")
			{
			//$date_cond= "and to_char(c.process_end_date,'MON-YYYY')='$date_key'";
			$date_cond= "and a.process_end_date between '$start_date' and '$end_date'";
			}
			else $date_cond= "";
		 
	
		  $sql_batch_prod=("SELECT  b.batch_no,b.booking_without_order,b.job_no,b.id,b.entry_form,b.batch_weight,b.is_sales,b.batch_against,a.process_end_date as batch_date,a.floor_id
		from  pro_batch_create_mst b,pro_fab_subprocess a where  a.batch_id=b.id   and a.status_active=1 and a.load_unload_id=2 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and a.entry_form in (35,38) $date_cond $companyIDCond $w_companyIDCond2");
		$sql_batch_prod_result=sql_select( $sql_batch_prod);
		foreach($sql_batch_prod_result as $row)
		{
			//$all_batch_idArr[$row[csf('id')]]=$row[csf('id')];
			//$batch_id_arr[]=$row[csf("id")];
			$floor_id=$row[csf("floor_id")];
		$entry_formId=$row[csf("entry_form")];
		$batch_against_id=$row[csf("batch_against")];
		$batch_id_arr[]=$row[csf("id")];
		$batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
		 
		$batch_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
		$batch_data_arr[$row[csf("id")]]['is_sales']=$row[csf("is_sales")];
			if($row[csf("job_no")]!="")
			{
			$batch_data_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			}
		$batch_booking_without_arr[$row[csf("id")]]=$row[csf("booking_without_order")];
	 
 		if($batch_against_id==1 || $batch_against_id==3)
		{		
			 
			// $batch_id_arr[]=$row[csf("id")]; 
			$batch_description_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			$batch_description_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
			$batch_description_arr[$row[csf("id")]]['without_order']=$row[csf("booking_without_order")];
			$batch_description_arr[$row[csf("id")]]['batch_weight']+=$row[csf("batch_weight")];
			$batch_description_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];
			$batch_description_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			$batch_description_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			
			$batch_description_arr[$row[csf("id")]]['color_range']=$row[csf("color_range_id")];
			$batch_description_arr[$row[csf("id")]]['fabric_type']=$row[csf("fabric_type")];
			$batch_no_arr[]="'".$row[csf("batch_no")]."'";
			// $batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
			
			$batch_wise_arr[$row[csf("batch_no")]]['batch_id'].=$row[csf("id")].',';
			$batch_wise_arr[$row[csf("batch_no")]]['batch_no']=$row[csf("batch_no")];
			$batch_wise_arr[$row[csf("batch_no")]]['extention_no'].=$row[csf("extention_no")].',';
			$batch_ext_wise_arr[$row[csf("id")]]['extention_no']=$row[csf("extention_no")];
			
		}
		if($batch_against_id==2)
		{
			$batch_description_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			$batch_description_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
			$batch_description_arr[$row[csf("id")]]['without_order']=$row[csf("booking_without_order")];
			$batch_description_arr[$row[csf("id")]]['batch_weight']+=$row[csf("batch_weight")];
			$batch_description_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];
			$batch_description_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			$batch_description_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			
			$batch_description_arr[$row[csf("id")]]['color_range']=$row[csf("color_range_id")];
			$batch_description_arr[$row[csf("id")]]['fabric_type']=$row[csf("fabric_type")];
			$batch_no_arr[]="'".$row[csf("batch_no")]."'";
			// $batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
			
			$batch_wise_arr[$row[csf("batch_no")]]['batch_id'].=$row[csf("id")].',';
			$batch_wise_arr[$row[csf("batch_no")]]['batch_no']=$row[csf("batch_no")];
			$batch_wise_arr[$row[csf("batch_no")]]['extention_no'].=$row[csf("extention_no")].',';
			$batch_ext_wise_arr[$row[csf("id")]]['extention_no']=$row[csf("extention_no")];
			$batch_ext_wise_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			
 			$re_batch_wise_arr[$row[csf("batch_no")]]['batch_id'].=$row[csf("id")].',';
			
		}
		if($row[csf("batch_against")]==2)
		{
			/*if($row[csf("re_dyeing_from")]>0)
			{
				$reding_batch_id[$row[csf("re_dyeing_from")]]=$row[csf("re_dyeing_from")]; 
			}
			else
			{
				$reding_batch_id[$row[csf("id")]]=$row[csf("id")]; 
			}*/
			$reding_batch_id[$row[csf("id")]]=$row[csf("id")];
		}
		else
		{
			$non_reding_batch_id[$row[csf("id")]]=$row[csf("id")];
		}
		
			//	$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
		}
		
		  $sql_product=("SELECT a.floor_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,b.job_no,b.extention_no,b.batch_no,b.is_sales,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.batch_qnty
		from  pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_create_dtls c where  a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id   and a.entry_form in (35)  and b.status_active=1 and a.load_unload_id=2 and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0 $date_cond $companyIDCond $w_companyIDCond2
		union
 SELECT a.floor_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,
b.job_no,b.extention_no,b.batch_no,b.is_sales,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,
b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.trims_wgt_qnty as batch_qnty 
from pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_trims_dtls c 
where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id 
and a.entry_form in (35,38)  and b.status_active=1 and a.load_unload_id=2
and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0  $date_cond $companyIDCond $w_companyIDCond2 order by  batch_date asc"); 
		
		$sql_prod=sql_select($sql_product);$tot_self_trim_qty=0;$tot_self_trimqty=0;
		$production_qty=0;$sub_trims_wgt_check_array=array();$jj=1;$self_trims_wgt_check_array=array();$jk=1;
		 foreach($sql_prod as $row)
		 {
			//$prod_arr[$row[csf("batch_date")]][$row[csf("floor_id")]]['qty']+=$row[csf("production_qty")];
			//
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
			$batch_color_range_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
			
			$batch_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			$batch_data_arr[$row[csf("id")]]['is_sales']=$row[csf("is_sales")];
			if($row[csf("job_no")]!="")
			{
			$batch_data_arr[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			}
			$batch_data_arr[$row[csf("id")]]['batch_against']=$row[csf("batch_against")];
		
			$batch_dying_floor_arr[$row[csf("floor_id")]]=$row[csf("floor_id")];
			//$prod_date_arr[$row[csf("batch_date")]]=$row[csf("batch_date")];
		 if($row[csf("entry_form")]==36 && $row[csf("batch_against")]==1 && $row[csf("result")]==1)
		 {
			
 			 $batch_noSub=$row[csf('id')];
			if (!in_array($batch_noSub,$sub_trims_wgt_check_array))
			{ $jj++;

				 $sub_trims_wgt_check_array[]=$batch_noSub;
				 $tot_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				 $tot_trim_qty=0;
			}
 			//$total_trims_weight_qty+=$tot_trim_qty;
			
			 $subcon_prod_arr[$row[csf("id")]]['production_qty_subcontact']+=$row[csf("batch_qnty")];
			 if($total_trims_weight_qty>0)
			 {
			 $subcon_prod_arr[$row[csf("id")]]['subcon_trimsWgt']=$tot_trim_qty;
			 }
		 }
		 
		 if($row[csf("entry_form")]==0 && $row[csf("batch_against")]==1 && $row[csf("result")]==1)
		 {
 			 
			 $batch_noSelf=$row[csf('id')];
			if (!in_array($batch_noSelf,$self_trims_wgt_check_array))
			{ $jk++;

				 $sub_trims_wgt_check_array[]=$batch_noSelf;
				 $tot_self_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				 $tot_self_trim_qty=0;
			}
		 
			// $subcon_prod_arr[$row[csf("id")]]['production_qty_subcontact']+=$row[csf("batch_qnty")];
			 if($tot_self_trim_qty>0)
			 {
			 $selfTrim_prod_arr[$row[csf("id")]]['subcon_trimsWgt']=$tot_self_trim_qty;
			  $tot_self_trimqty+=$tot_self_trim_qty;
			 }
		 }
		 
			$prod_arr[$row[csf("id")]]['prod_qty']+=$row[csf("batch_qnty")];
			$prod_arr[$row[csf("id")]]['batch_qty']+=$row[csf("batch_qnty")];
		 
			$prod_arr[$row[csf("id")]]['floor_id']=$row[csf("floor_id")];
			$prod_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			$prod_result_arr[$row[csf("id")]]['result']=$row[csf("result")];
			 
			$production_qty+=$row[csf("batch_qnty")];
			 
		 }
		 
		  
		 
		// $batch_cond_for_in=where_con_using_array($issue_single_batch_arr,0,"a.batch_id");
		// $batch_cond_for_in2=where_con_using_array($issue_single_batch_arr,0,"b.id");
		 
		     $sql_sub_product=("SELECT a.floor_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,b.job_no,b.extention_no,b.is_sales,b.batch_no,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form,e.party_id, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.batch_qnty
		from  pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_create_dtls c,subcon_ord_dtls d,subcon_ord_mst e where  a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and c.po_id=d.id and  d.job_no_mst=e.subcon_job and a.entry_form in (38) and e.party_id not in(426,439,444,458,425,427,428,443,392,564,565,563) and b.status_active=1 and a.load_unload_id=2 and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0 and d.status_active=1 and e.status_active=1 $date_cond $companyIDCond $w_companyIDCond2  order by batch_date "); 
		
		$sql_sub_prod=sql_select($sql_sub_product);//$tot_self_trim_qty=0;$tot_self_trimqty=0;
		$subcon_trims_wgt_check_array=array();$jjj=1;$jkk=1;
		 foreach($sql_sub_prod as $row)
		 {
			//$prod_arr[$row[csf("batch_date")]][$row[csf("floor_id")]]['qty']+=$row[csf("production_qty")];
			//
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
			$batch_color_range_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
			$batch_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			$batch_data_arr[$row[csf("id")]]['is_sales']=$row[csf("is_sales")];
			$batch_data_arr[$row[csf("id")]]['party_id']=$row[csf("party_id")];
			$batch_data_arr[$row[csf("id")]]['batch_against']=$row[csf("batch_against")];
		
			$batch_dying_floor_arr[$row[csf("floor_id")]]=$row[csf("floor_id")];
			//$prod_date_arr[$row[csf("batch_date")]]=$row[csf("batch_date")];
		 if($row[csf("entry_form")]==36 && $row[csf("batch_against")]==1 && $row[csf("result")]==1)
		 {
			 $batch_noSubcon=$row[csf('id')];
			if (!in_array($batch_noSub,$subcon_trims_wgt_check_array))
			{ $jjj++;

				 $subcon_trims_wgt_check_array[]=$batch_noSubcon;
				 $tot_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				 $tot_trim_qty=0;
			}
			//$total_trims_weight_qty+=$tot_trim_qty;
			 $subcon_prod_arr[$row[csf("id")]]['production_qty_subcontact']+=$row[csf("batch_qnty")];
			 if($total_trims_weight_qty>0)
			 {
			 $subcon_prod_arr[$row[csf("id")]]['subcon_trimsWgt']=$tot_trim_qty;
			 }
		 }
		
		 
		   $prod_arr[$row[csf("id")]]['prod_qty']+=$row[csf("batch_qnty")];
			$prod_arr[$row[csf("id")]]['batch_qty']+=$row[csf("batch_qnty")];
		 
			$prod_arr[$row[csf("id")]]['floor_id']=$row[csf("floor_id")];
			$prod_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			$prod_result_arr[$row[csf("id")]]['result']=$row[csf("result")];
			 
			$production_qty+=$row[csf("batch_qnty")];
			 
		 }
		  unset($sql_sub_prod);
		 // $batch_cond_for_in=where_con_using_array($issue_single_batch_arr,0,"b.id");
		  
		 
			$sql_qty = " (select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no,b.total_trims_weight,
			sum(case when a.service_source=1 then  b.batch_weight end) as batch_weight,
			SUM(case when a.service_source=1 and b.batch_against!=3 and c.is_sales!=1 then c.batch_qnty end) AS production_qty_inhouse,
			SUM(case when a.service_source=3 then c.batch_qnty end) AS production_qty_outbound,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=1 and b.is_sales!=1  then c.batch_qnty end) AS prod_qty_sample_without_order,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=0 and c.is_sales!=1  then c.batch_qnty end) AS prod_qty_sample_with_order,
			SUM(case WHEN c.is_sales=1 then c.batch_qnty end) AS fabric_sales_order_qty
			from pro_batch_create_dtls c, wo_po_break_down e, wo_po_details_master d, pro_fab_subprocess a,
			 lib_machine_name g, pro_batch_create_mst b 
			where a.batch_id=b.id and b.entry_form=0 and g.id=a.machine_id and b.id=c.mst_id and a.batch_id=c.mst_id and a.entry_form=35 and a.load_unload_id=2 and b.batch_against in(1)  and c.po_id=e.id and d.job_no=e.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  c.status_active=1 and c.is_deleted=0  and a.result=1  $date_cond $companyIDCond $w_companyIDCond2
			group by b.working_company_id,b.company_id,b.color_range_id,b.id,b.batch_no,b.total_trims_weight)
			union  
			( select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no,b.total_trims_weight,
			sum(case when a.service_source=1 then  b.batch_weight end) as batch_weight,
			SUM(case when a.service_source=1 and b.batch_against in(1) and c.is_sales!=1  then c.batch_qnty end) AS production_qty_inhouse,
			SUM(case when a.service_source=3 then c.batch_qnty end) AS production_qty_outbound,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=1 and c.is_sales!=1 then c.batch_qnty end) AS prod_qty_sample_without_order,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=0 and c.is_sales!=1 then c.batch_qnty end) AS prod_qty_sample_with_order,
			SUM(case WHEN c.is_sales=1 then c.batch_qnty end) AS fabric_sales_order_qty
			from pro_batch_create_dtls c, pro_batch_create_mst b, pro_fab_subprocess a, lib_machine_name g,wo_non_ord_samp_booking_mst h
			where  h.booking_no=b.booking_no and a.batch_id=b.id and a.batch_id=c.mst_id and b.entry_form=0 and g.id=a.machine_id and b.id=c.mst_id and a.entry_form=35 and a.load_unload_id=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  c.status_active=1 and c.is_deleted=0  and a.result=1 $date_cond $companyIDCond $w_companyIDCond2 
			group by b.id,b.color_range_id,b.working_company_id,b.company_id,b.batch_no,b.total_trims_weight ) 
			union
			(select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no, SUM(c.trims_wgt_qnty) AS total_trims_weight, 0 as batch_weight, 0 AS production_qty_inhouse, 0 AS production_qty_outbound, 0 AS prod_qty_sample_without_order, 0 AS prod_qty_sample_with_order, 0 AS fabric_sales_order_qty
			from pro_batch_create_mst b,pro_batch_trims_dtls c, wo_po_details_master d, pro_fab_subprocess a, lib_machine_name g 
			where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and g.id=a.machine_id and d.job_no=b.job_no and b.entry_form=136 and a.entry_form=35 and a.load_unload_id=2 and b.batch_against in(1) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.result=1 and c.status_active=1 and c.is_deleted=0  $date_cond $companyIDCond $w_companyIDCond2
			GROUP BY b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no) ";

			 // echo $sql_qty;die;
			$sql_result=sql_select( $sql_qty);

		//$fabric_sales_order_qty=0;
		$total_trims_weight_qty=0;$tttm=0;
		$j=1;$self_trims_wgt_check_array=array();
		foreach($sql_result as $row)
		{
			//$production_qty_inhouse+=$row[csf('production_qty_inhouse')];
			//$production_qty_outbound+=$row[csf('production_qty_outbound')];
			//$prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
			//$prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
			//$fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
			$batch_noSelf=$row[csf('id')];
			if (!in_array($batch_noSelf,$self_trims_wgt_check_array))
			{ 
				$j++;

				$self_trims_wgt_check_array[]=$batch_noSelf;
				$tot_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				$tot_trim_qty=0;
			}
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
			$batch_color_range_arr[$row[csf('id')]]['color_range_id']=$row[csf("color_range_id")]; 
			
			// $prod_arr[$row[csf("id")]]['batch_date']=$row[csf("process_end_date")];
			
			$total_trims_weight_qty+=$tot_trim_qty;
			$tot_sample=$row[csf("prod_qty_sample_without_order")]+$row[csf('prod_qty_sample_with_order')];
			$prod_arr2[$row[csf("id")]]['self_batch_qty']+=$row[csf("production_qty_inhouse")]+$row[csf('production_qty_outbound')];
			if($tot_sample>0)
			{
				// echo $tot_sample.',';
			$prod_arr2[$row[csf("id")]]['sample_batch_qty']+=$tot_sample;
			}
			$prod_arr2[$row[csf("id")]]['fabric_sales_order_qty']+=$row[csf("fabric_sales_order_qty")];
			if($tot_trim_qty>0)
			{
			$prod_arr2[$row[csf("id")]]['tot_trim_qty']+=$tot_trim_qty;
			$prod_TrimsArr[$row[csf("id")]]=$tot_trim_qty;
			}
			
			$tot_Qty+=$row[csf("production_qty_inhouse")]+$row[csf('production_qty_outbound')]+$tot_trim_qty+$tot_sample;
			
		//$tttm+=$tot_trim_qty;  
		}
		
		 
	
	$req_idArr=array_unique(explode(",",$req_id));
	//print_r($req_idArr);die;
	$req_idCond="and b.mst_id in($req_id)";
	//$batch_idCond="and a.batch_id in($batch_id)";
	 $batch_cond_for_in=where_con_using_array($batch_id_arr,0,"a.batch_id");
	  $sql_req = "SELECT a.ID,b.mst_id as REQ_ID,a.batch_id as BATCH_ID,a.BATCH_QTY,a.NEW_BATCH_WEIGHT,a.ENTRY_FORM,c.batch_id as RE_BATCH_ID,c.requ_no as REQU_NO from pro_recipe_entry_mst a, dyes_chem_requ_recipe_att b,dyes_chem_issue_requ_mst c where b.recipe_id=a.id  and c.id=b.mst_id  $batch_cond_for_in  and a.status_active=1 and a.is_deleted=0 and b.status_active=1";
	// echo $sql_req; die;
	$req_res = sql_select($sql_req);
	$req_id_array = array();$batch_req_no_array = array();
	foreach ($req_res as $val) 
	{
		$recipe_id_array[$val['ID']] = $val['ID'];
		$req_id_array[$val['REQ_ID']] = $val['REQ_ID'];
		$batch_req_id_array[$val['BATCH_ID']].= $val['REQ_ID'].',';
		$batch_req_id_array2[$val['BATCH_ID']]= $val['REQ_ID'];
		$batch_all_id_array[$val['BATCH_ID']]= $val['BATCH_ID'];
		
		$batch_req_no_array[$val['REQ_ID']]= $val['REQU_NO'];
		
		 
			if($val['ENTRY_FORM']==60) //Adding Topping
			{
				$batch_wgt=$val['NEW_BATCH_WEIGHT'];
			}
			else
			{
				$batch_wgt=$val['BATCH_QTY'];
			}
			//echo $batch_wgt.',';
		$batch_wgt_array[$val['BATCH_ID']] = $batch_wgt;
		$batchWgtChkk[$val['REQ_ID']].=$val['BATCH_ID'].',';
		
	}
	unset($req_res);
	//print_r($batch_req_no_array);
	

	if(count($req_id_array))
	{
		$requisition_id_cond=where_con_using_array($req_id_array,0,"a.requisition_no");
	}

	 $issue_sql = sql_select("SELECT a.MST_ID ,b.ISSUE_DATE,b.BUYER_ID,a.REQUISITION_NO,b.BATCH_NO,b.COMPANY_ID,a.ITEM_CATEGORY,a.PROD_ID,a.CONS_QUANTITY,a.CONS_AMOUNT,b.ISSUE_NUMBER from inv_transaction a,inv_issue_master b where a.mst_id=b.id and b.entry_form=5 and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.transaction_type=2   $requisition_id_cond ");
  //echo "SELECT a.MST_ID ,a.REQUISITION_NO,b.BATCH_NO,a.ITEM_CATEGORY,a.PROD_ID,a.CONS_QUANTITY,a.CONS_AMOUNT from inv_transaction a,inv_issue_master b where a.mst_id=b.id and b.entry_form=5 and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.transaction_type=2   $requisition_id_cond ";die;
	$issue_id_arr = array();$tot_amt=0;$tot_deys_issue_amt_popA=0;
	foreach ($issue_sql as $val) 
	{
		$batchArr=explode(",",$val['BATCH_NO']);
		foreach($batchArr as $bid)
		{
			$issue_item_cat_arr[$bid][$val['PROD_ID']][$val['ITEM_CATEGORY']]=$val['ITEM_CATEGORY'];
			$batch_all_id_array[$bid]=$bid;
			$batch_Req_id_array[$val['REQUISITION_NO']]=$val['BATCH_NO'];
			
		$issue_data_arr[$bid]['ISSUE_NUMBER']=$val['ISSUE_NUMBER'];
		$issue_data_arr[$bid]['ISSUE_DATE']=$val['ISSUE_DATE'];
		$issue_data_arr[$bid]['BUYER_ID']=$val['BUYER_ID'];
		$issue_data_arr[$bid]['REQUISITION_NO']=$val['REQUISITION_NO'];
		$issue_data_arr[$bid]['COMPANY_ID']=$val['COMPANY_ID'];
		
		}
		$issue_data_arr[$val['ISSUE_NUMBER']]+=$val['CONS_QUANTITY'];
		//$issue_item_cat_amt_arr[$val['PROD_ID']][$val['ITEM_CATEGORY']]+=$val['CONS_AMOUNT'];
		
		
		$issue_item_cat_amt_arr2[$val['REQUISITION_NO']][$val['PROD_ID']][$val['ITEM_CATEGORY']]+=$val['CONS_AMOUNT'];
		
		//$issue_id_arr[$val['MST_ID']] = $val['MST_ID'];
		$issue_id_arr[$val['MST_ID']] = $val['MST_ID'];
		$tot_amt+=$val['CONS_AMOUNT'];

					
	}
	
	//print_r($batch_Req_id_array);
	if(count($batch_all_id_array))
	{
		$batch_ids_cond=where_con_using_array($batch_all_id_array,0,"b.id");
		$batch_ids_cond2=where_con_using_array($batch_all_id_array,0,"b.mst_id");
	}
	
	$po_fabric_sql=sql_select("SELECT b.mst_id as BATCH_ID,a.grouping as REF_NO
	from wo_po_break_down  a,pro_batch_create_dtls b where  b.po_id=a.id   
	 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and b.is_sales=0  and a.grouping is not null $batch_ids_cond2" );
	 foreach ($po_fabric_sql as $row) 
	{
		if($row["REF_NO"])
		{
		$batch_ref_no_arr[$row["BATCH_ID"]]['ref_no'].=$row["REF_NO"].',';
		}
		
	}
	unset($po_fabric_sql);
	
	
	 
	 $batch_fabric_sql=sql_select("SELECT b.id,b.batch_no,b.batch_weight,b.booking_no, b.batch_against
	from pro_recipe_entry_mst  a,pro_batch_create_mst b  where  a.batch_id=b.id 
	 and a.status_active=1 and a.is_deleted=0 $batch_ids_cond group by  b.id,b.batch_no,b.booking_no,b.batch_weight, b.batch_against" );
	 
	foreach ($batch_fabric_sql as $row) 
	{
		//$batch_req_id=$batch_req_id_array2[$row[csf("id")]];
		//echo $batch_req_id.'d';
		$batch_wgt_arr[$row[csf("id")]]['batchWgt']=$row[csf("batch_weight")];
		$batch_wgt_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
	}
	
	 
	foreach ($issue_sql as $val) 
	{
		$item_cat= $val['ITEM_CATEGORY'];
		
		//$all_batchIds=rtrim($batch_Req_id,',');
					$batIdArr=array_unique(explode(",",$val['BATCH_NO'])); 
					$tot_batch_wgt=0;
					foreach($batIdArr as $batchId)
					{
						//$tot_batch_wgt+=$batch_description_arr[$batchId]['batch_weight'];
							$tot_batch_wgt+=$batch_wgt_arr[$batchId]['batchWgt']; 
							 $batch_weight=$batch_wgt_arr[$batchId]['batchWgt'];
					}
					$issue_item_cat_dyes=$val['CONS_AMOUNT'];
					$issue_dyesQty=$val['CONS_QUANTITY'];
				 	//  echo $issue_item_cat_dyes.'='.$tot_batch_wgt.'='.$batch_weight.'<br> ';
					if($issue_item_cat_dyes>0)
					{
						if($item_cat==6)
						{
							$tot_deys_amt=($issue_item_cat_dyes/$tot_batch_wgt)*$batch_weight;
							$dyes_issue_item_cat_amt_arr[$val['PROD_ID']]['cost']+=$tot_deys_amt;
							$dyes_issue_item_cat_amt_arr[$val['PROD_ID']]['ISSUE_NUMBER']=$val['ISSUE_NUMBER'];
							$dyes_issue_item_cat_amt_arr[$val['PROD_ID']]['COMPANY_ID']=$val['COMPANY_ID'];
							
							$tot_deys_qty=($issue_dyesQty/$tot_batch_wgt)*$batch_weight;
							$dyes_issue_item_cat_amt_arr[$val['PROD_ID']]['qty']+=$tot_deys_qty;
							
							//$tot_deys_issue_amt_popA+=($issue_item_cat_dyes/$tot_batch_wgt)*$batch_weight;
					
						}
						else if($item_cat==5 || $item_cat==7)
						{
							$tot_chekical_amt=($issue_item_cat_dyes/$tot_batch_wgt)*$batch_weight;
							$chemical_issue_item_cat_amt_arr[$val['PROD_ID']]['cost']+=$tot_chekical_amt;
							$dyes_issue_item_cat_amt_arr[$val['PROD_ID']]['ISSUE_NUMBER']=$val['ISSUE_NUMBER'];
							$dyes_issue_item_cat_amt_arr[$val['PROD_ID']]['COMPANY_ID']=$val['COMPANY_ID'];
							
							$tot_chemical_qty=($issue_dyesQty/$tot_batch_wgt)*$batch_weight;
							$chemical_issue_item_cat_amt_arr[$val['PROD_ID']]['qty']+=$tot_chemical_qty;
							
						//$tot_deys_issue_amt_popA+=($issue_item_cat_dyes/$tot_batch_wgt)*$batch_weight;
					
						}
					}
		
	}
	
	// echo $tot_deys_issue_amt_popA.'DD';die;
	
   
   if(count($req_id_array))
	{
		$batch_reqId_cond=where_con_using_array($req_id_array,0,"b.requisition_no");
		
	}
       $sql_dtls_dyes = "select a.batch_no,b.item_category,c.id as prod_id, c.product_name_details, c.unit_of_measure, c.avg_rate_per_unit, (b.cons_quantity) as qnty,(b.cons_amount) as cons_amount
	from inv_issue_master a,inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=5 and a.batch_no  is not null and b.status_active=1  $batch_reqId_cond  "; 
	$res_dyes_chemical = sql_select($sql_dtls_dyes);
	foreach ($res_dyes_chemical as $row) 
	{
		$batchArr=explode(",",$row[csf('batch_no')]);
		foreach($batchArr as $bid)
		{
			//$surplus_solution = $row[csf('surplus_solution')];
			$issue_item_cat_arr[$bid][$row[csf('prod_id')]][$row[csf('item_category')]]=$row[csf('item_category')];
		}
		$batch_desc_arr[$row[csf("prod_id")]]['desc']=$row[csf("product_name_details")];
		$batch_desc_arr[$row[csf("prod_id")]]['unit_of_measure']=$row[csf("unit_of_measure")];
	} 
	if(count($req_id_array))
	{
		$requisition_id_cond=where_con_using_array($req_id_array,0,"a.requisition_no");
	}
	
	$issue_sql = sql_select("SELECT a.MST_ID ,a.REQUISITION_NO,b.BATCH_NO,a.ITEM_CATEGORY,a.PROD_ID,a.CONS_QUANTITY,a.CONS_AMOUNT from inv_transaction a,inv_issue_master b where a.mst_id=b.id and b.entry_form=5 and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.transaction_type=2 $requisition_id_cond   ");//$w_company_idcond2

	   
	 
	$issue_id_arr = array();$tot_issueQty=0;  

	foreach ($issue_sql as $val) 
	{
		 
		$batchArr=array_filter(array_unique(explode(",",$val['BATCH_NO'])));
		foreach($batchArr as $bid)
		{
			$issue_item_cat_arr[$bid][$val['PROD_ID']][$val['ITEM_CATEGORY']]=$val['ITEM_CATEGORY'];
			if($bid!='')
			{
			$batch_all_id_array[$bid]= $bid;
			$batch_req_id_array[$bid].= $val['REQUISITION_NO'].',';
			}
		}
			$issue_item_cat_cost_arr[$val['REQUISITION_NO']][$val['PROD_ID']][$val['ITEM_CATEGORY']]=$val['CONS_QUANTITY'];
			$issue_item_cat_cost_dtls_arr[$val['REQUISITION_NO']][$val['ITEM_CATEGORY']]+=$val['CONS_QUANTITY'];
			
			$issue_item_cat_amt_dtls_arr[$val['REQUISITION_NO']][$val['ITEM_CATEGORY']]+=$val['CONS_AMOUNT'];
		
		 $ReqBatch_id_arr[$val['REQUISITION_NO']] = $val['BATCH_NO'];
		$issue_id_arr[$val['MST_ID']] = $val['MST_ID'];
		$tot_issueQty+=$val['CONS_QUANTITY'];
		
	}
//	$batch_idCond2="and c.id in($batch_id)";
	
	if(count($batch_all_id_array))
	{
		$batch_ids_cond=where_con_using_array($batch_all_id_array,0,"b.id");
		$batch_idCond2=where_con_using_array($batch_all_id_array,0,"c.id");
	}
	
	$batch_fabric_sql=sql_select("SELECT b.id,b.batch_no,b.batch_weight,b.total_trims_weight, b.batch_against
	from pro_recipe_entry_mst  a,pro_batch_create_mst b  where  a.batch_id=b.id 
	 and a.status_active=1 and a.is_deleted=0 $batch_ids_cond group by  b.id,b.batch_no,b.batch_weight,b.total_trims_weight, b.batch_against" );
	
	
	foreach ($batch_fabric_sql as $row) 
	{
		//$batch_req_id=$batch_req_id_array2[$row[csf("id")]];
		//echo $batch_req_id.'d';
		$batch_wgt_arr[$row[csf("id")]]['batchWgt']=$row[csf("batch_weight")];
		$batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
		
		if($row[csf("total_trims_weight")]>0)
		{
			$batch_wgt_arr[$row[csf("id")]]['trims_weight']=$row[csf("total_trims_weight")]; 
			
			//echo $row[csf("total_trims_weight")].','; 
		}
	}
	
	$batchIdArr = array();
	foreach(array_unique($issue_single_batch_arr) as $b_id)
	{
		$batchIdArr[$b_id] = $b_id;
	}
	$batchCond=where_con_using_array($batchIdArr,0,"c.id");
	
	//if($w_companyID) $w_companyIDCond2="and a.service_company=$w_companyID";
	//if($company_id) $companyIDCond="and a.company_id=$company_id";
	$company_cond="";
//	$company_cond = str_replace("a.company_id", "c.company_id", $company_id);	
	if($company_id) $company_cond="and c.company_id=$company_id";
	if ($w_companyID==0) $wo_company_idcond =""; else $wo_company_idcond =" and c.working_company_id='$w_companyID'";
	
	
	  $sql = "(SELECT p.id as recipe_id,p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup,a.avg_rate_per_unit, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure,b.prod_id, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
		from pro_recipe_entry_mst p, product_details_master a, pro_recipe_entry_dtls b,pro_batch_create_mst c  
		where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.ratio>0  and b.status_active=1 and b.is_deleted=0  and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 $batchCond and p.entry_form in(59,60) ) 
		union 
		(
		SELECT p.id as recipe_id,p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup,0 as avg_rate_per_unit, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, b.prod_id as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure,b.prod_id, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
			from pro_recipe_entry_mst p, pro_recipe_entry_dtls b,pro_batch_create_mst c  
			where p.id=b.mst_id and p.batch_id=c.id and b.status_active=1 and b.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.prod_id=0 and b.sub_process_id in(93,94,95,96,97,98) and p.status_active=1 and p.is_deleted=0 $batchCond and p.entry_form in(59,60) )  order by sub_seq,seq_no"; 
	 // echo $sql; die();
	$costDataArr = array();  
	$res = sql_select($sql);
	$tot_recCost=0;$tot_issue_qty=0;
	foreach ($res as $row) 
	{
		$current_stock = $row[csf('current_stock')];
		$recipe_type = $row[csf('recipe_type')];
		$surplus_solution = $row[csf('surplus_solution')];
		$pickup = $row[csf('pickup')];
		$batch_wgt = $row[csf('batch_qty')];//pickup,p.batch_qty
		 
		$batch_req_id=rtrim($batch_req_id_array[$row[csf('batch_id')]],',');
		$batch_req_idArr=array_unique(explode(",",$batch_req_id_array[$row[csf('batch_id')]]));
		 //echo $batch_req_id.'D';
		$tot_batch_wgt=$batch_wgt_array[$row[csf('batch_id')]];
		//echo  $tot_batch_wgt.'D';
		$batch_qty=$row[csf('batch_qty')];
		if($issue_item_cat_arr[$row[csf('batch_id')]][$row[csf('prod_id')]][$row[csf('item_category_id')]])
		{
			//echo $recipe_qnty.'='.$row[csf('avg_rate_per_unit')].'<br>';
			//$issue_qty=0;
			foreach($batch_req_idArr as $req_id)
			{
				if($issue_reqId_chk_arr[$row[csf('batch_id')]][$row[csf('recipe_id')]][$row[csf('prod_id')]]=='')
				{	
					$issue_qty+=$issue_item_cat_cost_arr[$req_id][$row[csf('prod_id')]][$row[csf('item_category_id')]];
					//$reqIdArr[$req_id]=$req_id;
				
					$tot_issue_qty+=$issue_qty;
					$issue_reqId_chk_arr[$row[csf('batch_id')]][$row[csf('recipe_id')]][$row[csf('prod_id')]]=111;
				 	//	echo "SDDD";
				}
				
			}
			//$issue_qty=$issue_item_cat_cost_arr[$req_id][$row[csf('prod_id')]][$row[csf('item_category_id')]];
			$issue_perKg=($issue_qty/$tot_batch_wgt);
			$issue_wgt_per=$issue_perKg*$batch_qty;
		//	echo $tot_batch_wgt.'='.$issue_qty.'='.$issue_wgt_per.'='.$issue_wgt_per*$row[csf('avg_rate_per_unit')].'<br>';
		$costDataArr[$row[csf('batch_id')]][$row[csf('item_category_id')]] +=$issue_wgt_per*$row[csf('avg_rate_per_unit')];
			//echo "AA";
		$ReqIdArr[$row[csf('batch_id')]].=$batch_req_id.',';
		//$recipe_qnty*$row[csf('avg_rate_per_unit')];
		$categryDataArr[$row[csf('batch_id')]].=$row[csf('item_category_id')].',';
		$tot_recCost+=$issue_wgt_per*$row[csf('avg_rate_per_unit')];
		}
	}
	//print_r($ReqIdArr);
	$batchCond2=where_con_using_array($issue_single_batch_arr,0,"b.id");
		$non_fabric_sql=sql_select("SELECT a.GROUPING,a.booking_no as BOOKING_NO,a.buyer_id as BUYER
		from wo_non_ord_samp_booking_mst  a,pro_batch_create_mst b where  b.booking_no_id=a.id  
		and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $batchCond2" );
		foreach ($non_fabric_sql as $row) 
		{
			if($row["BOOKING_NO"])
			{
			$batch_booking_buyer_arr[$row["BOOKING_NO"]]['BUYER']=$row["BUYER"];
			$batch_booking_buyer_arr[$row["BOOKING_NO"]]['GROUPING']=$row["GROUPING"];
			}
		}
		unset($non_fabric_sql);
		
		$fabric_sales_booking_sql=sql_select("SELECT b.booking_no as BOOKING_NO,a.within_group as WITHIN_GROUP,a.buyer_id as BUYER
		from fabric_sales_order_mst  a,pro_batch_create_mst b where  b.sales_order_id=a.id  
		 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0   $batchCond2" );
		
		 foreach ($fabric_sales_booking_sql as $row) 
		{
			if($row["BOOKING_NO"])
			{
			$batch_booking_buyer_arr[$row["BOOKING_NO"]]['BUYER']=$row["BUYER"];
			$batch_booking_buyer_arr[$row["BOOKING_NO"]]['WITHIN_GROUP']=$row["WITHIN_GROUP"];
			}
		}
		unset($fabric_sales_booking_sql);
		//359010
	$po_job_sql_buyer=sql_select("SELECT c.buyer_name as BUYER_NAME,b.id as BATCH_ID,a.grouping as REF_NO
	from wo_po_break_down  a,pro_batch_create_mst b,wo_po_details_master c  where  a.job_id=c.id  and b.job_no=c.job_no and a.job_no_mst=b.job_no
	 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0    $batchCond2" );
	/* echo "SELECT c.buyer_name,b.id as BATCH_ID,a.grouping as REF_NO
	from wo_po_break_down  a,pro_batch_create_mst b,wo_po_details_master c  where  a.job_id=c.id  and b.job_no=c.job_no and a.job_no_mst=b.job_no
	 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0    $batchCond2" ;die;*/
	 foreach ($po_job_sql_buyer as $row) 
	{
		$batch_job_buyer_arr[$row["BATCH_ID"]]['buyer_name']=$row["BUYER_NAME"];
	}
	unset($po_job_sql_buyer);
	
			//$batch_idArr=array_unique(explode(",",$batch_id));
			//echo count($issue_single_batch_arr);die;
		$tot_cost=0;
		foreach($issue_single_batch_arr as $b_id) 
		{
			
					$ReqId=rtrim($ReqIdArr[$b_id],',');
					$sum_ReqIdsArr=array_unique(explode(",",$ReqId));
					// echo $ReqId.'=';
					$first_chemi_cost=$first_dyeing_cost=0;
					$sum_issue_amt_deys=0;$sum_issue_amt_chemical=0;
					//$batch_weight=$batch_wgt_arr[$b_id]["batchWgt"];
					$batch_weight=$batch_description_arr[$b_id]["batch_weight"];
					$sum_batchIdArr='';$tot_sum_chemical_cost=$tot_sum_deys_cost=0;
					foreach($sum_ReqIdsArr as $reqId)
					{
						
						$sum_issue_amt_chemical=$issue_item_cat_amt_dtls_arr[$reqId][5]+$issue_item_cat_amt_dtls_arr[$reqId][7];
						//echo $sum_issue_amt_chemical.'d';
						$sum_issue_amt_deys=$issue_item_cat_amt_dtls_arr[$reqId][6];
					   $batch_idsAll=$ReqBatch_id_arr[$reqId]; 
						$sum_batchIds=rtrim($batch_idsAll,',');
						$sumbatIdArr=array_unique(explode(",",$sum_batchIds)); 
						$sum_tot_batch_wgt=0;
						foreach($sumbatIdArr as $batchId)
						{
						//$tot_batch_wgt+=$batch_description_arr[$batchId]['batch_weight'];
						// echo $batch_wgt_arr[$batchId]['batchWgt'].'p';
						$sum_tot_batch_wgt+=$batch_wgt_arr[$batchId]['batchWgt']; 
						}
						// echo $sum_issue_amt_chemical.'='.$sum_tot_batch_wgt.'='.$batch_weight.'<br>';
						if($sum_issue_amt_chemical>0)
						{
						$tot_sum_chemical_cost+=($sum_issue_amt_chemical/$sum_tot_batch_wgt)*$batch_weight;
						//$requ_no_arr[$reqId]['req_no'].=$batch_req_no_array[$reqId].',';
						}
						if($sum_issue_amt_deys>0)
						{
						$tot_sum_deys_cost+=($sum_issue_amt_deys/$sum_tot_batch_wgt)*$batch_weight;
						//$requ_no_arr[$reqId]['req_no'].=$batch_req_no_array[$reqId].',';
						}
						
						//$all_req_no.=$batch_req_no_array[$reqId].',';
						
						
					}
					// echo  $tot_sum_chemical_cost.',';
						if($tot_sum_chemical_cost)
						{
						//$issue_amt_perKg_chemical=($sum_issue_amt_chemical/$sum_tot_batch_wgt);
						$summissue_amt_cal_chemical=$tot_sum_chemical_cost;
						}
						else
						 {$summissue_amt_cal_chemical=0;}
						//echo $sum_issue_amt_chemical.'='.$sum_tot_batch_wgt.'='.$batch_weight.',';
						if($tot_sum_deys_cost)
						{
						//$sum_issue_amt_perKg_dyes=($sum_issue_amt_deys/$sum_tot_batch_wgt);
						$sum_issue_amt_cal_dyes=$tot_sum_deys_cost;
						} 
						else
						{$sum_issue_amt_cal_dyes=0;}
					
					$che_cost_arr[$b_id]['tot_dyes_chemical_cost']+=$sum_issue_amt_cal_dyes+$summissue_amt_cal_chemical;
					
					
					
					$tot_cost+=$sum_issue_amt_cal_dyes+$summissue_amt_cal_chemical;	
					
			 
					// echo $first_dyeing_cost."<br>";
		}
			// echo $tot_cost.'DSDS';die;
		 
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<div style="width:960px; margin-left:30px" id="report_div">
     <!--<div style="width:870px;" align="center"><input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
    <div style="width:960px; font-family:'Arial Narrow'; font-size:14px;">Total Dyes & Chemical Cost Dtls</div>
    <table align="center" cellspacing="0" width="960" border="1" rules="all" class="rpt_table" >
        <thead align="center">
    	   <tr>
                <th width="20">SL</th>
                <th width="100">Issue No</th>
                <th width="60">Issue Date</th>
                <th width="100">Requisition No</th>
                <th width="100">Issue To</th>
                <th width="90">Buyer</th>
                <th width="70">Int.Ref. No</th>
                <th width="100">Fabric Booking</th>
                <th width="80">Batch No</th>
                <th width="70">Batch Wgt.</th>
                <th width="70">Issue Qty</th>
                <th>Amount(BDT)</th>
           </tr>
		</thead>
		<?  
		//echo $req_id.'D';
		//$req_idArr=array_unique(explode(",",$req_id));
		//print_r($req_idArr);
		$i=1;$total_dyeing_qnty=$total_batchWgt=0;
		foreach($che_cost_arr as $batch_id=>$row)
		{
			 
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
				
		$batchWgt=$batch_wgt_arr[$batch_id]['batchWgt'];
		$booking_no=$batch_wgt_arr[$batch_id]['booking_no'];
		$booking_without_order=$batch_booking_without_arr[$batch_id];
		$is_sales=$batch_data_arr[$batch_id]['is_sales'];
		$req_no=$issue_data_arr[$batch_id]['REQUISITION_NO'];
		$ISSUE_NUMBER=$issue_data_arr[$batch_id]['ISSUE_NUMBER'];
		//$requ_no_arr[$batch_id]['req_no'];
		//echo $requ_no_arr[$req_no]['req_no'].', ';
		
		$requ_nos=$batch_req_no_array[$req_no]; 
	//	$requ_nos=implode(", ",array_unique(explode(",",$requ_no)));
		
		$ref_no=rtrim($batch_ref_no_arr[$batch_id]['ref_no'],',');
				
		$issue_date=$issue_data_arr[$batch_id]['ISSUE_DATE'];
		
		$buyer_id=$issue_data_arr[$batch_id]['BUYER_ID'];
		$COMPANY_ID=$issue_data_arr[$batch_id]['COMPANY_ID'];
		
		if($booking_without_order==1)
		{
			$ref_no='';
			$buyer_id=$batch_booking_buyer_arr[$booking_no]['BUYER'];
			$ref_no=$batch_booking_buyer_arr[$booking_no]['GROUPING'];
			$ref_no.=$ref_no.',';
			$ref_no=rtrim($ref_no,',');
		}
		$entry_formId=$batch_data_arr[$batch_id]['entry_form'];
		$job_no="";
		$job_no=$batch_data_arr[$batch_id]['job_no'];
		//$batch_data_arr[$row[csf("id")]]['job_no'];
		
		if($job_no!="")
		{
		$buyer_id="";
		$buyer_id=$batch_job_buyer_arr[$batch_id]['buyer_name'];
		//if($batch_id==359010) echo $job_no.'='.$buyer_id;
		}
		
		if($entry_formId==36)
		{
		$buyer_id=$batch_data_arr[$batch_id]['party_id'];
		}
		if($is_sales==1)
		{
			$sales_buyer=$batch_booking_buyer_arr[$booking_no]['BUYER'];
			$within_grp=$batch_booking_buyer_arr[$booking_no]['WITHIN_GROUP'];
			
			if($within_grp==2)  
			{
				$buyer_name=$buyer_library[$sales_buyer];
			}
			else
			{
				$buyer_name=$company_library[$sales_buyer];
			}
			
			 
		}
		else
		{
			$buyer_name=$buyer_library[$buyer_id];
		}	
		
		
		
		$ref_nos=implode(", ",array_unique(explode(",",$ref_no)));
		
				//$ISSUE_NUMBER=$row['ISSUE_NUMBER'];
				//$COMPANY_ID=$row['COMPANY_ID'];
				$tot_dyes_chemical_cost=$row['tot_dyes_chemical_cost'];
				
				$desc=$batch_desc_arr[$prod_id]['desc'];//$chmecal_costDataArr[$prod_id]['desc'];;
				$unit_of_measure=$batch_desc_arr[$prod_id]['unit_of_measure'];
				$dyes_issue_wgt_perAmt=$tot_dyes_chemical_cost; 
				$issue_wgt_perQty=$issue_data_arr[$ISSUE_NUMBER];
			if($dyes_issue_wgt_perAmt>0)
			{
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trDys_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trDys_<? echo $i; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><p><? echo $ISSUE_NUMBER; ?></p></td>
                    <td align="center"><? echo $issue_date; ?></td>
                    <td align="center" title="<?=$req_no;?>"><p><? echo $requ_nos; ?></p></td>
                    <td align="center"><p><? echo $company_library[$COMPANY_ID]; ?></p></td>
                    <td align="center" title="BuyerId=<?=$buyer_id.',Is Sales='.$is_sales; ?>"><p><? echo $buyer_library[$buyer_id]; ?></p></td>
                    
                     <td align="center" title="<??>"><? echo $ref_nos; ?></td>
                     <td align="center" title="<??>"><p><? echo $booking_no; ?></p></td>
                        
                    <td align="center"><? echo $batch_arr[$batch_id]; ?></td>
                    <td align="center"><? echo number_format($batchWgt,0); $total_batchWgt+=$batchWgt;?></td>
					<td align="right"><? echo number_format($issue_wgt_perQty,2,'.',''); $total_dyeing_qnty+=$issue_wgt_perQty; ?></td>
					<td align="right"><?  echo  number_format($dyes_issue_wgt_perAmt,2,'.',''); $total_dyeing_amount+=$dyes_issue_wgt_perAmt; ?></td>
				</tr>
			</tbody>
			<? 
			$i++;
		  }
				 
		}
		?>
        <tfoot>
        	<tr>
                <th colspan="9" align="right">Total:</th>
                 <th align="right"><? echo number_format($total_batchWgt,0); ?></th>
                <th align="right"><? echo number_format($total_dyeing_qnty,2); ?></th>
              
                <th align="right"><? echo number_format($total_dyeing_amount,2); ?></th>
            </tr>
        </tfoot>
      </table>
      
    
    <?         
	exit();
}
if ($action=="dyeing_earn_inhouse_popup")  // All Production Data popup dyeing_earn_inboundSub_popup
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Dyeing Prod. poppup Info","../../../", 1, 1, $unicode,'','');
	$date_key=str_replace("'","",$date_key);
	$date_key=explode("__",$date_key);
	$start_date=$date_key[0];
	$end_date=$date_key[1];
	$batch_id=str_replace("'","",$batch_id);
	$w_companyID=str_replace("'","",$w_companyID);
	$company_id=str_replace("'","",$company_id);
	$type_id=str_replace("'","",$type);
	//echo $date_key.'dd';die;
	$w_companyIDCond="";$companyIDCond="";$w_companyIDCond2="";
	//if($w_companyID) $w_companyIDCond="and a.working_company_id=$w_companyID";
	if($w_companyID) $w_companyIDCond2="and a.service_company=$w_companyID";
	if($company_id) $companyIDCond="and a.company_id=$company_id";
	
	
	 		if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$start_date),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$end_date),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$start_date),"","",1);
				$end_date=change_date_format(str_replace("'","",$end_date),"","",1);
			}
			
			if($start_date!="" && $end_date!="")
			{
			//$date_cond= "and to_char(c.process_end_date,'MON-YYYY')='$date_key'";
			$date_cond= "and a.process_end_date between '$start_date' and '$end_date'";
			}
			else $date_cond= "";
			
		 
			
		 $sql_batch_prod=("SELECT  b.id,b.job_no,b.company_id,b.working_company_id
		from  pro_batch_create_mst b,pro_fab_subprocess a where  a.batch_id=b.id   and a.status_active=1 and a.load_unload_id=2 and a.entry_form in (35,38)   and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 $date_cond $companyIDCond $w_companyIDCond2"); 
		$sql_batch_prod_result=sql_select( $sql_batch_prod);
		foreach($sql_batch_prod_result as $row)
		{
			$all_batch_idArr[$row[csf('id')]]=$row[csf('id')];
			$batch_job_Arr[$row[csf('id')]]['job']=$row[csf('job_no')];
			if($row[csf('working_company_id')])
			{
			$batch_job_Arr[$row[csf('id')]]['working_company_id']=$row[csf('working_company_id')];
			}
			$batch_job_Arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
		}
		
	//echo count($batch_idArr);
//	$all_batch_ids=implode(",",$all_batch_idArr);
	//echo $all_batch_ids;die;
		
		
			//echo $batch_id;
			// $batch_arr=array_unique(explode(",",$all_batch_idArr));
			 $batch_cond_for_in=where_con_using_array($all_batch_idArr,0,"b.id");
		  
		 
			$sql_qty = " (select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no,b.total_trims_weight,
			sum(case when a.service_source=1 then  b.batch_weight end) as batch_weight,
			SUM(case when a.service_source=1 and b.batch_against!=3 and c.is_sales!=1 then c.batch_qnty end) AS production_qty_inhouse,
			SUM(case when a.service_source=3 then c.batch_qnty end) AS production_qty_outbound,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=1 and b.is_sales!=1  then c.batch_qnty end) AS prod_qty_sample_without_order,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=0 and c.is_sales!=1  then c.batch_qnty end) AS prod_qty_sample_with_order,
			SUM(case WHEN c.is_sales=1 then c.batch_qnty end) AS fabric_sales_order_qty
			from pro_batch_create_dtls c, wo_po_break_down e, wo_po_details_master d, pro_fab_subprocess a,
			 lib_machine_name g, pro_batch_create_mst b 
			where a.batch_id=b.id and b.entry_form=0 and g.id=a.machine_id and b.id=c.mst_id and a.batch_id=c.mst_id and a.entry_form=35 and a.load_unload_id=2 and b.batch_against in(1)  and c.po_id=e.id and d.job_no=e.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  c.status_active=1 and c.is_deleted=0  and a.result=1  $date_cond $companyIDCond $w_companyIDCond2
			group by b.working_company_id,b.company_id,b.color_range_id,b.id,b.batch_no,b.total_trims_weight)
			union  
			( select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no,b.total_trims_weight,
			sum(case when a.service_source=1 then  b.batch_weight end) as batch_weight,
			SUM(case when a.service_source=1 and b.batch_against in(1) and c.is_sales!=1  then c.batch_qnty end) AS production_qty_inhouse,
			SUM(case when a.service_source=3 then c.batch_qnty end) AS production_qty_outbound,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=1 and c.is_sales!=1 then c.batch_qnty end) AS prod_qty_sample_without_order,
			SUM(case WHEN b.batch_against=3 and b.booking_without_order=0 and c.is_sales!=1 then c.batch_qnty end) AS prod_qty_sample_with_order,
			SUM(case WHEN c.is_sales=1 then c.batch_qnty end) AS fabric_sales_order_qty
			from pro_batch_create_dtls c, pro_batch_create_mst b, pro_fab_subprocess a, lib_machine_name g,wo_non_ord_samp_booking_mst h
			where  h.booking_no=b.booking_no and a.batch_id=b.id and a.batch_id=c.mst_id and b.entry_form=0 and g.id=a.machine_id and b.id=c.mst_id and a.entry_form=35 and a.load_unload_id=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  c.status_active=1 and c.is_deleted=0  and a.result=1  $date_cond $companyIDCond $w_companyIDCond2   
			group by b.id,b.color_range_id,b.working_company_id,b.company_id,b.batch_no,b.total_trims_weight ) 
			union
			(select b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no, SUM(c.trims_wgt_qnty) AS total_trims_weight, 0 as batch_weight, 0 AS production_qty_inhouse, 0 AS production_qty_outbound, 0 AS prod_qty_sample_without_order, 0 AS prod_qty_sample_with_order, 0 AS fabric_sales_order_qty
			from pro_batch_create_mst b,pro_batch_trims_dtls c, wo_po_details_master d, pro_fab_subprocess a, lib_machine_name g 
			where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and g.id=a.machine_id and d.job_no=b.job_no and b.entry_form=136 and a.entry_form=35 and a.load_unload_id=2 and b.batch_against in(1) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.result=1 and c.status_active=1 and c.is_deleted=0  $date_cond $companyIDCond $w_companyIDCond2
			GROUP BY b.working_company_id,b.company_id,b.id,b.color_range_id,b.batch_no) ";

			// echo $sql_qty;
			$sql_result=sql_select( $sql_qty);

		//$fabric_sales_order_qty=0;
		$total_trims_weight_qty=0;$tttm=0;
		$j=1;$self_trims_wgt_check_array=array();
		foreach($sql_result as $row)
		{
			 
			$batch_noSelf=$row[csf('id')];
			if (!in_array($batch_noSelf,$self_trims_wgt_check_array))
			{ 
				$j++;

				$self_trims_wgt_check_array[]=$batch_noSelf;
				$tot_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				$tot_trim_qty=0;
			}
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")];
			
			
			// $prod_arr[$row[csf("id")]]['batch_date']=$row[csf("process_end_date")];
			
			$total_trims_weight_qty+=$tot_trim_qty;
			$tot_sample=$row[csf("prod_qty_sample_without_order")]+$row[csf('prod_qty_sample_with_order')];
			$prod_arr2[$row[csf("id")]]['self_batch_qty']+=$row[csf("production_qty_inhouse")]+$row[csf('production_qty_outbound')];
			if($tot_sample>0)
			{
				// echo $tot_sample.',';
			$prod_arr2[$row[csf("id")]]['sample_batch_qty']+=$tot_sample;
			}
			$prod_arr2[$row[csf("id")]]['fabric_sales_order_qty']+=$row[csf("fabric_sales_order_qty")];
			if($tot_trim_qty>0)
			{
			$prod_arr2[$row[csf("id")]]['tot_trim_qty']+=$tot_trim_qty;
			$prod_TrimsArr[$row[csf("id")]]=$tot_trim_qty;
			}
			
			$tot_Qty+=$row[csf("production_qty_inhouse")]+$row[csf('production_qty_outbound')]+$tot_trim_qty+$tot_sample;
			
		//$tttm+=$tot_trim_qty;  
		}
		//echo $tot_Qty;die;
		///=================////////////===============================
		  $sql_product=("SELECT a.floor_id,a.machine_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,b.job_no,b.extention_no,b.batch_no,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.batch_qnty
		from  pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_create_dtls c where  a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id   and a.entry_form in (35)  and b.status_active=1 and a.load_unload_id=2 and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0 $date_cond $companyIDCond $w_companyIDCond2
		union
 SELECT a.floor_id,a.machine_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,
b.job_no,b.extention_no,b.batch_no,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,
b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.trims_wgt_qnty as batch_qnty 
from pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_trims_dtls c 
where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id 
and a.entry_form in (35,38)  and b.status_active=1 and a.load_unload_id=2
and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0  $date_cond $companyIDCond $w_companyIDCond2  order by  batch_date asc");
		
		$sql_prod=sql_select($sql_product);$tot_self_trim_qty=0;$tot_self_trimqty=0;
		$production_qty=0;$sub_trims_wgt_check_array=array();$jj=1;$self_trims_wgt_check_array=array();$jk=1;
		 foreach($sql_prod as $row)
		 {
			//$prod_arr[$row[csf("batch_date")]][$row[csf("floor_id")]]['qty']+=$row[csf("production_qty")];
			//
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
			$batch_data_arr[$row[csf("id")]]['batch_no']=$row[csf("batch_no")];
			$batch_data_arr[$row[csf("id")]]['floor_id']=$row[csf("floor_id")];
			$batch_data_arr[$row[csf("id")]]['machine_id']=$row[csf("machine_id")];
			$batch_data_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
			$batch_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			$batch_data_arr[$row[csf("id")]]['batch_against']=$row[csf("batch_against")];
		
			//$batch_dying_floor_arr[$row[csf("floor_id")]]=$row[csf("floor_id")];
			//$prod_date_arr[$row[csf("batch_date")]]=$row[csf("batch_date")];
		 if($row[csf("entry_form")]==36 && $row[csf("batch_against")]==1 && $row[csf("result")]==1)
		 {
			
 			 $batch_noSub=$row[csf('id')];
			if (!in_array($batch_noSub,$sub_trims_wgt_check_array))
			{ $jj++;

				 $sub_trims_wgt_check_array[]=$batch_noSub;
				 $tot_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				 $tot_trim_qty=0;
			}
 			//$total_trims_weight_qty+=$tot_trim_qty;
			//if($row[csf("id")]==15467) echo $row[csf("id")];
			 $subcon_prod_arr[$row[csf("id")]]['production_qty_subcontact']+=$row[csf("batch_qnty")];
			 if($total_trims_weight_qty>0)
			 {
			 $subcon_prod_arr[$row[csf("id")]]['subcon_trimsWgt']=$tot_trim_qty;
			 }
		 }
		 
		 if($row[csf("entry_form")]==0 && $row[csf("batch_against")]==1 && $row[csf("result")]==1)
		 {
 			 
			 $batch_noSelf=$row[csf('id')];
			if (!in_array($batch_noSelf,$self_trims_wgt_check_array))
			{ $jk++;

				 $sub_trims_wgt_check_array[]=$batch_noSelf;
				 $tot_self_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				 $tot_self_trim_qty=0;
			}
		 
			// $subcon_prod_arr[$row[csf("id")]]['production_qty_subcontact']+=$row[csf("batch_qnty")];
			 if($tot_self_trim_qty>0)
			 {
			 $selfTrim_prod_arr[$row[csf("id")]]['subcon_trimsWgt']=$tot_self_trim_qty;
			  $tot_self_trimqty+=$tot_self_trim_qty;
			 }
		 }
		 
			$prod_arr[$row[csf("id")]]['prod_qty']+=$row[csf("batch_qnty")];
			$prod_arr[$row[csf("id")]]['batch_qty']+=$row[csf("batch_qnty")];
		 
			$prod_arr[$row[csf("id")]]['floor_id']=$row[csf("floor_id")];
			$prod_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			$prod_result_arr[$row[csf("id")]]['result']=$row[csf("result")];
			 
			$production_qty+=$row[csf("batch_qnty")];
			 
		 }
		   $sql_sub_product=("SELECT a.floor_id,a.result,a.process_end_date as batch_date,b.id,b.booking_without_order,d.job_no_mst,d.id as po_id,d.cust_style_ref,e.party_id,b.extention_no,b.batch_no,b.color_range_id,b.booking_no,b.batch_weight,b.color_id,b.entry_form, b.batch_against, b.re_dyeing_from,b.total_trims_weight,c.batch_qnty
		from  pro_batch_create_mst b,pro_fab_subprocess a,pro_batch_create_dtls c,subcon_ord_dtls d,subcon_ord_mst e where  a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and c.po_id=d.id and  d.job_no_mst=e.subcon_job and a.entry_form in (38) and e.party_id not in(426,439,444,458,425,427,428,443,392,564,565,563) and b.status_active=1 and a.load_unload_id=2 and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0 and d.status_active=1 and e.status_active=1 $date_cond $companyIDCond $w_companyIDCond2  order by batch_date ");//die;
		
		$sql_sub_prod=sql_select($sql_sub_product);//$tot_self_trim_qty=0;$tot_self_trimqty=0;
		$subcon_trims_wgt_check_array=array();$jjj=1;$jkk=1;
		 foreach($sql_sub_prod as $row)
		 {
			//$prod_arr[$row[csf("batch_date")]][$row[csf("floor_id")]]['qty']+=$row[csf("production_qty")];
			//
			$issue_single_batch_arr[$row[csf("id")]]=$row[csf("id")]; 
			//$batch_color_range_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
		//	$batch_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
			//$batch_data_arr[$row[csf("id")]]['batch_against']=$row[csf("batch_against")];
		
			//$batch_dying_floor_arr[$row[csf("floor_id")]]=$row[csf("floor_id")];
			//$prod_date_arr[$row[csf("batch_date")]]=$row[csf("batch_date")];
		 if($row[csf("entry_form")]==36 && $row[csf("batch_against")]==1 && $row[csf("result")]==1)
		 {
			 $batch_noSubcon=$row[csf('id')];
			if (!in_array($batch_noSub,$subcon_trims_wgt_check_array))
			{ $jjj++;

				 $subcon_trims_wgt_check_array[]=$batch_noSubcon;
				 $tot_trim_qty=$row[csf('total_trims_weight')];
			}
			else
			{
				 $tot_trim_qty=0;
			}
			//$total_trims_weight_qty+=$tot_trim_qty;
			 $subcon_prod_arr[$row[csf("id")]]['production_qty_subcontact']+=$row[csf("batch_qnty")];
			 if($tot_trim_qty>0)
			 {
			 $subcon_prod_arr[$row[csf("id")]]['subcon_trimsWgt']=$tot_trim_qty;
			 }
		 }
		 unset($sql_sub_prod);
		 
		   $prod_arr[$row[csf("id")]]['prod_qty']+=$row[csf("batch_qnty")];
			$prod_arr[$row[csf("id")]]['batch_qty']+=$row[csf("batch_qnty")];
		 
			$prod_arr[$row[csf("id")]]['floor_id']=$row[csf("floor_id")];
			$prod_arr[$row[csf("id")]]['job_no']=$row[csf("job_no_mst")];
			
			$prod_arr[$row[csf("id")]]['batch_date']=$row[csf("batch_date")];
			$prod_result_arr[$row[csf("id")]]['result']=$row[csf("result")];
			
			$sub_batch_data_arr[$row[csf("po_id")]]['job_no']=$row[csf("job_no_mst")];
			$sub_batch_data_arr[$row[csf("po_id")]]['style_ref_no']=$row[csf("cust_style_ref")];//$po_buyer_array[$row[csf('po_id')]]['buyer']
			$sub_batch_data_arr[$row[csf("po_id")]]['buyer']=$buyer_library[$row[csf('party_id')]];
			
			 
			$production_qty+=$row[csf("batch_qnty")];
			 
		 }
		 
		
			
	// $batch_cond_for_in2=where_con_using_array($all_batch_idArr,0,"a.id");
	      $sql_data="SELECT b.id,b.batch_no,a.service_company as working_company_id,b.color_range_id,a.company_id,b.batch_date,b.color_id,a.floor_id,a.entry_form,b.batch_against,(c.batch_qnty) as batch_qty,a.process_id, a.machine_id,a.process_end_date as prod_date,b.batch_weight,c.prod_id,c.po_id,c.width_dia_type,b.booking_no,b.booking_no_id,b.extention_no, c.item_description, b.booking_without_order 
		from pro_batch_create_mst b, pro_batch_create_dtls c,pro_fab_subprocess a 
		where  c.mst_id=b.id  and a.batch_id=b.id and a.batch_id=c.mst_id and a.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0     and c.status_active=1 and c.is_deleted=0    $companyIDCond $w_companyIDCond2  $date_cond 
		order by b.id";		 
	 // echo $sql_data;
	$nameArray=sql_select($sql_data);
	foreach($nameArray as $row)
    {
		$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		if($row[csf('booking_without_order')]==1)
		{
			$non_booking_array[$row[csf('booking_no')]] = $row[csf('booking_no')];
			$non_booking_id_array[$row[csf('booking_no_id')]] = $row[csf('booking_no_id')];
			$non_batch_id_array[$row[csf('id')]] = $row[csf('booking_no_id')];
		}
		$batch_id_array[$row[csf('id')]] = $row[csf('id')];
		$const=explode(",",$row[csf('item_description')]);
		$batch_wise_const_arr[$row[csf('id')]][$const[0]]=$const[0];
		
	}
	// print_r($non_booking_array); 
 	
	
	//$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$po_buyer_array=array();
 $sql_job="select a.style_ref_no,b.id,a.buyer_name,b.po_number,b.grouping as ref_no,b.job_no_mst from wo_po_break_down b,wo_po_details_master a where  a.id=b.job_id  $po_cond";
	$sql_job_result = sql_select($sql_job);
	foreach ($sql_job_result as $val) 
	{
		$po_buyer_array[$val[csf('id')]]['buyer']=$buyer_library[$val[csf('buyer_name')]];
		$job_buyer_array[$val[csf('job_no_mst')]]['buyer']=$buyer_library[$val[csf('buyer_name')]];
		$job_buyer_array[$val[csf('job_no_mst')]]['style']=$val[csf('style_ref_no')];
		$job_buyer_array[$val[csf('job_no_mst')]]['ref_no']=$val[csf('ref_no')];
		
		$po_buyer_array[$val[csf('id')]]['ref_no']=$val[csf('ref_no')];
		$po_buyer_array[$val[csf('id')]]['job_no']=$val[csf('job_no_mst')];
		$po_buyer_array[$val[csf('id')]]['style_ref_no']=$val[csf('style_ref_no')];
	}
	unset($sql_job_result);
	
	 $non_booking_cond_for_in=where_con_using_array($non_booking_id_array,0,"a.id");

	 $sql_non_booking="select a.grouping,b.id,a.buyer_id from pro_batch_create_mst b,wo_non_ord_samp_booking_mst a where  a.id=b.booking_no_id  and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0   $non_booking_cond_for_in";
	$sql_non_booking_result = sql_select($sql_non_booking);
	foreach ($sql_non_booking_result as $val) 
	{
		$po_non_buyer_array[$val[csf('id')]]['buyer']=$buyer_library[$val[csf('buyer_id')]];
		$po_non_buyer_array[$val[csf('id')]]['ref_no']=$val[csf('grouping')];
	//	$po_buyer_array[$val[csf('id')]]['job_no']=$val[csf('job_no_mst')];
		//$po_buyer_array[$val[csf('id')]]['style_ref_no']=$val[csf('style_ref_no')];
	}
	unset($sql_non_booking_result);

	  
	foreach($nameArray as $row)
    {
		//$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
		$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
		$process_idArr=array_unique(explode(",",$row[csf('process_id')]));
		$all_prod_date_arr[$row[csf('prod_date')]].=$row[csf('id')].',';
		//$process_idArr=array_unique(explode(",",$row[csf('process_id')]));
		$dying_amt=0;
		 
		 
					$const=explode(",",$row[csf('item_description')]);
					 
					$prod_date_qty_arr[$row[csf('id')]]['self_amount']+=$dying_amt;//$row[csf('batch_qty')]*$conv_rate;
					$prod_date_qty_arr[$row[csf('id')]]['self_qty']+=$row[csf('batch_qty')];
				    
					$prod_date_qty_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
					$prod_date_qty_arr[$row[csf('id')]]['prod_date']=$row[csf('prod_date')];
					$prod_date_qty_arr[$row[csf('id')]]['working_company']=$row[csf('working_company_id')];
					$prod_date_qty_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
					$prod_date_qty_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
					
					if($row[csf('entry_form')]!=38)
					{
						//$non_batch_id_array[$row[csf('id')]]
						if($row[csf('booking_without_order')]==1)
						{
							$batch_data_arr[$row[csf('id')]]['buyer']=$po_non_buyer_array[$row[csf('id')]]['buyer'];
							$batch_data_arr[$row[csf('id')]]['ref_no'].=$po_non_buyer_array[$row[csf('id')]]['ref_no'].',';
						}
						else
						{
						//$batch_job_idArr[$row[csf('id')]]
						//
						$job_buyer=$po_buyer_array[$row[csf('po_id')]]['buyer'];
						if($row[csf('entry_form')]==136)
						{
						//$job_no=$po_buyer_array[$row[csf('po_id')]]['job_no'];
							//$job_buyer=$job_buyer_array[$job_no]['buyer'];
						}
						$batch_data_arr[$row[csf('id')]]['buyer']=$job_buyer;
						$batch_data_arr[$row[csf('id')]]['ref_no'].=$po_buyer_array[$row[csf('po_id')]]['ref_no'].',';
						$batch_data_arr[$row[csf('id')]]['job_no'].=$po_buyer_array[$row[csf('po_id')]]['job_no'].',';
						$batch_data_arr[$row[csf('id')]]['style_ref_no'].=$po_buyer_array[$row[csf('po_id')]]['style_ref_no'].',';
						}
					}
					else
					{
						//$sub_batch_data_arr[$row[csf("id")]]['job_no']=$row[csf("job_no_mst")];
						//$sub_batch_data_arr[$row[csf("id")]]['style_ref_no']=$row[csf("cust_style_ref")];//$po_buyer_array[$row[csf('po_id')]]['buyer']
						//$sub_batch_data_arr[$row[csf("id")]]['buyer']=$row[csf("party_id")];
						
			
						$batch_data_arr[$row[csf('id')]]['buyer']=$sub_batch_data_arr[$row[csf('po_id')]]['buyer'];
						//$prod_date_qty_arr[$row[csf('id')]]['ref_no'].=$po_buyer_array[$row[csf('po_id')]]['ref_no'].',';
						$batch_data_arr[$row[csf('id')]]['job_no'].=$sub_batch_data_arr[$row[csf('po_id')]]['job_no'].',';
						$batch_data_arr[$row[csf('id')]]['style_ref_no'].=$sub_batch_data_arr[$row[csf('po_id')]]['style_ref_no'].',';
					}
					
					$prod_date_qty_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
					$prod_date_qty_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
					$prod_date_qty_arr[$row[csf('id')]]['constructuon']=$const[0];
					
				$batch_data_arr[$row[csf("id")]]['batch_against']=$row[csf("batch_against")];
				$batch_data_arr[$row[csf("id")]]['working_company']=$row[csf("working_company_id")];
				$batch_data_arr[$row[csf("id")]]['company_id']=$row[csf("company_id")];
				$batch_data_arr[$row[csf("id")]]['batch_no']=$row[csf("batch_no")];
				$batch_data_arr[$row[csf("id")]]['floor_id']=$row[csf("floor_id")];
				$batch_data_arr[$row[csf("id")]]['machine_id']=$row[csf("machine_id")];
				$batch_data_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
				//$batch_data_arr[$row[csf("id")]]['style_ref_no'].=$po_buyer_array[$row[csf('po_id')]]['style_ref_no'].',';
				//$batch_data_arr[$row[csf("id")]]['buyer']=$po_buyer_array[$row[csf('po_id')]]['buyer'];
				//$batch_data_arr[$row[csf("id")]]['job_no'].=$po_buyer_array[$row[csf('po_id')]]['job_no'].',';
				//$batch_data_arr[$row[csf("id")]]['ref_no'].=$po_buyer_array[$row[csf('po_id')]]['ref_no'].',';
			
				 
		
		
		
	}
	 ob_start();
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
         <div id="report_container"> </div>
        <?
        $table_width=1030;
		$company_short_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
		?>
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
         <caption> <b>Batch Details</b></caption>
            <thead>
                <tr>
                    <th width="20" >SL</th>
                    <th width="80">LC Company</th>
                    <th width="80">Working Company</th>
					<th width="80">Buyer</th>
                    <th width="80">Internal Ref. </th>
                    <th width="100">Job no</th>
                    <th width="80">Style</th>
                    <th width="60">Floor Name</th>
                    <th width="80">Machine</th>
                    <th width="70">Batch No</th>
                    <th width="80">Fabric Color</th>
                    <th width="80">Color Range</th>
                    <th width="70">Batch Qty</th>
                    <th width="70">Sales Batch Qty</th>
                    
                </tr>
            </thead>
            <tbody>
                 <?
				  $batch_id_cond_for_in2=where_con_using_array($issue_single_batch_arr,0,"b.id");
				$sales_dataArr = sql_select("SELECT b.id as batch_id,a.id,a.job_no,a.sales_booking_no,a.within_group,b.batch_weight,a.buyer_id,a.style_ref_no, a.po_buyer from fabric_sales_order_mst a,pro_batch_create_mst b where a.id=b.sales_order_id and a.status_active=1  and b.status_active=1  $batch_id_cond_for_in2");
				//echo "SELECT b.id as batch_id,a.id,a.job_no,a.sales_booking_no,a.within_group,b.batch_weight,a.buyer_id,a.style_ref_no, a.po_buyer from fabric_sales_order_mst a,pro_batch_create_mst b where a.id=b.sales_order_id and a.status_active=1  and b.status_active=1  $batch_id_cond_for_in2";
					 
					foreach ($sales_dataArr as $row)
					{
						//$sales_batch_arr[$row[csf('batch_id')]]=$row[csf('job_no')];
						$sales_batchQty_Arr[$row[csf('batch_id')]]['sales_batch_weight']=$row[csf('batch_weight')];
						
					}
					unset($sales_dataArr);
					
                        $i=1;$tot_dyeing_qty=$tot_dyeing_amount=$tot_dyeing_sales_qty=0;
                        foreach($issue_single_batch_arr as $bId=>$row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//if($bId==15467) echo "A";
							$batch_against_id=$batch_data_arr[$bId]['batch_against'];
							$batch_job=$batch_job_Arr[$bId]['job'];
							
							$job_working_company_id=$batch_job_Arr[$bId]['working_company_id'];
							$job_company_id=$batch_job_Arr[$bId]['company_id'];
							
							$company_id=$batch_data_arr[$bId]['company_id'];
							$w_company_id=$batch_data_arr[$bId]['working_company'];
														
							if($company_id=='') $company_id=$job_company_id;
							if($w_company_id=='') $w_company_id=$job_working_company_id;
							
							$color_range_id=$batch_data_arr[$bId]['color_range_id'];
							$machine_id=$batch_data_arr[$bId]['machine_id'];
							$batch_no=$batch_data_arr[$bId]['batch_no']; 
							$ref_no=rtrim($batch_data_arr[$bId]['ref_no'],',');
							$ref_noArr=array_unique(explode(",",$ref_no));
							$job_no=rtrim($batch_data_arr[$bId]['job_no'],',');
							if($job_no=='') $job_no=$batch_job;
							$job_noArr=array_unique(explode(",",$job_no));
							$style_ref_no=rtrim($batch_data_arr[$bId]['style_ref_no'],',');
							$style_ref_noArr=array_unique(explode(",",$style_ref_no));
							$prod_Trims=0;
							if($batch_against_id==1)
							{
							$prod_Trims=$prod_TrimsArr[$bId];
							}
							$subcon_prodqty=$subcon_prod_arr[$bId]['production_qty_subcontact'];
							$self_batch_qty=$prod_arr2[$bId]['self_batch_qty']+$prod_arr2[$bId]['sample_batch_qty']+$prod_Trims+$subcon_prodqty;
							
							$sales_batch_weight=$sales_batchQty_Arr[$bId]['sales_batch_weight'];
							
							$job_buyer=$job_buyer_array[$batch_job]['buyer'];
							$job_ref=$job_buyer_array[$batch_job]['ref_no'];
							$job_style=$job_buyer_array[$batch_job]['style'];
							//if($batch_job=='AOPL-22-00097') echo $job_style.'D'.$job_buyer;else echo " ";
							if($batch_data_arr[$bId]['buyer']=='')
							{
								$style_ref_noArr='';
								$buyer=$job_buyer;
								$style_ref_noArr[$job_style]=$job_style;
								//if($batch_job=='AOPL-22-00097') echo $style_ref_noArr.'='.$job_buyer;
								$ref_noArr[$job_ref]=$job_ref;
							}
							else
							{
								$buyer=$batch_data_arr[$bId]['buyer'];
								$style_ref_noArr=$style_ref_noArr;
								$ref_noArr=$ref_noArr;
							}
							if(($self_batch_qty+$sales_batch_weight)>0)
							{
                         ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
                                <td  width="20"><? echo $i; ?></td>
                                <td  width="80"><? echo $company_short_library[$company_id]; ?></td>
                                <td  width="80"><? echo $company_short_library[$w_company_id]; ?></td>
                                <td  width="80" title="<? echo $job_buyer.'='.$batch_data_arr[$bId]['buyer'];?>"><? echo $buyer; ?></td>
								<td  width="80"><? echo implode(",",$ref_noArr); ?></td>
                                <td  width="100" title="<? //echo $row[('color_id')];?>"><p><? echo implode(",",$job_noArr); ?></p></td>
                                <td  width="80" title="<? //echo $row[('color_id')];?>"><p><? echo implode(",",$style_ref_noArr); ?></p></td>
                                <td  width="60"><? echo $floor_library[$row[('floor_id')]]; ?></td>
                                <td  width="80" title="<? echo $machine_id;?>"><p><? echo $machine_arr[$machine_id]; ?></p></td>
                                <td  width="70" align="center"><? echo $batch_no;?></td>
                                
                                <td  width="80" align="center" title=""><? echo $color_library[$row[('color_id')]]; ?></td>
                                <td  width="80" align="center" title=""><? echo $color_range[$color_range_id]; ?></td>
                                <td  width="80" align="right" title=""><? echo number_format($self_batch_qty,2); ?></td>
                                  <td  width="80" align="right" title=""><? echo number_format($sales_batch_weight,2); ?></td>
                              
                            </tr>
                        <?
                         $i++;
						 $tot_dyeing_qty+=$self_batch_qty;$tot_dyeing_sales_qty+=$sales_batch_weight;
						 $tot_dyeing_amount+=$row[('self_amount')];
                         }
						}
                        ?>
                        <tr bgcolor="#CCCCCC"> 
                        <td align="right" colspan="12"><strong>Total</strong></td>
                       
                        <td align="right"><? echo number_format($tot_dyeing_qty,2); ?>&nbsp;</td> 
                        <td align="right"><? echo number_format($tot_dyeing_sales_qty,2); ?>&nbsp;</td>
                        
                    </tr>
            </tbody>
		</table>
        <?
		$html=ob_get_contents();
			ob_flush();
			foreach (glob(""."*.xls") as $filename) 
			{
			   @unlink($filename);
			}
			
			//html to xls convert
			$name=time();
			$name=$user_id."_".$name.".xls";
			$create_new_excel = fopen(''.$name, 'w');	
			$is_created = fwrite($create_new_excel,$html);
		if($type_id==2)
		{
            
	
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
    <?
			}
	?>
        </div>
        <?
}
?>	
