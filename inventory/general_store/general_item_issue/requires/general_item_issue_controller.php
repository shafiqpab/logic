<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
// ---------------------------------------------------------------------------
// user credential data prepare start
$userCredential = sql_select("SELECT store_location_id,unit_id as company_id,company_location_id,item_cate_id FROM user_passwd where id=$user_id");
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];

if ($company_location_id !='') {
    $company_location_credential_cond = "and lib_location.id in($company_location_id)";
}

if($item_cate_id !='') {
    $item_cate_credential_cond =  "and lib_item_group.id in($item_cate_id)";  
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
// user credential data prepare end 


if($db_type==2 || $db_type==1 )
{
	$mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
	$concat="";
	$concat_coma="||";
}
else if ($db_type==0)
{
	$mrr_date_check="and year(insert_date)=".date('Y',time())."";
	$concat="concat";
	$concat_coma=",";
}
//--------------------------------------------------------------------------------------------
//$trim_group_arr = return_library_array("select id, trim_uom from lib_item_group","id","trim_uom");
$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");

// ==============Start Floor Room Rack Shelf Bin upto variable Settings============
if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_name' and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}
// ==============End Floor Room Rack Shelf Bin upto variable Settings==============

//load drop down company location
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $company_location_credential_cond order by location_name","id,location_name", 1, "-- Select --", 0, "load_drop_down( 'requires/general_item_issue_controller', this.value+'__'+$data, 'load_drop_down_store', 'store_td' );",0 );     	 
	exit();
}

if ($action=="load_drop_down_itemgroupPop")
{	   
	echo create_drop_down( "cbo_item_group", 180, "select id,item_name from lib_item_group where item_category=$data and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", 0, "","" );  	 
	exit();
}
if ($action=="load_drop_down_store_up")
{
	$data=explode("**",$data);
	//echo "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=$data[1] and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] group by a.id,a.store_name order by a.store_name";die;
	echo create_drop_down( "cbo_store_name", 180, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=$data[1] $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] group by a.id,a.store_name order by a.store_name","id,store_name", 1, "Select Store", 0, "","" ); 	 
	exit();	   
}

if ($action=="load_drop_down_loan_party")
{
	echo create_drop_down( "cbo_loan_party", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b 
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","1","" );
	exit();
}


if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	$machine_category=$data[2];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	if($machine_category==0 || $machine_category=="") $category_cond=""; else $category_cond=" and b.category_id=$machine_category";
	
	echo create_drop_down( "cbo_issue_floor", 130, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 $location_cond $category_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/general_item_issue_controller',document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_machine_category').value+'_'+this.value, 'load_drop_machine', 'machine_td' );","" );
  exit();	 
}
if ($action=="load_drop_machine")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$machine_category=$data[1];
	$floor_id=$data[2];
	if($machine_category==0 || $machine_category=="") $machine_cond=""; else $machine_cond=" and category_id=$machine_category";
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";
	
	echo create_drop_down( "cbo_machine_name", 120, "select id, machine_no as machine_name from lib_machine_name where  company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond $machine_cond order by machine_no","id,machine_name", 1, "-- Select Machine --", 0, "","" );
	exit();
}

//load drop down company department
if ($action=="load_drop_down_department")
{
	//echo "select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id='$data' order by department_name";die;
	echo create_drop_down( "cbo_department", 120, "select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id='$data' order by department_name","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_section', 'section_td' );",0 );     	 
	exit();
}

//load drop down company section
if ($action=="load_drop_down_section")
{
	echo create_drop_down( "cbo_section", 120, "select id,section_name from lib_section where status_active =1 and is_deleted=0 and department_id='$data' order by section_name","id,section_name", 1, "-- Select --", $selected, "",0 );     	 
	exit();
}

//load drop down store
if ($action=="load_drop_down_store")
{
	$data=explode("__",$data);
	echo create_drop_down( "cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and a.location_id = $data[0] and b.category_type in(4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99) and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"load_drop_down('requires/general_item_issue_controller', this.value+'_'+$data[1], 'load_drop_floor','floor_td');load_drop_down('requires/general_item_issue_controller', this.value+'_'+$data[1], 'load_drop_room','room_td');load_drop_down('requires/general_item_issue_controller', this.value+'_'+$data[1], 'load_drop_rack','rack_td');load_drop_down('requires/general_item_issue_controller', this.value+'_'+$data[1], 'load_drop_shelf','shelf_td');load_drop_down('requires/general_item_issue_controller', this.value+'_'+$data[1], 'load_drop_bin','bin_td');");
	exit();
}

if($action == "load_drop_floor")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_floor", "110", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "", 1 );
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];

	echo create_drop_down( "cbo_room", "110", "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "", 1 );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_rack", '110', "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "" , 1);
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_self", '110', "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "", 1 );
}

if($action == "load_drop_bin")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_binbox", '110', "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", 0, "", 1 );
}
 
//load drop down item group
if ($action=="load_drop_down_itemgroup")
{
	//load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_uom', 'uom_td' );
	echo create_drop_down( "cbo_item_group", 150, "select id,item_name from lib_item_group where item_category=$data and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", 0, "",1 );  	 
	exit();
}   

//load drop down uom
if ($action=="load_drop_down_uom")
{	   
	if($data==0) $uom=0; else $uom=$trim_group_arr[$data];
	echo create_drop_down( "cbo_uom", 130, $unit_of_measurement, "", 1, "-- Select --", $uom , "", 1);  	 
	exit();
} 

if ($action=="load_drop_down_location_popup")
{
	echo create_drop_down( "cbo_location_name", 90, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $company_location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );
	exit();
}


if($action=="chk_issue_requisition_variabe")
{
	$data_ref=explode("**",$data);
	
    $sql =  sql_select("select allocation, id from variable_settings_inventory where company_name = $data_ref[0] and variable_list = 24 and is_deleted = 0 and status_active = 1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('allocation')];
	}
	else
	{ 
		$return_data=0; 
	}
	
	echo $return_data;
	
	if( $data_ref[1]==22)
	{
		$variable_lot=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name= $data_ref[0] and variable_list=32 and status_active=1 and is_deleted=0");
		echo "**".$variable_lot;
	}
	
	die;
}

if ($action=="item_issue_requisition_popup_search")
{
    echo load_html_head_contents("Item Issue Requisition search From", "../../../../", 1, 1,'','1','');
    extract($_REQUEST);
	//echo $item_category_id;die;

	?>
	<script>

        function hidden_item_value(id)
        {
           // alert ($("#hidden_approval_necessity_setup").val());
            $('#hidden_item_issue_id').val(id);
			var ref = id.split("_");
			if ($("#hidden_approval_necessity_setup").val()==1)
			{
				if(ref[6]==1)
				{
					parent.emailwindow.hide();
				}
				else 
				{
					alert("Please Approve Requisition First");return;
				}
			}
			else
			{
				parent.emailwindow.hide();
			}
			
        }

        function item_issue_requisition_popup()
        {

        	if (form_validation('cbo_company_name','Company')==false)
			{
				alert('Pls, Select Company.');
				return;
			}
            show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('txt_indent_date').value+'**'+document.getElementById('txt_required_date').value+'**'+document.getElementById('txt_remarks').value+'**'+document.getElementById('txt_manual_requisition_no').value+'**'+document.getElementById('cbo_location_name').value+'**'+document.getElementById('cbo_division_name').value+'**'+document.getElementById('cbo_department_name').value+'**'+document.getElementById('cbo_section_name').value+'**'+document.getElementById('cbo_sub_section_name').value+'**'+document.getElementById('cbo_delivery_point').value+'**'+document.getElementById('txt_system_id').value+'**'+<? echo $item_category_id;?>, 'items_search_list_view', 'search_div', 'general_item_issue_controller', 'setFilterGrid(\'list_view\',-1)');
        }
        function fnc_sub_section()
         {
             $('#cbo_sub_section_name').css('display','none');
         }
    </script>
	</head>
	<body>

		<? 
			$necessity_sql=sql_select("select b.approval_need as approval_need, a.id as max_id from approval_setup_mst a, approval_setup_dtls b 
			where a.id=b.mst_id and a.company_id = $cbo_company_name and b.page_id = 23 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.id=(select max(id) as id from approval_setup_mst where company_id = $cbo_company_name and status_active=1)");

		?>
	    <div align="center" style="width:800px;">
	        <form name="searchitemreqfrm" id="searchitemreqfrm">
	            <fieldset style="width:940px; margin-left:3px">
	            <legend>Search</legend>
	                <table cellpadding="0" cellspacing="0" width="20%" class="rpt_table" rules="all">
	                    <thead>
	                        <th class="must_entry_caption">Company</th>
                             <th>Indent No.</th>
                            <th>Indent Date</th>
                             <th>Remarks</th>
                             <th>Manual Requisition No</th>
                            <th align="right">Required Date</th>
                            <th align="right">Location</th>
                            <th align="right">Division</th>
                            <th align="right">Department</th>
                            <th align="right">Section</th>
                            <th align="right">Sub Section</th>
                            <th align="right">Delivery Point</th>
	                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /><input type="hidden" name="id_field" id="id_field" value="" />		</th>
	                    </thead>
	                    <tbody>
	                    <tr>
	                    	<td>
								<?
                                    $company="select comp.id,comp.company_name from lib_company comp where  comp.status_active=1 and comp.is_deleted=0  order by company_name";
                                    echo create_drop_down("cbo_company_name",100,$company,"id,company_name",1,"--select--",$cbo_company_name,"load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_location_popup','location_td');",1);
                                 ?>
	                  		</td>
                            <td><input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:70px" ></td>
                      		<td><input type="text" name="txt_indent_date" id="txt_indent_date" class="datepicker" style="width:70px" ></td>
                      		<td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:70px" ></td>
							<td><input type="text" name="txt_manual_requisition_no" id="txt_manual_requisition_no" class="text_boxes" style="width:70px"></td>
                            <td><input type="text" name="txt_required_date" id="txt_required_date" class="datepicker" style="width:70px" readonly></td>
                            <td id="location_td_popup">
								<?php
                                    echo create_drop_down( "cbo_location_name", 90,$blank_array,"id,location_name", 1, "-- Select --",0,"");
                                ?>
			                 </td>
				            <td  id="division_td" width="90">
							   <?php
									echo create_drop_down( "cbo_division_name", 90,$blank_array,"", 1, "-- Select --" );
				               ?>
				            </td>
                            <td width="70" id="department_td">
								<?php
                       				 echo create_drop_down( "cbo_department_name", 90,$blank_array,"", 1, "-- Select --" );
                   				?>
				            </td>
                             <td id="section_td"  width="132">
                             	<?
									echo create_drop_down( "cbo_section_name", 90,$blank_array,"", 1, "-- Select --",'' );
								?>
				            </td>
                            <td  id="sub_section_td" width="90">
								<?php
									echo create_drop_down( "cbo_sub_section_name", 90,$blank_array,"", 1, "-- Select --" );
	                			?>
				            </td>
                            <td><input type="text" name="cbo_delivery_point" id="cbo_delivery_point" style="width:90px" class="text_boxes"></td>
	                		<td>
	                			<input type="hidden" id="hidden_approval_necessity_setup" value="<?php  echo $necessity_sql[0][csf('approval_need')]; ?>" />
	                			<input type="hidden" id="hidden_item_issue_id" />
                            	<input type="hidden" id="hidden_item_cost_center" />
                            	<input type="hidden" id="hidden_itemissue_req_sys_id" />
                            	<input type="button" id="search_button" class="formbutton" value="Show" onClick="item_issue_requisition_popup()" style="width:100px;" />
	                  		</td>
	                    </tr>
	                    </tbody>
	                    </table>
	               <div style="width:100%; margin-top:10px;" id="search_div" align="center"></div>
	            </fieldset>
	        </form>
	    </div>
	</body>
    <script>
    set_all_onclick();
    var cbo_company_name=$("#cbo_company_name").val();
    load_drop_down( 'general_item_issue_controller', cbo_company_name, 'load_drop_down_location_popup','location_td_popup');
    </script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	    <?
	exit();

}

if($action=="items_search_list_view")
{
	$data=explode('**',$data);
	$remarks_no=$data[3];
	$requisition_no=$data[4];
	$delivery=$data[10];
	$indent_no=trim($data[11]);
	$item_category_id=trim($data[12]);
	//echo $item_category_id;die;
	//var_dump($data);die;
	if($data[0]!=0){ $company_id=" and a.company_id = $data[0]";}else{ echo "Select Company"; die;}
	if($data[3]!=''){ $remarks=" and a.remarks like '$remarks_no%'";}else{ echo "";}
	if($data[4]!=''){ $manual_requisition_no=" and a.manual_requisition_no like '$requisition_no%'";}else{ echo "";}
	if($data[5]!=0){ $location_id=" and a.location_id = $data[5]";}else{ echo "";}
	if($data[6]!=0){ $division_id=" and a.division_id = $data[6]";}else{ echo "";}
	if($data[7]!=0){ $department_id=" and a.department_id = $data[7]";}else{ echo "";}
	if($data[8]!=0){ $section_id=" and a.section_id = $data[8]";}else{ echo "";}
	if($data[9]!=0){ $sub_section_id=" and a.sub_section_id = $data[9]";}else{ echo "";}
	if($data[10]!=''){ $delivery_id=" and a.delivery_point like '$delivery%'";}else{ echo "";}
	if($data[11]!=''){ $ind_id=" and a.itemissue_req_sys_id like '%$indent_no'";}else{ echo "";}
	//$date=change_date_format($data[1],'mm-dd-yyyy');
	//if($data[1]!=0){ $indent_date=" and indent_date = $data[1]";}else{ $indent_date=""; }
	$section_library=return_library_array( "select id, section_name from lib_section", "id", "section_name"  );
	$department=return_library_array( "select id, department_name from lib_department", "id", "department_name"  );
	$location=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$division=return_library_array( "select id, division_name from lib_division", "id", "division_name"  );
	$section_library=return_library_array( "select id, section_name from lib_section", "id", "section_name"  );


	$date=$data[1];
	$re_date=$data[2];
	if($data[1]!=0)
	{
		if($db_type==0)
		{
			$indent_date = "and a.indent_date ='".change_date_format($date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$indent_date = "and a.indent_date ='".change_date_format($date,'','',1)."'";
		}
	}
	else
	{
		$indent_date = "";
	}

	if($data[2]!=0)
	{
		if($db_type==0)
		{
			$require_date = "and a.required_date ='".change_date_format($re_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$require_date = "and a.required_date ='".change_date_format($re_date,'','',1)."'";
		}
	}
	else
	{
		$require_date = "";
	}

	$sql="select a.id, a.itemissue_req_sys_id, a.company_id, a.indent_date, a.required_date, a.location_id, a.division_id, a.department_id, a.section_id, a.sub_section_id, a.delivery_point, a.remarks, a.manual_requisition_no, a.is_approved 
	from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c 
	where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active in(1,3) and c.item_category_id in($item_category_id) $remarks $manual_requisition_no $company_id $indent_date $require_date $location_id $division_id $department_id $section_id $sub_section_id $delivery_id $ind_id
	group by a.id, a.itemissue_req_sys_id, a.company_id, a.indent_date, a.required_date, a.location_id, a.division_id, a.department_id, a.section_id, a.sub_section_id, a.delivery_point, a.remarks, a.manual_requisition_no, a.is_approved";
		//echo $sql;// die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$company_arr,4=>$location,5=>$division,6=>$department,7=>$section_library);

	echo  create_list_view("list_view", "Company,Indent No.,Indent date,Required Date,Location,Division,Department,Section,Sub Section,Delivery Point", "150,100,80,100,100,80,80,80,80","1030","320",0, $sql, "hidden_item_value", "id,itemissue_req_sys_id,indent_date,location_id,department_id,section_id,is_approved", "", 1, "company_id,0,0,0,location_id,division_id,department_id,section_id", $arr , "company_id,itemissue_req_sys_id,indent_date,required_date,location_id,division_id,department_id,section_id,sub_section_id,delivery_point", "",'','0,0,3,3');


}

if ($action=="show_item_issue_listview")
{
	//var_dump($data);die;
	$data_ref=explode("__",$data);
	$req_id=$data_ref[0];
	$item_cat=$data_ref[1];
	if(is_numeric($req_id))
	{
		$sql="select b.id, b.mst_id as rid, b.req_qty, b.item_group, b.item_description, b.product_id, a.location_id  from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c 
		where b.product_id=c.id and c.item_category_id in($item_cat) and b.mst_id=a.id and b.mst_id='$req_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3)";
	}
	else
	{
	 	$sql="select a.id as rid a.itemissue_req_sys_id, department_id, b.mst_id, b.req_qty, b.item_group, b.item_description, b.product_id,a.location_id from inv_item_issue_requisition_mst a,inv_itemissue_requisition_dtls b where b.mst_id=a.id and a.itemissue_req_sys_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}
	//echo $sql;
	$nameArray=sql_select( $sql );

	?>
 	<div style="width:290px;">
	    <table width="290" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left">
	    	<thead>
				<tr>
					<th width="35">SL</th>
					<th width="80">Item Group</th>
					<th width="95">Item Description</th>
                    <th >Req. Qty.</th>
				</tr>
		</thead>
	     </table>
	<div id="" style="max-height:363px; width:307px; overflow-y:scroll" >
	    <table width="290" cellspacing="0" cellpadding="0" border="0" rules="all"  class="rpt_table" align="left">

			<tbody>
	        <?
			$item_group=return_library_array("select id,item_name from lib_item_group",'id','item_name');
	         $i=1;
					foreach ($nameArray as $selectResult)
				   	{
	       	 		?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="get_php_form_data('<? echo $selectResult[csf('product_id')];?>+**+<? echo $selectResult[csf('req_qty')];?>+**+<? echo $selectResult[csf('rid')];?>+**+<? echo $selectResult[csf('location_id')];?>','populate_item_details_form_data_dtls','requires/general_item_issue_controller');" >
                        <td width="35"><? echo $i; ?></td>
                        <td width="80"><? echo $item_group[$selectResult[csf("item_group")]];?></td>
                        <td width="95"><? echo $selectResult[csf("item_description")];?></td>
                        <td align="right"><? echo $selectResult[csf("req_qty")];?></td>
                    </tr>
	            <? $i++;


				}?>
	            </tbody>
		</table>

	     </div>
     </div>

		<?

}

if($action=="populate_item_details_form_data_dtls")
{
	$ex_data = explode("**",$data);
	//echo $ex_data[3]."=".$ex_data[4];die;

	$qnty=sql_select("select sum(a.cons_quantity) as Q from inv_transaction a , inv_issue_master b where a.prod_id=$ex_data[0] and a.mst_id=b.id and b.req_id=$ex_data[2] and a.transaction_type=2 and a.status_active=1");

	$total_qnty=$qnty[0]['Q'];
	//echo "select id,item_group_id,sub_group_code,item_description,unit_of_measure,current_stock,item_category_id,item_size from product_details_master where id='$ex_data[0]'";
	$data_ar=sql_select("select id,company_id,item_group_id,sub_group_code,item_description,unit_of_measure,current_stock,item_category_id,item_size from product_details_master where id='$ex_data[0]'");
	foreach ($data_ar as $info)
	{
		echo "document.getElementById('cbo_item_group').value 			= '".$info[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_item_category').value 		= '".$info[csf("item_category_id")]."';\n";
		$item_des=$info[csf("item_description")];
		if($info[csf("item_size")]!="") $item_des.=", ".$info[csf("item_size")];
		echo "document.getElementById('txt_item_desc').value 			= '".$item_des."';\n";
		echo "document.getElementById('cbo_uom').value 					= '".$info[csf("unit_of_measure")]."';\n";
		echo "document.getElementById('txt_current_stock').value 		= '".$info[csf("current_stock")]."';\n";
		echo "document.getElementById('current_prod_id').value 			= '".$info[csf("id")]."';\n";
		//echo "load_drop_down( 'requires/general_item_issue_controller', ".$ex_data[4].", 'load_drop_down_department', 'department_td' );\n";
		echo "load_drop_down('requires/general_item_issue_controller', ".$ex_data[3]."+'__'+".$info[csf('company_id')].", 'load_drop_down_store','store_td');\n";
		
		if($ex_data[3])
		{
			echo "document.getElementById('cbo_division').value 			= '".$ex_data[3]."';\n";
			echo "load_drop_down( 'requires/general_item_issue_controller', ".$ex_data[3].", 'load_drop_down_department', 'department_td' );\n";
			echo "document.getElementById('cbo_department').value 			= '".$ex_data[4]."';\n";
		}
		//echo "document.getElementById('hidden_req_qnty').value 			= '".$ex_data[1]."';\n";
		//echo "document.getElementById('total_issued_qnty').value 		= '".$total_qnty."';\n";
		//echo "document.getElementById('cbo_store_name').value 			= '0';\n";
		//echo "document.getElementById('txt_current_stock').value 		= '';\n";

	}
}

if($action=="check_reqn_no")
{
	//echo $data;
	$sql = sql_select("select id,company_id from inv_item_issue_requisition_mst where status_active=1 and is_deleted=0 and itemissue_req_sys_id='$data' ");
    if(count($sql)>0) echo $sql[0][csf('company_id')]."**".$sql[0][csf('id')];
	else{ echo 0; }
	exit();
}

if ($action=="item_description_popup")
{
	echo load_html_head_contents("Item popup", "../../../../", 1, 1,'','1','');	
	extract($_REQUEST);
	$item_cat=str_replace("'","",$item_cat);	
	//echo $item_cat.jahid;die;	
	?>
	<script> 
		function js_set_value(item_description)
		{
	  		 $("#item_description_all").val(item_description);
	  		//$("#item_description_all").val('lktoilix sdoi;f il;of opod loiioo;potg09p pgsaos 1205 050');
	 		parent.emailwindow.hide(); 
		}
		function open_itemCode_popup()
		{
			if( form_validation('cbo_item_category','Item Category Name')==false )
			{
				return;
			}
			var cbo_item_category = $("#cbo_item_category").val();
			var page_link="general_item_issue_controller.php?action=item_code_popup&cbo_item_category="+cbo_item_category; 
			var title="Item Code Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var item_cote_all=this.contentDoc.getElementById("item_id").value;//alert(item_description_all); 
				var splitArr = item_cote_all.split("_");
				$("#hide_product_id").val(splitArr[0]); 
				$("#txt_item_code").val(splitArr[1]);
			}
		} 
	</script>
	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
	            <thead>
	                <tr>                	 
	                    <th width="230" class="must_entry_caption">Item Category</th>
	                    <th width="230">Item Group</th>
	                    <th width="180" style="display:none">Store Name</th>
	                    <th width="130">Product Id</th>
	                    <th width="130">Item Code</th>
	                    <th ><input type="reset" name="re_button" id="re_button" value="Reset" style="width:90px" class="formbutton"  /></th>           
	                </tr>
	            </thead>
	            <tbody>
	                <tr>                    
	                    <td>
	                        <?  
	                           // $search_by = array(1=>'Return Number');
								//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index )
								//echo create_drop_down( "cbo_item_category", 180, $item_category,"", 1, "-- Select --", 0, "load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_itemgroupPop', 'item_group_td' );", 0,"","","",$item_cate);

							echo create_drop_down( "cbo_item_category", 180, $item_category,"", 1, "-- Select --", $item_cat, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_itemgroupPop', 'item_group_td' );load_drop_down( 'general_item_issue_controller', $company_id+'**'+this.value, 'load_drop_down_store_up', 'store_td' );", 0,$item_cat);
	                        ?>
	                    </td>
	                    <td width="" align="center" id="item_group_td">
	                    	<?  
	                            //$search_by = array(1=>'Return Number');
								//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_item_group", 180, $blank_array, "", 1, "-- Select --", 0, "", 0,"" );
	                        ?>	
	                    </td>
	                    <td align="center" id="store_td"  style="display:none">
	                        <?  
								//$company_id=str_replace("'","",$company_id);
								echo create_drop_down( "cbo_store_name", 180, $blank_array, "", 1, "-- Select --", 0, "", 0,"" );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" id="txt_product_id" name="txt_product_id" style="width:100px;" class="text_boxes">
	                    </td>
	                    <td align="center">
	                        <input type="text" id="txt_item_code" name="txt_item_code" style="width:100px;" class="text_boxes" placeholder="Browse Or Write" onDblClick="open_itemCode_popup();">
	                        <input type="hidden" id="hide_product_id" name="hide_product_id" >
	                    </td>
	                    <td align="center">
	                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_item_category').value+'_'+document.getElementById('cbo_item_group').value+'_'+document.getElementById('cbo_store_name').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_item_code').value+'_'+document.getElementById('txt_product_id').value+'_'+<? echo $cbo_store_name; ?>, 'create_item_search_list_view', 'search_div', 'general_item_issue_controller', 'setFilterGrid(\'tbl_serial\',-1)')" style="width:90px;" />				
	                    </td>
	            	</tr>
	            </tbody>
	    	</table> 
        <br>   
        <div align="center" valign="top" id="search_div"> </div> 
    </form>
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?		
}

if ($action=="create_item_search_list_view")
{
	$ex_data = explode("_",$data);
	$item_group=$store_name=0;
	$item_category_id = $ex_data[0];
	$item_group = $ex_data[1];
	$store_name = $ex_data[2];
	$company = $ex_data[3];
	$item_code_name = str_replace("'","",$ex_data[4]);
	$txt_prod_id = str_replace("'","",$ex_data[5]);
	$store_id = $ex_data[6];
	//echo $item_category_id;die;	
	$sql_rackWiseBalanceShow=sql_select("select id, rack_balance from variable_settings_inventory where company_name=$company and item_category_id=$item_category_id and variable_list=21 and status_active=1 and is_deleted=0");
	$varriable_setting_rack_self_maintain=$sql_rackWiseBalanceShow[0][csf('rack_balance')];
	if ($varriable_setting_rack_self_maintain==1) $table_width=1300;
	else $table_width=1000;
	?>
    <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="" rules="all" >
        <thead>
            <tr>                	 
                <th width="30">SL</th>
                <th width="60">Prod Id</th>
                <th width="80">Current Stock</th>
				<th width="100">Re-Order Level</th>
                <th width="80">Category</th>
                <th width="100">Item Group</th>
                <th width="80">Sub Group</th>
                <th width="80">Item Number</th>
                <th width="80">Item Code</th>
                <th width="180">Description</th>
                <th width="110">Store Name</th>
                <?
	            if ($varriable_setting_rack_self_maintain==1)
	            {	
	            	?>
	                <th width="60">Floor</th>
	                <th width="60">Room</th>
	                <th width="60">Rack</th>
	                <th width="60">Shelf</th>
	                <th width="60">Bin/Box</th>
	                <?
	            }
	            ?>    
				<th>Lot</th>               
            </tr>
        </thead>
    </table>
    <div style="width:<? echo $table_width; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    	<table width="<? echo $table_width-20; ?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="tbl_serial" rules="all">
        	<tbody> 
	            <?
	            
				
				$entry_cond=$item_code_cond=$prod_cond=$store_cond='';
				if(str_replace("'","",$item_category_id)==4) $entry_cond="and b.entry_form=20";
	            if ($item_category_id!=0) $item_category_sql=" and a.item_category in($item_category_id) and b.item_category_id in($item_category_id)"; else { echo "Please Select item category."; die; };
				//echo $item_category;
	            if( $item_group != 0 ) $item_group_con=" and b.item_group_id=$item_group";
	            if( $store_name != 0 ) $store_name_con=" and a.store_id='$store_name'";
				if( $item_code_name != '' ) $item_code_cond=" and b.item_code='$item_code_name'";
				if( $txt_prod_id != '' ) $prod_cond=" and b.id='$txt_prod_id'";
				if( $store_id != 0 ) $store_cond=" and a.store_id=$store_id";
	            //echo $company;die;
	            
				if($item_category_id==22)
				{
					$sql="SELECT  b.id as ID, (sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as BALANCE_STOCK, sum(case when a.transaction_type in(1,4,5) then a.balance_qnty else 0 end) as RECEIVE, sum(case when a.transaction_type in(2,3,6) then a.balance_qnty else 0 end) as ISSUE, b.CURRENT_STOCK, b.ITEM_CATEGORY_ID, b.ITEM_GROUP_ID, b.SUB_GROUP_NAME,b.RE_ORDER_LABEL, b.ITEM_NUMBER, $concat(b.item_description $concat_coma ',' $concat_coma b.item_size) as DES, a.store_id as STORE_ID, a.FLOOR_ID, a.ROOM, a.RACK, a.SELF, a.BIN_BOX, b.ITEM_CODE, b.BRAND_NAME, b.ORIGIN, b.MODEL, b.order_uom as ORDER_UOM, a.batch_lot as BATCH_LOT
					from  inv_transaction a, product_details_master b
					where a.prod_id=b.id and a.company_id=$company and a.status_active=1 and a.is_deleted=0 $store_cond $store_name_con $item_category_sql $item_group_con  $item_code_cond $entry_cond $prod_cond  and b.status_active in(1,3) and b.is_deleted=0
					group by a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, b.id, b.item_category_id, b.item_group_id, b.sub_group_name,b.RE_ORDER_LABEL, b.item_number, b.item_description, b.item_size, b.current_stock, b.item_code, b.brand_name, b.origin, b.model, b.order_uom, a.batch_lot";
				}
				else
				{
					$sql="SELECT  b.id as ID, (sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as BALANCE_STOCK, sum(case when a.transaction_type in(1,4,5) then a.balance_qnty else 0 end) as RECEIVE, sum(case when a.transaction_type in(2,3,6) then a.balance_qnty else 0 end) as ISSUE, b.CURRENT_STOCK, b.ITEM_CATEGORY_ID, b.ITEM_GROUP_ID, b.SUB_GROUP_NAME,b.RE_ORDER_LABEL, b.ITEM_NUMBER, $concat(b.item_description $concat_coma ',' $concat_coma b.item_size) as DES, a.store_id as STORE_ID, a.FLOOR_ID, a.ROOM, a.RACK, a.SELF, a.BIN_BOX, b.ITEM_CODE, b.BRAND_NAME, b.ORIGIN, b.MODEL, b.order_uom as ORDER_UOM
					from  inv_transaction a, product_details_master b
					where a.prod_id=b.id and a.company_id=$company and a.status_active=1 and a.is_deleted=0 $store_cond $store_name_con $item_category_sql $item_group_con  $item_code_cond $entry_cond $prod_cond and b.status_active in(1,3) and b.is_deleted=0
					group by a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, b.id, b.item_category_id, b.item_group_id, b.sub_group_name,b.RE_ORDER_LABEL, b.item_number, b.item_description, b.item_size, b.current_stock, b.item_code, b.brand_name, b.origin, b.model, b.order_uom";
				}
				
	           //echo $sql;
	            $itemgroup_arr = return_library_array("select id,item_name from lib_item_group where item_category not in (1,2,3,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');
	            $store_arr = return_library_array("select id,store_name from lib_store_location where company_id=$company and status_active=1 and is_deleted=0 order by store_name",'id','store_name');
	            $floor_room_rack_arr = return_library_array("select floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst where company_id=$company and status_active=1 and is_deleted=0 order by floor_room_rack_name",'floor_room_rack_id','floor_room_rack_name');
	            $arr=array(0=>$item_category,1=>$itemgroup_arr,3=>$store_arr);
	            $result=sql_select($sql);
            	$i=1;
            	foreach($result as $row)
            	{
            		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";           
            		?>
            		<input type="hidden" id="item_description_all" value="" style=" width:300px;" />
            		<tr bgcolor="<? echo $bgcolor; ?>"  onClick='js_set_value("<? echo $row['ID']; ?>*<? echo $row['DES'] ;?>*<? echo $row['BALANCE_STOCK'];  //echo ($row[csf('receive')]-$row[csf('issue')]) ; ?>*<? echo $row['ITEM_CATEGORY_ID'] ; ?>*<? echo $row['ITEM_GROUP_ID'] ; ?>*<? echo $row['STORE_ID'] ; ?>*<? echo $row['BRAND_NAME'] ; ?>*<? echo $row['ORIGIN'] ; ?>*<? echo $row['MODEL'] ; ?>*<? echo $row['FLOOR_ID'] ; ?>*<? echo $row['ROOM'] ; ?>*<? echo $row['RACK'] ; ?>*<? echo $row['SELF'] ; ?>*<? echo $row['BIN_BOX'] ; ?>*<? echo $row['ORDER_UOM'] ; ?>*<? echo $row['BATCH_LOT'] ; ?>")' id="" style="cursor:pointer">
		                <td width="30" align="center"><? echo $i;  ?></td>
		                <td align="center" width="60"><? echo $row['ID']; ?></td>
		                <td align="right" width="80"><? echo number_format($row['BALANCE_STOCK'],0); ?>&nbsp;</td>
						<td width="100" ><? echo $row['RE_ORDER_LABEL']; ?></td>
		                <td width="80" ><? echo $item_category[$row['ITEM_CATEGORY_ID']]; ?></td>
		                <td width="100"><? echo $itemgroup_arr[$row['ITEM_GROUP_ID']] ; ?></td>
		                <td width="80"><? echo $row['SUB_GROUP_NAME'] ; ?></td>
		                <td width="80"><? echo $row['ITEM_NUMBER']; ?></td>
		                <td width="80"><? echo $row['ITEM_CODE'] ; ?></td>
		                <td width="180"><? echo $row['DES'] ; ?></td>
		                <td width="110"><? echo $store_arr[$row['STORE_ID']] ; ?></td>
		                <?
		                if ($varriable_setting_rack_self_maintain==1)
		                {
		                	?>	
			                <td width="60"><? echo $floor_room_rack_arr[$row['FLOOR_ID']] ; ?></td>
							<td width="60"><? echo $floor_room_rack_arr[$row['ROOM']] ; ?></td>
							<td width="60"><? echo $floor_room_rack_arr[$row['RACK']] ; ?></td>
							<td width="60"><? echo $floor_room_rack_arr[$row['SELF']] ; ?></td>
							<td width="60"><? echo $floor_room_rack_arr[$row['BIN_BOX']] ; ?></td>
							<?
						}
						?>	
                        <td><? echo $row['BATCH_LOT'] ; ?></td>		                
            		</tr> 
            		<? 
            		$i++;
        		}            
        		?>
        	</tbody>
    	</table>  
    </div>
    <?
}

if($action=="item_code_popup")
{
	echo load_html_head_contents("Item popup", "../../../../", 1, 1,'','1','');	
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(str)
	{
  		$("#item_id").val(str);
 		parent.emailwindow.hide(); 
	}
	</script>
    <input type="hidden" id="item_id" name="item_id">
    <?
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$sql="select id, product_name_details, item_code from product_details_master where item_category_id in($cbo_item_category)";
	//echo $sql="selece id, product_name_details, item_code from product_details_master where item_category_id='$cbo_item_category'";
	echo create_list_view ( "list_view","Item Description,Item Code", "200","390","200",0, $sql, "js_set_value", "id,item_code", "", 1, "0,0", $arr, "product_name_details,item_code", "0,0", 'setFilterGrid("list_view",-1);');
}

if($action=="serial_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST); 
	//echo $txt_received_id; die;
	//echo $current_prod_id; die;

 	$serialStringID = str_replace("'","",$serialStringID);
 	//$serialStringNo = str_replace("'","",$serialStringNo);
	$txt_received_id = str_replace("'","",$txt_received_id);
	$current_prod_id = str_replace("'","",$current_prod_id);
	
 	?>
	<script>
	var selected_id = new Array();
	var selected_no = new Array();	
	
	 
	var serialNoArr="<? echo $serialStringID; ?>";
 	var chk_selected_no = new Array();
	var chk_selected_id = new Array();
	if(serialNoArr!=""){chk_selected_no=serialNoArr.split(",");}
	
	 
	
	function check_all_data() 
	{
		var tbl_row_count = document.getElementById( 'hidden_all_id' ).value.split(","); 
 		//tbl_row_count = tbl_row_count-1;
		for( var i = 0; i < tbl_row_count.length; i++ ) {
 			if( jQuery.inArray( $('#txt_serial_id' + tbl_row_count[i]).val(), chk_selected_id ) != -1 )
			js_set_value( tbl_row_count[i] );
		}
	}
	
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				//x.style.backgroundColor = ( $serialStringID != "")? newColor : origColor;
			}
		} 
		
	function js_set_value( str ) { //alert(str);
		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
		
		if( jQuery.inArray( $('#txt_serial_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_serial_id' + str).val() );
			selected_no.push( $('#txt_serial_no' + str).val() );
 		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_serial_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_no.splice( i, 1 );
		}
		var id = '';	var no = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			no += selected_no[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		no = no.substr( 0, no.length - 1 );
  		$('#txt_string_id').val( id );
		$('#txt_string_no').val( no );
	}
	 
	function fn_onClosed()
	{
		var txt_string = $('#txt_string').val();
		if(txt_string==""){ alert("Please Select The Serial"); return;}
		parent.emailwindow.hide();
	}
	 
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
    	<table width="300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_header" >
				<thead>
					<tr>                	 
						<th width="300">Serial No</th>
 					</tr>
				</thead>
        </table>        
        <div style="width:300px; min-height:220px">
		<table width="300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_serial" style="overflow:scroll; min-height:200px" >
 				<tbody>
                	<?
						$i=1;
						$sql="select id,serial_no from inv_serial_no_details where prod_id=$current_prod_id and is_issued=0";
						//echo $sql;die;
						$result = sql_select($sql);
						$count=count($result );
						foreach($result as $row) 
						{
							if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($new_data=="") $new_data=$row[csf("id")]; else $new_data .=",".$row[csf("id")];				
						?>	
							<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value(<? echo $row[csf("id")]; ?>)" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
								<td  width="300">
									<? echo trim($row[csf("serial_no")]); ?> 
									<input type="hidden" id="txt_serial_id<? echo $row[csf("id")]; ?>" value="<? echo $row[csf("id")]; ?>" >
                                    <input type="hidden" id="txt_serial_no<? echo $row[csf("id")]; ?>" value="<? echo $row[csf("serial_no")]; ?>" >
								</td>
									<?  
									
									if($count==$i)
									{
									?> 
                                    <input type="hidden" id="hidden_all_id" value="<? echo $new_data; ?>" >
                                    <? } ?>
							</tr> 
					<? 
						
							$i++;
						}

				?>
				</tbody>         
			</table>  
            </div>
            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></div>  
            <!-- Hidden field here-->
			<input type="hidden" id="txt_string_id" value="" />
            <input type="hidden" id="txt_string_no" value="" />				 
			<!--END--> 
			</form>
	   </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    
    <script>
	//alert(serialNoArr);
		if( serialNoArr!="" )
		{
			serialNoArr=serialNoArr.split(",");
			for(var k=0;k<serialNoArr.length; k++)
			{
				js_set_value(serialNoArr[k] );
				//alert(serialNoArr[k]);
			}
		}
	</script>
	</html>
	<?
}

if($action=="order_popup")
{
echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
extract($_REQUEST); 
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where find_in_set($company,tag_company) and status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');
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
			else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<? 			
				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				} 
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:230px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}																																													
		}
	
	function js_set_value(id,po_no)
	{ 
		$("#hidden_string").val(id+"_"+po_no); 
   		parent.emailwindow.hide();
 	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
            		<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                   		 <thead>                	 
                        	<th width="130">Search By</th>
                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                        	<th width="200">Date Range</th>
                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    	</thead>
        				<tr>
                    		<td width="130">  
							<? 
							$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
							echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
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
                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_po_search_list_view', 'search_div', 'general_item_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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
                    <input type="hidden" id="hidden_string">
          		</td>
            </tr>
    </table>
    <br>
    <div id="search_div"></div>    
    </form>
	</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
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
 	//$garments_nature = $ex_data[5];
	$year = $ex_data[5];
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";	
				
 	}
	if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and b.pub_shipment_date between '".change_date_format($txt_date_from,"yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to,"yyyy-mm-dd", "-",1)."'";
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
	if(trim($year)!=0) $sql_cond .= "  and to_char(b.shipment_date,'YYYY')=$year";
		
 	$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.pub_shipment_date,b.po_number,b.po_quantity ,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b 
			where
			a.job_no = b.job_no_mst and
			a.status_active=1 and 
			a.is_deleted=0  and b.status_active=1 and 
			b.is_deleted=0 
			$sql_cond"; 
	//echo $sql;die;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	?>
    <div style="width:820px;">
     	<table cellspacing="0" width="100%" border="1" class="rpt_table" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="80" >Shipment Date</th>
                <th width="120" >Order No</th>
                <th width="150" >Buyer</th>
                <th width="150" >Style</th>
                 <th width="100" >Order Qnty</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:820px; max-height:220px;overflow-y:scroll;" >	 
        <table cellspacing="0" width="802" class="rpt_table" id="tbl_po_list" border="1" rules="all">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
 					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $row[csf("po_number")];?>');" > 
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="80" align="center"><p><? echo change_date_format($row[csf("pub_shipment_date")]);?></p></td>		
							<td width="120" align="center"><p><? echo $row[csf("po_number")]; ?></p></td>
							<td width="150"><p><? echo $buyer_arr[$row[csf("buyer_name")]];  ?></p></td>	
							<td width="150"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
 							<td width="95" align="right" style="padding-right:5px;"><p><? echo $row[csf("po_quantity")];?> </p></td>
							<td><p><?  echo $company_arr[$row[csf("company_name")]];?></p> </td> 	
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
 
//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_prod_id=str_replace("'","",$current_prod_id);
	$variable_lot=str_replace("'","",$variable_lot);
	$txt_lot=str_replace("'","",$txt_lot);
	$txt_issue_qnty = str_replace("'","",$txt_issue_qnty);
	
	if($txt_store_sl_no=="") $txt_store_sl_no="''";
	
	if(str_replace("'","",$cbo_floor)=="") $cbo_floor=0;
	if(str_replace("'","",$cbo_room)=="") $cbo_room=0;
	if(str_replace("'","",$cbo_rack)=="") $cbo_rack=0;
	if(str_replace("'","",$cbo_self)=="") $cbo_self=0;
	if(str_replace("'","",$cbo_binbox)=="") $cbo_binbox=0;
	
	$req_id=str_replace("'", '', $hidden_issue_req_id);
	if($req_id!="")
	{
		$requisition_company_id = return_field_value("company_id", "inv_item_issue_requisition_mst", "id=$req_id", "company_id");
		//echo "20**".$requisition_company_id.'**'.str_replace("'", "", $cbo_company_name);die;
		if($requisition_company_id != str_replace("'", "", $cbo_company_name))
		{
			echo "20**Company must be same of Requisition Company";die;
		}
		$trans_id=str_replace("'","",$update_id);
		$up_cond="";
		if($trans_id!="") $up_cond=" and b.id <> $trans_id";
		$prev_req_rcv=sql_select("select sum(b.cons_quantity) as rcv_qnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=21 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.req_id=$req_id and b.prod_id=$current_prod_id $up_cond");
		$prev_req_qnty=$prev_req_rcv[0][csf("rcv_qnty")];
		
		$sql_req=sql_select("select sum(req_qty) as req_qty from inv_itemissue_requisition_dtls where mst_id=$req_id and product_id=$current_prod_id and status_active=1 and is_deleted=0 ");
		$cu_req_qnty=($sql_req[0][csf("req_qty")]-$prev_req_qnty)*1;
		$issu_qnty=str_replace("'","",$txt_issue_qnty)*1;
		if($issu_qnty>$cu_req_qnty)
		{
			echo "20**Issue Quantity Not Allow Over Requisition Quantity \n Requisition Balance Quantity=$cu_req_qnty";die;
		}
	}
	
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and transaction_type in (1,4,5) and status_active = 1", "max_date");      
	$max_recv_date = strtotime($max_recv_date);
	$issue_date = strtotime(str_replace("'", "", $txt_issue_date));
	if ($issue_date < $max_recv_date) 
        {
            echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Item";
            die;
	}
	
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_name and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	if($variable_store_wise_rate != 1) $variable_store_wise_rate=2;
        
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 	 
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//---------------Check Duplicate product in Same return number ------------------------//
		$txt_prod_id=str_replace("'","",$current_prod_id);
		$duplicate = is_duplicate_field("b.id","inv_issue_master a, inv_transaction b","a.id=b.mst_id and a.id=$txt_system_id and b.prod_id=$txt_prod_id and b.transaction_type=2"); 
		if($duplicate==1 && str_replace("'","",$txt_system_no)!="") 
		{
			//check_table_status( $_SESSION['menu_id'],0);
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			//check_table_status( $_SESSION['menu_id'], 0 );
			disconnect($con);
			die;
		}
		
		
		if($variable_lot==1  && str_replace("'","",$cbo_item_category)==22)
		{
			//$sqlLotCon.= " and batch_lot='$txt_lot'" ;
		}
		
		$global_stock_sql=sql_select("select AVG_RATE_PER_UNIT, CURRENT_STOCK from PRODUCT_DETAILS_MASTER where id=$txt_prod_id");
		$global_stock_qnty=$global_stock_sql[0]["CURRENT_STOCK"];
		$global_avg_rate=$global_stock_sql[0]["AVG_RATE_PER_UNIT"];
		
		$stock_qnty=return_field_value("sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end) - (case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_stock","inv_transaction","status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name $sqlLotCon","balance_stock");
		
		//######### this stock item store level and calculate rate ########//
		$store_stock_sql="select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT 
		from inv_transaction 
		where status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name";
		//echo "20**$store_stock_sql";disconnect($con);die;
		$store_stock_sql_result=sql_select($store_stock_sql);
		$store_item_rate=0;
		if($store_stock_sql_result[0]["BALANCE_AMT"]!=0 && $store_stock_sql_result[0]["BALANCE_STOCK"]!=0)
		{
			$store_item_rate=$store_stock_sql_result[0]["BALANCE_AMT"]/$store_stock_sql_result[0]["BALANCE_STOCK"];
		}
		
		if(str_replace("'","",$txt_issue_qnty)>$stock_qnty && str_replace("'","",$txt_issue_qnty)>$global_stock_qnty && $global_avg_rate >0)
		{
			echo "17**Issue Quantity Exceeds The Current Stock Quantity".str_replace("'","",$txt_issue_qnty)."=".$stock_qnty; disconnect($con);die;			
		}
		
		//product master table information
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$txt_prod_id");
		$avg_rate=$stock_qnty=$stock_value=0;
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_qnty = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
		}
		
		if(number_format($avg_rate,10,".","")==0)
		{
			echo "20**Rate Not Found.";disconnect($con);die;
		}
		
 		//issue master table entry here START---------------------------------------//	
		if( str_replace("'","",$cbo_item_category) == 22)
		{	
			if( str_replace("'","",$txt_system_no) == "" ) //new insert
			{
				//$id=return_next_id("id", "inv_issue_master", 1);		
				//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GIS', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_name and entry_form=21 $mrr_date_check order by id DESC ", "issue_number_prefix", "issue_number_prefix_num" ));
				
				$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
				$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_name),'GIS',21,date("Y",time())));
				
				$field_array_master="id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_purpose, entry_form, company_id, issue_date, loan_party, challan_no, req_no,remarks,store_sl_no, req_id, inserted_by, insert_date";
				$data_array_master="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',".$cbo_issue_purpose.",21,".$cbo_company_name.",".$txt_issue_date.",".$cbo_loan_party.",".$txt_challan_no.",".$txt_issue_req_no.",".$txt_remarks.",".$txt_store_sl_no.",'".$req_id."','".$user_id."','".$pc_date_time."')";
				//echo $field_array."<br>".$data_array;die;
				//$rID = sql_insert("inv_issue_master",$field_array_master,$data_array_master,1);
			}
			else //update
			{
                $new_mrr_number[0]=str_replace("'","",$txt_system_no);
				$id=str_replace("'","",$txt_system_id);
				$field_array_master="issue_purpose*issue_date*loan_party*challan_no*req_no*remarks*store_sl_no*req_id*updated_by*update_date";
				$data_array_master="".$cbo_issue_purpose."*".$txt_issue_date."*".$cbo_loan_party."*".$txt_challan_no."*".$txt_issue_req_no."*".$txt_remarks."*".$txt_store_sl_no."*'".$req_id."'*'".$user_id."'*'".$pc_date_time."'";
				//echo "20**".$field_array."<br>".$txt_system_id;die;
				//$rID=sql_update("inv_issue_master",$field_array_master,$data_array_master,"id",$id,1); 
            }
		}
		else
		{
			if( str_replace("'","",$txt_system_no) == "" ) //new insert
			{
				//$id=return_next_id("id", "inv_issue_master", 1);		
				//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GIS', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_name and entry_form=21 $mrr_date_check order by id DESC ", "issue_number_prefix", "issue_number_prefix_num" ));
				
				$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
				$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_name),'GIS',21,date("Y",time())));
				
				$field_array_master="id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_purpose, entry_form, company_id, issue_date, challan_no, req_no,remarks,store_sl_no, inserted_by, insert_date";
				$data_array_master="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',".$cbo_issue_purpose.",21,".$cbo_company_name.",".$txt_issue_date.",".$txt_challan_no.",".$txt_issue_req_no.",".$txt_remarks.",".$txt_store_sl_no.",'".$user_id."','".$pc_date_time."')";
				//echo "10**".$field_array."====".$data_array;die;
				//$rID = sql_insert("inv_issue_master",$field_array_master,$data_array_master,1);
			}
			else //update
			{
                $new_mrr_number[0]=str_replace("'","",$txt_system_no);
				$id=str_replace("'","",$txt_system_id);
				$field_array_master="issue_purpose*issue_date*challan_no*req_no*remarks*store_sl_no*updated_by*update_date";
				$data_array_master="".$cbo_issue_purpose."*".$txt_issue_date."*".$txt_challan_no."*".$txt_issue_req_no."*".$txt_remarks."*".$txt_store_sl_no."*'".$user_id."'*'".$pc_date_time."'";
				//echo "20**".$field_array."<br>".$txt_system_id;die;
				//$rID=sql_update("inv_issue_master",$field_array_master,$data_array_master,"id",$id,1);  
                
            }

	    }
		//issue master table entry here END---------------------------------------//
		 
		
		
		//inventory TRANSACTION table data entry START----------------------------------------------------------//	
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		if(str_replace("'","",$cbo_use_for)=="") $cbo_use_for=0;
 		$issue_stock_value = $avg_rate*$txt_issue_qnty;
		$issue_store_value = $store_item_rate*$txt_issue_qnty;		
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans_insert = "id,mst_id,company_id,prod_id,item_category,transaction_type,transaction_date,store_id,order_id,cons_uom,cons_quantity,cons_rate,cons_amount,production_floor,machine_id,item_return_qty,machine_category,floor_id,room,rack,self,bin_box,location_id,department_id,section_id,use_for,inserted_by,insert_date,batch_lot,store_rate,store_amount";
 		$data_array_trans_insert = "(".$transactionID.",".$id.",".$cbo_company_name.",".$txt_prod_id.",".$cbo_item_category.",2,".$txt_issue_date.",".$cbo_store_name.",".$txt_order_id.",".$cbo_uom.",".$txt_issue_qnty.",".number_format($avg_rate,10,'.','').",".number_format($issue_stock_value,8,'.','').",".$cbo_issue_floor.",".$cbo_machine_name.",".$txt_return_qty.",".$cbo_machine_category.",".$cbo_floor.",".$cbo_room.",".$cbo_rack.",".$cbo_self.",".$cbo_binbox.",".$cbo_location.",".$cbo_department.",".$cbo_section.",".$cbo_use_for.",'".$user_id."','".$pc_date_time."','".$txt_lot."',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")"; 
		
		//inventory TRANSACTION table data entry  END----------------------------------------------------------//
		
		//if LIFO/FIFO then START -----------------------------------------//
		$field_array_lifu_fifu = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,item_return_qty,rate,amount,inserted_by,insert_date";
		$update_array_lifu_fifu = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate=0; 
		$data_array_lifu_fifu="";
		$updateID_array_lifu_fifu=array();
		$update_data_lifu_fifu=array();
		$issueQnty = $txt_issue_qnty;
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		if($db_type==0)
		{		
			$returnString=return_field_value("concat(store_method,'_',allocation)","variable_settings_inventory","company_name=$cbo_company_name and variable_list=17 and item_category_id in($cbo_item_category) and status_active=1 and is_deleted=0");
		}
		else
		{
			$returnString=return_field_value("(store_method || '_' || allocation) as store_data","variable_settings_inventory","company_name=$cbo_company_name and variable_list=17 and item_category_id in($cbo_item_category) and status_active=1 and is_deleted=0","store_data");
		}
		
		$expString = explode("_",$returnString); 
		$isLIFOfifo = $expString[0];
		$check_allocation = $expString[1];
		 
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC"; 
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$txt_prod_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category and store_id=$cbo_store_name order by id $cond_lifofifo");			
		foreach($sql as $result)
		{
			$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);					
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty-$issueQnty; // minus issue qnty 
			$issueStockBalance = $balance_amount-($issueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{					
				$amount = $issueQnty*$cons_rate;
				//for insert
				if($data_array_lifu_fifu!="") $data_array_lifu_fifu .= ",";  
				$data_array_lifu_fifu .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$transactionID.",21,".$txt_prod_id.",".$issueQnty.",".$txt_return_qty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array_lifu_fifu[]=$recv_trans_id; 
				$update_data_lifu_fifu[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{
				$issueQntyBalance  = $issueQnty-$balance_qnty;				
				$issueQnty = $balance_qnty;				
				$amount = $issueQnty*$cons_rate;
				//for insert
				if($data_array_lifu_fifu!="") $data_array_lifu_fifu .= ",";  
				$data_array_lifu_fifu .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$transactionID.",21,".$txt_prod_id.",".$balance_qnty.",".$txt_return_qty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array_lifu_fifu[]=$recv_trans_id; 
				$update_data_lifu_fifu[$recv_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}
			//$mrrWiseIsID++;
		}//end foreach
 		// LIFO/FIFO then END-----------------------------------------------//
		 
 		//product master table data UPDATE START----------------------//
  		$currentStock   = $stock_qnty-$txt_issue_qnty;
		$StockValue	 	= 0;
		if ($currentStock  != 0){
			$StockValue	 	= $stock_value-($txt_issue_qnty*$avg_rate);
			$avgRate	 	= number_format($StockValue/$currentStock,10,'.','');			
		}

		$field_array_product	= "last_issued_qnty*item_return_qty*current_stock*stock_value*updated_by*update_date"; 
		$data_array_product	= "".$txt_issue_qnty."*".$txt_return_qty."*".$currentStock."*".number_format($StockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
		//------------------ product_details_master END--------------//
		if($variable_store_wise_rate==1)
		{
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_name");
			$store_up_id=0;
			if(count($sql_store)<1)
			{
				echo "20**No Data Found.";disconnect($con);die;
			}
			elseif(count($sql_store)>1)
			{
				echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
			}
			else
			{
				$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
				foreach($sql_store as $result)
				{
					$store_up_id=$result[csf("id")];
					$store_presentStock	=$result[csf("current_stock")];
					$store_presentStockValue =$result[csf("stock_value")];
					$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
				}
				
				$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
				$currentStock_store		=$store_presentStock-$txt_issue_qnty;
				$currentValue_store		=$store_presentStockValue-$issue_store_value;
				$data_array_store= "".$txt_issue_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			}
		}
		
		if( str_replace("'","",$txt_system_no) == "" ) //new insert
		{
			$rID = sql_insert("inv_issue_master",$field_array_master,$data_array_master,1);
 		}
		else //update
		{
			$rID=sql_update("inv_issue_master",$field_array_master,$data_array_master,"id",$id,1);
 		}
		$transID = sql_insert("inv_transaction",$field_array_trans_insert,$data_array_trans_insert,1);
		$prodUpdate 	= sql_update("product_details_master",$field_array_product,$data_array_product,"id",$txt_prod_id,1);
		$mrrWiseIssueID=true;
		if($data_array_lifu_fifu!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_lifu_fifu,$data_array_lifu_fifu,1);
		}
		//transaction table stock update here------------------------//
		$upTrID=$storeRID=true;
		if(count($updateID_array_lifu_fifu)>0)
		{
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_lifu_fifu,$update_data_lifu_fifu,$updateID_array_lifu_fifu),1);
		}
		$storeRID=true;
		if($store_up_id>0 && $variable_store_wise_rate==1)
		{
			$storeRID=sql_update("inv_store_wise_gen_qty_dtls",$field_array_store,$data_array_store,"id",$store_up_id,1);
		}
		
		 
 		$txt_serial_id 	= trim(str_replace("'","",$txt_serial_id));
		$serialUpdate=true;
 		if($txt_serial_id!="")
		{
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				if( (count($se_data)<=str_replace("'","",$txt_issue_qnty)))
				{
					$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
					$txt_serial_id_arr=explode(",",$txt_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("".$transactionID."*1*'".$user_id."'*'".$pc_date_time."'"));
						}
					}
					
					$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
					//$serialUpdate = execute_query("update inv_serial_no_details set issue_trans_id=$transactionID , is_issued=1 where id in ($txt_serial_id)",1);
				}
				else
				{
					echo "50";
					//check_table_status( $_SESSION['menu_id'], 0 );
					disconnect($con);
					die;
				}
			}
			else
			{
				$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
				$txt_serial_id_arr=explode(",",$txt_serial_id);
				if(count($txt_serial_id_arr)>0)
				{
					foreach($txt_serial_id_arr as $serial_id)
					{
						$update_data_serial[$serial_id]=explode("*",("".$transactionID."*1*'".$user_id."'*'".$pc_date_time."'"));
					}
				}
				
				$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
				
				//$serialUpdate 	= execute_query("update inv_serial_no_details set issue_trans_id=$transactionID, is_issued=1 where id in ($txt_serial_id)",1);
				
			}
		}
		//echo "10**".$rID." && ".$transID." && ".$mrrWiseIssueID." && ".$upTrID." && ".$prodUpdate." && ".$serialUpdate." && ".$storeRID;die;
		//mysql_query("ROLLBACK");die; 
 		 
		//release lock table   oci_commit($con); oci_rollback($con); 
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $transID && $mrrWiseIssueID && $upTrID && $prodUpdate && $serialUpdate && $storeRID)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_mrr_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_mrr_number[0]."**".$id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $transID && $mrrWiseIssueID && $upTrID && $prodUpdate && $serialUpdate && $storeRID)
			{
				oci_commit($con);  
				echo "0**".$new_mrr_number[0]."**".$id;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$new_mrr_number[0]."**".$id;
			}
		}
		//check_table_status( $_SESSION['menu_id'], 0 );
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}  
		//check update id
		if( str_replace("'","",$update_id) == "" ||  str_replace("'","",$txt_system_no)=="" )
		{
			echo "10";
			//check_table_status( $_SESSION['menu_id'], 0 );
			disconnect($con);
			die(); 
		}
		//variable_list=17 is_allocated,  item_category_id=1 is yarn--------------------
		if($db_type==0)
		{		
			$returnString=return_field_value("concat(store_method,'_',allocation)","variable_settings_inventory","company_name=$cbo_company_name and variable_list=17 and item_category_id in($cbo_item_category) and status_active=1 and is_deleted=0");
		}
		else
		{
			$returnString=return_field_value("(store_method || '_' || allocation) as store_data","variable_settings_inventory","company_name=$cbo_company_name and variable_list=17 and item_category_id in($cbo_item_category) and status_active=1 and is_deleted=0","store_data");
		}
		$expString = explode("_",$returnString); 
		$isLIFOfifo = $expString[0];
		$check_allocation = $expString[1];
		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select( "select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, b.cons_quantity,b.item_return_qty, b.cons_amount, b.store_amount from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and b.transaction_type=2");
		
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=$before_stock_value=$before_prod_rate=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
 			$before_stock_qnty = $result[csf("current_stock")];
			$before_prod_rate = $result[csf("avg_rate_per_unit")];
			$before_stock_value = $result[csf("stock_value")]; 
			$before_issue_qnty = $result[csf("cons_quantity")];
			$before_return_qty = $result[csf("item_return_qty")];
			$before_issue_value = $result[csf("cons_amount")]; 
			$before_store_amount = $result[csf("store_amount")];
		}
		//current product ID
		$txt_prod_id = str_replace("'","",$current_prod_id);
		$txt_issue_qnty = str_replace("'","",$txt_issue_qnty);
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$txt_prod_id");
		$curr_avg_rate=$curr_stock_qnty=$curr_stock_value=0;
		foreach($sql as $result)
		{
			$curr_avg_rate 	   = $result[csf("avg_rate_per_unit")];
			$curr_stock_qnty 	 = $result[csf("current_stock")];
			$curr_stock_value 	= $result[csf("stock_value")]; 
		}
		
		if(number_format($curr_avg_rate,10,".","")==0)
		{
			echo "20**Rate Not Found.";disconnect($con);die;
		}
		
		$max_transaction_id = return_field_value("max(id) as max_trans_id", "inv_transaction", "prod_id=$before_prod_id and transaction_type in(1,4,5) and status_active = 1", "max_trans_id");
		if($max_transaction_id > str_replace("'","",$update_id))
		{
			echo "20**Next Transaction Found, Update Not Allow";disconnect($con);die;
		}
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//		
		
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = $curr_stock_qnty+$before_issue_qnty-$txt_issue_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty 
 			//$adj_stock_val  = $curr_stock_value+$before_issue_value-($txt_issue_qnty*$curr_avg_rate); // CurrentStockValue + Before Issue Value - Current Issue Value
 			$adj_stock_val=0;
 			if ($adj_stock_qnty != 0){
 				$adj_stock_val  = $adj_stock_qnty*$curr_avg_rate; 				
 			} 

 			$update_array_prod	= "last_issued_qnty*item_return_qty*current_stock*stock_value*updated_by*update_date"; //*allocated_qnty*available_qnty
 			$data_array_prod		= "".$txt_issue_qnty."*".$txt_return_qty."*".$adj_stock_qnty."*".number_format($adj_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
			
			//$adj_avgrate	= number_format($adj_stock_val/$adj_stock_qnty,$dec_place[3],'.','');
			 
			if($variable_lot==1  && str_replace("'","",$cbo_item_category)==22)
			{
				$sqlLotCon.= " and batch_lot='$txt_lot'" ;
			}
			$stock_qnty=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock","inv_transaction","status_active=1 and prod_id=$before_prod_id and store_id=$cbo_store_name $sqlLotCon","balance_stock");
			$latest_current_stock=$stock_qnty+$before_issue_qnty;
			//now current stock
			$curr_avg_rate 		= $curr_avg_rate;
			$curr_stock_qnty 	= $adj_stock_qnty;
			$curr_stock_value 	= $adj_stock_val;
			
			$global_stock_sql=sql_select("select AVG_RATE_PER_UNIT, CURRENT_STOCK from PRODUCT_DETAILS_MASTER where id=$before_prod_id");
			$global_stock_qnty=$global_stock_sql[0]["CURRENT_STOCK"]+$before_issue_qnty;
			$global_avg_rate=$global_stock_sql[0]["AVG_RATE_PER_UNIT"];
		}
		else
		{
			$updateID_array = $update_data = array();
			
			if($variable_lot==1  && str_replace("'","",$cbo_item_category)==22)
			{
				$sqlLotCon.= " and batch_lot='$txt_lot'" ;
			}
			$latest_current_stock=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock","inv_transaction","status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name $sqlLotCon","balance_stock");
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty+$before_issue_qnty; // CurrentStock + Before Issue Qnty
			$adj_before_stock_val=0;
			$updateID_array_prod[]=$before_prod_id;
			if ($adj_before_stock_qnty != 0){
				$adj_before_stock_val  	 = $before_stock_value+$before_issue_value; // CurrentStockValue + Before Issue Value				
			} 

			$update_array_prod	= "last_issued_qnty*item_return_qty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod[$before_prod_id]=explode("*",("".$before_issue_qnty."*".$before_return_qty."*".$adj_before_stock_qnty."*".number_format($adj_before_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'"));
			
			//$adj_before_avgrate	   = number_format($adj_before_stock_val/$adj_before_stock_qnty,$dec_place[3],'.','');
			$adj_before_avgrate	   = $before_prod_rate;
			
			
 			//current product adjust
			$adj_curr_stock_qnty  = $curr_stock_qnty-$txt_issue_qnty; // CurrentStock + Before Issue Qnty
			$adj_curr_stock_val=0;
			$updateID_array_prod[]=$txt_prod_id;
			if ($adj_curr_stock_qnty != 0){
				$adj_curr_stock_val  = $adj_curr_stock_qnty*$curr_avg_rate;				
			} 

			$update_array_prod	= "last_issued_qnty*item_return_qty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod[$txt_prod_id]=explode("*",("".$txt_issue_qnty."*".$txt_return_qty."*".$adj_curr_stock_qnty."*".number_format($adj_curr_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'"));
			
			//now current stock
			$curr_avg_rate 		= $curr_avg_rate;
			$curr_stock_qnty 	= $adj_curr_stock_qnty;
			$curr_stock_value 	= $adj_curr_stock_val;
			
			$global_stock_sql=sql_select("select AVG_RATE_PER_UNIT, CURRENT_STOCK from PRODUCT_DETAILS_MASTER where id=$txt_prod_id");
			$global_stock_qnty=$global_stock_sql[0]["CURRENT_STOCK"];
			$global_avg_rate=$global_stock_sql[0]["AVG_RATE_PER_UNIT"];
		}
		if(str_replace("'","",$txt_issue_qnty)>$latest_current_stock && str_replace("'","",$txt_issue_qnty)>$global_stock_qnty && $global_avg_rate >0)
		{
			echo "17**Issue Quantity Exceeds The Current Stock Quantity";
			//check_table_status( $_SESSION['menu_id'], 0 );
			disconnect($con); 
			die;			
		}
  		//------------------ product_details_master END--------------//
		//----------Store wise table start here-------------------------//
		$up_conds="";
		if(str_replace("'","",$update_id)) $up_conds=" and id <> $update_id";
		$store_stock_sql="select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT 
		from inv_transaction 
		where status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name $up_conds";
		//echo "20**$store_stock_sql";disconnect($con);die;
		$store_stock_sql_result=sql_select($store_stock_sql);
		$store_item_rate=0;
		if($store_stock_sql_result[0]["BALANCE_AMT"]!=0 && $store_stock_sql_result[0]["BALANCE_STOCK"]!=0)
		{
			$store_item_rate=$store_stock_sql_result[0]["BALANCE_AMT"]/$store_stock_sql_result[0]["BALANCE_STOCK"];
		}
		
		$issue_store_value=$txt_issue_qnty*$store_item_rate;
		if($variable_store_wise_rate==1)
		{
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_name");
			$store_up_id=0;
			if(count($sql_store)<1)
			{
				echo "20**No Data Found.";disconnect($con);die;
			}
			elseif(count($sql_store)>1)
			{
				echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
			}
			else
			{
				$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
				foreach($sql_store as $result)
				{
					$store_up_id=$result[csf("id")];
					$store_presentStock	=$result[csf("current_stock")];
					$store_presentStockValue =$result[csf("stock_value")];
					$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
				}
				$adj_beforeStock_store			=$store_presentStock+$before_issue_qnty;
				$adj_beforeStockValue_store		=$store_presentStockValue+$before_store_amount;
				
				$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
				$currentStock_store		=$adj_beforeStock_store-$txt_issue_qnty;
				$currentValue_store		=$adj_beforeStockValue_store-$issue_store_value;
				$data_array_store= "".$txt_issue_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			}
		}
		//----------Store wise table end here-------------------------//
		 		
 		//transaction table START--------------------------//
		
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=21");
		
		$updateID_array_trans = array();
		$update_data_trans = array();
		foreach($sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
			$updateID_array_trans[]=$result[csf("id")]; 
			$update_data_trans[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
			
			$trans_data_array[$result[csf("id")]]['qnty']=$adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt']=$adjAmount;
		}
		
		
		
		//****************************************** NEW ENTRY START *****************************************//
		
		//issue master update START--------------------------------------//
		if( str_replace("'","",$cbo_item_category) == 22)
		{
			$field_array_update_issue="issue_purpose*issue_date*loan_party*challan_no*req_no*remarks*store_sl_no*req_id*updated_by*update_date";
			$data_array_update_issue="".$cbo_issue_purpose."*".$txt_issue_date."*".$cbo_loan_party."*".$txt_challan_no."*".$txt_issue_req_no."*".$txt_remarks."*".$txt_store_sl_no."*'".$req_id."'*'".$user_id."'*'".$pc_date_time."'";

		}
		else
		{
			$field_array_update_issue="issue_purpose*issue_date*challan_no*req_no*remarks*store_sl_no*req_id*updated_by*update_date";
			$data_array_update_issue="".$cbo_issue_purpose."*".$txt_issue_date."*".$txt_challan_no."*".$txt_issue_req_no."*".$txt_remarks."*".$txt_store_sl_no."*'".$req_id."'*'".$user_id."'*'".$pc_date_time."'";
		}
		
		//issue master update END---------------------------------------// 
 		 
		//inventory TRANSACTION table data UPDATE START----------------------------------------------------------//	
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		if(str_replace("'","",$cbo_use_for)=="") $cbo_use_for=0;
		$avg_rate = $curr_avg_rate; // asign current rate
 		$issue_stock_value = $avg_rate*$txt_issue_qnty;
		
		$field_array_again = "prod_id*item_category*transaction_type*transaction_date*store_id*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*production_floor*machine_id*item_return_qty*machine_category*floor_id*room*rack*self*bin_box*location_id*department_id*section_id*use_for*updated_by*update_date*batch_lot*store_rate*store_amount";
 		$data_array_again = "".$txt_prod_id."*".$cbo_item_category."*2*".$txt_issue_date."*".$cbo_store_name."*".$txt_order_id."*".$cbo_uom."*".$txt_issue_qnty."*".number_format($avg_rate,10,'.','')."*".number_format($issue_stock_value,8,'.','')."*".$cbo_issue_floor."*".$cbo_machine_name."*".$txt_return_qty."*".$cbo_machine_category."*".$cbo_floor."*".$cbo_room."*".$cbo_rack."*".$cbo_self."*".$cbo_binbox."*".$cbo_location."*".$cbo_department."*".$cbo_section."*".$cbo_use_for."*'".$user_id."'*'".$pc_date_time."'*'".$txt_lot."'*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""; 
		//echo $field_array."<br>".$data_array;."-".;
		
		
		//$transID = sql_update("inv_transaction",$field_array_again,$data_array_again,"id",$update_id,0);
 		//inventory TRANSACTION table data UPDATE  END----------------------------------------------------------//
		
		//if LIFO/FIFO then START -----------------------------------------//
		$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,item_return_qty,rate,amount,inserted_by,insert_date";
		$update_array_tran = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate=0; 
		/*$updateID_array_tran_up=array();
		$update_data_tran_up=array();
		$update_data_mrr_insert=array();
		*/$issueQnty = $txt_issue_qnty;
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$txt_prod_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category and store_id=$cbo_store_name order by id $cond_lifofifo");
 		foreach($sql as $result)
		{
			$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);				
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			if($trans_data_array[$issue_trans_id]['qnty']=="")
			{
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
			}
			else
			{
				$balance_qnty = $trans_data_array[$issue_trans_id]['qnty'];
				$balance_amount = $trans_data_array[$issue_trans_id]['amnt'];
			}
			
			/*$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];*/
			$cons_rate = $result[csf("cons_rate")]; 
			$issueQntyBalance = $balance_qnty-$issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($issueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{
				$amount = $issueQnty*$cons_rate;
				//for insert
				if($update_data_mrr_insert!="") $update_data_mrr_insert .= ",";  
				$update_data_mrr_insert .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$update_id.",21,".$txt_prod_id.",".$issueQnty.",".$txt_return_qty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array_tran_up[]=$issue_trans_id; 
				$update_data_tran_up[$issue_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{
				$issueQntyBalance  = $issueQnty-$balance_qnty;				
				$issueQnty = $balance_qnty;				
				$amount = $issueQnty*$cons_rate;
				
				//for insert
				if($update_data_mrr_insert!="") $update_data_mrr_insert .= ",";  
				$update_data_mrr_insert .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$update_id.",21,".$txt_prod_id.",".$issueQnty.",".$txt_return_qty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
				//echo "20**".$data_array;die;
				//for update
				$updateID_array_tran_up[]=$issue_trans_id; 
				$update_data_tran_up[$issue_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}
			//$mrrWiseIsID++;
		}
		
		
		//****************************************** All query execute Bellow*****************************************//
		
		$query1=$query2=$query3=$rID=$transID=$mrrWiseIssueID=$upTrID=$serialUpdate=$serialDelete=$storeRID=true;
		if($before_prod_id==$txt_prod_id)
		{
 			$query1 		= sql_update("product_details_master",$update_array_prod,$data_array_prod,"id",$before_prod_id,0);
		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$data_array_prod,$updateID_array_prod),0);
		}
		
		if(count($updateID_array_trans)>0)//update receive trans row
		{
 			$query2=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_trans,$update_data_trans,$updateID_array_trans),0);
		}
		
		
		if(count($update_data_trans)>0)
		{
			 $updateIDArray = implode(",",$update_data_trans);
			 $query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=21",0);
		}
		
		
		if(trim(str_replace("'","",$txt_system_id))!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_update_issue,$data_array_update_issue,"id",$txt_system_id,1);
		}
		
		$transID = sql_update("inv_transaction",$field_array_again,$data_array_again,"id",$update_id,0);
		
		
		if($update_data_mrr_insert!="")
		{	 
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$update_data_mrr_insert,0);
		}
		//transaction table stock update here------------------------//
		if(count($updateID_array_tran_up)>0)
		{
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_tran,$update_data_tran_up,$updateID_array_tran_up),0);
		}
		
		$txt_serial_id 	= trim(str_replace("'","",$txt_serial_id));
 		if($txt_serial_id!="")
		{
			$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
			$before_serial_id=trim(str_replace("'","",$before_serial_id));$txt_serial_id=trim(str_replace("'","",$txt_serial_id));$update_id=trim(str_replace("'","",$update_id));
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				if( (count($se_data)<=str_replace("'","",$txt_issue_qnty)))
				{
					if($before_serial_id !="")
					{
						$txt_before_serial_id_arr=explode(",",$before_serial_id);
						if(count($txt_before_serial_id_arr)>0)
						{
							foreach($txt_before_serial_id_arr as $serial_id)
							{
								$update_data_before_serial[$serial_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
							}
							$serialDelete=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_before_serial,$txt_before_serial_id_arr),1);
							//$serialDelete=execute_query("update inv_serial_no_details set issue_trans_id=0 , is_issued=0 where id in ($before_serial_id)",0);
						}
					}
					$txt_serial_id_arr=explode(",",$txt_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("".$update_id."*1*'".$user_id."'*'".$pc_date_time."'"));
						}
					}
					$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
					
				}
				else
				{
					echo "50";
					//check_table_status( $_SESSION['menu_id'], 0 );
					disconnect($con);
					die;
				}
			}
			else
			{
				
				if($before_serial_id !="")
				{
					//echo "nahid";die;
					$txt_before_serial_id_arr=explode(",",$before_serial_id);
					if(count($txt_before_serial_id_arr)>0)
					{
						foreach($txt_before_serial_id_arr as $serial_id)
						{
							$update_data_before_serial[$serial_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
						}
						$serialDelete=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_before_serial,$txt_before_serial_id_arr),1);
					}
				}
				//echo $serialDelete;die;
				$txt_serial_id_arr=explode(",",$txt_serial_id);
				if(count($txt_serial_id_arr)>0)
				{
					foreach($txt_serial_id_arr as $serial_id)
					{
						$update_data_serial[$serial_id]=explode("*",("".$update_id."*1*'".$user_id."'*'".$pc_date_time."'"));
					}
				}
				$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
			}
		}
		$storeRID=true;
		if($store_up_id>0 && $variable_store_wise_rate==1)
		{
			$storeRID=sql_update("inv_store_wise_gen_qty_dtls",$field_array_store,$data_array_store,"id",$store_up_id,1);
		}
		//$query1 $transID $mrrWiseIssueID
		//echo $query1 ."&&". $query2 ."&&". $query3 ."&&". $rID ."&&". $transID ."&&". $mrrWiseIssueID ."&&". $upTrID ."&&". $serialUpdate  ."&&". $serialDelete  ."&&". $storeRID;die;
		
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($query1 &&  $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID && $serialUpdate  && $serialDelete && $storeRID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id);
			}
		}
		//$query1=$query2=$query3=$rID=$transID=$mrrWiseIssueID=$upTrID=$serialUpdate=$serialDelete    
		if($db_type==2 || $db_type==1 )
		{
			if($query1 &&  $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID && $serialUpdate  && $serialDelete && $storeRID)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id);
			}
		}
		//check_table_status( $_SESSION['menu_id'], 0 );
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = str_replace("'","",$txt_system_id);
		if($mst_id=="" || $mst_id==0)
		{ 
			echo "16**Delete not allowed. Problem occurred"; disconnect($con);die;
		}
		else 
		{
			$update_id = str_replace("'","",$update_id);
			$product_id = str_replace("'","",$current_prod_id);
			if( str_replace("'","",$update_id) == "" )
			{
				echo "16**Delete not allowed. Problem occurred"; disconnect($con);die;
			}
			$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(1,2,3,4,5,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id ","id");
			if($chk_next_transaction !="")
			{ 
				echo "17**Delete not allowed.This item is used in another transaction"; disconnect($con);die;
			}
			else
			{
				$mrr_table_id=return_field_value("id","inv_mrr_wise_issue_details","prod_id=$product_id and status_active=1 and is_deleted=0 and issue_trans_id=$update_id ","id");

				$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount, a.store_amount,b.current_stock,b.stock_value from inv_transaction a, product_details_master b where a.status_active=1 and a.id=$update_id and a.prod_id=b.id");
			
				$before_prod_id=$before_receive_qnty=$before_rate=$beforeAmount=$before_brand="";
				$beforeStock=$beforeStockValue=0;
				foreach( $sql as $row)
				{
					$before_prod_id 		= $row[csf("prod_id")]; 
					$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
					$before_rate 			= $row[csf("cons_rate")]; 
					$beforeAmount			= $row[csf("cons_amount")]; //stock value
					$beforeStoreAmount		= $row[csf("store_amount")];
					$beforeStock			=$row[csf("current_stock")];
					$beforeStockValue		=$row[csf("stock_value")];
					//$beforeAvgRate			=$row[csf("avg_rate_per_unit")];	
				}
				//stock value minus here---------------------------//
				$adj_beforeStock			=$beforeStock+$before_receive_qnty;
				$adj_beforeStockValue=0;
				//$adj_beforeAvgRate			=number_format(($adj_beforeStockValue/$adj_beforeStock),$dec_place[3],'.','');		
				if ($adj_beforeStock != 0) {
					$adj_beforeStockValue	=$beforeStockValue+$beforeAmount;					
				}
				if($variable_store_wise_rate==1)
				{
					$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$before_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_name");
					$store_up_id=0;
					if(count($sql_store)<1)
					{
						echo "20**No Data Found.";disconnect($con);die;
					}
					elseif(count($sql_store)>1)
					{
						echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
					}
					else
					{
						$store_presentStock=$store_presentStockValue=$store_presentAvgRate=$store_before_receive_qnty=0;
						foreach($sql_store as $result)
						{
							$store_up_id=$result[csf("id")];
							$store_presentStock	=$result[csf("current_stock")];
							$store_presentStockValue =$result[csf("stock_value")];
							$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
						}
						$currentStock_store		=$store_presentStock+$before_receive_qnty;
						$currentValue_store		=$store_presentStockValue+$beforeStoreAmount;
						
						$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
						$data_array_store= "".$before_receive_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
					}
				}

				$field_array_product="current_stock*stock_value*updated_by*update_date";
				$data_array_product = "".$adj_beforeStock."*".number_format($adj_beforeStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";

				$sql_mst = sql_select("select id from inv_transaction where status_active=1 and is_deleted=0 and transaction_type=2 and mst_id=$mst_id");
				
				if(count($sql_mst)==1)
				{
					$field_array_mst="updated_by*update_date*status_active*is_deleted";
					$data_array_mst="".$user_id."*'".$pc_date_time."'*0*1";

					$rID4=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$mst_id,1);
					$resetLoad=1;
				}
				else
				{
					$rID4=1;
					$resetLoad=2;
				}
				
				$field_array_trans="updated_by*update_date*status_active*is_deleted";
				$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";

				$field_array_mrr="updated_by*update_date*status_active*is_deleted";
				$data_array_mrr="".$user_id."*'".$pc_date_time."'*0*1";
				
				$rID=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
				$rID2=sql_update("product_details_master",$field_array_product,$data_array_product,"id",$product_id,1);
				$rID3=sql_update("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,"issue_trans_id",$update_id,1);
				$storeRID=true;
				if($store_up_id>0 && $variable_store_wise_rate==1)
				{
					$storeRID=sql_update("inv_store_wise_gen_qty_dtls",$field_array_store,$data_array_store,"id",$store_up_id,1);
				}
			}
		}
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $storeRID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$resetLoad;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$resetLoad;
			}
		}
		if($db_type==2 || $db_type==1)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $storeRID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$resetLoad;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$resetLoad;
			}
		}
		disconnect($con);
		die;
	}		
}

if($action=="search_by_drop_down")
{
	echo create_drop_down( "cbo_item_category", 150, $item_category,"", 1, "-- Select --", 0, "", 1,"4,8,9,10,11,15,16,17,18,19,20,21,22" );
}

if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
<script>
	function js_set_value(sys_id)
	{
 		$("#hidden_sys_id").val(sys_id); // mrr number
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
                    <th width="100">Search By</th>
                    <th width="250" align="center" id="search_by_td_up">Enter Issue No</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr> 
                    <td> 
                        <?  
 							$search_by = array(1=>'Issue No',2=>'Req No',3=>'Challan No',4=>'Item Category');
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+'<? echo $item_cat; ?>', 'create_mrr_search_list_view', 'search_div', 'general_item_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here -->
                     <input type="hidden" id="hidden_sys_id" value="hidden_sys_id" />
                    <!-- END -->
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
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
 	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	$item_cat = $ex_data[5];
 	$company_arr = return_library_array("select id, company_name from lib_company",'id','company_name');
 	$store_arr = return_library_array("select id, store_name from lib_store_location",'id','store_name');

 	$sql_cond="";
	if($fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd','',-1)."' and '".change_date_format($toDate,'yyyy-mm-dd','',-1)."'";
		}
	}
 	if($company!="" && $company*1!=0) $sql_cond .= " and a.company_id='$company'";
	
	
 	if($txt_search_common!="" || $txt_search_common!=0)
	{
		if($txt_search_by==1)
		{	
			$sql_cond .= " and a.issue_number like '%$txt_search_common%'";			
		}
		else if($txt_search_by==2)
		{		
			$sql_cond .= " and a.req_no like '%$txt_search_common%'";	
 		}
		else if($txt_search_by==3)
		{
			$sql_cond .= " and a.challan_no like '%$txt_search_common%'";	
		} 
	}
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
	$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];
	
	$credientian_cond="";
	if($cre_company_id!="") $credientian_cond=" and a.company_id in($cre_company_id)";
	//if($cre_supplier_id!="") $credientian_cond.=" and a.supplier_id in($cre_supplier_id)";
	if($cre_store_location_id!="") $credientian_cond.=" and b.store_id in($cre_store_location_id)";
	if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";
		
	$sql = "select a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date, sum(b.cons_quantity) as cons_quantity
	from inv_issue_master a, inv_transaction b
	where a.id=b.mst_id and a.entry_form=21 and b.transaction_type=2 and b.item_category in($item_cat) and a.status_active=1 and b.status_active=1 $sql_cond $credientian_cond
	group by a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date
	order by a.issue_number";
	//echo $sql;die;
	$result = sql_select( $sql );
	?>
    	<div>
            <div style="width:720px;">
                <table cellspacing="0" cellpadding="0" width="720" class="rpt_table" rules="all" border="1">
                    <thead>
                        <th width="50">SL</th>
                        <th width="150">Issue No</th>				
                        <th width="120">Date</th>              
                        <th width="120">Purpose</th>               
                        <th width="120">Req No</th>
                        <th >Issue Qnty</th> 
                    </thead>
                </table>
             </div>
            <div style="width:720px;overflow-y:scroll; min-height:200px; max-height:210px;" id="search_div" >
                <table cellspacing="0" cellpadding="0" width="702" class="rpt_table" id="list_view"  rules="all" border="1">
        <?	
            $i=1;   
            foreach( $result as $row ){
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
					
				//$issuQnty = return_field_value("sum(cons_quantity) as cons_quantity","inv_transaction","mst_id=".$row[csf("id")]." and transaction_type=2 and item_category not in (1,2,3,5,6,7,12,13,14) group by mst_id","cons_quantity");	
        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onclick="js_set_value('<? echo $row[csf("id")];?>');"> 
                            <td width="50" align="center"><? echo $i; ?></td>	
                            <td width="150"><p><? echo $row[csf("issue_number")];?></p></td>              	            			
                            <td width="120"><p><? echo $row[csf("issue_date")]; ?></p></td>								
                            <td width="120"><p><? echo $general_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>					
                            <td width="120"><p><? echo $row[csf("req_no")]; ?></p></td>
                            <td  align="right"><p><? echo number_format($row[csf("cons_quantity")],4); ?></p></td> 
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


if($action=="populate_data_from_data")
{
	$sql = "select id, issue_number, issue_purpose, company_id, issue_date, loan_party, challan_no, req_no, remarks,store_sl_no,is_posted_account from inv_issue_master  where id='$data' and entry_form=21";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_store_sl_no').val('".$row[csf("store_sl_no")]."');\n";
        echo "$('#txt_system_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#txt_system_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_company_name').val(".$row[csf("company_id")].");\n";
  		echo "$('#cbo_issue_purpose').val('".$row[csf("issue_purpose")]."');\n";
		echo "$('#hidden_posted_in_account').val('".$row[csf("is_posted_account")]."');\n";
		if($row[csf("issue_purpose")]==5)
		{
			echo"load_drop_down( 'requires/general_item_issue_controller', ".$row[csf("company_id")].", 'load_drop_down_loan_party', 'loan_party_td' );\n";
			echo "$('#cbo_loan_party').val('".$row[csf("loan_party")]."');\n";
		}
 		echo "$('#txt_issue_date').val('".change_date_format($row[csf("issue_date")])."');\n";
 		echo "$('#txt_issue_req_no').val('".$row[csf("req_no")]."');\n";
 		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
  		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		//clear child form
		echo "fnc_loan_party('".$row[csf("issue_purpose")]."');\n";
		echo "$('#tbl_child').find('select,input').val('');\n";
  	}
	exit();	
}

if($action=="show_dtls_list_view")
{
	$ex_data = explode("**",$data);
	$issue_number_id = $ex_data[0];
 	
	$cond="";
	if($issue_number_id!="") $cond .= " and a.id='$issue_number_id'";
 	
	$location_arr=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$po_no_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category not in (1,2,3,4,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');
	
	$sql = "select a.issue_number, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.item_description, c.item_group_id, b.order_id 
			from inv_issue_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 $cond";
			//echo $sql;
			
	$result = sql_select($sql);
	$i=1;
	$total_qnty=0;
	?> 
    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:1000px" rules="all">
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Category</th>
                    <th>Group</th>
                    <th>Description</th>
                    <th>Store</th>
                    <th>Issue Qnty</th>
                    <th>UOM</th>
                    <th>Serial No</th>
                    <th>Machine Categ.</th>
                    <th>Machine No</th>
                    <th>Buyer Order</th>
                    <th>Loc./Dept./Sec.</th>
                </tr>
            </thead>
            <tbody>
            	<? foreach($result as $row){  
					
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF"; 
					if($db_type==0)
					{
						$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
						$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
					}
					else
					{
						$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
						$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
					}
					$total_qnty +=	$row[csf("cons_quantity")];
 				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")];?>","child_form_input_data","requires/general_item_issue_controller")' style="cursor:pointer" >
                        <td width="30"><? echo $i; ?></td>
                        <td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
                        <td width="100"><p><? echo $group_arr[$row[csf("item_group_id")]]; ?></p></td>
                        <td width="100"><p><? echo $row[csf("item_description")]; ?></p></td>
                        <td width="90"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("cons_quantity")],2); ?></p></td>
                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                        <td width="50"><p><? echo $serialNo; ?></p></td>
                        <td width="70"><p><? echo $machine_category[$row[csf("machine_category")]]; ?></p></td>
                        <td width="50"><p><? echo $machine_arr[$row[csf("machine_id")]]; ?></p></td>
                        <td width="80"><p><? echo $po_no_arr[$row[csf("order_id")]]; ?></p></td>
                        <td width="170"><p><? echo $location_arr[$row[csf("location_id")]].', '.$department_arr[$row[csf("department_id")]].', '.$section_arr[$row[csf("section_id")]]; ?></p></td>
                   </tr>
                <? $i++; } ?>
                	<tfoot>
                            <th colspan="5" align="right">Total :</th>
                            <th><? echo number_format($total_qnty,2); ?></th>
                            <th colspan="6">&nbsp;</th>                            
                     </tfoot>
            </tbody>
        </table>
    <?
	exit();
}

if($action=="child_form_input_data")
{
	$rcv_dtls_id = $data;	
 	
	/*$sql = "select b.id, b.location_id, b.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id,b.item_return_qty, b.cons_quantity, c.current_stock, b.store_id, b.cons_uom, b.order_id, b.floor_id, b.machine_id, b.machine_category, b.location_id, b.department_id, b.section_id, b.room, b.rack, b.self, b.bin_box, b.use_for 
			from inv_transaction b, product_details_master c
			where b.prod_id=c.id and b.id='$rcv_dtls_id' and b.transaction_type=2 and b.item_category not in (1,2,3,5,6,7,12,13,14)"; */

	/*new dev	*/	
	
	$sql = "SELECT b.id, b.location_id, b.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id,b.item_return_qty, b.cons_quantity, c.current_stock, b.store_id, b.cons_uom, b.order_id, b.floor_id, b.machine_id, b.machine_category, b.location_id, b.department_id, b.section_id, b.room, b.rack, b.self, b.bin_box, b.use_for ,c.brand_name,c.origin,c.model, b.production_floor, b.batch_lot
	from inv_transaction b, product_details_master c
	where b.prod_id=c.id and b.id='$rcv_dtls_id' and b.transaction_type=2 and b.item_category not in (1,2,3,5,6,7,12,13,14)";
	//echo $sql;die;
	$result = sql_select($sql);
    $com_id=$result[0][csf("company_id")];
	$item_category_id=$result[0][csf("item_category_id")];
	$variable_lot=0;
	if($item_category_id==22)
	{
		$variable_lot=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name= $com_id and variable_list=32 and status_active=1 and is_deleted=0");
	}
	
	//echo $com_id;die;
	foreach($result as $row)
	{
		echo "$('#txt_item_desc').val('".$row[csf("item_description")]."');\n";
		echo "$('#cbo_item_category').val(".$row[csf("item_category_id")].");\n";
		echo "$('#cbo_item_group').val(".$row[csf("item_group_id")].");\n";
		echo "$('#txt_issue_qnty').val(".$row[csf("cons_quantity")].");\n";
		echo "$('#txt_return_qty').val(".$row[csf("item_return_qty")].");\n";
		echo "$('#hidden_p_issue_qnty').val(".$row[csf("cons_quantity")].");\n"; 
 		//echo "$('#txt_current_stock').val(".($row[csf("current_stock")]+$row[csf("cons_quantity")]).");\n";
 		echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("location_id")]."+'__'+".$row[csf('company_id')].", 'load_drop_down_store','store_td');\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
		echo "$('#cbo_store_name').attr('disabled', true);\n";
 		echo "$('#cbo_uom').val(".$row[csf("cons_uom")].");\n";
		
		echo "$('#cbo_item_category').attr('disabled', true);\n";
 		echo "$('#txt_item_desc').attr('disabled', true);\n";
 		echo "$('#cbo_item_group').attr('disabled', true);\n";
		$lot_cond="";
		if($item_category_id==22 && $variable_lot==1) $lot_cond=" and batch_lot='".$row[csf("batch_lot")]."'";
		$currnet_stock=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock","inv_transaction","status_active=1 and prod_id='".$row[csf("prod_id")]."' and store_id='".$row[csf("store_id")]."' $lot_cond","balance_stock");
		echo "$('#txt_current_stock').val(".($currnet_stock+$row[csf("cons_quantity")]).");\n";
		
		//$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		//$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		if($db_type==0)
		{
			$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
			$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		}
		else
		{
			$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
			$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
		}
		echo "$('#txt_serial_no').val('".$serialNo."');\n";
		echo "$('#txt_serial_id').val('".$serialID."');\n";
		echo "$('#before_serial_id').val('".$serialID."');\n";



		/*echo "load_drop_down( 'requires/general_item_issue_controller',".$row[csf("company_id")]."+'_'+".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_issue_floor').val(".$row[csf("floor_id")].");\n";*/



		echo "$('#cbo_machine_category').val(".$row[csf("machine_category")].");\n";
		echo "load_drop_down( 'requires/general_item_issue_controller',".$row[csf("company_id")]."+'_'+".$row[csf("location_id")]."+'_'+".$row[csf("machine_category")].", 'load_drop_down_floor', 'issue_floor_td' );\n";		
		echo "$('#cbo_issue_floor').val(".$row[csf("production_floor")].");\n";


		if($row[csf("production_floor")]!="") $prod_floor=$row[csf("production_floor")]; else $prod_floor=0;
		echo "load_drop_down( 'requires/general_item_issue_controller',".$row[csf("company_id")]."+'_'+".$row[csf("machine_category")]."+'_'+".$prod_floor.", 'load_drop_machine', 'machine_td' );\n";
		echo "$('#cbo_machine_name').val(".$row[csf("machine_id")].");\n";

		echo "$('#txt_order_id').val(".$row[csf("order_id")].");\n";
		$buyer_order=return_field_value("po_number","wo_po_break_down","id=".$row[csf("order_id")]);
		echo "$('#txt_buyer_order').val('".$buyer_order."');\n";
		echo "$('#cbo_location').val(".$row[csf("location_id")].");\n";
		echo "$('#cbo_department').val(".$row[csf("department_id")].");\n";
		//echo "load_drop_down( 'requires/general_item_issue_controller',document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_department').value, 'load_drop_down_section', 'section_td');\n";
		echo "load_drop_down( 'requires/general_item_issue_controller', ".$row[csf("department_id")].", 'load_drop_down_section', 'section_td' );\n";
		echo "$('#cbo_section').val(".$row[csf("section_id")].");\n";
		//echo "$('#cbo_section').val(".$row[csf("section_id")].");\n"; 

		echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_floor','floor_td');\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";
		if($row[csf("floor_id")])
		{
			echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_room','room_td');\n";
		}
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";
		if($row[csf("room")])
		{
			echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_rack','rack_td');\n";
		}
		echo "document.getElementById('cbo_rack').value 					= '".$row[csf("rack")]."';\n";
		if($row[csf("rack")])
		{
			echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_shelf','shelf_td');\n";
		}
		echo "document.getElementById('cbo_self').value 					= '".$row[csf("self")]."';\n";
		if($row[csf("self")])
		{
			echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_bin','bin_td');\n";
		}
		echo "document.getElementById('cbo_binbox').value 						= '".$row[csf("bin_box")]."';\n";
		
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#cbo_rack').attr('disabled','disabled');\n";
		echo "$('#cbo_self').attr('disabled','disabled');\n";
		echo "$('#cbo_binbox').attr('disabled','disabled');\n";

 		/*echo "$('#cbo_room').val(".$row[csf("room")].");\n";
		echo "fn_room_rack_self_box();\n";
		echo "$('#cbo_rack').val('".$row[csf("rack")]."');\n";
		echo "fn_room_rack_self_box();\n";
 		echo "$('#cbo_self').val(".$row[csf("self")].");\n";
		echo "fn_room_rack_self_box();\n";
		echo "$('#cbo_binbox').val(".$row[csf("bin_box")].");\n";*/



		echo "$('#current_prod_id').val(".$row[csf("prod_id")].");\n";

		echo "$('#txt_brand').val('".$row[csf("brand_name")]."');\n";//new dev
		echo "$('#cbo_origin').val(".$row[csf("origin")].");\n";//new dev
		echo "$('#txt_model').val('".$row[csf("model")]."');\n";//new dev
		echo "$('#txt_lot').val('".$row[csf("batch_lot")]."');\n";//new dev


		echo "$('#update_id').val(".$row[csf("id")].");\n"; 
		echo "$('#cbo_use_for').val('".$row[csf("use_for")]."');\n";
		echo "set_button_status(1, permission, 'fnc_general_item_issue_entry',1,1);\n";
		//echo "$('#tbl_master').find('input,select').attr('disabled', false);\n";
	}
	exit();
}
//################################################# function Here #########################################//

//function for domestic rate find--------------//
//parameters rate,ile cost,exchange rate,conversion factor
function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor){
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;	
}

if ($action=="general_item_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);

	$sql=" select a.id, a.issue_number, a.issue_purpose, a.issue_date, a.req_no, a.challan_no, a.remarks,a.store_sl_no, max(b.item_category) as item_category, a.loan_party 
	from inv_issue_master a, inv_transaction b 
	where a.id=b.mst_id and a.id=$data[1] and b.transaction_type=2 
	group by a.id, a.issue_number, a.issue_purpose, a.issue_date, a.req_no, a.challan_no, a.remarks,a.store_sl_no,a.loan_party ";
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$floor_arr = return_library_array("select id, floor_name from  lib_prod_floor","id","floor_name");
	$loan_party_arr = return_library_array("select id, supplier_name from  lib_supplier","id","supplier_name");
?>
<div style="width:1060px;">
    <table width="980" cellspacing="0" align="right">
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
						Plot No: <? echo $result['plot_no']; ?> 
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result['zip_code']; ?> 
						Province No: <? echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $item_category[$dataArray[0][csf('item_category')]];?> Issue Challan</u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>System ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="130"><strong>Issue Purpose :</strong></td> <td width="175px"><? echo $general_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
            <td width="125"><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Issue Req. No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('req_no')]; ?></td>
            <td><strong>Challan No :</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Remarks:</strong></td><td width="175px"><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
         <tr>
               <td><strong>Bar Code:</strong></td><td  colspan="3" id="barcode_img_id"></td>
               <td><strong>Store Sl No:</strong></td><td width="175px"><? echo $dataArray[0][csf('store_sl_no')]; ?></td>
       </tr>
	   <tr>
               <td></td><td  colspan="3"></td>
               <td><strong>Loan Party:</strong></td><td width="175px"><? echo $loan_party_arr[$dataArray[0][csf('loan_party')]]; ?></td>
       </tr>
    </table>
         <br>
	<div style="width:100%;">
    <table align="" cellspacing="0" width="1160"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Item Category</th>
            <th width="90" align="center">Item Group</th>
            <th width="160" align="center">Item Description</th>
            <th width="60" align="center">Store</th> 
            <th width="60" align="center">Issue Qnty</th>
            <th width="70" align="center">Lot</th>
            <th width="50" align="center">UOM</th>
            <th width="80" align="center">Serial No</th>
            <th width="80" align="center">Machine Categ.</th> 
            <th width="80" align="center">Floor</th>
            <th width="50" align="center">Machine No</th>
            <th width="60" align="center">Buyer Order</th>
            <th width="100" align="center">Buyer Name</th>
            <th width="80" align="center">Loc./ Dept./ Sec.</th>
            <th width="80" align="center">Use For</th>                
        </thead>
        <tbody>
<?
	//$mrr_no=$dataArray[0][csf('issue_number')];
	$cond="";
	if($data[1]!="") $cond .= " and a.id='$data[1]'";
	$location_arr=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category not in (1,2,3,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');
	
	$i=1;
	$sql_result = sql_select("select a.issue_number, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.floor_id, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.item_description, c.item_group_id, b.order_id, b.buyer_id, b.use_for,b.batch_lot from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and b.status_active=1 and a.entry_form=21 $cond");


	$order_arr=array();
	foreach ($sql_result as $row) {
		$order_arr[$row[csf('order_id')]]=$row[csf('order_id')];
	}

	$order_ids=where_con_using_array($order_arr,1,'c.order_id');
	$order_data="select a.buyer_name,b.po_number, c.order_id from wo_po_details_master a, wo_po_break_down b, inv_transaction c where a.job_no = b.job_no_mst and b.id=c.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_ids";
	//echo $order_data; die;
	$data_array=sql_select($order_data);

	$buyer_name_arr=array(); 
	foreach ($data_array as $row) {
		$buyer_name_arr[$row[csf('order_id')]]=$row[csf('buyer_name')];
		
	}
		
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			if($db_type==0)
			{
				$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
				$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
				$order_num=return_field_value("group_concat(po_number)","wo_po_break_down","id=".$row[csf("order_id")]);
				
			}
			else
			{
				$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
				$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
				$order_num=return_field_value("LISTAGG(CAST(po_number AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY po_number) as po_number","wo_po_break_down","id=".$row[csf("order_id")],"po_number");
				
			}
			
			
			$buyer_id=$buyer_name_arr[$row[csf('order_id')]];
			$cons_quantity=$row[csf('cons_quantity')];
			$cons_quantity_sum += $cons_quantity;
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                <td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                <td><? echo $row[csf("item_description")]; ?></td>
                <td align="center"><? echo $store_arr[$row[csf("store_id")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
                <td align="center"><? echo $row[csf("batch_lot")]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                <td><? echo $serialNo; ?></td>
                <td><? echo $machine_category[$row[csf("machine_category")]]; ?></td>
                <td ><? echo $floor_arr[$row[csf("floor_id")]]; ?></td>
                <td align="center"><? echo $machine_arr[$row[csf("machine_id")]]; ?></td>
                <td align="center"><? echo $order_num; ?></td>
                <td align="center"><? echo $buyer_arr[$buyer_id]; ?></td>
                <td><? echo $location_arr[$row[csf("location_id")]].', '.$department_arr[$row[csf("department_id")]].', '.$section_arr[$row[csf("section_id")]]; ?></td>
                <td><? echo $use_for[$row[csf("use_for")]]; ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" align="right">Total :</td>
                <td align="right"><? echo $cons_quantity_sum; ?></td>
                <td colspan="10">&nbsp;</td>
            </tr>                           
        </tfoot>
    </table>
        <br>
		 <?
            echo signature_table(12, $data[0], "1000px");
         ?>
	</div>
	</div>
     <script type="text/javascript" src="../../../../js/jquery.js"></script>
      <script type="text/javascript" src="../../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){
		   
			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		  
		} 
  
	 generateBarcode('<? echo $data[2]; ?>');
	 
	 
	 </script>
            
    
<?
exit();
}

?>
