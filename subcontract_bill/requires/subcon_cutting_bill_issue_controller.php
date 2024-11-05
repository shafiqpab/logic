<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active=1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_party_name")
{
	$data=explode('_',$data);
	if($data[1]==2)
	{
		/*echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "show_list_view(document.getElementById('cbo_party_source').value+'***'+this.value,'cutting_delivery_list_view','cutting_info_list','requires/subcon_cutting_bill_issue_controller','setFilterGrid(\'list_view_issue\',-1)');","","","","","",5 ); */
		
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 ); 
		
	}
	else if($data[1]==1)
	{
		/*echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Party --", $selected, "show_list_view(document.getElementById('cbo_party_source').value+'***'+this.value,'cutting_delivery_list_view','cutting_info_list','requires/subcon_cutting_bill_issue_controller','setFilterGrid(\'list_view_issue\',-1)');","","","","","",5 ); 
		*/
		
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Party --", $selected, "","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
	exit();
}

if ($action=="load_drop_down_party_name_popup")
{
	$data=explode('_',$data);
	echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 );
	exit(); 
}

if ($action=="bill_no_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('issue_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="cuttingbill_1"  id="cuttingbill_1" autocomplete="off">
                <table width="630" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Party Name</th>
                        <th width="80">Bill ID</th>
                        <th width="170">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="hidden" id="issue_id">  
								<?   
									echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'subcon_cutting_bill_issue_controller', this.value, 'load_drop_down_party_name_popup', 'party_td' );",0 );
                                ?>
                            </td>
                            <td width="140" id="party_td">
								<?
									echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $ex_data[1], "","","","","","",5 );
                                ?> 
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'cutting_bill_list_view', 'search_div', 'subcon_cutting_bill_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
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
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="cutting_bill_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name=" and party_id='$data[1]'"; else $party_name="";
	if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[3], "mm-dd-yyyy", "/",1)."'"; else $return_date="";
	if ($data[4]!='') $bill_id_cond=" and prefix_no_num='$data[4]'"; else $bill_id_cond="";
	
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	
	$arr=array (2=>$location,4=>$party_arr,5=>$knitting_source,6=>$bill_for);
	if($db_type==0)
	{
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$sql= "select id, bill_no, prefix_no_num, $year_cond, location_id, bill_date, party_id, party_source, bill_for from subcon_inbound_bill_mst where status_active=1 and process_id=1 $company_name $party_name $return_date $bill_id_cond order by id DESC";
	
	echo  create_list_view("list_view", "Bill No,Year,Location,Bill Date,Party,Source,Bill For", "50,40,80,70,100,120,140","630","250",0, $sql , "js_set_value", "id", "", 1, "0,0,location_id,0,party_id,party_source,bill_for", $arr , "prefix_no_num,year,location_id,bill_date,party_id,party_source,bill_for", "subcon_cutting_bill_issue_controller","",'0,0,0,3,0,0,0') ;
	exit();
}

if ($action=="load_php_data_to_form_issue")
{
	
	$sql="SELECT min(delivery_date) as min_date, max(delivery_date) as max_date FROM subcon_inbound_bill_dtls WHERE mst_id='$data' and status_active=1 and is_deleted=0 group by mst_id";
	
	$sql_result_arr =sql_select($sql); 
	$mindate='';  $maxdate='';
	$mindate=$sql_result_arr[0][csf('min_date')];
	$maxdate=$sql_result_arr[0][csf('max_date')];
	unset($sql_result_arr);
	
	$nameArray= sql_select("select id, bill_no, company_id, location_id, bill_date, party_id, party_source, bill_for, is_posted_account , post_integration_unlock from subcon_inbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_cutting_bill_issue_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "document.getElementById('cbo_party_source').value				= '".$row[csf("party_source")]."';\n"; 
		echo "load_drop_down( 'requires/subcon_cutting_bill_issue_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_source').value, 'load_drop_down_party_name', 'party_td' );\n";
		echo "document.getElementById('hidden_acc_integ').value				= '".$row[csf("is_posted_account")]."';\n";
		echo "document.getElementById('hidden_integ_unlock').value			= '".$row[csf("post_integration_unlock")]."';\n";
		
		if($row[csf("is_posted_account")]==1 && $row[csf("post_integration_unlock")]==0)
		{
			echo "$('#accounting_integration_div').text('All Ready Posted in Accounting.');\n"; 
		}
		else if($row[csf("is_posted_account")]==1 && $row[csf("post_integration_unlock")]==1)
		{
			echo "$('#accounting_integration_div').text('Deleting not allowed since posted in Accounts.Only Data changing is allowed.');\n"; 
		}
		else 
		{
			echo "$('#accounting_integration_div').text('');\n"; 
		}
		echo "document.getElementById('txt_bill_form_date').value 			= '".change_date_format($mindate)."';\n";  
		echo "document.getElementById('txt_bill_to_date').value 			= '".change_date_format($maxdate)."';\n";   
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('cbo_bill_for').value					= '".$row[csf("bill_for")]."';\n"; 
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
	}
	exit();	
}

if ($action=="cutting_delivery_list_view")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,1,'');
	$data=explode('***',$data);
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	$date_from=$data[5];
	$date_to=$data[6];
	if(!empty($data[2]))
	{
		$company_id=$data[4];
	}
	else{
		$company_id=$data[7];
	}
	
	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") $date_cond= " and production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
		$year_cond= "year(b.insert_date)as year";

	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!="") $date_cond= " and production_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	if($data[0]==2)
	{
		?>
		</head>
		<body>
        <div>
            <div style="width:100%;">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="817px" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="80">Challan No</th>
						<th width="80">Delivery Date</th>
						<th width="150">Order No</th>                    
						<th width="200">Fabric Description</th>
						<th width="100">Delivery Qty</th>
						<th width="100">Process</th>
						<th width="" >Currency</th>
					</thead>
			 </table>
        </div>
        <div style="width:820px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800px" class="rpt_table" id="list_view_issue">
			 <?
				$order_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$currency_arr=return_library_array( "select b.id, a.currency_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','currency_id');
                $i=1;
                $date_cond=str_replace("production_date", "a.delivery_date", $date_cond);
				if(!$data[2])
				{
					$sql="select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where b.bill_status=0 and a.id=b.mst_id and a.company_id='$company_id' and a.party_id='$data[1]' and b.process_id='1' and a.status_active=1 and a.is_deleted=0 $date_cond order by b.id"; 
				}
				else
				{
					$sql="(select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.order_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.party_id='$data[1]' and b.process_id='1' and a.status_active=1 and b.bill_status='0' $date_cond)
					 union 
					 	(select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.order_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.party_id='$data[1]' and b.process_id='1' and b.id in ( $data[3] ) and a.status_active=1 $date_cond)  order by id";
				}
				 //echo $sql;
				$sql_result =sql_select($sql);
                foreach($sql_result as $row)
				{
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr id="tr_<?  echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."***".$currency_arr[$row[csf('order_id')]]; ?>');" > 
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="80" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                        <td width="150" align="center"><? echo $order_arr[$row[csf('order_id')]]; ?></td>
                        <?
                        $process_id_val=$row[csf('process_id')];
						if($process_id_val==1 || $process_id_val==5)
						{
							$item_id_arr=$garments_item;
						}
						else
						{
							$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
						}
						?>
                        <td width="200" align="center"><? echo $item_id_arr[$row[csf('item_id')]]; ?></td>
                        <td width="100" align="right"><? echo $row[csf('delivery_qty')]; ?>&nbsp;</td>
                        <td width="100" align="center"><? echo $production_process[$row[csf('process_id')]]; ?></td>
                        <td width="" align="center"><? echo $currency[$currency_arr[$row[csf('order_id')]]]; ?>
                        <input type="hidden" id="currid<? echo $row[csf('id')]; ?>" value="<? echo $currency_arr[$row[csf('order_id')]]; ?>"></td>
                    </tr>
                    <?php
                    $i++;
                }
				?>
            </table>
            </div>
            <table width="800">
                <tr>
                    <td align="center" colspan="8" >
                         <input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close()" />
                    </td>
                </tr>
           </table>
      </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	}
	else if($data[0]==1)
	{
		?>
		</head>
		<body>
        <div>
            <div style="width:100%;">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910px" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="70">Challan No</th>
						<th width="70">Prod. Date</th>
                        <th width="70">Job No</th>
                        <th width="100">Style Ref.</th>
                        <th width="110">Buyer</th> 
						<th width="110">Order No</th>                    
						<th width="100">Gmts. Item</th>
						<th width="90">Prod. Qty</th>
                        <th>Country</th>
					</thead>
			 </table>
        </div>
        <div style="width:910px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="892px" class="rpt_table" id="list_view_issue">
			 <?
				$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
				$country_arr=return_library_array("select id, country_name from lib_country",'id','country_name');
			 
			 	$job_order_arr=array();
				$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
				$sql_job_result =sql_select($sql_job);
				foreach($sql_job_result as $row)
				{
					$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
					$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
					$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
					$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
				}
				
                $i=1;
				if(!$data[2])
				{
					//$sql="select challan_no || '_' || production_date || '_' || po_break_down_id || '_' || country_id || '_' ||  item_number_id  as delivery_id, challan_no, production_date, po_break_down_id, country_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where serving_company=$data[1] $date_cond and production_source=1 and produced_by=1 and production_type=1 and status_active=1 and is_deleted=0 group by challan_no, production_date, po_break_down_id, country_id, item_number_id order by challan_no, production_date"; 
					
					$sql="select id as delivery_id, challan_no, production_date, po_break_down_id, item_number_id, serving_company, country_id, production_quantity from pro_garments_production_mst where  serving_company=$data[1] and company_id='$company_id'  $date_cond and production_source=1 and produced_by=1 and production_type=5 and status_active=1 and is_deleted=0 order by id DESC"; 
				}
				else
				{
					//$sql="(select challan_no, production_date, po_break_down_id, item_number_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where serving_company=$data[1] $date_cond and production_source=1 and produced_by=1 and production_type=1 and status_active=1 and is_deleted=0 group by challan_no, production_date, po_break_down_id, item_number_id order by challan_no, production_date)";
					$sql="select id, challan_no, production_date, po_break_down_id, item_number_id, serving_company, country_id, production_quantity from pro_garments_production_mst where serving_company=$data[1] and company_id='$company_id' $date_cond and production_source=1 and produced_by=1 and production_type=5 and status_active=1 and is_deleted=0 order by id DESC";
					
				}
				// echo $sql;
				$sql_result =sql_select($sql);
                foreach($sql_result as $row)
				{
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr id="tr_<?  echo $row[csf('delivery_id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('delivery_id')]."***".'1'; ?>');" > 
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="70" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="70"><? echo change_date_format($row[csf('production_date')]); ?></td>
                        <td width="70"><p><? echo $job_order_arr[$row[csf('po_break_down_id')]]['job']; ?></p></td>
                        <td width="100"><p><? echo $job_order_arr[$row[csf('po_break_down_id')]]['style']; ?></p></td>
                        <td width="110"><p><? echo $buyer_arr[$job_order_arr[$row[csf('po_break_down_id')]]['buyer']]; ?></p></td>
                        <td width="110"><p><? echo $job_order_arr[$row[csf('po_break_down_id')]]['po']; ?></p></td>
                        <td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                        <td width="90" align="right"><? echo $row[csf('production_quantity')]; ?>&nbsp;</td>
                        <td><p><? echo $country_arr[$row[csf('country_id')]]; ?>
                        <input type="hidden" id="currid<? echo $row[csf('delivery_id')]; ?>" value="<? echo '1'; ?>"></p></td>
                    </tr>
                    <?php
                    $i++;
                }
				?>
            </table>
            </div>
            <table width="910">
                <tr>
                    <td align="center" colspan="10" >
                         <input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close()" />
                    </td>
                </tr>
           </table>
      </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	}
	exit();
}

if ($action=="load_php_dtls_form")  //new issue
{
	$data = explode("_",$data);
	
	//$old_selected_id="'".implode("','",explode(",",$data[0]))."'";
	//$old_issue_id="'".implode("','",explode(",",$data[1]))."'";
	$del_id=array_diff(explode(",",$data[0]), explode(",",$data[1]));
	//$bill_id=array_intersect(explode(",",$data[0]), explode(",",$data[1]));
	$bill_id=array_intersect(explode(",",$old_selected_id), explode(",",$old_issue_id));
	$delete_id=array_diff(explode(",",$data[1]), explode(",",$data[0]));
	$del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id); $delete_id=implode(",",$delete_id);
	
	
	//print_r()[
	
	
		if($data[3]==2)
		{
			$order_array=array();
			$order_sql="Select b.id, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref, b.rate, b.amount, a.currency_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach ($order_sql_result as $row)
			{
				$order_array[$row[csf("id")]]['order_no']=$row[csf("order_no")];
				$order_array[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
				$order_array[$row[csf("id")]]['cust_buyer']=$row[csf("cust_buyer")];
				$order_array[$row[csf("id")]]['cust_style_ref']=$row[csf("cust_style_ref")];
				$order_array[$row[csf("id")]]['rate']=$row[csf("rate")];
				$order_array[$row[csf("id")]]['amount']=$row[csf("amount")];
				$order_array[$row[csf("id")]]['currency_id']=$row[csf("currency_id")];
			}
			//var_dump($order_array);die;
			
			if( $data[2]!="" )
			{
				$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, currency_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 and process_id=1 order by id"; 
			}
			else
			{
				if($bill_id!="" && $del_id!="")
					$sql="(select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, currency_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id=1)
					 union
					 (select 0, listagg(b.id,'_') within group (order by b.id) as delivery_id, a.delivery_date, a.challan_no, listagg(b.item_id,'_') within group (order by b.item_id) as item_id, b.carton_roll, b.delivery_qty, 0, 0, null, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.id in ($del_id) and a.status_active=1 and b.process_id=1 group by b.id, a.delivery_date, a.challan_no, b.item_id, b.carton_roll, b.delivery_qty, b.order_id) order by upd_id";
				else if($bill_id!="" && $del_id=="")
					$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, currency_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id=1";
				else  if($bill_id=="" && $del_id!="")
					$sql="select 0, b.id as delivery_id, a.delivery_date, a.challan_no, b.item_id, b.carton_roll, b.delivery_qty, 0, 0, 0, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.id in ($del_id) and a.status_active=1 and a.is_deleted=0 and b.process_id=1"; 
			}
			//echo $sql;
			$sql_result =sql_select($sql);	
			$k=0;
			$num_rowss=count($sql_result);
			foreach ($sql_result as $row)
			{
				$k++;
				if( $data[2]!="" )
				{
					if($data[1]=="") $data[1]=$row[csf("delivery_id")]; else $data[1].=",".$row[csf("delivery_id")];
				}
			?>
				 <tr align="center">				
					<td>
						<? if ($k==$num_rowss) { ?>
							<input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:80px" value="<? echo $data[1]; ?>" />
							<input type="hidden" name="delete_id" id="delete_id"  style="width:80px" value="<? echo $delete_id; ?>" />
						<? } ?>
						<input type="hidden" name="curanci_<? echo $k; ?>" id="curanci_<? echo $k; ?>"  style="width:80px" value="<? echo $order_array[$row[csf("order_id")]]['currency_id']; ?>" />
						<input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
						<input type="hidden" name="deliveryid_<? echo $k; ?>" id="deliveryid_<? echo $k; ?>" value="<? echo $row[csf("delivery_id")]; ?>">
						<input type="text" name="deleverydate_<? echo $k; ?>" id="deleverydate_<? echo $k; ?>"  class="datepicker" style="width:65px" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" disabled />									
					</td>
					<td>
						<input type="text" name="challenno_<? echo $k; ?>" id="challenno_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
					</td>
					<td>
						<input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>">
						<input type="text" name="orderno_<? echo $k; ?>" id="orderno_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $order_array[$row[csf("order_id")]]['order_no']; ?>" readonly />										
					</td>
					<td>
						<input type="text" name="stylename_<? echo $k; ?>" id="stylename_<? echo $k; ?>"  class="text_boxes" style="width:80px;" value="<? echo $order_array[$row[csf("order_id")]]['cust_style_ref']; ?>" readonly />
					</td>
					<td>
						<input type="text" name="buyername_<? echo $k; ?>" id="buyername_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $order_array[$row[csf("order_id")]]['cust_buyer']; ?>" readonly />								
					</td>
					<td>			
						<input name="numberroll_<? echo $k; ?>" id="numberroll_<? echo $k; ?>" type="text" class="text_boxes" style="width:45px" value="<? echo $row[csf("carton_roll")]; ?>" readonly />							
					</td> 
					<td>
						<input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("item_id")]; ?>">
						<input type="text" name="yarndesc_<? echo $k; ?>" id="yarndesc_<? echo $k; ?>"  class="text_boxes" style="width:125px" value="<? echo $garments_item[$row[csf("item_id")]]; ?>" readonly/>
					</td>
					<td>
						<? echo create_drop_down( "cbouom_.$k", 40, $unit_of_measurement,"", 0, "--Select UOM--",1,"",1,$order_array[$row[csf("order_id")]]['order_uom'],"" );?>
					</td>
					<td>
						<input type="text" name="deliveryqnty_<? echo $k; ?>" id="deliveryqnty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("delivery_qty")]; ?>" readonly />
					</td>
					<td>
						<input type="text" name="txtrate_<? echo $k; ?>" id="txtrate_<? echo $k; ?>"  class="text_boxes_numeric" style="width:40px;" value="<? echo $order_array[$row[csf("order_id")]]['rate']; ?>" readonly />
					</td>
						<?
							$total_amount=$row[csf("delivery_qty")]*$order_array[$row[csf("order_id")]]['rate'];
						?>
					<td>
						<input type="text" name="amount_<? echo $k; ?>" id="amount_<? echo $k; ?>" style="width:60px;"  class="text_boxes_numeric"  value="<? echo $total_amount; ?>" readonly />
					</td>
					<td>
					<? 
						//$order_array[$row[csf("order_id")]]['currency_id']
						echo create_drop_down( "cbo_curanci_$k", 60, $currency,"", 1, "-Select Currency-",$row[csf("currency_id")],"",0,"" );
					?>
					</td>
					<td>
						<input type="text" name="remarks_<? echo $k; ?>" id="remarks_<? echo $k; ?>" value="<? echo $row[csf("remarks")]; ?>"  class="text_boxes" style="width:80px" />
					</td>
				</tr>
			<?	
			}
		}
		else
		{
		$old_selected_id="'".implode("','",explode(",",$data[0]))."'";
		$old_issue_id="'".implode("','",explode(",",$data[1]))."'";
		
		$data_selected=implode(',',explode('_',$data[0]));
		$data_issue=implode(',',explode('_',$data[1]));
		
		$delv_id=array_diff(explode(",",$data_selected), explode(",",$data_issue));
		$billiss_id=array_intersect(explode(",",$old_selected_id), explode(",",$old_issue_id));
		
		$delv_id=implode(",",$delv_id); $billiss_id=implode(",",$billiss_id);
		//echo $delv_id.'=='.$billiss_id;
		$job_array=array();
		$job_sql="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
		$job_sql_result =sql_select($job_sql);
		foreach ($job_sql_result as $row)
		{
			$job_array[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
			$job_array[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
			$job_array[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$job_array[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		//var_dump($job_array);die;
		if($db_type==0)
		{
			$delivery_id_cond=" group_concat(id)";
			$item_id_cond="group_concat(item_number_id)";
			$challan_cond="group_concat(item_number_id)";
		}
		else if ($db_type==2)
		{
			$delivery_id_cond="listagg(id,',') within group (order by id)";
			$item_id_cond="listagg(item_number_id,',') within group (order by item_number_id)";
			$challan_cond="listagg(item_number_id,',') within group (order by item_number_id)";
		}
		
		if( $data[2]!="" )
		{
			$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, currency_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 and process_id=1"; 
		}
		else
		{
			if($billiss_id!="" && $delv_id!="")
				$sql="(select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, currency_id from subcon_inbound_bill_dtls where delivery_id in ($billiss_id) and status_active=1 and is_deleted=0 and process_id=5 )
				 union
				 (select 0 as upd_id, $delivery_id_cond as delivery_id, production_date as delivery_date, id as challan_no, $item_id_cond as item_id, null as carton_roll, production_quantity as delivery_qty, 0, 0, null, po_break_down_id as order_id from  pro_garments_production_mst where id in ($delv_id) and production_source=1 and produced_by=1 and production_type=5 and status_active=1 and is_deleted=0 group by id, production_date, item_number_id, production_quantity, po_break_down_id )";
			else if($billiss_id!="" && $delv_id=="")
				$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, currency_id from subcon_inbound_bill_dtls where delivery_id in ($billiss_id) and status_active=1 and is_deleted=0 and process_id=5";
			else  if($billiss_id=="" && $delv_id!="")
				$sql="select 0, id as delivery_id, production_date as delivery_date, id as challan_no, item_number_id as item_id, null as carton_roll, production_quantity as delivery_qty, 0, 0, null, po_break_down_id as order_id from  pro_garments_production_mst where id in ($delv_id) and production_source=1 and produced_by=1 and production_type=5 and status_active=1 and is_deleted=0"; 
		}
		//echo $sql; die;
		$sql_result =sql_select($sql);	
		$k=0;
		$num_rowss=count($sql_result);
		foreach ($sql_result as $row)
		{
			$k++;
			if( $data[2]!="" )
			{
				if($data[1]=="") $data[1]=$row[csf("delivery_id")]; else $data[1].=",".$row[csf("delivery_id")];
			}
			?>
			<tr align="center" id="dtls_form_delete">				
				<td>
					<? if ($k==$num_rowss) { ?>
					<input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:80px" value="<? echo $data[1]; ?>" />
					<input type="hidden" name="delete_id" id="delete_id"  style="width:80px" value="<? echo $delete_id; ?>" />
					<? } ?>
					<input type="hidden" name="curanci_<? echo $k; ?>" id="curanci_<? echo $k; ?>"  style="width:80px" value="<? //echo $order_array[$row[csf("order_id")]]['currency_id']; ?>" />
					<input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
					<input type="hidden" name="deliveryid_<? echo $k; ?>" id="deliveryid_<? echo $k; ?>" value="<? echo $row[csf("delivery_id")]; ?>">
					<input type="text" name="txt_deleverydate_<? echo $k; ?>" id="txt_deleverydate_<? echo $k; ?>"  class="datepicker" style="width:60px" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" disabled />									
				</td>
				<td>
					<input type="text" name="txt_challenno_<? echo $k; ?>" id="txt_challenno_<? echo $k; ?>"  class="text_boxes" style="width:55px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
				</td>
				<td>
					<input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>" style="width:50px" > 
					<input type="text" name="txt_orderno_<? echo $k; ?>" id="txt_orderno_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? echo $job_array[$row[csf("order_id")]]['po']; ?>" readonly />										
				</td>
				<td>
					<input type="text" name="txt_stylename_<? echo $k; ?>" id="txt_stylename_<? echo $k; ?>"  class="text_boxes" style="width:80px;" value="<? echo $job_array[$row[csf("order_id")]]['style']; ?>" readonly />
				</td>
				<td>
					<input type="text" name="txt_buyername_<? echo $k; ?>" id="txt_buyername_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $buyerArr[$job_array[$row[csf("order_id")]]['buyer']]; ?>" readonly />								
				</td>
				<td>			
					<input name="txt_numberroll_<? echo $k; ?>" id="txt_numberroll_<? echo $k; ?>" type="text" class="text_boxes" style="width:40px" value="<? echo $row[csf("carton_roll")]; ?>" readonly />							
				</td> 
				<td>
					<input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("item_id")]; ?>">
					<input type="text" name="text_febricdesc_<? echo $k; ?>" id="text_febricdesc_<? echo $k; ?>"  class="text_boxes" style="width:105px" value="<? echo $garments_item[$row[csf("item_id")]]; ?>" readonly/>
				</td>
				<td>
					<input type="hidden" name="color_process_<? echo $k; ?>" id="color_process_<? echo $k; ?>" value="<? //echo $order_array[$row[csf("order_id")]]['main_process_id']; ?>">
					<input type="text" name="txt_color_process_<? echo $k; ?>" id="txt_color_process_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? //echo $color_arr[$color_id[$row[csf("item_id")]]].''.$production_process[$order_array[$row[csf("order_id")]]['main_process_id']]; ?>" readonly/>
				</td>
				<td>
					<?
						$process=explode(',',$order_array[$row[csf("order_id")]]['process_id']);
						$add_process='';
						foreach($process as $inf)
						{
							if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=",".$conversion_cost_head_array[$inf];
						}
					?>
					<input type="hidden" name="add_process_<? echo $k; ?>" id="add_process_<? echo $k; ?>" value="<? //echo $order_array[$row[csf("order_id")]]['process_id']; ?>">
					<input type="text" name="txt_add_process_<? echo $k; ?>" id="txt_add_process_<? echo $k; ?>"  class="text_boxes" style="width:115px" value="<? //echo $add_process; ?>" readonly/>
				</td>
				<td>
					<input type="text" name="txt_deliveryqnty_<? echo $k; ?>" id="txt_deliveryqnty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:60px" value="<? echo $row[csf("delivery_qty")]; ?>" readonly />
				</td>
				<td>
					<input type="text" name="txt_rate_<? echo $k; ?>" id="txt_rate_<? echo $k; ?>"  class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("rate")]; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);" />
				</td>
				<td>
					<?
						$total_amount=$row[csf("delivery_qty")]*$row[csf("rate")]; 
					?>
					<input type="text" name="txt_amount_<? echo $k; ?>" id="txt_amount_<? echo $k; ?>" style="width:60px"  class="text_boxes_numeric"  value="<? echo $row[csf("amount")]; ?>" readonly />
				</td>
                <td>
                <? 
                    //$order_array[$row[csf("order_id")]]['currency_id']
                    echo create_drop_down( "cbo_curanci_$k", 60, $currency,"", 1, "-Select Currency-",$row[csf("currency_id")],"",0,"" );
                ?>
                </td>
				<td>
					<input type="text" name="txt_remarks_<? echo $k; ?>" id="txt_remarks_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $row[csf("remarks")]; ?>" />
				</td>
			</tr>
		<?	
		}
	}
	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id="1";
	if ($operation==0)   // Insert Here = delivery_id
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if (is_duplicate_field( "delivery_id", "subcon_inbound_bill_dtls", "mst_id=$update_id" )==1)
		{
			echo "11**0"; 
			disconnect($con);
			die;			
		}
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'CUT', date("Y",time()), 5, "select prefix_no,prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_id and process_id=$bill_process_id order by prefix_no_num desc ", "prefix_no", "prefix_no_num" ));
		
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_inbound_bill_mst", 1 ) ; 	
			$field_array="id,prefix_no,prefix_no_num,bill_no,company_id,location_id,bill_date,party_id,party_source,bill_for,process_id,inserted_by,insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_party_name.",".$cbo_party_source.",".$cbo_bill_for.",'".$bill_process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			//echo "INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array; 
			$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,1);
			$return_no=$new_bill_no[0]; 
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="bill_no*company_id*location_id*bill_date*party_id*party_source*bill_for*updated_by*update_date";
			$data_array="".$txt_bill_no."*".$cbo_company_id."*".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_party_source."*".$cbo_bill_for."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,1);
			$return_no=str_replace("'",'',$txt_bill_no);
		}
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, packing_qnty, delivery_qty, rate, amount, remarks, currency_id, process_id, inserted_by, insert_date";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*item_id*packing_qnty*delivery_qty*rate*amount*remarks*currency_id*updated_by*update_date";
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";	  
		$process_id=1;
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="deleverydate_".$i;
			$challen_no="challenno_".$i;
			$orderid="ordernoid_".$i;
			$style_name="stylename_".$i;
			$buyer_name="buyername_".$i;
			$item_id="itemid_".$i;
			$number_roll="numberroll_".$i;
			$quantity="deliveryqnty_".$i;
			$rate="txtrate_".$i;
			$amount="amount_".$i;
			$remarks="remarks_".$i;
			//$curanci="curanci_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$curanci="cbo_curanci_".$i;
			
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if($$amount!="")
				{
				  if ($add_comma!=0) $data_array1 .=",";
				  $data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$number_roll.",".$$quantity.",".$$rate.",".$$amount.",".$$remarks.",".$$curanci.",'".$process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				  $id1=$id1+1;
				  $add_comma++;
				}
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1"));
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$number_roll."*".$$quantity."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1"));
			}
		}
		//echo bulk_update_sql_statement( "subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr );
		$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		
		//echo bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery );
		$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
			
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
		}
		if(str_replace("'",'',$$style_name)=="" || str_replace("'",'',$$buyer_name)=="")  
		{
			$order_id_arr[]=str_replace("'",'',$$orderid);
			$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));
			
		}
		else
		{
			$order_id_arr[]=str_replace("'",'',$$orderid);
			$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));	
		}
		//echo bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr );die;
		$rID4=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr ));
			
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else if($rID)
			{
				mysql_query("ROLLBACK");  
				echo "5**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1 && $rID2 && $rID4)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else if($rID)
			{
				oci_rollback($con);
				echo "5**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=str_replace("'",'',$update_id);
		$nameArray= sql_select("select is_posted_account,post_integration_unlock from subcon_inbound_bill_mst where id='$id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		$post_integration_unlock=$nameArray[0][csf('post_integration_unlock')];
		
		if($posted_account==1 && $post_integration_unlock==0)
		{
			echo "14**All Ready Posted in Accounting.";
			disconnect($con);
			exit();
		}
		$field_array="bill_no*company_id*location_id*bill_date*party_id*party_source*bill_for*updated_by*update_date";
		$data_array="".$txt_bill_no."*".$cbo_company_id."*".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_party_source."*".$cbo_bill_for."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,1);                                                                     
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, packing_qnty, delivery_qty, rate, amount, remarks, currency_id, process_id, inserted_by, insert_date";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*item_id*packing_qnty*delivery_qty*rate*amount*remarks*currency_id*updated_by*update_date";
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		$process_id=1;
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="deleverydate_".$i;
			$challen_no="challenno_".$i;
			$orderid="ordernoid_".$i;
			$style_name="stylename_".$i;
			$buyer_name="buyername_".$i;
			$item_id="itemid_".$i;
			$number_roll="numberroll_".$i;
			$quantity="deliveryqnty_".$i;
			$rate="txtrate_".$i;
			$amount="amount_".$i;
			$remarks="remarks_".$i;
			//$curanci="curanci_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$curanci="cbo_curanci_".$i;
				
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$number_roll.",".$$quantity.",".$$rate.",".$$amount.",".$$remarks.",".$$curanci.",'".$process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1")); 
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$number_roll."*".$$quantity."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1"));
			}
		}
		//echo bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery );
		$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
		//echo bulk_update_sql_statement( "subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr );
		$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));		
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
		}
		if(str_replace("'",'',$delete_id)!="")
		{
			$delete_id=str_replace("'",'',$delete_id);
			$rID3=execute_query( "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id)",0);
			$delete_id=explode(",",str_replace("'",'',$delete_id));
			for ($i=0;$i<count($delete_id);$i++)
			{
				$id_delivery[]=$delete_id[$i];
				$data_delivery[str_replace("'",'',$delete_id[$i])] =explode(",",("0"));
			}
		}
		//echo bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_delivery,$id_delivery );
		$rID3=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_delivery,$id_delivery ));
		
		if(str_replace("'",'',$$style_name)=="" || str_replace("'",'',$$buyer_name)=="")  
		{
			$order_id_arr[]=str_replace("'",'',$$orderid);
			$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));
		}
		else
		{
			$order_id_arr[]=str_replace("'",'',$$orderid);
			$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));	
		}
		//echo bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr );die;
		$rID4=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr ));
				
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else if($rID && $rID1 && $rID2 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1 && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else if($rID && $rID1 && $rID2 && $rID4)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==2)  //Delete here======================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=str_replace("'",'',$update_id);
		$return_no=str_replace("'",'',$txt_bill_no);
		$field_array_delivery="bill_status";
		if(str_replace("'",'',$delete_id)!="")
		{
			$delete_id=str_replace("'",'',$delete_id);
			$rID3=execute_query( "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id)",0);
			$delete_id=explode(",",str_replace("'",'',$delete_id));
			for ($i=0;$i<count($delete_id);$i++)
			{
				$id_delivery[]=$delete_id[$i];
				$data_delivery[str_replace("'",'',$delete_id[$i])] =explode(",",("0"));
			}
		}
		//echo bulk_update_sql_statement( "subcon_delivery", "id",$field_array_delivery,$data_delivery,$id_delivery );
		$rID4=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_delivery,$id_delivery ));
		
		if($db_type==0)
		{
			if($rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="cutting_bill_print") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, bill_for from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:990px;">
         <table width="990" cellspacing="0" align="right" border="0">
            <tr>
                <td colspan="6" align="center" style="font-size:x-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center">
                    <?
                        $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
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
                            Website No: <? echo $result[csf('website')];?> <br>
                           <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
                        }
                    ?> 
                </td>
            </tr>           
        	<tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong><? echo $data[3]; ?></strong></u></td>
            </tr>
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
             <tr>
             <?
			 	if($dataArray[0][csf('party_source')]==3)
				{
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
                    	$address="";
						if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
				}
			 ?>
             
                <td><strong>Party Name : </strong></td><td colspan="5"> <? echo $party_library[$dataArray[0][csf('party_id')]].' : Address :- '.$address; ?></td>
            </tr>
        </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="990"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="60" align="center">Challan No</th>
                <th width="65" align="center">D. Date</th>
                <th width="70"align="center">Order</th> 
                <th width="70" align="center">Buyer</th>
                <th width="70" align="center">Style</th>
                <th width="110" align="center">Y. Desc</th>                   
                <th width="120" align="center">F. Des.</th>
                <th width="30" align="center">Roll</th>
                <th width="60" align="center">D. Qty</th>
                <th width="30" align="center">UOM</th>
                <th width="60" align="center">Currency</th>
                <th width="30" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th width="" align="center">Remarks</th>
            </thead>
		 <?
		 	$order_array=array();
			$order_sql="select id, order_no, order_uom, cust_buyer, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
			}
			//var_dump($order_array);
     		$i=1;
			$mst_id=$dataArray[0][csf('id')];
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, packing_qnty, delivery_qty, rate, amount, remarks, currency_id, process_id from subcon_inbound_bill_dtls where mst_id='$mst_id' and process_id='1' and status_active=1 and is_deleted=0"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>"> 
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td><p><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></p></td>
                    <td><p><? echo $order_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
                    <td><p><? echo $order_array[$row[csf('order_id')]]['cust_style_ref']; ?></p></td>
                    <td><p><? echo $yarn_desc_arr[$row[csf('item_id')]]; ?></p></td>
                    <td><p><? echo $const_comp_arr[$row[csf('item_id')]]; ?></p></td>
                    <td align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td><p><? echo $unit_of_measurement[$order_array[$row[csf('order_id')]]['order_uom']]; ?></p></td>
                    <td><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf('remarks')]; ?></p></td>
                    <? 
					$carrency_id=$row['currency_id'];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				   ?>
                </tr>
                <?php
                $i++;
			}
			?>
        	<tr> 
                <td align="right" colspan="8"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                <td colspan="3">&nbsp;</td>
                
                
                <td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="15" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <table width="990" align="left" > 
            <tr>
                <td colspan=15 align=left>&bull; Receiver should be aware of the quantity &amp; specification of the Product(s) at the time of taking delivery.</td>
            </tr>
            <tr>
                <td colspan=15 align=left>&bull; No claim will be entertained after delivery of goods.</td>
            </tr>
            <tr>
                <td colspan=15 align=left>&bull; Delivery Challan have been attached.</td>
            </tr>
            <tr>
                <td colspan=15 align=left>&bull; Payment should be made within seven days from the bill date.</td>
            </tr> 
        </table>
        <br>
		 <?
            echo signature_table(50, $data[0], "930px");
         ?>
   </div>
   </div>
<?
}
?>