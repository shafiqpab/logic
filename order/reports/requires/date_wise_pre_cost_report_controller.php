<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   This Report Will Create Knit Garments Date Wise Pre Cost report.
Functionality   :
JS Functions    :
Created by      :   Shariar Ahmed
Creation date   :   31-12-2023
Updated by      :
Update date     :
QC Performed BY :   
QC Date         :   
Comments        :
*/
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer"){
	//echo "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name";
    echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","0","" );
    exit();
}

//action_style_popup
if ($action == "action_style_popup")
{
	echo load_html_head_contents("Style Reference Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyerID = str_replace("'", "", $buyerID);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style)
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str)
		{

			if (str != "") str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1)
			{
				selected_id.push(str[1]);
				selected_name.push(str[2]);
			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_style_ref').val(name);
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th id="search_by_td_up" width="170">Please Enter Style Ref.</th>
					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
					<input type="hidden" name="hide_style_ref" id="hide_style_ref" value=""/>
					<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Style Ref", 2 => "Job No");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 130, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'action_create_style_listview', 'search_div', 'date_wise_pre_cost_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:100px;"/>
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");

//create_style_ref_search_list_view
if ($action == "action_create_style_listview")
{
	$data = explode('**', $data);
	$company_id = $data[0];
	//if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];

	if ($data[1] == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] >0 )
				$buyer_id_cond = " and buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else
		{
			$buyer_id_cond = "";
		}
	}
	else
	{
		$buyer_id_cond = " and buyer_name=$data[1]";//.str_replace("'","",$cbo_buyer_name)
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1) $search_field = "style_ref_no"; else $search_field = "job_no";

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year,";
	else $year_field = "";//defined Later

	$arr = array(0 => $company_arr, 1 => $buyer_arr);
	$sql = "select id, $year_field job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "120,120,60,70", "600", "240", 0, $sql, "js_set_value", "id,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no", "", '', '0,0,0,0,0', '', 1);
	exit();
}

//action_job_popup
if($action=="action_job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_search_type;
	?>
	<script>
		var search_type='<? echo $cbo_search_type;?>';
		//alert(search_type);
		/*function js_set_value(str)
		{
			//alert(str);
			var splitData = str.split("_");
			$("#hdn_job_id").val(splitData[0]); 
			$("#hdn_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}*/
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_po = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style)
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}	

		function js_set_value(str)
		{
			if (str != "")
				str = str.split("_");
				
			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');
			
			if ((jQuery.inArray(str[1], selected_id) == -1) && (jQuery.inArray(str[3], selected_po) == -1))
			{
				selected_id.push(str[1]);
				selected_name.push(str[2]);
				selected_po.push(str[3]);
			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if ((selected_id[i] == str[1]) && (selected_po[i] == str[3]))
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_po.splice(i, 1);
			}
			
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$("#hdn_job_id").val(id); 
			$("#hdn_job_no").val(name); 
		}		
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hdn_job_id" id="hdn_job_id" value="" />
                    <input type="hidden" name="hdn_job_no" id="hdn_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
						<? 
                        echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                        ?>
                        </td>                 
                        <td align="center">	
                    	<?
						$search_by_arr=array(1=>"Job No",2=>"Style Ref");
						$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";	
						echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );//cbo_search_type
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'action_create_job_listview', 'search_div', 'date_wise_pre_cost_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="action_create_job_listview")
{
	$data = explode('**',$data);
	$company_id = $data[0];
	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";
	
	//$search_common=$data[4];
	$cbo_search_type = 2;
	//echo $month_id;
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	if($search_by==2)
		$search_field="a.style_ref_no";
	else
		$search_field="a.job_no";
		
	if($db_type==0)
		$year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2)
		$year_field="to_char(a.insert_date,'YYYY') as year";
	else
		$year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0)
			$year_cond=" and year(a.insert_date)=$year_id";
		else
			$year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con =" and to_char(a.insert_date,'YYYY')";
		
		if($year_id!=0)
			$year_cond="$year_field_con=$year_id";
		else
			$year_cond="";	
	}
	
	$selete_data = "id,job_no_prefix_num,po_id";
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	$sql= "select b.id as po_id, a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by a.job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No,File No,Ref. No", "120,100,80,60,100,100,60,80","780","240",0, $sql , "js_set_value", $selete_data, "", 1, "company_name,buyer_name,0,0,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number,file_no,grouping", "",'','0,0,0,0,0,0','',1) ;
	exit();
}

//action_order_popup
if ($action == "action_order_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$buyerID = str_replace("'", "", $buyerID);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style)
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function js_set_value(str)
		{
			if (str != "") str = str.split("_");
			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');
			if (jQuery.inArray(str[1], selected_id) == -1)
			{
				selected_id.push(str[1]);
				selected_name.push(str[2]);
			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';

				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_order_id').val(id);
			$('#hide_order_no').val(name);
		}

		function fn_change_caption(str)
		{
			if(str==1)
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('Shipment From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('Shipment To Date');
			}
			else
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('Shipment From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('Shipment To Date');
			}
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:980px;">
				<table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th id="search_by_td_up" width="170">Please Enter Order No</th>
					<th width="90" >Date Category</th>
					<th width="70"  id="from_date_html">Shipment From Date</th>
					<th id="to_date_html">Shipment To Date</th>
					<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
					<input type="hidden" name="hide_order_no" id="hide_order_no" value=""/>
					<input type="hidden" name="hide_order_id" id="hide_order_id" value=""/>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", $buyerID, "", 0);
							?>
						</td>
						<td align="center">
							<?
							//$search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No", 4 => "Internal Ref", 5 => "File No");
							//$dd = "change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
							$search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No");
							$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<?
							$search_type=array(1=>'Shipment Date');
							echo create_drop_down( "cbo_date_type", 90, $search_type, "",0, "-- Select --", $selected, "fn_change_caption(this.value)" );
							?>
						</td>
						<td align="center"><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px"  value="" placeholder="From Date" /></td>
						<td align="center"><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px"  value="" placeholder="To Date" /></td>

						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_date_type').value, 'action_create_order_listview', 'search_div', 'date_wise_pre_cost_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="7" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_create_order_listview
if ($action == "action_create_order_listview")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	if ($data[1] == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] >0 )
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else
		{
			$buyer_id_cond = "";
		}
	}
	else
	{
		$buyer_id_cond = " and a.buyer_name=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1)
		$search_field = "b.po_number";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	/* else if ($search_by == 4)
		$search_field = "b.grouping";
	else if ($search_by == 5)
		$search_field = "b.file_no"; */
	else
		$search_field = "a.job_no";

	if (trim($data[3]) != "")
	{
		$search_field_cond = " and $search_field like '$search_string'";
	}
	else
	{
		$search_field_cond = "";
	}


	$start_date = trim($data[4]);
	$end_date = trim($data[5]);
	$cbo_date_category = str_replace("'", "", trim($data[6]));

	if($cbo_date_category==1)
	{
		if ($start_date != "" && $end_date != "")
		{
			if ($db_type == 0)
			{
				$date_cond = "and b.pub_shipment_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			}
			else
			{
				$date_cond = "and b.pub_shipment_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}
		}
		else
		{
			$date_cond = "";
		}
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = "";//defined Later

	$arr = array(0 => $company_arr, 1 => $buyer_arr);
	$sql = "select b.id, to_char(a.insert_date,'YYYY') as year, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b
	left join tna_process_mst c on b.id=c.po_number_id and c.status_active=1 and c.is_deleted=0 $date_cond2  where a.job_no=b.job_no_mst and a.company_name=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $buyer_id_cond $date_cond group by b.id, a.insert_date, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping, b.file_no order by b.id, b.pub_shipment_date";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Internal Ref, File No, Shipment Date", "70,70,50,60,130,130,90,90", "860", "220", 0, $sql, "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,grouping,file_no,pub_shipment_date", "", '', '0,0,0,0,0,0,0,0,3', '', 1);
	exit();
}


if ($action=="report_generate")
{
    extract($_REQUEST);
    $cbo_company_id=str_replace("'","",$cbo_company_id);
    $cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
	$txt_style = str_replace("'","",$txt_style);
	$hdn_style = str_replace("'","",$hdn_style);
	$txt_job = str_replace("'","",$txt_job);
	$hdn_job = str_replace("'","",$hdn_job);
	$txt_order = str_replace("'","",$txt_order);
	$hdn_order = str_replace("'","",$hdn_order);
	$based_on=str_replace("'","",$cbo_based_on);
	$txt_ir_no=str_replace("'","",$txt_ir_no);

    $companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
    $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$colorArr = return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $supplierArr = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b,lib_company c where a.id=b.supplier_id and c.id=b.tag_company and a.status_active =1 and a.is_deleted=0 and c.core_business not in(3) group by a.id,a.supplier_name","id","supplier_name");
    $fabric_booking_type = array(118 => "Main Fabric Booking",108=>'Partial Fabric Booking', 88 => "Short Fabric Booking", 89 => "Sample Fabric Booking - With Order", 0 => 'Sample Fabric Booking - Without Order');

    
	if($type==0)//show 
	{
		$date_from=str_replace("'","",$txt_date_from);
		$date_to=str_replace("'","",$txt_date_to);
		$lib_user = return_library_array("select id,user_name from user_passwd","id","user_name");
			if($cbo_company_id) $company_cond=" and a.company_name in($cbo_company_id)"; else $company_cond="";
			if($cbo_buyer_id) $buyerCond=" and a.buyer_name in($cbo_buyer_id)"; else $buyerCond="";
			if($txt_ir_no) $refCond=" and b.grouping in('$txt_ir_no')"; else $refCond="";
			if($based_on==3){
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond2.=" and h.approved_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'"; else $booking_date_cond2="";
			}else{
				/* if($txt_date_from!="" && $txt_date_to!="") $date_cond="and c.insert_date between '$txt_date_from' and '$txt_date_to'"; else $date_cond=""; */

				if($txt_date_from!="" && $txt_date_to!="") $date_cond.=" and c.insert_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'"; else $date_cond="";
			}
			
	
			//for style condition
			$style_condition = '';
			$style_condition2 = '';
			if($txt_style != '')
			{
				$style_condition = " AND c.job_no IN(SELECT job_no FROM wo_po_details_master WHERE id IN(".$hdn_style."))";
				$style_condition2 = " AND a.id IN(".$hdn_style.")";
			}
			
			//for job condition
			$job_condition = '';
			$job_condition2 = '';
			if($txt_job != '')
			{
				$job_condition = " AND c.job_no IN(SELECT job_no FROM wo_po_details_master WHERE id IN(".$hdn_job."))";
				$job_condition2 = " AND a.id IN(".$hdn_job.")";
			}
			
			//for order condition
			$order_condition = '';
			if($txt_order != '')
			{
				$order_condition = " AND b.id IN('".str_replace(',', "','", $hdn_order)."')";
			}
			$sewing_data=sql_select("select po_break_down_id, SUM(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and production_type=5  group by po_break_down_id");
			foreach($sewing_data as $sewing_row)
			{
				$sewing_data_array[$sewing_row[csf('po_break_down_id')]]['production_quantity']=$sewing_row[csf('production_quantity')];
			} 

			$exfactory_data=sql_select("select po_break_down_id,sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id");
			foreach($exfactory_data as $exfatory_row)
			{
				$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]]['ex_factory_qnty']=$exfatory_row[csf('ex_factory_qnty')];
			} 
			if($based_on==3){
				$sql_budget="select a.id as job_id,a.product_dept,a.job_no_prefix_num,a.client_id,c.ready_to_approved, a.insert_date,c.approved as is_approved, a.order_uom,a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price,a.dealing_marchant, a.gmts_item_id,a.inserted_by, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date,b.shipment_date, b.po_received_date, a.set_smv,b.po_quantity, b.po_total_price, b.unit_price, b.grouping, b.file_no,c.costing_date,a.company_name,d.booking_type,d.is_short,d.fab_nature,d.booking_no,e.entry_form as booking_entry_id, e.fabric_source,e.item_category from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c,wo_booking_dtls d,wo_booking_mst e,approval_history h where a.id=b.job_id and a.id=c.job_id and a.job_no=d.job_no and c.id = h.mst_id and d.booking_mst_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and e.is_deleted=0 and d.is_deleted=0 $company_cond $buyerCond $booking_date_cond2 $job_condition2 $order_condition $style_condition2 $refCond group by a.id,a.product_dept,a.job_no_prefix_num,a.client_id,c.ready_to_approved, a.insert_date,c.approved, a.order_uom,a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price,a.dealing_marchant, a.gmts_item_id,a.inserted_by, a.total_set_qnty, b.plan_cut, b.id, b.po_number, b.pub_shipment_date,b.shipment_date, b.po_received_date, a.set_smv,b.po_quantity, b.po_total_price, b.unit_price, b.grouping, b.file_no,c.costing_date,a.company_name,d.booking_type,d.is_short,d.fab_nature,d.booking_no,e.entry_form, e.fabric_source,e.item_category,c.costing_per  order by a.job_no";

				$nameArray_approved = sql_select("select e.job_id,max(b.approved_date) as last_approve_date,min(b.approved_date) as first_approve_date from wo_po_details_master a,wo_pre_cost_mst e, approval_history b where a.id=e.job_id and e.id=b.mst_id and b.entry_form in(77) and b.approved=1 $company_cond group by e.job_id ");
				foreach($nameArray_approved as $row)
				{		
					$approve_data_arr[$row[csf('job_id')]]['last_approve_date']=$row[csf('last_approve_date')];
					$approve_data_arr[$row[csf('job_id')]]['first_approve_date']=$row[csf('first_approve_date')];
				}
				$nameArray_approved = sql_select("select e.job_id,max(b.approved_date) as last_unapprove_date from wo_po_details_master a,wo_pre_cost_mst e, approval_history b where a.id=e.job_id and e.id=b.mst_id and b.entry_form in(77) $company_cond and b.approved=0 group by e.job_id ");
				foreach($nameArray_approved as $row)
				{		
					$approve_data_arr[$row[csf('job_id')]]['last_unapprove_date']=$row[csf('last_unapprove_date')];
				}
				$sql_pre_cost=sql_select("select  c.job_id,c.total_cost from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_dtls c where a.id=b.job_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $company_cond $buyerCond $date_cond $job_condition2 $order_condition $style_condition2 $refCond");
				foreach($sql_pre_cost as $row)
				{		
					$pre_tot_cost_arr[$row[csf('job_id')]]['total_cost']=$row[csf('total_cost')];
				}
				$yesno_approved = sql_select("select e.job_id,max(b.id) as appId,max(b.approved_date) as last_approve_date,min(b.approved_date) as first_approve_date,b.current_approval_status from  wo_po_details_master a,wo_pre_cost_mst e, approval_history b where a.id=e.job_id and e.id=b.mst_id and b.entry_form in(77) $company_cond group by e.job_id,b.current_approval_status ");
				foreach($yesno_approved as $row)
				{		
					$approve_data_arr[$row[csf('job_id')]]['approval_status'][$row[csf('appId')]]=$row[csf('current_approval_status')];
				}
			}else{
				
				$sql_budget="select a.id as job_id,a.product_dept,a.job_no_prefix_num,a.client_id,c.ready_to_approved, a.insert_date,c.approved as is_approved, a.order_uom,a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price,a.dealing_marchant, a.gmts_item_id,a.inserted_by, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date,b.shipment_date, b.po_received_date, a.set_smv,b.po_quantity, b.po_total_price, b.unit_price, b.grouping, b.file_no,c.costing_date,a.company_name,d.booking_type,d.is_short,d.fab_nature,d.booking_no,e.entry_form as booking_entry_id, e.fabric_source,e.item_category,c.costing_per from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c,wo_booking_dtls d,wo_booking_mst e where a.id=b.job_id and a.id=c.job_id and a.job_no=d.job_no and d.booking_mst_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0  and d.is_deleted=0  and e.is_deleted=0 $company_cond $buyerCond $date_cond $job_condition2 $order_condition $style_condition2 $refCond group by a.id,a.product_dept,a.job_no_prefix_num,a.client_id,c.ready_to_approved, a.insert_date,c.approved, a.order_uom,a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price,a.dealing_marchant, a.gmts_item_id,a.inserted_by, a.total_set_qnty, b.plan_cut, b.id, b.po_number, b.pub_shipment_date,b.shipment_date, b.po_received_date, a.set_smv,b.po_quantity, b.po_total_price, b.unit_price, b.grouping, b.file_no,c.costing_date,a.company_name,d.booking_type,d.is_short,d.fab_nature,d.booking_no,e.entry_form, e.fabric_source,e.item_category,c.costing_per order by a.job_no";
				$sql_pre_cost=sql_select("select  c.job_id,c.total_cost from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_dtls c where a.id=b.job_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $company_cond $buyerCond $date_cond $job_condition2 $order_condition $style_condition2 $refCond");
				foreach($sql_pre_cost as $row)
				{		
					$pre_tot_cost_arr[$row[csf('job_id')]]['total_cost']=$row[csf('total_cost')];
				}
				$nameArray_approved = sql_select("select e.job_id,max(b.approved_date) as last_approve_date,min(b.approved_date) as first_approve_date from wo_po_details_master a,wo_pre_cost_mst e, approval_history b where a.id=e.job_id and e.id=b.mst_id and b.entry_form in(77) and b.approved=1 $company_cond group by e.job_id ");
				foreach($nameArray_approved as $row)
				{		
					$approve_data_arr[$row[csf('job_id')]]['last_approve_date']=$row[csf('last_approve_date')];
					$approve_data_arr[$row[csf('job_id')]]['first_approve_date']=$row[csf('first_approve_date')];
				}
				$nameArray_approved = sql_select("select e.job_id,max(b.approved_date) as last_unapprove_date from wo_po_details_master a,wo_pre_cost_mst e, approval_history b where a.id=e.job_id and e.id=b.mst_id and b.entry_form in(77) $company_cond and b.approved=0 group by e.job_id ");
				foreach($nameArray_approved as $row)
				{		
					$approve_data_arr[$row[csf('job_id')]]['last_unapprove_date']=$row[csf('last_unapprove_date')];
				}
				$yesno_approved = sql_select("select e.job_id,max(b.id) as appId,max(b.approved_date) as last_approve_date,min(b.approved_date) as first_approve_date,b.current_approval_status from  wo_po_details_master a,wo_pre_cost_mst e, approval_history b where a.id=e.job_id and e.id=b.mst_id and b.entry_form in(77) $company_cond group by e.job_id,b.current_approval_status ");
				foreach($yesno_approved as $row)
				{		
					$approve_data_arr[$row[csf('job_id')]]['approval_status'][$row[csf('appId')]]=$row[csf('current_approval_status')];
				}
			}
			/* echo "<pre>";
			print_r($approve_data_arr); */
		   
		$sql_data=sql_select($sql_budget);
		$pre_cost_date_arr =array();
		$po_break_down_ids="";
		foreach ($sql_data as $value) {
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['job_no'] = $value[csf('job_no')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['job_id'] = $value[csf('job_id')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['po_id'] = $value[csf('po_id')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['costing_date'] = $value[csf('costing_date')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['buyer_name'] = $value[csf('buyer_name')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['company_id'] = $value[csf('company_name')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['insert_date'] = $value[csf('insert_date')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['is_approved'] = $value[csf('is_approved')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['shipment_date'] = $value[csf('shipment_date')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['po_received_date'] = $value[csf('po_received_date')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['ready_to_approved'] = $value[csf('ready_to_approved')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['ex_factory_qnty'] += $exfactory_data_array[$value[csf('po_id')]]['ex_factory_qnty'];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['production_quantity'] += $sewing_data_array[$sewing_row[csf('po_break_down_id')]]['production_quantity'];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['item_category'] = $value[csf('item_category')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['booking_entry_id'] = $value[csf('booking_entry_id')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['costing_per'] = $value[csf('costing_per')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['grouping'] = $value[csf('grouping')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['style_ref_no'] = $value[csf('style_ref_no')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['client_id'] = $value[csf('client_id')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['fabric_source'] = $value[csf('fabric_source')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['entry_form_id'] = $value[csf('entry_form_id')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['entry_form'] = $value[csf('entry_form')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['product_dept'] = $product_dept[$value[csf('product_dept')]];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['inserted_by'] = $lib_user[$value[csf('inserted_by')]];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['po_quantity'] += $value[csf('po_quantity')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['booking_type'] = $value[csf('booking_type')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['is_short'] = $value[csf('is_short')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['fab_nature'] = $value[csf('fab_nature')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['booking_no'] = $value[csf('booking_no')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['plan_cut'] += $value[csf('plan_cut')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['unit_price'] = $value[csf('unit_price')];
			$pre_cost_date_arr[$value[csf('job_id')]][$value[csf('booking_no')]]['inserted_by'] = $lib_user[$value[csf('inserted_by')]];
			
			$job_arr[$value[csf("job_no")]][$value[csf("booking_no")]]=$value[csf("job_no")];
			$booking_arr[$value[csf("booking_no")]]=$value[csf("booking_no")];
	   }
	    /* echo "<pre>";
	   print_r($job_arr);die; */ 

		//=====================================FSO===================================================
						
		$fso_data = sql_select("select a.job_no,a.booking_date,b.booking_no,sum(c.cons_quantity) as grey_qty,f.id as jobid from fabric_sales_order_mst a left join wo_booking_mst b on a.booking_id=b.id left join wo_po_details_master f on a.style_ref_no=f.style_ref_no and a.po_job_no=f.job_no ,inv_transaction c,order_wise_pro_details d where c.id=d.trans_id and d.po_breakdown_id=a.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  ".where_con_using_array($booking_arr,1,'a.sales_booking_no')."  and c.transaction_type=2 and c.receive_basis in(3,8) group by  a.job_no,a.booking_date,b.booking_no,f.id");
		foreach($fso_data as $value){
			$pre_cost_date_arr[$value[csf('jobid')]][$value[csf('booking_no')]]['fso_qty'] = $value[csf('grey_qty')];
			$pre_cost_date_arr[$value[csf('jobid')]][$value[csf('booking_no')]]['sales_booking_no'] = $value[csf('job_no')];
			$pre_cost_date_arr[$value[csf('jobid')]][$value[csf('booking_no')]]['sales_booking_date'] = $value[csf('booking_date')];
		}
		unset($fso_data);
		$fso_rtn_data = sql_select("select a.job_no,a.booking_date,b.booking_no,sum(c.cons_quantity) as grey_qty,f.id as jobid from fabric_sales_order_mst a left join wo_booking_mst b on a.booking_id=b.id left join wo_po_details_master f on a.style_ref_no=f.style_ref_no and a.po_job_no=f.job_no ,inv_transaction c,order_wise_pro_details d where c.id=d.trans_id and d.po_breakdown_id=a.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  ".where_con_using_array($booking_arr,1,'a.sales_booking_no')."  and c.transaction_type=4 and d.entry_form=9 and c.receive_basis in(3,8) group by  a.job_no,a.booking_date,b.booking_no,f.id");
		foreach($fso_rtn_data as $value){
			$pre_cost_date_arr[$value[csf('jobid')]][$value[csf('booking_no')]]['fso_rtn_qty'] = $value[csf('grey_qty')];
		}
		unset($fso_rtn_data);
		 /* echo '<pre>';print_r($pre_cost_date_arr);die;  */
	
		ob_start();
		?>
		<div align="center">
			<table width="3130px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="32" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="32" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
			</table>
			<table width="3130px" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr style="font-size:13px">
						<th style="word-wrap: break-word;" width="30">SL.</th>
						<th style="word-wrap: break-word;" width="100">Company</th>
						<th style="word-wrap: break-word;" width="100">Job No</th>
						<th style="word-wrap: break-word;" width="100">Job Insert Date</th>
						<th style="word-wrap: break-word;" width="100">IR/IB</th>
						<th style="word-wrap: break-word;" width="100">Revise No</th>
						<th style="word-wrap: break-word;" width="100">Fabric Booking No</th>
						<th style="word-wrap: break-word;" width="100">FSO No</th>
						<th style="word-wrap: break-word;" width="100">FSO Date</th>
						<th style="word-wrap: break-word;" width="100">Trims Booking No</th>
						<th style="word-wrap: break-word;" width="100">Net Yarn Issue Qty</th>
						<th style="word-wrap: break-word;" width="100">Waiting For 1st Approval</th>
						<th style="word-wrap: break-word;" width="100">Shipment Date</th>
						<th style="word-wrap: break-word;" width="100">Ready To Approved</th>
						<th style="word-wrap: break-word;" width="100">Approval Status</th>
						<th style="word-wrap: break-word;" width="100">1st Appv. Date</th>
						<th style="word-wrap: break-word;" width="100">Last Appv. Date</th>
						<th style="word-wrap: break-word;" width="100">Last Un-Appv. Date</th>
						<th style="word-wrap: break-word;" width="100">Days Passed From<br>Last Un Approval</th>
						<th style="word-wrap: break-word;" width="100">Po Received Date</th>
						<th style="word-wrap: break-word;" width="100">Buyer</th>
						<th style="word-wrap: break-word;" width="100">Buyer Client</th>
						<th style="word-wrap: break-word;" width="100">Style Ref.</th>
						<th style="word-wrap: break-word;" width="100">Product Dept.</th>
						<th style="word-wrap: break-word;" width="100">Fabric Nature</th>
						<th style="word-wrap: break-word;" width="100">FOB Price/Pcs</th>
						<th style="word-wrap: break-word;" width="100">Pre-Cost Value</th>						
						<th style="word-wrap: break-word;" width="100">Order Qty</th>
						<th style="word-wrap: break-word;" width="100">Cutting Production Qty</th>												
						<th style="word-wrap: break-word;" width="100">Sewing Production Qty</th>
						<th style="word-wrap: break-word;" width="100">Ex-Factory</th>
						<th style="word-wrap: break-word;" width="100">User Name</th>
					 </tr>
				</thead>
			</table>
			 <div style="width:3130px;">
			<table width="3130px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
				<tbody>
					<?
					$print_report_format_arr=sql_select("select format_id,template_name from lib_report_template where module_id=2 and report_id=1 and is_deleted=0 and status_active=1 and template_name in ($cbo_company_id) ");	//main fabric booking				
					foreach($print_report_format_arr as $row){
						$format_ids=explode(",",$row[csf('format_id')]);
						$report_btn_arr[118][$row[csf('template_name')]]=$format_ids[0];
						
					}

					$print_report_format_arr2=sql_select("select format_id,template_name from lib_report_template where module_id=2 and report_id=2 and is_deleted=0 and status_active=1 and template_name in ($cbo_company_id) ");//short fabric booking
					foreach($print_report_format_arr2 as $row){
						$format_ids=explode(",",$row[csf('format_id')]);
						$report_btn_arr[88][$row[csf('template_name')]]=$format_ids[0];
						
					}
					$print_report_format_arr3=sql_select("select format_id,template_name from lib_report_template where module_id=2 and report_id=35 and is_deleted=0 and status_active=1 and template_name in ($cbo_company_id) ");//short fabric booking
					foreach($print_report_format_arr2 as $row){
						$format_ids=explode(",",$row[csf('format_id')]);
						$report_btn_arr[108][$row[csf('template_name')]]=$format_ids[0];
						
					}
					

					$i=1; $tot_rows=0;
					$total_booking_qty_kg =0; $total_booking_qty_mtr =0; $total_booking_qty_yds= 0;
					$po_rowspan_arr=array();$job_rowspan_arr=array();$color_rowspan_arr=array();$fab_rowspan_arr=array();

				   foreach($pre_cost_date_arr as $bookingno=>$bookingdata)
					{
						$job_rowspan=0;
						foreach($bookingdata as $fabid=>$row)
						{
							$job_rowspan++;
						}
						$job_rowspan_arr[$bookingno]=$job_rowspan;
					}
					foreach ($pre_cost_date_arr as $bookingno=>$bookingdata) 
					{
						$x=1;
						foreach ($bookingdata as $fabid=>$row)
						{
							$fabric_nature=$row[csf('item_category')];
							$po_br_ids=$row['po_id'];
							if($row['booking_type']==1 && $row['booking_entry_id']==118){
									$row_id=$report_btn_arr[$row['booking_entry_id']][$row['company_id']];
									
									if($row_id==786){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['booking_entry_id']."','show_fabric_booking_report25','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
									}
									else if($row_id==426){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['booking_entry_id']."','show_fabric_booking_report_print23','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
									}else if($row_id==502){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['booking_entry_id']."','show_fabric_booking_report26','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
									}
								}elseif($row['booking_type']==1 && $row['booking_entry_id']==88){
									$row_id=$report_btn_arr[$row['booking_entry_id']][$row['company_id']];
									if($row_id==72){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['booking_entry_id']."','print_booking_6','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
									}
									else if($row_id==191){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['booking_entry_id']."','print_booking_7','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
									}
									else if($row_id==45){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['booking_entry_id']."','print_booking_4','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
									}
									else if($row_id==53){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['booking_entry_id']."','print_booking_5','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
									}
								}elseif($row['booking_type']==1 && $row['booking_entry_id']==108){
									$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['booking_entry_id']."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
								}

						$print_report_format=return_field_value("format_id","lib_report_template","template_name in(".$cbo_company_id.") and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
						$print_button=explode(",",$print_report_format);
						if($print_button[0]==25) $precost_button="budgetsheet2";
						else if($print_button[0]==50) $precost_button="preCostRpt";
						else if($print_button[0]==51) $precost_button="preCostRpt2";
						else if($print_button[0]==52) $precost_button="bomRpt";
						else if($print_button[0]==63) $precost_button="bomRpt2";
						else if($print_button[0]==156) $precost_button="accessories_details";
						else if($print_button[0]==157) $precost_button="accessories_details2";
						else if($print_button[0]==158) $precost_button="preCostRptWoven";
						else if($print_button[0]==159) $precost_button="bomRptWoven";
						else if($print_button[0]==170) $precost_button="preCostRpt3";
						else if($print_button[0]==171) $precost_button="preCostRpt4";
						else if($print_button[0]==142) $precost_button="preCostRptBpkW";
						else if($print_button[0]==192) $precost_button="checkListRpt";
						else if($print_button[0]==197) $precost_button="bomRpt3";
						else if($print_button[0]==211) $precost_button="mo_sheet";
						else if($print_button[0]==221) $precost_button="fabric_cost_detail";
						else if($print_button[0]==173) $precost_button="preCostRpt5";
						else if($print_button[0]==238) $precost_button="summary";
						else if($print_button[0]==215) $precost_button="budget3_details";
						else if($print_button[0]==270) $precost_button="preCostRpt6";
						else if($print_button[0]==581) $precost_button="costsheet";
						else if($print_button[0]==730) $precost_button="budgetsheet";
						else if($print_button[0]==351) $precost_button="bomRpt4";
						else if($print_button[0]==381) $precost_button="mo_sheet_1";
						else if($print_button[0]==268) $precost_button="budget_4";
						else if($print_button[0]==403) $precost_button="mo_sheet_3";
						else if($print_button[0]==769) $precost_button="preCostRpt7";
						else if($print_button[0]==445) $precost_button="preCostRpt8";
						else if($print_button[0]==460) $precost_button="trims_check_list";
						else if($print_button[0]==129) $precost_button="budget5";
						else if($print_button[0]==235) $precost_button="preCostRpt9";
						else if($print_button[0]==25) $precost_button="budgetsheet2";
						else if($print_button[0]==120) $precost_button="budgetsheet3";
						else if($print_button[0]==494) $precost_button="ocsReport";
						else if($print_button[0]==498) $precost_button="preCostRpt10";
						else if($print_button[0]==800) $precost_button="preCostRpt11";
						else if($print_button[0]==427) $precost_button="preCostRpt12";
						else if($print_button[0]==341) $precost_button="budgetsheet4";
						else if($print_button[0]==342) $precost_button="budgetsheet2v3";
						else if($print_button[0]==486) $precost_button="accessories_details3";
						else if($print_button[0]==874) $precost_button="preCostRpt13";
						else if($print_button[0]==881) $precost_button="fabricBom";
						else if($print_button[0]==509) $precost_button="masterWO";
						else $precost_button="";

						$print_report_format_v3=return_field_value("format_id","lib_report_template","template_name in(".$cbo_company_id.") and module_id=2 and report_id=161 and is_deleted=0 and status_active=1");
						$print_button_v3=explode(",",$print_report_format_v3);

							if($print_button_v3[0]==50) $action_v3="preCostRpt";
							if($print_button_v3[0]==51) $action_v3="preCostRpt2";
							if($print_button_v3[0]==52) $action_v3="bomRpt";
							if($print_button_v3[0]==63) $action_v3="bomRpt2";
							if($print_button_v3[0]==156) $action_v3="accessories_details";
							if($print_button_v3[0]==157) $action_v3="accessories_details2";
							if($print_button_v3[0]==158) $action_v3="preCostRptWoven";
							if($print_button_v3[0]==159) $action_v3="bomRptWoven";
							if($print_button_v3[0]==170) $action_v3="preCostRpt3";
							if($print_button_v3[0]==171) $action_v3="preCostRpt4";
							if($print_button_v3[0]==142) $action_v3="preCostRptBpkW";
							if($print_button_v3[0]==192) $action_v3="checkListRpt";
							if($print_button_v3[0]==197) $action_v3="bomRpt3";
							if($print_button_v3[0]==211) $action_v3="mo_sheet";
							if($print_button_v3[0]==221) $action_v3="fabric_cost_detail";
							if($print_button_v3[0]==173) $action_v3="preCostRpt5";
							if($print_button_v3[0]==238) $action_v3="summary";
							if($print_button_v3[0]==215) $action_v3="budget3_details";
							if($print_button_v3[0]==270) $action_v3="preCostRpt6";
							if($print_button_v3[0]==581) $action_v3="costsheet";
							if($print_button_v3[0]==730) $action_v3="budgetsheet";
							if($print_button_v3[0]==759) $action_v3="materialSheet";
							if($print_button_v3[0]==351) $action_v3="bomRpt4";
							if($print_button_v3[0]==268) $action_v3="budget_4";
							if($print_button_v3[0]==381) $action_v3="mo_sheet_2";
							if($print_button_v3[0]==405) $action_v3="materialSheet2";
							if($print_button_v3[0]==765) $action_v3="bomRpt5";
							if($print_button_v3[0]==403) $action_v3="mo_sheet_3";
							if($print_button_v3[0]==445) $action_v3="preCostRpt8";

					if($row['booking_entry_id']==87) $rpt_form=26;
					else $rpt_form=219;
					$print_report_format=return_field_value("format_id","lib_report_template","template_name in(".$cbo_company_id.") and module_id=2 and report_id=$rpt_form and is_deleted=0 and status_active=1");
				//echo $print_report_format;die;
					$reportArr= explode(',',$print_report_format);
					$report=$reportArr[0];
				//echo $report.'DDD';
				if($report==67){$reporAction="show_trim_booking_report2";}
				elseif($report==183){$reporAction="show_trim_booking_report3";}
				elseif($report==235){$reporAction="show_trim_booking_report5";}
				elseif($report==227){$reporAction="show_trim_booking_report8";}
				elseif($report==209){$reporAction="show_trim_booking_report4";}
				elseif($report==235){$reporAction="show_trim_booking_report5";}
				elseif($report==176){$reporAction="show_trim_booking_report6";}
				elseif($report==746){$reporAction="show_trim_booking_report7";}
				elseif($report==177){$reporAction="show_trim_booking_report9";}
				elseif($report==241){$reporAction="show_trim_booking_report11";}
				elseif($report==274){$reporAction="show_trim_booking_report10";}
				elseif($report==269){$reporAction="show_trim_booking_report12";}
				elseif($report==28){$reporAction="show_trim_booking_report13";}
				elseif($report==280){$reporAction="show_trim_booking_report14";}
				elseif($report==304){$reporAction="show_trim_booking_report15";}
				elseif($report==14){$reporAction="show_trim_booking_report16";}
				elseif($report==719){$reporAction="show_trim_booking_report17";}
				elseif($report==339){$reporAction="show_trim_booking_report18";}
				elseif($report==433){$reporAction="show_trim_booking_report19";}
				elseif($report==404){$reporAction="show_trim_booking_report21";}
				elseif($report==419){$reporAction="show_trim_booking_report22";}
				elseif($report==774){$reporAction="show_trim_booking_report_wg";}
				elseif($report==786){$reporAction="show_trim_booking_report25";}
				elseif($report==502){$reporAction="show_trim_booking_report26";}
				elseif($report==437){$reporAction="show_trim_booking_report27";}
				elseif($report==845){$reporAction="show_trim_booking_report_AAL";}

				$short_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_id."' and module_id=2 and report_id=57 and is_deleted=0 and status_active=1");
				//echo $print_report_format;
					$shortreportArr= explode(',',$short_print_report_format);
					$shortreport=$shortreportArr[0];

					if($shortreport==19){$reporAction="show_trim_booking_report3";}
					elseif($shortreport==67){$reporAction="show_trim_booking_report2";}
					elseif($shortreport==16){$reporAction="show_trim_booking_report4";}
					elseif($shortreport==177){$reporAction="show_trim_booking_report5";}
						//echo $row[12]; die;
						$booking_qty_kg = 0; $booking_qty_mtr=0; $booking_qty_yds = 0;
						$is_approved=$row['is_approved'];
						$first_approve_dateTime=$last_approve_dateTime=$last_unapprove_dateTime='';
					   
						if($based_on==3){
							$first_approve_dateTime=$approve_data_arr[$row['job_id']]['first_approve_date'];
							$last_approve_dateTime=$approve_data_arr[$row['job_id']]['last_approve_date'];
							$last_unapprove_dateTime=$approve_data_arr[$row['job_id']]['last_unapprove_date'];
						}else{
							$first_approve_dateTime=$approve_data_arr[$row['job_id']]['first_approve_date'];
							$last_approve_dateTime=$approve_data_arr[$row['job_id']]['last_approve_date'];
							$last_unapprove_dateTime=$approve_data_arr[$row['job_id']]['last_unapprove_date'];
						}
						$total_cost=$pre_tot_cost_arr[$row['job_id']]['total_cost'];
						$first_approve_dateTimeArr=explode(" ",$first_approve_dateTime);
						$last_approve_dateTimeArr=explode(" ",$last_approve_dateTime);
						$last_unapprove_dateTimeArr=explode(" ",$last_unapprove_dateTime);
							$last_approve_date=$first_approve_date=$first_unapprove_date="";
							if(count($first_approve_dateTimeArr))
							{
								$first_approve_date=$first_approve_dateTimeArr[0];
							}
							if(count($last_approve_dateTimeArr))
							{
								$last_approve_date=$last_approve_dateTimeArr[0];
							}
							if(count($last_unapprove_dateTimeArr))
							{
								$last_unapprove_date=$last_unapprove_dateTimeArr[0];
							}
							
							$re_job_no=$row['job_no'];
						
							$revise_approved = sql_select("select max(b.approved_no) as approved_no,count(b.id) as revised_no,e.approved from wo_po_details_master a,wo_pre_cost_mst e, approval_history b where a.id=e.job_id and e.id=b.mst_id and b.entry_form in(77) and e.job_no='$re_job_no' $company_cond group by e.approved");
							list($nameArray_approved_row) = $revise_approved;
						
						$fabric_nature=$row[csf('item_category')];

						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
						<?
						if($x==1)
						{
						?>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="30" align="center"><? echo $i; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? echo $companyArr[$row['company_id']]; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100"align="center" >
							<p>
							<?php 
								$company_name=$row['company_id'];
								$buyer_name=$row['buyer_name'];
								$costing_date=$row['costing_date'];
								$costing_per=$row['costing_per'];
								$style_ref_no=$row['style_ref_no'];
							 ?>
							<a href='#report_details' onClick="generate_report_v3('<? echo $company_name; ?>','<? echo $row['job_no']; ?>','<? echo $style_ref_no; ?>','<? echo $buyer_name;?>','<? echo $costing_date;?>','<? echo $po_br_ids;?>','<? echo $precost_button;?>');">
								<? echo $row['job_no']; ?>
							</a>
							
						&nbsp;</p>
							</td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? echo change_date_format($row['insert_date']); ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center" ><? echo $row['grouping']; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><?
							if($nameArray_approved_row[csf('approved_no')]>1)
							{
								?>
								
								<b><? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
								<?
							}
							  	?></td>
							<?
								} ?>
								<td style="word-wrap: break-word;" width="100"align="center" ><? 
								if($row['booking_type']==1){
									echo $variable;
								}
								
								 ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $row['sales_booking_no']; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo change_date_format($row['sales_booking_date']); ?></td>
						
							 <td style="word-wrap: break-word;" width="100" align="center" ><p><?  if($row['booking_type']==2){?><a href="#" onClick="generate_trim_report('<? echo $reporAction; ?>','<? echo $row['booking_no']; ?>',<? echo $row['company_id']; ?>,<? echo $row['is_approved']; ?>,<? echo $row['is_short']; ?>,<? echo $row['booking_entry_id']; ?>)"><? echo $row['booking_no']; ?></a></p><?} ?></td>
							 <td style="word-wrap: break-word;" width="100" align="right"><?
							 $net_qty=$row['fso_qty']-$row['fso_rtn_qty'];
							 echo $net_qty;$tot_net_qnty+=$net_qty; ?></td>
							 <?
								if($x==1)
								{
								?>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>"style="word-wrap: break-word;" width="100" align="center" title="<? echo $row['booking_date'];?>"><? 
								$lead_time=0;
								if($row[("insert_date")]!="" && $row[("insert_date")]!="0000-00-00")
								{
									$lead_time=datediff("d", $row[("insert_date")], date("d-m-Y"));
								}
								$waiting_time=$lead_time-1;
								if($first_approve_date=="" && $waiting_time>0) echo $waiting_time." Days"; else echo " ";
							 ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center" title="<? echo $row['shipment_date'];?>"><? echo change_date_format($row['shipment_date'], "d-M-y", "-", 1); ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100"  align="center" align="center"><?=$yes_no[$row['ready_to_approved']]; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><?=$yes_no[max($approve_data_arr[$row['job_id']]['approval_status'])];; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center" title="<? echo $first_approve_dateTime;?>"><? echo change_date_format($first_approve_date, "d-M-y", "-", 1); ?> </td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center" title="<? echo $last_approve_dateTime;?>"><? echo change_date_format($last_approve_date, "d-M-y", "-", 1); ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center" title="<? echo $last_unapprove_dateTime;?>"><? echo change_date_format($last_unapprove_dateTime, "d-M-y", "-", 1); ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? 
							$approval_statuss=max($approve_data_arr[$row['job_id']]['approval_status']);
							$lead_times=0;
							if($last_unapprove_dateTime!="" && $last_unapprove_dateTime!="0000-00-00")
							{
								$lead_times=datediff("d", $last_unapprove_dateTime, date("d-m-Y"));
							}
							if($last_unapprove_dateTime>$last_approve_date && $approval_statuss!=1) echo $lead_times." Days"; else echo "";
							//echo change_date_format($row['booking_date'], "d-M-y", "-", 1); ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? echo change_date_format($row['po_received_date'], "d-M-y", "-", 1); ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? echo $buyerArr[$row['buyer_name']]; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? echo $buyerArr[$row['client_id']]; ?></td>
						
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? echo $row['style_ref_no']; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? echo $row['product_dept']; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? echo $gmts_nature[$row['fabric_source']] ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? echo $row['unit_price']; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="right"><? echo fn_number_format($total_cost/12,6); ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="right"><? echo $row['po_quantity']; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="right"><? echo $row['plan_cut']; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="right"><? echo $row['production_quantity']; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="right"><? echo $row['ex_factory_qnty']; ?></td>
							<td rowspan="<? echo $job_rowspan_arr[$bookingno];?>" style="word-wrap: break-word;" width="100" align="center"><? echo $row['inserted_by']; ?></td>
							<?
						$tot_production_quantity+=$row['production_quantity'];
						$tot_ex_factory_qnty+=$row['ex_factory_qnty'];
						$tot_po_qnty+=$row['po_quantity'];
					  	$tot_plan_qnty+=$row['plan_cut'];
						}?>
						</tr>
					
					  <? $i++; $tot_rows++; $x++;
					  	
						
					} } ?>
				</tbody>
			</table>
            </div>
			<table width="3130px" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
				<tfoot>
					<tr style="font-size:13px">
						<th bgcolor= "#A0A6AC" width="30"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100" align='center' >Total</th>
						<th bgcolor= "#A0A6AC"width="100"><? echo $tot_net_qnty; ?></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"><? echo $tot_po_qnty; ?></th>
						<th bgcolor= "#A0A6AC"width="100"><? echo $tot_plan_qnty; ?></th>
						<th bgcolor= "#A0A6AC"width="100"><? echo $tot_production_quantity; ?></th>
						<th bgcolor= "#A0A6AC"width="100"><? echo $tot_ex_factory_qnty; ?></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
	}
    foreach (glob("$user_id*.xls") as $filename)
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    $filename=$user_id."_".$name.".xls";
    echo "$total_data####$filename####$tot_rows####$type";
    exit();
}
