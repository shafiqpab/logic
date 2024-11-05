 <?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action == "is_checked_style")
{
	$inQuotation=sql_select( "select id from  wo_price_quotation where style_ref='$data'" );
	$inOrder=sql_select( "select id from  wo_po_details_master where style_ref_no='$data'" );

	if(count($inQuotation)>0 || count($inOrder)>0){
		echo 1;
		exit();
	}
	else{
		echo 0;
		exit();
	}
}
if ($action=="search_list_view")
{
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer where status_active=1","id","buyer_name");
	$arr=array (2=>$buyer_arr,3=>$row_status);
	echo  create_list_view ( "list_view", "Style Id No,Style Name, Buyer Name, Status", "150,150,150,200","650","220",0, "select id,style_ref_name,buyer_id,status_active,style_no from lib_style_ref where is_deleted=0 order by id desc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,buyer_id,status_active", $arr , "style_no,style_ref_name,buyer_id,status_active", "requires/style_ref_controller", 'setFilterGrid("list_view",-1);' ) ;
}

if($action=="load_drop_down_buyer_brand")
{
	$sql= "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active=1 and is_deleted=0 order by brand_name";
	//echo $sql;die;
	echo create_drop_down( "cbo_buyer_brand_id", 130,$sql,"id,brand_name", 1, "-- Select Brand --", $selected, "" );   
}
if($action=="load_drop_down_buyer_division")
{
	$sql= "select id, division_name from lib_division_name where buyer_id='$data' and status_active=1 and is_deleted=0 order by division_name";
	//echo $sql;die;
	echo create_drop_down( "txt_division", 130,$sql,"id,division_name", 1, "-- Select Division --", $selected, "load_drop_down( 'requires/style_ref_controller',this.value, 'load_drop_down_division_department', 'department_td' );" );   
}
if($action=="load_drop_down_division_department")
{
	$sql= "select id, department_name from lib_department_name where division_id='$data' and status_active=1 and is_deleted=0 order by department_name";
	//echo $sql;die;
	echo create_drop_down( "cbo_department_id", 130,$sql,"id,department_name", 1, "-- Select Department --", $selected, "" );   
}




if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,style_ref_name,set_break_down,buyer_id,order_uom,gmts_item_id,department_id, status_active,style_no,buyer_brand_id,level_type_id,design_type,product_department_id,division,short_name,offer_qnty from  lib_style_ref where id='$data'" );
	//echo "select id,STYLE_REF_NAME,BUYER_ID, status_active from  lib_style_ref where id='$data'";
	
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_style_name').value = '".($inf[csf("STYLE_REF_NAME")])."';\n";
		echo "document.getElementById('txt_style_short_name').value = '".($inf[csf("short_name")])."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".($inf[csf("BUYER_ID")])."';\n";
		echo "load_drop_down( 'requires/style_ref_controller', document.getElementById('cbo_buyer_name').value, 'load_drop_down_buyer_brand', 'brand_td' );\n"; 
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('cbo_order_uom').value  = '".($inf[csf("order_uom")])."';\n";
    echo "document.getElementById('txt_offer_qnty').value  = '".($inf[csf("offer_qnty")])."';\n";
		echo "document.getElementById('cbo_gmt_item').value  = '".($inf[csf("gmts_item_id")])."';\n";
		
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		echo "document.getElementById('txt_style_id').value  = '".($inf[csf("style_no")])."';\n";
		echo "document.getElementById('cbo_level_type_id').value  = '".($inf[csf("level_type_id")])."';\n";
		echo "document.getElementById('cbo_design_type').value  = '".($inf[csf("design_type")])."';\n";
		echo "document.getElementById('set_breck_down').value  = '".($inf[csf("set_break_down")])."';\n";
		echo "document.getElementById('cbo_product_department').value  = '".($inf[csf("product_department_id")])."';\n";
		echo "document.getElementById('cbo_buyer_brand_id').value  = '".($inf[csf("buyer_brand_id")])."';\n";
		echo "load_drop_down( 'requires/style_ref_controller', document.getElementById('cbo_buyer_name').value, 'load_drop_down_buyer_division', 'division_td' );\n"; 
		echo "document.getElementById('txt_division').value  = '".($inf[csf("division")])."';\n";

		echo "load_drop_down( 'requires/style_ref_controller', document.getElementById('txt_division').value, 'load_drop_down_division_department', 'department_td' );\n";
		echo "document.getElementById('cbo_department_id').value  = '".($inf[csf("department_id")])."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_style_ref_info',1);\n";
		$inQuotation=sql_select( "select id from  wo_price_quotation where style_ref='".($inf[csf("STYLE_REF_NAME")])."'" );
		$inOrder=sql_select( "select id from  wo_po_details_master where style_ref_no='".($inf[csf("STYLE_REF_NAME")])."'" );
		$inSampleReq=sql_select( "select id from  sample_development_mst where style_ref_no='".($inf[csf("STYLE_REF_NAME")])."'" );
		if(count($inQuotation)>0 || count($inOrder)>0 || count($inSampleReq)>0){
			echo "$('#txt_style_name').attr('disabled','true')".";\n";
			echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
			echo "$('#cbo_status').attr('disabled','true')".";\n";
		}
		else{
			echo "$('#txt_style_name').removeAttr('disabled')".";\n";
			echo "$('#cbo_buyer_name').removeAttr('disabled')".";\n";
			echo "$('#cbo_status').removeAttr('disabled')".";\n";
		}
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
 	// echo "10**<pre>";
 	// print_r($process);
 	// echo "</pre>";die;
	extract(check_magic_quote_gpc( $process ));

	// echo $txt_offer_qnty;die;

	if(empty($txt_offer_qnty)){
		$txt_offer_qnty = 0;
	}

	if($db_type==0)
	{
		$year_cond=" and YEAR(insert_date)";	
	}
	else if($db_type==2)
	{
		$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
	}

	if ($operation==0)  // Insert Here
	{

		if (is_duplicate_field( "style_ref_name", "lib_style_ref", "style_ref_name=$txt_style_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; disconnect($con);die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			check_table_status( $_SESSION['menu_id'],1);
			$id=return_next_id( "id", "lib_style_ref", 0 ) ;

			if($db_type==0)
			{
				$year_cond=" and YEAR(insert_date)";	
			}
			else if($db_type==2)
			{
				$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
			}
			$buyer_txt='';
			$sql_buy=sql_select("select short_name from lib_buyer where status_active=1 and is_deleted=0 and id=$cbo_buyer_name");
			if(count($sql_buy))
			{
				$buyer_txt=$sql_buy[0][csf('short_name')];
			}
			
			
			$buyer_brand_txt='';
			$sql_buy_brand=sql_select("select brand_name from lib_buyer_brand where status_active=1 and is_deleted=0 and id=$cbo_buyer_brand_id");
			if(count($sql_buy_brand))
			{
				$buyer_brand_txt=$sql_buy_brand[0][csf('brand_name')];
			}
			

			$new_wo_number=explode("*",return_mrr_number( '', '', $buyer_brand_txt, '', 5, "select id,style_number_prefix,style_number_prefix_num 
			from  lib_style_ref where status_active=1 and is_deleted=0 and buyer_brand_id=$cbo_buyer_brand_id and short_name='$txt_style_short_name.'  order by id desc ", "style_number_prefix", "style_number_prefix_num" ));

			$num = $new_wo_number[2];
			$new_mrr_fin = $buyer_brand_txt.'-'.$txt_style_short_name.'-'. str_pad($num, 5, 0, STR_PAD_LEFT);
            $style_prefix = $buyer_brand_txt.'-'.$txt_style_short_name;
			//echo  $style_prefix; die;
			
			$field_array="id,style_number_prefix,style_number_prefix_num,style_no,style_ref_name,short_name,buyer_id,order_uom,gmts_item_id,department_id,buyer_brand_id,level_type_id,design_type,product_department_id,division,set_break_down,total_set_qnty,set_smv,offer_qnty,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id. ",'" . $style_prefix . "'," . $new_wo_number[2] . ",'" . $new_mrr_fin. "','" .$txt_style_name. "','" .$txt_style_short_name."',".$cbo_buyer_name.",".$cbo_order_uom.",'".str_replace("'", "", $cbo_gmt_item)."',".$cbo_department_id.",'".$cbo_buyer_brand_id."','".$cbo_level_type_id."','".$cbo_design_type."','".$cbo_product_department."','".$txt_division."','".str_replace("'", "", $set_breck_down)."','".str_replace("'", "", $tot_set_qnty)."','".str_replace("'", "", $txt_sew_smv)."',".$txt_offer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			// echo "insert into lib_style_ref($field_array)values".$data_array;die;
			

			$field_array1="id, style_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
			$id1=return_next_id( "id", "  WO_STYLE_SET_DETAILS", 1 );
			$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
			for($c=0;$c < count($set_breck_down_array);$c++)
			{
				$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."')";
				$add_comma++;
				$id1=$id1+1;
			}
			//echo $data_array;die;
			$rID = sql_insert("lib_style_ref", $field_array, $data_array,1);

			$rID1 = sql_insert("WO_STYLE_SET_DETAILS", $field_array1, $data_array1,1);
			check_table_status( $_SESSION['menu_id'],0);
			//echo "10**=".$rID.'='.$rID1.'=';die;
			if($db_type==0)
			{
				if($rID && $rID1){
					mysql_query("COMMIT");
					echo "0**".$rID."**".$buyer_txt;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID."**".$rID1."**".$buyer_txt;
				}
			}

			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID1)
				{
					oci_commit($con);
					echo "0**".$rID."**".$buyer_txt;
				}
			else
				{
					oci_rollback($con);
					echo "10**".$rID."**".$rID1."**".$buyer_txt;
				}

			}
			disconnect($con);
			die;
		}
	}

	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "style_ref_name", "lib_style_ref", "style_ref_name=$txt_style_name  and id!=$update_id and is_deleted=0 and department_id=$cbo_department_id" ) == 1)
		{
			echo "11**0"; disconnect($con);die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			//$field_array="id,style_number_prefix,style_number_prefix_num,style_no,style_ref_name,buyer_id,buyer_brand_id,level_type_id,design_type,product_department_id,division,inserted_by,insert_date,status_active,is_deleted";
			


			$field_array="style_ref_name*short_name*buyer_id*order_uom*gmts_item_id*department_id*buyer_brand_id*level_type_id*design_type*product_department_id*division*set_break_down*total_set_qnty*set_smv*offer_qnty*updated_by*update_date*status_active*is_deleted";
			$data_array="'".str_replace("'", "", $txt_style_name)."'*'".str_replace("'", "", $txt_style_short_name)."'*'".str_replace("'", "", $cbo_buyer_name)."'*'".str_replace("'", "", $cbo_order_uom)."'*'".str_replace("'", "", $cbo_gmt_item)."'*'".str_replace("'", "", $cbo_department_id)."'*'".str_replace("'", "", $cbo_buyer_brand_id) ."'*'". str_replace("'", "", $cbo_level_type_id)."'*'".str_replace("'", "", $cbo_design_type) ."'*'". str_replace("'", "", $cbo_product_department) ."'*'".str_replace("'", "", $txt_division )."'*'".str_replace("'", "", $set_breck_down )."'*'".str_replace("'", "", $tot_set_qnty )."'*'".str_replace("'", "", $txt_sew_smv )."'*'".str_replace("'", "", $txt_offer_qnty)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";

			$rID=sql_update("lib_style_ref",$field_array,$data_array,"id","".$update_id."",1);

			$field_array1="id, style_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
			$add_comma=0;
			$id1=return_next_id( "id", "  WO_STYLE_SET_DETAILS", 1 ) ;
			$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
			for($c=0;$c < count($set_breck_down_array);$c++)
			{
				$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.", ".$update_id.", '".$set_breck_down_arr[0]."', '".$set_breck_down_arr[1]."', '".$set_breck_down_arr[2]."', '".$set_breck_down_arr[3]."', '".$set_breck_down_arr[4]."')";
				$add_comma++;
				$id1=$id1+1;
				//$item_ids.=$set_breck_down_arr[0].',';
			}

			$rID1=execute_query( "delete from WO_STYLE_SET_DETAILS where style_id =".$update_id."",0);
			$rID2=sql_insert("WO_STYLE_SET_DETAILS", $field_array1, $data_array1,1);

			if($db_type==0)
			{
				if($rID && $rID1 && $rID2){
					mysql_query("COMMIT");
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK");
					echo "10**".$rID."**".$rID1 ."**". $rID2;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			if($rID && $rID1 && $rID2)
			    {
					oci_commit($con);
					echo "1**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID."**".$rID1 ."**". $rID2;
				}
			}
			disconnect($con);
			die;
		}

	}
	else if ($operation==2)   // Delete Here
	{


		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=sql_delete("lib_style_ref", $field_array, $data_array, "id", "".$update_id."", 1);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		}

		if($db_type==2 || $db_type==1 )
			{
			if($rID )
			    {
					oci_commit($con);
					echo "1**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
	}
}
if($action=="open_set_list_view")
{
  echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
  extract($_REQUEST);
  //echo $set_smv_id;
  ?>
  <script>

  var set_smv_id='<? echo $set_smv_id; ?>';
  function add_break_down_set_tr( i )
  {
    var unit_id= document.getElementById('unit_id').value;
    if(unit_id==1)
    {
      alert('Only One Item');
      return false;
    }
    var row_num=$('#tbl_set_details tr').length-1;
    if (row_num!=i)
    {
      return false;
    }

    if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
    {
      return;
    }
    else
    {
      i++;

       $("#tbl_set_details tr:last").clone().find("input,select,a").each(function() {
        $(this).attr({
          'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
          'name': function(_, name) { return name + i },
          'value': function(_, value) { return value }
        });
        }).end().appendTo("#tbl_set_details");

        $('#cboitem_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);check_smv_set("+i+");check_smv_set_popup("+i+");");

        $('#txtsetitemratio_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
        $('#smv_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");

        $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
        $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
        $('#cboitem_'+i).val('');
        set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
        set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
    }
  }

  function fn_delete_down_tr(rowNo,table_id)
  {
    if(table_id=='tbl_set_details')
    {
      var numRow = $('table#tbl_set_details tbody tr').length;
      if(numRow==rowNo && rowNo!=1)
      {
        $('#tbl_set_details tbody tr:last').remove();
      }
      /*else
      {
      } */
       //set_all_onclick();
       set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
       set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
        //set_sum_value( 'cons_sum', 'cons_'  );
        //set_sum_value( 'processloss_sum', 'processloss_'  );
        //set_sum_value( 'requirement_sum', 'requirement_');
            //set_sum_value( 'pcs_sum', 'pcs_');
    }
  }

  function calculate_set_smv(i)
  {
    var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
    var smv=document.getElementById('smv_'+i).value;
    var set_smv=txtsetitemratio*smv;
    document.getElementById('smvset_'+i).value=set_smv;
    set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
    set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
  }

  function set_sum_value_set(des_fil_id,field_id)
  {
    var rowCount = $('#tbl_set_details tr').length-1;
    math_operation( des_fil_id, field_id, '+', rowCount );
  }

  function set_sum_value_smv(des_fil_id,field_id)
  {
    var rowCount = $('#tbl_set_details tr').length-1;
    var ddd={ dec_type:1, comma:0, currency:1}
    math_operation( des_fil_id, field_id, '+', rowCount,ddd );
    //math_operation( des_fil_id, field_id, '+', rowCount );
  }

  function js_set_value_set()
  {
    var rowCount = $('#tbl_set_details tr').length-1;
    var set_breck_down="";
    var item_id=""
    for(var i=1; i<=rowCount; i++)
    {
      if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i+'*smv_'+i,'Gmts Items*Set Ratio*Smv')==false)
      {
        return;
      }
      if($('#hidquotid_'+i).val()=='') $('#hidquotid_'+i).val(0);
      if(set_breck_down=="")
      {
        set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#hidquotid_'+i).val();
        item_id+=$('#cboitem_'+i).val();
      }
      else
      {
        set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#hidquotid_'+i).val();
        item_id+=","+$('#cboitem_'+i).val();
      }

    }
    document.getElementById('set_breck_down').value=set_breck_down;
    document.getElementById('item_id').value=item_id;

    parent.emailwindow.hide();
  }

  function check_duplicate(id,td)
  {
    var item_id=(document.getElementById('cboitem_'+id).value);
    var row_num=$('#tbl_set_details tr').length-1;
    for (var k=1;k<=row_num; k++)
    {
      if(k==id)
      {
        continue;
      }
      else
      {
        if(item_id==document.getElementById('cboitem_'+k).value)
        {
          alert("Same Gmts Item Duplication Not Allowed.");
          document.getElementById(td).value="0";
          document.getElementById(td).focus();
        }
      }
    }
  }

  function check_smv_set(id)
  {
    var smv=(document.getElementById('smv_'+id).value);
    var row_num=$('#tbl_set_details tr').length-1;
    //alert(item_id);
    var txt_style_ref='<? echo $txt_style_ref ?>';

    var item_id=$('#cboitem_'+id).val();
    //alert(td);
    //get_php_form_data(company_id,'set_smv_work_study','requires/style_ref_controller' );
    var response=return_global_ajax_value(txt_style_ref+"**"+item_id, 'set_smv_work_study', '', 'style_ref_controller');
    var response=response.split("_");
    if(response[0]==1)
    {
      if(set_smv_id==1)
      {
        $('#smv_'+id).val(response[1]);
        $('#tot_smv_qnty').val(response[1]);
        /*for (var k=1;k<=row_num; k++)
        {
          $('#smv_'+k).val(response[1]);
        }*/
      }
    }
  }

  function check_smv_set_popup(id)
  {
    var smv=(document.getElementById('smv_'+id).value);
    var row_num=$('#tbl_set_details tr').length-1;

    var txt_style_ref='<? echo $txt_style_ref ?>';
    var cbo_company_name='<? echo $cbo_company_name ?>';
    var cbo_buyer_name='<? echo $cbo_buyer_name ?>';
    var item_id=$('#cboitem_'+id).val();
      //alert(set_smv_id);
    if(set_smv_id==4 || set_smv_id==6)
    {
      $('#smv_'+id).val('');
      $('#tot_smv_qnty').val('');
      $('#hidquotid_'+id).val('');

      var page_link="style_ref_controller.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&id="+id+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name;
    }
    else
    {
      return;
    }

    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'SMV Pop Up', 'width=650px,height=220px,center=1,resize=1,scrolling=0','../../')
    emailwindow.onclose=function()
    {
      var theform=this.contentDoc.forms[0];
      var selected_smv_data=this.contentDoc.getElementById("selected_smv").value;
      var smv_data=selected_smv_data.split("_");
      var row_id=smv_data[3];

      $("#smv_"+row_id).val(smv_data[0]);
      $("#smv_"+row_id).attr('readonly','readonly');
      $("#hidquotid_"+row_id).val(smv_data[4]);

      calculate_set_smv(row_id);
    }
  }
  </script>
  </head>
  <body>
         <div id="set_details"  align="center">
        <fieldset>
            <form id="setdetails_1" autocomplete="off">
              <input type="hidden" id="set_breck_down" />
              <input type="hidden" id="item_id" />
              <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />

              <table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                    <thead>
                        <tr>
                            <th width="250">Item</th><th width="80">Set Item Ratio</th><th width="80">SMV/Pcs</th><th width=""></th>
                          </tr>
                      </thead>
                      <tbody>
                      <?

            $data_array=explode("__",$set_breck_down);
            if($data_array[0]=="")
            {
              $data_array=array();
            }
            if ( count($data_array)>0)
            {
              $i=0;
              foreach( $data_array as $row )
              {
                $i++;
                $data=explode('_',$row);
                $gmt_item_id_s=$data[0];
                if(empty($gmt_item_id_s))
                {
                  $gmt_item_id_s=$item_id;
                }
                

                ?>
                    <tr id="settr_1" align="center">
                          <td>
                          <?
                          echo create_drop_down( "cboitem_".$i, 250, $garments_item, "",1," -- Select Item --", $gmt_item_id_s, "check_duplicate(".$i.",this.id ); check_smv_set(".$i."); check_smv_set_popup(".$i.");",'','' );
                          ?>

                          </td>
                          <td>
                          <input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:70px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[1] ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> />
                          </td>

                         <td>
                          <input type="text" id="smv_<? echo $i;?>"   name="smv_<? echo $i;?>" style="width:70px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[2] ?>" />
                          <input type="hidden" id="smvset_<? echo $i;?>"   name="smvset_<? echo $i;?>" style="width:70px"  class="text_boxes_numeric"  value="<? echo $data[3] ?>" />
                          </td>
                          <td>
                          <input type="hidden" id="hidquotid_<? echo $i;?>" name="hidquotid_<? echo $i;?>" style="width:30px" class="text_boxes_numeric" value="<? echo $data[4]; ?>" readonly/>
                          <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?> )" />
                          <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" />
                           </td>
                      </tr>
                  <?
              }
            }
            else
            {
               //$sql=sql_select("select a.id,a.item_name from sample_development_mst a,sample_development_dtls b where  a.quotation_id='$txt_inquery_id' and  a.id=b.sample_mst_id");

               $item_name = return_field_value("item_name" ," sample_development_mst","quotation_id='$txt_inquery_id'");
               $gmt_item_id_s=$item_name;
              if(empty($gmt_item_id_s))
              {
                $gmt_item_id_s=$item_id;
              }

              ?>
              <tr id="settr_1" align="center">
                     <td>
                      <?
                      echo create_drop_down( "cboitem_1", 240, $garments_item, "",1,"--Select--", $gmt_item_id_s, 'check_duplicate(1,this.id ); check_smv_set(1); check_smv_set_popup(1);','','' );
                      ?>
                      </td>
                       <td>
                      <input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:70px" class="text_boxes_numeric" onChange="calculate_set_smv(1)" value="<? if ($unit_id==1) {echo "1";} else{echo "";}?>"  <? if ($unit_id==1){echo "readonly";} else{echo "";}?>  />
                       </td>
                       <td>
                      <input type="text" id="smv_1"   name="smv_1" style="width:70px"  class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="<? //echo $smv_pcs_precost; ?>" />
                      <input type="hidden" id="smvset_1"   name="smvset_1" style="width:70px"  class="text_boxes_numeric"  value="<? //echo $smv_set_precost; ?>" />
                      </td>
                      <td>
                      <input type="hidden" id="hidquotid_1" name="hidquotid_1" style="width:30px" class="text_boxes_numeric" value="" readonly/>
                      <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                      <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                      </td>
                </tr>
              <?
            }
            ?>
                  </tbody>
                  </table>
                  <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                  <tfoot>
                        <tr>
                              <th width="250">Total</th>
                              <th  width="80"><input type="text" id="tot_set_qnty" name="tot_set_qnty"  class="text_boxes_numeric" style="width:70px"  value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly  /></th>
                                <th  width="80">
                                  <input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:70px"  value="<? //if($tot_smv_qnty !=''){ echo $tot_smv_qnty;} else{ echo 1;} ?>" readonly />
                              </th>
                              <th width=""></th>
                          </tr>
                      </tfoot>
                  </table>

                  <table width="800" cellspacing="0" class="" border="0">

                    <tr>
                          <td align="center" height="15" width="100%"> </td>
                      </tr>
                    <tr>
                          <td align="center" width="100%" class="button_container">

                      <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/>

                          </td>
                      </tr>
                  </table>

              </form>
          </fieldset>
          </div>
   </body>
  <script>
    set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
    set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
  </script>
  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  </html>
  <?
  exit();
}
if($action=="open_smv_list")
{
  echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
  extract($_REQUEST);

  $item_id=$item_id;
  $style_id=$txt_style_ref;
  $set_smv_id=$set_smv_id;
  $row_id=$id;
  $set_smv_id=$set_smv_id;
  $cbo_buyer_name=$cbo_buyer_name;
  $cbo_company_name=$cbo_company_name;
  //echo $cbo_company_name;
  ?>
  <script type="text/javascript">
      function js_set_value(id)
      {   //alert(id);
      document.getElementById('selected_smv').value=id;
      parent.emailwindow.hide();
      }
    </script>

    </head>
    <body>
    <div align="center" style="width:100%;" >
  <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="400" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th width="150">Buyer Name</th>
                <th width="100">Style Ref </th>
                <th>
                    <input type="hidden" id="selected_job">
                    <input type="hidden" id="item_id" value="<?  echo $item_id;?>">
                    <input type="hidden" id="row_id" value="<?  echo $row_id;?>">
                    <input type="hidden" id="company_id" value="<?  echo $cbo_company_name;?>">
                &nbsp;</th>
            </thead>
            <tr>
                <td id=""><? echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px" value="<? echo $txt_style_ref;?>" disabled></td>
                <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('item_id').value+'_'+document.getElementById('row_id').value, 'create_item_smv_search_list_view', 'search_div', 'style_ref_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
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
if($action=="create_item_smv_search_list_view")
{
  $data=explode('_',$data);
  $company=$data[0];
  $buyer_id=$data[1];
  $style=$data[2];
  $item_id=$data[3];
  $row_id=$data[4];

  //if ($company!=0) $company_con=" and a.company_id='$company'";else $company_con="";
  if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'";else $buyer_id_con="";
  if ($style!="") $style_con=" and a.style_ref ='$style'";else $style_con="";
  if ($item_id!=0) $gmts_item_con=" and a.gmts_item_id='$item_id'";else $gmts_item_con="";
  if ($item_id!=0) $gmts_item_con2=" and a.gmt_item_id='$item_id'";else $gmts_item_con2="";
  ?>
  <input type="hidden" id="selected_smv" name="selected_smv" />
  <?
  $sewing_sql="select a.id as lib_sewing_id, a.gmt_item_id, a.bodypart_id, a.operation_name, a.department_code as dcode from lib_sewing_operation_entry a where a.is_deleted=0 $gmts_item_con2  order by a.id Desc";
  $result = sql_select($sewing_sql);
  foreach($result as $row)
  {
    $code_smv_arr[$row[csf('lib_sewing_id')]]['dcode']=$row[csf('dcode')];
    $code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('bodypart_id')]]['operation_name']=$row[csf('operation_name')];
  }
  // print_r($code_smv_arr);b.lib_sewing_id
  if($db_type==0)
  {
    $group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
    $id_group_con="group_concat(a.id)";
  }
  else
  {
    $group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
    $id_group_con="listagg(a.id,',') within group (order by a.id)";
  }

  $sql="select a.id, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 $gmts_item_con $style_con $buyer_id_con
  order by id DESC";

  $sql_result=sql_select($sql);
  foreach($sql_result as $row)
  {
    //$operation_name=$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('body_part_id')]]['operation_name'];
    $smv_dtls_arr['str']['style_ref']=$row[csf('style_ref')];
    $smv_dtls_arr['str']['operation_count']=$row[csf('operation_count')];
    $smv_dtls_arr['str']['id'].=$row[csf('id')].',';
    //$smv_dtls_arr[$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
    $smv_dtls_arr['str']['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
    //$smv_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
    //$smv_dtls_arr[$row[csf('id')]]['operation_name']=$operation_name;
    $code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
    $smv=0;
    $smv=$row[csf('operator_smv')]+$row[csf('helper_smv')];

    $smv_sewing_arr[$code_id][$row[csf('lib_sewing_id')]]['operator_smv']+=$smv;
  }
  //print_r($smv_dtls_arr);
  ?>
  <table width="600" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Sys. ID.</th>
                <th width="200">Style</th>
                <th width="60">Avg. Sewing SMV</th>
                <th width="60">Avg. Cuting SMV</th>
                <th width="60">Avg. Finish SMV</th>
                <th>No of Operation</th>
            </tr>
        </thead>
        <tbody id="list_view">
        <?
        $i=1;
    foreach($smv_dtls_arr as $arrdata)
    {
      if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
      $lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
      $lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));

      $finish_smv=$cut_smv=$sewing_smv=0;
      foreach($lib_sewing_ids as $lsid)
      {
        $finish_smv+=$smv_sewing_arr[4][$lsid]['operator_smv'];
        $cut_smv+=$smv_sewing_arr[7][$lsid]['operator_smv'];
        $sewing_smv+=$smv_sewing_arr[8][$lsid]['operator_smv'];
      }
      $sys_id=rtrim($arrdata['id'],',');
      $ids=array_filter(array_unique(explode(",",$sys_id)));
      //print_r($ids);
      $id_str=""; $k=0;
      foreach($ids as $idstr)
      {
        if($id_str=="") $id_str=$idstr; else $id_str.=','.$idstr;
        $k++;
      }
      $finish_smv=$finish_smv/$k;
      $cut_smv=$cut_smv/$k;
      $sewing_smv=$sewing_smv/$k;

      $data=$sewing_smv."_".$cut_smv."_".$finish_smv."_".$row_id."_".$id_str;
      ?>
      <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
                <td width="30"><? echo $i;//.'='.$k ?></td>
                <td width="140" style="word-break:break-all"><? echo $id_str; ?></td>
                <td width="160" style="word-break:break-all"><? echo $arrdata['style_ref']; ?></td>
                <td width="60" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
                <td width="60" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
                <td width="60" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
                <td><p><? echo $arrdata['operation_count']; ?></p></td>
      </tr>
      <?
      $i++;
    }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
            </tr>
        </tfoot>
  </table>
  <?
  exit();
}



if($action == 'send_auto_mail'){
  list($mst_id,$mail,$mail_body) = explode('**',$data);
  $sql = "select ID,STYLE_NUMBER_PREFIX,STYLE_NUMBER_PREFIX_NUM,STYLE_NO,STYLE_REF_NAME,SHORT_NAME,BUYER_ID,ORDER_UOM,GMTS_ITEM_ID,DEPARTMENT_ID,BUYER_BRAND_ID,LEVEL_TYPE_ID,DESIGN_TYPE,PRODUCT_DEPARTMENT_ID,DIVISION,SET_BREAK_DOWN,TOTAL_SET_QNTY,SET_SMV,OFFER_QNTY,INSERTED_BY,INSERT_DATE FROM LIB_STYLE_REF where id=$mst_id and status_active =1 and is_deleted = 0";
  //echo $sql;
  $sql_result = sql_select($sql);
  $row = $sql_result[0];

  $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and id=".$row['BUYER_ID']."","id","buyer_name");
  $user_arr = return_library_array("select id, USER_NAME from USER_PASSWD where status_active=1 and id=".$row['INSERTED_BY']."","id","USER_NAME");
 
  ob_start();
  ?>

  <table border="1" rules="all">
  <tr>
      <td colspan="2"><strong>Style ref entry Auto mail details</strong></td>
    </tr>
    <tr>
      <td>Buyer Name</td><td><?= $buyer_arr[$row['BUYER_ID']];?></td>
    </tr>
    <tr>
      <td>Style Name</td><td><?= $row['STYLE_REF_NAME'];?></td>
    </tr>
    <tr>
      <td>Garments Item</td><td><?= $garments_item[$row['GMTS_ITEM_ID']];?></td>
    </tr>
    <tr>
      <td>Offer Qnty</td><td><?= $row['OFFER_QNTY'];?></td>
    </tr>
    <tr>
      <td>Insert date & time</td><td><?= $row['INSERT_DATE'];?></td>
    </tr>
    <tr>
      <td>User Name</td><td><?= $user_arr[$row['INSERTED_BY']];?></td>
    </tr>
  </table>



  <?

  $mailBody = ob_get_contents();
  ob_clean();
  
    include('../../../auto_mail/setting/mail_setting.php');

    //Att file....
    $imgSql="select IMAGE_LOCATION,REAL_FILE_NAME from common_photo_library where is_deleted=0  and MASTER_TBLE_ID='$mst_id' and FORM_NAME='style_ref_entry'";
    $imgSqlResult=sql_select($imgSql);
    foreach($imgSqlResult as $rows){
      $att_file_arr[]='../../../'.$rows['IMAGE_LOCATION'].'**'.$rows['REAL_FILE_NAME'];
    }

    $sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=139 and b.mail_user_setup_id=c.id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=5 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql=sql_select($sql);
    //echo $sql;die;
		
		if($mail){$toArr=array($mail);}
    else{$toArr=array();}
		foreach($mail_sql as $row)
		{
			$toArr[$row['EMAIL_ADDRESS']]=$row['EMAIL_ADDRESS'];
		}

    $to = implode(',',$toArr);
    //echo $to;die;
    $subject="Style ref entry Auto mail details";
    $header=mailHeader();
    echo sendMailMailer( $to, $subject, $mailBody."<br>".$mail_body, $from_mail,$att_file_arr );
    exit();
}

?>

