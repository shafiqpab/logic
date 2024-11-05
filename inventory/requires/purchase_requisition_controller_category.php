<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 160,"select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1","id,location_name", 1, "-- Select --", $selected, "" );
}

if ($action=="load_drop_down_division")
{
	echo create_drop_down( "cbo_division_name", 160,"select id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1","id,division_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/purchase_requisition_controller', this.value, 'load_drop_down_department','department_td');" );
}

if ($action=="load_drop_down_department")
{
	echo create_drop_down( "cbo_department_name", 160,"select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/purchase_requisition_controller', this.value, 'load_drop_down_section','section_td');" );
}

if ($action=="load_drop_down_section")
{
	echo create_drop_down( "cbo_section_name", 160,"select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1","id,section_name", 1, "-- Select --", $selected, "" );
}

if ($action=="load_drop_down_stor")
{
	$data=explode("_",$data);
	 echo create_drop_down( "cbo_store_name", 160,"select a.id,a.store_name,b.store_location_id,b.category_type from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=$data[0] and a.is_deleted=0 and a.company_id='$data[1]' and a.status_active=1","id,store_name", 1, "-- Select --", $selected, "" );
}


if ($action=="purchase_requisition_popup")
{
 	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	extract($_REQUEST); 
?>
<script>
	  function js_set_value(id)
	  {
		  document.getElementById('selected_job').value=id;
		  parent.emailwindow.hide();
	  }
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="purchaserequisition_2"  id="purchaserequisition_2" autocomplete="off">
	<table width="900" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Item Category</th>
                        <th width="100">Requisition No</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>            
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_job">
							<? 
								echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, ""); 
                            ?>
                    	</td>
                   		<td>
							<? 
								echo create_drop_down( "cbo_item_category_id", 170,$item_category,"", 1, "-- Select --", $selected, "","","","","","1,2,3,12,13,14,24,25"); 	 
                            ?>
                        </td>
                        <td>
                           
                            <input name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:100px">
					 	</td>
                    	<td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 	</td> 
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_requisition_no').value, 'purchase_requisition_list_view', 'search_div', 'purchase_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                       </td>
        			</tr>
             	</table>
          	</td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
				<? echo load_month_buttons(1);  ?>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top" id="search_div"> 
            </td>
        </tr>
    </table>    
    </form>
   </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="purchase_requisition_list_view")
{	
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $item_category_id=" and item_category_id='$data[1]'"; else $item_category_id="";//{ echo "Please Select item category."; die; }
	
	$requisition_no=$data[4];
	if(str_replace("'","",$requisition_no)!="") $get_cond .= "and requ_no  like '%".str_replace("'","",$requisition_no)."%'  "; else  $get_cond=""; 
	
	//echo $requisition_no;
	
	if($db_type==0)
				{
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $order_rcv_date ="";
				}
	if($db_type==2)
	{
if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], 'mm-dd-yyyy','/',1)."' and '".change_date_format($data[3], 'mm-dd-yyyy','/',1)."'"; else $order_rcv_date ="";
	}
	
	$sql= "select id,requ_prefix_num,requisition_date,company_id,item_category_id,location_id,department_id,section_id,manual_req from inv_purchase_requisition_mst where status_active=1 and is_deleted=0 and entry_form=69 $company  $item_category_id $order_rcv_date $get_cond  order by id desc";
	 
	$company=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	
	$arr=array (2=>$company,3=>$item_category,4=>$location,5=>$department,6=>$section);
	
	echo  create_list_view("list_view", "Requisition No,Requisition Date,Company,Item Category,Location,Department,Section,Manual Req", "80,80,100,100,100,90,90,80","850","250",0, $sql , "js_set_value", "id", "",1,"0,0,company_id,item_category_id,location_id,department_id,section_id,0", $arr , "requ_prefix_num,requisition_date,company_id,item_category_id,location_id,department_id,section_id,manual_req","purchase_requisition_controller","",'0,3,0,0,0,0,0,0') ;	
	exit(); 
} 

if ($action=="load_php_requ_popup_to_form")
{
	 $nameArray=sql_select( "select id,requ_no,company_id,item_category_id,location_id,division_id,department_id,section_id,requisition_date,store_name,pay_mode,source,cbo_currency,delivery_date,remarks,manual_req,is_approved from inv_purchase_requisition_mst where id='$data'" );

	 /*---------------additional code--------------*/

	 /*$nameArray=sql_select( "select id,requ_no,company_id,item_category_id,location_id,division_id,department_id,section_id,requisition_date,store_name,pay_mode,source,cbo_currency,delivery_date,remarks,brand_name,model,origin,manual_req,is_approved from inv_purchase_requisition_mst where id='$data'" );*/
	  
  foreach ($nameArray as $row)
  {	
	 
	  echo "document.getElementById('txt_requisition_no').value 		= '".$row[csf("requ_no")]."';\n";  
	  echo "document.getElementById('cbo_company_name').value 			= '".$row[csf("company_id")]."';\n";  
	  //echo "$('#cbo_company_name').attr('disabled','true')".";\n";
	  echo "document.getElementById('cbo_item_category_id').value 		= '".$row[csf("item_category_id")]."';\n";
	  //echo "$('#cbo_item_category_id').attr('disabled',true);\n";
	  echo "show_list_view('".$row[csf("item_category_id")]."','item_category_details', 'item_category_div', 'requires/purchase_requisition_controller', '' );\n";
	  echo "load_drop_down( 'requires/purchase_requisition_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location','location_td');\n";
	  echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
	  echo "load_drop_down( 'requires/purchase_requisition_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_division','division_td');\n"; 	
	  echo "document.getElementById('cbo_division_name').value			= '".$row[csf("division_id")]."';\n"; 
	  echo "load_drop_down( 'requires/purchase_requisition_controller', document.getElementById('cbo_division_name').value, 'load_drop_down_department','department_td');\n"; 
	  echo "document.getElementById('cbo_department_name').value		= '".$row[csf("department_id")]."';\n";
	  echo "load_drop_down( 'requires/purchase_requisition_controller', document.getElementById('cbo_department_name').value, 'load_drop_down_section','section_td');\n"; 
	  echo "document.getElementById('cbo_section_name').value			= '".$row[csf("section_id")]."';\n"; 	
	  echo "document.getElementById('txt_date_from').value				= '".change_date_format($row[csf("requisition_date")])."';\n"; 
	  echo "load_drop_down( 'requires/purchase_requisition_controller', document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_stor','stor_td');\n";
	  echo "document.getElementById('cbo_store_name').value				= '".$row[csf("store_name")]."';\n";
	  echo "document.getElementById('cbo_pay_mode').value				= '".$row[csf("pay_mode")]."';\n"; 	
	  echo "document.getElementById('cbo_source').value					= '".$row[csf("source")]."';\n"; 
	  echo "document.getElementById('cbo_currency_name').value			= '".$row[csf("cbo_currency")]."';\n";
	  echo "document.getElementById('txt_date_delivery').value			= '".change_date_format($row[csf("delivery_date")])."';\n"; 
	  echo "document.getElementById('txt_remarks').value				= '".$row[csf("remarks")]."';\n";
	  /*---------additional code----------------*/

	 /* echo "document.getElementById('txt_brand').value				= '".$row[csf("brand_name")]."';\n";

	  echo "document.getElementById('txt_model_name').value				= '".$row[csf("model")]."';\n";

	  echo "$('#cbo_origin').val('".$row[csf("origin")]."');\n";*/


	 



	   echo "document.getElementById('txt_manual_req').value			= '".$row[csf("manual_req")]."';\n";
	  echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";
	  echo "document.getElementById('is_approved').value          		= '".$row[csf("is_approved")]."';\n";	
	  
	  if($row[csf("is_approved")]==1)
	  {
		 echo "$('#approved').text('Approved');\n"; 
	  }
	  else
	  {
		 echo "$('#approved').text('');\n";
	  }
	  
	  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_purchase_requisition',1);\n";	
  }	
}

if ($action=="purchase_manual_requisition_popup")
{
 	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	extract($_REQUEST); 
?>
<script>
	  function js_set_value(manual_req)
	  {
		  document.getElementById('txt_manual_req').value=manual_req;
		  parent.emailwindow.hide();
	  }
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="purchaserequisition_6"  id="purchaserequisition_6" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Item Category</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>            
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="txt_manual_req">
							<? 
								echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, ""); 
                            ?>
                    	</td>
                   		<td>
							<? 
								echo create_drop_down( "cbo_item_category_id", 170,$item_category,"", 1, "-- Select --", $selected, "","","","","","1,2,3,4,12,13,14");   	 
                            ?>
                        </td>
                    	<td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 	</td> 
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'manual_purchase_requisition_list_view', 'search_div1', 'purchase_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                       </td>
        			</tr>
             	</table>
          	</td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
				<? echo load_month_buttons(1);  ?>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top" id="search_div1"> 
            </td>
        </tr>
    </table>    
    </form>
   </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="manual_purchase_requisition_list_view")
{	
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $item_category_id=" and item_category_id='$data[1]'"; else $item_category_id="";//{ echo "Please Select item category."; die; }
	 if($db_type==2)
	 {
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], 'mm-dd-yyyy','/',1)."' and '".change_date_format($data[3],'mm-dd-yyyy','/',1)."'"; else $order_rcv_date ="";
	 }
	 if($db_type==0)
	 {
	if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = " and requisition_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $order_rcv_date ="";
	 }
	
	 $sql= "select id,requ_prefix_num,requisition_date,company_id,item_category_id,location_id,department_id,section_id,manual_req from inv_purchase_requisition_mst where status_active=1 and is_deleted=0 $company  $item_category_id $order_rcv_date order by id asc";
	 
	$company=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	
	$arr=array (2=>$company,3=>$item_category,4=>$location,5=>$department,6=>$section);
	
	echo  create_list_view("list_view", "Requisition No,Requisition Date,Company,Item Category,Location,Department,Section,Manual Req", "80,80,100,100,100,90,90,80","850","250",0, $sql , "js_set_value", "manual_req", "",1,"0,0,company_id,item_category_id,location_id,department_id,section_id,0", $arr , "requ_prefix_num,requisition_date,company_id,item_category_id,location_id,department_id,section_id,manual_req","purchase_requisition_controller","",'0,3,0,0,0,0,0,0') ;	
	exit(); 
}

if ($action=="item_category_details")
{
	//echo $data;die;
	/*if($data==5 || $data==6 || $data==7 || $data==8 || $data==9 || $data==10 || $data==11)
	{*/
		$nameArray=sql_select( "select id,user_given_code_status from variable_settings_inventory where item_category_id='$data' and user_given_code_status=1 and status_active=1 and is_deleted=0" );
		foreach ($nameArray as $row)
	 	{
			$user_given_code_status=$row[csf('user_given_code_status')];
		}
		
		
?>
 
<table class="rpt_table" width="1090px" cellspacing="1" id="tbl_purchase_item">
    <thead>
        <th width="80">Item Account</th>
        <th width="80">Item Group</th>
        <th width="80">Item Sub. Group</th>
        <th width="120">Item Description</th>
        <th width="60">Item Size</th>
        <th width="80">Required For</th>
        <th width="50">Cons UOM</th>
        <th width="50" class="must_entry_caption">Quantity</th>
        <th width="50">Rate</th>
        <th width="55">Amount</th>
        <th width="50">Stock</th>
        <th width="55">Re-Order Level</th>
        <th width="100">Remarks</th>
        <th width="60">Status</th>
         <!-- additional code -->
       <th width="80">Brand</th>
       <th width="80">Model</th>
       <th width="80">Origin</th>
       <!-- <th width="120">Origin</th>
       <th width="120">MOdel</th> -->
    </thead>
    <tbody>
        <tr class="general" >
            <td>
            <?
            if($user_given_code_status==1)
            {
				?>
					<input type="text" name="itemaccount_1" id="itemaccount_1" class="text_boxes" value="" style="width:80px;" maxlength="200" placeholder="Double click"  onDblClick="openmypage()" readonly />
				<? 
            } 
            else
            {
				?>
                    <input type="text" name="itemaccount_1" id="itemaccount_1" class="text_boxes" value="" style="width:80px;" maxlength="200" readonly/>      
				<?
            }
            ?>
            </td>
            <td>
            <?
            if($user_given_code_status==1)
            {
				?>
                    <input type="text" name="txtitemgroupid_1" id="txtitemgroupid_1" class="text_boxes" value="" style="width:80px;" maxlength="200" readonly/>
				<?
            }
            else
            {
				?>
                    <input type="text" name="txtitemgroupid_1" id="txtitemgroupid_1" class="text_boxes" value="" style="width:80px;" maxlength="200" readonly placeholder="Double click"  onDblClick="openmypage()"/>
				<?
            }
            ?>
            </td>
            <td>
                <input type="text" name="sub_group_1" id="sub_group_1" class="text_boxes" value="" style="width:80px;" maxlength="200" readonly />
            </td>
            <td>
                <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:120px;" maxlength="200" readonly />
            </td>


            <td id="group_td">
                <input type="hidden" name="hiddenid_1" id="hiddenid_1" />
                <input type="text" name="itemsize_1" id="itemsize_1" class="text_boxes" value="" style="width:60px;" maxlength="200" readonly />
                <input type="hidden" name="hiddenitemgroupid_1" id="hiddenitemgroupid_1" class="text_boxes" value="" style="width:100px;" maxlength="200" readonly />
            </td>
            <td>
                <!--<input type="text" name="txtreqfor_1" id="txtreqfor_1" class="text_boxes" value="" style="width:70px;" maxlength="200" />-->
                <?php
					echo create_drop_down( "txtreqfor_1", 90, $use_for,'', 1, '-- Select --',0,'',0,''); 
				?>
            </td> 
            <td id="tduom_1">
                <input type="text" name="txtuom_1" id="txtuom_1" class="text_boxes" value="" style="width:50px;" maxlength="200" readonly />
                <input type="hidden" name="hiddentxtuom_1" id="hiddentxtuom_1" class="text_boxes" value="" style="width:60px;" maxlength="200" readonly />
            </td> 
            <td>
                <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:60px;" onKeyUp="calculate_val()"/>
            </td>
            <td>
                <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:50px;" onKeyUp="calculate_val()" />
            </td>
            <td>
                <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:60px; text-align:right;" readonly />
            </td>
            <td>
                <input type="text" name="stock_1" id="stock_1" class="text_boxes_numeric" value="" style="width:60px;" maxlength="200" readonly />
            </td>
            <td><input type="hidden" name="update_id" id="update_id" />
                <input type="text" name="reorderlable_1" id="reorderlable_1" class="text_boxes_numeric" value="" style="width:60px;" maxlength="200" readonly />
            </td>
            <td>
                <input type="text" name="txt_remarks_1" id="txt_remarks_1" class="text_boxes" value="" style="width:95px;" />
            </td>	
            <td> 
                <input type="hidden" name="item_1" id="item_1" value="" />
                <input type="hidden" name="hidden_update_id" id="hidden_update_id" readonly= "readonly" /> <!-- for update --> 
                <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes_numeric"  readonly= "readonly" value="0" />
				<?php echo create_drop_down( "cbostatus_1", 70, $row_status,'', 0, '',1,0); ?> 
            </td>

            <!--  additional code -->

              <!-- <td><Input type="text" name="txt_brand" ID="txt_brand"  style="width:100px" class="text_boxes" value="" autocomplete="off" /></td> -->
              <td><Input type="text" name="txtbrand_1" ID="txtbrand_1"  style="width:80px" class="text_boxes" autocomplete="off" /></td>

              <td><Input type="text" name="txtmodelname_1" ID="txtmodelname_1"  style="width:80px" class="text_boxes" autocomplete="off" /></td>

              <td><? //new
              echo create_drop_down( "cboOrigin_1", 155, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0 );            
              ?></td>

             


               <!-- <td>Origin</td>
              <td><?
              echo create_drop_down( "cbo_origin", 155, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0 );            
              ?></td>-->
                  
              

        </tr>
    </tbody>
    </table>
    <table width="100%">
    	<tr><td>&nbsp;</td></tr>
        <tr>
            <td width="80%" height="20" valign="middle" align="center" class="button_container"> 
                <?
                    echo load_submit_buttons( $permission, "fnc_purchase_requisition_dtls", 0,0 ,"reset_form('purchaserequisition_1*purchaserequisition_2','item_category_div*purchase_requisition_list_view_dtls*approved','','','disable_enable_fields(\'cbo_company_name\');$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();')",2) ;
                ?>
            </td>    
        </tr>				
    </table>
<?
	//}
}


if($action=="account_order_popup")
{
	echo load_html_head_contents("Item Description Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	list($company,$itemCategory,$store_id)=explode('_',$data);
	?>
	<script>
	
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	 function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				 eval($('#tr_'+i).attr("onclick"));  
			}
		}
		
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		str=str[1];
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
		}
		var id = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		
		$('#item_1').val( id );
	} 
	
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Item Group</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">	
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_item_group" id="txt_item_group" />
                        </td> 
                        <td align="center">	
                    		<input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code" />
                        </td> 
                        <td align="center">
                        	 <input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description" />
                        </td>                 
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+'<? echo $itemCategory; ?>'+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+document.getElementById('txt_item_group').value+'**'+'<? echo $store_id; ?>', 'account_order_popup_list_view', 'search_div', 'purchase_requisition_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
            <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if ($action=="account_order_popup_list_view")
{
	echo load_html_head_contents("Item Creation popup", "../../", 1, 1,'','1','');	
	extract($_REQUEST);
	list($company_name,$item_category_id,$item_description,$item_code,$item_group,$store_id)=explode('**',$data);
?>

</head>	
<body>
	<div align="center" style="width:100%" >
	<form name="order_popup_1"  id="order_popup_1">
	<fieldset style="width:1210px"> 
        <input type="hidden" id="item_1" />
     <?
	  if($item_description!=""){$search_con=" and a.item_description like('%$item_description%')";}
	  if($item_code!=""){$search_con .= " and a.item_code like('%$item_code')";}
	  if($item_group!=""){$search_con .= " and b.item_name like('%$item_group%')";}
	  
	  if ($company_name!=0) $company=" and a.company_id='$company_name'"; else { echo "Please Select Company First."; die; }
	  if ($item_category_id!=0) $item_category_list=" and a.item_category_id='$item_category_id'"; else { echo "Please Select Item Category."; die; }
	  $entry_cond="";
	  if(str_replace("'","",$item_category_id)==4) $entry_cond="and a.entry_form=20";
	   
	 /* $arr=array (1=>$item_category,5=>$unit_of_measurement,9=>$row_status);
	  $sql="select a.id,a.item_account,a.item_category_id,a.item_description,a.item_size,a.item_group_id,a.unit_of_measure,a.current_stock,a.re_order_label,a.status_active,b.item_name,
	  sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as balance_stock
	  from lib_item_group b, product_details_master a left join  inv_transaction c on a.id=c.prod_id and c.store_id=$store_id
	  where b.id=a.item_group_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company $search_con $item_category_list $entry_cond 
	  group by a.id,a.item_account,a.item_category_id,a.item_description,a.item_size,a.item_group_id,a.unit_of_measure,a.current_stock,a.re_order_label,a.status_active,b.item_name";
	  //echo $sql;
	   
	  echo  create_list_view ( "list_view","Item Account,Item Category,Item Description,Item Size,Item Group,Order UOM,Stock,ReOrder Level,Product ID,Status", "120,100,140,80,100,80,80,100,50,50","950","250",0, $sql, "js_set_value", "id", "", '', "0,item_category_id,0,0,0,unit_of_measure,0,0,0,status_active", $arr , "item_account,item_category_id,item_description,item_size,item_name,unit_of_measure,balance_stock,re_order_label,id,status_active", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,1,1,1,0','',1 ); */
	  /*-------------additional code---------------------------*/
	  
	  $sql="select a.id,a.item_account,a.item_code,a.origin,a.sub_group_name,a.item_category_id,a.item_description,a.brand_name,a.model,a.item_size,a.item_group_id,a.unit_of_measure,a.current_stock,a.re_order_label,a.status_active,b.item_name,
	  sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as balance_stock
	  from lib_item_group b, product_details_master a left join  inv_transaction c on a.id=c.prod_id and c.store_id=$store_id
	  where b.id=a.item_group_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company $search_con $item_category_list $entry_cond 
	  group by a.id,a.brand_name,a.model,a.item_account,a.item_code,a.origin,a.sub_group_name,a.item_category_id,a.item_description,a.item_size,a.item_group_id,a.unit_of_measure,a.current_stock,a.re_order_label,a.status_active,b.item_name";
	  //echo $sql;die;
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	  $arr=array (2=>$item_category,5=>$origin_lib,10=>$unit_of_measurement,14=>$row_status);  
	  echo  create_list_view ( "list_view","Item Account,Item Code,Item Category,Brand,Model,Origin,Sub Group Name,Item Description,Item Size,Item Group,Order UOM,Stock,ReOrder Level,Product ID,Status", "120,80,100,60 ,70,60,70,140,80,100,80,80,100,50,50","1280","250",0, $sql, "js_set_value", "id", "", '', "0,0,item_category_id,0,0,origin,0,0,0,0,unit_of_measure,0,0,0,status_active", $arr , "item_account,a.item_code,item_category_id,brand_name,model,origin,sub_group_name,item_description,item_size,item_name,unit_of_measure,balance_stock,re_order_label,id,status_active", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,1,1,1,0','',1 );
    ?>
    </fieldset>
    </form>
	 </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>                                  
<? 		
}

if ($action=="load_php_popup_to_form")
{
	$explode_data = explode("**",$data);
	$data=$explode_data[0];
	$table_row=$explode_data[1];
	$item=$explode_data[2];
	$store_id=$explode_data[3];
	
    if($data!="")
	{
		//echo  "select a.id,a.item_account,a.sub_group_name,a.item_category_id,a.item_description,a.item_size,a.item_group_id,a.unit_of_measure,a.current_stock,a.re_order_label,a.status_active,b.item_name from product_details_master a,lib_item_group b where a.id in ($data) and a.status_active=1 and a.item_group_id=b.id";

	/*$nameArray=sql_select( "select a.id, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name,
	sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as balance_stock
	from lib_item_group b, product_details_master a left join  inv_transaction c on a.id=c.prod_id and c.store_id=$store_id
	where a.id in ($data) and a.status_active=1 and a.item_group_id=b.id
	group by a.id, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name");*/
	/*------------additional code-------------*/
	
      $nameArray=sql_select( "select a.id,a.brand_name,a.model,a.origin, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name,
	sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)- (case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as balance_stock
	from lib_item_group b, product_details_master a left join  inv_transaction c on a.id=c.prod_id and c.store_id=$store_id
	where a.id in ($data) and a.status_active=1 and a.item_group_id=b.id
	group by a.id,a.brand_name,a.model,a.origin, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name"); 
	foreach ($nameArray as $inf)
		{
			$table_row++;
			$nameArray=sql_select( "select id,user_given_code_status from variable_settings_inventory where item_category_id='$item' and user_given_code_status=1 and status_active=1 and is_deleted=0" );
			foreach ($nameArray as $row)
			{
				$user_given_code_status=$row[csf('user_given_code_status')];
			}
		?>
			<tr class="general" id="tr_<? echo $table_row; ?>">
				<td>
				<?
				if($user_given_code_status==1)
				{
				?>
				 <input type="text" name="itemaccount_<? echo $table_row; ?>" id="itemaccount_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_account")]; ?>" style="width:70px;" maxlength="200" placeholder="Double click"  onDblClick="openmypage()" readonly />
				<?
				}
				else
				{
				?>
				 <input type="text" name="itemaccount_<? echo $table_row; ?>" id="itemaccount_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_account")]; ?>" style="width:70px;" maxlength="200" />
				<?	
				}
				?>
				</td>
				<td>
				 <?
				if($user_given_code_status==1)
				{
				?>
				<input type="text" name="itemdescription_<? echo $table_row; ?>" id="itemdescription_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_group_id")];?>" style="width:90px;" maxlength="200" readonly/>
				 <?
				}
				else
				{
				?>
				<input type="text" name="txtitemgroupid_<? echo $table_row; ?>" id="txtitemgroupid_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_name")];?>" style="width:90px;" maxlength="200" readonly placeholder="Double click"  onDblClick="openmypage()" />
				<input type="hidden" name="hiddenitemgroupid_<? echo $table_row; ?>" id="hiddenitemgroupid_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_group_id")];?>" style="width:90px;" maxlength="200" readonly placeholder="Double click"  onDblClick="openmypage()"  />
				
				<?	
				}
				?>
                 <input type="hidden" name="hiddenid_<? echo $table_row; ?>" id="hiddenid_<? echo $table_row; ?>" value="" />
				</td>
                <td>
				<input type="text" name="sub_group_<? echo $table_row; ?>" id="sub_group_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("sub_group_name")];?>" style="width:90px;" maxlength="200" readonly />
				</td>
				
				<td id="group_td">
				<input type="text" name="itemdescription_<? echo $table_row; ?>" id="itemdescription_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_description")];?>" style="width:130px;" maxlength="200" readonly />
				<input type="hidden" name="hiddenitemgroupid_<? echo $table_row; ?>" id="hiddenitemgroupid_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_group_id")];?>" style="width:100px;" maxlength="200" readonly />
				</td>
                <td>
				<input type="hidden" name="item_<? echo $table_row; ?>" id="item_<? echo $table_row; ?>" value="<? echo $inf[csf("id")];?>" />
				<input type="text" name="itemsize_<? echo $table_row; ?>" id="itemsize_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_size")];?>" style="width:60px;" maxlength="200" readonly />
				</td>
                <td>
                   <!-- <input type="text" name="txtreqfor_<?// echo $table_row; ?>" id="txtreqfor_<?// echo $table_row; ?>" class="text_boxes" value="<? //echo $inf[csf("unit_of_measure")];?>" style="width:70px;" maxlength="200" />-->
                <?php
					echo create_drop_down( "txtreqfor_".$table_row, 90, $use_for,'', 1, '-- Select --',0,'',0,''); 
				?>    
                </td> 
				<td id="tduom_1"><!--$unit_of_measurement[]-->
				<input type="text" name="txtuom_<? echo $table_row; ?>" id="txtuom_<? echo $table_row; ?>" class="text_boxes" value="<? echo $unit_of_measurement[$inf[csf("unit_of_measure")]];?>" style="width:50px;" maxlength="200" readonly />
				 <input type="hidden" name="hiddentxtuom_<? echo $table_row; ?>" id="hiddentxtuom_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("unit_of_measure")];?>" style="width:55px;" maxlength="200" readonly />
				</td> 
				<td>
				<input type="text" name="quantity_<? echo $table_row; ?>" id="quantity_<? echo $table_row; ?>" class="text_boxes_numeric" autocomplete="off" value="" style="width:50px;" onKeyUp="calculate_val()"/>
				</td>
				<td>
				<input type="text" name="rate_<? echo $table_row; ?>" id="rate_<? echo $table_row; ?>" class="text_boxes_numeric" autocomplete="off" value="" style="width:50px;" onKeyUp="calculate_val()"/>
				</td>
				<td>
				<input type="text" name="amount_<? echo $table_row; ?>" id="amount_<? echo $table_row; ?>" class="text_boxes_numeric" autocomplete="off" value="" style="width:50px;" readonly/>
				</td>
				<td>
				<input type="text" name="stock_<? echo $table_row; ?>" id="stock_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? echo $inf[csf("balance_stock")];?>" style="width:50px;" maxlength="200" readonly />
				</td>
				<td>
				<input type="text" name="reorderlable_<? echo $table_row; ?>" id="reorderlable_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? echo $inf[csf("re_order_label")];?>" style="width:60px;" maxlength="200" readonly />
				</td>
                <td>
                	<input type="text" name="txt_remarks_<? echo $table_row; ?>" id="txt_remarks_<? echo $table_row; ?>" class="text_boxes" value="" style="width:95px;" /> 
				</td>	
				<td> 
				<?php 
				echo create_drop_down( "cbostatus_".$table_row, 60, $row_status,'', '', '',$inf[csf("status_active")],'',0,''); 
				?> 
				</td>
                <!--   additional code -->
				<td>
				<input type="text" name="txtbrand_<? echo $table_row; ?>" id="txtbrand_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("brand_name")];?>" style="width:100px;" maxlength="200" />
				</td>

				<td>
				<input type="text" name="txtmodelname_<? echo $table_row; ?>" id="txtmodelname_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("model")];?>" style="width:100px;" maxlength="200" />
				</td>

				<td >	
                     <?php //new
                       echo create_drop_down( "cboOrigin_".$table_row, 155, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', $inf[csf("origin")] );  ?>
				</td>

			</tr>
			<?
		}
	}
	exit();
}

if ($action=="purchase_requisition_list_view_dtls")
{
	$arr=array (5=>$unit_of_measurement,12=>$row_status); 
	$sql="select a.item_account,a.item_description,a.item_size,a.item_group_id,b.required_for,a.unit_of_measure,a.re_order_label,b.id,b.quantity,b.rate,b.amount,b.stock,b.status_active,b.remarks,c.item_name from product_details_master a,inv_purchase_requisition_dtls b, lib_item_group c where b.is_deleted=0 and b.mst_id='$data' and a.id=b.product_id and a.item_group_id=c.id";
	  
	echo create_list_view ("list_view","Item Account,Item Description,Item Size,Item Group,Required For,Cons UOM,Quantity,Rate,Amount,Stock,Re-Order Level,Remarks,Status", "110,130,110,110,70,70,70,70,70,70,70,90,70","1160","300",0, $sql, "get_php_form_data", "id", "'order_details_form_data'", '', "0,0,0,0,0,unit_of_measure,0,0,0,0,0,0,status_active", $arr , "item_account,item_description,item_size,item_name,required_for,unit_of_measure,quantity,rate,amount,stock,re_order_label,remarks,status_active", "requires/purchase_requisition_controller", '','0,0,0,0,0,0,2,2,2,2,2,0,0','',0 ); 

	
	
	exit();
}

if ($action=="order_details_form_data")
{
	/*$nameArray=sql_select( "select a.id,a.required_for,a.quantity,a.rate,a.amount,a.stock,a.remarks,a.status_active,b.item_account,b.item_description,b.item_size,b.item_group_id,b.unit_of_measure,b.re_order_label,c.item_name from inv_purchase_requisition_dtls a,product_details_master b,lib_item_group c where a.id='$data' and a.is_deleted=0 and a.product_id=b.id and b.item_group_id=c.id" );*/

	/*------------ADDITIONAL CODE----------------*/
	$nameArray=sql_select( "select a.id,a.brand_name,a.origin,a.model,a.required_for,a.quantity,a.rate,a.amount,a.stock,a.remarks,a.status_active,b.item_account,b.item_description,b.item_size,b.item_group_id,b.unit_of_measure,b.re_order_label,c.item_name from inv_purchase_requisition_dtls a,product_details_master b,lib_item_group c where a.id=$data and a.is_deleted=0 and a.product_id=b.id and b.item_group_id=c.id" );
	  
	foreach ($nameArray as $row)
	{	
		/*echo "document.getElementById('itemaccount_1').value 				= '".$row[csf("item_account")]."';\n";  
		echo "document.getElementById('itemdescription_1').value 			= '".$row[csf("item_description")]."';\n";  
 		echo "document.getElementById('itemsize_1').value 				= '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('txtitemgroupid_1').value			= '".$row[csf("item_name")]."';\n";
		echo "document.getElementById('hiddenitemgroupid_1').value		= '".$row[csf("item_group_id")]."';\n";
		echo "document.getElementById('txtreqfor_1').value				= '".$row[csf("required_for")]."';\n";
		echo "document.getElementById('txtuom_1').value					= '".$unit_of_measurement[$row[csf("unit_of_measure")]]."';\n";
		echo "document.getElementById('hiddentxtuom_1').value				= '".$row[csf("unit_of_measure")]."';\n"; 
		echo "document.getElementById('quantity_1').value					= '".$row[csf("quantity")]."';\n";
		echo "document.getElementById('rate_1').value						= '".$row[csf("rate")]."';\n"; 	
		echo "document.getElementById('amount_1').value					= '".$row[csf("amount")]."';\n";
		echo "document.getElementById('txt_remarks_1').value				= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('stock_1').value					= '".$row[csf("stock")]."';\n"; 	
		echo "document.getElementById('reorderlable_1').value				= '".$row[csf("re_order_label")]."';\n"; 
		echo "document.getElementById('cbostatus_1').value				= '".$row[csf("status_active")]."';\n";
 		echo "document.getElementById('txtbrand_1').value						= '".$row[csf("brand_name")]."';\n";
 		echo "$('#cboOrigin_1').val('".$row[csf("origin")]."');\n";
		echo "document.getElementById('txtmodelname_1').value						= '".$row[csf("model")]."';\n";
 		echo "document.getElementById('hiddenid_1').value          		= '".$row[csf("id")]."';\n";*/
		echo "$('#itemaccount_1').val('".$row[csf('item_account')]."');\n";
		echo "$('#itemdescription_1').val('".$row[csf('item_description')]."');\n";
		echo "$('#itemsize_1').val('".$row[csf('item_size')]."');\n";
		echo "$('#txtitemgroupid_1').val('".$row[csf('item_name')]."');\n";
		echo "$('#hiddenitemgroupid_1').val('".$row[csf('item_group_id')]."');\n";
		echo "$('#txtreqfor_1').val('".$row[csf('required_for')]."');\n";
		echo "$('#txtuom_1').val('".$unit_of_measurement[$row[csf('unit_of_measure')]]."');\n";
		echo "$('#hiddentxtuom_1').val('".$row[csf('unit_of_measure')]."');\n";
		echo "$('#quantity_1').val('".$row[csf('quantity')]."');\n";
		echo "$('#rate_1').val('".$row[csf('rate')]."');\n";
		echo "$('#amount_1').val('".$row[csf('amount')]."');\n";
		echo "$('#txt_remarks_1').val('".$row[csf('remarks')]."');\n";
		echo "$('#stock_1').val('".$row[csf('stock')]."');\n";
		echo "$('#reorderlable_1').val('".$row[csf('re_order_label')]."');\n";
		echo "$('#cbostatus_1').val('".$row[csf('status_active')]."');\n";
		echo "$('#txtbrand_1').val('".$row[csf('brand_name')]."');\n";
		echo "$('#txtmodelname_1').val('".$row[csf('model')]."');\n";

		echo "$('#cboOrigin_1').val('".$row[csf("origin")]."');\n";


 		echo "$('#hiddenid_1').val('".$row[csf('id')]."');\n";
 		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_purchase_requisition_dtls',2);\n";
 	}	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here=======================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	  
	 /* $s=check_table_status( $_SESSION['menu_id'], 0 );
	  echo "10**0**".$s; die;*/
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) 
		{ 
			echo "15**0"; disconnect($con);die;
		}
	 
		if($db_type==0)
		{
			$new_requ_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '','RQSN', date("Y",time()), 5, "select requ_no_prefix,requ_prefix_num from inv_purchase_requisition_mst where company_id=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." and entry_form=69 order by id desc ", "requ_no_prefix", "requ_prefix_num" ));
		}
	    else if($db_type==2)
		{
			$new_requ_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '','RQSN', date("Y",time()), 5, "select requ_no_prefix,requ_prefix_num from inv_purchase_requisition_mst where company_id=$cbo_company_name and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." and entry_form=69 order by id desc ", "requ_no_prefix", "requ_prefix_num" ));
		}
				  
		$id=return_next_id("id","inv_purchase_requisition_mst",1);
		$field_array="id,entry_form,requ_no,requ_no_prefix,requ_prefix_num,company_id,item_category_id,location_id,division_id,department_id,section_id,requisition_date,store_name,pay_mode,source,cbo_currency,delivery_date,remarks,manual_req,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id.",69,'".$new_requ_no[0]."','".$new_requ_no[1]."',".$new_requ_no[2].",".$cbo_company_name.",".$cbo_item_category_id.",".$cbo_location_name.",".$cbo_division_name.",".$cbo_department_name.",".$cbo_section_name.",".$txt_date_from.",".$cbo_store_name.",".$cbo_pay_mode.",".$cbo_source.",".$cbo_currency_name.",".$txt_date_delivery.",".$txt_remarks.",".$txt_manual_req.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		/*----------additional code-------------------*/
		/*$field_array="id,entry_form,requ_no,requ_no_prefix,requ_prefix_num,company_id,item_category_id,location_id,division_id,department_id,section_id,requisition_date,store_name,pay_mode,source,cbo_currency,delivery_date,remarks,brand_name,model,origin,manual_req,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id.",69,'".$new_requ_no[0]."','".$new_requ_no[1]."',".$new_requ_no[2].",".$cbo_company_name.",".$cbo_item_category_id.",".$cbo_location_name.",".$cbo_division_name.",".$cbo_department_name.",".$cbo_section_name.",".$txt_date_from.",".$cbo_store_name.",".$cbo_pay_mode.",".$cbo_source.",".$cbo_currency_name.",".$txt_date_delivery.",".$txt_remarks.",".$txt_brand.",".$cbo_origin.",".$txt_model_name.",".$cbo_origin.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";*/
		// echo "insert into inv_purchase_requisition_mst (".$field_array.") values ".$data_array;die;

		$rID=sql_insert("inv_purchase_requisition_mst",$field_array,$data_array,0);
		 
		if($db_type==0)
		{
			if($rID){
			 	mysql_query("COMMIT"); 
			  	echo "0**".str_replace("'", '', $new_requ_no[0])."**".$id; 
			}
			else{
			  	mysql_query("ROLLBACK"); 
			  	echo "10**".$id;
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
			 	oci_commit($con);
			 	echo "0**".str_replace("'", '', $new_requ_no[0])."**".$id; 
			}
			else
			{
				oci_rollback($con);
			 	echo "10**".$id;
			}
		}
		
		check_table_status( $_SESSION['menu_id'],0);
		// if($db_type==2) {oci_commit($con);}
		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here==================================================================
	{
		$con = connect();
		if($db_type==0)
	  	{
			mysql_query("BEGIN");
	  	}
		
	  	$field_array="company_id*item_category_id*location_id*division_id*department_id*section_id*requisition_date*store_name*pay_mode*source*cbo_currency*delivery_date*remarks*manual_req*updated_by*update_date";
	  
	  	$data_array="".$cbo_company_name."*".$cbo_item_category_id."*".$cbo_location_name."*".$cbo_division_name."*".$cbo_department_name."*".$cbo_section_name."*".$txt_date_from."*".$cbo_store_name."*".$cbo_pay_mode."*".$cbo_source."*".$cbo_currency_name."*".$txt_date_delivery."*".$txt_remarks."*".$txt_manual_req."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
	  	/*---------additional code---------------*/

	  	/*$field_array="company_id*item_category_id*location_id*division_id*department_id*section_id*requisition_date*store_name*pay_mode*source*cbo_currency*delivery_date*remarks*brand_name*model*origin*manual_req*updated_by*update_date";
	  
	  	$data_array="".$cbo_company_name."*".$cbo_item_category_id."*".$cbo_location_name."*".$cbo_division_name."*".$cbo_department_name."*".$cbo_section_name."*".$txt_date_from."*".$cbo_store_name."*".$cbo_pay_mode."*".$cbo_source."*".$cbo_currency_name."*".$txt_date_delivery."*".$txt_remarks."*".$txt_brand."*".$txt_model_name."*".$cbo_origin."*".$txt_manual_req."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";*/
	
	  	$rID=sql_update("inv_purchase_requisition_mst",$field_array,$data_array,"id",$update_id,1);

		if($db_type==0)
		  {
			  if($rID)
			  {
				  mysql_query("COMMIT");  
				  echo "1**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
			  else
			  {
				  mysql_query("ROLLBACK"); 
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		 else if($db_type==2)
		  {
			  if($rID)
			  {
				 oci_commit($con);
				 echo "1**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
			  else
			  {
				  oci_rollback($con);
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		  disconnect($con);
		  die;	
	}
	else if ($operation==2)   
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";

		$rID=sql_delete("inv_purchase_requisition_mst",$field_array,$data_array,"id","".$update_id."",1);
		$rID2=sql_update("inv_purchase_requisition_dtls",'status_active*is_deleted','0*1',"mst_id",$update_id,1);
		  if($db_type==0)
		  {
			  if($rID && $rID2)
			  {
				  mysql_query("COMMIT");  
				  echo "2**";
			  }
			  else
			  {
				  mysql_query("ROLLBACK"); 
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		  else if($db_type==2)
		  {
			  if($rID && $rID2)
			  {
				oci_commit($con);
				  echo "2**";
			  }
			  else
			  {
				 oci_rollback($con);
				  echo "10**".str_replace("'",'',$txt_requisition_no)."**".str_replace("'",'',$update_id);
			  }
		  }
		  disconnect($con);
		  die;
	}
}

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	  if ($operation==0)  // Insert Here==================================================================
	  {
		  $con = connect();
		  if($db_type==0)
		  {
			  mysql_query("BEGIN");
		  }
		  
		  /*$id=return_next_id( "id", "inv_purchase_requisition_dtls",1);
		  $field_array ="id,mst_id,product_id,required_for,cons_uom,quantity,rate,amount,stock,remarks,inserted_by,insert_date,status_active,is_deleted";*/
		  /*---------additional code-------------*/
		  $id=return_next_id( "id", "inv_purchase_requisition_dtls",1);
		  $field_array ="id,mst_id,product_id,required_for,cons_uom,quantity,brand_name,model,origin,rate,amount,stock,remarks,inserted_by,insert_date,status_active,is_deleted";
		
		  //$i=1;
		  for($i=1; $i<=$tot_row; $i++)
			  {
				  $item_account="itemaccount_".$i;
				  $item_description="itemdescription_".$i;
				  $item_size="itemsize_".$i;
				  $item_group_id="hiddenitemgroupid_".$i;
				  $txtreqfor="txtreqfor_".$i;
				  //$txt_uom="txtuom_".$i;
				  $hidden_txtuom="hiddentxtuom_".$i;
				  $quantity="quantity_".$i;
				  $rate="rate_".$i;
				  $amount="amount_".$i;
				  $stock="stock_".$i;
				  $reorder_lable="reorderlable_".$i;
				  $txt_remarks="txt_remarks_".$i;
				  $cbo_status="cbostatus_".$i;
				  /*additional code*/
				  $txt_brand="txtbrand_".$i;
				  $txt_model_name="txtmodelname_".$i;
				  $cbo_origin="cboOrigin_".$i;

				 // echo $cbo_origin;die;

				  $item_id="item_".$i;
				  
				if($$quantity!="")
				{
				  if ($i!=1) $data_array .=",";
				  /*$data_array .="(".$id.",".$update_id.",".$$item_id.",".$$txtreqfor.",".$$hidden_txtuom.",".$$quantity.",".$$txt_brand.",".$$txt_model_name.",".$$rate.",".$$amount.",".$$stock.",".$$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbo_status.",0)";*/
				  $data_array .="(".$id.",".$update_id.",".$$item_id.",".$$txtreqfor.",".$$hidden_txtuom.",".$$quantity.",".$$txt_brand.",".$$txt_model_name.",".$$cbo_origin.",".$$rate.",".$$amount.",".$$stock.",".$$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbo_status.",0)";
				  $id=$id+1;
				  /*additional code*/
				  /*$data_array .="(".$id.",".$update_id.",".$$item_id.",".$$txtreqfor.",".$$hidden_txtuom.",".$$quantity.",".$$txt_brand.",".$$txt_model_name.",".$$rate.",".$$amount.",".$$stock.",".$$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cbo_status.",0)";
				  $id=$id+1;*/
				}
			  }
			// echo "insert into inv_purchase_requisition_dtls (".$field_array.") values ".$data_array; die;
		  $rID=1;
		  $rID=sql_insert("inv_purchase_requisition_dtls",$field_array,$data_array,0);
		  //echo $rID;die;
			  if($db_type==0)
			  {
				  if($rID )
				  {
					  mysql_query("COMMIT");  
					  //echo "0**".$update_id;
					  echo "0**".str_replace("'", '', $update_id);
				  }
				  else
				  {
					  mysql_query("ROLLBACK"); 
					  echo "10**".str_replace("'", '', $update_id);
				  }
			  }
			   if($db_type==2)
			  {
				  if($rID)
				  {
					  
					 oci_commit($con);
					  echo "0**".str_replace("'", '', $update_id);
				  }
				  else
				  {
					 oci_rollback($con);
					  echo "10**".str_replace("'", '', $update_id);
				  }
			  }
			  
			  disconnect($con);
			  die;
	  }
	
	else if ($operation==1)  // Update Here===============================================
	{
		  $con = connect();
		  if($db_type==0)
		  {
			  mysql_query("BEGIN");
		  }
		  //$field_array="item_account*item_description*item_size*item_group*cons_uom*quantity*rate*amount*stock*re_order_level*status_active*updated_by*update_date";
		 // $field_array="required_for*quantity*rate*amount*stock*remarks*status_active*updated_by*update_date";
		  
		  /*additional code*/
		  $field_array="required_for*quantity*brand_name*model*origin*rate*amount*stock*remarks*status_active*updated_by*update_date";
		  
			  $item_account="itemaccount_".$tot_row;
			  $item_description="itemdescription_".$tot_row;
			  $item_size="itemsize_".$tot_row;
			  $item_group_id="hiddenitemgroupid_".$tot_row;
			  $txtreqfor="txtreqfor_".$tot_row;
			  //$txt_uom="txtuom_".$tot_row;
			  $hidden_txtuom="hiddentxtuom_".$tot_row;
			  $quantity="quantity_".$tot_row;
			  $rate="rate_".$tot_row;
			  $amount="amount_".$tot_row;
			  $stock="stock_".$tot_row;
			  $reorder_lable="reorderlable_".$tot_row;
			  $txt_remarks="txt_remarks_".$tot_row;
			  $cbo_status="cbostatus_".$tot_row;
			  /*additional code*/
			  $txt_brand="txtbrand_".$tot_row;
			  $txt_model_name="txtmodelname_".$tot_row;
			  $cbo_origin="cboOrigin_".$tot_row;//new
			  $hiddenid_dtls="hiddenid_".$tot_row;
			  		 // echo "330309   ".$$hiddenid_dtls;die;
			  $hiddDtls=str_replace("'", '', $$hiddenid_dtls);

			  /*$data_array ="".$$txtreqfor."*".$$quantity."*".$$txt_brand."*".$$txt_model_name."*".$$rate."*".$$amount."*".$$stock."*".$$txt_remarks."*".$$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";*/
			  $data_array ="".$$txtreqfor."*".$$quantity."*".$$txt_brand."*".$$txt_model_name."*".$$cbo_origin."*".$$rate."*".$$amount."*".$$stock."*".$$txt_remarks."*".$$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			  /*additional code*/
			  /*$data_array ="".$$txtreqfor."*".$$quantity."*".$$txt_brand."*".$$txt_model_name."*".$$rate."*".$$amount."*".$$stock."*".$$txt_remarks."*".$$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";*/
			  $rID=1;
			  $rID=sql_update("inv_purchase_requisition_dtls",$field_array,$data_array,"id",$hiddDtls,1);
			//  echo $rID;die;

		  if($db_type==0)
		  {
			  if($rID )
			  {
				  mysql_query("COMMIT"); 
				  echo "1**".str_replace("'", '', $update_id);  
			  }
			  else
			  {
				  mysql_query("ROLLBACK"); 
				  echo "10**".str_replace("'", '', $update_id);
			  }
		  }
		   if($db_type==2)
		  {
			  if($rID)
			  {
				 oci_commit($con);
				  echo "1**".str_replace("'", '', $update_id);  
			  }
			  else
			  {
					oci_rollback($con);
				  echo "10**".str_replace("'", '', $update_id);
			  }
		  }
		  disconnect($con);
		  die;
	}
	else if ($operation==2)   
	{
		$con = connect();
		  if($db_type==0)
		  {
			  mysql_query("BEGIN");
		  }
		  
		  
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";
		$rID=sql_update("inv_purchase_requisition_dtls",$field_array,$data_array,"id",$hiddenid_1,1);
		
		//$rID=sql_update("inv_purchase_requisition_dtls",'status_active*is_deleted','0*1',"id",$hiddenid_1,1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="purchase_requisition_print")
{
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST); 
	$data=explode('*',$data);
	 //print($data[5]);
	$update_id=$data[1];
	?>
	<div id="table_row" style="width:1000px;">
	<?
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	
	$pay_cash=$dataArray[0][csf('pay_mode')];
	
	
	if ($data[4]!=3)
	{
		
		if ($data[4]==4)
		{
			$widths=130;
			$th_span=2;	
		}
		elseif ($data[4]==2 && $pay_cash==4)
		{
			$widths=130;
			$th_span=2;
		}
		else
		{
			$widths=0;
			$th_span=0;
		}
		
		?>
		<table width="1000" align="right">
            <tr class="form_caption">
            <td style="font-size:22px; margin-bottom:50px;">&nbsp; </td>
            	<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
				<?
                $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
                </td>
                <td colspan="5" align="center" style="font-size:18px;">  
                <?
				
				echo show_company($data[0],'',''); //Aziz
                /*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
                foreach ($nameArray as $result)
                { 
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?> 
					Road No: <? echo $result[csf('road_no')]; ?> 
					Block No: <? echo $result[csf('block_no')];?> 
					City No: <? echo $result[csf('city')];?> 
					Zip Code: <? echo $result[csf('zip_code')]; ?> 
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
					Email Address: <? echo $result[csf('email')];?> 
					Website No: <? echo $result[csf('website')];
                }*/
                $req=explode('-',$dataArray[0][csf('requ_no')]);
                ?>  
               
                </td> 
            </tr>
            <tr> 
            <td>&nbsp; </td>
            	<td colspan="5" align="center" style="font-size:22px"><strong><u>Store <? echo $data[0] ?></u></strong></td>
            </tr>
            <tr>
                <td width="120" style="font-size:16px"><strong>Req. No:</strong></td>
                <td width="175px" style="font-size:16px"><? echo $req[2].'-'.$req[3]; ?></td>
                <td width="130" style="font-size:16px"><strong>Item Catg:</strong></td> <td width="175px" style="font-size:20px"><? echo $item_category[$dataArray[0][csf('item_category_id')]]; ?></td>
                <td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:16px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
                <td style="font-size:16px"><strong>Department:</strong></td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
                <td style="font-size:16px"><strong>Section:</strong></td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>
            <tr>
            	<td style="font-size:16px"><strong>Req. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
                <td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
                <td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                <td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
                <td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
            </tr>
            <tr>
                <td style="font-size:16px"><strong>Remarks:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
                <td></td> <td></td>
                <td></td><td></td>
            </tr>
		</table>
		<br>
        <?
		//$margin='-133px;';
		
		//echo $th_span.'='.$cash_span.'='.$span.'='.$margin.'='.$widths.'='.$cash;
		?>
        
		<table cellspacing="0" width="<? echo $cash+1100; ?>"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <th colspan="<? echo $th_span+11; ?>" width="<? echo $cash+890; ?>" align="center" ><strong>Item Details</strong></th>
                    <th width="80" align="center" style="font-size:16px" rowspan="2"><strong>Last Req. Info (Date+Qty)</strong></th>
                    <th rowspan="2" style="font-size:16px">Remarks</th> 
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="50">Item Code</th>
                    <th width="150">Item Group</th>
                    <th width="200">Item Des.</th>
                    <th width="70">Req. For</th>
                    <th width="50">UOM</th>
                    <th width="60">Req. Qty.</th>
                    <? 
					if ($data[4]==4)
                    {
						?>
						<th width="60">Rate</th>
						<th width="70">Amount</th>  
						<?
                    }
                    if ($data[4]==2 && $pay_cash==4)
                    {
						?>
						<th width="50">Rate</th>
						<th width="70">Amount</th>
						<?
                    }
                    ?>
                    <th width="70">Stock</th>                  
                    <th width="80">Last Rec. Date</th>
                    <th width="60">Last Rec. Qty.</th>
                    <th width="70">Last Rate</th>
                </tr>
            </thead>
            <tbody>
			<?
            $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
            $receive_array=array();
            /*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];			
				//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
            }*/
			 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];			
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
            }
            
            $i=1;
            // echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
            $sql_result= sql_select(" select a.id, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  ");
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row[csf('quantity')];
				$quantity_sum += $quantity;
				$amount=$row[csf('amount')];
				$amount_sum += $amount;
				
				$current_stock=$row[csf('stock')];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);
				
				if ($data[4]==1)
				{
					$item_code=$row[csf('item_account')];
				}
				else
				{
					$item_account=explode('-',$row[csf('item_account')]);
					$item_code=$item_account[3];
				}
				/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
				$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
				
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:20px">
                    <td><? echo $i; ?></td>
                    <td><div style="word-wrap:break-word:50px;"><? echo $item_code; ?></div></td>
                    <td><p><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                    <td><p>
					<? 
					echo $row[csf('item_description')]; 
					if($row[csf('item_description')]!="" && $row[csf('item_size')]!="") echo ', '; 
					echo $row[csf('item_size')]; 
					?></td>
                    <td><p><? echo $use_for[$row[csf('required_for')]]; ?>&nbsp;</p></td>
                    <td><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo $row[csf('quantity')]; ?>&nbsp;</p></td>
                    <?
					if ($data[4]==4)
                    {
						?>
                         <td align="right"><? echo $row[csf('rate')]; ?></td>
                         <td align="right"><? echo $row[csf('amount')]; ?></td>   
                        <?
					}
					
                    if ($data[4]==2 && $pay_cash==4)
                    {
						?>
						<td align="right"><? echo $row[csf('rate')]; ?></td>
						<td align="right"><? echo $row[csf('amount')]; ?></td>
						<? 
                    } 
					?>
                    
                    <td align="right"><p><? echo number_format($row[csf('stock')],2); ?>&nbsp;</p></td>
                    <td align="center"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo $last_rec_rate;//$last_req_info[2]; ?>&nbsp;</p></td>
                    <td align="center"><p>
                    <? 
                    if(trim($last_req_info[0])!="0000-00-00" && trim($last_req_info[0])!="") echo change_date_format($last_req_info[0]).'<br>'; else echo "&nbsp;<br>";
                    echo $last_req_info[1];
                    ?>
                    &nbsp;</p></td>
                    <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
				</tr>
				<?
				$last_qnty +=$last_rec_qty;
				$total_amount+=$row[csf('amount')];
				$i++;
			}
			?>
            </tbody>
            <tr bgcolor="#dddddd">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong>Total : </strong></td>
                <?
				if ($data[4]==4)
                { 
					?>
					<td align="right"></td>
					<td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
					<?
				}
                if ($data[4]==2 && $pay_cash==4)
                {
					?>
                    <td align="right" ><? //echo number_format($current_stock_sum,0,'',','); ?></td>
                    <td align="right" ><? echo number_format($total_amount,0,'',','); ?></td>
                    <?
                } 
				?>
                <td align="right" ><? echo number_format($current_stock_sum,2); ?></td>
                <td align="right" ></td>
                <td align="right"><? echo number_format($last_qnty,0,'',','); ?></td>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
			
		</table>
		<br>
		<? 
		$report_width= $cash+1050;
		echo signature_table(25, $data[0], $report_width."px"); ?>
		<?
	}
	else
	{
		/*if ($data[4]==1)
		{
			//$cash_span=2;//$cash=120;
			$display="";$span=11;
			if($pay_cash!=4)
			{
				$cash=100;
				//$cash_span=2;	
			}
			else
			{
				$cash="100";
				//$cash_span="";	
			}
		}
		else
		{
			if($data[4]==3)
			{
				if($data[5]==1)// Item Show
				{
					//echo $data[5];
					$display_col="";
					$width_col=150;
					$span=1;
					
				}
				else //// Not Item Show
				{
					$display_col="display:none";
					$width_col='';
					$span='';
					
				}
				
			}
			else
			{
			
				$display="display:none";
				$span=10;
				if($pay_cash==4)
				{
				$cash=100;
				$cash_span=2;	
				}
				else
				{
				$cash=100;
				$cash_span="";	
				}
			}
		}*/
		
		if($data[5]==1)
		{
			$display_col="";
			$width_col=150;
			$span=1;
		}
		else
		{
			$display_col="display:none";
			$width_col='';
			$span='';
		}
		
		?>
		<table width="970" style=" margin-right:20px;">
            <tr class="form_caption">
            	<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
				<?
                $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
                </td>
                <td colspan="5" align="center" style="font-size:14px">  
                <?
                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
                foreach ($nameArray as $result)
                { 
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?> 
					Road No: <? echo $result[csf('road_no')]; ?> 
					Block No: <? echo $result[csf('block_no')];?> 
					City No: <? echo $result[csf('city')];?> 
					Zip Code: <? echo $result[csf('zip_code')]; ?> 
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
					Email Address: <? echo $result[csf('email')];?> 
					Website No: <? echo $result[csf('website')];
                }
                $req=explode('-',$dataArray[0][csf('requ_no')]);
                ?>   
                </td> 
            </tr>
            <tr> 
            	<td colspan="6" align="center" style="font-size:18px"><strong><u>Store <? echo $data[2] ?></u></strong></td>
            </tr>
            <tr>
                <td width="120"><strong>Req. No:</strong></td><td width="175"><? echo $req[2].'-'.$req[3]; ?></td>
                <td width="130"><strong>Item Catg:</strong></td> <td width="175"><? echo $item_category[$dataArray[0][csf('item_category_id')]]; ?></td>
                <td width="125"><strong>Source:</strong></td><td width="175"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Manual Req.:</strong></td> <td ><? echo $dataArray[0][csf('manual_req')]; ?></td>
                <td><strong>Department:</strong></td><td ><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
                <td><strong>Section:</strong></td><td ><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>
            <tr>
            	<td><strong>Req. Date:</strong></td><td><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
                <td><strong>Store Name:</strong></td><td><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
                <td><strong>Pay Mode:</strong></td><td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Location:</strong></td> <td><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                <td><strong>Currency:</strong></td> <td><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
                <td><strong>Del. Date:</strong></td><td><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
            </tr>
            <tr>
                <td><strong>Remarks:</strong></td> <td><? echo $dataArray[0][csf('remarks')]; ?></td>
                <td></td> <td></td>
                <td></td><td></td>
            </tr>
		</table>
		<br>
		<table width="<? echo $width_col+970; ?>" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th colspan="<? echo $span+12; ?>" align="center" >Item Details</th>
                    <th width="70" align="center" style="font-size:11px" rowspan="2">Avg. Monthly issue</th>
                    <th rowspan="2" style="font-size:12px">Avg. Monthly Rec.</th> 
                </tr>
                <tr style="font-size:12px">
                    <th width="30">SL</th>
                    <th width="50">Item Code</th>
                    <th width="100" style=" <? echo $display_col; ?> ">Item Group</th>
                    <th width="180">Item Des.</th>
                    <th width="70">Req. For</th>
                    <th width="50">UOM</th>
                    <th width="60">Req. Qty.</th>
                    <th width="50">Rate</th>
                    <th width="70">Amount</th>
                    <th width="70">Stock</th>                  
                    <th width="70">Last Rec. Date</th>
                    <th width="70">Last Rec. Qty.</th>
                    <th width="50">Last Rate</th>
                </tr>
            </thead>
			<?
            $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
            $receive_array=array();
           /* $rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
            }*/
			 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];			
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
            }
            if($db_type==2)
			{ 
				$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'"; 
			}
			elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";
			
			//echo "select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id";die;
			$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_issue_data=array();
			foreach($issue_sql as $row)
			{
				$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
			}
			
			//var_dump($prev_issue_data);die;
			
			$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_receive_data=array();
			foreach($receive_sql as $row)
			{
				$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
			}
			
            $i=1;
			
            // echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
            $sql_result= sql_select(" select a.id, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label, c.item_code
			from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
			where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  ");
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row[csf('quantity')];
				$quantity_sum += $quantity;
				$amount=$row[csf('amount')];
				$amount_sum += $amount;
				
				$current_stock=$row[csf('stock')];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);
				$last_rec_qty=0;
				$item_code=$row[csf('item_code')];
				/*if ($data[4]==1)
				{
					$item_code=$row[csf('item_account')];
				}
				else
				{
					$item_account=explode('-',$row[csf('item_account')]);
					$item_code=$item_account[3];
				}*/
				/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
				$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td><? echo $item_code; ?></td>
                    <td style=" <? echo $display_col; ?> "><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                    <td>
					<? 
					echo $row[csf('item_description')]; 
					if($row[csf('item_description')]!="" && $row[csf('item_size')]!="") echo ', '; 
					echo $row[csf('item_size')]; 
					?></td>
                    <td><? echo $row[csf('required_for')]; ?></td>
                    <td><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                    <td align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('stock')],2); ?></td>
                    <td><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></td>
                    <td align="right"><? echo number_format($last_rec_qty,2); ?></td>
                    <td align="right"><? echo $last_rec_rate;//$last_req_info[2]; ?></td>
                    <td align="right">
                    <? 
				    $min_issue_date=$prev_issue_data[$row[csf("product_id")]]["transaction_date"];
					$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
					$year_issue_total=$prev_issue_data[$row[csf("product_id")]]["isssue_qty"];
					$issue_avg=$year_issue_total/$month_issue_diff;
					echo number_format($issue_avg,2);
					//echo $row[csf("product_id")];
                    ?>
                    </td>
                    <td align="right">
					<? 
					$min_receive_date=$prev_receive_data[$row[csf("product_id")]]["transaction_date"];
					$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
					$year_receive_total=$prev_receive_data[$row[csf("product_id")]]["receive_qty"];
					$receive_avg=$year_receive_total/$month_receive_diff;
					echo number_format($receive_avg,2);
					?>
                    </td>
				</tr>
				<?
				$total_last_qnty +=$last_rec_qty;
				$total_req_qnty+=$row[csf('quantity')];
				$total_amount+=$row[csf('amount')];
				$total_stock+=$row[csf('stock')];
				
				$i++;
			}
		$currency_id=$dataArray[0][csf('cbo_currency')];
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
            <tfoot>
            	<tr>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th style=" <? echo $display_col; ?> ">&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total:</th>
                    <th ><? echo number_format($total_req_qnty,0,'',','); ?></th>
                    <th >&nbsp;</th>
                    <th ><? echo number_format($total_amount,2);?></th>
                    <th ><? echo number_format($total_stock,2);?></th>                  
                    <th>&nbsp;</th>
                    <th ><? echo number_format($total_last_qnty,2);?></th>
                    <th>&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                </tr>
                 <tr>
                <th colspan="<? echo 14+$span; ?>"  style="border:1px solid black; text-align: center">
                Total Amount (In Word): <? echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency); ?>
                 </th>
             </tr>
            
            </tfoot>
            
            	
		</table>
		<br>
		<?
		if($data[5]==1) $rpt_width=150+970; else  $rpt_width=970;
		
		echo signature_table(25, $data[0], $rpt_width."px"); ?>
		<?
	}
	exit();
}


if($action=="purchase_requisition_print_3")
{
    echo load_html_head_contents("Report Info","../", 1, 1, $unicode,'','');
	extract($_REQUEST); 
	$data=explode('*',$data);
	 //print($data[5]);
	$update_id=$data[1];
	?>
	<div id="table_row" style="width:1000px;">
	<?
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks from inv_purchase_requisition_mst where id=$update_id";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	
	$pay_cash=$dataArray[0][csf('pay_mode')];
	
	
	if ($data[4]!=3)
	{
		
		if ($data[4]==4)
		{
			$widths=130;
			$th_span=2;	
		}
		elseif ($data[4]==2 && $pay_cash==4)
		{
			$widths=130;
			$th_span=2;
		}
		else
		{
			$widths=0;
			$th_span=0;
		}
		
		?>
		<table width="1000" align="right">
            <tr class="form_caption">
            <td style="font-size:22px; margin-bottom:50px;">&nbsp; </td>
            	<td colspan="5" align="center" style="font-size:28px; margin-bottom:50px;"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
				<?
                $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
                </td>
                <td colspan="5" align="center" style="font-size:18px;">  
                <?
				
				//echo show_company($data[0],'',''); //Aziz
                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
                foreach ($nameArray as $result)
                { 
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?> 
					Road No: <? echo $result[csf('road_no')]; ?> 
					Block No: <? echo $result[csf('block_no')];?> 
					City No: <? echo $result[csf('city')];?> 
					Zip Code: <? echo $result[csf('zip_code')]; ?> 
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
					Email Address: <? echo $result[csf('email')];?> 
					Website No: <? echo $result[csf('website')];
                }
                $req=explode('-',$dataArray[0][csf('requ_no')]);
                ?>  
               
                </td> 
            </tr>
            <tr> 
            <td>&nbsp; </td>
            	<td colspan="5" align="center" style="font-size:22px"><strong><u>Store <? echo $data[0] ?></u></strong></td>
            </tr>
            <tr>
                <td width="120" style="font-size:16px"><strong>Req. No:</strong></td>
                <td width="175px" style="font-size:16px"><? echo $req[2].'-'.$req[3]; ?></td>
                <td width="130" style="font-size:16px"><strong>Item Catg:</strong></td> <td width="175px" style="font-size:20px"><? echo $item_category[$dataArray[0][csf('item_category_id')]]; ?></td>
                <td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:16px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
                <td style="font-size:16px"><strong>Department:</strong></td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
                <td style="font-size:16px"><strong>Section:</strong></td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>
            <tr>
            	<td style="font-size:16px"><strong>Req. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
                <td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
                <td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            </tr>
            <tr>
                <td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                <td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
                <td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
            </tr>
            <tr>
                <td style="font-size:16px"><strong>Remarks:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
                <td></td> <td></td>
                <td></td><td></td>
            </tr>
		</table>
		<br>
        <?
		//$margin='-133px;';
		
		//echo $th_span.'='.$cash_span.'='.$span.'='.$margin.'='.$widths.'='.$cash;
		?>
        
		<table cellspacing="0" width="<? echo $cash+1100; ?>"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <th colspan="<? echo $th_span+19; ?>" width="<? echo $cash+890; ?>" align="center" ><strong>Item Details</strong></th>
                     
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="50">Item Code</th>
                    <th width="150">Item Group</th>
                    <th width="150">Sub Group Name</th>
                    <th width="200">Item Des.</th>
                    <th width="200">Brand/Origin/Model</th>
                    <th width="70">Req. For</th>
                    <th width="50">UOM</th>
                    <th width="60">Req. Qty.</th>
                    <? 
                    $data[4]=4;
					if ($data[4]==4)
                    {
						?>
						<th width="60">Rate</th>
						<th width="70">Amount</th>  
						<?
                    }
                    if ($data[4]==2 && $pay_cash==4)
                    {
						?>
						<th width="50">Rate</th>
						<th width="70">Amount</th>
						<?
                    }
                    ?>
                    <th width="70">Stock</th>                  
                    <th width="100">Last Rec. Date</th>
                    <th width="60">Last Rec. Qty.</th>
                    <th width="70">Last Rate</th>
                    <th width="70">Requsition Value</th>
                    <th width="70">Avg. Monthly issue</th>
                    <th width="70">Avg. Monthly Rec.</th>
                    <th width="120">Last Supplier</th>
                </tr>
            </thead>
            <tbody>
			<?
            $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
            $receive_array=array();
            /*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];			
				//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
            }*/
			 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];			
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
				$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
            }
            
            $i=1;
            // echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
            $sql= " select a.id,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  ";
            $sql_result=sql_select($sql);  
           // echo $sql;die;
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row[csf('quantity')];
				$quantity_sum += $quantity;
				$amount=$row[csf('amount')];
				//test 
				$sub_group_name=$row[csf('sub_group_name')];
				$amount_sum += $amount;
				
				$current_stock=$row[csf('stock')];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);
				
				if ($data[4]==1)
				{
					$item_code=$row[csf('item_account')];
				}
				else
				{
					$item_account=explode('-',$row[csf('item_account')]);
					$item_code=$item_account[3];
				}
				/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
				$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
				$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];
				
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:20px">
                    <td><? echo $i; ?></td>
                    <td><div style="word-wrap:break-word:50px;"><? echo $row[csf('item_code')]; ?></div></td>
                    <td><p><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                    <td><p>
					<? 
					echo $row[csf('sub_group_name')]; 
					// if($row[csf('item_description')]!="" && $row[csf('item_size')]!="") echo ', '; 
					// echo $row[csf('item_size')]; 
					?> </p></td>
                    <td><p> <? echo $row[csf("item_description")];?> </p></td>
                    <td><p> B:<? echo $row[csf("brand_name")] ."<br>";?></p><p> O: <? echo $origin_lib[$row[csf("origin")]]."<br>";?></p><p> M: <?echo $row[csf("model")];?> </p></td>
                    <td><p>  <? echo $row[csf("required_for")]; ?></p></td>
                     <td><p>  <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo $row[csf('quantity')]; ?>&nbsp;</p></td>
                    <?
					if ($data[4]==4)
                    {
						?>
                         <td align="right"><? echo $row[csf('rate')]; ?></td>
                         <td align="right"><? echo $row[csf('amount')]; ?></td>   
                        <?
					}
					
                    if ($data[4]==2 && $pay_cash==4)
                    {
						?>
						<td align="right"><? echo $row[csf('rate')]; ?></td>
						<td align="right"><? echo $row[csf('amount')]; ?></td>
						<? 
                    } 
					?>
                    
                    <td align="right"><p><? echo number_format($row[csf('current_stock')],2); ?></p></td>
                    <td align="center"><p><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($last_rec_qty,0,'',','); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo $last_rec_rate;//$last_req_info[2]; ?>&nbsp;</p></td>
                    <td align="right"><p>
                    <? 
                    // if(trim($last_req_info[0])!="0000-00-00" && trim($last_req_info[0])!="") echo change_date_format($last_req_info[0]).'<br>'; else echo "&nbsp;<br>";
                    //echo $last_req_info[1];
                    $reqsit_value="";
                    $reqsit_value=$row[csf('quantity')]*$last_rec_qty;
                    echo $reqsit_value;
                    ?>
                    &nbsp;</p></td>

                    <td align="right">
                    <? 
				    $min_issue_date=$prev_issue_data[$row[csf("product_id")]]["transaction_date"];
					$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
					$year_issue_total=$prev_issue_data[$row[csf("product_id")]]["isssue_qty"];
					$issue_avg=$year_issue_total/$month_issue_diff;
					echo number_format($issue_avg,2);
					//echo $row[csf("product_id")];
                    ?>
                    </td>
                    <td align="right">
					<? 
					$min_receive_date=$prev_receive_data[$row[csf("product_id")]]["transaction_date"];
					$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
					$year_receive_total=$prev_receive_data[$row[csf("product_id")]]["receive_qty"];
					$receive_avg=$year_receive_total/$month_receive_diff;
					echo number_format($receive_avg,2);
					?>
                    </td>
                    <!-- <td><p><? //echo $row[csf('remarks')]; ?>&nbsp;</p></td> -->
                    <td><p><? echo $supplier_array[$last_rec_supp]; ?>&nbsp;</p></td>
                    
				</tr>
				<?
				$last_qnty +=$last_rec_qty;
				$total_amount+=$row[csf('amount')];
				$i++;
			}
			?>
            </tbody>
            <tr bgcolor="#dddddd">
                
                <td align="right" colspan="10"><strong>Total : </strong></td>
                <?
				if ($data[4]==4)
                { 
					?>
					
					<td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
					<?
				}
                if ($data[4]==2 && $pay_cash==4)
                {
					?>
                   
                    <td align="right" ><? echo number_format($total_amount,0,'',','); ?></td>
                    <?
                } 
				?>
				<td align="right" ></td>
                <td align="right" ><? //echo number_format($current_stock_sum,2); ?></td>
                
                <td align="right"><? echo number_format($last_qnty,0,'',','); ?></td>
                <td align="right">&nbsp;</td>
                <td align="right"><?php echo $reqsit_value;?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
<!--                 <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td> -->
			</tr>
			
		</table>
		<br>
		<? 
		$report_width= $cash+1050;
		echo signature_table(25, $data[0], $report_width."px"); ?>
		<?
	}
	else
	{
		/*if ($data[4]==1)
		{
			//$cash_span=2;//$cash=120;
			$display="";$span=11;
			if($pay_cash!=4)
			{
				$cash=100;
				//$cash_span=2;	
			}
			else
			{
				$cash="100";
				//$cash_span="";	
			}
		}
		else
		{
			if($data[4]==3)
			{
				if($data[5]==1)// Item Show
				{
					//echo $data[5];
					$display_col="";
					$width_col=150;
					$span=1;
					
				}
				else //// Not Item Show
				{
					$display_col="display:none";
					$width_col='';
					$span='';
					
				}
				
			}
			else
			{
			
				$display="display:none";
				$span=10;
				if($pay_cash==4)
				{
				$cash=100;
				$cash_span=2;	
				}
				else
				{
				$cash=100;
				$cash_span="";	
				}
			}
		}*/
		
		if($data[5]==1)
		{
			$display_col="";
			$width_col=150;
			$span=1;
		}
		else
		{
			$display_col="display:none";
			$width_col='';
			$span='';
		}
		
		?>
		<table width="970" style=" margin-right:20px;">
            <tr class="form_caption">
            	<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
				<?
                $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
                <td  align="left">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
                </td>
                <td colspan="5" align="center" style="font-size:14px">  
                <?
                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
                foreach ($nameArray as $result)
                { 
					?>
					Plot No: <? echo $result[csf('plot_no')]; ?> 
					Road No: <? echo $result[csf('road_no')]; ?> 
					Block No: <? echo $result[csf('block_no')];?> 
					City No: <? echo $result[csf('city')];?> 
					Zip Code: <? echo $result[csf('zip_code')]; ?> 
					Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
					Email Address: <? echo $result[csf('email')];?> 
					Website No: <? echo $result[csf('website')];
                }
                $req=explode('-',$dataArray[0][csf('requ_no')]);
                ?>   
                </td> 
            </tr>
            <tr> 
            	<td colspan="6" align="center" style="font-size:18px"><strong><u>Store <? echo $data[2] ?></u></strong></td>
            </tr>
            <tr>
                <td width="120"><strong>Req. No:</strong></td><td width="175"><? echo $req[2].'-'.$req[3]; ?></td>
                <td width="130"><strong>Item Catg:</strong></td> <td width="175"><? echo $item_category[$dataArray[0][csf('item_category_id')]]; ?></td>
                <td width="125"><strong>Source:</strong></td><td width="175"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Manual Req.:</strong></td> <td ><? echo $dataArray[0][csf('manual_req')]; ?></td>
                <td><strong>Department:</strong></td><td ><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
                <td><strong>Section:</strong></td><td ><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>
            <tr>
            	<td><strong>Req. Date:</strong></td><td><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
                <td><strong>Store Name:</strong></td><td><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
                <td><strong>Pay Mode:</strong></td><td><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Location:</strong></td> <td><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                <td><strong>Currency:</strong></td> <td><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
                <td><strong>Del. Date:</strong></td><td><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
            </tr>
            <tr>
                <td><strong>Remarks:</strong></td> <td><? echo $dataArray[0][csf('remarks')]; ?></td>
                <td></td> <td></td>
                <td></td><td></td>
            </tr>
		</table>
		<br>
		<table width="<? echo $width_col+970; ?>" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr>
                    <th colspan="<? echo $span+12; ?>" align="center" >Item Details</th>
                    <th width="70" align="center" style="font-size:11px" rowspan="2">Avg. Monthly issue</th>
                    <th rowspan="2" style="font-size:12px">Avg. Monthly Rec.</th> 
                </tr>
                <tr style="font-size:12px">
                    <th width="30">SL</th>
                    <th width="50">Item Code</th>
                    <th width="100" style=" <? echo $display_col; ?> ">Item Group</th>
                    <th width="180">Item Des.</th>
                    <th width="70">Req. For</th>
                    <th width="50">UOM</th>
                    <th width="60">Req. Qty.</th>
                    <th width="50">Rate</th>
                    <th width="70">Amount</th>
                    <th width="70">Stock</th>                  
                    <th width="70">Last Rec. Date</th>
                    <th width="70">Last Rec. Qty.</th>
                    <th width="50">Last Rate</th>
                </tr>
            </thead>
			<?
            $item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
            $receive_array=array();
           /* $rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
            }*/
			 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date, b.cons_quantity as rec_qty,cons_rate as cons_rate, b.supplier_id, from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
            $rec_sql_result= sql_select($rec_sql);
            foreach($rec_sql_result as $row)
            {
				$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
				$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];			
				$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
				$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
            }
            if($db_type==2)
			{ 
				$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'"; 
			}
			elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";
			
			//echo "select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id";die;
			$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_issue_data=array();
			foreach($issue_sql as $row)
			{
				$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
			}
			
			//var_dump($prev_issue_data);die;
			
			$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
			$prev_receive_data=array();
			foreach($receive_sql as $row)
			{
				$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
			}
			
            $i=1;
			
            // echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
            $sql_result= sql_select(" select a.id, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label, c.item_code
			from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
			where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0  ");
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$quantity=$row[csf('quantity')];
				$quantity_sum += $quantity;
				$amount=$row[csf('amount')];
				$amount_sum += $amount;
				
				$current_stock=$row[csf('stock')];
				$current_stock_sum += $current_stock;
				if($db_type==2)
				{
					$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
				}
				if($db_type==0)
				{
					$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
				}
				$last_req_info=explode('_',$last_req_info);
				//print_r($dataaa);
				$last_rec_qty=0;
				$item_code=$row[csf('item_code')];
				/*if ($data[4]==1)
				{
					$item_code=$row[csf('item_account')];
				}
				else
				{
					$item_account=explode('-',$row[csf('item_account')]);
					$item_code=$item_account[3];
				}*/
				/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
				$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
				$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
				$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
				$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td><? echo $item_code; ?></td>
                    <td style=" <? echo $display_col; ?> "><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                    <td>
					<? 
					echo $row[csf('item_description')]; 
					if($row[csf('item_description')]!="" && $row[csf('item_size')]!="") echo ', '; 
					echo $row[csf('item_size')]; 
					?></td>
                    <td><? echo $row[csf('required_for')]; ?></td>
                    <td><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                    <td align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('stock')],2); ?></td>
                    <td><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "&nbsp;";?></td>
                    <td align="right"><? echo number_format($last_rec_qty,2); ?></td>
                    <td align="right"><? echo $last_rec_rate;//$last_req_info[2]; ?></td>
                    <td align="right">
                    <? 
				    $min_issue_date=$prev_issue_data[$row[csf("product_id")]]["transaction_date"];
					$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
					$year_issue_total=$prev_issue_data[$row[csf("product_id")]]["isssue_qty"];
					$issue_avg=$year_issue_total/$month_issue_diff;
					echo number_format($issue_avg,2);
					//echo $row[csf("product_id")];
                    ?>
                    </td>
                    <td align="right">
					<? 
					$min_receive_date=$prev_receive_data[$row[csf("product_id")]]["transaction_date"];
					$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
					$year_receive_total=$prev_receive_data[$row[csf("product_id")]]["receive_qty"];
					$receive_avg=$year_receive_total/$month_receive_diff;
					echo number_format($receive_avg,2);
					?>
                    </td>
				</tr>
				<?
				$total_last_qnty +=$last_rec_qty;
				$total_req_qnty+=$row[csf('quantity')];
				$total_amount+=$row[csf('amount')];
				$total_stock+=$row[csf('stock')];
				
				$i++;
			}
		$currency_id=$dataArray[0][csf('cbo_currency')];
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
            <tfoot>
            	<tr>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th style=" <? echo $display_col; ?> ">&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >Total:</th>
                    <th ><? echo number_format($total_req_qnty,0,'',','); ?></th>
                    <th >&nbsp;</th>
                    <th ><? echo number_format($total_amount,2);?></th>
                    <th ><? echo number_format($total_stock,2);?></th>                  
                    <th>&nbsp;</th>
                    <th ><? echo number_format($total_last_qnty,2);?></th>
                    <th>&nbsp;</th>
                    <th >&nbsp;</th>
                    <th >&nbsp;</th>

                </tr>
                 <tr>
                <th colspan="<? echo 14+$span; ?>"  style="border:1px solid black; text-align: center">
                Total Amount (In Word): <? echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency); ?>
                 </th>
             </tr>
            
            </tfoot>
            
            	
		</table>
		<br>
		<?
		if($data[5]==1) $rpt_width=150+970; else  $rpt_width=970;
		
		echo signature_table(25, $data[0], $rpt_width."px"); ?>
		<?
	}
	exit();
}
?>