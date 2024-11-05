<?
	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//----------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Yarn Count Determination", "../../", 1, 1,$unicode,'','');
?>
 
<script type="text/javascript">

		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
		
	var permission='<? echo $permission; ?>';
	
	function fnc_yarn_count_determination( operation )
	{
		if (form_validation('cbofabricnature*txtconstruction*cbocompone','Fab Nature*Constrution*Comp 1')==false)
		{
			return;
		}
		else // Save Here
		{
			eval(get_submitted_variables('cbofabricnature*txtconstruction*cbocompone*percentone*cbocomptwo*percenttwo*txtgsmweight*cbocolortype*cbocountcotton*cbotypecotton*cbocountdenier*cbotypedenier*stichlength*cbostatus*updateid'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbofabricnature*txtconstruction*cbocompone*percentone*cbocomptwo*percenttwo*txtgsmweight*cbocolortype*cbocountcotton*cbotypecotton*cbocountdenier*cbotypedenier*stichlength*cbostatus*updateid',"../../");
			freeze_window(operation);
			http.open("POST","requires/yarn_count_determination_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_count_determination_reponse;
		}
	}
	
	function fnc_yarn_count_determination_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText)
			var reponse=http.responseText.split('**');
			show_msg(reponse[0]);
			show_list_view(reponse[1],'search_list_view','yarn_count_container','../merchandising_details/requires/yarn_count_determination_controller','setFilterGrid("list_view",-1)');
			reset_form('yarncountdetermination_1','','');
			set_button_status(0, permission, 'fnc_yarn_count_determination');
			release_freezing();
		}
	}
	
/*function set_cons_uom(trim_group_id)
{
	
	var http = createObject();
	http.onreadystatechange = function() {
		if( http.readyState == 4 && http.status == 200 ) {
			document.getElementById('cbo_cons_uom').value = http.responseText;
		}
	}
	http.open( "GET","../merchandising_details/requires/yarn_count_determination_controller.php?trim_group_id=" +trim_group_id+ "&action=set_cons_uom" , false );
	http.send();
	
}*/

function control_composition(id,td,type)
{
	var cbocompone=(document.getElementById('cbocompone').value);
	var cbocomptwo=(document.getElementById('cbocomptwo').value);
	var percentone=(document.getElementById('percentone').value)*1;
	var percenttwo=(document.getElementById('percenttwo').value)*1;
	//var row_num=$('#tbl_yarn_cost tr').length-1;
	
	if(type=='percent_one' && percentone>100)
	{
		alert("Greater Than 100 Not Allwed")
		document.getElementById('percentone').value="";
	}
	
	if(type=='percent_one' && percentone<=0)
	{
		alert("0 Or Less Than 0 Not Allwed")
		document.getElementById('percentone').value="";
		document.getElementById('percentone').disabled=true;
		document.getElementById('cbocompone').value=0;
		document.getElementById('cbocompone').disabled=true;
		document.getElementById('percenttwo').value=100;
	    document.getElementById('percenttwo').disabled=false;
		document.getElementById('cbocomptwo').disabled=false;

	}
	if(type=='percent_one' && percentone==100)
	{
		document.getElementById('percenttwo').value="";
		document.getElementById('cbocomptwo').value=0;
		document.getElementById('percenttwo').disabled=true;
		document.getElementById('cbocomptwo').disabled=true;

	}
	
	if(type=='percent_one' && percentone < 100 && percentone > 0 )
	{
		document.getElementById('percenttwo').value=100-percentone;
	    document.getElementById('percenttwo').disabled=false;
		document.getElementById('cbocomptwo').disabled=false;
		//document.getElementById('cbocomptwo').value=0;
	}
	
	if(type=='comp_one' && cbocompone==cbocomptwo  )
	{
		alert("Same Composition Not Allowed");
		document.getElementById('cbocompone').value=0;
		//document.getElementById('percenttwo').value=100-percentone;
		//document.getElementById('cbocomptwo').value=0;
	}
	
	
	
	
	
	if(type=='percent_two' && percenttwo>100)
	{
		alert("Greater Than 100 Not Allwed")
		document.getElementById('percenttwo').value="";
		//document.getElementById('cbocompone').value=0;
	}
	if(type=='percent_two' && percenttwo<=0)
	{
		alert("0 Or Less Than 0 Not Allwed")
		document.getElementById('percenttwo').value="";
		document.getElementById('percenttwo').disabled=true;
		document.getElementById('cbocomptwo').value=0;
		document.getElementById('cbocomptwo').disabled=true;
		document.getElementById('percentone').value=100;
		document.getElementById('percentone').disabled=false;
		document.getElementById('cbocompone').disabled=false;
	}
	if(type=='percent_two' && percenttwo==100)
	{
		document.getElementById('percentone').value="";
		document.getElementById('cbocompone').value=0;
		document.getElementById('percentone').disabled=true;
		document.getElementById('cbocompone').disabled=true;
	}
	
	if(type=='percent_two' && percenttwo<100 && percenttwo>0)
	{
		document.getElementById('percentone').value=100-percenttwo;
		document.getElementById('percentone').disabled=false;
		document.getElementById('cbocompone').disabled=false;

		//document.getElementById('cbocompone').value=0;
	}
	
	if(type=='comp_two' && cbocomptwo==cbocompone)
	{
		alert("Same Composition Not Allowed");
		document.getElementById('cbocomptwo').value=0;
		//document.getElementById('percentone').value=100-percenttwo;
		//document.getElementById('cbocompone').value=0;
	}
}

function change_caption( value, td_id )
{
	if(value==2)
	{
		document.getElementById(td_id).innerHTML="GSM";
	}
	else
	{
		document.getElementById(td_id).innerHTML="Weight";
	}
	
}
</script>
</head>	
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%; position:relative; margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../../",$permission);  ?>	     
        
        <form name="yarncountdetermination_1" id="yarncountdetermination_1" autocomplete="off">
            <fieldset style="width:1080px;">
                <legend>Yarn Count Determination </legend>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
                    <thead>
                    	<tr>
                        	<th width="100" class="must_entry_caption">Fab Nature</th> <th width="100" class="must_entry_caption">Construction</th><th  width="100" class="must_entry_caption">Comp 1</th><th  width="50">%</th><th width="90">Comp 2</th><th width="50">%</th>  <th width="80" id="gsmweight_caption">GSM</th> <th width="100">Color Range</th> <th width="70"> Cotton Count</th><th width="100"> Cotton Type</th> <th width="70"> Denier Count</th><th width="100"> Denier Type</th> <th width="75">Stich Length</th><th width="95">Status</th><th width=""></th>
                        </tr>
                    </thead>
                        <tr id="yarncost_1" align="center">
                                   <td>
								   <?  echo create_drop_down( "cbofabricnature", 100, $item_category,"", 0, "", '', "change_caption( this.value, 'gsmweight_caption' );",$disabled,"2,3" ); ?>
                                   </td>
                                   <td>
                                    <input type="text" id="txtconstruction"  name="txtconstruction" class="text_boxes" style="width:95px" value=""  />
                                   </td>
                                   
                                    <td><?  echo create_drop_down( "cbocompone", 100, $composition,"", 1, "-- Select --", '', "control_composition(1,this.id,'comp_one')",'','' ); ?></td>
                                   <td>
                                    <input type="text" id="percentone"  name="percentone" class="text_boxes" style="width:50px" onChange="control_composition(1,this.id,'percent_one')" value="" />
                                    </td>
                                    <td><?  echo create_drop_down( "cbocomptwo", 90, $composition,"", 1, "-- Select --", '', "control_composition(1,this.id,'comp_two')",'','' ); ?></td>
                                    <td>
                                    <input type="text" id="percenttwo"  name="percenttwo" class="text_boxes" style="width:50px" onChange="control_composition(1,this.id,'percent_two')" value="" />
                                    </td>
                                    <td>
                                    <input type="text" id="txtgsmweight" name="txtgsmweight" class="text_boxes_numeric" style="width:80px"  value=""  /> 
                                    </td>
                                   <td><?  echo create_drop_down( "cbocolortype", 100, $color_range,"", 1, "-- Select --", '', "",$disabled,"" ); ?></td>

                                    <td>
									<? 
									echo create_drop_down( "cbocountcotton", 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --", '', '','','' ); 
									?>
                                    </td>
                                    <td>
									<?  
									echo create_drop_down( "cbotypecotton", 100, $yarn_type,"", 1, "-- Select --", '', '','','' ); 
									?>
                                    </td>
                                    <td>
									<? 
									echo create_drop_down( "cbocountdenier", 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --", '', '','','' ); 
									?>
                                    </td>
                                    <td><?  echo create_drop_down( "cbotypedenier", 100, $yarn_type,"", 1, "-- Select --", '', '','','' ); ?></td>
                                    <td>
                                    <input type="text" id="stichlength" name="stichlength" class="text_boxes_numeric" style="width:60px" value=""> 
                                    </td>
                                    <td width="95"><? echo create_drop_down( "cbostatus", 95, $row_status,"", 0, "0", '', '','','' );  ?></td>  
                                    <td>
                                    <input type="hidden" id="updateid" name="updateid"  class="text_boxes" style="width:20px" value=""  />                                    </td> 
                                </tr>
                                <tr>
                                    <td colspan="15" align="center" class="button_container">
                                        <? 
                                         echo load_submit_buttons( $permission, "fnc_yarn_count_determination", 0,0 ,"reset_form('yarncountdetermination_1','','')",1);
                                        ?> 
                                    </td>				
                                </tr>	
                                <tr>
                                    <td colspan="15" align="center" id="">
                                    
                                    </td>				
                                </tr>	
                    </table>
                    <div id="yarn_count_container">
                    <?
									  $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	                                  $arr=array (0=>$item_category,2=>$composition, 4=>$composition, 7=>$color_range,8=>$lib_yarn_count,9=>$yarn_type, 10=>$lib_yarn_count,11=>$yarn_type,13=>$row_status);
                                       echo  create_list_view ( "list_view", "Fab Nature, Construction,Comp-1,%,Comp-1,%,GSM/Weight,Color Range,Cotton Count,Cotton Type,Denier Count,Denier Type,Stich Length,Status", "100,100,100,50,90,50,80,100,70,100,70,100,75,95","1230","350",0, "select fab_nature_id,construction,copm_one_id,percent_one,copm_two_id, percent_two,gsm_weight,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id,stich_length,status_active,id from  lib_yarn_count_determination  where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'",1, "fab_nature_id,0,copm_one_id,0,copm_two_id,0,0,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id,0,status_active", $arr , "fab_nature_id,construction,copm_one_id,percent_one,copm_two_id,percent_two,gsm_weight,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id,stich_length,status_active", "../merchandising_details/requires/yarn_count_determination_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,0') ;
									   ?>
                    </div>
            </fieldset>
        </form>	
        
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
