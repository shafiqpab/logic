<?
session_start();
include('../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select sewing_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("sewing_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
	
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$data and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo!=1) $prod_reso_allo=0;
	echo "document.getElementById('prod_reso_allo').value=".$prod_reso_allo.";\n";
	
 	exit();
}


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/sewing_output_gross_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/sewing_output_gross_controller', document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value, 'load_drop_down_sewing_output_line', 'sewing_line_td' );get_php_form_data(document.getElementById('cbo_source').value,'line_disable_enable','requires/sewing_output_gross_controller');" );     	 
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/sewing_output_gross_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );",0 );     	 
}

if($action=="line_disable_enable")
{
	if($data==1)
		echo "disable_enable_fields('cbo_sewing_line',0,'','');\n";
	else
	{
		echo "$('#cbo_sewing_line').val(0);\n";
		echo "disable_enable_fields('cbo_sewing_line',1,'','');\n";	
	}
}

if($action=="load_drop_down_sewing_output")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	
	if($data==3)
	{
		echo create_drop_down( "cbo_sewing_company", 170, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sewing_output_gross_controller');",0,0 ); 
	}
	else if($data==1)
	{
 		echo create_drop_down( "cbo_sewing_company", 170, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", $selected_company,  "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(), 'display_bl_qnty', 'requires/sewing_output_gross_controller');",0,0 ); 
		
	}
 	else
	{
		echo create_drop_down( "cbo_sewing_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
	}
			
	exit();
}

if($action=="display_bl_qnty")
{
	$explode_data = explode("**",$data);
	$sewing_company=$explode_data[0];
	$source=$explode_data[1];
	$po_break_down_id=$explode_data[2];
	$item_id=$explode_data[3];
	$country_id=$explode_data[4];
	
	$dataArray=sql_select("select SUM(CASE WHEN production_type=4 THEN production_quantity END) as totalinput,SUM(CASE WHEN production_type=5 THEN production_quantity ELSE 0 END) as totalsewing from pro_gar_prod_gross_mst WHERE po_break_down_id='$po_break_down_id' and item_number_id='$item_id' and country_id='$country_id' and production_source='$source' and serving_company='$sewing_company' and status_active=1 and is_deleted=0");
	foreach($dataArray as $row)
	{  
		echo "$('#txt_input_quantity').val('".$row['totalinput']."');\n";
		echo "$('#txt_cumul_sewing_qty').val('".$row['totalsewing']."');\n";
		$yet_to_produced = $row['totalinput']-$row['totalsewing'];
		echo "$('#txt_yet_to_sewing').val('".$yet_to_produced."');\n";
	}
	
	exit();
}

if($action=="load_drop_down_sewing_output_line")
{
	$explode_data = explode("_",$data);
	$location = $explode_data[0];
	$prod_reso_allocation = $explode_data[1];
	$txt_sewing_date = $explode_data[2];
	
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_sewing_date=="")
		{ 
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 and location_id='$location'");
		}
		else
		{
			if($db_type==0)
				{
					$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.location_id='$location' and a.is_deleted=0 and b.is_deleted=0 group by a.id");
				}
			if($db_type==2 || $db_type==1)
				{	
					$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
					
			}
		}

		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_sewing_line", 110,$line_array,"", 1, "--- Select ---", $selected, "",0,0 );		
	}
	else
	{
		echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and location_name='$location' and location_name!=0 order by line_name","id,line_name", 1, "Select Line", $selected, "" );
	}
}

if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);	
	$prod_reso_allocation = $explode_data[2];
	$txt_sewing_date = $explode_data[3];
	$cond="";
	
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_sewing_date=="")
		{ 
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
			
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
			
		if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
			}
			if($db_type==2 || $db_type==1)
			{	
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
				
			}
		}

		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_sewing_line", 110,$line_array,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";
		
		echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
	}
}


if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
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
				
				$buyer_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name",'id','buyer_name');				
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
                     			<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'sewing_output_gross_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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
			b.is_deleted=0 
			$sql_cond "; 
	//echo $sql;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$po_country_arr=return_library_array( "select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	
	$po_country_data_arr=array();
	$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");
	
	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	}
	
	?>
    
     <div style="width:930px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="100">Order No</th>
                <th width="100">Shipment Date</th>
                <th width="100">Buyer</th>
                <th width="130">Style</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="100">Order Qnty</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:930px; max-height:240px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="912" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";
				
				$country=explode(",",$po_country_arr[$row[csf("id")]]);
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
								<td width="40" align="center"><?php echo $i; ?></td>
								<td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
								<td width="100" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>		
								<td width="100"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>	
								<td width="130"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
								<td width="140"><p><?php  echo $garments_item[$grmts_item];?></p></td>	
								<td width="100"><p><?php echo $country_library[$country_id]; ?></p></td>
								<td width="100" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?> </td>
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
	$country_id = $dataArr[2];
	
	$res = sql_select("select a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name 
			from wo_po_break_down a, wo_po_details_master b
			where a.job_no_mst=b.job_no and a.id=$po_id"); 
 
  	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
  		  		
   	
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
		$country_id = $dataArr[4];
		
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		
		//#############################################################################################//
		// order wise - color level, color and size level
		
		if( $variableSettings==2 ) // color level
		{
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_gar_prod_gross_mst pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
		}
		else if( $variableSettings==3 ) //color and size level
		{
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
		}
		else // by default color and size level
		{
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
		}
		
		$colorResult = sql_select($sql);		
 		//print_r($sql);
  		$colorHTML="";
		$colorID='';
		$chkColor = array(); 
		$i=0;$totalQnty=0;
 		foreach($colorResult as $color)
		{
 			
			if( $variableSettings==2 ) // color level
			{ 
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';				
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
 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';				
			}
			
			$i++; 
		}
		//echo $colorHTML;die; 
		if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
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
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];	
	$country_id = $dataArr[2];
	$prod_reso_allo = $dataArr[3];
?>	 
     <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="120" align="center">Item Name</th>
                <th width="100" align="center">Country</th>
                <th width="75" align="center">Prod. Date</th>
                <th width="85" align="center">Prod. Qnty</th> 
                <th width="120" align="center">Serving Company</th>                   
                <th width="100" align="center">Sewing Line</th>
                <th width="60" align="center">Rep. Hour</th>
                <th width="100" align="center">Supervisor</th>
                <th width="" align="center">Location</th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
		<?php  
			$i=1;
			$total_production_qnty=0;
			 if(str_replace("'","",$country_id)!="") $sql_cond="and  country_id=".str_replace("'","",$country_id)."";
			 if($db_type==0)
			 {
			$sqlResult =sql_select("select id,po_break_down_id,item_number_id,country_id, production_date, production_quantity, production_source, serving_company, sewing_line, supervisor, location, prod_reso_allo, TIME_FORMAT( production_hour, '%H:%i' ) as production_hour from pro_gar_prod_gross_mst where po_break_down_id='$po_id' and item_number_id='$item_id' $sql_cond and production_type='5' and status_active=1 and is_deleted=0 order by id");
	
			 }
			 else
			 {
	
			$sqlResult =sql_select("select id,po_break_down_id,item_number_id,country_id, production_date, production_quantity, production_source, serving_company, sewing_line, supervisor, location, prod_reso_allo, TO_CHAR(production_hour,'HH24:MI') as production_hour from pro_gar_prod_gross_mst where po_break_down_id='$po_id' and item_number_id='$item_id' $sql_cond and production_type='5' and status_active=1 and is_deleted=0 order by id");
	
			 }
			foreach($sqlResult as $selectResult){
				
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('production_quantity')];
				
				$sewing_line='';
				if($selectResult[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$selectResult[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_arr[$val]; else $sewing_line.=",".$sewing_line_arr[$val];
					}
				}
				else $sewing_line=$sewing_line_arr[$selectResult[csf('sewing_line')]];
				
  		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_input_form_data','requires/sewing_output_gross_controller');" > 
				<td width="40" align="center"><? echo $i; ?></td>
                <td width="120" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                <td width="100" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                <td width="75" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                <td width="85" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
				<?php
                        $source= $selectResult[csf('production_source')];
					   	if($source==3) $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
						else $serving_company= $company_arr[$selectResult[csf('serving_company')]];
                 ?>	
                <td width="120" align="center"><?php echo $serving_company; ?></p></td>
                <td width="100" align="center"><p><? echo $sewing_line; ?></p></td>
                <td width="60" align="center"><p><? echo $selectResult[csf('production_hour')]; ?></p></td>
                <td width="100" align="center"><p><? echo $selectResult[csf('supervisor')]; ?></p></td>
                <td width="" align="center"><p><? echo $location_arr[$selectResult[csf('location')]]; ?></p></td>
			</tr>
			<?php
			$i++;
			}
			?>
            <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="4"></th>
                </tr>
            </tfoot>-->
		</table>
        </div>
	<?
	
}



if($action=="populate_input_form_data")
{
	  
	//production type=5 come from array
	if($db_type==0) $production_time=" TIME_FORMAT(production_hours, '%H:%i' ) as production_hour";
	else            $production_time=" TO_CHAR(production_hours,'HH24:MI') as production_hour";
	
	$sql_dtls ="select id, garments_nature, po_break_down_id, item_number_id, country_id, production_source, serving_company, sewing_line, location, embel_name, embel_type, production_date, production_quantity, production_source, production_type, $production_time , sewing_line, supervisor, remarks, floor_id, reject_qnty, alter_qnty, total_produced, yet_to_produced, spot_qnty from pro_gar_prod_gross_mst where id='$data' and production_type='5' and status_active=1 and is_deleted=0 order by id";
  	//echo $sql_dtls;
	$sqlResult =sql_select($sql_dtls);
	foreach($sqlResult as $result)
	{ 
		echo "$('#txt_sewing_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/sewing_output_gross_controller', ".$result[csf('production_source')].", 'load_drop_down_sewing_output', 'sew_company_td' );\n";
		echo "$('#cbo_sewing_company').val('".$result[csf('serving_company')]."');\n";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/sewing_output_gross_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		
		echo "load_drop_down( 'requires/sewing_output_gross_controller', document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value, 'load_drop_down_sewing_output_line', 'sewing_line_td' );\n";
		
		echo "$('#cbo_sewing_line').val('".$result[csf('sewing_line')]."');\n";
		echo "get_php_form_data(".$result[csf('production_source')].",'line_disable_enable','requires/sewing_output_gross_controller');\n";
		
		if($result[csf('production_hour')]>12)
		{
			$hour = $result[csf('production_hour')]-12;  $time=2;
 		}
		else if($result[csf('production_hour')]==12)
		{
			$hour = "00";  $time=1;
		}
		else
		{
			$hour = $result[csf('production_hour')]; $time=1;
		}
		echo "$('#txt_reporting_hour').val('".$result[csf('production_hour')]."');\n";
	
		echo "$('#txt_super_visor').val('".$result[csf('supervisor')]."');\n";
		echo "$('#txt_sewing_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_reject_qnty').val('".$result[csf('reject_qnty')]."');\n";
		echo "$('#txt_alter_qnty').val('".$result[csf('alter_qnty')]."');\n";
		echo "$('#txt_spot_qnty').val('".$result[csf('spot_qnty')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		
		$dft_id=""; $alt_save_data=""; $bk_save_data="";$wt_save_data="";$me_save_data=""; $altType_id=""; $bktType_id="";$metType_id="";$wttType_id=""; $altpoint_id="";$bktpoint_id="";$metpoint_id=""; $bktpoint_id=""; $wttpoint_id="";
		$defect_sql=sql_select("select id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft_gross where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0 and production_type='5'");
		//echo "select id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft_gross where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0 and production_type='5'";
		foreach($defect_sql as $dft_row)
		{
			if($dft_row[csf('defect_type_id')]==1) //Front Part
			{
				if($alt_save_data=="") $alt_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $alt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($altpoint_id=="") $altpoint_id=$dft_row[csf('defect_point_id')]; else $altpoint_id.=','.$dft_row[csf('defect_point_id')];
				$altType_id=$dft_row[csf('defect_type_id')];
			}

			if($dft_row[csf('defect_type_id')]==2)//Back
			{
				if($bk_save_data=="") $bk_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $bk_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($bktpoint_id=="") $bktpoint_id=$dft_row[csf('defect_point_id')]; else $bktpoint_id.=','.$dft_row[csf('defect_point_id')];
				$bktType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==3) //West Band
			{
				if($wt_save_data=="") $wt_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $wt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($wttpoint_id=="") $wttpoint_id=$dft_row[csf('defect_point_id')]; else $wttpoint_id.=','.$dft_row[csf('defect_point_id')];
				$wttType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==4)//Mesure
			{
				if($me_save_data=="") $me_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $me_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($metpoint_id=="") $metpoint_id=$dft_row[csf('defect_point_id')]; else $metpoint_id.=','.$dft_row[csf('defect_point_id')];
				$metType_id=$dft_row[csf('defect_type_id')];
			}
		}
		echo "$('#save_data').val('".$alt_save_data."');\n";
		echo "$('#all_defect_id').val('".$altpoint_id."');\n";
		echo "$('#defect_type_id').val('".$altType_id."');\n";

		echo "$('#save_dataBack').val('".$bk_save_data."');\n";
		echo "$('#allBack_defect_id').val('".$sptpoint_id."');\n";
		echo "$('#defectBack_type_id').val('".$bktType_id."');\n";
		
		echo "$('#save_dataWest').val('".$wt_save_data."');\n";
		echo "$('#allWest_defect_id').val('".$wttpoint_id."');\n";
		echo "$('#defectWest_type_id').val('".$wttType_id."');\n";
		
		echo "$('#save_dataMeasure').val('".$me_save_data."');\n";
		echo "$('#allMeasure_defect_id').val('".$metpoint_id."');\n";
		echo "$('#defectMeasure_type_id').val('".$metType_id."');\n";
		
		
		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_sewing_output_entry',1,1);\n";
		
	
	}
 	exit();		
	
}




//pro_gar_prod_gross_mst
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
		$id=return_next_id("id", "pro_gar_prod_gross_mst", 1);
		//production_type array	  
  		$field_array="id, company_id, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, sewing_line, supervisor, production_hours, remarks, floor_id, alter_qnty, reject_qnty, prod_reso_allo, spot_qnty, inserted_by, insert_date";
		if($db_type==0)
		{
		$data_array="(".$id.",".$cbo_company_name.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_sewing_company.",".$cbo_location.",".$txt_sewing_date.",".$txt_sewing_qty.",5,".$cbo_sewing_line.",".$txt_super_visor.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_alter_qnty.",".$txt_reject_qnty.",".$prod_reso_allo.",".$txt_spot_qnty.",".$user_id.",'".$pc_date_time."')";
		}
		else
		{
		$txt_reporting_hour=str_replace("'","",$txt_sewing_date)." ".str_replace("'","",$txt_reporting_hour);
		$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		$data_array="INSERT INTO pro_gar_prod_gross_mst(".$field_array.") VALUES(".$id.",".$cbo_company_name.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_sewing_company.",".$cbo_location.",".$txt_sewing_date.",".$txt_sewing_qty.",5,".$cbo_sewing_line.",".$txt_super_visor.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_alter_qnty.",".$txt_reject_qnty.",".$prod_reso_allo.",".$txt_spot_qnty.",".$user_id.",'".$pc_date_time."')";
			
		}
		$defectQ=true;
		$data_array_defect="";
		$save_string=explode(",",str_replace("'","",$save_data));
		$dft_front_id=="";
		
		if(count($save_string)>0 && str_replace("'","",$save_data)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft_gross", 1);
			$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_array=array();
			for($i=0;$i<count($save_string);$i++)
			{
				$order_dtls=explode("**",$save_string[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_array) )
				{
					$defect_array[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_array[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$f=0;
			foreach($defect_array as $key=>$val)
			{
				if( $f>0 ) $data_array_defect.=",";
 				
			//echo "10**";
		//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defect.="(".$dft_id.",".$id.",5,".$hidden_po_break_down_id.",".$defect_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				$dft_id = $dft_id + 1;
				$f++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defect!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft_gross (".$field_array_defect.") VALUES ".$data_array_defect.""; die;
			$defectFront=sql_insert("pro_gmts_prod_dft_gross",$field_array_defect,$data_array_defect,1);
		}
	//Front part End
		$defectBack=true;
		$data_array_defectbk="";
		$save_dataStringBack=explode(",",str_replace("'","",$save_dataBack));
		$dftbk_id=="";
		
		if(count($save_dataStringBack)>0 && str_replace("'","",$save_dataBack)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_bk_id=return_next_id("id", "pro_gmts_prod_dft_gross", 1);
			$field_array_defectbk="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectBack_array=array();
			for($i=0;$i<count($save_dataStringBack);$i++)
			{
				$order_dtls=explode("**",$save_dataStringBack[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectBack_array) )
				{
					$defectBack_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectBack_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$b=0;
			foreach($defectBack_array as $keysp=>$valsp)
			{
				if( $b>0 ) $data_array_defectbk.=",";

				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectbk.="(".$dft_bk_id.",".$id.",5,".$hidden_po_break_down_id.",".$defectBack_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				$dft_bk_id = $dft_bk_id + 1;
				$b++;
			}
		}

		if($data_array_defectbk!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft_gross (".$field_array_defectbk.") VALUES ".$data_array_defectbk.""; die;
			//$=sql_insert2("pro_gmts_prod_dft_gross",$data_array_defectbk,$field_array_defectbk,1);
			$defectBk=sql_insert("pro_gmts_prod_dft_gross",$field_array_defectbk,$data_array_defectbk,1);
			//echo "10**=".$defectBk.'ASA';die;
		}
		//Back part End
		$defectWest=true;
		$data_array_defectWt="";
		$save_dataStringWest=explode(",",str_replace("'","",$save_dataWest));
		$dftwt_id=="";
		
		if(count($save_dataStringWest)>0 && str_replace("'","",$save_dataWest)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_wt_id=return_next_id("id", "pro_gmts_prod_dft_gross", 1);
			$field_array_defectwt="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectWest_array=array();
			for($i=0;$i<count($save_dataStringWest);$i++)
			{
				$order_dtls=explode("**",$save_dataStringWest[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectWest_array) )
				{
					$defectWest_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectWest_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$w=0;
			foreach($defectWest_array as $keysp=>$valsp)
			{
				if( $w>0 ) $data_array_defectwt.=",";

				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectwt.="(".$dft_wt_id.",".$id.",5,".$hidden_po_break_down_id.",".$defectWest_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				$dft_wt_id = $dft_wt_id + 1;
				$w++;
			}
		}

		if($data_array_defectwt!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectwt.") VALUES ".$data_array_defectwt.""; die;
			$defectWt=sql_insert("pro_gmts_prod_dft_gross",$field_array_defectwt,$data_array_defectwt,1);
		}
		//West Band End
		$defectMeasure=true;
		$data_array_defectM="";
		$save_dataStringMeasure=explode(",",str_replace("'","",$save_dataMeasure));
		$dftwt_id=="";
		$dft_me_id=return_next_id("id", "pro_gmts_prod_dft_gross", 1);
		if(count($save_dataStringMeasure)>0 && str_replace("'","",$save_dataMeasure)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defectMe="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectMeasure_array=array();
			for($i=0;$i<count($save_dataStringMeasure);$i++)
			{
				$order_dtls=explode("**",$save_dataStringMeasure[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectMeasure_array) )
				{
					$defectMeasure_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectMeasure_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$m=0;
			foreach($defectMeasure_array as $keysp=>$valsp)
			{
				if( $m>0 ) $data_array_defectme.=",";


				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectme.="(".$dft_me_id.",".$id.",5,".$hidden_po_break_down_id.",".$defectMeasure_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				$dft_me_id = $dft_me_id + 1;
				$m++;
			}
		}

		if($data_array_defectme!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_defectsp.""; die;
			$defectme=sql_insert("pro_gmts_prod_dft_gross",$field_array_defectMe,$data_array_defectme,0);
		}
		
		if($db_type==0)
		{
 		$rID=sql_insert("pro_gar_prod_gross_mst",$field_array,$data_array,1);
	
		}
		else
		{
			//echo "10**".$data_array;die;
		$rID=execute_query($data_array);	
		}
		//check_table_status( $_SESSION['menu_id'],0);
		//echo "10**AAA=".$defectFront.'='.$defectBk.'='.$defectWt.'='.$defectme.'='.$rID;die;
		 	  
		if($db_type==0)
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
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
				{
					oci_commit($con);  
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".$txt_mst_id;
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
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}	
 
 		// pro_gar_prod_gross_mst table data entry here 
		if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_reporting_hour;else $reportTime = 12+str_replace("'","",$txt_reporting_hour);
 		
	
 		$field_array="production_source*serving_company*location*production_date*production_quantity*production_type*sewing_line*supervisor*production_hours*remarks*floor_id*reject_qnty*alter_qnty*prod_reso_allo*spot_qnty*updated_by*update_date";
	    if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_sewing_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
		$data_array="".$cbo_source."*".$cbo_sewing_company."*".$cbo_location."*'".str_replace("'","",$txt_sewing_date)."'*".$txt_sewing_qty."*5*".$cbo_sewing_line."*".$txt_super_visor."*".$txt_reporting_hour."*".$txt_remark."*".$cbo_floor."*".$txt_reject_qnty."*".$txt_alter_qnty."*".$prod_reso_allo."*".$txt_spot_qnty."*".$user_id."*'".$pc_date_time."'";
		$defectQ=true;
		$data_array_defect="";
		$save_string=explode(",",str_replace("'","",$save_data));
		$dft_front_id=="";
		
		if(count($save_string)>0 && str_replace("'","",$save_data)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft_gross", 1);
			$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_array=array();
			for($i=0;$i<count($save_string);$i++)
			{
				$order_dtls=explode("**",$save_string[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_array) )
				{
					$defect_array[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_array[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$f=0;
			foreach($defect_array as $key=>$val)
			{
				if( $f>0 ) $data_array_defect.=",";
				$defectPointId=$key;
				$defect_qty=$val;
				$data_array_defect.="(".$dft_id.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defect_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				$dft_id = $dft_id + 1;
				$f++;
			}
		}
		if($data_array_defect!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft_gross (".$field_array_defect.") VALUES ".$data_array_defect.""; die;
			$del_deft1=execute_query("DELETE FROM pro_gmts_prod_dft_gross WHERE mst_id=$txt_mst_id and defect_type_id=1 and production_type=5");
			$defectFront=sql_insert("pro_gmts_prod_dft_gross",$field_array_defect,$data_array_defect,1);
		}
	//Front part End
		$defectBack=true;
		$data_array_defectbk="";
		$save_dataStringBack=explode(",",str_replace("'","",$save_dataBack));
		$dftbk_id=="";
		
		if(count($save_dataStringBack)>0 && str_replace("'","",$save_dataBack)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_bk_id=return_next_id("id", "pro_gmts_prod_dft_gross", 1);
			$field_array_defectbk="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectBack_array=array();
			for($i=0;$i<count($save_dataStringBack);$i++)
			{
				$order_dtls=explode("**",$save_dataStringBack[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectBack_array) )
				{
					$defectBack_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectBack_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$b=0;
			foreach($defectBack_array as $keysp=>$valsp)
			{
				if( $b>0 ) $data_array_defectbk.=",";

				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectbk.="(".$dft_bk_id.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectBack_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				$dft_bk_id = $dft_bk_id + 1;
				$b++;
			}
		}

		if($data_array_defectbk!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft_gross (".$field_array_defectbk.") VALUES ".$data_array_defectbk.""; die;
			$del_deft2=execute_query("DELETE FROM pro_gmts_prod_dft_gross WHERE mst_id=$txt_mst_id and defect_type_id=2 and production_type=5");
			$defectBk=sql_insert("pro_gmts_prod_dft_gross",$field_array_defectbk,$data_array_defectbk,1);
			//echo "10**=".$defectBk.'ASA';die;
		}
		//Back part End
		$defectWest=true;
		$data_array_defectWt="";
		$save_dataStringWest=explode(",",str_replace("'","",$save_dataWest));
		$dftwt_id=="";
		
		if(count($save_dataStringWest)>0 && str_replace("'","",$save_dataWest)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_wt_id=return_next_id("id", "pro_gmts_prod_dft_gross", 1);
			$field_array_defectwt="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectWest_array=array();
			for($i=0;$i<count($save_dataStringWest);$i++)
			{
				$order_dtls=explode("**",$save_dataStringWest[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectWest_array) )
				{
					$defectWest_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectWest_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$w=0;
			foreach($defectWest_array as $keysp=>$valsp)
			{
				if( $w>0 ) $data_array_defectwt.=",";

				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectwt.="(".$dft_wt_id.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectWest_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				$dft_wt_id = $dft_wt_id + 1;
				$w++;
			}
		}

		if($data_array_defectwt!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectwt.") VALUES ".$data_array_defectwt.""; die;
			$del_deft3=execute_query("DELETE FROM pro_gmts_prod_dft_gross WHERE mst_id=$txt_mst_id and defect_type_id=3 and production_type=5");
			$defectWt=sql_insert("pro_gmts_prod_dft_gross",$field_array_defectwt,$data_array_defectwt,1);
		}
		//West Band End
		$defectMeasure=true;
		$data_array_defectM="";
		$save_dataStringMeasure=explode(",",str_replace("'","",$save_dataMeasure));
		$dftwt_id=="";
		$dft_me_id=return_next_id("id", "pro_gmts_prod_dft_gross", 1);
		if(count($save_dataStringMeasure)>0 && str_replace("'","",$save_dataMeasure)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defectMe="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectMeasure_array=array();
			for($i=0;$i<count($save_dataStringMeasure);$i++)
			{
				$order_dtls=explode("**",$save_dataStringMeasure[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectMeasure_array) )
				{
					$defectMeasure_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectMeasure_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$m=0;
			foreach($defectMeasure_array as $keysp=>$valsp)
			{
				if( $m>0 ) $data_array_defectme.=",";

				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectme.="(".$dft_me_id.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectMeasure_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				$dft_me_id = $dft_me_id + 1;
				$m++;
			}
		}

		if($data_array_defectme!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft_gross (".$field_array_defectMe.") VALUES ".$data_array_defectme.""; die;
			$del_deft4=execute_query("DELETE FROM pro_gmts_prod_dft_gross WHERE mst_id=$txt_mst_id and defect_type_id=4 and production_type=5");
			$defectme=sql_insert("pro_gmts_prod_dft_gross",$field_array_defectMe,$data_array_defectme,0);
		}
 		$rID=sql_update("pro_gar_prod_gross_mst",$field_array,$data_array,"id","".$txt_mst_id."",1);
	   // echo "10**".$rID.'='.$defectFront.'='.$defectBk.'='.$defectWt.'='.$defectme;die;
		//check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
				if($rID )
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
		if($db_type==2 || $db_type==1 )
		{
			//echo $rID."**".$txt_mst_id;
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
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
 		
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// echo $txt_mst_id;die;
		
 		$rID =sql_update("pro_gar_prod_gross_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',str_replace("'","",$txt_mst_id),1);
		//$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		
 		if($db_type==0)
		{
			if($rID)
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
				echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".($txt_mst_id); 
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


if($action=="defect_data")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$caption_name="";
	if($type==1) $caption_name="Front Check Qty";
	else if($type==2) $caption_name="Back Part Check Qty";
	else if($type==3) $caption_name="WestBand Check Qty";
	else if($type==4) $caption_name="Measurement Check Qty";
	?>
    <script>
		function fnc_close()
		{
			var save_string='';	var tot_defect_qnty='';
			var defect_id_array = new Array();
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtDefectId=$(this).find('input[name="txtDefectId[]"]').val();
				var txtDefectQnty=$(this).find('input[name="txtDefectQnty[]"]').val();
				var txtDefectUpdateId=$(this).find('input[name="txtDefectUpdateId[]"]').val();		
				tot_defect_qnty=tot_defect_qnty*1+txtDefectQnty*1;
				//				
				if(txtDefectQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtDefectUpdateId+"**"+txtDefectId+"**"+txtDefectQnty;
					}
					else
					{
						save_string+=","+txtDefectUpdateId+"**"+txtDefectId+"**"+txtDefectQnty;
					}
					
					if( jQuery.inArray( txtDefectId, defect_id_array) == -1 ) 
					{
						defect_id_array.push(txtDefectId);
					}
				}
			});
			//alert (save_string);
			//var defect_type_id=
			$('#defect_type_id').val();
			$('#save_string').val( save_string );
			$('#tot_defectQnty').val( tot_defect_qnty );
			$('#all_defect_id').val( defect_id_array );
			parent.emailwindow.hide();
		}
	</script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../",$permission,1); ?>
            <fieldset style="width:350px;">
                <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="<? echo $save_data; ?>">
                <input type="hidden" name="tot_defectQnty" id="tot_defectQnty" class="text_boxes" value="<? echo $defect_qty; ?>">
                <input type="hidden" name="all_defect_id" id="all_defect_id" class="text_boxes" value="<? echo $all_defect_id; ?>">
                <input type="hidden" name="defect_type_id" id="defect_type_id" class="text_boxes" value="<? echo $type; ?>">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="340">  
            	<thead>
                	<tr><th colspan="3"><? echo $caption_name; ?></th></tr>
                	<tr><th width="40">SL</th><th width="150">Defect Name</th><th>Defect Qty</th></tr>
                </thead>
            </table>
            <div style="width:340px; max-height:300px; overflow-y:scroll" id="list_container" align="left"> 
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">  
                    <?
					if($type==1)//Front
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);
						
						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==2)//Back part
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);
						
						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==3) //West part
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);
						
						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==4) //Measure
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);
						
						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
                        foreach($sew_fin_measurment_check_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					
                    ?>
                </table>
            </div>
			<table width="320" id="table_id">
				 <tr>
					<td align="center" colspan="3">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>
				</tr>
			</table>
            </fieldset>
        </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
  <?	
}
if($action=="sewing_output_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$order_library=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	$sewing_library=return_library_array( "select id, line_name from  lib_sewing_line", "id", "line_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$line_data_variable=return_library_array("select id, line_number from prod_resource_mst", "id","line_number");
	
	$sql="select id, company_id, challan_no, sewing_line, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_hour, production_quantity, production_type, remarks, floor_id, sewing_line, alter_qnty, reject_qnty, spot_qnty from pro_gar_prod_gross_mst where production_type=5 and id='$data[1]' and status_active=1 and is_deleted=0 ";
	//echo $sql;
	$dataArray=sql_select($sql);
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result['plot_no']; ?> 
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result['zip_code']; ?> 
						Province No: <?php echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><? echo $data[2];  ?> Challan</strong></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add"); 
                foreach ($nameArray as $result)
                { 
                    $address="";
                    if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
				foreach($dataArray as $row)
				{
					$job_no=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"job_no");
					$buyer_val=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"buyer_name");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
				}
            ?> 
        	<td width="270" rowspan="4" valign="top" colspan="2"><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></td>
            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?></td>
            <td width="125"><strong>Buyer:</strong></td><td width="175px"><? echo $buyer_library[$buyer_val]; ?></td>
        </tr>
        <tr>
            <td><strong>Job No :</strong></td><td width="175px"><? echo $job_no; ?></td>
            <td><strong>Style Ref.:</strong></td> <td width="175px"><? echo $style_val; ?></td>
        </tr>
        <tr>
        	<td><strong>Item:</strong></td> <td width="175px"><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
            <td><strong>QC Pass Qty:</strong></td><td width="175px"><? echo $dataArray[0][csf('production_quantity')]; ?></td>
        </tr>
        <tr>
            <td><strong>Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Input Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Sewing Line: </strong></td><td width="175px"><? echo $sewing_library[$line_data_variable[$dataArray[0][csf('sewing_line')]]]; ?></td>
            <td><strong>Reporting Hour:</strong></td> <td width="175px"><? echo $dataArray[0][csf('production_hour')]; ?></td>
            <td><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Alter Qty: </strong></td><td width="175px"><? echo $dataArray[0][csf('alter_qnty')]; ?></td>
            <td><strong>Spot Qty:</strong></td> <td width="175px"><? echo $dataArray[0][csf('spot_qnty')]; ?></td>
            <td><strong>Reject Qty:</strong></td> <td width="175px"><? echo $dataArray[0][csf('reject_qnty')]; ?></td>
        </tr>
        <tr>
            <td colspan="6"><strong><p>Remarks:  <? echo $dataArray[0][csf('remarks')]; ?></p></strong></td>
        </tr>
    </table>
    <br>
        <?
			$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_gar_prod_gross_mst a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[size_number_id]]=$row[size_number_id];
				$qun_array[$row[color_number_id]][$row[size_number_id]]=$row[production_qnty];
			}
			
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_gar_prod_gross_mst a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[color_number_id]]=$row[color_number_id];
			}
			
			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?> 
         	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
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
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
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
            echo signature_table(29, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}
?>