<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "" );   	 
} 


if ($action=="chalan_print_list_view")
{
	//extract($_REQUEST);
	echo load_html_head_contents("Sub-Contract Order Info", "../../", 1,1, $unicode,1,'');
?>
 	<script>
	
	</script>
</head>
<body>
	<form name="challan_2"  id="challan_2">
	<div style="width:100%;">
	  <input type="button" id="show_button" align="right" class="formbutton" style="width:100px" value="Print" onClick="new_window()" />
	  <table cellspacing="0" width="100%" class="rpt_table" id="">
			  <thead>
					<th width="50">SL</th>
                    <th width="200" align="center">Delivery Item</th>
                    <th width="100" align="center">Process</th>
					<th width="100" align="center">Delivery Date</th>
					<th width="100" align="center">Delivery Qnty</th>                    
					<th width="100" align="center">Challan No</th>
					<th width="100" align="center">Transport Company</th>
                    <th width="" align="center">Forwarder</th>  
				</thead>
         </table>
     </div>
    <div style="width:100%;overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" width="100%" class="rpt_table" id="chalan_table">
		<?php  
			$i=1;
			$party_arr=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
				
			$data=explode('_',$data);
			if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
			if ($data[1]!=0) $buyer=" and party_id='$data[1]'"; else { echo "Please Select Buyer First."; die; }
			if ($data[2]!=0) $process=" and process_id='$data[2]'"; else $process="";
			if ($data[3]!="" &&  $data[4]!="") $order_delivery_date = "and delivery_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $order_delivery_date ="";
		
			$sql = sql_select("select id,process_id,item_id,delivery_date,delivery_qnty,packing_qnty,chalan_no,transport_company,forwarder_id from subcon_delivery where status_active=1 $company $buyer $process $order_delivery_date"); 	
			
			foreach($sql as $row)
			{
				if ($i%2==0)  
                	$bgcolor="#E9F3FF";
                else
               	 	$bgcolor="#FFFFFF";
				?>
			<tr id="tr_<?  echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('chalan_no')]; ?>')" > 
				<td width="50" align="center"><? echo $i; ?></td>
                <?
					$process_id_val=$row[csf('process_id')];
					//$production_process=array(1=>"Cutting",2=>"Knitting",3=>"Dyeing",4=>"Finishing",5=>"Sewing",6=>"Fabric Printing",7=>"Washing",8=>"Printing",9=>"Embroidery");
					if($process_id_val==1 || $process_id_val==5)
					{
						$item_id_arr=$garments_item;
					}
					else
					{
						$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
					}
				?>
                <td width="200" align="center"><p><? echo $item_id_arr[$row[csf('item_id')]]; ?></p></td>
                <td width="100" align="center"><? echo $production_process[$row[csf('process_id')]]; ?></td>
                <td width="100" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                <td width="100" align="center"><? echo $row[csf('delivery_qnty')]; ?></td>
                <td width="100" align="center"><p><? echo $row[csf('chalan_no')]; ?></p></td>
                <td width="100" align="center"><p><? echo $row[csf('transport_company')]; ?></p></td>
                <td width="" align="center"><p><? echo $party_arr[$row[csf('forwarder_id')]]; ?></p></td>
			</tr>
			<?php
			$i++;
			}
			?>
            
		</table>
       </div>
       <table>
    	<tr align="center">
            <td align="center" valign="top">&nbsp;</td>
       </tr>
   	  </table> 
</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
	<?	
} 


if ($action=="chalan_print_window")
{
    extract($_REQUEST);
	echo load_html_head_contents("Sub-Contract Delivery Challan", "../../", 1,1, $unicode,1,'');
	$data=explode('_',$data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location=return_field_value("location_name","lib_location","company_id=$data[0]");
	$address=return_field_value("address","lib_location","company_id=$data[0]");
	
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	$party_address=return_field_value("address_1","lib_buyer","id=$data[1]");
?>
	<script>
	
	</script>
</head>
<body>
		<div id="table_row" style="width:900px;">
        <?
		$party_arr=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
        $sql_hed = sql_select("select id,delivery_date,chalan_no,transport_company,forwarder_id from subcon_delivery where id in($data[5])"); 
		foreach ($sql_hed as $row)
		{
			//if($chalan=="") $chalan=$row[csf("chalan_no")]; else $chalan.=",".$row[csf("chalan_no")];
			$chalan=$row[csf("chalan_no")];
			if($transport=="") $transport=$row[csf("transport_company")]; else $transport.=",".$row[csf("transport_company")];
			if($date=="") 
				$date=$row[csf("delivery_date")]; 
			else
			{ 
				if(strtotime($date)>strtotime($row[csf("delivery_date")]) )
					$date = $date;
				else
					$date = $row[csf("delivery_date")]; 	
			}
			if($forwarder=="") $forwarder=$party_arr[$row[csf("forwarder_id")]]; else $forwarder.=",".$party_arr[$row[csf("forwarder_id")]];
		}
		?>
		<table width="900">
				<tr class="form_caption">
					<td colspan="20" align="center"><h2><? echo $company_library[$data[0]]; ?></h2></td>
				</tr>
				<tr class="form_caption">
					<td colspan="20" align="center"><? echo $location.",".$address; ?></td>
				</tr>
                <tr>
                	<td>To</td>
                </tr>
                <tr>
                	<td><? echo $party_library[$data[1]]; ?></td>
                </tr>
                 <tr>
                	<td><? echo $party_address; ?></td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                </tr>
                <tr>
                	<td><strong>Challan No : </strong></td> <td width="300px"><? echo $chalan; ?></td>
                    <td align="right"><strong>Transport Company : </strong></td><td width="300px"> <? echo $transport; ?></td>
                </tr>
                 <tr>
                	<td><strong>Challan Date : </strong></td> <td width="300px"> <? echo $date; ?></td>
                    <td align="right"><strong>Forworder : </strong> </td>  <td width="300px"><? echo $forwarder; ?></td>
                </tr>
			</table>
           
            <br><br>
	<div style="width:100%;">
		<table border="1px" cellspacing="0" width="900" style="background-color:#CCCCCC; height:30px;" class="" id="approval_div">
				<thead >
					<th width="50">SL</th>
                    <th width="200" align="center">Item</th>
                    <th width="100" align="center">Order No</th>
					<th width="100" align="center">Process</th>
					<th width="150" align="center">Sub Process</th>                    
					<th width="100" align="center">Cust. Rrf</th>
                    <th width="100" align="center">UOM</th>  
                    <th width="" align="center">Qnty</th>
				</thead>
         </table>
     </div>
     <div style="width:100%;max-height:180px; overflow:y-scroll" id="scroll_body" align="left">
	 <table border="1px" cellspacing="0" width="900" class="" id="approval_div">
     <?
     $i=1;
			$sql = sql_select("select a.id,a.process_id,a.item_id,a.delivery_date,a.delivery_qnty,a.packing_qnty,a.chalan_no,a.transport_company,a.forwarder_id,b.order_no,b.process_id as sub_process,b.cust_buyer,b. 	cust_style_ref,b.order_uom from subcon_delivery a,subcon_ord_dtls b where a.order_id=b.id and a.id in($data[5])"); 	
			foreach($sql as $row)
			{
				if ($i%2==0)  
                	$bgcolor="#E9F3FF";
                else
               	 	$bgcolor="#FFFFFF";
			$process_id_val=$row[csf('process_id')];
					//$production_process=array(1=>"Cutting",2=>"Knitting",3=>"Dyeing",4=>"Finishing",5=>"Sewing",6=>"Fabric Printing",7=>"Washing",8=>"Printing",9=>"Embroidery");
					if($process_id_val==1 || $process_id_val==5)
					{
						$item_id_arr=$garments_item;
					}
					else
					{
						$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
					}
				?>
     	<tr id="" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="" > 
     		<td width="50" align="center"><? echo $i; ?></td>
             <td width="200" align="center"><? echo $item_id_arr[$row[csf('item_id')]]; ?></td>
            <td width="100" align="center"><? echo $row[csf('order_no')]; ?></td>
            <td width="100" align="center"><? echo $production_process[$row[csf('process_id')]]; ?></td>
            <?
			$sub='';
			$sub_process=explode(',',$row[csf('sub_process')]);
			if($sub_process[0]!=="")$coma=","; else $coma="";
			 foreach($sub_process as $process_row)
			 {
					$sub.=$conversion_cost_head_array[$process_row].$coma;
			 }
			?>
            <td width="150" align="center"><? echo $sub; ?></td>
            <? if($row[csf('cust_buyer')]!=="")$coma=","; else $coma=""; ?>
            <td width="100" align="center"><p><? echo $row[csf('cust_buyer')].$coma.$row[csf('cust_style_ref')]; ?></p></td>
            <td width="100" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
            <td width="" align="center"><p><? echo $row[csf('delivery_qnty')]; ?></p></td>
     	</tr>
        <?
		$i++;
		}
		?>
     </table>
      <table width="900">
            <? $total_delivery_qnty=return_field_value("sum(delivery_qnty)","subcon_delivery","id in ($data[5]) and status_active=1 and is_deleted=0"); ?>
                <tr class="form_caption">
                	<td align="right">Total:<? echo $total_delivery_qnty; ?></td>
                </tr>
            </table>
            
            <br><br>
             <table width="900">
                <tr class="form_caption">
                	<td align="left">Signature:</td>
                </tr>
            </table>
     </div>
     
           
     
    
     </div>
     
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
     
<?

}

?>