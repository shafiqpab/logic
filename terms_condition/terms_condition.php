<?
/*-------------------------------------------- Comments
Version (MySql)          :  V2
Version (Oracle)         :  V1
Purpose			         :  This form will create for Terms & Contition  of any page.
Functionality	         :	
JS Functions	         :
Created by		         :  Md. Saidul Islam Reza 
Creation date 	         :  10 Augutst, 2017
Requirment Client        :  
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :              
DB Script                : 
Updated by 		         : 
Update date		         : 
QC Performed BY	         :		
QC Date			         :	
Comments		         : $fso_id parameter will only come from batch creation page if user want to load FSO remarks in Terms and Condition List
*/

function terms_condition($page_id,$sys_id,$path,$fso_id=0,$buyer_id_selector=''){ //terms_condition(108,'txt_booking_no','../../');
	echo "<input type='button' id='set_button' class='image_uploader' style='width:140px;' value='Terms & Condition/Notes' onClick=\"open_terms_condition_popup(".$page_id.",'".$sys_id."','".$path."','Terms Condition','".$fso_id."','".$buyer_id_selector."')\" />";
}
?>
<script type="text/javascript">
function open_terms_condition_popup(page_id,sys_id,path,title,fso_id,buyer_id_selector){
	// alert(buyer_id);
		var sys_id=document.getElementById(sys_id).value;
		//var buyer_id=document.getElementById(buyer_id_selector).value;
		//alert(fso_id);
		if (fso_id!=0)
		{
			var fso_id=document.getElementById(fso_id).value;
		}
		if (buyer_id_selector!=0)
		{
			var buyer_id=document.getElementById(buyer_id_selector).value;
		//alert(fso_id);
		}
		
		if (sys_id==""){
			alert("Please Save First")
			return;
		}	
		else{
			//page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', path+'terms_condition/requires/terms_condition_controller.php?action=terms_condition_popup&page_id='+page_id+'&sys_id='+sys_id+'&fso_id='+fso_id+'&buyer_id='+buyer_id, title, 'width=770px,height=470px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
			}
		}
	}

</script>
