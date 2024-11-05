<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down('requires/bundle_wise_sewing_output_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}

if ($action=="load_drop_down_working_location")
{
	echo create_drop_down( "cbo_working_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
/* 	echo create_drop_down( "cbo_floor", 180, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (9) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/bundle_wise_sewing_output_controller', $('#cbo_company_name').val()+'_'+$('#cbo_location').val()+'_'+this.value, 'load_drop_down_line', 'line_td' );",0 );
*/
 	echo create_drop_down( "cbo_floor", 180, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/bundle_wise_sewing_output_controller', $('#cbo_emb_company').val()+'_'+$('#cbo_location').val()+'_'+this.value+'_'+$('#txt_issue_date').val(), 'load_drop_down_line', 'line_td' );",0 );
	exit();
}

if ($action=="load_drop_down_line")
{
	list($company_id,$location,$floor,$issue_date)=explode("_",$data);

	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");

	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	$cond="";
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();

		if( $floor==0 && $location!=0 ) $cond = " and a.location_id= $location";
		if( $floor!=0 ) $cond = " and a.floor_id= $floor";

		if($db_type==0) $issue_date = date("Y-m-d",strtotime($issue_date));
		else $issue_date = change_date_format(date("Y-m-d",strtotime($issue_date)),'','',1);

		$cond.=" and b.pr_date='".$issue_date."'";

		if($db_type==0)
		{
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num order by a.prod_resource_num asc, a.id asc");
		}
		else if($db_type==2 || $db_type==1)
		{
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by  a.prod_resource_num,a.id asc");
		}
		 $line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$line_library[$val]]=$row[csf('id')];

				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		echo create_drop_down( "cbo_line_no", 180,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $floor==0 && $location!=0 ) $cond = " and location_name= $location";
		if( $floor!=0 ) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

		echo create_drop_down( "cbo_line_no", 160, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
	}
	exit();
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

	$delivery_basis=return_field_value("cut_panel_delevery","variable_settings_production","company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
	if($delivery_basis==3 || $delivery_basis==2) $delivery_basis=3; else $delivery_basis=1;
	echo "$('#delivery_basis').val(".$delivery_basis.");\n";
 	exit();
}

if ($action=="load_variable_settings_for_working_company")
{
	$sql_result = sql_select("select working_company_mandatory from variable_settings_production where company_name=$data and variable_list=41 and status_active=1");
	$working_company="";
 	foreach($sql_result as $row)
	{
		$working_company=$row[csf("working_company_mandatory")];
	}
	echo $working_company;
 	exit();
}

if($action=="load_drop_down_embro_issue_source")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = 0;//$explode_data[1]; // 0 Added for URMI

	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_sewing_output_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/bundle_wise_sewing_output_controller');get_php_form_data(this.value,'load_variable_settings_for_working_company','requires/bundle_wise_sewing_output_controller');load_html();" );
		}
		else
		{
			echo create_drop_down( "cbo_emb_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", 0, "load_drop_down( 'requires/bundle_wise_sewing_output_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/bundle_wise_sewing_output_controller');get_php_form_data(this.value,'load_variable_settings_for_working_company','requires/bundle_wise_sewing_output_controller');load_html();" );
		}
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 180, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/bundle_wise_sewing_output_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/bundle_wise_sewing_output_controller');get_php_form_data(this.value,'load_variable_settings_for_working_company','requires/bundle_wise_sewing_output_controller');load_html();",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 180, $blank_array,"", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_sewing_output_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/bundle_wise_sewing_output_controller');get_php_form_data(this.value,'load_variable_settings_for_working_company','requires/bundle_wise_sewing_output_controller');load_html();",0 );

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
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px" class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px" class="text_boxes" id="txt_search_common"	value="" />';
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
				document.getElementById('search_by_td').innerHTML='<select name="txt_search_common" style="width:230px" class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
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
                                    <input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'bundle_wise_sewing_output_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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
    exit();
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

 	$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_embe_cost_dtls c
			where
			a.job_no = b.job_no_mst and a.job_no = c.job_no and a.status_active=1 and  a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=$garments_nature and c.emb_name=2
			$sql_cond group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut order by b.id DESC";
	//echo $sql;die;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}

	$po_country_data_arr=array();
	$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	}

	$total_issu_qty_data_arr=array();
	$total_issu_qty_arr=sql_select( "select po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=2 group by po_break_down_id, item_number_id, country_id");

	foreach($total_issu_qty_arr as $row)
	{
		$total_issu_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
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
                <th width="80">Total Issue Qty</th>
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
				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
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
							<td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
                            <td width="80" align="right">
							<?php
								echo $total_cut_qty=$total_issu_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]
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

if($action=="bundle_popup_rescan")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);

?>
	<script>

		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($("#search"+i).css("display") !='none'){
				 js_set_value( i );
				}
			}
		}
		var selected_id = new Array();
		var selected_line=new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str)
		{

			if( selected_line.length>0 && trim($('#td_line' + str).attr('title'))!='')
			{
				if( jQuery.inArray( $('#td_line' + str).attr('title'), selected_line ) == -1 ) {
					alert('Line mix not allowed, please check again.');
					return;
				}
			}
			else
			{
				if( trim($('#td_line' + str).attr('title'))!='')
				{
					selected_line.push( $('#td_line' + str).attr('title') );
					$('#hidden_bundle_line').val( $('#td_line' + str).attr('title') );
				}
			}
			 //alert(str);
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			if(selected_id.length==0) selected_line.length=0;

			$('#hidden_bundle_nos').val( id );
			//$('#hidden_bundle_line').val( selected_line[0] );
		}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			selected_id = new Array();
			selected_line.length=0;
		}

    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:810px;">
		<legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact"  checked> is exact
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;  <input type="checkbox" value="1" name="is_defect" id="is_defect"> is defect </legend>
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Cut Year</th>
                    <th>Job No</th>
                    <th>Order No</th>
                    <th class="must_entry_caption">Cut No</th>
                    <th>Bundle No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                        <input type="hidden" name="hidden_bundle_line" id="hidden_bundle_line">
                    </th>
                </thead>
                <tr class="general">
                    <td align="center">
                    <?
						echo create_drop_down( "cbo_cut_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
					?>
                    </td>
                    <td align="center">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                    </td>
                    <td><input type="text" name="txt_cut_no" id="txt_cut_no" style="width:120px" class="text_boxes" /></td>
                    <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked')+'_'+'<? echo trim($line_id,','); ?>'+'_'+$('#is_defect').is(':checked'), 'create_bundle_rescan_search_list_view', 'search_div', 'bundle_wise_sewing_output_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_bundle_rescan_search_list_view")
{
	//_2____11534_2017_true_0
 	$ex_data = explode("_",$data);
	$txt_order_no = "%".trim($ex_data[0])."%";
	$company = $ex_data[1];
	if(trim($ex_data[2])) $bundle_no = "".trim($ex_data[2])."";
	else $bundle_no = "%".trim($ex_data[2])."%";
	$selectedBuldle=$ex_data[3];
	$job_no=$ex_data[4];
	$cut_no=$ex_data[5];
	$syear = substr($ex_data[6],2);
	$is_exact=$ex_data[7];
	$line_id=$ex_data[8];
	$is_defect=$ex_data[9];
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	/*if (trim($cut_no) == '')
    {
        echo "<h2 style='color:#D00; text-align:center;'><u>Please Select-Cut No</u></h2>";
        exit();
    }*/
	if($cut_no!='')
	{
		if($is_exact=='true') $cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
		else $cutCon = " and c.cut_no like '%".$cut_no."'";
	}

	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$line_arr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');

	  $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$company' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
	$resource_alocate_line = return_library_array("select id, line_number from prod_resource_mst", "id", "line_number");

	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr[$bn]=$bn;
	}

	if($job_no!='') $jobCon=" and f.job_no_prefix_num = $job_no";
	if($line_id==0) $line_id_cond=""; else $line_id_cond=" and a.sewing_line='$line_id'";

	$sql="SELECT c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, sum(c.production_qnty) as qty, e.po_number,a.sewing_line from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' and c.production_type=4 and a.status_active=1 and a.is_deleted=0 $jobCon $cutCon $line_id_cond group by c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, e.po_number,a.sewing_line order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";

	$result = sql_select($sql);
	foreach($result as $value)
	{
		$input_qty_arr[$value[csf('sewing_line')]][$value[csf('barcode_no')]]+=$value[csf('qty')];
		$input_barcode_arr[]=$value[csf('barcode_no')];
		$total_input[$value[csf('barcode_no')]]+=$value[csf('qty')];
	}
   if($is_defect=='false')
   {
   		 $receive_sql="select c.barcode_no, sum(c.production_qnty+c.alter_qty+c.spot_qty) as qty,a.sewing_line from pro_gmts_delivery_mst  a, pro_garments_production_dtls c where  a.id=c.delivery_mst_id  and c.production_type=5 and a.status_active=1 and a.is_deleted=0 $cutCon  and c.status_active=1 and c.is_deleted=0 group by c.barcode_no,a.sewing_line";
   }
   else
   {
   	    //$receive_sql="SELECT c.barcode_no, sum(case when c.replace_qty>c.reject_qty then  (c.alter_qty+c.spot_qty-(c.replace_qty-c.reject_qty  )) else    (c.alter_qty+c.spot_qty )   end ) as qty,a.sewing_line from pro_gmts_delivery_mst  a, pro_garments_production_dtls c where  a.id=c.delivery_mst_id  and c.production_type=5 and a.status_active=1 and a.is_deleted=0 $cutCon  and c.status_active=1 and c.is_deleted=0 group by c.barcode_no,a.sewing_line";

    	 $receive_sql="SELECT c.barcode_no, (sum( case when c.is_rescan=0 then  c.alter_qty+c.spot_qty else 0 end)-sum(case when c.is_rescan=0 and c.replace_qty>c.reject_qty then c.replace_qty-c.reject_qty else 0 end ))-sum(case when c.is_rescan=2 then c.production_qnty else 0 end ) as qty,a.sewing_line from pro_gmts_delivery_mst  a, pro_garments_production_dtls c where  a.id=c.delivery_mst_id  and c.production_type=5 and a.status_active=1 and a.is_deleted=0 $cutCon  and c.status_active=1 and c.is_deleted=0 group by c.barcode_no,a.sewing_line";
   }
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row){
		$output_qty_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]]+=$row[csf('qty')];
		$output_barcode_arr[]=$row[csf('barcode_no')];
		$total_output[$row[csf('barcode_no')]]+=$row[csf('qty')];
	}
	//print_r($output_qty_arr);

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="40">Year</th>
            <th width="40">Job</th>
            <th width="90">Order No</th>
            <th width="100">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="80">Line No</th>

            <th width="70">Cut No</th>
            <th width="70">Bundle No</th>
            <th>Qty</th>
        </thead>
	</table>
	<div style="width:850px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
            foreach ($result as $row)
            {
			 ///print_r($output_qty_arr[$row[csf('sewing_line')]]);
				 $rescan_qty=$input_qty_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]]-$output_qty_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]];
				 if($is_defect=='true')
				 {
				 	   $rescan_qty=$output_qty_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]] ;
				 }

				 $rescan_qty_total=$total_input[$row[csf('barcode_no')]]-$total_output[$row[csf('barcode_no')]];

				if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" && $rescan_qty>0 && $rescan_qty_total>0 && in_array($row[csf('barcode_no')],$output_barcode_arr) )
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="20">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="40" align="center"><p><? echo $year; ?></p></td>
						<td width="40" align="center"><p><? echo $job*1; ?></p></td>
						<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                        <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                        <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                        <td width="80" id="td_line<? echo $i;  ?>"  title="<? echo $row[csf('sewing_line')];?>">
                        <p>
                            <?
                            if ($prod_reso_allocation == 1) {
                                $sewing_line = $resource_alocate_line[$row[csf('sewing_line')]];
                                $sewing_line_arr = explode(",", $sewing_line);
                                $sewing_line_name = "";
                                foreach ($sewing_line_arr as $line_id) {
                                    $sewing_line_name .= $line_arr[$line_id] . ",";
                                }
                                $sewing_line_name = chop($sewing_line_name, ",");
                                echo $sewing_line_name;
                            } else {
                                echo $line_arr[$row[csf('sewing_line')]];
                            }
                            ?></p>
                            </td>
						<td width="70"><? echo $row[csf('cut_no')]; ?></td>
						<td width="70"><? echo $row[csf('bundle_no')]; ?></td>
                        <td align="right"><? echo $rescan_qty; ?></td>
					</tr>
					<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
                <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?
	exit();
}



if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$embel_name = $dataArr[2];
	$country_id = $dataArr[3];

	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name
			from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c
			where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id=$po_id group by a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name");

 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";

  		$dataArray=sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=4 and embel_name='$embel_name' THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0");
 		foreach($dataArray as $row)
		{
 			echo "$('#txt_cutting_qty').val('".$row[csf('totalcutting')]."');\n";
			echo "$('#txt_cumul_issue_qty').attr('placeholder','".$row[csf('totalprinting')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalprinting')]."');\n";
			$yet_to_produced = $row[csf('totalcutting')]-$row[csf('totalprinting')];
			echo "$('#txt_yet_to_issue').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
		}
  	}
 	exit();
}

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);

?>
	<script>

		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($("#search"+i).css("display") !='none'){
				 js_set_value( i );
				}
			}
		}
		var selected_id = new Array();
		var selected_line = new Array();
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str)
		{
			if(selected_line.length>0 && trim($('#td_line' + str).attr('title'))!='')
			{
				if( jQuery.inArray( $('#td_line' + str).attr('title'), selected_line ) == -1 ) {
					alert('Line mix not allowed, please check again.');
					return;
				}
			}
			else{
				if(trim($('#td_line' + str).attr('title'))!='')
				{
					selected_line.push( $('#td_line' + str).attr('title') );
					$('#hidden_bundle_line').val(  $('#td_line' + str).attr('title') );
				}
			}
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			if(selected_id.length==0) selected_line.length=0;

			$('#hidden_bundle_nos').val( id );
		}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

		function reset_hide_field()
		{
			$('#hidden_bundle_nos').val( '' );
			selected_id = new Array();
			selected_line.length=0;

		}

    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:810px;">
		<legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact"  checked> is exact</legend>
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Cut Year</th>
                    <th>Job No</th>
                    <th>Order No</th>
                    <th class="must_entry_caption">Cut No</th>
                    <th>Bundle No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                        <input type="hidden" name="hidden_bundle_line" id="hidden_bundle_line">
                    </th>
                </thead>
                <tr class="general">
                    <td align="center">
                    <?
						echo create_drop_down( "cbo_cut_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
					?>
                    </td>
                    <td align="center">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                    </td>
                    <td><input type="text" name="txt_cut_no" id="txt_cut_no" style="width:120px" class="text_boxes" /></td>
                    <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked')+'_'+'<? echo trim($line_id,','); ?>', 'create_bundle_search_list_view', 'search_div', 'bundle_wise_sewing_output_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_order_no = "%".trim($ex_data[0])."%";
	$company = $ex_data[1];
	//$bundle_no = "%".trim($ex_data[2])."%";
	if(trim($ex_data[2])) $bundle_no = "".trim($ex_data[2])."";
	else $bundle_no = "%".trim($ex_data[2])."%";
	$selectedBuldle=$ex_data[3];
	$job_no=$ex_data[4];
	$order_no =str_replace("'","", $ex_data[0]);
	$bndl_no =str_replace("'","", $ex_data[2]);
	$cut_no=$ex_data[5];
	$syear = substr($ex_data[6],2);
	$is_exact=$ex_data[7];
	$line_id=$ex_data[8];
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	//if($job_no!=''){$jobCon=" and d.job_no_mst like '%$job_no'";}
	/*if (trim($cut_no) == '')
    {
        echo "<h2 style='color:#D00; text-align:center;'><u>Please Select-Cut No</u></h2>";
        exit();
    }*/
	if($cut_no!='')
	{
		if($is_exact=='true')
		{
			 //$cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
			 //$cutCon_a = " and b.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";

			$cutCon = " and c.cut_no = '$cut_no'";
            $cutCon_a = " and b.cut_no = '$cut_no'";

		}
		else
		{
			$cutCon = " and c.cut_no like '%".$cut_no."%'";
			$cutCon_a = " and b.cut_no like '%".$cut_no."%'";
		}
	}

	if($job_no!='')
    {
        if($is_exact=='true') 
		{
			$jobCon=" and f.job_no = '$job_no'";
			$po_arr=return_library_array( "select id, id from wo_po_break_down where job_no_mst='$job_no'",'id','id');
			//print_r($po_arr);
			$poIds = implode(",",$po_arr);
			$jobCon_rescan = " and a.po_break_down_id in($poIds)";
		}
        else  
		{
			$jobCon=" and f.job_no like '%$job_no%'";
			$po_arr=return_library_array( "select id, id from wo_po_break_down where job_no_mst like '%$job_no%'",'id','id');			
			$poIds = implode(",",$po_arr);
			$jobCon_rescan = " and a.po_break_down_id in($poIds)";
		}
         
    }
    $orderCon="";
    if($order_no)
    {
        if($is_exact=='true') 
    	{
    		$orderCon=" and e.po_number = '$order_no'";
			$po_arr=return_library_array( "select id, id from wo_po_break_down where po_number='$order_no'",'id','id');
			//print_r($po_arr);
			$poIds = implode(",",$po_arr);
			$poCon_rescan = " and a.po_break_down_id in($poIds)";
    	}
        else  
        {
        	$orderCon=" and e.po_number like '%$order_no%'";
			$po_arr=return_library_array( "select id, id from wo_po_break_down where po_number like '%$order_no%'",'id','id');			
			$poIds = implode(",",$po_arr);
			$poCon_rescan = " and a.po_break_down_id in($poIds)";
        }

    }

    $bndlCon="";
    if($bndl_no)
    {
        if($is_exact=='true') $bndlCon=" and c.bundle_no = '$bndl_no'";
        else  $bndlCon=" and c.bundle_no like '%$bndl_no%'";

    }
    $year_cond="";
    if($syear)
    {
        $year_cond .= " and c.cut_no like '%-$syear-%' ";
    }




	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$line_arr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');

	$nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$company' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
	$resource_alocate_line = return_library_array("select id, line_number from prod_resource_mst", "id", "line_number");

	
	$scanne=sql_select( "select b.bundle_no, sum(b.production_qnty) as production_qnty,a.sewing_line from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=5 and b.status_active=1 and b.is_deleted=0 $cutCon_a $jobCon_rescan $poCon_rescan group by b.bundle_no,a.sewing_line");
	foreach($scanne as $row)
	{
		$scanned_bundle_arr[$row[csf("sewing_line")]][$row[csf("bundle_no")]]=$row[csf("production_qnty")];
		$duplicate_bundle[$row[csf("bundle_no")]] +=$row[csf("production_qnty")];

	}
	//die;
	//print_r($scanned_bundle_arr);
	foreach(explode(",",$selectedBuldle) as $bn)
	{
		$scanned_bundle_arr_ex[$bn]=$bn;
	}

	//if($job_no!='') $jobCon=" and f.job_no_prefix_num = $job_no";
	if($line_id==0) $line_id_cond=""; else $line_id_cond=" and a.sewing_line='$line_id'";

	$sql="SELECT c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, e.po_number,a.sewing_line from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company $orderCon $bndlCon and c.production_type=4 and a.status_active=1 and a.is_deleted=0 $jobCon $year_cond $cutCon $line_id_cond group by c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,a.sewing_line order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";

	//echo $sql;die;
	$result = sql_select($sql);

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="40">Year</th>
            <th width="40">Job</th>
            <th width="90">Order No</th>
            <th width="100">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="80">Line</th>
            <th width="70">Cut No</th>
            <th width="70">Bundle No</th>
            <th>Qty</th>
        </thead>
	</table>
	<div style="width:850px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
            foreach ($result as $row)
            {
				$row[csf('qty')]=$row[csf('qty')]-$scanned_bundle_arr[$row[csf("sewing_line")]][$row[csf('bundle_no')]];

				$balance_qnty=$row[csf('qty')]-$duplicate_bundle[$row[csf("bundle_no")]];

				//$scanned_bundle_arr[$row[csf("sewing_line")]][$row[csf("bundle_no")]]
				if($scanned_bundle_arr[$row[csf("sewing_line")]][$row[csf('bundle_no')]]=="" && $scanned_bundle_arr_ex[$row[csf('bundle_no')]]=='' && $balance_qnty>0)
				//if($row[csf('qty')]>0)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="20">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="40" align="center"><p><? echo $year; ?></p></td>
						<td width="40" align="center"><p><? echo $job*1; ?></p></td>
						<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                        <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                        <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                        <td width="80" id="td_line<? echo $i;?>" title="<? echo $row[csf('sewing_line')];?>">
                        <p>
                            <?
                            if ($prod_reso_allocation == 1) {
                                $sewing_line = $resource_alocate_line[$row[csf('sewing_line')]];
                                $sewing_line_arr = explode(",", $sewing_line);
                                $sewing_line_name = "";
                                foreach ($sewing_line_arr as $line_id) {
                                    $sewing_line_name .= $line_arr[$line_id] . ",";
                                }
                                $sewing_line_name = chop($sewing_line_name, ",");
                                echo $sewing_line_name;
                            } else {
                                echo $line_arr[$row[csf('sewing_line')]];
                            }
                            ?></p></td>
						<td width="70"><? echo $row[csf('cut_no')]; ?></td>
						<td width="70"><? echo $row[csf('bundle_no')]; ?></td>
                        <td align="right"><? echo $row[csf('qty')]; ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
                <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?
	exit();
}
if($action=="load_mst_data")
{
	$data=explode("__",$data);
	//echo $data;die;
 	$ex_data = "'".implode("','",explode(",",$data[0]))."'";
	if( $data[1]!=0 )
		$str_cond=" and a.sewing_line= $data[1]";
	else
		$str_cond="";

	$bundle_count=count(explode(",",$ex_data)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$ex_data),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($ex_data)";
	}

	$txt_order_no = "%".trim($ex_data[0])."%";
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	$sql_mst_data=sql_select("select a.sys_number, a.company_id, a.location_id, a.embel_name, a.embel_type, a.serving_company, a.floor_id, a.sewing_line, a.organic, a.production_source, a.delivery_date
	from pro_gmts_delivery_mst a, pro_garments_production_dtls c where a.id=c.delivery_mst_id $bundle_nos_cond $str_cond  and c.production_type=4 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.sys_number, a.company_id,a.location_id, a.embel_name, a.embel_type, a.serving_company,a.floor_id,a.sewing_line,a.organic,a.production_source,a.delivery_date");
	//print_r($sql_mst_data);die;

	if(count($sql_mst_data)>0)
	{
		foreach($sql_mst_data as $val)
		{

		//	$cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "barcode_no in ($bundle_nos)");

			echo "load_drop_down( 'requires/bundle_wise_sewing_output_controller', '".$val[csf('serving_company')]."', 'load_drop_down_location', 'location_td' );\n";

			echo "load_drop_down( 'requires/bundle_wise_sewing_output_controller','".$val[csf('serving_company')]."_".$val[csf('location_id')]."_".$val[csf('floor_id')]."_".$data[2]."', 'load_drop_down_line', 'line_td');\n";


			if($val[csf('production_source')]==1) {$serv_comp=$company_arr[$val[csf('serving_company')]]; }
			else { $serv_comp=$supplier_arr[$val[csf('serving_company')]];}
			$location=$location_arr[$val[csf('location_id')]];
			$floor=$floor_arr[$val[csf('floor_id')]];
			//echo "$('#txt_issue_challan_scan').val('".$val[csf('sys_number')]."');\n";
			//echo "$('#cbo_embel_type').val(".$val[csf('embel_type')].");\n";
			echo "$('#cbo_source').val('".$val[csf('production_source')]."');\n";
			echo "$('#cbo_emb_company_show').val('".$serv_comp."');\n";
			echo "$('#cbo_emb_company').val(".$val[csf('serving_company')].");\n";
			//echo "$('#txt_location_name').val('".$location."');\n";
			echo "$('#txt_floor_name').val('".$floor."');\n";
			echo "$('#txt_organic').val('".$val[csf('organic')]."');\n";
			echo "$('#cbo_floor').val(".$val[csf('floor_id')].");\n";
			echo "$('#cbo_location').val(".$val[csf('location_id')].");\n";
			echo "$('#cbo_line_no').val(".$val[csf('sewing_line')].");\n";

			echo "$('#common_data').val('".$val[csf('serving_company')]."');\n";

			echo "$('#cbo_company_name').attr('disabled','disabled');\n";
			echo "$('#cbo_source').attr('disabled','disabled');\n";
			echo "$('#cbo_emb_company_show').attr('disabled','disabled');\n";
			echo "$('#cbo_emb_company').attr('disabled','disabled');\n";
			//echo "$('#txt_location_name').val('".$location."');\n";
			echo "$('#txt_floor_name').attr('disabled','disabled');\n";
			echo "$('#txt_organic').attr('disabled','disabled');\n";
			echo "$('#cbo_floor').attr('disabled','disabled');\n";
			echo "$('#cbo_location').attr('disabled','disabled');\n";
			echo "$('#cbo_line_no').attr('disabled','disabled');\n";
		}
	}
	else
		echo "alert('All Bundle must be under Selected Line, Company, Sewing Company, Location, Floor. Please Check');\n";

	exit();
}

/*if($action=="qty_rescan_check")
{
	$data=explode("__",$data);
	$bundle_no="'".implode("','",explode(",",$data[0]))."'";
	$msg=1;



	$receive_sql="select c.barcode_no, sum(c.production_qnty) as qty from pro_gmts_delivery_mst a,pro_garments_production_dtls c where a.id=c.delivery_mst_id
	 and c.barcode_no  in ($bundle_no) and c.production_type=5 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_challan_id,c.barcode_no";

	//echo $receive_sql;die;
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row){
		$receive_qty_arr[$row[csf('barcode_no')]]=$row[csf('qty')];
	}

	if(!empty($receive_qty_arr))
	{
		$result=sql_select("select a.sys_number,b.bundle_no,b.barcode_no,a.id,sum(b.production_qnty) as qty from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.barcode_no in ($bundle_no) and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number, b.bundle_no,b.barcode_no,a.id");

		$datastr="";
		$issed_bundle_challan='';
		if(count($result)>0)
		{
			foreach ($result as $row)
			{
				$production_qty=$row[csf('qty')]-$receive_qty_arr[$row[csf('barcode_no')]]."*".$production_qty;
				if($production_qty>0)
				{
					$msg=2;
					if($datastr!="") $datastr.=",";
					$datastr.=$row[csf('id')]."*".$row[csf('sys_number')]."*".$row[csf('bundle_no')]."*".$production_qty;
				}
			}
		}
	}

	echo rtrim($msg)."_".rtrim($datastr);
	exit();
}*/

if($action=="challan_duplicate_check")
{
	$data=explode("__",$data);
	$bundle_no="'".implode("','",explode(",",$data[0]))."'";

	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond=""; $recbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$recbundle_nos_cond=" and (";
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_no),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$recbundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
		}
		$recbundle_nos_cond=chop($recbundle_nos_cond,'or ');
		$recbundle_nos_cond.=")";

		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$recbundle_nos_cond=" and c.barcode_no in ($bundle_no)";
		$bundle_nos_cond=" and b.barcode_no in ($bundle_no)";
	}

	$sql_mst_data=sql_select("select  a.sys_number,a.sewing_line, sum(c.production_qnty+c.reject_qty+c.alter_qty+c.spot_qty-c.replace_qty) as production_qnty,c.bundle_no
	from pro_gmts_delivery_mst a, pro_garments_production_dtls c where a.id=c.delivery_mst_id and c.production_type=5 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $recbundle_nos_cond group by a.sys_number,a.sewing_line,c.bundle_no");

	foreach ($sql_mst_data as $row)
	{
	 	$production_qnty[$row[csf('sewing_line')]][$row[csf('bundle_no')]]+=$row[csf('production_qnty')];
		$sys_number[$row[csf('sewing_line')]][$row[csf('bundle_no')]]=$row[csf('sys_number')];

	}
	$msg='';
	$sql_mst_data=sql_select("select a.sewing_line, sum(c.production_qnty) as production_qnty,c.bundle_no
	from pro_gmts_delivery_mst a, pro_garments_production_dtls c where a.id=c.delivery_mst_id and c.production_type=4 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $recbundle_nos_cond group by a.sewing_line,c.bundle_no");
	$kk=0;
	foreach ($sql_mst_data as $row)
	{
 		if($data[2])
		{
 			if($row[csf('sewing_line')] != $data[2])
 			{
 				$msg="Line Mixing Not Allow";
 				echo ($msg)."_".($datastr)."_".$datastrmst."_".$kk."_".$line;
 				die;
 			}
		}
		if( $row[csf('production_qnty')]-$production_qnty[$row[csf('sewing_line')]][$row[csf('bundle_no')]]>0)
		{
			//$production_qnty_new[$row[csf('sewing_line')]][$row[csf('bundle_no')]]+=$row[csf('production_qnty')];
			$line=$row[csf('sewing_line')];
			$kk++;
			if($datastrmst!='') $datastrmst .="**";
			$datastrmst .=$row[csf('bundle_no')]."*".$row[csf('sewing_line')];
		}
		else
		{
			$msg=" Duplicate scan found for bundle no:- ".$row[csf('bundle_no')]." in Challan no: ".$sys_number[$row[csf('sewing_line')]][$row[csf('bundle_no')]];
		}
	}
	echo ($msg)."_".($datastr)."_".$datastrmst."_".$kk."_".$line;
	exit();
	/*
	if(count($production_qnty_new)>1)
	{
		foreach ($sql_mst_data as $row)
		{
			if($datastrmst!='') $datastrmst .="**";
				$datastrmst .=$row[csf('bundle_no')]."*".$row[csf('sewing_line')];
		}1_ _UHM-17-193-14*41**UHM-17-193-14*43_2_43
	}*/

		//$scanned_bundle_arr=return_library_array( "select b.bundle_no, sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=5 and b.barcode_no in ($bundle_no)  and b.status_active=1 and b.is_deleted=0 group by b.bundle_no",'bundle_no','production_qnty');


	$result=sql_select("select a.sys_number,b.bundle_no from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond group by a.sys_number,b.bundle_no");
	$datastr="";
	if(count($result)>0)
	{
		foreach ($result as $row)
		{
			//$msg=2;
			//$datastr=$row[csf('bundle_no')]."##".$row[csf('sys_number')];
		}
	}

}
if($action=="bundle_popup_line_select")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	//echo $bundle_info;die;
?>
	<script>
		function js_set_value( str)
		{
			$('#hidden_bundle_info').val( str );
			parent.emailwindow.hide();
		}

    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:370px;">
		<legend>Select Only One Challan </legend>
        	<input type="hidden" name="hidden_bundle_info" id="hidden_bundle_info">
            <table cellpadding="0" cellspacing="0" width="350" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="150">Bundle No</th>
                    <th width="200">Line No</th>
                </thead>
                <?php
				//echo $data;//17990000009421
				$bundle_issue_arr=explode("**",$data);

				$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	//echo "select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0";
				$prod_reso_allocation = $nameArray[0][csf('auto_update')];
				$cond="";
				if($prod_reso_allocation==1)
				{
					$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
					$line_array=array();

					if($db_type==0)
					{
						$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num order by a.prod_resource_num asc, a.id asc");
					}
					else if($db_type==2 || $db_type==1)
					{
						$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by  a.prod_resource_num,a.id asc");
					}
					$line_merge=9999;
					foreach($line_data as $row)
					{
						$line='';
						$line_number=explode(",",$row[csf('line_number')]);
						foreach($line_number as $val)
						{
							if(count($line_number)>1)
							{
								$line_merge++;
								$new_arr[$line_merge]=$row[csf('id')];
							}
							else
								$new_arr[$line_library[$val]]=$row[csf('id')];

							if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
						}
						$line_array[$row[csf('id')]]=$line;
					}
					//ksort($new_arr);
					foreach($new_arr as $key=>$v)
					{
						$line_array_new[$v]=$line_array[$v];
					}
					//echo create_drop_down( "cbo_line_no", 180,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
				}
				else
					$line_array_new=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );


				$i=1;
				foreach($bundle_issue_arr as $value)
				{
					$val=explode("*",$value);
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$single_bundle=explode("*",$value);
				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $val[1]; ?>')">
                    <td align="center"><?php echo $val[0];?> </td>
                    <td align="center">	 <?php echo $line_array_new[$val[1]];?> </td>
                </tr>
                <?php
				$i++;
				}

				?>
           </table>

		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}

if($action=="qty_rescan_check")
{
	//echo $data;die;
	$data=explode("__",$data);
	$bundle_no="'".implode("','",explode(",",$data[0]))."'";
	$msg='';

	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond=""; $recbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$recbundle_nos_cond=" and (";
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_no),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$recbundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
		}
		$recbundle_nos_cond=chop($recbundle_nos_cond,'or ');
		$recbundle_nos_cond.=")";

		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$recbundle_nos_cond=" and c.barcode_no in ($bundle_no)";
		$bundle_nos_cond=" and b.barcode_no in ($bundle_no)";
	}

	$issue_qty=return_field_value("sum(b.production_qnty) as issue_qty","pro_gmts_delivery_mst a,pro_garments_production_dtls b","a.id=b.delivery_mst_id and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond","issue_qty");

	$receive_qty=return_field_value("sum(b.production_qnty) as issue_qty","pro_gmts_delivery_mst a,pro_garments_production_dtls b","a.id=b.delivery_mst_id  and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond group by b.bundle_no","issue_qty");

	if($receive_qty=='' && $issue_qty=='' ) $msg=3;
	else if($receive_qty=='' && $issue_qty>0 ) $msg=4;
	else if(($issue_qty-$receive_qty)>0 && $receive_qty>0 ) $msg=1;
	else if(($issue_qty-$receive_qty)==0 && $receive_qty>0 )
	{

		$result=sql_select("select a.sys_number,b.bundle_no from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recbundle_nos_cond group by a.sys_number,b.bundle_no");
		$datastr="";
		if(count($result)>0)
		{
			foreach ($result as $row)
			{
				$msg=2;
				$datastr=$row[csf('bundle_no')]."##".$row[csf('sys_number')];
			}
		}
	}

	$sql_mst_data=sql_select("select a.sewing_line, c.bundle_no
	from pro_gmts_delivery_mst a, pro_garments_production_dtls c where a.id=c.delivery_mst_id and c.production_type=4 and a.status_active=1 and a.is_deleted=0 and is_rescan=1 and c.status_active=1 and c.is_deleted=0 $recbundle_nos_cond");
	$kk=0;
	if(count($sql_mst_data)>0)
	{
		foreach ($sql_mst_data as $row)
		{
			$line=$row[csf('sewing_line')];
			if(count($sql_mst_data)>0)
			{
				$kk++;
				if($datastrmst!='') $datastrmst .="**";
					$datastrmst .=$row[csf('bundle_no')]."*".$row[csf('sewing_line')];
			}
		}
	}
	//else $line=$row[csf('sewing_line')];

	echo ($msg)."_".($datastr)."_".$datastrmst."_".$kk."_".$line;
	//echo rtrim($msg)."_".rtrim($datastr)."_".$datastrmst;
	exit();
}

if($action=="populate_bundle_data")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=explode(",",$ex_data[2]);
	$bundle_nos="'".implode("','",$bundle)."'";

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
	}

	/*foreach($bundle as $bn)
	{
		$curentscanned_bundle_arr[$bn]=$bn;
	}*/

	//$scanned_bundle_arr=return_library_array( "select b.bundle_no, sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=5 and b.barcode_no in ($bundle_nos) and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.bundle_no",'bundle_no','production_qnty');

	//$receive_sql= "select b.bundle_no, sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=5 and b.barcode_no in ($bundle_nos) and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.bundle_no";

$receive_sql="select c.barcode_no,c.bundle_no, sum(c.production_qnty+c.reject_qty+c.alter_qty+c.spot_qty-c.replace_qty) as qty,a.sewing_line from pro_gmts_delivery_mst a,pro_garments_production_dtls c where a.id=c.delivery_mst_id and c.production_type=5 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond $str_cond group by c.bundle_no,c.barcode_no,a.sewing_line order by  length(c.bundle_no) asc, c.bundle_no asc"; // and c.barcode_no  in ($bundle_nos)
	//echo $receive_sql;die;
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row){
		$receive_qty_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]]=$row[csf('qty')];
		$scanned_bundle_arr[$row[csf('sewing_line')]][$row[csf('bundle_no')]]=$row[csf('bundle_no')];
		$duplicate_bundle[$row[csf('bundle_no')]]+=$row[csf('qty')];
	}



 	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
//	 print_r($scanned_bundle_arr);die;
	$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}
	if( $ex_data[4]!=0 )
		$str_cond=" and a.sewing_line= $ex_data[4]";
	else
		$str_cond="";



	$sql="SELECT max(c.id)  as prdid,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, sum(c.production_qnty) as qty, e.po_number,a.sewing_line from pro_gmts_delivery_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name=$ex_data[3] and a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.production_type=4 $str_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number,a.sewing_line order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";

	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{
		$balance_qnty= $row[csf('qty')]-$duplicate_bundle[$row[csf('bundle_no')]];
		if( $scanned_bundle_arr[$row[csf('sewing_line')]][$row[csf('bundle_no')]]=="" && $curentscanned_bundle_arr[$row[csf('bundle_no')]]=='' && $balance_qnty>0)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//$qty=$bundle_qty_arr[$row[csf('bundle_no')]]-$row[csf('raj_qty')];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>


                <td width="50" id="rejQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="rejectQty[]" id="rejectQty_<? echo $i; ?>" style="width:35px" value="" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);" onDblClick="pop_entry_reject(<? echo $i; ?>)"/></td>
                <td width="50" id="altQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="alterQty[]" id="alterQty_<? echo $i; ?>" style="width:35px" value="" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);" onDblClick="pop_entry_alter(<? echo $i; ?>)"/></td>
                <td width="50" id="sptQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="spotQty[]" id="spotQty_<? echo $i; ?>" style="width:35px" value="" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);" onDblClick="pop_entry_spot(<? echo $i; ?>)"/></td>
                <td width="50" id="repQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="replaceQty[]" id="replaceQty_<? echo $i; ?>" style="width:35px" value="" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);"/></td>

                <td width="50" id="qcQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>

                <td id="button_1" align="center">

                    <input type="hidden" name="cutNo[]" id="cutNo<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
                    <input type="hidden" name="prod_qty[]" id="prod_qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"value="<? echo $row[csf('prdid')]; ?>"/>
                    <input type="hidden" name="actual_reject[]" id="actual_reject_<? echo $i; ?>"  value=""  />
                    <input type="hidden" name="actual_alter[]" id="actual_alter_<? echo $i; ?>"  value=""  />
                    <input type="hidden" name="actual_spot[]" id="actual_spot_<? echo $i; ?>"  value=""  />
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="0"/>
                </td>
			</tr>
		<?
        	$i--;
		}
	}
	exit();
}

if($action=="populate_bundle_data_rescan")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=explode(",",$ex_data[2]);
	$bundle_nos="'".implode("','",$bundle)."'";

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
	}

	//$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=5 and c.barcode_no in ($bundle_nos) and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no','bundle_no');

	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');



	$year_field="";
	if($db_type==0)
	{
		$year_field="YEAR(f.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(f.insert_date,'YYYY')";
	}
	if( $ex_data[4]!=0 )
		$str_cond=" and a.sewing_line= $ex_data[4]";
	else
		$str_cond="";


	$receive_sql="select c.barcode_no,c.bundle_no,sum(c.production_qnty+c.alter_qty+c.spot_qty)  as qty,a.sewing_line from pro_gmts_delivery_mst a,pro_garments_production_dtls c where a.id=c.delivery_mst_id and c.production_type=5 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond $str_cond group by c.bundle_no,c.barcode_no,a.sewing_line order by  length(c.bundle_no) asc, c.bundle_no asc";
	if($ex_data[5]=='true')
	{


		 $receive_sql="SELECT c.barcode_no,c.bundle_no, (sum( case when c.is_rescan=0 then  c.alter_qty+c.spot_qty else 0 end)-sum(case when c.is_rescan=0 and c.replace_qty>c.reject_qty then c.replace_qty-c.reject_qty else 0 end ))-sum(case when c.is_rescan=2 then c.production_qnty else 0 end ) as qty,a.sewing_line from pro_gmts_delivery_mst a,pro_garments_production_dtls c where a.id=c.delivery_mst_id and c.production_type=5 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond $str_cond group by c.bundle_no,c.barcode_no,a.sewing_line order by  length(c.bundle_no) asc, c.bundle_no asc";
	}

	//echo $receive_sql;die;
	$receive_result = sql_select($receive_sql);
	foreach ($receive_result as $row){
		$receive_qty_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]]=$row[csf('qty')];
		$scanned_bundle_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]]=$row[csf('bundle_no')];
		$total_output[$row[csf('barcode_no')]]+=$row[csf('qty')];
	}
	//print_r($receive_qty_arr);
	 //echo "TEST";
	// 17990000010408,17990000010405
	 $sql="SELECT max(c.id)  as prdid,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, sum(c.production_qnty) as qty, e.po_number,a.sewing_line from pro_gmts_delivery_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name=$ex_data[3] and a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.production_type=4 $str_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number,a.sewing_line  order by  c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";  //and is_rescan=1

	//  echo $sql;


	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{
		$rescan_val=1;
		$production_qty=$row[csf('qty')]-$receive_qty_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]];
		$rep_val="";
		if($ex_data[5]=='true')
		{
			$rescan_val=2;
			$production_qty= $receive_qty_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]];
			$rep_val=$production_qty;
		}
		$rescan_qty_total=$row[csf('qty')]-$total_output[$row[csf('barcode_no')]];

		//$production_qty=$input_qty_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]]-$receive_qty_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]];
		if( $production_qty>0 && $rescan_qty_total>0 )  //&& $scanned_bundle_arr[$row[csf('sewing_line')]][$row[csf('barcode_no')]]!=''
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//$qty=$bundle_qty_arr[$row[csf('bundle_no')]]-$row[csf('raj_qty')];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $production_qty; ?>&nbsp;</td>


                <td width="50" id="rejQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="rejectQty[]" <? echo $disabled;?> id="rejectQty_<? echo $i; ?>" style="width:35px" value="" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);" onDblClick="pop_entry_reject(<? echo $i; ?>)"/></td>
                <td width="50" id="altQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="alterQty[]" <? echo $disabled;?> id="alterQty_<? echo $i; ?>" style="width:35px" value="" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);" onDblClick="pop_entry_alter(<? echo $i; ?>)"/></td>
                <td width="50"  id="sptQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="spotQty[]" <? echo $disabled;?> id="spotQty_<? echo $i; ?>" style="width:35px" value="" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);" onDblClick="pop_entry_spot(<? echo $i; ?>)"/></td>
                <td width="50"  id="repQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="replaceQty[]" <? echo $disabled;?> id="replaceQty_<? echo $i; ?>" style="width:35px" value="<? //echo $rep_val;?>" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);"/></td>

                <td width="50" id="qcQty_<? echo $i; ?>" align="right"><? echo $production_qty; ?>&nbsp;</td>

                <td id="button_1" align="center">

                    <input type="hidden" name="cutNo[]" id="cutNo<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $production_qty; ?>"/>
                    <input type="hidden" name="prod_qty[]" id="prod_qty_<? echo $i; ?>" value="<? echo $production_qty; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row[csf('prdid')]; ?>"/>
                    <input type="hidden" name="actual_reject[]" id="actual_reject_<? echo $i; ?>"  value=""  />
                    <input type="hidden" name="actual_alter[]" id="actual_alter_<? echo $i; ?>"  value=""  />
                    <input type="hidden" name="actual_spot[]" id="actual_spot_<? echo $i; ?>"  value=""  />
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<? echo $rescan_val;?>"/>
                </td>
			</tr>
		<?
        	$i--;
		}
	}
	exit();
}

if ($action == "populate_bundle_data_check") {

	 $ex_data = explode("**", $data);
	$bundle = explode(",", $ex_data[0]);
	$mst_id = explode(",", $ex_data[2]);
	$bundle_nos = "'" . implode("','", $bundle) . "'";

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
	}

	$year_field = "";
	if ($db_type == 0) $year_field = "YEAR(f.insert_date)";
	else if ($db_type == 2) $year_field = "to_char(f.insert_date,'YYYY')";

	/*if( $ex_data[4]!=0 && ($ex_data[1]*1)>0 ) $str_cond=" and a.sewing_line= $ex_data[3]";
	else
	{
		$str_cond="";
	}*/

	//$sql = "select d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, f.company_name from pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.barcode_no in ($bundle_nos) and c.production_type=4 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, e.po_number, f.company_name order by c.bundle_no desc";
	//echo  "select a.sewing_line from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=4  and b.status_active=1 and b.is_deleted=0 and b.barcode_no in ($bundle_nos) $str_cond";
	$result=sql_select( "select a.sewing_line from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=4  and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond $str_cond");
	/*foreach($scanne as $row)
	{
		$scanned_bundle_arr[$row[csf("sewing_line")]][$row[csf("bundle_no")]]=$row[csf("production_qnty")];
	}
	*/

	//$result = sql_select($sql);
	$msg_type=1;
	if(count($result)>0){
		foreach($result as $row)
		{
			if( $row[csf('sewing_line')]!=trim($ex_data[4]) && ($ex_data[1]*1)>0  ){
				$msg_type=2;
			}
		}
	}
	else $msg_type=3;

	echo $msg_type;
	exit();
}

if($action=="populate_bundle_data_update")
{
 	$ex_data = explode("**",$data);
	$bundle=explode(",",$ex_data[0]);
	$mst_id=$ex_data[2];
	$company_id=$ex_data[3];
	$bundle_nos="'".implode("','",$bundle)."'";

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $rejbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$rejbundle_nos_cond=" and (";
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$rejbundle_nos_cond.=" bundle_no in($bundleNos) or ";
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$rejbundle_nos_cond=chop($rejbundle_nos_cond,'or ');
		$rejbundle_nos_cond.=")";

		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$rejbundle_nos_cond=" and bundle_no in ($bundle_nos)";
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
	}

	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$year_field="";
	if($db_type==0) $year_field="YEAR(f.insert_date)";
	else if($db_type==2) $year_field="to_char(f.insert_date,'YYYY')";

    // ============================== For Reject =================================
    $sql_bundle_reject=sql_select("SELECT a.defect_type_id,a.production_type,a.defect_point_id,a.defect_qty,a.bundle_no from  pro_gmts_prod_dft a where a.production_type=5 and defect_type_id =2 $rejbundle_nos_cond");
	 $bundle_reject_data=array();
	 foreach($sql_bundle_reject as $inf)
	 {
		 if(in_array($inf[csf('bundle_no')],$check_arr_rej))
		 {
		 	$bundle_reject_data[$inf[csf('bundle_no')]].="__".$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
		 }
		 else
		 {
		 	$bundle_reject_data[$inf[csf('bundle_no')]]=$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
		 }
		$check_arr_rej[]=$inf[csf('bundle_no')];
	}
    // ============================== For Alter =================================

	$sql_bundle_reject=sql_select("SELECT a.defect_type_id,a.production_type,a.defect_point_id,a.defect_qty,a.bundle_no from  pro_gmts_prod_dft a where a.production_type=5 and defect_type_id =3 $rejbundle_nos_cond");
	 $bundle_alter_data=array();
	 foreach($sql_bundle_reject as $inf)
	 {
		 if(in_array($inf[csf('bundle_no')],$check_arr_spot))
		 {
		 	$bundle_alter_data[$inf[csf('bundle_no')]].="__".$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
		 }
		 else
		 {
		 	$bundle_alter_data[$inf[csf('bundle_no')]]=$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
		 }
		$check_arr_spot[]=$inf[csf('bundle_no')];
	}
    // ============================== For Spot =================================

	 $sql_bundle_reject=sql_select("select a.defect_type_id,a.production_type,a.defect_point_id,a.defect_qty,a.bundle_no from  pro_gmts_prod_dft a where a.production_type=5 and defect_type_id =4 $rejbundle_nos_cond");
	 $bundle_spot_data=array();
	 foreach($sql_bundle_reject as $inf)
	 {
		 if(in_array($inf[csf('bundle_no')],$check_arr_spot))
		 {
		 	$bundle_spot_data[$inf[csf('bundle_no')]].="__".$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
		 }
		 else
		 {
		 	$bundle_spot_data[$inf[csf('bundle_no')]]=$inf[csf('defect_point_id')]."*".$inf[csf('defect_qty')];
		 }
		$check_arr_spot[]=$inf[csf('bundle_no')];
	 }



	$company_short_arr=return_library_array( "select id, company_short_name from lib_company where id=$company_id",'id','company_short_name');

	$sql="SELECT max(c.id)  as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, d.item_number_id,c.barcode_no, d.country_id,c.reject_qty,c.alter_qty,c.spot_qty,c.replace_qty, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.is_rescan, c.production_qnty as qty, e.po_number from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and a.delivery_mst_id=$mst_id and a.company_id=$company_id and c.production_type=5 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num,f.insert_date, f.buyer_name, d.item_number_id, d.country_id,c.reject_qty,c.alter_qty,c.spot_qty,c.replace_qty, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.is_rescan, c.production_qnty, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";  ///and f.company_name='$company_id'
	//echo $sql; die;
	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
			<td width="30"><? echo $i; ?></td>
			<td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
			<td width="50" align="center"><? echo $row[csf('year')]; ?></td>
			<td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
			<td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
			<td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
			<td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
			<td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
			<td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
			<td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>

			<td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo ($row[csf('qty')]+$row[csf('reject_qty')]+$row[csf('alter_qty')]+$row[csf('spot_qty')])-$row[csf('replace_qty')]; ?>&nbsp;</td>

			<?  $colo=''; if($bundle_alter_data[$row[csf('barcode_no')]]!='') $colo="  border-color:#FF0000";?>

			<td width="50" id="rejQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="rejectQty[]" id="rejectQty_<? echo $i; ?>" style="width:35px; " value="<? echo $row[csf('reject_qty')];?>" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);" onDblClick="pop_entry_reject(<? echo $i; ?>)"/></td>

			<td width="50" id="altQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="alterQty[]" id="alterQty_<? echo $i; ?>" style="width:35px; <? echo $colo; ?>" value="<? echo $row[csf('alter_qty')];?>" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);" onDblClick="pop_entry_alter(<? echo $i; ?>)"/></td>
            <?  $colo=''; if($bundle_spot_data[$row[csf('barcode_no')]]!='') $colo="  border-color:#FF0000";?>
			<td width="50" id="sptQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="spotQty[]" id="spotQty_<? echo $i; ?>" style="width:35px; <? echo $colo; ?>" value="<? echo $row[csf('spot_qty')];?>" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);" onDblClick="pop_entry_spot(<? echo $i; ?>)"/></td>
			<td width="50" id="repQty_<? echo $i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="replaceQty[]" id="replaceQty_<? echo $i; ?>" style="width:35px" value="<? echo $row[csf('replace_qty')];?>" onBlur="calculate_qcpasss(<? echo $i; ?>)" onKeyPress="return numOnly(this,event,this.id);"/></td>

			 <td width="50" id="qcQty_<? echo $i; ?>" align="right"><? echo $row[csf('qty')]; ?>&nbsp;</td>
			<td id="button_1" align="center">

				<input type="hidden" name="cutNo[]" id="cutNo<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>

				<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
				<input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
				<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
				<input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
				<input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
				<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
				<input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
				<input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
				<input type="hidden" name="prod_qty[]" id="prod_qty_<? echo $i; ?>" value="<? echo $row[csf('qty')]; ?>"/>
				<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row[csf('prdid')]; ?>"/>
				<input type="hidden" name="actual_reject[]" id="actual_reject_<? echo $i; ?>"  value="<? echo $bundle_reject_data[$row[csf('barcode_no')]];?>"  />
				<input type="hidden" name="actual_alter[]" id="actual_alter_<? echo $i; ?>"  value="<? echo $bundle_alter_data[$row[csf('barcode_no')]]; //$row[csf('reject_qty')]; // ?>"  />
				<input type="hidden" name="actual_spot[]" id="actual_spot_<? echo $i; ?>"  value="<? echo $bundle_spot_data[$row[csf('barcode_no')]]; //$row[csf('spot_qty')];// ?>"  />
                <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="<? echo $row[csf('is_rescan')]; ?>"/>
			</td>
		</tr>
	<?
		$i--;

	}
	exit();
}

if($action=="reject_qty_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$caption_name="";
	//print_r($sew_fin_reject_defect_type);die;

	?>
   <script>
   		function fnc_close()
		{
			var save_string='';
			var total_qty=0;
			$("#tbl_list_search").find('tr').each(function()
			{

				var txtDefectQnty=$(this).find('input[name="txtDefectQnty[]"]').val();
				var txtDefectId=$(this).find('input[name="txtDefectId[]"]').val();
				//var txtDefectUpdateId=$(this).find('input[name="txtDefectUpdateId[]"]').val();

				if(txtDefectQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtDefectId+"*"+txtDefectQnty;
						total_qty+=txtDefectQnty*1;
					}
					else
					{
						save_string+="__"+txtDefectId+"*"+txtDefectQnty;
						total_qty+=txtDefectQnty*1;
					}
				}
			});

			$('#actual_reject_infos').val( save_string );
			$('#actual_reject_qty').val(total_qty);

			parent.emailwindow.hide();
		}

	   function calculate_reject()
	   {
		 var reject_qty=0;
		 $("#tbl_list_search").find('tbody tr').each(function()
			{
				//alert(4);
				reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
			});
		   $("#reject_qty_td").text(reject_qty);
	   }

   </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../",$permission,1); ?>
            <fieldset style="width:360px;">

			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
            	<thead>
                	<tr><th colspan="3">Reject Record</th></tr>
                	<tr><th width="40">SL</th><th width="150">Reject Name</th><th>No. of Defect</th></tr>
                </thead>
            </table>
            <div style="width:350px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">
                <tbody>
                    <?

						$explSaveData = explode("__",$actual_infos);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("*",$val);
							//$defect_dataArray['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[0]]['defectid']=$difectVal[0];
							$defect_dataArray[$difectVal[0]]['defectQnty']=$difectVal[1];
						}

                        $i=1;
						$total_reject=0;
						sort($sew_fin_reject_type_arr);
                        foreach($sew_fin_reject_type_arr as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>"  onKeyUp="calculate_reject()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					?>
                    </tbody>
                    <tfoot>
                        <tr class="tbl_bottom">
                            <td align="right" colspan="2">Toral</td>

                            <td align="right"  id="reject_qty_td" style="padding-right:20px"> <? echo $total_reject; ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
			<table width="320" id="table_id">
				 <tr>
					<td align="center" colspan="3">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        <input type="hidden" id="actual_reject_infos" />
                         <input type="hidden" id="actual_reject_qty" />
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
  exit();
}

if($action=="alter_qty_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$caption_name="";
	//print_r($sew_fin_alter_defect_type);die;

	?>
   <script>
   		function fnc_close()
		{
			var save_string='';
			var total_qty=0;
			$("#tbl_list_search").find('tr').each(function()
			{

				var txtDefectQnty=$(this).find('input[name="txtDefectQnty[]"]').val();
				var txtDefectId=$(this).find('input[name="txtDefectId[]"]').val();
				//var txtDefectUpdateId=$(this).find('input[name="txtDefectUpdateId[]"]').val();

				if(txtDefectQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtDefectId+"*"+txtDefectQnty;
						total_qty+=txtDefectQnty*1;
					}
					else
					{
						save_string+="__"+txtDefectId+"*"+txtDefectQnty;
						total_qty+=txtDefectQnty*1;
					}
				}
			});

			$('#actual_alter_infos').val( save_string );
			$('#actual_alter_qty').val(total_qty);

			parent.emailwindow.hide();
		}

	   function calculate_alter()
	   {
		 var alter_qty=0;
		 $("#tbl_list_search").find('tbody tr').each(function()
			{
				//alert(4);
				alter_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
			});
		   $("#alter_qty_td").text(alter_qty);
	   }

   </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../",$permission,1); ?>
            <fieldset style="width:360px;">

			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
            	<thead>
                	<tr><th colspan="3">Alter Record</th></tr>
                	<tr><th width="40">SL</th><th width="150">Alter Name</th><th>No. of Defect</th></tr>
                </thead>
            </table>
            <div style="width:350px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">
                <tbody>
                    <?

						$explSaveData = explode("__",$actual_infos);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("*",$val);
							//$defect_dataArray['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[0]]['defectid']=$difectVal[0];
							$defect_dataArray[$difectVal[0]]['defectQnty']=$difectVal[1];
						}

                        $i=1;
						$total_alter=0;
                        foreach($sew_fin_alter_defect_type as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_alter+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>"  onKeyUp="calculate_alter()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					?>
                    </tbody>
                    <tfoot>
                        <tr class="tbl_bottom">
                            <td align="right" colspan="2">Toral</td>

                            <td align="right"  id="alter_qty_td" style="padding-right:20px"> <? echo $total_alter; ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
			<table width="320" id="table_id">
				 <tr>
					<td align="center" colspan="3">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        <input type="hidden" id="actual_alter_infos" />
                         <input type="hidden" id="actual_alter_qty" />
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
  exit();
}

if($action=="spot_qty_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$caption_name="";
	//print_r($sew_fin_alter_defect_type);die;

	?>
   <script>
   		function fnc_close()
		{
			var save_string='';
			var total_qty=0;
			$("#tbl_list_search").find('tr').each(function()
			{

				var txtDefectQnty=$(this).find('input[name="txtDefectQnty[]"]').val();
				var txtDefectId=$(this).find('input[name="txtDefectId[]"]').val();
				//var txtDefectUpdateId=$(this).find('input[name="txtDefectUpdateId[]"]').val();

				if(txtDefectQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtDefectId+"*"+txtDefectQnty;
						total_qty+=txtDefectQnty*1;
					}
					else
					{
						save_string+="__"+txtDefectId+"*"+txtDefectQnty;
						total_qty+=txtDefectQnty*1;
					}
				}
			});

			$('#actual_spot_infos').val( save_string );
			$('#actual_spot_qty').val(total_qty);

			parent.emailwindow.hide();
		}

	   function calculate_spot()
	   {
		 var spot_qty=0;
		 $("#tbl_list_search").find('tbody tr').each(function()
			{
				//alert(4);
				spot_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
			});
		   $("#spot_qty_td").text(spot_qty);
	   }

    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../",$permission,1); ?>
            <fieldset style="width:360px;">

			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
            	<thead>
                	<tr><th colspan="3">Reject Record</th></tr>
                	<tr><th width="40">SL</th><th width="150">Reject Name</th><th>No. of Defect</th></tr>
                </thead>
            </table>
            <div style="width:350px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">
                <tbody>
                    <?

						$explSaveData = explode("__",$actual_infos);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("*",$val);
							//$defect_dataArray['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[0]]['defectid']=$difectVal[0];
							$defect_dataArray[$difectVal[0]]['defectQnty']=$difectVal[1];
						}

                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_spot_defect_type as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>"  onKeyUp="calculate_spot()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					?>
                    </tbody>
                    <tfoot>
                        <tr class="tbl_bottom">
                            <td align="right" colspan="2">Toral</td>

                            <td align="right"  id="spot_qty_td" style="padding-right:20px"> <? echo $total_reject; ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
			<table width="320" id="table_id">
				 <tr>
					<td align="center" colspan="3">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        <input type="hidden" id="actual_spot_infos" />
                         <input type="hidden" id="actual_spot_qty" />
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
  exit();
}

if($action=="bundle_nos")
{
	$bundle_nos=return_library_array( "select b.bundle_no, b.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","bundle_no","barcode_no");
	$bundle_nos=implode(",",$bundle_nos);

	echo $bundle_nos;
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

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	//#############################################################################################//
	//order wise - color level, color and size level

	//$variableSettings=2;

	if( $variableSettings==2 ) // color level
	{
		if($db_type==0)
		{
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty,
					(select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END)
					from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty,
					(select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END)
					from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName'
					and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)
					group by color_number_id";
		}
		else
		{
			$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN c.production_type=2 and c.embel_name='$embelName' then b.production_qnty ELSE 0 END) as cur_production_qnty
					from wo_po_color_size_breakdown a
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					left join pro_garments_production_mst c on c.id=b.mst_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and
					a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";

		}

		$colorResult = sql_select($sql);
	}
	else if( $variableSettings==3 ) //color and size level
	{



		$dtlsData = sql_select("select a.color_size_break_down_id,
								sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
								sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and
								b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0
								and a.production_type in(1,2) group by a.color_size_break_down_id");

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
		}
		//print_r($color_size_qnty_array);

		$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)
				order by color_number_id, id";

		$colorResult = sql_select($sql);
	}

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

			$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
			$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];



			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';
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

?>
    <div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th width="150" align="center">Item Name</th>
                <th width="120" align="center">Country</th>
                <th width="80" align="center">Production Date</th>
                <th width="80" align="center">Production Qnty</th>
                <th width="150" align="center">Serving Company</th>
                <th width="120" align="center">Location</th>
                <th align="center">Challan No</th>
            </thead>
        </table>
    </div>
	<div style="width:100%;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
		<?php
			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("select id,po_break_down_id,item_number_id,country_id,production_date,production_quantity,production_source,
			serving_company,location,challan_no from pro_garments_production_mst where delivery_mst_id='$data' and status_active=1
			and is_deleted=0 order by id");
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
               	$total_production_qnty+=$selectResult[csf('production_quantity')];
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_issue_form_data','requires/bundle_wise_sewing_output_controller');" >
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="120" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                    <td width="80" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                    <td width="80" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
                    	<?php
                    		$source= $selectResult[csf('production_source')];
                            if($source==3) $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
                            else $serving_company= $company_arr[$selectResult[csf('serving_company')]];
                     	?>
                    <td width="150" align="center"><p><?php echo $serving_company; ?></p></td>
                    <td width="120" align="center"><p><? echo $location_arr[$selectResult[csf('location')]]; ?></p></td>
                    <td align="center"><p><?php echo $selectResult[csf('challan_no')]; ?></p></td>
                </tr>
            <?php
                $i++;
			}
			?>
		</table>
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

		$sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as
		order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active in(1,2,3) and is_deleted=0
		group by po_break_down_id, item_number_id, country_id");
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

if($action=="populate_issue_form_data")
{
	//production type=2 come from array
	$sqlResult =sql_select("select id,garments_nature,challan_no,po_break_down_id,item_number_id,country_id,production_source,serving_company,location,
	embel_name,embel_type,production_date,production_quantity,production_source,production_type,entry_break_down_type,production_hour,sewing_line,
	supervisor,carton_qty,remarks,floor_id,alter_qnty,reject_qnty,total_produced,yet_to_produced from pro_garments_production_mst where id='$data'
	and production_type='4' and status_active=1 and is_deleted=0 order by id");
  	//echo "sdfds".$sqlResult;die;
	foreach($sqlResult as $result)
	{
		//echo "$('#txt_issue_date').val('".change_date_format($result[csf('production_date')])."');\n";
  		echo "$('#txt_issue_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
		echo "$('#txt_iss_id').val('".$result[csf('id')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";

			$dataArray=sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=4 and embel_name=".$result[csf('embel_name')]." THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and is_deleted=0");
 		foreach($dataArray as $row)
		{
 			echo "$('#txt_cutting_qty').val('".$row[csf('totalCutting')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalPrinting')]."');\n";
			$yet_to_produced = $row[csf('totalCutting')]-$row[csf('totalPrinting')];
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
		}

		echo "get_php_form_data(".$result[csf('po_break_down_id')]."+'**'+".$result[csf("item_number_id")]."+'**'+".$result[csf("embel_name")]."+'**'+".$result[csf("country_id")].", 'populate_data_from_search_popup', 'requires/bundle_wise_sewing_output_controller' );\n";

		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";

		echo "show_list_view('".$result[csf('po_break_down_id')]."','show_country_listview','list_view_country','requires/bundle_wise_sewing_output_controller','');\n";

		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_sewing_bundle_output_entry',1,1);\n";

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
			$country_id = $result[csf('country_id')];

			$sql_dtls = sql_select("select color_size_break_down_id,production_qnty,size_number_id, color_number_id
			from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id
			and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
			foreach($sql_dtls as $row)
			{
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
			}

			//$variableSettings=2;



			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty,
					(select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END)
					from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty,
					(select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END)
					from pro_garments_production_dtls cur where cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)
					group by color_number_id";
				}
				else
				{
					$sql="select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as cur_production_qnty
					from wo_po_color_size_breakdown a
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					left join pro_garments_production_mst c on c.id=b.mst_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and
					a.status_active in(1,2,3)  group by a.item_number_id, a.color_number_id";

				}
			}
			else if( $variableSettings==3 ) //color and size level
			{
				$dtlsData = sql_select("select a.color_size_break_down_id,
				sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id'
				and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2)
				group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				}
				//print_r($color_size_qnty_array);

				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)
				order by color_number_id";

			}
			else // by default color and size level
			{
				$dtlsData = sql_select("select a.color_size_break_down_id,
				sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id'
				and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2)
				group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				}
				//print_r($color_size_qnty_array);

				$sql="select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)
					order by color_number_id";
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
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];


					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ></td></tr>';
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

	$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name=$cbo_working_company_name and variable_list=23 and status_active=1 and is_deleted=0");
	if(!$prod_reso_allocation){$prod_reso_allocation=1;}

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here entry form =160 all production pages
		//if ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}
		$file = fopen("output.txt", "r");
        $fread = fread($file,20);
        if($fread==1)
        {
            echo "420**Another action is runing. Please try after sometimes."; 
            die();
        }
        else
        {
            $file2 = fopen("output.txt", "w");
            fwrite($file2,"1");
            fclose($file2);
        }
        fclose($file);

		if(str_replace("'","",$txt_system_id)=="")
		{
			//$mst_id=return_next_id("id", "pro_gmts_delivery_mst", 1);

			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";// $year_cond="extract(year from insert_date)";//
			else $year_cond="";//defined Later
			$mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq",  "pro_gmts_delivery_mst", $con );

			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'SWO',0,date("Y",time()),0,0,5,0,0 ));


			$field_array_delivery="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, production_source, serving_company, floor_id,sewing_line, organic, delivery_date,working_company_id,working_location_id, inserted_by, insert_date";
			$data_array_delivery="(".$mst_id.",'".$new_sys_number[1]."','".(int)$new_sys_number[2]."','".$new_sys_number[0]."', ".$cbo_company_name.",5,".$cbo_location.",".$delivery_basis.",".$cbo_source.",".$cbo_emb_company.",".$cbo_floor.",".$cbo_line_no.",".$txt_organic.",".$txt_issue_date.",".$cbo_working_company_name.",".$cbo_working_location.",".$user_id.",'".$pc_date_time."')";
			$challan_no=(int)$new_sys_number[2];
			$txt_challan_no=$new_sys_number[0];
		}
		else
		{
			$mst_id=str_replace("'","",$txt_system_id);
			$txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
			$challan_no=(int) $txt_chal_no[3];

			$field_array_delivery="organic*delivery_date*updated_by*update_date";
			$data_array_delivery="".$txt_organic."*".$txt_issue_date."*".$user_id."*'".$pc_date_time."'";
		}

		if(str_replace("'","",$delivery_basis)==3)
		{
			if($db_type==2)
			{
				$txt_reporting_hour=str_replace("'","",$txt_issue_date)." ".str_replace("'","",$txt_reporting_hour);
				$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			}

			//$id=return_next_id("id", "pro_garments_production_mst", 1);
			$field_array_mst="id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_hour, production_quantity, reject_qnty, alter_qnty, spot_qnty, replace_qty, production_type, entry_break_down_type, remarks, floor_id,sewing_line, prod_reso_allo, inserted_by, insert_date";

			$cutArr=array();$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); $colorSizeIdArr=array();$bundleNoArr=array();

			for($j=1;$j<=$tot_row;$j++)
			{
				$bundleCheck="bundleNo_".$j;
				$is_rescans="isRescan_".$j;
                if($$is_rescans!=1 && $$is_rescans!=2)
                {
					$bundleCheckArr[trim($$bundleCheck)]=trim($$bundleCheck);
				}
				$cutNo="cutNo_".$j;
				$all_cut_no_arr[$$cutNo]=$$cutNo;
				
				$bundleNoArr[$$bundleCheck]=$$bundleCheck;
				
			}
			$cut_nums="'".implode("','", $all_cut_no_arr)."'";
			
            $bundle_wise_type_sql="SELECT b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no in ($cut_nums) ";
            $bundle_wise_type_array=array();
            $bundle_wise_data=sql_select($bundle_wise_type_sql);
            foreach($bundle_wise_data as $vals)
            {
                $bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
            }

			//echo '10**'. $bundle="'".implode("','",$bundleCheckArr)."'";die;
			
			$bundle="'".implode("','", $bundleNoArr)."'";
			$receive_sql="select c.barcode_no,c.bundle_no from pro_garments_production_dtls c where  c.bundle_no  in ($bundle)  and c.production_type=5 and c.status_active=1 and c.is_deleted=0 and (is_rescan=0 or is_rescan is null)";
			$receive_result = sql_select($receive_sql);
			foreach ($receive_result as $row)
			{
				$duplicate_bundle[trim($row[csf('bundle_no')])]=trim($row[csf('bundle_no')]);
			}

			
			//echo '10**2';print $receive_sql;die;
			
			
			for($j=1;$j<=$tot_row;$j++)
			{
				$cutNo="cutNo_".$j;
				$bundleNo="bundleNo_".$j;
				$barcodeNo="barcodeNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorId="colorId_".$j;
				$sizeId="sizeId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$qty="qty_".$j;
				$rejectQty="rejectQty_".$j;
				$alterQty="alterQty_".$j;
				$spotQty="spotQty_".$j;
				$replaceQty="replaceQty_".$j;
				$checkRescan="isRescan_".$j;
				if($duplicate_bundle[trim($$bundleNo)]=="")
				{
					$bundleCutArr[$$bundleNo]=$$cutNo;
					$bundleBarcodeArr[$$bundleNo]=$$barcodeNo;
					$cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
					$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
					$mstArrRej[$$orderId][$$gmtsitemId][$$countryId]+=$$rejectQty*1;
					$mstArrAlt[$$orderId][$$gmtsitemId][$$countryId]+=$$alterQty*1;
					$mstArrSpot[$$orderId][$$gmtsitemId][$$countryId]+=$$spotQty*1;
					$mstArrRep[$$orderId][$$gmtsitemId][$$countryId]+=$$replaceQty*1;

					$colorSizeArr[$$bundleNo]=$$orderId."**".$$gmtsitemId."**".$$countryId;
					$dtlsArr[$$bundleNo]+=$$qty;
					$dtlsArrRej[$$bundleNo]+=$$rejectQty*1;
					$dtlsArrColorSize[$$bundleNo]=$$colorSizeId;
					$bundleRescanArr[$$bundleNo]=$$checkRescan;

					$dtlsArrAlt[$$bundleNo]+=$$alterQty*1;
					$dtlsArrSpot[$$bundleNo]+=$$spotQty*1;
					$dtlsArrRep[$$bundleNo]+=$$replaceQty*1;

				}

			}
			
			foreach($mstArr as $orderId=>$orderData)
			{
				foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
				{
					foreach($gmtsItemIdData as $countryId=>$qty)
					{
						 $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
						if($db_type==2)
						{

							$data_array_mst.=" INTO pro_garments_production_mst (".$field_array_mst.") VALUES(".$id.",".$mst_id.",'".$cutArr[$orderId][$gmtsItemId][$countryId]."',".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.", ".$gmtsItemId.",".$countryId.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$txt_issue_date.",".$txt_reporting_hour.",".$qty.",'".$mstArrRej[$orderId][$gmtsItemId][$countryId]."','".$mstArrAlt[$orderId][$gmtsItemId][$countryId]."','".$mstArrSpot[$orderId][$gmtsItemId][$countryId]."','".$mstArrRep[$orderId][$gmtsItemId][$countryId]."',5,".$sewing_production_variable.",".$txt_remark.",".$cbo_floor.",".$cbo_line_no.",".$prod_reso_allocation.",".$user_id.",'".$pc_date_time."')";
						}
						else
						{
 							if($data_array_mst!="") $data_array_mst.=",";
							$data_array_mst.="(".$id.",".$mst_id.",'".$cutArr[$orderId][$gmtsItemId][$countryId]."',".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.", ".$gmtsItemId.",".$countryId.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$txt_issue_date.",".$txt_reporting_hour.",".$qty.",'".$mstArrRej[$orderId][$gmtsItemId][$countryId]."','".$mstArrAlt[$orderId][$gmtsItemId][$countryId]."','".$mstArrSpot[$orderId][$gmtsItemId][$countryId]."','".$mstArrRep[$orderId][$gmtsItemId][$countryId]."',5,".$sewing_production_variable.",".$txt_remark.",".$cbo_floor.",".$cbo_line_no.",".$prod_reso_allocation.",".$user_id.",'".$pc_date_time."')";
						}
						$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
						//$id = $id+1;
					}
				}
			}
			  // echo "10**".$query="INSERT ALL".$data_array_mst." SELECT * FROM dual";die;
			  //echo "0**"; echo sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst );die;

			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$field_array_dtls="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,reject_qty,alter_qty,spot_qty,replace_qty,cut_no,bundle_no,barcode_no,is_rescan,color_type_id";

			//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defect="id, mst_id, production_type, po_break_down_id,color_size_break_down_id, defect_type_id, defect_point_id, defect_qty, bundle_no, inserted_by, insert_date";

			foreach($dtlsArr as $bundle_no=>$qty)
			{
				$colorSizedData=explode("**",$colorSizeArr[$bundle_no]);
				$gmtsMstId=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				//$cut_no=$cutArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				$cut_no=$bundleCutArr[$bundle_no];
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",5,'".$dtlsArrColorSize[$bundle_no]."','".$qty."','".$dtlsArrRej[$bundle_no]."','".$dtlsArrAlt[$bundle_no]."','".$dtlsArrSpot[$bundle_no]."','".$dtlsArrRep[$bundle_no]."','".$cut_no."','".$bundle_no."','".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."','".$bundle_wise_type_array[$bundle_no]."')";
				$colorSizeIdArr[$bundle_no]=$gmtsMstId;
				//$dtls_id = $dtls_id+1;
			}


			for($j=1;$j<=$tot_row;$j++)
			{
				$bundleNo="bundleNo_".$j;
				$orderId="orderId_".$j;
				$dtlsId=$colorSizeIdArr[$$bundleNo];
				$actual_reject="actual_reject_".$j;
				$actual_alter="actual_alter_".$j;
				$actual_spot="actual_spot_".$j;
				$colorSizeId="colorSizeId_".$j;

				$rls=0;
				//Reject..........
				if($$actual_reject!="")
				{
					$actual_reject_info=explode("__",$$actual_reject);
					for( $rls=0; $rls<count($actual_reject_info); $rls++ )
					{
						list($defectPointId,$defect_qty)=explode("*",$actual_reject_info[$rls]);
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
						$data_array_defect.="(".$dft_id.",".$dtlsId.",5,".$$orderId.",".$$colorSizeId.",2,".$defectPointId.",'".$defect_qty."','".$bundleBarcodeArr[$$bundleNo]."',".$user_id.",'".$pc_date_time."')";
						//$dft_id = $dft_id+1;
					}
				}
				//Alter..........
				if($$actual_alter!="")
				{
					$actual_alter_info=explode("__",$$actual_alter);
					for( $rls=0; $rls<count($actual_alter_info); $rls++ )
					{
						list($defectPointId,$defect_qty)=explode("*",$actual_alter_info[$rls]);
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
						$data_array_defect.="(".$dft_id.",".$dtlsId.",5,".$$orderId.",".$$colorSizeId.",3,".$defectPointId.",'".$defect_qty."','".$bundleBarcodeArr[$$bundleNo]."',".$user_id.",'".$pc_date_time."')";
						//$dft_id = $dft_id+1;
					}
				}

				//spot..........
				if($$actual_spot!="")
				{
					$actual_spot_info=explode("__",$$actual_spot);
					for( $rls=0; $rls<count($actual_spot_info); $rls++ )
					{
						list($defectPointId,$defect_qty)=explode("*",$actual_spot_info[$rls]);
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
						$data_array_defect.="(".$dft_id.",".$dtlsId.",5,".$$orderId.",".$$colorSizeId.",4,".$defectPointId.",'".$defect_qty."','".$bundleBarcodeArr[$$bundleNo]."',".$user_id.",'".$pc_date_time."')";
						//$dft_id = $dft_id+1;
					}
				}
			}

			if(str_replace("'","",$txt_system_id)=="")
			{
				$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			}
			else
			{
				$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			}

			if($db_type==2)
			{
				$query="INSERT ALL".$data_array_mst." SELECT * FROM dual";
				$rID=execute_query($query);
			}
			else
			{
				$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
			}

			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
			if($data_array_defect!="")
			{
				$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
			}


			//echo "10**$data_array_defect";die;
			//$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);

			//echo "10**insert into pro_garments_production_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		 	// echo "10**".$challanrID ."&&". $query ."&&". $dtlsrID ."&&". $defectQ;die;
			//release lock table


			if($db_type==0)
			{
				if($challanrID && $rID && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($challanrID && $rID && $dtlsrID)
				{
					oci_commit($con);
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
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
		  $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq","pro_garments_production_mst", $con );
			$field_array1="id, delivery_mst_id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity,reject_qnty,alter_qnty,spot_qnty,replace_qty, production_type,  production_type, entry_break_down_type, remarks, floor_id,sewing_line,prod_reso_allo, inserted_by, insert_date";

			$data_array1="(".$id.",".$mst_id.",".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$hidden_po_break_down_id.", ".$cbo_item_name.",".$cbo_country_name.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$txt_issue_date.",".$txt_issue_qty.",'".$mstArrRej[$orderId][$gmtsItemId][$countryId]."','".$mstArrAlt[$orderId][$gmtsItemId][$countryId]."','".$mstArrSpot[$orderId][$gmtsItemId][$countryId]."','".$mstArrRep[$orderId][$gmtsItemId][$countryId]."',5,".$sewing_production_variable.",".$txt_remark.",".$cbo_floor.",".$cbo_line_no.",".$prod_reso_allocation.",".$user_id.",'".$pc_date_time."')";


			//echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;
			// pro_garments_production_dtls table entry here ----------------------------------///
			$field_array="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty";
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}


				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue);
			//	$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$mst_id.",".$id.",5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					else $data_array .= ",(".$dtls_id.",".$mst_id.",".$id.",5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}

			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf('id')];
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

					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$mst_id.",".$id.",5,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					else $data_array .= ",(".$dtls_id.",".$mst_id.",".$id.",5,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
			//echo "10** insert into pro_garments_production_mst($field_array1)values".$data_array1;die;
			$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
			if(str_replace("'","",$txt_system_id)=="")
			{
				$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			}
			else
			{
				$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			}

			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
			{
				$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			}



		 //echo "10**".$rID."**".$challanrID."**".$dtlsrID."**".$challanrID;die;
			if($db_type==0)
			{
				if($rID && $challanrID && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($rID && $challanrID && $dtlsrID)
				{
					oci_commit($con);
					echo "0**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}		
           
        $file = fopen("output.txt", "w");
        fwrite($file,"0");
        fclose($file);
		//check_table_status( 160,0);
		disconnect($con);

		die;

	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		 //if  ( check_table_status( 160, 1 )==0 ) { echo "15**1"; die;}
		$mst_id=str_replace("'","",$txt_system_id);
		$txt_chal_no=explode("-",str_replace("'","",$txt_challan_no));
		$challan_no=(int) $txt_chal_no[3];

			for($j=1;$j<=$tot_row;$j++)
			{
				$bundleCheck="bundleNo_".$j;
				$is_rescans="isRescan_".$j;
                if($$is_rescans!=1 && $$is_rescans!=2)
                {
					$bundleCheckArr[trim($$bundleCheck)]=trim($$bundleCheck);
				}

				$cutNo="cutNo_".$j;
				$all_cut_no_arr[$$cutNo]=$$cutNo;

			}
			$cut_nums="'".implode("','", $all_cut_no_arr)."'";
            $bundle_wise_type_sql="SELECT b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no in ($cut_nums) ";
            $bundle_wise_type_array=array();
            $bundle_wise_data=sql_select($bundle_wise_type_sql);
            foreach($bundle_wise_data as $vals)
            {
                $bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
            }

			$bundle="'".implode("','",$bundleCheckArr)."'";
			$receive_sql="select c.barcode_no,c.bundle_no from pro_garments_production_dtls c where  c.bundle_no  in ($bundle)  and c.production_type=5 and c.status_active=1 and c.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null) and c.delivery_mst_id!=$mst_id";
			$receive_result = sql_select($receive_sql);
			foreach ($receive_result as $row)
			{

				$duplicate_bundles[trim($row[csf('bundle_no')])]=trim($row[csf('bundle_no')]);
			}

		if(str_replace("'","",$delivery_basis)==3)
		{
			$field_array_delivery="organic*delivery_date*updated_by*update_date";
			$data_array_delivery="".$txt_organic."*".$txt_issue_date."*".$user_id."*'".$pc_date_time."'";

			//$id=return_next_id("id", "pro_garments_production_mst", 1);
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);

			if($db_type==2)
			{
				$txt_reporting_hour=str_replace("'","",$txt_issue_date)." ".str_replace("'","",$txt_reporting_hour);
				$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			}
			//echo "10**";
			//-------
			$production_mst_id_arr=return_library_array( "select id, id as mst_id  from  pro_garments_production_mst where delivery_mst_id=$mst_id and production_type=5 and status_active=1 and is_deleted=0",'id','mst_id');
			//print_r($production_mst_id_arr);
			$production_mst_id=implode(',',$production_mst_id_arr);
			$rejectDelete=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id in (".$production_mst_id.") and defect_type_id in(2,3,4) and production_type=5");
			//echo "DELETE FROM pro_gmts_prod_dft WHERE mst_id in (".$production_mst_id.") and defect_type_id in(3,4) and production_type=5";

			$delete = execute_query("UPDATE pro_garments_production_mst SET is_deleted=1, status_active=0 WHERE delivery_mst_id=$mst_id and production_type=5");
			$delete_dtls = execute_query("UPDATE pro_garments_production_dtls SET is_deleted=1, status_active=0 WHERE delivery_mst_id=$mst_id and production_type=5");
			$delete_bundle = execute_query("UPDATE pro_cut_delivery_color_dtls SET is_deleted=1, status_active=0 WHERE delivery_mst_id=$mst_id and production_type=5");

			$field_array_mst="id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date,production_hour, production_quantity,reject_qnty,alter_qnty,spot_qnty,replace_qty, production_type, entry_break_down_type, remarks, floor_id,sewing_line,prod_reso_allo, inserted_by, insert_date";

			$cutArr=array();$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array(); $colorSizeIdArr=array();
			for($j=1;$j<=$tot_row;$j++)
			{
				$cutNo="cutNo_".$j;
				$bundleNo="bundleNo_".$j;
				$barcodeNo="barcodeNo_".$j;
				$orderId="orderId_".$j;
				$gmtsitemId="gmtsitemId_".$j;
				$countryId="countryId_".$j;
				$colorId="colorId_".$j;
				$sizeId="sizeId_".$j;
				$colorSizeId="colorSizeId_".$j;
				$qty="qty_".$j;
				$rejectQty="rejectQty_".$j;
				$alterQty="alterQty_".$j;
				$spotQty="spotQty_".$j;
				$replaceQty="replaceQty_".$j;
				$checkRescan="isRescan_".$j;

				 if($duplicate_bundles[trim($$bundleNo)]=="")
				 {
					$bundleCutArr[$$bundleNo]=$$cutNo;
					$cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
					$mstArr[$$orderId][$$gmtsitemId][$$countryId]+=$$qty;
					$mstArrRej[$$orderId][$$gmtsitemId][$$countryId]+=$$rejectQty;
					$mstArrAlt[$$orderId][$$gmtsitemId][$$countryId]+=$$alterQty;
					$mstArrSpot[$$orderId][$$gmtsitemId][$$countryId]+=$$spotQty;
					$mstArrRep[$$orderId][$$gmtsitemId][$$countryId]+=$$replaceQty;
					$colorSizeArr[$$bundleNo]=$$orderId."**".$$gmtsitemId."**".$$countryId;
					$dtlsArr[$$bundleNo]+=$$qty;
					$dtlsArrRej[$$bundleNo]+=$$rejectQty;
					$dtlsArrColorSize[$$bundleNo]=$$colorSizeId;
					$dtlsArrAlt[$$bundleNo]+=$$alterQty;
					$dtlsArrSpot[$$bundleNo]+=$$spotQty;
					$dtlsArrRep[$$bundleNo]+=$$replaceQty;
					$bundleRescanArr[$$bundleNo]=$$checkRescan;
					$bundleBarcodeArr[$$bundleNo]=$$barcodeNo;
 				 }


			}

			foreach($mstArr as $orderId=>$orderData)
			{
				foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
				{
					foreach($gmtsItemIdData as $countryId=>$qty)
					{
						$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
						if($db_type==2)
						{
							$data_array_mst.=" INTO pro_garments_production_mst (".$field_array_mst.") VALUES(".$id.",".$mst_id.",'".$cutArr[$orderId][$gmtsItemId][$countryId]."',".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.", ".$gmtsItemId.",".$countryId.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$txt_issue_date.",".$txt_reporting_hour.",".$qty.",'".$mstArrRej[$orderId][$gmtsItemId][$countryId]."','".$mstArrAlt[$orderId][$gmtsItemId][$countryId]."','".$mstArrSpot[$orderId][$gmtsItemId][$countryId]."','".$mstArrRep[$orderId][$gmtsItemId][$countryId]."',5,".$sewing_production_variable.",".$txt_remark.",".$cbo_floor.",".$cbo_line_no.",".$prod_reso_allocation.",".$user_id.",'".$pc_date_time."')";
						}
						else
						{
							if($data_array_mst!="") $data_array_mst.=",";
							$data_array_mst.="(".$id.",".$mst_id.",'".$cutArr[$orderId][$gmtsItemId][$countryId]."',".$cbo_company_name.",".$garments_nature.",'".$challan_no."',".$orderId.", ".$gmtsItemId.",".$countryId.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$txt_issue_date.",".$txt_reporting_hour.",".$qty.",'".$mstArrRej[$orderId][$gmtsItemId][$countryId]."','".$mstArrAlt[$orderId][$gmtsItemId][$countryId]."','".$mstArrSpot[$orderId][$gmtsItemId][$countryId]."','".$mstArrRep[$orderId][$gmtsItemId][$countryId]."',5,".$sewing_production_variable.",".$txt_remark.",".$cbo_floor.",".$cbo_line_no.",".$prod_reso_allocation.",".$user_id.",'".$pc_date_time."')";
						}
						$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
						//$id = $id+1;
					}
				}
			}

			$field_array_dtls="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,reject_qty,alter_qty,spot_qty,replace_qty,cut_no,bundle_no,barcode_no,is_rescan,color_type_id";

			foreach($dtlsArr as $bundle_no=>$qty)
			{
				$colorSizedData=explode("**",$colorSizeArr[$bundle_no]);
				$gmtsMstId=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				//$cut_no=$cutArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
				$cut_no=$bundleCutArr[$bundle_no];
				if($data_array_dtls!="") $data_array_dtls.=",";
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
				$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",5,'".$dtlsArrColorSize[$bundle_no]."','".$qty."','".$dtlsArrRej[$bundle_no]."','".$dtlsArrAlt[$bundle_no]."','".$dtlsArrSpot[$bundle_no]."','".$dtlsArrRep[$bundle_no]."','".$cut_no."','".$bundle_no."','".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."','".$bundle_wise_type_array[$bundle_no]."')";
				$colorSizeIdArr[$bundle_no]=$gmtsMstId;
				//$dtls_id = $dtls_id+1;
			}

			$data_array_defect="";
			$field_array_defect="id, mst_id, production_type, po_break_down_id,color_size_break_down_id, defect_type_id, defect_point_id, defect_qty, bundle_no, inserted_by, insert_date";
			for($m=1;$m<=$tot_row;$m++)
			{
				$bundleNo="bundleNo_".$m;
				$orderId="orderId_".$m;
				$dtlsId=$colorSizeIdArr[$$bundleNo];
				$actual_reject="actual_reject_".$m;
				$actual_alter="actual_alter_".$m;
				$actual_spot="actual_spot_".$m;
				$colorSizeId="colorSizeId_".$m;

				$rls=0;
				//Reject..........
				if($$actual_reject!="")
				{
					$actual_reject_info=explode("__",$$actual_reject);
					foreach($actual_reject_info as $altval)
					{
						$ex_altval=""; $defectPointId=0; $defect_qty=0;
						$ex_altval=explode("*",$altval);
						$defectPointId=$ex_altval[0];
						$defect_qty=$ex_altval[1];
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$data_array_defect.="(".$dft_id.",".$dtlsId.",5,".$$orderId.",".$$colorSizeId.",2,".$defectPointId.",'".$defect_qty."','".$bundleBarcodeArr[$$bundleNo]."',".$user_id.",'".$pc_date_time."')";
						//$dft_id = $dft_id+1;
					}


					/*for($rls=0;$rls<count($actual_reject_info); $rls++)
					{
						list($defectPointId,$defect_qty)=explode("*",$actual_alter_info[$rls]);
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$data_array_defect.="(".$dft_id.",".$dtlsId.",5,".$$orderId.",3,".$defectPointId.",'".$defect_qty."','".$$bundleNo."',".$user_id.",'".$pc_date_time."')";
						$dft_id = $dft_id+1;
					}*/
				}

				//Alter..........
				if($$actual_alter!="")
				{
					$actual_alter_info=explode("__",$$actual_alter);
					foreach($actual_alter_info as $altval)
					{
						$ex_altval=""; $defectPointId=0; $defect_qty=0;
						$ex_altval=explode("*",$altval);
						$defectPointId=$ex_altval[0];
						$defect_qty=$ex_altval[1];
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$data_array_defect.="(".$dft_id.",".$dtlsId.",5,".$$orderId.",".$$colorSizeId.",3,".$defectPointId.",'".$defect_qty."','".$bundleBarcodeArr[$$bundleNo]."',".$user_id.",'".$pc_date_time."')";
						//$dft_id = $dft_id+1;
					}


					/*for($rls=0;$rls<count($actual_alter_info); $rls++)
					{
						list($defectPointId,$defect_qty)=explode("*",$actual_alter_info[$rls]);
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$data_array_defect.="(".$dft_id.",".$dtlsId.",5,".$$orderId.",3,".$defectPointId.",'".$defect_qty."','".$$bundleNo."',".$user_id.",'".$pc_date_time."')";
						$dft_id = $dft_id+1;
					}*/
				}

				//spot..........
				if($$actual_spot!="")
				{
					$actual_spot_info=explode("__",$$actual_spot);
					foreach($actual_spot_info as $sptval)
					{
						$ex_altval=""; $defectPointId=0; $defect_qty=0;
						$ex_sptval=explode("*",$sptval);
						$defectPointId=$ex_sptval[0];
						$defect_qty=$ex_sptval[1];
						$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$data_array_defect.="(".$dft_id.",".$dtlsId.",5,".$$orderId.",".$$colorSizeId.",4,".$defectPointId.",'".$defect_qty."','".$bundleBarcodeArr[$$bundleNo]."',".$user_id.",'".$pc_date_time."')";
						//$dft_id = $dft_id+1;
					}




					/*for($rls=0;$rls<count($actual_spot_info); $rls++)
					{
						list($defectPointId,$defect_qty)=explode("*",$actual_spot_info[$rls]);
						if( trim($data_array_defect)!="") $data_array_defect.=",";
						$data_array_defect.="(".$dft_id.",".$dtlsId.",5,".$$orderId.",4,".$defectPointId.",'".$defect_qty."','".$$bundleNo."',".$user_id.",'".$pc_date_time."')";
						$dft_id = $dft_id+1;
					}*/
				}

			}


			$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);

			if($db_type==2)
			{
				$query="INSERT ALL".$data_array_mst." SELECT * FROM dual";
				$rID=execute_query($query);
			}
			else
			{
				$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
			}

			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
			if($data_array_defect!="")
			{
				$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
			}

			 //echo "10**$data_array_mst";die;
			
			//$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);

			// echo "10**insert into pro_gmts_prod_dft (".$field_array_defect.") values ".$data_array_defect;die;
			//echo $challanrID ."&&". $rID ."&&". $dtlsrID ."&&". $bundlerID ."&&". $delete ."&&". $delete_dtls ."&&". $delete_bundle;die;
			//release lock table

			if($db_type==0)
			{
				if($challanrID && $rID && $dtlsrID && $delete && $delete_dtls)
				{
					mysql_query("COMMIT");
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if($db_type==1 || $db_type==2 )
			{
				if($challanrID && $rID && $dtlsrID && $delete && $delete_dtls)
				{
					oci_commit($con);
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
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
			// pro_garments_production_mst table data entry here
			$field_array1="production_source*serving_company*location*production_date*production_quantity*production_type*entry_break_down_type*challan_no*remarks*floor_id*sewing_line*total_produced*yet_to_produced*prod_reso_allo*updated_by*update_date";

			$data_array1="".$cbo_source."*".$cbo_emb_company."*".$cbo_location."*".$txt_issue_date."*".$txt_issue_qty."*5*".$sewing_production_variable."*'".$challan_no."'*".$txt_remark."*".$cbo_floor."*".$cbo_line_no."*".$txt_cumul_issue_qty."*".$txt_yet_to_issue."*".$prod_reso_allocation."*".$user_id."*'".$pc_date_time."'";
			// pro_garments_production_dtls table data entry here

			if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' )// check is not gross level
			{
				//$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
				$dtlsrDelete = execute_query("update pro_garments_production_dtls SET is_deleted=1, status_active=0 where mst_id=$txt_mst_id",1);

				$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty";

				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{
					$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id" );
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
						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );

						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
				}

				if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{
					$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
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
						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",4,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",4,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
				}
				//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			}//end cond

			$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_system_id,1);
			$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);//echo $rID;die;
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
			{
				$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			}

			//release lock table
			//check_table_status( $_SESSION['menu_id'],0);

			//echo "10**-".$field_array;die;

			//echo '10*'.$rID .'&&'. $challanrID .'&&'. $dtlsrID .'&&'. $dtlsrDelete;die;
			if($db_type==0)
			{
				if($rID && $challanrID && $dtlsrID && $dtlsrDelete)
				{
					mysql_query("COMMIT");
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $challanrID && $dtlsrID && $dtlsrDelete)
				{
					oci_commit($con);
					echo "1**".$mst_id."**".str_replace("'","",$txt_challan_no);
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;


	}
	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		$mst_id=str_replace("'","",$txt_system_id);


 		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="challan_no_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?>

	<script>

		function js_set_value(id)
		{
			$('#hidden_mst_id').val(id);
			parent.emailwindow.hide();
		}

    </script>

</head>

<body>
<div align="center" style="width:830px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:820px;">
		<legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                <thead>
                	<th width="180">Sewing Company</th>
                    <th width="100">Order No</th>
                    <th width="100">Challan No</th>
                    <th width="100">Cutting No</th>
                    <th width="120">Line No</th>
                    <th width="70">Input Date</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_name; ?>">
                    	<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                	<td align="center" id="emb_company_td">
                    	<?
							echo create_drop_down( "cbo_emb_company", 180, $line_library,"", 1, "--- Select ---", $selected, "" );
						?>
                    </td>
                    <td><input type="text" style="width:90px" class="text_boxes"  name="txt_order_no" id="txt_order_no" /></td>
                    <td><input type="text" style="width:90px" class="text_boxes"  name="txt_search_common" id="txt_search_common" /></td>
                    <td><input type="text" style="width:90px" class="text_boxes"  name="txt_cut_no" id="txt_cut_no" /></td>
                    <td>
                    	<?
							$line_library=return_library_array( "select id,line_name from lib_sewing_line where company_name=$cbo_company_name", "id", "line_name"  );
							echo create_drop_down( "cbo_line_no", 120, $line_library,"", 1, "--- Select ---", $selected, "" );
						?>
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_issue_date" id="txt_issue_date" value="" class="datepicker" style="width:60px;"  />
                    </td>

            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_line_no').value+'_'+document.getElementById('txt_issue_date').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_emb_company').value+'_'+document.getElementById('txt_order_no').value+'_<?php echo $cbo_source; ?>'+'_'+document.getElementById('txt_cut_no').value, 'create_challan_search_list_view', 'search_div', 'bundle_wise_sewing_output_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>

                </tr>
           </table>
           <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	load_drop_down('bundle_wise_sewing_output_controller','<?php echo $cbo_source; ?>_<?php echo $cbo_serving_company; ?>'  , 'load_drop_down_sewing_company', 'emb_company_td');

</script>

</html>
<?

exit();
}

if ($action == "load_drop_down_sewing_company") {
    $explode_data = explode("_", $data);
    $data = $explode_data[0];
    $serving_company =$explode_data[1];// $explode_data[1];

    if ($data == 3)
	{
        if ($db_type == 0)
		{
            echo create_drop_down("cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,23,party_type) order by supplier_name", "id,supplier_name", 1, "--- Select ---", $serving_company,"",1);
        }
		else
		{
            echo create_drop_down("cbo_emb_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", $serving_company,"",1);
        }
    }
	else if ($data == 1)
	{
		echo create_drop_down("cbo_emb_company", 180, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 1, "--- Select ---", $serving_company,"",1);

	}
    else
	{
        echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "", 0);
	}

    exit();
}

if($action=="create_challan_search_list_view")
{
	list($challan,$line_no,$issue_date,$company_id,$sew_company,$order_no, $cbo_source,$cutting_no) = explode("_",$data);
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$search_string="%".trim($data[0])."";
	if($challan!=''){$challan_con=" and b.challan_no ='$challan'";}

	if($db_type==0) {$year_field="YEAR(a.insert_date) as year";$production_hour="b.production_hour as production_hour"; }
	else if($db_type==2) {$year_field="MAX(to_char(a.insert_date,'YYYY')) as year";  $production_hour="TO_CHAR(b.production_hour,'HH24:MI') as production_hour"; }
	else $year_field="";//defined Later


	if($order_no!=''){$order_con=" and c.po_number like('%$order_no%')";}else{$order_con="";}
	if($db_type==0) if($issue_date!='') $issue_date_con = "and a.delivery_date = '".change_date_format($issue_date, "yyyy-mm-dd", "-")."'"; else $issue_date_con ="";
	else if($db_type==2) if($issue_date!='')$issue_date_con = "and a.delivery_date = '".change_date_format($issue_date,'','',1)."'"; else $issue_date_con ="";

	if($sew_company!=0){$sew_company_con=" and b.serving_company=$sew_company";}
	$cutting_no_cond=($cutting_no)? " and d.cut_no like '%".$cutting_no ."%'" : " ";

	$sql = "SELECT a.id, $year_field, a.sys_number_prefix_num, a.sys_number, a.delivery_date,$production_hour, a.production_source, a.serving_company, a.location_id, a.floor_id,a.sewing_line,c.po_number,d.cut_no from pro_gmts_delivery_mst a,pro_garments_production_mst b ,wo_po_break_down c,pro_garments_production_dtls d where b.po_break_down_id=c.id and a.id=b.delivery_mst_id and b.id=d.mst_id and d.status_active=1 and d.is_deleted=0 and b.production_type=5 and b.production_source=$cbo_source and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0 $order_con $challan_con $issue_date_con $sew_company_con $cutting_no_cond group by a.id , a.sys_number_prefix_num, a.sys_number, a.delivery_date,b.production_hour, a.production_source, a.serving_company,a.location_id, a.floor_id,a.sewing_line,c.po_number,d.cut_no order by a.id DESC";

	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];

	 //echo $sql;//die;
	$result = sql_select($sql);
	$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor",'id','floor_name');
	$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$resource_alocate_line=return_library_array( "select id, line_number from prod_resource_mst", "id", "line_number"  );
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" align="left">
        <thead>
            <th width="30">SL</th>
            <th width="40">Challan</th>
            <th width="80">Cut No</th>
            <th width="40">Year</th>
            <th width="60">Input Date</th>
            <th width="50">Input Hour</th>
            <th width="60">Source</th>
            <th width="110">Sewing Company</th>
            <th width="110">Location</th>
            <th width="100">Floor</th>
            <th width="45">Line</th>
            <th>Order No</th>
        </thead>
	</table>
	<div style="width:820px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
        <table align="left" cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
            foreach ($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";

                if($row[csf('production_source')]==1) $serv_comp=$company_arr[$row[csf('serving_company')]];
				else $serv_comp=$supplier_arr[$row[csf('serving_company')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
                    <td width="30"><? echo $i; ?></td>
                    <td width="40"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo $row[csf('cut_no')]; ?></p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="60" align="center"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('production_hour')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="110"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                    <td width="100"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                    <td width="45"><p>
					<?
					if($prod_reso_allocation==1)
					{
						$sewing_line=$resource_alocate_line[$row[csf('sewing_line')]];
						$sewing_line_arr=explode(",",$sewing_line);
						$sewing_line_name="";
						foreach($sewing_line_arr as $line_id)
						{
							$sewing_line_name.=$line_library[$line_id].",";
						}
						$sewing_line_name=chop($sewing_line_name,",");
						echo $sewing_line_name;
					}
					else
					{
						echo $line_library[$row[csf('sewing_line')]];
					}
					 ?></p>
                     </td>
                     <td><p><? echo $row[csf('po_number')];?></p></td>
                </tr>
        	<?
            $i++;
            }
        	?>
        </table>
    </div>
	<?
    exit();
}

if($action=='populate_data_from_challan_popup')
{
	if($db_type==2)
	{
		$data_array=sql_select("SELECT a.id, a.company_id, a.sys_number, a.embel_type, a.embel_name, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic, a.delivery_date,b.sewing_line,TO_CHAR(b.production_hour,'HH24:MI') as production_hour,a.working_company_id,a.working_location_id,b.remarks from pro_gmts_delivery_mst a,pro_garments_production_mst b where a.id=b.delivery_mst_id and b.production_type=5 and a.id='$data' and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	}
	else
	{
		$data_array=sql_select("SELECT a.id, a.company_id, a.sys_number, a.embel_type, a.embel_name, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic, a.delivery_date,b.sewing_line,b.production_hour,a.working_company_id,a.working_location_id,b.remarks from pro_gmts_delivery_mst a,pro_garments_production_mst b where a.id=b.delivery_mst_id and b.production_type=5 and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	}

	foreach ($data_array as $row)
	{
		//echo "load_drop_down( 'requires/bundle_wise_sewing_output_controller', $('#cbo_company_name').val()+'_'+".$row[csf('location_id')]."+'_'+".$row[csf('floor_id')].", 'load_drop_down_line', 'line_td' );\n";

	    echo "load_drop_down( 'requires/bundle_wise_sewing_output_controller',".$row[csf("serving_company")]."+'_'+" . $row[csf('location_id')] . "+'_'+" . $row[csf('floor_id')] . "+'_'+'" . change_date_format($row[csf('delivery_date')]) . "', 'load_drop_down_line', 'line_td' );\n";

		echo "load_drop_down( 'requires/bundle_wise_sewing_output_controller','".$row[csf('working_company_id')]."', 'load_drop_down_working_location', 'working_location_td' );\n";
		echo "load_drop_down( 'requires/bundle_wise_sewing_output_controller', '".$row[csf('location_id')]."', 'load_drop_down_floor', 'floor_td' );\n";


		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("sys_number")]."';\n";
		echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";
		//echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#cbo_source').val('".$row[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/bundle_wise_sewing_output_controller', '".$row[csf('production_source')]."', 'load_drop_down_embro_issue_source', 'emb_company_td' );\n";

		echo "load_drop_down( 'requires/bundle_wise_sewing_output_controller', '".$row[csf('serving_company')]."', 'load_drop_down_location', 'location_td' );\n";

		echo "$('#cbo_emb_company').val('".$row[csf('serving_company')]."');\n";
		echo "$('#cbo_location').val('".$row[csf('location_id')]."');\n";
		echo "$('#cbo_floor').val('".$row[csf('floor_id')]."');\n";
		echo "$('#cbo_line_no').val('".$row[csf('sewing_line')]."');\n";
		echo "$('#txt_reporting_hour').val('".$row[csf('production_hour')]."');\n";
		//echo "$('#cbo_embel_type').val('".$row[csf('embel_type')]."');\n";
		echo "$('#txt_organic').val('".$row[csf('organic')]."');\n";
		echo "$('#txt_system_id').val('".$row[csf('id')]."');\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf('delivery_date')])."');\n";


		echo "$('#cbo_working_company_name').val('".$row[csf('working_company_id')]."');\n";
		echo "$('#cbo_working_location').val('".$row[csf('working_location_id')]."');\n";
		echo "$('#txt_remark').val('".$row[csf('remarks')]."');\n";

		echo "disable_enable_fields('cbo_company_name*cbo_source*cbo_emb_company*cbo_location*cbo_floor*cbo_line_no*txt_issue_date',1);\n";

		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_sewing_bundle_output_entry',1,1);\n";
		exit();
	}
}

if($action=="emblishment_issue_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$order_array=array();
	$order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no, a.style_description,b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
	}


	$sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
	production_source, serving_company, floor_id, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=5 and id='$data[1]' and status_active=1 and is_deleted=0 ";

	$dataArray=sql_select($sql);

?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?

					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <?php echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];

					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Sewing Output Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="125"><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="125"><strong>Embel. Name :</strong></td><td width="175px"><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="125"><strong>Emb. Type:</strong></td><td width="175px">
			<?
				if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
				elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
			 ?>
            </td>
        </tr>
        <tr>
            <td><strong>Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Sew.Company:</strong></td><td>
				<?
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else echo $supplier_library[$dataArray[0][csf('serving_company')]];

                ?>
            </td>
            <td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Floor :</strong></td><td><? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Organic :</strong></td><td><? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date :</strong></td><td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
         <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
        </tr>
        <tr>
        	<td  colspan="6" id="barcode_img_id"></td>
        </tr>
    </table>
         <br>
        <?
			$delivery_mst_id =$dataArray[0][csf('id')];

			if($data[2]==3)
			{

				$sql="SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id, d.color_number_id,
				count(b.id) as 	num_of_bundle
				from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
				where a.delivery_mst_id ='$data[1]'
				and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.production_type=5 and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3)
				and d.is_deleted=0
				group by a.po_break_down_id,a.item_number_id,a.country_id,d.color_number_id
				order by a.po_break_down_id,d.color_number_id ";
			}
			else
			{
				$sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id
				from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]'
				and c.id=a.mst_id  and a.color_size_break_down_id=b.id and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0
				group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ";
			}
			$result=sql_select($sql);
		?>

	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Buyer</th>
            <th width="80" align="center">Job</th>
            <th width="80" align="center">Style Ref</th>
            <th width="100" align="center">Style Des</th>
            <th width="80" align="center">Order No.</th>
            <th width="80" align="center">Gmt. Item</th>
            <th width="80" align="center">Country</th>
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Gmt. Qty</th>
            <? if($data[2]==3)  {  ?>
            <th align="center">No of Bundle</th>
            <? }   ?>
        </thead>
        <tbody>
			<?

            $i=1;
            $tot_qnty=array();
                foreach($result as $val)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                        <td><? echo $i;  ?></td>
                        <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                        <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                        <td align="center"><? echo $country_library[$val[csf('country_id')]]; ?></td>
                        <td align="center"><? echo $color_library[$val[csf('color_number_id')]];?></td>
                        <td align="right"><?  echo $val[csf('production_qnty')]; ?></td>
                        <? if($data[2]==3)
						 {  ?>
                        <td  align="center"> <?  echo $val[csf('num_of_bundle')]; ?></td>
                        <?
						$total_bundle+=$val[csf('num_of_bundle')];
						}
						?>

                    </tr>
                    <?
					$production_quantity+=$val[csf('production_qnty')];
					$i++;
                }
            ?>
        </tbody>
        <tr>
        <? if($data[3]==3) $colspan=8 ; else $colspan=7; ?>
            <td colspan="9" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><?  echo $production_quantity; ?></td>
             <? if($data[2]==3)  {  ?>
            <td  align="center"> <?  echo $total_bundle; ?></td>
            <? }   ?>
        </tr>
    </table>
        <br>
		 <?
            echo signature_table(28, $data[0], "900px");
         ?>
	</div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
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

			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
<?
exit();
}


if($action=="emblishment_issue_print_2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$order_array=array();
	$order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no, a.style_description,b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
	}

	/*$sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
	production_source, serving_company, floor_id, organic, delivery_date from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";*/



	/*$sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
	production_source, serving_company, floor_id,sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=5 and id='$data[1]' and
	status_active=1 and is_deleted=0 ";*/

	if($db_type==0)
	{
$sql="select a.id, a.sys_number_prefix, a.sys_number_prefix_num, a.sys_number, a.company_id, a.production_type, a.location_id, a.delivery_basis, a.embel_name, a.embel_type, a.production_source, a.serving_company, a.floor_id,a.sewing_line, a.organic, a.delivery_date,a.working_company_id,a.working_location_id,b.remarks,b.production_hour from pro_gmts_delivery_mst a,pro_garments_production_mst b where a.id=b.delivery_mst_id and a.production_type=5 and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	}
	else if($db_type==2 || $db_type==1)
	{
		$sql="select a.id, a.sys_number_prefix, a.sys_number_prefix_num, a.sys_number, a.company_id, a.production_type, a.location_id, a.delivery_basis, a.embel_name, a.embel_type, a.production_source, a.serving_company, a.floor_id,a.sewing_line, a.organic, a.delivery_date,a.working_company_id,a.working_location_id,b.remarks,TO_CHAR(production_hour,'HH24:MI') as production_hour from pro_gmts_delivery_mst a,pro_garments_production_mst b where a.id=b.delivery_mst_id and a.production_type=5 and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	}

	$dataArray=sql_select($sql);
	// var_dump($dataArray[0]);

	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='".$dataArray[0][csf('company_id')]."' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	if($prod_reso_allocation==1)
	{
		if( $dataArray[0][csf('floor_id')]==0 && $dataArray[0][csf('location_id')]!=0 ) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
		if( $dataArray[0][csf('floor_id')]!=0 ) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

		if($db_type==0)
		{
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
		}
		else if($db_type==2 || $db_type==1)
		{
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
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
	 $line=$line_array[$dataArray[0][csf('sewing_line')]];
	}
	else
	{
	 $line=$line_library[$dataArray[0][csf('sewing_line')]];
	}

?>
<div style="width:930px;">
    <table cellspacing="0" >
        <tr>
            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
				<?

					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{

						 echo $result[csf('city')];

					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><u><strong>Sewing Output Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
            <td width="95"><strong>Challan No:</strong></td>
            <td width="150"><? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="80"><strong>Source:</strong></td>
            <td width="190"><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td width="120"><strong>Sew. Company:</strong></td>
            <td>
				<?
					if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
					else echo $supplier_library[$dataArray[0][csf('serving_company')]];

                ?>
            </td>
        </tr>
        <tr>
            <td><strong>Location:</strong></td><td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
            <td><strong>Floor :</strong></td><td><? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        	<td><strong>Line :</strong></td><td><? echo $line; ?></td>
        </tr>
        <tr>
         <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
         <td><strong>Output Date :</strong></td><td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
            <td>Remarks: </td>
            <td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
            <td><strong>Hour (24):</strong> </td>
            <td><? echo $dataArray[0][csf('production_hour')]; ?></td>
        </tr>
        <tr>
        	<td  colspan="6" id="barcode_img_id"></td>
        </tr>

    </table>
         <br>
        <?

			$delivery_mst_id =$dataArray[0][csf('id')];

			if($data[2]==3)
			{

				$sql="SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id, b.bundle_no,d.size_number_id,d.color_number_id,
				count(b.id) as 	num_of_bundle
				from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
				where a.delivery_mst_id ='$data[1]'
				and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.production_type=5 and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3)
				and d.is_deleted=0
				group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.size_number_id,d.color_number_id
				order by  length(b.bundle_no) asc, b.bundle_no  asc";//order by a.po_break_down_id,d.color_number_id
			}
			else
			{
				$sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,b.bundle_no,d.size_number_id, b.color_number_id
				from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]'
				and c.id=a.mst_id  and a.color_size_break_down_id=b.id and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0
				group by c.po_break_down_id,c.item_number_id,c.country_id,b.bundle_no,d.size_number_id,b.color_number_id ";
			}


			$result=sql_select($sql);
		?>

	<div style="width:100%;">
    <table cellspacing="0" width="900"  border="1" rules="all" class="rpt_table">
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="60" align="center">Bundle No</th>
            <th width="60" align="center">Job</th>
            <th width="80" align="center">Buyer</th>
            <th width="80" align="center">Style Ref</th>
            <th width="100" align="center">Style Des</th>
            <th width="80" align="center">Order No.</th>
            <th width="80" align="center">Gmt. Item</th>
            <!--<th width="80" align="center">Country</th>-->
            <th width="80" align="center">Color</th>
            <th width="80" align="center">Size</th>
            <th width="60" align="center">Gmt. Qty</th>
        </thead>
        <tbody>
			<?

            $i=1;
            $tot_qnty=array();
                foreach($result as $val)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                        <td><? echo $i;  ?></td>
                        <td align="center"><? echo $val[csf('bundle_no')]; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                        <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                        <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                        <!--<td align="center"><? //echo $country_library[$val[csf('country_id')]]; ?></td>-->
                        <td align="center"><? echo $color_library[$val[csf('color_number_id')]];?></td>
                        <td align="center"><? echo $size_library[$val[csf('size_number_id')]];?></td>
                        <td align="right"><?  echo $val[csf('production_qnty')]; ?></td>

                    </tr>
                    <?
					$production_quantity+=$val[csf('production_qnty')];
					$total_bundle+=$val[csf('num_of_bundle')];
					$size_qty_arr[$val[csf('size_number_id')]]+=$val[csf('production_qnty')];
					$i++;
                }
            ?>
        </tbody>
            <td colspan="3"><strong>No. Of Bundle :<?  echo $total_bundle; ?></strong></td>
            <td colspan="7" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><?  echo $production_quantity; ?></td>
    </table>

     <br>
    <table cellspacing="0" border="1" rules="all">
        <thead>
           <tr><td colspan="3"><strong>Size Wise Summary</strong></td></tr>
           <tr bgcolor="#dddddd" align="center"><td>SL</td><td>Size</td><td>Quantity (Pcs)</td></tr>
        </thead>
        <tbody>
        	<? $i=1;
			foreach($size_qty_arr as $size_id=>$size_qty):
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
            	<tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i;?></td>
                    <td><? echo $size_library[$size_id];?></td>
                    <td align="right"><? echo $size_qty;?></td>
                </tr>
            <? $i++;endforeach;?>
        </tbody>
        <tfoot>
           <tr><td colspan="2">Total</td><td align="right"><? echo $production_quantity; ?></td></tr>
        </tfoot>
    </table>

        <br>
		 <?
           echo signature_table(28, $data[0], "900px");
         ?>
	</div>
	</div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	function generateBarcode( valuess ){
			var value = valuess;//$("#barcodeValue").val();
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

			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
	    generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	 </script>
<?
exit();
}
?>
