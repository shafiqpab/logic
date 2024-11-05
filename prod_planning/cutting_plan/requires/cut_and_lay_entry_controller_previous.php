<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$order_number_arr=return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number');
$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/cut_and_lay_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' )" );     	 
	exit();
}
if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 140, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
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
		?>
        <?  
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
		//***********************************End Tna date*******************************************************************************************
}


if($action=="size_popup")
{
  	echo load_html_head_contents("Cut and bundle details","../../../", 1, 1, '','1','');
	extract($_REQUEST);

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
		var xxxx=0;
		   for(var i=1; i<=row_num; i++)
		   {
			   xxxx+=($("#txt_size_qty_"+i).val()!='')?$("#txt_size_qty_"+i).val()*1:0; 
			 /*  if($("#txt_size_ratio_"+i).val()!='')
			   {
				 ratio_total+=$("#txt_size_ratio_"+i).val(); 
			   }*/
		   }
			 $('#total_size_qty').text(xxxx);
			// $('#total_size_ratio').text(ratio_total);
			 $('#hidden_marker_qty').val(xxxx);
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
		var to_marker_qty=$("#hidden_marker_qty").val();
		var job_id=$("#hidden_update_job_id").val();
		var cut_no=$("#hidden_update_cut_no").val();	
        var row_num=$('#tbl_size_details tbody tr').length;
        var data1="action=save_update_delete_size&operation="+operation+"&row_num="+row_num+"&color_id="+color_id+"&mst_id="+mst_id+"&dtls_id="+dtls_id+"&bundle_per_pcs="+bundle_per_pcs+"&to_marker_qty="+to_marker_qty+"&cbo_company_id="+cbo_company_id+"&job_id="+job_id+"&cut_no="+cut_no+"&order_id="+order_id+"&gmt_id="+gmt_id;
	     var data2='';
		for(var k=1; k<=row_num; k++)
		{
	     		data2+=get_submitted_data_string('txt_lay_balance_'+k+'*txt_size_ratio_'+k+'*txt_size_qty_'+k+'*txt_bundle_'+k+'*hidden_size_id_'+k+'*update_size_id_'+k,"../../../",k);
		}
	    var data=data1+data2;
		//alert(data);
		//freeze_window(operation);
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
						$('#msg_box_popp').html("Data Save  Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
				 }
			 if(reponse[0]==1)
				 {
					 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#msg_box_popp').html("Data Update  Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
				 }
			
			show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3],'show_bundle_list_view','search_div','cut_and_lay_entry_controller','setFilterGrid("list_view",-1)');
			var update_size_id=reponse[3].split('_');
			$("#hidden_plant_qty").val(reponse[4]);
			$("#hidden_total_marker").val(reponse[5]);
			$("#hidden_lay_balance").val(reponse[6]);
			for(var i=1;i<=update_size_id.length;i++)
			{
			$('#update_size_id_'+i).val(update_size_id[i-1]);	
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
				show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3],'show_bundle_list_view','search_div','cut_and_lay_entry_controller','setFilterGrid("list_view",-1)');
		   }
		     if(reponse[0]==201)
		    {
				alert("Save Restricted.This information found in Cutting Qc Page Which System Id "+reponse[3]+".");
				show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3],'show_bundle_list_view','search_div','cut_and_lay_entry_controller','setFilterGrid("list_view",-1)');
		   }
		 release_freezing();
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
	   print_report(<? echo $cbo_company_id; ?>+'*'+<? echo $mst_id; ?>+'*'+<? echo $details_id; ?>+'*'+report_title  , "cut_lay_bundle_print", "cut_and_lay_entry_controller")
		
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
		var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_controller");
		window.open(url,"##");
		}
		else
		{
		var url=return_ajax_request_value(data, "print_report_bundle_barcode_eight", "cut_and_lay_entry_controller");
		window.open(url,"##");	
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
	$("#bundleNo_"+row_num).val($("#bundleNo_"+actual_id).val()+"/");
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
		  serial_rearrange()
		
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
    $("#sizeName_"+id_row).attr("disabled",false);
	$('#bundleSizeQty_'+id_row).removeAttr("onKeyUp").attr("onKeyUp","fnc_rearrange_rmg("+id_row+");");
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
			
			j++;
			tot_row++;
			dataString_bundle+='&txtBundleNo_' + j + '=' + bundle_no + '&txtBundleQty_' + j + '=' + bundle_size_qty + '&txtBundleFrom_' + j + '=' + bundle_from+ '&txtBundleTo_' + j + '=' + bundle_to+ '&txtSizeId_' + j + '=' + bundle_size_id+ '&txtHiddenSizeId_' + j + '=' + hidden_size_id+ '&hiddenSizeqty_' + j + '=' + hidden_size_qty+ '&hiddenUpdateFlag_' + j + '=' + hidden_update_flag+'&hiddenUpdateValue_' + j + '=' + hiddenUpdateValue;
		});
		

		var bundle_mst_id=$("#hidden_mst_id").val();
		var bundle_dtls_id=$("#hidden_detls_id").val();
		var data="action=save_update_delete_bundle&operation="+operation+'&tot_row='+tot_row+'&bundle_dtls_id='+bundle_dtls_id+'&bundle_mst_id='+bundle_mst_id+dataString_bundle;
		//alert(data);return;
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
			show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3],'show_bundle_list_view','search_div','cut_and_lay_entry_controller','setFilterGrid("list_view",-1)');
		}
		release_freezing();	
	}
}
	
</script>

</head>
<body onLoad="set_hotkey()">
<div id="msg_box_popp"  style=" height:15px; width:200px;  position:relative; left:250px "></div>
<div align="center" style="width:100%; overflow-y:hidden; position:absolute; top:5px;">

		<? echo load_freeze_divs ("../../../",$permission); 
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
          <fieldset style="width:400px;">
            <table cellpadding="0" cellspacing="0" width="400" class="" id="tbl_size_details">
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
                         <input type="button" id="close_size_id" width="100px" name="close_size_id"  class="formbutton" style="width:60px" onClick="size_popup_close(<? echo $size; ?>,$('#hidden_marker_qty').val(),$('#hidden_plant_qty').val(),$('#hidden_total_marker').val(),$('#hidden_lay_balance').val())" value="Close"/>
                        <input type="button" id="btn_print" name="btn_print" value=" Bundle List" class="formbutton"  onClick="fnc_print_bundle()"/>
                        <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 6/page" class="formbutton" onClick="fnc_bundle_report(6)"/>
                            <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value=" Bundle Sticker 8/page" class="formbutton" onClick="fnc_bundle_report(8)"/>
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
                        <th><input type='hidden' id="hidden_marker_qty" name="hidden_marker_qty"  value="<? echo $total_marker_qty; ?>"/>
                        <input type='hidden' id="hidden_marker_no_x" name="hidden_marker_no_x"  />
                        </th>
                   </tr>
                    <tr>
                    <td   align="center" valign="middle" colspan="5" >
                         <? echo load_submit_buttons( $permission, "fnc_cut_lay_size_info", 0,0,"clear_size_form()",1);?>
                     </td>
                     </tr>
                     <tr>
                     <td   align="center"  colspan="5" >
                          <input type="button" id="close_size_id" name="close_size_id"  class="formbutton"  style="width:50px" onClick="size_popup_close(<? echo $size; ?>,$('#hidden_marker_qty').val(),$('#hidden_plant_qty').val(),$('#hidden_total_marker').val(),$('#hidden_lay_balance').val())" value="Close"/>
                         <input type="button" id="btn_print" name="btn_print" value=" Bundle List" class="formbutton" onClick="fnc_print_bundle()"/>
                         <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 6/page" class="formbutton" onClick="fnc_bundle_report(6)"/>
                          <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 8/page" class="formbutton" onClick="fnc_bundle_report(8)"/>
                        
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
    
    <?  } ?>

</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

//---------------------------bundle qty update---------------------------------------------------------------------------------


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
		$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","cutting_no=".$txt_cutting_no."");
		if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; die;}
		$id=return_next_id("id","ppl_cut_lay_bundle",1);
		$rID=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$bundle_mst_id." and dtls_id=".$bundle_dtls_id."",0);
		$field_array="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,number_start,number_end,size_qty,update_flag,update_value,inserted_by,insert_date,
		status_active,is_deleted";
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
				
				 $data_array.="(".$id.",".$bundle_mst_id.",".$bundle_dtls_id.",".$$new_bundle_size_id.",'".$new_bundle_prifix."','".$new_bundle_prif_no[0]."','".$$new_bundle_no."',".$$new_bundle_from.",".$$new_bundle_to.",".$$new_bundle_qty.",".$update_flag.",'".$update_flag_value."','".$user_id."','".$pc_date_time."',1,0)";
				$id = $id+1;
			}
		//echo $data_array;die;	
		$rID1=sql_insert("ppl_cut_lay_bundle",$field_array,$data_array,1);
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
		
		if($db_type==2 || $db_type==1 )
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

//bundle_bar_code stiker****************************************************************************************************************************************************

if($action=="print_report_bundle_barcode_eight")
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
	$pdf=new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //where id in ($data)
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
    	 $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id");
		 
		 $cope_page=1;
		 if(count($sql_bundle_copy)!=0)
		 {
		 foreach($sql_bundle_copy as $inf)
		 {
			if($br==8) { $pdf->AddPage(); $br=0; $i=10; $j=12; $k=0; }
			foreach($color_sizeID_arr as $val)
			   {
				    
					if($br==8) 
					{
						 $pdf->AddPage(); $br=0; $i=10; $j=12; $k=0;
				    }
					
					if( $k>0 && $k<2 ) { $i=$i+105; }
						$pdf->Code39($i, $j, $val[csf("bundle_no")]);
						$pdf->Code39($i+45, $j-4, "Bundle Card ", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
						$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+6, "Cut Date	 : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
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
						
					$k++;
					
					if($k==2)
					{  $k=0; $i=10; $j=$j+75; }
					$br++;
				 }
				 $br=8;
		    $cope_page++;
	       }   
	    }
		else
		{
		   foreach($color_sizeID_arr as $val)
			   {
					if($br==8) { $pdf->AddPage(); $br=0; $i=10; $j=12; $k=0; }
					if( $k>0 && $k<2 ) { $i=$i+105; }
						$pdf->Code39($i, $j, $val[csf("bundle_no")]);
						//$pdf->Code39($i+45, $j-4, "Bundle Card ", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
						$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+6, "Cut Date	 : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
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
	$pdf=new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //where id in ($data)
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
		$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","cutting_no='".$cut_no."'");
		//echo $cutting_qc_no;die;
		if($cutting_qc_no!="") { echo "201**".$mst_id."**".$dtls_id."**".$cutting_qc_no; die;}
		$rID=execute_query("delete from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$rID1=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$id=return_next_id("id", "  ppl_cut_lay_size", 1);	
		$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date,status_active,is_deleted";
		$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
		$field_array1="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,number_start,number_end,size_qty,inserted_by,insert_date,status_active,is_deleted";
		$sql_bundle=sql_select("SELECT c.bundle_num_prefix_no,c.bundle_num_prefix  FROM ppl_cut_lay_bundle c,ppl_cut_lay_mst a,ppl_cut_lay_dtls b WHERE b.id=c.dtls_id and a.id=c.mst_id and a.cutting_no='".$cut_no."'  ORDER BY c.bundle_num_prefix_no DESC ");
		  $sql_rmg=sql_select("select max(a. number_end) as last_rmg from ppl_cut_lay_bundle a, ppl_cut_lay_mst b where b.id=a.mst_id and b.cutting_no='".$cut_no."' ");
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
						 $cut_no_prifix=substr($cut_no,4);
						for($i=0; $i<=8; $i++)
						{
						 if(substr($cut_no_prifix, 0, 1)=="0")
						 {
						   $cut_no_prifix=substr($cut_no_prifix,1);
						 }
						
						}
						 $bundle_prif=$company_sort_name."-".$cut_no_prifix;
					}
		
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
				    $bundle_no=$bundle_prif."-".$bundle_no_prif[0];
				    if ($add_comma>0) $data_array1.=","; 
					 if ($k>0) $data_array1.=",";  
					 if($k!=$bundle_per_size-1)
						  {
							 $bundle_from=$bundle_start+($bundle_qty*$k);
							 $bundle_end=$bundle_start+$bundle_qty*($k+1)-1;
							 $data_array1.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id_value.",'".$bundle_prif."','".$bundle_no_prif[0]."','".$bundle_no."',".$bundle_from.",".$bundle_end.",".$bundle_qty.",'".$user_id."','".$pc_date_time."',1,0)";
						  }
					 else
					  {
							$bundle_from=$bundle_start+($bundle_qty*$k);
							$bundle_end=$bundle_start+$size_qty_value-1;
							$bundle_qty=str_replace("'","",$bundle_end)-str_replace("'","",$bundle_from)+1;
						    $data_array1.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id_value.",'".$bundle_prif."','".$bundle_no_prif[0]."','".$bundle_no."',".$bundle_from.",".$bundle_end.",".$bundle_qty.",'".$user_id."','".$pc_date_time."',1,0)";
						 }
				  $bundle_id=$bundle_id+1;
				  }
				  $bundle_start=$bundle_end+1;
				  $jj++;
	         	}
			}
        if($data_array_up!="")
		{  
		$rID3=sql_insert(" ppl_cut_lay_size",$field_array,$data_array,0);
		//echo "insert into ppl_cut_lay_bundle($field_array1)values".$data_array1;
		$rID4=sql_insert(" ppl_cut_lay_bundle",$field_array1,$data_array1,0);
		$field_array_up="marker_qty*updated_by*update_date";
		$data_array_up="".$to_marker_qty."*'".$user_id."'*'".$pc_date_time."'";
		$rID5=sql_update(" ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0); 
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
			disconnect($con);
			die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	  {
			$con = connect();		
			if($db_type==0)	{ mysql_query("BEGIN"); }
			
			$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","cutting_no='".$cut_no."'");
			//echo $cutting_qc_no;die;
			if($cutting_qc_no!="") { echo "200**".$mst_id."**".$dtls_id."**".$cutting_qc_no; die;}
			//master table update*********************************************************************hidden_size_id_
			$flag=0;
			$field_array_up="size_id*size_ratio*marker_qty*bundle_sequence*updated_by*update_date";
			$field_array1="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,number_start,number_end,size_qty,inserted_by,insert_date";
			$id=return_next_id("id", "  ppl_cut_lay_size", 1);	
			$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
		    $sql_rmg=sql_select("select min(a. number_start) as start_rmg from ppl_cut_lay_bundle a, ppl_cut_lay_mst b where b.id=a.mst_id and b.cutting_no='".$cut_no."' and a.mst_id=".$mst_id." and a.dtls_id=".$dtls_id."" );
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
		  $sql_bundle=sql_select("SELECT c.bundle_num_prefix_no,c.bundle_num_prefix  FROM ppl_cut_lay_bundle c,ppl_cut_lay_mst a,ppl_cut_lay_dtls b WHERE b.id=c.dtls_id and a.id=c.mst_id and c.mst_id=".$mst_id." and c.dtls_id=".$dtls_id."  ORDER BY c.bundle_num_prefix_no ASC");
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
                                   <input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onKeyUp="bundle_calclution(<? echo $i;  ?>,<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>"  style="width:50px; text-align:right; <? if($update_f_value[0]!="") echo "background-color:#F3F;"; ?>" class="text_boxes"  disabled/>
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

    <?	
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
	
	$sql="select a.cut_num_prefix_no, a.table_no, a.job_no, a.entry_date,a.marker_length, a.marker_width ,a.fabric_width, a.gsm, b.order_id,b.color_id,b.gmt_item_id,b.plies,b.marker_qty ,b.order_qty from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";
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
						
						
						     $size_data=sql_select("select DISTINCT a.id,a.size_id,a.bundle_sequence,a.marker_qty from  ppl_cut_lay_size a where  a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");    
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
                                        <td align="center"><? echo $cut_no;   ?></td>
									   <td align="center"><? echo $bundle_prifix[2];  ?></td>
									   <td align="center"><? echo $row[csf('size_qty')];  ?></td>
									   <td align="center"><? echo $row[csf('number_start')];  ?></td>
									   <td align="center"><? echo $row[csf('number_end')];  ?></td>
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
				$sql_prifix=sql_select( "SELECT DISTINCT  cut_num_prefix_no FROM ppl_cut_lay_mst WHERE company_id=".$cbo_company_name." ORDER BY cut_num_prefix_no DESC ");
				if( count($sql_prifix)>0)
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
				$cut_no_prifix[0]+=1;
				$comp_prefix=return_field_value("company_short_name","lib_company", "id=$cbo_company_name");
				$cut_no=str_pad((int) $cut_no_prifix[0],10,"0",STR_PAD_LEFT);
				$new_cutting_number=$comp_prefix."-".$cut_no;
				$new_cutting_prifix=$comp_prefix."-".$job_prifix;
				$id=return_next_id("id", " ppl_cut_lay_mst", 1);	
				$field_array="id,cut_num_prefix,cut_num_prefix_no,cutting_no,table_no,job_no,batch_id,company_id,entry_date,start_time,end_date,end_time ,marker_length,marker_width,fabric_width, gsm,width_dia,inserted_by,insert_date,status_active,is_deleted";
				$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
				$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
				$data_array="(".$id.",'".$new_cutting_prifix."',".$cut_no_prifix[0].",'".$new_cutting_number."',".$tbl_id.",".$txt_job_no.",".$txt_batch_no.",".$cbo_company_name.",".$txt_entry_date.",'".$start_time."',".$txt_end_date.",'".$end_time."',".$txt_marker_length.",".$txt_marker_width.",".$txt_fabric_width.",".$txt_gsm.",".$cbo_width_dia.",'".$user_id."','".$pc_date_time."',1,0)";
				//$rID1=sql_insert(" ppl_cut_lay_mst",$field_array,$data_array,0); 
			}
		else
			{
				$field_array="table_no*job_no*batch_id*company_id*entry_date*start_time*entry_date*end_time*marker_length*marker_width*fabric_width*gsm*width_dia*updated_by*update_date";
				$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
			    $end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
				$data_array="".$update_tbl_id."*".$txt_job_no."*".$txt_batch_no."*".$cbo_company_name."*".$txt_entry_date."*'".$start_time."'*,".$txt_end_date."*'".$end_time."'*".$txt_marker_length."*".$txt_marker_width."*".$txt_fabric_width."*".$txt_gsm."*".$cbo_width_dia."*'".$user_id."'*'".$pc_date_time."'";
				//$rID1=sql_update("ppl_cut_lay_mst",$field_array,$data_array,"id",$update_id,0); 
		     }
		    $detls_id=return_next_id("id", " ppl_cut_lay_dtls", 1);
		    $field_array1="id, mst_id,order_id,ship_date,color_id,gmt_item_id,plies,order_qty,inserted_by,insert_date,status_active,is_deleted";
			$field_array_up="order_id*ship_date*color_id*gmt_item_id*plies*order_qty*updated_by*update_date";
		    $add_comma=0;
			for($i=1; $i<=$row_num; $i++)
			   {
					$cbo_order_id="cboorderno_".$i;
					$txt_ship_date="txtshipdate_".$i;
					$cbocolor="cbocolor_".$i;
					$cbo_gmt_id="cbogmtsitem_".$i;
					$order_qty="txtorderqty_".$i;
					$txt_plics="txtplics_".$i;
					$update_details_id="updateDetails_".$i;
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
								$updateID_array[]=str_replace("'",'',$$update_details_id); 
								$data_array_up[str_replace("'",'',$$update_details_id)]=explode("*",("".$$cbo_order_id."*".$$txt_ship_date."*".$$cbocolor."*".$$cbo_gmt_id."*".$$txt_plics."*".$$order_qty."*'".$user_id."'*'".$pc_date_time."'*1*0"));
					
						  }
					 else
					      {
							   if ($add_comma!=0) $data_array1 .=",";
							   if ($add_comma!=0) $detls_id_array .="_";
								$data_array1.="(".$detls_id.",".$master_id.",".$$cbo_order_id.",".$$txt_ship_date.",".$$cbocolor.",".$$cbo_gmt_id.",".$$txt_plics.",".$$order_qty.",'".$user_id."','".$pc_date_time."',1,0)";   
								$detls_id_array.=$detls_id;
								$detls_id=$detls_id+1;
								$add_comma++;
						  }
		        }
			$detls_id_update.=implode("_",$updateID_array);
			$detls_id_update.="_".$detls_id_array;
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
	      if($db_type==0)
		        {
				  if(str_replace("'","",$update_id)=="")
				        {
							if($rID1 && $rID2)
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
							 if( $rID1 && $rID2)
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
			$field_array="table_no*job_no*batch_id*company_id*entry_date*start_time*end_date*end_time*marker_length*marker_width*fabric_width*gsm*width_dia*updated_by*update_date";
			$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
			$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
			$data_array="".$tbl_id."*".$txt_job_no."*".$txt_batch_no."*".$cbo_company_name."*".$txt_entry_date."*'".$start_time."'*".$txt_end_date."*'".$end_time."'*".$txt_marker_length."*".$txt_marker_width."*".$txt_fabric_width."*".$txt_gsm."*".$cbo_width_dia."*'".$user_id."'*'".$pc_date_time."'";
			$rID1=sql_update("ppl_cut_lay_mst",$field_array,$data_array,"id",$update_id,0);
		    $detls_id=return_next_id("id", " ppl_cut_lay_dtls", 1);
			$field_array1="id, mst_id,order_id,ship_date,color_id,gmt_item_id,plies,order_qty,inserted_by,insert_date,status_active,is_deleted";
			$field_array_up="order_id*ship_date*color_id*gmt_item_id*plies*order_qty*updated_by*update_date";
			$add_comma=0;
			for($i=1; $i<=$row_num; $i++)
				{
					$cbo_order_id="cboorderno_".$i;
					$txt_ship_date="txtshipdate_".$i;
					$cbocolor="cbocolor_".$i;
					$cbo_gmt_id="cbogmtsitem_".$i;
					$order_qty="txtorderqty_".$i;
					$txt_plics="txtplics_".$i;
					$update_details_id="updateDetails_".$i;
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
								$updateID_array[]=str_replace("'",'',$$update_details_id); 
								$data_array_up[str_replace("'",'',$$update_details_id)]=explode("*",("".$$cbo_order_id."*".$$txt_ship_date."*".$$cbocolor."*".$$cbo_gmt_id."*".$$txt_plics."*".$$order_qty."*'".$user_id."'*'".$pc_date_time."'*1*0"));
						  }
					 else
					      {
							    if ($add_comma!=0) $data_array1 .=",";
							    if ($add_comma!=0) $detls_id_array .="_";
								$data_array1.="(".$detls_id.",".$msster_id.",".$$cbo_order_id.",".$$txt_ship_date.",".$$cbocolor.",".$$cbo_gmt_id.",".$$txt_plics.",".$$order_qty.",'".$user_id."','".$pc_date_time."',1,0)";   
								$detls_id_array.=$detls_id;
								$detls_id=$detls_id+1;
								$add_comma++;
						  }
				  }
			$detls_id_update.=implode("_",$updateID_array);
			$detls_id_update.="_".$detls_id_array;
			if(count($updateID_array)>0)
				{
					$rID2=execute_query(bulk_update_sql_statement("ppl_cut_lay_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);
				}
			if(count($data_array1)>0)  
				{
				   $rID2=sql_insert(" ppl_cut_lay_dtls",$field_array1,$data_array1,1); 
				}
		 if($db_type==0)
			 {
				if($rID1 && $rID2)
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
					if($rID1 && $rID2)
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
	
	$sql_order="select a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,d.order_id,d.color_id,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where  a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and c.id=d.order_id $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond order by id";
	$table_no_arr=return_library_array( "select id,table_no  from  lib_cutting_table",'id','table_no');
	$arr=array(2=>$table_no_arr,4=>$order_number_arr,5=>$color_arr);
	echo create_list_view("list_view", "Cut No,Year,Table No,Job No,Order NO,Color,Marker Length,Markar Width,Fabric Width,Entry Date","90,50,60,120,120,100,80,80,80,120","950","270",0, $sql_order , "js_set_cutting_value", "id", "", 1, "0,0,table_no,0,order_id,color_id,0,0,0,0,0", $arr, "cut_num_prefix_no,year,table_no,job_no,order_id,color_id,marker_length,marker_width,fabric_width,entry_date", "","setFilterGrid('list_view',-1)") ;
}


if($action=="load_php_mst_form")
{
    $sql_data=sql_select("select b.id as   tbl_id,b.table_no,b.location_id,b.floor_id,a.id,a.job_no,a.company_id,a.entry_date,end_date,a.marker_length,a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.cutting_no,a.batch_id,a.start_time,a.end_time
	from  ppl_cut_lay_mst a, lib_cutting_table b
	where   a.table_no=b.id and a.id=".$data." ");
	
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
			echo "document.getElementById('txt_gsm').value  = '".($val[csf("gsm")])."';\n";
			echo "document.getElementById('cbo_width_dia').value  = '".($val[csf("width_dia")])."';\n";
			echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";  
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
}

if($action=="order_details_list")
{
	// $sql_gmt_arr="select ";
	 $tbl_row=0;
	 $sql_dtls=sql_select("select a.id,a.order_id,a.ship_date,a.color_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,a.lay_balance_qty,b.job_no,b.job_year,b.company_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b where b.id=a.mst_id and mst_id=".$data." ");
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
                       <input type="text" name="txtplics_<? echo $tbl_row; ?>"  id="txtplics_<? echo $tbl_row; ?>" class="text_boxes_numeric"  style="width:80px"  value="<? echo $val[csf('plies')];?>"/>
                      <input type="hidden" name="hiddenorder_<? echo $tbl_row; ?>"  id="hiddenorder_<? echo $tbl_row; ?>"  />
                      <input type="hidden" name="updateDetails_<? echo $tbl_row; ?>"  id="updateDetails_<? echo $tbl_row; ?>"  value="<? echo $val[csf('id')]; ?>" />
                      <input type="hidden" name="prifix_id"  id="prifix_id"  />
                </td>
                <td align="center">
                   	  <input type="text" name="txtsizeratio_<? echo $tbl_row; ?>" id="txtsizeratio_<? echo $tbl_row; ?>" class="text_boxes_numeric" onClick="openmypage_sizeNo(this.id);"  placeholder="Browse" style="width:50px" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick(1)"/>
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
}

if($action=="cut_lay_entry_report_print")
{
    // extract($_REQUEST);
	$data=explode('*',$data);
	
	$sql=sql_select("select id,cut_num_prefix_no,table_no,marker_length,marker_width,fabric_width,gsm,width_dia,batch_id,company_id from ppl_cut_lay_mst where cutting_no='".$data[0]."' ");
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
		}
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
	$sql_buyer_arr=sql_select("select buyer_name,style_ref_no from  wo_po_details_master where job_no='$data[1]'");
	$sql_order=sql_select("select order_id,gmt_item_id,order_qty from ppl_cut_lay_dtls where mst_id='$mst_id'");
	//print_r($sql_order);
	$order_number="";
	 foreach($sql_order as $order_val)
		 { 
		   $item_name=$order_val[csf('gmt_item_id')];
		   $order_qty+=$order_val[csf('order_qty')]; 
		   if($order_number!="")  $order_number.=",".$order_number_arr[$order_val[csf('order_id')]];
		   else  $order_number=$order_number_arr[$order_val[csf('order_id')]];
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
              <td>Start Time</td><td align="center" width="100"></td><td align="center" width="100"><strong>Total Time Taken</td>
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
     $sql_main_qry=sql_select("select a.id,b.id as sid,a.color_id,a.plies,b.size_id,b.size_ratio,b.marker_qty from    ppl_cut_lay_size b,ppl_cut_lay_dtls a where a.id=b.dtls_id and a.mst_id=$mst_id order by a.id,b.id");
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
	 }
	// print_r($total_gmt_qty);
   $col_span=count($size_id_arr);
   $td_width=450/$col_span;
  
  // echo $td_width;die;
   
   ?>
    <table border="1" cellpadding="1" cellspacing="1"   width="1100"class="rpt_table" rules="all">
          <tr height="30" >
          <td width="30">SL</td>
          <td width="60" align="center">Roll No </td>
          <td width="60" align="center">Roll Kgs </td>
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
		  foreach($plice_data_arr as $plice_id=>$plice_val)
			  {
				// echo  $plice_id;
				
				?>
                 <tr height="20">
                      <td width=""><? echo $i;  ?></td>
                      <td width="" align="center"> </td>
                      <td width="" align="center"></td>
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
                     <tr height="20">
                      <td width=""><? echo $i+1;  ?></td>
                      <td width="" align="center"> </td>
                      <td width="" align="center"></td>
                     
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
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
                     <tr height="20">
                      <td width=""><? echo $i+2;  ?></td>
                      <td width="" align="center"> </td>
                      <td width="" align="center"></td>
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
                      <td width="" align="right" valign="bottom" ><? echo $total_gmt_qty[$plice_id]['gmt_qty'];  ?></td> 
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
              </tr>
                     <tr height="20">
                     
                      <td width=""  align="left"> <? echo $i+3;  ?></td>
                      <td width=""  align="right"></td>
                      <td width=""  align="right"></td>
                       
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
                      <td width="" align="center"> </td>
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
	?>
    <div style=" width:150px; position:absolute; left:0px   ">
 
          <table border="1" cellpadding="1" cellspacing="1"   width="152"class="rpt_table" rules="all" >
               <tr style=" border-top:hidden" >
                       <td width="30"><? echo $i+$k; ?></td>
                       <td width="60"  ></td>
                       <td width="60"  ></td>
                  </tr>
          <?
		    for($k=1; $k<6; $k++)
			{
		  
		  ?>
                  <tr >
                       <td width="30"><? echo $i+$k; ?></td>
                       <td width="60"  ></td>
                       <td width="60"  ></td>
                  </tr>
                  
                 <?
			}
			
			?>
            </table>
       </div>
       
       <div style=" width:160px; position:absolute; left:160px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="160"class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="80">Booking<br>Consumption <br>Per Dzn</td>
                       <td width="80" align="center" ></td>
                  </tr>
            </table>
       </div>
       
        <div style=" width:160px; position:absolute; left:330px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="160"class="rpt_table" rules="all">
                  <tr  height="30" >
                       <td width="80" >CAD Marker<br>Consumption <br>Per Dzn</td>
                       <td width="80" align="center" ></td>
                  </tr>
            </table>
       </div>
         <div style=" width:180px; position:absolute; left:500px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="180"class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="40" rowspan="2">Net<br>KGS <br>Used</td>
                       <td width="70" align="center" >KGs</td>
                       <td width="70" align="center" >G.Qty</td>
                  </tr>
                   <tr  height="30">
                       
                       <td width="70" align="center" ></td>
                       <td width="70" align="center" ></td>
                  </tr>
            </table>
       </div>
       
        <div style=" width:230px; position:absolute; right:181px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="220"class="rpt_table" rules="all">
                  <tr  height="20">
                       <td width="80" rowspan="2">Net<br>Composition <br>Per Dzn</td>
                       <td width="70" align="center" >Net</td>
                       <td width="70" align="center" ></td>
                  </tr>
                   <tr  height="20">
                       
                       <td width="70" align="center" ></td>
                       <td width="70" align="center" ></td>
                  </tr>
            </table>
       </div>
         <div style=" width:180px; position:absolute; right:0; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="180"class="rpt_table" rules="all">
                  <tr  height="40">
                       <td width="100">Lay<br>Loss/Gain</td>
                       <td width="80" align="center" ></td>
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
   
}


?>