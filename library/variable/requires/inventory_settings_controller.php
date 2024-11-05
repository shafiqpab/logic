<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$userid = $_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$rcv_type_arr= array(1=>"Item Level",2=>"WO PI Details Level"); 
$ack_yes_no=array(1=>"No",2=>"Yes");

if($action=="load_drop_down_menu")
{
	echo create_drop_down( "cbo_page_neme", 250, "select m_menu_id,menu_name from main_menu where m_module_id=$data and root_menu>0 and status=1 order by menu_name",'m_menu_id,menu_name', 1, '---- Select ----', "0", "" );
	exit;
}

if($action=="load_drop_down_group")
{
	$data_ref=explode("**",$data);
	echo create_drop_down( "cbo_item_group".$data_ref[1], 170, "select id,item_name from lib_item_group where status_active=1 and item_category=$data_ref[0] order by item_name",'id,item_name', 1, '---- ALL ----', "0", "" );
	exit;
}

if ($action=="on_change_data")
{
	//echo $data; die;
	$explode_data = explode("_",$data);
	$type = $explode_data[0];
	$company_id = $explode_data[1];
	//echo $type."**".$company_id; die;
	if($type==8)  //Inventory_ILE/Landed CostStandard---------------------------------------------
	{
		?>
		<fieldset>
		  <legend>ILE/Landed Cost Standard</legend>
		
		  <table cellpadding="2" cellspacing="0" width="100%" class="rpt_table" id="tbl_variable_list" >
			<thead>
			  <tr>
				<th width="270">Category</th>
				<th width="170" style="display:none;">Item Group</th>
				<th width="270">Source</th>
				<th>Standard %</th>
			  </tr>
			</thead>     
			<tbody>             
			  <?php 
			  $itemGroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 ","id","item_name");
			  $sqlResult= sql_select("select category,item_group,source,standard from variable_inv_ile_standard where company_name='$company_id' and variable_list=8 order by id");
			  $row_count=count($sqlResult);  
			  $i=1;             
			  foreach( $sqlResult as $rows ) 
			  {
				?>
				<tr> 
				  <td align="center">
					<?
					echo create_drop_down( "cbo_category".$i, 170, $item_category,"", 1, "-- Select --", $rows[csf("category")], "load_drop_down( 'requires/inventory_settings_controller', this.value+'**'+".$i.", 'load_drop_down_group', 'tdItemGroup_$i' );",0 );
					?>  
				  </td>
				  <td align="center" id="tdItemGroup_<?=$i;?>" style="display:none;">
					<?
					echo create_drop_down( "cbo_item_group".$i, 170, $itemGroupArr,"", 1, "-- ALL --", $rows[csf("item_group")], "",0 );
					?> 
				  </td>
				  <td align="center">
					<?
					echo create_drop_down( "cbo_source".$i, 170, $source,"", 1, "-- Select --", $rows[csf("source")],"",0 );
					?>
				  </td>
				  <td align="center"><input type="text" name="txt_standard<? echo $i; ?>" id="txt_standard<? echo $i; ?>" value="<? echo $rows[csf("standard")]; ?>" class="text_boxes_numeric" onFocus="add_variable_row(<? echo $i; ?>)"   style="width:160px"/>
				  </td>
				</tr>
				<?
				$i++;
			  }
			  if($row_count==0 || $row_count==""){ //----------for new save--------
			  ?>
			  <tr> 
				<td align="center">
				  <?
				  echo create_drop_down( "cbo_category".$i, 170, $item_category,"", 1, "-- Select --", "", "",0 );
				  ?>  
				</td>
				<td align="center" style="display:none;">
				  <?
				  echo create_drop_down( "cbo_item_group".$i, 170, $itemGroupArr,"", 1, "-- ALL --", "", "",0 );
				  ?> 
				</td>
				<td align="center">
				  <?
				  echo create_drop_down( "cbo_source".$i, 170, $source,"", 1, "-- Select --", 0, "",0 );
				  ?>
				</td>
				<td align="center"><input type="text" name="txt_standard<? echo $i; ?>" id="txt_standard<? echo $i; ?>" class="text_boxes_numeric" onFocus="add_variable_row(<? echo $i; ?>)"   style="width:160px"/></td>
			  </tr>                               
		
			  <? } ?>  
			</tbody>                      	
		  </table>
		  <table>   
			<tr>
			  <td colspan="4">&nbsp;  </td>                                    
			</tr>
			<tr>
			  <td colspan="4" valign="bottom" align="center" class="button_container">
			  <input  type="hidden"name="update_id" id="update_id" value="">
			  <? 
			  if($row_count>0) $flag=1; else $flag=0;
			  echo load_submit_buttons( $permission, "fnc_variable_settings_inventory_ile", $flag,0,"reset_form('inventoryvariablesettings_1','variable_settings_container','')",1);
			  ?>
			  </td>					
			</tr>              
		  </table>    
		</fieldset>    
		<?
		exit();
	}
	else if($type=="10") // Item Rate Manage in MRR------------------------------------------------
	{
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		  <legend>Item Rate Manage in MRR</legend>
			<table cellspacing="0" cellpadding="0" width="700" class="rpt_table" border="1" rules="all">
			  <thead> 
				<th class="must_entry_caption">Item Category</th>
				<th class="must_entry_caption">Rate Optional</th>
				<th class="must_entry_caption">Is Editable</th>
			  </thead>
			  <tr align="center">
				<td>
				  <? 
				  echo create_drop_down( "cbo_category", 150, $item_category ,'', 1, '---- Select ----', "0", "" );
				  ?>
				</td>
				<td id="menu_td">
				  <? 
				  echo create_drop_down( "cbo_rate_optional", 150, $yes_no,'', 1, '---- Select ----', "0", "" );
				  ?>
				</td>
				<td>
				  <? 
				  echo create_drop_down( "cbo_editable", 150, $yes_no,'', 1, '---- Select ----',"0", "" );
				  ?>  
				</td>
			  </tr>
			  <tr>
				<td colspan="4" valign="bottom" align="center" class="button_container">
				  <input  type="hidden" name="update_id" id="update_id" value="">
				  <? 
				  echo load_submit_buttons( $permission, "fnc_variable_settings_inventory", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1);
				  ?>
				</td>         
			  </tr>
			</table>
		  <div id="list_view_con" style="margin-top:15px"> 
			<?
			$company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
			$arr=array (0=>$company_name_arr,1=>$item_category,2=>$yes_no,3=>$yes_no);
		
			echo  create_list_view ( "list_view", "Company,Item Category,Rate Optional, Is Editable","180,180,100,100","630","220",0,"select id,company_name,item_category_id,rate_optional,is_editable from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_item_rate_mrr'",1,"company_name,item_category_id,rate_optional,is_editable",$arr,"company_name,item_category_id,rate_optional,is_editable","../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
			?>  
		  </div>
		</fieldset>     
		<?
		exit();
	}
	else if($type=="16" || $type=="19" || $type=="33" || $type=="34") // User given item code--------------------------------
	{
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		  <legend><? if($type=="16"){echo "User Given Item Code";}else if($type=="33"){echo "Auto Batch No Generate YES/NO";}else if($type=="34"){echo "Change PO to Style Wise YES/NO";} else{echo "Receive Control On Gate Entry";} ?></legend>
		  <table cellspacing="0" cellpadding="0" width="400" class="rpt_table">
			<thead> 
			  <th class="must_entry_caption">Item Category</th>
			  <th class="must_entry_caption"><? if($type=="16"){echo "Code Required";}else if($type=="33"){echo "Generate";}else if($type=="34"){echo "PO to Style Wise";} else {echo "Receive Controll";}?></th>
			</thead>
			  <tr align="center">
			  <td>
				<? 
				if ($type=="33" || $type=="34") 
				{

					echo create_drop_down( "cbo_item_category", 180, $item_category,'', 0, '---- Select ----', "0", "","",3 );
				}
				else
				{
					echo create_drop_down( "cbo_item_category", 180, $item_category,'', 1, '---- Select ----', "0", "" );
				}
				?>
			  </td>
			  <td>
				<? 
				echo create_drop_down( "cbo_item_status", 180, $yes_no,'', 1, '---- Select ----',"0", "" );
				?>	
			  </td>
			</tr>
			<tr>
			  <td colspan="2" valign="bottom" align="center" class="button_container">
				<input  type="hidden"name="update_id" id="update_id" value="">
				<? 
				echo load_submit_buttons( $permission, "fnc_variable_settings_inventory", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1);
				?>
			  </td>					
			</tr>
		  </table>
		
		<div id="list_view_con" style="margin-top:15px"> 
		  <?
		  $company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
		  $arr=array (0=>$company_name_arr,1=>$item_category,2=>$yes_no);
		  echo  create_list_view ( "list_view", "Company Name,Item Category,Status", "150,150,150","470","220",0, "select id,company_name,item_category_id,user_given_code_status from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_form'",1,"company_name,item_category_id,user_given_code_status", $arr , "company_name,item_category_id,user_given_code_status", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
		  ?>  
		  </div>
		</fieldset>		  
		<?
		exit();
	} // ---- User given item code END ------------
	else if($type==17)
	{	 
		//Book keeping mmethod---------------------------------------------------------------------------------
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		  <legend>Book Keeping Method</legend>
			<table cellspacing="0" cellpadding="0" width="400" class="rpt_table">
			  <thead> 
				<th class="must_entry_caption">Item Category</th>
				<th class="must_entry_caption">Method</th>
			  </thead>
			<tr align="center">
			  <td>
				<? 
				echo create_drop_down( "cbo_item_category", 180, $item_category,'', 1, '---- Select ----', "0", "" );
				?>
			  </td>
			  <td>
				<? 
				echo create_drop_down( "cbo_store_method", 180, $store_method,'', 1, '---- Select ----',"0", "" );
				?>	
			  </td>
			</tr>
			<tr>
			  <td colspan="2" valign="bottom" align="center" class="button_container">                                
			  <? 
			  echo load_submit_buttons( $permission, "fnc_variable_settings_inventory_store_method", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1);
			  ?>
			  <input  type="hidden"name="update_id" id="update_id" value="">
			  </td>					
			</tr>
		  </table>
		  <div id="list_view_con" style="margin-top:15px;"> 
			<?
			$company_name_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
			$arr=array (0=>$company_name_arr,1=>$item_category,2=>$store_method);
			echo  create_list_view ( "list_view", "Company Name,Item Category,Method", "200,200,200","650","220",0, "select id,company_name,item_category_id,store_method from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_data_for_store_method_form'",1,"company_name,item_category_id,store_method", $arr , "company_name,item_category_id,store_method", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
			?>  
		  </div>
		</fieldset>	 
		<?
		exit();
	}// END Book keeping mmethod-----------------------------------------
	else if($type==18)
	{//Allocated Quantity-------------------------------------------------------------------
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		  <legend>Allocated Quantity</legend>
			<table cellspacing="0" cellpadding="0" width="400" class="rpt_table">
			<thead> 
			  <th class="must_entry_caption">Item Category</th>
			  <th class="must_entry_caption">Allocated</th>
			  <th class="must_entry_caption">Sample Without Order</th>
			  <th class="must_entry_caption">Sales Order</th>
			  </thead>
			  <tr align="center">
				<td>
				  <? 
				  echo create_drop_down( "cbo_item_category", 180, $item_category,'', 1, '---- Select ----', "0", "" );
				  ?>
				</td>
				<td>
				  <? 
				  echo create_drop_down( "cbo_allocated", 180, $yes_no,'', 1, '---- Select ----',"0", "" );
				  ?>	
				</td>
				<td>
				  <? 
				  echo create_drop_down( "cbo_smn_allocated", 180, $yes_no,'', 1, '---- Select ----',"0", "" );
				  ?>	
				</td>
				<td>
				  <? 
				  echo create_drop_down( "cbo_sales_allocated", 180, $yes_no,'', 0, '',"2", "" );
				  ?>	
				</td>
			  </tr>
			<tr>
			  <td colspan="4" valign="bottom" align="center" class="button_container">                                
				<? 
				echo load_submit_buttons( $permission, "fnc_variable_settings_inventory_allocation", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1);
				?>
				<input  type="hidden"name="update_id" id="update_id" value="">
			  </td>					
			</tr>
			</table>
		  <div id="list_view_con" style="margin-top:15px;"> 
			<?
			$company_name_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
			$arr=array (0=>$company_name_arr,1=>$item_category,2=>$yes_no,3=>$yes_no,4=>$yes_no);
			echo  create_list_view ( "list_view", "Company Name,Item Category,Allocation,SMN. Allocation,Sales Allocation", "200,150,150,150,150","850","220",0, "select id,company_name,item_category_id,allocation,smn_allocation,sales_allocation from variable_settings_inventory where company_name='".$company_id."' and variable_list='".$type."' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_data_for_allocation_form'",1,"company_name,item_category_id,allocation,smn_allocation,sales_allocation", $arr , "company_name,item_category_id,allocation,smn_allocation,sales_allocation", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
			?>  
		  </div>
		</fieldset>	 
		<?
		exit();
	}// END Allocated Method-------------------------------------
	else if($type=="20") // Receive Basis Controll---------------------------------------
	{
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		  <legend>Receive Basis Controll</legend>
		  <table cellspacing="0" cellpadding="0" width="700" class="rpt_table" border="1" rules="all">
			<thead> 
			  <th class="must_entry_caption">Page Name</th>
			  <th class="must_entry_caption">Independent Controll</th>
	          <th class="must_entry_caption">Rate Optional</th>
	          <th class="must_entry_caption">Rate Hide</th>
			  <th class="must_entry_caption">Rate Edit</th>
			</thead>
			<tr align="center">
			  <td id="menu_td">
				<? 
				//echo create_drop_down( "cbo_page_neme", 250, $entry_form,'', 1, '---- Select ----', "0", "" );
				?>
	            <input type="text" id="txt_menu_name" name="txt_menu_name" style="width:200px" class="text_boxes" onDblClick="fn_menu_page()" readonly placeholder="Browse" />
	            <input type="hidden" id="txt_menu_id" name="txt_menu_id" />
			  </td>
			  <td>
				<? 
				echo create_drop_down( "cbo_independent_con", 150, $yes_no,'', 1, '---- Select ----',"0", "" );
				?>	
			  </td>
	          <td>
				<? 
				echo create_drop_down( "cbo_rate_opption", 150, $yes_no,'', 1, '---- Select ----',"0", "" );
				?>	
			  </td>
	          <td>
				<? 
				echo create_drop_down( "cbo_rate_hide", 150, $yes_no,'', 1, '---- Select ----',"0", "" );
				?>	
			  </td>
			  <td>
				<? 
				echo create_drop_down( "cbo_rate_con", 150, $yes_no,'', 1, '---- Select ----',"0", "" );
				?>	
			  </td>
			</tr>
			<tr>
			  <td colspan="5" valign="bottom" align="center" class="button_container">
			  <input  type="hidden"name="update_id" id="update_id" value="">
			  <? 
			  echo load_submit_buttons( $permission, "fnc_variable_settings_inventory", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1);
			  ?>
			  </td>					
			</tr>
		  </table>
		  <div id="list_view_con" style="margin-top:15px"> 
			<?
			$company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
			$arr=array (0=>$company_name_arr,1=>$entry_form,2=>$yes_no,3=>$yes_no,4=>$yes_no,5=>$yes_no);
			echo  create_list_view ( "list_view", "Company Name,Page Name,Independent Controll,Rate Optional,Rate Hide,Rate Edit", "150,250,90,90,90","850","220",0, "select id,company_name,menu_page_id,independent_controll,rate_edit,rate_optional,is_editable from variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_form_inventory'",1,"company_name,menu_page_id,independent_controll,rate_optional,is_editable,rate_edit", $arr , "company_name,menu_page_id,independent_controll,rate_optional,is_editable,rate_edit", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
			?>  
		  </div>
		</fieldset>		  
		<?
		exit();
	}
	else if($type==21)
	{	 //Rack Wise Balance Show-------------------------------------------------------------------
		?>
		<fieldset style="width:100%; margin-bottom:10px">
			<legend>Rack Wise Balance Show</legend>
			<table cellspacing="0" cellpadding="0" width="600" class="rpt_table">
				<thead>
					<th class="must_entry_caption">Item Category</th>
					<th class="must_entry_caption">Rack Wise Balance Show</th>
					<th class="must_entry_caption">Up To</th>
				</thead>
				<tr align="center">
					<td>
						<?
						echo create_drop_down( "cbo_item_category", 180, $item_category_type_arr,'', 1, '---- Select ----',"0", "" );
						?>
					</td>
					<td>
						<?
						echo create_drop_down( "cbo_rack_balance", 180, $yes_no,'', 1, '---- Select ----',"0", "" );
						?>
					</td>
					<td>
						<?
						echo create_drop_down( "cbo_up_to", 180, $rack_shelf_upto_arr,'', 1, '---- Select ----',"0", "" );
						?>
					</td>
				</tr>
				<tr>
					<td colspan="3" valign="bottom" align="center" class="button_container">
						<?
						echo load_submit_buttons( $permission, "fnc_variable_settings_inventory_allocation", 0,0 ,"reset_form('inventoryvariablesettings_1','','','')",1);
						?>
						<input  type="hidden"name="update_id" id="update_id" value="">
					</td>
				</tr>
			</table>
			<div id="list_view_con" style="margin-top:15px;">
				<?
				$company_name_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
				$arr=array (0=>$company_name_arr,1=>$item_category_type_arr,2=>$yes_no,3=>$rack_shelf_upto_arr);
				// echo "select id,company_name,item_category_id,rack_balance from variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ";
				echo  create_list_view ( "list_view", "Company Name,Item Category,Rack Wise Balance Show,Up To", "200,200,200,200","850","220",0, "select id,company_name,item_category_id,rack_balance,store_method from variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_data_for_rack_balance_form'",1,"company_name,item_category_id,rack_balance,store_method", $arr , "company_name,item_category_id,rack_balance,store_method", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
				?>
			</div>
		</fieldset>
	    <?
	    exit();
	}
	else if($type==22 || $type==23)
	{
		if($type==22)
		{
			$legen_caption = "Yarn Services Process Loss"; 
			$th_category = "Service Type";
			$th_percentage = "Process %";
			$th_control = "Process Loss";
			$cbo_drop_down_arr = $yarn_issue_purpose ;
			$display_down_item_list = "2,12,15,38,46,7,50,51,63";
		}
		else
		{	
			$drop_down_item_list = "";
			$legen_caption = "Material Over Receive Control"; 
			$th_category = "Item Category";
			$th_percentage = "Over Rcv. %";
			$th_control = "Over Rcv. Control";
			$cbo_drop_down_arr = $item_category;
			$display_down_item_list = "";
		}
		
		?>
		<fieldset>
		  <legend> <? echo $legen_caption; ?> </legend>
		  <table cellpadding="2" cellspacing="0" width="480" class="rpt_table" id="tbl_variable_list" border="0" rules="all">
			<thead>
			  <tr>
				<th width="35">SL</th>
				<th width="190"><? echo $th_category; ?></th>
				<th width="100"><? echo $th_percentage; ?></th>
				<th width="70"><? echo $th_control; ?></th>
                <th></th>
			  </tr>
			</thead>     
			<tbody>             
			<?php 
			$itemGroupArr = return_library_array("select id,item_name from lib_item_group","id","item_name");
			$sqlResult= sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment,over_rcv_percent_textile from variable_inv_ile_standard where company_name='$company_id' and variable_list=$type order by id");
			$row_count=count($sqlResult);  
			$i=1;             
			foreach( $sqlResult as $rows ) 
			{
			  ?>
			  <tr> 
				<td width="35"><? echo $i;?></td>
				<td width="170" align="center">
				  <?
				  echo create_drop_down( "cbo_category".$i, 170, $cbo_drop_down_arr,"", 1, "-- Select --", $rows[csf("category")], "fu_check_duplicate_item($i)",0,$display_down_item_list );
				  ?>  
				</td>
				<td width="80" align="center" id="overPercentTd_<? echo $i; ?>">
				<?
					if($type==22)
					{
						?>
						<input type="text" name="txt_over_rcv_percent<? echo $i; ?>" id="txt_over_rcv_percent<? echo $i; ?>" value="<? echo $rows[csf("over_rcv_percent")]; ?>" class="text_boxes_numeric" style="width:100px"  onkeyup="fn_over_rcv_percent_check(<? echo $i; ?>,this.value,1)" />
						<?	
					}
					else
					{
						if( $rows[csf("category")]==1 ||  $rows[csf("category")]==2)
						{
							$place_holder_1 = ($rows[csf("category")]==2)? "Garments fabric" : "Grey Yarn";
							$place_holder_2 = ($rows[csf("category")]==2)? "Textile fabric" : "Dyed Yarn";
							?>
							<input type="text" name="txt_over_rcv_percent<? echo $i; ?>" id="txt_over_rcv_percent<? echo $i; ?>" value="<? echo $rows[csf("over_rcv_percent")]; ?>" class="text_boxes_numeric" style="width:40px" placeholder="<? echo $place_holder_1;?>" onKeyUp="fn_over_rcv_percent_check(<? echo $i; ?>,this.value,1)" />
							<input type="text" name="txt_over_rcv_percent_textile[]" id="txt_over_rcv_percent_textile<? echo $i; ?>" value="<? echo $rows[csf("over_rcv_percent_textile")]; ?>" class="text_boxes_numeric" style="width:40px" placeholder="<? echo $place_holder_2;?>"/>
							<?
						}
						else
						{
							?>
							<input type="text" name="txt_over_rcv_percent<? echo $i; ?>" id="txt_over_rcv_percent<? echo $i; ?>" value="<? echo $rows[csf("over_rcv_percent")]; ?>" class="text_boxes_numeric" style="width:100px"  onkeyup="fn_over_rcv_percent_check(<? echo $i; ?>,this.value,1)" />
							<?
						}
					}					
					?>
				</td>

				<td width="60" align="center">
				  <?
				  echo create_drop_down( "txt_over_rcv_payment".$i, 60, $yes_no,"", 0, "", $rows[csf("over_rcv_payment")], "fn_over_rcv_percent_check(".$i.",this.value,2)",0 );
				  ?>
				</td>

                <td>
                <? if($row_count==$i) $display_fac=''; else $display_fac='display:none;'; ?>
                <input style="width:30px; <? echo $display_fac; ?>" type="button" id="incrementfactor_<? echo $i; ?>" name="incrementfactor[]"  class="formbutton" value="+" onClick="fn_add_new_row(<? echo $i; ?>,<? echo $type; ?>)"/>
                <input style="width:30px; <? echo $display_fac; ?>" type="button" id="decrementfactor_<? echo $i; ?>" name="decrementfactor[]"  class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>)"/>
                </td>
			  </tr>
			  <?
			  $i++;
			}
			if($row_count==0 || $row_count=="")
			{ //----------for new save--------
			  ?>
			  <tr> 
				<td width="35"><? echo $i;?></td>
				<td width="170" align="center">
				  <?
				  echo create_drop_down( "cbo_category".$i, 170, $cbo_drop_down_arr,"", 1, "-- Select --", "", "fu_check_duplicate_item($i)",0,$display_down_item_list );
				  ?>  
				</td>
				<td width="80" align="center" id="overPercentTd_<? echo $i; ?>">
					<input type="text" name="txt_over_rcv_percent<? echo $i; ?>" id="txt_over_rcv_percent<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" onKeyUp="fn_over_rcv_percent_check(<? echo $i; ?>,this.value,1)"/>
				</td>
				<td width="60" align="center">
				  <?
				  echo create_drop_down( "txt_over_rcv_payment".$i, 60, $yes_no,"", 0, "",'', "fn_over_rcv_percent_check(".$i.",this.value,2)",0 );
				  ?>
				</td>
                <td>
                <input style="width:30px;" type="button" id="incrementfactor_<? echo $i; ?>" name="incrementfactor[]"  class="formbutton" value="+" onClick="fn_add_new_row(<? echo $i; ?>,<? echo $type; ?>)"/>
                <input style="width:30px;" type="button" id="decrementfactor_<? echo $i; ?>" name="decrementfactor[]"  class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>)"/>
                </td>
			  </tr>   
			  <? 
			} ?>  
			</tbody>                      	
		  </table>
		  <table>   
			<tr>
			  <td colspan="4">&nbsp;  </td>                                    
			</tr>
			<tr>
			  <td colspan="4" valign="bottom" align="center" class="button_container">
			  <input  type="hidden"name="update_id" id="update_id" value="">
			  <? 
			  if($row_count>0) $flag=1; else $flag=0;
			  echo load_submit_buttons( $permission, "fnc_material_over_receive_control", $flag,0,"reset_form('inventoryvariablesettings_1','variable_settings_container','')",1);
			  ?>
			  </td>					
			</tr>              
		  </table>    
		</fieldset>    
		<?
		exit();
	}
	 
	else if($type == "26" || $type == "40" || $type == "44")
	{
		$status = sql_select("select allocation,id from variable_settings_inventory where company_name = $company_id and variable_list = $type and is_deleted = 0 and status_active = 1");
		
		$select_status =  $status[0][csf('allocation')];
		$update_id =  $status[0][csf('id')];
		if ($update_id){ $is_update = 1;}else {$is_update = 0;}
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		  	<legend>User Given Code</legend>
		  	<table cellspacing="0" cellpadding="0" width="700" class="rpt_table" border="1" rules="all">
				<tr>
				  <td align="center"><? echo create_drop_down( "cbo_independent_con", 150, $yes_no,'', 1, '---- Select ----',$select_status, "" ); ?>
				  </td>
				</tr>
				<tr>
					<td valign="bottom" align="center" class="button_container">
						<input  type="hidden"name="update_id" id="update_id" value="<? echo $update_id;?>">
						<? echo load_submit_buttons( $permission, "fnc_variable_settings_requisition_mandatory", $is_update,0 ,"reset_form('inventoryvariablesettings_1','','')",1); ?>
					</td>
				</tr>
		  	</table>
		</fieldset>
		<?
	}
	else if($type==25)//Yarn item and rate matching with budget-----------------------------------
	{
		$control_level_arr = array(1=>"Rate Level", 2=>"Value Level",3=>"Quantity Level");

		$status = sql_select("select during_issue,id,user_given_code_status,tolerant_percent from variable_settings_inventory where company_name = $company_id and variable_list = $type and is_deleted = 0 and status_active = 1");	
		$select_status =  $status[0][csf('during_issue')];
		$control_level_status =  $status[0][csf('user_given_code_status')];
		$tolerant_percent =  $status[0][csf('tolerant_percent')];
		
		$update_id =  $status[0][csf('id')];
		if ($update_id){ $is_update = 1;}else {$is_update = 0;}
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		  	<legend>Yarn item and rate matching with budget</legend>
			<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
			  	
			  	<thead>
				  <tr>
					<th width="170">Effect On</th>
					<th width="170">Control Level</th>
					<th width="170">Yes/No</th>
					<th width="170">Tolerant %</th>
				  </tr>
				</thead>

			  	<tr>
					<td><strong>During Issue / Allocation</strong></td>
					<td align="center"><? echo create_drop_down( "cbo_control_level", 150, $control_level_arr,'', 1, '---- Select ----',$control_level_status, "" ); ?></td>

					<td align="center"><? echo create_drop_down( "cbo_during_issue", 150, $yes_no,'', 1, '---- Select ----',$select_status, "fnc_target_disabled_enabled()" ); ?></td>
					<td width="170" align="center"><input type="text" name="txt_tolerant" id="txt_tolerant" class="text_boxes_numeric"  style="width:160px" disabled="" value="<? echo $tolerant_percent; ?>" /></td>
			 	</tr>
		
			  	<tr>
					<td colspan="4" valign="bottom" align="center" class="button_container">
				  	<input  type="hidden"name="update_id" id="update_id" value="<? echo $update_id;?>">
					<? echo load_submit_buttons( $permission, "fnc_during_issue", $is_update,0 ,"reset_form('inventoryvariablesettings_1','','')",1); ?>
					</td>
			  	</tr>
		  	</table>
		</fieldset>
		<?
		exit();
	}
	else if($type == "27")
	{
		?>
		<fieldset style="width:100%; margin-bottom:10px">
			<legend>Ack.  Required For Item Transfer</legend>
			<table cellspacing="0" cellpadding="0" width="600" class="rpt_table">
				<thead>
					<th class="must_entry_caption">Item Category</th>
					<th class="must_entry_caption">Is Required</th>
				</thead>
				<tr align="center">
					<td>
						<?
						echo create_drop_down( "cbo_item_category", 180, $item_category_type_arr,'', 1, '---- Select ----',"0", "" );
						?>
					</td>
					<td>
						<? 
						
						echo create_drop_down( "cbo_independent_con", 150, $ack_yes_no,'', 1, '---- Select ----',$select_status, "" ); 
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="bottom" align="center" class="button_container">
						<input  type="hidden"name="update_id" id="update_id" value="<? //echo $update_id;?>">
						<? echo load_submit_buttons( $permission, "fnc_variable_settings_auto_transfer_rcv", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1); ?>
					</td>
				</tr>
			</table>
            <div id="list_view_con" style="margin-top:15px;">
				<?
				$company_name_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
				$arr=array (0=>$company_name_arr,1=>$item_category_type_arr,2=>$ack_yes_no);
				// echo "select id,company_name,item_category_id,rack_balance from variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ";
				echo  create_list_view ( "list_view", "Company Name,Item Category,Is Required", "250,250","650","220",0, "select id, company_name, item_category_id, auto_transfer_rcv from variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_data_for_ack'",1,"company_name,item_category_id,auto_transfer_rcv", $arr , "company_name,item_category_id,auto_transfer_rcv", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
				?>
			</div>
		</fieldset>
		<?
		exit();
	}
	else if($type == "28")
	{
		$status = sql_select("select yarn_issue_basis,id from variable_settings_inventory where company_name = $company_id and variable_list = $type and is_deleted = 0 and status_active = 1");
		$select_status =  $status[0][csf('yarn_issue_basis')];
		$update_id =  $status[0][csf('id')];
		if ($update_id){ $is_update = 1;}else {$is_update = 0;}
		//echo "<pre>";
		//print_r($status);die;
		
		//$select_status =  $status[0][csf('auto_transfer_rcv')];
		//$update_id =  $status[0][csf('id')];
		// if ($update_id){ $is_update = 1;}else {$is_update = 0;}
		?>	
		<fieldset style="width:100%; margin-bottom:10px">
		  <legend>Yarn Issue Basis</legend>
		  <table cellspacing="0" cellpadding="0" width="700" class="rpt_table" border="1" rules="all">
			<tr>
			  <? $arr=array(0=>'--Select--',1=>'Requisition',2=>'Demand');?>
			  <td align="center"><span style="font-weight: bold">Basis:</span>&nbsp;&nbsp;&nbsp;<? echo create_drop_down( "cbo_independent_con", 150, $arr,'', 0, '---- Select ----',$select_status, "" ); ?></td>
			</tr>
			<tr>
			  <td valign="bottom" align="center" class="button_container">
			  <input  type="hidden"name="update_id" id="update_id" value="<? echo $update_id;?>">
			  <? echo load_submit_buttons( $permission, "fnc_variable_settings_yarn_issue_basis", $is_update,0 ,"reset_form('inventoryvariablesettings_1','','')",1); ?>
			  </td>
			</tr>
		  </table>
		</fieldset>
		<?
		exit();
	}
	else if($type == "48")
	{
		$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $company_id and variable_list = $type and is_deleted = 0 and status_active = 1");
		
		$select_status =  $status[0][csf('ready_to_approve')];
		$update_id =  $status[0][csf('id')];
		if ($update_id){ $is_update = 1;}else {$is_update = 0;}
		//echo "<pre>";
		//print_r($status);die;
		
		//$select_status =  $status[0][csf('auto_transfer_rcv')];
		//$update_id =  $status[0][csf('id')];
		// if ($update_id){ $is_update = 1;}else {$is_update = 0;}
		?>	
		<fieldset style="width:100%; margin-bottom:10px">
		  <legend>Ready To Approve</legend>
		  <table cellspacing="0" cellpadding="0" width="700" class="rpt_table" border="1" rules="all">
			<tr>
			  <? $arr= array(1 => "Yes", 2 => "No");?>
			  <td align="center"><span style="font-weight: bold">Ready To Approve:</span>&nbsp;&nbsp;&nbsp;<? echo create_drop_down( "cbo_independent_con", 150, $arr,'', 0, '---- Select ----',$select_status, "" ); ?></td>
			</tr>
			<tr>
			  <td valign="bottom" align="center" class="button_container">
			  <input  type="hidden"name="update_id" id="update_id" value="<? echo $update_id;?>">
			  <? echo load_submit_buttons( $permission, "fnc_variable_settings_dye_issue_basis", $is_update,0 ,"reset_form('inventoryvariablesettings_1','','')",1); ?>
			  </td>
			</tr>
		  </table>
		</fieldset>
		<?
		exit();
	}
	else if($type == "29" || $type=="32" || $type=="38" || $type=="41" || $type=="42" || $type=="43" || $type=="45")
	{
		$status = sql_select("select auto_transfer_rcv,id from variable_settings_inventory where company_name = $company_id and variable_list = $type and is_deleted = 0 and status_active = 1");
		
		$select_status =  $status[0][csf('auto_transfer_rcv')];
		$update_id =  $status[0][csf('id')];
		if ($update_id){ $is_update = 1;}else {$is_update = 0;}
		?>
		
		<fieldset style="width:100%; margin-bottom:10px">
		<legend>Lot Maintain</legend>
			<table cellspacing="0" cellpadding="0" width="700" class="rpt_table" border="1" rules="all">
				<tr>
                	<?
					if($type==38)
					{
						?>
						<td width="300" align="right">Is Maintain :&nbsp;</td>
                        <?
					}
					else if($type==29 || $type==32)
					{
						?>
						<td width="300" align="right">Lot Maintain :&nbsp;</td>
                        <?
					}
					else if($type==41)
					{
						?>
						<td width="300" align="right">Woven GRN Maintain :&nbsp;</td>
                        <?
					}
					else if($type==42)
					{
						?>
						<td width="300" align="right">MRR wise balancing maintain :&nbsp;</td>
                        <?
					}

					else if($type==43)
					{
						?>
						<td width="300" align="right">Yarn Parking Receive/GRN Entry :&nbsp;</td>
                        <?
					}
					else if($type==45)
					{
						?>
						<td width="300" align="right">Item Create From REQ/WO :&nbsp;</td>
                        <?
					}
					                	
					if($type==38)
					{
						?>
                        <td align="left">&nbsp;&nbsp;<? echo create_drop_down( "cbo_independent_con", 150, $yes_no,'', 0, '',$select_status, "" ); ?></td>
                        <?
					}
					else
					{
						?>
                        <td align="left">&nbsp;&nbsp;<? echo create_drop_down( "cbo_independent_con", 150, $yes_no,'', 1, '---- Select ----',$select_status, "" ); ?></td>
                        <?
					}
					?>
					
				</tr>
				<tr>
					<td colspan="2" valign="bottom" align="center" class="button_container">
					<input  type="hidden"name="update_id" id="update_id" value="<? echo $update_id;?>">
					<? echo load_submit_buttons( $permission, "fnc_variable_settings_auto_transfer_rcv", $is_update,0 ,"reset_form('inventoryvariablesettings_1','','')",1); ?>
					</td>
				</tr>
			</table>
		</fieldset>
		<?
		exit();
	}
	else if($type==30 || $type == "24") //Requisition Basis Transfer
	{
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		  <legend><? if($type==30) echo "Requisition Basis Transfer"; else echo "Issue Requisition Mandatory"; ?></legend>
		  <table cellspacing="0" cellpadding="0" width="400" class="rpt_table">
			<thead> 
			  <th class="must_entry_caption">Item Category</th>
			  <th class="must_entry_caption">Is Required</th>
			</thead>
			  <tr align="center">
			  <td>
				<?
				if($type==30) $selected_category="13,2,8,5"; else $selected_category=""; 
				echo create_drop_down( "cbo_item_category", 180, $item_category_type_arr,'', 1, 'Select', '0', "",'',"$selected_category" );
				?>
			  </td>
			  <td>
				<? 
				echo create_drop_down( "cbo_item_status", 180, $yes_no,'', 1, '---- Select ----',"0", "" );
				?>	
			  </td>
			</tr>
			<tr>
			  <td colspan="2" valign="bottom" align="center" class="button_container">
				<input  type="hidden"name="update_id" id="update_id" value="">
				<? 
				echo load_submit_buttons( $permission, "fnc_variable_settings_inventory", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1);
				?>
			  </td>					
			</tr>
		  </table>
		
		<div id="list_view_con" style="margin-top:15px"> 
		  <?
		  $company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
		  $arr=array (0=>$company_name_arr,1=>$item_category_type_arr,2=>$yes_no);
		  echo  create_list_view ( "list_view", "Company Name,Item Category,Is Required", "150,150,150","470","220",0, "select id,company_name,item_category_id,user_given_code_status from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_form'",1,"company_name,item_category_id,user_given_code_status", $arr , "company_name,item_category_id,user_given_code_status", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
		  ?>  
		  </div>
		</fieldset>		  
		<?
		exit();
	}
  	else if($type==31) //wo pi receive level
	{
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		  <legend>WO PI Receive Level</legend>
		  <table cellspacing="0" cellpadding="0" width="400" class="rpt_table">
			<thead> 
			  <th class="must_entry_caption">Item Category</th>
			  <th class="must_entry_caption">Receive Type</th>
			</thead>
			  <tr align="center">
			  <td>
				<? 
				echo create_drop_down( "cbo_item_category", 180, $item_category,'', 0, '', '1', "",'','1' );
				?>
			  </td>
			  <td>
				<?
				echo create_drop_down( "cbo_item_status", 180, $rcv_type_arr,'', 1, '---- Select ----',"0", "" );
				?>	
			  </td>
			</tr>
			<tr>
			  <td colspan="2" valign="bottom" align="center" class="button_container">
				<input  type="hidden"name="update_id" id="update_id" value="">
				<? 
				echo load_submit_buttons( $permission, "fnc_variable_settings_inventory", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1);
				?>
			  </td>					
			</tr>
		  </table>
		
		<div id="list_view_con" style="margin-top:15px"> 
		  <?
		  if($type==31) $item_type_arr=$rcv_type_arr; else $item_type_arr=$yes_no;
		  $company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
		  $arr=array (0=>$company_name_arr,1=>$item_category,2=>$item_type_arr);
		  echo create_list_view ( "list_view", "Company Name,Item Category,Status", "150,150,150","470","220",0, "select id,company_name,item_category_id,user_given_code_status from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_form'",1,"company_name,item_category_id,user_given_code_status", $arr , "company_name,item_category_id,user_given_code_status", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
		  ?>  
		  </div>
		</fieldset>		  
		<?
		exit();
	}
    else if( $type=="35" ) // Item Issue Req. Stock Validation
    {
        $nameArray=sql_select( "SELECT id, user_given_code_status from variable_settings_inventory where company_name='$company_id' and variable_list=35 order by id" );
        if(count($nameArray)>0) $is_update=1; 
        else $is_update=0;
        ?>
            <div>
               <fieldset>
                    <legend>Item Issue Req. Stock Validation</legend>
                    <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">                    
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <thead>
                                <tr>
                                    <th align="right" style="width:150px;" >Item show: &nbsp;</th>
                                    <th>
                                        <?
                                            $stock_val_arr=array(1=>"With Zero Stock",2=>"Without Zero Stock");
                                            echo create_drop_down( "cbo_item_show_status", 150, $stock_val_arr,'', 1, '---- Select ----',$nameArray[0][csf("user_given_code_status")], "" );
                                        ?>                                              
                                    </th>
                                </tr> 
                            </thead>
                        </table>
                        <table cellspacing="0" width="100%" class="rpt_table" rules="all" border="1">
                            <tr>
                               <td colspan="2" height="40" valign="bottom" align="center" class="button_container">
                                    <input  type="hidden" name="update_id" id="update_id" value="<? echo $nameArray[0][csf("id")]; ?>">
                                    <? 
                                        $permission=$_SESSION['page_permission'];
                                        echo load_submit_buttons( $permission, "fnc_variable_settings_inventory", $is_update,0 ,"reset_form('inventoryvariablesettings_1','','',1)");
                                    ?>
                                </td>                   
                            </tr>  
                        </table>
                    </div>
                </fieldset>
            </div>
        <?                          
    }
	/*
	|-----------------------------------------------------------------------
	| For Yarn Test Mandatory/Approval For Allocation/yarn test source
	|-----------------------------------------------------------------------
	*/
	else if( $type == 36 || $type == 37 || $type == 49 )
	{
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		<?
		if($type == 49){
			?>
				<legend>For Yarn Test</legend>
			<?
		}
		else{
			?>
				<legend>For Yarn Allocation</legend>
			<?
		}
		?>
		  
			<table cellspacing="0" cellpadding="0" width="400" class="rpt_table">
				<thead> 
					<? if($type == 36) { $list_tblClmn = "Is Test Mandatory"; ?> <th class="must_entry_caption">Is Yarn Test Mandatory</th> <? }?>
					<? if($type == 37) { $list_tblClmn = "Test Approval Mandatory"; ?> <th class="must_entry_caption">Yarn Test Approval Mandatory</th> <? }?>
					<? if($type == 49) { $list_tblClmn = "Yarn Test Source"; ?> <th class="must_entry_caption">Yarn Test Source</th> <? }?>
			  	</thead>
			  <tr align="center">
				<td>
				  <? 
				  if($type == 49){
					echo create_drop_down( "cbo_is_yarn_test_mandatory", 100, $yarn_test_sourceArr,'', 0, '',"2", "" );
				  }
				  else
				  	echo create_drop_down( "cbo_is_yarn_test_mandatory", 100, $yes_no,'', 0, '',"2", "" );
				  ?>	
				</td>
			  </tr>
			<tr>
			  <td colspan="4" valign="bottom" align="center" class="button_container">                                
				<? 
				echo load_submit_buttons( $permission, "func_vs_yarn_test_mandatory", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1);
				?>
				<input  type="hidden"name="update_id" id="update_id" value="">
			  </td>					
			</tr>
			</table>
		  <div id="list_view_con" style="margin-top:15px;"> 
			<?
			$company_name_arr=return_library_array("select id, company_name from lib_company", "id", "company_name");
			if($type == 49){
				$arr=array (0=>$company_name_arr,1=>$item_category,2=>$yarn_test_sourceArr);
			}
			else{
				$arr=array (0=>$company_name_arr,1=>$item_category,2=>$yes_no);		
			}

			echo  create_list_view ( "list_view", "Company Name,Item Category,$list_tblClmn", "200,100,150","500","220",0, "select id,company_name,item_category_id,yes_no from variable_settings_inventory where company_name='".$company_id."' and variable_list='".$type."' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_data_for_yarn_test'",1,"company_name,item_category_id,yes_no", $arr , "company_name,item_category_id,yes_no", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
			?>  
		  </div>
		</fieldset>	 
		<?
		exit();
	}
	else if($type==39)
	{
		?>
		<fieldset>
		  <legend>Quarantine/Parking Stock Maintain</legend>
		  <table cellpadding="2" cellspacing="0" width="480" class="rpt_table" id="tbl_variable_list" border="0" rules="all">
			<thead>
			  <tr>
				<th width="35">SL</th>
				<th width="190">Item Category</th>
				<th width="100">Parking</th>
				<th width="70">Shrinkage</th>
                <th></th>
			  </tr>
			</thead>     
			<tbody>             
			<?php 
			$itemGroupArr = return_library_array("select id,item_name from lib_item_group","id","item_name");
			$sqlResult= sql_select("select category, over_rcv_percent, over_rcv_payment from variable_inv_ile_standard where company_name='$company_id' and variable_list=39 order by id");
			$row_count=count($sqlResult);  
			$i=1;             
			foreach( $sqlResult as $rows ) 
			{
			  ?>
			  <tr> 
				<td width="35"><? echo $i;?></td>
				<td width="170" align="center">
				  <?
				  echo create_drop_down( "cbo_category".$i, 170, $item_category_type_arr,"", 1, "-- Select --", $rows[csf("category")], "fu_check_duplicate_item($i)",0 );
				  ?>  
				</td>
				<td width="80" align="center" id="overPercentTd_<? echo $i; ?>">
                  <?
				  echo create_drop_down( "txt_over_rcv_percent".$i, 60, $yes_no,"", 0, "", $rows[csf("over_rcv_percent")], "",0 );
				  ?>
				</td>

				<td width="60" align="center">
				  <?
				  echo create_drop_down( "txt_over_rcv_payment".$i, 60, $yes_no,"", 0, "", $rows[csf("over_rcv_payment")], "",0 );
				  ?>
				</td>

                <td>
                <? if($row_count==$i) $display_fac=''; else $display_fac='display:none;'; ?>
                <input style="width:30px; <? echo $display_fac; ?>" type="button" id="incrementfactor_<? echo $i; ?>" name="incrementfactor[]"  class="formbutton" value="+" onClick="fn_add_parking_row(<? echo $i; ?>)"/>
                <input style="width:30px; <? echo $display_fac; ?>" type="button" id="decrementfactor_<? echo $i; ?>" name="decrementfactor[]"  class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>)"/>
                </td>
			  </tr>
			  <?
			  $i++;
			}
			if($row_count==0 || $row_count=="")
			{ //----------for new save--------
			  ?>
			  <tr> 
				<td width="35"><? echo $i;?></td>
				<td width="170" align="center">
				  <?
				  echo create_drop_down( "cbo_category".$i, 170, $item_category_type_arr,"", 1, "-- Select --", "", "fu_check_duplicate_item($i)",0 );
				  ?>  
				</td>
				<td width="80" align="center" id="overPercentTd_<? echo $i; ?>">
                  <?
				  echo create_drop_down( "txt_over_rcv_percent".$i, 60, $yes_no,"", 0, "", $selected, "",0 );
				  ?>
				</td>
				<td width="60" align="center">
				  <?
				  echo create_drop_down( "txt_over_rcv_payment".$i, 60, $yes_no,"", 0, "",'', "",0 );
				  ?>
				</td>
                <td>
                <input style="width:30px;" type="button" id="incrementfactor_<? echo $i; ?>" name="incrementfactor[]"  class="formbutton" value="+" onClick="fn_add_parking_row(<? echo $i; ?>)"/>
                <input style="width:30px;" type="button" id="decrementfactor_<? echo $i; ?>" name="decrementfactor[]"  class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>)"/>
                </td>
			  </tr>   
			  <? 
			} ?>  
			</tbody>                      	
		  </table>
		  <table>   
			<tr>
			  <td colspan="4">&nbsp;  </td>                                    
			</tr>
			<tr>
			  <td colspan="4" valign="bottom" align="center" class="button_container">
			  <input  type="hidden"name="update_id" id="update_id" value="">
			  <? 
			  if($row_count>0) $flag=1; else $flag=0;
			  echo load_submit_buttons( $permission, "fnc_material_over_receive_control", $flag,0,"reset_form('inventoryvariablesettings_1','variable_settings_container','')",1);
			  ?>
			  </td>					
			</tr>              
		  </table>    
		</fieldset>    
		<?
		exit();
	} //End For Yarn Test Mandatory/Approval For Allocation
	else if($type==46)
	{
		$status = sql_select("select id,item_category_id,yes_no,tolerant_percent from variable_settings_inventory where company_name = $company_id and variable_list = $type and is_deleted = 0 and status_active = 1");	
		$item_category_id =  $status[0][csf('item_category_id')];
		$over_issue_status =  $status[0][csf('yes_no')];
		$over_percentage =  $status[0][csf('tolerant_percent')];
		
		$update_id =  $status[0][csf('id')];
		if ($update_id){ $is_update = 1;}else {$is_update = 0;}
		?>
		<fieldset style="width:100%; margin-bottom:10px">
		  	<legend>Yarn item and rate matching with budget</legend>
			<table cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
			  	
			  	<thead>
				  <tr>
					<th width="170">Item Category</th>
					<th width="170">Over Issue</th>
					<th width="170">Over Percentage %</th>
				  </tr>
				</thead>

			  	<tr>
					<td align="center">
						<?
						echo create_drop_down( "cbo_item_category", 180, $item_category,'', 1, '---- Select ----', $item_category_id, "" );
						?>	
					</td>

					<td align="center">
						<? echo create_drop_down( "cbo_over_issue", 150, $yes_no,'', 1, '---- Select ----',$over_issue_status, "fnc_target_disabled_enabled()" ); ?>
					</td>
					<td width="170" align="center">
						<input type="text" name="txt_over_percentage" id="txt_over_percentage" class="text_boxes_numeric"  style="width:160px" value="<? echo $over_percentage; ?>" />
					</td>
			 	</tr>
		
			  	<tr>
					<td colspan="4" valign="bottom" align="center" class="button_container">
				  	<input  type="hidden"name="update_id" id="update_id" value="<? echo $update_id;?>">
					<? echo load_submit_buttons( $permission, "fnc_over_issue", $is_update,0 ,"reset_form('inventoryvariablesettings_1','','')",1); ?>
					</td>
			  	</tr>
		  	</table>
		</fieldset>
		<?
		exit();
	}
	else if($type==47)
	{
		?>
		<fieldset style="width:100%; margin-bottom:10px">
			<legend>Store Wise Rate Maintain</legend>
			<table cellspacing="0" cellpadding="0" width="600" class="rpt_table">
				<thead>
					<th class="must_entry_caption">Item Category</th>
					<th class="must_entry_caption">Is Required</th>
				</thead>
				<tr align="center">
					<td>
						<?
						echo create_drop_down( "cbo_item_category", 180, $item_category_type_arr,'', 1, '---- Select ----',"0", "", "", "1,5,8" );
						?>
					</td>
					<td>
						<? 
						
						echo create_drop_down( "cbo_independent_con", 150, $yes_no,'', 1, '---- Select ----',$select_status, "" ); 
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="bottom" align="center" class="button_container">
						<input  type="hidden"name="update_id" id="update_id" value="<? //echo $update_id;?>">
						<? echo load_submit_buttons( $permission, "fnc_variable_settings_auto_transfer_rcv", 0,0 ,"reset_form('inventoryvariablesettings_1','','')",1); ?>
					</td>
				</tr>
			</table>
            <div id="list_view_con" style="margin-top:15px;">
				<?
				$company_name_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
				$arr=array (0=>$company_name_arr,1=>$item_category_type_arr,2=>$yes_no);
				// echo "select id,company_name,item_category_id,rack_balance from variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ";
				echo  create_list_view ( "list_view", "Company Name,Item Category,Is Required", "250,250","650","220",0, "select id, company_name, item_category_id, auto_transfer_rcv from variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_data_for_ack'",1,"company_name,item_category_id,auto_transfer_rcv", $arr , "company_name,item_category_id,auto_transfer_rcv", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
				?>
			</div>
		</fieldset>
		<?
		exit();
	}
	else if($type == 50)
	{
		$query="select TOLERANT_PERCENT from variable_settings_inventory where company_name=$company_id and variable_list=50";
		$hour= sql_select($query);
		 
		?>
		<fieldset style="width:70%; margin-bottom:10px; margin-top:10px;">
			<legend>Time Determinant of Gate Out.</legend>
			<table cellspacing="0" cellpadding="0" width="360" class="rpt_table" style="margin-top:6px;">
				 
				<tr align="center">
					<td style="font-weight:bold; font-size:16px; width:140px;">
						Hour
					</td>
					<td>						
						<input type="text" name="txt_hour" id="txt_hour" class="text_boxes_numeric" style="width:120px" value="<? echo $hour[0]['TOLERANT_PERCENT']; ?>"/>
					
					</td>					 
				</tr>
				
				<tr>
					<td colspan="2" valign="bottom" align="center" class="button_container">
						<input  type="hidden"name="update_id" id="update_id" value="<? //echo $update_id;?>">
						<? 
							echo load_submit_buttons( $permission, "fnc_gate_out_hour", 1,0 ,"reset_form('inventoryvariablesettings_1','','')",1); 
						?>
					</td>
				</tr>
			</table>			 
             
		</fieldset>
		<?
		exit();
	}
	else if($type == 51)
	{
		$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $company_id and variable_list =51 and is_deleted = 0 and status_active = 1");
		// echo "<pre>";
		// print_r($status);
		$select_status =  $status[0][csf('ready_to_approve')];
		$update_id =  $status[0][csf('id')];
		if ($update_id){ $is_update = 1;}else {$is_update = 0;}
		//echo "<pre>";
		//print_r($status);die;
		
		//$select_status =  $status[0][csf('auto_transfer_rcv')];
		//$update_id =  $status[0][csf('id')];
		// if ($update_id){ $is_update = 1;}else {$is_update = 0;}
		?>	
		<fieldset style="width:100%; margin-bottom:10px">
		  <legend>Ready To Approve</legend>
		  <table cellspacing="0" cellpadding="0" width="700" class="rpt_table" border="1" rules="all">
			<tr>
			  <? $arr= array(1 => "Yes", 2 => "No");?>
			  <td align="center"><span style="font-weight: bold">Stock Display:</span>&nbsp;&nbsp;&nbsp;<? echo create_drop_down( "cbo_stock_display", 150, $arr,'', 0, '---- Select ----',$select_status, "" ); ?></td>
			</tr>
			<tr>
			  <td valign="bottom" align="center" class="button_container">
			  <input  type="hidden"name="update_id" id="update_id" value="<? echo $update_id;?>">
			  <? echo load_submit_buttons( $permission, "fnc_variable_settings_dye_issue_basis", $is_update,0 ,"reset_form('inventoryvariablesettings_1','','')",1); ?>
			  </td>
			</tr>
		  </table>
		</fieldset>
		<?
		exit();
	}
}


if ($action=="menu_popup")
{
	echo load_html_head_contents("Menu Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(menu_str)
		{
			var menu_str_ref=menu_str.split("**");
			document.getElementById('txt_menu_id').value=menu_str_ref[0];
			document.getElementById('txt_menu_name').value=menu_str_ref[1];
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="460" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="320">Menu Name</th>
						<th> Menu Id
                        <input type="hidden" id="txt_menu_name" name="txt_menu_name">
                        <input type="hidden" id="txt_menu_id" name="txt_menu_id">
                        </th>
					</tr>
				</thead>
                <tbody id="list_view">
                <?
				$i=1;
				ksort($entry_form);
				foreach($entry_form as $m_id=>$menu_name)
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $m_id. "**" . $menu_name;?>');">
                        <td align="center"><? echo $i;?></td>
                        <td><? echo  $menu_name; ?></td>
                        <td align="center"><? echo $m_id; ?></td>
					</tr>
                    <?
					$i++;
				}
				?>
                </tbody>
            </table>
        </form>
    </div>
	</body>
	<script>
		setFilterGrid('list_view',1);
	</script>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}



//-------------------------------------------------------------------
//-------------------------------------------------------------------
if($action=="append_load_details_container")
{
	$itemGroupArr = return_library_array("select id,item_name from lib_item_group","id","item_name");
	$i=$data+1;
 	?>
            <tr> 
                <td align="center">
                    <?
                        echo create_drop_down( "cbo_category".$i, 170, $item_category,"", 1, "-- Select --", 0, "load_drop_down( 'requires/inventory_settings_controller', this.value+'**'+".$i.", 'load_drop_down_group', 'tdItemGroup_$i' );",0 );
                    ?>  
                </td>
                <td align="center" id="tdItemGroup_<?=$i;?>" style="display:none;">
                    <?
                        echo create_drop_down( "cbo_item_group".$i, 170, $itemGroupArr,"", 1, "-- ALL --", 0, "",0 );
                    ?> 
                </td>
                <td align="center">
                    <?
                        echo create_drop_down( "cbo_source".$i, 170, $source,"", 1, "-- Select --", 0, "",0 );
                    ?>
                </td>
                <td align="center"><input type="text" name="txt_standard<? echo $i; ?>" id="txt_standard<? echo $i; ?>" value="<? echo $rows[csf("standard")]; ?>" class="text_boxes_numeric" onFocus="add_variable_row(<? echo $i; ?>)"   style="width:160px"/></td>
            </tr>
           
     <?   
	 exit();                    
}


if($action=="append_load_material_over_receive_control") 
{	
	$data_arr = explode("_",$data);
	$row_id = $data_arr[0];
	$type = $data_arr[1]; // $type =1 then meterial over receive, other than yarn service process loss
	$i=$row_id+1;
	
	if($type==22)
	{
		$category = $yarn_issue_purpose;
		$drop_down_item_list = "2,12,15,38,46,7,50,51,63";
	}
	else
	{
		$category = $item_category;
		$drop_down_item_list = "";
	}
	
	//$itemGroupArr = return_library_array("select id,item_name from lib_item_group","id","item_name");

 	?>
        <tr> 
            <td width="35"><? echo $i;?></td>
            <td width="170" align="center">
                <?
                    echo create_drop_down( "cbo_category".$i, 170, $category,"", 1, "-- Select --", "", "fu_check_duplicate_item($i)",0, $drop_down_item_list );
                ?>  
            </td>
            <td width="80" align="center" id="overPercentTd_<? echo $i; ?>">
            	<input type="text" name="txt_over_rcv_percent<? echo $i; ?>" id="txt_over_rcv_percent<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" onKeyUp="fn_over_rcv_percent_check(<? echo $i; ?>,this.value)"/>
            </td>
            <td width="60" align="center">
                <?
                    echo create_drop_down( "txt_over_rcv_payment".$i, 60, $yes_no,"", 0, "",'', "fn_over_rcv_percent_check(".$i.",this.value,2);",0 );
                ?>
            </td>
            <td>
            <input style="width:30px;" type="button" id="incrementfactor_<? echo $i; ?>" name="incrementfactor[]"  class="formbutton" value="+" onClick="fn_add_new_row(<? echo $i; ?>,<? echo $type; ?>)"/>
            <input style="width:30px;" type="button" id="decrementfactor_<? echo $i; ?>" name="decrementfactor[]"  class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>)"/>
            </td>
        </tr>            
     <?   
	 exit();                    
}


if($action=="append_load_parking_control")
{
	//$itemGroupArr = return_library_array("select id,item_name from lib_item_group","id","item_name");
	$i=$data+1;
 	?>
        <tr> 
            <td width="35"><? echo $i;?></td>
            <td width="170" align="center">
                <?
                    echo create_drop_down( "cbo_category".$i, 170, $item_category,"", 1, "-- Select --", "", "fu_check_duplicate_item($i)",0 );
                ?>  
            </td>

			<td width="80" align="center" id="overPercentTd_<? echo $i; ?>">
				<?
				echo create_drop_down( "txt_over_rcv_percent".$i, 60, $yes_no,"", 0, "", $rows[csf("over_rcv_percent")], "",0 );
				?>
			</td>

            <td width="60" align="center">
                <?
                    echo create_drop_down( "txt_over_rcv_payment".$i, 60, $yes_no,"", 0, "",'', "fn_over_rcv_percent_check(".$i.",this.value,2);",0 );
                ?>
            </td>
            <td>
            <input style="width:30px;" type="button" id="incrementfactor_<? echo $i; ?>" name="incrementfactor[]"  class="formbutton" value="+" onClick="fn_add_parking_row(<? echo $i; ?>)"/>
            <input style="width:30px;" type="button" id="decrementfactor_<? echo $i; ?>" name="decrementfactor[]"  class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>)"/>
            </td>
        </tr>            
     <?   
	 exit();                    
}

//-------------------------------------------------------------------
//-------------------------------------------------------------------


if ($action=="on_change_data_list")
{
	$ex_data = explode("_",$data);
	$type = $ex_data[0];
	$company_id = $ex_data[1];
	if($type==20)
	{
		$company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
		$arr=array (0=>$company_name_arr,1=>$entry_form,2=>$yes_no,3=>$yes_no,4=>$yes_no,5=>$yes_no);
		echo  create_list_view ("list_view", "Company Name,Page Name,Independent Controll,Rate Optional,Rate Hide,Rate Edit", "150,250,90,90,90","850","220",0, "select id,company_name,menu_page_id,independent_controll,rate_edit,rate_optional,is_editable from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_form_inventory'",1,"company_name,menu_page_id,independent_controll,rate_optional,is_editable,rate_edit", $arr , "company_name,menu_page_id,independent_controll,rate_optional,is_editable,rate_edit", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
	}
	/*else if($type==22)
	{
		
		$company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
		$menu_name_arr=return_library_array( "select m_menu_id,menu_name from main_menu where m_module_id=6 and root_menu>0", "m_menu_id", "menu_name"  );
		$arr=array (0=>$company_name_arr,1=>$menu_name_arr,2=>$yes_no,3=>$yes_no);
		echo  create_list_view ( "list_view", "Company Name,Page Name,Independent Controll", "180,200,100,150","570","220",0, "select id,company_name,menu_page_id,independent_controll from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_independent'",1,"company_name,menu_page_id,independent_controll", $arr , "company_name,menu_page_id,independent_controll", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
	}*/
    else if($type==10)
    {
      $company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
        $arr=array (0=>$company_name_arr,1=>$item_category,2=>$yes_no,3=>$yes_no);

        echo  create_list_view ( "list_view", "Company,Item Category,Rate Optional, Is Editable","180,180,100,100","630","220",0,"select id,company_name,item_category_id,rate_optional,is_editable from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_item_rate_mrr'",1,"company_name,item_category_id,rate_optional,is_editable",$arr,"company_name,item_category_id,rate_optional,is_editable","../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",1);' );
    }
    else if($type==30 || $type==24)
    {
		$company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
		$arr=array (0=>$company_name_arr,1=>$item_category_type_arr,2=>$yes_no);
		echo  create_list_view ( "list_view", "Company Name,Item Category,Status", "150,150,150","470","220",0, "select id,company_name,item_category_id,user_given_code_status from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_form'",1,"company_name,item_category_id,user_given_code_status", $arr , "company_name,item_category_id,user_given_code_status", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
    }
	else
	{
		if($type==31) $item_type_arr=$rcv_type_arr; else $item_type_arr=$yes_no;
		$company_name_arr=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
		$arr=array (0=>$company_name_arr,1=>$item_category,2=>$item_type_arr);
		echo  create_list_view ( "list_view", "Company Name,Item Category,Status", "150,150,150","470","220",0, "select id,company_name,item_category_id,user_given_code_status from  variable_settings_inventory where company_name='$company_id' and variable_list='$type' and status_active=1 and is_deleted=0 ","get_php_form_data","id","'load_php_data_to_form'",1,"company_name,item_category_id,user_given_code_status", $arr , "company_name,item_category_id,user_given_code_status", "../variable/requires/inventory_settings_controller",'setFilterGrid("list_view",-1);' );
	}			  
}

if ($action=="load_php_data_to_form")
{
	  $nameArray=sql_select( "select id,company_name,variable_list,item_category_id,user_given_code_status from variable_settings_inventory where id='$data'" );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('cbo_item_category').value = '".($inf[csf("item_category_id")])."';\n"; 
		  echo "document.getElementById('cbo_item_status').value = '".($inf[csf("user_given_code_status")])."';\n";
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory',1);\n";  
	  }
}

if ($action=="load_php_data_to_form_inventory")
{
	  $nameArray=sql_select( "select id,company_name,variable_list,module_id,menu_page_id,independent_controll,rate_optional, is_editable,rate_edit from variable_settings_inventory where id='$data'" );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('txt_menu_id').value = '".($inf[csf("menu_page_id")])."';\n";
		  echo "document.getElementById('txt_menu_name').value = '".($entry_form[$inf[csf("menu_page_id")]])."';\n"; 
		  echo "document.getElementById('cbo_independent_con').value = '".($inf[csf("independent_controll")])."';\n";
		  echo "document.getElementById('cbo_rate_opption').value = '".($inf[csf("rate_optional")])."';\n";
		  echo "document.getElementById('cbo_rate_hide').value = '".($inf[csf("is_editable")])."';\n";
		  echo "document.getElementById('cbo_rate_con').value = '".($inf[csf("rate_edit")])."';\n";
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory',1);\n";  
	  }
}

//load_php_data_to_independent
if ($action=="load_php_data_to_independent")
{
	  $nameArray=sql_select( "select id,company_name,variable_list,module_id,menu_page_id,independent_controll,rate_edit from variable_settings_inventory where id='$data'" );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('cbo_module').value = '".($inf[csf("module_id")])."';\n";
		  echo "load_drop_down( 'requires/inventory_settings_controller',".$inf[csf("module_id")].", 'load_drop_down_menu', 'menu_td' );\n";
		  echo "document.getElementById('cbo_page_neme').value = '".($inf[csf("menu_page_id")])."';\n"; 
		  echo "document.getElementById('cbo_independent_con').value = '".($inf[csf("independent_controll")])."';\n";
		
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory',1);\n";  
	  }
}

if ($action=="load_php_data_to_item_rate_mrr")
{
    $nameArray=sql_select( "select id,company_name,variable_list,item_category_id,rate_optional,is_editable from variable_settings_inventory where id='$data'" );
    foreach ($nameArray as $inf)
    {
      echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
      echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
      echo "document.getElementById('cbo_category').value = '".($inf[csf("item_category_id")])."';\n";
      echo "document.getElementById('cbo_rate_optional').value = '".($inf[csf("rate_optional")])."';\n"; 
      echo "document.getElementById('cbo_editable').value = '".($inf[csf("is_editable")])."';\n";
      echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
      echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory',1);\n";  
    }
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$variable_list=str_replace("'","",$cbo_variable_list);
	
	if($variable_list==10)
	{
	    if ($operation==0)  // Insert Here==============================================================================
	    {
	      if(is_duplicate_field( "item_category_id", " variable_settings_inventory", "item_category_id=$cbo_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
	      {
	          echo "11**0"; die;
	      }
	      else
	      {
	        $con = connect();
	          if($db_type==0)
	          {
	            mysql_query("BEGIN");
	          }
	          $id=return_next_id( "id", "variable_settings_inventory", 1 );
	          
	          $field_array="id,company_name,variable_list,item_category_id,rate_optional,is_editable,inserted_by,insert_date,status_active,is_deleted";
	          $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_category.",".$cbo_rate_optional.",".$cbo_editable.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
	          //print_r($data_array);die;
	          $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
	          if($db_type==0)
	          {
	            if($rID ){
	              mysql_query("COMMIT");  
	              echo "0**".$rID;
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
	            echo "0**".$rID;
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
	      else if ($operation==1)   // Update Here=========================================================
	      {
	        
	       //  echo "select item_category_id from  variable_settings_inventory where item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0";die;
	       if(is_duplicate_field( "item_category_id", " variable_settings_inventory", "item_category_id=$cbo_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and id<>$update_id and is_deleted=0" ) == 1)
	      {
	        echo "11**0"; die;
	      }
	      else
	      {
	          $con = connect();
	          if($db_type==0)
	          {
	            mysql_query("BEGIN");
	          }
	          
	          $field_array="company_name*variable_list*item_category_id*rate_optional*is_editable*updated_by*update_date*status_active*is_deleted";
	          $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_category."*".$cbo_rate_optional."*".$cbo_editable."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
	         // print_r($data_array);die;
	          $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
	          //print_r($rID);die;
	          if($db_type==0)
	          {
	            if($rID ){
	              mysql_query("COMMIT");  
	              echo "1**".$rID;
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
	      else if ($operation==2)   // Delete Here===================================================
	      {
	        $con = connect();
	        if($db_type==0)
	        {
	          mysql_query("BEGIN");
	        }
	        
	        $field_array="updated_by*update_date*status_active*is_deleted";
	        $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
	        
	        $rID=sql_delete("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
	        
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
	            echo "2**".$rID;
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
	else if($variable_list==20)
	{
		if ($operation==0)  // Insert Here==============================================================================
		{
			
			if(is_duplicate_field( "menu_page_id", " variable_settings_inventory", "menu_page_id=$txt_menu_id and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
			{
					echo "11**0"; die;
			}
			else
			{
				$con = connect();
				  if($db_type==0)
				  {
					  mysql_query("BEGIN");
				  }
				  $id=return_next_id( "id", "variable_settings_inventory", 1 );
				  
				  $field_array="id,company_name,variable_list,menu_page_id,independent_controll,rate_optional, is_editable,rate_edit,inserted_by,insert_date,status_active,is_deleted";
				  $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$txt_menu_id.",".$cbo_independent_con.",".$cbo_rate_opption.",".$cbo_rate_hide.",".$cbo_rate_con.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
				  //echo "10**insert into variable_settings_inventory (".$field_array.") values ".$data_array;die;
				  //print_r($data_array);die;
				  $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
				  if($db_type==0)
				  {
					  if($rID ){
						  mysql_query("COMMIT");  
						  echo "0**".$rID;
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
						echo "0**".$rID;
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
		  else if ($operation==1)   // Update Here=========================================================
		  {
			  
			 //  echo "select item_category_id from  variable_settings_inventory where item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0";die;
			 if(is_duplicate_field( "menu_page_id", " variable_settings_inventory", "menu_page_id=$txt_menu_id and company_name=$cbo_company_name and variable_list=$cbo_variable_list and id<>$update_id and is_deleted=0" ) == 1)
			{
				echo "11**0"; die;
			}
			else
			{
				  $con = connect();
				  if($db_type==0)
				  {
					  mysql_query("BEGIN");
				  }
				  
				  $field_array="company_name*variable_list*menu_page_id*independent_controll*rate_optional*is_editable*rate_edit*updated_by*update_date*status_active*is_deleted";
				  $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$txt_menu_id."*".$cbo_independent_con."*".$cbo_rate_opption."*".$cbo_rate_hide."*".$cbo_rate_con."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
				 // print_r($data_array);die;
				  $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
				  //print_r($rID);die;
				  if($db_type==0)
				  {
					  if($rID ){
						  mysql_query("COMMIT");  
						  echo "1**".$rID;
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
		  else if ($operation==2)   // Delete Here===================================================
		  {
			  $con = connect();
			  if($db_type==0)
			  {
				  mysql_query("BEGIN");
			  }
			  
			  $field_array="updated_by*update_date*status_active*is_deleted";
			  $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			  
			  $rID=sql_delete("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
			  
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
						echo "2**".$rID;
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
	/*else if($variable_list==22)
	{
		if ($operation==0)  // Insert Here==============================================================================
		{
			
			if(is_duplicate_field( "menu_page_id", " variable_settings_inventory", "menu_page_id=$cbo_page_neme and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
			{
					echo "11**0"; die;
			}
			else
			{
				$con = connect();
				  if($db_type==0)
				  {
					  mysql_query("BEGIN");
				  }
				  $id=return_next_id( "id", "variable_settings_inventory", 1 );
				  
				  $field_array="id,company_name,variable_list,module_id,menu_page_id,independent_controll,inserted_by,insert_date,status_active,is_deleted";
				  $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_module.",".$cbo_page_neme.",".$cbo_independent_con.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
				  //print_r($data_array);die;
				  $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
				  if($db_type==0)
				  {
					  if($rID ){
						  mysql_query("COMMIT");  
						  echo "0**".$rID;
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
						echo "0**".$rID;
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
		  else if ($operation==1)   // Update Here=========================================================
		  {
			  
			 //  echo "select item_category_id from  variable_settings_inventory where item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0";die;
			 if(is_duplicate_field( "menu_page_id", " variable_settings_inventory", "menu_page_id=$cbo_page_neme and company_name=$cbo_company_name and variable_list=$cbo_variable_list and id<>$update_id and is_deleted=0" ) == 1)
			{
				echo "11**0"; die;
			}
			else
			{
				  $con = connect();
				  if($db_type==0)
				  {
					  mysql_query("BEGIN");
				  }
				  
				  $field_array="company_name*variable_list*module_id*menu_page_id*independent_controll*updated_by*update_date*status_active*is_deleted";
				  $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_module."*".$cbo_page_neme."*".$cbo_independent_con."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
				 // print_r($data_array);die;
				  $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
				  //print_r($rID);die;
				  if($db_type==0)
				  {
					  if($rID ){
						  mysql_query("COMMIT");  
						  echo "1**".$rID;
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
		  else if ($operation==2)   // Delete Here===================================================
		  {
			  $con = connect();
			  if($db_type==0)
			  {
				  mysql_query("BEGIN");
			  }
			  
			  $field_array="updated_by*update_date*status_active*is_deleted";
			  $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			  
			  $rID=sql_delete("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
			  
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
						echo "2**".$rID;
					}
					else{
						oci_rollback($con);
						echo "10**".$rID;
					}
					
				  }
				disconnect($con);
				die;
		}
	}*/
	else if($variable_list==26 || $variable_list==40 || $variable_list==44)
    {
            if($operation == 0)
            {
              	 //item_category_id=$cbo_item_category and    ## Category not use for 24,26  in V. setting
			    if(is_duplicate_field( "id", " variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
                {
                                echo "11**0"; die;
                }
                else
                {
                        $con = connect();
                          if($db_type==0)
                          {
                                  mysql_query("BEGIN");
                          }
                          $id=return_next_id( "id", "variable_settings_inventory", 1 );

                          $field_array="id,company_name,variable_list,allocation,inserted_by,insert_date,status_active,is_deleted";
                          $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_independent_con.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
                          //print_r($data_array);die;
                          $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
                          if($db_type==0)
                          {
                                  if($rID ){
                                          mysql_query("COMMIT");  
                                          echo "0**".$rID;
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
                                        echo "0**".$rID;
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
            else if($operation == 1)
            {
                 $con = connect();
                if($db_type==0)
                {
                        mysql_query("BEGIN");
                }

                $field_array="company_name*variable_list*allocation*updated_by*update_date*status_active*is_deleted";
                $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_independent_con."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
               // print_r($data_array);die;
                $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
                //print_r($rID);die;
                if($db_type==0)
                {
                        if($rID ){
                                mysql_query("COMMIT");  
                                echo "1**".$rID;
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
	else if($variable_list==25)
    {
        if($operation == 0)
        {
            if(is_duplicate_field( "id", " variable_settings_inventory", "item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
            {
                echo "11**0"; die;
            }
            else
            {
                $con = connect();
				if($db_type==0)
				{
				   mysql_query("BEGIN");
				}
				$id=return_next_id( "id", "variable_settings_inventory", 1 );

				$field_array="id,company_name,variable_list,during_issue,user_given_code_status,tolerant_percent,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_control_level.",".$txt_tolerant.",".$cbo_during_issue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
				//print_r($data_array);die;
				$rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
				
				if($db_type==0)
				{
					if($rID )
					{
					  	mysql_query("COMMIT");  
					  	echo "0**".$rID;
					}
					else
					{
					  	mysql_query("ROLLBACK"); 
					  	echo "10**".$rID;
					}
				}

                if($db_type==2 || $db_type==1 )
                {

                    if($rID )
                    {
						oci_commit($con);   
						echo "0**".$rID;
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
        else if($operation == 1)
        {
             $con = connect();
            if($db_type==0)
            {
                    mysql_query("BEGIN");
            }

            $field_array="company_name*variable_list*during_issue*user_given_code_status*tolerant_percent*updated_by*update_date*status_active*is_deleted";
            $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_during_issue."*".$cbo_control_level."*".$txt_tolerant."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
           // print_r($data_array);die;
            $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
            //print_r($rID);die;
            if($db_type==0)
            {
                if($rID )
                {
                    mysql_query("COMMIT");  
                    echo "1**".$rID;
                }
                else
                {
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
    else if($variable_list==46)
    {
        if($operation == 0)
        {
            if(is_duplicate_field( "id", " variable_settings_inventory", "item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
            {
                echo "11**0"; die;
            }
            else
            {
                $con = connect();
				if($db_type==0)
				{
				   mysql_query("BEGIN");
				}
				$id=return_next_id( "id", "variable_settings_inventory", 1 );

				$field_array="id,company_name,variable_list,item_category_id,yes_no,tolerant_percent,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_over_issue.",".$txt_over_percentage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
				//print_r($data_array);die;
				$rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);

				if($db_type==0)
				{
					if($rID )
					{
					  	mysql_query("COMMIT");  
					  	echo "0**".$rID;
					}
					else
					{
					  	mysql_query("ROLLBACK"); 
					  	echo "10**".$rID;
					}
				}

                if($db_type==2 || $db_type==1 )
                {

                    if($rID )
                    {
						oci_commit($con);   
						echo "0**".$rID;
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
        else if($operation == 1)
        {
             $con = connect();
            if($db_type==0)
            {
                    mysql_query("BEGIN");
            }

            $field_array="company_name*variable_list*item_category_id*yes_no*tolerant_percent*updated_by*update_date*status_active*is_deleted";
            $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_category."*".$cbo_over_issue."*".$txt_over_percentage."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
           // print_r($data_array);die;
            $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
            //print_r($rID);die;
            if($db_type==0)
            {
                if($rID )
                {
                    mysql_query("COMMIT");  
                    echo "1**".$rID;
                }
                else
                {
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
	else if($variable_list==28)
    {
            if($operation == 0)
            {
                 //item_category_id=$cbo_item_category and    ## Category not use for 24,26  in V. setting
          if(is_duplicate_field( "id", " variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
                {
                                echo "11**0"; die;
                }
                else
                {
                        $con = connect();
                          if($db_type==0)
                          {
                                  mysql_query("BEGIN");
                          }
                          $id=return_next_id( "id", "variable_settings_inventory", 1 );

                          $field_array="id,item_category_id,company_name,variable_list,yarn_issue_basis,inserted_by,insert_date,status_active,is_deleted";
                          $data_array="(".$id.",1,".$cbo_company_name.",".$cbo_variable_list.",".$cbo_independent_con.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
                          //print_r($data_array);die;
                          $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
                          if($db_type==0)
                          {
                                  if($rID ){
                                          mysql_query("COMMIT");  
                                          echo "0**".$rID;
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
                                        echo "0**".$rID;
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
            else if($operation == 1)
            {
                 $con = connect();
                if($db_type==0)
                {
                        mysql_query("BEGIN");
                }

                $field_array="company_name*variable_list*yarn_issue_basis*updated_by*update_date*status_active*is_deleted";
                $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_independent_con."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
               // print_r($data_array);die;
                $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
                //print_r($rID);die;
                if($db_type==0)
                {
                        if($rID ){
                                mysql_query("COMMIT");  
                                echo "1**".$rID;
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
	else if($variable_list==48)
    {
            if($operation == 0)
            {
                 //item_category_id=$cbo_item_category and    ## Category not use for 24,26  in V. setting
          if(is_duplicate_field( "id", " variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
                {
                                echo "11**0"; die;
                }
                else
                {
                        $con = connect();
                          if($db_type==0)
                          {
                                  mysql_query("BEGIN");
                          }
                          $id=return_next_id( "id", "variable_settings_inventory", 1 );

                          $field_array="id,item_category_id,company_name,variable_list,ready_to_approve,inserted_by,insert_date,status_active,is_deleted";
                          $data_array="(".$id.",1,".$cbo_company_name.",".$cbo_variable_list.",".$cbo_independent_con.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
                        //echo "10**insert into variable_settings_inventory ($field_array) values" . $data_array;die;
                          $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
                          if($db_type==0)
                          {
                                  if($rID ){
                                          mysql_query("COMMIT");  
                                          echo "0**".$rID;
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
                                        echo "0**".$rID;
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
            else if($operation == 1)
            {
                 $con = connect();
                if($db_type==0)
                {
                        mysql_query("BEGIN");
                }

                $field_array="company_name*variable_list*ready_to_approve*updated_by*update_date*status_active*is_deleted";
                $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_independent_con."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
               // print_r($data_array);die;
                $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
                //print_r($rID);die;
                if($db_type==0)
                {
                        if($rID ){
                                mysql_query("COMMIT");  
                                echo "1**".$rID;
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
	else if($variable_list==51)
    {
            if($operation == 0)
            {
                 //item_category_id=$cbo_item_category and    ## Category not use for 24,26  in V. setting
          if(is_duplicate_field( "id", " variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
                {
                                echo "11**0"; die;
                }
                else
                {
                        $con = connect();
                          if($db_type==0)
                          {
                                  mysql_query("BEGIN");
                          }
                          $id=return_next_id( "id", "variable_settings_inventory", 1 );

                          $field_array="id,item_category_id,company_name,variable_list,ready_to_approve,inserted_by,insert_date,status_active,is_deleted";
                          $data_array="(".$id.",1,".$cbo_company_name.",".$cbo_variable_list.",".$cbo_stock_display.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
                        //echo "10**insert into variable_settings_inventory ($field_array) values" . $data_array;die;
                          $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
                          if($db_type==0)
                          {
                                  if($rID ){
                                          mysql_query("COMMIT");  
                                          echo "0**".$rID;
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
                                        echo "0**".$rID;
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
            else if($operation == 1)
            {
                 $con = connect();
                if($db_type==0)
                {
                        mysql_query("BEGIN");
                }

                $field_array="company_name*variable_list*ready_to_approve*updated_by*update_date*status_active*is_deleted";
                $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_stock_display."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
               // print_r($data_array);die;
                $rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
                //print_r($rID);die;
                if($db_type==0)
                {
                        if($rID ){
                                mysql_query("COMMIT");  
                                echo "1**".$rID;
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
	else if($variable_list==35)
    {
        if($operation == 0)
        {

			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "variable_settings_inventory", 1 );
			$cbo_item_show_status=str_replace("'","",$cbo_item_show_status);
			$field_array="id,company_name,variable_list,user_given_code_status,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_show_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
			// echo "10**INSERT INTO variable_settings_inventory (".$field_array.") VALUES ".$data_array; oci_rollback($con);disconnect($con);die;
			$rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
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
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}

			}
			disconnect($con);
			die;
   
		}
		else if($operation == 1)
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="company_name*variable_list*user_given_code_status*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_show_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
			// print_r($data_array);die;
			$rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
			//print_r($rID);die;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID;
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
	/*
	|-----------------------------------------------------------------------
	| For Yarn Test Mandatory For Allocation
	|-----------------------------------------------------------------------
	*/
	else if($variable_list == 36 || $variable_list == 37 || $variable_list == 49)
    {
        if($operation == 0)
        {
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			if(is_duplicate_field( "id", " variable_settings_inventory", "item_category_id=1 and company_name=$cbo_company_name and variable_list=$cbo_variable_list and status_active=1 and is_deleted=0" ) == 1)
			{
				echo "11**0";
				die;
			}

			$id=return_next_id( "id", "variable_settings_inventory", 1 );
			$cbo_item_show_status=str_replace("'","",$cbo_item_show_status);
			$field_array="id,company_name,variable_list,yes_no,item_category_id,status_active,is_deleted,inserted_by,insert_date";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_is_yarn_test_mandatory.",1,1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			/*echo "10**INSERT INTO variable_settings_inventory (".$field_array.") VALUES ".$data_array;
			oci_rollback($con);
			disconnect($con);
			die;*/
			$rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
			if($db_type==0)
			{
				if($rID)
				{
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}

			if($db_type==2 || $db_type==1 )
			{
				if($rID)
				{
					oci_commit($con);   
					echo "0**".$rID;
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
		else if($operation == 1)
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="company_name*variable_list*yes_no*status_active*is_deleted*updated_by*update_date";
			$data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_is_yarn_test_mandatory."*1*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
			if($db_type==0)
			{
				if($rID)
				{
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}

			if($db_type==2 || $db_type==1 )
			{
				if($rID)
				{
					oci_commit($con);   
					echo "1**".$rID;
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
	//End For Yarn Test Mandatory For Allocation
	else
	{
		if ($operation==0)  // Insert Here==============================================================================
		{
			
			if(is_duplicate_field( "item_category_id", " variable_settings_inventory", "item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0" ) == 1)
			{
					echo "11**0"; die;
			}
			else
			{
				$con = connect();
				  if($db_type==0)
				  {
					  mysql_query("BEGIN");
				  }
				  $id=return_next_id( "id", "variable_settings_inventory", 1 );
				  
				  $field_array="id,company_name,variable_list,item_category_id,user_given_code_status,inserted_by,insert_date,status_active,is_deleted";
				  $data_array="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_item_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,'0')";
				  //print_r($data_array);die;
				  $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
				  if($db_type==0)
				  {
					  if($rID ){
						  mysql_query("COMMIT");  
						  echo "0**".$rID;
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
						echo "0**".$rID;
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
		else if ($operation==1)   // Update Here=========================================================
		{
			  
			//echo "10**select item_category_id from  variable_settings_inventory where item_category_id=$cbo_item_category and company_name=$cbo_company_name and variable_list=$cbo_variable_list and is_deleted=0";die;
			if(is_duplicate_field( "item_category_id", " variable_settings_inventory", "item_category_id=$cbo_item_category and company_name=$cbo_company_name   and variable_list=$cbo_variable_list and is_deleted=0 and id!=$update_id" ) == 1)
			{
				echo "11**0"; die;
			}
			else
			{
				$con = connect();
				if($db_type==0)
				{
				  mysql_query("BEGIN");
				}

				$field_array="company_name*variable_list*item_category_id*user_given_code_status*updated_by*update_date*status_active*is_deleted";
				$data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_category."*".$cbo_item_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".'1'."*'0'";
				// print_r($data_array);die;
				$rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);
				//print_r($rID);die;
				if($db_type==0)
				{
				  if($rID ){
					  mysql_query("COMMIT");  
					  echo "1**".$rID;
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
		else if ($operation==2)   // Delete Here===================================================
		{
			$con = connect();
			if($db_type==0)
			{
			  mysql_query("BEGIN");
			}
				  
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

			$rID=sql_delete("variable_settings_inventory",$field_array,$data_array,"id","".$update_id."",1);

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
					echo "2**".$rID;
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

//---------------------------------------------ile standard save here-----------------------------------// 
if ($action=="save_update_delete_ile")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here==============================================================================
	{
 	
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "variable_inv_ile_standard", 1 );		
		$field_array="id,company_name, variable_list, category, item_group, source, standard,inserted_by,insert_date";
		$rows = str_replace("'","",$row);
		$data_array="";
		for($i=1;$i<=$rows;$i++)
		{ 
			
			$cbo_category 	= 'cbo_category'.$i;
			$cbo_item_group = 'cbo_item_group'.$i;
			$cbo_source 	= 'cbo_source'.$i;
			$txt_standard 	= 'txt_standard'.$i;
			if( $$cbo_category!=0 || $$cbo_item_group!=0 || $$cbo_source!=0 ||  $$txt_standard!="" )
			{ 
				if(trim($data_array)!="") $data_array .= ",";
				$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$$cbo_category.",".$$cbo_item_group.",".$$cbo_source.",".$$txt_standard.",".$userid.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		
		//echo $data_array."#####".$field_array;die;
		$rID=sql_insert("variable_inv_ile_standard",$field_array,$data_array,1);
		if($db_type==0)
		{
		  if($rID ){
			  mysql_query("COMMIT");  
			  echo "0**".$rID;
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
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		
		}
		disconnect($con);
		die;		
	}
 	else if ($operation==1)   // Update Here=========================================================
	{
		 
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		//echo "10**DELETE FROM variable_inv_ile_standard WHERE company_name=$cbo_company_name and variable_list=$cbo_variable_list";die;
		// delete here--------------- 
		$deleteSQL = execute_query("DELETE FROM variable_inv_ile_standard WHERE company_name=$cbo_company_name and variable_list=$cbo_variable_list",0);
		
		$id=return_next_id( "id", "variable_inv_ile_standard", 1 );		
		$field_array="id,company_name, variable_list, category, item_group, source, standard,inserted_by,insert_date";
		$rows = str_replace("'","",$row);
		$data_array="";
		for($i=1;$i<=$rows;$i++)
		{ 
			
			$cbo_category 	= 'cbo_category'.$i;
			$cbo_item_group = 'cbo_item_group'.$i;
			$cbo_source 	= 'cbo_source'.$i;
			$txt_standard 	= 'txt_standard'.$i;
			if( $$cbo_category!=0 || $$cbo_item_group!=0 || $$cbo_source!=0 ||  $$txt_standard!="" )
			{ 
				if(trim($data_array)!="") $data_array .= ",";
				$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$$cbo_category.",".$$cbo_item_group.",".$$cbo_source.",".$$txt_standard.",".$userid.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		
		//print_r($data_array);die;
		$rID=sql_insert("variable_inv_ile_standard",$field_array,$data_array,1);
		if($db_type==0)
		{
		  if( $rID ){
			  mysql_query("COMMIT");  
			  echo "0**".$rID;
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
	 else if ($operation==2)   // Delete Here===================================================
	 {
		  //no operation
	 }
}

if ($action=="save_update_delete_gate_out_hour")
{
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process )); 
   
 
   // echo "10**=".$cbo_item_category;die;
  if ($operation==0)  // Insert Here==============================================================================
  {
  
    $con = connect();
    if($db_type==0)
    {
      mysql_query("BEGIN");
    }	     
	
    $id=return_next_id( "id", "variable_settings_inventory", 1 );   
    $field_array="id,company_name,variable_list,TOLERANT_PERCENT,inserted_by,insert_date";

	
   
    $data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$txt_hour.",".$userid.",'".$pc_date_time."')";
    $id=$id+1;
     
    
    //echo $data_array."#####".$field_array;die;
    $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
    if($db_type==0)
    {
      if($rID ){
        mysql_query("COMMIT");  
        echo "0**".$rID;
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
          echo "0**".$rID;
        }
        else{
          oci_rollback($con);
          echo "10**".$rID;
        }
    
    }
    disconnect($con);
    die;    
  }
  else if ($operation==1)   // Update Here=========================================================
  {
     
    $con = connect();
    if($db_type==0)
    {
      mysql_query("BEGIN");
    }
	
     
    // delete here--------------- 
    $deleteSQL = execute_query("DELETE FROM variable_settings_inventory WHERE company_name=$cbo_company_name and variable_list=$cbo_variable_list",0);
    
    $id=return_next_id( "id", "variable_settings_inventory", 1 );   
    $field_array="id,company_name,variable_list,TOLERANT_PERCENT,inserted_by,insert_date";
   
    $data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$txt_hour.",".$userid.",'".$pc_date_time."')";
    $id=$id+1;
    //echo "10**".$data_array."#####".$field_array;die;
    $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1); 
    if($db_type==0)
    {
      if( $rID ){
        mysql_query("COMMIT");  
        echo "0**".$rID;
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
   else if ($operation==2)   // Delete Here===================================================
   {
      //no operation
   }   
}

if ($action=="save_update_delete_auto_transfer_rcv")
{
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process )); 
  $cbo_item_category=str_replace("'","",$cbo_item_category);

  if($cbo_item_category=='')
  {
	  $cbo_item_category=0;
  }
   // echo "10**=".$cbo_item_category;die;
  if ($operation==0)  // Insert Here==============================================================================
  {
  
    $con = connect();
    if($db_type==0)
    {
      mysql_query("BEGIN");
    }
	
    $check = is_duplicate_field( "id", "variable_settings_inventory", "status_active=1 and company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category" );
	if($check==1)
	{
		echo"11** Duplicate Data Not Allow";
		disconnect($con);
		die;
	}
	
    $id=return_next_id( "id", "variable_settings_inventory", 1 );   
    $field_array="id,company_name,variable_list,item_category_id,auto_transfer_rcv,inserted_by,insert_date";
   
    $data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_independent_con.",".$userid.",'".$pc_date_time."')";
    $id=$id+1;
     
    
    //echo $data_array."#####".$field_array;die;
    $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
    if($db_type==0)
    {
      if($rID ){
        mysql_query("COMMIT");  
        echo "0**".$rID;
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
          echo "0**".$rID;
        }
        else{
          oci_rollback($con);
          echo "10**".$rID;
        }
    
    }
    disconnect($con);
    die;    
  }
  else if ($operation==1)   // Update Here=========================================================
  {
     
    $con = connect();
    if($db_type==0)
    {
      mysql_query("BEGIN");
    }
	
    $check = is_duplicate_field( "id", "variable_settings_inventory", "status_active=1 and company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category and id <> $update_id" );
	if($check==1)
	{
		echo"11** Duplicate Data Not Allow";
		disconnect($con);
		die;
	}
    // delete here--------------- 
    $deleteSQL = execute_query("DELETE FROM variable_settings_inventory WHERE company_name=$cbo_company_name and item_category_id=$cbo_item_category and variable_list=$cbo_variable_list",0);
    
    $id=return_next_id( "id", "variable_settings_inventory", 1 );   
    $field_array="id,company_name,item_category_id,variable_list,auto_transfer_rcv,inserted_by,insert_date";
   
    $data_array.="(".$id.",".$cbo_company_name.",".$cbo_item_category.",".$cbo_variable_list.",".$cbo_independent_con.",".$userid.",'".$pc_date_time."')";
    $id=$id+1;
    //echo "10**".$data_array."#####".$field_array;die;
    $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1); 
    if($db_type==0)
    {
      if( $rID ){
        mysql_query("COMMIT");  
        echo "0**".$rID;
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
   else if ($operation==2)   // Delete Here===================================================
   {
      //no operation
   }   
}

if ($action=="save_update_delete_auto_lot_maintain")
{
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process )); 
  $cbo_item_category=str_replace("'","",$cbo_item_category);

  if($cbo_item_category=='')
  {
	  $cbo_item_category=0;
  }
   // echo "10**=".$cbo_item_category;die;
  if ($operation==0)  // Insert Here==============================================================================
  {
  
    $con = connect();
    if($db_type==0)
    {
      mysql_query("BEGIN");
    }
	
    $check = is_duplicate_field( "id", "variable_settings_inventory", "status_active=1 and company_name=$cbo_company_name and variable_list=$cbo_variable_list" );
	if($check==1)
	{
		echo"11** Duplicate Data Not Allow";
		disconnect($con);
		die;
	}
	
    $id=return_next_id( "id", "variable_settings_inventory", 1 );   
    $field_array="id,company_name,variable_list,auto_transfer_rcv,inserted_by,insert_date";
   
    $data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_independent_con.",".$userid.",'".$pc_date_time."')";
     
    
    //echo $data_array."#####".$field_array;die;
    $rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
    if($db_type==0)
    {
      if($rID ){
        mysql_query("COMMIT");  
        echo "0**".$id;
      }
      else{
        mysql_query("ROLLBACK"); 
        echo "10**".$id;
      }
    }
    
    if($db_type==2 || $db_type==1 )
    {
    
       if($rID )
          {
          oci_commit($con);   
          echo "0**".$id;
        }
        else{
          oci_rollback($con);
          echo "10**".$id;
        }
    
    }
    disconnect($con);
    die;    
  }
  else if ($operation==1)   // Update Here=========================================================
  {
     
    $con = connect();
    if($db_type==0)
    {
      mysql_query("BEGIN");
    }
	
    $check = is_duplicate_field( "id", "variable_settings_inventory", "status_active=1 and company_name=$cbo_company_name and variable_list=$cbo_variable_list and id <> $update_id" );
	if($check==1)
	{
		echo"11** Duplicate Data Not Allow";
		disconnect($con);
		die;
	}
    // delete here--------------- 
    $field_array="company_name*variable_list*auto_transfer_rcv*updated_by*update_date";
    $data_array="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_independent_con."*'".$userid."'*'".$pc_date_time."'";
	$rID = sql_update("variable_settings_inventory",$field_array,$data_array,"id",$update_id,0);
	
    if($db_type==0)
    {
      if( $rID ){
        mysql_query("COMMIT");  
        echo "0**".str_replace("'","",$update_id);
      }
      else{
        mysql_query("ROLLBACK"); 
        echo "10**".str_replace("'","",$update_id);
      }
    }
    
    if($db_type==2 || $db_type==1 )
    {   
       if($rID )
          {
          oci_commit($con);   
          echo "1**".str_replace("'","",$update_id);
        }
        else{
          oci_rollback($con);
          echo "10**".str_replace("'","",$update_id);
        }
    
    }
    disconnect($con);
    die;
   }
   else if ($operation==2)   // Delete Here===================================================
   {
      //no operation
   }   
}

if ($action=="save_update_delete_material_over_receive_control")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here==============================================================================
	{
 	
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "variable_inv_ile_standard", 1 );		
		$field_array="id,company_name, variable_list, category,over_rcv_percent,over_rcv_payment,over_rcv_percent_textile,inserted_by,insert_date";
		$rows = str_replace("'","",$row);
		$data_array="";
		for($i=1;$i<=$rows;$i++)
		{ 
			$cbo_category = 'cbo_category'.$i;
			$txt_over_rcv_percent = 'txt_over_rcv_percent'.$i;
			$txt_over_rcv_payment = 'txt_over_rcv_payment'.$i;
			$txt_over_rcv_percent_textile = 'txt_over_rcv_percent_textile'.$i;

			if(str_replace("'", "", $$txt_over_rcv_percent_textile)*1 !=0)
			{
				$textile_over_percent = $$txt_over_rcv_percent_textile;
			}else{
				$textile_over_percent =0;
			}
			
			if( $$cbo_category!=0 || $$txt_over_rcv_percent!=0 ||  $$txt_over_rcv_payment!="" || $textile_over_percent !=0)
			{ 
				if(trim($data_array)!="") $data_array .= ",";
				$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$$cbo_category.",".$$txt_over_rcv_percent.",".$$txt_over_rcv_payment.",".$textile_over_percent.",".$userid.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		
		//echo "10**".$data_array."#####".$field_array;die;
		$rID=sql_insert("variable_inv_ile_standard",$field_array,$data_array,1);
		if($db_type==0)
		{
		  if($rID ){
			  mysql_query("COMMIT");  
			  echo "0**".$rID;
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
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		
		}
		disconnect($con);
		die;		
	}
 	else if ($operation==1)   // Update Here=========================================================
	{
		 
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		// delete here--------------- 
		$deleteSQL = execute_query("DELETE FROM variable_inv_ile_standard WHERE company_name=$cbo_company_name and variable_list=$cbo_variable_list",0);
		
		$id=return_next_id( "id", "variable_inv_ile_standard", 1 );		
		$field_array="id,company_name, variable_list, category,over_rcv_percent,over_rcv_payment,over_rcv_percent_textile,inserted_by,insert_date";
		$rows = str_replace("'","",$row);
		$data_array="";
		for($i=1;$i<=$rows;$i++)
		{
			$cbo_category 	= 'cbo_category'.$i;
			$txt_over_rcv_percent = 'txt_over_rcv_percent'.$i;
			$txt_over_rcv_payment = 'txt_over_rcv_payment'.$i;
			$txt_over_rcv_percent_textile = 'txt_over_rcv_percent_textile'.$i;


			if(str_replace("'", "", $$txt_over_rcv_percent_textile)*1 !=0)
			{
				$textile_over_percent = $$txt_over_rcv_percent_textile;
			}else{
				$textile_over_percent =0;
			}


			if( $$cbo_category!=0  ||  $$txt_over_rcv_payment!="" ||  $$txt_over_rcv_percent!=0 )
			{ 
				if(trim($data_array)!="") $data_array .= ",";
				$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$$cbo_category.",".$$txt_over_rcv_percent.",".$$txt_over_rcv_payment.",".$textile_over_percent.",".$userid.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		
		//print_r($data_array);die;
		//echo "10**".$data_array."#####".$field_array;die;
		$rID=sql_insert("variable_inv_ile_standard",$field_array,$data_array,1);
		if($db_type==0)
		{
		  if( $rID ){
			  mysql_query("COMMIT");  
			  echo "0**".$rID;
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
	else if ($operation==2)   // Delete Here===================================================
	{
	  //no operation
	}	  
}

//save update delete for store method
if ($action=="save_update_delete_store_method")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here==============================================================================
	{
 	
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		$check = is_duplicate_field( "id", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category" );
		if($check==1)
		{
			echo"11";
			disconnect($con);
			exit();
		}
		
		$id=return_next_id( "id", "variable_settings_inventory", 1 );		
		$field_array="id, company_name, variable_list, item_category_id, store_method, inserted_by, insert_date";		
		$data_array="";		 
		$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_store_method.",".$userid.",'".$pc_date_time."')";
		$id=$id+1;
						
		//echo $field_array."#####".$data_array;die;
		$rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
		if($db_type==0)
		{
		  if($rID ){
			  mysql_query("COMMIT");  
			  echo "0**".$rID;
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
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			
		}
		disconnect($con);
		die;		
	}
 	else if ($operation==1)   // Update Here=========================================================
	{
		 
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		if(str_replace("'",$update_id)=="")
		{
			echo "10";disconnect($con);die;
		}
		
		$check = is_duplicate_field( "id", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category and id!=$update_id" );
		if($check==1)
		{
			echo"11";
			disconnect($con);
			exit();
		}
		 
		$field_array="company_name*variable_list*item_category_id*store_method*updated_by*update_date";		
		$data_array="";
		$data_array.="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_category."*".$cbo_store_method."*".$userid."*'".$pc_date_time."'"; 		
		//print_r($data_array);die;
		$rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id",$update_id,1);
		if($db_type==0)
		{
		  if( $rID ){
			  mysql_query("COMMIT");  
			  echo "0**".$rID;
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
	 else if ($operation==2)   // Delete Here===================================================
	 {
		 
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		 $deleteSQL = execute_query("DELETE FROM variable_settings_inventory WHERE id=$update_id","","");
		 if($db_type==0)
		{
		  if( $deleteSQL ){
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
			 if($deleteSQL )
			    {
					oci_commit($con);   
					echo "2**".$rID;
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

//save update delete for allocated quantity
if ($action=="save_update_delete_allocated")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here==============================================================================
	{
 	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$cbo_variable_list)==21)
		{
			$check = is_duplicate_field( "id", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category and status_active=1" );
			if ($check==1)
			{
				echo "11";
				disconnect($con);
				exit();
			}
				
			$id=return_next_id( "id", "variable_settings_inventory", 1 );		
			$field_array="id, company_name, variable_list, item_category_id, rack_balance, store_method, inserted_by, insert_date";		
			$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_rack_balance.",".$cbo_up_to.",".$userid.",'".$pc_date_time."')";
		}
		else
		{
			$check = is_duplicate_field( "id", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category" );
			if($check==1)
			{
				echo"11";
				disconnect($con);
				exit();
			}
			
			$id=return_next_id( "id", "variable_settings_inventory", 1 );		
			$field_array="id, company_name, variable_list, item_category_id, allocation, smn_allocation, sales_allocation, inserted_by, insert_date";		
			$data_array.="(".$id.",".$cbo_company_name.",".$cbo_variable_list.",".$cbo_item_category.",".$cbo_allocated.",".$cbo_smn_allocated.",".$cbo_sales_allocated.",".$userid.",'".$pc_date_time."')";
		}

		//echo $field_array."#####".$data_array;die;
		$rID=sql_insert("variable_settings_inventory",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);   
				echo "0**".$rID;
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
 	else if ($operation==1)   // Update Here=========================================================
	{
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
	
		if(str_replace("'","",$update_id)=="")
		{
			echo "10";
			disconnect($con);
			die;
		}
		
		if(str_replace("'","",$cbo_variable_list)==21)
		{
			$field_array="company_name*variable_list*item_category_id*rack_balance*store_method*updated_by*update_date";		
			$data_array.="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_category."*".$cbo_rack_balance."*".$cbo_up_to."*".$userid."*'".$pc_date_time."'"; 		
			$rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id",$update_id,1);
		}
		else
		{
			$check = is_duplicate_field( "id", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=$cbo_variable_list and item_category_id=$cbo_item_category and id!=$update_id" );
			if($check==1)
			{
				echo"11";
				disconnect($con);
				exit();
			}
		 
			$field_array="company_name*variable_list*item_category_id*allocation*smn_allocation*sales_allocation*updated_by*update_date";		
			$data_array.="".$cbo_company_name."*".$cbo_variable_list."*".$cbo_item_category."*".$cbo_allocated."*".$cbo_smn_allocated."*".$cbo_sales_allocated."*".$userid."*'".$pc_date_time."'"; 		
			
			$rID=sql_update("variable_settings_inventory",$field_array,$data_array,"id",$update_id,1);
		}
		
		if($db_type==0)
		{
		  if( $rID ){
			  mysql_query("COMMIT");  
			  echo "0**".$rID;
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
	else if ($operation==2)   // Delete Here===================================================
	{
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		 $deleteSQL=execute_query("DELETE FROM variable_settings_inventory WHERE id=$update_id",1);
		 if($db_type==0)
		{
		  if( $deleteSQL ){
			  mysql_query("COMMIT");  
			  echo "0**".$rID;
		  }
		  else{
			  mysql_query("ROLLBACK"); 
			  echo "10**".$rID;
		  }
		}
		
		if($db_type==2 || $db_type==1)
		{
			 if($deleteSQL )
			    {
					oci_commit($con);   
					echo "2**".$rID;
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

//for update get data
if($action=="load_data_for_store_method_form")
{
	 $nameArray=sql_select( "select id,company_name,variable_list,item_category_id,user_given_code_status, store_method from variable_settings_inventory where id='$data'" );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('cbo_item_category').value = '".($inf[csf("item_category_id")])."';\n"; 
		  echo "document.getElementById('cbo_store_method').value = '".($inf[csf("store_method")])."';\n";
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory_store_method',1);\n";  
	  }
	  exit();
}

//for update get data allocation------------------
if($action=="load_data_for_allocation_form")
{
	//echo "select id,company_name,variable_list,item_category_id,user_given_code_status, allocation from variable_settings_inventory where id='$data'";
	$nameArray=sql_select( "SELECT id AS ID, company_name AS COMPANY_NAME, variable_list AS VARIABLE_LIST, item_category_id AS ITEM_CATEGORY_ID, allocation AS ALLOCATION, smn_allocation AS SMN_ALLOCATION, sales_allocation AS SALES_ALLOCATION FROM variable_settings_inventory WHERE id='".$data."'",1 );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_name').value = '".($inf['COMPANY_NAME'])."';\n"; 
		echo "document.getElementById('cbo_variable_list').value = '".($inf['VARIABLE_LIST'])."';\n";
		echo "document.getElementById('cbo_item_category').value = '".($inf['ITEM_CATEGORY_ID'])."';\n"; 
		echo "document.getElementById('cbo_allocated').value = '".($inf['ALLOCATION'])."';\n";
		echo "document.getElementById('cbo_smn_allocated').value = '".($inf['SMN_ALLOCATION'])."';\n";
		echo "document.getElementById('cbo_sales_allocated').value = '".($inf['SALES_ALLOCATION'])."';\n";
		echo "document.getElementById('update_id').value = '".($inf['ID'])."';\n";    
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory_allocation',1);\n";  
	}
	exit();
}

//load_data_for_yarn_test mendetory/approval
if($action=="load_data_for_yarn_test")
{
	$nameArray=sql_select( "SELECT id AS ID, company_name AS COMPANY_NAME, variable_list AS VARIABLE_LIST, yes_no AS YES_NO FROM variable_settings_inventory WHERE id='".$data."'",1 );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_name').value = '".($inf['COMPANY_NAME'])."';\n"; 
		echo "document.getElementById('cbo_variable_list').value = '".($inf['VARIABLE_LIST'])."';\n";
		echo "document.getElementById('cbo_is_yarn_test_mandatory').value = '".($inf['YES_NO'])."';\n"; 
		echo "document.getElementById('update_id').value = '".($inf['ID'])."';\n";    
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'func_vs_yarn_test_mandatory',1);\n";  
	}
	exit();
}

//for update get data allocation------------------
if($action=="load_data_for_rack_balance_form")
{
	//echo "select id,company_name,variable_list,item_category_id,user_given_code_status, allocation from variable_settings_inventory where id='$data'";
	 $nameArray=sql_select( "select id,company_name,variable_list,item_category_id,user_given_code_status, rack_balance, store_method from variable_settings_inventory where id='$data'",1 );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('cbo_item_category').value = '".($inf[csf("item_category_id")])."';\n"; 
		  echo "document.getElementById('cbo_rack_balance').value = '".($inf[csf("rack_balance")])."';\n";
		  echo "document.getElementById('cbo_up_to').value = '".($inf[csf("store_method")])."';\n";
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_inventory_allocation',1);\n";  
	  }
	  exit();
}

if($action=="load_data_for_ack")
{
	//echo "select id,company_name,variable_list,item_category_id,user_given_code_status, allocation from variable_settings_inventory where id='$data'";
	 $nameArray=sql_select( "select id,company_name,variable_list,item_category_id,user_given_code_status, auto_transfer_rcv from variable_settings_inventory where id='$data'",1 );
	  foreach ($nameArray as $inf)
	  {
		  echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_name")])."';\n"; 
		  echo "document.getElementById('cbo_variable_list').value = '".($inf[csf("variable_list")])."';\n";
		  echo "document.getElementById('cbo_item_category').value = '".($inf[csf("item_category_id")])."';\n"; 
		  echo "document.getElementById('cbo_independent_con').value = '".($inf[csf("auto_transfer_rcv")])."';\n";
		  echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		  echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_variable_settings_auto_transfer_rcv',1);\n";  
	  }
	  exit();
}
?>