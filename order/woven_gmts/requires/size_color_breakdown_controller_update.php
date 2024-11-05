<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  Ashraful
Converted Date           :  24-05-2014
Purpose			         : 	This form will create Knit Garments Order Entry
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	13-10-2012
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :From this version oracle conversion is start
----------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
//---------------------------------------------------- Start------------------------------------
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );

if ($action=="order_popup")
{
  	echo load_html_head_contents("Sample Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	function js_set_value( po_id )
	{
		//alert(po_id)
		document.getElementById('po_id').value=po_id;
		parent.emailwindow.hide();
	}
    </script>
</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="700" cellspacing="0" cellpadding="0" border="0">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>                	 
                        <th width="150">Company Name</th><th width="150">Buyer Name</th><th width="200">Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_job"/> <input type="hidden" id="po_id">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'size_color_breakdown_controller_update', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>
                        </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
					?>	</td>
                    <td>
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:86px">
					<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:86px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_po_search_list_view', 'search_div', 'size_color_breakdown_controller_update', 'setFilterGrid(\'list_view\',-1)')" style="width:170px;" /></td>
        		</tr>
                <tr>
                <td  align="center" height="40" valign="middle" colspan="4"><? echo load_month_buttons();  ?>
               </td>
            </tr>
             </table>
          </td>
        </tr>
        <tr>
            <td align="center"valign="top" id="search_div"> 
	
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

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
} 



if($action=="create_po_search_list_view")
{
	
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else { echo "Please Select Buyer First."; die; }
	
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$arr=array (1=>$comp,2=>$buyer_arr);
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1  $shipment_date $company $buyer order by a.job_no";  
		 
		echo  create_list_view("list_view", "Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "90,120,100,100,90,90,90,80","1000","320",0, $sql , "js_set_value", "id", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "",'','0,0,0,0,1,0,1,3');
}







if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select a.garments_nature,a.job_no,a.company_name,a.buyer_name,a.location_name,a.style_ref_no,a.style_description,a.product_dept,a.currency_id,a.agent_name,a.order_repeat_no,a.region,team_leader,a.dealing_marchant,a.packing,remarks,a.ship_mode,a.order_uom,a.gmts_item_id,a.set_break_down,a.total_set_qnty,b.id,b.po_number,b.po_quantity,b.plan_cut,b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst and  b.id='$data' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
	foreach ($data_array as $row)
	{
		//echo "reset_form('sizecolormaster_1','size_color_breakdown','')\n";
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";
		echo "document.getElementById('txt_order_no').value = '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('order_id').value = '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";  
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";  
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";  
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		echo "document.getElementById('txt_ship_date').value = '".change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy","-")."';\n"; 
		echo "document.getElementById('txt_po_qnty').value = '".$row[csf("po_quantity")]."';\n"; 
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('txt_plan_cut_qnty').value = '".$row[csf("plan_cut")]."';\n"; 
		echo "document.getElementById('cbo_order_uom_2').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";  
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";  
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n";  
	}
	$sql_data=sql_select( "select po_break_down_id, sum(production_quantity) as production_quantity from  pro_garments_production_mst where po_break_down_id='$data' and production_type=1 and  status_active=1 and is_deleted=0 group by po_break_down_id");
	foreach($sql_data as $row_data)
	{
		if($row_data[csf('production_quantity')]>0)
		{
			echo "$('#cbo_po_country').attr('disabled','true')".";\n";
			echo "$('#cbo_po_country').attr('title','Cutting Qty Found')".";\n";
		}
		else{
			echo "$('#cbo_po_country').removeAttr('disabled')".";\n";
			echo "$('#cbo_po_country').removeAttr('title')".";\n";
		}
	}
}

if ($action=="populate_size_color_breakdown")
{
	extract($_REQUEST);
	$data=explode("_",$data);
	$order_id=$data[0];
	$po_break_down_data=sql_select("select a.excess_cut,a.po_quantity,a.po_number,a.unit_price,b.company_name, 	b.buyer_name,b.gmts_item_id,b.order_uom,b.set_break_down,b.total_set_qnty from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id='$order_id'");
	list($po_break_down_data_array )=$po_break_down_data;
	$company_name=$po_break_down_data_array[csf(company_name)];
	$buyer_name=$po_break_down_data_array[csf(buyer_name)];
	$gmt_item_id=$po_break_down_data_array[csf(gmts_item_id)];
	$order_uom=$po_break_down_data_array[csf(order_uom)];
	$set_break_down=$po_break_down_data_array[csf(set_break_down)];
	$total_set_qnty=$po_break_down_data_array[csf(total_set_qnty)];
	$order_number=$po_break_down_data_array[csf(po_number)];
	$po_qnty=$po_break_down_data_array[csf(po_quantity)];
	$excess_cut=$po_break_down_data_array[csf(excess_cut)];
	if($order_uom==58)
	{
		$unit_price=$po_break_down_data_array[csf(unit_price)]/$total_set_qnty;
	}
	else
	{
		$unit_price=$po_break_down_data_array[csf(unit_price)];
	}
   $color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$company_name  and variable_list=23  and status_active=1 and is_deleted=0");
   if($color_from_library==1)
   {
	   $readonly="readonly";
	   $plachoder="placeholder='Click'";
	   $onClick="onClick='color_select_popup($buyer_name,this.id)'";
   }
   else
   {
	   $readonly="";
	   $plachoder="";
	   $onClick="";
   }

	?>
    <fieldset style="width:1180px;">
    <input type="hidden" id="order_id" value="<? echo $order_id ; ?>"> 
    <input type="hidden" id="txt_buyer_id" value="<? echo $buyer_name ; ?>"> 
    <input type="hidden" id="total" value="<? echo $po_qnty; ?>" />
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="rpt_table" id="size_color_break_down_list" rules="all">
	<thead>
        <tr>
        <th width="180" >Gmts Item</th><th width="120">Article Number</th><th width="120">Color</th><th width="80">Size</th><th width="80">Order Qnty</th><th width="80">Rate</th><th width="80"> Amount</th><th width="50">Excess Cut %</th><th width="80">Plan Cut Qnty.</th><th width="80">Status</th> <th></th>
        </tr>
	</thead>
	<tbody id="data_form">
    <tr id="break_1"  onClick="tr_index(this)">
            <td>
            <?
				echo create_drop_down( "cbogmtsitem_1", 172, $garments_item,"", 1, "-- Select Item --", $selected, "calculate_total_amnt( 1 );check_duplicate(1,this.id)",1,$gmt_item_id,1); 
			?>
            </td>
            <td>
            <input type="text" id="txtarticleno_1" name="txtarticleno_1" onChange="check_duplicate(1,this.id)" class="text_boxes" style="width:110px"  disabled /> 
            </td>
            <td>
            <input type="text" id="txtcolor_1" onChange="check_duplicate(1,this.id)" name="txtcolor_1" class="text_boxes" style="width:110px" <? echo $readonly." ".$onClick." ".$plachoder; ?>  disabled/> 
            </td>

            <td>
            <input type="text" id="txtsize_1" name="txtsize_1" onChange="check_duplicate(1,this.id)" class="text_boxes" style="width:70px" disabled /> 
            </td>
            <td>
            <input type="text" id="txtorderquantity_1" name="txtorderquantity_1" onBlur="set_excess_cut(this.value,document.getElementById('txtorderexcesscut_1').value, 1)" class="text_boxes_numeric" style="width:75px" disabled/> 
            </td>
            <td>
            <input type="text" id="txtorderrate_1" onBlur="calculate_total_amnt( 1 )" value="<? echo $unit_price; ?>"  name="txtorderrate_1"  class="text_boxes_numeric" style="width:75px" />
            </td>
            <td>
            <input type="text" id="txtorderamount_1" name="txtorderamount_1" readonly class="text_boxes_numeric" style="width:75px" disabled/> 
            </td>
            <td>
            <input type="text" id="txtorderexcesscut_1" onBlur="set_excess_cut(document.getElementById('txtorderquantity_1').value, this.value, 1)" value="<? echo $excess_cut; ?>" name="txtorderexcesscut_1" class="text_boxes_numeric" style="width:45px" disabled/> 
            </td>
            <td>
            <input type="text" id="txtorderplancut_1"    name="txtorderplancut_1" class="text_boxes_numeric" style="width:75px" disabled /> 
            </td>
            <td>
            <? echo create_drop_down( "cbostatus_1", 80, $row_status, 0, "", 1, "","",1 );  ?>
            </td>   
            <td>
            <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
            <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'size_color_break_down_list' );"/>
            <input type="hidden" id="hiddenid_1" name="hiddenid_1" value="" style="width:15px;" class="text_boxes"/>
            </td>  
        </tr>
        </tbody>
	</table>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
        <tr style="text-align:right">
            <td width="180">
            
            </td>
            <td width="122">
            </td>
            <td width="122">
            <input type="text" id="txt_total_order_item_qnty"  name="txt_total_order_item_qnty"  class="text_boxes" style="width:110px;text-align:right;" disabled />
            </td>
            <td width="82">
            <input type="text" id="txt_total_order_item_yetto_qnty"  name="txt_total_order_item_yetto_qnty" class="text_boxes" style="width:70px;text-align:right;" disabled />
            </td>
            <td width="80">
            <input type="text" id="txt_total_order_qnty" name="txt_total_order_qnty" value="<? //echo $total_qnty; ?>"    class="text_boxes" style="width:75px;text-align:right;" disabled>
            </td>
            <td width="87">
            <input type="text" id="txt_avg_rate"  name="txt_avg_rate" value="<? //echo $total_amount/$total_qnty; ?>" class="text_boxes" style="width:75px;text-align:right;" disabled>
            </td>
            <td width="86">
            <input type="text" id="txt_total_amt"  name="txt_total_amt" value="<? //echo $total_amount; ?>"   class="text_boxes" style="width:75px;text-align:right;" disabled>
            </td>
            <td width="47">
            <input type="text" id="txt_avg_excess_cut"  name="txt_avg_excess_cut" value="<? //echo number_format(((($total_plan_cut-$total_qnty)/$total_qnty)*100),2); ?>"  class="text_boxes" style="width:45px;text-align:right;" disabled>
            </td>
            <td width="86">
            <input type="text" id="txt_total_plan_cut"  name="txt_total_plan_cut" value="<? //echo $total_plan_cut; ?>" class="text_boxes" style="width:75px;text-align:right;" disabled>
            </td>
            <td width="80"></td>
            <td></td>  
        </tr>
        
        <tr>
            <td align="center" colspan="11"  class="button_container">
            
            <?
				echo load_submit_buttons( $permission, "fnc_size_color_breakdown", 0,0 ,"reset_form('sizecolormaster_1','po_list_view*size_color_breakdown','')",1) ; ;
            ?>  
            
            </td> 
        </tr>
        <tr>
            <td align="center" colspan="11" id="country_po_active_listview11">
				<?
                $country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );//order_quantity
                $arr=array (0=>$order_status,4=>$country_library,6=>$unit_of_measurement);
				if($db_type==0)
				{
                $sql= "select a.is_confirmed,a.po_number,a.po_received_date,a.pub_shipment_date,c.country_remarks,c.country_id,sum(c.order_quantity) as po_quantity,b.order_uom,a.unit_price,sum(c.order_total) as po_total_price,sum(c.excess_cut_perc) as excess_cut,sum(c.plan_cut_qnty) as plan_cut,c.cutup_date,	c.cutup,c.country_ship_date,DATEDIFF(a.shipment_date,a.po_received_date) date_diff,a.status_active,a.details_remarks ,a.id from  wo_po_break_down a, wo_po_details_master b,wo_po_color_size_breakdown c  where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no_mst and c.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$order_id' group by a.id,c.country_id"; 
				}
				if($db_type==2)
				{
				   $sql= "select a.is_confirmed,a.po_number,a.po_received_date,a.pub_shipment_date,c.country_remarks,c.country_id,sum(c.order_quantity) as po_quantity,b.order_uom,a.unit_price,sum(c.order_total) as po_total_price,sum(c.excess_cut_perc) as excess_cut,sum(c.plan_cut_qnty) as plan_cut,c.cutup_date,	c.cutup,c.country_ship_date,a.status_active,a.details_remarks ,a.id from  wo_po_break_down a, wo_po_details_master b,wo_po_color_size_breakdown c  where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no_mst and c.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$order_id' group by a.id,a.is_confirmed,a.po_number,a.po_received_date,a.pub_shipment_date,c.country_remarks,a.status_active,a.details_remarks,c.country_id,c.country_id,c.cutup_date,	c.cutup,c.country_ship_date,b.order_uom,a.unit_price"; 
				}
				$data_array=sql_select($sql);
                ?>
                <table width="1065" border="0" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
                <thead>
                <tr>
                <th width="50">Sl</th> <th width="90">Order Status</th> <th width="130">PO No</th> <th width="80">PO Recv.Date</th> <th width="80">Ship Date</th> <th width="80">Country</th> <th width="80">PO Qnty</th> <th width="50">Order Uom</th> <th width="80">Avg. Rate</th> <th width="80">Amount</th> <th width="80">Excess Cut %</th><th width="80">Plan Cut Qnty</th><th>Lead Time</th>
                </tr>
                </thead>
                </table>
                <table width="1065" border="0" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
                <tbody>
                <?
				$i=0;
				$txt_tot_po_qnty=0;
				$txt_tot_avg_rate=0;
				$txt_tot_amount=0;
				$txt_tot_excess_cut=0;
				$txt_tot_plancut=0;
				foreach($data_array as $row)
				{
					$i++;
					$txt_tot_po_qnty+=$row[csf("po_quantity")];
					$txt_tot_amount+=$row[csf("po_total_price")];
					$txt_tot_plancut+=$row[csf("plan_cut")];
					$excess_cut=number_format(((($row[csf("plan_cut")]-$row[csf("po_quantity")])/$row[csf("po_quantity")])*100),4);
					$rate=number_format(($row[csf("po_total_price")]/$row[csf("po_quantity")]),2)
				?>
                <tr onClick="populate_size_color_breakdown_with_data('<? echo $row[csf("id")]."_".$row[csf("country_id")]."_".change_date_format($row[csf("country_ship_date")],"dd-mm-yyyy","-")."_".change_date_format($row[csf("cutup_date")],"dd-mm-yyyy","-")."_".$row[csf("cutup")]."_".$row[csf("country_remarks")];?>')" style="cursor:pointer">
                <td width="50"><? echo $i;?></td> <td width="90"><? echo $order_status[$row[csf("is_confirmed")]]; ?></td> <td width="130"><? echo $row[csf("po_number")]; ?></td> <td width="80"><? echo change_date_format($row[csf("po_received_date")],"dd-mm-yyyy","-"); ?></td> <td width="80"><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy","-"); ?></td> <td width="80"><? echo $country_library[$row[csf("country_id")]]; ?></td> <td width="80" align="right"><? echo number_format($row[csf("po_quantity")],0); ?></td> <td width="50"><? //echo $unit_of_measurement[$row[csf("order_uom")]]; ?>Pcs</td> <td width="80" align="right"><? echo number_format($rate,2); ?></td> <td width="80" align="right"><? echo number_format($row[csf("po_total_price")],2); ?></td> <td width="80" align="right"><? echo number_format($excess_cut,4); ?></td><td width="80" align="right"><? echo number_format($row[csf("plan_cut")],0); ?></td><td align="right"><? echo $row[csf("date_diff")]; ?></td>
                </tr>
                <?
				}
				$txt_tot_avg_rate=number_format(($txt_tot_amount/$txt_tot_po_qnty),2);
				
				$txt_tot_excess_cut=number_format(((($txt_tot_plancut-$txt_tot_po_qnty)/$txt_tot_po_qnty)*100),2)
				?>
                </tbody>
                <tfoot>
                <th width="50"></th> <th width="90"></th> <th width="130"></th> <th width="80"></th> <th width="80"></th> <th width="80"></th> <th width="80"><input type="text" class="text_boxes_numeric" id="txt_tot_po_qnty" style="width:68px" value="<? echo $txt_tot_po_qnty; ?>" disabled /></th> <th width="50"></th> <th width="80"><input type="text" class="text_boxes_numeric" id="txt_tot_avg_rate" style="width:68px" value="<? echo $txt_tot_avg_rate; ?>" disabled /></th> <th width="80"><input type="text" class="text_boxes_numeric" id="txt_tot_amount" style="width:68px" value="<? echo $txt_tot_amount; ?>" disabled /></th> <th width="80"><input type="text" class="text_boxes_numeric" id="txt_tot_excess_cut" style="width:68px" value="<? echo $txt_tot_excess_cut; ?>" disabled /></th><th width="80"><input type="text" class="text_boxes_numeric" id="txt_tot_plancut" style="width:68px" value="<? echo $txt_tot_plancut; ?>" disabled /></th><th></th>
                </tfoot>
                </table>
            </td> 
        </tr>
        
        <tr>
        <td colspan="11" align="center">
        <input type="button" id="colse_1" name="colse_1" onClick="parent.emailwindow.hide();" class="formbutton" value="Close"/>
        </td>
        </tr>
	</table>
    </fieldset>
    <?
}


if($action=="populate_size_color_breakdown_with_data")
{
	
$data=explode("_",$data);
//print_r($data);
//echo "select * from wo_po_color_size_breakdown where po_break_down_id='$data[0]' and country_id=$data[1]  and is_deleted=0 and status_active=1";
$po_break_down_data=sql_select("select a.excess_cut,a.po_quantity,a.po_number,a.unit_price,b.company_name,b.buyer_name,b.gmts_item_id,b.order_uom,total_set_qnty from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id='$data[0]'");
list($po_break_down_data_array )=$po_break_down_data;
$gmt_item_id=$po_break_down_data_array[csf(gmts_item_id)];
$company_name=$po_break_down_data_array[csf(company_name)];
	$buyer_name=$po_break_down_data_array[csf(buyer_name)];

$color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$company_name  and variable_list=23  and status_active=1 and is_deleted=0");
   if($color_from_library==1)
   {
	   $readonly="readonly='readonly'";
	   $plachoder="placeholder='Click'";
	   $onClick="onClick='color_select_popup($buyer_name,this.id)'";
   }
   else
   {
	   $readonly="";
	   $plachoder="";
	   $onClick="";
   }
   
$excess_cut_disable=$_SESSION['logic_erp']['data_arr'][122][$company_name][txt_excess_cut][is_disable];
$sql_slap=sql_select("select excut_source,editable from variable_order_tracking where company_name=$company_name and variable_list=45 and is_deleted=0 and status_active=1");
			if(count($sql_slap>0))
			{
				foreach($sql_slap as $row )
				{
					$excess_variable=$row[csf('excut_source')];
					
					if($excess_variable==2) //slap
					{
						$editable_id=$row[csf('editable')];
					}
				}
				if($editable_id=="" || $editable_id==0) $editable_id=0; else $editable_id=$editable_id;
				if($editable_id==1) //Slap//Yes
				{
					$excess_fld_disable=""; 
				}
				else if($editable_id==2 || $editable_id==0) //Slap//No
				{
					$excess_fld_disable="disabled";
				}
				else $excess_fld_disable="disabled";
			}

$break_data=sql_select("select * from wo_po_color_size_breakdown where po_break_down_id='$data[0]' and country_id=$data[1]  and is_deleted=0 and status_active=1");
foreach($break_data as $row)
{
	$k++;
	$id=$row[csf("id")];
	$total_qnty+=$row[csf("order_quantity")];
	$total_amount+=$row[csf("order_total")];
	$total_plan_cut+=$row[csf("plan_cut_qnty")];
	$item_number_id=$row[csf("item_number_id")];
	$article_number=$row[csf("article_number")];
	//$color_number_id=return_field_value('color_name','lib_color','id="'.$row[csf("color_number_id")].'"');
	//$size_number_id=return_field_value('size_name','lib_size','id="'.$row[csf("size_number_id")].'"');
	$color_number_id=$color_library[$row[csf("color_number_id")]];
	$size_number_id=$size_library[$row[csf("size_number_id")]];
	$order_quantity=$row[csf("order_quantity")];
	$order_rate=$row[csf("order_rate")];
	$order_total=$row[csf("order_total")];
	$excess_cut_perc=$row[csf("excess_cut_perc")];
	$plan_cut_qnty=$row[csf("plan_cut_qnty")];
	$status_active=$row[csf("status_active")];
	//echo $item_number_id;
?>

<tr id="break_1" onClick="tr_index(this)" >
    <td>
    <?  echo create_drop_down( "cbogmtsitem_".$k, 172, $garments_item,"", 0, "",$item_number_id, "calculate_total_amnt(".$k." );check_duplicate(".$k.",this.id)",1,$gmt_item_id); ?>
    </td>
    <td>
    <input type="text" id="txtarticleno_<? echo $k; ?>" name="txtarticleno_<? echo $k; ?>" value="<? echo $article_number; ?>" onChange="check_duplicate(1,this.id)" class="text_boxes" style="width:110px" disabled> 
    </td>
    <td>
    <input type="text" id="txtcolor_<? echo $k; ?>" onChange="check_duplicate(<? echo $k; ?> ,this.id)"name="txtcolor_<? echo $k; ?>" value="<? echo $color_number_id; ?>"  class="text_boxes" style="width:110px"  <? echo $readonly." ".$onClick ?> disabled> 
    </td>
    
    <td>
    <input type="text" id="txtsize_<? echo $k; ?>" name="txtsize_<? echo $k; ?>" onChange="check_duplicate(<? echo $k; ?>,this.id)" value="<? echo $size_number_id; ?>"  class="text_boxes" style="width:70px" disabled> 
    </td>
    <td>
    <input type="text" id="txtorderquantity_<? echo $k; ?>" name="txtorderquantity_<? echo $k; ?>" onBlur="set_excess_cut(this.value,document.getElementById('txtorderexcesscut_<? echo $k; ?>').value, <? echo $k; ?>)" value="<? echo $order_quantity; ?>"  class="text_boxes_numeric" style="width:75px" disabled> 
    </td>
    <td>
    <input type="text" id="txtorderrate_<? echo $k; ?>" onBlur="calculate_total_amnt( <? echo $k; ?> )" value="<? echo $order_rate; ?>"  name="txtorderrate_<? echo $k; ?>"  class="text_boxes_numeric" style="width:75px" > 
    </td>
    <td>
    <input type="text" id="txtorderamount_<? echo $k; ?>" name="txtorderamount_<? echo $k; ?>"  value="<? echo $order_total; ?>" readonly class="text_boxes_numeric" style="width:75px" disabled> 
    </td>
    <td>
	
    <input type="text" id="txtorderexcesscut_<? echo $k; ?>" onBlur="set_excess_cut(document.getElementById('txtorderquantity_<? echo $k; ?>').value, this.value, <? echo $k; ?>)"  name="txtorderexcesscut_<? echo $k; ?>" value="<? echo $excess_cut_perc; ?>" class="text_boxes_numeric" style="width:45px" <?php echo $excess_fld_disable;//if($excess_cut_disable==2) echo ""; else echo "disabled"; ?> />
    </td>
    <td>
    <input type="text" id="txtorderplancut_<? echo $k; ?>"    name="txtorderplancut_<? echo $k; ?>" value="<? echo $plan_cut_qnty; ?>" class="text_boxes_numeric" style="width:75px" readonly disabled /> 
    </td>
    <td>
    <? echo create_drop_down( "cbostatus_".$k, 80, $row_status, 0, "", 1, $status_active,"",1 );  ?>
    </td>  
    <td>
    <input type="button" id="increaseset_<? echo $k; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $k; ?>)" />
    <input type="button" id="decreaseset_<? echo $k; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k; ?> ,'size_color_break_down_list' );" />
  
    <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid_<? echo $k; ?>" value="<? echo $id; ?>" style="width:15px;" class="text_boxes" />
    </td>    
</tr>
<?
}
}

if($action=="check_country")
{
	$data=explode("_",$data);
	$sql="Select country_id from wo_po_color_size_breakdown where po_break_down_id=$data[0] and country_id=$data[1] and is_deleted =0 and status_active=1";
	$data_array=sql_select($sql);
	$country=count($data_array);
	echo $country;
	
}

if($action=="delete_row_color_size")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	//$rID_de1=execute_query( "delete from   wo_po_color_size_breakdown where  id =".$data."",0);
	$rID_de1=execute_query( "update wo_po_color_size_breakdown set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id =".$data."",0);
	if($db_type==0)
	{
		if($rID_de1 ){
		mysql_query("COMMIT");  
		echo "2";
		}
		else{
		mysql_query("ROLLBACK"); 
		echo "10";
		}
	}
	
	if($db_type==2 || $db_type==1 )
	{
		if($rID_de1 ){
		oci_commit($con);
		echo "2";
		}
		else{
		oci_rollback($con);
		echo "10";
		}
	}
	disconnect($con);
	die;
}
if($action=="inserted_po_qnty")
{
	$data=explode("_",$data);
	$order_quantity=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id='$data[0]' and status_active =1 and is_deleted=0 group by po_break_down_id");
	$order_quantity_country=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id='$data[0]' and country_id=$data[1] and status_active =1 and is_deleted=0 group by po_break_down_id");
	echo $order_quantity."_".$order_quantity_country;
	die;
}


if($action=="populate_size_color_breakdown_pop_up")
{
	echo load_html_head_contents("Color Size Pop Up","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	var permission='<? echo $permission; ?>';
	//alert(permission);
	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
	function openmypage(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("po_id") //Access form field with id="emailfield"
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/size_color_breakdown_controller_update" );
				show_list_view(theemail.value,'populate_size_color_breakdown','size_color_breakdown','../woven_order/requires/size_color_breakdown_controller_update','');
				//set_button_status(0, permission, 'fnc_size_color_breakdown',1);
				release_freezing();
			} 
		}
	}
	
	function fnc_size_color_breakdown( operation )
	{
		var row_num=$('#size_color_break_down_list tr').length-1;
		var data_all="";
		var po_qnty="";
		var total=(document.getElementById('total').value)*1;
		var cbo_order_uom=document.getElementById('cbo_order_uom').value;
		var tot_set_qnty=(document.getElementById('tot_set_qnty').value)*1;
		var txt_total_order_qnty=(document.getElementById('txt_total_order_qnty').value)*1;
		var order_id=document.getElementById('order_id').value;
		var txt_buyer_id=document.getElementById('txt_buyer_id').value;
		var cbo_po_country=document.getElementById('cbo_po_country').value;
		var hid_old_country=document.getElementById('hid_old_country').value;
		//alert(operation)
	/*if(operation==1 || operation==2)
	{
		
		var po_id=order_id;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'woven_order_entry_controller_update')
		var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
		if(trim(booking_no_with_approvet_status_arr[0]) !="")
		{
			var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
			if(booking_no_with_approvet_status_arr[1] !="")
			{
				al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
			}
			al_magg+=" found,\nPlease Un-approved the booking first";
			alert(al_magg)
		    return;
		}
		
		if(trim(booking_no_with_approvet_status_arr[1]) !="")
		{
			var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
			var r=confirm(al_magg);
			if(r==false)
			{
				return;
			}
			else
			{
				//continue;
			}
		    
		}
	}
		*/
		if(cbo_order_uom==58)
		{
			po_qnty	=total*tot_set_qnty;
		}
		else
		{
			po_qnty	=total;
		}
		
		//alert(inserted_po_qnty_arr[0])
		/*if(operation==0)
		{
			var inserted_po_qnty=return_global_ajax_value(order_id+'_'+cbo_po_country, 'inserted_po_qnty', '', 'size_color_breakdown_controller_update');
		    var inserted_po_qnty_arr=inserted_po_qnty.split("_");
			if((inserted_po_qnty_arr[0]*1+txt_total_order_qnty*1)>po_qnty*1)
			{
			alert("Break Down Qnty Does Not Match with Order Qnty In Pcs");
			return;
			}
			
		}*/
		
	/*	if(operation==1)
		{
			var inserted_po_qnty=return_global_ajax_value(order_id+'_'+hid_old_country, 'inserted_po_qnty', '', 'size_color_breakdown_controller_update');
		    var inserted_po_qnty_arr=inserted_po_qnty.split("_");
			if((((inserted_po_qnty_arr[0]*1+txt_total_order_qnty*1)-inserted_po_qnty_arr[1]*1))>po_qnty)
			{
			alert("Break Down Qnty Does Not Match with Order Qnty In Pcs");
			return;
			}
			
		}*/
		//alert(inserted_po_qnty_arr[0])
		//alert(inserted_po_qnty_arr[1])
		
		/*if(po_qnty>txt_total_order_qnty)
		{
			alert("Break Down Qnty Does Not Match with Order Qnty In Pcs");
			return;
		}*/
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('cbo_po_country*txt_country_ship_date*cbogmtsitem_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i,'Country*Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*Packing')==false)
			{
				return;
			}
			if($('#txtorderrate_'+i).val()==0)
			{
				alert("Fill Up Rate");	
				$('#txtorderrate_'+i).focus();
				return;
			}
			eval(get_submitted_variables('txt_job_no*order_id*txt_tot_avg_rate*txt_tot_amount*cbo_order_uom*tot_set_qnty*txt_tot_excess_cut*txt_tot_plancut*cbo_po_country*txt_country_ship_date*hiddenid_'+i+'*cbogmtsitem_'+i+'*txtarticleno_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i+'*txtorderamount_'+i+'*txtorderexcesscut_'+i+'*txtorderplancut_'+i+'*cbostatus_'+i));
			
			data_all=data_all+get_submitted_data_string('txt_country_ship_date*txt_remark*txt_cutup_date*cbo_cut_up*cbogmtsitem_'+i+'*hiddenid_'+i+'*txtarticleno_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i+'*txtorderamount_'+i+'*txtorderexcesscut_'+i+'*txtorderplancut_'+i+'*cbostatus_'+i,"../../../",i);
		}
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+'&txt_job_no='+txt_job_no+'&order_id='+order_id+'&txt_buyer_id='+txt_buyer_id+'&txt_avg_rate='+txt_tot_avg_rate+'&txt_total_amt='+txt_tot_amount+'&cbo_order_uom='+cbo_order_uom +'&tot_set_qnty='+tot_set_qnty+'&txt_avg_excess_cut='+txt_tot_excess_cut+'&txt_total_plan_cut='+txt_tot_plancut+'&cbo_po_country='+cbo_po_country+'&hid_old_country='+hid_old_country+data_all;//
		//'&txt_country_ship_date='+txt_country_ship_date+
		freeze_window(operation);
		http.open("POST","size_color_breakdown_controller_update.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponse;
	}
	
	function fnc_on_submit_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			if(trim(reponse[0]) ==12)
			{
				alert("Country Shipment Date Not Allowed");
				release_freezing();	
				return; 
			}
			//show_list_view(document.getElementById('txt_job_no').value,'show_po_active_listview','po_list_view','../woven_order/requires/size_color_breakdown_controller_update','');
			// show_list_view(document.getElementById('order_id').value,'show_country_po_active_listview','country_po_active_listview','../woven_order/requires/size_color_breakdown_controller_update','');
			show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','size_color_breakdown_controller_update','');
		    show_list_view(document.getElementById('order_id').value+"_"+document.getElementById('cbo_po_country').value,'populate_size_color_breakdown_with_data','data_form','size_color_breakdown_controller_update','');
			document.getElementById('cbo_po_country').value=0;
			document.getElementById('hid_old_country').value="";
			//document.getElementById('txt_cutup_date').value="";
			//document.getElementById('cbo_cut_up').value=0;
			//document.getElementById('txt_country_ship_date').value="";
			//$("#txt_country_ship_date").attr("disabled",false);
			document.getElementById('txt_first_range').value="";
		    document.getElementById('txt_second_range').value="";
		    document.getElementById('txt_click_range').value="";
		    document.getElementById('txt_copy_color').value="";
			document.getElementById('txt_avg_price').value="";
			document.getElementById('txt_excess_cut').value="";
			var row_num=$('#size_color_break_down_list tr').length-1;
			for (var i=1; i<=row_num; i++)
		    {
				document.getElementById('hiddenid_'+i).value="";
			}
			set_button_status(0, permission, 'fnc_size_color_breakdown',1);
			if(reponse[0] !=2)
			{
			calculate_total_amnt( 1 )
			}
			if(reponse[0] ==2)
			{
			show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','size_color_breakdown_controller_update','');
			}
			release_freezing();
		}
	}
	
	function add_break_down_tr( i )
	{
		var row_num=$('#size_color_break_down_list tr').length-1;
		if (i==0)
		{
			i=1;
			$("#txtcolor_"+i).autocomplete({
			source: str_color
			});
			$("#txtsize_"+i).autocomplete({
			source:  str_size 
			}); 
			return;
		}
		if (row_num!=i)
		{
			return false;
		}
		if (form_validation('cbogmtsitem_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i,'Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*Packing')==false)
		{
			return;
		}
		if($('#txtorderrate_'+i).val()==0)
		{
			alert("Fill Up Rate");	
			$('#txtorderrate_'+i).focus();
			return;
		}
		else
		{
			i++;
			$("#size_color_break_down_list tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			'name': function(_, name) { return name + i },
			'value': function(_, value) { return value }              
			});
			}).end().appendTo("#size_color_break_down_list");
			
			$('#cbogmtsitem_'+i).removeAttr("onChange").attr("onChange","calculate_total_amnt("+i+");check_duplicate("+i+",this.id)");
			$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#txtarticleno_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");

			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'size_color_break_down_list');");
			$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+i+"').value,"+i+")");
			$('#txtorderrate_'+i).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+i+")");
			$('#txtorderexcesscut_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+i+"').value,this.value,"+i+")");
			$('#txtsize_'+i).val('');
			// onBlur="(document.getElementById('txtorderquantity_1').value, this.value, 1)"
			var j=i-1;
			//$('#txtcolor_'+i).removeAttr("onfocus").attr("onfocus","add_break_down_tr("+j+");");
			$('#cbogmtsitem_'+i).val($('#cbogmtsitem_'+j).val()); 
			$('#cbostatus_'+i).val($('#cbostatus_'+j).val());
			$('#hiddenid_'+i).val("");
			$('#txtorderquantity_'+i).val("");
			set_all_onclick();
			$("#txtcolor_"+i).autocomplete({
			source: str_color
			});
			$("#txtsize_"+i).autocomplete({
			source:  str_size 
			}); 
			calculate_total_amnt( i )
		}
	}
	
	function copyset_tr()
	{
		var txt_first_range=document.getElementById('txt_first_range').value
		var txt_second_range=document.getElementById('txt_second_range').value
		for(var i=txt_first_range; i<=txt_second_range; i++)
		{
		
		var txt_copy_color=(document.getElementById('txt_copy_color').value).toUpperCase();
		var txtcolor=(document.getElementById('txtcolor_'+i).value).toUpperCase();
		var cbogmtsitem=(document.getElementById('cbogmtsitem_'+i).value);
		var cbogmtsitem_copy=(document.getElementById('cbogmtsitem').value);
		var row_num=$('#size_color_break_down_list tr').length-1;
		
			
			/*if(txt_copy_color==txtcolor)
			{
				
				$("#size_color_break_down_list tr:eq("+i+")").css('background-color', 'Red');
				continue;
			}*/
		
		
		row_num+=1;
		$("#size_color_break_down_list tr:eq("+i+")").clone().find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			'name': function(_, name) { return name + row_num },
			'value': function(_, value) { return value }              
			});
			}).end().appendTo("#size_color_break_down_list");
		$('#cbogmtsitem_'+row_num).removeAttr("onChange").attr("onChange","calculate_total_amnt("+row_num+");check_duplicate("+row_num+",this.id)");
			$('#txtcolor_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");
			$('#txtsize_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");
		    $('#txtarticleno_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");

			$('#increaseset_'+row_num).removeAttr("onClick").attr("onClick","add_break_down_tr("+row_num+");");
			$('#decreaseset_'+row_num).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+row_num+",'size_color_break_down_list');");
			$('#txtorderquantity_'+row_num).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+row_num+"').value,"+row_num+")");
			$('#txtorderrate_'+row_num).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+row_num+")");
			$('#txtorderexcesscut_'+row_num).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+row_num+"').value,this.value,"+row_num+")");
			if(txt_copy_color !="")
			{
			$('#txtcolor_'+row_num).val(txt_copy_color);
			}
			$('#cbogmtsitem_'+row_num).val(cbogmtsitem_copy);
			$('#hiddenid_'+row_num).val("");
			
			calculate_total_amnt( i )
		}
		
	}
	
	function fn_deletebreak_down_tr(rowNo,table_id) 
	{   
		if(table_id=='size_color_break_down_list')
		{
			var numRow = $('table#size_color_break_down_list tbody tr').length; 
			//alert (numRow);
			/*if(numRow==rowNo && rowNo!=1)
			{
				
				if($('#hiddenid_'+rowNo).val()=="")
				{
					$('#size_color_break_down_list tbody tr:last').remove();
					calculate_total_amnt( rowNo-1 );
				}
				else
				{
					//permission_array=permission.split("_");
					//alert(permission_array[2]);
					//var cbogmtsitem=$('#cbogmtsitem_'+rowNo).val();
					//var cbogmtsitem=$('#cbogmtsitem_'+rowNo).val();
					//var cbogmtsitem=$('#cbogmtsitem_'+rowNo).val();
					//var cbogmtsitem=$('#cbogmtsitem_'+rowNo).val();
					
					$('#size_color_break_down_list tbody tr:last').remove();
					calculate_total_amnt( rowNo-1 );
					//alert("Remove Restricted!");	
				}
			}
			else
			{
				var index=rowNo-1
				//$("#size_color_break_down_list tbody tr:eq("+index+")").hide()
				$("#size_color_break_down_list tbody tr:eq("+index+")").remove()
				re_order()
				calculate_total_amnt( rowNo-1 );
			}*/ 
			
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var updateid=$('#hiddenid_'+rowNo).val();
				if(updateid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(updateid, 'delete_row_color_size', '', 'size_color_breakdown_controller_update');
				}
				var index=rowNo-1
				$("table#size_color_break_down_list tbody tr:eq("+index+")").remove()
				var numRow = $('table#size_color_break_down_list tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
								'value': function(_, value) { return value }             
							}); 
						$('#cbogmtsitem_'+i).removeAttr("onChange").attr("onChange","calculate_total_amnt("+i+");check_duplicate("+i+",this.id)");
						$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
						$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
					    $('#txtarticleno_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");

						$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
						$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'size_color_break_down_list');");
						$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+i+"').value,"+i+")");
						$('#txtorderrate_'+i).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+i+")");
						$('#txtorderexcesscut_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+i+"').value,this.value,"+i+")");
					})
				}
			}
			calculate_total_amnt( rowNo-1 )
		}
	}
	function re_order()
	{
		var row_num=$('#size_color_break_down_list tr').length-1;
		for(i=0;i<=row_num;i++)
		{
		$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
			'value': function(_, value) { return value }             
			}); 
			
			$('#cbogmtsitem_'+i).removeAttr("onChange").attr("onChange","calculate_total_amnt("+i+");check_duplicate("+i+",this.id)");
			$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
			$('#txtarticleno_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");

			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'size_color_break_down_list');");
			$('#txtorderquantity_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(this.value,document.getElementById('txtorderexcesscut_"+i+"').value,"+i+")");
			$('#txtorderrate_'+i).removeAttr("onBlur").attr("onBlur","calculate_total_amnt("+i+")");
			$('#txtorderexcesscut_'+i).removeAttr("onBlur").attr("onBlur","set_excess_cut(document.getElementById('txtorderquantity_"+i+"').value,this.value,"+i+")");
			set_all_onclick();
			$("#txtcolor_"+i).autocomplete({
			source: str_color
			});
			$("#txtsize_"+i).autocomplete({
			source:  str_size 
			});
			})
		}
	}
	
	function populate_size_color_breakdown_with_data(data)
	{
		freeze_window(5);
		var data=data.split("_");
		
		document.getElementById('cbo_po_country').value=data[1];
		document.getElementById('hid_old_country').value=data[1];
		document.getElementById('txt_country_ship_date').value=data[2];
		document.getElementById('txt_cutup_date').value=data[3];
		document.getElementById('cbo_cut_up').value=data[4];
		document.getElementById('txt_remark').value=data[5];
		if(data[3] !="")
		{
			 $("#txt_country_ship_date").attr("disabled",true);
		}
		else
		{
			$("#txt_country_ship_date").attr("disabled",false);
		}
		document.getElementById('txt_first_range').value="";
		document.getElementById('txt_second_range').value="";
		document.getElementById('txt_click_range').value="";
		document.getElementById('txt_copy_color').value="";
		show_list_view(data[0]+"_"+data[1],'populate_size_color_breakdown_with_data','data_form','size_color_breakdown_controller_update','');
		set_button_status(1, permission, 'fnc_size_color_breakdown',1);
		calculate_total_amnt( 1 )
		release_freezing();
	}
	
	function check_country(country_id)
	{
		var po_id=document.getElementById('order_id').value;
		var country=return_global_ajax_value(po_id+"_"+country_id, 'check_country', '', 'size_color_breakdown_controller_update');
		if(country>0)
		{
			alert("This Country Data Already Inserted");
			document.getElementById('cbo_po_country').value=0;
		}
		
	}
	
	
	function set_excess_cut( val, excs, id )
	{
		document.getElementById('txtorderplancut_'+id).value=(val*1)+((excs*val)/100);
		calculate_total_amnt(id);
	}
	
	function calculate_total_amnt( id )
	{
		//alert(id);
		document.getElementById('txtorderamount_'+id).value=document.getElementById('txtorderrate_'+id).value*document.getElementById('txtorderquantity_'+id).value
		var po_qnty=(document.getElementById('total').value)*1;
		var item_id=(document.getElementById('cbogmtsitem_'+id).value);
		var cbo_order_uom=document.getElementById('cbo_order_uom').value;
		if(cbo_order_uom==58)
		{
			var set_breck_down=document.getElementById('set_breck_down').value;
			set_breck_down=set_breck_down.split("__");
			var item_id_value_array=new Array();
			for (var si=0;si<set_breck_down.length; si++)
			{
			var item_id_value=set_breck_down[si].split("_");
			item_id_value_array[item_id_value[0]]=item_id_value[1]
			}
			po_qnty=po_qnty*item_id_value_array[item_id];
			//document.getElementById('set_item_qnty_level').innerHTML="Set Item Qnty";
			//document.getElementById('set_item_qnty').innerHTML=item_id_value_array[item_id];
			//document.getElementById('qnty_eq_in_pcs_level').innerHTML="Qnty in Pcs";
			//document.getElementById('qnty_eq_in_pcs').innerHTML=po_qnty;
		}
		var row_num=$('#size_color_break_down_list tr').length-1;
		var tot=0;
		var item_tot=0;
		var avg_rate = 0; 
		var tot_amount = 0;
		var avg_excess_cut = 0;
		var tot_plan_cut = 0;
		for (var k=1;k<=row_num; k++)
		{
			if(item_id==document.getElementById('cbogmtsitem_'+k).value)
			{
				item_tot=(item_tot*1)+(document.getElementById('txtorderquantity_'+k).value*1);
			}
			tot=(tot*1)+(document.getElementById('txtorderquantity_'+k).value*1);
			avg_rate=((avg_rate*1)+(document.getElementById('txtorderrate_'+k).value*1));
			tot_amount=(tot_amount*1)+(document.getElementById('txtorderamount_'+k).value*1);
			avg_excess_cut=((avg_excess_cut*1)+(document.getElementById('txtorderexcesscut_'+k).value*1))
			tot_plan_cut=(tot_plan_cut*1)+(document.getElementById('txtorderplancut_'+k).value*1); 
		}
		avg_excess_cut=((tot_plan_cut-tot)/tot)*100;
		avg_rate=tot_amount/tot*1;
		$('#txt_total_order_qnty').val(tot);
		$('#txt_total_order_item_qnty').val(item_tot);
		$('#txt_total_order_item_yetto_qnty').val(po_qnty-item_tot);
		$('#txt_avg_rate').val(number_format_common(avg_rate, 3, 0,2));
		$('#txt_total_amt').val(tot_amount);
		$('#txt_avg_excess_cut').val(number_format_common(avg_excess_cut,6, 0,2));
		$('#txt_total_plan_cut').val(tot_plan_cut);
		
		if (item_tot>po_qnty)
		{
			alert('Breakdown Quantity Over The Po Qnty Not Allowed.');
			document.getElementById('txtorderquantity_'+id).value="";
			document.getElementById('txtorderplancut_'+id).value="";
			document.getElementById('txtorderamount_'+id).value="";
			$('#txtorderquantity_'+id).focus();
			return;
		}
	}
	
	function check_duplicate(id,td)
	{
		var item_id=(document.getElementById('cbogmtsitem_'+id).value);
		var txtcolor=(document.getElementById('txtcolor_'+id).value).toUpperCase();
		var txtsize=(document.getElementById('txtsize_'+id).value).toUpperCase();
		var txtarticleno= (document.getElementById('txtarticleno_'+id).value).toUpperCase();

		var row_num=$('#size_color_break_down_list tr').length-1;
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				if(item_id==document.getElementById('cbogmtsitem_'+k).value && txtcolor==document.getElementById('txtcolor_'+k).value && txtsize==document.getElementById('txtsize_'+k).value && trim(txtarticleno)==trim(document.getElementById('txtarticleno_'+k).value.toUpperCase()))
				{
				alert("Same Gmts Item, Same Article Number, Same Color and Same Size Duplication Not Allowed.");
				document.getElementById(td).value="";
				document.getElementById(td).focus();
				}
			}
		}
	}
	
	
	function tr_index(tr)
	{
		var index_main=$(tr).index();
		var index=$(tr).index()+1;
		document.getElementById('txt_click_range').value=document.getElementById('txt_click_range').value*1+1;
		if(document.getElementById('txt_click_range').value==1)
		{
		document.getElementById('txt_first_range').value=index;
		$("#size_color_break_down_list tr:eq("+index+")").css('background-color', 'Yellow');
		//$(tr).css('background-color', 'Red');
		}
		else
		{
			var color_remove_in=document.getElementById('txt_second_range').value;
			//alert(color_remove_in)
			$("#size_color_break_down_list tr:eq("+color_remove_in+")").css('background-color', '');
			if(document.getElementById('txt_first_range').value<index)
			{
						document.getElementById('txt_second_range').value=index;
						$("#size_color_break_down_list tr:eq("+index+")").css('background-color', 'Yellow');
						//$(tr).css('background-color', 'Red');

			}
			else
			{
				document.getElementById('txt_second_range').value=document.getElementById('txt_first_range').value;
				document.getElementById('txt_first_range').value=index
				$("#size_color_break_down_list tr:eq("+index+")").css('background-color', 'Yellow');
			}
		}
		
	}
	

	
	function check_copy(val)
	{
		copied_table="";
		if (val==0)
		{
			$('#chk_copy').val(1);	// attr('checked',true);
			copied_table=$("#size_color_break_down_list tbody").html();
		}
		else
		$('#chk_copy').val(0); 	//attr('checked',false);	
		alert(copied_table);
	}
	
	function add_copied_po_breakdown()
	{
		$("#size_color_break_down_list tbody").html('');
		$("#size_color_break_down_list tbody").html(copied_table);
	}
	
function color_select_popup(buyer_name,texbox_id)
{
	//var page_link='requires/sample_booking_non_order_controller.php?action=color_popup'
	//alert(texbox_id)
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'size_color_breakdown_controller_update.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=450px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var color_name=this.contentDoc.getElementById("color_name");
		if (color_name.value!="")
		{
			$('#'+texbox_id).val(color_name.value);
		}
	}
}
function copy_avg_price()
{
	//var permission_array=permission.split("_");
	//var updateid=$('#hiddenid_'+rowNo).val();
	var avg_price=document.getElementById('txt_avg_price').value;
	
	if(avg_price =="")
	{
		alert("Insert rate");
		return;
	}
	if(avg_price ==0)
	{
		alert("Insert rate");
		return;
	}
	
	var order_id=document.getElementById('order_id').value;
	/*var po_id=order_id;
	var txt_job_no=document.getElementById('txt_job_no').value;
	var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'woven_order_entry_controller')
	var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
	if(trim(booking_no_with_approvet_status_arr[0]) !="")
	{
		var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
		if(booking_no_with_approvet_status_arr[1] !="")
		{
			al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
		}
		al_magg+=" found,\nPlease Un-approved the booking first";
		alert(al_magg)
		return;
	}
	
	if(trim(booking_no_with_approvet_status_arr[1]) !="")
	{
		var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
		var r=confirm(al_magg);
		if(r==false)
		{
			return;
		}
		else
		{
			//continue;
		}
		
	}*/
	
	var tot_row=return_global_ajax_value(order_id+'_'+avg_price+'_'+txt_job_no, 'update_avg_rate', '', 'size_color_breakdown_controller_update');
	show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','size_color_breakdown_controller_update','');
    show_list_view(document.getElementById('order_id').value+"_"+document.getElementById('cbo_po_country').value,'populate_size_color_breakdown_with_data','data_form','size_color_breakdown_controller_update','');
	document.getElementById('cbo_po_country').value=0;
	document.getElementById('hid_old_country').value=0;
	alert("Total "+trim(tot_row)+" Rows Updated");
}


function copy_excess_cut()
{
	//var permission_array=permission.split("_");
	//var updateid=$('#hiddenid_'+rowNo).val();
	var txt_excess_cut=document.getElementById('txt_excess_cut').value;
	
	if(txt_excess_cut =="")
	{
		alert("Insert rate");
		return;
	}
	if(txt_excess_cut ==0)
	{
		alert("Insert rate");
		return;
	}
	
	var order_id=document.getElementById('order_id').value;
	var po_id=order_id;
	var txt_job_no=document.getElementById('txt_job_no').value;
	var booking_no_with_approvet_status = return_ajax_request_value(txt_job_no+"_"+po_id, 'booking_no_with_approved_status', 'woven_order_entry_controller_update')
	var booking_no_with_approvet_status_arr=booking_no_with_approvet_status.split("_");
	if(trim(booking_no_with_approvet_status_arr[0]) !="")
	{
		var al_magg="Main Fabric Approved Booking No "+booking_no_with_approvet_status_arr[0];
		if(booking_no_with_approvet_status_arr[1] !="")
		{
			al_magg+=" and Un-Approved Booking No "+booking_no_with_approvet_status_arr[1];
		}
		al_magg+=" found,\nPlease Un-approved the booking first";
		alert(al_magg)
		return;
	}
	
	if(trim(booking_no_with_approvet_status_arr[1]) !="")
	{
		var al_magg=" Main Fabric Un-Approved Booking No "+booking_no_with_approvet_status_arr[1]+" Found\n If you update this job\n You have to update  Pre-cost and booking against this Job ";
		var r=confirm(al_magg);
		if(r==false)
		{
			return;
		}
		else
		{
			//continue;
		}
		
	}
	
	var tot_row=return_global_ajax_value(order_id+'_'+txt_excess_cut+'_'+txt_job_no, 'update_txt_excess_cut', '', 'size_color_breakdown_controller_update');
	show_list_view(document.getElementById('order_id').value,'populate_size_color_breakdown','size_color_breakdown','size_color_breakdown_controller_update','');
    show_list_view(document.getElementById('order_id').value+"_"+document.getElementById('cbo_po_country').value,'populate_size_color_breakdown_with_data','data_form','size_color_breakdown_controller_update','');
	document.getElementById('cbo_po_country').value=0;
	document.getElementById('hid_old_country').value=0;
	alert("Total "+trim(tot_row)+" Rows Updated");
}

function vacant_form()
{
	document.getElementById('cbogmtsitem').value="";
	document.getElementById('txt_first_range').value="";
	document.getElementById('txt_second_range').value="";
	document.getElementById('txt_copy_color').value="";
}
function set_ship_date() {
	var txt_cutup_date=document.getElementById('txt_cutup_date').value;
	var cbo_cut_up=document.getElementById('cbo_cut_up').value;
	if(txt_cutup_date=="")
	{
		alert("Insert Cutup Date");
		 $("#txt_country_ship_date").attr("disabled",false);
		return;
	}
	if(cbo_cut_up==0)
	{
		alert("Select Cutup");
		$("#txt_country_ship_date").attr("disabled",false);
		return;
	}
	var set_ship_date=return_global_ajax_value(txt_cutup_date+'_'+cbo_cut_up, 'set_ship_date', '', 'size_color_breakdown_controller_update');
	document.getElementById('txt_country_ship_date').value=set_ship_date;
	 $("#txt_country_ship_date").attr("disabled",true);
    }
    </script>

</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
  
        <!-- Important Field outside Form --> 
        <input type="hidden" id="garments_nature" value="2">
        <!-- End Important Field outside Form -->
        <? echo load_freeze_divs ("../../../",$permission);  ?>
       
        <fieldset style="width:950px; visibility:collapse">
         
            <legend>Color & Size Breakdown Entry</legend>
            <form name="sizecolormaster_1" id="sizecolormaster_1" autocomplete="off">
                <table  width="950" cellspacing="2" cellpadding="0" border="0">
                    <tr style="visibility:hidden">
                        <td  width="130" height="" align="right"></td>              
                        <td  width="170" >
                        </td>
                        <td  width="130" align="right">Order No </td>
                        <td width="170">
                        <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/size_color_breakdown_controller_update.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="Order No" name="txt_order_no" id="txt_order_no" readonly />
                        <input type="hidden" id="order_id" name="order_id" readonly />
                        </td>
                        <td width="130" align="right"></td>
                        <td>
                        </td>
                    </tr>
                    <tr style="visibility:hidden">
                        <td  width="130" height="" align="right">Job No</td>              
                        <td  width="170" >
                        <input style="width:160px;" type="text" title="Double Click to Search" onDblClick="openmypage('requires/size_color_breakdown_controller_update.php?action=order_popup','Job/Order Selection Form')" class="text_boxes"  name="txt_job_no" id="txt_job_no" disabled />
                        </td>
                        <td  width="130" align="right">Company Name </td>
                        <td width="170">
                        <?
                        echo create_drop_down( "cbo_company_name", 172, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "",1 );
                        ?> 
                        </td>
                        <td width="130" align="right">Location Name</td>
                        <td id="location">
                        <? 
                        echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "",1 );		
                        ?>	
                        </td>
                    </tr>
                    <tr style="visibility:hidden">
                        <td align="right">Buyer Name</td>
                        <td id="buyer_td">
                        <? 
                        echo create_drop_down( "cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,1);   
                        ?>	  
                        </td>
                        <td align="right">Style Ref.</td>
                        <td>
                        <input class="text_boxes" type="text" style="width:160px" disabled placeholder="Double Click for Quotation" name="txt_style_ref" id="txt_style_ref"/>	
                        </td>
                        <td align="right">
                        Style Description
                        </td>
                        <td>	
                        <input class="text_boxes" type="text" style="width:160px;" disabled name="txt_style_description" id="txt_style_description"/>
                        </td>
                    </tr>
                    <tr style="visibility:hidden">
                        <td height="" align="right">Pord. Dept.</td>   
                        <td >
                        <? 
                        echo create_drop_down( "cbo_product_department", 172, $product_dept, "",1, "-- Select prod. Dept--", $selected, "" ,1);
                        ?>
                        </td>
                        <td align="right">Currency</td>
                        <td>
                        <? 
                        echo create_drop_down( "cbo_currercy", 172, $currency, "", 1, "-- Select Currency--", 2, "",1 );
                        ?>	  
                        </td>
                        <td align="right">Agent </td>
                        <td id="agent_td">
                        <?	 	echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "",1 );  
                        
                        ?>
                        </td>
                    </tr>
                    <tr style="visibility:hidden">
                        <td  align="right">Region</td>
                        <td>
                        <? 
                        echo create_drop_down( "cbo_region", 172, $region, "",1, "-- Select Region --", $selected, "",1 );
                        ?>	  
                        </td>
                        <td align="right">Team Leader</td>   
                        <td>
                        <?  
                        echo create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "",1 );
                        ?>		
                        </td>
                        <td align="right">Dealing Merchant</td>   
                        <td> 
                        <? 
                        echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "",1 );
                        ?>	
                        </td>
                    </tr>
                    <tr style="visibility:hidden">
                        <td  align="right">Shipment Date</td>
                        <td>
                        <input class="datepicker" type="text" style="width:160px;"  name="txt_ship_date" id="txt_ship_date" disabled/>
                        </td>
                        <td  align="right">Po Qnty</td>
                        <td>
                        <input class="text_boxes" type="text" style="width:100px;"  name="txt_po_qnty" id="txt_po_qnty" disabled/>
                        <? 
                        echo create_drop_down( "cbo_order_uom",55, $unit_of_measurement, "",0, "", 1, "","1","1,58" );
                        ?>
                        </td>
                        <td  align="right">Plan Cut Qnty</td>
                        <td>
                        <input class="text_boxes" type="text" style="width:100px;"  name="txt_plan_cut_qnty" id="txt_plan_cut_qnty" disabled/>
                        <? 
                        echo create_drop_down( "cbo_order_uom_2",55, $unit_of_measurement, "",0, "", 1, "","1","1,58" );
                        ?>
                        </td>
                    </tr>
                   
                    <tr style="visibility:hidden">
                        <td align="center" height="20" colspan="6" class="image_uploaders">
                        <input type="hidden" id="update_id">
                        <input type="hidden" id="set_breck_down" />     
                        <input type="hidden" id="item_id" />
                        <input type="hidden" id="tot_set_qnty" />  
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
       
       <?
            $buyer_name=$cbo_buyer_name;
            $color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=23  and status_active=1 and is_deleted=0");
            if($color_from_library==1)
            {
            $readonly="readonly='readonly'";
            $plachoder="placeholder='Click'";
            $onClick="onClick='color_select_popup($buyer_name,this.id)'";
            }
            else
            {
            $readonly="";
            $plachoder="";
            $onClick="";
            }
			$sql_slap=sql_select("select excut_source,editable from variable_order_tracking where company_name=$cbo_company_name and variable_list=45 and is_deleted=0 and status_active=1");
			if(count($sql_slap>0))
			{
				foreach($sql_slap as $row )
				{
					$excess_variable=$row[csf('excut_source')];
					
					if($excess_variable==2) //slap
					{
						$editable_id=$row[csf('editable')];
					}
				}
				if($editable_id=="" || $editable_id==0) $editable_id=0; else $editable_id=$editable_id;
				if($editable_id==1) //Slap//Yes
				{
					$excess_fld_disable=""; 
				}
				else if($editable_id==2 || $editable_id==0) //Slap//No
				{
					$excess_fld_disable="disabled";
				}
				else $excess_fld_disable="disabled";
			}
	
			
       ?>
       <fieldset style="width:1180px">
            <legend>Copy Panel</legend>
            <table>
            <tr>
            <td colspan=>
            New Item: 
            </td>
            <td>

			<?
            echo create_drop_down( "cbogmtsitem", 170, $garments_item,"", 0, "","", "","",$item_id);
            ?> 
            <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_first_range" id="txt_first_range"/>
            <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_second_range" id="txt_second_range"/>
            <input class="text_boxes" type="hidden" style="width:100px;"  name="txt_click_range" id="txt_click_range" value="0"/> 
            </td>
            <td>
             New Color: 
            </td>
            <td> 
             <input class="text_boxes" type="text" style="width:160px;"  name="txt_copy_color" id="txt_copy_color" <? echo $onClick." ".$readonly." ".$plachoder; ?>/>
            </td>
            <td>
            Copy Rate
            </td>
            <td>
            <input name="txt_avg_price" id="txt_avg_price" class="text_boxes_numeric" type="text" value=""  style="width:150px "  />
            </td>
            <td align="right">
            Copy Excess Cut %
            </td>
            <td>
            <input name="txt_excess_cut" id="txt_excess_cut"  class="text_boxes_numeric" type="text" style="width:160px" <? echo $excess_fld_disable;?>/>
            </td>
        </tr>
        
        <tr>
            <td colspan="4" align="center">
             <input type="reset" value="Reset Range" class="formbutton"  onClick="vacant_form()"/>
            <input type="button" id="copyset1" style="width:50px" class="formbutton" value="Copy" onClick="copyset_tr()" /> 
            </td>
            <td colspan="2" align="center">
            <input type="button" id="copyset2" style="width:100px" class="formbutton" value="Copy Rate" onClick="copy_avg_price()" /> 
            </td>
            
            <td colspan="2" align="center">
            <input type="button" id="copyset3" style="width:100px" class="formbutton" value="Copy Excess Cut" onClick="copy_excess_cut()" /> 
            </td>
        </tr>
        </table>
        </fieldset>
        <br/>
        <fieldset style="width:1180px">
        <legend>Input Panel</legend>
        <table>
            <tr>
                <td  align="right">Country</td>
                <td>
                <?php
                echo create_drop_down( "cbo_po_country", 170,"select id,country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "Select", "","check_country(this.value)" ); 
                ?>
                <input type="hidden" id="hid_old_country" />
                </td>
                <td  align="right">Cut-off Date</td>
                <td>
                <input class="datepicker" type="text" style="width:160px;"  name="txt_cutup_date" id="txt_cutup_date" onChange="set_ship_date()" value="<? echo $txt_org_shipment_date; ?>"/>
                </td>
                <td  align="right">Cutoff</td>
                <td>
                <? 
                echo create_drop_down( "cbo_cut_up",160, $cut_up_array, "",1, "Select", "", "set_ship_date()","","" );
                ?>
                </td>
                <td  align="right">Country Shipment Date</td>
                <td>
                <input class="datepicker" type="text" style="width:70px;"  name="txt_country_ship_date" id="txt_country_ship_date" />
                </td>
                <td  align="right">Remark</td>
                <td>
                 <input type="text" id="txt_remark" name="txt_remark"  class="text_boxes"  style="width:150px;"/>
                </td>
            </tr>
            </table>
            </fieldset>
            
        <br/>
        <div id="size_color_breakdown">
        </div>
    </div>
</body>  
<script>
get_php_form_data(<? echo $data;?>, "populate_data_from_search_popup", "size_color_breakdown_controller_update" );
show_list_view('<? echo $data;?>','populate_size_color_breakdown','size_color_breakdown','size_color_breakdown_controller_update','');
</script>         
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="set_ship_date")
{
$data=explode("_",$data);
$Date = change_date_format($data[0],"yyyy-mm-dd","-");
	if($data[1]==1)
	{
		echo date('d-m-Y', strtotime($Date. ' - 1 days'));
	}
	if($data[1]==2)
	{
		echo date('d-m-Y', strtotime($Date. ' + 1 days'));
	}
	if($data[1]==3)
	{
		echo date('d-m-Y', strtotime($Date. ' + 3 days'));
	}
}

if($action=="update_avg_rate")
{
	$i=0;
	$data=explode("_",$data);
	$data_array=sql_select("select id,order_quantity from wo_po_color_size_breakdown where po_break_down_id=$data[0] and is_deleted=0 and status_active=1");
	foreach($data_array as $row)
	{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$order_total=$data[1]*$row[csf('order_quantity')];
	$rID=execute_query( "update  wo_po_color_size_breakdown set order_rate=$data[1],order_total=$order_total  where  po_break_down_id =$data[0] and id=".$row[csf('id')]."",1);
	if($db_type==0)
	{
		if($rID ){
		mysql_query("COMMIT");  
		//echo "2";
		}
		else{
		mysql_query("ROLLBACK"); 
		//echo "10";
		}
	}
	
	if($db_type==2 || $db_type==1 )
	{
		if($rID ){
		oci_commit($con);
		//echo "2";
		}
		else{
		oci_rollback($con);
		//echo "10";
		}
	}
	disconnect($con);
	$i++;
	}
	echo $i;
	die;
}
if($action=="update_txt_excess_cut")
{
	$i=0;
	$data=explode("_",$data);
	$data_array=sql_select("select id,order_quantity from wo_po_color_size_breakdown where po_break_down_id=$data[0] and is_deleted=0 and status_active=1");
	foreach($data_array as $row)
	{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$excess_cut_perc=($data[1]*$row[csf('order_quantity')]/100)+$row[csf('order_quantity')];
	$rID=execute_query( "update  wo_po_color_size_breakdown set excess_cut_perc=$data[1],plan_cut_qnty=$excess_cut_perc  where  po_break_down_id =$data[0] and id=".$row[csf('id')]."",1);
	$rID1=execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no ='".$data[2]."' and booking_type=1 and is_short=2 ",1);

	if($db_type==0)
	{
		if($rID && $rID1){
		mysql_query("COMMIT");  
		//echo "2";
		}
		else{
		mysql_query("ROLLBACK"); 
		//echo "10";
		}
	}
	
	if($db_type==2 || $db_type==1 )
	{
		if($rID && $rID1){
		oci_commit($con);
		//echo "2";
		}
		else{
		oci_rollback($con);
		//echo "10";
		}
	}
	disconnect($con);
	$i++;
	}
	echo $i;
	die;
}
if($action=="color_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script> 
	function js_set_value(data)
	{
		document.getElementById('color_name').value=data;
		parent.emailwindow.hide();
	}
	</script> 
	</head>
	<body>
	<body>
	<div align="center">
	<form>
	<input type="hidden" id="color_name" name="color_name" />
	<?
	if($buyer_name=="" || $buyer_name=="")
	{
		$sql="select color_name,id FROM lib_color  WHERE status_active=1 and is_deleted=0"; 
	}
	else
	{
		$sql="select a.color_name,a.id FROM lib_color a, lib_color_tag_buyer b  WHERE a.id=b.color_id and b.buyer_id=$buyer_name and  status_active=1 and is_deleted=0"; 
	}
	echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/sample_booking_non_order_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;
	?>
    </form>
    </div>
    </body>
    </html>
    <?
	exit();
}



if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if (file_exists('dateretriction.php'))
	{
		require('dateretriction.php');
	}
	if ($operation==0)  //Insert Here
	{
		 $con = connect();
		 if($db_type==0)
		 {
			mysql_query("BEGIN");
		 }
		 if($cbo_order_uom==58)
		 {
			 $txt_avg_rate=$txt_avg_rate*$tot_set_qnty;
			 $txt_total_plan_cut=$txt_total_plan_cut/$tot_set_qnty;
		 }
		 else
		 {
			$txt_avg_rate=$txt_avg_rate; 
			$txt_total_plan_cut=$txt_total_plan_cut;
		 }
		 
		 $new_array_color=return_library_array( "select a.color_number_id,b.id,b.color_name from wo_po_color_size_breakdown a, lib_color b where b.id=a.color_number_id and a.po_break_down_id=$order_id", "id", "color_name"  );
		 $new_array_size=return_library_array( "select a.size_number_id,b.id,b.size_name from wo_po_color_size_breakdown a, lib_size b where b.id=a.size_number_id and a.po_break_down_id=$order_id", "id", "size_name"  );
		 $color_mst=return_library_array( "select color_mst_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and color_mst_id !=0", "color_number_id", "color_mst_id"  );
		 $size_mst=return_library_array( "select size_mst_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id"  );
		 $item_mst=return_library_array( "select item_mst_id,item_number_id from wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id"  );
		 $id=return_next_id( "id", "wo_po_color_size_breakdown",1);
		 
		 $field_array="id,po_break_down_id,job_no_mst,color_mst_id, size_mst_id, item_mst_id, article_number, item_number_id,country_id,cutup_date,cutup,country_remarks,country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total,excess_cut_perc,plan_cut_qnty,is_deleted,status_active,inserted_by,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txtarticleno="txtarticleno_".$i;
			 $cbogmtsitem="cbogmtsitem_".$i;
			 $txtarticleno="txtarticleno_".$i;
			 $txtcolor="txtcolor_".$i;
			 $txtsize="txtsize_".$i;
			 $txtorderquantity="txtorderquantity_".$i;
			 $txtorderrate="txtorderrate_".$i;
			 $txtorderamount="txtorderamount_".$i;
			 $txtorderexcesscut="txtorderexcesscut_".$i;
			 $txtorderplancut="txtorderplancut_".$i;
			 $cbostatus="cbostatus_".$i;
			 
			 /*if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
			 {
				  $color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name");  
				  $new_array_color[$color_id]=str_replace("'","",$$txtcolor);
			 }
			 else 
			 {
				 $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color);
			 }
			 
			 if (!in_array(str_replace("'","",$$txtsize),$new_array_size))
			 {
				  $size_id = return_id( str_replace("'","",$$txtsize), $size_library, "lib_size", "id,size_name");   
				  $new_array_size[$size_id]=str_replace("'","",$$txtsize);
			 }
			 else
			 {
				$size_id =  array_search(str_replace("'","",$$txtsize), $new_array_size); 
			 }*/

			if(str_replace("'","",$$txtcolor) !="")
			{
			    if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
			    {
			        $color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name","401");
			        $new_array_color[$color_id]=str_replace("'","",$$txtcolor);
			    }
			    else $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color);
			}
			else
			{
			    $color_id=0;
			}

			if(str_replace("'","",$$txtsize)!="")
			{
			    if (!in_array(str_replace("'","",$$txtsize),$new_array_size))
			    {
			        $size_id = return_id( str_replace("'","",$$txtsize), $size_library, "lib_size", "id,size_name","401");
			        $new_array_size[$size_id]=str_replace("'","",$$txtsize);
			    }
			    else $size_id =  array_search(str_replace("'","",$$txtsize), $new_array_size);
			}
			else
			{
			    $size_id=0;
			}
			 
			 
			 if (array_key_exists(str_replace("'","",$$cbogmtsitem),$item_mst))
			  {
				 $item_mst_id=$item_mst[str_replace("'","",$$cbogmtsitem)];
			  }
			else
			  {
			     $item_mst[str_replace("'","",$$cbogmtsitem)]=$id;
				 $item_mst_id=$id;
			  }
			  
			  
			  
			 if(array_key_exists($color_id,$color_mst))
			 {
				  $color_mst_id=$color_mst[$color_id];	
			 }
			 
			 else
			 {
			  
			   $color_mst[$color_id]=$id;
			   $color_mst_id=$id;
			 }
			 if(array_key_exists($size_id,$size_mst))
			 {
				 $size_mst_id=$size_mst[$size_id];	 
			 }
		     else
			 {
				  $size_mst[$size_id]=$id;
				  $size_mst_id=$id;
			   
			 }
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$order_id.",'".$txt_job_no."','".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$$txtarticleno.",".$$cbogmtsitem.",".$cbo_po_country.",".$txt_cutup_date.",".$cbo_cut_up.",".$txt_remark.",".$txt_country_ship_date.",".$size_id.",".$color_id.",".$$txtorderquantity.",".$$txtorderrate.",".$$txtorderamount.",".$$txtorderexcesscut.",".$$txtorderplancut.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		 }
		 //$rID=sql_insert("wo_po_color_size_breakdown",$field_array,$data_array,0);
		 
		 $field_array1="unit_price*po_total_price*excess_cut*plan_cut*updated_by*update_date";
		 $data_array1="'".$txt_avg_rate."'*'".$txt_total_amt."'*'".$txt_avg_excess_cut."'*'".$txt_total_plan_cut."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 $rID=sql_insert("wo_po_color_size_breakdown",$field_array,$data_array,0);
		 $rID1=sql_update("wo_po_break_down",$field_array1,$data_array1,"id","".$order_id."",1);
		 $return_data= update_job_mast("'".$txt_job_no."'"); //define in common_functions.php
		 update_cost_sheet("'".$txt_job_no."'");

//============================================================================================
		/* $sam=1;
		 $id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		 $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
		 $field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted"; 		
		 $data_array_sample=sql_select("select a.id as po_id,a.po_number, b.id as color_size_table_id, b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst='$txt_job_no' and b.color_mst_id !=0 and a.id=b.po_break_down_id and  b.po_break_down_id='$order_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_break_down_id,b.color_mst_id order by a.id");
		 foreach ( $data_array_sample as $row_sam1 )
                 {
					 if ($sam!=1) $data_array_sm .=",";
			          $data_array_sm .="(".$id_sm.",'".$txt_job_no."',".$row_sam1[po_id].",".$row_sam1[color_size_table_id].",'".$cbosampletype."',1,0)";
			          $id_sm=$id_sm+1;
					  $sam=$sam+1;
				 }
				 $rID3=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);*/
		if($db_type==0) $sequNullCheck="IFNULL(sequ,0)";
		else if($db_type==2) $sequNullCheck="nvl(sequ,0)";
		$sam=1;
		 $id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		$sample_tag=sql_select("select tag_sample,sequ from lib_buyer_tag_sample where $sequNullCheck!=0 and buyer_id=$txt_buyer_id order by sequ");
		 $field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted";
		 $data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst='$txt_job_no' and b.color_mst_id !=0 and a.id=b.po_break_down_id and  b.po_break_down_id='$order_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
		 foreach($sample_tag as $sample_tag_row)
		 {
			 foreach ( $data_array_sample as $row_sam1 )
			 {
				 $dup_data=sql_select("select id from wo_po_sample_approval_info where job_no_mst='$txt_job_no' and po_break_down_id=".$row_sam1[csf('po_id')]." and color_number_id=".$row_sam1[csf('color_size_table_id')]." and sample_type_id='".$sample_tag_row[csf('tag_sample')]."' and status_active=1 and is_deleted=0");
				 list($idsm)=$dup_data;
				 if( $idsm[csf('id')] =='')
				 {
				  if ($sam!=1) $data_array_sm .=",";
				  $data_array_sm .="(".$id_sm.",'".$txt_job_no."',".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",'".$sample_tag_row[csf('tag_sample')]."',1,0)";
				  $id_sm=$id_sm+1;
				  $sam=$sam+1;
				 }
			 }
		 }
		 if($data_array_sm !='')
		 {
		 	$rID3=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);
		 }
		 
//============================================================================================
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con);  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($cbo_order_uom==58)
		 {
			 $txt_avg_rate=$txt_avg_rate*$tot_set_qnty;
			 $txt_total_plan_cut=$txt_total_plan_cut/$tot_set_qnty;
		 }
		 else
		 {
			$txt_avg_rate=$txt_avg_rate; 
			$txt_total_plan_cut=$txt_total_plan_cut;
		 }
		 
		 $new_array_size=return_library_array( "select a.size_number_id,b.id,b.size_name from wo_po_color_size_breakdown a, lib_size b where b.id=a.size_number_id and a.po_break_down_id=$order_id", "id", "size_name"  );
		 $new_array_color=return_library_array( "select a.color_number_id,b.id,b.color_name from wo_po_color_size_breakdown a, lib_color b where b.id=a.color_number_id and a.po_break_down_id=$order_id", "id", "color_name"  );
		 $color_mst=return_library_array( "select color_mst_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and color_mst_id !=0", "color_number_id", "color_mst_id"  );
		 $size_mst=return_library_array( "select size_mst_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id"  );
		 $item_mst=return_library_array( "select item_mst_id,item_number_id from wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id"  );
		 
		 /*$color_mst=return_library_array( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and color_mst_id !=0", "color_number_id", "id"  );
		 $size_mst=return_library_array( "select id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "id"  );
		 $item_mst=return_library_array( "select id,item_number_id from wo_po_color_size_breakdown where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "id"  );*/
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $hiddenid="hiddenid_".$i;
			 $txtarticleno="txtarticleno_".$i;
			 $cbogmtsitem="cbogmtsitem_".$i;
			 $txtarticleno="txtarticleno_".$i;
			 $txtcolor="txtcolor_".$i;
			 $txtsize="txtsize_".$i;
			 $txtorderquantity="txtorderquantity_".$i;
			 $txtorderrate="txtorderrate_".$i;
			 $txtorderamount="txtorderamount_".$i;
			 $txtorderexcesscut="txtorderexcesscut_".$i;
			 $txtorderplancut="txtorderplancut_".$i;
			 $cbostatus="cbostatus_".$i;
			 
			 /*if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
			 {
				  $color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name");  
				  $new_array_color[$color_id]=str_replace("'","",$$txtcolor);
			 }
			 else 
			 {
				 $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color); 
			 }
			 if (!in_array(str_replace("'","",$$txtsize),$new_array_size))
			 {
				  $size_id = return_id( str_replace("'","",$$txtsize), $size_library, "lib_size", "id,size_name");   
				  $new_array_size[$size_id]=str_replace("'","",$$txtsize);
			 }
			 else 
			 {
				$size_id =  array_search(str_replace("'","",$$txtsize), $new_array_size); 
			 }*/
			if(str_replace("'","",$$txtcolor) !="")
			{
			    if (!in_array(str_replace("'","",$$txtcolor),$new_array_color))
			    {
			        $color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name","401");
			        $new_array_color[$color_id]=str_replace("'","",$$txtcolor);
			    }
			    else $color_id =  array_search(str_replace("'","",$$txtcolor), $new_array_color);
			}
			else
			{
			    $color_id=0;
			}

			if(str_replace("'","",$$txtsize)!="")
			{
			    if (!in_array(str_replace("'","",$$txtsize),$new_array_size))
			    {
			        $size_id = return_id( str_replace("'","",$$txtsize), $size_library, "lib_size", "id,size_name","401");
			        $new_array_size[$size_id]=str_replace("'","",$$txtsize);
			    }
			    else $size_id =  array_search(str_replace("'","",$$txtsize), $new_array_size);
			}
			else
			{
			    $size_id=0;
			}
			 
			 
			 
			if(str_replace("'",'',$$hiddenid)!="")
			{
				
			 if (array_key_exists(str_replace("'","",$$cbogmtsitem),$item_mst))
			  {
				 $item_mst_id=$item_mst[str_replace("'","",$$cbogmtsitem)];
			  }
			else
			  {
			     $item_mst[str_replace("'","",$$cbogmtsitem)]=str_replace("'",'',$$hiddenid);
				 $item_mst_id=str_replace("'",'',$$hiddenid);
			  }
			  
			  
			  
			 if(array_key_exists($color_id,$color_mst))
			 {
				  $color_mst_id=$color_mst[$color_id];	
			 }
			 
			 else
			 {
			  
			   $color_mst[$color_id]=str_replace("'",'',$$hiddenid);
			   $color_mst_id=str_replace("'",'',$$hiddenid);
			 }
			 if(array_key_exists($size_id,$size_mst))
			 {
				 $size_mst_id=$size_mst[$size_id];	 
			 }
		     else
			 {
				  $size_mst[$size_id]=str_replace("'",'',$$hiddenid);
				  $size_mst_id=str_replace("'",'',$$hiddenid);
			   
			 }
			 $PrevData=sql_select("select color_number_id,country_ship_date from wo_po_color_size_breakdown where id=".$$hiddenid);
			 $PrevColorNumberId=$PrevData[0][csf('color_number_id')];
			 $PrevCountryShipDate=$PrevData[0][csf('country_ship_date')];
			 
				$field_array="job_no_mst* 	color_mst_id*size_mst_id*item_mst_id*article_number*item_number_id*country_id*cutup_date*cutup*country_remarks*country_ship_date*size_number_id*color_number_id*order_quantity*order_rate*order_total*excess_cut_perc*plan_cut_qnty*color_number_id_prev*country_ship_date_prev*is_deleted*status_active*updated_by";
				$data_array="'".$txt_job_no."'*'".$color_mst_id."'*'".$size_mst_id."'*'".$item_mst_id."'*".$$txtarticleno."*".$$cbogmtsitem."*".$cbo_po_country."*".$txt_cutup_date."*".$cbo_cut_up."*".$txt_remark."*".$txt_country_ship_date."*".$size_id."*".$color_id."*".$$txtorderquantity."*".$$txtorderrate."*".$$txtorderamount."*".$$txtorderexcesscut."*".$$txtorderplancut."*'".$PrevColorNumberId."'*'".$PrevCountryShipDate."'*0*".$$cbostatus."*".$_SESSION['logic_erp']['user_id']."";
				$rID=sql_update("wo_po_color_size_breakdown",$field_array,$data_array,"id","".$$hiddenid."",1);
			}
			
			
			
			if(str_replace("'",'',$$hiddenid)=="")
			{
			 $id=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
				 
			 if (array_key_exists(str_replace("'","",$$cbogmtsitem),$item_mst))
			  {
				 $item_mst_id=$item_mst[str_replace("'","",$$cbogmtsitem)];
			  }
			else
			  {
			     $item_mst[str_replace("'","",$$cbogmtsitem)]=$id;
				 $item_mst_id=$id;
			  }
			  
			  
			 if(array_key_exists($color_id,$color_mst))
			 {
				  $color_mst_id=$color_mst[$color_id];	
			 }
			 
			 else
			 {
			  
			   $color_mst[$color_id]=$id;
			   $color_mst_id=$id;
			 }
			 if(array_key_exists($size_id,$size_mst))
			 {
				 $size_mst_id=$size_mst[$size_id];	 
			 }
		     else
			 {
				  $size_mst[$size_id]=$id;
				  $size_mst_id=$id;
			   
			 }
			 
			 
				$field_array="id,po_break_down_id,job_no_mst,color_mst_id, size_mst_id, item_mst_id, article_number, item_number_id,country_id,cutup_date,cutup,country_ship_date, size_number_id, color_number_id, order_quantity, order_rate,order_total,excess_cut_perc,plan_cut_qnty,is_deleted,status_active,inserted_by,insert_date";
				$data_array="(".$id.",".$order_id.",'".$txt_job_no."','".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$$txtarticleno.",".$$cbogmtsitem.",".$cbo_po_country.",".$txt_cutup_date.",".$cbo_cut_up.",".$txt_country_ship_date.",".$size_id.",".$color_id.",".$$txtorderquantity.",".$$txtorderrate.",".$$txtorderamount.",".$$txtorderexcesscut.",".$$txtorderplancut.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$rID=sql_insert("wo_po_color_size_breakdown",$field_array,$data_array,1);
			}
		 }
		 $field_array1="unit_price*po_total_price*excess_cut*plan_cut*updated_by*update_date";
		 $data_array1="'".$txt_avg_rate."'*'".$txt_total_amt."'*'".$txt_avg_excess_cut."'*'".$txt_total_plan_cut."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 $rID1=sql_update("wo_po_break_down",$field_array1,$data_array1,"id","".$order_id."",1);
		 $return_data= update_job_mast("'".$txt_job_no."'");//define in common_functions.php
		 update_cost_sheet("'".$txt_job_no."'");

 
//============================================================================================
		 /*$sam=1;
		 $id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		 $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
		 $field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted"; 		
		 $data_array_sample=sql_select("select a.id as po_id,a.po_number, b.id as color_size_table_id, b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst='$txt_job_no' and b.color_mst_id !=0 and a.id=b.po_break_down_id and  b.po_break_down_id='$order_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_break_down_id,b.color_mst_id order by a.id");
		 foreach ( $data_array_sample as $row_sam1 )
                 {
						$dup_data=sql_select("select id from wo_po_sample_approval_info where job_no_mst='$txt_job_no' and po_break_down_id=$row_sam1[po_id] and color_number_id=$row_sam1[color_size_table_id] and sample_type_id='$cbosampletype' and status_active=1 and is_deleted=0");
						list($id)=$dup_data;
						if( $id[id] =='')
						{
							  if ($sam!=1) $data_array_sm .=",";
							  $data_array_sm .="(".$id_sm.",'".$txt_job_no."',".$row_sam1[po_id].",".$row_sam1[color_size_table_id].",'".$cbosampletype."',1,0)";
							  $id_sm=$id_sm+1;
							  $sam=$sam+1;
						}
				 }
				 if($data_array_sm !='')
				 {
					 $rID=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);
				 }*/
				 
	//	execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no ='".$txt_job_no."' and booking_type=1 and is_short=2 ",1);
		if($db_type==0) $sequNullCheck="IFNULL(sequ,0)";
		else if($db_type==2) $sequNullCheck="nvl(sequ,0)";
		 $sam=1;
		 $id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		 //$cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
		 $sample_tag=sql_select("select tag_sample,sequ from lib_buyer_tag_sample where $sequNullCheck!=0 and buyer_id=$txt_buyer_id order by sequ");
		 $field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted";
		 $data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst='$txt_job_no' and b.color_mst_id !=0 and a.id=b.po_break_down_id and  b.po_break_down_id='$order_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
		foreach($sample_tag as $sample_tag_row)
		{
			foreach ( $data_array_sample as $row_sam1 )
			{
				$dup_data=sql_select("select id from wo_po_sample_approval_info where job_no_mst='$txt_job_no' and po_break_down_id=".$row_sam1[csf('po_id')]." and color_number_id=".$row_sam1[csf('color_size_table_id')]." and sample_type_id='".$sample_tag_row[csf('tag_sample')]."' and status_active=1 and is_deleted=0");
				list($idsm)=$dup_data;
				if( $idsm[csf('id')] =='')
				{
					if ($sam!=1) $data_array_sm .=",";
					$data_array_sm .="(".$id_sm.",'".$txt_job_no."',".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",'".$sample_tag_row[csf('tag_sample')]."',1,0)";
					$id_sm=$id_sm+1;
					$sam=$sam+1;
				}
			}
		}
		// echo "10**AAAA**insert into wo_po_sample_approval_info (".$field_array_sm.") values".$data_array_sm."select tag_sample,sequ from lib_buyer_tag_sample where sequ!=0 and buyer_id=$txt_buyer_id order by sequ";die;
		 if($data_array_sm !='')
		 {
			 $rID=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);
		 }
//===============================================================================================
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$txt_job_no."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$txt_job_no."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "0**".$txt_job_no."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$txt_job_no."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		//$field_array="status_active*is_deleted";
		//$data_array="'2'*'1'";
		//$rID=sql_delete("wo_po_color_size_breakdown",$field_array,$data_array,"id","".$hiddenid."",1);
		$rID=execute_query( "update  wo_po_color_size_breakdown set status_active=0,is_deleted=1  where  po_break_down_id =$order_id and country_id=$cbo_po_country and job_no_mst='".$txt_job_no."' ",1);
		//execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no ='".$txt_job_no."' and booking_type=1 and is_short=2 ",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".$txt_job_no."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$txt_job_no."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "2**".$txt_job_no."**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$txt_job_no."**".$rID;
			}
		}
		disconnect($con);
		//echo "2****".$rID;
	}
}
 
 
 
if ($action=="get_excess_cut_percent")
{
	 $data=explode("_",$data);
	 $qry_result=sql_select( "select slab_rang_start,slab_rang_end,excess_percent from  var_prod_excess_cutting_slab where company_name='$data[1]' and variable_list=2 and status_active=1 and is_deleted=0");
	 foreach ($qry_result as $row)
	 {
		 if ( $data[0]>=$row[csf("slab_rang_start")] && $data[0]<=$row[csf("slab_rang_end")] )
		 {
			 echo $row[csf("excess_percent")]; disconnect($con);die;
		 }
	 }
	 echo "0"; disconnect($con);die;
}




/*if ($action=="show_po_active_listview")
{
	$arr=array (0=>$order_status,5=>$unit_of_measurement);
 	$sql= "select a.is_confirmed,a.po_number,a.po_received_date,a.pub_shipment_date,a.po_quantity,b.order_uom,a.unit_price,a.po_total_price,a.excess_cut,a.plan_cut,DATEDIFF(a.shipment_date,a.po_received_date) date_diff,a.status_active,a.details_remarks ,a.id from  wo_po_break_down a, wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst='$data'"; 
	 
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,PO Qnty, Order Uom,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time ", "90,130,80,80,80,50,80,80,80,80","975","220",0, $sql , "show_list_view", "id,po_number", "'populate_size_color_breakdown','size_color_breakdown'", 1, "is_confirmed,0,0,0,0,order_uom,0,0,0,0,0", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,po_quantity,order_uom,unit_price,po_total_price,excess_cut,plan_cut,date_diff", "../woven_order/requires/size_color_breakdown_controller_update",'','0,0,3,3,1,0,2,2,2,1,1');
}

if ($action=="show_country_po_active_listview")
{
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$arr=array (0=>$order_status,5=>$country_library);
 	$sql= "select a.is_confirmed,a.po_number,a.po_received_date,a.pub_shipment_date,a.po_quantity,c.country_id,a.unit_price,a.po_total_price,a.excess_cut,sum(c.plan_cut_qnty) as plan_cut,DATEDIFF(a.shipment_date,a.po_received_date) date_diff,a.status_active,a.details_remarks ,a.id from  wo_po_break_down a, wo_po_details_master b,wo_po_color_size_breakdown c  where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no_mst and c.job_no_mst=b.job_no and a.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$data' group by a.id,c.country_id"; 
	 
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,PO Qnty, Order Uom,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time ", "90,130,80,80,80,50,80,80,80,80","975","220",0, $sql , "show_list_view", "id,po_number", "'populate_size_color_breakdown','size_color_breakdown'", 1, "is_confirmed,0,0,0,0,country_id,0,0,0,0,0", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,po_quantity,country_id,unit_price,po_total_price,excess_cut,plan_cut,date_diff", "size_color_breakdown_controller_update",'','0,0,3,3,1,0,2,2,2,1,1');
}*/
?>