<?
session_start();
include('../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
 
if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select finishing_update,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("finishing_update")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
 	exit();
}

 
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/finish_input_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );     	 
	exit();
}

if ($action=="load_drop_down_floor")
{	
	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
}

if($action=="load_drop_down_source")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	
	if($data==3)
		echo create_drop_down( "cbo_iron_company", 170, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "",0,0 );  
	else if($data==1)
 		echo create_drop_down( "cbo_iron_company", 170, "select id,company_name from lib_company where is_deleted=0 and status_active=1 order by company_name","id,company_name", 1, "--- Select ---", $selected_company, "",0,0 ); 
 	else
		echo create_drop_down( "cbo_iron_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );	
			
	exit();
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
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where find_in_set($company,tag_company) and status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');				
				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				} 
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:230px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}																																													
																																												
		}
	
	function js_set_value(id,item_id,po_qnty,plan_qnty)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id); 
		$("#hidden_po_qnty").val(po_qnty);
  		parent.emailwindow.hide();
 	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >

<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
    		<tr>
        		<td align="center" width="100%">
            		<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
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
                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'finish_input_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" height="40" valign="middle">
					<? echo load_month_buttons();  ?>
                    <input type="hidden" id="hidden_mst_id">
                    <input type="hidden" id="hidden_grmtItem_id">
                    <input type="hidden" id="hidden_po_qnty">
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
	if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
		
 	$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity ,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b 
			where
			a.job_no = b.job_no_mst and
			a.status_active=1 and 
			a.is_deleted=0 and
			a.garments_nature=$garments_nature
			$sql_cond"; 
	//echo $sql;die;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$arr=array(3=>$buyer_arr,7=>$company_arr);
	//echo  create_list_view("list_view", "SL, Ship. Date, Order No, Buyer, Style, Item, Order Qnty, Company Name", "90,120,100,100,200,100,100,100","1000","320",0, $sql , "js_set_value", "id", "", 1, "0,0,0,buyer_name,0,0,0,company_name", $arr , " ", "",'','0,0,0,0,0,0,2,0') ;
	?>
    
    <div style="width:920px;">
     	<table cellspacing="0" width="100%" class="rpt_table">
            <thead>
                <th width="50" >SL</th>
                <th width="100" >Shipment Date</th>
                <th width="100" >Order No</th>
                <th width="100" >Buyer</th>
                <th width="150" >Style</th>
                <th width="150" >Item</th>
                <th width="100" >Order Qnty</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:920px; max-height:220px;overflow-y:scroll;" >	 
        <table cellspacing="0" width="100%" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
                
				$exp_grmts_item = explode("__",$row["set_break_down"]);
				$numOfItem = count($exp_grmts_item);
				$set_qty="";$grmts_item="";
				for($k=0;$k<$numOfItem;$k++)								
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}
 					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[id];?>,'<? echo $grmts_item;?>',<? echo $row[po_quantity]*$set_qty;?>,<? echo $row[plan_cut]*$set_qty;?>);" > 
							<td width="50" align="center"><?php echo $i; ?></td>
							<td width="100" align="center"><?php echo change_date_format($row["shipment_date"]);?></td>		
							<td width="100" align="center"><?php echo $row["po_number"]; ?></td>
							<td width="100"><?php echo $buyer_arr[$row["buyer_name"]];  ?></td>	
							<td width="150"><?php echo $row["style_ref_no"]; ?></td>
							<td width="150"><?php  echo $garments_item[$grmts_item];?> </td>	
							<td width="100" align="right"><?php echo $row["po_quantity"]*$set_qty;?> </td>
							<td><?php  echo $company_arr[$row["company_name"]];?> </td> 	
						</tr>
					<? 
					$i++;
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
  		  		 
   		$dataArray=sql_select("select SUM(CASE WHEN production_type=5 THEN production_quantity END) as totalinput,SUM(CASE WHEN production_type=6 THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]."  and item_number_id='$item_id' and status_active=1 and is_deleted=0");
 		foreach($dataArray as $row)
		{  
			echo "$('#txt_sewing_quantity').val('".$row['totalinput']."');\n";
			echo "$('#txt_cumul_iron_qty').val('".$row['totalsewing']."');\n";
			$yet_to_produced = $row['totalinput']-$row['totalsewing'];
			echo "$('#txt_yet_to_iron').val('".$yet_to_produced."');\n";
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
		
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		
		//#############################################################################################//
		// order wise - color level, color and size level
		
		if( $variableSettings==2 ) // color level
		{
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=5 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=6 and cur.is_deleted=0 ) as cur_production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and is_deleted=0 and status_active=1 group by color_number_id";
		}
		else if( $variableSettings==3 ) //color and size level
		{
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=5 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=6 and cur.is_deleted=0 ) as cur_production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
		}
		else // by default color and size level
		{
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=5 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=6 and cur.is_deleted=0 ) as cur_production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
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
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];	
?>	 
	<div style="width:100%;">
		<table cellspacing="0" width="100%" class="rpt_table">
				<thead>
					<th width="50">SL</th>
                    <th width="150" align="center">Item Name</th>
					<th width="120" align="center">Production Date</th>
					<th width="120" align="center">Production Qnty</th>                    
					<th width="120" align="center">Reporting Hour</th>
					<th width="150" align="center">Serving Company</th>
					<th width="" align="center">Location</th>
				</thead>
         </table>
     </div>
     <div style="width:100%;max-height:180px; overflow:y-scroll" id="iron_list_view" align="left">
		<table cellspacing="0" width="100%" class="rpt_table">
		<?php  
			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("select id,po_break_down_id,item_number_id,production_date,production_quantity,production_source,production_source,production_hour,serving_company,location from pro_garments_production_mst where po_break_down_id='$po_id' and item_number_id='$item_id' and production_type='6' and status_active=1 and is_deleted=0 order by id");
			foreach($sqlResult as $selectResult){
				
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('production_quantity')];
 		?>
        
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_input_form_data','requires/finish_input_controller');" > 
				<td width="50" align="center"><? echo $i; ?></td>
                <td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                <td width="120" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                <td width="120" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
                
 				<?php  
					if($selectResult[csf('production_hour')]>12)
						$hour = ($selectResult[csf('production_hour')]-12)." PM";
					else if($selectResult[csf('production_hour')]==12)
						$hour = "00:00 AM";
					else
						$hour = $selectResult[csf('production_hour')]." AM";	
				?> 
                <td width="120" align="center"><?php echo $hour; ?></td>
				<?php
                       $source= $selectResult[csf('production_source')];
					   if($source==3)
							$serving_company= return_field_value("supplier_name","lib_supplier","id='".$selectResult[csf('serving_company')]."'");
						else
							$serving_company= return_field_value("company_name","lib_company","id='".$selectResult[csf('serving_company')]."'");
                ?>	
                <td width="150" align="center"><p><?php echo $serving_company; ?></p></td>
 				<?php 
 					$location_name= return_field_value("location_name","lib_location","id='".$selectResult[csf('location')]."'");
				?>
                <td width="" align="center"><? echo $location_name; ?></td>
			</tr>
			<?php
			$i++;
			}
			?>
            <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>-->
		</table>
        </div>
	<?
	
}



if($action=="populate_input_form_data")
{
	  
//'garments_nature*cbo_company_name*sewing_production_variable*hidden_po_break_down_id*hidden_colorSizeID*cbo_buyer_name*txt_style_no*cbo_item_name*txt_order_qty*cbo_source*cbo_sewing_company*cbo_location*cbo_floor*txt_iron_date*txt_reporting_hour*cbo_time*txt_iron_qty*txt_remark*txt_sewing_quantity*txt_cumul_iron_qty*txt_yet_to_iron*hidden_break_down_html*txt_mst_id',"../../"
	
	//production type=6 come from array
	$sqlResult =sql_select("select id,garments_nature,po_break_down_id,item_number_id,production_source,serving_company,sewing_line,location,embel_name,embel_type,production_date,production_quantity,production_source,production_source,production_type,entry_break_down_type,production_hour,sewing_line,supervisor,carton_qty,remarks,floor_id,alter_qnty,reject_qnty,total_produced,yet_to_produced  from pro_garments_production_mst where id='$data' and production_type='6' and status_active=1 and is_deleted=0 order by id");
  	//echo "sdfds".$sqlResult;die;
	foreach($sqlResult as $result)
	{ 
		
		echo "$('#txt_iron_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/finish_input_controller', ".$result[csf('production_source')].", 'load_drop_down_source', 'iron_company_td' );\n";
		echo "$('#cbo_iron_company').val('".$result[csf('serving_company')]."');\n";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/finish_input_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		
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
		echo "$('#txt_reporting_hour').val('".$hour."');\n";
		echo "$('#cbo_time').val('".$time."');\n";
 		echo "$('#txt_iron_qty').val('".$result[csf('production_quantity')]."');\n";
 		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		
 		$input_qty = $result[csf('total_produced')]+$result[csf('yet_to_produced')];
 		echo "$('#txt_sewing_quantity').val('".$input_qty."');\n";
		echo "$('#txt_cumul_iron_qty').val('".$result[csf('total_produced')]."');\n";		
		echo "$('#txt_yet_to_iron').val('".$result[csf('yet_to_produced')]."');\n"; 
		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_iron_input',1);\n";
		
		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		if( $variableSettings!=1 ) // gross level
		{ 
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			
			$sql_dtls = sql_select("select color_size_break_down_id,production_qnty,size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id'");	
			foreach($sql_dtls as $row)
			{				  
			  
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
			}  
			 
			if( $variableSettings==2 ) // color level
			{
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=5 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=6 and cur.is_deleted=0 ) as cur_production_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and is_deleted=0 and status_active=1 group by color_number_id";
			}
			else if( $variableSettings==3 ) //color and size level
			{
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=5 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=6 and cur.is_deleted=0 ) as cur_production_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
			}
			else // by default color and size level
			{
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=5 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=6 and cur.is_deleted=0 ) as cur_production_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
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
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';				
					$totalQnty += $amount;
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
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ></td></tr>';				
					$colorWiseTotal += $amount;
				}
				
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
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
 		
 		//$id=return_next_id("id", "pro_garments_production_mst", 1);
 		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );

		if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_reporting_hour;else $reportTime = 12+str_replace("'","",$txt_reporting_hour);
		//production_type array	
  		$field_array="id, garments_nature, po_break_down_id, item_number_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, production_hour, remarks, floor_id, total_produced, yet_to_produced, inserted_by, insert_date"; 
		$data_array="(".$id.",".$garments_nature.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_source.",".$cbo_iron_company.",".$cbo_location.",".$txt_iron_date.",".$txt_iron_qty.",6,".$sewing_production_variable.",".$reportTime.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_iron_qty.",".$txt_yet_to_iron.",".$user_id.",'".$pc_date_time."')";
 		$rID=sql_insert("pro_garments_production_mst",$field_array,$data_array,1);
		//echo $data_array;die;
		
		
		// pro_garments_production_dtls table entry here ----------------------------------///
		$field_array="id, mst_id,production_type,color_size_break_down_id,production_qnty";
  		
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{		
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and color_mst_id!=0 order by id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}	
			
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 			$rowEx = explode("**",$colorIDvalue); 
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				
				//6 for finish Input Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

				if($j==0)$data_array = "(".$dtls_id.",".$id.",6,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
				else $data_array .= ",(".$dtls_id.",".$id.",6,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
				//$dtls_id=$dtls_id+1;							
 				$j++;								
			}
 		}//color level wise
		
		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{		
				
			$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name order by size_number_id,color_number_id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val['id'];
			}	
			
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
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
 				
				//6 for finish Input Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

				if($j==0)$data_array = "(".$dtls_id.",".$id.",6,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array .= ",(".$dtls_id.",".$id.",6,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
			//	$dtls_id=$dtls_id+1;
 				$j++;
			}
		}//color and size wise
		
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{ 
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
		
		//release lock table
	//	check_table_status( $_SESSION['menu_id'],0);
		 	  
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
		}else{
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
			echo "0**".$rID;
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
 
 		// pro_garments_production_mst table data entry here 
		if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_reporting_hour;else $reportTime = 12+str_replace("'","",$txt_reporting_hour);
 		$field_array="production_source*serving_company*location*production_date*production_quantity*production_type*entry_break_down_type*production_hour*remarks*floor_id*total_produced*yet_to_produced*updated_by*update_date";
		$data_array="".$cbo_source."*".$cbo_iron_company."*".$cbo_location."*'".change_date_format(str_replace("'","",$txt_iron_date),'yyyy-mm-dd')."'*".$txt_iron_qty."*6*".$sewing_production_variable."*".$reportTime."*".$txt_remark."*".$cbo_floor."*".$txt_cumul_iron_qty."*".$txt_yet_to_iron."*".$user_id."*'".$pc_date_time."'";
 		$rID=sql_update("pro_garments_production_mst",$field_array,$data_array,"id","".$txt_mst_id."",1);
		//echo $data_array;die;
		
		// pro_garments_production_dtls table data entry here 
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) // check is not gross level
		{
			
			$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty";
			
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and color_mst_id!=0 order by id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					//6 for finish Input Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",6,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",6,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
				//	$dtls_id=$dtls_id+1;							
					$j++;								
				}
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
					
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name order by size_number_id,color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val['id'];
				}	
				
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
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
					//6 for finish Input Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",6,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",6,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
			
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		
		}//end cond
		
		
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
			}else{
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
			echo "0**".$rID;
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
			echo "0**".$rID;
		}
		disconnect($con);
		die;
	}
 
}
 

?>