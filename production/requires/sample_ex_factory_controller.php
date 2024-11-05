<?
session_start();
include('../../includes/common.php');


$user_id = $_SESSION['logic_erp']["user_id"];
 
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//************************************ Start *************************************************

if($db_type==2 || $db_type==1 )
{
	$mrr_date_check=" to_char(a.insert_date,'YYYY')";
}
else if ($db_type==0)
{
	$mrr_date_check=" year(a.insert_date)";
}

$sample_name_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no"  );
$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );		 
}



if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	exit();	 
}


if($action=="populate_data_from_search_popup")
{
	$dataArray=sql_select("select SUM(size_qty) as size_qty from sample_development_size WHERE mst_id=".$data."");
	foreach($dataArray as $row)
	{  
		echo "$('#txt_sewing_qnty').val('".$row[csf('size_qty')]."');\n";
	}
	
	$smp_mst_id = sql_select("select a.id,a.sys_number, a.company_id, a.location, a.delivery_to, a.ex_factory_date, a.transport_company_id, a.truck_no, a.lock_no, a.driver_name, a.dl_no, a.mobile_no, a.do_no, a.gp_no, a.final_destination, a.forwarder, a.dipo_name from sample_ex_factory_mst a,sample_ex_factory_dtls b where a.id=b.sample_ex_factory_mst_id and b.sample_development_id=$data and b.status_active=1 and b.is_deleted=0");
	 
	echo "load_drop_down('requires/sample_ex_factory_controller', '".$smp_mst_id[0][csf('company_id')]."', 'load_drop_down_location', 'location_td' );";
	
	if($smp_mst_id[0][csf('sys_number')]){
		echo "$('#txt_challan_no').val('".$smp_mst_id[0][csf('sys_number')]."');\n";
		echo "$('#mst_update_id').val('".$smp_mst_id[0][csf('id')]."');\n";
		echo "$('#cbo_company_name').val('".$smp_mst_id[0][csf('company_id')]."');\n";
		echo "$('#cbo_location_name').val('".$smp_mst_id[0][csf('location')]."');\n";
		echo "$('#txt_delivery_to').val('".$smp_mst_id[0][csf('delivery_to')]."');\n";
		echo "$('#txt_ex_factory_date').val('".change_date_format($smp_mst_id[0][csf('ex_factory_date')])."');\n";
		echo "$('#cbo_transport_company').val('".$smp_mst_id[0][csf('transport_company_id')]."');\n";
		echo "$('#txt_truck_no').val('".$smp_mst_id[0][csf('truck_no')]."');\n";
		echo "$('#txt_lock_no').val('".$smp_mst_id[0][csf('lock_no')]."');\n";
		echo "$('#txt_driver_name').val('".$smp_mst_id[0][csf('driver_name')]."');\n";
		echo "$('#txt_dl_no').val('".$smp_mst_id[0][csf('dl_no')]."');\n";
		echo "$('#txt_mobile_no').val('".$smp_mst_id[0][csf('mobile_no')]."');\n";
		echo "$('#txt_do_no').val('".$smp_mst_id[0][csf('do_no')]."');\n";
		echo "$('#txt_gp_no').val('".$smp_mst_id[0][csf('gp_no')]."');\n";
		echo "$('#txt_final_destination').val('".$smp_mst_id[0][csf('final_destination')]."');\n";
		echo "$('#cbo_forwarder').val('".$smp_mst_id[0][csf('forwarder')]."');\n";
		echo "$('#txt_dipo_name').val('".$smp_mst_id[0][csf('dipo_name')]."');\n";
	}
	
 exit();	
}//end action...............;



if($action=="set_po_number")
{
	$po_number=return_field_value("po_number","wo_po_break_down","id='$data'","po_number");
	echo "$('#txt_development_sample_id').val('".$po_number."');\n";
}//end action...............;


if($action=="sys_surch_popup")
{
extract($_REQUEST);
echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
?>
	<script>
	function js_set_value(smp,mst)
	{
 		$("#selected_id").val(smp+'*'+mst);
    	parent.emailwindow.hide();
 	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
             <thead>                	 
                <th width="160">Transport Com.</th>
                <th width="150">Buyer Name</th>
                <th width="100">Challan No</th>
                <th width="100">Order No</th>
                <th width="200">Ex-Factory Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </thead>
            <tr align="center">
                <td>  
                <? 
                echo create_drop_down( "cbo_trans_com", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id,supplier_name", 1, "-- Select --", $selected, "",0 );
                ?>
                </td>
                <td>  
                <? 
					echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
				?>
                </td>
                <td align="center" >				
                    <input type="text" style="width:100px" class="text_boxes"  name="txt_delivery_to" id="txt_delivery_to" />			
                </td>
                <td align="center" >				
                    <input type="text" style="width:100px" class="text_boxes"  name="txt_po_no" id="txt_po_no" />			
                </td>
                <td align="center">
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly> To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                </td> 
                <td align="center">
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_trans_com').value+'_'+document.getElementById('txt_delivery_to').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_po_no').value, 'create_delivery_search_list', 'search_div_delivery', 'sample_ex_factory_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td align="center" height="40" colspan="6" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="selected_id" >
                </td>
            </tr>
        </table>
        <div id="search_div_delivery" style="margin-top:20px;"></div>
    </form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}//end action...............;



if($action=="dev_sample_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Development Info","../../", 1, 1, $unicode);
?>
<html>
    <head>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
			//alert(str); 
			if(str==0) 
			{		
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';		 
			}
			else if(str==1) 
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}																																								
		}
		
		function js_set_value( mst_id )
		{
			document.getElementById('selected_id').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
	<div align="center" style="width:100%;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
		<table width="900" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
            		<table  cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
                        <thead>
                        	<th  colspan="6">
                              <?
                               echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                        
                        </thead>
                        <thead>
                        	<th width="140">Company Name</th>
                            <th width="160">Buyer Name</th>                	 
                            <th width="130">Style ID</th>
                            <th  width="130" >Style Name</th>
                            <th width="200">Est. Ship Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100%" /></th>
                        </thead>
        				<tr>
                        	<td width="140"> 
								<input type="hidden" id="selected_id"/>
								<? 
                                    echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'sample_ex_factory_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>
                    		</td>
                   			<td id="buyer_td" width="160">
								 <? 
                                    echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" );
                                ?>	
                            </td>
                            <td width="130">  
								<input type="text" style="width:130px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  />	
                            </td>
                            <td width="130" align="center">				
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_style_name" id="txt_style_name"  />			
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td> 
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name').value+'_'+<? echo $order_type;?>, 'create_po_search_list_view', 'search_div', 'sample_ex_factory_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" height="40" valign="middle">
				    <? 
                    echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                    ?>
					<? echo load_month_buttons();  ?>
          		</td>
            </tr>
        	<tr>
            	<td align="center" valign="top" id="search_div"></td>
        	</tr>
    	</table>
    </form>
	</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	load_drop_down( 'sample_ex_factory_controller',<? echo $company; ?>, 'load_drop_down_buyer', 'buyer_td' );
</script>
</html>
<?
exit();
}//end action...............;


if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	
	if ($data[2]!=0){
		$company=" and company_id='$data[2]'";
		$company_2=" and a.company_name='$data[2]'";
	}
	else { echo "Please Select Company First."; die; }
	
	if ($data[3]!=0){
		 $buyer=" and buyer_name='$data[3]'";
		 $buye_2=" and b.buyer_name='$data[3]'";
		 }
		 else{ $buyer="";}
		 
	if($data[0]==1)
		{
		   if (trim($data[1])!="") $style_id_cond=" and id='$data[1]'"; else $style_id_cond="";
		   if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
		   if (trim($data[1])!="") $style_id_cond_2=" and b.id='$data[1]'"; else $style_id_cond_2="";
		   if ($data[6]!="") $style_cond_2=" and a.style_ref_no='$data[6]'"; else $style_cond_2="";
		}
	
	if($data[0]==4 || $data[0]==0)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
		  if (trim($data[1])!="") $style_id_cond_2=" and b.id like '%$data[1]%' "; else $style_id_cond_2="";
		  if ($data[6]!="") $style_cond_2=" and a.style_ref_no like '%$data[6]%' "; else $style_cond_2="";
		}
	
	if($data[0]==2)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '$data[1]%' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
		  if (trim($data[1])!="") $style_id_cond_2=" and b.id like '$data[1]%' "; else $style_id_cond_2="";
		  if ($data[6]!="") $style_cond_2=" a.and style_ref_no like '$data[6]%' "; else $style_cond_2="";
		
		}
	
	if($data[0]==3)
		{
		  if (trim($data[1])!="") $style_id_cond=" and id like '%$data[1]' "; else $style_id_cond="";
		  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
		  if (trim($data[1])!="") $style_id_cond_2=" and b.id like '%$data[1]' "; else $style_id_cond_2="";
		  if ($data[6]!="") $style_cond_2=" and a.style_ref_no like '%$data[6]' "; else $style_cond_2="";
		}
	
	
	
	
	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
		
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate_2  = "and b.pub_shipment_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate_2 ="";
	}
	if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate_2  = "and b.pub_shipment_date  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate_2 ="";
	
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	
	$arr=array (1=>$comp,2=>$buyer_arr,4=>$product_dept,5=>$team_leader,6=>$dealing_marchant);
	
	
	if($data[7]==2){// 2=Sample Woth Order
	$sql= "select b.id,b.po_number,a.company_name as company_id,a.buyer_name,a.style_ref_no,a.product_dept,a.team_leader,a.dealing_marchant from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 $company_2 $buye_2 $style_id_cond_2 $style_cond_2 $estimated_shipdate order by b.id";
	
		echo  create_list_view("list_view", "Order No,Company,Buyer Name,Style Name,Product Department,Team Leader,Dealing Merchant", "130,140,140,100,90,90,90","900","240",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_name,0,product_dept,team_leader,dealing_marchant,0", $arr , "po_number,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant", "",'','0,0,0,0,0,0,0,0') ;
	
	}
	else
	{// 3=Sample Wothout Order
	$sql= "select id,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant,article_no from sample_development_mst where status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate order by id";
	
		echo  create_list_view("list_view", "Style Id,Company,Buyer Name,Style Name,Product Department,Team Leader,Dealing Merchant,Article Number", "60,140,140,100,90,90,90,70","900","240",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_name,0,product_dept,team_leader,dealing_marchant,0", $arr , "id,company_id,buyer_name,style_ref_no,product_dept,team_leader,dealing_marchant,article_no", "",'','0,0,0,0,0,0,0,0') ;
	
	}
		

	exit();
}//end action...............;


if($action=="show_sample_item_listview")
{
?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th>Garmengts Item Name</th>
            <th width="120">Sample Name</th>
            <th width="60">Sample Qty</th>                    
        </thead>
		<?  
		$i=1;
		
		$sqlResult = sql_select("select a.id,a.item_name,b.sample_name,sum(c.size_qty) as size_qty from sample_development_mst a,sample_development_dtls b, sample_development_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and a.id=$data group by b.sample_name,a.item_name,a.id"); 
		
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_sample_item_data(<? echo $row[csf('id')];?>,<? echo $row[csf('sample_name')]; ?>,<? echo $data; ?>);"> 
				<td><? echo $i; ?></td>
				<td><p><? echo $garments_item[$row[csf('item_name')]]; ?></p></td>
				<td><p><? echo $sample_name_library[$row[csf('sample_name')]]; ?></p></td>
				<td align="right"><?php  echo $row[csf('size_qty')]; ?></td>
			</tr>
		<?	
			$i++;
		}
		?>
	</table>
	<?
	exit();
}//end action...............;


if($action=="show_dtls_listview")
{
	list($smp_id,$mst_id)=explode('*',$data);
	if($mst_id)$sql_con="sample_ex_factory_mst_id=$mst_id"; else $sql_con="sample_development_id=$smp_id";
?>	 
 <fieldset style="overflow:hidden; margin:5px 0;">
     <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Dev. Sample ID</th>
                <th width="110">Sample Name</th>
                <th width="80">Ex-Factory Qnty</th>
                <th width="80">Carton Qnty</th>
                <th width="100">Invoice No</th>
                <th width="150">LC/SC No</th> 
                <th width="80">Qnty/ Ctn</th>
                <th width="100">Shiping Status</th>
                <th>Remarks</th>
            </thead>
		</table>
	</div>
	<div style="width:100%; max-height:180px; overflow:y-scroll" id="sewing_production_list_view">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
		<?php 
		
		//echo "select id, sample_ex_factory_mst_id, sample_development_id, sample_name, ex_factory_qty, carton_qty, invoice_no, lc_sc_no, carton_per_qty, remarks, shiping_status from sample_ex_factory_dtls where $sql_con and status_active=1 and is_deleted=0";
		
		
		 
			$i=1;
			$sqlResult =sql_select("select id, order_type,sample_ex_factory_mst_id, sample_development_id, sample_name, ex_factory_qty, carton_qty, invoice_no, lc_sc_no, carton_per_qty, remarks, shiping_status from sample_ex_factory_dtls where $sql_con and status_active=1 and is_deleted=0");
			foreach($sqlResult as $row){
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				
  		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('sample_development_id')].'**'.$row[csf('sample_ex_factory_mst_id')].'**'.$row[csf('id')].'**'.$row[csf('order_type')]; ?>','populate_input_form_data','requires/sample_ex_factory_controller');" > 
				<td width="30" align="center"><? echo $i; ?></td>
                <td width="80"><? echo $row[csf('sample_development_id')]; ?></td>
                <td width="110"><p><? echo $sample_name_library[$row[csf('sample_name')]]; ?></p></td>
                <td width="80" align="right"><?php echo $row[csf('ex_factory_qty')]; ?></td>
                <td align="right" width="80"><?php echo $row[csf('carton_qty')]; ?></td>
                <td width="100"><?php echo $row[csf('invoice_no')]; ?></td>
                <td width="150"><?php echo $row[csf('lc_sc_no')]; ?></td>
                <td align="right" width="80"><? echo $row[csf('carton_per_qty')]; ?></td>
                <td width="100">
					<? 
						if($row[csf('shiping_status')]==1) echo "Full Shipment";
						if($row[csf('shiping_status')]==2) echo "Partial Shipment";
						if($row[csf('shiping_status')]==3) echo "Full Painding";
                    ?>
                </td>
                <td><p><? echo $row[csf('remarks')]; ?></p></td>
			</tr>
			<?php
			$i++;
			}
			?>
		</table>
    </div>
</fieldset>


<?
	exit();	
}//end action...............;


if($action=="populate_input_form_data")
{
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	list($smp_id,$mst_id,$dtls_id,$order_type)=explode('**',$data);
		
	//Save data........................

		$colorResult = sql_select("select a.id,a.order_type,a.sample_development_id,a.sample_name, a.ex_factory_qty, a.carton_qty, a.invoice_no, a.lc_sc_id, a.lc_sc_no, a.carton_per_qty, a.remarks, a.shiping_status,b.color_id as sample_color,b.size_id,b.size_pass_qty as size_qty from sample_ex_factory_dtls a, sample_ex_factory_colorsize b where a.id=b.sample_ex_factory_dtls_id and a.sample_ex_factory_mst_id = $mst_id and b.sample_ex_factory_mst_id = $mst_id  and a.status_active=1 and a.is_deleted=0"); 
 		
		
	foreach($colorResult as $row)
	{	
		if($row[csf("sample_development_id")]){
			$colorTotal[$row[csf("id")]][$row[csf("sample_color")]]+=$row[csf("size_qty")];	
			$colorData[$row[csf("id")]][$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];	
			
			$sizeQcPassQty[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];	
			$totSizeQcPassQty+=$row[csf("size_qty")];	

			$dtlsArr[$row[csf("id")]]['order_type']=$row[csf('order_type')];
			$dtlsArr[$row[csf("id")]]['sample_development_id']=$row[csf('sample_development_id')];
			$dtlsArr[$row[csf("id")]]['sample_name']=$row[csf('sample_name')];
			$dtlsArr[$row[csf("id")]]['ex_factory_qty']=$row[csf('ex_factory_qty')];
			$dtlsArr[$row[csf("id")]]['carton_qty']=$row[csf('carton_qty')];
			$dtlsArr[$row[csf("id")]]['invoice_no']=$row[csf('invoice_no')];
			$dtlsArr[$row[csf("id")]]['lc_sc_id']=$row[csf('lc_sc_id')];
			$dtlsArr[$row[csf("id")]]['lc_sc_no']=$row[csf('lc_sc_no')];
			$dtlsArr[$row[csf("id")]]['carton_per_qty']=$row[csf('carton_per_qty')];
			$dtlsArr[$row[csf("id")]]['remarks']=$row[csf('remarks')];
			$dtlsArr[$row[csf("id")]]['shiping_status']=$row[csf('shiping_status')];
		}
	}
		
	
	
	
	
		$result_inv=sql_select("select id,invoice_no from com_export_invoice_ship_mst where id =".$dtlsArr[$dtls_id]['invoice_no']."");	
		foreach($result_inv as $row){
		$inv_data[$row[csf('id')]]=$row[csf('invoice_no')];
		}
		
		
		echo "$('#dtls_update_id').val('".$dtls_id."');\n";
		echo "$('#cbo_order_type').val('".$dtlsArr[$dtls_id]['order_type']."');\n";
		if($order_type==2)
		{
			$po_number=return_field_value("po_number","wo_po_break_down","id='".$dtlsArr[$dtls_id]['sample_development_id']."'","po_number");
			echo "$('#txt_development_sample_id').val('".$po_number."');\n";
		}
		else
		{
			echo "$('#txt_development_sample_id').val('".$dtlsArr[$dtls_id]['sample_development_id']."');\n";
		}
		echo "$('#txt_development_sample_id').attr('placeholder',".$dtlsArr[$dtls_id]['sample_development_id'].");\n";
		echo "$('#txt_sample_name').val('".$dtlsArr[$dtls_id]['sample_name']."');\n";
		echo "$('#txt_ex_factory_qty').val('".$dtlsArr[$dtls_id]['ex_factory_qty']."');\n";
		echo "$('#txt_total_carton_qnty').val('".$dtlsArr[$dtls_id]['carton_qty']."');\n";
		echo "$('#txt_invoice_no').val('".$inv_data[$dtlsArr[$dtls_id]['invoice_no']]."');\n";
		echo "$('#txt_invoice_no').attr('placeholder','".$dtlsArr[$dtls_id]['invoice_no']."');\n";
		echo "$('#txt_lc_sc_no').val('".$dtlsArr[$dtls_id]['lc_sc_no']."');\n";
		echo "$('#txt_lc_sc_no').attr('placeholder','".$dtlsArr[$dtls_id]['lc_sc_id']."');\n";
		echo "$('#txt_carton_per_qnty').val('".$dtlsArr[$dtls_id]['carton_per_qty']."');\n";
		echo "$('#txt_remark').val('".$dtlsArr[$dtls_id]['remarks']."');\n";
		echo "$('#cbo_shipping_status').val('".$dtlsArr[$dtls_id]['shiping_status']."');\n";
	
	
		
	if($order_type==3){
		//New data........................;
		$sqlResult = sql_select("select a.sample_color,b.size_id,b.size_qty from sample_development_dtls a, sample_development_size b where a.id=b.dtls_id and a.sample_mst_id=$smp_id and a.sample_name=".$dtlsArr[$dtls_id]['sample_name'].""); 
 		foreach($sqlResult as $row)
		{
		$smp_qty_arr[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];	
		$total_sew_qty+=$row[csf("size_qty")];	
		}
	}
	else
	{
		// new data;
		$colorResult = sql_select("select b.color_number_id,b.size_number_id,b.order_quantity from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.id=".$dtlsArr[$dtls_id]['sample_development_id']."  and a.status_active=1 and b.is_deleted=0"); 
 		foreach($colorResult as $row)
		{
		$smp_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf("order_quantity")];	
		$total_sew_qty+=$row[csf("order_quantity")];	
		}
	}
	

		
		
		foreach($colorData[$dtls_id] as $color_id=>$color_value)
		{
			$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'">'.$colorTotal[$dtls_id][$color_id].'</span> </h3>';
			$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
			$i=1;
			foreach($color_value as $size_id=>$size_qty)
			{
				$colorID .= $color_id."*".$size_id.",";
				
				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:80px" value="'.$size_qty.'" placeholder="'.($smp_qty_arr[$color_id][$size_id]-($sizeQcPassQty[$color_id][$size_id]-$size_qty)).'" onblur="fn_total('.$color_id.','.$i.')"></td></tr>';				
			$i++;
			}
			$colorHTML .= "</table></div>";
		
		}
		
		echo "$('#txt_sewing_qnty').val(".$total_sew_qty.");\n";
		echo "$('#txt_cumul_ex_factory_qty').val(".$totSizeQcPassQty.");\n";
		echo "$('#txt_yet_quantity').val(".($total_sew_qty-$totSizeQcPassQty).");\n";
		
		
		echo "set_button_status(1, permission, 'fnc_exFactory_entry',1,0);\n";
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	
	
	exit();		
}//end action...............;



if($action=="color_and_size_level")
{
		list($mst_id,$smp_id,$smp_dev_id)=explode('**',$data);
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		
		//Save data;
		$colorResult = sql_select("
		select 
			b.sample_name,c.color_id,c.size_id,c.size_pass_qty 	
		from 
			sample_ex_factory_mst a, 
			sample_ex_factory_dtls b,
			sample_ex_factory_colorsize c
		where 
			a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_development_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"); 
 		foreach($colorResult as $row)
		{
		$qcPassQtyArr[$row[csf("sample_name")]][$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];	
		$totQcPassQty+=$row[csf("size_pass_qty")];	
		}
		
		
		// new data;
		$colorResult = sql_select("select a.sample_color,b.size_id,b.size_qty from sample_development_dtls a, sample_development_size b where a.id=b.dtls_id and a.sample_mst_id=$mst_id and a.sample_name=$smp_id  and a.status_active=1 and a.is_deleted=0"); 
 		foreach($colorResult as $row)
		{
		$colorData[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];	
		$total_sew_qty+=$row[csf("size_qty")];	
		}
		
		
		foreach($colorData as $color_id=>$color_value)
		{
			$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'"></span> </h3>';
			$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
			$i=1;
			foreach($color_value as $size_id=>$size_qty)
			{
				$colorID .= $color_id."*".$size_id.",";
				
				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'" class="text_boxes_numeric" style="width:80px" placeholder="'.($size_qty-$qcPassQtyArr[$smp_id][$color_id][$size_id]).'" onblur="fn_total('.$color_id.','.$i.')" /></td></tr>';				
			$i++;
			}
			$colorHTML .= "</table></div>";
		
		}
		
		
		echo "$('#txt_sewing_qnty').val(".$total_sew_qty.");\n";
		echo "$('#txt_cumul_ex_factory_qty').val(".$totQcPassQty.");\n";
		echo "$('#txt_yet_quantity').val(".($total_sew_qty-$totQcPassQty).");\n";
		echo "$('#dtls_update_id').val('');\n";
		echo "$('#txt_development_sample_id').val('".$smp_dev_id."');\n";
		echo "$('#txt_development_sample_id').attr('placeholder',".$smp_dev_id.");\n";

		echo "$('#txt_sample_name').val('".$smp_id."');\n";
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
  
  exit();
}//end action...............;



if($action=="color_and_size_level_with_order")
{
		list($mst_id,$smp_id,$smp_dev_id)=explode('**',$data);
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		
		//Save data;
		$colorResult = sql_select("
		select 
			b.sample_name,c.color_id,c.size_id,c.size_pass_qty 	
		from 
			sample_ex_factory_mst a, 
			sample_ex_factory_dtls b,
			sample_ex_factory_colorsize c
		where 
			a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_development_id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"); 
 		foreach($colorResult as $row)
		{
		$qcPassQtyArr[$row[csf("sample_name")]][$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];	
		$totQcPassQty+=$row[csf("size_pass_qty")];	
		}
		
		// new data;
		$colorResult = sql_select("select b.color_number_id,b.size_number_id,b.order_quantity from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.id=$mst_id  and a.status_active=1 and b.is_deleted=0"); 
 		foreach($colorResult as $row)
		{
		$colorData[$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf("order_quantity")];	
		$total_sew_qty+=$row[csf("order_quantity")];	
		}
		
		
		foreach($colorData as $color_id=>$color_value)
		{
			$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'"></span> </h3>';
			$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
			$i=1;
			foreach($color_value as $size_id=>$size_qty)
			{
				$colorID .= $color_id."*".$size_id.",";
				
				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'" class="text_boxes_numeric" style="width:80px" placeholder="'.($size_qty-$qcPassQtyArr[$smp_id][$color_id][$size_id]).'" onblur="fn_total('.$color_id.','.$i.')" /></td></tr>';				
			$i++;
			}
			$colorHTML .= "</table></div>";
		
		}
		
		
		echo "$('#txt_sewing_qnty').val(".$total_sew_qty.");\n";
		echo "$('#txt_cumul_ex_factory_qty').val(".$totQcPassQty.");\n";
		echo "$('#txt_yet_quantity').val(".($total_sew_qty-$totQcPassQty).");\n";
		echo "$('#dtls_update_id').val('');\n";
		
		echo "$('#txt_development_sample_id').val('".$mst_id."');\n";
		echo "$('#txt_development_sample_id').attr('placeholder',".$smp_dev_id.");\n";
		
		echo "$('#txt_sample_name').val('".$smp_id."');\n";
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
  
  exit();
}//end action...............;



if ($action=="lcsc_popup")
{
extract($_REQUEST);
echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
?>
<script>
function js_set_value(str)
{
	$("#lc_id_no").val(str);
	parent.emailwindow.hide();
	//parent.emailwindow.hide();
}
</script>

<?
	if($db_type==0)
	{	
 		$sql = "select a.id, a.invoice_no, a.invoice_date, a.buyer_id, a.lc_sc_id, sum(b.current_invoice_qnty) as order_quantity, group_concat(b.po_breakdown_id) as po_id, a.benificiary_id,a.is_lc from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id = b.mst_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0  group by a.id order by a.invoice_no"; 
	}
	else
	{
		$sql = "select a.id, a.invoice_no, max(a.invoice_date) as invoice_date, a.buyer_id, a.lc_sc_id, sum(b.current_invoice_qnty) as order_quantity, listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id, a.benificiary_id,a.is_lc from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id = b.mst_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 group by a.id,a.invoice_no,a.buyer_id,a.lc_sc_id,a.benificiary_id,a.is_lc order by a.invoice_no";
		
	}
	//echo $sql;die;
	
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($db_type==0)
	{
		$po_num_arr=return_library_array("select id, group_concat(distinct(po_number)) as po_number from wo_po_break_down where status_active=1 and is_deleted=0 ", "id", "po_number");
	}
	else
	{
		$po_num_arr=return_library_array("select id, listagg(CAST(po_number as VARCHAR(4000)),',') within group (order by po_number) as po_number from wo_po_break_down where status_active=1 and is_deleted=0 group by id", "id", "po_number");
	}

   ?>
  	<div style="width:870px; margin-top:10px">
     	<table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="120" >Invoice No</th>
                <th width="75" >Invoice Date</th>
                <th width="120" >Buyer</th>
                <th width="150" >LC/SC No</th>
                <th width="120" >Order No</th>
                <th width="120" >Order Qty</th>
                <th width="">Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:870px; max-height:320px;overflow-y:scroll;" >	 
        <table cellspacing="0" width="852" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$po_number=$po_num_arr[$row[csf("po_id")]];
				

				if($row[csf("is_lc")]==1) //  lc
				{
					$lc_sc = $lc_num_arr[$row[csf('lc_sc_id')]];
				}
				else
				{
					$lc_sc = $sc_num_arr[$row[csf('lc_sc_id')]];
				}
				 
 					?>
                    <input type="hidden" id="lc_id_no" name="lc_id_no">
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')];?>,<? echo $row[csf('invoice_no')];?>,<? echo $row[csf('lc_sc_id')]; ?>,<? echo $lc_sc;?>');" > 
							<td width="30" align="center"><? echo $i; ?></td>
                            <td width="120" align="left"><p><? echo $row[csf("invoice_no")]; ?></p></td>
							<td width="75" align="center"><? echo change_date_format($row[csf("invoice_date")]);?></td>		
 							<td width="120"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>	
							<td width="150"><p><? echo $lc_sc; ?></p></td>
                            <td width="120"><p><? echo $po_number; ?></p></td>
							<td width="120" align="right"><? echo $row[csf("order_quantity")];?> </td>	
 							<td width=""><p><?  echo $company_arr[$row[csf("benificiary_id")]];?></p></td> 	
						</tr>
					<? 
					$i++;
             }
   		?>
			</table>
            <script>setFilterGrid("tbl_invoice_list",-1);</script>
		</div> 
	  <?  
exit();	
}//end action...............;

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0) // Insert part----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		$flag=1;
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		
		if($mst_update_id=='')
		{
		// master part--------------------------------------------------------------;
			
			$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GDS', date("Y",time()), 5, "select a.sys_number_prefix,a.sys_number_prefix_num from sample_ex_factory_mst a where a.company_id=$cbo_company_name and $mrr_date_check =".date('Y',time())." order by a.id DESC", "sys_number_prefix", "sys_number_prefix_num" ));			
			
			
			$mst_id=return_next_id("id", "sample_ex_factory_mst", 1);
			$field_array_mst="id,sys_number_prefix,sys_number_prefix_num,sys_number, company_id, location, delivery_to, ex_factory_date, transport_company_id, truck_no, lock_no, driver_name, dl_no, mobile_no, do_no, gp_no, final_destination, forwarder, dipo_name, inserted_by, insert_date, status_active, is_deleted";
			$data_array_mst="(".$mst_id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$txt_delivery_to.",".$txt_ex_factory_date.",".$cbo_transport_company.",".$txt_truck_no.",".$txt_lock_no.",".$txt_driver_name.",".$txt_dl_no.",".$txt_mobile_no.",".$txt_do_no.",".$txt_gp_no.",".$txt_final_destination.",".$cbo_forwarder.",".$txt_dipo_name.",".$user_id.",'".$pc_date_time."','1','0')";
		
		// Details part--------------------------------------------------------------;
			$dtls_id=return_next_id("id", "sample_ex_factory_dtls", 1);
			$field_array_dtls="id, sample_ex_factory_mst_id,order_type, sample_development_id, sample_name, ex_factory_qty, carton_qty, invoice_no, lc_sc_id,lc_sc_no, carton_per_qty, remarks, shiping_status, inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls="(".$dtls_id.",".$mst_id.",".$cbo_order_type.",".$txt_development_sample_id.",'".$sample_name."',".$txt_ex_factory_qty.",".$txt_total_carton_qnty.",'".$invoice_id."','".$lcsc_id."',".$txt_lc_sc_no.",".$txt_carton_per_qnty.",".$txt_remark.",".$cbo_shipping_status.",".$user_id.",'".$pc_date_time."','1','0')";
	
		// Color & Size Breakdown part--------------------------------------------------------------;
		$field_array_brk="id, sample_ex_factory_mst_id, sample_ex_factory_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted";
		$colorsize_brk_id=return_next_id("id", "sample_ex_factory_colorsize", 1);
			
			
			// size quantity value;
			$rowEx = explode("***",$colorIDvalue); 
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];				
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $colorID.$sizeID;
	
				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0')";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0')";
				$colorsize_brk_id+=1;
				$j++;
			}
		
			
			//insert here----------------------------------------;
			$rID_mst=sql_insert("sample_ex_factory_mst",$field_array_mst,$data_array_mst,0);
			if($flag==1) 
			{
				if($rID_mst) $flag=1; else $flag=0; 
			} 
			
			//$rID_dtls=execute_query("insert into sample_ex_factory_dtls ($field_array_dtls) values $data_array_dtls");
			$rID_dtls=sql_insert("sample_ex_factory_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rID_dtls) $flag=1; else $flag=0; 
			} 
	
			$rID_brk=sql_insert("sample_ex_factory_colorsize",$field_array_brk,$data_array_brk,0);

			if($flag==1) 
			{
				if($rID_brk) $flag=1; else $flag=0; 
			} 
	
	
	//echo $data_array_dtls;
	 //echo $rID_mst.','.$rID_dtls.','.$rID_brk; mysql_query("ROLLBACK");die;
			
			
			
			//ROLLBACK/COMMIT here----------------------------------------;
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");  
					echo "0**".$mst_id."**".$txt_development_sample_id."**".$new_mrr_number[0];
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);  
					echo "0**".$mst_id."**".$txt_development_sample_id."**".$new_mrr_number[0];
				}
				else
				{
					oci_rollback($con);
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			
		}
		else
		{
		// Details part--------------------------------------------------------------;
	
			$dtls_id=return_next_id("id", "sample_ex_factory_dtls", 1);
			$field_array_dtls="id, sample_ex_factory_mst_id,order_type, sample_development_id, sample_name, ex_factory_qty, carton_qty, invoice_no, lc_sc_id, lc_sc_no,carton_per_qty, remarks, shiping_status, inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls="(".$dtls_id.",".$mst_update_id.",".$cbo_order_type.",".$txt_development_sample_id.",'".$sample_name."',".$txt_ex_factory_qty.",".$txt_total_carton_qnty.",'".$invoice_id."','".$lcsc_id."',".$txt_lc_sc_no.",".$txt_carton_per_qnty.",".$txt_remark.",".$cbo_shipping_status.",".$user_id.",'".$pc_date_time."','1','0')";
		
		// Color & Size Breakdown part--------------------------------------------------------------;
		$field_array_brk="id, sample_ex_factory_mst_id, sample_ex_factory_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted";
		$colorsize_brk_id=return_next_id("id", "sample_ex_factory_colorsize", 1);
			
			// size quantity value;
			$rowEx = explode("***",$colorIDvalue); 
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];				
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
	
				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0')";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0')";
				$colorsize_brk_id+=1;
				$j++;
			}
		
			
			$rID_dtls=sql_insert("sample_ex_factory_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rID_dtls) $flag=1; else $flag=0; 
			} 
	
			$rID_brk=sql_insert("sample_ex_factory_colorsize",$field_array_brk,$data_array_brk,0);
			if($flag==1) 
			{
				if($rID_brk) $flag=1; else $flag=0; 
			} 
			
	//echo $rID_dtls.','.$rID_brk; mysql_query("ROLLBACK");die;
	
			//ROLLBACK/COMMIT here----------------------------------------;
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");  
					echo "0**".$mst_update_id."**".$txt_development_sample_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);  
					echo "0**".$mst_update_id."**".$txt_development_sample_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			
		}
					
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update part ------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		
		if($mst_update_id!='')
		{
			// master part--------------------------------------------------------------;
			$field_array_mst="company_id*location*delivery_to*ex_factory_date*transport_company_id*truck_no*lock_no* driver_name*dl_no*mobile_no*do_no*gp_no*final_destination*forwarder*dipo_name*updated_by*update_date";
			$data_array_mst="".$cbo_company_name."*".$cbo_location_name."*".$txt_delivery_to."*".$txt_ex_factory_date."*".$cbo_transport_company."*".$txt_truck_no."*".$txt_lock_no."*".$txt_driver_name."*".$txt_dl_no."*".$txt_mobile_no."*".$txt_do_no."*".$txt_gp_no."*".$txt_final_destination."*".$cbo_forwarder."*".$txt_dipo_name."*".$user_id."*'".$pc_date_time."'";
			$rID_mst=sql_update("sample_ex_factory_mst",$field_array_mst,$data_array_mst,"id","".$mst_update_id."",1);
			
			
		// Dtls part--------------------------------------------------------------;
			
			/*$txt_development_sample_id=str_replace("'","",$txt_development_sample_id);
			$sample_name=str_replace("'","",$sample_name);
			$txt_ex_factory_qty=str_replace("'","",$txt_ex_factory_qty);
			$txt_total_carton_qnty=str_replace("'","",$txt_total_carton_qnty);
			$invoice_id=str_replace("'","",$invoice_id);
			$lcsc_id=str_replace("'","",$lcsc_id);
			$txt_carton_per_qnty=str_replace("'","",$txt_carton_per_qnty);
			$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);*/			
			
			$field_array_dtls="order_type*sample_development_id*sample_name*ex_factory_qty*carton_qty* invoice_no*lc_sc_id*lc_sc_no*carton_per_qty*remarks*shiping_status*updated_by*update_date";
			$data_array_dtls="".$cbo_order_type."*".$txt_development_sample_id."*".$sample_name."*".$txt_ex_factory_qty."*".$txt_total_carton_qnty."*'".$invoice_id."'*'".$lcsc_id."'*".$txt_lc_sc_no."*".$txt_carton_per_qnty."*".$txt_remark."*".$cbo_shipping_status."*".$user_id."*'".$pc_date_time."'";
			$rID_dtls=sql_update("sample_ex_factory_dtls",$field_array_dtls,$data_array_dtls,"id","".$dtls_update_id."",1);
	
		// Color & Size Breakdown part--------------------------------------------------------------;
		if($rID_mst && $rID_dtls){
		$rID_brk_delete = execute_query("DELETE from sample_ex_factory_colorsize WHERE sample_ex_factory_dtls_id=$dtls_update_id");//Delete fast;
		}
		
		$field_array_brk="id, sample_ex_factory_mst_id, sample_ex_factory_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted";
		$colorsize_brk_id=return_next_id("id", "sample_ex_factory_colorsize", 1);
			
			// size quantity value;
			$rowEx = explode("***",$colorIDvalue); 
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];				
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
	
				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0')";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0')";
				$colorsize_brk_id+=1;
				$j++;
			}
			
			$rID_brk=sql_insert("sample_ex_factory_colorsize",$field_array_brk,$data_array_brk,0);
		
		
		//echo $data_array_dtls;
		//$rID_mst.','.$rID_dtls.','.$rID_brk; mysql_query("ROLLBACK");die;
		//-------------------------------------------------------------------------------------------	
			if($db_type==0)
			{
				if($rID_mst && $rID_dtls && $rID_brk_delete && $rID_brk)
				{
					mysql_query("COMMIT");  
					echo "1**".$mst_update_id."**".$txt_development_sample_id."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$mst_update_id."**".$txt_development_sample_id."**0";
				}
			
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID_mst && $rID_dtls && $rID_brk_delete && $rID_brk)
				{
					oci_commit($con);  
					echo "1**".$mst_update_id."**".$txt_development_sample_id."**0";
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mst_update_id."**".$txt_development_sample_id."**0";
				}
			}
		}
		disconnect($con);
		die;
	}
 
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
 		
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);


 		$rID = sql_delete("sample_ex_factory_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'sample_ex_factory_mst_id  ',$mst_update_id,1);
		$dtlsrID = sql_delete("sample_ex_factory_colorsize","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'sample_ex_factory_mst_id',$mst_update_id,1);

 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "2**".$mst_update_id; 
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_update_id; 
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$mst_update_id; 
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_update_id; 
			}
		}
		disconnect($con);
		die;
	}

}//end action...............;



if($action=="create_delivery_search_list")
{
	
$buyer_name_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');
	
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$exfact_qty_arr=return_library_array( "select sample_ex_factory_mst_id, sum(ex_factory_qty) as ex_factory_qty from sample_ex_factory_dtls where status_active=1  group by sample_ex_factory_mst_id",'sample_ex_factory_mst_id','ex_factory_qty');	

$trans_com_arr=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id","supplier_name");

$smp_id_arr=return_library_array( "select sample_ex_factory_mst_id,sample_development_id from sample_ex_factory_dtls where status_active=1 group by sample_ex_factory_mst_id,sample_development_id",'sample_ex_factory_mst_id','sample_development_id');	
	
	
	$ex_data = explode("_",$data);
	$trans_com = $ex_data[0];
	$txt_delivery_to = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$po_no = str_replace("'","",$ex_data[5]);
	//echo $trans_com;die;
	$sql_cond="";
	if($txt_date_from!="" || $txt_date_to!="") 
	{
		if($db_type==0){$sql_cond .= " and ex_factory_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and ex_factory_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}
	
	if(trim($company)!="") $sql_cond .= " and company_id='$company'";
	if(trim($trans_com)!=0) $sql_cond .= " and transport_company_id='$trans_com'";
	if(trim($txt_delivery_to)!=0) $sql_cond .= " and delivery_to='$txt_delivery_to'";


	$sql = "select id,sys_number, company_id, location, delivery_to, ex_factory_date, transport_company_id, truck_no, lock_no, driver_name, dl_no, mobile_no, do_no, gp_no, final_destination, forwarder, dipo_name from  sample_ex_factory_mst where status_active=1 and is_deleted=0 $sql_cond";
	$result = sql_select($sql);
   ?>
     	<table cellspacing="0" width="1030" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="37" >SL</th>
                <th width="100" >Sys Num</th>
                <th width="70" >Buyer Name</th>
                <th width="155" >Transport Company</th>
                <th width="100" >Ex-fact Date</th>
                <th width="120" >Driver Name</th>
                <th width="90" >Truck No</th>
                <th width="90">Lock No</th>
                <th>Ex-fact Qty</th>
            </thead>
     	</table>
     <div style="width:1030px; max-height:220px;overflow-y:scroll;" >	 
        <table cellspacing="0" width="1012" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $smp_id_arr[$row[csf('id')]];?>,<? echo $row[csf('id')];?>);" > 
                    <td width="35" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><p><? echo $row[csf("sys_number")]; ?></p></td>
                    <td width="70"><p><? echo $buyer_name_arr[$row[csf("delivery_to")]]; ?>&nbsp;</p></td>
                    <td width="155" align="center"><p><? echo $trans_com_arr[$row[csf("transport_company_id")]];?>&nbsp;</p></td>		
                    <td width="100" align="center"><p><? echo change_date_format($row[csf("ex_factory_date")]); ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row[csf("driver_name")]; ?>&nbsp;</p></td>
                    <td width="90"><p><? echo $row[csf("truck_no")];?>&nbsp;</p></td>	
                    <td width="90"><p><?  echo $row[csf("lock_no")];?>&nbsp;</p></td> 
                    <td align="right"><p><?  echo number_format($exfact_qty_arr[$row[csf("id")]],0,"","");?></p></td> 
                   	
                </tr>
				<? 
				$i++;
             }
   		?>
			</table>
		</div> 
	  <?  
exit();	

}//end action...............;


if($action=="ex_factory_print")
{
	extract($_REQUEST);
	list($mst_id,$company_name)=explode('*',$data);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_lib=return_library_array( "select id,location_name from lib_location where company_id='$company_name'", "id", "location_name"  );
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_name'","image_location");
	$supplier_lib=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	
	$country_lib=return_library_array( "select id,country_name from lib_country", "id","country_name"  );
	
	
	
	$mst_data=sql_select("select * from sample_ex_factory_mst where id=$mst_id and status_active=1");
	
	
		$sql="SELECT * from sample_ex_factory_dtls where sample_ex_factory_mst_id=$mst_id  and status_active=1 and is_deleted=0";
		$result=sql_select($sql);
		 foreach($result as $row){
			$data_arr[]=array(
				'sample_name'=>$row[csf('sample_name')],
				'sample_development_id'=>$row[csf('sample_development_id')],
				'invoice_no'=>$row[csf('invoice_no')],
				'ex_factory_qty'=>$row[csf('ex_factory_qty')],
				'carton_qty'=>$row[csf('carton_qty')],
				'remarks'=>$row[csf('remarks')]
				);
			$smp_id_arr[]=$row[csf('sample_name')]; 
			$inv_id_arr[]=$row[csf('invoice_no')]; 
			 
		 }
	
	$smp_id= implode(',',$smp_id_arr);	
	$inv_id= implode(',',$inv_id_arr);	
	

	$result_inv=sql_select("select id,invoice_no,country_id from com_export_invoice_ship_mst where id in($inv_id)");	
	foreach($result_inv as $row){
		$inv_data[$row[csf('id')]]=$row[csf('invoice_no')];
		$cun_data[$row[csf('id')]]=$country_lib[$row[csf('country_id')]];
	}
	
	
	$result_smp=sql_select("select b.sample_name,a.buyer_name,a.style_ref_no,a.item_name from sample_development_mst a,sample_development_dtls b where a.company_id=$company_name and b.sample_name in($smp_id) group by a.buyer_name,a.style_ref_no,a.item_name,b.sample_name");	
	foreach($result_smp as $row){
		$buy_data[$row[csf('sample_name')]]=$buyer_lib[$row[csf('buyer_name')]];
		$sty_data[$row[csf('sample_name')]]=$row[csf('style_ref_no')];
		$item_data[$row[csf('sample_name')]]=$garments_item[$row[csf('item_name')]];
	}



	
?>


<div style="width:800px; border:1px solid #fff;">
    <table width="100%" cellspacing="0" align="right" cellpadding="10">
        <tr>
            <td colspan="6" align="center" valign="middle">
                <img src="../<? echo $image_location; ?>" height="50" width="60" style="float:left;">
                <strong style=" font-size:30px;"><? echo $company_library[$company_name]; ?></strong>
            </td>
        </tr>
        <tr>
        	<td colspan="6" align="center" style="font-size:14px;">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_name"); 
					foreach ($nameArray as $result)
					{ 
					?>
						<? if($result[csf('plot_no')]!="") echo $result[csf('plot_no')].", "; ?> 
						<? if($result[csf('level_no')]!="") echo $result[csf('level_no')].", ";?>
						<? if($result[csf('road_no')]!="") echo $result[csf('road_no')].", "; ?> 
						<? if($result[csf('block_no')]!="") echo $result[csf('block_no')].", ";?>
						<? if($result[csf('city')]!="") echo $result[csf('city')].", ";?>
						<? if($result[csf('zip_code')]!="") echo $result[csf('zip_code')].", "; ?> 
						<? if($result[csf('province')]!="") echo $result[csf('province')];?> 
						<? if($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]].", "; ?><br> 
						<? if($result[csf('email')]!="") echo $result[csf('email')].", ";?> 
						<? if($result[csf('website')]!="") echo $result[csf('website')]; 
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center"><strong>100% Export Oriented</strong></td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:x-large;" align="center"><strong>Sample Delivery Challan</strong></td>
        </tr>
        <tr >
        	<td width="90"><strong>Challan No</strong></td> 
            <td width="200">: <? echo $mst_id; ?></td>
            <td width="100"><strong>Date</strong></td>
            <td width="100">: <? echo change_date_format($mst_data[0][csf('ex_factory_date')]); ?></td>
            <td width="100"><strong>Driver Name</strong></td>
            <td>: <? echo $mst_data[0][csf('driver_name')]; ?></td>
        </tr>
			
        <tr >
        	<td><strong>C & F Name</strong></td> 
            <td>: <? echo $mst_id; ?></td>
            <td><strong>Mobile</strong></td>
            <td>: <? echo $mst_data[0][csf('mobile_no')]; ?></td>
            <td><strong>Do No</strong></td>
            <td>: <? echo $mst_data[0][csf('do_no')]; ?></td>
        </tr>
			
        <tr >
        	<td><strong>Address</strong></td> 
            <td>: <? echo $location_lib[$mst_data[0][csf('location')]]; ?></td>
            <td><strong>DL No</strong></td>
            <td>: <? echo $mst_data[0][csf('dl_no')]; ?></td>
            <td><strong>GP No</strong></td>
            <td>: <? echo $mst_data[0][csf('gp_no')]; ?></td>
        </tr>
			
        <tr >
        	<td><strong>Trns.Comp.</strong></td> 
            <td>: <? echo $supplier_lib[$mst_data[0][csf('transport_company_id')]]; ?></td>
            <td><strong>Truck No</strong></td>
            <td>: <? echo $mst_data[0][csf('truck_no')]; ?></td>
            <td><strong>Lock No</strong></td>
            <td>: <? echo $mst_data[0][csf('lock_no')]; ?></td>
        </tr>
        <tr >
        	<td><strong>Forwarder</strong></td> 
            <td>: <? echo $supplier_lib[$mst_data[0][csf('forwarder')]]; ?></td>
            <td><strong>Depo Name</strong></td>
            <td>: <? echo $mst_data[0][csf('dipo_name')]; ?></td>
            <td><strong>Final Desti.</strong></td>
            <td>: <? echo $mst_data[0][csf('final_destination')]; ?></td>
        </tr>
			
    </table>
         
	<div style="width:100%;">
    <table align="right" cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="100" >Buyer</th>
            <th width="120">Style Ref.</th>
            <th width="80">Sample ID</th>
            <th width="80">S. Type</th>
            <th width="120">Item Name</th>
            <th width="50">Invoice No</th>
            <th width="60" >Delivery Qty</th>
            <th width="50">NO Of Carton</th>
            <th>Remarks</th>
        </thead>
        <tbody>
		<?
        $i=1;
        foreach($data_arr as $row)
        {
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $buy_data[$row['sample_name']]; ?></td>
                <td><? echo $sty_data[$row['sample_name']]; ?></td>
                <td><? echo $row['sample_development_id']; ?></td>
                <td><? echo $sample_name_library[$row['sample_name']]; ?></td>
                <td><? echo $item_data[$row['sample_name']]; ?></td>
                <td><? echo $inv_data[$row['invoice_no']]; ?></td>
                <td align="right"><? echo $row['ex_factory_qty']; ?></td>
                <td align="right"><? echo $row['carton_qty']; ?></td>
                <td><? echo $row['remarks']; ?></td>
            </tr>
            <?
            $i++;
        }
        ?>
        </tbody>
        
        <tr>
            <td colspan="7" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
            <td align="right"><? echo number_format($tot_qnty,0,"",""); ?></td>
            <td></td>
        </tr>
    </table>
	</div>
		 <?
            echo signature_table(77, $company_name, "810px");
         ?>
	</div>
<?
exit();	
}//end action...............;




//------------------------------------------***************---------------------------------------









?>