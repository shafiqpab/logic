<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	?>
	<script>
		function js_set_value( job_no )
		{
			document.getElementById('selected_job').value=job_no;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                 <th width="150" colspan="3"> </th>
                    <th><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                  <th width="150" colspan="4"> </th>
                </tr>
                <tr>
                    <th width="130">Buyer Name</th>
                    <th width="80">Job No</th>
                    <th width="100">Style Ref </th>
                    <th width="100">Internal Ref </th>
                    <th width="100">File No </th>
                    <th width="100">Order No</th>
                    <th width="160">Ship. Date Range</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tr class="general">
                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $buyer_id, "" ); ?></td>
                <td>
                	<input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px" >
                	<input type="hidden" id="selected_job">
                </td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
                <td>
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                </td>
                <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'bom_of_yarn_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="8">
                    <? echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );	?>
                    <? echo load_month_buttons(); ?>
                </td>
            </tr>
     	</table>
    	<div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if(str_replace("'","",$data[1])==0)
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
	
	$job_cond=""; $order_cond=""; $style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number = '$data[6]'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no ='$data[7]'"; //else  $style_cond="";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'  $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '$data[6]%'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '$data[7]%'  "; //else  $style_cond="";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'  $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]'"; //else  $style_cond="";
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'  $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; //else  $order_cond="";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]%'"; //else  $style_cond="";
	}

	$internal_ref = str_replace("'","",$data[9]);
	$file_no = str_replace("'","",$data[10]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
		$insert_year="YEAR(a.insert_date)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
		$insert_year="to_char(a.insert_date,'YYYY')";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr);

	$sql= "select $insert_year as year, a.id, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.quotation_id, a.currency_id, a.job_quantity, b.po_number, b.grouping, b.file_no, b.po_quantity, b.shipment_date, a.job_no, c.id as pre_id, c.exchange_rate, c.costing_per, c.approved  from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 and a.job_no=b.job_no_mst and a.garments_nature=100 and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer_id_cond $job_cond $style_cond $order_cond $file_no_cond $internal_ref_cond $year_cond order by a.id DESC";
	//echo $sql;
	echo  create_list_view("list_view", "Year,Job No,Company,Buyer Name,Style Ref. No,Internal Ref,File No,Job Qty.,PO number,PO Quantity,Shipment Date, Precost id", "50,50,120,100,100,100,100,80,90,80,70,100","1080","270",0, $sql , "js_set_value", "job_no,buyer_name,quotation_id,currency_id,exchange_rate,costing_per,approved,id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0,0", $arr , "year,job_no_prefix_num,company_name,buyer_name,style_ref_no,grouping,file_no,job_quantity,po_number,po_quantity,shipment_date,pre_id", "",'','0,0,0,0,0,0,0,1,0,1,3,0') ;
	exit();
}