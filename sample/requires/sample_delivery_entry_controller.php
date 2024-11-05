<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
$sample_name_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$item_arrs=return_library_array("select id,item_name from lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");
$color_library=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
$size_library=return_library_array( "select id, size_name from lib_size where status_active=1",'id','size_name');
$req_library=return_library_array( "select id, requisition_number_prefix_num from sample_development_mst where status_active=1 and is_deleted=0 and entry_form_id in (117,203,449)",'id','requisition_number_prefix_num');

$delivery_sql=sql_select("select sample_dtls_part_tbl_id,sample_name,gmts_item_id,sum(ex_factory_qty) as qc_pass_qty  from  sample_ex_factory_dtls where entry_form_id=396 and status_active=1 and is_deleted=0  group by sample_dtls_part_tbl_id,sample_name,gmts_item_id ");
foreach ($delivery_sql as  $result)
{
 	$delivery_arr[$result[csf('sample_dtls_part_tbl_id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]=$result[csf('qc_pass_qty')];
}

$sample_dtls_sql=sql_select("select id,sample_name,gmts_item_id,sample_prod_qty from  sample_development_dtls where entry_form_id in (117,203,449) and status_active=1 and is_deleted=0  group by id,sample_name,gmts_item_id,sample_prod_qty order by id ");
foreach ($sample_dtls_sql as  $result)
{
 	$sample_dtls_arr[$result[csf('id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]=$result[csf('sample_prod_qty')];
}


if($db_type==2 || $db_type==1 )
{
	$mrr_date_check=" to_char(a.insert_date,'YYYY')";
}
else if ($db_type==0)
{
	$mrr_date_check=" year(a.insert_date)";
}

 $lc_num_arr = return_library_array("select id, export_lc_no from com_export_lc where status_active=1 and is_deleted=0", "id", "export_lc_no"  );
$sc_num_arr = return_library_array("select id, contract_no from com_sales_contract where status_active=1 and is_deleted=0", "id", "contract_no");


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
}



if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if($action=="populate_data_yet_to_cut")
{
	list($ex_fac_mst,$smp_tbl_id,$req_id,$sample_name,$gmts)=explode("__", $data);
	$val=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_ex_factory_mst_id=$ex_fac_mst and sample_dtls_part_tbl_id=$smp_tbl_id and sample_name=$sample_name and gmts_item_id=$gmts and entry_form_id=396 and sample_development_id=$req_id");
	echo $val;
	exit();
}

if($action=="sample_requisition_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../", 1, 1, $unicode);
?>
<html>
    <head>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		function search_populate(str)
		{
 			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style ID";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Enter Style Name";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
		}

		function js_set_value( mst_id )
		{
			document.getElementById('selected_id').value=mst_id;
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
	<div align="center" style="width:100%;" >
	<form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
    <table  width="1000" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
        <thead>
            <th colspan="7">
              <?
               echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
              ?>
            </th>
        </thead>
        <thead>
            <th width="140">Company Name</th>
            <th width="160">Buyer Name</th>
            <th width="130">Requisition No</th>
			<th width="100">Int. Ref. No </th>
            <th  width="130" >Style Ref</th>
            <th width="200">Est. Ship Date Range</th>
            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
        </thead>
        <tr class="general">
            <td width="140">
                <input type="hidden" id="selected_id"/>
                <? $is_disabled=0;
                    if($company)$is_disabled=1;
                    echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'sample_delivery_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",$is_disabled );
                ?>
            </td>
            <td id="buyer_td" width="160">
                 <?
                    echo create_drop_down( "cbo_buyer_name", 157, $blank_array,'', 1, "-- Select Buyer --" );
                ?>
            </td>
            <td width="130">
                <input type="text" style="width:130px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  />
            </td>
			<td>
				<input type="text" style="width:90px;" name="txt_int_ref_no" id="txt_int_ref_no" class="text_boxes" placeholder="Write"  />
			</td>
            <td width="130" align="center">
                <input type="text" style="width:130px" class="text_boxes"  name="txt_style_name" id="txt_style_name"  />
            </td>
            <td align="center">
                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"> To
                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
            </td>
            <td align="center">
                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name').value+'_'+document.getElementById('txt_int_ref_no').value, 'create_po_search_list_view', 'search_div', 'sample_delivery_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
            </td>
        </tr>
        <tr>
            <td align="center" colspan="6" valign="middle"><? echo load_month_buttons(1); ?></td>
        </tr>
    </table>
        <div id="search_div"></div>
    </form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	load_drop_down( 'sample_delivery_entry_controller',<? echo $company; ?>, 'load_drop_down_buyer', 'buyer_td' );
</script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);

	if ($data[2]!=0) $company=" and company_id='$data[2]'"; else { echo "Please Select Company First."; die; }
	if ($data[3]!=0) $buyer=" and buyer_name='$data[3]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[0]==1)
	{
	   if (trim($data[1])!="") $style_id_cond=" and requisition_number_prefix_num='$data[1]'"; else $style_id_cond="";
	   if ($data[6]!="") $style_cond=" and style_ref_no='$data[6]'"; else $style_cond="";
	}
	else if($data[0]==4 || $data[0]==0)
	{
	  if (trim($data[1])!="") $style_id_cond=" and requisition_number_prefix_num like '%$data[1]%' "; else $style_id_cond="";
	  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]%' "; else $style_cond="";
	}
	else if($data[0]==2)
	{
	  if (trim($data[1])!="") $style_id_cond=" and requisition_number_prefix_num like '$data[1]%' "; else $style_id_cond="";
	  if ($data[6]!="") $style_cond=" and style_ref_no like '$data[6]%' "; else $style_cond="";
	}
	else if($data[0]==3)
	{
	  if (trim($data[1])!="") $style_id_cond=" and requisition_number_prefix_num like '%$data[1]' "; else $style_id_cond="";
	  if ($data[6]!="") $style_cond=" and style_ref_no like '%$data[6]' "; else $style_cond="";
	}


	if($db_type==0)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $estimated_shipdate ="";
	}
	else if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$requistion_qnty=return_library_array( "SELECT sample_mst_id, sum(sample_prod_qty) as sample_prod_qty from sample_development_dtls where status_active=1 group by sample_mst_id ",'sample_mst_id','sample_prod_qty');

	$output_qnty=return_library_array( "SELECT sample_development_id , sum(qc_pass_qty) as qc_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b  where a.id=b.sample_sewing_output_mst_id and a.entry_form_id=130 and b.entry_form_id=130 and  b.status_active=1 and  a.status_active=1 group by sample_development_id ",'sample_development_id','qc_pass_qty');

	$bal_arr=array();
	foreach($requistion_qnty as $key=>$val)
	{
		$bal=$val-$output_qnty[$key];
		$bal_arr[$key]=$bal;
	}
	$txt_int_ref_no=trim(str_replace("'","",$data[7]));
	if($txt_int_ref_no!="") $ref_cond=" and internal_ref like '%$txt_int_ref_no%'";else $ref_cond="";


	$arr=array (1=>$buyer_arr,4=>$dealing_marchant,5=>$requistion_qnty,6=>$output_qnty,7=>$bal_arr,8=>$product_dept);



	   $sql= "select id, requisition_number_prefix_num, company_id, buyer_name, style_ref_no, product_dept,dealing_marchant,internal_ref from sample_development_mst where id in(select sample_development_id from sample_sewing_output_mst where entry_form_id=130  and status_active=1 and is_deleted=0) and entry_form_id in (117,203,449)  and status_active=1 and is_deleted=0  $ref_cond $company $buyer $style_id_cond $style_cond  $style_id_in_cond  $estimated_shipdate order by id asc";
	 $sql_chk=sql_select($sql);
	 if(count($sql_chk)<=0) ////Issue Id=11602
	 {
		  $sql= "select id, requisition_number_prefix_num, company_id, buyer_name, style_ref_no, product_dept,dealing_marchant,internal_ref from sample_development_mst where id in(select to_po_id from pro_gmts_delivery_dtls where   status_active=1 and is_deleted=0) and entry_form_id in (117,203,449)  and status_active=1 and is_deleted=0  $ref_cond $company $buyer $style_id_cond $style_cond  $style_id_in_cond  $estimated_shipdate order by id asc";

	 }

	echo  create_list_view("list_view", "Req No,Buyer Name,Style Name,Int. Ref. No ,Dealing Merchant,Req. Qty., Prod. Qty., Bal Qty.,Product Department", "60,140,130,80,120,80,80,80,90","900","240",0, $sql , "js_set_value", "id", "", 1, "0,buyer_name,0,0,dealing_marchant,id,id,id,product_dept", $arr, "requisition_number_prefix_num,buyer_name,style_ref_no,internal_ref,dealing_marchant,id,id,id,product_dept", "",'',"0,0,0,0,0,0,0,0") ;

	exit();
}

if($action=="populate_data_from_search_popup")
{
	$val=explode("**",$data);
	$datas=explode("__", $val[0]);
	$data=$datas[0];
	$challan_id=$datas[1];
	$type=$val[1];
	if($type==1)
	{
		$res = sql_select("select requisition_number_prefix_num,id,company_id,location_id,sample_stage_id,buyer_name,style_ref_no,item_name from sample_development_mst where entry_form_id in (117,203,449) and id=$data  and status_active=1 and is_deleted=0");

	  	foreach($res as $result)
		{
			echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
		    echo "load_drop_down( 'requires/sample_delivery_entry_controller', '".$result[csf("company_id")]."', 'load_drop_down_location', 'location_td' );\n";
		    echo "$('#cbo_location_name').val('".$result[csf('location_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
			echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
			echo "$('#txt_sample_requisition_id').val('".$result[csf('requisition_number_prefix_num')]."');\n";
			echo "$('#hidden_requisition_id').val('".$result[csf('id')]."');\n";
		    echo "$('#cbo_sample_stage').val('".$result[csf('sample_stage_id')]."');\n";
		}
	}
	else
	{
		$res = sql_select("select requisition_number_prefix_num,id,company_id,location_id,sample_stage_id,buyer_name,style_ref_no,item_name from sample_development_mst where entry_form_id in (117,203,449) and id=$data  and status_active=1 and is_deleted=0");

	  	foreach($res as $result)
		{
			echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
			echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
			echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
			echo "$('#txt_sample_requisition_id').val('".$result[csf('requisition_number_prefix_num')]."');\n";
			echo "$('#hidden_requisition_id').val('".$result[csf('id')]."');\n";
		    echo "$('#cbo_sample_stage').val('".$result[csf('sample_stage_id')]."');\n";
		}

	    $smp_mst_id = sql_select("SELECT location, id,sys_number_prefix,sys_number_prefix_num,sys_number, company_id, location, delivery_to, ex_factory_date, gp_no, final_destination,received_by from sample_ex_factory_mst where id='$challan_id' and entry_form_id=396 and status_active=1 and is_deleted=0");
		if(count($smp_mst_id)>0)
	 	{
	 		echo "load_drop_down( 'requires/sample_delivery_entry_controller', '".$smp_mst_id[0][csf('company_id')]."', 'load_drop_down_location', 'location_td' );\n";
		    echo "$('#cbo_location_name').val('".$smp_mst_id[0][csf('location')]."');\n";
	 		echo "$('#mst_update_id').val('".$smp_mst_id[0][csf('id')]."');\n";
			//echo "$('#cbo_company_name').val('".$smp_mst_id[0][csf('company_id')]."');\n";
			//echo "$('#cbo_location_name').val('".$smp_mst_id[0][csf('location')]."');\n";
	 		echo "$('#txt_challan_no').val('".$smp_mst_id[0][csf('sys_number')]."');\n";
	 		echo "$('#cbo_delivery_to').val('".$smp_mst_id[0][csf('delivery_to')]."');\n";
	 		echo "$('#txt_gp_no').val('".$smp_mst_id[0][csf('gp_no')]."');\n";
	 		echo "$('#txt_final_destination').val('".$smp_mst_id[0][csf('final_destination')]."');\n";
	 		echo "$('#txt_received_by').val('".$smp_mst_id[0][csf('received_by')]."');\n";
		}
	}
  	exit();
}

if($action=="show_sample_item_listview")
{
?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
        <thead>
           <th width="30">SL</th>
            <th width="120">Sample Name</th>
            <th width="120">Garments Item</th>
            <th width="75">Color</th>
            <th>Sample Qty</th>
        </thead>
		<?
		$i=1;

		$sqlResult = sql_select("select b.id,b.gmts_item_id,b.sample_name,b.sample_color,sum(c.total_qty) as size_qty from sample_development_mst a,sample_development_dtls b, sample_development_size
 c where a.id=b.sample_mst_id and b.id=c.dtls_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.sample_name,b.sample_color,b.gmts_item_id,b.id order by b.id asc");

		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_sample_item_data(<? echo $row[csf('id')];?>,<? echo $row[csf('sample_name')]; ?>,<? echo $row[csf('gmts_item_id')]; ?>,<? echo $row[csf('sample_color')]; ?>);">
				<td><? echo $i; ?></td>
				<td><p><? echo $sample_name_library[$row[csf('sample_name')]]; ?></p></td>
				<td><p><? echo $item_arrs[$row[csf('gmts_item_id')]]; ?></p></td>
				<td><p><? echo $color_library[$row[csf('sample_color')]]; ?></p></td>
				<td align="right"><?php echo $row[csf('size_qty')]; ?></td>
			</tr>
		<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="color_and_size_level")
{
	list($sample_dtls_part_tbl_id,$smp_id,$req_id,$gmts,$color)=explode('**',$data);
	$is_exists_wash_dyeing=return_field_value("id","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and sample_dtls_row_id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0 and entry_form_id=131");
	$val_req_embel=return_field_value("id","sample_development_fabric_acc","sample_name_re=$smp_id and gmts_item_id_re=$gmts and sample_mst_id=$req_id and status_active=1 and is_deleted=0 and form_type=3  and name_re not in (99)   ");
	 //echo $is_exists_wash_dyeing.'='.$val_req_embel.'=';
	//echo "select id from sample_development_fabric_acc where sample_name_re=$smp_id and gmts_item_id_re=$gmts and sample_mst_id=$req_id and status_active=1 and is_deleted=0 and form_type=3 and name_re<>1 and  name_re<>2 and name_re<>4";

	if($db_type==0)
	{
		$emb_names=sql_select("select  group_concat(name_re) as name from sample_development_fabric_acc where sample_mst_id=$req_id and form_type=3 and is_deleted=0 and status_active=1  and name_re<>1 and  name_re<>2 and name_re<>4 and sample_name_re=$smp_id and gmts_item_id_re=$gmts order by id asc");
	}
	else
	{
		$emb_names=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$req_id and form_type=3 and is_deleted=0 and status_active=1  and name_re not in(99)  and sample_name_re=$smp_id and gmts_item_id_re=$gmts order by id asc");
	}

 	$name_id=$emb_names[0][csf('name')];
	$name_arr=explode(',', $name_id);
	$last_emb=end($name_arr);
	if(!$last_emb)$last_emb=3;
	// echo "$('#txt_sample_requisition_id').val('".$last_emb."');\n"; die;

 	if(trim($is_exists_wash_dyeing)=='' && $val_req_embel=='')
	{
  		 //echo "$('#txt_sample_requisition_id').val('is_exist_emb');\n"; die;
  		$colorResult_qc_pass = sql_select("select b.sample_name, c.color_id, c.size_id, c.size_pass_qty
		from
			sample_ex_factory_mst a, sample_ex_factory_dtls b, sample_ex_factory_colorsize c
		where
			a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_development_id=$req_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=396 and b.entry_form_id=396 and c.entry_form_id=396 and b.sample_name=$smp_id and b.gmts_item_id=$gmts and b.sample_dtls_part_tbl_id=$sample_dtls_part_tbl_id");


		$msg_ttl="Total Sewing Qty";
		$colorResult = sql_select("select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=$smp_id  and b.item_number_id=$gmts and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=130 and a.entry_form_id=130 and c.entry_form_id=130 and c.color_id=$color");

		if(count($colorResult)<=0)  //Issue Id=11602
		{
		  $sql_sam_trans=sql_select("select b.from_po_id,b.production_quantity from pro_gmts_delivery_dtls b,pro_gmts_delivery_mst a where a.id=b.mst_id  and a.transfer_criteria=2 and  b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.to_po_id in($req_id) and b.item_number_id=$gmts");
		$msg_ttl="Transfer Qty";

		$production_quantity=0;
		foreach($sql_sam_trans as $row) //If Transfer found Finish Garments Order to Order Transfer
		{
			$from_po_id=$row[csf("from_po_id")];
			$production_quantity+=$row[csf("production_quantity")];
		}

			// echo "SELECT b.size_number_id as size_id,b.color_number_id as color_id, a.production_qnty as size_pass_qty from  pro_garments_production_mst c ,pro_garments_production_dtls a, wo_po_color_size_breakdown b where    c.id=a.mst_id and c.status_active=1  and a.color_size_break_down_id=b.id and c.po_break_down_id='$from_po_id' and b.po_break_down_id='$from_po_id' and b.item_number_id='$gmts'  and c.production_type=10 and a.trans_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";

			 $colorResult = sql_select("SELECT b.size_number_id as size_id,b.color_number_id as color_id, a.production_qnty as size_pass_qty from  pro_garments_production_mst c ,pro_garments_production_dtls a, wo_po_color_size_breakdown b where    c.id=a.mst_id and c.status_active=1  and a.color_size_break_down_id=b.id and c.po_break_down_id='$from_po_id' and b.po_break_down_id='$from_po_id' and b.item_number_id='$gmts'  and c.production_type=10 and a.trans_type=6  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

		}
		$serial=count($colorResult);
		if($serial==0 || $serial=='')
		{
			echo "alert('Cutting/Transfer data not available for this sample and item(Not Found Sewing Outpot)');\n";
		}

 		$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=130 and sample_dtls_row_id=$sample_dtls_part_tbl_id");
		if($total_cut>0)
		{
			$total_cut=$total_cut;
		}
		else $total_cut=$production_quantity;

		$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$smp_id and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=396 and sample_dtls_part_tbl_id=$sample_dtls_part_tbl_id");
		 echo "$('#dynamic_cut_qty').html('$msg_ttl');\n";
	}
	else
	{
		// echo "$('#txt_sample_requisition_id').val('33');\n"; die;
		$msg_ttl="Sample Embl. Qty";
		$colorResult_qc_pass = sql_select("select b.sample_name, c.color_id, c.size_id, c.size_pass_qty
		from
			sample_ex_factory_mst a, sample_ex_factory_dtls b, sample_ex_factory_colorsize c
		where
			a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.sample_development_id=$req_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=396 and b.entry_form_id=396 and c.entry_form_id=396 and b.sample_name=$smp_id and b.gmts_item_id=$gmts and b.sample_dtls_part_tbl_id=$sample_dtls_part_tbl_id");

		$colorResult = sql_select("select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=$smp_id and b.item_number_id=$gmts and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id in (128,131) and b.embel_name=$last_emb and c.color_id=$color");

		//echo "select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=$smp_id and b.item_number_id=$gmts and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id in (128,131) and b.embel_name=$last_emb and c.color_id=$color";

		if(count($colorResult)<=0)  //Issue Id=11602
		{
			$colorResult=sql_select("select b.from_po_id,b.production_quantity from pro_gmts_delivery_dtls b,pro_gmts_delivery_mst a where a.id=b.mst_id  and a.transfer_criteria=2 and  b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and b.to_po_id in($req_id) and b.item_number_id=$gmts");

			$msg_ttl="Transfer Qty";

			$production_quantity=0;
			foreach($colorResult as $row) //If Transfer found
			{
				$from_po_id=$row[csf("from_po_id")];
				$production_quantity+=$row[csf("production_quantity")];
			}

			$colorResult=sql_select("select a.color_id,a.qnty from sample_development_fabric_acc b,sample_develop_embl_color_size a where b.sample_mst_id=a.mst_id   and b.id=a.dtls_id and a.color_id=$color and b.sample_name_re=$smp_id and b.gmts_item_id_re=$gmts and b.sample_mst_id=$req_id and b.status_active=1 and b.is_deleted=0 and b.form_type=3  and  b.status_active=1 and b.is_deleted=0  and a.qnty=0 and a.status_active=1 and a.is_deleted=0 and a.color_id=$color ");

			if(count($colorResult)<=0)  //Emblishment
			{
				$msg_ttl="Emblishment Qty";
				$serial=count($colorResult);
				if($serial==0 || $serial=='')
				{
					echo "alert('as per requisitions you have to Sample Emblishment first then delivery');\n";
				}
			}
			else
			{
				$serial=count($colorResult);
				if($serial==0 || $serial=='')
				{
					echo "alert('as per requisitions you have to Sample Transfer first then delivery');\n";
				}
			}
		}
		else{
			$serial=count($colorResult);
			if($serial==0 || $serial=='')
			{
				  echo "alert('as per requisitions you have to $emblishment_name_array[$last_emb] first then delivery');\n";
			}
		}

  		 $total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id  in (128,131) and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$last_emb");

  		 $total_cuml=return_field_value("sum(ex_factory_qty) as ex_factory_qty","sample_ex_factory_dtls","sample_name=$smp_id and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=396 and sample_dtls_part_tbl_id=$sample_dtls_part_tbl_id","ex_factory_qty");
  		 // echo "$('#txt_sample_requisition_id').val('a $total_cuml');\n"; die;
		 if($last_emb)
		 {
  		 	echo "$('#dynamic_cut_qty').html('Total $emblishment_name_array[$last_emb] Qty');\n";
		 }
	}

	foreach($colorResult_qc_pass as $row)
	{
		$qcPassQtyArr[$row[csf("sample_name")]][$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];
		$totQcPassQty+=$row[csf("size_pass_qty")];
	}

	foreach($colorResult as $row)
	{
		$colorData[$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];
	}

	foreach($colorData as $color_id=>$color_value)
	{
		$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'"></span> </h3>';
		$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
		$i=1;
		foreach($color_value as $size_id=>$total_qty)
		{
			$colorID .= $color_id."*".$size_id.",";

			$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'" class="text_boxes_numeric" style="width:80px" placeholder="'.($total_qty-$qcPassQtyArr[$smp_id][$color_id][$size_id]).'" onblur="fn_total('.$color_id.','.$i.')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color_id.','.$i.')" '.$disable.'></td></tr>';

			$i++;
		}
		$colorHTML .= "</table></div>";
	}
	echo "$('#txt_cumul_delivery_qty').val('');\n";
	echo "$('#txt_total_finished_qty').val('');\n";
	echo "$('#txt_yet_to_delivery').val('');\n";
	echo "$('#txt_delivery_qty').val('');\n";
	//echo "$('#txt_reject_qnty').val('');\n";
	echo "$('#txt_remark').val('');\n";

	$value=return_field_value("gmts_item_id","sample_development_dtls","entry_form_id in (117,203,449) and sample_mst_id=$req_id and id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0");
	$qty=return_field_value("sum(total_qty)","sample_development_size","mst_id=$req_id and dtls_id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0");


	//$total_cuml=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$embel_name");


	$name_re_val=return_field_value("name_re","sample_development_fabric_acc","sample_mst_id=$req_id and sample_name_re=$smp_id and gmts_item_id_re=$gmts and status_active=1 and is_deleted=0 and form_type=3");
	$type_re_val=return_field_value("type_re","sample_development_fabric_acc","sample_mst_id=$req_id and sample_name_re=$smp_id and gmts_item_id_re=$gmts and status_active=1 and is_deleted=0 and form_type=3");
	echo "$('#cbo_item_name').val(".$value.");\n";
	echo "$('#txt_sample_qty').val(".$qty.");\n";
	echo "$('#txt_cumul_delivery_qty').val(".$total_cuml.");\n";
	echo "$('#txt_total_finished_qty').val(".$total_cut.");\n";
	echo "var smpqty=$('#txt_sample_qty').val();\n";
	echo "var total_cuts=$('#txt_total_finished_qty').val();\n";
	echo "var qcqty=$('#txt_cumul_delivery_qty').val();\n";
	if($total_cut)
	{
		echo "$('#txt_yet_to_delivery').val(total_cuts-qcqty);\n";
	}
	else
	{
		echo "$('#txt_yet_to_delivery').val(qcqty);\n";
	}
	echo "$('#dtls_update_id').val('');\n";
	echo "$('#cbo_sample_name').val(".$smp_id.");\n";
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	echo "$('#hidden_sample_dtls_tbl_id').val(".$sample_dtls_part_tbl_id.");\n";

    exit();
}

 if($action=="show_dtls_listview")
{
	list($smp_id,$mst_id)=explode('*',$data);
	//if($mst_id)$sql_con="sample_ex_factory_mst_id=$mst_id"; else $sql_con="sample_development_id=$smp_id";
	 $sql_con="sample_ex_factory_mst_id=$mst_id";
?>
 <fieldset style="overflow:hidden; margin:5px 0;">
     <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
            	<th width="30">&nbsp;</th>
				<th width="30">SL</th>
				<th width="50">Req No</th>
				<th width="110">Sample Name</th>
				<th width="110">Garments Item</th>
				<th width="80">Delivery Qnty</th>
				<th width="80">Carton Qnty</th>
				<th width="100">Shiping Status</th>
				<th>Remarks</th>
            </thead>
		</table>
	</div>
	<div style="width:100%; max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="ex_fac_tbl">
		<?php

$i = 1;
$kk = 1;
$sqlResult = sql_select("SELECT id,sample_ex_factory_mst_id, sample_development_id, sample_name,gmts_item_id, ex_factory_qty, carton_qty,carton_per_qty, remarks, shiping_status,sample_dtls_part_tbl_id from sample_ex_factory_dtls where $sql_con and status_active=1 and is_deleted=0 and entry_form_id=396");
foreach ($sqlResult as $row) {
	$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
	$click_var = "onclick=get_php_form_data('" . $row[csf('sample_development_id')] . '**' . $row[csf('sample_ex_factory_mst_id')] . '**' . $row[csf('id')] . '**' . $row[csf('sample_name')] . '**' . $row[csf('gmts_item_id')] . '**' . $row[csf('sample_dtls_part_tbl_id')] . "'" . ",'populate_input_form_data','requires/sample_delivery_entry_controller');";

	?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  >
				<td width="30" align="center"><input type="checkbox" value="<? echo $row[csf('id')];?>" id="isChk_<?echo $kk;?>"   > &nbsp;&nbsp;<? //echo $i; ?>
				<input type="hidden" value="<? echo $row[csf('sample_development_id')];?>" id="hiddenReqId_<?echo $kk++;?>"   >

				</td>
				<td onClick="<? echo $click_val;?>" width="30" align="center"><? echo $i; ?></td>
                <td width="50" onClick="<? echo $click_var;?>" ><? echo $req_library[$row[csf('sample_development_id')]]; ?></td>
                <td width="110" <? echo $click_var;?> ><p><? echo $sample_name_library[$row[csf('sample_name')]]; ?></p></td>
                <td width="110" <? echo $click_var;?> ><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                <td width="80" <? echo $click_var;?>  align="right"><?php echo $row[csf('ex_factory_qty')]; ?></td>
                <td align="right" <? echo $click_var;?>  width="80"><?php echo $row[csf('carton_qty')]; ?></td>
                 <td width="100" <? echo $click_var;?> >
					<?
						if($row[csf('shiping_status')]==1) echo "Full Shipment";
						if($row[csf('shiping_status')]==2) echo "Partial Shipment";
						if($row[csf('shiping_status')]==3) echo "Full Shipment/Closed";
                    ?>
                </td>
                <td <? echo $click_var;?> ><p><? echo $row[csf('remarks')]; ?></p></td>
			</tr>
			<?php
$i++;
}
?>
		</table>
    </div>
</fieldset>


<?
	exit();
}

if($action=="populate_input_form_data")
{

 	list($req_id,$mst_id,$dtls_id,$sample_name,$gmts,$sample_dtls_row_id)=explode('**',$data);
 	$is_exists_wash_dyeing=return_field_value("id","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and sample_dtls_row_id=$sample_dtls_row_id and status_active=1 and is_deleted=0 and entry_form_id=131");
	$val_req_embel=return_field_value("id","sample_development_fabric_acc","sample_name_re=$sample_name and gmts_item_id_re=$gmts and sample_mst_id=$req_id and status_active=1 and is_deleted=0 and form_type=3");
	if($db_type==0)
	{
		$emb_names=sql_select("select  group_concat(name_re) as name from sample_development_fabric_acc where sample_mst_id=$req_id and form_type=3 and is_deleted=0 and status_active=1  and name_re<>1 and  name_re<>2 and name_re<>4 and sample_name_re=$sample_name and gmts_item_id_re=$gmts order by id asc");
	}
	else
	{
		$emb_names=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$req_id and form_type=3 and is_deleted=0 and status_active=1  and name_re<>1 and  name_re<>2 and name_re<>4 and sample_name_re=$sample_name and gmts_item_id_re=$gmts order by id asc");
	}
 	$name_id=$emb_names[0][csf('name')];
 	$name_arr=explode(',', $name_id);
	$last_emb=end($name_arr);

 	if($db_type==2){$reporting_hour_fill=" TO_CHAR( a.reporting_hour,'HH24:MI' ) as reporting_hour ";}
	else{$reporting_hour_fill=" TIME_FORMAT( a.reporting_hour, '%H:%i' ) as reporting_hour ";}

	$colorResult = sql_select("SELECT a.id,a.order_type,a.sample_development_id,a.sample_name,a.gmts_item_id, a.ex_factory_qty,a.delivery_date, a.carton_qty, a.carton_per_qty, a.remarks, a.shiping_status,b.color_id as sample_color,b.size_id,b.size_pass_qty as size_qty from sample_ex_factory_dtls a, sample_ex_factory_colorsize b where a.id=b.sample_ex_factory_dtls_id and a.sample_development_id = $req_id   and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=396 and b.entry_form_id=396  ");


	foreach($colorResult as $row)
	{
		if($row[csf("sample_development_id")]){
			$colorTotal[$row[csf("id")]][$row[csf("sample_color")]]+=$row[csf("size_qty")];
			$colorData[$row[csf("id")]][$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];

			$sizeQcPassQty[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];
			$totSizeQcPassQty+=$row[csf("size_qty")];

			$dtlsArr[$row[csf("id")]]['order_type']=$row[csf('order_type')];
			$dtlsArr[$row[csf("id")]]['sample_development_id']=$row[csf('sample_development_id')];
			$dtlsArr[$row[csf("id")]]['sample_name']=$row[csf('sample_name')];
			$dtlsArr[$row[csf("id")]]['gmts_item_id']=$row[csf('gmts_item_id')];
			$dtlsArr[$row[csf("id")]]['ex_factory_qty']=$row[csf('ex_factory_qty')];
			$dtlsArr[$row[csf("id")]]['delivery_date']=$row[csf('delivery_date')];
			$dtlsArr[$row[csf("id")]]['carton_qty']=$row[csf('carton_qty')];
			$dtlsArr[$row[csf("id")]]['invoice_no']=$row[csf('invoice_no')];
			$dtlsArr[$row[csf("id")]]['lc_sc_id']=$row[csf('lc_sc_id')];
			$dtlsArr[$row[csf("id")]]['lc_sc_no']=$row[csf('lc_sc_no')];
			$dtlsArr[$row[csf("id")]]['carton_per_qty']=$row[csf('carton_per_qty')];
			$dtlsArr[$row[csf("id")]]['remarks']=$row[csf('remarks')];
			$dtlsArr[$row[csf("id")]]['shiping_status']=$row[csf('shiping_status')];
			$dtlsArr[$row[csf("id")]]['sample_dtls_part_tbl_id']=$row[csf('sample_dtls_part_tbl_id')];

		}
	}

		echo "$('#dtls_update_id').val('".$dtls_id."');\n";
		echo "$('#mst_update_id').val('".$mst_id."');\n";
		echo "$('#cbo_sample_name').val('".$dtlsArr[$dtls_id]['sample_name']."');\n";
	    echo "$('#txt_sample_requisition_id').val('".$req_library[$dtlsArr[$dtls_id]['sample_development_id']]."');\n";
	    echo "$('#hidden_requisition_id').val('".$dtlsArr[$dtls_id]['sample_development_id']."');\n";
		echo "$('#cbo_item_name').val('".$dtlsArr[$dtls_id]['gmts_item_id']."');\n";
 		echo "$('#txt_delivery_qty').val('".$dtlsArr[$dtls_id]['ex_factory_qty']."');\n";
 		echo "$('#hidden_previous_delv_qty').val('".$dtlsArr[$dtls_id]['ex_factory_qty']."');\n";
 	    echo "$('#txt_delivery_date').val('".change_date_format($dtlsArr[$dtls_id]['delivery_date'])."');\n";

  		echo "$('#txt_carton_qnty').val('".$dtlsArr[$dtls_id]['carton_qty']."');\n";
 		echo "$('#txt_remark').val('".$dtlsArr[$dtls_id]['remarks']."');\n";
 		echo "$('#cbo_shipping_status').val('".$dtlsArr[$dtls_id]['shiping_status']."');\n";



 		if($is_exist_emb=='' && $val_req_embel=='')
 		{
 			$sqlResult =  "select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c  where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']." and b.item_number_id=$gmts and a.entry_form_id=130 and b.entry_form_id=130 and c.entry_form_id=130 and b.sample_dtls_row_id='$sample_dtls_row_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" ;
 			$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=130 and sample_dtls_row_id='$sample_dtls_row_id'");
 			$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$sample_name and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=396 and sample_dtls_part_tbl_id='$sample_dtls_row_id' ");
 		}

 		else
 		{
 			$sqlResult =  "select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c  where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']." and b.item_number_id=".$dtlsArr[$dtls_id]['gmts_item_id']." and a.entry_form_id=131 and b.entry_form_id=131 and c.entry_form_id=131 and b.sample_dtls_row_id='$sample_dtls_row_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=$last_emb" ;
 			$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=131 and sample_dtls_row_id='$sample_dtls_row_id' and embel_name=$last_emb");
 			$total_cuml=return_field_value("sum(ex_factory_qty)","sample_ex_factory_dtls","sample_name=$sample_name and gmts_item_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=396 and sample_dtls_part_tbl_id='$sample_dtls_row_id'");

 		}

	      $sqlResult = sql_select($sqlResult);


  		foreach($sqlResult as $row)
		{
		  $smp_qty_arr[$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];
		}



		foreach($colorData[$dtls_id] as $color_id=>$color_value)
		{
			$colorHTML .= '<h3 align="left" id="accordion_h'.$color_id.'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color_id.'\', \'\',1)"> <span id="accordion_h'.$color_id.'span">+</span>'.$color_library[$color_id].' : <span id="total_'.$color_id.'">'.$colorTotal[$dtls_id][$color_id].'</span> </h3>';
			$colorHTML .= '<div id="content_search_panel_'.$color_id.'" style="display:none" class="accord_close"><table id="table_'.$color_id.'">';
			$i=1;
			foreach($color_value as $size_id=>$size_qty)
			{
				$colorID .= $color_id."*".$size_id.",";
				//echo "string main ".$smp_qty_arr[$color_id][$size_id]." done".$sizeQcPassQty[$color_id][$size_id]." another".$size_qty;

				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:80px" value="'.$size_qty.'" placeholder="'.( ($smp_qty_arr[$color_id][$size_id]-$sizeQcPassQty[$color_id][$size_id] )+$size_qty ).'" onblur="fn_total('.$color_id.','.$i.')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" value="'.$colorDataRej[$dtls_id][$color_id][$size_id].'" placeholder="Rej. Qty" onblur="fn_total_rej('.$color_id.','.$i.')" '.$disable.'></td></tr>';
			$i++;
			}
			$colorHTML .= "</table></div>";

		}
		//list($smp_id,$mst_id,$dtls_id,$sample_name,$gmts,$sample_dtls_row_id)=explode('**',$data);

	     echo "$('#txt_total_finished_qty').val(".$total_cut.");\n";
  		echo "$('#txt_cumul_delivery_qty').val(".$total_cuml.");\n";
		echo "var total_cuts=$('#txt_total_finished_qty').val();\n";
		echo "var qcqty=$('#txt_cumul_delivery_qty').val();\n";
		echo "$('#txt_yet_to_delivery').val(total_cuts*1-qcqty*1);\n";

		echo "set_button_status(1, permission, 'fnc_sample_delivery_entry',1,0);\n";
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
 		echo "$('#hidden_sample_dtls_tbl_id').val('".$sample_dtls_row_id."');\n";

 	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$sample_dtls="SELECT b.id, b.sample_mst_id,b.sample_prod_qty, b.gmts_item_id, b.sample_color, b.sample_name from sample_development_mst a , sample_development_dtls b where a.id = b.sample_mst_id and b.status_active=1 and a.company_id=$cbo_company_name and a.id = $hidden_requisition_id group by  b.id, b.sample_mst_id,b.sample_prod_qty, b.gmts_item_id, b.sample_color, b.sample_name order by b.sample_mst_id desc";
	$details_arr=array();
	$sample_qty_arr = array();
	foreach(sql_select($sample_dtls) as $val )
	{
		$details_arr[$val[csf("sample_mst_id")]]+=1;
		$sample_qty_arr[$val[csf("sample_mst_id")]][$val[csf("sample_name")]][$val[csf("gmts_item_id")]][$val[csf("sample_color")]] += $val[csf("sample_prod_qty")];
	}

	if ($operation==0) // Insert part----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		$flag=1;
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);

		if($mst_update_id=='')
		{
			// master part--------------------------------------------------------------;

			$shipping_flag = 0;

			$previous_sampl_data = sql_select("SELECT a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty ,b.color_id FROM sample_ex_factory_dtls a JOIN sample_ex_factory_colorsize b ON a.id=b.sample_ex_factory_dtls_id where a.sample_development_id = $hidden_requisition_id and sample_name = $cbo_sample_name and gmts_item_id=$cbo_item_name and a.status_active=1");
			$previous_sample_qty = array();
			foreach ($previous_sampl_data as $value) {
				$previous_sample_qty[$value[csf('sample_development_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('color_id')]] += $value[csf('ex_factory_qty')];
			}

			$color = explode("*",str_replace("'","",$colorIDvalue));

			$total_delivery_qty = $previous_sample_qty[str_replace("'","",$hidden_requisition_id)][str_replace("'","",$cbo_sample_name)][str_replace("'","",$cbo_item_name)][$color[0]] + str_replace("'","",$txt_delivery_qty);
			if($total_delivery_qty >= $sample_qty_arr[str_replace("'","",$hidden_requisition_id)][str_replace("'","",$cbo_sample_name)][str_replace("'","",$cbo_item_name)][$color[0]]){
				$cbo_shipping_status = 3;
				$shipping_flag = 1;
			}
			else
			{
				$cbo_shipping_status =2;
			}
			if(count($previous_sampl_data)>0 && $shipping_flag==1){
				$delete_lab_test=execute_query("Update sample_ex_factory_dtls set shiping_status=3 where sample_development_id = $hidden_requisition_id and sample_name = $cbo_sample_name and gmts_item_id=$cbo_item_name",1);
			}

			$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GDS', date("Y",time()), 5, "select a.sys_number_prefix,a.sys_number_prefix_num from sample_ex_factory_mst a where a.entry_form_id=396 and a.company_id=$cbo_company_name and $mrr_date_check =".date('Y',time())." order by a.id DESC", "sys_number_prefix", "sys_number_prefix_num" ));

			$mst_id=return_next_id("id", "sample_ex_factory_mst", 1);
			$field_array_mst="id,sys_number_prefix,sys_number_prefix_num,sys_number, company_id, location, delivery_to,  gp_no, final_destination,received_by, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
			$data_array_mst="(".$mst_id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_delivery_to.",".$txt_gp_no.",".$txt_final_destination.",".$txt_received_by.",".$user_id.",'".$pc_date_time."','1','0','396')";
 		 // Details part--------------------------------------------------------------;
			$dtls_id=return_next_id("id", "sample_ex_factory_dtls", 1);
			$field_array_dtls="id, sample_ex_factory_mst_id, sample_development_id,sample_dtls_part_tbl_id, sample_name,gmts_item_id,delivery_date, ex_factory_qty, carton_qty, remarks, shiping_status, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
			$data_array_dtls="(".$dtls_id.",".$mst_id.",".$hidden_requisition_id.",".$hidden_sample_dtls_tbl_id.",".$cbo_sample_name.",".$cbo_item_name.",".$txt_delivery_date.",".$txt_delivery_qty.",".$txt_carton_qnty.",".$txt_remark.",".$cbo_shipping_status.",".$user_id.",'".$pc_date_time."','1','0','396')";

		 // Color & Size Breakdown part--------------------------------------------------------------;
		 $field_array_brk="id, sample_ex_factory_mst_id, sample_ex_factory_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
		 $colorsize_brk_id=return_next_id("id", "sample_ex_factory_colorsize", 1);


			// size quantity value;
			$rowEx = explode("***",$colorIDvalue);
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $colorID.$sizeID;

				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0','396')";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0','396')";
				$colorsize_brk_id+=1;
				$j++;
			}


			//insert here----------------------------------------;
			$rID_mst=sql_insert("sample_ex_factory_mst",$field_array_mst,$data_array_mst,0);
			if($flag==1)
			{
				if($rID_mst) $flag=1; else $flag=0;
			}

			//$rID_dtls=execute_query("insert into sample_ex_factory_dtls ($field_array_dtls) values $data_array_dtls");
			$rID_dtls=sql_insert("sample_ex_factory_dtls",$field_array_dtls,$data_array_dtls,0);

			if($flag==1)
			{
				if($rID_dtls) $flag=1; else $flag=0;
			}

			$rID_brk=sql_insert("sample_ex_factory_colorsize",$field_array_brk,$data_array_brk,0);


			if($flag==1)
			{
				if($rID_brk) $flag=1; else $flag=0;
			}
			if($flag==1)
			{
				$db_table='sample_development_dtls';
				$field_array_update="is_complete_prod";
				$data_array_update="".'1'."";
				$total=$delivery_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)]+ str_replace("'","", $txt_delivery_qty);
   				if($total >= $sample_dtls_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)])
 				{
 					$rID_up=sql_multirow_update($db_table, $field_array_update, $data_array_update,"id",$hidden_sample_dtls_tbl_id,1);

 				}

			}

			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "0**".$mst_id."**".$hidden_requisition_id."**".$new_mrr_number[0]."**".$hidden_sample_dtls_tbl_id;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "0**".$mst_id."**".$hidden_requisition_id."**".$new_mrr_number[0]."**".$hidden_sample_dtls_tbl_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**0**"."&nbsp;"."**0";
				}
			}

		}
		else
		{
			$shipping_flag = 0;
			$previous_sampl_data = sql_select("SELECT a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty ,b.color_id FROM sample_ex_factory_dtls a JOIN sample_ex_factory_colorsize b ON a.id=b.sample_ex_factory_dtls_id where a.sample_development_id = $hidden_requisition_id and sample_name = $cbo_sample_name and gmts_item_id=$cbo_item_name and a.status_active=1");
			$previous_sample_qty = array();
			foreach ($previous_sampl_data as $value) {
				$previous_sample_qty[$value[csf('sample_development_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('color_id')]] += $value[csf('ex_factory_qty')];
			}

			$color = explode("*",str_replace("'","",$colorIDvalue));

			$total_delivery_qty = $previous_sample_qty[str_replace("'","",$hidden_requisition_id)][str_replace("'","",$cbo_sample_name)][str_replace("'","",$cbo_item_name)][$color[0]] + str_replace("'","",$txt_delivery_qty);
			if($total_delivery_qty >= $sample_qty_arr[str_replace("'","",$hidden_requisition_id)][str_replace("'","",$cbo_sample_name)][str_replace("'","",$cbo_item_name)][$color[0]]){
				$cbo_shipping_status = 3;
				$shipping_flag = 1;
			}
			else
			{
				$cbo_shipping_status =2;
			}

			if(count($previous_sampl_data)>0 && $shipping_flag==1){
				$delete_lab_test=execute_query("Update sample_ex_factory_dtls set shiping_status=3 where sample_development_id = $hidden_requisition_id and sample_name = $cbo_sample_name and gmts_item_id=$cbo_item_name",1);
			}

 			$dtls_id=return_next_id("id", "sample_ex_factory_dtls", 1);
			$field_array_dtls="id, sample_ex_factory_mst_id, sample_development_id,sample_dtls_part_tbl_id, sample_name,gmts_item_id,delivery_date, ex_factory_qty, carton_qty, remarks, shiping_status, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
			$data_array_dtls="(".$dtls_id.",".$mst_update_id.",".$hidden_requisition_id.",".$hidden_sample_dtls_tbl_id.",".$cbo_sample_name.",".$cbo_item_name.",".$txt_delivery_date.",".$txt_delivery_qty.",".$txt_carton_qnty.",".$txt_remark.",".$cbo_shipping_status.",".$user_id.",'".$pc_date_time."','1','0','396')";

			// Color & Size Breakdown part--------------------------------------------------------------;
			$field_array_brk="id, sample_ex_factory_mst_id, sample_ex_factory_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
			$colorsize_brk_id=return_next_id("id", "sample_ex_factory_colorsize", 1);

			// size quantity value;
			$rowEx = explode("***",$colorIDvalue);
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];

				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0','396')";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0','396')";
				$colorsize_brk_id+=1;
				$j++;
			}


			$rID_dtls=sql_insert("sample_ex_factory_dtls",$field_array_dtls,$data_array_dtls,0);
 			if($flag==1)
			{
				if($rID_dtls) $flag=1; else $flag=0;
			}

			$rID_brk=sql_insert("sample_ex_factory_colorsize",$field_array_brk,$data_array_brk,0);
			if($flag==1)
			{
				if($rID_brk) $flag=1; else $flag=0;
			}
			if($flag==1)
			{
				$db_table='sample_development_dtls';
				$field_array_update="is_complete_prod";
				$data_array_update="".'1'."";
				$total=$delivery_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)]+ str_replace("'","", $txt_delivery_qty);
   				if($total >= $sample_dtls_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)])
 				{
 					$rID_up=sql_multirow_update($db_table, $field_array_update, $data_array_update,"id",$hidden_sample_dtls_tbl_id,1);

 				}

			}

			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "0**".$mst_update_id."**".$hidden_requisition_id."**".str_replace("'","",$txt_challan_no)."**".$hidden_sample_dtls_tbl_id;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**0**"."&nbsp;"."**0";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "0**".$mst_update_id."**".$hidden_requisition_id."**".str_replace("'","",$txt_challan_no)."**".$hidden_sample_dtls_tbl_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**0**"."&nbsp;"."**0";
				}
			}

		}

		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update part ------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		$shipping_flag = 0;
		$previous_sampl_data = sql_select("SELECT a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty ,b.color_id FROM sample_ex_factory_dtls a JOIN sample_ex_factory_colorsize b ON a.id=b.sample_ex_factory_dtls_id where a.sample_development_id = $hidden_requisition_id and sample_name = $cbo_sample_name and gmts_item_id=$cbo_item_name and a.id <> $dtls_update_id and a.status_active=1");
		$previous_sample_qty = array();
		foreach ($previous_sampl_data as $value) {
			$previous_sample_qty[$value[csf('sample_development_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('color_id')]] += $value[csf('ex_factory_qty')];
		}

		$color = explode("*",str_replace("'","",$colorIDvalue));

		$total_delivery_qty = $previous_sample_qty[str_replace("'","",$hidden_requisition_id)][str_replace("'","",$cbo_sample_name)][str_replace("'","",$cbo_item_name)][$color[0]] + str_replace("'","",$txt_delivery_qty);
		if($total_delivery_qty >= $sample_qty_arr[str_replace("'","",$hidden_requisition_id)][str_replace("'","",$cbo_sample_name)][str_replace("'","",$cbo_item_name)][$color[0]]){
			$cbo_shipping_status = 3;
			$shipping_flag = 1;
		}
		else
		{
			$cbo_shipping_status =2;
		}

		if(count($previous_sampl_data)>0 && $shipping_flag==1){
			$delete_lab_test=execute_query("Update sample_ex_factory_dtls set shiping_status=3 where sample_development_id = $hidden_requisition_id and sample_name = $cbo_sample_name and gmts_item_id=$cbo_item_name",1);
		}
 		if($mst_update_id!='')
		{
			// master part--------------------------------------------------------------;
		   $field_array_mst="delivery_to*gp_no*final_destination*received_by*updated_by*update_date";
			$data_array_mst="".$cbo_delivery_to."*".$txt_gp_no."*".$txt_final_destination."*".$txt_received_by."*".$user_id."*'".$pc_date_time."'";

 			$rID_mst=sql_update("sample_ex_factory_mst",$field_array_mst,$data_array_mst,"id","".$mst_update_id."",1);

			$field_array_dtls="ex_factory_qty*delivery_date*carton_qty*remarks*shiping_status*updated_by*update_date";
			$data_array_dtls="".$txt_delivery_qty."*".$txt_delivery_date."*".$txt_carton_qnty."*".$txt_remark."*".$cbo_shipping_status."*".$user_id."*'".$pc_date_time."'";
			$rID_dtls=sql_update("sample_ex_factory_dtls",$field_array_dtls,$data_array_dtls,"id","".$dtls_update_id."",1);


		// Color & Size Breakdown part--------------------------------------------------------------;
		if($rID_mst && $rID_dtls)
		{
		$rID_brk_delete = execute_query("DELETE from sample_ex_factory_colorsize WHERE sample_ex_factory_dtls_id=$dtls_update_id");//Delete fast;
		}

		$field_array_brk="id, sample_ex_factory_mst_id, sample_ex_factory_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
		$colorsize_brk_id=return_next_id("id", "sample_ex_factory_colorsize", 1);

			// size quantity value;
			$rowEx = explode("***",$colorIDvalue);
			$data_array_brk="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$colorID = $colorAndSizeAndValue_arr[0];
				$sizeID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];

				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0',396)";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0',396)";
				$colorsize_brk_id+=1;
				$j++;
			}

			$rID_brk=sql_insert("sample_ex_factory_colorsize",$field_array_brk,$data_array_brk,0);

			$old_delv=str_replace("'", "", $hidden_previous_delv_qty);
			$new_delv=str_replace("'", "", $txt_delivery_qty);
			$delv_diff=$new_delv - $old_delv;
  			if($rID_brk==1 && $delv_diff !=0)
			{
				$db_table='sample_development_dtls';
				$field_array_update="is_complete_prod";
				$data_array_update="".'1'."";
 				$total=($delivery_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)] )+ ($delv_diff);

				$rIds=sql_multirow_update($db_table, $field_array_update, "''","id",$hidden_sample_dtls_tbl_id,1);
   				if($total >= $sample_dtls_arr[str_replace("'", "", $hidden_sample_dtls_tbl_id)][str_replace("'", "", $cbo_sample_name)][str_replace("'", "", $cbo_item_name)])
 				{
  					$rID_up=sql_multirow_update($db_table, $field_array_update, $data_array_update,"id",$hidden_sample_dtls_tbl_id,1);

 				}



			}

 		//$rID_mst.','.$rID_dtls.','.$rID_brk; mysql_query("ROLLBACK");die;
		//-------------------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($rID_mst && $rID_dtls && $rID_brk_delete && $rID_brk)
				{
					mysql_query("COMMIT");
					echo "1**".$mst_update_id."**".$hidden_requisition_id."**0"."**".$hidden_sample_dtls_tbl_id;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$mst_update_id."**".$hidden_requisition_id."**0";
				}

			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID_mst && $rID_dtls && $rID_brk_delete && $rID_brk)
				{
					oci_commit($con);
					echo "1**".$mst_update_id."**".$hidden_requisition_id."**0"."**".$hidden_sample_dtls_tbl_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$mst_update_id."**".$hidden_requisition_id."**0";
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
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);


 		$rID = sql_delete("sample_ex_factory_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id  ',$dtls_update_id,1);
		$dtlsrID = sql_delete("sample_ex_factory_colorsize","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'sample_ex_factory_dtls_id',$dtls_update_id,1);

 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".$mst_update_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$mst_update_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".$mst_update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_update_id;
			}
		}
		disconnect($con);
		die;
	}

}

if($action=="sys_surch_popup")
{
extract($_REQUEST);
echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
$is_disabled=0;
if($company)$is_disabled=1;
 ?>
	<script>
	function js_set_value(smp,mst)
	{
 		$("#selected_id").val(smp+'*'+mst);
    	parent.emailwindow.hide();
 	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
             <thead>
                <th width="160">Company</th>
                <th width="150">Buyer Name</th>
                <th width="100">Challan No</th>
                <th width="100">Req No</th>
                <th width="200">Delivery Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </thead>
            <tr align="center">
                <td>
                <?
                echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select --", $company, "",$is_disabled );
                ?>
                </td>
                <td>
                <?
					echo create_drop_down( "cbo_buyer_name", 150, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );
				?>
                </td>
                <td align="center" >
                    <input type="text" style="width:100px" class="text_boxes"  name="txt_challan" id="txt_challan" />
                </td>
                <td align="center" >
                    <input type="text" style="width:100px" class="text_boxes"  name="txt_req_no" id="txt_req_no" />
                </td>
                <td align="center">
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly> To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                </td>
                <td align="center">
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_challan').value+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_delivery_search_list', 'search_div_delivery', 'sample_delivery_entry_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td align="center" height="40" colspan="6" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="selected_id" >
                </td>
            </tr>
        </table>
        <div id="search_div_delivery" style="margin-top:20px;"></div>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}

if($action=="create_delivery_search_list")
{

	$buyer_name_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');
	$team_leader_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 group by id,team_member_name order by team_member_name",'id','team_member_name');

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
//	$exfact_qty_arr=return_library_array( "select sample_ex_factory_mst_id, sum(ex_factory_qty) as ex_factory_qty from sample_ex_factory_dtls where status_active=1  group by sample_ex_factory_mst_id",'sample_ex_factory_mst_id','ex_factory_qty');

	$sql_del=sql_select("select sample_development_id,sample_ex_factory_mst_id, (ex_factory_qty) as ex_factory_qty from sample_ex_factory_dtls where status_active=1 ");
	foreach($sql_del as $row)
	{
		$exfact_qty_arr[$row[csf('sample_ex_factory_mst_id')]]+=$row[csf('ex_factory_qty')];
		$smp_id_arr[$row[csf('sample_ex_factory_mst_id')]]=$row[csf('sample_development_id')];
	}


	$trans_com_arr=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and a.is_deleted=0 and b.party_type=35   order by a.supplier_name","id","supplier_name");

$sql_dev= sql_select("select b.sample_ex_factory_mst_id,b.sample_development_id,a.buyer_name from sample_ex_factory_dtls b,sample_development_mst a where a.id=b.sample_development_id and b.status_active=1");
	foreach($sql_dev as $row)
	{
		$smp_buyer_arr[$row[csf('sample_development_id')]]=$row[csf('buyer_name')];
	}
	//$smp_buyer_arr=return_library_array( "select sample_ex_factory_mst_id,sample_development_id from sample_ex_factory_dtls b,sample_development_mst a where a.id=b.sample_development_id and b.status_active=1",'sample_ex_factory_mst_id','sample_development_id');


	$ex_data = explode("_",$data);
	//echo "<pre>";print_r($ex_data);die;
	$company = $ex_data[0];
	$cbo_delivery_to = $ex_data[1];
	$challan = $ex_data[2];
	$req_no=$ex_data[3];
	$txt_date_from = $ex_data[4];
	$txt_date_to = $ex_data[5];
	 $date_cond="";
 	if($txt_date_from!="" and  $txt_date_to!="")
	{
		if($db_type==0){$date_cond  = " select a.sample_ex_factory_mst_id from sample_ex_factory_dtls a where delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $date_cond= " and a.id in(select b.sample_ex_factory_mst_id  from sample_ex_factory_dtls b where a.delivery_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to)) ."')";}

	}
	if(trim($company)!=0) {$com_cond= " and a.company_id= '$company'";} else {$com_cond="";}
	if(trim($cbo_delivery_to)!=0) {$delv_cond= " and a.delivery_to= '$cbo_delivery_to'";} else {$delv_cond="";}

	if(trim($challan)!="") {$challan_cond= " and a.sys_number_prefix_num= '$challan'";} else {$challan_cond="";}

	if(trim($req_no)!="") {$req_cond= " and a.id in (  select b.sample_ex_factory_mst_id from sample_ex_factory_dtls b where sample_development_id c in(select c.id from sample_development_mst c where c.requisition_number_prefix_num ='$req_no')) ";} else {$req_cond="";}

	$sql = "select a.id,a.sys_number, a.company_id, a.location, a.delivery_to,  a.gp_no, a.final_destination, a.received_by from   sample_ex_factory_mst a join sample_ex_factory_dtls b on a.id=b.sample_ex_factory_mst_id  where a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $com_cond $delv_cond $challan_cond $date_cond $req_cond and a.entry_form_id=396 order by id desc";
	// echo $sql;die;
	$result = sql_select($sql);
   ?>
     	<table cellspacing="0" width="1030" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
					<th width="37" >SL</th>
					<th width="120" >Sys Num</th>
					<th width="160" >Buyer Name</th>
 					<th width="120" >GP No</th>
					<th width="120" >Final Destination</th>
					<th width="130" >Received By</th>
					<th>Ex-fact Qty</th>
            </thead>
     	</table>
     <div style="width:1030px; max-height:220px;overflow-y:scroll;" >
        <table cellspacing="0" width="1012" class="rpt_table" cellpadding="0" border="1" rules="all" id="tbl_invoice_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$dev_buyer=$smp_buyer_arr[$smp_id_arr[$row[csf('id')]]];
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $smp_id_arr[$row[csf('id')]];?>,<? echo $row[csf('id')];?>);" >
                    <td width="35" align="center"><? echo $i; ?></td>
                    <td width="120" align="center"><p><? echo $row[csf("sys_number")]; ?></p></td>
                    <td width="160" align="center"><p><? echo $buyer_name_arr[$dev_buyer]; ?>&nbsp;</p></td>

					<td width="120" align="center"><p><? echo $row[csf("gp_no")]; ?>&nbsp;</p></td>
                    <td width="120" align="center"><p><? echo $row[csf("final_destination")]; ?>&nbsp;</p></td>
                    <td width="130" align="center"><p><? echo $row[csf("received_by")];?>&nbsp;</p></td>
                    <td align="center"><p><?  echo number_format($exfact_qty_arr[$row[csf("id")]],0,"","");?></p></td>

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

if($action=="delivery_print")
{
	extract($_REQUEST);
	list($mst_id,$dtls_id,$company_name,$sample_name,$gmts,$req_id,$hidden_sample_dtls_tbl_id)=explode('*',$data);

	$req_id_arr=array_unique(explode(",", $req_id));
	$req_id=implode(',', $req_id_arr);
	echo load_html_head_contents("Garments Delivery Info","../../", 1, 1, $unicode);
	//  echo load_html_head_contentss("Garments Delivery Info","../../", 1, 1, $unicode,'','');

 	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$buyer_add=return_library_array( "select id, address_1 from lib_buyer", "id", "address_1"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_lib=return_library_array( "select id,location_name from lib_location where company_id='$company_name'", "id", "location_name"  );

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_name'","image_location");
	$supplier_lib=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );

	$order_arr=return_library_array( "select  id, job_no from wo_po_details_master  where status_active=1 and is_deleted=0", "id","job_no"  );
  	$dealing_marchant=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name", "id","team_member_name"  );
    $mst_data=sql_select("select * from sample_ex_factory_mst where id=$mst_id and status_active=1 and entry_form_id=396");
    $dtls_data=sql_select("select delivery_date from sample_ex_factory_dtls where sample_ex_factory_mst_id=$mst_id and status_active=1 and entry_form_id=396");

    	$req_array=array();
	$req_sql=sql_select("select * from sample_development_mst where is_deleted=0 and status_active=1 and entry_form_id in (117,203,449) and id in($req_id)");
 	foreach($req_sql as $row)
	{
		$req_array[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
		$req_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$req_array[$row[csf("id")]]['dealing_marchant']=$row[csf("dealing_marchant")];
		$req_array[$row[csf("id")]]['requisition_number_prefix_num']=$row[csf("requisition_number_prefix_num")];
		$req_array[$row[csf("id")]]['sample_stage_id']=$row[csf("sample_stage_id")];
		$req_array[$row[csf("id")]]['quotation_id']=$row[csf("quotation_id")];
	}

		$sql="SELECT * from sample_ex_factory_dtls where sample_ex_factory_mst_id=$mst_id  and status_active=1 and is_deleted=0 and entry_form_id=396";
		$result=sql_select($sql);
		 foreach($result as $row){
			$data_arr[]=array(
				'sample_name'=>$row[csf('sample_name')],
				'sample_development_id'=>$row[csf('sample_development_id')],
				'invoice_no'=>$row[csf('invoice_no')],
				'ex_factory_qty'=>$row[csf('ex_factory_qty')],
				'carton_qty'=>$row[csf('carton_qty')],
				'remarks'=>$row[csf('remarks')]
				);
			$smp_id_arr[]=$row[csf('sample_name')];
			$gmts_id_arr[]=$row[csf('gmts_item_id')];

		 }
	$smp_id= implode(',',$smp_id_arr);
	$gmts_id= implode(',',$gmts_id_arr);


	$result_smp=sql_select("SELECT b.sample_name,a.buyer_name,a.style_ref_no,b.gmts_item_id from sample_development_mst a,sample_development_dtls b where a.company_id=$company_name and a.entry_form_id in (117,203,449) and b.entry_form_id in (117,203,449) and a.id in($req_id) group by a.buyer_name,a.style_ref_no,b.gmts_item_id,b.sample_name");

	foreach($result_smp as $row){
		$buy_data[$row[csf('sample_name')]]=$buyer_lib[$row[csf('buyer_name')]];
		$sty_data[$row[csf('sample_name')]]=$row[csf('style_ref_no')];
		$item_data[$row[csf('sample_name')]]=$garments_item[$row[csf('gmts_item_id')]];
	}
?>


<div style="width:1000px; border:1px solid #fff; ">
    <table width="100%" cellspacing="0" align="right" cellpadding="10"  >
        <tr>
            <td colspan="6" align="center" valign="middle">
                <img src="../<? echo $image_location; ?>" height="50" width="60" style="float:left;">
                <strong style=" font-size:xx-large;"><? echo $company_library[$company_name]; ?>
                  </strong>

             </td>
             <td colspan="3" id="barcode_img_id"></td>

        </tr>
		<tr>
        	<td colspan="6" align="center" style="font-size:16px;" >
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_name");
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
						<? if($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
						<? if($result[csf('email')]!="") echo $result[csf('email')].", ";?>
						<? if($result[csf('website')]!="") echo $result[csf('website')];
					}

                ?>

            </td>
            <td colspan="3"></td>
         </tr>
         <tr>
            <td colspan="6" align="center"><strong>100% Export Oriented</strong></td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:20px;" align="center"><strong>Sample Delivery Challan</strong></td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="9" height="5">  </td>
        </tr>

        <tr>
			<td colspan="2" style="font-size:16px;">  <?
			$all_buyer="";
			foreach($req_id_arr as $v)
			{
				if($all_buyer) $all_buyer.=" , ".$buyer_lib[$req_array[$v]['buyer_name']];
				else $all_buyer=$buyer_lib[$req_array[$v]['buyer_name']];
			}
			 echo $all_buyer;
			  ?>  </td>
			<td align="left"><strong>Delivery Date :</strong></td>
			<td  colspan="2" style="font-size:16px;" align="left"> <? echo change_date_format($dtls_data[0][csf('delivery_date')]); ?> </td>

			<td colspan="4"></td>

	    </tr>
	    <tr>
			<td colspan="2" style="max-width: 150px;"><?
			$buyer_st="";
			foreach($req_ibuyer_std_arr as $vl)
			{
				if($buyer_st) $buyer_st.=" , ".$buyer_add[$req_array[$vl]['buyer_name']];
				else $buyer_st=$buyer_add[$req_array[$vl]['buyer_name']];
			}
			 echo $buyer_st;

			 ?></td>
			<td align="left" valign="top"><strong>Challan No :</strong></td>
			<td  colspan="2" style="font-size:16px;" align="left" valign="top"> <? echo $mst_data[0][csf('sys_number')]; ?>

			</td>

			<td colspan="4"></td>
	    </tr>

	    <tr>
	    	<td colspan="2"></td>
			<td align="left" valign="top"><strong>Requisition No :</strong></td>
			<td colspan="2" style="font-size:16px;" align="left" valign="top"> <?

			$req_st="";
			foreach($req_id_arr as $vals)
			{

				if($req_st) $req_st.=" , ".$req_array[$vals]['requisition_number_prefix_num'];
				else $req_st .=$req_array[$vals]['requisition_number_prefix_num'];
			}
			 echo $req_st;

			  ?>

			</td>

			<td colspan="4"></td>
	    </tr>

	    <tr>
	    	<td colspan="2"></td>
			<td align="left" valign="top"><strong>Dealing Merchant :</strong></td>
			<td  colspan="2" style="font-size:16px;" align="left" valign="top"> <?
			$dealing_marchant_st="";
			foreach($req_id_arr as $vals)
			{

				if($dealing_marchant_st) $dealing_marchant_st.=" , ".$dealing_marchant[$req_array[$vals]['dealing_marchant']];
				else $dealing_marchant_st .=$dealing_marchant[$req_array[$vals]['dealing_marchant']];
			}
			 echo $dealing_marchant_st;

			   ?> </td>

			<td colspan="4"></td>
	    </tr>
    </table>
    </div>


	<?
	 $sql="	SELECT a.id, a.gmts_item_id,a.sample_development_id,a.remarks ,a.sample_name,	b.color_id,b.size_id,sum(a.ex_factory_qty) as ex_factory_qty,sum(a.carton_qty) as carton_qty,sum(b.size_pass_qty) as  size_pass_qty
	from 		sample_ex_factory_dtls a,sample_ex_factory_colorsize b	where
		a.sample_ex_factory_mst_id=$mst_id   and a.id=b.sample_ex_factory_dtls_id and a.id in($dtls_id)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.entry_form_id=396 and b.entry_form_id=396 group by a.id,a.gmts_item_id,a.sample_development_id,a.remarks ,a.sample_name,	b.color_id,b.size_id";

	$sql_result=sql_select($sql);
	foreach ($sql_result as $rows)
	{
  		$dtls_data['sample_name']=$rows[csf('sample_name')];
		$dtls_data['ex_factory_qty']+=$rows[csf('ex_factory_qty')];
		$dtls_data['carton_qty']+=$rows[csf('carton_qty')];
		$dtls_data['remarks']=$rows[csf('remarks')];
		$dtls_data['gmts_item_id']=$rows[csf('gmts_item_id')];
		$size_arr[$rows[csf('size_id')]]=$rows[csf('size_id')];

		$tot_color_good_qty[$rows[csf('id')]][$rows[csf('color_id')]]+=$rows[csf('size_pass_qty')];
		$tot_size_good_qty[$rows[csf('size_id')]]+=$rows[csf('size_pass_qty')];
		$good_qty[$rows[csf('id')]][$rows[csf('color_id')]][$rows[csf('size_id')]]+=$rows[csf('size_pass_qty')];



 	}
 		//print_r($tot_size_good_qty);die;
 		$tot_size=count($size_arr);
		$width=round((100/$tot_size)+25);
		$width_2=($width*$tot_size)+650;

		$sql2="	SELECT a.id, a.gmts_item_id,a.sample_development_id,a.remarks ,a.sample_name,	b.color_id ,sum(a.ex_factory_qty) as ex_factory_qty,sum(a.carton_qty) as carton_qty,sum(b.size_pass_qty) as  size_pass_qty
	from 		sample_ex_factory_dtls a,sample_ex_factory_colorsize b	where
		a.sample_ex_factory_mst_id=$mst_id   and a.id=b.sample_ex_factory_dtls_id and a.id in($dtls_id)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.entry_form_id=396 and b.entry_form_id=396 group by a.id,a.gmts_item_id,a.sample_development_id,a.remarks ,a.sample_name,	b.color_id";

	$sql_result=sql_select($sql2);




	?>
	<div style="width:<? echo $width_2;?>px;float: left;margin-top: 20px;">
     <table align="right" cellspacing="0" width="100%"  border="1" class="rpt_table" rules="all">
      <thead bgcolor="#dddddd" align="center">
        <tr>
			<th width="30" rowspan="2">SL</th>
			<th width="100" rowspan="2" >Buyer</th>
			<th width="120" rowspan="2">Style Ref.</th>
			<?
			if($req_array[$req_id]['sample_stage_id']==1)
			{
			?>
			<th width="80" rowspan="2">Job No</th>
			<?
			}

			?>
			<th width="120" rowspan="2">Sample</th>
			<th width="120" rowspan="2">Item Name</th>
			<th width="80" rowspan="2">Color</th>
			<th colspan="<? echo $tot_size;?>">Size</th>
			<th width="80" rowspan="2" >Delivery Qty</th>
 			<th rowspan="2">Remarks</th>
        </tr>
        <tr>
				<?
                foreach ($size_arr as $size_id)
                {
                    ?>
                    <th align="center" width="<? echo $width;?>"><? echo $size_library[$size_id]; ?></th>
                    <?
                }
                ?>
         </tr>
       </thead>
        <tbody>
        <?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;$cols_pan=6;
            foreach($sql_result as $key=>$vals)
            {
            	  $color_id=$vals[csf("color_id")];
            	  $dtls_ids=$vals[csf("id")];
            	  $req_id=$vals[csf("sample_development_id")];
            	  $sample_name=$vals[csf("sample_name")];
            	  $gmts_item_id=$vals[csf("gmts_item_id")];
           		  $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";


                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="right"><? echo $i; ?></td>
                        <td align="center"> <? echo $buyer_lib[$req_array[$req_id]['buyer_name']]; ?> </td>
                        <td align="center"> <? echo $req_array[$req_id]['style_ref_no']; ?> </td>
                        <?
                        if($req_array[$req_id]['sample_stage_id']==1)
                         {
                         	$cols_pan=7;
                         	?>
                         	<td align="center"> <? echo $order_arr[$req_array[$req_id]['quotation_id']]; ?> </td>

                        <?
                         }

                        ?>
                        <td align="center"> <? echo $sample_name_library[$sample_name]; ?> </td>
                         <td align="center"> <? echo $garments_item[$gmts_item_id]; ?> </td>
                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                        <?
                        foreach ($size_arr as $size_id)
                        {
                            ?>
                            <td align="right"><? echo $good_qty[$dtls_ids][$color_id][$size_id]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? echo $tot_color_good_qty[$dtls_ids][$color_id]; ?></td>
                           <td> <? echo $vals[csf('remarks')]; ?> </td>
                     </tr>
                    <?
					$i++;
                }
            ?>

        </tbody>
        <tr>
            <td colspan="<? echo $cols_pan;?>" align="right"><b> Grand Total :</b> </td>
            <?
				 foreach ($size_arr as $size_id)
				{
					?>
                    <td align="right"><?php echo $tot_size_good_qty[$size_id]; ?></td>
                    <?
				}
			?>
            <td colspan="2">&nbsp;</td>
        </tr>


    </table>
	</div>
		 <?
            echo signature_table(127, $company_name, "810px");
              $barcode_no=$mst_data[0][csf('sys_number')];
         ?>
	</div>
	<script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $barcode_no; ?>','barcode_img_id');
    </script>
<?
exit();
}

function load_html_head_contentss($title, $path, $filter, $popup, $unicode, $multi_select, $am_chart, $jqlatest)
 {
	 $html='
	 <!DOCTYPE HTML>
	 <html>
	 <head>
	 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	 <title>'.$title.'</title>
 	 <script src="'.$path.'includes/functions.js" type="text/javascript"></script>';


	 if( $jqlatest==1 ) $html .=' <script type="text/javascript" src="'.$path.'js/jquery_latest.js"></script>';
	 else  $html .=' <script type="text/javascript" src="'.$path.'js/jquery.js"></script>';

	 if ( $filter!="" )
	 	$html .='
	 		<link href="'.$path.'css/filtergrid.css" rel="stylesheet" type="text/css" media="screen" />
     		<script src="'.$path.'js/tablefilter.js" type="text/javascript"></script>';

	if ( $popup!="" )
	 	$html .='
			<link href="'.$path.'css/modal_window.css" rel="stylesheet" type="text/css" />
			<script type="text/javascript" src="'.$path.'js/modal_window.js"></script>';
    if ( $unicode!="" )
	 	$html .='
     		<script type="text/javascript" src="'.$path.'js/driver.phonetic.js" ></script>
    		<script type="text/javascript" src="'.$path.'js/driver.probhat.js" ></script>
    		<script type="text/javascript" src="'.$path.'js/engine.js" ></script>';

	if ( $multi_select!="" )
	 	$html .='
			 <script src="'.$path.'js/multi_select.js" type="text/javascript"></script>';
	if ($am_chart!="")
			$html .='
				<script type="text/javascript" src="'.$path.'ext_resource/amcharts/flash/swfobject.js" ></script>
				<script type="text/javascript" src="'.$path.'ext_resource/amcharts/javascript/amcharts.js" ></script>
				<script type="text/javascript" src="'.$path.'ext_resource/amcharts/javascript/amfallback.js" ></script>
				<script type="text/javascript" src="'.$path.'ext_resource/amcharts/javascript/raphael.js" ></script>
				<script type="text/javascript" src="'.$path.'js/chart/logic_chart.js" ></script>';

	 return $html; die;
 }
?>