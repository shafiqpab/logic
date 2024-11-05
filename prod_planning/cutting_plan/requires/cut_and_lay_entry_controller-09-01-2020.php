<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//echo $action;
//die;

//--------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/cut_and_lay_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' )" );     	 
	exit();
}
if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 140, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );  
	exit();   	 
}

if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1)";}
if($db_type==2){ $insert_year="extract(year from b.insert_date)";}

if ($action=="load_drop_down_buyer")
{    
     $data=explode("**",$data);
	 $sql="select distinct c.id,c.buyer_name from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.company_name=".$data[2]."  and job_no_prefix_num='".$data[0]."' and $insert_year='".$data[1]."' and b.buyer_name=c.id and a.status_active=1";
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
	 $sql="select distinct b.job_no from  wo_po_break_down a,wo_po_details_master b where  a.job_no_mst=b.job_no and b.company_name=".$data[2]."  and job_no_prefix_num='".$data[0]."' and $insert_year='".$data[1]."' and  a.status_active=1 and  b.status_active=1";
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
	$sql="select id,po_number from  wo_po_break_down where job_no_mst='".$data[0]."' and status_active=1";
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
	if(count($gmt_item_arr)>1)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[2]", 120, $garments_item,"", 1, "-- Select Item --", $selected, "change_color(this.id,this.value)","",$gmt_item_id);
	}
	if(count($gmt_item_arr)==0)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[2]", 120, $blank_array,"", 1, "-- Select Item --", $selected, "","");
	}
	exit();
}

if ($action=="load_drop_down_color")
{
	$ex_data = explode("_",$data);
	$color_item_arr=return_library_array( "select distinct a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b where a.id=b.color_number_id and b.po_break_down_id=".$ex_data[0]." and item_number_id=".$ex_data[1]."");
    $color_item_id=implode(",",$color_item_arr);
	if(count($color_item_arr)==1)
	{ 
		echo create_drop_down( "cbocolor_$ex_data[2]", 130, "select distinct a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b where a.id=b.color_number_id and b.po_break_down_id=".$ex_data[0]." and item_number_id=".$ex_data[1]."","id,color_name", 1, "select color",$color_item_id, "change_marker(this.id,this.value)");
		exit();
	}
	if(count($gmt_item_arr)==0)
	{ 
		echo create_drop_down( "cbocolor_$ex_data[2]", 130, $blank_array,"id,color_name", 1, "select color", $selected, "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbocolor_$ex_data[2]", 130, "select distinct a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b where a.id=b.color_number_id and b.po_break_down_id=".$ex_data[0]." and item_number_id=".$ex_data[1]."","id,color_name", 1, "select color", $selected, "change_marker(this.id,this.value)");	
	}
	exit();		
}

if ($action=="load_drop_down_order_qty")
{
	$ex_data = explode("_",$data);

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
		echo "document.getElementById('txttotallay_$ex_data[3]').value  = '".($rows[csf("mark_qty")])."';\n"; 
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
	<input style="width:80px;" type="text"   class="datepicker" autocomplete="off"  name="txtshipdate_<? echo $ex_data[2]; ?>" id="txtshipdate_<? echo $ex_data[2]; ?>"  placeholder="Display" value="<? echo change_date_format($ship_data); ?>" readonly/>  
	<?
	exit();
}

$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');
$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
//--------------------------------------------------------------------------------------------

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

if($action=="size_popup")
{
  	echo load_html_head_contents("Cut and bundle details","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//echo $size_wise_repeat_cut_no;die;
?>
	<script>
	
	var permission='<? echo $permission; ?>';
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
		var value=(value1*1)*(value2*1);
		var lay_value=$("#txt_layf_balance_"+x[3]).val()*1;
		
		if(value>lay_value)
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
		distribute_qnty(value1, size_id, value2, value);
		total_size_qty();
		total_size_ration();
	}
	
	function distribute_qnty(plies, size_id, size_ratio, marker_qty)
	{
		var balance=marker_qty*1; var totalMarker=0;
		var row_num=$("#tbl_size_details tbody tr").length;
		for(var i=1; i<=row_num; i++)
		{
			var size_id_curr=$("#hidden_size_id_"+i).val();
			var txt_lay_balance=$("#txt_lay_balance_"+i).val()*1;
				
			if(size_id_curr==size_id)
			{
				if(balance>0)
				{
					if(balance>txt_lay_balance)
					{
						if(txt_lay_balance<0) {txt_lay_balance=0;}
						var marker_qty_curr=txt_lay_balance;
						balance=balance-txt_lay_balance;
					}
					else
					{
						var marker_qty_curr=balance;
						balance=0;
					}
					
					//totalMarker = totalMarker*1+marker_qty_curr*1;
					var ratio=Math.round(marker_qty_curr/plies);
					$("#txt_size_ratio_"+i).val(ratio);
					$("#txt_size_qty_"+i).val(marker_qty_curr);
				}
				else
				{
					$("#txt_size_ratio_"+i).val('');
					$("#txt_size_qty_"+i).val('');
				}
			}
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
	}
	$('#total_size_qty').text(tot_qty);
	$('#hidden_marker_qty').val(tot_qty);
}

//gsd_
function sum_size_qty_adjust(plies,size_id,i)
{
	var marker_qty_curr=$("#txt_size_qty_"+i).val();
	var ratio=Math.round(marker_qty_curr/plies);
	$("#txt_size_ratio_"+i).val(ratio);
	$("#txt_size_qty_"+i).val(marker_qty_curr);
	
	total_size_qty();
	total_size_ration();
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
	var size_wise_repeat_cut_no=<? echo $size_wise_repeat_cut_no; ?>;
	var bundle_per_pcs=$("#txt_bundle_pcs").val();
	var to_marker_qty=$("#hidden_marker_qty").val();
	var job_id=$("#hidden_update_job_id").val();
	var cut_no=$("#hidden_update_cut_no").val();	
	var row_num=$('#tbl_size_details tbody tr').length;
	var data1="action=save_update_delete_size&operation="+operation+"&row_num="+row_num+"&color_id="+color_id+"&mst_id="+mst_id+"&dtls_id="+dtls_id+"&bundle_per_pcs="+bundle_per_pcs+"&to_marker_qty="+to_marker_qty+"&cbo_company_id="+cbo_company_id+"&job_id="+job_id+"&cut_no="+cut_no+"&order_id="+order_id+"&gmt_id="+gmt_id+"&size_wise_repeat_cut_no="+size_wise_repeat_cut_no;
	 var data2=''; var size_data='';

	if(size_wise_repeat_cut_no==1)
	{
		/*if(($("#total_sizef_qty").text()*1)!=($("#total_size_qty").text()*1))
		{
			alert("Marker Qty Mismatch Between Size Label Total and Country Label Total");return;
		}*/
		
		var size_row_num=$('#tbl_size tbody tr').length;
		for(var k=1; k<=size_row_num; k++)
		{
			size_data+=get_submitted_data_string('txt_layf_balance_'+k+'*txt_sizef_ratio_'+k+'*txt_sizef_qty_'+k+'*hidden_sizef_id_'+k,"../../../",k);
		}
		
		size_data=size_data+"&size_row_num="+size_row_num;	
		
		for(var k=1; k<=row_num; k++)
		{
			data2+=get_submitted_data_string('cboCountryType_'+k+'*cboCountry_'+k+'*txt_lay_balance_'+k+'*txt_size_ratio_'+k+'*txt_size_qty_'+k+'*txt_bundle_'+k+'*hidden_size_id_'+k+'*update_size_id_'+k,"../../../",k);
		}
	}
	else
	{
		for(var k=1; k<=row_num; k++)
		{
			data2+=get_submitted_data_string('txt_lay_balance_'+k+'*txt_size_ratio_'+k+'*txt_size_qty_'+k+'*txt_bundle_'+k+'*hidden_size_id_'+k+'*update_size_id_'+k,"../../../",k);
		}	
	}
	var data=data1+data2+size_data;
	//alert(size_data);return;
	freeze_window(operation);
	http.open("POST","cut_and_lay_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_cut_lay_size_info_reponse;
}

function fnc_cut_lay_size_info_reponse()
{
	if(http.readyState == 4) 
	{
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
			 if(reponse[0]==1)
			 {
				 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
				 {
					$('#msg_box_popp').html("Data Update Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				 });
			 }
			
			show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3]+'**'+reponse[7],'show_bundle_list_view','search_div','cut_and_lay_entry_controller','setFilterGrid("list_view",-1)');
			var update_size_id=reponse[3].split('_');
			$("#hidden_plant_qty").val(reponse[4]);
			$("#hidden_total_marker").val(reponse[5]);
			$("#hidden_lay_balance").val(reponse[6]);
			if(reponse[7]==1)
			{
				var update_data=reponse[3].split(',');
				var dtlsId_array = new Array();
				for(var k=0; k<update_data.length; k++)
				{
					var datas=update_data[k].split("__");
					var index=datas[1]+datas[2]+datas[3];
					dtlsId_array[index] = datas[0]+"**"+datas[4];
				}
				
				var row_num=$('#tbl_size_details tbody tr').length;
				for(var i=1;i<=row_num;i++)
				{
					var cboCountryType=	$("#cboCountryType_"+i).val();
					var cboCountry=	$("#cboCountry_"+i).val();
					var hidden_size_id=	$("#hidden_size_id_"+i).val();
					var index=cboCountryType+cboCountry+hidden_size_id;
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
		if(reponse[0]==15)
		{
			alert("No Data Found");
		}
		if(reponse[0]==200)
		{
			alert("Update Restricted.This information found in Cutting Qc Page Which System Id "+reponse[3]+".");
			//show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3],'show_bundle_list_view','search_div','cut_and_lay_entry_controller','setFilterGrid("list_view",-1)');
		}
		if(reponse[0]==201)
		{
			alert("Save Restricted.This information found in Cutting Qc Page Which System Id "+reponse[3]+".");
			//show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3],'show_bundle_list_view','search_div','cut_and_lay_entry_controller','setFilterGrid("list_view",-1)');
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
	   print_report(<? echo $cbo_company_id; ?>+'*'+<? echo $mst_id; ?>+'*'+<? echo $details_id; ?>+'*'+report_title+'*'+<? echo $size_wise_repeat_cut_no; ?>  , "cut_lay_bundle_print", "cut_and_lay_entry_controller")
		
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
	}
	
	function fnc_bundle_report_eight(column_list)
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
		data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id;?>;
		
		var title = 'Search Job No';	
		var page_link = 'cut_and_lay_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			data=data+'***'+prodID;
			if(column_list==8)
			{
				var url=return_ajax_request_value(data, "print_barcode_eitht", "cut_and_lay_entry_controller");
				window.open(url,"##");	
			}
			else
			{
				var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_controller");
				window.open(url,"##");	
			}
		}
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
		var page_link = 'cut_and_lay_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		 {
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			data=data+'***'+prodID;
			//window.open("cut_and_lay_entry_controller.php?data=" + data+'&action=print_barcode_one', true );
			var url=return_ajax_request_value(data, "print_barcode_one", "cut_and_lay_entry_controller");
			window.open(url,"##");
		 }

		//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_roll_wise_entry_controller");
		//window.open(url,"##");
	}

	function fnc_bundle_report_ten()
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
		data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id;?>;
		
		var title = 'Search Job No';	
		var page_link = 'cut_and_lay_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		 {
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			data=data+'***'+prodID;
			
			var url=return_ajax_request_value(data, "print_barcode_ten", "cut_and_lay_entry_controller");
			window.open(url,"##");	
			
		 }
	}
	
	function fnc_bundle_report_ten_bpkw()
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
		data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id;?>;
		
		var title = 'Search Job No';	
		var page_link = 'cut_and_lay_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		 {
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			data=data+'***'+prodID;
			
			var url=return_ajax_request_value(data, "print_barcode_ten_bpkw", "cut_and_lay_entry_controller");
			window.open(url,"##");	
		 }
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
		var url=return_ajax_request_value(data, "report_bundle_text_file", "cut_and_lay_entry_controller");
	    window.open(url+".zip","##");
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
		$('#bundleSizeQty_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","bundle_calclution("+actual_id+","+row_num+");");
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
		$("#bundleNo_"+row_num).val($("#bundleNo_"+actual_id).val()+"-");
		serial_rearrange();
	}
	
	function delete_bundle_row(actual_id,rowNo) 
	{ 
		var total_add_id=$("#hiddenExtraTr_"+actual_id).val();
		// alert(total_add_id);
		var id_arr=total_add_id.split("**")
		
		id_arr.splice(id_arr.indexOf(rowNo), 1);
		// alert(id_arr.length)
		if( id_arr.length==1)  $('#addButton_'+actual_id).removeAttr("onclick").attr("onclick","fnc_addRow("+actual_id+","+actual_id+");");
		var new_id=id_arr.join("**");
		$("#hiddenExtraTr_"+actual_id).val(new_id);
		//alert( $("#hiddenExtraTr_"+actual_id).val())
		$("#trBundleListSave_"+rowNo).remove();
		serial_rearrange();
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
   var size_wise_repeat_cut_no=<? echo $size_wise_repeat_cut_no; ?>;
   if(size_wise_repeat_cut_no!=1)
	{
		$("#bundleSizeQty_"+id_row).attr("disabled",false);
		$("#sizeName_"+id_row).attr("disabled",false);
		$('#bundleSizeQty_'+id_row).removeAttr("onKeyUp").attr("onKeyUp","fnc_rearrange_rmg("+id_row+");");
		$("#hiddenUpdateFlag_"+id_row).val(6);
	}
	else 
	{
		$("#bundleSizeQty_"+id_row).attr("disabled",false);
		$("#sizeName_"+id_row).attr("disabled",false);
		$("#cboCountryB_"+id_row).removeAttr("disabled","disabled");
		$("#hiddenUpdateFlag_"+id_row).val(6);
		
	}
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
		/*var bundle_no=($(this).find('input[name="bundleNo[]"]').val()).match("/");
		if(bundle_no=="/")
		{
			 var bundle_break=($(this).find('input[name="bundleNo[]"]').val()).split('/');
			 if(bundle_break[1]=="")
			 {
			  $(this).find('input[name="bundleNo[]"]').css({"background-color":"red"});
			  error=1;
			 }
		}
		var bundle_size_qty=$(this).find('input[name="bundleSizeQty[]"]').val();
		if(bundle_size_qty<=0 || bundle_size_qty=="")
		{
		$(this).find('input[name="bundleSizeQty[]"]').css({"background-color":"red"});
		error=1;
		}*/
		s++;
	});
	
	}
  
  function bundle_calclution(actual_id,row_id)
  {
	  
	var size_wise_repeat_cut_no=<? echo $size_wise_repeat_cut_no; ?>;
	if(size_wise_repeat_cut_no==1)
	{
		//alert(44)
		var rmg_no_arr=[];
		$("#tbl_bundle_list_save").find('tbody tr').each(function()
		{
			var bundle_from=parseInt($(this).find('input[name="rmgNoStart[]"]').val());
			//var bundle_size_id=parseInt($(this).find('select[name="sizeName[]"]').val());
			var cboCountry=parseInt($(this).find('select[name="cboCountryB[]"]').val());
			var bundle_size_id=parseInt($(this).find('input[name="hiddenSizeId[]"]').val());
			
			//var index=cboCountry+"."+bundle_size_id;
			var index=bundle_size_id;
			if(rmg_no_arr[index]!=undefined)
			{
				if(rmg_no_arr[index]>bundle_from)
				{
					rmg_no_arr[index]=bundle_from;
				}
			}
			else
			{
				rmg_no_arr[index]=bundle_from;
			}
		});
		
		$("#tbl_bundle_list_save").find('tbody tr').each(function()
		{
			var bundle_from=parseInt($(this).find('input[name="rmgNoStart[]"]').val());
			var bundle_size_id=parseInt($(this).find('select[name="sizeName[]"]').val());
			var cboCountry=parseInt($(this).find('select[name="cboCountryB[]"]').val());
			var qty=$(this).find('input[name="bundleSizeQty[]"]').val()*1;
			
			//var index=cboCountry+"."+bundle_size_id;
			var index=bundle_size_id;
			var from=rmg_no_arr[index];
			var to=rmg_no_arr[index]*1+qty*1-1;
			rmg_no_arr[index]+=qty*1;
			
			$(this).find('input[name="rmgNoStart[]"]').val(from);
			$(this).find('input[name="rmgNoEnd[]"]').val(to);
		});
		
	}
	else
	{
		var old_bundle_qty=$("#hiddenSizeQty_"+actual_id).val(); 
		var bundle_qty=$("#bundleSizeQty_"+actual_id).val();
		var first_rmg_qty=$("#rmgNoStart_"+actual_id).val(); 
		if(actual_id==row_id)
		{
			if(parseInt(bundle_qty)<=parseInt(old_bundle_qty))
			{
			   $("#rmgNoEnd_"+actual_id).val(parseInt(bundle_qty)+parseInt(first_rmg_qty)-1); 
			}
			else
			{
				$("#rmgNoEnd_"+actual_id).val('');
			}
			
		}
		else
		{
			 var total_id=$("#hiddenExtraTr_"+actual_id).val();
			 var id_arr=total_id.split("**");
			 var total_qty=0;
			 var start_qty=0;
			 for(var k=0; k<id_arr.length; k++)
				 {
					 if(id_arr[k]<=row_id)
						{
							 total_qty=total_qty+parseInt($("#bundleSizeQty_"+id_arr[k]).val());
							 if(id_arr[k]<row_id)   start_qty=start_qty+parseInt($("#bundleSizeQty_"+id_arr[k]).val());
							 if(parseInt(total_qty)<=parseInt(old_bundle_qty))
								{
								   $("#rmgNoStart_"+row_id).val(parseInt($("#rmgNoEnd_"+id_arr[k-1]).val())+1); 
								   $("#rmgNoEnd_"+row_id).val(parseInt($("#rmgNoStart_"+id_arr[k]).val())+parseInt($("#bundleSizeQty_"+row_id).val())-1); 
								}
								else
								{
									$("#rmgNoStart_"+row_id).val('');
									$("#bundleSizeQty_"+row_id).val('');
									$("#rmgNoEnd_"+row_id).val('');
								}
					   }
					   else
					   {
						  $("#rmgNoEnd_"+id_arr[k]).val(''); 
						  $("#rmgNoStart_"+id_arr[k]).val('');
						  $("#bundleSizeQty_"+id_arr[k]).val('');
					   }
				 }
		 }
	}
 }
  //********************************************** BUNDLE UPDATE *******************************************
  
function fnc_cut_lay_bundle_info(operation) 
{ 
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		var dataString_bundle="";  
		var size_wise_repeat_cut_no=<? echo $size_wise_repeat_cut_no; ?>;
		var j=0; var z=0; var tot_row=0; var sl=0; var error=0;
		
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
			
			
			var bundle_size_qty=$(this).find('input[name="bundleSizeQty[]"]').val();
		    if(bundle_size_qty<=0 || bundle_size_qty=="")
			{
		    $(this).find('input[name="bundleSizeQty[]"]').css({"background-color":"red"});
			error=1;
			}
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
			
			var hiddenCountryType=''; var hiddenCountry='';
			if(size_wise_repeat_cut_no==1)
			{
				hiddenCountryType=$(this).find('input[name="hiddenCountryType[]"]').val();
				hiddenCountry=$(this).find('input[name="hiddenCountry[]"]').val();
			}
			
			j++;
			tot_row++;
			dataString_bundle+='&txtBundleNo_' + j + '=' + bundle_no + '&txtBundleQty_' + j + '=' + bundle_size_qty + '&txtBundleFrom_' + j + '=' + bundle_from+ '&txtBundleTo_' + j + '=' + bundle_to+ '&txtSizeId_' + j + '=' + bundle_size_id+ '&txtHiddenSizeId_' + j + '=' + hidden_size_id+ '&hiddenSizeqty_' + j + '=' + hidden_size_qty+ '&hiddenUpdateFlag_' + j + '=' + hidden_update_flag+'&hiddenUpdateValue_' + j + '=' + hiddenUpdateValue+'&hiddenCountryType_' + j + '=' + hiddenCountryType+'&hiddenCountry_' + j + '=' + hiddenCountry;
		});
		

		var bundle_mst_id=$("#hidden_mst_id").val();
		var bundle_dtls_id=$("#hidden_detls_id").val();
		//alert(bundle_dtls_id);return;
		var hidden_cutting_no=$("#hidden_cutting_no").val();
		var data="action=save_update_delete_bundle&operation="+operation+'&tot_row='+tot_row+'&bundle_dtls_id='+bundle_dtls_id+'&bundle_mst_id='+bundle_mst_id+dataString_bundle+'&hidden_cutting_no='+hidden_cutting_no;
		//alert(data);return;hidden_cutting_no
		freeze_window(operation);
		http.open("POST","cut_and_lay_entry_controller.php",true);
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
			set_button_status(1, permission, 'fnc_cut_lay_bundle_info',1);	
			var size_wise_repeat_cut_no=<? echo $size_wise_repeat_cut_no; ?>;
			show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3]+'**'+size_wise_repeat_cut_no,'show_bundle_list_view','search_div','cut_and_lay_entry_controller','setFilterGrid("list_view",-1)');
		}
		release_freezing();	
	}
}

 
function fnc_cut_lay_bundle_info_country(operation) 
{ 
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		var dataString_bundle="";  
		var size_wise_repeat_cut_no=<? echo $size_wise_repeat_cut_no; ?>;
		var j=0; var z=0; var tot_row=0; var sl=0; var error=0;
		
		$("#tbl_bundle_list_save").find('tbody tr').each(function()
		 {
			var bundle_no=($(this).find('input[name="bundleNo[]"]').val()).match("/");
			if(bundle_no=="/")
			{
				 var bundle_break=($(this).find('input[name="bundleNo[]"]').val()).split('/');
				 if(bundle_break[1]=="")
				 {
				  $(this).find('input[name="bundleNo[]"]').css({"background-color":"red"});
				  error=1;
				 }
			}
			var bundle_size_qty=$(this).find('input[name="bundleSizeQty[]"]').val();
		    if(bundle_size_qty<=0 || bundle_size_qty=="")
			{
		    $(this).find('input[name="bundleSizeQty[]"]').css({"background-color":"red"});
			error=1;
			}
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
			var hiddenCountryType=$(this).find('input[name="hiddenCountryTypeB[]"]').val();
			var cboCountry=$(this).find('select[name="cboCountryB[]"]').val();
			var hiddenCountry=$(this).find('input[name="hiddenCountryB[]"]').val();
			
			j++;
			tot_row++;
			dataString_bundle+='&txtBundleNo_' + j + '=' + bundle_no + '&txtBundleQty_' + j + '=' + bundle_size_qty + '&txtBundleFrom_' + j + '=' + bundle_from+ '&txtBundleTo_' + j + '=' + bundle_to+ '&txtSizeId_' + j + '=' + bundle_size_id+ '&txtHiddenSizeId_' + j + '=' + hidden_size_id+ '&hiddenSizeqty_' + j + '=' + hidden_size_qty+ '&hiddenUpdateFlag_' + j + '=' + hidden_update_flag+'&hiddenUpdateValue_' + j + '=' + hiddenUpdateValue+'&hiddenCountryType_' + j + '=' + hiddenCountryType+'&hiddenCountry_' + j + '=' + hiddenCountry+'&cboCountry_' + j + '=' + cboCountry ;
		});
		

		var bundle_mst_id=$("#hidden_mst_id").val();
		var bundle_dtls_id=$("#hidden_detls_id").val();
		//alert(bundle_dtls_id);return;
		var hidden_cutting_no=$("#hidden_cutting_no").val();
		var data="action=save_update_delete_bundle_country&operation="+operation+'&tot_row='+tot_row+'&bundle_dtls_id='+bundle_dtls_id+'&bundle_mst_id='+bundle_mst_id+dataString_bundle+'&hidden_cutting_no='+hidden_cutting_no;
		//alert(data);return;//hidden_cutting_no
		freeze_window(operation);
		http.open("POST","cut_and_lay_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_cut_lay_bundle_info_country_reply;
}

function fnc_cut_lay_bundle_info_country_reply()
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
			set_button_status(1, permission, 'fnc_cut_lay_bundle_info_country',1);	
			var size_wise_repeat_cut_no=<? echo $size_wise_repeat_cut_no; ?>;
			show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3]+'**'+size_wise_repeat_cut_no,'show_bundle_list_view','search_div','cut_and_lay_entry_controller','setFilterGrid("list_view",-1)');
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
	var page_link = 'cut_and_lay_entry_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var prodID=this.contentDoc.getElementById("txt_selected_id").value;
		data=data+'***'+prodID;
		//var url=return_ajax_request_value(data, "print_barcode_operation", "cut_and_lay_gmts_no_wise_entry_controller");
		http.open( 'POST', 'cut_and_lay_entry_controller.php?action=print_qrcode_operation&data='+ data );

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
<div id="msg_box_popp"  style=" height:15px; width:200px;  position:relative; left:250px "></div>
<div align="center" style="width:100%; overflow-y:hidden; position:absolute; top:5px;">
	<input type="hidden" id="hidden_cutting_no" name="hidden_cutting_no" value="<? echo $cutting_no; ?>" />

		<? echo load_freeze_divs ("../../../",$permission); 
		//$size_wise_repeat_cut_no=1;
		if($size_wise_repeat_cut_no==1)
		{
			$color_name=$color_arr[$cbo_color_id]; 
			$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
			$sql_bundle=return_field_value("max(size_qty) as size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =$details_id ","size_qty");  
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
                                	<input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<?php echo $txt_piles; ?>" readonly/>
                                </td>
                                <td class="must_entry_caption"><strong>Pcs Per Bundle</strong></td>
                                <td><input type="text" style="width:50px" class="text_boxes_numeric"  name="txt_bundle_pcs" id="txt_bundle_pcs" value="<? echo $sql_bundle; ?>" /></td>
                            </tr>
                        </thead>
                    </table>
				</fieldset>
            	<br/>
				<fieldset style="width:500px;">
				<?
					$sql_query=sql_select("SELECT country_type, country_id, size_number_id, plan_cut_qnty, country_ship_date from wo_po_color_size_breakdown where item_number_id=$cbo_gmt_id and po_break_down_id=$order_id and color_number_id=$cbo_color_id and status_active=1 and is_deleted=0 order by id");
                    $size_details=array(); $sizeId_arr=array(); $shipDate_arr=array(); $po_country_array=array(); $sizeDateArr=array();
                    foreach($sql_query as $row)
                    {
                        //if($row[csf('country_type')]==1) $country_id=0; else $country_id=$row[csf('country_id')];
                        $country_id=$row[csf('country_id')];
                        $size_details[$row[csf('size_number_id')]][$row[csf('country_type')]][$country_id]+=$row[csf("plan_cut_qnty")];
						//$size_details[$row[csf('country_type')]][$country_id][$row[csf('size_number_id')]]+=$row[csf("plan_cut_qnty")];
                        $sizeId_arr[$row[csf('size_number_id')]]+=$row[csf("plan_cut_qnty")];
						$shipDate_arr[$row[csf('country_type')]][$country_id]=$row[csf("country_ship_date")];
						$po_country_array[$country_id]=$country_arr[$country_id];
						
						$sizeDateArr[$row[csf('size_number_id')]][$row[csf('country_type')]][$country_id]=change_date_format($row[csf("country_ship_date")], "yyyy-mm-dd", "-");
                    }
					
					$size_wise_arr=array();
                    $sizeWiseData=sql_select("select size_ratio, size_id, marker_qty from ppl_cut_lay_size_dtls where mst_id=".$mst_id." and dtls_id=".$details_id." and status_active=1");
                    foreach($sizeWiseData as $value)
					{
						$size_wise_arr[$value[csf('size_id')]]['ratio']=$value[csf('size_ratio')];
						$size_wise_arr[$value[csf('size_id')]]['marker_qty']=$value[csf('marker_qty')];
					}
					//echo $size_wise_arr[531]['marker_qty']."<br>";	
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
                    $lay_blData=sql_select("select sum(a.marker_qty) as marker_qty, a.country_type, a.country_id, a.size_id from ppl_cut_lay_size a, ppl_cut_lay_dtls b where b.id=a.dtls_id and b.order_id=$order_id and b.gmt_item_id=$cbo_gmt_id and a.color_id=$cbo_color_id and a.status_active=1 and a.is_deleted=0 group by a.country_type,a.country_id, a.size_id");
					//echo "select sum(a.marker_qty) as marker_qty, a.country_type, a.country_id, a.size_id from ppl_cut_lay_size a, ppl_cut_lay_dtls b where b.id=a.dtls_id and b.order_id=$order_id and b.gmt_item_id=$cbo_gmt_id and a.color_id=$cbo_color_id and a.status_active=1 and a.is_deleted=0 group by a.country_type,a.country_id, a.size_id";
                    foreach($lay_blData as $value)
                    {
                        $lay_bl_qty_arr[$value[csf('country_type')]][$value[csf('country_id')]][$value[csf('size_id')]]=$value[csf('marker_qty')];
						$lay_bl_qty_size_arr[$value[csf('size_id')]]+=$value[csf('marker_qty')];
                    }
                    ?>
					<table cellpadding="0" cellspacing="0" width="370" id="tbl_size">
                        <thead class="form_table_header">
                            <th>Size</th>
                            <th>Lay Balance</th>
                            <th>CAD Size Ratio</th>
                            <th>Marker Qty</th>
                        </thead>
                        <tbody>
                        <?
							//print_r($sizeId_arr);
							$i=1; $total_layf_balance=0; $total_markerf_qty=0; $total_sizef_ratio=0;
							foreach($sizeId_arr as $size_id=>$plan_cut_qty)
							{
								$lay_balance=$plan_cut_qty-$lay_bl_qty_size_arr[$size_id]+$size_wise_arr[$size_id]['marker_qty'];
								$total_layf_balance+=$lay_balance;
								$data=explode("**",$sizeDaraArr[$country_type_id][$country_id][$size_id]);
								$total_markerf_qty+=$size_wise_arr[$size_id]['marker_qty'];
								$total_sizef_ratio+=$size_wise_arr[$size_id]['ratio'];	
												
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
									</td>
								</tr>
							<?
								$i++;
							}
						?>
                        </tbody>
                        <tfoot>
                            <tr class="form_table_header">
                                <th>Total</th>
                                <th align="right"><? echo $total_layf_balance; ?></th>
                                <th id="total_sizef_ratio" align="right"><? echo $total_sizef_ratio; ?></th>
                                <th id="total_sizef_qty" align="right"><? echo $total_markerf_qty; ?>
                                <input type='hidden' id="hidden_size_marker_qty" name="hidden_size_marker_qty" value="<? echo $total_markerf_qty; ?>"/></th>
                            </tr>
                        </tfoot>
					</table>
                    <br>
					<table cellpadding="0" cellspacing="0" width="600" class="" id="tbl_size_details">
                        <thead class="form_table_header">
                            <th>Country Type</th>
                            <th>Country Name</th>
                            <th>Country Ship. Date</th>
                            <th>Size</th>
                            <th>Lay Balance</th>
                            <th>CAD Size Ratio</th>
                            <th>Marker Qty</th>
                            <th>Bundle Priority</th>
                            <th></th>
                        </thead>
                        <tbody>
                        <?
						$i=1; $total_lay_balance=0; $total_marker_qty=0; $total_size_ratio=0;
						foreach($sizeDateArr as $size_id=>$country_val)
					  	{//country_type_id=>$country_val
							foreach($country_val as $country_type_id=>$country_data)
							{
								// asort($country_data);
								foreach($country_data as $country_id=>$shipDate)
								{
									$plan_cut_qnty=$size_details[$size_id][$country_type_id][$country_id];
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
										<td align="center">				
											<input type="text" style="width:45px" class="text_boxes_numeric" onKeyUp="check_size_qty(<? echo $txt_piles; ?>,this.value,this.id)" name="txt_size_ratio_<? echo $i; ?>" id="txt_size_ratio_<? echo $i; ?>" value="<? echo $data[0]; ?>" disabled  />	
										</td>
										<td align="center">				
											<input type="text" style="width:50px" class="text_boxes_numeric"  name="txt_size_qty_<? echo $i; ?>" id="txt_size_qty_<? echo $i; ?>"  value="<? echo $data[1]; ?>" onKeyUp="sum_size_qty_adjust(<? echo $txt_piles; ?>,<? echo $size_id; ?>,<? echo $i; ?>)" readonly />	
										</td>
										<td align="center">				
											<input type="text" style="width:40px" class="text_boxes_numeric"  name="txt_bundle_<? echo $i; ?>" id="txt_bundle_<? echo $i; ?>" onKeyUp="sequence_duplication_check(<? echo $i; ?>)" value="<? echo $data[2]; ?>" />	
										</td>
                                        <td>
                                        <input type="button"  value="Adj" name="buttonRatio[]" id="buttonRatio_<? echo $i;  ?>" style="width:50px" class="formbuttonplasminus" onClick="fnc_adjust_ratio(<? echo $i;  ?>)"/>
                                        </td>						
									</tr>
									<?
									$i++;
								}
							}
					  	}
                       ?>
                        </tbody>
                        <tfoot>
                            <tr class="form_table_header">
                                <th colspan="4">Total</th>
                                <th align="right"><? echo $total_lay_balance; ?></th>
                                <th id="total_size_ratio" align="right"><? echo $total_size_ratio; ?></th>
                                <th id="total_size_qty" align="right"><? echo $total_marker_qty; ?></th>
                                <th><input type='hidden' id="hidden_marker_qty" name="hidden_marker_qty"  value="<? echo $total_marker_qty; ?>"/></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td align="center" valign="middle" colspan="7" >
                                    <? echo load_submit_buttons( $permission, "fnc_cut_lay_size_info", $is_update,0,"clear_size_form()",1);?>
                                </td>
                                
                            </tr>
                            <tr>
                                <td align="center"  colspan="7" >
                                    <input type="button" id="close_size_id" name="close_size_id"  class="formbutton"  style="width:50px" onClick="size_popup_close(<? echo $size; ?>,$('#hidden_marker_qty').val(),$('#hidden_plant_qty').val(),$('#hidden_total_marker').val(),$('#hidden_lay_balance').val())" value="Close"/>
                                    <input type="button" id="btn_print" name="btn_print" value=" Bundle List" class="formbutton" onClick="fnc_print_bundle()"/>
                                    <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 6/page" class="formbutton" onClick="fnc_bundle_report_eight(6;)"/>
                                    <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 8/page" class="formbutton" onClick="fnc_bundle_report_eight(8);"/>
                                     <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 10/page" class="formbutton" onClick="fnc_bundle_report_ten();"/>
                                     <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 1/Page" class="formbutton" onClick="fnc_bundle_report_one();"/>
                                    <input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text();"/>
                                    <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="BPKW 10/page" class="formbutton" onClick="fnc_bundle_report_ten_bpkw();"/>
                                    <input type="button" id="btn_stiker_qr" name="btn_stiker_qr" value="QR" class="formbutton" onClick="fnc_bundle_report_qr_code();"/>
                                    
                                    <input type='hidden' id="hidden_marker_no_x" name="hidden_marker_no_x"  />
                                    <input type='hidden' id="hidden_total_marker" name="hidden_total_marker"  />
                                    <input type='hidden' id="hidden_lay_balance" name="hidden_lay_balance"  />
                                    <input type='hidden' id="hidden_plant_qty" name="hidden_plant_qty"  />
                                </td>
                            </tr>
                        </tfoot>
					</table>
                </fieldset>
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
                    $bundle_data=sql_select("SELECT a.id,a.bundle_no,a.barcode_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value, a.country_type, a.country_id from ppl_cut_lay_bundle a where a.mst_id=".$mst_id." and a.dtls_id=".$details_id." order by a.id ASC");
                    if(count($bundle_data)>0)
                    {
                    ?>
                        <fieldset style="width:800px">
                            <legend>Bundle No and RMG qty details</legend>
                            <table cellpadding="0" cellspacing="0" width="780" class="rpt_table" id="tbl_bundle_list_save">
                                <thead class="form_table_header">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Bundle</th>
                                    <th colspan="2">RMG Number</th>
                                    <th>
                                        <input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />  
                                        <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $details_id; ?>" />
                                    </th>
                                    <th></th>
                                    <th>Report &nbsp;</th>
                                </thead>
                                <thead class="form_table_header">
                                    <th width="50">SL No</th>
                                    <th width="80">Country Type</th>
                                    <th width="80">Country Name</th>
                                    <th width="110">Bundle No</th>
                                    <th width="80">Quantity</th>
                                    <th width="80">From</th>
                                    <th width="80">To</th>
                                    <th width="80" rowspan="2" >Size</th>
                                    <th width="100" rowspan="2" ></th>
                                    <th width="60"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
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
                                        <td align="center"  id=""><input type="text" id="sirialNo_<? echo $i;  ?>" name="sirialNo[]" style="width:30px;" class="text_boxes"  value="<? echo $i;  ?>"  disabled/>
                                        <input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]"  value="<? echo $i;  ?>"style="width:30px;" />                   
                                        <input type="hidden" id="hiddenUpdateFlag_<? echo $i;  ?>" name="hiddenUpdateFlag[]"  value="<? echo $row[csf('update_flag')];?> " style="width:30px;" />
                                        <input type="hidden" id="hiddenUpdateValue_<? echo $i;  ?>" name="hiddenUpdateValue[]"  value="<? echo $row[csf('update_value')];?> " style="width:30px;" />
                                        </td>
                                        <td align="center">	
                                            <?
                                                echo create_drop_down( "cboCountryTypeB_".$i, 80, $country_type,'', 0, '',$row[csf('country_type')],'',1); 
                                            ?>
                                             <input type="hidden" id="hiddenCountryTypeB_<? echo $i;  ?>" name="hiddenCountryTypeB[]"  value="<? echo $row[csf('country_type')];?> "/> 
                                        </td> 
                                        <td align="center">	
                                             <?
                                                echo create_drop_down("cboCountryB_".$i,80,$po_country_array,'',0,'',$row[csf('country_id')],'',1,'','','','','','','cboCountryB[]');  
                                            ?> 
                                             <input type="hidden" id="hiddenCountryB_<? echo $i;  ?>" name="hiddenCountryB[]"  value="<? echo $row[csf('country_id')];?> " />
                                        </td>
                                        <td align="center" title="<? echo $row[csf('barcode_no')];?>" >
                                        <input type="text" name="bundleNo[]" id="bundleNo_<? echo $i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes"  style="width:70px;  text-align:center" disabled/>
                                        </td>
                                        <td align="center">
                                        <input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onKeyUp="bundle_calclution(<? echo $i;  ?>,<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right; <? if($update_f_value[0]!="") echo "background-color:#F3F;"; ?>"  class="text_boxes"  disabled/>
                                        <input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<? echo $i;  ?>" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right" class="text_boxes"  disabled/>
                                        </td>
                                        <td align="center">
                                        <input type="text" name="rmgNoStart[]" id="rmgNoStart_<? echo $i;  ?>" value="<? echo $row[csf('number_start')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled />
                                        </td>
                                        <td align="center">
                                        <input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<? echo $i;  ?>" value="<? echo $row[csf('number_end')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled/>
                                        
                                        </td>
                                        <td align="center" id="update_sizename_<? echo $i;  ?>">
                                        <select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:70px; text-align:center;  <? if($update_f_value[1]!="") echo "background-color:#F3F;"; ?> "  disabled  >
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
                                        <td align="center">
                                        <input type="button"  value="+" name="addButton[]" id="addButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<? echo $i;  ?>','<? echo $i;  ?>')"/>
                                        <input type="button"  value="Adj." name="rowUpdate[]" id="rowUpdate_<? echo $i;  ?>" style="width:50px" class="formbuttonplasminus" onClick="fnc_updateRow('<? echo $i;  ?>')"/>
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
                                    <td colspan="10" align="center" class="button_container">
                                        <? echo load_submit_buttons( $permission, "fnc_cut_lay_bundle_info_country", 1,0,"clear_size_form()",1);?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
					<?
                    }
                    ?>
                </div>  
			</form>
        	<?	
		}
		else
		{
			$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');        
			$sql_update=sql_select("select  a.id,a.size_id,a.size_ratio,a.marker_qty,a.bundle_sequence from ppl_cut_lay_size a where a.mst_id=$mst_id and a.dtls_id=$details_id and a.color_id=$cbo_color_id order by  a.id");
			
			$k=1;
			foreach($sql_update as $value)
			{
			  $update_data[$value[csf('size_id')]]['size_id']=$value[csf('size_id')];
			  $update_data[$value[csf('size_id')]]['id']=$value[csf('id')];	
			  $update_data[$value[csf('size_id')]]['size_ratio']=$value[csf('size_ratio')];	
			  $update_data[$value[csf('size_id')]]['marker_qty']=$value[csf('marker_qty')];	
			  $update_data[$value[csf('size_id')]]['bundle_sequence']=$value[csf('bundle_sequence')];	
			
				$k++;
			}
			
			if(count($sql_update)>0)
			{
				$sql_bundle=return_field_value("size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =$details_id ");
				?>
		   <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off" >
			 <fieldset style="width:450px;">
			  
				<table cellpadding="2" cellspacing="0" width="450" class="" id="tbl_bundle_size">
				   <thead>
					   <tr >
							 <td><strong>Color</strong></td>
							 <td><input type="text" style="width:80px" class="text_boxes"  name="txt_show_color" id="txt_show_color" 
							 value="<?php echo $color_arr[$cbo_color_id]; ?>" readonly/>
								 <input type="hidden" id="hidden_update_job_id" name="hidden_update_job_id"   value="<? echo $job_id; ?>"/>
								 <input type="hidden" id="hidden_update_cut_no" name="hidden_update_cut_no"   value="<? echo $cutting_no; ?>"/>
							 </td>
							 <td><strong>Plies</strong></td>
							 <td><input type="text" style="width:80px" class="text_boxes"  name="txt_search_common" id="txt_search_common" 
							 value="<?php echo $txt_piles; ?>" readonly/></td>
							 <td class="must_entry_caption"><strong>Pcs Per Bundle</strong></td>
							 <td><input type="text" style="width:50px" class="text_boxes_numeric"  name="txt_bundle_pcs" id="txt_bundle_pcs"  value="<? echo $sql_bundle; ?>"/></td>
						  </tr>
					 </thead>
				</table>
				</fieldset>
				<br/>
			  <fieldset style="width:500px;">
				<table cellpadding="0" cellspacing="0" width="500" class="" id="tbl_size_details">
					<thead class="form_table_header">
							<th>Size</th>
							<th>Lay Balance</th>
							<th>CAD Size Ratio</th>
							<th>Marker Qty</th>
							<th>Bundle Priority</th>
					</thead>
					   <tbody >
						<?
						$i=1;
						$total_marker_qty=0;
						$total_lay_balance=0;
						
				 $sql_query=sql_select("select id,country_ship_date,size_number_id, sum(plan_cut_qnty) as plan_qty from wo_po_color_size_breakdown  where item_number_id=$cbo_gmt_id  and po_break_down_id=$order_id and  color_number_id=$cbo_color_id and status_active=1 group by id,country_ship_date,po_break_down_id,item_number_id,color_number_id,size_number_id order by id,country_ship_date ");
					foreach($sql_query as $data_val)
								{
									$country_ship_ditals[$data_val[csf("size_number_id")]]['size']=$data_val[csf("size_number_id")];
									$country_ship_ditals[$data_val[csf("size_number_id")]]['plan_qty']+=$data_val[csf("plan_qty")];
								}
				
					foreach($country_ship_ditals as $val)
					{
						 $size_qty_arr=sql_select("select sum(a.marker_qty) as marker_total from  ppl_cut_lay_size a, ppl_cut_lay_dtls b where b.color_id=a.color_id  and b.id=a.dtls_id and b.order_id=$order_id and b.gmt_item_id=$cbo_gmt_id   and  a.color_id=$cbo_color_id  and a.size_id=".$val['size']."");
						 if(count($size_qty_arr)>0)
							{
								foreach($size_qty_arr as $value)
								{
								$lay_balance=$val['plan_qty']-$value[csf('marker_total')]+$update_data[$val['size']]['marker_qty'];	
								}
							}
					   else
							{
				
							}
							$size_name=$size_arr[$val['size']];
							$size_id=$val['size'];
							$sql_size_details=sql_select("select  a.id,a.size_id,a.size_ratio,a.marker_qty,a.bundle_sequence from ppl_cut_lay_size a where a.mst_id=$mst_id and a.dtls_id=$details_id and a.color_id=$cbo_color_id  and a.size_id=".$val['size']."");
							
							$size_ratio=''; 
							$marker_qty='';
							$bundle_sequence='';
							$update_size_id='';
							foreach($sql_size_details as $size_val)
							{
								
								if($size_val[csf('size_id')]!="")
								  {
									  $update_size_id=$size_val[csf('id')];
									  $size_ratio=$size_val[csf('size_ratio')];
									  $total_size_retio_update+=$size_ratio;
									  $marker_qty=$size_val[csf('size_ratio')]*$txt_piles; 
									  if($size_val[csf('bundle_sequence')]!=0)
										 {
										  $bundle_sequence=$size_val[csf('bundle_sequence')];
										 }
									 $total_marker_qty+=$marker_qty;
								  }
						  else
								  {
									  $update_size_id='';
									  $size_ratio=''; 
									  $marker_qty='';
									  $bundle_sequence='';    
								  }
							}
					
						$total_lay_balance=$total_lay_balance+$lay_balance;	
						?>
						
						 <tr class="" id="gsd_<? echo $i; ?>">
								<td align="center">	
									<input type="text" style="width:80px" class="text_boxes"  name="txt_size_<? echo $i; ?>" id="txt_size_<? echo $i; ?>" value="<? echo $size_name; ?>" />
									<input type="hidden" id="hidden_size_id_<? echo $i; ?>" name="hidden_size_id_<? echo $i; ?>" value="<?  echo $size_id; ?>">
									<input type="hidden" id="update_size_id_<? echo $i; ?>" name="update_size_id_<? echo $i; ?>" value="<?  echo $update_size_id; ?>">
									
								</td>                 
								<td align="center">				
									<input type="text" style="width:80px" class="text_boxes_numeric"  name="txt_lay_balance_<? //echo $i; ?>" id="txt_lay_balance_<? echo $i; ?>"  value="<? echo $lay_balance; ?>" />
								</td> 
								<td align="center">				
									<input type="text" style="width:80px" class="text_boxes_numeric" onKeyUp="check_size_qty(<? echo $txt_piles; ?>,this.value,this.id)" name="txt_size_ratio_<? echo $i; ?>" id="txt_size_ratio_<? echo $i; ?>" value="<? echo $size_ratio; ?>" />	
								</td>
								<td align="center">				
									<input type="text" style="width:80px" class="text_boxes_numeric"  name="txt_size_qty_<? echo $i; ?>" id="txt_size_qty_<? echo $i; ?>"  value="<? echo $marker_qty; ?>" />	
								</td>
								<td align="center">				
									<input type="text" style="width:60px" class="text_boxes_numeric"  name="txt_bundle_<? echo $i; ?>" id="txt_bundle_<? echo $i; ?>" value="<? echo $bundle_sequence; ?>"/>	
								</td>						
						  </tr>
				   <?
						$i++;
						}
				?>
					  </tbody>
				 <tfoot >
					   <tr class="form_table_header">
							<th>Total</th>
							<th align="right"><? echo $total_lay_balance; ?></th>
							<th id="total_size_ratio" align="right" ><? echo $total_size_retio_update; ?></th>
							<th id="total_size_qty" align="right"> <? echo $total_marker_qty; ?></th>
							<th><input type='hidden' id="hidden_marker_qty" name="hidden_marker_qty"  value="<? echo $total_marker_qty; ?>"/>
							</th>
					 </tr>
					 <tr>
						<td align="center" colspan="5">
						   <? echo load_submit_buttons( $permission, "fnc_cut_lay_size_info", 1,0,"clear_size_form()",1);?>
						   </td>
					  </tr>
					  <tr>
						<td align="center" colspan="5">
							 <input type="button" id="close_size_id" width="100px" name="close_size_id"  class="formbutton" style="width:60px" onClick="size_popup_close(<? echo $size; ?>,$('#hidden_marker_qty').val(),$('#hidden_plant_qty').val(),$('#hidden_total_marker').val(),$('#hidden_lay_balance').val());" value="Close"/>
							<input type="button" id="btn_print" name="btn_print" value=" Bundle List" class="formbutton"  onClick="fnc_print_bundle();"/>
							<input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 6/page" class="formbutton" onClick="fnc_bundle_report_eight(6);"/>
                            <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value=" Bundle Sticker 8/page" class="formbutton" onClick="fnc_bundle_report_eight(8);"/>
                            
                            <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 10/page" class="formbutton" onClick="fnc_bundle_report_ten();"/>
                            <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 1/Page" class="formbutton" onClick="fnc_bundle_report_one();"/>
                            
                           <input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text();"/> <!--fnc_send_printer()-->
                           <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="BPKW 10/page" class="formbutton" onClick="fnc_bundle_report_ten_bpkw();"/>
                           <input type="button" id="btn_stiker_qr" name="btn_stiker_qr" value="QR" class="formbutton" onClick="fnc_bundle_report_qr_code();"/>
							<input type='hidden' id="hidden_marker_no_x" name="hidden_marker_no_x"  value="<? echo $marker_quantity; ?>" />
							<input type='hidden' id="hidden_total_marker" name="hidden_total_marker" value="<? echo $total_lay_qty; ?>"  />
							<input type='hidden' id="hidden_lay_balance" name="hidden_lay_balance" value="<? echo $total_lay_balance; ?>"  />
							<input type='hidden' id="hidden_plant_qty" name="hidden_plant_qty"  value="<? echo $order_quantity; ?>" />
							   
						</td>
				 </tr>
				</tfoot>
		   </table>
		 </fieldset>
		   <br/>
	
		   <div id="search_div" style="margin-top:10px">
			  <fieldset style="width:750px">
			  <legend>Bundle No and RMG qty details</legend>
				<table cellpadding="0" cellspacing="0" width="700" class="rpt_table" id="tbl_bundle_list_save">
							<thead class="form_table_header">
								  <th ></th>
								  <th ></th>
								  <th >Bundle</th>
								  <th  colspan="2">RMG Number</th>
								  <th ><input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />  
									   <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $details_id; ?>" />
								  </th>
								  <th ></th>
								  <th >Report &nbsp;</th>
							</thead>
							<thead class="form_table_header">
								  <th width="80">SL No</th>
								  <th width="140">Bundle No</th>
								  <th width="80">Quantity</th>
								  <th width="80">From</th>
								  <th width="80">To</th>
								  <th width="80" rowspan="2" >Size</th>
								  <th width="100" rowspan="2" ></th>
								  <th  width="60"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
							</thead>
							<tbody id="trBundleListSave">
							<?
							
							   $sql_size_name=sql_select("select size_id from  ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$details_id."");
							   $size_colour_arr=array();
							   foreach($sql_size_name as $asf)
								{
								  $size_colour_arr[$asf[csf("size_id")]]=$size_arr[$asf[csf("size_id")]];	
								}
								//print_r($size_colour_arr);
								$bundle_data=sql_select("select DISTINCT a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$mst_id." and a.dtls_id=".$details_id."  order by a.id ASC");
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
									   <td align="center"  id=""><input type="text" id="sirialNo_<? echo $i;  ?>" name="sirialNo[]" style="width:40px;" class="text_boxes"  value="<? echo $i;  ?>"  disabled/>
									   <input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]"  value="<? echo $i;  ?>"style="width:30px;" />                   
									  <input type="hidden" id="hiddenUpdateFlag_<? echo $i;  ?>" name="hiddenUpdateFlag[]"  value="<? echo $row[csf('update_flag')];?> " style="width:30px;" />
									   <input type="hidden" id="hiddenUpdateValue_<? echo $i;  ?>" name="hiddenUpdateValue[]"  value="<? echo $row[csf('update_value')];?> " style="width:30px;" />
									   </td>
									   <td align="center">
									   <input type="text" name="bundleNo[]" id="bundleNo_<? echo $i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes"  style="width:80px;  text-align:center" disabled/>
									   </td>
									   <td align="center">
									   <input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onKeyUp="bundle_calclution(<? echo $i;  ?>,<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right; <? if($update_f_value[0]!="") echo "background-color:#F3F;"; ?>"  class="text_boxes"  disabled/>
										   <input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<? echo $i;  ?>" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right" class="text_boxes"  disabled/>
									   </td>
									   <td align="center">
										<input type="text" name="rmgNoStart[]" id="rmgNoStart_<? echo $i;  ?>" value="<? echo $row[csf('number_start')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled />
										</td>
									   <td align="center">
									   <input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<? echo $i;  ?>" value="<? echo $row[csf('number_end')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled/>
										
									   </td>
									   <td align="center" id="update_sizename_<? echo $i;  ?>">
										<select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:100px; text-align:center;  <? if($update_f_value[1]!="") echo "background-color:#F3F;"; ?>    "  disabled  >
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
									   <td align="center">
									   <input type="button"  value="+" name="addButton[]" id="addButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<? echo $i;  ?>','<? echo $i;  ?>')"/>
										<input type="button"  value="Adj." name="rowUpdate[]" id="rowUpdate_<? echo $i;  ?>" style="width:50px" class="formbuttonplasminus" onClick="fnc_updateRow('<? echo $i;  ?>')"/>
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
				 <table cellpadding="0" cellspacing="0" width="700" class="rpt_table">
					<tr>
						<td colspan="8" align="center" class="button_container">
							 <? echo load_submit_buttons( $permission, "fnc_cut_lay_bundle_info", 1,0,"clear_size_form()",1);?>
						</td>
					</tr>
				 </table>
			</fieldset>
		   
		   
		   </div>  
			</form>
				<?
			}
			else
			{
			?>
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			 <fieldset style="width:450px;">
				<table cellpadding="0" cellspacing="0" width="450" class="" id="tbl_bundle_size">
				   <thead>
					   <tr >
							 <td><strong>Color</strong></td>
							 <td><input type="text" style="width:80px" class="text_boxes"  name="txt_show_color" id="txt_show_color" 
							 value="<?php echo $color_arr[$cbo_color_id]; ?>" readonly/>
								 <input type="hidden" id="hidden_update_job_id" name="hidden_update_job_id"   value="<? echo $job_id; ?>"/>
								 <input type="hidden" id="hidden_update_cut_no" name="hidden_update_cut_no"   value="<? echo $cutting_no; ?>"/>
							 </td>
							 <td><strong>Plies</strong></td>
							 <td><input type="text" style="width:80px" class="text_boxes"  name="txt_search_common" id="txt_search_common" 
							 value="<?php echo $txt_piles; ?>" readonly/></td>
							 <td class="must_entry_caption"><strong>Pcs Per Bundle</strong></td>
							 <td><input type="text" style="width:50px" class="text_boxes_numeric"  name="txt_bundle_pcs" id="txt_bundle_pcs" /></td>
						  </tr>
					 </thead>
				</table>
			  </fieldset>
				
				<br/>
				<fieldset style="width:500px;">
				<table cellpadding="0" cellspacing="0" width="500" class="" id="tbl_size_details">
					<thead class="form_table_header">
							<th>Size</th>
							<th>Lay Balance</th>
							<th>CAD Size Ratio</th>
							<th>Marker Qty</th>
							<th>Bundle Priority</th>
					</thead>
					<tbody>
					<?
			
					$sql_query=sql_select("select id,country_ship_date,size_number_id, sum(plan_cut_qnty) as plan_qty from wo_po_color_size_breakdown  where item_number_id=$cbo_gmt_id  and po_break_down_id=$order_id and  color_number_id=$cbo_color_id and status_active=1 group by id,country_ship_date,po_break_down_id,item_number_id,color_number_id,size_number_id order by id,country_ship_date");
					$country_ship_ditals=array();
					foreach($sql_query as $data_val)
						{
							$country_ship_ditals[$data_val[csf("size_number_id")]]['size']=$data_val[csf("size_number_id")];
							$country_ship_ditals[$data_val[csf("size_number_id")]]['plan_qty']+=$data_val[csf("plan_qty")];
						}
					$i=1;
					$total_lay_balance=0;
					foreach($country_ship_ditals as $row)
					  {
					   $size_qty_arr=sql_select("select sum(a.marker_qty) as marker_total from  ppl_cut_lay_size a, ppl_cut_lay_dtls b where  b.id= a.dtls_id and b.order_id=$order_id and b.gmt_item_id=$cbo_gmt_id  and  a.color_id=$cbo_color_id  and a.size_id=".$row['size']." group by b.order_id,b.gmt_item_id,a.color_id,a.size_id");
						if(count($size_qty_arr)>0)
							{
								foreach($size_qty_arr as $value)
								{
								$lay_balance=$row['plan_qty']-$value[csf('marker_total')];	
								}
							}
					   else
							{
								$lay_balance=$row['plan_qty'];  
							  
							}
						$total_lay_balance=$total_lay_balance+$lay_balance;	
					?>
						 <tr class="" id="gsd_<? echo $i; ?>">
								<td align="center">	
								  <input type="text" style="width:80px" class="text_boxes"  name="txt_size_<? echo $i; ?>" id="txt_size_<? echo $i; ?>" value="<? echo $size_arr[$row['size']]; ?>" />
								  <input type="hidden" id="hidden_size_id_<? echo $i; ?>" name="hidden_size_id_<? echo $i; ?>" value="<? echo $row['size']; ?>">
								  <input type="hidden" id="update_size_id_<? echo $i; ?>" name="update_size_id_<? echo $i; ?>" value="">
								</td>                 
								<td align="center">				
									<input type="text" style="width:80px" class="text_boxes_numeric"  name="txt_lay_balance_<? echo $i; ?>" id="txt_lay_balance_<? echo $i; ?>"  value="<? echo $lay_balance; ?>" />
								</td> 
								<td align="center">				
									<input type="text" style="width:80px" class="text_boxes_numeric" onKeyUp="check_size_qty(<? echo $txt_piles; ?>,this.value,this.id)" name="txt_size_ratio_<? echo $i; ?>" id="txt_size_ratio_<? echo $i; ?>" />	
								</td>
								<td align="center">				
									<input type="text" style="width:80px" class="text_boxes_numeric"  name="txt_size_qty_<? echo $i; ?>" id="txt_size_qty_<? echo $i; ?>"  />	
								</td>
								<td align="center">				
									<input type="text" style="width:60px" class="text_boxes_numeric"  name="txt_bundle_<? echo $i; ?>" id="txt_bundle_<? echo $i; ?>" />	
								</td>						
						  </tr>
				   <?
					  $i++;
					  }
					
					?>
				 </tbody>
				 <tfoot>
					   <tr class="form_table_header">
							<th>Total</th>
							<th align="right"><? echo $total_lay_balance; ?></th>
							<th id="total_size_ratio" align="right"></th>
							<th id="total_size_qty" align="right"></th>
							<th><input type='hidden' id="hidden_marker_qty" name="hidden_marker_qty"  value="<? echo $total_marker_qty; ?>"/></th>
					   </tr>
						<tr>
						<td   align="center" valign="middle" colspan="5" >
							 <? echo load_submit_buttons( $permission, "fnc_cut_lay_size_info", 0,0,"clear_size_form()",1);?>
						 </td>
						 </tr>
						 <tr>
						 <td   align="center"  colspan="5" >
							  <input type="button" id="close_size_id" name="close_size_id"  class="formbutton"  style="width:50px" onClick="size_popup_close(<? echo $size; ?>,$('#hidden_marker_qty').val(),$('#hidden_plant_qty').val(),$('#hidden_total_marker').val(),$('#hidden_lay_balance').val());" value="Close"/>
							 <input type="button" id="btn_print" name="btn_print" value=" Bundle List" class="formbutton" onClick="fnc_print_bundle();"/>
							 <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 6/page" class="formbutton" onClick="fnc_bundle_report_eight(6);"/>
							 <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 8/page" class="formbutton" onClick="fnc_bundle_report_eight(8);"/>
                             <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 10/page" class="formbutton" onClick="fnc_bundle_report_ten();"/>
                             <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 1/Page" class="formbutton" onClick="fnc_bundle_report_one();;"/>
							<input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text();"/>
                            <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="BPKW 10/page" class="formbutton" onClick="fnc_bundle_report_ten_bpkw();"/>
                            <input type="button" id="btn_stiker_qr" name="btn_stiker_qr" value="QR" class="formbutton" onClick="fnc_bundle_report_qr_code();"/>

						   <!-- fnc_send_printer-->
							 <input type='hidden' id="hidden_marker_no_x" name="hidden_marker_no_x"  />
							 <input type='hidden' id="hidden_total_marker" name="hidden_total_marker"  />
							 <input type='hidden' id="hidden_lay_balance" name="hidden_lay_balance"  />
							 <input type='hidden' id="hidden_plant_qty" name="hidden_plant_qty"  />
					</td>
				   </tr>
					   
					   
				  </tfoot>
		   </table>
	   		</fieldset>
		   	<br/>
		   
		   <div id="search_div" style="margin-top:10px"></div>  
			</form>
		
			<?  
		} 
		}
	?>

</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//---------------------------bundle qty update---------------------------------------------------------------------------------

if ($action=="save_update_delete_bundle_country")
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
		$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","cutting_no='".$hidden_cutting_no."'");
		//echo $cutting_qc_no;die;
		//if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; die;}
		$id=return_next_id("id","ppl_cut_lay_bundle",1);
		$field_array="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,number_start,number_end,size_qty,update_flag,update_value,country_type,country_id,inserted_by,insert_date,status_active,is_deleted";
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
				$bundle_prif=explode("-",$$new_bundle_no);
				$new_bundle_prif_no=explode('/',$bundle_prif[2]);
				$new_bundle_prifix=$bundle_prif[0]."-".$bundle_prif[1];
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
					//echo $update_flag_value."***";die;
				if($data_array!="") $data_array.=",";
				$data_array.="(".$id.",".$bundle_mst_id.",".$bundle_dtls_id.",".$$new_bundle_size_id.",'".$new_bundle_prifix."','".$new_bundle_prif_no[0]."','".$$new_bundle_no."',".$$new_bundle_from.",".$$new_bundle_to.",".$$new_bundle_qty.",".$update_flag.",'".$update_flag_value."','".str_replace("'","",$$hiddenCountryType)."','".str_replace("'","",$$hiddenCountry)."','".$user_id."','".$pc_date_time."',1,0)";
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
		/*$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
			
		$nameArray=sql_select( "select id,product_id from inv_purchase_requisition_dtls where product_id=$update_id" ); 
		if($nameArray)
			{
			echo "13**";die;
			}
			
			$field_array="updated_by*update_date*status_active*is_deleted";
	    	$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$rID=sql_delete("product_details_master",$field_array,$data_array,"id","".$update_id."",1);
				
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;*/
	}		
}
//----------------------------------bundle qty update finish---------------------------------------------------------------------------------


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
		$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","cutting_no='".$hidden_cutting_no."'");
		//echo $cutting_qc_no;die;
		if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; die;}
		$id=return_next_id("id","ppl_cut_lay_bundle",1);
		$rID=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$bundle_mst_id." and dtls_id=".$bundle_dtls_id."",0);
		
		$field_array="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,number_start,number_end,size_qty,update_flag,update_value,country_type,country_id,inserted_by,insert_date,status_active,is_deleted";
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
				$hiddenCountry="hiddenCountry_".$j;
				$hiddenCountryType="hiddenCountryType_".$j;
				$bundle_prif=explode("-",$$new_bundle_no);
				$new_bundle_prif_no=explode('/',$bundle_prif[2]);
				$new_bundle_prifix=$bundle_prif[0]."-".$bundle_prif[1];
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
					//echo $update_flag_value."***";die;
				if($data_array!="") $data_array.=",";
				 $data_array.="(".$id.",".$bundle_mst_id.",".$bundle_dtls_id.",".$$new_bundle_size_id.",'".$new_bundle_prifix."','".$new_bundle_prif_no[0]."','".$$new_bundle_no."',".$$new_bundle_from.",".$$new_bundle_to.",".$$new_bundle_qty.",".$update_flag.",'".$update_flag_value."','".str_replace("'","",$$hiddenCountryType)."','".str_replace("'","",$$hiddenCountry)."','".$user_id."','".$pc_date_time."',1,0)";
				$id = $id+1;
			}
		//echo $data_array;die;	
		//echo "10**insert into ppl_cut_lay_bundle($field_array) values".$data_array;die;
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
		/*$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
			
		$nameArray=sql_select( "select id,product_id from inv_purchase_requisition_dtls where product_id=$update_id" ); 
		if($nameArray)
			{
			echo "13**";die;
			}
			
			$field_array="updated_by*update_date*status_active*is_deleted";
	    	$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			$rID=sql_delete("product_details_master",$field_array,$data_array,"id","".$update_id."",1);
				
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;*/
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
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[1] and entry_form=76");
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
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2] and entry_form=76");
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
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2] and entry_form=76");
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
	
	echo  create_list_view("tbl_list_search", "Bundle Use For", "200","240","180",0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);
    echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();	
}
	
if($action=="print_barcode_eitht")
{	
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	//echo $data;die;
	$data=explode("***",$data);
	$detls_id=$data[3];
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	// $sub_process_id=return_field_value2("group_concat(b.sub_process_id)  as sub_process_id ","pro_recipe_entry_dtls b, pro_recipe_entry_mst a",
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls"," id=$detls_id and status_active=1 and is_deleted=0 ","order_cut_no");
	//echo $order_cut_no;die;
	
	$pdf=new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty,country_id from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //w
	
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
	
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2] and entry_form=76");
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
	 
	// echo "select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])";
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
						$pdf->Code39($i+55, $j-4, "Bundle Card ", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+55, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
						$pdf->Code39($i, $j+6, "Cut Sys No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+6, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+11,  "Buyer : ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+11,"Ord:".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+16,  "Style Ref  :  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+21, "Item :  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						//$pdf->Code39($i+40, $j+21, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+21, "Size :  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
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
					{  $k=0; $i=10; $j=$j+75; }
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
						$pdf->Code39($i+55, $j-4, "Bundle Card ", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+55, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
						$pdf->Code39($i, $j+6, "Cut Sys No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+6, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+11,  "Buyer : ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+11,"Ord:".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+16,  "Style Ref  :  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+21, "Item :  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						//$pdf->Code39($i+40, $j+21, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+21, "Size :  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
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
					{  $k=0; $i=10; $j=$j+75; }
					$br++;
				
				} 
		}
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}

	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	//ini_set('display_errors', 1);
	$pdf->Output( $name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_ten")
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
	$order_cut_no=return_field_value(" order_cut_no "," ppl_cut_lay_dtls"," id=$detls_id and status_active=1 and is_deleted=0 ","order_cut_no");
	//echo $order_cut_no;
	
	$pdf=new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty,country_id from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //w
	
	$i=10; $j=8; $k=0;
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
	
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2] and entry_form=76");
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
	// echo "select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])";
		 if($data[7]=="") $data[7]=0;
    	 $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
		 $cope_page=1;
		 if(count($sql_bundle_copy)!=0)
		 {
		 foreach($color_sizeID_arr as $val)
		 {
			if($br==10) { $pdf->AddPage(); $br=0; $i=10; $j=8; $k=0; }
			foreach($sql_bundle_copy as $inf)
			   {
					if($br==10) 
					{
						 $pdf->AddPage(); $br=0; $i=10; $j=8; $k=0;
				    }
					
					if( $k>0 && $k<2 ) { $i=$i+102; }
						$pdf->Code39($i, $j, $val[csf("bundle_no")]);
						$pdf->Code39($i+55, $j-4, "Bundle Card ", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+55, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
						$pdf->Code39($i, $j+5, "Cut Sys No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+5, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+9,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+9,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+13,  "Style Ref:  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+17, "Item:  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+17, "Size:  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+21, "Country:  ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+21, "Color:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
					    $pdf->Code39($i+40, $j+25, "Bundle No:".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+25, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+29, "Gmts. No:  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+29, "Gmts. Qty.: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
					$k++;
					
					if($k==2)
					{  $k=0; $i=10; $j=$j+52; }
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
					if($br==10) { $pdf->AddPage(); $br=0; $i=8; $j=6; $k=0; }
					if( $k>0 && $k<2 ) { $i=$i+102; }
						$pdf->Code39($i, $j, $val[csf("bundle_no")]);
						$pdf->Code39($i+55, $j-4, "Bundle Card ", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+55, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
						$pdf->Code39($i, $j+5, "Cut Sys No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+5, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+9,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+9,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+13,  "Style Ref:  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+17, "Item:  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+17, "Size:  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+21, "Country:  ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+21, "Color:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
					    $pdf->Code39($i+40, $j+25, "Bundle No:".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+25, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+29, "Gmts. No:  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+29, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
					$k++;
					
					if($k==2)
					{  $k=0; $i=10; $j=$j+52; }
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


if($action=="print_barcode_ten_bpkw")
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
	$order_cut_no=return_field_value(" order_cut_no "," ppl_cut_lay_dtls"," id=$detls_id and status_active=1 and is_deleted=0 ","order_cut_no");
	//echo $order_cut_no;
	
	$pdf=new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty,country_id from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //w
	
	$i=10; $j=5; $k=0;
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
	
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2] and entry_form=76");
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
	// echo "select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])";
		 if($data[7]=="") $data[7]=0;
    	 $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
		 $cope_page=1;
		 if(count($sql_bundle_copy)!=0)
		 {
		 foreach($color_sizeID_arr as $val)
		 {
			if($br==10) { $pdf->AddPage(); $br=0; $i=10; $j=5; $k=0; }
			foreach($sql_bundle_copy as $inf)
			{
				if($br==10) 
				{
					 $pdf->AddPage(); $br=0; $i=10; $j=5; $k=0;
				}
				
				if( $k>0 && $k<2 ) { $i=$i+102; }
				
				$pdf->Code40($i, $j, $val[csf("bundle_no")]);
				
				$pdf->Code40($i+55, $j,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.2, $h = 3, $wide = true, true) ;	
				$pdf->Code40($i+55, $j+3, "ID No :", $ext = true, $cks = false, $w = 0.2, $h = 3, $wide = true, true) ;							
				//$pdf->Code39($i, $j+5, "Cut Sys No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+2, "Order Cut No: ".$order_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+2, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+5.5,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+5.5,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+9,  "Style Ref:  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				
				$pdf->Code40($i, $j+12.5, "Size:  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+12.5, "Item:  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				
				$pdf->Code40($i, $j+16, "Country:  ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+16, "Color:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+19.5, "Bundle No:".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+19.5, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+23, "Gmts. Qty.: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+23, "Gmts. No:  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				
				$pdf->Code40($i, $j+26.5, "Spot Qty.: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+26.5, "Reject Qty.: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+30, "QC Pass Qty.: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+30, "Total Qty.: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
					
				$k++;
				
				if($k==2)
				{  $k=0; $i=10; $j=$j+53; }
				$br++;
				$cope_page++;
			 }
	       }   
	    }
		else
		{
		   foreach($color_sizeID_arr as $val)
		   {
				if($br==10) { $pdf->AddPage(); $br=0; $i=8; $j=5; $k=0; }
				if( $k>0 && $k<2 ) { $i=$i+102; }
				$pdf->Code40($i, $j, $val[csf("bundle_no")]);
				
				$pdf->Code40($i+55, $j,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.2, $h = 3, $wide = true, true) ;	
				$pdf->Code40($i+55, $j+3, "ID No :", $ext = true, $cks = false, $w = 0.2, $h = 3, $wide = true, true) ;							
				//$pdf->Code39($i, $j+5, "Cut Sys No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+2, "Order Cut No: ".$order_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+2, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+5.5,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+5.5,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+9,  "Style Ref:  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				
				$pdf->Code40($i, $j+12.5, "Size:  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+12.5, "Item:  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				
				$pdf->Code40($i, $j+16, "Country:  ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+16, "Color:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+19.5, "Bundle No:".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+19.5, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+23, "Gmts. Qty.: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+23, "Gmts. No:  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				
				$pdf->Code40($i, $j+26.5, "Spot Qty.: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+26.5, "Reject Qty.: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i, $j+30, "QC Pass Qty.: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code40($i+40, $j+30, "Total Qty.: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$k++;
				
				if($k==2)
				{  $k=0; $i=10; $j=$j+53; }
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

//html format print one for per page.

/*if($action=="print_barcode_one")
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
	from ppl_cut_lay_bundle  a,ppl_cut_lay_size_dtls b
	where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");
	
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
				
				
				echo '<tr><td style="padding-left:5px;padding-top:10px;padding-bottom:5px"><div id="div_'.$i.'"></div>'.$bundle.'</td><td>'.$title.'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Cut Sys No: '.$new_cut_no.'</td><td>Cut Date: '.$cut_date.'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Buyer: '.$buyer_library[$buyer_name].'</td><td>PO: '.$po_number.'</td></tr>';
				echo '<tr><td style="padding-left:5px;" colspan="2">&nbsp;&nbsp;Style Ref: '.$style_name."**".$table_no_library[$table_name].'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Size: '.$size_arr[$val[csf("size_id")]].'</td><td>Item: '.$garments_item[$data[4]].'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Batch No: '.$bacth_array[$batch_id]."**".$batch_no.'</td><td>Color: '.$color_library[$data[5]].'</td></tr>';
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
}*/


//pdf format print one for per page.

if($action=="print_barcode_one")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	$data=explode("***",$data);
	
	//print_r( $data );die;
	//$ext_data=explode("__",$data[1]);
	//$cs_data=explode("__",$data[2]);
	
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	//$pdf=new PDF_Code39();
	$pdf=new PDF_Code39('P','mm','a8');
	
	//$pdf->SetFont('Times', '', 8);
	
	//$pdf=new PDF_Code39();
	$pdf->AddPage();
	
	
	
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty,country_id from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //where id in ($data)
	$color_sizeqty_sql=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty,country_id from ppl_cut_lay_bundle where dtls_id=$data[3] " );
	$total_size_qty_arr=array();
	foreach($color_sizeqty_sql as $val_qty)
	{
		$total_cut_qty+=$val_qty[csf('size_qty')];
		$total_size_qty_arr[$val_qty[csf('size_id')]]+=$val_qty[csf('size_qty')];
	}
	//print_r($total_size_qty_arr);
	//echo $total_cut_qty;die;
	 $sql_name=sql_select("select b.buyer_name, b.style_ref_no, b.product_dept, b.job_no, a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	 foreach($sql_name as $value)
	 {
		 $product_dept_name=$value[csf('product_dept')];
		 $style_name=$value[csf('style_ref_no')];
		 $buyer_name=$value[csf('buyer_name')];
		 $job_number=$value[csf('job_no')];
		 $po_number=$value[csf('po_number')];
	 }
	 
	 
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2] and entry_form=76");
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
	 //var_dump($color_sizeID_arr);die;
	 //echo $buyer_library[$buyer_name].jahid;die;
	 
	 $size_wise_repeat_cut_no=return_field_value("gmt_num_rep_sty","variable_order_tracking","company_name=".$company_id." and variable_list=28 and is_deleted=0 and status_active=1"); 
	 if($data[7]=="") $data[7]=0;
	 $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
		
	$page_num=1;	
	$i=2; $j=3; $k=0; $br=0; $n=0;	//$bundle_array=array(); 
	if(count($sql_bundle_copy)!=0)
	{
		foreach($color_sizeID_arr as $val)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=3; $k=0; }
			foreach($sql_bundle_copy as $inf)
			{
				if($br==1) 
				{
					$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
				}
				
				//if( $k>0 && $k<2 ) { $i=$i+105; }
				//$pdf->Code39($x, $y, $code, $ext = true, $cks = false, $w = 0.22, $h = 10, $wide = true, $textonly=false,$fontSize=12);
				
				$pdf->Code39($i, $j, $val[csf("bundle_no")]);
				$pdf->Code39($i+30, $j+7, "Cut Qty:(".$total_cut_qty.")", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true,11) ;
				$pdf->Code39($i+50, $j-4, "Bundle", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true,11) ;
				$pdf->Code39($i+50, $j+0,  "Card", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true,11) ;
				$pdf->Code39($i, $j+5,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				if($size_wise_repeat_cut_no==1)
				{
					$pdf->Code39($i+30, $j+5,  "Country : ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				}
				$pdf->Code39($i, $j+10, "Table No : ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i+25, $j+10, "Date : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i, $j+15,  $buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i+25, $j+15,"O: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i, $j+20,  "Style : ".$style_name, $ext = true, $cks = false, $w = 0.4, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i, $j+25, "Item :".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.4, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i, $j+30, "Size : ".$size_arr[$val[csf("size_id")]]."(".$total_size_qty_arr[$val[csf("size_id")]].")", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i+25, $j+30, "Color:".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i, $j+35, "Batch:".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i+30, $j+35, "Gmts. No:".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i, $j+40, "Gmts. Qnty : ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				$pdf->Code39($i+50, $j+41, "Page ".$page_num, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
				
				$k++;
				$i=2; $j=$j+40;
				
				
				$br++;
				$page_num++;
			}
		}   
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1) 
			{
				$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
			}
			
			//if( $k>0 && $k<2 ) { $i=$i+105; }
			
			
			$pdf->Code39($i, $j, $val[csf("bundle_no")]);
			$pdf->Code39($i+30, $j+7, "Cut Qty:(".$total_cut_qty.")", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true,11) ;
			$pdf->Code39($i+50, $j-4, "Bundle", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true,11) ;
			$pdf->Code39($i+50, $j+0,  "Card", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true,11) ;
			
			$pdf->Code39($i, $j+5,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			if($size_wise_repeat_cut_no==1)
			{
				$pdf->Code39($i, $j+5,  "Country : ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			}
			$pdf->Code39($i, $j+10, "Table No : ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			$pdf->Code39($i+25, $j+10, "Date : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			$pdf->Code39($i, $j+15, $buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			$pdf->Code39($i+25, $j+15,"O: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			$pdf->Code39($i, $j+20,  "Style : ".$style_name, $ext = true, $cks = false, $w = 0.4, $h = 10, $wide = true, true,11) ;
			$pdf->Code39($i, $j+25, "Item :".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.4, $h = 10, $wide = true, true,11) ;
			$pdf->Code39($i, $j+30, "Size : ".$size_arr[$val[csf("size_id")]]."(".$total_size_qty_arr[$val[csf("size_id")]].")", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			$pdf->Code39($i+25, $j+30, "Color:".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			$pdf->Code39($i, $j+35, "Batch:".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			$pdf->Code39($i+30, $j+35, "Gmts. No:".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			$pdf->Code39($i, $j+40, "Gmts. Qnty:".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			
			$pdf->Code39($i+50, $j+41, "Page ".$page_num, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
			
			$k++;
			$i=2; $j=$j+40;
			$br++;
			$page_num++;
		} 
	}
		
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = str_replace(".","",$buyer_library[$buyer_name])."_".$job_number."_".str_replace('/', '-', $po_number) ."_". date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
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
	 
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2] and entry_form=76");
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
		 $size_wise_repeat_cut_no=return_field_value("gmt_num_rep_sty","variable_order_tracking","company_name=".$company_id." and variable_list=28 and is_deleted=0 and status_active=1"); 
    	 if($data[7]=="") $data[7]=0;
    	 $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
		 
		 $cope_page=1;
		 if(count($sql_bundle_copy)!=0)
		 {
			 
			 foreach($color_sizeID_arr as $val)
			 {
				if($br==6) { $pdf->AddPage(); $br=0; $i=5; $j=10; $k=0; }
				foreach($sql_bundle_copy as $inf)
				   {
						if($br==6) 
						{
							 $pdf->AddPage(); $br=0; $i=5; $j=10; $k=0;
						 }
						
						if( $k>0 && $k<2 ) { $i=$i+100; }
							
							$pdf->Code39($i, $j, $val[csf("bundle_no")]);
							$pdf->Code39($i+55, $j-4, "Bundle Card ", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
							$pdf->Code39($i+55, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
							if($size_wise_repeat_cut_no==1)
							{
							$pdf->Code39($i+45, $j+6,  "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
							}
							$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
							$pdf->Code39($i+40, $j+6, "Cut Date : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
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
						$cope_page++;
					 }
					 //$br=6;
				
				} 
				
		 }
		else
		{
		   foreach($color_sizeID_arr as $val)
			   {
					if($br==6) { $pdf->AddPage(); $br=0; $i=5; $j=10; $k=0; }
					if( $k>0 && $k<2 ) { $i=$i+100; }
						$pdf->Code39($i, $j, $val[csf("bundle_no")]);
						if($size_wise_repeat_cut_no==1)
						{
						$pdf->Code39($i+45, $j+6,  "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						}
						//$pdf->Code39($i+45, $j, "Bundle Card ".$bundle_title, $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+40, $j+6, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
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
		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","cutting_no='".$cut_no."'");
		$cut_on_prifix = return_field_value("cut_num_prefix_no","ppl_cut_lay_mst","cutting_no = '".$cut_no."' and entry_form=76");
		//echo $cut_on_prifix;die;
		if($cutting_qc_no!="") { echo "201**".$mst_id."**".$dtls_id."**".$cutting_qc_no; die;}
		
		//$rIDdelete=execute_query("delete from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		//$rIDdelete2=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$year_id=date('Y',time());
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		if($size_wise_repeat_cut_no==1)
		{
			$bundle_no_array=array(); 
			$id=return_next_id("id", "ppl_cut_lay_size", 1);	
			$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,country_type,country_id,size_wise_repeat,inserted_by,insert_date";

			$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
			$field_array_bundle="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,number_start,number_end,size_qty,country_type,country_id,order_id,barcode_no,barcode_year,barcode_prifix,inserted_by,insert_date";
			/*$sql_bundle=sql_select("SELECT country_type, country_id, max(bundle_num_prefix_no) as num_prefix, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='".$mst_id."' group by country_type, country_id");
			foreach($sql_bundle as $row)
			{
				$bundle_no_array[$row[csf('country_type')]][$row[csf('country_id')]]['num_prefix']=$row[csf('num_prefix')];
				$bundle_no_array[$row[csf('country_type')]][$row[csf('country_id')]]['last_rmg']=$row[csf('last_rmg')];
			}*/
			
			$sql_bundle=sql_select("SELECT size_id, max(bundle_num_prefix_no) as num_prefix, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='".$mst_id."' group by size_id");
			foreach($sql_bundle as $row)
			{
				$bundle_no_array[$row[csf('size_id')]]['num_prefix']=$row[csf('num_prefix')];
				$bundle_no_array[$row[csf('size_id')]]['last_rmg']=$row[csf('last_rmg')];
			}
			
			$last_bundle_no=return_field_value("max(bundle_num_prefix_no) as last_prefix", "ppl_cut_lay_bundle","mst_id=$mst_id","last_prefix");
			
			$update_id=''; $bundle_prif_no=$last_bundle_no;
			for($i=1; $i<=$row_num; $i++)
			{
				$txt_size_id="hidden_size_id_".$i;
				$txt_lay_balance="txt_lay_balance_".$i;
				$size_ratio="txt_size_ratio_".$i;
				$size_qty="txt_size_qty_".$i;
				$txt_sequence="txt_bundle_".$i;
				$cboCountryType="cboCountryType_".$i;
				$cboCountry="cboCountry_".$i;
				
				if(str_replace("'",'',$$size_qty)>0)
				{
					if(str_replace("'",'',$$txt_sequence)!="")
					{
						$arr_sequence=str_replace("'",'',$$txt_sequence);	
					}
					else
					{
						$arr_sequence=$arr_sequence+1;	
					}
					
					if($data_array!="") $data_array.= ",";
					$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",".$$size_ratio.",".$$size_qty.",".$arr_sequence.",".$$cboCountryType.",".$$cboCountry.",".$size_wise_repeat_cut_no.",".$user_id.",'".$pc_date_time."')";
					
					//$last_rmg_no=$bundle_no_array[$$cboCountryType][$cboCountry]['last_rmg'];
					//$bundle_prif_no=$bundle_no_array[$$cboCountryType][$cboCountry]['num_prefix'];
					$last_rmg_no=$bundle_no_array[$$txt_size_id]['last_rmg'];
					
					$srange=0; $erange=$last_rmg_no; $bl_size_qty=str_replace("'",'',$$size_qty); $data='';
					$bundle_per_size=ceil(str_replace("'","",$$size_qty)/str_replace("'","",$bundle_per_pcs)); $tot_bundle_qty=0;
					for($k=1; $k<=$bundle_per_size; $k++)
					{ 
						if($k==$bundle_per_size) 
						{
							$bundle_qty=$bl_size_qty-$tot_bundle_qty;
						}
						else 
						{
							$bundle_qty=$bundle_per_pcs;
						}
						
						$company_sort_name=explode("-",$cut_no);
						$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
						$bundle_prif_no=$bundle_prif_no+1;
						$bundle_no=$bundle_prif."-".$bundle_prif_no;
						$srange=$erange+1;
						$erange=$srange+$bundle_qty-1;
						$tot_bundle_qty+=$bundle_qty;

						$barcode_suffix_no=$barcode_suffix_no+1;
						$barcode_no=$year_id."99".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
						
						$data.=$mst_id.",".$dtls_id.",".$$txt_size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."',".$srange.",".$erange.",".$bundle_qty.",".$$cboCountryType.",".$$cboCountry.",".$order_id.",".$barcode_no.",".$year_id.",".$barcode_suffix_no."**";
						$bundle_no_array[$$txt_size_id]['last_rmg']=$erange;
					}
					$data_array_up[$arr_sequence]=$data;
					$update_id.=$id."__".str_replace("'",'',$$cboCountryType)."__".str_replace("'",'',$$cboCountry)."__".str_replace("'",'',$$txt_size_id)."__".$arr_sequence.",";
					$id=$id+1;
				}
			}
			
			ksort($data_array_up);
			foreach($data_array_up as $sequence=>$data)
			{ 
				$dataArr=explode("**",substr($data,0,-2));
				foreach($dataArr as $dataBundle)
				{
					if($data_array_bundle!="") $data_array_bundle.= ",";
					$data_array_bundle.="(".$bundle_id.",".$dataBundle.",".$user_id.",'".$pc_date_time."')";
					$bundle_id=$bundle_id+1;
				}
			}
						
			$id_size=return_next_id("id", "ppl_cut_lay_size_dtls", 1);	
			$field_array_size="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,inserted_by,insert_date";

			for($i=1; $i<=$size_row_num; $i++)
			{
				$hidden_sizef_id="hidden_sizef_id_".$i;
				$txt_layf_balance="txt_layf_balance_".$i;
				$txt_sizef_ratio="txt_sizef_ratio_".$i;
				$txt_sizef_qty="txt_sizef_qty_".$i;
				
				if(str_replace("'",'',$$txt_sizef_qty)>0)
				{
					if($data_array_size!="") $data_array_size.= ",";
					$data_array_size.="(".$id_size.",".$mst_id.",".$dtls_id.",".$color_id.",".$$hidden_sizef_id.",".$$txt_sizef_ratio.",".$$txt_sizef_qty.",".$user_id.",'".$pc_date_time."')";
					$id_size++;
				}
			}
			
			//echo "10**".$last_bundle_no;die;
			//echo "10**insert into ppl_cut_lay_size_dtls($field_array_size)values".$data_array_size;die;
			$rID=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
			$rID_size=sql_insert("ppl_cut_lay_size_dtls",$field_array_size,$data_array_size,0);
			//echo "10**".$rID_size;die;
			// echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle;die;
			$rID2=sql_insert("ppl_cut_lay_bundle",$field_array_bundle,$data_array_bundle,0);
			$field_array_up="marker_qty*updated_by*update_date";
			$data_array_up="".$to_marker_qty."*'".$user_id."'*'".$pc_date_time."'";
			$rID3=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0); 
			//echo "10**".$rID.$rID2.$rID3;die;
			
			$sql="select sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id=".$order_id." and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1 group by po_break_down_id,item_number_id,color_number_id ";
			$result=sql_select($sql);
			foreach($result as $row)
			{
				$plan_qty=$row[csf("plan_qty")];
			}
			$sql_marker="select sum(marker_qty) as mark_qty from  ppl_cut_lay_dtls where order_id=".$order_id." and gmt_item_id=".$gmt_id." and color_id=".$color_id." and status_active=1";
			$result=sql_select($sql_marker);
			foreach($result as $rows)
			{
				$total_marker_qty=$rows[csf("mark_qty")];
			}
			$lay_balance=$plan_qty-$total_marker_qty;
			
			if($db_type==0)
			{
				if($rID && $rID_size && $rID2 && $rID3)
				{
					mysql_query("COMMIT");  
					echo "0**".$mst_id."**".$dtls_id."**".substr($updtae_id,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance."**".$size_wise_repeat_cut_no;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1)
			{
				if($rID && $rID_size && $rID2 && $rID3)
				{
					oci_commit($con);  
					echo "0**".$mst_id."**".$dtls_id."**".substr($update_id,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance."**".$size_wise_repeat_cut_no;
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		else
		{
			$id=return_next_id("id", "ppl_cut_lay_size", 1);	
			$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date,status_active,is_deleted";
			
			$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
			$field_array1="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,number_start,number_end,size_qty,order_id,inserted_by,insert_date,status_active,is_deleted";
			
			$sql_bundle=sql_select("SELECT c.bundle_num_prefix_no, c.bundle_num_prefix FROM ppl_cut_lay_bundle c,ppl_cut_lay_mst a,ppl_cut_lay_dtls b WHERE b.id=c.dtls_id and a.id=c.mst_id and a.id='".$mst_id."' and a.entry_form=76 ORDER BY c.bundle_num_prefix_no DESC ");
			$sql_rmg=sql_select("select max(a.number_end) as last_rmg from ppl_cut_lay_bundle a, ppl_cut_lay_mst b where b.id=a.mst_id and b.cutting_no='".$cut_no."' and b.entry_form=76");
			if( count($sql_rmg)>0)
					{
					 foreach($sql_rmg as $rmg_value)
						  {
						  $bundle_start=$rmg_value[csf('last_rmg')];
						  }
					}
				else
					{
						 $bundle_start=0;	
					}
		    $bundle_start+=1;
	       if(str_replace("'","",$update_id)=="")
		   {
				if( count($sql_bundle)>0)
					{
						foreach($sql_bundle as $row)
						{
					    	$bundle_no_prif[]=$row[csf('bundle_num_prefix_no')];
							$bundle_prif=$row[csf('bundle_num_prefix')];		
						}
					}
				else
					{
						 $bundle_no_prif[]=0;
						 $company_sort_name=substr($cut_no,0,2);
						/* $cut_no_prifix=substr($cut_no,4);
						for($i=0; $i<=8; $i++)
						{
						 if(substr($cut_no_prifix, 0, 1)=="0")
						 {
						   $cut_no_prifix=substr($cut_no_prifix,1);
						 }
						
						}*/
						 $bundle_prif=$company_sort_name."-".$cut_on_prifix;
					}
			//echo $bundle_prif;die;
			$arr_sequence=0;
			$add_comma=0;
			for($i=1; $i<=$row_num; $i++)
		  	{
				$txt_size_id="hidden_size_id_".$i;
				$txt_lay_balance="txt_lay_balance_".$i;
				$size_ratio="txt_size_ratio_".$i;
				$size_qty="txt_size_qty_".$i;
				$txt_sequence="txt_bundle_".$i;
			 	if(str_replace("'","",$$size_ratio)!="")
		       {
				if ($add_comma!=0) $data_array.=",";
				if(str_replace("'",'',$$txt_sequence)!="")
						{
						  $arr_sequence=str_replace("'",'',$$txt_sequence);	
						}
				else
						{
						
						$arr_sequence=$arr_sequence+1;	
						}
				$updateID_array[]=str_replace("'",'',$id); 
				
					$data_array_up[$arr_sequence]=("".$id."*".$mst_id."*".$dtls_id."*".$color_id."*".$$txt_size_id."*".$$size_ratio."*".$$size_qty."*".$arr_sequence."*'".$user_id."'*'".$pc_date_time."'*1*0");
		    }
			$id=$id+1;
		  }
		    ksort($data_array_up);
			$jj=1;
			$bundle_end=0;
			$bundle_from=0;
			foreach($data_array_up  as $key=>$value)
			{
				$value=explode("*",$value);
				$size_id_value=str_replace("'",'',$value[4]);
				$size_id_value=str_replace("'",'',$value[4]);
				$size_ratio_value=str_replace("'",'',$value[5]);
				$size_qty_value=str_replace("'",'',$value[6]);
				$sequence_value=str_replace("'",'',$value[7]);
				if($jj==1)
				{
				$data_array.="(".implode(",",$value).")";
				}
				else
				{
				$data_array.=",(".implode(",",$value).")";	
				}
				$bundle_qty=$bundle_per_pcs;
				$bundle_per_size=ceil(str_replace("'","",$size_qty_value)/str_replace("'","",$bundle_per_pcs));
				if ($jj!=1) $data_array1.=",";
				for($k=0; $k<$bundle_per_size; $k++)
				  {  
				    $bd_id++;
				    $bundle_no_prif[0]+=1;
				    $bundle_no=$bundle_prif."-".str_pad((int) $bundle_no_prif[0],3,"0",STR_PAD_LEFT);
				    if ($add_comma>0) $data_array1.=","; 
					 if ($k>0) $data_array1.=",";  
					 if($k!=$bundle_per_size-1)
						  {
							 $bundle_from=$bundle_start+($bundle_qty*$k);
							 $bundle_end=$bundle_start+$bundle_qty*($k+1)-1;
							 $data_array1.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id_value.",'".$bundle_prif."','".$bundle_no_prif[0]."','".$bundle_no."',".$bundle_from.",".$bundle_end.",".$bundle_qty.",".$order_id.",'".$user_id."','".$pc_date_time."',1,0)";
						  }
					 else
					  {
							$bundle_from=$bundle_start+($bundle_qty*$k);
							$bundle_end=$bundle_start+$size_qty_value-1;
							$bundle_qty=str_replace("'","",$bundle_end)-str_replace("'","",$bundle_from)+1;
						    $data_array1.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id_value.",'".$bundle_prif."','".$bundle_no_prif[0]."','".$bundle_no."',".$bundle_from.",".$bundle_end.",".$bundle_qty.",".$order_id.",'".$user_id."','".$pc_date_time."',1,0)";
						 }
				  $bundle_id=$bundle_id+1;
				  }
				  $bundle_start=$bundle_end+1;
				  $jj++;
	         	}
			}
			
			if($data_array_up!="")
			{  
				$rID3=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
				echo "15**0**insert into ppl_cut_lay_bundle($field_array1)values".$data_array1;die();
				$rID4=sql_insert("ppl_cut_lay_bundle",$field_array1,$data_array1,0);
				$field_array_up="marker_qty*updated_by*update_date";
				$data_array_up="".$to_marker_qty."*'".$user_id."'*'".$pc_date_time."'";
				$rID5=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0); 
				$updateID_array=implode('_',$updateID_array);
				$sql="select sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id=".$order_id." and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1 group by po_break_down_id,item_number_id,color_number_id ";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					$plan_qty=$row[csf("plan_qty")];
				}
				$sql_marker="select sum(marker_qty) as mark_qty from  ppl_cut_lay_dtls where order_id=".$order_id." and gmt_item_id=".$gmt_id." and color_id=".$color_id." and status_active=1 group by order_id,gmt_item_id,color_id ";
				$result=sql_select($sql_marker);
				foreach($result as $rows)
				{
					$total_marker_qty=$rows[csf("mark_qty")];
				}
				$lay_balance=$plan_qty-$total_marker_qty;
			}
			else
			{
				echo "15**0"; die;
			}
			if($db_type==0)
			  {
				if($rID3 && $rID4 && $rID5)
					{
						mysql_query("COMMIT");  
						echo "0**".$mst_id."**".$dtls_id."**".$updateID_array."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
					}
				else
					{
						mysql_query("ROLLBACK"); 
						echo "10**";
					}
				
			 }
			 if($db_type==2 || $db_type==1)
					{
					if($rID3 && $rID4 && $rID5)
						{
							oci_commit($con);  
							echo "0**".$mst_id."**".$dtls_id."**".$updateID_array."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
						}
					else
						{
							oci_rollback($con);
							echo "10**";
						}
					}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	  {
			$con = connect();		
			if($db_type==0)	{ mysql_query("BEGIN"); }
			
			$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","cutting_no='".$cut_no."'");
			$cut_on_prifix = return_field_value("cut_num_prefix_no","ppl_cut_lay_mst","cutting_no = '".$cut_no."' and entry_form=76");
			//echo $cutting_qc_no;die;
			if($cutting_qc_no!="") { echo "200**".$mst_id."**".$dtls_id."**".$cutting_qc_no; die;}
			
			if($size_wise_repeat_cut_no==1)
			{
				$bundle_no_array=array(); 
				$id=return_next_id("id", "ppl_cut_lay_size", 1);	
				$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,country_type,country_id,size_wise_repeat,inserted_by,insert_date";
				$field_array_update="size_id*size_ratio*marker_qty*bundle_sequence*country_type*country_id*updated_by*update_date";
				
				$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
				$field_array_bundle="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,number_start,number_end,size_qty,country_type,country_id,inserted_by,insert_date";
				
				/*$sql_bundle=sql_select("SELECT country_type, country_id, max(bundle_num_prefix_no) as num_prefix, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='".$mst_id."' group by  country_type, country_id");
				foreach($sql_bundle as $row)
				{
					$bundle_no_array[$row[csf('country_type')]][$row[csf('country_id')]]['num_prefix']=$row[csf('num_prefix')];
					$bundle_no_array[$row[csf('country_type')]][$row[csf('country_id')]]['last_rmg']=$row[csf('last_rmg')];
				}*/
				
				$sql_bundle=sql_select("SELECT size_id, max(bundle_num_prefix_no) as num_prefix, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='".$mst_id."' group by size_id");
				foreach($sql_bundle as $row)
				{
					$bundle_no_array[$row[csf('size_id')]]['num_prefix']=$row[csf('num_prefix')];
					$bundle_no_array[$row[csf('size_id')]]['last_rmg']=$row[csf('last_rmg')];
				}
				
				$delete=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
				$last_bundle_no=return_field_value("max(bundle_num_prefix_no) as last_prefix", "ppl_cut_lay_bundle","mst_id=$mst_id","last_prefix");
				
				$update_id=''; $bundle_prif_no=$last_bundle_no;
				for($i=1; $i<=$row_num; $i++)
				{
					$txt_size_id="hidden_size_id_".$i;
					$txt_lay_balance="txt_lay_balance_".$i;
					$size_ratio="txt_size_ratio_".$i;
					$size_qty="txt_size_qty_".$i;
					$txt_sequence="txt_bundle_".$i;
					$cboCountryType="cboCountryType_".$i;
					$cboCountry="cboCountry_".$i;
					$updateSizeTableId="update_size_id_".$i;
					
					/*if(str_replace("'",'',$$updateSizeTableId)>0)
					{
						if(str_replace("'",'',$$txt_sequence)!="")
						{
							$arr_sequence=str_replace("'",'',$$txt_sequence);	
						}
						else
						{
							$arr_sequence=$arr_sequence+1;	
						}
						
						$updateId_arr[]=str_replace("'",'',$$updateSizeTableId);
						$data_array_update[str_replace("'",'',$$updateSizeTableId)]=explode("*",("".$$txt_size_id."*".$$size_ratio."*".$$size_qty."*".$arr_sequence."*".$$cboCountryType."*".$$cboCountry."*'".$user_id."'*'".$pc_date_time."'"));
						$update_id.=str_replace("'",'',$$updateSizeTableId)."__".str_replace("'",'',$$cboCountryType)."__".str_replace("'",'',$$cboCountry)."__".str_replace("'",'',$$txt_size_id)."__".$arr_sequence.",";
					}
					else
					{
						if(str_replace("'","",$$size_qty)>0)
						{
							if(str_replace("'",'',$$txt_sequence)!="")
							{
								$arr_sequence=str_replace("'",'',$$txt_sequence);	
							}
							else
							{
								$arr_sequence=$arr_sequence+1;	
							}
							
							if($data_array!="") $data_array.= ",";
							$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",".$$size_ratio.",".$$size_qty.",".$arr_sequence.",".$$cboCountryType.",".$$cboCountry.",".$size_wise_repeat_cut_no.",".$user_id.",'".$pc_date_time."')";
							
							$update_id.=$id."__".str_replace("'",'',$$cboCountryType)."__".str_replace("'",'',$$cboCountry)."__".str_replace("'",'',$$txt_size_id)."__".$arr_sequence.",";
							$id=$id+1;
						}
					}*/
					
					if(str_replace("'","",$$size_qty)>0)
					{
						if(str_replace("'",'',$$txt_sequence)!="")
						{
							$arr_sequence=str_replace("'",'',$$txt_sequence);	
						}
						else
						{
							$arr_sequence=$arr_sequence+1;	
						}
						
						if($data_array!="") $data_array.= ",";
						$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",".$$size_ratio.",".$$size_qty.",".$arr_sequence.",".$$cboCountryType.",".$$cboCountry.",".$size_wise_repeat_cut_no.",".$user_id.",'".$pc_date_time."')";
						
						$update_id.=$id."__".str_replace("'",'',$$cboCountryType)."__".str_replace("'",'',$$cboCountry)."__".str_replace("'",'',$$txt_size_id)."__".$arr_sequence.",";
						$id=$id+1;
					}
					
					if(str_replace("'","",$$size_qty)>0)
					{
						//$last_rmg_no=$bundle_no_array[$$cboCountryType][$cboCountry]['last_rmg'];
						//$bundle_prif_no=$bundle_no_array[$$cboCountryType][$cboCountry]['num_prefix'];
						$last_rmg_no=$bundle_no_array[$$txt_size_id]['last_rmg'];
						
						$srange=0; $erange=$last_rmg_no; $bl_size_qty=str_replace("'",'',$$size_qty); $data='';
						$bundle_per_size=ceil(str_replace("'","",$$size_qty)/str_replace("'","",$bundle_per_pcs)); $tot_bundle_qty=0;
						for($k=1; $k<=$bundle_per_size; $k++)
						{ 
							if($k==$bundle_per_size) 
							{
								$bundle_qty=$bl_size_qty-$tot_bundle_qty;
							}
							else 
							{
								$bundle_qty=$bundle_per_pcs;
							}
							
							$company_sort_name=explode("-",$cut_no);
							$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
							$bundle_prif_no=$bundle_prif_no+1;
							$bundle_no=$bundle_prif."-".$bundle_prif_no;
							$srange=$erange+1;
							$erange=$srange+$bundle_qty-1;
							$tot_bundle_qty+=$bundle_qty;
							
							$data.=$mst_id.",".$dtls_id.",".$$txt_size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."',".$srange.",".$erange.",".$bundle_qty.",".$$cboCountryType.",".$$cboCountry."**";
							
							$bundle_no_array[$$txt_size_id]['last_rmg']=$erange;
						}
						$data_array_up[$arr_sequence]=$data;
					}
				}
				
				ksort($data_array_up);
				foreach($data_array_up as $sequence=>$data)
				{ 
					$dataArr=explode("**",substr($data,0,-2));
					foreach($dataArr as $dataBundle)
					{
						if($data_array_bundle!="") $data_array_bundle.= ",";
						$data_array_bundle.="(".$bundle_id.",".$dataBundle.",".$user_id.",'".$pc_date_time."')";
						$bundle_id=$bundle_id+1;
					}
				}
				
				$delete_size=execute_query("delete from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
				$delete_size_dtls=execute_query("delete from ppl_cut_lay_size_dtls where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
				
				$id_size=return_next_id("id", "ppl_cut_lay_size_dtls", 1);	
				$field_array_size="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,inserted_by,insert_date";
	
				for($i=1; $i<=$size_row_num; $i++)
				{
					$hidden_sizef_id="hidden_sizef_id_".$i;
					$txt_layf_balance="txt_layf_balance_".$i;
					$txt_sizef_ratio="txt_sizef_ratio_".$i;
					$txt_sizef_qty="txt_sizef_qty_".$i;
					
					if(str_replace("'",'',$$txt_sizef_qty)>0)
					{
						if($data_array_size!="") $data_array_size.= ",";
						$data_array_size.="(".$id_size.",".$mst_id.",".$dtls_id.",".$color_id.",".$$hidden_sizef_id.",".$$txt_sizef_ratio.",".$$txt_sizef_qty.",".$user_id.",'".$pc_date_time."')";
						$id_size++;
					}
				}
				//mysql_query("ROLLBACK"); 
				//echo "10**insert into ppl_cut_lay_size_dtls($field_array_size) values".$data_array_size;die;
				$rID=true; $rID2=true;
				/*if(count($data_array_update)>0)
				{
					$rID=execute_query(bulk_update_sql_statement( "ppl_cut_lay_size", "id", $field_array_update, $data_array_update, $updateId_arr ));
					//echo "10**".bulk_update_sql_statement( "ppl_cut_lay_size", "id", $field_array_update, $data_array_update, $updateId_arr );
				}*/
				
				if($data_array!="")
				{
					//echo "insert into ppl_cut_lay_size($field_array)values".$data_array;
					$rID2=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
				}
				
				$rID_size=sql_insert("ppl_cut_lay_size_dtls",$field_array_size,$data_array_size,0);
				//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle;die;
				$rID3=sql_insert("ppl_cut_lay_bundle",$field_array_bundle,$data_array_bundle,0);
				
				$field_array_up="marker_qty*updated_by*update_date";
				$data_array_up="".$to_marker_qty."*'".$user_id."'*'".$pc_date_time."'";
				$rID4=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0); 
				//echo "10**".$rID.$rID2.$rID3.$rID4.$delete;die;
				
				$sql="select sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id=".$order_id." and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1 group by po_break_down_id,item_number_id,color_number_id ";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					$plan_qty=$row[csf("plan_qty")];
				}
				
				$sql_marker="select sum(marker_qty) as mark_qty from  ppl_cut_lay_dtls where order_id=".$order_id." and gmt_item_id=".$gmt_id." and color_id=".$color_id." and status_active=1";
				$result=sql_select($sql_marker);
				foreach($result as $rows)
				{
					$total_marker_qty=$rows[csf("mark_qty")];
				}
				$lay_balance=$plan_qty-$total_marker_qty;
				
				if($db_type==0)
				{
					if($rID && $rID_size && $delete_size && $delete_size_dtls && $rID3 && $rID4 && $delete)
					{
						mysql_query("COMMIT");  
						echo "1**".$mst_id."**".$dtls_id."**".substr($update_id,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance."**".$size_wise_repeat_cut_no;
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**";
					}
				}
				else if($db_type==2 || $db_type==1)
				{
					if($rID && $rID_size && $delete_size && $delete_size_dtls && $rID3 && $rID4 && $delete)
					{
						oci_commit($con);  
						echo "1**".$mst_id."**".$dtls_id."**".substr($update_id,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance."**".$size_wise_repeat_cut_no;
					}
					else
					{
						oci_rollback($con);
						echo "10**";
					}
				}
			}
			else
			{
				//master table update*********************************************************************hidden_size_id_
				$flag=0;
				$field_array_up="size_id*size_ratio*marker_qty*bundle_sequence*updated_by*update_date";
				$field_array1="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,number_start,number_end,size_qty,inserted_by,insert_date";
				$id=return_next_id("id", "  ppl_cut_lay_size", 1);	
				$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
				$sql_rmg=sql_select("select min(a. number_start) as start_rmg from ppl_cut_lay_bundle a, ppl_cut_lay_mst b where b.id=a.mst_id and b.cutting_no='".$cut_no."' and a.mst_id=".$mst_id." and a.dtls_id=".$dtls_id." and b.entry_form=76" );
				if( count($sql_rmg)>0)
						{
						 foreach($sql_rmg as $rmg_value)
							  {
							   $bundle_start=$rmg_value[csf('start_rmg')];
							  }
						}
					else
						{
							 $bundle_start=0;	
						}
			  $field_array_in="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date";
			  $field_array1_in="id,mst_id,dtls_id,size_id,bundle_no,number_start,number_end,size_qty,inserted_by,insert_date";
			  $sql_bundle=sql_select("SELECT c.bundle_num_prefix_no,c.bundle_num_prefix  FROM ppl_cut_lay_bundle c,ppl_cut_lay_mst a,ppl_cut_lay_dtls b WHERE b.id=c.dtls_id and a.id=c.mst_id and c.mst_id=".$mst_id." and c.dtls_id=".$dtls_id." and a.entry_form=76 ORDER BY c.bundle_num_prefix_no ASC");
			  if(str_replace("'","",$dtls_id)!="")
			   {
					if( count($sql_bundle)>0)
						{
							foreach($sql_bundle as $row)
							{
								$bundle_no_prif[]=$row[csf('bundle_num_prefix_no')];
								$bundle_prif=$row[csf('bundle_num_prefix')];		
							}
						}
					else
						{
							 $bundle_no_prif[]=1;
							 $company_sort_name=substr($cut_no,0,2);
							 $cut_no_prifix=str_replace("0","",substr($cut_no,4));	
							 $bundle_prif=$company_sort_name."-".$cut_no_prifix;
						}
			 $rID=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
			 if($rID) $flag=0; else $flag=1;
			 $add_comma=0;
			 $arr_sequence=0;
			 for($i=1; $i<=$row_num; $i++)
			  {
					$txt_size_id="hidden_size_id_".$i;
					$txt_lay_balance="txt_lay_balance_".$i;
					$size_ratio="txt_size_ratio_".$i;
					$size_qty="txt_size_qty_".$i;
					$txt_sequence="txt_bundle_".$i;
					$txt_update_size="update_size_id_".$i;
					
			
				 if(str_replace("'","",$$size_ratio)!="")
					{
					if ($add_comma!=0) $data_array.=",";
						//******************
						if(str_replace("'",'',$$txt_sequence)!="")
							{
							  $arr_sequence=str_replace("'",'',$$txt_sequence);	
							}
					else
							{
							$arr_sequence=$arr_sequence+1;	
							}
							
						if(str_replace("'",'',$$txt_update_size)!="")  
							{
								$updateID_array[]=str_replace("'",'',$$txt_update_size); 
								$data_array_up[str_replace("'",'',$$txt_update_size)]=explode("*",("".$$txt_size_id."*".$$size_ratio."*".$$size_qty."*".$$txt_sequence."*'".$user_id."'*'".$pc_date_time."'"));
								$data_arr_up[$arr_sequence]=("".$color_id."*".$$txt_size_id."*".$$size_ratio."*".$$size_qty."*".$$txt_sequence."");
							}
					   else
							{
								$updateID_array[]=str_replace("'",'',$id); 
								//if ($add_comma!=0) $data_array1_in.=",";
								$data_array_in.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",".$$size_ratio.",".$$size_qty.",".$$txt_sequence.",'".$user_id."','".$pc_date_time."')";
								$data_arr_up[$arr_sequence]=("".$color_id."*".$$txt_size_id."*".$$size_ratio."*".$$size_qty."*".$$txt_sequence."");
								$id=$id+1;
							}	
	
					}
				$id=$id+1;
			  }
			   // print_r($updateID_array);die;      
				ksort($data_arr_up);
				$jj=1;
				$bundle_end=0;
				$bundle_from=1;
				
				foreach($data_arr_up  as $key=>$value)
				{
					$value=explode("*",$value);
				
					$size_id_value=str_replace("'",'',$value[1]);
					$size_ratio_value=str_replace("'",'',$value[2]);
					$size_qty_value=str_replace("'",'',$value[3]);
					$sequence_value=str_replace("'",'',$value[4]);
					if($jj==1)
					{
					$data_array.="(".implode(",",$value).")";
					}
					else
					{
					$data_array.=",(".implode(",",$value).")";	
					}
					
					
					$bundle_qty=$bundle_per_pcs;
					$bundle_per_size=ceil(str_replace("'","",$size_qty_value)/str_replace("'","",$bundle_per_pcs));
					
					
					if ($jj!=1) $data_array1.=",";
					for($k=0; $k<$bundle_per_size; $k++)
					  {  
						   $bd_id++;
					   
						$bundle_no=$bundle_prif."-".$bundle_no_prif[0];
						if ($add_comma>0) $data_array1.=","; 
						 if ($k>0) $data_array1.=",";  
						 if($k!=$bundle_per_size-1)
							  {
								 $bundle_from=$bundle_start+($bundle_qty*$k);
								 $bundle_end=$bundle_start+$bundle_qty*($k+1)-1;
								 $data_array1.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id_value.",'".$bundle_prif."',".$bundle_no_prif[0].",'".$bundle_no."',".$bundle_from.",".$bundle_end.",".$bundle_qty.",'".$user_id."','".$pc_date_time."')";
							  }
						 else
						  {
								$bundle_from=$bundle_start+($bundle_qty*$k);
								$bundle_end=$bundle_start+$size_qty_value-1;
								$bundle_qty=str_replace("'","",$bundle_end)-str_replace("'","",$bundle_from)+1;
								 $data_array1.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id_value.",'".$bundle_prif."',".$bundle_no_prif[0].",'".$bundle_no."',".$bundle_from.",".$bundle_end.",".$bundle_qty.",'".$user_id."','".$pc_date_time."')";
							 }
						 $bundle_no_prif[0]+=1;
						$bundle_id=$bundle_id+1;
						// $k++; 
					  }
					  $bundle_start=$bundle_end+1;
						$jj++;
					}
				}
				//echo $data_array_in;die;
				  if(count($updateID_array)>0)
					{
						$rID1=execute_query(bulk_update_sql_statement("ppl_cut_lay_size","id",$field_array_up,$data_array_up,$updateID_array),1);
						if($rID1) $flag=0; else $flag=1;
					}
					
			   if(count($data_array_in)>0)
					{
						$rID3=sql_insert(" ppl_cut_lay_size",$field_array_in,$data_array_in,0);
						if($rID3) $flag=0; else $flag=1;
					}
					
			   $detls_id_update.=implode("_",$updateID_array); 
			 
			   $rID2=sql_insert(" ppl_cut_lay_bundle",$field_array1,$data_array1,0); 
			   if($rID2) $flag=0; else $flag=2;
			   $field_array2="marker_qty*updated_by*update_date";
			   $data_array2="".$to_marker_qty."*'".$user_id."'*'".$pc_date_time."'";
			   $rID5=sql_update(" ppl_cut_lay_dtls",$field_array2,$data_array2,"id",$dtls_id,0); 
			   if($rID5) $flag=0; else $flag=1;
			   //update nest rmg qty*********************************************************************************************************
			  $sql_next_data=sql_select("select id,plies from  ppl_cut_lay_dtls where mst_id=".$mst_id." and id >".$dtls_id."  order by id");
			$kk=1;
			
			  foreach($sql_next_data as $nval)
			  {
					   $sql_bundle=return_field_value("max(size_qty)","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =".$nval[csf('id')]."");
					   $sql_data_dtls=sql_select("select mst_id,dtls_id,size_id,size_ratio,marker_qty,bundle_sequence from ppl_cut_lay_size where  mst_id=".$mst_id." and dtls_id =".$nval[csf('id')]."  order by bundle_sequence");
					   $arr_sequence=0;
						foreach($sql_data_dtls as $nvalue)
						{
							if($nvalue[csf("bundle_sequence")]!=0)
								{
								  $arr_sequence=$nvalue[csf("bundle_sequence")];	
								}
							else
								{
								$arr_sequence=$arr_sequence+1;	
								}
								$data_arr_next[$arr_sequence]=("".$nvalue[csf("mst_id")]."*".$nvalue[csf("dtls_id")]."*".$nvalue[csf("size_id")]."*".$nvalue[csf("size_ratio")]."*".$nvalue[csf("marker_qty")]."*".$nvalue[csf("bundle_sequence")]."*".$nvalue[csf("marker_qty")]."");
						  ksort($data_arr_next);
						}
					//if($data_arr_next!="")
					//{
							$bundle_end_next=0;
							$bundle_from_next=0;
							//$bundle_no_prif=explode("-",$bundle_no);
							if ($kk!=1) $data_array_next.=",";
							$kkk=1;
							foreach($data_arr_next  as $nkey=>$nxtvalue)
							{
								$nxtvalue=explode("*",$nxtvalue);
								$next_mst_id=$nxtvalue[0];
								$next_details=$nxtvalue[1];
								$size_id_value_next=$nxtvalue[2];
								$size_ratio_value_next=$nxtvalue[3];
								$size_qty_value_next=$nxtvalue[4];
								$sequence_value_next=$nxtvalue[5];
								if($kk==1)
								  {
									$data_array.="(".implode(",",$nxtvalue).")";
								  }
								else
								  {
									$data_array.=",(".implode(",",$nxtvalue).")";	
								  }
								$bundle_qty=$sql_bundle;
								$bundle_per_size=ceil($size_qty_value_next/$sql_bundle);
								if ($kkk!=1) $data_array_next.=",";
								for($k=0; $k<$bundle_per_size; $k++)
								  {  
									 $bd_id++;
									 $bundle_no=$bundle_prif."-".$bundle_no_prif[0];
									//$bundle_no=$job_id."-".$bundle_no_prif[3];
									 if ($k>0) $data_array_next.=",";  
									 if($k!=$bundle_per_size-1)
										  {
											 $bundle_from=$bundle_start+($bundle_qty*$k);
											 $bundle_end=$bundle_start+$bundle_qty*($k+1)-1;
											 $data_array_next.="(".$bundle_id.",".$next_mst_id.",".$next_details.",".$size_id_value_next.",'".$bundle_prif."',".$bundle_no_prif[3].",'".$bundle_no."',".$bundle_from.",".$bundle_end.",".$bundle_qty.",'".$user_id."','".$pc_date_time."')";
										  }
									 else
									  {
											$bundle_from=$bundle_start+($bundle_qty*$k);
											$bundle_end=$bundle_start+$size_qty_value_next-1;
											$bundle_qty=str_replace("'","",$bundle_end)-str_replace("'","",$bundle_from)+1;
											$data_array_next.="(".$bundle_id.",".$next_mst_id.",".$next_details.",".$size_id_value_next.",'".$bundle_prif."',".$bundle_no_prif[3].",'".$bundle_no."',".$bundle_from.",".$bundle_end.",".$bundle_qty.",'".$user_id."','".$pc_date_time."')";
										 }
								   $bundle_id=$bundle_id+1;
								   $bundle_no_prif[0]+=1;
								  }
								  $bundle_start=$bundle_end+1;
								  $kkk++;		
						  }
								 $kk++;	
					// }
				  
				  
			  }
	
			   $rID6=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$mst_id." and dtls_id > ".$dtls_id."",0);
			
			   if($rID6) $flag=0; else $flag=1;
			  if(count($data_array_next)>0)
				{
					
					$rID7=sql_insert(" ppl_cut_lay_bundle",$field_array1,$data_array_next,0);
					if($rID7) $flag=0; else $flag=1;
				}
			  $updateID_array=implode('_',$updateID_array);
				$sql="select sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id=".$order_id." and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1 group by po_break_down_id,item_number_id,color_number_id ";
			$result=sql_select($sql);
			foreach($result as $row)
				{
					$plan_qty=$row[csf("plan_qty")];
				}
			$sql_marker="select sum(marker_qty) as mark_qty from  ppl_cut_lay_dtls where order_id=".$order_id." and gmt_item_id=".$gmt_id." and color_id=".$color_id." and status_active=1 group by order_id,gmt_item_id,color_id ";
			$result=sql_select($sql_marker);
				foreach($result as $rows)
				{
					$total_marker_qty=$rows[csf("mark_qty")];
				}
				$lay_balance=$plan_qty-$total_marker_qty;
			 if($db_type==0)
				 {
					 //echo $flag;
					if($flag==0)
					   {
						mysql_query("COMMIT");  
							echo "1**".$mst_id."**".$dtls_id."**".$updateID_array."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
						}
					else
					   {
						mysql_query("ROLLBACK"); 
						echo "10**".str_replace("'","",$dtls_id);
					   }
				 }
				if($db_type==2 || $db_type==1 )
				  {
					if($flag==0)
					   {
							oci_commit($con); 
							echo "1**".$mst_id."**".$dtls_id."**".$updateID_array."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
						}
					else
					   {
						oci_rollback($con); 
						echo "10**".str_replace("'","",$dtls_id);
					   }
				  }
			}
			disconnect($con);
			die;
	   }
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		
	}		
}

if($action=="show_bundle_list_view")
{
	$ex_data= explode("**",$data);
	$mst_id=$ex_data[0];
	$dtls_id=$ex_data[1];
	$size_id_arr=explode("_",$ex_data[2]);
	$size_id_arr=implode(',',$size_id_arr);
	$size_wise_repeat_cut_no=$ex_data[3];
	
	if($size_wise_repeat_cut_no==1)
	{
		$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$po_country_array=array();
		$sql_query=sql_select("select distinct a.country_id as country_id from wo_po_color_size_breakdown a, ppl_cut_lay_dtls b where a.item_number_id=b.gmt_item_id and a.po_break_down_id=b.order_id and a.color_number_id=b.color_id and b.mst_id=$mst_id and b.id=$dtls_id and a.status_active=1 and a.is_deleted=0");
		$size_details=array(); $sizeId_arr=array(); $shipDate_arr=array();
		foreach($sql_query as $row)
		{
			$po_country_array[$row[csf('country_id')]]=$country_arr[$row[csf('country_id')]];
		}
	?>
        <fieldset style="width:800px">
            <legend>Bundle No and RMG qty details</legend>
            <table cellpadding="0" cellspacing="0" width="780" class="rpt_table" id="tbl_bundle_list_save">
                <thead class="form_table_header">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Bundle</th>
                    <th colspan="2">RMG Number</th>
                    <th>
                        <input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />  
                        <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $dtls_id; ?>" />
                    </th>
                    <th></th>
                    <th>Report &nbsp;</th>
                </thead>
                <thead class="form_table_header">
                    <th width="50">SL No</th>
                    <th width="80">Country Type</th>
                    <th width="80">Country Name</th>
                    <th width="110">Bundle No</th>
                    <th width="80">Quantity</th>
                    <th width="80">From</th>
                    <th width="80">To</th>
                    <th width="80" rowspan="2" >Size</th>
                    <th width="100" rowspan="2" ></th>
                    <th width="60"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
                </thead>
                <tbody id="trBundleListSave">
                <?
                $sql_size_name=sql_select("select size_id from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."");
                $size_colour_arr=array();
                foreach($sql_size_name as $asf)
                {
                    $size_colour_arr[$asf[csf("size_id")]]=$size_arr[$asf[csf("size_id")]];	
                }

                $bundle_data=sql_select("select a.id,a.bundle_no,a.barcode_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value, a.country_type, a.country_id from ppl_cut_lay_bundle a where a.mst_id=".$mst_id." and a.dtls_id=".$dtls_id." order by a.id ASC");
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
                        <td align="center"  id=""><input type="text" id="sirialNo_<? echo $i;  ?>" name="sirialNo[]" style="width:30px;" class="text_boxes"  value="<? echo $i;  ?>"  disabled/>
                        <input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]"  value="<? echo $i;  ?>"style="width:30px;" />                   
                        <input type="hidden" id="hiddenUpdateFlag_<? echo $i;  ?>" name="hiddenUpdateFlag[]"  value="<? echo $row[csf('update_flag')];?> " style="width:30px;" />
                        <input type="hidden" id="hiddenUpdateValue_<? echo $i;  ?>" name="hiddenUpdateValue[]"  value="<? echo $row[csf('update_value')];?> " style="width:30px;" />
                        </td>
                        <td align="center">	
                            <?
                                echo create_drop_down( "cboCountryType_B".$i, 80, $country_type,'', 0, '',$row[csf('country_type')],'',1); 
                            ?>                                          
                            <input type="hidden" id="hiddenCountryTypeB_<? echo $i;  ?>" name="hiddenCountryTypeB[]"  value="<? echo $row[csf('country_type')];?> "/> 
                        </td> 
                        <td align="center">	
                             <?
                              echo create_drop_down("cboCountryB_".$i,80,$po_country_array,'',0,'',$row[csf('country_id')],'',1,'','','','','','','cboCountryB[]'); 
                            ?> 
                            <input type="hidden" id="hiddenCountryB_<? echo $i;  ?>" name="hiddenCountryB[]"  value="<? echo $row[csf('country_id')];?> " />
                        </td>
                        <td align="center" title="<? echo $row[csf('barcode_no')];?>" >
                        <input type="text" name="bundleNo[]" id="bundleNo_<? echo $i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes"  style="width:70px;  text-align:center" disabled/>
                        </td>
                        <td align="center">
                        <input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onKeyUp="bundle_calclution(<? echo $i;  ?>,<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right; <? if($update_f_value[0]!="") echo "background-color:#F3F;"; ?>"  class="text_boxes"  disabled/>
                        <input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<? echo $i;  ?>" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right" class="text_boxes"  disabled/>
                        </td>
                        <td align="center">
                        <input type="text" name="rmgNoStart[]" id="rmgNoStart_<? echo $i;  ?>" value="<? echo $row[csf('number_start')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled />
                        </td>
                        <td align="center">
                        <input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<? echo $i;  ?>" value="<? echo $row[csf('number_end')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled/>
                        
                        </td>
                        <td align="center" id="update_sizename_<? echo $i;  ?>">
                        <select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:70px; text-align:center;  <? if($update_f_value[1]!="") echo "background-color:#F3F;"; ?> "  disabled  >
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
                        <td align="center">
                        <input type="button"  value="+" name="addButton[]" id="addButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<? echo $i;  ?>','<? echo $i;  ?>')"/>
                        <input type="button"  value="Adj." name="rowUpdate[]" id="rowUpdate_<? echo $i;  ?>" style="width:50px" class="formbuttonplasminus" onClick="fnc_updateRow('<? echo $i;  ?>')"/>
                        </td>
                        <td align="center">
                        <input id="chk_bundle_<? echo $i;  ?>" type="checkbox" name="chk_bundle" >
                        <input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid_<? echo $i; ?>" value="<? echo $row[csf('id')];?>" style="width:15px;" class="text_boxes"/>
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
                        <? echo load_submit_buttons( $permission, "fnc_cut_lay_bundle_info_country", 1,0,"clear_size_form()",1);?>
                    </td>
                </tr>
            </table>
        </fieldset>
    <?
	}
	else
	{
	?>    
     	<fieldset style="width:750px">
              <legend>Bundle No and RMG qty details</legend>
                <table cellpadding="0" cellspacing="0" width="700" class="rpt_table" id="tbl_bundle_list_save">
                            <thead class="form_table_header">
                                  <th ></th>
                                  <th ></th>
                                  <th >Bundle</th>
                                  <th  colspan="2">RMG Number</th>
                                  <th ><input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />  
                                       <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $dtls_id; ?>" />
                                  </th>
                                  <th ></th>
                                  <th >Report &nbsp;</th>
                            </thead>
                            <thead class="form_table_header">
                                  <th width="80">SL No</th>
                                  <th width="140">Bundle No</th>
                                  <th width="80">Quantity</th>
                                  <th width="80">From</th>
                                  <th width="80">To</th>
                                  <th width="80" rowspan="2" >Size</th>
                                  <th width="100" rowspan="2" ></th>
                                  <th  width="60"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
                            </thead>
                            <tbody id="trBundleListSave">
                            <?
                               $sql_size_name=sql_select("select size_id from  ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."");
                               $size_colour_arr=array();
                               foreach($sql_size_name as $asf)
                                {
                                  $size_colour_arr[$asf[csf("size_id")]]=$size_arr[$asf[csf("size_id")]];	
                                }
                                $bundle_data=sql_select("select DISTINCT a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$mst_id." and a.dtls_id=".$dtls_id."  order by a.id ASC");
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
                                      <td align="center"  id=""><input type="text" id="sirialNo_<? echo $i;  ?>" name="sirialNo[]" style="width:40px;" class="text_boxes"  value="<? echo $i;  ?>"  disabled/>
                                       <input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]"  value="<? echo $i;  ?>"style="width:30px;" />
                                       <input type="hidden" id="hiddenUpdateFlag_<? echo $i;  ?>" name="hiddenUpdateFlag[]"  value="<? echo $row[csf('update_flag')];?> " style="width:30px;" />
                                       <input type="hidden" id="hiddenUpdateValue_<? echo $i;  ?>" name="hiddenUpdateValue[]"  value="<? echo $row[csf('update_value')];?> " style="width:30px;" />
                                       </td>
                                       <td align="center">
                                       <input type="text" name="bundleNo[]" id="bundleNo_<? echo $i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes"  style="width:80px; text-align:center" disabled/>
                                       </td>
                                       <td align="center">
                                       <input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onKeyUp="bundle_calclution(<? echo $i;  ?>,<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right; <? if(trim($update_f_value[0])!="") echo "background-color:#F3F;"; ?>" class="text_boxes"  disabled/>
                                           <input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<? echo $i;  ?>" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right" class="text_boxes"  disabled/>
                                       </td>
                                       <td align="center">
                                        <input type="text" name="rmgNoStart[]" id="rmgNoStart_<? echo $i;  ?>" value="<? echo $row[csf('number_start')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled />
                                        </td>
                                       <td align="center">
                                       <input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<? echo $i;  ?>" value="<? echo $row[csf('number_end')];  ?>" style="width:50px; text-align:right" class="text_boxes"  disabled/>
                                        
                                       </td>
                                     <td align="center" id="update_sizename_<? echo $i;  ?>">
                                        <select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:100px; text-align:center;  <? if(trim($update_f_value[1])!="") echo "background-color:#F3F;"; ?>    "  disabled  >
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
                                       <td align="center">
                                       <input type="button"  value="+" name="addButton[]" id="addButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<? echo $i;  ?>','<? echo $i;  ?>')"/>
                                         <input type="button"  value="Adj." name="rowUpdate[]" id="rowUpdate_<? echo $i;  ?>" style="width:50px" class="formbuttonplasminus" onClick="fnc_updateRow('<? echo $i;  ?>')"/>
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
                 <table cellpadding="0" cellspacing="0" width="700" class="rpt_table">
                    <tr>
                        <td colspan="8" align="center" class="button_container">
                             <? echo load_submit_buttons( $permission, "fnc_cut_lay_bundle_info", 1,0,"clear_size_form()",1);?>
                        </td>
                    </tr>
                 </table>
            </fieldset>
    
        <?	
	}
}
if($action=="cut_lay_bundle_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$po_number_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
	
	$sql="select a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width ,a.fabric_width, a.gsm, b.order_id,b.color_id,b.gmt_item_id,b.plies,b.marker_qty ,b.order_qty from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' and a.entry_form=76 ";
	//print_r($table_no_library);die;
	$dataArray=sql_select($sql);
	$sql_buyer=sql_select("select buyer_name from wo_po_details_master where job_no='".$dataArray[0][csf('job_no')]."' and company_name=$data[0]  ");
	$cut_no_prifix=$dataArray[0][csf('cut_num_prefix_no')];
	$cut_no=$company_short_arr[$data[0]]."-".$cut_no_prifix;
?>
<div style="width:1100px; ">
    <table width="900" cellspacing="0" align="center">
         <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Lay and Bundle Information</u></strong></td>
        </tr>
         <tr>
        	<td width="120"><strong>Cut No:</strong></td><td width="160"><? echo $cut_no; ?></td>
            <td width="120"><strong>Table No :</strong></td> <td width="160"><?  echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
            <td width="120"><strong>Job No :</strong></td> <td width="160"><? echo $dataArray[0][csf('job_no')]; ?></td>
        </tr>
         <tr>
			<td><strong>Buyer:</strong></td> <td width="160"><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
            <td><strong>Order No :</strong></td> <td width="160"><? echo $po_number_library[$dataArray[0][csf('order_id')]]; ?></td>
             <td><strong>Order qty:</strong></td> <td width="160"><? echo $dataArray[0][csf('order_qty')]; ?></td>
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
     <div style="width:1000px;">
     <?
	if($data[4]==1)
	{
		$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		?>
		<table align="center" cellspacing="0" width="700"  border="1" rules="all" class="rpt_table" >
              <thead bgcolor="#dddddd" align="center">
                      <th ></th>
                      <th colspan="3"></th>
                      <th >Bundle</th>
                      <th  colspan="2">RMG Number</th>
                      <th ></th>
              </thead>
              <thead bgcolor="#dddddd" align="center">
                      <th width="80">SL No</th>
                      <th width="140">Cut No</th>
                      <th width="100">Country</th>
                      <th width="140">Bundle No</th>
                      <th width="80">Quantity</th>
                      <th width="80">From</th>
                      <th width="80">To</th>
                      <th width="80" rowspan="2" >Size</th>
                </thead>
                <tbody> 
                <?  
                //echo "select DISTINCT a.size_id from  ppl_cut_lay_size a where  a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.size_id ASC";die;
                // $bundle_data=sql_select("select DISTINCT a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$mst_id." and a.dtls_id=".$details_id."  order by a.id ASC");
                
                     $size_data=sql_select("select size_id,marker_qty as sqty from ppl_cut_lay_size_dtls where mst_id='$data[1]' and dtls_id='$data[2]' order by id ASC");    
                     $j=1;
                     foreach($size_data as $size_val)
                     {
              			$total_marker_qty_size=0;
                       $bundle_data=sql_select("select DISTINCT a.id,a.bundle_no as bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.country_id from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]."  order by a.id ASC");
                        foreach($bundle_data as $row)
                           { 
                           $bundle_prifix=explode('-',$row[csf('bundle_no')]); 
                ?>
                           <tr>
                               <td align="center"><? echo $j;  ?></td>
                               <td align="center"><? echo str_replace("0","",$dataArray[0][csf('cutting_no')]);   ?></td>
                      		   <td align="center"><? echo $country_arr[$row[csf('country_id')]];   ?></td>
                               <td align="center"><? echo $bundle_prifix[2];  ?></td>
                               <td align="center"><? echo $row[csf('size_qty')];  ?></td>
                               <td align="center"><? echo $cut_no_prifix."-".$row[csf('number_start')];  ?></td>
                               <td align="center"><? echo $cut_no_prifix."-".$row[csf('number_end')];  ?></td>
                               <td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
                               </tr>
                <?
							$total_marker_qty_size+=$row[csf('size_qty')];
							$total_marker_qty+=$row[csf('size_qty')];
                           $j++;
                          }
                        
                ?>		  
                         <tr bgcolor="#eeeeee">
                               <td align="center"></td>
                               <td  colspan="3" align="right">  <? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
                              
                               <td align="center"><? echo $total_marker_qty_size;  ?></td>
                               <td align="center"></td>
                               <td align="center"></td>
                               <td align="center"></td>
                               </tr>		  
                <?	  
                       }
                ?>
                           <tr bgcolor="#BBBBBB">
                               <td align="center"></td>
                               <td  colspan="3"  align="right"> Total marker qty</td>
                              
                               <td align="center"><? echo $total_marker_qty;  ?></td>
                               <td align="center"></td>
                               <td align="center"></td>
                               <td align="center"></td>
                           </tr>
              </tbody>
      </table>
		
		<?
		}
		else
		{
		?>
			<table align="center" cellspacing="0" width="700"  border="1" rules="all" class="rpt_table" >
              <thead bgcolor="#dddddd" align="center">
                      <th ></th>
                      <th colspan="2"></th>
                      <th >Bundle</th>
                      <th  colspan="2">RMG Number</th>
                      <th ></th>
              </thead>
              <thead bgcolor="#dddddd" align="center">
                      <th width="80">SL No</th>
                      <th width="140">Cut No</th>
                      <th width="140">Bundle No</th>
                      <th width="80">Quantity</th>
                      <th width="80">From</th>
                      <th width="80">To</th>
                      <th width="80" rowspan="2" >Size</th>
                </thead>
                <tbody> 
                <?  
                //echo "select DISTINCT a.size_id from  ppl_cut_lay_size a where  a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.size_id ASC";die;
                // $bundle_data=sql_select("select DISTINCT a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty from ppl_cut_lay_bundle a, ppl_cut_lay_size b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id=".$mst_id." and a.dtls_id=".$details_id."  order by a.id ASC");
                
                
                     $size_data=sql_select("select DISTINCT a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size a where  a.mst_id='$data[1]' and a.dtls_id='$data[2]' order by a.bundle_sequence ASC");    
                    $j=1;
                     foreach($size_data as $size_val)
                      {
                        
                       $bundle_data=sql_select("select DISTINCT a.id,a.bundle_no as bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty from ppl_cut_lay_bundle a  where  a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]."  order by a.id ASC");
                        foreach($bundle_data as $row)
                           { 
                           $bundle_prifix=explode('-',$row[csf('bundle_no')]); 
                ?>
                           <tr>
                               <td align="center"><? echo $j;  ?></td>
                                <td align="center"><? echo str_replace("0","",$dataArray[0][csf('cutting_no')]);   ?></td>
                               <td align="center"><? echo $bundle_prifix[2];  ?></td>
                               <td align="center"><? echo $row[csf('size_qty')];  ?></td>
                               <td align="center"><? echo $cut_no_prifix."-".$row[csf('number_start')];  ?></td>
                               <td align="center"><? echo $cut_no_prifix."-".$row[csf('number_end')];  ?></td>
                               <td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
                               </tr>
                <?
                           $j++;
                          }
                        $total_marker_qty+=$size_val[csf('marker_qty')];
                ?>		  
                         <tr bgcolor="#eeeeee">
                               <td align="center"></td>
                               <td  colspan="4" align="right">  <? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
                              
                               <td align="center"><? echo $size_val[csf('marker_qty')];  ?></td>
                               <td align="center"></td>
                               </tr>		  
                <?	  
                       }
                ?>
                           <tr bgcolor="#BBBBBB">
                               <td align="center"></td>
                               <td  colspan="4"  align="right"> Total marker qty</td>
                              
                               <td align="center"><? echo $total_marker_qty;  ?></td>
                               <td align="center"></td>
                           </tr>
              </tbody>
      </table>
		
		<?
		}
	  
	  ?>
        <br>
		 <?
            echo signature_table(9, $data[0], "900px");
         ?>
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
            <table width="980" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="150">Company name</th>
                    <th width="150">Buyer name</th>
                    <th width="100">Job No</th>
                    <th width="100">Order No</th>
                    <th width="220">Date Range</th>
                    <th width="140"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
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
                          <input style="width:100px;" type="text"  class="text_boxes"   name="txt_job_prifix" id="txt_job_prifix"  />
                      </td>
                      <td width="100">
                          <input style="width:120px;" type="text"  class="text_boxes"   name="txt_po_no" id="txt_po_no"  />
                      </td>
                    <td align="center">
                           <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                           <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                    </td>
                    <td align="center">
                         <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_po_no').value, 'create_job_search_list_view', 'search_div', 'cut_and_lay_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        		<tr>                  
            	<td align="center" height="40" valign="middle" colspan="6">
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
	$job_cond="";
	
	if(str_replace("'","",$company)=="") $conpany_cond=""; else $conpany_cond="and b.company_name=".str_replace("'","",$company)."";
	if(str_replace("'","",$buyer)==0) $buyer_cond=""; else $buyer_cond="and b.buyer_name=".str_replace("'","",$buyer)."";
    if($db_type==2) $year_cond=" and extract(year from b.insert_date)=$job_year";
    if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$job_year";
	if(str_replace("'","",$job_prifix)!="")  $job_cond="and b.job_no_prefix_num=".str_replace("'","",$job_prifix)."  $year_cond";
	if(str_replace("'","",$po_no)!="")  $order_cond="and a.po_number like '%".str_replace("'","",$po_no)."%' "; else $order_cond="";
	if($db_type==0)
	{
		if( $from_date!="" && $to_date!="" ) $sql_cond = " and a.pub_shipment_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	$sql_order="select b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,SUBSTRING_INDEX(b.insert_date, '-', 1) as year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond group by b.buyer_name,b.job_no,a.po_number ";  
	}
	
	if($db_type==2)
	{
	 if( str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="" ) 
	  {
		  $sql_cond = " and a.pub_shipment_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	  }
	
	 
	$sql_order="select b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,extract(year from b.insert_date) as year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond group by  b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num, b.insert_date order by  job_no_prefix_num";  
	}
//echo $sql_order;
	$arr=array (3=>$buyer_arr);
	echo create_list_view("list_view", "Job NO,Year,Style Ref,Buyer Name, Orer No,Shipment Date","100,100,150,150,150,150","850","270",0, $sql_order , "js_set_order", "job_no,buyer_name,year", "", 1, "0,0,0,buyer_name,0,0,0", $arr, "job_prefix,year,style_ref_no,buyer_name,po_number,pub_shipment_date", "","setFilterGrid('list_view',-1)") ;	
	
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
		$sql_table=return_field_value("id","lib_cutting_table","company_id =$cbo_company_name AND location_id =$cbo_location_name AND floor_id =$cbo_floor_name AND table_no = $txt_table_no");
	   	if($sql_table!="")
		{
			$tbl_id=$sql_table;
		}
	   	else
		{
			$tbl_id=return_next_id("id", "  lib_cutting_table", 1);
			$field_array_table="id,table_no,company_id,location_id,floor_id,inserted_by,insert_date,status_active,is_deleted";
			$data_array_table="(".$tbl_id.",".$txt_table_no.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_floor_name.",'".$user_id."','".$pc_date_time."',1,0)";
			//$rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);	
		}
	  	if(str_replace("'","",$update_id)=="")
		{
			$job_prifix=return_field_value("job_no_prefix_num","wo_po_details_master","job_no=$txt_job_no");
			//$sql_prifix=sql_select( "SELECT DISTINCT  cut_num_prefix_no FROM ppl_cut_lay_mst WHERE company_id=".$cbo_company_name."  ORDER BY cut_num_prefix_no DESC "); //and entry_form=76

			$new_sys_number = explode("*", return_next_id_by_sequence("", "ppl_cut_lay_mst",$con,1,$cbo_company_name,'',0,date("Y",time()),0,0,0,0,0 ));

			/*if( count($sql_prifix)>0)
			{
				foreach($sql_prifix as $row)
				{
					$cut_no_prifix[]=$row[csf('cut_num_prefix_no')];	
				}
			}
			else
			{
				 $cut_no_prifix[0]=0;	
			}
			$cut_no_prifix[0]+=1;*/
			$cut_no_prifix[]=$new_sys_number[2];

			$comp_prefix=return_field_value("company_short_name","lib_company", "id=$cbo_company_name");
			$cut_no=str_pad((int) $cut_no_prifix[0],6,"0",STR_PAD_LEFT);
			$new_cutting_number=str_replace("--", "-",$new_sys_number[1]).$cut_no;
			$new_cutting_prifix=str_replace("--", "-",$new_sys_number[1]);
			//$id=return_next_id("id", " ppl_cut_lay_mst", 1);	
			$id= return_next_id_by_sequence(  "ppl_cut_lay_mst_seq",  "ppl_cut_lay_mst", $con );

			$field_array="id,cut_num_prefix,cut_num_prefix_no,cutting_no,table_no,job_no,batch_id,company_id,entry_date,start_time,end_date,end_time ,marker_length,marker_width,fabric_width,lay_fabric_wght,cad_marker_cons,gsm,width_dia,remarks,entry_form,inserted_by,insert_date,status_active,is_deleted,efficiency,wastage_qnty,extra_lay_wgt";
			$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
			$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
			$data_array="(".$id.",'".$new_cutting_prifix."',".$cut_no_prifix[0].",'".$new_cutting_number."',".$tbl_id.",".$txt_job_no.",".$txt_batch_no.",".$cbo_company_name.",".$txt_entry_date.",'".$start_time."',".$txt_end_date.",'".$end_time."',".$txt_marker_length.",".$txt_marker_width.",".$txt_fabric_width.",".$txt_lay_wght.",".$txt_marker_cons.",".$txt_gsm.",".$cbo_width_dia.",".$txt_remark.",76,'".$user_id."','".$pc_date_time."',1,0,".$txt_efficiency.",".$txt_wastage_qnty.",".$hidden_lay_extra_wgt.")";
				//$rID1=sql_insert(" ppl_cut_lay_mst",$field_array,$data_array,0); 
		}
		else
		{
			$field_array="table_no*job_no*batch_id*company_id*entry_date*start_time*entry_date*end_time*marker_length*marker_width*fabric_width*lay_fabric_wght*cad_marker_cons*gsm*width_dia*remarks*updated_by*update_date*efficiency*wastage_qnty*extra_lay_wgt";
			$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
			   $end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
			$data_array="".$update_tbl_id."*".$txt_job_no."*".$txt_batch_no."*".$cbo_company_name."*".$txt_entry_date."*'".$start_time."'*,".$txt_end_date."*'".$end_time."'*".$txt_marker_length."*".$txt_marker_width."*".$txt_fabric_width."*".$txt_lay_wght."*".$txt_marker_cons."*".$txt_gsm."*".$cbo_width_dia."*".$txt_remark."*'".$user_id."'*'".$pc_date_time."'*".$txt_efficiency."*".$txt_wastage_qnty."*".$hidden_lay_extra_wgt."";
 		}
	    //$detls_id=return_next_id("id", " ppl_cut_lay_dtls", 1);
	    $detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
	    $field_array1="id, mst_id,order_id,order_cut_no,ship_date,color_id,gmt_item_id,plies,order_qty,roll_data,inserted_by,insert_date,status_active,is_deleted,extra_roll_data";
		$field_array_up="order_id*order_cut_no*ship_date*color_id*gmt_item_id*plies*order_qty*roll_data*updated_by*update_date*extra_roll_data";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );

	    $add_comma=0;
	    $field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_id, roll_no, plies, inserted_by, insert_date,is_extra_roll";
		
		//$order_cut_no_arr=return_library_array( "select order_id,max(order_cut_no) as order_cut_no from ppl_cut_lay_dtls group by order_id", "order_id", "order_cut_no"  );
			
			for($i=1; $i<=$row_num; $i++)
			{
				$cbo_order_id="cboorderno_".$i;
				$txt_ship_date="txtshipdate_".$i;
				$cbocolor="cbocolor_".$i;
				$cbo_gmt_id="cbogmtsitem_".$i;
				$order_qty="txtorderqty_".$i;
				$txt_plics="txtplics_".$i;
				$rollData="rollData_".$i;
				$extra_roll="hiddenExtralRollData_".$i;
				$update_details_id="updateDetails_".$i;
				$order_cut_no="orderCutNo_".$i;
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

				$save_string=explode("__",str_replace("'",'',$$rollData)); $response_data='';
				if(str_replace("'",'',$$rollData) !="")
				{
					for($x=0;$x<count($save_string);$x++)
					{
						$roll_dtls=explode("=",$save_string[$x]);
						$barcode_no=$roll_dtls[0];
						$roll_no=$roll_dtls[1];
						$roll_id=$roll_dtls[2];
						$roll_qnty=$roll_dtls[3];
						$plies=$roll_dtls[4];
						$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
		//$new_grey_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'GPE',2,date("Y",time()),13 ));

						if(str_replace("'",'',$$roll_maintained)!=1) $roll_id=$id_roll;
						
						if($data_array_roll!="") $data_array_roll.= ",";
						$data_array_roll.="(".$id_roll.",".$barcode_no.",".$master_id.",".$dtlsId.",".$$cbo_order_id.",'138','".$roll_qnty."','".$roll_id."','".$roll_no."','".$plies."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','')";
						$response_data.=$barcode_no."=".$roll_no."=".$roll_id."=".$roll_qnty."=".$plies."__";
						//$id_roll = $id_roll+1;
					}
				}
				$extra_save_string=explode("__",str_replace("'",'',$$extra_roll)); 
				$extra_response_data='';
				if(str_replace("'",'',$$extra_roll) !="")
				{
					
					for($x=0;$x<count($extra_save_string);$x++)
					{
						$extra_roll_dtls=explode("=",$extra_save_string[$x]);
 						$roll_no=$extra_roll_dtls[0];
						$roll_id=$extra_roll_dtls[1];
						$roll_qnty=$extra_roll_dtls[2];
						$plies=$extra_roll_dtls[3];
						$barcode_no=0;
						$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						if(str_replace("'",'',$$roll_maintained)!=1) $roll_id=$id_roll;
						
						if($data_array_roll!="") $data_array_roll.= ",";
						$data_array_roll.="(".$id_roll.",".$barcode_no.",".$master_id.",".$dtlsId.",".$$cbo_order_id.",'138','".$roll_qnty."','".$roll_id."','".$roll_no."','".$plies."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1')";
						$extra_response_data.=$barcode_no."=".$roll_no."=".$roll_id."=".$roll_qnty."=".$plies."__";
						//$id_roll = $id_roll+1;
					}
				}

				 $response_data=substr($response_data,0,-2);
				 $extra_response_data=substr($extra_response_data,0,-2);
					
				if(str_replace("'",'',$$update_details_id)!="")  
				{
					$updateID_array[]=str_replace("'",'',$$update_details_id); 
					$data_array_up[str_replace("'",'',$$update_details_id)]=explode("*",("".$$cbo_order_id."*".$$cbo_order_id."*".$$txt_ship_date."*".$$cbocolor."*".$$cbo_gmt_id."*".$$txt_plics."*".$$order_qty."*'".$response_data."'*'".$user_id."'*'".$pc_date_time."'*'".$extra_response_data."'"));
				
				}
				else
				{
				   if ($add_comma!=0) $data_array1 .=",";
				   if ($add_comma!=0) $detls_id_array .="_";
					$data_array1.="(".$detls_id.",".$master_id.",".$$cbo_order_id.",".$$order_cut_no.",".$$txt_ship_date.",".$$cbocolor.",".$$cbo_gmt_id.",".$$txt_plics.",".$$order_qty.",'".$response_data."','".$user_id."','".$pc_date_time."',1,0,'".$extra_response_data."')";   
					$detls_id_array.=$detls_id."#".str_replace("'",$$order_cut_no);
					//$detls_id_array.=$detls_id."#".str_replace("'",$$order_cut_no);
					//$detls_id=$detls_id+1;
					$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
					$add_comma++;
				}


				
				
		     }
				
			//$detls_id_update.=implode("_",$updateID_array);
			$detls_id_update.=$detls_id_array;
		    if($sql_table=="") 
		    { $rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);	 }
			
			 if(str_replace("'","",$update_id)=="")
			   {
				$rID1=sql_insert(" ppl_cut_lay_mst",$field_array,$data_array,0);    
			   }
			  else
			  {
				$rID1=sql_update("ppl_cut_lay_mst",$field_array,$data_array,"id",$update_id,0);   
			  }
			if(count($updateID_array)>0)
				{
					$rID2=execute_query(bulk_update_sql_statement("ppl_cut_lay_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);
				}
			if(count($data_array1)>0)  
				{
			       $rID2=sql_insert(" ppl_cut_lay_dtls",$field_array1,$data_array1,1); 
				}

				if($data_array_roll!="")
				{
					$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
				}
				//echo "10**insert into ppl_cut_lay_size($field_array1)values".$data_array1;die;
				 // echo "1022**".$rID3;die;
	      if($db_type==0)
		        {
				  if(str_replace("'","",$update_id)=="")
				        {
							if($rID1 && $rID2 )
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
							 if( $rID1 && $rID2 )
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
			if($db_type==2 || $db_type==1 )
				{
					
				  if(str_replace("'","",$update_id)=="")
				        {
							if($rID1 && $rID2)
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
							 if( $rID1 && $rID2)
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
			$con = connect();	
 			if($db_type==0)	{ mysql_query("BEGIN"); }
			$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","cutting_no=".$txt_cutting_no."");
			if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; die;}
		    $sql_table=return_field_value("id","lib_cutting_table","company_id =$cbo_company_name AND location_id =$cbo_location_name AND floor_id =$cbo_floor_name AND table_no = $txt_table_no");
		    if($sql_table!="")
			    {
			    	 $tbl_id=$sql_table;
			    }
			else
				{
					$tbl_id=return_next_id("id", "  lib_cutting_table", 1);
					$field_array_table="id,table_no,company_id,location_id,floor_id,inserted_by,insert_date,status_active,is_deleted";
					$data_array_table="(".$tbl_id.",".$txt_table_no.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_floor_name.",'".$user_id."','".$pc_date_time."',1,0)";
					//echo "insert into  ppl_cut_lay_table_no($field_array_table) values".$data_array_table;
					$rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);	
				}
			//master table update*********************************************************************
			$field_array="table_no*job_no*batch_id*company_id*entry_date*start_time*end_date*end_time*marker_length*marker_width*fabric_width*lay_fabric_wght*cad_marker_cons*gsm*width_dia*remarks*updated_by*update_date*efficiency*wastage_qnty*extra_lay_wgt";
			$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
			$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
			$data_array="".$tbl_id."*".$txt_job_no."*".$txt_batch_no."*".$cbo_company_name."*".$txt_entry_date."*'".$start_time."'*".$txt_end_date."*'".$end_time."'*".$txt_marker_length."*".$txt_marker_width."*".$txt_fabric_width."*".$txt_lay_wght."*".$txt_marker_cons."*".$txt_gsm."*".$cbo_width_dia."*".$txt_remark."*'".$user_id."'*'".$pc_date_time."'*".$txt_efficiency."*".$txt_wastage_qnty."*".$hidden_lay_extra_wgt."";
			$rID1=sql_update("ppl_cut_lay_mst",$field_array,$data_array,"id",$update_id,0);
		    //$detls_id=return_next_id("id", " ppl_cut_lay_dtls", 1);
		    $detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
			$field_array1="id, mst_id,order_id,order_cut_no,ship_date,color_id,gmt_item_id,plies,order_qty,roll_data,inserted_by,insert_date,status_active,is_deleted,extra_roll_data";
			$field_array_up="order_id*order_cut_no*ship_date*color_id*gmt_item_id*plies*order_qty*roll_data*updated_by*update_date*extra_roll_data";
			$add_comma=0;
			//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
			$order_cut_no_arr=return_library_array( "select order_id,max(order_cut_no) as order_cut_no from ppl_cut_lay_dtls group by order_id", "order_id", "order_cut_no"  );
			$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_id, roll_no, plies, inserted_by, insert_date,is_extra_roll";
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
					$extra_roll="hiddenExtralRollData_".$i;
					// echo $$extra_roll;die;
					if(str_replace("'","",$update_id)!="")
						  {
							    $msster_id=$update_id;
						  }
						  else
						  {
							    $msster_id=$id;  
						  }

					if(str_replace("'",'',$$update_details_id)!="")  
					{ 
						$dtlsId=str_replace("'",'',$$update_details_id);
					}
					else
					{
						$dtlsId=$detls_id; 
					}

				$save_string=explode("__",str_replace("'",'',$$rollData)); $response_data='';
				if(str_replace("'","",$$rollData) !="")
				{
					for($x=0;$x<count($save_string);$x++)
					{
						$roll_dtls=explode("=",$save_string[$x]);
						$barcode_no=$roll_dtls[0];
						$roll_no=$roll_dtls[1];
						$roll_id=$roll_dtls[2];
						$roll_qnty=$roll_dtls[3];
						$plies=$roll_dtls[4];
						$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						if(str_replace("'",'',$$roll_maintained)!=1) $roll_id=$id_roll;
						
						if($data_array_roll!="") $data_array_roll.= ",";
						$data_array_roll.="(".$id_roll.",".$barcode_no.",".$msster_id.",".$dtlsId.",".$$cbo_order_id.",'138','".$roll_qnty."','".$roll_id."','".$roll_no."','".$plies."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','')";
						$response_data.=$barcode_no."=".$roll_no."=".$roll_id."=".$roll_qnty."=".$plies."__";
						//$id_roll = $id_roll+1;
					}
				}

				$extra_save_string=explode("__",str_replace("'",'',$$extra_roll)); 
				//print_r($extra_save_string);die;
				$extra_response_data='';
				if(str_replace("'","",$$extra_roll) !="")
				{
					
					for($x=0;$x<count($extra_save_string);$x++)
					{
						$extra_roll_dtls=explode("=",$extra_save_string[$x]);
						$barcode_no=$extra_roll_dtls[0];
 						$roll_no=$extra_roll_dtls[1];
						$roll_id=$extra_roll_dtls[2];
						$roll_qnty=$extra_roll_dtls[3];
						$plies=$extra_roll_dtls[4];
						$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
 						if(str_replace("'",'',$$roll_maintained)!=1) $roll_id=$id_roll;
						
						if($data_array_roll!="") $data_array_roll.= ",";
						$data_array_roll.="(".$id_roll.",".$barcode_no.",".$msster_id.",".$dtlsId.",".$$cbo_order_id.",'138','".$roll_qnty."','".$roll_id."','".$roll_no."','".$plies."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1')";
						$extra_response_data.=$barcode_no."=".$roll_no."=".$roll_id."=".$roll_qnty."=".$plies."__";
						//$id_roll = $id_roll+1;
					}
				}
 
			      $response_data=substr($response_data,0,-2);
			      $extra_response_data=substr($extra_response_data,0,-2);
  			      //$response_data=str_replace('**', '__', $response_data);
 					if(str_replace("'",'',$$update_details_id)!="")  
					  {
						//$field_array_up="order_id*order_cut_no*ship_date*color_id*gmt_item_id*plies*order_qty*roll_data*updated_by*update_date*extra_roll_data";

							$updateID_array[]=str_replace("'",'',$$update_details_id); 
							$data_array_up[str_replace("'",'',$$update_details_id)]=explode("*",("".$$cbo_order_id."*".$$order_cut_no."*".$$txt_ship_date."*".$$cbocolor."*".$$cbo_gmt_id."*".$$txt_plics."*".$$order_qty."*'".$response_data."'*'".$user_id."'*'".$pc_date_time."'*'".$extra_response_data."'"));
							
							if ($add_comma!=0) $detls_id_array .="_";
							$detls_id_array.=str_replace("'",'',$$update_details_id)."#".str_replace("'",'',$$order_cut_no);
							$add_comma++;
					  }

					 else
					  {
						  //	$order_cut_no=$order_cut_no_arr[str_replace("'","",$$cbo_order_id)]+1;
							//$order_cut_no_arr[str_replace("'","",$$cbo_order_id)]=$order_cut_no;
							
							if ($add_comma!=0) $data_array1 .=",";
							if ($add_comma!=0) $detls_id_array .="_";
							$data_array1.="(".$detls_id.",".$msster_id.",".$$cbo_order_id.",".$$order_cut_no.",".$$txt_ship_date.",".$$cbocolor.",".$$cbo_gmt_id.",".$$txt_plics.",".$$order_qty.",'".$response_data."','".$user_id."','".$pc_date_time."',1,0,'".$extra_response_data."')";   
							$detls_id_array.=$detls_id."#".str_replace("'",'',$$order_cut_no);
							//$detls_id=$detls_id+1;
							$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
							$add_comma++;
					  }
						 // print_r($data_array_up);
				  }
			//$detls_id_update.=implode("_",$updateID_array);
			$detls_id_update.=$detls_id_array;
			if(count($updateID_array)>0)
				{
					$rID2=execute_query(bulk_update_sql_statement("ppl_cut_lay_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);
				}

			if(count($data_array1)>0)  
				{
				   $rID2=sql_insert(" ppl_cut_lay_dtls",$field_array1,$data_array1,1); 
				}
			$delete_roll=execute_query("delete from pro_roll_details where mst_id=$msster_id and entry_form=138 or entry_form=127",0);	
			if($data_array_roll!="")
			{
				$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			}	 
   //echo  $rID1 . $rID2 . $rID3;die;
 		 if($db_type==0)
			 {
				if($rID1 && $rID2 )
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

			if($db_type==2 || $db_type==1 )
			  {
					if($rID1 && $rID2 )
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
            <table width="950" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="140">Company name</th>
                    <th width="130">Cutting No</th>
                    <th width="130">Job No</th>
                    <th width="130">Order No</th>
                    <th width="250">Date Range</th>
                    <th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                  <tr>                    
                        <td>
                              <? 
                        
                                   echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1);
                             ?>
                        </td>
                      
                        <td align="center" >
                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes"/>
                                <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td align="center">
                               <input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:120px"  />
                        </td>
                        <td align="center">
                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
                        </td>
                        <td align="center" width="250">
                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                        </td>
                        <td align="center">
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_cutting_search_list_view', 'search_div', 'cut_and_lay_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                        </td>
                 </tr>
        		 <tr>                  
                        <td align="center" height="40" valign="middle" colspan="6">
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
	  if($db_type==2)
	       {
			      $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		   }
	}
	
	$sql_order="select a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,d.order_id,d.color_id,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where  a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.entry_form=76 and c.id=d.order_id $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond order by id";
	$table_no_arr=return_library_array( "select id,table_no  from  lib_cutting_table",'id','table_no');
	$arr=array(2=>$table_no_arr,4=>$order_number_arr,5=>$color_arr);
	echo create_list_view("list_view", "Cut No,Year,Table No,Job No,Order NO,Color,Marker Length,Markar Width,Fabric Width,Entry Date","90,50,60,120,120,100,80,80,80,120","950","270",0, $sql_order , "js_set_cutting_value", "id", "", 1, "0,0,table_no,0,order_id,color_id,0,0,0,0,0", $arr, "cut_num_prefix_no,year,table_no,job_no,order_id,color_id,marker_length,marker_width,fabric_width,entry_date", "","setFilterGrid('list_view',-1)") ;
}


if($action=="load_php_mst_form")
{
    $sql_data=sql_select("select b.id as   tbl_id,b.table_no,b.location_id,b.floor_id,a.id,a.job_no,a.company_id,a.remarks,a.entry_date,end_date,a.marker_length,a.marker_width,a.fabric_width,a.lay_fabric_wght,a.cad_marker_cons,a.gsm,a.width_dia,a.cutting_no,a.batch_id,a.start_time,a.end_time,a.efficiency,a.wastage_qnty,extra_lay_wgt
	from  ppl_cut_lay_mst a, lib_cutting_table b
	where   a.table_no=b.id and a.entry_form=76 and a.id=".$data." ");
	
    foreach($sql_data as $val)
	  {
		    $start_time=explode(":",$val[csf("start_time")]);
		    $end_time=explode(":",$val[csf("end_time")]);
			echo "document.getElementById('cbo_company_name').value = '".($val[csf("company_id")])."';\n"; 
			echo "document.getElementById('txt_table_no').value = '".($val[csf("table_no")])."';\n"; 
			echo "$('#txt_entry_date').val('".change_date_format($val[csf("entry_date")])."');\n";  
			echo "$('#txt_end_date').val('".change_date_format($val[csf("end_date")])."');\n"; 
			echo "document.getElementById('txt_marker_length').value  = '".($val[csf("marker_length")])."';\n"; 
			echo "document.getElementById('txt_marker_width').value  = '".($val[csf("marker_width")])."';\n";  
			echo "document.getElementById('txt_fabric_width').value = '".($val[csf("fabric_width")])."';\n"; 
			echo "document.getElementById('txt_lay_wght').value  = '".($val[csf("lay_fabric_wght")])."';\n"; 
			echo "document.getElementById('txt_marker_cons').value  = '".($val[csf("cad_marker_cons")])."';\n";    
			echo "document.getElementById('txt_gsm').value  = '".($val[csf("gsm")])."';\n";
			echo "document.getElementById('cbo_width_dia').value  = '".($val[csf("width_dia")])."';\n";
			echo "document.getElementById('txt_remark').value  = '".($val[csf("remarks")])."';\n";
			echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";  
			echo "document.getElementById('txt_batch_no').value = '".($val[csf("batch_id")])."';\n"; 
			echo "document.getElementById('txt_job_no').value = '".($val[csf("job_no")])."';\n"; 
			echo "document.getElementById('update_tbl_id').value  = '".($val[csf("tbl_id")])."';\n";  
			echo "document.getElementById('update_id').value = '".($val[csf("id")])."';\n";
			echo "document.getElementById('txt_cutting_no').value = '".($val[csf("cutting_no")])."';\n";
			echo "document.getElementById('txt_wastage_qnty').value = '".($val[csf("wastage_qnty")])."';\n";
			echo "document.getElementById('hidden_lay_extra_wgt').value = '".($val[csf("extra_lay_wgt")])."';\n";
			echo "document.getElementById('txt_efficiency').value = '".($val[csf("efficiency")])."';\n";
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
}

if($action=="order_details_list")
{
	// $sql_gmt_arr="select ";
	 $tbl_row=0;
	 $sql_dtls=sql_select("select a.id,a.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,a.lay_balance_qty,b.job_no,b.job_year,b.company_id, a.order_cut_no ,a.roll_data,a.extra_roll_data from ppl_cut_lay_dtls a, ppl_cut_lay_mst b where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and mst_id=".$data." and b.entry_form=76");
	 foreach($sql_dtls as $val)
	   {
		 	$sql="select sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id=".$val[csf("order_id")]." and item_number_id=".$val[csf("gmt_item_id")]." and color_number_id=".$val[csf("color_id")]." and status_active=1 group by po_break_down_id,item_number_id,color_number_id ";
	$result=sql_select($sql);
	foreach($result as $row)
		{
			$plan_qty=$row[csf("plan_qty")];
		}
		$sql_marker="select sum(marker_qty) as mark_qty from  ppl_cut_lay_dtls where order_id=".$val[csf("order_id")]." and gmt_item_id=".$val[csf("gmt_item_id")]." and color_id=".$val[csf("color_id")]." and status_active=1 group by order_id,gmt_item_id,color_id ";
	$result=sql_select($sql_marker);
		foreach($result as $rows)
		{
		    $total_marker_qty=$rows[csf("mark_qty")];
		}
		$lay_balance=$plan_qty-$total_marker_qty;
		   
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
                        <input  type="hidden" id="neworder_<? echo $tbl_row; ?>" name="neworder_<? echo $tbl_row; ?>" />
                </td>  
                <td align="center" id="cutNo_<? echo $tbl_row; ?>">
                    <input style="width:70px;" class="text_boxes_numeric" type="text" name="orderCutNo_<? echo $tbl_row; ?>" id="orderCutNo_<? echo $tbl_row; ?>" placeholder="" value="<? echo $val[csf('order_cut_no')]; ?>"  />
                </td>                            
                <td align="center" id="ship_<? echo $tbl_row; ?>">
                        <input style="width:80px;" type="text"   class="datepicker" autocomplete="off"  name="txtshipdate_<? echo $tbl_row; ?>" id="txtshipdate_<? echo $tbl_row; ?>"   value="<? echo change_date_format($val[csf('ship_date')]);?>"readonly/>
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
                         echo create_drop_down( "cbocolor_".$tbl_row, 130, "select distinct a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b where a.id=b.color_number_id and b.po_break_down_id=".$val[csf('order_id')]." and item_number_id=".$val[csf('gmt_item_id')]."","id,color_name", 1, "select color",  $val[csf('color_id')], "change_marker(this.id,this.value)");
                     ?>
                </td>
                <td align="center">
                       <input type="text" name="txtplics_<? echo $tbl_row; ?>"  id="txtplics_<? echo $tbl_row; ?>" class="text_boxes_numeric"  style="width:80px"  value="<? echo $val[csf('plies')];?>"  onDblClick="openmypage_roll(<? echo $tbl_row; ?>)" readonly/>
                      <input type="hidden" name="hiddenorder_<? echo $tbl_row; ?>"  id="hiddenorder_<? echo $tbl_row; ?>"  />
                      <input type="hidden" name="updateDetails_<? echo $tbl_row; ?>"  id="updateDetails_<? echo $tbl_row; ?>"  value="<? echo $val[csf('id')]; ?>" />

                      <input type="hidden" name="rollData_<? echo $tbl_row; ?>" id="rollData_<? echo $tbl_row; ?>" class="text_boxes" value="<? echo $val[csf('roll_data')]; ?>" />
                      <input type="hidden" name="prifix_id"  id="prifix_id"  />

                      <input type="hidden" name="hiddenExtralRollData_<? echo $tbl_row; ?>" id="hiddenExtralRollData_<? echo $tbl_row; ?>" class="text_boxes" value="<? echo $val[csf('extra_roll_data')]; ?>" />
                </td>
                <td align="center">
                   	  <input type="text" name="txtsizeratio_<? echo $tbl_row; ?>" id="txtsizeratio_<? echo $tbl_row; ?>" class="text_boxes_numeric" onDblClick="openmypage_sizeNo(this.id);"  placeholder="Browse" style="width:50px" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick(1)"/>
                </td>
                <td align="center" id="marker_<? echo $tbl_row; ?>">
                      <input type="text" name="txtmarkerqty_<? echo $tbl_row; ?>"  id="txtmarkerqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px" value="<? echo $val[csf('marker_qty')];?>" />
                </td>
                 <td align="center" id="order_<? echo $tbl_row; ?>">
                     <input type="text" name="txtorderqty_<? echo $tbl_row; ?>" id="txtorderqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  value="<? echo $plan_qty;?>"/>
                </td>
                 <td align="center">
                     <input type="text" name="txttotallay_<? echo $tbl_row; ?>"  id="txttotallay_<? echo $tbl_row; ?>"class="text_boxes_numeric"  placeholder="Display" style="width:60px" value="<? echo $total_marker_qty;?>"/>
                </td>
                <td align="center">
                     <input type="text" name="txtlaybalanceqty_<? echo $tbl_row; ?>"  id="txtlaybalanceqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  value="<? echo $lay_balance;?>"/>
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
	//print_r($data);die;
	$sql=sql_select("select id,job_no,cut_num_prefix_no,table_no,remarks,marker_length,marker_width,fabric_width,gsm,width_dia,batch_id,company_id,lay_fabric_wght,cad_marker_cons,efficiency,wastage_qnty ,extra_lay_wgt
, entry_date,end_date,end_time, start_time from ppl_cut_lay_mst where cutting_no='".$data[0]."' and entry_form=76 ");

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
			$lay_fabric_wght=$val[csf('lay_fabric_wght')];
			$cad_marker_cons=$val[csf('cad_marker_cons')];
			$job_no=$val[csf('job_no')];
			$remarks=$val[csf('remarks')];
			$efficiency=$val[csf('efficiency')];
			$wastage_qnty=$val[csf('wastage_qnty')];
			$extra_lay_wgt=$val[csf('extra_lay_wgt')];
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

	$roll_maintain=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$company_id' and variable_list=3 and item_category_id=51 and is_deleted=0 and status_active=1");

	$sql_buyer_arr=sql_select("select buyer_name,style_ref_no from  wo_po_details_master where job_no='$data[1]'");
	$sql_order=sql_select("select order_id,gmt_item_id,order_qty from ppl_cut_lay_dtls where mst_id='$mst_id'");
	//print_r($sql_order);
	$order_number=""; $order_id='';
	 foreach($sql_order as $order_val)
		 { 
		   $item_name=$order_val[csf('gmt_item_id')];
		   $order_qty+=$order_val[csf('order_qty')]; 
		   if($order_number!="")  $order_number.=",".$order_number_arr[$order_val[csf('order_id')]];
		   else  $order_number=$order_number_arr[$order_val[csf('order_id')]];
		   
		   if($order_id!="")  $order_id.=",".$order_val[csf('order_id')];
		   else  $order_id=$order_val[csf('order_id')];
		 }
		 
	$order_id=implode(",",array_unique(explode(",",$order_id)));
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
              <td>Order No</td><td align="center"><p> <? echo implode(",",array_unique(explode(",",$order_number))); ?></p></td>
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
              <td>Start Date & Start Time</td><td align="center" width="100"><?php
			  $plan_start_time=change_date_format($val[csf('entry_date')])." ".$val[csf('start_time')];
			  $plan_end_time=change_date_format($val[csf('end_date')])." ".$val[csf('end_time')];
			  
			//echo  datediff( "d", $plan_start_time, $plan_end_time) ;die;
			  $time_difference=strtotime($plan_end_time)-strtotime($plan_start_time);
			  $day=""; $hour="";$min="";
			  if($time_difference>=86400)
			  {
				  $day=floor($time_difference/86400);
				  $hour_difference=$time_difference-($day*86400);
				  if($hour_difference>0)
				  $hour=floor($hour_difference/3600);
				  $min_difference=$hour_difference-($hour*3600);
				  if($min_difference>0)
				  {
					 $min= ($min_difference/60);
				  }
			  }
			  else
			  {
				  $hour=floor($time_difference/3600);
				  $min_difference=$time_difference-($hour*3600);
				  if($min_difference>0)
				  {
					 $min= ($min_difference/60);
				  }
			  }
			  $date_string='';
			  if($day==1) $date_string=$day." Day";
			  else if($day>1) $date_string=$day." Days ";
			  if($hour>0) $date_string.=$hour." Hour ";
			  if($min>0) $date_string.=$min." Min ";
			  //echo $day."Day".$hour."Hour".$min."Min";die;
			   echo change_date_format($val[csf('entry_date')])." ".$val[csf('start_time')]; ?></td><td align="center" width="100"><strong>Total Time Taken</strong></td>
         </tr >
         <tr height="30">
              <td>End Date & End Time</td><td align="center" width="100"><?php echo change_date_format($val[csf('end_date')])." ".$val[csf('end_time')]; ?></td><td align="center" width="100"><?php echo $date_string; ?></td>
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

        
         <br>


   <?
   
	if($data[2]==1)
	{  
		$sql_main_qry=sql_select("select a.id,a.color_id,a.plies,b.size_id,sum(b.size_ratio) as size_ratio,sum(b.marker_qty) as marker_qty from ppl_cut_lay_size b,ppl_cut_lay_dtls a where a.id=b.dtls_id and a.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id,a.color_id,a.plies,b.size_id order by a.id");
		//echo "select a.id,a.color_id,a.plies,b.size_id,sum(b.size_ratio) as size_ratio,sum(b.marker_qty) as marker_qty from ppl_cut_lay_size b,ppl_cut_lay_dtls a where a.id=b.dtls_id and a.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id,a.color_id,a.plies,b.size_id order by a.id";die;
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 foreach($sql_main_qry as $main_val)
	 {
		$size_qty=return_field_value("max(size_qty) as size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =".$main_val[csf('id')]." ","size_qty");
	    //$size_id_arr[$main_val[csf('size_id')]]=$main_val[csf('size_id')];
		$plice_data_arr[$main_val[csf('id')]]['plies']=$main_val[csf('plies')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('size_id')]]['size']=$main_val[csf('size_id')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('size_id')]]['size_ratio']=$main_val[csf('size_ratio')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('size_id')]]['marker_qty']=$main_val[csf('marker_qty')];
		
		$total_gmt_qty[$main_val[csf('id')]]['gmt_qty']+=$main_val[csf('marker_qty')];
		$grand_total+=$main_val[csf('marker_qty')];
		$plice_data_arr[$main_val[csf('id')]]['bundle_qty']=$size_qty;
		$plice_data_arr[$main_val[csf('id')]]['color']=$main_val[csf('color_id')];
	 }
	 
	 $sizeRatio=array();
	 $sizeData=sql_select("select size_id,dtls_id,color_id, size_ratio FROM ppl_cut_lay_size_dtls WHERE mst_id = $mst_id and status_active=1 and is_deleted=0 order by id");
	 foreach($sizeData as $sRow)
	 {
		$size_id_arr[$sRow[csf('size_id')]]= $sRow[csf('size_ratio')];
		$sizeRatio[$sRow[csf('dtls_id')]][$sRow[csf('size_id')]]= $sRow[csf('size_ratio')];
	 }
	
	 $sql_bundle_qry=sql_select("select a.id,a.size_qty,a.dtls_id,a.size_id from ppl_cut_lay_dtls b, ppl_cut_lay_bundle a where b.mst_id=$mst_id 
	 and b.mst_id=a.mst_id and a.status_active=1  and  a.is_deleted=0   and b.status_active=1  and  b.is_deleted=0
	 group by a.id,a.size_qty,a.dtls_id,a.size_id
	  order by a.id");
	 
	 foreach($sql_bundle_qry as $sval)
	 {
		$maximum_qty=$plice_data_arr[$sval[csf('dtls_id')]]['bundle_qty'];
	  if($sval[csf('size_qty')]==$maximum_qty)
		{
		$detali_data_arr[$sval[csf('dtls_id')]][$sval[csf('size_id')]]['bundle_full']+=1;	
		}
		else if($sval[csf('size_qty')]<$size_qty)
		{
		$detali_data_arr[$sval[csf('dtls_id')]][$sval[csf('size_id')]]['bundle_short']+=1;	
		}	 
		 
	 }
	 
	 
	 
	//print_r($size_id_arr);
	//die;
   $col_span=count($size_id_arr);
   $td_width=450/$col_span;
  
  // echo $td_width;die;
   
   ?>
    <table border="1" cellpadding="1" cellspacing="1"   width="1080"class="rpt_table" rules="all">
          <tr height="30" >
          <td width="30">SL</td>
          <td width="60" align="center">Roll No </td>
          <td width="60" align="center">Roll Kgs </td>
           <?
          if($roll_maintain==1)
          {
          	?>
          	<td width="100" align="center">Barcode </td>
          	<?
          }

          ?>
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
		 $i=1; $tot_gmts=0;
		  foreach($plice_data_arr as $plice_id=>$plice_val)
			  {
 				$roll_sql=sql_select("select  b.id,a.id,b.roll_no,b.qnty,b.barcode_no,b.plies  from ppl_cut_lay_dtls a,pro_roll_details b  where a.id=b.dtls_id and a.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and (b.entry_form=138 or b.entry_form=127) and (b.is_extra_roll is null or b.is_extra_roll=0)  order by b.id");

				$extra_roll_sql=sql_select("select  b.id,a.id,b.roll_no,b.qnty,b.barcode_no,b.plies  from ppl_cut_lay_dtls a,pro_roll_details b  where a.id=b.dtls_id and a.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and (b.entry_form=138 or b.entry_form=127) and b.is_extra_roll=1 order by b.id");
				//and a.id=$plice_id 
 
				$tot_qnty=sql_select("select  sum(b.qnty) as qty from ppl_cut_lay_dtls a,pro_roll_details b  where a.id=b.dtls_id and a.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and (b.entry_form=138 or b.entry_form=127) and ( b.is_extra_roll is null or  b.is_extra_roll =0)  order by b.id");
				//and a.id=$plice_id

				$extra_tot_qnty=sql_select("select  sum(b.qnty) as qty from ppl_cut_lay_dtls a,pro_roll_details b  where a.id=b.dtls_id and a.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and (b.entry_form=138 or b.entry_form=127) and   b.is_extra_roll=1  order by b.id");

 				 
				
				?>
                 <tr height="20">
                     <td width="" style="<? if(count($roll_sql)<1){echo "border-top-style:hidden;";} ?>"><? if(count($roll_sql)>=1){  echo $i; }?></td>
                     <td width="" align="center" style="<? if(count($roll_sql)<1){echo "border-top-style:hidden;";} ?>"> <? echo $roll_sql[0][csf("roll_no")]; ?> </td>
                      <td width="" align="center" style="<? if(count($roll_sql)<1){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[0][csf("qnty")]; ?> </td>
                      <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center" style="<? if(count($roll_sql)<1){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[0][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>
                      <td width="" align="center" rowspan="4"style="vertical-align:middle"><div class="block_div"><? echo $color_arr[$plice_val['color']];  ?></div></td>
                      <td width="" align="left" rowspan="2"><? echo $plice_val['plies']." Plies";  ?></td>
                       <td width="">Size</td>
                       
                   <?
					  foreach($size_id_arr as $size_id=>$size_val)
						{
					 ?>
						   <td width="<? echo $td_width; ?>" align="center" ><? echo $size_arr[$detali_data_arr[$plice_id][$size_id]['size']]; ?>	</td>
						 
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
                      <td width="" style="<? if(count($roll_sql)<2){echo "border-top-style:hidden;";} ?>" ><? if(count($roll_sql)>=2){  echo $i+1;}  ?></td>
                      <td width="" align="center" style="<? if(count($roll_sql)<2){echo "border-top-style:hidden;";} ?>"> <? echo $roll_sql[1][csf("roll_no")]; ?> </td>
                      <td width="" align="center" style="<? if(count($roll_sql)<2){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[1][csf("qnty")]; ?> </td>
                      <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center" style="<? if(count($roll_sql)<2){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[1][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>
                     
                       <td width="">CAD Ratio</td>
               	 <?
				  foreach($size_id_arr as $size_id=>$size_val)
				    {
						$ratio=$sizeRatio[$plice_id][$size_id];
						$total_size_ratio+=$ratio;
						//$total_size_ratio+=$detali_data_arr[$plice_id][$size_id]['size_ratio'];
				 ?>
                       <td width="<? echo $td_width; ?>" align="center"><? echo $ratio; //$detali_data_arr[$plice_id][$size_id]['size_ratio']; ?></td>
                     
                 <?
				     }
                 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_size_ratio;  ?></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                    <!--   <td width="" align="center"></td>
                      <td width="" align="center"> </td> -->
                   </tr>
                     <tr height="20">
                      <td width="" style="<? if(count($roll_sql)<3){echo "border-top-style:hidden;";} ?>"><? if(count($roll_sql)>=3){ echo $i+2; } ?></td>
                      <td width="" align="center" style="<? if(count($roll_sql)<3){echo "border-top-style:hidden;";} ?>"> <? echo $roll_sql[2][csf("roll_no")]; ?> </td>
                      <td width="" align="center" style="<? if(count($roll_sql)<3){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[2][csf("qnty")]; ?> </td>

                       <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center" style="<? if(count($roll_sql)<3){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[2][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>
                       <td width="" align="left" rowspan="2"><?  echo $plice_val['bundle_qty']."/Bundle";  ?></td>
                       <td width=""> Gmts Qty.
</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
						 ?>
							   <td width="<? echo $td_width; ?>" align="center" ><? echo $detali_data_arr[$plice_id][$size_id]['marker_qty'];  ?>	</td>
							 
						 <?
							 }
							 $tot_gmts+=$total_gmt_qty[$plice_id]['gmt_qty'];
						 ?> 
                      <td width="" align="right" valign="bottom" ><? echo $total_gmt_qty[$plice_id]['gmt_qty'];  ?></td> 
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <!-- <td width="" align="center"></td>
                      <td width="" align="center"> </td> -->
                   </tr>
              </tr>
                     <tr height="20">
                     
                    <td width=""  align="left" style="<? if(count($roll_sql)<4){echo "border-top-style:hidden;";} ?>"> <?  if(count($roll_sql)>=4){ echo $i+3; } ?></td>
                     <td width="" align="center" style="<? if(count($roll_sql)<4){echo "border-top-style:hidden;";} ?>"> <? echo $roll_sql[3][csf("roll_no")]; ?> </td>
                      <td width="" align="center" style="<? if(count($roll_sql)<4){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[3][csf("qnty")]; ?> </td>
                       <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center" style="<? if(count($roll_sql)<4){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[3][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>
                       
                       
                       <td width="">Bundle Qty.</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
						 ?>
							   <td width="<? echo $td_width; ?>" align="center"  style="font-size:14px;">
							   <?  
							    $bdl_qty=$detali_data_arr[$plice_id][$size_id]['bundle_full']; 
							    $extra_bdl=$detali_data_arr[$plice_id][$size_id]['bundle_short'];
								if($extra_bdl!=0) $bdl_qty=$bdl_qty." Full &  $extra_bdl  Incomplete";
							    echo $bdl_qty;
							    ?>	
                               </td>
							 
						 <?
							 }
						 ?>  
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                     <!--  <td width="" align="center"> </td> -->
                   </tr>       
                
              <?
				$i=$i+4;  
			  }
		
		     /* if(count($roll_sql>4))
		      {
			     $j=4;
			    for($k=1; $k<count($roll_sql)-4; $k++)
				{
			  
			  ?>
	                  <tr >
	                       <td width="26"><? echo $i+$k; ?></td>
	                       <td width="49" align="center"> <? echo $roll_sql[$j][csf("roll_no")]; ?> </td>
	                      <td width="49" align="center"><? echo $roll_sql[$j][csf("qnty")]; ?> </td>
	                       <?
				          if($roll_maintain==1)
				          {
				          	?>
				          	<td width="" align="center"><? echo $roll_sql[$j][csf("barcode_no")]; ?> </td>
				          	<?
				          }
				          ?>
	                  </tr>
	                  
	                 <?
	                 $j++;
				}
			   }*/
			
			?>
    
         
      </table>
      <?
	    $table_height=30+($i+1)*20;
		//echo $table_height;die;
		$div_position=$table_height+420;
      
     
	}
  else
  {
      $sql_main_qry=sql_select("select a.id,a.color_id,a.plies,b.size_id,sum(b.size_ratio) as size_ratio,sum(b.marker_qty) as  marker_qty from ppl_cut_lay_size b,ppl_cut_lay_dtls a where a.id=b.dtls_id and a.mst_id=$mst_id and b.status_active=1 and b.size_ratio>0 and  b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  group by  a.id,a.color_id,a.plies,b.size_id order by a.id");

	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;

	 foreach($sql_main_qry as $main_val)
	 {
	    $size_id_arr[$main_val[csf('size_id')]]=$main_val[csf('size_id')];
		$plice_data_arr[$main_val[csf('id')]]['plies']=$main_val[csf('plies')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('size_id')]]['size']=$main_val[csf('size_id')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('size_id')]]['size_ratio']=$main_val[csf('size_ratio')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('size_id')]]['marker_qty']=$main_val[csf('marker_qty')];
		$total_gmt_qty[$main_val[csf('id')]]['gmt_qty']+=$main_val[csf('marker_qty')];
		$grand_total+=$main_val[csf('marker_qty')];
		$size_qty=return_field_value("size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =".$main_val[csf('id')]." ");
		$plice_data_arr[$main_val[csf('id')]]['bundle_qty']=$size_qty;
		$plice_data_arr[$main_val[csf('id')]]['color']=$main_val[csf('color_id')];
		$id=$main_val[csf('id')];

	 }
	//echo "<pre>";print_r($roll_sql);die;
   $col_span=count($size_id_arr);
   $td_width=450/$col_span;
  
  // echo $td_width;die;
   
   ?>
    <table border="1" cellpadding="1" cellspacing="1"   width="1080"class="rpt_table" rules="all">
          <tr height="30" >
          <td width="30">SL</td>
          <td width="60" align="center">Roll No </td>
          <td width="60" align="center">Roll Kgs </td>
          <?
          if($roll_maintain==1)
          {
          	?>
          	<td width="100" align="center">Barcode </td>
          	<?
          }

          ?>
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
		 $i=1;
		 // echo "<pre>";print_r($plice_data_arr);die;
 		  foreach($plice_data_arr as $plice_id=>$plice_val)
			  {
 				$roll_sql=sql_select("select  b.id,a.id,b.roll_no,b.qnty,b.barcode_no,b.plies  from ppl_cut_lay_dtls a,pro_roll_details b  where a.id=b.dtls_id and a.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and (b.entry_form=138 or b.entry_form=127)  and (b.is_extra_roll is null or b.is_extra_roll=0)  order by b.id");
 				//and a.id=$plice_id 

				$extra_roll_sql=sql_select("select  b.id,a.id,b.roll_no,b.qnty,b.barcode_no,b.plies  from ppl_cut_lay_dtls a,pro_roll_details b  where a.id=b.dtls_id and a.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and (b.entry_form=138 or b.entry_form=127) and b.is_extra_roll=1 order by b.id");
				//and a.id=$plice_id 

				$tot_qnty=sql_select("select  sum(b.qnty) as qty from ppl_cut_lay_dtls a,pro_roll_details b  where a.id=b.dtls_id and a.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and (b.entry_form=138 or b.entry_form=127) and ( b.is_extra_roll is null or  b.is_extra_roll =0)  order by b.id");
				//and a.id=$plice_id

				$extra_tot_qnty=sql_select("select  sum(b.qnty) as qty from ppl_cut_lay_dtls a,pro_roll_details b  where a.id=b.dtls_id and a.mst_id='$mst_id' and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and (b.entry_form=138 or b.entry_form=127) and   b.is_extra_roll=1  order by b.id");
				//and a.id=$plice_id
				 

				?>
                 <tr height="20">
                     <td width="" style="<? if(count($roll_sql)<1){echo "border-top-style:hidden;";} ?>"><? if(count($roll_sql)>=1){  echo $i; }?></td>
                     <td width="" align="center" style="<? if(count($roll_sql)<1){echo "border-top-style:hidden;";} ?>"> <? echo $roll_sql[0][csf("roll_no")]; ?> </td>
                      <td width="" align="center" style="<? if(count($roll_sql)<1){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[0][csf("qnty")]; ?> </td>
                      <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center" style="<? if(count($roll_sql)<1){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[0][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>
                      <td width="" align="center" rowspan="4"style="vertical-align:middle"><div class="block_div"><? echo $color_arr[$plice_val['color']];  ?></div></td>
                      <td width="" align="left" rowspan="2"><? echo $plice_val['plies']." Plies";  ?></td>
                       <td width="">Size</td>
                       
                   <?
					  foreach($size_id_arr as $size_id=>$size_val)
						{
					 ?>
						   <td width="<? echo $td_width; ?>" align="center" ><? echo $size_arr[$detali_data_arr[$plice_id][$size_id]['size']];  ?>	</td>
						 
					 <?
						 }
					 ?>
                      <td width="" align="right" valign="bottom" ></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
                     <tr height="20" >
                      <td width="" style="<? if(count($roll_sql)<2){echo "border-top-style:hidden;";} ?>" ><? if(count($roll_sql)>=2){  echo $i+1;}  ?></td>
                      <td width="" align="center" style="<? if(count($roll_sql)<2){echo "border-top-style:hidden;";} ?>"> <? echo $roll_sql[1][csf("roll_no")]; ?> </td>
                      <td width="" align="center" style="<? if(count($roll_sql)<2){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[1][csf("qnty")]; ?> </td>
                      <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center" style="<? if(count($roll_sql)<2){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[1][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>
                       <td width="">CAD Ratio</td>
               	 <?
				  foreach($size_id_arr as $size_id=>$size_val)
				    {
						$total_size_ratio+=$detali_data_arr[$plice_id][$size_id]['size_ratio'];
				 ?>
                       <td width="<? echo $td_width; ?>" align="center" ><? echo $detali_data_arr[$plice_id][$size_id]['size_ratio'];  ?>	</td>
                     
                 <?
				     }
                 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_size_ratio;  ?></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                      <!-- <td width="" align="center"></td>
                      <td width="" align="center"> </td> -->
                   </tr>
                     <tr height="20">
                      <td width="" style="<? if(count($roll_sql)<3){echo "border-top-style:hidden;";} ?>"><? if(count($roll_sql)>=3){ echo $i+2; } ?></td>
                      <td width="" align="center" style="<? if(count($roll_sql)<3){echo "border-top-style:hidden;";} ?>"> <? echo $roll_sql[2][csf("roll_no")]; ?> </td>
                      <td width="" align="center" style="<? if(count($roll_sql)<3){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[2][csf("qnty")]; ?> </td>

                       <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center" style="<? if(count($roll_sql)<3){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[2][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>

                       <td width="" align="left" rowspan="2"><?  echo $plice_val['bundle_qty']."/Bundle";  ?></td>
                       <td width=""> Gmts Qty.
</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
						 ?>
							   <td width="<? echo $td_width; ?>" align="center" ><? echo $detali_data_arr[$plice_id][$size_id]['marker_qty'];  ?>	</td>
							 
						 <?
							 }
							  
						 ?> 
                      <td width="" align="right" valign="bottom" ><? echo $total_gmt_qty[$plice_id]['gmt_qty']; $tot_gmts+=$total_gmt_qty[$plice_id]['gmt_qty'];  ?></td> 
					  <td width="" align="center"></td>
                      <td width=""></td>
                     <!--  <td width="" align="center"></td>
                       <td width="" align="center">  </td>   -->
                   </tr>
              </tr>
                     <tr height="20">
                     
                      <td width=""  align="left" style="<? if(count($roll_sql)<4){echo "border-top-style:hidden;";} ?>"> <?  if(count($roll_sql)>=4){ echo $i+3; } ?></td>
                     <td width="" align="center" style="<? if(count($roll_sql)<4){echo "border-top-style:hidden;";} ?>"> <? echo $roll_sql[3][csf("roll_no")]; ?> </td>
                      <td width="" align="center" style="<? if(count($roll_sql)<4){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[3][csf("qnty")]; ?> </td>
                       <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center" style="<? if(count($roll_sql)<4){echo "border-top-style:hidden;";} ?>"><? echo $roll_sql[3][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>
                       
                       <td width="">Bundle Qty.</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
						 ?>
							   <td width="<? echo $td_width; ?>" align="center"  style="font-size:14px;">
							   <?  
							   $bdl_qty=floor($detali_data_arr[$plice_id][$size_id]['marker_qty']/$plice_val['bundle_qty']); 
							    $extra_bdl=($detali_data_arr[$plice_id][$size_id]['marker_qty']%$plice_val['bundle_qty']);
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
                      <!-- <td width="" align="center"> </td> -->
                   </tr>       
                
              <?
				$i=$i+4;  
			  }
		
		     ?> 
    
         
      </table>
      <?
	    $table_height=30+($i+1)*20;
		//echo $table_height;die;
		$div_position=$table_height+420;
      
  }
  
		$color_size_qty_arr=array();
		$color_size_sql=sql_select ("SELECT po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown 
		where is_deleted=0 and status_active=1 and  po_break_down_id in (".$order_id.") group by po_break_down_id,item_number_id,size_number_id,color_number_id");
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

	//print_r($con_per_dzn);
	//echo array_sum($con_per_dzn);
//Changed By saeed vai used cad_marker_cons instead of cons_qnty

	$con_qnty=($con_qnty/$costing_per_qty)*12;
	$net_cons=($lay_fabric_wght/$tot_gmts)*12;
	$loss_gain='&nbsp;'; $gain='&nbsp;'; $loss='&nbsp;';
	$cons_balance=$cad_marker_cons-$net_cons;
	if($cad_marker_cons>$net_cons) 
	{
		$loss_gain='Gain';
		$gain=number_format($cons_balance,4);
	}
	else if($cad_marker_cons<$net_cons) 
	{
		$loss_gain='Loss';
		$loss=number_format(abs($cons_balance),4);
	}
	
	?>
    <div style=" width:233px; position:absolute; left:0px   ">
 
          <table border="1" cellpadding="1" cellspacing="1"   width="233"class="rpt_table" rules="all" >
          <? if(count($roll_sql)>4)
          {?>
               <tr style=" border-top:hidden" >
                       <td width="26"><? echo $i+$k; ?></td>
                       <td width="49" align="center"> <? echo $roll_sql[4][csf("roll_no")]; ?> </td>
                      <td width="49" align="center"><? echo $roll_sql[4][csf("qnty")]; ?> </td>
                       <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center"><? echo $roll_sql[4][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>
                  </tr>
          <? }
           
          $j=5;
		    for($k=1; $k<count($roll_sql)-4; $k++)
			{
		  
		  ?>
                  <tr >
                       <td width=""><? echo $i+$k; ?></td>
                       <td width="" align="center"> <? echo $roll_sql[$j][csf("roll_no")]; ?> </td>
                      <td width="" align="center"><? echo $roll_sql[$j][csf("qnty")]; ?> </td>
                       <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center"><? echo $roll_sql[$j][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>
                  </tr>
                  
                 <?
                 $j++;
			}
			
			?>
			<tr>
				<td colspan="4">Total Wgt: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <? echo $tot_qnty[0][csf("qty")]; ?></td>
				 
			</tr>
			<tr>
				<td colspan="4" align="center"> <strong>Extra Roll Info</strong></td>
			</tr>
			<?
			for($p=0;$p<count($extra_roll_sql);$p++)
			{


			?>

			<tr >
                       <td width="30"><? echo $p+1; ?></td>
                       <td width="50" align="center"> <? echo $extra_roll_sql[$p][csf("roll_no")]; ?> </td>
                      <td width="50" align="center"><? echo $extra_roll_sql[$p][csf("qnty")]; ?> </td>
                       <?
			          if($roll_maintain==1)
			          {
			          	?>
			          	<td width="" align="center"><? echo $extra_roll_sql[$p][csf("barcode_no")]; ?> </td>
			          	<?
			          }
			          ?>
            </tr>
            <?
        	}
        	?>

        	<tr>
				<td colspan="4">Extra Wgt:   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <? echo $extra_tot_qnty[0][csf("qty")]; ?></td>
				 
			</tr>
            </table>
       </div>
       
       <div style=" width:160px; position:absolute; left:260px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="160"class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="80">Booking<br>Consumption <br>Per Dzn</td>
                       <td width="80" align="center" ><? echo number_format($con_qnty,4); ?></td>
                  </tr>
            </table>
       </div>
       
        <div style=" width:160px; position:absolute; left:430px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="160"class="rpt_table" rules="all">
                  <tr  height="30" >
                       <td width="80" >CAD Marker<br>Consumption <br>Per Dzn</td>
                       <td width="80" align="center" ><? echo $cad_marker_cons; ?></td>
                  </tr>
            </table>
       </div>
         <div style=" width:180px; position:absolute; left:600px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="180"class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="40" rowspan="2">Net<br>KGS <br>Used</td>
                       <td width="70" align="center" >KGs</td>
                       <td width="70" align="center" >G.Qty</td>
                  </tr>
                   <tr  height="30">
                       <td width="70" align="center" ><? echo $lay_fabric_wght; ?></td>
                       <td width="70" align="center" ><? echo $tot_gmts; ?></td>

                  </tr>
            </table>
       </div>
       
        <div style=" width:300px; position:absolute; right:181px ;left:800px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="300"class="rpt_table" rules="all">
                  <tr height="20">
                       <td width="80" rowspan="2">Net<br>Consumption <br>Per Dzn</td>
                       <td width="70" align="center">Net</td>
                       <td width="70" align="center">Loss</td>
                       <td width="" align="center">Gain</td>
                  </tr>
                   <tr height="20" >
                       <td width="70" align="center" style="word-wrap:break-word; word-break: break-all;" ><? echo number_format($net_cons,4); ?></td>
                       <td width="70" align="center" style="word-wrap:break-word; word-break: break-all;" ><? echo $loss; ?></td>
                       <td width="70" align="center" style="word-wrap:break-word; word-break: break-all;" ><? echo $gain; ?></td>
                  </tr>
            </table>
       </div>
         <div style=" width:180px; position:absolute; right:0;left: 1110px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="180"class="rpt_table" rules="all">
                  <tr  height="40">
                       <td width="100">Lay<br>Loss/Gain</td>
                       <td width="80" align="center" ><? echo $loss_gain; ?></td>
                  </tr>
            </table>
       </div>
       <br/> <br/> <br/> <br/><br/><br/> 
        <div style=" width:900px; margin-left:260px;  ">
          <table border="1" cellpadding="1" cellspacing="1"   width="900" class="rpt_table" rules="all">
                  <tr>
                       <td width="100">Remarks</td>
                       <td width="450" colspan="6" align="left" ><? echo $remarks; ?></td>
                       <td colspan="4" width="300" align="center"> Wastage Qnty. (Kg) : <? echo $wastage_qnty; ?></td>
                  </tr>
            </table>
       </div>

       <div style=" width:900px; margin-left:260px; margin-top: 15px; ">
          <table border="1" cellpadding="1" cellspacing="1"   width="900" class="rpt_table" rules="all">
                  <tr>
                       <td width="300" colspan="2" align="center">Po Consumption</td>
                       <td width="300" colspan="2" align="center"> CAD marker Consumpt</td>
                      <td width="300" colspan="2" align="center">Actual Consumption</td>
                  </tr>
                  <tr>
                  	<td align="center">Kg/Pcs</td>
                  	<td align="center">Kg/Dzn</td>
                  	<td align="center">Kg/Pcs</td>
                  	<td align="center">Kg/Dzn</td>
                  	<td align="center">Kg/Pcs</td>
                  	<td align="center">Kg/Dzn</td>
                  </tr>


                   <tr>
                  	<td align="center"><? $con_qnty_pcs=$con_qnty/12;echo number_format($con_qnty_pcs,4); ?></td>
                  	<td align="center"><? echo number_format($con_qnty,4); ?></td>
                  	<td align="center"><? echo number_format($cad_marker_cons/12,4); ?></td>
                  	<td align="center"><? echo $cad_marker_cons; ?></td>
                  	<td align="center"><? echo number_format(($lay_fabric_wght/$tot_gmts),4); ?></td>
                  	<td align="center"><? echo number_format(($lay_fabric_wght/$tot_gmts)*12,4); ?></td>
                  </tr>
                  <tr>
                  <?
                  $sqls=sql_select("select po_break_down_id,dia_width from wo_pre_cos_fab_co_avg_con_dtls where   po_break_down_id='$order_id' group by po_break_down_id,dia_width ");
                  $res="";
                  foreach($sqls as $vals)
                  {
                  	if($res=="")
                  	 {
                  		$res=$vals[csf("dia_width")];
                  	 }
                  	else 
                  	{ 
                  		$res.=','.$vals[csf("dia_width")];
                  	}

                  }

 
                  ?>

                  	<td colspan="2" align="center">Width/Dia : <? echo $res; ?> </td>
                  	<td colspan="2" align="center">Width/Dia : <? echo  $marker_with;?></td>
                  	<td colspan="2" align="center">Width/Dia : <? echo  $fabric_with;?></td>
                  </tr>

            </table>
       </div>

       <div style=" width:900px; margin-left:260px; margin-top: 15px; ">
          <table border="1" cellpadding="1" cellspacing="1"   width="900" class="rpt_table" rules="all">
                  <tr>
                  	<td colspan="6" align="center"><strong>Comparison</strong></td>
                  </tr>
                  <tr>
                       <td width="300" colspan="2" align="center"></td>
                       <td width="300" colspan="2" align="center"> Pcs/Per(Kg)</td>
                      <td width="300" colspan="2" align="center"> Dzn/Per (kg)</td>
                  </tr>
                  <tr>
                  	<td align="center" colspan="2">Diff btn Po & Actual(kg)</td>
                   	<td align="center" colspan="2"><? echo number_format($con_qnty_pcs -($lay_fabric_wght/$tot_gmts),4); ?></td>
                   	<td align="center" colspan="2"><? echo  number_format($con_qnty- ($lay_fabric_wght/$tot_gmts)*12,4);?></td>
                  	 

                 
                  
                  </tr>

                  <tr>
                  	<td align="center" colspan="2">Diff btn CAD & Actual(kg)</td>
                   	<td align="center" colspan="2"><? echo number_format($cad_marker_cons/12,4) -number_format(($lay_fabric_wght/$tot_gmts),4); ?></td>
                   	<td align="center" colspan="2"><? echo $cad_marker_cons - number_format(($lay_fabric_wght/$tot_gmts)*12,4);?></td>
                  	 
                  </tr>
                  <tr>
                  	<td colspan="6">&nbsp;</td>
                  </tr>

                   <tr>
                  	<td align="center" colspan="2">Total Fabric (kg)</td>
                   	<td align="center" colspan="4"><? echo number_format($lay_fabric_wght,4); ?></td>
                   	 
                  	 
                  </tr>

                  <tr>
                  	<td align="center" colspan="2">Gmt Qnty</td>
                   	<td align="center" colspan="4"> <? echo number_format($tot_gmts,4); ?> </td>
                    
                  	 
                  </tr>

                  <tr>
                  	<td align="center" colspan="2">Wastage Qnty (Kg):</td>
                   	<td align="center" colspan="4"><? echo $wastage_qnty;?></td>
                    
                  	 
                  </tr>

                    

            </table>
       </div>



      <div style=" width:1100px;  left:0px; margin-top:20px; ">
         <?
           echo signature_table(58, $company_id, "1100px");
         ?>
      </div>
 </div>
    <?
   exit();
} 

if($action=="size_wise_repeat_cut_no")
{
	$size_wise_repeat_cut_no=return_field_value("gmt_num_rep_sty","variable_order_tracking","company_name='$data' and variable_list=28 and is_deleted=0 and status_active=1"); 
	if($size_wise_repeat_cut_no==1) $size_wise_repeat_cut_no=$size_wise_repeat_cut_no; else $size_wise_repeat_cut_no=0;
	echo "document.getElementById('size_wise_repeat_cut_no').value = '".$size_wise_repeat_cut_no."';\n";
	exit();	
}


if($action=="roll_maintained")
{
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and item_category_id=51 and is_deleted=0 and status_active=1");
	if($roll_maintained==1) $roll_maintained=$roll_maintained; else $roll_maintained=0;
	
	echo "document.getElementById('roll_maintained').value 					= '".$roll_maintained."';\n";
	
	$bundle_bo_creation=return_field_value("smv_source","variable_settings_production","company_name ='$data' and variable_list=37 and is_deleted=0 and status_active=1");
	if($bundle_bo_creation<1) $bundle_bo_creation=1;
	
	echo "document.getElementById('bundle_bo_creation').value 					= '".$bundle_bo_creation."';\n";
	
	$batch_controll=return_field_value("is_control","variable_settings_production","company_name='$data' and variable_list=38 and status_active=1 and is_deleted=0");
	echo "document.getElementById('txt_batch_no_mandatory').value 					= '".$batch_controll."';\n";
	exit();	
}

if($action=="check_batch_no")
{
	$data=explode("**",$data);
	$sql="select c.barcode_no from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in (64,37,72) and a.batch_no='".trim($data[0])."' and b.po_id='".trim($data[1])."' and a.is_deleted=0 and   a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 ";
	
	$data_array=sql_select($sql);
	$barcode_arr=array();
	if(count($data_array)>0)
	{
		foreach($data_array as $val)
		{
			$barcode_arr[$val[csf('barcode_no')]]=$val[csf('barcode_no')];
		}
		//$barcode_arr=json_encode($barcode_arr);
		//echo $barcode_arr;
		echo trim(implode(",",$barcode_arr));	
		//print_r($barcode_arr);die;
	}
	else
	{
		echo "0";
	}
	exit();	
}

if($action=="roll_popup")
{
  	echo load_html_head_contents("Plies Info Roll Wise","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	
	
	
?>
	<script>
		var roll_maintained=<? echo $roll_maintained; ?>;
		var rollData='<? echo $rollData; ?>';
		var ExtraRollData='<? echo $ExtraRollData; ?>';
		var scanned_barcode=new Array(); var roll_details_array=new Array(); var barcode_array=new Array();
		<?
			$scanned_barcode_array=array();
			$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=138 and status_active=1 and is_deleted=0");
			foreach($scanned_barcode_data as $row)
			{
				$scanned_barcode_array[]=$row[csf('barcode_no')];
			}
			$jsscanned_barcode_array= json_encode($scanned_barcode_array);
			echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";

			$data_array=sql_select("select c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description from  pro_roll_details c, pro_batch_create_dtls b where c.dtls_id=b.id and  c.entry_form in (64,37) and c.po_breakdown_id=$order_no and c.status_active=1 and c.is_deleted=0"); //and b.color_id=$color
 			$roll_details_array=array(); $barcode_array=array(); 
			foreach($data_array as $row)
			{
				$item_description_arr=explode(",",$row[csf('item_description')]);
				$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
				$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
				$roll_details_array[$row[csf("barcode_no")]]['qnty']=$row[csf("qnty")];
				$roll_details_array[$row[csf("barcode_no")]]['gsm']=$item_description_arr[2];
				$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
			}
			
			$data_array=sql_select("select c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.gsm from pro_grey_batch_dtls b, pro_roll_details c where b.id=c.dtls_id and c.entry_form=72 and c.po_breakdown_id=$order_no  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"); //and b.color_id=$color
 		
			foreach($data_array as $row)
			{
				$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
				$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
				$roll_details_array[$row[csf("barcode_no")]]['qnty']=$row[csf("qnty")];
				$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
				$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
			}
			
			$jsroll_details_array= json_encode($roll_details_array);
			echo "var roll_details_array = ". $jsroll_details_array . ";\n";
			
			$jsbarcode_array= json_encode($barcode_array);
			echo "var barcode_array = ". $jsbarcode_array . ";\n";
		?>
		
	 new function ($) {
			$.fn.getCursorPosition = function () {
				var pos = 0;
				var el = $(this).get(0);
				// IE Support
				if (document.selection) {
					el.focus();
					var Sel = document.selection.createRange();
					var SelLength = document.selection.createRange().text.length;
					Sel.moveStart('character', -el.value.length);
					pos = Sel.text.length - SelLength;
				}
				// Firefox support
				else if (el.selectionStart || el.selectionStart == '0')
					pos = el.selectionStart;
				return pos;
			}
		} (jQuery);

		function navigate_arrow_key()
		{
			$('.text_boxes_numeric').keyup(function(e){
				if( e.which==39 )
				{
					//if( $(this).getCursorPosition() == $(this).val().length ) 
					if($(this).closest('td').index()*1==1)
					{
						$(this).closest('tr').find('td:eq('+ ($(this).closest('td').index()*1+2) +')').find('.text_boxes,.text_boxes_numeric').focus();
					}
					else
						$(this).closest('td').next().find('.text_boxes_numeric').focus();
				}
				else if( e.which==37 )
				{
					//if( $(this).getCursorPosition() == 0 ) 
					if($(this).closest('td').index()*1==3)
					{
						$(this).closest('tr').find('td:eq('+ ($(this).closest('td').index()*1-2) +')').find('.text_boxes,.text_boxes_numeric').focus();
					}
					else
						$(this).closest('td').prev().find('.text_boxes_numeric').focus();
				}
				else if( e.which==40 )
				{
					$(this).closest('tr').next().find('td:eq('+ $(this).closest('td').index() +')').find('.text_boxes,.text_boxes_numeric').focus();
				}
				else if( e.which==38 )
				{
					$(this).closest('tr').prev().find('td:eq('+$(this).closest('td').index()+')').find('.text_boxes,.text_boxes_numeric').focus();
				}
			});
		}

		$(document).ready(function(e) {
            if(roll_maintained==1)
			{
				$('#barcode_div').show();
				$('#batch_div').show();
				
			}
			else
			{
				$('#barcode_div').hide();
				$('#batch_div').hide();
			}
			
			if(rollData!="")  
			{
				var data=rollData.split("__");
				for(var k=0; k<data.length; k++)
				{
					var datas=data[k].split("=");
					var barcode_no=datas[0];
					var rollNo=datas[1];
					var rollId=datas[2];
					var rollWgt=datas[3];
					var plies=datas[4];
					var gsm=datas[5];

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
					$("#gsm_"+row_num).val(gsm);
					
					if( jQuery.inArray( barcode_no, scanned_barcode )>-1) 
					{ 
						scanned_barcode.push(barcode_no); 
					}
				}
			}
			else
			{
				if(roll_maintained!=1)
				{
					for(var k=1; k<15; k++)
					{
						add_break_down_tr(k);
						$("#rollNo_"+k).val(k);
					}
					$("#rollNo_15").val(15);
				}
			}


			if(ExtraRollData!="")  
			{
				var extradata=ExtraRollData.split("__");
				var p=1;
 				for(var k=0; k<extradata.length; k++)
				{
  					extra_add_break_down_tr(p);
  					var exdatas=extradata[k].split("=");
 					var rollNo=exdatas[1];
					var rollId=exdatas[2];
					var rollWgt=exdatas[3];
					var plies=exdatas[4];
 					$("#extraRollNo_"+p).val(rollNo);
					$("#extraRollId_"+p).val(rollId);
					$("#extraRollWgt_"+p).val(rollWgt);
					$("#extraPlies_"+p).val(plies);
					p++;
 					
					 
				}
				$('#extra_tbl_list_search tbody tr:last').remove();
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
			
			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			
			$('#txt_tot_row').val(row_num);
			set_all_onclick();
			navigate_arrow_key();
		}
		
		function fn_deleteRow(rowNo) 
		{ 
			var numRow = $('#tbl_list_search tbody tr').length; 
			if(numRow!=1)
			{
				
				var bar_code=$('#barcodeNo_'+rowNo).val();
				var index = scanned_barcode.indexOf(bar_code);
				scanned_barcode.splice(index,1);
				$("#tr_"+rowNo).remove();
			}
		}

		function extra_add_break_down_tr( i )
		{ 
			var row_num=$('#extra_tbl_list_search tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{ 
				i++;
		       var k=i-1;
				$("#extra_tbl_list_search tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({ 
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  
				  'value': function(_, value) { return value }              
				});
				}).end().appendTo("#extra_tbl_list_search");
				$("#txtSL_"+i).val(i); 
				$("#extraRollNo_"+i).val('');
				$("#extraRollId_"+i).val('');
				$("#extraRollWgt_"+i).val('');
				$("#extraPlies_"+i).val('');
				
 			$('#extraincrease_'+i).removeAttr("value").attr("value","+");
			$('#extradecrease_'+i).removeAttr("value").attr("value","-");
			$('#extraincrease_'+i).removeAttr("onclick").attr("onclick","extra_add_break_down_tr("+i+");");
			$('#extradecrease_'+i).removeAttr("onclick").attr("onclick","extra_fn_deleteRow("+i+");");
				//set_all_onclick();
  			}
			
		
			 
		}
		
		function extra_fn_deleteRow(rowNo) 
		{ 
			var numRow = $('#extra_tbl_list_search tbody tr').length; 
			if(numRow!=1)
			{
				$('#extra_tbl_list_search tbody tr:last').remove();
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
		
		$('#txt_batch_no').live('keydown', function(e) {
			if (e.keyCode === 13) 
			{
				e.preventDefault();
				var batch_no=$('#txt_batch_no').val();
				var order_id=<?php echo $order_no; ?>;
				var response_data=return_global_ajax_value( batch_no+"**"+order_id, 'check_batch_no', '', 'cut_and_lay_entry_controller');
				//alert(response_data);return;
				var row_num=$('#txt_tot_row').val();
				if(response_data!=0)
				{
					response_data_arr=trim(response_data).split(",");
					//alert(response_data_arr.length);return;
					for (var i = 0; i < response_data_arr.length; i++) 
					{
						var bar_code=response_data_arr[i];
						//alert(bar_code);return;
						if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
						{ 
							alert('Sorry! Barcode Already Scanned.'); 
							//$('#txt_bar_code_num').val('');
							return; 
						}
						
						if(barcode_array[bar_code])
						{
							if($('#barcodeNo_'+row_num).val()!="")
							{
								add_break_down_tr(row_num);
								row_num++;
							}
							load_data(row_num, bar_code);
						}
					}
				
				
				
				//alert(response_data)
					//response_data_arr=response_data.split(",");
					//for (var i = 0; i < response_data_arr.length; i++) 
					//{
					
						//var bar_code=response_data_arr[i];
					//alert(bar_code)
						/*if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
						{ 
							alert('Sorry! Barcode Already Scanned.'); 
							$('#txt_bar_code_num').val('');
							return; 
						}*/
					//}
				
				}
				/*if(!barcode_array[bar_code])
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
				load_data(row_num, bar_code);*/
			}
		});
		
		
		
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
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','cut_and_lay_entry_controller.php?order_no='+<? echo $order_no; ?>+'&color='+<? echo $color; ?>+'&action=barcode_popup','Barcode Popup', 'width=480px,height=300px,center=1,resize=1,scrolling=0','../../')
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
			$("#gsm_"+row_num).val(roll_details_array[bar_code]['gsm']);
			scanned_barcode.push(bar_code);
		}
		
		function fnc_close()
		{
			var save_string='';	
			var tot_plies='';
			var tot_weight='';
			$("#tbl_list_search").find('tr').each(function()
			{
 				var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
				var rollNo=$(this).find('input[name="rollNo[]"]').val();
				var rollId=$(this).find('input[name="rollId[]"]').val();
				var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
				var plies=$(this).find('input[name="plies[]"]').val();
				var gsm=$(this).find('input[name="gsm[]"]').val();
				
				if(plies*1>0)
				{
					tot_plies=tot_plies*1+plies*1;
					tot_weight=tot_weight*1 + rollWgt*1;
					if(barcodeNo=="") barcodeNo=0;
					if(save_string=="")
					{
						save_string=barcodeNo+"="+rollNo+"="+rollId+"="+rollWgt+"="+plies+"="+gsm;
					}
					else
					{
						save_string+="__"+barcodeNo+"="+rollNo+"="+rollId+"="+rollWgt+"="+plies+"="+gsm;
					}
				}
			});
			var extra_wgt="";
			var extra_save_string="";
			var p=1;
  			$("#extra_tbl_list_search tbody").find('tr').each(function()
			{ 
  				 var barcode=0;
				var extrarollNo=$("#extraRollNo_"+p).val();
				var extrarollId=$("#extraRollId_"+p).val();
				var extrarollWgt=$("#extraRollWgt_"+p).val();
				var extraplies=$("#extraPlies_"+p).val();
 			 	p++;
 				
				if(extraplies*1>0)
				{
					tot_plies=tot_plies*1+extraplies*1;
					tot_weight=tot_weight*1 + extrarollWgt*1;
					extra_wgt=extra_wgt*1 + extrarollWgt*1;
					 
					if(extra_save_string=="")
					{
						extra_save_string=barcode+"="+extrarollNo+"="+extrarollId+"="+extrarollWgt+"="+extraplies;
					}
					else
					{
						extra_save_string+="__"+barcode+"="+extrarollNo+"="+extrarollId+"="+extrarollWgt+"="+extraplies;
					}
				}
			});
			 //alert(extra_save_string);
			$('#hide_data').val( save_string );
			$('#hide_extra_roll_data').val( extra_save_string );
			$('#hide_plies').val( tot_plies );
			$('#hide_sum_roll_weight').val( tot_weight );
			$('#hide_extra_wgt').val( extra_wgt );
			
			parent.emailwindow.hide();
		}
		
		
		function openmypage_batch()
		{ 
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','cut_and_lay_entry_controller.php?order_no='+<? echo $order_no; ?>+'&color='+<? echo $color; ?>+'&action=batch_popup','Batch Barcode Popup', 'width=580px,height=300px,center=1,resize=1,scrolling=0','../../')
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
		
		
		
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
	<fieldset style="width:590px">  
    	<div style="margin-bottom:5px; display:none; float:left" id="barcode_div">
            <strong>Barcode Number</strong>&nbsp;&nbsp;
            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
        </div>
		<div style="margin-bottom:5px; display:none; float:left" id="batch_div">
            <strong>Batch No</strong>&nbsp;&nbsp;
            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:170px;" onDblClick="openmypage_batch()" placeholder="Browse/Write/scan"/>
        </div>
        <table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
            <thead>
                <th>Roll Number</th>
                <th>Roll Weight</th>
				<? 
					$disbled=""; 
					if($roll_maintained==1) 
					{
						echo "<th>Barcode No</th>"; 
						$disbled="disabled";
					} 
				?>
                <th>Plies</th>
                 <? 
					$disbled=""; 
					if($roll_maintained==1) 
					{
						echo "<th>GSM</th>"; 
						$disbled="disabled";
					} 
				?>
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
                    <? if($roll_maintained==1) 
                    { 
                    ?>
                        <td><input type="text" id="barcodeNo_1" name="barcodeNo[]" class="text_boxes_numeric" value="" style="width:100px" disabled/></td>
                    <?
                    } 
					else
					{
					?>
                        <td style="display:none"><input type="text" id="barcodeNo_1" name="barcodeNo[]" class="text_boxes_numeric" value="" style="width:100px"/></td>
                    <?
					}
                    ?>
                    <td>
                        <input type="text" id="plies_1" name="plies[]" class="text_boxes_numeric" value="" style="width:100px"/> 
                    </td>
                    <?
                    if($roll_maintained==1)
                    {
                    	?>
                    	<td>
                        <input type="text" id="gsm_1" name="gsm[]" class="text_boxes_numeric" value="" style="width:100px" disabled="" /> 
                      </td>

                    	<?
                     }
                    ?>
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

        <table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="extra_tbl_list_search"><br>

        <caption><strong>Extra Roll & Plies</strong></caption>
            <thead>
            	<th>SL</th>
                <th>Roll Number</th>
                <th>Roll Weight</th>
                <th>Plies</th>
                <th></th>
            </thead>
            <tbody>
				 
                <tr id="extratr_1" class="general"> 
                <td> <input type="text" id="txtSL_1" name="txtSL[]" class="text_boxes_numeric" style="width:30px" readonly value="1" /> </td>
                    <td>
                        <input type="text" id="extraRollNo_1" name="extraRollNo[]" class="text_boxes_numeric" style="width:110px" />
                         <input type="hidden" id="extraRollId_1" name="extraRollId[]" value=""/>
                    </td>
                    <td>
                        <input type="text" id="extraRollWgt_1" name="extraRollWgt[]" class="text_boxes_numeric" value="" style="width:100px" />
                    </td>
                     
                    <td>
                        <input type="text" id="extraPlies_1" name="extraPlies[]" class="text_boxes_numeric" value="" style="width:100px"/> 
                    </td>
                    
                  
                    <td width="70">
                    	
							 <input type="button" id="extraincrease_1" name="extraincrease[]" style="width:30px" class="formbutton" value="+" onClick="extra_add_break_down_tr(1)" />	
						
                        <input type="button" id="extradecrease_1" name="extradecrease[]" style="width:30px" class="formbutton" value="-" onClick="extra_fn_deleteRow(1);" />
                    </td>
                </tr>
            </tbody>
        </table>


        <div align="center" style="margin-top:5px">
            <input type="button" class="formbutton" onClick="fnc_close()" value="Close" style="width:100px"/>
            <input type="hidden" id="hide_plies" />
             <input type="hidden" id="hide_sum_roll_weight" />
            <input type="hidden" id="hide_data" />
            <input type="hidden" id="hide_extra_roll_data" />
            <input type="hidden" id="hide_extra_wgt" />

            
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
                    <th width="55">Roll Qty.</th>
                     <th>GSM</th>
                </thead>
            </table>
            <div style="width:420px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" id="tbl_list_search">  
                    <? 
					$scanned_barcode_arr=array();
					$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form=138 and status_active=1 and is_deleted=0");
					foreach ($barcodeData as $row)
					{
						$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
					}
					///echo "select c.barcode_no, c.roll_id, c.roll_no, c.qnty from pro_grey_batch_dtls b, pro_roll_details c where b.id=c.dtls_id and c.entry_form=72 and c.po_breakdown_id=$order_no and b.color_id=$color and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			
                    $data_array=sql_select("select c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.gsm from pro_grey_batch_dtls b, pro_roll_details c where b.id=c.dtls_id and c.entry_form=72 and c.po_breakdown_id=$order_no  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"); // change by subbir and b.color_id=$color
					$i=1;
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
                                <td align="right" width="55"><? echo number_format($row[csf('qnty')],2); ?></td>
                                <td><? echo $row[csf('gsm')]; ?></td>
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
}

if($action=="batch_popup")
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
<div align="center" style="width:550px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:540px; margin-left:2px">
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="520">
                <thead>
                    <th width="50">SL</th>
					<th width="100">Batch No</th>
                    <th width="130">Barcode No</th>
                    <th width="100">Roll No</th>
                    <th width="55">Roll Qty.</th>
                    <th>GSM</th>
                </thead>
            </table>
            <div style="width:520px; max-height:200px; overflow-y:scroll" id="list_container" align="left"> 
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="500" id="tbl_list_search">  
                    <? 
					$scanned_barcode_arr=array();
					$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form=138 and status_active=1 and is_deleted=0");
					foreach ($barcodeData as $row)
					{
						$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
					}
					//echo "select a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.gsm from pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in (64,37) and c.po_breakdown_id=$order_no  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.gsm";
			
                    $data_array=sql_select("select a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in (64,37) and c.po_breakdown_id=$order_no  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description"); // change by subbir and b.color_id=$color
					$i=1;
					foreach($data_array as $row)
                    {  
                        if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
						{
							$item_description_arr=explode(",",$row[csf('item_description')]);
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                                <td width="50">
                                    <? echo $i; ?>
                                     <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                                </td>
								<td width="100"><? echo $row[csf('batch_no')]; ?></td>
                                <td width="130"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                                <td width="100"><? echo $row[csf('roll_no')]; ?></td>
                                <td align="right" width="55"><? echo number_format($row[csf('qnty')],2); ?></td>
                                <td><? echo $item_description_arr[2]; ?></td>
                            </tr>
						<? 
						$i++;
						}
                    } 
                    ?>
                </table>
            </div>
            <table width="520">
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

if($action=="print_qrcode_operation")
{	
	//echo "1000".$data;die;
	$data=explode("***",$data);
	$detls_id=$data[3];
	$garments_item_name=$garments_item[$data[4]];
	$color_library=return_library_array( "select id, color_name from lib_color where id=$data[5]", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	//echo "select a.id, a.size_id, a.bundle_no, a.barcode_no, a.order_id, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_no, b.bundle_sequence, b.color_id from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence, a.id";
	//$color_sizeID_arr=sql_select("select a.id, a.size_id, a.bundle_no, a.barcode_no, a.order_id, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_no, b.bundle_sequence, b.color_id from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence, a.id");
	
	$color_sizeID_arr=sql_select("select a.id, a.size_id, a.bundle_no, a.barcode_no, a.order_id, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_no from ppl_cut_lay_bundle a where a.id in ($data[0]) order by  a.id");
	
	foreach($color_sizeID_arr as $val_qty)
	{
		$total_cut_qty+=$val_qty[csf('size_qty')];
		$total_size_qty_arr[$val_qty[csf('size_id')]]+=$val_qty[csf('size_qty')];
	}
	$bundle_array=array();
	$sql_name=sql_select("select b.buyer_name, b.style_ref_no, b.product_dept, a.po_number, a.id from wo_po_details_master b, wo_po_break_down a where a.job_no_mst=b.job_no and a.job_no_mst='".$data[1]."'");

	foreach($sql_name as $value)
	{
		$product_dept_name 						=$value[csf('product_dept')];
		$style_name 							=substr($value[csf('style_ref_no')],0,26);
		$buyer_name 							=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]] 		=$value[csf('po_number')];
	}
	$buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");
	//return_library_array( "select id,short_name from lib_buyer where id=$buyer_name ", "id", "short_name");
	//echo "select entry_date, table_no, cut_num_prefix_no, batch_id, company_id, cutting_no from ppl_cut_lay_mst where id=$data[2]";
	$sql_cut_name=sql_select("select entry_date, table_no, cut_num_prefix_no, batch_id, company_id, cutting_no from ppl_cut_lay_mst where id=$data[2]");

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
								<td width="40%"  >
									<table  width="100%" border="0">
										<tr>
											<td><div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="60" width=""></div></td>
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
			$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("bundle_no")]).'.png';
	    	QRcode::png($val[csf("bundle_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
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
	exit();
}

?>