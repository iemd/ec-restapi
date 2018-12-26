<?php
// Routes

/*$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});*/
/***********************************************************************/
// 1. Users Registration
/***********************************************************************/
$app->post('/registration', function ($request, $response, $args){
	$input = $request->getParsedBody();
	if(!empty($input['mobile']) && !empty($input['email']) && !empty($input['token'])){
        $mobile = $input['mobile'];
		$email = $input['email'];
		$token = $input['token'];
        /*****************************/  		
		$stmt1 = $this->db->prepare("SELECT * FROM ec_user WHERE email='".$email."'"); //$stmt1
		$stmt1->execute(); 
		$nRows = count($stmt1->fetchAll());
		if($nRows == 0){ //if user with email not exists.
		    $stmt2 = $this->db->prepare("INSERT INTO ec_user(mobile,email,registration_token) VALUES(:mobile, :email, :registration_token)");
		    $params2 = array(':mobile'=>$mobile, ':email'=>$email,':registration_token'=>$token);
		    $result = $stmt2->execute($params2);
		    $last_id = $this->db->lastInsertId();
		    if($result){
				$username = ucfirst(substr($email, 0, strpos($email, '@')));	 
				$verification_code = substr(md5(uniqid(rand(), true)), 6, 6);
				//require 'includes/PHPMailer/PHPMailerAutoload.php';
				$mail = new PHPMailer();
				$mail->IsSMTP();                                      // set mailer to use SMTP
				//$mail->SMTPDebug = 1;                               // Enable verbose debug output
				$mail->Host = "erachat.condoassist2u.com";                 // specify main and backup server
				$mail->SMTPAuth = true;                               // turn on SMTP authentication
				$mail->Username = "support@erachat.condoassist2u.com";// SMTP username
				$mail->Password = "4Nf6WTmqLb@]";                       // SMTP password
				$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
				$mail->Port = 587;                                    // TCP port to connect to
				$mail->SMTPOptions = array(
					'ssl' => array(
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
					)
				);
				$mail->SetFrom('support@erachat.condoassist2u.com','EraChat');

				//$mail->AddAddress("josh@example.net", "Josh Adams");
				$mail->AddAddress($email);   // name is optional
				$mail->AddReplyTo("donotreply@erachat.condoassist2u.com", "EraChat");

				$mail->WordWrap = 50;                                 // set word wrap to 50 characters
				//$mail->AddAttachment("/var/tmp/file.tar.gz");       // add attachments
				//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");  // optional name
				$mail->IsHTML(true);                                  // set email format to HTML

				$mail->Subject = 'EraChat Account Activation';
				$bodyContent = '<h1>Hello, <b>'.$username.'</b></h1>';
				$bodyContent .= '<p>Congratulations and greetings from the EraChat team! Your account at EraChat account is now active. You are now able to utilise our mobile apps facilities for your daily activities. </p>								  
				<p>To activate your account, Your verification code is: </br> <b>'.$verification_code.'</b> </p>
				<p>Feel free to communicate with us using the above account information for any query. We offer 24 hours customer support with a dedicated team on phone and via e-mail. </br> We hope you enjoy your experience with us! </br> For further enquiries, please email to support@erachat.com</p>
				<p>Yours sincerely,</p>
				<p>EraChat Support</p>
				<p><small>This is a computer generated email. Do not reply to this email.</small></p>';
				$mail->Body = $bodyContent;
				//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
				if(!$mail->Send()){
			
				   $json = array("error" => true, "message" => $mail->ErrorInfo);
				   
				}else{
				    function generateRandomString($length = 8){
						$char="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
						$random = substr(str_shuffle($char), 0, $length);
						return $random;
					}
					$qrcode = generateRandomString(20);
					$randName = md5(uniqid(rand() * time()));
					$qrimage_name = $introducer_id.$randName.".png";
					$qrimage_url = "uploads/qrcode/".$qrimage_name;				
					$this->qr->setText($qrcode);
					$this->qr->setSize(100);
					$this->qr->setMargin(20);
					$this->qr->writeFile($qrimage_url);
					//---	
				    $stmt3 = $this->db->prepare("UPDATE `ec_user` SET `verification_code` = '".$verification_code."',`qr_code` = '".$qrcode."',`qr_image` = '".$qrimage_name."'  WHERE `user_id` = '".$last_id."'");
				    $result = $stmt3->execute(); 
				    if($result){
					 $json = array("error" => false, "user_id" =>$last_id, "verification_code"=>$verification_code, "message" => "Registered successfully!");  
				    }else{				   
					 $json = array("error" => true, "message" => "Database error!");  
				    }
				}		 
							
			 }else{
				 
				  $json = array("error" => true, "message" => "Database error!");
			 }	
		}else{ //if user with email exists.
			$stmt1->execute(); 
			while($row = $stmt1->fetch()){					
				    $user_id = $row['user_id'];			 
			}
			$stmt4 = $this->db->prepare("UPDATE `ec_user` SET `registration_token` = '".$token."', `is_active` = '0' WHERE  user_id = '".$user_id."'");
		    $result = $stmt4->execute(); 
			if($result){
				$username = ucfirst(substr($email, 0, strpos($email, '@')));	 
				$verification_code = substr(md5(uniqid(rand(), true)), 6, 6);
				//require 'includes/PHPMailer/PHPMailerAutoload.php';
				$mail = new PHPMailer();
				$mail->IsSMTP();                                      // set mailer to use SMTP
				//$mail->SMTPDebug = 1;                                 // Enable verbose debug output
				$mail->Host = "erachat.condoassist2u.com";                 // specify main and backup server
				$mail->SMTPAuth = true;                               // turn on SMTP authentication
				$mail->Username = 'support@erachat.condoassist2u.com';// SMTP username
		        $mail->Password = '4Nf6WTmqLb@]';                     // SMTP password
				$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
				$mail->Port = 587;                                    // TCP port to connect to
				$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
				);
				$mail->SetFrom('support@erachat.condoassist2u.com','EraChat');

				//$mail->AddAddress("josh@example.net", "Josh Adams");
				$mail->AddAddress($email);   // name is optional
				$mail->AddReplyTo("donotreply@erachat.condoassist2u.com", "EraChat");

				$mail->WordWrap = 50;                                 // set word wrap to 50 characters
				//$mail->AddAttachment("/var/tmp/file.tar.gz");       // add attachments
				//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");  // optional name
				$mail->IsHTML(true);                                  // set email format to HTML

				$mail->Subject = 'EraChat Account Activation';
				$bodyContent = '<h1>Hello, <b>'.$username.'</b></h1>';
				$bodyContent .= '<p>Congratulations and greetings from the EraChat team! Your account at EraChat account is now active. You are now able to utilise our mobile apps facilities for your daily activities. </p>								  
				<p>To activate your account, Your verification code is: </br> <b>'.$verification_code.'</b> </p>
				<p>Feel free to communicate with us using the above account information for any query. We offer 24 hours customer support with a dedicated team on phone and via e-mail. </br> We hope you enjoy your experience with us! </br> For further enquiries, please email to support@erachat.com</p>
				<p>Yours sincerely,</p>
				<p>EraChat Support</p>
				<p><small>This is a computer generated email. Do not reply to this email.</small></p>';
				$mail->Body = $bodyContent;
				//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
				if(!$mail->Send()){
					
				   $json = array("error" => true, "message" => $mail->ErrorInfo);
				   
				}else{
				   $stmt5 = $this->db->prepare("UPDATE `ec_user` SET `verification_code` = '".$verification_code."'  WHERE `user_id` = '".$user_id."'");
				   $result = $stmt5->execute(); 
				   if($result){
					 $json = array("error" => false, "user_id" =>$user_id, "verification_code"=>$verification_code, "message" => "Registered successfully!");  
				   }else{				   
					 $json = array("error" => true, "message" => "Database error!");  
				   }
				}		 
							
			 }else{
				 
				  $json = array("error" => true, "message" => "Database error!");
			 }			
			/*
			$sql = "SELECT * FROM ec_user WHERE email ='".$email."'";
		    $stmt = $this->db->prepare($sql);
			$stmt->execute(); 
			$userDetail = $stmt->fetchAll(); 
			$json = array("error" => false, "message" => $userDetail);
			*/
		} // end if user with email exists.
     }else{
		 
		 $json = array("error" => true, "message" => "Email, Mobile and Token not blank!"); 
		 
	 }
	 $response->withHeader('Content-type', 'application/json');
     return $response->withJson($json);
});
/***********************************************************************/
// 2. Verify Users
/***********************************************************************/
$app->post('/verify', function ($request, $response, $args) {	
	 $input = $request->getParsedBody();	
	 if(!empty($input['verification_code']) && !empty($input['user_id'])){
		$user_id = $input['user_id']; 
		$verification_code = $input['verification_code']; 
        try{
			$stmt1 = $this->db->prepare("SELECT * FROM ec_user WHERE is_active = 0 AND user_id ='".$user_id."'");
			$stmt1->execute(); 
			$nRows = count($stmt1->fetchAll());
			if($nRows == 1 ){
			    $stmt1->execute();
                while($row = $stmt1->fetch()){	
				
					$vcode = $row['verification_code']; 					
				} 
                if($vcode == $verification_code){
					$stmt2 = $this->db->prepare("UPDATE `ec_user` SET `is_active` = '1'  WHERE `user_id` = '".$user_id."'");
					$result = $stmt2->execute();
					if($result){
						
						 $json = array("error" => false, "message" => "Verified Successfully!");  
						 
					}else{
						 
						 $json = array("error" => true, "message" => "Database Error!");
					}			
				}else{
					
					$json = array("error" => true, "message" => "Wrong verification code!");
				}
			}else{
				
				$json = array("error" => true, "message" => "Invalid UserID!"); 
			}		 
		}catch(PDOException $e){
			
			$json = array("error" => true, "message" => $e->getMessage());
		} 
	 }else{
		 
		 $json = array("error" => true, "message" => "Verification Code not blank!"); 
		 
	 }
	 $response->withHeader('Content-type', 'application/json');
     return $response->withJson($json);
});	
/***********************************************************************/
// 3. Get Users
/***********************************************************************/
$app->get('/users', function ($request, $response, $args) {

	try{	
		$sql = "SELECT user_id,mobile,email,updated_at,created_at,is_active FROM ec_user WHERE is_active =1 ";
		//$users = $this->db->query($sql);
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		//$result = $stmt->rowCount();
		$nRows = $this->db->query($sql)->fetchColumn();
		if($nRows >0 ){
			$users = $stmt->fetchAll();			
			$json = array("error" => false, "message" => $users);
		}else{
			
			$json = array("error" => false, "message" =>"Not Found!");
		}
    }catch(PDOException $e){
			
	    $json = array("error" => true, "message" => $e->getMessage());
    }	
	
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 4. Send one to one text
/***********************************************************************/
$app->post('/oneToOneText', function ($request, $response, $args) {
    $uri = $request->getUri();
    $baseUrl = $uri->getBaseUrl();
	$input = $request->getParsedBody();
    if(!empty($input['user_id']) && !empty($input['sender_id']) && !empty($input['message'])){
		$user_id = $input['user_id'];
	    $sender_id = $input['sender_id'];
	    $message = $input['message'];
		$sql = "SELECT * FROM ec_user where `is_blocked` = 0 AND `is_active` = 1 AND user_id='".$user_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			$update = "INSERT INTO ec_user_message(user_id,sender_id,message,created_at) VALUES(:user_id,:sender_id,:message,CONVERT_TZ( NOW(),'+8:00','+5:30'))";
			$stmt_update = $this->db->prepare($update);
			$params_users = array(':user_id' =>$user_id, ':sender_id' =>$sender_id, ':message'=>$message);
			$stmt_update->execute($params_users);
			$last_msg_id = $this->db->lastInsertId();
		    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			$rowCount = count($stmt->fetchAll());
			$sender_name = null;
			$sender_image = null;
			$mobile = null;
			if($rowCount>0){
			    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				while($row = $stmt->fetch()){
					
					$sender_name = $row['user_name'];
					$mobile = $row['mobile'];
					$sender_image = $row['photo'];
				 //$registrationIDs = array($row['registration_token']);
				 
				}
				$sender_image_url = $baseUrl."/uploads/user/".$sender_image;
			}
			$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			while($row = $stmt->fetch()){
				
			 $registrationID = $row['registration_token'];
			 //$registrationIDs = array($row['registration_token']);
			 
			}
			
			// API access key from Google API's Console
			
			 define( 'API_ACCESS_KEY', 'AIzaSyAscUsZbNdHpw69abg0ApcKhG9ZoJpcYMY' ); 
			   
			// prep the bundle
			
			$headers = array
			(
				'Content-Type: application/json',
				'Authorization:key='.API_ACCESS_KEY		
			);
			date_default_timezone_set('Asia/Kolkata');
			$date = date('Y-m-d H:i:s'); 
			$fields = array
			(
			    'message_id'    => $last_msg_id,
				'sender_id' 	=> $sender_id,
				'sender_name' 	=> $sender_name,
				'sender_image' 	=> $sender_image_url,
				'mobile' 		=> $mobile,
			    'receiver_id' 	=> $user_id,
				'chat_type' 	=> 'one_to_one',	
				'type' => 'text',
				'message' 	=> $message,
				'date' 	=> $date

			);
			$messaage = array
			(
				'content_available' => true ,
  	     	    'priority' =>  'high',
				'to'	=> $registrationID,
				//'registration_ids' 	=> $registrationIDs,
				'data'			=> $fields

			); 

			$url = 'https://fcm.googleapis.com/fcm/send';
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, $url );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $messaage ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			$json = json_decode($result,true);
		    // if($json['success'] == 1){
				
				
			// }
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"UserID, SenderID and Message not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 5. Send one to one image
/***********************************************************************/
$app->post('/oneToOneImage', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	$user_id = $input['user_id'];
	$sender_id = $input['sender_id'];
	$image = $input['image'];
    if(!empty($user_id) && !empty($sender_id) && !empty($image)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		$randName = md5(uniqid(rand() * time()));
		//decode the image
		$decodedImage = base64_decode($image);
		$image_name = $randName.".jpg";
		//upload the image
		$filepath = "uploads/message/";		
		$path = file_put_contents($filepath.$image_name, $decodedImage);
		$imageUrl = $baseUrl."/uploads/message/".$image_name;
		$sql = "SELECT * FROM ec_user where `is_blocked` = 0 AND `is_active` = 1 AND user_id='".$user_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			$update = "INSERT INTO ec_user_message(user_id,sender_id,message,type,created_at) VALUES(:user_id,:sender_id,:message,:type,CONVERT_TZ(NOW(),'+8:00','+5:30'))";
			$stmt_update = $this->db->prepare($update);
			$params_users = array(':user_id' =>$user_id, ':sender_id' =>$sender_id, ':message'=>$image_name, ':type'=>1);
			$stmt_update->execute($params_users);
			$last_msg_id = $this->db->lastInsertId();
		    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			$rowCount = count($stmt->fetchAll());
			$sender_name = null;
			$mobile = null;
			$sender_image = null;
			if($rowCount>0){
				$sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				while($row = $stmt->fetch()){
					
				 $sender_name = $row['user_name'];
				 $mobile = $row['mobile'];
				 $sender_image = $row['photo'];
				 //$registrationIDs = array($row['registration_token']);
				 
				}
				$sender_image_url = $baseUrl."/uploads/user/".$sender_image;
			}
			
			$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			while($row = $stmt->fetch()){
				
			 $registrationID = $row['registration_token'];
			 //$registrationIDs = array($row['registration_token']);
			 
			}
			
			// API access key from Google API's Console
			
			 define( 'API_ACCESS_KEY', 'AIzaSyAscUsZbNdHpw69abg0ApcKhG9ZoJpcYMY' ); 
			   
			// prep the bundle
			
			$headers = array
			(
				'Content-Type: application/json',
				'Authorization:key='.API_ACCESS_KEY		
			);
			date_default_timezone_set('Asia/Kolkata');
			$date = date('Y-m-d H:i:s'); 
			$fields = array
			(
			    'message_id'   => $last_msg_id,
				'sender_id'    => $sender_id,
				'sender_name'  => $sender_name,
				'sender_image' => $sender_image_url,
				'mobile' 	   => $mobile,
			    'receiver_id'  => $user_id,
				'chat_type' 	=> 'one_to_one',	
				'type' 		   => 'image',
				'message' 	   => $imageUrl,
				'date' 		   => $date

			);
			$messaage = array
			(
				'content_available' => true ,
  	     	    'priority' =>  'high',
				'to'	=> $registrationID,
				//'registration_ids' 	=> $registrationIDs,
				'data'			=> $fields

			); 

			$url = 'https://fcm.googleapis.com/fcm/send';
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, $url );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $messaage ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			$json = json_decode($result,true);
		    // if($json['success'] == 1){
				
				
			// }
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"UserID, SenderID and Image not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 6. Send one to one video
/***********************************************************************/
$app->post('/oneToOneVideo', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	$user_id = $input['user_id'];
	$sender_id = $input['sender_id'];
	$video = $input['video'];
    if(!empty($user_id) && !empty($sender_id) && !empty($video)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		$randName = md5(uniqid(rand() * time()));
		//decode the image
		$decodedVideo = base64_decode($video);
		$video_name = $randName.".mp4";
		//upload the image
		$filepath = "uploads/message/";		
		$path = file_put_contents($filepath.$video_name, $decodedVideo);
		$videoUrl = $baseUrl."/uploads/message/".$video_name;
		$sql = "SELECT * FROM ec_user where `is_blocked` = 0 AND `is_active` = 1 AND user_id='".$user_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			$update = "INSERT INTO ec_user_message(user_id,sender_id,message,type,created_at) VALUES(:user_id,:sender_id,:message,:type,CONVERT_TZ(NOW(),'+8:00','+5:30'))";
			$stmt_update = $this->db->prepare($update);
			$params_users = array(':user_id' =>$user_id, ':sender_id' =>$sender_id, ':message'=>$video_name, ':type'=>2);
			$stmt_update->execute($params_users);
			$last_msg_id = $this->db->lastInsertId();
		    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			$rowCount = count($stmt->fetchAll());
			$sender_name = null;
			$mobile = null;
			$sender_image = null;
			if($rowCount>0){
				$sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				while($row = $stmt->fetch()){
					
				 $sender_name = $row['user_name'];
				 $mobile = $row['mobile'];
				 $sender_image = $row['photo'];
				 //$registrationIDs = array($row['registration_token']);
				 
				}
				$sender_image_url = $baseUrl."/uploads/user/".$sender_image;
			}
			
			$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			while($row = $stmt->fetch()){
				
			 $registrationID = $row['registration_token'];
			 //$registrationIDs = array($row['registration_token']);
			 
			}
			
			// API access key from Google API's Console
			
			 define( 'API_ACCESS_KEY', 'AIzaSyAscUsZbNdHpw69abg0ApcKhG9ZoJpcYMY' ); 
			   
			// prep the bundle
			
			$headers = array
			(
				'Content-Type: application/json',
				'Authorization:key='.API_ACCESS_KEY		
			);
			date_default_timezone_set('Asia/Kolkata');
			$date = date('Y-m-d H:i:s'); 
			$fields = array
			(
			    'message_id'   => $last_msg_id,
				'sender_id'    => $sender_id,
				'sender_name'  => $sender_name,
				'sender_image'  => $sender_image_url,
				'mobile' 	   => $mobile,
			    'receiver_id'  => $user_id,
				'chat_type' 	=> 'one_to_one',	
				'type' 		   => 'video',
				'message' 	   => $videoUrl,
				'date' 		   => $date

			);
			$messaage = array
			(
				'content_available' => true ,
  	     	    'priority' =>  'high',
				'to'	=> $registrationID,
				//'registration_ids' 	=> $registrationIDs,
				'data'			=> $fields

			); 

			$url = 'https://fcm.googleapis.com/fcm/send';
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, $url );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $messaage ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			$json = json_decode($result,true);
		    // if($json['success'] == 1){
				
				
			// }
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"UserID, SenderID and Video not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 7. Send one to one voice
/***********************************************************************/
$app->post('/oneToOneVoice', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	$user_id = $input['user_id'];
	$sender_id = $input['sender_id'];
	$voice = $input['voice'];
    if(!empty($user_id) && !empty($sender_id) && !empty($voice)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		$randName = md5(uniqid(rand() * time()));
		//decode the image
		$decodedVoice = base64_decode($voice);
		$voice_name = $randName.".mp3";
		//upload the image
		$filepath = "uploads/message/";		
		$path = file_put_contents($filepath.$voice_name, $decodedVoice);
		$voiceUrl = $baseUrl."/uploads/message/".$voice_name;
		$sql = "SELECT * FROM ec_user where `is_blocked` = 0 AND `is_active` = 1 AND user_id='".$user_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			$update = "INSERT INTO ec_user_message(user_id,sender_id,message,type,created_at) VALUES(:user_id,:sender_id,:message,:type,CONVERT_TZ(NOW(),'+8:00','+5:30'))";
			$stmt_update = $this->db->prepare($update);
			$params_users = array(':user_id' =>$user_id, ':sender_id' =>$sender_id, ':message'=>$voice_name, ':type'=>3);
			$stmt_update->execute($params_users);
			$last_msg_id = $this->db->lastInsertId();
		    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			$rowCount = count($stmt->fetchAll());
			$sender_name = null;
			$mobile = null;
			$sender_image = null;
			if($rowCount>0){
				$sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				while($row = $stmt->fetch()){
					
				 $sender_name = $row['user_name'];
				 $mobile = $row['mobile'];
				 $sender_image = $row['photo'];
				 //$registrationIDs = array($row['registration_token']);
				 
				}
				$sender_image_url = $baseUrl."/uploads/user/".$sender_image;
			}
			
			$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			while($row = $stmt->fetch()){
				
			 $registrationID = $row['registration_token'];
			 //$registrationIDs = array($row['registration_token']);
			 
			}
			
			// API access key from Google API's Console
			
			 define( 'API_ACCESS_KEY', 'AIzaSyAscUsZbNdHpw69abg0ApcKhG9ZoJpcYMY' ); 
			   
			// prep the bundle
			
			$headers = array
			(
				'Content-Type: application/json',
				'Authorization:key='.API_ACCESS_KEY		
			);
			date_default_timezone_set('Asia/Kolkata');
			$date = date('Y-m-d H:i:s'); 
			$fields = array
			(
			    'message_id'   => $last_msg_id,
				'sender_id'    => $sender_id,
				'sender_name'  => $sender_name,
				'sender_image'  => $sender_image_url,
				'mobile' 	   => $mobile,
			    'receiver_id'  => $user_id,	
				'chat_type' 	=> 'one_to_one',	
				'type' 		   => 'voice',
				'message' 	   => $voiceUrl,
				'date' 		   => $date

			);
			$messaage = array
			(
				'content_available' => true ,
  	     	    'priority' =>  'high',
				'to'	=> $registrationID,
				//'registration_ids' 	=> $registrationIDs,
				'data'			=> $fields

			); 

			$url = 'https://fcm.googleapis.com/fcm/send';
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, $url );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $messaage ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			$json = json_decode($result,true);
		    // if($json['success'] == 1){
				
				
			// }
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"UserID, SenderID and Voice not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 8. Set Username
/***********************************************************************/
$app->post('/setUsername', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	
	$user_id = $input['user_id'];
	$username = $input['username'];
	if(!empty($user_id) && !empty($username)){
		$sql = "SELECT * FROM ec_user where `is_active` = 1 AND user_id='".$user_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			try{
			 
				$sql = "UPDATE `ec_user` SET `user_name` = '".$username."'  WHERE `user_id` = '".$user_id."'";
				$stmt = $this->db->prepare($sql);
				$result = $stmt->execute();
				if($result){
					 
					 $json = array("error" => false, "username"=>$username, "message" => "Username updated successfully!"); 
					 
				}else{
					 
					 $json = array("error" => true, "message" => "Database Error!");
			    }
			}catch(PDOException $e){
				
				 $json = array("error" => true, "message" => $e->getMessage());
			}			
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID and Username not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 9. Set Status
/***********************************************************************/
$app->post('/setStatus', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	
	$user_id = $input['user_id'];
	$status = $input['status'];
	if(!empty($user_id) && !empty($status)){
		$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			try{
			 
				$sql = "UPDATE `ec_user` SET `status` = '".$status."'  WHERE `user_id` = '".$user_id."'";
				$stmt = $this->db->prepare($sql);
				$result = $stmt->execute();
				if($result){
					 
					 $json = array("error" => false,"status"=>$status, "message" => "Status updated successfully!"); 
					 
				}else{
					 
					 $json = array("error" => true, "message" => "Database Error!");
			    }
			}catch(PDOException $e){
				
				 $json = array("error" => true, "message" => $e->getMessage());
			}			
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID and Status not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 10. Set Photo
/***********************************************************************/
$app->post('/setPhoto', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	$user_id = $input['user_id'];
	$photo = $input['photo'];
	if(!empty($user_id) && !empty($photo)){
		$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			
			$randName = md5(uniqid(rand() * time()));
			//decode the image
			$decodedImage = base64_decode($photo);
			$image_name = $user_id."_".$randName.".jpg";
			//upload the image
			$filepath = "uploads/user/";		
			$path = file_put_contents($filepath.$image_name, $decodedImage);
		 					
			$sql = "UPDATE `ec_user` SET `photo` = '".$image_name."'  WHERE `user_id` = '".$user_id."'";
			$stmt = $this->db->prepare($sql);
			$result = $stmt->execute();
			if($result){
					 
				$json = array("error" => false, "message" => "Photo updated successfully!"); 
					 
			}else{
					 
				$json = array("error" => true, "message" => "Database Error!");
			}
					
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID and Photo not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 11. Update Photo
/***********************************************************************/
$app->post('/updatePhoto', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	$user_id = $input['user_id'];
	$photo = $input['photo'];
	if(!empty($user_id) && !empty($photo)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
            while($row = $stmt->fetch()){
				$oldPhoto = "uploads/user/".$row['photo'];
			}			
			if($oldPhoto != "uploads/user/user-icon.png"){
				if(file_exists($oldPhoto)){					
                   unlink($oldPhoto);
				}				
			}
			$randName = md5(uniqid(rand() * time()));
			//decode the image
			$decodedImage = base64_decode($photo);
			$image_name = $user_id."_".$randName.".jpg";
			//upload the image
			$filepath = "uploads/user/";		
			$path = file_put_contents($filepath.$image_name, $decodedImage);
		 					
			$sql = "UPDATE `ec_user` SET `photo` = '".$image_name."'  WHERE `user_id` = '".$user_id."'";
			$stmt = $this->db->prepare($sql);
			$result = $stmt->execute();
			if($result){
				
				$profile_image_url = $baseUrl."/uploads/user/".$image_name;	 
				$json = array("error" => false, "message" => $profile_image_url); 
					 
			}else{
					 
				$json = array("error" => true, "message" => "Database Error!");
			}
					
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID and Photo not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 12. Create Group
/***********************************************************************/
$app->post('/createGroup', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	$group_name = $input['group_name'];
	$group_admin = $input['group_admin'];
	$group_photo = $input['group_photo'];
	
	if(!empty($group_name) && !empty($group_admin)){
		try{
			$insert = 'INSERT INTO ec_group(group_name,group_admin) VALUES(:group_name,:group_admin)';
			$stmt = $this->db->prepare($insert);
			$params_users = array(':group_name' =>$group_name,':group_admin' =>$group_admin);
			$result = $stmt->execute($params_users);
			$group_id = $this->db->lastInsertId();
	
			if($result){
				
				if(!empty($group_photo)){
					$randName = md5(uniqid(rand() * time()));
				    //decode the image
					$decodedImage = base64_decode($group_photo);
					$image_name = $group_id."_".$randName.".jpg";
					//upload the image
					$filepath = "uploads/group/";		
					$path = file_put_contents($filepath.$image_name, $decodedImage);
				}else{
					
					$image_name = "group_icon.png";
				}
				$sql = "UPDATE `ec_group` SET `photo` = '".$image_name."'  WHERE `group_id` = '".$group_id."'";
				$stmt = $this->db->prepare($sql);
				$result = $stmt->execute();
				if($result){
					$member = array($group_admin);
					$member = serialize($member);
					$stmt = $this->db->prepare("INSERT INTO ec_group_member(group_id,member_id) VALUES('".$group_id."','".$member."')");
					$result = $stmt->execute();
					if($result){
						$json = array("error" => false, "group_id" =>$group_id, "message" => "Group created successfully!");
					}else{
						$json = array("error" => true, "message" =>"Database Error!");
					}					
				}else{
					
					$json = array("error" => true, "message" =>"Database Error!");
				}				
			}else{
				
				$json = array("error" => true, "message" =>"Database Error!");
			}
		}catch(PDOException $pe){
				
			$json = array("error" => true, "message" => $pe->getMessage());
		}		
	}else{
		
		$json = array("error" => true, "message" =>"GroupName,GroupAdmin and GroupPhoto not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
}); 
/***********************************************************************/
// 13. Add Member to Group
/***********************************************************************/
$app->post('/addMembersInGroup', function ($request, $response, $args) {	
	$input = $request->getParsedBody();
	$uri = $request->getUri();
    $baseUrl = $uri->getBaseUrl();
	if(!empty($input['group_id']) && !empty($input['members'])){
		$group_id = $input['group_id'];
		$members = $input['members'];
		$membersToAdd = explode(',',$members);
		try{
			$stmt1 = $this->db->prepare("SELECT * FROM ec_group where group_id='".$group_id."'");
			$stmt1->execute(); 
			$rowCount = count($stmt1->fetchAll());
			if($rowCount>0){
				$stmt2 = $this->db->prepare("SELECT * FROM ec_group_member WHERE group_id='".$group_id."'");
				$stmt2->execute(); 
				$nRows = count($stmt2->fetchAll()); 
				if($nRows > 0){
				    $stmt2->execute();
					while($row = $stmt2->fetch()){					
						$member_id = $row['member_id'];				
					}
                    $saved_members = unserialize($member_id);
					$stmt1->execute(); 
					while($row = $stmt1->fetch()){					
						$group_admin = $row['group_admin']; 
						$groupDetail['group_id'] = $row['group_id']; 
						$groupDetail['group_name'] = $row['group_name'];							
						$groupDetail['group_admin'] = $row['group_admin'];
						$groupDetail['group_photo'] = $baseUrl."/uploads/group/".$row['photo'];
						$groupDetail['created_at'] = $row['created_at'];			
					}				
					$addedMembers = array();
					$membersToAdd = array_values(array_unique($membersToAdd));			
					/*if(($key = array_search($group_admin, $membersToAdd)) !== false) {
						unset($membersToAdd[$key]);
					}*/					
					$allMembers = array_values(array_unique(array_merge($saved_members,$membersToAdd)));
					$allMembers = serialize($allMembers);					
					$stmt3 = $this->db->prepare("UPDATE ec_group_member SET member_id ='".$allMembers."' WHERE group_id='".$group_id."'");
					$result = $stmt3->execute();
					if($result){
							
						$addedMembers[] = $membersToAdd;
					}
					$groupDetail['members']= $addedMembers; 	
					$json = array("error" => false, "message" => $groupDetail);		
				}else{
					
					$json = array("error" => false, "message" => 'Invalid GroupID!');					
				}		
			}else{
				
				$json = array("error" => false, "message" => 'Invalid GroupID!');
			}
		}catch(PDOException $pe){
				
			$json = array("error" => true, "message" => $pe->getMessage());
		}	
    }else{
		
		$json = array("error" => true, "message" =>"Members not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 14. Get Groups
/***********************************************************************/
$app->get('/getGroups', function ($request, $response, $args) {

	try{	
		$sql = "SELECT * FROM ec_group";
		$stmt1 = $this->db->prepare($sql);
		$stmt1->execute();
		$nRows = count($stmt1->fetchAll());
		if($nRows > 0){	
			$sql = "SELECT * FROM ec_group";
			$stmt1 = $this->db->prepare($sql);
			$stmt1->execute();
			$groups = $stmt1->fetchAll();	
			$json = array("error" => false, "message" => $groups);
		}else{
			
			$json = array("error" => false, "message" =>"Not Found!");
		}
    }catch(PDOException $e){
			
	    $json = array("error" => true, "message" => $e->getMessage());
    }	
	
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 15. Get User detail
/***********************************************************************/
$app->post('/getUserDetail', function ($request, $response, $args) {
    $input = $request->getParsedBody();
	$user_id = $input['user_id'];
	if(!empty($user_id)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		try{	
			$sql = "SELECT * FROM ec_user WHERE is_active =1 AND user_id ='".$user_id."' ";
			//$users = $this->db->query($sql);
			$stmt = $this->db->prepare($sql);
			$stmt->execute(); 
			$result = count($stmt->fetchAll());
			if($result >0 ){
				$sql = "SELECT * FROM ec_user WHERE is_active =1 AND user_id ='".$user_id."' ";
			    $stmt = $this->db->prepare($sql);
			    $stmt->execute();				
				while($row = $stmt->fetch()){					
				
				  $photo = $row['photo'];				
				 
				}
				$user_image_url = $baseUrl."/uploads/user/".$photo;
				$sql = "SELECT * FROM ec_user WHERE is_active =1 AND user_id ='".$user_id."' ";
			    $stmt = $this->db->prepare($sql);
			    $stmt->execute();
				$user = $stmt->fetchAll();
				$user[0]['photo'] = $user_image_url;				
				$json = array("error" => false, "message" => $user);
			}else{
				
				$json = array("error" => false, "message" =>"Not Found!");
			}
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 16. Get User Groups
/***********************************************************************/
$app->post('/getUserCreatedGroups', function ($request, $response, $args) {
    $input = $request->getParsedBody();
	$uri = $request->getUri();
    $baseUrl = $uri->getBaseUrl();	
	if(!empty($input['group_admin'])){
		$group_admin = $input['group_admin'];
		try{	
			$stmt1 = $this->db->prepare("SELECT * FROM ec_group_member");
			$stmt1->execute();
			$nRows = count($stmt1->fetchAll());
			if($nRows > 0){	
				$stmt1->execute();
				$allGroups = $stmt1->fetchAll();
				$groupIds = array(0);
				foreach($allGroups as $group){
					$groupMemberIds = unserialize($group['member_id']);
					//print_r($groupMembers);
					if(in_array($group_admin,$groupMemberIds)){
						$groupIds[] = $group['group_id'];					
					}							
				}
				$groupIds = implode(',', array_map('intval', $groupIds));
				$stmt2 = $this->db->prepare("SELECT * FROM ec_group WHERE group_id IN ($groupIds)");
				$stmt2->execute();
				$nRows = count($stmt2->fetchAll());
				if($nRows>0){
					$stmt2->execute();
					$memberInGroups = $stmt2->fetchAll();
					$i =0;
					foreach($memberInGroups as $group){
					$memberInGroups[$i]['photo'] = $baseUrl."/uploads/group/".$group['photo'];
						$i++; 
					}
					$json = array("error" => false, "message" => $memberInGroups);	
				}else{
					
					$json = array("error" => true, "message" =>"Not Found!");
				}
					
			}else{
				
				$json = array("error" => true, "message" =>"No Group Found!");
			}
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 17. Get Registered Contacts
/***********************************************************************/
$app->post('/getRegisteredContact', function ($request, $response, $args) {
	$input = $request->getParsedBody();
	$uri = $request->getUri();
    $baseUrl = $uri->getBaseUrl();	
	if(!empty($input['user_id']) && !empty($input['contact'])){
		$user_id = $input['user_id'];
		$contact = explode(',',$input['contact']);
		try{	
		    //$mobile = join(",",$contact);
			//$sql = "SELECT * FROM `ec_user` WHERE `mobile` IN ($mobile)";	
			$contact_nos = implode(',', array_map('intval', $contact));		
		    $sql = "SELECT user_id,user_name,mobile,email,photo,status,is_active FROM `ec_user` WHERE `mobile` IN ($contact_nos)";
			$stmt1 = $this->db->prepare($sql);
			$stmt1->execute();
			$nRows = count($stmt1->fetchAll());
			$contacts = $stmt1->fetchAll();
			if($nRows > 0){	
				//$sql = "SELECT user_id,user_name,mobile,email,photo,status,is_active FROM `ec_user` WHERE `mobile` IN ($contact_nos)";
				//$stmt1 = $this->db->prepare($sql);
				$stmt1->execute();
				$contacts = $stmt1->fetchAll();
                $i =0;
                foreach($contacts as $contact){
					
					$contacts[$i]['photo'] = $baseUrl."/uploads/user/".$contact['photo'];
					$stmt2 = $this->db->prepare("SELECT * FROM ec_user_contact WHERE user_id= '".$user_id."'");
					$stmt2->execute(); 
					$result = count($stmt2->fetchAll());
					if($result >0 ){
						$stmt2->execute(); 	
						$added_contacts = $stmt2->fetchAll();
						foreach($added_contacts as $added_contact){							
						 $added_contact_user_id[] = $added_contact['contact'];							
						}
						
					}else{
						$added_contact_user_id = array(0);
					}
					if(in_array($contact['user_id'],$added_contact_user_id)){
						
						$contacts[$i]['in_contact'] = true;
						
					}else{
						
						$contacts[$i]['in_contact'] = false;	
					}	
					$i++; 
				} 				
				$json = array("error" => false, "message" => $contacts);
			}else{
				
				$json = array("error" => false, "message" =>"No Contact Found!");
			}
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}
	}else{
		
		$json = array("error" => true, "message" =>"UserID and Contact not empty!");
	}
	
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 18. Get User Joined Groups
/***********************************************************************/
$app->post('/getUserJoinedGroups', function ($request, $response, $args) {
    $input = $request->getParsedBody();
	$user_id = $input['user_id'];
	if(!empty($user_id)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		try{
          	$sql = "SELECT * FROM ec_user WHERE is_active =1 AND user_id ='".$user_id."' ";
			$stmt = $this->db->prepare($sql);
			$stmt->execute(); 
			$result = count($stmt->fetchAll());
			if($result >0 ){			
				$sql = "SELECT * FROM ec_group_member";
				$stmt1 = $this->db->prepare($sql);
				$stmt1->execute();
				$nRows = count($stmt1->fetchAll());
				if($nRows > 0){	
					$sql = "SELECT * FROM ec_group_member";
					$stmt1 = $this->db->prepare($sql);
					$stmt1->execute();
					$groupMembers = $stmt1->fetchAll();
					$i =0;
					$userJoinedGroupIds = array();
					foreach($groupMembers as $groupMember){
						$sql = "SELECT * FROM ec_group_member WHERE group_id = '".$groupMember['group_id']."'";
						$stmt1 = $this->db->prepare($sql);
						$stmt1->execute();
						while($row = $stmt1->fetch()){
							$group_members = unserialize($row['member_id']);
							if(in_array($user_id,$group_members))
							{
							 $userJoinedGroupIds[] = $row['group_id'];	
							}	
							//$group_photo = $row['photo'];
						}					
						//$groups[$i]['photo'] = $baseUrl."/uploads/group/".$group_photo;
						//$i++; 
					} 
					$json = array("error" => false, "message" => $userJoinedGroupIds);
				}else{
					
					$json = array("error" => false, "message" =>"Not Found!");
				}
			}else{
				
				$json = array("error" => false, "message" =>"Invalid UserID!");
			}	
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 19. Get Group Details
/***********************************************************************/
$app->post('/getGroupDetails', function ($request, $response, $args) {
    $input = $request->getParsedBody();
	$group_id = $input['group_id'];
	if(!empty($group_id)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		try{	
			$sql = "SELECT * FROM ec_group WHERE group_id = '".$group_id."'";
			$stmt1 = $this->db->prepare($sql);
			$stmt1->execute();
			$nRows = count($stmt1->fetchAll());
			if($nRows > 0){			
			    $groupDetail = array();
				$sql = "SELECT * FROM ec_group WHERE group_id = '".$group_id."'";
			    $stmt1 = $this->db->prepare($sql);
			    $stmt1->execute();
			    while($row = $stmt1->fetch()){
					$groupDetail['group_id'] = $row['group_id'];
					$groupDetail['group_name'] = $row['group_name'];
					$groupDetail['photo'] = $baseUrl."/uploads/group/".$row['photo'];
					$group_admin_id = $row['group_admin'];
				}
                $sql = "SELECT * FROM ec_user WHERE user_id = '".$group_admin_id."'";
			    $stmt1 = $this->db->prepare($sql);
			    $stmt1->execute();
				$nRows = count($stmt1->fetchAll());
				if($nRows > 0){	
				    $sql = "SELECT * FROM ec_user WHERE user_id = '".$group_admin_id."'";
			        $stmt1 = $this->db->prepare($sql);
			        $stmt1->execute();
					while($row = $stmt1->fetch()){
						
						$groupDetail['group_admin'] = $row['user_name'];
						
					} 
                }else{
					$groupDetail['group_admin'] = null;
				} 				
				$sql = "SELECT group_id,member_id FROM ec_group_member WHERE group_id = '".$group_id."'";
				$stmt1 = $this->db->prepare($sql);
				$stmt1->execute();
				$nRows = count($stmt1->fetchAll());
				if($nRows > 0){	
					//$member_id = array();
					$sql = "SELECT group_id,member_id FROM ec_group_member WHERE group_id = '".$group_id."'";
				    $stmt1 = $this->db->prepare($sql);
				    $stmt1->execute();
					while($row = $stmt1->fetch()){					
						$member_id = unserialize($row['member_id']);				
					}
					if(count($member_id)==0){$member_id=array(0);}
					$sql = 'SELECT user_id,user_name,mobile,email,updated_at,created_at,photo,status,is_active FROM ec_user WHERE is_active =1 AND user_id IN ('.implode(',', array_map('intval', $member_id)).')';
					$stmt1 = $this->db->prepare($sql);
					$stmt1->execute();
					$members = $stmt1->fetchAll();
					$i =0;
					foreach($members as $member){
						$members[$i]['photo'] = $baseUrl."/uploads/user/".$member['photo'];
						$i++; 
					} 	
					$groupDetail['member'] = $members;
                }else{
					$groupDetail['member'] = array();
				} 					
				$json = array("error" => false, "message" => $groupDetail);
			}else{
				
				$json = array("error" => false, "message" =>"Group Not Found!");
			}
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 20. Update Group Photo
/***********************************************************************/
$app->post('/updateGroupPhoto', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	$group_id = $input['group_id'];
	$photo = $input['photo'];
	if(!empty($group_id) && !empty($photo)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		$sql = "SELECT * FROM ec_group where group_id='".$group_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			$sql = "SELECT * FROM ec_group where group_id='".$group_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
            while($row = $stmt->fetch()){
				$oldPhoto = "uploads/group/".$row['photo'];
			}			
			if($oldPhoto != "uploads/group/group-icon.png"){
				if(file_exists($oldPhoto)){					
                   unlink($oldPhoto);
				}				
			}
			$randName = md5(uniqid(rand() * time()));
			//decode the image
			$decodedImage = base64_decode($photo);
			$image_name = $group_id."_".$randName.".jpg";
			//upload the image
			$filepath = "uploads/group/";		
			$path = file_put_contents($filepath.$image_name, $decodedImage);
		 					
			$sql = "UPDATE `ec_group` SET `photo` = '".$image_name."'  WHERE `group_id` = '".$group_id."'";
			$stmt = $this->db->prepare($sql);
			$result = $stmt->execute();
			if($result){
				
				$group_image_url = $baseUrl."/uploads/group/".$image_name;	 
				$json = array("error" => false, "message" => $group_image_url); 
					 
			}else{
					 
				$json = array("error" => true, "message" => "Database Error!");
			}
					
		}else{
			
			$json = array("error" => true, "message" =>"Invalid GroupID!");
		}	
	}else{
		
		$json = array("error" => true, "message" =>"GroupID and Photo not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 21. Set Groupname
/***********************************************************************/
$app->post('/setGroupname', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	
	$group_id = $input['group_id'];
	$groupname = $input['groupname'];
	if(!empty($group_id) && !empty($groupname)){
		$sql = "SELECT * FROM ec_group where group_id='".$group_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			try{
			 
				$sql = "UPDATE `ec_group` SET `group_name` = '".$groupname."'  WHERE `group_id` = '".$group_id."'";
				$stmt = $this->db->prepare($sql);
				$result = $stmt->execute();
				if($result){
					 
					 $json = array("error" => false, "groupname"=>$groupname, "message" => "Groupname updated successfully!"); 
					 
				}else{
					 
					 $json = array("error" => true, "message" => "Database Error!");
			    }
			}catch(PDOException $e){
				
				 $json = array("error" => true, "message" => $e->getMessage());
			}			
		}else{
			
			$json = array("error" => true, "message" =>"Invalid GroupID!");
		}	
	}else{
		
		$json = array("error" => true, "message" =>"GroupID and GroupName not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 22. Exit Member from Group
/***********************************************************************/
$app->post('/exitFromGroup', function ($request, $response, $args){	
	$input = $request->getParsedBody();	
	if(!empty($input['group_id']) && !empty($input['member_id'])){
		$group_id = $input['group_id'];
		$member_id = $input['member_id'];		
		try{			
			$stmt1 = $this->db->prepare("SELECT * FROM ec_group_member WHERE group_id = '".$group_id."'");
			$stmt1->execute(); 	
			$rowCount = count($stmt1->fetchAll());
		    if($rowCount>0){
				$stmt1->execute();
			    while($row = $stmt1->fetch()){					
					$member_ids = unserialize($row['member_id']);				
				}
				//print_r($member_ids);
				if(($key = array_search($member_id, $member_ids)) !== false) {					
                    unset($member_ids[$key]);
					$reindexed_members = serialize(array_values($member_ids));
					$stmt2 = $this->db->prepare("UPDATE ec_group_member SET member_id ='".$reindexed_members."' WHERE group_id = '".$group_id."'");
					$result = $stmt2->execute();
					if($result){
								
						$json = array("error" => false, "message" => "Exit Successfully!");
					}else{
						
						$json = array("error" => false, "message" => "Database Error!");
					}
                }else{
					
					$json = array("error" => false, "message" => "Invalid MemberID!");
				}
							
			}else{
				
				$json = array("error" => false, "message" => "Invalid GroupID!");
				
			}
		}catch(PDOException $pe){
				
			$json = array("error" => true, "message" => $pe->getMessage());
		}	
    }else{
		
		$json = array("error" => true, "message" =>"GroupID and MemberID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 23. Send Group Message
/***********************************************************************/
$app->post('/sendGroupMessage', function ($request, $response, $args) {
	
	$input = $request->getParsedBody();
	
	$group_id = $input['group_id'];
	$sender_id = $input['sender_id'];
	$message = $input['message'];
	$type = $input['type']; // text,image,video,voice,location
	
    if(!empty($group_id) && !empty($sender_id) && !empty($message) && !empty($type)){
		$uri = $request->getUri();
	    $baseUrl = $uri->getBaseUrl();
        switch($type){
		
			case "text": $msg_type = 0;
				break;
			case "image": 
				$msg_type = 1;
				$randName = md5(uniqid(rand() * time()));
				//decode the image
				$decodedImage = base64_decode($message);
				$message = $randName.".jpg";
				//upload the image
			    $filepath = "uploads/message/";		
			    $path = file_put_contents($filepath.$message, $decodedImage);
			    //$imageUrl = $baseUrl."/uploads/message/".$message;
				$message = $baseUrl."/uploads/message/".$message;	
				break;
			case "video": $msg_type = 2;
				$randName = md5(uniqid(rand() * time()));
				//decode the image
				$decodedVideo = base64_decode($message);
				$message = $randName.".mp4";
				//upload the image
				$filepath = "uploads/message/";		
				$path = file_put_contents($filepath.$message, $decodedVideo);
				//$videoUrl = $baseUrl."/uploads/message/".$message;
				$message = $baseUrl."/uploads/message/".$message;
				break;
			case "voice": $msg_type = 3;
			    $randName = md5(uniqid(rand() * time()));
				//decode the image
				$decodedVoice = base64_decode($message);
				$message = $randName.".mp3";
				//upload the image
				$filepath = "uploads/message/";		
				$path = file_put_contents($filepath.$message, $decodedVoice);
				//$voiceUrl = $baseUrl."/uploads/message/".$message;
				$message = $baseUrl."/uploads/message/".$message;
				break;
			case "location": $msg_type = 5;
				break;	
			default:
				break;
	    }
		$sql = "SELECT * FROM ec_group where group_id='".$group_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			$group_name = '';
			$group_admin = '';
			$group_image_url = '';
		    $sql = "SELECT * FROM ec_group where group_id='".$group_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute(); 
			while($row = $stmt->fetch()){
				
			    $group_id = $row['group_id'];
				$group_name = $row['group_name'];
				$group_admin = $row['group_admin'];
				$group_image_url = $baseUrl."/uploads/group/".$row['photo'];
				
			}		
			//$member_id = array();
			$sql = "SELECT group_id,member_id FROM ec_group_member WHERE group_id = '".$group_id."'";
			$stmt1 = $this->db->prepare($sql);
			$stmt1->execute();			
			while($row = $stmt1->fetch()){
				
			    $member_id = unserialize($row['member_id']);		       
				
			}
			if(($key = array_search($sender_id, $member_id)) !== false){
				unset($member_id[$key]);
			}//Exclude sender_id. 		
			$user_ids = implode(',',$member_id);
            $sql = 'INSERT INTO ec_group_message(group_id,user_id,sender_id,type,message) VALUES(:group_id,:user_id,:sender_id,:type,:message)';
			$stmt_update = $this->db->prepare($sql);
			$params_users = array(':group_id' =>$group_id,':user_id' =>$user_ids, ':sender_id' =>$sender_id,':type'=>$msg_type, ':message'=>$message);
			$stmt_update->execute($params_users);				
			$last_msg_id = $this->db->lastInsertId();
		    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			$rowCount = count($stmt->fetchAll());
			$sender_name = null;
			$sender_image = null;
			$mobile = null;
			if($rowCount>0){
			    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				while($row = $stmt->fetch()){
					
					$sender_name = $row['user_name'];
					$mobile = $row['mobile'];
					$sender_image = $row['photo'];
				 //$registrationIDs = array($row['registration_token']);
				 
				}
				$sender_image_url = $baseUrl."/uploads/user/".$sender_image;
			}
			$registrationIDs = array();
			$sql = 'SELECT * FROM ec_user where user_id IN ('.implode(',', array_map('intval', $member_id)).')';
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			while($row = $stmt->fetch()){
				
			 $registrationIDs[] = $row['registration_token'];
			 			 
			}			
			// API access key from Google API's Console
			
			 define( 'API_ACCESS_KEY', 'AIzaSyAscUsZbNdHpw69abg0ApcKhG9ZoJpcYMY' ); 
			   
			// prep the bundle
			
			$headers = array
			(
				'Content-Type: application/json',
				'Authorization:key='.API_ACCESS_KEY		
			);
			date_default_timezone_set('Asia/Kolkata');
			$date = date('Y-m-d H:i:s'); 
			$fields = array
			(
			    'message_id'    => $last_msg_id,
				'group_id' => $group_id,
				'group_name' => $group_name,
				'group_admin' => $group_admin,
				'group_photo' => $group_image_url,
				'sender_id' 	=> $sender_id,
				'sender_name' 	=> $sender_name,
				'sender_image' 	=> $sender_image_url,
				'mobile' 		=> $mobile,
			    'receiver_id' 	=> $user_ids,
				'chat_type' 	=> 'group',
				'type' => $type,
				'message' 	=> $message,
				'date' 	=> $date

			);
			$messaage = array
			(
				'content_available' => true ,
  	     	    'priority' =>  'high',
				//'to'	=> $registrationID,
				'registration_ids' 	=> $registrationIDs,
				'data'			=> $fields

			); 

			$url = 'https://fcm.googleapis.com/fcm/send';
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, $url );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $messaage ) );
			$result = curl_exec($ch);
			curl_close($ch);
			$json = json_decode($result,true);
		    // if($json['success'] == 1){
				
				
			// }
		}else{
			
			$json = array("error" => true, "message" =>"Invalid GroupID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"UserID, SenderID, Message and Type not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 24. One to One Contact Refer
/***********************************************************************/
$app->post('/oneToOneContactRefer', function($request, $response, $args) {
	
	$input = $request->getParsedBody();	
	$sender_id = $input['sender_id'];
	$user_id = $input['receiver_id'];
	$contact_id = $input['contact_id'];
	$contact_no = $input['contact_no'];
	$contact_image = $input['contact_image'];
	if(!empty($user_id) && !empty($sender_id)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			$randName = md5(uniqid(rand() * time()));
		    //decode the image
		    $decodedImage = base64_decode($contact_image);
			$contact_image = $randName.".jpg";
			//upload the image
			$filepath = "uploads/message/contact/";		
			$path = file_put_contents($filepath.$contact_image, $decodedImage);
			$imageUrl = $baseUrl."/uploads/message/contact/".$contact_image;
			$message = $contact_id."|".$contact_no."|".$imageUrl;
			$update = "INSERT INTO ec_user_message(user_id,sender_id,message,type,created_at) VALUES(:user_id,:sender_id,:message,:type, CONVERT_TZ(NOW(),'+8:00','+5:30'))";
			$stmt_update = $this->db->prepare($update);
			$params_users = array(':user_id' =>$user_id, ':sender_id' =>$sender_id, ':message'=>$message,':type'=>4);
			$stmt_update->execute($params_users);
			$last_msg_id = $this->db->lastInsertId();
		    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			$rowCount = count($stmt->fetchAll());
			$sender_name = null;
			$sender_image = null;
			$mobile = null;
			if($rowCount>0){
			    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				while($row = $stmt->fetch()){
					
					$sender_name = $row['user_name'];
					$mobile = $row['mobile'];
					$sender_image = $row['photo'];
				 //$registrationIDs = array($row['registration_token']);
				 
				}
				$sender_image_url = $baseUrl."/uploads/user/".$sender_image;
			}
			$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			while($row = $stmt->fetch()){
				
			 $registrationID = $row['registration_token'];
			 //$registrationIDs = array($row['registration_token']);
			 
			}
			
			// API access key from Google API's Console
			
			 define( 'API_ACCESS_KEY', 'AIzaSyAscUsZbNdHpw69abg0ApcKhG9ZoJpcYMY' ); 
			   
			// prep the bundle
			
			$headers = array
			(
				'Content-Type: application/json',
				'Authorization:key='.API_ACCESS_KEY		
			);
			$date = date('Y-m-d H:i:s'); 
			$fields = array
			(
			    'message_id'    => $last_msg_id,
				'sender_id' 	=> $sender_id,
				'sender_name' 	=> $sender_name,
				'sender_image' 	=> $sender_image_url,
				'mobile' 		=> $mobile,
			    'receiver_id' 	=> $user_id,
				'chat_type' 	=> 'one_to_one',	
				'type' 			=> 'contact',
				'contact_id'	=> $contact_id,
                'contact_no'	=> $contact_no,
				'contact_image' => $imageUrl,	
				//'message' 	=> $message,
				'date' 	=> $date

			);
			$messaage = array
			(
				'content_available' => true ,
  	     	    'priority' =>  'high',
				'to'	=> $registrationID,
				//'registration_ids' 	=> $registrationIDs,
				'data'			=> $fields

			); 

			$url = 'https://fcm.googleapis.com/fcm/send';
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, $url );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $messaage ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			$json = json_decode($result,true);
		    // if($json['success'] == 1){
				
				
			// }
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"SenderID and ReceiverID not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 25. Group Contact Refer
/***********************************************************************/
$app->post('/groupContactRefer', function($request, $response, $args) {
	
	$input = $request->getParsedBody();	
	$sender_id = $input['sender_id'];
	$group_id = $input['group_id'];
	$contact_id = $input['contact_id'];
	$contact_no = $input['contact_no'];
	$contact_image = $input['contact_image'];
	if(!empty($group_id) && !empty($sender_id)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		$sql = "SELECT * FROM ec_group where group_id='".$group_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			$sql = "SELECT * FROM ec_group_member where group_id='".$group_id."'";
			$stmt = $this->db->prepare($sql);
			$stmt->execute(); 
			$rowCount = count($stmt->fetchAll());
			if($rowCount>0){
				$randName = md5(uniqid(rand() * time()));
				//decode the image
				$decodedImage = base64_decode($contact_image);
				$contact_image = $randName.".jpg";
				//upload the image
				$filepath = "uploads/message/contact/";		
				$path = file_put_contents($filepath.$contact_image, $decodedImage);
				$imageUrl = $baseUrl."/uploads/message/contact/".$contact_image;
				$message = $contact_id."|".$contact_no."|".$imageUrl;
				$group_name = '';
				$group_admin = '';
				$group_image_url = '';
				$sql = "SELECT * FROM ec_group where group_id='".$group_id."'";
				$stmt = $this->db->prepare($sql);
				$stmt->execute(); 
				while($row = $stmt->fetch()){
					
					$group_id = $row['group_id'];
					$group_name = $row['group_name'];
					$group_admin = $row['group_admin'];
					$group_image_url = $baseUrl."/uploads/group/".$row['photo'];
					
				}		
				//$member_id = array();
				$sql = "SELECT group_id,member_id FROM ec_group_member WHERE group_id = '".$group_id."'";
				$stmt1 = $this->db->prepare($sql);
				$stmt1->execute();			
				while($row = $stmt1->fetch()){
					
					$member_id = unserialize($row['member_id']);		       
					
				}
				if(($key = array_search($sender_id, $member_id)) !== false){
					unset($member_id[$key]);
			    }//Exclude sender_id. 	
				$user_ids = implode(',',$member_id);
				$sql = 'INSERT INTO ec_group_message(group_id,user_id,sender_id,message,type) VALUES(:group_id,:user_id,:sender_id,:message,:type)';
				$stmt_update = $this->db->prepare($sql);
				$params_users = array(':group_id' =>$group_id,':user_id' =>$user_ids, ':sender_id' =>$sender_id,':message'=>$message,':type'=>4);
				$stmt_update->execute($params_users);				
				$last_msg_id = $this->db->lastInsertId();
				$sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				$rowCount = count($stmt->fetchAll());
				$sender_name = null;
				$sender_image = null;
				$mobile = null;
				if($rowCount>0){
					$sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
					$stmt = $this->db->prepare($sql);
					$stmt->execute();
					while($row = $stmt->fetch()){
						
						$sender_name = $row['user_name'];
						$mobile = $row['mobile'];
						$sender_image = $row['photo'];
					 //$registrationIDs = array($row['registration_token']);
					 
					}
					$sender_image_url = $baseUrl."/uploads/user/".$sender_image;
				}
				$registrationIDs = array();
				$sql = 'SELECT * FROM ec_user where user_id IN ('.implode(',', array_map('intval', $member_id)).')';
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				while($row = $stmt->fetch()){
					
				 $registrationIDs[] = $row['registration_token'];
							 
				}			
				// API access key from Google API's Console
				
				 define( 'API_ACCESS_KEY', 'AIzaSyAscUsZbNdHpw69abg0ApcKhG9ZoJpcYMY' ); 
				   
				// prep the bundle
				
				$headers = array
				(
					'Content-Type: application/json',
					'Authorization:key='.API_ACCESS_KEY		
				);
				date_default_timezone_set('Asia/Kolkata');
				$date = date('Y-m-d H:i:s'); 
				$fields = array
				(
					'message_id'    => $last_msg_id,
					'group_id' => $group_id,
					'group_name' => $group_name,
					'group_admin' => $group_admin,
					'group_photo' => $group_image_url,
					'sender_id' 	=> $sender_id,
					'sender_name' 	=> $sender_name,
					'sender_image' 	=> $sender_image_url,
					'mobile' 		=> $mobile,
					'receiver_id' 	=> $user_ids,
					'chat_type'     => 'group',	
					'type'          => 'contact',
					'contact_id'	=> $contact_id,
					'contact_no'	=> $contact_no,
					'contact_image' => $imageUrl,	
					//'message' 	=> $message,
					'date' 	=> $date

				);
				$messaage = array
				(
					'content_available' => true ,
					'priority' =>  'high',
					//'to'	=> $registrationID,
					'registration_ids' 	=> $registrationIDs,
					'data'			=> $fields

				); 

				$url = 'https://fcm.googleapis.com/fcm/send';
				$ch = curl_init();
				curl_setopt( $ch,CURLOPT_URL, $url );
				curl_setopt( $ch,CURLOPT_POST, true );
				curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
				curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $messaage ) );
				$result = curl_exec($ch);
				curl_close($ch);
				$json = json_decode($result,true);
				// if($json['success'] == 1){
					
					
				// }
			}else{
				
				$json = array("error" => true, "message" =>"No Member in gorup!");
			}	
		}else{
				
			$json = array("error" => true, "message" =>"Invalid GroupID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"SenderID and ReceiverID not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 26. Add Contact
/***********************************************************************/
$app->post('/addContact', function($request, $response, $args) {
	
	$input = $request->getParsedBody();	
	$sender_id = $input['sender_id'];
	$receiver_id = $input['receiver_id'];
	$status = $input['status'];
	if(!empty($sender_id) && !empty($receiver_id) && !empty($status)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		$sql = "SELECT * FROM ec_user_invitation WHERE sender_id= '".$sender_id."' AND receiver_id= '".$receiver_id."' AND status='pending'"; 
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){	
			try{
				if($status == 'accepted'){
					$sql = "UPDATE ec_user_invitation SET status='".$status."' WHERE sender_id= '".$sender_id."' AND receiver_id='".$receiver_id."'"; 
				    $stmt = $this->db->prepare($sql);
				    $result = $stmt->execute();
                    if($result){
	
							$sql = 'INSERT INTO ec_user_contact(user_id,contact) VALUES(:user_id,:contact)';
							$stmt = $this->db->prepare($sql);
							$params_users = array(':user_id' =>$sender_id,':contact' =>$receiver_id);
							$result1 = $stmt->execute($params_users);	
							$params_users = array(':user_id' =>$receiver_id,':contact' =>$sender_id);
							$result2 = $stmt->execute($params_users);
							if($result1 && $result2){
								$sql = "DELETE FROM ec_user_invitation WHERE sender_id= '".$sender_id."' AND receiver_id='".$receiver_id."' AND status='accepted'";
								$stmt = $this->db->prepare($sql);
								$delete = $stmt->execute();
								if($delete){
									$json = array("error" => false, "message" => "Accepted!");	
								}else{
									
									$json = array("error" => true, "message" => "Database Error!");
								}							
							}else{
								
								$json = array("error" => true, "message" => "Database Error!");
							}
									
					}					
				}elseif($status == 'rejected'){
					
					$sql = "DELETE FROM ec_user_invitation WHERE sender_id= '".$sender_id."' AND receiver_id='".$receiver_id."' AND status='pending'";
					$stmt = $this->db->prepare($sql);
					$delete = $stmt->execute();
					if($delete){						
						$json = array("error" => false, "message" => "Rejected!");						
					}else{
						
						$json = array("error" => true, "message" => "Database Error!");						
					}	
				}else{
					
					$json = array("error" => true, "message" => "Invalid status!");
				}
						
			}catch(PDOException $pe){					
					$json = array("error" => true, "message" => $pe->getMessage());
					
			}		
		}else{
			
			$json = array("error" => true, "message" =>"Invalid SenderID or ReceiverID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"SenderID,ReceiverID and Status not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 27. Send Invitation
/***********************************************************************/
$app->post('/sendInvitation', function($request, $response, $args) {	
	$input = $request->getParsedBody();
	$uri = $request->getUri();
    $baseUrl = $uri->getBaseUrl();
	if(!empty($input['sender_id']) && !empty($input['receiver_mobile'])){
		$sender_id = $input['sender_id'];
	    $receiver_mobile = $input['receiver_mobile'];		
		$stmt1 = $this->db->prepare("SELECT * FROM ec_user where user_id = :sender_id"); // $stmt1
		$params1 = array(':sender_id' =>$sender_id);
		$stmt1->execute($params1);
        $rowCount = count($stmt1->fetchAll());
		if($rowCount>0){            		
			$stmt2 = $this->db->prepare("SELECT * FROM ec_user where mobile = :receiver_mobile"); // $stmt2
			$params2 = array(':receiver_mobile' =>$receiver_mobile);
			$stmt2->execute($params2);
			$rowCount = count($stmt2->fetchAll());
			if($rowCount>0){ 
                $stmt2->execute($params2);
				while($row = $stmt2->fetch()){				
					$receiver_id = $row['user_id'];				
				} 
				if($sender_id != $receiver_id){
					$sql = "SELECT * FROM ec_user_contact WHERE user_id= '".$sender_id."' AND contact= '".$receiver_id."'"; 
					$stmt = $this->db->prepare($sql);
					$stmt->execute(); 
					$rowCount = count($stmt->fetchAll());
					if($rowCount == 0){	
						$sender_name = null;
						$sender_image = null;
						$sender_mobile = null;		
						$stmt1->execute($params1);					
						while($row = $stmt1->fetch()){
							
							$sender_name = $row['user_name'];
							$sender_mobile = $row['mobile'];
							$sender_image = $row['photo'];

						}
						$sender_image_url = $baseUrl."/uploads/user/".$sender_image;		
						$stmt2->execute($params2);
						while($row = $stmt2->fetch()){
						 $receiver_name = $row['user_name'];
						 $receiver_mobile = $row['mobile']; 			 
						 $registrationID = $row['registration_token'];
						 //$registrationIDs = array($row['registration_token']);
						 
						}
						if(empty($sender_name)){$sender_name = $sender_mobile;}
						if(empty($receiver_name)){$receiver_name = $receiver_mobile;}
						$message  = $sender_name." would like to add you on EraChat<br>";
						$message .= "Hi ".$receiver_name.", I'd like to add you as a contact.";
						// API access key from Google API's Console
						
						 define( 'API_ACCESS_KEY', 'AIzaSyAscUsZbNdHpw69abg0ApcKhG9ZoJpcYMY' ); 
						   
						// prep the bundle
						
						$headers = array
						(
							'Content-Type: application/json',
							'Authorization:key='.API_ACCESS_KEY		
						);
						$date = date('Y-m-d H:i:s'); 
						$fields = array
						(
							//'message_id'    => $last_msg_id,
							'sender_id' 	=> $sender_id,
							'sender_name' 	=> $sender_name,
							'sender_image' 	=> $sender_image_url,
							'mobile' 		=> $sender_mobile,
							'receiver_id' 	=> $receiver_id,
                            'chat_type' 	=> 'invitation', 							
							'type' 			=> 'invitation',				
							'message' 	    => $message,
							'date' 	        => $date

						);
						$messaage = array
						(
							'content_available' => true ,
							'priority' =>  'high',
							'to'	=> $registrationID,
							//'registration_ids' 	=> $registrationIDs,
							'data'			=> $fields

						); 

						$url = 'https://fcm.googleapis.com/fcm/send';
						$ch = curl_init();
						curl_setopt( $ch,CURLOPT_URL, $url );
						curl_setopt( $ch,CURLOPT_POST, true );
						curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
						curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
						curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
						curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $messaage ) );
						$result = curl_exec($ch );
						curl_close( $ch );
						$json = json_decode($result,true);
						if($json['success'] == 1){
							try{						
								$stmt3 = $this->db->prepare("SELECT * FROM ec_user_invitation WHERE sender_id= :sender_id AND receiver_id=:receiver_id AND status=:status");  // $stmt3
								$params3 = array(':sender_id' =>$sender_id,':receiver_id' =>$receiver_id,':status' =>'pending');
								$stmt3->execute($params3);
								$rowCount = count($stmt3->fetchAll());
								if($rowCount == 0){
									$stmt4 = $this->db->prepare("INSERT INTO ec_user_invitation(sender_id,receiver_id,status) VALUES(:sender_id,:receiver_id,:status)"); //$stmt4
									$params4 = array(':sender_id' =>$sender_id,':receiver_id' =>$receiver_id, ':status' =>'pending');
									$stmt4->execute($params4);						
								}else{
										
								}				
							}catch(PDOException $pe){					
								$json = array("error" => true, "message" => $pe->getMessage());
								
							}				
						}
					}else{						
										
						$json = array("error" => true, "message" => "Contact already added!");
					}
				}else{						
										
					$json = array("error" => true, "message" => "Try Again!");
				}	
			}else{
				
				$json = array("error" => true, "message" =>"Invalid Receiver Mobile!");
			}	
		}else{
			
			$json = array("error" => true, "message" =>"Invalid SenderID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"SenderID and ReceiverID not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 28. Get Invitation
/***********************************************************************/
$app->post('/getInvitation', function ($request, $response, $args) {
    $input = $request->getParsedBody();
	$user_id = $input['user_id'];
	if(!empty($user_id)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		try{	
			$sql = "SELECT * FROM ec_user_invitation WHERE receiver_id= '".$user_id."' AND status='pending'"; 
			//$users = $this->db->query($sql);
			$stmt = $this->db->prepare($sql);
			$stmt->execute(); 
			$result = count($stmt->fetchAll());
			if($result >0 ){
				//$sql = "SELECT * FROM ec_user_invitation WHERE receiver_id= '".$user_id."' AND status='pending'"; 
			    //$stmt = $this->db->prepare($sql);
						
			    $stmt->execute();			
				$invitations = $stmt->fetchAll();
				$i=0; 
				foreach($invitations as $invitation){
					$sql = "SELECT * FROM ec_user WHERE user_id='".$invitation['sender_id']."'";
					$stmt = $this->db->prepare($sql);
			        $stmt->execute(); 
					while($row = $row = $stmt->fetch()){
						$sender_name  = $row['user_name'];
						$sender_mobile  = $row['mobile'];
						$sender_image = $baseUrl."/uploads/user/".$row['photo'];
					}
					//array_splice($invitations, 2, 0, $sender_name);
					//array_splice($invitations, 3, 0, $sender_image); 	
					$invitations[$i]['sender_name'] =  $sender_name;
					$invitations[$i]['sender_mobile'] =  $sender_mobile;
					$invitations[$i]['sender_image'] = $sender_image;
					$i++;
				}
				
				$json = array("error" => false, "message" => $invitations);
			}else{
				
				$json = array("error" => false, "message" =>"Invalid UserID!");
			}
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 29. Get Contact
/***********************************************************************/
$app->post('/getContact', function ($request, $response, $args) {
    $input = $request->getParsedBody();
	$user_id = $input['user_id'];
	if(!empty($user_id)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		try{	
			$sql = "SELECT * FROM ec_user_contact WHERE user_id= '".$user_id."'"; 
			//$users = $this->db->query($sql);
			$stmt = $this->db->prepare($sql);
			$stmt->execute(); 
			$result = count($stmt->fetchAll());
			if($result >0 ){
				//$sql = "SELECT * FROM ec_user_invitation WHERE sender_id= '".$user_id."' AND status='pending'"; 
			    //$stmt = $this->db->prepare($sql);
			    $stmt->execute();
                while($row = $stmt->fetch()){
					
					$contact[] = $row['contact'];  
					
				}
				$user_ids = implode(',', array_map('intval', $contact));
				$sql = "SELECT user_id,user_name,mobile,email,updated_at,created_at,photo,status,is_active FROM ec_user WHERE user_id IN ($user_ids)";
			    $stmt = $this->db->prepare($sql);
			    $stmt->execute();
				$users = $stmt->fetchAll();	
				$i =0;
				foreach($users as $user){
				    $users[$i]['photo'] = $baseUrl."/uploads/user/".$user['photo'];
					$i++; 	
				}	
				$json = array("error" => false, "message" => $users);
			}else{
				
				$json = array("error" => false, "message" =>"No Contact Found!");
			}
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 30. Search User
/***********************************************************************/
$app->post('/searchUser', function ($request, $response, $args) {
    $input = $request->getParsedBody();
	$uri = $request->getUri();
    $baseUrl = $uri->getBaseUrl();	
	if(!empty($input['search']) && !empty($input['user_id'])){
		$user_id = $input['user_id'];
		$search = $input['search'];
		try{

			$stmt1 = $this->db->prepare("SELECT * FROM ec_user WHERE is_active =1 AND user_id ='".$user_id."'");
			$stmt1->execute(); 
			$result = count($stmt1->fetchAll());
			if($result > 0 ){			
				$sql = "SELECT user_id,user_name,mobile,email,updated_at,created_at,photo,status,is_active FROM ec_user WHERE (user_name LIKE '%".$search."%')  AND is_active =1";
				$stmt = $this->db->prepare($sql);
				$stmt->execute(); 
				$result = count($stmt->fetchAll());
				if($result >0 ){
					$stmt2 = $this->db->prepare("SELECT * FROM ec_user_contact WHERE user_id= '".$user_id."'");
					$stmt2->execute(); 
					$result = count($stmt2->fetchAll());
					if($result > 0 ){
						$stmt2->execute();
						while($row = $stmt2->fetch()){
							
							$added_contact_user_id[] = $row['contact'];  
							
						}
					}else{
						$added_contact_user_id = array(0);
					}					
					$stmt->execute();	
					$users = $stmt->fetchAll();	
					$i=0;$in_contact = 'false';
					foreach($users as $user){
						$users[$i]['photo'] = $baseUrl."/uploads/user/".$user['photo'];	
						if(in_array($user['user_id'],$added_contact_user_id)){
						
							$users[$i]['in_contact'] = true;
						
					    }else{
						
							$users[$i]['in_contact'] = false;	
					    }				    
					    $i++;
					}
					$json = array("error" => false, "message" => $users);
				}else{
					
					$json = array("error" => true, "message" =>"Not Found!");
				}
			}else{
				
				$json = array("error" => true, "message" =>"Invalid UserID!");
			}	
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}	
	}else{
		
		$json = array("error" => true, "message" =>"SearchWord and UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 31. Block User
/***********************************************************************/
$app->post('/blockUser', function ($request, $response, $args){	
	$input = $request->getParsedBody();
	if(!empty($input['user_id'])){
		$user_id = $input['user_id'];
		$stmt1 = $this->db->prepare("SELECT * FROM ec_user where user_id='".$user_id."'");
		$stmt1->execute(); 
		$rowCount = count($stmt1->fetchAll());
		if($rowCount>0){
			try{			 
				$stmt2 = $this->db->prepare("UPDATE `ec_user` SET `is_blocked` = 1  WHERE `user_id` = '".$user_id."'");
				$result = $stmt2->execute();
				if($result){
					 
					 $json = array("error" => false, "message" => "User blocked successfully!"); 
					 
				}else{
					 
					 $json = array("error" => true, "message" => "Database Error!");
			    }
			}catch(PDOException $e){
				
				 $json = array("error" => true, "message" => $e->getMessage());
			}			
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 32. Unblock User
/***********************************************************************/
$app->post('/unblockUser', function ($request, $response, $args){	
	$input = $request->getParsedBody();
	if(!empty($input['user_id'])){
		$user_id = $input['user_id'];
		$stmt1 = $this->db->prepare("SELECT * FROM ec_user where user_id='".$user_id."'");
		$stmt1->execute(); 
		$rowCount = count($stmt1->fetchAll());
		if($rowCount>0){
			try{			 
				$stmt2 = $this->db->prepare("UPDATE `ec_user` SET `is_blocked` = 0  WHERE `user_id` = '".$user_id."'");
				$result = $stmt2->execute();
				if($result){
					 
					 $json = array("error" => false, "message" => "User unblocked successfully!"); 
					 
				}else{
					 
					 $json = array("error" => true, "message" => "Database Error!");
			    }
			}catch(PDOException $e){
				
				 $json = array("error" => true, "message" => $e->getMessage());
			}			
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 33. Get Blocked Users
/***********************************************************************/
$app->post('/getBlockedUsers', function ($request, $response, $args) {
    $input = $request->getParsedBody();
	$uri = $request->getUri();
    $baseUrl = $uri->getBaseUrl();	
	if(!empty($input['user_id'])){
		$user_id = $input['user_id'];
		try{	
			$stmt1 = $this->db->prepare("SELECT * FROM ec_user_contact WHERE user_id= '".$user_id."'");
			$stmt1->execute(); 
			$result = count($stmt1->fetchAll()); 
			if($result >0 ){

			    $stmt1->execute();
                while($row = $stmt1->fetch()){
					
					$contact[] = $row['contact'];  
					
				}
				$user_ids = implode(',', array_map('intval', $contact));
				$stmt2 = $this->db->prepare("SELECT user_id,user_name,mobile,email,updated_at,created_at,photo,status,is_blocked,is_active FROM ec_user WHERE  is_blocked = 1 AND user_id IN ($user_ids)");
			    $stmt2->execute();
				$users = $stmt2->fetchAll();	
				$i =0;
				foreach($users as $user){
				    $users[$i]['photo'] = $baseUrl."/uploads/user/".$user['photo'];
					$i++; 	
				}	
				$json = array("error" => false, "message" => $users);
			}else{
				
				$json = array("error" => false, "message" =>"No Contact Found!");
			}
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 34. Send one to one location
/***********************************************************************/
$app->post('/oneToOneLocation', function ($request, $response, $args) {
    $uri = $request->getUri();
    $baseUrl = $uri->getBaseUrl();
	$input = $request->getParsedBody();
    if(!empty($input['user_id']) && !empty($input['sender_id']) && !empty($input['message'])){
		$user_id = $input['user_id'];
	    $sender_id = $input['sender_id'];
	    $message = $input['message'];
		$sql = "SELECT * FROM ec_user where `is_blocked` = 0 AND `is_active` = 1 AND user_id='".$user_id."'";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(); 
		$rowCount = count($stmt->fetchAll());
		if($rowCount>0){
			$update = "INSERT INTO ec_user_message(user_id,sender_id,message,type,created_at) VALUES(:user_id,:sender_id,:message,:type,CONVERT_TZ( NOW(),'+8:00','+5:30'))";
			$stmt_update = $this->db->prepare($update);
			$params_users = array(':user_id' =>$user_id, ':sender_id' =>$sender_id, ':message'=>$message,':type'=>5);
			$stmt_update->execute($params_users);
			$last_msg_id = $this->db->lastInsertId();
		    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			$rowCount = count($stmt->fetchAll());
			$sender_name = null;
			$sender_image = null;
			$mobile = null;
			if($rowCount>0){
			    $sql = "SELECT * FROM ec_user where user_id='".$sender_id."'";
				$stmt = $this->db->prepare($sql);
				$stmt->execute();
				while($row = $stmt->fetch()){
					
					$sender_name = $row['user_name'];
					$mobile = $row['mobile'];
					$sender_image = $row['photo'];
				 //$registrationIDs = array($row['registration_token']);
				 
				}
				$sender_image_url = $baseUrl."/uploads/user/".$sender_image;
			}
			$sql = "SELECT * FROM ec_user where user_id='".$user_id."'";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
			while($row = $stmt->fetch()){
				
			 $registrationID = $row['registration_token'];
			 //$registrationIDs = array($row['registration_token']);
			 
			}
			
			// API access key from Google API's Console
			
			 define( 'API_ACCESS_KEY', 'AIzaSyAscUsZbNdHpw69abg0ApcKhG9ZoJpcYMY' ); 
			   
			// prep the bundle
			
			$headers = array
			(
				'Content-Type: application/json',
				'Authorization:key='.API_ACCESS_KEY		
			);
			date_default_timezone_set('Asia/Kolkata');
			$date = date('Y-m-d H:i:s'); 
			$fields = array
			(
			    'message_id'    => $last_msg_id,
				'sender_id' 	=> $sender_id,
				'sender_name' 	=> $sender_name,
				'sender_image' 	=> $sender_image_url,
				'mobile' 		=> $mobile,
			    'receiver_id' 	=> $user_id,
				'chat_type' => 'one_to_one',				
				'type' => 'location',
				'message' 	=> $message,
				'date' 	=> $date

			);
			$messaage = array
			(
				'content_available' => true ,
  	     	    'priority' =>  'high',
				'to'	=> $registrationID,
				//'registration_ids' 	=> $registrationIDs,
				'data'			=> $fields

			); 

			$url = 'https://fcm.googleapis.com/fcm/send';
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, $url );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $messaage ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			$json = json_decode($result,true);
		    // if($json['success'] == 1){
				
				
			// }
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"UserID, SenderID and Message not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 35. Set Location
/***********************************************************************/
$app->post('/setLocation', function ($request, $response, $args){	
	$input = $request->getParsedBody();
	if(!empty($input['user_id']) && !empty($input['location'])){
		$user_id = $input['user_id'];
		$location = $input['location'];
		$stmt1 = $this->db->prepare("SELECT * FROM ec_user where `is_active` =1 AND user_id='".$user_id."'");
		$stmt1->execute(); 
		$rowCount = count($stmt1->fetchAll());
		if($rowCount>0){
			try{			 
				$stmt2 = $this->db->prepare("UPDATE `ec_user` SET `location` = '".$location."'  WHERE `user_id` = '".$user_id."'");
				$result = $stmt2->execute();
				if($result){
					 
					 $json = array("error" => false, "message" => "Location updated successfully!"); 
					 
				}else{
					 
					 $json = array("error" => true, "message" => "Database Error!");
			    }
			}catch(PDOException $e){
				
				 $json = array("error" => true, "message" => $e->getMessage());
			}			
		}else{
			
			$json = array("error" => true, "message" =>"Invalid UserID!");
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID and Location not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 36. Get Nearby Users
/***********************************************************************/
$app->post('/getNearbyUsers', function($request, $response,$args){
	$input = $request->getParsedBody();
	if(!empty($input['user_id'])){
	    $user_id = $input['user_id'];	
		try{
			$stmt1 = $this->db->prepare("SELECT * FROM `ec_user` WHERE `is_active` =1 AND `user_id` = '".$user_id."'");
			$stmt1->execute(); 
			$nRows = count($stmt1->fetchAll());
			if($nRows >0 ){
				$stmt1->execute();
                while($row = $stmt1->fetch()){
					$location = $row['location'];
					$arr_loc = explode(',',$location);
					$latFrom = $arr_loc[0];
					$longFrom = $arr_loc[0]; 
				}				
				function vincentyGreatCircleDistance($latitudeFrom,$longitudeFrom,$latitudeTo,$longitudeTo,$earthRadius=6371000){
					// convert from degrees to radians
					$latFrom = deg2rad($latitudeFrom);
					$lonFrom = deg2rad($longitudeFrom);
					$latTo = deg2rad($latitudeTo);
					$lonTo = deg2rad($longitudeTo);
					$lonDelta = $lonTo - $lonFrom;
					$a = pow(cos($latTo)*sin($lonDelta),2)+pow(cos($latFrom)*sin($latTo)-sin($latFrom)*cos($latTo)*cos($lonDelta),2);
					$b = sin($latFrom)*sin($latTo)+cos($latFrom)*cos($latTo)*cos($lonDelta);
					$angle = atan2(sqrt($a), $b);
					return $angle * $earthRadius;
				}
				$nearByUsers = array();
				$stmt2 = $this->db->prepare("SELECT user_id,user_name,mobile,email,updated_at,created_at,photo,status,location,is_blocked,is_active FROM `ec_user` WHERE `is_active` = 1 AND location IS NOT NULL");
				$stmt2->execute();
				$allUsers = $stmt2->fetchAll();
				foreach($allUsers as $user){
					$location = $user['location'];
					$arr_loc = explode(',',$location);
					$latTo = $arr_loc[0];
					$longTo = $arr_loc[0]; 
					if(vincentyGreatCircleDistance($latFrom,$longFrom,$latTo,$longTo) < 50){
						$nearByUsers[] = $user;
					}else{
						
					}				
				}							
				$json = array("error" => false, "message" => $nearByUsers);
			}else{
				
				$json = array("error" => false, "message" =>"Invalid UserID!");
			}
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 37. Delete Account
/***********************************************************************/
$app->post('/deleteAccount', function ($request, $response, $args){	
	$input = $request->getParsedBody();
	if(!empty($input['user_id'])){
		$user_id = $input['user_id'];
		try{
			$stmt1 = $this->db->prepare("SELECT * FROM `ec_user` WHERE  `user_id` = '".$user_id."'");
			$stmt1->execute(); 
			$nRows = count($stmt1->fetchAll());
			if($nRows >0 ){
				$stmt2 = $this->db->prepare("DELETE FROM `ec_user` WHERE  `user_id` = '".$user_id."'");
			    if($stmt2->execute()){
					
				    $json = array("error" => false, "message" => "Your account is deleted!");
					
				}else{
					 $json = array("error" => true, "message" => "Database Error!");
				} 		
				
			}else{
				
				$json = array("error" => true, "message" => "Invalid UserID!");
			}
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}	  
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 38. Get Qr Code
/***********************************************************************/
$app->post('/getQrCode', function ($request, $response, $args) {
    $input = $request->getParsedBody();
	$user_id = $input['user_id'];
	if(!empty($user_id)){
		$uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
		try{	
			$sql = "SELECT user_id,qr_image as qrcode,created_at FROM ec_user WHERE is_active =1 AND user_id ='".$user_id."' ";
			//$users = $this->db->query($sql);
			$stmt = $this->db->prepare($sql);
			$stmt->execute(); 
			$result = count($stmt->fetchAll());
			if($result >0 ){
				$sql = "SELECT user_id,qr_image as qrcode,created_at FROM ec_user WHERE is_active =1 AND user_id ='".$user_id."' ";
			    $stmt = $this->db->prepare($sql);
			    $stmt->execute();				
				while($row = $stmt->fetch()){					
				
				  $qr_code = $row['qrcode'];				
				 
				}
				$qr_code_url = $baseUrl."/uploads/qrcode/".$qr_code;
				$sql = "SELECT user_id,qr_image as qrcode,created_at FROM ec_user WHERE is_active =1 AND user_id ='".$user_id."' ";
			    $stmt = $this->db->prepare($sql);
			    $stmt->execute();
				$user = $stmt->fetchAll();
				$user[0]['qrcode'] = $qr_code_url;				
				$json = array("error" => false, "message" => $user);
			}else{
				
				$json = array("error" => false, "message" =>"Not Found!");
			}
		}catch(PDOException $e){
				
			$json = array("error" => true, "message" => $e->getMessage());
		}	
	}else{
		
		$json = array("error" => true, "message" =>"UserID not empty!");
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});
/***********************************************************************/
// 39. Send Invitation By QrCode
/***********************************************************************/
$app->post('/sendInvitationByQrcode', function($request, $response, $args) {	
	$input = $request->getParsedBody();
	$uri = $request->getUri();
    $baseUrl = $uri->getBaseUrl();
	if(!empty($input['sender_id']) && !empty($input['qr_code'])){
		$sender_id = $input['sender_id'];
	    $qr_code = $input['qr_code'];		
		$stmt1 = $this->db->prepare("SELECT * FROM ec_user where user_id = :sender_id"); // $stmt1
		$params1 = array(':sender_id' =>$sender_id);
		$stmt1->execute($params1);
        $rowCount = count($stmt1->fetchAll());
		if($rowCount>0){            		
			$stmt2 = $this->db->prepare("SELECT * FROM ec_user where qr_code = :qr_code"); // $stmt2
			$params2 = array(':qr_code' =>$qr_code);
			$stmt2->execute($params2);
			$rowCount = count($stmt2->fetchAll());
			if($rowCount>0){ 
                $stmt2->execute($params2);
				while($row = $stmt2->fetch()){				
					$receiver_id = $row['user_id'];				
				} 
				if($sender_id != $receiver_id){
					$sql = "SELECT * FROM ec_user_contact WHERE user_id= '".$sender_id."' AND contact= '".$receiver_id."'"; 
					$stmt = $this->db->prepare($sql);
					$stmt->execute(); 
					$rowCount = count($stmt->fetchAll());
					if($rowCount == 0){	
						$sender_name = null;
						$sender_image = null;
						$sender_mobile = null;		
						$stmt1->execute($params1);					
						while($row = $stmt1->fetch()){
							
							$sender_name = $row['user_name'];
							$sender_mobile = $row['mobile'];
							$sender_image = $row['photo'];

						}
						$sender_image_url = $baseUrl."/uploads/user/".$sender_image;		
						$stmt2->execute($params2);
						while($row = $stmt2->fetch()){
						 $receiver_name = $row['user_name'];
						 $receiver_mobile = $row['mobile']; 			 
						 $registrationID = $row['registration_token'];
						 //$registrationIDs = array($row['registration_token']);
						 
						}
						if(empty($sender_name)){$sender_name = $sender_mobile;}
						if(empty($receiver_name)){$receiver_name = $receiver_mobile;}
						$message  = $sender_name." would like to add you on EraChat<br>";
						$message .= "Hi ".$receiver_name.", I'd like to add you as a contact.";
						// API access key from Google API's Console
						
						 define( 'API_ACCESS_KEY', 'AIzaSyAscUsZbNdHpw69abg0ApcKhG9ZoJpcYMY' ); 
						   
						// prep the bundle
						
						$headers = array
						(
							'Content-Type: application/json',
							'Authorization:key='.API_ACCESS_KEY		
						);
						$date = date('Y-m-d H:i:s'); 
						$fields = array
						(
							//'message_id'    => $last_msg_id,
							'sender_id' 	=> $sender_id,
							'sender_name' 	=> $sender_name,
							'sender_image' 	=> $sender_image_url,
							'mobile' 		=> $sender_mobile,
							'receiver_id' 	=> $receiver_id,
                            'chat_type' 	=> 'invitation', 							
							'type' 			=> 'invitation',				
							'message' 	    => $message,
							'date' 	        => $date

						);
						$messaage = array
						(
							'content_available' => true ,
							'priority' =>  'high',
							'to'	=> $registrationID,
							//'registration_ids' 	=> $registrationIDs,
							'data'			=> $fields

						); 

						$url = 'https://fcm.googleapis.com/fcm/send';
						$ch = curl_init();
						curl_setopt( $ch,CURLOPT_URL, $url );
						curl_setopt( $ch,CURLOPT_POST, true );
						curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
						curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
						curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
						curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $messaage ) );
						$result = curl_exec($ch );
						curl_close( $ch );
						$json = json_decode($result,true);
						if($json['success'] == 1){
							try{						
								$stmt3 = $this->db->prepare("SELECT * FROM ec_user_invitation WHERE sender_id= :sender_id AND receiver_id=:receiver_id AND status=:status");  // $stmt3
								$params3 = array(':sender_id' =>$sender_id,':receiver_id' =>$receiver_id,':status' =>'pending');
								$stmt3->execute($params3);
								$rowCount = count($stmt3->fetchAll());
								if($rowCount == 0){
									$stmt4 = $this->db->prepare("INSERT INTO ec_user_invitation(sender_id,receiver_id,status) VALUES(:sender_id,:receiver_id,:status)"); //$stmt4
									$params4 = array(':sender_id' =>$sender_id,':receiver_id' =>$receiver_id, ':status' =>'pending');
									$stmt4->execute($params4);						
								}else{
										
								}				
							}catch(PDOException $pe){					
								$json = array("error" => true, "message" => $pe->getMessage());
								
							}				
						}
					}else{						
										
						$json = array("error" => true, "message" => "Contact already added!");
					}
				}else{						
										
					$json = array("error" => true, "message" => "Try Again!");
				}	
			}else{
				
				$json = array("error" => true, "message" =>"Invalid QrCode!");
			}	
		}else{
			
			$json = array("error" => true, "message" =>"Invalid SenderID!");
		}
		
	}else{
		
		$json = array("error" => true, "message" =>"SenderID and QrCode not empty!");
	    	
	}
	$response->withHeader('Content-type', 'application/json');
    return $response->withJson($json);
		
	//Render index view
    //return $this->renderer->render($response, 'index.phtml', $args);
});