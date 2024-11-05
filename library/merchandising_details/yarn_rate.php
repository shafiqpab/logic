<?
	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//----------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Yarn Rate", "../../", 1, 1,$unicode,'','');
?>
 
<script type="text/javascript">

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_yarn_rate( operation )
	{
		if (form_validation('cbo_supplier*cbocountcotton*cbocompone*percentone*cbotypecotton*txt_rate*txt_date','Supplier Name*Yarn Count*Composition*Percent*Type*Rate/KG*Effective Date')==false)
		{
			return;
		}
		
		else 
		{
			
			var data_all=get_submitted_data_string('cbo_supplier*cbocountcotton*cbocompone*percentone*cbotypecotton*txt_rate*txt_date*update_id',"../../");
			
		    var data="action=save_update_delete&operation="+operation+data_all;
			freeze_window(operation);
			http.open("POST","requires/yarn_rate_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_rate_reponse;
		}
	}
	
	function fnc_yarn_rate_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			
				show_msg(trim(reponse[0]));
				show_list_view(reponse[1],'search_list_view','yarn_count_container','../merchandising_details/requires/yarn_rate_controller','setFilterGrid("list_view",-1)');
				// reset_form('cbocountcotton','','');
        $('#cbocountcotton').val('');
				set_button_status(0, permission, 'fnc_yarn_rate',1);
				release_freezing();
		}
	}
	


function openmypage_comp(inc)
  {
    var page_link="requires/yarn_rate_controller.php?action=composition_popup&inc="+inc;
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Composition Popup", 'width=480px,height=350px,center=1,resize=1,scrolling=0','../');
    emailwindow.onclose=function()
    {
      
      var hidcompid=this.contentDoc.getElementById("hidcompid").value;
      var hidcompname=this.contentDoc.getElementById("hidcompname").value;
      $('#cbocompone').val(hidcompid);
      $('#txtcompone').val(hidcompname);
      
    }
  }






</script>
</head>	
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%; position:relative; margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../../",$permission);  ?>	     
        <form name="yarnrate_1" id="yarnrate_1" autocomplete="off">
            <fieldset style="width:1080px;">
                <legend>Yarn Count Determination </legend>
                
                <table style="border:none" cellpadding="0" cellspacing="2" border="0">
                    <thead class="form_table_header">
                        <tr align="center" >
                   <td width="300" class="must_entry_caption">
                       Supplier Name
                   </td>
                   <td width="100" class="must_entry_caption">
                       Yarn Count
                   </td>
                    <td width="350" class="must_entry_caption">
                      Composition
                    </td>
                    <td width="40" class="must_entry_caption">
                      %
                    </td>
                     <td width="110" class="must_entry_caption">
                        Type
                      </td>
                      <td width="50" class="must_entry_caption">
                      Rate/KG
                    </td>
                    <td width="70" class="must_entry_caption">
                        Effective Date
                      </td>
                   </tr>
                    
                   </thead>
                    	<tr>
                        <td>
                         <?  echo create_drop_down( "cbo_supplier", 300, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );   ?>
                        </td>
                        <td>
                        <? 
						echo create_drop_down( "cbocountcotton", 100, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1," -- Select Count --", '', '','','' ); 
						?>   
                        </td>
                        <td>
                          <input type="text" id="txtcompone"  name="txtcompone"  class="text_boxes" style="width:350px" value="" readonly placeholder="Browse" onDblClick="openmypage_comp(1);" />
                            <input type="hidden" id="cbocompone"  name="cbocompone" class="text_boxes" style="width:50px" value="" />
                        </td>
                        <!-- <td>
                       <?  //echo create_drop_down( "cbocompone", 350, $composition,"", 1, "-- Select --", '', "",'','' ); ?> 
                        </td> -->
                        <td>
                       <input type="text" id="percentone"  name="percentone" onChange="" class="text_boxes" style="width:40px"  value="" />           
                        </td>
                        <td>
						  <?  
                             echo create_drop_down( "cbotypecotton", 110, $yarn_type,"", 1, "-- Select --", '', '','','' ); 
                          ?>                  
                        </td>
                        <td>
                       <input type="text" id="txt_rate"  name="txt_rate" onChange="" class="text_boxes_numeric" style="width:50px"  value="" />           
                        </td>
                        <td>
                       <input type="text" id="txt_date"  name="txt_date" onChange="" class="datepicker" style="width:70px"  value="" />           
                        </td>
                        </tr>
                      
                        <tr>
                        <td colspan="7" align="center" class="button_container">
                         <? 
                        echo load_submit_buttons( $permission, "fnc_yarn_rate", 0,0 ,"reset_form('yarnrate_1','','')",1);
                        ?> 
                            <input type="hidden" id="update_id" value=""/> 
                        </td>				
                        </tr>
                    </table>
                    </fieldset>
                    </form>	
                     <fieldset style="width:1080px;">
                    <div id="yarn_count_container">
                    <?
						$lib_sup=return_library_array("select supplier_name,id from lib_supplier", "id", "supplier_name");
					    $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
						$sql="select id,supplier_id,yarn_count,composition,percent,yarn_type,rate,effective_date from lib_yarn_rate where status_active=1 and is_deleted=0 order by id";
						$arr=array (0=>$lib_sup,1=>$lib_yarn_count,2=>$composition,4=>$yarn_type);
						echo  create_list_view ( "list_view", "Supplier Name,Yarn Count,Composition,Percent,Type,Rate/KG,Effective Date", "250,100,300,40,110,50,70","1080","350",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'",1, "supplier_id,yarn_count,composition,0,yarn_type,0,0", $arr , "supplier_id,yarn_count,composition,percent,yarn_type,rate,effective_date", "../merchandising_details/requires/yarn_rate_controller",'setFilterGrid("list_view",-1);','0,0,0,1,0,2,3') ;
						?>
                    </div>
                    </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
