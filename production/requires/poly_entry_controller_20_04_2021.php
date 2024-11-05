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
$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

if ($action=="load_variable_settings")
{
	list($lcCompany,$working_company)=explode('**',$data);
	
	echo "$('#poly_production_variable').val(0);\n";
	$sql_result = sql_select("select sewing_production, production_entry from variable_settings_production where company_name=$lcCompany and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#poly_production_variable').val(".$result[csf("sewing_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
	
	
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$working_company and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo!=1) $prod_reso_allo=0;
	echo "document.getElementById('prod_reso_allo').value=".$prod_reso_allo.";\n";
	
	echo "$('#poly_production_variable_rej').val(0);\n";
	$sql_result_rej = sql_select("select sewing_production from variable_settings_production where company_name=$working_company and variable_list=28 and status_active=1");
 	foreach($sql_result_rej as $result)
	{
		echo "$('#poly_production_variable_rej').val(".$result[csf("sewing_production")].");\n";
	}
	//$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$working_company and variable_list=33 and page_category_id=29","is_control");
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$working_company and variable_list=33 and page_category_id=103","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";
	
	$variable_qty_source_poly=return_field_value("qty_source_poly","variable_settings_production","company_name=$working_company and variable_list=42","qty_source_poly");
	echo "document.getElementById('txt_qty_source').value='".$variable_qty_source_poly."';\n";
	
 	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/poly_entry_controller', this.value, 'load_drop_down_floor', 'floor_td'); load_drop_down( 'requires/poly_entry_controller', document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_poly_date').value, 'load_drop_down_poly_output_line', 'poly_line_td'); get_php_form_data(document.getElementById('cbo_source').value, 'line_disable_enable','requires/poly_entry_controller');");
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 150, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (13) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/poly_entry_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_poly_date').value, 'load_drop_down_poly_line_floor', 'poly_line_td' );",0 );  
	exit();   	 
}

if($action=="line_disable_enable")
{
	if($data==1)
		echo "disable_enable_fields('cbo_poly_line',0,'','');\n";
	else
	{
		echo "$('#cbo_poly_line').val(0);\n";
		echo "disable_enable_fields('cbo_poly_line',1,'','');\n";	
	}
	exit();
}

if($action=="load_drop_down_poly_output")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_poly_company", 150, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "fnc_loadvariable($('#cbo_company_name').val()); fnc_workorder_search(this.value);",0,0 );
		}
		else
		{	
			echo create_drop_down( "cbo_poly_company", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--- Select ---", $selected, " fnc_loadvariable($('#cbo_company_name').val()); fnc_workorder_search(this.value);",0,0 );
		}
	}
	else if($data==1)
	{
 		echo create_drop_down( "cbo_poly_company", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "",  "fnc_loadvariable(this.value); load_drop_down( 'requires/poly_entry_controller', this.value, 'load_drop_down_location', 'location_td' ); fnc_company_check(document.getElementById('cbo_source').value);",0,0 ); 
		
	}
 	else
	{
		echo create_drop_down( "cbo_poly_company", 150, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	exit();
}

if ($action=="load_drop_down_workorder")
{
	$explode_data = explode("_",$data);
	
	$sql = "select a.id,a.sys_number from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=".$explode_data[2]." and a.company_id=$explode_data[0]  and a.rate_for=30 and a.service_provider_id=$explode_data[1]   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number order by a.id"; 
	//echo $sql;
	echo create_drop_down( "cbo_work_order", 150, $sql,"id,sys_number", 1, "-- Select Work Order --", $selected, "fnc_workorder_rate('$data',this.value)",0 );
	exit();
}

if($action=="populate_workorder_rate")
{
	$data=explode("_",$data);
	$po_break_down_id=$data[2];
	$company_id=$data[0];
	$suppplier=$data[1];
	$sql = sql_select("select a.id, a.sys_number, a.currence, a.exchange_rate, sum(b.avg_rate) as rate, b.uom from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=".$data[3]."  and a.id=b.mst_id and b.order_id=".$po_break_down_id." and a.company_id=$company_id and a.service_provider_id=$suppplier and a.rate_for=30   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number,a.currence ,a.exchange_rate,b.uom order by a.id"); 
	//echo $sql;
	if($sql[0][csf('uom')]==2) 
	{
		$rate=$sql[0][csf('rate')]/12;
	}
	else
	{
		$rate=$sql[0][csf('rate')];
	}
	echo "$('#hidden_currency_id').val('".$sql[0][csf('currence')]."');\n";
	echo "$('#hidden_exchange_rate').val('".$sql[0][csf('exchange_rate')]."');\n";
	echo "$('#hidden_piece_rate').val('".$rate."');\n";
	echo "$('#workorder_rate_id').text('');\n";
	$rate_string='';
	$rate_string=$rate." ".$currency[$sql[0][csf('currence')]];
	if(trim($rate_string)!="") 
	{
		$rate_string="Work Order Rate ".$rate_string." /Pcs";
		echo "$('#workorder_rate_id').text('".$rate_string."');\n";
	}
	exit();
}


if($action=="load_drop_down_poly_output_line")
{
	//echo $data; die;
	$explode_data = explode("_",$data);
	$location = $explode_data[0];
	$prod_reso_allocation = $explode_data[1];
	$txt_poly_date = $explode_data[2];
	
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_poly_date=="")
		{ 
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 and location_id='$location' order by a.prod_resource_num");
		}
		else
		{
			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_poly_date,'yyyy-mm-dd')."' and a.location_id='$location' and a.is_deleted=0 and b.is_deleted=0 group by a.id order by a.prod_resource_num");
			}
			if($db_type==2 || $db_type==1)
			{	
				//echo "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_poly_date))."' and a.is_deleted=0 and b.is_deleted=0 and a.location_id='$location' group by a.id, a.line_number";
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_poly_date))."' and a.is_deleted=0 and b.is_deleted=0 and a.location_id='$location' group by a.id, a.line_number, a.prod_resource_num order by a.prod_resource_num");
			}
		}

		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			
			foreach($line_number as $id=>$val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		echo create_drop_down( "cbo_poly_line", 110,$line_array,"", 1, "--- Select ---", $selected, "",0,0 );		
	}
	else
	{
		echo create_drop_down( "cbo_poly_line", 110, "select id, line_name from lib_sewing_line where is_deleted=0 and status_active=1 and location_name='$location' and location_name!=0 order by sewing_line_serial","id,line_name", 1, "Select Line", $selected, "" );
	}
	exit();
}

if($action=="load_drop_down_poly_line_floor")
{
	$explode_data = explode("_",$data);	
	$prod_reso_allocation = $explode_data[2];
	$txt_poly_date = $explode_data[3];
	$cond="";
	
	/*if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_poly_date=="")
		{ 
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
			
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 and location_id='$location' order by prod_resource_num");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
			
			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_poly_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id order by a.prod_resource_num");
			}
			if($db_type==2 || $db_type==1)
			{	
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_poly_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number, a.prod_resource_num order by a.prod_resource_num");
			}

			//echo "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_poly_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number, a.prod_resource_num order by a.prod_resource_num"; die;
		}

		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $id=>$val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			
			$line_array[$row[csf('id')]]=$line;
		}
		echo create_drop_down( "cbo_poly_line", 110,$line_array,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";
		
		echo create_drop_down( "cbo_poly_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by sewing_line_serial","id,line_name", 1, "--- Select ---", $selected, "",0,0 );

	}*/
	if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";
		
		echo create_drop_down( "cbo_poly_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by sewing_line_serial","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
	exit();
}

if($action=="load_drop_down_color_type")
{
	// $explode_data = explode("_",$data);	
	// $company_id = $explode_data[0];
	$po_id = $data;
	$sql_job = sql_select("SELECT job_no_mst from wo_po_break_down where id=$po_id and status_active=1");
	$job_no = $sql_job[0][csf('job_no_mst')];

	$sql = sql_select("SELECT color_type_id from wo_pre_cost_fabric_cost_dtls where job_no='$job_no' and status_active=1 order by color_type_id asc");
	$color_type_array = array();
	$color_type_array[0] = "-- Select --";
	foreach ($sql as $val) 
	{
		$color_type_array[$val[csf('color_type_id')]] = $color_type[$val[csf('color_type_id')]];
	}
		
	echo create_drop_down( "cbo_color_type", 110, $color_type_array, 1, "-- Select --",$selected,"",0);
	
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
	
	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id,pack_type)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id); 
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_country_id").val(country_id);
		$("#hidden_pack_type").val(pack_type);
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
									//$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
									$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref");
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
                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+<? echo $production_company; ?>+'_'+<? echo $hidden_variable_cntl; ?>+'_'+<? echo $hidden_preceding_process; ?>, 'create_po_search_list_view', 'search_div', 'poly_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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
                    <input type="hidden" id="hidden_pack_type">
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
	/*Sewing Input=28 Sewing Output=29 Iron entry=30 
	Packing And Finishing=31 Ex-Factory=32 Inspection=91
	Poly Entry=103*/
	
	$variable_qty_source_poly=return_field_value("qty_source_poly","variable_settings_production","company_name=$company and variable_list=42","qty_source_poly");
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";		
		else if(trim($txt_search_by)==3)
			$sql_cond = " and a.job_no like '%".trim($txt_search_common)."'";	
		else if(trim($txt_search_by)==4)
			$sql_cond = " and c.acc_po_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==5)
			$sql_cond = " and b.file_no like ".trim($txt_search_common)."";
		else if(trim($txt_search_by)==6)
			$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";		
 	}
	
	if($txt_date_from!="" || $txt_date_to!="") 
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}
	
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
	
	if(trim($txt_search_by)==4 && trim($txt_search_common)!="")
	{
		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
			where a.job_no = b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=$garments_nature
			$sql_cond group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut order by b.shipment_date DESC";
	}
	else
	{	
 		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b 
			where a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond order by b.shipment_date DESC";
	}
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	
	$po_country_data_arr=array();
	$country_sql=sql_select( "select po_break_down_id, item_number_id, country_id, pack_type, sum(order_quantity) as po_qty, sum(plan_cut_qnty) as plan_cut_qty from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id, pack_type"); 
	
	foreach($country_sql as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]['po_qty']=$row[csf('po_qty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]['plan_cut_qty']=$row[csf('plan_cut_qty')];
	}
	unset($country_sql);
	
	$total_out_qty_data_arr=array();
	$total_out_qty=sql_select( "select a.po_break_down_id, a.item_number_id, a.country_id, a.pack_type, sum(b.production_qnty) as production_quantity from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and a.production_type=11 and b.production_type=11 group by a.po_break_down_id, a.item_number_id, a.country_id, a.pack_type");
	
	foreach($total_out_qty as $row)
	{
		$total_out_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]=$row[csf('production_quantity')];
	}
	unset($total_out_qty);
	$qty_source=0;
	/*Sewing Input=28 Sewing Output=29 Iron entry=30 
	Packing And Finishing=31 Ex-Factory=32 Inspection=91
	Poly Entry=103*/

	/*if($variable_qty_source_poly==1) $qty_source=5; //Sweing Output
	else if($variable_qty_source_poly==2) $qty_source=7;//Iron Output*/

	if($preceding_process==28) $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry
	// default source for poly from sewing output

	
	$total_in_qty_data_arr=array();
    $sql= "select a.po_break_down_id, a.item_number_id, a.country_id, sum(b.production_qnty) as production_quantity, a.pack_type from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0  and a.production_type='$qty_source' and b.production_type='$qty_source' and a.serving_company=$production_company group by a.po_break_down_id, a.item_number_id, a.country_id, a.pack_type ";
	$total_in_qty=sql_select($sql);
	foreach($total_in_qty as $row)
	{
		$total_in_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]=$row[csf('production_quantity')];
	}
	unset($total_in_qty);
	
	 //echo $sql;
	?>
    
     <div style="width:1270px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Job No</th>
                <th width="90">Buyer</th>
                <th width="100">Style</th>
                <th width="80">File No</th>
                <th width="80">Ref. No</th>
                <th width="120">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Total Attach. Input Qty</th>
                <th width="80">Total Attach. Output Qty</th>
                <th width="80">Balance</th>
                <th>Pack Type</th>
            </thead>
     	</table>
     </div>
     <div style="width:1270px; max-height:240px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1252" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
			foreach( $result as $key=>$row )
            {
 
				foreach($po_country_data_arr[$row[csf('id')]] as $grmts_item=>$item_data)
				{
					foreach($item_data as $country_id=>$country_data)
					{
						foreach($country_data as $pack_type=>$val)
						{
							if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_in_qty=0; $total_cut_qty=0; $balance=0;
							$po_qnty=$val['po_qty'];
							$plan_cut_qnty=$val['plan_cut_qty'];
							if($total_in_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type]!=0){
								unset($result[$key]);
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>','<? echo $pack_type;?>');" > 
									<td width="30" align="center"><?php echo $i; ?></td>
									<td width="60"><p><?php echo change_date_format($row[csf("shipment_date")]);?></p></td>
                                    <td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
									<td width="100"><p><?php echo $row[csf("job_no")]; ?></p></td>		
									<td width="90"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>	
									<td width="100"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
									<td width="80"><p><?php echo $row[csf("file_no")]; ?></p></td>
									<td width="80"><p><?php echo $row[csf("grouping")]; ?></p></td>
									<td width="120"><p><?php echo $garments_item[$grmts_item];?></p></td>	
									<td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
									<td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
									<td width="80" align="right"><?php $total_in_qty=$total_in_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type];echo $total_in_qty;  ?>&nbsp;</td>
									<td width="80" align="right"><?php $total_cut_qty=$total_out_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type]; echo $total_cut_qty; ?>&nbsp;</td>
								   <td width="80" align="right"><?php $balance=$total_in_qty-$total_cut_qty; if($pack_type!="") echo ""; else if ($balance==0) echo ''; else echo $balance;?>&nbsp;</td>
								   <td><?php echo $pack_type;?>&nbsp;</td> 	
								</tr>
							<? 
							$i++;
							}
						}
					}
				}
            }
			
			foreach( $result as $row )
            {
				foreach($po_country_data_arr[$row[csf('id')]] as $grmts_item=>$item_data)
				{
					foreach($item_data as $country_id=>$country_data)
					{
						foreach($country_data as $pack_type=>$val)
						{
							if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_in_qty=0; $total_cut_qty=0; $balance=0;
							$po_qnty=$val['po_qty'];
							$plan_cut_qnty=$val['plan_cut_qty'];
							
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>','<? echo $pack_type;?>');" > 
									<td width="30" align="center"><?php echo $i; ?></td>
									<td width="60"><p><?php echo change_date_format($row[csf("shipment_date")]);?></p></td>
                                    <td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
									<td width="100"><p><?php echo $row[csf("job_no")]; ?></p></td>		
									<td width="90"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>	
									<td width="100"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
									<td width="80"><p><?php echo $row[csf("file_no")]; ?></p></td>
									<td width="80"><p><?php echo $row[csf("grouping")]; ?></p></td>
									<td width="120"><p><?php echo $garments_item[$grmts_item];?></p></td>	
									<td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
									<td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
									<td width="80" align="right"><?php if($qty_source!=0){$total_in_qty=$total_in_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type];}else{$total_in_qty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type]['plan_cut_qty'];} echo $total_in_qty; ?>&nbsp;</td>
									<td width="80" align="right"><?php $total_cut_qty=$total_out_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type]; echo $total_cut_qty; ?>&nbsp;</td>
								   <td width="80" align="right"><?php $balance=$total_in_qty-$total_cut_qty; if($pack_type!="") echo ""; else if ($balance==0) echo ''; else echo $balance;?>&nbsp;</td>
								   <td><?php echo $pack_type;?>&nbsp;</td> 	
								</tr>
							<? 
							$i++;
						}
					}
				}
            }
			unset($result);
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
	$pack_type = $dataArr[4];
 	if( $pack_type=='') $pack_type_cond=''; else $pack_type_cond=" and a.pack_type='$pack_type'";
	$qty_source=0;
	if($dataArr[3]==28) $qty_source=4; //Sewing Input
	else if($dataArr[3]==29) $qty_source=5;//Sewing Output
	else if($dataArr[3]==30) $qty_source=7;//Iron Output
	else if($dataArr[3]==31) $qty_source=8;//Packing And Finishing
	else if($dataArr[3]==32) $qty_source=7;//Iron Output
	else if($dataArr[3]==91) $qty_source=7;//Iron Output
	else if($dataArr[3]==103) $qty_source=11;//Poly Entry


	
	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name 
			from wo_po_break_down a, wo_po_details_master b
			where a.job_no_mst=b.job_no and a.id=$po_id"); 
 
  	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
  		  		
   		if($qty_source!=0)
   		{
   			$dataArray=sql_select("select SUM(CASE WHEN a.production_type='$qty_source' and b.production_type='$qty_source' THEN b.production_qnty END) as totalinput,SUM(CASE WHEN a.production_type=11 and b.production_type=11  THEN b.production_qnty ELSE 0 END) as totalpoly from pro_garments_production_mst a,pro_garments_production_dtls b WHERE a.id=b.mst_id and a.po_break_down_id=".$result[csf('id')]."  and a.item_number_id='$item_id' and a.country_id='$country_id' $pack_type_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	 		foreach($dataArray as $row)
			{  
				echo "$('#txt_input_quantity').val('".$row[csf('totalinput')]."');\n";
				echo "$('#txt_cumul_poly_qty').val('".$row[csf('totalpoly')]."');\n";
				$yet_to_produced = $row[csf('totalinput')]-$row[csf('totalpoly')];
				echo "$('#txt_yet_to_poly').val('".$yet_to_produced."');\n";
			}
   		}

		if($qty_source==0)
		{
			$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and status_active in(1,2,3) and is_deleted=0");
		
			$total_produced = return_field_value("sum(b.production_qnty)","pro_garments_production_mst a,pro_garments_production_dtls b","a.id=b.mst_id and  a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type=11 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_type=11 and a.status_active=1 ");
			echo "$('#txt_input_quantity').val('".$plan_cut_qnty."');\n";		
			echo "$('#txt_cumul_poly_qty').attr('placeholder','".$total_produced."');\n";
			echo "$('#txt_cumul_poly_qty').val('".$total_produced."');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
			echo "$('#txt_yet_to_poly').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_poly').val('".$yet_to_produced."');\n";
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
	$pack_type=$dataArr[7];
	if( $pack_type=='') $pack_type_cond=''; else $pack_type_cond=" and pack_type='$pack_type'";
	if( $pack_type=='') $pack_typeCond=''; else $pack_typeCond=" and b.pack_type='$pack_type'";
	 
 	$qty_source=0;
	if($dataArr[6]==28) $qty_source=4; //Sewing Input
	else if($dataArr[6]==29) $qty_source=5;//Sewing Output
	else if($dataArr[6]==30) $qty_source=7;//Iron Output
	else if($dataArr[6]==31) $qty_source=8;//Packing And Finishing
	else if($dataArr[6]==32) $qty_source=7;//Iron Output
	else if($dataArr[6]==91) $qty_source=7;//Iron Output
	else if($dataArr[6]==103) $qty_source=11;//Poly Entry	
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	if($qty_source !=0)
	{

	 if( $variableSettings==2 ) // color level
	 {
			if($db_type==0)
			{
				$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type='$qty_source' and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=11 and cur.is_deleted=0 ) as cur_production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) $pack_type_cond group by color_number_id";
			}
			else
			{
				$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=11 then b.production_qnty ELSE 0 END) as cur_production_qnty,
						sum(CASE WHEN c.production_type=11 then b.reject_qty ELSE 0 END) as reject_qty
						from wo_po_color_size_breakdown a 
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.is_deleted=0 and b.status_active=1
						left join pro_garments_production_mst c on c.id=b.mst_id and c.is_deleted=0 and c.status_active=1
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3)  $pack_type_cond group by a.item_number_id, a.color_number_id";	
				
			}
	 }
	else //if( $variableSettings==3) color and size level (if not lib set, defaulst this;)
	{
			
		$dtlsData = sql_select("select a.color_size_break_down_id,
									sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN a.production_type=11 then a.production_qnty ELSE 0 END) as cur_production_qnty ,
									sum(CASE WHEN a.production_type=11 then a.reject_qty ELSE 0 END) as reject_qty
									from pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,11) $pack_typeCond group by a.color_size_break_down_id");
									
		foreach($dtlsData as $row)
		{				  
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
		} 
		
		$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) $pack_type_cond order by color_number_id, size_order";
			
	}
	/*else // by default color and size level
	{
		
			
			$dtlsData = sql_select("select a.color_size_break_down_id,
									sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN a.production_type=11 then a.production_qnty ELSE 0 END) as cur_production_qnty,
									sum(CASE WHEN a.production_type=11 then a.reject_qty ELSE 0 END) as reject_qty
									from pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,11) group by a.color_size_break_down_id");
									
		foreach($dtlsData as $row)
		{				  
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
		} 
		
		$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 $pack_type_cond order by color_number_id, id";
	}*/

	}


	else // if preceding process =0 in variable setting then plan cut quantity will show
	{
		if( $variableSettings==2 ) // color level
		{
			if($db_type==0)
			{
			
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and production_type=11 ) as production_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) group by color_number_id";
			}
			else
			{
				$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty
				from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.production_type=11
				where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";	
				
			}
		}
		else if( $variableSettings==3 ) //color and size level
		{				
				
			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=11 then a.production_qnty ELSE 0 END) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1  and b.status_active=1  and a.is_deleted= 0 and b.is_deleted=0  and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(11) group by a.color_size_break_down_id");
					
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
			} 
			
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order"; //color_number_id, id
			
			
		}
		else // by default color and size level
		{
			 
				
			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=11 then a.production_qnty ELSE 0 END) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(11) group by a.color_size_break_down_id");
					
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
			} 
			
			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order";//color_number_id, id 
		}
	}	
	//echo $sql;
	
	if($variableSettingsRej!=1){ $disable=""; }
	else{ $disable="disabled";}	
		
	$colorResult = sql_select($sql);		
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
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';				
				$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
				$colorID .= $color[csf("color_number_id")].",";
			}
			else //color and size level
			{
				if( !in_array( $color[csf("color_number_id")], $chkColor ) )
				{
					if( $i!=0 ) $colorHTML .= "</table></div>";
					$i=0;
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:250px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];					
				}
				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				
				$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
				$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
				$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
				if($qty_source==0){$iss_qnty=0;$rcv_qnty=0;$rej_qnty=0;$color[csf("order_quantity")]=0;}
				
				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:60px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:60px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
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
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut_qnty")]-$color[csf("production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';				
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
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];					
				}
						 $bundle_mst_data="";
						 $bundle_dtls_data="";
					 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";
 				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];
				
 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'></td><td><input type="button" name="button" id="button_'.$color[csf("color_number_id")].($i+1).'" value="Click For Bundle" class="formbutton" style="size:30px;" onclick="openmypage_bandle('.$color[csf("id")].','.$color[csf("color_number_id")].($i+1).','.$tmp_col_size.');" /></td></tr>';				
			}
			
			$i++; 
		}
	}
	
	//echo $colorHTML;die; 
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	exit();
}

 if($action=="show_dtls_listview")
{
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$poly_line_arr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];	
	$country_id = $dataArr[2];
	$prod_reso_allo = $dataArr[3];
	$pack_type = $dataArr[4];
	$variableSettings = $dataArr[5];
	if($pack_type=='') $pack_type_cond=""; else $pack_type_cond=" and pack_type='$pack_type'";
?>	 
     <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="20">SL</th>
                <th width="110">Item Name</th>
                <th width="80">Country</th>
                <th width="65">Prod. Date</th>
                <th width="60">QC Pass Qty</th>
                <th width="50">Alter Qty</th>
                <th width="50">Spot Qty</th> 
                <th width="50">Reject Qty</th>
                <th width="90">Serving Company</th> 
                <th width="50">Poly Line</th>
                <th width="50">Rep. Hour</th>
                <th width="80">Supervisor</th>
                <th width="50">Challan No</th>
                <th width="50"> Sys Chln No</th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="poly_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php  
			$i=1;
			$total_production_qnty=0;
			if($db_type==2)
			{
				if($variableSettings !=1)
				{
					$sqls="SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, sum(b.production_qnty) as production_quantity, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, TO_CHAR(a.production_hour,'HH24:MI') as prod_hour,a.challan_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and  a. po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='11' and b.production_type='11' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pack_type_cond group by a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo,  a.production_hour ,a.challan_no order by TO_CHAR(a.production_hour,'DD-MON-YYYY HH24:MI')";
				}
				else
				{
					$sqls="SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, sum(a.production_quantity) as production_quantity, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, TO_CHAR(a.production_hour,'HH24:MI') as prod_hour,a.challan_no from pro_garments_production_mst a where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='11' and a.status_active=1 and a.is_deleted=0 $pack_type_cond group by a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo,  a.production_hour ,a.challan_no order by TO_CHAR(a.production_hour,'DD-MON-YYYY HH24:MI')";
				}
				 
			}			 
			else 
			{
				if($variableSettings !=1)
				{
			 		$sqls="SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, b.production_qnty as production_quantity, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, TIME_FORMAT( a.production_hour, '%H:%i' ) as prod_hour,a.challan_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='11' and b.production_type='11' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pack_type_cond group by a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo,   a.production_hour,a.challan_no  order by a.production_date, a.production_hour";
			 	}
			 	else
			 	{
			 		$sqls="SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, a.production_quantity as production_quantity, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, TIME_FORMAT( a.production_hour, '%H:%i' ) as prod_hour,a.challan_no from pro_garments_production_mst a where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='11' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pack_type_cond group by a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo,   a.production_hour,a.challan_no  order by a.production_date, a.production_hour";
			 	}
				  
			}
			// echo $sql;
			$sqlResult =sql_select($sqls);
			foreach($sqlResult as $selectResult){
				
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$total_production_qnty+=$selectResult[csf('production_quantity')];
				
				$poly_line='';
				if($selectResult[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$selectResult[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($poly_line=='') $poly_line=$poly_line_arr[$val]; else $poly_line.=",".$poly_line_arr[$val];
					}
				}
				else $poly_line=$poly_line_arr[$selectResult[csf('sewing_line')]];
				
  		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $selectResult[csf('id')].'**'.$variableSettings; ?>','populate_input_form_data','requires/poly_entry_controller');" > 
				<td width="20" align="center"><? echo $i; ?></td>
                <td width="110" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                <td width="80" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                <td width="65" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                <td width="60" align="right"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
                <td width="50" align="right"><?php  echo $selectResult[csf('alter_qnty')]; ?></td>
                <td width="50" align="right"><?php  echo $selectResult[csf('spot_qnty')]; ?></td>
                <td width="50" align="right"><?php  echo $selectResult[csf('reject_qnty')]; ?></td>
				<?php
                        $source= $selectResult[csf('production_source')];
					   	if($source==3) $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
						else $serving_company= $company_arr[$selectResult[csf('serving_company')]];
                 ?>	
                <td width="90" style="padding-left:2px;"><?php echo $serving_company; ?></p></td>
                
                <td width="50" align="center"><p><? echo $poly_line; ?></p></td>
                <td width="50" align="center"><p><? echo $selectResult[csf('prod_hour')]; ?></p></td>
                <td width="80" align="center"><p><? echo $selectResult[csf('supervisor')]; ?>&nbsp;</p></td>
                <td width="50" align="center"><p><? echo $selectResult[csf('challan_no')]; ?></p></td>
                <td width="50" align="center"><p><? echo $selectResult[csf('id')]; ?></p></td>
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
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table">
        <thead>
            <th width="15">SL</th>
            <th width="100">Item Name</th>
            <th width="75">Country</th>
            <th width="55">Shipment Date</th>
            <th width="60">Order Qty.</th>
            <th width="30">Pack Type</th>
            <th>Poly Qty.</th>                    
        </thead>
		<? 
		$issue_qnty_arr=sql_select("select a.po_break_down_id, a.item_number_id, a.country_id, a.pack_type, b.production_qnty as cutting_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type=11 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("pack_type")]]+=$row[csf("cutting_qnty")];
		} 
		$i=1;
		$sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, pack_type, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id, pack_type order by country_ship_date Asc");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$issue_qnty=$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("pack_type")]];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')].",'".$row[csf('pack_type')]."'"; ?>);"> 
				<td width="15"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="75"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="55" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="60"><?php  echo $row[csf('order_qnty')]; ?></td>
                <td width="60"><?php  echo $row[csf('pack_type')]; ?>&nbsp;</td>
                <td align="right"><?php  echo $issue_qnty; ?></td>
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
	//production type=5 come from array
	$data_ex = explode("**", $data);
	$id = $data_ex[0];
	$variableSettings = $data_ex[1];
	if($db_type==2)
	{
		if($variableSettings !=1)
		{
			$sql_dtls ="SELECT a.id,a.company_id, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.pack_type, a.production_source, a.produced_by, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date, sum(b.production_qnty) as production_quantity,  a.production_type, a.entry_break_down_type, a.break_down_type_rej, TO_CHAR(a.production_hour,'HH24:MI') as production_hour,  a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id,a.currency_id, a.exchange_rate, a.rate,b.color_type_id from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and  a.id='$id' and a.production_type='11'   and b.production_type='11'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.company_id, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.pack_type, a.production_source, a.produced_by, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date,     a.production_type, a.entry_break_down_type, a.break_down_type_rej,a.production_hour,  a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id,a.currency_id, a.exchange_rate, a.rate,b.color_type_id   order by a.id";
		}
		else
		{
			$sql_dtls ="SELECT a.id,a.company_id, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.pack_type, a.production_source, a.produced_by, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date, sum(a.production_quantity) as production_quantity,  a.production_type, a.entry_break_down_type, a.break_down_type_rej, TO_CHAR(a.production_hour,'HH24:MI') as production_hour,  a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id,a.currency_id, a.exchange_rate, a.rate from pro_garments_production_mst a where a.id='$id' and a.production_type='11' and a.status_active=1 and a.is_deleted=0 group by a.id,a.company_id, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.pack_type, a.production_source, a.produced_by, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date,     a.production_type, a.entry_break_down_type, a.break_down_type_rej,a.production_hour,  a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id,a.currency_id, a.exchange_rate, a.rate   order by a.id";
		}
	}
	else
	{
		if($variableSettings !=1)
		{
			$sql_dtls ="SELECT a.id,a.company_id, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.pack_type, a.production_source, a.produced_by, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date, sum(b.production_qnty) as production_quantity,  a.production_type, a.entry_break_down_type, a.break_down_type_rej, TIME_FORMAT( a.production_hour, '%H:%i' ) as production_hour, a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id,a.currency_id, a.exchange_rate, a.rate,b.color_type_id from pro_garments_production_mst a,pro_garments_production_dtls b where  a.id=b.mst_id and  a.id='$id' and a.production_type='11'   and b.production_type='11'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.company_id, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.pack_type, a.production_source, a.produced_by, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date,  a.production_type, a.entry_break_down_type, a.break_down_type_rej,   a.production_hour, a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id,a.currency_id, a.exchange_rate, a.rate,b.color_type_id   order by a.id";
		}
		else
		{
			$sql_dtls ="SELECT a.id,a.company_id, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.pack_type, a.production_source, a.produced_by, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date, sum(a.production_quantity) as production_quantity,  a.production_type, a.entry_break_down_type, a.break_down_type_rej, TIME_FORMAT( a.production_hour, '%H:%i' ) as production_hour, a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id,a.currency_id, a.exchange_rate, a.rate from pro_garments_production_mst a where  a.id='$id' and a.production_type='11' and a.status_active=1 and a.is_deleted=0 group by a.id,a.company_id, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.pack_type, a.production_source, a.produced_by, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date,  a.production_type, a.entry_break_down_type, a.break_down_type_rej,   a.production_hour, a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id,a.currency_id, a.exchange_rate, a.rate   order by a.id";
		}
	}
  	  // echo  $sql_dtls;
	$sqlResult =sql_select($sql_dtls); 
	if($sqlResult[0][csf('production_source')]==1)
	{
		$company_id=$sqlResult[0][csf('serving_company')];
	}
	else
	{
		$company_id=$sqlResult[0][csf('company_id')];
	}
 	
	$variable_qty_source_poly=return_field_value("qty_source_poly","variable_settings_production","company_name=$company_id and variable_list=42","qty_source_poly");
	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=103 and company_name='$company_id'");  
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
 	
	//$qty_source=0;
	/*if($variable_qty_source_poly==1) $qty_source=5; //Sweing Output
	else if($variable_qty_source_poly==2) $qty_source=7;//Iron Output*/

	$qty_source=0;
	if($preceding_process==28) $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry
 	 
 		foreach($sqlResult as $result)
		{ 
			echo "$('#txt_poly_date').val('".change_date_format($result[csf('production_date')])."');\n";
			echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
			echo "load_drop_down( 'requires/poly_entry_controller', ".$result[csf('production_source')].", 'load_drop_down_poly_output', 'sew_company_td' );\n";
			echo "$('#cbo_poly_company').val('".$result[csf('serving_company')]."');\n";
			echo "load_drop_down( 'requires/poly_entry_controller',".$result[csf('serving_company')].", 'load_drop_down_location', 'location_td' );";

			echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
			echo "load_drop_down( 'requires/poly_entry_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
			echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";

			echo "load_drop_down( 'requires/poly_entry_controller', ".$result[csf('po_break_down_id')].", 'load_drop_down_color_type', 'color_type_td' );\n";
			echo "$('#cbo_color_type').val('".$result[csf('color_type_id')]."');\n";
			
			echo "load_drop_down( 'requires/poly_entry_controller', document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_poly_date').value, 'load_drop_down_poly_line_floor', 'poly_line_td' );\n";
			
			echo "$('#cbo_poly_line').val('".$result[csf('sewing_line')]."');\n";
			echo "get_php_form_data(".$result[csf('production_source')].",'line_disable_enable','requires/poly_entry_controller');\n";
			
			if($result[csf('production_source')]==3)
			{
				$company_id=$sqlResult[0][csf('company_id')];
			 	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=103 and company_name='$company_id'");  
				$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
				echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
 	 			echo "load_drop_down( 'requires/poly_entry_controller', '".$result[csf('company_id')]."_".$result[csf('serving_company')]."_".$result[csf('po_break_down_id')]."', 'load_drop_down_workorder', 'workorder_td' );\n";
				
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
			$pack_type=$result[csf('pack_type')];
			echo "$('#cbo_produced_by').val('".$result[csf('produced_by')]."');\n";
			echo "$('#txt_reporting_hour').val('".$result[csf('production_hour')]."');\n";
			echo "$('#txt_super_visor').val('".$result[csf('supervisor')]."');\n";
			echo "$('#txt_poly_qty').val('".$result[csf('production_quantity')]."');\n";
			echo "$('#txt_reject_qnty').val('".$result[csf('reject_qnty')]."');\n";
			echo "$('#txt_alter_qnty').val('".$result[csf('alter_qnty')]."');\n";
			echo "$('#txt_spot_qnty').val('".$result[csf('spot_qnty')]."');\n";
			echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
			echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
			if($pack_type=='') $pack_typeCond=""; else $pack_typeCond="and pack_type='$pack_type'";
			if($qty_source!=0)
			{
				$dataArray=sql_select("select SUM(CASE WHEN a.production_type='$qty_source' and b.production_type='$qty_source' THEN  b.production_qnty END) as totalinput,SUM(CASE WHEN a.production_type=11 and b.production_type=11 THEN b.production_qnty ELSE 0 END) as totalpoly from pro_garments_production_mst a,pro_garments_production_dtls b WHERE a.id=b.mst_id and a.po_break_down_id=".$result[csf('po_break_down_id')]." and a.item_number_id=".$result[csf('item_number_id')]." and a.country_id=".$result[csf('country_id')]." $pack_typeCond and a.production_source=".$result[csf('production_source')]." and a.serving_company=".$result[csf('serving_company')]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		  		foreach($dataArray as $row)
				{  
					echo "$('#txt_input_quantity').val('".$row[csf('totalinput')]."');\n";
					echo "$('#txt_cumul_poly_qty').val('".$row[csf('totalpoly')]."');\n";
					$yet_to_produced = $row[csf('totalinput')]-$row[csf('totalpoly')];
					echo "$('#txt_yet_to_poly').val('".$yet_to_produced."');\n";
				}

			}
			else
			{  
				$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and status_active in(1,2,3) and is_deleted=0");
		
				$total_produced = return_field_value("sum(b.production_qnty)","pro_garments_production_mst a,pro_garments_production_dtls b","a.id=b.mst_id and  a.po_break_down_id=".$result[csf('po_break_down_id')]." and a.item_number_id=".$result[csf('item_number_id')]." and a.country_id=".$result[csf('country_id')]." and a.production_type=11 and b.production_type=11  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				echo "$('#txt_input_quantity').val('".$plan_cut_qnty."');\n";	
				echo "$('#txt_cumul_poly_qty').val('".$total_produced."');\n";
				$yet_to_produced = $plan_cut_qnty - $total_produced;
				echo "$('#txt_yet_to_poly').val('".$yet_to_produced."');\n";
			}
			
			$dft_id=""; $alt_save_data=""; $spt_save_data=""; $rej_save_data=""; $altType_id=""; $sptType_id="";$rejType_id=""; $altpoint_id=""; $sptpoint_id="";$rejpoint_id="";
			$defect_sql=sql_select("select id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0 and production_type='11'");
			foreach($defect_sql as $dft_row)
			{
				if($dft_row[csf('defect_type_id')]==1)
				{
					if($alt_save_data=="") $alt_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $alt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
					if($altpoint_id=="") $altpoint_id=$dft_row[csf('defect_point_id')]; else $altpoint_id.=','.$dft_row[csf('defect_point_id')];
					$altType_id=$dft_row[csf('defect_type_id')];
				}
				
				if($dft_row[csf('defect_type_id')]==2)
				{
					if($spt_save_data=="") $spt_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $spt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
					if($sptpoint_id=="") $sptpoint_id=$dft_row[csf('defect_point_id')]; else $sptpoint_id.=','.$dft_row[csf('defect_point_id')];
					$sptType_id=$dft_row[csf('defect_type_id')];
				}
				
				if($dft_row[csf('defect_type_id')]==3)
				{
					if($rej_save_data=="") $rej_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $rej_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
					if($rejpoint_id=="") $rejpoint_id=$dft_row[csf('defect_point_id')]; else $rejpoint_id.=','.$dft_row[csf('defect_point_id')];
					$rejType_id=$dft_row[csf('defect_type_id')];
				}
			}
			echo "$('#save_data').val('".$alt_save_data."');\n";
			echo "$('#all_defect_id').val('".$altpoint_id."');\n";
			echo "$('#defect_type_id').val('".$altType_id."');\n";
			
			echo "$('#save_dataSpot').val('".$spt_save_data."');\n";
			echo "$('#allSpot_defect_id').val('".$sptpoint_id."');\n";
			echo "$('#defectSpot_type_id').val('".$sptType_id."');\n";
			
			echo "$('#save_dataReject').val('".$rej_save_data."');\n";
			echo "$('#allReject_defect_id').val('".$rejpoint_id."');\n";
			echo "$('#defectReject_type_id').val('".$rejType_id."');\n";
			
			echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
			echo "$('#txt_sys_chln').val('".$result[csf('id')]."');\n";
	 		echo "set_button_status(1, permission, 'fnc_poly_output_entry',1,1);\n";
			
			//break down of color and size------------------------------------------
	 		//#############################################################################################//
			// order wise - color level, color and size level
			$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
			$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

			$variableSettings = $result[csf('entry_break_down_type')];
			$variableSettingsRej = $result[csf('break_down_type_rej')];
			
			//$variableSettings=2;
			if($pack_type=='') $pack_typeCond=""; else $pack_typeCond="and pack_type='$pack_type'";
			if($pack_type!='') $pack_cond="and b.pack_type='$pack_type'"; else $pack_cond="";
			if($pack_type=='') $packTypecond=""; else $packTypecond="and a.pack_type='$pack_type'";
			if($qty_source!=0)
			{
				if( $variableSettings!=1 ) // gross level
				{ 
					$po_id = $result[csf('po_break_down_id')];
					$item_id = $result[csf('item_number_id')];
					$country_id = $result[csf('country_id')];
					$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from  pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id=$id and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $pack_cond");	
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
							
							$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type='$qty_source' and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=11 and cur.is_deleted=0) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=11 and cur.is_deleted=0 ) as reject_qty
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) $pack_typeCond group by color_number_id";
						}
						else
						{
							$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
									sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN c.production_type=11 then b.production_qnty ELSE 0 END) as cur_production_qnty,
									sum(CASE WHEN c.production_type=11 then b.reject_qty ELSE 0 END) as reject_qty
									from wo_po_color_size_breakdown a 
									left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
									left join pro_garments_production_mst c on c.id=b.mst_id
									where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) $packTypecond group by a.item_number_id, a.color_number_id";	
						}
					}
					else if( $variableSettings==3 ) //color and size level
					{
							$dtlsData = sql_select("select a.color_size_break_down_id,
												sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
												sum(CASE WHEN a.production_type=11 then a.production_qnty ELSE 0 END) as cur_production_qnty,
												sum(CASE WHEN a.production_type=11 then a.reject_qty ELSE 0 END) as reject_qty
												from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,11) $pack_cond group by a.color_size_break_down_id ");
												
							foreach($dtlsData as $row)
							{				  
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
							} 
							//print_r($color_size_qnty_array);
							
							$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
								from wo_po_color_size_breakdown
								where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) $pack_typeCond order by color_number_id, size_order";
					}
					else // by default color and size level
					{
							
							$dtlsData = sql_select("select a.color_size_break_down_id,
												sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
												sum(CASE WHEN a.production_type=11 then a.production_qnty ELSE 0 END) as cur_production_qnty,
												sum(CASE WHEN a.production_type=11 then a.reject_qty ELSE 0 END) as reject_qty
												from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in ($qty_source,11) $pack_cond group by a.color_size_break_down_id");
												
							foreach($dtlsData as $row)
							{				  
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
							} 
						$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) $pack_typeCond order by color_number_id,size_order";
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
							$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onblur="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';				
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
							$rej_qnty=$rejectArr[$index];
							//$color_size_qnty_array[$color[csf('id')]]['rej'];
							
							$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
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
				}
			}

			if($qty_source==0)
			{
				if( $variableSettings!=1 ) // gross level
				{ 
					$po_id = $result[csf('po_break_down_id')];
					$item_id = $result[csf('item_number_id')];
					$country_id = $result[csf('country_id')];
					
					
					$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$id and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");	

					foreach($sql_dtls as $row)
					{				  
						if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')]."*".$row[csf('color_number_id')];
					  	$amountArr[$index] = $row[csf('production_qnty')];
						$rejectArr[$index] = $row[csf('reject_qty')];
					}  
					
					if( $variableSettings==2 ) // color level
					{
						if($db_type==0)
						{
							
							$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then production_qnty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and  	production_type=11 ) as production_qnty, (select sum(CASE WHEN pro_garments_production_dtls.color_size_break_down_id=wo_po_color_size_breakdown.id then reject_qty ELSE 0 END) from pro_garments_production_dtls where is_deleted=0 and production_type=11 ) as reject_qty
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 group by color_number_id";
						}
						else
						{
							$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty, sum(b.reject_qty) as reject_qty
						from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.production_type=11
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3) group by a.item_number_id, a.color_number_id";	
						
						}
					}
					else if( $variableSettings==3 ) //color and size level
					{
						
							$dtlsData = sql_select("select a.color_size_break_down_id,
												sum(CASE WHEN a.production_type=11 then a.production_qnty ELSE 0 END) as production_qnty,
												sum(CASE WHEN a.production_type=11 then a.reject_qty ELSE 0 END) as reject_qty
												from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id  and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(11) group by a.color_size_break_down_id");
							//and b.id='$data'

							foreach($dtlsData as $row)
							{				  
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
								$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
							} 
							
							$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order"; 
							
							
					}
					else // by default color and size level
					{
						
							
						$dtlsData = sql_select("select a.color_size_break_down_id,
												sum(CASE WHEN a.production_type=11 then a.production_qnty ELSE 0 END) as production_qnty,
												sum(CASE WHEN a.production_type=11 then a.reject_qty ELSE 0 END) as reject_qty
												from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(11) group by a.color_size_break_down_id");	
												
						foreach($dtlsData as $row)
						{				  
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
							$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
						}  
						//print_r($color_size_qnty_array);
						
						$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
							from wo_po_color_size_breakdown
							where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order";
							
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
							$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut_qnty")]-$color[csf("production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onblur="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';				
							$totalQnty += $amount;
							$totalRejQnty += $rejectAmt;
							$colorID .= $color[csf("color_number_id")].",";
						}
						else //color and size level
						{
							$index = $color[csf("size_number_id")]."*".$color[csf("color_number_id")];
							
							$amount = $amountArr[$index];
							//$amount = $color[csf("size_number_id")]."*".$color[csf("color_number_id")];
							if( !in_array( $color[csf("color_number_id")], $chkColor ) )
							{
								if( $i!=0 ) $colorHTML .= "</table></div>";
								$i=0;
								$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].': <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
								$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
								$chkColor[] = $color[csf("color_number_id")];
								$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
								
							}
							
							
							 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";					
		 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
							$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];
 							$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
							 
							
							$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($color[csf("plan_cut_qnty")]-$cut_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'></td><td><input type="button" name="button" value="Click For Bundle" class="formbutton" style="size:30px;" onclick="openmypage_bandle('.$color[csf("id")].','.$color[csf("color_number_id")].($i+1).','.$tmp_col_size.');" /></td></tr>';				
							//$colorWiseTotal += $amount;
							 $bundle_dtls_data="";
							 $bundle_dtls_data="";
						}
						$i++; 
					}
					//echo $colorHTML;die; 
					if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$result[csf('production_quantity')].'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
					echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
					if( $variableSettings==3 )echo "$totalFn;\n";
					$colorList = substr($colorID,0,-1);
					echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
				}
			}
			
		}
 	 
 	 
 	exit();		
}

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
	if(!str_replace("'","",$poly_production_variable)) $poly_production_variable=3;
	//$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=103");
	//$variable_qty_source_poly=return_field_value("qty_source_poly","variable_settings_production","company_name=$cbo_company_name and variable_list=42","qty_source_poly");
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=103 and company_name='$cbo_company_name'");  
    $preceding_process=$control_and_preceding[0][csf("preceding_page_id")];
	$qty_source=0;
	if($preceding_process==28)  $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here   entry form 160 = production all pages
		//if  ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}
 		
		
		//----------Compare by finishing qty and iron qty qty for validation----------------
		
		/*if($is_control==1 && $user_level!=2)
		{
			$txt_poly_qty=str_replace("'","",$txt_poly_qty);
			$country_poly_input_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=4 and country_id=$cbo_country_name and status_active=1 and is_deleted=0");
			
			$country_poly_output_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=5 and country_id=$cbo_country_name and status_active=1 and is_deleted=0");
			
			//echo $country_poly_output_qty .'<'. $country_iron_qty.'+'.$txt_iron_qty;die;
			if($country_poly_input_qty < $country_poly_output_qty+$txt_poly_qty)
			{
				echo "25**0";
				disconnect($con);
				die;
			}
		
		}*/
		//--------------------------------------------------------------Compare end;
		
		
		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		 $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
		/*if(str_replace("'","",$cbo_time)==1) 
		{
			if(str_replace("'","",$txt_reporting_hour)==12) $reportTime = 12+str_replace("'","",$txt_reporting_hour); else $reportTime = $txt_reporting_hour; 
		}
		else 
		{
			if(str_replace("'","",$txt_reporting_hour)==12) $reportTime = $txt_reporting_hour; else $reportTime = 12+str_replace("'","",$txt_reporting_hour);
		}*/
		
		$field_array1="id, garments_nature, company_id, challan_no, po_break_down_id, item_number_id, country_id, pack_type, production_source, serving_company, location, produced_by, production_date, production_quantity, production_type, entry_break_down_type, break_down_type_rej, sewing_line, supervisor, production_hour, remarks, floor_id, alter_qnty, reject_qnty, total_produced, yet_to_produced, prod_reso_allo, spot_qnty,wo_order_id,currency_id, exchange_rate, rate, amount, inserted_by, insert_date";
		
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_poly_qty);}
		
		if($db_type==0)
		{
			$data_array1="(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$txt_pack_type.",".$cbo_source.",".$cbo_poly_company.",".$cbo_location.",".$cbo_produced_by.",".$txt_poly_date.",".$txt_poly_qty.",11,".$poly_production_variable.",".$poly_production_variable_rej.",".$cbo_poly_line.",".$txt_super_visor.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_alter_qnty.",".$txt_reject_qnty.",".$txt_cumul_poly_qty.",".$txt_yet_to_poly.",".$prod_reso_allo.",".$txt_spot_qnty.",".$cbo_work_order.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."')";
		}
		else
		{
			$txt_reporting_hour=str_replace("'","",$txt_poly_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			$data_array1="INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES (".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$txt_pack_type.",".$cbo_source.",".$cbo_poly_company.",".$cbo_location.",".$cbo_produced_by.",".$txt_poly_date.",".$txt_poly_qty.",11,".$poly_production_variable.",".$poly_production_variable_rej.",".$cbo_poly_line.",".$txt_super_visor.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_alter_qnty.",".$txt_reject_qnty.",".$txt_cumul_poly_qty.",".$txt_yet_to_poly.",".$prod_reso_allo.",".$txt_spot_qnty.",".$cbo_work_order.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."')";
		}
		
 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		//echo $data_array;die;
		
		
		// pro_garments_production_dtls table entry here ----------------------------------///
		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,color_type_id";
		
		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and b.pack_type=$txt_pack_type";
		$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=11 then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b 
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in($qty_source,11) $pack_type_cond
										group by a.color_size_break_down_id");
		$color_pord_data=array();							
		foreach($dtlsData as $row)
		{				  
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}
  		if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type";
		if(str_replace("'","",$poly_production_variable)==2)//color level wise
		{		
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id<>0 and status_active in(1,2,3) and is_deleted=0 $packType_cond order by id" );
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
				
				if($is_control==1 && $user_level!=2)
				{
					if($colorSizeNumberIDArr[1]>0)
					{
						if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
						{
							echo "35**Production Quantity Not Over poly Input Qnty";
							//check_table_status( 160,0);
							disconnect($con);
							die;
						}
					}
				}
				
				
				//11for poly Input Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
				if($j==0)$data_array = "(".$dtls_id.",".$id.",11,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
				else $data_array .= ",(".$dtls_id.",".$id.",11,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
				//$dtls_id=$dtls_id+1;							
 				$j++;								
			}
 		}//color level wise
		
		if(str_replace("'","",$poly_production_variable)==3)//color and size wise
		{		
				
			$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0 $packType_cond order by size_number_id,color_number_id" );
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
		//	$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];				
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$colorID;
				
				if($is_control==1 && $user_level!=2)
				{
					if($colorSizeValue>0)
					{
						if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
						{
							echo "35**Production Quantity Not Over poly Input Qnty";
							//check_table_status( 160,0);
							disconnect($con);
							die;
						}
					}
				}
				
 				
				//4 for poly Input Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
				if($j==0)$data_array = "(".$dtls_id.",".$id.",11,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
				else $data_array .= ",(".$dtls_id.",".$id.",11,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
				//$dtls_id=$dtls_id+1;
 				$j++;
			}
		}//color and size wise
		
		/*===================================================================
		/								Alter 								/
		/==================================================================*/
		$defectQ=true; 
		$data_array_defect="";
		$save_string=explode(",",str_replace("'","",$save_data));
		
		if(count($save_string)>0 && str_replace("'","",$save_data)!="")  
		{		 
 			//order_wise_pro_details table data insert START-----//  
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
			}
			$i=0; 
			foreach($defect_array as $key=>$val)
			{
				if( $i>0 ) $data_array_defect.=",";
				if( $dft_id=="" ) 
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con ); 

					
				} 
				else
				{
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con ); 

				}  
				$defectPointId=$key;
				$defect_qty=$val;
				$data_array_defect.="(".$dft_id.",".$id.",11,".$hidden_po_break_down_id.",".$defect_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}
		if($data_array_defect!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defect.") VALUES ".$data_array_defect."";// die;
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}
		/*===================================================================
		/								Spot 								/
		/==================================================================*/
		$defectSpot=true;
		$data_array_defectsp="";
		$save_dataSpot=explode(",",str_replace("'","",$save_dataSpot));
		if(count($save_dataSpot)>0 && str_replace("'","",$save_dataSpot)!="")  
		{		 
 			//order_wise_pro_details table data insert START-----//  
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectSpt_array=array(); 
			for($i=0;$i<count($save_dataSpot);$i++)
			{
				$order_dtls=explode("**",$save_dataSpot[$i]);			
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];
				
				if( array_key_exists($defectsp_point_id,$defectSpt_array) )
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0; 
			foreach($defectSpt_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectsp.=",";
				//if( $dftSp_id=="" ) $dftSp_id=return_next_id("id", "pro_gmts_prod_dft", 1); else $dftSp_id = $dftSp_id+1;
				$dftSp_id=return_next_id_by_sequence("PRO_GMTS_PROD_DFT_SEQ", "pro_gmts_prod_dft", $con);
				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_defectsp.="(".$dftSp_id.",".$id.",11,".$hidden_po_break_down_id.",".$defectSpot_type_id.",".$defectspPointId.",'".$defectsp_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}
		
		if($data_array_defectsp!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_defectsp.""; die;
			$defectSpot=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_defectsp,0);
		}
		/*===================================================================
		/								Reject 								/
		/==================================================================*/
		$defectReject=true;
		$data_array_reject="";
		$save_dataReject=explode(",",str_replace("'","",$save_dataReject));
		if(count($save_dataReject)>0 && str_replace("'","",$save_dataReject)!="")  
		{		 
 			//order_wise_pro_details table data insert START-----//  
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectSpt_array=array(); 
			for($i=0;$i<count($save_dataReject);$i++)
			{
				$order_dtls=explode("**",$save_dataReject[$i]);			
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];
				
				if( array_key_exists($defectsp_point_id,$defectSpt_array) )
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0; 
			foreach($defectSpt_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_reject.=",";
				//if( $dftSp_id=="" ) $dftSp_id=return_next_id("id", "pro_gmts_prod_dft", 1); else $dftSp_id = $dftSp_id+1;
				$dftRej_id=return_next_id_by_sequence("PRO_GMTS_PROD_DFT_SEQ", "pro_gmts_prod_dft", $con);
				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_reject.="(".$dftRej_id.",".$id.",11,".$hidden_po_break_down_id.",".$defectReject_type_id.",".$defectspPointId.",'".$defectsp_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}
		
		if($data_array_reject!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_reject.""; die;
			$defectSpot=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_reject,0);
		}


		
		if($db_type==2)
		{
			$rID=execute_query($data_array1);
		}
		else
		{
			$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		} 
		
		if(str_replace("'","",$poly_production_variable)==2 || str_replace("'","",$poly_production_variable)==3)
		{
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
		//release lock table
		// echo "10**$rID**$dtlsrID";die();
		
		if($db_type==0)
		{	  
			if(str_replace("'","",$poly_production_variable)!=1)
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
			if(str_replace("'","",$poly_production_variable)!=1)
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
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		//table lock here   entry form 160 = production all pages
		// if  ( check_table_status( 160, 1 )==0 ) { echo "15**1"; die;}	
 
 		// pro_garments_production_mst table data entry here 
		//if(str_replace("'","",$cbo_time)==1)$reportTime = $txt_reporting_hour;else $reportTime = 12+str_replace("'","",$txt_reporting_hour);
		/*if(str_replace("'","",$cbo_time)==1) 
		{
			if(str_replace("'","",$txt_reporting_hour)==12) $reportTime = 12+str_replace("'","",$txt_reporting_hour); else $reportTime = $txt_reporting_hour; 
		}
		else 
		{
			if(str_replace("'","",$txt_reporting_hour)==12) $reportTime = $txt_reporting_hour; else $reportTime = 12+str_replace("'","",$txt_reporting_hour);
		}*/
		
		//----------Compare by finishing qty and iron qty qty for validation----------------
		/*if($is_control==1 && $user_level!=2)
		{
			$txt_poly_qty=str_replace("'","",$txt_poly_qty);
			$txt_mst_id=str_replace("'","",$txt_mst_id);
			$country_poly_input_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=4 and country_id=$cbo_country_name and status_active=1 and is_deleted=0");
			
			$country_poly_output_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=5 and country_id=$cbo_country_name and status_active=1 and is_deleted=0 and id <> $txt_mst_id");
			
			// echo $country_poly_input_qty .'<'. $country_poly_output_qty.'+'.$txt_poly_qty;die;
			if($country_poly_input_qty < $country_poly_output_qty+$txt_poly_qty)
			{
				echo "25**0";
				disconnect($con);
				die;
			}
		
		}*/
		//--------------------------------------------------------------Compare end;
		
	$sql_result=sql_select("select b.bundle_no from  pro_garments_production_mst a,pro_garments_production_dtls b where  a.id=b.mst_id and a.id =$txt_mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
		foreach($sql_result as $row)
		{				  
			if($row[csf('bundle_no')]!=''){$check_bundle_no[$row[csf('bundle_no')]]=$row[csf('bundle_no')];}
		}
		if(count($check_bundle_no) > 0){echo "101**".implode(',',$check_bundle_no);disconnect($con);exit();}
		
		
		
		if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_poly_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
			
 		$field_array1="pack_type*production_source*serving_company*location*produced_by*production_date*production_quantity*production_type*entry_break_down_type* break_down_type_rej*sewing_line*supervisor*production_hour*challan_no*remarks*floor_id*reject_qnty*alter_qnty*total_produced*yet_to_produced*prod_reso_allo*spot_qnty*wo_order_id*currency_id *exchange_rate*rate*amount*updated_by*update_date";
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_sewing_qty);}
		else {$amount="";}
		
		$data_array1="".$txt_pack_type."*".$cbo_source."*".$cbo_poly_company."*".$cbo_location."*".$cbo_produced_by."*".$txt_poly_date."*".$txt_poly_qty."*11*".$poly_production_variable."*".$poly_production_variable_rej."*".$cbo_poly_line."*".$txt_super_visor."*".$txt_reporting_hour."*".$txt_challan."*".$txt_remark."*".$cbo_floor."*".$txt_reject_qnty."*".$txt_alter_qnty."*".$txt_cumul_poly_qty."*".$txt_yet_to_poly."*".$prod_reso_allo."*".$txt_spot_qnty."*".$cbo_work_order."*".$hidden_currency_id."*".$hidden_exchange_rate."*'".$rate."'*'".$amount."'*".$user_id."*'".$pc_date_time."'";
		
		//echo "10**".$field_array1.'=='.$data_array1;die;
 		//$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		//
		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and b.pack_type=$txt_pack_type";
		// pro_garments_production_dtls table data entry here 
		if(str_replace("'","",$poly_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) // check is not gross level
		{
			
			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=11 then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b 
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in($qty_source,11) and b.id!=$txt_mst_id $pack_type_cond
										group by a.color_size_break_down_id");
			$color_pord_data=array();							
			foreach($dtlsData as $row)
			{				  
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			
			
 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,color_type_id";
			if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type";
			if(str_replace("'","",$poly_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 and status_active in(1,2,3) and is_deleted=0 $packType_cond order by id" );
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
					if($is_control==1 && $user_level!=2)
					{
						if($colorSizeNumberIDArr[1]>0)
						{
							if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
							{
								echo "35**Production Quantity Not Over poly Input Qnty";
								//check_table_status( 160,0);
								disconnect($con);
								die;
							}
						}
					}
					
					//4 for poly Input Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",11,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",11,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
					//$dtls_id=$dtls_id+1;							
					$j++;								
				}
			}
			
			if(str_replace("'","",$poly_production_variable)==3)//color and size wise
			{		
					
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0 $packType_cond order by size_number_id,color_number_id" );
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
					
					if($is_control==1 && $user_level!=2)
					{
						if($colorSizeValue>0)
						{
							if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
							{
								echo "35**Production Quantity Not Over Poly Input Qnty";
								//check_table_status( 160,0);
								disconnect($con);
								die;
							}
						}
					}
					//4 for poly Input Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",11,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",11,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
 			//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}//end cond
		//echo "10**".$data_array;die;
		
		$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);

		/*===================================================================
		/								Alter    							/
		/==================================================================*/
		$defectQ=true; 
		$data_array_defect="";
		$save_string=explode(",",str_replace("'","",$save_data));
		
		if(count($save_string)>0 && str_replace("'","",$save_data)!="")  
		{		 
 			//order_wise_pro_details table data insert START-----//  
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
			}
			$i=0; 
			foreach($defect_array as $key=>$val)
			{
				if( $i>0 ) $data_array_defect.=",";
				if( $dft_id=="" ) 
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con ); 

					
				} 
				else
				{
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con ); 

				}  
				$defectPointId=$key;
				$defect_qty=$val;
				$data_array_defect.="(".$dft_id.",".$txt_mst_id.",11,".$hidden_po_break_down_id.",".$defect_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}
		if($data_array_defect!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defect.") VALUES ".$data_array_defect."";// die;
			//echo "5**DELETE FROM pro_gmts_prod_dft WHERE mst_id='$txt_mst_id' and defect_type_id=1";die;
			$query3=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=1 and production_type=11");
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}
		/*===================================================================
		/								Spot    							/
		/==================================================================*/
		$defectSpot=true;
		$data_array_defectsp="";
		$save_dataSpot=explode(",",str_replace("'","",$save_dataSpot));
		if(count($save_dataSpot)>0 && str_replace("'","",$save_dataSpot)!="")  
		{		 
 			//order_wise_pro_details table data insert START-----//  
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectSpt_array=array(); 
			for($i=0;$i<count($save_dataSpot);$i++)
			{
				$order_dtls=explode("**",$save_dataSpot[$i]);			
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];
				
				if( array_key_exists($defectsp_point_id,$defectSpt_array) )
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0; 
			foreach($defectSpt_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectsp.=",";
				//if( $dftSp_id=="" ) $dftSp_id=return_next_id("id", "pro_gmts_prod_dft", 1); else $dftSp_id = $dftSp_id+1;
				$dftSp_id=return_next_id_by_sequence("PRO_GMTS_PROD_DFT_SEQ", "pro_gmts_prod_dft", $con);
				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_defectsp.="(".$dftSp_id.",".$txt_mst_id.",11,".$hidden_po_break_down_id.",".$defectSpot_type_id.",".$defectspPointId.",'".$defectsp_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}
		
		if($data_array_defectsp!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_defectsp.""; die;
			//echo "DELETE FROM pro_gmts_prod_dft WHERE mst_id='$txt_mst_id' and defect_type_id=2";die;
			$query4=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=2 and production_type=11");
			$defectSpot=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_defectsp,0);
		}
		/*===================================================================
		/								Reject    							/
		/==================================================================*/
		$defectReject=true;
		$data_array_defectsp="";
		$save_dataReject=explode(",",str_replace("'","",$save_dataReject));
		if(count($save_dataReject)>0 && str_replace("'","",$save_dataReject)!="")  
		{		 
 			//order_wise_pro_details table data insert START-----//  
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectSpt_array=array(); 
			for($i=0;$i<count($save_dataReject);$i++)
			{
				$order_dtls=explode("**",$save_dataReject[$i]);			
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];
				
				if( array_key_exists($defectsp_point_id,$defectSpt_array) )
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0; 
			foreach($defectSpt_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_reject.=",";
				//if( $dftSp_id=="" ) $dftSp_id=return_next_id("id", "pro_gmts_prod_dft", 1); else $dftSp_id = $dftSp_id+1;
				$dftSp_id=return_next_id_by_sequence("PRO_GMTS_PROD_DFT_SEQ", "pro_gmts_prod_dft", $con);
				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_reject.="(".$dftSp_id.",".$txt_mst_id.",11,".$hidden_po_break_down_id.",".$defectReject_type_id.",".$defectspPointId.",'".$defectsp_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			} 
		}
		
		if($data_array_reject!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_reject.""; die;
			//echo "DELETE FROM pro_gmts_prod_dft WHERE mst_id='$txt_mst_id' and defect_type_id=2";die;
			$query4=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=2 and production_type=11");
			$defectSpot=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_reject,0);
		}

		
		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		
		if(str_replace("'","",$poly_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
		
		//release lock table
	
		
		if($db_type==0)
		{
			if(str_replace("'","",$poly_production_variable)!=1)
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
			if(str_replace("'","",$poly_production_variable)!=1)
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

if($action=="poly_output_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$poly_library=return_library_array( "select id, line_name from  lib_sewing_line", "id", "line_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$line_data_variable=return_library_array("select id, line_number from prod_resource_mst", "id","line_number");
	
	$job_array=array();
	$job_sql="SELECT a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
	}
	
	if($db_type==2)
	{
		$sql="select id, company_id, challan_no, sewing_line, po_break_down_id, item_number_id,entry_break_down_type,break_down_type_rej, country_id, production_source, produced_by, serving_company, location, embel_name, embel_type, production_date, TO_CHAR(production_hour,'HH24:MI') as production_hour, production_quantity, production_type, remarks, floor_id, sewing_line, alter_qnty, reject_qnty, spot_qnty from pro_garments_production_mst where production_type=11 and id='$data[1]' and status_active=1 and is_deleted=0 ";
	}
	else
	{
		$sql="select id, company_id, challan_no, sewing_line, po_break_down_id, item_number_id, country_id, entry_break_down_type,break_down_type_rej,production_source, produced_by, serving_company, location, embel_name, embel_type, production_date, TIME_FORMAT( production_hour, '%H:%i' ) as production_hour, production_quantity, production_type, remarks, floor_id, sewing_line, alter_qnty, reject_qnty, spot_qnty from pro_garments_production_mst where production_type=11 and id='$data[1]' and status_active=1 and is_deleted=0 ";
	}
	//echo $sql;
	$dataArray=sql_select($sql);
	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];
	//echo $entry_break_down_type.'='.$entry_break_down_type;
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
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><strong><? echo $data[2]; ?> Challan</strong></td>
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
            ?> 
        	<td width="270" rowspan="4" valign="top" colspan="2"><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></td>
            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $job_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
            <td width="125"><strong>Buyer:</strong></td><td width="175px"><? echo $buyer_library[$job_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        </tr>
        <tr>
            <td><strong>Job No :</strong></td><td><? echo $job_array[$dataArray[0][csf('po_break_down_id')]]['job_no']; ?></td>
            <td><strong>Style Ref.:</strong></td><td><? echo $job_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        </tr>
        <tr>
        	<td><strong>Item:</strong></td> <td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
            <td><strong>QC Pass Qty:</strong></td><td><? echo $dataArray[0][csf('production_quantity')]; ?></td>
        </tr>
        <tr>
            <td><strong>Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Input Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Poly Line: </strong></td><td><? echo $poly_library[$line_data_variable[$dataArray[0][csf('sewing_line')]]]; ?></td>
            <td><strong>Reporting Hour:</strong></td><td><? echo $dataArray[0][csf('production_hour')]; ?></td>
            <td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Alter Qty: </strong></td><td><? echo $dataArray[0][csf('alter_qnty')]; ?></td>
            <td><strong>Spot Qty:</strong></td><td><? echo $dataArray[0][csf('spot_qnty')]; ?></td>
            <td><strong></strong></td><td><? //echo $dataArray[0][csf('reject_qnty')]; ?></td>
        </tr>
        <tr>
        	<td><strong>System Challan: </strong></td><td><? echo $dataArray[0][csf('id')]; ?></td>
        	<td><strong>Produced By: </strong></td><td><? echo $worker_type[$dataArray[0][csf('produced_by')]]; ?></td>
        	<td>&nbsp;</td><td>&nbsp;</td>
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
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}
			
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 group by b.color_number_id ";
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
            <th width="80" align="center">Total Issue Qty.</th>
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
		$sql="SELECT sum(a.production_qnty) as production_qnty,sum(reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
		//echo $sql;
		$result=sql_select($sql);
		$size_array=array ();
		$qun_array=array ();$reject_qun_array=array();
		foreach ( $result as $row )
		{
			$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
		}
			
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 group by b.color_number_id ";
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
            <th width="80" align="center">Total Issue Qty.</th>
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
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
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
            echo signature_table(103, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}

if ($action=="piece_rate_order_cheack")
{
	$ex_data=explode('**',$data);
	if($db_type==0)
	{
		$piece_sql="select a.sys_number, sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=$ex_data[0] and b.item_id=$ex_data[1] and a.rate_for=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number";
	}
	else if($db_type==2)
	{
		$piece_sql="select a.sys_number, sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=$ex_data[0] and b.item_id=$ex_data[1] and a.rate_for=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number";
	}
	//echo $piece_sql;
	$data_array=sql_select($piece_sql,0);
	if(count($data_array)>0)
	{
		$sys_number=""; $wo_qty=0;
		foreach($data_array as $row)
		{
			if ($sys_number=="") $sys_number=$row[csf('sys_number')]; else $sys_number.=','.$row[csf('sys_number')];
			$wo_qty+=$row[csf('wo_qty')];
		}
		echo "1"."_".$sys_number."_".$wo_qty;
	}
	else
	{
		echo "0_";
	}
	exit();	
}

if($action=="defect_data")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$caption_name="";
	if($type==1) $caption_name="Alter Qty";
	else if($type==2) $caption_name="Spot Qty";
	else if($type==3) $caption_name="Reject Qty";
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
					if($type==1)
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
                        foreach($sew_fin_alter_defect_type as $id=>$val)
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
					else if($type==2)
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
                        foreach($sew_fin_spot_defect_type as $id=>$val)
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
					else if($type==3)
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
                        foreach($sew_fin_reject_type_arr as $id=>$val)
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


if($action=="production_process_control")
{
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=103 and company_name='$data'");  
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }
	
	exit();	
}



?>