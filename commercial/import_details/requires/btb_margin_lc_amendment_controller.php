<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

$item_category_mix=array(1=>"Yarn",2=>"Knit Finish Fabrics",3=>"Woven Fabrics",4=>"Accessories",5=>"Chemicals",6=>"Dyes",7=>"Auxilary Chemicals",8=>"Spare Parts",9=>"Spare Parts & Machinaries",10=>"Other Capital Items",11=>"Stationaries",12=>"Services - Fabric",13=>'Grey Fabric(Knit)',14=>'Grey Fabric(woven)',15=>'Electical',16=>'Maintenance',17=>'Medical',18=>'ICT',19=>'Print & Publication',20=>'Utilities & Lubricants',21=>'Construction Materials',22=>'Printing Chemicals & Dyes',23=>'Dyes Chemicals & Auxilary Chemicals',24=>'Services - Yarn Dyeing ',25=>'Services - Embellishment',28=>'Cut Panel',30=>'Garments',31=>'Services Lab Test',32=>'Vehicle Components',33=>'Others',110=>'Knit Fabric');

//------------------------------------------Load Drop Down on Change---------------------------------------------//
if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);
	if($data[2]){
		$suppyler_id="and c.id='$data[2]'";
	}

	if ($data[1]==0) 
	{
		echo create_drop_down( "cbo_supplier_id",165,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[3]==1)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$data[2] order by comp.company_name",'id,company_name', 0, '----Select----',0,0,1);
	}
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_supplier_id",165,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $suppyler_id and  a.tag_company='$data[0]' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name  order by c.supplier_name",'id,supplier_name', 0, '----Select----',0,0,0);		
	}
	else if($data[1]==2 || $data[1]==3 || $data[1]==13 || $data[1]==14)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name, c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $suppyler_id and a.tag_company='$data[0]' and b.party_type =9 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name  order by c.supplier_name",'id,supplier_name', 0, '----Select----',0,0,0);		
	}
	else if($data[1]==4)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $suppyler_id and a.tag_company='$data[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name  order by c.supplier_name",'id,supplier_name', 0, '----Select----',0,0,0);
		
	}
	else if($data[1]==5 || $data[1]==6 || $data[1]==7)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $suppyler_id and a.tag_company='$data[0]' and b.party_type=3 group by c.id, c.supplier_name  order by c.supplier_name",'id,supplier_name', 0, '----Select----',0,0,0);
	}
	else if($data[1]==8)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $suppyler_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name  order by c.supplier_name",'id,supplier_name', 0, '----Select----',0,0,0);
	}
	else if($data[1]==9 || $data[1]==10)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $suppyler_id and a.tag_company='$data[0]' and b.party_type = 6 group by c.id, c.supplier_name  order by c.supplier_name",'id,supplier_name', 0, '----Select----',0,0,0);
	} 
	else if($data[1]==11)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $suppyler_id and a.tag_company='$data[0]' and b.party_type = 8 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name  order by c.supplier_name",'id,supplier_name', 0, '----Select----',0,0,0);
	}
	else if($data[1]==12 || $data[1]==24 || $data[1]==25)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $suppyler_id and a.tag_company='$data[0]' and b.party_type in(20,21,22,23,24,30,31,32,35,36,37,38,39) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name  order by c.supplier_name",'id,supplier_name', 0, '----Select----',0,0,0);
	}
	else if($data[1]==110)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 0, '----Select----',0,0,0);
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $suppyler_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 0, '-- Select Supplier --',0,'',0);
	} 
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
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

				data_all=data_all+get_submitted_data_string('txt_btb_lc_no*termscondition_'+i,"");
			}
			var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
			//freeze_window(operation);
			http.open("POST","btb_margin_lc_amendment_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}

	function fnc_fabric_booking_terms_condition_reponse()
	{

		if(http.readyState == 4)
		{
		    var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==0 || reponse[0]==1)
				{
					//alert(reponse[0]);
					parent.emailwindow.hide();
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
           <input type="text" id="txt_btb_lc_no" style="text-align:center;" name="txt_btb_lc_no" value="<? echo str_replace("'","",$txt_btb_lc_no) ?>"/>
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where entry_form=706 and booking_no=$txt_btb_lc_no");// quotation_id='$data'
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
                                     <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
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
						//echo "select id, terms from  lib_terms_condition  where is_default=1 and page_id in(203,140) order by id asc ";
						$data_array2=sql_select("select id, terms from  lib_terms_condition  where is_default=1 and page_id in(706) order by id asc ");// quotation_id='$data'
						foreach( $data_array2 as $row )
							{
								$i++;
						?>
						<tr id="settr_1" align="center">
										<td>
										<? echo $i;?>
										</td>
										<td>
										<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  />
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
<script type="text/javascript">
	var data_array='<? echo count($data_array) ;?>';
	var permissions='<? echo $permission ;?>';
	if(data_array*1>0)
	{
		set_button_status(1, permissions, 'fnc_fabric_booking_terms_condition',1);
 	}

</script>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	 
	if ($operation==0 || $operation==1 )  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms,entry_form";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_btb_lc_no.",".$$termscondition.",706)";
			$id=$id+1;
		 }
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where entry_form=706 and  booking_no =".$txt_btb_lc_no."",0);
		if($operation==0)
		{
			$rID_de3=1;
		}

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 //check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_de3 ){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0];
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3 ){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	exit();

}




if($action=="btb_lc_search")
{
	echo load_html_head_contents("BTB L/C Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		function js_set_value(id,closing_status)
		{
			$('#hidden_btb_id').val(id);
			$('#hidden_ref_closing_status').val(closing_status);
			if(closing_status==1)
			{
				alert("This Reference is closed. No operation is allowed");
			}
			parent.emailwindow.hide();
		}	
    </script>

	</head>

	<body>
	<div align="center" style="width:1050px;">
		<form name="searchscfrm"  id="searchscfrm">
			<fieldset style="width:100%; margin-left:10px">
	            <legend>Enter search words</legend>           
	            	<table cellpadding="0" cellspacing="0" width="1050" class="rpt_table">
	                	<thead>
	                    	<th>Company</th>
	                        <th>File No</th>
	                        <th>LC No</th>
	                    	<th>Item Category</th>
	                        <th>Supplier</th>
	                        <th>Enter Sys Num</th>
	                        <th>L/C Date</th>
	                        <th>
	                        	<input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" />
	                        	<input type="hidden" name="id_field" id="id_field" value="" />
	                        </th>
	                    </thead>
	                    <tr class="general">
	                    	
							<td>
							   <? 
									echo create_drop_down( "txt_company_id",135,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'btb_margin_lc_amendment_controller',this.value+'_'+document.getElementById('cbo_item_category_id').value,'load_supplier_dropdown','supplier_td' );",0); 
								?>  
							 </td>
							 <td>
								 <input type="text" style="width:90px" class="text_boxes"  name="txt_file_no" id="txt_file_no" />
							 </td>                   
							 <td>
								 <input type="text" style="width:130px" class="text_boxes"  name="txt_lc_no" id="txt_lc_no" />
							 </td>                   
	                        <td> 
	                             <? echo create_drop_down( "cbo_item_category_id", 135, $item_category_mix,'', 1, '--Select--',0,"load_drop_down( 'btb_margin_lc_amendment_controller',document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_item_category_id').value,'load_supplier_dropdown','supplier_td' );",0); ?>  
	                        </td>
	                         <td align="center" id="supplier_td">
	                          <? echo create_drop_down( "cbo_supplier_id", 165,$blank_array,'', 0, '',0,0,0); ?>       
	                          
	                         </td>            
							 <td id="search_by_td">
								 <input type="text" style="width:70px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
								 <input type="hidden" id="hidden_btb_id" />
								 <input type="hidden" id="hidden_ref_closing_status" />
							 </td>    
							<td> 
	                        	 <input type="text" name="btb_start_date" id="btb_start_date" class="datepicker" style="width:70px;" />To
	                             <input type="text" name="btb_end_date" id="btb_end_date" class="datepicker" style="width:70px;" />
	                        </td>						
	                         <td>
	                 		  	<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('cbo_supplier_id').value+'**'+document.getElementById('btb_start_date').value+'**'+document.getElementById('btb_end_date').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_lc_no').value+'**'+document.getElementById('txt_file_no').value, 'create_btb_search_list_view', 'search_div', 'btb_margin_lc_amendment_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
	                         </td>
						</tr>
	               </table>
	               <table width="100%" style="margin-top:5px" align="center">
						<tr>
	                    	<td colspan="5" id="search_div" align="center"></td>
	                    </tr>
	                </table> 
	            </fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
 
}

if($action=="create_btb_search_list_view")
{
	$data=explode('**',$data);
	$company_id = $data[0];
	$item_category_id = $data[1];
	$supplier_id = $data[2];
	$lc_start_date = $data[3];
	$lc_end_date = $data[4];
	$system_id = $data[5]; 
	$lc_no = $data[6]; 
	$file_no = $data[7]; 
	
	if($company_id==0)
	{
		echo 'Select Importer';die;
	}
	
	if($company_id!=0) $company=$company_id;
	if($supplier_id!=0) $supplier=$supplier_id; else $supplier='%%';
	if($system_id!='') $system_number=$system_id; else $system_number='%%';
	if($lc_no!='') $lc_no_cond=$lc_no; else $lc_no_cond='%%';
	if($file_no!='')
	{
		$btb_file_sql="SELECT b.import_mst_id from com_export_lc a, com_btb_export_lc_attachment b where a.id=b.lc_sc_id and b.is_lc_sc=0 and a.internal_file_no='$file_no' and b.status_active=1 
		union all 
		select b.import_mst_id from com_sales_contract a, com_btb_export_lc_attachment b where a.id=b.lc_sc_id and b.is_lc_sc=1 and a.internal_file_no='$file_no' and b.status_active=1 ";
		// echo $btb_file_sql;
		$btb_file_result=sql_select($btb_file_sql);
		foreach($btb_file_result as $row)
		{
			$btb_file_id[$row["IMPORT_MST_ID"]]=$row["IMPORT_MST_ID"];
		}
		$btb_file_in=where_con_using_array($btb_file_id,0,'id');
	}
	else
	{
		$btb_file_in="";
	}
	$category_entry_form_cond="";
	if($item_category_id>0) $category_entry_form_cond=" and pi_entry_form = '".$category_wise_entry_form[$item_category_id]."'";
	
	if($lc_start_date!='' && $lc_end_date!='')
	{
		if($db_type==0)
		{
			$date = " and application_date between '".change_date_format($lc_start_date,'yyyy-mm-dd')."' and '".change_date_format($lc_end_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$date = " and application_date between '".change_date_format($lc_start_date,'','',1)."' and '".change_date_format($lc_end_date,'','',1)."'";
		}
	}
	else
	{
		$date = "";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	 
	$sql="SELECT id, $year_field btb_prefix_number, btb_system_id, lc_number, supplier_id, application_date, last_shipment_date, lc_date, lc_value, item_category_id, supplier_id, importer_id, ref_closing_status 
	FROM com_btb_lc_master_details 
	WHERE btb_system_id like '%".$system_number."' and lc_number like '%".$lc_no_cond."' and importer_id = '".$company."' and supplier_id like '".$supplier."' $category_entry_form_cond $date $btb_file_in and status_active=1 and is_deleted=0 
	order by id";
	
	// echo $sql ;
	
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$exportPiSuppArr = array();
	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	unset($exportPiSupp);
	
	//$arr=array(2=>$supplier_lib);
	//echo  create_list_view("list_view", "Year,System Id,Supplier,L/C Number,L/C Date,L/C Value,Application Date,Last Ship Date", "60,70,150,150,100,120,100,100","950","240",0, $sql , "js_set_value", "id", "", 1, "0,0,supplier_id,0,0,0,0,0", $arr , "year,btb_prefix_number,supplier_id,lc_number,lc_date,lc_value,application_date,last_shipment_date", "",'','0,0,0,0,3,2,3,3') ;
	?>
	<table width="990" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead> 
            <th width="40">SL</th>
            <th width="110">Item Category</th>
            <th width="55">Year</th>
            <th width="65">System Id</th>
            <th width="150">Supplier</th>
            <th width="150">L/C Number</th>
            <th width="80">L/C Date</th>
            <th width="100">L/C Value</th>
            <th width="100">Application Date</th>
            <th>Last Ship Date</th>
        </thead>
    </table>
     <div style="width:990px; overflow-y:scroll; max-height:280px">  
     	<table width="970" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view"> 
		<?
			$data_array=sql_select($sql); $i = 1; 
            foreach($data_array as $row)
            { 
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				$supplier='';
				if($row[csf('item_category_id')]==110)
				{
					$supplier=$comp[$row[csf('supplier_id')]];
				}
				else
				{
					$supplier=$supplier_lib[$row[csf('supplier_id')]];
				}

				if($exportPiSuppArr[$row[csf('id')]] == 1){
					$supplier=$comp[$row[csf('supplier_id')]];
				}
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]; ?>', '<? echo $row[csf('ref_closing_status')]; ?>');">
                	<td width="40"><? echo $i; ?></td>
					<td width="110"><? echo $item_category_mix[$row[csf('item_category_id')]]; ?></td>
					<td width="55" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="65"><? echo $row[csf('btb_prefix_number')]; ?></td>
					<td width="150"><p><? echo $supplier; ?></p></td>
                    <td width="150"><p><? echo $row[csf('lc_number')]; ?></p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row[csf('lc_date')]); ?></p></td>
                    <td width="100" align="right"><? echo number_format($row[csf('lc_value')],2); ?>&nbsp;</td>
					<td width="100" align="center"><? echo change_date_format($row[csf('application_date')]); ?>&nbsp;</td>
                    <td align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
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

if($action=='populate_data_from_btb_lc')
{
	$data_array=sql_select("SELECT id, lc_number, supplier_id, importer_id, garments_qty, uom_id, lc_date, application_date, last_shipment_date, lc_expiry_date, item_basis_id, lc_value, currency_id, issuing_bank_id, item_category_id, tenor, tolerance, inco_term_id, inco_term_place, delivery_mode_id, port_of_loading, port_of_discharge, remarks, partial_shipment, pi_id, pi_value, payterm_id, ud_no, ud_date, pi_entry_form, lc_category from com_btb_lc_master_details where id='$data'");

	$exportPiSupp = sql_select("SELECT c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id and a.id='$data'");
	$exportPiSuppArr = array();
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}



	foreach ($data_array as $row)
	{  
		$file_no='';
		
		$nameArray=sql_select("select a.internal_file_no from com_export_lc a, com_btb_export_lc_attachment b where a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id='$data' and b.status_active=1 and b.is_deleted=0",1);
		if(count($nameArray)>0)
		{
			$file_no=$nameArray[0][csf("internal_file_no")];
		}
		else
		{
			$nameArray=sql_select("select a.internal_file_no from com_sales_contract a, com_btb_export_lc_attachment b where a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id='$data' and b.status_active=1 and b.is_deleted=0",1);
			$file_no=$nameArray[0][csf("internal_file_no")];
		}
		
		if($row[csf("pi_id")]!="")
		{
			if($db_type==0)
			{
				$pi_no=return_field_value("group_concat(pi_number)","com_pi_master_details","id in(".$row[csf("pi_id")].") and status_active=1 and is_deleted=0");
			}
			else
			{
				//$pi_no=return_field_value("LISTAGG(cast(pi_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as pi_number","com_pi_master_details","id in(".$row[csf("pi_id")].") and status_active=1 and is_deleted=0","pi_number");
				$pi_no = return_field_value("rtrim(xmlagg(xmlelement(e,pi_number,',').extract('//text()') order by pi_number).GetClobVal(),',') AS pi_number ", "com_pi_master_details", " id in(".$row[csf("pi_id")].") and status_active=1 and is_deleted=0", "pi_number");
            	$pi_no = $pi_no->load();
			}
		}
		else
		{
			$pi_no="";
		}
		
 		echo "document.getElementById('txt_system_id').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_internal_file_no').value		= '".$file_no."';\n";
		echo "document.getElementById('txt_btb_lc_no').value 			= '".$row[csf("lc_number")]."';\n";
		echo "document.getElementById('cbo_importer_id').value 			= '".$row[csf("importer_id")]."';\n";
		echo "document.getElementById('cbo_supplier_id').value			= '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_lc_value').value 			= '".$row[csf("lc_value")]."';\n";
		echo "document.getElementById('cbo_currency_name').value 		= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_item_category_id').value 	= '".$row[csf("item_category_id")]."';\n";
		echo "document.getElementById('txt_pi_entry_form').value 		= '".$row[csf("pi_entry_form")]."';\n";		
		echo "document.getElementById('cbo_issuing_bank').value 		= '".$row[csf("issuing_bank_id")]."';\n";
		echo "document.getElementById('txt_lc_date').value 				= '".change_date_format($row[csf("lc_date")])."';\n";
		echo "document.getElementById('txt_last_shipment_date').value 	= '".change_date_format($row[csf("last_shipment_date")])."';\n";
		echo "document.getElementById('txt_expiry_date').value 			= '".change_date_format($row[csf("lc_expiry_date")])."';\n";
		echo "document.getElementById('txt_tolerance').value 			= '".$row[csf("tolerance")]."';\n";
		echo "document.getElementById('cbo_delevery_mode').value 		= '".$row[csf("delivery_mode_id")]."';\n";
		echo "document.getElementById('cbo_pay_term').value 			= '".$row[csf("payterm_id")]."';\n";
		echo "document.getElementById('txt_tenor').value 				= '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('cbo_lc_basis_id').value 			= '".$row[csf("item_basis_id")]."';\n";
		echo "document.getElementById('txt_gmt_qnty').value 			= '".$row[csf("garments_qty")]."';\n";

		echo "document.getElementById('txt_ud_no').value 			= '".$row[csf("ud_no")]."';\n";
		echo "document.getElementById('txt_ud_date').value 			= '".$row[csf("ud_date")]."';\n";
		
		echo "active_inactive(".$row[csf("item_basis_id")].");\n";
		
		echo "document.getElementById('txt_application_date').value 	= '".change_date_format($row[csf("application_date")])."';\n";
		echo "document.getElementById('txt_port_of_loading').value 		= '".$row[csf("port_of_loading")]."';\n";
		echo "document.getElementById('txt_port_of_discharge').value 	= '".$row[csf("port_of_discharge")]."';\n";
		echo "document.getElementById('txt_remarks').value 				= '".$row[csf("remarks")]."';\n";
		
		echo "document.getElementById('txt_amendment_no').value 			= '';\n";
		echo "document.getElementById('update_id').value 					= '';\n";
		echo "document.getElementById('txt_amendment_date').value 			= '';\n";
		echo "document.getElementById('txt_amendment_value').value 			= '';\n";
		echo "document.getElementById('hide_amendment_value').value 		= '';\n";
		echo "document.getElementById('cbo_value_change_by').value 			= '0';\n";
		echo "document.getElementById('hide_value_change_by').value 		= '';\n";
		echo "document.getElementById('txt_pi').value 						= '".$pi_no."';\n";
		echo "document.getElementById('txt_hidden_pi_id').value 			= '".$row[csf("pi_id")]."';\n";
		echo "document.getElementById('txt_pi_value').value 				= '".$row[csf("pi_value")]."';\n";
		
		/*echo "document.getElementById('txt_last_shipment_date_amnd').value	= '';\n";
		echo "document.getElementById('txt_expiry_date_amend').value 		= '';\n";
		echo "document.getElementById('cbo_delevery_mode_amnd').value 		= '0';\n";
		echo "document.getElementById('cbo_inco_term').value 				= '0';\n";  
		echo "document.getElementById('txt_inco_term_place').value 			= '';\n";
		echo "document.getElementById('cbo_partial_ship_id').value 			= '';\n";  
		echo "document.getElementById('txt_port_of_loading_amnd').value 	= '';\n";
		echo "document.getElementById('txt_port_of_discharge_amnd').value 	= '';\n";
		echo "document.getElementById('cbo_pay_term_amnd').value 			= '0';\n";
		echo "document.getElementById('txt_tenor_amnd').value 				= '';\n";
		echo "document.getElementById('txt_remarks_amnd').value 			= '';\n";*/
		
		echo "document.getElementById('txt_last_shipment_date_amnd').value	= '".change_date_format($row[csf("last_shipment_date")])."';\n";
		echo "document.getElementById('txt_expiry_date_amend').value 		= '".change_date_format($row[csf("lc_expiry_date")])."';\n";
		echo "document.getElementById('cbo_delevery_mode_amnd').value 		= '".$row[csf("delivery_mode_id")]."';\n";
		echo "document.getElementById('cbo_inco_term').value 				= '".$row[csf("inco_term_id")]."';\n";  
		echo "document.getElementById('txt_inco_term_place').value 			= '".$row[csf("inco_term_place")]."';\n";
		echo "document.getElementById('cbo_partial_ship_id').value 			= '".$row[csf("partial_shipment")]."';\n";  
		echo "document.getElementById('txt_port_of_loading_amnd').value 	= '".$row[csf("port_of_loading")]."';\n";
		echo "document.getElementById('txt_port_of_discharge_amnd').value 	= '".$row[csf("port_of_discharge")]."';\n";
		echo "document.getElementById('cbo_pay_term_amnd').value 			= '".$row[csf("payterm_id")]."';\n";
		//echo "document.getElementById('txt_ud_no_amnd').value 			= '".$row[csf("ud_no")]."';\n";
		//echo "document.getElementById('txt_ud_date_amnd').value 			= '".$row[csf("ud_date")]."';\n";
		echo "document.getElementById('txt_tenor_amnd').value 				= '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('txt_remarks_amnd').value 			= '".$row[csf("remarks")]."';\n";
	
		if($exportPiSuppArr[$row[csf("id")]]==1){
			echo "load_drop_down('requires/btb_margin_lc_amendment_controller',".$row[csf("importer_id")]."+'_'+".$row[csf("lc_category")]."+'_'+".$row[csf("supplier_id")]."+'_'+".$exportPiSuppArr[$row[csf("id")]].", 'load_supplier_dropdown', 'supplier_td' );\n";
		}
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_amendment_save',1);\n";
 		
		exit();
	}
}
// PI Popup
if ($action=="pi_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

		<script>
		
		var btb_id='<? echo $btb_id; ?>';
		var payTerm="";
		var tenor="";
		var selected_id = new Array, selected_name = new Array();
		var supplier_id_arr_chk = new Array; var entry_form_arr = new Array;
			
			function check_all_data()
			{
				var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
				tbl_row_count = tbl_row_count - 1;

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
				var old=document.getElementById('txt_pi_row_id').value;
				if(old!="")
				{   
					old=old.split(",");
					for(var i=0; i<old.length; i++)
					{  
						js_set_value( old[i] ) 
					}
				}
			}

			function js_set_value( str ) 
			{
				if(btb_id!="")
				{
					var data=$('#txt_individual_id' + str).val()+"**"+btb_id;
					if(document.getElementById('search' + str).style.backgroundColor=='yellow')
					{
						var pi_no=$('#search' + str).find("td:eq(1)").text();
						var response = return_global_ajax_value( data, 'check_used_or_not', '', 'btb_margin_lc_amendment_controller');
						response=response.split("**");
						if(response[0]==1)
						{
							alert("Bellow Invoice Found Against PI- "+pi_no+". So You can't Detach it.\n Invoice No: "+response[1]);
							return false;
						}
					}
				}
				
				var refClosingStatus=$('#refClosingStatus_' + str).val();
				if(refClosingStatus==1)
				{
					alert("This PI Already Closed");return;
				}
				var supplier_id = $('#supplierChk_' + str).val();
				if(supplier_id_arr_chk.length==0)
				{
					supplier_id_arr_chk.push( supplier_id );
				}
				else if( jQuery.inArray( supplier_id, supplier_id_arr_chk )==-1 &&  supplier_id_arr_chk.length>0)
				{
					alert("Supplier Mixed is Not Allowed");
					return;
				}
				
				var entry_form = $('#entryForm_' + str).val();
				var item_category_id = $('#itemCategory_' + str).val();

				if(payTerm=="") payTerm = $('#payTerm_' + str).val();
				if(tenor=="") tenor = $('#tenor_' + str).val();

				/*if(entry_form_arr.length==0)
				{
					entry_form_arr.push( entry_form );
				}
				else if( jQuery.inArray( entry_form, entry_form_arr )==-1 &&  entry_form_arr.length>0)
				{
					alert("Entry Form Mixed is Not Allowed");
					return;
				}*/
				
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual' + str).val() );
					
				}
				else
				{
					for( var i = 0; i < selected_id.length; i++ )
					{
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
				var id =''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );
			}
			
			function reset_hide_field(type)
			{
				$('#txt_selected_id').val( '' );
				$('#txt_selected').val( '' );
				if(type==1)
				{
					$('#search_div').html( '' );
				}
			}
			$('document').ready(function(){
				var importer_id="<?= $cbo_importer_id; ?>";
				var item_category_id="<?= $item_category_id; ?>";
				var cbo_supplier_id="<?= $cbo_supplier_id; ?>";
				load_drop_down( 'btb_margin_lc_amendment_controller',importer_id+'_'+item_category_id+'_'+cbo_supplier_id, 'load_supplier_dropdown', 'supplier_td' );
				});
			
			
		</script>

	</head>

	<body>
	<div align="center" style="width:950px;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<fieldset style="width:990px;margin-left:10px">
				<table style="margin-top:10px" width="980" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>  
						<th width="120">Importer</th> 
						<th width="120">Supplier</th>              	 
						<th width="100">PI Number</th>
						<th width="200">Date Range</th>  
						<th><input type="reset" name="button" class="formbutton" value="Reset" onClick="reset_hide_field(1)" style="width:100px;"></th>
						<input type="hidden" name="txt_item_category" id="txt_item_category" class="text_boxes" style="width:70px" value="<? echo $item_category_id; ?>">  
						<input type="hidden" name="txt_supplier_id" id="txt_supplier_id" class="text_boxes" style="width:70px" value="<? echo $cbo_supplier_id; ?>">  
						<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
						<input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />  
					</thead>
					<tr>
						<td align="center">
							<? 
								echo create_drop_down( "cbo_company_id", 165,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '----Select----',$cbo_importer_id,"load_drop_down( 'btb_margin_lc_amendment_controller',this.value+'_'+document.getElementById('txt_item_category').value+'_'+document.getElementById('txt_supplier_id').value, 'load_supplier_dropdown', 'supplier_td' );",0); 
							?>  
						
						</td>
						<td align="center" id="supplier_td">
						<? echo create_drop_down( "cbo_supplier_id", 165,$blank_array,'', 1, "",$cbo_supplier_id,0,0); ?>       
						
						</td>
						<td align="center">
						<input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:120px">
						</td>
						<td align="center">
						<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
						<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td> 
						<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_item_category').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+'<? echo $txt_hidden_pi_id; ?>'+'_'+'<? echo $pi_entry_form; ?>'+'_'+'<? echo $cbo_importer_id; ?>', 'create_pi_search_list_view', 'search_div', 'btb_margin_lc_amendment_controller', 'setFilterGrid(\'list_view\',-1)');reset_hide_field(0);set_all();" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</table>
				<div style="margin-top:10px" id="search_div"></div>
			</fieldset>   
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_pi_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!="") $pi_number="%".$data[0]."%"; else $pi_number = '%%';
	if($db_type==0)
	{
		if ($data[1]!="" && $data[2]!="") $pi_date = "and pi_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $pi_date ="";
	}
	else if($db_type==2)
	{
		if($data[1]!="" && $data[2]!="") $pi_date ="and pi_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'";
		else $pi_date="";
	}
	$item_category_id =$data[3];
	if($data[4]!=0) $importer_id =$data[4]; else $importer_id='%%';
	if($data[5]!=0) $supplier_id =$data[5]; else $supplier_id='%%';
	$lc_impoter_id =$data[8];
	if(trim(str_replace("'","",$data[8])) != trim(str_replace("'","",$data[4])))
	{
		echo "PI And BTB LC Impoter Not Match";die;
	}
	
	/*if($supplier_id==0)
	{
		echo 'Select Supplier'; 
		echo '<input type="hidden" name="txt_pi_row_id" id="txt_pi_row_id" value=""/>';
		die;
	}
	if($all_pi_id=="")
	{
		$sql= "select id, pi_number, pi_date, item_category_id, importer_id, supplier_id, last_shipment_date, hs_code, pi_basis_id, a.import_pi 
		from com_pi_master_details a 
		where supplier_id like '".$supplier_id."' and importer_id like '".$importer_id."' and entry_form = $pi_entry_form and pi_number like '$pi_number' $pi_date $import_pi_cond and status_active = 1 and is_deleted =0 and id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0) 
		group by id, pi_number, pi_date, item_category_id, importer_id, supplier_id, last_shipment_date, hs_code, pi_basis_id, a.import_pi order by pi_number";  
	}
	else
	{
		$sql= "select id, pi_number, pi_date, item_category_id, importer_id, supplier_id, last_shipment_date, hs_code, pi_basis_id, a.import_pi 
		from com_pi_master_details a 
		where supplier_id like '".$supplier_id."' and importer_id like '".$importer_id."' and entry_form = $pi_entry_form and pi_number like '$pi_number' $pi_date $import_pi_cond and status_active = 1 and is_deleted =0 and id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0) or id in($all_pi_id) group by id, pi_number, pi_date, item_category_id, importer_id, supplier_id, last_shipment_date, hs_code, pi_basis_id, a.import_pi order by pi_number";   
	}
	*/
	
	$all_pi_id=$data[6];
	$hidden_pi_id=explode(",",$all_pi_id);
	$pi_entry_form =$data[7];
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$import_pi_cond='';
	if($item_category_id==110) 
	{
		$item_category_id=10; 
		$import_pi_cond=" and a.import_pi=1 and a.within_group=1";
	} 
	

	$item_category_cond="";
	if($item_category_id)
	{
		$item_category_cond = " and b.item_category_id = $item_category_id ";
	}
	
	$nameArray = sql_select("SELECT pi_source_btb_lc FROM variable_settings_commercial where company_name=$importer_id and variable_list=25 and is_deleted = 0 AND status_active = 1");
	if($all_pi_id=="")
	{
		if ($nameArray[0][csf("pi_source_btb_lc")] == 2)
		{
			$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.internal_file_no 
			from com_pi_master_details a, com_pi_item_details b, commercial_office_note_dtls c, commercial_office_note_mst d
			where a.id=b.pi_id and b.pi_id=c.pi_id and d.id=c.mst_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' $pi_date $import_pi_cond $approved_cond and a.status_active=1 and a.is_deleted=0 and d.is_approved=1 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0)
			group by a.id, a.pi_number, a.priority_id, a.pi_date, a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.internal_file_no  order by a.pi_number";
		}
		else
		{
			$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.internal_file_no 
			from com_pi_master_details a, com_pi_item_details b
			where a.id = b.pi_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' $pi_date $import_pi_cond $approved_cond and a.status_active = 1 and a.is_deleted =0 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0)
			group by a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.internal_file_no  order by a.pi_number";
		}
	}
	else
	{
		if ($nameArray[0][csf("pi_source_btb_lc")] == 2)
		{
			$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.internal_file_no 
			from com_pi_master_details a, com_pi_item_details b, commercial_office_note_dtls c, commercial_office_note_mst d
			where a.id=b.pi_id and d.id=c.mst_id and b.pi_id=c.pi_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' $pi_date $import_pi_cond $approved_cond and a.status_active=1 and a.is_deleted=0 and d.is_approved=1 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0  and pi_id not in($all_pi_id) )
			group by a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved,a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.internal_file_no  order by a.pi_number";
		}
		else
		{
			$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.internal_file_no 
			from com_pi_master_details a, com_pi_item_details b
			where a.id = b.pi_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' $pi_date $import_pi_cond $approved_cond and a.status_active = 1 and a.is_deleted =0 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0 and pi_id not in($all_pi_id) )
			group by a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved,a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.internal_file_no  order by a.pi_number";
		}
	}
	
	
	// echo $sql; die;
	?>	
		<div style="width:980px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
                <thead class="table_header" style="width:980px;">
                    <th width="40">SL No</th>
                    <th width="110">PI No</th>
					<th width="90">PI Sys. ID</th>
                    <th width="110">File No</th>
                    <th width="90">PI Date</th>
                    <th width="100">Item Category</th>
                    <th width="120">Importer</th>
                    <th width="120">Supplier</th>
                    <th width="80">HS Code</th>
                    <th width="100">PI Basis</th>
                </thead>
                <tbody class="table_body" style="width:980px; max-height:250px;" id="list_view">
                <? 
                 $i=1; $pi_row_id="";
                 $nameArray=sql_select( $sql );
                 foreach ($nameArray as $selectResult)
                 {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	

					if(in_array($selectResult[csf('id')],$hidden_pi_id)) 
					{
						if($pi_row_id=="") $pi_row_id=$i; else $pi_row_id.=",".$i;
					}
					
					if($selectResult[csf('import_pi')]==1)
					{
						$supplier=$comp[$selectResult[csf('supplier_id')]];
						$category=$export_item_category[$selectResult[csf('item_category_id')]];
					}
					else
					{
						$supplier=$supplier_arr[$selectResult[csf('supplier_id')]];
						$category=$item_category[$selectResult[csf('item_category_id')]];
					}
           			?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                        <td width="40" align="center"><? echo "$i"; ?>
                         <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>"/>	
                         <input type="hidden" name="txt_individual" id="txt_individual<? echo $i; ?>" value="<? echo $selectResult[csf('pi_number')]; ?>"/>
                         <input type="hidden" name="approvalStatus[]" id="approvalStatus_<? echo $i; ?>" value="<? echo $selectResult[csf('approved')]; ?>"/>
                         <input type="hidden" name="supplierChk[]" id="supplierChk_<? echo $i; ?>" value="<? echo $selectResult[csf('import_pi')].'_'.$selectResult[csf('supplier_id')]; ?>"/>
                         <input type="hidden" name="entryForm[]" id="entryForm_<? echo $i; ?>" value="<? echo $selectResult[csf('entry_form')];?>"/>
                         <input type="hidden" name="itemCategory[]" id="itemCategory_<? echo $i; ?>" value="<? echo $selectResult[csf('item_category_id')];?>"/>
                         <input type="hidden" name="refClosingStatus[]" id="refClosingStatus_<? echo $i; ?>" value="<? echo $selectResult[csf('ref_closing_status')];?>"/>
                         <input type="hidden" name="payTerm[]" id="payTerm_<? echo $i; ?>" value="<? echo $selectResult[csf('pay_term')];?>"/>
                         <input type="hidden" name="tenor[]" id="tenor_<? echo $i; ?>" value="<? echo $selectResult[csf('tenor')];?>"/>
                        </td>	
                        <td width="110"><p><? echo $selectResult[csf('pi_number')];?></p></td>
                        <td width="90"><p><? echo $selectResult[csf('id')];?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('internal_file_no')];?></p></td>
                        <td width="90"><? echo change_date_format($selectResult[csf('pi_date')]);?></td> 
                        <td width="100"><? echo $category;//$item_category[$selectResult[csf('item_category_id')]];  ?></td>
                        <td width="120"><? echo $comp[$selectResult[csf('importer_id')]];  ?></td>
                        <td width="120"><? echo $supplier;//$supplier[$selectResult[csf('supplier_id')]];  ?></td>
                        <td width="80"><p><? echo $selectResult[csf('hs_code')];  ?></p></td>
                        <td width="100"><? echo $pi_basis[$selectResult[csf('pi_basis_id')]];  ?></td>
                    </tr>
                <?
                	$i++;
                 }
                 ?>
                 <input type="hidden" name="txt_pi_row_id" id="txt_pi_row_id" value="<? echo $pi_row_id; ?>"/>	
            </tbody> 
        </table>
        <table width="940" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	</div>
<?	 
}

if($action=="check_used_or_not")
{
	$data=explode("**",$data);
	$pi_id=$data[0];
	$btb_id=$data[1];
	
	$sql="select a.invoice_no from com_import_invoice_mst a, com_import_invoice_dtls b where a.id=b.import_invoice_id and b.btb_lc_id=$btb_id and b.pi_id=$pi_id and b.status_active=1 and b.is_deleted=0 and b.CURRENT_ACCEPTANCE_VALUE>0 group by a.id, a.invoice_no";
	$data_array=sql_select($sql);
	$invoice_no='';
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($invoice_no=="") $invoice_no=$row[csf('invoice_no')]; else $invoice_no.=", ".$row[csf('invoice_no')];
		}
		echo "1**".$invoice_no;
	}
	else
	{
		echo "0**";	
	}
	exit();
}

if ($action=="set_value_pi_select")
{
	$data=explode("**",$data);
	$pi_id=$data[0];
	$lc_value=$data[1];
	$currency_id=$data[2];
	
	$inc_dec=0; $amnd_value=0;
	
	$pi_value = return_field_value("sum(net_total_amount)","com_pi_master_details","id in($pi_id) and status_active=1 and is_deleted=0");
	
	if($currency_id==1)
		$pi_value=number_format($pi_value,$dec_place[4],'.','');
	else
		$pi_value=number_format($pi_value,$dec_place[5],'.','');
	
	if($pi_value>$lc_value)
	{
		$inc_dec=1;
		$amnd_value=$pi_value-$lc_value;
	}
	else if($pi_value<$lc_value)
	{
		$inc_dec=2;
		$amnd_value=$lc_value-$pi_value;
	}
	else
	{
		$inc_dec=0;
		$amnd_value=0;
	}
	
	if($currency_id==1)
		$amnd_value=number_format($amnd_value,$dec_place[4],'.','');
	else
		$amnd_value=number_format($amnd_value,$dec_place[5],'.','');
	
	echo "document.getElementById('txt_pi_value').value					= '".$pi_value."';\n";
	echo "document.getElementById('txt_amendment_value').value 			= '".$amnd_value."';\n";
	echo "document.getElementById('hide_amendment_value').value 		= '".$amnd_value."';\n";
	echo "document.getElementById('cbo_value_change_by').value 			= '".$inc_dec."';\n";
	echo "document.getElementById('hide_value_change_by').value 		= '".$inc_dec."';\n";
	
	exit();
}


//amendment popup
if($action=="amendment_popup")
{
	echo load_html_head_contents("BTB LC Amendment Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		function js_set_value(id)
		{
			$('#hidden_amendment_no').val(id);
			parent.emailwindow.hide();
		}	
    </script>
    <div align="center" style="width:100%; margin-top:10px">
        <input type="hidden" id="hidden_amendment_no" value="" />
        <?
        $sql = "SELECT id, amendment_no, amendment_date, btb_lc_no, btb_lc_value FROM com_btb_lc_amendment WHERE btb_id='$btb_lc_id' and amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0 order by id";
                    
        echo  create_list_view("list_view", "Amendment No,Amendment Date,BTB LC No, LC Value", "110,100,150,130","600","250",0, $sql , "js_set_value", "id", "", 1, 0, 0, "amendment_no,amendment_date,btb_lc_no,btb_lc_value", "",'setFilterGrid(\'list_view\',-1)','0,3,0,2');
        ?>
    </div>
    <?
	exit();
}


if($action=="get_amendment_data")
{
	$data_array = sql_select("SELECT btb_id, amendment_no, amendment_date, amendment_value, garments_qty, uom_id, value_change_by, last_shipment_date, expiry_date, delivery_mode, inco_term, inco_term_place, partial_shipment, port_of_loading, port_of_discharge, pay_term, addendum_no, addendum_date, tenor, pi_id, pi_value, remarks, application_amendment_date, qnty_change_by FROM com_btb_lc_amendment WHERE id='$data' and status_active=1 and is_deleted=0");
						
	foreach ($data_array as $row)
	{ 	
		 $sql=sql_select("SELECT last_shipment_date, lc_expiry_date, delivery_mode_id, inco_term_id, inco_term_place, partial_shipment, port_of_loading, port_of_discharge, payterm_id, remarks, tenor, pi_id, pi_value, currency_id FROM com_btb_lc_master_details WHERE id='".$row[csf("btb_id")]."'");
		 
		/* $pi_currency_id=return_field_value("concat_ws('**',pi_id,currency_id)","com_btb_lc_master_details","id=$row[btb_id]");
		 $pi_currency_id=explode("**",$pi_currency_id);*/
		 
		 $pi_id=$sql[0][csf('pi_id')]; 
		 $currency_id=$sql[0][csf('currency_id')];
		 
 		 if($pi_id!="")
		 {
			if($db_type==0)
			{
				$pi_no=return_field_value("group_concat(pi_number)","com_pi_master_details","id in($pi_id) and status_active=1 and is_deleted=0");
			}
			else
			{
				//$pi_no=return_field_value("LISTAGG(cast(pi_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as pi_number","com_pi_master_details","id in($pi_id) and status_active=1 and is_deleted=0","pi_number");
				$pi_no = return_field_value("rtrim(xmlagg(xmlelement(e,pi_number,',').extract('//text()') order by pi_number).GetClobVal(),',') AS pi_number ", "com_pi_master_details", " id in($pi_id) and status_active=1 and is_deleted=0", "pi_number");
            	$pi_no = $pi_no->load();
			}
			
			$pi_value = return_field_value("sum(net_pi_amount)","com_pi_item_details","pi_id in($pi_id) and status_active=1 and is_deleted=0");
			
			if($currency_id==1)
				$pi_value=number_format($pi_value,$dec_place[4],'.','');
			else
				$pi_value=number_format($pi_value,$dec_place[5],'.','');
		 }
		 else
		 {
			$pi_no="";
			$pi_value="";
		 }

 		 echo "document.getElementById('txt_amendment_no').value 			= '".$row[csf("amendment_no")]."';\n";
 		 echo "document.getElementById('txt_amendment_date').value 			= '".change_date_format($row[csf("amendment_date")])."';\n";
		 echo "document.getElementById('txt_amendment_value').value 		= '".$row[csf("amendment_value")]."';\n";
		 echo "document.getElementById('hide_amendment_value').value 		= '".$row[csf("amendment_value")]."';\n";
		 echo "document.getElementById('cbo_value_change_by').value 		= '".$row[csf("value_change_by")]."';\n";
		 echo "document.getElementById('hide_value_change_by').value 		= '".$row[csf("value_change_by")]."';\n";
		 echo "document.getElementById('txt_gmt_qnty_amnd').value 		    = '".$row[csf("garments_qty")]."';\n";
		 echo "document.getElementById('cbo_garments_qnty_change_by').value = '".$row[csf("qnty_change_by")]."';\n";
		 echo "document.getElementById('hdn_gmt_qnty_amnd').value 		    = '".$row[csf("garments_qty")]."';\n";
		 echo "document.getElementById('hdn_qnty_change_by').value 			= '".$row[csf("qnty_change_by")]."';\n";

		 echo "document.getElementById('txt_addendum_no').value 		= '".$row[csf("addendum_no")]."';\n";
		 echo "document.getElementById('txt_addendum_date').value 		= '".change_date_format($row[csf("addendum_date")])."';\n";
		 echo "document.getElementById('txt_application_date_amnd').value 		= '".change_date_format($row[csf("application_amendment_date")])."';\n";


		 echo "document.getElementById('txt_last_shipment_date_amnd').value	= '".change_date_format($sql[0][csf("last_shipment_date")])."';\n";
		 echo "document.getElementById('txt_expiry_date_amend').value 		= '".change_date_format($sql[0][csf("lc_expiry_date")])."';\n";
		 echo "document.getElementById('cbo_delevery_mode_amnd').value 		= '".$sql[0][csf("delivery_mode_id")]."';\n";
		 echo "document.getElementById('cbo_inco_term').value 				= '".$sql[0][csf("inco_term_id")]."';\n";  
		 echo "document.getElementById('txt_inco_term_place').value 		= '".$sql[0][csf("inco_term_place")]."';\n";
		 echo "document.getElementById('cbo_partial_ship_id').value 		= '".$sql[0][csf("partial_shipment")]."';\n";  
		 echo "document.getElementById('txt_port_of_loading_amnd').value 	= '".$sql[0][csf("port_of_loading")]."';\n";
		 echo "document.getElementById('txt_port_of_discharge_amnd').value 	= '".$sql[0][csf("port_of_discharge")]."';\n";
		 echo "document.getElementById('cbo_pay_term_amnd').value 			= '".$sql[0][csf("payterm_id")]."';\n";
		 echo "document.getElementById('txt_tenor_amnd').value 				= '".$sql[0][csf("tenor")]."';\n";
		 echo "document.getElementById('txt_pi').value 						= '".$pi_no."';\n";
		 echo "document.getElementById('txt_hidden_pi_id').value 			= '".$pi_id."';\n";
		 echo "document.getElementById('txt_pi_value').value 				= '".$pi_value."';\n";
		 echo "document.getElementById('txt_remarks_amnd').value 			= '".$sql[0][csf("remarks")]."';\n";
		 echo "document.getElementById('update_id').value 					= '".$data."';\n";
 		 echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_amendment_save',1);\n";
 	}
	exit();
}


if ($action=="save_update_delete_amendment")
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
		if (is_duplicate_field( "amendment_no", "com_btb_lc_amendment", "amendment_no=$txt_amendment_no and status_active=1 and btb_id=$txt_system_id")==1)
		{
			echo "11**0"; disconnect($con);
			die;			
		}
		$user_id=''; $entry_date='';
		$data_array=sql_select("select lc_number, lc_value, last_shipment_date, lc_expiry_date, delivery_mode_id, inco_term_id, inco_term_place, partial_shipment, port_of_loading, port_of_discharge, payterm_id, tolerance, remarks, addendum_no,addendum_date, tenor, garments_qty, uom_id, ud_no, ud_date, pi_id, pi_value, currency_id, updated_by, inserted_by, update_date, insert_date,ref_closing_status from com_btb_lc_master_details where id=$txt_system_id");
		
		$lc_value = $data_array[0][csf('lc_value')];
		$btb_lc_no = $data_array[0][csf('lc_number')];
		$currency_id = $data_array[0][csf('currency_id')];
		$ref_closing_status = $data_array[0][csf('ref_closing_status')];
		$garments_qty = $data_array[0][csf('garments_qty')];
		
		if($data_array[0][csf('updated_by')]==0)
		{
			$user_id=$data_array[0][csf('inserted_by')];
			$entry_date=$data_array[0][csf('insert_date')];
		}
		else 
		{
			$user_id=$data_array[0][csf('updated_by')];
			$entry_date=$data_array[0][csf('update_date')];
		}
		
		if($currency_id==1)
		{
			$txt_pi_value=number_format(str_replace("'", '',$txt_pi_value),$dec_place[4],'.','');
			$txt_amendment_value=number_format(str_replace("'", '',$txt_amendment_value),$dec_place[4],'.','');
		}
		else
		{
			$txt_pi_value=number_format(str_replace("'", '',$txt_pi_value),$dec_place[5],'.','');
			$txt_amendment_value=number_format(str_replace("'", '',$txt_amendment_value),$dec_place[5],'.','');
		}
		
		if( str_replace("'", '', $cbo_value_change_by)==1 )
		{
			$new_lc_value = $lc_value+str_replace("'", '', $txt_amendment_value);
			//echo $new_lc_value;die;
		}
		else if(str_replace("'", '', $cbo_value_change_by)==2 )
		{
			$new_lc_value = $lc_value-str_replace("'", '', $txt_amendment_value);
		}
		else
		{	
			$new_lc_value = $lc_value;	
		}
		$maximum_tolarence = 0; $minimum_tolarence = 0;
 		$maximum_tolarence = $new_lc_value+($new_lc_value*$data_array[0][csf('tolerance')])/100;
		$minimum_tolarence = $new_lc_value-($new_lc_value*$data_array[0][csf('tolerance')])/100;

		$cbo_garments_qnty_change_by=str_replace("'", '',$cbo_garments_qnty_change_by);
		$txt_gmt_qnty_amnd=str_replace("'", '',$txt_gmt_qnty_amnd);
		if($cbo_garments_qnty_change_by==1)
		{
			$gmt_qnty_amnd=$garments_qty+$txt_gmt_qnty_amnd;
		}
		else if($cbo_garments_qnty_change_by==2)
		{
			$gmt_qnty_amnd=$garments_qty-$txt_gmt_qnty_amnd;
		}
		else
		{
			$gmt_qnty_amnd=$garments_qty;
		}
 		
		$field_array_update="lc_value*max_lc_value*min_lc_value*last_shipment_date*lc_expiry_date*delivery_mode_id*payterm_id*garments_qty*uom_id*ud_no*ud_date*inco_term_id*inco_term_place*partial_shipment*port_of_loading*port_of_discharge*remarks*addendum_no*addendum_date*tenor*pi_id*pi_value*updated_by*update_date";
		
		$data_array_update=$new_lc_value."*".$maximum_tolarence."*".$minimum_tolarence."*".$txt_last_shipment_date_amnd."*".$txt_expiry_date_amend."*".$cbo_delevery_mode_amnd."*".$cbo_pay_term_amnd."*'".$gmt_qnty_amnd."'*1*".$txt_ud_no_amnd."*".$txt_ud_date_amnd."*".$cbo_inco_term."*".$txt_inco_term_place."*".$cbo_partial_ship_id."*".$txt_port_of_loading_amnd."*".$txt_port_of_discharge_amnd."*".$txt_remarks_amnd."*".$txt_addendum_no."*".$txt_addendum_date."*".$txt_tenor_amnd."*".$txt_hidden_pi_id."*'".$txt_pi_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("com_btb_lc_master_details",$field_array_update,$data_array_update,"id","".$txt_system_id."",1);
		if($rID) $flag=1; else $flag=0; */
		
		if (is_duplicate_field( "amendment_no", "com_btb_lc_amendment", "amendment_no=0 and btb_id=$txt_system_id")==0)
		{
			$id=return_next_id( "id", "com_btb_lc_amendment", 1 );
			$field_array="id, amendment_no, amendment_date, btb_id, btb_lc_no, btb_lc_value, amendment_value, value_change_by, last_shipment_date, expiry_date, delivery_mode, pay_term, garments_qty, uom_id, ud_no, ud_date, inco_term, inco_term_place, partial_shipment, port_of_loading, port_of_discharge, remarks, addendum_no, addendum_date, tenor, pi_id, pi_value, is_original, inserted_by, insert_date, application_amendment_date";
			
			$amnd_date="";
			$data_array_amnd="(".$id.",0,'".$amnd_date."',".$txt_system_id.",'".$btb_lc_no."','".$lc_value."',0,0,'".$data_array[0][csf(last_shipment_date)]."','".$data_array[0][csf(lc_expiry_date)]."','".$data_array[0][csf(delivery_mode_id)]."','".$data_array[0][csf(payterm_id)]."','".$data_array[0][csf(garments_qty)]."','".$data_array[0][csf(uom_id)]."','".$data_array[0][csf(ud_no)]."','".$data_array[0][csf(ud_date)]."','".$data_array[0][csf(inco_term_id)]."','".$data_array[0][csf(inco_term_place)]."','".$data_array[0][csf(partial_shipment)]."','".$data_array[0][csf(port_of_loading)]."','".$data_array[0][csf(port_of_discharge)]."','".$data_array[0][csf(remarks)]."','".$data_array[0][csf(addendum_no)]."','".$data_array[0][csf(addendum_date)]."','".$data_array[0][csf(tenor)]."','".$data_array[0][csf(pi_id)]."','".$data_array[0][csf(pi_value)]."',1,'".$user_id."','".$entry_date."',".$txt_application_date_amnd.")";
			
			/*$rID2=sql_insert("com_btb_lc_amendment",$field_array,$data_array_amnd,0);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0; 
			}*/
			
			$id+=1;
		}
		else
		{
			$id=return_next_id( "id", "com_btb_lc_amendment", 1 );
		}

		$shipment_date=strtotime($data_array[0][csf('last_shipment_date')]);
		$shipment_date_amnd=strtotime(str_replace("'","",$txt_last_shipment_date_amnd));
		$expiry_date=strtotime($data_array[0][csf('lc_expiry_date')]);
		$expiry_date_amnd=strtotime(str_replace("'","",$txt_expiry_date_amend));

		$field_array_amnd=""; $data_array_amnd2="";
		
		$field_array_amnd="id, amendment_no, amendment_date, btb_id, btb_lc_no, btb_lc_value, amendment_value, value_change_by, application_amendment_date,garments_qty,qnty_change_by";
		$data_array_amnd2="(".$id.",".$txt_amendment_no.",".$txt_amendment_date.",".$txt_system_id.",'".$btb_lc_no."','".$new_lc_value."','".$txt_amendment_value."',".$cbo_value_change_by.",".$txt_application_date_amnd.",'".$txt_gmt_qnty_amnd."',".$cbo_garments_qnty_change_by;
		
		if($data_array[0][csf('pi_id')]!=str_replace("'","",$txt_hidden_pi_id))
		{
			$field_array_amnd.=",pi_id,pi_value";
			$data_array_amnd2.=",".$txt_hidden_pi_id.",'".$txt_pi_value."'";
			
			//----------Insert Data in  com_btb_lc_pi Table----------------------------------------
			/*$delete=execute_query( "delete from com_btb_lc_pi where com_btb_lc_master_details_id = $txt_system_id",0);
			if($flag==1) 
			{
				if($delete) $flag=1; else $flag=0; 
			} */
			
			if(str_replace("'","",$txt_hidden_pi_id)!="")
			{
				$data_array2="";
				$tag_pi=explode(',',str_replace("'","",$txt_hidden_pi_id));
				$id_lbtb_lc_pi=return_next_id( "id","com_btb_lc_pi", 1 );
				$field_array2="id, com_btb_lc_master_details_id, pi_id, inserted_by, insert_date";
				for($i=0; $i<count($tag_pi); $i++)
				{  
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array2.="$add_comma(".$id_lbtb_lc_pi.",".$txt_system_id.",".$tag_pi[$i].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id_lbtb_lc_pi++;
				}
			
				/*$btb_pi=sql_insert("com_btb_lc_pi",$field_array2,$data_array2,0);
				if($flag==1) 
				{
					if($btb_pi) $flag=1; else $flag=0; 
				} */
			}
		}
		
		if($shipment_date!=$shipment_date_amnd)
		{
			$field_array_amnd.=",last_shipment_date";
			$data_array_amnd2.=",".$txt_last_shipment_date_amnd;
		}
		
		if($expiry_date!=$expiry_date_amnd)
		{
			$field_array_amnd.=",expiry_date";
			$data_array_amnd2.=",".$txt_expiry_date_amend;
		}
		
		if($data_array[0][csf('delivery_mode_id')]!=str_replace("'","",$cbo_delevery_mode_amnd))
		{
			$field_array_amnd.=",delivery_mode";
			$data_array_amnd2.=",".$cbo_delevery_mode_amnd;
		}
		
		if($data_array[0][csf('payterm_id')]!=str_replace("'","",$cbo_pay_term_amnd))
		{
			$field_array_amnd.=",pay_term";
			$data_array_amnd2.=",".$cbo_pay_term_amnd;
		}
        /* if($data_array[0][csf('garments_qty')]!=str_replace("'","",$txt_gmt_qnty_amnd))
        {
            $field_array_amnd.=",garments_qty";
            $data_array_amnd2.=",".$txt_gmt_qnty_amnd;
        } */
        if($data_array[0][csf('uom_id')]!=str_replace("'","",$cbo_gmt_uom_id_amnd))
        {
            $field_array_amnd.=",uom_id";
            $data_array_amnd2.=",1";
        }

		if($data_array[0][csf('ud_no')]!=str_replace("'","",$txt_ud_no_amnd))
		{
			$field_array_amnd.=",ud_no";
			$data_array_amnd2.=",".$txt_ud_no_amnd;
		}

		if($data_array[0][csf('ud_date')]!=str_replace("'","",$txt_ud_date_amnd))
		{
			$field_array_amnd.=",ud_date";
			$data_array_amnd2.=",".$txt_ud_date_amnd;
		}
		
		if($data_array[0][csf('inco_term_id')]!=str_replace("'","",$cbo_inco_term))
		{
			$field_array_amnd.=",inco_term";
			$data_array_amnd2.=",".$cbo_inco_term;
		}
		
		if($data_array[0][csf('inco_term_place')]!=str_replace("'","",$txt_inco_term_place))
		{
			$field_array_amnd.=",inco_term_place";
			$data_array_amnd2.=",".$txt_inco_term_place;
		}
		
		if($data_array[0][csf('partial_shipment')]!=str_replace("'","",$cbo_partial_ship_id))
		{
			$field_array_amnd.=",partial_shipment";
			$data_array_amnd2.=",".$cbo_partial_ship_id;
		}
		
		if($data_array[0][csf('port_of_loading')]!=str_replace("'","",$txt_port_of_loading_amnd))
		{
			$field_array_amnd.=",port_of_loading";
			$data_array_amnd2.=",".$txt_port_of_loading_amnd;
		}
		
		if($data_array[0][csf('port_of_discharge')]!=str_replace("'","",$txt_port_of_discharge_amnd))
		{
			$field_array_amnd.=",port_of_discharge";
			$data_array_amnd2.=",".$txt_port_of_discharge_amnd;
		}
		
		if($data_array[0][csf('remarks')]!=str_replace("'","",$txt_remarks_amnd))
		{
			$field_array_amnd.=",remarks";
			$data_array_amnd2.=",".$txt_remarks_amnd;
		}

		if($data_array[0][csf('addendum_no')]!=str_replace("'","",$txt_addendum_no))
		{
			$field_array_amnd.=",addendum_no";
			$data_array_amnd2.=",".$txt_addendum_no;
		}

		if($data_array[0][csf('addendum_date')]!=str_replace("'","",$txt_addendum_date))
		{
			$field_array_amnd.=",addendum_date";
			$data_array_amnd2.=",".$txt_addendum_date;
		}
		
		if($data_array[0][csf('tenor')]!=str_replace("'","",$txt_tenor_amnd))
		{
			$field_array_amnd.=",tenor";
			$data_array_amnd2.=",".$txt_tenor_amnd;
		}
		
		$field_array_amnd.=",inserted_by, insert_date";
		$data_array_amnd2.=",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$isFirstamnd=is_duplicate_field( "amendment_no", "com_btb_lc_amendment", "amendment_no=0 and btb_id=$txt_system_id");
		
		$rID=$rID2=$btb_pi=true;
		if($ref_closing_status!=1)
		{
			$rID=sql_update("com_btb_lc_master_details",$field_array_update,$data_array_update,"id","".$txt_system_id."",1);
		}
		//echo "5**$rID";die;
		if($rID) $flag=1; else $flag=0; 
		
		if ($ref_closing_status!=1) {
			if($isFirstamnd==0)
			{
				//	echo "insert into com_btb_lc_amendment (".$field_array.") values ".$data_array_amnd;die;
				$rID2=sql_insert("com_btb_lc_amendment",$field_array,$data_array_amnd,0);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0; 
				}
			}
		}
		
		if($data_array[0][csf('pi_id')]!=str_replace("'","",$txt_hidden_pi_id))
		{
			$delete=execute_query("delete from com_btb_lc_pi where com_btb_lc_master_details_id = $txt_system_id",0);
			if($flag==1) 
			{
				if($delete) $flag=1; else $flag=0; 
			} 
			
			if(str_replace("'","",$txt_hidden_pi_id)!="")
			{ 
				$btb_pi=sql_insert("com_btb_lc_pi",$field_array2,$data_array2,0);
				if($flag==1) 
				{
					if($btb_pi) $flag=1; else $flag=0; 
				} 
			}
		}
		//echo "insert into com_btb_lc_amendment (".$field_array_amnd.") values ".$data_array_amnd2;die;
		$rID3=sql_insert("com_btb_lc_amendment",$field_array_amnd,$data_array_amnd2,1);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		
		
		//echo "5**$rID==$rID2==$btb_pi=$rID3";die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**0**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**".str_replace("'", '', $txt_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**0**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				oci_rollback($con); 
				echo "5**0**".str_replace("'", '', $txt_system_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$last_amendment_id=return_field_value("max(id)","com_btb_lc_amendment","btb_id=$txt_system_id");
		if($last_amendment_id!=str_replace("'", '', $update_id))
		{
			echo "14**1"; disconnect($con);
			die;
		}
		if (is_duplicate_field( "id", "com_btb_lc_amendment", "amendment_no=$txt_amendment_no and status_active=1 and btb_id=$txt_system_id and id<>$update_id")==1)
		{
			echo "11**1"; disconnect($con);
			die;			
		}
		$data_array=sql_select("select lc_number, lc_value, last_shipment_date, lc_expiry_date, delivery_mode_id, inco_term_id, inco_term_place, partial_shipment, port_of_loading, port_of_discharge, payterm_id, garments_qty, uom_id, tolerance, remarks, addendum_no, addendum_date, tenor, pi_id, pi_value, currency_id, ref_closing_status from com_btb_lc_master_details where id=$txt_system_id");
		$lc_value = $data_array[0][csf('lc_value')];
		$btb_lc_no = $data_array[0][csf('lc_number')];
		$currency_id = $data_array[0][csf('currency_id')];
		$ref_closing_status = $data_array[0][csf('ref_closing_status')];
		$garments_qty = $data_array[0][csf('garments_qty')];
		if($currency_id==1)
		{
			$txt_pi_value=number_format(str_replace("'", '',$txt_pi_value),$dec_place[4],'.','');
			$txt_amendment_value=number_format(str_replace("'", '',$txt_amendment_value),$dec_place[4],'.','');
		}
		else
		{
			$txt_pi_value=number_format(str_replace("'", '',$txt_pi_value),$dec_place[5],'.','');
			$txt_amendment_value=number_format(str_replace("'", '',$txt_amendment_value),$dec_place[5],'.','');
		}
		
		$prev_amn_sql=sql_select("select id, value_change_by, btb_lc_value, amendment_value from com_btb_lc_amendment where id=$update_id");
		if($prev_amn_sql[0][csf("value_change_by")]== str_replace("'", '', $cbo_value_change_by))
		{
			if(str_replace("'", '', $hide_value_change_by)==1) 
			{
				$lc_value=$lc_value-str_replace("'", '', $hide_amendment_value);
			} 
			else if(str_replace("'", '', $hide_value_change_by)==2)
			{ 
				$lc_value=$lc_value+str_replace("'", '', $hide_amendment_value);
			}
			else 
			{
				$lc_value = $lc_value;	
			}
		}

		
		$new_lc_value = str_replace("'", '', $txt_pi_value)*1;	
		
		//echo "10**".$new_lc_value."_ps";die;
		
		//echo $new_lc_value;die;
		
		$maximum_tolarence = 0; $minimum_tolarence = 0;
 		$maximum_tolarence = $new_lc_value+($new_lc_value*$data_array[0][csf('tolerance')])/100;
		$minimum_tolarence = $new_lc_value-($new_lc_value*$data_array[0][csf('tolerance')])/100;

		$hdn_qnty_change_by=str_replace("'", '',$hdn_qnty_change_by);
		$hdn_gmt_qnty_amnd=str_replace("'", '',$hdn_gmt_qnty_amnd);
		$cbo_garments_qnty_change_by=str_replace("'", '',$cbo_garments_qnty_change_by);
		$txt_gmt_qnty_amnd=str_replace("'", '',$txt_gmt_qnty_amnd);

		if($hdn_qnty_change_by==1)
		{
			$garments_qty=$garments_qty-$hdn_gmt_qnty_amnd;
		}
		else if($hdn_qnty_change_by==2)
		{
			$garments_qty=$garments_qty+$hdn_gmt_qnty_amnd;
		}

		if($cbo_garments_qnty_change_by==1)
		{
			$gmt_qnty_amnd=$garments_qty+$txt_gmt_qnty_amnd;
		}
		else if($cbo_garments_qnty_change_by==2)
		{
			$gmt_qnty_amnd=$garments_qty-$txt_gmt_qnty_amnd;
		}
		else
		{
			$gmt_qnty_amnd=$garments_qty;
		}
 		
		//update BTB lc table
		$field_array_update="lc_value*max_lc_value*min_lc_value*last_shipment_date*lc_expiry_date*delivery_mode_id*payterm_id*inco_term_id*inco_term_place*partial_shipment*port_of_loading*port_of_discharge*remarks*addendum_no*addendum_date*garments_qty*uom_id*tenor*pi_id*pi_value*updated_by*update_date";
		
		$data_array_update=$new_lc_value."*".$maximum_tolarence."*".$minimum_tolarence."*".$txt_last_shipment_date_amnd."*".$txt_expiry_date_amend."*".$cbo_delevery_mode_amnd."*".$cbo_pay_term_amnd."*".$cbo_inco_term."*".$txt_inco_term_place."*".$cbo_partial_ship_id."*".$txt_port_of_loading_amnd."*".$txt_port_of_discharge_amnd."*".$txt_remarks_amnd."*".$txt_addendum_no."*".$txt_addendum_date."*'".$gmt_qnty_amnd."'*1*".$txt_tenor_amnd."*".$txt_hidden_pi_id."*'".$txt_pi_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		
		$shipment_date=strtotime($data_array[0][csf('last_shipment_date')]);
		$shipment_date_amnd=strtotime(str_replace("'","",$txt_last_shipment_date_amnd));
		$expiry_date=strtotime($data_array[0][csf('lc_expiry_date')]);
		$expiry_date_amnd=strtotime(str_replace("'","",$txt_expiry_date_amend));

		$field_array_amnd=""; $data_array_amnd2="";
		$txt_amendment_value=str_replace("'", '', $txt_amendment_value);

		
		$prev_max_id = return_field_value("max(id) as id ","com_btb_lc_amendment","id < ".str_replace("'", '', $update_id)." and btb_id=$txt_system_id and value_change_by<>0 and status_active=1 and is_deleted=0","id");
		if($prev_max_id=="")
		{
			$prev_max_sql="select btb_lc_value, amendment_value, value_change_by from com_btb_lc_amendment where id < ".str_replace("'", '', $update_id)." and btb_id=$txt_system_id and status_active=1 and is_deleted=0";
			$prev_max_result=sql_select($prev_max_sql);
			$prev_max_lc_value=$prev_max_result[0][csf("btb_lc_value")];
			if($new_lc_value>$prev_max_lc_value)
			{
				$inc_dec=1;
				$amnd_value=$new_lc_value-$prev_max_lc_value;
			}
			else if($new_lc_value<$prev_max_lc_value)
			{
				$inc_dec=2;
				$amnd_value=$prev_max_lc_value-$new_lc_value;
			}
			else
			{
				$inc_dec=0;
				$amnd_value=0;
			}
		}
		else
		{
			$prev_max_sql="select btb_lc_value, amendment_value, value_change_by from com_btb_lc_amendment where id =$prev_max_id  and btb_id=$txt_system_id and status_active=1 and is_deleted=0";
			$prev_max_result=sql_select($prev_max_sql);
			$prev_max_lc_value=$prev_max_result[0][csf("btb_lc_value")];
			$prev_max_amendment_value=$prev_max_result[0][csf("amendment_value")];
			$prev_max_value_change_by=$prev_max_result[0][csf("value_change_by")];
			if($new_lc_value>$prev_max_lc_value)
			{
				$inc_dec=1;
				$amnd_value=$new_lc_value-$prev_max_lc_value;
			}
			else if($new_lc_value<$prev_max_lc_value)
			{
				$inc_dec=2;
				$amnd_value=$prev_max_lc_value-$new_lc_value;
			}
			else
			{
				$inc_dec=0;
				$amnd_value=0;
			}
		}
		
		//$field_array_amnd="amendment_date*btb_lc_no*btb_lc_value*amendment_value*value_change_by*updated_by*update_date";
		//$data_array_amnd2=$txt_amendment_date."*'".$btb_lc_no."'*'".$new_lc_value."'*'".$amnd_value."'*".$inc_dec."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_amnd="amendment_date*btb_lc_no*btb_lc_value*amendment_value*value_change_by*updated_by*update_date*application_amendment_date*qnty_change_by";
		$data_array_amnd2=$txt_amendment_date."*'".$btb_lc_no."'*'".$new_lc_value."'*'".$amnd_value."'*".$inc_dec."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_application_date_amnd."*".$cbo_garments_qnty_change_by;
		
		if($data_array[0][csf('pi_id')]!=str_replace("'","",$txt_hidden_pi_id))
		{
			$field_array_amnd.="*pi_id*pi_value";
			$data_array_amnd2.="*".$txt_hidden_pi_id."*'".$txt_pi_value."'";
			

			if(str_replace("'","",$txt_hidden_pi_id)!="")
			{
				$data_array2="";
				$tag_pi=explode(',',str_replace("'","",$txt_hidden_pi_id));
				
				$field_array2="id, com_btb_lc_master_details_id, pi_id, inserted_by, insert_date";
				for($i=0; $i<count($tag_pi); $i++)
				{  
					if($id_lbtb_lc_pi=="") {$id_lbtb_lc_pi=return_next_id( "id","com_btb_lc_pi", 1 ); }
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array2.="$add_comma(".$id_lbtb_lc_pi.",".$txt_system_id.",".$tag_pi[$i].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id_lbtb_lc_pi++;
				}
			
				/*$btb_pi=sql_insert("com_btb_lc_pi",$field_array2,$data_array2,0);
				if($flag==1) 
				{
					if($btb_pi) $flag=1; else $flag=0; 
				} */
			}
		}
		
		if($shipment_date!=$shipment_date_amnd)
		{
			$field_array_amnd.="*last_shipment_date";
			$data_array_amnd2.="*".$txt_last_shipment_date_amnd;
		}
		
		if($expiry_date!=$expiry_date_amnd)
		{
			$field_array_amnd.="*expiry_date";
			$data_array_amnd2.="*".$txt_expiry_date_amend;
		}
		
		if($data_array[0][csf('delivery_mode_id')]!=str_replace("'","",$cbo_delevery_mode_amnd))
		{
			$field_array_amnd.="*delivery_mode";
			$data_array_amnd2.="*".$cbo_delevery_mode_amnd;
		}
		
		if($data_array[0][csf('payterm_id')]!=str_replace("'","",$cbo_pay_term_amnd))
		{
			$field_array_amnd.="*pay_term";
			$data_array_amnd2.="*".$cbo_pay_term_amnd;
		}
		
		if($data_array[0][csf('inco_term_id')]!=str_replace("'","",$cbo_inco_term))
		{
			$field_array_amnd.="*inco_term";
			$data_array_amnd2.="*".$cbo_inco_term;
		}
		
		if($data_array[0][csf('inco_term_place')]!=str_replace("'","",$txt_inco_term_place))
		{
			$field_array_amnd.="*inco_term_place";
			$data_array_amnd2.="*".$txt_inco_term_place;
		}
		
		if($data_array[0][csf('partial_shipment')]!=str_replace("'","",$cbo_partial_ship_id))
		{
			$field_array_amnd.="*partial_shipment";
			$data_array_amnd2.="*".$cbo_partial_ship_id;
		}
		
		if($data_array[0][csf('port_of_loading')]!=str_replace("'","",$txt_port_of_loading_amnd))
		{
			$field_array_amnd.="*port_of_loading";
			$data_array_amnd2.="*".$txt_port_of_loading_amnd;
		}
		
		if($data_array[0][csf('port_of_discharge')]!=str_replace("'","",$txt_port_of_discharge_amnd))
		{
			$field_array_amnd.="*port_of_discharge";
			$data_array_amnd2.="*".$txt_port_of_discharge_amnd;
		}
		
		if($data_array[0][csf('remarks')]!=str_replace("'","",$txt_remarks_amnd))
		{
			$field_array_amnd.="*remarks";
			$data_array_amnd2.="*".$txt_remarks_amnd;
		}

		if($data_array[0][csf('addendum_no')]!=str_replace("'","",$txt_addendum_no))
		{
			$field_array_amnd.="*addendum_no";
			$data_array_amnd2.="*".$txt_addendum_no;
		}

		if($data_array[0][csf('addendum_date')]!=str_replace("'","",$txt_addendum_date))
		{
			$field_array_amnd.="*addendum_date";
			$data_array_amnd2.="*".$txt_addendum_date;
		}
        if($data_array[0][csf('garments_qty')]!=str_replace("'","",$txt_gmt_qnty_amnd))
        {
            $field_array_amnd.="*garments_qty";
            $data_array_amnd2.="*".$txt_gmt_qnty_amnd;
        }
        if($data_array[0][csf('uom_id')]!=str_replace("'","",$cbo_gmt_uom_id_amnd))
        {
            $field_array_amnd.="*uom_id";
            $data_array_amnd2.="*1";
        }
		
		if($data_array[0][csf('tenor')]!=str_replace("'","",$txt_tenor_amnd))
		{
			$field_array_amnd.="*tenor";
			$data_array_amnd2.="*".$txt_tenor_amnd;
		}
		
		if($ref_closing_status!=1)
		{
			$rID=sql_update("com_btb_lc_master_details",$field_array_update,$data_array_update,"id","".$txt_system_id."",0);
		}
		if($rID) $flag=1; else $flag=0;
		
		$rID2=sql_update("com_btb_lc_amendment",$field_array_amnd,$data_array_amnd2,"id","".$update_id."",1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		
		if($data_array[0][csf('pi_id')]!=str_replace("'","",$txt_hidden_pi_id))
		{
			//----------Insert Data in  com_btb_lc_pi Table----------------------------------------
			$delete=execute_query( "delete from com_btb_lc_pi where com_btb_lc_master_details_id = $txt_system_id",0);
			if($flag==1) 
			{
				if($delete) $flag=1; else $flag=0; 
			} 
			
			if(str_replace("'","",$txt_hidden_pi_id)!="")
			{
				$btb_pi=sql_insert("com_btb_lc_pi",$field_array2,$data_array2,0);
				if($flag==1) 
				{
					if($btb_pi) $flag=1; else $flag=0; 
				} 
			}
		}
		//	echo "5**1**".$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**0**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**1**".str_replace("'", '', $txt_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**0**".str_replace("'", '', $txt_system_id);
			}
			else
			{
				oci_rollback($con); 
				echo "6**1**".str_replace("'", '', $txt_system_id);
			}
		}
		disconnect($con);
		die;
	}
	
}

if($action == "print_amendment_letter"){
	extract($_REQUEST);
	//var_dump ($_REQUEST);
	$cbo_importer_id = str_replace("'","",$cbo_importer_id);
	$txt_amendment_date = str_replace("'","",$txt_amendment_date);
	$txt_internal_file_no = str_replace("'","",$txt_internal_file_no);
	$txt_btb_lc_no = str_replace("'","",$txt_btb_lc_no);
	$txt_lc_date = str_replace("'","",$txt_lc_date);
	$txt_lc_value = str_replace("'","",$txt_lc_value);
	$txt_amendment_no = str_replace("'","",$txt_amendment_no);
	$txt_amendment_value = str_replace("'","",$txt_amendment_value);
	$hide_amendment_value = str_replace("'","",$hide_amendment_value);
	$txt_hidden_pi_id = str_replace("'","",$txt_hidden_pi_id);
	$pi_nos = str_replace("'","",$txt_hidden_pi_id);
	$pi_nos = explode(",", $pi_nos);
	$txt_pi = explode(",", $txt_pi);
	
	if(count($pi_nos) > 1){
		$no_of_pi_ids = count($pi_nos);
		$pi_no = "<em>AS PER PI(".$no_of_pi_ids.")</em>";
	} else{
		$pi_no = $txt_pi[0];
	}
	$txt_pi_value = str_replace("'","",$txt_pi_value);
	$txt_last_shipment_date_amnd = str_replace("'","",$txt_last_shipment_date_amnd);
	$txt_expiry_date_amend = str_replace("'","",$txt_expiry_date_amend);
	$txt_system_id = str_replace("'","",$txt_system_id);
	$update_id = str_replace("'","",$update_id);
	$hide_value_change_by = str_replace("'","",$hide_value_change_by);
	$cbo_issuing_bank = str_replace("'","",$cbo_issuing_bank);
	$cbo_currency_name = str_replace("'","",$cbo_currency_name);
	
	$bank_array_res = sql_select("select id, bank_name, branch_name, contact_person, contact_no, email, address, designation  from lib_bank where id = $cbo_issuing_bank and issusing_bank=1 and is_deleted=0 and status_active =1");
	foreach ($bank_array_res as $row){
		$bank_array[$row[csf("id")]]["bank_name"] =$row[csf("bank_name")];
		$bank_array[$row[csf("id")]]["branch_name"] =$row[csf("branch_name")];
		$bank_array[$row[csf("id")]]["contact_person"] =$row[csf("contact_person")];
		$bank_array[$row[csf("id")]]["contact_no"] =$row[csf("contact_no")];
		$bank_array[$row[csf("id")]]["email"] =$row[csf("email")];
		$bank_array[$row[csf("id")]]["address"] =$row[csf("address")];
		$bank_array[$row[csf("id")]]["designation"] =$row[csf("designation")];
	}

	$designation_array_res = sql_select("select id, system_designation, custom_designation, custom_designation_local from lib_designation where is_deleted=0 and status_active =1");

	foreach ($designation_array_res as $row){
		$designation_array[$row[csf("id")]]["system_designation"] =$row[csf("system_designation")];
		$designation_array[$row[csf("id")]]["custom_designation"] =$row[csf("custom_designation")];
		$designation_array[$row[csf("id")]]["custom_designation_local"] =$row[csf("custom_designation_local")];
	}
	$company = return_library_array("select id, company_name from lib_company where is_deleted = 0 and status_active = 1", "id", "company_name");

	$sql = "SELECT id, btb_prefix_number, btb_system_id, lc_number, item_category_id, importer_id 
	FROM com_btb_lc_master_details 
	WHERE importer_id = '".$cbo_importer_id."' and is_deleted = 0 and item_basis_id=1 and id=$txt_system_id
	union all
	SELECT id, btb_prefix_number, btb_system_id, lc_number, item_category_id, importer_id 
	FROM com_btb_lc_master_details WHERE importer_id = '".$cbo_importer_id."' and is_deleted = 0 and item_basis_id=2 and id=$txt_system_id
	order by item_category_id, id";

	$btb_lc_result = sql_select($sql);

	foreach ($btb_lc_result as $row) {
		$btb_lc_details[$row[csf("id")]]["btb_prefix_number"] = $row[csf("btb_prefix_number")];
		$btb_lc_details[$row[csf("id")]]["btb_system_id"] = $row[csf("btb_system_id")];
		$btb_lc_details[$row[csf("id")]]["lc_number"] = $row[csf("lc_number")];
	}

	//echo $sql;//die;
	//var_dump($company);
	ob_start();
	?>
	<style type="text/css">
			.a4size {
	           width: 21cm;
	           height: 26.7cm;
	           font-family: Cambria, Georgia, serif;
			   font-size: 14px;
			   text-align:left;
			   padding-top:40px;
	        }
			.a4size table tr td, .a4size table tr th{
				font-size:inherit!important;
				}
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 18px;margin: 90px 100PX 54px 25px;
	            }
	        size: A4 portrait;
	        }
	</style>
	<div class="a4size">
	<br/>
		<table width="794" style="text-align:left;">
			<thead>
				<tr>
					<th width="25"></th>
					<th width="650">
						DATE :  <? echo strtoupper($txt_amendment_date); ?><br/>
						INTERNAL REF : <? echo $txt_internal_file_no; ?><br/> 
						SYSTEM REF : <? echo $btb_lc_details[$txt_system_id]["btb_system_id"]; ?>
					</th>
					<th width="25"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
					<strong>TO<br/>
						THE <? echo strtoupper($designation_array[$bank_array[$cbo_issuing_bank]["designation"]]["custom_designation"]); ?><br/>
						<? echo strtoupper($bank_array[$cbo_issuing_bank]["bank_name"]); ?><br/>
						<? echo strtoupper($bank_array[$cbo_issuing_bank]["address"]); ?><br/>
					</strong>
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td style="padding-top: 60px;">
					<strong>SUBJECT:</strong> REQUEST TO AMEND OUR BACK TO BACK L/C <strong><? echo $txt_btb_lc_no; ?></strong> DT. <strong><? echo strtoupper($txt_lc_date); ?></strong> BY <strong><? echo ($cbo_currency_name == 2) ? "$": "TK";  echo number_format($txt_amendment_value,4); ?></strong> MAKING TOTAL L/C VALUE TO <strong><? echo ($cbo_currency_name == 2) ? "$": "tk";  echo number_format($txt_lc_value,4); ?></strong> ONLY.
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
						DEAR CONCREN<br/><br/>

						WITH REFERENCE TO THE ABOVE, WE WOULD VERY MUCH APPRECIATE FOR AMENDING ABOVE MENTIONED LC AS FOLLOWS: <br/><br/>
						<ol style="margin-left: 25px; padding-left:10px;">
							<li>
								PLEASE INCREASE L/C VALUE BY <strong><? echo ($cbo_currency_name == 2) ? "$": "tk";  echo number_format($txt_amendment_value,4); ?></strong>	AMOUNT AFTER AMENDMENT WILL BE TOTAL <strong><? echo ($cbo_currency_name == 2) ? "$": "tk";  echo number_format($txt_lc_value,4); ?></strong>	INSTEAD OF EXISTING.
							</li>
							<li>
								SHIPMENT VALIDITY NOW TO BE EXTENDED AS PER BENEFICIARIES PRO-FORMA INVOICES NO. <strong><? echo $pi_no; ?></strong> DT. <strong><? echo strtoupper($txt_amendment_date); ?></strong> ENCLOSED.
							</li>
							<li>
								SHIPMENT DATE AND EXPIRY DATE 	WILL BE <strong><? echo strtoupper($txt_last_shipment_date_amnd); ?></strong> AND <strong><? echo strtoupper($txt_expiry_date_amend); ?></strong> RESPECTIVELY I/O EXISTING.
							</li>
							<li>ICI/CCB/MC/ADDEN: </li>
						</ol><br/><br/>

					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650">
					ALL OTHER TERMS & CONDITION OF THE LC WILL REMAIN UNCHANGED.<br/>

					THEREFORE, YOU ARE REQUESTED TO AMEND THE SAID L/C AT YOUR EARLIEST.

					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
					<strong>THANKING YOU,<br/> </strong>  
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td style="padding-top: 70px;">
					<? echo strtoupper($company[$cbo_importer_id]); ?>	
					</td>
					<td width="25"></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?
	$html = ob_get_contents();
	ob_clean();
	echo $html;
	exit();
}


if($action == "print_amendment_letter_4"){
	extract($_REQUEST);
	//var_dump ($_REQUEST);
	$cbo_importer_id = str_replace("'","",$cbo_importer_id);
	$txt_amendment_date = str_replace("'","",$txt_amendment_date);
	$txt_internal_file_no = str_replace("'","",$txt_internal_file_no);
	$txt_btb_lc_no = str_replace("'","",$txt_btb_lc_no);
	$txt_lc_date = str_replace("'","",$txt_lc_date);
	$txt_lc_value = str_replace("'","",$txt_lc_value);
	$txt_amendment_no = str_replace("'","",$txt_amendment_no);
	$txt_amendment_value = str_replace("'","",$txt_amendment_value);
	$hide_amendment_value = str_replace("'","",$hide_amendment_value);
	$txt_hidden_pi_id = str_replace("'","",$txt_hidden_pi_id);
	$pi_nos = str_replace("'","",$txt_hidden_pi_id);
	$pi_nos = explode(",", $pi_nos);
	$txt_pi2 = str_replace("'","", $txt_pi);
	$txt_pi = explode(",", $txt_pi);
	$gmt_Qnty=str_replace("'","",$txt_gmt_qnty_amnd);
	
	$txt_pi_value = str_replace("'","",$txt_pi_value);
	
	if(count($pi_nos) > 1){
		$no_of_pi_ids = count($pi_nos);
		$pi_no = "<em>AS PER PI(".$no_of_pi_ids.")</em>";
	} else{
		$pi_no = $txt_pi[0];
	}
	$txt_pi_value = str_replace("'","",$txt_pi_value);
	$txt_last_shipment_date_amnd = str_replace("'","",$txt_last_shipment_date_amnd);
	$txt_expiry_date_amend = str_replace("'","",$txt_expiry_date_amend);
	$txt_system_id = str_replace("'","",$txt_system_id);
	$update_id = str_replace("'","",$update_id);
	$hide_value_change_by = str_replace("'","",$hide_value_change_by);
	$cbo_issuing_bank = str_replace("'","",$cbo_issuing_bank);
	$cbo_currency_name = str_replace("'","",$cbo_currency_name);
	
	$bank_array_res = sql_select("select id, bank_name, branch_name, contact_person, contact_no, email, address, designation  from lib_bank where id = $cbo_issuing_bank and issusing_bank=1 and is_deleted=0 and status_active =1");
	foreach ($bank_array_res as $row){
		$bank_array[$row[csf("id")]]["bank_name"] =$row[csf("bank_name")];
		$bank_array[$row[csf("id")]]["branch_name"] =$row[csf("branch_name")];
		$bank_array[$row[csf("id")]]["contact_person"] =$row[csf("contact_person")];
		$bank_array[$row[csf("id")]]["contact_no"] =$row[csf("contact_no")];
		$bank_array[$row[csf("id")]]["email"] =$row[csf("email")];
		$bank_array[$row[csf("id")]]["address"] =$row[csf("address")];
		$bank_array[$row[csf("id")]]["designation"] =$row[csf("designation")];
	}

	$designation_array_res = sql_select("select id, system_designation, custom_designation, custom_designation_local from lib_designation where is_deleted=0 and status_active =1");

	foreach ($designation_array_res as $row){
		$designation_array[$row[csf("id")]]["system_designation"] =$row[csf("system_designation")];
		$designation_array[$row[csf("id")]]["custom_designation"] =$row[csf("custom_designation")];
		$designation_array[$row[csf("id")]]["custom_designation_local"] =$row[csf("custom_designation_local")];
	}
	$company = return_library_array("select id, company_name from lib_company where is_deleted = 0 and status_active = 1", "id", "company_name");

	$sql = "SELECT id, btb_prefix_number, btb_system_id, lc_number, item_category_id, importer_id 
	FROM com_btb_lc_master_details 
	WHERE importer_id = '".$cbo_importer_id."' and is_deleted = 0 and item_basis_id=1 and id=$txt_system_id
	union all
	SELECT id, btb_prefix_number, btb_system_id, lc_number, item_category_id, importer_id 
	FROM com_btb_lc_master_details WHERE importer_id = '".$cbo_importer_id."' and is_deleted = 0 and item_basis_id=2 and id=$txt_system_id
	order by item_category_id, id";

	$btb_lc_result = sql_select($sql);

	foreach ($btb_lc_result as $row) {
		$btb_lc_details[$row[csf("id")]]["btb_prefix_number"] = $row[csf("btb_prefix_number")];
		$btb_lc_details[$row[csf("id")]]["btb_system_id"] = $row[csf("btb_system_id")];
		$btb_lc_details[$row[csf("id")]]["lc_number"] = $row[csf("lc_number")];
	}

	//echo $sql;//die;
	//var_dump($company);
	$for_five_Six1=sql_select("select terms from  wo_booking_terms_condition where entry_form=706 and booking_no=$txt_btb_lc_no");
	$for_five_Six2=sql_select("select terms from  lib_terms_condition  where is_default=1 and page_id in(706) order by id asc ");
	$five_six_data=array();
	if(count($for_five_Six1)>0){
		foreach($for_five_Six1 as $val){
			array_push($five_six_data,$val);
		}
	}

	else{
		foreach($for_five_Six2 as $val){
			array_push($five_six_data,$val);
		}
	}
	 
	$sqlw="select a.currency_id, a.lc_date, a.importer_id, b.lc_sc_id, b.is_lc_sc, b.current_distribution, b.status_active from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and a.LC_NUMBER='$txt_btb_lc_no' and b.is_deleted=0 and b.status_active=1";
	$lc_sc_sql=sql_select($sqlw);
	$count_row=count($lc_sc_sql);
	$ex_sl_lc="";
	$ex_sl_lc_date="";
	$i=0;
	if($count_row>0)
	{
		foreach($lc_sc_sql as $row_lc)
		{
			 
			if($row_lc[csf("is_lc_sc")]==0)
			{				 
				$sql_sc_lc=sql_select("select export_lc_no as lc_sc_no, LC_DATE as lc_date, buyer_name, lc_value as value,import_btb from com_export_lc where id='".$row_lc[csf("lc_sc_id")]."'");
			}
			else
			{				 
				$sql_sc_lc=sql_select("select contract_no as lc_sc_no,CONTRACT_DATE as lc_date, buyer_name, contract_value as value, 0 as import_btb from com_sales_contract where id='".$row_lc[csf("lc_sc_id")]."'");
			}		
			if($i==0){
				$ex_sl_lc=$ex_sl_lc." &nbsp;".$sql_sc_lc[0]['LC_SC_NO'];
				$ex_sl_lc_date=$ex_sl_lc_date." &nbsp;".$sql_sc_lc[0]['LC_DATE'];
			}
			else{
				$ex_sl_lc=$ex_sl_lc.", &nbsp;".$sql_sc_lc[0]['LC_SC_NO'];
				$ex_sl_lc_date=$ex_sl_lc_date.", &nbsp;".$sql_sc_lc[0]['LC_DATE'];
			}
			$i++;	 
		}
		$i=0;
	}	
	$pi_no=explode(",",$txt_pi2);$pi_date="";
	foreach($pi_no as $var){
		$pi_date_sql="select PI_DATE from com_pi_master_details where PI_NUMBER='$var'";
		$pi_date_result=sql_select($pi_date_sql);
		if($i==0){
			$pi_date=$pi_date." &nbsp;".$pi_date_result[0]['PI_DATE'];
		}
		else{
			$pi_date=$pi_date.", &nbsp;".$pi_date_result[0]['PI_DATE'];
		}
		$i++;
	}
	
	 

	ob_start();
	?>
	<style type="text/css">
			.a4size {
	           width: 21cm;
	           height: 26.7cm;
	           font-family: Cambria, Georgia, serif;
			   font-size: 14px;
			   text-align:left;
			   padding-top:40px;
	        }
			.a4size table tr td, .a4size table tr th{
				font-size:inherit!important;
				}
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 18px;margin: 90px 100PX 54px 25px;
	            }
	        size: A4 portrait;
	        }
	</style>
	<div class="a4size">
	<br/>
		<table width="794" style="text-align:left;">
			<thead>
				<tr>
					<th width="25"></th>
					<th width="650">
						DATE :  <? echo strtoupper($txt_amendment_date); ?><br/>						 
					</th>
					<th width="25"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
					    TO<br/>
						The Manager<br/>
						<? echo strtoupper($bank_array[$cbo_issuing_bank]["bank_name"]); ?><br/>
						<? echo strtoupper($bank_array[$cbo_issuing_bank]["address"])."."; ?><br/>
					
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td style="padding-top: 60px;">
					<strong>SUBJECT:</strong> Request for amend the back to back L/C no. <strong><? echo $txt_btb_lc_no; ?></strong> dated: <strong><? echo strtoupper($txt_lc_date); ?></strong> for US$ <strong> <? echo number_format($txt_pi_value,2); ?> </strong>against Export LC /Sales Contract no:<strong><? echo $ex_sl_lc;  ?></strong> dated: <strong><? echo $ex_sl_lc_date.".";  ?></strong> 
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
						DEAR Sir,<br/><br/>

						We due respect we would like to request you that please amend the above L/C as follows: <br/><br/>
						<ol style="margin-left: 25px; padding-left:10px;">
							<li>
							 The L/C will be Value increase of USD <strong><? echo number_format($txt_amendment_value,2); ?></strong> and total L/C value now to read USD <strong><? echo number_format($txt_pi_value,2);  ?></strong> only. 
							</li>
							<li>
							Please read new Proforma Invoice no: <strong><? echo $txt_pi2; ?> </strong> date: <strong><? echo $pi_date; ?></strong>
							</li>
							<li>
							Please insert the new garments quantity <strong><? echo $gmt_Qnty; ?></strong> pcs instead of existing.
							</li>
							<li>
							Shipment date needs to be extended up to <strong><? echo $txt_last_shipment_date_amnd; ?></strong> and Expiry date will be <strong><? echo $txt_expiry_date_amend; ?></strong> instead of existing.
							</li>
							<li>
								 <? echo $five_six_data[0]['TERMS']; ?>
							</li>
							<li>
								<? echo $five_six_data[1]['TERMS']; ?>
							</li>
						</ol><br/><br/>

					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650">
					All other terms and condition will be remaining unchanged.<br/><br/><br/>

					Your kind co-operation and early action will be highly appreciated.

					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
					<strong>THANKING YOU.<br/> </strong>  
					</td>
					<td width="25"></td>
				</tr>				 
			</tbody>
		</table>
	</div>
	<?
	$html = ob_get_contents();
	ob_clean();
	echo $html;
	exit();
}

if($action == "print_amendment_letter_2")
{
	extract($_REQUEST);
	//var_dump ($_REQUEST);
	$cbo_importer_id = str_replace("'","",$cbo_importer_id);
	$txt_amendment_date = str_replace("'","",$txt_amendment_date);
	$txt_internal_file_no = str_replace("'","",$txt_internal_file_no);
	$txt_btb_lc_no = str_replace("'","",$txt_btb_lc_no);
	$txt_lc_date = str_replace("'","",$txt_lc_date);
	$txt_lc_value = str_replace("'","",$txt_lc_value);
	$txt_amendment_no = str_replace("'","",$txt_amendment_no);
	$txt_amendment_value = str_replace("'","",$txt_amendment_value);
	$hide_amendment_value = str_replace("'","",$hide_amendment_value);
	$txt_hidden_pi_id = str_replace("'","",$txt_hidden_pi_id);
	$pi_nos = str_replace("'","",$txt_hidden_pi_id);
	$pi_nos = explode(",", $pi_nos);
	$txt_pi = explode(",", $txt_pi);
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);//die;

	
	if(count($pi_nos) > 1){
		$no_of_pi_ids = count($pi_nos);
		$pi_no = "<em>AS PER PI(".$no_of_pi_ids.")</em>";
	} else{
		$pi_no = $txt_pi[0];
	}
	$txt_pi_value = str_replace("'","",$txt_pi_value);
	$txt_last_shipment_date_amnd = str_replace("'","",$txt_last_shipment_date_amnd);
	$txt_expiry_date_amend = str_replace("'","",$txt_expiry_date_amend);
	$txt_system_id = str_replace("'","",$txt_system_id);
	$update_id = str_replace("'","",$update_id);
	$hide_value_change_by = str_replace("'","",$hide_value_change_by);

	if($hide_value_change_by == 1) {
		$value_changed_by = "INCREASE";
	}else{
		$value_changed_by = "DECREASE";
	}
	$cbo_issuing_bank = str_replace("'","",$cbo_issuing_bank);
	$cbo_currency_name = str_replace("'","",$cbo_currency_name);
	
	$bank_array_res = sql_select("select id, bank_name, branch_name, contact_person, contact_no, email, address, designation  from lib_bank where id = $cbo_issuing_bank and issusing_bank=1 and is_deleted=0 and status_active =1");
	foreach ($bank_array_res as $row){
		$bank_array[$row[csf("id")]]["bank_name"] =$row[csf("bank_name")];
		$bank_array[$row[csf("id")]]["branch_name"] =$row[csf("branch_name")];
		$bank_array[$row[csf("id")]]["contact_person"] =$row[csf("contact_person")];
		$bank_array[$row[csf("id")]]["contact_no"] =$row[csf("contact_no")];
		$bank_array[$row[csf("id")]]["email"] =$row[csf("email")];
		$bank_array[$row[csf("id")]]["address"] =$row[csf("address")];
		$bank_array[$row[csf("id")]]["designation"] =$row[csf("designation")];
	}

	$designation_array_res = sql_select("select id, system_designation, custom_designation, custom_designation_local from lib_designation where is_deleted=0 and status_active =1");

	foreach ($designation_array_res as $row){
		$designation_array[$row[csf("id")]]["system_designation"] =$row[csf("system_designation")];
		$designation_array[$row[csf("id")]]["custom_designation"] =$row[csf("custom_designation")];
		$designation_array[$row[csf("id")]]["custom_designation_local"] =$row[csf("custom_designation_local")];
	}
	$company = return_library_array("select id, company_name from lib_company where is_deleted = 0 and status_active = 1", "id", "company_name");
	$supplier = return_library_array("select id, supplier_name from lib_supplier where is_deleted = 0 and status_active = 1", "id", "supplier_name");
	//var_dump($supplier);

	$sql = "select id, btb_prefix_number, btb_system_id, lc_number, item_category_id, importer_id from com_btb_lc_master_details where importer_id = '".$cbo_importer_id."' and is_deleted = 0 and item_basis_id=1 and id=$txt_system_id
	union all
	select id, btb_prefix_number, btb_system_id, lc_number, item_category_id, importer_id 
	from com_btb_lc_master_details where importer_id = '".$cbo_importer_id."' and is_deleted = 0 and item_basis_id=2 and id=$txt_system_id
	order by item_category_id, id";

	$btb_lc_result = sql_select($sql);

	foreach ($btb_lc_result as $row) {
		$btb_lc_details[$row[csf("id")]]["btb_prefix_number"] = $row[csf("btb_prefix_number")];
		$btb_lc_details[$row[csf("id")]]["btb_system_id"] = $row[csf("btb_system_id")];
		$btb_lc_details[$row[csf("id")]]["lc_number"] = $row[csf("lc_number")];
	}

	$sql_hs_code = "select a.id, a.hs_code, a.pi_number, a.pi_date  from com_pi_master_details a, com_btb_lc_pi b  where a.id = b.pi_id and   a.item_category_id = 1 and a.importer_id = $cbo_importer_id and b.com_btb_lc_master_details_id = $txt_system_id and a.is_deleted = 0 and a.status_active =1 and b.status_active =1 ";
	$hs_code_resutl = sql_select($sql_hs_code);
	foreach ($hs_code_resutl as $key => $value) {
		$hs_code = $value[csf("hs_code")];
		$pi_number = $value[csf("pi_number")];
		$pi_date = $value[csf("pi_date")];
	}

	//echo $sql;//die;
	//var_dump($company);
	ob_start();
	?>
	<style type="text/css">
			.a4size {
	           width: 21cm;
	           height: 26.7cm;
	           font-family: Cambria, Georgia, serif;
			   font-size: 14px;
			   text-align:left;
			   padding-top:40px;
	        }
			.a4size table tr td, .a4size table tr th{
				font-size:inherit!important;
				}
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 18px;margin: 90px 100PX 54px 25px;
	            }
	        size: A4 portrait;
	        }
	</style>
	<div class="a4size">
	<br/>
		<table width="794" style="text-align:left;">
			<thead>
				<tr>
					<th width="25"></th>
					<th width="650">
						DATE :  <? echo strtoupper(date("d.m.Y")); ?><br/>
						
					</th>
					<th width="25"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
					<strong>TO<br/>
						THE <? echo strtoupper($designation_array[$bank_array[$cbo_issuing_bank]["designation"]]["custom_designation"]); ?><br/>
						<? echo strtoupper($bank_array[$cbo_issuing_bank]["bank_name"]); ?><br/>
						<? echo strtoupper($bank_array[$cbo_issuing_bank]["address"]); ?><br/>
					</strong>
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td style="padding-top: 60px;">
					<strong>SUBJECT:</strong> PLEASE AMEND <? echo ".".$txt_amendment_no; ?> THE L/C NO: <strong><? echo $txt_btb_lc_no; ?></strong> DT. <strong><? echo strtoupper($txt_lc_date); ?></strong> VALUE <strong><? echo ($cbo_currency_name == 2) ? "$ USD. (": "TK (";  
					echo ($cbo_currency_name == 2) ? "$": "TK";
					echo $txt_pi_value.")"; ?></strong> BENE NAME. <strong><? echo $supplier[$cbo_supplier_id]; ?></strong>
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
						DEAR SIR,<br/><br/>

						We need an  amendment for the following clauses.... <br/><br/>
						<ol style="margin-left: 25px; padding-left:10px;">
							<li>
								MENTION HS CODE <strong><? echo $hs_code; ?></strong> I/O <? echo $hs_code;?>	UNDER TAG F47A POINT 3 </strong>
							</li>
							<li>
								IN THE CREDIT PLEASE ADD THE PROFORMA INVOICE NO: <strong><? echo $pi_no; ?></strong> DT. <strong><? echo strtoupper($pi_date); ?></strong> VALUE <? echo ($cbo_currency_name == 2) ? "USD-": "TK-";  echo $txt_pi_value; ?>
							</li>
							<li>
								IN THE CREDIT VALUE <? echo $value_changed_by;?> BY  <? echo ($cbo_currency_name == 2) ? "USD-": "TK-";  echo $txt_amendment_value; ?> AND NOW TOTAL CREDIT VALUE IS TO BE READ AS <? echo ($cbo_currency_name == 2) ? "USD-": "TK-";  echo $txt_pi_value; ?>
							</li>
							<li>
								SHIPMENT DATE: <strong><? echo strtoupper($txt_last_shipment_date_amnd); ?></strong> DATE AND PLACE OF EXPIRY <strong><? echo strtoupper($txt_expiry_date_amend); ?></strong> INSTEAD OF EXISTION.
							</li>
						</ol><br/><br/>

					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650">
					ALL OTHER TERMS & CONDITION OF THE LC WILL REMAIN UNCHANGED.<br/>

					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
					<strong>THANKING YOU,<br/> </strong>  
					<strong>YOURS FAITHFULLY,<br/> </strong>  
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td style="padding-top: 70px;">
					<p style="border-top: 1px solid black;"><strong><? echo "Authorised Signature"; ?></strong></p>
					</td>
					<td width="25"></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?
	$html = ob_get_contents();
	ob_clean();
	echo $html;
	exit();
}


if($action == "print_amendment_letter_3")
{
	extract($_REQUEST);
	//var_dump ($_REQUEST);
	$cbo_importer_id = str_replace("'","",$cbo_importer_id);
	$txt_amendment_date = str_replace("'","",$txt_amendment_date);
	$txt_internal_file_no = str_replace("'","",$txt_internal_file_no);
	$txt_btb_lc_no = str_replace("'","",$txt_btb_lc_no);
	$txt_lc_date = str_replace("'","",$txt_lc_date);
	$txt_lc_value = str_replace("'","",$txt_lc_value);
	$txt_amendment_no = str_replace("'","",$txt_amendment_no);
	$txt_amendment_value = str_replace("'","",$txt_amendment_value);
	$hide_amendment_value = str_replace("'","",$hide_amendment_value);
	$txt_hidden_pi_id = str_replace("'","",$txt_hidden_pi_id);
	$pi_nos = str_replace("'","",$txt_hidden_pi_id);
	$pi_nos = explode(",", $pi_nos);
	$txt_pi = explode(",", $txt_pi);
	$cbo_supplier_id = str_replace("'","",$cbo_supplier_id);//die;

	
	if(count($pi_nos) > 1){
		$no_of_pi_ids = count($pi_nos);
		$pi_no = "<em>AS PER PI(".$no_of_pi_ids.")</em>";
	} else{
		$pi_no = $txt_pi[0];
	}
	$txt_pi_value = str_replace("'","",$txt_pi_value);
	$txt_last_shipment_date_amnd = str_replace("'","",$txt_last_shipment_date_amnd);
	$txt_expiry_date_amend = str_replace("'","",$txt_expiry_date_amend);
	$txt_system_id = str_replace("'","",$txt_system_id);
	$update_id = str_replace("'","",$update_id);
	$hide_value_change_by = str_replace("'","",$hide_value_change_by);

	if($hide_value_change_by == 1) {
		$value_changed_by = "INCREASE";
	}else{
		$value_changed_by = "DECREASE";
	}
	$cbo_issuing_bank = str_replace("'","",$cbo_issuing_bank);
	$cbo_currency_name = str_replace("'","",$cbo_currency_name);
	
	$bank_array_res = sql_select("select id, bank_name, branch_name, contact_person, contact_no, email, address, designation  from lib_bank where id = $cbo_issuing_bank and issusing_bank=1 and is_deleted=0 and status_active =1");
	foreach ($bank_array_res as $row){
		$bank_array[$row[csf("id")]]["bank_name"] =$row[csf("bank_name")];
		$bank_array[$row[csf("id")]]["branch_name"] =$row[csf("branch_name")];
		$bank_array[$row[csf("id")]]["contact_person"] =$row[csf("contact_person")];
		$bank_array[$row[csf("id")]]["contact_no"] =$row[csf("contact_no")];
		$bank_array[$row[csf("id")]]["email"] =$row[csf("email")];
		$bank_array[$row[csf("id")]]["address"] =$row[csf("address")];
		$bank_array[$row[csf("id")]]["designation"] =$row[csf("designation")];
	}

	$designation_array_res = sql_select("select id, system_designation, custom_designation, custom_designation_local from lib_designation where is_deleted=0 and status_active =1");

	foreach ($designation_array_res as $row){
		$designation_array[$row[csf("id")]]["system_designation"] =$row[csf("system_designation")];
		$designation_array[$row[csf("id")]]["custom_designation"] =$row[csf("custom_designation")];
		$designation_array[$row[csf("id")]]["custom_designation_local"] =$row[csf("custom_designation_local")];
	}
	$company = return_library_array("select id, company_name from lib_company where is_deleted = 0 and status_active = 1", "id", "company_name");
	$supplier = return_library_array("select id, supplier_name from lib_supplier where is_deleted = 0 and status_active = 1", "id", "supplier_name");
	//var_dump($supplier);

	$sql = "select id, btb_prefix_number, btb_system_id, lc_number, item_category_id, importer_id, cover_note_no, cover_note_date from com_btb_lc_master_details where importer_id = '".$cbo_importer_id."' and is_deleted = 0 and item_basis_id in(1,2) and id=$txt_system_id
	order by item_category_id, id";

	$btb_lc_result = sql_select($sql);

	foreach ($btb_lc_result as $row) {
		$btb_lc_details[$row[csf("id")]]["btb_prefix_number"] = $row[csf("btb_prefix_number")];
		$btb_lc_details[$row[csf("id")]]["btb_system_id"] = $row[csf("btb_system_id")];
		$btb_lc_details[$row[csf("id")]]["lc_number"] = $row[csf("lc_number")];
		$cover_note_no=$row[csf("cover_note_no")];
		$cover_note_date=$row[csf("cover_note_date")];
	}

	$sql_hs_code = "select a.id, a.hs_code, a.pi_number, a.pi_date  from com_pi_master_details a, com_btb_lc_pi b  where a.id = b.pi_id  and a.importer_id = $cbo_importer_id and b.com_btb_lc_master_details_id = $txt_system_id and a.is_deleted = 0 and a.status_active =1 and b.status_active =1 ";
	$hs_code_resutl = sql_select($sql_hs_code);
	foreach ($hs_code_resutl as $key => $value) {
		$hs_code = $value[csf("hs_code")];
		$pi_number = $value[csf("pi_number")];
		$pi_date = $value[csf("pi_date")];
	}

	//echo $sql;//die;
	//var_dump($company);
	ob_start();
	?>
	<style type="text/css">
			.a4size {
	           width: 21cm;
	           height: 26.7cm;
	           font-family: Cambria, Georgia, serif;
			   font-size: 14px;
			   text-align:left;
			   padding-top:40px;
	        }
			.a4size table tr td, .a4size table tr th{
				font-size:inherit!important;
				}
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 18px;margin: 90px 100PX 54px 25px;
	            }
	        size: A4 portrait;
	        }
	</style>
	<div class="a4size">
	<br/>
		<table width="794" style="text-align:left;">
			<thead>
				<tr>
					<th width="25"></th>
					<th width="650">
						Date :  <? echo strtoupper(date("d.m.Y")); ?><br/>
						
					</th>
					<th width="25"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
					<strong>TO<br/>
						THE <? echo strtoupper($designation_array[$bank_array[$cbo_issuing_bank]["designation"]]["custom_designation"]); ?><br/>
						<? echo strtoupper($bank_array[$cbo_issuing_bank]["bank_name"]); ?><br/>
						<? echo strtoupper($bank_array[$cbo_issuing_bank]["address"]); ?><br/>
					</strong>
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td style="padding-top: 60px;">
					<strong>SUBJECT:</strong> PLEASE AMEND <? echo ".".$txt_amendment_no; ?> THE L/C NO: <strong><? echo $txt_btb_lc_no; ?></strong> DT. <strong><? echo strtoupper($txt_lc_date); ?></strong> VALUE <strong><? echo ($cbo_currency_name == 2) ? "$ USD. (": "TK (";  
					echo ($cbo_currency_name == 2) ? "$": "TK";
					echo $txt_pi_value.")"; ?></strong> BENE NAME. <strong><? echo $supplier[$cbo_supplier_id]; ?></strong>
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
						DEAR SIR,<br/><br/>

						WE NEED AN  AMENDMENT FOR THE FOLLOWING CLAUSES.... <br/><br/>
						<ol style="margin-left: 25px; padding-left:10px;">
							<li>
								MENTION HS CODE <strong><? echo $hs_code; ?></strong> I/O <? echo $hs_code;?>	UNDER TAG F47A POINT 3 </strong>
							</li>
							<li>
								IN THE CREDIT PLEASE ADD THE PROFORMA INVOICE NO: <strong><? echo $pi_no; ?></strong> DT. <strong><? echo strtoupper($pi_date); ?></strong> VALUE <? echo ($cbo_currency_name == 2) ? "USD-": "TK-";  echo $txt_pi_value; ?>
							</li>
							<li>
								IN THE CREDIT VALUE <? echo $value_changed_by;?> BY  <? echo ($cbo_currency_name == 2) ? "USD-": "TK-";  echo $txt_amendment_value; ?> AND NOW TOTAL CREDIT VALUE IS TO BE READ AS <? echo ($cbo_currency_name == 2) ? "USD-": "TK-";  echo $txt_pi_value; ?>
							</li>
							<li>
								SHIPMENT DATE: <strong><? echo strtoupper($txt_last_shipment_date_amnd); ?></strong> DATE AND PLACE OF EXPIRY <strong><? echo strtoupper($txt_expiry_date_amend); ?></strong> INSTEAD OF EXISTION.
							</li>
							<li>
								PLEASE READ INSURANCE AMENDMENT ENDORSEMENT NO. <strong><? echo $cover_note_no; ?></strong> DT. <strong><? echo strtoupper($cover_note_date); ?></strong> IN ADDITION TO EXISTING.
							</li>
						</ol><br/><br/>
						
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650">
					ALL OTHER TERMS & CONDITION OF THE LC WILL REMAIN UNCHANGED.<br/>

					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td width="650" style="padding-top: 45px;">
					<strong>THANKING YOU,<br/> </strong>  
					<strong>YOURS FAITHFULLY,<br/> </strong>  
					</td>
					<td width="25"></td>
				</tr>
				<tr>
					<td width="25"></td>
					<td style="padding-top: 70px;">
					<p style="border-top: 1px solid black;"><strong><? echo "Authorised Signature"; ?></strong></p>
					</td>
					<td width="25"></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?
	$html = ob_get_contents();
	ob_clean();
	echo $html;
	exit();
}
?>


 