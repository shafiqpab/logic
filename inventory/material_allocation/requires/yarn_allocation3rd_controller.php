<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select--", "", "" );   	 
}


if ($action=="fabric_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>
     
	<script>
	 
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" align="center">
            <thead>                	 
                <th>Company Name</th>
                <th>Buyer Name</th>
                <th>Booking No</th>
                <th>Internal Ref</th>
                <th>File No</th>
                <th>Date Range</th>
                <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /></th>           
            </thead>
            <tr class="general">
                <td> <input type="hidden" id="selected_booking">
                    <? 
                        echo create_drop_down( "cbo_company_mst", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'yarn_allocation3rd_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                    ?>
                </td>
                <td id="buyer_td"> <? echo create_drop_down( "cbo_buyer_name", 170, $blank_array,"", 1, "--Select--" ); ?></td>
                <td>				
                    <input type="text" style="width:100px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                </td> 
                 <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
                 <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>  
                <td>
                	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
                  	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                 </td> 
                 <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_booking_search_list_view', 'search_div', 'yarn_allocation3rd_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
         </table>
         <table>
            <tr>
                <td align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
            </tr>
		</table> 
        <div id="search_div" style="margin-top:5px"></div>   
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer="";
		}
	}
	else
	{
		$buyer=" and a.buyer_id='$data[1]'";
		
	}
	
	if(trim($data[4])=="") $search_field_cond=""; else $search_field_cond=" and a.booking_no like '%".trim($data[4])."'";
	$internal_ref = trim($data[5]);
	$file_no = trim($data[6]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	//echo $file_no_cond.'='.$internal_ref_cond;
	
	if ($data[2]!="" &&  $data[3]!="")
	{
		if($db_type==0)
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[2],'','',1)."' and '".change_date_format($data[3],'','',1)."'";
		}
	}
	else $booking_date ="";
	
	$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );

	$po_array=array();
	$sql_po=sql_select ("select a.id,a.booking_no,a.po_break_down_id from wo_booking_mst a where $company $buyer $booking_date and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$po_number[$value].",";
		}
		
		$po_array[$row[csf("id")]]=rtrim($po_number_string,",");
	}
	 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$job_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
	$arr=array (3=>$comp,4=>$buyer_arr,5=>$job_num,6=>$po_array,7=>$item_category,8=>$fabric_source,9=>$suplier);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	 $sql= "select a.booking_no_prefix_num, b.grouping, b.file_no, $year_field a.booking_no, a.booking_date, a.booking_type, a.is_short, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id from wo_booking_mst a,wo_po_break_down b where a.job_no=b.job_no_mst and $company $buyer $booking_date $search_field_cond and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $file_no_cond $internal_ref_cond group by a.booking_no_prefix_num,a.insert_date,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.booking_type, a.is_short, a.po_break_down_id, a.id, a.item_category, a.fabric_source, a.supplier_id, b.grouping, b.file_no order by a.booking_no"; 
	//  echo $sql; die;

	//echo create_list_view("list_view", "Year,Booking No,Booking Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Supplier,Internal Ref,File No", "50,70,80,60,60,60,180,110,80,80,80","1060","280",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,0,company_id,buyer_id,job_no,id,item_category,fabric_source,supplier_id,0,0", $arr , "year,booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,id,item_category,fabric_source,supplier_id,grouping,file_no", '','','0,0,3,0,0,0,0,0,0,0,0,0','','');
	
	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table">
			<thead>
				<th width="40">SL</th>
                <th width="50">Year</th>
                <th width="70">Booking No</th>
                <th width="60">Type</th>
				<th width="80">Booking Date</th>
				<th width="60">Buyer</th>
                <th width="60">Job No.</th>
                <th width="180">PO number</th>
				<th width="110">Fabric Nature</th>
                <th width="80">Fabric Source</th>
                <th width="80">Supplier</th>
                <th width="80">Internal Ref</th>
				<th>File No</th>
			</thead>
		</table>
		<div style="width:1060px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040" class="rpt_table" id="list_view">
            <?
				$i=1;
				$result = sql_select($sql);

				// for checking Reference Closing

				$sql_2 = "SELECT DISTINCT INV_PUR_REQ_MST_ID, CLOSING_STATUS FROM INV_REFERENCE_CLOSING WHERE     REFERENCE_TYPE = 108 AND status_active = 1 AND is_deleted = 0 AND insert_date IN (  SELECT MAX (insert_date) FROM INV_REFERENCE_CLOSING WHERE     REFERENCE_TYPE = 108 AND status_active = 1 AND is_deleted = 0 GROUP BY INV_PUR_REQ_MST_ID)";
				// echo $sql_2;
					
				$result_2 = sql_select($sql_2);

				foreach($result as $key=> $row)
				{
					foreach($result_2 as $val)
					{
						if(($row['ID'] == $val['INV_PUR_REQ_MST_ID']) && ($val['CLOSING_STATUS']==1)) {
							// echo $row['ID']."  ";
							unset($result[$key]);
						}
					}
				}

				foreach ($result as $row)
				{  
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	 
						
					if($row[csf('booking_type')]==4) $booking_type="Sample";
					else
					{
						if($row[csf('is_short')]==1) $booking_type="Short";
						else $booking_type="Main";
					}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>');"> 
						<td width="40"><? echo $i; ?></td>
						<td width="50"><? echo $row[csf('year')]; ?></td>
                        <td width="70"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
                        <td width="60"><p>&nbsp;<? echo $booking_type; ?></p></td> 
                        <td width="80" align="center"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>   
						<td width="60"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                        <td width="60"><p><? echo $job_num[$row[csf('job_no')]]; ?></p></td>
                        <td width="180"><p><? echo $po_array[$row[csf('id')]]; ?>&nbsp;</p></td>
						<td width="110"><p><? echo $item_category[$row[csf('item_category')]]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $fabric_source[$row[csf('fabric_source')]]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $suplier[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('file_no')]; ?>&nbsp;</p></td>
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
//new development
if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
	
?>
     
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value(job_no)
	{
		document.getElementById('selected_job').value=job_no;
		//return;
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_2"  id="searchorderfrm_2" autocomplete="off">
	<table width="1200" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        <thead>
                         <th width="150" colspan="4"> </th>
                        	<th>
                              <?
                               echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th width="150" colspan="4"> </th>
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Job No</th>
                        <th width="100">Style Ref </th>
                        <th width="100">Internal Ref</th>
                        <th width="100">File No</th>
                        <th width="120">Order No</th>
                        <th width="200">Ship Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="selected_job">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'yarn_allocation3rd_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button1" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div2', 'yarn_allocation3rd_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
            <td align="center" valign="top" id="search_div2">  </td>
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
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	
	
	if($db_type==0)
	{
	$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	$year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$order_cond="";
	$job_cond=""; 
	$style_cond="";
	if($data[7]==1)
		{
		  if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]'  $year_cond";
		  if (trim($data[8])!="") $order_cond=" and b.po_number='$data[8]'  "; //else  $order_cond=""; 
		  if (trim($data[9])!="") $style_cond=" and a.style_ref_no='$data[9]'  "; //else  $style_cond=""; 
		}
	
	if($data[7]==4 || $data[7]==0)
		{
		  if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  ";
		  if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%'  "; //else  $style_cond=""; 

		}
	
	if($data[7]==2)
		{
		  if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[8])!="") $order_cond=" and b.po_number like '$data[8]%'  ";
		  if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%'  "; //else  $style_cond=""; 

		}
	
	if($data[7]==3)
		{
		  if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[8])!="") $order_cond=" and b.po_number like '%$data[8]'  ";
		  if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]'  "; //else  $style_cond=""; 

		}
			
	$internal_ref = str_replace("'","",$data[10]);
	$file_no = str_replace("'","",$data[11]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	/*if($file_no!="" || $internal_ref!="")
	{
	$sql_po=sql_select("select b.id from  wo_po_break_down b where   b.status_active=1 and b.is_deleted=0  $file_no_cond  $internal_ref_cond");
	 $po_id_data=$sql_po[0][csf('id')];
	}
	if($po_id_data!="" || $po_id_data!=0) $po_data_cond=" and b.id='$po_id_data' "; else $po_data_cond="";*/
	
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr);
	if ($data[2]==0)
	{
	if($db_type==0)
		{
			 $sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, group_concat(distinct b.po_number) AS po_number,group_concat(distinct b.id ) AS po_id,sum(b.po_quantity) as po_quantity,b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond group by a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.file_no";
	 		//$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."   and a.is_deleted=0 $company $buyer $job_cond $style_cond  order by a.job_no";
		}
	 if($db_type==2)
		{
			 
			 
			 //$sql="SELECT a.job_no, LISTAGG(b.po_number, ',') WITHIN GROUP (ORDER BY b.po_number) AS po_number FROM   wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst GROUP BY a.job_no";
//echo  $sql;
//$group_concat="listagg(cast(b.item_description as varchar2(4000)),'**') within group (order by b.item_description) AS fabric_type,listagg(b.po_id ,',') within group (order by b.po_id) AS po_id,listagg(b.width_dia_type ,',') within group (order by b.width_dia_type) AS width_dia_type";
			 
			 //$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.po_number,b.po_quantity,b.shipment_date,b.grouping,b.file_no,(pub_shipment_date - po_received_date) as  date_diff,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.job_no";
			 
			 $sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, LISTAGG(cast(b.po_number  as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) AS po_number,listagg(b.id ,',') within group (order by b.id) AS po_id,sum(b.po_quantity) as po_quantity,b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond group by a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.file_no";
			
			
		}
		//echo $sql;
		 echo  create_list_view("list_view", "Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Qty, File No", "40,130,130,150,120,150,150","1020","320",0, $sql , "js_set_value", "job_no,po_number,po_id", "", 1, "0,company_name,buyer_name,0,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,file_no", "",'','0,0,0,0,0,0,0,0');
	}
	else
	{
		$arr=array (2=>$comp,3=>$buyer_arr);
		if($db_type==0)
		{
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and  a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."   and a.is_deleted=0 $company $buyer $job_cond $style_cond  order by a.job_no";
		}
		if($db_type==2)
		{
		 $sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no";
		}
		//echo $sql;
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "90,60,120,100","1020","320",0, $sql , "js_set_value", "job_no,po_number", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "",'','0,0,0,0,0');
	}
} 

if ($action=="populate_data_from_search_popup_job")
{
	$company_id=return_field_value("company_name","wo_po_details_master","job_no ='$data' and is_deleted=0 and status_active=1");
	 
	$data_array=sql_select("select a.job_no,a.job_no_prefix,a.job_no_prefix_num,a.company_name,a.buyer_name,a.location_name,a.style_ref_no,a.style_description,a.order_repeat_no,a.set_break_down,a.quotation_id,a.job_quantity,a.order_uom
 from wo_po_details_master a,wo_po_details_mas_set_details b  where a.job_no=b.job_no and a.job_no='$data' and b.job_no='$data'");
 $i=0;
	foreach ($data_array as $row)
	{
		if($i==0)
		{
			$i++;
			//$cbo_dealing_merchant= create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where team_id='".$row[csf("team_leader")]."'and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
			
			echo "load_drop_down( 'requires/yarn_allocation3rd_controller', '".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' ) ;\n";
			echo "document.getElementById('txt_job_no').value 				= '".$row[csf("job_no")]."';\n"; 
			//echo "document.getElementById('txt_order_id').value 			= '".$row[csf("buyer_name")]."';\n";  
			echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_name")]."';\n";
			echo "document.getElementById('cbo_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n"; 
			echo "$('#cbo_buyer_name').attr('disabled','true')".";\n"; 
		}
		
	}
	//$get_data_field=return_field_value("a.booking_no,'_',contact_person", "lib_buyer", "id=1");
	//$get_data_field=return_field_value("a.booking_no", "wo_booking_mst", "job_no=$jobno");
	//echo "document.getElementById('txt_booking_no').value = '".$get_data_field."';\n"; 
	
}

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select a.booking_no,a.company_id,a.buyer_id,a.job_no,a.po_break_down_id, sum(b.grey_fab_qnty) as booking_qnty from wo_booking_mst a,wo_booking_dtls b  where a.booking_no=b.booking_no and  a.booking_no='$data' and a.status_active=1 and  b.is_deleted=0 group by a.booking_no,a.company_id,a.buyer_id,a.job_no,a.po_break_down_id"; 
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "load_drop_down( 'requires/yarn_allocation3rd_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ) ;\n";
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n"; 
        echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n"; 
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_booking_qnty').value = '".$row[csf("booking_qnty")]."';\n";
		$po_no="";
		$sql_po= "select po_number from  wo_po_break_down  where id in(".$row[csf('po_break_down_id')].")"; 
		$data_array_po=sql_select($sql_po);
		foreach ($data_array_po as $row_po)
		{
			$po_no.=$row_po[csf('po_number')].",";
		}
		echo "document.getElementById('txt_order_no').value = '".rtrim($po_no,",")."';\n";
		echo "document.getElementById('txt_order_id').value = '".$row[csf("po_break_down_id")]."';\n";
		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
	 }
	 
	 exit();
}

if($action=="fabric_description_list")
{
?>
    <table cellspacing="0" width="340" class="rpt_table" border="0" rules="all">
    <thead>
        <tr>
            <th width="30">SL</th>
            <th width="220">Fabric Description</th>
            <th width="90">Booking Qnty</th>
        </tr>
    </thead>
    <?
        if($db_type==0)
        {
            $sql="select a.id,a.body_part_id,a.color_type_id,a.fabric_description,a.gsm_weight,a.width_dia_type,c.dia_width,c.booking_no,c.fabric_color_id,sum(grey_fab_qnty) as grey_fab_qnty  
           from
           wo_pre_cost_fabric_cost_dtls a, 
           wo_booking_dtls c 
           where 
           a.job_no=c.job_no and  
           a.id= c.pre_cost_fabric_cost_dtls_id and 
           c.job_no ='$data' and 
           a.fab_nature_id=2 and 
           booking_type=1 and 
           a.status_active=1 
           and a.is_deleted=0 
           and c.status_active=1 
           and c.is_deleted=0 group by a.id";
        }
        else
        {
            $sql="select a.fabric_description, a.gsm_weight, a.width_dia_type,sum(b.grey_fab_qnty) as grey_fab_qnty  
            from
            wo_pre_cost_fabric_cost_dtls a,
            wo_booking_dtls b
            where 
            a.job_no=b.job_no and  
            a.id= b.pre_cost_fabric_cost_dtls_id and 
            b.job_no ='$data' and 
            a.fab_nature_id=2 and 
            b.booking_type=1 and 
            b.status_active=1 and 
            b.is_deleted=0 and
            a.status_active=1 and 
            a.is_deleted=0 
            group by a.fabric_description, a.gsm_weight, a.width_dia_type";
        }
        //echo $sql;
        $DataArray=sql_select($sql);
        $i=1;
        $total_qnty=0;
        foreach($DataArray as $row)
        {
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		?>
			<tr bgcolor="<? echo $bgcolor;  ?>">
                <td><? echo $i;  ?></td>
                <td><? echo $row[csf("fabric_description")].",".$row[csf("gsm_weight")].",".$fabric_typee[$row[csf("width_dia_type")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("grey_fab_qnty")],2); $total_qnty+=$row[csf("grey_fab_qnty")];?></td>
			</tr>
		<?
			$i++;
		}
		
		?>

        <tfoot>
            <th></th>
            <th></th>
            <th><? echo number_format($total_qnty,2);?></th>
        </tfoot>
    </table>
<?
exit();
}

if($action=="yarn_description_list")
{
?>
    <table cellspacing="0" width="340" class="rpt_table" border="0" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="220">Yarn Description</th>
            <th width="90">Cons/ Dzn</th>
        </thead>
		<?
		$count_array=return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1","id","yarn_count");
		$sql="select count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty from wo_pre_cost_fab_yarn_cost_dtls where job_no='$data' and status_active=1 and is_deleted=0";
		$DataArray=sql_select($sql);
		$i=1;
		foreach($DataArray as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td>
					<?
                        echo $count_array[$row[csf("count_id")]].",";
                        if($row[csf("copm_one_id")] !=0 || $row[csf("copm_one_id")] !="" )
                        {
                            echo $composition[$row[csf("copm_one_id")]];
                        }
                        if($row[csf("percent_one")] !=0 || $row[csf("percent_one")] !="" )
                        {
                            echo $row[csf("percent_one")]."%,";
                        }
                        
                        if($row[csf("copm_two_id")] !=0  )
                        {
                            echo $composition[$row[csf("copm_two_id")]]."";
                        }
                        if($row[csf("percent_two")] !=0 )
                        {
                            echo $row[csf("percent_two")]."%,";
                        }
                        
                        if($row[csf("type_id")] !=0 || $row[csf("type_id")] !="" )
                        {
                            echo $yarn_type[$row[csf("type_id")]].",";
                        }
                        if($row[csf("cons_ratio")] !=0 || $row[csf("cons_ratio")] !="" )
                        {
                            echo $row[csf("cons_ratio")]."%";
                        }
                    ?>
                </td>
                <td align="right"><? echo number_format($row[csf("cons_qnty")],2); ?></td>
            </tr>
		<?
			$i++;
		}
        ?>
    </table>
<?
exit();
}

if($action=="open_item_popup")
{
	echo load_html_head_contents("Item List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( str ) 
		{
			var str_array=str.split("_");
			$('#product_id').val( str_array[0] );
			$('#product_name').val( str_array[1] );
			$('#available_qnty').val( str_array[2] );
			$('#unit_of_measurment').val( str_array[3] );
			parent.emailwindow.hide()
		}
	   
	
    </script>
</head>

<body>
<div align="center" style="width:1000px;">
</div>
<div align="center" style="width:910px;">
<input type="hidden" id="product_id" />
<input type="hidden" name="product_name" id="product_name" value="" />
<input type="hidden" name="available_qnty" id="available_qnty" value="" />
<input type="hidden" name="unit_of_measurment" id="unit_of_measurment" value="" />
	<? 
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$supplier=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');

	$arr=array (0=>$comp,1=>$item_category,2=>$supplier);
	
	
	//$sql= "select id,company_id,item_category_id,supplier_id,lot,product_name_details,current_stock,allocated_qnty,available_qnty,unit_of_measure from product_details_master where company_id=$cbo_company_name and item_category_id=$cbo_item_category and current_stock > allocated_qnty and status_active=1 and is_deleted=0"; old condition
	
	 $sql= "select id,company_id,item_category_id,supplier_id,lot,product_name_details,current_stock,allocated_qnty,available_qnty,unit_of_measure from product_details_master where company_id=$cbo_company_name and item_category_id=$cbo_item_category and current_stock > 0 and status_active=1 and is_deleted=0"; //new condition
	 
	//echo  create_list_view("list_view", "Company,Item Catagory,Supplier,Lot,Product Name,Current Stock,Allocated Qnty,Unallocated Qnty", "60,100,100,50,350,100,100,100","1010","320",0, $sql , "js_set_value", "id,product_name_details,available_qnty,unit_of_measure", "", 1, "company_id,item_category_id,supplier_id,0,0,0,0,0", $arr , "company_id,item_category_id,supplier_id,lot,product_name_details,current_stock,allocated_qnty,available_qnty", '','setFilterGrid(\'list_view\',-1)','0,0,0,0,0,2,2,2','',"");
/*	if($db_type==0)
	{
		$returnRes = explode(",",return_field_value("concat(min(transaction_date),',',max(transaction_date))","inv_transaction","prod_id=".$row[csf("id")]));
	}
	else
	{
		$returnRes=explode(",",return_field_value("(min(transaction_date) || ',' || max(transaction_date)) as tran_date","inv_transaction","prod_id=".$row[csf("id")],"tran_date"));
	}*/
	$transaction_date_arr=array();
	$sql_date=sql_select("select prod_id,min(transaction_date) as min_date,max(transaction_date) as max_date from inv_transaction group by prod_id");
	foreach($sql_date as $row_d)
	{
		$transaction_date_arr[$row_d[csf('prod_id')]]['min_date']=$row_d[csf('min_date')];	
		$transaction_date_arr[$row_d[csf('prod_id')]]['max_date']=$row_d[csf('max_date')];	
	}
	
	?>
    
<table cellspacing="0" width="900" class="rpt_table" border="0" rules="all">
<thead>
<tr>
<th width="40">SL</th>
<th width="70">Company</th>
<th width="130">Supplier</th>
<th width="70">Lot</th>
<th width="200">Product Name</th>
<th width="80">Current Stock</th>
<th width="80">Allocated Qnty</th>
<th width="80">Unallocated Qnty</th>
<th width="60">Age (Days)</th>
<th width="">DOH</th>
</tr>
</thead>
</table>
<div align="" style="width:900px;max-height:300px; overflow-y:scroll;">
<table id="list_view" cellspacing="0" width="880" class="rpt_table" border="0" rules="all">
<tbody >
<?
	$DataArray=sql_select($sql);
	$i=1;
	foreach($DataArray as $row)
	{
		if ($i%2==0)  
		$bgcolor="#E9F3FF";
		else
		$bgcolor="#FFFFFF";	
		if($txt_item_id==$row[csf("id")])
		{
			$bgcolor="#FFFF66";
		}
		else
		{
		  $bgcolor=$bgcolor;	
		}
		//$Fabric_des = trim($body_part[$row[csf("body_part_id")]]).", ".trim($color_type[$row[csf("color_type_id")]]).", ".trim($row[csf("fabric_description")]).", ".trim($row[csf("gsm_weight")]).", ".trim($fabric_typee[$row[csf("width_dia_type")]]).",".trim($row[csf("dia_width")]);

?>
<tr bgcolor="<? echo $bgcolor;  ?>" onClick="js_set_value('<? echo $row[csf("id")]."_".$row[csf("product_name_details")]."_".$row[csf("available_qnty")]."_".$row[csf("unit_of_measure")];?>')" style="cursor:pointer" >
<td width="40"><? echo $i;  ?></td>
<td width="70"><? echo $comp[$row[csf("company_id")]] ;?></td>
<td width="130"><p><? echo $supplier[$row[csf("supplier_id")]]; ?></p></td>
<td width="70"><p><? echo $row[csf("lot")]; ?></p></td>
<td width="200"><p><? echo $row[csf("product_name_details")]; ?></p></td>
<td width="80" align="right"><? echo number_format($row[csf("current_stock")],2); ?></td>
<td width="80" align="right"><? echo number_format($row[csf("allocated_qnty")],2); ?></td>
<td width="80" align="right"><? echo number_format($row[csf("available_qnty")],2); ?></td>
<?
	//echo $returnRes[0];
	$ageOfDays = datediff("d",$transaction_date_arr[$row[csf("id")]]['min_date'],date("Y-m-d"));
	$daysOnHand = datediff("d",$transaction_date_arr[$row[csf("id")]]['max_date'],date("Y-m-d"));
?>
<td width="60" align="right"><? echo $ageOfDays; ?></td>
<td width="" align="right"><? echo $daysOnHand; ?></td>
</tr>
<?
$i++;
	}
?>
</tbody>

</table>
</div>
</div>
</body>  
<script>setFilterGrid('list_view',-1)</script>         
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
 
}


if($action=="open_qnty_popup")
{
	echo load_html_head_contents("Item List", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	function distribution_value(mehtod)
	    {
			if(mehtod==1)
			{
				$('#tbl_order_qnty_list input[name="txt_qnty[]"]').removeAttr('disabled', 'disabled');
				$('#allocated_qnty').attr('disabled', 'disabled');
			}
			else
			{
				$('#tbl_order_qnty_list input[name="txt_qnty[]"]').attr('disabled', 'disabled');
				$('#allocated_qnty').removeAttr('disabled', 'disabled');
			}
		}
		
	function set_sum_value(des_fil_id,field_id,table_id)
	{
		var rowCount = $('#tbl_order_qnty_list tr').length-2;
		var ddd={dec_type:6,comma:0,currency:1};
		math_operation( des_fil_id, field_id, '+', rowCount,ddd);
	}
	function js_set_value_qnty()
	{
		var rowCount = $('#tbl_order_qnty_list tr').length-2;
		var qnty_breck_down="";
		for(var i=1; i<=rowCount; i++)
		{
			if (form_validation('txt_qnty_'+i,'Qnty')==false)
			{
				return;
			}
			if(qnty_breck_down=="")
			{
				qnty_breck_down=$('#txt_qnty_'+i).val();
			}
			else
			{
				qnty_breck_down+="_"+$('#txt_qnty_'+i).val();
			}
		}
		document.getElementById('qnty_breck_down').value=qnty_breck_down;
		var allocated_qnty=document.getElementById('allocated_qnty').value;
		var hide_allocated_qnty=document.getElementById('hide_allocated_qnty').value;
		var available_qnty=document.getElementById('available_qnty').value;
		
		var available_qnty_curr=available_qnty*1+hide_allocated_qnty*1;
		//alert(available_qnty_curr);
		if(allocated_qnty*1>available_qnty_curr*1)
		{
			alert("Allocated qnty greater than available qnty");
			return;
		}
		else
		{
			parent.emailwindow.hide();
		}
		
	}
	
	function calculate_poportion(value)
	{
		var tot_po_qnty=(document.getElementById('tot_po_qnty').value)*1;
		var rowCount = $('#tbl_order_qnty_list tr').length-2;
		for(var i=1; i<=rowCount; i++)
		{
			var txt_order_qnty=($('#txt_order_qnty_'+i).val())*1;
			
			$('#txt_qnty_'+i).val(number_format_common(((value/tot_po_qnty)*txt_order_qnty),2,0,1));
		}
		set_sum_value('allocated_qnty','txt_qnty_','tbl_order_qnty_list')
	}
    </script>
</head>

<body>
<?
$data=explode(",",$txt_order_id);
$data1=explode("_",$qnty_breck_down);
//print_r($data);
?>
    <div align="center" style="width:800px;">
        <strong>Distribution Method:</strong>
        <input type="radio" name="distribution_type" id="distribution_type_0" value="0" onClick="distribution_value(this.value)" checked />
        <label for="distribution_type_0">Proportionately</label>
        <input type="radio" name="distribution_type" id="distribution_type_1" value="1" onClick="distribution_value(this.value)" />
        <label for="distribution_type_1">Manually</label>
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="700" id="tbl_order_qnty_list" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>                	 
                        <th width="150" colspan="5">
                        Available Qnty:<input type="text" name="available_qnty"  id="available_qnty" style="width:60px " value="<? echo $available_qnty; ?>" class="text_boxes_numeric" disabled />
                        Allocated Qnty:<input type="text" name="allocated_qnty"  id="allocated_qnty" style="width:60px "  class="text_boxes_numeric" value="<? echo $txt_qnty;?>" onChange="calculate_poportion(this.value)"/>
                        <input type="text" name="hide_allocated_qnty"  id="hide_allocated_qnty" style="width:60px "  class="text_boxes" value="<? echo $txt_qnty;?>"/>
                        <input type="hidden" name="qnty_breck_down"  id="qnty_breck_down" style="width:60px "  class="text_boxes" value="<? echo $qnty_breck_down;?>"/>
                        Booking Qnty:<input type="text" name="booking_qnty"  id="booking_qnty" style="width:60px "  class="text_boxes_numeric" value="<? echo $txt_booking_qnty;?>" readonly/>
                        </th>
                    </tr>
                    <tr>                	 
                        <th width="200">Order No</th>
                        <th width="100">Internal Ref</th>
                        <th width="100">File No</th>
                        <th width="150">Order Qnty</th>
                        <th width="150" class="must_entry_caption">Qnty</th>
                    </tr>
                </thead>
                <tbody>
					<?
					
					$sl=1;
					$tot_po_qnty=0;
                    for($i=0;$i<count($data);$i++)
                    {
						//echo "select po_number,po_quantity,plan_cut from wo_po_break_down where id =$data[$i]";
						$sql_order_no_qnty=sql_select("select po_number,po_quantity,plan_cut,grouping,file_no from wo_po_break_down where id=$data[$i]");
						list($order_data)=$sql_order_no_qnty;
						//print_r($order_data);
						$tot_po_qnty+=$order_data[csf('plan_cut')];
                    ?>
                    <tr>
                        <td width="200">
                        <input type="text" class="text_boxes"  name="txt_order_no[]"  id="txt_order_no_<? echo $sl; ?>" style="width:200px " value="<? echo $order_data[csf('po_number')];?>" disabled />
                        <input type="hidden" name="txt_order_id[]"  id="txt_order_id_<? echo $sl; ?>" style="width:160px " value="<? echo $data[$i];?>" disabled />
                        </td>
                          <td width="90" align="right">
                           <input type="text" class="text_boxes"  name="txt_ref[]"  id="txt_ref_<? echo $sl; ?>" style="width:90px " value="<? echo $order_data[csf('grouping')];?>" disabled />
						</td>
                         <td width="90" align="right">
                          <input type="text" class="text_boxes"  name="txt_file[]"  id="txt_file_<? echo $sl; ?>" style="width:90px " value="<? echo $order_data[csf('file_no')];?>" disabled />
						</td>
                        <td width="150">
                        <input type="text" name="txt_order_qnty[]"  id="txt_order_qnty_<? echo $sl; ?>" style="width:150px "  class="text_boxes_numeric"  value="<? echo $order_data[csf('plan_cut')];?>" disabled />
                        </td>
                        <td width="150">
                        <input type="text" name="txt_qnty[]"  id="txt_qnty_<? echo $sl; ?>" style="width:150px " value="<? echo $data1[$i]; ?>" class="text_boxes_numeric" onChange="set_sum_value('allocated_qnty','txt_qnty_','tbl_order_qnty_list')" disabled />
                        </td>
                    </tr>
                    <?
					$sl++;
				    }
					?>
                </tbody>
                
            </table>
            <table width="740"  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
            <tr>
           <td align="center" width="100%" class="button_container">
                        
						        <input type="button" class="formbutton" value="Close" onClick="js_set_value_qnty()"/>
                                <input type="hidden" name="tot_po_qnty" id="tot_po_qnty" value="<? echo $tot_po_qnty;?>"/>

                        </td> 
            </tr>
            </table>
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit(); 
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "inv_material_allocation_mst", 1 ) ;
		$field_array="id,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,qnty_break_down,inserted_by,insert_date";
		$data_array="(".$id.",".$txt_job_no.",".$txt_order_id.",".$cbo_item_category.",".$txt_allocation_date.",".$txt_booking_no.",".$txt_item_id.",".$txt_qnty.",".$qnty_breck_down.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
//====================================================================================
		$add_comma=0;
		$id1=return_next_id( "id", "inv_material_allocation_dtls", 1 ) ;
		$field_array1="id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date";
		$po_break_down_id=explode(',',str_replace("'",'',$txt_order_id));
        $qnty_data=explode("_",str_replace("'",'',$qnty_breck_down));
		
		if ( count($po_break_down_id)>0)
		{
			for($c=0;$c < count($po_break_down_id);$c++)
			{
				 
				 if ($add_comma!=0) $data_array1 .=",";
				 $data_array1 .="(".$id1.",".$id.",".$txt_job_no.",".$po_break_down_id[$c].",".$txt_booking_no.",".$cbo_item_category.",".$txt_allocation_date.",".$txt_item_id.",".$qnty_data[$c].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 $id1=$id1+1;
				 $add_comma++;
			}
		//$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
		}
	//	echo "insert into inv_material_allocation_dtls (".$field_array1.") values $data_array1";die;

//=================================================
 		$rID=sql_insert("inv_material_allocation_mst",$field_array,$data_array,0);
		$rID1=true;
		if($data_array1!='')
		{
			$rID1=sql_insert("inv_material_allocation_dtls",$field_array1,$data_array1,0);
		}
		
		$rID_de=execute_query( "update product_details_master set allocated_qnty=(allocated_qnty+$txt_qnty) where id=$txt_item_id",0 );
		$rID_dep=execute_query( "update product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_item_id  ",1 );
		
		if($db_type==0)
		{
			if($rID && $rID1 && $rID_de && $rID_dep)
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID_de && $rID_dep)
			{
				oci_commit($con);  
				echo "0**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}

		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="job_no*po_break_down_id*item_category*allocation_date*booking_no*item_id*qnty*qnty_break_down*updated_by*update_date";
		$data_array="".$txt_job_no."*".$txt_order_id."*".$cbo_item_category."*".$txt_allocation_date."*".$txt_booking_no."*".$txt_item_id."*".$txt_qnty."*".$qnty_breck_down."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
//====================================================================================
		$add_comma=0;
		$id1=return_next_id( "id", "inv_material_allocation_dtls", 1 ) ;
		$field_array1="id,mst_id,job_no,po_break_down_id,booking_no,item_category,allocation_date,item_id,qnty,inserted_by,insert_date";
		$po_break_down_id=explode(',',str_replace("'",'',$txt_order_id));
        $qnty_data=explode("_",str_replace("'",'',$qnty_breck_down));
		
		if ( count($po_break_down_id)>0)
		{
			$$rID_de=execute_query( "delete from inv_material_allocation_dtls where mst_id=$update_id",1 );
			for($c=0;$c < count($po_break_down_id);$c++)
			{
				 
				 if ($add_comma!=0) $data_array1 .=",";
				 $data_array1 .="(".$id1.",".$update_id.",".$txt_job_no.",".$po_break_down_id[$c].",".$txt_booking_no.",".$cbo_item_category.",".$txt_allocation_date.",".$txt_item_id.",".$qnty_data[$c].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 $id1=$id1+1;
				 $add_comma++;
			}
		}
//=================================================
		$rID=sql_update("inv_material_allocation_mst",$field_array,$data_array,"id","".$update_id."",0);
//echo $data_array1;
		$rID1=true;
		if($data_array1 !='')
		{
			$rID1=sql_insert("inv_material_allocation_dtls",$field_array1,$data_array1,0);
		}
		$rID_adj=execute_query( "update  product_details_master set allocated_qnty=(allocated_qnty-$txt_old_qnty) where id=$txt_item_id_old",0 );
		$rID_adjal=execute_query( "update  product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_item_id_old  ",0 );
		
		$rID_de=execute_query( "update  product_details_master set allocated_qnty=(allocated_qnty+$txt_qnty) where id=$txt_item_id",0 );
		$rID_deal=execute_query( "update  product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$txt_item_id",1 );

		if($db_type==0)
		{
			if($rID && $rID1 && $rID_adj && $rID_adjal && $rID_de && $rID_deal)
			{
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID_adj && $rID_adjal && $rID_de && $rID_deal)
			{
				oci_commit($con);    
				echo "1**".$rID;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		$field_array="status_active*is_deleted";
		$data_array="'0'*'1'";
		$rID=sql_delete("inv_material_allocation_mst",$field_array,$data_array,"id","".$update_id."",1);
		$rID2=sql_delete("inv_material_allocation_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		$rID3=execute_query( "update product_details_master set allocated_qnty=(allocated_qnty-$txt_old_qnty) where id=$txt_item_id_old  ",1 );
		$rID4=execute_query( "update product_details_master set available_qnty=(current_stock+allocated_qnty) where id=$txt_item_id_old  ",1 );
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);    
				echo "2**".$rID;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
}


if($action=="show_item_active_listview")
{
	$data=explode("_",$data);
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
	$supplier=return_library_array( "select id, short_name from  lib_supplier",'id','short_name');
	$color=return_library_array( "select id, color_name from   lib_color",'id','color_name');

	$prod_data_arr=array();
	$prod_data=sql_select( "select id, product_name_details, supplier_id, lot from product_details_master where item_category_id=1");
	foreach($prod_data as $row)
	{
		$prod_data_arr[$row[csf('id')]]['prod_details']=$row[csf('product_name_details')];
		$prod_data_arr[$row[csf('id')]]['supp']=$row[csf('supplier_id')];
		$prod_data_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
	}
	
	if($db_type==0)
	{
		$po_sql=sql_select("select distinct a.po_number,a.grouping,a.file_no,b.id from wo_po_break_down a,inv_material_allocation_mst b where a.job_no_mst=b.job_no and b.job_no='$data[0]' and  FIND_IN_SET(a.id, b.po_break_down_id)");
	}
	else
	{
		$po_sql=sql_select("select a.po_number,a.grouping,a.file_no,b.id from wo_po_break_down a,inv_material_allocation_mst b, inv_material_allocation_dtls c where a.job_no_mst=b.job_no and b.id=c.mst_id and b.job_no='$data[0]' and a.id=c.po_break_down_id group by b.id, a.po_number,a.grouping,a.file_no");
	}
	
	$po_num_array=array();$po_data_arr=array();
	foreach($po_sql as $row)
	{
		
		$po_data_arr[$row[csf('po_number')]]['ref']=$row[csf('grouping')];
		$po_data_arr[$row[csf('po_number')]]['file']=$row[csf('file_no')];
		
		if (array_key_exists($row[csf('id')],$po_num_array))
		{
			$po_num_array[$row[csf('id')]]=$po_num_array[$row[csf('id')]].",".$row[csf('po_number')];
			 
		}
		else
		{
			$po_num_array[$row[csf('id')]]=$row[csf('po_number')];
			 
		}	
		
	}
	
	//$sql= "select a.id as sid, a.id as id,a.job_no,a.po_break_down_id,a.item_id,a.qnty,b.company_name,b.buyer_name,b.location_name from inv_material_allocation_mst a,wo_po_details_master b where a.job_no=b.job_no and a.job_no='$data[0]' and a.item_category=$data[1] and a.booking_no='$data[2]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql= "select a.id as sid, a.id as id,a.job_no,a.po_break_down_id,a.item_id,a.qnty,b.company_name,b.buyer_name,b.location_name from inv_material_allocation_mst a,wo_po_details_master b where a.job_no=b.job_no and a.job_no='$data[0]' and a.item_category=$data[1]  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$result=sql_select($sql);
	//$arr=array (1=>$comp,2=>$buyer,3=>$supplier,4=>$po_num_array,5=>$item);	  
	//echo create_list_view("list_view", "SID,Company,Buyer,Supplier,Order No,Allocated Yarn,Qnty", "50,60,50,100,200,200,100"," 900","320",0, $sql , "get_php_form_data", "id", "'populate_material_allocation_data'", 1, "0,company_name,buyer_name,location_name,id,item_id,0", $arr , "sid,company_name,buyer_name,location_name,id,item_id,qnty", 'requires/yarn_allocation3rd_controller','','0,0,0,0,0,0,2','',"");
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">SID</th>
			<th width="60">Company</th>               
			<th width="60">Buyer</th>
			<th width="70">Supplier</th>
            <th width="100">Internal Ref</th>
            <th width="90">File No</th>
			<th width="170">Order No</th>
			<th width="150">Allocated Yarn</th>
            <th width="70">Lot</th>
			<th>Qnty</th>
		</thead>
	</table>
	<div style="width:950px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">  
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_no=array_unique(explode(",",$po_num_array[$row[csf('id')]]));
					
				$ref_cond='';$file_cond='';
				foreach($po_no as $row_data)
				{  
					if($ref_cond=="") $ref_cond=$po_data_arr[$row_data]['ref']; else $ref_cond.=",".$po_data_arr[$row_data]['ref'];
					if($file_cond=="") $file_cond=$po_data_arr[$row_data]['file']; else $file_cond.=",".$po_data_arr[$row_data]['file'];
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'populate_material_allocation_data','requires/yarn_allocation3rd_controller');"> 
					<td width="30"><? echo $i; ?></td>
					<td width="50"><p><? echo $row[csf('sid')]; ?></p></td>
					<td width="60"><p><? echo $comp[$row[csf('company_name')]]; ?></p></td>               
					<td width="60"><p><? echo $buyer[$row[csf('buyer_name')]]; ?></p></td>
					<td width="70"><p><? echo $supplier[$prod_data_arr[$row[csf('item_id')]]['supp']]; ?></p></td>
                    <td width="100"><p><? echo implode(",",array_unique(explode(",",$ref_cond))); ?></p></td>
                    <td width="90"><p><? echo implode(",",array_unique(explode(",",$file_cond))); ?></p></td>
					<td width="170"><p><? echo $po_num_array[$row[csf('id')]]; ?></p></td>
					<td width="150"><p><? echo $prod_data_arr[$row[csf('item_id')]]['prod_details']; ?></p></td>
                    <td width="70"><p><? echo $prod_data_arr[$row[csf('item_id')]]['lot']; ?></p></td>
					<td align="right"><? echo number_format($row[csf('qnty')],2); ?>&nbsp;</td>
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

if($action=="populate_material_allocation_data")
{
	//$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
	if($db_type==0)
	{
		$po_sql=sql_select("select distinct a.po_number,b.id from wo_po_break_down a,inv_material_allocation_mst b where a.job_no_mst=b.job_no and b.id='$data' and  FIND_IN_SET(a.id, b.po_break_down_id)");
	}
	else
	{
		$po_sql=sql_select("select a.po_number,b.id from wo_po_break_down a,inv_material_allocation_mst b, inv_material_allocation_dtls c where a.job_no_mst=b.job_no and b.id=c.mst_id and b.id='$data' and a.id=c.po_break_down_id group by b.id, a.po_number");
	}
	
	$po_num_array=array();
	foreach($po_sql as $row)
	{
		if (array_key_exists($row[csf('id')],$po_num_array))
		  {
		  $po_num_array[$row[csf('id')]]=$po_num_array[$row[csf('id')]].",".$row[csf('po_number')];
		  }
		else
		  {
		  $po_num_array[$row[csf('id')]]=$row[csf('po_number')];
		  }	
	}
	
	$sql= sql_select("select a.id,a.job_no,a.po_break_down_id,a.item_category,a.allocation_date,a.item_id,a.qnty,a.qnty_break_down,b.company_name,b.buyer_name,b.location_name from  inv_material_allocation_mst a,wo_po_details_master b where  a.job_no=b.job_no and a.id='$data' and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0"); 
	foreach($sql as $row_data)
	{
		//echo "select product_name_details,available_qnty from   product_details_master where id=$row_data[csf('id')]";
		$item_name=sql_select("select product_name_details,available_qnty,unit_of_measure from product_details_master where id='".$row_data[csf('item_id')]."'");
		list($item_name_row)=$item_name;
		echo "document.getElementById('txt_order_no').value = '".$po_num_array[$row_data[csf("id")]]."';\n";  
		echo "document.getElementById('txt_order_id').value = '".$row_data[csf("po_break_down_id")]."';\n"; 
		echo "document.getElementById('cbo_item_category').value = '".$row_data[csf("item_category")]."';\n"; 
		echo "document.getElementById('txt_allocation_date').value = '".change_date_format($row_data[csf("allocation_date")], "dd-mm-yyyy", "-")."';\n"; 
		echo "document.getElementById('txt_item').value = '".$item_name_row[csf("product_name_details")]."';\n";  
		echo "document.getElementById('txt_item_id').value = '".$row_data[csf("item_id")]."';\n"; 
		echo "document.getElementById('txt_item_id_old').value = '".$row_data[csf("item_id")]."';\n"; 
		echo "document.getElementById('txt_qnty').value = '".$row_data[csf("qnty")]."';\n";
		echo "document.getElementById('txt_old_qnty').value = '".$row_data[csf("qnty")]."';\n";
		echo "document.getElementById('qnty_breck_down').value = '".$row_data[csf("qnty_break_down")]."';\n";
		echo "document.getElementById('available_qnty').value = '".$item_name_row[csf("available_qnty")]."';\n";
	    echo "document.getElementById('cbo_uom').value = '".$item_name_row[csf("unit_of_measure")]."';\n";
	    echo "document.getElementById('update_id').value = '".$row_data[csf("id")]."';\n";
	   	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_material_allocation_entry',1);\n";  
	}
	exit();
	
}
?>
