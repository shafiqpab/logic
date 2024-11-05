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
//$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
//$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
if ($action=="variable_chack")
{
	extract($_REQUEST);
	//echo "$company";
	$company=str_replace("'","",$company);
	$variable_setting=return_field_value("color_from_library","variable_order_tracking","variable_list=24 and company_name=$company");
	echo $variable_setting;
	exit;

}

if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=28 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();	
}
if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id and c.tag_company=$data and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type=2) and a.id in(select supplier_id from lib_supplier_party_type where party_type=21) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
	exit;
} 

/*if($action=="check_conversion_rate")
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
}*/


if($action=="check_conversion_rate") //Conversion Exchange Rate
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
	exit;
}



if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit;
} 

if ($action=="load_drop_down_color")
{
	if($db_type==0)
	{
		$color_ids= return_field_value("group_concat(distinct stripe_color) as color_number_id","wo_pre_stripe_color","job_no='$data' and status_active=1 and is_deleted=0","color_number_id");
	}
	else if($db_type==2)
	{
		//echo "select listagg(stripe_color,',') within group (order by stripe_color) as color_number_id from wo_pre_stripe_color where job_no='$data'";
		$color_ids= return_field_value("LISTAGG(cast(stripe_color as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY stripe_color) as color_number_id","wo_pre_stripe_color","job_no='$data' and status_active=1 and is_deleted=0","color_number_id");
		
	}
	
	$stripe_color_no=implode(",",array_unique(explode(",",$color_ids)));
	if(empty($stripe_color_no)) $stripe_color_no=0;
	echo create_drop_down( "txt_yern_color", 90, "select id,color_name from  lib_color where id in($stripe_color_no) ","id,color_name", 1, "-- Select color--", $selected, "get_php_form_data(document.getElementById('txt_job_no').value+'**'+this.value+'**'+document.getElementById('update_id').value+'**'+document.getElementById('dtls_update_id').value, 'populate_budge_req_data', 'requires/yarn_dyeing_booking_without_lot_controller' )" );
	exit;
}

if ($action=="load_drop_down_compisition")
{
	if($db_type==0)
	{
		$comp_ids= return_field_value("group_concat(distinct copm_one_id) as copm_one_id","wo_pre_cost_fab_yarn_cost_dtls","job_no='$data' and status_active=1 and is_deleted=0","copm_one_id");
	}
	else if($db_type==2)
	{
		//echo "select listagg(stripe_color,',') within group (order by stripe_color) as color_number_id from wo_pre_stripe_color where job_no='$data'";
		$comp_ids= return_field_value("LISTAGG(cast(copm_one_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY copm_one_id) as copm_one_id","wo_pre_cost_fab_yarn_cost_dtls","job_no='$data' and status_active=1 and is_deleted=0","copm_one_id");
		
	}
	
	$stripe_comp_ids=implode(",",array_unique(explode(",",$comp_ids)));
	if(empty($stripe_comp_ids)) $stripe_comp_ids=0;
	echo create_drop_down( "cbo_composition", 100, $composition,"", 1, "-- Select --", 0, "",0,"$stripe_comp_ids" );
	exit;
} 

if ($action=="load_drop_down_yarn_type")
{
	if($db_type==0)
	{
		$yarn_type_ids= return_field_value("group_concat(distinct type_id) as type_id","wo_pre_cost_fab_yarn_cost_dtls","job_no='$data' and status_active=1 and is_deleted=0","type_id");
	}
	else if($db_type==2)
	{
		//echo "select listagg(stripe_color,',') within group (order by stripe_color) as color_number_id from wo_pre_stripe_color where job_no='$data'";
		$yarn_type_ids= return_field_value("LISTAGG(cast(type_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY type_id) as type_id","wo_pre_cost_fab_yarn_cost_dtls","job_no='$data' and status_active=1 and is_deleted=0","type_id");
		
	}
	
	$stripe_yarn_type_ids=implode(",",array_unique(explode(",",$yarn_type_ids)));
	if(empty($stripe_yarn_type_ids)) $stripe_yarn_type_ids=0;
	echo create_drop_down( "cbo_yarn_type", 80, $yarn_type,"", 1, "-- Select --", 0, "",0,"$stripe_yarn_type_ids" );
	exit;
}

 

if($action=="populate_budge_req_data")
{
	$data_ref=explode("**",$data);
	if($data_ref[2]>0) $mst_cond="  and mst_id!=$data_ref[2]"; else $mst_cond="";
	if($data_ref[3]>0) $dtls_cond="  and id!=$data_ref[3]"; else $dtls_cond="";
	$stripe_required=return_field_value("sum(fabreqtotkg) as fabreqtotkg"," wo_pre_stripe_color","job_no='$data_ref[0]' and stripe_color=$data_ref[1] and status_active=1 and is_deleted=0 group by job_no, stripe_color","fabreqtotkg");
	$prev_booking=return_field_value("sum(yarn_wo_qty) as yarn_wo_qty"," wo_yarn_dyeing_dtls","job_no='$data_ref[0]' and yarn_color=$data_ref[1] and status_active=1 and is_deleted=0 $dtls_cond group by job_no, yarn_color","yarn_wo_qty");
	$cum_bal=$stripe_required-$prev_booking;
	echo "$('#txt_budget_wo_qty').val('".number_format($cum_bal,2,'.','')."');\n";
	exit;

}



$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$count_arr=return_library_array( "Select id, yarn_count from  lib_yarn_count where  status_active=1",'id','yarn_count');
$brand_arr=return_library_array( "Select id, brand_name from  lib_brand where  status_active=1",'id','brand_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$fabric_description_arr=return_library_array( "select id, fabric_description from wo_pre_cost_fabric_cost_dtls",'id','fabric_description');
$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");



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
 
<div align="center" style="width:815px;" >
<form name="searchjob"  id="searchjob" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th width="145">Company</th>
                <th width="145">Buyer</th>
                <th width="80">Job No</th>
                <th width="80">Style No</th>
                <th width="80">Order No</th>
                <th width="80">File No</th>
                <th width="80">Internal Ref. No</th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:85px" class="formbutton" onClick="reset_form('searchjob','search_div','')"  /></th>           
            </thead>
            <tbody>
                <tr>
                    <td>
					<? 
                    	echo create_drop_down( "cbo_company_name", 145, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$company)/*$selected */, "load_drop_down( 'yarn_dyeing_booking_without_lot_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                    ?>
                    </td>
                    <td align="center" id="buyer_td">				
					<?
                    	$blank_array="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
						//echo create_drop_down( "cbo_buyer_name", 130, $blank_array, 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
						echo create_drop_down( "cbo_buyer_name", 145, $blank_array,"id,buyer_name", 1, "-- Select Buyer --",0);
                    ?>	
                    </td>    
                    <td align="center">
                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:75px" />
                     </td>
                     <td align="center">
                        <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:75px" />
                     </td>
                     <td align="center">
                        <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px" />
                     </td>
                     <td align="center">
                        <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:75px" />
                     </td>
                     <td align="center">
                        <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:75px" />
                     </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_job_search_list_view', 'search_div', 'yarn_dyeing_booking_without_lot_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:85px;" />				
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
}

if ($action=="create_job_search_list_view")
{
	$data=explode("_",$data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_buyer_name=str_replace("'","",$data[1]);
	$txt_job_no=str_replace("'","",$data[2]);
	$txt_style_no=str_replace("'","",$data[3]);
	$txt_order_no=str_replace("'","",$data[4]);
	$txt_file_no=str_replace("'","",$data[5]);
	$txt_ref_no=str_replace("'","",$data[6]);
	
	//echo $cbo_company_name."**".$txt_style_no."**".$txt_job_no."<br>";die;
	$sql_cond="";
	if($cbo_company_name!=0) $sql_cond=" and a.company_name='$cbo_company_name'";
	if($cbo_buyer_name!=0) $sql_cond.=" and a.buyer_name='$cbo_buyer_name'";
	if($txt_job_no!="") $sql_cond.=" and a.job_no like '%$txt_job_no%'";
	if($txt_style_no!="") $sql_cond.=" and a.style_ref_no like '%$txt_style_no%'";
	if($txt_order_no!="") $sql_cond.=" and b.po_number like '%$txt_order_no%'";
	if($txt_file_no!="") $sql_cond.=" and b.file_no like '%$txt_file_no%'";
	if($txt_ref_no!="") $sql_cond.=" and b.grouping like '%$txt_ref_no%'";
	
	if($db_type==0)
	{
		$sql="select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, group_concat(b.po_number) as po_number, group_concat(b.file_no) as file_no, group_concat(b.grouping) as grouping, year(a.insert_date) as year from  wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.approved=1 $sql_cond group by a.job_no";
	}
	else if($db_type==2)
	{
		$sql="select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, listagg(cast(b.po_number as varchar(4000)),',')  within group (order by b.po_number) as po_number, listagg(cast(b.file_no as varchar(4000)),',')  within group (order by b.file_no) as file_no, listagg(cast(b.grouping as varchar(4000)),',')  within group (order by b.grouping) as  grouping, to_char(a.insert_date,'YYYY') as year from  wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst  and a.job_no=c.job_no and c.approved=1 $sql_cond group by  a.id,a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,a.insert_date";	
	}
	//echo $sql;
	//$arr=array(2=>$buyer_arr);
	
	
//	echo  create_list_view("list_view", "Job No, Year ,Buyer, Style Ref.NO, Order No.","70,80,100,120,170","590","260",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,buyer_name,0,0", $arr, "job_no_prefix_num,company_name,buyer_name,style_ref_no,po_number", "",'','0,0,0,0,0,0') ;	
	echo '<input type="hidden" id="hidden_tbl_id">';
	?>
<div style="width:810px;"align="left">

        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="50">Year</th>
                <th width="60">Job No</th>
                <th width="130">Buyer</th>
                <th width="100"> Style Ref.NO</th>
                <th width="100"> File No</th>
                <th width="100"> Internal Ref. No</th>
                <th >Order No.</th>
               
            </thead>
        </table>
        <div style="width:808px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table" id="tbl_list_search" >
            <?
			 
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					$po_number=implode(",",array_unique(explode(",",$selectResult[csf("po_number")])));
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$file_no=implode(",",array_unique(explode(",",$selectResult[csf('file_no')])));
					$int_ref_no=implode(",",array_unique(explode(",",$selectResult[csf('grouping')])));
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('job_no')]; ?>'+'_'+'<? echo $file_no; ?>'+'_'+'<? echo $int_ref_no; ?>'); "> 
                    
                     <td width="40"><p> <? echo $i; ?></p></td>
                      <td width="50"  align="center"> <p><? echo $selectResult[csf('year')]; ?></p></td>	
                      <td width="60"  align="center"> <p><? echo $selectResult[csf("job_no_prefix_num")]; ?></p></td>	
                      <td width="130"><p><?  echo  $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>	
                      <td width="100"> <p><?  echo $selectResult[csf('style_ref_no')]; ?></p></td>
                      <td width="100"> <p><?  echo $file_no; ?></p></td>
                      <td width="100"> <p><?  echo $int_ref_no; ?></p></td>	
                      <td ><p> <? echo $po_number;?></p></td>	
                    </tr>
                <?
                	$i++;
				}
			?>
            </table>
        </div>
        </div>
	 <?	
	
	
	
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo $cbo_source;die;
	//echo $update_id;die;
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_pro_id=str_replace("'","",$txt_pro_id);
	$cbo_count=str_replace("'","",$cbo_count);
	$txt_item_des=str_replace("'","",$txt_item_des);
	$color_id=str_replace("'","",$color_id);
	$cbo_color_range=str_replace("'","",$cbo_color_range);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$txt_yern_color=str_replace("'","",$txt_yern_color);
	$is_short=str_replace("'","",$cbo_is_short);
	$txt_app_batch_no=str_replace("'","",$txt_app_batch_no);

	$txt_item_desc = $composition[str_replace("'","",$cbo_composition)]." ".str_replace("'","",$txt_pacent)."% ".$yarn_type[str_replace("'","",$cbo_yarn_type)];
	
	//echo $cbo_company_name."**".$cbo_buyer_name."**".$txt_job_no."**".$cbo_company_name."**".$cbo_buyer_name."**".$txt_job_no; die;
	
	$stripe_required=return_field_value("sum(fabreqtotkg) as fabreqtotkg"," wo_pre_stripe_color","job_no='$txt_job_no' and stripe_color=$txt_yern_color and status_active=1 and is_deleted=0","fabreqtotkg");
	
		
	if ($operation==0) 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$previous_wo_qnty=return_field_value("sum(yarn_wo_qty) as yarn_wo_qty","wo_yarn_dyeing_dtls","job_no='$txt_job_no' and yarn_color=$txt_yern_color and status_active=1 and is_deleted=0","yarn_wo_qty");
		$wo_qnty=str_replace("'","",$txt_wo_qty)+$previous_wo_qnty;
		
		
		/*$duplicate = is_duplicate_field("id","wo_yarn_dyeing_dtls","job_no='$txt_job_no' and product_id='$txt_pro_id' and count='$cbo_count' and yarn_description='$txt_item_des' and yarn_color='$txt_yern_color' and color_range='$cbo_color_range' and referance_no='$txt_ref_no' and entry_form=41 and status_active=1 and is_deleted=0");
		if($duplicate==1) 
		{
			echo "11**Duplicate is Not Allow in Same Job Number.";
			die;
		}*/
		
		if($is_short==2)
		{
			if(($wo_qnty*1)>($stripe_required*1))
			{
				echo "40**Work Order Quantity Does Not Allow More Then Fabric Required.";disconnect($con);die;
			}
		}
		
		/*$color_id=return_id( $txt_yern_color, $color_arr, "lib_color", "id,color_name");*/
		
		
		
		if(str_replace("'","",$update_id)!="") //update
		{
			$id= return_field_value("id"," wo_yarn_dyeing_mst","id=$update_id");//check sys id for update or insert	
			$field_array="company_id*supplier_id*booking_date*delivery_date*delivery_date_end*dy_delivery_date_start*dy_delivery_date_end*currency*ecchange_rate*pay_mode*source*attention*is_short*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_name."*".$cbo_supplier_name."*".$txt_booking_date."*".$txt_delivery_date."*".$txt_delivery_end."*".$dy_delevery_start."*".$dy_delevery_end."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$cbo_is_short."*'".$user_id."'*'".$pc_date_time."'*1*0";
			//$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$id,1);	
			$return_no=str_replace("'",'',$txt_booking_no);
		}
		
		else // new insert
 		{			
			$id=return_next_id("id", "wo_yarn_dyeing_mst", 1);			
			// inv_gate_out_mst master table entry here START---------------------------------------//	
			//function return_mrr_number( $company, $location, $category, $year, $num_length, $main_query, $str_fld_name, $num_fld_name, $old_mrr_no )
				//echo $cbo_company_name;die;
				 if($db_type==2)
				{
					$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'YDW', date("Y"), 5, "select yarn_dyeing_prefix,yarn_dyeing_prefix_num from  wo_yarn_dyeing_mst where company_id=$cbo_company_name and  TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id DESC ", "yarn_dyeing_prefix", "yarn_dyeing_prefix_num",""));
				}
				 if($db_type==0)
				{
					$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'YDW', date("Y"), 5, "select yarn_dyeing_prefix,yarn_dyeing_prefix_num from  wo_yarn_dyeing_mst where company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by id DESC ", "yarn_dyeing_prefix", "yarn_dyeing_prefix_num",""));
				}
			
			//echo $new_sys_number[0];die;
			$field_array="id,yarn_dyeing_prefix,yarn_dyeing_prefix_num,ydw_no,entry_form,company_id,supplier_id,item_category_id,booking_date,delivery_date,delivery_date_end,dy_delivery_date_start,dy_delivery_date_end,currency,ecchange_rate,pay_mode,source,attention,is_short,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',125,".$cbo_company_name.",".$cbo_supplier_name.",".$cbo_item_category_id.",".$txt_booking_date.",".$txt_delivery_date.",".$txt_delivery_end.",".$dy_delevery_start.",".$dy_delevery_end.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$cbo_is_short.",'".$user_id."','".$pc_date_time."',1,0)";
			//echo $field_array."<br>".$data_array;die;
			//$rID=sql_insert("wo_yarn_dyeing_mst",$field_array,$data_array,1); 		
			// inv_gate_in_mst master table entry here END---------------------------------------// 
			$return_no=str_replace("'",'',$new_sys_number[0]);
		}
		
		$dtlsid=return_next_id("id","wo_yarn_dyeing_dtls", 1);		
  		$field_array_dts="id,mst_id,job_no,product_id,job_no_id,entry_form,count,yarn_description,yarn_comp_type1st,yarn_comp_percent1st,yarn_type,yarn_color,color_range,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag,no_of_cone,min_require_cone,file_no,internal_ref_no,remarks,referance_no,status_active,is_deleted,app_batch_no";
		$data_array_dts="(".$dtlsid.",".$id.",'".$txt_job_no."','".$txt_pro_id."',".$txt_job_id.",125,'".$cbo_count."','".$txt_item_desc."',".$cbo_composition.",".$txt_pacent.",".$cbo_yarn_type.",".$txt_yern_color.",'".$cbo_color_range."',".$cbo_uom.",".$txt_wo_qty.",".$txt_dyeing_charge.",".$txt_amount.",".$txt_bag.",".$txt_cone.",".$txt_min_req_cone.",".$txt_file_no.",".$txt_int_ref_no.",".$txt_remarks.",'".$txt_ref_no."',1,0,'".$txt_app_batch_no."')";
 		//echo $field_array_dts."<br>".$data_array_dts;die;
		//echo "0**"."INSERT INTO wo_yarn_dyeing_dtls (".$field_array_dts.") VALUES ".$data_array_dts;die;
 		//$dtlsrID=sql_insert("wo_yarn_dyeing_dtls",$field_array_dts,$data_array_dts,1); 	
		//echo "mm";die;
		
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
		//table lock here 
		//check_table_status( $_SESSION['menu_id'],0);
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		//check update id
		if( str_replace("'","",$update_id) == "")
		{
			echo "15";disconnect($con);exit(); 
		}
		$previous_wo_qnty=return_field_value("sum(yarn_wo_qty) as yarn_wo_qty","wo_yarn_dyeing_dtls","id<>$dtls_update_id and job_no='$txt_job_no' and yarn_color=$txt_yern_color and status_active=1 and is_deleted=0","yarn_wo_qty");
		$wo_qnty=str_replace("'","",$txt_wo_qty)+$previous_wo_qnty;
		
		/*$duplicate = is_duplicate_field("id","wo_yarn_dyeing_dtls"," id!=$dtls_update_id and job_no='$txt_job_no' and product_id='$txt_pro_id' and count='$cbo_count' and yarn_description='$txt_item_des' and yarn_color='$txt_yern_color' and color_range='$cbo_color_range' and referance_no='$txt_ref_no' and entry_form=41");
		
		if($duplicate==1) 
		{
			echo "11**Duplicate is Not Allow in Same Job Number.";
			die;
		}*/
		
		if($is_short==2)
		{
			if(($wo_qnty*1)>($stripe_required*1))
			{
				echo "40**Work Order Quantity Does Not Allow More Then Fabric Required.";disconnect($con);die;
			}
		}
		
		/*$txt_yern_color=return_id( $txt_yern_color, $color_arr, "lib_color", "id,color_name");*/
		
		//wo_yarn_dyeing_mst master table UPDATE here START----------------------//	".$txt_pro_id.",
		$field_array="company_id*supplier_id*booking_date*delivery_date*delivery_date_end*dy_delivery_date_start*dy_delivery_date_end*currency*ecchange_rate*pay_mode*source*attention*is_short*updated_by*update_date*status_active*is_deleted";
		$data_array="".$cbo_company_name."*".$cbo_supplier_name."*".$txt_booking_date."*".$txt_delivery_date."*".$txt_delivery_end."*".$dy_delevery_start."*".$dy_delevery_end."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$cbo_is_short."*'".$user_id."'*'".$pc_date_time."'*1*0";
		
		
		//inv_gate_in_mst master table UPDATE here END---------------------------------------// 
		
		//inv_gate_in_dtls details table UPDATE here START-----------------------------------//	

 		$field_array_dtls = "job_no*product_id*job_no_id*count*yarn_description*yarn_comp_type1st*yarn_comp_percent1st*yarn_type*yarn_color*color_range*uom*yarn_wo_qty*dyeing_charge*amount*no_of_bag*no_of_cone*min_require_cone*file_no*internal_ref_no*remarks*referance_no*app_batch_no";
 		$data_array_dtls = "'".$txt_job_no."'*'".$txt_pro_id."'*".$txt_job_id."*".$cbo_count."*'".$txt_item_desc."'*".$cbo_composition."*".$txt_pacent."*".$cbo_yarn_type."*".$txt_yern_color."*".$cbo_color_range."*".$cbo_uom."*".$txt_wo_qty."*".$txt_dyeing_charge."*".$txt_amount."
*".$txt_bag."*".$txt_cone."*".$txt_min_req_cone."*".$txt_file_no."*".$txt_int_ref_no."*".$txt_remarks."*'".$txt_ref_no."'*'".$txt_app_batch_no."'";
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
		$txt_booking_no=str_replace("'","",$txt_booking_no);
		// master table delete here---------------------------------------
		if($update_id=="" || $update_id==0){ echo "15**0"; disconnect($con);die;}
		
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

if($action=="show_dtls_list_view")
{
	
	//echo "$data";die;
 	//$sql = "select a.id, a.job_no, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom,yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.referance_no, b.file_no, b.internal_ref_no from wo_yarn_dyeing_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.mst_id='$data'";
	
	if($db_type==0)
	{
		$sql = "select a.id, a.job_no, a.count, a.yarn_description, a.app_batch_no, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.referance_no, a.yarn_comp_type1st, a.yarn_comp_percent1st, group_concat(b.file_no) as file_no, group_concat(b.grouping) as internal_ref_no from wo_yarn_dyeing_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.mst_id='$data' group by a.id, a.job_no, a.count, a.yarn_description, a.app_batch_no, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.referance_no, a.yarn_comp_type1st, a.yarn_comp_percent1st";
	}
	else
	{
		$sql = "select a.id, a.job_no, a.count, a.yarn_description, a.app_batch_no, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.referance_no, a.yarn_comp_type1st, a.yarn_comp_percent1st, listagg(cast(b.file_no as varchar(4000)), ',') within group(order by b.file_no) as file_no, listagg(cast(b.grouping as varchar(4000)), ',') within group(order by b.grouping) as internal_ref_no from wo_yarn_dyeing_dtls a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.mst_id='$data' group by a.id, a.job_no, a.count, a.yarn_description, a.app_batch_no, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.referance_no, a.yarn_comp_type1st, a.yarn_comp_percent1st";
	}
	$sql_result=sql_select($sql);
	
	/*$arr=array(1=>$count_arr,3=>$color_arr,4=>$unit_of_measurement);

 	echo create_list_view("list_view", "Job No,Count,Description,Color,UOM,WO QTY,Charge,Amount,No of Bag,No of Cone,Minimum Require Cone,Ref NO,File No,Internal Ref. No","80,60,150,100,60,70,70,80,60,60,70,100,80","1200","260",0, $sql, "get_php_form_data", "id", "'child_form_input_data','requires/yarn_dyeing_booking_without_lot_controller'", 1, "0,count,0,yarn_color,uom,0,0,0,0,0,0,0,0,0", $arr, "job_no,count,yarn_description,yarn_color,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag,no_of_cone,min_require_cone,referance_no,file_no,internal_ref_no", "","",'0,0,0,0,0,1,1,2,1,1,1,0,0,0',"");*/
	if(count($sql_result)>0)
	{
		?>
		<table width="1280" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="80">Job No</th>
					<th width="60">Count</th>
					<th width="150">Description</th>
					<th width="100">Color</th>
					<th width="60">UOM</th>
					<th width="70">WO QTY</th>
					<th width="70">Charge</th>
					<th width="80">Amount</th>
					<th width="80">App. Batch No</th>
					<th width="60">No of Bag</th>
					<th width="60">No of Cone</th>
					<th width="70">Minimum Require Cone</th>
					<th width="90">Ref NO</th>
					<th width="90">File No</th>
					<th >Internal Ref. No</th>
				</tr>
			</thead>
			<tbody>
			<?
			$i=1;
			
			foreach($sql_result as $row)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$yarn_descirption=$composition[$row[csf("yarn_comp_type1st")]]." ".$row[csf("yarn_comp_percent1st")]."%";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onClick="get_php_form_data(<? echo $row[csf("id")]; ?>, 'child_form_input_data', 'requires/yarn_dyeing_booking_without_lot_controller' );">
					<td><p><? echo $i; ?></p></td>
					<td><p><? echo $row[csf("job_no")]; ?>&nbsp;</p></td>
					<td><p><? echo $count_arr[$row[csf("count")]]; ?>&nbsp;</p></td>
					<td><p><? echo $yarn_descirption; ?>&nbsp;</p></td>
					<td><p><? echo $color_arr[$row[csf("yarn_color")]]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?>&nbsp;</p></td>
					<td align="right"><? echo number_format($row[csf("yarn_wo_qty")],0); ?></td>
					<td align="right"><? echo number_format($row[csf("dyeing_charge")],2); ?></td>
					<td align="right"><? echo number_format($row[csf("amount")],2); ?></td>

					<td align="center"><p><? echo $row[csf("app_batch_no")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("no_of_bag")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("no_of_cone")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("min_require_cone")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf("referance_no")]; ?>&nbsp;</p></td>
					<td align="center"><p><? echo implode(",",array_unique(explode(",",$row[csf("file_no")]))); ?>&nbsp;</p></td>
					<td align="center"><p><? echo implode(",",array_unique(explode(",",$row[csf("internal_ref_no")]))); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<tbody>
		</table>
		<?
	}
	exit();
		
}

if($action=="child_form_input_data")
{
	//echo $data;
	
	/*if($db_type==0)
	{
		$order_no_arr = return_library_array( "select a.id, group_concat(distinct b.po_number) as po_number  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst group by a.id",'id','po_number');

	}
	else
	{
		$order_no_arr = return_library_array( "select a.id, LISTAGG(CAST(b.po_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) as po_number  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst group by a.id",'id','po_number');
	}*/
	if($db_type==0)
	{
		$sql = "select a.id, a.mst_id, a.job_no, a.app_batch_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_type, group_concat(b.file_no) as file_no, group_concat(b.grouping) as internal_ref_no
		from wo_yarn_dyeing_dtls a, wo_po_break_down b 
		where a.job_no=b.job_no_mst and a.id='$data' 
		group by a.id, a.mst_id, a.job_no, a.app_batch_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_type";
	}
	else
	{
		$sql = "select a.id, a.mst_id, a.job_no, a.app_batch_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_type, listagg(cast(b.file_no as varchar(4000)), ',') within group(order by b.file_no) as file_no, listagg(cast(b.grouping as varchar(4000)), ',') within group(order by b.grouping) as internal_ref_no 
		from wo_yarn_dyeing_dtls a, wo_po_break_down b 
		where a.job_no=b.job_no_mst and a.id='$data' 
		group by a.id, a.mst_id, a.job_no, a.app_batch_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_type";
	}
	
	
	//echo $sql;
	$sql_re=sql_select($sql);
	foreach($sql_re as $row)
	{
		echo "$('#txt_job_no').val('".$row[csf("job_no")]."');\n";
		echo "$('#txt_job_id').val('".$row[csf("job_no_id")]."');\n";
		echo "$('#txt_pro_id').val(".$row[csf("product_id")].");\n";
		$lot=return_field_value("lot"," product_details_master","id=".$row[csf("product_id")]."","lot");
		$job_ref=$row[csf("job_no")];
		echo "load_drop_down( 'requires/yarn_dyeing_booking_without_lot_controller','$job_ref', 'load_drop_down_color', 'color_td' );\n";
		echo "load_drop_down( 'requires/yarn_dyeing_booking_without_lot_controller','$job_ref', 'load_drop_down_compisition', 'composition_td' );\n";
		echo "load_drop_down( 'requires/yarn_dyeing_booking_without_lot_controller','$job_ref', 'load_drop_down_yarn_type', 'yarn_type_td' );\n";
		
		echo "$('#txt_lot').val('$lot');\n";
		echo "$('#cbo_count').val(".$row[csf("count")].");\n";
		echo "$('#txt_item_des').val('".$row[csf("yarn_description")]."');\n";
		echo "$('#cbo_composition').val(".$row[csf("yarn_comp_type1st")].");\n";
		echo "$('#txt_pacent').val('".$row[csf("yarn_comp_percent1st")]."');\n";
		echo "$('#cbo_yarn_type').val('".$row[csf("yarn_type")]."');\n";

		echo "$('#txt_app_batch_no').val('".$row[csf("app_batch_no")]."');\n";
		
		echo "$('#txt_yern_color').val(".$row[csf("yarn_color")].");\n";
		echo "$('#cbo_color_range').val(".$row[csf("color_range")].");\n";
		echo "$('#cbo_uom').val(".$row[csf("uom")].");\n";
		echo "$('#txt_wo_qty').val(".$row[csf("yarn_wo_qty")].");\n";
		//echo "select sum(fabreqtotkg) as fabreqtotkg from  wo_pre_stripe_color where job_no='".$row[csf("job_no")]."' and stripe_color=".$row[csf("yarn_color")].";\n";
		$stripe_required=return_field_value("sum(fabreqtotkg) as fabreqtotkg"," wo_pre_stripe_color","job_no='".$row[csf("job_no")]."' and stripe_color=".$row[csf("yarn_color")]." and status_active=1 and is_deleted=0 group by job_no, stripe_color","fabreqtotkg");
		if($stripe_required>0) $stripe_required=$stripe_required; else $stripe_required=0;
		$prev_booking=return_field_value("sum(yarn_wo_qty) as yarn_wo_qty "," wo_yarn_dyeing_dtls "," job_no='".$row[csf("job_no")]."' and yarn_color=".$row[csf("yarn_color")]." and mst_id!=".$row[csf("mst_id")]." and status_active=1 and is_deleted=0 group by job_no,yarn_color","yarn_wo_qty");
		
		$cu_bal=$stripe_required-$prev_booking;
		$cu_bal=number_format($cu_bal,2,'.','');
		
		echo "$('#txt_budget_wo_qty').val(".$cu_bal.");\n";
		
		echo "$('#txt_dyeing_charge').val(".$row[csf("dyeing_charge")].");\n";
		echo "$('#txt_amount').val(".$row[csf("amount")].");\n";		
 		echo "$('#txt_bag').val(".$row[csf("no_of_bag")].");\n";
		echo "$('#txt_cone').val(".$row[csf("no_of_cone")].");\n";
		echo "$('#txt_min_req_cone').val(".$row[csf("min_require_cone")].");\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#txt_ref_no').val('".$row[csf("referance_no")]."');\n";
		$file_no=implode(",",array_unique(explode(",",$row[csf("file_no")])));
		echo "$('#txt_file_no').val('".$file_no."');\n";
		$internal_ref_no=implode(",",array_unique(explode(",",$row[csf("internal_ref_no")])));
		echo "$('#txt_int_ref_no').val('".$internal_ref_no."');\n";
		//update id here
		echo "$('#dtls_update_id').val(".$row[csf("id")].");\n";		
		echo "set_button_status(1, permission, 'fnc_yarn_dyeing',1,0);\n";
	}
}



if ($action=="yern_dyeing_booking_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST); 
	//echo "$company";die; 
	if($db_type==0) $select_field_grp="group by a.id order by supplier_name"; 
	else if($db_type==2) $select_field_grp="group by a.id,a.supplier_name order by supplier_name";
	
	$current_date=date('d-m-Y');
	//$previous_date=strtotime($current_date)-(60*60*60*24);
	//$previous_day=date('d-m-Y',$previous_date);
	$previous_day=date("d-m-Y",strtotime(date("d-m-Y"). '-60 days'));
	
	//echo $current_date."##".$previous_day;die;
	
?>
     
<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) 
			document.getElementById('chk_job_wo_po').value=1;
		else 
			document.getElementById('chk_job_wo_po').value=0;
	}
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
    <div style="width:930px;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="900" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
        
            <thead>
            	<tr>
                	<th colspan="2"> </th>
                    <th  >
                      <?
                       echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                      ?>
                    </th>
                    <th colspan="3" style="text-align:right"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Job</th>
                </tr>
                <tr>
                	<th width="170">Buyer Name</th>
                    <th  width="170">Supplier Name</th>
                    <th width="100">Booking No</th>
                    <th width="100">Job No</th>
                    <th  width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
                </tr>
             </thead>
            <tbody>
                <tr>
                	<td align="center">
					<?
					echo create_drop_down( "cbo_buyer_name", 172, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0);
					?>
                    </td>
                    <td align="center">
					<?
                    echo create_drop_down( "cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id and c.tag_company=$company and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type=2) and a.id in(select supplier_id from lib_supplier_party_type where party_type=21) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
                    ?>
                    </td>
                     <td align="center"><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                    <td align="center"><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" value="<? //echo $previous_day; ?>" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" value="<? //echo $current_date; ?>" />
                     </td> 
                     <td align="center">
                       <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('chk_job_wo_po').value, 'create_sys_search_list_view', 'search_div', 'yarn_dyeing_booking_without_lot_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />				
                    </td>
                </tr>
                <tr>                  
                    <td align="center" height="40" valign="middle" colspan="6">
                        <? echo load_month_buttons(1);  ?>
                        <!-- Hidden field here-------->
                       <!-- input type="hidden" id="hidden_tbl_id" value="" ---->
                       
                        <input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
                         <input type="hidden" id="hidden_id" value="hidden_id" />
                        <!-- ---------END-------------> 
                    </td>
                </tr>    
            </tbody>
        </table> 
        <br>   
        <div align="center" valign="top" id="search_div"></div> 
        </form>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_sys_search_list_view")
{ 

	$contact_person=return_library_array( "select id, contact_person from lib_supplier",'id','contact_person');

	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val=$ex_data[4];
	$chk_job_wo_po=trim($ex_data[9]);
	//echo $buyer_val;die;
 	//$sql_cond=""; LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
	/*if($db_type==0)
	{
		$order_no_arr = return_library_array( "select a.id, group_concat(distinct b.po_number) as po_number  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst group by a.id",'id','po_number');

	}
	else
	{
		$order_no_arr = return_library_array( "select a.id, LISTAGG(CAST(b.po_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_number) as po_number  from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst group by a.id",'id','po_number');
	}*/
	
	
	//var_dump($order_no_arr);die;
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";
	if($db_type==0) 
	{
	 $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$ex_data[8]";
	 $year_cond=" and SUBSTRING_INDEX(d.insert_date, '-', 1)=$ex_data[8]"; 
	 if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	if($db_type==2) 
	{
	  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[8]";
	  $year_cond=" and to_char(d.insert_date,'YYYY')=$ex_data[8]";
	  if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";
	}
	
	//TO_CHAR(insert_date,'YYYY')
	/*$sql = "select id, yarn_dyeing_prefix_num, ydw_no, company_id, supplier_id, booking_date, delivery_date, currency, ecchange_rate, pay_mode,source, attention from  wo_yarn_dyeing_mst where  status_active=1 and is_deleted=0 $supplier $company $sql_cond";*/
	
	if($ex_data[5]==4 || $ex_data[5]==0)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num like '%$ex_data[7]%' $year_cond "; else  $job_cond=""; 
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
	}
    if($ex_data[5]==1)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num ='$ex_data[7]' "; else  $job_cond=""; 
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num ='$ex_data[6]'   "; else $booking_cond="";
	}
   if($ex_data[5]==2)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num like '$ex_data[7]%'  $year_cond"; else  $job_cond=""; 
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
	}
	if($ex_data[5]==3)
	{
		if (str_replace("'","",$ex_data[7])!="") $job_cond=" and d.job_no_prefix_num like '%$ex_data[7]'  $year_cond"; else  $job_cond=""; 
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]'  $booking_year_cond  "; else $booking_cond="";
	}
	
	if($db_type==0) $select_year="year(a.insert_date) as year"; else $select_year="to_char(a.insert_date,'YYYY') as year";
	if($chk_job_wo_po==1)
	{
		$sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number  
		from wo_yarn_dyeing_mst a
		where a.status_active=1 and a.is_deleted=0 and a.entry_form=125 and a.id not in(select mst_id from wo_yarn_dyeing_dtls where job_no_id>0 and entry_form=125  and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond";
	}
	else
	{
		if($db_type==0)
		{
			$sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year,group_concat(distinct b.job_no_id) as job_no_id, group_concat(distinct b.job_no) as job_no,d.buyer_name, group_concat(distinct e.po_number) as po_number  
			from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, wo_po_details_master d
			where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=125 and b.entry_form=125 $company $supplier  $sql_cond  $buyer_cond $job_cond $booking_cond
			group by a.id";
		}
		//LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
		else if($db_type==2)
		{
			$sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, d.buyer_name, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id
			from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, wo_po_details_master d
			where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.entry_form=125 and b.entry_form=125 $company $supplier  $sql_cond  $buyer_cond  $job_cond $booking_cond
			group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,d.buyer_name";
		}
		$nameArray=sql_select( $sql );
		$all_job_id="";
		foreach($nameArray as $row)
		{
			$all_job_id.=$row[csf("job_no_id")].",";
		}
		//echo $all_job_id;die;
		$all_job_id=array_chunk(array_unique(explode(",",chop($all_job_id,","))),999);
		
		$po_sql="select p.mst_id as mst_id, b.id, b.po_number from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b where p.job_no_id=a.id and a.job_no=b.job_no_mst";
		$p=1;
		foreach($all_job_id as $job_id)
		{
			//$po_sql
			if($p==1) $po_sql .=" and (a.id in(".implode(',',$job_id).")"; else $po_sql .=" or a.id in(".implode(',',$job_id).")";
			$p++;
		}
		$po_sql .=")";
		
		//echo $po_sql;die;
		
		$po_result=sql_select($po_sql);
		$po_data=array();
		foreach($po_result as $row)
		{
			$po_data[$row[csf("mst_id")]].=$row[csf("po_number")].",";
		}
		
	}
	
	//echo $po_data[524];die;
	
	//echo "<pre>";print_r($po_data);die;
	//echo $sql;
	 
	//echo $sql;//die;
	//echo $sql_cond; die;
	
	//$sample_arr = return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	//$arr=array(4=>$buyer_arr,5=>$supplier_arr);
	//function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all ,$new_conn ){}
	//echo create_list_view("list_view4", "Booking no,Year,Job No,Order No,Buyer Name,Supplier Name,Booking Date,Delevary Date","60,60,150,150,110,130,70,75","880","300",0, $sql , "js_set_value", "id,ydw_no", "", 1, "0,0,0,0,buyer_name,supplier_id,0,0", $arr, "yarn_dyeing_prefix_num,year,job_no,po_number,buyer_name,supplier_id,booking_date,delivery_date", "yarn_dyeing_booking_without_lot_controller",'','0,0,0,0,0,0,3,3') ;	
?>	<div style="width:930px; "  align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="50">Booking no</th>
                <th width="40">Year</th>
                <th width="120">Job No</th>
                <th width="300">Order No</th>
                <th width="100">Buyer Name</th>
                <th width="120">Supplier Name</th>
               
               <!-- <th width="120">Contact PERSON</th> -->

                <th width="70">Booking Date</th>
                <th >Delevary Date</th>
            </thead>
        </table>
        <div style="width:930px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="912" class="rpt_table" id="tbl_list_search" >
            <?
			 
				$i=1;
				$nameArray=sql_select( $sql );
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult)
				{
					
					$job_no=implode(",",array_unique(explode(",",$selectResult[csf("job_no")])));
					$job_no_id=implode(",",array_unique(explode(",",$selectResult[csf("job_no_id")])));
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'); "> 
                    
                     <td width="30" align="center"> <p><? echo $i; ?></p></td>
                      <td width="50" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>	
                      <td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>	
                      <td width="120"><p><?  echo $job_no; ?></p></td>	
                      <td width="300"> <p>
					  <?  
					  $po_no=implode(",",array_unique(explode(",",chop($po_data[$selectResult[csf("id")]],","))));
					  echo $po_no;  
					  ?></p></td>
                      <td width="100"><p> <? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>	
                      <td width="120"> <p><? echo $supplier_arr[$selectResult[csf('supplier_id')]]; ?></p></td>	

                     <!--  <td width="120"> <p><? echo $contact_person[$selectResult[csf('supplier_id')]]; ?></p></td> -->


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
	 

<? exit();
	
}
if($action=="populate_master_from_data")
{
	$sql="select  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention, a.is_short from wo_yarn_dyeing_mst a where  a.id=$data";
	//echo $sql;die;
	$contact_person=return_library_array( "select id, contact_person from lib_supplier",'id','contact_person');
	$res = sql_select($sql);
	foreach($res as $row)
	{	
		echo "$('#txt_booking_no').val('".$row[csf("ydw_no")]."');\n";
		echo "$('#cbo_company_name').val('".$row[csf("company_id")]."');\n"; 		
		//echo "$('#hidden_type').val(".$row[csf("piworeq_type")].");\n";
		echo "$('#cbo_supplier_name').val('".$row[csf("supplier_id")]."');\n";

		echo "$('#txt_attention').val('".$contact_person[$row[csf("supplier_id")]]."');\n";

		echo "$('#txt_booking_date').val('".change_date_format($row[csf("booking_date")])."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		//echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";	
		echo "$('#cbo_currency').val('".$row[csf("currency")]."');\n";
		echo "set_exchang('".$row[csf("currency")]."');\n";
		echo "$('#txt_exchange_rate').val('".$row[csf("ecchange_rate")]."');\n";	
		echo "$('#cbo_pay_mode').val('".$row[csf("pay_mode")]."');\n";
		//echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";	
		echo "$('#cbo_source').val('".$row[csf("source")]."');\n"; 
		echo "$('#txt_delivery_end').val('".change_date_format($row[csf("delivery_date_end")])."');\n"; 
		echo "$('#dy_delevery_start').val('".change_date_format($row[csf("dy_delivery_date_start")])."');\n"; 
		echo "$('#dy_delevery_end').val('".change_date_format($row[csf("dy_delivery_date_end")])."');\n"; 
		echo "$('#update_id').val(".$row[csf("id")].");\n"; //cbo_is_short	
		echo "$('#cbo_is_short').val(".$row[csf("is_short")].");\n"; 	  	
		//right side list view 
		//echo "show_list_view(".$row[csf("piworeq_type")]."+'**'+".$row[csf("pi_wo_req_id")].",'show_product_listview','list_product_container','requires/get_out_entry_controller','');\n";
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
}

/*if($action=="lot_search_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$job_no=str_replace("'","",$job_no);
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
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company ?>+'_'+document.getElementById('txt_lot_search').value, 'create_lot_search_list_view', 'search_div', 'yarn_dyeing_booking_without_lot_controller', 'setFilterGrid(\'table_charge\',-1)')" style="width:100px;" /></td>
        		
                
               
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
	//echo $job_no;die;
	
	if($lot_search!='') $lot_cond="and lot ='$lot_search'";else  $lot_cond="";
	if($company!='') $com_cond="and company_id =$company";else  $com_cond="";
		?>
	
	</head>
	<body>
		<div style="width:585px;" >
			<fieldset>
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table width="585" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center"  rules="all">
						<thead>
							<tr>
								<th width="30">Sl No.</th>
                                <th width="100">Lot No</th>
                                <th width="90">Brand</th>
								<th width="200">Product Name Details</th>
                                <th width="80">Stock</th>
								<th>UOM</th> 
							</tr>
						</thead>
					</table>
					<?
					
					$sql="select id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,brand,current_stock,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color from product_details_master where item_category_id=1 $com_cond $lot_cond";
					
					//echo $sql;
					$sql_result=sql_select($sql);
					?>
            			<div style="width:585px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;">
						<table width="585" cellspacing="0" cellpadding="0" border="0" class="rpt_table"  style="cursor:pointer" rules="all" id="table_charge">
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
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value2('<? echo $composition[$row[csf("yarn_comp_type1st")]]." ". $row[csf("yarn_comp_percent1st")]." ". $composition[$row[csf("yarn_comp_type2nd")]]." ". $row[csf("yarn_comp_percent2nd")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $color_arr[$row[csf("color")]]; ?>,<? echo $row[csf("yarn_count_id")]; ?>,<? echo $row[csf("lot")]; ?>,<? echo $row[csf("id")]; ?>')">
									<td width="30" align="center"><p><? echo $i;  ?></p></td>
                                    <td width="100" align="center"><p><? echo $row[csf("lot")]; ?></p></td>
                                    <td width="90"><p><? echo $brand_arr[$row[csf("brand")]]; ?></p></td>
									<td width="200"><? echo $row[csf("product_name_details")]; ?></p></td>
									<td width="80" align="right"><p><? echo $row[csf("current_stock")]; ?></p></td>
									<td><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td> 
								</tr>
								<?
								$i++;
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
}*/

if($action=="lot_search_popup2")//Old--Not Used
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$job_no=str_replace("'","",$job_no);
	//echo $job_no;die;
		?>
	<script>
		function js_set_value42(str)
		{
			alert(str);
			$("#hidden_product").val(str);
			parent.emailwindow.hide(); 
		}
	</script>
	</head>
	<body>
		<div style="width:595px;" >
			<fieldset>
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table width="595" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
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
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value2('<? echo $composition[$row[csf("yarn_comp_type1st")]]." ". $row[csf("yarn_comp_percent1st")]." ". $composition[$row[csf("yarn_comp_type2nd")]]." ". $row[csf("yarn_comp_percent2nd")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $color_arr[$row[csf("color")]]; ?>,<? echo $row[csf("yarn_count_id")]; ?>,<? echo $row[csf("lot")]; ?>,<? echo $row[csf("id")]; ?>')">
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
							  <input type="text" id="hidden_product" style="width:200px;" />  
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
		http.open("POST","yarn_dyeing_booking_without_lot_controller.php",true);
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
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no order by id");// quotation_id='$data'
					if(count($data_array)>0)
					{
						$button_status=1;
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
						$button_status=0;
						$data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con where is_default=1 ");// quotation_id='$data'
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
                            echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", $button_status,0 ,"reset_form('termscondi_1','','','','')",1) ; 
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
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{ 
$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0 || $operation==1)  // Insert Here and Update Here
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
		$rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		// check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_de3)
			{
				mysql_query("COMMIT");  
				echo $operation."**".$txt_booking_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$txt_booking_no;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3)
			{
				oci_commit($con);
				echo $operation."**".$txt_booking_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$txt_booking_no;
			}
		}
		disconnect($con);
		die;
	}	
	
}



if($action=="show_with_multiple_job")
{
	//echo "xxxx";die;
	//echo load_html_head_contents("Yarn Dyeing WO", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$job_quantity_arr=return_library_array( "select job_no,job_quantity from wo_po_details_master",'job_no','job_quantity');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	
	$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.ecchange_rate, a.is_short from wo_yarn_dyeing_mst a where a.id=$update_id"); 
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
		$exchange_rate=$result[csf('ecchange_rate')];
		$is_short=$result[csf('is_short')];
	}
	
	?>
	<div style="width:1220px" align="center">      
        <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
           <tr>
               <td width="100"> 
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
                                            
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
                            }
                              
							               ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Yarn Dyeing Work Order  <? if($is_short==1) echo " (Short) "; ?> </strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id";
       /* $nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.ecchange_rate from wo_yarn_dyeing_mst a where a.id=$update_id"); 
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
			$exchange_rate=$result[csf('ecchange_rate')];
			
        }*/
		
		
        ?>
       <table width="900" style="" align="center">
       		<tr>
            	<td width="350"  style="font-size:12px">
                        <table width="350" style="" align="left">
                            <tr  >
                                <td   width="120"><b>To</b>   </td>
                                <td width="230">:&nbsp;&nbsp;<? echo $supplier_arr[$supplier_id];?></td>
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
                                <td >:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
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
       
        <table width="1220" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
        	<thead>
            <tr>
             	<th width="30" align="center">Sl</th>
                <th width="65" align="center">Color</th>
                <th width="80" align="center">Color Range</th>
                <th width="70" align="center">File No</th>
                <th width="70" align="center">Internal Ref. No</th>
                <th  align="center" width="50">Ref No.</th>
                <th  align="center" width="70">Style Ref.No.</th>
                <th width="30" align="center">Yarn Count</th>
                <th width="140" align="center">Yarn Description</th>
                <th width="40" align="center">Brand</th>
                <th width="40" align="center">Lot</th>
                <th width="40" align="center">UOM</th>
                <th width="60" align="center">WO Qty</th>
                <th width="50" align="center">Dyeing Rate</th>
                <th width="70" align="center">Amount</th>
                <th  align="center" width="40">Min Req. Cone</th>
                <th  align="center" width="80">Job No.</th>
                <th  align="center" width="80">Buyer</th>
                <th  align="center">Order No</th>
            </tr>
            </thead>
            <?
			
			$product_sql=sql_select("select id, lot, brand from product_details_master");	
			$product_data_array=array();
			foreach($product_sql as $row)
			{
				$product_data_array[$row[csf("id")]]["lot"]=$row[csf("lot")];
				$product_data_array[$row[csf("id")]]["brand"]=$row[csf("brand")];
			}
          
			if($db_type==0)
			{
				$sql="select a.id, a.ydw_no, b.id as dtls_id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty as yarn_wo_qty, b.dyeing_charge, b.amount as amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name, group_concat(d.po_number) as po_number, group_concat(d.file_no) as file_no, group_concat(d.grouping) as internal_ref_no
				from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c, wo_po_break_down d
				where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and b.job_no_id=c.id and c.job_no=d.job_no_mst and a.id=$update_id
				group by a.id, a.ydw_no, b.id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty, b.dyeing_charge, b.amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name order by b.id";
			}
			else
			{
				//listagg(CAST(c.id as VARCHAR(4000)),',') within group (order by c.id) as po_id
				$sql="select a.id, a.ydw_no, b.id as dtls_id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty as yarn_wo_qty, b.dyeing_charge, b.amount as amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name, listagg(CAST(d.po_number as VARCHAR(4000)),',') within group (order by d.po_number) as po_number, listagg(CAST(d.file_no as VARCHAR(4000)),',') within group (order by d.file_no) as file_no, listagg(CAST(d.grouping as VARCHAR(4000)),',') within group (order by d.grouping) as internal_ref_no
				from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c, wo_po_break_down d
				where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and b.job_no_id=c.id and c.job_no=d.job_no_mst and a.id=$update_id
				group by a.id, a.ydw_no, b.id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty, b.dyeing_charge, b.amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name order by b.id";
			}
			
			$sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";$tot_job_no='';$total_dtls_id=0;
			foreach($sql_result as $row)
			{
				
				if($tot_job_no=="") $tot_job_no=$row[csf("job_no")]; else $tot_job_no=$tot_job_no.",".$row[csf("job_no")];
				if($total_dtls_id==0) $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];
				$job_strip_color[$row[csf("job_no")]].=$row[csf("yarn_color")].",";
				$all_stripe_color.=$row[csf("yarn_color")].",";
				
				$product_id=$row[csf("product_id")];
				//var_dump($product_id);
				if($product_id)
				{ 
					$lot_amt=$product_data_array[$product_id]["lot"];
					$brand=$product_data_array[$product_id]["brand"];
				}
				$all_job_arr[]=$row[csf("job_no")];
				$all_style_arr[]=$row[csf("style_ref_no")];
			//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);	

			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";	
			
			?>
            <tr bgcolor="<? echo $bgcolor; ?>">
            	<td align="center"><p><? echo $i; ?></p></td>
                <td><p><? echo $color_arr[$row[csf("yarn_color")]]; ?>&nbsp;</p></td>
                <td><p><? echo $color_range[$row[csf("color_range")]]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("file_no")]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("internal_ref_no")]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $row[csf("referance_no")]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p> </td>
                <td align="center"><p><? echo $count_arr[$row[csf("count")]]; ?>&nbsp;</p></td>
                <td> <p><? echo $row[csf("yarn_description")]; ?> &nbsp;</p></td>
                <td><p><? echo $brand_arr[$brand]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $lot_amt; ?>&nbsp;</p></td>
                <td align="center"><p><? echo "KG"; ?>&nbsp;</p></td>
                <td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?></td>
                <td align="right"><? echo $row[csf("dyeing_charge")]; ?></td>
                <td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?> </td>
                <td align="center"><p><? echo $row[csf("min_require_cone")]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $row[csf("job_no")]; ?>&nbsp;</p></td>
                <td align="center"><p> <? echo $buyer_arr[$row[csf("buyer_name")]]; ?>&nbsp;</p> </td>
                <td align="center"> <div style="width:120px; word-wrap:break-word"> <? echo implode(",",array_unique(explode(",",$row[csf('po_number')]))); ?> </div> </td>
            </tr>
            <?
			$i++;
			$yarn_count_des="";
			$style_no="";
			}
			?>
             <tr>
                <td colspan="12" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
                <td align="right" ><b><? echo $total_qty; ?></b></td>
                <td align="right">&nbsp;</td>
                <td align="right"><b><? echo number_format($total_amount,2); ?></b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
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
            <td colspan="19" align="center">Total Dyeing Amount (in word): &nbsp;<? echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency); //echo number_to_words($total_amount,"USD", "CENTS");?> </td> 
            </tr>
        </table>
        
        
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
        <table width="1220" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>
        
        <table  width="1220" class="rpt_table"    border="0" cellpadding="0" cellspacing="0" align="center">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
            </tr>
        </thead>
        <tbody>
        <?
		//echo "select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no'";
		//echo "select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no'";
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no'");// quotation_id='$data'
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
        $data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con");// quotation_id='$data'
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
    <?
    if($show_comment==1)
	{
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table" >
        <thead>
            <tr> 
                <th colspan="9" align="center"><b> Comments</b> </th>
            </tr>
            <tr>
                <th width="50">SL</th>
                <th width="150">Job No</th>
                <th width="200">PO No</th>
                <th width="200">Ship Date</th>
                <th width="100">Pre-Cost Value</th>
                <th width="110">WO Value</th>
                <th width="110">Short WO Value</th>
                <th width="110">Balance</th>
                <th>Comments </th>
            </tr>
        </thead>
        <tbody>
        <?
		$all_stripe_color=implode(",",array_unique(explode(",",chop($all_stripe_color,","))));
        $job_cond_arr=array_unique(explode(",",$tot_job_no));
        if($db_type==0)
        {
            $job_po_sql=sql_select("select job_no_mst, group_concat(po_number) as po_number, group_concat(shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0 group by job_no_mst");
        }
        else
        {
            $job_po_sql=sql_select("select job_no_mst, listagg(cast(po_number as varchar(4000)),',') within group(order by po_number) as po_number, listagg(cast(shipment_date as varchar(4000)),',') within group(order by shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0 group by job_no_mst");
        }
        $job_po_data=array();
        foreach($job_po_sql as $row)
        {
            $job_po_data[$row[csf("job_no_mst")]]["po_number"]=$row[csf("po_number")];
            $job_po_data[$row[csf("job_no_mst")]]["shipment_date"]=$row[csf("shipment_date")];
        }
        $sql_stripe_required=sql_select("select job_no, stripe_color, fabreqtotkg from wo_pre_stripe_color where job_no in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0");
        $strip_req_data=array();
        foreach($sql_stripe_required as $row)
        {
            $strip_req_data[$row[csf("job_no")]][$row[csf("stripe_color")]]=$row[csf("fabreqtotkg")];
        }
        $sql_strip_rate=sql_select("select job_no, color_break_down from wo_pre_cost_fab_conv_cost_dtls where job_no in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0");
        $strip_req_rate=array();
        foreach($sql_strip_rate as $row)
        {
            $color_data=explode("__",$row[csf("color_break_down")]);
            foreach($color_data as $color_rate)
            {
                $color_rate_ref=explode("_",$color_rate);
                $strip_req_rate[$row[csf("job_no")]][$color_rate_ref[0]]=$color_rate_ref[1]*1;
            }
        }
        $job_wise_badge_val=array();
        foreach($job_strip_color as $job_no=>$strip_color)
        {
            $strip_color_arr=array_unique(explode(",",chop($strip_color,",")));
            foreach($strip_color_arr as $strip_color_id)
            {
                $job_wise_badge_val[$job_no]+=$strip_req_data[$job_no][$strip_color_id]*$strip_req_rate[$job_no][$strip_color_id];
            }
        }
		
		$prev_wo_data=sql_select("select b.job_no_id, b.yarn_color, 
			sum(case when a.is_short<>1 then b.amount else 0 end) as amount, 
			sum(case when a.is_short=1 then b.amount else 0 end) as short_amount 
			from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form=125 and b.status_active=1 and b.is_deleted=0 and b.job_no in('".implode($job_cond_arr,"','")."') and b.id not in($total_dtls_id) group by b.job_no_id, b.yarn_color");
		foreach($prev_wo_data as $row)
		{
			$prev_job_entry[$row[csf("job_no_id")]][$row[csf("yarn_color")]]+=$row[csf("amount")];
			$prev_job_short_entry[$row[csf("job_no_id")]][$row[csf("yarn_color")]]+=$row[csf("short_amount")];
		}
		if($db_type==0)
		{
			$yarn_data="select b.job_no_id, max(a.currency) as currency, b.job_no, 
			sum(case when a.is_short<>1 then b.amount else 0 end) as amount, 
			sum(case when a.is_short=1 then b.amount else 0 end) as short_amount, group_concat(b.yarn_color) as yarn_color
			from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a  
			where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   
			group by b.job_no_id, b.job_no";
		}
		else
		{
			$yarn_data="select b.job_no_id, max(a.currency) as currency, b.job_no, 
			sum(case when a.is_short<>1 then b.amount else 0 end) as amount, 
			sum(case when a.is_short=1 then b.amount else 0 end) as short_amount, 
			listagg( cast(b.yarn_color as varchar(4000)),',') within group (order by b.yarn_color) as yarn_color
			from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a  
			where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   
			group by b.job_no_id, b.job_no";
		}
		
		/*$prev_job_entry=return_library_array( "select b.job_no_id, sum(b.amount) as job_amt from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form=41 and b.status_active=1 and b.is_deleted=0 and b.job_no in('".implode($job_cond_arr,"','")."') and b.id not in($total_dtls_id)  and b.yarn_color in($all_stripe_color)  group by b.job_no_id", "job_no_id", "job_amt");
        
        $yarn_data=("select b.job_no_id, b.job_no,sum(b.amount) as amount, a.currency
        from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a  where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by b.job_no_id, b.job_no, a.currency ");*/
        
        //echo $yarn_data."<br>";
        
        $nameArray=sql_select( $yarn_data );
        foreach ($nameArray as $selectResult)
        {
			
			$prev_qnty=0;
			$strip_color_arr=array_unique(explode(",",$selectResult[csf("yarn_color")]));
			foreach($strip_color_arr as $strip_id)
			{
				$prev_qnty+=$prev_job_entry[$selectResult[csf("job_no_id")]][$strip_id];
				$prev_short_qnty+=$prev_job_short_entry[$selectResult[csf("job_no_id")]][$strip_id];
			}
        
            if($selectResult[csf("currency")]==2)
            {
                $tot_yarn_dyeing=$selectResult[csf("amount")]+$prev_qnty;
				$tot_yarn_dyeing_short=$selectResult[csf("short_amount")]+$prev_short_qnty;
            }
            else
            {
                $current_date=date("Y-m-d");
                $currency_rate=set_conversion_rate(2, $current_date );
                $tot_yarn_dyeing=$selectResult[csf("amount")]+$prev_qnty;
                $tot_yarn_dyeing=$tot_yarn_dyeing/$currency_rate;
				
				$tot_yarn_dyeing_short=$selectResult[csf("short_amount")]+$prev_short_qnty;
                $tot_yarn_dyeing_short=$tot_yarn_dyeing_short/$currency_rate;
            }
            //echo $convamount;
            $po_no=$job_po_data[$selectResult[csf('job_no')]]['po_number'];
            $shipment_date=array_unique(explode(",",$job_po_data[$selectResult[csf('job_no')]]['shipment_date']));
            $ship_date="";
            foreach ($shipment_date as $date_row)
            {
                if($ship_date=='') $ship_date=change_date_format($date_row); else $ship_date.=",".change_date_format($date_row);
            }
            $pre_cost_yarn_deying=$job_wise_badge_val[$selectResult[csf('job_no')]];
            ?>
            <tr>
                <td align="center"><? echo $i;?></td>
                <td><p><? echo $selectResult[csf('job_no')];?>&nbsp;</p></td>
                <td><p><? echo $po_no;?>&nbsp;</p></td>
                <td><p><? echo $ship_date;?>&nbsp;</p></td>
                <td align="right"><? echo number_format($pre_cost_yarn_deying,2); ?></td>
                <td align="right"><? echo number_format($tot_yarn_dyeing,2); ?> </td>
                <td align="right"><? echo number_format($tot_yarn_dyeing_short,2); ?> </td>
                <td align="right"><? $tot_balance=$pre_cost_yarn_deying-$tot_yarn_dyeing; echo number_format($tot_balance,2); ?></td>
                <td>
                <? 
                if( $pre_cost_yarn_deying>$tot_yarn_dyeing)
                {
                    echo "Less Booking";
                }
                else if ($pre_cost_yarn_deying<$tot_yarn_dyeing) 
                {
                    echo "Over Booking";
                } 
                else if ($pre_cost_yarn_deying==$tot_yarn_dyeing) 
                {
                    echo "As Per";
                } 
                else
                {
                    echo "&nbsp;";
                }
                ?>
                </td>
            </tr>
            <?
            $tot_pre_yarn_dyeing+=$pre_cost_yarn_deying;
            $total_yarn_dyeing+=$tot_yarn_dyeing;
			$total_yarn_dyeing_short+=$tot_yarn_dyeing_short;
            $tot_balance_yarn_dyeing+=$tot_balance;
            $i++;
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" align="right">  <b>Total</b></th>
                <th align="right"><? echo number_format($tot_pre_yarn_dyeing,2); ?></th>
                <th align="right"><? echo number_format($total_yarn_dyeing,2); ?></th>
                <th align="right"><? echo number_format($total_yarn_dyeing_short,2); ?></th>
                <th align="right"><? echo number_format($tot_balance_yarn_dyeing,2); ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    
    <? } ?>
    </div>
    <div>
		<?
        	echo signature_table(43, $cbo_company_name, "1220px");
			echo "****".custom_file_name($txt_booking_no,implode(',',$all_style_arr),implode(',',$all_job_arr));
        ?>
    </div>
<?
}

if($action=="show_with_multiple_job_without_rate")
{
	
	//echo "uuuu";die;
	//echo load_html_head_contents("Yarn Dyeing WO", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1140px" align="center">      
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
           <tr>
               <td width="100"> 
               </td>
               <td width="900">                                     
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
                            <strong>Yarn Dyeing Work Order </strong>
                             </td> 
                            </tr>
                      </table>
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
			$currency_id=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
        }
		
        ?>
       <table width="900" align="left">
       		<tr>
            	<td width="350"  style="font-size:12px">
                        <table width="350" style="" align="left">
                            <tr  >
                                <td   width="120"><b>To</b>   </td>
                                <td width="230">:&nbsp;&nbsp;<? echo $supplier_arr[$supplier_id];?></td>
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
                                <td >:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
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
       
        <table width="1140" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
        	<thead>
            <tr>
             	<th width="30" align="center">Sl</th>
                <th width="65" align="center">Color</th>
                <th width="80" align="center">Color Range</th>
                <th  align="center" width="50">Ref No.</th>
                <th  align="center" width="70">Style Ref.No.</th>
                <th width="30" align="center">Yarn Count</th>
                <th width="140" align="center">Yarn Description</th>
                <th width="60" align="center">Brand</th>
                <th width="50" align="center">Lot</th>
                <th width="40" align="center">UOM</th>
                <th width="60" align="center">WO Qty</th>
                <th  align="center" width="50">Min Req. Cone</th>
                <th  width="80">Job No.</th>
                <th width="80">Buyer</th>
                <th width="110">Order No</th>
                <th >File No <br> Internal Ref No</th>
            </tr>
            </thead>
			<?
            /*if($db_type==0) $select_field_grp="group by  b.job_no_id, b.count, b.yarn_color, b.color_range order by b.id"; 
            else if($db_type==2) $select_field_grp="group by b.job_no_id, b.yarn_color, b.color_range,a.id, a.ydw_no,b.id,b.product_id,b.job_no,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no order by b.id ";
            $multi_job_arr=array();
            $style_no=sql_select("select a.style_ref_no,a.job_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.status_active=1 and b.is_deleted=0");
            
            foreach($style_no as $row_s)
            {
            $multi_job_arr[$row_s[csf('job_no')]]['style']=$row_s[csf('style_ref_no')];
            $multi_job_arr[$row_s[csf('job_no')]]['buyer']=$row_s[csf('buyer_name')];
            $multi_job_arr[$row_s[csf('job_no')]]['po_no'].=$row_s[csf('po_number')].",";
            }	*/	
            
            $product_sql=sql_select("select id, lot, brand from product_details_master");	
			$product_data_array=array();
			foreach($product_sql as $row)
			{
				$product_data_array[$row[csf("id")]]["lot"]=$row[csf("lot")];
				$product_data_array[$row[csf("id")]]["brand"]=$row[csf("brand")];
			}
			
			if($db_type==0)
			{
				$sql="select a.id, a.ydw_no, b.id as dtls_id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty as yarn_wo_qty, b.dyeing_charge, b.amount as amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name, group_concat(d.po_number) as po_number, group_concat(d.file_no) as file_no, group_concat(d.grouping) as internal_ref_no
				from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c, wo_po_break_down d
				where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and b.job_no_id=c.id and c.job_no=d.job_no_mst and a.id=$update_id
				group by a.id, a.ydw_no, b.id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty, b.dyeing_charge, b.amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name order by b.id";
			}
			else
			{
				//listagg(CAST(c.id as VARCHAR(4000)),',') within group (order by c.id) as po_id
				$sql="select a.id, a.ydw_no, b.id as dtls_id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty as yarn_wo_qty, b.dyeing_charge, b.amount as amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name, listagg(CAST(d.po_number as VARCHAR(4000)),',') within group (order by d.po_number) as po_number, listagg(CAST(d.file_no as VARCHAR(4000)),',') within group (order by d.file_no) as file_no, listagg(CAST(d.grouping as VARCHAR(4000)),',') within group (order by d.grouping) as internal_ref_no
				from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c, wo_po_break_down d
				where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and b.job_no_id=c.id and c.job_no=d.job_no_mst and a.id=$update_id
				group by a.id, a.ydw_no, b.id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty, b.dyeing_charge, b.amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name order by b.id";
			}
			//echo $sql;
			$sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
			foreach($sql_result as $row)
			{
				$product_id=$row[csf("product_id")];
				//var_dump($product_id);
				if($product_id)
				{ 
					$lot_amt=$product_data_array[$product_id]["lot"];
					$brand=$product_data_array[$product_id]["brand"];
				}
				$all_job_arr[]=$row[csf("job_no")];
				$all_style_arr[]=$row[csf("style_ref_no")];
			//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);	

			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";	
			
			?>
            <tr bgcolor="<? echo $bgcolor; ?>">
            	<td align="center" valign="middle"><? echo $i; ?></td>
                <td  valign="top"><p><? echo $color_arr[$row[csf("yarn_color")]]; ?></p></td>
                <td  valign="top"><p><? echo $color_range[$row[csf("color_range")]]; ?></p></td>
                <td align="center"  valign="top"><p><? echo $row[csf("referance_no")]; ?></p></td>
                <td align="center"  valign="top"><p> <? echo $row[csf("style_ref_no")]; ?>  </p></td>
                <td align="center"  valign="top"><p><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></p></td>
                <td  valign="top"> <p><? echo $row[csf("yarn_description")]; ?> </p></td>
                <td  valign="top"><p><? echo $brand_arr[$brand]; ?></p></td>
                <td align="center"  valign="top"><p><? echo $lot_amt; ?></p></td>
                <td align="center"  valign="top"><p><? echo "KG"; ?></p></td>
                <td align="right" ><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?></td>
                <td align="center"  valign="top"><p><? echo $row[csf("min_require_cone")]; ?></p></td>
                <td align="center"  valign="top"><p><? echo $row[csf("job_no")]; ?></p></td>
                <td align="center"  valign="top"> <p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?> </p></td>
                <td align="center"  valign="top"><p><? echo implode(",",array_unique(explode(",",$row[csf("po_number")]))); ?> </p></td>
                <td align="center"   valign="top"><p><? echo implode(",",array_unique(explode(",",$row[csf("file_no")])))."<br>".implode(",",array_unique(explode(",",$row[csf("internal_ref_no")]))); ?> </p></td>
            </tr>
            <?
			$i++;
			$yarn_count_des="";
			$style_no="";
			}
			?>
             <tr>
                <td colspan="10" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
                <td align="right" ><b><? echo $total_qty; ?></b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        
        
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
        <table width="1140"><tr><td><strong>Note:</strong></td></tr></table>
        
        <table  width="1140" class="rpt_table"    border="0" cellpadding="0" cellspacing="0">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
            </tr>
        </thead>
        <tbody>
        <?
		//echo "select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no'";
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no'");// quotation_id='$data'
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
        $data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con");// quotation_id='$data'
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
        
    </div>
    <div>
		<?
        	echo signature_table(43, $cbo_company_name, "1140px");
			echo "****".custom_file_name($txt_booking_no,implode(',',$all_style_arr),implode(',',$all_job_arr));
        ?>
    </div>
<?

}


if($action=="print2")
{
	
	//echo "uuuu";die;
	//echo load_html_head_contents("Yarn Dyeing WO", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$supplier_phone_arr=return_library_array( "select id, contact_no from lib_supplier",'id','contact_no');
	$supplier_email_arr=return_library_array( "select id, email from lib_supplier",'id','email');
	$supplier_attention_arr=return_library_array( "select id, contact_person from lib_supplier",'id','contact_person');
	$supplier_address_arr=return_library_array( "select id, address_1 from lib_supplier",'id','address_1');
	

	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1140px" align="center">      
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
           <tr>
               <td width="100"> 
               </td>
               <td width="900">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Arial Narrow', Arial, sans-serif">
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

                                             <? echo $result[csf('plot_no')]; ?> 
                                             <? echo $result[csf('level_no')]?>
                                             <? echo $result[csf('road_no')]; ?> 
                                             <? echo $result[csf('block_no')];?> 
                                             <? echo $result[csf('city')];?> 
                                             <? echo $result[csf('zip_code')]; ?> 
                                             <?php echo $result[csf('province')];?> 
                                             <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                             <? echo $result[csf('email')];?> 
                                             <? echo $result[csf('website')];
                            }
							               ?>   
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>Yarn Dyeing Work Order </strong>
                             </td> 
                            </tr>
                      </table>
                </td>       
            </tr>
       </table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
        $nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.booking_date from wo_yarn_dyeing_mst a where a.id=$update_id"); 


        //.................for finding buyer name................

         $style_no="select  a.buyer_name
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b 
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0";
		 
        $sql_result=sql_select($style_no);
		
		foreach($sql_result as $row)
		{
			$total_buyer.=$row[csf("buyer_name")].",";
		}
		
		
		$order_repeat_no=array();
		$repeat_no="select  a.order_repeat_no
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b 
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0";
		 
        $sql_repeat_no=sql_select($repeat_no);
		
		foreach($sql_repeat_no as $row)
		{
			$order_repeat_no[$row[csf("order_repeat_no")]]=$row[csf("order_repeat_no")];
		}


        //...................for finding buyer name..................





        foreach ($nameArray as $result)
        {
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_id=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$booking_date=$result[csf('booking_date')];
        }
		
        ?>
       <table width="900" align="left" style="font-family: 'Arial Narrow', Arial, sans-serif"">
       		<tr>
            	<td width="350"  style="font-size:12px">
                        <table width="350" style="" align="left">
                            <tr  >
                                <td   width="120" style="font-size:18px"><b>To</b>   </td>
                                <td width="230" style="font-size:18px">:&nbsp;&nbsp;<b><? echo $supplier_arr[$supplier_id];?></b></td>
                            </tr>
                            
                             <tr>
                                <td  style="font-size:18px"><b>Wo No.</b>   </td>
                                <td  style="font-size:18px">:&nbsp;&nbsp;<b><? echo $work_order;?></b>    </td>
                            </tr> 
                            
                            <tr>
                                <td style="font-size:12px"><b>Attention</b></td>
                                <td >:&nbsp;&nbsp;<? echo $attention;//$supplier_attention_arr[$supplier_id];//$attention; ?></td>
                            </tr> 
                            
                            <tr>
                                <!-- <td style="font-size:12px"><b>Booking Date</b></td>
                                <td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td> -->

                                <td style="font-size:12px"><b>Phone</b></td>
                                <td >:&nbsp;&nbsp;<? echo $supplier_phone_arr[$supplier_id];//"phone";//$currency[$currency_id]; ?></td>

                            </tr> 

                            <tr>
                                <!-- <td style="font-size:12px"><b>Currency</b></td>
                                <td >:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td> -->
                                <td style="font-size:12px"><b>Mail</b></td>
                                <td >:&nbsp;&nbsp;<? echo $supplier_email_arr[$supplier_id];//"mail";//$currency[$currency_id]; ?></td>

                            </tr>  

                            <tr>
                                <td style="font-size:12px"><b>Address</b></td>
                                <td >:&nbsp;&nbsp;<? echo $supplier_address_arr[$supplier_id];//"address";//$currency[$currency_id]; ?></td>

                            </tr> 
                              <tr>
                                <td style="font-size:12px"><b>Order Repeat No</b></td>
                                <td >:&nbsp;&nbsp;<? echo implode(",",$order_repeat_no);//"address";//$currency[$currency_id]; ?></td>

                            </tr> 
                            
                        </table>
                </td>
                <td width="350"  style="font-size:12px">
                		<table width="350" style="" align="left">
                            <tr>                              
                                <td  width="120"><b>WO Date</b>   </td>
                                <td width="230" >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date);  else echo "";//echo "wo date";?> </td>
                            </tr>
                            <tr>                  
                                <td  width="120"><b>Buyer</b>   </td>
                                <td width="230" >:&nbsp;&nbsp;<? 

                                     $buyer_id_arr=array_unique(explode(",",$total_buyer));
									 $all_buyer="";
									 foreach( $buyer_id_arr as $row)
									 {
										  $all_buyer.=$buyer_name_arr[$row].",";
									 }
									 $all_buyer=chop($all_buyer," , ");
									 echo $all_buyer;
                                //echo "Buyer";?> </td>

                            </tr>
                            <tr>                                
                                <td  width="120"><b>Season</b>   </td>
                                <td width="230" >:&nbsp;&nbsp;<? 

                                /*$sql_test="select  DISTINCT p.job_no
									from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b 
									where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id  and  p.status_active=1 and p.is_deleted=0";*/
                                     

                                     $sql="select  DISTINCT p.job_no AS job_no, a.season_buyer_wise AS season_buyer_wise, d.season_name AS Season_name
									from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b ,lib_buyer_season d
									where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and p.job_no = a.job_no and a.season_buyer_wise = d.id  and  p.status_active=1 and p.is_deleted=0";

									//echo $sql_test;die;

									$sql_result=sql_select($sql);
									//print_r($sql_result); die;
									foreach($sql_result as $data){
										echo $data[csf("season_name")].",";	
									}

                            
                                
                                ?> </td>

                            </tr> 
                            <tr>                          
                                <td  width="120"><b>Currency</b>   </td>
                                <td width="230" >:&nbsp;&nbsp;<? echo $currency[$currency_id];//"Currency";?> </td>
                            </tr>  

                            <!-- <tr>                          
                                <td  width="120"><b>Booking No</b>   </td>
                                <td width="230" >:&nbsp;&nbsp;<? //echo "Booking No";?> </td>
                            </tr>  -->
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
       
        <table width="1140" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table" style="font-family: 'Arial Narrow', Arial, sans-serif">
        	<thead>
            <tr>
             	<th width="30" align="center">Sl</th>

             	<th  width="190">Job No.</th>

             	<th  width="190">Booking No.</th>

             	<th  align="center" width="140">Style Ref.No.</th>

                <th width="140" align="center">Color</th>

                <th width="140" align="center">Gmt Color Combo</th>


                <th  align="center" width="100">Ref No.</th>
               
                <th width="90" align="center">Yarn Count</th>
                <th width="250" align="center">Yarn Description</th>
                <th width="60" align="center">Brand</th>
                <th width="60" align="center">WO Qty</th>

                <th width="50" align="center">Dyeing Rate</th>
                <th width="70" align="center">Amount</th>

                <th  align="center" width="50">Min Req. Cone</th>
                
                <th  align="center" width="30" >Remarks/Shade</th>
            </tr>
            </thead>


			<?
            /*if($db_type==0) $select_field_grp="group by  b.job_no_id, b.count, b.yarn_color, b.color_range order by b.id"; 
            else if($db_type==2) $select_field_grp="group by b.job_no_id, b.yarn_color, b.color_range,a.id, a.ydw_no,b.id,b.product_id,b.job_no,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no order by b.id ";
            $multi_job_arr=array();
            $style_no=sql_select("select a.style_ref_no,a.job_no,a.buyer_name,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.status_active=1 and b.is_deleted=0");
            
            foreach($style_no as $row_s)
            {
            $multi_job_arr[$row_s[csf('job_no')]]['style']=$row_s[csf('style_ref_no')];
            $multi_job_arr[$row_s[csf('job_no')]]['buyer']=$row_s[csf('buyer_name')];
            $multi_job_arr[$row_s[csf('job_no')]]['po_no'].=$row_s[csf('po_number')].",";
            }	*/	
            
            $product_sql=sql_select("select id, lot, brand from product_details_master");	
			$product_data_array=array();
			foreach($product_sql as $row)
			{
				$product_data_array[$row[csf("id")]]["lot"]=$row[csf("lot")];
				$product_data_array[$row[csf("id")]]["brand"]=$row[csf("brand")];
			}
			
			if($db_type==0)
			{
				$sql="select a.id, a.ydw_no, b.id as dtls_id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty as yarn_wo_qty, b.dyeing_charge, b.amount as amount, b.min_require_cone, b.referance_no, c.style_ref_no,yarn_comp_type1st,yarn_comp_percent1st, c.buyer_name, group_concat(d.po_number) as po_number, group_concat(d.file_no) as file_no, group_concat(d.grouping) as internal_ref_no
				from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c, wo_po_break_down d
				where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and b.job_no_id=c.id and c.job_no=d.job_no_mst and a.id=$update_id
				group by a.id, a.ydw_no, b.id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty, b.dyeing_charge, b.amount, b.min_require_cone, b.referance_no, c.style_ref_no,yarn_comp_type1st,yarn_comp_percent1st, c.buyer_name order by b.id";
			}
			else
			{
				//listagg(CAST(c.id as VARCHAR(4000)),',') within group (order by c.id) as po_id

				$sql="select a.id, a.ydw_no, b.id as dtls_id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range, b.yarn_wo_qty as yarn_wo_qty, b.dyeing_charge, b.amount as amount, b.min_require_cone, b.referance_no,b.remarks,yarn_comp_type1st,yarn_comp_percent1st,c.style_ref_no, c.buyer_name, listagg(CAST(d.po_number as VARCHAR(4000)),',') within group (order by d.po_number) as po_number, listagg(CAST(d.file_no as VARCHAR(4000)),',') within group (order by d.file_no) as file_no, listagg(CAST(d.grouping as VARCHAR(4000)),',') within group (order by d.grouping) as internal_ref_no
				from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_po_details_master c, wo_po_break_down d
				where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and b.job_no_id=c.id and c.job_no=d.job_no_mst and a.id=$update_id
				group by a.id, a.ydw_no, b.id, b.product_id, b.job_no, b.job_no_id, b.yarn_color, b.yarn_description, b.count, b.color_range,b.remarks,yarn_comp_type1st,yarn_comp_percent1st, b.yarn_wo_qty, b.dyeing_charge, b.amount, b.min_require_cone, b.referance_no, c.style_ref_no, c.buyer_name order by b.id";

				


			}
			//echo $sql;

		  $booking_arr=sql_select("select job_no,booking_no from  wo_booking_mst where booking_type=1 and status_active=1");
		  $job_arr=array();
		  foreach($booking_arr as $row)
		{
			$job_arr[$row[csf("job_no")]].=$row[csf("booking_no")].',';
		}
		//print_r($job_arr['D n C-17-00423']);
		unset($booking_arr);

         //	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');

			$sql_result=sql_select($sql);$total_qtys=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
			foreach($sql_result as $row)
			{
				$product_id=$row[csf("product_id")];
				//var_dump($product_id);
				if($product_id)
				{ 
					$lot_amt=$product_data_array[$product_id]["lot"];
					$brand=$product_data_array[$product_id]["brand"];
				}
				$all_job_arr[]=$row[csf("job_no")];
				$all_style_arr[]=$row[csf("style_ref_no")];
			//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);	

			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";	
			
			?>
            <tr  bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
            	<td align="center" valign="middle"><? echo $i; ?></td>

                <td align="center"  valign="top"><p><? echo $row[csf("job_no")]; ?></p></td>

                <td align="center"  valign="top"><p><?

                     
                     $booking_no='';                   
                     $booking_no=implode(",",array_unique(array_filter(explode(',',$job_arr[$row[csf("job_no")]]))));
                     
                 echo  $booking_no;//implode(',', $job_arr[$row[csf("job_no")]]);//$row[csf("job_no")]; ?></p></td>

            	<td align="center"  valign="top"><p> <? echo $row[csf("style_ref_no")]; ?>  </p></td>

                <td  valign="top"><p><? echo $color_arr[$row[csf("yarn_color")]]; ?></p></td>
                <td  valign="top"><p><? echo $color_range[$row[csf("color_range")]]; ?></p></td>
                <td align="center"  valign="top"><p><? echo $row[csf("referance_no")]; ?></p></td>

                <td align="center"  valign="top"><p><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></p></td>
                <td  valign="top"> <p><?echo $row[csf("yarn_description")]; //echo $composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].'%'; ?> </p></td>
                <td  valign="top"><p><? echo $brand_arr[$brand]; ?></p></td>
  
                 <td align="right" ><? echo $row[csf("yarn_wo_qty")]; $total_qtys+=$row[csf("yarn_wo_qty")]; ?></td>
               

                <td align="center"  ><p><? echo $row[csf("dyeing_charge")];//"test 1"; ?></p></td>
                <td align="center" ><p><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")]; //echo "test2"  valign="top"; ?></p></td>

                <td align="center"  valign="top"><p><? echo $row[csf("min_require_cone")]; ?></p></td>

                 <td align="center"  valign="top"><p><? echo $row[csf("remarks")]; //echo "remarks"; // echo $row[csf("remarks")];?></p></td>
            </tr>
            <?
			$i++;
			$yarn_count_des="";
			$style_no="";
			}
			?>
             <tr>
                <td colspan="10" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
                <td align="right" ><b><? echo $total_qtys; ?></b></td>
                <td>&nbsp;</td> 
                <td align="right"><b><? echo number_format($total_amount,2); ?></b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <!-- <td>&nbsp;</td>
                <td>&nbsp;</td> -->
            </tr>
        </table>
        
        
          <!--==============================================AS PER GMTS COLOR START=========================================  -->


        <table width="1140"><tr><td><strong>Note:</strong></td></tr></table> 
        
         <? echo get_spacial_instruction($txt_booking_no,"1140px");?> 
        
    </div>
    <div>
		<?
        	echo signature_table(43, $cbo_company_name, "1140px");
			echo "****".custom_file_name($txt_booking_no,implode(',',$all_style_arr),implode(',',$all_job_arr));
        ?>
    </div>
<?

}




if($action=="show_without_rate_booking_report")
{
	//echo "uuuu";die;
	//echo load_html_head_contents("Yarn Dyeing WO", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1000px" align="center">      
      <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
           <tr>
              
               <td width="700">                                     
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
                            <strong>Yarn Dyeing Work Order </strong>
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
			$currency_id=$result[csf('currency')];
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
                                <td width="230">:&nbsp;&nbsp;<? echo $supplier_arr[$supplier_id];?></td>
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
                                <td >:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
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
        
		$style_no="select a.style_ref_no, a.job_no, a.buyer_name, b.po_number, b.file_no, b.grouping as inter_ref_no, p.id as dtls_id
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b 
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0";
		
		//echo $style_no;
		
        $sql_result=sql_select($style_no);
		$style_all=$total_dtls_id=$tot_job_no=$total_buyer=$total_order_no=$all_file=$all_inter_ref="";
		foreach($sql_result as $row)
		{
			$total_dtls_id.=$row[csf("dtls_id")].",";
			$style_all.=$row[csf("style_ref_no")].",";
			$tot_job_no.=$row[csf("job_no")].",";
			$total_buyer.=$row[csf("buyer_name")].",";
			$total_order_no.=$row[csf("po_number")].",";
			$all_file.=$row[csf("file_no")].",";
			$all_inter_ref.=$row[csf("inter_ref_no")].",";
		}
		$total_dtls_id=chop($total_dtls_id," , ");$style_all=chop($style_all," , ");$tot_job_no=chop($tot_job_no," , ");$total_buyer=chop($total_buyer," , ");$total_order_no=chop($total_order_no," , ");$all_file=chop($all_file," , ");$all_inter_ref=chop($all_inter_ref," , ");
        ?>
       
       <table width="950" align="center">
       		<tr  style="font-size:12px">
            	<td width="120"><b>Style </b></td>
                <td width="830" valign="top">:&nbsp;
				<?
				$style_all_arr=array_unique(explode(",",$style_all));
				$all_style="";
				foreach($style_all_arr as $row)
				{
					$all_style.=$row.",";
				}
				$all_style=chop($all_style," , ");
				echo $all_style;
				 ?>
                </td>
            </tr>
       		<tr  style="font-size:12px">
            	<td><b>Job No </b></td>
                <td valign="top">:&nbsp;
				<?
				$all_job_arr=array_unique(explode(",",$tot_job_no));
				$all_job="";
				foreach($all_job_arr as $row)
				{
					$all_job.=$row.",";
				}
				$all_job=chop($all_job," , ");
				echo $all_job;
				 ?>
                 </td>
            </tr>
            <tr style="font-size:12px">
            	<td valign="top"><b>Buyer </b> </td>
                <td valign="top">:&nbsp;
				<?
				 $buyer_id_arr=array_unique(explode(",",$total_buyer));
				 $all_buyer="";
				 foreach( $buyer_id_arr as $row)
				 {
					  $all_buyer.=$buyer_name_arr[$row].",";
				 }
				 $all_buyer=chop($all_buyer," , ");
				 echo $all_buyer;
				?>
                </td>
            </tr >
            <tr style="font-size:12px">
            	<td valign="top"><b>Order No</b></td>
                <td valign="top">: &nbsp;
				<?
				$all_order_arr=array_unique(explode(",",$total_order_no));
				$all_order="";
				foreach($all_order_arr as $row)
				{
					$all_order.=$row.",";
				}
				$all_order=chop($all_order," , ");
				echo $all_order; 
				?>
                </td>
            </tr>
            
       </table>
       <table width="950" align="center" style="font-size:12px">
       		<tr>
            	<td width="120" valign="top"><b>File No</b></td>
                <td width="355" valign="top">: &nbsp;
                <?
				$all_file_arr=array_unique(explode(",",$all_file));
				$all_file="";
				foreach($all_file_arr as $row)
				{
					$all_file.=$row.",";
				}
				$all_file=chop($all_file," , ");
				echo $all_file; 
				?>
                </td>
                <td width="120" valign="top"><b>Internal Ref. No</b></td>
                <td valign="top">: &nbsp;
                <?
				$all_inter_ref_arr=array_unique(explode(",",$all_inter_ref));
				$all_ref="";
				foreach($all_inter_ref_arr as $row)
				{
					$all_ref.=$row.",";
				}
				$all_ref=chop($all_ref," , ");
				echo $all_ref; 
				?>
                </td>
            </tr>
       </table>
            	
       
          
        <table width="1000" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
        	<thead>
            <tr>
             	<th width="40" align="center">Sl</th>
                <th width="70" align="center">Color</th>
                <th width="85" align="center">Color Range</th>
                <th  align="center" width="70">Ref NO.</th>
                <th width="40" align="center">Yarn Count</th>
                <th width="190" align="center">Yarn Description</th>
                <th width="70" align="center">Brand</th>
                <th width="65" align="center">Lot</th>
                <th width="50" align="center">UOM</th>
                <th width="70" align="center">WO Qty</th>
                <th  align="center" width="65" >Min Req. Cone</th>
                <th  align="center" >Remarks/Shade</th>
            </tr>
            </thead>
            <?
			if($db_type==0) $select_f_grp="group by count, yarn_color, color_range order by id"; 
			else if($db_type==2) $select_f_grp="group by yarn_color, color_range,id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,remarks order by id ";
						
			$sql_color="select id,product_id,job_no,job_no_id,yarn_color,yarn_description,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no,remarks
			from 
					wo_yarn_dyeing_dtls
			where 
					status_active=1 and id in($total_dtls_id) $select_f_grp";
			//echo $sql_color;die;
			$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
			foreach($sql_result as $row)
			{
				$product_id=$row[csf("product_id")];
				if($product_id)
				{
					$sql_brand=sql_select("select lot,brand from product_details_master where id in($product_id)");
					foreach($sql_brand as $row_barand)
					{
						$lot_amt=$row_barand[csf("lot")];
						$brand=$row_barand[csf("brand")];
					}
					
				}
				
				
			//$yarn_count=explode(" ",$row[csf("yarn_description")]);

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
                <!--<td align="center">
				<?
				 /*$style_no=return_field_value( "style_ref_no", " wo_po_details_master","job_no='".$row[csf("job_no")]."'"); 
				 echo $style_no;*/
				?>
                </td>-->
                <td align="center"><? echo $count_arr[$row[csf("count")]]; ?></td>
                <td>
				<?
				
				echo $row[csf("yarn_description")];
				 //echo $row[csf("yarn_description")]; 
				?>
                </td>
                <td><? echo $brand_arr[$brand]; ?></td>
                <td align="center"><? echo $lot_amt; ?></td>
                <td align="center"><? echo "KG"; ?></td>
                <td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
                <td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
                <td align="center"><? echo $row[csf("remarks")]; ?></td>
            </tr>
            <?
			$i++;
			}
			?>
             <tr>
                <td colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
                <td align="right" ><b><? echo $total_qty; ?></b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        
        
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
        <table width="1000" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>
        
        <table  width="1000" class="rpt_table"    border="0" cellpadding="0" cellspacing="0" align="center">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
            </tr>
        </thead>
        <tbody>
        <?
		//echo "select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no'";
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no'");// quotation_id='$data'
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
        $data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con");// quotation_id='$data'
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
        
    </div>
    <div>
		<?
        	echo signature_table(43, $cbo_company_name, "1000px");
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
	//echo load_html_head_contents("Yarn Dyeing WO", "../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$job_quantity_arr=return_library_array( "select job_no,job_quantity from wo_po_details_master",'job_no','job_quantity');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//echo $show_comment;
	
	$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.ecchange_rate, a.is_short from wo_yarn_dyeing_mst a where a.id=$update_id"); 
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
		$exchange_rate=$result[csf('ecchange_rate')];
		$is_short=$result[csf('is_short')];
	}
	
	$varcode_work_order_no=$work_order;
	
	
	?>
	<div style="width:1000px" align="center">      
        <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
           <tr>
               <td width="700">                                     
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
                            <strong>Yarn Dyeing Work Order <? if($is_short==1) echo " (Short) "; ?> </strong>
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
        
		
        ?>
       <table width="950" style="" align="center">
       		<tr>
            	<td width="350"  style="font-size:12px">
                        <table width="350" style="" align="left">
                            <tr  >
                                <td   width="120"><b>To</b>   </td>
                                <td width="230">:&nbsp;&nbsp;<? echo $supplier_arr[$supplier_id];?></td>
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
                                <td >:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
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
	  			/*$multi_job_arr=array();
				$style_no=sql_select("select a.style_ref_no,a.job_no,b.pub_shipment_date,a.buyer_name,b.po_number,b.po_quantity from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.status_active=1 and b.is_deleted=0");
				 
				foreach($style_no as $row_s)
				{
					$multi_job_arr[$row_s[csf('job_no')]]['style_ref_no']=$row_s[csf('style_ref_no')];
					$multi_job_arr[$row_s[csf('job_no')]]['buyer']=$row_s[csf('buyer_name')];
					$multi_job_arr[$row_s[csf('job_no')]]['po_no'].=$row_s[csf('po_number')].",";
					$multi_job_arr[$row_s[csf('job_no')]]['ship'].=$row_s[csf('pub_shipment_date')].",";
					$multi_job_arr[$row_s[csf('job_no')]]['po_quantity']+=$row_s[csf('po_quantity')];
					
				}		
	   $sql="select a.id, a.ydw_no,b.job_no,b.yarn_color,b.id as dtls_id
			from 
					wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b 
			where 
					a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
	   $sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";$tot_job_no="";$total_buyer="";$total_order_no="";$total_dtls_id=0;$style_all="";
	   $total_order_qty=0;
		foreach($sql_result as $row)
		{
			if($total_dtls_id==0) $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];
			
			if($tot_job_no=="") $tot_job_no=$row[csf("job_no")]; else $tot_job_no=$tot_job_no.",".$row[csf("job_no")];
			if($style_all=="") $style_all=$multi_job_arr[$row[csf('job_no')]]['style_ref_no']; else $style_all=$style_all.",".$multi_job_arr[$row[csf('job_no')]]['style_ref_no'];
			$bayer=$multi_job_arr[$row[csf('job_no')]]['buyer'];
			if($total_buyer=="") $total_buyer=$bayer; else $total_buyer=$total_buyer.",".$bayer;
			$order_no=$multi_job_arr[$row[csf('job_no')]]['po_no'];
			if($total_order_no=="") $total_order_no=$order_no; else $total_order_no=$total_order_no.",".$order_no;
			
			
		}
		$total_order_no=substr($total_order_no,0,-1);
		//var_dump($total_dtls_id);
		//die;*/
		
		$style_no="select a.style_ref_no, a.job_no, a.buyer_name, b.po_number, b.file_no, b.grouping as inter_ref_no, b.po_quantity, p.id as dtls_id 
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b 
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0";
        $sql_result=sql_select($style_no);
		$style_all=$total_dtls_id=$tot_job_no=$total_buyer=$total_order_no=$all_file=$all_inter_ref="";//$total_order_qty=0;
		foreach($sql_result as $row)
		{
			$total_dtls_id.=$row[csf("dtls_id")].",";
			$style_all.=$row[csf("style_ref_no")].",";
			$tot_job_no.=$row[csf("job_no")].",";
			$total_buyer.=$row[csf("buyer_name")].",";
			$total_order_no.=$row[csf("po_number")].",";
			$all_file.=$row[csf("file_no")].",";
			$all_inter_ref.=$row[csf("inter_ref_no")].",";
			//$total_order_qty+=$row[csf("po_quantity")];
		}
		$total_dtls_id=chop($total_dtls_id," , ");$style_all=chop($style_all," , ");$tot_job_no=chop($tot_job_no," , ");$total_buyer=chop($total_buyer," , ");$total_order_no=chop($total_order_no," , ");$all_file=chop($all_file," , ");$all_inter_ref=chop($all_inter_ref," , ");
		
		$style_po="select a.job_no, sum(distinct b.po_quantity) as po_quantity
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b 
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0 group by a.job_no";
        $sql_po=sql_select($style_po);
		$total_order_qty=0;
		foreach($sql_po as $row)
		{
			$total_order_qty+=$row[csf('po_quantity')];
		}
	   
	   ?>
       
       <table width="950" align="center">
       		<tr  style="font-size:12px">
            	<td width="120"><b>Style </b></td>
                <td width="830" valign="top">:&nbsp;
				<?
				$style_all_arr=array_unique(explode(",",$style_all));
				$all_style="";
				foreach($style_all_arr as $row)
				{
					$all_style.=$row.",";
				}
				$all_style=chop($all_style," , ");
				echo $all_style;
				 ?>
                </td>
            </tr>
       		<tr  style="font-size:12px">
            	<td><b>Job No </b></td>
                <td valign="top">:&nbsp;
				<?
				$all_job_arr=array_unique(explode(",",$tot_job_no));
				$all_job="";
				foreach($all_job_arr as $row)
				{
					$all_job.=$row.",";
				}
				$all_job=chop($all_job," , ");
				echo $all_job;
				 ?>
                 </td>
            </tr>
            <tr style="font-size:12px">
            	<td valign="top"><b>Buyer </b> </td>
                <td valign="top">:&nbsp;
				<?
				 $buyer_id_arr=array_unique(explode(",",$total_buyer));
				 $all_buyer="";
				 foreach( $buyer_id_arr as $row)
				 {
					  $all_buyer.=$buyer_name_arr[$row].",";
				 }
				 $all_buyer=chop($all_buyer," , ");
				 echo $all_buyer;
				?>
                </td>
            </tr >
            <tr style="font-size:12px">
            	<td valign="top"><b>Order No</b></td>
                <td valign="top">: &nbsp;
				<?
				$all_order_arr=array_unique(explode(",",$total_order_no));
				$all_order="";
				foreach($all_order_arr as $row)
				{
					$all_order.=$row.",";
				}
				$all_order=chop($all_order," , ");
				echo $all_order; 
				?>
                </td>
            </tr>
            
            <tr style="font-size:12px">
            	<td valign="top"><b>Order Qty.</b></td>
                <td valign="top">: &nbsp;
				<?
				echo $total_order_qty;
				
				?>
                </td>
            </tr>
            
       </table>     	
       
          
        <table width="1000"  align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
        	<thead>
            <tr>
             	<th width="30" align="center">Sl</th>
                <th width="70" align="center">Color</th>
                <th width="80" align="center">Color Range</th>
                <th  align="center" width="50">Ref No.</th>
                <th width="60" align="center">File No</th>
                <th width="60" align="center">Internal Ref. No</th>
                <th width="30" align="center">Yarn Count</th>
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
			$sql_color="select id,product_id,job_no,job_no_id,yarn_color,yarn_description,count,color_range,yarn_wo_qty as yarn_wo_qty,dyeing_charge,amount as amount,min_require_cone,referance_no, remarks, file_no, internal_ref_no
			from 
					wo_yarn_dyeing_dtls
			where 
					status_active=1 and id in($total_dtls_id)";		
			//echo $sql_color;die;
			$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";$job_strip_color=array();
			foreach($sql_result as $row)
			{
				$product_id=$row[csf("product_id")];
				//var_dump($product_id);
				$job_strip_color[$row[csf("job_no")]].=$row[csf("yarn_color")].",";
				$all_stripe_color.=$row[csf("yarn_color")].",";
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
					<td><p> <?  echo $row[csf("yarn_description")]; ?></p></td>
					<td><p><? echo $brand_arr[$brand]; ?></p></td>
					<td align="center"><p><? echo $lot_amt; ?></p></td>
                    <td align="center"><p><? echo "KG"; ?></p></td>
					<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
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
                <td colspan="11" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
                <td align="right" ><b><? echo $total_qty; ?></b></td>
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
            <td colspan="16" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </td> 
            </tr>
        </table>
        
          <!--=======================AS PER GMTS COLOR START=========================================  -->
        <table width="1000" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>
        <? echo get_spacial_instruction($txt_booking_no);?>
    <br> <br>
    <?
    if($show_comment==1)
	{
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" >
            <thead>
                <tr> 
                    <th colspan="9" align="center"><b> Comments</b> </th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="120">Job No</th>
                    <th width="180">PO No</th>
                    <th width="150">Ship Date</th>
                    <th width="80">Pre-Cost Value</th>
                    <th width="80">WO Value</th>
                    <th width="80">Short WO Value</th>
                    <th width="80">Balance</th>
                    <th>Comments </th>
                </tr>
            </thead>
            <tbody>
            <?
			$all_stripe_color=implode(",",array_unique(explode(",",chop($all_stripe_color,","))));
            $job_cond_arr=array_unique(explode(",",$tot_job_no));
			//print_r($job_strip_color);
			if($db_type==0)
			{
				$job_po_sql=sql_select("select job_no_mst, group_concat(po_number) as po_number, group_concat(shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0 group by job_no_mst");
			}
			else
			{
				$job_po_sql=sql_select("select job_no_mst, listagg(cast(po_number as varchar(4000)),',') within group(order by po_number) as po_number, listagg(cast(shipment_date as varchar(4000)),',') within group(order by shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0 group by job_no_mst");
			}
			$job_po_data=array();
			foreach($job_po_sql as $row)
			{
				$job_po_data[$row[csf("job_no_mst")]]["po_number"]=$row[csf("po_number")];
				$job_po_data[$row[csf("job_no_mst")]]["shipment_date"]=$row[csf("shipment_date")];
			}
            $sql_stripe_required=sql_select("select job_no, stripe_color, sum(fabreqtotkg) as fabreqtotkg from wo_pre_stripe_color where job_no in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0 group by job_no, stripe_color");
            $strip_req_data=array();
            foreach($sql_stripe_required as $row)
            {
                $strip_req_data[$row[csf("job_no")]][$row[csf("stripe_color")]]=$row[csf("fabreqtotkg")];
            }
            $sql_strip_rate=sql_select("select job_no, color_break_down from wo_pre_cost_fab_conv_cost_dtls where job_no in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0 and cons_process=30");
            $strip_req_rate=array();
            foreach($sql_strip_rate as $row)
            {
                $color_data=explode("__",$row[csf("color_break_down")]);
                foreach($color_data as $color_rate)
                {
					$color_rate_ref=explode("_",$color_rate);
					if($color_rate_ref[1]>0)
					{
						$strip_req_rate[$row[csf("job_no")]][$color_rate_ref[0]]=$color_rate_ref[1]*1;
					}
                    
                }
            }
			
			//var_dump($strip_req_rate);
			
            $job_wise_badge_val=array();
            foreach($job_strip_color as $job_no=>$strip_color)
            {
                $strip_color_arr=array_unique(explode(",",chop($strip_color,",")));
                foreach($strip_color_arr as $strip_color_id)
                {
                    $job_wise_badge_val[$job_no]+=$strip_req_data[$job_no][$strip_color_id]*$strip_req_rate[$job_no][$strip_color_id];
                }
            }
            //var_dump($job_wise_badge_val);die;
			$total_dtls_id=implode(",",array_unique(explode(",",$total_dtls_id)));
			$prev_wo_data=sql_select("select b.job_no_id, b.yarn_color, 
			sum(case when a.is_short<>1 then b.amount else 0 end) as amount, 
			sum(case when a.is_short=1 then b.amount else 0 end) as short_amount 
			from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form=125 and b.status_active=1 and b.is_deleted=0 and b.job_no in('".implode($job_cond_arr,"','")."') and b.id not in($total_dtls_id) group by b.job_no_id, b.yarn_color");
			foreach($prev_wo_data as $row)
			{
				$prev_job_entry[$row[csf("job_no_id")]][$row[csf("yarn_color")]]+=$row[csf("amount")];
				$prev_job_short_entry[$row[csf("job_no_id")]][$row[csf("yarn_color")]]+=$row[csf("short_amount")];
			}
            //$prev_job_entry=return_library_array( "select b.job_no_id, sum(b.amount) as job_amt from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form=41 and b.status_active=1 and b.is_deleted=0 and b.job_no in('".implode($job_cond_arr,"','")."') and b.id not in($total_dtls_id) and b.yarn_color in($all_stripe_color)  group by b.job_no_id", "job_no_id", "job_amt");//
            if($db_type==0)
			{
				$yarn_data="select b.job_no_id, max(a.currency) as currency, b.job_no, 
				sum(case when a.is_short<>1 then b.amount else 0 end) as amount, 
				sum(case when a.is_short=1 then b.amount else 0 end) as short_amount, group_concat(b.yarn_color) as yarn_color
            	from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a  
				where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by b.job_no_id, b.job_no";
			}
			else
			{
				$yarn_data="select b.job_no_id, max(a.currency) as currency, b.job_no,  
				sum(case when a.is_short<>1 then b.amount else 0 end) as amount, 
				sum(case when a.is_short=1 then b.amount else 0 end) as short_amount, 
				listagg( cast(b.yarn_color as varchar(4000)),',') within group (order by b.yarn_color) as yarn_color
            	from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a  
				where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   
				group by b.job_no_id, b.job_no";
			}
            
            
            //echo $yarn_data."<br>";
            
            $nameArray=sql_select( $yarn_data );
            foreach ($nameArray as $selectResult)
            {
				$prev_qnty=0;
				$strip_color_arr=array_unique(explode(",",$selectResult[csf("yarn_color")]));
				foreach($strip_color_arr as $strip_id)
				{
					$prev_qnty+=$prev_job_entry[$selectResult[csf("job_no_id")]][$strip_id];
					$prev_short_qnty+=$prev_job_short_entry[$selectResult[csf("job_no_id")]][$strip_id];
					
				}
            
                if($selectResult[csf("currency")]==2)
                {
                    $tot_yarn_dyeing=$selectResult[csf("amount")]+$prev_qnty;
					$tot_yarn_dyeing_short=$selectResult[csf("short_amount")]+$prev_short_qnty;
                }
                else
                {
                    $current_date=date("Y-m-d");
                    $currency_rate=set_conversion_rate(2, $current_date );
                    $tot_yarn_dyeing=$selectResult[csf("amount")]+$prev_qnty;
                    $tot_yarn_dyeing=$tot_yarn_dyeing/$currency_rate;
					
					$tot_yarn_dyeing_short=$selectResult[csf("short_amount")]+$prev_short_qnty;
                    $tot_yarn_dyeing_short=$tot_yarn_dyeing_short/$currency_rate;
                }
                //echo $convamount;
                $po_no=$job_po_data[$selectResult[csf('job_no')]]['po_number'];
                $shipment_date=array_unique(explode(",",$job_po_data[$selectResult[csf('job_no')]]['shipment_date']));
				$ship_date="";
                foreach ($shipment_date as $date_row)
                {
                    if($ship_date=='') $ship_date=change_date_format($date_row); else $ship_date.=",".change_date_format($date_row);
                }
				$pre_cost_yarn_deying=$job_wise_badge_val[$selectResult[csf('job_no')]];
                ?>
                <tr>
                    <td align="center"><? echo $i;?></td>
                    <td><p><? echo $selectResult[csf('job_no')];?> &nbsp;</p></td>
                    <td><p><? echo $po_no;?>&nbsp;</p></td>
                    <td><p><? echo $ship_date;?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($pre_cost_yarn_deying,2); ?></td>
                    <td align="right"><? echo number_format($tot_yarn_dyeing,2); ?> </td>
                    <td align="right"><? echo number_format($tot_yarn_dyeing_short,2); ?> </td>
                    <td align="right"><? $tot_balance=$pre_cost_yarn_deying-$tot_yarn_dyeing; echo number_format($tot_balance,2); ?></td>
                    <td>
                    <? 
                    if( $pre_cost_yarn_deying>$tot_yarn_dyeing)
                    {
                    	echo "Less Booking";
                    }
                    else if ($pre_cost_yarn_deying<$tot_yarn_dyeing) 
                    {
                    	echo "Over Booking";
                    } 
                    else if ($pre_cost_yarn_deying==$tot_yarn_dyeing) 
                    {
                    	echo "As Per";
                    } 
                    else
                    {
                    	echo "&nbsp;";
                    }
                    ?>
                    </td>
                </tr>
                <?
                $tot_pre_yarn_dyeing+=$pre_cost_yarn_deying;
                $total_yarn_dyeing+=$tot_yarn_dyeing;
				$total_yarn_dyeing_short+=$tot_yarn_dyeing_short;
                $tot_balance_yarn_dyeing+=$tot_balance;
                $i++;
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" align="right">  <b>Total</b></th>
                    <th align="right"><? echo number_format($tot_pre_yarn_dyeing,2); ?></th>
                    <th align="right"><? echo number_format($total_yarn_dyeing,2); ?></th>
                    <th align="right"><? echo number_format($total_yarn_dyeing_short,2); ?></th>
                    <th align="right"><? echo number_format($tot_balance_yarn_dyeing,2); ?></th>
                    <th></th>
                </tr>
            </tfoot>
		</table>
		<?
	}
	?>
    </div>
    <div>
		<?
        	echo signature_table(43, $cbo_company_name, "1000px");
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


if($action=="show_print2_booking_report")
{
	//echo "uuuu";die;
	//echo load_html_head_contents("Yarn Dyeing WO", "../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$job_quantity_arr=return_library_array( "select job_no,job_quantity from wo_po_details_master",'job_no','job_quantity');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//echo $show_comment;
	
	$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.ecchange_rate, a.is_short from wo_yarn_dyeing_mst a where a.id=$update_id"); 
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
		$exchange_rate=$result[csf('ecchange_rate')];
		$is_short=$result[csf('is_short')];
	}
	
	$varcode_work_order_no=$work_order;
	
	
	?>
	<div style="width:1000px" align="center">      
        <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
           <tr>
               <td width="700">                                     
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
                            <strong>Yarn Dyeing Work Order <? if($is_short==1) echo " (Short) "; ?> Without Lot</strong>
                             </td> 
                        </tr>
                  </table>
                </td>   
               <td width="250" id="barcode_img_id">
               
               </td>      
            </tr>
       </table>
		

       <?
		
		$style_no="select a.style_ref_no, a.job_no, a.buyer_name, b.po_number, b.file_no, b.grouping as inter_ref_no, b.po_quantity, p.id as dtls_id 
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b 
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0";
        $sql_result=sql_select($style_no);
		$style_all=$total_dtls_id=$tot_job_no=$total_buyer=$total_order_no=$all_file=$all_inter_ref="";//$total_order_qty=0;
		foreach($sql_result as $row)
		{
			$total_dtls_id.=$row[csf("dtls_id")].",";
			$style_all.=$row[csf("style_ref_no")].",";
			$tot_job_no.=$row[csf("job_no")].",";
			$total_buyer.=$row[csf("buyer_name")].",";
			$total_order_no.=$row[csf("po_number")].",";
			$all_file.=$row[csf("file_no")].",";
			$all_inter_ref.=$row[csf("inter_ref_no")].",";
			//$total_order_qty+=$row[csf("po_quantity")];
		}
		$total_dtls_id=chop($total_dtls_id," , ");$style_all=chop($style_all," , ");$tot_job_no=chop($tot_job_no," , ");$total_buyer=chop($total_buyer," , ");$total_order_no=chop($total_order_no," , ");$all_file=chop($all_file," , ");$all_inter_ref=chop($all_inter_ref," , ");
		
		$style_po="select a.job_no, sum(distinct b.po_quantity) as po_quantity
		from wo_yarn_dyeing_dtls p, wo_po_details_master a, wo_po_break_down b 
		where p.job_no_id=a.id and a.job_no=b.job_no_mst and p.mst_id=$update_id and  p.status_active=1 and p.is_deleted=0 group by a.job_no";
        $sql_po=sql_select($style_po);
		$total_order_qty=0;
		foreach($sql_po as $row)
		{
			$total_order_qty+=$row[csf('po_quantity')];
		}
	   
	   ?>


        <br>
       <table width="950" style="" align="center">
       		<tr>
            	<td width="350"  style="font-size:12px">
                        <table width="350" style="" align="left">
                            <tr>
                                <td   width="120"><b>To</b>   </td>
                                <td width="230">&nbsp;</td>
                            </tr>
                            <tr>
                            	<td colspan="2"><? echo $supplier_arr[$supplier_id];?></td>
                            </tr>
                                
                            <tr>
                                <td style="font-size:12px; padding-top:30px;"><b>Attention</b></td>
                                <td style="padding-top:30px;">:&nbsp;&nbsp;<? echo $attention; ?></td>
                            </tr> 
                            <tr>
                                <td style="font-size:12px;"><b>Cell</b></td>
                                <td>:&nbsp;&nbsp;</td>
                            </tr> 
                            <tr>
                                <td style="font-size:12px;"><b>Email</b></td>
                                <td>:&nbsp;&nbsp;</td>
                            </tr> 

                        </table>
                </td>
                <td width="350"  style="font-size:12px">
                		<table width="350" style="" align="left">

                			<tr>
                                <td width="120" style="font-size:12px"><b>Booking Date</b></td>
                                <td width="230">:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
                            </tr> 
                            <tr>
                                <td  ><b>Wo No.</b>   </td>
                                <td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
                            </tr> 
                         
				            <tr  style="font-size:12px">
				            	<td><b>Job No </b></td>
				                <td valign="top">:&nbsp;
									<?
										$all_job_arr=array_unique(explode(",",$tot_job_no));
										$all_job="";
										foreach($all_job_arr as $row)
										{
											$all_job.=$row.",";
										}
										$all_job=chop($all_job," , ");
										echo $all_job;
								 	?>
				                 </td>
				            </tr>
				            <!-- <tr style="font-size:12px">
				            	<td valign="top"><b>Buyer </b> </td>
				                <td valign="top">:&nbsp;
									<?
										 /*$buyer_id_arr=array_unique(explode(",",$total_buyer));
										 $all_buyer="";
										 foreach( $buyer_id_arr as $row)
										 {
											  $all_buyer.=$buyer_name_arr[$row].",";
										 }
										 $all_buyer=chop($all_buyer," , ");
										 echo $all_buyer;*/
									?>
				                </td>
				            </tr > -->
				    
				            <tr style="font-size:12px">
				            	<td valign="top"><b>Order Qty.</b></td>
				                <td valign="top">: &nbsp;
									<?
										echo $total_order_qty;
									
									?>
				                </td>
				            </tr>
				            <tr>
                                <td style="font-size:12px"><b>Currency</b></td>
                                <td >:&nbsp;&nbsp;<? echo $currency[$currency_id]; ?></td>
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
            <tr>
            	<td width="350"  style="font-size:12px"></td>
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
                </td>
            </tr>
       </table>
       </br>

        <div width="1000" align="left" >
        	<p><b>
        		&nbsp; Dear Sir, <br>
        		&nbsp; Reference to understand with you, please rearrange to dye the following yarn. Terms & conditions are stated below. So we would request you to like special care in this regards.
        	</b></p>
        </div>

        <br>
        <table width="1000"  align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
        	<thead>
            <tr>
            	<th width="30" align="center">Sl</th>
                <th width="70" align="center">Color</th>
                <th width="160" align="center">Yarn Description</th>
                <th width="60" align="center">Yarn Count</th>
                <th width="50" align="center">UOM</th>
                <th width="60" align="center">WO Qty</th>
                <th width="70" align="center">Dyeing Rate</th>
                <th width="70" align="center">Amount</th>
                <th  align="center" width="80" >Min Req. Cone</th>
                <th  align="center" width="70">Ref No.</th>
                <th width="90" align="center">App Batch No.</th>
                <th  align="center" >Remarks/ Shade</th>
            </tr>
            </thead>
            <?
			$sql_color="select id,product_id,job_no,job_no_id,yarn_color,yarn_description,count,color_range,yarn_wo_qty as yarn_wo_qty,dyeing_charge,amount as amount,app_batch_no,min_require_cone,referance_no, remarks, file_no, internal_ref_no
			from 
					wo_yarn_dyeing_dtls
			where 
					status_active=1 and id in($total_dtls_id)";		
			//echo $sql_color;die;
			$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";$job_strip_color=array();
			foreach($sql_result as $row)
			{
				$product_id=$row[csf("product_id")];
				//var_dump($product_id);
				$job_strip_color[$row[csf("job_no")]].=$row[csf("yarn_color")].",";
				$all_stripe_color.=$row[csf("yarn_color")].",";
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
	
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";	
				
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					
					<td align="center"><p><? echo $i; ?></p></td>
					<td><p><? echo $color_arr[$row[csf("yarn_color")]]; ?></p></td>
					<td><p> <?  echo $row[csf("yarn_description")]; ?></p></td>
					<td align="center"><p><? echo $count_arr[$row[csf("count")]]; ?></p></td>
					<td align="center"><p><? echo "KG"; ?></p></td>
					<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
					<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
					<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
					<td align="center"><p><? echo $row[csf("min_require_cone")]; ?></p></td>
					<td align="center"><p><? echo $row[csf("referance_no")]; ?></p></td>
					<td align="center"><p><? echo $row[csf("app_batch_no")]; ?></p></td>
					<td align="center"><p><? echo $row[csf("remarks")]; ?></p></td>

				</tr>
				<?
				$i++;
				$yarn_count_des="";
				$style_no="";
			}
			?>
             <tr>
                <td colspan="5" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
                <td align="right" ><b><? echo $total_qty; ?></b></td>
                <td align="right">&nbsp;</td>
                <td align="right"><b><? echo number_format($total_amount,2); ?></b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
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
            <td colspan="16" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </td> 
            </tr>
        </table>
        
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
        <table width="1000" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>
        
        <table  width="1000" class="rpt_table"    border="0" cellpadding="0" cellspacing="0" align="center">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
            </tr>
        </thead>
        <tbody>
        <?
		//echo "select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no'";
        $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no ='$txt_booking_no'");// quotation_id='$data'
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
        $data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con");// quotation_id='$data'
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
    <br>
    <?
    /*if($show_comment==1)
	{
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" >
            <thead>
                <tr> 
                    <th colspan="9" align="center"><b> Comments</b> </th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="120">Job No</th>
                    <th width="180">PO No</th>
                    <th width="150">Ship Date</th>
                    <th width="80">Pre-Cost Value</th>
                    <th width="80">WO Value</th>
                    <th width="80">Short WO Value</th>
                    <th width="80">Balance</th>
                    <th>Comments </th>
                </tr>
            </thead>
            <tbody>
            <?
			$all_stripe_color=implode(",",array_unique(explode(",",chop($all_stripe_color,","))));
            $job_cond_arr=array_unique(explode(",",$tot_job_no));
			//print_r($job_strip_color);
			if($db_type==0)
			{
				$job_po_sql=sql_select("select job_no_mst, group_concat(po_number) as po_number, group_concat(shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0 group by job_no_mst");
			}
			else
			{
				$job_po_sql=sql_select("select job_no_mst, listagg(cast(po_number as varchar(4000)),',') within group(order by po_number) as po_number, listagg(cast(shipment_date as varchar(4000)),',') within group(order by shipment_date) as shipment_date from wo_po_break_down where job_no_mst in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0 group by job_no_mst");
			}
			$job_po_data=array();
			foreach($job_po_sql as $row)
			{
				$job_po_data[$row[csf("job_no_mst")]]["po_number"]=$row[csf("po_number")];
				$job_po_data[$row[csf("job_no_mst")]]["shipment_date"]=$row[csf("shipment_date")];
			}
            $sql_stripe_required=sql_select("select job_no, stripe_color, sum(fabreqtotkg) as fabreqtotkg from wo_pre_stripe_color where job_no in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0 group by job_no, stripe_color");
            $strip_req_data=array();
            foreach($sql_stripe_required as $row)
            {
                $strip_req_data[$row[csf("job_no")]][$row[csf("stripe_color")]]=$row[csf("fabreqtotkg")];
            }
            $sql_strip_rate=sql_select("select job_no, color_break_down from wo_pre_cost_fab_conv_cost_dtls where job_no in('".implode($job_cond_arr,"','")."') and status_active=1 and is_deleted=0 and cons_process=30");
            $strip_req_rate=array();
            foreach($sql_strip_rate as $row)
            {
                $color_data=explode("__",$row[csf("color_break_down")]);
                foreach($color_data as $color_rate)
                {
					$color_rate_ref=explode("_",$color_rate);
					if($color_rate_ref[1]>0)
					{
						$strip_req_rate[$row[csf("job_no")]][$color_rate_ref[0]]=$color_rate_ref[1]*1;
					}
                    
                }
            }
			
			//var_dump($strip_req_rate);
			
            $job_wise_badge_val=array();
            foreach($job_strip_color as $job_no=>$strip_color)
            {
                $strip_color_arr=array_unique(explode(",",chop($strip_color,",")));
                foreach($strip_color_arr as $strip_color_id)
                {
                    $job_wise_badge_val[$job_no]+=$strip_req_data[$job_no][$strip_color_id]*$strip_req_rate[$job_no][$strip_color_id];
                }
            }
            //var_dump($job_wise_badge_val);die;
			$total_dtls_id=implode(",",array_unique(explode(",",$total_dtls_id)));
			$prev_wo_data=sql_select("select b.job_no_id, b.yarn_color, 
			sum(case when a.is_short<>1 then b.amount else 0 end) as amount, 
			sum(case when a.is_short=1 then b.amount else 0 end) as short_amount 
			from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form=125 and b.status_active=1 and b.is_deleted=0 and b.job_no in('".implode($job_cond_arr,"','")."') and b.id not in($total_dtls_id) group by b.job_no_id, b.yarn_color");
			foreach($prev_wo_data as $row)
			{
				$prev_job_entry[$row[csf("job_no_id")]][$row[csf("yarn_color")]]+=$row[csf("amount")];
				$prev_job_short_entry[$row[csf("job_no_id")]][$row[csf("yarn_color")]]+=$row[csf("short_amount")];
			}
            //$prev_job_entry=return_library_array( "select b.job_no_id, sum(b.amount) as job_amt from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form=41 and b.status_active=1 and b.is_deleted=0 and b.job_no in('".implode($job_cond_arr,"','")."') and b.id not in($total_dtls_id) and b.yarn_color in($all_stripe_color)  group by b.job_no_id", "job_no_id", "job_amt");//
            if($db_type==0)
			{
				$yarn_data="select b.job_no_id, max(a.currency) as currency, b.job_no, 
				sum(case when a.is_short<>1 then b.amount else 0 end) as amount, 
				sum(case when a.is_short=1 then b.amount else 0 end) as short_amount, group_concat(b.yarn_color) as yarn_color
            	from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a  
				where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by b.job_no_id, b.job_no";
			}
			else
			{
				$yarn_data="select b.job_no_id, max(a.currency) as currency, b.job_no,  
				sum(case when a.is_short<>1 then b.amount else 0 end) as amount, 
				sum(case when a.is_short=1 then b.amount else 0 end) as short_amount, 
				listagg( cast(b.yarn_color as varchar(4000)),',') within group (order by b.yarn_color) as yarn_color
            	from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst a  
				where a.id=b.mst_id and b.id in($total_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   
				group by b.job_no_id, b.job_no";
			}
            
            
            //echo $yarn_data."<br>";
            
            $nameArray=sql_select( $yarn_data );
            foreach ($nameArray as $selectResult)
            {
				$prev_qnty=0;
				$strip_color_arr=array_unique(explode(",",$selectResult[csf("yarn_color")]));
				foreach($strip_color_arr as $strip_id)
				{
					$prev_qnty+=$prev_job_entry[$selectResult[csf("job_no_id")]][$strip_id];
					$prev_short_qnty+=$prev_job_short_entry[$selectResult[csf("job_no_id")]][$strip_id];
					
				}
            
                if($selectResult[csf("currency")]==2)
                {
                    $tot_yarn_dyeing=$selectResult[csf("amount")]+$prev_qnty;
					$tot_yarn_dyeing_short=$selectResult[csf("short_amount")]+$prev_short_qnty;
                }
                else
                {
                    $current_date=date("Y-m-d");
                    $currency_rate=set_conversion_rate(2, $current_date );
                    $tot_yarn_dyeing=$selectResult[csf("amount")]+$prev_qnty;
                    $tot_yarn_dyeing=$tot_yarn_dyeing/$currency_rate;
					
					$tot_yarn_dyeing_short=$selectResult[csf("short_amount")]+$prev_short_qnty;
                    $tot_yarn_dyeing_short=$tot_yarn_dyeing_short/$currency_rate;
                }
                //echo $convamount;
                $po_no=$job_po_data[$selectResult[csf('job_no')]]['po_number'];
                $shipment_date=array_unique(explode(",",$job_po_data[$selectResult[csf('job_no')]]['shipment_date']));
				$ship_date="";
                foreach ($shipment_date as $date_row)
                {
                    if($ship_date=='') $ship_date=change_date_format($date_row); else $ship_date.=",".change_date_format($date_row);
                }
				$pre_cost_yarn_deying=$job_wise_badge_val[$selectResult[csf('job_no')]];
                ?>
                <tr>
                    <td align="center"><? echo $i;?></td>
                    <td><p><? echo $selectResult[csf('job_no')];?> &nbsp;</p></td>
                    <td><p><? echo $po_no;?>&nbsp;</p></td>
                    <td><p><? echo $ship_date;?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($pre_cost_yarn_deying,2); ?></td>
                    <td align="right"><? echo number_format($tot_yarn_dyeing,2); ?> </td>
                    <td align="right"><? echo number_format($tot_yarn_dyeing_short,2); ?> </td>
                    <td align="right"><? $tot_balance=$pre_cost_yarn_deying-$tot_yarn_dyeing; echo number_format($tot_balance,2); ?></td>
                    <td>
                    <? 
                    if( $pre_cost_yarn_deying>$tot_yarn_dyeing)
                    {
                    	echo "Less Booking";
                    }
                    else if ($pre_cost_yarn_deying<$tot_yarn_dyeing) 
                    {
                    	echo "Over Booking";
                    } 
                    else if ($pre_cost_yarn_deying==$tot_yarn_dyeing) 
                    {
                    	echo "As Per";
                    } 
                    else
                    {
                    	echo "&nbsp;";
                    }
                    ?>
                    </td>
                </tr>
                <?
                $tot_pre_yarn_dyeing+=$pre_cost_yarn_deying;
                $total_yarn_dyeing+=$tot_yarn_dyeing;
				$total_yarn_dyeing_short+=$tot_yarn_dyeing_short;
                $tot_balance_yarn_dyeing+=$tot_balance;
                $i++;
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" align="right">  <b>Total</b></th>
                    <th align="right"><? echo number_format($tot_pre_yarn_dyeing,2); ?></th>
                    <th align="right"><? echo number_format($total_yarn_dyeing,2); ?></th>
                    <th align="right"><? echo number_format($total_yarn_dyeing_short,2); ?></th>
                    <th align="right"><? echo number_format($tot_balance_yarn_dyeing,2); ?></th>
                    <th></th>
                </tr>
            </tfoot>
		</table>
		<?
	}*/
	?>
    </div>
    <div>
		<?
        	echo signature_table(43, $cbo_company_name, "1000px");
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


?>
