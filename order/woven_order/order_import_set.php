<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Import Set
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	15-11-2019
Updated by 		: 		
Update date		: 
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Set Order Import","../../", 1, 1, $unicode,1,'');


if($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
		}
	
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
        <table width="900" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Job No</th>
                    <th>Style Ref </th>
                    <th>Internal Ref</th>
                    <th>File No</th>
                    <th>Order No</th>
                    <th colspan="2">Ship Date Range</th>
                    <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>
                </tr>        
            </thead>
            <tr class="general">
                <td> 
                    <input type="hidden" id="selected_job">
					<? echo create_drop_down( "cbo_company_mst", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "--Select Company--", $selected,"load_drop_down( 'order_import', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 120, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'order_import', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
        	</tr>
            <tr>
                <td height="40" align="center" colspan="10">
                <? echo load_month_buttons(1);  ?>
                </td>
            </tr>
        </table>
        <div id="search_div"></div>
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
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else
		{
			$buyer="";
		}
	}
	else
	{
		$buyer=" and a.buyer_name='$data[1]'";
	}
	
	//if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[6]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	
	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]' $year_cond";
		if (trim($data[8])!="") $order_cond=" and b.po_number='$data[8]' "; //else  $order_cond=""; 
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no='$data[9]' "; //else  $style_cond=""; 
	}
	else if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%' $year_cond"; //else  $job_cond=""; 
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]%' ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%' "; //else  $style_cond=""; 
	}
	else if($data[7]==2)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%' $year_cond"; //else  $job_cond=""; 
		if (trim($data[8])!="") $order_cond=" and b.po_number like '$data[8]%' ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%' "; //else  $style_cond=""; 
	}
	else if($data[7]==3)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]' $year_cond"; //else  $job_cond=""; 
		if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]' ";
		if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]' "; //else  $style_cond=""; 
	}
			
	$internal_ref = str_replace("'","",$data[10]);
	$file_no = str_replace("'","",$data[11]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	if ($data[2]==0)
	{
		$arr=array(2=>$buyer_arr,9=>$item_category);
		if($db_type==0)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, a.order_repeat_no, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, DATEDIFF(pub_shipment_date,po_received_date) as date_diff, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=2 and a.order_uom=1 and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond order by a.job_no DESC";// and b.is_confirmed=2
		}
		else if($db_type==2)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, a.order_repeat_no, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=2 and a.order_uom=1  and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond order by a.job_no DESC";//and b.is_confirmed=2
		}
		//echo $sql;
		echo  create_list_view("list_view", "Job No,Year,Buyer Name,Style Ref. No,Job Qty.,Repeat No,PO number,PO Qty.,Shipment Date,Gmts Nature,Ref no, File No,Lead time", "50,40,100,100,70,50,50,70,65,65,70,70,50","900","300",0, $sql , "js_set_value", "job_no", "", 1, "0,0,buyer_name,0,0,0,order_repeat_no,0,0,garments_nature,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,style_ref_no,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,garments_nature,grouping,file_no,date_diff", "",'','0,0,0,0,1,0,0,1,3,0,0,0,0');
	}
	else
	{
		$arr=array (2=>$company_arr,3=>$buyer_arr,5=>$item_category);
		if($db_type==0)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.garments_nature, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=2 and a.order_uom=1 and a.status_active=1 and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no DESC";
		}
		else if($db_type==2)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=2 and a.order_uom=1 and a.status_active=1 and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no DESC";
		}
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,110,100,100,60","900","200",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
	exit();
}

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   
	exit();	 
}


//include 'excel_reader.php';       // include the class
//$excel = new Spreadsheet_Excel_Reader();     
// creates object instance of the class
/*$excel->read('Puma-ExcelUpload.xls');   // reads and stores the excel file data

// Test to see the excel data stored in $sheets property
echo '<table border="1" class="rpt_table">';
$x=1;
while($x<=$excel->sheets[0]['numRows']) { // reading row by row 
  echo "\t<tr>\n";
  $y=1;
  while($y<=$excel->sheets[0]['numCols']) {// reading column by column 
	$cell = isset($excel->sheets[0]['cells'][$x][$y]) ? $excel->sheets[0]['cells'][$x][$y] : '';
	echo "\t\t<td>$cell</td>\n";  // get each cells values
	$y++;
  }  
  echo "\t</tr>\n";
  $x++;
}
echo '</table>';*/
?>
<script>
	function openmypage_job(page_link,title)
	{
		page_link=page_link;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=430px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job");
			if (theemail.value!="")
			{
				freeze_window(5);
				$('#txt_job_no').val( theemail.value );
				release_freezing();
			}
		}
	}
</script>

</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<!-- Important Field outside Form -->  
    <? echo load_freeze_divs ("../../",$permission);  ?>
            <h3 style="width:510px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Excel File Browse (.xls Only)</h3> 
         	<div id="content_search_panel" style="width:500px" >
            <fieldset style="width:500px;">
                <form name="excelImport_1" id="excelImport_1" action="excel_order_import_set.php" enctype="multipart/form-data" method="post"> 
				<table width="500" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <tr>
                	<!--<td width="90"><b>Job</b></td>
                    <td width="120"><input style="width:110px;" type="text" title="Double Click to Search" onDblClick="openmypage_job('order_import.php?action=job_popup','Job/Order Selection Form')" class="text_boxes" placeholder="New Job No" name="txt_job_no" id="txt_job_no" readonly />                 </td>-->
                    <td width="90" class="must_entry_caption"><b>Select File</b></td>
                    <td width="200">
                        <input type="file" id="uploadfile" name="uploadfile" class="image_uploader" required style="width:200px" />
                    </td>
                    <td>
                    	<input type="submit" name="submit" value="Upload" class="formbutton" style="width:60px" />
                    </td>
                </tr>
                </table>
                </form>
        </fieldset>
    </div>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?


?>
