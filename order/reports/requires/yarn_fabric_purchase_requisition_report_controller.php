<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" )
{
	header("location:login.php");
	die;
}

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//load_drop_down_buyer
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "func_onchange_buyer();" );
	exit();
}

//load_drop_down_sub_dep
if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_department_id", 140, "select id, sub_department_name from lib_pro_sub_deparatment where buyer_id in($data[0]) and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select --", $selected, "" );
	exit();
}

//action_season_popup
if ($action == "action_season_popup")
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

			$('#hdn_season_id').val(id);
			$('#hdn_season_name').val(name);
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:250px;">
            	<input type="hidden" id="hdn_season_id" name="hdn_season_id" />
            	<input type="hidden" id="hdn_season_name" name="hdn_season_name" />
                <div style="margin-top:15px" id="search_div"></div>
            </fieldset>
        </form>
    </div>
</body>
<script>
	show_list_view ('<? echo $companyID; ?>'+'**'+'<? echo $buyerID; ?>', 'action_create_season_listview', 'search_div', 'yarn_fabric_purchase_requisition_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//action_create_season_listview
if ($action == "action_create_season_listview")
{
	$data = explode('**', $data);
	$arr = array();
	$sql = "select id, season_name from lib_buyer_season where buyer_id='".$data[1]."' and status_active=1 and is_deleted=0 order by id";
	echo create_list_view("tbl_list_search", "Season Name", "210", "250", "240", 0, $sql, "js_set_value", "id,season_name", "", 1, "0", $arr, "season_name", "", '', '0', '', 1);
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
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'action_create_style_listview', 'search_div', 'yarn_fabric_purchase_requisition_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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

		function js_set_value_13062021(str)
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

			$("#hdn_job_id").val(id); 
			$("#hdn_job_no").val(name); 
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'action_create_job_listview', 'search_div', 'yarn_fabric_purchase_requisition_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	
	/*
	if($cbo_search_type==1)//Style
	{
		$selete_data="id,style_ref_no";	
	}
	else if($cbo_search_type==1) //Job
	{
		$selete_data="id,job_no_prefix_num";	
	}
	
	else if($cbo_search_type==3)
	{
		$selete_data="po_id,po_number";	
	}
	else if($cbo_search_type==4) //File
	{
		$selete_data="po_id,file_no";	
	}
	else if($cbo_search_type==5) //Ref. no
	{
		$selete_data="po_id,grouping";	
	}
	*/
	
	$selete_data = "id,job_no_prefix_num,po_id";
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	/*if($cbo_search_type!=6)
	{
		$sql= "select b.id as po_id,a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field,b.po_number,b.file_no,b.grouping from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by a.job_no";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No,File No,Ref. No", "120,100,80,60,100,100,60,80","780","240",0, $sql , "js_set_value", "$selete_data", "", 1, "company_name,buyer_name,0,0,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number,file_no,grouping", "",'','0,0,0,0,0,0','') ;
	}
	else
	{
		$sql= "select c.id as book_id,c.booking_no_prefix_num as booking_prefix,c.booking_date, a.job_no, a.company_name, a.buyer_name, $year_field from wo_po_details_master a,wo_po_break_down b,wo_booking_mst c,wo_booking_dtls d where a.job_no=b.job_no_mst and c.booking_no=d.booking_no and b.id=d.po_break_down_id and d.job_no=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id  and c.company_id=$company_id and c.entry_form=108 and c.booking_type in(1) and c.is_short in(2) and $search_field like '$search_string' $buyer_id_cond $year_cond group by  c.id,c.booking_no_prefix_num,c.booking_date,a.job_no,a.company_name, a.buyer_name,a.insert_date order by a.job_no";
		echo create_list_view("tbl_list_search", "Company,Buyer,Job No,Year,Booking No.,Booking Date", "150,120,120,60,100,100","780","240",0, $sql , "js_set_value", "book_id,booking_prefix", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no,year,booking_prefix,booking_date", "",'','0,0,0,0,0,3','') ;
	}*/
	
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
			else if(str==2)
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('TNA Start From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('TNA Start To Date');
			}
			else if(str==3)
			{
				$('#from_date_html').html('');
				$('#from_date_html').html('TNA Finish From Date');
				$('#to_date_html').html('');
				$('#to_date_html').html('TNA Finish To Date');
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
							$search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No", 4 => "Internal Ref", 5 => "File No");
							$dd = "change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<?
							$search_type=array(1=>'Shipment Date',2=>'Knit TNA Start Date',3=>'Knit TNA Finish');
							echo create_drop_down( "cbo_date_type", 90, $search_type, "",0, "-- Select --", $selected, "fn_change_caption(this.value)" );
							?>
						</td>
						<td align="center"><input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px"  value="" placeholder="From Date" /></td>
						<td align="center"><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker"  style="width:55px"  value="" placeholder="To Date" /></td>

						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_date_type').value, 'action_create_order_listview', 'search_div', 'yarn_fabric_purchase_requisition_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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
	else if ($search_by == 4)
		$search_field = "b.grouping";
	else if ($search_by == 5)
		$search_field = "b.file_no";
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
	else if($cbo_date_category==2)
	{
		if ($start_date != "" && $end_date != "")
		{
			if ($db_type == 0)
			{
				$date_cond2 = "and c.task_start_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			}
			else
			{
				$date_cond2 = "and c.task_start_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}

			$tnaTaskNameCond = "and c.task_number=60";
		}
		else
		{
			$date_cond2 = "";
			$tnaTaskNameCond = "";
		}
	}
	else if($cbo_date_category==3)
	{
		if ($start_date != "" && $end_date != "")
		{
			if ($db_type == 0)
			{
				$date_cond2 = "and c.task_finish_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
			}
			else
			{
				$date_cond2 = "and c.task_finish_date between '" . change_date_format($start_date, '', '', 1) . "' and '" . change_date_format($end_date, '', '', 1) . "'";
			}
			$tnaTaskNameCond = "and c.task_number=60";
		}
		else
		{
			$date_cond2 = "";
			$tnaTaskNameCond = "";
		}
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = "";//defined Later

	$arr = array(0 => $company_arr, 1 => $buyer_arr);
	$sql = "select b.id, to_char(a.insert_date,'YYYY') as year, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b
	left join tna_process_mst c on b.id=c.po_number_id and c.status_active=1 and c.is_deleted=0 $date_cond2 $tnaTaskNameCond where a.job_no=b.job_no_mst and a.company_name=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $buyer_id_cond $date_cond group by b.id, a.insert_date, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date, b.grouping, b.file_no order by b.id, b.pub_shipment_date";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Internal Ref, File No, Shipment Date", "70,70,50,60,130,130,90,90", "860", "220", 0, $sql, "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,grouping,file_no,pub_shipment_date", "", '', '0,0,0,0,0,0,0,0,3', '', 1);
	exit();
}

//action_generate_report
if ($action=="action_generate_report")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_buyer_id = str_replace("'","",$cbo_buyer_id);
	$cbo_department_id = str_replace("'","",$cbo_department_id);
	$txt_season = str_replace("'","",$txt_season);
	$hdn_season = str_replace("'","",$hdn_season);
	$txt_style = str_replace("'","",$txt_style);
	$hdn_style = str_replace("'","",$hdn_style);
	$txt_job = str_replace("'","",$txt_job);
	$hdn_job = str_replace("'","",$hdn_job);
	$txt_order = str_replace("'","",$txt_order);
	$hdn_order = str_replace("'","",$hdn_order);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	
	//for buyer condition
	$buyer_condition = '';
	$buyer_condition2 = '';
	if($cbo_buyer_id != 0)
	{
		$buyer_condition = " AND a.buyer_id = ".$cbo_buyer_id."";
		$buyer_condition2 = " AND a.buyer_name = ".$cbo_buyer_id."";
	}
	
	//for season condition
	$season_condition = '';
	$season_condition2 = '';
	if($txt_season != '')
	{
		$season_condition = " AND b.job_no IN(SELECT job_no FROM wo_po_details_master WHERE company_id = ".$cbo_company_id." AND buyer_name = ".$cbo_buyer_id." AND season_buyer_wise IN(".$hdn_season."))";
		$season_condition2 = " AND season_buyer_wise IN(".$hdn_season.")";
	}
	
	//for style condition
	$style_condition = '';
	$style_condition2 = '';
	if($txt_style != '')
	{
		$style_condition = " AND b.job_no IN(SELECT job_no FROM wo_po_details_master WHERE id IN(".$hdn_style."))";
		$style_condition2 = " AND a.id IN(".$hdn_style.")";
	}
	
	//for job condition
	$job_condition = '';
	$job_condition2 = '';
	if($txt_job != '')
	{
		$job_condition = " AND b.job_no IN(SELECT job_no FROM wo_po_details_master WHERE id IN(".$hdn_job."))";
		$job_condition2 = " AND a.id IN(".$hdn_job.")";
	}
	
	//for order condition
	$order_condition = '';
	if($txt_order != '')
	{
		$order_condition = " AND b.po_break_down_id IN('".str_replace(',', "','", $hdn_order)."')";
	}

	//for date condition
	$date_condition = '';
	if( $txt_date_from != '' && $txt_date_to != '')
	{
		$date_condition=" AND a.booking_date BETWEEN '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' AND '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'";
	}
	
	//for woven information
	$sql_booking="SELECT a.id AS BOOKING_ID, a.booking_no AS BOOKING_NO, a.item_category AS ITEM_CATEGORY, a.pay_mode AS PAYMODE, a.remarks AS REMARKS, a.fabric_source AS FABRIC_SOURCE, a.is_approved AS IS_APPROVED, a.booking_no_prefix_num AS BOOKING_NO_PREFIX_NUM, a.booking_date AS BOOKING_DATE, a.supplier_id AS SUPPLIER_ID, a.buyer_id AS BUYER_ID, b.pre_cost_fabric_cost_dtls_id AS FAB_COST_DTLS_ID, b.fabric_color_id AS FABRIC_COLOR_ID, b.color_type AS COLOR_TYPE, b.uom AS UOM, b.gmts_color_id AS GMTS_COLOR_ID, b.construction AS CONSTRUCTION, b.copmposition AS COMPOSITION, b.dia_width AS DIA_WIDTH, b.gsm_weight AS GMS_WEIGHT, b.pre_cost_remarks AS PRE_REMARKS, b.remark AS DTLS_REMARKS, b.fin_fab_qnty AS FIN_FAB_QNTY, b.grey_fab_qnty AS GREY_FAB_QNTY, b.adjust_qty AS ADJUST_QTY, b.wo_qnty AS WO_QNTY, b.amount AS AMOUNT, b.rate AS RATE, b.job_no AS JOB_NO, b.po_break_down_id AS PO_ID, c.pub_shipment_date AS PUB_SHIPMENT_DATE, c.excess_cut AS EXCESS_CUT, c.po_received_date AS PO_RECEIVED_DATE, c.po_quantity AS PO_QUANTITY, c.unit_price AS UNIT_PRICE, c.plan_cut AS PLAN_CUT, c.po_total_price AS PO_TOTAL_PRICE, c.shipment_date AS SHIPMENT_DATE FROM wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c WHERE a.booking_no = b.booking_no AND b.job_no = c.job_no_mst AND b.po_break_down_id = c.id AND a.company_id = ".$cbo_company_id." AND a.booking_type IN(1) AND a.item_category IN(2,3) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 ".$buyer_condition." ".$season_condition." ".$style_condition." ".$job_condition." ".$order_condition." ".$date_condition." ORDER BY a.booking_no, b.job_no ";
	/*
	--AND a.job_no = b.job_no
	--AND a.po_break_down_id = b.po_break_down_id
	--AND a.po_break_down_id = c.id
	--AND a.is_short IN(2)
	*/
	//echo $sql_booking;
	$booking_data=sql_select($sql_booking);
	$jobNoArr = array();
	$poIdArr = array();
	$bookingArr = array();
	$preCostFabCostDtlsIdArr = array();
	$purchaseQty = array();
	foreach($booking_data as $row)
	{
		$jobNoArr[$row['JOB_NO']] = $row['JOB_NO'];
		$poIdArr[$row['PO_ID']] = $row['PO_ID'];
		$preCostFabCostDtlsIdArr[$row['FAB_COST_DTLS_ID']] = $row['FAB_COST_DTLS_ID'];
		$bookingArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
	}
	
	//for class condition
	$condition= new condition();
	if(!empty($poIdArr))
	{
		$condition->po_id_in(implode(",",$poIdArr));
	}

	//for fabric
	$condition->init();
	$fabric= new fabric($condition);
	//echo $fabric->getQuery(); die;
	$fabric_costing_arr = $fabric->getQtyArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
	$fabric_costing_arr_summary = $fabric->getQtyArray_by_orderAndFabriccostidSupplier_knitAndwoven_greyAndfinish();
	//echo "<pre>";
	//print_r($fabric_costing_arr_summary);
	//echo "<pre>";

	//for yarn
	$yarnCondition= new condition();
	if(!empty($jobNoArr))
	{
		$yarnCondition->job_no("IN('".implode("','",$jobNoArr)."')");
	}
	
	$yarnCondition->init();
	$yarn= new yarn($yarnCondition);
	//echo $yarn->getQuery();
	//$yarn_costing_arr = $yarn->getOrderCountAndCompositionWiseYarnQtyArray();
	$yarn_costing_arr = $yarn->getJobCountCompositionColorAndTypeWiseYarnQtyAndAmountArray();
	$yarn_costing_arr_summary = $yarn->getJobCountCompositionColorAndTypeSupplierWiseYarnQtyAndAmountArray();
	//echo "<pre>";
	//print_r($yarn_costing_arr_summary);
	//echo "<pre>";
	
	$job_arr = array();
	$wovenDataArr = array();
	$yarnDataArray = array();
	foreach($booking_data as $row)
	{
		$job_arr[$row['JOB_NO']] = $row['JOB_NO'];
		$purchaseQty[$row['FAB_COST_DTLS_ID']]['purchase_qty'] += $row['GREY_FAB_QNTY'];
		if($row['ITEM_CATEGORY'] == 3)
		{
			$total_grey_req_yds = array_sum($fabric_costing_arr['woven']['grey'][$row['PO_ID']][$row['FAB_COST_DTLS_ID']]);
			if(($total_grey_req_yds*1 > 0))
			{
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['order_qty_pcs'] = $row['PO_QUANTITY'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['order_rcv_date'] = date('d-m-Y', strtotime($row['PO_RECEIVED_DATE']));
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['shipment_date'] = date('d-m-Y', strtotime($row['SHIPMENT_DATE']));
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fob_price'] = $row['UNIT_PRICE'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fob_value'] = $row['PO_TOTAL_PRICE'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['excess_cutting'] = $row['EXCESS_CUT'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['qty_with_cutting'] = $row['PLAN_CUT'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['remarks'] = $row['DTLS_REMARKS'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fab_cost_dtls_id'] = $row['FAB_COST_DTLS_ID'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['po_id'] = $row['PO_ID'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['uom'] = $row['UOM'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['rate'] = $row['RATE'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['supplier'][$row['SUPPLIER_ID']] = $supllier_arr[$row['SUPPLIER_ID']];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['total_grey_req_yds'] += $total_grey_req_yds;
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['purchase_qty'] += $row['GREY_FAB_QNTY'];
			}
		}
		else if($row['ITEM_CATEGORY'] == 2)
		{
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['order_qty_pcs'] = $row['PO_QUANTITY'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['order_rcv_date'] = date('d-m-Y', strtotime($row['PO_RECEIVED_DATE']));
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['shipment_date'] = date('d-m-Y', strtotime($row['SHIPMENT_DATE']));
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fob_price'] = $row['UNIT_PRICE'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fob_value'] = $row['PO_TOTAL_PRICE'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['excess_cutting'] = $row['EXCESS_CUT'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['qty_with_cutting'] = $row['PLAN_CUT'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['remarks'] = $row['DTLS_REMARKS'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fab_cost_dtls_id'] = $row['FAB_COST_DTLS_ID'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['po_id'] = $row['PO_ID'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['uom'] = $row['UOM'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['rate'] = $row['RATE'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['supplier'][$row['SUPPLIER_ID']] = $supllier_arr[$row['SUPPLIER_ID']];
		}
	}
	unset($booking_data);
	//echo "<pre>";
	//print_r($yarnDataArray);
	//echo "</pre>";
	
	//for price dzn
	$sql_dzn = sql_select("SELECT JOB_NO, PRICE_DZN FROM wo_pre_cost_dtls WHERE status_active=1 AND is_deleted=0".where_con_using_array($jobNoArr, '1', 'JOB_NO'));
	$priceDznArr = array();
	foreach($sql_dzn as $row)
	{
		$priceDznArr[$row['JOB_NO']] = $row['PRICE_DZN'];
	}
	
	//for terns and condition
	$termsDataArr = array();
	$sql_terms_rlst = sql_select("SELECT TERMS FROM WO_BOOKING_TERMS_CONDITION WHERE ENTRY_FORM = 108 ".where_con_using_array($bookingArr, '1', 'BOOKING_NO')." ORDER BY ID");
	if(empty($sql_terms_rlst))
	{
		$sql_terms_rlst = sql_select("SELECT ID, TERMS FROM LIB_TERMS_CONDITION WHERE IS_DEFAULT = 1 AND PAGE_ID = 108 ORDER BY ID");
	}

	foreach($sql_terms_rlst as $row)
	{
		$termsDataArr[$row['TERMS']] = $row['TERMS'];
	}
	unset($sql_terms_rlst);

	//for yarn information
	$sql_yarn = "SELECT a.id AS ID, a.requ_no AS REQU_NO, b.id AS DTLS_ID, b.job_no AS JOB_NO, b.color_id AS COLOR_ID, b.count_id AS COUNT_ID, b.composition_id AS COMPOSITION_ID, b.com_percent AS COM_PERCENT, b.yarn_type_id AS YARN_TYPE_ID, b.cons_uom AS CONS_UOM, b.quantity AS QUANTITY, b.rate AS RATE, b.amount AS AMOUNT, b.remarks AS REMARKS FROM inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0".where_con_using_array($jobNoArr, '1', 'b.job_no')." ORDER BY b.id ASC";
	//echo $sql_yarn; die;
	$sql_yarn_rslt = sql_select($sql_yarn);
	$yarnDataArr = array();
	$reqDtlsIdArr = array();
	$requisitionNoArr = array();
	$compositionIdArr = array();
	$countIdArr = array();
	foreach($sql_yarn_rslt as $row)
	{
		$requisitionNoArr[$row['REQU_NO']] = $row['REQU_NO'];
		$reqDtlsIdArr[$row['DTLS_ID']] = $row['DTLS_ID'];
		$compositionIdArr[$row['COMPOSITION_ID']] = $row['COMPOSITION_ID'];
		$countIdArr[$row['COUNT_ID']] = $row['COUNT_ID'];

		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['color_id'] = $row['COLOR_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['count_id'] = $row['COUNT_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['remarks'] = $row['REMARKS'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['com_percent'] = $row['COM_PERCENT'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['yarn_type_id'] = $row['YARN_TYPE_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['quantity'] = $row['QUANTITY'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['rate'] = $row['RATE'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['dtls_id'] = $row['DTLS_ID'];

		$total_grey_req_kg = 0;
		foreach($yarn_costing_arr[$row['JOB_NO']][$row['COUNT_ID']][$row['COMPOSITION_ID']] as $percent=>$percentArr)
		{
			foreach($percentArr as $key=>$val)
			{
				$total_grey_req_kg = $val['qty'];
			}
		}
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['total_grey_req_kg'] += $total_grey_req_kg;
	}
	unset($sql_yarn_rslt);
	/*echo "<pre>";
	print_r($yarnDataArr);
	echo "</pre>";*/
	
	//for production information
	$sql_product = "SELECT ID, YARN_COMP_TYPE1ST, YARN_COUNT_ID, AVAILABLE_QNTY, LOT FROM PRODUCT_DETAILS_MASTER WHERE STATUS_ACTIVE = 1 ".where_con_using_array($compositionIdArr, '0', 'YARN_COMP_TYPE1ST')." ".where_con_using_array($countIdArr, '0', 'YARN_COUNT_ID');
	//echo $sql_product;
	$sql_product_rslt = sql_select($sql_product);
	$productDataArr = array();
	foreach($sql_product_rslt as $row)
	{
		$productDataArr[$row['YARN_COMP_TYPE1ST']][$row['YARN_COUNT_ID']]['available_qty'] += $row['AVAILABLE_QNTY'];
		if($row['AVAILABLE_QNTY'] != 0)
		{
			$productDataArr[$row['YARN_COMP_TYPE1ST']][$row['YARN_COUNT_ID']]['lot'][$row['LOT']]= $row['LOT'];
		}
	}
	unset($sql_product_rslt);
	
	//for Yarn Purchase Order iformation
	/*
	$sql_yarn_work_order = "SELECT A.SUPPLIER_ID, B.REQUISITION_DTLS_ID, B.YARN_COUNT, B.RATE FROM WO_NON_ORDER_INFO_MST A, WO_NON_ORDER_INFO_DTLS B WHERE A.ID = B.MST_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 ".where_con_using_array($reqDtlsIdArr, '0', 'B.REQUISITION_DTLS_ID');
	$sql_yarn_work_order_rslt = sql_select($sql_yarn_work_order);
	$yarnWorkOrderDataArr = array();
	foreach($sql_yarn_work_order_rslt as $row)
	{
		$yarnWorkOrderDataArr[$row['REQUISITION_DTLS_ID']][$row['YARN_COUNT']]['supplier_id'][$row['SUPPLIER_ID']] = $supllier_arr[$row['SUPPLIER_ID']];
		$yarnWorkOrderDataArr[$row['REQUISITION_DTLS_ID']][$row['YARN_COUNT']]['rate'] = $row['RATE'];
	}
	unset($sql_yarn_work_order_rslt);
	*/
	//"<pre>";
	//print_r($yarnWorkOrderDataArr);
	//echo "</pre>";
	
	//for yarn terns and condition
	$sql_terms_rlst = sql_select("SELECT TERMS FROM WO_BOOKING_TERMS_CONDITION WHERE ENTRY_FORM = 70 ".where_con_using_array($requisitionNoArr, '1', 'BOOKING_NO')." ORDER BY ID");
	if(empty($sql_terms_rlst))
	{
		$sql_terms_rlst = sql_select("SELECT ID, TERMS FROM LIB_TERMS_CONDITION WHERE IS_DEFAULT = 1 AND PAGE_ID = 70 ORDER BY ID");
	}

	foreach($sql_terms_rlst as $row)
	{
		$termsDataArr[$row['TERMS']] = $row['TERMS'];
	}
	unset($sql_terms_rlst);

	//for job details
	$sql_data="select a.job_no,a.job_no_prefix_num,a.company_name,a.season_buyer_wise,a.season_matrix,a.buyer_name,a.style_ref_no,a.team_leader,a.dealing_marchant, a.gmts_item_id,a.job_quantity, b.id,b.is_confirmed,b.po_number,b.pub_shipment_date,b.shipment_date,b.po_quantity,b.unit_price,b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_name=".$cbo_company_id." ".$buyer_condition2." ".$season_condition2." ".$style_condition2." ".$job_condition2." AND b.id IN(".implode(',', $poIdArr).")";
	//echo $sql_data;
	$res_data=sql_select($sql_data);
	$job_wise_arr=array();
	foreach( $res_data as $row_data)
	{
		//$po_id_arr[$row_data[csf('id')]]=$row_data[csf('id')];
		//$po_wise_arr[$row_data[csf('id')]]['job']=$row_data[csf('job_no')];
		//$job_wise_arr[$row_data[csf('job_no')]]['buyer']=$row_data[csf('buyer_name')];
		$job_wise_arr[$row_data[csf('job_no')]]['style']=$row_data[csf('style_ref_no')];
		//$job_wise_arr[$row_data[csf('job_no')]]['season']=$row_data[csf('season_buyer_wise')];
		//$job_wise_arr[$row_data[csf('job_no')]]['matrix_season']=$row_data[csf('season_matrix')];
		//$job_wise_arr[$row_data[csf('job_no')]]['team_leader']=$team_leader_arr[$row_data[csf('team_leader')]];
		//$job_wise_arr[$row_data[csf('job_no')]]['dealing_marchant']=$deal_merchant_arr[$row_data[csf('dealing_marchant')]];
	}
	//echo "<pre>";
	//print_r($job_wise_arr);
	
	//for fabric pre cost information
	$fabric_pre_cost_data = array();
	$yarn_pre_cost_data = array();
	$sql_fabric_pre_cost = "SELECT ID, JOB_NO, FAB_NATURE_ID, COLOR_TYPE_ID, LIB_YARN_COUNT_DETER_ID, CONSTRUCTION, COMPOSITION, FABRIC_DESCRIPTION, COLOR, AVG_CONS, AVG_CONS_YARN, AVG_FINISH_CONS, FABRIC_SOURCE, RATE, AMOUNT, AVG_PROCESS_LOSS
	FROM WO_PRE_COST_FABRIC_COST_DTLS
	WHERE STATUS_ACTIVE = 1 ".where_con_using_array($jobNoArr, '1', 'JOB_NO');
	//echo $sql_fabric_pre_cost;
	$sql_fabric_pre_cost_rslt=sql_select($sql_fabric_pre_cost);
	foreach($sql_fabric_pre_cost_rslt as $row)
	{
		//for woven
		if($row['FAB_NATURE_ID'] == 3)
		{
			$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['woven_avg_cons'] = $row['AVG_CONS'];
			$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['process_loss_percent'] = $row['AVG_PROCESS_LOSS'];
			$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['amount'] += ($row['AVG_CONS']*$row['RATE']);
		}
		//for knit
		else if($row['FAB_NATURE_ID'] == 2)
		{
			$yarn_pre_cost_data[$row['JOB_NO']]['yarn_process_loss']['process_loss_percent'] = $row['AVG_PROCESS_LOSS'];
			$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['avg_cons'] = $row['AVG_CONS'];
		}
	}
	unset($sql_fabric_pre_cost_rslt);
	//echo "<pre>";
	//print_r($fabric_pre_cost_data);
	//echo "</pre>";
	
	//for yarn pre cost information
	$sql_yarn_pre_cost = "SELECT ID, FABRIC_COST_DTLS_ID, JOB_NO, COUNT_ID, COPM_ONE_ID, PERCENT_ONE, COPM_TWO_ID, PERCENT_TWO, COLOR, TYPE_ID, CONS_RATIO, CONS_QNTY, AVG_CONS_QNTY, SUPPLIER_ID, RATE, AMOUNT, STATUS_ACTIVE FROM WO_PRE_COST_FAB_YARN_COST_DTLS WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($jobNoArr, '1', 'JOB_NO');
	//echo $sql_yarn_pre_cost;
	$sql_yarn_pre_cost_rslt=sql_select($sql_yarn_pre_cost);
	foreach($sql_yarn_pre_cost_rslt as $row)
	{
		//$yarn_pre_cost_data[$row['JOB_NO']][$row['COUNT_ID']]['avg_cons'] += $row['CONS_QNTY'];
		$yarn_pre_cost_data[$row['JOB_NO']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['avg_cons'] += $row['CONS_QNTY'];
		$yarn_pre_cost_data[$row['JOB_NO']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['amount'] += $row['AMOUNT'];
		
		//for supplier
		$pre_cost_supplier[$row['JOB_NO']][$row['COPM_ONE_ID']][$row['COUNT_ID']][$row['TYPE_ID']]['supplier_id'][$row['SUPPLIER_ID']] = $supllier_arr[$row['SUPPLIER_ID']];
	}
	unset($sql_yarn_pre_cost_rslt);
	/*echo "<pre>";
	print_r($pre_cost_supplier);
	echo "</pre>";*/

	//for images
	$images = return_library_array("select master_tble_id, image_location from common_photo_library where file_type = 1".where_con_using_array($jobNoArr, '1', 'master_tble_id'), "master_tble_id", "image_location");
	//echo "<pre>";
	//print_r($images);
	
	//for company details
	$company_library = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

	//for row span
	$jobRowSpan = array();
	$yarnJobRowSpan = array();
	foreach($job_arr as $job)
	{
		//for woven info
		foreach($wovenDataArr[$job] as $fabric=>$fabricArr)
		{
			foreach($fabricArr as $colorType=>$row)
			{
				$jobRowSpan[$job]++;
			}
		}
		
		//for yarn info
		foreach($yarnDataArr[$job] as $yarnComposition=>$yarnCompArr)
		{
			foreach($yarnCompArr as $yarnCnt=>$yarnRow)
			{
				$yarnJobRowSpan[$job]++;
			}
		}
	}
	//echo "<pre>";
	//print_r($jobRowSpan);
	//echo "</pre>";
	?>
	<!--<fieldset>-->
	<!--<style type="text/css">
		table tr td {
			font-size: 13px;
		}
		.rpt_table thead th{
			font-size: 14px;
		}
		.rpt_table tfoot th{
			font-size: 14px;
		}
	</style>-->		
    <div style="width:2030">
        <table width="2030"  cellspacing="0">
			<tr class="form_caption" style="border:none;">
				<td align="center" style="border:none; font-size:18px;" colspan="22">
					<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
				</td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="25"> <? echo $report_title ;?></td>
			</tr>
		</table>
		<table width="2030" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<th width="40">SL</th>
				<th width="100">Job No</th>
				<th width="100">Picture</th>
				<th width="100">Style Ref.</th>
				<th width="150">Fabric/YARN Construction</th>
				<th width="120">Color</th>
				<th width="60">Yarn count</th>
				<th width="70">Woven Fabric Reff No</th>
				<th width="70">Order Quantity (Pcs)</th>
				<th width="70">Order Receive Date</th>
				<th width="70">Shipment Date</th>
				<th width="70">FOB Price</th>
				<th width="70">FOB Value</th>
				<th width="50">Excess cutting %</th>
				<th width="50">Qty with Cutting %</th>
				<th width="50">Process Loss %</th>
				<th width="70">Total Cons. Yarn</th>
				<th width="70">Total Cons. Woven Fabric</th>
				<th width="70">Total Grey Req. (Yds)</th>
				<th width="70">Total Grey Req. (Kgs)</th>
				<th width="70">Unallocated Yarn Stock</th>
				<th width="100">Stock Ref. (Lot)</th>
				<th width="70">Balance</th>
				<th width="70">Need to purchase</th>
				<th>Remarks</th>
			</thead>
		</table>
		<div style="max-height:350px; overflow-y:scroll; width:2030px" id="scroll_body" >
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2010" rules="all" id="table_body" >
				<tbody>
                <?php
				$sl = 0;
				$zs = 0;
				$totalGreyReqYds = 0;
				$totalGreyReqKgs = 0;
				$totalBalance = 0;
				$totalPurchase = 0;
				$summary_data = array();
				foreach($job_arr as $job)
				{
					$bgcolor="#FFFFFF";
					$style = $job_wise_arr[$job]['style'];
					//for woven info
					if(!empty($wovenDataArr[$job]))
					{
						$sl++;
						$rowSpanCond = 0;
						foreach($wovenDataArr[$job] as $fabric=>$fabricArr)
						{
							foreach($fabricArr as $colorType=>$row)
							{
								$zs++;
								/*if ($sl%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";*/
								
								$process_loss = $fabric_pre_cost_data[$row['fab_cost_dtls_id']][$job]['process_loss_percent'].'%';
								$consumption_woven_fabric = $fabric_pre_cost_data[$row['fab_cost_dtls_id']][$job]['woven_avg_cons'];
								
								//$total_grey_req_yds = array_sum($fabric_costing_arr['woven']['grey'][$row['po_id']][$row['fab_cost_dtls_id']]);
								$total_grey_req_yds = $row['total_grey_req_yds']*1;
								//$purchase_qty = $purchaseQty[$job][$fabric][$colorType][$row['fab_cost_dtls_id']]['purchase_qty'];
								//$fabric_purchase_qty = $purchaseQty[$row['fab_cost_dtls_id']]['purchase_qty'];
								$fabric_purchase_qty = $row['purchase_qty'];
								
								//$balance_qty = $total_grey_req_yds-$fabric_purchase_qty;
								$balance_qty = $total_grey_req_yds;
								
								//for total
								$totalGreyReqYds += $total_grey_req_yds;
								$totalBalance += $balance_qty;
								$totalPurchase += $fabric_purchase_qty;
	
								//for summary
								$tot_grey_req_yds = 0;
								foreach($row['supplier'] as $key=>$supplier)
								{
									if($key*1 != 0)
									{
										$tot_grey_req_yds += array_sum($fabric_costing_arr_summary['woven']['grey'][$row['po_id']][$row['fab_cost_dtls_id']][$key]);
										$summary_data['woven'][$fabric][$supplier][$row['rate']]['fabric'] = $fabric;
										$summary_data['woven'][$fabric][$supplier][$row['rate']]['count'] = '';
										$summary_data['woven'][$fabric][$supplier][$row['rate']]['required_qty'] += $tot_grey_req_yds;
										$summary_data['woven'][$fabric][$supplier][$row['rate']]['stock'] = '';
										$summary_data['woven'][$fabric][$supplier][$row['rate']]['purchase_qty'] += $fabric_purchase_qty;
										$summary_data['woven'][$fabric][$supplier][$row['rate']]['mill'] = $row['supplier'];
										$summary_data['woven'][$fabric][$supplier][$row['rate']]['rate'] = $row['rate'];
										$summary_data['woven'][$fabric][$supplier][$row['rate']]['fabric_yarn_value'] += $fabric_purchase_qty*$row['rate'];
										
										$amount = $fabric_pre_cost_data[$row['fab_cost_dtls_id']][$job]['amount'];
										$price_dzn = $priceDznArr[$job];
										$summary_data['woven'][$fabric][$supplier][$row['rate']]['from_total_value'] += $amount/$price_dzn*100;
										
										$total_fabric_value += $fabric_purchase_qty*$row['rate'];	
										$total_fabric_from_value += $amount/$price_dzn*100;	
									}
								}

								$rowSpan = $jobRowSpan[$job];
								if($rowSpanCond == 0)
								{
									$rowSpanCond++;
									?>
									<tr valign="top" height="20" bgcolor="<? echo $bgcolor; ?>">
										<td width="40" align="center" rowspan="<?php echo $rowSpan; ?>"><?php echo $sl; ?></td>
										<td width="100" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $job; ?></td>
										<td width="100" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><img src="<?php echo '../../'.$images[$job]; ?>" height="97" width="89" /></td>
										<td width="100" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $style; ?></td>
										<td width="150" style="word-break: break-all;"><?php echo $fabric; ?></td>
										<td width="120" align="center" style="word-break: break-all;"><?php echo $color_type[$colorType]; ?></td>
										<td width="60" style="word-break: break-all;"></td>

<td width="70" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php //echo $colorType; ?></td>
<td width="70" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['order_qty_pcs']; ?></td>
<td width="70" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['order_rcv_date']; ?></td>
<td width="70" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['shipment_date']; ?></td>

<td width="70" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo '$'.number_format($row['fob_price'], 2); ?></td>
<td width="70" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo '$'.number_format($row['fob_value'], 2); ?></td>
<td width="50" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo number_format($row['excess_cutting'],2).'%'; ?></td>
<td width="50" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['qty_with_cutting'].' Pcs'; ?></td>
										
<td width="50" align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($consumption_woven_fabric, 2).' Yds'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_yds, 2).' Yds'; ?></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" style="word-break: break-all;"></td>
<td width="100" style="word-break: break-all;"></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($balance_qty, 2).' Yds'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($fabric_purchase_qty, 2).' Yds'; ?></td>
<td style="word-break: break-all;"><?php echo $row['remarks']; ?></td>
									</tr>
									<?php
								}
								else
								{
									?>
									<tr valign="top" height="20" bgcolor="<? echo $bgcolor; ?>">
										<td style="word-break: break-all;"><?php echo $fabric; ?></td>
										<td align="center" style="word-break: break-all;"><?php echo $color_type[$colorType]; ?></td>
										<td style="word-break: break-all;"></td>
										<td align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
										<td style="word-break: break-all;"></td>
										<td align="right" style="word-break: break-all;"><?php echo number_format($consumption_woven_fabric, 2).' Yds'; ?></td>
										<td align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_yds, 2).' Yds'; ?></td>
										<td style="word-break: break-all;"></td>
										<td style="word-break: break-all;"></td>
										<td style="word-break: break-all;"></td>
										<td align="right" style="word-break: break-all;"><?php echo number_format($balance_qty, 2).' Yds'; ?></td>
										<td  align="right" style="word-break: break-all;"><?php echo number_format($fabric_purchase_qty, 2).' Yds'; ?></td>
										<td style="word-break: break-all;"><?php echo $row['remarks']; ?></td>
									</tr>
									<?php
								}
							}
						}
					}
					
					//for yarn info
					if(!empty($yarnDataArr[$job]))
					{
						$sl++;
						$yarnRowSpanCond = 0;
						foreach($yarnDataArr[$job] as $yarnComposition=>$yarnComposArr)
						{
							foreach($yarnComposArr as $yarnCountId=>$yarnRow)
							{
								$yarnRow['order_qty_pcs'] = '';
								$yarnRow['order_rcv_date'] = '';
								$yarnRow['shipment_date'] = '';
								$yarnRow['fob_price'] = '';
								$yarnRow['fob_value'] = '';
								$yarnRow['excess_cutting'] = '';
								$yarnRow['qty_with_cutting'] = '';
								
								foreach($yarnDataArray[$job] as $compo=>$compo_arr)
								{
									foreach($compo_arr as $clr_type=>$clr_arr)
									{
										$yarnRow['order_qty_pcs'] += $clr_arr['order_qty_pcs'];
										$yarnRow['order_rcv_date'] = $clr_arr['order_rcv_date'];
										$yarnRow['shipment_date'] = $clr_arr['shipment_date'];
										$yarnRow['fob_price'] = $clr_arr['fob_price'];
										$yarnRow['fob_value'] = $clr_arr['fob_value'];
										$yarnRow['excess_cutting'] = $clr_arr['excess_cutting'];
										$yarnRow['qty_with_cutting'] = $clr_arr['qty_with_cutting'];
									}
								}
								
								$process_loss = $yarn_pre_cost_data[$job]['yarn_process_loss']['process_loss_percent'].'%';
								$consumption_yarn = $yarn_pre_cost_data[$job][$yarnComposition][$yarnRow['count_id']]['avg_cons'];
								$fabric = $composition[$yarnComposition].' '.$yarnRow['com_percent'].' % '.$yarn_type[$yarnRow['yarn_type_id']];
								$total_grey_req_kg = $yarnRow['total_grey_req_kg'];
								$unallocated_qty = $productDataArr[$yarnComposition][$yarnRow['count_id']]['available_qty'];
								$product_id = implode(', ', $productDataArr[$yarnComposition][$yarnRow['count_id']]['lot']);
								$yarn_purchase_qty = $yarnRow['quantity']*1;
								$yarn_balance_qty = number_format($total_grey_req_kg,2,'.','')-number_format($unallocated_qty,2,'.','');
								
								//for total
								$totalGreyReqKgs += $total_grey_req_kg;
								$totalBalance += $yarn_balance_qty;
								$totalPurchase += $yarn_purchase_qty;
								
								//for summary
								$tot_grey_req_kg = 0;
								foreach($pre_cost_supplier[$job][$yarnComposition][$yarnCountId][$yarnRow['yarn_type_id']]['supplier_id'] as $sup=>$supplier)
								{
									if($sup*1 != 0)
									{
										$tot_grey_req_kg = 0;
										foreach($yarn_costing_arr_summary[$job][$yarnCountId][$yarnComposition] as $percent=>$percentArr)
										{
											foreach($percentArr as $typ=>$typArr)
											{
												foreach($typArr as $key=>$val)
												{
													$tot_grey_req_kg += $val['qty'];
												}
											}
										}
										
										$cnt = $yarn_count_details[$yarnRow['count_id']];
										$summary_data['yarn'][$fabric][$cnt][$sup][$yarnRow['rate']]['fabric'] = $fabric;
										$summary_data['yarn'][$fabric][$cnt][$sup][$yarnRow['rate']]['count'] = $cnt;
										$summary_data['yarn'][$fabric][$cnt][$sup][$yarnRow['rate']]['required_qty'] += $tot_grey_req_kg;
										$summary_data['yarn'][$fabric][$cnt][$sup][$yarnRow['rate']]['stock'] = $product_id;
										$summary_data['yarn'][$fabric][$cnt][$sup][$yarnRow['rate']]['purchase_qty'] += $yarn_purchase_qty;
										$summary_data['yarn'][$fabric][$cnt][$sup][$yarnRow['rate']]['mill'] = $supplier;
										$summary_data['yarn'][$fabric][$cnt][$sup][$yarnRow['rate']]['rate'] = $yarnRow['rate'];
										$summary_data['yarn'][$fabric][$cnt][$sup][$yarnRow['rate']]['fabric_yarn_value'] = $yarn_purchase_qty*$yarnRow['rate'];
										$amount = $yarn_pre_cost_data[$fabric][$yarnComposition][$yarnRow['count_id']]['amount'];
										$price_dzn = $priceDznArr[$job];
										$amnt = 0;
										if($price_dzn*1 != 0)
										{
											$amnt = ($amount/$price_dzn)*100;
										}
										
										$summary_data['yarn'][$fabric][$cnt][$sup][$yarnRow['rate']]['from_total_value'] = $amnt;
										$total_yarn_value += $yarn_purchase_qty*$yarnRow['rate'];	
										$total_yarn_from_value += $amnt;
									}
								}

								$yarnRowSpan = $yarnJobRowSpan[$job];
								if($yarnRowSpanCond == 0)
								{
									$yarnRowSpanCond++;
									?>
									<tr valign="top" height="20" bgcolor="<? echo $bgcolor; ?>">
										<td width="40" rowspan="<? echo $yarnRowSpan; ?>" align="center"><?php echo $sl; ?></td>
										<td width="100" rowspan="<? echo $yarnRowSpan; ?>" align="center" style="word-break: break-all;"><?php echo $job; ?></td>
										<td width="100" rowspan="<? echo $yarnRowSpan; ?>" style="word-break: break-all;">
											<img src="<?php echo '../../'.$images[$job]; ?>" height="97" width="89" />
										</td>
										<td width="100" rowspan="<? echo $yarnRowSpan; ?>" align="center" style="word-break: break-all;"><?php echo $style; ?></td>
										<td width="150" style="word-break: break-all;"><?php echo $fabric; ?></td>
										<td width="120" align="center" style="word-break: break-all;"><?php echo $color_library[$yarnRow['color_id']]; ?></td>
										<td width="60" style="word-break: break-all;"><?php echo $yarn_count_details[$yarnRow['count_id']]; ?></td>
                                        
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" style="word-break: break-all;"><?php //Woven Fabric Reff No; ?></td>
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" align="right" style="word-break: break-all;"><?php echo $yarnRow['order_qty_pcs']; ?></td>
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" align="center" style="word-break: break-all;"><?php echo $yarnRow['order_rcv_date']; ?></td>
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" align="center" style="word-break: break-all;"><?php echo $yarnRow['shipment_date']; ?></td>
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" align="right" style="word-break: break-all;"><?php echo '$'.number_format($yarnRow['fob_price'], 2); ?></td>
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" align="right" style="word-break: break-all;"><?php echo '$'.number_format($yarnRow['fob_value'], 2); ?></td>
<td width="50" rowspan="<? echo $yarnRowSpan; ?>" align="center" style="word-break: break-all;"><?php echo number_format($yarnRow['excess_cutting'],2).'%'; ?></td>
<td width="50" rowspan="<? echo $yarnRowSpan; ?>" align="right" style="word-break: break-all;"><?php echo $yarnRow['qty_with_cutting'].' Pcs'; ?></td>

<td width="50" align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($consumption_yarn, 2).' Kgs'; ?></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_kg, 2).' Kgs'; ?></td>
<td width="70" style="word-break: break-all;"><?php echo number_format($unallocated_qty, 2); ?></td>
<td width="100" style="word-break: break-all;"><?php echo $product_id; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($yarn_balance_qty, 2).' Kgs'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($yarn_purchase_qty, 2).' Kgs'; ?></td>
<td style="word-break: break-all;"><?php echo $yarnRow['remarks']; ?></td>
									</tr>
									<?php
								}
								else
								{
								?>
									<tr valign="top" height="20" bgcolor="<? echo $bgcolor; ?>">
<td width="150" style="word-break: break-all;"><?php echo $fabric; ?></td>
<td width="120" align="center" style="word-break: break-all;"><?php echo $color_library[$yarnRow['color_id']]; ?></td>
<td width="60" style="word-break: break-all;"><?php echo $yarn_count_details[$yarnRow['count_id']]; ?></td>

<td width="50" align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($consumption_yarn, 2).' Kgs'; ?></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_kg, 2).' Kgs'; ?></td>
<td width="70" style="word-break: break-all;"><?php echo number_format($unallocated_qty, 2); ?></td>
<td width="100" style="word-break: break-all;"><?php echo $product_id; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($yarn_balance_qty, 2).' Kgs'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($yarn_purchase_qty, 2).' Kgs'; ?></td>
<td style="word-break: break-all;"><?php echo $yarnRow['remarks']; ?></td>
									</tr>
								<?php
								}
							}
						}
					}
				}
				?>
            	</tbody>
                <tfoot>
                	<tr>

                    	<th align="right" colspan="18">Total&nbsp;</th>
                        <th align="right"><?php echo number_format($totalGreyReqYds, 2).' Yds'; ?></th>
                        <th align="right"><?php echo number_format($totalGreyReqKgs, 2).' Kgs'; ?></th>
                        <th></th>
                        <th></th>
                        <th align="right"><?php //echo number_format($totalBalance, 2); ?></th>
                        <th align="right"><?php //echo number_format($totalPurchase, 2); ?></th>
                        <th></td>
                    </tr>
                </tfoot>
            </table>
		</div>
        <table width="940" cellspacing="0" border="1" class="rpt_table" rules="all" style="margin-top:10px;">
            <thead>
                <th width="40">SL</th>
                <th width="130">Fabric/YARN</th>
                <th width="70">Yarn count</th>
                <th width="100">Required Qty</th>
                <th width="100">Stock</th>
                <th width="100">Purchase Qty</th>
                <th width="120">Mill</th>
                <th width="80">Rate</th>
                <th width="100">Woven Fabric/Yarn Value</th>
                <th width="100">% From total Value</th>
            </thead>
            <tbody>
            <?php
            $sl = 0;
            foreach($summary_data['woven'] as $fb=>$fb_arr)
			{
				foreach($fb_arr as $mill=>$mill_arr)
				{
					foreach($mill_arr as $rate=>$val)
					{
						$sl++;
						?>
						<tr height="20">
							<td><?php echo $sl; ?></td>
							<td style="word-break: break-all;"><?php echo $val['fabric']; ?></td>
							<td><?php echo $val['count']; ?></td>
							<td align="right"><?php echo number_format($val['required_qty'], 2).' Yds'; ?></td>
							<td style="word-break: break-all;"><?php echo $val['stock']; ?></td>
							<td align="right"><?php echo number_format($val['purchase_qty'], 2).' Yds'; ?></td>
							<td style="word-break: break-all;"><?php echo $mill; ?></td>
							<td align="center"><?php echo '$'.number_format($val['rate'], 4); ?></td>
							<td align="right"><?php echo '$'.number_format($val['fabric_yarn_value'], 2); ?></td>
							<td align="right"><?php echo number_format($val['from_total_value'], 2); ?>%</td>
						</tr>
						<?php
					}
				}
			}
            foreach($summary_data['yarn'] as $fb=>$fb_arr)
			{
				foreach($fb_arr as $cnt=>$cnt_arr)
				{
					foreach($cnt_arr as $sup=>$sup_arr)
					{
						foreach($sup_arr as $rate=>$val)
						{
							$sl++;
							?>
							<tr height="20">
								<td><?php echo $sl; ?></td>
								<td style="word-break: break-all;"><?php echo $val['fabric']; ?></td>
								<td><?php echo $val['count']; ?></td>
								<td align="right"><?php echo number_format($val['required_qty'], 2).' Kgs'; ?></td>
								<td style="word-break: break-all;"><?php echo $val['stock']; ?></td>
								<td align="right"><?php echo number_format($val['purchase_qty'], 2).' Kgs'; ?></td>
								<td style="word-break: break-all;"><?php echo $val['mill']; ?></td>
								<td align="center"><?php echo '$'.number_format($val['rate'], 4); ?></td>
								<td align="right"><?php echo '$'.number_format($val['fabric_yarn_value'], 2); ?></td>
								<td align="right"><?php echo number_format($val['from_total_value'], 2); ?>%</td>
							</tr>
							<?php
						}
					}
				}
			}
            ?>
            </tbody>
            <tfoot style="font-weight:bold;">
                <?php
				if($total_fabric_value>0)
				{
				?>
                <tr height="20">
                    <td colspan="8" align="center">TOTAL FABRIC COST</td>
                    <td align="right"><?php echo '$'.number_format($total_fabric_value, 2); ?></td>
                    <td align="right"><?php echo number_format($total_fabric_from_value, 2); ?>%</td>
                </tr>
                <?php
				}
				
				if($total_yarn_value>0)
				{
				?>
                <tr height="20">
                    <td colspan="8" align="center">TOTAL YARN COST</td>
                    <td align="right"><?php echo '$'.number_format($total_yarn_value, 2); ?></td>
                    <td align="right"><?php echo number_format($total_yarn_from_value, 2); ?>%</td>
                </tr>
                <?php
				}
				?>
            </tfoot>
        </table>
        <table width="940" cellspacing="0" border="0" style="margin-top:30px;">
            <tbody>
                <tr height="20">
                    <td colspan="2"><strong><u>Remarks :</u></strong></td>
                </tr>
                <?php
                $sl = 0;
                foreach($termsDataArr as $key=>$val)
                {
                    $sl++;
                    ?>
                    <tr height="20">
                        <td width="20" valign="top"><?php echo $sl.'.'; ?></td>
                        <td valign="top"><p><?php echo $val; ?></p></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <br/> <br/> <br/><br/> <br/> <br/>
        <div style="width:100%;">
            <table cellspacing="0" width="100%" border="0" >
                <tr align="center">
                    <td style="text-decoration:overline; font-weight:bold;">MERCHANDISER</td>
                    <td style="text-decoration:overline; font-weight:bold;">MERCHANDISING AGM</td>
                    <td style="text-decoration:overline; font-weight:bold;">MERCHANDISING DIRECTOR</td>
                </tr>
            </table>
        </div>
        </div>
	<!--</fieldset>-->
	<?php
	//yarnWorkOrderDataArr
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
	echo "$total_data####$filename";
	exit();
}

if ($action=="action_generate_report_26082021")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_buyer_id = str_replace("'","",$cbo_buyer_id);
	$cbo_department_id = str_replace("'","",$cbo_department_id);
	$txt_season = str_replace("'","",$txt_season);
	$hdn_season = str_replace("'","",$hdn_season);
	$txt_style = str_replace("'","",$txt_style);
	$hdn_style = str_replace("'","",$hdn_style);
	$txt_job = str_replace("'","",$txt_job);
	$hdn_job = str_replace("'","",$hdn_job);
	$txt_order = str_replace("'","",$txt_order);
	$hdn_order = str_replace("'","",$hdn_order);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	
	//for buyer condition
	$buyer_condition = '';
	$buyer_condition2 = '';
	if($cbo_buyer_id != 0)
	{
		$buyer_condition = " AND a.buyer_id = ".$cbo_buyer_id."";
		$buyer_condition2 = " AND a.buyer_name = ".$cbo_buyer_id."";
	}
	
	//for season condition
	$season_condition = '';
	$season_condition2 = '';
	if($txt_season != '')
	{
		$season_condition = " AND b.job_no IN(SELECT job_no FROM wo_po_details_master WHERE company_id = ".$cbo_company_id." AND buyer_name = ".$cbo_buyer_id." AND season_buyer_wise IN(".$hdn_season."))";
		$season_condition2 = " AND season_buyer_wise IN(".$hdn_season.")";
	}
	
	//for style condition
	$style_condition = '';
	$style_condition2 = '';
	if($txt_style != '')
	{
		$style_condition = " AND b.job_no IN(SELECT job_no FROM wo_po_details_master WHERE id IN(".$hdn_style."))";
		$style_condition2 = " AND a.id IN(".$hdn_style.")";
	}
	
	//for job condition
	$job_condition = '';
	$job_condition2 = '';
	if($txt_job != '')
	{
		$job_condition = " AND b.job_no IN(SELECT job_no FROM wo_po_details_master WHERE id IN(".$hdn_job."))";
		$job_condition2 = " AND a.id IN(".$hdn_job.")";
	}
	
	//for order condition
	$order_condition = '';
	if($txt_order != '')
	{
		$order_condition = " AND b.po_break_down_id IN('".str_replace(',', "','", $hdn_order)."')";
	}

	//for date condition
	$date_condition = '';
	if( $txt_date_from != '' && $txt_date_to != '')
	{
		$date_condition=" AND a.booking_date BETWEEN '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' AND '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'";
	}
	
	//for woven information
	$sql_booking="SELECT a.id AS BOOKING_ID, a.booking_no AS BOOKING_NO, a.item_category AS ITEM_CATEGORY, a.pay_mode AS PAYMODE, a.remarks AS REMARKS, a.fabric_source AS FABRIC_SOURCE, a.is_approved AS IS_APPROVED, a.booking_no_prefix_num AS BOOKING_NO_PREFIX_NUM, a.booking_date AS BOOKING_DATE, a.supplier_id AS SUPPLIER_ID, a.buyer_id AS BUYER_ID, b.pre_cost_fabric_cost_dtls_id AS FAB_COST_DTLS_ID, b.fabric_color_id AS FABRIC_COLOR_ID, b.color_type AS COLOR_TYPE, b.uom AS UOM, b.gmts_color_id AS GMTS_COLOR_ID, b.construction AS CONSTRUCTION, b.copmposition AS COMPOSITION, b.dia_width AS DIA_WIDTH, b.gsm_weight AS GMS_WEIGHT, b.pre_cost_remarks AS PRE_REMARKS, b.remark AS DTLS_REMARKS, b.fin_fab_qnty AS FIN_FAB_QNTY, b.grey_fab_qnty AS GREY_FAB_QNTY, b.adjust_qty AS ADJUST_QTY, b.wo_qnty AS WO_QNTY, b.amount AS AMOUNT, b.rate AS RATE, b.job_no AS JOB_NO, b.po_break_down_id AS PO_ID, c.pub_shipment_date AS PUB_SHIPMENT_DATE, c.excess_cut AS EXCESS_CUT, c.po_received_date AS PO_RECEIVED_DATE, c.po_quantity AS PO_QUANTITY, c.unit_price AS UNIT_PRICE, c.plan_cut AS PLAN_CUT, c.po_total_price AS PO_TOTAL_PRICE, c.shipment_date AS SHIPMENT_DATE FROM wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c WHERE a.booking_no = b.booking_no AND b.job_no = c.job_no_mst AND b.po_break_down_id = c.id AND a.company_id = ".$cbo_company_id." AND a.booking_type IN(1) AND a.item_category IN(2,3) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 ".$buyer_condition." ".$season_condition." ".$style_condition." ".$job_condition." ".$order_condition." ".$date_condition." ORDER BY a.booking_no, b.job_no ";
	/*
	--AND a.job_no = b.job_no
	--AND a.po_break_down_id = b.po_break_down_id
	--AND a.po_break_down_id = c.id
	--AND a.is_short IN(2)
	*/
	//echo $sql_booking;
	$booking_data=sql_select($sql_booking);
	$jobNoArr = array();
	$poIdArr = array();
	$bookingArr = array();
	$preCostFabCostDtlsIdArr = array();
	$purchaseQty = array();
	foreach($booking_data as $row)
	{
		$jobNoArr[$row['JOB_NO']] = $row['JOB_NO'];
		$poIdArr[$row['PO_ID']] = $row['PO_ID'];
		$preCostFabCostDtlsIdArr[$row['FAB_COST_DTLS_ID']] = $row['FAB_COST_DTLS_ID'];
		$bookingArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
	}
	
	//for class condition
	$condition= new condition();
	if(!empty($poIdArr))
	{
		$condition->po_id_in(implode(",",$poIdArr));
	}

	//for fabric
	$condition->init();
	$fabric= new fabric($condition);
	//echo $fabric->getQuery(); die;
	$fabric_costing_arr = $fabric->getQtyArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
	$fabric_costing_arr_summary = $fabric->getQtyArray_by_orderAndFabriccostidSupplier_knitAndwoven_greyAndfinish();
	//echo "<pre>";
	//print_r($fabric_costing_arr_summary);
	//echo "<pre>";

	//for yarn
	$yarnCondition= new condition();
	if(!empty($jobNoArr))
	{
		$yarnCondition->job_no("IN('".implode("','",$jobNoArr)."')");
	}
	
	$yarnCondition->init();
	$yarn= new yarn($yarnCondition);
	//echo $yarn->getQuery();
	//$yarn_costing_arr = $yarn->getOrderCountAndCompositionWiseYarnQtyArray();
	$yarn_costing_arr = $yarn->getJobCountCompositionColorAndTypeWiseYarnQtyAndAmountArray();
	$yarn_costing_arr_summary = $yarn->getJobCountCompositionColorAndTypeSupplierWiseYarnQtyAndAmountArray();
	//echo "<pre>";
	//print_r($yarn_costing_arr_summary);
	//echo "<pre>";
	
	$job_arr = array();
	$wovenDataArr = array();
	$yarnDataArray = array();
	foreach($booking_data as $row)
	{
		$job_arr[$row['JOB_NO']] = $row['JOB_NO'];
		$purchaseQty[$row['FAB_COST_DTLS_ID']]['purchase_qty'] += $row['GREY_FAB_QNTY'];
		if($row['ITEM_CATEGORY'] == 3)
		{
			$total_grey_req_yds = array_sum($fabric_costing_arr['woven']['grey'][$row['PO_ID']][$row['FAB_COST_DTLS_ID']]);
			if(($total_grey_req_yds*1 > 0))
			{
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['order_qty_pcs'] = $row['PO_QUANTITY'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['order_rcv_date'] = date('d-m-Y', strtotime($row['PO_RECEIVED_DATE']));
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['shipment_date'] = date('d-m-Y', strtotime($row['SHIPMENT_DATE']));
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fob_price'] = $row['UNIT_PRICE'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fob_value'] = $row['PO_TOTAL_PRICE'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['excess_cutting'] = $row['EXCESS_CUT'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['qty_with_cutting'] = $row['PLAN_CUT'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['remarks'] = $row['DTLS_REMARKS'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fab_cost_dtls_id'] = $row['FAB_COST_DTLS_ID'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['po_id'] = $row['PO_ID'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['uom'] = $row['UOM'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['rate'] = $row['RATE'];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['supplier'][$row['SUPPLIER_ID']] = $supllier_arr[$row['SUPPLIER_ID']];
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['total_grey_req_yds'] += $total_grey_req_yds;
				$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['purchase_qty'] += $row['GREY_FAB_QNTY'];
			}
		}
		else if($row['ITEM_CATEGORY'] == 2)
		{
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['order_qty_pcs'] = $row['PO_QUANTITY'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['order_rcv_date'] = date('d-m-Y', strtotime($row['PO_RECEIVED_DATE']));
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['shipment_date'] = date('d-m-Y', strtotime($row['SHIPMENT_DATE']));
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fob_price'] = $row['UNIT_PRICE'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fob_value'] = $row['PO_TOTAL_PRICE'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['excess_cutting'] = $row['EXCESS_CUT'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['qty_with_cutting'] = $row['PLAN_CUT'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['remarks'] = $row['DTLS_REMARKS'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fab_cost_dtls_id'] = $row['FAB_COST_DTLS_ID'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['po_id'] = $row['PO_ID'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['uom'] = $row['UOM'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['rate'] = $row['RATE'];
			$yarnDataArray[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['supplier'][$row['SUPPLIER_ID']] = $supllier_arr[$row['SUPPLIER_ID']];
		}
	}
	unset($booking_data);
	//echo "<pre>";
	//print_r($yarnDataArray);
	//echo "</pre>";
	
	//for price dzn
	$sql_dzn = sql_select("SELECT JOB_NO, PRICE_DZN FROM wo_pre_cost_dtls WHERE status_active=1 AND is_deleted=0".where_con_using_array($jobNoArr, '1', 'JOB_NO'));
	$priceDznArr = array();
	foreach($sql_dzn as $row)
	{
		$priceDznArr[$row['JOB_NO']] = $row['PRICE_DZN'];
	}
	
	//for terns and condition
	$termsDataArr = array();
	$sql_terms_rlst = sql_select("SELECT TERMS FROM WO_BOOKING_TERMS_CONDITION WHERE ENTRY_FORM = 108 ".where_con_using_array($bookingArr, '1', 'BOOKING_NO')." ORDER BY ID");
	if(empty($sql_terms_rlst))
	{
		$sql_terms_rlst = sql_select("SELECT ID, TERMS FROM LIB_TERMS_CONDITION WHERE IS_DEFAULT = 1 AND PAGE_ID = 108 ORDER BY ID");
	}

	foreach($sql_terms_rlst as $row)
	{
		$termsDataArr[$row['TERMS']] = $row['TERMS'];
	}
	unset($sql_terms_rlst);

	//for yarn information
	$sql_yarn = "SELECT a.id AS ID, a.requ_no AS REQU_NO, b.id AS DTLS_ID, b.job_no AS JOB_NO, b.color_id AS COLOR_ID, b.count_id AS COUNT_ID, b.composition_id AS COMPOSITION_ID, b.com_percent AS COM_PERCENT, b.yarn_type_id AS YARN_TYPE_ID, b.cons_uom AS CONS_UOM, b.quantity AS QUANTITY, b.rate AS RATE, b.amount AS AMOUNT, b.remarks AS REMARKS FROM inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0".where_con_using_array($jobNoArr, '1', 'b.job_no')." ORDER BY b.id ASC";
	//echo $sql_yarn; die;
	$sql_yarn_rslt = sql_select($sql_yarn);
	$yarnDataArr = array();
	$reqDtlsIdArr = array();
	$requisitionNoArr = array();
	$compositionIdArr = array();
	$countIdArr = array();
	foreach($sql_yarn_rslt as $row)
	{
		$requisitionNoArr[$row['REQU_NO']] = $row['REQU_NO'];
		$reqDtlsIdArr[$row['DTLS_ID']] = $row['DTLS_ID'];
		$compositionIdArr[$row['COMPOSITION_ID']] = $row['COMPOSITION_ID'];
		$countIdArr[$row['COUNT_ID']] = $row['COUNT_ID'];

		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['color_id'] = $row['COLOR_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['count_id'] = $row['COUNT_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['remarks'] = $row['REMARKS'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['com_percent'] = $row['COM_PERCENT'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['yarn_type_id'] = $row['YARN_TYPE_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['quantity'] = $row['QUANTITY'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['rate'] = $row['RATE'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['dtls_id'] = $row['DTLS_ID'];

		$total_grey_req_kg = 0;
		foreach($yarn_costing_arr[$row['JOB_NO']][$row['COUNT_ID']][$row['COMPOSITION_ID']] as $percent=>$percentArr)
		{
			foreach($percentArr as $key=>$val)
			{
				$total_grey_req_kg = $val['qty'];
			}
		}
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['total_grey_req_kg'] += $total_grey_req_kg;
	}
	unset($sql_yarn_rslt);
	/*"<pre>";
	print_r($yarnDataArr);
	echo "</pre>";*/
	
	//for production information
	$sql_product = "SELECT ID, YARN_COMP_TYPE1ST, YARN_COUNT_ID, AVAILABLE_QNTY, LOT FROM PRODUCT_DETAILS_MASTER WHERE STATUS_ACTIVE = 1 ".where_con_using_array($compositionIdArr, '0', 'YARN_COMP_TYPE1ST')." ".where_con_using_array($countIdArr, '0', 'YARN_COUNT_ID');
	//echo $sql_product;
	$sql_product_rslt = sql_select($sql_product);
	$productDataArr = array();
	foreach($sql_product_rslt as $row)
	{
		$productDataArr[$row['YARN_COMP_TYPE1ST']][$row['YARN_COUNT_ID']]['available_qty'] += $row['AVAILABLE_QNTY'];
		if($row['AVAILABLE_QNTY'] != 0)
		{
			$productDataArr[$row['YARN_COMP_TYPE1ST']][$row['YARN_COUNT_ID']]['lot'][$row['LOT']]= $row['LOT'];
		}
	}
	unset($sql_product_rslt);
	
	//for Yarn Purchase Order iformation
	/*
	$sql_yarn_work_order = "SELECT A.SUPPLIER_ID, B.REQUISITION_DTLS_ID, B.YARN_COUNT, B.RATE FROM WO_NON_ORDER_INFO_MST A, WO_NON_ORDER_INFO_DTLS B WHERE A.ID = B.MST_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 ".where_con_using_array($reqDtlsIdArr, '0', 'B.REQUISITION_DTLS_ID');
	$sql_yarn_work_order_rslt = sql_select($sql_yarn_work_order);
	$yarnWorkOrderDataArr = array();
	foreach($sql_yarn_work_order_rslt as $row)
	{
		$yarnWorkOrderDataArr[$row['REQUISITION_DTLS_ID']][$row['YARN_COUNT']]['supplier_id'][$row['SUPPLIER_ID']] = $supllier_arr[$row['SUPPLIER_ID']];
		$yarnWorkOrderDataArr[$row['REQUISITION_DTLS_ID']][$row['YARN_COUNT']]['rate'] = $row['RATE'];
	}
	unset($sql_yarn_work_order_rslt);
	*/
	//"<pre>";
	//print_r($yarnWorkOrderDataArr);
	//echo "</pre>";
	
	//for yarn terns and condition
	$sql_terms_rlst = sql_select("SELECT TERMS FROM WO_BOOKING_TERMS_CONDITION WHERE ENTRY_FORM = 70 ".where_con_using_array($requisitionNoArr, '1', 'BOOKING_NO')." ORDER BY ID");
	if(empty($sql_terms_rlst))
	{
		$sql_terms_rlst = sql_select("SELECT ID, TERMS FROM LIB_TERMS_CONDITION WHERE IS_DEFAULT = 1 AND PAGE_ID = 70 ORDER BY ID");
	}

	foreach($sql_terms_rlst as $row)
	{
		$termsDataArr[$row['TERMS']] = $row['TERMS'];
	}
	unset($sql_terms_rlst);

	//for job details
	$sql_data="select a.job_no,a.job_no_prefix_num,a.company_name,a.season_buyer_wise,a.season_matrix,a.buyer_name,a.style_ref_no,a.team_leader,a.dealing_marchant, a.gmts_item_id,a.job_quantity, b.id,b.is_confirmed,b.po_number,b.pub_shipment_date,b.shipment_date,b.po_quantity,b.unit_price,b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_name=".$cbo_company_id." ".$buyer_condition2." ".$season_condition2." ".$style_condition2." ".$job_condition2." AND b.id IN(".implode(',', $poIdArr).")";
	//echo $sql_data;
	$res_data=sql_select($sql_data);
	$job_wise_arr=array();
	foreach( $res_data as $row_data)
	{
		//$po_id_arr[$row_data[csf('id')]]=$row_data[csf('id')];
		//$po_wise_arr[$row_data[csf('id')]]['job']=$row_data[csf('job_no')];
		//$job_wise_arr[$row_data[csf('job_no')]]['buyer']=$row_data[csf('buyer_name')];
		$job_wise_arr[$row_data[csf('job_no')]]['style']=$row_data[csf('style_ref_no')];
		//$job_wise_arr[$row_data[csf('job_no')]]['season']=$row_data[csf('season_buyer_wise')];
		//$job_wise_arr[$row_data[csf('job_no')]]['matrix_season']=$row_data[csf('season_matrix')];
		//$job_wise_arr[$row_data[csf('job_no')]]['team_leader']=$team_leader_arr[$row_data[csf('team_leader')]];
		//$job_wise_arr[$row_data[csf('job_no')]]['dealing_marchant']=$deal_merchant_arr[$row_data[csf('dealing_marchant')]];
	}
	//echo "<pre>";
	//print_r($job_wise_arr);
	
	//for fabric pre cost information
	$fabric_pre_cost_data = array();
	$yarn_pre_cost_data = array();
	$sql_fabric_pre_cost = "SELECT ID, JOB_NO, FAB_NATURE_ID, COLOR_TYPE_ID, LIB_YARN_COUNT_DETER_ID, CONSTRUCTION, COMPOSITION, FABRIC_DESCRIPTION, COLOR, AVG_CONS, AVG_CONS_YARN, AVG_FINISH_CONS, FABRIC_SOURCE, RATE, AMOUNT, AVG_PROCESS_LOSS
	FROM WO_PRE_COST_FABRIC_COST_DTLS
	WHERE STATUS_ACTIVE = 1 ".where_con_using_array($jobNoArr, '1', 'JOB_NO');
	//echo $sql_fabric_pre_cost;
	$sql_fabric_pre_cost_rslt=sql_select($sql_fabric_pre_cost);
	foreach($sql_fabric_pre_cost_rslt as $row)
	{
		//for woven
		if($row['FAB_NATURE_ID'] == 3)
		{
			$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['woven_avg_cons'] = $row['AVG_CONS'];
			$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['process_loss_percent'] = $row['AVG_PROCESS_LOSS'];
			$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['amount'] += ($row['AVG_CONS']*$row['RATE']);
		}
		//for knit
		else if($row['FAB_NATURE_ID'] == 2)
		{
			$yarn_pre_cost_data[$row['JOB_NO']]['yarn_process_loss']['process_loss_percent'] = $row['AVG_PROCESS_LOSS'];
			$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['avg_cons'] = $row['AVG_CONS'];
		}
	}
	unset($sql_fabric_pre_cost_rslt);
	//echo "<pre>";
	//print_r($fabric_pre_cost_data);
	//echo "</pre>";
	
	//for yarn pre cost information
	$sql_yarn_pre_cost = "SELECT ID, FABRIC_COST_DTLS_ID, JOB_NO, COUNT_ID, COPM_ONE_ID, PERCENT_ONE, COPM_TWO_ID, PERCENT_TWO, COLOR, TYPE_ID, CONS_RATIO, CONS_QNTY, AVG_CONS_QNTY, SUPPLIER_ID, RATE, AMOUNT, STATUS_ACTIVE FROM WO_PRE_COST_FAB_YARN_COST_DTLS WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($jobNoArr, '1', 'JOB_NO');
	//echo $sql_yarn_pre_cost;
	$sql_yarn_pre_cost_rslt=sql_select($sql_yarn_pre_cost);
	foreach($sql_yarn_pre_cost_rslt as $row)
	{
		//$yarn_pre_cost_data[$row['JOB_NO']][$row['COUNT_ID']]['avg_cons'] += $row['CONS_QNTY'];
		$yarn_pre_cost_data[$row['JOB_NO']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['avg_cons'] += $row['CONS_QNTY'];
		$yarn_pre_cost_data[$row['JOB_NO']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['amount'] += $row['AMOUNT'];
		
		//for supplier
		$pre_cost_supplier[$row['JOB_NO']][$row['COPM_ONE_ID']][$row['COUNT_ID']][$row['TYPE_ID']]['supplier_id'][$row['SUPPLIER_ID']] = $supllier_arr[$row['SUPPLIER_ID']];
	}
	unset($sql_yarn_pre_cost_rslt);
	/*echo "<pre>";
	print_r($pre_cost_supplier);
	echo "</pre>";*/

	//for images
	$images = return_library_array("select master_tble_id, image_location from common_photo_library where file_type = 1".where_con_using_array($jobNoArr, '1', 'master_tble_id'), "master_tble_id", "image_location");
	//echo "<pre>";
	//print_r($images);
	
	//for company details
	$company_library = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

	//for row span
	$jobRowSpan = array();
	$yarnJobRowSpan = array();
	foreach($job_arr as $job)
	{
		//for woven info
		foreach($wovenDataArr[$job] as $fabric=>$fabricArr)
		{
			foreach($fabricArr as $colorType=>$row)
			{
				$jobRowSpan[$job]++;
			}
		}
		
		//for yarn info
		foreach($yarnDataArr[$job] as $yarnComposition=>$yarnCompArr)
		{
			foreach($yarnCompArr as $yarnCnt=>$yarnRow)
			{
				$yarnJobRowSpan[$job]++;
			}
		}
	}
	//echo "<pre>";
	//print_r($jobRowSpan);
	//echo "</pre>";
	?>
	<!--<fieldset>-->
	<!--<style type="text/css">
		table tr td {
			font-size: 13px;
		}
		.rpt_table thead th{
			font-size: 14px;
		}
		.rpt_table tfoot th{
			font-size: 14px;
		}
	</style>-->		
    <div style="width:2030">
        <table width="2030"  cellspacing="0">
			<tr class="form_caption" style="border:none;">
				<td align="center" style="border:none; font-size:18px;" colspan="22">
					<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
				</td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="25"> <? echo $report_title ;?></td>
			</tr>
		</table>
		<table width="2030" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<th width="40">SL</th>
				<th width="100">Job No</th>
				<th width="100">Picture</th>
				<th width="100">Style Ref.</th>
				<th width="150">Fabric/YARN Construction</th>
				<th width="120">Color</th>
				<th width="60">Yarn count</th>
				<th width="70">Woven Fabric Reff No</th>
				<th width="70">Order Quantity (Pcs)</th>
				<th width="70">Order Receive Date</th>
				<th width="70">Shipment Date</th>
				<th width="70">FOB Price</th>
				<th width="70">FOB Value</th>
				<th width="50">Excess cutting %</th>
				<th width="50">Qty with Cutting %</th>
				<th width="50">Process Loss %</th>
				<th width="70">Total Cons. Yarn</th>
				<th width="70">Total Cons. Woven Fabric</th>
				<th width="70">Total Grey Req. (Yds)</th>
				<th width="70">Total Grey Req. (Kgs)</th>
				<th width="70">Unallocated Yarn Stock</th>
				<th width="100">Stock Ref. (Lot)</th>
				<th width="70">Balance</th>
				<th width="70">Need to purchase</th>
				<th>Remarks</th>
			</thead>
		</table>
		<div style="max-height:350px; overflow-y:scroll; width:2030px" id="scroll_body" >
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2010" rules="all" id="table_body" >
				<tbody>
                <?php
				$sl = 0;
				$zs = 0;
				$totalGreyReqYds = 0;
				$totalGreyReqKgs = 0;
				$totalBalance = 0;
				$totalPurchase = 0;
				$summary_data = array();
				foreach($job_arr as $job)
				{
					$bgcolor="#FFFFFF";
					$style = $job_wise_arr[$job]['style'];
					//for woven info
					if(!empty($wovenDataArr[$job]))
					{
						$sl++;
						$rowSpanCond = 0;
						foreach($wovenDataArr[$job] as $fabric=>$fabricArr)
						{
							foreach($fabricArr as $colorType=>$row)
							{
								$zs++;
								/*if ($sl%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";*/
								
								$process_loss = $fabric_pre_cost_data[$row['fab_cost_dtls_id']][$job]['process_loss_percent'].'%';
								$consumption_woven_fabric = $fabric_pre_cost_data[$row['fab_cost_dtls_id']][$job]['woven_avg_cons'];
								
								//$total_grey_req_yds = array_sum($fabric_costing_arr['woven']['grey'][$row['po_id']][$row['fab_cost_dtls_id']]);
								$total_grey_req_yds = $row['total_grey_req_yds']*1;
								//$purchase_qty = $purchaseQty[$job][$fabric][$colorType][$row['fab_cost_dtls_id']]['purchase_qty'];
								//$fabric_purchase_qty = $purchaseQty[$row['fab_cost_dtls_id']]['purchase_qty'];
								$fabric_purchase_qty = $row['purchase_qty'];
								
								//$balance_qty = $total_grey_req_yds-$fabric_purchase_qty;
								$balance_qty = $total_grey_req_yds;
								
								//for total
								$totalGreyReqYds += $total_grey_req_yds;
								$totalBalance += $balance_qty;
								$totalPurchase += $fabric_purchase_qty;
	
								//for summary
								/*$summary_data['woven'][$zs]['fabric'] = $fabric;
								$summary_data['woven'][$zs]['count'] = '';
								$summary_data['woven'][$zs]['required_qty'] = $total_grey_req_yds;
								$summary_data['woven'][$zs]['stock'] = '';
								$summary_data['woven'][$zs]['purchase_qty'] = $fabric_purchase_qty;
								$summary_data['woven'][$zs]['mill'] = $row['supplier'];
								$summary_data['woven'][$zs]['rate'] = $row['rate'];
								$summary_data['woven'][$zs]['fabric_yarn_value'] = $fabric_purchase_qty*$row['rate'];*/
								
								//$fabric_costing_arr_summary
								//$total_grey_req_yds = array_sum($fabric_costing_arr['woven']['grey'][$row['PO_ID']][$row['FAB_COST_DTLS_ID']]);
								//$wovenDataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['supplier'][$row['SUPPLIER_ID']];

								$tot_grey_req_yds = 0;
								foreach($row['supplier'] as $key=>$supplier)
								{
									if($key*1 != 0)
									{
										//echo $row['po_id'].'='.$row['fab_cost_dtls_id'].'='.$key."<br>";
										$tot_grey_req_yds += array_sum($fabric_costing_arr_summary['woven']['grey'][$row['po_id']][$row['fab_cost_dtls_id']][$key]);
										$summary_data['woven'][$job][$fabric][$supplier]['fabric'] = $fabric;
										$summary_data['woven'][$job][$fabric][$supplier]['count'] = '';
										$summary_data['woven'][$job][$fabric][$supplier]['required_qty'] += $tot_grey_req_yds;
										$summary_data['woven'][$job][$fabric][$supplier]['stock'] = '';
										$summary_data['woven'][$job][$fabric][$supplier]['purchase_qty'] += $fabric_purchase_qty;
										$summary_data['woven'][$job][$fabric][$supplier]['mill'] = $row['supplier'];
										$summary_data['woven'][$job][$fabric][$supplier]['rate'] = $row['rate'];
										$summary_data['woven'][$job][$fabric][$supplier]['fabric_yarn_value'] += $fabric_purchase_qty*$row['rate'];
										
										$amount = $fabric_pre_cost_data[$row['fab_cost_dtls_id']][$job]['amount'];
										$price_dzn = $priceDznArr[$job];
										$summary_data['woven'][$job][$fabric][$supplier]['from_total_value'] += $amount/$price_dzn*100;
									}
								}
								
								$total_fabric_value += $summary_data['woven'][$zs]['fabric_yarn_value'];	
								$total_fabric_from_value += $summary_data['woven'][$zs]['from_total_value'];	
	
								$rowSpan = $jobRowSpan[$job];
								if($rowSpanCond == 0)
								{
									$rowSpanCond++;
									?>
									<tr valign="top" height="20" bgcolor="<? echo $bgcolor; ?>">
										<td width="40" align="center" rowspan="<?php echo $rowSpan; ?>"><?php echo $sl; ?></td>
										<td width="100" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $job; ?></td>
										<td width="100" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><img src="<?php echo '../../'.$images[$job]; ?>" height="97" width="89" /></td>
										<td width="100" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $style; ?></td>
										<td width="150" style="word-break: break-all;"><?php echo $fabric; ?></td>
										<td width="120" align="center" style="word-break: break-all;"><?php echo $color_type[$colorType]; ?></td>
										<td width="60" style="word-break: break-all;"></td>

<td width="70" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php //echo $colorType; ?></td>
<td width="70" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['order_qty_pcs']; ?></td>
<td width="70" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['order_rcv_date']; ?></td>
<td width="70" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['shipment_date']; ?></td>

<td width="70" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo '$'.number_format($row['fob_price'], 2); ?></td>
<td width="70" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo '$'.number_format($row['fob_value'], 2); ?></td>
<td width="50" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo number_format($row['excess_cutting'],2).'%'; ?></td>
<td width="50" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['qty_with_cutting'].' Pcs'; ?></td>
										
<td width="50" align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($consumption_woven_fabric, 2).' Yds'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_yds, 2).' Yds'; ?></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" style="word-break: break-all;"></td>
<td width="100" style="word-break: break-all;"></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($balance_qty, 2).' Yds'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($fabric_purchase_qty, 2).' Yds'; ?></td>
<td style="word-break: break-all;"><?php echo $row['remarks']; ?></td>
									</tr>
									<?php
								}
								else
								{
									?>
									<tr valign="top" height="20" bgcolor="<? echo $bgcolor; ?>">
										<td style="word-break: break-all;"><?php echo $fabric; ?></td>
										<td align="center" style="word-break: break-all;"><?php echo $color_type[$colorType]; ?></td>
										<td style="word-break: break-all;"></td>
										<td align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
										<td style="word-break: break-all;"></td>
										<td align="right" style="word-break: break-all;"><?php echo number_format($consumption_woven_fabric, 2).' Yds'; ?></td>
										<td align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_yds, 2).' Yds'; ?></td>
										<td style="word-break: break-all;"></td>
										<td style="word-break: break-all;"></td>
										<td style="word-break: break-all;"></td>
										<td align="right" style="word-break: break-all;"><?php echo number_format($balance_qty, 2).' Yds'; ?></td>
										<td  align="right" style="word-break: break-all;"><?php echo number_format($fabric_purchase_qty, 2).' Yds'; ?></td>
										<td style="word-break: break-all;"><?php echo $row['remarks']; ?></td>
									</tr>
									<?php
								}
							}
						}
					}
					
					//for yarn info
					if(!empty($yarnDataArr[$job]))
					{
						$sl++;
						$yarnRowSpanCond = 0;
						foreach($yarnDataArr[$job] as $yarnComposition=>$yarnComposArr)
						{
							foreach($yarnComposArr as $yarnCountId=>$yarnRow)
							{
								$yarnRow['order_qty_pcs'] = '';
								$yarnRow['order_rcv_date'] = '';
								$yarnRow['shipment_date'] = '';
								$yarnRow['fob_price'] = '';
								$yarnRow['fob_value'] = '';
								$yarnRow['excess_cutting'] = '';
								$yarnRow['qty_with_cutting'] = '';
								
								foreach($yarnDataArray[$job] as $compo=>$compo_arr)
								{
									foreach($compo_arr as $clr_type=>$clr_arr)
									{
										$yarnRow['order_qty_pcs'] += $clr_arr['order_qty_pcs'];
										$yarnRow['order_rcv_date'] = $clr_arr['order_rcv_date'];
										$yarnRow['shipment_date'] = $clr_arr['shipment_date'];
										$yarnRow['fob_price'] = $clr_arr['fob_price'];
										$yarnRow['fob_value'] = $clr_arr['fob_value'];
										$yarnRow['excess_cutting'] = $clr_arr['excess_cutting'];
										$yarnRow['qty_with_cutting'] = $clr_arr['qty_with_cutting'];
									}
								}
								
								$process_loss = $yarn_pre_cost_data[$job]['yarn_process_loss']['process_loss_percent'].'%';
								$consumption_yarn = $yarn_pre_cost_data[$job][$yarnComposition][$yarnRow['count_id']]['avg_cons'];
								$fabric = $composition[$yarnComposition].' '.$yarnRow['com_percent'].' % '.$yarn_type[$yarnRow['yarn_type_id']];
								$total_grey_req_kg = $yarnRow['total_grey_req_kg'];
								$unallocated_qty = $productDataArr[$yarnComposition][$yarnRow['count_id']]['available_qty'];
								$product_id = implode(', ', $productDataArr[$yarnComposition][$yarnRow['count_id']]['lot']);
								$yarn_purchase_qty = $yarnRow['quantity']*1;
								$yarn_balance_qty = number_format($total_grey_req_kg,2,'.','')-number_format($unallocated_qty,2,'.','');
								
								//for total
								$totalGreyReqKgs += $total_grey_req_kg;
								$totalBalance += $yarn_balance_qty;
								$totalPurchase += $yarn_purchase_qty;
								
								//for summary
								$tot_grey_req_kg = 0;
								foreach($pre_cost_supplier[$job][$yarnComposition][$yarnCountId][$yarnRow['yarn_type_id']]['supplier_id'] as $sup=>$supplier)
								{
									if($sup*1 != 0)
									{
										$tot_grey_req_kg = 0;
										foreach($yarn_costing_arr_summary[$job][$yarnCountId][$yarnComposition] as $percent=>$percentArr)
										{
											foreach($percentArr as $typ=>$typArr)
											{
												foreach($typArr as $key=>$val)
												{
													//$tot_grey_req_kg = $val['qty'];
													$tot_grey_req_kg += $val['qty'];
												}
											}
										}
										
										$cnt = $yarn_count_details[$yarnRow['count_id']];
										$summary_data['yarn'][$job][$fabric][$cnt][$sup]['fabric'] = $fabric;
										$summary_data['yarn'][$job][$fabric][$cnt][$sup]['count'] = $cnt;
										$summary_data['yarn'][$job][$fabric][$cnt][$sup]['required_qty'] += $tot_grey_req_kg;
										$summary_data['yarn'][$job][$fabric][$cnt][$sup]['stock'] = $product_id;
										$summary_data['yarn'][$job][$fabric][$cnt][$sup]['purchase_qty'] += $yarn_purchase_qty;
										$summary_data['yarn'][$job][$fabric][$cnt][$sup]['mill'] = $supplier;
										$summary_data['yarn'][$job][$fabric][$cnt][$sup]['rate'] = $yarnRow['rate'];
										$summary_data['yarn'][$job][$fabric][$cnt][$sup]['fabric_yarn_value'] = $yarn_purchase_qty*$yarnRow['rate'];
										$amount = $yarn_pre_cost_data[$job][$fabric][$yarnComposition][$yarnRow['count_id']]['amount'];
										$price_dzn = $priceDznArr[$job];
										$summary_data['yarn'][$job][$fabric][$cnt][$sup]['from_total_value'] = $amount/$price_dzn*100;
		
										$total_yarn_value += $summary_data['yarn'][$zs]['fabric_yarn_value'];	
										$total_yarn_from_value += $summary_data['yarn'][$zs]['from_total_value'];
									}
								}
								
								$yarnRowSpan = $yarnJobRowSpan[$job];
								if($yarnRowSpanCond == 0)
								{
									$yarnRowSpanCond++;
									?>
									<tr valign="top" height="20" bgcolor="<? echo $bgcolor; ?>">
										<td width="40" rowspan="<? echo $yarnRowSpan; ?>" align="center"><?php echo $sl; ?></td>
										<td width="100" rowspan="<? echo $yarnRowSpan; ?>" align="center" style="word-break: break-all;"><?php echo $job; ?></td>
										<td width="100" rowspan="<? echo $yarnRowSpan; ?>" style="word-break: break-all;">
											<img src="<?php echo '../../'.$images[$job]; ?>" height="97" width="89" />
										</td>
										<td width="100" rowspan="<? echo $yarnRowSpan; ?>" align="center" style="word-break: break-all;"><?php echo $style; ?></td>
										<td width="150" style="word-break: break-all;"><?php echo $fabric; ?></td>
										<td width="120" align="center" style="word-break: break-all;"><?php echo $color_library[$yarnRow['color_id']]; ?></td>
										<td width="60" style="word-break: break-all;"><?php echo $yarn_count_details[$yarnRow['count_id']]; ?></td>
                                        
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" style="word-break: break-all;"><?php //Woven Fabric Reff No; ?></td>
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" align="right" style="word-break: break-all;"><?php echo $yarnRow['order_qty_pcs']; ?></td>
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" align="center" style="word-break: break-all;"><?php echo $yarnRow['order_rcv_date']; ?></td>
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" align="center" style="word-break: break-all;"><?php echo $yarnRow['shipment_date']; ?></td>
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" align="right" style="word-break: break-all;"><?php echo '$'.number_format($yarnRow['fob_price'], 2); ?></td>
<td width="70" rowspan="<? echo $yarnRowSpan; ?>" align="right" style="word-break: break-all;"><?php echo '$'.number_format($yarnRow['fob_value'], 2); ?></td>
<td width="50" rowspan="<? echo $yarnRowSpan; ?>" align="center" style="word-break: break-all;"><?php echo number_format($yarnRow['excess_cutting'],2).'%'; ?></td>
<td width="50" rowspan="<? echo $yarnRowSpan; ?>" align="right" style="word-break: break-all;"><?php echo $yarnRow['qty_with_cutting'].' Pcs'; ?></td>

<td width="50" align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($consumption_yarn, 2).' Kgs'; ?></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_kg, 2).' Kgs'; ?></td>
<td width="70" style="word-break: break-all;"><?php echo number_format($unallocated_qty, 2); ?></td>
<td width="100" style="word-break: break-all;"><?php echo $product_id; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($yarn_balance_qty, 2).' Kgs'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($yarn_purchase_qty, 2).' Kgs'; ?></td>
<td style="word-break: break-all;"><?php echo $yarnRow['remarks']; ?></td>
									</tr>
									<?php
								}
								else
								{
								?>
									<tr valign="top" height="20" bgcolor="<? echo $bgcolor; ?>">
<td width="150" style="word-break: break-all;"><?php echo $fabric; ?></td>
<td width="120" align="center" style="word-break: break-all;"><?php echo $color_library[$yarnRow['color_id']]; ?></td>
<td width="60" style="word-break: break-all;"><?php echo $yarn_count_details[$yarnRow['count_id']]; ?></td>

<td width="50" align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($consumption_yarn, 2).' Kgs'; ?></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" style="word-break: break-all;"></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_kg, 2).' Kgs'; ?></td>
<td width="70" style="word-break: break-all;"><?php echo number_format($unallocated_qty, 2); ?></td>
<td width="100" style="word-break: break-all;"><?php echo $product_id; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($yarn_balance_qty, 2).' Kgs'; ?></td>
<td width="70" align="right" style="word-break: break-all;"><?php echo number_format($yarn_purchase_qty, 2).' Kgs'; ?></td>
<td style="word-break: break-all;"><?php echo $yarnRow['remarks']; ?></td>
									</tr>
								<?php
								}
							}
						}
					}
				}
				?>
            	</tbody>
                <tfoot>
                	<tr>
                    	<th align="right" colspan="18">Total&nbsp;</th>
                        <th align="right"><?php echo number_format($totalGreyReqYds, 2).' Yds'; ?></th>
                        <th align="right"><?php echo number_format($totalGreyReqKgs, 2).' Kgs'; ?></th>
                        <th></th>
                        <th></th>
                        <th align="right"><?php //echo number_format($totalBalance, 2); ?></th>
                        <th align="right"><?php //echo number_format($totalPurchase, 2); ?></th>
                        <th></td>
                    </tr>
                </tfoot>
            </table>
		</div>
        <table width="940" cellspacing="0" border="1" class="rpt_table" rules="all" style="margin-top:10px;">
            <thead>
                <th width="40">SL</th>
                <th width="130">Fabric/YARN</th>
                <th width="70">Yarn count</th>
                <th width="100">Required Qty</th>
                <th width="100">Stock</th>
                <th width="100">Purchase Qty</th>
                <th width="120">Mill</th>
                <th width="80">Rate</th>
                <th width="100">Woven Fabric/Yarn Value</th>
                <th width="100">% From total Value</th>
            </thead>
            <tbody>
            <?php
			//echo "<pre>";
			//print_r($summary_data['woven']);
			//echo "</pre>";

            $sl = 0;
            foreach($summary_data['woven'] as $jb=>$jb_arr)
			{
				foreach($jb_arr as $fb=>$fb_arr)
				{
					foreach($fb_arr as $mill=>$val)
					{
						$sl++;
						?>
						<tr height="20">
							<td><?php echo $sl; ?></td>
							<td style="word-break: break-all;"><?php echo $val['fabric']; ?></td>
							<td><?php echo $val['count']; ?></td>
							<td align="right"><?php echo number_format($val['required_qty'], 2).' Yds'; ?></td>
							<td style="word-break: break-all;"><?php echo $val['stock']; ?></td>
							<td align="right"><?php echo number_format($val['purchase_qty'], 2).' Yds'; ?></td>
							<td style="word-break: break-all;"><?php echo $mill; ?></td>
							<td align="center"><?php echo '$'.number_format($val['rate'], 4); ?></td>
							<td align="right"><?php echo '$'.number_format($val['fabric_yarn_value'], 2); ?></td>
							<td align="right"><?php echo number_format($val['from_total_value'], 2); ?>%</td>
						</tr>
						<?php
					}
				}
			}
            foreach($summary_data['yarn'] as $jb=>$jb_arr)
			{
				foreach($jb_arr as $fb=>$fb_arr)
				{
					foreach($fb_arr as $cnt=>$cnt_arr)
					{
						foreach($cnt_arr as $key=>$val)
						{
							$sl++;
							?>
							<tr height="20">
								<td><?php echo $sl; ?></td>
								<td style="word-break: break-all;"><?php echo $val['fabric']; ?></td>
								<td><?php echo $val['count']; ?></td>
								<td align="right"><?php echo number_format($val['required_qty'], 2).' Kgs'; ?></td>
								<td style="word-break: break-all;"><?php echo $val['stock']; ?></td>
								<td align="right"><?php echo number_format($val['purchase_qty'], 2).' Kgs'; ?></td>
								<td style="word-break: break-all;"><?php echo $val['mill']; ?></td>
								<td align="center"><?php echo '$'.number_format($val['rate'], 4); ?></td>
								<td align="right"><?php echo '$'.number_format($val['fabric_yarn_value'], 2); ?></td>
								<td align="right"><?php echo number_format($val['from_total_value'], 2); ?>%</td>
							</tr>
							<?php
						}
					}
				}
			}
            ?>
            </tbody>
            <tfoot style="font-weight:bold;">
                <?php
				if($total_fabric_value>0)
				{
				?>
                <tr height="20">
                    <td colspan="8" align="center">TOTAL FABRIC COST</td>
                    <td align="right"><?php echo '$'.number_format($total_fabric_value, 2); ?></td>
                    <td align="right"><?php echo number_format($total_fabric_from_value, 2); ?>%</td>
                </tr>
                <?php
				}
				
				if($total_yarn_value>0)
				{
				?>
                <tr height="20">
                    <td colspan="8" align="center">TOTAL YARN COST</td>
                    <td align="right"><?php echo '$'.number_format($total_yarn_value, 2); ?></td>
                    <td align="right"><?php echo number_format($total_yarn_from_value, 2); ?>%</td>
                </tr>
                <?php
				}
				?>
            </tfoot>
        </table>
        <table width="940" cellspacing="0" border="0" style="margin-top:30px;">
            <tbody>
                <tr height="20">
                    <td colspan="2"><strong><u>Remarks :</u></strong></td>
                </tr>
                <?php
                $sl = 0;
                foreach($termsDataArr as $key=>$val)
                {
                    $sl++;
                    ?>
                    <tr height="20">
                        <td width="20" valign="top"><?php echo $sl.'.'; ?></td>
                        <td valign="top"><p><?php echo $val; ?></p></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <br/> <br/> <br/><br/> <br/> <br/>
        <div style="width:100%;">
            <table cellspacing="0" width="100%" border="0" >
                <tr align="center">
                    <td style="text-decoration:overline; font-weight:bold;">MERCHANDISER</td>
                    <td style="text-decoration:overline; font-weight:bold;">MERCHANDISING AGM</td>
                    <td style="text-decoration:overline; font-weight:bold;">MERCHANDISING DIRECTOR</td>
                </tr>
            </table>
        </div>
        </div>
	<!--</fieldset>-->
	<?php
	//yarnWorkOrderDataArr
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
	echo "$total_data####$filename";
	exit();
}

if ($action=="action_generate_report_13062021")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	ob_start();
	
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_buyer_id = str_replace("'","",$cbo_buyer_id);
	$cbo_department_id = str_replace("'","",$cbo_department_id);
	$txt_season = str_replace("'","",$txt_season);
	$hdn_season = str_replace("'","",$hdn_season);
	$txt_style = str_replace("'","",$txt_style);
	$hdn_style = str_replace("'","",$hdn_style);
	$txt_job = str_replace("'","",$txt_job);
	$hdn_job = str_replace("'","",$hdn_job);
	$txt_order = str_replace("'","",$txt_order);
	$hdn_order = str_replace("'","",$hdn_order);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	
	//for buyer condition
	$buyer_condition = '';
	$buyer_condition2 = '';
	if($cbo_buyer_id != 0)
	{
		$buyer_condition = " AND a.buyer_id = ".$cbo_buyer_id."";
		$buyer_condition2 = " AND a.buyer_name = ".$cbo_buyer_id."";
	}
	
	//for season condition
	$season_condition = '';
	$season_condition2 = '';
	if($txt_season != '')
	{
		$season_condition = " AND b.job_no IN(SELECT job_no FROM wo_po_details_master WHERE company_id = ".$cbo_company_id." AND buyer_name = ".$cbo_buyer_id." AND season_buyer_wise IN(".$hdn_season."))";
		$season_condition2 = " AND season_buyer_wise IN(".$hdn_season.")";
	}
	
	//for style condition
	$style_condition = '';
	$style_condition2 = '';
	if($txt_style != '')
	{
		$style_condition = " AND b.job_no IN(SELECT job_no FROM wo_po_details_master WHERE id IN(".$hdn_style."))";
		$style_condition2 = " AND a.id IN(".$hdn_style.")";
	}
	
	//for job condition
	$job_condition = '';
	$job_condition2 = '';
	if($txt_job != '')
	{
		$job_condition = " AND b.job_no IN(SELECT job_no FROM wo_po_details_master WHERE id IN(".$hdn_job."))";
		$job_condition2 = " AND a.id IN(".$hdn_job.")";
	}
	
	//for order condition
	$order_condition = '';
	if($txt_order != '')
	{
		$order_condition = " AND b.po_break_down_id IN('".str_replace(',', "','", $hdn_order)."')";
	}

	//for date condition
	$date_condition = '';
	if( $txt_date_from != '' && $txt_date_to != '')
	{
		$date_condition=" AND a.booking_date BETWEEN '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' AND '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'";
	}
	
	//for woven information
	$sql_booking="SELECT a.id AS BOOKING_ID, a.booking_no AS BOOKING_NO, a.item_category AS ITEM_CATEGORY, a.pay_mode AS PAYMODE, a.remarks AS REMARKS, a.fabric_source AS FABRIC_SOURCE, a.is_approved AS IS_APPROVED, a.booking_no_prefix_num AS BOOKING_NO_PREFIX_NUM, a.booking_date AS BOOKING_DATE, a.supplier_id AS SUPPLIER_ID, a.buyer_id AS BUYER_ID, 
	b.pre_cost_fabric_cost_dtls_id AS FAB_COST_DTLS_ID, b.fabric_color_id AS FABRIC_COLOR_ID, b.color_type AS COLOR_TYPE, b.uom AS UOM, b.gmts_color_id AS GMTS_COLOR_ID, b.construction AS CONSTRUCTION, b.copmposition AS COMPOSITION, b.dia_width AS DIA_WIDTH, b.gsm_weight AS GMS_WEIGHT, b.pre_cost_remarks AS PRE_REMARKS, b.remark AS DTLS_REMARKS, b.fin_fab_qnty AS FIN_FAB_QNTY, b.grey_fab_qnty AS GREY_FAB_QNTY, b.adjust_qty AS ADJUST_QTY, b.wo_qnty AS WO_QNTY, b.amount AS AMOUNT, b.rate AS RATE, b.job_no AS JOB_NO, b.po_break_down_id AS PO_ID,
	c.pub_shipment_date AS PUB_SHIPMENT_DATE, c.excess_cut AS EXCESS_CUT, c.po_received_date AS PO_RECEIVED_DATE, c.po_quantity AS PO_QUANTITY, c.unit_price AS UNIT_PRICE, c.plan_cut AS PLAN_CUT, c.po_total_price AS PO_TOTAL_PRICE, c.shipment_date AS SHIPMENT_DATE 
   	FROM wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c
	WHERE 
	a.booking_no = b.booking_no 
	AND b.job_no = c.job_no_mst
	AND b.po_break_down_id = c.id
	AND a.company_id = ".$cbo_company_id." 
	AND a.booking_type IN(1)
	AND a.item_category IN(2,3)
	AND a.status_active = 1
	AND a.is_deleted = 0
	AND b.status_active = 1
	AND b.is_deleted = 0
	".$buyer_condition."
	".$season_condition."
	".$style_condition."
	".$job_condition."
	".$order_condition."
	".$date_condition."
	ORDER BY a.booking_no, b.job_no ";
	/*
	--AND a.job_no = b.job_no
	--AND a.po_break_down_id = b.po_break_down_id
	--AND a.po_break_down_id = c.id
	--AND a.is_short IN(2)
	*/
	//echo $sql_booking;
	$booking_data=sql_select($sql_booking);
	$dataArr = array();
	$jobNoArr = array();
	$poIdArr = array();
	$bookingArr = array();
	$preCostFabCostDtlsIdArr = array();
	$purchaseQty = array();
	foreach($booking_data as $row)
	{
		$jobNoArr[$row['JOB_NO']] = $row['JOB_NO'];
		$poIdArr[$row['PO_ID']] = $row['PO_ID'];
		$preCostFabCostDtlsIdArr[$row['FAB_COST_DTLS_ID']] = $row['FAB_COST_DTLS_ID'];
		$bookingArr[$row['BOOKING_NO']] = $row['BOOKING_NO'];
	}
	
	//for class condition
	$condition= new condition();
	if(!empty($poIdArr))
	{
		$condition->po_id_in(implode(",",$poIdArr));
	}

	//for fabric
	$condition->init();
	$fabric= new fabric($condition);
	//echo $fabric->getQuery(); die;
	$fabric_costing_arr = $fabric->getQtyArray_by_orderAndFabriccostid_knitAndwoven_greyAndfinish();
	//echo "<pre>";
	//print_r($fabric_costing_arr);

	//for yarn
	$yarnCondition= new condition();
	if(!empty($jobNoArr))
	{
		$yarnCondition->job_no("IN('".implode("','",$jobNoArr)."')");
	}
	
	$yarnCondition->init();
	$yarn= new yarn($yarnCondition);
	//echo $yarn->getQuery();
	//$yarn_costing_arr = $yarn->getOrderCountAndCompositionWiseYarnQtyArray();
	$yarn_costing_arr = $yarn->getJobCountCompositionColorAndTypeWiseYarnQtyAndAmountArray();
	//echo "<pre>";
	//print_r($yarn_costing_arr);
	
	foreach($booking_data as $row)
	{
		//$purchaseQty[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']][$row['FAB_COST_DTLS_ID']]['purchase_qty'] += $row['GREY_FAB_QNTY'];
		$purchaseQty[$row['FAB_COST_DTLS_ID']]['purchase_qty'] += $row['GREY_FAB_QNTY'];
		
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['order_qty_pcs'] = $row['PO_QUANTITY'];
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['order_rcv_date'] = date('d-m-Y', strtotime($row['PO_RECEIVED_DATE']));
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['shipment_date'] = date('d-m-Y', strtotime($row['SHIPMENT_DATE']));
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fob_price'] = $row['UNIT_PRICE'];
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fob_value'] = $row['PO_TOTAL_PRICE'];
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['excess_cutting'] = $row['EXCESS_CUT'];
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['qty_with_cutting'] = $row['PLAN_CUT'];
		//$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['total_consumption_yarn'] = $row[''];
		//$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['total_consumption_woven_fabric'] = $row[''];
		//$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['total_grey_req_fab'] = $row[''];
		//$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['total_grey_req_yarn'] = $row[''];
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['remarks'] = $row['DTLS_REMARKS'];
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['fab_cost_dtls_id'] = $row['FAB_COST_DTLS_ID'];
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['po_id'] = $row['PO_ID'];
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['uom'] = $row['UOM'];
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['rate'] = $row['RATE'];
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['supplier'] = $supllier_arr[$row['SUPPLIER_ID']];
		
		$total_grey_req_yds = array_sum($fabric_costing_arr['woven']['grey'][$row['PO_ID']][$row['FAB_COST_DTLS_ID']]);	
		$dataArr[$row['JOB_NO']][$row['COMPOSITION']][$row['COLOR_TYPE']]['total_grey_req_yds'] += $total_grey_req_yds;
	}
	unset($booking_data);
	
	//for price dzn
	$sql_dzn = sql_select("SELECT JOB_NO, PRICE_DZN FROM wo_pre_cost_dtls WHERE status_active=1 AND is_deleted=0".where_con_using_array($jobNoArr, '1', 'JOB_NO'));
	$priceDznArr = array();
	foreach($sql_dzn as $row)
	{
		$priceDznArr[$row['JOB_NO']] = $row['PRICE_DZN'];
	}
	
	//for terns and condition
	$termsDataArr = array();
	$sql_terms_rlst = sql_select("SELECT TERMS FROM WO_BOOKING_TERMS_CONDITION WHERE ENTRY_FORM = 108 ".where_con_using_array($bookingArr, '1', 'BOOKING_NO')." ORDER BY ID");
	if(empty($sql_terms_rlst))
	{
		$sql_terms_rlst = sql_select("SELECT ID, TERMS FROM LIB_TERMS_CONDITION WHERE IS_DEFAULT = 1 AND PAGE_ID = 108 ORDER BY ID");
	}

	foreach($sql_terms_rlst as $row)
	{
		$termsDataArr[$row['TERMS']] = $row['TERMS'];
	}
	unset($sql_terms_rlst);


	//for yarn information
	$sql_yarn = "SELECT a.id AS ID, a.requ_no AS REQU_NO, b.id AS DTLS_ID, b.job_no AS JOB_NO, b.color_id AS COLOR_ID, b.count_id AS COUNT_ID, b.composition_id AS COMPOSITION_ID, b.com_percent AS COM_PERCENT, b.yarn_type_id AS YARN_TYPE_ID, b.cons_uom AS CONS_UOM, b.quantity AS QUANTITY, b.rate AS RATE, b.amount AS AMOUNT, b.remarks AS REMARKS FROM inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0".where_con_using_array($jobNoArr, '1', 'b.job_no')." ORDER BY b.id ASC";
	//echo $sql;die;
	$sql_yarn_rslt = sql_select($sql_yarn);
	$yarnDataArr = array();
	$reqDtlsIdArr = array();
	$requisitionNoArr = array();
	$compositionIdArr = array();
	$countIdArr = array();
	foreach($sql_yarn_rslt as $row)
	{
		$requisitionNoArr[$row['REQU_NO']] = $row['REQU_NO'];
		$reqDtlsIdArr[$row['DTLS_ID']] = $row['DTLS_ID'];
		$compositionIdArr[$row['COMPOSITION_ID']] = $row['COMPOSITION_ID'];
		$countIdArr[$row['count_id']] = $row['COUNT_ID'];
		
		/*$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']]['color_id'] = $row['COLOR_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']]['count_id'] = $row['COUNT_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']]['remarks'] = $row['REMARKS'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']]['com_percent'] = $row['COM_PERCENT'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']]['yarn_type_id'] = $row['YARN_TYPE_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']]['quantity'] = $row['QUANTITY'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']]['rate'] = $row['RATE'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']]['dtls_id'] = $row['DTLS_ID'];*/
		
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['color_id'] = $row['COLOR_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['count_id'] = $row['COUNT_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['remarks'] = $row['REMARKS'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['com_percent'] = $row['COM_PERCENT'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['yarn_type_id'] = $row['YARN_TYPE_ID'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['quantity'] = $row['QUANTITY'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['rate'] = $row['RATE'];
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['dtls_id'] = $row['DTLS_ID'];

		$total_grey_req_kg = 0;
		foreach($yarn_costing_arr[$row['JOB_NO']][$row['COUNT_ID']][$row['COMPOSITION_ID']] as $percent=>$percentArr)
		{
			foreach($percentArr as $key=>$val)
			{
				$total_grey_req_kg = $val['qty'];
			}
		}
		$yarnDataArr[$row['JOB_NO']][$row['COMPOSITION_ID']][$row['COUNT_ID']]['total_grey_req_kg'] += $total_grey_req_kg;
			
		
	}
	unset($sql_yarn_rslt);
	//echo "<pre>";
	//print_r($yarnDataArr);
	
	//for production information
	$sql_product = "SELECT ID, YARN_COMP_TYPE1ST, YARN_COUNT_ID, AVAILABLE_QNTY, LOT FROM PRODUCT_DETAILS_MASTER WHERE STATUS_ACTIVE = 1 ".where_con_using_array($compositionIdArr, '0', 'YARN_COMP_TYPE1ST')." ".where_con_using_array($countIdArr, '0', 'YARN_COUNT_ID');
	//echo $sql_product;
	$sql_product_rslt = sql_select($sql_product);
	$productDataArr = array();
	foreach($sql_product_rslt as $row)
	{
		$productDataArr[$row['YARN_COMP_TYPE1ST']][$row['YARN_COUNT_ID']]['available_qty'] += $row['AVAILABLE_QNTY'];
		if($row['AVAILABLE_QNTY'] != 0)
		{
			$productDataArr[$row['YARN_COMP_TYPE1ST']][$row['YARN_COUNT_ID']]['lot'][$row['LOT']]= $row['LOT'];
		}
		//$productDataArr[$row['YARN_COMP_TYPE1ST']][$row['YARN_COUNT_ID']]['product_id'][$row['ID']]= $row['ID'];
	}
	unset($sql_product_rslt);
	
	//for Yarn Purchase Order iformation
	$sql_yarn_work_order = "SELECT A.SUPPLIER_ID, B.REQUISITION_DTLS_ID, B.YARN_COUNT, B.RATE FROM WO_NON_ORDER_INFO_MST A, WO_NON_ORDER_INFO_DTLS B WHERE A.ID = B.MST_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 ".where_con_using_array($reqDtlsIdArr, '0', 'B.REQUISITION_DTLS_ID');
	$sql_yarn_work_order_rslt = sql_select($sql_yarn_work_order);
	$yarnWorkOrderDataArr = array();
	foreach($sql_yarn_work_order_rslt as $row)
	{
		$yarnWorkOrderDataArr[$row['REQUISITION_DTLS_ID']][$row['YARN_COUNT']]['supplier_id'] = $supllier_arr[$row['SUPPLIER_ID']];
		$yarnWorkOrderDataArr[$row['REQUISITION_DTLS_ID']][$row['YARN_COUNT']]['rate'] = $row['RATE'];
	}
	unset($sql_yarn_work_order_rslt);
	
	//for yarn terns and condition
	$sql_terms_rlst = sql_select("SELECT TERMS FROM WO_BOOKING_TERMS_CONDITION WHERE ENTRY_FORM = 70 ".where_con_using_array($requisitionNoArr, '1', 'BOOKING_NO')." ORDER BY ID");
	if(empty($sql_terms_rlst))
	{
		$sql_terms_rlst = sql_select("SELECT ID, TERMS FROM LIB_TERMS_CONDITION WHERE IS_DEFAULT = 1 AND PAGE_ID = 70 ORDER BY ID");
	}

	foreach($sql_terms_rlst as $row)
	{
		$termsDataArr[$row['TERMS']] = $row['TERMS'];
	}
	unset($sql_terms_rlst);

	//for job details
	$sql_data="select a.job_no,a.job_no_prefix_num,a.company_name,a.season_buyer_wise,a.season_matrix,a.buyer_name,a.style_ref_no,a.team_leader,a.dealing_marchant, a.gmts_item_id,a.job_quantity, b.id,b.is_confirmed,b.po_number,b.pub_shipment_date,b.shipment_date,b.po_quantity,b.unit_price,b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_name=".$cbo_company_id." ".$buyer_condition2." ".$season_condition2." ".$style_condition2." ".$job_condition2." AND b.id IN(".implode(',', $poIdArr).")";
	//echo $sql_data;
	$res_data=sql_select($sql_data);
	$job_wise_arr=array();
	foreach( $res_data as $row_data)
	{
		$po_id_arr[$row_data[csf('id')]]=$row_data[csf('id')];
		$po_wise_arr[$row_data[csf('id')]]['job']=$row_data[csf('job_no')];
		$job_wise_arr[$row_data[csf('job_no')]]['buyer']=$row_data[csf('buyer_name')];
		$job_wise_arr[$row_data[csf('job_no')]]['style']=$row_data[csf('style_ref_no')];
		$job_wise_arr[$row_data[csf('job_no')]]['season']=$row_data[csf('season_buyer_wise')];
		$job_wise_arr[$row_data[csf('job_no')]]['matrix_season']=$row_data[csf('season_matrix')];
		$job_wise_arr[$row_data[csf('job_no')]]['team_leader']=$team_leader_arr[$row_data[csf('team_leader')]];
		$job_wise_arr[$row_data[csf('job_no')]]['dealing_marchant']=$deal_merchant_arr[$row_data[csf('dealing_marchant')]];
	}
	//echo "<pre>";
	//print_r($job_wise_arr);
	
	//for fabric pre cost information
	$fabric_pre_cost_data = array();
	$yarn_pre_cost_data = array();
	//$sql_fabric_pre_cost = "SELECT ID, JOB_NO, FAB_NATURE_ID, COLOR_TYPE_ID, LIB_YARN_COUNT_DETER_ID, CONSTRUCTION, COMPOSITION, FABRIC_DESCRIPTION, COLOR, AVG_CONS, FABRIC_SOURCE, RATE, AMOUNT, AVG_FINISH_CONS, AVG_PROCESS_LOSS FROM WO_PRE_COST_FABRIC_COST_DTLS WHERE STATUS_ACTIVE = 1 ".where_con_using_array($preCostFabCostDtlsIdArr, '0', 'ID');
	
	$sql_fabric_pre_cost = "SELECT ID, JOB_NO, FAB_NATURE_ID, COLOR_TYPE_ID, LIB_YARN_COUNT_DETER_ID, CONSTRUCTION, COMPOSITION, FABRIC_DESCRIPTION, COLOR, AVG_CONS, AVG_CONS_YARN, AVG_FINISH_CONS, FABRIC_SOURCE, RATE, AMOUNT, AVG_PROCESS_LOSS
	FROM WO_PRE_COST_FABRIC_COST_DTLS
	WHERE STATUS_ACTIVE = 1 ".where_con_using_array($jobNoArr, '1', 'JOB_NO');
	//echo $sql_fabric_pre_cost;
	$sql_fabric_pre_cost_rslt=sql_select($sql_fabric_pre_cost);
	foreach($sql_fabric_pre_cost_rslt as $row)
	{
		$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['avg_cons'] = $row['AVG_CONS'];
		
		//for woven
		if($row['FAB_NATURE_ID'] == 3)
		{
			$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['process_loss_percent'] = $row['AVG_PROCESS_LOSS'];
			$fabric_pre_cost_data[$row['ID']][$row['JOB_NO']]['amount'] += ($row['AVG_CONS_YARN']*$row['RATE']);
		}
		//for knit
		else if($row['FAB_NATURE_ID'] == 2)
		{
			$yarn_pre_cost_data[$row['JOB_NO']]['yarn_process_loss']['process_loss_percent'] = $row['AVG_PROCESS_LOSS'];
		}
	}
	unset($sql_fabric_pre_cost_rslt);
	//echo "<pre>";
	//print_r($fabric_pre_cost_data);
	
	//for yarn pre cost information
    //$sql_yarn_pre_cost = "SELECT ID, FABRIC_COST_DTLS_ID, JOB_NO, COUNT_ID, COPM_ONE_ID, PERCENT_ONE, COPM_TWO_ID, PERCENT_TWO, COLOR, TYPE_ID, CONS_RATIO, CONS_QNTY, AVG_CONS_QNTY, SUPPLIER_ID, RATE, AMOUNT, STATUS_ACTIVE FROM WO_PRE_COST_FAB_YARN_COST_DTLS WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($jobNoArr, '1', 'JOB_NO').where_con_using_array($preCostFabCostDtlsIdArr, '0', 'FABRIC_COST_DTLS_ID');

	$sql_yarn_pre_cost = "SELECT ID, FABRIC_COST_DTLS_ID, JOB_NO, COUNT_ID, COPM_ONE_ID, PERCENT_ONE, COPM_TWO_ID, PERCENT_TWO, COLOR, TYPE_ID, CONS_RATIO, CONS_QNTY, AVG_CONS_QNTY, SUPPLIER_ID, RATE, AMOUNT, STATUS_ACTIVE FROM WO_PRE_COST_FAB_YARN_COST_DTLS WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ".where_con_using_array($jobNoArr, '1', 'JOB_NO');
	//echo $sql_yarn_pre_cost;
	$sql_yarn_pre_cost_rslt=sql_select($sql_yarn_pre_cost);
	foreach($sql_yarn_pre_cost_rslt as $row)
	{
		//$yarn_pre_cost_data[$row['JOB_NO']][$row['COUNT_ID']]['avg_cons'] += $row['CONS_QNTY'];
		$yarn_pre_cost_data[$row['JOB_NO']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['avg_cons'] += $row['CONS_QNTY'];
		$yarn_pre_cost_data[$row['JOB_NO']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['amount'] += $row['AMOUNT'];
	}
	unset($sql_yarn_pre_cost_rslt);
	//echo "<pre>";
	//print_r($yarn_pre_cost_data);

	//for images
	$images = return_library_array("select master_tble_id, image_location from common_photo_library where file_type = 1".where_con_using_array($jobNoArr, '1', 'master_tble_id'), "master_tble_id", "image_location");
	//echo "<pre>";
	//print_r($images);
	
	//for company details
	$company_library = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

	//for row span
	$jobRowSpan = array();
	foreach($dataArr as $job=>$jobArr)
	{
		//for woven info
		foreach($jobArr as $fabric=>$fabricArr)
		{
			foreach($fabricArr as $colorType=>$row)
			{
				$jobRowSpan[$job]++;
			}
		}
		
		//for yarn info
		foreach($yarnDataArr[$job] as $yarnComposition=>$yarnCompArr)
		{
			foreach($yarnCompArr as $yarnCnt=>$yarnRow)
			{
				$jobRowSpan[$job]++;
			}
		}
	}
	?>
	<!--<fieldset>-->
	<!--<style type="text/css">
		table tr td {
			font-size: 13px;
		}
		.rpt_table thead th{
			font-size: 14px;
		}
		.rpt_table tfoot th{
			font-size: 14px;
		}
	</style>-->		
    <div style="width:2030">
        <table width="2030"  cellspacing="0">
			<tr class="form_caption" style="border:none;">
				<td align="center" style="border:none; font-size:18px;" colspan="22">
					<? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
				</td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="25"> <? echo $report_title ;?></td>
			</tr>
		</table>
		<table width="2030" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<th width="40">SL</th>
				<th width="100">Job No</th>
				<th width="100">Picture</th>
				<th width="100">Style Ref.</th>
				<th width="150">Fabric/YARN Construction</th>
				<th width="120">Color</th>
				<th width="60">Yarn count</th>
				<th width="70">Woven Fabric Reff No</th>
				<th width="70">Order Quantity (Pcs)</th>
				<th width="70">Order Receive Date</th>
				<th width="70">Shipment Date</th>
				<th width="70">FOB Price</th>
				<th width="70">FOB Value</th>
				<th width="50">Excess cutting %</th>
				<th width="50">Qty with Cutting %</th>
				<th width="50">Process Loss %</th>
				<th width="70">Total Cons. Yarn</th>
				<th width="70">Total Cons. Woven Fabric</th>
				<th width="70">Total Grey Req. (Yds)</th>
				<th width="70">Total Grey Req. (Kgs)</th>
				<th width="70">Unallocated Yarn Stock</th>
				<th width="100">Stock Ref. (Lot)</th>
				<th width="70">Balance</th>
				<th width="70">Need to purchase</th>
				<th>Remarks</th>
			</thead>
		</table>
		<div style="max-height:350px; overflow-y:scroll; width:2030px" id="scroll_body" >
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2010" rules="all" id="table_body" >
				<tbody>
                <?php
				$sl = 0;
				$zs = 0;
				$totalGreyReqYds = 0;
				$totalGreyReqKgs = 0;
				$totalBalance = 0;
				$totalPurchase = 0;
				$summary_data = array();
				foreach($dataArr as $job=>$jobArr)
				{
					$rowSpanCond = 0;
					//for woven info
					foreach($jobArr as $fabric=>$fabricArr)
					{
						foreach($fabricArr as $colorType=>$row)
						{
							$zs++;
							$sl++;
							if ($sl%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							$bgcolor="#FFFFFF";
							$rowSpan = $jobRowSpan[$job];
							$style = $job_wise_arr[$job]['style'];
							$process_loss = $fabric_pre_cost_data[$row['fab_cost_dtls_id']][$job]['process_loss_percent'].'%';
							$consumption_woven_fabric = $fabric_pre_cost_data[$row['fab_cost_dtls_id']][$job]['avg_cons'];
							
							//$total_grey_req_yds = array_sum($fabric_costing_arr['woven']['grey'][$row['po_id']][$row['fab_cost_dtls_id']]);
							$total_grey_req_yds = $row['total_grey_req_yds'];
							//$purchase_qty = $purchaseQty[$job][$fabric][$colorType][$row['fab_cost_dtls_id']]['purchase_qty'];
							$fabric_purchase_qty = $purchaseQty[$row['fab_cost_dtls_id']]['purchase_qty'];
							//$balance_qty = $total_grey_req_yds-$fabric_purchase_qty;
							$balance_qty = $total_grey_req_yds;
							
							//for total
							$totalGreyReqYds += $total_grey_req_yds;
							$totalBalance += $balance_qty;
							$totalPurchase += $fabric_purchase_qty;

							//for summary
							$summary_data['woven'][$zs]['fabric'] = $fabric;
							$summary_data['woven'][$zs]['count'] = '';
							$summary_data['woven'][$zs]['required_qty'] = $total_grey_req_yds;
							$summary_data['woven'][$zs]['stock'] = '';
							$summary_data['woven'][$zs]['purchase_qty'] = $fabric_purchase_qty;
							$summary_data['woven'][$zs]['mill'] = $row['supplier'];
							$summary_data['woven'][$zs]['rate'] = $row['rate'];
							$summary_data['woven'][$zs]['fabric_yarn_value'] = $fabric_purchase_qty*$row['rate'];
							
							$amount = $fabric_pre_cost_data[$row['fab_cost_dtls_id']][$job]['amount'];
							$price_dzn = $priceDznArr[$job];
							$summary_data['woven'][$zs]['from_total_value'] = $amount/$price_dzn*100;
							
							$total_fabric_value += $summary_data['woven'][$zs]['fabric_yarn_value'];	
							$total_fabric_from_value += $summary_data['woven'][$zs]['from_total_value'];	

							if($rowSpanCond == 0)
							{
								$rowSpanCond++;
								?>
								<tr valign="middle" height="20" bgcolor="<? echo $bgcolor; ?>">
                                    <td width="40" align="center" rowspan="<?php echo $rowSpan; ?>"><?php echo $sl; ?></td>
                                    <td width="100" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $job; ?></td>
                                    <td width="100" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><img src="<?php echo '../../'.$images[$job]; ?>" height="97" width="89" /></td>
                                    <td width="100" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $style; ?></td>
                                    <td width="150" style="word-break: break-all;"><?php echo $fabric; ?></td>
                                    <td width="120" align="center" style="word-break: break-all;"><?php echo $color_type[$colorType]; ?></td>
                                    <td width="60" style="word-break: break-all;"></td>
                                    <td width="70" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php //echo $colorType; ?></td>
                                    <td width="70" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['order_qty_pcs']; ?></td>
                                    <td width="70" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['order_rcv_date']; ?></td>
                                    <td width="70" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['shipment_date']; ?></td>
                                    <td width="70" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo '$'.number_format($row['fob_price'], 2); ?></td>
                                    <td width="70" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo '$'.number_format($row['fob_value'], 2); ?></td>
                                    <td width="50" align="center" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo number_format($row['excess_cutting'],2).'%'; ?></td>
                                    <td width="50" align="right" rowspan="<?php echo $rowSpan; ?>" style="word-break: break-all;"><?php echo $row['qty_with_cutting'].' Pcs'; ?></td>
                                    <td width="50" align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
                                    <td width="70" style="word-break: break-all;"></td>
                                    <td width="70" align="right" style="word-break: break-all;"><?php echo number_format($consumption_woven_fabric, 2).' Yds'; ?></td>
                                    <td width="70" align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_yds, 2).' Yds'; ?></td>
                                    <td width="70" style="word-break: break-all;"></td>
                                    <td width="70" style="word-break: break-all;"></td>
                                    <td width="100" style="word-break: break-all;"></td>
                                    <td width="70" align="right" style="word-break: break-all;"><?php echo number_format($balance_qty, 2).' Yds'; ?></td>
                                    <td width="70" align="right" style="word-break: break-all;"><?php echo number_format($fabric_purchase_qty, 2).' Yds'; ?></td>
                                    <td style="word-break: break-all;"><?php echo $row['remarks']; ?></td>
								</tr>
								<?php
							}
							else
							{
								?>
								<tr valign="middle" height="20" bgcolor="<? echo $bgcolor; ?>">
									<td style="word-break: break-all;"><?php echo $fabric; ?></td>
									<td align="center" style="word-break: break-all;"><?php echo $color_type[$colorType]; ?></td>
									<td style="word-break: break-all;"></td>
									<td align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
                                    <td style="word-break: break-all;"></td>
									<td align="right" style="word-break: break-all;"><?php echo number_format($consumption_woven_fabric, 2).' Yds'; ?></td>
									<td align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_yds, 2).' Yds'; ?></td>
									<td style="word-break: break-all;"></td>
									<td style="word-break: break-all;"></td>
									<td style="word-break: break-all;"></td>
									<td align="right" style="word-break: break-all;"><?php echo number_format($balance_qty, 2).' Yds'; ?></td>
									<td  align="right" style="word-break: break-all;"><?php echo number_format($fabric_purchase_qty, 2).' Yds'; ?></td>
									<td style="word-break: break-all;"><?php echo $row['remarks']; ?></td>
								</tr>
								<?php
							}
						}
					}
					
					//for yarn info
					foreach($yarnDataArr[$job] as $yarnComposition=>$yarnComposArr)
					{
						foreach($yarnComposArr as $yarnCountId=>$yarnRow)
						{
							$process_loss = $yarn_pre_cost_data[$job]['yarn_process_loss']['process_loss_percent'].'%';
							//$consumption_yarn = $yarn_pre_cost_data[$job][$yarnRow['count_id']]['avg_cons'];
							$consumption_yarn = $yarn_pre_cost_data[$job][$yarnComposition][$yarnRow['count_id']]['avg_cons'];
							//$yarn_pre_cost_data[$row['JOB_NO']][$row['COPM_ONE_ID']][$row['COUNT_ID']]['avg_cons'] += $row['CONS_QNTY'];							
							
							$fabric = $composition[$yarnComposition].' '.$yarnRow['com_percent'].' % '.$yarn_type[$yarnRow['yarn_type_id']];
	
							/*$total_grey_req_kg = 0;
							foreach($yarn_costing_arr[$job][$yarnRow['count_id']][$yarnComposition] as $percent=>$percentArr)
							{
								foreach($percentArr as $key=>$val)
								{
									$total_grey_req_kg = $val['qty'];
								}
							}*/
							
							$total_grey_req_kg = $yarnRow['total_grey_req_kg'];
							
							$unallocated_qty = $productDataArr[$yarnComposition][$yarnRow['count_id']]['available_qty'];
							$product_id = implode(', ', $productDataArr[$yarnComposition][$yarnRow['count_id']]['lot']);
	
							$yarn_purchase_qty = $yarnRow['quantity']*1;
							//$yarn_balance_qty = number_format($total_grey_req_kg,2,'.','')-number_format($yarn_purchase_qty,2,'.','');
							$yarn_balance_qty = number_format($total_grey_req_kg,2,'.','')-number_format($unallocated_qty,2,'.','');
							
							//for total
							$totalGreyReqKgs += $total_grey_req_kg;
							$totalBalance += $yarn_balance_qty;
							$totalPurchase += $yarn_purchase_qty;
							
							//for summary
							if(!empty($yarnWorkOrderDataArr[$yarnRow['dtls_id']][$yarnRow['count_id']]))
							{
								$zs++;
								$summary_data['yarn'][$zs]['fabric'] = $fabric;
								$summary_data['yarn'][$zs]['count'] = $yarn_count_details[$yarnRow['count_id']];
								$summary_data['yarn'][$zs]['required_qty'] = $total_grey_req_kg;
								$summary_data['yarn'][$zs]['stock'] = $product_id;
								$summary_data['yarn'][$zs]['purchase_qty'] = $yarn_purchase_qty;
								$summary_data['yarn'][$zs]['mill'] = $yarnWorkOrderDataArr[$yarnRow['dtls_id']][$yarnRow['count_id']]['supplier_id'];
								$summary_data['yarn'][$zs]['rate'] = $yarnWorkOrderDataArr[$yarnRow['dtls_id']][$yarnRow['count_id']]['rate'];
								$summary_data['yarn'][$zs]['fabric_yarn_value'] = $yarn_purchase_qty*$yarnWorkOrderDataArr[$yarnRow['dtls_id']][$yarnRow['count_id']]['rate'];
								$amount = $yarn_pre_cost_data[$job][$yarnComposition][$yarnRow['count_id']]['amount'];
								$price_dzn = $priceDznArr[$job];
								$summary_data['yarn'][$zs]['from_total_value'] = $amount/$price_dzn*100;

								$total_yarn_value += $summary_data['yarn'][$zs]['fabric_yarn_value'];	
								$total_yarn_from_value += $summary_data['yarn'][$zs]['from_total_value'];	
							}
							?>
							<tr valign="middle" height="20" bgcolor="<? echo $bgcolor; ?>">
								<td style="word-break: break-all;"><?php echo $fabric; ?></td>
								<td align="center" style="word-break: break-all;"><?php echo $color_library[$yarnRow['color_id']]; ?></td>
								<td align="center" style="word-break: break-all;"><?php echo $yarn_count_details[$yarnRow['count_id']]; ?></td>
								<td align="center" style="word-break: break-all;"><?php echo number_format($process_loss, 2).'%'; ?></td>
								<td align="center" style="word-break: break-all;"><?php echo number_format($consumption_yarn, 2).' Kgs'; ?></td>
								<td align="center" style="word-break: break-all;"></td>
								<td style="word-break: break-all;"></td>
								<td align="right" style="word-break: break-all;"><?php echo number_format($total_grey_req_kg, 2).' Kgs'; ?></td>
								<td align="right" style="word-break: break-all;"><?php echo number_format($unallocated_qty, 2); ?></td>
								<td style="word-break: break-all;"><?php echo $product_id; ?></td>
								<td align="right" style="word-break: break-all;"><?php echo number_format($yarn_balance_qty, 2).' Kgs'; ?></td>
								<td align="right" style="word-break: break-all;"><?php echo number_format($yarn_purchase_qty, 2).' Kgs'; ?></td>
								<td style="word-break: break-all;"><?php echo $yarnRow['remarks']; ?></td>
							</tr>
							<?php
						}
					}
				}
				?>
            	</tbody>
                <tfoot>
                	<tr>
                    	<th align="right" colspan="18">Total&nbsp;</th>
                        <th align="right"><?php echo number_format($totalGreyReqYds, 2).' Yds'; ?></th>
                        <th align="right"><?php echo number_format($totalGreyReqKgs, 2).' Kgs'; ?></th>
                        <th></th>
                        <th></th>
                        <th align="right"><?php //echo number_format($totalBalance, 2); ?></th>
                        <th align="right"><?php //echo number_format($totalPurchase, 2); ?></th>
                        <th></td>
                    </tr>
                </tfoot>
            </table>
		</div>
        <table width="940" cellspacing="0" border="1" class="rpt_table" rules="all" style="margin-top:10px;">
            <thead>
                <th width="40">SL</th>
                <th width="130">Fabric/YARN</th>
                <th width="70">Yarn count</th>
                <th width="100">Required Qty</th>
                <th width="100">Stock</th>
                <th width="100">Purchase Qty</th>
                <th width="120">Mill</th>
                <th width="80">Rate</th>
                <th width="100">Woven Fabric/Yarn Value</th>
                <th width="100">% From total Value</th>
            </thead>
            <tbody>
            <?php
            $sl = 0;
            foreach($summary_data['woven'] as $key=>$val)
            {
                $sl++;
                ?>
                <tr height="20">
                    <td><?php echo $sl; ?></td>
                    <td style="word-break: break-all;"><?php echo $val['fabric']; ?></td>
                    <td><?php echo $val['count']; ?></td>
                    <td align="right"><?php echo number_format($val['required_qty'], 2).' Yds'; ?></td>
                    <td style="word-break: break-all;"><?php echo $val['stock']; ?></td>
                    <td align="right"><?php echo number_format($val['purchase_qty'], 2).' Yds'; ?></td>
                    <td style="word-break: break-all;"><?php echo $val['mill']; ?></td>
                    <td align="center"><?php echo '$'.number_format($val['rate'], 4); ?></td>
                    <td align="right"><?php echo '$'.number_format($val['fabric_yarn_value'], 2); ?></td>
                    <td align="right"><?php echo number_format($val['from_total_value'], 2); ?>%</td>
                </tr>
                <?php
            }
            foreach($summary_data['yarn'] as $key=>$val)
            {
                $sl++;
                ?>
                <tr height="20">
                    <td><?php echo $sl; ?></td>
                    <td style="word-break: break-all;"><?php echo $val['fabric']; ?></td>
                    <td><?php echo $val['count']; ?></td>
                    <td align="right"><?php echo number_format($val['required_qty'], 2).' Kgs'; ?></td>
                    <td style="word-break: break-all;"><?php echo $val['stock']; ?></td>
                    <td align="right"><?php echo number_format($val['purchase_qty'], 2).' Kgs'; ?></td>
                    <td style="word-break: break-all;"><?php echo $val['mill']; ?></td>
                    <td align="center"><?php echo '$'.number_format($val['rate'], 4); ?></td>
                    <td align="right"><?php echo '$'.number_format($val['fabric_yarn_value'], 2); ?></td>
                    <td align="right"><?php echo number_format($val['from_total_value'], 2); ?>%</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
            <tfoot style="font-weight:bold;">
                <?php
				if($total_fabric_value>0)
				{
				?>
                <tr height="20">
                    <td colspan="8" align="center">TOTAL FABRIC COST</td>
                    <td align="right"><?php echo '$'.number_format($total_fabric_value, 2); ?></td>
                    <td align="right"><?php echo number_format($total_fabric_from_value, 2); ?>%</td>
                </tr>
                <?php
				}
				if($total_yarn_value>0)
				{
				?>
                <tr height="20">
                    <td colspan="8" align="center">TOTAL YARN COST</td>
                    <td align="right"><?php echo '$'.number_format($total_yarn_value, 2); ?></td>
                    <td align="right"><?php echo number_format($total_yarn_from_value, 2); ?>%</td>
                </tr>
                <?php
				}
				?>
            </tfoot>
        </table>
        <table width="940" cellspacing="0" border="0" style="margin-top:30px;">
            <tbody>
                <tr height="20">
                    <td colspan="2"><strong><u>Remarks :</u></strong></td>
                </tr>
                <?php
                $sl = 0;
                foreach($termsDataArr as $key=>$val)
                {
                    $sl++;
                    ?>
                    <tr height="20">
                        <td width="20" valign="top"><?php echo $sl.'.'; ?></td>
                        <td valign="top"><p><?php echo $val; ?></p></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <br/> <br/> <br/><br/> <br/> <br/>
        <div style="width:100%;">
            <table cellspacing="0" width="100%" border="0" >
                <tr align="center">
                    <td style="text-decoration:overline; font-weight:bold;">MERCHANDISER</td>
                    <td style="text-decoration:overline; font-weight:bold;">MERCHANDISING AGM</td>
                    <td style="text-decoration:overline; font-weight:bold;">MERCHANDISING DIRECTOR</td>
                </tr>
            </table>
        </div>
        </div>
	<!--</fieldset>-->
	<?php
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
	echo "$total_data####$filename";
	exit();
}
?>