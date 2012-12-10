<?php
/**
 * @package SISContactInformation
 * @version 1.0
 */
/*
Plugin Name: SISContactInformation
Description: To save the contact information in database and that list will be displayed in admin
Author: Surya Narayana Murthy K
Version: 1.0
*/

/**
	Function to display the contact form in front end.
	Use the short code "[siscontactform]"(with square brackets) in page or post content in admin,
	Then you can see the contact form in related frontend page or post
	
	If you have any problem with form display, use 
	<?php if(function_exists('sfront_end_form')) sfront_end_form(); ?>
*/
function sfront_end_form() {
	global $post;
	global $wpdb;
    /*
    Create a table to store the information given by user
    */
    $create_stables = $wpdb->query('CREATE TABLE IF NOT EXISTS  '. $wpdb->prefix .'sgcontact_information (
								  `sguest_id` bigint(20) NOT NULL AUTO_INCREMENT,
								  `sgname` varchar(255) NOT NULL,
								  `sgemail` varchar(255) NOT NULL,
								  `sgsubject` varchar(255) NOT NULL,
								  `sgmessage` text NOT NULL,
								  `sgmail_status` int(11) NOT NULL,
								  `sgcreated_date` datetime NOT NULL,
								  `sgip_address` varchar(20) NOT NULL,
								  PRIMARY KEY (`sguest_id`)
								) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT = 1 ;
								 ');

	if(count($_POST) > 0) {
			
			/*
				Code to validate the form on server side
			*/
			$error_message = false;

			//Filter the input
			$name     	= sisantize_input($_POST['txtName']);
			$email      = sisantize_input($_POST['txtEmail']); 
			$is_email   = filter_input(INPUT_POST, "txtEmail", FILTER_VALIDATE_EMAIL);
			$subject  	= sisantize_input($_POST['txtSubject']);
			$message  	= sisantize_input($_POST['taMessage']);
			$ip_address = $_SERVER['REMOTE_ADDR'];
			
			//Check the required fields are empty or not
			if($name == "") {
				$name_error_msg = "Name required";
				$error_message = true;
			}
			if($email == "") {
				$email_error_msg = "Email required";
				$error_message = true;
			}
			if($email != "") {
				if($is_email == "") {
					$email_error_msg = "Please enter valid email";
					$error_message = true;
				}
			}
			if($subject == "") {
				$subject_error_msg = "Subject required";
				$error_message = true;
			}
			if($message == "") {
				$message_error_msg = "Message required";
				$error_message = true;
			}
			
			
			/*
				If form fields are valid, then the information will be mailed to administrator
				and the information will be saved to the database
			*/
			if($error_message == false) {
					
					//Send an email
					$mail_status = send_email($name, $email, $subject, $message);

					if($mail_status != 1) $mail_status = 0;
					
					
					//Insert into database
					$insert_user_info = "INSERT INTO ". $wpdb->prefix ."sgcontact_information
										SET sgname = '".$name."', 
										sgemail = '".$email."', 
										sgsubject = '".$subject."', 
										sgmessage = '".$message."', 
										sgmail_status = '".$mail_status."', 
										sgcreated_date = NOW(), 
										sgip_address = '".$_SERVER['REMOTE_ADDR']."'";
					$result_user_info = $wpdb->query( $insert_user_info );
					
					if($post->post_type == 'page') {
						$redirect_url = get_option('siteurl').'/?msg=s&page_id='.$post->ID.'&pkey='.md5(rand().rand().time()).'#'.sha1(crypt($post->ID, rand(0, time()).rand()));
					}
					if($post->post_type == 'post') {
						$redirect_url = get_option('siteurl').'/?msg=s&p='.$post->ID.'&pkey='.md5(rand().rand().time()).'#'.sha1(crypt($post->ID, rand(0, time()).rand()));
					}
					
					if(!headers_sent()) {
						wp_redirect( $redirect_url );
						exit;
					}
					else
					{
						?><script>location.replace("<?php echo $redirect_url;?>");</script><?php exit;
					}
			}
				
	}
	
    ?>
    <style>
        .scontactinformation tr td {
            padding:0px;
			margin:0px;
            border-top:0px;
            border-bottom: 0px;
        }
        .errormessage {
            color:red;
        }
        .hcontact_information {
            font-weight: bold;
            font-size: 14px;
        }
        .successmessage {
            color:white;
            background:#0000FF;
            padding:6px;
            margin:6px;
        }
    </style>
	
    <script>
		/*
			####################################################
			Javascript functions to validate form
			Created on November 23 2012
			Version 1.0
			####################################################
		*/
		
		
		/*
			Function to validate email
			It return true if the email is valid otherwise it returns false 
		*/
		function isemail(objname)
		{
			var validemail = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$/;
			var objval = objname.value.toLowerCase();
			if(!objval.match(validemail)) return false;
			return true;
		}

		/*
			Function to validate the form fields
		*/		
        function svalidate_form() {
            
            frmobj = document.frmContactInformation;
            var sname    = frmobj.txtName.value;
            var semail   = frmobj.txtEmail.value;
            var ssubject = frmobj.txtSubject.value;
            var smessage = frmobj.taMessage.value;
            
            sname = sname.replace('/\s+/g', '');
            semail = semail.replace('/\s+/g', '');
            ssubject = ssubject.replace('/\s+/g', '');
            smessage = smessage.replace('/\s+/g', '');
            
            
            var error = false;
			
            document.getElementById('mename').innerHTML 	= '';
            document.getElementById('meemail').innerHTML	= '';
			document.getElementById('mesubject').innerHTML	= '';
			document.getElementById('memessage').innerHTML	= '';
			
            if(sname == "") {
                error = true;
                document.getElementById('mename').innerHTML = "Name required";
            }
            if(semail == "") {
                error = true;
                document.getElementById('meemail').innerHTML = "Email required";
            }
			if(semail != "") {
				
				if(!isemail(document.frmContactInformation.txtEmail)) {
					error = true;
                	document.getElementById('meemail').innerHTML = "Please enter valid email";
				}
				
			}
            if(ssubject == "") {
                error = true;
                document.getElementById('mesubject').innerHTML = "Subject required";
            }
            if(smessage == "") {
                error = true;
                document.getElementById('memessage').innerHTML = "Message required";
            }
            
            if(error == true) return false;
            
            return true;
        }
		
		function set_error_msg_empty(obj_value, meobj_id) {
			obj_value  = obj_value.replace('/\s+/g', '');
			if(obj_value != "") document.getElementById(meobj_id).innerHTML = '';
			if(meobj_id == 'meemail') {
				if(!isemail(document.frmContactInformation.txtEmail)) document.getElementById(meobj_id).innerHTML = 'Please enter valid email';
			}
		}
    </script>
    <form method="post" name="frmContactInformation" id="frmContactInformation" onsubmit="return svalidate_form()">
        <table border="1" width="100%" class="scontactinformation">
                <tr>
                    <td colspan="2"><h1 class="hcontact_information">Contact Information</h1></td>
                </tr>
				
				<?php
				if($_GET['msg'] == 's') {
					?>
					<tr>
						<td colspan="2"><br /><span class="successmessage">Thanks for contacting us. Our team will reply you soon.</span></td>
					</tr>
					<?php
				}
				?>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                
                <tr>
                    <td>Name*</td>
                    <td>
                        <input type="text" name="txtName" id="txtName" onchange="set_error_msg_empty(this.value, 'mename');" value="<?php echo $_POST['txtName'];?>"/>
                    </td>
                </tr>
				
				<tr>
					<td>&nbsp;</td><td><span id="mename" class="errormessage"><?php echo $name_error_msg;?></span></td>
				</tr>
                
				<tr>
                    <td>Email*</td>
                    <td>
                        <input type="text" name="txtEmail" id="txtEmail" onchange="set_error_msg_empty(this.value, 'meemail');" value="<?php echo $_POST['txtEmail'];?>"/>
                    </td>
                </tr>
				
				<tr>
					<td>&nbsp;</td><td><span id="meemail" class="errormessage"><?php echo $email_error_msg;?></span></td>
				</tr>
                
				<tr>
                    <td>Subject*</td>
                    <td>
                        <input type="text" name="txtSubject" id="txtSubject" onchange="set_error_msg_empty(this.value, 'mesubject');" value="<?php echo $_POST['txtSubject'];?>"/>
                    </td>
                </tr>
				
				<tr>
					<td>&nbsp;</td><td><span id="mesubject" class="errormessage"><?php echo $subject_error_msg;?></span></td>
				</tr>
                
				<tr>
                    <td>Message*</td>
                    <td>
                        <textarea name="taMessage" id="taMessage" onchange="set_error_msg_empty(this.value, 'memessage');"><?php echo $_POST['taMessage'];?></textarea>
                    </td>
                </tr>
				
				<tr>
					<td>&nbsp;</td><td><span id="memessage" class="errormessage"><?php echo $message_error_msg;?></span></td>
				</tr>
                
				<tr>
                    <td colspan="2"><input type="submit" name="btnSubmit" id="btnSubmit" value="Submit"></td>
                </tr>
				
        </table>
    </form>
    <?php
}

/**
	Function to santize the given input	
*/
function sisantize_input($input_value) {
	
		$input_value = mysql_real_escape_string(strip_tags(trim($input_value)));
		$input_value = filter_var($input_value, FILTER_SANITIZE_STRING);
		
		return $input_value;
}

/**
	Function to send an email
*/
function send_email($name, $email, $subject, $message) {

		$to  = get_option('admin_email');
		
		// subject
		$subject = $name . ' send a message to you';
		
		// message
		$body = '
		<html>
		<head>
		  <title>Contact Information From ' . $name . '</title>
		</head>
		<body>
		  <p>Dear <strong>admin</strong>, </p>
		  <table>
			<tr>
			  <td colspan="2" height="50px;">Please check the information below, User <b>'. $name .'</b> want to contact you</td>
			</tr>
			<tr>
			  <td width="25%"><b>Name</b></td><td> ' . $name . ' </td>
			</tr>
			<tr>
			  <td><b>Email</b></td><td> ' . $email . ' </td>
			</tr>
			<tr>
			  <td><b>Subject</b></td><td> ' . $subject . ' </td>
			</tr>
			<tr>
			  <td><b>Message</b></td><td> ' . $message . ' </td>
			</tr>
			<tr>
			  <td colspan="2" height="30px">&nbsp;</td>
			</tr>
			<tr>
			  <td colspan="2">Thanks</td>
			</tr>
		  </table>
		</body>
		</html>
		';
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		$headers .= 'To: < ' .get_option('admin_email'). ' >' . "\r\n";
		$headers .= 'From: ' . $name . ' <' . $email . '>' . "\r\n";
		
		// Mail it
		return mail($to, $subject, $body, $headers);

}	

//Create shortcode for form
add_shortcode('siscontactform', 'sfront_end_form');

################################################################################################################################################

// Hook for adding admin menus
add_action('admin_menu', 'sg_contact_info_list');

// action function for above hook
function sg_contact_info_list() {
    add_menu_page(__('Contacts List','menu-main'), __('Contacts List','menu-main'), 'manage_options', 'key-handle-menu', 'main_page_info_list' );
}


function pagination($start,$limit,$total,$filePath,$otherParams)
{
    $allPages = ceil($total/$limit);

    $currentPage = floor($start/$limit) + 1;

    $pagination = "";

    if ($allPages>10)
    {
        $maxPages = ($allPages>9) ? 9 : $allPages;

        if ($currentPage>=1&&$currentPage<=$allPages)
        {
            $pagination .= ($currentPage>4) ? " ... " : " ";

            $minPages = ($currentPage>4) ? $currentPage : 5;
            $maxPages = ($currentPage<$allPages-4) ? $currentPage : $allPages - 4;

            for($i=$minPages-4; $i<$maxPages+5; $i++)
            {
                $pagination .= ($i == $currentPage) ? "<a href=\"".$filePath."?start=".(($i-1)*$limit).$otherParams."\" class='sicpagenumber'>".$i."</a>" : "<a href=\"".$filePath."?start=".(($i-1)*$limit).$otherParams."\" class='sipagenumber'>".$i."</a>";
            }
            $pagination .= ($currentPage<$allPages-4) ? " ... " : " ";
        }
        else
        {

            $pagination .= " ... ";
        }
    }
    else
    {
        for($i=1; $i<$allPages+1; $i++)
        {
            $pagination .= ($i==$currentPage) ? "<a href=\"".$filePath."?start=".(($i-1)*$limit).$otherParams."\" class='sicpagenumber'>".$i."</a>" : "<a href=\"".$filePath."?start=".(($i-1)*$limit).$otherParams."\" class='sipagenumber'>".$i."</a> ";
        }
    }

    if ($currentPage > 1) {
        $previous = "<a href=\"".$filePath."?start=".(($currentPage-2)*$limit).$otherParams."\" class='sipagenumber'>Previous</a>";
        echo $previous;
    }

    echo $pagination;

    if ($currentPage < $allPages) {
        $next = "<a href=\"".$filePath."?start=".($currentPage*$limit).$otherParams."\" class='sipagenumber'>Next</a>";
        echo $next;
    }

}



// This function displays the page content in admin
function main_page_info_list() {
        global $wpdb;
		?>
		<style>
			.sinformation_pagination {
				margin:20px 0px 20px 0px;
				text-align:center;
			}
			.sinformation_pagination .sipagenumber{
				padding:8px;
				background:#1A95D0;
				color:white;
				margin:2px;
			}
			.sinformation_pagination .sicpagenumber{
				padding:8px;
				background:#990033;
				color:#CCCCCC;
				margin:2px;
			}
			.errormessage {
				color:red;
				text-align:center;
				font-size:18px;
			}
		</style>
		<?php
		include dirname(__FILE__)."/users_list.php";
}