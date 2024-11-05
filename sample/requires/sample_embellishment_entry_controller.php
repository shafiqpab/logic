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
$color_library=return_library_array( "select id, color_name from lib_color","id","color_name");
$size_library=return_library_array( "select id, size_name from lib_size","id","size_name");


if ($action=="load_drop_down_emb_name")
{
	$ex_data=explode("__",$data);
	// print_r($ex_data[0]);
 	echo create_drop_down( "cbo_embel_name", 170, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "load_drop_down( 'requires/sample_embellishment_entry_controller', this.value+'**'+".$ex_data[1].", 'load_drop_down_emb_receive_type', 'emb_type_td' );",'',$ex_data[0],'','','99' );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 157, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
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
		echo create_drop_down( "cbo_embel_type", 170, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "","","$embl_type" );
	elseif($emb_name==2)
		echo create_drop_down( "cbo_embel_type", 170, $emblishment_embroy_type,"", 1, "--- Select Embroidery---", $selected, "" ,"","$embl_type" );
	elseif($emb_name==3)
		echo create_drop_down( "cbo_embel_type", 170, $emblishment_wash_type,"", 1, "--- Select wash---", $selected, "","","$embl_type" );
	elseif($emb_name==4)
		echo create_drop_down( "cbo_embel_type", 170, $emblishment_spwork_type,"", 1, "--- Select Special Works---", $selected, "","","$embl_type" );
	elseif($emb_name==5)
		echo create_drop_down( "cbo_embel_type", 170, $emblishment_gmts_type,"", 1, "--- Select---", $selected, "","","$embl_type" );
	else
		echo create_drop_down( "cbo_embel_type", 170, $blank_array,"", 1, "--- Select---", $selected, "" );
	exit();
}


if($action=="populate_data_yet_to_cut")
{
	$data=explode("__", $data);
	$val=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_sewing_output_mst_id=$data[1] and sample_dtls_row_id=$data[0] and entry_form_id=128 and sample_name=$data[2] and item_number_id=$data[3] and embel_name=$data[4]");

	echo $val;
	exit();
}

if($action=="embellishment_name_as_per_req")
{
	$emb="";
	$data=explode("**", $data);
	$emb_names=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$data[0] and form_type=3 and is_deleted=0 and status_active=1   and sample_name_re=$data[1] and gmts_item_id_re=$data[2] order by id asc");
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
	$emb_names=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$data[0] and form_type=3 and is_deleted=0 and status_active=1   and sample_name_re=$data[1] and gmts_item_id_re=$data[2] order by id asc");
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
   echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/sample_embellishment_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
 	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
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
			echo create_drop_down( "cbo_embellishment_company", 170, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sample_embellishment_entry_controller');",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_embellishment_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sample_embellishment_entry_controller');",0,0 );
		}
	}
	else if($data==1)
	{
 		echo create_drop_down( "cbo_embellishment_company", 170, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", $selected_company,  "load_drop_down( 'requires/sample_embellishment_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 );

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
		<table width="1000" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
            		<table  cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
                        <thead>
                        	<th  colspan="4">
                              <?
                               echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                            <th colspan="2"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_shipped_po">Full Shipped PO</th>

                        </thead>
                        <thead>
                        	<th width="140">Company Name</th>
                            <th width="160">Buyer Name</th>
                            <th width="130">Requisition No</th>
                            <th width="100">Int. Ref. No </th>   
                            <th  width="130" >Style Ref</th>
                            <th width="200">Est. Ship Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100%" /></th>
                        </thead>
        				<tr>
                        	<td width="140">
								<input type="hidden" id="selected_id"/>
								<? //echo $company.'system';
								    if ($company != 0){
								    	echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'sample_embellishment_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1 );
								    }
								    else
								    {
								    	echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'sample_embellishment_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",0 );
								    }

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
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style_id').value+'_'+document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_name').value+'_'+document.getElementById('chk_shipped_po').value+'_'+document.getElementById('txt_int_ref_no').value, 'create_po_search_list_view', 'search_div', 'sample_embellishment_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                            </td>
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
            	<td align="center" valign="top" id="search_div"></td>
        	</tr>
    	</table>
    </form>
	</div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
	    load_drop_down( 'sample_embellishment_entry_controller',<? echo $company; ?>, 'load_drop_down_buyer', 'buyer_td' );
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
			if($req_id==0) $req_id.=$key;
			else  $req_id.=','.$key;

		}
		else{
			if($req_id1==0) $req_id1.=$key;
			else  $req_id1.=','.$key;
		}
	}

	$txt_int_ref_no=trim(str_replace("'","",$data[8]));
		if($txt_int_ref_no!="") $ref_cond=" and internal_ref like '%$txt_int_ref_no%'";else $ref_cond="";
		
		/*
		$dtls_found= "SELECT  b.style_id,a.grouping from wo_non_ord_samp_booking_mst a ,wo_non_ord_samp_booking_dtls b  where  a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0  and   a.entry_form_id=140 and  b.entry_form_id=140 $ref_cond  $company  $buyer  and a.booking_type=4 and  a.status_active=1 and a.is_deleted=0   ";
		$booking_dtls_arr=array();
		$internal_ref_arr=array();
		$style_id_in_cond="";
		foreach(sql_select($dtls_found) as $v)
		{
			$booking_dtls_arr[$v[csf("style_id")]]=$v[csf("style_id")];
			$internal_ref_arr[$v[csf("style_id")]]=$v[csf("grouping")];
		}
		
		//echo "<pre>";
		//print_r($internal_ref_arr);
		if($txt_int_ref_no!="")
		{
			$style_id=implode(",",$booking_dtls_arr);
			$style_id_in_cond=" and id in ($style_id)";
		}*/
	

	$req_id_array = explode(",", $req_id);
	$req_id_list_arr=array_chunk($req_id_array,999);
	$reqCond = " and ";$reqCond2 = " and ";
	$p=1;
	foreach($req_id_list_arr as $reqids)
    {
    	if($p==1) 
		{
			$reqCond .="  ( a.sample_development_id not in(".implode(',',$reqids).")"; 
			$reqCond2 .="  ( a.sample_development_id in(".implode(',',$reqids).")"; 
		}
        else
        {
          $reqCond .=" or a.sample_development_id not in(".implode(',',$reqids).")";
		  $reqCond2 .=" or a.sample_development_id in(".implode(',',$reqids).")";
      	}
        $p++;
    }
    $reqCond .=")";$reqCond2 .=")";


	$arr=array (2=>$buyer_arr,4=>$product_dept,6=>$dealing_marchant);

	if($data[7] == 1){
		//$sql= "select id,requisition_number_prefix_num,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant from sample_development_mst where id in(select a.sample_development_id from sample_sewing_output_mst a join sample_ex_factory_dtls b on a.sample_development_id=b.sample_development_id where a.entry_form_id=338 and a.status_active=1 and a.is_deleted=0 and b.shiping_status =3) and id in(select sample_mst_id from sample_development_fabric_acc where form_type=3  and status_active=1 and is_deleted=0) and entry_form_id in (117,203,449))  and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate order by id asc";
		$sql= "SELECT id, requisition_number_prefix_num, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant, $yearCond as year ,internal_ref from sample_development_mst where id in(select a.sample_development_id from sample_sewing_output_mst a join sample_development_fabric_acc b on a.sample_development_id = b.sample_mst_id where a.entry_form_id=338 and a.status_active=1 and a.is_deleted=0 and b.form_type=3 and b.status_active=1 and b.is_deleted=0 $reqCond2 group by a.sample_development_id) and entry_form_id  in (117,203,449) and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $ref_cond $style_cond $estimated_shipdate order by id asc";
	}
	else{
		//$sql= "select id,requisition_number_prefix_num,company_id,buyer_name,style_ref_no,product_dept,dealing_marchant from sample_development_mst where id in(select sample_development_id from sample_sewing_output_mst where entry_form_id=338  and status_active=1 and is_deleted=0) and id in(select sample_mst_id from sample_development_fabric_acc where form_type=3  and status_active=1 and is_deleted=0) and entry_form_id in (117,203,449)  and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond $estimated_shipdate order by id asc";
		$sql= "SELECT id, requisition_number_prefix_num, company_id, buyer_name, style_ref_no, product_dept, dealing_marchant, $yearCond as year,internal_ref from sample_development_mst where id in(select a.sample_development_id from sample_sewing_output_mst a join sample_development_fabric_acc b on a.sample_development_id = b.sample_mst_id where a.entry_form_id=338 and a.status_active=1 and a.is_deleted=0 and b.form_type=3 and b.status_active=1 and b.is_deleted=0 $reqCond group by a.sample_development_id) and entry_form_id  in (117,203,449) and status_active=1 and is_deleted=0 $company $buyer $style_id_cond $style_cond  $ref_cond $estimated_shipdate order by id asc";
	}
//echo $sql;
	echo  create_list_view("list_view", "Req No,Year,Buyer Name,Style Name,Product Department,Int. Ref. No ,Dealing Merchant", "60,140,140,100,90,90,90,90","1000","240",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,0,product_dept,0,dealing_marchant", $arr , "requisition_number_prefix_num,year,buyer_name,style_ref_no,product_dept,internal_ref,dealing_marchant", "",'','0,0,0,0,0,0') ;
	exit();
}

if($action=="populate_data_from_search_popup")
{
    $res = sql_select("select id,requisition_number_prefix_num,company_id,buyer_name,style_ref_no,item_name from sample_development_mst where entry_form_id in (117,203,449) and id=$data  and status_active=1 and is_deleted=0");
    $sqls=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$data and form_type=3 and is_deleted=0 and status_active=1  and name_re not in (99) order by id asc");

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
	    echo "load_drop_down( 'requires/sample_embellishment_entry_controller', '".$result[csf("company_id")]."', 'load_drop_down_location', 'location_td' );\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_sample_requisition_id').val('".$result[csf('requisition_number_prefix_num')]."');\n";
		echo "$('#hidden_requisition_id').val('".$result[csf('id')]."');\n";
		echo "load_drop_down( 'requires/sample_embellishment_entry_controller', '".$names_re.'__'.$data."', 'load_drop_down_emb_name', 'embel_name_td' );\n";
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
            <th>Garments Item</th>
            <th>Color</th>
            <th width="60">Sample Qty</th>
        </thead>
		<?
		$i=1;

		$sqlResult = sql_select("select b.id,b.gmts_item_id,b.sample_name,b.sample_color,sum(c.total_qty) as size_qty from sample_development_mst a,sample_development_dtls b, sample_development_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and a.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.sample_name,b.sample_color,b.gmts_item_id,b.id order by b.id asc");

		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_sample_item_data(<? echo $row[csf('id')];?>,<? echo $row[csf('sample_name')]; ?>,<? echo $row[csf('gmts_item_id')]; ?>);">
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
	list($sample_dtls_part_tbl_id,$smp_id,$req_id,$gmts,$embel_name,$type,$position_data,$status_id,$embel_type)=explode('**',$data);
		// echo $type.'=='.$embel_type.'DS';die;
if($status_id==0) 
{
	 $status_idCond="";$status_idCond2="";
}
else { 
 $status_idCond="and b.sample_status_id=$status_id ";$status_idCond2="and  sample_status_id=$status_id ";
}
	$val_req_embel = return_field_value("id","sample_development_fabric_acc","sample_name_re=$smp_id and gmts_item_id_re=$gmts and sample_mst_id=$req_id and status_active=1 and is_deleted=0 and form_type=3 and name_re<>3 and name_re<>5 ");
	if($val_req_embel=="")
	{
		//echo "alert('Emblishment is not available for this sample and item');\n";
		echo "$('#breakdown_td_id').html('');\n";
		//die();
	}
	$embel_typeCond="";$embel_typeCond2="";
if($embel_type) { 

	$embel_typeCond=" and b.embel_type=$embel_type";
	$embel_typeCond2=" and embel_type=$embel_type";
}
	
 	if($type=='single')
	{
		//Inserted data;
		$colorResult_qc_pass = sql_select(" select a.id,b.sample_name,c.color_id,c.size_id,c.size_pass_qty,c.size_rej_qty
		from
			sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c
		where
			a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and  a.sample_development_id=$req_id and b.sample_name=$smp_id and b.item_number_id=$gmts and b.embel_name=$embel_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=128 and b.entry_form_id=128 and c.entry_form_id=128 and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and b.embel_name=$embel_name  and c.status_active=1 and c.is_deleted=0 $status_idCond order by c.id asc");// 
			 
		$colorResult = sql_select("select c.color_id, c.size_id, c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=$smp_id  and b.embel_name=$embel_name and b.item_number_id=$gmts and b.sample_dtls_row_id=$sample_dtls_part_tbl_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=338 and c.color_id is not null $status_idCond $embel_typeCond2");//
		
		$total_cut=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and embel_name=$embel_name and sample_dtls_row_id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0 and entry_form_id=338   $status_idCond2 $embel_typeCond2","qc_pass_qty");//

		$total_cuml=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts  and embel_name=$embel_nameand status_active=1 and is_deleted=0 and entry_form_id=128  and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$embel_name","qc_pass_qty"); //and embel_name=$embel_name
		//echo $total_cuml.'DDSxxx';
		echo "$('#dynamic_cut_qty').html('Total Cutting Qty');\n";
	}
	else if($type=='position_one')
	{
		//Inserted data;
		$colorResult_qc_pass = sql_select("
		select
			b.sample_name,c.color_id,c.size_id,c.size_pass_qty,c.size_rej_qty
		from
			sample_sewing_output_mst a,
			sample_sewing_output_dtls b,
			sample_sewing_output_colorsize c
		where
			a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and  a.sample_development_id=$req_id and b.sample_name=$smp_id and b.item_number_id=$gmts and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=128 and b.entry_form_id=128 and c.entry_form_id=128 and b.embel_name=$embel_name ");

		$colorResult = sql_select("select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=$smp_id  and b.item_number_id=$gmts and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=128 and b.embel_name=$position_data and c.color_id is not null ");

  		 $total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$position_data");

  		$total_cuml=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$embel_name");//
  		echo "$('#dynamic_cut_qty').html('Total $emblishment_name_array[$position_data] Qty');\n";

	}

	else if($type=='position_two')
	{
 		$colorResult_qc_pass = sql_select("
		select
			b.sample_name,c.color_id,c.size_id,c.size_pass_qty,c.size_rej_qty
		from
			sample_sewing_output_mst a,
			sample_sewing_output_dtls b,
			sample_sewing_output_colorsize c
		where
			a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and  a.sample_development_id=$req_id and b.sample_name=$smp_id and b.item_number_id=$gmts and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=128 and b.entry_form_id=128 and c.entry_form_id=128 and b.embel_name=$embel_name order by c.id asc");


		$colorResult = sql_select("select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=$smp_id  and b.item_number_id=$gmts and b.sample_dtls_row_id=$sample_dtls_part_tbl_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=128 and b.embel_name=$position_data and c.color_id is not null order by c.id asc");

		$total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$position_data");

  		$total_cuml=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$embel_name ");//
  		  echo "$('#dynamic_cut_qty').html('Total $emblishment_name_array[$position_data] Qty');\n";



	}
	$totQcPassRejQty=$totQcPassQty=0;

	foreach($colorResult_qc_pass as $row)
		{
		$qcPassQtyArr[$row[csf("sample_name")]][$row[csf("color_id")]][$row[csf("size_id")]]+=$row[csf("size_pass_qty")];
		$totQcPassQty+=$row[csf("size_pass_qty")];
		$totQcPassRejQty+=$row[csf("size_rej_qty")];
		}
//print_r($qcPassQtyArr);
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
				//echo $total_qty.'='.$qcPassQtyArr[$smp_id][$color_id][$size_id].'k';

				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'" class="text_boxes_numeric" style="width:80px" placeholder="'.($total_qty-$qcPassQtyArr[$smp_id][$color_id][$size_id]).'" onblur="fn_total('.$color_id.','.$i.')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color_id.','.$i.')" '.$disable.'><input type="hidden" name="colorSizeinput" id="colorSizeinput_'.$color_id.$i.'"  value="'.$total_qty.'" class="text_boxes_numeric" style="width:50px" ></td></tr>';

				$i++;
			}
			$colorHTML .= "</table></div>";
		}
		echo "$('#txt_cumul_embel_qty').val('');\n";
		echo "$('#txt_total_cutting_qty').val('');\n";
		echo "$('#txt_yet_to_embel').val('');\n";
 		echo "$('#txt_reporting_hour').val('');\n";
		echo "$('#txt_embellishment_qty').val('');\n";
		echo "$('#txt_reject_qnty').val('');\n";
		echo "$('#txt_remark').val('');\n";

  		$value=return_field_value("gmts_item_id","sample_development_dtls","entry_form_id in (117,203,449) and sample_mst_id=$req_id and id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0");
  		$qty=return_field_value("sum(total_qty)","sample_development_size","mst_id=$req_id and dtls_id=$sample_dtls_part_tbl_id and status_active=1 and is_deleted=0");


  		//$total_cuml=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$smp_id and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_part_tbl_id and embel_name=$embel_name");


 		$name_re_val=return_field_value("name_re","sample_development_fabric_acc","sample_mst_id=$req_id and sample_name_re=$smp_id and gmts_item_id_re=$gmts and status_active=1 and is_deleted=0 and form_type=3");
 		$type_re_val=return_field_value("type_re","sample_development_fabric_acc","sample_mst_id=$req_id and sample_name_re=$smp_id and gmts_item_id_re=$gmts and status_active=1 and is_deleted=0 and form_type=3");
 		echo "$('#cbo_item_name').val(".$value.");\n";
 		echo "$('#txt_sample_qty').val(".$qty.");\n";
		echo "$('#txt_cumul_embel_qty').val(".$total_cuml.");\n";
		echo "$('#txt_total_cutting_qty').val(".$total_cut.");\n";
		
		echo "$('#txt_cumul_rej_qty').val(".$totQcPassRejQty.");\n";
		echo "$('#txt_cumul_previ_qty').val('".$totQcPassQty."');\n";
		
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
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="570" class="rpt_table">
            <thead>
            	<th width="30">&nbsp;</th>
                <th width="30">SL</th>
                <th width="110">Sample Name</th>
                <th width="70">Prod. Date</th>
                 <th width="70">Status</th>
                <th width="80">QC Pass Qty</th>
                <th width="60">Reject Qty</th>
                 <th width="60">Rep. Hour</th>

            </thead>
		</table>
	 
	<div style="width:590; max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="570" class="rpt_table" id="tbl_list_search">
		<?php
$i = 1;
if ($db_type == 2) {$reporting_hour_fill = " TO_CHAR( reporting_hour,'HH24:MI' ) as reporting_hour ";} else { $reporting_hour_fill = " TIME_FORMAT( reporting_hour, '%H:%i' ) as reporting_hour ";}
//echo "select a.id,a.sample_development_id,b.id as dtls_id, b.sample_name,b.sample_dtls_row_id,b.embel_name,b.item_number_id,b.sewing_date,$reporting_hour_fill,b.qc_pass_qty,b.reject_qty from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and a.sample_development_id=$data  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=128 and b.entry_form_id=128 order by b.id asc ";

$sqlResult = sql_select("select a.id,a.sample_development_id,b.id as dtls_id, b.sample_name,b.sample_dtls_row_id,b.embel_name,b.embel_type,b.item_number_id,b.sewing_date,b.sample_status_id,$reporting_hour_fill,b.qc_pass_qty,b.reject_qty from sample_sewing_output_mst a,sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and a.sample_development_id=$data  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.entry_form_id=128 and b.entry_form_id=128 order by b.id asc ");

foreach ($sqlResult as $row) {

	$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
	//$dtls_data = "'".$row[csf('sample_development_id')].'**'.$row[csf('id')].'**'.$row[csf('dtls_id')].'**'.$row[csf('sample_name')].'**'.$row[csf('item_number_id')].'**'.$row[csf('sample_dtls_row_id')].'**'.$row[csf('embel_name')]."'";
	$data_val = "'" . $row[csf('sample_development_id')] . '**' . $row[csf('id')] . '**' . $row[csf('dtls_id')] . '**' . $row[csf('sample_name')] . '**' . $row[csf('item_number_id')] . '**' . $row[csf('sample_dtls_row_id')] . '**' . $row[csf('embel_name')] .'**' . $row[csf('sample_status_id')] .'**' . $row[csf('embel_type')] . "'";
	//echo $data_val;
	$click_val = "get_php_form_data($data_val,'populate_input_form_data','requires/sample_embellishment_entry_controller');"

	?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer">
				<td width="30" align="center">
					<input type="checkbox" id="check_for_print_<? echo $i;?>" name="check_for_print" onChange="release_print_buuton(this)" value="<? echo $row[csf('dtls_id')];?>"/>
				</td>
				<td onClick="<? echo $click_val;?>" width="30" align="center"><? echo $i; ?></td>
				<td onClick="<? echo $click_val;?>"  width="110"><p><? echo $sample_name_library[$row[csf('sample_name')]]; ?></p></td>
				<td onClick="<? echo $click_val;?>"  width="70" align="center"><?php echo change_date_format($row[csf('sewing_date')]); ?></td>
                <td onClick="<? echo $click_val;?>"  width="70" align="center"><? echo $sample_statusArr[$row[csf('sample_status_id')]]; ?></td>
				<td title="Data come from Sample Req. with Booking(Sample Dtls part)" onClick="<? echo $click_val;?>"  width="80" align="right"><? echo $row[csf('qc_pass_qty')]; ?></td>
              
				<td onClick="<? echo $click_val;?>"  width="60" align="right"><? echo $row[csf('reject_qty')]; ?></td>
				<td onClick="<? echo $click_val;?>"  width="60"><p><? echo $row[csf('reporting_hour')]; ?></p></td>
			</tr>
			<?php
$i++;
}
?>
		</table>
    </div></div>
    </fieldset>


    <?
	exit();
}



if($action=="populate_input_form_data")
{

 	list($smp_id,$mst_id,$dtls_id,$sample_name,$gmts,$sample_dtls_row_id,$embel_name,$status_id,$embel_type)=explode('**',$data);
	
	if($status_id==0) 
	{
		 $status_idCond="";$status_idCond2="";
	}
	else { 
	 $status_idCond="and b.sample_status_id=$status_id ";$status_idCond2="and  sample_status_id=$status_id ";
	}
	$embel_typeCond="";$embel_typeCond2="";
	if($embel_type) { 

		$embel_typeCond=" and b.embel_type=$embel_type";
		$embel_typeCond2=" and embel_type=$embel_type";
	}

	$smp_mst_id = sql_select("select id,company_id,production_source,sewing_company,location,floor_id,receive_no_prefix_num from sample_sewing_output_mst where id=$mst_id and entry_form_id=128 and status_active=1 and is_deleted=0");
	if(count($smp_mst_id)>0)
 	{
		echo "load_drop_down('requires/sample_embellishment_entry_controller', '".$smp_mst_id[0][csf('production_source')].'**'.$smp_mst_id[0][csf('company_id')]."', 'load_drop_down_sewing_output', 'sew_company_td' );";
	
		echo "$('#mst_update_id').val('".$smp_mst_id[0][csf('id')]."');\n";
		echo "$('#txt_mst_issue_id').val('".$smp_mst_id[0][csf('receive_no_prefix_num')]."');\n";
		echo "$('#cbo_source').val('".$smp_mst_id[0][csf('production_source')]."');\n";
		echo "$('#cbo_embellishment_company').val('".$smp_mst_id[0][csf('sewing_company')]."');\n";
		echo "$('#cbo_location').val('".$smp_mst_id[0][csf('location')]."');\n";
		echo "load_drop_down( 'requires/sample_embellishment_entry_controller', '".$smp_mst_id[0][csf("location")]."', 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$smp_mst_id[0][csf('floor_id')]."');\n";
	}
	
	
  	$emb_names=sql_select("select  listagg(name_re,',') WITHIN GROUP (ORDER BY id) as name from sample_development_fabric_acc where sample_mst_id=$smp_id and form_type=3 and is_deleted=0 and status_active=1  and name_re<>3 and  name_re<>5  and sample_name_re=$sample_name and gmts_item_id_re=$gmts order by id asc");

	//Inserted data........................
	if($db_type==2){$reporting_hour_fill=" TO_CHAR( a.reporting_hour,'HH24:MI' ) as reporting_hour ";}
	else{$reporting_hour_fill=" TIME_FORMAT( a.reporting_hour, '%H:%i' ) as reporting_hour ";}

		$colorResult = sql_select("select a.id,a.sample_dtls_row_id, a.sample_status_id,a.sample_name,a.embel_name,a.embel_type,a.total_cut_qty,a.prod_qty,a.item_number_id, a.sewing_date, a.line_no,$reporting_hour_fill, a.qc_pass_qty, a.reject_qty,a.remarks,b.color_id as sample_color,b.size_id,b.size_pass_qty as size_qty,b.size_rej_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b where  a.id=b.sample_sewing_output_dtls_id and a.sample_sewing_output_mst_id=$mst_id and b.sample_sewing_output_mst_id = $mst_id and a.sample_name=$sample_name and a.item_number_id=$gmts and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=128 and b.entry_form_id=128 and a.sample_dtls_row_id=$sample_dtls_row_id and a.embel_name=$embel_name");

	foreach($colorResult as $row)
	{
		if($row[csf("sample_color")])
		{
			$colorTotal[$row[csf("id")]][$row[csf("sample_color")]]+=$row[csf("size_qty")];
			$colorData[$row[csf("id")]][$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];
			$colorDataRej[$row[csf("id")]][$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_rej_qty")];

			$sizeQcPassQty[$row[csf("sample_color")]][$row[csf("size_id")]]+=$row[csf("size_qty")];
			$totSizeQcPassQty+=$row[csf("size_qty")];

			$dtlsArr[$row[csf("id")]]['sample_name']=$row[csf('sample_name')];
			$dtlsArr[$row[csf("id")]]['item_number_id']=$row[csf('item_number_id')];
			$dtlsArr[$row[csf("id")]]['prod_qty']=$row[csf('prod_qty')];
 			$dtlsArr[$row[csf("id")]]['sewing_date']=$row[csf('sewing_date')];
 			$dtlsArr[$row[csf("id")]]['reporting_hour']=$row[csf('reporting_hour')];
 			$dtlsArr[$row[csf("id")]]['qc_pass_qty']=$row[csf('qc_pass_qty')];
			$dtlsArr[$row[csf("id")]]['reject_qty']=$row[csf('reject_qty')];
 			$dtlsArr[$row[csf("id")]]['remarks']=$row[csf('remarks')];
 			$dtlsArr[$row[csf("id")]]['sample_dtls_part_tbl_id']=$row[csf('sample_dtls_row_id')];
 			$dtlsArr[$row[csf("id")]]['embel_name']=$row[csf('embel_name')];
 			$dtlsArr[$row[csf("id")]]['embel_type']=$row[csf('embel_type')];
			$dtlsArr[$row[csf("id")]]['status_id']=$row[csf('sample_status_id')];
 			$dtlsArr[$row[csf("id")]]['total_cut_qty']=$row[csf('total_cut_qty')];
		}
	}
	 	echo "$('#txt_total_cutting_qty').val('');\n";
 		echo "$('#dtls_update_id').val('".$dtls_id."');\n";
		echo "$('#cbo_sample_name').val('".$dtlsArr[$dtls_id]['sample_name']."');\n";
		echo "$('#cbo_status_id').val('".$dtlsArr[$dtls_id]['status_id']."');\n";
		echo "$('#cbo_item_name').val('".$dtlsArr[$dtls_id]['item_number_id']."');\n";
		echo "$('#txt_sample_qty').val('".$dtlsArr[$dtls_id]['prod_qty']."');\n";
 		echo "$('#txt_embellishment_date').val('".change_date_format($dtlsArr[$dtls_id]['sewing_date'])."');\n";
		echo "$('#txt_reporting_hour').val('".$dtlsArr[$dtls_id]['reporting_hour']."');\n";
		echo "$('#txt_embellishment_qty').val('".$dtlsArr[$dtls_id]['qc_pass_qty']."');\n";
		echo "$('#txt_reject_qnty').val('".$dtlsArr[$dtls_id]['reject_qty']."');\n";
		echo "$('#txt_remark').val('".$dtlsArr[$dtls_id]['remarks']."');\n";
		echo "$('#cbo_embel_name').val('".$dtlsArr[$dtls_id]['embel_name']."');\n";
		echo "load_drop_down( 'requires/sample_embellishment_entry_controller', '".$dtlsArr[$dtls_id]['embel_name'].'**'.$mst_id."', 'load_drop_down_emb_receive_type', 'emb_type_td' );\n";
		echo "$('#cbo_embel_type').val('".$dtlsArr[$dtls_id]['embel_type']."');\n";

		$this_emb=$dtlsArr[$dtls_id]['embel_name'];
		$name_id=$emb_names[0][csf('name')];
		$name_arr=explode(',', $name_id);
		$req_position=array_search($this_emb, $name_arr);
 	    if($req_position==0) 
		 {
		   $sqlResult = sql_select("select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c  where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']." and b.item_number_id=".$dtlsArr[$dtls_id]['item_number_id']." and a.entry_form_id=338 and b.entry_form_id=338 and c.entry_form_id=338 and b.sample_dtls_row_id=$sample_dtls_row_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $embel_typeCond $status_idCond");
		   $total_cuml=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$embel_name  and sample_status_id=$status_id","qc_pass_qty");//
		   $total_cut=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=338 and sample_dtls_row_id=$sample_dtls_row_id $status_idCond2 $embel_typeCond2","qc_pass_qty");
		   
		   $total_cuml_rej=return_field_value("sum(reject_qty) as reject_qty","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$embel_name $status_idCond2","reject_qty");//
		   
		   echo "$('#dynamic_cut_qty').html('Total Cutting Qty');\n";


 	     }

	      else if($req_position==1)
		  {
  		    $sqlResult = sql_select("select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c  where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']." and b.item_number_id=".$dtlsArr[$dtls_id]['item_number_id']." and a.entry_form_id=128 and b.entry_form_id=128 and c.entry_form_id=128 and b.sample_dtls_row_id=$sample_dtls_row_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=$name_arr[0] $status_idCond");
  		     $total_cuml=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$embel_name $status_idCond2");//and embel_name=$embel_name
  		     $val=$name_arr[0];
    		 $total_cut=return_field_value("sum(qc_pass_qty)","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$name_arr[0] and sample_status_id=$status_id");
			  $total_cuml_rej=return_field_value("sum(reject_qty) as reject_qty","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$name_arr[0] $status_idCond2","reject_qty");//and embel_name=$name_arr[0]
  		     echo "$('#dynamic_cut_qty').html('Total $emblishment_name_array[$val] Qty');\n";
			 //reject_qty

	      }
	      else
	      {
	      	 $sqlResult = sql_select("select c.color_id,c.size_id,c.size_pass_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c  where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.sample_name=".$dtlsArr[$dtls_id]['sample_name']." and b.item_number_id=".$dtlsArr[$dtls_id]['item_number_id']." and a.entry_form_id=128 and b.entry_form_id=128 and c.entry_form_id=128 and b.sample_dtls_row_id=$sample_dtls_row_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=$name_arr[1] and b.sample_status_id=$status_id ");
	      	  $total_cuml=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$embel_name $status_idCond2","qc_pass_qty");//and embel_name=$embel_name
	      	  $total_cut=return_field_value("sum(qc_pass_qty) as qc_pass_qty","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$name_arr[1]  $status_idCond2","qc_pass_qty");
			  $total_cuml_rej=return_field_value("sum(reject_qty) as reject_qty","sample_sewing_output_dtls","sample_name=$sample_name and item_number_id=$gmts and status_active=1 and is_deleted=0 and entry_form_id=128 and sample_dtls_row_id=$sample_dtls_row_id and embel_name=$name_arr[1]  $status_idCond2","reject_qty");//and embel_name=$name_arr[1]
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

				$colorHTML .='<tr><td>'.$size_library[$size_id].'</td><td><input type="text" name="colSizeQty" id="colSizeQty_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:80px" value="'.$size_qty.'" placeholder="'.($smp_qty_arr[$color_id][$size_id]-($sizeQcPassQty[$color_id][$size_id]-$size_qty)).'" onblur="fn_total('.$color_id.','.$i.')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color_id.$i.'"  class="text_boxes_numeric" style="width:50px" value="'.$colorDataRej[$dtls_id][$color_id][$size_id].'" placeholder="Rej. Qty" onblur="fn_total_rej('.$color_id.','.$i.')" '.$disable.'><input type="hidden" name="colorSizeinput" id="colorSizeinput_'.$color_id.$i.'"  value="'.$smp_qty_arr[$color_id][$size_id].'" class="text_boxes_numeric" style="width:50px" ></td></tr>';
			$i++;
			}
			$colorHTML .= "</table></div>";

		}
	
		//list($smp_id,$mst_id,$dtls_id,$sample_name,$gmts,$sample_dtls_row_id)=explode('**',$data);
	    echo "$('#txt_total_cutting_qty').val(".$total_cut.");\n";
  		echo "$('#txt_cumul_embel_qty').val(".$total_cuml.");\n";
		$qc_pass_qty_prev=$dtlsArr[$dtls_id]['qc_pass_qty'];
		$prev_rej=$dtlsArr[$dtls_id]['reject_qty'];
			//echo $total_cuml.'=AAA'.$qc_pass_qty_prev.',';
		if($dtls_id) $total_cuml_rej=$total_cuml_rej-$prev_rej;
		if($dtls_id) $total_cuml_prev=$total_cuml-$qc_pass_qty_prev;
		echo "$('#txt_cumul_rej_qty').val('".$total_cuml_rej."');\n";
		echo "$('#txt_cumul_previ_qty').val('".$total_cuml_prev."');\n";
		
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

		if($mst_update_id=='')
		{
			if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
		
			$new_rec_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SER', date("Y",time()), 5, "select receive_no_prefix, receive_no_prefix_num from sample_sewing_output_mst where company_id=$cbo_company_name and entry_form_id=128 and $date_cond=".date('Y',time())." order by id desc ", "receive_no_prefix", "receive_no_prefix_num" ));
		// master part--------------------------------------------------------------;
			$mst_id=return_next_id("id", "sample_sewing_output_mst", 1);
			$field_array_mst="id, company_id, sample_development_id, production_source, sewing_company, location, floor_id,receive_no_prefix, receive_no_prefix_num, receive_no, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
			$data_array_mst="(".$mst_id.",".$cbo_company_name.",".$hidden_requisition_id.",".$cbo_source.",".$cbo_embellishment_company.",".$cbo_location.",".$cbo_floor.",'".$new_rec_no[1]."',".$new_rec_no[2].",'".$new_rec_no[0]."',".$user_id.",'".$pc_date_time."','1','0',128)";

		// Details part--------------------------------------------------------------;
		if($db_type==2 || $db_type==1){
			$txt_reporting_hour=str_replace("'","",$txt_embellishment_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			}

			$dtls_id=return_next_id("id", "sample_sewing_output_dtls", 1);
			$field_array_dtls="id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date,reporting_hour, qc_pass_qty,reject_qty, remarks, inserted_by, insert_date, status_active, is_deleted,entry_form_id,prod_qty,embel_name,embel_type,total_cut_qty,sample_status_id";
			$data_array_dtls="(".$dtls_id.",".$mst_id.",".$cbo_sample_name.",".$hidden_sample_dtls_tbl_id.",".$cbo_item_name.",".$txt_embellishment_date.",".$txt_reporting_hour.",".$txt_embellishment_qty.",".$txt_reject_qnty.",".$txt_remark.",".$user_id.",'".$pc_date_time."','1','0',128,".$txt_sample_qty.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_total_cutting_qty.",".$cbo_status_id.")";


		// Color & Size Breakdown part--------------------------------------------------------------;cbo_status_id
		$field_array_brk="id, sample_sewing_output_mst_id, sample_sewing_output_dtls_id, color_id, size_id, size_pass_qty, size_rej_qty, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
		$colorsize_brk_id=return_next_id("id", "sample_sewing_output_colorsize", 1);

			// size reject value;
			$rowExRej = explode("***",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$colorID = $colorAndSizeRej_arr[0];
				$sizeID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $colorID.$sizeID;
				$rejQtyArr[$index]=$colorSizeRej;
			}


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

				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0',128)";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0',128)";
				$colorsize_brk_id+=1;
				$j++;
			}


			//insert here----------------------------------------;
			$rID_mst=sql_insert("sample_sewing_output_mst",$field_array_mst,$data_array_mst,0);

			if($flag==1)
			{
				if($rID_mst) $flag=1; else $flag=0;
			}

			$rID_dtls=execute_query("insert into sample_sewing_output_dtls ($field_array_dtls) values $data_array_dtls");

			//$rID_dtls=sql_insert("sample_sewing_output_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1)
			{
				if($rID_dtls) $flag=1; else $flag=0;
			}

			$rID_brk=sql_insert("sample_sewing_output_colorsize",$field_array_brk,$data_array_brk,0);
			 //echo 'abcd '.$rID_brk;die;
			if($flag==1)
			{
				if($rID_brk) $flag=1; else $flag=0;
			}


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
					echo "0**".$mst_id."**".$hidden_requisition_id."**0**".$dtls_id."**".$hidden_sample_dtls_tbl_id."**".$new_rec_no[2];
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
		// Details part--------------------------------------------------------------;
		if($db_type==2 || $db_type==1){
			$txt_reporting_hour=str_replace("'","",$txt_embellishment_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			}

			$dtls_id=return_next_id("id", "sample_sewing_output_dtls", 1);

  			$field_array_dtls="id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date,reporting_hour, qc_pass_qty,reject_qty, remarks, inserted_by, insert_date, status_active, is_deleted,entry_form_id,prod_qty,embel_name,embel_type,total_cut_qty,sample_status_id";
 			$data_array_dtls="(".$dtls_id.",".$mst_update_id.",".$cbo_sample_name.",".$hidden_sample_dtls_tbl_id.",".$cbo_item_name.",".$txt_embellishment_date.",".$txt_reporting_hour.",".$txt_embellishment_qty.",".$txt_reject_qnty.",".$txt_remark.",".$user_id.",'".$pc_date_time."','1','0',128,".$txt_sample_qty.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_total_cutting_qty.",".$cbo_status_id.")";

		// Color & Size Breakdown part--------------------------------------------------------------;
		$field_array_brk="id, sample_sewing_output_mst_id, sample_sewing_output_dtls_id, color_id, size_id, size_pass_qty, size_rej_qty, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
		$colorsize_brk_id=return_next_id("id", "sample_sewing_output_colorsize", 1);

			// size reject value;
			$rowExRej = explode("***",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$colorID = $colorAndSizeRej_arr[0];
				$sizeID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $colorID.$sizeID;
				$rejQtyArr[$index]=$colorSizeRej;
			}


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

				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0',128)";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0',128)";
				$colorsize_brk_id+=1;
				$j++;
			}


			$rID_dtls=execute_query("insert into sample_sewing_output_dtls ($field_array_dtls) values $data_array_dtls");

			//$rID_dtls=sql_insert("sample_sewing_output_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1)
			{
				if($rID_dtls) $flag=1; else $flag=0;
			}

			$rID_brk=sql_insert("sample_sewing_output_colorsize",$field_array_brk,$data_array_brk,0);
			//echo 'abcd '.$rID_brk;die;
			if($flag==1)
			{
				if($rID_brk) $flag=1; else $flag=0;
			}

			//ROLLBACK/COMMIT here----------------------------------------;
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");
					echo "0**".$mst_update_id."**".$hidden_requisition_id."**0**".$dtls_id."**".$hidden_sample_dtls_tbl_id;
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
					echo "0**".$mst_update_id."**".$hidden_requisition_id."**0**".$dtls_id."**".$hidden_sample_dtls_tbl_id."**".$new_rec_no[2];
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

		if($mst_update_id!='')
		{
			// master part--------------------------------------------------------------;
			$field_array_mst="company_id*production_source*sewing_company* location*floor_id*updated_by*update_date";
			$data_array_mst="".$cbo_company_name."*".$cbo_source."*".$cbo_embellishment_company."*".$cbo_location."*".$cbo_floor."*".$user_id."*'".$pc_date_time."'";
			$rID_mst=sql_update("sample_sewing_output_mst",$field_array_mst,$data_array_mst,"id","".$mst_update_id."",1);


		// Dtls part--------------------------------------------------------------;
		if($db_type==2 || $db_type==1){
			$txt_reporting_hour=str_replace("'","",$txt_embellishment_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			}

			$field_array_dtls="sewing_date*embel_name*embel_type*reporting_hour*qc_pass_qty*reject_qty*sample_status_id*remarks*updated_by*update_date";
			$data_array_dtls="".$txt_embellishment_date."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_reporting_hour."*".$txt_embellishment_qty."*".$txt_reject_qnty."*".$cbo_status_id."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";
			$rID_dtls=sql_update("sample_sewing_output_dtls",$field_array_dtls,$data_array_dtls,"id","".$dtls_update_id."",1);

		// Color & Size Breakdown part--------------------------------------------------------------;
		$rID_brk_delete = execute_query("DELETE from sample_sewing_output_colorsize WHERE sample_sewing_output_dtls_id=$dtls_update_id");

		$field_array_brk="id, sample_sewing_output_mst_id, sample_sewing_output_dtls_id, color_id, size_id, size_pass_qty, size_rej_qty, inserted_by, insert_date, status_active, is_deleted,entry_form_id";
		$colorsize_brk_id=return_next_id("id", "sample_sewing_output_colorsize", 1);

			// size reject value;
			$rowExRej = explode("***",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$colorID = $colorAndSizeRej_arr[0];
				$sizeID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $colorID.$sizeID;
				$rejQtyArr[$index]=$colorSizeRej;
			}


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

				if($j==0)$data_array_brk = "(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0',128)";
				else $data_array_brk .= ",(".$colorsize_brk_id.",".$mst_update_id.",".$dtls_update_id.",'".$colorID."','".$sizeID."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$user_id.",'".$pc_date_time."','1','0',128)";
				$colorsize_brk_id+=1;
				$j++;
			}

			$rID_brk=sql_insert("sample_sewing_output_colorsize",$field_array_brk,$data_array_brk,0);



		//echo $rID_mst .'&&'. $rID_dtls .'&&'. $rID_brk_delete .'&&'. $rID_brk; die;

		//-------------------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($rID_mst && $rID_dtls && $rID_brk_delete && $rID_brk)
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
			if($db_type==2 || $db_type==1 )
			{
				if($rID_mst && $rID_dtls && $rID_brk_delete && $rID_brk)
				{
					oci_commit($con);
					echo "1**".$mst_update_id."**".$hidden_requisition_id."**0**".$dtls_update_id."**".$hidden_sample_dtls_tbl_id."**".$new_rec_no[2];
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


 		$rID = sql_delete("sample_sewing_output_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'sample_sewing_output_mst_id  ',$mst_update_id,1);
		$dtlsrID = sql_delete("sample_sewing_output_colorsize","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'sample_sewing_output_mst_id',$mst_update_id,1);

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

	$sql_mst="SELECT a.id, a.production_source, a.sewing_company from sample_sewing_output_mst a where a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=128";
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
		a.sample_sewing_output_mst_id=$mst_id and a.id=b.sample_sewing_output_dtls_id and a.id IN($dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=128";
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
                <th rowspan="2" width="80">Reject Qty</th>
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
                        <td align="right"><? echo $tot_color_rej_qty[$color_id]; ?></td>
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
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>
   <br>

   <!-- ......................Reject Qty Part...................................... -->
   <? if($is_reject){?>
   <div><strong> Reject Qty.</strong></div>
    <table border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th rowspan="2" width="30">SL</th>
                <th rowspan="2">Color</th>
                <th colspan="<? echo $tot_size;?>">Size</th>
                <th rowspan="2" width="80">QC Pass Qty(Pcs)</th>
                <th rowspan="2" width="80">Reject Qty</th>
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
            foreach($sql_result as $keys=>$vals)
            {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                $color_id= $vals[csf('color_id')];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $color_library[$color_id]; ?></td>
                        <?
                        foreach ($size_arr as $size_id)
                        {
                            ?>
                            <td align="right"><? echo $rej_qty[$color_id][$size_id]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? echo $tot_color_good_qty[$color_id]; ?></td>
                        <td align="right"><? echo $tot_color_rej_qty[$color_id]; ?></td>
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
                    <td align="right"><?php echo $tot_size_rej_qty[$size_id]; ?></td>
                    <?
				}
			?>
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>
    <? }else{echo "<b>Note: Reject quantity not found..</b>";} ?>
   <br>
				
	<? echo signature_table(265, $company_id, "750px"); ?>
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
	//echo $mst_id.'mst';
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name"  );
	$garments_items=return_library_array( "select id, item_name from  lib_garment_item", "id", "item_name"  );

	$sql_mst="SELECT a.id, a.production_source, a.sewing_company from sample_sewing_output_mst a where a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=128";
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
		a.sample_sewing_output_mst_id=$mst_id and a.id=b.sample_sewing_output_dtls_id and a.id IN($dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=128";
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
		<td colspan="3" align="center"><strong>Sample <? echo $emblishment_name_array[$embName];?> Receive Challan</strong></td>
        </tr>
		<tr><td><strong>Receive ID : </strong><? echo $mst_id; ?></td></tr>
        <tr>
            <td><strong>Buyer : </strong><? echo $buyer_library[$dtls_data['buyer_name']]; ?></td>
            <td><strong>Style Ref. : </strong><? echo $dtls_data['style_ref_no']; ?></td>
            <td><strong>QC Pass Qty : </strong><? echo $dtls_data['qc_pass_qty']; ?> Pcs</td>
        </tr>
        <tr>
            <td><strong>Sample Requisition No : </strong><? echo $dtls_data['req_no']; ?></td>
            <td><strong>Item : </strong><? echo implode(",", $gmts_item_arr); ?></td>
            <td><strong><? if($dtls_data['embel_name']==1){echo "Production";} elseif($dtls_data['embel_name']==2){echo "Embroidery";} else {echo "Production";} ?> Date : </strong><? echo implode(",", $emb_date_arr);  ?></td>
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
				<th rowspan="2">Status</th>
                <th rowspan="2">Color</th>
                <th colspan="<? echo $tot_size;?>">Size</th>
                <th rowspan="2" width="80">QC Pass Qty(Pcs)</th>
                <th rowspan="2" width="80">Reject Qty</th>
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
                        <td align="right"><? echo $tot_color_rej_qty[$color_id]; ?></td>
                        <td><? echo $reporting_hour_arr[$color_id]; ?></td>
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
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>
   <br>

   <!-- ......................Reject Qty Part...................................... -->
   <? if($is_reject){?>
   <div><strong> Reject Qty.</strong></div>
    <table border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <tr>
                <th rowspan="2" width="30">SL</th>
                <th rowspan="2">Color</th>
                <th colspan="<? echo $tot_size;?>">Size</th>
                <th rowspan="2" width="80">QC Pass Qty(Pcs)</th>
                <th rowspan="2" width="80">Reject Qty</th>
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
            foreach($sql_result as $keys=>$vals)
            {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                $color_id= $vals[csf('color_id')];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $color_library[$color_id]; ?></td>
                        <?
                        foreach ($size_arr as $size_id)
                        {
                            ?>
                            <td align="right"><? echo $rej_qty[$color_id][$size_id]; ?></td>
                            <?
                        }
                        ?>
                        <td align="right"><? echo $tot_color_good_qty[$color_id]; ?></td>
                        <td align="right"><? echo $tot_color_rej_qty[$color_id]; ?></td>
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
                    <td align="right"><?php echo $tot_size_rej_qty[$size_id]; ?></td>
                    <?
				}
			?>
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>
    <? }else{echo "<b>Note: Reject quantity not found..</b>";} ?>
   <br>

   <? echo signature_table(265, $company_id, "750px"); ?>
    </div>
    </div>
    <?
    exit();
}


?>