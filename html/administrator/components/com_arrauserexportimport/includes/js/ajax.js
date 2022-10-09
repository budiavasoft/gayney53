function saveEmailJomSocial(){
	form = document.emailForm;
	subject_template = form.subject_template.value;
	from_email = form.from_email.value;
	from_name = form.from_name.value;
	sitename = form.sitename.value;
	email_template = form.email_template.value;
	send_email_to_import = form.send_email_to_import;
	
	if(send_email_to_import.checked == true){
		var message = new Array();
		var ids = new Array();
		var message_text = "";
			
		if(subject_template.trim().length == 0){
			message[message.length]= "Email subject";
			ids[ids.length] = "subject_template";
		}
	
		if(from_email.trim().length == 0){
			message[message.length]= "from email";
			ids[ids.length] = "from_email";
		}
		
		if(from_name.trim().length == 0){
			message[message.length]= "from name";
			ids[ids.length] = "from_name";
		}
		
		if(sitename.trim().length == 0){
			message[message.length]= "site name";
			ids[ids.length] = "sitename";
		}
		
		if(email_template.trim().length == 0){
			message[message.length]= "body email";
			ids[ids.length] = "email_template";
		}
		
		if(message.length != 0){
			for(i=0; i<message.length; i++){
				if(document.getElementById(ids[i])) {document.getElementById(ids[i]).style.border = "1px solid #FF0000";}
				if(i != message.length-1){
					message_text += "'{"+message[i] + "}', ";
				} 
				else{ 
					message_text += "'{"+message[i]+"}'"; 
				}
			}
			alert("Field(s) "+message_text+" is(are) not completed.");
			return false;
		}
		var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
		if (!filter.test(from_email)) {
			alert('Please provide a valid email address');
			return false;
		}		
	}		
	
	var myAjax = new Ajax('components/com_arrauserexportimport/includes/js/functions.php?task=save_email_jomsocial&subject_template='+subject_template+
						  '&from_email='+from_email+
						  '&from_name='+from_name+
						  '&sitename='+sitename+
						  '&email_template='+email_template+
						  "&send_email_to_import="+send_email_to_import.checked, 
			   { 	
				onSuccess: function(response) {				  	
				   to_be_replaced = document.getElementById('message_error');
				   prefix = response.slice(0,1);
				   if(prefix!="1"){
					  to_be_replaced.style.color="#FF0000";//error-red
					  to_be_replaced.style.fontWeight="bold";
				   }
				   else{					   
					  to_be_replaced.style.color="#0B55C4"; //ok-blue
					  to_be_replaced.style.fontWeight="bold";
				   }
				   to_be_replaced.innerHTML = response.slice(1, response.length);			   
					}
				});
    myAjax.request();		
    return true;
	
}

function showAllActiveUsers(){
	   var myAjax = new Ajax('components/com_arrauserexportimport/includes/js/functions.php?task=all_active_users', 
		   { 	
			onSuccess: function(response) {
			   to_be_replaced = document.getElementById('active_counts');
			   to_be_replaced.innerHTML = response;
		        }
		    });
	   myAjax.request();		
	   return true;	
}

function showAtLeastOneVisit(){
	   var myAjax = new Ajax('components/com_arrauserexportimport/includes/js/functions.php?task=at_least_one_visit', 
		   { 	
			onSuccess: function(response) {
			   to_be_replaced = document.getElementById('at_least_one_visit');
			   to_be_replaced.innerHTML = response;			   
		        }
		    });
	   myAjax.request();		
	   return true;	
}

function truncateAllTables(){
	if(confirm('Are you shore that you what to truncate this tables? This will empty all existing records in those tables.')){
		var myAjax = new Ajax('components/com_arrauserexportimport/includes/js/functions.php?task=truncate_tables', 
			   { 	
				onSuccess: function(response) {
				   to_be_replaced = document.getElementById('truncate_message');
				   to_be_replaced.style.fontWeight="bold";
				   to_be_replaced.innerHTML = response;			   
					}
				});
		   myAjax.request();		
		   return true;	
    }
    else{
       return false;
    }		   
}

function saveEmailExportSettings(){
	form = document.emailForm;
	subject_template = form.subject_template.value;
	from_email = form.from_email.value;
	from_name = form.from_name.value;
	sitename = form.sitename.value;
	email_template = form.email_template.value;
	
	var message = new Array();
	var ids = new Array();
	var message_text = "";
			
	if(subject_template.trim().length == 0){
		message[message.length]= "Email subject";
		ids[ids.length] = "subject_template";
	}

	if(from_email.trim().length == 0){
		message[message.length]= "from email";
		ids[ids.length] = "from_email";
	}
	
	if(from_name.trim().length == 0){
		message[message.length]= "from name";
		ids[ids.length] = "from_name";
	}
	
	if(sitename.trim().length == 0){
		message[message.length]= "site name";
		ids[ids.length] = "sitename";
	}
	
	if(email_template.trim().length == 0){
		message[message.length]= "body email";
		ids[ids.length] = "email_template";
	}
	
	if(message.length != 0){
		for(i=0; i<message.length; i++){
			if(document.getElementById(ids[i])) {document.getElementById(ids[i]).style.border = "1px solid #FF0000";}
			if(i != message.length-1){
				message_text += "'{"+message[i] + "}', ";
			} 
			else{ 
				message_text += "'{"+message[i]+"}'"; 
			}
		}
		alert("Field(s) "+message_text+" is(are) not completed.");
		return false;
	}
	var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
	if (!filter.test(from_email)) {
		alert('Please provide a valid email address');
		return false;
	}
	
	var myAjax = new Ajax('components/com_arrauserexportimport/includes/js/functions.php?task=save_settings_email_export&subject_template='+subject_template+
						  '&from_email='+from_email+
						  '&from_name='+from_name+
						  '&sitename='+sitename+
						  '&email_template='+email_template, 
			   { 	
				onSuccess: function(response) {				  	
				   to_be_replaced = document.getElementById('message_error');
				   prefix = response.slice(0,1);
				   if(prefix!="1"){
					  to_be_replaced.style.color="#FF0000";//error-red
					  to_be_replaced.style.fontWeight="bold";
				   }
				   else{					   
					  to_be_replaced.style.color="#0B55C4"; //ok-blue
					  to_be_replaced.style.fontWeight="bold";
				   }
				   to_be_replaced.innerHTML = response.slice(1, response.length);			   
					}
				});
    myAjax.request();		
    return true;	 
}

function saveJomsocialEmailExport(){
	form = document.emailForm;
	subject_template = form.subject_template.value;
	from_email = form.from_email.value;
	from_name = form.from_name.value;
	sitename = form.sitename.value;
	email_template = form.email_template.value;
	
	var message = new Array();
	var ids = new Array();
	var message_text = "";
			
	if(subject_template.trim().length == 0){
		message[message.length]= "Email subject";
		ids[ids.length] = "subject_template";
	}

	if(from_email.trim().length == 0){
		message[message.length]= "from email";
		ids[ids.length] = "from_email";
	}
	
	if(from_name.trim().length == 0){
		message[message.length]= "from name";
		ids[ids.length] = "from_name";
	}
	
	if(sitename.trim().length == 0){
		message[message.length]= "site name";
		ids[ids.length] = "sitename";
	}
	
	if(email_template.trim().length == 0){
		message[message.length]= "body email";
		ids[ids.length] = "email_template";
	}
	
	if(message.length != 0){
		for(i=0; i<message.length; i++){
			if(document.getElementById(ids[i])) {document.getElementById(ids[i]).style.border = "1px solid #FF0000";}
			if(i != message.length-1){
				message_text += "'{"+message[i] + "}', ";
			} 
			else{ 
				message_text += "'{"+message[i]+"}'"; 
			}
		}
		alert("Field(s) "+message_text+" is(are) not completed.");
		return false;
	}
	var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
	if (!filter.test(from_email)) {
		alert('Please provide a valid email address');
		return false;
	}
	
	var myAjax = new Ajax('components/com_arrauserexportimport/includes/js/functions.php?task=jomsocial_save_email_export&subject_template='+subject_template+
						  '&from_email='+from_email+
						  '&from_name='+from_name+
						  '&sitename='+sitename+
						  '&email_template='+email_template, 
			   { 	
				onSuccess: function(response) {				  	
				   to_be_replaced = document.getElementById('message_error');
				   prefix = response.slice(0,1);
				   if(prefix!="1"){
					  to_be_replaced.style.color="#FF0000";//error-red
					  to_be_replaced.style.fontWeight="bold";
				   }
				   else{					   
					  to_be_replaced.style.color="#0B55C4"; //ok-blue
					  to_be_replaced.style.fontWeight="bold";
				   }
				   to_be_replaced.innerHTML = response.slice(1, response.length);			   
					}
				});
    myAjax.request();		
    return true;	 
}