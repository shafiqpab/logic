<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

function get_app_lavel($entry_form){
	$app_lavel_arr = array(
		1 => 'ITEM (if item mix no),STORE,DEPARTMENT & LOCATION',
		7 => 'BUYER',
		8 => 'BUYER & Brand',
		13 => 'BUYER',
		19 => 'Department',
		27 => 'Item,Buyer & Brand',
		49 => 'No',
		56 => 'STORE & DEPARTMENT',
		59 => 'Department',
		67 => 'Buyer',
		93 => 'Buyer',
		92 => 'Buyer',
		91 => 'Buyer',
	);
	return "Note: This approval handle by <u>".$app_lavel_arr[$entry_form]. "</u> Level.";
}

$act=explode("**",$action);
if($act[0]=="quot_popup")
{
	//$designation_arr=sql_select("select custom_designation from lib_designation");	
	?>
	<script>
	var uidselected="<? echo trim($act[1]); ?>";
	var uddselected="<? echo trim($act[2]); ?>";
	var trselected="<? echo trim($act[3]); ?>";
	  function js_set_value(id)
	  {
		  document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }
	</script>
	<input type="hidden" name="selected_id" id="selected_id" />
	<?	
		
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	
	$sql = "select id,user_name,user_full_name,designation from user_passwd where valid=1";
	//echo $sql;
	//$sql="select id,user_name,user_full_name,(select custom_designation from lib_designation) from user_passwd where valid=1";
	
	$designation_arr = return_library_array("select id,custom_designation from lib_designation","id","custom_designation");
	//print_r($designation_arr); die;
	$arr = array(2 => $designation_arr);
	echo  create_list_view("list_view", "User Name,Full Name,Designation", "150,170,130","580","360",0, $sql , "js_set_value", "id,user_name,user_full_name,designation", "", 0, "0,0,designation", $arr , "user_name,user_full_name,designation",1,'setFilterGrid("list_view",-1);','0,0,0','') ;
	?>
	<script>
		if(uidselected!=''){
			var xx=trselected.split(',');	
			var xx0=uidselected.split(',');	
			var xx1=uddselected.split('*');	
			
			for(i=0; i< xx.length; i++){
				js_set_value(xx[i],xx0[i]+'__'+xx1[i]);	
			}
		}
	</script>  
	<?
	exit();	
}// end action quot_popup


if($action=="openpopup_approved_sync")
{
	echo load_html_head_contents("Approved Data Sync", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$tag_report = return_library_array("select m_menu_id, menu_name from main_menu", "m_menu_id", "menu_name");
	$sql = "select a.id,a.user_name from user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$company_name and b.page_id=$cbo_report_id and b.entry_form=$cbo_tag_report and b.is_deleted=0 order by b.SEQUENCE_NO";
	$select_user_arr = return_library_array($sql, "id", "user_name");
	

	?>
	<script> 
        function openDepartmentPopup(i)
		{
			var departmentid   = $('#txtdepartmentids').val();
			var company_name   = $('#company_id').val();
			var cbo_tag_report = $('#report_id').val();
			var title          = 'Show Departments';
			var page_link      = 'electronic_approval_setup_controller.php?departmentid='+departmentid+'&company_name='+company_name+'&cbo_tag_report='+cbo_tag_report+'&departmentid='+departmentid+'&action=department_popup';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=500px,height=290px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var process_id   = this.contentDoc.getElementById("hidden_process_id").value;
				var process_name = this.contentDoc.getElementById("hidden_process_name").value;
				$('#txtdepartmentids').val(process_id);
				$('#txt_department').val(process_name);
			}
	    }

		function open_buyerpopup(i)
		{ 
			var hidden_buyer_id = $('#buyer_id').val();
			var company_name    = $('#company_id').val();
			var title           = 'Show Buyers';
			var page_link       = 'electronic_approval_setup_controller.php?hidden_buyer_id='+hidden_buyer_id+'&company_name='+company_name+'&action=buyer_name_popup';
			emailwindow         = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=290px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose = function()
			{
				var theform      = this.contentDoc.forms[0]
				var process_id   = this.contentDoc.getElementById("hidden_process_id").value;
				var process_name = this.contentDoc.getElementById("hidden_process_name").value;
				$('#buyer_id').val(process_id);
				$('#txt_buyer').val(process_name);
			}
		}
		function open_brandpopup(i)
		{ 
			var hidden_buyer_id = $('#buyer_id').val();
			var hidden_brand_id = $('#brand_id').val();
			var company_name = $('#company_id').val();
			var title = 'Show Brands';
			var page_link = 'electronic_approval_setup_controller.php?hidden_brand_id='+hidden_brand_id+'&company_name='+company_name+'&hidden_buyer_id='+hidden_buyer_id+'&action=brand_name_popup';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=290px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform      = this.contentDoc.forms[0]
				var process_id   = this.contentDoc.getElementById("hidden_process_id").value;
				var process_name = this.contentDoc.getElementById("hidden_process_name").value;
				$('#brand_id').val(process_id);
				$('#txt_brand').val(process_name);
			}
		}
		function openLocationPopup(i)
		{
			var location_id  = $('#location_id').val();
			var company_name = $('#company_id').val();
			var title        = 'Show Locations';	
			var page_link    = 'electronic_approval_setup_controller.php?locationid='+location_id+'&company_name='+company_name+'&action=location_popup';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=290px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform       = this.contentDoc.forms[0]
				var location_id   = this.contentDoc.getElementById("selected_location_id").value;
				var location_name = this.contentDoc.getElementById("selected_location_name").value;
				$('#location_id').val(location_id);
				$('#txt_location').val(location_name);
				
			}
		}
		function openStorePopup(i)
		{
			var store_id     = $('#store_id').val();
			var company_name = $('#company_id').val();
			var title        = 'Show Store';	
			var page_link    = 'electronic_approval_setup_controller.php?store_id='+store_id+'&company_name='+company_name+'&action=store_popup';
			emailwindow      = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=290px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose = function()
			{
				var theform    = this.contentDoc.forms[0]
				var store_id   = this.contentDoc.getElementById("selected_store_id").value;
				var store_name = this.contentDoc.getElementById("selected_store_name").value;
				$('#store_id').val(store_id);
				$('#txt_store').val(store_name);
			}
		}
		function openItemCatPopup(i)
		{
			var txtitemcatid = $('#txtitemcatid').val();
			var company_name = $('#company_id').val();
			var title        = 'Show Categorys';
			var page_link    = 'electronic_approval_setup_controller.php?itemcatid='+txtitemcatid+'&company_name='+company_name+'&action=item_cat_popup';
			emailwindow      = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=290px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose = function()
			{
				var theform      = this.contentDoc.forms[0];
				var itemcat_id   = this.contentDoc.getElementById("selected_itemcat_id").value;
				var itemcat_name = this.contentDoc.getElementById("selected_itemcat_name").value;
				$('#txtitemcatid').val(itemcat_id);
				$('#txt_itemcat').val(itemcat_name);
			}
		} 
		function fn_approval_submit(i)
		{
			if (confirm('Data Submit! Are You Sure?') == false) {return;}
			 

			var company_id = $("#company_id").val();
			var report_id  = $("#report_id").val();
			var cbo_tag_report = $("#cbo_tag_report").val();
			var cbo_form_user  = $("#cbo_form_user").val();
			var cbo_form_seq   = $("#cbo_form_seq").val();
			var cbo_form_group = $("#cbo_form_group").val();
			var txtdepartmentids = $("#txtdepartmentids").val();
			var buyer_id = $("#buyer_id").val();
			var brand_id = $("#brand_id").val();
			var location_id = $("#location_id").val(); 
			var store_id    = $("#store_id").val();
			var category_id = $("#txtitemcatid").val();
			var cbo_to_user = $("#cbo_to_user").val();
			var cbo_to_seq  = $("#cbo_to_seq").val();
			var cbo_to_group = $("#cbo_to_group").val();

			// freeze_window(3);
		    var data = "action=approval_sync_save&company_id="+company_id+"&report_id="+report_id+"&cbo_tag_report="+cbo_tag_report+"&cbo_form_user="+cbo_form_user+"&cbo_form_seq="+cbo_form_seq+"&cbo_form_group="+cbo_form_group+"&department_id="+txtdepartmentids+"&buyer_id="+buyer_id+"&brand_id="+brand_id+"&location_id="+location_id+"&store_id="+store_id+"&category_id="+category_id+"&cbo_to_user="+cbo_to_user+"&cbo_to_seq="+cbo_to_seq+"&cbo_to_group="+cbo_to_group;
 
			
			http.open("POST","electronic_approval_setup_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = ()=>{
				if(http.readyState == 4) 
				{ 
					var response=trim(http.responseText).split('**');
					if(response[0]==1)
					{
						alert("Change Successfully.");
					}
					else if(response[0]==0)
					{
						alert("Sorry! Data is Not Changed.");
					}
					release_freezing();
				}
			}
		}
 
    </script>
	</head>
	 <body>
		<div align="center">
			<form>
	            <input type="hidden" readonly name="company_id" id="company_id"  class="text_boxes" value="<?= $company_name;?>"/>
	            <input type="hidden" readonly name="report_id" id="report_id"  class="text_boxes" value="<?= $cbo_report_id;?>"/>
	            <input type="hidden" readonly name="cbo_tag_report" id="cbo_tag_report"  class="text_boxes" value="<?= $cbo_tag_report;?>"/>
				<fieldset style="width:600px;height: 300px;margin-top: 10px;">
				    <span id="messagebox_main"></span>
					<table cellspacing="0" cellpadding="0" border="0" rules="all" width="600" class="" align="left" >
					   <tr> 
							<td style="width: 100%;padding: 5px;">
								<strong>Company Name </strong>:<?= $company_arr[$company_name];?> 
								<strong>Page/Report Name </strong>:<?= $tag_report[$cbo_report_id];?> 
								<strong>Tag Report </strong>:<?= $entry_form_for_approval[$cbo_tag_report];?>
							</td>
					    </tr>
					</table>
					<table align="center" cellspacing="3" border="0" cellpadding="5">
						<thead> 
							<th colspan="3">From</th>
							<th colspan="3">To</th>
						</thead>
						<tbody>
							<tr> 
								<td align="right" class="must_entry_caption" title="Must Entry Field.">User</td>
								<td>
								    <?= create_drop_down("cbo_form_user", 180, $select_user_arr,"",  1, "-- Select From User --", $selected,"339,1","339","","","339","");
								?>
								</td>
								<td rowspan="9" style="width:30PX;"></td>
								<td align="right" class="must_entry_caption" title="Must Entry Field.">User</td>
								<td>
									<?= create_drop_down("cbo_to_user", 180, $select_user_arr,"",  1, "-- Select To User --", $selected,"339,1","339","","","339","");
									?>
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption" title="Must Entry Field.">Seq</td>
								<td>
								    <?php
									$seq = array();
									for ($x = 1; $x <= $rowCount; $x++) {
										$seq[$x] = $x;
									}
									echo create_drop_down("cbo_form_seq", 180, $seq,  1, "-- Select To Seq --", $selected,"","","","","","");
									?> 
								</td>
								<td align="right" class="must_entry_caption" title="Must Entry Field.">Seq</td>
								<td>
								    <?= create_drop_down("cbo_to_seq", 180, $seq,  1, "-- Select To Seq --", $selected,"","","","","","");?>
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption" title="Group">Group</td>
								<td>
								    <?= create_drop_down("cbo_form_group", 180, $seq,  1, "-- Select From Group --", $selected,"","","","","","");?>
								</td>
								<td align="right" class="must_entry_caption" title="Group">Group</td>
								<td>
								    <?= create_drop_down("cbo_to_group", 180, $seq,  1, "-- Select To Group --", $selected,"","","","","","");?>
								</td>
							</tr>
							<tr>
								<td align="right" title="Department">Department</td>
								<td>
								    <input type="text" readonly name="txt_department" id="txt_department" class="text_boxes" onDblClick="openDepartmentPopup(1);" placeholder="Browse" style="width: 170px;"/>
									<input type="hidden" readonly name="txtdepartmentids" id="txtdepartmentids" class="text_boxes"/>
								</td>
							</tr>
							<tr>
								<td align="right" title="Buyer">Buyer</td>
								<td>
								    <input type="text" readonly name="txt_buyer" id="txt_buyer"  class="text_boxes" onDblClick="open_buyerpopup(1);" placeholder="Browse" style="width: 170px;"/>
									<input type="hidden" readonly name="buyer_id" id="buyer_id" class="text_boxes"/>
								</td>
							</tr>
							<tr>
								<td align="right" title="Brand">Brand</td>
								<td>
								    <input type="text" readonly name="txt_brand" id="txt_brand"  class="text_boxes" onDblClick="open_brandpopup(1);" placeholder="Browse" style="width: 170px;"/>
									<input type="hidden" readonly name="brand_id" id="brand_id" class="text_boxes"/>
								</td>
							</tr>
							<tr>
								<td align="right" title="Location">Location</td>
								<td>
								    <input type="text" readonly name="txt_location" id="txt_location"  class="text_boxes" onDblClick="openLocationPopup(1);" placeholder="Browse" style="width: 170px;"/>
									<input type="hidden" readonly name="location_id" id="location_id" class="text_boxes"/>
								</td>
							</tr>
							<tr>
								<td align="right" title="Store">Store</td>
								<td>
								    <input type="text" readonly name="txt_store" id="txt_store"  class="text_boxes" onDblClick="openStorePopup(1);" placeholder="Browse" style="width: 170px;"/>
									<input type="hidden" readonly name="store_id" id="store_id" class="text_boxes"/>
								</td>
							</tr>
							<tr>
								<td align="right" title="Item Cat.">Item Cat.</td>
								<td>
								    <input type="text" readonly name="txt_itemcat" id="txt_itemcat" class="text_boxes" onDblClick="openItemCatPopup(1);" placeholder="Browse" style="width: 170px;"/>
									<input type="hidden" readonly name="txtitemcatid" id="txtitemcatid" class="text_boxes"/>
								</td>
							</tr>
						</tbody>
						<br>
						<br>
						<tr>
							<td align="center" colspan="6">
								<input type="button" name="confirm" onClick="fn_approval_submit(1)" class="formbutton" value="Submit" style="width:100px;margin-top: 10px;"/>
							</td>
						</tr>
					</table>
				</fieldset>
				<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
			</form>
		</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script src="../../includes/functions.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}
 

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
 
	if($_SESSION['logic_erp']['user_code']!='SUPERADMIN')
	{
		if($operation==1 || $operation==2)
		{
			echo "21**Update/Delete Restricted. If need Please Contract With MIS.";
			die;
		}
	}
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN");}


		$duplicate_form=is_duplicate_field("ENTRY_FORM","electronic_approval_setup","ENTRY_FORM=$cbo_tag_report and company_id=$cbo_company_name and is_deleted=0");

		if($duplicate_form==1)
		{
			echo "11**This Tag Report [".$entry_form_for_approval[$cbo_tag_report]."] is exist";
			disconnect($con);
			exit;
		} 
		
		for($i=1; $i <= $torow; $i++)
		{
			$cbo_Report_id=str_replace("'","",$cbo_Report_id);
			$userid=${"userid_" . $i};
			
			$duplicate=is_duplicate_field("user_id","electronic_approval_setup","page_id=$cbo_Report_id and company_id=$cbo_company_name and user_id=$userid and is_deleted=0");
			if($duplicate==1)
			{
				echo "11**This User is exist for this Approval Page.";
				disconnect($con);
				exit;
			}
		}

	
		$field_array_mst="id,company_id,buyer_id,brand_id,page_id,entry_form,user_id,bypass, sequence_no,group_no,department,LOCATION,ITEM_CATEGORY,FABRIC_SOURCE, approved_by, approved_date,is_deleted";
		
		if(str_replace("'","",$update_id)=="")
		{
			$mst_id= return_next_id("id","electronic_approval_setup",1);
			for($i=1; $i <= $torow; $i++)
			{
				$cbo_Report_id=str_replace("'","",$cbo_Report_id);
				
				$userid=${"userid_" . $i};
				$txtcanbypass=${"txtcanbypass_" . $i};
				$txtsequenceno=${"txtsequenceno_" . $i};
				$txtgroup=${"txtgroup_" . $i};
				$txtbuyerid=${"txtbuyerid_" . $i};
				$txtbrandid=${"txtbrandid_" . $i};
				$txtdepartmentid=${"txtdepartmentid_" . $i};
				$txtlocationid=${"txtlocationid_" . $i};
				$txtitemcatid=${"txtitemcatid_" . $i};
				$txtfbsource=${"txtfbsourceid_" . $i};


				if($i!=1)$data_array_mst.=",";
				$data_array_mst.="(".$mst_id.",".$cbo_company_name.",".$txtbuyerid.",".$txtbrandid.",".$cbo_Report_id.",".$cbo_tag_report.",".$userid.",".$txtcanbypass.",".$txtsequenceno.",".$txtgroup.",'".str_replace("'","",$txtdepartmentid)."','".str_replace("'","",$txtlocationid)."','".str_replace("'","",$txtitemcatid)."','".str_replace("'","",$txtfbsource)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
				$mst_id++;
			}// end for loop
			 //echo "insert into electronic_approval_setup (".$field_array_mst.") values ".$data_array_mst;die;
			$rID=sql_insert("electronic_approval_setup",$field_array_mst,$data_array_mst,1);
			//echo $rID;die;
		}

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id);
			}
		}
		disconnect($con);die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(str_replace("'","",$$updateid_1)!="")
		{
			$cbo_Report_id=str_replace("'","",$cbo_Report_id);
			$field_array1="updated_by*update_date*is_deleted";
			$data_array1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
		}
		
		$mst_id= return_next_id("id","electronic_approval_setup",1);
		$field_array_up="company_id*buyer_id*brand_id*page_id*entry_form*user_id*bypass*sequence_no*group_no*department*LOCATION*ITEM_CATEGORY*FABRIC_SOURCE*updated_by*update_date*is_deleted";
		$field_array_mst="id,company_id,buyer_id,brand_id,page_id,entry_form,user_id,bypass, sequence_no,group_no,department,LOCATION,ITEM_CATEGORY,FABRIC_SOURCE, approved_by, approved_date,is_deleted";
		$j=1;
		
		for($id=1; $id <= $torow; $id++)
		{
			$updateid="updateid_" . $id;
			$userid="userid_" . $id; 
			$txtcanbypass="txtcanbypass_" . $id;
			$txtsequenceno="txtsequenceno_" . $id;
			$txtgroup="txtgroup_" . $id;
			$txtbuyerid="txtbuyerid_" . $id;
			$txtbrandid="txtbrandid_" . $id;
			$txtdepartmentid="txtdepartmentid_".$id;
			$txtlocationid=${"txtlocationid_" . $id};
			$txtitemcatid=${"txtitemcatid_" . $id};
			$txtfbsource=${"txtfbsourceid_" . $id};

			if(str_replace("'","",$$updateid)!="")
			{
				$id_arr[]=str_replace("'","",$$updateid);
				$data_array_up[str_replace("'",'',$$updateid)] =explode("*",("".$cbo_company_name."*'".str_replace("'","",$$txtbuyerid)."'*".$$txtbrandid."*".$cbo_Report_id."*".$cbo_tag_report."*".$$userid."*".$$txtcanbypass."*".$$txtsequenceno."*".$$txtgroup."*'".str_replace("'", "", $$txtdepartmentid)."'*'".str_replace("'", "", $txtlocationid)."'*'".str_replace("'", "", $txtitemcatid)."'*'".str_replace("'", "", $txtfbsource)."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0"));
				
				$mstUpdate_id_array=array();
				$sql_dtls="Select id from electronic_approval_setup where page_id=$cbo_Report_id and company_id=$cbo_company_name and entry_form=$cbo_tag_report and is_deleted=0";
				$nameArray=sql_select( $sql_dtls );
				foreach($nameArray as $row)
				{
					$mstUpdate_id_array[]=$row[csf('id')];
				}
			}
			else
			{
				//$mst_id= return_next_id("id","electronic_approval_setup",1);
				if($j!=1)$data_array_mst.=",";
				$data_array_mst.="(".$mst_id.",".$cbo_company_name.",".$$txtbuyerid.",".$$txtbrandid.",".$cbo_Report_id.",".$cbo_tag_report.",".$$userid.",".$$txtcanbypass.",".$$txtsequenceno.",".$$txtgroup.",'".str_replace("'", "", $$txtdepartmentid)."','".str_replace("'", "", $txtlocationid)."','".str_replace("'", "", $txtitemcatid)."','".str_replace("'", "", $txtfbsource)."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
				$mst_id++;
				$j++;
				
			}
		}  
	 
		// print_r($data_array_up);

		// echo $data_array_mst;die;
		
		if(implode(',',$id_arr)!="")
		{
			$distance_delete_id=array_diff($mstUpdate_id_array,$id_arr);
		}
		else
		{
			$distance_delete_id=$mstUpdate_id_array;
		}
		$field_array_del="is_deleted*updated_by*update_date";
		$data_array_del="'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		if(implode(',',$distance_delete_id)!="")
		{
			foreach($distance_delete_id as $id_val)
			{
				$rID=sql_update("electronic_approval_setup",$field_array_del,$data_array_del,"id","".$id_val."",1);
			}
		}
		
		$flag=0;	
		if(trim(str_replace("'","",$$updateid_1))!="")
		{
			$rID=sql_update("electronic_approval_setup",$field_array1,$data_array1,"page_id*company_id","".$cbo_Report_id."*".$cbo_company_name."",1);
			if($rID) $flag=1; else $flag=0;
		}
		//echo $flag;die;
		
		if(count($data_array_up)>0)
		{
			//echo bulk_update_sql_statement("electronic_approval_setup", "id",$field_array_up,$data_array_up,$id_arr );die;
			$rID1=execute_query(bulk_update_sql_statement("electronic_approval_setup", "id",$field_array_up,$data_array_up,$id_arr ),1);	
			if($rID1) $flag=1; else $flag=0;
		}
	
		if($data_array_mst!="")	
		{	
			//echo "insert into electronic_approval_setup value($field_array_mst) values $data_array_mst";die;
			
			$rID2=sql_insert("electronic_approval_setup",$field_array_mst,$data_array_mst,1);
			if($flag==1)
			{
			   if($rID2) $flag=1; else $flag=0;
			}
		}	
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$rID);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$rID);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$rID);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$rID);
			}
		}
		disconnect($con);die;
	}
	/*else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		for($id=1; $id <= $torow; $id++)
		{
			$updateid="updateid_" . $id;
			$field_array="updated_by*update_date*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("electronic_approval_setup",$field_array,$data_array,"id","".$$updateid."",1);
		}
		
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2)
		{
			if($rID )
			{
				oci_commit($con);
				echo "2**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);die;
	}*/

}// end save_update_delete;

if($action=="approval_authority")
{
	// cbo_company_name
	// cbo_tag_report
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

 

	$sql = "SELECT a.USER_ID, a.ENTRY_FORM,a.BYPASS, a.SEQUENCE_NO, a.GROUP_NO, a.BUYER_ID, a.BRAND_ID, a.DEPARTMENT, a.LOCATION, a.ITEM_CATEGORY FROM electronic_approval_setup a WHERE COMPANY_ID =$cbo_company_name AND ENTRY_FORM=$cbo_tag_report";
	$sqlResult=sql_select( $sql );

    $oldSequenceArr = array();
	foreach($sqlResult as $data){
		$oldSequenceArr[$data['SEQUENCE_NO']] = $data;
	}

    // echo "<pre>";
	// print_r($oldSequenceArr);
	// echo "</pre>";
	// exit; 

	$newSequenceArr = array();
	for($id=1; $id <= $torow; $id++)
	{
		$updateid="updateid_" . $id;
		$userid="userid_" . $id; 
		$txtcanbypass="txtcanbypass_" . $id;
		$txtsequenceno="txtsequenceno_" . $id;
		$txtgroup="txtgroup_" . $id;
		$txtbuyerid="txtbuyerid_" . $id;
		$txtbrandid="txtbrandid_" . $id;
		$txtdepartmentid="txtdepartmentid_".$id;
		$txtlocationid=${"txtlocationid_" . $id};
		$txtitemcatid=${"txtitemcatid_" . $id};
 
		$newSequenceArr[$$txtsequenceno] = [
			"USER_ID"=>$$userid,
			"ENTRY_FORM"=>$cbo_tag_report,
			"BYPASS"=>$$txtcanbypass, 
			"SEQUENCE_NO"=>$$txtsequenceno,
			"GROUP"=>$$txtgroup,
			"BUYER_ID"=>$$txtbuyerid,
			"BRAND_ID"=>$$txtbrandid,
			"DEPARTMENT_ID"=>$$txtdepartmentid,
			"LOCATION_ID"=>$$txtlocationid,
			"ITEM_CATEGORY_ID"=>$$txtitemcatid,
		];
	}
	//print_r('result');

	$array1 = array(
		'a1' => array('name' => 'aaa', 'age' => '30'),
		'b1' => array('name' => 'bbb', 'age' => '40'),
		'c1' => array('name' => 'ccc', 'age' => '30')
	);
	
	$array2 = array(
		'a1' => array('name' => 'aaa', 'age' => '30'),
		'b1' => array('name' => 'zzz','age'=>'60'),
		'c1' => array('name' => 'ccc', 'age' => '30')
	);

	$result = array();
	foreach($array1 as $key => $val) {
		if(is_array($val) && isset($array2[$key])) {
			$tmp = check_diff_multi($val, $array2[$key]);
			if($tmp) {
				$result[$key] = $tmp;
			}
		}
		elseif(!isset($array2[$key])) {
			$result[$key] = null;
		}
		elseif($val !== $array2[$key]) {
			$result[$key] = $array2[$key];
		}

		if(isset($array2[$key])) {
			unset($array2[$key]);
		}
    }
	$result = array_merge($result, $array2);
	 
	print '<pre>';
	print_r($result);
	print '</pre>';
	die;	
  
	echo "<pre>";
	print_r($newSequenceArr);
	echo "</pre>";
	exit;
 
}

if($action=="electronic_approval_setup_from_data")
{
	
	$data=explode("_",$data);
	
	$buyer_arr=return_library_array( "select id, buyer_name from   lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id, brand_name from   LIB_BUYER_BRAND",'id','brand_name');

	$item_cat_arr=return_library_array( "select a.CATEGORY_ID as ID,a.SHORT_NAME from LIB_ITEM_CATEGORY_LIST a where a.STATUS_ACTIVE=1 AND a.is_deleted=0",'ID','SHORT_NAME');// and a.CATEGORY_TYPE=1

	$location_arr=return_library_array( "select a.ID,a.LOCATION_NAME from LIB_LOCATION a where a.STATUS_ACTIVE=1 AND a.is_deleted=0 and a.COMPANY_ID={$data[0]}",'ID','LOCATION_NAME');
	
	 

	if($data[2]==11){$department_arr=$cost_components;}
	else{
		$department_arr=return_library_array( "select id,DEPARTMENT_NAME from LIB_DEPARTMENT comp where status_active =1 and is_deleted=0 order by DEPARTMENT_NAME",'id','DEPARTMENT_NAME');
	}

	echo "$('#department_td').text('".(($data[2]==11)?'Component':'Department')."');\n";
	echo"
		for(di=$('#evaluation_tbl tbody tr').length; di > 1;  di--){
			fn_deletebreak_down_tr(di,'evaluation_tbl' );	
		} \n";
	
	$sql="select id,DEPARTMENT, bypass,buyer_id,brand_id, sequence_no, user_id, page_id,entry_form,group_no,LOCATION,ITEM_CATEGORY,FABRIC_SOURCE from electronic_approval_setup where company_id=$data[0] and page_id=$data[1] and is_deleted=0 order by sequence_no";
	 //echo $sql;die;
	$res = sql_select($sql);

	$i=1;
	foreach($res as $row)
	{ 
		if($i!=1){echo "add_factor_row('".($i-1)."');\n";}
		$user_pass = sql_select("select user_name, user_full_name, designation from user_passwd where id=".$row[csf("user_id")]."");	

		$buyer_ids=explode(",",$row[csf("buyer_id")]);
		$buyer_cond='';
		foreach($buyer_ids as $row_id)
		{
			if($buyer_cond=='') $buyer_cond=$buyer_arr[$row_id];else $buyer_cond.=",".$buyer_arr[$row_id];	
		}
		
		$brand_ids=explode(",",$row[csf("brand_id")]);
		$brand_name_arr=array();
		foreach($brand_ids as $row_id)
		{
			$brand_name_arr[$row_id]=$brand_arr[$row_id];	
		}
		 
		$depart_arr=array();
		$depart_ids=explode(",",$row['DEPARTMENT']);
		foreach($depart_ids as $row_id)
		{
			$depart_arr[$row_id]=$department_arr[$row_id];	
		}


		$loca_arr=array();
		$location_ids=explode(",",$row['LOCATION']);
		foreach($location_ids as $row_id)
		{
			$loca_arr[$row_id]=$location_arr[$row_id];	
		}

		$item_arr=array();
		$item_cat_ids=explode(",",$row['ITEM_CATEGORY']);
		foreach($item_cat_ids as $row_id)
		{
			$item_arr[$row_id]=$item_cat_arr[$row_id];	
		}
 	
 
		echo "$('#updateid_$i').val('".$row[csf("id")]."');\n";
		echo "$('#txtcanbypass_$i').val('".$row[csf("bypass")]."');\n";
		echo "$('#txtsequenceno_$i').val('".$row[csf("sequence_no")]."');\n";
		echo "$('#txtgroup_$i').val('".$row[csf("group_no")]."');\n";
		echo "$('#txtbuyerid_$i').val('".$row[csf("buyer_id")]."');\n";
		echo "$('#txtbuyer_$i').val('".$buyer_cond."');\n";
		echo "$('#txtbrandid_$i').val('".$row[csf("brand_id")]."');\n";
		echo "$('#txtdepartmentid_$i').val('".$row['DEPARTMENT']."');\n";
		echo "$('#txtdepartment_$i').val('".implode(',',$depart_arr)."');\n";
		echo "$('#txtbrand_$i').val('".implode(',',$brand_name_arr)."');\n";
		echo "$('#userid_$i').val('".$row[csf("user_id")]."');\n";
		echo "$('#cbo_Report_id').val('".$row[csf("page_id")]."');\n";
		echo "$('#cbo_tag_report').val('".$row[csf("entry_form")]."');\n";


		echo "$('#txtlocationid_$i').val('".$row['LOCATION']."');\n";
		echo "$('#txtlocation_$i').val('".implode(',',$loca_arr)."');\n";
		echo "$('#txtitemcatid_$i').val('".$row['ITEM_CATEGORY']."');\n";
		echo "$('#txtfbsourceid_$i').val('".$row['FABRIC_SOURCE']."');\n";
		echo "$('#txtitemcat_$i').val('".implode(',',$item_arr)."');\n";

		$source_arr = explode(',',$row['FABRIC_SOURCE']);
		$tmpSource = array();
		foreach($source_arr as $source)
		{
			$tmpSource[$source] = $fabric_source[$source];
		}
		
		echo "$('#txtfbsource_$i').val('".implode(',',$tmpSource)."');\n";
		
		foreach($user_pass as $row1)
		{	
			$lib_deg = sql_select("select custom_designation from lib_designation where id=".$row1[csf("designation")]."");	
			echo "$('#txtfullname_$i').val('".$row1[csf("user_full_name")]."');\n"; 		
				foreach($lib_deg as $deg){echo "$('#txtdesignation_$i').val('".$deg[csf("custom_designation")]."');\n";}
			echo "$('#txtsigningauthority_$i').val('".$row1[csf("user_name")]."');\n";
		}
			
	$i++;
	}	
	echo "set_button_status(1, permission, 'fnc_electronic_approval_setup',1);\n";

	echo "$('#note_view').html('".get_app_lavel($row['ENTRY_FORM'])."');\n";
	
	exit();	
}


if($action=="create_emp_list_view")
{
	$sql="select a.company_id, a.page_id,a.ENTRY_FORM,b.menu_name from electronic_approval_setup a, main_menu b where b.m_menu_id=a.page_id and a.company_id=$data and a.is_deleted=0 GROUP BY a.company_id,a.ENTRY_FORM, b.menu_name,a.page_id";
		
	$arr=array(1=>$entry_form_for_approval);
	echo create_list_view("list_view", "Page/Report Name,Entry Form", "300,200","550","260",0, $sql, "get_php_form_data", "company_id,page_id,ENTRY_FORM", "'electronic_approval_setup_from_data','requires/electronic_approval_setup_controller'", 1, "0,ENTRY_FORM", $arr , "menu_name,ENTRY_FORM", "employee_info_controller",'','0,0') ;
}

if($action=="buyer_name_popup")
{
	echo load_html_head_contents("Buyer Name popup Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
   //echo $company_name;
	//echo 'hhhh';die;
	?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var style_ref_array= new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function set_all()
		{
			var old=document.getElementById('txt_process_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
    </script>

	</head>

	<body>
	<div align="center">
		<fieldset style="width:320px;margin-left:10px">
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
			<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table" >
					<thead>
						<th width="50">SL</th>
						<th width="">Buyer Name</th>
					</thead>
				</table>
				<div style="width:320px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="300" class="rpt_table" id="tbl_list_search" >
					<?
					if($company_name) $tag_company_cond=" and b.tag_company='$company_name' ";
					else $tag_company_cond="";
						$sql=sql_select("select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $tag_company_cond  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90))  group by buy.id, buy.buyer_name order by buy.buyer_name");
						$i=1; $buyer_id_row_id=''; 
						$hidden_buyer_id=explode(",",$hidden_buyer_id);
						//print_r( $hidden_process_id);
						foreach($sql as $id=>$name)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							if(in_array($name[csf('id')],$hidden_buyer_id)) 
							{ 
								if($buyer_id_row_id=="") $buyer_id_row_id=$i; else $buyer_id_row_id.=",".$i;
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $name[csf('id')]; ?>"/>	
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name[csf('buyer_name')]; ?>"/>
								</td>	
								<td width=""><p><? echo $name[csf('buyer_name')]; ?></p></td>
							</tr>
							<?
							$i++;
						}
					?>
						<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $buyer_id_row_id; ?>"/>
					</table>
				</div>
				<table width="300" cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td align="center" height="30" valign="bottom">
							<div style="width:100%"> 
								<div style="width:50%; float:left" align="left">
									<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
								</div>
								<div style="width:50%; float:left" align="left">
									<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
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
		set_all();
	</script>
	</html>
	<?
	exit();
}


if($action=="brand_name_popup")
{
	echo load_html_head_contents("Brand Name popup Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
  // echo $hidden_buyer_id;
  	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var style_ref_array= new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function set_all()
		{
			var old=document.getElementById('txt_process_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
    </script>

	</head>

	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
			<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table" align="left" >
					<thead>
						<th width="50">SL</th>
						<th width="150">Buyer Name</th>
						<th width="">Brand Name</th>
					</thead>
				</table>
				<div style="width:370px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" id="tbl_list_search" align="left" >
					<?
						if($hidden_buyer_id!=''){$buyer_con=" and BUYER_ID in($hidden_buyer_id)";}
						$sql = sql_select("select ID, BUYER_ID,BRAND_NAME from LIB_BUYER_BRAND where status_active =1 and is_deleted=0 $buyer_con order by brand_name");
						$i=1; 
						$brand_id_row_id=''; 
						$hidden_brand_id = explode(",",$hidden_brand_id);
						foreach($sql as $id=>$name)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							if(in_array($name[csf('id')],$hidden_brand_id)) 
							{ 
								if($brand_id_row_id=="") $brand_id_row_id=$i; else $brand_id_row_id.=",".$i;
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $name[csf('id')]; ?>"/>	
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name[csf('brand_name')]; ?>"/>
								</td>	
								<td width="150"><p><? echo $buyer_arr[$name['BUYER_ID']]; ?></p></td>
								<td width=""><p><? echo $name['BRAND_NAME']; ?></p></td>
							</tr>
							<?
							$i++;
						}
					?>
					<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $brand_id_row_id; ?>"/>
					</table>
				</div>
				<table width="300" cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td align="center" height="30" valign="bottom">
							<div style="width:100%"> 
								<div style="width:50%; float:left" align="left">
									<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
								</div>
								<div style="width:50%; float:left" align="left">
									<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
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
		set_all();
	</script>
	</html>
	<?
	exit();
}


if($action=='check_data_is_exis')
{
	$data=explode("_", $data);
	$company_id=$data[0];
	$report_id=$data[1];
	
	$sql="select company_id, page_id from electronic_approval_setup where page_id='$report_id' and company_id=$company_id and is_deleted=0 GROUP BY company_id, page_id";
	$sql_result =sql_select($sql); 
	if(count($sql_result)>0)
	{
		echo "yes";
	}
	else
	{
		echo "no";
	}
}




if($action=="department_popup")
{
	echo load_html_head_contents("Department Name popup Info", "../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var style_ref_array= new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
		
		
    </script>

	</head>

	<body>
	<div align="center">
	<form name="searchorder_1"  id="searchorder_1"  autocomplete="off">
		<fieldset style="width:440px;"><legend>Enter search words</legend>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="440" class="rpt_table" align="left" >
				<thead>
					<th width="35">SL</th>
					<th width="200"><?=($cbo_tag_report==11)?"Component":"Department";?></th>
					<? if($cbo_tag_report<>11){?>
					<th>Division</th>
					<? } ?>
				</thead>
			</table>
			<div style="width:440px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table" id="tbl_list_search" align="left" >
				<?
					$i=1;
					//$division_arr=return_library_array("select ID,DIVISION_NAME from LIB_DIVISION","id","DIVISION_NAME");
					if($cbo_tag_report==11){
						foreach($cost_components as $component_id=>$component_name){
							$sql_result[]=array(
								'ID'=>$component_id,
								'DEPARTMENT_NAME'=>$component_name,
								'DIVISION_NAME'=>'',
							);
						}
					}
					else{
						$sql="select a.ID,a.DEPARTMENT_NAME, b.DIVISION_NAME from LIB_DEPARTMENT a,LIB_DIVISION b where b.id=a.division_id and a.STATUS_ACTIVE=1 AND a.is_deleted=0 and b.STATUS_ACTIVE=1 AND b.is_deleted=0 and b.COMPANY_ID=$company_name";
						$sql_result =sql_select($sql); 
					}
					
					
					foreach($sql_result as $row)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?= $i;?>)"> 
							<td width="35" align="center"><?=$i; ?>
								<input type="hidden" name="txt_serial" id="txt_serial<?=$row['ID'];?>" value="<?= $i; ?>"/>	
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i;?>" value="<?= $row['ID']; ?>"/>	
								<input type="hidden" name="txt_individual" id="txt_individual<?=$i;?>" value="<?= $row['DEPARTMENT_NAME']; ?>"/>
							</td>	
							<td width="200"><p><?= $row['DEPARTMENT_NAME']; ?></p></td>
							
							<? if($cbo_tag_report<>11){?>
								<td><p><?= $row['DIVISION_NAME']; ?></p></td>
							<? } ?>
						</tr>
						<?
						$i++;
					}
				?>
				</table>
			</div>
			<table width="300" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table>

		</fieldset> 
			<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
	</form>
		
		
	</div>    
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		var departmentid='<?=$departmentid;?>';
		var departmentidArr=departmentid.split(',');
		for(var i=0;i<departmentidArr.length;i++){
			var serial = $('#txt_serial' + departmentidArr[i]).val();
			js_set_value( serial );
		}
	</script>
	</html>
	<?
	exit();
}
 

if($action=="location_popup")
{
	echo load_html_head_contents("Location name popup Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_location_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_location_id' + str).val() );
				selected_name.push( $('#txt_location' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_location_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#selected_location_id').val(id);
			$('#selected_location_name').val(name);
		}
		
		
    </script>

	</head>

	<body>
	<div align="center">
	<form name="searchorder_1"  id="searchorder_1"  autocomplete="off">
		<fieldset style="width:440px;"><legend>Enter search words</legend>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="440" class="rpt_table" align="left" >
				<thead>
					<th width="35">SL</th>
					<th width="200">Location Name</th>
				</thead>
			</table>
			<div style="width:440px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table" id="tbl_list_search" align="left" >
				<?
					$i=1;
					$sql="select a.ID,a.LOCATION_NAME from LIB_LOCATION a where a.STATUS_ACTIVE=1 AND a.is_deleted=0 and a.COMPANY_ID=$company_name";
					$sql_result =sql_select($sql); 
					foreach($sql_result as $row)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?= $i;?>)"> 
							<td width="35" align="center"><?=$i; ?>
								<input type="hidden" name="txt_serial" id="txt_serial<?=$row['ID'];?>" value="<?= $i; ?>"/>	
								<input type="hidden" name="txt_location_id" id="txt_location_id<?=$i;?>" value="<?= $row['ID']; ?>"/>	
								<input type="hidden" name="txt_location" id="txt_location<?=$i;?>" value="<?= $row['LOCATION_NAME']; ?>"/>
							</td>	
							<td width="200"><p><?= $row['LOCATION_NAME']; ?></p></td>
						</tr>
						<?
						$i++;
					}
				?>
				</table>
			</div>
			<table width="300" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table>

		</fieldset> 
			<input type="hidden" name="selected_location_id" id="selected_location_id" class="text_boxes" value="">
			<input type="hidden" name="selected_location_name" id="selected_location_name" class="text_boxes" value="">
	</form>
		
		
	</div>    
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		var locationid='<?=$locationid;?>';
		var locationidArr=locationid.split(',');
		for(var i=0;i<locationidArr.length;i++){
			var serial = $('#txt_serial' + locationidArr[i]).val();
			js_set_value( serial );
		}
	</script>
	</html>
	<?
	exit();
}
 
if($action=="store_popup")
{
	echo load_html_head_contents("Store name popup Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_location_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_location_id' + str).val() );
				selected_name.push( $('#txt_location' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_location_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#selected_store_id').val(id);
			$('#selected_store_name').val(name);
		}
    </script>
	</head>
	<body>
	<div align="center">
	<form name="searchorder_1" id="searchorder_1" autocomplete="off">
		<fieldset style="width:440px;"><legend>Enter search words</legend>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="440" class="rpt_table" align="left" >
				<thead>
					<th width="35">SL</th>
					<th width="200">Store Name</th>
				</thead>
			</table>
			<div style="width:440px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table" id="tbl_list_search" align="left" >
				<?
					$i=1;
					$sql="select a.ID,a.STORE_NAME from LIB_STORE_LOCATION a where a.STATUS_ACTIVE=1 AND a.is_deleted=0 and a.COMPANY_ID=$company_name";
					$sql_result =sql_select($sql); 
					foreach($sql_result as $row)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?= $i;?>)"> 
							<td width="35" align="center"><?=$i; ?>
								<input type="hidden" name="txt_serial" id="txt_serial<?=$row['ID'];?>" value="<?= $i; ?>"/>	
								<input type="hidden" name="txt_location_id" id="txt_location_id<?=$i;?>" value="<?= $row['ID']; ?>"/>	
								<input type="hidden" name="txt_location" id="txt_location<?=$i;?>" value="<?= $row['STORE_NAME']; ?>"/>
							</td>	
							<td width="200"><p><?= $row['STORE_NAME']; ?></p></td>
						</tr>
						<?
						$i++;
					}
				?>
				</table>
			</div>
			<table width="300" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table>

		</fieldset> 
			<input type="hidden" name="selected_store_id" id="selected_store_id" class="text_boxes" value="">
			<input type="hidden" name="selected_store_name" id="selected_store_name" class="text_boxes" value="">
	</form>
		
		
	</div>    
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		var storeid='<?=$store_id;?>';
		var storeArr=storeid.split(',');
		for(var i=0;i<storeArr.length;i++){
			var serial = $('#txt_serial' + storeArr[i]).val();
			js_set_value( serial );
		}
	</script>
	</html>
	<?
	exit();
}

if($action=="item_cat_popup")
{
	echo load_html_head_contents("Location name popup Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	 
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_cat_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_cat_id' + str).val() );
				selected_name.push( $('#txt_cat' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_cat_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#selected_itemcat_id').val(id);
			$('#selected_itemcat_name').val(name);
		} 
    </script>
	</head>
	<body>
	<div align="center">
	<form name="searchorder_1"  id="searchorder_1"  autocomplete="off">
		<fieldset style="width:430px;"><legend>Enter search words</legend>
			<table width="400" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
				<thead>
					<th width="25">SL</th>
					<th width="200">ACTUAL CATEGORY NAME</th>
					<th width="175">SHORT NAME</th>
				</thead>
			</table>
			<div style="width:420px; overflow-y:scroll; max-height:250px;float:left" id="buyer_list_view">
				<table width="400" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search" align="left" >
				<?
					$i=1;
					$sql="select a.CATEGORY_ID as ID,a.ACTUAL_CATEGORY_NAME,a.SHORT_NAME from LIB_ITEM_CATEGORY_LIST a where a.STATUS_ACTIVE=1 AND a.is_deleted=0";// and a.CATEGORY_TYPE=1
					$sql_result =sql_select($sql); 
					foreach($sql_result as $row)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?= $i;?>)"> 
							<td width="25" align="center"><?=$i; ?>
								<input type="hidden" name="txt_serial" id="txt_serial<?=$row['ID'];?>" value="<?= $i; ?>"/>	
								<input type="hidden" name="txt_cat_id" id="txt_cat_id<?=$i;?>" value="<?= $row['ID']; ?>"/>	
								<input type="hidden" name="txt_cat" id="txt_cat<?=$i;?>" value="<?= $row['SHORT_NAME']; ?>"/>
							</td>	
							<td width="200"><p><?= $row['ACTUAL_CATEGORY_NAME']; ?></p></td>
							<td width="175"><p><?= $row['SHORT_NAME']; ?></p></td>
						</tr>
						<?
						$i++;
					}
				?>
				</table>
			</div>
			<table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table> 
		</fieldset> 
			<input type="hidden" name="selected_itemcat_id" id="selected_itemcat_id" class="text_boxes" value="">
			<input type="hidden" name="selected_itemcat_name" id="selected_itemcat_name" class="text_boxes" value="">
	</form> 
	</div>    
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		var itemcatid='<?=$itemcatid;?>';
		var itemcatidArr=itemcatid.split(',');
		for(var i=0;i<itemcatidArr.length;i++){
			var serial = $('#txt_serial' + itemcatidArr[i]).val();
			js_set_value( serial );
		}
	</script>
	</html>
	<?
	exit();
}

if($action=="source_popup")
{
	echo load_html_head_contents("FB Source", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	 
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array();
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_source_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_source_id' + str).val() );
				selected_name.push( $('#txt_source' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_source_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#selected_source_id').val(id);
			$('#selected_source_name').val(name);
		} 
    </script>
	</head>
	<body>
	<div align="center">
	<form name="searchorder_1"  id="searchorder_1"  autocomplete="off">
		<fieldset style="width:430px;"><legend>Enter search words</legend>
			<table width="400" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left" >
				<thead>
					<th width="25">SL</th>
					<th width="200">ACTUAL CATEGORY NAME</th>
					<th width="175">SHORT NAME</th>
				</thead>
			</table>
			<div style="width:420px; overflow-y:scroll; max-height:250px;float:left" id="buyer_list_view">
				<table width="400" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search" align="left" >
				<?
					$i=1;
					foreach($fabric_source as $source_id => $source_name)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer" id="search<?=$i;?>" onClick="js_set_value(<?= $i;?>)"> 
							<td width="25" align="center"><?=$i; ?>
								<input type="hidden" name="txt_serial" id="txt_serial<?=$source_id;?>" value="<?= $i; ?>"/>	
								<input type="hidden" name="txt_source_id" id="txt_source_id<?=$i;?>" value="<?= $source_id; ?>"/>	
								<input type="hidden" name="txt_source" id="txt_source<?=$i;?>" value="<?= $source_name; ?>"/>
							</td>	
							<td width="200"><p><?= $source_name; ?></p></td>
						</tr>
						<?
						$i++;
					}
				?>
				</table>
			</div>
			<table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%"> 
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table> 
		</fieldset> 
			<input type="hidden" name="selected_source_id" id="selected_source_id" class="text_boxes" value="">
			<input type="hidden" name="selected_source_name" id="selected_source_name" class="text_boxes" value="">
	</form> 
	</div>    
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		var sourceid='<?=$sourceid;?>';
		var sourceidArr=sourceid.split(',');
		for(var i=0;i<sourceidArr.length;i++){
			var serial = $('#txt_serial' + sourceidArr[i]).val();
			js_set_value( serial );
		}
	</script>
	</html>
	<?
	exit();
}
 
if($action="approval_sync_save"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if($_SESSION['logic_erp']['user_code'] != 'SUPERADMIN'){
		 echo "21**Update/Delete Restricted. If need Please Contract With MIS.";die;
	}

	// other_purchase_work_order_approval_controller  
	if($cbo_tag_report == 17){ 
			//SYNC .................................
			define('ENTRY_FORM', 17);
 		
			$sync_data_arr[1] = [
				'COMPANY_ID' => $company_id,
				'ENTRY_FORM' => ENTRY_FORM,
				'BUYER_ID' => $buyer_id,
				'FROM_APPROVED_BY' => $cbo_form_user,
				'FROM_SEQUENCE_NO' => $cbo_form_seq,
				'TO_APPROVED_BY' => $cbo_to_user,
				'TO_SEQUENCE_NO' => $cbo_to_seq
			];

			$con = connect();     
			$flag = 1;
		   foreach($sync_data_arr as $APP_ROWS){
		
				//history......................................................................
				if($flag == 1){
					$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
						select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=147  and b.item_category_id not in(1,4,5,6,7,11,23) and a.ready_to_approved=1 and a.company_name=".$APP_ROWS['COMPANY_ID']."  and a.is_approved in(1,3)) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
					$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
				}
		   }
		   //print_r($eq_rr['history']);oci_rollback($con);die;
			 
			if($flag==1)
			{
				oci_commit($con);
				echo 1;
			}
			else
			{
				oci_rollback($con);
				echo 0;
			}
			disconnect($con);
			die;
		
		

	}
	
	else if($cbo_tag_report == 70){ // quick_costing_approval_v3_controller

		define('ENTRY_FORM', 70);

		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'BRAND_ID' => $brand_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
 
		$con = connect();     
		$flag = 1;
		foreach($sync_data_arr as $APP_ROWS){
		
			//app mst......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$where_con = " and buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
				if($APP_ROWS['BRAND_ID'] != ''){$where_con .= " and BRAND_ID in(".$APP_ROWS['BRAND_ID'].")";}
				
				$APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].", APPROVED_BY=".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in(select qc_no from QC_MST where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY=".$APP_ROWS['FROM_APPROVED_BY']." and APPROVED > 0 $where_con)";
		 		//echo $APPROVAL_MST_SQL;die;
			 	//$eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
			}

			//echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
			
			//mst.........................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$where_con = " and buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
				if($APP_ROWS['BRAND_ID'] != ''){$where_con .= " and BRAND_ID in(".$APP_ROWS['BRAND_ID'].")";}

				$MST_SQL = "update QC_MST set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY=".$APP_ROWS['FROM_APPROVED_BY']."  and APPROVED > 0 $where_con";
				echo $MST_SQL;die;
				//$eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
			}
		}
		//print_r($eq_rr);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
    // short_feb_booking_approval_controller
	else if($cbo_tag_report == 12){

		define('ENTRY_FORM', 12);

		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
 
		$con = connect();     
		$flag = 1;
		foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_HISTORY_SQL = "update  APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."  AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and MST_ID  in(select a.id from wo_booking_mst a where a.COMPANY_ID = ".$APP_ROWS['COMPANY_ID']."  and READY_TO_APPROVED = 1 and item_category in(2,3,13) $buyer_con)";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
		}
		//print_r($eq_rr['history']);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// sample_feb_booking_wo_approval_controller
	else if($cbo_tag_report == 13){

		define('ENTRY_FORM',13);

		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
 
	    $con = connect();     
		$flag = 1;
		foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_HISTORY_SQL = "update  APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."  AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and MST_ID  in(select a.id from wo_booking_mst a where a.COMPANY_ID = ".$APP_ROWS['COMPANY_ID']." and a.is_short=2  and A.booking_type=4  and A.READY_TO_APPROVED = 1 and A.item_category in(2,3,13) and a.IS_APPROVED in(1,3) $buyer_con)";
				// echo $APPROVAL_HISTORY_SQL;die;
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
		}
		//print_r($eq_rr['history']);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// non_order_sample_booking_approval_controller
	else if($cbo_tag_report == 9){
 
		define('ENTRY_FORM', 9);
  
		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
	
		$sync_data_arr[2] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
	
		$sync_data_arr[3] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' =>  $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
	 
		$con = connect();     
		$flag = 1;
	    foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in( select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_ord_samp_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.entry_form_id in(90,610,140,439) and a.item_category in(2,3,13)  and a.ready_to_approved=1 and a.company_id=".$APP_ROWS['COMPANY_ID']." $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
	    }
	    // print_r($eq_rr['history']);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// trims_booking_approval_controller
	else if($cbo_tag_report == 8){
		//SYNC .................................
		define('ENTRY_FORM', 8);
  

		$sync_data_arr[2] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];

		$con = connect();     
		$flag = 1;
		foreach($sync_data_arr as $APP_ROWS){
			// history......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_booking_mst a where a.status_active=1 and a.is_deleted=0  and a.item_category in(4)  and a.ready_to_approved=1 and a.company_id=".$APP_ROWS['COMPANY_ID']." $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
		}
		//print_r($eq_rr['history']);oci_rollback($con);die;
		
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// fabric_booking_approval_controller v2
	else if ($cbo_tag_report==7){
		//SYNC .................................
		define('ENTRY_FORM', 7);
   
		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
	 
		$con = connect();     
		$flag = 1;
		foreach($sync_data_arr as $APP_ROWS){
        
			//history......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
	 
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']."  where id in(
				select id from APPROVAL_HISTORY where mst_id in(select id from wo_booking_mst where IS_APPROVED in(1,3) and STATUS_ACTIVE=1 and READY_TO_APPROVED=1 and COMPANY_ID={$APP_ROWS['COMPANY_ID']} $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']."   and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
	
	
			//app mst......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in(select id from wo_booking_mst where IS_APPROVED in(1,3) and STATUS_ACTIVE=1 and READY_TO_APPROVED=1  and COMPANY_ID={$APP_ROWS['COMPANY_ID']}  and APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." $buyer_con) ";
				$eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
			}
	
			//echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
			
			//mst.........................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
				$MST_SQL = "update wo_booking_mst set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." and IS_APPROVED in(1,3) $buyer_con";
				$eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
			}
	
	
	
	   }
	
	    // print_r($eq_rr['history']);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// pi_approval_new
	else if ($cbo_tag_report==27){

		define('ENTRY_FORM', 27);

		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
 
		$con = connect();     
		$flag = 1;
	    foreach($sync_data_arr as $APP_ROWS){
			// history......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(select id from APPROVAL_HISTORY where mst_id in(select a.id  from com_pi_master_details a where a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and a.importer_id=".$APP_ROWS['COMPANY_ID']." $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
	    } 
	    // print_r($eq_rr['history']);oci_rollback($con);die; 
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// purchase_requisition_approval_controller_v2
	else if ($cbo_tag_report==1){

		define('ENTRY_FORM', 1);
   
		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
		$con = connect();     
		$flag = 1;
	    foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
				//if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
				//if($APP_ROWS['LOCATION_ID'] != ''){$buyer_con .= " and a.location_id in(".$APP_ROWS['LOCATION_ID'].")";}
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']."  where id in(
				select id from APPROVAL_HISTORY where mst_id in(select id from inv_purchase_requisition_mst where ENTRY_FORM=69 and IS_APPROVED in(1,3) and STATUS_ACTIVE=1 and READY_TO_APPROVE=1 and COMPANY_ID={$APP_ROWS['COMPANY_ID']} $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']."   and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			} 
			//app mst......................................................................
			if($flag == 1){
			   // if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in(select id from inv_purchase_requisition_mst where ENTRY_FORM=69 and IS_APPROVED in(1,3) and STATUS_ACTIVE=1 and READY_TO_APPROVE=1  and COMPANY_ID={$APP_ROWS['COMPANY_ID']}  and APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." $buyer_con)";
				$eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
			}
			//echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
			//mst.........................................................................
			if($flag == 1){
			   // if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_id in(".$APP_ROWS['BUYER_ID'].")";}
				$MST_SQL = "update inv_purchase_requisition_mst set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." and IS_APPROVED in(1,3) $buyer_con";
				$eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
			}
	    }
	    // print_r($eq_rr['history']);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// pre_costing_approval_wvn_v2_controller
	else if ($cbo_tag_report==46){
		define('ENTRY_FORM',46);
  
		$sync_data_arr[2] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
		
		$con = connect();     
		$flag = 1;
	    foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and b.buyer_name in(".$APP_ROWS['BUYER_ID'].")";}
				//if($APP_ROWS['LOCATION_ID'] != ''){$buyer_con .= " and a.location_id in(".$APP_ROWS['LOCATION_ID'].")";}
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']."  where id in(
				select id from APPROVAL_HISTORY where mst_id in(select a.id from wo_pre_cost_mst a,wo_po_details_master b where  a.job_id=b.id and b.company_name={$APP_ROWS['COMPANY_ID']} and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']."   and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
		    // echo  $APPROVAL_HISTORY_SQL;oci_rollback($con);die;
			//app mst......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_name in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in(select a.id from wo_pre_cost_mst a,wo_po_details_master b where  a.job_id=b.id and b.company_name={$APP_ROWS['COMPANY_ID']} and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and  a.READY_TO_APPROVEd=1 and a.APPROVED_SEQU_BY = ".$APP_ROWS['FROM_APPROVED_BY']." $buyer_con)";
				$eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
			}
		    // echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
			//mst.........................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_name in(".$APP_ROWS['BUYER_ID'].")";}
				$MST_SQL = "update wo_pre_cost_mst set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED in(1,3) and READY_TO_APPROVEd=1 and job_id in(select b.id from wo_po_details_master b where  b.company_name={$APP_ROWS['COMPANY_ID']} and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $buyer_con)";
				$eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
			}
		   // echo  $MST_SQL;oci_rollback($con);die;
	    }
	    // print_r($eq_rr['history']);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// pre_costing_approval_controller
	else if ($cbo_tag_report==15){

		define('ENTRY_FORM', 15);
  
		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
		
		$con = connect();     
		$flag = 1;
		foreach($sync_data_arr as $APP_ROWS){
        
			//history......................................................................
			if($flag == 1){
				/*
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and b.buyer_name in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']."  where id in(
				select id from APPROVAL_HISTORY where mst_id in(select a.id from wo_pre_cost_mst a,wo_po_details_master b where  a.job_id=b.id and b.company_name={$APP_ROWS['COMPANY_ID']}  and a.APPROVED in(1,3) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
				*/
			}
		   //echo  $APPROVAL_HISTORY_SQL;oci_rollback($con);die;
	
			//app mst......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_name in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in(select a.id from wo_pre_cost_mst a,wo_po_details_master b where  a.job_id=b.id  and a.APPROVED in(1,3) and b.company_name={$APP_ROWS['COMPANY_ID']} and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and  a.READY_TO_APPROVEd=1 and a.APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." $buyer_con)";
				
				 //echo $APPROVAL_MST_SQL;die;
				
				$eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
			}
	
		  // echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
			
			//mst.........................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and buyer_name in(".$APP_ROWS['BUYER_ID'].")";}
				$MST_SQL = "update wo_pre_cost_mst set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED in(1,3) and READY_TO_APPROVEd=1 and job_id in(select b.id from wo_po_details_master b where  b.company_name={$APP_ROWS['COMPANY_ID']} and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 $buyer_con)";
				$eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
			}
	
		   // echo  $MST_SQL;oci_rollback($con);die;
	   }
	



	   if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// lab_test_approval_controller_v2
	else if ($cbo_tag_report==78){

		define('ENTRY_FORM', 78);

		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
	 
		$con = connect();     
		$flag = 1;
	    foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and c.BUYER_NAME in(".$APP_ROWS['BUYER_ID'].")";}
	  
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']."  where id in(
				select id from APPROVAL_HISTORY where mst_id in(select a.id from wo_labtest_mst a,wo_labtest_dtls b,wo_po_details_master c where a.id=b.mst_id and b.job_no=c.job_no and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.IS_APPROVED in(1,3) and a.READY_TO_APPROVED=1 and a.COMPANY_ID={$APP_ROWS['COMPANY_ID']}  $buyer_con) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']."   and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
			//app mst......................................................................
			if($flag == 1){
				if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and c.BUYER_NAME in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in( select a.id from wo_labtest_mst a,wo_labtest_dtls b,wo_po_details_master c where a.id=b.mst_id and b.job_no=c.job_no and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.IS_APPROVED in(1,3) and a.READY_TO_APPROVED=1 and a.COMPANY_ID={$APP_ROWS['COMPANY_ID']} and a.APPROVED_SEQU_BY ={$APP_ROWS['FROM_APPROVED_BY']}  $buyer_con)";
				$eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
			}
			//echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
			//mst.........................................................................
			if($flag == 1){
				$MST_SQL = "update wo_labtest_mst set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and IS_APPROVED in(1,3) and READY_TO_APPROVEd=1 and COMPANY_ID={$APP_ROWS['COMPANY_ID']} and id in(select a.id from wo_labtest_mst a,wo_labtest_dtls b,wo_po_details_master c where a.id=b.mst_id and b.job_no=c.job_no and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.IS_APPROVED in(1,3) and a.READY_TO_APPROVED=1 and a.COMPANY_ID={$APP_ROWS['COMPANY_ID']} and a.APPROVED_SEQU_BY ={$APP_ROWS['FROM_APPROVED_BY']}  $buyer_con)";
				$eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
			}
	    } 
	   //print_r($eq_rr['history']);oci_rollback($con);die;
		 
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// embellishment_work_order_approval_controller
	else if ($cbo_tag_report==32){
        //SYNC .................................
		define('ENTRY_FORM', 32); 
		
		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];

		$con = connect();     
		$flag = 1;
		foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
			// if($APP_ROWS['BUYER_ID'] != ''){$buyer_con = " and a.BUYER_ID in(".$APP_ROWS['BUYER_ID'].")";}
				$APPROVAL_HISTORY_SQL = "update  APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."  AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and MST_ID  in(select a.id from wo_booking_mst a where  a.company_id={$APP_ROWS['COMPANY_ID']} and a.is_short in(2,3) and a.booking_type=6 and a.item_category=25 and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and a.is_approved  in (1,3)  $buyer_con)";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
		}
		//print_r($eq_rr['history']);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// yarn_work_order_approval_controller
	else if ($cbo_tag_report==2){

		define('ENTRY_FORM', 2);
  
		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
	
		$sync_data_arr[2] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
	 
	 
		$con = connect();     
		$flag = 1;
	    foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
					select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=144  and b.item_category_id=1 and a.ready_to_approved=1 and a.company_name=".$APP_ROWS['COMPANY_ID']."  and a.is_approved in(1,3)) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
					)";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
	    } 
	    // print_r($eq_rr['history']);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// service_work_order_approval_controller
	else if ($cbo_tag_report==60){

		//SYNC .................................
		define('ENTRY_FORM', 60);
  
		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
		 
		$con = connect();     
		$flag = 1;
	    foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
					select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_order_info_mst a where a.status_active=1 and a.is_deleted=0 and a.entry_form=484 and a.ready_to_approved=1 and a.company_name=".$APP_ROWS['COMPANY_ID']."  and a.is_approved in(1,3)) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
					)";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
	    }
	    // print_r($eq_rr['history']);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// stationary_work_order_approval_controller
	else if ($cbo_tag_report==5){

		//SYNC .................................
		define('ENTRY_FORM', 5);

		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
	 
		$con = connect();     
		$flag = 1;
	    foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
					select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=146  and b.item_category_id not in(1,2,3,12,13,14) and a.ready_to_approved=1 and a.company_name=".$APP_ROWS['COMPANY_ID']."  and a.is_approved in(1,3)) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
					)";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
	    } 
	    // print_r($eq_rr['history']);oci_rollback($con);die;
		 
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// dyes_chemical_wo_approval_controller
	else if ($cbo_tag_report==3){
		
		//SYNC .................................
		define('ENTRY_FORM', 3); 
	
		$sync_data_arr[1] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];

		$con = connect();     
		$flag = 1;
	    foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where id in(
					select id from APPROVAL_HISTORY where mst_id in(select a.id  from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=145  and b.item_category_id in (5,6,7,23) and a.ready_to_approved=1 and a.company_name=".$APP_ROWS['COMPANY_ID']."  and a.is_approved in(1,3)) and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM']."
					)";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
	    } 
	    // print_r($eq_rr['history']);oci_rollback($con);die;
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	// item_issue_requisition_approval_controller_v2
	else if ($cbo_tag_report==56){
		//SYNC .................................
		define('ENTRY_FORM', 56);

		$sync_data_arr[2] = [
			'COMPANY_ID' => $company_id,
			'ENTRY_FORM' => ENTRY_FORM,
			'BUYER_ID' => $buyer_id,
			'FROM_APPROVED_BY' => $cbo_form_user,
			'FROM_SEQUENCE_NO' => $cbo_form_seq,
			'TO_APPROVED_BY' => $cbo_to_user,
			'TO_SEQUENCE_NO' => $cbo_to_seq
		];
	 
		$con = connect();     
		$flag = 1;
		foreach($sync_data_arr as $APP_ROWS){
			//history......................................................................
			if($flag == 1){ 
				$APPROVAL_HISTORY_SQL = "update APPROVAL_HISTORY set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']."  where id in(select id from APPROVAL_HISTORY where mst_id in(select a.id from INV_ITEM_ISSUE_REQUISITION_MST a where  a.COMPANY_ID={$APP_ROWS['COMPANY_ID']}  and a.IS_APPROVED in(1,3) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0) and APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." and SEQUENCE_NO=".$APP_ROWS['FROM_SEQUENCE_NO']." and ENTRY_FORM = ".$APP_ROWS['ENTRY_FORM'].")";
				$eq_rr['history'][] = $flag = execute_query($APPROVAL_HISTORY_SQL,0);
			}
		   //echo  $APPROVAL_HISTORY_SQL;oci_rollback($con);die;
	
			//app mst......................................................................
			if($flag == 1){
				$APPROVAL_MST_SQL = "update APPROVAL_MST set SEQUENCE_NO=".$APP_ROWS['TO_SEQUENCE_NO'].",APPROVED_BY = ".$APP_ROWS['TO_APPROVED_BY']." where ENTRY_FORM =".$APP_ROWS['ENTRY_FORM']." AND APPROVED_BY = ".$APP_ROWS['FROM_APPROVED_BY']." AND SEQUENCE_NO = ".$APP_ROWS['FROM_SEQUENCE_NO']." and mst_id in(select a.id from INV_ITEM_ISSUE_REQUISITION_MST a where  a.COMPANY_ID={$APP_ROWS['COMPANY_ID']}  and a.IS_APPROVED in(1,3) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and  a.READY_TO_APPROVED=1 and a.APPROVED_SEQU_BY = ".$APP_ROWS['FROM_APPROVED_BY'].")";
				$eq_rr['appmst'][] = $flag = execute_query($APPROVAL_MST_SQL,0);
			}
	
		    // echo  $APPROVAL_MST_SQL;oci_rollback($con);die;
			
			//mst.........................................................................
			if($flag == 1){
				$MST_SQL = "update INV_ITEM_ISSUE_REQUISITION_MST set APPROVED_SEQU_BY=".$APP_ROWS['TO_SEQUENCE_NO']."  where APPROVED_SEQU_BY = ".$APP_ROWS['FROM_SEQUENCE_NO']." and IS_APPROVED in(1,3) and READY_TO_APPROVED=1 and COMPANY_ID={$APP_ROWS['COMPANY_ID']}";
				$eq_rr['mst'][] = $flag = execute_query($MST_SQL,0);
			}
		   // echo  $MST_SQL;oci_rollback($con);die;
	    }
	 
	   //print_r($eq_rr['history']);oci_rollback($con);die;
		
	 
		if($flag==1)
		{
			oci_commit($con);
			echo 1;
		}
		else
		{
			oci_rollback($con);
			echo 0;
		}
		disconnect($con);
		die;
	}
	//echo '0';
	echo "1**0";
 
}

?>
