<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//--------------------------------array library------------------------------------------------------------
$sample_library=return_library_array( "select id, sample_name from  lib_sample", "id", "sample_name"  );

if($action=="load_drop_down_sent")
{
	$data = explode("_",$data);
	if($data[0]==1)
	{
	 echo create_drop_down( "cbo_out_company", 170, "select id,buyer_name from  lib_buyer  where status_active=1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","0" );
    }
	else if($data[0]==2)
	{
	 echo create_drop_down( "cbo_out_company", 170, "select id,supplier_name from  lib_supplier  where status_active=1 and is_deleted=0  order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected,"","0" );
	}
     else if($data[0]==3)
	{
	 echo create_drop_down( "cbo_out_company", 170, "select id,other_party_name from  lib_other_party where status_active=1 and is_deleted=0  order by other_party_name","id,other_party_name", 1, "-- Select Other Party --", $selected,"","0" );
	}
	
	exit();
}
if ($action=="load_drop_down_out_location")
{
	echo create_drop_down( "cbo_out_location_id", 170, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}
if ($action=="load_drop_down_com_location")
{
	 
	echo create_drop_down( "cbo_com_location_id", 170, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}


if($action=="load_drop_down_dying_source")
{
	
	$data = explode("_",$data);
	//print_r($data);die;
	$basis_id=$data[1];
	$sql_issue_dtls="select knit_dye_source,knit_dye_company,issue_purpose from inv_issue_master where  issue_number='$data[0]' and status_active=1 and is_deleted=0";
	$res = sql_select($sql_issue_dtls);
	foreach($res as $row)
	{
		$dying_source=$row[csf("knit_dye_source")];
		$dying_company=$row[csf("knit_dye_company")];
		$issue_purpose=$row[csf("issue_purpose")];
	}
	if( $basis_id==3)
	{
		if($dying_source==1)
		echo create_drop_down( "cbo_out_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select --", $company, "" );
		else if($dying_source==3 && $issue_purpose==1)
		echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		else if($dying_source==3 && $issue_purpose==2)
		echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,21,24) and a.status_active=1 group by a.id order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		else if($dying_source==3)		
		echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		else
		echo create_drop_down( "cbo_out_company", 170, $blank_array,"", 1, "-- Select --", 0, "",0 );
	}
	else if( $basis_id==4)
	{
		if($dying_source==1 || $dying_source==3)
		{
			echo create_drop_down( "cbo_out_company", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "$company_id", "","" );
		}
		/*else if($dying_source==3)
		{
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=21 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Sewing Company--", 1, "" );
		}*/
		else
		{
			echo create_drop_down( "cbo_out_company", 170, $blank_array,"",1, "--Select Sewing Company--", 1, "" );
		}
	
	}
	else if($basis_id==6)
	{
		echo create_drop_down( "cbo_out_company", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "$company_id", "","" );
	}
	
	else if( $basis_id==2)
	{
		
		
		//echo $dying_source.'==='.$issue_purpose;die;
		if($dying_source==1)
		
			echo create_drop_down( "cbo_out_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select --", $company, "" );
		
		else if($dying_source==3 && $issue_purpose==1)
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		else if($dying_source==3 && $issue_purpose==2)
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,21,24) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		else if($dying_source==3)		
			echo create_drop_down( "cbo_out_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		else if($dying_source==0)		
			echo create_drop_down( "cbo_out_company", 170, $blank_array,"", 1, "-- Select --", $selected, "","","" );	
	}
	else if($basis_id==5 || $basis_id==7 )
	{
		echo create_drop_down( "cbo_out_company", 170, "select id,company_name from lib_company  where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $selected,"","0" );
	}
	else
	{
		echo create_drop_down( "cbo_out_company", 170, "select id,company_name from lib_company  where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0,"",1 );
	}
	
	//$sql = "select department_id,section,within_group,sent_by,sent_to,challan_no,basis from inv_gate_pass_mst where sys_number='$data'";
}


//load drop down supplier
	if ($action=="load_drop_down_supplier")
	{	  
		echo create_drop_down( "cbo_supplier", 170, "select a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type=20 and c.tag_company=$data and a.status_active=1 and
		a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );  	 
		exit();
	}

 
//wo/pi popup here----------------------// 
if ($action=="piworeq_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(str)
	{
		//master part call here
		$("#hidden_tbl_id").val(str);
		parent.emailwindow.hide(); 
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="860" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="150">Company Name</th>
                    <th width="150" align="center" id="search_by_td_up">Basis</th>
                     <th width="60" align="center">Gate Out ID</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?  
                             echo create_drop_down( "cbo_company_id", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>
                    </td>
                    <td width="250" align="center" id="search_by_td">
						<? 
                       // $get_pass_basis=array(1=>"Independent",2=>"Challan(Yarn)",3=>"Challan(Gray Fabric)",4=>"Challan(Finish Fabric)",5=>"Challan(General Item)",6=>"Challan(Trims)",6=>"Challan(Dyes & Chemical)",7=>"Challan(Trims)");
                            echo create_drop_down( "cbo_basis", 150, $get_pass_basis,"",1, "-- Select --", 0, "" ); 
                        ?>
                    </td>  
                    <td>
                     <input type="text"  class="text_boxes" id="gate_out_id" name="gate_out_id" style="width:60"/>
                    </td>  
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                     </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_basis').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('gate_out_id').value, 'create_wopireq_search_list_view', 'search_div', 'material_goods_parking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                    <input type="hidden" id="hidden_tbl_id" value="" />
                    <!-- ---------END-------------> 
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}



if($action=="create_wopireq_search_list_view")
{
	
 	$ex_data = explode("_",$data);
	$txt_basis = str_replace("'","",$ex_data[1]);
	
	$txt_date_from =str_replace("'","",$ex_data[2]);
	$txt_date_to = str_replace("'","",$ex_data[3]);
	$company = str_replace("'","",$ex_data[0]);
	$gate_out_id = str_replace("'","",$ex_data[4]);
  
	if($company!=0) $com_cond= " and b.company_id=$company "; else $com_cond="";
	if($txt_basis!=0) $basis_cond= " and b.basis=$txt_basis "; else $basis_cond="";
	if($gate_out_id!=0) $gate_out_id_cond= " and b.sys_number_prefix_num=$gate_out_id "; else $gate_out_id_cond="";
	$sql_cond="";	

	if($db_type==0)
		{
		if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.out_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
	if($db_type==2)
		{
		if( $txt_date_from!="" || $txt_date_to!="" ) $sql_cond .= " and a.out_date  between '".change_date_format($txt_date_from,'mm-dd-yyyy','-',1)."' and '".change_date_format($txt_date_to,'mm-dd-yyyy','-',1)."'";
		}
		
	/*$sql_gate=sql_select("select gate_pass_no as sys_number from inv_gate_in_mst where  gate_pass_no is not null and status_active=1 and is_deleted=0");
	$sys_number="";
	foreach($sql_gate as $row)
	{
		if($sys_number!="") $sys_number.=","."'".$row[csf('sys_number')]."'";
		else $sys_number="'".$row[csf('sys_number')]."'";
	}
	//echo $sys_number;
	
		$sys_number_row=count(array_unique(explode(",",$sys_number)));
		//echo $issue;
		$gateIds=chop($sys_number,','); $gate_passIds_cond="";
		if($db_type==2 && $sys_number_row>1000)
		{
			$gate_passIds_cond=" and (";
			$gate_passIdsArr=array_chunk(explode(",",$gateIds),999);
			foreach($gate_passIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$gate_passIds_cond.=" b.sys_number not in($ids) or ";
			}
			$gate_passIds_cond=chop($gate_passIds_cond,'or ');
			$gate_passIds_cond.=")";
		}
		else
		{
			$gate_passIds_cond=" and  b.sys_number not in($gateIds)";
		}*/
	//echo $gate_passIds_cond;
	
 		 $sql = "select a.id as id,b.sys_number_prefix_num ,b.sys_number, b.company_id,b.basis ,a.out_date,b.challan_no 
				from inv_gate_out_scan a,inv_gate_pass_mst b
				where b.sys_number=a.gate_pass_id and	b.status_active=1 and b.is_deleted=0  and b.sys_number not in(select gate_pass_no as sys_number from inv_gate_in_mst where  gate_pass_no is not null and status_active=1 and is_deleted=0)  $com_cond $basis_cond $sql_cond $gate_out_id_cond  order by b.sys_number_prefix_num";
		
		


	 //$get_pass_basis=array(1=>"Independent",2=>"Challan(Yarn)",3=>"Challan(Gray Fabric)",4=>"Challan(Finish Fabric)",5=>"Challan(General Item)",6=>"Challan(Trims)",6=>"Challan(Dyes & Chemical)",7=>"Challan(Trims)");
	$result = sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$arr=array(0=>$company_library,2=>$get_pass_basis);
	echo create_list_view("list_view", "Company Name,Gate Out ID,Basis,Out Date,Challan No","150,150,170,100,180","800","230",0, $sql , "js_set_value", "sys_number", "", 1, "company_id,0,basis,0,0", $arr, "company_id,sys_number,basis,out_date,challan_no", "",'','0,0,0,0,0,0') ;	
	exit();	
	
}




if($action=="populate_main_from_data")
{
 		 $sql = "select department_id,section,attention,company_id,com_location_id,location_id,returnable,out_date,carried_by,est_return_date,within_group,sent_by,sent_to,challan_no,basis from inv_gate_pass_mst where sys_number='$data'";
		
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{	
        $chalan_no=$row[csf('challan_no')];
		// $sql_issue_dtls=sql_select("select knit_dye_source,knit_dye_company,issue_purpose from inv_issue_master where  issue_number='$chalan_no' and status_active=1 and is_deleted=0");
		// $dying_source=$sql_issue_dtls[0][csf('knit_dye_source')];
		
		 $basis=$row[csf('basis')];
		 $returnable=$row[csf('returnable')];
		 $within_group=$row[csf('within_group')];
  		echo "$('#cbo_department_name').val(".$row[csf("department_id")].");\n";
		echo "$('#cbo_section').val(".$row[csf("section")].");\n";
		echo "$('#txt_receive_from').val('".$row[csf("sent_by")]."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		echo "$('#txt_carried_by').val('".$row[csf("carried_by")]."');\n";
		echo "$('#cbo_returnable').val('".$row[csf("returnable")]."');\n";
		echo "$('#txt_out_date').val('".change_date_format($row[csf("out_date")])."');\n";
		echo "$('#cbo_company_name').val('".$row[csf("sent_to")]."');\n"; 
		
		echo "$('#txt_return_date').val('".change_date_format($row[csf("est_return_date")])."');\n";
		echo "load_drop_down( 'requires/material_goods_parking_controller','".$row[csf("sent_to")]."', 'load_drop_down_com_location', 'com_location_td' );";
		echo "$('#cbo_com_location_id').val(".$row[csf("location_id")].");\n";
		
		echo "load_drop_down( 'requires/material_goods_parking_controller','".$row[csf("company_id")]."', 'load_drop_down_out_location', 'out_location_td' );";
		echo "$('#cbo_out_location_id').val('".$row[csf("com_location_id")]."');\n";
			/*if($basis==1 && $returnable==1 && $within_group==2 )
			{
				echo "$('#cbo_company_name').attr('disabled',false);\n";
			}
			else
			{
				echo "$('#cbo_company_name').attr('disabled',true);\n";
			}*/
		/*if($basis==2)	
		{
			if($dying_source==1)
			{
		echo "load_drop_down( 'requires/material_goods_parking_controller','$chalan_no'+'_'+$basis, 'load_drop_down_dying_source', 'sent_td');\n";
			}
		}*/
		
		
		echo "load_drop_down( 'requires/material_goods_parking_controller','$chalan_no'+'_'+$basis, 'load_drop_down_dying_source', 'sent_td');\n";
		
		if($basis==4)
		{
			echo "$('#cbo_out_company').val('".$row[csf("company_id")]."');\n";
			
		} 
			
  	}
	
	exit();	
}


//right side product list create here--------------------//
if($action=="show_product_listview")
{ 
	
	$sql_gate=sql_select("select gate_pass_no as sys_number from inv_gate_in_mst where  gate_pass_no is not null and status_active=1 and is_deleted=0");
	$sys_number="";
	foreach($sql_gate as $row)
	{
		if($sys_number!="") $sys_number.=","."'".$row[csf('sys_number')]."'";
		else $sys_number="'".$row[csf('sys_number')]."'";
	}
	
		$sys_number_row=count(array_unique(explode(",",$sys_number)));
		//echo $issue;
		$gateIds=chop($sys_number,','); $gate_passIds_cond="";
		if($db_type==2 && $sys_number_row>1000)
		{
			$gate_passIds_cond=" and (";
			$gate_passIdsArr=array_chunk(explode(",",$gateIds),999);
			foreach($gate_passIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$gate_passIds_cond.=" b.sys_number not in($ids) or ";
			}
			$gate_passIds_cond=chop($gate_passIds_cond,'or ');
			$gate_passIds_cond.=")";
		}
		else
		{
			$gate_passIds_cond=" and  b.sys_number not in($gateIds)";
		}
	
	$tbl_row=0;
 	$sql = "select a.id,sample_id,a.item_category_id,a.item_description,a.quantity,a.uom,a.rate,a.amount,a.remarks,a.buyer_order  from  inv_gate_pass_dtls a ,inv_gate_pass_mst b where b.id=a.mst_id and  b.sys_number='$data' and  b.sys_number not in(select gate_pass_no as sys_number from inv_gate_in_mst where  gate_pass_no is not null and status_active=1 and is_deleted=0)
	and a.status_active=1 and a.is_deleted=0
	"; 
  
    $result=sql_select($sql);
	if(count($result)>0)
	{
	foreach($result as $row)
			{
			 $tbl_row++;
				?>
			 <tr class="" id="tr_<? echo $tbl_row; ?>" style="height:10px;">
                    <td>
						<? 
                            echo create_drop_down( "cboitemcategory_".$tbl_row, 120,$item_category,"",1, "-- Select --",$row[csf('item_category_id')] , "",1 ); 
                        ?>
                    </td>
                    <td>
                        <? 
						echo create_drop_down( "cbosample_".$tbl_row, 100, "select id,sample_name from lib_sample where status_active=1 order by sample_name","id,sample_name",1, "-- Select --", $row[csf('sample_id')],0,1 ); 
					    ?> 
                    </td>
                    <td><input type="text" name="txtitemdescription_<? echo $tbl_row; ?>" id="txtitemdescription_<? echo $tbl_row; ?>" class="text_boxes" style="width:200px;" value="<? echo $row[csf('item_description')];?>" disabled></td>
                    <td><input type="text" name="txtcalanquantity_<? echo $tbl_row; ?>" id="txtcalanquantity_<? echo $tbl_row; ?>" class="text_boxes_numeric" onKeyUp="fn_calculate_amount()"   value="<? echo $row[csf('quantity')];?>" style="width:60px;" disabled></td>
                    <td><input type="text" name="txtquantity_<? echo $tbl_row; ?>" id="txtquantity_<? echo $tbl_row; ?>" class="text_boxes_numeric"   value="" style="width:60px;"></td>
                    <td><? echo create_drop_down( "cbouom_".$tbl_row, 60, $unit_of_measurement,"", 1, "-- Select--", $row[csf('uom')], "",1 ); ?></td>
                    <!--<td><input type="text" name="txtuomqty_<? //echo $tbl_row; ?>" id="txtuomqty_<? //echo $tbl_row; ?>" class="text_boxes"   value="" style="width:60px;"></td>-->
                    <td><input type="text" name="txtrate_<? echo $tbl_row; ?>" id="txtrate_<? echo $tbl_row; ?>" class="text_boxes_numeric" onKeyUp="fn_calculate_amount()"  value="<? echo $row[csf('rate')];?>" style="width:60px" disabled></td>
                    <td><input type="text" name="txtamount_<? echo $tbl_row; ?>" id="txtamount_<? echo $tbl_row; ?>" class="text_boxes_numeric" style="width:80px"  value="<? echo $row[csf('amount')];?>" randomly disabled></td>
                    <td><input type="text" name="txtorder_<? echo $tbl_row; ?>" id="txtorder_<? echo $tbl_row; ?>" class="text_boxes" style="width:80px"      value="<? echo $row[csf('buyer_order')];?>" readonly disabled></td>
                    <td><input type="text" name="txtremarks_<? echo $tbl_row; ?>" id="txtremarks_<? echo $tbl_row; ?>" class="text_boxes" style="width:150px"    value="<? echo $row[csf('remarks')];?>">
                    <input type="hidden" id="updatedtlsid_<? echo $tbl_row; ?>" name="updatedtlsid_<? echo $tbl_row; ?>" value="" />
                      
                     </td>
                </tr>
				<?php
			$i++;
			}
	}
	else
	{
		echo "No Found Data";die;	
	}
}


  
  
if($action=="wo_pi_req_product_form_input")
{
	
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$product_name_details = $ex_data[1];
	$wo_pi_req_ID = $ex_data[2]; //pi,wo,req dtls table ID
 	$category = $ex_data[3];
	
	if($receive_basis==1) // pi basis
	{	
		$sql = "select uom,quantity,net_pi_rate as rate,amount from com_pi_item_details where id=$wo_pi_req_ID";
 	}  
	else if($receive_basis==2) // wo basis
	{
		$sql = "select uom,supplier_order_quantity as quantity,rate,amount from wo_non_order_info_dtls where id=$wo_pi_req_ID";
 	}
	else if($receive_basis==3) // requisition basis
	{
		$sql = "select cons_uom as uom,quantity,rate,amount from inv_purchase_requisition_dtls  where id=$wo_pi_req_ID";	
 	}	
	
 	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{ 
		echo "$('#txt_item_description').val('".$product_name_details."');\n";
		echo "$('#cbo_uom').val(".$row[csf("uom")].");\n";
		echo "$('#cbo_uom').attr('disabled',true);\n";
		echo "$('#txt_quantity').val(".$row[csf("quantity")].");\n";
		echo "$('#txt_rate').val('".number_format($row[csf("rate")],$dec_place[3],".","")."');\n";
		echo "$('#txt_amount').val('".number_format($row[csf("amount")],$dec_place[4],".","")."');\n";
  	}
	
	exit();	
}  
  
  


//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
   extract(check_magic_quote_gpc( $process )); 

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// validate condition for (gate pass>=get in)
		$validate_gate_pass=sql_select("select a.sys_number as pass_sys_number,sum(b.quantity) as pass_qty from  inv_gate_pass_mst a,inv_gate_pass_dtls b
where a.id=b.mst_id  group by a.sys_number");
		$validate_gate_in=sql_select("select c.gate_pass_no as gate_in_sys_number,sum(d.quantity) as gate_in_qty from  inv_gate_in_mst c,inv_gate_in_dtl d 
where c.id=d.mst_id group by c.gate_pass_no");
		$gate_pass_qty=0;
		$gate_in_qty=0;
		foreach($validate_gate_pass as $row)
		{
			$gate_pass_qty+=$row[csf("pass_qty")];
		}
			//echo $gate_pass_qty;		
		foreach($validate_gate_in as $row)
		{
			$gate_in_qty+=$row[csf("gate_in_qty")];
		}
			//echo $gate_in_qty;
			$txtquantity=0;
			for($i=1; $i<=$row_num; $i++)
			{
			$txtquantity="txtquantity_".$i;
			$txtquantity=str_replace("'","",$$txtquantity);
			$txtquantity+=$txtquantity;
			}
			 //echo "10**".$txtquantity; die;
			 $gate_in_qty_total=$gate_in_qty+$txtquantity;
			if($gate_pass_qty<$gate_in_qty_total)
			{
				 echo "30** Gate in Quantity is more than pass Quantity";
				 disconnect($con);exit();
			}
		//echo "select a.sys_number as pass_sys_number,sum(b.quantity) as pass_qty,c.gate_pass_no as gate_in_sys_number,sum(d.quantity) as gate_in_qty from  inv_gate_pass_mst a,inv_gate_pass_dtls b,inv_gate_in_mst c,inv_gate_in_dtl d where a.sys_number=".$txt_pass_id." and a.id=b.mst_id and c.id=d.mst_id group by a.sys_number,c.gate_pass_no";

		if(str_replace("'","",$update_id)=="")
		{
			$id=return_next_id("id", "inv_gate_in_mst", 1);			
			if($db_type==2)
			{
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GIE', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from inv_gate_in_mst where company_id=$cbo_company_name and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id DESC ", "sys_number_prefix", "sys_number_prefix_num" ));
			} 
			if($db_type==0)
			{	
			$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GIE', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from inv_gate_in_mst where company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by id DESC ", "sys_number_prefix", "sys_number_prefix_num" ));
			}
				//update_id
			$field_array="id,sys_number_prefix,sys_number_prefix_num,sys_number,company_id,within_group,party_type,sending_company,
			receive_from,department_id,section,in_date,attention,com_location_id,challan_no,time_hour,time_minute,carried_by,pi_reference,entry_form,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$cbo_group.",".$cbo_party_type.",".$cbo_out_company.",".$txt_receive_from.",".$cbo_department_name.",".$cbo_section.",".$txt_in_date.",".$txt_attention.",".$cbo_com_location_id.",".$txt_challan_no.",".$txt_start_hours.",".$txt_start_minuties.",".$txt_carried_by.",".$txt_reference.",124,'".$user_id."','".$pc_date_time."',1,0)";
			 $txt_system_no=$new_sys_number[0];
		}
		else
		{
		$id=str_replace("'",'',$update_id);
		$field_array="department_id*section*in_date*attention*returnable*est_return_date*com_location_id*out_location_id*out_date*time_hour*time_minute*updated_by*update_date*status_active*is_deleted";
		$data_array="".$cbo_department_name."*".$cbo_section."*".$txt_in_date."*".$txt_attention."*".$cbo_returnable."*".$txt_return_date."*".$cbo_com_location_id."*".$cbo_out_location_id."*".$txt_out_date."*".$txt_start_hours."*".$txt_start_minuties."*'".$user_id."'*'".$pc_date_time."'*1*0";
		 $txt_system_no=$txt_system_id;
 		//$field_array1="quantity*remarks*updated_by*update_date*status_active*is_deleted";
 		//$data_array1= "".$txtquantity_1."*".$txtremarks_1."*'".$user_id."'*'".$pc_date_time."'*1*0";
		//$rID=sql_update("inv_gate_in_mst",$field_array,$data_array,"id",$update_id,1);
		//print_r($data_array);	
		}
		//if($id == "" ){ echo "15"; exit(); }
		$dtlsid=return_next_id("id", "inv_gate_in_dtl", 1);		
  		$field_array1="id,mst_id,sample_id,item_category_id,buyer_order,item_description,chalan_qty,quantity,uom,rate,amount,remarks,inserted_by,
		insert_date,status_active,is_deleted";
		   $add_comma=0;
			for($i=1; $i<=$row_num; $i++)
			   {
					$item_category_id="cboitemcategory_".$i;
					$txt_sample="cbosample_".$i;
					$txt_descrption="txtitemdescription_".$i;
					$txt_qty="txtquantity_".$i;
					$txt_chalan_qty="txtcalanquantity_".$i;
					$cbo_uom="cbouom_".$i;
					$txtuomqty="txtuomqty_".$i;
					$txt_rate="txtrate_".$i;
					$txt_amount="txtamount_".$i;
					$txt_order="txtorder_".$i;
					$txt_ramarks="txtremarks_".$i;
					$update_details_id="updatedtlsid_".$i;
				if(str_replace("'","",$$txt_qty)!='' || str_replace("'","",$$txt_qty)!=0)
				   {	
				    if ($add_comma!=0) $data_array1 .=",";
					$data_array1.="(".$dtlsid.",".$id.",".$$txt_sample.",".$$item_category_id.",".$$txt_order.",".$$txt_descrption.",".$$txt_chalan_qty.",".$$txt_qty.",".$$cbo_uom.",".$$txt_rate.",".$$txt_amount.",".$$txt_ramarks.",'".$user_id."','".$pc_date_time."',1,0)"; 
					$dtlsid=$dtlsid+1;
					$add_comma++;
				   }
			   }
		if(str_replace("'","",$update_id)=="")
		{
		  $rID=sql_insert("inv_gate_in_mst",$field_array,$data_array,0);
		}
		else
		{
		$rID=sql_update("inv_gate_in_mst",$field_array,$data_array,"id",$update_id,1);	
		}
	 // echo "10**insert into inv_gate_in_dtl (".$field_array1.") values ".$data_array1;die;	
		$dtlsrID=sql_insert("inv_gate_in_dtl",$field_array1,$data_array1,1);
		//echo "10**".$rID.'=='.$dtlsrID;
		//print($data_array1);die;
		if($db_type==0)
			{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "0**".$txt_system_no."**".str_replace("'","",$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2)
		{
		
			if($rID && $dtlsrID)
			{
			oci_commit($con);
			echo "0**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$id);
			}
		
			else
			{	oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$id);
			}
		}	
		disconnect($con);
		die;
				
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//echo $txtorder_1;die;
		/*if( str_replace("'","",$update_id) == "" )
		{
			echo "15";exit(); 
		}*///cboitemcategory_
		// validate condition for (gate pass>=get in)
		$validate_gate_pass=sql_select("select a.sys_number as pass_sys_number,sum(b.quantity) as pass_qty from  inv_gate_pass_mst a,inv_gate_pass_dtls b
where a.sys_number=".$txt_pass_id." and a.id=b.mst_id  group by a.sys_number");
		$validate_gate_in=sql_select("select c.gate_pass_no as gate_in_sys_number,sum(d.quantity) as gate_in_qty from  inv_gate_in_mst c,inv_gate_in_dtl d 
where c.gate_pass_no=".$txt_pass_id." and c.id=d.mst_id group by c.gate_pass_no");
		/*$gate_pass_qty=0;
		$gate_in_qty=0;
		foreach($validate_gate_pass as $row)
		{
			$gate_pass_qty+=$row[csf("pass_qty")];
		}
			//echo $gate_pass_qty;		
		foreach($validate_gate_in as $row)
		{
			$gate_in_qty+=$row[csf("gate_in_qty")];
		}
			//echo $gate_in_qty;
			$txtquantity=0;
			for($i=1; $i<=$row_num; $i++)
			{
			$txtquantity="txtquantity_".$i;
			$txtquantity=str_replace("'","",$$txtquantity);
			$txtquantity+=$txtquantity;
			}
			 //echo "10**".$txtquantity; die;
			 $gate_in_qty_total=$gate_in_qty+$txtquantity;
			if($gate_pass_qty<$gate_in_qty_total)
			{
				 echo "30** Gate in Quantity is more than pass Quantity";
				 exit();
			}*/
		
	    $field_array="department_id*section*in_date*attention*com_location_id*time_hour*time_minute*carried_by*pi_reference*entry_form*updated_by*update_date*status_active*is_deleted";
		$data_array="".$cbo_department_name."*".$cbo_section."*".$txt_in_date."*".$txt_attention."*".$cbo_com_location_id."*".$txt_start_hours."*".$txt_start_minuties."*".$txt_carried_by."*".$txt_reference."*124*'".$user_id."'*'".$pc_date_time."'*1*0";
 		$field_array1="item_category_id*sample_id*item_description*chalan_qty*uom*rate*amount*quantity*buyer_order*remarks*updated_by*update_date*status_active*is_deleted";
 		$data_array1= "".$cboitemcategory_1."*".$cbosample_1."*".$txtitemdescription_1."*".$txtcalanquantity_1."*".$cbouom_1."*".$txtrate_1."*".$txtamount_1."*".$txtquantity_1."*".$txtorder_1."*".$txtremarks_1."*'".$user_id."'*'".$pc_date_time."'*1*0";
		//print($data_array);die;
		$rID=sql_update("inv_gate_in_mst",$field_array,$data_array,"id",$update_id,1);	
 		$dtlsrID=sql_update("inv_gate_in_dtl",$field_array1,$data_array1,"id",str_replace("'","",$updatedtlsid_1),1); 
		//echo $dtlsrID;die;
		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if( $rID && $dtlsrID  )
			{
				oci_commit($con);
			    echo "1**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
		    	echo "10**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = return_field_value("id","inv_gate_in_mst","sys_number=$txt_system_id");	
		if($mst_id=="" || $mst_id==0){ echo "15**0";disconnect($con); die;}
		//$rID=1;
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";
		$rID=sql_update("inv_gate_in_mst",$field_array,$data_array,"id",$mst_id,1);
		$dtlsrID=sql_update("inv_gate_in_dtl",$field_array,$data_array,"mst_id",$mst_id,1);	
 		/*$rID = sql_update("inv_gate_in_mst",'status_active*is_deleted','0*1',"id",$mst_id,1);
		$dtlsrID = sql_update("inv_gate_in_dtl",'status_active*is_deleted','0*1',"mst_id",$mst_id,1);*/
	
	if($db_type==0)
		{	
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{	if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_id);
			}
		}
		else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id);
			}
		disconnect($con);
		die;
	}		
}



if($action=="sys_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	//echo $cbo_group;  
?>
     
<script>
	function js_set_value(sys_number)
	{
 		$("#hidden_sys_number").val(sys_number); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th >Company</th>
                    <th >System ID</th>
                   <input type="hidden" id="within_group" name="within_group" value="<? echo $cbo_group;?>" />
                    <th >Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">
                        <?
						 echo create_drop_down( "cbo_company_id", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1 );
						 ?>
                    </td>
                    
                    <td width="" align="center" >				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_gate_pass_id" id="txt_gate_pass_id" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_gate_pass_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('within_group').value, 'create_sys_search_list_view', 'search_div', 'material_goods_parking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
                     <input type="hidden" id="hidden_update_id" value="hidden_update_id" />
                    <!-- ---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table> 
        <br>   
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}




if($action=="create_sys_search_list_view")
{
	$ex_data = explode("_",$data);
	$gate_pass_id = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$within_group = $ex_data[4];
	$company = $ex_data[0];
 	//echo $fromDate;die;
 	$sql_cond="";
 
	if($db_type==2) 
		{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and in_date  between '".change_date_format($fromDate,'mm-dd-yyyy','-',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','-',1)."'";
		}
	if($db_type==0) 
		{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and in_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
	if(str_replace("'","",$company)!=0) $sql_cond .= " and company_id=".str_replace("'","",$company)." ";
	
	if(str_replace("'","",$gate_pass_id)!="") $get_cond .= "and sys_number_prefix_num  like '".str_replace("'","",$gate_pass_id)."'  "; else  $get_cond=""; 
	if(str_replace("'","",$within_group)!=0) $within_group_cond .= "and within_group in($within_group)  "; else  $within_group_cond=""; 

	
	$sql = "select id,sys_number_prefix_num, sys_number,within_group,gate_pass_no,department_id, challan_no, in_date 
			from inv_gate_in_mst where status_active=1 and is_deleted=0 $sql_cond $get_cond $within_group_cond ";
	//echo $sql;
	$department_arr = return_library_array( "select id, department_name from  lib_department",'id','department_name');
	$arr=array(1=>$department_arr,5=>$yes_no);
	echo create_list_view("list_view", "System No, Department,Gate Pass NO, Challan No, IN Date,Within Group","120,150,120,120,100","780","260",0, $sql , "js_set_value", "id,sys_number", "", 1, "0,department_id,0,0,0,within_group", $arr, "sys_number,department_id,gate_pass_no,challan_no,in_date,within_group", "",'','0,0,0,0,3,0') ;	
	exit();
	
}

if($action=="populate_master_from_data")
{
	
	$sql="select company_id,gate_pass_no,carried_by,within_group,party_type,pi_reference,sending_company,
attention,returnable,est_return_date,com_location_id,out_location_id,out_date,receive_from,department_id,section,in_date,challan_no,time_hour,time_minute from inv_gate_in_mst where id=$data ";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{	
        $sql_is = "select basis from inv_gate_pass_mst where sys_number='".$row[csf("gate_pass_no")]."'";
		$result = sql_select($sql_is);
		foreach($result as $val)
		{	
		$basis=$val[csf('basis')];
		}
		$chalan_no=$row[csf("challan_no")];
		$party_type=$row[csf("party_type")];
		$send_com=$row[csf("sending_company")];
 		echo "$('#cbo_company_name').val(".$row[csf("company_id")].");\n";
		echo "$('#txt_pass_id').val('".$row[csf("gate_pass_no")]."');\n"; 		
		echo "$('#txt_receive_from').val('".$row[csf("receive_from")]."');\n";
		echo "$('#cbo_department_name').val('".$row[csf("department_id")]."');\n";
		echo "$('#cbo_section').val(".$row[csf("section")].");\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_in_date').val('".change_date_format($row[csf("in_date")])."');\n";	
		echo "$('#txt_start_hours').val(".$row[csf("time_hour")].");\n";
		echo "$('#txt_carried_by').val('".$row[csf("carried_by")]."');\n";
		echo "$('#txt_reference').val('".$row[csf("pi_reference")]."');\n";
		echo "$('#txt_start_minuties').val(".$row[csf("time_minute")].");\n";
		echo "$('#cbo_group').val(".$row[csf("within_group")].");\n";
		//cbo_com_location_id*cbo_out_location_id*txt_out_date*cbo_returnable*txt_return_date*txt_attention*
		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		echo "$('#cbo_returnable').val(".$row[csf("returnable")].");\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("est_return_date")])."');\n";
		echo "$('#txt_out_date').val('".change_date_format($row[csf("out_date")])."');\n";
		
		echo "load_drop_down( 'requires/material_goods_parking_controller','".$row[csf("company_id")]."', 'load_drop_down_com_location', 'com_location_td' );";
		echo "$('#cbo_com_location_id').val('".$row[csf("com_location_id")]."');\n";
		
		echo "load_drop_down( 'requires/material_goods_parking_controller','".$row[csf("company_id")]."', 'load_drop_down_out_location', 'out_location_td' );";
		echo "$('#cbo_out_location_id').val('".$row[csf("out_location_id")]."');\n";
		
		if($row[csf("within_group")]==1)
		{
		echo "load_drop_down( 'requires/material_goods_parking_controller','$chalan_no'+'_'+$basis, 'load_drop_down_dying_source', 'sent_td');\n";	
		}
		else
		{
		echo "$('#cbo_party_type').val(".$row[csf("party_type")].");\n";
		echo "load_drop_down( 'requires/material_goods_parking_controller', '$party_type'+'_'+$send_com, 'load_drop_down_sent', 'sent_td');";	
		}
	
		echo "$('#cbo_out_company').val(".$row[csf("sending_company")].");\n";
 		  	
		//right side list view 
		//echo "show_list_view(".$row[csf("piworeq_type")]."+'**'+".$row[csf("pi_wo_req_id")].",'show_product_listview','list_product_container','requires/material_goods_parking_controller','');\n";
  	}
	
	exit();	
}


if($action=="show_dtls_list_view")
{
	extract($data); 
		
 $sql = "select id,sample_id,item_category_id,buyer_order,item_description,chalan_qty,quantity,uom,uom_qty,rate,amount,buyer_order,remarks from inv_gate_in_dtl  where mst_id=$data"; 
	//echo $sql;
	$arr=array(0=>$item_category,1=>$sample_library,5=>$unit_of_measurement);
	
 	echo create_list_view("list_view", "Item Category,Sample,Item Description,Challan Qty,Quantity,UOM,UOM Qty,Rate,Amount,Buyer Order,Remarks","120,100,150,80,80,80,80,80,80,150,100","1140","260",0, $sql, "get_php_form_data", "id", "'child_form_input_data','requires/material_goods_parking_controller'", 1, "item_category_id,sample_id,0,0,0,uom,0,0,0,0,0", $arr, "item_category_id,sample_id,item_description,chalan_qty,quantity,uom,uom_qty,rate,amount,buyer_order,remarks", "","",'0,0,0,0,0,0,0',"4,chalan_qty,quantity,'',uom_qty,'',amount,2");	
	exit();
		
} 


if($action=="child_form_input_data")
{
	
	//$data = details table ID 	
	$sql = "select id,sample_id,item_category_id,uom_qty,buyer_order,item_description,chalan_qty,quantity,uom,rate,amount,buyer_order,remarks from inv_gate_in_dtl  where id=$data"; 
	$result = sql_select($sql);
    
	foreach($result as $row)
	{
		echo "$('#txtitemdescription_1').val('".$row[csf("item_description")]."');\n";
		echo "$('#cbouom_1').val(".$row[csf("uom")].");\n";
		echo "$('#txtquantity_1').val(".$row[csf("quantity")].");\n";
		//echo "$('#txtuomqty_1').val(".$row[csf("uom_qty")].");\n";
		echo "$('#txtrate_1').val(".$row[csf("rate")].");\n";
		echo "$('#txtamount_1').val(".$row[csf("amount")].");\n";		
 		echo "$('#txtremarks_1').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbosample_1').val(".$row[csf("sample_id")].");\n";
		echo "$('#cboitemcategory_1').val(".$row[csf("item_category_id")].");\n";		
 		echo "$('#txtcalanquantity_1').val('".$row[csf("chalan_qty")]."');\n";
		echo "$('#txtorder_1').val('".$row[csf("buyer_order")]."');\n";		
		echo "$('#updatedtlsid_1').val(".$row[csf("id")].");\n";
		if($row[csf("item_category_id")]!=0)
		{
			echo " gate_enable_disable(".$row[csf("item_category_id")].");\n";		
		}
		else if($row[csf("sample_id")]!=0)
		{
			echo " gate_enable_disable(".$row[csf("sample_id")].");\n";	
		}
		//echo "show_list_view(".$row[csf("wo_po_type")]."+'**'+".$row[csf("wo_pi_no")].",'show_product_listview','list_product_container','requires/yarn_receive_controller','');\n";
		echo "set_button_status(1, permission, 'fnc_getin_entry',1,1);\n";
	}
	exit();
}

if ($action=="get_in_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data[0]);
	$company=$data[0];
	$location=$data[4];
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$section_library=return_library_array( "select id,section_name from   lib_section", "id","section_name"  );
	$deparntment_library=return_library_array( "select id,department_name from   lib_department", "id", "department_name"  );
	$sample_library=return_library_array( "select id,sample_name from   lib_sample", "id", "sample_name"  );
	//$address=return_field_value("address","lib_location","id=$data[3]");
	
	
	$sql="select id,sys_number,company_id,party_type,carried_by,gate_pass_no,sending_company,receive_from,department_id,section,in_date,challan_no,time_hour,time_minute from inv_gate_in_mst where sys_number='$data[1]' and status_active=1 and is_deleted=0";
	//echo $sql;
	$dataArray=sql_select($sql);
	$party_type=$dataArray[0][csf('party_type')];
	if($party_type==1)
	{
		$sample_library=return_library_array( "select id,buyer_name  from   lib_buyer", "id", "buyer_name"  );
		$out_company=$sample_library[$dataArray[0][csf('sending_company')]];
	}
	else if($party_type==2)
	{
		$sample_library=return_library_array( "select id,supplier_name   from   lib_supplier  ", "id", "supplier_name"  );
		$out_company=$sample_library[$dataArray[0][csf('sending_company')]];
		
	}
	else if($party_type==3)
	{
		$sample_library=return_library_array( "select id,other_party_name from   lib_other_party ", "id", "other_party_name"  );
		$out_company=$sample_library[$dataArray[0][csf('sending_company')]];
		
	}
	else
	{
	$out_company=$company_library[$dataArray[0][csf('sending_company')]];	
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
?>
<div style="width:930px;" align="center">

 <table width="900" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $com_dtls[0]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center">
				<?
					echo $com_dtls[1];
				//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";die;
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						<? echo $result[csf('plot_no')]; ?> 
						<? echo $result[csf('level_no')]?>
						<? echo $result[csf('road_no')]; ?> 
						<? echo $result[csf('block_no')];?> 
						<? echo $result[csf('city')];?> 
						<? echo $result[csf('zip_code')]; ?> 
						<? echo $result[csf('province')];?> 
						<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						<? echo $result[csf('email')];?> 
						<? echo $result[csf('website')];
					}*/
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="7" align="center" style="font-size:x-large"><strong><u>Gate In Challan</u></strong></td>
        </tr>
        <tr>
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="120"><strong>Gate Pass ID:</strong></td><td width="175px" colspan="2"><? echo $dataArray[0][csf('gate_pass_no')]; ?></td>
            <td width="125"><strong>Out Company:</strong></td><td width="175px"><? echo $out_company; //$company_library[$dataArray[0][csf('sending_company')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Receive From:</strong></td> <td width="175px"><? echo $dataArray[0][csf('receive_from')]; ?></td>
            <td><strong>Department:</strong></td><td width="175px" colspan="2"><? echo $deparntment_library[$dataArray[0][csf('department_id')]]; ?></td>
            <td><strong>Section:</strong></td><td width="175px"><? echo $section_library[$dataArray[0][csf('section')]]; ?></td>
        </tr>
          <tr>
            <td><strong>In Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('in_date')]); ?></td>
            <td><strong>Challan NO:</strong></td><td width="175px" colspan="2"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>IN Time:</strong></td><td width="175px"><? echo $dataArray[0][csf('time_hour')]." HH ".$dataArray[0][csf('time_minute')]." Min"; ?></td>
        </tr>
        <tr>
          <td><strong>Carried By:</strong></td> <td width="175px"><? echo $dataArray[0][csf('carried_by')]; ?></td>
        </tr>
    </table>
     <br>
    <table align="center" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="100" align="center">Item Category</th>
            <th width="100" align="center">Sample</th>
            <th width="150" align="center">Item Description</th>
            <th width="50" align="center">UOM</th>
            <th width="80" align="center">Challan Qty</th>
            <th width="80" align="center">Quantity</th>
            <th width="80" align="center">UOM Qty.</th>
            <th width="80" align="center">Rate</th> 
            <th width="80" align="center">Amount </th>
            <th width="80" align="center">Buyer Order </th>
            <th width="100" align="center">Remarks</th>
        </thead>
<?
    $i=1;
	$gate_id=$dataArray[0][csf('id')];
	$sql_dtls= " select id,sample_id,item_category_id,uom_qty,buyer_order,chalan_qty,item_description, uom, quantity, rate, amount, remarks from inv_gate_in_dtl where mst_id=$gate_id and status_active=1 and is_deleted=0 ";
	//echo $sql_dtls;
	$sql_result=sql_select($sql_dtls);
	
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$chalan_qty+=$row[csf('chalan_qty')];
			$quantity+=$row[csf('quantity')];
			$tot_uom_qty+=$row[csf('uom_qty')];
			$amount+=$row[csf('amount')];
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><?  echo $item_category[$row[csf('item_category_id')]]; ?></td>
                <td><?  echo $sample_library[$row[csf('sample_id')]]; ?></td>
                <td><?  echo $row[csf('item_description')]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                <td align="right"><? echo $row[csf('chalan_qty')]; ?></td>
                <td align="right"><? echo $row[csf('quantity')]; ?></td>
                 <td align="right"><? echo $row[csf('uom_qty')]; ?></td>
                <td align="right"><? echo number_format($row[csf('rate')],2,'.',''); ?></td>
                <td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                <td align="right"><? echo $row[csf('buyer_order')]; ?></td>
                <td><? echo $row[csf('remarks')]; ?></td>
			</tr>
           
		<?php
        $uom_unit="Kg";
        $uom_gm="Grams";
    $i++;
    }
	?>
       <tfoot>
               <tr>
                    
                    <th colspan="5" align="right">Total</th>
                   
                    <th width="" align="right"><? echo $chalan_qty ; ?> </th>
                    <th width="" align="right"><? echo $quantity ; ?></th>
                      <th width="" align="right"><? echo $tot_uom_qty ; ?></th>
                    <th width="" align="center"></th> 
                    <th width="" align="right"><? echo number_format($amount,2,'.',''); ; ?> </th>
                    <th width="" align="center">  </th>
                    <th width="" align="center"></th>
                </tr>
           </tfoot>
    </table>
</div>
<div>
    <?
         echo signature_table(33, $data[0], "900px");
    ?>
</div>
    
    
    <?
	   exit();
}


?>
