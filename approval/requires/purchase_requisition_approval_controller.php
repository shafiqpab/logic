<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php'); 

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');


if ($action=="load_drop_down_store")
{
	$permitted_store_id=return_field_value("STORE_LOCATION_ID","user_passwd","id='".$user_id."'");
	if($permitted_store_id){$storCon=" and id in($permitted_store_id)";}
	echo create_drop_down( "cbo_store_id", 130, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id=$data $storCon order by store_name","id,store_name", 1, "-- All --","","",0,"","","","");
	exit();
}


if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>	

	<script>

	// flowing script for multy select data------------------------------------------------------------------------------start;
	  function js_set_value(id)
	  { 
	 // alert(id)
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }

	// avobe script for multy select data------------------------------------------------------------------------------end;

	</script>
	<form>
	        <input type="hidden" id="selected_id" name="selected_id" /> 
	       <?php
	        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
			 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
			 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
				$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by sequence_no";
			 $arr=array (2=>$custom_designation,3=>$Department);
			 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
			?>
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>
	<?
}


if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);


	if($approval_type==0)
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

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=1 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",1,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history=="" && $approved_no_cause!="")
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*1*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history!="" && $approved_no_cause!="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=1 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",1,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*1*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}
			else if($approved_no_history!="" && $approved_no_cause=="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=1 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",1,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=1 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*1*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}

		}

		if ($operation==1)  // Update Here
		{

		}

	}//type=0
	
}

if ($action=="appcause_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);

	$data_all=explode('_',$data);

	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];

	if($app_cause=="")
	{
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=1 and user_id='$user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
		//echo $sql_cause; //die;
		$nameArray_cause=sql_select($sql_cause);
		if(count($nameArray_cause)>0){
			foreach($nameArray_cause as $row)
			{
				$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
				$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
			}
		}
		else
		{
			$app_cause = '';
		}
	}

	?>
    <script>
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});

		var permission='<? echo $permission; ?>';
		function fnc_appv_entry(operation)
		{
			var appv_cause = $('#appv_cause').val();

			if (form_validation('appv_cause','Approval Cause')==false)
			{
				if (appv_cause=='')
				{
					alert("Please write cause.");
				}
				return;
			}
			else
			{

				var data="action=save_update_delete_appv_cause&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","purchase_requisition_approval_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				//release_freezing();
				//alert(http.responseText);return;

				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();

				
			}
		}

		function fnc_close()
		{
			appv_cause= $("#appv_cause").val();

			document.getElementById('hidden_appv_cause').value=appv_cause;

			parent.emailwindow.hide();
		}

		
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_req_year=str_replace("'","",$cbo_req_year);
	$txt_req_no=str_replace("'","",$txt_req_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);



	$req_date_cond=$req_no_conds='';
	if ($txt_req_no != '') $req_no_conds=" and a.requ_prefix_num=$txt_req_no";
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if($db_type==0)
		{
			$txt_date_from = date("Y-m-d", strtotime($txt_date_from));
			$txt_date_to = date("Y-m-d", strtotime($txt_date_to));
			$req_date_cond = " and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
		}
		else
		{
			$txt_date_from = date("d-M-Y", strtotime($txt_date_from));
			$txt_date_to = date("d-M-Y", strtotime($txt_date_to));
			$req_date_cond = " and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
		}	
	}	
	?>

	<script type="text/javascript">
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			var title = 'Approval Cause Info';
			var page_link = 'requires/purchase_requisition_approval_controller.php?data='+data+'&action=appcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}
	</script>
	<?

	$req_year_cond='';
	if($db_type==0)
	{
		if ($cbo_req_year != 0) $req_year_cond= " and year(a.insert_date)=$cbo_req_year";
		$year_cond_prefix= "year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if ($cbo_req_year != 0) $req_year_cond= " and TO_CHAR(a.insert_date,'YYYY')=$cbo_req_year";
		$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";
	}
	//echo $req_date_cond.'system'.$req_year_cond;die;
	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="")
	{		
		$user_id=$txt_alter_user_id;	
	}

	$userCredentialCategory = sql_select("SELECT item_cate_id as ITEM_CATE_ID FROM user_passwd where id=$user_id");
	$item_cate_id = $userCredentialCategory[0]['ITEM_CATE_ID'];
	//echo $item_cate_id;die;
	if($item_cate_id !='') $item_category_credential_cond = $item_cate_id ;
	else $item_category_credential_cond = implode(",",array_keys($item_category));
	
	//$permitted_item_category = implode(',',(array_keys($item_category)));
	//$permitted_item_category=return_field_value("item_cate_id","user_passwd","id=".$user_id."");

	/* $permitted_item_category ='';
	
	if($permitted_item_category)
	{
		$permitted_item_category=$permitted_item_category;
	}
	else
	{
		$permitted_item_category=implode(",", array_flip(array_diff($item_category, explode(",", "1,2,3,12,13,14"))));
	}
	
	if(str_replace("'","",$cbo_item_category_id)==0) $item_category_id=$permitted_item_category; else $item_category_id=str_replace("'","",$cbo_item_category_id); */


	//$user_id=7;
	//echo " select sequence_no from electronic_approval_setup where page_id=$menu_id and user_id=$user_id";die;
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted = 0");
	// echo "company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted = 0";
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and is_deleted = 0");
	
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pre-Costing.</font>";
		die;
	}	
	
	$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1  and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	$approval_case_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		
		if($rowu[csf('approval_type')]==2)
		{
			$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
		}
		$approval_case_arr[$rowu[csf('booking_id')]][$rowu[csf('approval_type')]]=$rowu[csf('approval_cause')];
	}
	

	
	if($previous_approved==1 && $approval_type==1)	//approval process with prevous approve start
	{
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";
		if($db_type == 0 )
			{$select_item_cat = "group_concat(c.item_category) as item_category_id ";
		}else{
			$select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
		}

		$sql="SELECT a.id,a.remarks,a.remarks, a.company_id, a.requ_no,a.requ_prefix_num, $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, b.id as approval_id, a.is_approved, a.department_id, sum(c.amount) as req_value, b.approved_date, b.approved_by 
		from inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c 
		where a.id=b.mst_id and a.id = c.mst_id and a.entry_form=69 and b.entry_form=1 and a.company_id=$company_name and c.item_category not in(1,2,3,12,13,14) and c.item_category in($item_category_credential_cond) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.is_approved in (1,3) $sequence_no_cond $req_year_cond $req_date_cond $req_no_conds
		group by a.id,a.remarks, a.company_id, a.requ_no,a.requ_prefix_num, $year_cond_prefix, a.requisition_date, a.delivery_date, b.id, a.is_approved, a.department_id, b.approved_date, b.approved_by 
		order by a.id ";
		// echo "$sql";
	}
	else if($approval_type==0)	// unapproval process start
	{
		 
		if($user_sequence_no==$min_sequence_no)//"1,2,3,12,13,14 // First user
		{
			if($db_type==0)
			{
				$select_item_cat = "group_concat(b.item_category) as item_category_id ";
			}else{
				$select_item_cat = "listagg(b.item_category, ',') within group (order by b.item_category) as item_category_id ";
			}

			 	$sql ="SELECT a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, 0 as approval_id, a.is_approved, a.department_id, sum(b.amount) as req_value
				from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
				where a.id = b.mst_id and a.entry_form=69 and a.company_id=$company_name and b.item_category not in(1,2,3,12,13,14)  and b.item_category in($item_category_credential_cond) and a.is_approved in($approval_type,2) and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 $req_year_cond $req_date_cond $req_no_conds
				group by a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix , a.requisition_date, a.delivery_date, a.is_approved, a.department_id
				order by a.id";
		}
		else // Next user
		{
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
						
			if($sequence_no=="") // bypass if previous user Yes
			{
				if($db_type==0)
				{
					
					$seqSql="select group_concat(sequence_no) as sequence_no_by  from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					
					$requsition_id=return_field_value("group_concat(distinct(b.mst_id)) as requsition_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id = c.mst_id and a.entry_form=69 and a.company_id=$company_name  and c.item_category in($item_category_credential_cond) and c.item_category not in(1,2,3,12,13,14) and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1 $date_cond","requsition_id");
					$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
					
					$requsition_id_app_byuser=return_field_value("group_concat(distinct(b.mst_id)) as requsition_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id = c.mst_id and a.entry_form=69 and a.company_id=$company_name and a.ready_to_approve=1 and  c.item_category in ($item_category_id)  and c.item_category in($item_category_credential_cond) and c.item_category not in(1,2,3,12,13,14) and b.sequence_no=$user_sequence_no and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1","requsition_id");
				}
				else
				{
					//$sequence_no_by=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0","sequence_no");
					/*echo $seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];*/
					
					$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
					//echo $seqSql;die;
					$seqData=sql_select($seqSql);
					
					$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
					foreach($seqData as $sRow)
					{
						if($sRow[csf('bypass')]==2)
						{
							$sequence_no_by_no.=$sRow[csf('sequence_no')].",";
							if($sRow[csf('buyer_id')]!="") 
							{
								$buyerIds.=$sRow[csf('buyer_id')].",";
								
								$buyer_id_arr=explode(",",$sRow[csf('buyer_id')]);
								$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
								if(count($result)>0)
								{
									$query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and a.buyer_id in(".implode(",",$result).")) or ";
								}
								$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
							}
						}
						else
						{
							$sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
						}
					}
					//var_dump($check_buyerIds_arr);die;
					$buyerIds=chop($buyerIds,',');
					if($buyerIds=="") 
					{
						$buyerIds_cond=""; 
						$seqCond="";
					}
					else 
					{
						$buyerIds_cond=" and a.buyer_id not in($buyerIds)";
						$seqCond=" and (".chop($query_string,'or ').")";
					}
					//echo $seqCond;die;
					$sequence_no_by_no=chop($sequence_no_by_no,',');
					$sequence_no_by_yes=chop($sequence_no_by_yes,',');
					
					if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
					if($sequence_no_by_no=="") $sequence_no_by_no=0;

				
					
					//$requsition_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as requsition_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id = c.mst_id and a.company_id=$company_name and c.item_category not in(1,2,3,12,13,14) and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1  $date_cond","requsition_id");
					
					//echo "select LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as requsition_id from inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c where a.id=b.mst_id and a.id = c.mst_id and a.company_id=$company_name and c.item_category not in(1,2,3,12,13,14) and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1  $date_cond";


					//$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
					
					//$requsition_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as requsition_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id = c.mst_id and a.company_id=$company_name and c.item_category not in(1,2,3,12,13,14) and b.sequence_no=$user_sequence_no and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1","requsition_id");
					//$requsition_id_app_byuser=implode(",",array_unique(explode(",",$requsition_id_app_byuser)));
					
					$booking_id='';
					$booking_id_sql="select distinct (b.mst_id) as booking_id from inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c where a.id=b.mst_id and a.id=c.mst_id and a.company_id=$company_name  and c.item_category in($item_category_credential_cond) and c.item_category not in(1,2,3,12,13,14) and b.sequence_no in ($sequence_no_by_yes) and a.entry_form=69 and b.entry_form=1 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond";
					//echo $booking_id_sql;die;
					$bResult=sql_select($booking_id_sql);
					foreach($bResult as $bRow)
					{
						$booking_id.=$bRow[csf('booking_id')].",";
					}
					
					$booking_id=chop($booking_id,',');
					
					$booking_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as booking_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id=c.mst_id and a.company_id=$company_name  and c.item_category in($item_category_credential_cond) and c.item_category not in(1,2,3,12,13,14) and b.sequence_no=$user_sequence_no and a.entry_form=69 and b.entry_form=1 and b.current_approval_status=1","booking_id");
					$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
					
					$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
					$booking_id=implode(",",$result);
					
					$booking_id_cond="";
					if($booking_id!="")
					{
						if($db_type==2 && count($result)>999)
						{
							$booking_id_chunk_arr=array_chunk($result,999) ;
							foreach($booking_id_chunk_arr as $chunk_arr)
							{
								$chunk_arr_value=implode(",",$chunk_arr);	
								$bokIds_cond.=" a.id in($chunk_arr_value) or ";	
							}
							
							$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
							//echo $booking_id_cond;die;
						}
						else
						{
							$booking_id_cond=" and a.id in($booking_id)";	 
						}
					}
					else $booking_id_cond="";
				}
				$result=array_diff(explode(',',$requsition_id),explode(',',$requsition_id_app_byuser));
				$requsition_id=implode(",",$result);
				// print_r($requsition_id);
				if($db_type==0)
				{
					$select_item_cat = "group_concat(b.item_category) as item_category_id ";
				}else{
					
					$select_item_cat = "listagg(b.item_category, ',') within group (order by b.item_category) as item_category_id ";
				}
				
				
				$sql=" SELECT x.* from  (SELECT DISTINCT (a.id),a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, a.is_approved, a.department_id, sum(b.amount) as req_value
				from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id =b.mst_id and a.entry_form=69 and a.company_id=$company_name and b.item_category in($item_category_credential_cond) and b.item_category not in(1,2,3,12,13,14) and a.is_approved in(0,2) and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_year_cond $req_date_cond $req_no_conds
				GROUP by a.id, a.company_id,a.remarks, a.requ_no, a.requ_prefix_num , $year_cond_prefix, a.requisition_date, a.delivery_date, a.is_approved, a.department_id"; 
				if($booking_id!="")
				{
					$sql.=" UNION ALL

					SELECT DISTINCT (a.id),a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, a.is_approved, a.department_id, sum(b.amount) as req_value
					from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id =b.mst_id and a.entry_form=69 and a.company_id=$company_name and b.item_category in($item_category_credential_cond) and b.item_category not in(1,2,3,12,13,14) and a.is_approved in(3) and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_year_cond $req_date_cond $booking_id_cond $req_no_conds
					GROUP by a.id,a.remarks, a.company_id,a.remarks, a.requ_no, a.requ_prefix_num , $year_cond_prefix, a.is_approved, a.requisition_date, a.delivery_date, a.department_id) x  order by x.id";
					// echo $sql;
				}
				else
				{ 
					$sql="SELECT DISTINCT (a.id),a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, a.is_approved, a.department_id, sum(b.amount) as req_value
					from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id =b.mst_id and a.entry_form=69 and a.company_id=$company_name and b.item_category in($item_category_credential_cond) and b.item_category not in(1,2,3,12,13,14) and a.is_approved=$approval_type and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_year_cond $req_date_cond $req_no_conds
					group by a.id, a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix, a.requisition_date, a.delivery_date, a.is_approved, a.department_id order by a.id";//and a.is_approved in (0,2,3)
				}
			}
			else // bypass No
			{

				$user_sequence_no=$user_sequence_no-1;
				// echo $user_sequence_no;die;
				if($sequence_no==$user_sequence_no) 
				{
					$sequence_no_by_pass=$sequence_no;
					$sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
					if($db_type==0) 
					{
						$select_item_cat = "group_concat(c.item_category) as item_category_id ";
					}
					else if($db_type==2) 
					{
						$select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
					}
				}
				else
				{
					if($db_type==0) 
					{
						$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");

						$select_item_cat = "group_concat(c.item_category) as item_category_id ";

					}
					else if($db_type==2) 
					{
						$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no","sequence_no");// and bypass=1

						//echo $user_sequence_no;die;
						 //echo "select listagg(sequence_no,',') within group (order by sequence_no) as sequence_no from electronic_approval_setup where company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1";die;
						
						
						$select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
					}
					
					if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
					else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
				
				}
					$sql="SELECT a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num, $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, b.id as approval_id, a.is_approved, a.department_id, sum(c.amount) as req_value, b.approved_date, b.approved_by  
					from inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c 
					where a.id=b.mst_id and a.id = c.mst_id and a.ready_to_approve=1 and a.entry_form=69 and b.entry_form=1 and a.company_id=$company_name  and c.item_category in($item_category_credential_cond) and c.item_category not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.is_approved in (1,3) $sequence_no_cond $req_year_cond $req_date_cond $req_no_conds
					group by a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num, $year_cond_prefix, a.requisition_date, a.delivery_date, b.id, a.is_approved, a.department_id, b.approved_date, b.approved_by order by a.id";
					 //  echo $sql;die;
			}
		}
	}
	else // approval process start
	{
		$sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		if($db_type == 0 )
			{$select_item_cat = "group_concat(c.item_category) as item_category_id ";
		}else{
			$select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
		}
		$sql="SELECT a.id,a.remarks, a.company_id, a.requ_no,a.requ_prefix_num, $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, b.id as approval_id, a.is_approved, a.department_id, sum(c.amount) as req_value, b.approved_date, b.approved_by 
		from inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c 
		where a.id=b.mst_id and a.id = c.mst_id and a.entry_form=69 and b.entry_form=1 and a.company_id=$company_name  and c.item_category in($item_category_credential_cond) and c.item_category not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.is_approved in (1,3) $sequence_no_cond $req_year_cond $req_date_cond $req_no_conds 
		group by a.id,a.remarks, a.company_id, a.requ_no,a.requ_prefix_num, $year_cond_prefix, a.requisition_date, a.delivery_date,b.id, a.is_approved, a.department_id, b.approved_date, b.approved_by  
		order by a.id ";
	}
	
 	//echo $sql;die;
 
	$department_arr=return_library_array( "select id, department_name from lib_department", 'id', 'department_name' );
	$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id =39 and is_deleted=0 and status_active=1");
    /*echo "select format_id from lib_report_template where template_name='3' and module_id=6 and report_id=39 and is_deleted=0 and status_active=1";*/
    //var_dump($print_report_format);a.approved_date,a.approved_by
    $format_ids=explode(",",$print_report_format);
    // print_r($format_ids);
    $cause_with=0;
   if($approval_type==0){

    $cause_with=100;
   }
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo 1170+$cause_with;?>px; margin-top:10px">
        <legend>Purchase Requisition Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo 1140+$cause_with;?>" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="50">SL</th>
                    <th width="120">Requisition No</th>
                    <th width="120">Image/File</th>
                    <th width="60">Year</th>
                    <th width="150">Item Category</th>
                    <th width="100">Depatment</th>
                    <th width="100">Requisition Value</th>                    
                    <th width="70">Requisition Date</th>
                    <th  width="70" title="Delivery Date">In-House Demand date</th>
                    <th  width="130">Last Approval Date and Person</th>
					<th <? if($approval_type==0) {echo 'width="100"';} ?>> Un-approve request</th>
						<? 
					if($approval_type==0)
					{
						?>
					 <th>Not Appv. Cause</th>
						<? 
					}

						?>
                </thead>
            </table>
            <div style="width:<? echo 1140+$cause_with;?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo 1222+$cause_with;?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1; $j=0;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$value='';
								$unapprove_value_id=$row[csf('id')];
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									$app_id = sql_select("select id from approval_history where mst_id='".$row[csf('id')]."' and entry_form='1'  order by id desc");
									
									$value=$row[csf('id')]."**".$app_id[0][csf('id')];
								}

								$variable='';
								if($format_ids[$j]==120) // Print Report 2 //121,122,123,129,169,227,118,119
                                {
                                    $type=3;
                                }
                               elseif($format_ids[$j]==121) // Print Report 2 //121,122,123,129,169,227,118,119
                                {
                                    $type=4;
                                }
                                elseif($format_ids[$j]==122) // Print Report 3
                                {	
                                	$type=5;
                                }
                                elseif($format_ids[$j]==123) // Print Report 4
                                {
                                    $type=6;
                                }
                                elseif($format_ids[$j]==129) // Print Report 5
                                {
                                    $type=7;
                                }
                                elseif($format_ids[$j]==169) // Print Report 6
                                { 
                                    $type=8;
                                }
                                elseif($format_ids[$j]==227) // Print Report 8
                                {
                                    $type=10;
                                }
                                elseif($format_ids[$j]==118) // Print Report With Group
                                {
                                    $type=1;
                                }
                                elseif($format_ids[$j]==119) // Print Report Without Group
                                {
                                    $type=2;
                                }
                                elseif($format_ids[$j]==241) // Print Report Without Group
                                {
                                    $type=11;
                                }
                                elseif($format_ids[$j]==419) // Print 22
                                {
                                    $type=12;
                                }
                                elseif($format_ids[$j]==719) // Print 22
                                {
                                    $type=13;
                                }
                                else{
                                   $type=0;   
                                }
                                   $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('id')]."','Purchase Requisition','".$row[csf('is_approved')]."','".$row[csf('remarks,')]."','".$type."')\"> ".$row[csf('requ_prefix_num')]." <a/>";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" /> 
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                        <input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
                                    </td>   
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="120" align="center"><p><? echo $variable; ?></p></td>
									<td width="120" align="right"><input type="button" class="image_uploader" style="width:70px" value="view" onClick="openImgFile ('<?=$row[csf('ID')];?>','purchase_requisition')"></td>
                                    <td width="60"><p><? echo $row[csf('year')]; ?></p></td>
                                    <td width="150">
                                    	<p>
                                    		<? 
                                    			$item_category_names = "";$item_id_arr = array();
                                    			//$item_id_arr= array_unique(explode(",", $row[csf('item_category_id')]));
                                    			$item_id_arr= array_unique(explode(",", $row[csf("item_category_id")]));
                                    			foreach($item_id_arr as $item_id)
                                    			{
                                    				$item_category_names .= $item_category[$item_id].",";
                                    			}
                                    			echo chop($item_category_names, ",");
                                    		?>
                                    	</p>
                                    </td>
                                    <td width="100" align="center"><p><? echo $department_arr[$row[csf('department_id')]]; ?></p></td>
                                    <td width="100" align="right"><p><? echo number_format($row[csf('req_value')],2); ?></p></td>
									<td width="70" align="center"><? if($row[csf('requisition_date')]!="0000-00-00") echo change_date_format($row[csf('requisition_date')]); ?>&nbsp;</td>
									<td width="70" align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
									<td width="130" align="center"><p><? echo $row[csf('approved_date')].'<br>'.$user_arr[$row[csf('approved_by')]]; ?></p></td>
									<td <? if($approval_type==0) {echo 'width="100"';} ?>  align="center"> 
									<p>
									<? 
									//echo $row[csf('id')].'='.$unapproved_request_arr[$row[csf('id')]];
										if($approval_type==1)
										{
											$unapproved_request=$unapproved_request_arr[$row[csf('id')]]; 
											if($unapproved_request!='')
											{
												$view_request='View';
											}
										}
										else
										{
											$unapproved_request=''; 
											$view_request='';
										}
										
									?>
									<a href="#report_details" onClick="openmypage('<? echo $unapproved_request; ?>','unapprove_request_action','Unapprove Request Details')"><? echo $view_request; ?></a>
									  </p>&nbsp;</td>

									  <? 

									if($approval_type==0)
									{
										$casues=$approval_case_arr[$unapprove_value_id][$approval_type]
										?>
										 <td align="center" style="word-break:break-all">
	                                        	<input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:70px" value="<? echo $casues;?>" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$unapprove_value_id; ?>,<?=$approval_type; ?>,<?=$i;?>)">&nbsp;
	                                    </td>
										<? 
									}

										?>
									
								</tr>
								<?
								$i++;
							
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo 1020+$cause_with;?>" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/><input type="button" value="Deny" class="formbutton" style="width:100px;; display:<?=($approval_type==1)?'none':'';?> <?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
<?
	exit();	
}

if ($action=="unapprove_request_action")
{
	echo load_html_head_contents("Un Approval Request","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data_all=explode('_',$data);
	$requ_unapprove=$data_all[1];
	//$unapp_request=$data_all[1];
?>
<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_request" id="unappv_request" readonly class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"><? echo $requ_unapprove;?> </textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                       
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
               
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
			  <script src="../includes/functions_bottom.js" type="text/javascript"></script>
        </div>

<?
}


if($action=='purchase_requisition'){
	$file_arr=return_library_array( "select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where MASTER_TBLE_ID='".$id."' and FORM_NAME='purchase_requisition'", "ID", "IMAGE_LOCATION"  );
	foreach($file_arr as $file){
		echo "<a target='_blank' href='../../".$file."'>Download</a> ";
	}
}


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$user_id=7;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	
	if($_REQUEST['txt_alter_user_id']!="") 	$user_id_approval=$_REQUEST['txt_alter_user_id'];
	else						$user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id_approval and company_id = $cbo_company_name and is_deleted = 0");
    /*echo "select sequence_no from electronic_approval_setup where page_id=$menu_id and user_id=$user_id_approval and company_id = $cbo_company_name and is_deleted = 0"; */

    $min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted = 0");
    //echo $user_sequence_no."Tipu="."page_id=$menu_id and user_id=$user_id";die; //OK

	if($approval_type==0)
	{
		$response=$req_nos;

		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");

        if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
        //echo $partial_approval;die;

        /*$rID=sql_multirow_update("wo_non_order_info_mst","is_approved",1,"id",$req_nos,0);
        if($rID) $flag=1; else $flag=0;*/
        
        $reqs_ids=explode(",",$req_nos);
        // $field_array="id, entry_form, mst_id, approved_no, approved_by, approved_date"; 

		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date";  
		
		$i=0;
        $id=return_next_id( "id","approval_history", 1 ) ;
		$appid=return_next_id( "id","approval_mst", 1 ) ;
        
        $approved_no_array=array();
		foreach($reqs_ids as $val)
        {
            $approved_no=return_field_value("max(approved_no) as approved_no","approval_history","mst_id='$val' and entry_form=1","approved_no");
            $approved_no=$approved_no+1;
        
            if($i!=0) $data_array.=",";
             
            $data_array.="(".$id.",1,".$val.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id_approval.",'".$pc_date_time."')";
            
            $approved_no_array[$val]=$approved_no;
                
            $id=$id+1;

			//app mst data.......................
			if($app_data_array!=''){$app_data_array.=",";}
			$app_data_array.="(".$appid.",1,".$val.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$appid++;

            //$i++;
        }
		
		//echo "10**";
		$approved_string="";
    
        foreach($approved_no_array as $key=>$value)
        {
            $approved_string.=" WHEN $key THEN $value";
        }
        
        $approved_string_mst="CASE id ".$approved_string." END";
        $approved_string_dtls="CASE mst_id ".$approved_string." END";
		
		$sql_insert="INSERT into inv_pur_requisition_mst_hist(id, hist_mst_id, approved_no, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date) 
		select	
		'', id, $approved_string_mst, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  inv_purchase_requisition_mst where id in ($req_nos)";
		
		//echo $sql_insert;
		$sql_insert_dtls="INSERT into  inv_pur_requisition_dtls_hist(id, approved_no, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
		select	
		'', $approved_string_dtls, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from  inv_purchase_requisition_dtls where mst_id in ($req_nos)";

        $rID=sql_multirow_update("inv_purchase_requisition_mst","is_approved",$partial_approval,"id",$req_nos,0);    
        if($rID) $flag=1; else $flag=0;

        $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=1 and mst_id in ($approval_ids)"; //die;
        $rIDapp=execute_query($query,1);
        if($flag==1) 
        {
            if($rIDapp) $flag=1; else $flag=0; 
        }
		//echo "insert into approval_history $field_array values($data_array)";die;
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
        if($flag==1) 
        {
            if($rID2) $flag=1; else $flag=0; 
            
        }

		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			$rID6=sql_insert("approval_mst",$field_array,$app_data_array,0);
			if($rID6) $flag=1; else $flag=0; 
		}

        $rID3=execute_query($sql_insert,0);
        if($flag==1) 
        {
            if($rID3) $flag=1; else $flag=0; 
            
        }       
        $rID4=execute_query($sql_insert_dtls,1);
        if($flag==1) 
        {
            if($rID4) $flag=1; else $flag=0; 
            
        } 
        //echo "10**".$rID.'='.$rIDapp.'='.$rID2.'='.$rID3.'='.$rID4.'='.$flag; die;
        if($flag==1) $msg='19'; else $msg='21'; 
	}
	else if($approval_type==5)
	{



		//========================================================================================
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=1 and mst_id in ($req_nos) ";
	
		$nameArray=sql_select($sqlBookinghistory); $bookidstr=""; $approval_id="";
		foreach ($nameArray as $row)
		{
			if($bookidstr=="") $bookidstr=$row[csf('mst_id')]; else $bookidstr.=','.$row[csf('mst_id')];
			if($approval_id=="") $approval_id=$row[csf('id')]; else $approval_id.=','.$row[csf('id')];
		}
		
		$appBookNoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
		$approval_ids=implode(",",array_filter(array_unique(explode(",",$approval_id))));
		
		$rID=sql_multirow_update("inv_purchase_requisition_mst","ready_to_approve*IS_APPROVED",'0*2',"id",$req_nos,0);
		

		if($rID) $flag=1; else $flag=0;


		if($approval_ids!="")
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=1 and current_approval_status=1 and id in ($approval_ids)";
			//echo "10**".$query;
			$rID2=execute_query($query,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;

				
			}

			
			if($flag==1) 
			{
				$query="delete from approval_mst  WHERE entry_form=1 and mst_id in ($req_nos)";
				$rID3=execute_query($query,1); 
				if($rID3) $flag=1; else $flag=0; 
			}
		}

		//echo "10**".$rID.'='.$rID2.'='.$rID4.'='.$flag; die;
		$response=$req_nos;
		if($flag==1) $msg='50'; else $msg='51';



	}
	else
	{
		
		$req_nos = explode(',',$req_nos); 
		
		$reqs_ids=''; $app_ids='';
		
		foreach($req_nos as $value)
		{
			$data = explode('**',$value);
			$reqs_id=$data[0];
			$app_id=$data[1];
			
			if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}

		$sql_app_res=sql_select("select approval_need, allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and b.page_id=13 and a.status_active=1 and b.status_active=1 order by a.setup_date desc fetch first 1 rows only");
		foreach ($sql_app_res as $row)
		{
			$approval_need=$row[csf('approval_need')];
			$allow_partial=$row[csf('allow_partial')];
		}

		if ($approval_need==1 || $allow_partial==1)
		{
			$sql_work_order="select d.wo_number, a.requ_no from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, wo_non_order_info_dtls c, wo_non_order_info_mst d where a.id=b.mst_id and b.id=c.requisition_dtls_id and c.mst_id=d.id and a.entry_form=69 and d.entry_form in(145,146,147) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.id in($reqs_ids) group by d.wo_number, a.requ_no";
			$sql_work_order_res=sql_select($sql_work_order);
			foreach ($sql_work_order_res as $row)
			{
				if ($row[csf('wo_number')]){
					echo "40**Un Approved Not Allow Because Work Order Found (".$row[csf('wo_number')].") against This Requisition No (".$row[csf('requ_no')].")";
					disconnect($con);die;
				}
			}
		}
		
		$rID=sql_multirow_update("inv_purchase_requisition_mst","is_approved*ready_to_approve",'0*2',"id",$reqs_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=1 and mst_id in ($reqs_ids)";
		$rID2=execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
			
		// $data=$user_id."*'".$pc_date_time."'";		
		// $rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$app_ids,1);
		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id_approval."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$app_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=1 and mst_id in ($reqs_ids)";
			$rID4=execute_query($query,1); 
			if($rID4) $flag=1; else $flag=0; 
		}
		//echo $rID4;die();
		$response=$reqs_ids;
		
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response;
		}
	}
	
	if($db_type==2 || $db_type==1 )
	{
		
		if($flag==1)
		{
			oci_commit($con);
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;
	
}

?>