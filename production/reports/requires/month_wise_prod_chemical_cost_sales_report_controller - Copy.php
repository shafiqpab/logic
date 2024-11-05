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
//$order_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
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

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$working_company= str_replace("'","",$cbo_working_company_name);
	$cbo_company_name= str_replace("'","",$cbo_company_name);
	$txt_batch_id=str_replace("'","",$txt_batch_id);
	$batch_no=trim(str_replace("'","",$txt_batch_no));
	$cbo_year=str_replace("'","",$cbo_year_selection);
	$batch_type=str_replace("'","",$cbo_batch_type);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	
	
	 
	
	
	if($batch_type==0)
	$search_field_cond_batch="and a.entry_form in (0,36)";
	else if($batch_type==1)
	$search_field_cond_batch="and a.entry_form=0 ";
	else if($batch_type==2)
	$search_field_cond_batch="and a.entry_form=36";
	else if($batch_type==4)
	$search_field_cond_batch="and a.entry_form=136";
	else if($batch_type==5)
	$search_field_cond_batch="and a.batch_against=3";// sample
	else 
	$search_field_cond_batch="and a.entry_form in(0,36) and b.batch_against in(2) and  b.re_dyeing_from!=0 ";
	
	$rec_date_cond="";$issue_date_cond='';
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
				$date_cond=" and c.process_end_date between '$start_date' and '$end_date'";
				$rec_date_cond=" and a.receive_date between '$start_date' and '$end_date'";
				$issue_date_cond=" and a.issue_date between '$start_date' and '$end_date'";
				//$date_cond_dyeing=" and a.process_end_date between '$start_date' and '$end_date'";
				//$ship_date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		
	
	}
	 

	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no ='$batch_no' ";
	if ($batch_no=="") $batch_no_cond2=""; else $batch_no_cond2=" and c.batch_no ='$batch_no' ";
 
	if ($cbo_company_name==0) $lcCompany_cond=""; else $lcCompany_cond="  and a.company_id=".$cbo_company_name." ";
	if ($working_company==0) $workingCompany_cond=""; else $workingCompany_cond="  and a.working_company_id=".$working_company." ";
	if ($working_company==0) $workingCompany_cond2=""; else $workingCompany_cond2="  and c.service_company=".$working_company." ";
	if ($working_company==0) $knit_company_cond=""; else $knit_company_cond="  and a.knitting_company=".$working_company." ";
	
	if ($cbo_company_name==0) $lcCompany_cond2=""; else $lcCompany_cond2="  and a.company_name=".$cbo_company_name." ";
	if ($working_company==0) $workingCompany_cond2=""; else $workingCompany_cond2="  and a.working_company_name=".$working_company." ";
	
//	if ($cbo_floor_id==0) $floor_cond=""; else $floor_cond="  and c.floor_id=".$cbo_floor_id." ";
	//if ($txt_machine_id==0) $mc_cond=""; else $mc_cond="  and c.machine_id=".$txt_machine_id." ";
	 $cbo_year=str_replace("'","",$cbo_year_selection);
	 $year_cond = "";
	if($cbo_year) $year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year";
	if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	
	if($txt_ref_no!='')
	{
		if($batch_type==4) //Trim batch
		{
		  $sql_po="SELECT a.id as batch_id,c.style_ref_no,c.season_buyer_wise,b.id,b.po_number,b.file_no,b.grouping as ref_no,b.job_no_mst from wo_po_details_master c,wo_po_break_down b,pro_batch_create_mst a where c.id=b.job_id and b.job_no_mst=a.job_no  and  b.status_active=1 and b.is_deleted=0 and   a.status_active=1 and a.is_deleted=0  $search_field_cond_batch $year_cond $batch_no_cond  $lcCompany_cond  $workingCompany_cond ";
		}
		else
		{
			$sql_po="SELECT a.style_ref_no,a.season_buyer_wise,b.id,b.po_number,b.file_no,b.grouping as ref_no,b.job_no_mst from wo_po_details_master a,wo_po_break_down b  where a.id=b.job_id and   b.status_active=1 and b.is_deleted=0 $ref_cond $lcCompany_cond2 $workingCompany_cond2  ";
		}
		// echo $sql_po;
		$res_po=sql_select($sql_po);
		$all_po_id='';
		foreach($res_po as $row) //$job_no_arr
		{			
			if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
			$trim_batch_idArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
			$po_idArr[$row[csf('id')]]=$row[csf('id')];
		}
		
		$po_id_cond=where_con_using_array($po_idArr,0,"po_id");
	
		if($batch_type==4) //Trim batch
		{
		 	$po_cond_for_in='';
			$po_cond_for_in=where_con_using_array($trim_batch_idArr,0,"mst_id");
			//echo "SELECT mst_id as batch_id from pro_batch_trims_dtls  where   status_active=1 and is_deleted=0 $po_cond_for_in";die;
			$sql_batch=sql_select("SELECT mst_id as batch_id from pro_batch_trims_dtls  where   status_active=1 and is_deleted=0 $po_cond_for_in");
		}
		else
		{
			$sql_batch=sql_select("SELECT mst_id as batch_id from pro_batch_create_dtls  where   status_active=1 and is_deleted=0 $po_id_cond");
			//echo "SELECT mst_id as batch_id from pro_batch_create_dtls  where   status_active=1 and is_deleted=0 $po_id_cond";
		}
		 $all_batch_id='';
		foreach($sql_batch as $row) 
		{
			//if($all_batch_id=="") $all_batch_id=$row[csf('batch_id')]; else $all_batch_id.=",".$row[csf('batch_id')];
			$all_batch_idArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
		}
		
		$all_batch_id_cond=where_con_using_array($all_batch_idArr,0,"a.id");
	}
	
		
	
	
	
	
       $sql_data="(SELECT a.id,a.batch_no,a.color_id,a.floor_id,a.color_range_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,c.process_id, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c 
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id  and c.entry_form in(35,38)   and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
  and a.status_active=1 and a.is_deleted=0  and b.po_id>0  $all_batch_id_cond $search_field_cond_batch $year_cond $batch_no_cond $date_cond  $lcCompany_cond  $workingCompany_cond 
  union
  SELECT a.id,a.batch_no,a.color_id,a.floor_id,a.color_range_id,c.entry_form,a.batch_against,(b.trims_wgt_qnty) as batch_qty,c.process_id, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,0 as prod_id,0 as po_id,0 as width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description as item_description, a.booking_without_order 
		from pro_batch_create_mst a, pro_batch_trims_dtls b,pro_fab_subprocess c 
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id   and c.entry_form in(35,38)   and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
  and a.status_active=1 and a.is_deleted=0  $all_batch_id_cond $search_field_cond_batch $year_cond $batch_no_cond $date_cond  $lcCompany_cond  $workingCompany_cond 
 ) order by prod_date" ;		 
	   // echo $sql_data; 
	$batch_against_chk=array(2);
	$nameArray=sql_select($sql_data);
	$white_wash_colorArr=array(4,7,19);
	$not_white_wash_colorArr=array(3,13,14,15,16,17,18,25,26,27,28);
	$double_dyeing_colorArr=array(13,14,15,16,17,18,25,26,27,28);
	foreach($nameArray as $row)
    {
		$mon_year=date('M-Y',strtotime($row[csf('prod_date')]));
		$batch_mon_arr[$row[csf('id')]]=$row[csf('id')];
		$prod_data_mon_arr[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
		$prod_data_mon_arr[$row[csf('id')]]['mon_year']=$mon_year;
		
		$color_range_id=$row[csf('color_range_id')];
		/* if(in_array($color_range_id,$white_wash_colorArr))
		 {
				 
				$prod_date_qty_arr[$mon_year]['white_wash_qty']+=$row[csf('batch_qty')]; 
		 }
		 if(!in_array($color_range_id,$not_white_wash_colorArr))
		 {
				 
				$prod_date_qty_arr[$mon_year]['avg_color_qty']+=$row[csf('batch_qty')]; 
		 }
		 if($color_range_id==3)
		 {
				 
				$prod_date_qty_arr[$mon_year]['black_color_qty']+=$row[csf('batch_qty')]; 
		 }
		 if(in_array($color_range_id,$double_dyeing_colorArr))
		 {
				 
				$prod_date_qty_arr[$mon_year]['double_dyeing_qty']+=$row[csf('batch_qty')]; 
		 }*/
	//	$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		 
		$batch_id_array[$row[csf('id')]] = $row[csf('id')];
		$batch_id_mon_arr[$mon_year] .= $row[csf('id')].',';
		
		$prod_date_arr[$mon_year]=$row[csf('prod_date')];
		//$prod_date_qty_arr[$mon_year]['self_qty']+=$row[csf('batch_qty')];
		
	}
//print_r($prod_date_arr);
$sql_qty = " (select a.working_company_id,a.company_id,a.id,a.batch_no,a.total_trims_weight,
			sum(case when c.service_source=1 then  a.batch_weight end) as batch_weight,
			SUM(case when c.service_source=1 and a.batch_against!=3 and b.is_sales!=1 then b.batch_qnty end) AS production_qty_inhouse,
			SUM(case when c.service_source=3 then b.batch_qnty end) AS production_qty_outbound,
			SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 and a.is_sales!=1  then b.batch_qnty end) AS prod_qty_sample_without_order,
			SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 and b.is_sales!=1  then b.batch_qnty end) AS prod_qty_sample_with_order,
			SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty
			from pro_batch_create_mst a,pro_batch_create_dtls b, wo_po_break_down e, wo_po_details_master d, pro_fab_subprocess c,
			 lib_machine_name g 
			where c.batch_id=a.id and a.entry_form=0 and g.id=c.machine_id and a.id=b.mst_id and c.batch_id=b.mst_id and c.entry_form=35 and c.load_unload_id=2 and a.batch_against in(1)  and b.po_id=e.id and d.id=e.job_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  c.status_active=1 and c.is_deleted=0  and c.result=1  $search_field_cond_batch $year_cond $batch_no_cond $date_cond  $lcCompany_cond  $workingCompany_cond 
			group by a.working_company_id,a.company_id,a.id,a.batch_no,a.total_trims_weight)
			union  
			( select a.working_company_id,a.company_id,a.id,a.batch_no,a.total_trims_weight,
			sum(case when c.service_source=1 then  a.batch_weight end) as batch_weight,
			SUM(case when c.service_source=1 and a.batch_against in(1) and b.is_sales!=1  then b.batch_qnty end) AS production_qty_inhouse,
			SUM(case when c.service_source=3 then b.batch_qnty end) AS production_qty_outbound,
			SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 and a.is_sales!=1 then b.batch_qnty end) AS prod_qty_sample_without_order,
			SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 and a.is_sales!=1 then b.batch_qnty end) AS prod_qty_sample_with_order,
			SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty
			from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess c, lib_machine_name g,wo_non_ord_samp_booking_mst h
			where  h.booking_no=a.booking_no and c.batch_id=a.id and c.batch_id=b.mst_id and a.entry_form=0 and g.id=c.machine_id and a.id=b.mst_id and c.entry_form=35 and c.load_unload_id=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  c.status_active=1 and c.is_deleted=0  and c.result=1   $search_field_cond_batch $year_cond $batch_no_cond $date_cond  $lcCompany_cond  $workingCompany_cond 
			group by a.id,a.working_company_id,a.company_id,a.batch_no,a.total_trims_weight ) 
			union
			(select a.working_company_id,a.company_id,a.id,a.batch_no, SUM(b.trims_wgt_qnty) AS total_trims_weight, 0 as batch_weight, 0 AS production_qty_inhouse, 0 AS production_qty_outbound, 0 AS prod_qty_sample_without_order, 0 AS prod_qty_sample_with_order, 0 AS fabric_sales_order_qty
			from pro_batch_create_mst a,pro_batch_trims_dtls b, wo_po_details_master d, pro_fab_subprocess c, lib_machine_name g 
			where c.batch_id=a.id and a.id=b.mst_id and c.batch_id=b.mst_id and g.id=c.machine_id and d.job_no=a.job_no and a.entry_form=136 and c.entry_form=35 and c.load_unload_id=2 and a.batch_against in(1) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.result=1 and c.status_active=1 and c.is_deleted=0   $search_field_cond_batch $year_cond $batch_no_cond $date_cond  $lcCompany_cond  $workingCompany_cond 
			GROUP BY a.working_company_id,a.company_id,a.id,a.batch_no) ";

			 //echo $sql_qty;die;
			$sql_result=sql_select( $sql_qty);

		//$fabric_sales_order_qty=0;
		$total_trims_weight_qty=0;
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
			$prod_arr2[$row[csf("id")]]['tot_trim_qty']+=$tot_trim_qty;
			$prod_TrimsArr[$row[csf("id")]]=$tot_trim_qty;
			
		}
		
		 $sql_sub_product=("SELECT c.floor_id,c.result,c.process_end_date as prod_date,a.id,a.booking_without_order,a.job_no,a.extention_no,a.batch_no,a.color_range_id,a.booking_no,a.batch_weight,a.color_id,a.entry_form, a.batch_against, a.re_dyeing_from,a.total_trims_weight,b.batch_qnty
		from  pro_batch_create_mst a,pro_fab_subprocess c,pro_batch_create_dtls b,subcon_ord_dtls d,subcon_ord_mst e where  a.id=b.mst_id and a.id=c.batch_id and c.batch_id=b.mst_id and b.po_id=d.id and  d.job_no_mst=e.subcon_job and c.entry_form in (38) and e.party_id not in(426,439,444,458,425,427,428,443,392,564,565,563) and b.status_active=1 and c.load_unload_id=2 and b.is_deleted=0 and c.is_deleted=0 and a.is_deleted=0 and d.status_active=1 and e.status_active=1  $search_field_cond_batch $year_cond $batch_no_cond $date_cond  $lcCompany_cond  $workingCompany_cond ");//die;
		
		$sql_sub_prod=sql_select($sql_sub_product);
		foreach ($sql_sub_prod as $row) 
		{
			$mon_year=date('M-Y',strtotime($row[csf('prod_date')]));
			$batch_mon_arr[$row[csf('id')]]=$row[csf('id')];
			$batch_id_array[$row[csf('id')]] = $row[csf('id')];
			$batch_id_mon_arr[$mon_year] .= $row[csf('id')].',';
			$prod_date_arr[$mon_year]=$row[csf('prod_date')];
			$sub_prod_qty_arr[$row[csf('id')]]+=$row[csf('batch_qnty')];
		}
		unset($sql_sub_prod);
		
		
		
		
	// ========================================Main Query End============================================
	
	$batch_id_cond=where_con_using_array($batch_id_array,0,"a.batch_id");
	// =============================================================
	$batch_all_id_array=array();
	//$batch_id_conds = ($batch_type==3) ? str_replace("b.id", "a.batch_id", $re_dying_cond2) : str_replace("b.re_dyeing_from", "a.batch_id", $re_dying_cond);
	  $sql_req = "SELECT a.ID,b.mst_id as REQ_ID,a.batch_id as BATCH_ID,a.BATCH_QTY,a.BATCH_QTY,a.NEW_BATCH_WEIGHT,a.ENTRY_FORM,c.batch_id as RE_BATCH_ID from pro_recipe_entry_mst a, dyes_chem_requ_recipe_att b,dyes_chem_issue_requ_mst c where b.recipe_id=a.id  and c.id=b.mst_id $batch_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 ";
	//  echo $sql_req;
	$result_req = sql_select($sql_req);
	$req_id_array = array();
	foreach ($result_req as $val) 
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
	    //echo "SELECT a.MST_ID ,a.REQUISITION_NO,b.BATCH_NO,a.ITEM_CATEGORY,a.PROD_ID,a.CONS_QUANTITY,a.CONS_AMOUNT from inv_transaction a,inv_issue_master b where a.mst_id=b.id and b.entry_form=5 and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.transaction_type=2 $requisition_id_cond  $w_company_idcond2 ";
	 
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
			//$issue_item_cat_cost_arr[$val['REQUISITION_NO']][$val['PROD_ID']][$val['ITEM_CATEGORY']]=$val['CONS_QUANTITY'];
			//$issue_item_cat_cost_dtls_arr[$val['REQUISITION_NO']][$val['ITEM_CATEGORY']]+=$val['CONS_QUANTITY'];
			
			$issue_item_cat_amt_dtls_arr[$val['REQUISITION_NO']][$val['ITEM_CATEGORY']]+=$val['CONS_AMOUNT'];
		
		$ReqBatch_id_arr[$val['REQUISITION_NO']] = $val['BATCH_NO'];
		//$issue_id_arr[$val['MST_ID']] = $val['MST_ID'];
		//$tot_issueQty+=$val['CONS_QUANTITY'];
		
	}
	// echo count($batch_all_id_array);die;
	if(count($batch_all_id_array))
	{
		$batch_ids_cond=where_con_using_array($batch_all_id_array,0,"b.id");
	}
	
	$batch_fabric_sql=sql_select("SELECT b.id,b.batch_no,b.batch_weight, b.batch_against
	from pro_recipe_entry_mst  a,pro_batch_create_mst b  where  a.batch_id=b.id 
	 and a.status_active=1 and a.is_deleted=0 $batch_ids_cond group by  b.id,b.batch_no,b.batch_weight, b.batch_against" );
	/* echo "SELECT b.id,b.batch_no,b.batch_weight, b.batch_against
	from pro_recipe_entry_mst  a,pro_batch_create_mst b  where  a.batch_id=b.id 
	 and a.status_active=1 and a.is_deleted=0 $batch_ids_cond group by  b.id,b.batch_no,b.batch_weight, b.batch_against";die;*/
	 foreach ($batch_fabric_sql as $row) 
	{
		//$batch_req_id=$batch_req_id_array2[$row[csf("id")]];
		//echo $batch_req_id.'d';
		$batch_wgt_arr[$row[csf("id")]]['batchWgt']=$row[csf("batch_weight")];
	}
	
	$batch_sub_sql=sql_select("SELECT b.id,a.amount
	from subcon_inbound_bill_dtls  a,pro_batch_create_mst b  where  a.batch_id=b.id 
	 and a.status_active=1 and a.is_deleted=0 $batch_ids_cond " );
	 
	 foreach ($batch_sub_sql as $row) 
	{
		//$batch_req_id=$batch_req_id_array2[$row[csf("id")]];
		//echo $batch_req_id.'d';
		$dye_fin_bill_arr[$row[csf("id")]]['amount']+=$row[csf("amount")];
	}
	
	
 //subcon_inbound_bill_dtls

	//echo "<pre>";
	//print_r($prod_date_qty_arr);
	// =========================== Total Fabric Booking Qty (Fin.Fab.) with order End ===================
ob_start();
if(count($nameArray)==0)
    {
        ?>
        <div style="font-size:20px;color:red;text-align:center">Data not found!</div>
        <?
        die;
    }
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
            
		
        <table width="1160" cellspacing="0" cellpadding="0" border="0" rules="all" >
		    <tr class="form_caption">
		        <td colspan="11" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		    </tr>
		    <tr class="form_caption">
		        <td colspan="11" align="center"><?   echo $company_library[$working_company]; ?><br>
		        <b>
		        <?
				echo ($start_date == '0000-00-00' || $start_date == '' ? '' : change_date_format($start_date)).' To ';echo  ($end_date == '0000-00-00' || $end_date == '' ? '' : change_date_format($end_date));
		        ?> </b>
		        </td>
		    </tr>
		</table>
         <div align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1140" class="rpt_table">
			<thead>
			 
             <tr>
				 
				<th width="100" style="word-wrap: break-word; width: 100px;">Month</th>

				<th width="100"  style="word-wrap: break-word; width: 100px;">Total Dyeing <br>Production Kg</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">White Color/<br>Wash/Peroxide<br> wash %</th>	
                <th width="100"  style="word-wrap: break-word; width: 100px;">Avg Color %</th>			
				<th width="100" style="word-wrap: break-word; width: 100px;">Black Color %</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Double Dyeing %</th>
				<th width="100" style="word-wrap: break-word; width: 100px;">Total Dyes<br>Chemical <br>Cost Tk</th>
				<th width="100"  style="word-wrap: break-word; width: 100px;">Cost /Kg</th>				
				<th width="100" style="word-wrap: break-word; width: 100px;">Total Sales<br> Bill Tk</th>
                <th width="100" style="word-wrap: break-word; width: 100px;">Sales/Kg</th>	
				<th width="">Dyes & Chemical <br>Cost on Sales%</th>
				 
                </tr>
				
			</thead>
		</table>

	
         <div style="max-height:350px; width:1158px; overflow-y:scroll;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1140" class="rpt_table" id="table_body">
		 <tbody>
		<?
		foreach($batch_mon_arr as $bid)
		{
			$mon_year=$prod_data_mon_arr[$bid]['mon_year'];
			$color_range_id=$prod_data_mon_arr[$bid]['color_range_id'];
			//$mon_year_chk=date('M-Y',strtotime($mon_year));
			//echo $mon_year.'d';
			$sub_prod_qty=$sub_prod_qty_arr[$bid]; 
			 //echo $sub_prod_qty.'d';
			$prod_batch_qty=$prod_arr2[$bid]['self_batch_qty']+$prod_arr2[$bid]['sample_batch_qty']+$prod_arr2[$bid]['tot_trim_qty']+$sub_prod_qty;
			$prod_date_qty_arr[$mon_year]['self_qty']+=$prod_batch_qty;
		if(in_array($color_range_id,$white_wash_colorArr))
		 {
				$prod_date_qty_arr[$mon_year]['white_wash_qty']+=$prod_batch_qty; 
		 }
		 if(!in_array($color_range_id,$not_white_wash_colorArr))
		 {
				$prod_date_qty_arr[$mon_year]['avg_color_qty']+=$prod_batch_qty; 
		 }
		 if($color_range_id==3)
		 {
				$prod_date_qty_arr[$mon_year]['black_color_qty']+=$prod_batch_qty; 
		 }
		 if(in_array($color_range_id,$double_dyeing_colorArr))
		 {
				$prod_date_qty_arr[$mon_year]['double_dyeing_qty']+=$prod_batch_qty; 
		 }
			$prod_date_arr[$mon_year]=$mon_year;
			$tot_dye_fin_bill_amount+=$dye_fin_bill_arr[$bid]['amount'];
			$batch_req_id=rtrim($batch_req_id_array[$bid],',');
			$sum_ReqIdsArr=array_unique(explode(",",$batch_req_id));
			$batch_weight=$batch_wgt_arr[$bid]["batchWgt"];
			 $sum_batchIdArr='';$tot_sum_chemical_cost=$tot_sum_deys_cost=$tot_dye_fin_bill_amount=0;
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
				
				if($sum_issue_amt_chemical>0)
				{
				$tot_sum_chemical_cost+=($sum_issue_amt_chemical/$sum_tot_batch_wgt)*$batch_weight;
				}
				if($sum_issue_amt_deys>0)
				{
				$tot_sum_deys_cost+=($sum_issue_amt_deys/$sum_tot_batch_wgt)*$batch_weight;
				}
			
			
			}
			$tot_dyes_chemical_cost=$tot_sum_chemical_cost+$tot_sum_deys_cost; 
			//$tot_dye_fin_bill_amount+=$dye_fin_bill_arr[$bid]['amount'];
			$che_cost_arr[$mon_year]['tot_dyes_chemical_cost']+=$tot_dyes_chemical_cost; 
			$mon_dye_fin_bill_arr[$mon_year]['dye_fin_bill']+=$dye_fin_bill_arr[$bid]['amount'];
			
			
		//	$mon_year_data_arr[$bid]['self_batch_qty']=$prod_arr2[$bid]['self_batch_qty'];
			//$mon_year_data_arr[$bid]['sample_batch_qty']=$prod_arr2[$bid]['sample_batch_qty'];
			//$mon_year_data_arr[$bid]['tot_trim_qty']=$prod_arr2[$bid]['tot_trim_qty'];
	} //Main Loop End *********
	//print_r($che_cost_arr);
	//die;
		

			    $i=1;
			    $total_self_qty_inhouse=$total_white_wash_qty=$total_avg_color_qty=$total_black_color_qty=$total_double_dyeing_qty=$total_tot_dyes_chemical_cost=$total_tot_dye_fin_bill_amount=$total_inbound_finish_qty=$total_tot_finishing_kg=$total_inbound_sub_finish_amt=$total_tot_dyeing_fin_earn=$total_tot_dye_chemical_cost=$total_tot_income=$total_avg_cost_per=0;$total_special_finish_amt=$total_special_finish_qty=0;
				//ksort($prod_date_arr);
				// print_r($prod_date_arr);die;
				foreach($prod_date_arr as $date_key=>$row)
				{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$po_id=rtrim($row[('po_id')],',');
								$batch_id=rtrim($batch_id_mon_arr[$date_key],',');
								$batch_ids=array_unique(explode(",",$batch_id));
								$batch_ids_all=implode(",",array_unique(explode(",",$batch_id)));
								//echo $batch_ids_all.'D';
							   // $job_no=""; $buyer="";
							  //  $po_id=array_unique(explode(",",$po_id));
							 /* $sum_batchIdArr='';$tot_sum_chemical_cost=$tot_sum_deys_cost=$tot_dye_fin_bill_amount=0;
							    foreach($batch_ids as $bid)
							    {
									$tot_dye_fin_bill_amount+=$dye_fin_bill_arr[$bid]['amount'];
									 $batch_req_id=rtrim($batch_req_id_array[$bid],',');
									$sum_ReqIdsArr=array_unique(explode(",",$batch_req_id));
									$batch_weight=$batch_wgt_arr[$bid]["batchWgt"];
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
										
										if($sum_issue_amt_chemical>0)
										{
										$tot_sum_chemical_cost+=($sum_issue_amt_chemical/$sum_tot_batch_wgt)*$batch_weight;
										}
										if($sum_issue_amt_deys>0)
										{
											
										$tot_sum_deys_cost+=($sum_issue_amt_deys/$sum_tot_batch_wgt)*$batch_weight;
										
										}
										
										
									}
							    }*/
								$tot_dye_fin_bill_amount=$mon_dye_fin_bill_arr[$date_key]['dye_fin_bill'];
								//$tot_dyes_chemical_cost=$tot_sum_chemical_cost+$tot_sum_deys_cost; 
								$tot_dyes_chemical_cost=$che_cost_arr[$date_key]['tot_dyes_chemical_cost'];
								// echo $tot_dyes_chemical_cost.'='.$tot_sum_chemical_cost.'='.$tot_sum_deys_cost.'<br>';
							 if($tot_dyes_chemical_cost>0) $tot_dyes_chemical_cost=$tot_dyes_chemical_cost;else $tot_dyes_chemical_cost=0;
								$self_qty_inhouse=	$prod_date_qty_arr[$date_key]['self_qty'];
								$white_wash_qty=	$prod_date_qty_arr[$date_key]['white_wash_qty'];
								if($white_wash_qty)
								{
								$white_wash_qty_per=	($white_wash_qty*100)/$self_qty_inhouse;
								}
								else $white_wash_qty=0;
								
								$avg_color_qty=	$prod_date_qty_arr[$date_key]['avg_color_qty'];
								if($avg_color_qty)
								{
								$avg_color_qty_per=	($avg_color_qty*100)/$self_qty_inhouse;
								} else $avg_color_qty_per=0;
								
								$black_color_qty=	$prod_date_qty_arr[$date_key]['black_color_qty'];
								if($black_color_qty)
								{
								$black_color_qty_per=	($black_color_qty*100)/$self_qty_inhouse;
								}
								 else $black_color_qty_per=0;
								$double_dyeing_qty=	$prod_date_qty_arr[$date_key]['double_dyeing_qty'];
								//echo $tot_dyes_chemical_cost.'DDD';
								 
								?>
								
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
									
									 
									<td width="100"  style="word-break: break-all; word-wrap: break-word;"><p><? echo $date_key; ?></p></td>
								
									<td width="100" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $batch_ids_all; ?>','<? echo $working_company; ?>','dyeing_earn_inhouse_popup',1)"></a><? echo number_format($self_qty_inhouse,2); ?></p></td>
									<td width="100" title="Qty=<? echo $white_wash_qty;?>*100/Prod Qty"  style="word-break: break-all; word-wrap: break-word;" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $batch_ids_all; ?>','<? echo $working_company; ?>','dyeing_earn_inhouse_popup',2)"></a><? echo number_format($white_wash_qty_per,2); ?></p></td>
                                   
									<td width="100" align="right" title="Qty=<? echo $avg_color_qty;?>*100/Prod Qty"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $batch_ids_all; ?>','<? echo $working_company; ?>','dyeing_earn_inboundSub_popup',3)"></a><? echo number_format($avg_color_qty_per,2); ?></p></td>
                                    <td width="100" align="right" title="<? echo $black_color_qty;?>*100/Prod Qty"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $batch_ids_all; ?>','<? echo $working_company; ?>','dyeing_earn_inboundSub_popup',4)"></a><? echo number_format($black_color_qty_per,2); ?></p></td>
									<td width="100" align="right" title="<? echo $double_dyeing_qty;?>*100/Prod Qty"><p><? echo number_format($double_dyeing_qty_per,2); ?></p></td>
                                    
                                    <td width="100" align="right" title="<? //echo $tot_sum_chemical_cost.'='.$tot_sum_deys_cost;?>"><p><? echo number_format($tot_dyes_chemical_cost,2); ?></p></td>
								
									<td width="100" align="right" title="Tot Dyes Chemical Cost/Prod Qty" ><p><? if($tot_dyes_chemical_cost>0 && $self_qty_inhouse>0) echo number_format($tot_dyes_chemical_cost/$self_qty_inhouse,2);else echo ""; ?></p></td>
									<td width="100" align="right" title=""><p><? echo number_format($tot_dye_fin_bill_amount,2); ?></p></td>
                					 <td width="100" title="Total Sales Bill/Total Prod Qty"  style="word-break: break-all; word-wrap: break-word;" align="right"><p><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_key; ?>','<? echo $special_batch_idArr; ?>','<? echo $working_company; ?>','special_qty_inhouse_popup',9)"></a><? if($self_qty_inhouse && $tot_dye_fin_bill_amount>0) echo number_format($self_qty_inhouse/$tot_dye_fin_bill_amount,2);else echo ""; ?></p></td>
								
									<td width="" align="right" title="Total Dyes Chemical Cost*100/Total Sales Bill"><p><? if($tot_dyes_chemical_cost>0 && $tot_dye_fin_bill_amount>0) echo number_format($tot_dyes_chemical_cost*100/$tot_dye_fin_bill_amount,2);else echo ""; ?></p></td>

									
							    </tr>
							    <?
								
								$total_self_qty_inhouse+=$self_qty_inhouse;
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
            <table class="rpt_table" width="1140" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                      <th width="100" align="right">Total</th>
					<th width="100" id="tot_dyeing_prod_qty" align="right"><? echo number_format($total_self_qty_inhouse,0,'.',''); ?></th>
					<th width="100" align="right"><? if($total_white_wash_qty && $total_self_qty_inhouse) echo number_format(($total_white_wash_qty*100)/$total_self_qty_inhouse,2,'.','');else echo ""; ?></th>
					<th width="100" align="right"><?  if($total_avg_color_qty && $total_self_qty_inhouse) echo number_format(($total_avg_color_qty*100)/$total_self_qty_inhouse,2,'.','');else echo "";?></th>
					<th width="100" align="right"><? if($total_black_color_qty && $total_self_qty_inhouse) echo number_format(($total_black_color_qty*100)/$total_self_qty_inhouse,2,'.','');else echo ""; ?></th>

					<th width="100" align="right"><strong><? if($total_double_dyeing_qty && $total_self_qty_inhouse) echo number_format(($total_double_dyeing_qty*100)/$total_self_qty_inhouse,2,'.','');else echo ""; ?></strong></th>
					<th width="100" align="right"><strong><? if($total_tot_dyes_chemical_cost && $total_self_qty_inhouse) echo number_format($total_tot_dyes_chemical_cost,2,'.','');else echo ""; ?></strong></th>  

					<th width="100"  align="right"><? if($total_tot_dyes_chemical_cost && $total_self_qty_inhouse)  echo number_format($total_tot_dyes_chemical_cost/$total_self_qty_inhouse,2,'.','');else echo ""; ?></th>
                    
					<th width="100"  align="right"><? echo number_format($total_tot_dye_fin_bill_amount,2,'.',''); ?></th>    

                    <th width="100" align="right"><? if($total_tot_dye_fin_bill_amount && $total_self_qty_inhouse) echo number_format($total_tot_dye_fin_bill_amount/$total_self_qty_inhouse,2,'.',''); ?></th>
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
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$report_type";
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
				$date_cond=" and c.process_end_date between '$start_date' and '$end_date'";
	
	     $sql_data="SELECT a.id,a.batch_no,a.batch_date,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,c.process_id, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and c.entry_form in(35)   and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0   and c.status_active=1 and c.is_deleted=0   and d.status_active=1 and d.is_deleted=0 and a.id in($batch_id) and a.working_company_id=$company_id  $date_cond 
		order by a.id";		 
	//echo $sql_data;
	$nameArray=sql_select($sql_data);
	foreach($nameArray as $row)
    {
		$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		$batch_id_array[$row[csf('id')]] = $row[csf('id')];
			$const=explode(",",$row[csf('item_description')]);
		$batch_wise_const_arr[$row[csf('id')]][$const[0]]=$const[0];
		
	}
	// print_r($batch_wise_const_arr);
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
	
	//$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
 $sql_job="select b.id,a.buyer_name,b.po_number from wo_po_break_down b,wo_po_details_master a where  a.id=b.job_id  $po_cond";
	$sql_job_result = sql_select($sql_job);
	foreach ($sql_job_result as $val) 
	{
		$po_buyer_array[$val[csf('id')]]['buyer']=$buyer_library[$val[csf('buyer_name')]];
		$po_buyer_array[$val[csf('id')]]['po_no']=$val[csf('po_number')];
	}
	unset($sql_job_result);

	   $sql_po="SELECT f.id as conv_id,a.buyer_name,b.id,b.job_no_mst,b.po_number,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.lib_yarn_count_deter_id as deter_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0    and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and f.cons_process not in(1,30,35) and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1  $po_cond order by f.id";
	   
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$color_break_down=$val[csf('color_break_down')];
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		//$po_buyer_array[$val[csf('id')]]['buyer']=$buyer_library[$val[csf('buyer_name')]];
		//$po_buyer_array[$val[csf('id')]]['po_no']=$val[csf('po_number')];
		
		if($val[csf('cons_process')]==31 && $color_break_down!='')
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $val[csf('color_break_down')];

			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
			$po_color_fab_array[$val[csf('id')]][$arr_2[3]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
			$po_color_fabricDying_array[$val[csf('id')]][$arr_2[3]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
			//echo $arr_2[1].', ';
			}
		}
		else
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
			//echo $val[csf('charge_unit')].',';
		}
		
	}
	 $process_array=array(1,30,35); $batch_against_chk=array(2);
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
				/*for($ci=0;$ci<count($arr_1);$ci++)
				{
				$arr_2=explode("_",$arr_1[$ci]);
				//$conv_rate=$po_color_fab_array[$row[csf('po_id')]][$arr_2[1]][$arr_2[3]][$row[csf('deter_id')]][$key_id]['rate'];
				//echo $conv_rate.'='.$row[csf('po_id')].'='.$arr_2[1].'='.$arr_2[3].'='.$val[csf('deter_id')].'='.$key.'<br>';
				}*/
				$conv_rate=$po_color_fab_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('deter_id')]][$key_id]['rate'];
				$fab_conv_rate=$po_color_fabricDying_array[$row[csf('po_id')]][$row[csf('color_id')]][$key_id]['rate'];
				if($conv_rate==0 || $conv_rate=='') $conv_rate=$fab_conv_rate;
				//echo $conv_rate.'='.$fab_conv_rate.'<br>'; 
				//$po_color_fabricDying_array[$val[csf('id')]][$arr_2[3]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
				
			}
			/*else
			{
				$conv_rate=$po_color_fab_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('deter_id')]][$key_id]['rate'];
				//echo "A,";
				//$conv_rate=0;
				//echo "B".$conv_rate.'<br>';
				
			}*/
			
		 //}
			if($row[csf('entry_form')]==35 && $conv_rate>0)
			 {
				
				if(!in_array($row[csf('batch_against')],$batch_against_chk))
				{
					// echo $row[csf('batch_qty')].'='.$conv_rate.'<br>';
				$dying_amt+=$row[csf('batch_qty')]*$conv_rate;
				}
				//echo $conv_rate.'='.$row[csf('batch_qty')].'='.$row[csf('po_id')].'<br>';
				
			
			 }
		}
		//if($row[csf('entry_form')]==35 && $row[csf('batch_qty')]>0)
				//{
					//echo $conv_rate.'='.$row[csf('batch_qty')]*$conv_rate.'<br>';
					
					//echo $row[csf('id')].'='.$row[csf('po_id')].'='.$key_id.'='.$dying_amt.'<br>';
					$const=explode(",",$row[csf('item_description')]);
					if(!in_array($row[csf('batch_against')],$batch_against_chk))
				    {
					$prod_date_qty_arr[$row[csf('id')]]['self_amount']+=$dying_amt;//$row[csf('batch_qty')]*$conv_rate;
					$prod_date_qty_arr[$row[csf('id')]]['self_qty']+=$row[csf('batch_qty')];
				    }
					$prod_date_qty_arr[$row[csf('id')]]['prod_date']=$row[csf('prod_date')];
					$prod_date_qty_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
					$prod_date_qty_arr[$row[csf('id')]]['buyer']=$po_buyer_array[$row[csf('po_id')]]['buyer'];
					$prod_date_qty_arr[$row[csf('id')]]['po_no']=$po_buyer_array[$row[csf('po_id')]]['po_no'];
					$prod_date_qty_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
					$prod_date_qty_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
					$prod_date_qty_arr[$row[csf('id')]]['constructuon']=$const[0];
					
					// 
				//}
		//echo $color_break_down.'dd';
		//$po_color_fab_array[$row[csf('po_id')]][$arr_2[1]][$arr_2[3]][$row[csf('deter_id')]][$row[csf('cons_process')]]['rate'];
		
		
		
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
        $table_width=980;
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
					<th width="150">Construction</th>
                    <th width="80">Buyer </th>
                    <th width="100">Order No</th>
                    <th width="80">Batch Date</th>
                    <th width="60">Batch ID</th>
                    <th width="100">Color</th>
                    <th width="70">Batch Qty</th>
                    <?
                    if($type_id==2)
					{
					?>
                    <th width="80">Rate$</th>
                    <th width="80">Amount</th>
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
								<td  width="150"><? echo implode(",",$batch_wise_const_arr[$bId]); ?></td>
                                 <td  width="80" title="<? echo $row[('buyer')];?>"><? echo $row[('buyer')]; ?></td>
                                  <td  width="100" title="<? //echo $row[('color_id')];?>"><p><? echo $row[('po_no')]; ?></p></td>
                                  
                                <td  width="80"><? echo change_date_format($row[('batch_date')]); ?></td>
                                 <td  width="60"><? echo $bId; ?></td>
                                 <td  width="100" title="Color_id=<? echo $row[('color_id')];?>"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
                                <td  width="70" align="right"><? echo number_format($row[('self_qty')],2);?></td>
                                 <?
								// $col_spna=5;
							if($type_id==2)
							{
							?>
                                <td  width="80" align="right" title="Amount/Qty(Avg Rate=<? echo number_format($row[('self_amount')]/$row[('self_qty')],5);?>)"><? echo number_format($row[('self_amount')]/$row[('self_qty')],3); ?></td>
                                <td  width="80" align="right" title="Rate*Batch Qty"><? echo number_format($row[('self_amount')],2); ?></td>
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
                        <tr bgcolor="#CCCCCC"> 
                        <td align="right" colspan="9"><strong>Total</strong></td>
                        <td align="right"><? echo number_format($tot_dyeing_qty,2); ?>&nbsp;</td>
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
if ($action=="dyeing_earn_inboundSub_popup")  // All Production Data popup 
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Dyeing inhouse poppup Info","../../../", 1, 1, $unicode,'','');
	
	$date_key=str_replace("'","",$date_key);
	$batch_id=str_replace("'","",$batch_id);
	$company_id=str_replace("'","",$companyID);
	$type_id=str_replace("'","",$type);
	$exchange_rate=str_replace("'","",$exchange_rate);
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
				$date_cond=" and c.process_end_date between '$start_date' and '$end_date'";
	
/*	$sql_data="SELECT a.id,a.batch_no,a.batch_date,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,c.process_id, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and c.entry_form in(35) and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 and a.working_company_id=$company_id  $date_cond 
		order by a.id";	*/
		 $sql_data_sub="SELECT a.id,a.batch_no,a.batch_date,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,b.gsm,b.grey_dia,b.fin_dia,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c 
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and c.entry_form in(38) and a.batch_against not in(2) and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 and a.id in($batch_id) and a.company_id=$company_id $date_cond
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
				if($sub_po_cond=="") $sub_po_cond.=" and ( b.id in ($ids) ";
				else
					$sub_po_cond.=" or  b.id in ($ids) "; 
			}
			$sub_po_cond.=") ";

		}
		else
		{
			$sub_po_cond.=" and b.id in ($sub_poIds) ";
		}
	}
	//$order_wise_rate_arr = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0 and rate>0","id","rate");
	//subcon_ord_breakdown
	//$sql_subcon="SELECT a.currency_id,b.main_process_id,b.id,b.rate from subcon_ord_dtls b,subcon_ord_mst a where a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0 and b.rate>0 and b.main_process_id in(4,3) $sub_po_cond";
	$sql_subcon="SELECT a.currency_id,b.main_process_id,b.id,c.color_id,c.rate,c.gsm,c.grey_dia,c.finish_dia from subcon_ord_dtls b,subcon_ord_mst a,subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.id=c.mst_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.rate>0 and b.main_process_id in(4,3) $sub_po_cond";
	$sql_subcon_res=sql_select($sql_subcon);
	foreach($sql_subcon_res as $row)
    {
		$sub_order_wise_arr[$row[csf('id')]]['process']  = $row[csf('main_process_id')];
		$sub_order_wise_arr[$row[csf('id')]]['currency_id']  = $row[csf('currency_id')];
		$fabCond=$row[csf('gsm')].'_'.$row[csf('grey_dia')].'_'.$row[csf('finish_dia')];
		if($row[csf('currency_id')]==1) //TK
		{ 
		//echo  $row[csf('rate')].'='.$exchange_rate.'<br>';
		$sub_order_wise_rate_arr[$row[csf('color_id')]][$row[csf('id')]][$fabCond]['rate'] = $row[csf('rate')]/$exchange_rate;
		//$sub_order_wise_process_arr[$row[csf('id')]][$row[csf('main_process_id')]][$fabCond]['fin_rate'] = $row[csf('rate')]/$exchange_rate;
		}
		else
		{
			$sub_order_wise_rate_arr[$row[csf('color_id')]][$row[csf('id')]][$fabCond]['rate'] = $row[csf('rate')];
			//$sub_order_wise_process_arr[$row[csf('id')]][$row[csf('main_process_id')]][$fabCond]['fin_rate'] = $row[csf('rate')];
		}
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
		//$sub_order_wise_process_arr[$row[csf('id')]][$row[csf('main_process_id')]]['fin_rate'];
		//gsm,b.grey_dia,b.fin_dia
		$fabCond=$row[csf('gsm')].'_'.$row[csf('grey_dia')].'_'.$row[csf('fin_dia')];
		
		//echo $color_break_down.'dd'; color_id
		//$po_color_fab_array[$row[csf('po_id')]][$arr_2[1]][$arr_2[3]][$row[csf('deter_id')]][$row[csf('cons_process')]]['rate'];
		$sub_rate=$sub_order_wise_rate_arr[$row[csf('color_id')]][$row[csf('po_id')]][$fabCond]['rate'];
		// echo $sub_rate.'='.$row[csf('batch_qty')].'<br>';
			/*$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
			$all_prod_date_arr[$row[csf('prod_date')]].=$row[csf('id')].',';
			$prod_date_qty_arr[$row[csf('prod_date')]]['sub_batch_id'].=$row[csf('id')].',';
			$prod_date_qty_arr[$row[csf('prod_date')]]['subcon_qty']+=$row[csf('batch_qty')];
			$prod_date_qty_arr[$row[csf('prod_date')]]['subcon_amount']+=$row[csf('batch_qty')]*$sub_rate;*/
		//echo $row[csf('color_id')].'='.$row[csf('batch_qty')].'='.$sub_rate.'<br>';
		
			$prod_date_qty_arr[$row[csf('id')]]['self_qty']+=$row[csf('batch_qty')];
			$prod_date_qty_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			$prod_date_qty_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$prod_date_qty_arr[$row[csf('id')]]['prod_date']=$row[csf('prod_date')];
			$prod_date_qty_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$prod_date_qty_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			$prod_date_qty_arr[$row[csf('id')]]['self_amount']+=$row[csf('batch_qty')]*$sub_rate;
		
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
                    <th width="80">Amount$</th>
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
                                <td  width="80" title="colorId=<? echo $row[('color_id')]?>"><? echo $batch_id; ?></td>
                             
                                <td  width="80" align="right"><? echo number_format($row[('self_qty')],0);?></td>
                                 <?
								// $col_spna=5;
							if($type_id==4)
							{
							?>
                                <td  width="80" align="right" title="Amount/Batch Qty(Avg rate)"><? echo number_format($row[('self_amount')]/$row[('self_qty')],3); ?></td>
                                <td  width="80" align="right" title="Rate*Batch Qty"><? echo number_format($row[('self_amount')],2); ?></td>
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
                        <tr bgcolor="#CCCCCC"> 
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
         <?
		 if($type_id==4)
		{
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

if ($action=="dyes_chemical_cost_earn_popup")  // All Production Data popup 
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Dyeing inhouse poppup Info","../../../", 1, 1, $unicode,'','');
	
	$date_key=str_replace("'","",$date_key);
	$batch_id=str_replace("'","",$batch_id);
	$batch_id=implode(",",explode("_",$batch_id));
	$company_id=str_replace("'","",$companyID);
	$type_id=str_replace("'","",$type);
	$re_dying_batch=str_replace("'","",$re_dying_batch);
	//echo $re_dying_batch.'DDD';
	$re_dying_batch=implode(",",explode("_",$re_dying_batch));
	
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
			$date_cond_wash="and a.issue_date between '$start_date' and '$end_date'";
		$sql_data_batch=sql_select("select a.id,a.batch_no,c.po_number from pro_batch_create_mst a,pro_batch_create_dtls b,wo_po_break_down c where a.id=b.mst_id and c.id=b.po_id and a.status_active=1 and b.status_active=1 and a.id in($batch_id)");
		 
		foreach($sql_data_batch as $val)
		{
			$batch_no=$val[csf("batch_no")];
			$po_no_arr[$val[csf("id")]].=$val[csf("po_number")].',';
		}
	
	 $sql_dyes_cost =sql_select("select a.issue_number,a.buyer_id,a.batch_no,b.item_category,(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b
    where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=5  and a.company_id=$company_id and a.issue_purpose not in(13) and a.batch_no  is not null  and   b.item_category in (5,6,7) ");
	foreach($sql_dyes_cost as $val)
	{
		$batchArr=array_unique(explode(",",$val[csf("batch_no")]));
		foreach($batchArr as $bid)
		{
		$dyes_chemical_arr[$bid]['chemical_cost']+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
		$dyes_chemical_issue_no_arr[$bid]['issue_number'].=$val[csf("issue_number")].',';
		$dyes_chemical_issue_no_arr[$bid]['buyer_id'].=$buyer_library[$val[csf("buyer_id")]].',';
		}
    }
	 
	 
	 
	
	//re_dying_batch
	
	  $sql_data="SELECT a.id,a.batch_no,a.batch_date,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,c.process_id, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c 
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id  and c.entry_form in(35,38)  and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 and a.id in($batch_id) and c.service_company=$company_id  $date_cond 
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
		$buyer_id=rtrim($dyes_chemical_issue_no_arr[$row[csf('id')]]['buyer_id'],',');
		$buyer_ids=implode(",",array_unique(explode(",",$buyer_id)));
			
		//$dyes_chemical_issue_no_arr[$bid]['issue_number']
		$issue_number=rtrim($dyes_chemical_issue_no_arr[$row[csf('id')]]['issue_number'],',');
		$issue_numbers=implode(",",array_unique(explode(",",$issue_number)));
		//echo $issue_numbers.'DD';
		
		if($chemical_cost>0)
		{
			$prod_date_qty_arr[$row[csf('id')]]['self_qty']+=$row[csf('batch_qty')];
			$prod_date_qty_arr[$row[csf('id')]]['issue_numbers'].=$issue_numbers.',';
			$prod_date_qty_arr[$row[csf('id')]]['buyer_ids'].=$buyer_ids.',';
			$prod_date_qty_arr[$row[csf('id')]]['prod_date']=$row[csf('prod_date')];
			$prod_date_qty_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$prod_date_qty_arr[$row[csf('id')]]['color']=$color_library[$row[csf('color_id')]];
			$prod_date_qty_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			$prod_date_qty_arr[$row[csf('id')]]['self_amount']+=$row[csf('batch_qty')]*$conv_rate;
			$prod_date_qty_arr[$row[csf('id')]]['dyes_cost']=$chemical_cost;
		}
		
		
	}
	
	  $sql_data_re="SELECT a.id,a.batch_no,a.batch_date,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,c.process_id, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c 
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id  and c.entry_form in(35,38) and a.batch_against=2  and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 and a.id in($re_dying_batch) and c.service_company=$company_id  $date_cond 
		order by a.id";		 
	//echo $sql_data;
	$nameArray_re=sql_select($sql_data_re); 
	
	

	foreach($nameArray_re as $row)
    {
		//$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
		$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
		$all_prod_date_arr[$row[csf('prod_date')]].=$row[csf('id')].',';
		
			$re_chemical_cost=$dyes_chemical_arr[$row[csf('id')]]['chemical_cost'];
			//$dyes_chemical_issue_no_arr[$bid]['buyer_id'];
			$buyer_id=rtrim($dyes_chemical_issue_no_arr[$row[csf('id')]]['buyer_id'],',');
			$buyer_ids=implode(",",array_unique(explode(",",$buyer_id)));
			
			$issue_number=rtrim($dyes_chemical_issue_no_arr[$row[csf('id')]]['issue_number'],',');
			
			$issue_numbers=implode(",",array_unique(explode(",",$issue_number)));
		
		//echo $row[csf('batch_no')].'='.$row[csf('id')].'<br>';
		if($re_chemical_cost>0)
		{
			$prod_date_qty_arr[$row[csf('id')]]['self_qty']+=$row[csf('batch_qty')];
			$prod_date_qty_arr[$row[csf('id')]]['issue_numbers'].=$issue_numbers.',';
			$prod_date_qty_arr[$row[csf('id')]]['buyer_ids'].=$buyer_ids.',';
			$prod_date_qty_arr[$row[csf('id')]]['prod_date']=$row[csf('prod_date')];
			$prod_date_qty_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$prod_date_qty_arr[$row[csf('id')]]['color']=$color_library[$row[csf('color_id')]];
			$prod_date_qty_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			//$prod_date_qty_arr[$row[csf('id')]]['self_amount']+=$row[csf('batch_qty')]*$conv_rate;
			$prod_date_qty_arr[$row[csf('id')]]['re_dyes_cost']=$re_chemical_cost;
		}
		
		
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
        $table_width=1070;
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
                    <th width="100">Buyer </th>
                    <th width="100">Order No </th> 
                    <th width="100">Color</th>
                    <th width="80">Batch Date</th>
                    <th width="80">Batch ID</th>
                  
                    <th width="80">Batch Qty</th>
                    <th width="80">1st Batch Issue Amount$</th>
                    <th width="80">Sub-Sequent Issue Amount$</th>
                     <th width="80">Issue Amount$</th>
                    <th width="80">AVG Rate$</th>
                   
                   
                    
                </tr>
            </thead>
            <tbody>
                 <?
                        $i=1;$tot_dyeing_qty=$tot_dyeing_amount=$tot_chemical_cost_amount=$tot_chemical_cost_amount_re=$tot_issue_chemical_cost_amount=0;
                        foreach($prod_date_qty_arr as $bId=>$row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$chemical_cost=$row[('dyes_cost')];
							$re_chemical_cost=$row[('re_dyes_cost')];
							$issue_numbers=$row[('issue_numbers')];
							$color=$row[('color')];
							 $buyer_ids=rtrim($row[('buyer_ids')],',');
							  $po_no=rtrim($po_no_arr[$bId],',');
							 // echo $buyer_ids.'DD';
							 $po_nos=implode(",",array_unique(explode(",",$po_no)));
							 $buyer_name=implode(",",array_unique(explode(",",$buyer_ids)));
							//$chemical_cost=$dyes_chemical_arr[$bId]['chemical_cost'];dyes_cost
							if($chemical_cost>0 || $re_chemical_cost>0)
							{
                         ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
                                <td  width="20"><? echo $i; ?></td>
                                <td  width="80"><? echo change_date_format($row[('prod_date')]); ?></td>
                               
                                <td  width="80" ><? echo $row[('batch_no')]; ?></td> 
                                <td  width="100" ><? echo $buyer_name; ?></td>
                                <td  width="100" ><? echo $po_nos; ?></td> 
                                <td  width="100" ><? echo $color; ?></td>
                                
                                <td  width="80"><? echo change_date_format($row[('batch_date')]); ?></td>
                                 <td  width="80"><? echo $bId; ?></td>
                                <td  width="80" align="right"><? echo number_format($row[('self_qty')],0);?></td>
                                <td  width="80" align="right" title="Avg Rate*Batch Qty"><? echo number_format($chemical_cost,2); ?></td>
                                 <td  width="80" align="right"  title="All Ext. Batch"><? echo number_format($re_chemical_cost,2); ?></td>
                                 <td  width="80" align="right"   title="Issue Nos=<? echo $issue_numbers;?> 1st Batch Issue Amount+Sub-Sequent Issue Amount"><? echo number_format($re_chemical_cost+$chemical_cost,2); ?></td>
                                <td  width="80" align="right" title="Issue Amount/Batch Qty"><? 
								$tot_amount=$re_chemical_cost+$chemical_cost;
								echo number_format($tot_amount/$row[('self_qty')],3); ?></td>
                              
								 
                            </tr>
                        <?
                        $i++;
						$tot_dyeing_qty+=$row[('self_qty')];
						$tot_dyeing_amount+=$chemical_cost/$row[('self_qty')];
						$tot_chemical_cost_amount+=$chemical_cost;
						$tot_chemical_cost_amount_re+=$re_chemical_cost;
						$tot_issue_chemical_cost_amount+=$re_chemical_cost+$chemical_cost;
                        }
                        ?>
                      
                    <?
					 }
					?>
                      <tr bgcolor="#CCCCCC"> 
                        <td align="right" colspan="8"><strong>Total</strong></td>
                        <td align="right"><strong><? echo number_format($tot_dyeing_qty,2); ?>&nbsp;</strong></td> 
                        <td align="right"><strong><? echo number_format($tot_chemical_cost_amount,2); ?>&nbsp;</strong></td>
                        <td align="right"><strong><? echo number_format($tot_chemical_cost_amount_re,2); ?>&nbsp;</strong></td>
                         <td align="right"><strong><? echo number_format($tot_issue_chemical_cost_amount,2); ?>&nbsp;</strong></td>
                      
                        <td align="right"><? //echo number_format($tot_chemical_cost_amount/$tot_dyeing_qty,2,'.',''); ?>&nbsp;</td>
                       
                      </tr>
            </tbody>
		</table>
        <br>
        <?
        $sql_wash_dyes_cost =sql_select("select a.issue_number,a.buyer_id,a.issue_date,a.req_no,a.batch_no,b.item_category,b.cons_quantity,(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b
    where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=5  and a.issue_purpose in(13) and a.company_id=$company_id and b.item_category in (5,6,7) and (a.batch_no  is null or a.batch_no=0) $date_cond_wash ");//and (a.batch_no  is null or a.batch_no=0)
	 
		?>
        <table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
        <caption><b> Machine Wash Issue Details:</b> </caption>
            <thead>
                <tr>
                    <th width="20" >SL</th>
                    <th width="100">Issue Date</th>
                    <th width="120">Issue No</th>
                    <th width="80">Issue Qty</th>
                    <th width="80">Issue Amount$</th>
                    <th width="80">AVG Rate$</th>
                   
                   
                    
                </tr>
            </thead>
            <tbody>
                 <?
                        $i=1;$tot_wash_dyeing_qty=0;
						$tot_wash_issue_chemical_cost_amount=0;
                        foreach($sql_wash_dyes_cost as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$wash_chemical_cost=$row[csf('dyes_chemical_cost')]/$exchange_rate;
							 
							//$chemical_cost=$dyes_chemical_arr[$bId]['chemical_cost'];dyes_cost
							if($wash_chemical_cost>0)
							{
                         ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
                                <td  width="20"><? echo $i; ?></td>
                                <td  width="100"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td  width="120" ><? echo $row[csf('issue_number')]; ?></td>
                                <td  width="80" align="right"><? echo number_format($row[csf('cons_quantity')],0);?></td>
                                <td  width="80" align="right" title="Avg Rate*Issue Qty"><? echo number_format($wash_chemical_cost,2); ?></td>
                                <td  width="80" align="right" title="Issue Amount/Batch Qty"><? 
								$tot_amount=$wash_chemical_cost;
								echo number_format($tot_amount/$row[csf('cons_quantity')],3); ?></td>
                              
								 
                            </tr>
                        <?
                        $i++;
						$tot_wash_dyeing_qty+=$row[csf('cons_quantity')];
						 
						//$tot_chemical_cost_amount+=$wash_chemical_cost;
						//$tot_chemical_cost_amount_re+=$re_chemical_cost;
						$tot_wash_issue_chemical_cost_amount+=$wash_chemical_cost;
                        }
                        ?>
                      
                    <?
					 }
					?>
                      <tr bgcolor="#CCCCCC"> 
                        <td align="right" colspan="3"><strong>Total</strong></td>
                        <td align="right"><strong><? echo number_format($tot_wash_dyeing_qty,0); ?>&nbsp;</strong></td> 
                         <td align="right"><strong><? echo number_format($tot_wash_issue_chemical_cost_amount,2); ?>&nbsp;</strong></td>
                      
                        <td align="right"><? //echo number_format($tot_chemical_cost_amount/$tot_dyeing_qty,2,'.',''); ?>&nbsp;</td>
                       
                      </tr>
                       <tr bgcolor="#CCCCCC"> 
                        <td align="right" colspan="3"><strong>Grand Total</strong></td>
                        <td align="right"><? //echo number_format($tot_dyeing_qty,0); ?>&nbsp;</td> 
                         <td align="right"><strong><? echo number_format($tot_wash_issue_chemical_cost_amount+$tot_issue_chemical_cost_amount,2); ?>&nbsp;</strong></td>
                      
                        <td align="right"><? //echo number_format($tot_chemical_cost_amount/$tot_dyeing_qty,2,'.',''); ?>&nbsp;</td>
                       
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
	
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
    <?
		 
	?>
    
        </div>
        <?
		exit();
}
if ($action=="special_qty_inhouse_popup")  // All Production Data popup dyeing_earn_inboundSub_popup
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Special Finish inhouse poppup Info","../../../", 1, 1, $unicode,'','');
	
	$date_key=str_replace("'","",$date_key);
	$batch_id=str_replace("'","",$batch_id);
	$company_id=str_replace("'","",$companyID);
	$type_id=str_replace("'","",$type);
	$batch_id_no=explode("_",$batch_Arr);
	//$batch_id=implode(",",$batch_id_no);
	//echo $batch_Arr.'dd'.$batch_id; 
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
				$date_cond=" and c.process_end_date between '$start_date' and '$end_date'";
	
	 /*  $sql_data="SELECT a.id,a.batch_no,a.batch_date,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,a.process_id, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and c.entry_form in(35)   and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 and c.status_active=1 and c.is_deleted=0   and d.status_active=1 and d.is_deleted=0 and a.id in($batch_id) and a.working_company_id=$company_id  $date_cond 
		order by a.id";	*/
			 
		//$sql_data_special="SELECT a.id,a.batch_no,a.batch_date,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,c.process_id,c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		//from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d
	//	where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and c.entry_form in(32,48,33,34)  and a.is_sales=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
 // and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and b.po_id>0  and  a.id in($batch_id) and a.working_company_id=$company_id  $date_cond 
		//order by a.id";	 
		 $sql_data_special="SELECT e.id as dtls_id,a.id,a.batch_no,a.batch_date,a.color_id,a.floor_id,c.entry_form,a.batch_against,(e.production_qty) as batch_qty,c.process_id,c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d,pro_fab_subprocess_dtls e
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and c.id=e.mst_id and e.prod_id=b.prod_id and e.prod_id=d.id    and c.entry_form in(32,48,33,34)  and a.is_sales=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
  and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and b.po_id>0  and  a.id in($batch_id) and a.working_company_id=$company_id  $date_cond 
		order by a.id";	 
	//echo $sql_data;
	$nameArray=sql_select($sql_data_special);
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
	
	//$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
 $sql_job="select b.id,a.buyer_name,b.po_number from wo_po_break_down b,wo_po_details_master a where  a.id=b.job_id  $po_cond";
	$sql_job_result = sql_select($sql_job);
	foreach ($sql_job_result as $val) 
	{
		$po_buyer_array[$val[csf('id')]]['buyer']=$buyer_library[$val[csf('buyer_name')]];
		$po_buyer_array[$val[csf('id')]]['po_no']=$val[csf('po_number')];
	}
	unset($sql_job_result);

	   $sql_po="SELECT f.id as conv_id,a.buyer_name,b.id,b.job_no_mst,b.po_number,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.lib_yarn_count_deter_id as deter_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0    and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and f.cons_process not in(1,30,35) and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $po_cond order by f.id";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$color_break_down=$val[csf('color_break_down')];
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		//$po_buyer_array[$val[csf('id')]]['buyer']=$buyer_library[$val[csf('buyer_name')]];
		//$po_buyer_array[$val[csf('id')]]['po_no']=$val[csf('po_number')];
		
		if($val[csf('cons_process')]==31 && $color_break_down!='')
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $val[csf('color_break_down')];

			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
			$po_color_fab_array[$val[csf('id')]][$arr_2[3]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
			$po_color_fabricDying_array[$val[csf('id')]][$arr_2[3]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
			//echo $arr_2[1].', ';
			}
		}
		else
		{
			//$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
			$po_color_only_fab_array[$val[csf('id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
			//echo $val[csf('charge_unit')].',';
		}
		
	}
	 $process_array=array(1,30,35); $batch_against_chk=array(2);
	foreach($nameArray as $row)
    {
		//$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
		$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
		$process_idArr=array_unique(explode(",",$row[csf('process_id')]));
		$all_prod_date_arr[$row[csf('prod_date')]].=$row[csf('id')].',';
		//$process_idArr=array_unique(explode(",",$row[csf('process_id')]));
		$dtls_id=$row[csf('dtls_id')];
		
		$fin_fab_conv_rate=$po_color_only_fab_array[$row[csf('po_id')]][$row[csf('process_id')]]['rate'];
		$special_amt=$row[csf('batch_qty')]*$fin_fab_conv_rate;
		$process_id=$row[csf('process_id')];
		 //echo $special_amt.'='.$row[csf('process_id')].', ';
		 
					if($special_amt>0)
					{
						if($dtls_chk_arr[$dtls_id]=="")
						{
						$prod_date_qty_arr[$row[csf('id')]][$process_id]['self_amount']+=$special_amt;//$row[csf('batch_qty')]*$conv_rate;
						$prod_date_qty_arr[$row[csf('id')]][$process_id]['self_qty']+=$row[csf('batch_qty')];
						$dtls_chk_arr[$dtls_id]=$dtls_id;
						}
				   // }
					$prod_date_qty_arr[$row[csf('id')]][$process_id]['prod_date']=$row[csf('prod_date')];
					$prod_date_qty_arr[$row[csf('id')]][$process_id]['color_id']=$row[csf('color_id')];
					$prod_date_qty_arr[$row[csf('id')]][$process_id]['process_id']=$row[csf('process_id')];
					$prod_date_qty_arr[$row[csf('id')]][$process_id]['buyer']=$po_buyer_array[$row[csf('po_id')]]['buyer'];
					$prod_date_qty_arr[$row[csf('id')]][$process_id]['po_no']=$po_buyer_array[$row[csf('po_id')]]['po_no'];
					$prod_date_qty_arr[$row[csf('id')]][$process_id]['batch_no']=$row[csf('batch_no')];
					$prod_date_qty_arr[$row[csf('id')]][$process_id]['batch_date']=$row[csf('batch_date')];
					}
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
        $table_width=830;
		?>
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
         <caption> <b>Inhouse Finishing qty Details</b></caption>
            <thead>
                <tr>
                    <th width="20" >SL</th>
                    <th width="80">Prod Date</th>
                    <th width="80">Batch No</th>
                    <th width="80">Buyer </th>
                    <th width="100">Order No</th>
                    <th width="80">Batch Date</th>
                    <th width="60">Batch ID</th>
                    <th width="100">Color</th>
                    <th width="100">Process</th>
                    <th width="70">Finish Qty</th>
                    <?
                    if($type_id==2)
					{
					?>
                    <th width="80">Rate$</th>
                    <th width="80">Amount$</th>
                    <?
					}
					?>
                    
                </tr>
            </thead>
            <tbody>
                 <?
                        $i=1;$tot_dyeing_qty=$tot_dyeing_amount=0;
                        foreach($prod_date_qty_arr as $bId=>$bId_data)
                        {
						 foreach($bId_data as $p_id=>$row)
                       	 {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                         ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
                                <td  width="20"><? echo $i; ?></td>
                                <td  width="80"><? echo change_date_format($row[('prod_date')]); ?></td>
                             
                                <td  width="80" title="Color_id=<? echo $row[('color_id')];?>"><? echo $row[('batch_no')]; ?></td>
                                 <td  width="80" title="<? echo $row[('buyer')];?>"><? echo $row[('buyer')]; ?></td>
                                  <td  width="100" title="<? //echo $row[('color_id')];?>"><p><? echo $row[('po_no')]; ?></p></td>
                                  
                                <td  width="80"><? echo change_date_format($row[('batch_date')]); ?></td>
                                 <td  width="60"><? echo $bId; ?></td>
                                 <td  width="100" title="Color_id=<? echo $row[('color_id')];?>"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
                                <td  width="100" title="Color_id=<? echo $row[('color_id')];?>"><p><? echo $conversion_cost_head_array[$p_id]; ?></p></td>
                                <td  width="70" align="right"><? echo number_format($row[('self_qty')],2);?></td>
                                 <?
								// $col_spna=5;
							if($type_id==2)
							{
							?>
                                <td  width="80" align="right" title="Amount/Qty(Avg Rate=<? echo number_format($row[('self_amount')]/$row[('self_qty')],5);?>)"><? echo number_format($row[('self_amount')]/$row[('self_qty')],3); ?></td>
                                <td  width="80" align="right" title="Rate*Batch Qty"><? echo number_format($row[('self_amount')],2); ?></td>
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
                        }
                        ?>
                        <tr bgcolor="#CCCCCC"> 
                        <td align="right" colspan="9"><strong>Total</strong></td>
                        <td align="right"><? echo number_format($tot_dyeing_qty,2); ?>&nbsp;</td>
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
         <?
		if($type_id==2)
		{
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
		 
		
		exit();
}
?>	
