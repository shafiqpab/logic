<?
session_start();
include('../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select iron_update,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("iron_update")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
		
	}
	
	echo "$('#iron_production_variable_rej').val(0);\n";
	$sql_result_rej = sql_select("select iron_update from variable_settings_production where company_name=$data and variable_list=28 and status_active=1");
 	foreach($sql_result_rej as $result)
	{
		
		echo "$('#iron_production_variable_rej').val(".$result[csf("iron_update")].");\n";
		if($result[csf("iron_update")]==3)
		{
				echo "$('#txt_reject_qnty').attr('readonly','readonly');\n";
		}
		else
		{
			echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";	
		}
	}
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=30","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";
 	exit();
}

 
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/iron_output_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );     	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (10) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down('requires/iron_output_controller', document.getElementById('cbo_iron_company').value+'_'+document.getElementById('cbo_location').value+'_'+this.value, 'load_drop_down_table', 'table_td' );",0 );     	 
}

if ($action=='load_drop_down_table')
{
	$data=explode("_",$data);
	$company_id = $data[0];
	$location_id = $data[1];
	$floor_id = $data[2];
	echo create_drop_down( 'cbo_table_name', 172, "select id, table_name from lib_table_entry where table_type=2 and company_name=$company_id and location_name=$location_id and floor_name=$floor_id and is_deleted=0 and status_active=1 order by table_name", 'id,table_name', 1, '-- Select --', $selected, '', 0 );
}


if($action=="load_drop_down_source")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	
	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_iron_company", 170, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "fnc_workorder_search(this.value);",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_iron_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "fnc_workorder_search(this.value);" );
		}
	}
	else if($data==1)
 		echo create_drop_down( "cbo_iron_company", 170, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/iron_output_controller', this.value, 'load_drop_down_location', 'location_td' );fnc_company_check(document.getElementById('cbo_source').value);",0,0 ); 
 	else
		echo create_drop_down( "cbo_iron_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );	
			
	exit();
}


if ($action=="load_drop_down_workorder")
{
	$explode_data = explode("_",$data);
	
	$sql = "SELECT a.id,a.sys_number from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=".$explode_data[2]." and a.company_id=$explode_data[0]  and a.rate_for=35 and a.service_provider_id=$explode_data[1]   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number order by a.id"; 
	//echo $sql;
	echo create_drop_down( "cbo_work_order", 170, $sql,"id,sys_number", 1, "-- Select Work Order --", $selected, "fnc_workorder_rate('$data',this.value)",0 );
}

if($action=="populate_workorder_rate")
{
	$data=explode("_",$data);
	$po_break_down_id=$data[2];
	$company_id=$data[0];
	$suppplier=$data[1];
	$sql = sql_select("SELECT a.id,a.sys_number,a.currence,a.exchange_rate,sum(b.avg_rate) as rate,b.uom from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=".$po_break_down_id." and a.company_id=$company_id and a.service_provider_id=$suppplier and a.rate_for=35   and a.id=".$data[3]." and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number,a.currence ,a.exchange_rate,b.uom order by a.id"); 
	//echo $sql;
	if($sql[0][csf('uom')]==2) 
	{
		$rate=$sql[0][csf('rate')]/12;
	}
	else
	{
		$rate=$sql[0][csf('rate')];
	}
	echo "$('#workorder_rate_id').text('');\n";
	echo "$('#hidden_currency_id').val('".$sql[0][csf('currence')]."');\n";
	echo "$('#hidden_exchange_rate').val('".$sql[0][csf('exchange_rate')]."');\n";
	echo "$('#hidden_piece_rate').val('".$rate."');\n";
	$rate_string='';
	$rate_string=$rate." ".$currency[$sql[0][csf('currence')]];
	if(trim($rate_string)!="") 
	{
		$rate_string="Work Order Rate ".$rate_string." /Pcs";
		echo "$('#workorder_rate_id').text('".$rate_string."');\n";
	}
	
	//echo "$('#workorder_rate_td').text('".$rate."');\n";
	//echo "$('#txt_style_no').val('".$sql[0][csf('style_ref_no')]."');\n";
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
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==4)
			{
				document.getElementById('search_by_th_up').innerHTML="Actual PO No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==5)
			{
				document.getElementById('search_by_th_up').innerHTML="File No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==6)
			{
				document.getElementById('search_by_th_up').innerHTML="Internal Ref.";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==7)
			{
				document.getElementById('search_by_th_up').innerHTML="Booking No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
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
            <table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                 <thead>   
                    <tr>
                        <th colspan="5"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'',1, "-- Select --",4, "",0);?></th>
                    </tr>             	 
                    <th width="130">Search By</th>
                    <th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                    <th width="200">Date Range</th>
                    <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                </thead>
                <tr class="general">
                    <td width="130"> 
                        <?
                            $searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
                            $searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref.",7=>"Booking No");
                            echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select --", $selected, "search_populate(this.value)",0 );
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
                        <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+<? echo $production_company; ?>+'_'+<? echo $hidden_variable_cntl; ?>+'_'+<? echo $hidden_preceding_process; ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'iron_output_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="middle" colspan="5">
                    <?=load_month_buttons(1); ?>
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
 	$production_company = $ex_data[6];
 	$variable_cntl = $ex_data[7];
	$preceding_process = $ex_data[8];
	$cbo_year = $ex_data[9];
	$search_type =$ex_data[10];

	if($txt_search_common=="" && $txt_date_from=="" && $txt_date_to=="")
	{
		echo "<div style='color:red;font-size:20px;font-weight:bold;text-align:center;'>Please enter anyone of search field value.</div>";
		die();
	}
	if($txt_date_from !="" && $txt_date_to !="")
	{
		$tot_days = datediff('d',$txt_date_from,$txt_date_to);
		if($tot_days>93)// max 3 month
		{
			echo "<div style='color:red;font-size:16px;font-weight:bold;text-align:center;'>Invalid Date Range.</div>"; die;
		}
	}

	//echo $cbo_year;//die("sumon");
	$qty_source=0;
	if($preceding_process==28) $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry
	else if($preceding_process==312) $qty_source=85;//Finishing Receive Entry 	
	// default source for Iron from sewing output
 	$sql_cond="";
	 if ($search_type==4){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
			{
				$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
			}
			else if(trim($txt_search_by)==1)
			{
				$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
			}
			else if(trim($txt_search_by)==2)
			{
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			}
			else if(trim($txt_search_by)==3)
			{
				$sql_cond = " and a.job_no like '%".trim($txt_search_common)."'";
			}
			else if(trim($txt_search_by)==4)
			{
				$sql_cond = " and c.acc_po_no like '%".trim($txt_search_common)."%'";
			}
			else if(trim($txt_search_by)==5)
			{
				$sql_cond = " and b.file_no like '%".trim($txt_search_common)."%'";
			}
			else if(trim($txt_search_by)==6)
			{
				$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";
			}
			else if(trim($txt_search_by)==7)
			{
				$order_id_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id from WO_BOOKING_DTLS where upper(booking_no) like upper('%".trim($txt_search_common)."%') and booking_type=1 and is_short=2", "po_break_down_id", "po_break_down_id");
				$sql_cond = where_con_using_array($order_id_arr,0,"b.id");
			}
		}
	}
	else if ($search_type==1){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
			{
				$sql_cond = " and b.po_number ='$txt_search_common'";
			}	
			else if(trim($txt_search_by)==1)
			{
				$sql_cond = " and a.style_ref_no ='$txt_search_common'";
			}	
			else if(trim($txt_search_by)==2)
			{
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			}	
			else if(trim($txt_search_by)==3)
			{
				$sql_cond = " and a.job_no='$txt_search_common'";
			}	
			else if(trim($txt_search_by)==4)
			{
				$sql_cond = " and c.acc_po_no='$txt_search_common'";
			}	
			else if(trim($txt_search_by)==5)
			{
				$sql_cond = " and b.file_no='$txt_search_common'";
			}	
			else if(trim($txt_search_by)==6)
			{
				$sql_cond = " and b.grouping='$txt_search_common'";
			}
			else if(trim($txt_search_by)==7)  
			{
				$order_id_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id from WO_BOOKING_DTLS where booking_no='$txt_search_common' and booking_type=1 and is_short=2", "po_break_down_id", "po_break_down_id");
				$sql_cond = where_con_using_array($order_id_arr,0,"b.id");
			}	
		}
	}
	else if ($search_type==2){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
			{
				$sql_cond = " and b.po_number like '".trim($txt_search_common)."%'";
			}	
			else if(trim($txt_search_by)==1)
			{
				$sql_cond = " and a.style_ref_no like '".trim($txt_search_common)."%'";
			}	
			else if(trim($txt_search_by)==2)
			{
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			}	
			else if(trim($txt_search_by)==3)
			{
				$sql_cond = " and a.job_no like '".trim($txt_search_common)."'";
			}	
			else if(trim($txt_search_by)==4)
			{
				$sql_cond = " and c.acc_po_no like '".trim($txt_search_common)."%'";
			}	
			else if(trim($txt_search_by)==5)
			{
				$sql_cond = " and b.file_no like '".trim($txt_search_common)."%'";
			}	
			else if(trim($txt_search_by)==6)
			{
				$sql_cond = " and b.grouping like '".trim($txt_search_common)."%'";
			}
			else if(trim($txt_search_by)==7) 
			{
				$order_id_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id from WO_BOOKING_DTLS where upper(booking_no) like upper('".trim($txt_search_common)."%') and booking_type=1 and is_short=2", "po_break_down_id", "po_break_down_id");
				$sql_cond = where_con_using_array($order_id_arr,0,"b.id");
			}	
		}
	}
	else if ($search_type==3){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
			{
				$sql_cond = " and b.po_number like '%".trim($txt_search_common)."'";
			}	
			else if(trim($txt_search_by)==1)
			{
				$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."'";
			}	
			else if(trim($txt_search_by)==2)
			{
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			}	
			else if(trim($txt_search_by)==3)
			{
				$sql_cond = " and a.job_no like '%".trim($txt_search_common)."'";
			}	
			else if(trim($txt_search_by)==4)
			{
				$sql_cond = " and c.acc_po_no like '%".trim($txt_search_common)."'";
			}	
			else if(trim($txt_search_by)==5)
			{
				$sql_cond = " and b.file_no like '%".trim($txt_search_common)."'";
			}	
			else if(trim($txt_search_by)==6)
			{
				$sql_cond = " and b.grouping like '%".trim($txt_search_common)."'";
			}
			else if(trim($txt_search_by)==7) 
			{
				$order_id_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id from WO_BOOKING_DTLS where upper(booking_no) like upper('%".trim($txt_search_common)."') and booking_type=1 and is_short=2", "po_break_down_id", "po_break_down_id");
				$sql_cond = where_con_using_array($order_id_arr,0,"b.id");
			}	
		}
	}
	if($txt_date_from!="" || $txt_date_to!="") 
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}
	
	/*if($cbo_year !=""){
		$sql_cond .= " and extract(year from (a.insert_date)) = $cbo_year";	
	}*/

	$qty_source_cond="";
	if($qty_source!=0)
	{
		$qty_source_cond="and b.id in(SELECT po_break_down_id from pro_garments_production_mst where production_type='$qty_source' and status_active=1 and is_deleted=0)";
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
	if($cbo_year !=0)
	{
		if($db_type==0) { $sql_shipment_year_cond=" and YEAR(b.insert_date)=$cbo_year";   }
		if($db_type==2) {$sql_shipment_year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year";}
	}

	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$company");
    $projected_po_cond = ($is_projected_po_allow==2) ? " and b.is_confirmed=1" : "";
	
	if(trim($txt_search_by)==4 && trim($txt_search_common)!="")
	{
		$sql = "SELECT b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.file_no,b.grouping,b.po_quantity,b.plan_cut, a.job_no
			from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
			where
			a.id = b.job_id and
			b.id=c.po_break_down_id and
			b.shiping_status!=3 and 
			a.status_active=1 and 
			a.is_deleted=0 and
			b.status_active=1 and 
			b.is_deleted=0 and
			c.status_active=1 and 
			c.is_deleted=0 and
			a.garments_nature=$garments_nature
			$sql_cond $qty_source_cond $sql_shipment_year_cond $projected_po_cond group by b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.file_no,b.grouping,b.po_quantity,b.plan_cut order by b.shipment_date desc";
	}
	else
	{	
 		$sql = "SELECT b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.file_no,b.grouping,b.po_quantity ,b.plan_cut, a.job_no
			from wo_po_details_master a, wo_po_break_down b 
			where
			a.id = b.job_id and
			b.shiping_status!=3 and 
			a.status_active=1 and 
			a.is_deleted=0 and
			b.status_active=1 and 
			b.is_deleted=0 and
			a.garments_nature=$garments_nature
			$sql_cond $qty_source_cond $sql_shipment_year_cond $projected_po_cond order by b.shipment_date desc"; 
	}
	//   echo $sql;die;
	$result = sql_select($sql);
	$po_id_array = array();
	foreach ($result as $val) 
	{
		$po_id_array[$val['ID']] = $val['ID'];
	}
	// ============================= store data in gbl table ==============================
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=69");
	oci_commit($con);
	
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 69, 1, $po_id_array, $empty_arr);//Po ID
	disconnect($con);
	// print_r($po_id_array);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 
	$po_cond = where_con_using_array($po_id_array,0,'po_break_down_id');
	$po_country_data_arr=array();
	$poCountryData=sql_select( "SELECT a.po_break_down_id, a.item_number_id, a.country_id, (a.order_quantity) as qnty, (a.plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown a,GBL_TEMP_ENGINE tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=69  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0");
	
	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']+=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];

		if($po_country_arr[$row[csf("po_break_down_id")]]=="")
		{
			$po_country_arr[$row[csf("po_break_down_id")]].=$row[csf("country_id")];
		}
		else
		{
			$po_country_arr[$row[csf("po_break_down_id")]].=','.$row[csf("country_id")];
		}
	}

	$total_out_qty_data_arr=array();
	$total_out_qty=sql_select( "SELECT a.po_break_down_id, a.item_number_id, a.country_id,a.production_type, (a.production_quantity) as production_quantity from pro_garments_production_mst a,GBL_TEMP_ENGINE tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=69  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0 and a.production_type in(5,7)");	

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=69");
	oci_commit($con);
	disconnect($con);

	foreach($total_out_qty as $row)
	{
		$total_out_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('production_type')]]+=$row[csf('production_quantity')];
	}
	?>
    
     <div style="width:1200px;">
     	<table cellspacing="0" cellpadding="0" border="1" width="1200" rules="all"  class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Buyer</th>
                <th width="110">Style</th>
                <th width="80">Job No</th>
                <th width="80">File No</th>
                <th width="80">Internal Ref.</th>
                <th width="120">Item</th>
                <th width="80">Country</th>
                <th width="70">Order Qty</th>
                <th width="60">Sewing Output Qty</th>
                <th width="50">Total Iron Qty</th>
                <th width="50">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1200px; max-height:240px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1180" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";
				
				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
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
								<td width="60" style="word-break:break-all" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>		
								<td width="100" style="word-break:break-all"><p><?php echo $row[csf("po_number")]; ?></p></td>
								<td width="100" style="word-break:break-all"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>	
								<td width="110" style="word-break:break-all"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
								<td width="80" style="word-break:break-all"><p><?php echo $row[csf("job_no")]; ?></p></td>
                                <td width="80" style="word-break:break-all"><p><?php echo $row[csf("file_no")]; ?></p></td>
                                <td width="80" style="word-break:break-all"><p><?php echo $row[csf("grouping")]; ?></p></td>
								<td width="120" style="word-break:break-all"><p><?php  echo $garments_item[$grmts_item];?></p></td>	
								<td width="80" style="word-break:break-all"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
								<td width="70" style="word-break:break-all" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
                                <td width="60" style="word-break:break-all" align="right"><?=$total_sewing_out_qty=$total_out_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][5]; ?>&nbsp;</td>
                                <td width="50" style="word-break:break-all" align="right"><?=$total_cut_qty=$total_out_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][7]; ?> &nbsp;</td>
                                <td width="50" style="word-break:break-all" align="right"><? $balance=$po_qnty-$total_cut_qty; echo $balance; ?>&nbsp;</td>
								<td style="word-break:break-all"> <?php  echo $company_arr[$row[csf("company_name")]];?> </td> 	
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
	$preceding_process=$dataArr[3];
	$qty_source=0;
	if($dataArr[3]==28) $qty_source=4; //Sewing Input
	else if($dataArr[3]==29) $qty_source=5;//Sewing Output
	else if($dataArr[3]==30) $qty_source=7;//Iron Output
	else if($dataArr[3]==31) $qty_source=8;//Packing And Finishing
	else if($dataArr[3]==32) $qty_source=7;//Iron Output
	else if($dataArr[3]==91) $qty_source=7;//Iron Output
	else if($dataArr[3]==103) $qty_source=11;//Poly Entry
	else if($dataArr[6]==312) $qty_source=85;//Finishing Receive Entry 	
	
	$res = sql_select("SELECT a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name,a.grouping 
			from wo_po_break_down a, wo_po_details_master b
			where a.job_id=b.id and a.id=$po_id"); 
			//echo $res; die;
 
  	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_internal_ref').val('".$result[csf('grouping')]."');\n";

  		if($qty_source!=0)
   		{		 
   		   $dataArray=sql_select("SELECT SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalinput,SUM(CASE WHEN production_type=7 THEN production_quantity ELSE 0 END) as totalsewing, table_no from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]."  and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0 and production_type in($qty_source,7) group by table_no");

   		   // echo "select SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalinput,SUM(CASE WHEN production_type=7 THEN production_quantity ELSE 0 END) as totalsewing, table_no from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]."  and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0";

	 		foreach($dataArray as $row)
			{  
				echo "$('#txt_sewing_quantity').val('".$row[csf('totalinput')]."');\n";
				echo "$('#txt_cumul_iron_qty').val('".$row[csf('totalsewing')]."');\n";
				$yet_to_produced = $row[csf('totalinput')]-$row[csf('totalsewing')];
				echo "$('#txt_yet_to_iron').val('".$yet_to_produced."');\n";
				echo "$('#cbo_table_name').val('".$row[csf('table_no')]."');\n";
			}
		}

		if($qty_source==0)
		{
			$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0");
		
			$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and production_type=7 and is_deleted=0");
			echo "$('#txt_sewing_quantity').val('".$plan_cut_qnty."');\n";		
			echo "$('#txt_cumul_iron_qty').attr('placeholder','".$total_produced."');\n";
			echo "$('#txt_cumul_iron_qty').val('".$total_produced."');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
			echo "$('#txt_yet_to_iron').attr('placeholder','".$yet_to_produced."');\n";
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
	$country_id = $dataArr[4];
	$variableSettingsRej = $dataArr[5];
	$qty_source=0;
	if($dataArr[6]==28) $qty_source=4; //Sewing Input
	else if($dataArr[6]==29) $qty_source=5;//Sewing Output
	else if($dataArr[6]==30) $qty_source=7;//Iron Output
	else if($dataArr[6]==31) $qty_source=8;//Packing And Finishing
	else if($dataArr[6]==32) $qty_source=7;//Iron Output
	else if($dataArr[6]==91) $qty_source=7;//Iron Output
	else if($dataArr[6]==103) $qty_source=11;//Poly Entry
	else if($dataArr[6]==312) $qty_source=85;//Finishing Receive Entry 	
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	if($qty_source!=0)
	{
		if( $variableSettings==2 ) // color level
		{
			if($db_type==0)
			{
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type='$qty_source' and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type='7' and cur.is_deleted=0 ) as cur_production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
			}
			else
			{
				$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=$qty_source then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=7 then b.production_qnty ELSE 0 END) as cur_production_qnty,
						sum(CASE WHEN c.production_type=7 then b.reject_qty ELSE 0 END) as reject_qty
						from wo_po_color_size_breakdown a 
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";	
			}
		}
		else if( $variableSettings==3 ) //color and size level
		{
			$sql ="SELECT a.color_size_break_down_id,
									sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as cur_production_qnty,
									sum(CASE WHEN a.production_type=7 then a.reject_qty ELSE 0 END) as reject_qty
									from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,7) group by a.color_size_break_down_id";
			$dtlsData = sql_select($sql);
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
			} 
			
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, id";
						
		}
		else // by default color and size level
		{
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
				sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as cur_production_qnty,
				sum(CASE WHEN a.production_type=7 then a.reject_qty ELSE 0 END) as reject_qty
				from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type ='$qty_source' group by a.color_size_break_down_id");
			
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
			} 
			
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, id";
		}
	}		
	else // if preceding process =0 in variable setting then plan cut quantity will show
	{
		if( $variableSettings==2 ) // color level
		{
			if($db_type==0)
			{
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and production_type=7 ) as production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
			}
			else
			{
				$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty
				from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.production_type=7
				where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";	
			}
		}
		else if( $variableSettings==3 ) //color and size level
		{				
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(7) group by a.color_size_break_down_id");
					
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
			} 
			
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_order,size_order"; //color_number_id, id
		}
		else // by default color and size level
		{
			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and production_type=1 ) as production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
				
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(7) group by a.color_size_break_down_id");
					
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
			} 
			
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_order,size_order";//color_number_id, id 
		}
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
	//print_r($sql);
	$colorHTML="";
	$colorID='';
	$chkColor = array(); 
	$i=0;$totalQnty=0;
	if($qty_source!=0)
	{
		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{ 
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"></td><td><input type="text" name="txtColSizeReiron" id="colSizeReiron_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Re Iron." onkeyup="fn_colorReiron_total('.($i+1).') '.$disable.'"></td></tr>';				
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
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];					
				}
				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				
				$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
				$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
				$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
				$color_size_breakdown_id = $color[csf('id')];
				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" data-colorSizeBreakdown="'.$color_size_breakdown_id.'" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" data-colorSizeBreakdown="'.$color_size_breakdown_id.'" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'><input type="text" name="colorSizeReiron" data-colorSizeBreakdown="'.$color_size_breakdown_id.'" id="colSizeReiron_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Re-Iron. Qty" onkeyup="fn_total_reiron('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
			}
			$i++; 
		}
	}
	if($qty_source==0)
	{
		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{ 
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut_qnty")]-$color[csf("production_qnty")]).'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"></td><td><input type="text" name="txtColSizeReiron" id="colSizeReiron_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Re-Iron" onkeyup="fn_colorReiron_total('.($i+1).') '.$disable.'"></td></tr>';				
				$totalQnty += $color[csf("plan_cut_qnty")]-$color[csf("production_qnty")];
				$colorID .= $color[csf("color_number_id")].",";
			}
			else //color and size level
			{
				if( !in_array( $color[csf("color_number_id")], $chkColor ) )
				{
					if( $i!=0 ) $colorHTML .= "</table></div>";
					$i=0;
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)">  <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];					
				}
				$bundle_mst_data="";
				$bundle_dtls_data="";
				$tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";
				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];
				$color_size_breakdown_id = $color[csf('id')];
				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" data-colorSizeBreakdown="'.$color_size_breakdown_id.'" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" data-colorSizeBreakdown="'.$color_size_breakdown_id.'" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'><input type="text" name="colorSizeReiron" data-colorSizeBreakdown="'.$color_size_breakdown_id.'" id="colSizeReiron_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Re-Iron. Qty" onkeyup="fn_total_reiron('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'></td><td><input type="button" name="button" id="button_'.$color[csf("color_number_id")].($i+1).'" value="Click For Bundle" class="formbutton" style="size:30px;" onclick="openmypage_bandle('.$color[csf("id")].','.$color[csf("color_number_id")].($i+1).','.$tmp_col_size.');" /></td></tr>';				
			}
			$i++; 
		}
	}

	//echo $colorHTML;die; 
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th><th width="60">Re-Iron</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_reiron" class="text_boxes_numeric" style="width:60px"></th></tr></tfoot></table>'; }
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
	$country_id = $dataArr[2];
	?>	 
    <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="180" align="center">Item Name</th>
                <th width="110" align="center">Country</th>
                <th width="70" align="center">Production Date</th>
                <th width="80" align="center">Production Qty</th> 
                <th width="80" align="center">Re-Iron Qty</th> 
                <th width="80" align="center">Reject Qty</th>                  
                <th width="80" align="center">Reporting Hour</th>
                <th width="120" align="center">Serving Company</th>
                <th align="center">Location</th>
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
				$sqlResult =sql_select("SELECT id,po_break_down_id,country_id ,item_number_id,production_date, production_quantity, re_production_qty, production_source, TIME_FORMAT(production_hour, '%H:%i' ) as production_hour,serving_company, location, reject_qnty from pro_garments_production_mst where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and production_type='7' and status_active=1 and is_deleted=0 order by id");
			}
		    if($db_type==2)
			{
				$sqlResult =sql_select("SELECT id,po_break_down_id,country_id ,item_number_id,production_date, production_quantity, re_production_qty, production_source, TO_CHAR(production_hour,'HH24:MI') as production_hour,serving_company, location, reject_qnty from pro_garments_production_mst where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and production_type='7' and status_active=1 and is_deleted=0 order by id");
			}
			foreach($sqlResult as $selectResult)
			{
				
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('production_quantity')];
 				?>
        
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_input_form_data','requires/iron_output_controller');" > 
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="180" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
					<td width="110" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
					<td width="70" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
					<td width="80" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
					<td width="80" align="center"><?php  echo $selectResult[csf('re_production_qty')]; ?></td>
					<td width="80" align="center"><?php  echo $selectResult[csf('reject_qnty')]; ?></td>
					
					<td width="80" align="center"><?php echo $selectResult[csf('production_hour')]; ?></td>
					<?php
						$source= $selectResult[csf('production_source')];
						if($source==3)
								$serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
								
							else
								$serving_company= $company_arr[$selectResult[csf('serving_company')]];
					?>	
					<td width="120" align="center"><p><?php echo $serving_company; ?></p></td>
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
	exit();
}


if($action=="show_country_listview")
{
	$data_ex = explode("_", $data);
	$po_id = $data_ex[0];
	$preceding_process = $data_ex[1];
	$qty_source=0;
	$heading="";
	if($preceding_process==28)
	{
		 $qty_source=4; //Sewing Input
		 $heading = "Input Qty";
	}
	else if($preceding_process==29) 
	{
		$qty_source=5;//Sewing Output
		 $heading = "Output Qty";
	}
	else if($preceding_process==30) 
	{
		$qty_source=7;//Iron Output
		 $heading = "Iron Qty";
	}
	else if($preceding_process==31) 
	{
		$qty_source=8;//Packing And Finishing
		 $heading = "Finishing Qty";
	}
	else if($preceding_process==32) 
	{
		$qty_source=7;//Iron Output
		 $heading = "Sewing Qty";
	}
	else if($preceding_process==91) 
	{
		$qty_source=7;//Iron Output
		 $heading = "Iron Qty";
	}
	else if($preceding_process==103) 
	{
		$qty_source=11;//Poly Entry
		 $heading = "Poly Qty";
	}
	else if($preceding_process==312) 
	{
		$qty_source=85;//Finishing Receive Entry
		 $heading = "Finishing Receive Qty";
	}
	?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="435" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="100">Item Name</th>
            <th width="80">Country</th>
            <th width="55">Shipment Date</th>
            <th width="65">Order Qty.</th>  
            <th width="65">Iron Qty.</th>                  
            <th><? echo $heading;?></th>                  
        </thead>
		<?
		$issue_qnty_arr=sql_select("SELECT a.po_break_down_id, a.item_number_id, a.country_id,a.production_type, b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$po_id' and a.production_type in(7,$qty_source) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$qty_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			if($row[csf("production_type")]==7)
			{
				$qty_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]['iron_qty']+=$row[csf("production_qnty")];
			}
			else
			{
				$qty_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]['output_qty']+=$row[csf("production_qnty")];
			}
		}  
		$i=1;
		$sqlResult =sql_select("SELECT po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$issue_qnty=$qty_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]['iron_qty'];
			$output_qty=$qty_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]['output_qty'];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);"> 
				<td width="20"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="55" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="65"><?  echo $row[csf('order_qnty')]; ?></td>
                <td align="right"><?  echo $issue_qnty; ?></td>
                <td align="right"><?  echo $output_qty; ?></td>
			</tr>
		<?	
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="populate_input_form_data")
{

	if($db_type==0)
	{
		$sqlResult =sql_select("SELECT id,company_id, garments_nature, po_break_down_id, item_number_id, challan_no, country_id, production_source, serving_company, sewing_line, location, produced_by, embel_name, embel_type, production_date, production_quantity, re_production_qty, production_source, production_type, entry_break_down_type, break_down_type_rej, TIME_FORMAT( production_hour, '%H:%i' ) as production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, reject_qnty, total_produced, yet_to_produced ,wo_order_id,currency_id,exchange_rate,rate, table_no from pro_garments_production_mst where id='$data' and production_type='7' and status_active=1 and is_deleted=0 order by id");
	}
	if($db_type==2)
	{
		$sqlResult =sql_select("SELECT id,company_id, garments_nature, po_break_down_id, item_number_id, challan_no, country_id, production_source, serving_company, sewing_line, location, produced_by, embel_name, embel_type, production_date, production_quantity, re_production_qty, production_source, production_type, entry_break_down_type, break_down_type_rej, TO_CHAR(production_hour,'HH24:MI') as production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, table_no, alter_qnty, reject_qnty, total_produced, yet_to_produced,wo_order_id,currency_id,exchange_rate,rate from pro_garments_production_mst where id='$data' and production_type='7' and status_active=1 and is_deleted=0 order by id");
	}
	if($sqlResult[0][csf('production_source')]==1)
	{
		$company=$sqlResult[0][csf('serving_company')];
	}
	else
	{
		$company=$sqlResult[0][csf('company_id')];
	}

	 
	$control_and_preceding=sql_select("SELECT is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=30 and company_name='$company'");  
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
	$qty_source=0;
	if($preceding_process==28) $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry
	else if($preceding_process==312) $qty_source=85;//Finishing Receive Entry 	


  	//echo "sdfds".$qty_source;die;
	foreach($sqlResult as $result)
	{ 
		$tableArgs = $result[csf('company_id')].'_'.$result[csf('location')].'_'.$result[csf('floor_id')];

		echo "$('#txt_iron_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/iron_output_controller', ".$result[csf('production_source')].", 'load_drop_down_source', 'iron_company_td' );\n";
		echo "$('#cbo_iron_company').val('".$result[csf('serving_company')]."');\n";
		echo "load_drop_down( 'requires/iron_output_controller',".$result[csf('serving_company')].", 'load_drop_down_location', 'location_td' );";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/iron_output_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";

		echo "load_drop_down( 'requires/iron_output_controller', '".$tableArgs."', 'load_drop_down_table', 'table_td' );\n";
		echo "$('#cbo_table_name').val('".$result[csf('table_no')]."');\n";
		$rate_string='';
		if($result[csf('production_source')]==3)
		{
			$company=$sqlResult[0][csf('company_id')];
			$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=30 and company_name='$company'");  
			$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
			echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
			
			echo "load_drop_down( 'requires/iron_output_controller', '".$result[csf('company_id')]."_".$result[csf('serving_company')]."_".$result[csf('po_break_down_id')]."', 'load_drop_down_workorder', 'workorder_td' );\n";
			
			echo "$('#cbo_work_order').val('".$result[csf('wo_order_id')]."');\n";
			echo "$('#hidden_currency_id').val('".$result[csf('currency_id')]."');\n";
			echo "$('#hidden_exchange_rate').val('".$result[csf('exchange_rate')]."');\n";
			echo "$('#hidden_piece_rate').val('".$result[csf('rate')]."');\n";
			$rate_string=$result[csf('rate')]." ".$currency[$result[csf('currency_id')]];
			if(trim($rate_string)!="") 
			{
				$rate_string="Work Order Rate ".$rate_string." /Pcs";
				echo "$('#workorder_rate_id').text('".$rate_string."');\n";
			}
			else
			{
				echo "$('#workorder_rate_id').text('');\n";
			}
		}
 		echo "$('#cbo_produced_by').val('".$result[csf('produced_by')]."');\n";
		echo "$('#txt_reporting_hour').val('".$result[csf('production_hour')]."');\n";
 		echo "$('#txt_iron_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_reiron_qty').val('".$result[csf('re_production_qty')]."');\n";
		echo "$('#txt_reject_qnty').val('".$result[csf('reject_qnty')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
 		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
 		if($qty_source!=0)
		{
		   $dataArray=sql_select("SELECT SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalinput,SUM(CASE WHEN production_type=7 THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]."  and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and status_active=1 and is_deleted=0");
	 		foreach($dataArray as $row)
			{  
				echo "$('#txt_sewing_quantity').val('".$row[csf('totalinput')]."');\n";
				echo "$('#txt_cumul_iron_qty').val('".$row[csf('totalsewing')]."');\n";
				$yet_to_produced = $row[csf('totalinput')]-$row[csf('totalsewing')];
				echo "$('#txt_yet_to_iron').val('".$yet_to_produced."');\n";
			}
		}
		else
		{  
			$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and status_active=1 and is_deleted=0");
	
			$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and production_type=7 and is_deleted=0");
			echo "$('#txt_sewing_quantity').val('".$plan_cut_qnty."');\n";	
			echo "$('#txt_cumul_iron_qty').val('".$total_produced."');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
			echo "$('#txt_yet_to_iron').val('".$yet_to_produced."');\n";
		}


		
		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_iron_input',1,1);\n";
		
		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		$variableSettingsRej = $result[csf('break_down_type_rej')];
		if($qty_source!=0)
		{
			if( $variableSettings!=1 ) // Not gross level
			{ 
				$po_id = $result[csf('po_break_down_id')];
				$item_id = $result[csf('item_number_id')];
				$country_id = $result[csf('country_id')];
				
				$sql_dtls = sql_select("SELECT color_size_break_down_id, production_qnty, reject_qty,re_production_qty, size_number_id, color_number_id from  pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");	
				foreach($sql_dtls as $row)
				{				  
					if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
				  	$amountArr[$index] = $row[csf('production_qnty')];
					$rejectArr[$index] = $row[csf('reject_qty')];
					$reironArr[$index] = $row[csf('re_production_qty')];

					$clr_sz_brk_id = $row['COLOR_SIZE_BREAK_DOWN_ID'];
					$amountColorSizeArr[$clr_sz_brk_id] = $row[csf('production_qnty')];
					$rejectColorSizeArr[$clr_sz_brk_id] = $row[csf('reject_qty')];
					$reironColorSizeArr[$clr_sz_brk_id] = $row[csf('re_production_qty')];
				}  
				 
				if( $variableSettings==2 ) // color level
				{
					if($db_type==0)
					{
						$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type='$qty_source' and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=7 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=7 and cur.is_deleted=0 ) as reject_qty 
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
					}
					else
					{
						$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
								sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,
								sum(CASE WHEN c.production_type=7 then b.production_qnty ELSE 0 END) as cur_production_qnty,
								sum(CASE WHEN c.production_type=7 then b.reject_qty ELSE 0 END) as reject_qty,
								sum(CASE WHEN c.production_type=7 then b.re_production_qty ELSE 0 END) as re_production_qty
								from wo_po_color_size_breakdown a 
								left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
								left join pro_garments_production_mst c on c.id=b.mst_id
								where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";	
						
					}
				}
				else if( $variableSettings==3 ) //color and size level
				{
					$dtlsData = sql_select("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as cur_production_qnty,
											sum(CASE WHEN a.production_type=7 then a.reject_qty ELSE 0 END) as reject_qty,
											sum(CASE WHEN a.production_type=7 then a.re_production_qty ELSE 0 END) as re_production_qty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,7) group by a.color_size_break_down_id");
											
						foreach($dtlsData as $row)
						{				  
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['reiron']= $row[csf('re_production_qty')];
						} 
						
						$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,id";
							
				}
				else // by default color and size level
				{
					/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=5 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=7 and cur.is_deleted=0 ) as cur_production_qnty 
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
						
						
						$dtlsData = sql_select("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as cur_production_qnty,
											sum(CASE WHEN a.production_type=7 then a.reject_qty ELSE 0 END) as reject_qty,
											sum(CASE WHEN a.production_type=7 then a.re_production_qty ELSE 0 END) as re_production_qty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,7) group by a.color_size_break_down_id");
											
						foreach($dtlsData as $row)
						{				  
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['reiron']= $row[csf('re_production_qty')];
						} 
						
						$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,id";
				}
				//echo $sql;
				
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
						$reironAmt = $reironArr[$color[csf("color_number_id")]];
						$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"></td><td><input type="text" name="txtColSizeReiron" id="colSizeReiron_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Re-Iron" value="'.$reironAmt.'" onkeyup="fn_colorReiron_total('.($i+1).') '.$disable.'"></td></tr>';				
						$totalQnty += $amount;
						$totalRejQnty += $rejectAmt;
						$totalReironQnty += $reironAmt;
						$colorID .= $color[csf("color_number_id")].",";
					}
					else //color and size level
					{
						/* $index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];
						$amount = $amountArr[$index];
						$rej_qnty = $rejectArr[$index]; */

						$clr_sz_brk_id = $color[csf('id')];
						$amount   = $amountColorSizeArr[$clr_sz_brk_id];
						$rej_qnty = $rejectColorSizeArr[$clr_sz_brk_id];

						if( !in_array( $color[csf("color_number_id")], $chkColor ) )
						{
							if( $i!=0 ) $colorHTML .= "</table></div>";
							$i=0;$colorWiseTotal=0;
							$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
							$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
							$chkColor[] = $color[csf("color_number_id")];
							$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
						}
	 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
						
						$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
						$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
						// $rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
						$reiron_qnty=$color_size_qnty_array[$color[csf('id')]]['reiron'];
						
						$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" data-colorSizeBreakdown="'.$clr_sz_brk_id.'" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ><input type="text" name="colorSizeRej" data-colorSizeBreakdown="'.$clr_sz_brk_id.'" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'><input type="text" name="colorSizeReiron" data-colorSizeBreakdown="'.$clr_sz_brk_id.'" id="colSizeReiron_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Re-Iron Qty" onkeyup="fn_total_reiron('.$color[csf("color_number_id")].','.($i+1).')" value="'.$reiron_qnty.'" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
						$colorWiseTotal += $amount;
					}
					$i++; 
				}
				//echo $colorHTML;die; 
				if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th><th width="60">Re-Iron</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_reiron" placeholder="'.$totalReironQnty.'" value="'.$totalReironQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
				echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
				if( $variableSettings==3 )echo "$totalFn;\n";
				$colorList = substr($colorID,0,-1);
				echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
			} 
	    } 
	    if($qty_source==0)
			{
				if( $variableSettings!=1 ) // gross level
				{ 
					$po_id = $result[csf('po_break_down_id')];
					$item_id = $result[csf('item_number_id')];
					$country_id = $result[csf('country_id')];
					
					
					$sql_dtls = sql_select("SELECT color_size_break_down_id, production_qnty, reject_qty,re_production_qty, size_number_id, color_number_id from pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");	

					foreach($sql_dtls as $row)
					{				  
						if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
					  	$amountArr[$index] = $row[csf('production_qnty')];
						$rejectArr[$index] = $row[csf('reject_qty')];
						$reironArr[$index] = $row[csf('re_production_qty')];

						$clr_sz_brk_id = $row['COLOR_SIZE_BREAK_DOWN_ID'];
						$amountColorSizeArr[$clr_sz_brk_id] = $row[csf('production_qnty')];
						$rejectColorSizeArr[$clr_sz_brk_id] = $row[csf('reject_qty')];
						$reironColorSizeArr[$clr_sz_brk_id] = $row[csf('re_production_qty')];
					}  
					
					if( $variableSettings==2 ) // color level
					{
						if($db_type==0)
						{
							
							$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and  	production_type=7 ) as production_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then reject_qty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and production_type=7 ) as reject_qty
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 group by color_number_id";
						}
						else
						{
							$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty, 
							sum(b.reject_qty) as reject_qty,sum(b.re_production_qty) as re_production_qty
						from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.production_type=7
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";	
						
						}
					}
					else if( $variableSettings==3 ) //color and size level
					{
						
							$dtlsData = sql_select("SELECT a.color_size_break_down_id,
												sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as production_qnty,
												sum(CASE WHEN a.production_type=7 then a.reject_qty ELSE 0 END) as reject_qty,
												sum(CASE WHEN a.production_type=7 then a.re_production_qty ELSE 0 END) as re_production_qty
												from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id  and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(7) group by a.color_size_break_down_id");
							//and b.id='$data'

							foreach($dtlsData as $row)
							{				  
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['reiron']= $row[csf('re_production_qty')];
							} 
							
							$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_order,size_order"; 
							
							
					}
					else // by default color and size level
					{
						
							
						$dtlsData = sql_select("SELECT a.color_size_break_down_id,
												sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as production_qnty,
												sum(CASE WHEN a.production_type=7 then a.reject_qty ELSE 0 END) as reject_qty,
												sum(CASE WHEN a.production_type=7 then a.re_production_qty ELSE 0 END) as re_production_qty
												from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(7) group by a.color_size_break_down_id");	
												
						foreach($dtlsData as $row)
						{				  
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['reiron']= $row[csf('re_production_qty')];
						}  
						//print_r($color_size_qnty_array);
						
						$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_order,size_order";
							
					}
		 			
					if($variableSettingsRej!=1)
					{
						$disable="";
					}
					else
					{
						$disable="disabled";
					}
					// echo $sql;
		 			$colorResult = sql_select($sql);
		 			//print_r($sql_dtls);die;
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
							$reironAmt = $reironArr[$color[csf("color_number_id")]];
							$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut_qnty")]-$color[csf("production_qnty")]+$amount).'" value="'.$amount.'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onkeyup="fn_colorRej_total('.($i+1).') '.$disable.'"></td><td><input type="text" name="txtColSizeReiron" id="colSizeReiron_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Re-Iron" value="'.$reironAmt.'" onkeyup="fn_colorReiron_total('.($i+1).') '.$disable.'"></td></tr>';				
							$totalQnty += $amount;
							$totalRejQnty += $rejectAmt;
							$totalReironQnty += $reironAmt;
							$colorID .= $color[csf("color_number_id")].",";
						}
						else //color and size level
						{
							$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];
							
							$amount = $amountArr[$index];
							
							// $clr_sz_brk_id = $row['COLOR_SIZE_BREAK_DOWN_ID']; 
							// $amount = $amountColorSizeArr[$clr_sz_brk_id]; 

							//$amount = $color[csf("size_number_id")]."*".$color[csf("color_number_id")];
							if( !in_array( $color[csf("color_number_id")], $chkColor ) )
							{
								if( $i!=0 ) $colorHTML .= "</table></div>";
								$i=0;
								$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].': <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
								$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
								$chkColor[] = $color[csf("color_number_id")];
								$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
								
							}
							
							
							 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";					
		 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
							$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];
 							$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
 							$reiron_qnty=$color_size_qnty_array[$color[csf('id')]]['reiron'];
							
							
							$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" data-colorSizeBreakdown="'.$clr_sz_brk_id.'" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty+$amount).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ><input type="text" name="colorSizeRej" data-colorSizeBreakdown="'.$clr_sz_brk_id.'" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'><input type="text" name="colorSizeReiron" data-colorSizeBreakdown="'.$clr_sz_brk_id.'" id="colSizeReiron_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Re-Iron Qty" onkeyup="fn_total_reiron('.$color[csf("color_number_id")].','.($i+1).')" value="'.$reiron_qnty.'" '.$disable.'></td><td><input type="button" name="button" value="Click For Bundle" class="formbutton" style="size:30px;" onclick="openmypage_bandle('.$color[csf("id")].','.$color[csf("color_number_id")].($i+1).','.$tmp_col_size.');" /></td></tr>';				
							//$colorWiseTotal += $amount;
							 $bundle_dtls_data="";
							 $bundle_dtls_data="";
						}
						$i++; 
					}
					//echo $colorHTML;die; 
					if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th><th width="60">Re-Iron</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$result[csf('production_quantity')].'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_reiron" placeholder="'.$totalReironQnty.'" value="'.$totalReironQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
					echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
					if( $variableSettings==3 )echo "$totalFn;\n";
					$colorList = substr($colorID,0,-1);
					echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
				}
			}
	}
	if($qty_source==4)
	{
		echo "$('#caption_msg_id').text('Sewing Input Qty');\n";
	}
	else if($qty_source==5)
	{
		echo "$('#caption_msg_id').text('Sewing Output Qty');\n";
	}
	else if($qty_source==11)
	{
		echo "$('#caption_msg_id').text('Poly Entry Qty');\n";
	}

 	exit();		
}

//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_name");
	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active in(1,2,3) and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{			
			echo "786**Projected PO is not allowed to production. Please check variable settings";die();
		}
	}
	
 	$is_control_sql=sql_select("SELECT is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=30 and company_name=$cbo_company_name");  
    $is_control=$is_control_sql[0][csf("is_control")];
	$qty_source=5;
	if($preceding_process==28) $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry
	else if($preceding_process==312) $qty_source=85;//Finishing Receive Entry 	
	
	if(str_replace("'","",$sewing_production_variable)=="" || str_replace("'","",$sewing_production_variable)==0)
	{
		$sewing_production_variable = 3;
	}	
	

	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}
 		
		//----------Compare by finishing qty and iron qty qty for validation----------------
		
		$txt_iron_qnty=str_replace("'","",$txt_iron_qty);
		$txt_iron_qty = ($txt_iron_qnty=="") ? 0 : $txt_iron_qnty;
		
		if($is_control==1 && $user_level!=2)//dont hide ISD-23-00518
		{
			$country_sewing_output_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=5 and country_id=$cbo_country_name and status_active=1 and is_deleted=0");
			$country_iron_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=7 and country_id=$cbo_country_name and status_active=1 and is_deleted=0");
			//echo $country_sewing_output_qty .'<'. $country_iron_qty.'+'.$txt_iron_qty;die;
			if($country_sewing_output_qty < $country_iron_qty+$txt_iron_qty)
			{
				echo "25**0";
				disconnect($con);
				die;
			}
		}
		//--------------------------------------------------------------Compare end;
		
		//$id=return_next_id("id", "pro_garments_production_mst", 1);
	    $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );

		if(str_replace("'","",$cbo_time)==1){$reportTime = $txt_reporting_hour ;}else {$reportTime = 12+str_replace("'","",$txt_reporting_hour);}
		//production_type array	
  		$field_array1="id, garments_nature, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, produced_by, production_date, production_quantity, re_production_qty, production_type, entry_break_down_type, break_down_type_rej, production_hour, remarks, floor_id, total_produced, yet_to_produced, reject_qnty, wo_order_id,currency_id, exchange_rate, rate, amount, table_no, inserted_by, insert_date"; 
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_iron_qty);}
		if($db_type==0)
		{
			$data_array1="(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_iron_company.",".$cbo_location.",".$cbo_produced_by.",".$txt_iron_date.",".$txt_iron_qty.",".$txt_reiron_qty.",7,".$sewing_production_variable.",".$iron_production_variable_rej.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_iron_qty.",".$txt_yet_to_iron.",".$txt_reject_qnty.",".$cbo_work_order.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$cbo_table_name.",".$user_id.",'".$pc_date_time."')";

		}
		if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_iron_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			$data_array1="INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_iron_company.",".$cbo_location.",".$cbo_produced_by.",".$txt_iron_date.",".$txt_iron_qty.",".$txt_reiron_qty.",7,".$sewing_production_variable.",".$iron_production_variable_rej.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_iron_qty.",".$txt_yet_to_iron.",".$txt_reject_qnty.",".$cbo_work_order.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$cbo_table_name.",".$user_id.",'".$pc_date_time."')";
		
		}

		// echo "10**insert into trims_production_dtls (".$field_array1.") values ".$data_array1; die;

 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		//echo $data_array;die;

		// pro_garments_production_dtls table entry here ----------------------------------///
		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,re_production_qty";
		
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b 
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(5,7) 
										group by a.color_size_break_down_id");
		$color_pord_data=array();							
		foreach($dtlsData as $row)
		{				  
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}
  		
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{		
			$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val)
			{
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

			$rowExReiron = explode("**",$colorIDvalueReiron);
			foreach($rowExReiron as $rowR=>$valR)
			{
				$colorSizeReironIDArr = explode("*",$valR);
				//echo $colorSizeRejIDArr[0]; die;
				$reironQtyArr[$colorSizeReironIDArr[0]]=$colorSizeReironIDArr[1];
			}
			
 			$rowEx = array_filter(explode("**",$colorIDvalue)); 
 			if(count($rowEx)==0)
 			{
 				$rowEx = explode("**",$colorIDvalueReiron);
 			}
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				if($is_control==1 && $user_level!=2)//dont hide ISD-23-00518
				{
					if($colorSizeNumberIDArr[1]>0)
					{
						if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
						{
							echo "35**Production Quantity Not Over Sewing Out Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}
				
				//7 for Iron output Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
				if($j==0)$data_array = "(".$dtls_id.",".$id.",7,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$reironQtyArr[$colorSizeNumberIDArr[0]]."')";
				else $data_array .= ",(".$dtls_id.",".$id.",7,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$reironQtyArr[$colorSizeNumberIDArr[0]]."')";
			//	$dtls_id=$dtls_id+1;							
 				$j++;								
			}
 		}//color level wise
		
		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{		
				
			/* $color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by size_number_id,color_number_id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}	 */
			
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
			$rowExRej = explode("***",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				/* $sizeID = $colorAndSizeRej_arr[0];
				$colorID = $colorAndSizeRej_arr[1];				
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				$rejQtyArr[$index]=$colorSizeRej; */

				$color_size_id = $colorAndSizeRej_arr[0];
				$colorSizeRej  = $colorAndSizeRej_arr[1]; 
				$rejQtyArr[$color_size_id]=$colorSizeRej;
			}

			$rowExReiron = explode("***",$colorIDvalueReiron);
			foreach($rowExReiron as $rowR=>$valR)
			{
				$colorAndSizeReiron_arr = explode("*",$valR);
				/* $sizeID = $colorAndSizeReiron_arr[0];
				$colorID = $colorAndSizeReiron_arr[1];				
				$colorSizeReiron = $colorAndSizeReiron_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				$reironQtyArr[$index]=$colorSizeReiron; */

				$color_size_id    = $colorAndSizeReiron_arr[0];
				$colorSizeReiron  = $colorAndSizeReiron_arr[1]; 
				$reironQtyArr[$color_size_id]=$colorSizeReiron;
			}
			
 			$rowEx = array_filter(explode("***",$colorIDvalue)); 
 			if(count($rowEx)==0)
 			{
 				$rowEx = explode("***",$colorIDvalueReiron);
 			}
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				/* $sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];				
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID; */

				$color_size_break_down_id   = $colorAndSizeAndValue_arr[0];
				$colorSizeValue   			= $colorAndSizeAndValue_arr[1];  

				if($is_control==1 && $user_level!=2)//dont hide ISD-23-00518
				{
					if($colorSizeValue>0)
					{
						if(($colorSizeValue*1)>($color_pord_data[$color_size_break_down_id]*1))
						{
							echo "35**Production Quantity Not Over Sewing Out Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}
 				
				//7 for Iron output Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
				if($j==0)$data_array = "(".$dtls_id.",".$id.",7,'".$color_size_break_down_id."','".$colorSizeValue."','".$rejQtyArr[$color_size_break_down_id]."','".$reironQtyArr[$color_size_break_down_id]."')";
				else $data_array .= ",(".$dtls_id.",".$id.",7,'".$color_size_break_down_id."','".$colorSizeValue."','".$rejQtyArr[$color_size_break_down_id]."','".$reironQtyArr[$color_size_break_down_id]."')";
				//$dtls_id=$dtls_id+1;
 				$j++;
			}
		}//color and size wise
		
		if($db_type==2)
		{
			
			$rID=execute_query($data_array1);
			//echo "10**INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1; die;
		}
		else
		{
		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		}
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
		//----------Compare by finishing qty and iron qty qty for validation----------------
		if($is_control==1 && $user_level!=2)//dont hide ISD-23-00518
		{
			$txt_iron_qty=str_replace("'","",$txt_iron_qty);
			$txt_mst_id=str_replace("'","",$txt_mst_id);
			$country_sewing_output_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=5 and country_id=$cbo_country_name and status_active=1 and is_deleted=0");
			$country_iron_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=7 and country_id=$cbo_country_name and status_active=1 and is_deleted=0 and id <> $txt_mst_id");
			if($country_sewing_output_qty < $country_iron_qty+$txt_iron_qty)
			{
				echo "25**".str_replace("'","",$hidden_po_break_down_id);
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}
		}
		//--------------------------------------------------------------Compare end;
		
		
		// pro_garments_production_mst table data entry here 
		if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_iron_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
		
 		$field_array1="production_source*serving_company*location*produced_by*production_date*production_quantity*re_production_qty*production_type*entry_break_down_type*break_down_type_rej*production_hour*challan_no*remarks*floor_id*total_produced*yet_to_produced*reject_qnty*wo_order_id*currency_id*exchange_rate*rate*amount*table_no*updated_by*update_date";
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_iron_qty);}
		else {$amount="";}
		$data_array1="".$cbo_source."*".$cbo_iron_company."*".$cbo_location."*".$cbo_produced_by."*".$txt_iron_date."*".$txt_iron_qty."*".$txt_reiron_qty."*7*".$sewing_production_variable."*".$iron_production_variable_rej."*".$txt_reporting_hour."*".$txt_challan."*".$txt_remark."*".$cbo_floor."*".$txt_cumul_iron_qty."*".$txt_yet_to_iron."*".$txt_reject_qnty."*".$cbo_work_order."*".$hidden_currency_id."*".$hidden_exchange_rate."*'".$rate."'*'".$amount."'*".$cbo_table_name."*".$user_id."*'".$pc_date_time."'";
		
 		//$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		//echo $data_array1;die;
		
		// pro_garments_production_dtls table data entry here 
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) // check is not gross level
		{
			
			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=7 then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b 
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(5,7) and b.id !=$txt_mst_id
										group by a.color_size_break_down_id");
			$color_pord_data=array();							
			foreach($dtlsData as $row)
			{				  
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			
			
 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,re_production_qty";
			
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by id" );
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

				$rowExReiron = explode("**",$colorIDvalueReiron);
				foreach($rowExReiron as $rowR=>$valR)
				{
					$colorSizeReironIDArr = explode("*",$valR);
					//echo $colorSizeRejIDArr[0]; die;
					$reironQtyArr[$colorSizeReironIDArr[0]]=$colorSizeReironIDArr[1];
				}
				
				$rowEx = array_filter(explode("**",$colorIDvalue)); 
	 			if(count($rowEx)==0)
	 			{
	 				$rowEx = explode("**",$colorIDvalueReiron);
	 			}
				//	$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					if($is_control==1 && $user_level!=2)//dont hide ISD-23-00518
					{
						if($colorSizeNumberIDArr[1]>0)
						{
							if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
							{
								echo "35**Production Quantity Not Over Sewing Out Qnty";
								//check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}
					
					//7 for Iron output Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",7,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$reironQtyArr[$colorSizeNumberIDArr[0]]."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",7,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$reironQtyArr[$colorSizeNumberIDArr[0]]."')";
					//$dtls_id=$dtls_id+1;							
					$j++;								
				}
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
					
				/* $color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by size_number_id,color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				} */	
				
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				$rowExRej = explode("***",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					/* $sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];				
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					$rejQtyArr[$index]=$colorSizeRej; */

					$color_size_id = $colorAndSizeRej_arr[0];
					$colorSizeRej  = $colorAndSizeRej_arr[1]; 
					$rejQtyArr[$color_size_id]=$colorSizeRej;
				}

				$rowExReiron = explode("***",$colorIDvalueReiron);
				foreach($rowExReiron as $rowR=>$valR)
				{
					$colorAndSizeReiron_arr = explode("*",$valR);
					/* $sizeID = $colorAndSizeReiron_arr[0];
					$colorID = $colorAndSizeReiron_arr[1];				
					$colorSizeReiron = $colorAndSizeReiron_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					$reironQtyArr[$index]=$colorSizeReiron; */

					$color_size_id 	  = $colorAndSizeReiron_arr[0];
					$colorSizeReiron  = $colorAndSizeReiron_arr[1]; 
					$reironQtyArr[$color_size_id]=$colorSizeReiron;
				}
				
				$rowEx = array_filter(explode("***",$colorIDvalue)); 
	 			if(count($rowEx)==0)
	 			{
	 				$rowEx = explode("***",$colorIDvalueReiron);
	 			}
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					/* $sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];				
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID; */

					$color_size_id 	 = $colorAndSizeAndValue_arr[0];
					$colorSizeValue  = $colorAndSizeAndValue_arr[1];

					if($is_control==1 && $user_level!=2)//dont hide ISD-23-00518
					{
						if($colorSizeValue>0)
						{
							if(($colorSizeValue*1)>($color_pord_data[$color_size_id]*1))
							{
								echo "35**Production Quantity Not Over Sewing Out Qnty";
								//check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}
					
					//7 for Iron output Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",7,'".$color_size_id."','".$colorSizeValue."','".$rejQtyArr[$color_size_id]."','".$reironQtyArr[$color_size_id]."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",7,'".$color_size_id."','".$colorSizeValue."','".$rejQtyArr[$color_size_id]."','".$reironQtyArr[$color_size_id]."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
			
 			//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		
		}//end cond
		
		$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
		// echo "10**INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array; die;
		// echo "10**$rID**$dtlsrDelete**$dtlsrID";die();
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

if($action=="iron_output_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$sewing_library=return_library_array( "select id, line_name from  lib_sewing_line", "id", "line_name"  );
	
	$sql="SELECT id, company_id, challan_no, sewing_line, po_break_down_id,entry_break_down_type,break_down_type_rej, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_hour, production_quantity,reject_qnty, production_type, remarks, floor_id, sewing_line from pro_garments_production_mst where production_type=7 and id='$data[1]' and status_active=1 and is_deleted=0 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$po_id_array = array();
	foreach ($dataArray as $v) 
	{
		$po_id_array[$v['PO_BREAK_DOWN_ID']] = $v['PO_BREAK_DOWN_ID'];
	}
	$po_id_cond = where_con_using_array($po_id_array,0,"id");
	$order_library=return_library_array( "SELECT id, po_number from  wo_po_break_down where status_active=1 $po_id_cond", "id", "po_number" );
	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];
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
            <td colspan="6" align="center" style="font-size:x-large"><strong><? echo $data[2];  ?> Challan</strong></td>
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
				foreach($dataArray as $row)
				{
					$job_no=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"job_no");
					$buyer_val=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"buyer_name");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
				}
            ?> 
        	<td width="270" rowspan="5" valign="top" colspan="2"><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></td>
            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?></td>
            <td width="125"><strong>Buyer:</strong></td><td width="175px"><? echo $buyer_library[$buyer_val]; ?></td>
        </tr>
        <tr>
            <td><strong>Job No :</strong></td><td width="175px"><? echo $job_no; ?></td>
            <td><strong>Style Ref.:</strong></td> <td width="175px"><? echo $style_val; ?></td>
        </tr>
        <tr>
        	<td><strong>Item:</strong></td> <td width="175px"><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
            <td><strong>Order Qnty:</strong></td><td width="175px"><? echo $dataArray[0][csf('production_quantity')]; ?></td>
        </tr>
        <tr>
            <td><strong>Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Input Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Reporting Hour:</strong></td> <td width="175px"><? echo $dataArray[0][csf('production_hour')]; ?></td>
            <td><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Produced By: </strong></td><td><? echo $worker_type[$dataArray[0][csf('produced_by')]]; ?></td>
        </tr>
        <tr>
            <td colspan="6"><strong><p>Remarks:  <? echo $dataArray[0][csf('remarks')]; ?></p></strong></td>
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
			
			$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}
			
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}
			
			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?> 
         	<div style="width:100%;">
             <div style="margin-left:30px;"><strong> Goods Qty.</strong></div>
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
		}
		if($break_down_type_reject!=1)
		{ 
        
        	$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty,(reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.color_size_break_down_id=b.id and a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array();
			$qun_array=array();$reject_qun_array=array();
			$color_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('reject_qty')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}
			
			/* $sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.color_size_break_down_id=b.id and a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			} */
			
			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?> 
         	<div style="width:100%;">
            <div style="margin-left:30px;"><strong> Reject Qty.</strong></div>
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
                        foreach ($size_array as $reject_sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $reject_qun_array[$cid][$reject_sizval]; ?></td>
                            <?
                            $tot_reject_qnty[$cid]+=$reject_qun_array[$cid][$reject_sizval];
							$tot_reject_qnty_size[$reject_sizval]+=$reject_qun_array[$cid][$reject_sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_reject_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$reject_production_quantity+=$tot_reject_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $reject_sizval)
				{
					?>
                    <td align="right"><?php echo $tot_reject_qnty_size[$reject_sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $reject_production_quantity; ?></td>
        </tr>                           
    </table>
        <? 
		}
            echo signature_table(30, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}

if($action=="production_process_control")
{
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=30 and company_name='$data'");  
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }
	
	exit();	
}
?>