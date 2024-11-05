<?
session_start();
include('../../includes/common.php');


$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//************************************ Start *************************************************
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select ex_factory,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val('".$result[csf("ex_factory")]."');\n";
		echo "$('#styleOrOrderWisw').val('".$result[csf("production_entry")]."');\n";
	}
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=32","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";
 	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
}

/*if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}*/

if ($action=="lcsc_popup")
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
				document.getElementById('search_by_th_up').innerHTML="Enter Invoice No";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter LC/SC No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<?php
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				}
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:240px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}
		}

	function js_set_value(invoice_id,invoice_no,lcsc_id,lcsc_no)
	{
 		$("#hidden_invoice_no").val(invoice_id+"**"+invoice_no);
		$("#hidden_lcsc_no").val(lcsc_id+"**"+lcsc_no);
    	parent.emailwindow.hide();
 	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="770" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
             <thead>
                <th width="160">Search By</th>
                <th width="250" align="center" id="search_by_th_up">Enter Invoice No</th>
                <th width="200">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </thead>
            <tr align="center">
                <td>
                <?
                $searchby_arr=array(0=>"Invoice No",1=>"LS/SC No",2=>"Buyer Name");
                echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 0, "-- Select --", $selected, "search_populate(this.value)",0 );
                ?>
                </td>
                <td align="center" id="search_by_td">
                    <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
                </td>
                <td align="center">
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                </td>
                <td align="center">
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_lcsc_search_list', 'search_div', 'ex_factory_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td align="center" height="40" colspan="4" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_invoice_no" >
                    <input type="hidden" id="hidden_lcsc_no" >
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
}

if($action=="create_lcsc_search_list")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and a.invoice_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.lc_sc_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_id = '$txt_search_common'";
 	}

	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and a.invoice_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.invoice_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!="") $sql_cond .= " and a.benificiary_id='$company'";

	if($db_type==0)
	{
 		$sql = "select a.id, a.invoice_no, a.invoice_date, a.buyer_id, a.lc_sc_id, sum(b.current_invoice_qnty) as order_quantity, group_concat(b.po_breakdown_id) as po_id, a.benificiary_id,a.is_lc from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id = b.mst_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 $sql_cond group by a.id order by a.invoice_no";
	}
	else
	{

		$sql = "select a.id, a.invoice_no, max(a.invoice_date) as invoice_date, a.buyer_id, a.lc_sc_id, sum(b.current_invoice_qnty) as order_quantity, listagg(CAST(b.po_breakdown_id as VARCHAR(4000)),',') within group (order by b.po_breakdown_id) as po_id, a.benificiary_id,a.is_lc from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id = b.mst_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 $sql_cond group by a.id,a.invoice_no,a.buyer_id,a.lc_sc_id,a.benificiary_id,a.is_lc order by a.invoice_no";

	}
	//echo $sql;die;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
     //echo create_list_view("list_view","Invoice NO,Invoice Date,Buyer,LC/SC No,Order Qunty,Company","130,100,170,100,100,150","850","250",1,$sql,"js_set_value","invoice_no,lc_sc_no","",1,"0,0,buyer_id,0,0,benificiary_id",$printed_array,"invoice_no,invoice_date,buyer_id,lc_sc_no,order_quantity,benificiary_id","requires/ex_factory_controller","setFilterGrid('tbl_po_list',1)","0,0,0,0,0,1","","");
   ?>
  	<div style="width:870px; margin-top:10px">
     	<table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="120" >Invoice No</th>
                <th width="75" >Invoice Date</th>
                <th width="120" >Buyer</th>
                <th width="150" >LC/SC No</th>
                <th width="120" >Order No</th>
                <th width="120" >Order Qty</th>
                <th width="">Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:870px; max-height:220px;overflow-y:scroll;" >
        <table cellspacing="0" width="852" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
 				$po_id=$row[csf("po_id")];
				if ($po_id!='')
				{
					if($db_type==0)
					{
						$po_number=return_field_value("group_concat(distinct(po_number)) as po_number","wo_po_break_down","id in ($po_id)",'po_number');
					}
					else
					{

						$po_number=return_field_value("listagg(CAST(po_number as VARCHAR(4000)),',') within group (order by po_number) as po_number","wo_po_break_down","id in ($po_id)",'po_number');
					}

				}
				if($row["is_lc"]==1) //  lc
					$lc_sc = return_field_value("export_lc_no","com_export_lc","id=".$row[csf('lc_sc_id')]."");
				else
					$lc_sc = return_field_value("contract_no","com_sales_contract","id=".$row[csf('lc_sc_id')]."");

 					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')];?>,'<? echo $row[csf('invoice_no')];?>','<? echo $row[csf('lc_sc_id')]; ?>','<? echo $lc_sc;?>');" >
							<td width="30" align="center"><?php echo $i; ?></td>
                            <td width="120" align="left"><p><?php echo $row[csf("invoice_no")]; ?></p></td>
							<td width="75" align="center"><?php echo change_date_format($row[csf("invoice_date")]);?></td>
 							<td width="120"><p><?php echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
							<td width="150"><p><?php echo $lc_sc; ?></p></td>
                            <td width="120"><p><?php echo $po_number; ?></p></td>
							<td width="120" align="right"><?php echo $row[csf("order_quantity")];?> </td>
 							<td width=""><p><?php  echo $company_arr[$row[csf("benificiary_id")]];?></p></td>
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
				document.getElementById('search_by_th_up').innerHTML="Internal Ref";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<?php

					$buyer_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name",'id','buyer_name');
				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				}
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:230px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}
		}
	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id);
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_country_id").val(country_id);
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
                     			<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'ex_factory_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];

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
 		$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.file_no,b.grouping,b.po_quantity ,b.plan_cut from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c  where a.job_no = b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond group by b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.file_no,b.grouping,b.po_quantity ,b.plan_cut";
	}
	else
	{
		$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.file_no,b.grouping,b.po_quantity ,b.plan_cut from wo_po_details_master a, wo_po_break_down b  where a.job_no = b.job_no_mst and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond";
	}
	//echo $sql;
	$result = sql_select($sql);
	//print_r($result );
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');


	if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}


	$po_country_data_arr=array();
	$poCountryData=sql_select( "select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	}


	$total_ex_fac_data_arr=array();
	$total_ex_fac_arr=sql_select( "select po_break_down_id, item_number_id, country_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where entry_form <> 85 and  status_active=1 and is_deleted=0 and entry_form <> 85  group by po_break_down_id, item_number_id, country_id");
	foreach($total_ex_fac_arr as $row)
	{
		$total_ex_fac_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('ex_factory_qnty')];
	}
	?>
	<div style="width:1190px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Buyer</th>
                <th width="120">Style</th>
                <th width="80">File No</th>
                <th width="80">Internal Ref.</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Total Ex-factory Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1190px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1172" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));

				$numOfCountry = count($country);

				for($k=0;$k<$numOfItem;$k++)
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach($country as $country_id)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>');" >
								<td width="30" align="center"><?php echo $i; ?></td>
								<td width="70" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>
								<td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
								<td width="100"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
								<td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
                                <td width="80"><p><?php echo $row[csf("file_no")]; ?></p></td>
                                <td width="80"><p><?php echo $row[csf("grouping")]; ?></p></td>
								<td width="140"><p><?php  echo $garments_item[$grmts_item];?></p></td>
								<td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
								<td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
                                <td width="80" align="right">
								<?php
								echo $total_cut_qty=$total_ex_fac_data_arr[$row[csf('id')]][$grmts_item][$country_id];
                                 ?> &nbsp;
                               </td>
                               <td width="80" align="right">
                                <?php
                                 $balance=$po_qnty-$total_cut_qty;
                                 echo $balance;
                                 ?>&nbsp;
                               </td>
								<td><?php  echo $company_arr[$row[csf("company_name")]];?> </td>
							</tr>
						<?
						$i++;
					}
				}
            }
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

	$res = sql_select("select a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name,a.shipment_date   from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id=$po_id");

 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_shipment_date').val('".$result[csf('shipment_date')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";

		$finish_qty = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and production_type=8 and country_id='$country_id' and status_active=1 and is_deleted=0");
 		if($finish_qty=="")$finish_qty=0;

		$total_produced = return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and entry_form <> 85 and  is_deleted=0");
		if($total_produced=="")$total_produced=0;

 		echo "$('#txt_finish_quantity').val('".$finish_qty."');\n";
 		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_produced."');\n";
		echo "$('#txt_cumul_quantity').val('".$total_produced."');\n";
		$yet_to_produced = $finish_qty-$total_produced;
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
	$country_id = $dataArr[4];

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	//#############################################################################################//
	// order wise - color level, color and size level

	$ex_fac_value=array();

	//$variableSettings=2;

	if( $variableSettings==2 ) // color level
	{
		if($db_type==0)
		{
			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty,
			(select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END)
			from pro_garments_production_dtls pdtls where pdtls.production_type=8 and pdtls.is_deleted=0 ) as production_qnty,
			(select sum(CASE WHEN ex.color_size_break_down_id=wo_po_color_size_breakdown.id then ex.production_qnty ELSE 0 END)
			from pro_ex_factory_dtls ex where ex.is_deleted=0 ) as ex_production_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";*/

			$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN b.production_type=8 then b.production_qnty ELSE 0 END) as production_qnty
					from wo_po_color_size_breakdown a
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.status_active=1 and b.is_deleted=0
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

			$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
                    left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.status_active=1 and ex.is_deleted=0
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];

			}
		}
		else
		{
			$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN b.production_type=8 then b.production_qnty ELSE 0 END) as production_qnty
					from wo_po_color_size_breakdown a
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.status_active=1 and b.is_deleted=0
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

			$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
                    left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.status_active=1 and ex.is_deleted=0
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];

			}
		}
	}
	else if( $variableSettings==3 ) //color and size level
	{
		/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=8 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN ex.color_size_break_down_id=wo_po_color_size_breakdown.id then ex.production_qnty ELSE 0 END) from pro_ex_factory_dtls ex where ex.is_deleted=0 ) as ex_production_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/


			$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.color_size_break_down_id");
			foreach($prodData as $row)
			{
				$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
			}

			$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
                    left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id and ex.status_active=1 and ex.is_deleted=0
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id, a.size_number_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];

			}

			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, id";*/
			$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
					from wo_po_color_size_breakdown a
					where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_number_id, a.size_order";
	}
	else // by default color and size level
	{
		/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=8 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN ex.color_size_break_down_id=wo_po_color_size_breakdown.id then ex.production_qnty ELSE 0 END) from pro_ex_factory_dtls ex where ex.is_deleted=0 ) as ex_production_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/


			$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=8 group by a.color_size_break_down_id");
			foreach($prodData as $row)
			{
				$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
			}

			$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
                    left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id, a.size_number_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];

			}

			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, id";*/

			$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
					from wo_po_color_size_breakdown a
					where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_number_id, a.size_order";

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
			//echo "shajjad_".$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]];


			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
			$totalQnty += $color[csf("production_qnty")]-$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]];
			$colorID .= $color[csf("color_number_id")].",";
		}
		else //color and size level
		{
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

			$pro_qnty=$color_size_pro_qnty_array[$color[csf('id')]];
			$exfac_qnty=$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]][$color[csf('size_number_id')]];

			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty-$exfac_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';
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

if($action=="show_dtls_listview")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
?>
	<div style="width:930px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="150" align="center">Item Name</th>
                <th width="110" align="center">Country</th>
                <th width="110" align="center">Ex-Fact. Date</th>
                <th width="110" align="center">Ex-Fact. Qnty</th>
                <th width="120" align="center">Invoice No</th>
                <th width="120" align="center">LC/SC No</th>
                <th align="center">Challan No</th>
            </thead>
    	</table>
    </div>
	<div style="width:930px;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php
			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("select id,po_break_down_id,item_number_id,country_id,ex_factory_date,ex_factory_qnty,location,lc_sc_no,invoice_no,challan_no from  pro_ex_factory_mst where entry_form <> 85 and  po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0 order by id");
 			foreach($sqlResult as $selectResult)
			{
 				if ($i%2==0)
                	$bgcolor="#E9F3FF";
                else
               	 	$bgcolor="#FFFFFF";

				$total_production_qnty+=$selectResult[csf('ex_factory_qnty')];

				$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where id='".$selectResult[csf('invoice_no')]."'");
				foreach($sqlEx as $val)
				{
					if($val["is_lc"]==1) //  lc
						$lc_sc = return_field_value("export_lc_no","com_export_lc","id=".$val[csf('lc_sc_id')]."");
					else
						$lc_sc = return_field_value("contract_no","com_sales_contract","id=".$val[csf('lc_sc_id')]."");

					$invoiceNo = $val[csf('invoice_no')];
				}
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_exfactory_form_data','requires/ex_factory_controller');" >
                    <td width="40" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="110" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?>&nbsp;</p></td>
                    <td width="110" align="center"><p><?php echo change_date_format($selectResult[csf('ex_factory_date')]); ?></p></td>
                    <td width="110" align="center"><p><?php echo $selectResult[csf('ex_factory_qnty')]; ?></p></td>
                    <td width="120" align="center"><p><?php echo $invoiceNo; ?>&nbsp;</p></td>
                    <td width="120" align="center"><p><?php echo $lc_sc; ?>&nbsp;</p></td>
                    <td align="center"><p><?php echo $selectResult[csf('challan_no')]; ?>&nbsp;</p></td>
                </tr>
			<?php
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
<?
	exit();
}

if($action=="show_country_listview")
{
?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="110">Item Name</th>
            <th width="80">Country</th>
            <th width="75">Shipment Date</th>
            <th>Order Qty.</th>
        </thead>
		<?
		$i=1;
		$sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);">
				<td width="30"><? echo $i; ?></td>
				<td width="110"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="75" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right"><?php  echo $row[csf('order_qnty')]; ?></td>
			</tr>
		<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="populate_exfactory_form_data")
{
	$ex_fac_value=array();

	$sqlResult =sql_select("select id,garments_nature,po_break_down_id,item_number_id,country_id,location,ex_factory_date,ex_factory_qnty,total_carton_qnty,challan_no,invoice_no,lc_sc_no,carton_qnty,transport_com,remarks,shiping_status,entry_break_down_type,inspection_qty_validation  from pro_ex_factory_mst where entry_form <> 85 and  id='$data' and status_active=1 and is_deleted=0 order by id");
 	foreach($sqlResult as $result)
	{
 		echo "$('#cbo_location_name').val('".$result[csf('location')]."');\n";
		echo "$('#txt_ex_factory_date').val('".change_date_format($result[csf('ex_factory_date')])."');\n";
		echo "$('#txt_ex_quantity').attr('placeholder','".$result[csf('ex_factory_qnty')]."');\n";
 		echo "$('#txt_ex_quantity').val('".$result[csf('ex_factory_qnty')]."');\n";
		echo "$('#txt_total_carton_qnty').val('".$result[csf('total_carton_qnty')]."');\n";
		echo "$('#txt_challan_no').val('".$result[csf('challan_no')]."');\n";
		echo "$('#cbo_ins_qty_validation_type').val('".$result[csf('inspection_qty_validation')]."');\n";
		echo "$('#txt_invoice_no').val('');\n";
		echo "$('#txt_invoice_no').attr('placeholder','');\n";
 		echo "$('#txt_lc_sc_no').val('');\n";
		echo "$('#txt_lc_sc_no').attr('placeholder','');\n";



		$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where id='".$result[csf('invoice_no')]."'");
		foreach($sqlEx as $val)
		{
			echo "$('#txt_invoice_no').val('".$val[csf('invoice_no')]."');\n";
			echo "$('#txt_invoice_no').attr('placeholder','".$val[csf('id')]."');\n";

			if($val["is_lc"]==1) //  lc
					$lc_sc = return_field_value("export_lc_no","com_export_lc","id=".$val[csf('lc_sc_id')]."");
				else
					$lc_sc = return_field_value("contract_no","com_sales_contract","id=".$val[csf('lc_sc_id')]."");

			echo "$('#txt_lc_sc_no').val('".$lc_sc."');\n";
			echo "$('#txt_lc_sc_no').attr('placeholder','".$val[csf('lc_sc_id')]."');\n";
		}


 		echo "$('#txt_ctn_qnty').val('".$result[csf('carton_qnty')]."');\n";
		echo "$('#txt_transport_com').val('".$result[csf('transport_com')]."');\n";
		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		echo "$('#shipping_status').val('".$result[csf('shiping_status')]."');\n";

		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_exFactory_entry',1,1);\n";

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];


		//$variableSettings=2;

		if( $variableSettings!=1 ) // gross level
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];

			$sql_dtls = sql_select("select color_size_break_down_id,production_qnty,size_number_id, color_number_id from  pro_ex_factory_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
			foreach($sql_dtls as $row)
			{
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
			}

			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
					/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=8 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN ex.color_size_break_down_id=wo_po_color_size_breakdown.id then ex.production_qnty ELSE 0 END) from pro_ex_factory_dtls ex where ex.is_deleted=0 ) as ex_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";*/

					$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=8 then b.production_qnty ELSE 0 END) as production_qnty
							from wo_po_color_size_breakdown a
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

					$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
							left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id");
					foreach($sql_exfac as $row_exfac)
					{
						$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];

					}
				}
				else
				{
					$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN b.production_type=8 then b.production_qnty ELSE 0 END) as production_qnty
							from wo_po_color_size_breakdown a
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

					$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
							left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id");
					foreach($sql_exfac as $row_exfac)
					{
						$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]]=$row_exfac[csf("ex_production_qnty")];

					}
				}

			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=8 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN ex.color_size_break_down_id=wo_po_color_size_breakdown.id then ex.production_qnty ELSE 0 END) from pro_ex_factory_dtls ex where ex.is_deleted=0 ) as ex_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1"; */

					$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=8 group by a.color_size_break_down_id");
			foreach($prodData as $row)
			{
				$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
			}

			$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
                    left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id, a.size_number_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];

			}

			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id";*/
			$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
					from wo_po_color_size_breakdown a
					where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_number_id, a.size_order";

			}
			else // by default color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=8 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN ex.color_size_break_down_id=wo_po_color_size_breakdown.id then ex.production_qnty ELSE 0 END) from pro_ex_factory_dtls ex where ex.is_deleted=0 ) as ex_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1"; */


					$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type=8 group by a.color_size_break_down_id");
			foreach($prodData as $row)
			{
				$color_size_pro_qnty_array[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
			}

			$sql_exfac=sql_select("select a.item_number_id,a.color_number_id,a.size_number_id,sum(ex.production_qnty) as ex_production_qnty from wo_po_color_size_breakdown a
                    left join pro_ex_factory_dtls ex on ex.color_size_break_down_id=a.id
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id, a.size_number_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_value[$row_exfac[csf("item_number_id")]][$row_exfac[csf("color_number_id")]][$row_exfac[csf("size_number_id")]]=$row_exfac[csf("ex_production_qnty")];

			}

			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id";*/
			$sql = "select a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty, a.color_order, a.size_order
					from wo_po_color_size_breakdown a
					where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_number_id, a.size_order";


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
					$amount = $amountArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
					$totalQnty += $amount;
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

					$pro_qnty=$color_size_pro_qnty_array[$color[csf('id')]];
					$exfac_qnty=$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]][$color[csf('size_number_id')]];

					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty-$exfac_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ></td></tr>';
					$colorWiseTotal += $amount;
				}
				$i++;
			}
			//echo $colorHTML;die;
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
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
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_name");
	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active in(1,2,3) and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{			
			echo "786**Projected PO is not allowed to production. Please check variable settings";die();
		}
	}
	$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=32");

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		//table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		$country_order_qty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0");

		$country_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and entry_form <> 85 and  is_deleted=0");
		$country_exfactory_qty=$country_exfactory_qty+str_replace("'","",$txt_ex_quantity);
		if($country_exfactory_qty>=$country_order_qty) $country_order_status=3; else $country_order_status=str_replace("'","",$shipping_status);


		//----------Compare buyer inspection qty and ex-factory qty for validation----------------

		if($is_control==1 && $user_level!=2)
		{
			$cbo_ins_qty_validation_type=str_replace("'","",$cbo_ins_qty_validation_type);
			if($cbo_ins_qty_validation_type==2)
			{
			$country_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and inspection_status=1 and status_active=1 and is_deleted=0");
				if($country_insfection_qty < $country_exfactory_qty)
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}

			}
			else
			{
			$order_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and inspection_status=1 and status_active=1 and is_deleted=0");

			$order_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and status_active=1 and entry_form <> 85 and  is_deleted=0");

				if($order_insfection_qty < $order_exfactory_qty+str_replace("'","",$txt_ex_quantity))
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}
			}

		}
		//--------------------------------------------------------------Compare end;






		$id=return_next_id("id", "pro_ex_factory_mst", 1);
  		$field_array1="id, garments_nature, po_break_down_id, item_number_id, country_id, location, ex_factory_date, ex_factory_qnty, total_carton_qnty, challan_no, invoice_no, lc_sc_no, carton_qnty, transport_com, remarks, shiping_status, entry_break_down_type,inspection_qty_validation, inserted_by, insert_date";
		$data_array1="(".$id.",".$garments_nature.",".$hidden_po_break_down_id.", ".$cbo_item_name.",".$cbo_country_name.",".$cbo_location_name.",".$txt_ex_factory_date.",".$txt_ex_quantity.",".$txt_total_carton_qnty.",".$txt_challan_no.",'".$invoice_id."','".$lcsc_id."',".$txt_ctn_qnty.",".$txt_transport_com.",".$txt_remark.",".$shipping_status.",".$sewing_production_variable.",".$cbo_ins_qty_validation_type.",".$user_id.",'".$pc_date_time."')";

		//echo "INSERT INTO pro_ex_factory_mst (".$field_array1.") VALUES ".$data_array1;die;

 		//$rID=sql_insert("pro_ex_factory_mst",$field_array1,$data_array1,1);

		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name",1);

		$country_wise_status=return_field_value("count(id)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active=1 and is_deleted=0");
		if($country_wise_status>0) $order_status=2; else $order_status=3;
 		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id",1);

		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and entry_form <> 85 ",1);
		// pro_ex_factory_dtls table entry here ----------------------------------///


		$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b
								where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type=8 group by a.color_size_break_down_id");
		foreach($prodData as $row)
		{
			$color_size_data[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
		}

		$sql_exfac=sql_select("select a.color_size_break_down_id, sum(a.production_qnty) as ex_production_qnty
							from pro_ex_factory_dtls a, pro_ex_factory_mst b
							where a.status_active=1 and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and b.entry_form <> 85
							group by a.color_size_break_down_id");
		foreach($sql_exfac as $row_exfac)
		{
			$ex_fac_data[$row_exfac[csf("color_size_break_down_id")]]=$row_exfac[csf("ex_production_qnty")];

		}

		$field_array="id,mst_id,color_size_break_down_id,production_qnty";

		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 			$rowEx = explode("**",$colorIDvalue);
 			$dtls_id=return_next_id("id", "pro_ex_factory_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);

				if($is_control==1 && $user_level!=2)
				{
					$garments_delivery_data=0;
					if($colorSizeNumberIDArr[1]>0)
					{
						$garments_delivery_data=$color_size_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]-$ex_fac_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]];
						if(($colorSizeNumberIDArr[1]*1)>($garments_delivery_data*1))
						{
							echo "35**Ex-factory Quantity Not Over Finish Qnty";
							disconnect($con);
							die;
						}
					}
				}


				if($j==0)$data_array = "(".$dtls_id.",".$id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
				else $data_array .= ",(".$dtls_id.",".$id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
				$dtls_id=$dtls_id+1;
 				$j++;
			}
 		}

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{
			$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
 			$rowEx = explode("***",$colorIDvalue);
			$dtls_id=return_next_id("id", "pro_ex_factory_dtls", 1);
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
					$garments_delivery_data=0;
					if($colorSizeValue>0)
					{
						$garments_delivery_data=$color_size_data[$colSizeID_arr[$index]]-$ex_fac_data[$colSizeID_arr[$index]];
						if(($colorSizeValue*1)>($garments_delivery_data*1))
						{
							echo "35**Ex-factory Quantity Not Over Finish Qnty";
							disconnect($con);
							die;
						}
					}
				}

				if($j==0)$data_array = "(".$dtls_id.",".$id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				else $data_array .= ",(".$dtls_id.",".$id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
				$dtls_id=$dtls_id+1;
 				$j++;
			}
		}

		//echo "INSERT INTO pro_ex_factory_dtls (".$field_array.") VALUES ".$data_array;die;


		$rID=sql_insert("pro_ex_factory_mst",$field_array1,$data_array1,1);

		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("pro_ex_factory_dtls",$field_array,$data_array,1);
		}

		$invoiceID=true;
		if($invoice_id!="")
		{
 			$invoiceID=sql_update("com_export_invoice_ship_mst","ex_factory_date",$txt_ex_factory_date,"id",$invoice_id,1);
		}

		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{

			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID && $sts_ex_mst && $sts_country && $sts_ex && $invoiceID)
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
				if($rID && $sts_ex_mst && $sts_ex && $sts_country)
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
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID && $sts_ex_mst && $sts_country && $sts_ex && $invoiceID)
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
				if($rID && $sts_ex_mst && $sts_ex && $sts_country)
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
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}


		$field_array1="garments_nature*location*ex_factory_date*ex_factory_qnty*total_carton_qnty*challan_no*invoice_no*lc_sc_no*carton_qnty*transport_com*remarks*shiping_status*entry_break_down_type*inspection_qty_validation*updated_by*update_date";
		$data_array1="".$garments_nature."*".$cbo_location_name."*".$txt_ex_factory_date."*".$txt_ex_quantity."*".$txt_total_carton_qnty."*".$txt_challan_no."*'".$invoice_id."'*'".$lcsc_id."'*".$txt_ctn_qnty."*".$txt_transport_com."*".$txt_remark."*".$shipping_status."*".$sewing_production_variable."*".$cbo_ins_qty_validation_type."*".$user_id."*'".$pc_date_time."'";

 		//$rID=sql_update("pro_ex_factory_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		//echo $country_order_qty."**".$data_array;die;

		// pro_ex_factory_mst table data entry here
		$country_order_qty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0");

		$country_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and entry_form <> 85  and status_active=1 and is_deleted=0 and id<>$txt_mst_id");
		$country_exfactory_qty=$country_exfactory_qty+str_replace("'","",$txt_ex_quantity);

		if($country_exfactory_qty>=$country_order_qty) $country_order_status=3; else $country_order_status=str_replace("'","",$shipping_status);


		//----------Compare buyer inspection qty and ex-factory qty for validation----------------

		if($is_control==1 && $user_level!=2)
		{
			$cbo_ins_qty_validation_type=str_replace("'","",$cbo_ins_qty_validation_type);
			if($cbo_ins_qty_validation_type==2)
			{
			$country_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and inspection_status=1 and status_active=1 and is_deleted=0");
				if($country_insfection_qty < $country_exfactory_qty)
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}

			}
			else
			{
			$order_insfection_qty=return_field_value("sum(inspection_qnty)","pro_buyer_inspection","po_break_down_id=$hidden_po_break_down_id and inspection_status=1 and status_active=1 and is_deleted=0");

			$order_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and entry_form <> 85  and status_active=1 and is_deleted=0 and id<>$txt_mst_id");

				if($order_insfection_qty < $order_exfactory_qty+str_replace("'","",$txt_ex_quantity))
				{
					echo "25**".str_replace("'","",$hidden_po_break_down_id);
					disconnect($con);
					die;
				}
			}

		}
		//--------------------------------------------------------------Compare end;






		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name",1);

		$country_wise_status=return_field_value("count(id)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active=1 and is_deleted=0");
		if($country_wise_status>0) $order_status=2; else $order_status=3;
 		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id",1);

		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and entry_form <> 85   and country_id=$cbo_country_name",1);
		$dtlsrDelete=true;
		$dtlsrID=true;
		$rID=sql_update("pro_ex_factory_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			// pro_ex_factory_dtls table entry here ----------------------------------///

			$prodData = sql_select("select a.color_size_break_down_id,sum(a.production_qnty) as production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b
								where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type=8 group by a.color_size_break_down_id");
			foreach($prodData as $row)
			{
				$color_size_data[$row[csf('color_size_break_down_id')]]= $row[csf('production_qnty')];
			}

			$sql_exfac=sql_select("select a.color_size_break_down_id, sum(a.production_qnty) as ex_production_qnty
								from pro_ex_factory_dtls a, pro_ex_factory_mst b
								where a.status_active=1 and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name  and b.entry_form <> 85  and b.country_id=$cbo_country_name and b.id !=$txt_mst_id
								group by a.color_size_break_down_id");
			foreach($sql_exfac as $row_exfac)
			{
				$ex_fac_data[$row_exfac[csf("color_size_break_down_id")]]=$row_exfac[csf("ex_production_qnty")];

			}


			$dtlsrDelete = execute_query("delete from pro_ex_factory_dtls where mst_id=$txt_mst_id",1);
			$field_array="id, mst_id,color_size_break_down_id,production_qnty";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue);
				$dtls_id=return_next_id("id", "pro_ex_factory_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					if($is_control==1 && $user_level!=2)
					{
						$garments_delivery_data=0;
						if($colorSizeNumberIDArr[1]>0)
						{
							$garments_delivery_data=$color_size_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]-$ex_fac_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]];
							if(($colorSizeNumberIDArr[1]*1)>($garments_delivery_data*1))
							{
								echo "35**Ex-factory Qnty Quantity Not Over Finish Qnty";
								disconnect($con);
								die;
							}
						}
					}


					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."')";
					$dtls_id=$dtls_id+1;
					$j++;
				}
			}

			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				$rowEx = explode("***",$colorIDvalue);
				$dtls_id=return_next_id("id", "pro_ex_factory_dtls", 1);
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
						$garments_delivery_data=0;
						if($colorSizeValue>0)
						{
							$garments_delivery_data=$color_size_data[$colSizeID_arr[$index]]-$ex_fac_data[$colSizeID_arr[$index]];
							if(($colorSizeValue*1)>($garments_delivery_data*1))
							{
								echo "35**Ex-factory Qnty Quantity Not Over Finish Qnty";
								disconnect($con);
								die;
							}
						}
					}


					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",'".$colSizeID_arr[$index]."','".$colorSizeValue."')";
					$dtls_id=$dtls_id+1;
					$j++;
				}
			}

			//echo $data_array."--";
			if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
			{
				$dtlsrID=sql_insert("pro_ex_factory_dtls",$field_array,$data_array,1);
			}
			//$dtlsrID=sql_insert("pro_ex_factory_dtls",$field_array,$data_array,1);
		}//end cond

		$invoiceID=true;
		if($invoice_id!="")
		{
 			$invoiceID=sql_update("com_export_invoice_ship_mst","ex_factory_date",$txt_ex_factory_date,"id",$invoice_id,1);
		}
	//	echo "10**".$rID."&&".$dtlsrID."&&".$sts_country."&&".$sts_ex."&&".$dtlsrDelete;die;
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID && $sts_country && $sts_ex && $dtlsrDelete)
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
				if($rID && $sts_country && $sts_ex)
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
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID && $sts_country && $sts_ex && $dtlsrDelete)
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
				if($rID && $sts_country && $sts_ex)
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
		disconnect($con);
		die;
	}

	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$country_order_qty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0");

		$country_exfactory_qty=return_field_value("sum(ex_factory_qnty)","pro_ex_factory_mst","po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and entry_form <> 85  and status_active=1 and is_deleted=0 and id<>$txt_mst_id");

		if($country_exfactory_qty>=$country_order_qty) $country_order_status=3;
		else if($country_exfactory_qty>0 && $country_exfactory_qty < $country_order_qty) $country_order_status=2;
		else $country_order_status=1;

		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name",1);

		$country_wise_status=return_field_value("count(id)","wo_po_color_size_breakdown","po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active=1 and is_deleted=0");
		if($country_wise_status>0 && $country_exfactory_qty>0) $order_status=2;
		else if($country_wise_status>0 && $country_exfactory_qty<=0) $order_status=1;
		else $order_status=3;

 		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id",1);
		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and entry_form <> 85  and country_id=$cbo_country_name",1);

		$rID = sql_delete("pro_ex_factory_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_ex_factory_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);

 		if($db_type==0)
		{
			if($rID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst)
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
			if($rID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst)
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


if($action=="ex_factory_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from   lib_buyer", "id", "buyer_name"  );
	$order_library=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	$sewing_library=return_library_array( "select id, line_name from  lib_sewing_line", "id", "line_name"  );

	$sql="select id, po_break_down_id,  item_number_id, challan_no,  transport_com, ex_factory_date, invoice_no, lc_sc_no, shiping_status, ex_factory_qnty, remarks from pro_ex_factory_mst where status_active=1 and is_deleted=0 and entry_form <> 85  and id='$data[1]' ";
	//echo $sql;
	$dataArray=sql_select($sql);

?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">
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
            <td colspan="6" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?> Report</strong></td>
        </tr>
        <tr>
        	<td width="125"><strong>Order No:</strong></td> <td width="175px"><? echo $order_library[$dataArray[0][csf("po_break_down_id")]]; ?></td>
			<?
				foreach($dataArray as $row)
				{
					$job_no=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"job_no");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
					$buyer_val=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"buyer_name");
					$shipment_date=return_field_value("shipment_date"," wo_po_break_down","id=".$row[csf("po_break_down_id")],"shipment_date");

					$order_qnty=return_field_value("po_quantity"," wo_po_break_down","id=".$row[csf("po_break_down_id")],"po_quantity");

					$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where id='".$row[csf('lc_sc_no')]."'");
					foreach($sqlEx as $val)
					{
						if($val["is_lc"]==1) //  lc
							$lc_sc = return_field_value("export_lc_no","com_export_lc","id=".$val[csf('lc_sc_id')]."");
						else
							$lc_sc = return_field_value("contract_no","com_sales_contract","id=".$val[csf('lc_sc_id')]."");

						$invoiceNo = $val[csf('invoice_no')];
					}
				}

            ?>
            <td width="125"><strong>Buyer:</strong></td><td width="175px"><? echo $buyer_library[$buyer_val]; ?></td>
            <td width="125"><strong>Job No :</strong></td><td width="175px"><? echo $job_no; ?></td>
        </tr>
        <tr>
            <td><strong>Style:</strong></td><td width="175px"><? echo $style_val; ?></td>
            <td><strong>Shipment Date:</strong></td><td width="175px"><? echo change_date_format($shipment_date); ?></td>
            <td><strong>Item Name:</strong></td><td width="175px"><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        </tr>
        <tr>
            <td ><strong>Order Qnty:</strong></td> <td width="175px"><? echo $order_qnty; ?></td>
            <td ><strong>Ex-Factory Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('ex_factory_date')]); ?></td>
            <td><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Invoice No :</strong></td><td width="175px"><? echo $invoiceNo; ?></td>
            <td><strong>LC No:</strong></td><td width="175px"><? echo $lc_sc; ?></td>
            <td><strong>Trans. Com.:</strong></td><td width="175px"><? echo $dataArray[0][csf('transport_com')]; ?></td>
        </tr>
        <tr>
            <td><strong>Shiping Status :</strong></td><td width="175px"><? echo $shipment_status[$dataArray[0][csf('shiping_status')]]; ?></td>
            <td><strong>Remarks:</strong></td><td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table><br>
        <?
			$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_ex_factory_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_ex_factory_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
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
            <th width="80" align="center">Total Issue Qnty.</th>
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
            echo signature_table(32, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}
?>
