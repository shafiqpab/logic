<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//************************************ Start *************************************************
$order_num_arr=return_library_array("select id, order_no from subcon_ord_dtls", "id", "order_no");
$location_arr=return_library_array("select id, location_name from  lib_location", "id", "location_name");

if ($action=="load_variable_settings_control")
{
	echo "$('#data_control_status').val(0);\n";
	$sql_result = sql_select("SELECT is_control FROM variable_settings_production Where  COMPANY_NAME = '$data' AND VARIABLE_LIST = 33 AND PAGE_CATEGORY_ID = 32   ");
 	foreach($sql_result as $result)
	{
		echo "$('#data_control_status').val(".$result[csf("is_control")].");\n";
		 
	}
 	exit();
}


if ($action=="load_variable_settings")
{
	$data=explode("_",$data);
	//echo "KKKKK";
	echo "$('#sewing_production_variable').val(0);\n";

	if($data[1]==1)
	{
		$sql_result = sql_select("select cutting_update as ex_factory,sewing_production as production_entry  from  variable_settings_production where company_name=$data[0] and variable_list=1 and status_active=1");
	}
	else if($data[1]==5)
	{
		$sql_result = sql_select("select ex_factory, production_entry from variable_settings_production where company_name=$data[0] and variable_list=1 and status_active=1");
	}
	else if($data[1]==8)
	{
		$sql_result = sql_select("select printing_emb_production as ex_factory,sewing_production as production_entry  from  variable_settings_production where company_name=$data[0] and variable_list=1 and status_active=1");
	}
	else if($data[1]==10)
	{
		$sql_result = sql_select("select  	iron_update as ex_factory,sewing_production as production_entry  from  variable_settings_production where company_name=$data[0] and variable_list=1 and status_active=1");
	}
	else if($data[1]==11)
	{
		$sql_result = sql_select("select finishing_update as ex_factory,sewing_production as production_entry  from  variable_settings_production where company_name=$data[0] and variable_list=1 and status_active=1");
	}
	
	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("ex_factory")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
 	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );		 
}

if ($action=="load_drop_down_party_name")
{
    echo create_drop_down( "cbo_party_name", 152, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company='$data' and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "",'' ); 
	exit();	
}

if ($action=="delivery_id_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_delivery_id').value=id;
			parent.emailwindow.hide();
		}		
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="deliverysearch_1"  id="deliverysearch_1" autocomplete="off">
                <table width="650" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="110">Delivery ID</th>
                        <th width="80">Year</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('deliverysearch_1','search_div','','','','');" /></th>           
                    </thead>
                    <tbody>
                        <tr>
                            <td> <input type="hidden" id="selected_delivery_id"><? //$data=explode("_",$data); ?>  <!--  echo $data;-->
								<? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data, "",0); ?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:95px" />
                            </td>
                            <td> 
                                <?
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year", 60, $year,"", 1, "-Year-", $selected_year, "",0 );
                                ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_year').value, 'create_delivery_search_list_view', 'search_div', 'subcon_gmts_delivery_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div> </td>
                        </tr>
                    </tbody>
                </table>  
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_delivery_search_list_view")
{
	$data=explode('_',$data);
	//echo $data[3];
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!='') $delivery_id_cond=" and delivery_prefix_num= '$data[3]'"; else $delivery_id_cond="";
	//$trans_Type="issue";
	
	if($db_type==0)
	{ 
		if ($data[1]!="" &&  $data[2]!="") $delivery_date= " and delivery_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $delivery_date= "";
		$year_cond= "year(insert_date)as year";
	}
	else if ($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $delivery_date= " and delivery_date between '".change_date_format($data[1], "", "",1)."' and '".change_date_format($data[2], "", "",1)."'";  else $delivery_date= "";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$sql= "select id, delivery_no, company_id, delivery_prefix_num, $year_cond, location_id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where status_active=1 and is_deleted=0 and process_id=3 $company $delivery_date $delivery_id_cond order by id DESC";

	$result = sql_select($sql);
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	
	?> 
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>   
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table">
            <thead>
                <th width="50" >SL</th>
                <th width="70" >Delivery ID</th>
                <th width="60" >Year</th>
                <th width="120" >Party</th>
                <th width="120" >Challan No</th>
                <th width="70" >Delivery Date</th>
                <th>Location</th>
            </thead>
     	</table>
     </div>
     <div style="width:650px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="630" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);" > 
						<td width="50" align="center"><?php echo $i; ?></td>
						<td width="70" align="center"><?php echo $row[csf("delivery_prefix_num")]; ?></td>
                        <td width="60" align="center"><?php echo $row[csf("year")]; ?></td>		
						<td width="120" align="center"><?php echo $party_arr[$row[csf("party_id")]]; ?></td>
						<td width="120"><?php echo $row[csf("challan_no")];  ?></td>	
						<td width="70"><?php echo $row[csf("delivery_date")]; ?></td>
						<td ><?php echo $location_arr[$row[csf("location_id")]];?> </td>	
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

if ($action=="load_php_data_to_form")
{
	//echo "select id, delivery_no, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where id='$data' and status_active=1 and is_deleted=0";die;
	$nameArray=sql_select( "select id, delivery_no,party_id, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company, vehical_no,final_destination,lock_no,dl_no,do_no,attention,driver_name,mobile_no,remarks from subcon_delivery_mst where id='$data' and status_active=1 and is_deleted=0 " ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_sys_id').value 			= '".$row[csf("delivery_no")]."';\n";
		//echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		//echo "load_drop_down( 'requires/subcon_gmts_delivery_controller', $('#cbo_company_name').val(), 'load_drop_down_location', 'location_td' );";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/subcon_gmts_delivery_controller', $('#cbo_company_name').val(), 'load_drop_down_party_name', 'party_td' );";
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n"; 
		echo "$('#cbo_party_name').attr('disabled','true')".";\n"; 
		echo "document.getElementById('txt_challan_no').value		= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_delivery_date').value	= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('txt_transport_company').value 		= '".$row[csf("transport_company")]."';\n";   
		echo "document.getElementById('cbo_forwarder').value		= '".$row[csf("forwarder")]."';\n"; 
		echo "document.getElementById('txt_vehical_no').value		= '".$row[csf("vehical_no")]."';\n"; 
		echo "document.getElementById('txt_update_id').value			= '".$row[csf("id")]."';\n"; 
		echo "document.getElementById('txt_mst_id').value			= '".$row[csf("id")]."';\n"; 

		echo "document.getElementById('txt_final_destination').value= '".$row[csf("final_destination")]."';\n"; 
		echo "document.getElementById('txt_lock_no').value			= '".$row[csf("lock_no")]."';\n"; 
		echo "document.getElementById('txt_dl_no').value			= '".$row[csf("dl_no")]."';\n"; 
		echo "document.getElementById('txt_do_no').value			= '".$row[csf("do_no")]."';\n"; 
		echo "document.getElementById('txt_attention').value		= '".$row[csf("attention")]."';\n"; 
		echo "document.getElementById('txt_driver_name').value		= '".$row[csf("driver_name")]."';\n"; 
		echo "document.getElementById('txt_mobile_no').value		= '".$row[csf("mobile_no")]."';\n"; 
		echo "document.getElementById('txt_remarks').value			= '".$row[csf("remarks")]."';\n"; 
		//echo "set_button_status(0, '".$_SESSION['page_permission']."','fnc_material_issue',1);\n";txt_mst_id
	}
	exit();	
}

 
if($action=="delivery_list_view")
{
?>	
	<div style="width:830px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="120" >Item Name</th>
                <th width="120" >Order No</th>
                <th width="70" >Delivery Date</th>
                <th width="80" >Delivery Qty</th>                    
                <th width="120" >Location</th>
                <th width="120" >Process</th>
                <th align="center">Challan No</th>
            </thead>
    	</table> 
    </div>
	<div style="width:830px;max-height:180px; overflow:y-scroll" id="" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="details_table">
		<?  
			$i=1;
			$sqlResult =sql_select("select a.id,b.id as dtls_id,b.order_id,b.item_id,a.delivery_date,b.process_id,b.delivery_qty,a.location_id,a.challan_no from  subcon_delivery_mst a,  subcon_delivery_dtls b where a.id=b.mst_id and  a.id=$data and a.status_active=1 and a.is_deleted=0 order by a.id");
 			foreach($sqlResult as $selectResult)
			{
 				if ($i%2==0)  
                	$bgcolor="#E9F3FF";
                else
               	 	$bgcolor="#FFFFFF";
				
				 $data_string="'".$selectResult[csf('id')]."__".$selectResult[csf('dtls_id')]."'";
				//$order_num_arr
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $data_string; ?>,'populate_delivery_form_data','requires/subcon_gmts_delivery_controller');" > 
                    <td width="40" align="center"><? echo $i; ?></td>
                    <td width="120" align="center"><p><? echo $garments_item[$selectResult[csf('item_id')]]; ?></p></td>
                    <td width="120" align="center"><p><? echo $order_num_arr[$selectResult[csf('order_id')]]; ?>&nbsp;</p></td>
                     <td width="70" align="center"><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
                    <td width="80" align="center"><p><? echo $selectResult[csf('delivery_qty')]; ?></p></td>
                    <td width="120" align="center"><p><? echo $location_arr[$selectResult[csf('location_id')]]; ?></p></td>
                    <td width="120" align="center"><p><? echo $production_process[$selectResult[csf('process_id')]]; ?>&nbsp;</p></td>
                   
                    <td align="center"><p><? echo $selectResult[csf('challan_no')]; ?>&nbsp;</p></td>
                </tr>
			<?
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
    <!--<script> setFilterGrid("details_table",-1); </script>-->
<?
	exit();
}

if ($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1,'');
 	//$ex_data = explode("_",$data);
	$company_id=$company;
	$party_id=$cbo_party_name;
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_order").focus();
        });
	
		function js_set_value(id)
		{ 
			var response=id.split("_");
			$("#hidden_order_id").val(response[0]);
			$("#hidden_item_id").val(response[1]);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead> 
                        <tr>
                            <th colspan="5" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="100">Job No</th>
                            <th width="100">Style No</th>
                            <th width="100">Order No</th>
                            <th width="170">Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="ganeral">
                            <td  align="center">  
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_search_job" id="txt_search_job" placeholder="Search Job"/>			
                            </td>
                            <td  align="center">  
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_search_style" id="txt_search_style" placeholder="Search Style" />			
                            </td>
                            <td align="center">				
                                <input type="text" style="width:100px" class="text_boxes"  name="txt_search_order" id="txt_search_order" placeholder="Search Order" />			
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_style').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+<? echo $party_id; ?>+'_'+document.getElementById('cbo_string_search_type').value, 'create_order_search_list_view', 'search_div', 'subcon_gmts_delivery_controller', 'setFilterGrid(\'tbl_order_list\',-1)')" style="width:80px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" id="hidden_order_id">
                                <input type="hidden" id="hidden_item_id">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_order_search_list_view")
{
 	$ex_data = explode("_",$data);
	$search_job = $ex_data[0];
	$search_style = $ex_data[1];
	$search_order = $ex_data[2];
	$date_from = $ex_data[3];
	$date_to = $ex_data[4];
	$company = $ex_data[5];
	$party = $ex_data[6];
	$search_type = $ex_data[7];
	
	if($search_type==1)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num='$search_job'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref='$search_style'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no='$search_order'"; else $order_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num like '%$search_job%'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref like '%$search_style%'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no like '%$search_order%'"; else $order_cond="";
	}
	else if($search_type==2)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num like '$search_job%'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref like '$search_style%'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no like '$search_order%'"; else $order_cond="";
	}
	else if($search_type==3)
	{
		if ($search_job!='') $job_cond=" and b.job_no_prefix_num like '%$search_job'"; else $job_cond="";
		if ($search_style!='') $style_cond=" and a.cust_style_ref like '%$search_style'"; else $style_cond="";
		if ($search_order!='') $order_cond=" and a.order_no like '%$search_order'"; else $order_cond="";
	}
	
	if(	$party!=0) $party_cond=" and b.party_id='$party'"; else $party_cond="";
	
	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
		$year_cond= "year(b.insert_date)as year";

	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
		$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
	}
	
	$sql ="select a.id, a.order_rcv_date, a.order_no, a.order_uom, a.main_process_id, b.party_id, a.cust_style_ref, b.subcon_job, b.job_no_prefix_num, $year_cond, c.item_id, a.order_quantity as order_quantity, sum(c.qnty) as qnty from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c,subcon_gmts_prod_dtls d where a.main_process_id in (1,5,8,9,10,11) and a.job_no_mst=b.subcon_job and c.order_id=a.id and d.order_id=a.id and d.order_id=c.order_id and a.status_active=1 and a.is_deleted=0 and b.company_id='$company' $party_cond $job_cond $style_cond $order_cond $date_cond group by a.id, a.order_rcv_date, a.order_no,a.order_quantity, a.order_uom, a.main_process_id, b.party_id, a.cust_style_ref, b.subcon_job, b.job_no_prefix_num, b.insert_date, c.item_id order by b.job_no_prefix_num DESC";
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (4=>$production_process,5=>$garments_item);
	echo  create_list_view("tbl_order_list", "Job,Year,Delivery Date,Order No,Process,Item,Order Qty, Style", "60,60,70,100,100,120,100,100","750","250",0, $sql , "js_set_value", "id,item_id", "", 1, "0,0,0,0,main_process_id,item_id,0,0", $arr , "job_no_prefix_num,year,order_rcv_date,order_no,main_process_id,item_id,order_quantity,cust_style_ref", "requires/subcon_gmts_delivery_controller",'','0,0,3,0,0,0,2,0') ;
	exit();
}
 
if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	//echo "select a.id, a.delivery_date, a.order_no, a.main_process_id, b.subcon_job, c.item_id, sum(c.qnty) as order_quantity from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c where a.main_process_id in (1,5,8,9,10,11) and a.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.company_id='$company' and a.id='$po_id' and c.item_id='$item_id' group by a.id, a.delivery_date, a.order_no, a.main_process_id, b.subcon_job, c.item_id";
	$res = sql_select("select a.id, a.delivery_date, a.order_no, a.main_process_id, b.subcon_job, c.item_id, sum(c.qnty) as order_quantity from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c where a.main_process_id in (1,5,8,9,10,11) and a.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and a.id='$po_id' and c.item_id='$item_id' and a.id=c.order_id and b.id=c.mst_id group by a.id, a.delivery_date, a.order_no, a.main_process_id, b.subcon_job, c.item_id"); 
	
 	foreach($res as $result)
	{
		echo "$('#txt_order_qty').val('".$result[csf('order_quantity')]."');\n";
		echo "$('#cbo_item_name').val(".$item_id.");\n";
		
		echo "$('#txt_order_no').val('".$result[csf('order_no')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_process_name').val('".$result[csf('main_process_id')]."');\n";
		echo "get_php_form_data(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_process_name').value,'load_variable_settings','requires/subcon_gmts_delivery_controller');\n";
		echo "$('#txt_job_no').val('".$result[csf('subcon_job')]."');\n";
		if ($result[csf('main_process_id')]==1)
		{
			$prod_type=1;
		}
		else if ($result[csf('main_process_id')]==5)
		{
			$prod_type=2;
		}
		else if ($result[csf('main_process_id')]==10)
		{
			$prod_type=3;
		}
		else if ($result[csf('main_process_id')]==11)
		{
			$prod_type=4;
		}

		$production_qty = return_field_value("sum(production_qnty)","subcon_gmts_prod_dtls","order_id=".$result[csf('id')]." and gmts_item_id='$item_id' and production_type='$prod_type' and status_active=1 and is_deleted=0");
 		if($production_qty=="")$production_qty=0;
		//echo $result[csf('id')].'='.$item_id;die;
		 $total_delivery = return_field_value("sum(delivery_qty) as delivery_qty ","subcon_delivery_dtls","order_id=".$result[csf('id')]." and item_id='$item_id'","delivery_qty");
		// echo $total_delivery.'Aziz';
		if($total_delivery=="")$total_delivery=0;
		
 		echo "$('#txt_prod_quantity').val('".$production_qty."');\n";
 		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_delivery."');\n";
		echo "$('#txt_cumul_quantity').val('".$total_delivery."');\n";
		$yet_to_produced = $production_qty-$total_delivery;
		echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
		echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
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
	$cbo_process_name = $dataArr[4];
	//echo $cbo_process_name;
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	//#############################################################################################//
	// order wise - color level, color and size level
	
	$delivery_value=array();
	
	//$variableSettings=2;
	
	if( $variableSettings==2 ) // color level
	{
		if($db_type==0) $group_cond=" GROUP BY a.color_id";	
		else if($db_type==2) $group_cond=" GROUP BY  a.id,a.item_id, a.color_id";	
					
		$sql = "SELECT a.id,a.item_id, a.color_id, sum(a.qnty) as qnty, sum(a.plan_cut) as plan_cut FROM subcon_ord_breakdown a left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id WHERE a.order_id ='$po_id' and a.item_id='$item_id' $group_cond";

		
		
	}
	else if( $variableSettings==3 ) //color and size level
	{
			
			$sql = "SELECT id, item_id, color_id, size_id, qnty as qnty, plan_cut as plan_cut FROM subcon_ord_breakdown WHERE order_id ='$po_id' and item_id='$item_id' ";
			//echo "select a.ord_color_size_id, sum(a.prod_qnty) as production_qnty from  subcon_gmts_prod_col_sz a, subcon_gmts_prod_dtls b where a.dtls_id=b.id and b.order_id='$po_id' and b.gmts_item_id='$item_id' and a.ord_color_size_id!=0 group by a.ord_color_size_id";
			
			if($cbo_process_name==1) $prod_type=1;
			else if($cbo_process_name==5) $prod_type=2;
			else if($cbo_process_name==10) $prod_type=3;
			else if($cbo_process_name==11) $prod_type=4;
			$prodData = sql_select("select a.ord_color_size_id, sum(a.prod_qnty) as production_qnty from  subcon_gmts_prod_col_sz a, subcon_gmts_prod_dtls b where a.dtls_id=b.id and b.order_id='$po_id' and b.gmts_item_id='$item_id' and a.production_type='$prod_type' and a.ord_color_size_id!=0 group by a.ord_color_size_id");
			
			foreach($prodData as $row)
			{				  
				$color_size_pro_qnty_array[$row[csf('ord_color_size_id')]]= $row[csf('production_qnty')];
			}
			$sql_del=sql_select("select a.item_id,a.color_id,a.size_id,sum(b.delivery_qty) as production_qnty from 
			subcon_ord_breakdown a left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id where a.order_id='$po_id' and a.item_id='$item_id' group by a.item_id, a.color_id, a.size_id");
			foreach($sql_del as $row_exfac)
			{
				$delivery_value[$row_exfac[csf("item_id")]][$row_exfac[csf("color_id")]][$row_exfac[csf("size_id")]]=$row_exfac[csf("production_qnty")];
				
			}
			
	
/*			$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id";
*/	}
	else // by default color and size level
	{
		
			
			if($cbo_process_name==1) $prod_type=1;
			else if($cbo_process_name==5) $prod_type=2;
			else if($cbo_process_name==10) $prod_type=3;
			else if($cbo_process_name==11) $prod_type=4;
			
			$prodData = sql_select("select a.ord_color_size_id, sum(a.prod_qnty) as production_qnty from  subcon_gmts_prod_col_sz a, subcon_gmts_prod_dtls b where a.dtls_id=b.id and b.order_id='$po_id' and b.gmts_item_id='$item_id' and a.production_type='$prod_type' and a.ord_color_size_id!=0 group by a.ord_color_size_id");
			foreach($prodData as $row)
			{				  
				$color_size_pro_qnty_array[$row[csf('ord_color_size_id')]]= $row[csf('production_qnty')];
			}
			
			
					
			$sql = "SELECT id, item_id, color_id, size_id, qnty as qnty, plan_cut as plan_cut FROM subcon_ord_breakdown WHERE order_id ='$po_id' and item_id='$item_id' ";
	}
	
	//print_r($ex_fac_value);die;
	
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
			
			
			 
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("plan_cut")]-$ex_fac_value[$color[csf('item_id')]][$color[csf('color_id')]]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';				
			$totalQnty += $color[csf("plan_cut")]-$ex_fac_value[$color[csf('item_id')]][$color[csf('color_id')]];
			$colorID .= $color[csf("color_id")].",";
		}
		else //color and size level
		{
			if( !in_array( $color[csf("color_id")], $chkColor ) )
			{
				if( $i!=0 ) $colorHTML .= "</table></div>";
				$i=0;
				$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_id")].'span">+</span>'.$color_library[$color[csf("color_id")]].' : <span id="total_'.$color[csf("color_id")].'"></span> </h3>';
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_id")].'">';
				$chkColor[] = $color[csf("color_id")];					
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_id")]."*".$color[csf("color_id")].",";
			
			$pro_qnty=$color_size_pro_qnty_array[$color[csf('id')]];
			$exfac_qnty=$delivery_value[$color[csf('item_id')]][$color[csf('color_id')]][$color[csf('size_id')]];
			
			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty-$exfac_qnty).'" onblur="fn_total('.$color[csf("color_id")].','.($i+1).')"><input type="hidden" name="hidden_ord_breakdown_id" id="hidden_ord_breakdown_id_'.$color[csf("id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$color[csf("id")].'" ></td></tr>';				
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

if($action=="populate_delivery_form_data")
{
	$delivery_value=array(); $amountArr=array();
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$data_arr=explode("__", $data);
	$data=$data_arr[0];
	$dtls_id=$data_arr[1];
	
	 $sqlResult =sql_select("SELECT a.id, b.id,b.order_id as po_id, b.item_id as item_number_id,b.process_id, a.location_id, a.delivery_date, b.delivery_qty, b.total_carton_qnty, a.challan_no, b.carton_roll, a.transport_company, b.entry_break_down_type  from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.id='$data' and b.id ='$dtls_id' and a.status_active=1 and a.is_deleted=0 order by a.id");
	
 	foreach($sqlResult as $result)
	{
		 
 		echo "$('#hidden_po_break_down_id').val('".$result[csf('po_id')]."');\n";
		echo "$('#txt_order_no').val('".$order_num_arr[$result[csf('po_id')]]."');\n";
 		echo "$('#txt_ctn_qnty').val('".$result[csf('carton_roll')]."');\n";
		echo "$('#txt_total_carton_qnty').val('".$result[csf('total_carton_qnty')]."');\n";
		echo "$('#txt_delivery_qty').val('".$result[csf('delivery_qty')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#cbo_process_name').val('".$result[csf('process_id')]."');\n";
		echo "get_php_form_data(document.getElementById('hidden_po_break_down_id').value+'**'+document.getElementById('cbo_item_name').value, 'populate_data_from_search_popup', 'requires/subcon_gmts_delivery_controller');\n";
		echo "$('#txt_dtls_id').val('".$result[csf('id')]."');\n";
	
		$process_id=$result[csf('process_id')];
				
 	
		//echo "$('#txt_transport_company').val('".$result[csf('transport_company')]."');\n";
		
		//echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_gmts_delivery',1,1);\n";
		
		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level

		$variableSettings = $result[csf('entry_break_down_type')];
		
		
		//$variableSettings=2;
		
		if( $variableSettings!=1 ) // gross level
		{ 
			$po_id = $result[csf('po_id')];
			$item_id = $result[csf('item_number_id')];
			
			
			//$sql_dtls = sql_select("select a.id,b.delivery_qty,a.size_id, a.color_id from  subcon_ord_breakdown a,subcon_gmts_delivery_dtls b where   a.id=b.breakdown_color_size_id and a.order_id='$po_id' and a.item_id='$item_id' ");	
			$sql_dtls = sql_select("select a.id,b.delivery_qty,a.size_id, a.color_id from  subcon_ord_breakdown a,subcon_gmts_delivery_dtls b where   a.id=b.breakdown_color_size_id and a.order_id='$po_id' and a.item_id='$item_id' and b.mst_id='$data' and b.dtls_mst_id='$dtls_id' ");
			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_id')]; else $index = $row[csf('size_id')].$row[csf('color_id')];
			  	 $amountArr[$index] = $row[csf('delivery_qty')];
				// echo  $index;
			}  
			
			if( $variableSettings==2 ) // color level
			{
				//if($db_type==2)
				//{
					
					if($process_id==1)
					{
					$sql = "select a.item_id, a.color_id, sum(a.qnty) as order_quantity, sum(a.plan_cut) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=1 then b.prod_qnty ELSE 0 END) as prod_qnty
							
							from subcon_ord_breakdown a 
							left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id
							where a.order_id='$po_id' and a.item_id='$item_id'  group by a.item_id, a.color_id";
					}
					else if($process_id==5)
					{
					$sql = "select a.item_id, a.color_id, sum(a.qnty) as order_quantity, sum(a.plan_cut) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=2 then b.prod_qnty ELSE 0 END) as prod_qnty
							from subcon_ord_breakdown a 
							left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id
							where a.order_id='$po_id' and a.item_id='$item_id'  group by a.item_id, a.color_id";
					}
					else if($process_id==10)
					{
					$sql = "select a.item_id, a.color_id, sum(a.qnty) as order_quantity, sum(a.plan_cut) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=3 then b.prod_qnty ELSE 0 END) as prod_qnty
							from subcon_ord_breakdown a 
							left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id
							where a.order_id='$po_id' and a.item_id='$item_id'  group by a.item_id, a.color_id";
					}
					else if($process_id==11)
					{
					$sql = "select a.item_id, a.color_id, sum(a.qnty) as order_quantity, sum(a.plan_cut) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=4 then b.prod_qnty ELSE 0 END) as prod_qnty
							from subcon_ord_breakdown a 
							left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id
							where a.order_id='$po_id' and a.item_id='$item_id'  group by a.item_id, a.color_id";
					}
							
					$sql_del=sql_select("select a.item_id,a.color_id,sum(b.delivery_qty) as delivery_qty from subcon_ord_breakdown a
							left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id
							where a.order_id='$po_id' and a.item_id='$item_id' and b.dtls_mst_id='$dtls_id'  group by a.item_id, a.color_id");
					foreach($sql_del as $row_d)
					{
						$delivery_value[$row_d[csf("item_id")]][$row_d[csf("color_id")]]=$row_d[csf("delivery_qty")];
						
					}
				//}
				
				
			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=8 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN ex.color_size_break_down_id=wo_po_color_size_breakdown.id then ex.production_qnty ELSE 0 END) from pro_ex_factory_dtls ex where ex.is_deleted=0 ) as ex_production_qnty  
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1"; */
					if($process_id==1)
					{
					 $prodData = "select b.ord_color_size_id,sum(a.production_qnty) as production_qnty
										from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b where  b.dtls_id=a.id and a.order_id='$po_id' and a.gmts_item_id='$item_id' and b.production_type=1 group by b.ord_color_size_id";
					}
					else if($process_id==5)
					{
					 $prodData = "select b.ord_color_size_id,sum(a.production_qnty) as production_qnty
										from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b where  b.dtls_id=a.id and a.order_id='$po_id' and a.gmts_item_id='$item_id' and b.production_type=2 group by b.ord_color_size_id";
					}
					else if($process_id==10)
					{
					 $prodData = "select b.ord_color_size_id,sum(a.production_qnty) as production_qnty
										from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b where  b.dtls_id=a.id and a.order_id='$po_id' and a.gmts_item_id='$item_id' and b.production_type=3 group by b.ord_color_size_id";
					}
					else if($process_id==11)
					{
					 $prodData = "select b.ord_color_size_id,sum(a.production_qnty) as production_qnty
										from  subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz b where  b.dtls_id=a.id and a.order_id='$po_id' and a.gmts_item_id='$item_id' and b.production_type=4 group by b.ord_color_size_id";
					}
					$result_prod_data=sql_select( $prodData);
					//echo $prodData;die;
			foreach($result_prod_data as $row)
			{				  
				$color_size_pro_qnty_array[$row[csf('ord_color_size_id')]]= $row[csf('production_qnty')];
			}
			
			$sql_del=sql_select("select a.item_id,a.color_id,a.size_id,sum(b.delivery_qty) as production_qnty from subcon_ord_breakdown a
                    left join subcon_gmts_delivery_dtls b on b.breakdown_color_size_id=a.id where a.order_id='$po_id' and a.item_id='$item_id' and b.dtls_mst_id='$dtls_id'   group by a.item_id, a.color_id, a.size_id");
			foreach($sql_del as $row_exfac)
			{
				$delivery_value[$row_exfac[csf("item_id")]][$row_exfac[csf("color_id")]][$row_exfac[csf("size_id")]]=$row_exfac[csf("production_qnty")];
				
			}
					
			$sql = "select id, item_id, size_id, color_id, qnty, plan_cut from subcon_ord_breakdown where order_id='$po_id' and item_id='$item_id'   order by color_id";
				
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
					$amount = $amountArr[$color[csf("color_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$delivery_value[$color[csf('item_id')]][$color[csf('color_id')]]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')">
				<input type="text" name="txt_color_size_id" id="txt_color_size_id_'.($i+1).'" style="width:80px"  class="text_boxes_numeric"  value="'.$color[csf("id")].'" >
					</td></tr>';				
					$totalQnty += $amount;
					$colorID .= $color[csf("color_id")].",";
				}
				else //color and size level
				{
					 $index = $color[csf("size_id")].$color[csf("color_id")];
					$amount = $amountArr[$index];
				//echo $amount.'hhhhh';
					if( !in_array( $color[csf("color_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_id")].'span">+</span>'.$color_library[$color[csf("color_id")]].' : <span id="total_'.$color[csf("color_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_id")].'">' ;
						$chkColor[] = $color[csf("color_id")];
						$totalFn .= "fn_total(".$color[csf("color_id")].");";
						
					}
 					$colorID .= $color[csf("size_id")]."*".$color[csf("color_id")].",";
					
					 $pro_qnty=$color_size_pro_qnty_array[$color[csf('id')]];
					$delivery_qnty=$delivery_value[$color[csf('item_id')]][$color[csf('color_id')]][$color[csf('size_id')]];
					//echo $pro_qnty.'p'.$delivery_qnty.'d'.$amount;
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty-$delivery_qnty+$amount).'" onblur="fn_total('.$color[csf("color_id")].','.($i+1).')" value="'.$amount.'" ><input type="hidden" name="hidden_ord_breakdown_id" id="hidden_ord_breakdown_id_'.$color[csf("id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("id")].'" ></td></tr>';				
					$colorWiseTotal += $amount;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="hidden" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}//end if condtion
	}
 	exit();		
}

//pro_ex_factory_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
  //      	 echo "pre";
	 // print_r($process);die;
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		//table lock here 
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		if(str_replace("'","",$txt_update_id)=="")
		{
			$id=return_next_id("id", "subcon_delivery_mst", 1);

			if($db_type==2) $mrr_cond="and  TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GDLV', date("Y",time()), 5, "select delivery_prefix, delivery_prefix_num from subcon_delivery_mst where company_id=$cbo_company_id  and process_id=3 $mrr_cond order by id DESC ", "delivery_prefix", "delivery_prefix_num" ));
			
			$field_array_delivery="id, delivery_prefix, delivery_prefix_num, delivery_no, company_id, location_id,process_id, challan_no, party_id, transport_company, delivery_date, vehical_no, forwarder,final_destination,lock_no,dl_no,do_no,attention,driver_name,mobile_no,remarks, inserted_by, insert_date";
			if(str_replace("'","",$txt_challan_no)=="")
			{
				$challan_no=$new_sys_number[2];
			}
			else
			{
				$challan_no=str_replace("'","",$txt_challan_no);
			}
			
			$data_array_delivery="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."', ".$cbo_company_id.",".$cbo_location_name.",3,'".$challan_no."',".$cbo_party_name.",".$txt_transport_company.",".$txt_delivery_date.",".$txt_vehical_no.",".$cbo_forwarder.",".$txt_final_destination.",".$txt_lock_no.",".$txt_dl_no.",".$txt_do_no.",".$txt_attention.",".$txt_driver_name.",".$txt_mobile_no.",".$txt_remarks.",".$user_id.",'".$pc_date_time."')";
			$mrr_no=$new_sys_number[0];
			$mrr_no_challan=$new_sys_number[2];
		}
		else
		{
			$mst_id=str_replace("'","",$txt_update_id);
			$mrr_no=str_replace("'","",$txt_sys_id);
			$mrr_no_challan=str_replace("'","",$txt_challan_no);
			$id=str_replace("'","",$txt_update_id);
			
			$field_array_delivery="location_id*challan_no*party_id*transport_company*delivery_date*vehical_no*forwarder*final_destination*lock_no*dl_no*do_no*attention*driver_name*mobile_no*remarks*updated_by*update_date";
			$data_array_delivery="".$cbo_location_name."*'".$mrr_no_challan."'*".$cbo_party_name."*".$txt_transport_company."*".$txt_delivery_date."*".$txt_vehical_no."*".$cbo_forwarder."*".$txt_final_destination."*".$txt_lock_no."*".$txt_dl_no."*".$txt_do_no."*".$txt_attention."*".$txt_driver_name."*".$txt_mobile_no."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
		}
		// $id=return_next_id("id", "subcon_delivery_mst", 1);
		$id_dtls=return_next_id("id", "subcon_delivery_dtls", 1);
		
  		$field_array1="id, mst_id, order_id, process_id, item_id, delivery_qty,entry_break_down_type, total_carton_qnty, carton_roll";

		$data_array1="(".$id_dtls.",".$id.",".$hidden_po_break_down_id.",".$cbo_process_name.",".$cbo_item_name.",".$txt_delivery_qty.",".$sewing_production_variable.",".$txt_total_carton_qnty.",".$txt_ctn_qnty.")";

		// $data_array1="(".$id_dtls.",".$mst_id.",".$hidden_po_break_down_id.",".$cbo_process_name.",".$cbo_item_name.",".$txt_delivery_qty.",".$sewing_production_variable.",".$txt_total_carton_qnty.",".$txt_ctn_qnty.")";

		//echo "INSERT INTO subcon_delivery_dtls (".$field_array1.") VALUES ".$data_array1;die;
		
 		//$rID=sql_insert("pro_ex_factory_mst",$field_array1,$data_array1,1);

		// pro_ex_factory_dtls table entry here ----------------------------------///
		$field_array="id, mst_id, dtls_mst_id,breakdown_color_size_id,delivery_qty";
  		
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{		
			$color_sizeID_arr=sql_select( "select id, color_id from subcon_ord_breakdown where order_id=$hidden_po_break_down_id and item_id=$cbo_item_name order by id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}	
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 			$rowEx = explode("**",$colorIDvalue); 
 			$size_dtls_id=return_next_id("id", "subcon_gmts_delivery_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				
				if($j==0)$data_array = "(".$size_dtls_id.",".$id.",".$id_dtls.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
				else $data_array .= ",(".$size_dtls_id.",".$id.",".$id_dtls.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
				$size_dtls_id=$size_dtls_id+1;							
 				$j++;								
			}
 		}
		else if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{		
			$color_sizeID_arr=sql_select( "select id, color_id, size_id from subcon_ord_breakdown where order_id=$hidden_po_break_down_id and item_id=$cbo_item_name order by color_id, size_id" );

			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_id")].$val[csf("color_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}	
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
 			$rowEx = explode("***",$colorIDvalue); 
			$size_dtls_id=return_next_id("id", "subcon_gmts_delivery_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];				
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$colorID;
 				
				if($j==0)$data_array = "(".$size_dtls_id.",".$id.",".$id_dtls.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array .= ",(".$size_dtls_id.",".$id.",".$id_dtls.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$size_dtls_id=$size_dtls_id+1;
 				$j++;
			}
		}
		
		//echo "INSERT INTO subcon_gmts_delivery_dtls (".$field_array.") VALUES ".$data_array;
		
		//subcon_delivery_mst
		//echo "10**";
		//echo "INSERT INTO subcon_gmts_delivery_dtls (".$field_array.") VALUES ".$data_array;
		//echo "INSERT INTO subcon_delivery_mst (".$field_array_delivery.") VALUES ".$data_array_delivery;
		$rID=sql_insert("subcon_delivery_dtls",$field_array1,$data_array1,1);
		$DeliveryrID=true;
		if(str_replace("'","",$txt_update_id)=="")
		{
			$DeliveryrID=sql_insert("subcon_delivery_mst",$field_array_delivery,$data_array_delivery,1);
		}
		else
		{
			$DeliveryrID=sql_update("subcon_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$txt_update_id,1);
		}
		//echo $DeliveryrID; die;
		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("subcon_gmts_delivery_dtls",$field_array,$data_array,1);
		} 	  
		
		$deliverID=true;
		if($id!="")
		{
 			$deliverID=sql_update("subcon_delivery_mst","delivery_date",$txt_delivery_date,"id",$id,1);
		} 	
		 
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		
		//echo "10**".$rID.'='.$DeliveryrID.'='.$dtlsrID.'='.$deliverID; die;
		
		if($db_type==0)
		{
		
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $DeliveryrID && $dtlsrID && $deliverID)
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID  && $DeliveryrID )
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$id)."**".$mrr_no."**".$mrr_no_challan;
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
				if($rID  && $DeliveryrID && $dtlsrID  && $deliverID)
				{
					oci_commit($con); 
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID  && $DeliveryrID )
				{
					oci_commit($con);  
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$id)."**".$mrr_no."**".$mrr_no_challan;
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
		
		
		$delivery_mst_id=str_replace("'","",$txt_update_id);
		$txt_mst_id=str_replace("'","",$txt_update_id);
		$mrr_no=str_replace("'","",$txt_system_no);
		$mrr_no_challan=str_replace("'","",$txt_challan_no);
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$details_level_id=str_replace("'","",$txt_dtls_id);
		//echo $sewing_production_variable;die;
		/*$buyer_id_chack=return_field_value("party_id","subcon_delivery_mst","id=$delivery_mst_id","party_id");
		if($buyer_id_chack!=$cbo_buyer_name)
		{
			echo "50";die;
		}*/
		//table lock here 
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		
		$field_array1="order_id*process_id*item_id*delivery_qty*entry_break_down_type*total_carton_qnty*carton_roll";
		$data_array1="".$hidden_po_break_down_id."*".$cbo_process_name."*".$cbo_item_name."*".$txt_delivery_qty."*".$sewing_production_variable."*".$txt_total_carton_qnty."*".$txt_ctn_qnty."";
		//print_r($data_array1);
		//'141'*'5'*'1'*'17'*'3'*'20'*'0'1**122****4
		$field_array_delivery="location_id*challan_no*party_id*transport_company*delivery_date*vehical_no*forwarder*final_destination*lock_no*dl_no*do_no*attention*driver_name*mobile_no*remarks*updated_by*update_date";
		$data_array_delivery="".$cbo_location_name."*".$txt_challan_no."*".$cbo_party_name."*".$txt_transport_company."*".$txt_delivery_date."*".$txt_vehical_no."*".$cbo_forwarder."*".$txt_final_destination."*".$txt_lock_no."*".$txt_dl_no."*".$txt_do_no."*".$txt_attention."*".$txt_driver_name."*".$txt_mobile_no."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
 		//$rID=sql_update("pro_ex_factory_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		//echo $data_array1;die;
		//141'*'5'*'1'*'0'*'3'*'10'*'67'
//echo $sewing_production_variable.'Aziz'.$txt_mst_id;die;
		// pro_ex_factory_mst table data entry here
		/*$country_order_qty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0");
		
		$country_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0 and id<>$txt_mst_id");
		$country_exfactory_qty=$country_exfactory_qty+str_replace("'","",$txt_ex_quantity);
		
		if($country_exfactory_qty>=$country_order_qty) $country_order_status=3; else $country_order_status=str_replace("'","",$shipping_status); 
		
		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name",1);

		$country_wise_status=return_field_value("count(id)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active=1 and is_deleted=0");
		if($country_wise_status>0) $order_status=2; else $order_status=3;
 		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id",1);
		
		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name",1);*/
		
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			// pro_ex_factory_dtls table entry here ----------------------------------///
			//echo $sewing_production_variable.'Aziz';die;
			$dtlsrDelete = execute_query("DELETE from subcon_gmts_delivery_dtls where mst_id=$txt_mst_id and dtls_mst_id=$details_level_id ",1);
			$field_array="id,mst_id,dtls_mst_id,breakdown_color_size_id,delivery_qty";
			
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,color_id from subcon_ord_breakdown where order_id=$hidden_po_break_down_id and item_id=$cbo_item_name order by id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue); 
				$dtls_id=return_next_id("id", "subcon_gmts_delivery_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",".$txt_dtls_id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",".$txt_dtls_id.",".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					$dtls_id=$dtls_id+1;							
					$j++;								
				}
				//print_r($data_array);die;
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,size_id,color_id from subcon_ord_breakdown where order_id=$hidden_po_break_down_id and item_id=$cbo_item_name order by size_id,color_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_id")].$val[csf("color_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				$rowEx = explode("***",$colorIDvalue); 
				//print_r($rowEx).'Aziz';die;
				$dtls_id=return_next_id("id", "subcon_gmts_delivery_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];				
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$colorID;
					
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",".$txt_dtls_id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",".$txt_dtls_id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					$dtls_id=$dtls_id+1;
					$j++;
				}
			}
			
			$rID=sql_update("subcon_delivery_dtls",$field_array1,$data_array1,"id","".$txt_dtls_id."",1);
			$$txt_update_id=true;
			$deliveryrID=sql_update("subcon_delivery_mst",$field_array_delivery,$data_array_delivery,"id","".$delivery_mst_id."",1);
			//echo $txt_update_id;die;	
			//echo $data_array."--";
			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
			{
				$dtlsrID=sql_insert("subcon_gmts_delivery_dtls",$field_array,$data_array,1);
			} 
			//$dtlsrID=sql_insert("pro_ex_factory_dtls",$field_array,$data_array,1);
		}//end cond
 		
		$deliverID=true;
		if($txt_mst_id!="")
		{
 			$deliverID=sql_update("subcon_delivery_mst","delivery_date",$txt_delivery_date,"id",$txt_mst_id,1);
		} 	
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $deliveryrID && $dtlsrID && $dtlsrDelete)
				{
					mysql_query("COMMIT");  
					//echo "1**".str_replace("'","",$txt_mst_id)."**".$mrr_no."**".$mrr_no_challan;
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_mst_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID && $deliveryrID)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_mst_id)."**".$mrr_no."**".$mrr_no_challan;
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
				if($rID && $deliveryrID && $dtlsrID && $dtlsrDelete)
				{
					oci_commit($con); 
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_mst_id)."**".$mrr_no."**".$mrr_no_challan;
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID && $deliveryrID)
				{
					oci_commit($con); 
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_mst_id)."**".$mrr_no."**".$mrr_no_challan;
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
	{/*
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$delivery_mst_id=str_replace("'","",$txt_update_id);
		$mrr_no=str_replace("'","",$txt_system_no);
		$mrr_no_challan=str_replace("'","",$txt_challan_no);
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";

		//$rID=sql_delete("pro_ex_factory_mst",$field_array,$data_array,"id","".$txt_mst_id."",1);
  		
		//$rID = sql_delete("pro_ex_factory_mst",$field_array,$data_array,"id",$txt_mst_id,1);
		//$dtlsrID = sql_delete("pro_ex_factory_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
 		
 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$hidden_po_break_down_id); 
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$delivery_mst_id)."**".$mrr_no; 
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id); 
			}
		}
		disconnect($con);
		die;
	*/}
}

function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
return $strQuery; die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}

if($action=="ex_factory_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$invoice_library=return_library_array( "select id, invoice_no from  com_export_invoice_ship_mst", "id", "invoice_no"  );
	$order_sql=sql_select("select a.id, a.po_number, b.buyer_name from  wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and b.status_active=1");
	foreach($order_sql as $row)
	{
		$order_job_arr[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$order_job_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
	}
	
	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$delivery_mst_sql=sql_select("select id, transport_supplier, driver_name, truck_no, dl_no, lock_no, destination_place,challan_no,sys_number_prefix_num from pro_ex_factory_delivery_mst where id=$data[1]");
	foreach($delivery_mst_sql as $row)
	{
		$supplier_name=$row[csf("transport_supplier")];
		$driver_name=$row[csf("driver_name")];
		$truck_no=$row[csf("truck_no")];
		$dl_no=$row[csf("dl_no")];
		$lock_no=$row[csf("lock_no")];
		$destination_place=$row[csf("destination_place")];
		$challan_no=$row[csf("challan_no")];
		$sys_number_prefix_num=$row[csf("sys_number_prefix_num")];
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
?>
<div style="width:710px;">
    <table width="700" cellspacing="0" align="right" style="margin-bottom:20px;">
        <tr>
            <td rowspan="2" align="center"><img src="../<? echo $image_location; ?>" height="50" width="60"></td>
            <td colspan="5" align="center"  style="font-size:xx-large; "><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="5" align="center" style="font-size:14px;">  
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
        	<?
				$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id=$supplier_name");
				foreach($supplier_sql as $row)
				{
				
				$address_1=$row[csf("address_1")];
				$address_2=$row[csf("address_2")];
				$address_3=$row[csf("address_3")];
				$address_4=$row[csf("address_4")];
				$contact_no=$row[csf("contact_no")];
				}
				//echo $supplier_sql;die;
            
            ?>
        <tr>
            <td colspan="5" style="font-size:x-large; padding-left:252px;"><strong>Delivery Challan<?// echo $data[3]; ?></strong></td>
            <td style="font-size:12px;">Date : <? echo change_date_format($data[2]); ?></td>
        </tr>
        <tr style="font-size:12px;">
        	<td width="80" valign="top"><strong>Name:</strong></td> 
            <td width="220" valign="top"><? echo $supplier_library[$supplier_name]; ?></td>
            <td width="80" valign="top"><strong>Challan No :</strong></td>
            <td width="120" valign="top"><? echo $challan_no; ?> </td>
            <td width="80" valign="top"><strong>DL/NO:</strong></td>
            <td valign="top"><? echo $dl_no; ?> </td>
        </tr>
			
        <tr style="font-size:12px;">
            <td valign="top"><strong>Address:</strong></td>
            <td colspan="3" valign="top"><? echo $address_1."<br>"; if($contact_no!="") echo "Phone : ".$contact_no; ?> </td>
            <td><strong>Truck No:</strong></td>
            <td ><? echo $truck_no; ?> </td>
        </tr>
        <tr style="font-size:12px;">
            <td><strong>Destination :</strong></td>
            <td ><? echo $destination_place; ?> </td>
            <td  valign="top"><strong >Driver Name :</strong></td>
            <td  valign="top"><? echo $driver_name; ?> </td>
            <td><strong >Lock No :</strong></td>
            <td ><? echo $lock_no; ?> </td>
        </tr>
    </table><br>
        <?
		//listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id
		if($db_type==2)
		{
			$sql="SELECT po_break_down_id, listagg(CAST(invoice_no as VARCHAR(4000)),',') within group (order by invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty from pro_ex_factory_mst where delivery_mst_id=$data[1]  and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		else if($db_type==0)
		{
			$sql="SELECT po_break_down_id, group_concat(invoice_no) as invoice_no, sum(ex_factory_qnty) as ex_factory_qnty, sum(total_carton_qnty) as total_carton_qnty from pro_ex_factory_mst where delivery_mst_id=$data[1] and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		//echo $sql;die;
		$result=sql_select($sql);
			
		?> 
         
	<div style="width:700px;">
    <table align="right" cellspacing="0" width="700"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="50">SL</th>
            <th width="140" >Order No</th>
            <th width="140" >Buyer</th>
            <th width="280" >Invoice No</th>
            <th  >NO Of Carton</th>
        </thead>
        <tbody style="font-size:12px;">
		<?
        $i=1;
        $tot_qnty=array();
        foreach($result as $row)
        {
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
            $color_count=count($cid);
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i;  ?></td>
                <td><? echo $order_job_arr[$row[csf("po_break_down_id")]]['po_number']; ?></td>
                <td><? echo $buyer_library[$order_job_arr[$row[csf("po_break_down_id")]]['buyer_name']]; ?></td>
                <td>
				<?
				 $invoice_id="";
				 $invoice_id_arr=array_unique(explode(",",$row[csf("invoice_no")]));
				 foreach($invoice_id_arr as $inv_id)
				 {
					 if($invoice_id=="") $invoice_id=$invoice_library[$inv_id]; else $invoice_id=$invoice_id.",".$invoice_library[$inv_id];
					 
				 }
				 echo $invoice_id;
				?></td>
                <td align="right"><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></td>
            </tr>
            <?
            $i++;
        }
        ?>
        </tbody>
        
        <tr>
            <td colspan="4" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
        </tr>                           
    </table>
	</div>
		 <?
            echo signature_table(63, $data[0], "700px");
         ?>
	</div>
<?
exit();	
}

if($action=="gmts_delivery_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents("Garments Delivery Info","../", 1, 1, $unicode,'','');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from   lib_buyer", "id", "buyer_name"  );
	$buyer_address_library=return_library_array( "select id, address_1 from   lib_buyer", "id", "address_1"  );
	$location_library=return_library_array( "select id,location_name from  lib_location", "id","location_name"  );
	//echo "select transport_supplier from pro_ex_factory_delivery_mst where id=$data[1]";die;
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
?>
<div style="width:710px;">
    <table width="700" cellspacing="0" align="right" style="margin-bottom:20px;">
        <tr>
            <td rowspan="2" align="center"><img src="../../<? echo $image_location; ?>" height="50" width="60"></td>
            <td colspan="5" align="center"  style="font-size:xx-large; "><strong><? echo $company_library[$data[0]]; ?></strong></td>
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
        	<?
				$supplier_sql=sql_select("select id, supplier_name, contact_person, contact_no, designation, email, address_1, address_2, address_3, address_4 from  lib_supplier where id='$supplier_name'");
				foreach($supplier_sql as $row)
				{
				
				$address_1=$row[csf("address_1")];
				$address_2=$row[csf("address_2")];
				$address_3=$row[csf("address_3")];
				$address_4=$row[csf("address_4")];
				$contact_no=$row[csf("contact_no")];
				}
				//echo $supplier_sql;die;
            
            ?>
        <tr>
            <td colspan="6" style="font-size:x-large; padding-left:252px; text-align:center;"><strong><? echo $data[2]; ?></strong></td>
            
        </tr>
        <? 
		
		$sql_master ="SELECT a.id,a.party_id,b.order_id,a.challan_no,b.item_id,a.delivery_date,a.transport_company,a.vehical_no,a.location_id,a.company_id,a.final_destination,a.lock_no,a.dl_no,a.do_no,a.attention,a.driver_name,a.mobile_no,a.remarks,b.process_id,b.total_carton_qnty,b.delivery_qty  from  subcon_delivery_mst a,  subcon_delivery_dtls b where a.id=b.mst_id and  a.delivery_no='".$data[1]."' and a.status_active=1 and a.is_deleted=0 order by a.id";
		$result_master=sql_select($sql_master);
		$all_po_id="";
		foreach($result_master as $v)
		{
			if($all_po_id) $all_po_id.=",".$v[csf("order_id")];
			else $all_po_id.=$v[csf("order_id")];

		}

		$poID=$result_master[0][csf("order_id")];
		/*$sql_wo_po_break =sql_select("select a.id,a.po_number,a.job_no_mst from wo_po_break_down a where a.id=$poID");
		foreach($sql_wo_po_break as $poData)
		{
			$po_data_arr[$poData[csf("id")]]["po_number"]=$poData[csf("po_number")];
			$po_data_arr[$poData[csf("id")]]["job_no_mst"]=$poData[csf("job_no_mst")];
		}*/

		$sql_wo_po_break =sql_select("select a.id,a.order_no as po_number,a.job_no_mst from subcon_ord_dtls a where a.id in($all_po_id)");
		//echo "select a.id,a.order_no as po_number,a.job_no_mst from subcon_ord_dtls a where a.id in($all_po_id)";
		foreach($sql_wo_po_break as $poData)
		{
			$po_data_arr[$poData[csf("id")]]["po_number"]=$poData[csf("po_number")];
			$po_data_arr[$poData[csf("id")]]["job_no_mst"]=$poData[csf("job_no_mst")];
		}

		?>
        <tr style="font-size:12px;">
        	<td width="120" valign="top"><strong>Party Name:</strong></td> 
            <td width="220" valign="top"><? echo $buyer_library[$result_master[0][csf("party_id")]]; ?></td>
            <td width="120" valign="top"><strong>Challan No :</strong></td>
            <td width="120" valign="top"><? echo $result_master[0][csf("challan_no")]; ?> </td>
            <td width="120" valign="top"><strong>Delivery Date:</strong></td>
            <td valign="top" width="120"><? echo change_date_format($result_master[0][csf("delivery_date")]); ?> </td>
        </tr>
			
        <tr style="font-size:12px;">
            <td valign="top" width="120"><strong>Address:</strong></td>
            <td valign="top"><? echo $buyer_address_library[$result_master[0][csf("party_id")]]; ?> </td>
            <td><strong>Transport Com.</strong></td>
            <td ><? echo $result_master[0][csf("transport_company")]; ?> </td>
            <td><strong>Vehicle No:</strong></td>
            <td ><? echo $result_master[0][csf("vehical_no")]; ?> </td>
        </tr>
        <tr style="font-size:12px;">
            <td valign="top" width="120"><strong>Delivery Company:</strong></td>
            <td valign="top"><? echo $company_library[$result_master[0][csf("company_id")]]; ?> </td>
            <td><strong>Driver Name :</strong></td>
            <td ><? echo $result_master[0][csf("driver_name")]; ?> </td>
            <td><strong>DL No. :</strong></td>
            <td ><? echo $result_master[0][csf("dl_no")]; ?> </td>
        </tr>
        <tr style="font-size:12px;">
            <td><strong>Delivery Location :</strong></td>
            <td ><? echo $location_library[$result_master[0][csf("location_id")]]; ?> </td>
            <td><strong>Mobile No :</strong></td>
            <td ><? echo $result_master[0][csf("mobile_no")]; ?> </td>
            <td><strong>Lock No. :</strong></td>
            <td ><? echo $result_master[0][csf("lock_no")]; ?> </td>
        </tr>
        <tr style="font-size:12px;">
            <td><strong>Final Destination :</strong></td>
            <td ><? echo $result_master[0][csf("final_destination")]; ?> </td>
            <td><strong>DO No :</strong></td>
            <td ><? echo $result_master[0][csf("do_no")]; ?> </td>
            <td><strong>Attention :</strong></td>
            <td ><? echo $result_master[0][csf("attention")]; ?> </td>
        </tr>
        <tr style="font-size:12px;">
            <td><strong>Remarks :</strong></td>
            <td colspan="5"><? echo $result_master[0][csf("remarks")]; ?> </td>
        </tr>
    </table>
    <br>

	<div style="width:860px;">
    <table align="right" cellspacing="0" width="860"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="50">SL</th>
            <th width="140" >Item Name</th>
            <th width="140" >Job No</th>
            <th width="140" >Order No</th>
            <th width="140" >Process</th>
            <th width="140" >Total Carton Qty</th>
            <th >Delivery Qty</th>
        </thead>
        <tbody style="font-size:12px;">
		<?
        $i=1;
        $tot_qnty=array();
        foreach($result_master as $row)
        {
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
            $color_count=count($cid);
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i;  ?></td>
                <td><? echo $garments_item[$row[csf("item_id")]]; ?></td>
                <td><? echo $po_data_arr[$row[csf("order_id")]]["job_no_mst"]; ?></td>
                <td><? echo $po_data_arr[$row[csf("order_id")]]["po_number"]; ?></td>
                <td align="center"><? echo $production_process[$row[csf("process_id")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("total_carton_qnty")],0,"",""); $tot_carton_qnty +=$row[csf("total_carton_qnty")]; ?></td>
                <td align="right"><? echo number_format($row[csf("delivery_qty")],0,"",""); $tot_del_qnty +=$row[csf("delivery_qty")]; ?></td>

            </tr>
            <?
            $i++;
        }
        ?>
        </tbody>
        
        <tr>
            <td colspan="5" align="right"><strong>Grand Total :</strong></td>
            <td align="right"><? echo number_format($tot_carton_qnty,0,"",""); ?></td>
            <td align="right"><? echo number_format($tot_del_qnty,0,"",""); ?></td>
        </tr>                           
    </table>
	</div>
    	<div style=" margin-left:0px;">
		 <?
            echo signature_table(63, $data[0], "860px","","50");
         ?>
         </div>
	</div>
<?
exit();	
}
?>