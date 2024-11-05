<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create print Booking
Functionality	 :
JS Functions	 :
Created by		 : zakaria joy
Creation date 	 :
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

if($action=="test_item_popup")
{
	echo load_html_head_contents("Lab Test Work Order", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function fn_close()
		{
			var tot_row=$('#tbl_list_search tr').length;
			var all_party_id=""; var all_party_rate=""; var txt_wo_qty=""; var txt_wo_amt=0;
			for(var i=1; i<=tot_row; i++)
			{
				var wo_qnty=$('#woQty_'+i).val()*1;
				var wo_party_id=$('#txt_individual_id'+i).val()*1;
				var wo_party_rate=$('#txt_net_rate_update'+i).val()*1;
				var wo_amount=$('#woAmount_'+i).val()*1;
				if(wo_qnty>0)
				{
					if(all_party_id=="") all_party_id=wo_party_id; else all_party_id+=","+wo_party_id;
					if(all_party_rate=="") all_party_rate=wo_party_rate; else all_party_rate+=","+wo_party_rate;
					if(txt_wo_qty=="") txt_wo_qty=wo_party_id+"_"+wo_qnty+"_"+wo_amount; else txt_wo_qty+=","+wo_party_id+"_"+wo_qnty+"_"+wo_amount;
					txt_wo_amt+=wo_amount;
				}
			}

			$('#all_party_id').val(all_party_id);
			$('#all_party_rate').val(all_party_rate);
			$('#txt_wo_qty').val(txt_wo_qty);
			$('#txt_wo_amt').val(txt_wo_amt);
			parent.emailwindow.hide();
		}

		function cal_amount(strCon)
		{
			var rate=  $('#txt_net_rate_update'+strCon).val()*1;
			var Qnty=  $("#woQty_"+strCon).val()*1;
			var wo_amt=rate*Qnty;
			$('#woAmount_'+strCon).val(number_format(wo_amt,2,'.' , ""));
		}



    </script>
</head>
<body>
<div align="center">
	<fieldset style="width:1150px;margin-left:10px">
        <input type='hidden' id='all_party_id' value="<? echo $txt_party_type_id; ?>" />
        <input type='hidden' id='all_party_rate' value="<? echo $txt_party_type_name; ?>" />
        <input type='hidden' id='txt_wo_qty' value="<? echo $save_qty_break_data; ?>" />
        <input type='hidden' id='txt_wo_amt' value="<? echo $txt_amount; ?>" />
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="130">Test Category</th>
                    <th width="80">Test For</th>
                    <th width="120">Test Item</th>
                    <th width="70">Rate</th>
                    <th width="60">Upcharge %</th>
                    <th width="70">Upcharge Amount</th>
                    <th width="100">Net Rate</th>
                    <th width="70">Currency</th>
                    <th width="85">Net Rate <? echo $currency[$cbo_currency] ;?></th>
                    <th width="85">WO Rate</th>
                    <th width="85">WO Qty</th>
                    <th>WO Amount</th>
                </thead>
            </table>
            <div style="width:1150px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" id="tbl_list_search" >
                <?
					$prev_txt_party_type_id=explode(",",$txt_party_type_id);
					$prev_party_id_arr=array();
					foreach($prev_txt_party_type_id as $p_id)
					{
						$prev_party_id_arr[$p_id]=$p_id;
					}
					$variable_setting=return_field_value("lab_test_rate_update","variable_order_tracking","company_name=$cbo_company_name and variable_list=39");

					if($variable_setting==1) {$dissable_cond="";} else  { $dissable_cond="disabled";}
				//echo $variable_setting;die;
					if($db_type==2) { $txt_workorder_date=change_date_format($txt_workorder_date,'yyyy-mm-dd',"-",1);}
					else { $txt_workorder_date=change_date_format($txt_workorder_date,'yyyy-mm-dd');}
					$current_currency=set_conversion_rate($cbo_currency,$txt_workorder_date);
                    $i=1; $party_row_id='';
					//echo $txt_party_type_id;die;
					$hidden_party_id=explode(",",$txt_party_type_id);

					$hidden_party_value=explode(",",$txt_party_type_name);
					$currency_id="";
					$qt_break_arr=explode(",",$save_qty_break_data);
					$prev_qty_breakdown=array();
					foreach ($qt_break_arr as $value)
					{
						$value_ref=explode("_",$value);
						$prev_qty_breakdown[$value_ref[0]]=$value_ref[1];
					}
					$sql=sql_select("SELECT id,test_category,test_for,test_item,rate,upcharge_parcengate,upcharge_amount,net_rate,
					currency_id,testing_company
 					FROM lib_lab_test_rate_chart WHERE status_active =1 AND is_deleted =0 and testing_company=$cbo_supplier and test_for=$cbo_test_for");
                    foreach($sql as $name)
                    {
						$currency_id=$name[csf('currency_id')];
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$converted_currency=set_conversion_rate($name[csf('currency_id')],$txt_workorder_date);
						$actual_currency=$converted_currency/$current_currency;
						$actual_net_rate=$actual_currency*$name[csf('net_rate')];

						$key='';
						if(in_array($name[csf('id')],$hidden_party_id))
						{
							if($party_row_id=="") $party_row_id=$i; else $party_row_id.=",".$i;
							$key = array_search($name[csf('id')], $hidden_party_id);
							if(trim($hidden_party_value[$key])!=="")
							{
							$update_net_rate=$hidden_party_value[$key];
							}
							else
							{
							$update_net_rate=$actual_net_rate;
							}
						}
						else
						{
							$update_net_rate=$actual_net_rate;
						}

						if($prev_qty_breakdown[$name[csf('id')]]>0)
						{
							$wo_qty=$prev_qty_breakdown[$name[csf('id')]];
						}
						else
						{
							if($prev_party_id_arr[$name[csf('id')]]!="")
							{
								$wo_qty='1';
							}
							else
							{
								$wo_qty='';
							}

						}

						$TotalAmount=$wo_qty*$update_net_rate;

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" >
							<td width="40" align="center" title="<? echo $name[csf('id')];?>"><?php echo "$i"; ?>
							<input type="hidden" name="txt_individual_id<?php echo $i ?>" id="txt_individual_id<?php echo $i ?>" value="<? echo $name[csf('id')]; ?>"/>
							<input type="hidden" name="txt_individual<?php echo $i ?>" id="txt_individual<?php echo $i ?>" value="<? echo $actual_net_rate; ?>"/>
							</td>
							<td width="130" align="center"><p><? echo $testing_category[$name[csf('test_category')]]; ?></p></td>
							<td width="80" align="center"><p><? echo $test_for[$name[csf('test_for')]]; ?></p></td>
							<td width="120" align="center"><p><? echo $name[csf('test_item')]; ?></p></td>
							<td width="70" align="right"><p><? echo number_format($name[csf('rate')],2); ?></p></td>

							<td width="60" align="right"><p><? echo number_format($name[csf('upcharge_parcengate')],2); ?></p></td>
							<td width="70" align="right"><p><? echo number_format($name[csf('upcharge_amount')],2); ?></p></td>
							<td width="100" align="right"><p><? echo number_format($name[csf('net_rate')],2); ?></p></td>
							<td width="70" align="center"><p><? echo $currency[$name[csf('currency_id')]]; ?></p></td>
                            <td width="85" align="right"><p><? echo number_format($actual_net_rate,2); ?></p></td>
                            <td width="85" id="last_<?php echo $i ?>" align="center"><input type="text" id="txt_net_rate_update<?php echo $i ?>" name="txt_net_rate_update<?php echo $i ?>" value="<? echo number_format($update_net_rate,2,'.',''); ?>"  style="width:63px" class="text_boxes_numeric"  onkeyup="update_value_calculation(<?php echo $i ?>,this.value,<?php echo $name[csf('id')] ?>)" <? echo $dissable_cond; ?>/>
                            </td>

                            <td width="85" align="right">
                            	<p><input type="text" class="text_boxes_numeric" style="width:70px" value="<? echo $wo_qty; ?>"  onkeyup="cal_amount(<? echo $i;?>)"; id="woQty_<? echo $i;?>" >
                            	</p>
                            </td>

                            <td align="right">
                            	<p>
                            		<input type="text" class="text_boxes_numeric" style="width:80px" name="woAmount" id="woAmount_<? echo $i;?>" value="<? $wo_amt=$wo_qty*$update_net_rate; echo number_format($wo_amt,2,".",""); ?>" readonly>
                            	</p>
                            </td>
						</tr>
						<?
						$i++;
                    }
                ?>
                </table>
            </div>
             <table width="950" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%">

                            <div style="width:100%; float:left" align="center">
                              <input type="button" name="close" onClick="fn_close();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	//set_all_color(0);
	setFilterGrid("tbl_list_search",-1);
</script>
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
                            echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --",$cbo_company_name,"load_drop_down( 'labtest_work_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1 );
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
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'labtest_work_order_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:90px;" /></td>
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
	$job="";
	$sql=sql_select("select job_no from wo_labtest_mst a,  wo_labtest_dtls b  where a.id=b.mst_id and a.labtest_no='$txt_workorder_no'");
	foreach($sql as $row){
		$job=$row[csf('job_no')];
	}
	if($job){
		$disabled="disabled";
	}else{
		$disabled="";
	}

?>
	<script>
			function set_checkvalue()
			{
				if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
				else document.getElementById('chk_job_wo_po').value=0;
			}

			function js_set_value( job_no_po_id )
			{
				var jobPo=job_no_po_id.split("_");
				document.getElementById('selected_job').value=jobPo[0];
				document.getElementById('selected_po').value=jobPo[1];
				document.getElementById('selected_po_num').value=jobPo[2];
				parent.emailwindow.hide();
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
                         <input type="hidden" id="selected_po" name="selected_po">
                         <input type="hidden" id="selected_po_num" name="selected_po_num">
                         <input type="hidden" id="txt_workorder_no" name="txt_workorder_no" value="<? echo $txt_workorder_no ?>">

						<?
                            echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --",$cbo_company_name,"load_drop_down( 'labtest_work_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1 );
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
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_workorder_no').value, 'create_po_id_search_list_view', 'search_div', 'labtest_work_order_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:90px;" /></td>
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


	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr);


	$sql= "select to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,
	b.id,b.po_number,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b, wo_pre_cost_mst c,
	wo_pre_cost_dtls d   where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=d.job_no and a.status_active=1 and a.is_deleted=0
	and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.approved in (1,3) and b.shiping_status not in(3) $shipment_date
	$company $buyer  $job_cond $style_cond $order_cond
	group by a.insert_date,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,
	b.id,b.po_number,b.po_quantity,b.shipment_date,a.job_no,c.id order by a.job_no DESC";

	//echo $sql;
	echo  create_list_view("list_view", "Year,Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date, Precost id",
	"50,50,120,100,100,80,90,80,70,90","880","320",0, $sql , "js_set_value", "job_no,id,po_number", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr ,
	"year,job_no_prefix_num,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,pre_id", "",'','0,0,0,0,0,1,0,1,3,1') ;
}

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}
		$id=return_next_id("id", "wo_labtest_mst", 1);
		$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'LTW', date("Y"), 5, "select labtest_prefix, labtest_prefix_num from   wo_labtest_mst where company_id=$cbo_company_name and entry_form=274 and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id DESC ", "labtest_prefix", "labtest_prefix_num",""));

		$field_array="id, labtest_prefix, labtest_prefix_num, labtest_no, entry_form, company_id, supplier_id, wo_date, delivery_date, currency, ecchange_rate, pay_mode, attention, address, ready_to_approved, vat_percent, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',274,".$cbo_company_name.",".$cbo_supplier.",".$txt_workorder_date.",".$txt_delivery_date.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$txt_attention.",".$txt_address.",".$cbo_ready_to_approved.",".$txt_vat_per.",'".$user_id."','".$pc_date_time."',1,0)";

		$return_no=str_replace("'",'',$new_sys_number[0]);
		$rID=sql_insert("wo_labtest_mst",$field_array,$data_array,0);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}
		else if($db_type==1 || $db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
		$con = connect();
		$txt_booking_no=$txt_workorder_no;
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}
		}
		if($db_type==0)	{ mysql_query("BEGIN"); }
		if( str_replace("'","",$update_id) == "")
		{
			echo "15"; disconnect($con);exit();
		}
		if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$field_array="supplier_id*wo_date*delivery_date*currency*ecchange_rate*pay_mode*attention*address*ready_to_approved*vat_percent*updated_by*update_date";
		$data_array="".$cbo_supplier."*".$txt_workorder_date."*".$txt_delivery_date."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$txt_attention."*".$txt_address."*".$cbo_ready_to_approved."*".$txt_vat_per."*'".$user_id."'*'".$pc_date_time."'";
		$rID=sql_update("wo_labtest_mst",$field_array,$data_array,"id",$update_id,1);
		$return_no=str_replace("'",'',$txt_workorder_no);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
		}
		if($db_type==2)
		{
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
		}
		disconnect($con);
		die;
 	}

	else if ($operation==2)
	{
		$con = connect();
		$txt_booking_no=$txt_workorder_no;
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}
		}
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$update_id=str_replace("'","",$update_id);
		// master table delete here---------------------------------------
		if($update_id=="" || $update_id==0){ echo "15**0"; die;}
		$dtlsrID = sql_update("wo_labtest_mst",'status_active*is_deleted','0*1',"id",$update_id,1);
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

if($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$poCond="";
	if(str_replace("'","",$txt_order_id )){
		$poCond="and a.id=$txt_order_id";
	}
		$condition= new condition();
		if(str_replace("'","",$txt_job_no) !=''){
			$condition->job_no("=$txt_job_no");
		}
		$condition->init();
		$other= new other($condition);
		//echo $other->getQuery();die;
		$other_costing_arr=$other->getAmountArray_by_job();
		$job_no=str_replace("'","",$txt_job_no);
		$exchange_rate=str_replace("'","",$txt_exchange_rate);
		$wo_value_with_vat2=str_replace("'","",$txt_wo_value_with_vat);
		
		$wo_value_with_vat=$wo_value_with_vat2;
		
		
		$exchange_rate_arr=sql_select( "select  job_no,exchange_rate from wo_pre_cost_mst where job_no=$txt_job_no");
		//echo "select  job_no,exchange_rate from wo_pre_cost_mst where job_no=$txt_job_no";
		foreach($exchange_rate_arr as $row)
		{
			$job_wise_arr[$row[csf('job_no')]]['ex_rate']=$row[csf('exchange_rate')];
		}
		$lab_currency=sql_select("select a.currency from wo_labtest_mst a where  a.id=$update_id  and a.status_active=1");
		foreach($lab_currency as $row){
			
			$currency_id=$row[csf('currency')];
		} 

		$tot_prev_wo_val=0;
		$lab_prev=sql_select("select a.labtest_no,b.wo_with_vat_value,b.job_no,a.currency from wo_labtest_mst a, wo_labtest_dtls b where  a.id=b.mst_id and b.job_no=$txt_job_no and a.status_active=1 and b.status_active=1");
		foreach($lab_prev as $row){
			$currency_id=$row[csf('currency')];
			if($currency_ids==2){
				$prev_wo_arr[$row[csf('job_no')]][$row[csf('currency')]]['prev_value']+=$row[csf('wo_with_vat_value')];
			}else{
				$prev_wo_arr[$row[csf('job_no')]][$row[csf('currency')]]['prev_value']+=$row[csf('wo_with_vat_value')]*$job_wise_arr[$row[csf('job_no')]]['ex_rate'];
			}
		}
		if ($operation==0 || $operation==1)
		{	
			$job_no=str_replace("'","",$txt_job_no);
			$req_lab_test_cost=$other_costing_arr[$job_no]['lab_test'];

				if($currency_id==1) //Tk
				{
					$prev_net_amount=$prev_wo_arr[$job_no][$currency_id]['prev_value'];;
					$ex_rate=$job_wise_arr[$job_no]['ex_rate'];	
					$tot_lab_cost=$req_lab_test_cost*$ex_rate;
					$txt_wo_value_with_vat_chk=$wo_value_with_vat+$prev_net_amount;
				}
				else{
					$prev_net_amount=$prev_wo_arr[$job_no][$currency_id]['prev_value'];;
					$tot_lab_cost=$req_lab_test_cost;
					$txt_wo_value_with_vat_chk=$wo_value_with_vat+$prev_net_amount;
				}
	
		}
				 
				if($txt_wo_value_with_vat_chk>$tot_lab_cost)
				{
					echo "14**Lab Test Wo Amount($wo_value_with_vat) is greater than Pre Cost($tot_lab_cost) not allowed.**Previ=".$prev_net_amount;
					disconnect($con);
					die;
				}
	
				
			 //Loop end
			
	
		
/* 		if ($operation==0)
		{
			if($currency_id==1) //TK
			{
				//echo 'pre'.$tot_prev_wo_val.'wo'.$wo_value_with_vat.'ex-rate'.$exchange_rate; die;
				$current_wo_val=$tot_prev_wo_val+$wo_value_with_vat/$exchange_rate;
				$txt_wo_value_with_vat = $wo_value_with_vat/$exchange_rate;
				$req_lab_test_cost=$req_lab_test_cost*$exchange_rate_arr[0][csf('exchange_rate')];
			}
			else
			{
				$current_wo_val=$tot_prev_wo_val+$wo_value_with_vat;
			}
			
		}
		else if ($operation==1){
			if($currency_id==1) //TK
			{
				$current_wo_val=(($tot_prev_wo_val+$wo_value_with_vat)-$tot_prev_wo_val)/$exchange_rate;
				$txt_wo_value_with_vat = $wo_value_with_vat/$exchange_rate;
				$req_lab_test_cost=$req_lab_test_cost*$exchange_rate_arr[0][csf('exchange_rate')];
			}
			else
			{
				$current_wo_val=($tot_prev_wo_val+$wo_value_with_vat)-$tot_prev_wo_val;
			}
		}
	//	echo "budgetOver**".$current_wo_val.'='.$req_lab_test_cost.'='.$tot_prev_wo_val."=".$txt_wo_value_with_vat;die;
		if ($operation==0 || $operation==1)
		{
			$current_wo_val=number_format($current_wo_val,4, '.', '');
			$req_lab_test_cost=number_format($req_lab_test_cost,4, '.', '');
			if(($current_wo_val>$req_lab_test_cost))
			{
				echo "budgetOver**".str_replace("'","",$txt_job_no)."**".implode(",",$prev_wo_arr)."**".$req_lab_test_cost."**".$current_wo_val;
				disconnect($con);die;
			}
		} */
		
		
		
		
		//=========Validation End===================//
		
	$sql_order=sql_select("select a.job_no_mst,a.id,a.po_quantity,b.job_quantity  from wo_po_break_down a,wo_po_details_master b
	where job_no_mst=$txt_job_no $poCond  and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.id,a.job_no_mst,a.po_number,a.po_quantity,b.job_quantity");

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";  disconnect($con);die;}
		 $id_dtls=return_next_id( "id", "wo_labtest_dtls", 1 ) ;
		 $id_order_dtls=return_next_id( "id", "wo_labtest_order_dtls", 1 ) ;

		 //$color_id=return_id( $txt_color, $color_library, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_color)!="")
		{ 
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name","79");
				$new_array_color[$color_id]=str_replace("'","",$txt_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color); 
		}
		else $color_id=0;
		 $field_array="id,mst_id,job_no,po_id,entry_form,test_for,test_item_id,test_item_value,color,amount,discount,labtest_charge,wo_value,vat_amount,wo_with_vat_value,remarks, inserted_by,insert_date,status_active,is_deleted,qty_breakdown";
		 $field_array1="id,mst_id,job_no,dtls_id,order_id,wo_value,order_qty,inserted_by,insert_date,status_active,is_deleted";
		 $data_array="(".$id_dtls.",".$update_id.",".$txt_job_no.",".$txt_order_id.",274,".$cbo_test_for.",".$txt_party_type_id.",".$txt_party_type_name.",'".$color_id."',".$txt_amount.",".$txt_discount.",".$txt_delivery_charge.",".$txt_wo_value.",".$txt_vat_amount.",".$txt_wo_value_with_vat.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$save_qty_break_data.")";
		foreach($sql_order as $name)
		{
			$order_percentage=($name[csf('po_quantity')]/$name[csf('job_quantity')])*100;
			$txt_wo_value=str_replace("'","",$txt_wo_value);
			//$workorder_value=($txt_wo_value*$order_percentage)/100;
			$workorder_value=number_format(($txt_wo_value*$order_percentage)/100,4,".","");
			$order_id=$name[csf('id')];
			$order_qty=$name[csf('po_quantity')];

			if ($data_array1!=1) $data_array1 .=",";
			$data_array1 .="(".$id_order_dtls.",".$update_id.",".$txt_job_no.",".$id_dtls.",".$order_id.",".$workorder_value.",".$order_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id_order_dtls=$id_order_dtls+1;
		 }
		$rID=sql_insert("wo_labtest_order_dtls",$field_array1,$data_array1,0);
		$rID1=sql_insert("wo_labtest_dtls",$field_array,$data_array,0);
		check_table_status( $_SESSION['menu_id'],0);

		//echo "10**".$rID.'**'.$rID1.'**';
		 //echo "insert into wo_labtest_dtls (".$field_array.") values".$data_array;
		//die;
		if($db_type==0)
		{
			if($rID & $rID1 )
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$id_dtls)."**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$id_dtls)."**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID & $rID1){
				oci_commit($con);
				echo "0**".str_replace("'","",$id_dtls)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$id_dtls)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==1)
	{
		$con = connect();
		$txt_booking_no=$txt_workorder_no;

		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}
		}
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	    if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; disconnect($con); die;}
	  	//$color_id=return_id( $txt_color, $color_library, "lib_color", "id,color_name");
	  	if(str_replace("'","",$txt_color)!="")
		{ 
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name","79");
				$new_array_color[$color_id]=str_replace("'","",$txt_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color); 
		}
		else $color_id=0;
		$field_array="job_no*po_id*test_for*test_item_id*test_item_value*color*amount*discount*labtest_charge*wo_value*vat_amount*wo_with_vat_value*remarks*updated_by*update_date*qty_breakdown";
		$data_array="".$txt_job_no."*".$txt_order_id."*".$cbo_test_for."*".$txt_party_type_id."*".$txt_party_type_name."*'".$color_id."'*".$txt_amount."*".$txt_discount."*".$txt_delivery_charge."*".$txt_wo_value."*".$txt_vat_amount."*".str_replace(",","",$txt_wo_value_with_vat)."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'*".$save_qty_break_data."";//,".$txt_vat_amount.",".$txt_wo_value_with_vat."
		//print_r($data_array);
		$field_array1="id,mst_id,job_no,dtls_id,order_id,wo_value,order_qty,inserted_by,insert_date,status_active,is_deleted,qty_breakdown";
		$add_comma=0;
		$id_order_dtls=return_next_id( "id", "wo_labtest_order_dtls", 1 ) ;
		$sql_query=execute_query("delete from wo_labtest_order_dtls where mst_id=$update_id and  dtls_id=$update_dtls_id");
		foreach($sql_order as $name)
		{
			$order_percentage=($name[csf('po_quantity')]/$name[csf('job_quantity')])*100;
			$txt_wo_value=str_replace("'","",$txt_wo_value);
			//$workorder_value=($txt_wo_value*$order_percentage)/100;
			$workorder_value=number_format(($txt_wo_value*$order_percentage)/100,4,".","");
			$order_id=$name[csf('id')];
			$order_qty=$name[csf('po_quantity')];

			if ($data_array1) $data_array1 .=",";
			$data_array1 .="(".$id_order_dtls.",".$update_id.",".$txt_job_no.",".$update_dtls_id.",".$order_id.",".$workorder_value.",".$order_qty.", ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$save_qty_break_data.")";
			$id_order_dtls=$id_order_dtls+1;
			$i++;
		}
		$rID1=true;

		$update_dtls_id=str_replace("'", "", $update_dtls_id);
		$update_dtls_id="'".trim($update_dtls_id)."'";

	   $rID=sql_update("wo_labtest_dtls",$field_array,$data_array,"id",$update_dtls_id,1);
		if($data_array1 !="")
		{
			$rID1=sql_insert("wo_labtest_order_dtls",$field_array1,$data_array1,0);
		}
	//	 echo "insert into wo_labtest_order_dtls (".$field_array1.") Values ".$data_array1."";die;
	//echo "10**".$rID.'='.$rID1;die;
       check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);;
			}
		}

		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==2)
	{
		$con = connect();
		$txt_booking_no=$txt_workorder_no;

		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}
		}
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID=execute_query( "update wo_labtest_dtls set status_active=0,is_deleted =1 where  id =$update_dtls_id",0);
		$rID1=execute_query( "update wo_labtest_order_dtls set status_active=0,is_deleted =1 where  dtls_id=$update_dtls_id",0);

		if($db_type==0)
		{
			if($rID && $rID1){
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
				oci_commit($con);
				echo "2**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="load_dtls_data_view")
{
	$arr=array (1=>$test_for,3=>$color_library);

	$sql= "select id,mst_id,job_no,entry_form,test_for,test_item_id,color,amount,discount,labtest_charge,wo_with_vat_value,remarks from wo_labtest_dtls where mst_id=$data and is_deleted = 0 and status_active=1 ";
	echo  create_list_view("list_view", "Job No,Test For,Remarks,Color,Amount,Quick Delv Charge,Discount,WO Value", "100,110,180,120,95,95,95,95","1020","320",0, $sql , "get_php_form_data", "id", "'load_php_dtls_data_to_form'", 1, "0,test_for,0,color,0,0,0,0", $arr , "job_no,test_for,remarks,color,amount,labtest_charge,discount,wo_with_vat_value", "requires/labtest_work_order_controller",'','0,0,0,0,5,5,5,5','','');
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_wo_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_wo_search_list_view', 'search_div', 'labtest_work_order_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.supplier_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0)
	{
		$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		$year_id=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."'
		and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$year_id=" to_char(a.insert_date,'YYYY') as year";
	}

	if($data[6]==4 || $data[6]==0)
	{
	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.labtest_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
	}
    if($data[6]==1)
	{
	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.labtest_prefix_num ='$data[5]' "; else $booking_cond="";
	}
   	if($data[6]==2)
	{
	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.labtest_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
	}
	if($data[6]==3)
	{
	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.labtest_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
	}

	$approved=array(1=>"Yes",2=>"No");
	$suplier=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and
	b.party_type=26 order by a.supplier_name",'id','supplier_name');
		//echo $data[7];
	$arr=array (2=>$comp,4=>$suplier,5=>$currency,10=>$pay_mode,12=>$approved);

	if($data[7]==0)
	{

		$sql= "select a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date, $year_id,listagg(cast(b.job_no as varchar2(4000)),',') within group (order by b.id ) as job_no,listagg(cast(c.po_number as varchar2(4000)),',') within group (order by b.id ) as po_number,sum(b.wo_value) as wo_value from wo_labtest_mst a,wo_labtest_dtls b,wo_po_break_down c where a.id=b.mst_id and c.id=b.po_id and $company $buyer $booking_date  $booking_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form = 274 group by a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date order by a.id";
	}
	else
	{
		$sql= "select a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date, $year_id,null as po_number,0 as wo_value from wo_labtest_mst a where a.id not in( select a.id from wo_labtest_mst a,wo_labtest_dtls b,wo_po_break_down c where a.id=b.mst_id and c.id=b.po_id and $company $buyer $booking_date  $booking_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form = 274 group by a.id ) and a.status_active=1  and $company $buyer $booking_date  $booking_cond and a.is_deleted=0 and a.entry_form = 274 group by a.id, a.labtest_prefix, a.labtest_prefix_num, a.labtest_no, a.entry_form, a.company_id, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.attention, a.address, a.ready_to_approved, a.inserted_by, a.insert_date order by a.id";
	}
	echo  create_list_view("list_view", "WO No,Year,Job No,PO No,Test Companys,Currency,Exchange Rate,Wo Value,Wo Date,Delivery Date,Pay Mode,Attention,Ready To Approved", "60,60,130,100,150,70,60,70,70,70,100,100,70","1170","300",0, $sql , "js_set_value", "id,labtest_no", "", 1, "0,0,0,0,supplier_id,currency,0,0,0,0,pay_mode,0,ready_to_approved", $arr , "labtest_prefix_num,year,job_no,po_number,supplier_id,currency,ecchange_rate,wo_value,wo_date,delivery_date,pay_mode,attention,ready_to_approved", '','','1,0,0,0,0,1,1,2,3,3,0,0,0','','');
}

if ($action=="load_php_mst_data")
{

	 $data=explode("_",$data);
	 $sql= "select id, labtest_prefix, labtest_prefix_num, labtest_no, entry_form, company_id, supplier_id, wo_date, delivery_date, currency, ecchange_rate, pay_mode, attention, address, ready_to_approved, vat_percent, inserted_by, insert_date from wo_labtest_mst where labtest_no='$data[1]' and entry_form=274";
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_workorder_date').value = '".change_date_format($row[csf("wo_date")])."';\n";
		echo "document.getElementById('cbo_supplier').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("ecchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")])."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_vat_per').value = '".$row[csf("vat_percent")]."';\n";
		echo "document.getElementById('txt_address').value = '".$row[csf("address")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled',true);";
		echo "$('#cbo_supplier').attr('disabled',true);";
	 }
}


if($action=="load_php_dtls_data_to_form")
{

	 $sql= "select id,mst_id,job_no,po_id,entry_form,test_for,test_item_id,test_item_value,color,amount,discount,labtest_charge,wo_value,vat_amount,wo_with_vat_value,remarks,qty_breakdown from wo_labtest_dtls
	 where id=$data ";
	 //echo $sql;die;
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_test_for').value = '".$row[csf("test_for")]."';\n";
		echo "document.getElementById('txt_party_type_id').value = '".$row[csf("test_item_id")]."';\n";
		echo "document.getElementById('txt_party_type_name').value = '".$row[csf("test_item_value")]."';\n";
		echo "document.getElementById('txt_color').value = '".$color_library[$row[csf("color")]]."';\n";
		//echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n";
		echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n";
		echo "document.getElementById('txt_discount').value = '".$row[csf("discount")]."';\n";
		echo "document.getElementById('txt_delivery_charge').value = '".$row[csf("labtest_charge")]."';\n";
		echo "document.getElementById('txt_wo_value').value = '".$row[csf("wo_value")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_vat_amount').value = number_format('".$row[csf("vat_amount")]."',4);\n";
		echo "document.getElementById('txt_wo_value_with_vat').value = number_format('".$row[csf("wo_with_vat_value")]."',4);\n";
		echo "document.getElementById('txt_order_id').value = '".$row[csf("po_id")]."';\n";
		echo "document.getElementById('save_qty_break_data').value = '".$row[csf("qty_breakdown")]."';\n";


		echo "document.getElementById('update_dtls_id').value = '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_booking_dtls',2);\n";
		$sql_po=sql_select("select po_number from wo_po_break_down where id='".$row[csf('po_id')]."'");
		/*foreach($sql_po as $po_number){
		}*/
		echo "document.getElementById('txt_order_no').value = '".$sql_po[0][csf('po_number')]."';\n";
	 }
}

if($action=="show_trim_booking_report")
{
    extract($_REQUEST);

	echo load_html_head_contents("Lab Test Work Order", "../../", 1, 1,'','','');
	$data=explode('*',str_replace("'","",$data));

	$sql="select a.id, a.labtest_no, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.address, a.attention,a.ready_to_approved,a.vat_percent from wo_labtest_mst a where a.id='$data[1]' and a.company_id='$data[0]'"; // new
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );


	$lab_test_rate_library=return_library_array( "select id, test_item from lib_lab_test_rate_chart", "id", "test_item"  );

	$supplier_library=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id
	and b.party_type=26 order by a.supplier_name","id","supplier_name"  );
	$total_charge=0;
	$sql_dtls= "select id,mst_id,po_id,job_no,entry_form,test_for,test_item_id,test_item_value,color,amount,discount,labtest_charge,wo_value,vat_amount,remarks,test_item_id,qty_breakdown
	from wo_labtest_dtls
	where mst_id=$data[1] and is_deleted=0 and status_active=1";
	$sql_result= sql_select($sql_dtls);
	$amount_arr=array();
	$job_nos='';
	$poArr=array();
	foreach($sql_result as $inf)
	{
		$amount_arr[$inf[csf('job_no')]]['wo_value']=$inf[csf('wo_value')];
		$total_charge+=$inf[csf('labtest_charge')];
		if($job_nos=='') $job_nos=$inf[csf('job_no')];else $job_nos.=",".$inf[csf('job_no')];
		if($inf[csf('po_id')]){
			$poArr[$inf[csf('po_id')]]=$inf[csf('po_id')];
		}
	}
	$jobid=array_unique(explode(",",$job_nos));
	$jobs='';
	foreach($jobid as $jid)
	{
		if($jobs=='') $jobs="'$jid'";else $jobs.=","."'$jid'";
	}
	$poCond="";
	if(count($poArr)>0){
		$poCond="and b.id in (".implode(",",$poArr).")";
	}
	//echo $jobs;
	//echo $job_nos;
	$po_numberArr=array();
	$po_shipdateArr=array();
	$pos_sql="select b.id, b.po_number,b.shipment_date,a.buyer_name,a.style_ref_no as style_ref, a.job_no from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and a.job_no in($jobs) $poCond ";
	$sql_results=sql_select($pos_sql);
	foreach($sql_results as $row)
	{
		$buyer_library[$row[csf('job_no')]]=$row[csf('buyer_name')];
		$style_ref_no_library[$row[csf('job_no')]]=$row[csf('style_ref')];
		$po_no_arr[$row[csf('job_no')]].=$row[csf('po_number')].',';
		$po_numberArr[$row[csf('id')]]=$row[csf('po_number')];
		$po_shipdateArr[$row[csf('id')]]=$row[csf('shipment_date')];
	}

	$buyer_name='';
	foreach($jobid as $job)
	{
			if($buyer_name=='') $buyer_name=$buyer_name_arr[$buyer_library[$job]];else $buyer_name.=",".$buyer_name_arr[$buyer_library[$job]];
	}


$varcode_booking_no=$dataArray[0][csf('labtest_no')];


?>
<div style="width:1000px;" align="center">

	<table width="900" style="table-layout: fixed;">
		<tr>
			<td width="150" align="center" style="font-size: 14px"><strong><?
				$company_logo=sql_select( "select b.image_location from common_photo_library b where b.master_tble_id='$data[0]' and b.form_name='company_details'");
				?>
	            <img src="../../<?  echo $company_logo[0][csf('image_location')]; ?>"  width="100px"  height="70" ></strong>
        	</td>
			<td align="center" style="font-size: 14px; text-align:center;" valign="top">
            	<strong style="font-size: 30px"><? echo $company_library[$data[0]]; ?></strong><br>
            	<strong><?
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
                ?> </strong>
            </td>
			<td width="300" id="barcode_img_id" align="right" style="font-size: 14px"></td>
		</tr>
		<tr>
			<td></td><td align="center"><u style="font-size: 18px;font-weight: bold;">Lab Test Work Order</u></td><td></td>
		</tr>
	</table>
<div style="margin-top: 20px"></div>
	<table cellspacing="0" width="800" align="center" border="1" rules="all" class="rpt_table">
		<tr>
			<td width="80" ><strong>To : </strong></td>
			<td width="150"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]];?></td>
			<td width="110"><strong>Buyer:</strong></td>
			<td width="150"><? echo implode(",",array_unique(explode(",",$buyer_name))); ?></td>
			<td width="90"><strong>Wo No.:</strong></td>
			<td width="150"><? echo $dataArray[0][csf('labtest_no')];?></td>

		</tr>
		<tr>
			<td><strong>Address:</strong></td>
			<td><? echo $dataArray[0][csf('address')]; ?></td>

			<td><strong>Pay Mode:</strong></td>
			<td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
			<td><strong>Wo Date.:</strong></td>
			<td><? echo change_date_format($dataArray[0][csf('wo_date')]); ?></td>

		</tr>
		<tr>
			<td><strong>Attention:</strong></td>
			<td><? echo $dataArray[0][csf('attention')];?></td>
			<td><strong>Delivery Date:</strong></td>
			<td><? echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
			<td><strong>Rate For.:</strong></td>
			<td><?
			 if($total_charge>0) {$rate_for='Express';}  else  { $rate_for='Regular';}
			 echo $rate_for; ?></td>
		</tr>
		<tr>
			<td><strong>Currency:</strong></td>
			<td ><? echo $currency[$dataArray[0][csf('currency')]]; ?></td>
			<td><strong>Exchange Rate:</strong></td>
			<td ><? echo $dataArray[0][csf('ecchange_rate')]; ?></td>
			<td><strong> Vat %:</strong></td>
			<td><? echo $dataArray[0][csf('vat_percent')]; ?></td>
		</tr>

	</table>


        <br/> 

         <table  cellspacing="0" width="900"  border="1" rules="all" class=""  align="left" style="table-layout: fixed;">
         <thead bgcolor="#dddddd" >
             <tr>
             		<th width="30">SL</th>
                	<th width="100">Style/Job No</th>
                    <th width="100">Po No</th>
                    <th width="80">Ship Date</th>
                    <th width="70">Test For</th>
                    <th width="100">Remarks</th>
                    <th width="80">Color</th>
                    <th width="200">Test Item</th>
                    <th width="50">Qty</th>
                    <th width="50">Rate</th>
                    <th width="80">Amount</th>
                </tr>
        </thead>
        <tbody>

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

		if ($j%2==0)
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$test_item=explode(",",$row[csf('test_item_id')]);
			$test_item_value=explode(",",$row[csf('test_item_value')]);
			$test_item_qty_break=explode(",",$row[csf('qty_breakdown')]);

			$itemDataArr=array();
			foreach($test_item_qty_break as $item_data_string){
				list($item_id,$qty,$amount)=explode('_',$item_data_string);
				$itemDataArr[$item_id]=array(
					'qty'=>$qty,
					'amu'=>$amount
				);
			}

			//echo $row[csf('test_item_value')];
			$colum_span=count(explode(",",$row[csf('test_item_id')]));
			$colum_span=$colum_span+4;
			if(trim($all_job_no)!='') $all_job_no.=",'".$row[csf("job_no")]."'";
			else $all_job_no="'".$row[csf("job_no")]."'";
			$total_net_reate=0;

			//print_r($test_item_value);
			$index=0;
			foreach($test_item as $name)
			{

				if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$converted_currency=set_conversion_rate($name[csf('currency_id')],$txt_workorder_date);
				$actual_currency=$converted_currency/$current_currency;
				//$actual_net_rate=$actual_currency*$name[csf('net_rate')];

				if($row[csf("po_id")]==""){
					$po_no=rtrim($po_no_arr[$row[csf("job_no")]],',');
				}else{
					$po_no=$po_numberArr[$row[csf('po_id')]];
		            $shipment_date=$po_shipdateArr[$row[csf('po_id')]];
				}
				//$po_ids=explode(",",$po_no);
				//if(count($po_ids)>3)
				if(!in_array($i,$main_row))
				{
				$main_row[]=$i; //style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"
		?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center" style="font-size:15px" rowspan="<? echo $colum_span?>"><? echo $i; ?></td>
                    <td align="center" style="font-size:15px" rowspan="<? echo $colum_span?>"><? echo $style_ref_no_library[$row[csf("job_no")]].'<br/>'.$row[csf("job_no")]; ?></td>
                      <td  align="center" style="word-break:break-all;font-size:15px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" rowspan="<? echo $colum_span?>"><p><? echo $po_no; ?></p></td>
                      <td  align="center" style="word-break:break-all;font-size:15px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" rowspan="<? echo $colum_span?>"><p><? echo date("d-m-Y",strtotime($shipment_date)); ?></p></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $test_for[$row[csf("test_for")]]; ?></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $row[csf("remarks")]; ?></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $color_library[$row[csf("color")]]; ?></td>
                    <td align="left" style="word-break:break-all;font-size:15px"> <?  echo $lab_test_rate_library[$name];  ?> </td>

                   	<td align="right"><? echo $itemDataArr[$name]['qty'];?></td>
                    <td align="right"><? echo number_format($test_item_value[$index],2);?></td>
                    <td align="right" style="font-size:15px">
					<?
					  	$total_net_reate+=$itemDataArr[$name]['amu'];
						echo number_format($itemDataArr[$name]['amu'],4);
					?>
                    </td>
                </tr>
		   <?
				}
				else
				{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">

                    <td align="left" style="font-size:15px"><? echo $lab_test_rate_library[$name]; ?></td>

                   	<td align="right"><? echo $itemDataArr[$name]['qty'];?></td>
                    <td align="right"><? echo number_format($test_item_value[$index],2);?></td>
                    <td align="right" style="font-size:15px">
					<?
					  	$total_net_reate+=$itemDataArr[$name]['amu'];
						echo number_format($itemDataArr[$name]['amu'],4);
					?>
                    </td>
                </tr>

				<?
				}
				$index++;
			}
			?>
             <tr bgcolor="#E3E3E3">
                <td align="right" style="font-size:15px" colspan="3"><b>Gross Amount</b></td>
                <td align="right" style="font-size:15px"><b><? echo number_format($total_net_reate,4); ?></b></td>
			</tr>
            <tr bgcolor="<? //echo $bgcolor; ?>">
                <td align="right" style="font-size:15px" colspan="3" >Add Quick Delv Charge (USD)</td>
                <td align="right" style="font-size:15px"><? echo number_format($row[csf("labtest_charge")],4) ; ?></td>
			</tr>
              <tr bgcolor="<? //echo $bgcolor; ?>">
                <td align="right" style="font-size:15px" colspan="3" >Less Discount</td>
                <td align="right" style="font-size:15px"><? echo number_format($row[csf("discount")],4) ; ?></td>
			</tr>
            </tr>
              <tr bgcolor="#E3E3E3">
               <td align="right" style="font-size:15px" colspan="3" ><b>Total Value</b></td>
                <td align="right" style="font-size:15px"><b>
				<?
				 $toatal_wo_value=$row[csf("labtest_charge")]+$total_net_reate-$row[csf("discount")];
				 echo number_format(($toatal_wo_value),4) ;
				  ?></b></td>
			</tr>
            <!-- new develop -->
            <tr bgcolor="#E3E3E3">
               <td colspan="10" align="right" style="font-size:15px" ><b>Vat Amount</b></td>
                <td align="right" style="font-size:15px"><b>
					<?
						$toatal_vat_percent= $row[csf("vat_amount")];
						echo number_format(($toatal_vat_percent),4) ;
                    ?>
                 </b>
                </td>
			</tr>
            <tr bgcolor="#E3E3E3">
               <td colspan="10" align="right" style="font-size:15px"><b>Wo Value</b></td>
                <td align="right" style="font-size:15px"><b>
				<?
				 $wo_totall= $toatal_vat_percent+$toatal_wo_value;
				 $grand_wo_value+=$wo_totall;
				 echo number_format(($wo_totall),4) ;
				  ?></b></td>
			</tr>
            <!--  -->

            <?

        $i++;
        }


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
        	<tr bgcolor="#B7B7B7">
                <td align="right" style="font-size:15px" colspan="10"><b>Grand Total</b></td>
                <td align="right" style="font-size:15px"><b><? echo number_format(($grand_wo_value),4) ; ?></b></td>

			</tr>
			<tr>

				<td colspan="3"><strong>In Words:</strong></td>
				<td align="left" colspan="7" style="font-size:12px;"><b><? echo number_to_words(def_number_format($grand_wo_value,2,""),$mcurrency, $dcurrency);?></b></td>
			</tr>

			</tbody>

      </table>

    <table  cellspacing="0" width="800"  border="1" rules="all" class=""  align="left" style=" display:none">
        <thead bgcolor="#dddddd" >
        	<tr>
                 <th colspan="9" align="left">Comments</th>
           </tr>
           <tr>
                <th width="60">SL</th>
                <th width="120">Job No</th>
                <th width="130">Pre-Cost Value</th>
                <th width="140">WO Value</th>
                <th width="140">Balance</th>
                <th >Comments</th>

             </tr>
        </thead>
        <tbody>

<?
	$all_job_no=implode(",",array_unique(explode(",",$all_job_no)));

	$sql_pre_cost="select a.costing_per,b.lab_test,a.job_no from wo_pre_cost_mst a, wo_pre_cost_dtls b where a.job_no=b.job_no and a.job_no in ($all_job_no)";
	$result_precost= sql_select($sql_pre_cost);
	$job_arr_labtest=array();
	foreach($result_precost as $inf)
	{
		$costing_per=$inf[csf('costing_per')];
		if($costing_per==1)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/12;
		}
		else if($costing_per==2)
		{
			$costing_per_pcs=$inf[csf('lab_test')];
		}
		else if($costing_per==3)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/24;
		}
		else if($costing_per==4)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/36;
		}
		else if($costing_per==5)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/48;
		}
		$job_arr_labtest[$inf[csf('job_no')]]=$costing_per_pcs;
	}

	$poIdCond="";
	$poIdCond1="";
	if(count($poArr)>0){
		$poIdCond="and id in (".implode(",",$poArr).")";
		$poIdCond1="and order_id in (".implode(",",$poArr).")";
	}
	$sql_order_qty="select sum(po_quantity) as po_quantity,job_no_mst from  wo_po_break_down  where job_no_mst in ($all_job_no) and status_active=1  and is_deleted=0 $poIdCond group by job_no_mst ";

	$result_order= sql_select($sql_order_qty);
	$job_order_arr=array();
	foreach($result_order as $value)
	{
		$job_order_arr[$value[csf('job_no_mst')]]=$value[csf('po_quantity')];
	}
	$sql_order= "select job_no,sum(wo_value) as total_wo_value from wo_labtest_order_dtls where job_no in ($all_job_no) and status_active=1
	and is_deleted=0 $poIdCond1  group by  job_no  ";
	//echo $sql_order;
	$result= sql_select($sql_order);
	$i=1;
	$commants='';
	foreach($result as $val)
	{
		if ($i%2==0)
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$total_wo_value=$amount_arr[$val[csf('job_no')]]['wo_value'];
			$total_budget=$job_order_arr[$val[csf('job_no')]]*$job_arr_labtest[$val[csf('job_no')]];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center" style="font-size:15px"><? echo $i; ?></td>
                <td align="center" style="font-size:15px"><? echo $val[csf("job_no")]; ?></td>
                <td align="right" style="font-size:15px"><? echo number_format($total_budget,2); ?></td>
                <td align="right" style="font-size:15px"><? echo number_format($total_wo_value,2); ?></td>
                <td align="right" style="font-size:15px"><?
				$wo_balance=$total_budget-$total_wo_value;  echo number_format($wo_balance,2);
				 ?></td>
                <td align="center" style="font-size:15px">
				<?
					if($wo_balance<0)  $commants="Over"  ;
					else if($wo_balance==0)  $commants="At Per";
					else if($wo_balance>0)  $commants="Less";
					echo $commants;
				?>
                </td>
			</tr>
		<?
        $i++;
        }
        ?>
        </tbody>

      </table>
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


if($action=="show_trim_booking_report_new")
{
    extract($_REQUEST);

	echo load_html_head_contents("Lab Test Work Order", "../../", 1, 1,'','','');
	$data=explode('*',str_replace("'","",$data));
	$sql="select a.id, a.labtest_no, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.address, a.attention,
	a.ready_to_approved from wo_labtest_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//$style_ref_no_library=return_library_array( "select job_no, style_ref_no from wo_po_details_master", "job_no", "style_ref_no"  );

	$lab_test_rate_library=return_library_array( "select id, test_item from lib_lab_test_rate_chart", "id", "test_item"  );

	$supplier_library=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id
	and b.party_type=26 order by a.supplier_name","id","supplier_name"  );
	$total_charge=0;
	 $sql_dtls= "select id, mst_id, po_id, job_no, entry_form, test_for, test_item_id, test_item_value, color, amount, discount, labtest_charge, wo_value, remarks
	from wo_labtest_dtls
	where mst_id=$data[1] and is_deleted=0 and status_active=1 order by  job_no";
	$sql_result= sql_select($sql_dtls);
	$amount_arr=array();
	$job_nos='';
	$poArr=array();
	foreach($sql_result as $inf)
	{
		$amount_arr[$inf[csf('job_no')]]['wo_value']=$inf[csf('wo_value')];
		$total_charge+=$inf[csf('labtest_charge')];
		if($job_nos=='') $job_nos=$inf[csf('job_no')];else $job_nos.=",".$inf[csf('job_no')];
		if($inf[csf('po_id')]){
			$poArr[$inf[csf('po_id')]]=$inf[csf('po_id')];
		}
	}
	$jobid=array_unique(explode(",",$job_nos));
	$jobs='';
	foreach($jobid as $jid)
	{
		if($jobs=='') $jobs="'$jid'";else $jobs.=","."'$jid'";
	}
	$poCond="";
	if(count($poArr)>0){
		$poCond="and b.id in (".implode(",",$poArr).")";
	}
	//echo $jobs;
	//echo $job_nos;
	$po_numberArr=array();
	$po_shipdateArr=array();
	$job_arr=array();
	$pos_sql="select b.id, b.po_number,b.shipment_date,a.buyer_name,a.style_ref_no as style_ref, a.job_no from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and a.job_no in($jobs) $poCond ";
	$sql_results=sql_select($pos_sql);
	foreach($sql_results as $row)
	{
		$buyer_library[$row[csf('job_no')]]=$row[csf('buyer_name')];
		$style_ref_no_library[$row[csf('job_no')]]=$row[csf('style_ref')];
		$po_no_arr[$row[csf('job_no')]].=$row[csf('po_number')].',';
		$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
		$po_numberArr[$row[csf('id')]]=$row[csf('po_number')];
		$po_shipdateArr[$row[csf('id')]]=$row[csf('shipment_date')];

	}

	$buyer_name='';
	foreach($jobid as $job)
	{
			if($buyer_name=='') $buyer_name=$buyer_name_arr[$buyer_library[$job]];else $buyer_name.=",".$buyer_name_arr[$buyer_library[$job]];
	}
	//echo $buyer_name;
 //echo $total_charge."**";die;

 $varcode_booking_no=$dataArray[0][csf('labtest_no')];


 ?>
 <div style="width:1030px;" align="center">
    <table width="900" cellspacing="0" align="center" style="table-layout: fixed;">
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
            <td colspan="6" align="center"><strong><u style="font-size: 18px;font-weight: bold;">Lab Test Work Order</u></strong></td>
        </tr>
    </table>
    <br/> 
    <table cellspacing="0" width="900" align="center" border="1" rules="all" class="rpt_table">
        <tr>
        	<td width="150"><strong>Wo No :</strong></td><td width="175px"><? echo $dataArray[0][csf('labtest_no')]; ?></td>
            <td width="110"><strong>Test Company:</strong></td><td width=""><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td width="110"><strong>Buyer :</strong></td>
            <td >&nbsp;<? echo implode(",",array_unique(explode(",",$buyer_name))); ?></td>
        </tr>
        <tr>
        	<td width="150"><strong>WO Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('wo_date')]); ?></td>
            <td width="110"><strong>Currency:</strong></td> <td width="175px"><? echo $currency[$dataArray[0][csf('currency')]]; ?></td>
            <td width="115"><strong>Exchange Rate:</strong></td><td width="175px"><? echo $dataArray[0][csf('ecchange_rate')]; ?></td>

        </tr>
        <tr>
        	<td><strong>Delivery Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            <td><strong>Pay Mode:</strong></td> <td width="175px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            <td><strong>Attention:</strong></td><td width="175px"><? echo $dataArray[0][csf('attention')]; ?></td>

        </tr>
        <tr>

            <td><strong>Address :</strong></td><td width="" colspan="3"><? echo $dataArray[0][csf('address')]; ?></td>
          	<td><strong>Rate For:</strong></td><td width="175px"><?
			 if($total_charge>0) {$rate_for='Express';}  else  { $rate_for='Regular';}
			 echo $rate_for; ?>
            </td>
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
			$colum_span=$colum_span+4;
			if(trim($all_job_no)!='') $all_job_no.=",'".$row[csf("job_no")]."'";
			else $all_job_no="'".$row[csf("job_no")]."'";
			$total_net_reate=0;

			//print_r($test_item_value);
			$index=0;
			foreach($test_item as $name)
			{

				if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$converted_currency=set_conversion_rate($name[csf('currency_id')],$txt_workorder_date);
				$actual_currency=$converted_currency/$current_currency;
				//$actual_net_rate=$actual_currency*$name[csf('net_rate')];
				//$po_no=rtrim($po_no_arr[$row[csf("job_no")]],',');
				if($row[csf("po_id")]==""){
				$po_no=rtrim($po_no_arr[$row[csf("job_no")]],',');
				}else{
					$po_no=$po_numberArr[$row[csf('po_id')]];
		            $shipment_date=$po_shipdateArr[$row[csf('po_id')]];
				}

				if(!in_array($i,$main_row))
				{
				$main_row[]=$i; //style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"
				if(!in_array($row[csf("po_id")],$date_array))
				{

		?>
        		 <table  cellspacing="0" width="950"  border="1" rules="all" class=""  align="left" style=" margin:0px 0px 0px 40px;table-layout: fixed;">
		         <thead bgcolor="#dddddd" >
		         <tr>
		         <th width="800" align="left" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; text-align:left; font-weight: 900;" colspan="6">Job No:<? echo $row[csf("job_no")]; ?>,Style: <?  echo $style_ref_no_library[$row[csf("job_no")]] ;?>,Po No:<? echo $po_no ?>,Ship Date:<? echo date("d-m-Y",strtotime($shipment_date)); ?></th>
		         </tr>
		             <tr>
		             		<th width="40">SL</th>
		                    <th width="80">Test For</th>
		                    <th width="120">Remarks</th>
		                    <th width="120">Color</th>
		                    <th width="220">Test Item</th>
		                    <th width="">Amount</th>
		                </tr>
                        <? }
						$date_array[]=$row[csf("po_id")];

						?>
		        </thead>

		        <tbody >
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center" style="font-size:15px" rowspan="<? echo $colum_span?>"><? echo $i; ?></td>

                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $test_for[$row[csf("test_for")]]; ?></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $row[csf("remarks")]; ?></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $color_library[$row[csf("color")]]; ?></td>
                    <td align="left" style="word-break:break-all;font-size:15px"> <? echo $lab_test_rate_library[$name];  ?> </td>


                     <td align="right" style="font-size:15px">
                    <?
						//$actual_net_rate=$row[csf("amount")];
						$actual_net_rate=$test_item_value[$index];
                        echo number_format($actual_net_rate,4);  $total_net_reate+=$actual_net_rate;
						//echo $test_item_value[$index].'=lkkk';
                    ?>
                    </td>
                </tr>
		   <?
				}
				else
				{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">

                    <td align="left" style="font-size:15px"> <? echo $lab_test_rate_library[$name]; ?>
                    </td>
                     <td align="right" style="font-size:15px">
                    <?
						$actual_net_rate=$test_item_value[$index];
                        echo number_format($actual_net_rate,4);
                         $total_net_reate+=$actual_net_rate;
                    ?>
                    </td>
                </tr>

				<?
				}
				$index++;
			}
			?>
             <tr bgcolor="#E3E3E3">
                <td align="right" style="font-size:15px" ><b>Gross Amount</b></td>
                <td align="right" style="font-size:15px"><b><? echo number_format($total_net_reate,4); ?></b></td>
			</tr>
            <tr bgcolor="<? //echo $bgcolor; ?>">
                <td align="right" style="font-size:15px" >Add Quick Delv Charge (USD)</td>
                <td align="right" style="font-size:15px"><? echo number_format($row[csf("labtest_charge")],4) ; ?></td>
			</tr>
              <tr bgcolor="<? //echo $bgcolor; ?>">
                <td align="right" style="font-size:15px" >Less Discount</td>
                <td align="right" style="font-size:15px"><? echo number_format($row[csf("discount")],4) ; ?></td>
			</tr>
            </tr>
              <tr bgcolor="#E3E3E3">
                <td align="right" style="font-size:15px" ><b>WO Value</</td>
                <td align="right" style="font-size:15px"><b>
				<?
				 $toatal_wo_value=$row[csf("labtest_charge")]+$total_net_reate-$row[csf("discount")];
				 $grand_wo_value+=$toatal_wo_value;
				 echo number_format(($toatal_wo_value),4) ;
				  ?></b></td>
			</tr>
            <?

        $i++;
        }
        ?>
        	<tr bgcolor="#B7B7B7">
                <td align="right" style="font-size:15px" colspan="5"><b>Grand Total</</td>
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
    <table  cellspacing="0" width="800"  border="1" rules="all" class=""  align="left" style=" margin:30px 0px 0px 40px; display:none">
        <thead bgcolor="#dddddd" >
        	<tr>
                 <th colspan="9" align="left">Comments</th>
           </tr>
           <tr>
                <th width="60">SL</th>
                <th width="120">Job No</th>
                <th width="130">Pre-Cost Value</th>
                <th width="140">WO Value</th>
                <th width="140">Balance</th>
                <th >Comments</th>

             </tr>
        </thead>
        <tbody>

 <?
	$all_job_no=implode(",",array_unique(explode(",",$all_job_no)));

	$sql_pre_cost="select a.costing_per,b.lab_test,a.job_no from wo_pre_cost_mst a, wo_pre_cost_dtls b where a.job_no=b.job_no and a.job_no in ($all_job_no)";
	$result_precost= sql_select($sql_pre_cost);
	$job_arr_labtest=array();
	foreach($result_precost as $inf)
	{
		$costing_per=$inf[csf('costing_per')];
		if($costing_per==1)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/12;
		}
		else if($costing_per==2)
		{
			$costing_per_pcs=$inf[csf('lab_test')];
		}
		else if($costing_per==3)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/24;
		}
		else if($costing_per==4)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/36;
		}
		else if($costing_per==5)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/48;
		}
		$job_arr_labtest[$inf[csf('job_no')]]=$costing_per_pcs;
	}
	$poIdCond="";
	$poIdCond1="";
	if(count($poArr)>0){
		$poIdCond="and id in (".implode(",",$poArr).")";
		$poIdCond1="and order_id in (".implode(",",$poArr).")";
	}
	$sql_order_qty="select sum(po_quantity) as po_quantity,job_no_mst from  wo_po_break_down  where job_no_mst in ($all_job_no) and status_active=1  and is_deleted=0 $poIdCond group by job_no_mst ";

	$result_order= sql_select($sql_order_qty);
	$job_order_arr=array();
	foreach($result_order as $value)
	{
		$job_order_arr[$value[csf('job_no_mst')]]=$value[csf('po_quantity')];
	}
	$sql_order= "select job_no,sum(wo_value) as total_wo_value from wo_labtest_order_dtls where job_no in ($all_job_no) and status_active=1
	and is_deleted=0  $poIdCond1 group by  job_no  ";
	//echo $sql_order;
	$result= sql_select($sql_order);
	$i=1;
	$commants='';
	foreach($result as $val)
	{
		if ($i%2==0)
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$total_wo_value=$amount_arr[$val[csf('job_no')]]['wo_value'];
			$total_budget=$job_order_arr[$val[csf('job_no')]]*$job_arr_labtest[$val[csf('job_no')]];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center" style="font-size:15px"><? echo $i; ?></td>
                <td align="center" style="font-size:15px"><? echo $val[csf("job_no")]; ?></td>
                <td align="right" style="font-size:15px"><? echo number_format($total_budget,2); ?></td>
                <td align="right" style="font-size:15px"><? echo number_format($total_wo_value,2); ?></td>
                <td align="right" style="font-size:15px"><?
				$wo_balance=$total_budget-$total_wo_value;  echo number_format($wo_balance,2);
				 ?></td>
                <td align="center" style="font-size:15px">
				<?
					if($wo_balance<0)  $commants="Over"  ;
					else if($wo_balance==0)  $commants="At Per";
					else if($wo_balance>0)  $commants="Less";
					echo $commants;
				?>
                </td>
			</tr>
		<?
        $i++;
        }
        ?>
        </tbody>
     </div>
      </table>
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