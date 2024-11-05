<?
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
$sample_name_library=return_library_array( "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$color_library=return_library_array( "select id, color_name from lib_color","id","color_name");
$size_library=return_library_array( "select id, size_name from lib_size","id","size_name");

if ($action=="load_drop_down_emb_name")
{
	$ex_data=explode("__",$data);
 	echo create_drop_down( "cbo_embel_name", 150, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "load_drop_down( 'requires/sample_embellishment_issue_controller', this.value+'**'+".$ex_data[1].", 'load_drop_down_emb_receive_type', 'emb_type_td' );",'',$ex_data[0],'','','99' );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if($action=="load_drop_down_emb_receive_type")
{
	$data=explode("**",$data);
	$emb_name=$data[0];
	$mstId=$data[1];
	
	$sqls=sql_select("select  listagg(type_re,',') WITHIN GROUP (ORDER BY id) as type from sample_development_fabric_acc where sample_mst_id=$mstId and form_type=3 and is_deleted=0 and status_active=1 and name_re=$emb_name order by id asc");

    $embl_type="";

    foreach ($sqls as $key => $value)
    {
  	    foreach ($value as $key => $value2)
	  	{
	  		$embl_type.=$value2;
	  	}
    }
  	if($emb_name==1)
		echo create_drop_down( "cbo_embel_type", 150, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "","","$embl_type" );
	elseif($emb_name==2)
		echo create_drop_down( "cbo_embel_type", 150, $emblishment_embroy_type,"", 1, "--- Select Embroidery---", $selected, "" ,"","$embl_type" );
	elseif($emb_name==3)
		echo create_drop_down( "cbo_embel_type", 150, $emblishment_wash_type,"", 1, "--- Select wash---", $selected, "","","$embl_type" );
	elseif($emb_name==4)
		echo create_drop_down( "cbo_embel_type", 150, $emblishment_spwork_type,"", 1, "--- Select Special Works---", $selected, "","","$embl_type" );
	elseif($emb_name==5)
		echo create_drop_down( "cbo_embel_type", 150, $emblishment_gmts_type,"", 1, "--- Select---", $selected, "","","$embl_type" );
	else
		echo create_drop_down( "cbo_embel_type", 150, $blank_array,"", 1, "--- Select---", $selected, "" );
	exit();
}

if($action=="populate_data_yet_to_cut")
{
	$data=explode("__", $data);
	$val=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_sewing_output_mst_id=$data[1] and sample_dtls_row_id=$data[0] and entry_form_id=338 and sample_name=$data[2] and item_number_id=$data[3] and embel_name=$data[4]");
	echo $val;
	exit();
}

if($action=="embellishment_name_as_per_req")
{
	$emb="";
	$data=explode("**", $data);
	$emb_names=sql_select("select listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$data[0] and form_type=3 and is_deleted=0 and status_active=1  and name_re<>3 and  name_re<>5 and sample_name_re=$data[1] and gmts_item_id_re=$data[2] order by id asc");
	$name_id=$emb_names[0][csf('name')];
	$name_arr=explode(',', $name_id);
	$i=1;
	foreach ($name_arr as $key => $value)
	{
		$emb.=$i.'-'.$emblishment_name_array[$value].',';
		$i++;
	}
	echo rtrim($emb,',');
	exit();
}

if($action=="embellishment_id_as_per_req")
{
	$emb_id="";
	$data=explode("**", $data);
	$emb_names=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$data[0] and form_type=3 and is_deleted=0 and status_active=1  and name_re<>3 and  name_re<>5 and sample_name_re=$data[1] and gmts_item_id_re=$data[2] order by id asc");
	$name_id=$emb_names[0][csf('name')];
	$name_arr=explode(',', $name_id);
	$i=1;
	foreach ($name_arr as $key => $value)
	{
		$emb_id.=$value.',';
		$i++;
	}
	echo rtrim($emb_id,',');
	exit();
}

if($action=="embellishment_type_as_per_req")
{
	list($emb_name,$req_id,$sample_name,$gmts)=explode('__',$data);
	$val=return_field_value("type_re","sample_development_fabric_acc","form_type=3 and sample_mst_id=$req_id and name_re=$emb_name and sample_name_re=$sample_name and gmts_item_id_re=$gmts and status_active=1 and is_deleted=0");
	echo trim($val);
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/sample_embellishment_issue_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 150, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
}

if($action=="load_drop_down_sewing_output")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_embellishment_company", 150, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sample_embellishment_issue_controller');",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_embellishment_company", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sample_embellishment_issue_controller');",0,0 );
		}
	}
	else if($data==1)
	{
 		echo create_drop_down( "cbo_embellishment_company", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", $selected_company,  "load_drop_down( 'requires/sample_embellishment_issue_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 );
	}
 	else
	{
		echo create_drop_down( "cbo_embellishment_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	exit();
}

if($action=="sample_requisition_popup")
{
	extract($_REQUEST);
	//echo "<pre>";print_r($_REQUEST);
	echo load_html_head_contents("Sample Requisition Info","../../", 1, 1, $unicode);
    ?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });

		function js_set_value( mst_id )
		{
			document.getElementById('selected_id').value=mst_id;
			parent.emailwindow.hide();
		}
		function set_checkvalue()
		{
			if(document.getElementById('chk_shipped_po').value==0) document.getElementById('chk_shipped_po').value=1;
			else document.getElementById('chk_shipped_po').value=0;
		}

    </script>
    </head>
    <body>
	<div align="center" style="width:100%;" >
        <form name="searchsampledevelopmentfrm_1"  id="searchsampledevelopmentfrm_1" autocomplete="off">
            <table width="850" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
                <thead>
                    <tr>
                        <th colspan="4"><? echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                        <th colspan="3"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_shipped_po">Full Shipped PO</th>
                    </tr>
                    <tr>
                        <th width="140">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Requisition No</th>
						<th width="100">Int. Ref. No </th>  
                        <th width="100" >Style Ref</th>
                        <th width="130" colspan="2">Est. Ship Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                    <input type="hidden" id="selected_id"/>
                    <?
                    if ($company != 0){
                        echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'sample_embellishment_issue_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
                    }
                    else
                    {
                        echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'sample_embellishment_issue_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",0);
                    }
                    ?>
                    </td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                    <td><input type="text" style="width:70px" class="text_boxes"  name="txt_style_id" id="txt_style_id"  /></td>
					<td><input type="text" style="width:90px;" name="txt_int_ref_no" id="txt_int_ref_no" class="text_boxes" placeholder="Write"  /></td>
                    <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style_name" id="txt_style_name"  /></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td>
                    <td><input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name').value+'_'+document.getElementById('chk_shipped_po').value+'_'+document.getElementById('txt_int_ref_no').value, 'create_po_search_list_view', 'search_div', 'sample_embellishment_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div id="search_div"></div>
        </form>
	</div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
	    load_drop_down( 'sample_embellishment_issue_controller',<? echo $company; ?>, 'load_drop_down_buyer', 'buyer_td' );
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
		$yearCond="YEAR(insert_date)";
	}
	else if($db_type==2)
	{
		if ($data[4]!="" &&  $data[5]!="") $estimated_shipdate  = "and estimated_shipdate  between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $estimated_shipdate ="";
		$yearCond="to_char(insert_date,'YYYY')";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$dealing_marchant=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$team_leader=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

	if (trim($data[1])!="") $style_id_cond2=" and c.requisition_number_prefix_num like '$data[1]%' "; else $style_id_cond2="";
	$sample_dtls="SELECT b.id, b.sample_mst_id from sample_development_mst a join sample_development_dtls b on a.id = b.sample_mst_id where b.status_active=1 and a.company_id='$data[2]' group by  b.id, b.sample_mst_id ";
	$details_arr=array();
	foreach(sql_select($sample_dtls) as $val )
	{
		$details_arr[$val[csf("sample_mst_id")]]+=1;
	}
	$ex_fac_sql="SELECT  a.sample_development_id, a.sample_name,  a.shiping_status,b.color_id from sample_ex_factory_dtls a,sample_ex_factory_colorsize b, sample_development_mst c where a.id=b.sample_ex_factory_dtls_id and a.status_active=1 and b.status_active=1 and c.id = a.sample_development_id $style_id_cond2  group by a.sample_development_id, a.sample_name,  a.shiping_status,b.color_id order by shiping_status desc";
	$delv_arr=array();
	$cnt_arr=array();
	foreach(sql_select($ex_fac_sql) as $v)
	{
		$delv_arr[$v[csf("sample_development_id")]]=$v[csf("shiping_status")];
		$cnt_arr[$v[csf("sample_development_id")]]+=1;
	}
	//print_r($cnt_arr);die;
	$req_id=0;
	$req_id1=0;
	foreach($delv_arr as $key=>$v)
	{
		if($v==3 && $cnt_arr[$key]==$details_arr[$key])
		{
			if($req_id==0) $req_id.=$key; else  $req_id.=','.$key;
			$req_idArr[$req_id]=$req_id;
		}
		else{
			if($req_id1==0) $req_id1.=$key; else  $req_id1.=','.$key;
		}
	}
		
		$txt_int_ref_no=trim(str_replace("'","",$data[8]));
		if($txt_int_ref_no!="") $ref_cond=" and internal_ref like '%$txt_int_ref_no%'";else $ref_cond="";
		
	
	//$arr=array (2=>$buyer_arr,4=>$product_dept,6=>$dealing_marchant);
	$red_idcond=where_con_using_array($req_idArr,0,'a.sample_development_id');

	if($data[7] == 1){
		   $sql= "select id, requisition_number_prefix_num as prefix_no, company_id, buyer_name, style_ref_no, product_dept, internal_ref,dealing_marchant, $yearCond as year from sample_development_mst where id in(select a.sample_development_id from sample_sewing_output_mst a join sample_development_fabric_acc b on a.sample_development_id = b.sample_mst_id where a.entry_form_id=127 and a.status_active=1 and a.is_deleted=0 and b.form_type=3 and b.status_active=1 and b.is_deleted=0 $red_idcond group by a.sample_development_id) and entry_form_id  in (117,203,449)  and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $ref_cond $estimated_shipdate order by id DESC";
	}
	else{
		//$sql= "select id, requisition_number_prefix_num as prefix_no, company_id, buyer_name, style_ref_no, product_dept, internal_ref,dealing_marchant, $yearCond as year from sample_development_mst where id in(select a.sample_development_id from sample_sewing_output_mst a join sample_development_fabric_acc b on a.sample_development_id = b.sample_mst_id where a.entry_form_id=127 and a.status_active=1 and a.is_deleted=0 and b.form_type=3 and b.status_active=1 and b.is_deleted=0 and a.sample_development_id not in ($req_id) group by a.sample_development_id) and entry_form_id  in (117,203,449) and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $ref_cond $estimated_shipdate order by id DESC";
		$sql= "select id, requisition_number_prefix_num as prefix_no, company_id, buyer_name, style_ref_no, product_dept, internal_ref,dealing_marchant, $yearCond as year from sample_development_mst where    entry_form_id  in (117,203,449) and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $ref_cond $estimated_shipdate order by id DESC";
	}
	// echo $sql;
	$sql_result=sql_select($sql);
	
	 $sql_chk="select a.sample_development_id from sample_sewing_output_mst a join sample_development_fabric_acc b on a.sample_development_id = b.sample_mst_id where a.entry_form_id in(127,130) and a.status_active=1 and a.is_deleted=0 and b.form_type=3 and b.status_active=1 and b.is_deleted=0  group by a.sample_development_id";
	$sql_result_chk=sql_select($sql_chk);
	foreach($sql_result_chk as $row)
	{
		$emb_issue_check_arr[$row[csf('sample_development_id')]]=$row[csf('sample_development_id')];
		
	}
//echo $data[7].'DS';
	if($data[7] ==0){	
		foreach($sql_result as $result)
		{
			//echo $emb_issue_check_arr[$result[csf('id')]].'d';
			if($emb_issue_check_arr[$result[csf('id')]]!="")
			{
			$emb_issue_arr[$result[csf('id')]]['prefix_no']=$result[csf('prefix_no')];
			$emb_issue_arr[$result[csf('id')]]['company_id']=$result[csf('company_id')];
			$emb_issue_arr[$result[csf('id')]]['buyer_name']=$buyer_arr[$result[csf('buyer_name')]];
			$emb_issue_arr[$result[csf('id')]]['style_ref_no']=$result[csf('style_ref_no')];
			$emb_issue_arr[$result[csf('id')]]['product_dept']=$product_dept[$result[csf('product_dept')]];
			$emb_issue_arr[$result[csf('id')]]['internal_ref']=$result[csf('internal_ref')];
			$emb_issue_arr[$result[csf('id')]]['dealing_marchant']=$dealing_marchant[$result[csf('dealing_marchant')]];
			$emb_issue_arr[$result[csf('id')]]['year']=$result[csf('year')];
			$emb_issue_arr[$result[csf('id')]]['req_id']=$result[csf('id')];
			}
			 
		}
	}
	else
	{
		foreach($sql_result as $result)
		{
			 
			$emb_issue_arr[$result[csf('id')]]['prefix_no']=$result[csf('prefix_no')];
			$emb_issue_arr[$result[csf('id')]]['company_id']=$result[csf('company_id')];
			$emb_issue_arr[$result[csf('id')]]['buyer_name']=$buyer_arr[$result[csf('buyer_name')]];
			$emb_issue_arr[$result[csf('id')]]['style_ref_no']=$result[csf('style_ref_no')];
			$emb_issue_arr[$result[csf('id')]]['product_dept']=$product_dept[$result[csf('product_dept')]];
			$emb_issue_arr[$result[csf('id')]]['internal_ref']=$result[csf('internal_ref')];
			$emb_issue_arr[$result[csf('id')]]['dealing_marchant']=$dealing_marchant[$result[csf('dealing_marchant')]];
			$emb_issue_arr[$result[csf('id')]]['year']=$result[csf('year')];
			$emb_issue_arr[$result[csf('id')]]['req_id']=$result[csf('id')];
			 
			 
		}
	}
	?>
      <table width="720" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
                <thead>
                   
                    <tr>
                        <th width="20">SL#</th>
                        <th width="70">Req No</th>
                        <th width="70">Year</th>
                        <th width="100">Buyer Name</th>
						<th width="200">Style Name</th>  
                        <th width="70" >Prod. Dep.</th>
                        <th width="70" >Int. Ref. No</th>
                        <th width="70" >Dealing Merchant</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
               foreach($emb_issue_arr as $req_id=>$row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
             	 <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $req_id; ?>);">
                    <td> <? echo $i; ?> </td>
                    <td> <? echo $row['prefix_no']; ?> </td>
                    <td> <? echo $row['year']; ?> </td>
                    <td><p> <? echo $row['buyer_name']; ?></p> </td>
                    <td> <p><? echo $row['style_ref_no']; ?></p> </td>
                    <td> <p><? echo $row['product_dept']; ?> </td>
                    <td> <p><? echo $row['internal_ref']; ?></p> </td>
                    <td> <p><? echo $row['dealing_marchant']; ?></p> </td>
                   
                 </tr>
                 <?
				$i++;
				}
				 ?>
                    
                </tbody>
             </table>
                    
                    
    
    <?
	//echo $sql;
	//echo  create_list_view("list_view", "Req No,Year,Buyer Name,Style Name,Product Department,Int. Ref. No ,Dealing Merchant", "60,60,140,120,90,90,90,130","900","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,0,product_dept,0,dealing_marchant", $arr , "requisition_number_prefix_num,year,buyer_name,style_ref_no,product_dept,internal_ref,dealing_marchant", "",'','0,0,0,0,0,0') ;

	exit();
}

if($action=="populate_data_from_search_popup")
{
    $res = sql_select("select id,requisition_number_prefix_num,company_id,buyer_name,style_ref_no,item_name from sample_development_mst where entry_form_id in (117,203,449) and id=$data  and status_active=1 and is_deleted=0");
    $sqls=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$data and form_type=3 and is_deleted=0 and status_active=1  and name_re not in (99) order by id asc");
	
	 
		$sql_data_change=sql_select("SELECT is_updated from sample_development_dtls where entry_form_id=203 and sample_mst_id='$data' and  is_deleted=0  and status_active=1 and is_updated=1 order by id ASC");	
		if(count($sql_data_change)<=0) 
		{
			$sql_data_change=sql_select("SELECT is_updated from sample_development_fabric_acc where sample_mst_id='$data' and form_type=3 and  is_deleted=0  and status_active=1 and is_updated=1 order by id ASC");
		}
		$change_found="";
		if(count($sql_data_change)>0)
		{
			$change_found="Sample req changed found.";
		}
		
	 //Fabric
	 
		
	 
	

    $names_re="";

    foreach ($sqls as $key => $value)
    {
  	    foreach ($value as $key => $value2)
	  	{
	  		$names_re.=$value2;
	  	}
    }

  	foreach($res as $result)
	{
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
	    echo "load_drop_down( 'requires/sample_embellishment_issue_controller', '".$result[csf("company_id")]."', 'load_drop_down_location', 'location_td' );\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#Req_color_td').html('".$change_found."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_sample_requisition_id').val('".$result[csf('requisition_number_prefix_num')]."');\n";
		echo "$('#hidden_requisition_id').val('".$result[csf('id')]."');\n";
		echo "load_drop_down( 'requires/sample_embellishment_issue_controller', '".$names_re.'__'.$data."', 'load_drop_down_emb_name', 'embel_name_td' );\n";
	}

	/*$smp_mst_id = sql_select("select id,company_id,production_source,sewing_company,location,floor_id from sample_sewing_output_mst where sample_development_id=$data and entry_form_id=338 and status_active=1 and is_deleted=0");
	if(count($smp_mst_id)>0)
 	{
		echo "load_drop_down('requires/sample_embellishment_issue_controller', '".$smp_mst_id[0][csf('production_source')].'**'.$smp_mst_id[0][csf('company_id')]."', 'load_drop_down_sewing_output', 'sew_company_td' );";
	
		//echo "$('#mst_update_id').val('".$smp_mst_id[0][csf('id')]."');\n";
		echo "$('#cbo_source').val('".$smp_mst_id[0][csf('production_source')]."');\n";
		echo "$('#cbo_embellishment_company').val('".$smp_mst_id[0][csf('sewing_company')]."');\n";
		echo "$('#cbo_location').val('".$smp_mst_id[0][csf('location')]."');\n";
		echo "load_drop_down( 'requires/sample_embellishment_issue_controller', '".$smp_mst_id[0][csf("location")]."', 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$smp_mst_id[0][csf('floor_id')]."');\n";
	}*/
  	exit();
}

if($action=="show_sample_item_listview")
{
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="90">Sample Name</th>
            <th width="90">Garments Item</th>
            <th width="80">Color</th>
            <th>Sample Qty</th>
        </thead>
		<?
		$i=1;

		//$sqlResult = sql_select("select b.id, b.gmts_item_id, b.sample_name, b.sample_color, sum(c.total_qty) as size_qty from sample_development_mst a, sample_development_dtls b, sample_development_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.sample_name,b.sample_color,b.gmts_item_id,b.id order by b.id asc");
		$sqlResult = sql_select("select b.id, b.gmts_item_id, b.sample_name, b.sample_color, sum(d.qnty) as size_qty from sample_development_mst a, sample_development_dtls b, sample_development_size c,sample_develop_embl_color_size d where a.id=b.sample_mst_id and d.mst_id=b.sample_mst_id and d.mst_id=a.id and c.id=d.sample_size_dtls_id and b.id=c.dtls_id  and d.size_id=c.size_id and d.color_id=b.sample_color and d.item_id=b.gmts_item_id  and a.id=$data and d.qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.sample_name,b.sample_color,b.gmts_item_id,b.id order by b.id asc");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_sample_item_data(<? echo $row[csf('id')];?>,<? echo $row[csf('sample_name')]; ?>,<? echo $row[csf('gmts_item_id')]; ?>,<? echo $row[csf('sample_color')]; ?>);">
				<td><? echo $i; ?></td>
				<td style="word-break:break-all"><? echo $sample_name_library[$row[csf('sample_name')]]; ?></td>
				<td style="word-break:break-all"><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
				<td style="word-break:break-all"><? echo $color_library[$row[csf('sample_color')]]; ?></td>
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
	list($sample_dtls_part_tbl_id,$smp_id,$req_id,$gmts,$embel_name,$type,$position_data,$status_id,$color_id,$embel_type)=explode('**',$data);
	
	// echo $sample_dtls_part_tbl_id.'Dd'.$color_id;die;
      $emb_data="select a.id,b.size_id from sample_development_fabric_acc a,sample_develop_embl_color_size b where a.sample_mst_id=b.mst_id and a.id=b.dtls_id and a.sample_name_re=$smp_id and a.gmts_item_id_re=$gmts and a.sample_mst_id=$req_id and b.color_id=$color_id and a.type_re=$embel_type  and a.name_re=$embel_name and b.qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.form_type=3 "; 
  $emb_dataArr=sql_select($emb_data);
  foreach($emb_dataArr as $row)
	{
		$SampReqSizeArr[$row[csf("size_id")]]=$row[csf("size_id")];
	}
	// $sizeCond="";
	 if(count($SampReqSizeArr)>0)
	 {
			$sizeCond="and c.size_id in(".implode(",",$SampReqSizeArr).")"; 
	 }
	 else 	$sizeCond="and c.size_id in(0)"; 
	$val_req_embel = return_field_value("id","sample_development_fabric_acc","sample_name_re=$smp_id and gmts_item_id_re=$gmts and sample_mst_id=$req_id and status_active=1 and is_deleted=0 and form_type=3  ");//and name_re not in(3,5)
	if($val_req_embel=="")
	{
		echo "alert('Emblishment is not available for this sample and item');\n";
		echo "$('#breakdown_td_id').html('');\n";
		die();
	}
	
 	//if($type=='single')
	if($status_id==1 || $status_id==0) //Issue Id 23725; //Sample Cutting 
	{
		//Inserted data;
		$colorResult_qc_pass = sql_select("SELECT b.sample_name, c.color_id, c.size_id, c.size_pass_qty, c.size_rej_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and  a.sample_development_id=$req_id and b.sample_name=$smp_id and b.item_number_id=$gmts and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=338 and b.entry_form_id=338 and c.entry_form_id=338 and b.embel_name=$embel_name and b.embel_type=$embel_type and c.color_id=$color_id and b.sample_status_id=$status_id order by c.id asc");
		
		$colorResult = sql_select("SELECT c.color_id, c.size_id, c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and  a.sample_development_id=$req_id and b.sample_name=$smp_id  and b.item_number_id=$gmts and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=127 and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and c.color_id=$color_id $sizeCond");
		//echo "SELECT c.color_id, c.size_id, c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=$smp_id  and b.item_number_id=$gmts and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=127 and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and c.color_id=$color_id";
		// and b.sample_dtls_row_id=$sample_dtls_part_tbl_id
		$total_cut=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=127 and sample_dtls_row_id=$sample_dtls_part_tbl_id","qc_pass_qty");

		$total_cuml=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=338 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$embel_name and embel_type=$embel_type and sample_status_id=$status_id");
		echo "$('#dynamic_cut_qty').html('Total Cutting Qty');\n";
	}
	//else if($type=='position_one')
	else if($status_id==2) //Sample Sewing Output
	{
		//Inserted data;
		$colorResult_qc_pass = sql_select(" select b.sample_name, c.color_id, c.size_id, c.size_pass_qty, c.size_rej_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and  a.sample_development_id=$req_id and b.sample_name=$smp_id and b.item_number_id=$gmts and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=338 and b.entry_form_id=338 and c.entry_form_id=338 and b.embel_name=$embel_name and b.embel_type=$embel_type and  c.color_id=$color_id  and b.sample_status_id=$status_id ");
		
//sample_sewing_output_dtls
		//$colorResult = sql_select("select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=$smp_id  and b.item_number_id=$gmts and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=338 and b.embel_name=$position_data and c.color_id is not null ");// and b.sample_dtls_row_id=$sample_dtls_part_tbl_id
		
		$colorResult = sql_select("select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and  a.sample_development_id=$req_id and b.sample_name=$smp_id  and b.item_number_id=$gmts and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=130  and  c.color_id=$color_id $sizeCond "); 
		

  		// $total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=338 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$embel_name");
		$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=130 and sample_dtls_row_id=$sample_dtls_part_tbl_id ");


  		$total_cuml=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=338 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$embel_name and embel_type=$embel_type and sample_status_id=$status_id","qc_pass_qty");
  		echo "$('#dynamic_cut_qty').html('Total $emblishment_name_array[$position_data] Qty');\n";
	}
	 

	foreach($colorResult_qc_pass as $row)
	{
		$qcPassQtyArr[$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];
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
			//echo $total_qty.'=A='.$qcPassQtyArr[$color_id][$size_id].',';
			$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'" class="text_boxes_numeric" style="width:80px" placeholder="'.($total_qty-$qcPassQtyArr[$color_id][$size_id]).'" onblur="fn_total('.$color_id.','.$i.')"></td></tr>';

			$i++;
		}
		$colorHTML .= "</table></div>";
	}
	echo "$('#txt_cumul_embel_qty').val('');\n";
	echo "$('#txt_total_cutting_qty').val('');\n";
	echo "$('#txt_yet_to_embel').val('');\n";
	echo "$('#txt_embellishment_qty').val('');\n";
	echo "$('#txt_remark').val('');\n";

	$value=return_field_value("gmts_item_id","sample_development_dtls","entry_form_id in (117,203,449) and sample_mst_id=$req_id and id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0");
	$qty=return_field_value("sum(total_qty)","sample_development_size","mst_id=$req_id and dtls_id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0");

	$name_re_val=return_field_value("name_re","sample_development_fabric_acc","sample_mst_id=$req_id and sample_name_re=$smp_id and gmts_item_id_re=$gmts and status_active=1 and is_deleted=0 and form_type=3");
	$type_re_val=return_field_value("type_re","sample_development_fabric_acc","sample_mst_id=$req_id and sample_name_re=$smp_id and gmts_item_id_re=$gmts and status_active=1 and is_deleted=0 and form_type=3");
	echo "$('#cbo_item_name').val(".$value.");\n";
	echo "$('#txt_sample_qty').val(".$qty.");\n";
	echo "$('#txt_cumul_embel_qty').val(".$total_cuml.");\n";
	echo "$('#txt_total_cutting_qty').val(".$total_cut.");\n";
	echo "var smpqty=$('#txt_sample_qty').val();\n";
	echo "var total_cuts=$('#txt_total_cutting_qty').val();\n";
	echo "var qcqty=$('#txt_cumul_embel_qty').val();\n";
	echo "$('#txt_yet_to_embel').val(total_cuts-qcqty);\n";
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
    ?>
    <fieldset style="overflow:hidden; margin:5px 0;">
    <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
            	<th width="30">&nbsp;</th>
                <th width="30">SL</th>
                <th width="110">Sample Name</th>
                <th width="100">Emb. Name</th>
                <th width="100">Status</th>
                <th width="70">Prod. Date</th>
                <th>QC Pass Qty</th>
            </thead>
		</table>
	</div>
	<div style="width:720; max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_list_search">
		<?php
			$i = 1;
			$sqlResult = sql_select("select a.id, a.sample_development_id, b.id as dtls_id,b.sample_status_id, b.sample_name, b.sample_dtls_row_id, b.embel_name, b.embel_type, b.item_number_id, b.sewing_date, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and a.sample_development_id=$data  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=338 and b.entry_form_id=338 order by b.id asc ");
			
			foreach ($sqlResult as $row)
			{
				$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
				$data_val = "'" . $row[csf('sample_development_id')] . '**' . $row[csf('id')] . '**' . $row[csf('dtls_id')] . '**' . $row[csf('sample_name')] . '**' . $row[csf('item_number_id')] . '**' . $row[csf('sample_dtls_row_id')] . '**' . $row[csf('embel_name')] . '**' . $row[csf('sample_status_id')].'**' . $row[csf('embel_type')]."'";
				//echo $data_val;
				$click_val = "get_php_form_data($data_val,'populate_input_form_data','requires/sample_embellishment_issue_controller');"
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer">
                    <td width="30" align="center"><input type="checkbox" id="check_for_print_<? echo $i;?>" name="check_for_print" onClick="<? echo $click_val;?>" onChange="release_print_buuton(this)" value="<? echo $row[csf('dtls_id')];?>"/> </td>
                    <td onClick="<? echo $click_val;?>" width="30" align="center"><? echo $i; ?></td>
                    <td onClick="<? echo $click_val;?>"  width="110"><p><? echo $sample_name_library[$row[csf('sample_name')]]; ?></p></td>
                   
                    <td onClick="<? echo $click_val;?>"  width="100"><p><? echo $emblishment_name_array[$row[csf('embel_name')]]; ?></p></td>
                      <td onClick="<? echo $click_val;?>"  width="100"><p><? echo $sample_statusArr[$row[csf('sample_status_id')]]; ?></p></td>
                    <td onClick="<? echo $click_val;?>"  width="70" align="center"><?php echo change_date_format($row[csf('sewing_date')]); ?></td>
                    <td onClick="<? echo $click_val;?>" align="right"><? echo $row[csf('qc_pass_qty')]; ?></td>
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
 	list($smp_id,$mst_id,$dtls_id,$sample_name,$gmts,$sample_dtls_row_id,$embel_name,$status_id,$embel_type)=explode('**',$data);
  	$emb_names=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$smp_id and form_type=3 and is_deleted=0 and status_active=1  and name_re<>3 and  name_re<>5  and sample_name_re=$sample_name and gmts_item_id_re=$gmts order by id asc");
	
	$smp_mst_id = sql_select("select id,company_id,production_source,sewing_company,location,floor_id from sample_sewing_output_mst where id=$mst_id and entry_form_id=338 and status_active=1 and is_deleted=0");
	if(count($smp_mst_id)>0)
 	{
		echo "load_drop_down('requires/sample_embellishment_issue_controller', '".$smp_mst_id[0][csf('production_source')].'**'.$smp_mst_id[0][csf('company_id')]."', 'load_drop_down_sewing_output', 'sew_company_td' );";
	
		echo "$('#mst_update_id').val('".$smp_mst_id[0][csf('id')]."');\n";
		echo "$('#txt_mst_issue_id').val('".$smp_mst_id[0][csf('id')]."');\n";
		echo "$('#cbo_source').val('".$smp_mst_id[0][csf('production_source')]."');\n";
		echo "$('#cbo_embellishment_company').val('".$smp_mst_id[0][csf('sewing_company')]."');\n";
		echo "$('#cbo_location').val('".$smp_mst_id[0][csf('location')]."');\n";
		echo "load_drop_down( 'requires/sample_embellishment_issue_controller', '".$smp_mst_id[0][csf("location")]."', 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$smp_mst_id[0][csf('floor_id')]."');\n";
	}

	//Inserted data........................and sample_status_id=$status_id
	$colorResult = sql_select("select a.id, a.sample_dtls_row_id, a.sample_name, a.embel_name,a.sample_status_id, a.embel_type, a.total_cut_qty, a.prod_qty, a.item_number_id, a.sewing_date, a.line_no, a.qc_pass_qty, a.remarks, b.color_id as sample_color, b.size_id, b.size_pass_qty as size_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b where  a.id=b.sample_sewing_output_dtls_id and a.sample_sewing_output_mst_id=$mst_id and b.sample_sewing_output_mst_id = $mst_id and a.sample_name=$sample_name and a.item_number_id=$gmts and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=338 and b.entry_form_id=338 and a.sample_dtls_row_id=$sample_dtls_row_id and a.embel_name=$embel_name and a.embel_type=$embel_type and sample_status_id=$status_id");

	foreach($colorResult as $row)
	{
		if($row[csf("sample_color")])
		{
			$colorTotal[$row[csf("id")]][$row[csf("sample_color")]]+=$row[csf("size_qty")];
			$colorData[$row[csf("id")]][$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];

			$sizeQcPassQty[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];
			$totSizeQcPassQty+=$row[csf("size_qty")];

			$dtlsArr[$row[csf("id")]]['sample_name']=$row[csf('sample_name')];
			$dtlsArr[$row[csf("id")]]['sample_status_id']=$row[csf('sample_status_id')];
			$dtlsArr[$row[csf("id")]]['item_number_id']=$row[csf('item_number_id')];
			$dtlsArr[$row[csf("id")]]['prod_qty']=$row[csf('prod_qty')];
 			$dtlsArr[$row[csf("id")]]['sewing_date']=$row[csf('sewing_date')];
 			$dtlsArr[$row[csf("id")]]['qc_pass_qty']=$row[csf('qc_pass_qty')];
 			$dtlsArr[$row[csf("id")]]['remarks']=$row[csf('remarks')];
 			$dtlsArr[$row[csf("id")]]['sample_dtls_part_tbl_id']=$row[csf('sample_dtls_row_id')];
 			$dtlsArr[$row[csf("id")]]['embel_name']=$row[csf('embel_name')];
 			$dtlsArr[$row[csf("id")]]['embel_type']=$row[csf('embel_type')];
 			$dtlsArr[$row[csf("id")]]['total_cut_qty']=$row[csf('total_cut_qty')];
		}
	}
	//print_r($sizeQcPassQty);
	echo "$('#txt_total_cutting_qty').val('');\n";
	echo "$('#dtls_update_id').val('".$dtls_id."');\n";
	echo "$('#cbo_sample_name').val('".$dtlsArr[$dtls_id]['sample_name']."');\n";
	echo "$('#cbo_status_id').val('".$dtlsArr[$dtls_id]['sample_status_id']."');\n";
	
	echo "$('#cbo_item_name').val('".$dtlsArr[$dtls_id]['item_number_id']."');\n";
	echo "$('#txt_sample_qty').val('".$dtlsArr[$dtls_id]['prod_qty']."');\n";
	echo "$('#txt_embellishment_date').val('".change_date_format($dtlsArr[$dtls_id]['sewing_date'])."');\n";
	echo "$('#txt_embellishment_qty').val('".$dtlsArr[$dtls_id]['qc_pass_qty']."');\n";
	echo "$('#txt_remark').val('".$dtlsArr[$dtls_id]['remarks']."');\n";
	echo "$('#cbo_embel_name').val('".$dtlsArr[$dtls_id]['embel_name']."');\n";
	echo "load_drop_down( 'requires/sample_embellishment_issue_controller', '".$dtlsArr[$dtls_id]['embel_name'].'**'.$mst_id."', 'load_drop_down_emb_receive_type', 'emb_type_td' );\n";
	echo "$('#cbo_embel_type').val('".$dtlsArr[$dtls_id]['embel_type']."');\n";

	$this_emb=$dtlsArr[$dtls_id]['embel_name'];
	$name_id=$emb_names[0][csf('name')];
	$name_arr=explode(',', $name_id);
	$req_position=array_search($this_emb, $name_arr);
	if($req_position=="") $req_position=0;
	//echo $req_position.'DDD';
	//if($req_position==0)
	if($status_id==0 || $status_id==1) //Sample Cutting
	{
		$sqlResult = sql_select("select c.color_id, c.size_id, c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c  where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']." and b.item_number_id=".$dtlsArr[$dtls_id]['item_number_id']." and a.entry_form_id=127 and b.entry_form_id=127 and c.entry_form_id=127 and b.sample_dtls_row_id=$sample_dtls_row_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
	//	echo "select sum(qc_pass_qty) as qc_pass_qty from sample_sewing_output_dtls where sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=127 and sample_dtls_row_id=$sample_dtls_row_id";
		$total_cuml=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=338 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$embel_name and sample_status_id=$status_id","qc_pass_qty");
		$total_cut=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=127 and sample_dtls_row_id=$sample_dtls_row_id","qc_pass_qty");
		//echo $total_cut.'SDDD';
		echo "$('#dynamic_cut_qty').html('Total Cutting Qty');\n";
	}
	else if($status_id==2) // Sample Sewing Out
	{
		$sqlResult = sql_select("select c.color_id, c.size_id, c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c  where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']." and b.item_number_id=".$dtlsArr[$dtls_id]['item_number_id']." and a.entry_form_id=130 and b.entry_form_id=130 and c.entry_form_id=130 and b.sample_dtls_row_id=$sample_dtls_row_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");//and b.embel_name=$name_arr[0]
		$total_cuml=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=338 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$embel_name and sample_status_id=$status_id");
		$val=$name_arr[0];
		$total_cut=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=130 and sample_dtls_row_id=$sample_dtls_row_id ","qc_pass_qty"); //and embel_name=$name_arr[0]
		echo "$('#dynamic_cut_qty').html('Total Sewing Output Qty');\n";
	}
	else
	{
		$sqlResult = sql_select("select c.color_id, c.size_id, c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c  where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']." and b.item_number_id=".$dtlsArr[$dtls_id]['item_number_id']." and a.entry_form_id=338 and b.entry_form_id=338 and c.entry_form_id=338 and b.sample_dtls_row_id=$sample_dtls_row_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=$name_arr[1] ");
		$total_cuml=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=338 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$embel_name");
		$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=338 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$name_arr[1]");
		$val=$name_arr[1];
		echo "$('#dynamic_cut_qty').html('Total $emblishment_name_array[$val] Qty');\n";
	}

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
			//echo $size_qty.",";

			$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:80px" value="'.$size_qty.'" placeholder="'.($smp_qty_arr[$color_id][$size_id]-($size_qty)).'" onblur="fn_total('.$color_id.','.$i.')"></td></tr>';
		$i++;
		}
		$colorHTML .= "</table></div>";
	}
	//list($smp_id,$mst_id,$dtls_id,$sample_name,$gmts,$sample_dtls_row_id)=explode('**',$data);
	echo "$('#txt_total_cutting_qty').val(".$total_cut.");\n";
	echo "$('#txt_cumul_embel_qty').val(".$total_cuml.");\n";
	echo "var total_cuts=$('#txt_total_cutting_qty').val();\n";
	echo "var qcqty=$('#txt_cumul_embel_qty').val();\n";
	echo "$('#txt_yet_to_embel').val(total_cuts-qcqty);\n";

	echo "set_button_status(1, permission, 'fnc_sample_embellishment_entry',1,0);\n";
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	echo "$('#hidden_sample_dtls_tbl_id').val('".$dtlsArr[$dtls_id]['sample_dtls_part_tbl_id']."');\n";

 	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0) // Insert part----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		$flag=1;
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		$requisition_id=str_replace("'","",$hidden_requisition_id);
		$issue_qty=str_replace("'","",$txt_embellishment_qty);
		$status_id=str_replace("'","",$cbo_status_id);
		$embel_name=str_replace("'","",$cbo_embel_name);
		$embel_type=str_replace("'","",$cbo_embel_type);
		//338
		if($status_id==1) //Cutting
		{
			$sql_cutting="select b.entry_form_id,b.qc_pass_qty,b.sample_name, b.sample_dtls_row_id, b.item_number_id from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in(127) and a.entry_form_id in(127) and a.sample_development_id in($requisition_id) and b.sample_name=$cbo_sample_name and b.item_number_id=$cbo_item_name  and a.status_active=1 and b.status_active=1";
			$sql_cutting_res=sql_select($sql_cutting);
			$previ_issue_qty=$cutting_issue_qty=0;
			foreach($sql_cutting_res as $row)
			{
				 //Cutting
				 $cutting_issue_qty+=$row[csf('qc_pass_qty')];
				
			}
			$msg="Cutting";
		}
		else
		{
			$sql_sewing_out="select b.entry_form_id,b.qc_pass_qty,b.sample_name, b.sample_dtls_row_id, b.item_number_id from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in(130) and a.entry_form_id in(130) and a.sample_development_id in($requisition_id) and b.sample_name=$cbo_sample_name and b.item_number_id=$cbo_item_name  and a.status_active=1 and b.status_active=1";
			$sql_sewing_res=sql_select($sql_sewing_out);
			$previ_issue_qty=$cutting_issue_qty=0;
			foreach($sql_sewing_res as $row)
			{
				 //Sewing
				 $cutting_issue_qty+=$row[csf('qc_pass_qty')];
				
			}
			$msg="Sewing";
		}
		$sql_prev="select b.entry_form_id,b.qc_pass_qty,b.sample_name, b.sample_dtls_row_id, b.item_number_id from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in(338) and a.entry_form_id in(338) and a.sample_development_id in($requisition_id) and b.sample_name=$cbo_sample_name and b.item_number_id=$cbo_item_name  and b.embel_name=$cbo_embel_name and b.embel_type=$embel_type  and b.sample_status_id=$status_id and a.status_active=1 and b.status_active=1";
		$sql_prev_res=sql_select($sql_prev);
		
		foreach($sql_prev_res as $row)
		{
			if($row[csf('entry_form_id')]==338)//Issue
			{
			 $previ_issue_qty+=$row[csf('qc_pass_qty')];
			}
			
		}
		
		$tot_issue=$issue_qty+$previ_issue_qty;
		if($tot_issue>$cutting_issue_qty)
		{
			$msg="embellishment issue($previ_issue_qty) entry should not allow to save more than $msg quantity($cutting_issue_qty)";
			echo "13**".$msg.'**'.$issue_qty.'**'.$previ_issue_qty.'**'.$cutting_issue_qty;
			disconnect($con);
			die;
		}

		if($mst_update_id=='')
		{
			//master part--------------------------------------------------------------;
			$mst_id=return_next_id("id", "sample_sewing_output_mst", 1);
			$field_array_mst="id, company_id, sample_development_id, production_source, sewing_company, location, floor_id, inserted_by, insert_date, status_active, is_deleted, entry_form_id";
			$data_array_mst="(".$mst_id.",".$cbo_company_name.",".$hidden_requisition_id.",".$cbo_source.",".$cbo_embellishment_company.",".$cbo_location.",".$cbo_floor.",".$user_id.",'".$pc_date_time."','1','0',338)";
		}
		else
		{
			$field_array_mst="company_id*production_source*sewing_company*location*floor_id*updated_by*update_date";
			$data_array_mst="".$cbo_company_name."*".$cbo_source."*".$cbo_embellishment_company."*".$cbo_location."*".$cbo_floor."*".$user_id."*'".$pc_date_time."'";
			$mst_id=$mst_update_id;
		}

		//Details part--------------------------------------------------------------;
		$dtls_id=return_next_id("id", "sample_sewing_output_dtls", 1);
		$field_array_dtls="id, sample_sewing_output_mst_id, sample_name, sample_dtls_row_id, item_number_id, sewing_date, qc_pass_qty, remarks, inserted_by, insert_date, status_active, is_deleted, entry_form_id, prod_qty, embel_name, embel_type,sample_status_id, total_cut_qty";
		$data_array_dtls="(".$dtls_id.",".$mst_id.",".$cbo_sample_name.",".$hidden_sample_dtls_tbl_id.",".$cbo_item_name.",".$txt_embellishment_date.",".$txt_embellishment_qty.",".$txt_remark.",".$user_id.",'".$pc_date_time."','1','0',338,".$txt_sample_qty.",".$cbo_embel_name.",".$cbo_embel_type.",".$cbo_status_id.",".$txt_total_cutting_qty.")";

		// Color & Size Breakdown part--------------------------------------------------------------;
		$field_array_brk="id, sample_sewing_output_mst_id, sample_sewing_output_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted, entry_form_id";
		$colorsize_brk_id=return_next_id("id", "sample_sewing_output_colorsize", 1);
		
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

			if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0',338)";
			else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0',338)";
			$colorsize_brk_id+=1;
			$j++;
		}

		//insert here----------------------------------------;
		if($mst_update_id=='')
		{
			$rID_mst=sql_insert("sample_sewing_output_mst",$field_array_mst,$data_array_mst,0);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID_mst=sql_update("sample_sewing_output_mst",$field_array_mst,$data_array_mst,"id","".$mst_update_id."",1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		$rID_dtls=sql_insert("sample_sewing_output_dtls",$field_array_dtls,$data_array_dtls,0);
		if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;

		$rID_brk=sql_insert("sample_sewing_output_colorsize",$field_array_brk,$data_array_brk,0);
		if($rID_brk==1 && $flag==1) $flag=1; else $flag=0;

		//ROLLBACK/COMMIT here----------------------------------------;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$mst_id."**".$hidden_requisition_id."**0**".$dtls_id."**".$hidden_sample_dtls_tbl_id;
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
				echo "0**".$mst_id."**".$hidden_requisition_id."**0**".$dtls_id."**".$hidden_sample_dtls_tbl_id;;
			}
			else
			{
				oci_rollback($con);
				echo "10**0**"."&nbsp;"."**0";
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
		$requisition_id=str_replace("'","",$hidden_requisition_id);
		$issue_qty=str_replace("'","",$txt_embellishment_qty);
		$status_id=str_replace("'","",$cbo_status_id);
		$embel_name=str_replace("'","",$cbo_embel_name);
		$embel_type=str_replace("'","",$cbo_embel_type);
		//338
		if($status_id==1) //Cuttting
		{
			$sql_cutting="select b.entry_form_id,b.qc_pass_qty,b.sample_name, b.sample_dtls_row_id, b.item_number_id from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in(127) and a.entry_form_id in(127) and a.sample_development_id in($requisition_id) and b.sample_name=$cbo_sample_name and b.item_number_id=$cbo_item_name   and a.status_active=1 and b.status_active=1";
			$sql_cutting_res=sql_select($sql_cutting);
			$previ_issue_qty=$cutting_issue_qty=0;
			foreach($sql_cutting_res as $row)
			{
				if($row[csf('entry_form_id')]==127)//Cutting
				{
				 $cutting_issue_qty+=$row[csf('qc_pass_qty')];
				}
				
			}
			$msg="Cutting";
		}
		else
		{
			$sql_sewingout="select b.entry_form_id,b.qc_pass_qty,b.sample_name, b.sample_dtls_row_id, b.item_number_id from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in(130) and a.entry_form_id in(130) and a.sample_development_id in($requisition_id) and b.sample_name=$cbo_sample_name and b.item_number_id=$cbo_item_name   and a.status_active=1 and b.status_active=1";
			$sql_sewout_res=sql_select($sql_sewingout);
			$previ_issue_qty=$cutting_issue_qty=0;
			foreach($sql_sewout_res as $row)
			{
				if($row[csf('entry_form_id')]==130)//Sewing
				{
				 $cutting_issue_qty+=$row[csf('qc_pass_qty')];
				}
				
			}
			$msg="Sewing";
		}
		$sql_prev="select b.entry_form_id,b.qc_pass_qty,b.sample_name, b.sample_dtls_row_id, b.item_number_id from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in(338) and a.entry_form_id in(338) and a.sample_development_id in($requisition_id) and b.sample_name=$cbo_sample_name and b.item_number_id=$cbo_item_name and b.embel_name=$cbo_embel_name and b.embel_type=$embel_type  and b.id<>$dtls_update_id and a.status_active=1 and b.status_active=1";
		$sql_prev_res=sql_select($sql_prev);
		 
		foreach($sql_prev_res as $row)
		{
			if($row[csf('entry_form_id')]==338)//Issue
			{
			 $previ_issue_qty+=$row[csf('qc_pass_qty')];
			}
			 
		}
		
		$tot_issue=$issue_qty+$previ_issue_qty;
		if($tot_issue>$cutting_issue_qty)
		{
			$msg="embellishment issue($previ_issue_qty) entry should not allow to save more than $msg quantity($cutting_issue_qty)";
			echo "13**".$msg.'**'.$issue_qty.'**'.$tot_issue.'**'.$cutting_issue_qty;
			disconnect($con);
			die;
		}
		

		if($mst_update_id!='')
		{
			//master part--------------------------------------------------------------;
			$field_array_mst="company_id*production_source*sewing_company* location*floor_id*updated_by*update_date";
			$data_array_mst="".$cbo_company_name."*".$cbo_source."*".$cbo_embellishment_company."*".$cbo_location."*".$cbo_floor."*".$user_id."*'".$pc_date_time."'";

			//Dtls part--------------------------------------------------------------;
			$field_array_dtls="sewing_date*embel_name*embel_type*qc_pass_qty*sample_status_id*remarks*updated_by*update_date";
			$data_array_dtls="".$txt_embellishment_date."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_embellishment_qty."*".$cbo_status_id."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";

			// Color & Size Breakdown part--------------------------------------------------------------;
			$field_array_brk="id, sample_sewing_output_mst_id, sample_sewing_output_dtls_id, color_id, size_id, size_pass_qty, inserted_by, insert_date, status_active, is_deleted, entry_form_id";
			$colorsize_brk_id=return_next_id("id", "sample_sewing_output_colorsize", 1);
			
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

				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0',338)";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."',".$user_id.",'".$pc_date_time."','1','0',338)";
				$colorsize_brk_id+=1;
				$j++;
			}
			$flag=1;
			
			$rID_mst=sql_update("sample_sewing_output_mst",$field_array_mst,$data_array_mst,"id","".$mst_update_id."",1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
			$rID_dtls=sql_update("sample_sewing_output_dtls",$field_array_dtls,$data_array_dtls,"id","".$dtls_update_id."",1);
			if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
			$rID_brk_delete = execute_query("DELETE from sample_sewing_output_colorsize WHERE sample_sewing_output_dtls_id=$dtls_update_id");
			if($rID_brk_delete==1 && $flag==1) $flag=1; else $flag=0;

			$rID_brk=sql_insert("sample_sewing_output_colorsize",$field_array_brk,$data_array_brk,0);
			if($rID_brk==1 && $flag==1) $flag=1; else $flag=0;

			//echo $rID_mst .'&&'. $rID_dtls .'&&'. $rID_brk_delete .'&&'. $rID_brk; die;

			//-------------------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "1**".$mst_update_id."**".$hidden_requisition_id."**0**".$dtls_update_id."**".$hidden_sample_dtls_tbl_id;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$mst_update_id."**".$hidden_requisition_id."**0";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);
					echo "1**".$mst_update_id."**".$hidden_requisition_id."**0**".$dtls_update_id."**".$hidden_sample_dtls_tbl_id;;
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
	else if ($operation==2)  //Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		
		$sql_dtls=sql_select("select id from sample_sewing_output_dtls where sample_sewing_output_mst_id in($mst_update_id) and status_active=1 and is_deleted=0"); 
		$tot_row=count($sql_dtls);
		$flag=1;
		if($tot_row==1)
		{
			 $rID=execute_query( "update sample_sewing_output_mst set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  id in (".$mst_update_id.")",0);
				 if($rID) $flag=1;else  $flag=0;
		}
		
		$rID2=execute_query( "update sample_sewing_output_dtls set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  id in (".$dtls_update_id.")",0);
		 if($flag==1)
		 {
			 if($rID2) $flag=1;else  $flag=0;
		 }
		
		$rID3=execute_query( "update sample_sewing_output_colorsize set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where  sample_sewing_output_dtls_id in (".$dtls_update_id.")",0);
		if($flag==1)
		 {
			 if($rID3) $flag=1;else  $flag=0;
		 }
		 

 		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "2**".$mst_update_id."**".str_replace("'","",$hidden_requisition_id)."**0**".$dtls_update_id."**".$hidden_sample_dtls_tbl_id.'**'.$tot_row;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$mst_update_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".$mst_update_id."**".str_replace("'","",$hidden_requisition_id)."**0**".$dtls_update_id."**".$hidden_sample_dtls_tbl_id.'**'.$tot_row;
			}
			else //
			{
				oci_rollback($con);
				echo "10**".$mst_update_id;
			}
		}
		disconnect($con);
		die;
	}
	/* else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$mst_update_id=str_replace("'","",$mst_update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		$flag=1;
		
		$rIDmst = sql_delete("sample_sewing_output_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id  ',$mst_update_id,1);
		if($rIDmst==1 && $flag==1) $flag=1; else $flag=0;
 		$rIDdtls = sql_delete("sample_sewing_output_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'sample_sewing_output_mst_id  ',$mst_update_id,1);
		if($rIDdtls==1 && $flag==1) $flag=1; else $flag=0;
		$dtlsrID = sql_delete("sample_sewing_output_colorsize","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'sample_sewing_output_mst_id',$mst_update_id,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;

 		if($db_type==0)
		{
			if($flag==1)
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
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
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
	} */
}


if($action=="embellishment_print")
{
	extract($_REQUEST);
	//print_r($_REQUEST);
	list($company_id,$mst_id,$sample_id,$gmts,$dtls_id,$check_for_print,$req_id,$sample_dtls_part_id)=explode('*',$data);
	//echo $req_id.'system';
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name"  );
	$garments_items=return_library_array( "select id, item_name from  lib_garment_item", "id", "item_name"  );

	$sql_mst="SELECT a.id, a.production_source, a.sewing_company from sample_sewing_output_mst a where a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=338";
	$sql_mst_res=sql_select($sql_mst);
	if ($sql_mst_res[0][csf('production_source')] == 1)
	{
		$embel_com = $company_library[$sql_mst_res[0][csf('sewing_company')]];
	}
	else if ($sql_mst_res[0][csf('production_source')] == 3)
	{
		$embel_com = $party_library[$sql_mst_res[0][csf('sewing_company')]];
	}

	$res = sql_select("select buyer_name,style_ref_no,requisition_number_prefix_num from sample_development_mst where id=$req_id  and status_active=1 and is_deleted=0 and entry_form_id in (117,203,449)");

  	foreach($res as $rows)
	{
		$dtls_data['buyer_name']=$rows[csf('buyer_name')];
		$dtls_data['style_ref_no']=$rows[csf('style_ref_no')];
		$dtls_data['req_no']=$rows[csf('requisition_number_prefix_num')];
	}

	if($db_type==2){$reporting_hour_fill=" TO_CHAR( a.reporting_hour,'HH24:MI' ) as reporting_hour ";}
			else{$reporting_hour_fill=" TIME_FORMAT( a.reporting_hour, '%H:%i' ) as reporting_hour ";}

	$sql="
	SELECT a.sample_name, a.item_number_id, a.sewing_date,$reporting_hour_fill,a.qc_pass_qty,a.remarks ,
		b.color_id,b.size_id,b.size_pass_qty,b.size_rej_qty,a.embel_name,a.embel_type
	from
		sample_sewing_output_dtls a,sample_sewing_output_colorsize b
	where
		a.sample_sewing_output_mst_id=$mst_id and a.id=b.sample_sewing_output_dtls_id and a.id IN($dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=338";
	//echo $sql;	

	$sql_result=sql_select($sql);
	$gmts_item_arr=array();
	$emb_date_arr=array();
	$sample_name_arr=array();
	$reporting_hour_arr=array();
	$remarks_arr=array();

	foreach ($sql_result as $rows)
	{
		$dtls_data['embel_name']=$rows[csf('embel_name')];
		$dtls_data['embel_type']=$rows[csf('embel_type')];
		$dtls_data['sewing_date']=$rows[csf('sewing_date')];

		if(! in_array($rows[csf('item_number_id')], $gmts_item_arr))
		$gmts_item_arr[$rows[csf('item_number_id')] ] = $garments_items[$rows[csf('item_number_id')]];

		if(! in_array($rows[csf('sample_name')], $sample_name_arr))
		$sample_name_arr[$rows[csf('sample_name')] ] = $sample_name_library[$rows[csf('sample_name')]];

		if(! in_array($rows[csf('reporting_hour')], $reporting_hour_arr))
		$reporting_hour_arr[$rows[csf('color_id')]] = $rows[csf('reporting_hour')];

		if(! in_array($rows[csf('sewing_date')], $emb_date_arr))
		$emb_date_arr[$dtls_data['sewing_date']] = change_date_format($rows[csf('sewing_date')]);

		if(! in_array($rows[csf('remarks')], $remarks_arr))
		$remarks_arr[$rows[csf('remarks')]] = $rows[csf('remarks')];

		$dtls_data['qc_pass_qty']+=$rows[csf('qc_pass_qty')];
		$dtls_data[$rows[csf('color_id')]]=$rows[csf('remarks')];
		$dtls_data['gmts']=$rows[csf('item_number_id')];

		$size_arr[$rows[csf('size_id')]]=$rows[csf('size_id')];

		$tot_color_good_qty[$rows[csf('color_id')]]+=$rows[csf('size_pass_qty')];
		$tot_color_rej_qty[$rows[csf('color_id')]]+=$rows[csf('size_rej_qty')];

		$tot_size_good_qty[$rows[csf('size_id')]]+=$rows[csf('size_pass_qty')];
		$tot_size_rej_qty[$rows[csf('size_id')]]+=$rows[csf('size_rej_qty')];

		$good_qty[$rows[csf('color_id')]][$rows[csf('size_id')]]+=$rows[csf('size_pass_qty')];
		$rej_qty[$rows[csf('color_id')]][$rows[csf('size_id')]]+=$rows[csf('size_rej_qty')];

		$is_reject+=$rows[csf('size_rej_qty')];
	}


	$sql2="SELECT
		b.color_id, sum(b.size_pass_qty) as size_pass_qty,sum(b.size_rej_qty) as size_rej_qty,sum(a.qc_pass_qty) as qc_pass_qty 	from sample_sewing_output_dtls a,sample_sewing_output_colorsize b
		where 	a.sample_sewing_output_mst_id=$mst_id  and a.id=b.sample_sewing_output_dtls_id and a.id in($dtls_id)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by  b.color_id";
	$sql_result=sql_select($sql2);


	$tot_size=count($size_arr);
	$width=round((100/$tot_size)+25);
	$width_2=($width*$tot_size)+650;
	$emb_name=$dtls_data['embel_name'];
	//echo $dtls_data['embel_name'].'='.$dtls_data['embel_type'];
	if($emb_name==1)
	{
		$embel_type=$emblishment_print_type[$dtls_data['embel_type']];
	}
	else if($emb_name==2)
	{
		$embel_type=$emblishment_embroy_type[$dtls_data['embel_type']];
		//echo $embel_type.'fdf';
	}
	else if($emb_name==3)
	{
		$embel_type=$emblishment_wash_type[$dtls_data['embel_type']];
	}

	else if($emb_name==4)
	{
		$embel_type=$emblishment_spwork_type[$dtls_data['embel_type']];
	}
	else if($emb_name==5)
	{
		$embel_type=$emblishment_gmts_type[$dtls_data['embel_type']];
	}
	//else $embel_type='';



    ?>
    <div style="width:<? echo $width_2;?>px;">
    <table width="100%" cellspacing="0">
        <tr>
            <td colspan="3" align="center" style="font-size:22px"><strong><? echo $company_library[$company_id]; ?></strong></td>
        </tr>
        <tr>
        	<td colspan="3" align="center" style="font-size:12px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
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
            <td colspan="3" align="center"><strong>Sample <? if($dtls_data['embel_name']==1){echo "Printing";} elseif($dtls_data['embel_name']==2){echo "Embroidery";} else {echo "Special Works";} ?> </strong></td>
        </tr>

        <tr>
            <td><strong>Buyer : </strong><? echo $buyer_library[$dtls_data['buyer_name']]; ?></td>
            <td><strong>Style Ref. : </strong><? echo $dtls_data['style_ref_no']; ?></td>
            <td><strong>QC Pass Qty : </strong><? echo $dtls_data['qc_pass_qty']; ?> Pcs</td>
        </tr>
        <tr>
            <td><strong>Sample Requisition No : </strong><? echo $dtls_data['req_no']; ?></td>
            <td><strong>Item : </strong><? echo implode(",", $gmts_item_arr); ?></td>
            <td><strong><? if($dtls_data['embel_name']==1){echo "Printing";} elseif($dtls_data['embel_name']==2){echo "Embroidery";} else {echo "Special Works";} ?> Date : </strong><? echo implode(",", $emb_date_arr);  ?></td>
        </tr>
        <tr>
            <td><strong>Sample Name : </strong><? echo implode(",",$sample_name_arr); ?></td>
            <td><strong>Reporting Hour  : </strong><? echo implode(",", $reporting_hour_arr);  ?></td>
            <td><strong>Embel.Name : </strong><? echo $emblishment_name_array[$dtls_data['embel_name']]; ?></td>

        </tr>
         <tr>

            <td><strong>Embel. Type  : </strong><? echo $embel_type; ?></td>
            <td><strong>Remarks: </strong><? echo implode(",", $remarks_arr);  ?></td>
            <td><strong>Embel. Company : </strong><? echo $embel_com; ?></td>
        </tr>

    </table>
    <br>


    <!-- ......................Good Qty Part...................................... -->
    <div><strong> Good Qty.</strong></div>
    <table border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th rowspan="2" width="30">SL</th>
                <th rowspan="2">Color</th>
                <th colspan="<? echo $tot_size;?>">Size</th>
                <th rowspan="2" width="80">QC Pass Qty(Pcs)</th>
                
                <th rowspan="2" width="80">Reporting Hour</th>
                <th rowspan="2" width="80">Remarks</th>
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
            $i=1;
            foreach($sql_result as $key=>$vals)
            {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                $color_id=$vals[csf("color_id")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $color_library[$color_id]; ?></td>
                        <?
                        foreach ($size_arr as $size_id)
                        {
                            ?>
                            <td align="right"><? echo $good_qty[$color_id][$size_id]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? echo $tot_color_good_qty[$color_id]; ?></td>
                        
                        <td><? echo $reporting_hour_arr[$color_id]; ?></td>
            			<td><? echo $dtls_data[$color_id]; ?></td>
                    </tr>
                    <?
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				 foreach ($size_arr as $size_id)
				{
					?>
                    <td align="right"><?php echo $tot_size_good_qty[$size_id]; ?></td>
                    <?
				}
			?>
            <td colspan="">&nbsp;</td>
        </tr>
    </table>
   

	<? echo signature_table(222, $company_id, "750px"); ?>
    </div>
    </div>
    <?
    exit();
}

if($action=="embellishment_print2")
{
	extract($_REQUEST);
	//print_r($_REQUEST);
	list($company_id,$mst_id,$sample_id,$gmts,$dtls_id,$check_for_print,$req_id,$sample_dtls_part_id,$embName)=explode('*',$data);
	//echo $req_id.'system';
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name"  );
	$garments_items=return_library_array( "select id, item_name from  lib_garment_item", "id", "item_name"  );

	$sql_mst="SELECT a.id, a.production_source, a.sewing_company from sample_sewing_output_mst a where a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=338";
	$sql_mst_res=sql_select($sql_mst);
	if ($sql_mst_res[0][csf('production_source')] == 1)
	{
		$embel_com = $company_library[$sql_mst_res[0][csf('sewing_company')]];
	}
	else if ($sql_mst_res[0][csf('production_source')] == 3)
	{
		$embel_com = $party_library[$sql_mst_res[0][csf('sewing_company')]];
	}

	$res = sql_select("select buyer_name,style_ref_no,requisition_number_prefix_num from sample_development_mst where id=$req_id  and status_active=1 and is_deleted=0 and entry_form_id in (117,203,449)");

  	foreach($res as $rows)
	{
		$dtls_data['buyer_name']=$rows[csf('buyer_name')];
		$dtls_data['style_ref_no']=$rows[csf('style_ref_no')];
		$dtls_data['req_no']=$rows[csf('requisition_number_prefix_num')];
	}

	if($db_type==2){$reporting_hour_fill=" TO_CHAR( a.reporting_hour,'HH24:MI' ) as reporting_hour ";}
			else{$reporting_hour_fill=" TIME_FORMAT( a.reporting_hour, '%H:%i' ) as reporting_hour ";}

	$sql="
	SELECT a.sample_name, a.item_number_id, a.sewing_date,$reporting_hour_fill,a.qc_pass_qty,a.remarks ,
		b.color_id,b.size_id,b.size_pass_qty,b.size_rej_qty,a.embel_name,a.embel_type
	from
		sample_sewing_output_dtls a,sample_sewing_output_colorsize b
	where
		a.sample_sewing_output_mst_id=$mst_id and a.id=b.sample_sewing_output_dtls_id and a.id IN($dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=338";
	//echo $sql;	

	$sql_result=sql_select($sql);
	$gmts_item_arr=array();
	$emb_date_arr=array();
	$sample_name_arr=array();
	$reporting_hour_arr=array();
	$remarks_arr=array();

	foreach ($sql_result as $rows)
	{
		$dtls_data['embel_name']=$rows[csf('embel_name')];
		$dtls_data['embel_type']=$rows[csf('embel_type')];
		$dtls_data['sewing_date']=$rows[csf('sewing_date')];

		if(! in_array($rows[csf('item_number_id')], $gmts_item_arr))
		$gmts_item_arr[$rows[csf('item_number_id')] ] = $garments_items[$rows[csf('item_number_id')]];

		if(! in_array($rows[csf('sample_name')], $sample_name_arr))
		$sample_name_arr[$rows[csf('sample_name')] ] = $sample_name_library[$rows[csf('sample_name')]];

		if(! in_array($rows[csf('reporting_hour')], $reporting_hour_arr))
		$reporting_hour_arr[$rows[csf('color_id')]] = $rows[csf('reporting_hour')];

		if(! in_array($rows[csf('sewing_date')], $emb_date_arr))
		$emb_date_arr[$dtls_data['sewing_date']] = change_date_format($rows[csf('sewing_date')]);

		if(! in_array($rows[csf('remarks')], $remarks_arr))
		$remarks_arr[$rows[csf('remarks')]] = $rows[csf('remarks')];

		$dtls_data['qc_pass_qty']+=$rows[csf('qc_pass_qty')];
		$dtls_data[$rows[csf('color_id')]]=$rows[csf('remarks')];
		$dtls_data['gmts']=$rows[csf('item_number_id')];

		$size_arr[$rows[csf('size_id')]]=$rows[csf('size_id')];

		$tot_color_good_qty[$rows[csf('color_id')]]+=$rows[csf('size_pass_qty')];
		$tot_color_rej_qty[$rows[csf('color_id')]]+=$rows[csf('size_rej_qty')];

		$tot_size_good_qty[$rows[csf('size_id')]]+=$rows[csf('size_pass_qty')];
		$tot_size_rej_qty[$rows[csf('size_id')]]+=$rows[csf('size_rej_qty')];

		$good_qty[$rows[csf('color_id')]][$rows[csf('size_id')]]+=$rows[csf('size_pass_qty')];
		$rej_qty[$rows[csf('color_id')]][$rows[csf('size_id')]]+=$rows[csf('size_rej_qty')];

		$is_reject+=$rows[csf('size_rej_qty')];
	}


	$sql2="SELECT
		a.sample_status_id, b.color_id, sum(b.size_pass_qty) as size_pass_qty,sum(b.size_rej_qty) as size_rej_qty,sum(a.qc_pass_qty) as qc_pass_qty from sample_sewing_output_dtls a,sample_sewing_output_colorsize b
		where 	a.sample_sewing_output_mst_id=$mst_id  and a.id=b.sample_sewing_output_dtls_id and a.id in($dtls_id)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.sample_status_id, b.color_id";
	$sql_result=sql_select($sql2);


	$tot_size=count($size_arr);
	$width=round((100/$tot_size)+25);
	$width_2=($width*$tot_size)+650;
	$emb_name=$dtls_data['embel_name'];
	//echo $dtls_data['embel_name'].'='.$dtls_data['embel_type'];
	if($emb_name==1)
	{
		$embel_type=$emblishment_print_type[$dtls_data['embel_type']];
	}
	else if($emb_name==2)
	{
		$embel_type=$emblishment_embroy_type[$dtls_data['embel_type']];
		//echo $embel_type.'fdf';
	}
	else if($emb_name==3)
	{
		$embel_type=$emblishment_wash_type[$dtls_data['embel_type']];
	}

	else if($emb_name==4)
	{
		$embel_type=$emblishment_spwork_type[$dtls_data['embel_type']];
	}
	else if($emb_name==5)
	{
		$embel_type=$emblishment_gmts_type[$dtls_data['embel_type']];
	}
	//else $embel_type='';



    ?>
    <div style="width:<? echo $width_2;?>px;">
    <table width="100%" cellspacing="0">
        <tr>
            <td colspan="3" align="center" style="font-size:22px"><strong><? echo $company_library[$company_id]; ?></strong></td>
        </tr>
        <tr>
        	<td colspan="3" align="center" style="font-size:12px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
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
		<td colspan="3" align="center"><strong>Sample <? echo $emblishment_name_array[$embName];?>  Issue Challan</strong></td>
        <tr><td><strong>Issue ID : </strong><? echo $mst_id; ?></td></tr>
        <tr>
            <td><strong>Buyer : </strong><? echo $buyer_library[$dtls_data['buyer_name']]; ?></td>
            <td><strong>Style Ref. : </strong><? echo $dtls_data['style_ref_no']; ?></td>
            <td><strong>QC Pass Qty : </strong><? echo $dtls_data['qc_pass_qty']; ?> Pcs</td>
        </tr>
        <tr>
            <td><strong>Sample Requisition No : </strong><? echo $dtls_data['req_no']; ?></td>
            <td><strong>Item : </strong><? echo implode(",", $gmts_item_arr); ?></td>
            <td><strong><? if($dtls_data['embel_name']==1){echo "PrinIssue";} elseif($dtls_data['embel_name']==2){echo "Embroidery";} else {echo "Special Works";} ?> Date : </strong><? echo implode(",", $emb_date_arr);  ?></td>
        </tr>
        <tr>
            <td><strong>Sample Name : </strong><? echo implode(",",$sample_name_arr); ?></td>
            <!-- <td><strong>Reporting Hour  : </strong><? echo implode(",", $reporting_hour_arr);  ?></td> -->
            <td><strong>Embel.Name : </strong><? echo $emblishment_name_array[$dtls_data['embel_name']]; ?></td>

        </tr>
         <tr>

            <td><strong>Embel. Type  : </strong><? echo $embel_type; ?></td>
            <td><strong>Remarks: </strong><? echo implode(",", $remarks_arr);  ?></td>
            <td><strong>Embel. Company : </strong><? echo $embel_com; ?></td>
        </tr>

    </table>
    <br>


    <!-- ......................Good Qty Part...................................... -->
    <div><strong> Good Qty.</strong></div>
    <table border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th rowspan="2" width="30">SL</th>
				<th rowspan="2">Status</th>
                <th rowspan="2">Color</th>
                <th colspan="<? echo $tot_size;?>">Size</th>
                <th rowspan="2" width="80">QC Pass Qty(Pcs)</th>
                
                <!-- <th rowspan="2" width="80">Reporting Hour</th> -->
                <th rowspan="2" width="80">Remarks</th>
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
            $i=1;
            foreach($sql_result as $key=>$vals)
            {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                $color_id=$vals[csf("color_id")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
						<td><? echo $sample_statusArr[$vals[csf('sample_status_id')]]; ?></td>
                        <td><? echo $color_library[$color_id]; ?></td>
                        <?
                        foreach ($size_arr as $size_id)
                        {
                            ?>
                            <td align="right"><? echo $good_qty[$color_id][$size_id]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? echo $tot_color_good_qty[$color_id]; ?></td>
                        
                        <!-- <td><? echo $reporting_hour_arr[$color_id]; ?></td> -->
            			<td><? echo $dtls_data[$color_id]; ?></td>
                    </tr>
                    <?
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
            <?
				 foreach ($size_arr as $size_id)
				{
					?>
                    <td align="right"><?php echo $tot_size_good_qty[$size_id]; ?></td>
                    <?
				}
			?>
            <td colspan="">&nbsp;</td>
        </tr>
    </table>
   

	<? echo signature_table(222, $company_id, "750px"); ?>
    </div>
    </div>
    <?
    exit();
}

if($action=="embellishment_print3")
{
	extract($_REQUEST);
	list($company_id,$mst_id,$sample_id,$gmts,$dtls_id,$check_for_print,$req_id,$sample_dtls_part_id,$embName)=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$department_arr=return_library_array( "select id, department_name from lib_department", "id", "department_name"  );
	$location_lib=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$buyer_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name"  );
	$garments_items=return_library_array( "select id, item_name from  lib_garment_item", "id", "item_name"  );
	$location_name_arr=return_library_array( "select id,location_name from lib_location where status_active=1 and is_deleted=0",'id','location_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id'","image_location");


	$sql_mst="SELECT a.id, a.production_source, a.sewing_company from sample_sewing_output_mst a where a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=338";
	$sql_mst_res=sql_select($sql_mst);
	if ($sql_mst_res[0][csf('production_source')] == 1)
	{
		$embel_com = $company_library[$sql_mst_res[0][csf('sewing_company')]];
		$embel_location = $location_name_arr[$sql_mst_res[0][csf('sewing_company')]];
	}
	else if ($sql_mst_res[0][csf('production_source')] == 3)
	{
		$embel_com = $party_library[$sql_mst_res[0][csf('sewing_company')]];
		$embel_location = $location_name_arr[$sql_mst_res[0][csf('sewing_company')]];
	}

	$res = sql_select("select buyer_name,style_ref_no,requisition_number_prefix_num from sample_development_mst where id=$req_id  and status_active=1 and is_deleted=0 and entry_form_id in (117,203,449)");

  	foreach($res as $rows)
	{
		$dtls_data['buyer_name']=$rows[csf('buyer_name')];
		$dtls_data['style_ref_no']=$rows[csf('style_ref_no')];
		$dtls_data['req_no']=$rows[csf('requisition_number_prefix_num')];
	}

	if($db_type==2){$reporting_hour_fill=" TO_CHAR( a.reporting_hour,'HH24:MI' ) as reporting_hour ";}
			else{$reporting_hour_fill=" TIME_FORMAT( a.reporting_hour, '%H:%i' ) as reporting_hour ";}

	$sql="SELECT a.sample_name, a.item_number_id, a.sewing_date,$reporting_hour_fill,a.qc_pass_qty,a.remarks ,b.color_id,b.size_id,b.size_pass_qty,b.size_rej_qty,a.embel_name,a.embel_type from sample_sewing_output_dtls a,sample_sewing_output_colorsize b where a.sample_sewing_output_mst_id=$mst_id and a.id=b.sample_sewing_output_dtls_id and a.id IN($dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=338";
	$sql_result=sql_select($sql);
	$gmts_item_arr=array();
	$emb_date_arr=array();
	$sample_name_arr=array();
	$reporting_hour_arr=array();
	$remarks_arr=array();

	foreach ($sql_result as $rows)
	{
		$dtls_data['embel_name']=$rows[csf('embel_name')];
		$dtls_data['embel_type']=$rows[csf('embel_type')];
		$dtls_data['sewing_date']=$rows[csf('sewing_date')];

		if(! in_array($rows[csf('item_number_id')], $gmts_item_arr))
		$gmts_item_arr[$rows[csf('item_number_id')] ] = $garments_items[$rows[csf('item_number_id')]];

		if(! in_array($rows[csf('sample_name')], $sample_name_arr))
		$sample_name_arr[$rows[csf('sample_name')] ] = $sample_name_library[$rows[csf('sample_name')]];

		if(! in_array($rows[csf('reporting_hour')], $reporting_hour_arr))
		$reporting_hour_arr[$rows[csf('color_id')]] = $rows[csf('reporting_hour')];

		if(! in_array($rows[csf('sewing_date')], $emb_date_arr))
		$emb_date_arr[$dtls_data['sewing_date']] = change_date_format($rows[csf('sewing_date')]);

		if(! in_array($rows[csf('embel_name')], $emblishment_name_arr))
		$emblishment_name_arr[$dtls_data['embel_name']] = $emblishment_name_array[$rows[csf('embel_name')]];

		if(! in_array($rows[csf('remarks')], $remarks_arr))
		$remarks_arr[$rows[csf('remarks')]] = $rows[csf('remarks')];

		$dtls_data['qc_pass_qty']+=$rows[csf('qc_pass_qty')];
		$dtls_data[$rows[csf('color_id')]]=$rows[csf('remarks')];
		$dtls_data['gmts']=$rows[csf('item_number_id')];

		$size_arr[$rows[csf('size_id')]]=$rows[csf('size_id')];

		$tot_color_good_qty[$rows[csf('color_id')]]+=$rows[csf('size_pass_qty')];
		$tot_color_rej_qty[$rows[csf('color_id')]]+=$rows[csf('size_rej_qty')];

		$tot_size_good_qty[$rows[csf('size_id')]]+=$rows[csf('size_pass_qty')];
		$tot_size_rej_qty[$rows[csf('size_id')]]+=$rows[csf('size_rej_qty')];

		$good_qty[$rows[csf('color_id')]][$rows[csf('size_id')]]+=$rows[csf('size_pass_qty')];
		$rej_qty[$rows[csf('color_id')]][$rows[csf('size_id')]]+=$rows[csf('size_rej_qty')];

		$is_reject+=$rows[csf('size_rej_qty')];
	}


	$sql2="SELECT a.sample_status_id, b.color_id,b.size_id, sum(b.size_pass_qty) as size_pass_qty,sum(b.size_rej_qty) as size_rej_qty,sum(a.qc_pass_qty) as qc_pass_qty from sample_sewing_output_dtls a,sample_sewing_output_colorsize b where 	a.sample_sewing_output_mst_id=$mst_id  and a.id=b.sample_sewing_output_dtls_id and a.id in($dtls_id)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.sample_status_id, b.color_id,b.size_id";
	$sql_result=sql_select($sql2);
	$tot_size=count($size_arr);
	$width=1000;
	$width_2=($width*$tot_size)+650;
	$emb_name=$dtls_data['embel_name'];
	
	if($emb_name==1)
	{
		$embel_type=$emblishment_print_type[$dtls_data['embel_type']];
	}
	else if($emb_name==2)
	{
		$embel_type=$emblishment_embroy_type[$dtls_data['embel_type']];
	}
	else if($emb_name==3)
	{
		$embel_type=$emblishment_wash_type[$dtls_data['embel_type']];
	}

	else if($emb_name==4)
	{
		$embel_type=$emblishment_spwork_type[$dtls_data['embel_type']];
	}
	else if($emb_name==5)
	{
		$embel_type=$emblishment_gmts_type[$dtls_data['embel_type']];
	}
    ?>

    <table width="70%" cellspacing="0" align="center">
        <tr>
		<td rowspan="4" width="150"> <img src="../../<? echo $image_location; ?>" height="70" width="150" style="float:left;"></td>
            <td colspan="3" align="center" style="font-size:22px"><strong><? echo $company_library[$company_id]; ?></strong></td>
        </tr>
        <tr>
        	<td colspan="3" align="center" style="font-size:12px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
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
			<td colspan="3" align="center"><strong>Sample <? echo $emblishment_name_array[$embName];?>  Issue Challan</strong></td>
		</tr>
		</table>
       
		<table align="center" width="70%" cellspacing="0" border="1">
		<tr>
			<td><strong>Source  : </strong><? echo $knitting_source[$sql_mst_res[0][csf('production_source')]];?></td>
			<td><strong>Embel. Company : </strong><? echo $embel_com; ?></td>
			<td><strong>Location : </strong><? echo $embel_location; ?></td>
		</tr>
       
       <tr>
	   		<td><strong><? if($dtls_data['embel_name']==1){echo "Issue";} elseif($dtls_data['embel_name']==2){echo "Embroidery";} else {echo "Special Works";} ?> Date : </strong><? echo implode(",", $emb_date_arr);  ?></td>
			<td><strong>Embel.Name : </strong><? 
			
			echo implode(",", $emblishment_name_arr);  ?><? //echo $emblishment_name_array[$dtls_data['embel_name']]; ?></td>
			<td><strong>Embel. Type  : </strong><? echo $embel_type; ?></td>
	   </tr>

    </table>
    <br>

	<table  border="1" rules="all" class="rpt_table" width="70%" align="center">

		<?
		$res = sql_select("select buyer_name,style_ref_no,requisition_number from sample_development_mst where id=$req_id  and status_active=1 and is_deleted=0 and entry_form_id in (117,203,449)");

		foreach($res as $rows)
	  {
		  $dtls_data['req_no']=$rows[csf('requisition_number')];
	  }
		?>
		<tr>
	<td  width="50"><strong>Sample Requisition No</strong></td>
	<td  width="20"><?  echo $dtls_data['req_no'];?></td>
	<td  width="120" style="border-bottom:hidden;border-right:hidden;border-top:hidden"><strong></strong></td>
	<td  width="140"  style="border-bottom:hidden;border-right:hidden;border-top:hidden"><?  //echo $dtls_data['req_no'];?></td>
	</tr>
	</table>	
	<br><br>		
    <!-- ......................Good Qty Part...................................... -->
    <div></div>
    <table border="1" rules="all" class="rpt_table" width="70%" align="center">
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th rowspan="2">Buyer</th>
				<th rowspan="2">Style</th>
                <th rowspan="2">Color</th>
                <th rowspan="2">Size</th>
                <th rowspan="2">QC Pass Qty(Pcs)</th>
                <th rowspan="2" width="80">Remarks</th>
            </tr>
        </thead>
        <tbody>
			<?
            $i=1;
            foreach($sql_result as $key=>$vals)
            {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                $color_id=$vals[csf("color_id")];
				$size_id=$vals[csf("size_id")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $buyer_library[$dtls_data['buyer_name']]; ?></td>
						<td align="center"><? echo $dtls_data['style_ref_no']; ?></td>
                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                        <td align="center"><? echo $size_library[$size_id]; ?></td>
                        <td align="right"><? echo $tot_color_good_qty[$color_id]; ?></td>
            			<td align="center"><? echo $dtls_data[$color_id]; ?></td>
                    </tr>
                    <?
					$i++;
					$tot_qnty+=$tot_color_good_qty[$color_id];
                }
            ?>
        </tbody>
        <tr>
            <td colspan="4" align="right"><strong>Grand Total :</strong></td>
			<td align="right"><?php echo $tot_qnty; ?></td>
            <td colspan="">&nbsp;</td>
        </tr>
		<tr>
            <td colspan="6" style="border-left:hidden;border-right:hidden;border-bottom:hidden; text-align: left; font-size:15px;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
        </tr>
    </table>
	<? echo signature_table(222, $company_id, "1350px"); ?>
    </div>
    </div>

	<?
		//for gate pass
		$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.SECURITY_LOCK_NO,a.DRIVER_NAME,a.DRIVER_LICENSE_NO, b.QUANTITY, b.NO_OF_BAGS as challan_id FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b,sample_sewing_output_mst c WHERE a.id = b.mst_id And a.CHALLAN_NO=$mst_id AND a.company_id = ".$company_id." AND a.basis = 60 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0";
		//echo $sql_get_pass; die;
		$sql_get_pass_rslt = sql_select($sql_get_pass);
		$is_gate_pass = 0;
		$is_gate_out = 0;
		$gate_pass_id = '';
		$gatePassDataArr = array();
		foreach($sql_get_pass_rslt as $row)
		{			
					$is_gate_pass = 1;
					$gate_pass_id = $row['ID'];
					$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
					$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
					$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
					
					if($row['WITHIN_GROUP'] == 1)
					{
						$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					}
					
					//for gate pass info
					$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id'] = $row['SYS_NUMBER'];
					$gatePassDataArr[$row['CHALLAN_NO']]['from_company'] = $company_library[$row['COMPANY_ID']];
					$gatePassDataArr[$row['CHALLAN_NO']]['from_location'] =$location_lib[ $row['COM_LOCATION_ID']];
					$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
					$gatePassDataArr[$row['CHALLAN_NO']]['returnable'] = $yes_no[$row['RETURNABLE']];
					$gatePassDataArr[$row['CHALLAN_NO']]['est_return_date'] = $row['EST_RETURN_DATE'];
					$gatePassDataArr[$row['CHALLAN_NO']]['to_company'] = $row['SENT_TO'];
					$gatePassDataArr[$row['CHALLAN_NO']]['to_location'] = $location_lib[$row['LOCATION_NAME']];
					$gatePassDataArr[$row['CHALLAN_NO']]['delivery_kg'] += $row['QUANTITY'];
					$gatePassDataArr[$row['CHALLAN_NO']]['delivery_bag'] += $row['NO_OF_BAGS'];					
					$gatePassDataArr[$row['CHALLAN_NO']]['department'] = $department_arr[$row['DEPARTMENT_ID']];
					$gatePassDataArr[$row['CHALLAN_NO']]['attention'] = $row['ATTENTION'];
					$gatePassDataArr[$row['CHALLAN_NO']]['issue_purpose'] = $row['ISSUE_PURPOSE'];
					$gatePassDataArr[$row['CHALLAN_NO']]['remarks'] = $row['REMARKS'];
					$gatePassDataArr[$row['CHALLAN_NO']]['carried_by'] = $row['CARRIED_BY'];
					$gatePassDataArr[$row['CHALLAN_NO']]['vhicle_number'] = $row['VHICLE_NUMBER'];
					$gatePassDataArr[$row['CHALLAN_NO']]['mobile_no'] = $row['MOBILE_NO'];
					$gatePassDataArr[$row['CHALLAN_NO']]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
					$gatePassDataArr[$row['CHALLAN_NO']]['driver_name'] = $row['DRIVER_NAME'];
					$gatePassDataArr[$row['CHALLAN_NO']]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
		}
		
		//for gate out
		if($gate_pass_id != '')
		{
			$sql_gate_out="SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='".$gate_pass_id."'";
			$sql_gate_out_rslt = sql_select($sql_gate_out);
			if(!empty($sql_gate_out_rslt))
			{
				foreach($sql_gate_out_rslt as $row)
				{
					$is_gate_out = 1;
					$gatePassDataArr[$row['CHALLAN_NO']]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
					$gatePassDataArr[$row['CHALLAN_NO']]['out_time'] = $row['OUT_TIME'];
				}
			}
		}
	?>

<table border="1" rules="all" class="rpt_table" width="70%" align="center">
               
                <tr>
                    <td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
                    <td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id" height="50"></td>
                </tr>
                <tr>
                    <td colspan="2" title="<? echo $gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id']; ?>"><strong>From Company:</strong></td>
                    <td colspan="2" width="120"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['from_company']; ?></td>

                    <td colspan="2"><strong>To Company:</strong></td>
                    <td colspan="3" width="120"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['to_company']; ?></td>

                    <td colspan="3"><strong>Carried By:</strong></td>
                    <td colspan="3" width="120"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['carried_by']; ?></td>
                </tr>						
                <tr>
                    <td colspan="2"><strong>From Location:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['from_location']; ?></td>
                    <td colspan="2"><strong>To Location:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['to_location']; ?></td>
                    <td colspan="3"><strong>Driver Name:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['driver_name']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Gate Pass ID:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id']; ?></td>
                    <td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
                    <td align="center" colspan="3"><strong>PCS</td>
                    <td colspan="3"><strong>Vehicle Number:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['vhicle_number']; ?></td>
                </tr>						
                <tr>
                    <td colspan="2"><strong>Gate Pass Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_date']; ?></td>
                    <td align="center" colspan="3"><?php 
                     if ($gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id'] !="") 
                    {
                        if ($tot_qnty>0) {
                            echo $tot_qnty;
                         } 
                    } 
                    ?></td>
                    <td colspan="3"><strong>Driver License No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['driver_license_no']; ?></td>
                </tr>						
                <tr>
                    <td colspan="2"><strong>Out Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['out_date']; ?></td>
                    <td colspan="2"><strong>Dept. Name:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['department']; ?></td>
                    <td colspan="3"><strong>Mobile No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['mobile_no']; ?></td>
                </tr>						
                <tr>
                    <td colspan="2"><strong>Out Time:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['out_time']; ?></td>
                    <td colspan="2"><strong>Attention:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['attention']; ?></td>
                    <td colspan="3"><strong>Sequrity Lock No.:</strong></td>
                    <td colspan="3"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['security_lock_no']; ?></td>
                </tr>						
                <tr>
                    <td colspan="2"><strong>Returnable:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['returnable']; ?></td>
                    <td colspan="2"><strong>Purpose:</strong></td>
                    <td colspan="9"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['issue_purpose']; ?></td>
                </tr>						
                <tr>
                    <td colspan="2"><strong>Est. Return Date:</strong></td>
                    <td colspan="2"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['est_return_date']; ?></td>
                    <td colspan="2"><strong>Remarks:</strong></td>
                    <td colspan="9"><?php echo $gatePassDataArr[$row['CHALLAN_NO']]['remarks']; ?></td>
                </tr>
            </table>
			<br><br>
			  <!-- ============= Gate Pass Info End =========== -->
			<table border="1" rules="all" class="rpt_table" width="70%" align="center">
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th rowspan="2">Buyer</th>
				<th rowspan="2">Style</th>
                <th rowspan="2">Color</th>
                <th rowspan="2">Size</th>
                <th rowspan="2">QC Pass Qty(Pcs)</th>
                <th rowspan="2" width="80">Remarks</th>
            </tr>
        </thead>
        <tbody>
			<?
            $i=1;
            foreach($sql_result as $key=>$vals)
            {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                $color_id=$vals[csf("color_id")];
				$size_id=$vals[csf("size_id")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $buyer_library[$dtls_data['buyer_name']]; ?></td>
						<td align="center"><? echo $dtls_data['style_ref_no']; ?></td>
                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                        <td align="center"><? echo $size_library[$size_id]; ?></td>
                        <td align="right"><? echo $tot_color_good_qty[$color_id]; ?></td>
            			<td align="center"><? echo $dtls_data[$color_id]; ?></td>
                    </tr>
                    <?
					$i++;
					$tot_qty+=$tot_color_good_qty[$color_id];
                }
            ?>
        </tbody>
        <tr>
            <td colspan="4" align="right"><strong>Grand Total :</strong></td>
			<td align="right"><?php echo $tot_qty; ?></td>
            <td colspan="">&nbsp;</td>
        </tr>
    </table>
                  
        <table width="100%" cellspacing="0" align="left" cellpadding=""  style="margin-left: -50px;float: left;"   >
			<tr>
			<? echo signature_table(222, $company_id, "1350px"); ?>
			</tr>
        </table>
        </div>
		<script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquery.qrcode.min.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
        <script>
            function generateBarcodeGatePass(valuess)
            {
                var value = valuess;
                var btype = 'code39';
                var renderer = 'bmp';
                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                };
                $("#gate_pass_barcode_img_id").html('11');
                value = {code: value, rect: false};
                $("#gate_pass_barcode_img_id").show().barcode(value, btype, settings);
            }
            
			generateBarcodeGatePass('<? echo $gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id']; ?>');
        </script>
    <?
    exit();
}


?>

<!-- <script type="text/javascript" src="../js/jquery.qrcode.min.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
       
        <script>
            //for gate pass barcode
            function generateBarcodeGatePass(valuess)
            {
                //var zs = '<?php //echo $x; ?>';
                var value = valuess;//$("#barcodeValue").val();
				//alert(value);
				//console.clear();
				console.log(`value = ${value}`);
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer = 'bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                    output: renderer,
                    bgColor: '#FFFFFF',
                    color: '#000000',
                    barWidth: 1,
                    barHeight: 30,
                    moduleSize: 5,
                    posX: 10,
                    posY: 20,
                    addQuietZone: 1
                }
				//console.log(`settings=${settings.output}`);

                //$("#gate_pass_barcode_img_id").html('11');
				document.getElementById("gate_pass_barcode_img_id").value = "Helal";
				console.log('gate_pass_barcode_img_id');
                value = {code: value, rect: false};
				
                $("#gate_pass_barcode_img_id").show().barcode(value, btype, settings);
				console.log('2101');
            }
            
			generateBarcodeGatePass('<? //echo $gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id']; ?>');
        </script>
		
		<script type="text/javascript" src="../js/jquery.js"></script> -->