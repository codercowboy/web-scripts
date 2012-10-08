<?PHP

	// ###################################################################
	// #
	// #  World's Worst Software Mail Script - php form mailing script
	// #   written by Jason Baker (jason@onejasonforsale.com)
	// #   on github: https://github.com/codercowboy/web-scripts
	// #   more info: http://www.codercowboy.com
	// #
	// #    Written for use on the following sites:
	// #         http://www.onejasonforsale.com
	// #         http://www.worldsworstsoftware.com
	// #         http://www.theantipatterns.com
	// #         http://www.anesotericvision.com
	// #
	// # Installation instructions are included in the distributable
	// #    zip file.  Said zip file and the latest version of this script
	// #    can be found at http://www.worldsworstsoftware.com
	// #
	// ###################################################################
	// #
	// #  UPDATES:
	// #
	// #  2007/04/20
	// #   - Fixed an email validation bug.
	// #
	// #  2007/02/28
	// #   - Initial Version
	// #
	// ###################################################################
	// #
	// # Copyright (c) 2012, Coder Cowboy, LLC. All rights reserved.
	// # 
	// # Redistribution and use in source and binary forms, with or without
	// # modification, are permitted provided that the following conditions are met:
	// # 1. Redistributions of source code must retain the above copyright notice, this
	// # list of conditions and the following disclaimer.
	// # 2. Redistributions in binary form must reproduce the above copyright notice,
	// # this list of conditions and the following disclaimer in the documentation
	// # and/or other materials provided with the distribution.
	// #  
	// # THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	// # ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	// # WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	// # DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	// # ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	// # (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	// # LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	// # ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	// # (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	// # SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	// #  
	// # The views and conclusions contained in the software and documentation are those
	// # of the authors and should not be interpreted as representing official policies,
	// # either expressed or implied.
	// ###################################################################

	/***********************************/
	/* begin configuration information */
	/***********************************/

	/* put your email address in the quotes below */
	$to_email_address = "put@youremail.here.com";

	/* if you want a from name override, put that in the quotes below, otherwise put "" */
	$from_name_override = "";

	/* put your header include file name here, make sure it's a relative path to the script */
	$header_include_file_path = "EmailFormHeader.html";

	/* put your footer include file name here, make sure it's a relative path to the script */
	$footer_include_file_path = "EmailFormFooter.html";

	/*********************************************************************/
	/* end configuration information (do not alter code below this line) */
	/*********************************************************************/


	//email form code, do not alter code below this line
	$is_post_back = false;
	$error_occurred = false;
	$error_list = "";
	$from_name = "";
	$from_email_address = "";
	$subject = "";
	$message = "";


	//first figure out if this is a relevant post back at all
	$is_post_back = isset($_POST['WWSEmailForm']);

	if ($is_post_back)
	{
		//get the post data
		$from_name = $_POST['fromName'];
		$from_email_address = $_POST['fromEmailAddress'];
		$subject = $_POST['subject'];
		$message = $_POST['message'];

		//if the from email is not provided, thats an error
		if (strlen($from_email_address) == 0)
		{
			$error_occurred = true;
			$error_list .= "You must provide an Email Address.\n";
		}

		//if the message is not provided thats an error
		if (strlen($message) == 0)
		{
			$error_occurred = true;
			$error_list .= "You must provide a message.\n";
		}

		//if no errors have occurred try to send the email
		if ($error_occurred == false)
		{

			//the following two optimizations are suggested here: http://www.php.net/manual/en/function.mail.php

			//optimization #1: strip newlines from the subject if any exist
			$subject = str_replace("\n", "", $subject);

			//optimization #2: change full stop ("\n.") strings in message to prevent goofing up smtp protocol
			$message = str_replace("\n.", "\n..", $message);

			$headers = "";

			if (strlen($from_name_override) > 0)
			{
				$headers .= "From: " . $from_name_override . "<" . $to_email_address . ">\r\n";
				$message .= "\n\n(Sent by " . $from_name . " [" . $from_email_address . "])\n";
			}
			else
			{
				$headers .= "From: " . $from_name . "<" . $from_email_address . ">\r\n";
			}


			$mail_send_success = mail($to_email_address, $subject, $message, $headers);
			//if the email send fails for some reason, thats an error
			if ($mail_send_success == false)
			{
				$error_occurred = true;
				$error_list .= "There was an error trying to send your e-mail, try again.\n";
			}
		}
	}

?>

<?php include $header_include_file_path; ?>

<!-- begin world's worst software email form from http://www.worldsworstsoftware.com -->
<div id="wwsEmailForm">
<?php if ($is_post_back && !$error_occurred) { ?>
  <p><span class="successText">Your message has been sent!</span><p>
<?php } else { ?>
  <form name="EmailForm" action="contact.php" method="post">
  	<input type="hidden" name="WWSEmailForm" value="WWSEmailForm">
  	<?php if ($error_occurred) { ?>
  	<p><span class="errorText"><?php echo str_replace("\n", "<br>", $error_list); ?></span><p>
  	<?php } ?>
    <p>Your Name: <input id="fromNameTextBox" type="text" name="fromName" maxlength="100" value="<?php echo htmlspecialchars($from_name); ?>"></p>
    <p>Your Email Address: <input id="fromEmailAddressTextBox" type="text" name="fromEmailAddress" maxlength="100" value="<?php echo htmlspecialchars($from_email_address); ?>"></p>
    <p>Subject: <input id="subjectTextBox" type="text" name="subject" maxlength="200" value="<?php echo htmlspecialchars($subject); ?>"></p>
    <p>Your Message: <textarea id="messageTextBox" name="message" rows="10" cols="40"><?php echo htmlspecialchars($message); ?></textarea></p>
    <p><input id="submitButton" type="submit" value="Send Email"></p>
  </form>
<?php } ?>
</div>
<!-- end world's worst software email form -->

<?php include $footer_include_file_path; ?>

