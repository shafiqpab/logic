<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create Inspection Bill Work Order
Functionality	 :
JS Functions	 :
Created by		 : Md Mamun Ahmed Sagor
Creation date 	 : 24-01-2023
Requirment Client:
Requirment By    :
Requirment type  :
Requirment       :
Affected page    :
Affected Code    :
DB Script        :
Updated by 		 :
Update date		 :
QC Performed BY	 :
QC Date			 :
Comments		 : From this version oracle conversion is start
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.others.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
$inspection_for=array(1=>"Garments",2=>"Fabrics",3=>"Trims",4=>"Chemical" );
//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
//$po_number_arr=return_library_array("select id,po_number from wo_po_break_down", "id", "po_number");
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1
	and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type
	where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	die;
}

if($action=="order_popup_wovalue")
{
	echo load_html_head_contents("Lab Test Work Order", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
</head>
<body>
<div align="center">
	<fieldset style="width:470px;">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
          <table cellspacing="0" cellpadding="0" border="1" rules="all" width="450" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th width="200">Order No</th>
                    <th width="100">Order Qty</th>
                    <th width="">WO Value</th>
                </thead>
            </table>
            <div style="width:450px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="430" class="rpt_table" id="tbl_list_search" >
                <?
                    $i=1;
					$sql=sql_select("select a.job_no_mst,a.po_number,a.po_quantity,b.job_quantity  from wo_po_break_down a,wo_po_details_master b
					where job_no_mst='$txt_job_no'   and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1
					and b.is_deleted=0
					group by a.id,a.job_no_mst,a.po_number,a.po_quantity,b.job_quantity");
                    foreach($sql as $name)
                    {
						$order_percentage=($name[csf('po_quantity')]/$name[csf('job_quantity')])*100;
						$workorder_value=($wo_value*$order_percentage)/100;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $name[csf('id')]."_".$name[csf('net_rate')];?>')">
							<td width="50" align="center"><?php echo "$i"; ?></td>
							<td width="200" align="center"><p><? echo $name[csf('po_number')]; ?></p></td>
							<td width="100" align="right"><p><? echo $name[csf('po_quantity')]; ?></p></td>
							<td width="" align="right"><p><? echo number_format($workorder_value,2); ?></p></td>
						</tr>
						<?
						$i++;
                    }
                ?>
                </table>
        	</div>
        </form>
    </fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
<?
exit();
}

 

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

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
	<table width="920" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>
                    <tr>
                     <th width="150" colspan="3"> </th>
                            <th>
                              <?
                               echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th  colspan="3"></th>
                    </tr>
                    <tr>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                         <th width="100">Job No</th>
                          <th width="100">Style Ref </th>
                        <th width="140">Order No</th>
                        <th width="150">Date Range</th><th></th>
                        </tr>
                    </thead>
        			<tr>
                    	<td>
                        <input type="hidden" id="selected_job" name="selected_job">
						<?
                            echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --",$cbo_company_name,"load_drop_down( 'inspection_bill_work_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1 );
                        ?>
                    </td>
                   	<td id="buyer_td">
                     <?
						echo create_drop_down( "cbo_buyer_name", 162, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1
	and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type
	where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
					 ?>	</td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:90px"></td>
                     <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
					 </td>
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'inspection_bill_work_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:90px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
            <?
			    echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
			?>
			<? echo load_month_buttons();  ?>
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_po_search_list_view")
{

	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else  $buyer="";
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[5]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";}
	$job_cond="";
	$order_cond="";
	$style_cond="";
	if($data[8]==1)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]' $year_cond";
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number = '$data[6]'  ";
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no ='$data[7]'";
	}
	if($data[8]==2)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%' $year_cond";
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '$data[6]%'  ";
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '$data[7]%'  ";
	}
	if($data[8]==3)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]' $year_cond";
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]'  ";
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]'";
	}
	if($data[8]==4 || $data[8]==0)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%' $year_cond";
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  ";
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]%'";
	}
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr);

	if($db_type==0)
	{
		$sql= "select YEAR(a.insert_date) as year, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity, b.shipment_date, a.job_no, c.id as pre_id from wo_po_details_master  a, wo_po_break_down b, wo_pre_cost_mst c,
		wo_pre_cost_dtls d   where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=d.job_no and a.status_active=1 and a.is_deleted=0 and
		b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.approved=1 $shipment_date
		$company $buyer  $job_cond $style_cond $order_cond  order by a.job_no";
	}
	if($db_type==2)
	{
		 $sql= "select to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,
		 b.po_number,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b, wo_pre_cost_mst c,
		 wo_pre_cost_dtls d   where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=d.job_no and a.status_active=1 and a.is_deleted=0
		 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.approved=1 $shipment_date
		 $company $buyer  $job_cond $style_cond $order_cond
		 group by a.insert_date,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,
		 b.po_number,b.po_quantity,b.shipment_date,a.job_no,c.id order by a.job_no";
	}


	echo  create_list_view("list_view", "Year,Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date, Precost id",
	"50,50,120,100,100,80,90,80,70,90","880","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr ,
	"year,job_no_prefix_num,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,pre_id", "",'','0,0,0,0,0,1,0,1,3,1') ;
}


if ($action=="po_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	

  ?>
	<script>
			var selected_id = new Array;
			var selected_name = new Array;
			var selected_no = new Array;
			function check_all_data() {
				var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
				tbl_row_count = tbl_row_count - 0;
				for( var i = 1; i <= tbl_row_count; i++ ) {
					var onclickString = $('#tr_' + i).attr('onclick');
					var paramArr = onclickString.split("'");
					var functionParam = paramArr[1];
					js_set_value( functionParam );
					
				}
			}
			function set_checkvalue()
			{
				if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
				else document.getElementById('chk_job_wo_po').value=0;
			}
			// function js_set_value( job_no_po_id )
			// {
			// 	var jobPo=job_no_po_id.split("_");
			// 	document.getElementById('selected_job').value=jobPo[0];
			// 	document.getElementById('selected_po').value=jobPo[1];
			// 	document.getElementById('selected_po_num').value=jobPo[2];
			// 	parent.emailwindow.hide();
			// }
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) { 
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}
			function js_set_value( strCon ) 
			{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var str_or = splitSTR[1];
			var selectID = splitSTR[2];
			 
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );		 
				selected_no.push( str_or );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
		 
				selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				 
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			 	 
			num 	= num.substr( 0, num.length - 1 );
			// alert(`${id} ${num}`);
			$('#selected_job').val( num );
			$('#selected_job_id').val( id );
		 
		}

		
    </script>
 </head>
 <body>
 <div align="center" style="width:100%;" >
 <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="920" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>
                    <tr>
                     <th width="150" colspan="3"> </th>
                            <th>
                              <?
                               echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th  colspan="3"></th>
                    </tr>
                    <tr>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                         <th width="100">Job No</th>
                          <th width="100">Style Ref </th>
                        <th width="140">Order No</th>
                        <th width="150">Date Range</th><th></th>
                        </tr>
                    </thead>
        			<tr>
                    	<td>
                         <input type="hidden" id="selected_job" name="selected_job">
                         <input type="hidden" id="selected_job_id" name="selected_job_id">
                         <input type="hidden" id="txt_workorder_no" name="txt_workorder_no" value="<? echo $txt_workorder_no ?>">
						 <input type="hidden" id="cbo_level" name="cbo_level" value="<? echo $cbo_level ?>">

						<?
                            echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --",$cbo_company_name,"load_drop_down( 'inspection_bill_work_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1 );
                        ?>
                    </td>
                   	<td id="buyer_td">
                     <?
				echo create_drop_down( "cbo_buyer_name", 162, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1
	and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type
	where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
					 ?>	</td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:90px" value="<? //echo $job ?>" <? //echo $disabled ?>></td>
                     <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
					 </td>
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_workorder_no').value+'_'+document.getElementById('cbo_level').value, 'create_po_id_search_list_view', 'search_div', 'inspection_bill_work_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:90px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
            <?
			    echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
			?>
			<? echo load_month_buttons();  ?>
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
 <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
}

if($action=="create_po_id_search_list_view")
{

	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else  $buyer="";
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[5]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";}

	$job="";
	$job_cond="";
	$order_cond="";
	$style_cond="";
	if($data[8]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]' $year_cond";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number = '$data[6]'  ";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no ='$data[7]'";
	}
	if($data[8]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%' $year_cond";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '$data[6]%'  ";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '$data[7]%'  ";
	}
	if($data[8]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]' $year_cond";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]'  ";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]'";
	}
	if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%' $year_cond";
		if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  ";
		if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]%'";
	}
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	if($data[4]!=""){
		$job_cond=" and a.job_no_prefix_num='$data[4]'";
	}

	$sql_dtls="select b.job_id from wo_inspection_mst a,wo_inspection_dtls b where a.id=b.booking_mst_id and company_id=$data[0] $year_cond and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 group by b.job_id ";
	$sql_result= sql_select($sql_dtls);

		foreach ($sql_result as $row){
     		$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
		}

		if(count($job_id_arr)>0){
			$job_ids=implode(",",$job_id_arr);
			$job_conds="and a.id not in ($job_ids)";
		}else{
			$job_conds="";
		}

		 $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		 $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
		 $arr=array (2=>$comp,3=>$buyer_arr);

	if($data[10]==2){
		$sql= "select to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, a.id,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d   where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=d.job_no and a.status_active=1 and a.is_deleted=0 and d.inspection >0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.shiping_status not in(3) $shipment_date $company $buyer  $job_cond $style_cond $order_cond $year_cond $job_conds group by a.insert_date,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, a.id  ,a.job_no,c.id order by a.job_no DESC";

		echo  create_list_view("list_view", "Year,Job No,Company,Buyer Name,Style Ref. No,Job Qty.,Precost id","50,50,120,100,100,80,90","700","320",0, $sql , "js_set_value", "job_no,id", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr ,"year,job_no_prefix_num,company_name,buyer_name,style_ref_no,po_num,job_quantity,pre_id", "",'','0,0,0,0,0,0,0,0,0',"",1) ;
	}else{
		$sql= "select to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, a.id,a.job_no,c.id as pre_id,b.po_number as po_num,b.id as bid from wo_po_details_master  a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d   where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=d.job_no and a.status_active=1 and a.is_deleted=0 and d.inspection >0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.shiping_status not in(3) $shipment_date $company $buyer  $job_cond $style_cond $order_cond $year_cond $job_conds group by a.insert_date,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, a.id  ,a.job_no,c.id,b.po_number,b.id order by a.job_no DESC";

		echo  create_list_view("list_view", "Year,Job No,Company,Buyer Name,Style Ref. No,Po Number,Job Qty.,Precost id","50,50,120,100,100,100,80,90","800","320",0, $sql , "js_set_value", "job_no,bid", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr ,"year,job_no_prefix_num,company_name,buyer_name,style_ref_no,po_num,job_quantity,pre_id", "",'','0,0,0,0,0,0,0,0,0,0',"",1) ;
	}





}
if($action=="show_dtls_list_view"){

	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$job_ids=rtrim($job_ids,',');
	//echo $workorder_date.'='.$cbo_currency.'D';
	$wo_sql="select id,booking_mst_id,booking_no,job_no,job_id,buyer_id,inspection_for_id,wo_qnty,rate,amount,discount_per,discount_amount,(amount-discount_amount) as tot_amt,vat_per,vat_amount,net_amount,remark,style_desc,style_ref from wo_inspection_dtls where  job_id in ($job_ids) and is_deleted = 0 and status_active=1 ";
	$wo_data=sql_select($wo_sql);

	foreach($wo_data as $val){
		$job_data_arr[$val[csf('job_id')]]['wo_id']=$val[csf('id')];
		$job_data_arr[$val[csf('job_id')]]['inspection_for_id']=$val[csf('inspection_for_id')];
		$job_data_arr[$val[csf('job_id')]]['wo_qnty']+=$val[csf('wo_qnty')];
		$job_data_arr[$val[csf('job_id')]]['rate']=$val[csf('rate')];
		$job_data_arr[$val[csf('job_id')]]['amount']=$val[csf('amount')];
		$job_data_arr[$val[csf('job_id')]]['discount_per']=$val[csf('discount_per')];
		$job_data_arr[$val[csf('job_id')]]['discount_amount']=$val[csf('discount_amount')];
		$job_data_arr[$val[csf('job_id')]]['tot_amt']=$val[csf('tot_amt')];
		$job_data_arr[$val[csf('job_id')]]['vat_per']=$val[csf('vat_per')];
		$job_data_arr[$val[csf('job_id')]]['vat_amount']=$val[csf('vat_amount')];
		$job_data_arr[$val[csf('job_id')]]['net_amount']=$val[csf('net_amount')];
		$job_data_arr[$val[csf('job_id')]]['remark']=$val[csf('remark')];
		$job_data_arr[$val[csf('job_id')]]['style_desc']=$val[csf('style_desc')];
	}



	if($cbo_level==2){
		$sql= "SELECT a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.style_description, a.id as job_id, sum(b.po_quantity) as po_quantity, d.costing_per_id,d.inspection, c.exchange_rate as pid FROM wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d WHERE a.id = b.job_id AND a.id = c.job_id AND c.job_id = d.job_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND a.id IN ($job_ids) GROUP BY a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.style_description, d.costing_per_id,d.inspection, c.exchange_rate, a.id ORDER BY a.job_no";
	}else{
		$sql= "SELECT a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.style_description, a.id as job_id, sum(b.po_quantity) as po_quantity, d.costing_per_id,d.inspection, c.exchange_rate,b.po_number,b.id as pid FROM wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d WHERE a.id = b.job_id AND a.id = c.job_id AND c.job_id = d.job_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND b.id IN ($job_ids) GROUP BY a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.style_description, d.costing_per_id,d.inspection, c.exchange_rate, a.id ,b.po_number,b.id ORDER BY a.job_no";
	}
	
		  //echo $sql;die;
	$job_wise_data=sql_select($sql);


	$i=1;
	$jobId_Arr=array();

		foreach($job_wise_data as $val){
			
			$costing_per_id=$val[csf('costing_per_id')];
			$po_quantity=$val[csf('po_quantity')];
			$inspection=$val[csf('inspection')];
			$inspection_for_id=$job_data_arr[$val[csf('job_id')]]['inspection_for_id'];
			$wo_qnty=$job_data_arr[$val[csf('job_id')]]['wo_qnty'];
			$rate=$job_data_arr[$val[csf('job_id')]]['rate'];
			$amount=$job_data_arr[$val[csf('job_id')]]['amount'];
			$discount_per=$job_data_arr[$val[csf('job_id')]]['discount_per'];
			$discount_amount=$job_data_arr[$val[csf('job_id')]]['discount_amount'];
			$vat_per=$job_data_arr[$val[csf('job_id')]]['vat_per'];
			$vat_amount=$job_data_arr[$val[csf('job_id')]]['vat_amount'];
			$net_amount=$job_data_arr[$val[csf('job_id')]]['net_amount'];
			$remark=$job_data_arr[$val[csf('job_id')]]['remark'];
			$style_desc=$job_data_arr[$val[csf('job_id')]]['style_desc'];
			$wo_id=$job_data_arr[$val[csf('job_id')]]['wo_id'];
			$tot_amt=$job_data_arr[$val[csf('job_id')]]['tot_amt'];
			$order_price_per_dzn=0;

		
		 
			 
			 
				 ?>
			 <tr>
				<td>
					<input style="width:90px;" type="text" class="text_boxes"  name="txt_job_no_<?=$i;?>" id="txt_job_no_<?=$i;?>"   value="<?=$val[csf('job_no')];?>" readonly /><!--openmypage()-->
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_job_id_<?=$i;?>" id="txt_job_id_<?=$i;?>"  value="<?=$val[csf('job_id')];?>"  readonly/>
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_no_<?=$i;?>" id="txt_order_no_<?=$i;?>"  value="<?=$val[csf('po_number')];?>"  readonly/>
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id_<?=$i;?>" id="txt_order_id_<?=$i;?>"  value="<?=$val[csf('pid')];?>"  readonly/>
				</td>
				<? if($cbo_level==1){?>
				<td>
					<input style="width:90px;" type="text" class="text_boxes"  name="txt_order_no_<?=$i;?>" id="txt_order_no_<?=$i;?>"   value="<?=$val[csf('po_number')];?>" readonly /><!--openmypage()-->
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id_<?=$i;?>" id="txt_order_id_<?=$i;?>"  value="<?=$val[csf('pid')];?>"  readonly/>
				</td>
				<?}?>
				<td>	 
				<input style="width:90px;" type="text" class="text_boxes"  name="txt_buyer_name_<?=$i;?>" id="txt_buyer_name_<?=$i;?>"   value="<?=$buyer_arr[$val[csf('buyer_name')]];?>" readonly/>
				<input style="width:90px;" type="hidden" class="text_boxes"  name="cbo_buyer_id_<?=$i;?>" id="cbo_buyer_id_<?=$i;?>"  value="<?=$val[csf('buyer_name')];?>"  readonly/>
				
				 </td>
				<td>
					<input class="text_boxes" type="text" style="width:90px" name="txt_style_ref_<?=$i;?>" id="txt_style_ref_<?=$i;?>" value="<?=$val[csf('style_ref_no')];?>" readonly/>					
				</td>
				<? if($style_desc!=""){?>
				<td><input class="text_boxes" type="text" style="width:160px" name="txt_style_desc_<?=$i;?>" id="txt_style_desc_<?=$i;?>" value="<?=$style_desc;?>" /> </td>
				<?}else{?>
				<td><input class="text_boxes" type="text" style="width:160px" name="txt_style_desc_<?=$i;?>" id="txt_style_desc_<?=$i;?>" value="<?=$val[csf('style_description')];?>" /> </td>
					<?}?>
				<td><? echo create_drop_down( "cbo_inspection_id_$i", 90, $inspection_for, 0, 1, "Select Inspection",$inspection_for_id, "", "", "" );?></td>
				 <td>
					<input class="text_boxes" type="text" style="width:120px" name="txt_inspection_qnty_<?=$i;?>" id="txt_inspection_qnty_<?=$i;?>" value="<?=$wo_qnty;?>" onBlur="calculate_wo_value(<?=$i;?>,'update_qnty')" />
					<input type="hidden" id="update_dtls_id_<?=$i;?>" name="update_dtls_id_<?=$i;?>" value="<?=$wo_id;?>">
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:80px" name="txt_inspection_rate_<?=$i;?>" id="txt_inspection_rate_<?=$i;?>" onBlur="calculate_wo_value(<?=$i;?>,'rate')"  value="<?=$rate;?>"/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:70px" name="txt_amount_<?=$i;?>" id="txt_amount_<?=$i;?>"  value="<?=$amount;?>"  readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:70px" name="txt_discount_<?=$i;?>" id="txt_discount_<?=$i;?>" value="<?=$discount_per;?>" onBlur="calculate_wo_value(<?=$i;?>,'discount') "/>
				 </td>
				  <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_discount_value_<?=$i;?>" id="txt_discount_value_<?=$i;?>"   value="<?=$discount_amount;?>" onBlur="calculate_wo_value(<?=$i;?>,'dis_amt') "  />
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_insp_val_with_vat_<?=$i;?>" value="<?=$tot_amt;?>"  id="txt_insp_val_with_vat_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_<?=$i;?>" id="txt_vat_<?=$i;?>" value="<?=$vat_per;?>" onBlur="calculate_wo_value(<?=$i;?>,'vat')" />
				 </td>
				   <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_amount_<?=$i;?>" id="txt_vat_amount_<?=$i;?>" value="<?=$vat_amount;?>"  onBlur="calculate_wo_value(<?=$i;?>,'vat_amt') "/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_insp_val_without_vat_<?=$i;?>" value="<?=$net_amount;?>" id="txt_insp_val_without_vat_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes" type="text" style="width:100px" name="txt_remarks_<?=$i;?>" id="txt_remarks_<?=$i;?>" value="<?=$remark;?>"  />
					<input class="text_boxes_numeric" type="hidden" style="width:100px" name="txtRowId" id="txtRowId" value="<?=count($job_wise_data);?>"  />
				 </td>

			</tr>
			<? 
				$jobIdArr[$val[csf('job_id')]]=$val[csf('job_id')];
			$i++; 
		
	}
		 ?>
		  <tr>
				<td>
					<input style="width:90px;" type="text" class="text_boxes"  name="txt_job_no_<?=$i;?>" id="txt_job_no_<?=$i;?>"
					 onDblClick="openmypage_wovalue('<?=implode(',',$jobIdArr);?>',2)" placeholder="Double Click" readonly/><!--openmypage()-->
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_job_id_<?=$i;?>" id="txt_job_id_<?=$i;?>"   readonly/>
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_no_<?=$i;?>" id="txt_order_no_<?=$i;?>"   readonly/>
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id_<?=$i;?>" id="txt_order_id_<?=$i;?>"   readonly/>
				</td>
				<? if($cbo_level==1){?>
				<td>
					<input style="width:90px;" type="text" class="text_boxes"  name="txt_order_no_<?=$i;?>" id="txt_order_no_<?=$i;?>"
					 readonly/><!--openmypage()-->
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id_<?=$i;?>" id="txt_order_id_<?=$i;?>"   readonly/>
				</td>
				<?}?>
				<td>
				<input style="width:90px;" type="text" class="text_boxes"  name="txt_buyer_name_<?=$i;?>" id="txt_buyer_name_<?=$i;?>"   value="" readonly/>
				<input style="width:90px;" type="hidden" class="text_boxes"  name="cbo_buyer_id_<?=$i;?>" id="cbo_buyer_id_<?=$i;?>"  value=""  readonly/>
			  </td>
				<td>
					<input class="text_boxes" type="text" style="width:90px" name="txt_style_ref_<?=$i;?>" id="txt_style_ref_<?=$i;?>"/>					
				</td>
				<td><input class="text_boxes" type="text" style="width:160px" name="txt_style_desc_<?=$i;?>" id="txt_style_desc_<?=$i;?>"/> </td>
				<td><? echo create_drop_down( "cbo_inspection_id_<?=$i;?>", 90, $test_for, 0, 1, "Select Inspection",$selected, "", "", "" );?></td>
				 <td>
					<input class="text_boxes" type="text" style="width:120px" name="txt_inspection_qnty_<?=$i;?>" id="txt_inspection_qnty_<?=$i;?>" />
					<input type="hidden" id="update_dtls_id_<?=$i;?>" name="update_dtls_id_<?=$i;?>">
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:80px" name="txt_inspection_rate_<?=$i;?>" id="txt_inspection_rate_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:70px" name="txt_amount_<?=$i;?>" id="txt_amount_<?=$i;?>" onBlur="calculate_wo_value()"/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:70px" name="txt_discount_<?=$i;?>" id="txt_discount_<?=$i;?>" onBlur="calculate_wo_value()"/>
				 </td>
				  <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_discount_value_<?=$i;?>" id="txt_discount_value_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_insp_val_with_vat_<?=$i;?>" id="txt_insp_val_with_vat_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_<?=$i;?>" id="txt_vat_<?=$i;?>" readonly/>
				 </td>
				   <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_amount_<?=$i;?>" id="txt_vat_amount_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_insp_val_without_vat_<?=$i;?>" id="txt_insp_val_without_vat_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes" type="text" style="width:100px" name="txt_remarks_<?=$i;?>" id="txt_remarks_<?=$i;?>" readonly/>
				 </td>

			</tr>

			
		 <?
	
	exit();
}

 if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rownum=str_replace("'","",$txtRowId);

	for ($i=1; $i<=$rownum; $i++){
		$txt_job_id = "txt_job_id_".$i;
		$update_dtls_id = "update_dtls_id_".$i;
		$txt_insp_val_without_vat = "txt_insp_val_without_vat_".$i;
		$job_id=str_replace("'","",$$txt_job_id);
		$updatedtlsid=str_replace("'","",$$update_dtls_id);
		$job_idArr[$job_id]=$job_id;
		$update_dtls_idArr[$updatedtlsid]=$updatedtlsid;
	}
	$cbo_currency=str_replace("'","",$cbo_currency);
	$updateid=str_replace("'","",$update_id);
	if($updateid)
	{
		$updateCond=" and b.booking_mst_id!=$update_id ";
	}
	else{
		$updateCond="";
	}
	 
	$job_id=implode(",",$job_idArr);
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	 
	if(str_replace("'","",$job_id) !=''){
		 $condition->jobid_in($job_id);
	}
	$condition->init();
	$other= new other($condition);
	//echo "10**=A".$job_id;
	//echo $other->getQuery(); die;
	$other_costing_arr=$other->getAmountArray_by_job();
	//$inspection_cost=$other_costing_arr[$row[csf('po_id')]]['inspection'];
	if ($operation==1)
	{
	$sql_previ= "select a.currency_id,b.id,b.booking_mst_id,b.booking_no,b.job_no,b.inspection_for_id,b.net_amount,b.rate,b.amount,b.discount_per,b.discount_amount,(b.amount-b.discount_amount) as tot_amt,b.vat_per,b.vat_amount,b.remark from wo_inspection_dtls b,wo_inspection_mst a where  a.id=b.booking_mst_id  and b.job_id in($job_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $updateCond ";
		$sql_previ_res=sql_select($sql_previ);
		foreach($sql_previ_res as $row)
		{
			$Previ_InspectArr_arr[$row[csf('job_no')]][$row[csf('currency_id')]]['prev_net_amount']+=$row[csf('net_amount')];
		}
	}

	$sql_po="select  a.job_no,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.style_description,a.id as job_id,(b.po_quantity) as po_quantity,d.costing_per_id,d.inspection,c.exchange_rate from wo_po_details_master a,wo_po_break_down b,wo_pre_cost_mst c,wo_pre_cost_dtls d where a.id = b.job_id AND a.id = c.job_id AND c.job_id = d.job_id AND a.status_active = 1  and a.id in($job_id) AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0";
		  // echo $sql;
	  $job_wise_data=sql_select($sql_po);
	  foreach($job_wise_data as $row)
	  {
	  	$job_wise_arr[$row[csf('job_no')]]['ex_rate']=$row[csf('exchange_rate')];
	  }
	if ($operation==0 || $operation==1)
	{

		for ($i=1; $i<=$rownum; $i++){
		$txt_job_id = "txt_job_id_".$i;
		$txt_job_no = "txt_job_no_".$i;	
		$update_dtls_id = "update_dtls_id_".$i;
		$txt_insp_val_without_vat = "txt_insp_val_without_vat_".$i;
		$job_id=str_replace("'","",$$txt_job_id);
		$job_no=str_replace("'","",$$txt_job_no);
		$insp_val_without_vat=str_replace("'","",$$txt_insp_val_without_vat);
		$job_idArr[$job_id]=$job_id;
		$inspection_cost=$other_costing_arr[$job_no]['inspection'];
		// echo $inspection_cost.'D';
		// echo "14**Inspection Bill Wo Amount($insp_val_without_vat) is greater than Pre Cost($tot_inspection_cost) not allowed.**Previ=".$inspection_cost.'='.$sql_previ;
		// 		disconnect($con);
		// 		die;
			if($cbo_currency==1) //Tk
			{
				$prev_net_amount=$Previ_InspectArr_arr[$job_no][$cbo_currency]['prev_net_amount'];
				$ex_rate=$job_wise_arr[$job_no]['ex_rate'];	
				$tot_inspection_cost=$inspection_cost*$ex_rate;
				$insp_val_without_vat_chk=$insp_val_without_vat+$prev_net_amount;
			}
			else{
				$prev_net_amount=$Previ_InspectArr_arr[$job_no][$cbo_currency]['prev_net_amount'];
				$tot_inspection_cost=$inspection_cost;
				$insp_val_without_vat_chk=$insp_val_without_vat+$prev_net_amount;

			}
			 
			if($insp_val_without_vat_chk>$tot_inspection_cost)
			{
				echo "14**Inspection Bill Wo Amount($insp_val_without_vat) is greater than Pre Cost($tot_inspection_cost) not allowed.**Previ=".$prev_net_amount;
				disconnect($con);
				die;
			}

			
		} //Loop end
		

	}
//echo "10**=A";die;
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}

		//===================================Master Part==========================================================
		$id=return_next_id("id", "wo_inspection_mst", 1);
		$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'IBW', date("Y"), 5, "select booking_prefix, booking_prefix_num from   wo_inspection_mst where company_id=$cbo_company_name and entry_form=605 and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id DESC ", "booking_prefix", "booking_prefix_num",""));
	


		$field_array="id, booking_prefix, booking_prefix_num, booking_no, entry_form, company_id, supplier_id, booking_date, currency_id, exchange_rate, pay_mode, attention, cbo_level, remarks, job_id_breakdown, inserted_by, insert_date,is_deleted,status_active";
		
		$data_array="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',605,".$cbo_company_name.",".$cbo_supplier.",".$txt_workorder_date.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$txt_attention.",".$cbo_level.",".$txt_remarks.",".$txt_job_id_breakdown.",'".$user_id."','".$pc_date_time."',0,1)";

		$booking_no=str_replace("'",'',$new_sys_number[0]);
		$rID=sql_insert("wo_inspection_mst",$field_array,$data_array,0);


		$id1=return_next_id("id", "wo_inspection_dtls", 1);
		//===================================Dtls Part==========================================================
		$field_array2="id, booking_mst_id, booking_no, entry_form, job_id, job_no, po_breakdown_id, po_number, buyer_id, style_ref, style_desc, inspection_for_id, wo_qnty, rate, amount, discount_per,discount_amount,vat_per,vat_amount,net_amount,remark, inserted_by, insert_date,is_deleted,status_active";
		
		for ($i=1; $i<=$rownum; $i++){
			 
			
					$txt_job_no = "txt_job_no_".$i;	
					$txt_job_id = "txt_job_id_".$i;
					$txt_order_no = "txt_order_no_".$i;	
					$txt_order_id = "txt_order_id_".$i;
					$cbo_buyer_id = "cbo_buyer_id_".$i;
					$txt_style_ref = "txt_style_ref_".$i;
					$txt_style_desc = "txt_style_desc_".$i;
					$cbo_inspection_id = "cbo_inspection_id_".$i;
					$txt_inspection_qnty = "txt_inspection_qnty_".$i;
					$txt_inspection_rate = "txt_inspection_rate_".$i;
					$txt_amount = "txt_amount_".$i;
					$txt_discount = "txt_discount_".$i;
					$txt_discount_value = "txt_discount_value_".$i;
					
					$txt_vat = "txt_vat_".$i;
					$txt_vat_amount = "txt_vat_amount_".$i;
					$txt_insp_val_without_vat = "txt_insp_val_without_vat_".$i;
					$txt_remarks = "txt_remarks_".$i;
					$update_dtls_id = "update_dtls_id_".$i;
					
					//  echo $txt_job_no."==>".$txt_job_no_.$i."==>".$$txt_job_no."<br>";	

					if($i>1) $data_array2 .= ",";
					$data_array2 .= "(" . $id1 . "," . $id . ",'" . $booking_no . "',605," . $$txt_job_id . "," . $$txt_job_no . "," . $$txt_order_id . "," . $$txt_order_no . "," . $$cbo_buyer_id . "," . $$txt_style_ref . "," . $$txt_style_desc. "," . $$cbo_inspection_id . "," . $$txt_inspection_qnty . "," . $$txt_inspection_rate . "," . $$txt_amount . "," . $$txt_discount . "," . $$txt_discount_value . "," . $$txt_vat . "," . $$txt_vat_amount . "," . $$txt_insp_val_without_vat . "," . $$txt_remarks . "," . $_SESSION['logic_erp']['user_id']. ",'" . $pc_date_time."',0,1)";
					$id1 = $id1 + 1;
		}
	 //echo "10** insert into wo_inspection_dtls (".$field_array2.") values ".$data_array2;die;
		$rID1=sql_insert("wo_inspection_dtls",$field_array2,$data_array2,0);

			// echo "10**".$rID."===>".$rID1;die;
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$booking_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$booking_no)."**".str_replace("'",'',$id);
			}
		}
		else if($db_type==1 || $db_type==2)
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$booking_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$booking_no)."**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
		$con = connect();
		$txt_booking_no=$txt_workorder_no;
		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		if( str_replace("'","",$update_id) == "")
		{
			echo "15"; disconnect($con);exit();
		}

		if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}


		$field_array="supplier_id*booking_date*currency_id*exchange_rate*pay_mode*attention*cbo_level*remarks*job_id_breakdown*updated_by*update_date";
		$data_array="".$cbo_supplier."*".$txt_workorder_date."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$txt_attention."*".$cbo_level."*".$txt_remarks."*".$txt_job_id_breakdown."*'".$user_id."'*'".$pc_date_time."'";


	   //=======================================================dtls part=====================================================================
	   $id1=return_next_id("id", "wo_inspection_dtls", 1);
	   $sql_query=execute_query("delete from wo_inspection_dtls where mst_id=$update_id and  dtls_id=$update_dtls_id");
	   $field_array2="id, booking_mst_id, booking_no, entry_form, job_id, job_no, po_breakdown_id, po_number, buyer_id, style_ref, style_desc, inspection_for_id, wo_qnty, rate, amount, discount_per,discount_amount,vat_per,vat_amount,net_amount,remark, inserted_by, insert_date";


	    $field_array_up="inspection_for_id*wo_qnty*rate*amount*discount_per*discount_amount*vat_per*vat_amount*net_amount*remark*updated_by*update_date";
		$id_arr=array();
		
		$rownum=str_replace("'","",$txtRowId);
		
	   for ($i=1; $i <=$rownum; $i++){
				$txt_job_no = "txt_job_no_".$i;	
				$txt_job_id = "txt_job_id_".$i;
				$txt_order_no = "txt_order_no_".$i;	
				$txt_order_id = "txt_order_id_".$i;
				$cbo_buyer_id = "cbo_buyer_id_".$i;
				$txt_style_ref = "txt_style_ref_".$i;
				$txt_style_desc = "txt_style_desc_".$i;
				$cbo_inspection_id = "cbo_inspection_id_".$i;
				$txt_insp_qnty = "txt_inspection_qnty_".$i;
				$txt_insp_rate = "txt_inspection_rate_".$i;
				$txt_amount = "txt_amount_".$i;
				$txt_discount = "txt_discount_".$i;
				$txt_discount_value = "txt_discount_value_".$i;
				
				$txt_vat = "txt_vat_".$i;
				$txt_vat_amount = "txt_vat_amount_".$i;
				$txt_insp_val_without_vat = "txt_insp_val_without_vat_".$i;
				$txt_remarks = "txt_remarks_".$i;
				$update_dtls_id = "update_dtls_id_".$i;
				  
				if(str_replace("'",'',$$update_dtls_id)=="")  
				{ 
					if ($add_comma!=0) $data_array2 .=",";
					$data_array2 .= "(" . $id1 . "," . $update_id . "," . $txt_workorder_no . ",605," . $$txt_job_id . "," . $$txt_job_no . "," . $$txt_order_id . "," . $$txt_order_no . "," . $$cbo_buyer_id . "," . $$txt_style_ref . "," . $$txt_style_desc. "," . $$cbo_inspection_id . "," . $$txt_insp_qnty . "," . $$txt_insp_rate . "," . $$txt_amount . "," . $$txt_discount . "," . $$txt_discount_value . "," . $$txt_vat . "," . $$txt_vat_amount . "," . $$txt_insp_val_without_vat . "," . $$txt_remarks . "," . $_SESSION['logic_erp']['user_id']. ",'" . $pc_date_time."')";
					$id1 = $id1 + 1;
					$add_comma++;
				
				}
				else
				{
					
					$id_arr[]=str_replace("'",'',$$update_dtls_id);
					$data_array_up[str_replace("'",'',$$update_dtls_id)] =explode("*",("".$$cbo_inspection_id."*".$$txt_insp_qnty."*".$$txt_insp_rate."*".$$txt_amount."*".$$txt_discount."*".$$txt_discount_value."*".$$txt_vat."*".$$txt_vat_amount."*".$$txt_insp_val_without_vat."*".$$txt_remarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}

		}



		 //echo "10** insert into wo_inspection_dtls (".$field_array2.") values ".$data_array2;die;
		$rID=sql_update("wo_inspection_mst",$field_array,$data_array,"id",$update_id,1);	
		if($data_array_up!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("wo_inspection_dtls", "id",$field_array_up,$data_array_up,$id_arr ),1);
		}	
		
		$rID2=sql_insert("wo_inspection_dtls",$field_array2,$data_array2,0);
		$return_no=str_replace("'",'',$txt_workorder_no);
		check_table_status( $_SESSION['menu_id'],0);
		if($rID)
		{
			oci_commit($con);
			echo "1**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
		}
		else
		{
			oci_rollback($con);
			echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
		}
		
		disconnect($con);
		die;
 	}

	else if ($operation==2)
	{
		$con = connect();
		$txt_booking_no=$txt_workorder_no;
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$update_id=str_replace("'","",$update_id);
		if($update_id=="" || $update_id==0){ echo "15**0"; die;}
		$rownum=str_replace("'","",$txtRowId);
		
	   for ($i=1; $i <=$rownum; $i++){
		$update_dtls_id = "update_dtls_id_".$i;
		$dtlsrID=execute_query( "update wo_inspection_dtls set status_active=0,is_deleted=1 where  id in(".str_replace("'","",$$update_dtls_id).") and booking_mst_id=$update_id",0);
	   }
		$return_no=str_replace("'",'',$txt_workorder_no);
		if($db_type==0 )
		{
			if($dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".$return_no."**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($dtlsrID)
			{
				oci_commit($con);
				echo "2**".$return_no."**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

 

if ($action=="load_dtls_data_view")
{

	$sql_level="select a.cbo_level from wo_inspection_mst a where  a.id=$data and a.is_deleted = 0 and a.status_active=1";
	$level_data_array=sql_select($sql_level);
	foreach ($level_data_array as $lrow)
	{
		$cbo_lvl=$lrow[csf('cbo_level')];
	}
	$arr=array (1=>$inspection_for);
	$sql= "select id,booking_mst_id,booking_no,job_no,inspection_for_id,wo_qnty,rate,amount,discount_per,discount_amount,(amount-discount_amount) as tot_amt,vat_per,vat_amount,net_amount,remark,po_number from wo_inspection_dtls where booking_mst_id=$data and is_deleted = 0 and status_active=1 ";
 
 ?>
  <div style="width:1000px;">
  <legend>List View</legend>
        <table class="rpt_table" width="1000" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_list_search">
            <thead>
                <tr>
			    	<th>SL No</th>
                	<th>Job No</th>
					<? if($cbo_lvl==1){?>
					<th>Order No</th>
					<?}?>
                    <th>Insfection For</th>
                    <th>Insp. Qty</th>
                    <th>Insp. Rate</th>
                    <th>Amount(USD)</span></th>
                    <th>Discount %</th>
                    <th>Discount(USD)</span></th>
                    <th>Total Insp. Value(USD)</span></th>
                    <th>Vat %</th>
					<th>Vat Amount(USD)</span></th>
                    <th>Net Insp. Value(USD)</span></th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody >
				<?

	
	 //echo $sql;die;
	 $data_array=sql_select($sql);
	 $i=1;
	 foreach ($data_array as $row)
	 {
	
				 ?>
			 <tr onClick="show_list_view(<?=$row[csf('id')];?>,'load_php_dtls_data_to_form','booking_list_view_list','requires/inspection_bill_work_order_entry_controller','')" style="cursor:pointer" id="tr_1">
			    <td><?=$i;?></td>	
			    <td><?=$row[csf('job_no')];?></td>	
				<? if($cbo_lvl==1){?>
				<td><?=$row[csf('po_number')];?></td>	
				<?}?>		
				<td><?=$inspection_for[$row[csf('inspection_for_id')]];?></td>
				<td><?=$row[csf('wo_qnty')];?></td>
				<td><?=$row[csf('rate')];?></td>
				<td><?=$row[csf('amount')];?></td>
				<td><?=$row[csf('discount_per')];?></td>
				<td><?=$row[csf('discount_amount')];?></td>
				<td><?=$row[csf('tot_amt')];?></td>
				<td><?=$row[csf('vat_per')];?></td>
				<td><?=$row[csf('vat_amount')];?></td>
				<td><?=$row[csf('net_amount')];?></td>
				<td><?=$row[csf('remark')];?></td>

			</tr>
			<?
			
		$i++;
	 }
	 ?>
	    
	  </tbody>
 </table>

 </div><?
}

if ($action=="workorder_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
 ?>
 <script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	function js_set_value(id)
	{
		document.getElementById('selected_booking').value=id;
		parent.emailwindow.hide();
	}
 </script>
 </head>

 <body>
 <div align="center" style="width:100%;" >
 <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>
                           <th colspan="2"> </th>
                        	<th  >
                              <?
                               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                            <th colspan="3"></th>
                    </thead>
                    <thead>
                        <th width="150">Company Name</th>
                        <th width="150">Test Company</th>
                         <th width="100">WO No</th>
                        <th width="200">WO Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Orphan</th>
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_booking">
						<?
                        echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond
                        order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name, "");
                        ?>
                        </td>

                   	<td id="">
                      <?
                 	  echo create_drop_down( "cbo_supplier_name", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b
					  where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name","id,supplier_name", 1, "-- Select Test Company--", 0, "","" );
                ?>
                    </td>
                     <td><input name="txt_wo_prifix" id="txt_wo_prifix" class="text_boxes" style="width:100px" ></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td>
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_wo_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_wo_search_list_view', 'search_div', 'inspection_bill_work_order_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
			<?
			echo load_month_buttons(1);
			?>
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

if ($action=="create_wo_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company="and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.supplier_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0)
	{
		$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		$year_id=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."'
		and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$year_id=" to_char(a.insert_date,'YYYY') as year";
	}

	if($data[6]==4 || $data[6]==0)
	{
	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
	}
    if($data[6]==1)
	{
	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_prefix_num ='$data[5]' "; else $booking_cond="";
	}
   	if($data[6]==2)
	{
	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
	}
	if($data[6]==3)
	{
	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
	}


	$suplier=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and
	b.party_type=26 order by a.supplier_name",'id','supplier_name');
 //wo_po_break_down
	$arr=array (2=>$comp,4=>$suplier,5=>$pay_mode);
	
	//echo $data[7];
	$sql= "SELECT a.id,a.booking_no,b.job_no,to_char(a.insert_date,'YYYY') as year,a.pay_mode,a.supplier_id,a.currency_id,a.company_id,b.inspection_for_id,sum(wo_qnty) as wo_qnty,b.style_ref,a.booking_date from wo_inspection_mst a,wo_inspection_dtls b where  a.id=booking_mst_id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $booking_cond $company $buyer $booking_date group by a.id,a.booking_no,b.job_no,b.inspection_for_id,a.insert_date,b.style_ref,a.booking_date,a.pay_mode,a.supplier_id,a.currency_id,a.company_id order by a.id desc";
	echo  create_list_view("list_view", "Booking No,Year,Job No,Style Ref,Insp Companys,Pay Mode,Wo Date,WO Qnty", "100,60,100,120,120,70,100,100","800","300",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,0,0,supplier_id,pay_mode,0,0", $arr , "booking_no,year,job_no,style_ref,supplier_id,pay_mode,booking_date,wo_qnty", '','','0,0,0,1,1,2,3,0,0','','');
}


if ($action=="load_php_mst_data")
{

	 $data=explode("_",$data);
	 $sql= "select id, company_id, supplier_id, booking_date,currency_id, exchange_rate, pay_mode, attention, cbo_level, remarks from wo_inspection_mst where id='$data[0]' and entry_form=605";
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_workorder_date').value = '".change_date_format($row[csf("booking_date")])."';\n";
		echo "document.getElementById('cbo_supplier').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('cbo_level').value = '".$row[csf("cbo_level")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled',true);";
		echo "$('#cbo_supplier').attr('disabled',true);";
		echo "func_currency('".$row[csf("currency_id")]."')";
	 }
}


if($action=="load_php_dtls_data_to_form")
{
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$sql_level="select a.cbo_level from wo_inspection_mst a,wo_inspection_dtls b where a.id=b.booking_mst_id and b.id=$data and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1";
	$level_data_array=sql_select($sql_level);
	foreach ($level_data_array as $lrow)
	{
		$cbo_lvl=$lrow[csf('cbo_level')];
	}
	 $sql="select id,booking_mst_id,booking_no,job_no,job_id,buyer_id,inspection_for_id,wo_qnty,rate,amount,discount_per,discount_amount,(amount-discount_amount) as tot_amt,vat_per,vat_amount,net_amount,remark,style_desc,style_ref,po_number,po_breakdown_id from wo_inspection_dtls where  id=$data and is_deleted = 0 and status_active=1 ";
	 //echo $sql;die;
	 $data_array=sql_select($sql);
	 $i=1;
	 foreach ($data_array as $row)
	 {
		$buyer_id=$row[csf('buyer_id')];
				 ?>
			 <tr>
				<td>
					<input style="width:90px;" type="text" class="text_boxes"  name="txt_job_no_<?=$i;?>" id="txt_job_no_<?=$i;?>"   value="<?=$row[csf('job_no')];?>" readonly /><!--openmypage()-->
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_job_id_<?=$i;?>" id="txt_job_id_<?=$i;?>"  value="<?=$row[csf('job_id')];?>"   />
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_no_<?=$i;?>" id="txt_order_no_<?=$i;?>"  value="<?=$row[csf('po_number')];?>"   />
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id_<?=$i;?>" id="txt_order_id_<?=$i;?>"  value="<?=$row[csf('po_breakdown_id')];?>"   />
				</td>
				
				<? if($cbo_lvl==1){?>
				<td>
					<input style="width:90px;" type="text" class="text_boxes"  name="txt_order_no_<?=$i;?>" id="txt_order_no_<?=$i;?>"   value="<?=$row[csf('po_number')];?>" readonly /><!--openmypage()-->
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id_<?=$i;?>" id="txt_order_id_<?=$i;?>"  value="<?=$row[csf('po_breakdown_id')];?>"   />
				</td>
				<?}?>
				
				<td>
				<input style="width:90px;" type="text" class="text_boxes"  name="txt_buyer_name_<?=$i;?>" id="txt_buyer_name_<?=$i;?>"   value="<?=$buyer_arr[$row[csf('buyer_id')]];?>" readonly/>
				<input style="width:90px;" type="hidden" class="text_boxes"  name="cbo_buyer_id_<?=$i;?>" id="cbo_buyer_id_<?=$i;?>"  value="<?=$row[csf('buyer_id')];?>"  readonly/>
					 
				 </td>
				<td>
					<input class="text_boxes" type="text" style="width:90px" name="txt_style_ref_<?=$i;?>" id="txt_style_ref_<?=$i;?>" value="<?=$row[csf('style_ref')];?>"readonly/>					
				</td>
				<td><input class="text_boxes" type="text" style="width:160px" name="txt_style_desc_<?=$i;?>" id="txt_style_desc_<?=$i;?>" value="<?=$row[csf('style_desc')];?>" /> </td>
				<td><? echo create_drop_down( "cbo_inspection_id_$i", 90, $inspection_for, 0, 1, "Select Inspection",$row[csf('inspection_for_id')], "", "", "" );?></td>
				 <td>
					<input class="text_boxes" type="text" style="width:120px" name="txt_inspection_qnty_<?=$i;?>" id="txt_inspection_qnty_<?=$i;?>" value="<?=$row[csf('wo_qnty')];?>" onBlur="calculate_wo_value(<?=$i;?>,'update_qnty')"/>
					<input type="hidden" id="update_dtls_id_<?=$i;?>" name="update_dtls_id_<?=$i;?>" value="<?=$row[csf('id')];?>">
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:80px" name="txt_inspection_rate_<?=$i;?>" id="txt_inspection_rate_<?=$i;?>" value="<?=$row[csf('rate')];?>" onBlur="calculate_wo_value(<?=$i;?>,'rate')"/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:70px" name="txt_amount_<?=$i;?>" id="txt_amount_<?=$i;?>"  value="<?=$row[csf('amount')];?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:70px" name="txt_discount_<?=$i;?>" id="txt_discount_<?=$i;?>" onBlur="calculate_wo_value(<?=$i;?>,'discount')" value="<?=$row[csf('discount_per')];?>"/>
				 </td>
				  <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_discount_value_<?=$i;?>" id="txt_discount_value_<?=$i;?>" value="<?=$row[csf('discount_amount')];?>" onBlur="calculate_wo_value(<?=$i;?>,'dis_amt')"/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_insp_val_with_vat_<?=$i;?>" id="txt_insp_val_with_vat_<?=$i;?>" value="<?=$row[csf('tot_amt')];?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_<?=$i;?>" id="txt_vat_<?=$i;?>" value="<?=$row[csf('vat_per')];?>" onBlur="calculate_wo_value(<?=$i;?>,'vat')" />
				 </td>
				   <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_amount_<?=$i;?>" id="txt_vat_amount_<?=$i;?>" value="<?=$row[csf('vat_amount')];?>" onBlur="calculate_wo_value(1,'vat_amt') "/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_insp_val_without_vat_<?=$i;?>" id="txt_insp_val_without_vat_<?=$i;?>" value="<?=$row[csf('net_amount')];?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes" type="text" style="width:100px" name="txt_remarks_<?=$i;?>" id="txt_remarks_<?=$i;?>" value="<?=$row[csf('remark')];?>"  />
					<input class="text_boxes_numeric" type="hidden" style="width:100px" name="txtRowId" id="txtRowId" value="<?=count($data_array);?>"  />
				 </td>

			</tr>
			<?
			$jobIdArr[$row[csf('job_id')]]=$row[csf('job_id')];
		$i++;
	 }?>
	 <tr>
				<td>
					<input style="width:90px;" type="text" class="text_boxes"  name="txt_job_no_<?=$i;?>" id="txt_job_no_<?=$i;?>"
					 onDblClick="openmypage_wovalue('<?=implode(',',$jobIdArr);?>',2)" placeholder="Double Click" readonly/><!--openmypage()-->
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_job_id_<?=$i;?>" id="txt_job_id_<?=$i;?>"   readonly/>
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_no_<?=$i;?>" id="txt_order_no_<?=$i;?>"   readonly/>
					<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id_<?=$i;?>" id="txt_order_id_<?=$i;?>"   readonly/>
				</td>
					<? if($cbo_lvl==1){?>
					<td>
						<input style="width:90px;" type="text" class="text_boxes"  name="txt_order_no_<?=$i;?>" id="txt_order_no_<?=$i;?>"  readonly /><!--openmypage()-->
						<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id_<?=$i;?>" id="txt_order_id_<?=$i;?>"   />
					</td>
					<?}?>
				<td><input style="width:90px;" type="text" class="text_boxes"  name="txt_buyer_name_<?=$i;?>" id="txt_buyer_name_<?=$i;?>"   readonly/>
				<input style="width:90px;" type="hidden" class="text_boxes"  name="cbo_buyer_id_<?=$i;?>" id="cbo_buyer_id_<?=$i;?>"  value=""  readonly/></td>
				<td>
					<input class="text_boxes" type="text" style="width:90px" name="txt_style_ref_<?=$i;?>" id="txt_style_ref_<?=$i;?>"/>					
				</td>
				<td><input class="text_boxes" type="text" style="width:160px" name="txt_style_desc_<?=$i;?>" id="txt_style_desc_<?=$i;?>"/> </td>
				<td><? echo create_drop_down( "cbo_inspection_id_<?=$i;?>", 90, $test_for, 0, 1, "Select Inspection",$selected, "", "", "" );?></td>
				 <td>
					<input class="text_boxes" type="text" style="width:120px" name="txt_inspection_qnty_<?=$i;?>" id="txt_inspection_qnty_<?=$i;?>" />
					<input type="hidden" id="update_dtls_id_<?=$i;?>" name="update_dtls_id_<?=$i;?>">
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:80px" name="txt_inspection_rate_<?=$i;?>" id="txt_inspection_rate_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:70px" name="txt_amount_<?=$i;?>" id="txt_amount_<?=$i;?>" onBlur="calculate_wo_value()"/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:70px" name="txt_discount_<?=$i;?>" id="txt_discount_<?=$i;?>" onBlur="calculate_wo_value()"/>
				 </td>
				  <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_discount_value_<?=$i;?>" id="txt_discount_value_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_insp_val_with_vat_<?=$i;?>" id="txt_insp_val_with_vat_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_<?=$i;?>" id="txt_vat_<?=$i;?>" readonly/>
				 </td>
				   <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_amount_<?=$i;?>" id="txt_vat_amount_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes_numeric" type="text" style="width:100px" name="txt_insp_val_without_vat_<?=$i;?>" id="txt_insp_val_without_vat_<?=$i;?>" readonly/>
				 </td>
				 <td>
					<input class="text_boxes" type="text" style="width:100px" name="txt_remarks_<?=$i;?>" id="txt_remarks_<?=$i;?>" readonly/>
				 </td>

			</tr>
	 <?

}

if($action=="show_trim_booking_report_new")
{
    extract($_REQUEST);

	echo load_html_head_contents("Lab Test Work Order", "../../../", 1, 1,'','','');
	$data=explode('*',str_replace("'","",$data));
 
	$sql="select id,booking_no, company_id, supplier_id, booking_date,currency_id, exchange_rate, pay_mode, attention, remarks from wo_inspection_mst where id='$data[1]' and  company_id='$data[0]' and entry_form=605";
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	

	$supplier_library=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id
	and b.party_type=26 order by a.supplier_name","id","supplier_name"  );
	$total_charge=0;
 
    $sql_dtls="select id,booking_mst_id,booking_no,job_no,buyer_id,inspection_for_id,wo_qnty,rate,amount,discount_per,discount_amount,(amount-discount_amount) as tot_amt,vat_per,vat_amount,net_amount,remark,style_desc from wo_inspection_dtls where  booking_mst_id=$data[1] and is_deleted = 0 and status_active=1 ";
	$sql_result= sql_select($sql_dtls);
 
 


 ?>
 <div style="width:930px;" align="center">
    <table width="900" cellspacing="0" align="center" style=" margin:0px 0px 0px 40px;table-layout: fixed;">
        <tr>
             <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="2" rowspan="2" align="left" style="font-size:14px">
            <?
			$company_logo=sql_select( "select b.image_location from common_photo_library b where b.master_tble_id='$data[0]' and b.form_name='company_details'");
			?>
            <img src="../../<?  echo $company_logo[0][csf('image_location')]; ?>"  width="100p"  height="70" >
            </td>
        	<td colspan="4" rowspan="2" align="left" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
						 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
						 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
						 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
						 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
						 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
						 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
						 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
						 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];

					}
                ?>
            </td>
            <td colspan="2" rowspan="2" id="barcode_img_id" width="250">

            </td>

        </tr>
        <tr>

        </tr>
        <tr>
            <td colspan="6" align="center"><strong><u style="font-size: 18px;font-weight: bold;">Inspection Work Order</u></strong></td>
        </tr>
    </table>
    <br/> 	
    <table cellspacing="0" width="900" align="left" border="1" rules="all" class="rpt_table" style=" margin:0px 0px 10px 40px;table-layout: fixed;" >
        <tr>
        	<td width="100"><strong>Wo No :</strong></td><td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
            <td width="150"><strong>Insp Company:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
			<td width="120"><strong>WO Date:</strong></td> <td width="120px"><? echo change_date_format($dataArray[0][csf('booking_date')]); ?></td>
        </tr>
        <tr>
            <td width="110"><strong>Currency:</strong></td> <td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            <td width="115"><strong>Exchange Rate:</strong></td><td width="175px"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
			<td><strong>Delivery Date:</strong></td> <td width="120px"> </td>

        </tr>
        <tr>
            <td><strong>Pay Mode:</strong></td> <td width="175px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            <td><strong>Attention:</strong></td><td width="175px"><? echo $dataArray[0][csf('attention')]; ?></td>

        </tr>
        <tr>
            <td><strong>Remarks:</strong></td> <td  colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>
            

        </tr>
    </table>

    </table>
        <br/> 



 <?

	$i=1; $j=1;
	$all_job_no='';
	$sl_arr=array();
	$cbo_currency=$data[2];
	$txt_workorder_date=$data[3];
	if($db_type==2) { $txt_workorder_date=change_date_format($txt_workorder_date,'yyyy-mm-dd',"-",1);}
	else { $txt_workorder_date=change_date_format($txt_workorder_date,'yyyy-mm-dd');}
	$current_currency=set_conversion_rate($cbo_currency,$txt_workorder_date);
	$grand_wo_value=0;
	foreach($sql_result as $row)
	{

			if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$test_item=explode(",",$row[csf('test_item_id')]);
			$test_item_value=explode(",",$row[csf('test_item_value')]);
			//echo $row[csf('test_item_value')];
			$colum_span=count(explode(",",$row[csf('test_item_id')]));
			$colum_span=$colum_span+7;
			if(trim($all_job_no)!='') $all_job_no.=",'".$row[csf("job_no")]."'";
			else $all_job_no="'".$row[csf("job_no")]."'";
			$total_net_reate=0;

			//print_r($test_item_value);
			$index=0;
			foreach($test_item as $name)
			{

				if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				
				if(!in_array($i,$main_row))
				{
				$main_row[]=$i; //style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"
				if(!in_array($row[csf("job_no")],$date_array))
				{
					
		         ?>
        		 <table  cellspacing="0" width="900"  border="1" rules="all" class=""  align="left" style=" margin:0px 0px 0px 40px;table-layout: fixed;">
		         <thead bgcolor="#dddddd" >
		         <tr>
		         <th width="800" align="left" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; text-align:left; font-weight: 900;" colspan="5">Job No:<? echo $row[csf("job_no")]; ?>,Style: <?  echo $style_ref_no_library[$row[csf("job_no")]] ;?></th>
		         </tr>
		             <tr>
		             		<th width="40">SL</th>
		                    <th width="80">Inspection For</th>
		                    <th width="120">Remarks</th>		                   
		                    <th width="220">Test Item</th>
		                    <th width="">Amount</th>
		           </tr>
                        <? }
						$date_array[]=$row[csf("job_no")];

						?>
		        </thead>

		        <tbody >
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center" style="font-size:15px" width="40" rowspan="<? echo $colum_span?>"><? echo $i; ?></td>

                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $inspection_for[$row[csf("inspection_for_id")]]; ?></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $row[csf("remark")]; ?></td>
   
                </tr>
		   <?
				}
				 
				$index++;
			}
			?>
			<tr>
                <td align="right" style="font-size:15px" >Order Qty</td>
                <td align="right" style="font-size:15px"><b><? echo number_format($row[csf("wo_qnty")],4); ?></b></td>
			</tr>
			<tr>
                <td align="right" style="font-size:15px" >Rate</td>
                <td align="right" style="font-size:15px"><b><? echo number_format($row[csf("rate")],4); ?></b></td>
			</tr>
             <tr bgcolor="#E3E3E3">
                <td align="right" style="font-size:15px" ><b>Amount</b></td>
                <td align="right" style="font-size:15px"><b><? echo number_format($row[csf("amount")],4); ?></b></td>
			</tr>
            <tr>
                <td align="right" style="font-size:15px" >Less Discount</td>
                <td align="right" style="font-size:15px"><? echo number_format($row[csf("discount_amount")],4) ; ?></td>
			</tr>
              <tr>
                <td align="right" style="font-size:15px">Vat @ 2% </td>
                <td align="right" style="font-size:15px"><? echo number_format($row[csf("vat_per")],4) ; ?></td>
			</tr>
			
            </tr>
              <tr bgcolor="#E3E3E3">
                <td align="right" style="font-size:15px" ><b>WO Value</</td>
                <td align="right" style="font-size:15px"><b>
				<?
				 $toatal_wo_value=$row[csf("net_amount")];
				 $grand_wo_value+=$toatal_wo_value;
				 echo number_format(($toatal_wo_value),4) ;
				  ?></b></td>
			</tr>
            <?

        $i++;
        }
        ?>
        	<tr bgcolor="#B7B7B7">
                <td align="right" style="font-size:15px" ><b>Grand Total</</td>
                <td align="right" style="font-size:15px"><b><? echo number_format(($grand_wo_value),4) ; ?></b></td>
			</tr>

        </tbody>

      </table>
		<div align="center" style="float:left; margin-left:100px; font-size:18px; ">
        <?
	   $mcurrency="";
	   $dcurrency="";
	   if($cbo_currency==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	   }
	   if($cbo_currency==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS';
	   }
	   if($cbo_currency==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS';
	   }
	   ?>
            <b>Total Amount (in word):   <? echo number_to_words(def_number_format($grand_wo_value,2,""),$mcurrency, $dcurrency);?></b>
        </div>
   
		 <?
            echo signature_table(80, $data[0], "900px");
         ?>
   </div>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
     <?
	 exit();
}
?>