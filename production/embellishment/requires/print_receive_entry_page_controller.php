<?
session_start();
include('../../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$buyer_name_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
if($db_type==0) $select_field="group"; 
else if($db_type==2) $select_field="wm";
else $select_field="";//defined Later	

//------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 200, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/print_receive_entry_page_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 200, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (7,8,9) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
}

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("printing_emb_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
 	exit();
}

if ($action=="load_variable_settings_reject")
{
	echo "$('#embro_production_variable').val(0);\n";
	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$data and variable_list=28 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#embro_production_variable').val(".$result[csf("printing_emb_production")].");\n";
	}
 	exit();
}

if($action=="load_drop_down_emb_receive")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	
	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_emb_company", 200, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_emb_company", 200, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "" );
		}
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 200, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", $selected_company, "",0,0 );	
	else
		echo create_drop_down( "cbo_emb_company", 200, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );	
			
	exit();
}


/*
if($action=="load_drop_down_emb_receive_type")
{
	//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
	if($data==1)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "" );  
	elseif($data==2)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_embroy_type,"", 1, "--- Select Embroidery---", $selected, "" );
	elseif($data==3)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_wash_type,"", 1, "--- Select wash---", $selected, "" );	
	elseif($data==4)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_spwork_type,"", 1, "--- Select Special Works---", $selected, "" );
	elseif($data==5)
		echo create_drop_down( "cbo_embel_type", 200, $blank_array,"", 1, "--- Select---", $selected, "" );
	else
		echo create_drop_down( "cbo_embel_type", 200, $blank_array,"", 1, "--- Select---", $selected, "" );	
}
*/





if($action=="order_popup")
{
extract($_REQUEST);
echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		
		function search_populate(str)
		{
			//alert(str); 
			if(str==0) 
			{		
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';		 
			}
			else if(str==1) 
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<?php 
				if($db_type==0)
				{
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where find_in_set($company,tag_company) and status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');
				}
				else
				{
					$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name",'id','buyer_name');
				}				
				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				} 
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:230px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}																																													
		}
	
	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id)
	{ 
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id); 
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_country_id").val(country_id);
  		parent.emailwindow.hide();
 	}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
                    <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                   		 <thead>                	 
                        	<th width="130">Search By</th>
                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                        	<th width="200">Date Range</th>
                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    	</thead>               	 
        				<tr>
                    		<td width="130">  
							<? 
							$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
							echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
  							?>
                    		</td>
                   			<td width="180" align="center" id="search_by_td">				
								<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />			
            				</td>
                    		<td align="center">
                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
					  			<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 		</td> 
            		 		<td align="center">
                     			<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'print_receive_entry_page_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" height="40" valign="middle">
					<? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_mst_id">
                    <input type="hidden" id="hidden_grmtItem_id">
                    <input type="hidden" id="hidden_po_qnty">
                    <input type="hidden" id="hidden_country_id">
          		</td>
            </tr>
    	</table> 
        <div style="margin-top:10px" id="search_div"></div>     
    </form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];
	
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";		
 	}
	if($txt_date_from!="" || $txt_date_to!="") 
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}
	
	
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
		
 	$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity ,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b 
			where
			a.job_no = b.job_no_mst and
			a.status_active=1 and 
			a.is_deleted=0 and
			b.status_active=1 and 
			b.is_deleted=0 and
			a.garments_nature=$garments_nature
			$sql_cond"; 
	//echo $sql;die;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}
	
	
	
	$po_country_data_arr=array();
	$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
	
	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	}
	
	
	
	$total_rec_qty_data_arr=array();
	$total_rec_qty_arr=sql_select( "select po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=3 group by po_break_down_id, item_number_id, country_id");
	
	
	
	foreach($total_rec_qty_arr as $row)
	{
		$total_rec_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}
	?>
    <div style="width:1030px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Buyer</th>
                <th width="120">Style</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Total Rec.Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1030px; max-height:240px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1012" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";
				
				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				$numOfCountry = count($country);
				
				for($k=0;$k<$numOfItem;$k++)								
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}
					
					foreach($country as $country_id)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];
						
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>');" > 
                            <td width="30" align="center"><?php echo $i; ?></td>
                            <td width="70" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>		
                            <td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
                            <td width="100"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>	
                            <td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
                            <td width="140"><p><?php  echo $garments_item[$grmts_item];?></p></td>	
                            <td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
                            <td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp; </td>
                            <td width="80" align="right">
							<?php
							 echo $total_cut_qty=$total_rec_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id];
                             ?> &nbsp;
                           </td>
                           <td width="80" align="right">
							<?php
                             $balance=$po_qnty-$total_cut_qty;
                             echo $balance;
                             ?>&nbsp;
                           </td>
                            <td><?php  echo $company_arr[$row[csf("company_name")]];?> </td> 	
                        </tr>
						<? 
						$i++;
					}
				}
            }
   		?>
        </table>
    </div> 
	<?	
exit();	
}

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	if($dataArr[2]==0) $embel_name = "%%"; else $embel_name = $dataArr[2];
	$country_id = $dataArr[3];
	
	//echo "shajjad".$po_id;
	
	$res = sql_select("select a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name  
			from wo_po_break_down a, wo_po_details_master b
			where a.job_no_mst=b.job_no and a.id=$po_id"); 
	//print_r($res);die;
 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
  				
  		$dataArray=sql_select("select SUM(CASE WHEN production_type=2 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=3 THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and embel_name like '$embel_name' and country_id='$country_id' and is_deleted=0");
 		foreach($dataArray as $row)
		{ 
 			echo "$('#txt_issue_qty').val('".$row[csf('totalcutting')]."');\n";
			echo "$('#txt_cumul_receive_qty').attr('placeholder','".$row[csf('totalprinting')]."');\n";
			echo "$('#txt_cumul_receive_qty').val('".$row[csf('totalprinting')]."');\n";
			$yet_to_produced = $row[csf('totalcutting')]-$row[csf('totalprinting')];
			echo "$('#txt_yet_to_receive').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_receive').val('".$yet_to_produced."');\n";
		}
  	}
 	exit();	
}


if($action=="color_and_size_level")
{
		$dataArr = explode("**",$data);
		$po_id = $dataArr[0];
		$item_id = $dataArr[1];
		$variableSettings = $dataArr[2];
		$styleOrOrderWisw = $dataArr[3];
		$embelName = $dataArr[4];
		$country_id = $dataArr[5];
		$variableSettingsRej = $dataArr[6];
		
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		//#############################################################################################//
		// order wise - color level, color and size level
		
		//$variableSettings=2;
		
		if( $variableSettings==2 ) // color level
		{
			if($db_type==0)
			{
			
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls, pro_garments_production_mst mst where mst.id=pdtls.mst_id and mst.embel_name='$embelName' and pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as reject_qty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
			}
			else
			{
				$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=3 and c.embel_name='$embelName' then b.production_qnty ELSE 0 END) as cur_production_qnty,
						sum(CASE WHEN c.production_type=3 then b.reject_qty ELSE 0 END) as reject_qty
						from wo_po_color_size_breakdown a 
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";	
				
			}
				
			$colorResult = sql_select($sql);
				
		}
		else if( $variableSettings==3 ) //color and size level
		{
			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls, pro_garments_production_mst mst where mst.id=pdtls.mst_id and mst.embel_name='$embelName' and pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
				
				
			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.embel_name='$embelName' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");	
										
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
			}  
			//print_r($color_size_qnty_array);
				
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, id";
				
				
			$colorResult = sql_select($sql);	
		}
		else // by default color and size level
		{
			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls, pro_garments_production_mst mst where mst.id=pdtls.mst_id and mst.embel_name='$embelName' and pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id*/
				
			/*	
			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.embel_name='$embelName' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");	
										
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
			}  
			//print_r($color_size_qnty_array);
			
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, id";
				
			$colorResult = sql_select($sql);	*/
		}
		
		//$colorResult = sql_select($sql);		
 		//print_r($sql);
		if($variableSettingsRej!=1)
		{
			$disable="";
		}
		else
		{
			$disable="disabled";
		}
  		$colorHTML="";
		$colorID='';
		$chkColor = array(); 
		$i=0;$totalQnty=0;
 		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{ 
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).')  '.$disable.'"></td></tr>';				
				$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
				$colorID .= $color[csf("color_number_id")].",";
			}
			else //color and size level
			{
				if( !in_array( $color[csf("color_number_id")], $chkColor ) )
				{
					if( $i!=0 ) $colorHTML .= "</table></div>";
					$i=0;
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];					
				}
 				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				
				$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
				$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
				
				
 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'></td></tr>';				
			}
			
			$i++; 
		}
		//echo $colorHTML;die; 
		if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="Rej." class="text_boxes_numeric" style="width:60px" '.$disable.' ></th></tr></tfoot></table>'; }
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		//#############################################################################################//
		exit();
}


if($action=="show_dtls_listview")
{
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[3];
	$type = $dataArr[4];
	
	if($type==1) $embel_name="%%"; else $embel_name = $dataArr[2];
?>	
	
    <div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>  
                <th width="30">ID</th>
                <th width="140" align="center">Item Name</th>
                <th width="110" align="center">Country</th>
                <th width="80" align="center">Production Date</th>
                <th width="80" align="center">Production Qty</th>
                <th width="80" align="center">Reject Qnty</th>                    
                <th width="120" align="center">Serving Company</th>
                <th width="100" align="center">Location</th>
                <th align="center">Challan No</th>
            </thead> 
        </table>
    </div>
	<div style="width:100%;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_search">
		<?php 
		
		 //and embel_name like '$embel_name'
 			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("select id, po_break_down_id,embel_name,production_source, item_number_id, country_id, production_date, production_quantity, reject_qnty, production_source, serving_company, location, challan_no from pro_garments_production_mst where po_break_down_id='$po_id'  and item_number_id='$item_id' and country_id='$country_id' and production_type='3' and status_active=1 and is_deleted=0 order by id");
			foreach($sqlResult as $selectResult){
				
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
                $total_production_qnty+=$selectResult[csf('production_quantity')]; 
				//	 	
 		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_receive_entry_page_controller');"  > 
            	<td width="30" align="center"><input type="checkbox" id="tbl_<? echo $i; ?>"  onClick="fnc_checkbox_check(<? echo $i; ?>);"  />&nbsp;&nbsp;&nbsp; <? //echo $i; ?>
                  
                  
                   <input type="hidden" id="mstidall_<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>" style="width:30px"/>	
                   <input type="hidden" id="emblname_<? echo $i; ?>" name="emblname[]"   width="30" value="<? echo $selectResult[csf('embel_name')]; ?>" />
                    <input type="hidden" id="productionsource_<? echo $i; ?>"   width="30" value="<? echo $selectResult[csf('production_source')]; ?>" />
                </td>
				<td width="30" align="center"><? echo $selectResult[csf('id')]; ?>
                
                </td>
                <td width="140" align="center"><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></td>
                <td width="110" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                <td width="80" align="center"><? echo change_date_format($selectResult[csf('production_date')]); ?></td>
                <td width="80" align="center"><?  echo $selectResult[csf('production_quantity')]; ?></td>
                <td width="80" align="center"><?  echo $selectResult[csf('reject_qnty')]; ?></td>
				<?
                       	$source= $selectResult[csf('production_source')];
					   	if($source==1) $serving_company= $company_arr[$selectResult[csf('serving_company')]];
						else $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
                 ?>	
                <td width="120" align="center"><p><? echo $serving_company; ?></p></td>
                <td width="100" align="center"><p><? echo $location_arr[$selectResult[csf('location')]]; ?></p></td>
                <td align="center"><p><? echo $selectResult[csf('challan_no')]; ?></p></td>
			</tr>
           
			<?
			$i++;
			}
			?>
            
            <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>-->
           
		</table>
        <script>
	setFilterGrid("tbl_search",-1);
	</script>
	</div>
<?
	exit();
}

if($action=="show_country_listview")
{
?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="110">Item Name</th>
            <th width="80">Country</th>
            <th width="75">Shipment Date</th>
            <th>Order Qty.</th>                    
        </thead>
		<?  
		$i=1;
		$sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);"> 
				<td width="30"><? echo $i; ?></td>
				<td width="110"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="75" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right"><?php  echo $row[csf('order_qnty')]; ?></td>
			</tr>
		<?	
			$i++;
		}
		?>
	</table>
	<?
	exit();
}


if($action=="populate_receive_form_data")
{
	//production type=2 come from array
	$sqlResult =sql_select("select id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, break_down_type_rej, production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, reject_qnty, total_produced, yet_to_produced from pro_garments_production_mst where id='$data' and production_type='3' and status_active=1 and is_deleted=0 order by id");
  	//echo "sdfds".$sqlResult;die;
	foreach($sqlResult as $result)
	{ 
		echo "$('#txt_receive_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/print_receive_entry_page_controller', ".$result[csf('production_source')].", 'load_drop_down_emb_receive', 'emb_company_td' );\n";
		echo "$('#cbo_emb_company').val('".$result[csf('serving_company')]."');\n";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/print_receive_entry_page_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "$('#cbo_embel_name').val('".$result[csf('embel_name')]."');\n";
		echo "load_drop_down( 'requires/print_receive_entry_page_controller', ".$result[csf('embel_name')].", 'load_drop_down_emb_receive_type', 'emb_type_td' );\n";
		echo "$('#cbo_embel_type').val('".$result[csf('embel_type')]."');\n";
		
 		echo "$('#txt_receive_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_reject_qty').val('".$result[csf('reject_qnty')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		
		$dataArray=sql_select("select SUM(CASE WHEN production_type=2 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=3 THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and embel_name=".$result[csf('embel_name')]." and country_id=".$result[csf('country_id')]." and is_deleted=0");
 		foreach($dataArray as $row)
		{ 
			echo "$('#txt_issue_qty').attr('placeholder','".$row[csf('totalCutting')]."');\n";
 			echo "$('#txt_issue_qty').val('".$row[csf('totalCutting')]."');\n";
			echo "$('#txt_cumul_receive_qty').val('".$row[csf('totalPrinting')]."');\n";
			$yet_to_produced = $row[csf('totalCutting')]-$row[csf('totalPrinting')];
			echo "$('#txt_yet_to_receive').val('".$yet_to_produced."');\n";
		}

		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";	
		//echo "$('#txt_mst_id_all').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_print_receive_entry',1,1);\n";
		 
		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		$variableSettingsRej = $result[csf('break_down_type_rej')];
		if( $variableSettings!=1 ) // gross level
		{ 
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];
			
			$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");	
			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
				$rejectArr[$index] = $row[csf('reject_qty')];
			}  
			 
			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
				
					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as reject_qty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
				}
				else
				{
					$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as production_qnty,
							sum(CASE WHEN c.production_type=3 then b.production_qnty ELSE 0 END) as cur_production_qnty,
							sum(CASE WHEN c.production_type=3 then b.reject_qty ELSE 0 END) as reject_qty
							from wo_po_color_size_breakdown a 
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
							left join pro_garments_production_mst c on c.id=b.mst_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";	
					
				}
				
			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
					
					
				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and a.mst_id=$data and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");
										
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				} 
				
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id";	
					
					
			}
			else // by default color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
					
					
				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty 
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");
									
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}  
				//print_r($color_size_qnty_array);
				
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id";
					
					
			}
			if($variableSettingsRej!=1)
			{
				$disable="";
			}
			else
			{
				$disable="disabled";
			}
 
 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array(); 
			$i=0;$totalQnty=0;$colorWiseTotal=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{  
					$amount = $amountArr[$color[csf("color_number_id")]];
					$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onblur="fn_colorRej_total('.($i+1).')"></td></tr>';				
					$totalQnty += $amount;
					$totalRejQnty += $rejectAmt;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$amount = $amountArr[$index];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					
					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
					
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'></td></tr>';				
					$colorWiseTotal += $amount;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}//end if condtion
		//#############################################################################################//
	}
 	exit();		
}

//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
 		
		$color_sizeID_arr=sql_select( "select id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and status_active=1 order by id" );
		$colSizeID_arr=array();$i=0;
		foreach($color_sizeID_arr as $val){
			$colSizeID_arr[$i++]=$val[csf("id")];
		}
		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );

		//3 means print receive array 
 		$field_array1="id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, break_down_type_rej, remarks, floor_id, reject_qnty, total_produced, yet_to_produced, inserted_by, insert_date";
		$data_array1="(".$id.",".$cbo_company_name.",".$garments_nature.",".$txt_challan.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_receive_date.",".$txt_receive_qty.",3,".$sewing_production_variable.",".$embro_production_variable.",".$txt_remark.",".$cbo_floor.",".$txt_reject_qty.",".$txt_cumul_receive_qty.",".$txt_yet_to_receive.",".$user_id.",'".$pc_date_time."')";
		
 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		//echo $data_array;die;
		// pro_garments_production_dtls table entry here ----------------------------------///
		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty";
  		
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{		
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 and status_active=1 order by id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}	
			
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
			$rowExRej = explode("**",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorSizeRejIDArr = explode("*",$valR);
				//echo $colorSizeRejIDArr[0]; die;
				$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
			}
			
 			$rowEx = explode("**",$colorIDvalue); 
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				
				//3 for Receive Print / Emb. Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

				if($j==0)$data_array = "(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
				else $data_array .= ",(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
				//$dtls_id=$dtls_id+1;							
 				$j++;								
			}
 		}
		
		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{		
			$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 order by size_number_id,color_number_id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf('id')];
			}	
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
			$rowExRej = explode("***",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$sizeID = $colorAndSizeRej_arr[0];
				$colorID = $colorAndSizeRej_arr[1];				
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $sizeID.$colorID;
				$rejQtyArr[$index]=$colorSizeRej;
			}
			
 			$rowEx = explode("***",$colorIDvalue); 
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];				
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$colorID;
 				
				//3 for Receive Print / Emb. Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

				if($j==0)$data_array = "(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
				else $data_array .= ",(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
				//$dtls_id=$dtls_id+1;
 				$j++;
			}
		}
		//echo "INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		} 
		
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{	  
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID)
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con); 
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);  
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
			
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
		
		// pro_garments_production_mst table data entry here //3 means print receive
 		$field_array1="production_source*serving_company*location*embel_name*embel_type*production_date*production_quantity*production_type*entry_break_down_type*break_down_type_rej*remarks*floor_id*reject_qnty*total_produced*yet_to_produced*updated_by*update_date";
		$data_array1="".$cbo_source."*".$cbo_emb_company."*".$cbo_location."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_receive_date."*".$txt_receive_qty."*3*".$sewing_production_variable."*".$embro_production_variable."*".$txt_remark."*".$cbo_floor."*".$txt_reject_qty."*".$txt_cumul_receive_qty."*".$txt_yet_to_receive."*".$user_id."*'".$pc_date_time."'";
		
		
 		//$rID=sql_update("pro_garments_production_mst",$field_array,$data_array,"id","".$txt_mst_id."",1);
		//echo $data_array;die;
		// pro_garments_production_dtls table data entry here 
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) // check is not gross level
		{
			
			$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty";
			
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 and status_active=1 order by id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				
				$rowExRej = explode("**",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorSizeRejIDArr = explode("*",$valR);
					//echo $colorSizeRejIDArr[0]; die;
					$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
				}
				
				$rowEx = explode("**",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					//3 for Receive Print / Emb. Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."')";
					//$dtls_id=$dtls_id+1;							
					$j++;								
				}
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 order by size_number_id,color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				$rowExRej = explode("***",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					$sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];				
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$colorID;
					$rejQtyArr[$index]=$colorSizeRej;
				}
				
				$rowEx = explode("***",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];				
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$colorID;
					//3 for Receive Print / Emb. Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."')";
					$dtls_id=$dtls_id+1;
					$j++;
				}
			}
			
 			//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		
		}//end cond
		
		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1); //echo $rID;die;
		
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
		
		
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrDelete && $dtlsrID)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con); 
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);  
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
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
		
		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		
 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$hidden_po_break_down_id); 
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$hidden_po_break_down_id); 
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".str_replace("'","",$hidden_po_break_down_id); 
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id); 
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="emblishment_receive_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	//print_r( $mst_id);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$order_library=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	
	$sql="select min(id) as id, min(company_id) as company_id, sum(production_quantity) as production_quantity,sum(reject_qnty) as  reject_qnty,  min(challan_no) as challan_no,min( po_break_down_id) as po_break_down_id, min(item_number_id) as item_number_id,min(entry_break_down_type) as entry_break_down_type,min(break_down_type_rej) as break_down_type_rej, min(country_id) as country_id,
 min(production_source) as production_source, min(serving_company) as serving_company, min( location) as location , 
min(embel_name) as embel_name, min(embel_type) as embel_type, min(production_date) as production_date, min( production_type) as production_type, min(remarks) as remarks from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0";
	// $sql="select id, company_id, challan_no, po_break_down_id, item_number_id,entry_break_down_type,break_down_type_rej, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, reject_qnty, production_type, remarks, floor_id from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0 group by  production_source,embel_name,po_break_down_id";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];

?>
<div style="width:930px;">

    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:12px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add"); 
                foreach ($nameArray as $result)
                { 
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
            ?> 
        	<td width="100" rowspan="5" valign="top" colspan="2"><p><strong>Embel. Company : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></p></td>
            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?></td>
            <? 
				foreach($dataArray as $row)
				{
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
					$buyer_id=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"buyer_name");
					$order_qnty=return_field_value("po_quantity"," wo_po_break_down","id=".$row[csf("po_break_down_id")],"po_quantity");
				}
			?>
        	<td width="125"><strong>Style Ref. :</strong></td><td width="175px"><? echo $style_val; ?></td>
        </tr>
        <tr>
        	<td><strong>Order Qty:</strong></td><td><? echo $order_qnty; ?></td>
        	<td><strong>Item :</strong></td><td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
           <td><strong>Emb. Type:</strong></td><td ><? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; ?></td> 
        </tr>
        <tr>
            <td><strong>Embel. Name :</strong></td><td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td><strong>Receive Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td> 
            
        </tr>
        <tr>
            <td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Buyer :</strong></td><td><? echo $buyer_name_library[$buyer_id]; ?></td>
        </tr>
        <tr>
        	<td colspan="6" ><strong>Remarks:  <? echo $dataArray[0][csf('remarks')]; ?></strong></td> 
        </tr>
         
        <tr>
         <?
		if($entry_break_down_type==1)
		{
		?>

        	<td colspan="3" ><strong>Receive Qnty :  <? echo $dataArray[0][csf('production_quantity')]; ?></strong></td>
       <? 
		}
		 if($break_down_type_reject==1)
		{
  ?>
            <td colspan="3" ><strong>Reject Qnty:  <? echo $dataArray[0][csf('reject_qnty')]; ?></strong></td>  
       <? }
		   else
		 { ?>
			   <td colspan="6">&nbsp;</td>
	<?   }
	 ?>
        </tr>
			
    </table>
         <br>
        <?
		if($entry_break_down_type!=1)
		{
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			//$reject_qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				//$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
			}
			
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id,a.mst_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}
			
			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
			$country_id=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ","country_id");
		?> 
         
	<div style="width:100%;">
    <div style="margin-left:30px;"><strong> Goods Qty.</strong></div>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
              <th width="80" align="center">Country</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qnty.</th>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                         <td><? echo $country_library[$country_id]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
							$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>                           
    </table>
      <br>
      
		 <?
		}
		if($break_down_type_reject!=1)
		{ ?>
        <?
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			 $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.reject_qty>0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			$reject_qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
			}
			
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id)  and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}
			
			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
			
			
				$country_id2=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","country_id");
		?> 
         
	<div style="width:100%;">
    <div style="width:900px; margin-left:30px;">
   <table align="right" cellspacing="0" width="900"  border="0" rules="all" class="rpt_table"> 
    <tr><td><strong> Reject Qty:</strong></td></tr>
    </table>
    </div>
	<?
	//echo $size_arr=count($size_array).'azz';
	if(count($size_array)>0)
	{
	
	?>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
              <th width="80" align="center">Country</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qnty.</th>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
					
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                         <td><? echo $country_library[$country_id2]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $reject_qun_array[$cid][$sizval]; ?></td>
                            <?
                            $reject_tot_qnty[$cid]+=$reject_qun_array[$cid][$sizval];
							$reject_tot_qnty_size[$sizval]+=$reject_qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $reject_tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$reject_production_quantity+=$reject_tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $reject_tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $reject_production_quantity; ?></td>
        </tr>                           
    </table>
	<?
		}

	?>
		<br>		 
			
		<? }
            echo signature_table(27, $data[0], "900px");
         ?>
	</div>
	</div>
<?	
exit();
}
?>