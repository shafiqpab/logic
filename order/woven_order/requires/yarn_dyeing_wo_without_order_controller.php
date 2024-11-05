<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$sample_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");

if ($action=="variable_chack")
{
	extract($_REQUEST);
	//echo "$company";
	$company=str_replace("'","",$company);
	$variable_setting=return_field_value("color_from_library","variable_order_tracking","variable_list=24 and company_name=$company");
	echo $variable_setting;
	exit();
}

if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=8 and is_deleted=0 and status_active=1");
	$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$data and variable_list=18 and item_category_id = 1","allocation");
	if($variable_set_allocation==0 || $variable_set_allocation=='') $variable_set_allocation=2;else $variable_set_allocation=$variable_set_allocation;
	echo "document.getElementById('txt_allocation_variable').value = '".$variable_set_allocation."';\n";

	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}

if ($action=="load_drop_down_sample")
{
	$sql="Select b.id, b.sample_name from  lib_sample b,  sample_development_dtls a where a.sample_name=b.id and b.status_active=1 and a.sample_mst_id=$data group by b.id, b.sample_name order by b.sample_name ASC";
	echo create_drop_down( "cbo_sample_name", 70, $sql,"id,sample_name", 1, "-select-", $selected,"","0" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if($action=="load_drop_down_supplier")
{
	$exdata=explode("_",$data);
	if($exdata[1]==5 || $exdata[1]==3){
	   echo create_drop_down( "cbo_supplier_name", 140, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-Select Company-", $exdata[0], "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/print_booking_urmi_controller');",0,"" );
	}
	else
	{
		echo create_drop_down( "cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id and c.tag_company='$exdata[0]'  and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type=2) and a.id in(select supplier_id from lib_supplier_party_type where party_type=21) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-Select Supplier-",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/print_booking_urmi_controller');","");

		//echo create_drop_down( "cbo_supplier_name", 140, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$exdata[0]' and b.party_type in(1,2,21) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "-Select Supplier-",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/print_booking_urmi_controller');","");



	}
	exit();
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
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo "1"."_".$currency_rate;
	exit();
}

if ($action=="order_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $company;
	?>
	<script>
		function js_set_value(str)
		{
			$("#hidden_tbl_id").val(str); // wo/pi id
			parent.emailwindow.hide();
		}
	</script>

	<div align="center" style="width:715px;" >
		<form name="searchjob"  id="searchjob" autocomplete="off">
			<table width="560" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<th width="130">Company</th>
					<th width="130">Buyer</th>
					<th width="100">Sample Id</th>
					<th width="100">Requisition No.</th>
					<th width="100">Style Ref. No.</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:90px" class="formbutton" onClick="reset_form('searchjob','search_div','')"  /></th>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?=create_drop_down( "cbo_company_name", 145, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$company)/*$selected */, "load_drop_down( 'yarn_dyeing_wo_without_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?></td>
						<td id="buyer_td">
							<?
							$blank_array="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
							echo create_drop_down( "cbo_buyer_name", 145, $blank_array,"id,buyer_name", 1, "-- Select Buyer --",0);
							?>
						</td>
						<td><input type="text" name="txt_sample_id" id="txt_sample_id" class="text_boxes" style="width:90px" /></td>
						<td><input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:90px" /></td>
						<td><input type="text" name="txt_style_ref_no" id="txt_style_ref_no" class="text_boxes" style="width:90px" /></td>
						<td>
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_id').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_requisition_no').value+'_'+document.getElementById('txt_style_ref_no').value+'_<? echo $txt_sam_booking_no; ?>', 'create_sample_search_list_view', 'search_div', 'yarn_dyeing_wo_without_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:90px;" />
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div align="center" valign="top" id="search_div"> </div>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="create_sample_search_list_view")
{
	$data=explode("_",$data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_buyer_name=str_replace("'","",$data[1]);
	$txt_sample_id=str_replace("'","",$data[2]);
	$txt_requisition_no=str_replace("'","",$data[4]);
	$style_ref_no=str_replace("'","",$data[5]);
	$txt_sam_booking_no=str_replace("'","",$data[6]);

	//echo $cbo_company_name."**".$cbo_buyer_name."**".$txt_job_no;

	if($cbo_company_name!=0) $cbo_company_name="and a.company_id='$cbo_company_name'"; else $cbo_company_name="";
	if($cbo_buyer_name!=0) $cbo_buyer_name="and a.buyer_name='$cbo_buyer_name'"; else $cbo_buyer_name="";
	if($txt_sample_id!="") $sample_cond="and a.id=$txt_sample_id"; else $sample_cond="";
	if($txt_requisition_no!="") $txt_requisition_no_cond="and a.requisition_number LIKE '%$txt_requisition_no'"; else $txt_requisition_no_cond="";
	if($style_ref_no!="") $style_ref_no_cond="and a.style_ref_no = '$style_ref_no'"; else $style_ref_no_cond="";

	if($txt_sam_booking_no == '')
	{
		if($db_type==0)
		{
			$sql="select a.id, a.company_id, a.buyer_name, a.style_ref_no,group_concat(distinct b.sample_name) as sample_name, a.requisition_number from   sample_development_mst a,  sample_development_dtls b where a.id=b.sample_mst_id $cbo_company_name $cbo_buyer_name $sample_cond $txt_requisition_no_cond $style_ref_no_cond group by a.id ";
		}
		else if($db_type==2)
		{
			$sql="select a.id, a.company_id, a.buyer_name, a.style_ref_no,listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name,a.requisition_number from   sample_development_mst a,  sample_development_dtls b where a.id=b.sample_mst_id $cbo_company_name $cbo_buyer_name $sample_cond $txt_requisition_no_cond $style_ref_no_cond group by a.id, a.company_id, a.buyer_name, a.style_ref_no,a.requisition_number";

		}
	}
	else
	{
		if($db_type==0)
		{
			$sql="select a.id, a.company_id, a.buyer_name, a.style_ref_no,group_concat(distinct b.sample_name) as sample_name
			from sample_development_mst a,  sample_development_dtls b, wo_non_ord_samp_booking_dtls c
			where a.id=b.sample_mst_id and b.sample_mst_id=c.style_id and c.color_type_id in(2,3,4,6) and c.booking_no='$txt_sam_booking_no' $cbo_company_name $cbo_buyer_name $sample_cond
			group by a.id ";
		}
		else if($db_type==2)
		{
			$sql="select a.id, a.company_id, a.buyer_name, a.style_ref_no,listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name
			from sample_development_mst a,  sample_development_dtls b, wo_non_ord_samp_booking_dtls c
			where a.id=b.sample_mst_id and b.sample_mst_id=c.style_id and c.color_type_id in(2,3,4,6) and c.booking_no='$txt_sam_booking_no' $cbo_company_name $cbo_buyer_name $sample_cond
			group by a.id, a.company_id, a.buyer_name, a.style_ref_no";
		}
	}
	echo '<input type="hidden" id="hidden_tbl_id">';
	?>
	<div style="width:720px;" align="left">

		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="710" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="60">Sample Id</th>
				<th width="150">Sample Name</th>
				<th width="130">Buyer</th>
				<th width="90"> Style Ref.NO</th>
				<th width="50"> Requisition No.</th>

			</thead>
		</table>
		<div style="width:720px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="710" class="rpt_table" id="tbl_list_search" >
				<?

				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$sample_name=array_unique(explode(",",$selectResult[csf("sample_name")]));
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>); ">
						<td width="40"><p> <? echo $i; ?></p></td>
						<td width="60"> <p><? echo $selectResult[csf("id")]; ?></p></td>
						<td width="150"> <p>
						<?
                        $sample_name_result='';
                        foreach($sample_name as $val)
                        {
                            if($sample_name_result=='') $sample_name_result=$sample_arr[$val]; else $sample_name_result=$sample_name_result.",".$sample_arr[$val];
                        }
                        echo $sample_name_result;
                        ?></p></td>
                        <td width="130"><p><?  echo  $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>
                        <td width="90"> <p><?  echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="50"><?  echo $selectResult[csf('requisition_number')]; ?></td>

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

$color_arr=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name"  );

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$txt_sample_id		= str_replace("'","",$txt_sample_id);
	$cbo_sample_name	= str_replace("'","",$cbo_sample_name);
	$txt_pro_id			= str_replace("'","",$txt_pro_id);
	$cbo_count			= str_replace("'","",$cbo_count);
	$txt_item_des		= str_replace("'","",$txt_item_des);
	$color_id			= str_replace("'","",$color_id);
	$cbo_color_range	= str_replace("'","",$cbo_color_range);
	$txt_ref_no			= str_replace("'","",$txt_ref_no);
	$txt_wo_qty			= str_replace("'","",$txt_wo_qty);
	$hdn_wo_qty			= str_replace("'","",$hdn_wo_qty);
	$txt_yern_color		= str_replace("'","",$txt_yern_color);

	// CHEKING IF ALLOCATION IS SET "YES" IN VARIABLE SETTINGS
	$variable_smn_allocation_check = return_field_value("smn_allocation","variable_settings_inventory","company_name=$cbo_company_name and variable_list=18 and item_category_id = 1");

	$auto_allocation_from_requisition = return_field_value("auto_allocate_yarn_from_requis"," variable_settings_production","company_name=$cbo_company_name and variable_list=6 and status_active=1 and is_deleted=0","auto_allocate_yarn_from_requis");

	if($variable_smn_allocation_check == 1 && $auto_allocation_from_requisition!=1)
	{
		$booking_allocation_qnty = return_field_value("sum(qnty) as allocated_qnty","inv_material_allocation_mst","item_id=$txt_pro_id and booking_no=$txt_sam_booking_no and status_active=1 and is_deleted=0","allocated_qnty");

		$ydw_qnty_sql="select x.wo_num,x.booking_no,x.product_id,sum(x.yarn_wo_qty) yarn_wo_qty from(select LISTAGG(a.ydw_no, ',') WITHIN GROUP (ORDER BY b.id) as wo_num,b.booking_no,b.product_id,sum(b.yarn_wo_qty) yarn_wo_qty from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form=42 and b.entry_form=42 and b.booking_no=$txt_sam_booking_no and b.product_id=$txt_pro_id group by b.booking_no,b.product_id)x group by x.wo_num,x.booking_no,x.product_id";
		$ydw_qnty = sql_select($ydw_qnty_sql);

		$wo_numbers = implode(", ",array_unique(explode(",", $ydw_qnty[0][csf("wo_num")])));
		$previous_ydsw_qty = $ydw_qnty[0][csf('yarn_wo_qty')]*1;

		// GET PROGRAM NUMBERS AGAINST SAMPLE BOOKING
		if ($db_type == 0) {
			$all_knit_id = return_field_value("group_concat(distinct(b.id)) as knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id=b.mst_id and a.booking_no=$txt_sam_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "knit_id");
		} else {
			$all_knit_id = return_field_value("LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as knit_id", "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b", "a.id=b.mst_id and a.booking_no=$txt_sam_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "knit_id");
			$all_knit_id = implode(",", array_unique(explode(",", $all_knit_id)));
		}

		$previous_requsition_qty=0;
		// GET REQUISITION QUANTITY AGAINST PROGRAM NUMBERS
		if($all_knit_id!="")
		{
			if($db_type == 0)
			{
				$req_sql = "select a.booking_no,group_concat(distinct(c.requisition_no)) as requisition_no,c.prod_id,sum(c.yarn_qnty) as yarn_qnty from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and b.id in ($all_knit_id) and c.prod_id=$txt_pro_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.booking_no,c.prod_id";

			}else{
				$req_sql = "select a.booking_no,LISTAGG(c.requisition_no, ',') WITHIN GROUP (ORDER BY c.requisition_no) as requisition_no,c.prod_id,sum(c.yarn_qnty) as yarn_qnty from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and b.id in ($all_knit_id) and c.prod_id=$txt_pro_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.booking_no,c.prod_id";
			}

			$req_result = sql_select($req_sql);
			$requisition_nos = $req_result[0][csf('requisition_no')];
			$previous_requsition_qty =  $req_result[0][csf('yarn_qnty')];
		}

		if ($operation==0)
		{
			$allocation_balance = ($booking_allocation_qnty - ($previous_ydsw_qty+$previous_requsition_qty+$txt_wo_qty));
			$allocation_balance_msg = ($booking_allocation_qnty - ($previous_ydsw_qty+$previous_requsition_qty));

			if( $allocation_balance < 0)
			{
				echo "10**Yarn Wo. Qnty is not available.\n\nTotal Allocation=$booking_allocation_qnty\nPrevious work order QTY=$previous_ydsw_qty\nWork Order No(s)=$wo_numbers\nRequsition No:$requisition_nos\nRequsition QTY=$previous_requsition_qty\n\nBalance=$allocation_balance_msg";
				die;
			}
		}

		if ($operation==1)
		{
			$allocation_balance = $booking_allocation_qnty - (($previous_ydsw_qty-$hdn_wo_qty)+$previous_requsition_qty+$txt_wo_qty);
			$allocation_balance_msg = $booking_allocation_qnty - (($previous_ydsw_qty-$hdn_wo_qty)+$previous_requsition_qty);

			if( $allocation_balance < 0)
			{
				$allocation_balance = ($booking_allocation_qnty - (($previous_ydsw_qty-$hdn_wo_qty)+$txt_wo_qty));
				echo "10**Yarn Wo. Qnty is not available.\n\nTotal Allocation=$booking_allocation_qnty\nTotal work order QTY= ".(($previous_ydsw_qty-$hdn_wo_qty)+$txt_wo_qty)." (including this Wo.)\nWork Order No(s)=$wo_numbers\nRequsition No:$requisition_nos\nRequsition QTY=$previous_requsition_qty\n\nBalance=$allocation_balance_msg";
				die;
			}
		}
	}
	//echo "10**".$booking_allocation_qnty ."-((". $previous_ydsw_qty.")+".$previous_requsition_qty."+".$txt_wo_qty.")";
	//die;
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if (str_replace("'", "", trim($txt_yern_color)) != "")
		{
			if (!in_array(str_replace("'", "", trim($txt_yern_color)),$new_array_color))
			{
				$color_id = return_id( str_replace("'", "", trim($txt_yern_color)), $color_arr, "lib_color", "id,color_name","42");
				$new_array_color[$color_id] = str_replace("'", "", trim($txt_yern_color));
			}
			else {
				$color_id =  array_search(str_replace("'", "", trim($txt_yern_color)), $new_array_color);
			}
		}
		else $color_id=0;

		$duplicate = is_duplicate_field("id","wo_yarn_dyeing_dtls","job_no_id='$txt_sample_id' and product_id='$txt_pro_id' and count='$cbo_count' and yarn_description='$txt_item_des' and yarn_color='$color_id' and color_range='$cbo_color_range' and referance_no='$txt_ref_no' and entry_form=42");
		if($duplicate==1)
		{
			echo "11**Duplicate is Not Allow in Same Job Number.";
			die;
		}

		if(str_replace("'","",$update_id)!="") //update
		{
			$id= return_field_value("id"," wo_yarn_dyeing_mst","id=$update_id");//check sys id for update or insert
			$field_array="company_id*supplier_id*booking_date*delivery_date*delivery_date_end*dy_delivery_date_start*dy_delivery_date_end*currency*ecchange_rate*pay_mode*source*attention*tenor*ready_to_approved*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_name."*".$cbo_supplier_name."*".$txt_booking_date."*".$txt_delivery_date."*".$txt_delivery_end."*".$dy_delevery_start."*".$dy_delevery_end."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$txt_tenor."*".$cbo_ready_to_approved."*'".$user_id."'*'".$pc_date_time."'*1*0";
			//$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$id,1);
			$return_no=str_replace("'",'',$txt_booking_no);
		}
		else // new insert
		{

			$id = return_next_id_by_sequence("WO_YARN_DYEING_MST_YDW_PK_SEQ", "wo_yarn_dyeing_mst", $con);

			$new_sys_number = explode("*", return_next_id_by_sequence("WO_YARN_DYEING_MST_YDW_PK_SEQ", "wo_yarn_dyeing_mst",$con,1,$cbo_company_name,'YDW',999,date("Y",time()),0 ));

			//echo $new_sys_number[0];die;
			$field_array="id,yarn_dyeing_prefix,yarn_dyeing_prefix_num,ydw_no,entry_form,company_id,supplier_id,item_category_id,booking_date,delivery_date,delivery_date_end,dy_delivery_date_start,dy_delivery_date_end,currency,ecchange_rate,pay_mode,source,attention,tenor,ready_to_approved,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',42,".$cbo_company_name.",".$cbo_supplier_name.",".$cbo_item_category_id.",".$txt_booking_date.",".$txt_delivery_date.",".$txt_delivery_end.",".$dy_delevery_start.",".$dy_delevery_end.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$txt_tenor.",".$cbo_ready_to_approved.",'".$user_id."','".$pc_date_time."',1,0)";
			$return_no=str_replace("'",'',$new_sys_number[0]);
		}

		$dtlsid=return_next_id("id","wo_yarn_dyeing_dtls", 1);
		$field_array_dts="id,mst_id,job_no_id,booking_no,entry_form,sample_name,product_id,count,yarn_description,yarn_color,color_range,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag,no_of_cone,min_require_cone,remarks,batch_ld_no,referance_no,status_active,is_deleted";
		$data_array_dts="(".$dtlsid.",".$id.",'".$txt_sample_id."',".$txt_sam_booking_no.",42,'".$cbo_sample_name."','".$txt_pro_id."','".$cbo_count."','".$txt_item_des."',".$color_id.",'".$cbo_color_range."',".$cbo_uom.",".$txt_wo_qty.",".$txt_dyeing_charge.",".$txt_amount.",".$txt_bag.",".$txt_cone.",".$txt_min_req_cone.",".$txt_remarks.",".$txt_batch_ld_no.",'".$txt_ref_no."',1,0)";

		// Test for insert all
		if(str_replace("'","",$update_id)!="") //update
		{
			$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("wo_yarn_dyeing_mst",$field_array,$data_array,0);
		}
		$dtlsrID=sql_insert("wo_yarn_dyeing_dtls",$field_array_dts,$data_array_dts,1);

		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK");
				//echo "10**";
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}

		if($db_type==1 || $db_type==2)
		{
			if($rID && $dtlsrID)
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
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$dtls_update_id=str_replace("'","",$dtls_update_id);

		//check update id
		if( str_replace("'","",$update_id) == "")
		{
			echo "15";exit();
		}
		$is_approved=return_field_value( "is_approved", "wo_yarn_dyeing_mst","ydw_no=$txt_booking_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "approve**".str_replace("'","",$txt_booking_no);
			die;
		}

		if (str_replace("'", "", trim($txt_yern_color)) != "")
		{
			if (!in_array(str_replace("'", "", trim($txt_yern_color)),$new_array_color))
			{
				$color_id = return_id( str_replace("'", "", trim($txt_yern_color)), $color_arr, "lib_color", "id,color_name","42");
				$new_array_color[$color_id]=str_replace("'", "", trim($txt_yern_color));
			}
			else $color_id =  array_search(str_replace("'", "", trim($txt_yern_color)), $new_array_color);
		}
		else $color_id=0;

		$all_issue_qty = return_field_value("sum(c.cons_quantity) as issue_qnty"," inv_issue_master b, inv_transaction c ","b.id=c.mst_id and b.entry_form=3 and b.booking_no=$txt_booking_no and c.prod_id=$txt_pro_id  and b.item_category=1 and c.item_category=1 and c.transaction_type=2 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.dyeing_color_id=$color_id group by b.buyer_job_no","issue_qnty");

		$total_issue_return_qty = return_field_value("sum(c.cons_quantity) as issue_ret_qnty"," inv_receive_master b, inv_transaction c ","b.id=c.mst_id and b.entry_form=9 and b.booking_no=$txt_booking_no and c.prod_id=$txt_pro_id  and b.item_category=1 and c.item_category=1 and c.transaction_type=4 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by b.booking_no","issue_ret_qnty");

		//echo "13**".$all_issue_qty."===".$total_issue_return_qty; die;

		$balance_issue_qty = $all_issue_qty-$total_issue_return_qty;
		if($txt_wo_qty<$balance_issue_qty)
		{
			die("13**Work order quantity can not be less than Issue balance quantity.\nIssue balance quantity=$balance_issue_qty");
		}

		$recv_number=return_field_value( "recv_number", "wo_yarn_dyeing_mst a,inv_receive_master b, inv_transaction c"," b.booking_id=a.id and b.id=c.mst_id and a.ydw_no=$txt_booking_no and b.receive_basis=2 and b.receive_purpose=2 and b.entry_form=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category=1 and c.item_category=1 and c.transaction_type=1 and c.dyeing_color_id=$color_id group by recv_number");

		if($recv_number!="")
		{
			echo "13**Receive found against this Work Order. Update not allowed."."**".$recv_number;die;
		}


		$duplicate = is_duplicate_field("id","wo_yarn_dyeing_dtls"," id!=$dtls_update_id and job_no_id='$txt_sample_id' and product_id='$txt_pro_id' and count='$cbo_count' and yarn_description='$txt_item_des' and yarn_color='$color_id' and color_range='$cbo_color_range' and referance_no='$txt_ref_no' and entry_form=42");

		if($duplicate==1)
		{
			echo "11**Duplicate is Not Allow in Same Job Number.";
			die;
		}

		//wo_yarn_dyeing_mst master table UPDATE here START----------------------//	".$txt_pro_id.",
		$field_array="company_id*supplier_id*booking_date*delivery_date*delivery_date_end*dy_delivery_date_start*dy_delivery_date_end*currency*ecchange_rate*pay_mode*source*attention*tenor*ready_to_approved*updated_by*update_date*status_active*is_deleted";
		$data_array="".$cbo_company_name."*".$cbo_supplier_name."*".$txt_booking_date."*".$txt_delivery_date."*".$txt_delivery_end."*".$dy_delevery_start."*".$dy_delevery_end."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$txt_tenor."*".$cbo_ready_to_approved."*'".$user_id."'*'".$pc_date_time."'*1*0";

		//inv_gate_in_mst master table UPDATE here END---------------------------------------//

		//inv_gate_in_dtls details table UPDATE here START-----------------------------------//

		$field_array_dtls = "job_no_id*booking_no*product_id*sample_name*count*yarn_description*yarn_color*color_range*uom*yarn_wo_qty*dyeing_charge*amount*no_of_bag*no_of_cone*min_require_cone*remarks*batch_ld_no*referance_no";
		$data_array_dtls = "'".$txt_sample_id."'*".$txt_sam_booking_no."*'".$txt_pro_id."'*".$cbo_sample_name."*".$cbo_count."*'".$txt_item_des."'*".$color_id."*".$cbo_color_range."*".$cbo_uom."*".$txt_wo_qty."*".$txt_dyeing_charge."*".$txt_amount."
		*".$txt_bag."*".$txt_cone."*".$txt_min_req_cone."*".$txt_remarks."*".$txt_batch_ld_no."*'".$txt_ref_no."'";
		//echo $field_array."<br>".$data_array;die;
		$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$update_id,1);
		$dtlsrID = sql_update("wo_yarn_dyeing_dtls",$field_array_dtls,$data_array_dtls,"id",$dtls_update_id,1);
		//inv_gate_in_dtls details table UPDATE here END-----------------------------------//

		//release lock table
		$return_no=str_replace("'",'',$txt_booking_no);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $dtlsrID)
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
			if($rID && $dtlsrID)
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
	else if ($operation==2) // Delete Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$update_id=str_replace("'","",$update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		$is_approved=return_field_value( "is_approved", "wo_yarn_dyeing_mst","ydw_no=$txt_booking_no");
		if($is_approved==3){
			$is_approved=1;
		}
		if($is_approved==1){
			echo "approve**".str_replace("'","",$txt_booking_no);
			die;
		}

		$check_issue=return_field_value( "issue_number", "inv_issue_master a, inv_transaction b","a.booking_no=$txt_booking_no and b.prod_id=$txt_pro_id and a.id=b.mst_id and a.item_category=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","issue_number");

		if($check_issue!="")
		{
			die("13**Issue Found, Delete not allowed.\nIssue No= $check_issue");
		}

		$recv_number=return_field_value( "recv_number", "wo_yarn_dyeing_mst a,inv_receive_master b, inv_transaction c"," b.booking_id=a.id and b.id=c.mst_id and a.ydw_no=$txt_booking_no and b.receive_basis=2 and b.receive_purpose=2 and b.entry_form=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category=1 and c.item_category=1 and c.transaction_type=1 group by recv_number");

		if($recv_number!="")
		{
			die("13**Receive Found, Delete not allowed.\nReceive No= $recv_number");
		}

		$txt_booking_no=str_replace("'","",$txt_booking_no);
		// master table delete here---------------------------------------
		if($update_id=="" || $update_id==0){ echo "15**0"; die;}

 		//$rID = sql_update("wo_non_order_info_mst",'status_active*is_deleted','0*1',"id",$mst_id,1);
		$dtlsrID = sql_update("wo_yarn_dyeing_dtls",'status_active*is_deleted','0*1',"id",$dtls_update_id,1);
		if($db_type==0 )
		{
			if($dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".$txt_booking_no."**".$update_id;
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
				echo "2**".$txt_booking_no."**".$update_id;
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

$count_arr=return_library_array( "Select id, yarn_count from  lib_yarn_count where  status_active=1",'id','yarn_count');

if($action=="show_dtls_list_view")
{
	?>
	<table class="rpt_table" width="1470" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
				<th width="100">Booking NO</th>
				<th width="70">Sample Id</th>
				<th width="70">Lot</th>
				<th width="60">Count</th>
				<th width="200">Description</th>
				<th width="100">Color</th>
				<th width="100">Color Range</th>
				<th width="60">UOM</th>
				<th width="80">WO QTY</th>
				<th width="80">Charge</th>
				<th width="100">Amount</th>
				<th width="80">No of Bag</th>
				<th width="80">No of Cone</th>
				<th width="100">Minimum Require Cone</th>
				<th width="100">Batch/LD No</th>
				<th >Ref NO</th>
			</tr>
		</thead>
		<tbody>
			<?
			$sql = sql_select("select id,booking_no,job_no_id,count,yarn_description,yarn_color,color_range,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag, no_of_cone,min_require_cone,referance_no,product_id,batch_ld_no from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 and mst_id='$data'");
			$i=1;
			foreach($sql as $row)
			{
				$lot=return_field_value("lot"," product_details_master","id=".$row[csf("product_id")]."","lot");

				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="get_php_form_data(<? echo $row[csf("id")]; ?>, 'child_form_input_data', 'requires/yarn_dyeing_wo_without_order_controller')" style="cursor:pointer;">
					<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("booking_no")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("job_no_id")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $lot; ?>&nbsp;</p></td>
					<td><p><? echo $count_arr[$row[csf("count")]]; ?>&nbsp;</p></td>
					<td><p><? echo $row[csf("yarn_description")]; ?>&nbsp;</p></td>
					<td><p><? echo $color_arr[$row[csf("yarn_color")]]; ?>&nbsp;</p></td>
					<td><p><? echo $color_range[$row[csf("color_range")]]; ?>&nbsp;</p></td>
					<td><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf("yarn_wo_qty")],0); ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf("dyeing_charge")],2); ?>&nbsp;</p></td>
					<td align="right"><p><? echo number_format($row[csf("amount")],2); ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("no_of_bag")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("no_of_cone")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("min_require_cone")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("batch_ld_no")]; ?>&nbsp;</p></td>
					<td><p><? echo $row[csf("referance_no")]; ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
	</table>
	<?
	exit();
}

if ($action=="sam_book_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
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
	<table width="940" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                   <thead>
                           <th colspan="2"> </th>
                        	<th  >
                              <?
                               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                            <th colspan="4"></th>
                     </thead>
                    <thead>
                        <th width="150" class="must_entry_caption">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Booking No</th>
                        <th width="90">IR/CN</th>
                        <th width="80">Style Desc.</th>
                        <th width="200">Date Range</th><th></th>
                    </thead>
        			<tr>
                    	<td>
                        <input type="hidden" id="selected_booking">
							<?
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company, "load_drop_down( 'yarn_dyeing_wo_without_order_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_td' );");
							?>
                        </td>
                   	<td id="buyer_td">
                     <?
						echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company'  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
					 ?>	</td>

                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
					<td><input name="txt_ir_ref" id="txt_ir_ref" class="text_boxes" style="width:90px"></td>
                    <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td>
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_desc').value+'_'+document.getElementById('txt_ir_ref').value, 'create_booking_search_list_view', 'search_div', 'yarn_dyeing_wo_without_order_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
			<? echo load_month_buttons(1);  ?>
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
exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$style_desc=$data[7];
	if ($data[0]!=0) $company=" and  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($data[8]!="" ) $intRefCond="and c.internal_ref like '%$data[8]%' "; else  $intRefCond="";

	if ($data[1]!=0){$buyer=" and a.buyer_id='$data[1]'";}
	else{$buyer="";}

	if($db_type==0)
	{
		$booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
    }

	if($db_type==2)
	{
		  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]%' "; else $style_des_cond="";
	}

    if($data[6]==1)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]'   "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des='$data[7]' "; else $style_des_cond="";
	}

    if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '$data[7]%' "; else $style_des_cond="";
	}

	if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[7])!="") $style_des_cond=" and b.style_des like '%$data[7]' "; else $style_des_cond="";
	}

	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	//$arr=array (2=>$comp,3=>$buyer_arr,4=>$item_category,5=>$fabric_source,6=>$suplier,7=>$style_library);
	$sql= "SELECT a.id,a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,b.style_id,b.style_des,c.internal_ref from wo_non_ord_samp_booking_mst  a, wo_non_ord_samp_booking_dtls b,sample_development_mst c where a.booking_no=b.booking_no and b.style_id=c.id and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond $intRefCond and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.color_type_id in(2,3,4,6,32,33,44,47,48)  order by a.id DESC";
	//echo $sql;
	?>
   	<table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="0" rules="all"  style="word-break:break-all;">
        <thead>
        	<tr>
                <th width="30">SL</th>
                <th width="100">Booking No</th>
                <th width="80">IR/CN</th>
                <th width="80">Booking Date</th>
                <th width="100">Company</th>
                <th width="100">Buyer</th>
                <th width="80">Fabric Nature</th>
                <th width="80">Fabric Source</th>
                <th width="80">Supplier</th>
                <th width="50">Style</th>
                <th >Style Desc.</th>
            </tr>
        </thead>
    </table>
    <div style="weight:900px; max-height:300px; overflow-y:scroll">
    <table class="rpt_table" width="880" cellpadding="0" cellspacing="0" border="0" rules="all" id="table_body" style="word-break:break-all;">
        <tbody>
			<?
            $i=1;
            //echo $sql;
            $sql_data=sql_select($sql);
            foreach($sql_data as $row){
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]  ?>')" style="cursor:pointer">
                    <td width="30"><? echo $i;?></td>
                    <td width="100"><? echo $row[csf('booking_no_prefix_num')];?></td>
                    <td width="80"><? echo $row[csf('internal_ref')];?></td>
                    <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>
                    <td width="100"><? echo $comp[$row[csf('company_id')]];?></td>
                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                    <td width="80"><? echo $item_category[$row[csf('item_category')]];?></td>
                    <td width="80"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>
                    <td width="80">
						<?
                        if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
                            echo $comp[$row[csf('supplier_id')]];
                        }else{
                            echo $suplier[$row[csf('supplier_id')]];
                        }
                        ?>
                    </td>
                    <td width="50"><? echo $style_library[$row[csf('style_id')]];?></td>
                    <td ><? echo $row[csf('style_des')];?></td>
				</tr>
				<?
				$i++;
            }
            ?>
        </tbody>
    </table>
    </div>
    <?
	exit();
}

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_sample_booking")
{
	$sql="Select b.id, b.sample_name from  lib_sample b,  wo_non_ord_samp_booking_dtls a where a.sample_type=b.id and b.status_active=1 and a.status_active=1 and a.booking_no='$data' and a.color_type_id in(2,3,4,6,32,33,44,47,48) group by b.id, b.sample_name order by b.sample_name ASC";
	echo create_drop_down( "cbo_sample_name", 70, $sql,"id,sample_name", 1, "-select-", $selected,"","0" );
	exit();
}
if($action=="child_form_input_data")
{

	//echo $data;
	$sql = "select id,mst_id,booking_no,job_no,product_id,job_no_id,count,yarn_description,yarn_color,color_range,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag, no_of_cone,min_require_cone,remarks,referance_no,sample_name,batch_ld_no from wo_yarn_dyeing_dtls where id='$data'";
	//echo $sql;
	$sql_re=sql_select($sql);

	$company_id = return_field_value("a.company_id as company_id", "wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b"," b.mst_id=a.id and b.id=$data  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","company_id");

	$variavle_info_inv = sql_select("select allocation from variable_settings_inventory where company_name=$company_id and variable_list =18 and status_active=1 and is_deleted=0");
	$variable_set_allocation = $variavle_info_inv[0][csf("allocation")];

	foreach($sql_re as $row)
	{
		echo "$('#txt_sample_id').val('".$row[csf("job_no_id")]."');\n";
		echo "$('#txt_sam_booking_no').val('".$row[csf("booking_no")]."');\n";
		$fab_booking_no=$row[csf("booking_no")];


		if($row[csf("job_no_id")]>0)
		{
			echo "load_drop_down( 'requires/yarn_dyeing_wo_without_order_controller', ".$row[csf("job_no_id")].", 'load_drop_down_sample', 'sample_td' );\n";
		}
		else{
			echo "load_drop_down( 'requires/yarn_dyeing_wo_without_order_controller', '".$row[csf("booking_no")]."', 'load_drop_down_sample_booking', 'sample_td' );\n";
		}


		echo "$('#cbo_sample_name').val(".$row[csf("sample_name")].");\n";
		echo "$('#txt_pro_id').val(".$row[csf("product_id")].");\n";
		$lot=return_field_value("lot"," product_details_master","id=".$row[csf("product_id")]."","lot");
		if($row[csf("product_id")]>0)
		{
			echo "$('#txt_lot').val('$lot');\n";
			echo "$('#cbo_count').val(".$row[csf("count")].").attr('disabled',true);\n";
			echo "$('#txt_item_des').val('".$row[csf("yarn_description")]."').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#txt_lot').val('$lot');\n";
			echo "$('#cbo_count').val(".$row[csf("count")].");\n";
			echo "$('#txt_item_des').val('".$row[csf("yarn_description")]."');\n";
		}
		$job_ref=$row[csf("job_no")];

		echo "$('#txt_yern_color').val('".$color_arr[$row[csf("yarn_color")]]."');\n";
		echo "$('#cbo_color_range').val(".$row[csf("color_range")].");\n";
		echo "$('#cbo_uom').val(".$row[csf("uom")].");\n";
		echo "$('#txt_wo_qty').val(".$row[csf("yarn_wo_qty")].");\n";
		echo "$('#hdn_wo_qty').val(".$row[csf("yarn_wo_qty")].");\n";
		echo "$('#txt_dyeing_charge').val(".$row[csf("dyeing_charge")].");\n";
		echo "$('#txt_amount').val(".$row[csf("amount")].");\n";
		echo "$('#txt_bag').val(".$row[csf("no_of_bag")].");\n";
		echo "$('#txt_cone').val(".$row[csf("no_of_cone")].");\n";
		echo "$('#txt_min_req_cone').val(".$row[csf("min_require_cone")].");\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#txt_batch_ld_no').val('".$row[csf("batch_ld_no")]."');\n";
		echo "$('#txt_ref_no').val('".$row[csf("referance_no")]."');\n";
		echo "$('#dtls_update_id').val(".$row[csf("id")].");\n"; //update id here

		if($variable_set_allocation==1)
		{
			$sql_allowcate=sql_select("select a.id,a.lot,b.po_break_down_id as po_id,b.qnty from product_details_master a,inv_material_allocation_mst b where a.id=b.item_id  and b.booking_no='$fab_booking_no' and a.id=".$row[csf("product_id")]." and a.item_category_id=1  and a.status_active=1 and b.status_active=1");

			$allocated_qnty=0;
			foreach($sql_allowcate as $row)
			{
				$allocated_qnty+=$row[csf("qnty")];
			}
		}

		echo "$('#hidden_allocation_qty').val('".$allocated_qnty."');\n";
		echo "set_button_status(1, permission, 'fnc_yarn_dyeing',1,0);\n";

		$check_issue=return_field_value( "issue_number", "inv_issue_master a, inv_transaction b","a.booking_id='".$row[csf("mst_id")]."' and b.prod_id='".$row[csf("product_id")]."' and a.id=b.mst_id and a.item_category=1 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","issue_number");

		if($check_issue!="")
		{
			echo " $('#txt_sam_booking_no,#txt_sample_id,#cbo_supplier_name,#txt_lot,#txt_yern_color,#cbo_color_range,#txt_ref_no,#cbo_sample_name,#txt_dyeing_charge').attr('disabled',true);\n";
		}
		else
		{
			echo " $('#txt_sam_booking_no,#txt_sample_id,#cbo_supplier_name,#txt_lot,#txt_yern_color,#cbo_color_range,#txt_ref_no,#cbo_sample_name,#txt_dyeing_charge').attr('disabled',false);\n";
		}

	}

	exit();
}


if ($action=="yern_dyeing_booking_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo "$company";die;
	?>

	<script>
		function js_set_value(id)
		{
			$("#hidden_sys_number").val(id);
			//$("#hidden_id").val(id);
			parent.emailwindow.hide();
		}
	var permission= '<? echo $permission; ?>';
	</script>
	</head>
	<body>
		<div align="center" style="width:930px;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="900" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
					<thead>
						<tr>
							<th colspan="8"><?=create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
						</tr>
						<tr>
							<th width="150">Buyer Name</th>
                            <th width="80">Pay Mode</th>
							<th width="170">Supplier Name</th>
							<th width="100">Booking No</th>
							<th width="100">IR/CN</th>
							<th width="130" colspan="2">Date Range</th>
							<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td><?=create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0); ?></td>
                            <td><?=create_drop_down( "cbo_pay_mode", 80, $pay_mode,"", 1, "-Pay Mode-", "", "load_drop_down( 'yarn_dyeing_wo_without_order_controller',$company+'_'+this.value, 'load_drop_down_supplier', 'supplier_td' )","" ); ?></td>
							<td id="supplier_td"><?=create_drop_down( "cbo_supplier_name", 140, $blank_array,"", 1, "-Select Supplier-", $selected, "",0 ); ?></td>
							<td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:90px"></td>
							<td><input name="txt_ir_ref" id="txt_ir_ref" class="text_boxes" style="width:100px"></td>
							<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" /></td>
							<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" /></td>
							<td>
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<?=$company; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_pay_mode').value+'_'+document.getElementById('txt_ir_ref').value, 'create_sys_search_list_view', 'search_div', 'yarn_dyeing_wo_without_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" valign="middle" colspan="7">
								<?=load_month_buttons(1);  ?>
								<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
								<input type="hidden" id="hidden_id" value="hidden_id" />
	                    	</td>
	                </tr>
	            </tbody>
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

if($action=="create_sys_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val=$ex_data[4];
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//echo $buyer_val;die;
 	//$sql_cond=""; LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
	//var_dump($order_no_arr);die;
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if($db_type==0)
	{
		$booking_year_cond=" and year(a.insert_date)=$ex_data[5]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[5]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";
	}
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";
	if($ex_data[8]!=0 ) $payModeCond="and a.pay_mode='$ex_data[8]'"; else  $payModeCond="";
	//if($ex_data[9]!="" ) $intRefCond="and d.internal_ref like '%$ex_data[9]%' "; else  $intRefCond="";


	if($ex_data[7]==4 || $ex_data[7]==0)
		{
			if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
		}
    if($ex_data[7]==1)
		{
			if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num ='$ex_data[6]' "; else $booking_cond="";
		}
   if($ex_data[7]==2)
		{
			if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
		}
	if($ex_data[7]==3)
		{
			if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]'  $booking_year_cond  "; else $booking_cond="";
		}
	//TO_CHAR(insert_date,'YYYY')
	/*$sql = "select id, yarn_dyeing_prefix_num, ydw_no, company_id, supplier_id, booking_date, delivery_date, currency, ecchange_rate, pay_mode,source, attention from  wo_yarn_dyeing_mst where  status_active=1 and is_deleted=0 $supplier $company $sql_cond";*/

	if($ex_data[9]!="")
	{
		$intRefCond="and c.internal_ref like '%$ex_data[9]%' "; 
		$smn_ir_no_sql= "SELECT a.id, a.booking_no, c.internal_ref from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b,sample_development_mst c where a.booking_no=b.booking_no and b.style_id=c.id and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.color_type_id in(2,3,4,6,32,33,44,47,48)  $intRefCond";
		//echo $smn_ir_no_sql;
		$smn_ir_no_rslt=sql_select( $smn_ir_no_sql );
		$bookingNosArr=[];
		$booking_nos_chk=[];
		foreach ($smn_ir_no_rslt as $row)
		{
			if($booking_nos_chk[$row[csf("booking_no")]] == "")
			{
				$booking_nos_chk[$row[csf("booking_no")]] = $row[csf("booking_no")];
				array_push($bookingNosArr,$row[csf("booking_no")]);
			}
			
		}
		if(!empty($bookingNosArr)){ $smbIntRefCond="".where_con_using_array($bookingNosArr,1,'b.booking_no')."";}
	}

	 if($db_type==0)
	 {
		 $sql = "SELECT a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year,group_concat(distinct b.job_no_id) as job_no_id,group_concat(distinct b.sample_name) as sample_name, group_concat(distinct b.booking_no) as booking_no, d.buyer_name, d.internal_ref
		 from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join   sample_development_mst d on  b.job_no_id=d.id
		 where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=42 and b.entry_form=42 $company $supplier $sql_cond $buyer_cond $payModeCond $booking_cond $smbIntRefCond group by a.id,d.internal_ref order by a.id DESC";
	 }
	 //LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
	 else if($db_type==2)
	 {
		 $sql = "SELECT  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id, LISTAGG(CAST(b.sample_name AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.sample_name) as sample_name, d.buyer_name, d.internal_ref,LISTAGG(CAST(b.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.booking_no) as booking_no
		 from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join   sample_development_mst d on  b.job_no_id=d.id
		 where a.id=b.mst_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=42 and b.entry_form=42 $company $supplier $sql_cond $buyer_cond $payModeCond $booking_cond $smbIntRefCond
		group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,d.buyer_name,d.internal_ref order by a.id DESC";
	 }
	//echo $sql;

	$nameArray=sql_select( $sql );

	$booking_no_arr='';
	foreach ($nameArray as $row) 
	{
		$booking_no_arr=array_unique(explode(",",$row[csf("booking_no")]));
	}
	//var_dump($booking_no_arr);
	
	if(!empty($booking_no_arr))
	{
		$bookingNoArr=[];
		$booking_no_chk=[];
		foreach ($booking_no_arr as $booking_no)
		{
			if($booking_no_chk[$booking_no] == "")
			{
				$booking_no_chk[$booking_no] = $booking_no;
				array_push($bookingNoArr,$booking_no);
			}
			
		}

		$smn_sql= "SELECT a.id, a.booking_no, c.internal_ref from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b,sample_development_mst c where a.booking_no=b.booking_no and b.style_id=c.id and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.color_type_id in(2,3,4,6,32,33,44,47,48) ".where_con_using_array($bookingNoArr,1,'a.booking_no')." ";
		//echo $smn_sql; 
		$smn_rslt=sql_select($smn_sql);
		$smn_info_arr=[];
		foreach ($smn_rslt as $row) 
		{
			$smn_info_arr[$row[csf('booking_no')]] = $row[csf('internal_ref')];
		}
	}

	?>
	<div style="width:930px; "  align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="50">Booking no</th>
                <th width="70">IR/CN</th>
                <th width="40">Year</th>
                <th width="120">Sample Develop Id</th>
                <th width="220">Sample Name</th>
                <th width="100">Buyer Name</th>
                <th width="120">Supplier Name</th>
                <th width="70">Booking Date</th>
                <th >Delevary Date</th>
            </thead>
        </table>
        <div style="width:930px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
			
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult)
				{
					$sample_name=array_unique(explode(",",$selectResult[csf("sample_name")]));
					$booking_nos_arr=array_unique(explode(",",$selectResult[csf("booking_no")]));
					$sample_develop_id=implode(",",array_unique(explode(",",$selectResult[csf("job_no_id")])));
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$supplierName="";
					if($selectResult[csf("pay_mode")]==3 || $selectResult[csf("pay_mode")]==5) $supplierName=$company_library[$selectResult[csf('supplier_id')]]; else $supplierName=$supplier_arr[$selectResult[csf('supplier_id')]];
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'); ">

                     <td width="30" align="center"> <p><? echo $i; ?></p></td>
                      <td width="50" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
                      <td width="70" align="center"><p> 
						<? 
						$sample_ir_no="";
						foreach($booking_nos_arr as $val)
						{
							if($sample_ir_no=="") $sample_ir_no=$smn_info_arr[$val]; else $sample_ir_no=$sample_ir_no.",".$smn_info_arr[$val] ;
						}
						echo  $sample_ir_no;

						?></p>
					</td>
                      <td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>
                      <td width="120"><p>
					  <?
					  	echo $sample_develop_id;
					  ?>
                      </p></td>
                      <td width="220" style="word-break:break-all">
					  <?
					  $sample_name_group="";
					  $sample_arr;
					  foreach($sample_name as $val)
					  {
						  if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val] ;
					  }
					  echo  $sample_name_group;
					  	//$po_no=implode(",",array_unique(explode(",",$order_no_arr[$job_no_id]))); echo $po_no;
					  ?>
                      </p></td>
                      <td width="100"><p> <? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>
                      <td width="120" style="word-break:break-all"><?=$supplierName; ?></td>
                      <td width="70"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>
                      <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
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

if($action=="populate_master_from_data")
{
	$sql="select  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention, a.ready_to_approved, a.is_approved, a.tenor from wo_yarn_dyeing_mst a where a.entry_form=42 and a.ydw_no='".$data."'";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_booking_no').val('".$row[csf("ydw_no")]."');\n";
		echo "$('#cbo_company_name').val('".$row[csf("company_id")]."');\n";
		$pay_mode= $row[csf("pay_mode")];
		$string="'".$row[csf("company_id")]."_".$row[csf("pay_mode")]."'";
		//echo $string;die;
		echo "$('#cbo_pay_mode').val('".$row[csf("pay_mode")]."');\n";
		echo "load_drop_down( 'requires/yarn_dyeing_wo_without_order_controller',".$string.", 'load_drop_down_supplier', 'supplier_td' );\n";
		//echo "$('#hidden_type').val(".$row[csf("piworeq_type")].");\n";
		echo "$('#cbo_supplier_name').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#txt_booking_date').val('".change_date_format($row[csf("booking_date")])."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		//echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		echo "$('#cbo_currency').val('".$row[csf("currency")]."');\n";
		echo "$('#txt_exchange_rate').val('".$row[csf("ecchange_rate")]."');\n";

		echo "$('#txt_tenor').val('".$row[csf("tenor")]."');\n";
		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		echo "$('#cbo_source').val('".$row[csf("source")]."');\n";
		echo "$('#txt_delivery_end').val('".change_date_format($row[csf("delivery_date_end")])."');\n";
		echo "$('#dy_delevery_start').val('".change_date_format($row[csf("dy_delivery_date_start")])."');\n";
		echo "$('#dy_delevery_end').val('".change_date_format($row[csf("dy_delivery_date_end")])."');\n";
		echo "$('#cbo_ready_to_approved').val(".$row[csf("ready_to_approved")].");\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		//right side list view
		//echo "show_list_view(".$row[csf("piworeq_type")]."+'**'+".$row[csf("pi_wo_req_id")].",'show_product_listview','list_product_container','requires/get_out_entry_controller','');\n";
		if($row[csf("is_approved")] == 1){
	        echo "$('#approved').html('Approved');\n";
			echo " $('#cbo_company_name').attr('disabled',true);\n";
			echo " $('#cbo_pay_mode').attr('disabled',true);\n";
			echo " $('#cbo_currency').attr('disabled',true);\n";
			echo " $('#txt_wo_qty').attr('disabled',true);\n";
			echo " $('#id_approved_id').val('1');\n";
			//echo " $('#cbo_supplier_name').attr('disabled',true);\n";
	    }elseif($row[csf("is_approved")] == 3){
	        echo "$('#approved').html('Partial Approved');\n";
	    }else{
	        echo "$('#approved').html('');\n";
	    }
	}
	exit();
}
if($action=="dyeing_search_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	?>
<script>
	function js_set_value(str)
	{
		$("#hidden_rate").val(str);
		parent.emailwindow.hide();
	}
</script>
</head>
<body>
    <div align="center" style="width:590px;" >
        <fieldset>
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="570" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th width="40">Sl No.</th>
                            <th width="170">Const. Compo.</th>
                            <th width="100">Process Name</th>
                            <th width="100">Color</th>
                            <th width="90">Rate</th>
                            <th>UOM</th>
                        </tr>
                    </thead>
                </table>
                <?
				$sql="select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where comapny_id=$company and process_id=30 and status_active=1";
				//echo $sql;
				$sql_result=sql_select($sql);
				?>
            	<div style="width:570px; overflow-y:scroll; max-height:240px;font-size:12px; overflow-x:hidden; cursor:pointer;">
                    <table width="570" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" id="table_charge">
                        <tbody>
                        <?
						$i=1;
						foreach($sql_result as $row)
						{
							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							?>
                            <tr bgcolor="<?  echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("in_house_rate")]; ?>)">
                                <td width="40" align="center"><? echo $i;  ?></td>
                                <td width="170"><? echo $row[csf("const_comp")]; ?></td>
                                <td width="100" align="center"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
                                <td width="100"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                                <td width="90" align="right"><? echo number_format($row[csf("in_house_rate")],2); ?></td>
                                <td><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
                            </tr>
                            <?
							$i++;
						}
						?>
                          <input type="hidden" id="hidden_rate" />
                        </tbody>
                    </table>
                </div>
            </form>
        </fieldset>
    </div>
</body>
<script  type="text/javascript">setFilterGrid("table_charge",-1)</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

    <?
	exit();
}
if($action=="lot_search_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$job_no=str_replace("'","",$job_no);
	$txt_sam_booking_no=str_replace("'","",$txt_sam_booking_no);
	//echo $job_no;die;
		?>
	<script>
		function js_set_value2(str)
		{
			$("#hidden_product").val(str);
			parent.emailwindow.hide();
		}
	</script>

	</head>
	<body>
		<div style="width:595px;" align="center" >
			<fieldset>
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>

                    <tr>
                        <th width="150">Lot</th>
                         <th width="">&nbsp;</th>
                     </tr>
                    </thead>
        			<tr>
                    <td>
                    <input name="txt_lot_search" id="txt_lot_search" class="text_boxes" style="width:150px" placeholder="Write">
                    </td>
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company ?>+'_'+document.getElementById('txt_lot_search').value+'_'+'<? echo $txt_sam_booking_no ?>', 'create_lot_search_list_view', 'search_div', 'yarn_dyeing_wo_without_order_controller', 'setFilterGrid(\'table_charge\',-1)')" style="width:100px;" /></td>



             </table>
             <br>
             <table  width="595"  align="center">
             <tr>
            	<td align="center" valign="top" id="search_div">
            </td>
        	</tr>
             </table>
				</form>
			</fieldset>
		</div>
	</body>
	<!--<script  type="text/javascript">setFilterGrid("table_charge",-1)</script>  -->
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	//}
}

if($action=="create_lot_search_list_view")
{

	//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//extract($_REQUEST);
	$data=explode('_',$data);
	$company=str_replace("'","",$data[0]);
	$lot_search=str_replace("'","",$data[1]);
	$bookingNo=str_replace("'","",$data[2]);
	//echo $bookingNo;
	$brand_arr=return_library_array( "Select id, brand_name from  lib_brand where  status_active=1",'id','brand_name');

	if($lot_search!='') {
		$lot_cond="and lot ='$lot_search'";
		$lot_cond2="and c.lot ='$lot_search'";
	}
	else  { $lot_cond="";
		 $lot_cond2="";
	}
	if($company!='') $com_cond="and company_id =$company";else  $com_cond="";
	if($company!='') $com_cond2="and c.company_id =$company";else  $com_cond2="";
	if($bookingNo!='') $booking_cond="and a.booking_no ='$bookingNo'";else  $booking_cond="";

	$variable_set_allocation = return_field_value("smn_allocation", "variable_settings_inventory", "company_name=$company and variable_list=18 and item_category_id = 1","smn_allocation");
	?>

	</head>
	<body>
		<div style="width:860px;" >
			<fieldset>
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table width="860" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center"  rules="all">
						<thead>
							<tr>
								<th width="30">Sl No.</th>
								<th width="100">Lot No</th>
								<th width="90">Brand</th>
								<th width="200">Product Name Details</th>
								<th width="80">Stock</th>
								<th width="80">Allocated to Order</th>
								<th width="80">Un Allocated Qty.</th>
								<th width="60">Age (Days)</th>
								<th width="60">DOH</th>
								<th>UOM</th>
							</tr>
						</thead>
					</table>
					<?
					$date_array = array();
					$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
					$result_returnRes_date = sql_select($returnRes_date);
					foreach ($result_returnRes_date as $row) {
						$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
						$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
					}


				if($variable_set_allocation==1)
				{
				  $sql="select b.qnty as allocation_qty,c.id,c.product_name_details,c.allocated_qnty,c.available_qnty,c.lot,c.item_code,c.unit_of_measure,c.yarn_count_id,c.brand,current_stock,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.color from inv_material_allocation_mst a,inv_material_allocation_dtls b,product_details_master c where a.id=b.mst_id and c.id=b.item_id and c.id=a.item_id and c.item_category_id=1 and a.status_active=1 and b.status_active=1  $com_cond2 $lot_cond2 $booking_cond";
				}
				else
				{
					$sql="select id,product_name_details,allocated_qnty,available_qnty,lot,item_code,unit_of_measure,yarn_count_id,brand,current_stock,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color from product_details_master where item_category_id=1 $com_cond $lot_cond";
				}

					$sql_result=sql_select($sql);
					?>
					<div style="width:860px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;">
						<table width="860" cellspacing="0" cellpadding="0" border="0" class="rpt_table"  style="cursor:pointer" rules="all" id="table_charge">
							<tbody>
								<?
								$i=1;
								foreach($sql_result as $row)
								{
									if($row[csf('current_stock')]>0)
									{
										if ($i%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";

										$ageOfDays  = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
										$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));


										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value2('<? echo $composition[$row[csf("yarn_comp_type1st")]]." ". $row[csf("yarn_comp_percent1st")]." ". $composition[$row[csf("yarn_comp_type2nd")]]." ". $row[csf("yarn_comp_percent2nd")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $color_arr[$row[csf("color")]]; ?>*<? echo $row[csf("yarn_count_id")]; ?>*<? echo $row[csf("lot")]; ?>*<? echo $row[csf("id")]; ?>*<? echo $row[csf("allocation_qty")]; ?>')">
											<td width="30" align="center"><p><? echo $i;  ?></p></td>
											<td width="100" align="center"><p><? echo $row[csf("lot")]; ?></p></td>
											<td width="90"><p><? echo $brand_arr[$row[csf("brand")]]; ?></p></td>
											<td width="200"><? echo $row[csf("product_name_details")]; ?></p></td>
											<td width="80" align="right"><p><? echo $row[csf("current_stock")]; ?></p></td>
											<td width="80" align="right"><p><? echo $row[csf("allocated_qnty")]; ?></p></td>
											<td width="80" align="right"><p><? echo $row[csf("available_qnty")]; ?></p></td>
											<td width="60" align="right"><p><? echo $ageOfDays; ?></p></td>
											<td width="60" align="right"><p><? echo $daysOnHand; ?></p></td>

											<td><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
										</tr>
										<?
										$i++;
									}
								}
								?>
								<input type="hidden" id="hidden_product" style="width:100px;" />
							</tbody>
						</table>
					</div>
				</form>
			</fieldset>
		</div>
	</body>

	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
<?
}
if($action=="lot_search_popup23")//Old
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$brand_arr=return_library_array( "Select id, brand_name from  lib_brand where  status_active=1",'id','brand_name');
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$job_no=str_replace("'","",$job_no);
	//echo $job_no;die;
	?>
		<script>
			function js_set_value(str)
			{
				$("#hidden_product").val(str);
				parent.emailwindow.hide();
			}
		</script>
	</head>
	<body>
		<div style="width:595px;" >
			<fieldset>
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table width="595" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
						<thead>
							<tr>
								<th width="40">Sl No.</th>
								<th width="100">Lot No</th>
								<th width="90">Brand</th>
								<th width="200">Product Name Details</th>
								<th width="80">Stock</th>
								<th>UOM</th>
							</tr>
						</thead>
					</table>
					<?

					$sql="select id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,brand,current_stock,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color from product_details_master where company_id='$company' and item_category_id=1";

						//echo $sql;
					$sql_result=sql_select($sql);
					?>
					<div style="width:595px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;">
						<table width="595" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="table_charge" style="cursor:pointer" rules="all">
							<tbody>
								<?
								$i=1;
								foreach($sql_result as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $composition[$row[csf("yarn_comp_type1st")]]." ". $row[csf("yarn_comp_percent1st")]." ". $composition[$row[csf("yarn_comp_type2nd")]]." ". $row[csf("yarn_comp_percent2nd")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $color_arr[$row[csf("color")]]; ?>,<? echo $row[csf("yarn_count_id")]; ?>,<? echo $row[csf("lot")]; ?>,<? echo $row[csf("id")]; ?>')">
										<td width="40" align="center"><p><? echo $i;  ?></p></td>
										<td width="100" align="center"><p><? echo $row[csf("lot")]; ?></p></td>
										<td width="90"><p><? echo $brand_arr[$row[csf("brand")]]; ?></p></td>
										<td width="200">v<? echo $row[csf("product_name_details")]; ?></p></td>
										<td width="80" align="right"><p><? echo $row[csf("current_stock")]; ?></p></td>
										<td><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
									</tr>
									<?
									$i++;
								}
								?>
								<input type="hidden" id="hidden_product" style="width:200px;" />
							</tbody>
						</table>
					</div>
				</form>
			</fieldset>
		</div>
	</body>
	<script  type="text/javascript">setFilterGrid("table_charge",-1)</script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
<?
	//}

exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function add_break_down_tr(i)
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;

				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name + i },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_termcondi_details");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#termscondition_'+i).val("");
				$( "#tbl_termcondi_details tr:last" ).find( "td:first" ).html(i);
			}

		}

		function fn_deletebreak_down_tr(rowNo)
		{

			var numRow = $('table#tbl_termcondi_details tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}

		}

		function fnc_fabric_booking_terms_condition( operation )
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{

				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}

				data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../");
			//alert(data_all);
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
	//	alert(data);
		//freeze_window(operation);
		http.open("POST","yarn_dyeing_wo_without_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}
	function fnc_fabric_booking_terms_condition_reponse()
	{
		if(http.readyState == 4)
		{
	   // alert(http.responseText);
	   var reponse=trim(http.responseText).split('**');
	   if (reponse[0].length>2) reponse[0]=10;
	   if(reponse[0]==0 || reponse[0]==1)
	   {
				//$('#txt_terms_condision_book_con').val(reponse[1]);
				parent.emailwindow.hide();
				set_button_status(1, permission, 'fnc_fabric_booking_terms_condition',1,1);
			}
		}
	}
</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<fieldset>
			<form id="termscondi_1" autocomplete="off">
				<input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>" class="text_boxes" readonly />
				<input type="hidden" id="txt_terms_condision_book_con" name="txt_terms_condision_book_con" >

				<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
					<thead>
						<tr>
							<th width="50">Sl</th><th width="530">Terms</th><th ></th>
						</tr>
					</thead>
					<tbody>
						<?
					//echo "select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no";
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
							<tr id="settr_1" align="center">
								<td>
									<? echo $i;?>
								</td>
								<td>
									<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" />
								</td>
								<td>
									<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
									<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
								</td>
							</tr>
							<?
						}
					}
					else
					{

					$data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
					{
						$i++;
						?>
						<tr id="settr_1" align="center">
							<td>
								<? echo $i;?>
							</td>
							<td>
								<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" />
							</td>
							<td>
								<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
								<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
							</td>
						</tr>
						<?
					}
				}
				?>
			</tbody>
		</table>

		<table width="650" cellspacing="0" class="" border="0">
			<tr>
				<td align="center" height="15" width="100%"> </td>
			</tr>
			<tr>
				<td align="center" width="100%" class="button_container">
					<?
					echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
					?>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

exit();
}

if($action=="save_update_delete_fabric_booking_terms_condition")
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

		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		$field_array="id,booking_no,terms";
		for ($i=1;$i<=$total_row;$i++)
		{
			$termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		}
		 	//echo "INSERT INTO wo_booking_terms_condition (".$field_array.") VALUES ".$data_array;die;
		//echo "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."";
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);
		//echo $rID_de3;
		//print_r();

		$rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		// check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$txt_booking_no;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$txt_booking_no;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3)
			{
				oci_commit($con);
				echo "0**".$txt_booking_no;
			}

		}
		else{
			oci_rollback($con);
			echo "10**".$txt_booking_no;
		}
		disconnect($con);
		die;
	}
}

if($action=="show_with_multiple_job")
{
	//echo "xxxx";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);

	$sql="select id from electronic_approval_setup where company_id=$cbo_company_name and page_id in(1270,444) and is_deleted=0";
	$res_result_arr = sql_select($sql);
	$approval_arr=array();
	foreach($res_result_arr as $row){
		$approval_arr[$row["ID"]]["ID"]=$row["ID"];
	}

	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:900px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="750">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php echo $company_library[$cbo_company_name]; ?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>
									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong><? echo $report_title; ?> </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id";
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.is_approved from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$is_approved=$result[csf('is_approved')];
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];

		}
		$varcode_work_order_no=$work_order;

		?>
		<table width="900" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<?
							if($pay_mode==3 || $pay_mode==5)
			            	{
			            		echo $company_library[$new_supplier_name];
			            	}
			            	else
			            	{
			            		echo $supplier_name[$new_supplier_name];
			            	}



							// echo $supplier_arr[$supplier_id];

							 ?></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="200"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>

	<table width="1080" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
		<thead>
			<tr>
				<th width="30" align="center"><strong>Sl</strong></th>
				<th width="65" align="center"><strong>Color</strong></th>
				<th width="80" align="center"><strong>Color Range</strong></th>
				<th  align="center" width="50"><strong>Ref No.</strong></th>
				<th  align="center" width="70"><strong>Style Ref.No.</strong></th>
				<th width="30" align="center"><strong>Yarn Count</strong></th>
				<th width="140" align="center"><strong>Yarn Description</strong></th>
				<th width="60" align="center"><strong>Brand</strong></th>
				<th width="50" align="center"><strong>Lot</strong></th>
				<th width="60" align="center"><strong>WO Qty</strong></th>
				<th width="50" align="center"><strong>Dyeing Rate</strong></th>
				<th width="80" align="center"><strong>Amount</strong></th>
				<th  align="center" width="50"><strong>Min Req. Cone</strong></th>
				<th  align="center" width="80"><strong>Sample Develop Id</strong></th>
				<th  align="center" width="80"><strong>Buyer</strong></th>
				<th  align="center" ><strong>Sample Name</strong></th>
			</tr>
		</thead>
		<?
		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
		foreach($sql_brand as $row_barand)
		{
			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
		}
		$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
		$style_ref_sample=return_library_array( "select id, style_ref_no from  sample_development_mst",'id','style_ref_no');

		if($db_type==0)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id,b.sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,avg(b.dyeing_charge) as dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.yarn_color, b.color_range,b.product_id, b.count, b.yarn_description
			order by b.yarn_color";
		}
		else if($db_type==2)
		{
			$sql="select
			b.product_id,
			listagg(CAST(b.job_no_id AS VARCHAR(4000)),',')  within group (order by b.job_no_id) as job_no_id,
			listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name,
			b.yarn_color,
			b.yarn_description,
			b.count,
			b.color_range,
			sum(b.yarn_wo_qty) as yarn_wo_qty,
			avg(b.dyeing_charge) as dyeing_charge,
			sum(b.amount) as amount,
			listagg(CAST(b.referance_no AS VARCHAR(4000)),',')  within group (order by b.referance_no) as referance_no,
			listagg(CAST(b.min_require_cone AS VARCHAR(4000)),',')  within group (order by b.min_require_cone) as min_require_cone


			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.yarn_color, b.color_range,b.product_id, b.count, b.yarn_description
			order by b.yarn_color";
		}

		 // group by b.job_no_id, b.yarn_color, b.color_range,a.id,b.product_id,b.job_no,b.job_no_id,b.yarn_color,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no, a.ydw_no,b.id

			   //echo $sql;die;
		$sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
		foreach($sql_result as $row)
		{
			$product_id=$row[csf("product_id")];
			if($row[csf("product_id")]!="")
			{
				$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
				$brand=$product_lot[$row[csf("product_id")]]['brand'];
			}

			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
				<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
				<td align="center"><? echo $row[csf("referance_no")]; ?></td>
				<td align="center">
					<?
					$coma='';
					foreach(array_unique(explode(",",$row[csf("job_no_id")])) as $job_no_id){
						echo $style_ref_sample[$job_no_id].$coma;
						$coma=',';
					}
					?>
				</td>
				<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
				<td>
					<?
					echo $row[csf("yarn_description")];
					?>
				</td>
				<td><? echo $brand_arr[$brand]; ?></td>
				<td align="center"><? echo $lot_amt; ?></td>
				<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
				<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
				<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?> &nbsp;</td>
				<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
				<td align="center"><? echo $row[csf("job_no_id")]; ?></td>
				<td align="center"><? echo $buyer_arr[$buyer_sample[$row[csf("job_no_id")]]]; ?> </td>
				<td align="center">
					<?
					$sample_name_arr=array_unique(explode(",",$row[csf('sample_name')]));
					$sample_name_group="";
					foreach($sample_name_arr as $val)
					{
						if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val];
					}
					echo $sample_name_group;
					?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<tr>
				<th colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</th>
				<th align="right" ><b><? echo $total_qty; ?></b></th>
				<th align="right">&nbsp;</th>
				<th align="right"><b><? echo number_format($total_amount,2); ?></b></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>

		<?
		$mcurrency="";
		$dcurrency="";
		if($currency_id==1)
		{
			$mcurrency='Taka';
			$dcurrency='Paisa';
		}
		if($currency_id==2)
		{
			$mcurrency='USD';
			$dcurrency='CENTS';
		}
		if($currency_id==3)
		{
			$mcurrency='EURO';
			$dcurrency='CENTS';
		}
		?>
		<tr>
			<td colspan="16" align="center">Total Dyeing Amount (in word): &nbsp;<? echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency); //echo number_to_words($total_amount,"USD", "CENTS");?> </td>
		</tr>
	</table>


	<!--==============================================AS PER GMTS COLOR START=========================================  -->
	<table width="1080" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

	<? echo get_spacial_instruction($txt_booking_no,'1080px',42); ?>

</div>
<div>
<br>
	<table width="780" align="center">
			<tr>
				<div style="text-align:center;font-size:xx-large; font-style:italic; margin-top:20px; color:#FF0000;">
						<?
						if(count($approval_arr)>0)
						{
							if($is_approved == 0){echo "Draft";}else{}
						}
						?>
				</div>
			</tr>
	</table>
<br>
	<?
	echo signature_table(43, $cbo_company_name, "1080px"."",1);
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
	?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
</script>
<?
exit();
}

if($action=="show_with_multiple_job_without_rate")
{

	//echo "uuuu";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);

	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="750">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>
									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong><? echo $report_title;?> </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">
				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
		}
		$varcode_work_order_no=$work_order;

		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<?
							if($pay_mode==3 || $pay_mode==5)
			            	{
			            		echo $company_library[$new_supplier_name];
			            	}
			            	else
			            	{
			            		echo $supplier_name[$new_supplier_name];
			            	}

							//echo $supplier_arr[$supplier_id];
							?></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="250"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>

	<table width="950" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
		<thead>
			<tr>
				<th width="30" align="center"><strong>Sl</strong></th>
				<th width="65" align="center"><strong>Color</strong></th>
				<th width="80" align="center"><strong>Color Range</strong></th>
				<th  align="center" width="50"><strong>Ref No.</strong></th>
				<th  align="center" width="70"><strong>Style Ref.No.</strong></th>
				<th width="30" align="center"><strong>Yarn Count</strong></th>
				<th width="140" align="center"><strong>Yarn Description</strong></th>
				<th width="60" align="center"><strong>Brand</strong></th>
				<th width="50" align="center"><strong>Lot</strong></th>
				<th width="60" align="center"><strong>WO Qty</strong></th>
				<th  align="center" width="50"><strong>Min Req. Cone</strong></th>
				<th  align="center" width="80"><strong>Sample Develop Id</strong></th>
				<th  align="center" width="80"><strong>Buyer</strong></th>
				<th  align="center" ><strong>Sample Name</strong></th>
			</tr>
		</thead>

		<?
		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
		foreach($sql_brand as $row_barand)
		{
			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
		}
		$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
		$style_ref_sample=return_library_array( "select id, style_ref_no from  sample_development_mst",'id','style_ref_no');

		if($db_type==0)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id,b.sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.count, b.job_no_id, b.yarn_color, b.color_range
			order by b.id";
		}
		else if($db_type==2)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id, listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.job_no_id, b.yarn_color, b.color_range,a.id,b.product_id,b.job_no,b.job_no_id,b.yarn_color,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no, a.ydw_no,b.id
			order by b.id";
		}

		$sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
		foreach($sql_result as $row)
		{
			$product_id=$row[csf("product_id")];
				//var_dump($product_id);
			if($product_id)
			{
				$sql_brand=sql_select("select lot,brand from product_details_master where id in($product_id)");
				foreach($sql_brand as $row_barand)
				{
					$lot_amt=$row_barand[csf("lot")];
					$brand=$row_barand[csf("brand")];
				}

			}

				//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);
			$all_style_arr[]=$style_ref_sample[$row[csf("job_no_id")]];
			$all_job_arr[]=$row[csf("job_no")];

			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
				<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
				<td align="center"><? echo $row[csf("referance_no")]; ?></td>
				<td align="center">
					<?
					echo $style_ref_sample[$row[csf("job_no_id")]];
					?>
				</td>
				<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
				<td>
					<?
					echo $row[csf("yarn_description")];
					?>
				</td>
				<td><? echo $brand_arr[$brand]; ?></td>
				<td align="center"><? echo $lot_amt; ?></td>
				<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
				<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
				<td align="center"><? echo $row[csf("job_no_id")]; ?></td>
				<td align="center"><? echo $buyer_arr[$buyer_sample[$row[csf("job_no_id")]]]; ?> </td>
				<td align="center">
					<?
					$sample_name_arr=array_unique(explode(",",$row[csf('sample_name')]));
					$sample_name_group="";
					foreach($sample_name_arr as $val)
					{
						if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val];
					}
					echo $sample_name_group;
					?>
				</td>
			</tr>
			<?
			$i++;
			$yarn_count_des="";
			$style_no="";
		}
		?>
		<tfoot>
			<tr>
				<th colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</th>
				<th align="right" ><b><? echo $total_qty; ?></b></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>

	</table>


	<!--==============================================AS PER GMTS COLOR START=========================================  -->
	<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

		<? echo get_spacial_instruction($txt_booking_no,'950px',42); ?>


</div>
<div>
	<?
	echo signature_table(43, $cbo_company_name, "950px");
	echo "****".custom_file_name($txt_booking_no,implode(',',$all_style_arr),implode(',',$all_job_arr));
	?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
</script>
<?
exit();
}



if($action=="show_without_rate_booking_report")
{
	//echo "uuuu";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode_id = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);

	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");

	$merchant_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name",'id','team_member_name');
	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong><? echo $report_title;?> </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
		}

		$varcode_work_order_no=$work_order;
		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<?
							if($pay_mode_id==3 || $pay_mode_id==5)
			            	{
			            		echo $company_library[$new_supplier_name];
			            	}
			            	else
			            	{
			            		echo $supplier_name[$new_supplier_name];
			            	}
							//echo $supplier_arr[$supplier_id];

							 ?></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="250"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>

	<?
	$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
	$sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id
	from
	wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
	where
	a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
	$sql_result=sql_select($sql);$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name='';
	foreach($sql_result as $row)
	{
		if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

		if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];

		if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];

		if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

	}

	$total_samp_deve_id = implode(",",array_filter(array_unique(explode(",", $total_samp_deve_id))));
	if($total_samp_deve_id)
	{
		$deal_mercha_res = sql_select("select dealing_marchant from sample_development_mst where id in ($total_samp_deve_id) and status_active=1 and is_deleted=0");
		foreach ($deal_mercha_res as $val)
		{
			$dealing_marchants .= $merchant_arr[$val[csf("dealing_marchant")]].",";
		}
	}
	$dealing_marchants = implode(",",array_filter(array_unique(explode(",", chop($dealing_marchants,",")))));


	?>

	<table width="950" style="" align="center">
		<tr  style="font-size:12px">
			<td width="150"><b>Sample Id</b></td>
			<td width="800" valign="top">:&nbsp;
				<?
				echo $total_samp_deve_id;
				?>
			</td>
		</tr>

		<tr style="font-size:12px">
			<td valign="top"><b>Sample Name</b></td>
			<td valign="top">: &nbsp;
				<?
				$total_sample_name_arr=array_unique(explode(",",$total_sample_name));

				$all_order=explode(",",$total_order_no);
				$sample="";
				foreach($total_sample_name_arr as $row)
				{
					if($sample=="") $sample=$sample_arr[$row]; else $sample=$sample.",".$sample_arr[$row];
				}
				echo $sample;
				?>
			</td>
		</tr>
		<tr style="font-size:12px">
			<td valign="top"><b>Buyer </b> </td>
			<td valign="top">:&nbsp;
				<?
				$total_buyer_array=array_unique(explode(",",$total_buyer));
				$view_buyer="";
				foreach($total_buyer_array as $row)
				{
					if($view_buyer=="") $view_buyer=$buyer_arr[$row]; else $view_buyer=$view_buyer.",".$buyer_arr[$row];
				}
				echo $view_buyer;
				?>
			</td>
		</tr >
		<tr style="font-size:12px">
			<td valign="top"><b> Dealing Merchant </b> </td>
			<td valign="top">:&nbsp;
				<?

				echo $dealing_marchants;
				?>
			</td>
		</tr >
	</table>



	<table width="950" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
		<thead>
			<tr>
				<th width="40" align="center"><strong>Sl</strong></th>
				<th width="70" align="center"><strong>Color</strong></th>
				<th width="85" align="center"><strong>Color Range</strong></th>
				<th  align="center" width="70"><strong>Ref NO.</strong></th>
				<th width="40" align="center"><strong>Yarn Count</strong></th>
				<th width="190" align="center"><strong>Yarn Description</strong></th>
				<th width="70" align="center"><strong>Brand</strong></th>
				<th width="65" align="center"><strong>Lot</strong></th>
				<th width="70" align="center"><strong>WO Qty</strong></th>
				<th  align="center" width="65" ><strong>Min Req. Cone</strong></th>
				<th  align="center" ><strong>Remarks</strong></th>
			</tr>
		</thead>
		<?

		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
		foreach($sql_brand as $row_barand)
		{
			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
		}
		if($db_type==0) {
			$sql_color="select
			product_id,
			yarn_color,
			yarn_description,
			count,
			color_range,
			sum(yarn_wo_qty) as yarn_wo_qty,
			sum(yarn_wo_qty) as yarn_wo_qty,
			sum(dyeing_charge) as dyeing_charge,
			sum(amount) as amount,
			min_require_cone,
			referance_no,
			remarks
			from
			wo_yarn_dyeing_dtls
			where
			status_active=1 and id in($total_dtls_id) group by yarn_color, color_range,id,product_id,count,yarn_description order by yarn_color";
		}
		else if($db_type==2){
			$sql_color="select
			product_id,
			yarn_color,
			yarn_description,
			count,
			color_range,
			sum(yarn_wo_qty) as yarn_wo_qty,
			sum(dyeing_charge) as dyeing_charge,
			sum(amount) as amount,

			listagg(CAST(min_require_cone AS VARCHAR(4000)),',')  within group (order by min_require_cone) as min_require_cone,
			listagg(CAST(referance_no AS VARCHAR(4000)),',')  within group (order by referance_no) as referance_no,
			listagg(CAST(remarks AS VARCHAR(4000)),',')  within group (order by remarks) as remarks
			from
			wo_yarn_dyeing_dtls
			where
			status_active=1 and id in($total_dtls_id) group by yarn_color, color_range,id,product_id,count,yarn_description order by yarn_color";
		}
			 // echo $sql_color;


		$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
		foreach($sql_result as $row)
		{
			$product_id=$row[csf("product_id")];
			if($row[csf("product_id")]!="")
			{
				$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
				$brand=$product_lot[$row[csf("product_id")]]['brand'];
			}

			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
				<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
				<td align="center"><? echo $row[csf("referance_no")]; ?></td>
				<td align="center"><? echo $count_arr[$row[csf("count")]]; ?></td>
				<td><? echo $row[csf("yarn_description")]; ?></td>
				<td><? echo $brand_arr[$brand]; ?></td>
				<td align="center"><? echo $lot_amt; ?></td>
				<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
				<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
				<td align="center"><? echo $row[csf("remarks")]; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<tr>
				<th colspan="8" align="right"><strong>Total:</strong>&nbsp;&nbsp;</th>
				<th align="right" ><b><? echo $total_qty; ?></b></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>

	</table>


	<!--==============================================AS PER GMTS COLOR START=========================================  -->
	<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

		<? echo get_spacial_instruction($txt_booking_no,'950px',42); ?>


</div>
<div>
	<?
	//approved status
	/*$data_array_approve=sql_select("SELECT b.approved_by,b.approved_no, b.approved_date, c.user_full_name, c.designation, b.un_approved_by from inv_purchase_requisition_mst a, approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and b.entry_form=1 and a.id='$data[1]' order by b.id asc");*/

	$approved_sql=sql_select("SELECT  mst_id, approved_by,sequence_no ,min(approved_date) as approved_date from approval_history where entry_form=30 AND  mst_id ='$update_id' group by mst_id, approved_by,sequence_no order by sequence_no");

	$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date  from approval_history where entry_form=30 AND  mst_id ='$update_id' order by approved_date");


	foreach ($approved_his_sql as $key => $row)
	{
		$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
		if ($row[csf('un_approved_date')]!='')
		{
			$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
			$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
		}
	}
	/*echo "<pre>";
	print_r($array_data);//*/
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

	if(count($approved_sql) > 0)
	{
		$sl=1;
		?>
		<div style="margin-top:15px">
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b>Yarn Dyeing Work Order Without Order Aprroval Status </b></label>
				<thead>
					<tr style="font-weight:bold">
						<th width="20">SL</th>
						<th width="250">Name</th>
						<th width="200">Designation</th>
						<th width="100">Approval Date</th>
					</tr>
				</thead>
				<? foreach ($approved_sql as $key => $value)
				{
					?>
					<tr>
						<td width="20"><? echo $sl; ?></td>
						<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
						<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
						<td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
					</tr>
					<?
					$sl++;
				}
				?>
			</table>
		</div>
		<?
	}
	?>
	<? if(count($approved_his_sql) > 0)
	{
		$sl=1;
		?>
		<div style="margin-top:15px">
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b>Yarn Dyeing Work Order Without Order Approval / Un-Approval History </b></label>
				<thead>
					<tr style="font-weight:bold">
						<th width="20">SLcc</th>
						<th width="150">Approved / Un-Approved</th>
						<th width="150">Designation</th>
						<th width="50">Approval Status</th>
						<th width="150">Reason for Un-Approval</th>
						<th width="150">Date</th>
					</tr>
				</thead>
				<? foreach ($array_data as $approved_by => $data_value)
				{
					foreach ($data_value as $date => $value)
					{
						if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
							<td width="20"><? echo $sl; ?></td>
							<td width="150"><? echo $user_lib_name[$approved_by]; ?></td>
							<td width="150"><? echo $designation_lib[$user_lib_desg[$approved_by]]; ?></td>
							<td width="50"><? echo empty($value["un_approved_date"]) ? "Yes" : "No";  ?></td>
							<td width="150"><? echo $unapproved_request_arr[$value["mst_id"]] ?></td>
							<td width="150"><? echo $date; ?></td>
						</tr>
						<?
						$sl++;
					}
				}
				?>
			</table>
		</div>
		<?
	}


	echo signature_table(151, $cbo_company_name, "950px");
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
	?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
</script>
<?
exit();
}

if($action=="show_print_booking_report")
{
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$path=str_replace("'","",$path);
	if($path==1) $path="../../";
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$image_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode_id = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);


	$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.pay_mode,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.ecchange_rate, a.is_short from wo_yarn_dyeing_mst a where a.id=$update_id");
	foreach ($nameArray as $result)
	{
		$work_order=$result[csf('ydw_no')];
		$supplier_id=$result[csf('supplier_id')];
		$booking_date=$result[csf('booking_date')];
		$attention=$result[csf('attention')];
		$delivery_date=$result[csf('delivery_date')];
		$delivery_date_end=$result[csf('delivery_date_end')];
		$dy_delivery_start=$result[csf('dy_delivery_date_start')];
		$dy_delivery_end=$result[csf('dy_delivery_date_end')];
		$currency_id=$result[csf('currency')];
		$pay_mode_id=$result[csf('pay_mode')];
		$exchange_rate=$result[csf('ecchange_rate')];
		$is_short=$result[csf('is_short')];
	}
	$varcode_work_order_no=$work_order;

	$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
	$buyer_booking=return_library_array( "select booking_no, buyer_id from  wo_non_ord_samp_booking_mst",'booking_no','buyer_id');
	$style_ref_sample=return_library_array( "select id, style_ref_no from  sample_development_mst",'id','style_ref_no');
	$style_no="select a.style_ref_no, a.job_no, a.buyer_name, b.id as po_id,b.po_number,p.fab_booking_no, b.file_no, b.grouping as inter_ref_no, b.po_quantity, p.id as dtls_id,p.job_no_id,p.sample_name
	from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b
	where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.id=$update_id and  p.status_active=1 and p.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_result=sql_select($style_no);
	$style_all=$tot_job_no=$total_buyer=$total_order_no=$tot_sample_no=$all_file=$all_inter_ref="";//$total_order_qty=0;
	$po_ids='';$total_sample_name='';
	foreach($sql_result as $row)
	{

		$style_alll.=$row[csf("style_ref_no")].",";
		$tot_job_no.=$row[csf("job_no")].",";
		$tot_sample_no.=$row[csf("job_no_id")].",";
		$tottal_buyer.=$row[csf("buyer_name")].",";
		$total_order_no.=$row[csf("po_number")].",";
		$all_file.=$row[csf("file_no")].",";
		$all_inter_ref.=$row[csf("inter_ref_no")].",";
		if($po_ids=='') $po_ids=$row[csf("po_id")];else $po_ids.=",".$row[csf("po_id")];
		if($row[csf("fab_booking_no")]!='')
		{
		$booking_nosArr[$row[csf("fab_booking_no")]]=$row[csf("fab_booking_no")];
		}
	}
	$booking_nos=implode(",",$booking_nosArr);
	$style_all=chop($style_all," , ");$tot_job_no=chop($tot_job_no," , ");$tottal_buyer=chop($total_buyer," , ");$total_order_no=chop($total_order_no," , ");$all_file=chop($all_file," , ");$all_inter_ref=chop($all_inter_ref," , ");$tot_sample_no=chop($tot_sample_no," , ");
	 $sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id ,b.booking_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
			$sql_result=sql_select($sql);$total_sample_name='';$total_dtls_id='';$total_style_name='';$total_samp_deve_id="";$style_alll="";
			foreach($sql_result as $row)
			{
				if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];
				if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];
				if($style_alll=="") $style_alll=$style_ref_sample[$row[csf('job_no_id')]]; else $style_alll=$style_alll.",".$style_ref_sample[$row[csf('job_no_id')]];

				if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];

				if($row[csf("booking_no")] != '')
				{
					if($total_buyer=="") $total_buyer=$buyer_booking[$row[csf('booking_no')]]; else $total_buyer=$total_buyer.",".$buyer_booking[$row[csf('booking_no')]];
				}
				else
				{
					if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];
				}

			}

	$style_po="select a.job_no,p.fab_booking_no, sum(distinct b.po_quantity) as po_quantity
	from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b
	where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.id=$update_id and  p.status_active=1 and p.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no,p.fab_booking_no";
	$sql_po=sql_select($style_po);
	$total_order_qty=0;$booking_nos="";
	foreach($sql_po as $row)
	{
		$total_order_qty+=$row[csf('po_quantity')];
		$row[csf('job_no')].=$row[csf('fab_booking_no')].',';
		if($row[csf("fab_booking_no")]!="")
		{
			if($booking_nos=='') $booking_nos=$row[csf("fab_booking_no")];else $booking_nos.=",".$row[csf("fab_booking_no")];
		}
	}
	?>
	<div style="width:1060px" align="center">

        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
               <img  src='../../<? echo $image_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php
										echo $company_library[$cbo_company_name];
												?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?>
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?>
                                            Block No: <? echo $result[csf('block_no')];?>
                                            City No: <? echo $result[csf('city')];?>
                                            Zip Code: <? echo $result[csf('zip_code')]; ?>
                                            Province No: <?php echo $result[csf('province')]; ?>
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                            Email Address: <? echo $result[csf('email')];?>
                                            Website No: <? echo $result[csf('website')];
                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                          <strong>Yarn Dyeing Work Order Without Order<? if($is_short==1) echo " (Short) "; ?> </strong>
                             </td>
                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id">

               </td>
            </tr>
       </table>
		<?

	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$company_address_arr=return_library_array( "select id,city from   lib_company",'id','city');
		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
					<tr  >
							<td  width="120"><b>To</b><br>
							<td width="230">:&nbsp;&nbsp;<?if($pay_mode_id==3 || $pay_mode_id==5)
			            	{
			            		echo $company_library[$new_supplier_name];
			            	}
			            	else
			            	{
			            		echo $supplier_name[$new_supplier_name];
			            	}?></td>
						</tr>
						</tr>
                        <tr style="font-size:12px">
                            <td valign="top"><b>Address</b></td>
                            <td valign="top">: &nbsp;<?
							if($pay_mode_id==3 ||  $pay_mode_id==5)
							{
								$supp_add=$company_address_arr[$supplier_id];

							}
							else
							{
								$supp_add=$supplier_address_arr[$supplier_id];
							}
							if($pay_mode_id==5 || $pay_mode_id==3){
								echo $company_library[$supplier_id];
							}
							else{
								echo $supplier_arr[$supplier_id];
							}
							echo $supp_add;
							?></td>
                        </tr>
                        <tr style="font-size:12px">
                            <td valign="top"><b>Pay Mode</b></td>
                            <td valign="top">: &nbsp;<?=$pay_mode[$pay_mode_id]; ?></td>
                        </tr>
						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00 00:00:00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>
						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr style="font-size:12px">
							<td valign="top"><b>Sample Id</b></td>
							<td valign="top">:&nbsp;
						<?
							$total_samp_deve_id=implode(",",array_unique(explode(",",$total_samp_deve_id)));
							echo $total_samp_deve_id;

							?>
						</td>
						</tr>
						<tr style="font-size:12px">
							<td valign="top"><b>Sample Name</b></td>
							<td valign="top">: &nbsp;
							<?
							$total_sample_name_arr=array_unique(explode(",",$total_sample_name));

							$all_order=explode(",",$total_order_no);
							$sample="";
							foreach($total_sample_name_arr as $row)
							{
								if($sample=="") $sample=$sample_arr[$row]; else $sample=$sample.",".$sample_arr[$row];
							}
							echo $sample;
							?>
							</td>
						</tr>


						</tr>

					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00 00:00:00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00 00:00:00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00 00:00:00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00 00:00:00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>

						<? if($show_comment==1)	{ ?>
						<tr>
							<td valign="top"><b>Style No</b></td>
							<td valign="top">: &nbsp;
							<?
								$style_alll=implode(",",array_unique(explode(",",$style_alll)));
								echo $style_alll;
							?>
							</td>
						</tr>
						<tr style="font-size:12px">
							<td valign="top"><b>Buyer Name</b></td>
							<td valign="top">: &nbsp;
							<?
							$total_buyer_array=array_unique(explode(",",$total_buyer));
							$view_buyer="";
							foreach($total_buyer_array as $row)
							{
							if($view_buyer=="") $view_buyer=$buyer_arr[$row]; else $view_buyer=$view_buyer.",".$buyer_arr[$row];
							}
							echo $view_buyer;
							?>
							</td>
						</tr>
					<? } ?>

					</table>
				</td>
				<td width="250"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>


		<table width="1060"  align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30" align="center">Sl</th>
					<th width="70" align="center">Color</th>
					<th width="80" align="center">Color Range</th>
					<th  align="center" width="50">Ref No.</th>
					<th width="60" align="center">File No</th>
					<th width="60" align="center">Internal Ref. No</th>
					<th width="30" align="center">Yarn Count</th>
					<th width="60" align="center">GSM</th>
					<th width="160" align="center">Yarn Description</th>
					<th width="50" align="center">Brand</th>
					<th width="50" align="center">Lot</th>
					<th width="50" align="center">UOM</th>
					<th width="60" align="center">WO Qty</th>
					<th width="50" align="center">Dyeing Rate</th>
					<th width="70" align="center">Amount</th>
					<th  align="center" width="50" >Min Req. Cone</th>
					<th  align="center" >Remarks/ Shade</th>
				</tr>
			</thead>
			<?

				$sql_gsm="SELECT a.gsm_weight
				FROM wo_non_ord_samp_booking_dtls a, wo_yarn_dyeing_dtls b
			   WHERE    a.booking_no=b.booking_no
					 AND a.status_active = 1
					 AND b.status_active = 1
					 AND b.id IN ($total_dtls_id)";
				$sql_gsm_result=sql_select($sql_gsm);
				foreach($sql_gsm_result as $row)
				{
					$gsm_weight=$row[csf("gsm_weight")];
				}
			$sql_color="select id,product_id,job_no,job_no_id,yarn_color,yarn_description,count,color_range,yarn_wo_qty as yarn_wo_qty,dyeing_charge,amount as amount,min_require_cone,referance_no, remarks, file_no, internal_ref_no
			from
			wo_yarn_dyeing_dtls
			where
			status_active=1 and id in($total_dtls_id)";
			$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";$job_strip_color=array();
			foreach($sql_result as $row)
			{
				$product_id=$row[csf("product_id")];
				$job_strip_color[$row[csf("job_no")]].=$row[csf("yarn_color")].",";
				$all_stripe_color.=$row[csf("yarn_color")].",";
				if($product_id)
				{
					$sql_brand=sql_select("select lot,brand,gsm from product_details_master where id in($product_id)");
					foreach($sql_brand as $row_barand)
					{
						$lot_amt=$row_barand[csf("lot")];
						$brand=$row_barand[csf("brand")];
						$gsm=$row_barand[csf("gsm")];
					}
				}


				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";


				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><p><? echo $i; ?></p></td>
					<td><p><? echo $color_arr[$row[csf("yarn_color")]]; ?></p></td>
					<td><p><? echo $color_range[$row[csf("color_range")]]; ?></p></td>
					<td align="center"><p><? echo $row[csf("referance_no")]; ?></p></td>
					<td><p><? echo $row[csf("file_no")]; ?></p></td>
					<td><p><? echo $row[csf("internal_ref_no")]; ?></p></td>
					<td align="center"><p><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></p></td>
					<td align="center"><p><? echo $gsm_weight; ?></p></td>
					<td><p> <?  echo $row[csf("yarn_description")]; ?></p></td>
					<td><p><? echo $brand_arr[$brand]; ?></p></td>
					<td align="center"><p><? echo $lot_amt; ?></p></td>
					<td align="center"><p><? echo "KG"; ?></p></td>
					<td align="right"><? echo number_format($row[csf("yarn_wo_qty")],2,'.','');$total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
					<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
					<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
					<td align="center"><p><? echo $row[csf("min_require_cone")]; ?></p></td>
					<td align="center"><p><? echo $row[csf("remarks")]; ?></p></td>
				</tr>
				<?
				$i++;
				$yarn_count_des="";
				$style_no="";
			}
			?>
			<tr>
				<td colspan="12" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right" ><b><? echo number_format($total_qty,2,'.','') ; ?></b></td>
				<td align="right">&nbsp;</td>
				<td align="right"><b><? echo number_format($total_amount,2); ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?
			$mcurrency="";
			$dcurrency="";
			if($currency_id==1)
			{
				$mcurrency='Taka';
				$dcurrency='Paisa';
			}
			if($currency_id==2)
			{
				$mcurrency='USD';
				$dcurrency='CENTS';
			}
			if($currency_id==3)
			{
				$mcurrency='EURO';
				$dcurrency='CENTS';
			}
			?>
			<tr>
				<td colspan="17" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </td>
			</tr>
		</table>

		<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

				<table  width="950" class="rpt_table"    border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
					<thead>
						<tr style="border:1px solid black;">
							<th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
						</tr>
					</thead>
					<tbody>
						<?

        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no' and entry_form=42");//
        if ( count($data_array)>0)
        {
        	$i=0;
        	foreach( $data_array as $row )
        	{
        		$i++;
        		?>
        		<tr id="settr_1" align="" style="border:1px solid black;">
        			<td style="border:1px solid black;">
        				<? echo $i;?>
        			</td>
        			<td style="border:1px solid black;">
        				<? echo $row[csf('terms')]; ?>
        			</td>
        		</tr>
        		<?
        	}
        }
        else
        {
        	$i=0;
		$data_array=sql_select("select id, terms from lib_terms_condition where is_default=1 and page_id=42 order by id asc ");
        foreach( $data_array as $row )
        {
        	$i++;
        	?>
        	<tr id="settr_1" align="" style="border:1px solid black;">
        		<td style="border:1px solid black;">
        			<? echo $i;?>
        		</td>
        		<td style="border:1px solid black;">
        			<? echo $row[csf('terms')]; ?>
        		</td>
        	</tr>
        	<?
        }
    }
    ?>
</tbody>
</table>
		<br> <br>

	</div>
	<br/>
	<div>
	 <?

     $lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

		$mst_id=return_field_value("id as mst_id","wo_yarn_dyeing_mst","id=$wo_ord_id","mst_id");

	 $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=30  group by b.approved_by");

	 $unapprove_data_array=sql_select("select b.id,b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=30  order by b.approved_date");
          if(count($approve_data_array)>0)
			{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="40%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Designation</th>
				<th width="27%" style="border:1px solid black;">Approval Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($approve_data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>

                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
		<br>
		<?
		if(count($unapprove_data_array)>0)
		{
			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=29 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
				$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
			}
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($unapprove_data_array as $row){

			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
				<td width="20%" style="border:1px solid black;"><? echo '';?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
             </tr>
				<?
				$i++;
				if($row[csf('un_approved_date')]!="" && $row[csf('un_approved_date')]!="0000-00-00 00:00:00")
				{
				?>
			<tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
              </tr>

                <?
				$i++;
				}

			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
		<br/>
		<?
	echo signature_table(151, $cbo_company_name, "1000px");
	echo "****".custom_file_name($txt_booking_no,$all_style,$all_job);
	?>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
	</script>
	<?
}
if($action=="show_trim_booking_report")
{
	//echo "uuuu";die;
	//print_r($_REQUEST); die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode_id = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);


	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$merchant_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name",'id','team_member_name');

	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$term_condition=str_replace("'","",$term_condition);

	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" align="center" rules="all" >
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php echo $company_library[$cbo_company_name];?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong><? echo $report_title;?> </strong>
							</td>
						</tr>
					</table>
				</td>
				<td align="right">
					<div style="float:right;width:24px; margin-right:80px; text-align:right;">
						<div style="height:13px; width:15px;" id="qrcode"></div> 
					</div>
				</td>
				
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray=sql_select( "SELECT a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency, a.inserted_by from wo_yarn_dyeing_mst a where a.id=$update_id");
		$inserted_by=$nameArray[0]['INSERTED_BY'];
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_val=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];
		}
		$varcode_work_order_no=$work_order;
		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<?

							if($pay_mode_id==3 || $pay_mode_id==5)
			            	{
			            		echo $company_library[$new_supplier_name];
			            	}
			            	else
			            	{
			            		echo $supplier_name[$new_supplier_name];
			            	}



							//echo $supplier_arr[$supplier_id];

							?></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="250"  style="font-size:12px">
					<?
					//echo "select image_location from common_photo_library where master_tble_id='$update_id' and form_name='$form_name'";
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					if($entry_form == 42){
					?>
					<img src="<? echo '../'.$image_location; ?>" width="120" height="100" border="2" />
				<? }else{?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				<? }?>
				</td>
			</tr>
		</table>
	</br>

	<?
	  			/*$multi_job_arr=array();
				$style_no=sql_select("select a.style_ref_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.status_active=1 and b.is_deleted=0");

				 foreach($style_no as $row_s)
				 {

				$multi_job_arr[$row_s[csf('job_no')]]['buyer']=$row_s[csf('buyer_name')];
				$multi_job_arr[$row_s[csf('job_no')]]['po_no']=$row_s[csf('po_number')];
			}	*/
			$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
			$buyer_booking=return_library_array( "select booking_no, buyer_id from  wo_non_ord_samp_booking_mst",'booking_no','buyer_id');
			$sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id ,b.booking_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
			$sql_result=sql_select($sql);
			$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name=''; $total_booking_no='';
			foreach($sql_result as $row)
			{
				if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

				if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];
				if($total_booking_no=="") $total_booking_no=$row[csf("booking_no")]; else $total_booking_no=$total_booking_no.",".$row[csf("booking_no")];

				if($row[csf("booking_no")] != '')
				{
					if($total_buyer=="") $total_buyer=$buyer_booking[$row[csf('booking_no')]]; else $total_buyer=$total_buyer.",".$buyer_booking[$row[csf('booking_no')]];
				}
				else
				{
					if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];
				}


				if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

			}

			$total_samp_deve_id = implode(",",array_filter(array_unique(explode(",", $total_samp_deve_id))));
			if($total_samp_deve_id)
			{
				$deal_mercha_res = sql_select("select dealing_marchant from sample_development_mst where id in ($total_samp_deve_id) and status_active=1 and is_deleted=0");
				foreach ($deal_mercha_res as $val)
				{
					$dealing_marchants .= $merchant_arr[$val[csf("dealing_marchant")]].",";
				}
			}
			$dealing_marchants = implode(",",array_filter(array_unique(explode(",", chop($dealing_marchants,",")))));
		//var_dump($total_dtls_id);
		//die;
	   //echo "**".$total_samp_deve_id;die;
			?>

			<table width="950" style="" align="center">
				<tr  style="font-size:12px">
					<td width="150"><b>Sample Id</b></td>
					<td width="800" valign="top">:&nbsp;
						<?
						echo $total_samp_deve_id;
						?>
					</td>
				</tr>
				<tr  style="font-size:12px">
					<td width="150"><b>Booking NO</b></td>
					<td width="800" valign="top">:&nbsp;
						<?
						echo $total_booking_no;
						?>
					</td>
				</tr>

				<tr style="font-size:12px">
					<td valign="top"><b>Sample Name</b></td>
					<td valign="top">: &nbsp;
						<?
						$total_sample_name_arr=array_unique(explode(",",$total_sample_name));

						$all_order=explode(",",$total_order_no);
						$sample="";
						foreach($total_sample_name_arr as $row)
						{
							if($sample=="") $sample=$sample_arr[$row]; else $sample=$sample.",".$sample_arr[$row];
						}
						echo $sample;
						?>
					</td>
				</tr>
				<tr style="font-size:12px">
					<td valign="top"><b>Buyer </b> </td>
					<td valign="top">:&nbsp;
						<?
						$total_buyer_array=array_unique(explode(",",$total_buyer));
						$view_buyer="";
						foreach($total_buyer_array as $row)
						{
							if($view_buyer=="") $view_buyer=$buyer_arr[$row]; else $view_buyer=$view_buyer.",".$buyer_arr[$row];
						}
						echo $view_buyer;
						?>
					</td>
				</tr >
				<tr style="font-size:12px">
					<td valign="top"><b>Dealing Merchant</b> </td>
					<td valign="top">:&nbsp;
						<?

						echo $dealing_marchants;
						?>
					</td>
				</tr >
			</table>



			<table width="950"  align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30" align="center"><strong>Sl</strong></th>
						<th width="70" align="center"><strong>Color</strong></th>
						<th width="80" align="center"><strong>Color Range</strong></th>
						<th  align="center" width="50"><strong>Ref No.</strong></th>
						<th width="30" align="center"><strong>Yarn Count</strong></th>
						<th width="160" align="center"><strong>Yarn Description</strong></th>
						<th width="60" align="center"><strong>Brand</strong></th>
						<th width="60" align="center"><strong>Lot</strong></th>
						<th width="60" align="center"><strong>WO Qty</strong></th>
						<th width="50" align="center"><strong>Dyeing Rate</strong></th>
						<th width="80" align="center"><strong>Amount</strong></th>
						<th  align="center" width="60" ><strong>Min Req. Cone</strong></th>
						<th  align="center" ><strong>Remarks</strong></th>
					</tr>
				</thead>
				<?

				$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
				foreach($sql_brand as $row_barand)
				{
					$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
					$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
				}

				if($db_type==0){
					$sql_color="select
					product_id,
					yarn_color,
					yarn_description,
					count,
					color_range,
					sum(yarn_wo_qty) as yarn_wo_qty,
					avg(dyeing_charge) as dyeing_charge,
					sum(amount) as amount,
					min_require_cone,
					referance_no,
					remarks
					from
					wo_yarn_dyeing_dtls
					where
					status_active=1 and id in($total_dtls_id) group by yarn_color, color_range,
					product_id,count,yarn_description order by yarn_color ";			}
					else if($db_type==2){
						$sql_color="select
						product_id,
						yarn_color,
						yarn_description,
						count,
						color_range,
						sum(yarn_wo_qty) as yarn_wo_qty,
						avg(dyeing_charge) as dyeing_charge,
						sum(amount) as amount,
						listagg(CAST(min_require_cone AS VARCHAR(4000)),',')  within group (order by min_require_cone) as min_require_cone,
						listagg(CAST(referance_no AS VARCHAR(4000)),',')  within group (order by referance_no) as referance_no,
						listagg(CAST(remarks AS VARCHAR(4000)),',')  within group (order by remarks) as remarks
						from
						wo_yarn_dyeing_dtls
						where
						status_active=1 and id in($total_dtls_id) group by yarn_color, color_range,
						product_id,count,yarn_description order by yarn_color ";
					}







			//echo $sql_color;die;
					$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
					foreach($sql_result as $row)
					{
						$product_id=$row[csf("product_id")];
				//var_dump($product_id);
						if($row[csf("product_id")]!="")
						{
							$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
							$brand=$product_lot[$row[csf("product_id")]]['brand'];
						}

				//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
							<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
							<td align="center"><? echo $row[csf("referance_no")]; ?></td>
							<td align="center"><? echo $count_arr[$row[csf("count")]]; ?></td>
							<td>
								<?
								echo $row[csf("yarn_description")];
								?>
							</td>
							<td><? echo $brand_arr[$brand]; ?></td>
							<td align="center"><? echo $lot_amt; ?></td>
							<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
							<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
							<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
							<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
							<td align="center"><? echo $row[csf("remarks")]; ?></td>
						</tr>
						<?
						$i++;
						$yarn_count_des="";
						$style_no="";
					}
					?>
					<tfoot>
						<tr>
							<th colspan="8" align="right"><strong>Total:</strong>&nbsp;&nbsp;</th>
							<th align="right" ><b><? echo $total_qty; ?></b></th>
							<th align="right">&nbsp;</th>
							<th align="right"><b><? echo number_format($total_amount,2); ?></b></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
						</tr>
						<tr>
							<th colspan="13" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </th>
						</tr>
					</tfoot>

					<?
					$mcurrency="";
					$dcurrency="";
					if($currency_id==1)
					{
						$mcurrency='Taka';
						$dcurrency='Paisa';
					}
					if($currency_id==2)
					{
						$mcurrency='USD';
						$dcurrency='CENTS';
					}
					if($currency_id==3)
					{
						$mcurrency='EURO';
						$dcurrency='CENTS';
					}
					?>
				</table>


				<!--==============================================AS PER GMTS COLOR START=========================================  -->
			<?
			if($term_condition==1){	?>
				<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

				<table  width="950" class="rpt_table"    border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
					<thead>
						<tr style="border:1px solid black;">
							<th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
						</tr>
					</thead>
					<tbody>
						<?
		//echo "select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no'";
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no' and entry_form=42");// quotation_id='$data'
        if ( count($data_array)>0)
        {
        	$i=0;
        	foreach( $data_array as $row )
        	{
        		$i++;
        		?>
        		<tr id="settr_1" align="" style="border:1px solid black;">
        			<td style="border:1px solid black;">
        				<? echo $i;?>
        			</td>
        			<td style="border:1px solid black;">
        				<? echo $row[csf('terms')]; ?>
        			</td>
        		</tr>
        		<?
        	}
        }
        else
        {
        	$i=0;
        // $data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con");// quotation_id='$data'
		$data_array=sql_select("select id, terms from lib_terms_condition where is_default=1 and page_id=42 order by id asc ");
        foreach( $data_array as $row )
        {
        	$i++;
        	?>
        	<tr id="settr_1" align="" style="border:1px solid black;">
        		<td style="border:1px solid black;">
        			<? echo $i;?>
        		</td>
        		<td style="border:1px solid black;">
        			<? echo $row[csf('terms')]; ?>
        		</td>
        	</tr>
        	<?
        }
    }
    ?>
	</tbody>
	</table>
	<?}?>

	</div>
	<div>
		<?
		//approved status
		/*$data_array_approve=sql_select("SELECT b.approved_by,b.approved_no, b.approved_date, c.user_full_name, c.designation, b.un_approved_by from inv_purchase_requisition_mst a, approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and b.entry_form=1 and a.id='$data[1]' order by b.id asc");*/

		//echo "SELECT  mst_id, approved_by,sequence_no ,min(approved_date) as approved_date from approval_history where entry_form=30 AND  mst_id ='$update_id' group by mst_id, approved_by,sequence_no order by sequence_no";

		$approved_sql=sql_select("SELECT   mst_id, approved_by,sequence_no ,min(approved_date) as approved_date from approval_history where entry_form=30 AND  mst_id ='$update_id' group by mst_id, approved_by,sequence_no order by sequence_no");

		$approved_his_sql=sql_select("SELECT  id, mst_id, approved_by,sequence_no,approved_date,un_approved_reason,un_approved_date  from approval_history where entry_form=30 AND  mst_id ='$update_id' order by approved_date");

		//echo "SELECT  mst_id, approved_by,sequence_no,approved_date,un_approved_reason,un_approved_date  from approval_history where entry_form=30 AND  mst_id ='$update_id' order by approved_date ASC";

		// foreach ($approved_his_sql as $key => $row)
		// {
		// 	$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
		// 	if ($row[csf('un_approved_date')]!='')
		// 	{
		// 		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
		// 		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
		// 	}
		// }
		/*echo "<pre>";
		print_r($array_data);//*/
		$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
		$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");

		if(count($approved_sql) > 0)
		{
			$sl=1;
			?>
			<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<label><b>Yarn Dyeing Work Order Without Order Aprroval Status </b></label>
					<thead>
						<tr style="font-weight:bold">
							<th width="20">SL</th>
							<th width="250">Name</th>
							<th width="200">Designation</th>
							<th width="100">Approval Date</th>
						</tr>
					</thead>
					<? foreach ($approved_sql as $key => $value)
					{
						?>
						<tr>
							<td width="20"><? echo $sl; ?></td>
							<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
							<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
							<td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
						</tr>
						<?
						$sl++;
					}
					?>
				</table>
			</div>
			<?
		}
		/*

			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=29 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
			$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
			}
			?>
		<table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
				<thead>
				<tr style="border:1px solid black;">
					<th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
					</tr>
					<tr style="border:1px solid black;">
					<th width="3%" style="border:1px solid black;">Sl</th>
					<th width="30%" style="border:1px solid black;">Name</th>
					<th width="20%" style="border:1px solid black;">Designation</th>
					<th width="5%" style="border:1px solid black;">Approval Status</th>
					<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
					<th width="22%" style="border:1px solid black;"> Date</th>

					</tr>
				</thead>
				<tbody>
				<?
				$i=1;
				foreach($unapprove_data_array as $row){

				?>
				<tr style="border:1px solid black;">
					<td width="3%" style="border:1px solid black;"><? echo $i;?></td>
					<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
					<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
					<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
					<td width="20%" style="border:1px solid black;"><? echo '';?></td>
					<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
				</tr>
					<?
					$i++;
					if($row[csf('un_approved_date')]!="")
					{
					?>
				<tr style="border:1px solid black;">
					<td width="3%" style="border:1px solid black;"><? echo $i;?></td>
					<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
					<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
					<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
					<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
					<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
				</tr>

					<?
					$i++;
					}

				}
					?>
				</tbody>
			</table>
			<?
			}
		*/
	?>
	<?
	if(count($approved_his_sql) > 0){
	?>
	<div style="margin-top:15px">
	<table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
		 <thead>
		 <tr style="border:1px solid black;">
			 <label><strong>Approval/Un Approval History</strong></label>
			 </tr>
			 <tr style="border:1px solid black;">
			 <th width="3%" style="border:1px solid black;">Sl</th>
			<th width="30%" style="border:1px solid black;">Name</th>
			<th width="20%" style="border:1px solid black;">Designation</th>
			<th width="5%" style="border:1px solid black;">Approval Status</th>
			<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
			<th width="22%" style="border:1px solid black;"> Date</th>

			 </tr>
		 </thead>
		 <tbody>
			 <?
 			$i=1;
 			foreach($approved_his_sql as $row){
				//echo $row[csf('approved_by')];
 			?>
             <tr style="border:1px solid black;">
                 <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
 				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name[$row[csf('approved_by')]];?></td>
 				<td width="20%" style="border:1px solid black;text-align:center"><? echo $designation_lib[$user_lib_desg[$row[csf('approved_by')]]];?></td>
 				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
 				<td width="20%" style="border:1px solid black;"><? echo '';?></td>
 				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
              </tr>
 				<?
 				$i++;
 				if($row[csf('un_approved_date')]!="" && $row[csf('un_approved_date')] !="0000-00-00 00:00:00")
 				{
 				?>
 			<tr style="border:1px solid black;">
                 <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
 				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name[$row[csf('approved_by')]];?></td>
 				<td width="20%" style="border:1px solid black;text-align:center"><? echo $designation_lib[$user_lib_desg[$row[csf('approved_by')]]];?></td>
 				<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
 				<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
 				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
               </tr>

                 <?
 				$i++;
 				}

 			}
 				?>
             </tbody>
         </table>
	 </div>

	<?
	}
	?>



	<?
	//if($entry_form == 42){
		$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		echo signature_table(151,$cbo_company_name,"950px",'','',$user_lib_name[$inserted_by]);
	// }else{
	// 	echo signature_table(43, $cbo_company_name, "950px");
	// }
			//echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
	?>
	</div>
	<? if($entry_form == 42){?>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquerybarcode.js"></script>
	<? }else{?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<? }?>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
	</script>
	<script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
	<script>
		var main_value='<? echo $work_order."***1***2***1"; ?>';
		// alert(main_value);
		$('#qrcode').qrcode(main_value)			
	</script>
	<?
	exit();
}


if($action=="print3_report")
{
	//echo "uuuu";die;
	//print_r($_REQUEST); die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$pay_mode_id = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);

	$image_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$merchant_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name",'id','team_member_name');

	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$term_condition=str_replace("'","",$term_condition);

	?>
	<div style="width: 1200px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" align="center" rules="all" >
			<tr>
				<td width="100">
               		<img  src='../../<? echo $image_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               	</td>
				<td width="650">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php echo $company_library[$cbo_company_name];?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>
									Plot No: <? echo $result[csf('plot_no')];?>
									Level No: <? echo $result[csf('level_no')];?>
									Road No: <? echo $result[csf('road_no')];?>
									Block No: <? echo $result[csf('block_no')];?>
									Zip Code: <? echo $result[csf('zip_code')];?>
									<?
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong><? echo $report_title;?> </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		$nameArray=sql_select( "SELECT a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency, a.inserted_by from wo_yarn_dyeing_mst a where a.id=$update_id");
		$inserted_by=$nameArray[0]['INSERTED_BY'];
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_val=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];
		}
		// echo "<pre>";
		// print_r($nameArray);die;
		$varcode_work_order_no=$work_order;
		$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
		$company_address_arr=return_library_array( "select id,city from   lib_company",'id','city');
		?>

		<table width="100%" style="" align="center">
			<tr>
				<td width="100%"  style="font-size:12px">
					<table width="100%" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<?

							if($pay_mode_id==3 || $pay_mode_id==5)
			            	{
			            		echo $company_library[$new_supplier_name];
			            	}
			            	else
			            	{
			            		echo $supplier_name[$new_supplier_name];
			            	}

							?></td>
						</tr>
						<tr style="font-size:12px">
                            <td valign="top"><b>Address</b></td>
                            <td valign="top">: &nbsp;<?
							if($pay_mode_id==3 ||  $pay_mode_id==5)
							{
								$supp_add=$company_address_arr[$supplier_id];

							}
							else
							{
								$supp_add=$supplier_address_arr[$supplier_id];
							}
							if($pay_mode_id==5 || $pay_mode_id==3){
								echo $company_library[$supplier_id];
							}
							else{
								echo $supplier_arr[$supplier_id];
							}
							echo $supp_add;
							?></td>

							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>

							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
                        </tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>

							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>

							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>

							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>

							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>

					</table>
				</td>

			</tr>
		</table>
		</br>

		<?
			$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
			$buyer_booking=return_library_array( "select booking_no, buyer_id from  wo_non_ord_samp_booking_mst",'booking_no','buyer_id');

			$sql= "SELECT a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id ,b.booking_no, c.grouping as internal_ref, b.batch_ld_no from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_non_ord_samp_booking_mst c where a.status_active=1 and b.booking_no = c.booking_no and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";

			// echo $sql; die;

			$sql_result=sql_select($sql);

			// echo "<pre>";
			// print_r($sql_result);die;

			$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name=''; $total_booking_no='';
			foreach($sql_result as $row)
			{
				if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

				if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];
				if($total_booking_no=="") $total_booking_no=$row[csf("booking_no")]; else $total_booking_no=$total_booking_no.",".$row[csf("booking_no")];

				if($row[csf("booking_no")] != '')
				{
					if($total_buyer=="") $total_buyer=$buyer_booking[$row[csf('booking_no')]]; else $total_buyer=$total_buyer.",".$buyer_booking[$row[csf('booking_no')]];
				}
				else
				{
					if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];
				}


				if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

				// $internal_ref = $row[csf("internal_ref")];

			}

			$total_samp_deve_id = implode(",",array_filter(array_unique(explode(",", $total_samp_deve_id))));
			if($total_samp_deve_id)
			{
				$deal_mercha_res = sql_select("select dealing_marchant from sample_development_mst where id in ($total_samp_deve_id) and status_active=1 and is_deleted=0");
				foreach ($deal_mercha_res as $val)
				{
					$dealing_marchants .= $merchant_arr[$val[csf("dealing_marchant")]].",";
				}
			}
			$booking_no_arr = explode (",", $total_booking_no);
			$dealing_marchants = implode(",",array_filter(array_unique(explode(",", chop($dealing_marchants,",")))));
			$fabrication = sql_select("select distinct fabric_description from wo_non_ord_samp_booking_dtls where booking_no = '$booking_no_arr[0]'");
			// echo $total_booking_no;die;
			$internal_ref_sql = "select distinct  c.internal_ref from  sample_development_mst c, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where c.status_active = 1 and c.is_deleted = 0 and c.id in (b.style_id) and a.booking_no = b.booking_no and a.status_active = 1  and b.status_active = 1 and a.booking_no = '$booking_no_arr[0]'";

			// echo $fabrication; die;
			$internal_ref_arr = sql_select($internal_ref_sql);
			$internal_ref = $internal_ref_arr[0][csf('internal_ref')];

			$fabrication_str = '';
			foreach($fabrication as $key=>$fabric_value)
				{
					$fabrication_str .= $fabric_value['FABRIC_DESCRIPTION']." ";
				}
			?>

			<table width="78%" style="" align="left">
				<tr  style="font-size:12px">
					<td width="120"><b>Fabrication</b></td>
					<td width="800" valign="top">:&nbsp;
						<?
							echo $fabrication_str;
						?>
					</td>
				</tr>
				<tr  style="font-size:12px">
					<td width="120"><b>IR/CN</b></td>
					<td width="800" valign="top">:&nbsp;
						<?
						echo $internal_ref;
						?>
					</td>
				</tr>

						<?
						$total_sample_name_arr=array_unique(explode(",",$total_sample_name));

						$all_order=explode(",",$total_order_no);
						$sample="";
						foreach($total_sample_name_arr as $row)
						{
							if($sample=="") $sample=$sample_arr[$row]; else $sample=$sample.",".$sample_arr[$row];
						}

						?>
			</table>



			<table width="100%"  align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30" align="center"><strong>Sl</strong></th>
						<th width="70" align="center"><strong>Customer</strong></th>
						<th width="70" align="center"><strong>Style Ref No</strong></th>
						<th width="70" align="center"><strong>Sample Booking No</strong></th>
						<th width="30" align="center"><strong>Yarn Count</strong></th>
						<th width="160" align="center"><strong>Yarn Description</strong></th>
						<th width="60" align="center"><strong>Yarn Lot</strong></th>
						<th width="60" align="center"><strong>Brand</strong></th>
						<th width="60" align="center"><strong>Color</strong></th>
						<th width="60" align="center"><strong>WO Qty</strong></th>
						<th width="50" align="center"><strong>Dyeing Rate</strong></th>
						<th width="80" align="center"><strong>Amount</strong></th>
						<th width="80" align="center"><strong>No of Bags</strong></th>
						<th width="80" align="center"><strong>No of Cone</strong></th>
						<th width="80"  align="center" width="60" ><strong>Batch/LD No </strong></th>
						<th width="80" align="center" ><strong>Remarks/Shade</strong></th>
					</tr>
				</thead>
				<?

				$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
				foreach($sql_brand as $row_barand)
				{
					$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
					$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
				}

				if($db_type==0){

					$sql_color=" SELECT product_id, yarn_color, yarn_description, count, color_range, sum(yarn_wo_qty) as yarn_wo_qty, avg(dyeing_charge) as dyeing_charge, sum(amount) as amount, min_require_cone, referance_no, remarks, batch_ld_no from wo_yarn_dyeing_dtls where status_active=1 and id in($total_dtls_id) group by yarn_color, color_range, product_id,count,yarn_description order by yarn_color ";
				}
				else if($db_type==2){
					$sql_color = " SELECT a.product_id, a.yarn_color, a.yarn_description, a.booking_no, a.no_of_bag, a.no_of_cone, a.referance_no, a.remarks, a.min_require_cone, COUNT, b.style_id, c.buyer_name, c.style_ref_no, a.color_range, SUM (a.yarn_wo_qty) AS yarn_wo_qty, AVG (a.dyeing_charge)  AS dyeing_charge, SUM (a.amount) AS amount, a.batch_ld_no FROM wo_yarn_dyeing_dtls  a, wo_non_ord_samp_booking_dtls b, sample_development_mst  c WHERE  a.status_active = 1 AND a.id IN ($total_dtls_id) AND b.booking_no = a.booking_no AND c.id IN (b.style_id) AND b.booking_no = a.booking_no GROUP BY a.yarn_color, a.color_range, a.product_id, a.referance_no, a.remarks, COUNT, a.min_require_cone, b.style_id, c.buyer_name, c.style_ref_no, a.yarn_description, a.booking_no, a.no_of_bag, a.no_of_cone ORDER BY a.yarn_color";
					$sql_color = " SELECT distinct a.product_id, a.yarn_color, a.yarn_description, a.booking_no, a.no_of_bag, a.no_of_cone, a.referance_no, a.remarks, a.min_require_cone, COUNT, b.style_id, c.buyer_name, c.style_ref_no, a.color_range, a.yarn_wo_qty AS yarn_wo_qty, a.dyeing_charge AS dyeing_charge, a.amount AS amount, a.batch_ld_no FROM wo_yarn_dyeing_dtls a, wo_non_ord_samp_booking_dtls b, sample_development_mst c WHERE a.status_active = 1 AND a.id IN ($total_dtls_id) AND b.booking_no = a.booking_no AND c.id IN (b.style_id) AND b.booking_no = a.booking_no ORDER BY a.yarn_color";
				}

					// echo $sql_color;die;
					$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
					foreach($sql_result as $row)
					{
						$booking_number = $row[csf("booking_no")];

						$product_id=$row[csf("product_id")];
						//var_dump($product_id);
						if($row[csf("product_id")]!="")
						{
							$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
							$brand=$product_lot[$row[csf("product_id")]]['brand'];
						}


						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td align="center"><? echo $buyer_name_arr[$row[csf("buyer_name")]]; ?></td>
							<td align="center"><? echo $row[csf("style_ref_no")]; ?></td>
							<td align="center"><? echo $booking_number?></td>
							<td align="center"><? echo $count_arr[$row[csf("count")]]; ?></td>
							<td align="center"><?echo $row[csf("yarn_description")];?></td>
							<td align="center"><? echo $lot_amt; ?></td>
							<td align="center"><? echo $brand_arr[$brand]; ?></td>
							<td align="center"><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
							<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
							<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
							<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
							<td align="center"><? echo $row[csf("no_of_bag")]; ?></td>
							<td align="center"><? echo $row[csf("no_of_cone")]; ?></td>
							<td align="center"><? echo $row[csf("batch_ld_no")]; ?></td>
							<td align="center"><? echo $row[csf("remarks")]; ?></td>

						</tr>
						<?
						$i++;
						$yarn_count_des="";
						$style_no="";
					}
					?>
					<tfoot>
						<tr>
							<th colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</th>
							<th align="right" ><b><? echo $total_qty; ?></b></th>
							<th align="right">&nbsp;</th>
							<th align="right"><b><? echo number_format($total_amount,2); ?></b></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>

						</tr>
						<tr>
							<th colspan="16" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </th>
						</tr>
					</tfoot>

					<?
					$mcurrency="";
					$dcurrency="";
					if($currency_id==1)
					{
						$mcurrency='Taka';
						$dcurrency='Paisa';
					}
					if($currency_id==2)
					{
						$mcurrency='USD';
						$dcurrency='CENTS';
					}
					if($currency_id==3)
					{
						$mcurrency='EURO';
						$dcurrency='CENTS';
					}
					?>
				</table>


				<!--==============================================AS PER GMTS COLOR START=========================================  -->
			<?
			if($term_condition==1){	?>
				<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

				<table  width="950" class="rpt_table"    border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
					<thead>
						<tr style="border:1px solid black;">
							<th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
						</tr>
					</thead>
					<tbody>
						<?
				$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no' and entry_form=42");
				if ( count($data_array)>0)
				{
					$i=0;
					foreach( $data_array as $row )
					{
						$i++;
						?>
						<tr id="settr_1" align="" style="border:1px solid black;">
							<td style="border:1px solid black;">
								<? echo $i;?>
							</td>
							<td style="border:1px solid black;">
								<? echo $row[csf('terms')]; ?>
							</td>
						</tr>
						<?
					}
				}
				else
				{
					$i=0;
					$data_array=sql_select("select id, terms from lib_terms_condition where is_default=1 and page_id=42 order by id asc ");
					foreach( $data_array as $row )
					{
						$i++;
						?>
						<tr id="settr_1" align="" style="border:1px solid black;">
							<td style="border:1px solid black;">
								<? echo $i;?>
							</td>
							<td style="border:1px solid black;">
								<? echo $row[csf('terms')]; ?>
							</td>
						</tr>
						<?
					}
				}
				?>
			</tbody>
			</table>
				<?
		}
			?>

	</div>


	<div>

	<br><br>
	<div style="margin-left: -20%;" align="center">
		<strong>Note: This is Software Generated Copy, Signature is not required</strong>
	</div>



	</div>
	<? if($entry_form == 42){?>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquerybarcode.js"></script>
	<? }else{?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<? }?>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}


?>
