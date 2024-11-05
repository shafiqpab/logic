<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
//$order_type_arr = array(1=>"Self Order",2=>"Subcontract Order");
$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();
}


if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("SELECT sewing_production,production_entry,leftover_maintained,leftover_country_maintained,leftover_source,working_company_mandatory from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		// echo "$('#sewing_production_variable').val(".$result[csf("sewing_production")].");\n";
		echo "$('#sewing_production_variable').val(".$result[csf("leftover_maintained")].");\n";
		echo "$('#country_maintain_variable').val(".$result[csf("leftover_country_maintained")].");\n";
		echo "$('#leftover_source').val(".$result[csf("leftover_source")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
		echo "$('#wo_company_and_location_mandatory').val(".$result[csf("working_company_mandatory")].");\n";

	}

	echo "$('#iron_production_variable_rej').val(0);\n";
	$sql_result_rej = sql_select("select sewing_production from variable_settings_production where company_name='$data' and variable_list=28 and status_active=1");
 	foreach($sql_result_rej as $result)
	{

		echo "$('#iron_production_variable_rej').val(".$result[csf("sewing_production")].");\n";
		if($result[csf("sewing_production")]==3)
		{
				echo "$('#txt_reject_qnty').attr('readonly','readonly');\n";
		}
		else
		{
			echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";
		}
	}
	//$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=30","is_control");
	//echo "document.getElementById('variable_is_controll').value=".$variable_is_control.";\n";
 	exit();
}


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 170, "select location_name,id from lib_location where is_deleted=0  and status_active=1 and company_id='$data' order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/left_over_garments_receive_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_store_name', 'store_name_td');" );
}


if ($action=="load_drop_down_store_name")
{
	$dataEx = explode("_", str_replace("'", "", $data));
	$company_id = $dataEx[0];
	$location_id = $dataEx[1];
	$locationCond = "";
	if($location_id !=0)
	{
		$locationCond = " and a.location_id=$location_id";
	}
	echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=30  and a.status_active=1 and a.is_deleted=0 and a.company_id in($company_id) $locationCond group by a.id ,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "0", "","" );
	//and b.category_type in ($item_cate_credential_cond) $store_location_credential_cond
	exit();
}

if ($action=="load_drop_down_floor")
{

	echo create_drop_down( "cbo_floor_name", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_working_location")
{
	// echo create_drop_down( "cbo_working_location_name", 170, "select location_name,id from lib_location where is_deleted=0  and status_active=1 and company_id='$data' order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/left_over_garments_receive_controller', this.value, 'load_drop_down_working_floor', 'working_floor' )" );
	// echo create_drop_down( "cbo_working_location_name", 170, "select location_name,id from lib_location where is_deleted=0  and status_active=1 and company_id='$data' order by location_name",'id,location_name', 1, '--- Select Location ---', 0, '' );
	echo create_drop_down( "cbo_working_location_name", 170, "select location_name,id from lib_location where is_deleted=0  and status_active=1 and company_id='$data' order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/left_over_garments_receive_controller', this.value+'_'+document.getElementById('cbo_working_company_name').value, 'load_drop_down_working_floor', 'working_floor' )" );

}

if ($action=="load_drop_down_working_floor")
 {

 	// echo create_drop_down( "cbo_working_floor_name", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
 	// exit();
	 $data=explode('_',$data);
	 $loca=$data[0];
	 $com=$data[1];
	 echo create_drop_down( "cbo_working_floor_name", 170, "select id,floor_name from lib_prod_floor where production_process in (5,11) and status_active =1 and is_deleted=0 and company_id='$com' and location_id='$loca' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "","","","","","",4 );
	 exit();
 }


if ($action=="load_drop_down_location2")
{
	echo create_drop_down( "cbo_location_name", 120, "select location_name,id from lib_location where is_deleted=0  and status_active=1 and company_id='$data' order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'left_over_garments_receive_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_store_name2', 'cbo_store_name' )" );
	exit();
}

if ($action=="load_drop_down_store_name2")
{
	$dataEx = explode("_", str_replace("'", "", $data));
	$company_id = $dataEx[0];
	$location_id = $dataEx[1];
	$locationCond = "";
	if($location_id !=0)
	{
		$locationCond = " and a.location_id=$location_id";
	}
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=30  and a.status_active=1 and a.is_deleted=0 and a.company_id in($company_id) $locationCond group by a.id ,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "0", "","" );
	//and b.category_type in ($item_cate_credential_cond) $store_location_credential_cond
	exit();

	// echo create_drop_down( "cbo_store_name", 120, "select id,store_name from lib_store_location  where company_id=$company_id $locationCond and status_active =1 and is_deleted=0 order by store_name", "id,store_name", 1, "-- Select Store --", $selected, 0, "" );
}

if ($action=="load_drop_down_floor2")
{

	echo create_drop_down( "cbo_floor_name", 80, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );
}

if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	//echo $company."_".$order_type."_".$goods_type."_".$garments_nature."_".$location_name."_".$buyer_name."_".$store_name;
	if ($garments_nature=="")
	{
		$garments_nature = 0;
	}
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
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:180px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:180px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:180px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==4)
			{
				document.getElementById('search_by_th_up').innerHTML="Actual PO No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:180px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==5)
			{
				document.getElementById('search_by_th_up').innerHTML="File No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:180px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==6)
			{
				document.getElementById('search_by_th_up').innerHTML="Internal Ref.";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:180px " class="text_boxes" id="txt_search_common"	value=""  />';
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
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:180px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}
		}


		function js_set_value(id,po_number,country_id,gmts_item,company_id,location_name,currency_id)
		{
			// alert(id+'-'+po_number+'-'+gmts_item+'-'+company_id+'-'+location_name+'-'+currency_id);
			$("#hidden_id").val(id);//po id
			$("#hidden_po_number").val(po_number);
			$("#hidden_country_id").val(country_id);
			$("#hidden_gmts_item").val(gmts_item);
			$("#hidden_company_id").val(company_id);
			$("#hidden_location_name").val(location_name);
			$("#hidden_currency_id").val(currency_id);
			// $("#hidden_job_no").val(job_no);
			//$("#hidden_byer_name").val(buyer_id);
			//$("#hidden_country_name").val(country_name);

			//$("#hidden_style_ref_no").val(style_ref_no);

			//$("#hidden_order_rate").val(fob_rate);

			//$("#hidden_currency").val(currency);


			//$("#hidden_currency_rate").val(currency_id);


			//$("#hidden_po_qnty").val(po_qnty);
			//$("#hidden_plancut_qnty").val(plan_cut_qnty);
			//$("#hidden_shipment_date").val(shipment_date);
			//return;

			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">

	        <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="750">
	             <thead>
	                <th width="130">Search By</th>
	                <th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
					<th width="130">Shipment Status</th>
	                <th width="200">Date Range</th>
	                <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
	            </thead>
	            <tbody>
	                <tr>
	                    <td width="130">
	                        <?
	                            $searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref.");
	                            echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select --", $selected, "search_populate(this.value)",0 );


	                        ?>
	                    </td>

	                    <td width="180" align="center" id="search_by_td">
	                        <input type="text" style="width:180px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
	                    </td>
	                    <td width="130">
	                        <?
	                            echo create_drop_down( "cbo_shipment_status", 130, $shipment_status,"", 1, "-- Select --", 3, "",0 );


	                        ?>
	                    </td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
	                    </td>
	                    <td align="center">

	                        <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $order_type; ?>+'_'+<? echo $goods_type; ?>+'_'+<? echo $garments_nature; ?>+'_'+<? echo $location_name; ?>+'_'+<? echo $buyer_name; ?>+'_'+<? echo $store_name; ?>+'_'+<? echo $working_company_name; ?>+'_'+<? echo $working_location_name; ?>+'_'+<? echo $country_maintain_variable; ?>+'_'+<? echo $leftover_source; ?>+'_'+document.getElementById('cbo_shipment_status').value+'_'+<? echo $wo_company_and_location_mandatory; ?>, 'create_po_search_list_view', 'search_div', 'left_over_garments_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
	                    </td>
	                </tr>
	            </tbody>
	            <tfoot>
	                <tr bgcolor="#CCCCCC">
	                    <td  align="center" height="" valign="middle" colspan="5">
	                        <? echo load_month_buttons(1);  ?>
	                        <input type="hidden" id="hidden_id"> <!-- po id -->
	                        <input type="hidden" id="hidden_po_number"/>
	                        <input type="hidden" id="hidden_country_id"/>
	                        <input type="hidden" id="hidden_gmts_item"/>
	                        <input type="hidden" id="hidden_company_id"/>
	                        <input type="hidden" id="hidden_location_name"/>
	                        <input type="hidden" id="hidden_currency_id"/>
	                        <input type="hidden" id="hidden_job_no"/>

	                        <!--<input type="hidden" id="hidden_job_no">
	                        <input type="hidden" id="hidden_byer_name">
	                        <input type="hidden" id="hidden_country_name">
	                        <input type="hidden" id="hidden_style_ref_no">
	                        <input type="hidden" id="hidden_order_rate">
	                        <input type="hidden" id="hidden_currency">
	                        <input type="hidden" id="hidden_currency_rate">
	                        <input type="hidden" id="hidden_po_qnty">
	                        <input type="hidden" id="hidden_plancut_qnty">
	                        <input type="hidden" id="hidden_shipment_date">-->
	                    </td>
	                </tr>
	            </tfoot>
	        </table>
	        <fieldset style="width:750px;">
	        <div style="margin-top:10px" id="search_div"></div>
	        </fieldset>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_po_search_list_view")
{
	//echo $data;
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$order_type = $ex_data[5];
 	$goods_type = $ex_data[6];
 	$garments_nature = $ex_data[7];
 	$location = $ex_data[8];
	$buyer = $ex_data[9];
	$store = $ex_data[10];
	$working_company = $ex_data[11];
	$working_location = $ex_data[12];
	$country_maintain = $ex_data[13];
	$leftover_source = $ex_data[14];
	$shipment_status = $ex_data[15];
	$is_wo_com_loc_mandatory = str_replace("'","",$ex_data[16]);

	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
	$garments_item=return_library_array("SELECT id,item_name from  lib_garment_item", 'id', 'item_name');
	$location_name=return_library_array("SELECT id,location_name from lib_location where company_id =$company and status_active=1 and is_deleted=0", 'id', 'location_name');
	$country_arr=return_library_array( "SELECT id, country_name from lib_country",'id','country_name');

	/*// ===================================== GETTING FULL SHIPMENT PO ====================================
	$sql_po = "SELECT id from wo_po_break_down where status_active=3";
	$sql_po_res = sql_select($sql_po);
	$po_arr = array();
	foreach ($sql_po_res as $key => $val)
	{
		$po_arr[$val[csf('id')]] = $val[csf('id')];
	}
	$poIds = implode(",", $po_arr);*/


	// ================================MAKING QUERY CONDITIONS===================================
	$sql_cond="";
	if($order_type==1) // Self Order Type
	{
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
			{
				$sql_cond = " AND b.po_number like '%".trim($txt_search_common)."%'";
			}
			else if(trim($txt_search_by)==1)
			{
				$sql_cond = " AND a.style_ref_no like '%".trim($txt_search_common)."%'";
			}
			else if(trim($txt_search_by)==2)
			{
				$sql_cond = " AND a.buyer_name='$txt_search_common'";
			}
			else if(trim($txt_search_by)==3)
			{
				$sql_cond = " AND a.job_no like '%".trim($txt_search_common)."'";
			}
			else if(trim($txt_search_by)==4)
			{
				$sql_cond = " AND f.acc_po_no like '%".trim($txt_search_common)."%'";
			}
			else if(trim($txt_search_by)==5)
			{
				$sql_cond = " AND b.file_no like '%".trim($txt_search_common)."%'";
			}
			else if(trim($txt_search_by)==6)
			{
				$sql_cond = " AND b.grouping like '%".trim($txt_search_common)."%'";
			}
		}


		if($db_type==0)// FOR MYSQL
		{
			if($txt_date_from!="" && $txt_date_to!="")
			{
				$sql_cond .= " AND b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' AND '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			else if($txt_date_from=="" && $txt_date_to!=""){
				$sql_cond .= " AND b.shipment_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			else if($txt_date_from!="" && $txt_date_to==""){
				$sql_cond .= " AND b.shipment_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
			}
		}
		if($db_type==2 || $db_type==1) // FOR ORACLE
		{
			if($txt_date_from!="" && $txt_date_to!="")
			{
				$sql_cond .= " AND b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' AND '".date("j-M-Y",strtotime($txt_date_to))."'";
			}
			else if($txt_date_from=="" && $txt_date_to!=""){
				$sql_cond .= " AND b.shipment_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
			}
			else if($txt_date_from!="" && $txt_date_to==""){
				$sql_cond .= " AND b.shipment_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
			}

		}



		if(trim($company) != '0')
		{
			$sql_cond .= " AND a.company_name='$company'";
		}
		if(trim($location) != '0')
		{
			$sql_cond .= " AND a.location_name='$location'";
		}
		if(trim($buyer) != '0')
		{
			$sql_cond .= " AND a.buyer_name=$buyer";
		}
		if(trim($shipment_status) != '0')
		{
			$sql_cond .= " AND b.shiping_status=$shipment_status";
		}
	}
	else // Sub contruct order
	{
		if(trim($txt_search_common)!="")
		{
			$sql_cond .= " and a.order_no like '%".trim($txt_search_common)."'";
		}

		if(trim($company)!='0')
		{
			$sql_cond .= " and b.company_id='$company'";
		}

		if(trim($location)!='0')
		{
			$sql_cond .= " and b.location_id='$location'";
		}
	}
	if($leftover_source==1)
	{
		if($goods_type==2) $prod_type_id="5,80,8";  else $prod_type_id="5";
	}else
	{
		$prod_type_id = 11;
	}
	// if($leftover_source==1) $prod_type_id="5,80,8"; else $prod_type_id="11";
	$prod_qty_source = ($goods_type==2) ? "d.reject_qnty" : "e.production_qnty";
	if($order_type==1) // Self Order Type
	{
		// =============================== GETTING ACTIVE PO WITH SHIPMENT CLOSE ======================================
		if($goods_type==2) {
			$sql_po_actv = "SELECT d.po_break_down_id as po_id,sum($prod_qty_source) as sewing_out_qty from wo_po_details_master a, wo_po_break_down b left join wo_po_acc_po_info f on b.id=f.po_break_down_id AND f.is_deleted=0, wo_po_color_size_breakdown c, pro_garments_production_mst d, pro_garments_production_dtls e where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and d.id=e.mst_id and b.id=d.po_break_down_id  and e.production_type in ($prod_type_id) and d.production_type=e.production_type and a.status_active=1 and b.status_active=1 and d.status_active=1 and e.status_active=1 and c.status_active=1  and c.id=e.color_size_break_down_id $sql_cond group by d.po_break_down_id";
		} else {
			$sql_po_actv = "SELECT d.po_break_down_id as po_id, sum($prod_qty_source) as sewing_out_qty from wo_po_details_master a, wo_po_break_down b left join wo_po_acc_po_info f on b.id=f.po_break_down_id AND f.is_deleted=0, wo_po_color_size_breakdown c, pro_garments_production_mst d, pro_garments_production_dtls e where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and d.id=e.mst_id and b.id=d.po_break_down_id  and e.production_type in ($prod_type_id) and d.production_type=e.production_type and a.status_active=1 and b.status_active=1 and d.status_active=1 and e.status_active=1 and c.status_active=1 and c.id=e.color_size_break_down_id $sql_cond group by d.po_break_down_id";
		}

		// echo $sql_po_actv;
		$sql_po_actv_res = sql_select($sql_po_actv);

		$po_arr = array();
		foreach ($sql_po_actv_res as $val)
		{
			$po_arr[$val[csf('po_id')]] = $val[csf('po_id')];

		}
		// print_r($sewing_out_act_po_arr);die();

		$po_id_list_arr=array_chunk($po_arr,999);
		$poCond = " and ";
		$p=1;
		foreach($po_id_list_arr as $poids)
	    {
	    	if($p==1)
			{
				$poCond .="  ( a.po_break_down_id in(".implode(',',$poids).")";
			}
	        else
	        {
	          $poCond .=" or a.po_break_down_id in(".implode(',',$poids).")";
	      	}
	        $p++;
	    }
	    $poCond .=")";

		// ============================= GETTING EX-FACTORY QTY ============================
		$sql_ex_fact = "SELECT a.po_break_down_id as po_id, sum(case when a.entry_form != 85 then a.ex_factory_qnty else 0 end) - sum(case when a.entry_form = 85 then a.ex_factory_qnty else 0 end) as ex_fact_qty from pro_ex_factory_mst a where a.status_active=1 $poCond group by a.po_break_down_id";
		// echo $sql_ex_fact;
		$sql_ex_fact_res = sql_select($sql_ex_fact);
		$ex_fact_qty_arr = array();
		foreach ($sql_ex_fact_res as $val)
		{
			$ex_fact_qty_arr[$val[csf('po_id')]] = $val[csf('ex_fact_qty')];
		}

		// ============================= GETTING DELIVERY QTY ============================
		$delv_po_cond = str_replace("a.po_break_down_id", "b.from_po_id", $poCond);
		$sql_delivery = "SELECT b.from_po_id as po_id,sum(b.production_quantity) as delivery_qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b where a.id=b.mst_id $delv_po_cond and b.production_type=10 and a.status_active=1 and b.status_active=1 group by b.from_po_id";
		// echo $sql_delivery;die();
		$sql_delivery_res = sql_select($sql_delivery);
		$delivery_qty_arr = array();
		foreach ($sql_delivery_res as $val)
		{
			$delivery_qty_arr[$val[csf('po_id')]] = $val[csf('delivery_qty')];
		}

		// GETTING SEW OUT PO ID WHICH PO HAS SEW BALANCE
		$sewing_out_act_po_arr = array();
		foreach ($sql_po_actv_res as $val)
		{
			$exFactory_qty = $ex_fact_qty_arr[$val[csf('po_id')]] + $delivery_qty_arr[$val[csf('po_id')]];
			$sew_balance = ($goods_type==2) ?  $val[csf('sewing_out_qty')] : $val[csf('sewing_out_qty')] - $exFactory_qty;
			// echo $val[csf('sewing_out_qty')] ."-". $ex_fact_qty_arr[$val[csf('po_id')]];
			if($sew_balance > 0)
			{
				$sewing_out_act_po_arr[$val[csf('po_id')]] = $val[csf('po_id')];
			}
		}


		// =============================== GETTING IN-ACTIVE PO ======================================
		$sql_sewing_out = "SELECT d.po_break_down_id as po_id,sum($prod_qty_source) as sewing_out_qty from wo_po_details_master a, wo_po_break_down b left join wo_po_acc_po_info f on b.id=f.po_break_down_id AND f.is_deleted=0, wo_po_color_size_breakdown c, pro_garments_production_mst d, pro_garments_production_dtls e where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and d.id=e.mst_id and b.id=d.po_break_down_id  and e.production_type in ($prod_type_id) and d.production_type=e.production_type and a.status_active=1 and b.status_active=3 and d.status_active=1 and e.status_active=1 and c.status_active in(1,3)  $sql_cond group by d.po_break_down_id";//b.shiping_status=1
		// echo $sql_sewing_out;
		$sql_sewing_out_res = sql_select($sql_sewing_out);

		$sewing_out_inact_po_arr = array();
		foreach ($sql_sewing_out_res as $val)
		{
			if($val[csf('sewing_out_qty')] > 0)
			{
				$sewing_out_inact_po_arr[$val[csf('po_id')]] = $val[csf('po_id')];
			}
		}
		// print_r($sewing_out_inact_po_arr);
		$poIdArray = array();
		$poIdArray = array_filter(array_merge($sewing_out_act_po_arr,$sewing_out_inact_po_arr));
		// print_r($poIdArray);
		if(count($poIdArray)==0 && $order_type==1)
		{
			echo '<div style="text-aligncenter;font-size:18px;color:red;"padding:10px;>Data not available.</div';
			die();
		}


		// print_r($poIdArray);
		$poIds = implode(",", $poIdArray);
		$po_cond="";
		if(count($poIdArray)>999)
		{
			$chunk_arr=array_chunk($poIdArray,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( b.id in ($ids) ";
				else
					$po_cond.=" or b.id in ($ids) ";
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and b.id in ($poIds) ";
		}
		// echo $po_cond;die;
		// if(trim($buyer)!='0')
		// {
		// 	$sql_cond .= " AND a.buyer_name='$buyer'";
		// }

		if( $goods_type ==1 || $goods_type == 2 )	//Goods Type [Good GMT In Hand , Damage GMT]
		{
			$country_select = ($country_maintain==1) ? ",c.country_id AS country_id" : ",LISTAGG(c.country_id, ',') WITHIN GROUP (ORDER BY c.country_id) AS country_id";
			$group_by = ($country_maintain==1) ? ",c.country_id" : " ";
			$sql= "SELECT a.job_no, a.style_ref_no, a.buyer_name, a.gmts_item_id, a.location_name, a.currency_id, a.company_name, b.po_number, b.id, b.pub_shipment_date, b.shipment_date, b.unit_price as order_rate,b.grouping,c.item_number_id $country_select
            FROM wo_po_details_master a, wo_po_break_down b left join wo_po_acc_po_info f on b.id=f.po_break_down_id AND f.is_deleted=0, wo_po_color_size_breakdown c
            WHERE a.id=b.job_id AND b.id=c.po_break_down_id AND a.status_active=1 AND a.is_deleted=0 AND b.status_active in(1,3) AND b.is_deleted=0 AND c.status_active in(1,3) AND c.is_deleted=0  $sql_cond $po_cond
            GROUP BY a.job_no, a.style_ref_no, a.buyer_name, a.gmts_item_id, a.location_name, a.currency_id, a.company_name, b.po_number, b.id, b.pub_shipment_date, b.shipment_date,b.unit_price,b.grouping,c.item_number_id $group_by
            ORDER BY a.job_no DESC"; // AND a.garments_nature=$garments_nature
		}

		// echo $sql;
		//and b.shiping_status=3
		//and a.ship_mode=3
		//  echo $sql;die();
		$result = sql_select($sql);
		$left_over_po_arr = array();
		$left_over_item_arr = array();
		foreach ($result as $val)
		{
			$left_over_po_arr[$val[csf('id')]] = $val[csf('id')];
			$left_over_item_arr[$val[csf('item_number_id')]] = $val[csf('item_number_id')];
		}
		$left_over_po_id = implode(",", $left_over_po_arr);
		$left_over_item_id = implode(",", $left_over_item_arr);

		// ============================= GETTING SEWING OUTPUT QTY ============================
		$prod_qty_source = ($goods_type==2) ? "a.reject_qnty" : "b.production_qnty";
		$wo_com_loc_cond = ($is_wo_com_loc_mandatory==1) ? " and a.location=$working_location and a.serving_company=$working_company" : "";

		if($goods_type==2)
		{
			$sql_sewing_out = "SELECT a.po_break_down_id as po_id, a.item_number_id as item_id, a.country_id,b.production_type, $prod_qty_source as sewing_out_qty
			from pro_garments_production_mst a, pro_garments_production_dtls b
			where a.id=b.mst_id and a.po_break_down_id in($left_over_po_id) and a.item_number_id in($left_over_item_id) and a.company_id=$company  and b.production_type in ($prod_type_id) and a.production_type=b.production_type and a.status_active=1 and b.status_active=1 $wo_com_loc_cond
			group by a.po_break_down_id,a.item_number_id,a.country_id,b.production_type, $prod_qty_source";
		}
		else
		{
			$sql_sewing_out = "SELECT a.po_break_down_id as po_id, a.item_number_id as item_id, a.country_id, sum($prod_qty_source) as sewing_out_qty
			from pro_garments_production_mst a, pro_garments_production_dtls b
			where a.id=b.mst_id and a.po_break_down_id in($left_over_po_id) and a.item_number_id in($left_over_item_id) and a.company_id=$company and b.production_type in ($prod_type_id) and a.production_type=b.production_type and a.status_active=1 and b.status_active=1 $wo_com_loc_cond
			group by a.po_break_down_id,a.item_number_id,a.country_id, $prod_qty_source";
		}
		// echo $sql_sewing_out;die;
		$sql_sewing_out_res = sql_select($sql_sewing_out);
		$sewing_out_qty_arr = array();
		foreach ($sql_sewing_out_res as $val)
		{
			if($country_maintain==1)//yes
			{
				$sewing_out_qty_arr[$val[csf('po_id')]][$val[csf('item_id')]][$val[csf('country_id')]] += $val[csf('sewing_out_qty')];
			}
			else
			{
				$sewing_out_qty_arr[$val[csf('po_id')]][$val[csf('item_id')]] += $val[csf('sewing_out_qty')];
			}
		}
		// echo $sewing_out_qty;

		// ============================= GETTING RCV LEFTOVER QTY ============================
		$wo_com_loc_cond = ($is_wo_com_loc_mandatory==1) ? " and a.working_company_id=$working_company and a.working_location_id=$working_location" : "";

		$sql_leftover = "SELECT b.po_break_down_id as po_id,b.item_number_id as item_id,b.country_id,sum(b.total_left_over_receive) as leftover_qty from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and b.po_break_down_id in($left_over_po_id) and b.item_number_id in($left_over_item_id) and a.company_id=$company and a.location=$location and a.status_active=1 and b.status_active=1 and a.goods_type=$goods_type $wo_com_loc_cond group by b.po_break_down_id,b.item_number_id,b.country_id";
		// echo $sql_leftover;
		$sql_leftover_res = sql_select($sql_leftover);
		$leftover_qty_array = array();
		foreach ($sql_leftover_res as $val)
		{
			if($country_maintain==1)//yes
			{
				$leftover_qty_array[$val[csf('po_id')]][$val[csf('item_id')]][$val[csf('country_id')]] = $val[csf('leftover_qty')];
			}
			else
			{
				$leftover_qty_array[$val[csf('po_id')]][$val[csf('item_id')]] += $val[csf('leftover_qty')];
			}
		}
		// echo $delivery_qty;

		// ============================= GETTING DELIVERY QTY ============================
		$sql_delivery = "SELECT b.from_po_id as po_id,b.item_number_id as item_id,b.country_id, sum(b.production_quantity) as delivery_qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b where a.id=b.mst_id and b.from_po_id in($left_over_po_id) and b.item_number_id in($left_over_item_id) and b.production_type=10 and a.status_active=1 and b.status_active=1 group by b.from_po_id,b.item_number_id,b.country_id";
		// echo $sql_delivery;
		$sql_delivery_res = sql_select($sql_delivery);
		$delivery_qty_arr = array();
		foreach ($sql_delivery_res as $val)
		{
			if($country_maintain==1)//yes
			{
				$delivery_qty_arr[$val[csf('po_id')]][$val[csf('item_id')]][$val[csf('country_id')]] = $val[csf('delivery_qty')];
			}
			else
			{
				$delivery_qty_arr[$val[csf('po_id')]][$val[csf('item_id')]] += $val[csf('delivery_qty')];
			}
		}
		// echo $sql_delivery;

		// ============================= GETTING EX-FACTORY QTY ============================
		$wo_com_loc_cond = ($is_wo_com_loc_mandatory==1) ? " and c.delivery_company_id=$working_company and c.delivery_location_id=$working_location" : "";
		$sql_ex_fact = "SELECT a.po_break_down_id as po_id,a.item_number_id as item_id,a.country_id, sum(case when a.entry_form != 85 then a.ex_factory_qnty else 0 end) - sum(case when a.entry_form = 85 then a.ex_factory_qnty else 0 end) as ex_fact_qty  from pro_ex_factory_mst a,pro_ex_factory_delivery_mst c where c.id=a.delivery_mst_id and a.po_break_down_id in($left_over_po_id) and a.item_number_id in($left_over_item_id) and a.status_active=1  and c.status_active=1 $wo_com_loc_cond group by a.po_break_down_id,a.item_number_id,a.country_id";//and c.delivery_company_id=$working_company and c.delivery_location_id=$working_location
		// echo $sql_ex_fact;die();
		$sql_ex_fact_res = sql_select($sql_ex_fact);
		$ex_fact_qty_array = array();
		foreach ($sql_ex_fact_res as $val)
		{
			if($country_maintain==1)//yes
			{
				$ex_fact_qty_array[$val[csf('po_id')]][$val[csf('item_id')]][$val[csf('country_id')]] = $val[csf('ex_fact_qty')];
			}
			else
			{
				$ex_fact_qty_array[$val[csf('po_id')]][$val[csf('item_id')]] += $val[csf('ex_fact_qty')];
			}
		}

		// echo $sewing_out_qty - ($leftover_qty + $delivery_qty + $ex_fact_qty);
		$tbl_width = ($country_maintain==1) ? "1000" : "900";
		?>
        <style type="text/css">
        	table th, table tr td{word-wrap: break-word;word-break: break-all;}
        </style>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width;?>" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="70">Date</th>
                    <th width="100">Job Number</th>
					<th width="100">IR/IB</th>
                    <th width="100">PO Number</th>
                    <th width="100">Style Ref</th>
                    <? if($country_maintain==1){?>
                    <th width="100">Country</th>
                    <?}?>
                    <th width="100">Garments Item</th>
                    <th width="100">Location</th>
                    <th width="100">FOB Rate</th>
                    <th width="100">Buyer</th>
                    <th width="120">Balance</th>
                </thead>
            </table>

         <div style="width:<? echo $tbl_width+20;?>px;max-height:240px;overflow-y:scroll;" >
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width;?>" class="rpt_table" id="tbl_po_list">
            	<tbody>
		            <?
					$i=1;
					foreach( $result as $row )
					{
						if ($i%2==0)  $bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF";
						if($country_maintain==1)
						{
							$sewing_out_qty = $sewing_out_qty_arr[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('country_id')]];
							$leftover_qty 	= $leftover_qty_array[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('country_id')]];
							$delivery_qty 	= $delivery_qty_arr[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('country_id')]];
							$ex_fact_qty 	= $ex_fact_qty_array[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('country_id')]];

							$cumLft_qty 	= $leftover_qty_array[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
						}
						else
						{
							$sewing_out_qty = $sewing_out_qty_arr[$row[csf('id')]][$row[csf('item_number_id')]];
							$leftover_qty 	= $leftover_qty_array[$row[csf('id')]][$row[csf('item_number_id')]];
							$delivery_qty 	= $delivery_qty_arr[$row[csf('id')]][$row[csf('item_number_id')]];
							$ex_fact_qty 	= $ex_fact_qty_array[$row[csf('id')]][$row[csf('item_number_id')]];
						}

						$lftOvr_qty 	= ($goods_type==2) ? $sewing_out_qty : $sewing_out_qty - ($ex_fact_qty+$delivery_qty);//$cumLft_qty+
						// echo $sewing_out_qty."==".$ex_fact_qty."==".$delivery_qty."==".$leftover_qty."<br>";

						$balance 		= ($goods_type == 2) ? ($sewing_out_qty - $leftover_qty) : $sewing_out_qty - ($leftover_qty + $delivery_qty + $ex_fact_qty);

						$country_name = "";
						$countryId = "";
						if($country_maintain==1)
		            	{
		            	 	$country_name = $country_library[$row[csf('country_id')]];
		            	 	$countryId = $row[csf('country_id')];
		            	}
		            	else
		            	{
		            		$countryId = 0;

		            	}
		            	/*if($balance>0)
						{*/
			            	?>

			                <tr bgcolor="<?php echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>',
			                    '<? echo $row[csf('po_number')]; ?>',
			                    '<? echo $countryId; ?>',
			                    '<? echo $row[csf('item_number_id')]; ?>',
			                    '<? echo $row[csf('company_name')]; ?>',
			                    '<? echo $row[csf('location_name')]; ?>',
			                    '<? echo $row[csf('currency_id')]; ?>')">

			                    <td width="30"><? echo $i; ?></td>
			                    <td width="70"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
			                    <td width="100"><? echo $row[csf('job_no')]; ?></td>
								<td width="100"><? echo $row[csf('grouping')]; ?></td>
			                    <td width="100"><? echo $row[csf('po_number')]; ?></td>
			                    <td width="100"><? echo $row[csf('style_ref_no')]; ?></td>
			                    <? if($country_maintain==1){?>
			                    <td width="100"><? echo $country_name;?></td>
			                    <?}?>
			                    <td width="100"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
			                    <td width="100"><? echo $location_name[$row[csf('location_name')]]; ?></td>
			                    <td width="100"><? echo $row[csf('order_rate')]; ?></td>
			                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
			                    <td width="100"><? echo $balance; ?></td>
			                </tr>
			                <?
			                $i++;
			            /*}*/
		            }
		            ?>
            	</tbody>
            </table>
        </div>
		<?
		exit();
	}
	else // Sub contruct order
	{

		if(trim($buyer)!=0)
		{
			$sql_cond .= " and a.cust_buyer='$buyer'";
		}
		//echo $sql_cond;die;

		if( $goods_type ==1 || $goods_type == 2 )	//Goods Type [Good GMT In Hand , Damage GMT]
		{
			$sql = "SELECT a.id, a.order_no, a.cust_style_ref, a.cust_buyer, a.job_no_mst, a.order_quantity, a.order_uom, a.rate, a.amount, a.order_rcv_date, a.delivery_date, a.main_process_id, a.process_id, a.smv, a.grey_req, a.order_id, a.wastage,
			b.company_id, b.subcon_job, b.location_id, b.party_id, b.currency_id, b.within_group, b.embellishment_job, b.receive_date, b.party_location,
			c.item_id as item_id, sum(c.qnty) as qnty, sum(c.plan_cut) as plan_cut
			from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c
			where b.id=c.mst_id and a.job_no_mst=b.subcon_job and a.id=c.order_id $sql_cond  and a.status_active=1 and a.is_deleted=0
			group by
			a.id, a.order_no, a.cust_style_ref, a.cust_buyer, a.job_no_mst, a.order_quantity, a.order_uom, a.rate, a.amount, a.order_rcv_date, a.delivery_date, a.main_process_id, a.process_id, a.smv, a.grey_req, a.order_id, a.wastage, b.company_id, b.subcon_job, b.location_id, b.party_id, b.currency_id, b.within_group, b.embellishment_job, b.receive_date, b.party_location, c.item_id
			order by b.subcon_job desc";
		}

		// echo $sql;
		$result = sql_select($sql);

		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		?>
		<div style="width:900px;">
	     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
	            <thead>
	                <th width="30">SL</th>
	                <th width="100">Date</th>
	                <th width="100">Job Number</th>
	                <th width="100">PO Number</th>
	                <th width="100">Style Ref</th>
	                <th width="100">Buyer</th>
	            </thead>
	     	</table>
	     </div>
	     <div style="width:900px; max-height:240px;overflow-y:scroll;" >
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_po_list">
				<?
				$i=1;
	            foreach( $result as $row )
	            {
					?>
	                <tr  style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>',
	                    '<? echo $row[csf('order_no')] ?>',
	                    '<? echo $row[csf('country_id')] ?>',
	                    '<? echo $row[csf('item_id')] ?>',
	                    '<? echo $row[csf('company_id')] ?>',
	                    '<? echo $row[csf('location_id')] ?>',
	                    '<? echo $row[csf('currency_id')] ?>')">

	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
	                    <td width="100"><? echo $row[csf('subcon_job')]; ?></td>
	                    <td width="100"><? echo $row[csf('order_no')]; ?></td>
	                    <td width="100"><? echo $row[csf('cust_style_ref')]; ?></td>
	                    <td width="100"><? echo $buyer_arr[$row[csf('cust_buyer')]]; ?></td>
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
}

if($action=="get_left_over_balance")
{
	extract($_REQUEST);
	if($leftover_source==1)
	{
		if($goods_type==2) $prod_type_id="5,80,8";  else $prod_type_id="5";
	}else
	{
		$prod_type_id = 11;
	}
	// if($leftover_source==1) $prod_type_id="5,80,8"; else $prod_type_id="11";
	$prod_qty_source = ($goods_type==2) ? "b.reject_qty" : "b.production_qnty";

	if($order_type==1)// self order
	{
		$country_cond = ($country_maintain==1) ? " and a.country_id=$country_id" : "";
		$wo_com_loc_cond = ($wo_com_loc_mandatory==1) ? " and a.location=$wo_location and a.serving_company=$wo_company" : "";

		// ============================= GETTING SEWING OUTPUT QTY ============================
		if($goods_type==2) {
			$sql_sewing_out = "SELECT $prod_qty_source as sewing_out_qty
			from pro_garments_production_mst a, pro_garments_production_dtls b
			where a.id=b.mst_id and a.po_break_down_id=$po_id $country_cond and a.item_number_id=$gmts_item and b.production_type in ($prod_type_id) and a.production_type=b.production_type and a.status_active=1 and b.status_active=1 and a.company_id=$company $wo_com_loc_cond";
		} else {
			$sql_sewing_out = "SELECT sum($prod_qty_source) as sewing_out_qty
				from pro_garments_production_mst a, pro_garments_production_dtls b
				where a.id=b.mst_id and a.po_break_down_id=$po_id $country_cond and a.item_number_id=$gmts_item and b.production_type in ($prod_type_id) and a.production_type=b.production_type and a.status_active=1 and b.status_active=1 and a.company_id=$company $wo_com_loc_cond";
		}
		// echo $sql_sewing_out;
		$sql_sewing_out_res = sql_select($sql_sewing_out);
		$sewing_out_qty = 0;
		foreach ($sql_sewing_out_res as $val)
		{
			$sewing_out_qty += $val[csf('sewing_out_qty')];
		}
		// echo $sewing_out_qty;

		// ============================= GETTING LEFTOVER QTY ============================
		$country_cond = ($country_maintain==1) ? " and b.country_id=$country_id" : "";
		$wo_com_loc_cond = ($wo_com_loc_mandatory==1) ? " and a.working_company_id=$wo_company and a.working_location_id=$wo_location" : "";
		$sql_leftover = "SELECT sum(b.total_left_over_receive) as leftover_qty from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and b.po_break_down_id=$po_id $country_cond and b.item_number_id=$gmts_item and a.status_active=1 and b.status_active=1 and a.company_id=$company and a.location=$location and a.goods_type=$goods_type $wo_com_loc_cond ";
		$sql_leftover_res = sql_select($sql_leftover);
		$leftover_qty = 0;
		foreach ($sql_leftover_res as $val)
		{
			$leftover_qty += $val[csf('leftover_qty')];
		}
		// echo $delivery_qty;

		// ============================= GETTING DELIVERY QTY ============================

		$sql_delivery = "SELECT sum(b.production_quantity) as delivery_qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b where a.id=b.mst_id and b.from_po_id=$po_id $country_cond and b.item_number_id=$gmts_item and b.production_type=10 and a.status_active=1 and b.status_active=1";
		$sql_delivery_res = sql_select($sql_delivery);
		$delivery_qty = 0;
		foreach ($sql_delivery_res as $val)
		{
			$delivery_qty += $val[csf('delivery_qty')];
		}
		// echo $delivery_qty;

		// ============================= GETTING EX-FACTORY QTY ============================
		$country_cond = ($country_maintain==1) ? " and a.country_id=$country_id" : "";
		$wo_com_loc_cond = ($wo_com_loc_mandatory==1) ? " and c.delivery_company_id=$wo_company and c.delivery_location_id=$wo_location" : "";
		$sql_ex_fact = "SELECT sum(case when a.entry_form != 85 then a.ex_factory_qnty else 0 end) - sum(case when a.entry_form = 85 then a.ex_factory_qnty else 0 end) as ex_fact_qty from pro_ex_factory_mst a,pro_ex_factory_delivery_mst c where c.id=a.delivery_mst_id and a.po_break_down_id=$po_id $country_cond $wo_com_loc_cond and a.item_number_id=$gmts_item and a.status_active=1  and c.status_active=1";//and c.delivery_company_id=$wo_company and c.delivery_location_id=$wo_location
		// echo $sql_ex_fact;
		$sql_ex_fact_res = sql_select($sql_ex_fact);
		$ex_fact_qty = 0;
		foreach ($sql_ex_fact_res as $val)
		{
			$ex_fact_qty += $val[csf('ex_fact_qty')];
		}

		echo ($goods_type==2) ? $sewing_out_qty - $leftover_qty : $sewing_out_qty - ($leftover_qty + $delivery_qty + $ex_fact_qty);
		// echo ($goods_type==2) ? $reject_qty : $sewing_out_qty - ($leftover_qty + $delivery_qty + $ex_fact_qty);
	}
	else// subcon order
	{
		// ============================= GETTING LEFTOVER QTY ============================
		$sql_leftover = "SELECT sum(b.total_left_over_receive) as leftover_qty from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and b.po_break_down_id=$po_id and b.item_number_id=$gmts_item and a.status_active=1 and b.status_active=1 and a.goods_type=$goods_type";//and a.company_id=$company and a.location=$location
		$sql_leftover_res = sql_select($sql_leftover);
		$leftover_qty = 0;
		foreach ($sql_leftover_res as $val)
		{
			$leftover_qty += $val[csf('leftover_qty')];
		}
		// ============================= GETTING DELIVERY QTY ============================
		$sql_del = sql_select("SELECT sum(b.delivery_qty) as delv_qnty from subcon_ord_breakdown a left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id and b.status_active=1 where a.order_id='$po_id' and a.item_id='$gmts_item' and a.status_active=1");
		foreach($sql_del as $row_exfac)
		{
			$delivery_qty += $row_exfac[csf("delv_qnty")];
		}
		// ============================= GETTING PRODUCTION QTY ============================
		$sql = "SELECT sum(b.prod_qnty) as production_qnty from subcon_gmts_prod_dtls a, subcon_gmts_prod_col_sz b, subcon_ord_breakdown c where a.id=b.dtls_id and b.production_type=2 and a.status_active=1 and a.is_deleted=0 and a.order_id='$po_id' and c.item_id=$gmts_item and c.id=b.ord_color_size_id";
		$sqlRes = sql_select($sql);
		foreach ($sqlRes as $val)
		{
			$prod_qty += $val[csf("production_qnty")];
		}
		echo $prod_qty - ($delivery_qty + $leftover_qty);
	}
}

if($action=="system_number_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);

	//$order_type_arr = array(1=>"Self Order",2=>"Subcontract Order");
	$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
	?>
	<script>
		$(document).ready(function(e) {
            //$("#txt_search_common").focus();
			load_drop_down( 'left_over_garments_receive_controller','<? echo $company; ?>', 'load_drop_down_location2', 'location_td' );
        });

		function js_set_value(id)
		{
			$("#hidden_search_data").val(id);//po id
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:1300px;" >
	    <form name="searchorderfrm_1" style="width:1300px;" id="searchorderfrm_1" autocomplete="off">
	        <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>
	                    <th>Company</th>
						<th>Job No</th>
	                    <th>Location</th>
	                    <th>Order Type</th>
	                    <th>Buyer</th>
	                    <th>Store Name</th>
						<th>Int ref</th>
	                    <th>System No</th>
	                    <th width="200">Date Range</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td>
	                    <?
	                    echo create_drop_down( "cbo_company_name", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$company, "load_drop_down( 'left_over_garments_receive_controller', this.value, 'load_drop_down_location2', 'location_td' );");?>
	                    </td>
						<td>
						<input type="text" class="text_boxes" name="txt_job_no" id="txt_job_no">


						</td>
	                    <td id="location_td">
	                    <?
	                    echo create_drop_down( "cbo_location_name", 120, $blank_array,'', 1, '--- Select Location ---', $selected, "",0,0 );
	                    ?>
	                    </td>
	                    <td>
	                    <?
	                    echo create_drop_down( "cbo_order_type", 120, $order_source, "", 1, "-- Select --", $selected, "", "", "1,2", "", "");
	                    ?>
	                    </td>
	                    <td>
	                    <?
	                    echo create_drop_down( "cbo_buyer_name", 120, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","",0 );
	                    ?>
	                    </td>
	                    <td id="store_name_td">

	                    <?
	                    echo create_drop_down( "cbo_store_name", 120, "select id,store_name from lib_store_location", "id,store_name", 1, "-- Select Store --", $selected,"",0,0);
	                    ?>
	                    </td>
						<td>
						<input type="text" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref">

						</td>

	                    <td>
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />
	                    </td>
	                    <td align="center">
	                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
	                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
	                    </td>
	                    <td align="center">

	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_internal_ref').value, 'create_system_number_list_view', 'search_div', 'left_over_garments_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" />
	                    </td>
	                </tr>
	            </tbody>
	            <tfoot>
	                <tr>
	                    <td align="center" height="25" valign="middle" colspan="9" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%);">
	                    <? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_search_data">
	                    </td>
	                </tr>
	            </tfoot>
	        </table>
	        <div style="margin-top:10px" id="search_div"></div>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_system_number_list_view")
{
 	$ex_data = explode("_",$data);

    $company = $ex_data[0];
    $location = $ex_data[1];
    $txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
    $order_type = $ex_data[4];
    $buyer_id = $ex_data[5];
    $store_id = $ex_data[6];
    // $floor_id = $ex_data[7];
	$system_no = $ex_data[7];
	$job_no  =$ex_data[8];
	$internal_ref =$ex_data[9];

	$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
	$order_type_arr = array(1=>"Self Order",2=>"Subcontract Order");
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
	$garments_item=return_library_array("SELECT id,item_name from  lib_garment_item", 'id', 'item_name');
	$location_name=return_library_array("SELECT id,location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name');
	$country_arr=return_library_array( "SELECT id, country_name from lib_country",'id','country_name');
	$store_name_arr=return_library_array( "SELECT id,store_name from lib_store_location",'id','store_name');
	$floor_name_arr=return_library_array( "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0",'id','floor_name');
	$sql_cond="";


	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.leftover_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.leftover_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.leftover_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
		}
	}
	if($db_type==2 || $db_type==1)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.leftover_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.leftover_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.leftover_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
		}

	}

	if(trim($system_no)!="")
	{
		$sql_cond = " and a.sys_number like '%".trim($system_no)."'";
	}
	if(trim($company)!='0')
	{
		$sql_cond .= " and a.company_id='$company'";
	}
	if(trim($location)!='0')
	{
		$sql_cond .= " and a.location='$location'";
	}

	if(trim($order_type)!='0')
	{
		$sql_cond .= " and a.order_type='$order_type'";
	}
	if(trim($buyer_id)!=0)
	{
		$sql_cond .= " and a.buyer_name='$buyer_id'";
	}
	if(trim($store_id)!='0')
	{
		$sql_cond .= " and a.store_name='$store_id'";
	}

	if(trim($job_no) != '')
	{
		$sql_cond .= " and b.job_no like '%$job_no%'";
	}
	if(trim($internal_ref) != '')
	{
		$sql_cond .= " and c.grouping='$internal_ref'";
	}







	$sql = "SELECT a.id, a.sys_number, a.company_id, a.location, a.leftover_date, a.order_type, a.buyer_name, a.store_name, a.floor_id, a.goods_type,b.job_no, b.total_left_over_receive, c.GROUPING AS internal_ref from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b,wo_po_break_down c where a.id=b.mst_id and c.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0  $sql_cond order by a.id desc ";
	// echo $sql; die;
	$res = sql_select($sql);
	// $sql ="SELECT a.id, a.sys_number, a.company_id, a.location, a.leftover_date, a.order_type, a.buyer_name, a.store_name, a.floor_id, a.goods_type  from pro_leftover_gmts_rcv_mst a where  a.status_active=1 and a.is_deleted=0 $sql_cond order by a.id";
	//  echo $sql;
	$master_data_arr=array();
	foreach($res as $val)
	{
		$master_data_arr[$val[csf('id')]]['sys_number']=$val['SYS_NUMBER'];
		$master_data_arr[$val[csf('id')]]['company_id']=$val['COMPANY_ID'];
		$master_data_arr[$val[csf('id')]]['location']=$val['LOCATION'];
		$master_data_arr[$val[csf('id')]]['leftover_date']=$val['LEFTOVER_DATE'];
		$master_data_arr[$val[csf('id')]]['received_qty']=$val['TOTAL_LEFT_OVER_RECEIVE'];
		$master_data_arr[$val[csf('id')]]['order_type']=$val['ORDER_TYPE'];
		$master_data_arr[$val[csf('id')]]['buyer_name']=$val['BUYER_NAME'];
		$master_data_arr[$val[csf('id')]]['store_name']=$val['STORE_NAME'];
		$master_data_arr[$val[csf('id')]]['floor_id']=$val['FLOOR_ID'];
		$master_data_arr[$val[csf('id')]]['goods_type']=$val['GOODS_TYPE'];
		$master_data_arr[$val[csf('id')]]['job_no']=$val['JOB_NO'];
		$master_data_arr[$val[csf('id')]]['internal_ref']=$val['INTERNAL_REF'];
	}
 	// echo "<pre>";	print_r($master_data_arr);die();
	?>
	<div style="width:940px;margin:0 auto">
		<table  class="rpt_table" id="rpt_table_list_view" rules="all" width="920" height="" cellspacing="0" cellpadding="0" border="0" align="left">
			<thead>
				<tr align="left" width="200">
					<th  width="50">SL No</th>
					<th width="80">Job No</th>
					<th width="120">System Number</th>
					<th width="80">Receive Date</th>
					<th width="110">Internal Ref</th>
					<th width="100">Order Type</th>
					<th width="100">Buyer</th>
					<th width="120">Goods Type</th>
					<th width="80">Store Name</th>
					<th width="80">Floor</th>
					<th width="80">Received Qty</th>
				</tr>
			</thead>
		</table>
		<div style="max-height:220px; width:940px; overflow-y:scroll" id="">
			<table  class="rpt_table" id="list_view" rules="all" width="920" height="" cellspacing="0" cellpadding="0"
				border="0" align="left">
				<tbody>
					<? $i=1;
					foreach($master_data_arr as $key=>$row)
					{
						$set_value = $key."_".$row['sys_number']."_".$row['company_id'];
						?>

						<tr align="right" onClick="js_set_value('<?=$set_value;?>')" style="cursor:pointer"
							bgcolor="#FFFFFF">
							<td align="left" width="50"><?=$i;?></td>
							<td align="left"width="80"><? echo $row['job_no'];?></td>
							<td align="left" width="120"><? echo $row['sys_number']; ?></td>
							<td align="left" width="80"><? echo $row['leftover_date'];?></td>
							<td align="left" width="110"><? echo $row['internal_ref']; ?></td>
							<td align="left" width="100"><? echo $order_type_arr[$row['order_type']];?></td>
							<td align="left" width="100"><? echo $buyer_arr[$row['buyer_name']];?></td>
							<td align="left" width="120"><? echo $goods_type_arr[$row['goods_type']];?></td>
							<td align="left" width="80"><? echo $store_name_arr[$row['store_name']];?></td>
							<td align="left" width="80"><? echo $floor_name_arr[$row['floor_id']];?></td>
							<td align="right" width="80"><? echo $row['received_qty'];?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<?


	/* $sql ="SELECT a.id, a.sys_number, a.company_id, a.location, a.leftover_date, a.order_type, a.buyer_name, a.store_name, a.floor_id, a.goods_type  from pro_leftover_gmts_rcv_mst a where  a.status_active=1 and a.is_deleted=0 $sql_cond order by a.id";

	$arr=array(2=>$order_source,3=>$buyer_arr,4=>$store_name_arr,5=>$floor_name_arr);

	echo create_list_view("list_view", "System Number,Receive Date,Order Type,Buyer,Store Name,Floor","120,80,100,100,100,80","700","240",0, $sql , "js_set_value","id,sys_number,company_id", "",1, "0,0,order_type,buyer_name,store_name,floor_id", $arr,"sys_number,leftover_date,order_type,buyer_name,store_name,floor_id", "","setFilterGrid('list_view',-1)","0,3,0,0,0,0") ; */

	exit();
}


if($action=="show_dtls_listview")
{
	$dataArr = explode("**",$data);

	$location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "SELECT id, supplier_name from  lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "SELECT id, line_name from  lib_sewing_line",'id','line_name');
	$buyer_library=return_library_array( "SELECT id, short_name from   lib_buyer", "id", "short_name"  );


	$po_cond = "";
	if($dataArr[0]!=''){
		$po_cond = " and b.po_break_down_id=$dataArr[0]";
		// $po_cond = " and b.mst_id=$dataArr[0]";
	}
	if($dataArr[1]!=''){
		$item_id_cond = " and b.item_number_id=$dataArr[1]";
	}
	if($dataArr[2]!=''){
		if($dataArr[4]==1)// when country maintain yes
		{
			$country_cond = " and b.country_id=$dataArr[2]";
		}

	}
	if($dataArr[3]!=''){
		$company_cond = " and a.company_id=$dataArr[3]";
	}
	?>
    <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70" align="center">Date</th>
                <th width="180" align="center">PO</th>
                <th width="70" align="center">Style Ref</th>
                <th width="80" align="center">Buyer</th>
                <? if($dataArr[4]==1){?>
                <th width="80" align="center">Country</th>
                <?}?>
                <th width="80" align="center">Goods Type</th>
                <th width="80" align="center">Leftover Qty.</th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="iron_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php
			$i=1;
			if($db_type==0)
			{
				$sql ="SELECT a.leftover_date, a.buyer_name, a.goods_type, b.id, b.mst_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.leftover_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel, b.remarks from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $po_cond $item_id_cond $country_cond $company_cond order by b.id";
			}
		    if($db_type==2)
			{

				$sql ="SELECT a.leftover_date, a.buyer_name, a.goods_type, b.id, b.mst_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.leftover_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel, b.remarks from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $po_cond $item_id_cond $country_cond $company_cond order by b.id";
			}
			// echo $sql;
			$sqlResult =sql_select($sql);
			foreach($sqlResult as $selectResult)
			{

				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
	 			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="populate_selected_data(<? echo $selectResult[csf('id')]; ?>,<? echo $selectResult[csf('mst_id')]; ?>,<? echo $selectResult[csf('po_break_down_id')]; ?>,<? echo $selectResult[csf('country_id')]; ?>,<? echo $selectResult[csf('item_number_id')]; ?>,<? echo $selectResult[csf('sewing_production_variable')]; ?>);" >
					<td width="30" align="center"><? echo $i; ?></td>
	                <td width="70" align="center"><?php echo change_date_format($selectResult[csf('leftover_date')]); ?></td>
	                <td width="180" align="center"><p><? echo $selectResult[csf('order_no')]; ?></p></td>
	                <td width="70" align="center"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
	                <td width="80" align="center"><p><? echo $buyer_library[$selectResult[csf('buyer_name')]]; ?></p></td>
	                <? if($dataArr[4]==1){?>
	                <td width="80" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
	                <?}?>
	                <td width="80" align="center"><p><? echo $goods_type_arr[$selectResult[csf('goods_type')]]; ?></p></td>
	                <td width="80" align="center"><p id="leftOverReceive_<?php echo $i; ?>"><? echo $selectResult[csf('total_left_over_receive')];?></p></td>
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

if($action=="show_dtls_listview_system")
{
	$dataArr = explode("**",$data);

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );


	$po_cond = "";
	if($dataArr[0]!=''){
		$po_cond = " and b.mst_id='$dataArr[0]' ";
	}
	$country_maintain = $dataArr[1];
	/*if($dataArr[1]!=''){
		$item_id_cond = " and a.item_number_id='$item_id' ";
	}
	if($dataArr[2]!=''){
		$company_cond = " and a.company_id='$company_id'";
	}*/
	?>
    <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70" align="center">Date</th>
                <th width="100" align="center">Job No</th>
                <th width="180" align="center">PO</th>
                <th width="100" align="center">Internal Ref.</th>
                <th width="70" align="center">Style Ref</th>
                <th width="80" align="center">Buyer</th>
                <? if($country_maintain==1){?>
                <th width="80" align="center">Country</th>
                <?}?>
                <th width="80" align="center">Goods Type</th>
                <th width="80" align="center">Leftover Qty.</th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="iron_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php
			$i=1;
			$total_production_qnty=0;
			if($db_type==0)
			{
				$sql ="SELECT b.id, b.mst_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.leftover_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel, b.remarks, c.grouping, c.job_no_mst
				from pro_leftover_gmts_rcv_mst a,   pro_leftover_gmts_rcv_dtls b, wo_po_break_down c
				where b.status_active=1 and b.is_deleted=0 $po_cond and a.id=b.mst_id and b.po_break_down_id = c.id order by b.id";//,wo_po_color_size_breakdown d   and  c.color_size_break_down_id = d.id
			}
		    if($db_type==2)
			{

				$sql ="SELECT a.leftover_date, a.buyer_name, a.goods_type, b.id, b.mst_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.leftover_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel, b.remarks, c.grouping, c.job_no_mst
				from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, wo_po_break_down c
				where a.id=b.mst_id and b.po_break_down_id = c.id and b.status_active=1 and b.is_deleted=0 $po_cond
				order by b.id";//,wo_po_color_size_breakdown d  and  c.color_size_break_down_id = d.id po_break_down_id
			}
			//echo $sql;
			$sqlResult =sql_select($sql);
			foreach($sqlResult as $selectResult){

				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('total_left_over_receive')];
				//dtls_id,mst_id,po_id,country_id,item_id,variableSettings
 		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="populate_selected_data(<? echo $selectResult[csf('id')]; ?>,<? echo $selectResult[csf('mst_id')]; ?>,<? echo $selectResult[csf('po_break_down_id')]; ?>,<? echo $selectResult[csf('country_id')]; ?>,<? echo $selectResult[csf('item_number_id')]; ?>,<? echo $selectResult[csf('sewing_production_variable')]; ?>);" >
				<td width="30" align="center"><? echo $i; ?></td>
                <td width="70" align="center"><?php echo change_date_format($selectResult[csf('leftover_date')]); ?></td>
                <td width="100" align="center"><p><? echo $selectResult[csf('job_no_mst')]; ?></p></td>
                <td width="180" align="center"><p><? echo $selectResult[csf('order_no')]; ?></p></td>
                <td width="100" align="center"><p><? echo $selectResult[csf('grouping')]; ?></p></td>
                <td width="70" align="center"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                <td width="80" align="center"><p><? echo $buyer_library[$selectResult[csf('buyer_name')]]; ?></p></td>
                <? if($country_maintain==1){?>
                <td width="80" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                <?}?>
                <td width="80" align="center"><p><? echo $goods_type_arr[$selectResult[csf('goods_type')]]; ?></p></td>
                <td width="80" align="center"><p id="leftOverReceive_<?php echo $i; ?>"><? echo $selectResult[csf('total_left_over_receive')];//$selectResult[csf('total_left_over_receive')]; ?></p></td>
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
	extract($_REQUEST);
	$data_arr 				= explode("**", $data);
	$po_id 					= $data_arr[0];
	$company_name 			= $data_arr[1];
	$location_name 			= $data_arr[2];
	$order_type 			= $data_arr[3];
	$buyer_name 			= $data_arr[4];
	$goods_type 			= $data_arr[5];
	$working_company_name 	= $data_arr[6];
	$working_location_name 	= $data_arr[7];
	$country_maintain 		= $data_arr[8];
	$leftover_source 		= $data_arr[9];
	$wo_com_loc_mandatory	= $data_arr[10];
	if($leftover_source==1)
	{
		if($goods_type==2) $prod_type_id="5,80,8";  else $prod_type_id="5";
	}else
	{
		$prod_type_id = 11;
	}
	// if($leftover_source==1) $prod_type_id="5,80,8"; else $prod_type_id="11";
	//$prod_type_id = ($leftover_source==1) ? 5 : 11;
	$prod_qty_source = ($goods_type==2) ? "b.reject_qty" : "b.production_qnty";

	if($order_type==1)
	{
		$wo_com_loc_cond = ($wo_com_loc_mandatory==1) ? " and a.working_company_id=$working_company_name and a.working_location_id=$working_location_name" : "";
		// ============================= GETTING DELIVERY QTY ============================
		if($goods_type==2) {
			$sql_delivery = "SELECT b.from_po_id as po_id,b.country_id,b.item_number_id as item_id, b.production_quantity as delivery_qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b where a.id=b.mst_id and b.from_po_id=$po_id and a.company_id=$company_name   and b.production_type=10 and a.status_active=1 and b.status_active=1 $wo_com_loc_cond group by b.from_po_id,b.country_id,b.item_number_id";
		} else {
			$sql_delivery = "SELECT b.from_po_id as po_id,b.country_id,b.item_number_id as item_id,sum(b.production_quantity) as delivery_qty from pro_gmts_delivery_mst a, pro_gmts_delivery_dtls b where a.id=b.mst_id and b.from_po_id=$po_id and a.company_id=$company_name  and b.production_type=10 and a.status_active=1 and b.status_active=1 $wo_com_loc_cond group by b.from_po_id,b.country_id,b.item_number_id";
		}
		$sql_delivery_res = sql_select($sql_delivery);
		$delivery_qty_array = array();
		foreach ($sql_delivery_res as $val)
		{
			if($country_maintain==1)
			{
				$delivery_qty_array[$val[csf('po_id')]][$val[csf('country_id')]][$val[csf('item_id')]] = $val[csf('delivery_qty')];
			}
			else
			{
				$delivery_qty_array[$val[csf('po_id')]][$val[csf('item_id')]] += $val[csf('delivery_qty')];
			}
		}
		// echo $delivery_qty;

		// ============================= GETTING EX-FACTORY QTY ============================
		$wo_com_loc_cond = ($wo_com_loc_mandatory==1) ? " and c.delivery_company_id=$working_company_name and c.delivery_location_id=$working_location_name" : "";
		$sql_ex_fact = "SELECT a.po_break_down_id as po_id,a.item_number_id as item_id, a.country_id,sum(case when a.entry_form != 85 then a.ex_factory_qnty else 0 end) - sum(case when a.entry_form = 85 then a.ex_factory_qnty else 0 end) as ex_fact_qty from pro_ex_factory_mst a, pro_ex_factory_delivery_mst c where c.id=a.delivery_mst_id and a.po_break_down_id=$po_id and c.company_id=$company_name and a.status_active=1 and c.status_active=1 $wo_com_loc_cond group by a.po_break_down_id,a.item_number_id, a.country_id";// and c.delivery_company_id=$working_company_name and c.delivery_location_id=$working_location_name
		$sql_ex_fact_res = sql_select($sql_ex_fact);
		$ex_fact_qty_array = array();
		foreach ($sql_ex_fact_res as $val)
		{
			if($country_maintain==1)
			{
				$ex_fact_qty_array[$val[csf('po_id')]][$val[csf('country_id')]][$val[csf('item_id')]] = $val[csf('ex_fact_qty')];
			}
			else
			{
				$ex_fact_qty_array[$val[csf('po_id')]][$val[csf('item_id')]] += $val[csf('ex_fact_qty')];
			}
		}
		// ============================= GETTING SHIP DATE ============================
		$shipdateSql = "SELECT item_number_id as item_id,country_id, po_break_down_id as po_id, max(country_ship_date) as sdate from wo_po_color_size_breakdown where po_break_down_id=$po_id and status_active in(1,2,3) and is_deleted=0 group by item_number_id,country_id, po_break_down_id";
		// echo $shipdateSql;
		$shipdateSqlRes = sql_select($shipdateSql);
		$shipdateArr = array();
		foreach ($shipdateSqlRes as $val)
		{
			if($country_maintain==1)
			{
				$shipdateArr[$val[csf('po_id')]][$val[csf('country_id')]][$val[csf('item_id')]] = $val[csf('sdate')];
			}
			else
			{
				$shipdateArr[$val[csf('po_id')]][$val[csf('item_id')]] += $val[csf('sdate')];
			}
		}
		// ============================= GETTING SEWING OUTPUT QTY ============================
		$wo_com_loc_cond = ($wo_com_loc_mandatory==1) ? " and a.location=$working_location_name and a.serving_company=$working_company_name" : "";
		$sql_sewing_out = "SELECT a.po_break_down_id as po_id,a.item_number_id as item_id, a.country_id, sum($prod_qty_source) as sewing_out_qty
		from pro_garments_production_mst a, pro_garments_production_dtls b
		where a.id=b.mst_id and a.po_break_down_id=$po_id and b.production_type in ($prod_type_id) and a.company_id=$company_name  and a.production_type=b.production_type and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_com_loc_cond group by a.po_break_down_id,a.item_number_id, a.country_id";
		// echo $sql_sewing_out;
		$sql_sewing_out_res = sql_select($sql_sewing_out);
		$sewing_out_qty_array = array();
		foreach ($sql_sewing_out_res as $val)
		{
			if($country_maintain==1)
			{
				$sewing_out_qty_array[$val[csf('po_id')]][$val[csf('country_id')]][$val[csf('item_id')]] = $val[csf('sewing_out_qty')];
			}
			else
			{
				$sewing_out_qty_array[$val[csf('po_id')]][$val[csf('item_id')]] += $val[csf('sewing_out_qty')];
			}
			// $sewing_out_qty_array[$val[csf('po_id')]]['reject_qty'] = $val[csf('reject_qty')];
		}
		// ========================================= getting left over qty ============================
		$wo_com_loc_cond = ($wo_com_loc_mandatory==1) ? " and a.working_company_id=$working_company_name and a.working_location_id=$working_location_name" : "";
		$sqlLeft ="SELECT b.po_break_down_id as po_id, b.item_number_id as item_id, b.country_id, sum(b.total_left_over_receive) as left_qnty from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and b.po_break_down_id=$po_id and a.company_id=$company_name and a.location=$location_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.goods_type=$goods_type $wo_com_loc_cond group by b.po_break_down_id, b.item_number_id, b.country_id";
		 //echo $sqlLeft;
		$sqlLeftResult = sql_select($sqlLeft);
		$left_qnty_array = array();
		foreach ($sqlLeftResult as $key => $val)
		{
			if($country_maintain==1)
			{
				$left_qnty_array[$val['PO_ID']][$val['COUNTRY_ID']][$val['ITEM_ID']] = $val['LEFT_QNTY'];
			}
			else
			{
				$left_qnty_array[$val['PO_ID']][$val['ITEM_ID']] += $val['LEFT_QNTY'];
			}
		}
		// =================================== main query ============================
		$country_select = ($country_maintain==1) ? ",country_id AS country_id" : ",LISTAGG(country_id, ',') WITHIN GROUP (ORDER BY country_id) AS country_id";
		$group_by = ($country_maintain==1) ? ",country_id" : " ";
		$sql = "SELECT po_break_down_id,item_number_id,max(country_ship_date) as SDATE $country_select from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 and po_break_down_id=$po_id group by po_break_down_id,item_number_id $group_by";
		$sqlResult = sql_select($sql);
		$tbl_width = ($country_maintain==1) ? 380 : 300;
		?>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width;?>" class="rpt_table">
	        <thead>
	            <th width="20">SL</th>
	            <th width="100">Item Name</th>
	            <? if($country_maintain==1){?>
	            <th width="80">Country</th>
	            <? } ?>
	            <th width="60">Shipment Date</th>
	            <th width="40">Del. Bal.</th>
	            <th width="40">Cum. L.O Qty.</th>
	            <th width="40">Balance</th>
	        </thead>
			<?
			$i=1;
			foreach($sqlResult as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$shipDate 		= $row["SDATE"];

				if($country_maintain==1)
				{

					$sewing_out_qty = $sewing_out_qty_array[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
					$exfact_out_qty = $ex_fact_qty_array[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
					$delv_out_qty 	= $delivery_qty_array[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
					$cumLft_qty 	= $left_qnty_array[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
				}
				else
				{
					$sewing_out_qty = $sewing_out_qty_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]];
					$exfact_out_qty = $ex_fact_qty_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]];
					$delv_out_qty 	= $delivery_qty_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]];
					$cumLft_qty 	= $left_qnty_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]];
				}

				$lftOvr_qty 	= ($goods_type==2) ? $sewing_out_qty : $sewing_out_qty - ($exfact_out_qty+$delv_out_qty);//$cumLft_qty+
				$balance 		= ($goods_type==2) ? ($sewing_out_qty - $cumLft_qty) : $lftOvr_qty - $cumLft_qty;
				//echo $balance.'-'.$sewing_out_qty.' - '.$cumLft_qty.' - '.$lftOvr_qty;
				// $reject_qty 	= $sewing_out_qty_array[$row[csf("po_break_down_id")]]['reject_qty'];
				// echo $sewing_out_qty ."- (".$cumLft_qty."+".$exfact_out_qty."+".$delv_out_qty.")";

				$country_name = "";
				$countryId = "";
				if($country_maintain==1)
	        	{
	        	 	$country_name = $country_library[$row[csf('country_id')]];
	        	 	$countryId = $row[csf('country_id')];
	        	}
	        	else
	        	{
	        		$countryId = 0;
	        	}

			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",'".$countryId."',".$balance; ?>);">
					<td width="20"><? echo $i; ?></td>
					<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
					<? if($country_maintain==1){?>
					<td width="80"><p><? echo $country_name; ?>&nbsp;</p></td>
					<? } ?>
					<td width="60" align="center"><? if($shipDate!="0000-00-00") echo change_date_format($shipDate); ?>&nbsp;</td>
					<td align="right" width="40"><?  echo $lftOvr_qty; ?></td>
	                <td align="right" width="40"><?  echo $cumLft_qty; ?></td>
	                <td align="right" width="40"><?  echo $balance; ?></td>
				</tr>
			<?
				$i++;
			}
			?>
		</table>
		<?
		exit();
	}
	else // subcon order
	{
		// ============================================production ===============================
		$sql = "SELECT c.item_id, sum(b.prod_qnty) as production_qnty from subcon_gmts_prod_dtls a, subcon_gmts_prod_col_sz b, subcon_ord_breakdown c where a.id=b.dtls_id and b.production_type=2 and a.status_active=1 and a.is_deleted=0 and a.order_id=$po_id and c.id=b.ord_color_size_id group by c.item_id";
		$sqlRes = sql_select($sql);
		$prod_qty_array = array();
		foreach ($sqlRes as $val)
		{
			$prod_qty_array[$val[csf("item_id")]] = $val[csf("production_qnty")];
		}
		//============================================ delv qty =======================================
		$delivery_qty_array = array();
		$sql_del = sql_select("SELECT a.item_id, sum(b.delivery_qty) as production_qnty from subcon_ord_breakdown a left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id where a.order_id=$po_id group by a.item_id");
		foreach($sql_del as $row_exfac)
		{
			$delivery_qty_array[$row_exfac[csf("item_id")]] = $row_exfac[csf("production_qnty")];
		}
		// ========================================= getting left over qty ============================
		$sqlLeft ="SELECT  b.item_number_id as ITEM_ID, sum(case when c.production_type=1 then c.production_qnty else 0 end) as LEFT_QNTY from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, subcon_ord_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and c.color_size_break_down_id!=0 and c.production_type=1 and d.order_id=$po_id  group by b.item_number_id";
		// echo $sql;
		$sqlLeftResult = sql_select($sqlLeft);
		$left_qnty_array = array();
		foreach ($sqlLeftResult as $key => $val)
		{
			$left_qnty_array[$val['ITEM_ID']] = $val['LEFT_QNTY'];
		}
		// =================================== main query ============================

		$sql = "SELECT order_id,item_id from subcon_ord_breakdown where status_active in(1,2,3) and is_deleted=0 and order_id=$po_id group by order_id,item_id";
		$sqlResult = sql_select($sql);

		?>
	    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table">
	        <thead>
	            <th width="20">SL</th>
	            <th width="100">Item Name</th>
	            <th width="40">Del. Bal.</th>
	            <th width="40">Cum. L.O Qty.</th>
	            <th width="40">Balance</th>
	        </thead>
			<?
			$i=1;
			foreach($sqlResult as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$shipDate 		= $row["SDATE"];

				$prod_qty 		= $prod_qty_array[$row[csf("item_id")]];
				$delivery_qty 	= $delivery_qty_array[$row[csf("item_id")]];
				$cumLft_qty 	= $left_qnty_array[$row[csf("item_id")]];

				$lftOvr_qty 	= $prod_qty - $delivery_qty;
				$balance 		= $lftOvr_qty - $cumLft_qty;
				echo $prod_qty."+".$delivery_qty."+".$cumLft_qty."<br>";

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('order_id')].",".$row[csf('item_id')].",'0',".$balance; ?>);">
					<td width="20"><? echo $i; ?></td>
					<td width="100"><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
					<td align="right" width="40"><?  echo $lftOvr_qty; ?></td>
	                <td align="right" width="40"><?  echo $cumLft_qty; ?></td>
	                <td align="right" width="40"><?  echo $balance; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
		<?
		exit();

	}
}

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$preceding_process = $dataArr[3];
	$country_maintain = $dataArr[4];
	if($country_maintain==1){$country_cond = " and a.country_id=$country_id";$country_cond2 = " and country_id=$country_id";}


	$company_id_sql=sql_select("SELECT a.company_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id group by a.company_name");
	$company_id=$company_id_sql[0][csf("company_name")];
	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];


	$mst_table=($preceding_process==123)? "pro_cut_delivery_order_dtls" : "pro_garments_production_mst";
	$dtls_table=($preceding_process==123)? "pro_cut_delivery_color_dtls" : "pro_garments_production_dtls";

	$res = sql_select("SELECT a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name
			from wo_po_break_down a, wo_po_details_master b
			where a.job_id=b.id and a.id=$po_id and a.status_active=1 and b.status_active=1");

 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#txt_po_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_name').val('".$result[csf('style_ref_no')]."');\n";
 		if($qty_source!=0)
 		{
 			echo "$('#dynamic_msg').html('Total Cut Quantity');\n";
   			$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=$qty_source and b.production_type=$qty_source THEN b.production_qnty END) as totalreceive,SUM(CASE WHEN a.production_type=4 and b.production_type=4  THEN b.production_qnty ELSE 0 END) as totalinput from $mst_table a,$dtls_table b WHERE a.id=b.mst_id and  a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

   			if($qty_source=="9")
   			{
    			$pro_cut_sql=sql_select("SELECT SUM(CASE WHEN a.production_type=4 and b.production_type=4  THEN b.production_qnty ELSE 0 END) as totalinput from pro_garments_production_mst  a,pro_garments_production_dtls b WHERE a.id=b.mst_id and  a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' $country_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
   			}
   			if($sewing_level==1)
			{
				$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=$qty_source   THEN a.production_quantity END) as totalreceive,SUM(CASE WHEN a.production_type=4   THEN a.production_quantity ELSE 0 END) as totalinput from pro_garments_production_mst a WHERE  a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' $country_cond and a.status_active=1 and a.is_deleted=0 ");
			}

	 		foreach($dataArray as $row)
			{
	 			echo "$('#txt_receive_qnty').val('".$row[csf('totalreceive')]."');\n";
				echo "$('#txt_cumul_input_qty').val('".$row[csf('totalinput')]."');\n";
				$yet_to_produced = $row[csf('totalReceive')]-$row[csf('totalinput')];
				if($qty_source=="9")
				{
					echo "$('#txt_cumul_input_qty').val('".$pro_cut_sql[0][csf('totalinput')]."');\n";
					$yet_to_produced = $row[csf('totalReceive')]-$pro_cut_sql[0][csf('totalinput')];
				}
				echo "$('#txt_yet_to_input').attr('placeholder','".$yet_to_produced."');\n";
				echo "$('#txt_yet_to_input').val('".$yet_to_produced."');\n";
			}

 		}

		if($qty_source==0)
		{
			echo "$('#dynamic_msg').html('Total Plan Cut Qnty');\n";
			$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' $country_cond2 and status_active=1 and is_deleted=0");

			$total_produced = return_field_value("sum(b.production_qnty)","pro_garments_production_mst a,pro_garments_production_dtls b","a.id = b.mst_id and a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' $country_cond and a.production_type=4 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_type=4 and a.status_active=1");
			if($sewing_level==1)
			{
				$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst "," po_break_down_id=".$result[csf('id')]." and  item_number_id='$item_id' $country_cond2 and  production_type=4 and  is_deleted=0   and  status_active=1");
			}
			echo "$('#txt_receive_qnty').val('".$plan_cut_qnty."');\n";
 			echo "$('#txt_cumul_input_qty').val('".$total_produced."');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
 			echo "$('#txt_yet_to_input').val('".$yet_to_produced."');\n";
		}
  	}
 	exit();
}

if($action=="populate_mst_form_data")
{
	$sql ="SELECT a.id, a.sys_number, a.company_id, a.location, a.leftover_date, a.order_type, a.buyer_name, a.store_name, a.floor_id, a.garments_nature, a.goods_type, a.exchange_rate,a.working_company_id,a.working_location_id,a.working_floor_id, a.remarks from pro_leftover_gmts_rcv_mst a where a.id='$data' and a.status_active=1";

	// echo $sql.";\n";
	$result =sql_select($sql);
	echo"load_drop_down( 'requires/left_over_garments_receive_controller', ".$result[0][csf('company_id')].", 'load_drop_down_store_name', 'store_name_td' );\n";
	echo"load_drop_down( 'requires/left_over_garments_receive_controller', ".$result[0][csf('company_id')].", 'load_drop_down_location', 'cbo_location_name' );\n";

	echo"load_drop_down( 'requires/left_over_garments_receive_controller', '".$result[0][csf('working_company_id')]."', 'load_drop_down_working_location', 'working_location' );\n";
	echo"load_drop_down( 'requires/left_over_garments_receive_controller', '".$result[0][csf('working_location_id')]."_".$result[0][csf('working_company_id')]."', 'load_drop_down_working_floor', 'working_floor' );\n";

	echo "$('#txt_system_no').val('".$result[0][csf('sys_number')]."');\n";
	echo "$('#txt_mst_id').val('".$result[0][csf('id')]."');\n";
	echo "$('#cbo_company_name').val('".$result[0][csf('company_id')]."');\n";
	echo "$('#cbo_location_name').val('".$result[0][csf('location')]."');\n";
	echo "$('#txt_leftover_date').val('".change_date_format($result[0][csf('leftover_date')])."');\n";
	echo "$('#cbo_order_type').val('".$result[0][csf('order_type')]."');\n";
	echo "$('#cbo_buyer_name').val('".$result[0][csf('buyer_name')]."');\n";
	echo "$('#cbo_store_name').val('".$result[0][csf('store_name')]."');\n";
	// echo "$('#cbo_floor_name').val('".$result[0][csf('floor_id')]."');\n";
	echo "$('#exchange_rate').val('".$result[0][csf('exchange_rate')]."');\n";
	echo "$('#cbo_working_company_name').val('".$result[0][csf('working_company_id')]."');\n";
	echo "$('#cbo_working_location_name').val('".$result[0][csf('working_location_id')]."');\n";
	echo "$('#cbo_working_floor_name').val('".$result[0][csf('working_floor_id')]."');\n";
	echo "$('#txt_remark').val('".$result[0][csf('remarks')]."');\n";
	echo "$('#cbo_goods_type').val('".$result[0][csf('goods_type')]."');\n";


	echo "set_button_status(0, permission, 'fnc_left_over_gmts_input',1,0);\n";
 	exit();
}


if($action=="color_and_size_level_left_over")
{
	//echo $data;
	//210**3******1**3
	$dataArr 			= explode("**",$data);
	$po_id 				= $dataArr[0];
	$country_id 		= $dataArr[1];
	$company_id 		= $dataArr[2];
	$item_id 			= $dataArr[3];
	$goodsType 			= $dataArr[4];
	$variableSettings 	= $dataArr[5];
	$order_type 		= $dataArr[6];
	$location 			= $dataArr[7];
	$garments_nature 	= $dataArr[8];
	$country_maintain 	= $dataArr[9];
	$leftover_source 	= $dataArr[10];
	$working_company 	= $dataArr[11];
	$working_location 	= $dataArr[12];
	$is_wo_com_loc_mandatory = $dataArr[13];
	if($leftover_source==1)
	{
		if($goodsType==2) $prod_type_id="5,80,8";  else $prod_type_id="5";
	}else
	{
		$prod_type_id = 11;
	}
	// if($leftover_source==1) $prod_type_id="5,80,8"; else $prod_type_id="11";

	//$prod_type_id = ($leftover_source==1) ? 5 : 11;
	$prod_qty_source = ($goodsType==2) ? "a.reject_qty" : "a.production_qnty";

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	$country_cond = ($country_maintain==1) ? " and c.country_id=$country_id" : "";

	if($order_type==1) // Self Order
	{
		$sql= "SELECT a.job_no, a.style_ref_no, a.buyer_name, a.gmts_item_id, a.location_name, a.currency_id, a.company_name, b.po_number, b.id, b.pub_shipment_date, b.shipment_date, b.unit_price as order_rate, c.country_id, c.item_number_id
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0   and b.shiping_status in(1,2,3) and b.id=$po_id and a.company_name=$company_id and a.location_name=$location and c.item_number_id='$item_id' $country_cond";

		//and b.shiping_status=3
		//and a.ship_mode=3
		// echo $sql;
		$result = sql_select($sql);
		echo "$('#cbo_company_name').val('".$result[0][csf('company_name')]."');\n";
		echo "$('#cbo_location_name').val('".$result[0][csf('location_name')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[0][csf('buyer_name')]."');\n";
		echo "$('#txt_po_id').val('".$result[0][csf('id')]."');\n";
		echo "$('#txt_order_no').val('".$result[0][csf('po_number')]."');\n";
		echo "$('#hidden_job_no').val('".$result[0][csf('job_no')]."');\n";
		echo "$('#txt_style_name').val('".$result[0][csf('style_ref_no')]."');\n";
		//echo "$('#cbo_item_name').val('".$result[0][csf('gmts_item_id')]."');\n";
		echo "$('#cbo_item_name').val('".$result[0][csf('item_number_id')]."');\n";
		echo "$('#txt_fob_rate').val('".$result[0][csf('order_rate')]."');\n";
		echo "$('#cbo_currency').val('".$result[0][csf('currency_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[0][csf('country_id')]."');\n";
	}
	else // Subcontruct Order
	{
		$sql = "SELECT a.id, a.order_no, a.job_no_mst, a.cust_style_ref, a.cust_buyer, a.job_no_mst, a.order_quantity, a.order_uom, a.rate, a.amount, a.order_rcv_date, a.delivery_date, a.main_process_id, a.process_id, a.smv, a.grey_req, a.order_id, a.wastage,
		b.company_id, b.subcon_job, b.location_id, b.party_id, b.currency_id, b.within_group, b.embellishment_job, b.receive_date, b.party_location,
		c.item_id as item_id
		from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c
		where b.id=c.mst_id and a.job_no_mst=b.subcon_job and a.id=c.order_id and a.status_active=1 and a.is_deleted=0 and c.order_id='$po_id' and c.item_id='$item_id'";

		//echo $sql;
		$result = sql_select($sql);
		echo "$('#cbo_company_name').val('".$result[0][csf('company_id')]."');\n";
		echo "$('#cbo_location_name').val('".$result[0][csf('location_id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[0][csf('cust_buyer')]."');\n";
		echo "$('#txt_po_id').val('".$result[0][csf('id')]."');\n";
		echo "$('#txt_order_no').val('".$result[0][csf('order_no')]."');\n";
		echo "$('#hidden_job_no').val('".$result[0][csf('job_no_mst')]."');\n";
		echo "$('#txt_style_name').val('".$result[0][csf('cust_style_ref')]."');\n";
		echo "$('#cbo_item_name').val('".$result[0][csf('item_id')]."');\n";
		echo "$('#txt_fob_rate').val('".$result[0][csf('rate')]."');\n";
		echo "$('#cbo_currency').val('".$result[0][csf('currency_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[0][csf('country_id')]."');\n";
	}


	if( $variableSettings!=1 )
	{
		$wo_com_loc_cond = ($is_wo_com_loc_mandatory==1) ? " and a.working_company_id=$working_company and a.working_location_id=$working_location" : "";
		if($order_type==1)
		{
			$countryCond = str_replace("c.country_id", "b.country_id", $country_cond);
			$preProdData = sql_select("SELECT  d.size_number_id, d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id  and d.id=c.color_size_break_down_id and c.color_size_break_down_id!=0 and c.production_type=1 and d.po_break_down_id=$po_id and b.item_number_id='$item_id' $countryCond and a.goods_type=$goodsType and b.status_active=1 and c.status_active=1 and d.status_active in(1,2,3) $wo_com_loc_cond group by d.size_number_id, d.color_number_id");
			//echo "SELECT  d.size_number_id, d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id  and d.id=c.color_size_break_down_id and c.color_size_break_down_id!=0 and c.production_type=1 and d.po_break_down_id=$po_id and b.item_number_id='$item_id' $countryCond and a.goods_type=$goodsType and b.status_active=1 and c.status_active=1 and d.status_active in(1,2,3) $wo_com_loc_cond group by d.size_number_id, d.color_number_id";
		}
		else
		{
			$preProdData = sql_select("SELECT  d.size_id as size_number_id, d.color_id as color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, subcon_ord_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and c.color_size_break_down_id!=0 and c.production_type=1 and d.order_id=$po_id and b.item_number_id='$item_id' and b.status_active=1 and c.status_active=1 and d.status_active in(1,2,3) group by d.size_id, d.color_id");
		}

		foreach($preProdData as $row)
		{
			if( $variableSettings==2 )
			{
			 	$index = $row[csf('color_number_id')];
			}
			else
			{
			 	$index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
			}
			$color_size_qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['pre_rcv'] += $row[csf('production_qnty')];
			$amountArr[$index]['pre_rcv'] = $row[csf('production_qnty')];
		}
	}

	if($goodsType==1 || $goodsType==2)	//Good GMT In Hand - Damage GMT
	{
		if( $variableSettings==2 ) // color level
		{
			if($order_type==1) // Self Order
			{
				$countryCond = str_replace("c.country_id", "a.country_id", $country_cond);
				$wo_com_loc_cond = ($is_wo_com_loc_mandatory==1) ? " and c.delivery_company_id=$working_company and c.delivery_location_id=$working_location" : "";

				// $sql_exfac=sql_select("SELECT a.color_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a	left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.is_deleted=0 and ex.status_active=1	where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' $countryCond and a.is_deleted=0 and a.status_active in(1,2,3)	group by a.color_number_id ");
				$sql_exfac=sql_select("SELECT a.color_number_id,sum(case when b.entry_form != 85 then ex.production_qnty else 0 end) - sum(case when b.entry_form = 85 then ex.production_qnty else 0 end) as ex_production_qnty from wo_po_color_size_breakdown a, pro_ex_factory_dtls ex,pro_ex_factory_mst b,pro_ex_factory_delivery_mst c where ex.color_size_break_down_id=a.id and ex.is_deleted=0 and ex.status_active=1 and ex.mst_id=b.id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' $countryCond and a.po_break_down_id=b.po_break_down_id and c.id=b.delivery_mst_id  and c.status_active=1 and b.status_active=1 and ex.status_active=1 and a.is_deleted=0 and a.status_active in(1,2,3) $wo_com_loc_cond group by a.color_number_id ");

				foreach($sql_exfac as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("color_number_id")]]+=$row_exfac[csf("ex_production_qnty")];

				}

				$countryCond = str_replace("c.country_id", "b.country_id", $country_cond);
				$wo_com_loc_cond = ($is_wo_com_loc_mandatory==1) ? " and b.serving_company=$working_company and b.location=$working_location" : "";

				$dtlsData = sql_select("SELECT c.color_number_id, sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty, sum(CASE WHEN a.production_type in ($prod_type_id) then $prod_qty_source ELSE 0 END) as cur_production_qnty , sum(CASE WHEN a.production_type in ($prod_type_id) then a.reject_qty ELSE 0 END) as reject_qty from pro_garments_production_dtls a, pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and c.id=a.color_size_break_down_id and c.po_break_down_id=b.po_break_down_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' $countryCond and a.color_size_break_down_id!=0 and a.production_type in(4,$prod_type_id) and a.status_active=1 and b.status_active=1 and c.status_active in(1,2,3) $wo_com_loc_cond group by c.color_number_id");
				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_number_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_number_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_number_id')]]['rej']= $row[csf('reject_qty')];
				}
				$countryCond = str_replace("c.country_id", "country_id", $country_cond);
				$sql = "SELECT color_order, item_number_id, color_number_id, sum(order_quantity) as order_quantity, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' $countryCond and is_deleted=0 and status_active in(1,3) group by color_order, item_number_id, color_number_id order by color_number_id,color_order";
			}
			else // Subcontruct Order
			{

				$sql_del = sql_select("SELECT a.item_id, a.color_id,sum(b.delivery_qty) as production_qnty
				from subcon_ord_breakdown a
				left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id
				where a.order_id='$po_id' and a.item_id='$item_id'
				group by a.item_id, a.color_id");
				foreach($sql_del as $row_exfac)
				{
					$delivery_value[$row_exfac[csf("item_id")]][$row_exfac[csf("color_id")]]=$row_exfac[csf("production_qnty")];
				}
				$sql = "SELECT c.color_id, c.item_id, b.ord_color_size_id, sum(b.prod_qnty) as production_qnty
						from subcon_gmts_prod_dtls a, subcon_gmts_prod_col_sz b, subcon_ord_breakdown c
						where a.id=b.dtls_id and b.production_type=2 and a.status_active=1 and a.is_deleted=0 and a.order_id='$po_id' and c.id=b.ord_color_size_id
						group by c.color_id, c.item_id,b.ord_color_size_id order by c.color_id";
			}
		}
		else if( $variableSettings==3 ) //color and size level
		{
			if($order_type==1) // Self Order
			{
				$countryCond = str_replace("c.country_id", "a.country_id", $country_cond);
				$wo_com_loc_cond = ($is_wo_com_loc_mandatory==1) ? " and c.delivery_company_id=$working_company and c.delivery_location_id=$working_location" : "";

				// $sql_exfac=sql_select("SELECT a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.is_deleted=0 and ex.status_active=1 where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' $countryCond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.color_number_id,a.size_number_id ");

				$sql_exfac=sql_select("SELECT a.color_number_id,a.size_number_id,sum(case when b.entry_form != 85 then ex.production_qnty else 0 end) - sum(case when b.entry_form = 85 then ex.production_qnty else 0 end) as ex_production_qnty from wo_po_color_size_breakdown a, pro_ex_factory_dtls ex,pro_ex_factory_mst b,pro_ex_factory_delivery_mst c where ex.color_size_break_down_id=a.id and ex.is_deleted=0 and ex.status_active=1 and ex.mst_id=b.id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' $countryCond and a.po_break_down_id=b.po_break_down_id and c.id=b.delivery_mst_id  and c.status_active=1 and b.status_active=1 and a.is_deleted=0 and a.status_active in(1,2,3) $wo_com_loc_cond group by a.color_number_id,a.size_number_id ");

				//echo "SELECT a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a, pro_ex_factory_dtls ex,pro_ex_factory_mst b,pro_ex_factory_delivery_mst c where ex.color_size_break_down_id=a.id and ex.is_deleted=0 and ex.status_active=1 and ex.mst_id=b.id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' $countryCond and a.po_break_down_id=b.po_break_down_id and c.id=b.delivery_mst_id  and c.status_active=1 and b.status_active=1 and a.is_deleted=0 and a.status_active in(1,2,3) $wo_com_loc_cond group by a.color_number_id,a.size_number_id ";

				foreach($sql_exfac as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];
				}

				$countryCond = str_replace("c.country_id", "b.country_id", $country_cond);
				$wo_com_loc_cond = ($is_wo_com_loc_mandatory==1) ? " and b.serving_company=$working_company and b.location=$working_location" : "";
				$dtlsData = sql_select("SELECT c.color_number_id,c.size_number_id, sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty, sum(CASE WHEN a.production_type in ($prod_type_id) then $prod_qty_source ELSE 0 END) as cur_production_qnty , sum(CASE WHEN a.production_type in ($prod_type_id) then a.reject_qty ELSE 0 END) as reject_qty from pro_garments_production_dtls a, pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' $countryCond and a.color_size_break_down_id!=0 and a.production_type in(4,$prod_type_id) and c.id=a.color_size_break_down_id $wo_com_loc_cond group by c.color_number_id,c.size_number_id");
				//echo "SELECT c.color_number_id,c.size_number_id, sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty, sum(CASE WHEN a.production_type=$prod_type_id then $prod_qty_source ELSE 0 END) as cur_production_qnty , sum(CASE WHEN a.production_type=$prod_type_id then a.reject_qty ELSE 0 END) as reject_qty from pro_garments_production_dtls a, pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' $countryCond and a.color_size_break_down_id!=0 and a.production_type in(4,$prod_type_id) and c.id=a.color_size_break_down_id $wo_com_loc_cond group by c.color_number_id,c.size_number_id";
				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['rej']= $row[csf('reject_qty')];
				}
				$countryCond = str_replace("c.country_id", "country_id", $country_cond);
				$sql = "SELECT size_order, item_number_id, size_number_id, color_number_id, sum(order_quantity) as order_quantity, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' $countryCond and is_deleted=0 and status_active in(1,2,3) group by size_order, item_number_id, size_number_id, color_number_id order by color_number_id,size_order";
			}
			else // Subcontruct Order
			{

				$sql_del = sql_select("SELECT a.item_id, a.color_id, a.size_id, sum(b.delivery_qty) as production_qnty
				from subcon_ord_breakdown a
				left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id
				where a.order_id='$po_id' and a.item_id='$item_id'
				group by a.item_id, a.color_id, a.size_id");
				foreach($sql_del as $row_exfac)
				{
					$delivery_value[$row_exfac[csf("item_id")]][$row_exfac[csf("color_id")]][$row_exfac[csf("size_id")]]=$row_exfac[csf("production_qnty")];
				}
				$sql = "SELECT c.id, c.color_id, c.item_id, c.size_id, b.ord_color_size_id, sum(b.prod_qnty) as production_qnty
						from subcon_gmts_prod_dtls a, subcon_gmts_prod_col_sz b, subcon_ord_breakdown c
						where a.id=b.dtls_id and b.production_type=2 and a.status_active=1 and a.is_deleted=0 and a.order_id='$po_id' and c.id=b.ord_color_size_id
						group by c.id, c.color_id, c.item_id, c.size_id, b.ord_color_size_id order by c.color_id";
			}

		}
		else //by default gross level
		{
			echo "$('#txt_total_left_over_receive').attr('disabled',false);\n";
			die();
		}
		 //echo $sql;
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
				if($order_type==1) // Self Order
				{
					$index = $color_arr[$color[csf("color_number_id")]];
					$amount = $amountArr[$index];

					$iss_qnty=$color_size_qnty_array[$color[csf('color_number_id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('color_number_id')]]['rcv'];
					$rej_qnty=$color_size_qnty_array[$color[csf('color_number_id')]]['rej'];

					$exfac_qnty=$ex_fac_value[$color[csf('color_number_id')]];

					$pre_rcv_qnty 	= $amountArr[$color[csf('color_number_id')]]['pre_rcv'];


					$left_over_qty = ($goodsType==2) ? $rcv_qnty- $pre_rcv_qnty: $rcv_qnty-($exfac_qnty+$pre_rcv_qnty);

					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($left_over_qty).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
					$totalQnty += $left_over_qty;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else // subcontruct Order
				{
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
					// $colorHTML .='<tr><td width="70">'.$color_library[$color[csf("color_id")]].'</td><td width="60"><input type="text" name="colorSize" id="colSize_'.$color[csf("color_id")].($i+1).'" style="width:60px"  class="text_boxes_numeric" onblur="fn_total('.$color[csf("color_id")].','.($i+1).')"></td></tr>';// placeholder="'.($color[csf("production_qnty")]-$delivery_value[$color[csf("color_id")]]).'"

					$totalQnty += $color[csf("production_qnty")] - $delivery_value[$color[csf("color_id")]];
					$colorID .= $color[csf("color_id")].",";
				}

			}
			else //color and size level
			{
				if($order_type==1) // Self Order
				{
					$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];
					$amount = $amountArr[$index]['pre_rcv'];

					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[$color[csf("color_number_id")]] = $color[csf("color_number_id")];
					}
					//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

					$iss_qnty=$color_size_qnty_array[$color[csf('color_number_id')]][$color[csf('size_number_id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('color_number_id')]][$color[csf('size_number_id')]]['rcv'];
					$rej_qnty=$color_size_qnty_array[$color[csf('color_number_id')]][$color[csf('size_number_id')]]['rej'];

					$exfac_qnty=$ex_fac_value[$color[csf('color_number_id')]][$color[csf('size_number_id')]];

					$pre_rcv_qnty 	= $color_size_qnty_array[$color[csf('color_number_id')]][$color[csf('size_number_id')]]['pre_rcv'];

					$left_over_qty = 0;
					if($goodsType==2)// damage gmts
					{
						$left_over_qty = $rej_qnty-$pre_rcv_qnty;
					}
					else
					{
						$left_over_qty = $rcv_qnty-($exfac_qnty+$pre_rcv_qnty);
						 //echo $rcv_qnty."-(".$exfac_qnty."+".$pre_rcv_qnty.")".'<br>';
					}
					// $left_over_qty = ($goodsType==2) ? $rej_qnty-$pre_rcv_qnty : $rcv_qnty-($exfac_qnty+$pre_rcv_qnty);
					// echo $rej_qnty."-".$pre_rcv_qnty;

					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" placeholder="'.($left_over_qty).'" style="width:100px" value="" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';
					// placeholder="'.($left_over_qty).'"

				}
				else // subcontruct Order
				{
					$index = $color[csf("size_id")].$color_arr[$color[csf("color_id")]].$color[csf("color_id")];
					$amount = $amountArr[$index];

					$break_size_library=return_library_array( "select id, size_id from  subcon_ord_breakdown",'id','size_id');
					$break_color_library=return_library_array( "select id, color_id from  subcon_ord_breakdown",'id','color_id');
					$break_plan_cut_library=return_library_array( "select id, plan_cut from  subcon_ord_breakdown",'id','plan_cut');
					$prod_qnty= return_field_value("production_qnty","subcon_gmts_prod_dtls","id");

					if ( !in_array( $color[csf("color_id")],$chkColor) )
					{
						$color_size_array[]=$color[csf("color_id")];
						if( $j!=0 ) $colorHTML .= "</table></div>";
						$j=0;

						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_id")].'" style="width:230px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_id")].'\', \'\',1);"> <span id="accordion_h'.$color[csf("color_id")].'span">+</span>'.$color_library[$color[csf("color_id")]].' : <span id="total_'.$color[csf("color_id")].'">'.$prod_qnty_sub[$color[csf("color_id")]].'</span></h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_id")].'">';
						$chkColor[] = $color[csf("color_id")];
					}

					$pro_qnty	=$color[csf('production_qnty')];
					$exfac_qnty	=$delivery_value[$color[csf('item_id')]][$color[csf('color_id')]][$color[csf('size_id')]];
					$pre_rcv_qnty = $color_size_qnty_array[$color[csf('color_number_id')]][$color[csf('size_number_id')]]['pre_rcv'];

					$left_over_qty = $pro_qnty - ($exfac_qnty+$pre_rcv_qnty);

					// echo $pro_qnty ."- (".$exfac_qnty."+".$pre_rcv_qnty.")<br>";die();

					$colorID .= $color[csf("size_id")]."*".$color[csf("color_id")].",";

					$colorHTML .='<tr><td>'.$size_library[$break_size_library[$color[csf("ord_color_size_id")]]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_id")].($j+1).'" class="text_boxes_numeric"  value="'.$amount.'" placeholder="'.$left_over_qty.'" onblur="fn_total('.$color[csf("color_id")].','.($j+1).' )"style="text-align:right;width:100px;" /></td></tr>';//placeholder="'.$left_over_qty.'"
				}
			}
			$j++;
			$i++;

		}
		if( $variableSettings==2 )
		{
			$colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>';
			// $colorHTML = '<table rules="all" class="rpt_table" cellspacing="0" cellpadding="0" border="1"><thead><th width="70">Color</th><th width="60">Quantity</th></thead><tbody id="table_1">'.$colorHTML.'</tbody></table>';

		}




		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";

	}
	exit();
}


if($action=="color_and_size_level_left_over_update")
{
	$dataArr 			= explode("**",$data);
	$po_id 				= $dataArr[0];
	$country_id 		= $dataArr[1];
	$item_id 			= $dataArr[2];
	$dtls_id 			= $dataArr[3];
	$mst_id 			= $dataArr[4];
	$variableSettings 	= $dataArr[5];
	$order_type 		= $dataArr[6];
	$country_maintain 	= $dataArr[7];
	$leftover_source 	= $dataArr[8];
	$goods_type 		= $dataArr[9];
	$working_company 	= $dataArr[10];
	$working_location 	= $dataArr[11];
	$is_wo_com_loc_mandatory = $dataArr[12];
	if($leftover_source==1)
	{
		if($goods_type==2) $prod_type_id="5,80,8";  else $prod_type_id="5";
	}else
	{
		$prod_type_id = 11;
	}
	// if($leftover_source==1) $prod_type_id="5,80,8"; else $prod_type_id="11";

	//$prod_type_id = ($leftover_source==1) ? 5 : 11;
	$prod_qty_source = ($goods_type==2) ? "b.reject_qnty" : "a.production_qnty";

	$country_cond = ($country_maintain==1) ? " and d.country_id=$country_id" : "";

	$sql ="SELECT b.id, b.mst_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.leftover_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable,b.variable_country_maintain,b.leftover_source, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel, b.remarks, b.job_no,b.category_id, c.production_type, c.color_size_break_down_id, c.production_qnty
	from  pro_leftover_gmts_rcv_dtls b left join pro_leftover_gmts_rcv_clr_sz c on b.id=c.dtls_id and  c.status_active=1
	where b.id='$dtls_id' and  b.status_active=1 and b.is_deleted=0";

	// echo $sql;

	$result =sql_select($sql);
	echo "$('#txt_order_no').val('".$result[0][csf('order_no')]."');\n";
	echo "$('#txt_po_id').val('".$result[0][csf('po_break_down_id')]."');\n";
	echo "$('#hidden_job_no').val('".$result[0][csf('job_no')]."');\n";
	echo "$('#hidden_dtls_id').val('".$result[0][csf('id')]."');\n";
	echo "$('#txt_style_name').val('".$result[0][csf('style_ref_no')]."');\n";
	echo "$('#cbo_item_name').val('".$result[0][csf('item_number_id')]."');\n";
	echo "$('#cbo_country_name').val('".$result[0][csf('country_id')]."');\n";
	echo "$('#txt_total_left_over_receive').val('".$result[0][csf('total_left_over_receive')]."');\n";
	echo "$('#txt_total_left_over_receive_hidden').val('".$result[0][csf('total_left_over_receive')]."');\n";
	echo "$('#txt_remark2').val('".$result[0][csf('remarks')]."');\n";
	echo "$('#txt_mst_id').val('".$result[0][csf('mst_id')]."');\n";
	echo "$('#cbo_currency').val('".$result[0][csf('currency_id')]."');\n";
	echo "$('#txt_fob_rate').val('".$result[0][csf('fob_rate')]."');\n";
	echo "$('#txt_leftover_amount').val('".$result[0][csf('leftover_amount')]."');\n";
	echo "$('#txt_bdt_amount').val('".$result[0][csf('bdt_amount')]."');\n";
	echo "$('#cbo_room_no').val('".$result[0][csf('room_no')]."');\n";
	echo "$('#cbo_rack_no').val('".$result[0][csf('rack_no')]."');\n";
	echo "$('#cbo_shelf_no').val('".$result[0][csf('shelf_no')]."');\n";
	echo "$('#cbo_bin_no').val('".$result[0][csf('bin_no')]."');\n";
	echo "$('#cbo_category_id').val('".$result[0][csf('category_id')]."');\n";
	echo "$('#sewing_production_variable').val('".$result[0][csf('sewing_production_variable')]."');\n";
	echo "$('#country_maintain_variable').val('".$result[0][csf('variable_country_maintain')]."');\n";
	echo "$('#leftover_source').val('".$result[0][csf('leftover_source')]."');\n";
	echo "$('#hidden_po_break_down_id').val('".$result[0][csf('po_break_down_id')]."');\n";
	echo "$('#styleOrOrderWisw').val('".$result[0][csf('style_order_wisw')]."');\n";
	echo "$('#variable_is_controll').val('".$result[0][csf('variable_is_controll')]."');\n";
	echo "$('#txt_user_lebel').val('".$result[0][csf('user_lebel')]."');\n";
	echo "set_button_status(1, permission, 'fnc_left_over_gmts_input',1,0);\n";

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');


		if( $variableSettings!=1 )
		{
			if($order_type==1) // Self Order
			{
				$preProdData = sql_select("SELECT  c.color_size_break_down_id, d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and c.color_size_break_down_id!=0 and c.production_type=1 and d.item_number_id='$item_id' $country_cond and b.status_active=1 and c.status_active=1 group by c.color_size_break_down_id, d.color_number_id");
				foreach($preProdData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['pre_rcv'] += $row[csf('production_qnty')];
					$color_qnty_array[$row[csf('color_number_id')]]['pre_rcv']+= $row[csf('production_qnty')];
				}
			}
			else	// Subcontruct Order
			{
				$preProdData = sql_select("SELECT  c.color_size_break_down_id, d.color_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, subcon_ord_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and c.color_size_break_down_id!=0 and c.production_type=1 group by c.color_size_break_down_id, d.color_id");
				foreach($preProdData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['pre_rcv'] += $row[csf('production_qnty')];
					$color_qnty_array[$row[csf('color_id')]]['pre_rcv']+= $row[csf('production_qnty')];
				}
			}
		}

		//echo "<pre>";
		//print_r($color_size_qnty_array);



		if( $variableSettings==2 ) // color level
		{
			if($order_type==1) // Self Order
			{
				$ex_country_cond = str_replace("d.country_id", "a.country_id", $country_cond);
				$sql_exfac="SELECT a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.status_active=1 where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' $ex_country_cond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id  order by a.item_number_id, a.color_number_id";
				// echo $sql_exfac;
				$sql_exfac_res = sql_select($sql_exfac);
				foreach($sql_exfac_res as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];

				}
				$prod_country_cond = str_replace("d.country_id", "b.country_id", $country_cond);
				$prodData = "SELECT c.color_number_id, sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty, sum(CASE WHEN a.production_type in ($prod_type_id) then $prod_qty_source ELSE 0 END) as cur_production_qnty , sum(CASE WHEN a.production_type in ($prod_type_id) then a.reject_qty ELSE 0 END) as reject_qty from pro_garments_production_dtls a, pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and a.mst_id=b.id and a.color_size_break_down_id=c.id and c.po_break_down_id=b.po_break_down_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' $prod_country_cond and a.color_size_break_down_id!=0 and a.production_type in(4,$prod_type_id) group by c.color_number_id";
				// echo $prodData;
				$prodDataRes = sql_select($prodData);
				foreach($prodDataRes as $row)
				{
					$color_size_qnty_array[$row[csf('color_number_id')]]['prodQty']= $row[csf('cur_production_qnty')];
				}



				$dtlsData = "SELECT d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and c.color_size_break_down_id!=0 and c.production_type=1 and d.item_number_id='$item_id' $country_cond and b.status_active=1 and c.status_active=1 group by d.color_number_id";
				// echo $dtlsData;
				$dtlsDataRes = sql_select($dtlsData);
				foreach($dtlsDataRes as $row)
				{
					$color_size_qnty_array[$row[csf('color_number_id')]]['rcv']= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_number_id')]]+= $row[csf('production_qnty')];
				}

				$po_country_cond = str_replace("d.country_id", "country_id", $country_cond);
				$sql = "SELECT color_order, item_number_id, color_number_id, sum(order_quantity) as order_quantity, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' $po_country_cond and is_deleted=0 and status_active in(1,3) group by color_order, item_number_id, color_number_id order by color_number_id,color_order";
			}
			else // Subcontruct Order
			{

				$sql_del=sql_select("SELECT a.color_id,sum(b.delivery_qty) as production_qnty from
				subcon_ord_breakdown a left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id where a.order_id='$po_id' and a.item_id=$item_id group by a.color_id");
				foreach($sql_del as $row_exfac)
				{
					$delivery_value[$row_exfac[csf("color_id")]] = $row_exfac[csf("production_qnty")];
				}


				$prodData = sql_select("SELECT c.color_id, sum(b.prod_qnty) as production_qnty from subcon_gmts_prod_dtls a, subcon_gmts_prod_col_sz b, subcon_ord_breakdown c where a.id=b.dtls_id and b.production_type=2 and a.status_active=1 and a.is_deleted=0 and a.order_id=$po_id and a.gmts_item_id=$item_id and c.id=b.ord_color_size_id group by c.color_id");

				foreach($prodData as $row)
				{
					$color_size_qnty_array[$row[csf("color_id")]]['prodQty'] += $row[csf('production_qnty')];
				}

				$dtlsData = sql_select("SELECT  d.color_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, subcon_ord_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and b.item_number_id=$item_id and c.color_size_break_down_id!=0 and c.production_type=1 group by d.color_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_id')]]['rcv']= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_id')]]+= $row[csf('production_qnty')];
				}


				$sql = "SELECT color_id from subcon_ord_breakdown where order_id=$po_id and item_id=$item_id ";
			}

		}
		else if( $variableSettings==3 ) //color and size level
		{
			if($order_type==1) // Self Order
			{
				$ex_country_cond = str_replace("d.country_id", "a.country_id", $country_cond);
				$sql_exfac="SELECT a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.status_active=1 where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' $ex_country_cond and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id, a.size_number_id  order by a.item_number_id, a.color_number_id, a.size_number_id";
				// echo $sql_exfac;
				$sql_exfac_res = sql_select($sql_exfac);
				foreach($sql_exfac_res as $row_exfac)
				{
					$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];

				}

				$prod_country_cond = str_replace("d.country_id", "b.country_id", $country_cond);
				$prodData = "SELECT c.color_number_id,c.size_number_id, sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty, sum(CASE WHEN a.production_type in ($prod_type_id) then $prod_qty_source ELSE 0 END) as cur_production_qnty , sum(CASE WHEN a.production_type in ($prod_type_id) then a.reject_qty ELSE 0 END) as reject_qty from pro_garments_production_dtls a, pro_garments_production_mst b,wo_po_color_size_breakdown c where a.status_active=1 and b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' $prod_country_cond and a.color_size_break_down_id!=0 and a.production_type in(4,$prod_type_id) and c.id=a.color_size_break_down_id group by c.color_number_id,c.size_number_id";
				// echo $prodData;
				$prodDataRes = sql_select($prodData);
				foreach($prodDataRes as $row)
				{
					$color_size_qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['prodQty']= $row[csf('cur_production_qnty')];
				}


				$dtlsData = "SELECT  d.color_number_id,d.size_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and c.color_size_break_down_id!=0 and c.production_type=1 and d.item_number_id='$item_id' $country_cond and b.status_active=1 and c.status_active=1 group by d.color_number_id,d.size_number_id";
				// echo $dtlsData;
				$dtlsDataRes = sql_select($dtlsData);
				foreach($dtlsDataRes as $row)
				{
					$color_size_qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['rcv']= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_number_id')]]+= $row[csf('production_qnty')];
				}

				$po_country_cond = str_replace("d.country_id", "country_id", $country_cond);
				// $sql = "SELECT id,size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' $po_country_cond and is_deleted=0 and status_active in(1,3) order by color_number_id,size_order,id";
				$sql = "SELECT size_order, item_number_id, size_number_id, color_number_id, sum(order_quantity) as order_quantity, sum(plan_cut_qnty) AS plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' $po_country_cond and is_deleted=0 and status_active in(1,3) group by size_order, item_number_id, size_number_id, color_number_id order by color_number_id,size_order";
			}
			else	// Subcontruct Order
			{

				$sql_del=sql_select("SELECT a.item_id, a.color_id, a.size_id,sum(b.delivery_qty) as production_qnty from
				subcon_ord_breakdown a left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id where a.order_id='$po_id' and a.item_id='$item_id' group by a.item_id, a.color_id, a.size_id");
				foreach($sql_del as $row_exfac)
				{
					$delivery_value[$row_exfac[csf("item_id")]][$row_exfac[csf("color_id")]][$row_exfac[csf("size_id")]]=$row_exfac[csf("production_qnty")];
				}


				$prodData = sql_select("SELECT c.id, c.color_id, c.item_id, c.size_id, b.ord_color_size_id, sum(b.prod_qnty) as production_qnty from subcon_gmts_prod_dtls a, subcon_gmts_prod_col_sz b, subcon_ord_breakdown c where a.id=b.dtls_id and b.production_type=2 and a.status_active=1 and a.is_deleted=0 and a.order_id='$po_id' and c.id=b.ord_color_size_id group by c.id, c.color_id, c.item_id, c.size_id, b.ord_color_size_id order by c.color_id");

				foreach($prodData as $row)
				{
					$color_size_qnty_array[$row[csf('id')]]['prodQty'] += $row[csf('production_qnty')];
				}

				$dtlsData = sql_select("SELECT  c.color_size_break_down_id, d.color_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, subcon_ord_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and c.color_size_break_down_id!=0 and c.production_type=1 group by c.color_size_break_down_id, d.color_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_id')]]+= $row[csf('production_qnty')];
				}


				$sql = "SELECT id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part from subcon_ord_breakdown where order_id='$po_id' and item_id='$item_id' "; }

		}
		else //by default gross level
		{
			echo "$('#txt_total_left_over_receive').attr('disabled',false);\n";
			die();
		}

  		$colorHTML="";
		$colorID='';
		$chkColor = array();
		$i=0;
		$totalQnty=0;

		$colorResult = sql_select($sql);
 		foreach($colorResult as $color)
		{

			if( $variableSettings==2 ) // color level
			{
				if($order_type==1) // Self Order
				{
					$pro_qnty 		= $color_size_qnty_array[$color[csf('color_number_id')]]['prodQty'];
					$rcv_qnty 		= $color_size_qnty_array[$color[csf('color_number_id')]]['rcv'];
					$pre_rcv_qnty 	= $color_qnty_array[$color[csf('color_number_id')]]['pre_rcv'];

					$exfac_qnty		= $ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]];

					$left_over_qty = ($goods_type==2) ? ($pro_qnty-$pre_rcv_qnty)+$rcv_qnty : ($pro_qnty-($exfac_qnty+$pre_rcv_qnty))+$rcv_qnty;

					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($left_over_qty).'" style="width:100px"  onblur="fn_colorlevel_total('.($i+1).')" value="'.$rcv_qnty.'"></td></tr>';//placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'"
					$totalQnty += $rcv_qnty;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else // subcon order
				{
					$pro_qnty 		= $color_size_qnty_array[$color[csf('color_id')]]['prodQty'];
					$rcv_qnty 		= $color_size_qnty_array[$color[csf('color_id')]]['rcv'];
					$pre_rcv_qnty 	= $color_qnty_array[$color[csf('color_id')]]['pre_rcv'];

					$exfac_qnty		= $delivery_value[$color[csf('color_id')]];

					$left_over_qty = ($pro_qnty-($exfac_qnty+$pre_rcv_qnty))+$rcv_qnty;

					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($left_over_qty).'" style="width:100px"  onblur="fn_colorlevel_total('.($i+1).')" value="'.$rcv_qnty.'"></td></tr>';//placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'"
					$totalQnty += $rcv_qnty;
					$colorID .= $color[csf("color_id")].",";
				}
			}
			else if( $variableSettings==3 ) //color and size level
			{
				if($order_type==1) // Self Order
				{
					$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'">'.$color_size_total_qnty_array[$color[csf("color_number_id")]].'</span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
					}
					//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";


					$pro_qnty 		= $color_size_qnty_array[$color[csf('color_number_id')]][$color[csf('size_number_id')]]['prodQty'];
					$rcv_qnty 		= $color_size_qnty_array[$color[csf('color_number_id')]][$color[csf('size_number_id')]]['rcv'];
					$pre_rcv_qnty 	= $color_size_qnty_array[$color[csf('color_number_id')]][$color[csf('size_number_id')]]['pre_rcv'];

					$exfac_qnty		= $ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]][$color[csf('size_number_id')]];

					$left_over_qty = 0;
					if($goods_type==2)
					{
						$left_over_qty = $pro_qnty;
					}
					else
					{
						$left_over_qty = $color[csf("order_quantity")];
					}

					// $left_over_qty = ($goods_type==2) ? ($pro_qnty-$pre_rcv_qnty)+$rcv_qnty : ($pro_qnty-($exfac_qnty+$pre_rcv_qnty))+$rcv_qnty;

					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" placeholder="'.($left_over_qty).'" style="width:100px"  onblur="fn_total_update('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rcv_qnty.'" ></td></tr>';	//placeholder="'.($left_over_qty).'"

				}
				else // subcontruct Order
				{
					$index = $color[csf("size_id")].$color_arr[$color[csf("color_id")]].$color[csf("color_id")];

					if( !in_array( $color[csf("color_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_id")].'span">+</span>'.$color_library[$color[csf("color_id")]].' : <span id="total_'.$color[csf("color_id")].'">'.$color_size_total_qnty_array[$color[csf("color_id")]].'</span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_id")].'">';
						$chkColor[] = $color[csf("color_id")];
					}

					$colorID .= $color[csf("size_id")]."*".$color[csf("color_id")].",";

					$pro_qnty 		= $color_size_qnty_array[$color[csf('id')]]['prodQty'];
					$rcv_qnty 		= $color_size_qnty_array[$color[csf('id')]]['rcv'];
					$pre_rcv_qnty 	= $color_size_qnty_array[$color[csf('id')]]['pre_rcv'];

					$exfac_qnty=$delivery_value[$color[csf('item_id')]][$color[csf('color_id')]][$color[csf('size_id')]];

					$left_over_qty = ($pro_qnty - ($exfac_qnty+$pre_rcv_qnty))+$rcv_qnty;


					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" title="'.$pro_qnty.' - ('.$exfac_qnty.'+'.$pre_rcv_qnty.')"   keypress="fn_total_update('.$color[csf("color_id")].','.($i+1).')" value="'.$rcv_qnty.'" ></td></tr>';//placeholder="'.($left_over_qty).'"
				}

			}
			else
			{

			}

			$i++;
		}
		if( $variableSettings==2 )
		{
			$colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="70">Qty</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>';
			//placeholder="'.$totalQnty.'"
		}
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";

		exit();
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		if(str_replace("'","",$txt_mst_id)=='')
		{
			$id=return_next_id("id", "pro_leftover_gmts_rcv_mst", 1);
		}
		else
		{
			$id=str_replace("'","",$txt_mst_id);
		}
		if($leftover_source==1)
		{
			if($goodsType==2) $prod_type_id="5,80,8";  else $prod_type_id="5";
		}else
		{
			$prod_type_id = 11;
		}
		// if($leftover_source==1) $prod_type_id="5,80,8"; else $prod_type_id="11";
		$dtls_id=return_next_id("id", "pro_leftover_gmts_rcv_dtls", 1);
		$color_size_id=return_next_id("id", "pro_leftover_gmts_rcv_clr_sz", 1);

		if($db_type==2) $mrr_cond="and  TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
		$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'LGR', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from pro_leftover_gmts_rcv_mst where company_id=$cbo_company_name $mrr_cond order by id DESC ", "sys_number_prefix", "sys_number_prefix_num" ));

		 $field_array_mst="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, location, leftover_date, order_type, buyer_name, store_name, exchange_rate, goods_type,working_company_id,working_location_id,working_floor_id,remarks, inserted_by, insert_date";

		 $field_array_dtls="id, mst_id, po_break_down_id, order_no, production_type, style_ref_no, item_number_id, country_id, total_left_over_receive, currency_id, fob_rate, leftover_amount, bdt_amount, room_no, rack_no, shelf_no, bin_no, sewing_production_variable,variable_country_maintain,leftover_source, style_order_wisw, variable_is_controll, user_lebel, remarks, inserted_by, insert_date,job_no,category_id";

		 $field_array_mst_update="store_name*remarks*updated_by*update_date";



		if($db_type==0)
		{
			if(str_replace("'","",$txt_mst_id)=='')
			{
				$data_array_mst="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$txt_leftover_date.",".$cbo_order_type.",".$cbo_buyer_name.",".$cbo_store_name.",".$exchange_rate.",".$cbo_goods_type.",".$cbo_working_company_name.",".$cbo_working_location_name.",".$cbo_working_floor_name.",".$txt_remark.",".$user_id.",'".$pc_date_time."')";

				$data_array_dtls="(".$dtls_id.",'".$id."',".$txt_po_id.",".$txt_order_no.",1,".$txt_style_name.",".$cbo_item_name.",".$cbo_country_name.",".$txt_total_left_over_receive.",".$cbo_currency.",".$txt_fob_rate.",".$txt_leftover_amount.",".$txt_bdt_amount.",".$cbo_room_no.",".$cbo_rack_no.",".$cbo_shelf_no.",".$cbo_bin_no.",".$sewing_production_variable.",".$country_maintain_variable.",".$leftover_source.",".$styleOrOrderWisw.",".$variable_is_controll.",".$txt_user_lebel.",".$txt_remark2.",".$user_id.",'".$pc_date_time."',".$hidden_job_no.",".$cbo_category_id.")";
			}
			else
			{
				$data_array_dtls="(".$dtls_id.",".$txt_mst_id.",".$txt_po_id.",".$txt_order_no.",1,".$txt_style_name.",".$cbo_item_name.",".$cbo_country_name.",".$txt_total_left_over_receive.",".$cbo_currency.",".$txt_fob_rate.",".$txt_leftover_amount.",".$txt_bdt_amount.",".$cbo_room_no.",".$cbo_rack_no.",".$cbo_shelf_no.",".$cbo_bin_no.",".$sewing_production_variable.",".$country_maintain_variable.",".$leftover_source.",".$styleOrOrderWisw.",".$variable_is_controll.",".$txt_user_lebel.",".$txt_remark2.",".$user_id.",'".$pc_date_time."',".$hidden_job_no.",".$cbo_category_id.")";
			}

		}
		else
		{
			if(str_replace("'","",$txt_mst_id)=='')
			{
				$data_array_mst="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$txt_leftover_date.",".$cbo_order_type.",".$cbo_buyer_name.",".$cbo_store_name.",".$exchange_rate.",".$cbo_goods_type.",".$cbo_working_company_name.",".$cbo_working_location_name.",".$cbo_working_floor_name.",".$txt_remark.",".$user_id.",'".$pc_date_time."')";

				$data_array_dtls="(".$dtls_id.",'".$id."',".$txt_po_id.",".$txt_order_no.",1,".$txt_style_name.",".$cbo_item_name.",".$cbo_country_name.",".$txt_total_left_over_receive.",".$cbo_currency.",".$txt_fob_rate.",".$txt_leftover_amount.",".$txt_bdt_amount.",".$cbo_room_no.",".$cbo_rack_no.",".$cbo_shelf_no.",".$cbo_bin_no.",".$sewing_production_variable.",".$country_maintain_variable.",".$leftover_source.",".$styleOrOrderWisw.",".$variable_is_controll.",".$txt_user_lebel.",".$txt_remark2.",".$user_id.",'".$pc_date_time."',".$hidden_job_no.",".$cbo_category_id.")";
			}
			else
			{

				$data_array_mst_update="".$cbo_store_name."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";

				$data_array_dtls="(".$dtls_id.",".$txt_mst_id.",".$txt_po_id.",".$txt_order_no.",1,".$txt_style_name.",".$cbo_item_name.",".$cbo_country_name.",".$txt_total_left_over_receive.",".$cbo_currency.",".$txt_fob_rate.",".$txt_leftover_amount.",".$txt_bdt_amount.",".$cbo_room_no.",".$cbo_rack_no.",".$cbo_shelf_no.",".$cbo_bin_no.",".$sewing_production_variable.",".$country_maintain_variable.",".$leftover_source.",".$styleOrOrderWisw.",".$variable_is_controll.",".$txt_user_lebel.",".$txt_remark2.",".$user_id.",'".$pc_date_time."',".$hidden_job_no.",".$cbo_category_id.")";
			}
		}

		$country_cond = (str_replace("'", "", $country_maintain_variable)==1) ? " and b.country_id=$cbo_country_name" : "";
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
								sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
								sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as cur_production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b
								where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name $country_cond and a.color_size_break_down_id!=0 and a.production_type in(5,7)
								group by a.color_size_break_down_id");
		$color_pord_data=array();
		foreach($dtlsData as $row)
		{
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}

		$field_array_color_size="id, mst_id, dtls_id, production_type, color_size_break_down_id, production_qnty";
		//============================================== color wise =================================
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			if(str_replace("'","",$cbo_order_type)==1)	//self order
			{
				if(str_replace("'","",$cbo_item_name)!=0)
				{
					$item_cond = " and item_number_id=$cbo_item_name";
				}
				$country_cond = (str_replace("'", "", $country_maintain_variable)==1) ? " and country_id=$cbo_country_name" : "";
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_po_id  $country_cond $item_cond and status_active in(1,2,3) and is_deleted=0 order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$colSizeID_arr[$val[csf("color_number_id")]]=$val[csf("id")];
				}
			}
			// print_r($colSizeID_arr);die();
 			$rowEx = explode("**",$colorIDvalue);
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array_color_size="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);

				$colorID = $colorAndSizeAndValue_arr[0];
				$colorSizeValue = $colorAndSizeAndValue_arr[1];
				$index = $colorID;

				if($j==0)$data_array_color_size = "(".$color_size_id.",".$id.",".$dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array_color_size .= ",(".$color_size_id.",".$id.",".$dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$color_size_id=$color_size_id+1;
 				$j++;
			}
 		}//color level wise

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{

			if(str_replace("'","",$cbo_order_type)==1)	//self order
			{
				if(str_replace("'","",$cbo_item_name)!=0)
				{
					$item_cond = " and item_number_id=$cbo_item_name";
				}
				$country_cond = (str_replace("'", "", $country_maintain_variable)==1) ? " and country_id=$cbo_country_name" : "";
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_po_id $country_cond  $item_cond and status_active in(1,2,3) and is_deleted=0  order by size_number_id,color_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
			}
			else	// Subcontruct order
			{

				$color_sizeID_arr=sql_select( "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm, embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part
				from subcon_ord_breakdown
				where order_id=$txt_po_id and item_id=$cbo_item_name");
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_id")].$color_arr[$val[csf("color_id")]].$val[csf("color_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
			}



			$data_array_color_size="";
			$j=0;
 			$rowEx = explode("***",$colorIDvalue);

			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);

				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;

				if($j==0)$data_array_color_size = "(".$color_size_id.",".$id.",".$dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array_color_size .= ",(".$color_size_id.",".$id.",".$dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$color_size_id=$color_size_id+1;
 				$j++;
			}
		}

		$rID=$rID1=$rID2=$rID3=1;

		if(str_replace("'","",$txt_mst_id)==''){
			$rID=sql_insert("pro_leftover_gmts_rcv_mst",$field_array_mst,$data_array_mst,1);
		}

		if($data_array_dtls!=''){
			$rID1=sql_insert("pro_leftover_gmts_rcv_dtls",$field_array_dtls,$data_array_dtls,1);
		}

		if($data_array_color_size!=''){
			$rID2=sql_insert("pro_leftover_gmts_rcv_clr_sz",$field_array_color_size,$data_array_color_size,1);
		}


		if($data_array_mst_update!=''){
			$rID3=sql_update("pro_leftover_gmts_rcv_mst",$field_array_mst_update,$data_array_mst_update,"id","".$txt_mst_id."",1);
		}



		if(str_replace("'","",$txt_mst_id)!=""){
			$mstID = str_replace("'","",$txt_mst_id);
			$system_no = str_replace("'","",$txt_system_no);
		}else{
			$mstID = str_replace("'","",$id);
			$system_no = str_replace("'","",$new_sys_number[0]);
		}

		// echo "10** insert into pro_leftover_gmts_rcv_mst ($field_array_mst) values $data_array_mst";die;
		//echo "10** insert into pro_leftover_gmts_rcv_dtls ($field_array_dtls) values $data_array_dtls";die;
		//echo "10** insert into pro_leftover_gmts_rcv_clr_sz ($field_array_color_size) values $data_array_color_size";die;


		// echo "10**".$rID."**".$rID1."**".$rID2."**".$mstID."**".$system_no;die;



		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $rID1 && $rID2 && $rID3)
				{
					mysql_query("COMMIT");
					echo "0**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
			}
			else
			{
				if($rID && $rID1 && $rID2 && $rID3)
				{
					mysql_query("COMMIT");
					echo "0**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $rID1 && $rID2 && $rID3)
				{
					oci_commit($con);
					echo "0**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
			}
			else
			{
				if($rID && $rID1 && $rID2 && $rID3)
				{
					oci_commit($con);
					echo "0**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
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

		 //"id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, location, leftover_date, order_type, buyer_name, store_name, floor_id, exchange_rate, goods_type, remarks, inserted_by, insert_date";

		 $field_array_mst_update="working_company_id*working_location_id*working_floor_id*remarks*updated_by*update_date";
		 $data_array_mst_update="".$cbo_working_company_name."*".$cbo_working_location_name."*".$cbo_working_floor_name."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";




		 $field_array_dtls_update="total_left_over_receive*leftover_amount*bdt_amount*room_no*rack_no*shelf_no*bin_no*remarks*category_id*updated_by*update_date";
		 $data_array_dtls_update="".$txt_total_left_over_receive."*".$txt_leftover_amount."*".$txt_bdt_amount."*".$cbo_room_no."*".$cbo_rack_no."*".$cbo_shelf_no."*".$cbo_bin_no."*".$txt_remark2."*".$cbo_category_id."*".$user_id."*'".$pc_date_time."'";




 		$rID=sql_update("pro_leftover_gmts_rcv_mst",$field_array_mst_update,$data_array_mst_update,"id","".$txt_mst_id."",1);
		$rID1=sql_update("pro_leftover_gmts_rcv_dtls",$field_array_dtls_update,$data_array_dtls_update,"id","".$hidden_dtls_id."",1);


		$color_size_id=return_next_id("id", "pro_leftover_gmts_rcv_clr_sz", 1);
		$field_array_color_size="id, mst_id, dtls_id, production_type, color_size_break_down_id, production_qnty";


		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			if(str_replace("'","",$cbo_order_type)==1)
			{

				if(str_replace("'","",$cbo_item_name)!=0)//color and size wise
				{
					$item_cond = " and item_number_id=$cbo_item_name";
				}
				$country_cond = (str_replace("'", "", $country_maintain_variable)==1) ? " and country_id=$cbo_country_name" : "";
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_po_id  $country_cond $item_cond and status_active in(1,2,3) and is_deleted=0  order by size_number_id,color_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$colSizeID_arr[$val[csf("color_number_id")]]=$val[csf("id")];
				}
			}
			else
			{
				$color_sizeID_arr=sql_select( "select id, color_id  from subcon_ord_breakdown
				where order_id=$txt_po_id and item_id=$cbo_item_name");
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$colSizeID_arr[$val[csf("color_id")]]=$val[csf("id")];
				}
			}

 			$data_array_color_size="";
			$j=0;
 			$rowEx = explode("**",$colorIDvalue);

			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);

				$colorID = $colorAndSizeAndValue_arr[0];
				$colorSizeValue = $colorAndSizeAndValue_arr[1];
				$index = $colorID;

				if($j==0)$data_array_color_size = "(".$color_size_id.",".$txt_mst_id.",".$hidden_dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array_color_size .= ",(".$color_size_id.",".$txt_mst_id.",".$hidden_dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$color_size_id=$color_size_id+1;
 				$j++;
			}
 		}//color level wise


		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{

			if(str_replace("'","",$cbo_order_type)==1)
			{

				if(str_replace("'","",$cbo_item_name)!=0)//color and size wise
				{
					$item_cond = " and item_number_id=$cbo_item_name";
				}
				$country_cond = (str_replace("'", "", $country_maintain_variable)==1) ? " and country_id=$cbo_country_name" : "";
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_po_id  $country_cond $item_cond and status_active in(1,2,3) and is_deleted=0  order by size_number_id,color_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
			}
			else
			{
				$color_sizeID_arr=sql_select( "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm, embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part
				from subcon_ord_breakdown
				where order_id=$txt_po_id and item_id=$cbo_item_name");
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_id")].$color_arr[$val[csf("color_id")]].$val[csf("color_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
			}





			$data_array_color_size="";
			$j=0;
 			$rowEx = explode("***",$colorIDvalue);

			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);

				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;

				if($j==0)$data_array_color_size = "(".$color_size_id.",".$txt_mst_id.",".$hidden_dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array_color_size .= ",(".$color_size_id.",".$txt_mst_id.",".$hidden_dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$color_size_id=$color_size_id+1;
 				$j++;
			}
		}

		$dtlsrDelete = execute_query("delete from pro_leftover_gmts_rcv_clr_sz where dtls_id=$hidden_dtls_id",1);
		$rID2=1;
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$hidden_dtls_id)!='')
		{
			$rID2=sql_insert("pro_leftover_gmts_rcv_clr_sz",$field_array_color_size,$data_array_color_size,1);
			//echo "10** insert into pro_leftover_gmts_rcv_clr_sz ($field_array_color_size) values $data_array_color_size";die;
		}


		//echo "10**".$rID."**".$rID1."**".$rID2."**".$dtlsrDelete;die;

		if(str_replace("'","",$txt_mst_id)!=""){
			$mstID = str_replace("'","",$txt_mst_id);
			$system_no = str_replace("'","",$txt_system_no);
		}else{
			$mstID = str_replace("'","",$id);
			$system_no = str_replace("'","",$new_sys_number[0]);
		}

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $rID1 && $rID2 && $dtlsrDelete)
				{
					mysql_query("COMMIT");
					echo "1**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
			}else{
				if($rID && $rID1 && $rID2 && $dtlsrDelete)
				{
					mysql_query("COMMIT");
					echo "1**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
				else
				{
					mysql_query("ROLLBACK");
					//echo "10**".str_replace("'","",$hidden_po_break_down_id);
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $rID1 && $rID2 && $dtlsrDelete)
				{
					oci_commit($con);
					echo "1**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
			}
			else
			{
				if($rID && $rID1 && $rID2 && $dtlsrDelete)
				{
					oci_commit($con);
					echo "1**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name)."**".$txt_po_id;
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

		$poID = str_replace("'","",$hidden_po_break_down_id);
		$countryID = str_replace("'","",$cbo_country_name);
		$itemID = str_replace("'","",$cbo_item_name);
		$IssueData = sql_select("SELECT sum(total_issue) as issue_qty from pro_leftover_gmts_issue_dtls where po_break_down_id=$poID and country_id=$countryID and item_number_id=$itemID and status_active=1 and is_deleted=0") ;
		if($IssueData[0]['ISSUE_QTY'] >0)
		{
			echo "3**";disconnect($con);die();
		}

 		$rID = sql_delete("pro_leftover_gmts_rcv_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$rID1 = sql_delete("pro_leftover_gmts_rcv_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id ',$txt_mst_id,1);

		$rID2 = sql_delete("pro_leftover_gmts_rcv_clr_sz","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);

 		if($db_type==0)
		{
			if($rID && $rID1 && $rID2)
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
			if($rID && $rID1 && $rID2)
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


if($action=="left_over_gmts_receive_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);

	$rowCount = $data[5];
	$leftOverRcvArr = array();

	for ($i=1; $i < $rowCount; $i++) {
		$leftOverRcvArr[] = $data[5 + $i];
	}

	echo load_html_head_contents("Left Over Receive Info","../", 1, 1, $unicode,'','');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$buyer_library_arr=return_library_array( "select id, BUYER_NAME from   lib_buyer", "id", "BUYER_NAME"  );
	$location_library=return_library_array( "select id,location_name from  lib_location", "id","location_name"  );
	$store_name_library=return_library_array( "select id,store_name from lib_store_location", "id","store_name"  );
	$floor_name_library=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0", "id","floor_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	?>
	<div style="width:1010px;">
	    <table width="1000" cellspacing="0" align="right" style="margin-bottom:20px;">
	        <tr>
	            <td rowspan="3" align="center"><img src="../<? echo $image_location; ?>" height="50" width="60"></td>
	            <td colspan="6" align="center"  style="font-size:xx-large; "><strong><? echo $company_library[$data[0]]; ?></strong></td>
	            <td rowspan="3" width="60" >&nbsp;</td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="6" align="center" style="font-size:14px;">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
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
	            <td colspan="6" style="font-size:x-large; text-align:center;"><strong><? echo $data[2]; ?></strong></td>
	        </tr>
	         <tr>
	            <td colspan="8" style="text-align:center;"><strong>&nbsp;</strong></td>
	        </tr>
	        <?

			//$sql_master ="select a.id,a.party_id,b.order_id,a.challan_no,b.item_id,a.delivery_date,a.transport_company,a.vehical_no,a.location_id,a.company_id,b.process_id,b.total_carton_qnty,b.delivery_qty  from  subcon_delivery_mst a,  subcon_delivery_dtls b where a.id=b.mst_id and  a.delivery_no='".$data[1]."' and a.status_active=1 and a.is_deleted=0 order by a.id";
			//$result_master=sql_select($sql_master);
			//$poID=$result_master[0][csf("order_id")];

			//$sql_wo_po_break =sql_select("select a.id,a.order_no as po_number,a.job_no_mst from subcon_ord_dtls a where a.id=$poID");
			//foreach($sql_wo_po_break as $poData)
			//{
			//	$po_data_arr[$poData[csf("id")]]["po_number"]=$poData[csf("po_number")];
			//	$po_data_arr[$poData[csf("id")]]["job_no_mst"]=$poData[csf("job_no_mst")];
			//}

			$sql_mst = "select a.id, a.sys_number, a.company_id, a.location, a.leftover_date, a.order_type, a.buyer_name, a.store_name, a.working_floor_id, a.exchange_rate, a.garments_nature, a.goods_type, a.remarks as mst_remarks,a.working_company_id
			from pro_leftover_gmts_rcv_mst a
			where a.company_id='$data[0]' and a.id=$data[1]  and a.status_active=1 and a.is_deleted=0";
			//  echo $sql_mst;
			$result_mst=sql_select($sql_mst);

			$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
			$variable_settings = sql_select("SELECT SEWING_PRODUCTION_VARIABLE from pro_leftover_gmts_rcv_dtls where mst_id=$data[1]");
			$variable_settings_production = $variable_settings[0]['SEWING_PRODUCTION_VARIABLE'];
			?>
	        <tr style="font-size:12px;">
	        	<td width="110" valign=""><strong>Buyer Name</strong></td>
	            <td width="200" valign=""><strong>: </strong><? echo $buyer_library_arr[$result_mst[0][csf("buyer_name")]]; ?></td>
	            <td width="110" valign=""><strong>Goods Type</strong></td>
	            <td width="200" valign=""><strong>: </strong><? echo $goods_type_arr[$result_mst[0][csf("goods_type")]]; ?></td>
	            <td width="110" valign=""><strong>System ID</strong></td>
	            <td width="200" valign=""><strong>: </strong><? echo $result_mst[0][csf("sys_number")]; ?></td>
	            <td width="110" valign=""><strong>Receive Date</strong></td>
	            <td width="200" valign=""><strong>: </strong><? echo change_date_format($result_mst[0][csf("leftover_date")]); ?></td>
	        </tr>
	        <tr style="font-size:12px;">
	            <td width="" valign=""><strong>Store Name</strong></td>
	            <td width="" valign=""><strong>: </strong><? echo $store_name_library[$result_mst[0][csf("store_name")]]; ?></td>
	            <td width="" valign=""><strong>Floor</strong></td>
	            <td width="" valign=""><strong>: </strong><? echo $floor_name_library[$result_mst[0][csf("working_floor_id")]]; ?></td>
	            <td width="" valign=""><strong>Order Type</strong></td>
	            <td width="" valign=""><strong>: </strong><? echo $order_source[$result_mst[0][csf("order_type")]]; ?></td>
	            <td width="" valign=""><strong>Exchange Rate</strong></td>
	            <td width="" valign=""><strong>: </strong><? echo $result_mst[0][csf("exchange_rate")]; ?></td>
	        </tr>
	        <tr style="font-size:12px;">
	            <td><strong>Remarks</strong></td>
	            <td ><strong>: </strong><? echo $result_mst[0][csf("mst_remarks")]; ?> </td>
				<td><strong>Working Company</strong></td>
	            <td colspan="5"><strong>: </strong><? echo $company_library[$result_mst[0]["WORKING_COMPANY_ID"]]; ?> </td>

	        </tr>
	    </table>
	    <br>
	    <table align="right" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="100">JOb No</th>
	            <th width="100">Order</th>
	            <th width="100">Style</th>
	            <th width="100">IR/IB</th>
	            <th width="100">Items</th>
	            <? if($variable_settings_production !=1){?>
	            <th width="120">Color</th>
	            <th width="30">Size</th>
	            <? } ?>
	            <th width="80" style="word-break:normal;">Left Over Receive Qty</th>
	            <th width="50">Currency</th>
	            <th width="30">FOB Rate</th>
	            <th width="30">Amount</th>
	            <th width="30">Room</th>
	            <th width="30">Rack</th>
	            <th width="30">Shelf</th>
	            <th width="30">Bin</th>
	            <th width="">Remarks</th>
	        </thead>
	        <tbody style="font-size:12px;">
			<?
			$orderJobArray = array();
			if($data[4]==1)	// Self Order
			{
				if($variable_settings_production==1)
				{

					$sql_dtls="SELECT b.id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive as production_qnty, b.currency_id, b.fob_rate, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.style_order_wisw, b.remarks, d.color_number_id, d.size_number_id,e.grouping, e.job_no_mst from pro_leftover_gmts_rcv_dtls b, wo_po_color_size_breakdown d,wo_po_break_down e where b.mst_id='$data[1]' and b.po_break_down_id=d.po_break_down_id and d.po_break_down_id=e.id and b.status_active=1 and b.is_deleted=0 order by b.order_no, b.style_ref_no, b.item_number_id, d.color_number_id"; //die;
				}
				else
				{

					$sql_dtls="SELECT b.id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.style_order_wisw, b.remarks, c.production_qnty, c.color_size_break_down_id, d.color_number_id, d.size_number_id,e.grouping, e.job_no_mst from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d,wo_po_break_down e where b.mst_id='$data[1]'  and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id  and b.id = c.dtls_id and b.status_active=1 and b.is_deleted=0 order by b.order_no, b.style_ref_no, b.item_number_id, d.color_number_id"; //die;
				}
				// echo $sql_dtls;
				$result_dtls=sql_select($sql_dtls);
				foreach($result_dtls as $row)
				{
					$orderJobArray[$row['ORDER_NO']] = $row['JOB_NO_MST'];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["production_qnty"] += $row[csf("production_qnty")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["fob_rate"] = $row[csf("fob_rate")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["bdt_amount"] = $row[csf("bdt_amount")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["room_no"] = $row[csf("room_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["rack_no"] = $row[csf("rack_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["shelf_no"] = $row[csf("shelf_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["bin_no"] = $row[csf("bin_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["remarks"] = $row[csf("remarks")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["currency_id"] = $row[csf("currency_id")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["grouping"] = $row[csf("grouping")];


					/*$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("id")]]["production_qnty"] += $row[csf("production_qnty")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("id")]]["fob_rate"] = $row[csf("fob_rate")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("id")]]["bdt_amount"] = $row[csf("bdt_amount")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("id")]]["room_no"] = $row[csf("room_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("id")]]["rack_no"] = $row[csf("rack_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("id")]]["shelf_no"] = $row[csf("shelf_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("id")]]["bin_no"] = $row[csf("bin_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("id")]]["remarks"] = $row[csf("remarks")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("id")]]["currency_id"] = $row[csf("currency_id")];*/
				}
			}
			else	// Subcontruct Order
			{
				 $sql_dtls="SELECT b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.style_order_wisw, b.remarks, c.production_qnty, c.color_size_break_down_id, d.color_id, d.size_id , e.grouping, e.job_no_mst from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, subcon_ord_breakdown d, wo_po_break_down e where b.mst_id='$data[1]'  and c.color_size_break_down_id=d.id and b.po_break_down_id=e.id and b.id = c.dtls_id and b.status_active=1 and b.is_deleted=0 order by b.order_no, b.style_ref_no, b.item_number_id, d.color_id"; //die;
				$result_dtls=sql_select($sql_dtls);
				foreach($result_dtls as $row)
				{
					$orderJobArray[$row['ORDER_NO']] = $row['JOB_NO_MST'];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["production_qnty"] += $row[csf("production_qnty")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["fob_rate"] = $row[csf("fob_rate")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["bdt_amount"] = $row[csf("bdt_amount")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["room_no"] = $row[csf("room_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["rack_no"] = $row[csf("rack_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["shelf_no"] = $row[csf("shelf_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["bin_no"] = $row[csf("bin_no")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["remarks"] = $row[csf("remarks")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["currency_id"] = $row[csf("currency_id")];
					$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["grouping"] = $row[csf("grouping")];
				}
			}

			//	echo "<pre>";
			//	print_r($report_data_arr);
	        $i=1;
	       // $tot_qnty=array();
	       $tot_qnty=0;
		   $total_amount=0;

	       if($data[4]==1)	// Self Order
			{
				foreach($report_data_arr as $order_no=>$order_data)
					{
						foreach($order_data as $style_no=>$style_data)
						{
							foreach($style_data as $item_id=>$itme_data)
							{
								$rowCount = 0;
								foreach($itme_data as $color_id=>$color_data)
								{
									foreach($color_data as $size_id=>$size_data)
									{
										if ($i%2==0)   $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

										$tot_qnty += $size_data["production_qnty"];
										//$amount = $leftOverRcvArr[$rowCount] * $size_data["fob_rate"];
										$amount =$size_data["production_qnty"] * $size_data["fob_rate"];
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td><? echo $orderJobArray[$order_no];  ?></td>
											<td><? echo $order_no;  ?></td>
											<td><? echo $style_no; ?></td>
											<td><? echo $size_data["grouping"]; ?></td>
											<td><? echo $garments_item[$item_id]; ?></td>
											<? if($variable_settings_production !=1){?>
											<td><? echo $color_library[$color_id]; ?></td>
											<td><? echo $size_library[$size_id]; ?></td>
											<?}?>
											<td align="right"><? echo $size_data["production_qnty"]; ?></td>
											<td><? echo $currency[$size_data["currency_id"]]; ?></td>
											<td align="right"><? echo $size_data["fob_rate"]; ?></td>
											<td align="right"><? echo $amount; ?></td>
											<td><? echo $size_data["room_no"]; ?></td>
											<td><? echo $size_data["rack_no"]; ?></td>
											<td><? echo $size_data["shelf_no"]; ?></td>
											<td><? echo $size_data["bin_no"]; ?></td>
											<td><? echo $size_data["remarks"]; ?></td>
										</tr>
										<?
										$rowCount++;
										$total_amount+=$amount;
										$i++;
									}
								}
							}
						}
					}
			} else {		// Subcontruct Order
				foreach($report_data_arr as $order_no=>$order_data)
				{
					foreach($order_data as $style_no=>$style_data)
					{
						foreach($style_data as $item_id=>$itme_data)
						{
							foreach($itme_data as $color_id=>$color_data)
							{
								foreach($color_data as $size_id=>$size_data)
								{
									if ($i%2==0)   $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									$tot_qnty += $size_data["production_qnty"];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td><? echo $orderJobArray[$order_no];;  ?></td>
										<td><? echo $order_no;  ?></td>
										<td><? echo $style_no; ?></td>
										<td><? echo $size_data["grouping"]; ?></td>
										<td><? echo $garments_item[$item_id]; ?></td>
										<? if($variable_settings_production !=1){?>
										<td><? echo $color_library[$color_id]; ?></td>
										<td><? echo $size_library[$size_id]; ?></td>
										<?}?>
										<td><? echo $size_data["production_qnty"]; ?></td>
		                                <td><? echo $currency[$size_data["currency_id"]]; ?></td>
										<td><? echo $size_data["fob_rate"]; ?></td>
										<td><? echo $size_data["bdt_amount"]; ?></td>
										<td><? echo $size_data["room_no"]; ?></td>
										<td><? echo $size_data["rack_no"]; ?></td>
										<td><? echo $size_data["shelf_no"]; ?></td>
										<td><? echo $size_data["bin_no"]; ?></td>
										<td><? echo $size_data["remarks"]; ?></td>
									</tr>
									<?
									$i++;
								}
							}
						}
					}
				}
			}
	        ?>
	        <tr>
	        	<td colspan="<? if($variable_settings_production !=1){ echo 7;}else{ echo 3;}?>" align="right">Total :</td>
				<td align="right"><? echo $tot_qnty;  ?></td>
				<td colspan="2"></td>
				<td align="right"><? echo $total_amount;  ?></td>
			</tr>
	        </tbody>
	    </table>
	    <?
		echo signature_table(179, $data[0], "1000px","","10");
		?>
	</div>
	<?
	exit();
}
?>
