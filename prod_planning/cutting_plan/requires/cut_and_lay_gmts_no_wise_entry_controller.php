<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if(!function_exists('fn_delete_dir_with_files'))
{
	function fn_delete_dir_with_files($dir) {
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				fn_delete_dir_with_files($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}
}

//--------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 142, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' )" );     	 
	exit();
}
if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 132, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
}

if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1)";}
else if($db_type==2){ $insert_year="extract(year from b.insert_date)";}

if ($action=="load_drop_down_buyer")
{    
	$data=explode("**",$data);
	$sql="select distinct c.id,c.buyer_name from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where a.job_no_mst=b.job_no and b.company_name=".$data[2]."  and job_no_prefix_num='".$data[0]."' and $insert_year='".$data[1]."' and b.buyer_name=c.id and a.status_active=1";
	$result=sql_select($sql);
	foreach($result as $val)  
	{
		$buyer_value=$val[csf('buyer_name')];
	}
	echo create_drop_down( "txt_buyer_name", 140,$sql, "id,buyer_name", 0, "select Buyer" ,$buyer_value);
	exit();
}

if ($action=="load_drop_down_job")
{    
	$data=explode("**",$data);
	$sql="select distinct b.job_no from wo_po_break_down a,wo_po_details_master b where  a.job_no_mst=b.job_no and b.company_name=".$data[2]."  and job_no_prefix_num='".$data[0]."' and $insert_year='".$data[1]."' and a.status_active=1 and  b.status_active=1";
	$result=sql_select($sql);
	foreach($result as $val)  
	{
		$job_value=$val[csf('job_no')];
	}
	?>
	<input style="width:140px;" type="text"  onDblClick="openmypage_jobNo()" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_job_no" id="txt_job_no" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" value="<? echo $job_value; ?>" /> 
    <?
   	exit();
}

if ($action=="load_drop_down_order")
{   
    $data=explode("**",$data);
    $is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$data[1]");
	if($is_projected_po_allow ==2)
	{
		$sql="select id,po_number from wo_po_break_down where job_no_mst='".$data[0]."' and status_active=1 and is_confirmed=1";
	}
	else
	{
		$sql="select id,po_number from wo_po_break_down where job_no_mst='".$data[0]."' and status_active=1";
	}
	$result=sql_select($sql);
	foreach($result as $val)
	{
		$order_item_id=$val[csf('id')];
	}
    echo create_drop_down( "cboorderno_1", 120, $sql,"id,po_number", 1, "select order","", "change_data(this.value,this.id)","");
    exit();
}

if ($action=="load_drop_down_order_garment")
{
	$ex_data = explode("_",$data);

	$gmt_item_arr=return_library_array( "select a.gmts_item_id from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=".$ex_data[0]." and a.status_active=1",'id','gmts_item_id');
    $gmt_item_id=implode(",",$gmt_item_arr);
	if(count($gmt_item_arr)==1)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[2]", 120, $garments_item,"", 1, "-- Select Item --", $gmt_item_id, "change_color(this.id,this.value)","",$gmt_item_id);
	}
    else if(count($gmt_item_arr)>1)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[2]", 120, $garments_item,"", 1, "-- Select Item --", $selected, "change_color(this.id,this.value)","",$gmt_item_id);
	}
	else if(count($gmt_item_arr)==0)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[2]", 120, $blank_array,"", 1, "-- Select Item --", $selected, "","");
	}
	exit();
}

if ($action=="load_drop_down_color_type")
{   list($po_id,$row_no,$color,$gmt_id)=explode('_',$data);
	
	$sql_dtls=sql_select("select color_type_id from ppl_cut_lay_dtls where order_ids in(".$po_id.") and color_id=$color and gmt_item_id=$gmt_id");

	$color_type_arr=array();
	$sql="SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id and a.id=c.po_break_down_id and b.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id in($po_id) and c.cons>0  group by b.color_type_id";
	foreach(sql_select($sql) as $vals)
	{
		$color_type_arr[$vals[csf("color_type_id")]]=$color_type[$vals[csf("color_type_id")]];
	}
	$status=($sql_dtls[0][csf('color_type_id')])?1:0;
	
	echo create_drop_down( "cboColorType_".$row_no, 100, $color_type_arr,"", 1, "--Select--",$sql_dtls[0][csf('color_type_id')],"",$status,0);
	
	
	exit();
}



if ($action=="load_drop_down_color")
{
	$ex_data = explode("_",$data);
	$color_item_arr=sql_select( "select b.color_number_id from wo_po_color_size_breakdown b where  b.po_break_down_id=".$ex_data[0]." and item_number_id=".$ex_data[1]." and b.status_active=1 and  b.is_deleted=0");
	$color_arr = array();
	foreach ($color_item_arr as $key => $val) 
	{
		$color_arr[$val['COLOR_NUMBER_ID']] = $val['COLOR_NUMBER_ID'];
	}

    $color_item_id=implode(",",$color_arr);
    if(count($color_arr)==1)
	{ 
		echo create_drop_down( "cbocolor_$ex_data[2]", 100, "select distinct a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b where a.id=b.color_number_id and b.po_break_down_id=".$ex_data[0]." and item_number_id=".$ex_data[1]." and b.status_active=1 and  b.is_deleted=0 and a.status_active=1 and  a.is_deleted=0 ","id,color_name", 1, "select color",$color_item_id, "change_marker(this.id,this.value)");
		exit();
	}
	
	if(count($color_arr)==0)
	{ 
		echo create_drop_down( "cbocolor_$ex_data[2]", 100, $blank_array,"id,color_name", 1, "select color", $selected, "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbocolor_$ex_data[2]", 100, "SELECT distinct a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b where a.id=b.color_number_id and b.po_break_down_id=".$ex_data[0]." and item_number_id=".$ex_data[1]." and b.status_active=1 and  b.is_deleted=0 and a.status_active=1 and  a.is_deleted=0","id,color_name", 1, "select color", $selected, "change_marker(this.id,this.value)");	
	}
	exit();
}

if ($action=="load_drop_down_batch")
{
	$ex_data = explode("_",$data);

    $job_no=return_field_value( "job_no_mst", "wo_po_break_down","id=$ex_data[0]");    
    $contrast_color_arr=return_library_array( "select contrast_color_id,contrast_color_id as ccid from wo_pre_cos_fab_co_color_dtls where job_no='$job_no' and gmts_color_id='".$ex_data[1]."'",'contrast_color_id','contrast_color_id');


    if(count($contrast_color_arr)>0)
    {
	    array_push($contrast_color_arr, $ex_data[1]);
	    $all_color_ids = implode(",", $contrast_color_arr);
	}
	else
	{
		 $all_color_ids = $ex_data[1];
	}

	$batch_array=array();
	$sql="select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.color_id in($all_color_ids) and b.po_id='".$ex_data[0]."' and b.status_active=1 and b.is_deleted=0 and a.entry_form in(0,7,17,37,66,68) group by a.id, a.batch_no, a.extention_no";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$batch_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}
	
	$sql="select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.batch_id and b.id=c.dtls_id and a.color_id in($all_color_ids) and c.po_breakdown_id='".$ex_data[0]."' and b.status_active=1 and b.is_deleted=0 and c.entry_form in(14,15,134) and c.trans_type=5 group by a.id, a.batch_no, a.extention_no";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$batch_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}
	
	echo create_drop_down( "cbobatch_$ex_data[2]", 100, $batch_array,"", 1, "select Batch",$selected, "batch_match(this.id,this.value)");
	exit();
}


if ($action=="load_drop_down_order_qty_with_country")
{
	$ex_data = explode("_",$data);
	

	 $sql="select sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id=".$ex_data[0]." and item_number_id=".$ex_data[1]." and color_number_id=".$ex_data[2]." and country_id in (".$ex_data[4].") and status_active=1 group by po_break_down_id,item_number_id,color_number_id ";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		echo "document.getElementById('txtorderqty_$ex_data[3]').value  = '".($row[csf("plan_qty")])."';\n"; 
		$plan_qty=$row[csf("plan_qty")];
	}

	$sql_marker="select sum(b.size_qty) as mark_qty from  ppl_cut_lay_dtls a,ppl_cut_lay_bundle b where a.id=b.dtls_id and  a.order_id=".$ex_data[0]." and a.gmt_item_id=".$ex_data[1]." and a.color_id=".$ex_data[2]." and a.status_active=1 and b.country_id in (".$ex_data[4].") group by a.order_id,a.gmt_item_id,a.color_id ";
//echo $sql_marker;die;
	$result=sql_select($sql_marker);
	foreach($result as $rows)
	{
		
		echo "document.getElementById('txttotallay_$ex_data[3]').value  = '".$rows[csf("mark_qty")]."';\n"; 
		$marker_qty=$rows[csf("mark_qty")];
	}
	$lay_balance=$plan_qty-$marker_qty;
	echo "document.getElementById('txtlaybalanceqty_$ex_data[3]').value  = $lay_balance\n"; 
	
	exit();
}


if ($action=="load_drop_down_order_qty")
{
	$ex_data = explode("_",$data);
	$sql_country="select a.country_id,b.country_name  from  wo_po_color_size_breakdown a,lib_country b where a.country_id=b.id and  a.po_break_down_id=".$ex_data[0]." and a.item_number_id=".$ex_data[1]." and a.color_number_id=".$ex_data[2]." and a.status_active=1   ";
	$result_country=sql_select($sql_country);
	foreach ($result_country as  $value) {
		$country_name_arr[$value[csf('country_id')]]=$value[csf('country_name')];
		$country_id_arr[$value[csf('country_id')]]=$value[csf('country_id')];
	}

	echo "document.getElementById('countryName_$ex_data[3]').value  = '".implode(",",$country_name_arr)."';\n";
	echo "document.getElementById('countryId_$ex_data[3]').value  = '".implode(",",$country_id_arr)."';\n";

	 $sql="select sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id=".$ex_data[0]." and item_number_id=".$ex_data[1]." and color_number_id=".$ex_data[2]." and status_active=1 group by po_break_down_id,item_number_id,color_number_id ";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		echo "document.getElementById('txtorderqty_$ex_data[3]').value  = '".($row[csf("plan_qty")])."';\n"; 
		$plan_qty=$row[csf("plan_qty")];
	}

	$sql_marker="select sum(marker_qty) as mark_qty from  ppl_cut_lay_dtls where order_id=".$ex_data[0]." and gmt_item_id=".$ex_data[1]." and color_id=".$ex_data[2]." and status_active=1 group by order_id,gmt_item_id,color_id ";
	$result=sql_select($sql_marker);
	foreach($result as $rows)
	{
		
		echo "document.getElementById('txttotallay_$ex_data[3]').value  = '".$rows[csf("mark_qty")]."';\n"; 
		$marker_qty=$rows[csf("mark_qty")];
	}
	$lay_balance=$plan_qty-$marker_qty;
	echo "document.getElementById('txtlaybalanceqty_$ex_data[3]').value  = $lay_balance\n"; 
	
	exit();
}

if ($action=="load_drop_down_ship")
{
	$ex_data = explode("_",$data);
	$sql="select id,pub_shipment_date from  wo_po_break_down where id='".$ex_data[0]."' and status_active=1";
	$result=sql_select($sql);
	foreach($result as $row)
	{
	  $ship_data=$row[csf('pub_shipment_date')];
	}
	?>
	   <input style="width:70px;" type="text"   class="datepicker" autocomplete="off"  name="txtshipdate_<? echo $ex_data[2]; ?>" id="txtshipdate_<? echo $ex_data[2]; ?>"  placeholder="Display" value="<? echo change_date_format($ship_data); ?>" readonly/>  
	<?
	exit();
}

if ($action=="tna_date_status")
{
	$ex_data = explode("**",$data);
	$cut_start_date=$ex_data[0];
	$cut_end_date=$ex_data[1];
	$order_all=$ex_data[2];
	//echo $cut_start_date;die;
	//**********************************Tna Date*********************************************************************************************
	for($sl=1; $sl<=$row_num; $sl++)
	   {
			$cbo_order_id="cboorderno_".$sl;
			if($tna_order!="") $tna_order.=",".$$cbo_order_id;
			else $tna_order.=$$cbo_order_id;
	   }
	$tna_variable=return_field_value("tna_integrated","variable_order_tracking"," company_name=$ex_data[3] AND variable_list=14");
	if($tna_variable==1)
	{
	  $min_tna_date=return_field_value(" min(a.task_start_date) as min_start_date","tna_process_mst a, lib_tna_task b"," b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84","min_start_date");
	  $max_tna_date=return_field_value("max(a.task_finish_date) as max_end_date ","tna_process_mst a, lib_tna_task b"," b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84","max_end_date");
	  
	  $order_number_arr=return_library_array( "select id, po_number from wo_po_break_down where id in ($order_all)",'id','po_number');
	  
	//  $min_start_date=date("Y-m_d",strtotime($min_start_date));
	  $max_end_date=date("Y-m_d",strtotime($max_tna_date));
	  $cut_start_date=date("Y-m_d",strtotime($cut_start_date));
	  $cut_end_date=date("Y-m_d",strtotime($cut_end_date));
	  if($cut_end_date>$max_end_date) 
	  {
		 $sql_tna_date=sql_select("select a.po_number_id, a.task_start_date,a.task_finish_date	from  tna_process_mst a, lib_tna_task b where b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84");
		 if(count($sql_tna_date)>0)
			 {
				 foreach($sql_tna_date as $row)
				 {
					 if($poNumber=="")
					 {
						$poNumber=$order_number_arr[$row[csf('po_number_id')]];
						$po_st_date=$row[csf('task_start_date')];
						$po_en_date=$row[csf('task_finish_date')];
						$po_end_date=date("d-m-Y",strtotime($po_en_date));
						$po_start_date=date("d-m-Y",strtotime($po_st_date));
					 }
					 else
					 {
						$poNumber=$poNumber."**".$order_number_arr[$row[csf('po_number_id')]]; 
						$po_st_date=$row[csf('task_start_date')];
						$po_en_date=$row[csf('task_finish_date')];
						$po_start_date=$po_start_date."**".date("d-m-Y",strtotime($po_st_date));
						$po_end_date=$po_end_date."**".date("d-m-Y",strtotime($po_en_date));
					 }
				 }
			  $min_start_date=date("d-m-Y",strtotime($min_tna_date));
			  $max_end_date=date("d-m-Y",strtotime($max_tna_date)); 
			  echo "0##".$poNumber."##".$po_start_date."##".$po_end_date."##".$min_start_date."##".$max_end_date;die;
		 }
		  else echo 1;die;
	  }
	echo 1;die;
	}
	else echo 2;die;
	
		//***********************************End Tna date*******************************************************************************************
}

if($action=="country_popup")
{
	echo load_html_head_contents("Country Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	//$time_weight_panel;
	//echo $hidden_body_partstring;die;
	?>
		<script>
			var hiddiscountryseq='<?=$hiddiscountryseq; ?>';
			var isSeqUse='<?=$isSeqUse; ?>';
			//alert(isSeqUse)
			$(document).ready(function(e) {
				setFilterGrid('tbl_list_search',-1);
			});
			
			var selected_id = new Array(); var selected_name = new Array(); var selected_seq = new Array();
			
			function check_all_data() 
			{
				/*var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
				//alert(tbl_row_count)
				tbl_row_count = tbl_row_count-1;
				//alert(tbl_row_count)
				for( var i = 1; i <= tbl_row_count; i++ ) 
				{
					//var country_id=$('#txt_individual_id'+i).val();
					alert(i)
					js_set_value( i );
				}*/
				document.getElementById('chk_is_seq').checked=false;
				document.getElementById('chk_is_seq').value=0;
				
				$("#tbl_list_search tr").each(function() {
					var valTP=$(this).attr("id");
					if( valTP!=undefined )
					{
						//alert(valTP)
						$("#"+valTP).click();
					}
				});
			}
			
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}
			
			function set_all()
			{
				var old=document.getElementById('txt_po_row_id').value; 
				
				if(hiddiscountryseq==1 || isSeqUse==1)
				{
					document.getElementById('chk_is_seq').checked=true;
					document.getElementById('chk_is_seq').value=1;
					
					if(old!="")
					{   
						old=old.split(",");
						for(var k=0; k<old.length; k++)
						{  
							var seqdata=old[k].split("!");
							
							if(typeof(seqdata[1])!= 'undefined')
							{
								js_set_value( seqdata[0] );
							}
						} 
					}
				}
				else
				{
					document.getElementById('chk_is_seq').checked=false;
					document.getElementById('chk_is_seq').value=0;
					
					if(old!="")
					{   
						old=old.split(",");
						for(var k=0; k<old.length; k++)
						{  
							js_set_value(old[k]);
						} 
					}
				}
			}
			
			function js_set_value( str ) 
			{
				//alert($('#chk_is_seq').val());
				if($('#chk_is_seq').val()==0)
				{
					toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
					if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + str).val() );
						selected_name.push( $('#txt_individual' + str).val() );
						//selected_seq.push( $('#txtseqno_' + str).val() );
					}
					else {
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
						}
						selected_id.splice( i, 1 );
						selected_name.splice( i, 1 );
						//selected_seq.splice( i, 1 );
					}
					
					var id = ''; var name = ''; //var seq = '';
					for( var i = 0; i < selected_id.length; i++ ) {
						id += selected_id[i] + ',';
						name += selected_name[i] + ',';
						//seq += selected_seq[i] + ',';
					}
					
					id = id.substr( 0, id.length - 1 );
					name = name.substr( 0, name.length - 1 );
					//seq = seq.substr( 0, seq.length - 1 );
					
					$('#hidden_search_id').val(id);
					$('#hidden_search_name').val(name);
				}
				else if($('#chk_is_seq').val()==1)
				{
					var seqno=$('#txtseqno_'+str).val()*1;
					//alert(seqno)
					if(seqno>0 )
					{
						toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
						
						var countryid_seq=$('#txt_individual_id' + str).val()+'!'+seqno;
						//alert(countryid_seq)
						
						if( jQuery.inArray( countryid_seq, selected_id ) == -1 ) {
							selected_id.push( countryid_seq );
							selected_name.push( $('#txt_individual' + str).val() );
							//selected_seq.push( $('#txtseqno_' + str).val() );
						}
						else {
							for( var i = 0; i < selected_id.length; i++ ) {
								if( selected_id[i] == countryid_seq ) break;
							}
							selected_id.splice( i, 1 );
							selected_name.splice( i, 1 );
							//$('#txtseqno_'+str).val('')
							//selected_seq.splice( i, 1 );
						}
						var id = ''; var name = ''; //var seq = '';
						for( var i = 0; i < selected_id.length; i++ ) {
							id += selected_id[i] + ',';
							name += selected_name[i] + ',';
							//seq += selected_seq[i] + ',';
						}
						
						id = id.substr( 0, id.length - 1 );
						name = name.substr( 0, name.length - 1 );
						//seq = seq.substr( 0, seq.length - 1 );
						
						$('#hidden_search_id').val(id);
						$('#hidden_search_name').val(name);
					}
					else
					{
						toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
					}
				}
			}
			
			function fnc_seq()
			{
				if(document.getElementById('chk_is_seq').checked==false) document.getElementById('chk_is_seq').value=0;
				else if(document.getElementById('chk_is_seq').checked==true) document.getElementById('chk_is_seq').value=1;
			}
	    </script>

	</head>

	<body>
	<div align="center">
		<fieldset style="width:400px;margin-left:10px">
	    	<input type="hidden" name="hidden_search_id" id="hidden_search_id" class="text_boxes" value="">
	        <input type="hidden" name="hidden_search_name" id="hidden_search_name" class="text_boxes" value="">
	        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="390" class="rpt_table" >
	                <thead>
	                    <th width="40">SL</th>
	                    <th width="200">Country name</th>
                        <th width="70">Country Ship Date</th>
                        <th>Seq.<input type="checkbox" name="chk_is_seq" id="chk_is_seq" onClick="fnc_seq();" value="0" style="width:12px;" ></th>
	                </thead>
	            </table>
	            <div style="width:390px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table" id="tbl_list_search" >
	                <?
	                    $i=1;
	                    $sql_country="select a.country_id, b.country_name, a.country_ship_date from  wo_po_color_size_breakdown a,lib_country b where a.country_id=b.id and  a.po_break_down_id=".$order_id." and a.item_number_id=".$gmt_id." and a.color_number_id=".$color_value." and a.status_active=1 order by a.country_ship_date ASC";
	                   // echo $sql_country;die;
						$result_country=sql_select($sql_country);
						foreach ($result_country as  $value) {
							$country_id_arr[$value[csf('country_id')]]['cname']=$value[csf('country_name')];
							$country_id_arr[$value[csf('country_id')]]['cshipdate']=$value[csf('country_ship_date')];
						}
						unset($result_country);
						
						$excountry=explode(",",$hidden_country_id);
						$countrySeqArr=array();
						foreach($excountry as $cexdata)
						{
							$exseq=explode("!",$cexdata);
							$countrySeqArr[$exseq[0]]=$exseq[1];
						}
						
	                    foreach($country_id_arr as $country_id=>$countryval)
	                    {
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$country_id; ?>" onClick="js_set_value(<?=$country_id; ?>);"> 
								<td width="40" align="center" ><?=$i; ?>
									<input type="hidden" id="txt_individual_id<?=$country_id; ?>" name="txt_individual_id<?=$country_id; ?>" value="<?=$country_id; ?>" />
									<input type="hidden" id="txt_individual<?=$country_id; ?>" name="txt_individual<?=$country_id; ?>" value="<?=$countryval['cname']; ?>" />
								</td>	
								<td width="200" style="word-break:break-all"><?=$countryval['cname']; ?></td>
                                <td width="70" style="word-break:break-all"><?=change_date_format($countryval['cshipdate']); ?></td>
                                <td><input type="text" id="txtseqno_<?=$country_id; ?>" name="txtseqno_<?=$country_id; ?>" class="text_boxes_numeric" onBlur="js_set_value(<?=$country_id; ?>);" value="<?=$countrySeqArr[$country_id]; ?>" style="width:30px" /></td>
							</tr>
							<?
							$i++;
	                    }
	                ?>
	                </table>
	            </div>
	             <table width="370" cellspacing="0" cellpadding="0" style="border:none" align="center">
	                <tr>
	                    <td align="center" height="30" valign="bottom">
	                        <div style="width:100%"> 
                            	<div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                                </div>
	                            <div style="width:50%; float:left" align="left">
	                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" /><!---->
	                                <input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?=$hidden_country_id; ?>"/>
	                            </div>
	                        </div>
	                    </td>
	                </tr>
	            </table>
	        </form>
	    </fieldset>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}

if($action=="roll_popup")
{
  	echo load_html_head_contents("Plies Info Roll Wise","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//$roll_maintained=1;
	$batch_name = return_field_value("batch_no", "pro_batch_create_mst", "id=$batch_id", "batch_no");
?>
	<script>
		var roll_maintained=<? echo $roll_maintained; ?>;
		var rollData='<? echo $rollData; ?>';
		var scanned_barcode=new Array(); var roll_details_array=new Array(); var barcode_array=new Array();
		<?
			$scanned_barcode_array=array();
			$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=97 and status_active=1 and is_deleted=0");
			foreach($scanned_barcode_data as $row)
			{
				$scanned_barcode_array[]=$row[csf('barcode_no')];
			}
			$jsscanned_barcode_array= json_encode($scanned_barcode_array);
			echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";

			$data_array=sql_select("select c.barcode_no, c.roll_id, c.roll_no, c.qnty from pro_grey_batch_dtls b, pro_roll_details c where b.id=c.dtls_id and c.entry_form=72 and c.po_breakdown_id=$order_no and b.color_id=$color and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
			$roll_details_array=array(); $barcode_array=array(); 
			foreach($data_array as $row)
			{
				$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
				$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
				$roll_details_array[$row[csf("barcode_no")]]['qnty']=$row[csf("qnty")];
				
				$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
			}
			
			$jsroll_details_array= json_encode($roll_details_array);
			echo "var roll_details_array = ". $jsroll_details_array . ";\n";
			
			$jsbarcode_array= json_encode($barcode_array);
			echo "var barcode_array = ". $jsbarcode_array . ";\n";
		?>
				
		$(document).ready(function(e) {
            if(roll_maintained==1)
			{
				$('#barcode_div').show();
			}
			else
			{
				$('#barcode_div').hide();
			}
			
			if(rollData!="")
			{
				var data=rollData.split("**");
				for(var k=0; k<data.length; k++)
				{
					var datas=data[k].split("=");
					var barcode_no=datas[0];
					var rollNo=datas[1];
					var rollId=datas[2];
					var rollWgt=datas[3];
					var plies=datas[4];
					var batchNo=datas[5];
					var extraFabric=datas[6];
					var rejectFabric =datas[7];

					var row_num=$('#txt_tot_row').val();
					if($('#barcodeNo_'+row_num).val()!="")
					{
						add_break_down_tr(row_num);
						row_num++;
					}
					
					$("#barcodeNo_"+row_num).val(barcode_no);
					$("#rollNo_"+row_num).val(rollNo);
					$("#rollId_"+row_num).val(rollId);
					$("#rollWgt_"+row_num).val(rollWgt);
					$("#plies_"+row_num).val(plies);
					// $("#batchNo_"+row_num).val(batchNo);
					$("#batchNo_"+row_num).val('<?=$batch_name;?>');
					$("#extraFabric_"+row_num).val(extraFabric);
					$("#rejectFabric_"+row_num).val(rejectFabric);
					
					if( jQuery.inArray( barcode_no, scanned_barcode )>-1) 
					{ 
						scanned_barcode.push(barcode_no); 
					}
				}
			}
        });	
		
		function add_break_down_tr( i )
		{ 
			//var row_num=$('#tbl_list_search tbody tr').length;
			var row_num=$('#txt_tot_row').val();
			row_num++;

			var clone= $("#tr_"+i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});
			
			clone.find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return '' }              
			});
			 
			}).end();
			
			$("#tr_"+i).after(clone);
			
			$('#rollNo_'+row_num).removeAttr("onBlur").attr("onBlur","roll_duplication_check("+row_num+");");
			$("#batchNo_"+row_num).val('<?=$batch_name;?>');
			
			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			
			$('#txt_tot_row').val(row_num);
			set_all_onclick();
		}
		
		function fn_deleteRow(rowNo) 
		{ 
			var numRow = $('#tbl_list_search tbody tr').length; 
			if(numRow!=1)
			{
				$("#tr_"+rowNo).remove();
				var bar_code=$('#barcodeNo_'+row_num).val();
				var index = scanned_barcode.indexOf(bar_code);
				scanned_barcode.splice(index,1);
			}
		}
		
		function roll_duplication_check(row_id)
		{
			var row_num=$('#tbl_list_search tr').length;
			var roll_no=$('#rollNo_'+row_id).val();
			
			if(roll_no*1>0)
			{
				for(var j=1; j<=row_num; j++)
				{
					if(j==row_id)
					{
						continue;
					}
					else
					{
						var roll_no_check=$('#rollNo_'+j).val();	
						if(roll_no==roll_no_check)
						{
							alert("Duplicate Roll No.");
							$('#rollNo_'+row_id).val('');
							return;
						}
					}
				}
			}
		}
		
		$('#txt_bar_code_num').live('keydown', function(e) {
			if (e.keyCode === 13) 
			{
				e.preventDefault();
				var bar_code=$('#txt_bar_code_num').val();
				
				if(!barcode_array[bar_code])
				{ 	
					alert('Barcode is Not Valid');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_bar_code_num').val('');
					return; 
				}

				if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
				{ 
					alert('Sorry! Barcode Already Scanned.'); 
					$('#txt_bar_code_num').val('');
					return; 
				}
				
				var row_num=$('#txt_tot_row').val();
				if($('#barcodeNo_'+row_num).val()!="")
				{
					add_break_down_tr(row_num);
					row_num++;
				}
				load_data(row_num, bar_code);
			}
		});
		
		function openmypage_barcode()
		{ 
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','cut_and_lay_gmts_no_wise_entry_controller.php?order_no='+<? echo $order_no; ?>+'&color='+<? echo $color; ?>+'&action=barcode_popup','Barcode Popup', 'width=480px,height=300px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
				
				if(barcode_nos!="")
				{
					var barcode_upd=barcode_nos.split(",");
					var row_num=$('#txt_tot_row').val();
					for(var k=0; k<barcode_upd.length; k++)
					{
						if($('#barcodeNo_'+row_num).val()!="")
						{
							add_break_down_tr(row_num);
							row_num++;
						}
						
						var bar_code=barcode_upd[k];
						load_data(row_num, bar_code);
					}
				}
			}
		}
		
		function load_data(row_num, bar_code)
		{
			if(bar_code=="") bar_code=0;
			$("#barcodeNo_"+row_num).val(bar_code);
			$("#rollNo_"+row_num).val(roll_details_array[bar_code]['roll_no']);
			$("#rollId_"+row_num).val(roll_details_array[bar_code]['roll_id']);
			$("#rollWgt_"+row_num).val(roll_details_array[bar_code]['qnty']);
			scanned_barcode.push(bar_code);
		}
		
		function fnc_close()
		{
			var save_string='';	var tot_plies='';
			$("#tbl_list_search").find('tr').each(function()
			{
				var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
				var rollNo=$(this).find('input[name="rollNo[]"]').val();
				var rollId=$(this).find('input[name="rollId[]"]').val();
				var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
				var plies=$(this).find('input[name="plies[]"]').val();
				var batchNo=$(this).find('input[name="batchNo[]"]').val();
				var extraFabric=$(this).find('input[name="extraFabric[]"]').val();
				var rejectFabric =$(this).find('input[name="rejectFabric[]"]').val();
				
				if(plies*1>0)
				{
					tot_plies=tot_plies*1+plies*1;
					if(barcodeNo=="") barcodeNo=0;
					if(save_string=="")
					{
						save_string=barcodeNo+"="+rollNo+"="+rollId+"="+rollWgt+"="+plies+"="+batchNo+"="+extraFabric+"="+rejectFabric;
					}
					else
					{
						save_string+="**"+barcodeNo+"="+rollNo+"="+rollId+"="+rollWgt+"="+plies+"="+batchNo+"="+extraFabric+"="+rejectFabric;
					}
				}
			});
			
			$('#hide_data').val( save_string );
			$('#hide_plies').val( tot_plies );
			
			parent.emailwindow.hide();
		}
		
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
	<fieldset style="width:890px">
    	<div style="margin-bottom:5px; display:none" id="barcode_div">
            <strong>Barcode Number</strong>&nbsp;&nbsp;
            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
        </div>
        <table width="890" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
            <thead>
                <th>Roll Number</th>
                <th>Roll Weight</th>
                <th>Batch No</th>
                <th>Extra Fabric</th>
                <th>Reject Fabric</th>
				<? 
					$disbled=""; 
					if($roll_maintained==1) 
					{
						echo "<th>Barcode No</th>"; 
						$disbled="disabled";
					} 
				?>
                <th>Plies</th>
                <th></th>
            </thead>
            <tbody>
				<?
                    $save_string=explode("**",$actual_po_infos); $actual_po_data_array=array(); $i=0;
                    foreach($save_string as $value)
                    {
                        $value=explode("=",$value);
                        $actual_po_data_array[$value[0]]=$value[1];
                    }
                ?>
                <tr id="tr_1" class="general">
                    <td>
                        <input type="text" id="rollNo_1" name="rollNo[]" class="text_boxes_numeric" style="width:110px" onBlur="roll_duplication_check(1);" value="" <? echo $disbled; ?> />
                         <input type="hidden" id="rollId_1" name="rollId[]" value=""/>
                    </td>
                    <td>
                        <input type="text" id="rollWgt_1" name="rollWgt[]" class="text_boxes_numeric" value="" style="width:100px" <? echo $disbled; ?>/>
                    </td>
					<td>
                        <input type="text" id="batchNo_1" name="batchNo[]" class="text_boxes" value="<?=$batch_name;?>" style="width:100px" disabled readonly />
                    </td>
					<td>
                        <input type="text" id="extraFabric_1" name="extraFabric[]" class="text_boxes_numeric" value="" style="width:100px" <? echo $disbled; ?>/>
                    </td>
					<td>
                        <input type="text" id="rejectFabric_1" name="rejectFabric[]" class="text_boxes_numeric" value="" style="width:100px" <? echo $disbled; ?>/>
                    </td>
                    <? if($roll_maintained==1) 
                    { 
                    ?>
                        <td><input type="text" id="barcodeNo_1" name="barcodeNo[]" class="text_boxes_numeric" value="" style="width:100px" disabled/></td>
                    <?
                    } 
					else
					{
					?>
                        <td style="display:none"><input type="text" id="barcodeNo_1" name="barcodeNo[]" class="text_boxes_numeric" value="" style="width:100px" disabled/></td>
                    <?
					}
                    ?>
                    <td>
                        <input type="text" id="plies_1" name="plies[]" class="text_boxes_numeric" value="" style="width:100px"/> 
                    </td>
                    <td width="70">
                    	<? if($roll_maintained!=1) 
						{ 
						?>
							 <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />	
						<?
						} 
						?>
                        <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(1);" />
                    </td>
                </tr>
            </tbody>
        </table>
        <div align="center" style="margin-top:10px">
            <input type="button" class="formbutton" onClick="fnc_close()" value="Close" style="width:100px"/>
            <input type="hidden" id="hide_plies" />
            <input type="hidden" id="hide_data" />
            <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
        </div>
	</fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
	
		$(document).ready(function(e) {
        	setFilterGrid('tbl_list_search',-1);
        });
		
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_barcode_nos').val( id );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
    </script>

</head>

<body>
<div align="center" style="width:450px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:440px; margin-left:2px">
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="420">
                <thead>
                    <th width="50">SL</th>
                    <th width="130">Barcode No</th>
                    <th width="100">Roll No</th>
                    <th>Roll Qty.</th>
                </thead>
            </table>
            <div style="width:420px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" id="tbl_list_search">  
                    <? 
					$scanned_barcode_arr=array();
					$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form=97 and status_active=1 and is_deleted=0");
					foreach ($barcodeData as $row)
					{
						$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
					}
					///echo "select c.barcode_no, c.roll_id, c.roll_no, c.qnty from pro_grey_batch_dtls b, pro_roll_details c where b.id=c.dtls_id and c.entry_form=72 and c.po_breakdown_id=$order_no and b.color_id=$color and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                    $i=1;
                    $data_array=sql_select("select c.barcode_no, c.roll_id, c.roll_no, c.qnty from pro_grey_batch_dtls b, pro_roll_details c where b.id=c.dtls_id and c.entry_form=72 and c.po_breakdown_id=$order_no and b.color_id=$color and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
					foreach($data_array as $row)
                    {  
                        if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                                <td width="50">
                                    <? echo $i; ?>
                                     <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                                </td>
                                <td width="130"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                                <td width="100"><? echo $row[csf('roll_no')]; ?></td>
                                <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                            </tr>
						<? 
							$i++;
						}
                    } 
                    ?>
				</table>
            </div>
            <table width="420">
                <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];

	if($company_id==0) { echo "Please Select Company First."; die; }

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and d.po_number like '$search_string'";
	}
	
	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_num from pro_grey_prod_delivery_dtls where entry_form=56 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_num')]]=$row[csf('barcode_num')];
	}
	
	$sql="SELECT a.recv_number, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond"; 
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="120">System Id</th>
            <th width="110">Job No</th>
            <th width="110">Order No</th>
            <th width="80">Shipment Date</th>
            <th width="100">Barcode No</th>
            <th width="60">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:740px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="60"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="720">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?	
exit();
}

$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');

if($action=="size_popup")
{
  	echo load_html_head_contents("Cut and bundle details","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$size_disable_status=return_field_value("work_study_integrated","variable_settings_production","company_name =$cbo_company_id and variable_list = 10 and status_active=1 ");


	
?>
<script>
	
	var permission='<? echo $permission; ?>';
	var size_disable_status='<? echo $size_disable_status; ?>';
	var rmg_no_creation='<? echo $rmg_no_creation; ?>';
	
	function js_set_value( data)
   	{  
		var data=data.split("_");
		document.getElementById('hidden_batch_no').value=data[0];
		document.getElementById('hidden_batch_id').value=data[1];
		parent.emailwindow.hide();
   	}
	   
	function check_size_qty(value1,value2,id)
	{
		var x=id.split('_');
		var value=(value1*1)*(value2*1);
		var lay_value=$("#txt_lay_balance_"+x[3]).val();
		
		if(value>lay_value)
		{
			alert("Marker qty is geater than Lay Balance");
			$("#txt_size_qty_"+x[3]).css({"background-color":"red"});
		}
		else
		{
			$("#txt_size_qty_"+x[3]).css({"background-color":"white"});	
		}
		$("#txt_size_qty_"+x[3]).val(value);
		
		total_size_qty();
		total_size_ration();
	}
		
	function check_sizef_qty(value1,value2,id)
	{
		var x=id.split('_');
		var prev_qty=$("#txt_sizef_prev_qty_"+x[3]).val()*1;
		var value=(value1*1)*(value2*1);
		var lay_value=$("#txt_layf_balance_"+x[3]).val()*1;
		
		if(value>(lay_value*1+prev_qty*1))
		{
			alert("Marker qty is geater than Lay Balance");
			$("#txt_sizef_qty_"+x[3]).css({"background-color":"red"});
		}
		else
		{
			$("#txt_sizef_qty_"+x[3]).css({"background-color":"white"});	
		}
		$("#txt_sizef_qty_"+x[3]).val(value);
		
		var size_id=$("#hidden_sizef_id_"+x[3]).val();
		calculate_total();
		distribute_qnty(size_id, value2);
		total_size_qty();
		total_size_ration();
	}
	
	function distribute_qnty(size_id, size_ratio)
	{
		var row_num=$("#tbl_roll tbody tr").length;
		for(var i=1; i<=row_num; i++)
		{
			var plies=$("#piles_"+i).val()*1;
			var qty=size_ratio*plies;
				
			$("#sqty_"+size_id+"_"+i).val(qty);
		}
	}
	
	function calculate_total()
	{
		var row_num=$("#tbl_size tbody tr").length;
		var ratio_total=0; var qty_total=0;
		for(var i=1; i<=row_num; i++)
		{ 
			ratio_total=ratio_total+$("#txt_sizef_ratio_"+i).val()*1; 
			qty_total=qty_total+$("#txt_sizef_qty_"+i).val()*1; 
		}
		
		$('#total_sizef_ratio').text(ratio_total); 
		$('#total_sizef_qty').text(qty_total); 
	}
		
	function total_size_ration()
	{
		var row_num=$("#tbl_size_details tbody tr").length;
		ratio_total=0;
		for(var i=1; i<=row_num; i++)
		{ 
			ratio_total+=($("#txt_size_ratio_"+i).val()!='')?$("#txt_size_ratio_"+i).val()*1:0; 
		}
		$('#total_size_ratio').text(ratio_total); 
	}


 	function total_size_qty()
	{ 
		var row_num=$("#tbl_size_details tbody tr").length;
		var tot_qty=0;
		   for(var i=1; i<=row_num; i++)
		   {
			   tot_qty+=($("#txt_size_qty_"+i).val()!='')?$("#txt_size_qty_"+i).val()*1:0; 
			 /*  if($("#txt_size_ratio_"+i).val()!='')
			   {
				 ratio_total+=$("#txt_size_ratio_"+i).val(); 
			   }*/
		   }
			 $('#total_size_qty').text(tot_qty);
			// $('#total_size_ratio').text(ratio_total);
			 $('#hidden_marker_qty').val(tot_qty);
	 }

function fnc_cut_lay_size_info( operation )
{   
	if(form_validation('txt_bundle_pcs','Pcs Per Bundle')==false)
	{
		return;
	}

	var order_id=<? echo $order_id; ?>;
	var gmt_id=<? echo $cbo_gmt_id; ?>;
	var color_id=<? echo $cbo_color_id; ?>;
	var mst_id=<? echo $mst_id; ?>;
	var dtls_id=<? echo $details_id; ?>;
	var cbo_company_id=<? echo $cbo_company_id; ?>;
	
	var bundle_per_pcs=$("#txt_bundle_pcs").val();
	var to_marker_qty=$("#total_sizef_qty").text();
	var job_id=$("#hidden_update_job_id").val();
	var cut_no=$("#hidden_update_cut_no").val();
	var txt_plies=$("#txt_search_common").val();
	var txt_bundle_pcs=$("#txt_bundle_pcs").val();	
	var color_type_id=<? echo $cbo_color_type; ?>;
	//var roll_data=$("#roll_data").val();	
	
	var row_num=$('#tbl_size_details tbody tr').length;
	var data1="action=save_update_delete_size&operation="+operation+"&row_num="+row_num+"&color_id="+color_id+"&mst_id="+mst_id+"&dtls_id="+dtls_id+"&bundle_per_pcs="+bundle_per_pcs+"&to_marker_qty="+to_marker_qty+"&cbo_company_id="+cbo_company_id+"&job_id="+job_id+"&cut_no="+cut_no+"&order_id="+order_id+"&gmt_id="+gmt_id+"&txt_plies="+txt_plies+"&txt_bundle_pcs="+txt_bundle_pcs+"&rmg_no_creation="+rmg_no_creation+"&color_type_id="+color_type_id;
	 var data2=''; var size_data=''; var max_seq=0; var size_arr=[]; var roll_data='';

	var size_row_num=$('#tbl_size tbody tr').length;
	for(var k=1; k<=size_row_num; k++)
	{
		var seq=$("#txt_bundle_"+k).val()*1;
		if(seq>max_seq) max_seq=seq;
		// size_data+=get_submitted_data_string('txt_layf_balance_'+k+'*txt_sizef_ratio_'+k+'*txt_sizef_qty_'+k+'*hidden_sizef_id_'+k+'*txt_bundle_'+k,"../../../",k);
		size_data+='&txt_layf_balance_'+k+'='+$('#txt_layf_balance_'+k).val()+'&txt_sizef_ratio_'+k+'='+$('#txt_sizef_ratio_'+k).val()+'&txt_sizef_qty_'+k+'='+$('#txt_sizef_qty_'+k).val()+'&hidden_sizef_id_'+k+'='+$('#hidden_sizef_id_'+k).val()+'&txt_bundle_'+k+'='+$('#txt_bundle_'+k).val();
		var size_id=$("#hidden_sizef_id_"+k).val();	
		//size_arr[]=size_id;
		size_arr.push(size_id);
	}
	
	var roll_row_num=$("#tbl_roll tbody tr").length;
	for(var i=1; i<=roll_row_num; i++)
	{
		var barcode_no=0;
		var roll_no=$("#rollNo_"+i).val()*1;
		var roll_id=$("#rollId_"+i).val()*1;
		var roll_wgt=$("#rollWgt_"+i).val()*1;
		var plies=$("#piles_"+i).val()*1;
		var batch_no_plies=$("#batchNo_"+i).val()*1;
		var extra_fabric=$("#extraFabric_"+i).val()*1;
		var reject_fabric=$("#rejectFabric_"+i).val()*1;
		
		if(roll_data=="")
		{
			roll_data=barcode_no+"="+roll_no+"="+roll_id+"="+roll_wgt+"="+plies+"="+batch_no_plies+"="+extra_fabric+"="+reject_fabric;
		}
		else
		{
			roll_data+="|"+barcode_no+"="+roll_no+"="+roll_id+"="+roll_wgt+"="+plies+"="+batch_no_plies+"="+extra_fabric+"="+reject_fabric;
		}
		
		for(var z=0; z<size_arr.length; z++)
		{
			var size_id=size_arr[z];
			var qty=$("#sqty_"+size_id+"_"+i).val();
			roll_data+="="+qty
		}
	}
	
	size_data=size_data+"&size_row_num="+size_row_num+"&max_seq="+max_seq+"&roll_data="+roll_data;	
	
	for(var k=1; k<=row_num; k++)
	{
		// data2+=get_submitted_data_string('cboCountryType_'+k+'*cboCountry_'+k+'*txt_lay_balance_'+k+'*txt_size_ratio_'+k+'*txt_size_qty_'+k+'*hidden_size_id_'+k+'*update_size_id_'+k,"../../../",2);
		data2+='&cboCountryType_'+k+'='+$('#cboCountryType_'+k).val()+'&cboCountry_'+k+'='+$('#cboCountry_'+k).val()+'&txt_lay_balance_'+k+'='+$('#txt_lay_balance_'+k).val()+'&txt_size_ratio_'+k+'='+$('#txt_size_ratio_'+k).val()+'&txt_size_qty_'+k+'='+$('#txt_size_qty_'+k).val()+'&hidden_size_id_'+k+'='+$('#hidden_size_id_'+k).val()+'&update_size_id_'+k+'='+$('#update_size_id_'+k).val();
	}
	var data=data1+data2+size_data;
	// alert(data);return;
	freeze_window(operation);
	http.open("POST","cut_and_lay_gmts_no_wise_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_cut_lay_size_info_reponse;
}

function fnc_cut_lay_size_info_reponse()
{
	if(http.readyState == 4) 
	{
		//release_freezing(); return;
		//alert(http.responseText);
		var reponse=trim(http.responseText).split('**');
	    if(reponse[0]==0 || reponse[0]==1)
		 {
			 if(reponse[0]==0)
			 {
				 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
				 {
					$('#msg_box_popp').html("Data Save Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				 });
			 }
			 else if(reponse[0]==1)
			 {
				 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
				 {
					$('#msg_box_popp').html("Data Update Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				 });
			 }
			
			show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3]+'**'+reponse[7],'show_bundle_list_view','search_div','cut_and_lay_gmts_no_wise_entry_controller','setFilterGrid("list_view",-1)');
			var update_size_id=reponse[3].split('_');
			$("#hidden_plant_qty").val(reponse[4]);
			$("#hidden_total_marker").val(reponse[5]);
			$("#hidden_lay_balance").val(reponse[6]);
			$("#hidden_size_marker_qty").val(reponse[8]);
			
			
			if(reponse[7]==1)
			{
				var update_data=reponse[3].split(',');
				var dtlsId_array = new Array();
				for(var k=0; k<update_data.length; k++)
				{
					var datas=update_data[k].split("__");
					var index=datas[1];
					dtlsId_array[index] = datas[0]+"**"+datas[2];
				}
				
				var row_num=$('#tbl_size tbody tr').length;
				for(var i=1;i<=row_num;i++)
				{
					var index=	$("#hidden_size_id_"+i).val();
					var dtls_id=''; var sequence_no='';
					if(dtlsId_array[index])
					{
						var datas=dtlsId_array[index].split("**");
						dtls_id=datas[0];
						sequence_no=datas[1];
					}
					$('#update_size_id_'+i).val(dtls_id);	
					$('#txt_bundle_'+i).val(sequence_no);	
				}
			}
			else
			{
				for(var i=1;i<=update_size_id.length;i++)
				{
					$('#update_size_id_'+i).val(update_size_id[i-1]);	
				}
			}
			set_button_status(1, permission, 'fnc_cut_lay_size_info',1,1);
		}
		
		else if(reponse[0]==15)
		{
			alert("No Data Found");
		}
		else if(reponse[0]==200)
		{
			alert("Update Restricted.This information found in Cutting Qc Page Which System Id "+reponse[3]+".");
		}
		else if(reponse[0]==201)
		{
			alert("Save Restricted.This information found in Cutting Qc Page Which System Id "+reponse[3]+".");
		}
		release_freezing();
	}
} 

function sequence_duplication_check(row_id)
{
	var row_num=$('#tbl_size_details tbody tr').length;
	var sequence_no=$('#txt_bundle_'+row_id).val();

	if(sequence_no*1>0)
	{
		for(var j=1; j<=row_num; j++)
		{
			if(j==row_id)
			{
				continue;
			}
			else
			{
				var sequence_no_check=$('#txt_bundle_'+j).val();	
	
				if(sequence_no==sequence_no_check)
				{
					alert("Duplicate Sequence No.");
					$('#txt_bundle_'+row_id).val('');
					return;
				}
			}
		}
	}
}

function clear_size_form()
	{
		$("#txt_bundle_pcs").val('');
		var row_num=$('#tbl_size_details tbody tr').length;
		for(var i=1;i<=row_num;i++)
		{
		$('#txt_size_ratio_'+i).val('');
		$('#txt_size_qty_'+i).val('');
		$('#txt_bundle_'+i).val('');
		}
		
	}

	function size_popup_close(id,marker,plan,tomarker,lay_balance)
	{
		var pass_string=id+"**"+marker+"**"+plan+"**"+tomarker+"**"+lay_balance;
	
		document.getElementById('hidden_marker_no_x').value=pass_string;
	  	parent.emailwindow.hide();
	}
	
	
	function fnc_print_bundle()
	{
	   var report_title="Cut and Lay bundle ";
	   var country=$('#cboCountryBundle').val();
	   print_report(<? echo $cbo_company_id; ?>+'*'+<? echo $mst_id; ?>+'*'+<? echo $details_id; ?>+'*'+report_title+'*'+country, "cut_lay_bundle_print", "cut_and_lay_gmts_no_wise_entry_controller")
		
	}
	
	function check_all_report()
	{
		$("input[name=chk_bundle]").each(function(index, element) { 
				
				if( $('#check_all').prop('checked')==true) 
		 			$(this).attr('checked','true');
				else
					$(this).removeAttr('checked');
		});
	}

	function fnc_bundle_report(column_list)
	{
		var data="";
		var error=1;
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
			}
		});
	
		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		var job_id=$("#hidden_update_job_id").val();
		data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;
	
		if(column_list==6)
		{
			var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_gmts_no_wise_entry_controller");
			window.open(url,"##");
		}
		else
		{
			var url=return_ajax_request_value(data, "print_report_bundle_barcode_eight", "cut_and_lay_gmts_no_wise_entry_controller");
			window.open(url,"##");	
		}
		
	}
	
	
	function fnc_bundle_report_eight()
	{
		var data="";
		var error=1;
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
			}
		});
	
		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		var job_id=$("#hidden_update_job_id").val();
		data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;
		
		var title = 'Search Job No';	
		var page_link = 'cut_and_lay_gmts_no_wise_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		 {
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			data=data+'***'+prodID;
			var url=return_ajax_request_value(data, "print_barcode_eight", "cut_and_lay_gmts_no_wise_entry_controller");
			window.open(url,"##");	
		 }

		//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_gmts_no_wise_entry_controller");
		//window.open(url,"##");
	}
	
	function fnc_bundle_report_one()
	{
		var data="";
		var error=1;
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
			}
		});
	
		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		var job_id=$("#hidden_update_job_id").val();
		data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;
		
		var title = 'Search Job No';	
		var page_link = 'cut_and_lay_gmts_no_wise_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		 {
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			data=data+'***'+prodID;
			//var url=return_ajax_request_value(data, "print_barcode_one_pdf", "cut_and_lay_gmts_no_wise_entry_controller");
			//window.open(url,"##");	
			window.open("cut_and_lay_gmts_no_wise_entry_controller.php?data=" + data+'&action=print_barcode_one', true );
		 }

		//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_gmts_no_wise_entry_controller");
		//window.open(url,"##");
	}
	
	//fnc_bundle_report_one_urmi
	
	function fnc_bundle_report_one_urmi()
	{
		var data="";
		var error=1;
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
			}
		});
	
		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		var job_id=$("#hidden_update_job_id").val();
		data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;
		
		var title = 'Search Job No';	
		var page_link = 'cut_and_lay_gmts_no_wise_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		 {
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			data=data+'***'+prodID;
			var url=return_ajax_request_value(data, "print_barcode_one_urmi", "cut_and_lay_gmts_no_wise_entry_controller");
			window.open(url,"##");	
			//window.open("cut_and_lay_gmts_no_wise_entry_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
		 }

		//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_gmts_no_wise_entry_controller");
		//window.open(url,"##");
	}
	
	function fnc_bundle_report_one_hams()
	{
		var data="";
		var error=1;
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
			}
		});
	
		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		var job_id=$("#hidden_update_job_id").val();
		data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;
		
		var title = 'Search Job No';	
		var page_link = 'cut_and_lay_gmts_no_wise_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		 {
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			data=data+'***'+prodID;
			var url=return_ajax_request_value(data, "print_barcode_one_hams", "cut_and_lay_gmts_no_wise_entry_controller");
			window.open(url,"##");	
			//window.open("cut_and_lay_gmts_no_wise_entry_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
		 }

		//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_gmts_no_wise_entry_controller");
		//window.open(url,"##");
	}
	
	function fnc_send_printer_text()
	{
		var data="";
		var error=1;
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
			}
		});
	
		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		var job_id=$("#hidden_update_job_id").val();
		data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;
		var url=return_ajax_request_value(data, "report_bundle_text_file", "cut_and_lay_gmts_no_wise_entry_controller");
	
	    window.open(url+".zip","##");
		
		
	}

	function fnc_bundle_report_one_akh()
	{
		
		var data="";
		var error=1;
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				
				error=0;
				var idd=$(this).attr('id').split("_");
				if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
			}
		});
	
		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		var job_id=$("#hidden_update_job_id").val();
		data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;
		
		var title = 'Search Job No';	
		var page_link = 'cut_and_lay_roll_wise_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		 {
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			data=data+'***'+prodID;
			var url=return_ajax_request_value(data, "print_barcode_one_akh", "cut_and_lay_gmts_no_wise_entry_controller");
			window.open(url,"##");	
			//window.open("cut_and_lay_roll_wise_entry_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
		 }
	}

function fnc_addRow(actual_id,i)
{ 
	var row_num=$('#trBundleListSave tr').length;
	row_num++;
	var clone= $("#trBundleListSave_"+actual_id).clone();
	clone.attr({
		id: "trBundleListSave_"+ row_num,
	});
	
	clone.find("input,select").each(function(){
		  
	$(this).attr({ 
	  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
	  'name': function(_, name) { return name },
	  'value': function(_, value) { return value }              
	});
	 
	}).end();
	
	$("#trBundleListSave_"+i).after(clone);
	$('#addButton_'+actual_id).removeAttr("onclick").attr("onclick","fnc_addRow("+actual_id+","+row_num+");");
	$('#bundleSizeQty_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","bundle_calclution("+row_num+");");

	//=================================================================================================================
	$('#addButton_'+row_num).removeAttr("onclick").attr("onclick","delete_bundle_row("+actual_id+","+row_num+");");
	$("#addButton_"+row_num).val('-');
	//===================================================================================================================
	$("#hiddenExtraTr_"+actual_id).val($("#hiddenExtraTr_"+actual_id).val()+"**"+row_num);
	$("#bundleSizeQty_"+actual_id).attr("disabled",false);
    $("#bundleSizeQty_"+row_num).attr("disabled",false);
    $("#bundleNo_"+row_num).attr("disabled",false);
	$("#bundleSizeQty_"+row_num).val('');
	$("#serialNo_"+row_num).html('');
	$("#rmgNoStart_"+row_num).val('');
	$("#rmgNoEnd_"+row_num).val('');
	$("#bundleNo_"+row_num).val($("#bundleNo_"+actual_id).val()+"-");
	$("#hiddenUpdateValue_"+row_num).val('');
	$("#hiddenUpdateFlag_"+actual_id).val(6);
	$("#hiddenUpdateFlag_"+row_num).val(6);
	serial_rearrange();
}

function delete_bundle_row(actual_id,rowNo) 
{ 
	var total_add_id=$("#hiddenExtraTr_"+actual_id).val();
	var countryId=$("#hiddenCountryB_"+rowNo).val();
	var sizeId=$("#hiddenSizeId_"+rowNo).val();
	var pattern=$("#patternNo_"+rowNo).val();
	var rollId=$("#rollId_"+rowNo).val();
	// alert(total_add_id);
	var id_arr=total_add_id.split("**")
	
	id_arr.splice(id_arr.indexOf(rowNo), 1);
	// alert(id_arr.length)
	if( id_arr.length==1)  $('#addButton_'+actual_id).removeAttr("onclick").attr("onclick","fnc_addRow("+actual_id+","+actual_id+");");
	var new_id=id_arr.join("**");
	$("#hiddenExtraTr_"+actual_id).val(new_id);
	//alert( $("#hiddenExtraTr_"+actual_id).val())
	$("#trBundleListSave_"+rowNo).remove();
	//bundle_calclution_on_dlt(countryId,sizeId,pattern,rollId);
	bundle_calclution(rowNo);
	serial_rearrange();
}

function bundle_calclution_on_dlt(countryId,sizeId,pattern,rollId)
{
	var min_rmg_no=1; 
	$("#tbl_bundle_list_save").find('tbody tr').each(function()
	{
		var qty=$(this).find('input[name="bundleSizeQty[]"]').val()*1;
		
		var countryIdC=parseInt($(this).find('input[name="hiddenCountryB[]"]').val());
		var sizeIdC=parseInt($(this).find('input[name="hiddenSizeId[]"]').val());
		var patternNoC=trim($(this).find('input[name="patternNo[]"]').val());
		var rollIdC=parseInt($(this).find('input[name="rollId[]"]').val());
		
		if(countryId==countryIdC && sizeId==sizeIdC && pattern==patternNoC && rollId==rollIdC)
		{
			if(qty*1>0)
			{
				var from=min_rmg_no;
				var to=min_rmg_no*1+qty*1-1;
				min_rmg_no+=qty*1;
				$(this).find('input[name="rmgNoStart[]"]').val(from);
				$(this).find('input[name="rmgNoEnd[]"]').val(to);
			}
			else
			{
				$(this).find('input[name="rmgNoStart[]"]').val('');
				$(this).find('input[name="rmgNoEnd[]"]').val('');
			}
		}
	});
}
	  
function serial_rearrange()
{
	var k=1; 
	$("#tbl_bundle_list_save").find('tbody tr').each(function()
	 {
		$(this).find('input[name="sirialNo[]"]').val(k);
		//alert(k)
		k++;
	
	});
}

function fnc_updateRow(id_row)
{
	$("#bundleSizeQty_"+id_row).attr("disabled",false);
	//$("#sizeName_"+id_row).attr("disabled",false);
	$("#cboCountryB_"+id_row).removeAttr("disabled","disabled");
	if(size_disable_status==1)
	{
		$("#sizeName_"+id_row).attr("disabled","disabled");
	}
	else if(size_disable_status==2) 
	{
		$("#sizeName_"+id_row).removeAttr("disabled","disabled");
	}
	else
	{
		$("#sizeName_"+id_row).attr("disabled","disabled");
	}
	$("#hiddenUpdateFlag_"+id_row).val(6);
}
  
function fnc_rearrange_rmg (id_num)
{
	var s=0;
	var first_rmg=$("#rmgNoStart_1").val();
	var last_rmg=0;
	var bundle_qty=0;
	$("#tbl_bundle_list_save").find('tbody tr').each(function()
	 {
		  bundle_qty=$(this).find('input[name="bundleSizeQty[]"]').val();
		 
		 if(s==0) 
		 {
			 $(this).find('input[name="rmgNoEnd[]"]').val(parseInt(bundle_qty)+parseInt(first_rmg)-1);
			  last_rmg=parseInt(bundle_qty)+parseInt(first_rmg)-1;
		 }
		 else
		 {
			$(this).find('input[name="rmgNoStart[]"]').val(parseInt(last_rmg)+1);
			last_rmg=parseInt(last_rmg)+parseInt(bundle_qty); 
			$(this).find('input[name="rmgNoEnd[]"]').val(parseInt(last_rmg));
		 }
		s++;
	});
}

//function bundle_calclution(actual_id,row_id)
function bundle_calclution(rowNo)
{
	/*var countryId=$("#hiddenCountryB_"+rowNo).val();
	var sizeId=$("#hiddenSizeId_"+rowNo).val();
	var pattern=$("#patternNo_"+rowNo).val();
	var rollId=$("#rollId_"+rowNo).val();
	
	var min_rmg_no=1; 
	$("#tbl_bundle_list_save").find('tbody tr').each(function()
	{
		var qty=$(this).find('input[name="bundleSizeQty[]"]').val()*1;
		
		var countryIdC=parseInt($(this).find('input[name="hiddenCountryB[]"]').val());
		var sizeIdC=parseInt($(this).find('input[name="hiddenSizeId[]"]').val());
		var patternNoC=trim($(this).find('input[name="patternNo[]"]').val());
		//var rollIdC=parseInt($(this).find('input[name="rollId[]"]').val()); && rollId==rollIdC
		
		if(countryId==countryIdC && sizeId==sizeIdC && pattern==patternNoC)
		{
			if(qty*1>0)
			{
				var from=min_rmg_no;
				var to=min_rmg_no*1+qty*1-1;
				min_rmg_no+=qty*1;
				$(this).find('input[name="rmgNoStart[]"]').val(from);
				$(this).find('input[name="rmgNoEnd[]"]').val(to);
			}
			else
			{
				$(this).find('input[name="rmgNoStart[]"]').val('');
				$(this).find('input[name="rmgNoEnd[]"]').val('');
			}
		}
	});*/
	
	var min_rmg_no=0; var size_id_arr=new Array();
	$("#tbl_bundle_list_save").find('tbody tr').each(function()
	{
		var bundle_from=parseInt($(this).find('input[name="rmgNoStart[]"]').val());
		if(min_rmg_no==0) 
		{
			min_rmg_no=bundle_from;
		}
		else
		{
			if(min_rmg_no*1>bundle_from)
			{
				min_rmg_no=bundle_from;
			}
		}
	});
	
	$("#tbl_bundle_list_save").find('tbody tr').each(function()
	{
		var qty=$(this).find('input[name="bundleSizeQty[]"]').val()*1;
		var size_id=$(this).find('input[name="hiddenSizeId[]"]').val()*1;
		
		if(qty*1>0)
		{
			if(rmg_no_creation==1)
			{
				if( jQuery.inArray( size_id, size_id_arr )>-1) 
				{
					var from=min_rmg_no;
					var to=min_rmg_no*1+qty*1-1;
					min_rmg_no+=qty*1;
					
					$(this).find('input[name="rmgNoStart[]"]').val(from);
					$(this).find('input[name="rmgNoEnd[]"]').val(to);
				}
				else
				{
					size_id_arr.push(size_id);
					min_rmg_no=1;
					var from=min_rmg_no;
					var to=min_rmg_no*1+qty*1-1;
					min_rmg_no+=qty*1;
					
					$(this).find('input[name="rmgNoStart[]"]').val(from);
					$(this).find('input[name="rmgNoEnd[]"]').val(to);
				}
			}
			else
			{
				var from=min_rmg_no;
				var to=min_rmg_no*1+qty*1-1;
				min_rmg_no+=qty*1;
				
				$(this).find('input[name="rmgNoStart[]"]').val(from);
				$(this).find('input[name="rmgNoEnd[]"]').val(to);
			}
		}
		else
		{
			$(this).find('input[name="rmgNoStart[]"]').val('');
			$(this).find('input[name="rmgNoEnd[]"]').val('');
		}
	});
}
//**********************************************bundle update *****************************************************************************************
  
function fnc_cut_lay_bundle_info(operation) 
{ 
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		var dataString_bundle="";  
		var j=0; var z=0; var tot_row=0; var sl=0; var error=0;
		var cbo_color_type=<? echo $cbo_color_type; ?>;
		$("#tbl_bundle_list_save").find('tbody tr').each(function()
		{
			var bundle_break=($(this).find('input[name="bundleNo[]"]').val()).split('-');
			var bundle_no_split_length=bundle_break.length;
			if(bundle_no_split_length>3)
			{
				var check_bundle_prifix=bundle_no_split_length-1;
				 if(bundle_break[check_bundle_prifix]=="")
				 {
				  $(this).find('input[name="bundleNo[]"]').css({"background-color":"red"});
				  error=1;
				 }
			}
			/*var bundle_no=($(this).find('input[name="bundleNo[]"]').val()).match("/");
			if(bundle_no=="/")
			{
				 var bundle_break=($(this).find('input[name="bundleNo[]"]').val()).split('/');
				 if(bundle_break[1]=="")
				 {
				  $(this).find('input[name="bundleNo[]"]').css({"background-color":"red"});
				  error=1;
				 }
			}*/
		
			sl++;
		});
		
		if(error==1) { return;}
		
		$("#tbl_bundle_list_save").find('tbody tr').each(function()
		 {
			var bundle_no=$(this).find('input[name="bundleNo[]"]').val();
			var bundle_size_qty=$(this).find('input[name="bundleSizeQty[]"]').val();
			var bundle_from=$(this).find('input[name="rmgNoStart[]"]').val();
			var bundle_to=$(this).find('input[name="rmgNoEnd[]"]').val();
			var bundle_size_id=$(this).find('select[name="sizeName[]"]').val();
			var hidden_size_id=$(this).find('input[name="hiddenSizeId[]"]').val();
			var hidden_size_qty=$(this).find('input[name="hiddenSizeQty[]"]').val();
			var hidden_update_flag=$(this).find('input[name="hiddenUpdateFlag[]"]').val();
			var hiddenUpdateValue=$(this).find('input[name="hiddenUpdateValue[]"]').val();
			var rollNo=$(this).find('input[name="rollNo[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var patternNo=$(this).find('input[name="patternNo[]"]').val();
			var isExcess=$(this).find('input[name="isExcess[]"]').val();
			
			var hiddenCountryType=$(this).find('input[name="hiddenCountryTypeB[]"]').val();
			var cboCountry=$(this).find('select[name="cboCountryB[]"]').val();
			var hiddenCountry=$(this).find('input[name="hiddenCountryB[]"]').val();
			
			j++;
			tot_row++;
			dataString_bundle+='&txtBundleNo_' + j + '=' + bundle_no + '&txtBundleQty_' + j + '=' + bundle_size_qty + '&txtBundleFrom_' + j + '=' + bundle_from+ '&txtBundleTo_' + j + '=' + bundle_to+ '&txtSizeId_' + j + '=' + bundle_size_id+ '&txtHiddenSizeId_' + j + '=' + hidden_size_id+ '&hiddenSizeqty_' + j + '=' + hidden_size_qty+ '&hiddenUpdateFlag_' + j + '=' + hidden_update_flag+'&hiddenUpdateValue_' + j + '=' + hiddenUpdateValue+'&hiddenCountryType_' + j + '=' + hiddenCountryType+'&hiddenCountry_' + j + '=' + hiddenCountry+'&cboCountry_' + j + '=' + cboCountry +'&rollNo_' + j + '=' + rollNo +'&rollId_' + j + '=' + rollId +'&patternNo_' + j + '=' + patternNo +'&isExcess_' + j + '=' + isExcess;
		});
		
		var order_id=<? echo $order_id; ?>;
		var bundle_mst_id=$("#hidden_mst_id").val();
		var bundle_dtls_id=$("#hidden_detls_id").val();
		//alert(bundle_dtls_id);return;
		var hidden_cutting_no=$("#hidden_cutting_no").val();
		var data="action=save_update_delete_bundle&operation="+operation+'&tot_row='+tot_row+'&bundle_dtls_id='+bundle_dtls_id+'&bundle_mst_id='+bundle_mst_id+dataString_bundle+'&hidden_cutting_no='+hidden_cutting_no+"&order_id="+order_id+'&color_type_id='+cbo_color_type;
		//alert(data);return;hidden_cutting_no
		freeze_window(operation);
		http.open("POST","cut_and_lay_gmts_no_wise_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_cut_lay_bundle_reply_info;
}

function fnc_cut_lay_bundle_reply_info()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');	
			
		show_msg(trim(reponse[0]));
		
		if((reponse[0]==0 || reponse[0]==1))
		{
			$('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
			{
				$('#msg_box_popp').html("Data Update  Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			set_button_status(1, permission, 'fnc_cut_lay_bundle_info',2);	
			show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3],'show_bundle_list_view','search_div','cut_and_lay_gmts_no_wise_entry_controller','setFilterGrid("list_view",-1)');
		}
		else if(reponse[0]==200)
		{
			alert("Update Restricted.This information found in Cutting Qc Page Which System Id "+reponse[1]+".");
		}
		release_freezing();	
	}
}

function fnc_adjust_ratio(id)
{
	r=confirm("If You Want to Edit Size Ratio ,Please Adjust Carefully According to Size and Country of Your Own Responsibility");	
	if(r==false)
	{
		return;	
	}
	else
	{
		$("#txt_size_ratio_"+id).removeAttr('disabled','disabled');
		$("#txt_size_qty_"+id).removeAttr('readonly','readonly');
	}
}

function fnc_rollWiseSizeQty()
{
	var size_row_num=$('#tbl_size tbody tr').length;
	var size_data='';
	for(var k=1; k<=size_row_num; k++)
	{
		var hidden_sizef_id=$("#hidden_sizef_id_"+k).val();
		var txt_sizef_ratio=$("#txt_sizef_ratio_"+k).val();
		
		if(size_data=="") 
		{
			size_data=hidden_sizef_id+"_"+txt_sizef_ratio;
		}
		else
		{
			size_data+="|"+hidden_sizef_id+"_"+txt_sizef_ratio;
		}
	}
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','cut_and_lay_gmts_no_wise_entry_controller.php?rollData='+'<? echo $rollData; ?>'+'&size_data='+size_data+'&action=rollSize_popup','Roll Popup', 'width=680px,height=300px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var roll_data=this.contentDoc.getElementById("hidden_roll_data").value; //Barcode Nos
		$("#roll_data").val(roll_data);
	}
}

function fnc_bundle_report_qr_code()
{
	var data="";
	var error=1;
	$("input[name=chk_bundle]").each(function(index, element) {
		if( $(this).prop('checked')==true)
		{			
			error=0;
			var idd=$(this).attr('id').split("_");
			if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
		}
	});

	if( error==1 )
	{
		alert('No data selected');
		return;
	}
	var job_id=$("#hidden_update_job_id").val();
	var order_id='<? echo $order_id; ?>';
	data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;
	
	var title = 'Search Job No';	
	var page_link = 'cut_and_lay_gmts_no_wise_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var prodID=this.contentDoc.getElementById("txt_selected_id").value;
		data=data+'***'+prodID;
		//var url=return_ajax_request_value(data, "print_barcode_operation", "cut_and_lay_gmts_no_wise_entry_controller");
		http.open( 'POST', 'cut_and_lay_gmts_no_wise_entry_controller.php?action=print_qrcode_operation&data='+ data );

		http.onreadystatechange = response_pdf_data;
		http.send(null);
	 }
}

function fnc_bundle_report_qr2_code()
{
	var data="";
	var error=1;
	$("input[name=chk_bundle]").each(function(index, element) {
		if( $(this).prop('checked')==true)
		{			
			error=0;
			var idd=$(this).attr('id').split("_");
			if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
		}
	});

	if( error==1 )
	{
		alert('No data selected');
		return;
	}
	var job_id=$("#hidden_update_job_id").val();
	var order_id='<? echo $order_id; ?>';
	data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;
	
	var title = 'Search Job No';	
	var page_link = 'cut_and_lay_gmts_no_wise_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var prodID=this.contentDoc.getElementById("txt_selected_id").value;
		data=data+'***'+prodID;
		//var url=return_ajax_request_value(data, "print_barcode_operation", "cut_and_lay_gmts_no_wise_entry_controller");
		http.open( 'POST', 'cut_and_lay_gmts_no_wise_entry_controller.php?action=print_qrcode_operation2&data='+ data );

		http.onreadystatechange = response_pdf_data;
		http.send(null);
	 }
}

function response_pdf_data() 
{
	if(http.readyState == 4) 
	{
		var response = http.responseText.split('###');
		window.open(''+response[1], '', '');
	}
}
</script>

</head>
<body onLoad="set_hotkey()">
<div id="msg_box_popp" style=" height:15px; width:200px;  position:relative; left:250px "></div>
	<div align="center" style="width:100%; overflow-y:hidden; position:absolute; top:5px;">
		<input type="hidden" id="hidden_cutting_no" name="hidden_cutting_no" value="<? echo $cutting_no; ?>" />
		<? 
			echo load_freeze_divs ("../../../",$permission); 
			$color_name=return_field_value("color_name","lib_color","id='".$cbo_color_id."'"); 
			$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
			$pcs_per_bundle=return_field_value("pcs_per_bundle","ppl_cut_lay_dtls","id=$details_id ","pcs_per_bundle");  
		?>
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <fieldset style="width:450px;">
                <table cellpadding="0" cellspacing="0" width="450" class="" id="tbl_bundle_size">
                    <thead>
                        <tr>
                            <td><strong>Color</strong></td>
                            <td>
                                <input type="text" style="width:80px" class="text_boxes"  name="txt_show_color" id="txt_show_color" value="<? echo $color_name; ?>" readonly/>
                                <input type="hidden" id="hidden_update_job_id" name="hidden_update_job_id" value="<? echo $job_id; ?>"/>
                                <input type="hidden" id="hidden_update_cut_no" name="hidden_update_cut_no" value="<? echo $cutting_no; ?>"/>
                            </td>
                            <td><strong>Plies</strong></td>
                            <td>
                                <input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<?php echo $txt_piles;?>" readonly/>
                            </td>
                            <td class="must_entry_caption"><strong>Pcs Per Bundle</strong></td>
                            <td><input type="text" style="width:50px" class="text_boxes_numeric"  name="txt_bundle_pcs" id="txt_bundle_pcs" value="<? echo $pcs_per_bundle; ?>" /></td>
                        </tr>
                    </thead>
                </table>
            </fieldset>
            <br/>
            <fieldset style="width:800px;">
            <?
            //echo $hiddiscountryseq;die;
			if($hiddiscountryseq==1)
			{
				$excountry=explode(",",$cbo_countries); $countrystr="";
				foreach($excountry as $cseq)
				{
					$excountryseq=explode("!",$cseq);
					if($countrystr=="") $countrystr=$excountryseq[0]; else $countrystr.=','.$excountryseq[0];
				}
				$cbo_countries=$countrystr;
			}
			
            $master_contry_cond="";
            $size_contry_cond="";
            if($cbo_countries) $master_contry_cond=" and country_id in (".$cbo_countries.")";
			if($cbo_countries) $master_contryseq_cond=" and a.country_id in (".$cbo_countries.")";
            if($cbo_countries) $size_contry_cond=" and a.country_id in (".$cbo_countries.")";

			$po_country_array=array(); $size_order_arr=array();
			if($hiddiscountryseq==1)
			{
				$sql_query=sql_select("select a.country_type, a.country_id, a.size_number_id, a.plan_cut_qnty, a.country_ship_date, a.size_order, b.sequence_no from wo_po_color_size_breakdown a, ppl_cut_lay_country_seq_dtls b where a.item_number_id=$cbo_gmt_id and a.po_break_down_id=$order_id and a.color_number_id=$cbo_color_id and a.country_id=b.country_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $master_contryseq_cond and b.dtls_id=$details_id group by  a.country_type, a.country_id, a.size_number_id, a.plan_cut_qnty, a.country_ship_date, a.size_order, b.sequence_no, a.id order by b.sequence_no, a.size_order, a.country_ship_date, a.country_type, a.id");
				//echo "select a.country_type, a.country_id, a.size_number_id, a.plan_cut_qnty, a.country_ship_date, a.size_order, b.sequence_no from wo_po_color_size_breakdown a, ppl_cut_lay_country_seq_dtls b where a.item_number_id=$cbo_gmt_id and a.po_break_down_id=$order_id and a.color_number_id=$cbo_color_id and a.country_id=b.country_id and a.status_active=1 and a.is_deleted=0 $master_contryseq_cond  group by  a.country_type, a.country_id, a.size_number_id, a.plan_cut_qnty, a.country_ship_date, a.size_order, b.sequence_no, a.id order by b.sequence_no, a.size_order, a.id";
			}
			else
			{
				$sql_query=sql_select("select country_type, country_id, size_number_id, plan_cut_qnty, country_ship_date, size_order, 0 as sequence_no from wo_po_color_size_breakdown where item_number_id=$cbo_gmt_id and po_break_down_id=$order_id and color_number_id=$cbo_color_id and status_active=1 and is_deleted=0 $master_contry_cond order by size_order, country_ship_date, country_type, id");
			}
			$size_details=array(); $sizeId_arr=array(); $shipDate_arr=array();
			foreach($sql_query as $row)
			{
				//if($hiddiscountryseq==1) $row[csf('size_number_id')]=$row[csf('size_number_id')];
				//if($row[csf('country_type')]==1) $country_id=0; else $country_id=$row[csf('country_id')];
				$country_id=$row[csf('country_id')];
				$size_details[$row[csf('sequence_no')]][$row[csf('country_type')]][$country_id][$row[csf('size_number_id')]]+=$row[csf("plan_cut_qnty")];
				$sizeId_arr[$row[csf('size_order')]][$row[csf('size_number_id')]]+=$row[csf("plan_cut_qnty")];
				$shipDate_arr[$row[csf('country_type')]][$country_id]=$row[csf("country_ship_date")];
				$po_country_array[$country_id]=$country_arr[$country_id];
				$size_order_arr[$row[csf('size_number_id')]]=$row[csf("size_order")];
			}
			ksort($size_details);
			ksort($sizeId_arr);
			//echo "<pre>";
			//print_r($size_details);
                
                $size_wise_arr=array();
                $sizeWiseData=sql_select("select size_ratio, size_id, marker_qty, bundle_sequence from ppl_cut_lay_size_dtls where mst_id=".$mst_id." and dtls_id=".$details_id." and status_active=1");
                foreach($sizeWiseData as $value)
                {
                    $size_wise_arr[$value[csf('size_id')]]['ratio']=$value[csf('size_ratio')];
                    $size_wise_arr[$value[csf('size_id')]]['marker_qty']=$value[csf('marker_qty')];
					$size_wise_arr[$value[csf('size_id')]]['seq']=$value[csf('bundle_sequence')];
                }
                
                $sizeDaraArr=array();
                $sizeData=sql_select("select a.id, a.size_ratio, a.size_id, a.marker_qty, a.bundle_sequence, a.country_type, a.country_id from ppl_cut_lay_size a where a.mst_id=".$mst_id." and a.dtls_id=".$details_id." and a.status_active=1");
                if(count($sizeData)>0)
                {
                    $is_update=1;
                    foreach($sizeData as $value)
                    {
                        $sizeDaraArr[$value[csf('country_type')]][$value[csf('country_id')]][$value[csf('size_id')]]=$value[csf('size_ratio')]."**".$value[csf('marker_qty')]."**".$value[csf('bundle_sequence')]."**".$value[csf('id')];	
                    }
                }
                else
                {
                    $is_update=0;
                }

                $lay_bl_qty_arr=array();
                $lay_blData=sql_select("select sum(a.marker_qty) as marker_qty, a.country_type, a.country_id, a.size_id from ppl_cut_lay_size a, ppl_cut_lay_dtls b where b.id=a.dtls_id and b.order_id=$order_id and b.gmt_item_id=$cbo_gmt_id and a.color_id=$cbo_color_id  and a.status_active=1 $size_contry_cond group by a.country_type,a.country_id, a.size_id");
                foreach($lay_blData as $value)
                {
                    $lay_bl_qty_arr[$value[csf('country_type')]][$value[csf('country_id')]][$value[csf('size_id')]]=$value[csf('marker_qty')];
                    $lay_bl_qty_size_arr[$value[csf('size_id')]]+=$value[csf('marker_qty')];
                }
				
				$size_bl_qty_arr=return_library_array("select sum(a.size_qty) as size_qty, a.size_id from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where b.id=a.dtls_id and b.order_id=$order_id and b.gmt_item_id=$cbo_gmt_id and b.color_id=$cbo_color_id and a.status_active=1 $size_contry_cond group by a.size_id",'size_id','size_qty');

                ?>
                <table cellpadding="0" cellspacing="0" width="370" id="tbl_size">
                    <thead class="form_table_header">
                        <th>Size</th>
                        <th>Lay Balance</th>
                        <th>Size Ratio</th>
                        <th>Size Qty.</th>
                        <th>Bundle Priority</th>
                    </thead>
                    <tbody>
                    <?  
						//print_r($size_bl_qty_arr);
                        $i=1; $total_layf_balance=0; $total_markerf_qty=0; $total_sizef_ratio=0; $sizeDataArray=array();
                        //asort($size_order_arr);
						//foreach($sizeId_arr as $size_id=>$plan_cut_qty)
						$total_plan_cut_qty=0;
						foreach($sizeId_arr as $size_order=>$size_order_val)
                        {
							foreach($size_order_val as $size_id=>$plan_cut_qty)
							{
								//echo $plan_cut_qty."-".$lay_bl_qty_size_arr[$size_id];
								//$lay_balance=$plan_cut_qty-$lay_bl_qty_size_arr[$size_id]+$size_wise_arr[$size_id]['marker_qty'];
								//$plan_cut_qty=$sizeId_arr[$size_id];
								$total_plan_cut_qty+=$plan_cut_qty;
								$lay_balance=$plan_cut_qty-$size_bl_qty_arr[$size_id];
								$total_layf_balance+=$lay_balance;
								$data=explode("**",$sizeDaraArr[$country_type_id][$country_id][$size_id]);
								$total_markerf_qty+=$size_wise_arr[$size_id]['marker_qty'];
								$total_sizef_ratio+=$size_wise_arr[$size_id]['ratio'];
								
								$sizeDataArray[$size_id]=$size_wise_arr[$size_id]['ratio'];
								?>
								<tr id="size_<? echo $i; ?>">
									<td align="center">	
										<input type="text" style="width:80px" class="text_boxes" name="txt_sizef_<? echo $i; ?>" id="txt_sizef_<? echo $i; ?>" value="<? echo $size_arr[$size_id]; ?>"  readonly/>
										<input type="hidden" id="hidden_sizef_id_<? echo $i; ?>" name="hidden_sizef_id_<? echo $i; ?>" value="<? echo $size_id; ?>">
									</td>                 
									<td align="center">				
										<input type="text" style="width:80px" class="text_boxes_numeric"  name="txt_layf_balance_<? echo $i; ?>" id="txt_layf_balance_<? echo $i; ?>"  value="<? echo $lay_balance; ?>" />
									</td> 
									<td align="center">				
										<input type="text" style="width:80px" class="text_boxes_numeric" onKeyUp="check_sizef_qty(<? echo $txt_piles; ?>,this.value,this.id)" name="txt_sizef_ratio_<? echo $i; ?>" id="txt_sizef_ratio_<? echo $i; ?>" value="<? echo $size_wise_arr[$size_id]['ratio']; ?>" />	
									</td>
									<td align="center">				
										<input type="text" style="width:80px" class="text_boxes_numeric" name="txt_sizef_qty_<? echo $i; ?>" id="txt_sizef_qty_<? echo $i; ?>"  value="<? echo $size_wise_arr[$size_id]['marker_qty']; ?>" readonly />	
										<input type="hidden" name="txt_sizef_prev_qty_<? echo $i; ?>" id="txt_sizef_prev_qty_<? echo $i; ?>"  value="<? echo $size_wise_arr[$size_id]['marker_qty']; ?>" />	
									</td>
									<td align="center">				
										<input type="text" style="width:40px" class="text_boxes_numeric" name="txt_bundle_<? echo $i; ?>" id="txt_bundle_<? echo $i; ?>" onKeyUp="sequence_duplication_check(<? echo $i; ?>)" value="<? echo $size_wise_arr[$size_id]['seq']; ?>" />	
									</td>
								</tr>
							<?
								$i++;
							}
						}	
						$allData=$rollData;
                    ?>
                    </tbody>
                    <tfoot>
                        <tr class="form_table_header">
                            <th>Total</th>
                            <th align="right"><? echo $total_layf_balance; ?></th>
                            <th id="total_sizef_ratio" align="right"><? echo $total_sizef_ratio; ?></th>
                            <th id="total_sizef_qty" align="right"><? echo $total_markerf_qty; ?>
                            <input type='hidden' id="hidden_size_marker_qty" name="hidden_size_marker_qty" value="<? echo $total_markerf_qty; ?>"/></th>
                            <th><input type='hidden' id="roll_data" name="roll_data" value="<? //echo chop($allData,'|'); ?>"/></th>
                        </tr>
                    </tfoot>
                </table>
                <br>
                <div>
                	<!--<input type="button" style="width:150px" value="Roll Wise Size Qty" name="btn" id="btn" class="formbuttonplasminus" onClick="fnc_rollWiseSizeQty();"/>-->
                    <fieldset style="width:780px">
                    <legend>Roll Wise Size Qty</legend>
                    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="770" id="tbl_roll">
                            <thead>
                                <th width="60">Roll No</th>
                                <th width="70">Roll Wgt.</th>
                                <th width="60">Plies</th>
                                <?
                                foreach($sizeDataArray as $key=>$value)
                                {
                                    echo '<th>'.$size_arr[$key].'</th>';
                                } 
                                ?>
                            </thead>
                           	<?  
								$i=1; $rollDatas=explode("**",$allData);
								foreach($rollDatas as $data)
								{
                                    $datas=explode("=",$data);
                                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                     ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>"> 
                                        <td>
                                            <input type="text" id="rollNo_<? echo $i;?>" name="rollNo[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[1]; ?>" disabled>
                                            <input type="hidden" id="rollId_<? echo $i; ?>" name="rollId[]" value="<? echo $datas[2]; ?>">
                                        </td>
                                        <td><input type="text" id="rollWgt_<? echo $i; ?>" name="rollWgt[]" style="width:60px" class="text_boxes_numeric" value="<? echo $datas[3]; ?>" disabled></td>
                                        <td><input type="text" id="piles_<? echo $i; ?>" name="piles[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[4]; ?>" disabled></td>
                                        <?
                                        foreach($sizeDataArray as $key=>$value)
                                        {
                                        ?>
                                            <td align="center"><input type="text" id="sqty_<? echo $key."_".$i; ?>" name="sqty[]" style="width:50px" class="text_boxes_numeric" value="<? if($value*$datas[4]>0) echo $value*$datas[4]; ?>" disabled></td>
                                        <?
                                        } 
                                        ?>
                                    </tr>
                                    <? 
                                    $i++;
                                } 
                        	?>
                        </table>
                        <table>
                        	<tr>
                                <td align="center" valign="middle" colspan="5" >
                                    <? echo load_submit_buttons( $permission, "fnc_cut_lay_size_info", $is_update,0,"clear_size_form()",1);?>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" colspan="7" >
                                	
                                    <input type="button" id="close_size_id" name="close_size_id"  class="formbutton"  style="width:50px" onClick="size_popup_close(<? echo $size; ?>,$('#hidden_size_marker_qty').val(),$('#hidden_plant_qty').val(),$('#hidden_total_marker').val(),$('#hidden_lay_balance').val())" value="Close"/>
                                    <? echo create_drop_down("cboCountryBundle",120,$po_country_array,'',1,'-- ALL Country --','','',0); ?>
                                    <input type="button" id="btn_print" name="btn_print" value=" Bundle List" class="formbutton" onClick="fnc_print_bundle()"/>
                                    <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 6/Page" class="formbutton" onClick="fnc_bundle_report(6)" style="display:none"/>
                                    <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 8/Page" class="formbutton" onClick="fnc_bundle_report_eight()"/>
                                     <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 1/Page" class="formbutton" onClick="fnc_bundle_report_one()" style="display:none"/>
                                     <input type="button" id="btn_bundle_stiker_hams" name="btn_bundle_stiker_hams" value="Sticker 1/Page Hams" class="formbutton" onClick="fnc_bundle_report_one_hams()"/>
                                     <input type="button" id="btn_stiker_urmi" name="btn_stiker_urmi" value="Sticker 1/Page" class="formbutton" onClick="fnc_bundle_report_one_urmi()"/>
                                     <input type="button" id="btn_stiker_akh" name="btn_stiker_akh" value="Sticker 1.03/Page " class="formbutton" onClick="fnc_bundle_report_one_akh()"/>
                                    <input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text()"/>
                                    <input type="button" id="btn_stiker_qr" name="btn_stiker_qr" value="QR" class="formbutton" onClick="fnc_bundle_report_qr_code()"/>
                                    <input type="button" id="btn_stiker_qr2" name="btn_stiker_qr2" value="QR2" class="formbutton" onClick="fnc_bundle_report_qr2_code()"/>
                                    <input type='hidden' id="hidden_marker_no_x" name="hidden_marker_no_x"  value="<? echo $marker_quantity; ?>"/>
                                    <input type='hidden' id="hidden_total_marker" name="hidden_total_marker" value="<? echo $total_lay_qty; ?>" />
                                    <input type='hidden' id="hidden_lay_balance" name="hidden_lay_balance"  value="<? echo $total_lay_balance; ?>" />
                                    <input type='hidden' id="hidden_plant_qty" name="hidden_plant_qty"  value="<? echo $order_quantity; ?>" />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
                <br>
                <h3 align="left" id="accordion_h1" style="width:600px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> +Country & Size Wise Lay Balance</h3>
         		<div id="content_search_panel" style="display:none">  
                    <table cellpadding="0" cellspacing="0" width="600" class="" rules="all" border="1" id="tbl_size_details">
                        <thead class="form_table_header">
                            <th>Country Type</th>
                            <th>Country Name</th>
                            <th>Country Ship. Date</th>
                            <th>Size</th>
                            <th>Lay Balance</th>
                            <th style="display:none">Size Ratio</th>
                            <th style="display:none">Size Qty.</th>
                        </thead>
                        <tbody>
                        <?
                        $i=1; $total_lay_balance=0; $total_marker_qty=0; $total_size_ratio=0;
						foreach($size_details as $countryseq=>$seqdata)
						{
                        foreach($seqdata as $country_type_id=>$country_val)
                        {
                            foreach($country_val as $country_id=>$size_data)
                            {
                                foreach($size_data as $size_id=>$plan_cut_qnty)
                                {
                                    $data=explode("**",$sizeDaraArr[$country_type_id][$country_id][$size_id]);
                                    $lay_balance=$plan_cut_qnty-$lay_bl_qty_arr[$country_type_id][$country_id][$size_id]+$data[1];  
                                    $total_lay_balance+=$lay_balance;
                                    $total_marker_qty+=$data[1];
                                    $total_size_ratio+=$data[0];
                                ?>
                                    <tr class="" id="gsd_<? echo $i; ?>">
                                        <td align="center">	
                                            <?
                                                echo create_drop_down( "cboCountryType_".$i, 100, $country_type,'', 0, '',$country_type_id,'',1); 
                                            ?> 
                                        </td> 
                                        <td align="center">	
                                            <?
                                                echo create_drop_down( "cboCountry_".$i, 100, $country_arr, '', 0, '',$country_id,'',1); 
                                            ?> 
                                        </td>
                                        <td align="center">				
                                            <input type="text" style="width:80px" class="datepicker" name="shipdate_<? echo $i; ?>" id="shipdate_<? echo $i; ?>" value="<? echo change_date_format($shipDate_arr[$country_type_id][$country_id]); ?>" disabled readonly />	
                                        </td> 
                                        <td align="center">	
                                              <input type="text" style="width:80px" class="text_boxes"  name="txt_size_<? echo $i; ?>" id="txt_size_<? echo $i; ?>" value="<? echo $size_arr[$size_id]; ?>" readonly />
                                              <input type="hidden" id="hidden_size_id_<? echo $i; ?>" name="hidden_size_id_<? echo $i; ?>" value="<? echo $size_id; ?>">
                                              <input type="hidden" id="update_size_id_<? echo $i; ?>" name="update_size_id_<? echo $i; ?>" value="<? echo $data[3]; ?>">
                                        </td>                 
                                        <td align="center">				
                                            <input type="text" style="width:80px" class="text_boxes_numeric"  name="txt_lay_balance_<? echo $i; ?>" id="txt_lay_balance_<? echo $i; ?>"  value="<? echo $lay_balance; ?>" readonly />
                                        </td> 
                                        <td align="center" style="display:none">				
                                            <input type="text" style="width:45px" class="text_boxes_numeric" onKeyUp="check_size_qty(<? echo $txt_piles; ?>,this.value,this.id)" name="txt_size_ratio_<? echo $i; ?>" id="txt_size_ratio_<? echo $i; ?>" value="<? echo $data[0]; ?>" disabled  />	
                                        </td>
                                        <td align="center" style="display:none">				
                                            <input type="text" style="width:50px" class="text_boxes_numeric"  name="txt_size_qty_<? echo $i; ?>" id="txt_size_qty_<? echo $i; ?>"  value="<? echo $data[1]; ?>" readonly />	
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
                        <tfoot>
                            <tr class="form_table_header">
                                <th colspan="4">Total</th>
                                <th align="right"><? echo $total_lay_balance; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
     	</form>  
        <form name="searchorderfrm_2"  id="searchorderfrm_2" autocomplete="off">    
            <br/>
            <div id="search_div" style="margin-top:10px">
                <?
                $i=1;
                $sql_size_name=sql_select("select size_id from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$details_id."");
                $size_colour_arr=array();
                foreach($sql_size_name as $asf)
                {
                    $size_colour_arr[$asf[csf("size_id")]]=$size_arr[$asf[csf("size_id")]];	
                }
				$bundle_data=sql_select("select a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value, a.country_type, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.barcode_no, a.is_excess from ppl_cut_lay_bundle a where a.mst_id=".$mst_id." and a.dtls_id=".$details_id." and status_active=1 and is_deleted=0 order by a.id ASC");
				
                if(count($bundle_data)>0)
                {
                ?>
                    <fieldset style="width:850px">
                        <legend>Bundle No and RMG Qty Details</legend>
                        <table cellpadding="0" cellspacing="0" width="830" rules="all" border="1" class="rpt_table" id="tbl_bundle_list_save">
                            <thead class="form_table_header">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Bundle</th>
                                <th></th>
                                <th colspan="2">RMG Number</th>
                                <th>
                                    <input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />  
                                    <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $details_id; ?>" />
                                </th>
                                <th>Report &nbsp;</th>
                            </thead>
                            <thead class="form_table_header">
                                <th>SL No</th>
                                <th>Country Type</th>
                                <th>Country Name</th>
                                <th>Size</th>
                                <th>Pattern</th>
                                <th>Roll No</th>
                                <th>Bundle No</th>
                                <th>Quantity</th>
                                <th>From</th>
                                <th>To</th>
                                <th></th>
                                <th width="40"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
                            </thead>
                            <tbody id="trBundleListSave">
                            <?
                            foreach($bundle_data as $row)
                            { 
                                $update_f_value=""; 
                                if(str_replace("'","",$row[csf('update_flag')])==1)
                                {
                                    $update_f_value=explode("**",$row[csf('update_value')]);
                                }
                            ?>
                                <tr id="trBundleListSave_<? echo $i;  ?>">
                                    <td align="center" id="">
                                        <input type="text" id="sirialNo_<? echo $i; ?>" name="sirialNo[]" style="width:25px;" class="text_boxes" value="<? echo $i; ?>" disabled/>
                                        <input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]"  value="<? echo $i;  ?>" />                   
                                        <input type="hidden" id="hiddenUpdateFlag_<? echo $i;  ?>" name="hiddenUpdateFlag[]"  value="<? echo $row[csf('update_flag')];?> " />
                                        <input type="hidden" id="hiddenUpdateValue_<? echo $i;  ?>" name="hiddenUpdateValue[]"  value="<? echo $row[csf('update_value')];?> " />
                                    </td>
                                    <td align="center">	
                                        <?
                                            echo create_drop_down( "cboCountryTypeB_".$i, 80, $country_type,'', 0, '',$row[csf('country_type')],'',1); 
                                        ?>
                                         <input type="hidden" id="hiddenCountryTypeB_<? echo $i;  ?>" name="hiddenCountryTypeB[]"  value="<? echo $row[csf('country_type')];?> "/> 
                                    </td> 
                                    <td align="center">	
                                        <?
                                            echo create_drop_down("cboCountryB_".$i,80,$po_country_array,'',1,'',$row[csf('country_id')],'',1,'','','','','','','cboCountryB[]'); 
                                        ?> 
                                        <input type="hidden" id="hiddenCountryB_<? echo $i; ?>" name="hiddenCountryB[]" value="<? echo $row[csf('country_id')];?> " />
                                    </td>
                                    <td align="center" id="update_sizename_<? echo $i;  ?>">
                                        <select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:60px; text-align:center; <? if($update_f_value[1]!="") echo "background-color:#F3F;"; ?> "  disabled>
                                        <?
                                        // $l=1;
                                        foreach($sql_size_name as $asf)
                                        {
                                            if($asf[csf("size_id")]==$row[csf('size_id')]) $select_text="selected"; else $select_text="";
                                            ?>	
                                                <option value="<? echo $asf[csf("size_id")]; ?>" <? echo $select_text;  ?>><? echo $size_arr[$asf[csf("size_id")]]; ?> </option>
                                            <?
                                        }
                                        ?>          
                                        </select>
                                    	<input type="hidden" name="hiddenSizeId[]" id="hiddenSizeId_<? echo $i;  ?>" value="<? echo $row[csf('size_id')];  ?>" />
                                    </td>
                                    <td align="center"><input type="text" name="patternNo[]" id="patternNo_<? echo $i; ?>" value="<? echo $row[csf('pattern_no')]; ?>" class="text_boxes" style="width:35px; text-align:center" disabled/><input type="hidden" name="isExcess[]" id="isExcess_<? echo $i; ?>" value="<? echo $row[csf('is_excess')]; ?>"/></td>
                                    <td align="center">
                                    	<input type="text" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>" class="text_boxes" style="width:50px;  text-align:center" disabled/>
                                    	<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                                    </td>
                                    <td align="center">
                                    	<input type="text" name="bundleNo[]" id="bundleNo_<? echo $i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes"  style="width:110px; text-align:center" title="<?php echo $row[csf('barcode_no')]; ?>" disabled/>
                                    </td>
                                    <td align="center">
                                    	<input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onKeyUp="bundle_calclution(<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>"  style="width:40px; text-align:right; <? if($update_f_value[0]!="") echo "background-color:#F3F;"; ?>"  class="text_boxes"  disabled/>
                                    	<input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<? echo $i;  ?>" value="<? echo $row[csf('size_qty')];  ?>" disabled/>
                                    </td>
                                    <td align="center">
                                    	<input type="text" name="rmgNoStart[]" id="rmgNoStart_<? echo $i;  ?>" value="<? echo $row[csf('number_start')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled />
                                    </td>
                                    <td align="center">
                                    	<input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<? echo $i;  ?>" value="<? echo $row[csf('number_end')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled/>
                                    </td>
                                    <td align="center">
                                        <input type="button" value="+" name="addButton[]" id="addButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<? echo $i;  ?>','<? echo $i;  ?>')"/>
                                        <input type="button" value="Adj." name="rowUpdate[]" id="rowUpdate_<? echo $i;  ?>" style="width:40px;" class="formbuttonplasminus" onClick="fnc_updateRow('<? echo $i;  ?>')"/>
                                    </td>
                                    <td align="center">
                                        <input id="chk_bundle_<? echo $i;  ?>" type="checkbox" name="chk_bundle" >
                                        <input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid_<? echo $i; ?>" value="<? echo $row[csf('id')];  ?>" style="width:15px;" class="text_boxes"/>
                                    </td>
                                </tr>
                            <?
                            	$i++;
                            }
                            ?>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all">
                            <tr>
                                <td colspan="12" align="center" class="button_container">
                                    <? echo load_submit_buttons( $permission, "fnc_cut_lay_bundle_info", 1,0,"clear_size_form()",1);?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                <?
                }
                ?>
            </div>  
        </form>
	</div>    
</body>           
<script src="../../../includes/functions_bottom_noselect.js" type="text/javascript"></script>
<script>
	$('#cboCountryBundle').val(0);
</script>
</html>
<?
exit();
}

if($action=="print_qrcode_operation")
{	
	//echo "1000".$data;die;
	$data=explode("***",$data);
	$detls_id=$data[3];
	$garments_item_name=$garments_item[$data[4]];
	$color_library=return_library_array( "select id, color_name from lib_color where id=$data[5]", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');


	$color_sizeID_arr=sql_select("select 
										a.id,
										a.size_id,
										a.bundle_no,
										a.barcode_no,
										a.order_id,
										a.number_start,
										a.number_end,
										a.size_qty,
										a.country_id,
										a.roll_no,
										b.bundle_sequence,
										b.color_id 
									from 
										ppl_cut_lay_bundle a, 
										ppl_cut_lay_size_dtls b 
									where 
										a.mst_id=b.mst_id and 
										a.dtls_id=b.dtls_id and 
										a.size_id=b.size_id and 
										a.id in ($data[0]) 
									order by 
										b.bundle_sequence,
										a.id");
	
	foreach($color_sizeID_arr as $val_qty)
	{
		$total_cut_qty+=$val_qty[csf('size_qty')];
		$total_size_qty_arr[$val_qty[csf('size_id')]]+=$val_qty[csf('size_qty')];
	}
	$bundle_array=array();
	$sql_name=sql_select("select 
							b.buyer_name,
							b.style_ref_no,
							b.product_dept,
							a.po_number,
							a.id
						from 
							wo_po_details_master b,
							wo_po_break_down a
						where 
							a.job_no_mst=b.job_no and 
							a.job_no_mst='".$data[1]."'");

	foreach($sql_name as $value)
	{
		$product_dept_name 						=$value[csf('product_dept')];
		$style_name 							=substr($value[csf('style_ref_no')],0,26);
		$buyer_name 							=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]] 		=$value[csf('po_number')];
	}
	$buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");
	//return_library_array( "select id,short_name from lib_buyer where id=$buyer_name ", "id", "short_name");
	$sql_cut_name=sql_select("select 
								entry_date,
								table_no,
								cut_num_prefix_no,
								batch_id,
								company_id,
								cutting_no 
							from 
								ppl_cut_lay_mst 
							where id=$data[2]");

	foreach($sql_cut_name as $cut_value)
	{
		$ful_cut_no 		=$cut_value[csf('cutting_no')];
		$table_id 			=$cut_value[csf('table_no')];
		$cut_date 			=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
		$company_id 		=$cut_value[csf('company_id')];
		$batch_no 			=$cut_value[csf('batch_id')];
		$comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no 		=$comp_name."-".$cut_prifix;
		$bundle_title 		="";
	}

	$table_no=return_field_value("table_no","lib_cutting_table", "id=$table_id ");
	//return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	?>
    

    <?
     $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
     $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';

     foreach (glob($PNG_WEB_DIR."*.png") as $filename) {			
			@unlink($filename);
		}

     if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
     
     $filename = $PNG_TEMP_DIR.'test.png';
     $errorCorrectionLevel = 'L';
     $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php"; 
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('',    // mode - default ''
					array(60,70),		// array(65,210),    // format - A4, for example, default ''
					 5,     // font size - default 0
					 '',    // default font family
					 3,    // margin_left
					 3,    // margin right
					 3,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');

	if($data[7]=="") $data[7]=0;
	$i 		=1;
	$html 	='';
	$total_number_of_bundle=count($color_sizeID_arr);
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($color_sizeID_arr as $val)
		{
			foreach($sql_bundle_copy as $inf)
			{
				$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
		    	QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
				$po_number=$po_number_arr[$val[csf('order_id')]];
				$country_name=$country_arr[$val[csf('country_id')]];
				$bundle_array[$i]=$val[csf("barcode_no")];
			
				$mpdf->AddPage('',    // mode - default ''
					array(60,70),		// array(65,210),    // format - A4, for example, default ''
					 5,     // font size - default 0
					 '',    // default font family
					 3,    // margin_left
					 3,    // margin right
					 3,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');

				$html.='<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">
					    	
					        	<tr >
									<td  width="40%"  >
										<table  width="100%" border="0">
											<tr>
												<td  width=""  >
												<div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="60" width=""></div></td>
											</tr>
										</table>
									</td>
									<td  width="60%"  >
										<table  width="100%">
											<tr>
												<td width="">'.$val[csf("barcode_no")].'</td>
											</tr>
											<tr>
												<td width="">'.$val[csf("bundle_no")].'</td>
											</tr>
											<tr>
												<td width="">'.$data[1].'</td>
											</tr>
										</table>
									</td>
								</tr>

					</table>
					<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">			
								
						<tr>
							<td>Cut Qty: ('.$total_cut_qty.')</td>
			            	<td width="">'.$inf[csf("bundle_use_for")].'</td>
			            </tr>
			            <tr>
			            	<td width="50%">Table No :'.$table_no.' </td>
			            	<td width="50%">Date :'.$cut_date.'</td>
			            </tr>
			            <tr>
			            	<td>'.$buyer_short_name.'</td>
			            	<td>O:'.$po_number.'</td>
			            </tr>

			            <tr>
			            	<td width="" colspan="2">Style :'.$style_name.' </td>
			            </tr>

			            <tr>
			            	<td width="" colspan="2">Country :'.$country_name.' </td>
			            </tr>

			            <tr>
			            	<td colspan="2">Item :'.$garments_item_name.'</td>
			            </tr>

			            <tr>
			            	<td colspan="2">Color:'.$color_library[$data[5]].'</td>
			            </tr>

			            <tr>
			            	<td>Size : '.$size_arr[$val[csf("size_id")]].'('.$total_size_qty_arr[$val[csf("size_id")]].')</td>
			            	<td>Batch:'.$batch_no.'</td>
			            	
			            </tr>

			            <tr>
			            	<td>Gmts. No:'.$val[csf("number_start")].'-'.$val[csf("number_end")].'</td>
			            	<td>Gmts. Qnty:'.$val[csf("size_qty")].'</td>
			            </tr>

			            <tr>
			            	<td></td>
			            	<td align="right">Page '.$i.'</td>
			            </tr>
									

				    </table>';


				$mpdf->WriteHTML($html);
				$html='';
				$i++;
			}
			
		} 
	}
	else
	{
		foreach($color_sizeID_arr as $val)
		{

			$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
	    	QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
			$country_name=$country_arr[$val[csf('country_id')]];
			$po_number=$po_number_arr[$val[csf('order_id')]];
			$bundle_array[$i]=$val[csf("barcode_no")];
		
			$mpdf->AddPage('',    // mode - default ''
				array(60,70),		// array(65,210),    // format - A4, for example, default ''
				 5,     // font size - default 0
				 '',    // default font family
				 3,    // margin_left
				 3,    // margin right
				 3,     // margin top
				 0,    // margin bottom
				 0,     // margin header
				 0,     // margin footer
				 'L');

			$html.='<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">
				    	
				        	<tr >
								<td  width="40%"  >
									<table  width="100%" border="0">
										<tr>
											<td  width=""  >
											<div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="60" width=""></div></td>
										</tr>
									</table>
								</td>
								<td  width="60%"  >
									<table  width="100%">
										<tr>
											<td width="">'.$val[csf("barcode_no")].'</td>
										</tr>
										<tr>
											<td width="">'.$val[csf("bundle_no")].'</td>
										</tr>
										<tr>
											<td width="">'.$data[1].'</td>
										</tr>
									</table>
								</td>
							</tr>

				</table>
				<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">			
							
					<tr>
						<td>Cut Qty: ('.$total_cut_qty.')</td>
		            	<td width="">'.$inf[csf("bundle_use_for")].'</td>
		            </tr>
		            <tr>
		            	<td width="50%">Table No :'.$table_no.' </td>
		            	<td width="50%">Date :'.$cut_date.'</td>
		            </tr>
		            <tr>
		            	<td>'.$buyer_short_name.'</td>
		            	<td>O:'.$po_number.'</td>
		            </tr>

		            <tr>
		            	<td width="" colspan="2">Style :'.$style_name.' </td>
		            </tr>

		            <tr>
		            	<td width="" colspan="2">Country :'.$country_name.' </td>
		            </tr>

		            <tr>
		            	<td colspan="2">Item :'.$garments_item_name.'</td>
		            </tr>

		            <tr>
		            	<td colspan="2">Color:'.$color_library[$data[5]].'</td>
		            </tr>

		            <tr>
		            	<td>Size : '.$size_arr[$val[csf("size_id")]].'('.$total_size_qty_arr[$val[csf("size_id")]].')</td>
		            	<td>Batch:'.$batch_no.'</td>
		            	
		            </tr>

		            <tr>
		            	<td>Gmts. No:'.$val[csf("number_start")].'-'.$val[csf("number_end")].'</td>
		            	<td>Gmts. Qnty:'.$val[csf("size_qty")].'</td>
		            </tr>

		            <tr>
		            	<td></td>
		            	<td align="right">Page '.$i.'</td>
		            </tr>

			    </table>';

			$mpdf->WriteHTML($html);
			$html='';
			$i++;
		}

	}
	//$mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'lotRatioEntry_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
	$mpdf->Output($name, 'F');	
	echo "1###$name";
	fn_delete_dir_with_files("qrcode_image/".$ful_cut_no);		
	exit();

}

if($action=="print_qrcode_operation2") // for qr code 2
{	
	//echo "1000".$data;die;
	$data=explode("***",$data);
	$detls_id=$data[3];
	$garments_item_name=$garments_item[$data[4]];
	$color_library=return_library_array( "select id, color_name from lib_color where id=$data[5]", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');


	$color_sizeID_arr=sql_select("SELECT a.id, a.size_id, a.bundle_no,a.pattern_no, a.barcode_no, a.order_id, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_no, b.bundle_sequence, b.color_id from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence, a.id");
	foreach($color_sizeID_arr as $val_qty)
	{
		$total_cut_qty+=$val_qty[csf('size_qty')];
		$total_size_qty_arr[$val_qty[csf('size_id')]]+=$val_qty[csf('size_qty')];
	}
	$bundle_array=array();
	$sql_name=sql_select("SELECT b.buyer_name, b.style_ref_no, b.product_dept, a.po_number, a.id from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no and a.job_no_mst='".$data[1]."'");
	foreach($sql_name as $value)
	{
		$product_dept_name 						=$value[csf('product_dept')];
		$style_name 							=substr($value[csf('style_ref_no')],0,26);
		$buyer_name 							=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]] 		=$value[csf('po_number')];
	}
	$buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");
	//return_library_array( "select id,short_name from lib_buyer where id=$buyer_name ", "id", "short_name");
	$sql_cut_name=sql_select("SELECT entry_date, table_no, cut_num_prefix_no, batch_id, company_id, cutting_no from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$ful_cut_no 		=$cut_value[csf('cutting_no')];
		$table_id 			=$cut_value[csf('table_no')];
		$cut_date 			=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
		$company_id 		=$cut_value[csf('company_id')];
		$batch_no 			=$cut_value[csf('batch_id')];
		$comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no 		=$comp_name."-".$cut_prifix;
		$bundle_title 		="";
	}

	$table_no=return_field_value("table_no","lib_cutting_table", "id=$table_id ");
	//return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	?>
    

    <?
     $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
     $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';

     foreach (glob($PNG_WEB_DIR."*.png") as $filename) {			
			@unlink($filename);
		}

     if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
     
     $filename = $PNG_TEMP_DIR.'test.png';
     $errorCorrectionLevel = 'L';
     $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php"; 
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('',    // mode - default ''
					array(60,70),		// array(65,210),    // format - A4, for example, default ''
					 5,     // font size - default 0
					 '',    // default font family
					 3,    // margin_left
					 3,    // margin right
					 3,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');

	if($data[7]=="") $data[7]=0;
	$i 		=1;
	$html 	='';
	$total_number_of_bundle=count($color_sizeID_arr);
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($color_sizeID_arr as $val)
		{
			foreach($sql_bundle_copy as $inf)
			{
				$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
		    	QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
				$po_number=$po_number_arr[$val[csf('order_id')]];
				$country_name=$country_arr[$val[csf('country_id')]];
				$bundle_array[$i]=$val[csf("barcode_no")];
			
				$mpdf->AddPage('',    // mode - default ''
					array(60,70),		// array(65,210),    // format - A4, for example, default ''
					 5,     // font size - default 0
					 '',    // default font family
					 3,    // margin_left
					 3,    // margin right
					 3,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');

				$html.='<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">
					    	
					        	<tr >
									<td  width="40%"  >
										<table  width="100%" border="0">
											<tr>
												<td  width=""  >
												<div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="60" width=""></div></td>
											</tr>
										</table>
									</td>
									<td  width="60%"  >
										<table  width="100%">
											<tr>
												<td width="">'.$val[csf("barcode_no")].'</td>
											</tr>
											<tr>
												<td width="">'.$val[csf("bundle_no")].'</td>
											</tr>
											<tr>
												<td width="">'.$data[1].'</td>
											</tr>
										</table>
									</td>
								</tr>

					</table>
					<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">			
								
						<tr>
							<td style="font-size:12px;">Cut Qty: ('.$total_cut_qty.')</td>
			            	<td style="font-size:12px;" width="">'.$inf[csf("bundle_use_for")].'('.$val[csf("pattern_no")].')</td>
			            </tr>
			            <tr>
			            	<td style="font-size:12px;" width="50%">R.No :'.$val[csf("roll_no")].' </td>
			            	<td style="font-size:12px;" width="50%">Date :'.$cut_date.'</td>
			            </tr>
			            <tr>
			            	<td style="font-size:12px;">'.$buyer_short_name.'</td>
			            	<td style="font-size:12px;">O:'.$po_number.'</td>
			            </tr>

			            <tr>
			            	<td style="font-size:12px;" width="" colspan="2">Style :'.$style_name.' </td>
			            </tr>

			            <tr>
			            	<td style="font-size:12px;" width="" colspan="2">Country :'.$country_name.' </td>
			            </tr>

			            <tr>
			            	<td style="font-size:12px;" colspan="2">Item :'.$garments_item_name.'</td>
			            </tr>

			            <tr>
			            	<td style="font-size:12px;" colspan="2">Color:'.substr($color_library[$data[5]],0,30).'</td>
			            </tr>

			            <tr>
			            	<td style="font-size:12px;">Size : '.$size_arr[$val[csf("size_id")]].'('.$total_size_qty_arr[$val[csf("size_id")]].')</td>
			            	<td style="font-size:12px;">B#:'.$batch_no.'</td>
			            	
			            </tr>

			            <tr>
			            	<td style="font-size:12px;">G.No:'.$val[csf("number_start")].'-'.$val[csf("number_end")].'</td>
			            	<td style="font-size:12px;">G.Qnty:'.$val[csf("size_qty")].'</td>
			            </tr>

			            <tr>
			            	<td></td>
			            	<td align="right">Page '.$i.'</td>
			            </tr>
									

				    </table>';


				$mpdf->WriteHTML($html);
				$html='';
				$i++;
			}
			
		} 
	}
	else
	{
		foreach($color_sizeID_arr as $val)
		{

			$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
	    	QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
			$country_name=$country_arr[$val[csf('country_id')]];
			$po_number=$po_number_arr[$val[csf('order_id')]];
			$bundle_array[$i]=$val[csf("barcode_no")];
		
			$mpdf->AddPage('',    // mode - default ''
				array(60,70),		// array(65,210),    // format - A4, for example, default ''
				 5,     // font size - default 0
				 '',    // default font family
				 3,    // margin_left
				 3,    // margin right
				 3,     // margin top
				 0,    // margin bottom
				 0,     // margin header
				 0,     // margin footer
				 'L');

			$html.='<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">
				    	
				        	<tr >
								<td  width="40%"  >
									<table  width="100%" border="0">
										<tr>
											<td  width=""  >
											<div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="60" width=""></div></td>
										</tr>
									</table>
								</td>
								<td  width="60%"  >
									<table  width="100%">
										<tr>
											<td width="">'.$val[csf("barcode_no")].'</td>
										</tr>
										<tr>
											<td width="">'.$val[csf("bundle_no")].'</td>
										</tr>
										<tr>
											<td width="">'.$data[1].'</td>
										</tr>
									</table>
								</td>
							</tr>

				</table>
				<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">			
							
					<tr>
						<td style="font-size:12px;>Cut Qty: ('.$total_cut_qty.')</td>
		            	<td style="font-size:12px; width="">'.$inf[csf("bundle_use_for")].'('.$val[csf("pattern_no")].')</td>
		            </tr>
		            <tr>
		            	<td style="font-size:12px; width="50%">R.No :'.$val[csf("roll_no")].' </td>
		            	<td style="font-size:12px; width="50%">Date :'.$cut_date.'</td>
		            </tr>
		            <tr>
		            	<td style="font-size:12px;>'.$buyer_short_name.'</td>
		            	<td style="font-size:12px;>O:'.$po_number.'</td>
		            </tr>

		            <tr>
		            	<td style="font-size:12px; width="" colspan="2">Style :'.$style_name.' </td>
		            </tr>

		            <tr>
		            	<td style="font-size:12px; width="" colspan="2">Country :'.$country_name.' </td>
		            </tr>

		            <tr>
		            	<td style="font-size:12px; colspan="2">Item :'.$garments_item_name.'</td>
		            </tr>

		            <tr>
		            	<td style="font-size:12px; colspan="2">Color:'.substr($color_library[$data[5]],0,30).'</td>
		            </tr>

		            <tr>
		            	<td style="font-size:12px;>Size : '.$size_arr[$val[csf("size_id")]].'('.$total_size_qty_arr[$val[csf("size_id")]].')</td>
		            	<td style="font-size:12px;>B#:'.$batch_no.'</td>
		            	
		            </tr>

		            <tr>
		            	<td style="font-size:12px;>G.No:'.$val[csf("number_start")].'-'.$val[csf("number_end")].'</td>
		            	<td style="font-size:12px;>G.Qnty:'.$val[csf("size_qty")].'</td>
		            </tr>

		            <tr>
		            	<td></td>
		            	<td align="right">Page '.$i.'</td>
		            </tr>

			    </table>';

			$mpdf->WriteHTML($html);
			$html='';
			$i++;
		}

	}
	//$mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'lotRatioEntry_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
	$mpdf->Output($name, 'F');	
	echo "1###$name";
	fn_delete_dir_with_files("qrcode_image/".$ful_cut_no);			
	exit();

}

if($action=="rollSize_popup")
{
	echo load_html_head_contents("Roll Size Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$sizeDataArray=array();
	$size_datas=explode("|",$size_data);
	foreach($size_datas as $data)
	{
		$datas=explode("_",$data);
		$sizeDataArray[$datas[0]]=$datas[1];
	}
?> 
	<script>
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
    </script>

</head>

<body>
<div align="center" style="width:650px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:640px; margin-left:2px">
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="620">
                <thead>
                    <th width="60">Roll No</th>
                    <th width="70">Roll Wgt.</th>
                    <th width="60">Plies</th>
                    <?
					foreach($sizeDataArray as $key=>$value)
					{
						echo '<th>'.$size_arr[$key].'</th>';
					} 
					?>
                </thead>
               <? 
                   	$rollDatas=explode("**",$rollData); $allData='';
					foreach($rollDatas as $data)
					{
						$datas=explode("=",$data);
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$allData.=$data;
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>"> 
                            <td>
                            	<input type="text" id="rollNo_<? echo $i; ?>" name="rollNo[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[1]; ?>">
                            	<input type="hidden" id="rollId_<? echo $i; ?>" name="rollId[]" value="<? echo $datas[2]; ?>">
                            </td>
                            <td><input type="text" id="rollWgt_<? echo $i; ?>" name="rollWgt[]" style="width:60px" class="text_boxes_numeric" value="<? echo $datas[3]; ?>"></td>
                            <td><input type="text" id="piles_<? echo $i; ?>" name="piles[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[4]; ?>"></td>
                            <?
							foreach($sizeDataArray as $key=>$value)
							{
								$allData.="=".$value*$datas[4];
							?>
								<td align="center"><input type="text" id="sqty_<? echo $key."_".$i; ?>" name="sqty[]" style="width:50px" class="text_boxes_numeric" value="<? echo $value*$datas[4]; ?>"></td>
                            <?
							} 
							$allData.="|";
							?>
                        </tr>
						<? 
						$i++;
                    } 
                    ?>
            </table>
            <table width="620">
                <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        <input type="hidden" name="hidden_roll_data" id="hidden_roll_data" value="<? echo chop($allData,'|'); ?>"/>
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="save_update_delete_bundle")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==1)  // Insert Here=======================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","cutting_no='".$hidden_cutting_no."'");
		//echo $cutting_qc_no;die;
		if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; disconnect($con); die;}
		
		$previous_barcode_data=sql_select("select bundle_no,barcode_no,barcode_year,barcode_prifix from ppl_cut_lay_bundle where mst_id=".$bundle_mst_id."  and  dtls_id=".$bundle_dtls_id." and status_active=1 and is_deleted=0 ");
		foreach($previous_barcode_data as $b_val)
		{
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['year']=$b_val[csf("barcode_year")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['prifix']=$b_val[csf("barcode_prifix")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['barcode']=$b_val[csf("barcode_no")];
		}
		
		
		$id=return_next_id("id","ppl_cut_lay_bundle",1);

		$field_array="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,update_flag,update_value,country_type,country_id,roll_id,roll_no,pattern_no,order_id,is_excess,color_type_id,inserted_by,insert_date,status_active,is_deleted";
		
		$year_id=date('Y',time());
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$new_bundle_no="txtBundleNo_".$j;
			$new_bundle_qty="txtBundleQty_".$j;
			$hidden_bundle_qty="hiddenSizeqty_".$j;
			$new_bundle_from="txtBundleFrom_".$j;
			$new_bundle_to="txtBundleTo_".$j;
			$new_bundle_size_id="txtSizeId_".$j;
			$new_update_flag="hiddenUpdateFlag_".$j;
			$hidden_size_id="txtHiddenSizeId_".$j;
			$new_update_value="hiddenUpdateValue_".$j;
			$hiddenCountry="cboCountry_".$j;
			$hiddenCountryType="hiddenCountryType_".$j;
			$rollId="rollId_".$j;
			$rollNo="rollNo_".$j;
			$patternNo="patternNo_".$j;
			$isExcess="isExcess_".$j;
			$bundle_prif=explode("-",$$new_bundle_no);
			$new_bundle_prif_no=explode('-',$bundle_prif[3]);
			$new_bundle_prifix=$bundle_prif[0]."-".$bundle_prif[1]."-".$bundle_prif[2];
			$update_flag=0;
			$update_flag_value="";
			//echo $$new_update_flag."**".$$new_update_value;die;
			if(str_replace("'","",$$new_update_flag)!=1)
			{
				if(str_replace("'","",$$new_update_flag)==6)
				{
					if(trim($$hidden_bundle_qty)!=trim($$new_bundle_qty))	
					{
					   $update_flag_value="".str_replace("'","",$$hidden_bundle_qty)."";
					   $update_flag=1;
					}
					else
					{
						$update_flag_value="";
					}
					if(trim($$hidden_size_id)!=trim($$new_bundle_size_id))	
					{
					   $update_flag_value.="**".str_replace("'","",$$new_bundle_size_id)."";
					   $update_flag=1;
					}
					else
					{
						$update_flag_value.="**";
					}
				}
			}
			else
			{
				$update_flag=1;
				$update_flag_value=$$new_update_value;
			}
				
			
			if(empty($previous_barcode_arr[str_replace("'","",$$new_bundle_no)]))
			{
				$barcode_suffix_no=$barcode_suffix_no+1;
				$up_barcode_suffix=$barcode_suffix_no;
				$up_barcode_year=$year_id;
				$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
			}
			else
			{
				$up_barcode_suffix=$previous_barcode_arr[str_replace("'","",$$new_bundle_no)]['prifix'];
				$up_barcode_year=$previous_barcode_arr[str_replace("'","",$$new_bundle_no)]['year'];
				$barcode_no=$previous_barcode_arr[str_replace("'","",$$new_bundle_no)]['barcode'];
			}
				
				
				//echo $update_flag_value."***";die;
			if($data_array!="") $data_array.=",";
			 $data_array.="(".$id.",".$bundle_mst_id.",".$bundle_dtls_id.",".$$new_bundle_size_id.",'".$new_bundle_prifix."','".$new_bundle_prif_no[0]."','".$$new_bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."','".$$new_bundle_from."','".$$new_bundle_to."','".$$new_bundle_qty."',".$update_flag.",'".$update_flag_value."','".str_replace("'","",$$hiddenCountryType)."','".str_replace("'","",$$hiddenCountry)."','".str_replace("'","",$$rollId)."','".str_replace("'","",$$rollNo)."','".str_replace("'","",$$patternNo)."',".$order_id.",'".str_replace("'","",$$isExcess)."',".$color_type_id.",'".$user_id."','".$pc_date_time."',1,0)";
			$id = $id+1;
		}
		//echo $data_array;die;	
		//echo "10**insert into ppl_cut_lay_bundle($field_array) values".$data_array;die;
		$rID=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$bundle_mst_id." and dtls_id=".$bundle_dtls_id."",0);
		$rID1=sql_insert("ppl_cut_lay_bundle",$field_array,$data_array,1);
		//echo "10**".$rID.$rID1;die;
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".$bundle_mst_id."**".$bundle_dtls_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$bundle_mst_id."**".$bundle_dtls_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
			{
			    if($rID && $rID1)
					{
						oci_commit($con);   
						echo "0**".$bundle_mst_id."**".$bundle_dtls_id;
					}
				else{
						oci_rollback($con);
						echo "10**".$bundle_mst_id."**".$bundle_dtls_id;
					}
			}
			
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here=======================================================================
	{
		
	}		
}
//----------------------------------bundle qty update finish---------------------------------------------------------------------------------


if($action=="report_bundle_printer")
{
	$data=explode("***",$data);
	$con = connect();
	if($db_type==0)	{ mysql_query("BEGIN"); }
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle
	                              where mst_id=$data[1] and dtls_id=$data[2] order by id" );  //where id in ($data)
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //where id in ($data)
	
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a 
	                      where a.job_no_mst=b.job_no and a.id=$data[5]");
    foreach($sql_name as $value)
	 {
		 $product_dept_name=$value[csf('product_dept')];
		 $style_name=$value[csf('style_ref_no')];
		 $buyer_name=$value[csf('buyer_name')];
		 $po_number=$value[csf('po_number')];
	 }
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[1]");
	
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$table_no_library[$cut_value[csf('table_no')]];
	     $cut_date=$cut_value[csf('entry_date')];
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";
		 
	 }
	 
	 $bundle_calculate_id=return_next_id("id", "ppl_cut_lay_bundle_history",1);
	 $field_array_bundle="I1,I2,I3,I4,I5,I6,I7,I8,I9,I10";
	 $field_array="id,order_id,mst_id,detls_id,total_bundle,inserted_by,insert_date";
	 $data_array_print="";
	 $i=1;
	 foreach($color_sizeID_arr as $val)
	      {
			 $field1=$val[csf("bundle_no")];
			 $field2=$new_cut_no.",".$cut_date;
			 $field3=$buyer_library[$buyer_name].",".$po_number;
			 $field4=$style_name;
			 $field5=$garments_item[$data[3]];
			 $field6=$color_library[$data[4]];
			 $field7=$size_arr[$val[csf("size_id")]].",".$val[csf("bundle_no")];
			 $field8=$val[csf("size_qty")].",".$val[csf("number_start")]."-".$val[csf("number_end")];
			 $field9=$batch_no;
			 if(trim($data_array_print)!="") $data_array_print.=",";
			 $data_array_print.="('".$field1."','".$field2."','".$field3."','".$field4."','".$field5."','".$field6."','".$field7."','".$field8."','".$field9."','".$table_name."')";
			 $i++;
		 }
		 $total_bundle=$i-1;
		 $data_array="(".$bundle_calculate_id.",'".$data[5]."','".$data[1]."','".$data[2]."','".$total_bundle."','".$user_id."','".$pc_date_time."')";
		// echo $data_array;die;
		 $rID=sql_insert("ppl_cut_lay_bundle_history",$field_array,$data_array,1); 
		 $rID1=sql_insert("LABEL_OUT",$field_array_bundle,$data_array_print,1); 
		 if($db_type==0)
			 {
				if($rID && $rID1)
				   {
					mysql_query("COMMIT");  
					echo 0;
					}
				else
				   {
					mysql_query("ROLLBACK"); 
					echo 10;
				   }
			 }
			if($db_type==2 || $db_type==1 )
			  {
				if($rID && $rID1)
				   {
					oci_commit($con); 
					echo 0;
					}
				else
				   {
					oci_rollback($con);
					echo 10;
				   }
			  }
			disconnect($con);
			die;
	 
}
//bundle_bar_code stiker****************************************************************************************************************************************************

if($action=="report_bundle_text_file")
{
	$data=explode("***",$data);
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //where id in ($data)
    $bundle_array=array();
	 $sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	 foreach($sql_name as $value)
	 {
		 $product_dept_name=$value[csf('product_dept')];
		 $style_name=$value[csf('style_ref_no')];
		 $buyer_name=$value[csf('buyer_name')];
		 $po_number=$value[csf('po_number')];
	 }
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";

	 }
			foreach (glob(""."*.zip") as $filename)
			{			
			@unlink($filename);
		    }
		    $i=1;
			$zip = new ZipArchive();			// Load zip library	
			$filename = str_replace(".sql",".zip",'norsel_bundle.sql');			// Zip name
			if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE)
			{		// Opening zip file to load files
				$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
			}
			 $batch_number="";
			 if ($batch_no!="") $batch_number="(".$batch_no.")";
			foreach($color_sizeID_arr as $val)
			   {
						$file_name="NORSEL-IMPORT_".$i;
						$myfile = fopen($file_name.".txt", "w") or die("Unable to open file!");
						$txt ="Norsel_imp\r\n1\r\n";
						$txt .=$val[csf("bundle_no")]."\r\n";
						$txt .="Bundle: ".$val[csf("bundle_no")]."".$batch_number."\r\n";
						$txt .= "Cut No ".$new_cut_no.", ".$cut_date."\r\n";
						$txt .= $buyer_library[$buyer_name].", Ord: ". $po_number."\r\n";
						$txt .="Style ". $style_name."\r\n";
						$txt .=$garments_item[$data[4]]."\r\n";
						$txt .="Color ".trim($color_library[$data[5]])."\r\n";
						$txt .="Size ". $size_arr[$val[csf("size_id")]].", Table ".$table_no_library[$table_name]."\r\n";
						$txt .= "Gmts Qty. ".$val[csf("size_qty")];
						$txt .= ", SL No ".$val[csf("number_start")]."-".$val[csf("number_end")];
						
					
						fwrite($myfile, $txt);
						fclose($myfile);
					$i++;
				 }
				 foreach (glob(""."*.txt") as $filenames){			
				   $zip->addFile($file_folder.$filenames);			// Adding files into zip
				}
			$zip->close();
	     
	foreach (glob(""."*.txt") as $filename) {			
			@unlink($filename);
		}
	echo "norsel_bundle";
	exit();
}

if($action=="print_report_bundle_barcode_eight")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	$data_all=$data;
	$data=explode("***",$data);
	?>
      <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
		
	} 
	</script>
    <?
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";
		 

	 }
    $sql_bundle_copy="select id,bundle_use_for from ppl_bundle_title where company_id=$company_id";
	echo  create_list_view("tbl_list_search", "Bundle Use For", "240","240","180",0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);
    echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
exit();	
	
}

if($action=="print_barcode_one_akh")
{	
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no
	
	$data=explode("***",$data);
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name'); 
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//echo $order_cut_no;
	$pdf=new PDF_Code39('P','mm','a11');
	//$pdf=new PDF_Code39(); 
	//$pdf->SetFont('Arial', '', 1000);
	$pdf->AddPage();
	
	
	/*$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}*/
	
	$color_sizeID_arr=sql_select("SELECT a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence, a.pattern_no, a.is_excess,a.barcode_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) and a.size_qty>0 order by b.bundle_sequence,a.id");
	
	$i=2; $j=2; $k=0; $bundle_array=array(); $br=0; $n=0;
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach($sql_name as $value)
	{
		$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$batch_no=$cut_value[csf('batch_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	if($data[7]=="") $data[7]=0;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=2; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				if($br==1) 
				{
					$pdf->AddPage(); $br=0; $i=2; $j=2; $k=0;
				}
				
				//BNDL
				
				$bundle_no_arr=explode("-",$val[csf("bundle_no")]);
				$pdf->Code40($i, $j, $val[csf("barcode_no")],$w =1, $h = 8);
				$pdf->Code40($i+32, $j+7,"Bu.No: ". $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+11,"Size: ". $size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i+32, $j+11,"Cut. Date: ". $cut_date, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+15,"Buyer: ". $buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i+32, $j+15,"RMG Qty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+19,"Style: ". $style_name, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i+32, $j+19,"Parts: ". $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+23,"PO: ". $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i+32, $j+23,"Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+27,"Color: ". $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				// $pdf->Code40($i+32, $j+27,"Size: ". $size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+31,"Country: ". $country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i+32, $j+31,"Batch No: ". $batch_no, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+35,"Pattern: ". $val[csf("pattern_no")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i+32, $j+35,"Gmts No: (". $val[csf("number_start")]."-".$val[csf("number_end")].")", $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$k++;
				$i=2; $j=$j+35;
				$br++;
				//a.number_start,a.number_end
				//bacth_array[$batch_id]
			}
		}   
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1) 
			{
				$pdf->AddPage(); $br=0; $i=2; $j=2; $k=0;
			}
			
			$bundle_no_arr=explode("-",$val[csf("bundle_no")]);
		
			
			$pdf->Code40($i, $j, $val[csf("bundle_no")],$w =0.2, $h = 8);
			$pdf->Code40($i+50, $j-2.5,"Cutting Date", $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i+50, $j+1,$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+11,"Cutting No: ". $new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i+40, $j+11,"Size: ". $size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+15,"Buyer: ". $buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i+40, $j+15,"RMG Qty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+19,"Style Ref: ". $style_name, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i+40, $j+19,"Parts: ". $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+23,"P O No: ". $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i+40, $j+23,"Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+27,"Color: ". $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+31,"Country: ". $country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i+32, $j+31,"Batch No: ". $batch_no, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+35,"Pattern: ". $val[csf("pattern_no")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i+32, $j+35,"Gmts No: (". $val[csf("number_start")]."-".$val[csf("number_end")].")", $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$k++;
			$i=2; $j=$j+35;
			$br++;
		} 
	}
	
	foreach (glob(""."*.pdf") as $filename) {			
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}


	
if($action=="print_barcode_eight")
{	
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	
	$data=explode("***",$data);
	$detls_id=$data[3];
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	// $sub_process_id=return_field_value2("group_concat(b.sub_process_id)  as sub_process_id ","pro_recipe_entry_dtls b, pro_recipe_entry_mst a",
	$order_cut_no=return_field_value(" order_cut_no ","ppl_cut_lay_dtls"," id=$detls_id and status_active=1 and is_deleted=0 ","order_cut_no");
	//echo $order_cut_no;
	$pdf=new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();
	
	$color_sizeID_arr=sql_select("select a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence, a.pattern_no 
	from ppl_cut_lay_bundle a,ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) and a.size_qty>0 order by b.bundle_sequence,a.id");
	
	$i=10; $j=12; $k=0;
    $bundle_array=array();
	$br=0;
	$n=0;
	 $sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	 foreach($sql_name as $value)
	 {
		 $product_dept_name=$value[csf('product_dept')];
		 $style_name=$value[csf('style_ref_no')];
		 $buyer_name=$value[csf('buyer_name')];
		 $po_number=$value[csf('po_number')];
	 }
	 
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";
	 }
	 	 if($data[7]=="") $data[7]=0;
    	 $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
		 $cope_page=1;
		 if(count($sql_bundle_copy)!=0)
		 {
			 foreach($color_sizeID_arr as $val)
			 {
				if($br==8) { $pdf->AddPage(); $br=0; $i=10; $j=12; $k=0; }
				foreach($sql_bundle_copy as $inf)
				   {
						if($br==8) 
						{
							 $pdf->AddPage(); $br=0; $i=10; $j=12; $k=0;
						}
						
						if( $k>0 && $k<2 ) { $i=$i+105; }
							$pdf->Code39($i, $j, $val[csf("bundle_no")]);
							$pdf->Code39($i+45, $j-4, "Bundle Card", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
							$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
							$pdf->Code39($i+45, $j+6, "Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
							$pdf->Code39($i, $j+6, "Cut Sys No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i+38, $j+6, "Cut Date	 : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i, $j+11,  "Buyer : ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i+38, $j+11,"Ord:".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i, $j+16,  "Style Ref  :  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i+38, $j+21, "Item :  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							//$pdf->Code39($i+40, $j+21, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i, $j+21, "Size :  ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i, $j+26, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i+38, $j+26, "Color	:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						    $pdf->Code39($i+38, $j+31, "Bundle No:".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i, $j+31, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							//$pdf->Code39($i+38, $j+36, "Dept : ".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i+38, $j+36, "Gmts. No :  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i, $j+36, "Gmts. Qnty : ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i, $j+42, "Order Cut No: ".$order_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i+38, $j+42, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$k++;
						
						if($k==2)
						{  $k=0; $i=10; $j=$j+67; }
						$br++;
						$cope_page++;
					 }
					// $br=8;
			   
			   }   
	    }
		else
		{
		   foreach($color_sizeID_arr as $val)
			   {
					if($br==8) { $pdf->AddPage(); $br=0; $i=10; $j=12; $k=0; }
					if( $k>0 && $k<2 ) { $i=$i+105; }
						$pdf->Code39($i, $j, $val[csf("bundle_no")]);
						$pdf->Code39($i+45, $j-4, "Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
						$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+6, "Cut Date	 : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+11,  "Buyer : ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+11,"Ord:".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+16,  "Style Ref  :  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+21, "Item :  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						//$pdf->Code39($i+40, $j+21, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+21, "Size :  ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+26, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+26, "Color	:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
					    $pdf->Code39($i+38, $j+31, "Bundle No:".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;

						$pdf->Code39($i, $j+31, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
		
						//$pdf->Code39($i+38, $j+36, "Dept : ".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+36, "Gmts. No :  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+36, "Gmts. Qnty : ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						
					$k++;
					
					if($k==2)
					{  $k=0; $i=10; $j=$j+67; }
					$br++;
				
				} 
		}
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_one_urmi")
{	
	
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no
	
	$data=explode("***",$data);
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name'); 
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//echo $order_cut_no;
	$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=new PDF_Code39(); 
	//$pdf->SetFont('Arial', '', 1000);
	$pdf->AddPage();
	
	
	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}
	
	$color_sizeID_arr=sql_select("SELECT a.id,a.size_id,a.bundle_no,a.barcode_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence, a.pattern_no,
	 a.is_excess,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) and a.size_qty>0 order by b.bundle_sequence,a.id");
	
	$i=2; $j=0; $k=0; $bundle_array=array(); $br=0; $n=0;
	$sql_name=sql_select("SELECT b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach($sql_name as $value)
	{
		$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("SELECT entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$batch_no=$cut_value[csf('batch_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	if($data[7]=="") $data[7]=0;
	$sql_bundle_copy=sql_select("SELECT id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=0; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				if($br==1) 
				{
					$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
				}
				
				//BNDL
				
				$bundle_no_arr=explode("-",$val[csf("bundle_no")]);
				
				// if($val[csf('is_excess')]==1) $country="EXCESS"; else 
				$country=$country_arr[$val[csf("country_id")]];
				
				$pdf->Code40($i, $j-2, $buyer_library[$buyer_name]."  CNT# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+1.3, "S#".$val[csf("number_start")]."-".$val[csf("number_end")]." STY#". $style_name, $ext = true, $cks = false, $w =0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+4.6, "BNDL#".$val[csf("bundle_num_prefix_no")]." PO#".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+7.9, "COLOR#".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+11.1, "PART# ".$inf[csf("bundle_use_for")]."  Batch# ".$bacth_array[$batch_id], $ext = true, $cks = false, $w = 0.1, $h = 1, $wide=true,true,8);
				$pdf->Code40($i, $j+14.3, "C&R# ".$new_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.1, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+17.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code39($i, $j+23, $val[csf("barcode_no")]);
				
				$k++;
				$i=2; $j=$j+21;
				/*if($k==2)
				{
					$k=0; $i=10; $j=$j+75;
				}*/
				
				$br++;
			}
		}   
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1) 
			{
				$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
			}
			
			$bundle_no_arr=explode("-",$val[csf("bundle_no")]);
			if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];
			
			$pdf->Code40($i, $j-2, $buyer_library[$buyer_name]."  CNT# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+1.3, "S#".$val[csf("number_start")]."-".$val[csf("number_end")]." STY#". $style_name, $ext = true, $cks = false, $w =0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+4.6, "BNDL#".$val[csf("bundle_num_prefix_no")]." PO#".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+7.9, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+11.1, "PART# ".$inf[csf("bundle_use_for")]."  Batch# ".$bacth_array[$batch_id], $ext = true, $cks = false, $w = 0.1, $h = 1, $wide=true,true,8);
			$pdf->Code40($i, $j+14.3, "C&R# ".$new_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.1, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+17.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code39($i, $j+23, $val[csf("barcode_no")]);
			$k++;
			$i=2; $j=$j+23;
			/*if($k==2)
			{
				$k=0; $i=10; $j=$j+75;
			}*/
			
			$br++;
		} 
	}
	
	foreach (glob(""."*.pdf") as $filename) {			
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();

}

if($action=="print_barcode_one_hams")
{	
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no
	
	$data=explode("***",$data);
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name'); 
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//echo $order_cut_no;
	$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=new PDF_Code39(); 
	//$pdf->SetFont('Arial', '', 1000);
	$pdf->AddPage();
	
	
/*	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}*/
	
	$color_sizeID_arr=sql_select("select a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence, a.pattern_no, a.is_excess
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) and a.size_qty>0 order by b.bundle_sequence,a.id");
	
	$i=2; $j=2; $k=0; $bundle_array=array(); $br=0; $n=0;
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach($sql_name as $value)
	{
		$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$batch_no=$cut_value[csf('batch_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	if($data[7]=="") $data[7]=0;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=2; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				if($br==1) 
				{
					$pdf->AddPage(); $br=0; $i=2; $j=2; $k=0;
				}
				
				//BNDL
				
				$bundle_no_arr=explode("-",$val[csf("bundle_no")]);
				
				if($val[csf('is_excess')]==1) $country="EXCESS"; else 
				// $country=$country_arr[$val[csf("country_id")]];
				if( !empty($order_cut_no)) $order_cut_number="/".$order_cut_no;
				else $order_cut_number='';
				
				$pdf->Code40($i, $j-2, $buyer_library[$buyer_name]."  CNT# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+1.4, "STY# ". $style_name."  S#".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w =0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+4.8,"PO# ".$po_number."  BNDL# ".$bundle_no_arr[2], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+8.2, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+11.6, "PART# ".$inf[csf("bundle_use_for")]."  Batch# ".$batch_no, $ext = true, $cks = false, $w = 0.1, $h = 1, $wide=true,true,8);
				$pdf->Code40($i, $j+15, "C&R# ".$new_cut_no."".$order_cut_number." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.1, $h = 1, $wide = true, true,8) ;
				$pdf->Code39($i, $j+22, $val[csf("bundle_no")]);
				
				$k++;
				$i=2; $j=$j+21;
				/*if($k==2)
				{
					$k=0; $i=10; $j=$j+75;
				}*/
				
				$br++;
			}
		}   
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1) 
			{
				$pdf->AddPage(); $br=0; $i=2; $j=2; $k=0;
			}
			
			$bundle_no_arr=explode("-",$val[csf("bundle_no")]);
			if( !empty($order_cut_no)) $order_cut_number="/".$order_cut_no;
				else $order_cut_number='';
			//if($val[csf('is_excess')]==1) $country="EXCESS"; else 
			$country=$country_arr[$val[csf("country_id")]];
			
			$pdf->Code40($i, $j-2, $buyer_library[$buyer_name]."  CNT# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+1.4, "STY# ". $style_name."  S#".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w =0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+4.8,"PO# ".$po_number."  BNDL# ".$bundle_no_arr[2], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+8.2, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+11.6, "PART# ".$inf[csf("bundle_use_for")]."  Batch# ".$batch_no, $ext = true, $cks = false, $w = 0.1, $h = 1, $wide=true,true,8);
			$pdf->Code40($i, $j+15, "C&R# ".$new_cut_no."".$order_cut_number." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.1, $h = 1, $wide = true, true,8) ;
			$pdf->Code39($i, $j+22, $val[csf("bundle_no")]);
			$k++;
			$i=2; $j=$j+20;
			$br++;
		} 
	}
	
	foreach (glob(""."*.pdf") as $filename) {			
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_one_pdf")
{	
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	
	$data=explode("***",$data);
	$detls_id=$data[3];
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//echo $order_cut_no;
	$pdf=new PDF_Code39('P','mm','a6');
	//$pdf=new PDF_Code39(); 
	//$pdf->SetFont('Arial', '', 1000);
	$pdf->AddPage();
	
	
	$color_sizeID_arr=sql_select("select a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence 
	from ppl_cut_lay_bundle  a,ppl_cut_lay_size_dtls b
	where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");
	
	$i=8; $j=8; $k=0; $bundle_array=array(); $br=0; $n=0;
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach($sql_name as $value)
	{
		$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];
	}
	 
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$batch_no=$cut_value[csf('batch_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	if($data[7]=="") $data[7]=0;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($color_sizeID_arr as $val)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=8; $j=8; $k=0; }
			foreach($sql_bundle_copy as $inf)
			{
				if($br==1) 
				{
					$pdf->AddPage(); $br=0; $i=8; $j=8; $k=0;
				}
				
				//if( $k>0 && $k<2 ) { $i=$i+105; }
				$pdf->Code39($i, $j, $val[csf("bundle_no")]);
				$pdf->Code39($i+45, $j-4, "Bundle Card", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
				$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
				$pdf->Code39($i+45, $j+6, "Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
				$pdf->Code39($i, $j+6, "Cut Sys No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+6, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+11,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+11,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+16,  "Style Ref: ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+21, "Item: ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+21, "Size: ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+26, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+26, "Color: ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+31, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+31, "Gmts. No: ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+36, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+36, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				
				$k++;
				$i=8; $j=$j+60;
				/*if($k==2)
				{
					$k=0; $i=10; $j=$j+75;
				}*/
				
				$br++;
			}
		}   
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1) { $pdf->AddPage(); $br=0; $i=8; $j=8; $k=0;}
			
			$pdf->Code39($i, $j, $val[csf("bundle_no")]);
			$pdf->Code39($i+45, $j-4, "Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
			$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
			$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+6, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+11,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+11,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+16,  "Style Ref: ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+21, "Item: ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+21, "Size: ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+26, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+26, "Color: ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+31, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+31, "Gmts. No: ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+36, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10,$wide = true,true) ;
			$pdf->Code39($i, $j+36, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				
			$i=8; $j=$j+60;
			
			/*$k++;
			if($k==2)
			{ $k=0; $i=10; $j=$j+75; }*/
			$br++;
		} 
	}
	
	foreach (glob(""."*.pdf") as $filename) {			
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_one")
{	
	?>
    <style type="text/css" media="print">
       	 p{ page-break-after: always;}
    	</style>
    <?
	$data=explode("***",$data);
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	
	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}
	
	$color_sizeID_arr=sql_select("select a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence 
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");
	
	$bundle_array=array();
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach($sql_name as $value)
	{
		$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$batch_no=$cut_value[csf('batch_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	
	if($data[7]=="") $data[7]=0;
	$i=1;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($color_sizeID_arr as $val)
		{
			foreach($sql_bundle_copy as $inf)
			{
				$bundle_array[$i]=$val[csf("bundle_no")];
				echo '<table style="width: 4.0in;" border="0" cellpadding="0" cellspacing="0">';
				$bundle="&nbsp;&nbsp;".$val[csf("bundle_no")];
				$title="Bundle Card<br>".$inf[csf("bundle_use_for")]."<br>"."Roll No: ". $val[csf("roll_no")];
				/*
				$pdf->Code39($i, $j+11,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+11,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+16,  "Style Ref: ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+21, "Item: ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+21, "Size: ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+26, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+26, "Color: ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+31, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+31, "Gmts. No: ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+36, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+36, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;*/
				echo '<tr><td style="padding-left:5px;padding-top:10px;padding-bottom:5px"><div id="div_'.$i.'"></div>'.$bundle.'</td><td>'.$title.'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Cut Sys No: '.$new_cut_no.'</td><td>Cut Date: '.$cut_date.'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Buyer: '.$buyer_library[$buyer_name].'</td><td>PO: '.$po_number.'</td></tr>';
				echo '<tr><td style="padding-left:5px;" colspan="2">&nbsp;&nbsp;Style Ref: '.$style_name.'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Size: '.$size_arr[$val[csf("size_id")]].'</td><td>Item: '.$garments_item[$data[4]].'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Batch No: '.$bacth_array[$batch_id].'</td><td>Color: '.$color_library[$data[5]].'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. No: '.$val[csf("number_start")]."-".$val[csf("number_end")].'</td><td>Bundle No: '.$val[csf("bundle_no")].'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. Qnty: '.$val[csf("size_qty")].'</td><td>Country: '.$country_arr[$val[csf("country_id")]].'</td></tr>';
				echo '</table><p></p>';
				$i++;
			}
		}   
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			/*if($br==1) { $pdf->AddPage(); $br=0; $i=8; $j=8; $k=0;}
			
			$pdf->Code39($i, $j, $val[csf("bundle_no")]);
			$pdf->Code39($i+45, $j-4, "Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
			$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
			$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+6, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+11,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+11,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+16,  "Style Ref: ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+21, "Item: ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+21, "Size: ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+26, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+26, "Color: ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+31, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+31, "Gmts. No: ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+36, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10,$wide = true,true) ;
			$pdf->Code39($i, $j+36, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;*/
				
			$bundle_array[$i]=$val[csf("bundle_no")];
			echo '<table style="width: 4.0in;" border="0" cellpadding="0" cellspacing="0">';
			$bundle="&nbsp;&nbsp;".$val[csf("bundle_no")];
			$title="Roll No: ". $val[csf("roll_no")];
			echo '<tr><td style="padding-left:5px;padding-top:10px;padding-bottom:5px"><div id="div_'.$i.'"></div>'.$bundle.'</td><td>'.$title.'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Cut Sys No: '.$new_cut_no.'</td><td>Cut Date: '.$cut_date.'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Buyer: '.$buyer_library[$buyer_name].'</td><td>PO: '.$po_number.'</td></tr>';
			echo '<tr><td style="padding-left:5px;" colspan="2">&nbsp;&nbsp;Style Ref: '.$style_name.'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Size: '.$size_arr[$val[csf("size_id")]].'</td><td>Item: '.$garments_item[$data[4]].'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Batch No: '.$bacth_array[$batch_id].'</td><td>Color: '.$color_library[$data[5]].'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. No: '.$val[csf("number_start")]."-".$val[csf("number_end")].'</td><td>Bundle No: '.$val[csf("bundle_no")].'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. Qnty: '.$val[csf("size_qty")].'</td><td>Country: '.$country_arr[$val[csf("country_id")]].'</td></tr>';
			echo '</table><p></p>';
			$i++;
		} 
	}
	
	?>
    
    <script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		var barcode_array =<? echo json_encode($bundle_array); ?>;
		function generateBarcode( td_no, valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#div_"+td_no).show().barcode(value, btype, settings);
		}

		for (var i in barcode_array) 
		{
			generateBarcode(i,barcode_array[i]);
		}
	</script>
    <?
	exit();
}

if($action=="print_report_bundle_barcode")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	$data=explode("***",$data);
	
	//$ext_data=explode("__",$data[1]);
	//$cs_data=explode("__",$data[2]);
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$pdf=new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty,country_id from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //where id in ($data)
	$i=5; $j=10; $k=0;
    $bundle_array=array();
	$br=0;
	$n=0;
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach($sql_name as $value)
	{
		 $product_dept_name=$value[csf('product_dept')];
		 $style_name=$value[csf('style_ref_no')];
		 $buyer_name=$value[csf('buyer_name')];
		 $po_number=$value[csf('po_number')];
		 
		 
	}
	 
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";

	 }
    	 $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id");
		 
		 $cope_page=1;
		 if(count($sql_bundle_copy)!=0)
		 {
		 foreach($sql_bundle_copy as $inf)
		 {
			if($br==6) { $pdf->AddPage(); $br=0; $i=5; $j=10; $k=0; }
			foreach($color_sizeID_arr as $val)
			   {
				    
					if($br==6) 
					{
						 $pdf->AddPage(); $br=0; $i=5; $j=10; $k=0;
					     
					 }
					
					if( $k>0 && $k<2 ) { $i=$i+100; }
					    
						$pdf->Code39($i, $j, $val[csf("bundle_no")]);
						$pdf->Code39($i+45, $j-4, "Bundle Card ", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+45, $j, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+6, "Cut Date	 : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+12,  "Buyer : ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+12,"Ord:".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+18,  "Style Ref  :  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+24, "Item :  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+30, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+36, "Color	:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+30, "Size :  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+42, "Dept : ".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						
					   $pdf->Code39($i, $j+42, "Bundle No:".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+48, "Gmts. No :  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+48, "Gmts. Qnty : ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+54, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
					$k++;
					
					if($k==2)
					{  $k=0; $i=5; $j=$j+90; }
					$br++;
				 }
				 $br=6;
		    $cope_page++;
	    }  
		 }
		else
		{
		   foreach($color_sizeID_arr as $val)
			   {
					if($br==6) { $pdf->AddPage(); $br=0; $i=5; $j=10; $k=0; }
					if( $k>0 && $k<2 ) { $i=$i+100; }
						$pdf->Code39($i, $j, $val[csf("bundle_no")]);
						//$pdf->Code39($i+45, $j, "Bundle Card ".$bundle_title, $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+6, "Cut Date	 : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+12,  "Buyer : ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+12,"Ord:".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+18,  "Style Ref  :  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+24, "Item :  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+30, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+36, "Color	:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+30, "Size :  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+42, $j+42, "Dept : ".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						
					   $pdf->Code39($i, $j+42, "Bundle No:".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+48, "Gmts. No :  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+48, "Gmts. Qnty : ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+54, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
					$k++;
					
					if($k==2)
					{  $k=0; $i=5; $j=$j+90; }
					$br++;
				
				} 
		}
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

//save size and bundle
if($action=="save_update_delete_size")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
    	$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");
		$cut_on_prifix=$cutData[0][csf('cut_num_prefix_no')];
		$job_no=$cutData[0][csf('job_no')];
		
		$plan_qty= return_field_value("sum(plan_cut_qnty) as plan_qty","wo_po_color_size_breakdown","po_break_down_id=".$order_id." and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1","plan_qty");
		
		$total_marker_qty_prev= return_field_value("sum(marker_qty) as mark_qty","ppl_cut_lay_dtls","order_id=".$order_id." and gmt_item_id=".$gmt_id." and color_id=".$color_id." and status_active=1","mark_qty");
		
		$sizeRatioArr=array(); $sizeQtyArr=array(); $sizeQtyArrForC=array(); $sizeIdAgainstSeq=array(); $seqDatas='';
		$id_size=return_next_id("id", "ppl_cut_lay_size_dtls", 1);	
		$field_array_size="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date";
		for($i=1; $i<=$size_row_num; $i++)
		{
			$hidden_sizef_id="hidden_sizef_id_".$i;
			$txt_layf_balance="txt_layf_balance_".$i;
			$txt_sizef_ratio="txt_sizef_ratio_".$i;
			$txt_sizef_qty="txt_sizef_qty_".$i;
			$bundle_sequence="txt_bundle_".$i;

			if(str_replace("'",'',$$txt_sizef_qty)>0)
			{
				if(str_replace("'",'',$$bundle_sequence)>0)
				{
					$seq=str_replace("'",'',$$bundle_sequence);
				}
				else
				{
					$max_seq++;
					$seq=$max_seq;
				}
				
				$dataSize=$mst_id.",".$dtls_id.",".$color_id.",".$$hidden_sizef_id.",".$$txt_sizef_ratio.",".$$txt_sizef_qty.",".$seq;
				$data_array_up[$seq]=$dataSize;
				$sizeIdAgainstSeq[$seq]=str_replace("'",'',$$hidden_sizef_id);
				
				$sizeRatioArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_ratio);
				$sizeQtyArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeQtyArrForC[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeSeqArr[str_replace("'",'',$$hidden_sizef_id)]=$seq;
			}
		}
		
		ksort($data_array_up);
		foreach($data_array_up as $sequence=>$data)
		{
			if($data_array_size!="") $data_array_size.= ",";
			$data_array_size.="(".$id_size.",".$data.",".$user_id.",'".$pc_date_time."')";
			$seqDatas.=$id_size."__".$sizeIdAgainstSeq[$sequence]."__".$sequence.",";
			$id_size++; 
		}
		
		$roll_size_arr=array(); $roll_no_arr=array(); $rollsizeBl=array();
		$rollDatas=explode("|",$roll_data);
		$rollDtls_id=return_next_id("id", "ppl_cut_lay_roll_dtls",1);
		$field_array_roll_dtls="id,mst_id,dtls_id,roll_id,roll_no,roll_wgt,plies,size_id,size_qty,inserted_by,insert_date";
		foreach($rollDatas as $data)
		{
			$datas=explode("=",$data);
			$roll_no=$datas[1];
			$roll_id=$datas[2];
			$roll_wgt=$datas[3];
			$plies=$datas[4];
			$batch_No_plies=$datas[5];
			$extra_fabric=$datas[6];
			$reject_fabric=$datas[7];

			if($roll_id=="" || $roll_id==0) $roll_id=$rollDtls_id;
			
			$roll_no_arr[$roll_id]=$roll_no;
			foreach($sizeRatioArr as $size_id=>$size_ratio)
			{
				$size_qty=$size_ratio*$plies;
				if($data_array_roll_dtls!="") $data_array_roll_dtls.= ",";
				$data_array_roll_dtls.="(".$rollDtls_id.",".$mst_id.",".$dtls_id.",'".$roll_id."','".$roll_no."','".$roll_wgt."','".$plies."','".$size_id."',".$size_qty.",".$user_id.",'".$pc_date_time."')";
				// '".$batch_No_plies."','".$extra_fabric."','".$reject_fabric."',
				$rollsizeBl[$size_id][$roll_id]=$size_qty;
				$rollPliesArr[$size_id][$roll_id]=$plies;
				$rollDtls_id++;
			}
		}

		$bundle_no_array=array();
		$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
		$field_array_bundle="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,country_type,country_id,roll_id,roll_no,pattern_no,order_id,is_excess,color_type_id,inserted_by,insert_date";
		
		//$last_rmg_no=return_field_value("max(number_end) as last_rmg", "ppl_cut_lay_bundle","mst_id=$mst_id","last_rmg");
		//$last_bundle_no=return_field_value("max(bundle_num_prefix_no) as last_prefix", "ppl_cut_lay_bundle","mst_id=$mst_id","last_prefix");
		
		$bundleData=sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id");
		$last_rmg_no=$bundleData[0][csf('last_rmg')];
		$last_bundle_no=$bundleData[0][csf('last_prefix')];
		
		$update_id=''; $tot_marker_qnty_curr=0; $bundle_prif_no=$last_bundle_no; $size_country_array=array(); $country_type_array=array(); $sizeRatioBlArr=array();
		
		$id=return_next_id("id", "ppl_cut_lay_size", 1); 
		$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,country_type,country_id,order_id,size_wise_repeat,inserted_by,insert_date";
		for($i=1; $i<=$row_num; $i++)
		{
			$txt_size_id="hidden_size_id_".$i;
			$txt_lay_balance="txt_lay_balance_".$i;
			$cboCountryType="cboCountryType_".$i;
			$cboCountry="cboCountry_".$i;
			
			$marker_qty=0;
			$size_id=str_replace("'",'',$$txt_size_id);
			$lay_balance=str_replace("'",'',$$txt_lay_balance);
			$size_qty=$sizeQtyArrForC[$size_id];
			$country_type_array[str_replace("'",'',$$cboCountry)]=str_replace("'",'',$$cboCountryType);
			
			if($size_qty>0)
			{
				if($size_qty>$lay_balance) 
				{
					$marker_qty=$lay_balance; 
					$sizeQtyArrForC[$size_id]-=$marker_qty;
				}
				else 
				{
					$marker_qty=$size_qty;
					$sizeQtyArrForC[$size_id]=0;
				}
				$size_country_array[$size_id][str_replace("'",'',$$cboCountry)]+=$marker_qty;
				$tot_marker_qnty_curr+=$marker_qty;
				
				if($data_array!="") $data_array.= ",";
				$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",0,".$marker_qty.",".$$cboCountryType.",".$$cboCountry.",".$order_id.",0,".$user_id.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		//print_r($size_country_array[4]);die;
		//echo "10**";echo $txt_plies;die;
		$company_sort_name=explode("-",$cut_no); $bundle_per_pcs=str_replace("'","",$bundle_per_pcs);
		/*foreach($sizeQtyArr as $size_id=>$size_qty)
		{
			$erange=$last_rmg_no;
			foreach($size_country_array[$size_id] as $country_id=>$size_country_qty)
			{
				$bl_size_qty=$size_country_qty; $pattern_no="A";
				$size_ratio=$sizeRatioArr[$size_id];
				
				for($k=1; $k<=$size_ratio; $k++)
				{
					$plies=$txt_plies; $erange=0;
					foreach($roll_no_arr as $rollId=>$rollNo)
					{
						$bl_roll_plies=$rollPliesArr[$size_id][$rollId];
						
						if($plies>0)
						{
							if($bl_roll_plies>=$bundle_per_pcs)
							{
								$bundle_per_size=ceil($bl_roll_plies/$bundle_per_pcs);
								for($z=1; $z<=$bundle_per_size; $z++)
								{
									if($bl_size_qty>0)
									{
										if($bl_roll_plies>$bundle_per_pcs) 
										{
											$bundle_qty=$bundle_per_pcs;
										}
										else 
										{
											$bundle_qty=$bl_roll_plies;
										}
										
										if($bundle_qty>$bl_size_qty) 
										{
											$bundle_qty=$bl_size_qty;
										}
										
										$bl_roll_plies-=$bundle_qty;
										
										$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
										$bundle_prif_no=$bundle_prif_no+1;
										$bundle_no=$bundle_prif."-".$bundle_prif_no;
										$srange=$erange+1;
										$erange=$srange+$bundle_qty-1;
										$tot_bundle_qty+=$bundle_qty;
										$bl_size_qty-=$bundle_qty;
										$plies-=$bundle_qty;
										
										$country_type=$country_type_array[$country_id];
										
										if($data_array_bundle!="") $data_array_bundle.= ",";
										$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$user_id.",'".$pc_date_time."')";
										$bundle_id=$bundle_id+1;
									}
								}
							}
							else
							{
								$bundle_qty2=$bl_roll_plies;
								$bl_roll_plies=0;
								
								if($bundle_qty2>0 && $bl_size_qty>0)
								{
									if($bundle_qty2>$bl_size_qty) 
									{
										$bundle_qty2=$bl_size_qty;
									}
									
									$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
									$bundle_prif_no=$bundle_prif_no+1;
									$bundle_no=$bundle_prif."-".$bundle_prif_no;
									$srange=$erange+1;
									$erange=$srange+$bundle_qty2-1;
									$tot_bundle_qty+=$bundle_qty2;
									$bl_size_qty-=$bundle_qty2;
									$plies-=$bundle_qty2;
									
									$country_type=$country_type_array[$country_id];
									
									if($data_array_bundle!="") $data_array_bundle.= ",";
									$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$user_id.",'".$pc_date_time."')";
									$bundle_id=$bundle_id+1;
									//echo $rollNo.",".$srange.",".$erange;
								}
							}
						}
					}
					
					$pattern_no++;
				}
			}
		}*/
		
		asort($sizeSeqArr);
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			for($k=1; $k<=$size_ratio; $k++)
			{
				foreach($roll_no_arr as $rollId=>$rollNo)
				{
					$sizeRatioBlArr[$size_id][$pattern_no][$rollId]=$rollPliesArr[$size_id][$rollId];
				}
				$pattern_no++;
			}
		}
		
		$erange=0;
		if($rmg_no_creation==2) // cutting wise
		{
			$erange=return_field_value("max(number_end) as last_rmg","ppl_cut_lay_bundle","mst_id=$mst_id and dtls_id!=$dtls_id and status_active=1 and is_deleted=0","last_rmg");
		}
		else if($rmg_no_creation==3) // job wise
		{
			$erange=return_field_value("max(a.number_end) as last_rmg","ppl_cut_lay_bundle a, ppl_cut_lay_mst b","a.mst_id=b.id and b.entry_form=97 and b.job_no='$job_id' and a.status_active=1 and a.is_deleted=0","last_rmg");
		}
		else if($rmg_no_creation==4) // order wise
		{
			$erange=return_field_value("max(a.number_end) as last_rmg","ppl_cut_lay_bundle a, ppl_cut_lay_dtls b","a.dtls_id=b.id and b.order_id='$order_id' and a.status_active=1 and a.is_deleted=0","last_rmg");
		}
		
		//echo "10**".$erange;die;
		//print_r($rollsizeBl);//die;
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		// $year_id=date('Y',time());
		$cutNo = str_replace("'", "", $cut_no);
		$cutNoEx = explode("-", $cutNo);
		$year_id=$cutNoEx[1];
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			if($rmg_no_creation==1) { $erange=0; } 
			
			for($k=1; $k<=$size_ratio; $k++)
			{
				$plies=$txt_plies; $tmp_bl_arr=array();
				foreach($size_country_array[$size_id] as $country_id=>$size_country_qty)
				{
					foreach($roll_no_arr as $rollId=>$rollNo)
					{
						if($sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
						{
							$bl_size_qty=$size_country_array[$size_id][$country_id];
							if($plies>0 && $tmp_bl_arr[$size_id][$rollId][1]>0 && $bl_size_qty>0)
							{
								$bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1]; $tmp_bl_arr[$size_id][$rollId][1]=0;
								//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
								$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
								$bundle_prif_no=$bundle_prif_no+1;
								$bundle_no=$bundle_prif."-".$bundle_prif_no;
								$srange=$erange+1;
								$erange=$srange+$bundle_qty2-1;
								$tot_bundle_qty+=$bundle_qty2;
								$barcode_suffix_no=$barcode_suffix_no+1;
								$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
								//$bl_size_qty-=$bundle_qty2;
								$size_country_array[$size_id][$country_id]-=$bundle_qty2;
								$plies-=$bundle_qty2;
								
								$country_type=$country_type_array[$country_id];
								
								if($data_array_bundle!="") $data_array_bundle.= ",";
								$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$order_id.",0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
								$bundle_id=$bundle_id+1;
								$bl_roll_plies=$rollPliesArr[$size_id][$rollId]-($bundle_qty2+$tmp_bl_arr[$size_id][$rollId][2]); $tmp_bl_arr[$size_id][$rollId][2]=0;
								$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;
							}
							else
							{
								$bl_roll_plies=$rollPliesArr[$size_id][$rollId];
							}
							
							//$bl_size_qty=$size_country_array[$size_id][$country_id];
							if($plies>0 && $bl_roll_plies>0 && $bl_size_qty>0)
							{
								if($bl_roll_plies>=$bundle_per_pcs)
								{ 
									$bundle_per_size=ceil($bl_roll_plies/$bundle_per_pcs);
									for($z=1; $z<=$bundle_per_size; $z++)
									{
										$bl_size_qty=$size_country_array[$size_id][$country_id];
										if($bl_size_qty>0 && $sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
										{
											if($bl_roll_plies>$bundle_per_pcs) 
											{
												$bundle_qty=$bundle_per_pcs;
											}
											else 
											{
												$bundle_qty=$bl_roll_plies;
											}
											
											if($bundle_qty>$bl_size_qty) 
											{
												$bundle_qty=$bl_size_qty;
											}
											
											if($bundle_qty>$plies)
											{
												$bundle_qty=$plies; 
											}
											
											if($bundle_qty>$sizeRatioBlArr[$size_id][$pattern_no][$rollId])
											{
												$bundle_qty=$sizeRatioBlArr[$size_id][$pattern_no][$rollId]; 
											}
											
											$bl_roll_plies-=$bundle_qty;
											
											$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
											$bundle_prif_no=$bundle_prif_no+1;
											$bundle_no=$bundle_prif."-".$bundle_prif_no;
											$srange=$erange+1;
											$erange=$srange+$bundle_qty-1;
											$tot_bundle_qty+=$bundle_qty;
											$barcode_suffix_no=$barcode_suffix_no+1;
											$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
											//$bl_size_qty-=$bundle_qty;
											$size_country_array[$size_id][$country_id]-=$bundle_qty;
											$plies-=$bundle_qty;
											
											$country_type=$country_type_array[$country_id];
											
											if($data_array_bundle!="") $data_array_bundle.= ",";
											$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$order_id.",0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
											$bundle_id=$bundle_id+1;
											$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty;
										}
									}
								}
								else
								{
									//echo $bl_roll_plies."__".$bl_size_qty.",";
									//echo $bl_roll_plies."__".$bl_size_qty."__".$pattern_no."__".$country_id."<br>";
									/*$bundle_qty2=$bl_roll_plies; $bl_roll_plies=0;
									if($pattern_no=="B")
									{
										echo $rollId."__".$rollsizeBl[$size_id][$rollId].",";	
									}*/
									if($bl_roll_plies>$plies)
									{
										$bundle_qty2=$plies; 
										$bl_roll_plies=$bl_roll_plies-$plies;
									}
									else
									{
										$bundle_qty2=$bl_roll_plies; 
										$bl_roll_plies=0;
									}
									if($bundle_qty2>0)
									{
										if($bundle_qty2>$bl_size_qty) 
										{
											$tmp_bl_arr[$size_id][$rollId][1]=$bundle_qty2-$bl_size_qty;//echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
											$tmp_bl_arr[$size_id][$rollId][2]=$bl_size_qty;
											$bundle_qty2=$bl_size_qty;
										}
										else { $tmp_bl_arr[$size_id][$rollId][1]=0; $tmp_bl_arr[$size_id][$rollId][2]=0; }
										$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
										$bundle_prif_no=$bundle_prif_no+1;
										$bundle_no=$bundle_prif."-".$bundle_prif_no;
										$srange=$erange+1;
										$erange=$srange+$bundle_qty2-1;
										$tot_bundle_qty+=$bundle_qty2;
										$barcode_suffix_no=$barcode_suffix_no+1;
										$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
										//$bl_size_qty-=$bundle_qty2;
										$size_country_array[$size_id][$country_id]-=$bundle_qty2;
										$plies-=$bundle_qty2;
										
										$country_type=$country_type_array[$country_id];
										
										if($data_array_bundle!="") $data_array_bundle.= ",";
										$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$order_id.",0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
										$bundle_id=$bundle_id+1;
										$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;
										//echo $rollNo.",".$srange.",".$erange;
									}
								}
							}
						}
					}
				}
				$pattern_no++;
			}
		}
		//die;
		//echo "10**".key($size_country_array[4]);die;
		foreach($sizeRatioBlArr as $size_id=>$size_data)
		{
			//$country_type=1; $country_id=0;
			/*if(count($size_country_array[$size_id])==1) 
			{
				$country_id=key($size_country_array[$size_id]);
			}
			else $country_id=0;*/
			
			
			$country_id_keys=array_keys($size_country_array[$size_id]);
			$country_id=$country_id_keys[0];
			$country_type=$country_type_array[$country_id];
			
			if($rmg_no_creation==1) { $erange=0; }
			foreach($size_data as $pattern_no=>$pattern_data)
			{
				foreach($pattern_data as $rollId=>$bundle_qty)
				{
					if($bundle_qty>0)
					{
						if($bundle_qty>=$bundle_per_pcs)
						{ 
							$bundle_per_size=ceil($bundle_qty/$bundle_per_pcs);
							$bl_size_qty=$bundle_qty;
							for($z=1; $z<=$bundle_per_size; $z++)
							{
								if($bl_size_qty>0)
								{
									if($bl_size_qty>$bundle_per_pcs) 
									{
										$bundle_qty2=$bundle_per_pcs;
									}
									else 
									{
										$bundle_qty2=$bl_size_qty;
									}
									
									$bl_size_qty-=$bundle_qty2;
									
									$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
									$bundle_prif_no=$bundle_prif_no+1;
									$bundle_no=$bundle_prif."-".$bundle_prif_no;
									$srange=$erange+1;
									$erange=$srange+$bundle_qty2-1;
									$tot_bundle_qty+=$bundle_qty2;
									$barcode_suffix_no=$barcode_suffix_no+1;
									$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
										
									$rollNo=$roll_no_arr[$rollId];
												
									if($data_array_bundle!="") $data_array_bundle.= ",";
									$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."','".$country_id."','".$rollId."',".$rollNo.",'".$pattern_no."',".$order_id.",1,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
									$bundle_id=$bundle_id+1;
								}
							}
						}
						else
						{
							$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
							$bundle_prif_no=$bundle_prif_no+1;
							$bundle_no=$bundle_prif."-".$bundle_prif_no;
							$srange=$erange+1;
							$erange=$srange+$bundle_qty-1;
							$tot_bundle_qty+=$bundle_qty;
							$barcode_suffix_no=$barcode_suffix_no+1;
							$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
							$rollNo=$roll_no_arr[$rollId];
												
							if($data_array_bundle!="") $data_array_bundle.= ",";
							$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."','".$country_id."','".$rollId."',".$rollNo.",'".$pattern_no."',".$order_id.",1,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
							$bundle_id=$bundle_id+1;
						}
					}
				}
			}
		}

		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle)values".$data_array_bundle; die;
		//echo "10**insert into ppl_cut_lay_size($field_array)values".$data_array;die;
		$rID=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
		$rID_size=sql_insert("ppl_cut_lay_size_dtls",$field_array_size,$data_array_size,0);
		//echo "10**insert into ppl_cut_lay_roll_dtls($field_array_roll_dtls) values".$data_array_roll_dtls;die;
		$rID2=sql_insert("ppl_cut_lay_bundle",$field_array_bundle,$data_array_bundle,0);
		$rID3=sql_insert("ppl_cut_lay_roll_dtls",$field_array_roll_dtls,$data_array_roll_dtls,0);
		$field_array_up="marker_qty*pcs_per_bundle*updated_by*update_date";
		$data_array_up=$to_marker_qty."*'".$txt_bundle_pcs."'*'".$user_id."'*'".$pc_date_time."'";
		$rID4=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0); 
		//echo "10**".$rID.$rID_size.$rID2.$rID3.$rID4;die;
		
		$total_marker_qty=$total_marker_qty_prev+$tot_marker_qnty_curr;
		$lay_balance=$plan_qty-$total_marker_qty;
		//echo "10**".$seqDatas;die;
		if($db_type==0)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance."****".$tot_marker_qnty_curr;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);  
				echo "0**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance."****".$tot_marker_qnty_curr;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","cutting_no='".$cut_no."'");
		$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");
		//$cut_on_prifix = return_field_value("cut_num_prefix_no","ppl_cut_lay_mst","cutting_no = '".$cut_no."'");
		$cut_on_prifix=$cutData[0][csf('cut_num_prefix_no')];
		$job_no=$cutData[0][csf('job_no')];
		//echo $cuttingc_no;die;
		//if($cutting_qc_no!="") { echo "200**".$mst_id."**".$dtls_id."**".$cutting_qc_no; die;}
		
		$previous_barcode_data=sql_select("select bundle_no,barcode_no,barcode_year,barcode_prifix from ppl_cut_lay_bundle where mst_id=".$mst_id."  and  dtls_id=".$dtls_id." and status_active=1 and is_deleted=0 ");
		foreach($previous_barcode_data as $b_val)
		{
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['year']=$b_val[csf("barcode_year")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['prifix']=$b_val[csf("barcode_prifix")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['barcode']=$b_val[csf("barcode_no")];
		}
		
		$plan_qty= return_field_value("sum(plan_cut_qnty) as plan_qty","wo_po_color_size_breakdown","po_break_down_id=".$order_id." and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1","plan_qty");
		
		$total_marker_qty_prev= return_field_value("sum(marker_qty) as mark_qty","ppl_cut_lay_dtls","id!=$dtls_id and order_id=".$order_id." and gmt_item_id=".$gmt_id." and color_id=".$color_id." and status_active=1","mark_qty");
		
		$sizeRatioArr=array(); $sizeQtyArr=array(); $sizeQtyArrForC=array(); $sizeIdAgainstSeq=array();
		$id_size=return_next_id("id", "ppl_cut_lay_size_dtls", 1);	
		$field_array_size="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date";
		for($i=1; $i<=$size_row_num; $i++)
		{
			$hidden_sizef_id="hidden_sizef_id_".$i;
			$txt_layf_balance="txt_layf_balance_".$i;
			$txt_sizef_ratio="txt_sizef_ratio_".$i;
			$txt_sizef_qty="txt_sizef_qty_".$i;
			$bundle_sequence="txt_bundle_".$i;

			if(str_replace("'",'',$$txt_sizef_qty)>0)
			{
				if(str_replace("'",'',$$bundle_sequence)>0)
				{
					$seq=str_replace("'",'',$$bundle_sequence);
				}
				else
				{
					$max_seq++;
					$seq=$max_seq;
				}
				
				$dataSize=$mst_id.",".$dtls_id.",".$color_id.",".$$hidden_sizef_id.",".$$txt_sizef_ratio.",".$$txt_sizef_qty.",".$seq;
				$data_array_up[$seq]=$dataSize;
				$sizeIdAgainstSeq[$seq]=str_replace("'",'',$$hidden_sizef_id);
				
				$sizeRatioArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_ratio);
				$sizeQtyArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeQtyArrForC[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeSeqArr[str_replace("'",'',$$hidden_sizef_id)]=$seq;
			}
		}
		
		$seqDatas='';
		ksort($data_array_up);
		foreach($data_array_up as $sequence=>$data)
		{
			if($data_array_size!="") $data_array_size.= ",";
			$data_array_size.="(".$id_size.",".$data.",".$user_id.",'".$pc_date_time."')";
			$seqDatas.=$id_size."__".$sizeIdAgainstSeq[$sequence]."__".$sequence.",";
			$id_size++; 
		}
		
		$roll_size_arr=array(); $roll_no_arr=array(); $rollsizeBl=array(); $rollPliesArr=array(); $sizeRatioBlArr=array();
		$rollDatas=explode("|",$roll_data);
		$rollDtls_id=return_next_id("id", "ppl_cut_lay_roll_dtls",1);
		$field_array_roll_dtls="id,mst_id,dtls_id,roll_id,roll_no,roll_wgt,plies,size_id,size_qty,inserted_by,insert_date";
		foreach($rollDatas as $data)
		{
			$datas=explode("=",$data);
			$roll_no=$datas[1];
			$roll_id=$datas[2];
			$roll_wgt=$datas[3];
			$plies=$datas[4];
			$batch_No_plies=$datas[5];
			$extra_fabric==$datas[6];
			$reject_fabric=$datas[7];


			if($roll_id=="" || $roll_id==0) $roll_id=$rollDtls_id;
			
			$roll_no_arr[$roll_id]=$roll_no;
			foreach($sizeRatioArr as $size_id=>$size_ratio)
			{
				$size_qty=$size_ratio*$plies;
				if($data_array_roll_dtls!="") $data_array_roll_dtls.= ",";
				$data_array_roll_dtls.="(".$rollDtls_id.",".$mst_id.",".$dtls_id.",'".$roll_id."','".$roll_no."','".$roll_wgt."','".$plies."','".$size_id."',".$size_qty.",".$user_id.",'".$pc_date_time."')";
				// ,'".$batch_No_plies."','".$extra_fabric."','".$reject_fabric."'
				$rollsizeBl[$size_id][$roll_id]=$size_qty;
				$rollPliesArr[$size_id][$roll_id]=$plies;
				
				$rollDtls_id++;
			}
		}
		
		$bundle_no_array=array(); $last_rmg_no='';
		$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
		$field_array_bundle="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,country_type,country_id,roll_id,roll_no,pattern_no,order_id,is_excess,color_type_id,inserted_by,insert_date";
		
		//$last_rmg_no=return_field_value("max(a.number_end) as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b","a.mst_id=b.id and a.mst_id='".$mst_id."' and a.dtls_id!=$dtls_id","last_rmg");
		//$last_bundle_no=return_field_value("max(bundle_num_prefix_no) as last_prefix", "ppl_cut_lay_bundle","mst_id=$mst_id and dtls_id!=$dtls_id","last_prefix");
		
		$bundleData=sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id and dtls_id!=$dtls_id");
		$last_rmg_no=$bundleData[0][csf('last_rmg')];
		$last_bundle_no=$bundleData[0][csf('last_prefix')];
		
		$tot_marker_qnty_curr=0; $bundle_prif_no=$last_bundle_no; $size_country_array=array(); $country_type_array=array(); 
		//echo "10**"."SELECT size_id, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='".$mst_id."' and dtls_id!='".$dtls_id."' group by size_id";die;
		$id=return_next_id("id", "ppl_cut_lay_size", 1);	
		$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,country_type,country_id,order_id,size_wise_repeat,inserted_by,insert_date";
		for($i=1; $i<=$row_num; $i++)
		{
			$txt_size_id="hidden_size_id_".$i;
			$txt_lay_balance="txt_lay_balance_".$i;
			$cboCountryType="cboCountryType_".$i;
			$cboCountry="cboCountry_".$i;
			
			$marker_qty=0;
			$size_id=str_replace("'",'',$$txt_size_id);
			$lay_balance=str_replace("'",'',$$txt_lay_balance);
			$size_qty=$sizeQtyArrForC[$size_id];
			$country_type_array[str_replace("'",'',$$cboCountry)]=str_replace("'",'',$$cboCountryType);
			
			if($size_qty>0)
			{
				if($size_qty>$lay_balance) 
				{
					$marker_qty=$lay_balance; 
					$sizeQtyArrForC[$size_id]-=$marker_qty;
				}
				else 
				{
					$marker_qty=$size_qty;
					$sizeQtyArrForC[$size_id]=0;
				}
				$size_country_array[$size_id][str_replace("'",'',$$cboCountry)]+=$marker_qty;
				$tot_marker_qnty_curr+=$marker_qty;
				
				if($data_array!="") $data_array.= ",";
				$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",0,".$marker_qty.",".$$cboCountryType.",".$$cboCountry.",".$order_id.",0,".$user_id.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		
		//echo "10**";
		//print_r($sizeRatioBlArr);die;
		$company_sort_name=explode("-",$cut_no); $bundle_per_pcs=str_replace("'","",$bundle_per_pcs);
		/*foreach($sizeQtyArr as $size_id=>$size_qty)
		{
			$erange=$last_rmg_no;
			foreach($size_country_array[$size_id] as $country_id=>$size_country_qty)
			{
				$bl_size_qty=$size_country_qty; $pattern_no="A";
				$size_ratio=$sizeRatioArr[$size_id];
				
				for($k=1; $k<=$size_ratio; $k++)
				{
					$plies=$txt_plies; $erange=0;
					foreach($roll_no_arr as $rollId=>$rollNo)
					{
						$bl_roll_plies=$rollPliesArr[$size_id][$rollId];
						
						if($plies>0)
						{
							if($bl_roll_plies>=$bundle_per_pcs)
							{
								$bundle_per_size=ceil($bl_roll_plies/$bundle_per_pcs);
								for($z=1; $z<=$bundle_per_size; $z++)
								{
									if($bl_size_qty>0)
									{
										if($bl_roll_plies>$bundle_per_pcs) 
										{
											$bundle_qty=$bundle_per_pcs;
										}
										else 
										{
											$bundle_qty=$bl_roll_plies;
										}
										
										if($bundle_qty>$bl_size_qty) 
										{
											$bundle_qty=$bl_size_qty;
										}
										
										$bl_roll_plies-=$bundle_qty;
										
										$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
										$bundle_prif_no=$bundle_prif_no+1;
										$bundle_no=$bundle_prif."-".$bundle_prif_no;
										$srange=$erange+1;
										$erange=$srange+$bundle_qty-1;
										$tot_bundle_qty+=$bundle_qty;
										$bl_size_qty-=$bundle_qty;
										$plies-=$bundle_qty;
										
										$country_type=$country_type_array[$country_id];
										
										if($data_array_bundle!="") $data_array_bundle.= ",";
										$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$user_id.",'".$pc_date_time."')";
										$bundle_id=$bundle_id+1;
									}
								}
							}
							else
							{
								$bundle_qty2=$bl_roll_plies;
								$bl_roll_plies=0;
								
								if($bundle_qty2>0 && $bl_size_qty>0)
								{
									if($bundle_qty2>$bl_size_qty) 
									{
										$bundle_qty2=$bl_size_qty;
									}
									$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
									$bundle_prif_no=$bundle_prif_no+1;
									$bundle_no=$bundle_prif."-".$bundle_prif_no;
									$srange=$erange+1;
									$erange=$srange+$bundle_qty2-1;
									$tot_bundle_qty+=$bundle_qty2;
									$bl_size_qty-=$bundle_qty2;
									$plies-=$bundle_qty2;
									
									$country_type=$country_type_array[$country_id];
									
									if($data_array_bundle!="") $data_array_bundle.= ",";
									$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$user_id.",'".$pc_date_time."')";
									$bundle_id=$bundle_id+1;
									//echo $rollNo.",".$srange.",".$erange;
								}
							}
						}
					}
					
					$pattern_no++;
				}
			}
		}*/
		
		asort($sizeSeqArr);
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			for($k=1; $k<=$size_ratio; $k++)
			{
				foreach($roll_no_arr as $rollId=>$rollNo)
				{
					$sizeRatioBlArr[$size_id][$pattern_no][$rollId]=$rollPliesArr[$size_id][$rollId];
				}
				$pattern_no++;
			}
		}
		
		$erange=0;
		if($rmg_no_creation==2)
		{
			$erange=return_field_value("max(number_end) as last_rmg","ppl_cut_lay_bundle","mst_id=$mst_id and dtls_id!=$dtls_id and status_active=1 and is_deleted=0","last_rmg");
		}
		else if($rmg_no_creation==3)
		{
			$erange=return_field_value("max(a.number_end) as last_rmg","ppl_cut_lay_bundle a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c","a.mst_id=b.id and b.id=c.mst_id and b.entry_form=97 and b.job_no='$job_id' and c.id!=$dtls_id and a.status_active=1 and a.is_deleted=0","last_rmg");
		}
		else if($rmg_no_creation==4)
		{
			$erange=return_field_value("max(a.number_end) as last_rmg","ppl_cut_lay_bundle a, ppl_cut_lay_dtls b","a.dtls_id=b.id and b.order_id='$order_id' and b.id!=$dtls_id and a.status_active=1 and a.is_deleted=0","last_rmg");
			
		}
		//echo "10**".$z;die;
		//print_r($rollsizeBl);//die;
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		// $year_id=date('Y',time());
		$cutNo = str_replace("'", "", $cut_no);
		$cutNoEx = explode("-", $cutNo);
		$year_id=$cutNoEx[1];
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			if($rmg_no_creation==1) { $erange=0; } 
			
			for($k=1; $k<=$size_ratio; $k++)
			{
				$plies=$txt_plies; $tmp_bl_arr=array();
				foreach($size_country_array[$size_id] as $country_id=>$size_country_qty)
				{
					foreach($roll_no_arr as $rollId=>$rollNo)
					{
						if($sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
						{
							$bl_size_qty=$size_country_array[$size_id][$country_id];
							if($plies>0 && $tmp_bl_arr[$size_id][$rollId][1]>0 && $bl_size_qty>0)
							{
								$bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1]; $tmp_bl_arr[$size_id][$rollId][1]=0;
								$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
								$bundle_prif_no=$bundle_prif_no+1;
								$bundle_no=$bundle_prif."-".$bundle_prif_no;
								$srange=$erange+1;
								$erange=$srange+$bundle_qty2-1;
								$tot_bundle_qty+=$bundle_qty2;
								
								if(empty($previous_barcode_arr[$bundle_no]))
								{
									$barcode_suffix_no=$barcode_suffix_no+1;
									$up_barcode_suffix=$barcode_suffix_no;
									$up_barcode_year=$year_id;
									$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
								}
								else
								{
									$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
									$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
									$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
								}
								//$bl_size_qty-=$bundle_qty2;
								$size_country_array[$size_id][$country_id]-=$bundle_qty2;
								$plies-=$bundle_qty2;
								
								$country_type=$country_type_array[$country_id];
								
								if($data_array_bundle!="") $data_array_bundle.= ",";
								$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$order_id.",0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
								$bundle_id=$bundle_id+1;
								$bl_roll_plies=$rollPliesArr[$size_id][$rollId]-($bundle_qty2+$tmp_bl_arr[$size_id][$rollId][2]); $tmp_bl_arr[$size_id][$rollId][2]=0;
								$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;
							}
							else
							{
								$bl_roll_plies=$rollPliesArr[$size_id][$rollId];
							}
							
							//$bl_size_qty=$size_country_array[$size_id][$country_id];
							if($plies>0 && $bl_roll_plies>0 && $bl_size_qty>0)
							{
								if($bl_roll_plies>=$bundle_per_pcs)
								{ 
									$bundle_per_size=ceil($bl_roll_plies/$bundle_per_pcs);
									for($z=1; $z<=$bundle_per_size; $z++)
									{
										$bl_size_qty=$size_country_array[$size_id][$country_id];
										if($bl_size_qty>0 && $sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
										{
											/*if($pattern_no=="G")
											{
												echo $country_id."**".$rollId."**".$bundle_qty."**".$bl_roll_plies."**".$sizeRatioBlArr[$size_id][$pattern_no][$rollId]."__";
											}*/
											
											if($bl_roll_plies>$bundle_per_pcs) 
											{
												$bundle_qty=$bundle_per_pcs;
											}
											else 
											{
												$bundle_qty=$bl_roll_plies;
											}
											
											if($bundle_qty>$bl_size_qty) 
											{
												$bundle_qty=$bl_size_qty;
											}
											
											if($bundle_qty>$plies)
											{
												$bundle_qty=$plies; 
											}
											
											if($bundle_qty>$sizeRatioBlArr[$size_id][$pattern_no][$rollId])
											{
												$bundle_qty=$sizeRatioBlArr[$size_id][$pattern_no][$rollId]; 
											}
											
											$bl_roll_plies-=$bundle_qty;
											
											$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
											$bundle_prif_no=$bundle_prif_no+1;
											$bundle_no=$bundle_prif."-".$bundle_prif_no;
											$srange=$erange+1;
											$erange=$srange+$bundle_qty-1;
											$tot_bundle_qty+=$bundle_qty;
											
											if(empty($previous_barcode_arr[$bundle_no]))
											{
												$barcode_suffix_no=$barcode_suffix_no+1;
												$up_barcode_suffix=$barcode_suffix_no;
												$up_barcode_year=$year_id;
												$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
											}
											else
											{
												$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
												$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
												$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
											}
											//$bl_size_qty-=$bundle_qty;
											$size_country_array[$size_id][$country_id]-=$bundle_qty;
											$plies-=$bundle_qty;
											
											$country_type=$country_type_array[$country_id];
											
											if($data_array_bundle!="") $data_array_bundle.= ",";
											$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$order_id.",0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
											$bundle_id=$bundle_id+1;
											$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty;
										}
									}
								}
								else
								{
									//echo $bl_roll_plies."__".$bl_size_qty.",";
									//echo $bl_roll_plies."__".$bl_size_qty."__".$pattern_no."__".$country_id."<br>";
									/*$bundle_qty2=$bl_roll_plies; $bl_roll_plies=0;
									if($pattern_no=="B")
									{
										echo $rollId."__".$rollsizeBl[$size_id][$rollId].",";	
									}*/
									if($bl_roll_plies>$plies)
									{
										$bundle_qty2=$plies; 
										$bl_roll_plies=$bl_roll_plies-$plies;
									}
									else
									{
										$bundle_qty2=$bl_roll_plies; 
										$bl_roll_plies=0;
									}
									
									if($bundle_qty2>0)
									{
										if($bundle_qty2>$bl_size_qty) 
										{
											$tmp_bl_arr[$size_id][$rollId][1]=$bundle_qty2-$bl_size_qty;//echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
											$tmp_bl_arr[$size_id][$rollId][2]=$bl_size_qty;
											$bundle_qty2=$bl_size_qty;
										}
										else { $tmp_bl_arr[$size_id][$rollId][1]=0; $tmp_bl_arr[$size_id][$rollId][2]=0; }
										$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
										$bundle_prif_no=$bundle_prif_no+1;
										$bundle_no=$bundle_prif."-".$bundle_prif_no;
										$srange=$erange+1;
										$erange=$srange+$bundle_qty2-1;
										$tot_bundle_qty+=$bundle_qty2;
										
										if(empty($previous_barcode_arr[$bundle_no]))
										{
											$barcode_suffix_no=$barcode_suffix_no+1;
											$up_barcode_suffix=$barcode_suffix_no;
											$up_barcode_year=$year_id;
											$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
										}
										else
										{
											$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
											$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
											$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
										}
										//$bl_size_qty-=$bundle_qty2;
										$size_country_array[$size_id][$country_id]-=$bundle_qty2;
										$plies-=$bundle_qty2;
										
										$country_type=$country_type_array[$country_id];
										
										if($data_array_bundle!="") $data_array_bundle.= ",";
										$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$order_id.",0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
										$bundle_id=$bundle_id+1;
										$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;
										//echo $rollNo.",".$srange.",".$erange;
									}
								}
							}
						}
					}
				}
				$pattern_no++;
			}
		}
		//die;
		//echo "10**";print_r($sizeRatioBlArr);die;
		foreach($sizeRatioBlArr as $size_id=>$size_data)
		{
			//$country_type=1; $country_id=0;
			/*if(count($size_country_array[$size_id])==1) 
			{
				$country_id=key($size_country_array[$size_id]);
			}
			else $country_id=0;*/
			
			$country_id_keys=array_keys($size_country_array[$size_id]);
			$country_id=$country_id_keys[0];
			$country_type=$country_type_array[$country_id];
			if($rmg_no_creation==1) { $erange=0; }
			
			foreach($size_data as $pattern_no=>$pattern_data)
			{
				foreach($pattern_data as $rollId=>$bundle_qty)
				{
					if($bundle_qty>0)
					{
						if($bundle_qty>=$bundle_per_pcs)
						{ 
							$bundle_per_size=ceil($bundle_qty/$bundle_per_pcs);
							$bl_size_qty=$bundle_qty;
							for($z=1; $z<=$bundle_per_size; $z++)
							{
								if($bl_size_qty>0)
								{
									if($bl_size_qty>$bundle_per_pcs) 
									{
										$bundle_qty2=$bundle_per_pcs;
									}
									else 
									{
										$bundle_qty2=$bl_size_qty;
									}
									
									$bl_size_qty-=$bundle_qty2;
									
									$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
									$bundle_prif_no=$bundle_prif_no+1;
									$bundle_no=$bundle_prif."-".$bundle_prif_no;
									$srange=$erange+1;
									$erange=$srange+$bundle_qty2-1;
									$tot_bundle_qty+=$bundle_qty2;
									$rollNo=$roll_no_arr[$rollId];
									
									if(empty($previous_barcode_arr[$bundle_no]))
									{
										$barcode_suffix_no=$barcode_suffix_no+1;
										$up_barcode_suffix=$barcode_suffix_no;
										$up_barcode_year=$year_id;
										$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
									}
									else
									{
										$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
										$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
										$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
									}
												
									if($data_array_bundle!="") $data_array_bundle.= ",";
									$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."',".$order_id.",1,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
									$bundle_id=$bundle_id+1;
								}
							}
						}
						else
						{
							$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
							$bundle_prif_no=$bundle_prif_no+1;
							$bundle_no=$bundle_prif."-".$bundle_prif_no;
							$srange=$erange+1;
							$erange=$srange+$bundle_qty-1;
							$tot_bundle_qty+=$bundle_qty;
							$rollNo=$roll_no_arr[$rollId];
							
							if(empty($previous_barcode_arr[$bundle_no]))
							{
								$barcode_suffix_no=$barcode_suffix_no+1;
								$up_barcode_suffix=$barcode_suffix_no;
								$up_barcode_year=$year_id;
								$barcode_no=$year_id."97".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
							}
							else
							{
								$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
								$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
								$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
							}
												
							if($data_array_bundle!="") $data_array_bundle.= ",";
							$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."','".$country_id."','".$rollId."',".$rollNo.",'".$pattern_no."',".$order_id.",1,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
							$bundle_id=$bundle_id+1;
						}
					}
				}
			}
		}
	   // echo "10**insert into ppl_cut_lay_bundle($field_array_bundle)values".$data_array_bundle;die;
		//die;
		//echo "10**insert into ppl_cut_lay_size($field_array)values".$data_array;die;

		$delete=execute_query("delete from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_size=execute_query("delete from ppl_cut_lay_size_dtls where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_bundle=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_roll=execute_query("delete from ppl_cut_lay_roll_dtls where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		
		$rID=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
		$rID_size=sql_insert("ppl_cut_lay_size_dtls",$field_array_size,$data_array_size,0);
		$rID2=sql_insert("ppl_cut_lay_bundle",$field_array_bundle,$data_array_bundle,0);
		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle."**".$rID2;die;
		$rID3=sql_insert("ppl_cut_lay_roll_dtls",$field_array_roll_dtls,$data_array_roll_dtls,0);
		$field_array_up="marker_qty*pcs_per_bundle*updated_by*update_date";
		$data_array_up="".$to_marker_qty."*'".$txt_bundle_pcs."'*'".$user_id."'*'".$pc_date_time."'";
		$rID4=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0); 
		
		//echo "10**".$rID ."**". $rID_size ."**". $rID2 ."**". $rID3 ."**". $rID4 ."**". $delete ."**". $delete_size."**".$delete_bundle."**".$delete_roll;die;	
		
		$total_marker_qty=$total_marker_qty_prev+$tot_marker_qnty_curr;
		$lay_balance=$plan_qty-$total_marker_qty;
		//echo "10**".$lay_balance."**".$total_marker_qty."**".$total_marker_qty_prev."**".$tot_marker_qnty_curr;die;
		
		if($db_type==0)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4 && $delete && $delete_size && $delete_bundle && $delete_roll)
			{
				mysql_query("COMMIT");  
				echo "1**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance."****".$tot_marker_qnty_curr;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4 && $delete && $delete_size && $delete_bundle && $delete_roll)
			{
				oci_commit($con);  
				echo "1**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance."****".$tot_marker_qnty_curr;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		exit();	
	}	
}

if($action=="show_bundle_list_view")
{
	$ex_data= explode("**",$data);
	$mst_id=$ex_data[0];
	$dtls_id=$ex_data[1];
	
	$country_arr=return_library_array("select id, country_name from lib_country",'id','country_name'); 
	$po_country_array=array();
	$sql_query=sql_select("select distinct a.country_id as country_id from wo_po_color_size_breakdown a, ppl_cut_lay_dtls b where a.item_number_id=b.gmt_item_id and a.po_break_down_id=b.order_id and a.color_number_id=b.color_id and b.mst_id=$mst_id and b.id=$dtls_id and a.status_active=1 and a.is_deleted=0");
	$size_details=array(); $sizeId_arr=array(); $shipDate_arr=array();
	foreach($sql_query as $row)
	{
		$po_country_array[$row[csf('country_id')]]=$country_arr[$row[csf('country_id')]];
	}

	?>
    <fieldset style="width:820px">
        <legend>Bundle No and RMG qty details</legend>
        <table cellpadding="0" cellspacing="0" width="830" rules="all" border="1" class="rpt_table" id="tbl_bundle_list_save">
            <thead class="form_table_header">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Bundle</th>
                <th></th>
                <th colspan="2">RMG Number</th>
                <th>
                    <input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />  
                    <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $dtls_id; ?>" />
                </th>
                <th>Report &nbsp;</th>
            </thead>
            <thead class="form_table_header">
                <th>SL No</th>
                <th>Country Type</th>
                <th>Country Name</th>
                <th>Size</th>
                <th>Pattern</th>
                <th>Roll No</th>
                <th>Bundle No</th>
                <th>Quantity</th>
                <th>From</th>
                <th>To</th>
                <th></th>
                <th width="40"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
            </thead>
            <tbody id="trBundleListSave">
            <?
            $sql_size_name=sql_select("select size_id from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."");
            $size_colour_arr=array();
            foreach($sql_size_name as $asf)
            {
                $size_colour_arr[$asf[csf("size_id")]]=$size_arr[$asf[csf("size_id")]];	
            }

            $bundle_data=sql_select("select a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value, a.country_type, a.country_id, a.roll_no, a.roll_id, a.pattern_no,a.barcode_no, a.is_excess from ppl_cut_lay_bundle a where a.mst_id=".$mst_id." and a.dtls_id=".$dtls_id." order by a.id ASC");
            $i=1; 
            foreach($bundle_data as $row)
            { 
                $update_f_value=""; 
                if(str_replace("'","",$row[csf('update_flag')])==1)
                {
                    $update_f_value=explode("**",$row[csf('update_value')]);
                }
            ?>
                <tr id="trBundleListSave_<? echo $i;  ?>">
                    <td align="center"  id="">
                        <input type="text" id="sirialNo_<? echo $i;  ?>" name="sirialNo[]" style="width:25px;" class="text_boxes"  value="<? echo $i;  ?>"  disabled/>
                        <input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]"  value="<? echo $i;  ?>"/>                   
                        <input type="hidden" id="hiddenUpdateFlag_<? echo $i; ?>" name="hiddenUpdateFlag[]"  value="<? echo $row[csf('update_flag')];?> "/>
                        <input type="hidden" id="hiddenUpdateValue_<? echo $i; ?>" name="hiddenUpdateValue[]"  value="<? echo $row[csf('update_value')];?> " />
                    </td>
                    <td align="center">	
                        <?
                            echo create_drop_down( "cboCountryTypeB_".$i, 80, $country_type,'', 0, '',$row[csf('country_type')],'',1); 
                        ?>                                          
                        <input type="hidden" id="hiddenCountryTypeB_<? echo $i;  ?>" name="hiddenCountryTypeB[]"  value="<? echo $row[csf('country_type')];?> "/> 
                    </td> 
                    <td align="center">	
                        <?
							 echo create_drop_down("cboCountryB_".$i,80,$po_country_array,'',1,'',$row[csf('country_id')],'',1,'','','','','','','cboCountryB[]');
                        ?> 
                        <input type="hidden" id="hiddenCountryB_<? echo $i;  ?>" name="hiddenCountryB[]"  value="<? echo $row[csf('country_id')];?> " />
                    </td>
                    <td align="center" id="update_sizename_<? echo $i;  ?>">
                        <select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:60px; text-align:center;  <? if($update_f_value[1]!="") echo "background-color:#F3F;"; ?> "  disabled  >
                        <?
                        // $l=1;
                        foreach($sql_size_name as $asf)
                        {
                            if($asf[csf("size_id")]==$row[csf('size_id')]) $select_text="selected"; else $select_text="";
                            ?>	
                            <option value="<? echo $asf[csf("size_id")]; ?> " <? echo $select_text;  ?>><? echo $size_arr[$asf[csf("size_id")]]; ?> </option>
                            <?
                            }
                        ?>          
                        </select>
                        <input type="hidden" name="hiddenSizeId[]" id="hiddenSizeId_<? echo $i;  ?>" value="<? echo $row[csf('size_id')];  ?>" />
                    </td>
                    <td align="center"><input type="text" name="patternNo[]" id="patternNo_<? echo $i; ?>" value="<? echo $row[csf('pattern_no')]; ?>" class="text_boxes" style="width:35px; text-align:center" disabled/><input type="hidden" name="isExcess[]" id="isExcess_<? echo $i; ?>" value="<? echo $row[csf('is_excess')]; ?>"/></td>
                    <td align="center">
                    	<input type="text" name="rollNo[]" id="rollNo_<? echo $i;  ?>" value="<? echo $row[csf('roll_no')];  ?>" class="text_boxes"  style="width:50px;  text-align:center" disabled/>
                        <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                    </td>
                    <td align="center">
                    	<input type="text" name="bundleNo[]" id="bundleNo_<? echo $i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes"  style="width:105px;  text-align:center" title="<?php echo $row[csf('barcode_no')]; ?>" disabled/>
                    </td>
                    <td align="center">
                        <input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onKeyUp="bundle_calclution(<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right; <? if($update_f_value[0]!="") echo "background-color:#F3F;"; ?>"  class="text_boxes"  disabled/>
                        <input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<? echo $i;  ?>" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right" class="text_boxes"  disabled/>
                    </td>
                    <td align="center">
                    	<input type="text" name="rmgNoStart[]" id="rmgNoStart_<? echo $i;  ?>" value="<? echo $row[csf('number_start')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled />
                    </td>
                    <td align="center">
                    	<input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<? echo $i;  ?>" value="<? echo $row[csf('number_end')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled/>
                    </td>
                    <td align="center">
                        <input type="button" value="+" name="addButton[]" id="addButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<? echo $i;  ?>','<? echo $i;  ?>')"/>
                        <input type="button" value="Adj." name="rowUpdate[]" id="rowUpdate_<? echo $i;  ?>" style="width:40px;" class="formbuttonplasminus" onClick="fnc_updateRow('<? echo $i;  ?>')"/>
                    </td>
                    <td align="center">
                        <input id="chk_bundle_<? echo $i;  ?>" type="checkbox" name="chk_bundle" >
                        <input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid_<? echo $i; ?>" value="<? echo $row[csf('id')];  ?>" style="width:15px;" class="text_boxes"/>
                    </td>
                </tr>
            <?
            $i++;
            }
            ?>
            </tbody>
        </table>
        <table cellpadding="0" cellspacing="0" width="700">
            <tr>
                <td colspan="10" align="center" class="button_container">
                    <? echo load_submit_buttons($permission, "fnc_cut_lay_bundle_info", 1,0,"clear_size_form()",1);?>
                </td>
            </tr>
        </table>
    </fieldset>
    <?
	exit();
}

if($action=="cut_lay_bundle_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);

	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name" );
	
	$company_library=array(); $company_short_arr=array();
	$comapny_data=sql_select("select id, company_short_name, company_name from lib_company");
	foreach($comapny_data as $comR)
	{
		$company_library[$comR[csf('id')]]=$comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]]=$comR[csf('company_short_name')];
	}

	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//$po_number_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	
	$sql="select a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width ,a.fabric_width, a.gsm, b.order_id,b.color_id,b.order_cut_no,b.gmt_item_id,b.plies,b.marker_qty ,b.order_qty,b.batch_id from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";
	//echo $sql; die;
	//print_r($table_no_library);die;
	$dataArray=sql_select($sql);
	//echo print_r($dataArray); die;
	$sql_buyer=sql_select("select buyer_name from wo_po_details_master where job_no='".$dataArray[0][csf('job_no')]."' and company_name=$data[0]");
	$cut_no_prifix=$dataArray[0][csf('cut_num_prefix_no')];
	$order_cut_no=$dataArray[0][csf('order_cut_no')];
	//$cut_no=$company_short_arr[$data[0]]."-".$cut_no_prifix.'/'.$order_cut_no;
	$cut_no=$dataArray[0][csf('cutting_no')];
	
	$poData=sql_select( "select a.style_ref_no, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='".$dataArray[0][csf('order_id')]."'" );
	$batch_no=return_field_value("batch_no","pro_batch_create_mst","id='".$dataArray[0][csf('batch_id')]."'");
?>
<div style="width:1200px; ">
    <table width="1000" cellspacing="0" align="center">
         <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Lay and Bundle Information</u></strong></td>
        </tr>
         <tr>
        	<td width="120"><strong>Cut No:</strong></td><td width="180"><? echo $cut_no; ?></td>
            <td width="120"><strong>Table No :</strong></td> <td width="280"><?  echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
            <td width="120"><strong>Job No :</strong></td> <td width="180"><? echo $dataArray[0][csf('job_no')]; ?></td>
        </tr>
         <tr>
			<td><strong>Buyer:</strong></td> <td width="160"><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
            <td><strong>Order No :</strong></td> <td width="160"><? echo $poData[0][csf('po_number')]; ?></td>
             <td><strong>Order qty:</strong></td> <td width="160"><? echo $dataArray[0][csf('order_qty')]; ?></td>
        </tr>
        <tr>
			<td><strong>Batch No:</strong></td> <td width="160"><? echo $batch_no; ?></td>
            <td><strong>Style No :</strong></td> <td width="160"><? echo $poData[0][csf('style_ref_no')]; ?></td>
            <td><strong>File No:</strong></td> <td width="160"><? echo $poData[0][csf('file_no')]; ?></td>
        </tr>
        <tr>
			 <td><strong>Gmt Item:</strong></td> <td width="160"><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
             <td><strong>Color :</strong></td><td width="160"><? echo$color_library[$dataArray[0][csf('color_id')]]; ?></td>
             <td><strong>Marker Length :</strong></td><td width="160"><? echo $dataArray[0][csf('marker_length')]; ?></td>
        </tr>
        <tr>
             <td><strong>Marker Width :</strong></td><td width="160"><? echo $dataArray[0][csf('marker_width')]; ?></td>
            <td><strong>Fabric Width:</strong></td><td width="160"><? echo $dataArray[0][csf('fabric_width')]; ?></td>
              <td><strong>Gsm:</strong></td> <td width="160"><? echo $dataArray[0][csf('gsm')]; ?></td>
        </tr>
        <tr  height="">
              <td><strong>Plies:</strong></td> <td width="160"><? echo $dataArray[0][csf('plies')]; ?></td>
             <td><strong>Cut Date:</strong></td><td width="160"><? echo $dataArray[0][csf('entry_date')]; ?></td>
             <td  align="left" colspan="2" id="barcode_img_id"></td>
        </tr>
        <tr  height="">
              <td><strong>Order Cut No:</strong></td> <td width="160"><? echo $dataArray[0][csf('order_cut_no')]; ?></td>
            
        </tr>
    </table>
        <br>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

			function generateBarcode( valuess ){
				   
					var value = valuess;
					var btype = 'code39';
					var renderer ='bmp';
					var settings = {
					  output:renderer,
					  bgColor: '#FFFFFF',
					  color: '#000000',
					  barWidth: 1,
					  barHeight: 30,
					  moduleSize:5,
					  posX: 10,
					  posY: 20,
					  addQuietZone: 1
					};
					 value = {code:value, rect: false};
					$("#barcode_img_id").show().barcode(value, btype, settings);
				} 
			   generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
	 </script>
	<div style="width:1200px;">
    	<table align="center" cellspacing="0" width="1110" border="1" rules="all" class="rpt_table" >
              <thead bgcolor="#dddddd" align="center">
              		<tr>
                      <th></th>
                      <th colspan="5"></th>
                      <th>Bundle</th>
                      <th colspan="2">RMG Number</th>
                      <th colspan="3">QC</th>
                      <th></th>
                  </tr>
                  <tr>
                      <th width="50">SL No</th>
                      <th width="80">Cut No</th>
                      <th>Country Name</th>
                      <th width="60">Pattern No</th>
                      <th width="60">Roll No</th>
                      <th width="70">Bundle No</th>
                      <th width="70">Quantity</th>
                      <th width="70">From</th>
                      <th width="70">To</th>
                      <th width="60">Size</th>
                       <th width="50">REJ</th>
                      <th width="50">REP</th>
                      <th width="160">Remarks</th>
                	</tr>
                </thead>
                
                <tbody> 
                <?  
					 if($data[4]==0) $country_cond=""; else $country_cond=" and a.country_id='".$data[4]."'";
                     $size_data=sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");    
                     $j=1;
                     $country_size_arr =array();
                     $all_size_arr =array();
                     foreach($size_data as $size_val)
                     {
						$total_marker_qty_size=0;
                       	$bundle_data=sql_select("select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.pattern_no, a.is_excess from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC");
                        foreach($bundle_data as $row)
                        {
							if($row[csf('size_qty')]>0) 
							{
							   $bundle_prifix=explode('-',$row[csf('bundle_no')]); 

							   $country_size_arr[$row[csf('country_id')]][$row[csf('size_id')]]['size_id'] = $row[csf('size_id')];

							   $country_size_arr[$row[csf('country_id')]][$row[csf('size_id')]]['size_qty'] += $row[csf('size_qty')];

							   $all_size_arr[] = $row[csf('size_id')];
							?>
							   <tr>
								   <td align="center"><? echo $j;  ?></td>
								   <td align="center"><? echo $cut_no; ?></td>
								   <td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
								   <!-- if($row[csf('is_excess')]==1) echo "EXCESS"; else -->
								   <td align="center"><? echo $row[csf('pattern_no')]; ?></td>
								   <td align="center"><? echo $row[csf('roll_no')]; ?></td>
								   <td align="center"><?  echo $row[csf('bundle_num_prefix_no')]; //$bundle_prifix[2];  ?></td>
								   <td align="center"><? echo $row[csf('size_qty')];  ?></td>
								   <td align="center"><? echo $row[csf('number_start')];  ?></td>
								   <td align="center"><? echo $row[csf('number_end')];  ?></td>
								   <td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
                                   <td align="center"></td>
                                 	<td align="center"></td>
                               		<td align="center"></td>
								</tr>
							<?
							   $j++;
							   $total_marker_qty_size+=$row[csf('size_qty')];
							   $total_marker_qty+=$row[csf('size_qty')];
							}
                         }
                       //  $total_marker_qty+=$size_val[csf('marker_qty')];
                ?>		
                        
                        <tr bgcolor="#eeeeee">
                           <td align="center"></td>
                           <td  colspan="5" align="right"><? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
                           <td align="center"><? echo $total_marker_qty_size;  ?></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                        </tr>			  
                <?	  
                     }
                ?>
                
                <tr bgcolor="#BBBBBB">
                   <td align="center"></td>
                   <td  colspan="5"  align="right"> Total marker qty.</td>
                   <td align="center"><? echo $total_marker_qty;  ?></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                </tr>
			</tbody>
		</table>
		<br>
		<?php
			$all_size_arr = array_unique($all_size_arr);
			$total_size = count($all_size_arr);
		?>
		<table align="left" style="margin-left: 45px;" cellspacing="0" width="<?php echo 300+100*$total_size;?>" border="1" rules="all" class="rpt_table" >
          	<thead bgcolor="#dddddd" align="center">
          		<tr>
                  <th colspan="<?php echo 3+$total_size;?>" align="left">Country Break Down</th>
              	</tr>
              	<tr>
                  <th width="50">SL No</th>
                  <th width="150">Country Name</th>
                  <?php
                  	foreach($all_size_arr as $size)
                  	{
                  		?>
                  		<th><?php echo $size_arr[$size]; ?></th>
                  		<?php
                  	}
                  ?>
                  <th width="80">Total</th>
            	</tr>
            </thead>
            <tbody>
				<?php
            		$i=1;
            		$grand_total_qty = 0;
                  	foreach($country_size_arr as $country=> $country_data)
                  	{
                  		?>
						<tr>
							<td width="50"><?php echo $i; ?></td>
							<td width="150"><?php echo $country_arr[$country]; ?></td>
							<?php
								$size_total =0;
								foreach($all_size_arr as $size)
								{
									?>
									<td align="right" width="60">
										<?php
											foreach($country_data as $size_id=>$data)
											{
											
												if($country_size_arr[$country][$size_id]['size_id']==$size)
												{
													echo $country_size_arr[$country][$size_id]['size_qty'];

													$size_total += $country_size_arr[$country][$size_id]['size_qty'];
												}
											}
										?>
									</td>
									<?php
								}
							?>
							<td align="right"><?php echo $size_total;?></td>
						</tr>		
						<?php
						$grand_total_qty +=$size_total;
						$i++;
                  	}
                ?>
            </tbody>
            <tfoot>
            	<td align="right" colspan="<?php echo 2+$total_size;?>"><strong>Grand Total:</strong></td>
            	<td align="right"><strong><?php echo $grand_total_qty;?></strong></td>
            </tfoot>
        </table>
              
        <br>
		<? echo signature_table(9, $data[0], "1100px"); ?>
		</div>
	</div> 
	<?
	exit(); 
}

if($action=="job_search_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	
?>
	<script>
		function js_set_order(strCon ) 
		{
		document.getElementById('hidden_job_no').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="1090" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="150">Company name</th>
                    <th width="150">Buyer name</th>
                    <th width="70">Job No</th>
                    <th width="70">Style Ref</th>
                    <th width="120">Order No</th>
                     <th width="100">File No</th>
                    <th width="100">Internal Ref. No</th>
                    <th width="220">Date Range</th>
                    <th width=""><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>                    
                    <td>
                          <? 
                               echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",1);
                         ?>
                    </td>
                    <td align="center" width="150">
                             <?  
							   $sql="select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$cbo_company_id order by a.buyer_name";
							echo create_drop_down( "cbo_buyer_name", 140,$sql,"id,buyer_name", 1, "-- Select --", 0, "", 0,"5,6,7","","","" );
                            ?>
                            <input type="hidden" id="hidden_job_qty" name="hidden_job_qty" />
                            <input type="hidden" id="hidden_sip_date" name="hidden_sip_date" />
                            <input type="hidden" id="hidden_prifix" name="hidden_prifix" />
                            <input type="hidden" id="hidden_job_no" name="hidden_job_no" />
                    </td>
                    <td width="100">
                          <input style="width:70px;" type="text"  class="text_boxes"   name="txt_job_prifix" id="txt_job_prifix"  />
                    </td>
                      <td width="100">
                          <input style="width:70px;" type="text"  class="text_boxes"   name="txt_style" id="txt_style"  />
                    </td>
                    <td width="100">
                          <input style="width:100px;" type="text"  class="text_boxes"   name="txt_po_no" id="txt_po_no"  />
                    </td>
                    <td width="100">
                          <input style="width:80px;" type="text"  class="text_boxes"   name="txt_file_no" id="txt_file_no"  />
                    </td>
                    <td width="100">
                          <input style="width:100px;" type="text"  class="text_boxes"   name="txt_internal_ref" id="txt_internal_ref"  />
                    </td> 
                    <td align="center">
                           <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                           <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td align="center">
                         <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style').value, 'create_job_search_list_view', 'search_div', 'cut_and_lay_gmts_no_wise_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />				
                    </td>
            </tr>
        		<tr>                  
            	<td align="center" height="40" valign="middle" colspan="8">
					<? echo load_month_buttons(1);  ?>
                </td>
            </tr>   
            </tbody>
         </tr>         
        </table> 
          <div align="center" valign="top" id="search_div"> </div>  
        </form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
if($action=="create_job_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$buyer = $ex_data[1];
	$from_date = $ex_data[2];
	$to_date = $ex_data[3];
	$job_prifix= $ex_data[4];
	$job_year = $ex_data[5];
	$po_no = $ex_data[6];
	$file_no = $ex_data[7];
	$internal_reff = $ex_data[8];
	$style_ref = $ex_data[9];
	$job_cond="";
	
	if(str_replace("'","",$company)=="") $conpany_cond=""; else $conpany_cond="and b.company_name=".str_replace("'","",$company)."";
	if(str_replace("'","",$buyer)==0) $buyer_cond=""; else $buyer_cond="and b.buyer_name=".str_replace("'","",$buyer)."";
    if($db_type==2) $year_cond=" and extract(year from b.insert_date)=$job_year";
    if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$job_year";
	if(str_replace("'","",$job_prifix)!="")  $job_cond="and b.job_no_prefix_num=".str_replace("'","",$job_prifix)."  $year_cond";
	if(str_replace("'","",$po_no)!="")  $order_cond="and a.po_number like '%".str_replace("'","",$po_no)."%' "; else $order_cond="";
	
	if(str_replace("'","",$file_no)!="")  $file_cond="and a.file_no like '%".str_replace("'","",$file_no)."%' "; else $file_cond="";
	if(str_replace("'","",$internal_reff)!="")  $internal_reff_cond=" and a.grouping like '%".str_replace("'","",$internal_reff)."%' "; else $internal_reff_cond="";
		if(str_replace("'","",$style_ref)!="")  $style_ref_cond="and b.style_ref_no like '%".str_replace("'","",$style_ref)."%' "; else $style_ref_cond="";
	
	if($db_type==0)
	{
		if( $from_date!="" && $to_date!="" ) $sql_cond = " and a.pub_shipment_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	$sql_order="select b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,SUBSTRING_INDEX(b.insert_date, '-', 1) as year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_ref_cond group by b.buyer_name,b.job_no,a.po_number ";  
	}
	
	if($db_type==2)
	{
	 if( str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="" ) 
	  {
		  $sql_cond = " and a.pub_shipment_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	  }
	
	 
	$sql_order="select b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,extract(year from b.insert_date) as year,a.file_no,a.grouping from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_ref_cond  group by  b.job_no,b.buyer_name, a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num, b.insert_date,a.file_no,a.grouping order by  job_no_prefix_num";  
	}
	//echo $sql_order;
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$arr=array (3=>$buyer_arr);
	echo create_list_view("list_view", "Job NO,Year,Style Ref,Buyer Name,File No,Internal Ref. No, Order No,Shipment Date","60,60,150,150,100,100,150,100","1000","270",0, $sql_order , "js_set_order", "job_no,buyer_name,year,style_ref_no", "", 1, "0,0,0,buyer_name,0,0,0,0,0", $arr, "job_prefix,year,style_ref_no,buyer_name,file_no,grouping,po_number,pub_shipment_date", "","setFilterGrid('list_view',-1)") ;	
	
}
//master data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$sql_table=return_field_value("id","lib_cutting_table","company_id =$cbo_working_company_name AND location_id =$cbo_location_name AND floor_id =$cbo_floor_name AND table_no = $txt_table_no");
	   if($sql_table!="")
	   {
		   $tbl_id=$sql_table;
	   }
	   else
	   {
			$tbl_id=return_next_id("id", "lib_cutting_table", 1);
			$field_array_table="id,table_no,company_id,location_id,floor_id,inserted_by,insert_date,status_active,is_deleted";
			$data_array_table="(".$tbl_id.",".$txt_table_no.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_floor_name.",'".$user_id."','".$pc_date_time."',1,0)";
		//$rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);	
	   }
	   
		if(str_replace("'","",$update_id)=="")
		{
			$job_prifix=return_field_value("job_no_prefix_num","wo_po_details_master","job_no=$txt_job_no");
			//$sql_prifix=sql_select( "SELECT DISTINCT cut_num_prefix_no FROM ppl_cut_lay_mst WHERE company_id=".$cbo_company_name." and extract(year from insert_date)=".date('Y',time())."  ORDER BY cut_num_prefix_no DESC "); //and entry_form=97

			$new_sys_number = explode("*", return_next_id_by_sequence("", "ppl_cut_lay_mst",$con,1,$cbo_company_name,'',0,date("Y",time()),0,0,0,0,0 ));
			$cut_no_prifix[]=$new_sys_number[2];

			/*if( count($sql_prifix)>0)
			{
				$cut_no_prifix[]=$sql_prifix[0][csf('cut_num_prefix_no')];
			}
			else
			{
				 $cut_no_prifix[0]=0;	
			}
			$cut_no_prifix[0]+=1;*/
			$comp_prefix=return_field_value("company_short_name","lib_company", "id=$cbo_company_name");
			$cut_no=str_pad((int) $cut_no_prifix[0],6,"0",STR_PAD_LEFT);
			
			$year_id=date('Y',time());
			if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
			
			//$new_cutting_number=$comp_prefix."-".$cut_no;
			//$new_cutting_prifix=$comp_prefix."-".$job_prifix;
			/*$new_cutting_number=$comp_prefix."-".$year_id."-".$cut_no;
			$new_cutting_prifix=$comp_prefix."-".$year_id."-";*/
			$new_cutting_number=str_replace("--", "-",$new_sys_number[1]).$cut_no;
			$new_cutting_prifix=str_replace("--", "-",$new_sys_number[1]);

			//$id=return_next_id("id", "ppl_cut_lay_mst", 1);	location_id
			$id= return_next_id_by_sequence(  "ppl_cut_lay_mst_seq",  "ppl_cut_lay_mst", $con );
			$field_array="id,entry_form,cut_num_prefix,cut_num_prefix_no,cutting_no,table_no,job_no,batch_id,company_id,floor_id,working_company_id,entry_date,start_time,end_date,end_time,marker_length,marker_width,fabric_width,gsm,width_dia,cad_marker_cons,inserted_by,insert_date,status_active,is_deleted,location_id,remarks";
			$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
			$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
			$data_array="(".$id.",97,'".$new_cutting_prifix."',".$cut_no_prifix[0].",'".$new_cutting_number."',".$tbl_id.",".$txt_job_no.",".$txt_batch_no.",".$cbo_company_name.",".$cbo_floor_name.",".$cbo_working_company_name.",".$txt_entry_date.",'".$start_time."',".$txt_end_date.",'".$end_time."',".$txt_marker_length.",".$txt_marker_width.",".$txt_fabric_width.",".$txt_gsm.",".$cbo_width_dia.",".$txt_marker_cons.",'".$user_id."','".$pc_date_time."',1,0,".$cbo_location_name.",".$txt_remark.")";
			//$rID1=sql_insert(" ppl_cut_lay_mst",$field_array,$data_array,0); 
		}
		else
		{
			$field_array="table_no*job_no*batch_id*floor_id*working_company_id*entry_date*start_time*entry_date*end_time*marker_length*marker_width*fabric_width*gsm*width_dia*cad_marker_cons*updated_by*update_date*location_id*remarks";
			$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
			$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
			$data_array="".$update_tbl_id."*".$txt_job_no."*".$txt_batch_no."*".$cbo_floor_name."*".$cbo_working_company_name."*".$txt_entry_date."*'".$start_time."'*,".$txt_end_date."*'".$end_time."'*".$txt_marker_length."*".$txt_marker_width."*".$txt_fabric_width."*".$txt_gsm."*".$cbo_width_dia."*".$txt_marker_cons."*'".$user_id."'*'".$pc_date_time."'*".$pc_date_time."'*".$txt_remark."";
			//$rID1=sql_update("ppl_cut_lay_mst",$field_array,$data_array,"id",$update_id,0); 
		}
			
		//$detls_id=return_next_id("id", "ppl_cut_lay_dtls", 1);
		$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
		$field_array1="id,mst_id,order_id,order_ids,color_type_id,order_cut_no,ship_date,color_id,batch_id,gmt_item_id,plies,order_qty,roll_data,country_ids,iscountry_seq,inserted_by,insert_date,status_active,is_deleted";
		$field_array_up="order_id*order_ids*color_type_id*order_cut_no*ship_date*color_id*batch_id*gmt_item_id*plies*order_qty*roll_data*country_ids*iscountry_seq*updated_by*update_date";
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id,po_ids, entry_form, qnty, roll_id, roll_no, plies, inserted_by, insert_date";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$add_comma=0;
		
		//$order_cut_no_arr=return_library_array( "select order_id,max(order_cut_no) as order_cut_no from ppl_cut_lay_dtls group by order_id", "order_id", "order_cut_no"  );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id,po_ids, entry_form, qnty, roll_id, roll_no, plies, inserted_by, insert_date";
		$field_array_country_seq="id, mst_id, dtls_id, country_id, sequence_no, inserted_by, insert_date, status_active, is_deleted";
		$dataarr_country_seq="";
		for($i=1; $i<=$row_num; $i++)
		{
				$cbo_order_id="cboorderno_".$i;
				$txt_ship_date="txtshipdate_".$i;
				$cbocolor="cbocolor_".$i;
				$cbo_gmt_id="cbogmtsitem_".$i;
				$order_qty="txtorderqty_".$i;
				$txt_plics="txtplics_".$i;
				$update_details_id="updateDetails_".$i;
				$order_cut_no="orderCutNo_".$i;
				$rollData="rollData_".$i;
				$cbobatch="cbobatch_".$i;
				$cboColorType="cboColorType_".$i;
				$CountryIds="countryId_".$i;
				$hiddiscountryseq="hiddiscountryseq_".$i;
				//$order_cut_no=$order_cut_no_arr[str_replace("'","",$$cbo_order_id)]+1;
				//$order_cut_no_arr[str_replace("'","",$$cbo_order_id)]=$order_cut_no;
				
				if(str_replace("'","",$update_id)!="")
				{
					$master_id=$update_id;
				}
				else
				{
					 $master_id=$id;  
				}
				
				if(str_replace("'",'',$$update_details_id)!="")    
				{ 
					$dtlsId=str_replace("'",'',$$update_details_id); 
				}
				else
				{
					$dtlsId=$detls_id; 
				}
				
				$save_string=explode("**",str_replace("'",'',$$rollData)); $response_data='';
				for($x=0;$x<count($save_string);$x++)
				{
					$roll_dtls=explode("=",$save_string[$x]);
					$barcode_no=$roll_dtls[0];
					$roll_no=$roll_dtls[1];
					$roll_id=$roll_dtls[2];
					$roll_qnty=$roll_dtls[3];
					$plies=$roll_dtls[4];
					$batch_No_plies=$roll_dtls[5];
					$extra_fabric=$roll_dtls[6];
					$reject_fabric=$roll_dtls[7];

					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					if(str_replace("'",'',$$roll_maintained)!=1) $roll_id=$id_roll;
					
					if($data_array_roll!="") $data_array_roll.= ",";
					$data_array_roll.="(".$id_roll.",".$barcode_no.",".$master_id.",".$dtlsId.",".$$cbo_order_id.",".$$cbo_order_id.",97,'".$roll_qnty."','".$roll_id."','".$roll_no."','".$plies."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$response_data.=$barcode_no."=".$roll_no."=".$roll_id."=".$roll_qnty."=".$plies."=".$batch_No_plies."=".$extra_fabric."=".$reject_fabric."**";
					//$id_roll = $id_roll+1;
				}
				
				$response_data=substr($response_data,0,-2);
				
			
				if(str_replace("'",'',$$update_details_id)!="")  
				{
					$updateID_array[]=str_replace("'",'',$$update_details_id); 
					$data_array_up[str_replace("'",'',$$update_details_id)]=explode("*",("".$$cbo_order_id."*".$$cbo_order_id."*".$$cboColorType."*".$$cboColorType."*".$$txt_ship_date."*".$$cbocolor."*".$$cbobatch."*".$$cbo_gmt_id."*".$$txt_plics."*".$$order_qty."*'".$response_data."'*".$$CountryIds."'*".$$hiddiscountryseq."*'".$user_id."'*'".$pc_date_time."'*1*0"));
					$dId=str_replace("'",'',$$update_details_id); 
				}
				else
				{
				   if ($add_comma!=0) $data_array1 .=",";
				   if ($add_comma!=0) $detls_id_array .="_";
					$data_array1.="(".$detls_id.",".$master_id.",".$$cbo_order_id.",".$$cbo_order_id.",".$$cboColorType.",".$$order_cut_no.",".$$txt_ship_date.",".$$cbocolor.",".$$cbobatch.",".$$cbo_gmt_id.",".$$txt_plics.",".$$order_qty.",'".$response_data."',".$$CountryIds.",".$$hiddiscountryseq.",'".$user_id."','".$pc_date_time."',1,0)";   
					$detls_id_array.=$detls_id."#".str_replace("'",'',$$order_cut_no);
					//$detls_id_array.=$detls_id."#".str_replace("'",$$order_cut_no);
					//$dtlsId=$detls_id; 
					//$detls_id=$detls_id+1;
					$dId=$detls_id;
					$detls_id= return_next_id_by_sequence("ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
					$add_comma++;
				}
				
				if(str_replace("'",'',$$hiddiscountryseq)==1)
				{
					$cosuntrydata=array_filter(explode(",",str_replace("'",'',$$CountryIds)));
					foreach($cosuntrydata as $cdata)
					{
						$excountryseq=array_filter(explode("!",$cdata));
						$cseqdtls_id= return_next_id_by_sequence("ppl_cut_lay_country_seq_dtls_SEQ",  "ppl_cut_lay_country_seq_dtls", $con );
						if($dataarr_country_seq!="") $dataarr_country_seq.= ",";
						$dataarr_country_seq.="(".$cseqdtls_id.",".$master_id.",".$dId.",'".$excountryseq[0]."','".$excountryseq[1]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					}
				}
			}
				
			//$detls_id_update.=implode("_",$updateID_array);
			$detls_id_update.=$detls_id_array; $rID=true; $rID2=true; $rID3=true; $rID4=true; $rID5=true; $rIDydel=true;
		    if($sql_table=="") 
		    { 
				$rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);	 
			}
			
			if(str_replace("'","",$update_id)=="")
			{
				$rID1=sql_insert("ppl_cut_lay_mst",$field_array,$data_array,0);    
			}
			else
			{
				$rID1=sql_update("ppl_cut_lay_mst",$field_array,$data_array,"id",$update_id,0);   
			}
			
			if(count($updateID_array)>0)
			{
				$rID2=execute_query(bulk_update_sql_statement("ppl_cut_lay_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);
			}
			
			if($data_array1!="")
			{
			   $rID3=sql_insert("ppl_cut_lay_dtls",$field_array1,$data_array1,1); 
			}
			
			if($data_array_roll!="")
			{
				$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			}
			
			
			if($dataarr_country_seq!="")
			{
				$rIDydel=execute_query( "update ppl_cut_lay_country_seq_dtls set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id=".$master_id." and dtls_id=".implode(",",$updateID_array)." and status_active=1 and is_deleted=0",1);
				$rID5=sql_insert("ppl_cut_lay_country_seq_dtls",$field_array_country_seq,$dataarr_country_seq,0);
			}
			
			//echo "10**insert into ppl_cut_lay_mst( $field_array) values".$data_array;die;
			//echo "10**".$rID ."**". $rID1 ."**". $rID2 ."**". $rID3 ."**". $rID4;die;
			//
			//echo "10**".$rID2;die;
	      	if($db_type==0)
			{
			  	if(str_replace("'","",$update_id)=="")
				{
					if($rID && $rID1 && $rID2 && $rID3 && $rID4 && $rID5)
					{
						mysql_query("COMMIT");  
						echo "0**".str_replace("'","",$id)."**".$new_cutting_number."**".str_replace("'","",$tbl_id)."**".$detls_id_array;
					}
				   	else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
			   	if(str_replace("'","",$update_id)!="")
				{
					if($rID && $rID1 && $rID2 && $rID3 && $rID4 && $rID5)
					{
						mysql_query("COMMIT");  
						echo "0**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_cutting_no)."**".str_replace("'","",$tbl_id)."**".$detls_id_update;
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
			 }
			else if($db_type==2 || $db_type==1 )
			{
				if(str_replace("'","",$update_id)=="")
				{
					if($rID && $rID1 && $rID2 && $rID3 && $rID4 && $rID5)
					{
						oci_commit($con);   
						echo "0**".str_replace("'","",$id)."**".$new_cutting_number."**".str_replace("'","",$tbl_id)."**".$detls_id_array;
					}
				   	else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				if(str_replace("'","",$update_id)!="")
				{
					 if($rID && $rID1 && $rID2 && $rID3 && $rID4 && $rID5)
					{
						oci_commit($con);  
						echo "0**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_cutting_no)."**".str_replace("'","",$tbl_id)."**".$detls_id_update;
					}
					else
					{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
			}
			disconnect($con);
			die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		// var_dump($_REQUEST);
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

		//==============================================================
							//Order Mixed Restricted Start
		// ==============================================================
		if(str_replace("'","",$update_id)!="")
		{
			$order_sql = "select order_id from ppl_cut_lay_bundle where mst_id in ($update_id) and status_active=1 and is_deleted=0";
			$order_id_array = return_library_array($order_sql,'order_id','order_id');
			/* echo "<pre>";
			print_r($order_id_array); die; */ 
			// echo $row_num .'**'; die; 
			for($i=1; $i<=$row_num; $i++)
			{
				$cbo_order_id="cboorderno_".$i;
				$order_id = str_replace("'","",$$cbo_order_id) ;  

				if ( !in_array($order_id,$order_id_array ) && count($order_id_array)>0) { 
					echo "300**".$$cbo_order_id; disconnect($con);
					die;
				} 
			}
		}
		//==============================================================
							//Order Mixed Restricted End
		// ==============================================================

		$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","cutting_no=".$txt_cutting_no."");
		if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; disconnect($con); die;}
		$sql_table=return_field_value("id","lib_cutting_table","company_id =$cbo_working_company_name AND location_id =$cbo_location_name AND floor_id =$cbo_floor_name AND table_no = $txt_table_no");
		$rID=true; $rID2=true; $rID3=true; $rID4=true; $rID5=true; $rIDydel=true;
		if($sql_table!="")
		{
			 $tbl_id=$sql_table;
		}
		else
		{
			$tbl_id=return_next_id("id", "lib_cutting_table", 1);
			$field_array_table="id,table_no,company_id,location_id,floor_id,inserted_by,insert_date,status_active,is_deleted";
			$data_array_table="(".$tbl_id.",".$txt_table_no.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_floor_name.",'".$user_id."','".$pc_date_time."',1,0)";
			//echo "insert into  ppl_cut_lay_table_no($field_array_table) values".$data_array_table;
			//$rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);	
		}
		//master table update*********************************************************************
		$field_array="table_no*job_no*batch_id*floor_id*working_company_id*entry_date*start_time*end_date*end_time*marker_length*marker_width*fabric_width*gsm*width_dia*cad_marker_cons*updated_by*update_date*location_id*remarks";
		$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
		$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
		$data_array="".$tbl_id."*".$txt_job_no."*".$txt_batch_no."*".$cbo_floor_name."*".$cbo_working_company_name."*".$txt_entry_date."*'".$start_time."'*".$txt_end_date."*'".$end_time."'*".$txt_marker_length."*".$txt_marker_width."*".$txt_fabric_width."*".$txt_gsm."*".$cbo_width_dia."*".$txt_marker_cons."*'".$user_id."'*'".$pc_date_time."'*".$cbo_location_name."*".$txt_remark."";
		
		
		//$detls_id=return_next_id("id", " ppl_cut_lay_dtls", 1);
		$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
		$field_array1="id, mst_id,order_id,order_ids,color_type_id,order_cut_no,ship_date,color_id,batch_id,gmt_item_id,plies,order_qty,roll_data,country_ids,iscountry_seq,inserted_by,insert_date,status_active,is_deleted";
		$field_array_up="order_id*order_ids*color_type_id*order_cut_no*ship_date*color_id*batch_id*gmt_item_id*plies*order_qty*roll_data*country_ids*iscountry_seq*updated_by*update_date";
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_id, roll_no, plies, inserted_by, insert_date";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		
		$add_comma=0;
		
		$order_cut_no_arr=return_library_array( "select order_id,max(order_cut_no) as order_cut_no from ppl_cut_lay_dtls group by order_id", "order_id", "order_cut_no"  );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id,po_ids, entry_form, qnty, roll_id, roll_no, plies, inserted_by, insert_date";
		
		$field_array_country_seq="id, mst_id, dtls_id, country_id, sequence_no, inserted_by, insert_date, status_active, is_deleted";
		$dataarr_country_seq="";
		//echo "10**";
		for($i=1; $i<=$row_num; $i++)
		{
			$cbo_order_id="cboorderno_".$i;
			$orderCutNo="orderCutNo_".$i;
			$txt_ship_date="txtshipdate_".$i;
			$cbocolor="cbocolor_".$i;
			$cbo_gmt_id="cbogmtsitem_".$i;
			$order_qty="txtorderqty_".$i;
			$txt_plics="txtplics_".$i;
			$order_cut_no="orderCutNo_".$i;
			$update_details_id="updateDetails_".$i;
			$rollData="rollData_".$i;
			$cbobatch="cbobatch_".$i;
			$cboColorType="cboColorType_".$i;
			$country_ids="countryId_".$i;
			
			//echo $$country_ids.'==';
			$hiddiscountryseq="hiddiscountryseq_".$i;
			if(str_replace("'","",$update_id)!="") $msster_id=$update_id;
			else $msster_id=$id;  
			
			if(str_replace("'",'',$$update_details_id)!="") $dtlsId=str_replace("'",'',$$update_details_id);
			else $dtlsId=$detls_id; 
			
			$save_string=explode("**",str_replace("'",'',$$rollData)); $response_data='';
			for($x=0;$x<count($save_string);$x++)
			{
				$roll_dtls=explode("=",$save_string[$x]);
				$barcode_no=$roll_dtls[0];
				$roll_no=$roll_dtls[1];
				$roll_id=$roll_dtls[2];
				$roll_qnty=$roll_dtls[3];
				$plies=$roll_dtls[4];
				$batch_No_plies=$roll_dtls[5];
				$extra_fabric=$roll_dtls[6];
				$reject_fabric=$roll_dtls[7];
				
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
		//$new_grey_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'GPE',2,date("Y",time()),13 ));
				//$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),13 ));
				//$barcode_no = $barcode_year . "02" . str_pad($barcode_suffix_no[2], 7, "0", STR_PAD_LEFT);
				
				if(str_replace("'",'',$$roll_maintained)!=1) $roll_id=$id_roll;
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$barcode_no.",".$msster_id.",".$dtlsId.",".$$cbo_order_id.",".$$cbo_order_id.",97,'".$roll_qnty."','".$roll_id."','".$roll_no."','".$plies."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$response_data.=$barcode_no."=".$roll_no."=".$roll_id."=".$roll_qnty."=".$plies."=".$batch_No_plies."=".$extra_fabric."=".$reject_fabric."**";
				//$id_roll = $id_roll+1;
			}
			
			$response_data=substr($response_data,0,-2);
			
			
			if(str_replace("'",'',$$update_details_id)!="")  
			{
				$updateID_array[]=str_replace("'",'',$$update_details_id); 
				$data_array_up[str_replace("'",'',$$update_details_id)]=explode("_",("".$$cbo_order_id."_".$$cbo_order_id."_".$$cboColorType."_".$$order_cut_no."_".$$txt_ship_date."_".$$cbocolor."_".$$cbobatch."_".$$cbo_gmt_id."_".$$txt_plics."_".$$order_qty."_'".$response_data."'_".$$country_ids."_".$$hiddiscountryseq."_'".$user_id."'_'".$pc_date_time."'_1_0"));
				//$dtlsId=str_replace("'",'',$$update_details_id); 
				
				if ($add_comma!=0) $detls_id_array .="_";
				$detls_id_array.=str_replace("'",'',$$update_details_id)."#".str_replace("'",'',$$order_cut_no);
				$add_comma++;
				$dId=str_replace("'",'',$$update_details_id); 
			}
			 else
			{
			  //	$order_cut_no=$order_cut_no_arr[str_replace("'","",$$cbo_order_id)]+1;
				//$order_cut_no_arr[str_replace("'","",$$cbo_order_id)]=$order_cut_no;
				
				if ($add_comma!=0) $data_array1 .=",";
				if ($add_comma!=0) $detls_id_array .="_";
				$data_array1.="(".$detls_id.",".$msster_id.",".$$cbo_order_id.",".$$cbo_order_id.",".$$cboColorType.",".$$order_cut_no.",".$$txt_ship_date.",".$$cbocolor.",".$$cbobatch.",".$$cbo_gmt_id.",".$$txt_plics.",".$$order_qty.",'".$response_data."',".$$country_ids.",".$$hiddiscountryseq.",".$user_id.",'".$pc_date_time."',1,0)";   
				$detls_id_array.=$detls_id."#".str_replace("'",'',$$order_cut_no);
				//$dtlsId=$detls_id; 
				//$detls_id=$detls_id+1;
				$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
				$add_comma++;
				$dId=str_replace("'",'',$$update_details_id); 
			}
			
			if(str_replace("'",'',$$hiddiscountryseq)==1)
			{
				$cosuntrydata=array_filter(explode(",",str_replace("'",'',$$country_ids)));
				foreach($cosuntrydata as $cdata)
				{
					$excountryseq=array_filter(explode("!",$cdata));
					$cseqdtls_id= return_next_id_by_sequence("ppl_cut_lay_country_seq_dtls_SEQ",  "ppl_cut_lay_country_seq_dtls", $con );
					if($dataarr_country_seq!="") $dataarr_country_seq.= ",";
					$dataarr_country_seq.="(".$cseqdtls_id.",".$update_id.",'".$dId."','".$excountryseq[0]."','".$excountryseq[1]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				}
			}
		}
			//  echo "10**";
		//$detls_id_update.=implode("_",$updateID_array);
		//echo "10**insert into lib_cutting_table( $field_array_table) values".$data_array_table;die;
		if($sql_table=="")
		{
			$rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);	
		}
		
		$rID1=sql_update("ppl_cut_lay_mst",$field_array,$data_array,"id",$update_id,0);
		
		$detls_id_update.=$detls_id_array;
		if(count($updateID_array)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement("ppl_cut_lay_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);
		}
		//echo "10**".bulk_update_sql_statement("ppl_cut_lay_dtls","id",$field_array_up,$data_array_up,$updateID_array); die;
		
		if($data_array1!="")
		{
		   $rID3=sql_insert("ppl_cut_lay_dtls",$field_array1,$data_array1,1); 
		}
			
		$delete_roll=execute_query("delete from pro_roll_details where mst_id=$msster_id and entry_form=97",0);	
		if($data_array_roll!="")
		{
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		}
		if($dataarr_country_seq!="")
		{
			$rIDydel=execute_query( "update ppl_cut_lay_country_seq_dtls set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id=".$update_id." and dtls_id in (".implode(",",$updateID_array).") and status_active=1 and is_deleted=0",1);
			//echo "10**update ppl_cut_lay_country_seq_dtls set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id=".$update_id." and dtls_id=".implode(",",$updateID_array)." and status_active=1 and is_deleted=0";
			$rID5=sql_insert("ppl_cut_lay_country_seq_dtls",$field_array_country_seq,$dataarr_country_seq,0);
		}
		//echo "10**".$rID ."**". $rID1 ."**". $rID2 ."**". $rID3 ."**". $rID4."**". $rID5;die;
		//echo "10**insert into ppl_cut_lay_country_seq_dtls( $field_array_country_seq) values ".$dataarr_country_seq;die;	
		if($db_type==0)
		 {
			if($rID && $rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $delete_roll)
			   {
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_cutting_no)."**".str_replace("'","",$tbl_id)."**".$detls_id_update;
				}
			else
			   {
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_cutting_no);
			   }
		 }
		else if($db_type==2 || $db_type==1 )
		  {
				if($rID && $rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $delete_roll)
			   {
				oci_commit($con); 
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_cutting_no)."**".str_replace("'","",$tbl_id)."**".$detls_id_update;
			   }
			else
			   {
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_cutting_no);
			   }
		  }
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		
	}		
}

if($action=="cutting_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		function js_set_cutting_value(strCon ) 
		{
			
		document.getElementById('update_mst_id').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
 		<table width="1050" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="130">Company name</th>
                    <th width="100">Cutting No</th>
                    <th width="100">Job No</th>
                    <th width="100">Order No</th>
                    <th width="100">File No</th>
                    <th width="100">Ref. No</th>
                    <th width="250">Date Range</th>
                    <th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
				<tr class="general">                    
                    <td>
                         <? 
                         	echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1);
                         ?>
                    </td>
                    <td align="center" >
                        <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:100px"  class="text_boxes_numeric"/>
                        <input type="hidden" id="update_mst_id" name="update_mst_id" />
                    </td>
                    <td align="center">
                    	<input name="txt_job_search" id="txt_job_search" class="text_boxes_numeric" style="width:100px"  />
                    </td>
                    <td align="center">
                    	<input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:100px"  />
                    </td>
                    <td width="100">
                          <input style="width:80px;" type="text"  class="text_boxes"   name="txt_file_no" id="txt_file_no"  />
                    </td>
                    <td width="100">
                          <input style="width:100px;" type="text"  class="text_boxes"   name="txt_internal_ref" id="txt_internal_ref"  />
                    </td> 

                    <td align="center" width="250">
						<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
						<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                    </td>
                    <td align="center">
                    	<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value, 'create_cutting_search_list_view', 'search_div', 'cut_and_lay_gmts_no_wise_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
                 </tr>
        		 <tr>                  
                    <td align="center" valign="middle" colspan="8">
                    	<? echo load_month_buttons(1);  ?>
                    </td>
                </tr>   
			</tbody>
      	</table> 
		<div align="center" style="margin-top:5px" id="search_div"> </div>  
	</form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_cutting_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
	$file_no= str_replace("'", "",$ex_data[7]);
	$ref_no= str_replace("'", "",$ex_data[8]);
	$file_no_cond="";
	$ref_no_cond="";

	if($file_no)$file_no_cond=" and c.file_no like '%$file_no%' ";
	if($ref_no)$ref_no_cond=" and c.grouping like '%$ref_no%' ";

    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	
	$sql_order="SELECT a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width, c.po_number, d.marker_qty,d.order_id,d.color_id,b.buyer_name,b.style_ref_no,d.id as dtls_id,d.order_ids, $year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where a.id=d.mst_id and a.job_no=b.job_no and b.id=c.job_id and c.id=d.order_id and a.entry_form=97 
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $conpany_cond $cut_cond $job_cond $sql_cond $order_cond  $file_no_cond $ref_no_cond order by a.id DESC";
	// echo $sql_order;die;
	$res = sql_select($sql_order);
	$data_array = array();
	$order_ids = "";
	foreach ($res as $val)
	{
		$data_array[$val['ID']][$val['DTLS_ID']]['job_no'] = $val['JOB_NO'];
		$data_array[$val['ID']][$val['DTLS_ID']]['cut_no'] = $val['CUT_NUM_PREFIX_NO'];
		$data_array[$val['ID']][$val['DTLS_ID']]['table_no'] = $val['TABLE_NO'];
		$data_array[$val['ID']][$val['DTLS_ID']]['entry_date'] = $val['ENTRY_DATE'];
		$data_array[$val['ID']][$val['DTLS_ID']]['marker_length'] = $val['MARKER_LENGTH'];
		$data_array[$val['ID']][$val['DTLS_ID']]['marker_width'] = $val['MARKER_WIDTH'];
		$data_array[$val['ID']][$val['DTLS_ID']]['fabric_width'] = $val['FABRIC_WIDTH'];
		$data_array[$val['ID']][$val['DTLS_ID']]['shift_name'] = $val['SHIFT_NAME'];
		$data_array[$val['ID']][$val['DTLS_ID']]['color_id'] = $val['COLOR_ID'];
		 $data_array[$val['ID']][$val['DTLS_ID']]['marker_qty'] = $val['MARKER_QTY'];
		// $data_array[$val['ID']][$val['DTLS_ID']]['order_cut_no'] = $val['ORDER_CUT_NO'];
		$data_array[$val['ID']][$val['DTLS_ID']]['buyer_name'] = $val['BUYER_NAME'];
		$data_array[$val['ID']][$val['DTLS_ID']]['style_ref_no'] = $val['STYLE_REF_NO'];
		// $data_array[$val['ID']][$val['DTLS_ID']]['grouping'] = $val['GROUPING'];
		 $data_array[$val['ID']][$val['DTLS_ID']]['po_number'] = $val['PO_NUMBER'];
		$data_array[$val['ID']][$val['DTLS_ID']]['batch_id'] = $val['BATCH_ID'];
		$data_array[$val['ID']][$val['DTLS_ID']]['year'] = $val['YEAR'];
		$data_array[$val['ID']][$val['DTLS_ID']]['order_ids'] = $val['ORDER_IDS'];
		$order_ids .= $val['ORDER_IDS'].",";
	}
	$all_order_ids = implode(",",array_unique(array_filter(explode(",",$order_ids))));
	$order_sql = sql_select("SELECT d.id, d.po_number,d.grouping FROM wo_po_break_down d where d.status_active=1 and d.id in($all_order_ids)");
	$order_arr = array();
	foreach ($order_sql as $v)
	{
		$order_arr[$v['ID']]['po_number'] = $v['PO_NUMBER'];
		$order_arr[$v['ID']]['grouping'] = $v['GROUPING'];
	}
	$table_no_arr=return_library_array( "select id,table_no from lib_cutting_table",'id','table_no');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
  	?>
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="1160" cellspacing="0" cellpadding="0" border="0">
		<thead>
			<tr>
				<th width="40">SL No</th>
				<th width="60">Year</th>
				<th width="60">Cut No</th>
				<th width="60">Table No</th>
				<th width="70">Job No</th>
				<th width="120">Buyer Name</th>
				<th width="120">Style Ref.</th>										
				<th width="120">Order No</th>
				<th width="100">Color</th>
				<th width="70">Marker Length</th>
				<th width="70">Markar Width</th>
				<th width="70">Fabric Width</th>
				<th width="70">Total Lay Qty</th>
				<th>Entry Date</th>
			</tr>
		</thead>
	</table>

    	<table class="rpt_table" id="list_view" rules="all" width="1160" height="" cellspacing="0" cellpadding="0"
        border="0" >
			<tbody>
				<?
				$i = 1;
				foreach ($data_array as $cut_id=>$cut_data)
				{
					foreach ($cut_data as $dtls_id=>$val)
					{
						$order_ids = explode(",",$val['order_ids']);
						$po_number = "";
						$grouping = "";
						foreach ($order_ids as $v)
						{
							$po_number.= ($po_number=="") ? $order_arr[$v]['po_number'] : ",".$order_arr[$v]['po_number'];
							$grouping.= ($grouping=="") ? $order_arr[$v]['grouping'] : ",".$order_arr[$v]['grouping'];
						}
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr onclick="js_set_cutting_value('<?=$cut_id;?>')" style="cursor:pointer"  id="tr_1" height="" cellspacing="0" cellpadding="0"
                          border="0" width="1140"  bgcolor="<?=$bgcolor;?>">
							<td width="40"><p><?=$i;?></p></td>
							<td width="60"><p><?=$val['year'];?></p></td>
							<td width="60"><p><?=$val['cut_no'];?></p></td>
							<td width="60"><p><?=$table_no_arr[$val['table_no']];?></p></td>						
							<td width="70" ><p><?=$val['job_no'];?></p></td>
							<td width="120"><p><?=$buyer_library[$val['buyer_name']];?></p></td>
							<td width="120"style="word-break:break-all"><p><?=$val['style_ref_no'];?></p></td>
							<td width="120"style="word-break:break-all"><p><?=$po_number;?></p></td>						
							<td width="100"><p><?=$color_arr[$val['color_id']];?></p></td>
							<td align="center" width="70"><p><?=$val['marker_length'];?></p></td>
							<td align="center" width="70"><p><?=$val['marker_width'];?></p></td>
							<td align="center" width="70" style="word-break:break-all"><p><?=$val['fabric_width'];?></p></td>
							<td align="center" width="70"><p><?=$val['marker_qty'];?></td>
							<td><p><?=change_date_format($val['entry_date']);?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
			</tbody>
		</table>
		<script>
			setFilterGrid('list_view',-1);
		</script>
	<?

	
	// $arr=array(2=>$table_no_arr,8=>$buyer_arr, 5=>$color_arr);//,4=>$order_number_arr
	// echo create_list_view("list_view", "Cut No,Year,Table No,Job No,Buyer Name,Style No, Order NO,Color,Marker Length,Markar Width,Fabric Width,Total Lay Qty,Entry Date","70,55,60,120,130,130,130,110,90,80,80,100,120","1050","300",0, $sql_order , "js_set_cutting_value", "id", "", 1, "0,0,table_no,0,0,color_id,0,0,0,0,0,0", $arr, "cut_num_prefix_no,year,table_no,job_no,buyer_name,style_ref_no,po_number,color_id,marker_length,marker_width,fabric_width,marker_qty,entry_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,0,0,0,0,3") ;
	exit();
}

if($action=="load_php_mst_form")
{
    $sql_data=sql_select("select b.id as   tbl_id,b.table_no,b.location_id,b.floor_id,a.id,a.job_no,a.company_id,a.entry_date,end_date,a.marker_length,a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.cad_marker_cons,a.cutting_no,a.batch_id,a.start_time,a.end_time,a.working_company_id,a.remarks 
	from  ppl_cut_lay_mst a, lib_cutting_table b
	where   a.table_no=b.id and a.id=".$data." ");
	
    foreach($sql_data as $val)
	  {
		    $start_time=explode(":",$val[csf("start_time")]);
		    $end_time=explode(":",$val[csf("end_time")]);
			echo "document.getElementById('cbo_company_name').value = '".($val[csf("company_id")])."';\n"; 
			echo "document.getElementById('cbo_working_company_name').value = '".($val[csf("working_company_id")])."';\n";
			echo "load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller','".$val[csf("working_company_id")]."', 'load_drop_down_location', 'location_td') ;";
			echo "document.getElementById('txt_table_no').value = '".($val[csf("table_no")])."';\n"; 
			echo "$('#txt_entry_date').val('".change_date_format($val[csf("entry_date")])."');\n";  
			echo "$('#txt_end_date').val('".change_date_format($val[csf("end_date")])."');\n"; 
			echo "document.getElementById('txt_marker_length').value  = '".($val[csf("marker_length")])."';\n"; 
			echo "document.getElementById('txt_marker_width').value  = '".($val[csf("marker_width")])."';\n";  
			echo "document.getElementById('txt_fabric_width').value = '".($val[csf("fabric_width")])."';\n";    
			echo "document.getElementById('txt_gsm').value  = '".($val[csf("gsm")])."';\n";
			echo "document.getElementById('cbo_width_dia').value  = '".($val[csf("width_dia")])."';\n";
			echo "document.getElementById('txt_marker_cons').value  = '".($val[csf("cad_marker_cons")])."';\n";
			echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";
			echo "document.getElementById('txt_remark').value  = '".($val[csf("remarks")])."';\n";
			
			echo "load_drop_down( 'requires/cut_and_lay_gmts_no_wise_entry_controller','".$val[csf("location_id")]."', 'load_drop_down_floor', 'floor_td') ;";  
			echo "document.getElementById('txt_batch_no').value = '".($val[csf("batch_id")])."';\n"; 
			echo "document.getElementById('txt_job_no').value = '".($val[csf("job_no")])."';\n"; 
			echo "document.getElementById('update_tbl_id').value  = '".($val[csf("tbl_id")])."';\n";  
			echo "document.getElementById('update_id').value = '".($val[csf("id")])."';\n";
			echo "document.getElementById('txt_cutting_no').value = '".($val[csf("cutting_no")])."';\n"; 
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n"; 
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_lay_info',1);\n"; 
			echo "document.getElementById('txt_in_time_hours').value  = '".($start_time[0])."';\n";  
			echo "document.getElementById('txt_in_time_minuties').value = '".($start_time[1])."';\n";
			echo "document.getElementById('txt_out_time_hours').value = '".($end_time[0])."';\n"; 
			echo "document.getElementById('txt_out_time_minuties').value  = '".($end_time[1])."';\n";  
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n"; 
			if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1) as year";}
if($db_type==2){ $insert_year="extract(year from b.insert_date) as year";}
			$sql=sql_select("select distinct c.id,c.buyer_name,$insert_year from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.job_no='".$val[csf("job_no")]."' and b.buyer_name=c.id and a.status_active=1");
			
		foreach($sql as $row)
		   {
				echo "document.getElementById('txt_buyer_name').value = '".$row[csf("id")]."';\n"; 
				echo "document.getElementById('txt_job_year').value = '".$row[csf("year")]."';\n";   
		   }
	  }
	  exit();
}

if($action=="order_details_list")
{
	// $sql_gmt_arr="select ";
	 $tbl_row=0;
	 $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 

	$sql_dtls=sql_select("select a.id, a.order_id, a.ship_date, a.color_id, a.color_type_id, a.batch_id ,a.gmt_item_id, a.plies, a.marker_qty, a.order_qty, a.total_lay_qty, a.lay_balance_qty, b.job_no, b.job_year, b.company_id, a.order_cut_no, a.roll_data, a.country_ids, a.iscountry_seq from ppl_cut_lay_dtls a, ppl_cut_lay_mst b where b.id=a.mst_id and mst_id=".$data." order by a.id");
	$color_type_arr=array();
	$sql="SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id and a.id=c.po_break_down_id and b.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id in(".$sql_dtls[0][csf('order_id')].") and c.cons>0  group by b.color_type_id";
	foreach(sql_select($sql) as $vals)
	{
		$color_type_arr[$vals[csf("color_type_id")]]=$color_type[$vals[csf("color_type_id")]];
	}


	foreach($sql_dtls as $val)
	{

		$count_id_cond="";
		if($sql_dtls[0][csf('country_ids')]) $count_id_cond=" and country_id in (".$sql_dtls[0][csf('country_ids')].") ";
		if($sql_dtls[0][csf('country_ids')]) $count_id_cond2=" and a.country_id in (".$sql_dtls[0][csf('country_ids')].") ";

		$sql="select sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id=".$val[csf("order_id")]." and item_number_id=".$val[csf("gmt_item_id")]." and color_number_id=".$val[csf("color_id")]." and status_active=1 $count_id_cond group by po_break_down_id,item_number_id,color_number_id ";
		$result=sql_select($sql);
		foreach($result as $row)
		{
			$plan_qty=$row[csf("plan_qty")];
		}

		$sql_marker="select sum(b.size_qty) as mark_qty from  ppl_cut_lay_dtls a,ppl_cut_lay_bundle b  where a.order_id=".$val[csf("order_id")]." and a.gmt_item_id=".$val[csf("gmt_item_id")]." and b.color_id=".$val[csf("color_id")]." and a.status_active=1 $count_id_cond2 group by a.order_id,a.gmt_item_id,a.color_id ";
		//echo $sql_marker;die;
		$result=sql_select($sql_marker);
		foreach($result as $rows)
		{
			 $total_marker_qty=$rows[csf("mark_qty")];
		}
		$lay_balance=$plan_qty-$total_marker_qty;

		$country_name_arr=array();
		foreach (explode(",", $val[csf('country_ids')]) as  $value) {
			if($val[csf('iscountry_seq')]==0)
			{
				$country_name_arr[$value]=$country_arr[$value];
			}
			else
			{
				$excountryseq=explode("!",$value);
				$country_name_arr[$value]=$country_arr[$excountryseq[0]];
			}
		}
		   
	   $tbl_row++;
?>
	   <tr class="" id="tr_<? echo $tbl_row; ?>" style="height:10px;">
			<td align="center" id="order_id">
				<?   
					$update_job_id=explode("-",$val[csf('job_no')]);
					if($update_job_id[2]!="")
					{
					
					 $sql="select id ,job_no_mst,po_number from  wo_po_break_down where job_no_mst='".$val[csf('job_no')]."' and status_active=1";
					}
					else
					{
					  $sql="select a.id,a.po_number from  wo_po_break_down a,wo_po_details_master b where  a. job_no_mst=b.job_no and b.company_name=".$val[csf('company_id')]."  and job_no_prefix_num='".$update_job_id[0]."' and SUBSTRING_INDEX(b.insert_date, '-', 1)='".$val[csf('job_year')]."' and a.status_active=1"; 
						
					}
					//echo $sql;
					echo create_drop_down( "cboorderno_".$tbl_row, 120, $sql,"id,po_number", 1, "select order", $val[csf('order_id')], "change_data(this.value,this.id)","","");
				?>		 
			</td>  
			<td align="center" id="cutNo_<? echo $tbl_row; ?>">
				<input style="width:60px;" class="text_boxes_numeric" type="text" name="orderCutNo_<? echo $tbl_row; ?>" id="orderCutNo_<? echo $tbl_row; ?>" placeholder="" value="<? echo $val[csf('order_cut_no')]; ?>"  />
			</td>                            
			<td align="center" id="ship_<? echo $tbl_row; ?>">
					<input style="width:70px;" type="text"   class="datepicker" autocomplete="off"  name="txtshipdate_<? echo $tbl_row; ?>" id="txtshipdate_<? echo $tbl_row; ?>"   value="<? echo change_date_format($val[csf('ship_date')]);?>"readonly/>
			</td>                             
			<td align="center" id="garment_<? echo $tbl_row; ?>">
				 <? 
				
					 $gmt_item_arr=sql_select( "select item_number_id from  wo_po_color_size_breakdown where po_break_down_id='".$val[csf('order_id')]."' and status_active=1  group by item_number_id");
					foreach($gmt_item_arr as $ins)
					{
						if($gmt_item_id!="") $gmt_item_id.=",".$ins[csf("item_number_id")];
						else                 $gmt_item_id=$ins[csf("item_number_id")];
						
					}
					 echo create_drop_down( "cbogmtsitem_".$tbl_row, 120, $garments_item,"", 1, "-- Select Item --", $val[csf('gmt_item_id')], "change_color(this.id,this.value)","",$gmt_item_id);
				 ?>
			</td>
			<td align="center" id="color_<? echo $tbl_row; ?>">
				 <? 
					 echo create_drop_down( "cbocolor_".$tbl_row, 100, "select distinct a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b where a.id=b.color_number_id and b.po_break_down_id=".$val[csf('order_id')]." and item_number_id=".$val[csf('gmt_item_id')]."","id,color_name", 1, "select color",  $val[csf('color_id')], "change_marker(this.id,this.value)");
				 ?>
			</td>
			<td align="center" id="colorTypeId_<? echo $tbl_row; ?>">
                <?
                echo create_drop_down( "cboColorType_".$tbl_row, 100, $color_type_arr,"", 1, "--Select--",$val[csf('color_type_id')], "",1,0 );
                ?>
            </td>
            <td align="center">
                <input style="width:70px;" class="text_boxes" type="text" name="countryName_1" id="countryName_1" placeholder="Browse"  onDblClick="openmypage_country(<? echo $tbl_row; ?>)" value="<?php echo implode(",", $country_name_arr);?> "/>
                <input class="text_boxes" type="hidden" name="countryId_<? echo $tbl_row; ?>" id="countryId_<? echo $tbl_row; ?>" value="<?php echo $val[csf('country_ids')];?>" />
                <input class="text_boxes" type="hidden" name="hiddiscountryseq_<?=$tbl_row; ?>" id="hiddiscountryseq_<?=$tbl_row; ?>" value="<?=$val[csf('iscountry_seq')];?>" />
            </td>
			<td align="center" id="batch_<? echo $tbl_row; ?>">
				 <? 
					$sql="select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.color_id='".$val[csf('color_id')]."' and b.po_id='".$val[csf('order_id')]."' and a.entry_form in(0,7,17,37,66,68) and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no";
					$result=sql_select($sql);
					foreach($result as $row)
					{
						$ext='';
						if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
						$batch_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
					}

					$sql="select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.batch_id and b.id=c.dtls_id and a.color_id='".$val[csf('color_id')]."' and c.po_breakdown_id='".$val[csf('order_id')]."' and b.status_active=1 and b.is_deleted=0 and c.entry_form in(14,15,134) and c.trans_type=5 group by a.id, a.batch_no, a.extention_no";
					$result=sql_select($sql);
					foreach($result as $row)
					{
						$ext='';
						if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
						$batch_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
					}
					
					if( empty( $batch_array) )
					{
						echo create_drop_down( "cbobatch_".$tbl_row, 100, $blank_array,"", 1, "select Batch",  "", "");
					}
					else {
					 	echo create_drop_down( "cbobatch_".$tbl_row, 100, $batch_array,"", 1, "select Batch",  $val[csf('batch_id')], "");
					}
					
					 //echo create_drop_down( "cbobatch_".$tbl_row, 100, $batch_array,"", 1, "select Batch",  $val[csf('batch_id')], "");
				 ?>
			</td>
			<td align="center">
				   <input type="text" name="txtplics_<? echo $tbl_row; ?>"  id="txtplics_<? echo $tbl_row; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $val[csf('plies')];?>" placeholder="Double Click" onDblClick="openmypage_roll(<? echo $tbl_row; ?>)" readonly/>
				  <input type="hidden" name="hiddenorder_<? echo $tbl_row; ?>"  id="hiddenorder_<? echo $tbl_row; ?>"  />
				  <input type="hidden" name="updateDetails_<? echo $tbl_row; ?>"  id="updateDetails_<? echo $tbl_row; ?>"  value="<? echo $val[csf('id')]; ?>" />
				  <input type="hidden" name="rollData_<? echo $tbl_row; ?>" id="rollData_<? echo $tbl_row; ?>" class="text_boxes" value="<? echo $val[csf('roll_data')]; ?>" />
			</td>
			<td align="center">
				  <input type="text" name="txtsizeratio_<? echo $tbl_row; ?>" id="txtsizeratio_<? echo $tbl_row; ?>" class="text_boxes_numeric" onDblClick="openmypage_sizeNo(this.id);"  placeholder="Browse" style="width:50px" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick(1)" readonly/>
			</td>
			<td align="center" id="marker_<? echo $tbl_row; ?>">
				  <input type="text" name="txtmarkerqty_<? echo $tbl_row; ?>"  id="txtmarkerqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px" value="<? echo $val[csf('marker_qty')];?>" disabled />
			</td>
			 <td align="center" id="order_<? echo $tbl_row; ?>">
				 <input type="text" name="txtorderqty_<? echo $tbl_row; ?>" id="txtorderqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  value="<? echo $plan_qty;?>" disabled/>
			</td>
			 <td align="center">
				 <input type="text" name="txttotallay_<? echo $tbl_row; ?>"  id="txttotallay_<? echo $tbl_row; ?>"class="text_boxes_numeric"  placeholder="Display" style="width:60px" value="<? echo $total_marker_qty;?>" disabled/>
			</td>
			<td align="center">
				 <input type="text" name="txtlaybalanceqty_<? echo $tbl_row; ?>"  id="txtlaybalanceqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  value="<? echo $lay_balance;?>" disabled/>
			</td>
			<td width="70">
				 <input type="button" id="increase_<? echo $tbl_row; ?>" name="increase_<? echo $tbl_row; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tbl_row; ?>)" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick()" />
				<input type="button" id="decrease_<? echo $tbl_row; ?>" name="decrease_<? echo $tbl_row; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tbl_row; ?>);" />
			</td>
	   </tr>
<?	
	 }
	 exit();
}

if($action=="cut_lay_entry_report_print")
{
    // extract($_REQUEST);
	$data=explode('*',$data);
	
	$sql=sql_select("select id,job_no,cut_num_prefix_no,table_no,marker_length,marker_width,fabric_width,gsm,width_dia,cad_marker_cons,batch_id,company_id from ppl_cut_lay_mst where cutting_no='".$data[0]."' ");
	foreach($sql as $val)
		{
			$mst_id=$val[csf('id')];
			$company_id=$val[csf('company_id')];
			$cut_prifix=$val[csf('cut_num_prefix_no')];
			$table_no=$val[csf('table_no')];
			$marker_length=$val[csf('marker_length')];
			$marker_with=$val[csf('marker_width')];
			$fabric_with=$val[csf('fabric_width')];
			$gsm=$val[csf('gsm')];
			$dia_width=$val[csf('width_dia')];
			$txt_batch=$val[csf('batch_id')];
			$cad_marker_cons=$val[csf('cad_marker_cons')];
			$job_no=$val[csf('job_no')];
		}
		
	$costing_per=return_field_value("costing_per","wo_pre_cost_mst", "job_no='$job_no'");
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}		
		
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
	$sql_buyer_arr=sql_select("select buyer_name,style_ref_no from  wo_po_details_master where job_no='$data[1]'");
	$sql_order=sql_select("select order_id,gmt_item_id,order_qty from ppl_cut_lay_dtls where mst_id='$mst_id'");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
	$order_number_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='$data[1]'",'id','po_number');
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	//print_r($sql_order);
	$order_number=""; $order_id='';
	foreach($sql_order as $order_val)
	{ 
		$item_name=$order_val[csf('gmt_item_id')];
		$order_qty+=$order_val[csf('order_qty')]; 
		if($order_number!="")
		{
			$order_number.=",".$order_number_arr[$order_val[csf('order_id')]];
			$order_id.=",".$order_val[csf('order_id')];
		}
		else 
		{
			$order_number=$order_number_arr[$order_val[csf('order_id')]];
			$order_id=$order_val[csf('order_id')];
		}
	}
	?>
    <div style="width:1100px; position:relative">
    <div style=" width:500; height:200px; position:absolute; left:300px; top:0; ">
                <table width="500" cellspacing="0" align="center">
                    <tr>
                        <td  align="center" style="font-size:xx-large"><strong><? echo $company_library[$company_id]; ?></strong></td>
                    </tr> 
                    <tr>
                        <td  align="center" style="font-size:large"><strong>LAY CHART & CONSUMPTION REPORT</strong></td>
                    </tr>
               </table>
                 
           </div>
    <div style=" width:200; height:40px; position:absolute; right:0; top:50px; ">Date:  ......../......../.......... </div>
    <div style=" width:200; height:60px; position:absolute; right:0; top:90px; " id="barcode_img_id"> </div>
    <div style=" top:80px; width:270; height:200px; position:absolute; left:0; ">
	<table border="1"  cellspacing="0"  width="260" class="rpt_table" rules="all">
         <tr>
              <td width="80">Buyer</td><td width="180" align="center"><? echo $buyer_arr[$sql_buyer_arr[0][csf('buyer_name')]]; ?></td>
         </tr>
         <tr>
              <td>Job No</td><td align="center"> <? echo $data[1]; ?></td>
         </tr>
         <tr>
              <td>Style</td><td align="center"> <? echo $sql_buyer_arr[0][csf('style_ref_no')]; ?></td>
         </tr>
         <tr>
              <td>Item Name</td> <td align="center"><? echo $garments_item[$item_name]; ?></td>
        </tr>
         <tr>
              <td>Order No</td><td align="center"><p> <? echo $order_number; ?></p></td>
         </tr>
         <tr>
              <td>Order Qty</td><td align="right"><? echo $order_qty; ?></td>
         </tr>
    
    </table>
    </div>
  
    <div  style="width:250; position:absolute; height:30px; top:118px; left:280px">
          <table border="1" cellpadding="1" cellspacing="1"   width="220" class="rpt_table" rules="all">
              <tr >
              <td width="170"> CAD Fabric Width/Dia</td><td width="50" align="center" colspan="2"><? echo $fabric_with; ?></td>
             </tr>
          </table>
    </div>
    
    
    
    
   <div  style="width:250; position:absolute; height:30px; top:160px; left:280px">
      <table border="1" cellpadding="1" cellspacing="1"   width="220" class="rpt_table" rules="all">
          <tr >
          <td width="170">CAD GSM</td><td width="50" align="center" colspan="2"><? echo $gsm; ?></td>
         </tr>
      </table>
    </div>
    <div  style="width:285; position:absolute; height:100px; top:280px; left:280px">
      <table border="1" cellpadding="1" cellspacing="1"   width="285" class="rpt_table" rules="all">
          <tr height="20">
          <td width="65">Table No</td>
          <td width="75" align="center"><? echo $table_no_library[$table_no]; ?></td>
          <td width="75" align="center">Batch No </td>
          <td width="80" align="center">Dia(Tube<br>/Open)</td>
         </tr>
         <tr height="30">
          <td width="65">Cutting No</td>
          <td width="75" align="center"><? echo $comp_name."-".$cut_prifix; ?></td>
          <td width="75" align="center">  <? echo $txt_batch; ?></td>
          <td width="80" align="center"> <? echo $fabric_typee[$dia_width]; ?></td>
         </tr>
      </table>
    </div>
    
     <div  style="width:200; position:absolute; height:400px; top:164px; left:580px">
       <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
          <tr height="30">
          <td width="90">Sperading Operators</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="30">
          <td width="">Checked by Marker Man</td>
          <td width="" align="center"></td>
         </tr>
           <tr height="30">
          <td width="90">Cutter Man-1</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="43">
          <td width="">Cutter Man-2</td>
          <td width="" align="center"></td>
         </tr>
          <tr height="30">
          <td width="">Cutter Man-3</td>
          <td width="" align="center"></td>
         </tr>
      </table>
    </div>
    
    
    <div style=" width:300; position:absolute; top:175px; right:0px; ">
	<table border="1" cellpadding="1" cellspacing="1"   width="300"class="rpt_table" rules="all">
         <tr height="30">
              <td width="100"><strong>Line Q.I</strong></td><td width="200" align="center" colspan="2"></td>
         </tr>
         <tr height="30">
              <td><strong>Jr. DQ.C</strong></td><td align="center" colspan="2"></td>
         </tr>
         <tr height="30">
              <td><strong>Checked By Q.C</strong></td> <td align="center" colspan="2"></td>
        </tr >
         <tr height="30">
              <td>Start Time</td><td align="center" width="100"></td><td align="center" width="100"><strong>Total Time Taken</strong></td>
         </tr >
         <tr height="30">
              <td>End Time</td><td align="center" width="100"></td><td align="center" width="100"></td>
         </tr>
    
    </table>
    </div>
    <div style=" width:270; position:absolute; top:250px;  ">
	<div style=" float:left; text-align:center; margin-top:20px; width:80px;"><Strong>STEP LAY DETAILS</Strong></div>
    <div style=" float:right;width:190px;">
         <div style="  width:90px; background-color:#666666; color:white;"><Strong>Step-1</Strong></div>
        <table border="1" cellpadding="1" cellspacing="1"   width="180"class="rpt_table" rules="all">
         <tr height="30">
              <td width="80">CAD Marker Length</td><td width="80" align="center" ><? echo $marker_length;  ?></td>
         </tr>
         <tr height="30"  >
              <td>CAD Marker Width</td><td align="center" ><? echo $marker_with;  ?></td>
         </tr>
        
    
    </table>
    </div>
    </div>
 </div>
 
     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){
		   
			var value = valuess;//$("#barcodeValue").val();
		// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		  
		} 
	   generateBarcode('<? echo $data[0]; ?>');
	 </script>
    
    
 <div style=" width:1100px; position:absolute; top:385px; ">
   <style type="text/css">
            .block_div { 
                    width:auto;
                    height:auto;
                    text-wrap:normal;
					font-size:10.5px;
                    vertical-align:bottom;
                    display: block;
                    
                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
            }
          
        </style> 
   <?
 
     $sql_size_ration=sql_select("select a.id,b.size_id,b.size_ratio from   
     ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls a where a.id=b.dtls_id  and a.mst_id=$mst_id and b.status_active=1  and  b.is_deleted=0 and a.status_active=1
     and a.is_deleted=0 ");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 $size_ratio_arr=array();
	 foreach($sql_size_ration as $size_val)
	 {
	  	$size_ratio_arr[$size_val[csf('id')]][$size_val[csf('size_id')]]=$size_val[csf('size_ratio')];
	 }
	
	$sql_main_qry=sql_select("select c.id,a.id,a.color_id,c.size_id,c.roll_no,sum(c.roll_wgt) as roll_weight,c.plies, sum(c.size_qty) as size_qty,c.roll_id 
	 from  ppl_cut_lay_roll_dtls c,ppl_cut_lay_dtls a 
	 where  a.id=c.dtls_id and a.mst_id=$mst_id and c.status_active=1  and  c.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	 group by c.id,a.id,a.color_id,c.size_id,c.roll_no,c.plies,c.roll_id
	 order by a.id,c.id");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 foreach($sql_main_qry as $main_val)
	 {
	    $size_id_arr[$main_val[csf('size_id')]]=$main_val[csf('size_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['plies']=$main_val[csf('plies')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['size']=$main_val[csf('size_id')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['marker_qty']=$main_val[csf('size_qty')];
		$total_gmt_qty[$main_val[csf('id')]][$main_val[csf('roll_id')]]['gmt_qty']+=$main_val[csf('size_qty')];
		$grand_total+=$main_val[csf('marker_qty')];
		$size_qty=return_field_value("size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =".$main_val[csf('id')]." ");
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['bundle_qty']=$size_qty;
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['color']=$main_val[csf('color_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_no']=$main_val[csf('roll_no')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_weight']=$main_val[csf('roll_weight')];
	 } 
 //print_r($plice_data_arr);die;
   $col_span=count($size_id_arr);
   $td_width=450/$col_span;
  
  // echo $td_width;die;
   
   ?>
    <table border="1" cellpadding="1" cellspacing="1"   width="1100" class="rpt_table" rules="all">
          <tr height="30" >
          <td width="30">SL</td>
          <td width="60" align="center">Roll No </td>
          <td width="50" align="center">Roll Kgs </td>
          <td width="60" align="center">Color </td>
          <td width="70"> Plies & Pcs/Bundle</td>
           <td width="80">Particulars</td>
          <td width="470" align="center" colspan="<? echo $col_span; ?>">Size, Ratio and Garments Qty.</td>
          <td width="50" align="center">Total Gmts</td>
          <td width="70" align="center">Per Roll Cons</td>
           <td width="60">Cut Out Faults</td>
          <td width="60" align="center">End of Roll Length</td>
          <td width="60" align="center">Total Unused Length </td>
         </tr>
        <?
		 $i=1; $tot_gmts=0; $tot_roll_wght=0;
		  foreach($plice_data_arr as $dtls_id=>$dtls_val)
			  {
				foreach($dtls_val as $plice_id=>$plice_val)
				 {
					 $tot_roll_wght+=$plice_val['roll_weight'];
				 ?>
                 <tr height="20">
                      <td width="" rowspan="4"><? echo $i;  ?></td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_no']; ?> </td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_weight']; ?></td>
                      <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? echo $color_arr[$plice_val['color']];  ?></div></td>
                      <td width="" align="left" rowspan="2"><? echo $plice_val['plies']." Plies";  ?></td>
                       <td width="">Size</td>
                       
                   <?
					  foreach($size_id_arr as $size_id=>$size_val)
						{
					 ?>
						   <td width="<? echo $td_width; ?>" align="center" ><? echo $size_arr[$detali_data_arr[$dtls_id][$plice_id][$size_id]['size']];  ?>	</td>
						 
					 <?
						 }
					 ?>
                      <td width="" align="right" valign="bottom" ></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
                   <tr height="20">
                     <td width="">CAD Ratio</td>
               	 <?
				  foreach($size_id_arr as $size_id=>$size_val)
				    {
						$total_size_ratio+=$size_ratio_arr[$dtls_id][$size_id]['size_ratio'];
				 ?>
                       <td width="<? echo $td_width; ?>" align="center" ><? echo $size_ratio_arr[$dtls_id][$size_id]['size_ratio'];  ?>	</td>
                 <?
				     }
                 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_size_ratio; $total_size_ratio=0;  ?></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
                     <tr height="20">
                       <td width="" align="left" rowspan="2"><?  echo $plice_val['bundle_qty']."/Bundle";  ?></td>
                       <td width=""> Gmts Qty.
	</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
								$total_gmt_qty_roll+=$detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];
						 ?>
							   <td width="<? echo $td_width; ?>" align="center" ><? echo $detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];  ?>	</td>
							 
						 <?
							 }
							 $tot_gmts+=$total_gmt_qty_roll;
						 ?> 
                      <td width="" align="right" valign="bottom" ><? echo $total_gmt_qty_roll;$total_gmt_qty_roll=0;  ?></td> 
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
              </tr>
                     <tr height="20">
                       <td width="">Bundle Qty.</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
						  {
						 ?>
							   <td width="<? echo $td_width; ?>" align="center"  style="font-size:14px;">
							   <?  
							   $bdl_qty=floor($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']/$plice_val['bundle_qty']); 
							    $extra_bdl=($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']%$plice_val['bundle_qty']);
								if($extra_bdl!=0) $bdl_qty=$bdl_qty." Full & one $extra_bdl  pcs";
							  
							    echo $bdl_qty;
							    ?>	
                               </td>
						 <?
						 }
						 ?>  
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>       
                
              <?
				 $i=$i+1;
				 }
			  }
		
		     ?> 
    
         
      </table>
      <?
      $table_height=30+($i+1)*20;
	//echo $table_height;die;
	$div_position=$table_height+420;
	
	$color_size_qty_arr=array();
	$color_size_sql=sql_select ("SELECT po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown 
	where is_deleted=0 and status_active=1 and po_break_down_id in (".$order_id.") group by po_break_down_id,item_number_id,size_number_id,color_number_id");
	foreach($color_size_sql as $s_id)
	{
		$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		//$tot_plan_qty+=$s_id[csf('plan_cut_qnty')];
	}

   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum( b.cons ) AS conjumction
   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".$order_id.") and b.cons!=0 and a.body_part_id in (1,21,125)
   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
   $con_per_dzn=array();
   $po_item_qty_arr=array();
   $color_size_conjumtion=array();
   foreach($sql_sewing as $row_sew)
   {
		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
		
		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
		$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
	
		$tot_plan_qty+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
   }
	//    print_r($color_size_conjumtion);
	$con_qnty=0;
	foreach($color_size_conjumtion as $p_id=>$p_value)
	{
		foreach($p_value as $i_id=>$i_value)
		{
			foreach($i_value as $c_id=>$c_value)
			{
			foreach($c_value as $s_id=>$s_value)
				{
					foreach($s_value as $b_id=>$b_value)
					{
						$order_color_size_qty=$b_value['plan_cut_qty'];
						// $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						$order_qty=$tot_plan_qty;
						$order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						$conjunction_per= ($b_value['conjum']*$order_color_size_qty_per/100);
						$con_per_dzn[$p_id][$c_id]+=$conjunction_per;
						$con_qnty+=$conjunction_per;
					}
				}
			} 
		}
	}

	$con_qnty=($con_qnty/$costing_per_qty)*12;
	$net_cons=($tot_roll_wght/$tot_gmts)*12;
	$loss_gain='&nbsp;'; $gain='&nbsp;'; $loss='&nbsp;';
	/*$cons_balance=$cad_marker_cons-$net_cons;
	if($cad_marker_cons>$net_cons) 
	{
		$loss_gain='Gain';
		$gain=number_format($cons_balance,4);
	}
	else if($cad_marker_cons<$net_cons) 
	{
		$loss_gain='Loss';
		$loss=number_format(abs($cons_balance),4);
	}*/
	
	$cons_balance=$con_qnty-$net_cons;
	if($con_qnty>$net_cons) 
	{
		$loss_gain='Gain';
		$gain=number_format($cons_balance,4);
	}
	else if($con_qnty<$net_cons) 
	{
		$loss_gain='Loss';
		$loss=number_format(abs($cons_balance),4);
	}
	?>

       
        <div style="width:1100px;  margin-top: 10px;">
			<div style="width:220px; float: left; ">
				<table border="1" cellpadding="1" cellspacing="1" width="200" class="rpt_table" rules="all">
					<tr  height="30">
						<td width="100">Booking<br>Consumption <br>Per Dzn</td>
						<td width="100" align="center" ><? echo number_format($con_qnty,4); ?></td>
					</tr>
				</table>
			</div>

			<div style=" width:220px; float: left;">
				<table border="1" cellpadding="1" cellspacing="1"   width="200" class="rpt_table" rules="all">
					<tr  height="30" >
						<td width="100" >CAD Marker<br>Consumption <br>Per Dzn</td>
						<td width="100" align="center" ><? echo $cad_marker_cons; ?></td>
					</tr>
				</table>
			</div>
			<div style=" width:200px; float: left; ">
				<table border="1" cellpadding="1" cellspacing="1" width="180" class="rpt_table" rules="all">
					<tr  height="30">
						<td width="40" rowspan="2">Net<br>KGS <br>Used</td>
						<td width="70" align="center" >KGs</td>
						<td width="70" align="center" >G.Qty</td>
					</tr>
					<tr  height="30">
						
						<td width="70" align="center" ><? echo $tot_roll_wght; ?></td>
						<td width="70" align="center" ><? echo $tot_gmts; ?></td>
					</tr>
				</table>
			</div>

			<div style=" width:260px;  float: left;">
				<table border="1" cellpadding="1" cellspacing="1" width="240" class="rpt_table" rules="all">
					<!--<tr height="20">
						<td width="80" rowspan="2">Net<br>Composition <br>Per Dzn</td>
						<td width="70" align="center" >Net</td>
						<td width="70" align="center" ></td>
					</tr>
					<tr height="20">
						<td width="70" align="center" ><?echo number_format($net_cons,4); ?></td>
						<td width="70" align="center" ></td>
					</tr>-->
					<tr height="20">
						<td width="30" rowspan="2">Net<br>Consumption <br>Per Dzn</td>
						<td width="70" align="center">Net</td>
						<td width="70" align="center">Loss</td>
						<td width="70" align="center">Gain</td>
					</tr>
					<tr height="20">
						<td width="70" align="center" ><? echo number_format($net_cons,4); ?></td>
						<td width="70" align="center" ><? echo $loss; ?></td>
						<td width="70" align="center"><? echo $gain; ?></td>
					</tr>
				</table>
			</div>
			<div style=" width:200px; float: left; ">
				<table border="1" cellpadding="1" cellspacing="1" width="200" class="rpt_table" rules="all">
					<tr  height="40">
						<td width="100">Lay<br>Loss/Gain</td>
						<td width="100" align="center" ><? echo $loss_gain; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<div style="width:1100px;">
			<?
			    echo signature_table(58, $company_id, "1100px");
			?>
		</div>
	</div>	
    <?
   exit();
}
//Woven Lay Chart --JK
if($action=="cut_lay_entry_report_print_jk")
{
    // extract($_REQUEST);
	$data=explode('*',$data);
	
	$sql=sql_select("select id,job_no,cut_num_prefix_no,table_no,marker_length,marker_width,fabric_width,gsm,width_dia,cad_marker_cons,batch_id,company_id from ppl_cut_lay_mst where cutting_no='".$data[0]."' ");
	foreach($sql as $val)
		{
			$mst_id=$val[csf('id')];
			$company_id=$val[csf('company_id')];
			$cut_prifix=$val[csf('cut_num_prefix_no')];
			$table_no=$val[csf('table_no')];
			$marker_length=$val[csf('marker_length')];
			$marker_with=$val[csf('marker_width')];
			$fabric_with=$val[csf('fabric_width')];
			$gsm=$val[csf('gsm')];
			$dia_width=$val[csf('width_dia')];
			$txt_batch=$val[csf('batch_id')];
			$cad_marker_cons=$val[csf('cad_marker_cons')];
			$job_no=$val[csf('job_no')];
		}
		
	$costing_per=return_field_value("costing_per","wo_pre_cost_mst", "job_no='$job_no'");
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}		
		
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
	$sql_buyer_arr=sql_select("select buyer_name,style_ref_no from  wo_po_details_master where job_no='$data[1]'");
	$sql_order=sql_select("select order_id,gmt_item_id,order_qty from ppl_cut_lay_dtls where mst_id='$mst_id'");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
	$order_number_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='$data[1]'",'id','po_number');
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	//print_r($sql_order);
	$order_number=""; $order_id='';
	foreach($sql_order as $order_val)
	{ 
		$item_name=$order_val[csf('gmt_item_id')];
		$order_qty+=$order_val[csf('order_qty')]; 
		if($order_number!="")
		{
			$order_number.=",".$order_number_arr[$order_val[csf('order_id')]];
			$order_id.=",".$order_val[csf('order_id')];
		}
		else 
		{
			$order_number=$order_number_arr[$order_val[csf('order_id')]];
			$order_id=$order_val[csf('order_id')];
		}
	}
	?>
    <div style="width:1100px; position:relative">
    <div style=" width:500; height:200px; position:absolute; left:300px; top:0; ">
                <table width="500" cellspacing="0" align="center">
                    <tr>
                        <td  align="center" style="font-size:xx-large"><strong><? echo $company_library[$company_id]; ?></strong></td>
                    </tr> 
                    <tr>
                        <td  align="center" style="font-size:large"><strong>LAY CHART & CONSUMPTION REPORT</strong></td>
                    </tr>
               </table>
                 
           </div>
    <div style=" width:200; height:40px; position:absolute; right:0; top:50px; ">Date:  ......../......../.......... </div>
    <div style=" width:200; height:60px; position:absolute; right:0; top:90px; " id="barcode_img_id"> </div>
    <div style=" top:80px; width:270; height:200px; position:absolute; left:0; ">
	<table border="1"  cellspacing="0"  width="260"class="rpt_table" rules="all">
         <tr>
              <td width="80">Buyer</td><td width="180" align="center"><? echo $buyer_arr[$sql_buyer_arr[0][csf('buyer_name')]]; ?></td>
         </tr>
         <tr>
              <td>Job No</td><td align="center"> <? echo $data[1]; ?></td>
         </tr>
         <tr>
              <td>Style</td><td align="center"> <? echo $sql_buyer_arr[0][csf('style_ref_no')]; ?></td>
         </tr>
         <tr>
              <td>Item Name</td> <td align="center"><? echo $garments_item[$item_name]; ?></td>
        </tr>
         <tr>
              <td>Order No</td><td align="center"><p> <? echo $order_number; ?></p></td>
         </tr>
         <tr>
              <td>Order Qty</td><td align="right"><? echo $order_qty; ?></td>
         </tr>
    
    </table>
    </div>
  
    <div  style="width:250; position:absolute; height:30px; top:118px; left:280px">
          <table border="1" cellpadding="1" cellspacing="1"   width="220"class="rpt_table" rules="all">
              <tr >
              <td width="170"> CAD Fabric Width/Dia</td><td width="50" align="center" colspan="2"><? echo $fabric_with; ?></td>
             </tr>
          </table>
    </div>
    
    
    
    
   <div  style="width:250; position:absolute; height:30px; top:160px; left:280px">
      <table border="1" cellpadding="1" cellspacing="1"   width="220"class="rpt_table" rules="all">
          <tr >
          <td width="170">CAD GSM</td><td width="50" align="center" colspan="2"><? echo $gsm; ?></td>
         </tr>
      </table>
    </div>
    <div  style="width:300; position:absolute; height:100px; top:280px; left:280px">
      <table border="1" cellpadding="1" cellspacing="1"   width="300"class="rpt_table" rules="all">
          <tr height="20">
          <td width="80">Table No</td>
          <td width="75" align="center"><? echo $table_no_library[$table_no]; ?></td>
          <td width="75" align="center">Batch No </td>
          <td width="80" align="center">Dia(Tube<br>/Open)</td>
         </tr>
         <tr height="30">
          <td width="80">Cutting No</td>
          <td width="75" align="center"><? echo $comp_name."-".$cut_prifix; ?></td>
          <td width="75" align="center">  <? echo $txt_batch; ?></td>
          <td width="80" align="center"> <? echo $fabric_typee[$dia_width]; ?></td>
         </tr>
      </table>
    </div>
    
     <div  style="width:200; position:absolute; height:400px; top:164px; left:580px">
       <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
          <tr height="30">
          <td width="90">Sperading Operators</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="30">
          <td width="">Checked by Marker Man</td>
          <td width="" align="center"></td>
         </tr>
           <tr height="30">
          <td width="90">Cutter Man-1</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="43">
          <td width="">Cutter Man-2</td>
          <td width="" align="center"></td>
         </tr>
          <tr height="30">
          <td width="">Cutter Man-3</td>
          <td width="" align="center"></td>
         </tr>
      </table>
    </div>
    
    
    <div style=" width:300; position:absolute; top:175px; right:0px; ">
	<table border="1" cellpadding="1" cellspacing="1"   width="300"class="rpt_table" rules="all">
         <tr height="30">
              <td width="100"><strong>Line Q.I</strong></td><td width="200" align="center" colspan="2"></td>
         </tr>
         <tr height="30">
              <td><strong>Jr. DQ.C</strong></td><td align="center" colspan="2"></td>
         </tr>
         <tr height="30">
              <td><strong>Checked By Q.C</strong></td> <td align="center" colspan="2"></td>
        </tr >
         <tr height="30">
              <td>Start Time</td><td align="center" width="100"></td><td align="center" width="100"><strong>Total Time Taken</strong></td>
         </tr >
         <tr height="30">
              <td>End Time</td><td align="center" width="100"></td><td align="center" width="100"></td>
         </tr>
    
    </table>
    </div>
    <div style=" width:270; position:absolute; top:250px;  ">
	<div style=" float:left; text-align:center; margin-top:20px; width:80px;"><Strong>STEP LAY DETAILS</Strong></div>
    <div style=" float:right;width:190px;">
         <div style="  width:90px; background-color:#666666; color:white;"><Strong>Step-1</Strong></div>
        <table border="1" cellpadding="1" cellspacing="1"   width="180"class="rpt_table" rules="all">
         <tr height="30">
              <td width="80">CAD Marker Length</td><td width="80" align="center" ><? echo $marker_length;  ?></td>
         </tr>
         <tr height="30"  >
              <td>CAD Marker Width</td><td align="center" ><? echo $marker_with;  ?></td>
         </tr>
        
    
    </table>
    </div>
    </div>
 </div>
 
     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){
		   
			var value = valuess;//$("#barcodeValue").val();
		// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		  
		} 
	   generateBarcode('<? echo $data[0]; ?>');
	 </script>
    
    
 <div style=" width:1100px; position:absolute; top:385px; ">
   <style type="text/css">
            .block_div { 
                    width:auto;
                    height:auto;
                    text-wrap:normal;
					font-size:10.5px;
                    vertical-align:bottom;
                    display: block;
                    
                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
            }
          
        </style> 
   <?
 
     $sql_size_ration=sql_select("select a.id,b.size_id,b.size_ratio from   
     ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls a where a.id=b.dtls_id  and a.mst_id=$mst_id and b.status_active=1  and  b.is_deleted=0 and a.status_active=1
     and a.is_deleted=0 ");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 $size_ratio_arr=array();
	 foreach($sql_size_ration as $size_val)
	 {
	  	$size_ratio_arr[$size_val[csf('id')]][$size_val[csf('size_id')]]=$size_val[csf('size_ratio')];
	 }
	
	$sql_main_qry=sql_select("select c.id,a.id,a.color_id,c.size_id,c.roll_no,sum(c.roll_wgt) as roll_weight,c.plies, sum(c.size_qty) as size_qty,c.roll_id 
	 from  ppl_cut_lay_roll_dtls c,ppl_cut_lay_dtls a 
	 where  a.id=c.dtls_id and a.mst_id=$mst_id and c.status_active=1  and  c.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	 group by c.id,a.id,a.color_id,c.size_id,c.roll_no,c.plies,c.roll_id
	 order by a.id,c.id");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 foreach($sql_main_qry as $main_val)
	 {
	    $size_id_arr[$main_val[csf('size_id')]]=$main_val[csf('size_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['plies']=$main_val[csf('plies')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['size']=$main_val[csf('size_id')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['marker_qty']=$main_val[csf('size_qty')];
		$total_gmt_qty[$main_val[csf('id')]][$main_val[csf('roll_id')]]['gmt_qty']+=$main_val[csf('size_qty')];
		$grand_total+=$main_val[csf('marker_qty')];
		$size_qty=return_field_value("size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =".$main_val[csf('id')]." ");
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['bundle_qty']=$size_qty;
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['color']=$main_val[csf('color_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_no']=$main_val[csf('roll_no')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_weight']=$main_val[csf('roll_weight')];
	 } 
 //print_r($plice_data_arr);die;
   $col_span=count($size_id_arr);
   $td_width=450/$col_span;
  
  // echo $td_width;die;
   
   ?>
    <table border="1" cellpadding="1" cellspacing="1"   width="1100"class="rpt_table" rules="all">
          <tr height="30" >
          <td width="30">SL</td>
          <td width="60" align="center">Roll No </td>
          <td width="60" align="center">Roll Yrds </td>
          <td width="50" align="center">Color </td>
          <td width="70"> Plies & Pcs/Bundle</td>
           <td width="80">Particulars</td>
          <td width="470" align="center" colspan="<? echo $col_span; ?>">Size, Ratio and Garments Qty.</td>
          <td width="50" align="center">Total Gmts</td>
          <td width="70" align="center">Per Roll Cons</td>
           <td width="60">Cut Out Faults</td>
          <td width="60" align="center">End of Roll Length</td>
          <td width="60" align="center">Total Unused Length </td>
         </tr>
        <?
		 $i=1; $tot_gmts=0; $tot_roll_wght=0;
		  foreach($plice_data_arr as $dtls_id=>$dtls_val)
			  {
				foreach($dtls_val as $plice_id=>$plice_val)
				 {
					 $tot_roll_wght+=$plice_val['roll_weight'];
				 ?>
                 <tr height="20">
                      <td width="" rowspan="4"><? echo $i;  ?></td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_no']; ?> </td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_weight']; ?></td>
                      <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? echo $color_arr[$plice_val['color']];  ?></div></td>
                      <td width="" align="left" rowspan="2"><? echo $plice_val['plies']." Plies";  ?></td>
                       <td width="">Size</td>
                       
                   <?
					  foreach($size_id_arr as $size_id=>$size_val)
						{
					 ?>
						   <td width="<? echo $td_width; ?>" align="center" ><? echo $size_arr[$detali_data_arr[$dtls_id][$plice_id][$size_id]['size']];  ?>	</td>
						 
					 <?
						 }
					 ?>
                      <td width="" align="right" valign="bottom" ></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
                   <tr height="20">
                     <td width="">CAD Ratio</td>
               	 <?
				  foreach($size_id_arr as $size_id=>$size_val)
				    {
						$total_size_ratio+=$size_ratio_arr[$dtls_id][$size_id]['size_ratio'];
				 ?>
                       <td width="<? echo $td_width; ?>" align="center" ><? echo $size_ratio_arr[$dtls_id][$size_id]['size_ratio'];  ?>	</td>
                 <?
				     }
                 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_size_ratio; $total_size_ratio=0;  ?></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
                     <tr height="20">
                       <td width="" align="left" rowspan="2"><?  echo $plice_val['bundle_qty']."/Bundle";  ?></td>
                       <td width=""> Gmts Qty.
</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
								$total_gmt_qty_roll+=$detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];
						 ?>
							   <td width="<? echo $td_width; ?>" align="center" ><? echo $detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];  ?>	</td>
							 
						 <?
							 }
							 $tot_gmts+=$total_gmt_qty_roll;
						 ?> 
                      <td width="" align="right" valign="bottom" ><? echo $total_gmt_qty_roll;$total_gmt_qty_roll=0;  ?></td> 
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
              </tr>
                     <tr height="20">
                       <td width="">Bundle Qty.</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
						  {
						 ?>
							   <td width="<? echo $td_width; ?>" align="center"  style="font-size:14px;">
							   <?  
							   $bdl_qty=floor($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']/$plice_val['bundle_qty']); 
							    $extra_bdl=($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']%$plice_val['bundle_qty']);
								if($extra_bdl!=0) $bdl_qty=$bdl_qty." Full & one $extra_bdl  pcs";
							  
							    echo $bdl_qty;
							    ?>	
                               </td>
						 <?
						 }
						 ?>  
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>       
                
              <?
				 $i=$i+1;
				 }
			  }
		
		     ?> 
    
         
      </table>
      <?
      $table_height=30+($i+1)*20;
	//echo $table_height;die;
	$div_position=$table_height+420;
	
	$color_size_qty_arr=array();
	$color_size_sql=sql_select ("SELECT po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown 
	where is_deleted=0 and status_active=1 and po_break_down_id in (".$order_id.") group by po_break_down_id,item_number_id,size_number_id,color_number_id");
	foreach($color_size_sql as $s_id)
	{
		$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		//$tot_plan_qty+=$s_id[csf('plan_cut_qnty')];
	}

   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum( b.cons ) AS conjumction
   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".$order_id.") and b.cons!=0 and a.body_part_id in (1,20,125)
   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
   $con_per_dzn=array();
   $po_item_qty_arr=array();
   $color_size_conjumtion=array();
   foreach($sql_sewing as $row_sew)
   {
		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
		
		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
		$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
	
		$tot_plan_qty+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
   }
   //print_r($color_size_conjumtion);
	$con_qnty=0;
	foreach($color_size_conjumtion as $p_id=>$p_value)
	{
		foreach($p_value as $i_id=>$i_value)
		{
			foreach($i_value as $c_id=>$c_value)
			{
			foreach($c_value as $s_id=>$s_value)
				{
					foreach($s_value as $b_id=>$b_value)
					{
						$order_color_size_qty=$b_value['plan_cut_qty'];
						// $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						$order_qty=$tot_plan_qty;
						$order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						$conjunction_per= ($b_value['conjum']*$order_color_size_qty_per/100);
						$con_per_dzn[$p_id][$c_id]+=$conjunction_per;
						$con_qnty+=$conjunction_per;
					}
				}
			} 
		}
	}

	$con_qnty=($con_qnty/$costing_per_qty)*12;
	$net_cons=($tot_roll_wght/$tot_gmts)*12;
	$loss_gain='&nbsp;'; $gain='&nbsp;'; $loss='&nbsp;';
	/*$cons_balance=$cad_marker_cons-$net_cons;
	if($cad_marker_cons>$net_cons) 
	{
		$loss_gain='Gain';
		$gain=number_format($cons_balance,4);
	}
	else if($cad_marker_cons<$net_cons) 
	{
		$loss_gain='Loss';
		$loss=number_format(abs($cons_balance),4);
	}*/
	
	$cons_balance=$con_qnty-$net_cons;
	if($con_qnty>$net_cons) 
	{
		$loss_gain='Gain';
		$gain=number_format($cons_balance,4);
	}
	else if($con_qnty<$net_cons) 
	{
		$loss_gain='Loss';
		$loss=number_format(abs($cons_balance),4);
	}
	?>

       
       <div style=" width:160px; position:absolute; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="200" class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="100">Booking<br>Consumption <br>Per Yrds</td>
                       <td width="100" align="center" ><? echo number_format($con_qnty,4); ?></td>
                  </tr>
            </table>
       </div>
       
        <div style=" width:160px; position:absolute; left:220px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
                  <tr  height="30" >
                       <td width="100" >CAD Marker<br>Consumption <br>Per Yrds</td>
                       <td width="100" align="center" ><? echo $cad_marker_cons; ?></td>
                  </tr>
            </table>
       </div>
         <div style=" width:180px; position:absolute; left:440px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="180"class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="40" rowspan="2">Net<br>Yrds <br>Used</td>
                       <td width="70" align="center" >Yrds</td>
                       <td width="70" align="center" >G.Qty</td>
                  </tr>
                   <tr  height="30">
                       
                       <td width="70" align="center" ><? echo $tot_roll_wght; ?></td>
                       <td width="70" align="center" ><? echo $tot_gmts; ?></td>
                  </tr>
            </table>
       </div>
       
        <div style=" width:230px; position:absolute; right:191px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="220"class="rpt_table" rules="all">
                  <!--<tr height="20">
                       <td width="80" rowspan="2">Net<br>Composition <br>Per Dzn</td>
                       <td width="70" align="center" >Net</td>
                       <td width="70" align="center" ></td>
                  </tr>
                   <tr height="20">
                       <td width="70" align="center" ><?echo number_format($net_cons,4); ?></td>
                       <td width="70" align="center" ></td>
                  </tr>-->
                  <tr height="20">
                       <td width="80" rowspan="2">Net<br>Consumption <br>Per Yrds</td>
                       <td width="70" align="center">Net</td>
                       <td width="70" align="center">Loss</td>
                       <td width="70" align="center">Gain</td>
                  </tr>
                   <tr height="20">
                       <td width="70" align="center" ><? echo number_format($net_cons,4); ?></td>
                       <td width="70" align="center" ><? echo $loss; ?></td>
                       <td width="70" align="center"><? echo $gain; ?></td>
                  </tr>
            </table>
       </div>
         <div style=" width:180px; position:absolute; right:0; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="180"class="rpt_table" rules="all">
                  <tr  height="40">
                       <td width="100">Lay<br>Loss/Gain</td>
                       <td width="80" align="center" ><? echo $loss_gain; ?></td>
                  </tr>
            </table>
       </div>
      <div style=" width:1100px; position:absolute; left:0px; margin-top:150px; ">
         <?
           echo signature_table(58, $company_id, "1100px");
         ?>
      </div>
 </div>
    <?
   exit();
}
if($action=="roll_maintained")
{
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and item_category_id=51 and is_deleted=0 and status_active=1");
	if($roll_maintained==1) $roll_maintained=$roll_maintained; else $roll_maintained=0;
	
	$rmg_no_creation=return_field_value("smv_source","variable_settings_production","company_name='$data' and variable_list=39 and is_deleted=0 and status_active=1"); 
	if($rmg_no_creation=="") $rmg_no_creation=2; else $rmg_no_creation=$rmg_no_creation;
	
	echo "document.getElementById('roll_maintained').value 					= '".$roll_maintained."';\n";
	echo "document.getElementById('rmg_no_creation').value 					= '".$rmg_no_creation."';\n";
	
	exit();	
}

?>