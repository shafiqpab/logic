<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 170,"select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1","id,location_name", 1, "Display", "", "","1" );
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select--", "", "" );   	 
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

?>
     
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( job_no )
	{
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th><th width="150">Buyer Name</th><th width="200">Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_job">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'grey_allocation_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_po_search_list_view', 'search_div', 'grey_allocation_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle"><? echo load_month_buttons();  ?>
            </td>
            </tr>
        <tr>
            <td align="center" valign="top" id="search_div"> 
	
            </td>
        </tr>
    </table>    
     
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_po_search_list_view")
{
	
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else { echo "Please Select Buyer First."; die; }
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr);
	 	 $sql= "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer order by a.job_no";  
		 echo  create_list_view("list_view", "Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "90,120,100,100,90,90,90,80","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "",'','0,0,0,0,1,0,1,3') ;
	
} 
if($action=="populate_data_from_search_popup")
{
	$sql= sql_select("select company_name,buyer_name,location_name from wo_po_details_master where job_no='$data'"); 
	foreach( $sql as $row)
	{
				echo "load_drop_down( 'requires/grey_allocation_controller', '".$row[csf("company_name")]."', 'load_drop_down_location', 'location_td' ); load_drop_down( 'requires/grey_allocation_controller', '".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' ) ;\n";

		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n"; 
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n"; 
		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
	}

}

if($action=="open_order_popup")
{
	echo load_html_head_contents("Order List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array();
		var selected_job = new Array();
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length; 

			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				document.getElementById( 'tr_' + i ).click();
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			var str_array=str.split("_");
			if ( document.getElementById('job_no').value!="" && document.getElementById('job_no').value!=str_array[3] )
			{
				alert('No Job Mix Allowed')
				return;	
			}
			else
			{
			document.getElementById('job_no').value=str_array[3];
			toggle( document.getElementById( 'tr_' + str_array[0] ), '#FFFFCC' );
			
			
			
			if( jQuery.inArray( str_array[1], selected_id ) == -1 ) {
				selected_id.push( str_array[1] );
				selected_name.push( str_array[2] );
                selected_job.push( str_array[3] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str_array[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_job.splice( i, 1 );
			}
			var id = '';
			var name='';
			var job='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				job += selected_job[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			job = job.substr( 0, job.length - 1 );

			
			$('#order_id').val( id );
			$('#order_no').val( name );
			//$('#job_no').val( job );
			
			}
		}
	
    </script>
</head>

<body>
<div align="center" style="width:1000px;">
<input type="hidden" id="order_id" />
<input type="hidden" name="order_no" id="order_no" value="" />
<input type="hidden" name="job_no" id="job_no" value="" />
	<? 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (1=>$comp,2=>$buyer_arr);
	$sql= "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$txt_job_no'  and a.status_active=1 and b.status_active=1   order by a.job_no";  
	echo  create_list_view("list_view", "Job No,Company,Buyer,Style Ref. No,Job Qty.,PO number,PO Qty,Shipment Date", "90,60,50,100,70,90,70,80","710","320",0, $sql , "js_set_value", "id,po_number,job_no", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", '','setFilterGrid(\'list_view\',-1)','0,0,0,0,1,0,1,3','',1);
	?>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
 
}

if($action=="open_item_popup")
{
	echo load_html_head_contents("Item List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( str ) 
		{
			var str_array=str.split("_");
			$('#product_id').val( str_array[0] );
			$('#product_name').val( str_array[1] );
			$('#available_qnty').val( str_array[2] );
			$('#unit_of_measurment').val( str_array[3] );
			parent.emailwindow.hide()
		}
	   
	
    </script>
</head>

<body>
<div align="center" style="width:1000px;">
<input type="hidden" id="product_id" />
<input type="hidden" name="product_name" id="product_name" value="" />
<input type="hidden" name="available_qnty" id="available_qnty" value="" />
<input type="hidden" name="unit_of_measurment" id="unit_of_measurment" value="" />
	<? 
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$arr=array (0=>$comp,1=>$item_category);
	$sql= "select id,company_id,item_category_id,product_name_details,current_stock,allocated_qnty,available_qnty,unit_of_measure from product_details_master where company_id=$cbo_company_name and item_category_id=$cbo_item_category and current_stock > allocated_qnty and status_active=1 and  	is_deleted=0";  
	echo  create_list_view("list_view", "Company,Item Catagory,Product Name,Current Stock,Allocated Qnty,Available Qnty", "60,100,350,100,100,100","860","320",0, $sql , "js_set_value", "id,product_name_details,available_qnty,unit_of_measure", "", 1, "company_id,item_category_id,0,0,0,0", $arr , "company_id,item_category_id,product_name_details,current_stock,allocated_qnty,available_qnty", '','setFilterGrid(\'list_view\',-1)','0,0,0,2,2,2','',"");
	
	
	?>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
 
}
if($action=="open_qnty_popup")
{
	echo load_html_head_contents("Item List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	function distribution_value(mehtod)
	    {
			if(mehtod==1)
			{
				$('#tbl_order_qnty_list input[name="txt_qnty[]"]').removeAttr('disabled', 'disabled');
				$('#allocated_qnty').attr('disabled', 'disabled');
			}
			else
			{
				$('#tbl_order_qnty_list input[name="txt_qnty[]"]').attr('disabled', 'disabled');
				$('#allocated_qnty').removeAttr('disabled', 'disabled');
			}
		}
		
	function set_sum_value(des_fil_id,field_id,table_id)
	{
		var rowCount = $('#tbl_order_qnty_list tr').length-2;
		var ddd={dec_type:6,comma:0,currency:1};
		math_operation( des_fil_id, field_id, '+', rowCount,ddd);
	}
	function js_set_value_qnty()
	{
		var rowCount = $('#tbl_order_qnty_list tr').length-2;
		var qnty_breck_down="";
		for(var i=1; i<=rowCount; i++)
		{
			if (form_validation('txt_qnty_'+i,'Qnty')==false)
			{
				return;
			}
			if(qnty_breck_down=="")
			{
				qnty_breck_down=$('#txt_qnty_'+i).val();
			}
			else
			{
				qnty_breck_down+="_"+$('#txt_qnty_'+i).val();
			}
		}
		document.getElementById('qnty_breck_down').value=qnty_breck_down;
		var allocated_qnty=document.getElementById('allocated_qnty').value;
		var available_qnty=document.getElementById('available_qnty').value;
		if(allocated_qnty*1>available_qnty*1)
		{
			alert("Allocated qnty greater than available qnty");
			return;
		}
		else
		{
		parent.emailwindow.hide();
		}
		
	}
	
	function calculate_poportion(value)
	{
		var tot_po_qnty=(document.getElementById('tot_po_qnty').value)*1;
		var rowCount = $('#tbl_order_qnty_list tr').length-2;
		for(var i=1; i<=rowCount; i++)
		{
			var txt_order_qnty=($('#txt_order_qnty_'+i).val())*1;
			
			$('#txt_qnty_'+i).val(number_format_common(((value/tot_po_qnty)*txt_order_qnty),2,0,1));
		}
		set_sum_value('allocated_qnty','txt_qnty_','tbl_order_qnty_list')
	}
    </script>
</head>

<body>
<?
$data=explode(",",$txt_order_id);
$data1=explode("_",$qnty_breck_down);

?>
    <div align="center" style="width:1000px;">
        <strong>Distribution Method:</strong>
        <input type="radio" name="distribution_type" id="distribution_type_0" value="0" onClick="distribution_value(this.value)" checked />
        <label for="distribution_type_0">Proportionately</label>
        <input type="radio" name="distribution_type" id="distribution_type_1" value="1" onClick="distribution_value(this.value)" />
        <label for="distribution_type_1">Manually</label>
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="500" id="tbl_order_qnty_list" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>                	 
                        <th width="150" colspan="3">
                        Available Qnty:<input type="text" name="available_qnty"  id="available_qnty" style="width:90px " value="<? echo $available_qnty; ?>" class="text_boxes_numeric" disabled />
                        Allocated Qnty:<input type="text" name="allocated_qnty"  id="allocated_qnty" style="width:90px "  class="text_boxes_numeric" value="<? echo $txt_qnty;?>" onChange="calculate_poportion(this.value)"/>
                        <input type="hidden" name="qnty_breck_down"  id="qnty_breck_down" style="width:90px "  class="text_boxes" value="<? echo $qnty_breck_down;?>"/>
                        </th>
                    </tr>
                    <tr>                	 
                        <th width="200">Order No</th>
                        <th width="150">Order Qnty</th>
                        <th width="150" class="must_entry_caption">Qnty</th>
                    </tr>
                </thead>
                <tbody>
					<?
					
					$sl=1;
					$tot_po_qnty=0;
                    for($i=0;$i<count($data);$i++)
                    {
						$sql_order_no_qnty=sql_select("select po_number,po_quantity,plan_cut from wo_po_break_down where id =$data[$i]");
						list($order_data)=$sql_order_no_qnty;
						$tot_po_qnty+=$order_data[plan_cut];
                    ?>
                    <tr>
                        <td width="200">
                        <input type="text" class="text_boxes"  name="txt_order_no[]"  id="txt_order_no_<? echo $sl; ?>" style="width:200px " value="<? echo $order_data[po_number];?>" disabled />
                        <input type="hidden" name="txt_order_id[]"  id="txt_order_id_<? echo $sl; ?>" style="width:160px " value="<? echo $data[$i];?>" disabled />
                        </td>
                        <td width="150">
                        <input type="text" name="txt_order_qnty[]"  id="txt_order_qnty_<? echo $sl; ?>" style="width:150px "  class="text_boxes_numeric"  value="<? echo $order_data[plan_cut];?>" disabled />
                        </td>
                        <td width="150">
                        <input type="text" name="txt_qnty[]"  id="txt_qnty_<? echo $sl; ?>" style="width:150px " value="<? echo $data1[$i]; ?>" class="text_boxes_numeric" onChange="set_sum_value('allocated_qnty','txt_qnty_','tbl_order_qnty_list')" disabled />
                        </td>
                    </tr>
                    <?
					$sl++;
				    }
					?>
                </tbody>
                
            </table>
            <table width="540"  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
            <tr>
           <td align="center" width="100%" class="button_container">
                        
						        <input type="button" class="formbutton" value="Close" onClick="js_set_value_qnty()"/>
                                <input type="hidden" name="tot_po_qnty" id="tot_po_qnty" value="<? echo $tot_po_qnty;?>"/>

                        </td> 
            </tr>
            </table>
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit(); 
}

if($action=="save_update_delete")
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
		$id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
		$field_array="id,job_no,po_break_down_id,item_category,allocation_date,item_id,qnty,qnty_break_down,inserted_by,insert_date";
		$data_array="(".$id.",".$txt_job_no.",".$txt_order_id.",".$cbo_item_category.",".$txt_allocation_date.",".$txt_item_id.",".$txt_qnty.",".$qnty_breck_down.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
//====================================================================================
		$add_comma=0;
		$field_array1="id,mst_id,job_no,po_break_down_id,item_category,allocation_date,item_id,qnty,inserted_by,insert_date";
		$po_break_down_id=explode(',',str_replace("'",'',$txt_order_id));
        $qnty_data=explode("_",str_replace("'",'',$qnty_breck_down));
		
		if ( count($po_break_down_id)>0)
		{
			for($c=0;$c < count($po_break_down_id);$c++)
			{
				 $id1 = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				 if ($add_comma!=0) $data_array1 .=",";
				 $data_array1 .="(".$id1.",".$id.",".$txt_job_no.",".$po_break_down_id[$c].",".$cbo_item_category.",".$txt_allocation_date.",".$txt_item_id.",".$qnty_data[$c].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 $add_comma++;
			}
		//$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
		}
//=================================================
 		$rID=sql_insert("inv_material_allocation_mst",$field_array,$data_array,0);
		if($data_array1 !='')
		{
				 $rID1=sql_insert("inv_material_allocation_dtls",$field_array1,$data_array1,0);
		}
		$$rID_de=execute_query( "update  product_details_master set allocated_qnty=(allocated_qnty+$txt_qnty) where id=$txt_item_id  ",1 );
		$$rID_de=execute_query( "update  product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_item_id  ",1 );
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$rID;

		}
		disconnect($con);
		die;
	}
	
	if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="job_no*po_break_down_id*item_category*allocation_date*item_id*qnty*qnty_break_down*updated_by*update_date";
		$data_array="".$txt_job_no."*".$txt_order_id."*".$cbo_item_category."*".$txt_allocation_date."*".$txt_item_id."*".$txt_qnty."*".$qnty_breck_down."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
//====================================================================================
		$add_comma=0;
		$field_array1="id,mst_id,job_no,po_break_down_id,item_category,allocation_date,item_id,qnty,inserted_by,insert_date";
		$po_break_down_id=explode(',',str_replace("'",'',$txt_order_id));
        $qnty_data=explode("_",str_replace("'",'',$qnty_breck_down));
		
		if ( count($po_break_down_id)>0)
		{
			$$rID_de=execute_query( "delete from inv_material_allocation_dtls where mst_id=$update_id  ",1 );
			for($c=0;$c < count($po_break_down_id);$c++)
			{
				 $id1 = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
				 if ($add_comma!=0) $data_array1 .=",";
				 $data_array1 .="(".$id1.",".$id.",".$txt_job_no.",".$po_break_down_id[$c].",".$cbo_item_category.",".$txt_allocation_date.",".$txt_item_id.",".$qnty_data[$c].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 $add_comma++;
			}
		}
//=================================================
		$rID=sql_update("inv_material_allocation_mst",$field_array,$data_array,"id","".$update_id."",0);

		if($data_array1 !='')
		{
				 $rID1=sql_insert("inv_material_allocation_dtls",$field_array1,$data_array1,0);
		}
		$$rID_de=execute_query( "update  product_details_master set allocated_qnty=((allocated_qnty-$txt_old_qnty)+$txt_qnty) where id=$txt_item_id  ",1 );
		$$rID_de=execute_query( "update  product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_item_id  ",1 );

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "1**".$rID;

		}
		disconnect($con);
		die;
	}
	
	if ($operation==2)  // Insert Here
	{
		$con = connect();
		$field_array="status_active*is_deleted";
		$data_array="'0'*'1'";
		$rID=sql_delete("inv_material_allocation_mst",$field_array,$data_array,"id","".$update_id."",1);
		$rID=sql_delete("inv_material_allocation_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		$$rID_de=execute_query( "update  product_details_master set allocated_qnty=(allocated_qnty-$txt_qnty) where id=$txt_item_id  ",1 );
		$$rID_de=execute_query( "update  product_details_master set available_qnty=(current_stock+allocated_qnty) where id=$txt_item_id  ",1 );
		disconnect($con);
		echo "2****".$rID;
	}
	
}


if($action=="show_item_active_listview")
{
	$data=explode("_",$data);
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
	$location=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
	$po_sql=sql_select("select distinct a.po_number,b.id from wo_po_break_down a,inv_material_allocation_mst b where a.job_no_mst=b.job_no and b.job_no='$data[0]'  and  FIND_IN_SET(a.id, b.po_break_down_id)  ");
	$po_num_array=array();
	foreach($po_sql as $row)
	{
		if (array_key_exists($row[csf('id')],$po_num_array))
		  {
		  $po_num_array[$row[csf('id')]]=$po_num_array[$row[csf('id')]].$row[csf('po_number')].",";
		  }
		else
		  {
		  $po_num_array[$row[csf('id')]]=$row[csf('po_number')].",";
		  }	
	}
	$arr=array (0=>$comp,1=>$buyer,2=>$location,3=>$po_num_array,4=>$item);
	$sql= "select a.id,a.job_no,a.po_break_down_id,a.item_id,a.qnty,b.company_name,b.buyer_name,b.location_name from  inv_material_allocation_mst a,wo_po_details_master b where  a.job_no=b.job_no and a.job_no='$data[0]' and a.item_category=$data[1] and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0";  
	echo  create_list_view("list_view", "Company,Buyer,Location,Order No,Item,Qnty", "60,100,100,200,250,100","860","320",0, $sql , "get_php_form_data", "id", "'populate_material_allocation_data'", 1, "company_name,buyer_name,location_name,id,item_id,0", $arr , "company_name,buyer_name,location_name,id,item_id,qnty", 'requires/grey_allocation_controller','','0,0,0,0,0,2','',"");
}

if($action=="populate_material_allocation_data")
{
	//$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
	$po_sql=sql_select("select distinct a.po_number,b.id from wo_po_break_down a,inv_material_allocation_mst b where a.job_no_mst=b.job_no and b.id='$data' and  FIND_IN_SET(a.id, b.po_break_down_id)  ");
	$po_num_array=array();
	foreach($po_sql as $row)
	{
		if (array_key_exists($row[csf('id')],$po_num_array))
		  {
		  $po_num_array[$row[csf('id')]]=$po_num_array[$row[csf('id')]].$row[csf('po_number')].",";
		  }
		else
		  {
		  $po_num_array[$row[csf('id')]]=$row[csf('po_number')].",";
		  }	
	}
	$sql= sql_select("select a.id,a.job_no,a.po_break_down_id,a.item_category,a.allocation_date,a.item_id,a.qnty,a.qnty_break_down,b.company_name,b.buyer_name,b.location_name from  inv_material_allocation_mst a,wo_po_details_master b where  a.job_no=b.job_no and a.id='$data' and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0"); 
	foreach($sql as $row_data)
	{
		//echo "select product_name_details,available_qnty from   product_details_master where id=$row_data[csf('id')]";
		$item_name=sql_select("select product_name_details,available_qnty,unit_of_measure from product_details_master where id='".$row_data[csf('item_id')]."'");
		list($item_name_row)=$item_name;
		echo "document.getElementById('txt_order_no').value = '".$po_num_array[$row_data[csf("id")]]."';\n";  
		echo "document.getElementById('txt_order_id').value = '".$row_data[csf("po_break_down_id")]."';\n"; 
		echo "document.getElementById('cbo_item_category').value = '".$row_data[csf("item_category")]."';\n"; 
		echo "document.getElementById('txt_allocation_date').value = '".change_date_format($row_data[csf("allocation_date")], "dd-mm-yyyy", "-")."';\n"; 
		echo "document.getElementById('txt_item').value = '".$item_name_row[csf("product_name_details")]."';\n";  
		echo "document.getElementById('txt_item_id').value = '".$row_data[csf("item_id")]."';\n"; 
		echo "document.getElementById('txt_qnty').value = '".$row_data[csf("qnty")]."';\n";
		echo "document.getElementById('txt_old_qnty').value = '".$row_data[csf("qnty")]."';\n";

		echo "document.getElementById('qnty_breck_down').value = '".$row_data[csf("qnty_break_down")]."';\n";
		echo "document.getElementById('available_qnty').value = '".$item_name_row[csf("available_qnty")]."';\n";
	    echo "document.getElementById('cbo_uom').value = '".$item_name_row[csf("unit_of_measure")]."';\n";
	    echo "document.getElementById('update_id').value = '".$row_data[csf("id")]."';\n";
	   	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_material_allocation_entry',1);\n";  
	}
	
}
?>
