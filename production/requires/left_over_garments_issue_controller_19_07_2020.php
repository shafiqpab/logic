<?
session_start();
include('../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
//$order_type_arr = array(1=>"Self Order",2=>"Subcontract Order");
$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

if($action=="search_party")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>
		
	
		function js_set_value(partyData)
		{
			
			//alert(partyData); return;
			$("#hidden_party_data").val(partyData);//po id
			parent.emailwindow.hide();
		}
    </script>
	</head>
		<body>
			<input type="hidden" id="hidden_party_data">
			<?
					$sql ="select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 and tag_company in ('$company') order by buyer_name";
					// and party_type  in('80')
					
					echo create_list_view("list_view", "Buyer Name","120","390","270",0, $sql , "js_set_value","id,buyer_name", "",1, "0", $arr,"buyer_name", "","setFilterGrid('list_view',-1)","0") ;
					
				exit();

			?>
		</body>           
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="check_conversion_rate")
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();	
}


if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select sewing_production,production_entry,leftover_maintained,leftover_country_maintained from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{		
		echo "$('#sewing_production_variable').val(".$result[csf("leftover_maintained")].");\n";
		echo "$('#country_maintain_variable').val(".$result[csf("leftover_country_maintained")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
		
	}
	
	echo "$('#iron_production_variable_rej').val(0);\n";
	$sql_result_rej = sql_select("select sewing_production from variable_settings_production where company_name=$data and variable_list=28 and status_active=1");
 	foreach($sql_result_rej as $result)
	{
		
		echo "$('#iron_production_variable_rej').val(".$result[csf("sewing_production")].");\n";
		if($result[csf("sewing_production")]==3)
		{
				echo "$('#txt_reject_qnty').attr('readonly','readonly');\n";
		}
		else
		{
			echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";	
		}
	}
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=30","is_control");
	if($variable_is_control=="") $variable_is_control=0;
	echo "document.getElementById('variable_is_controll').value=".$variable_is_control.";\n";
 	exit();
}


if ($action=="load_drop_down_location")
{ 
	echo create_drop_down( "cbo_location_name", 170, "select location_name,id from lib_location where is_deleted=0  and status_active=1 and company_id='$data' order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "" );
	 //load_drop_down( 'requires/left_over_garments_issue_controller', this.value, 'load_drop_down_floor', 'cbo_floor' )     	 
}


if ($action=="load_drop_down_store_name")
{
	$data_ex = explode("_", $data);
	if($data_ex[1] !=0 || $data_ex[1] !=""){$location_cond = " and location_id=$data_ex[1]";}
	echo create_drop_down( "cbo_store_name", 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=30 and a.status_active=1 and a.is_deleted=0 and a.company_id in($data_ex[0]) $location_cond  group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "0", "","" );
	//and b.category_type in ($item_cate_credential_cond) $store_location_credential_cond
	exit();
	
}

if ($action=="load_drop_down_floor")
{
	$data = explode("_",$data);
	echo create_drop_down( "cbo_floor_name", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id='$data[0]' and location_id='$data[1]' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
}


if ($action=="load_drop_down_location2")
{ 
	echo create_drop_down( "cbo_location_name", 120, "select location_name,id from lib_location where is_deleted=0  and status_active=1 and company_id='$data' order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'left_over_garments_issue_controller', this.value, 'load_drop_down_store_name2', 'cbo_store_name' );" );     	 
}

if ($action=="load_drop_down_store_name2")
{
	$selected=$data;
	echo create_drop_down( "cbo_store_name", 120, "select id,store_name from lib_store_location  where id='$data' and status_active =1 and is_deleted=0 order by store_name", "id,store_name", 1, "-- Select Store --", $selected, 0, "" );
}

if ($action=="load_drop_down_floor2")
{
	
	echo create_drop_down( "cbo_floor_name", 80, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );     	 
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
	
		//function js_set_value(id,po_number,buyer_id,country_id,style_ref_no,gmts_item,fob_rate,currency,currency_id,job_no,company_id,po_qnty,plan_cut_qnty,shipment_date)
		//function js_set_value(dtlsId,mstId,country_name,location_name,currency_id)
		function js_set_value(po_id,company_id,location_name,store_name,country_name,item_id)
		{
			$("#hidden_po_id").val(po_id);
			$("#hidden_company_id").val(company_id);
			$("#hidden_location_name").val(location_name);
			$("#hidden_store_name").val(store_name);
			$("#hidden_country_name").val(country_name);
			$("#hidden_item_id").val(item_id);
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
                                    $searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref.");
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
            		 			
                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $order_type; ?>+'_'+<? echo $goods_type; ?>+'_'+<? echo $garments_nature; ?>+'_'+<? echo $location_name; ?>, 'create_po_search_list_view', 'search_div', 'left_over_garments_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" height="40" valign="middle">
					<? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_po_id"/>
                    <input type="hidden" id="hidden_company_id"/>
                    <input type="hidden" id="hidden_location_name"/>
                    <input type="hidden" id="hidden_store_name"/>
                    <input type="hidden" id="hidden_country_name"/>
                    <input type="hidden" id="hidden_item_id"/>
                    
                    <!--
                    <input type="hidden" id="hidden_dtls_id">
                    <input type="hidden" id="hidden_mst_id">
                    <input type="hidden" id="hidden_currency_id">
                    <input type="hidden" id="hidden_job_no">
                    <input type="hidden" id="hidden_byer_name">
                    
                    <input type="hidden" id="hidden_country_id">
                    <input type="hidden" id="hidden_style_ref_no">
                    <input type="hidden" id="hidden_gmts_item">
                    
                    <input type="hidden" id="hidden_order_rate">
                    <input type="hidden" id="hidden_currency">
                    <input type="hidden" id="hidden_currency_rate">
                    
                    <input type="hidden" id="hidden_company_id">
                    <input type="hidden" id="hidden_po_qnty">
                    <input type="hidden" id="hidden_plancut_qnty">
                    <input type="hidden" id="hidden_shipment_date">
                    -->

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
 	$order_type = $ex_data[5];
 	$goods_type = $ex_data[6];
 	$garments_nature = $ex_data[7];
 	$location_name = $ex_data[8];
	
	
	//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$garments_item=return_library_array("select id,item_name from  lib_garment_item", 'id', 'item_name');
	$location_library=return_library_array("select id,location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');

	
	
	$sql_cond="";
	
	
	/*if(trim($txt_search_common)!="")
	{
		$sql_cond = " and b.order_no like '%".trim($txt_search_common)."%'";
	}*/

	switch ($txt_search_by) 
	{
		case 1:
			$sql_cond = " and b.style_ref_no like '%".trim($txt_search_common)."%'";
			break;
		case 2:
			$sql_cond = " and a.buyer_name like '%".trim($txt_search_common)."%'";
			break;
		case 3:
			$sql_cond = " and b.job_no like '%".trim($txt_search_common)."%'";
			break;
		case 4:
			$sql_cond = " and b.order_no =".trim($txt_search_common)."";
			break;		
		default:
			$sql_cond = " and b.order_no like '%".trim($txt_search_common)."%'";
			break;
	}
	
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.leftover_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.leftover_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.leftover_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
		}
	}
	if($db_type==2 || $db_type==1)
	{ 
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.leftover_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.leftover_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.leftover_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
		}
		
	}	
	
	
	if(trim($company)!='0') 
	{
		$sql_cond .= " and a.company_id='$company'";
	}
	if(trim($location_name)!='0') 
	{
		$sql_cond .= " and a.location='$location_name'";
	}

	$sql_con_issue = str_replace("leftover_date", "issue_date", $sql_cond);
	$sql_issue = "SELECT b.po_break_down_id as po_id, b.item_number_id as item_id,a.store_name, sum(b.total_issue) as issue_qty, b.country_id from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_con_issue group by b.po_break_down_id, b.item_number_id,a.store_name, b.country_id";
	$sql_issue_res = sql_select($sql_issue);
	$issue_qnty_array = array();
	foreach ($sql_issue_res as $val) 
	{
		$issue_qnty_array[$val[csf('country_id')]][$val[csf('po_id')]][$val[csf('item_id')]][$val[csf('store_name')]] += $val[csf('issue_qty')];
	}
	
	/*
	$sql ="select a.sys_number, a.leftover_date, a.company_id, a.location, b.id as dtls_id, b.mst_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.leftover_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel, b.remarks 
	from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond
	group by a.sys_number, a.leftover_date, a.company_id, a.location, b.id, b.mst_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.leftover_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel, b.remarks
	";
	//a.leftover_date, a.company_id, a.location, 
	*/
	$sql ="SELECT a.company_id, a.location, b.po_break_down_id, b.order_no, b.style_ref_no, b.item_number_id,a.store_name, sum(b.total_left_over_receive) as rcv_qty, b.country_id from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by a.company_id, a.location, b.po_break_down_id, b.order_no, b.style_ref_no, b.item_number_id,a.store_name, b.country_id";
	// echo $sql;
	$result = sql_select($sql);

	?>
     <div style="width:900px;margin: 0 auto;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" align="left">
            <thead>
                <th width="30">SL</th>
                <th width="100">Country</th>
                <th width="150">PO Number</th>
                <th width="150">Style Ref</th>
                <th width="150">Garments Item</th>
                <th width="150">Location</th>
                <th width="150">Store Name</th>
                <th width="70">Balance</th>
            </thead>
     	</table>
	     <div style="width:900px; max-height:240px;overflow-y:auto;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_po_list" align="left">
			<?
				$i=1;
	            foreach( $result as $row )
	            {
	            	if ($i%2==0)  $bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF";
	            	$issue_qty = $issue_qnty_array[$row[csf('country_id')]][$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('store_name')]];
	            	$balance = $row[csf('rcv_qty')] - $issue_qty;
				?>
			
				<!--<tr onClick="js_set_value('<? //echo $row[csf('dtls_id')]; ?>','<? //echo $row[csf('mst_id')] ?>','<? //echo $row[csf('company_id')] ?>','<? //echo $row[csf('location')] ?>','<? //echo $row[csf('currency_id')] ?>')" style="cursor:pointer">-->
	            <tr onClick="js_set_value('<? echo $row[csf('po_break_down_id')] ?>','<? echo $row[csf('company_id')] ?>','<? echo $row[csf('location')] ?>','<? echo $row[csf('store_name')] ?>','<? echo $row[csf('country_id')] ?>','<? echo $row[csf('item_number_id')] ?>')" style="cursor:pointer" bgcolor="<?php echo $bgcolor; ?>">
					<td width="30" align="center"><? echo $i; ?></td>
                    <td width="100"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
					<td width="150"><? echo $row[csf('order_no')]; ?></td>
					<td width="150"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="150"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
					<td width="150"><? echo $location_library[$row[csf('location')]]; ?></td>
					<td width="150"><? echo $store_arr[$row[csf('store_name')]]; ?></td>
					<td width="70"><? echo $balance; ?></td>
				</tr>
				<?					
	            $i++;	
	            }
	   		?>
	        </table>
	    </div>
	</div>
	<?	
	exit();
}

if($action=="show_country_listview")
{	
	extract($_REQUEST);
	// ============================= GETTING RECEIVE QTY ============================
	$sqlrRcv ="SELECT b.po_break_down_id as po_id, b.item_number_id as item_id, b.country_id, sum(b.total_left_over_receive) as rcv_qnty from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and b.po_break_down_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, b.item_number_id, b.country_id";
	$sqlrRcv_res = sql_select($sqlrRcv);
	$rcv_qty_array = array();
	foreach ($sqlrRcv_res as $val) 
	{
		$rcv_qty_array[$val[csf('po_id')]][$val[csf('country_id')]][$val[csf('item_id')]] = $val[csf('rcv_qnty')];
	}
	
	$sqlLeft ="SELECT b.po_break_down_id as po_id, b.item_number_id as item_id, b.country_id, sum(b.total_issue) as issue_qnty from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b where a.id=b.mst_id and b.po_break_down_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, b.item_number_id, b.country_id";
	// echo $sql;
	$sqlLeftResult = sql_select($sqlLeft);
	$left_qty_array = array();
	foreach ($sqlLeftResult as $val) 
	{
		$left_qty_array[$val[csf('po_id')]][$val[csf('country_id')]][$val[csf('item_id')]] = $val[csf('issue_qnty')];
	}

	// =================================== main query ============================
	$sql = "SELECT po_break_down_id,item_number_id,max(country_ship_date) as sdate,country_id from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 and po_break_down_id=$data group by po_break_down_id,item_number_id,country_id";
	$sqlResult = sql_select($sql);
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="100">Item Name</th>
            <th width="80">Country</th>
            <th width="60">Shipment Date</th>
            <th width="40">Avlbl Qty.</th>
            <th width="40">Cum. Issue</th>
            <th width="40">Balance</th>
        </thead>
		<?
		$i=1;
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$shipDate 		= $row['SDATE'];
			
			$rcv_qty 	= $rcv_qty_array[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			$issueQty 	= $left_qty_array[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			$balance 	= $rcv_qty - $issueQty;
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$balance; ?>);">
				<td width="20"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="60" align="center"><? if($shipDate!="0000-00-00") echo change_date_format($shipDate); ?>&nbsp;</td>
				<td align="right" width="40"><?  echo $rcv_qty; ?></td>
                <td align="right" width="40"><?  echo $issueQty; ?></td>
                <td align="right" width="40"><?  echo $balance; ?></td>
			</tr>
		<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];

	$sql ="SELECT a.company_id,a.location,a.store_name,a.goods_type, b.po_break_down_id, b.item_number_id, b.country_id from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b where a.id=b.mst_id and b.po_break_down_id=$po_id and b.item_number_id=$item_id and b.country_id=$country_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	$sqlRes = sql_select($sql);
 	foreach($sqlRes as $result)
	{
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
		echo "$('#cbo_location_name').val('".$result[csf('location')]."');\n";
		echo "$('#cbo_store_name').val('".$result[csf('store_name')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		
  	}
 	exit();
}

if($action=="system_number_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	
	//$order_type_arr = array(1=>"Self Order",2=>"Subcontract Order");
	$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
	?>
	<script>
		$(document).ready(function(e) {
            //$("#txt_search_common").focus();
			load_drop_down( 'left_over_garments_issue_controller','<? echo $company; ?>', 'load_drop_down_location2', 'location_td' );
        });
		
		function js_set_value(id)
		{
			$("#hidden_search_data").val(id);//po id
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >

    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
                    <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                   		 <thead>  
                        	<th>Company</th>
                            <th>Location</th>
                            <th>Order Type</th>
                            <th style="display:none;">Buyer</th>
                            <th>Store Name</th>
                            <th>System No</th>
                        	<th width="200">Date Range</th>
                        	<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
                    	</thead>
        				<tr>
                        	<td>                                
								<? 
								echo create_drop_down( "cbo_company_name", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$company, "load_drop_down( 'left_over_garments_issue_controller', this.value, 'load_drop_down_location2', 'location_td' );");?>
							</td>
                             <td id="location_td">
								 <?
                                 echo create_drop_down( "cbo_location_name", 120, $blank_array,'', 1, '--- Select Location ---', $selected, "",0,0 );
                                 ?>
							 </td>
                             <td>
								<?
                            	echo create_drop_down( "cbo_order_type", 120, $order_source, "", 1, "-- Select --", $selected, "", "", "1,2", "", "");
                            	?>
							 </td>
                             <td style="display:none;">
								<? 
                                echo create_drop_down( "cbo_buyer_name", 120, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","",0 );
                                ?>
							</td>
                             <td id="store_name_td">
                            	
								<? 
                                 echo create_drop_down( "cbo_store_name", 120, "select id,store_name from lib_store_location", "id,store_name", 1, "-- Select Store --", $selected,"",0,0);
                                ?>
							</td>
                            
                    		<td> 
								<input type="text" style="width:100px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />
                    		</td>
                    		<td align="center">
                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
					  			<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
					 		</td> 
            		 		<td align="center">
            		 			
                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('txt_system_no').value, 'create_system_number_list_view', 'search_div', 'left_over_garments_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                                
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" height="40" valign="middle">
					<? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_search_data"> 
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
}


if($action=="create_system_number_list_view")
{
 	$ex_data = explode("_",$data);
	
    $company = $ex_data[0];
    $location = $ex_data[1];
    $txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
    $order_type = $ex_data[4];
    //$buyer_id = $ex_data[5];
    $store_id = $ex_data[6];
	$system_no = $ex_data[7];
	//$floor_id = $ex_data[8];
    
 	//$order_type_arr = array(1=>"Self Order",2=>"Subcontract Order");
	$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
	
	
	//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$garments_item=return_library_array("select id,item_name from  lib_garment_item", 'id', 'item_name');
	$location_name=return_library_array("select id,location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name');
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$store_name_arr=return_library_array( "select id,store_name from lib_store_location",'id','store_name');
	$floor_name_arr=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0",'id','floor_name');
	
	$sql_cond="";
	
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.issue_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.issue_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
		}
	}
	if($db_type==2 || $db_type==1)
	{ 
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.issue_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.issue_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.issue_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
		}
		
	}
	
	
	if(trim($system_no)!="")
	{
		$sql_cond = " and a.sys_number like '%".trim($system_no)."'";
	}
	if(trim($company)!='0') 
	{
		$sql_cond .= " and a.company_id='$company'";
	}
	if(trim($location_name)!=0) 
	{
		$sql_cond .= " and a.location='$location'";
	}
	
	if(trim($order_type)!='0') 
	{
		$sql_cond .= " and a.order_type='$order_type'";
	}
	/*if(trim($buyer_id)!='0') 
	{
		$sql_cond .= " and a.buyer_name='$buyer_id'";
	}*/
	if(trim($store_id)!='0') 
	{
		$sql_cond .= " and a.store_name='$store_id'";
	}
		
	/*if(trim($floor_id)!='0') 
	{
		$sql_cond .= " and a.floor_id='$floor_id'";
	}*/
	
	
	$sql ="select a.id, a.sys_number, a.company_id, a.location, a.issue_date, a.goods_type, a.order_type, a.party_name, a.party_id, a.issue_purpose, a.store_name, a.pay_term, a.currency_id, a.challan_no, a.exchange_rate, a.remarks from pro_leftover_gmts_issue_mst a where  a.status_active=1 and a.is_deleted=0 $sql_cond order by a.id";
	
	
	$arr=array(2=>$order_source,3=>$buyer_arr,4=>$store_name_arr,5=>$floor_name_arr);
	echo create_list_view("list_view", "System Number,Issue Date,Order Type,Buyer,Store Name","120,80,100,100,100","700","270",0, $sql , "js_set_value","id,sys_number,company_id", "",1, "0,0,order_type,0,store_name", $arr,"sys_number,issue_date,order_type,party_name,store_name", "","setFilterGrid('list_view',-1)","0,3,0,0,0") ;
	
	exit();
}


if($action=="show_dtls_listview")
{
	
	$dataArr = explode("**",$data);
	
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	
	$po_cond = "";
	if($dataArr[0]!=''){
		$po_cond = " and b.mst_id='$dataArr[0]' ";
	}
	/*if($dataArr[1]!=''){
		$item_id_cond = " and a.item_number_id='$item_id' ";
	}
	if($dataArr[2]!=''){
		$company_cond = " and a.company_id='$company_id'";
	}*/
?>	 
    <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70" align="center">Date</th>
                <th width="180" align="center">PO</th>
                <th width="70" align="center">Style Ref</th>
                <th width="80" align="center">Buyer</th> 
                <th width="80" align="center">Country</th> 
                <th width="80" align="center">Goods Type</th>                  
                <!--<th width="80" align="center">Leftover Qty.</th>-->
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
				$sql ="SELECT b.id, b.mst_id, b.receive_dtls_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.leftover_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel, b.remarks 
				from pro_leftover_gmts_issue_dtls b 
				where b.status_active=1 and b.is_deleted=0 $po_cond
				order by b.id";
			}
		    if($db_type==2)
			{
				
				$sql ="SELECT a.issue_date, a.goods_type, b.id, b.mst_id, b.receive_dtls_id, b.buyer_id, b.receive_dtls_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_issue, b.currency_id, b.fob_rate, b.issue_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel, b.remarks 
				from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b 
				where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 $po_cond
				order by b.id";
			}
			//echo $sql;
			$sqlResult =sql_select($sql);
			foreach($sqlResult as $selectResult){
				
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('production_quantity')];
				//dtls_id,mst_id,po_id,country_id,item_id,variableSettings
 		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="populate_selected_data(<? echo $selectResult[csf('id')]; ?>,<? echo $selectResult[csf('mst_id')]; ?>,<? echo $selectResult[csf('po_break_down_id')]; ?>,<? echo $selectResult[csf('country_id')]; ?>,<? echo $selectResult[csf('item_number_id')]; ?>,<? echo $selectResult[csf('sewing_production_variable')]; ?>);" > 
				<td width="30" align="center"><? echo $i; ?></td>
                <td width="70" align="center"><?php echo change_date_format($selectResult[csf('issue_date')]); ?></td>
                <td width="180" align="center"><p><? echo $selectResult[csf('order_no')]; ?></p></td>
                <td width="70" align="center"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                <td width="80" align="center"><p><? echo $buyer_library[$selectResult[csf('buyer_name')]]; ?>&nbsp;</p></td>
                <td width="80" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                <td width="80" align="center"><p><? echo $goods_type_arr[$selectResult[csf('goods_type')]]; ?></p></td>
			</tr>
			<?php
			$i++;
			}
			?>
		</table>
        </div>
	<?
}


if($action=="populate_mst_form_data")
{
	$sql ="SELECT a.id, a.sys_number, a.company_id, a.location, a.issue_date, a.goods_type, a.order_type, a.party_name, a.party_id, a.issue_purpose, a.store_name, a.pay_term, a.currency_id, a.challan_no, a.exchange_rate, a.remarks from pro_leftover_gmts_issue_mst a where a.id='$data' and a.status_active=1 and a.is_deleted =0 ";
	
	//echo $sql.";\n";
	$result =sql_select($sql);
	echo"load_drop_down( 'requires/left_over_garments_issue_controller', '".$result[0][csf('company_id')]."_".$result[0][csf('location')]."', 'load_drop_down_store_name', 'store_name_td' );\n"; 
	
	//echo"load_drop_down( 'requires/left_over_garments_issue_controller', ".$result[0][csf('location')].", 'load_drop_down_floor', 'cbo_floor' );\n";
	
	echo "$('#txt_system_no').val('".$result[0][csf('sys_number')]."');\n";
	echo "$('#txt_mst_id').val('".$result[0][csf('id')]."');\n";
	echo "$('#cbo_company_name').val('".$result[0][csf('company_id')]."');\n";
	echo "$('#cbo_location_name').val('".$result[0][csf('location')]."');\n";
	echo "$('#txt_issue_date').val('".change_date_format($result[0][csf('issue_date')])."');\n";
	echo "$('#cbo_order_type').val('".$result[0][csf('order_type')]."');\n";
	echo "$('#cbo_buyer_name').val('".$result[0][csf('buyer_name')]."');\n";
	echo "$('#cbo_store_name').val('".$result[0][csf('store_name')]."');\n";
	echo "$('#exchange_rate').val('".$result[0][csf('exchange_rate')]."');\n";
	echo "$('#txt_remark').val('".$result[0][csf('remarks')]."');\n";
	echo "$('#cbo_goods_type').val('".$result[0][csf('goods_type')]."');\n";
	echo "$('#cbo_issue_purpose').val('".$result[0][csf('issue_purpose')]."');\n";
	echo "$('#cbo_pay_term').val('".$result[0][csf('pay_term')]."');\n";
	echo "$('#cbo_currency_mst').val('".$result[0][csf('currency_id')]."');\n";
	
	
	echo "set_button_status(0, permission, 'fnc_left_over_gmts_input',1,0);\n";
 	exit();		
}



if($action=="color_and_size_level_left_over")
{
		$dataArr = explode("**",$data);
		//$dtls_id = $dataArr[0];
		//$mst_id = $dataArr[1];
		$poId = $dataArr[0];
		$company_id = $dataArr[1];
		$location_id = $dataArr[2];
		$country_name = $dataArr[3];
		$item_id = $dataArr[4];
		//$country_cond = " and b.country_id = '$country_name'";
		
		
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		
		$result = sql_select("SELECT a.order_type, a.buyer_name, a.exchange_rate,
		b.id, b.mst_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, b.total_left_over_receive, b.currency_id, b.fob_rate, b.leftover_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel, b.remarks 
		from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b 
		where a.id=b.mst_id and b.po_break_down_id='$poId' and b.country_id = '$country_name' and b.item_number_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		
		
		$variableSettings = $result[0][csf('sewing_production_variable')];
		$order_type = $result[0][csf('order_type')];
		// $item_id = $result[0][csf('item_number_id')];
		$po_id = $result[0][csf('po_break_down_id')];
		$country_id = $result[0][csf('country_id')];
		
		// echo "$('#exchange_rate').val('".$result[0][csf('exchange_rate')]."');\n";
		//echo "$('#cbo_currency_mst').val('".$result[0][csf('currency_id')]."');\n";
		
		echo "$('#txt_order_no').val('".$result[0][csf('order_no')]."');\n";
		echo "$('#txt_po_id').val('".$result[0][csf('po_break_down_id')]."');\n";
		//echo "$('#hidden_dtls_id').val('".$result[0][csf('id')]."');\n";
		//echo "$('#hidden_receive_dtls_id').val('".$result[0][csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[0][csf('buyer_name')]."');\n";
		echo "$('#txt_style_name').val('".$result[0][csf('style_ref_no')]."');\n";
		echo "$('#cbo_item_name').val('".$result[0][csf('item_number_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[0][csf('country_id')]."');\n";
		//echo "$('#txt_total_left_over_receive').val('".$result[0][csf('total_left_over_receive')]."');\n";
		echo "$('#txt_remark2').val('".$result[0][csf('remarks')]."');\n";
		echo "$('#cbo_currency').val('".$result[0][csf('currency_id')]."');\n";
		echo "$('#txt_fob_rate').val('".$result[0][csf('fob_rate')]."');\n";
		//echo "$('#txt_issue_amount').val('".$result[0][csf('leftover_amount')]."');\n";
		echo "$('#txt_bdt_amount').val('".$result[0][csf('bdt_amount')]."');\n";
		echo "$('#cbo_room_no').val('".$result[0][csf('room_no')]."');\n";
		echo "$('#cbo_rack_no').val('".$result[0][csf('rack_no')]."');\n";
		echo "$('#cbo_shelf_no').val('".$result[0][csf('shelf_no')]."');\n";
		echo "$('#cbo_bin_no').val('".$result[0][csf('bin_no')]."');\n";
		echo "$('#sewing_production_variable').val('".$result[0][csf('sewing_production_variable')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[0][csf('po_break_down_id')]."');\n";
		echo "$('#styleOrOrderWisw').val('".$result[0][csf('style_order_wisw')]."');\n";
		echo "$('#variable_is_controll').val('".$result[0][csf('variable_is_controll')]."');\n";
		echo "$('#txt_user_lebel').val('".$result[0][csf('user_lebel')]."');\n";
		
		//$rcv_qnty = $color_size_qnty_array[$color[csf('id')]]['issue'];
		//$rcv_qnty2 = $color_size_qnty_array2[$color[csf('id')]]['rcv'];
		if( $variableSettings==2 ) // color level
		{
			if($order_type==1) // Self Order
			{
				$dtlsData2 = sql_select("SELECT  d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.po_break_down_id=$po_id and b.country_id = '$country_name' and b.item_number_id=$item_id and c.color_size_break_down_id!=0 and c.production_type=1 group by d.color_number_id");
	

				foreach($dtlsData2 as $row2)
				{				  
					$color_size_qnty_array2[$row2[csf('color_number_id')]]['rcv']= $row2[csf('production_qnty')];
					$color_size_total_qnty_array2[$row2[csf('color_number_id')]]+= $row2[csf('production_qnty')];
				} 
					
				$dtlsData = sql_select("SELECT  d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.po_break_down_id=$po_id and b.country_id = '$country_name' and b.item_number_id=$item_id and c.color_size_break_down_id!=0 and c.production_type=1 group by d.color_number_id"); 
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_number_id')]]['issue']= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_number_id')]]+= $row[csf('production_qnty')];
				} 	
				
				$sql = "SELECT color_order, item_number_id, color_number_id, sum(order_quantity) as order_quantity, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_order, item_number_id, color_number_id order by color_number_id,color_order";
			}
			else	// Subcontruct Order
			{
				$dtlsData = sql_select("select  c.color_size_break_down_id, d.color_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, subcon_ord_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and b.country_id = '$country_name' and c.color_size_break_down_id!=0 and c.production_type=1 group by c.color_size_break_down_id, d.color_id");
				$color_size_qnty_array=array();
				foreach($dtlsData as $row)
				{
					//$color_size_qnty_array[$row[csf('color_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_number_id')]]+= $row[csf('production_qnty')];
				}
				
					
				$sql = "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part from subcon_ord_breakdown 
				where order_id='$po_id' and item_id='$item_id' ";
				
				
			}
				
		}
		else if( $variableSettings==3 ) //color and size level
		{
			if($order_type==1) // Self Order
			{
				/*echo "SELECT  c.color_size_break_down_id, d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.po_break_down_id=$po_id and b.country_id = '$country_name' and b.item_number_id=$item_id and c.color_size_break_down_id!=0 and c.production_type=1 group by c.color_size_break_down_id, d.color_number_id";*/
				$dtlsData2 = sql_select("SELECT  c.color_size_break_down_id, d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.po_break_down_id=$po_id and b.country_id = '$country_name' and b.item_number_id=$item_id and c.color_size_break_down_id!=0 and c.production_type=1 group by c.color_size_break_down_id, d.color_number_id");
	

				foreach($dtlsData2 as $row2)
				{				  
					$color_size_qnty_array2[$row2[csf('color_size_break_down_id')]]['rcv']= $row2[csf('production_qnty')];
					$color_size_total_qnty_array2[$row2[csf('color_number_id')]]+= $row2[csf('production_qnty')];
				} 
				

				/*$dtlsData = sql_select("select  c.color_size_break_down_id, d.color_number_id, 
				sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty 
				from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d 
			where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and c.color_size_break_down_id!=0 and c.production_type=1
			group by c.color_size_break_down_id, d.color_number_id");*/
	
				$dtlsData = sql_select("SELECT  c.color_size_break_down_id, d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.po_break_down_id=$po_id and b.country_id = '$country_name' and b.item_number_id=$item_id and c.color_size_break_down_id!=0 and c.production_type=1 group by c.color_size_break_down_id, d.color_number_id"); 
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['issue']= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_number_id')]]+= $row[csf('production_qnty')];
				} 	
				
				$sql = "SELECT id,size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order,id";
			}
			else	// Subcontruct Order
			{
				$dtlsData = sql_select("select  c.color_size_break_down_id, d.color_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, subcon_ord_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and b.country_id = '$country_name' and c.color_size_break_down_id!=0 and c.production_type=1 group by c.color_size_break_down_id, d.color_id");
				$color_size_qnty_array=array();
				foreach($dtlsData as $row)
				{
					//$color_size_qnty_array[$row[csf('color_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_number_id')]]+= $row[csf('production_qnty')];
				}
				
					
				$sql = "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part from subcon_ord_breakdown 
				where order_id='$po_id' and item_id='$item_id' ";
				
				
			}
				
		}
		else // by default color and size level
		{
			/*$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										
										from pro_leftover_gmts_rcv_dtls a,pro_leftover_gmts_rcv_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=1 group by a.color_size_break_down_id");
										
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('production_qnty')];
			} 
			
			$sql = "select id, size_order,item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order, id";*/
		}
		
		//echo $sql.";\n";
		
		if($variableSettingsRej!=1)
		{
			$disable="";
		}
		else
		{
			$disable="disabled";
		}
		
				
  		$colorHTML="";
		$colorID='';
		$chkColor = array(); 
		$i=0;
		$totalQnty=0;
		
		$colorResult = sql_select($sql);
 		foreach($colorResult as $color)
		{
 			
			if( $variableSettings==2 ) // color level
			{ 
				if($order_type==1) // Self Order
				{
					$rcv_qnty = $color_size_qnty_array2[$color[csf('color_number_id')]]['rcv'];
					$issue_qnty = $color_size_qnty_array[$color[csf('color_number_id')]]['issue'];
					
					$balance_qnty = $rcv_qnty-$issue_qnty;

					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($balance_qnty).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';				
					$totalQnty += $balance_qnty;
					$colorID .= $color[csf("color_number_id")].",";
				}

			}
			else //color and size level
			{
				if($order_type==1) // Self Order
				{
					$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];					
					}
					//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					
					$rcv_qnty = $color_size_qnty_array2[$color[csf('id')]]['rcv'];
					$issue_qnty = $color_size_qnty_array[$color[csf('id')]]['issue'];
					
					$balance_qnty = $rcv_qnty-$issue_qnty;
					
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($balance_qnty).'"  onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="" /></td></tr>';	
					
					//	<input name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'" class="text_boxes_numeric" style="width:70px" value="'.$balance_qnty.'" readonly="" disabled="" type="text" />
				}
				else
				{
					//"select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part from subcon_ord_breakdown where order_id='$po_id' and item_id='$item_id' "
				
					$index = $color[csf("size_id")].$color_arr[$color[csf("color_id")]].$color[csf("color_id")];
					if( !in_array( $color[csf("color_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_id")].'span">+</span>'.$color_library[$color[csf("color_id")]].' : <span id="total_'.$color[csf("color_id")].'">'.$color_size_total_qnty_array[$color[csf("color_id")]].'</span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_id")].'">';
						$chkColor[] = $color[csf("color_id")];					
					}
					//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_id")]."*".$color[csf("color_id")].",";
					
					$rcv_qnty = $color_size_qnty_array[$color[csf('id')]]['issue'];
					$rcv_qnty2 = $color_size_qnty_array2[$color[csf('id')]]['rcv'];
					$balance_qnty = $rcv_qnty - $rcv_qnty2;
					
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px"  onblur="fn_total('.$color[csf("color_id")].','.($i+1).')" value="" /><input name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_id")].($i+1).'" class="text_boxes_numeric" style="width:70px" value="'.$balance_qnty.'" readonly="" disabled="" type="text" /></td></tr>';	
					//placeholder="'.($rcv_qnty).'"	
				}
				
			}
			
			$i++; 
		}
		//echo $colorHTML;die; 
		if( $variableSettings==2 )
		{ 
			$colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; 
			//placeholder="'.$totalQnty.'"
		}
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		
		exit();
}



if($action=="color_and_size_level_left_over_update")
{
	//dtls_id+"**"+mst_id+"**"+po_id+"**"+country_id+"**"+item_id+"**"+variableSettings
		$dataArr = explode("**",$data);
		$dtls_id = $dataArr[0];
		$mst_id = $dataArr[1];
		$country_name = $dataArr[3];
		
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		


		$result = sql_select("SELECT a.sys_number, a.company_id, a.location, a.issue_date, a.goods_type, a.order_type, a.party_name, a.party_id, a.issue_purpose, a.store_name, a.pay_term, a.challan_no, a.exchange_rate, a.remarks,
			b.id, b.mst_id, b.receive_dtls_id, b.po_break_down_id, b.order_no, b.production_type, b.style_ref_no, b.item_number_id, b.country_id, 
			b.total_issue, b.sale_rate, b.currency_id, b.fob_rate, b.issue_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.sewing_production_variable, b.color_size_id, b.style_order_wisw, b.variable_is_controll, b.user_lebel,b.remarks,
			c.id as clr_sz_id, c.production_type, c.color_size_break_down_id, 
			c.production_qnty 
			from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c
			where a.id=b.mst_id and b.id=c.dtls_id and b.id='$dtls_id' and b.mst_id='$mst_id' and b.country_id = '$country_name'
			and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted =0");
			
		$variableSettings = $result[0][csf('sewing_production_variable')];
		$order_type = $result[0][csf('order_type')];
		$item_id = $result[0][csf('item_number_id')];
		$po_id = $result[0][csf('po_break_down_id')];
		$country_id = $result[0][csf('country_id')];
		
		echo "$('#txt_order_no').val('".$result[0][csf('order_no')]."');\n";
		echo "$('#txt_po_id').val('".$result[0][csf('po_break_down_id')]."');\n";
		echo "$('#txt_issue_date').val('".change_date_format($result[0][csf('issue_date')])."');\n";
		echo "$('#txt_mst_id').val('".$result[0][csf('mst_id')]."');\n";
		echo "$('#hidden_dtls_id').val('".$result[0][csf('id')]."');\n";
		echo "$('#cbo_store_name').val('".$result[0][csf('store_name')]."');\n";
		echo "$('#hidden_receive_dtls_id').val('".$result[0][csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[0][csf('buyer_name')]."');\n";
		echo "$('#txt_style_name').val('".$result[0][csf('style_ref_no')]."');\n";
		echo "$('#cbo_item_name').val('".$result[0][csf('item_number_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[0][csf('country_id')]."');\n";
		echo "$('#txt_total_issue').val('".$result[0][csf('total_issue')]."');\n";
		echo "$('#txt_sale_rate').val('".$result[0][csf('sale_rate')]."');\n";
		echo "$('#txt_remark2').val('".$result[0][csf('remarks')]."');\n";
		//echo "$('#cbo_currency_mst').val('".$result[0][csf('currency_id')]."');\n";
		echo "$('#cbo_currency').val('".$result[0][csf('currency_id')]."');\n";
		echo "$('#txt_fob_rate').val('".$result[0][csf('fob_rate')]."');\n";
		echo "$('#txt_issue_amount').val('".$result[0][csf('issue_amount')]."');\n";
		echo "$('#txt_bdt_amount').val('".$result[0][csf('bdt_amount')]."');\n";
		echo "$('#cbo_room_no').val('".$result[0][csf('room_no')]."');\n";
		echo "$('#cbo_rack_no').val('".$result[0][csf('rack_no')]."');\n";
		echo "$('#cbo_shelf_no').val('".$result[0][csf('shelf_no')]."');\n";
		echo "$('#cbo_bin_no').val('".$result[0][csf('bin_no')]."');\n";
		echo "$('#sewing_production_variable').val('".$result[0][csf('sewing_production_variable')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[0][csf('po_break_down_id')]."');\n";
		echo "$('#styleOrOrderWisw').val('".$result[0][csf('style_order_wisw')]."');\n";
		echo "$('#variable_is_controll').val('".$result[0][csf('variable_is_controll')]."');\n";
		echo "$('#txt_user_lebel').val('".$result[0][csf('user_lebel')]."');\n";
		echo "set_button_status(1, permission, 'fnc_left_over_gmts_input',1,0);\n";
		
		
		if( $variableSettings==2 ) // color level
		{
			if($order_type==1) // Self Order
			{
				
				$dtlsData2 = sql_select("select d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.po_break_down_id=$po_id and b.country_id = '$country_name' and c.color_size_break_down_id!=0 and c.production_type=1 group by d.color_number_id");
				foreach($dtlsData2 as $row2)
				{				  
					$color_size_qnty_array2[$row2[csf('color_number_id')]]['rcv']+= $row2[csf('production_qnty')];
					//$color_size_total_qnty_array2[$row2[csf('color_number_id')]]+= $row2[csf('production_qnty')];
				}
				
				
				
				$preIssue = sql_select("select d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.po_break_down_id=$po_id and b.country_id = '$country_name' and c.color_size_break_down_id!=0 and c.production_type=1 group by d.color_number_id"); 
				foreach($preIssue as $row3)
				{				  
					$color_size_qnty_array_pre[$row3[csf('color_number_id')]]['pre_issue']+= $row3[csf('production_qnty')];
				} 
				
				 
				$dtlsData = sql_select("select d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and b.country_id = '$country_name' and c.color_size_break_down_id!=0 and c.production_type=1 group by d.color_number_id"); 
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_number_id')]]['issue']+= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_number_id')]]+= $row[csf('production_qnty')];
				} 
				$sql = "SELECT color_order, item_number_id, color_number_id, sum(order_quantity) as order_quantity, sum(plan_cut_qnty) as plan_cut_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_order, item_number_id, color_number_id order by color_number_id,color_order";

			}
			else	// Subcontruct Order
			{
				$dtlsData = sql_select("select  c.color_size_break_down_id, d.color_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, subcon_ord_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and b.country_id = '$country_name' and c.color_size_break_down_id!=0 and c.production_type=1 group by c.color_size_break_down_id, d.color_id");
				$color_size_qnty_array=array();
				foreach($dtlsData as $row)
				{
					//$color_size_qnty_array[$row[csf('color_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['issue']= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_number_id')]]+= $row[csf('production_qnty')];
				}
				
					
				$sql = "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part 
				from subcon_ord_breakdown 
				where order_id='$po_id' and item_id='$item_id' ";
				
				
			}
				
		}
		else if( $variableSettings==3 ) //color and size level
		{
			if($order_type==1) // Self Order
			{
				
				$dtlsData2 = sql_select("select  c.color_size_break_down_id, d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_rcv_dtls b, pro_leftover_gmts_rcv_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.po_break_down_id=$po_id and b.country_id = '$country_name' and c.color_size_break_down_id!=0 and c.production_type=1 group by c.color_size_break_down_id, d.color_number_id");
				foreach($dtlsData2 as $row2)
				{				  
					$color_size_qnty_array2[$row2[csf('color_size_break_down_id')]]['rcv']+= $row2[csf('production_qnty')];
					//$color_size_total_qnty_array2[$row2[csf('color_number_id')]]+= $row2[csf('production_qnty')];
				}
				
				
				
				$preIssue = sql_select("select  c.color_size_break_down_id, d.color_number_id, sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty from pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, wo_po_color_size_breakdown d where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.po_break_down_id=$po_id and b.country_id = '$country_name' and c.color_size_break_down_id!=0 and c.production_type=1 group by c.color_size_break_down_id, d.color_number_id"); 
				foreach($preIssue as $row3)
				{				  
					$color_size_qnty_array_pre[$row3[csf('color_size_break_down_id')]]['pre_issue']+= $row3[csf('production_qnty')];
				} 
				
				 
				$dtlsData = sql_select("select  c.color_size_break_down_id, d.color_number_id, 
				sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty 
				from pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, wo_po_color_size_breakdown d 
	where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and b.country_id = '$country_name' and c.color_size_break_down_id!=0 and c.production_type=1
	group by c.color_size_break_down_id, d.color_number_id");
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['issue']+= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_number_id')]]+= $row[csf('production_qnty')];
				} 
				$sql = "select id,size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order,id";

			}
			else	// Subcontruct Order
			{
				$dtlsData = sql_select("select  c.color_size_break_down_id, d.color_id, 
				sum(case when c.production_type=1 then c.production_qnty else 0 end) as production_qnty 
				from pro_leftover_gmts_issue_dtls b, pro_leftover_gmts_issue_clr_sz c, subcon_ord_breakdown d 
	where b.id=c.dtls_id  and d.id=c.color_size_break_down_id and b.id=$dtls_id and b.country_id = '$country_name' and c.color_size_break_down_id!=0 and c.production_type=1
	group by c.color_size_break_down_id, d.color_id");
	
				$color_size_qnty_array=array();
				foreach($dtlsData as $row)
				{
					//$color_size_qnty_array[$row[csf('color_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['issue']= $row[csf('production_qnty')];
					$color_size_total_qnty_array[$row[csf('color_number_id')]]+= $row[csf('production_qnty')];
				}
				
					
				$sql = "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part 
				from subcon_ord_breakdown 
				where order_id='$po_id' and item_id='$item_id' ";
				
				
			}
				
		}
		else // by default color and size level
		{
				/*$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										
										from pro_leftover_gmts_rcv_dtls a,pro_leftover_gmts_rcv_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=1 group by a.color_size_break_down_id");
										
			foreach($dtlsData as $row)
			{				  
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('production_qnty')];
			} 
			
			$sql = "select id, size_order,item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty 
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order, id";*/
		}
		
		//echo $sql.";\n";
		
		if($variableSettingsRej!=1)
		{
			$disable="";
		}
		else
		{
			$disable="disabled";
		}
		
				
  		$colorHTML="";
		$colorID='';
		$chkColor = array(); 
		$i=0;
		$totalQnty=0;
		
		$colorResult = sql_select($sql);
 		foreach($colorResult as $color)
		{
			
 			
			if( $variableSettings==2 ) // color level
			{ 
				if($order_type==1) // Self Order
				{
					$rcv_qnty = $color_size_qnty_array2[$color[csf('color_number_id')]]['rcv'];
					$issue_qnty = $color_size_qnty_array[$color[csf('color_number_id')]]['issue'];
					$pre_issue_qnty  = $color_size_qnty_array_pre[$color[csf('color_number_id')]]['pre_issue'];
					
					
					$balance_qnty = ($rcv_qnty - ($issue_qnty+$pre_issue_qnty))+$issue_qnty;

					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($balance_qnty).'" onblur="fn_colorlevel_total('.($i+1).')" value="'.$issue_qnty.'"></td></tr>';				
					$totalQnty += $issue_qnty;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else
				{

				}

			}
			else //color and size level
			{
				if($order_type==1) // Self Order
				{
					$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];

					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'">'.$color_size_total_qnty_array[$color[csf("color_number_id")]].'</span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];					
					}
					//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					//print_r($colorID); die();
					
					$rcv_qnty = $color_size_qnty_array2[$color[csf('id')]]['rcv'];
					$issue_qnty = $color_size_qnty_array[$color[csf('id')]]['issue'];
					$pre_issue_qnty  = $color_size_qnty_array_pre[$color[csf('id')]]['pre_issue'];
					
					
					$balance_qnty = ($rcv_qnty - ($issue_qnty+$pre_issue_qnty))+$issue_qnty;
					
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($balance_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"  value="'.$issue_qnty.'" /></td></tr>';
					//<input name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'" class="text_boxes_numeric" style="width:70px" value="'.$balance_qnty.'" readonly="" disabled="" type="text" />	
					//
				}
				else
				{
					//"select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part from subcon_ord_breakdown where order_id='$po_id' and item_id='$item_id' "
				
					$index = $color[csf("size_id")].$color_arr[$color[csf("color_id")]].$color[csf("color_id")];
					if( !in_array( $color[csf("color_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_id")].'span">+</span>'.$color_library[$color[csf("color_id")]].' : <span id="total_'.$color[csf("color_id")].'">'.$color_size_total_qnty_array[$color[csf("color_id")]].'</span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_id")].'">';
						$chkColor[] = $color[csf("color_id")];					
					}
					//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_id")]."*".$color[csf("color_id")].",";
					
					$rcv_qnty = $color_size_qnty_array[$color[csf('id')]]['issue'];
					$rcv_qnty2 = $color_size_qnty_array2[$color[csf('id')]]['rcv'];
					$balance_qnty = $rcv_qnty2 - $rcv_qnty;
					
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px"  onblur="fn_total('.$color[csf("color_id")].','.($i+1).')"  value="'.$rcv_qnty.'" /><input name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_id")].($i+1).'" class="text_boxes_numeric" style="width:70px" value="'.$balance_qnty.'" readonly="" disabled="" type="text" /></td></tr>';	
					//placeholder="'.($rcv_qnty).'"	
				}
				//$balance_qnty=0;
			}
			
			$i++; 
		}
		//echo $colorHTML;die; 
		if( $variableSettings==2 )
		{ 
			$colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; 
			//placeholder="'.$totalQnty.'"
		}
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		
		exit();
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }		
		if(str_replace("'","",$txt_mst_id)=='')
		{
			$id=return_next_id("id", "pro_leftover_gmts_issue_mst", 1);
		}
		else
		{
			$id = str_replace("'","",$txt_mst_id);
		}
		$dtls_id=return_next_id("id", "pro_leftover_gmts_issue_dtls", 1);
		$color_size_id=return_next_id("id", "pro_leftover_gmts_issue_clr_sz", 1);
		
		if($db_type==2) $mrr_cond="and  TO_CHAR(insert_date,'YYYY')=".date('Y',time()); else if($db_type==0) $mrr_cond="and year(insert_date)=".date('Y',time());
		$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'LGI', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from pro_leftover_gmts_issue_mst where company_id=$cbo_company_name $mrr_cond order by id DESC ", "sys_number_prefix", "sys_number_prefix_num" ));
		
		 $field_array_mst="id,sys_number_prefix,sys_number_prefix_num,sys_number,company_id,location,issue_date,goods_type,order_type,party_name,party_id,issue_purpose,store_name,pay_term,currency_id,challan_no,exchange_rate,remarks,inserted_by, insert_date"; 
		
		 $field_array_dtls="id, mst_id,receive_dtls_id, po_break_down_id, order_no,buyer_id, production_type, style_ref_no, item_number_id, country_id, total_issue, sale_rate, currency_id, fob_rate, issue_amount, bdt_amount, room_no, rack_no, shelf_no, bin_no, sewing_production_variable, style_order_wisw, variable_is_controll, user_lebel, remarks, inserted_by, insert_date"; 
		 
		 $field_array_mst_update="issue_purpose*party_id*party_name*pay_term*challan_no*remarks*inserted_by*insert_date"; 

		

		if($db_type==0)
		{
			if(str_replace("'","",$txt_mst_id)==''){			
				$data_array_mst="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$txt_issue_date.",".$cbo_goods_type.",".$cbo_order_type.",".$txt_party_name.",".$hidden_party_id.",".$cbo_issue_purpose.",".$cbo_store_name.",".$cbo_pay_term.",".$cbo_currency_mst.",".$txt_challan_no.",".$exchange_rate.",".$txt_remark.",".$user_id.",'".$pc_date_time."')";
			
				$data_array_dtls="(".$dtls_id.",'".$id."',".$hidden_receive_dtls_id.",".$txt_po_id.",".$txt_order_no.",".$cbo_buyer_name.",1,".$txt_style_name.",".$cbo_item_name.",".$cbo_country_name.",".$txt_total_issue.",".$txt_sale_rate.",".$cbo_currency.",".$txt_fob_rate.",".$txt_issue_amount.",".$txt_bdt_amount.",".$cbo_room_no.",".$cbo_rack_no.",".$cbo_shelf_no.",".$cbo_bin_no.",".$sewing_production_variable.",".$styleOrOrderWisw.",".$variable_is_controll.",".$txt_user_lebel.",".$txt_remark2.",".$user_id.",'".$pc_date_time."')";
			}else{
				$data_array_dtls="(".$dtls_id.",".$txt_mst_id.",".$hidden_receive_dtls_id.",".$txt_po_id.",".$txt_order_no.",".$cbo_buyer_name.",1,".$txt_style_name.",".$cbo_item_name.",".$cbo_country_name.",".$txt_total_issue.",".$txt_sale_rate.",".$cbo_currency.",".$txt_fob_rate.",".$txt_issue_amount.",".$txt_bdt_amount.",".$cbo_room_no.",".$cbo_rack_no.",".$cbo_shelf_no.",".$cbo_bin_no.",".$sewing_production_variable.",".$styleOrOrderWisw.",".$variable_is_controll.",".$txt_user_lebel.",".$txt_remark2.",".$user_id.",'".$pc_date_time."')";
			}
			
		}
		else
		{
			if(str_replace("'","",$txt_mst_id)==''){			
				$data_array_mst="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$txt_issue_date.",".$cbo_goods_type.",".$cbo_order_type.",".$txt_party_name.",".$hidden_party_id.",".$cbo_issue_purpose.",".$cbo_store_name.",".$cbo_pay_term.",".$cbo_currency_mst.",".$txt_challan_no.",".$exchange_rate.",".$txt_remark.",".$user_id.",'".$pc_date_time."')";
			
				$data_array_dtls="(".$dtls_id.",'".$id."',".$hidden_receive_dtls_id.",".$txt_po_id.",".$txt_order_no.",".$cbo_buyer_name.",1,".$txt_style_name.",".$cbo_item_name.",".$cbo_country_name.",".$txt_total_issue.",".$txt_sale_rate.",".$cbo_currency.",".$txt_fob_rate.",".$txt_issue_amount.",".$txt_bdt_amount.",".$cbo_room_no.",".$cbo_rack_no.",".$cbo_shelf_no.",".$cbo_bin_no.",".$sewing_production_variable.",".$styleOrOrderWisw.",".$variable_is_controll.",".$txt_user_lebel.",".$txt_remark2.",".$user_id.",'".$pc_date_time."')";
			}else{
				 $data_array_mst_update="".$cbo_issue_purpose."*".$hidden_party_id."*".$txt_party_name."*".$cbo_pay_term."*".$txt_challan_no."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";
				
				$data_array_dtls="(".$dtls_id.",".$txt_mst_id.",".$hidden_receive_dtls_id.",".$txt_po_id.",".$txt_order_no.",".$cbo_buyer_name.",1,".$txt_style_name.",".$cbo_item_name.",".$cbo_country_name.",".$txt_total_issue.",".$txt_sale_rate.",".$cbo_currency.",".$txt_fob_rate.",".$txt_issue_amount.",".$txt_bdt_amount.",".$cbo_room_no.",".$cbo_rack_no.",".$cbo_shelf_no.",".$cbo_bin_no.",".$sewing_production_variable.",".$styleOrOrderWisw.",".$variable_is_controll.",".$txt_user_lebel.",".$txt_remark2.",".$user_id.",'".$pc_date_time."')";
			}
		}
		 
 
		
		$dtlsData = sql_select("select a.color_size_break_down_id,
								sum(a.total_left_over_receive) as production_qnty
								from pro_leftover_gmts_rcv_dtls a, pro_leftover_gmts_rcv_mst   b
								where a.production_type=1 and a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name and a.color_size_break_down_id!=0  
								group by a.color_size_break_down_id");
		$color_pord_data=array();							
		foreach($dtlsData as $row)
		{				  
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')];
		}
		
		$field_array_color_size="id, mst_id, dtls_id, production_type, color_size_break_down_id, production_qnty";
		
  		if(str_replace("'","",$sewing_production_variable)==2)//color  wise
		{
			if(str_replace("'","",$cbo_order_type)==1)// self order
			{
				if(str_replace("'","",$cbo_item_name)!=0)
				{
					$item_cond = " and item_number_id=$cbo_item_name";
				}
				$color_sizeID_arr=sql_select( "select id, size_number_id, color_number_id 
					from wo_po_color_size_breakdown 
					where po_break_down_id=$txt_po_id  and country_id=$cbo_country_name $item_cond and status_active=1 and is_deleted=0  order by size_number_id, color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					// $index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$val[csf("color_number_id")]]=$val[csf("id")];
				}
			}
			else // subcon order
			{
				 
				$color_sizeID_arr=sql_select( "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm, embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part 
				from subcon_ord_breakdown 
				where order_id=$txt_po_id and item_id=$cbo_item_name");
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					// $index = $val[csf("size_id")].$color_arr[$val[csf("color_id")]].$val[csf("color_id")];
					$colSizeID_arr[$val[csf("color_id")]]=$val[csf("id")];
				}
			}
			
			
				
			$data_array_color_size="";
			$j=0;
 			$rowEx = array_filter(explode("**",$colorIDvalue)); 
			
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				
				$colorID = $colorAndSizeAndValue_arr[0];
				$colorSizeValue = $colorAndSizeAndValue_arr[1];	
				$index = $colorID;
				
				if($j==0)$data_array_color_size = "(".$color_size_id.",".$id.",".$dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array_color_size .= ",(".$color_size_id.",".$id.",".$dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$color_size_id=$color_size_id+1;
 				$j++;
			}
		}

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{
			if(str_replace("'","",$cbo_order_type)==1)
			{
				if(str_replace("'","",$cbo_item_name)!=0)//color and size wise
				{
					$item_cond = " and item_number_id=$cbo_item_name";
				}
				$color_sizeID_arr=sql_select( "select id, size_number_id, color_number_id 
					from wo_po_color_size_breakdown 
					where po_break_down_id=$txt_po_id  and country_id=$cbo_country_name $item_cond and status_active=1 and is_deleted=0  order by size_number_id, color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
			}
			else
			{
				 
				$color_sizeID_arr=sql_select( "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm, embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part 
				from subcon_ord_breakdown 
				where order_id=$txt_po_id and item_id=$cbo_item_name");
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_id")].$color_arr[$val[csf("color_id")]].$val[csf("color_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
			}
			
			
				
			$data_array_color_size="";
			$j=0;
 			$rowEx = array_filter(explode("***",$colorIDvalue)); 
			
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];				
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				
				if($j==0)$data_array_color_size = "(".$color_size_id.",".$id.",".$dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array_color_size .= ",(".$color_size_id.",".$id.",".$dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$color_size_id=$color_size_id+1;
 				$j++;
			}
		}
		
		
		$rID=$rID1=$rID2=$rID3=1;
		   
		if(str_replace("'","",$txt_mst_id)==''){
			$rID=sql_insert("pro_leftover_gmts_issue_mst",$field_array_mst,$data_array_mst,1);
		}
		
		if($data_array_dtls!=''){
			$rID1=sql_insert("pro_leftover_gmts_issue_dtls",$field_array_dtls,$data_array_dtls,1);
		}
 		
		if($data_array_color_size!=''){
			$rID2=sql_insert("pro_leftover_gmts_issue_clr_sz",$field_array_color_size,$data_array_color_size,1);
		}
		
		if($data_array_mst_update!=''){
			$rID3=sql_update("pro_leftover_gmts_issue_mst",$field_array_mst_update,$data_array_mst_update,"id","".$txt_mst_id."",1);
		}
		
		
		if(str_replace("'","",$txt_mst_id)!=""){
			$mstID = str_replace("'","",$txt_mst_id);
			$system_no = str_replace("'","",$txt_system_no);
		}else{
			$mstID = str_replace("'","",$id);
			$system_no = str_replace("'","",$new_sys_number[0]);
		}
		
		//echo "10** insert into pro_leftover_gmts_issue_mst ($field_array_mst) values $data_array_mst";die;
		//echo "10** insert into pro_leftover_gmts_issue_dtls ($field_array_dtls) values $data_array_dtls";die;
		//echo "10** insert into pro_leftover_gmts_issue_clr_sz ($field_array_color_size) values $data_array_color_size";die;
		
		
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$mstID."**".$system_no;die;
		
		
		
		if($db_type==0)
		{	  
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $rID1 && $rID2 && $rID3)
				{
					mysql_query("COMMIT");  
					echo "0**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name);
				}
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $rID1 && $rID2 && $rID3)
				{
					oci_commit($con); 
					echo "0**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name);
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
		 
		 $field_array_mst_update="issue_date*issue_purpose*currency_id*party_id*party_name*pay_term*challan_no*remarks*updated_by*update_date"; 

		 $data_array_mst_update="".$txt_issue_date."*".$cbo_issue_purpose."*".$cbo_currency_mst."*".$hidden_party_id."*".$txt_party_name."*".$cbo_pay_term."*".$txt_challan_no."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";


		$field_array_dtls_update="currency_id*total_issue*sale_rate*issue_amount*bdt_amount*room_no*rack_no*shelf_no*bin_no*remarks*updated_by*update_date";
		
		$data_array_dtls_update="".$cbo_currency."*".$txt_total_issue."*".$txt_sale_rate."*".$txt_issue_amount."*".$txt_bdt_amount."*".$cbo_room_no."*".$cbo_rack_no."*".$cbo_shelf_no."*".$cbo_bin_no."*".$txt_remark2."*".$user_id."*'".$pc_date_time."'";
		
		
 		$rID=sql_update("pro_leftover_gmts_issue_mst",$field_array_mst_update,$data_array_mst_update,"id","".$txt_mst_id."",1);
 		//echo $rID; die;

		$rID1=sql_update("pro_leftover_gmts_issue_dtls",$field_array_dtls_update,$data_array_dtls_update,"id","".$hidden_dtls_id."",1);
		//echo $rID1; die;
		
		$color_size_id=return_next_id("id", "pro_leftover_gmts_issue_clr_sz", 1);
		$field_array_color_size="id, mst_id, dtls_id, production_type, color_size_break_down_id, production_qnty";
		
  		$dtlsData = sql_select("select a.color_size_break_down_id, sum(a.total_issue) as production_qnty from pro_leftover_gmts_issue_dtls a, pro_leftover_gmts_issue_mst   b where a.production_type=1 and a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name and a.color_size_break_down_id!=0 group by a.color_size_break_down_id"); 
  		$color_pord_data=array();
		foreach($dtlsData as $row)
		{				  
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')];
		}
		
		//$field_array_color_size="id, mst_id, dtls_id, production_type, color_size_break_down_id, production_qnty";
		
  		if(str_replace("'","",$sewing_production_variable)==2)//color wise
		{			
			if(str_replace("'","",$cbo_order_type)==1) // self order
			{
				if(str_replace("'","",$cbo_item_name)!=0)
				{
					$item_cond = " and item_number_id=$cbo_item_name";
				}
				$color_sizeID_arr=sql_select( "select id, size_number_id, color_number_id 
					from wo_po_color_size_breakdown 
					where po_break_down_id=$txt_po_id  and country_id=$cbo_country_name $item_cond and status_active=1 and is_deleted=0  order by size_number_id, color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					// $index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$val[csf("color_number_id")]]=$val[csf("id")];
				}
			}
			else // subcon order
			{
				 
				$color_sizeID_arr=sql_select( "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm, embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part 
				from subcon_ord_breakdown 
				where order_id=$txt_po_id and item_id=$cbo_item_name");
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					// $index = $val[csf("size_id")].$color_arr[$val[csf("color_id")]].$val[csf("color_id")];
					$colSizeID_arr[$val[csf("color_id")]]=$val[csf("id")];
				}
			}
			
			
				
			$data_array_color_size="";
			$j=0;
 			$rowEx = explode("**",$colorIDvalue); 
			
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				
				$colorID = $colorAndSizeAndValue_arr[0];
				$colorSizeValue = $colorAndSizeAndValue_arr[1];	
				$index = $colorID;
				
				if($j==0)$data_array_color_size = "(".$color_size_id.",".$txt_mst_id.",".$hidden_dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array_color_size .= ",(".$color_size_id.",".$txt_mst_id.",".$hidden_dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$color_size_id=$color_size_id+1;
 				$j++;
			}
		}

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{			
			if(str_replace("'","",$cbo_order_type)==1)
			{
				if(str_replace("'","",$cbo_item_name)!=0)//color and size wise
				{
					$item_cond = " and item_number_id=$cbo_item_name";
				}
				$color_sizeID_arr=sql_select( "select id, size_number_id, color_number_id 
					from wo_po_color_size_breakdown 
					where po_break_down_id=$txt_po_id  and country_id=$cbo_country_name $item_cond and status_active=1 and is_deleted=0  order by size_number_id, color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
			}
			else
			{
				 
				$color_sizeID_arr=sql_select( "select id, mst_id, order_id, item_id, color_id, size_id, qnty, rate, amount, excess_cut, plan_cut, process_loss, gsm, embellishment_type, description, dia_width_type, grey_dia, finish_dia, body_part 
				from subcon_ord_breakdown 
				where order_id=$txt_po_id and item_id=$cbo_item_name");
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_id")].$color_arr[$val[csf("color_id")]].$val[csf("color_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
			}
			
			
				
			$data_array_color_size="";
			$j=0;
 			$rowEx = explode("***",$colorIDvalue); 
			
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];				
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				
				if($j==0)$data_array_color_size = "(".$color_size_id.",".$txt_mst_id.",".$hidden_dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array_color_size .= ",(".$color_size_id.",".$txt_mst_id.",".$hidden_dtls_id.",1,'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$color_size_id=$color_size_id+1;
 				$j++;
			}
		}
		
		$dtlsrDelete = execute_query("delete from pro_leftover_gmts_issue_clr_sz where dtls_id=$hidden_dtls_id",1);
		$rID2=1;
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$hidden_dtls_id)!='')
		{
			$rID2=sql_insert("pro_leftover_gmts_issue_clr_sz",$field_array_color_size,$data_array_color_size,1);
			//echo "10** insert into pro_leftover_gmts_rcv_clr_sz ($field_array_color_size) values $data_array_color_size";die;
		}

		//echo "10** insert into pro_leftover_gmts_issue_mst ($field_array_mst_update) values $data_array_mst_update";die;
		//echo "10** insert into pro_leftover_gmts_issue_dtls ($field_array_dtls) values $data_array_dtls";die;
		//echo "10** insert into pro_leftover_gmts_issue_clr_sz ($field_array_color_size) values $data_array_color_size";die;
		
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$dtlsrDelete;die;
		
		if(str_replace("'","",$txt_mst_id)!=""){
			$mstID = str_replace("'","",$txt_mst_id);
			$system_no = str_replace("'","",$txt_system_no);
		}else{
			$mstID = str_replace("'","",$id);
			$system_no = str_replace("'","",$new_sys_number[0]);
		}
		
		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $rID1 && $rID2 && $dtlsrDelete)
				{
					mysql_query("COMMIT");  
					echo "1**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name);
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $rID1 && $rID2 && $dtlsrDelete)
				{
					oci_commit($con); 
					echo "1**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name);
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mstID."**".$system_no."**".str_replace("'","",$cbo_company_name);
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
		 
 		$rID = sql_delete("pro_leftover_gmts_rcv_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$rID1 = sql_delete("pro_leftover_gmts_rcv_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id ',$txt_mst_id,1);
		
		$rID2 = sql_delete("pro_leftover_gmts_rcv_clr_sz","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		
 		if($db_type==0)
		{
			if($rID && $rID1 && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$hidden_po_break_down_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$hidden_po_break_down_id);;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2)
			{
				oci_commit($con);   
				echo "2**".str_replace("'","",$hidden_po_break_down_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id);;
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="left_over_gmts_issue_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	echo load_html_head_contents("Left Over Issue Info","../", 1, 1, $unicode,'','');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$location_library=return_library_array( "select id,location_name from  lib_location", "id","location_name"  );
	$store_name_library=return_library_array( "select id,store_name from lib_store_location", "id","store_name"  );
	$floor_name_library=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0", "id","floor_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$issue_purpose_arr = array(1=>"Sell",2=>"Gift",3=>"Others");
	?>
	<div style="width:1010px;">
	    <table width="1000" cellspacing="0" align="right" style="margin-bottom:20px;">
	        <tr>
	            <td rowspan="3" align="center"><img src="../<? echo $image_location; ?>" height="50" width="60"></td>
	            <td colspan="6" align="center"  style="font-size:xx-large; "><strong><? echo $company_library[$data[0]]; ?></strong></td>
	            <td rowspan="3" width="60" >&nbsp;</td>
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
	        <tr>
	            <td colspan="6" style="font-size:x-large; text-align:center;"><strong><u><? echo $data[2]; ?></u></strong></td>           
	        </tr>
	         <tr>
	            <td colspan="8" style="text-align:center;"><strong>&nbsp;</strong></td>
	        </tr>
	        <?
			
			/*$sql_mst = "SELECT a.id, a.sys_number, a.company_id, a.location, a.issue_date, a.order_type, a.buyer_name, a.store_name, a.floor_id, a.exchange_rate, a.garments_nature, a.goods_type, a.remarks as mst_remarks
			from pro_leftover_gmts_issue_mst a
			where a.company_id='$data[0]' and a.id=$data[1] and a.status_active=1 and a.is_deleted=0";
			$result_mst=sql_select($sql_mst);
			
			$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");*/	

			$sql_dtls="SELECT a.issue_date,a.issue_purpose,a.challan_no,a.party_name, a.sys_number, b.id, b.mst_id, b.buyer_id, b.po_break_down_id, b.order_no, b.sale_rate, b.style_ref_no, sum(b.total_issue) as total_issue,sum( b.issue_amount) as issue_amount, b.remarks, b.currency_id from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and b.mst_id=$data[1] group by a.issue_date, a.issue_purpose, a.challan_no,a.party_name, a.sys_number, b.id, b.mst_id, b.buyer_id, b.po_break_down_id, b.order_no, b.style_ref_no, b.sale_rate, b.remarks,b.currency_id";
			$result_dtls=sql_select($sql_dtls);
			//print_r($result_dtls);
			$po_id_arr = array();
			foreach ($result_dtls as  $val) 
			{
				$po_id_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
			}
			$poIds = implode(",", $po_id_arr);
			$job_array = return_library_array( "SELECT id, job_no_mst from wo_po_break_down  where id in($poIds)", "id", "job_no_mst"  );			
			// print_r($job_arrray);
			// foreach($result_dtls as $row)
			// {			
			// 	$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["remarks"] = $row[csf("remarks")];
			// 	$report_data_arr[$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["currency_id"] = $row[csf("currency_id")];
			// }
				
					// echo "<pre>";
					// print_r($report_data_arr);
			?>
	    </table>
	    <table width="1000"" cellspacing="0" align="right" style="margin-top: 20px;">
			<tr>
				<td width="150"><strong>System Id</strong></td><td width="30px"><strong>:</strong></td><td width="250"><? echo $result_dtls[0][csf('sys_number')]; ?></td>
				<td width="150"><strong>Party Name</strong></td><td width="30px"><strong>:</strong></td><td width="250"><? echo $result_dtls[0][csf('party_name')]; ?></td>								
				<td width="150"><strong>Currency</strong></td> <td width="30px"><strong>:</strong></td><td width="250"><? echo $currency[$result_dtls[0][csf("currency_id")]]; ?></td>								
			</tr>
			<tr style="height: 20px;">
			</tr>
			<tr>
				<td width="150"><strong>Challan No</strong></td><td width="30px"><strong>:</strong></td><td width="250"><? echo $result_dtls[0][csf('challan_no')]; ?></td>
				<td width="170"><strong>Issue Purpose</strong></td><td width="30px"><strong>:</strong></td><td width="250"><? echo $issue_purpose_arr[$result_dtls[0][csf('issue_purpose')]]; ?></td>										
			</tr>
			<tr style="height: 20px;">
			</tr>					
		</table>
	    <br><br>
	    <table align="right" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="70">SL</th>
	            <th width="100">Date</th>
	            <th width="120">Job No</th>
	            <th width="140">PO</th>
	            <th width="120">Style Ref</th>
	            <th width="120">Buyer</th>
	            <th width="120">Issue Qty</th>
	            <th width="120">Sale Rate</th>
	            <th width="100" style="word-break: normal;">Sale Amount</th>	            
	            <th width="">Remarks</th>
	        </thead>
	        <tbody style="font-size:12px;">
				<?
		        $i=1;
		       // $tot_qnty=array();
		        $tot_qnty=0;
		        foreach($result_dtls as $size_id=>$size_data)
				{
					if ($i%2==0)   $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";				
					$tot_qnty += $size_data["production_qnty"];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i;  ?></td>
						<td><? echo change_date_format($size_data[csf("issue_date")]); ?></td>
						<td><? echo $job_array[$size_data[csf("po_break_down_id")]]; ?></td>
						<td><? echo $size_data[csf("order_no")]; ?></td>
						<td><? echo $size_data[csf("style_ref_no")]; ?></td>
						<td><? echo $buyer_library[$size_data[csf("buyer_id")]]; ?></td>
	                    <td><? echo $size_data[csf("total_issue")]; ?></td>															
	                    <td><? echo $size_data[csf("sale_rate")]; ?></td>															
						<td><? $tot_issue_amount=$size_data[csf("total_issue")]*$size_data[csf("sale_rate")]; echo $tot_issue_amount; ?></td>
						<td><? echo $size_data[csf("remarks")]; ?></td>						 
					</tr>
					<?
					$i++;
				}
		        ?>
	        </tbody>
	    </table>
	    <?  
			echo signature_table(178, $data[0], "1000px","","10"); 
		?>
	<!-- 	<br><br><br><br><br><br>
		<table width="1000" align="center" >	
	        
        <tr>
        	<td>&nbsp</td>        	
        	<td style="text-decoration-line: overline"><strong>Prepared By</strong>></td>
        	<td style="text-decoration-line: overline"><strong>Received By</strong>></td>
        	<td style="text-decoration-line: overline"><strong>Store Department</strong></td>           
            <td style="text-decoration-line: overline"><strong>Authorized By</strong></td>
        </tr>	        
	    </table>
		<br> -->
	</div>
	<?
	exit();	
}




function sql_update_test($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
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
	
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	 echo $strQuery; die;
	  //return $strQuery; die;
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

exit();
?>