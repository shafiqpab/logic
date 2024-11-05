
 																	// To Change Background Color of Validated Field\
 
jQuery(".text_boxes").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});

 jQuery(".text_area").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});

  jQuery(".combo_boxes").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
});

  jQuery(".text_boxes_numeric").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});
jQuery(".datepicker").click(function() {
    var contentPanelId = jQuery(this).attr("id");
    if(document.getElementById(contentPanelId).style.backgroundColor!="")
		document.getElementById(contentPanelId).style.backgroundColor="";
		 
});
 // To Change Background Color of Validated Field ends
 
 		/*																				//Numeric Text Box Validation
 jQuery(".text_boxes_numeric").keydown(function(e) {
	var c = String.fromCharCode(e.which);
 	var evt = (e) ? e : window.event;
      var key = (evt.keyCode) ? evt.keyCode : evt.which;
 

      if(key != null) 
        key = parseInt(key, 10);
		if (isUserFriendlyChar(key)) return false;
		
		
 alert(c+"="+ e.newValue+"="+key);
	 
      return true;
   
});
*/
jQuery(".text_boxes_numeric").keypress(function(e) {
	
	var c = String.fromCharCode(e.which);
 	var evt = (e) ? e : window.event;
    var key = (evt.keyCode) ? evt.keyCode : evt.which;
	if(key != null) key = parseInt(key, 10);
	var allowed = '1234567890.'; // ~ replace of Hash(#)
		if (isUserFriendlyChar(key)) return true
		else if (key != 8 && key !=0 && allowed.indexOf(c) < 0) 
			return false;
		else if (!numeric_valid( $(this).attr('id'), 0))
			return false;
	  
   
});

jQuery(".text_boxes_numeric").blur(function(e) {
	numeric_valid( $(this).attr('id'), 1)
});

function numeric_valid( id, from)
{
	var txt=$('#'+id).val();//.split('.');
	var dotposl=txt.lastIndexOf(".");
	var dotposf=txt.indexOf(".");
	if (dotposl!=dotposf)
	{
		var txt_d=$('#'+id).val().substr(0,dotposl);
		$('#'+id).val(txt_d);//alert(txt_d);
		numeric_valid( id, from )
	}
	else return true;
}

function isUserFriendlyChar(val) {
      	//Backspace, Tab, Enter, Insert, and Delete
      if(val == 8 || val == 9 || val == 13 || val == 46 )// || val == 45 Insert
        return true;
	// Ctrl, Alt, CapsLock, Home, End, and Arrows
     // if((val > 16 && val < 21) || (val > 34 && val < 41))
     //   return true;
	// The rest
      return false;
}
 //Numeric Text Box Validation Ends
 
 
																		// Special Character Validation
	jQuery(".text_boxes").keypress(function(e) {
		 var c = String.fromCharCode(e.which);
		 //alert(c);
		 var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+[]{};: '; // ~ replace of Hash(#)()
		 if (e.which != 8 && e.which !=0 && allowed.indexOf(c) < 0) 
			return false; 
		
	});

	$('.text_boxes').blur(function(e) {
	    var target = e.target || e.srcElement;
		if (document.getElementById(target.id).value!="") document.getElementById(target.id).value=document.getElementById(target.id).value.replace("#","~");
		if (document.getElementById(target.id).value!="") document.getElementById(target.id).value=document.getElementById(target.id).value.replace("(","[");
		if (document.getElementById(target.id).value!="") document.getElementById(target.id).value=document.getElementById(target.id).value.replace(")","]");
	});
																	 
	jQuery(".text_area").keypress(function(e) {
		 var c = String.fromCharCode(e.which);
		 var allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+[]{};: '; // ~ replace of Hash(#)()
		 if (e.which != 8 && e.which !=0 && allowed.indexOf(c) < 0) 
			return false; 
		
	});

  	$('.text_area').blur(function(e) {
	    var target = e.target || e.srcElement;
   		document.getElementById(target.id).value=document.getElementById(target.id).value.replace("#","~");
		document.getElementById(target.id).value=document.getElementById(target.id).value.replace("(","[");
		document.getElementById(target.id).value=document.getElementById(target.id).value.replace(")","]");
	 });
  
 
 // Special Character Validation Ends
 
	 															 // Global Date Picker Initialisaton
		  $( ".datepicker" ).datepicker({
				dateFormat: 'dd-mm-yy',
				changeMonth: true,
				changeYear: true
			});
				
		$('.timepicker').timepicker({
				H: true
			});
	 // Datapickker ENds
 
		function set_bangla()
		{
			$(".bangla").bnKb({
				'switchkey': {"webkit":"k","mozilla":"y","safari":"k","chrome":"k","msie":"y"},
				'driver': phonetic
			});
		}
         
		/*$( ".combo_boxes" ).each(function( index ) {
			if($('#'+this.id+' option').length==2)
			{
				if($('#'+this.id+' option:first').val()==0)
				{
					$('#'+this.id).val($('#'+this.id+' option:last').val());
					//alert($('#'+this.id+' option:last').val());
					eval( $('#'+this.id).attr('onchange') );
				}
			}
			else if($('#'+this.id+' option').length==1)
			{
				$('#'+this.id).val($('#'+this.id+' option:last').val());
				eval($('#'+this.id).attr('onchange'));
			}
		});*/
	
	$(".must_entry_caption").each(function( index ) {
		var ht=" <font color='blue'>"+$(this).html()+"</font>";
		$(this).html(ht);
		$(this).attr('title','Must Entry Field.');
	});
	
	var allowedchars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.-,%@!/\<>?+[]{};: ';
	$(".text_boxes").each(function( index ) {
		 var ttl=$(this).attr('title');
		 if(!ttl) var ttl=""; else ttl=ttl+";"
		$(this).attr('title',ttl+'  Allowed Characters: '+allowedchars);
	});
	$(".text_area").each(function( index ) {
		 var ttl=$(this).attr('title');
		 if(!ttl) var ttl=""; else ttl=ttl+";"
		$(this).attr('title',ttl+'  Allowed Characters: '+allowedchars);
	});
	
	//if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
		// alert("Android");
	//}
	
	/*
	if( select_job_year_all==1 )
	{
		$('#cbo_year').val(0);
	} 
	*/
	
	
	
	
if(isMobile.any()) {  // 09-03-2015
   //alert("This is a Mobile Device");
   $(".text_boxes").each(function( index ) {
		 var ttl=$(this).attr('onDblClick');
		 if(!ttl) var ttl=""; else ttl=ttl+";"
		$(this).attr('onClick',ttl);
	});
	
	$(".text_boxes_numeric").each(function( index ) {
		 var ttl=$(this).attr('onDblClick');
		 if(!ttl) var ttl=""; else ttl=ttl+";"
		$(this).attr('onClick',ttl);
	});
}

 
if( $('.form_caption').html()!=null)
{
	//alert ($('.form_caption').html());
	window.parent.document.title = $('.form_caption').html();
}
else
{
	if( $("a.active").html()!=null)
	{
		window.parent.document.title = $("a.active").html();
	}
	else
	{
		window.parent.document.title = 'Logic ERP Solution';
	}
}

var first_values=new Array;
var first_sts = new Array;
var check_disable=new Array;
var check_value = new Array;
function set_field_level_access( company )
{
	$.each( first_sts, function( keys, values ) {
		if(jQuery.inArray(values.f_titles, check_disable) == -1)
		{
			check_disable.push(values.f_titles);
			if(values.f_vals==undefined)
				$('#'+values.f_titles).attr('disabled',false);
			else
				$('#'+values.f_titles).attr('disabled',true);
		}
		
	});
	$.each( first_values, function( keys, values ) {
		
		if(jQuery.inArray(values.f_title, check_value) == -1)
		{
			check_value.push(values.f_title);
			$('#'+values.f_title).val(values.f_val);
		}
		
		
	});
	check_value.length=0;
	check_disable.length=0;
		
	if( field_level_data[company]==undefined){  
		return; 
	}
	
	
	if(company!=0)
	{
		
		$.each( field_level_data[company], function( key, value ) {
		 	
			if( value['is_disable']==1) {
				first_sts.push({
					f_titles: key, 
					f_vals:  $('#'+key).attr('disabled')
				});
				$('#'+key).attr('disabled',true);
			}
			else
			{
				first_sts.push({
					f_titles: key, 
					f_vals:  $('#'+key).attr('disabled')
				});
				$('#'+key).attr('disabled',false);
			}
			if(value['defalt_value']!='') 
			{
				 first_values.push({
					f_title: key, 
					f_val:  $('#'+key).val()
				});
				$('#'+key).attr('value',value['defalt_value']);
			}
		});
	}
	 
}

jQuery('.combo_boxes').live('change', function(){  
	var str= $(this).attr('id');
	if( str.indexOf("company") >-1)
		set_field_level_access( $(this).val() );

});

// set_field_level_access( 1 )