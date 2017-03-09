<?php 
/**
* 
*/
class Account
{
	public function register(){
		if (1) {
			return Json::make('1','login successfull',$data)->response();
		}
		return Json::make('0','Invalid credential')->withError(104)->response();
	}


	public function login(){
		$details=Input::post(array('email','password'));
		$rules=array(
			'email'=>'required',
			'password'=>'required',
		);
		if (!Validator::validate($details,$rules)) {
			return Json::make('0',Validator::error())->withError(103)->response();
		}
		if ($data=Auth::login($details,$type)) {
			return Json::make('1','login successfull',$data)->response();
		}
		return Json::make('0','Invalid credential')->withError(104)->response();
	}
	public function forgetPassword(){
		$details=Input::post(array('email'));
		$rules=array(
			'email'=>'required',
		);
		if (!Validator::validate($details,$rules)) {
			return Json::make('0',Validator::error())->withError(103)->response();
		}
		$user=Auth::getType($details['email']);
		$data=Auth::getmobileByemail($details);
		$mobile=$data->mobile;
		$name=$data->name;
		$details['pass_code']=rand(10000,99999);
		if(Auth::forgetPassword($details)){
			if($mobile){
				Sms::forgetPassword($details['pass_code'],$mobile,Helper::getCountry($data->country_id)->code);
			}
			Mail::send(array(
				'to'=>$details['email'],
				'subject'=>'Forget Password Code',
				'body'=>'<!DOCTYPE html PUBLIC >
							<html >
							<head>
									<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
									<title>Yahavi</title>
									<style type="text/css">
									</style>
							</head>
							<body style="font-family:\'Roboto\',sans-serif">
									<table class="main" border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td>
												<table align="center" border="0" cellpadding="0" cellspacing="0" width="600"  background="https://s3.ap-south-1.amazonaws.com/yahavi-cdn-1/emailerbg.jpg" style="padding-bottom:100px; ">
													<tr>
														<td>
															<a href="'.WEB_URL.'" style="display:inline-block; margin-left:44px;margin-top:20px;margin-bottom:10px; height:70px ;width:70px;"><img alt="img" style="height:65px ;width:70px;" src="https://yahavi.com/assets/v1/img/mailer-logo.png"/></a>
														</td>
													</tr>
													<tr>
														<td align="center"  bgcolor="#01A2A5" style="margin:0 auto;width:490px;display:block;">
															<table align="center" width="490" style=" color:#fff; height:80px; padding-top:5px;font-size:18px; font-weight:bold;padding-top:5px; " cellspacing="0" cellpadding="0">
																<tr>
																	<td align="center" colspan="1">CREATE
																		<span><span style="width:8px; height:8px; background:#fff; border-radius:50%; margin-bottom:2px; display:inline-block;"></span> SHOWCASE</span>
																		<span><span style="width:8px; height:8px; background:#fff; border-radius:50%; margin-bottom:2px; display:inline-block;"></span> CONNECT</span>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td style="width:490px; min-height:320px; margin:0 auto; display:block; background:#fff;">
															<div style="padding-top:25px; padding-left:10px; padding-right:10px; padding-bottom:10px; font-size:12px; color:#434343;">
																<p style="padding-bottom:12px;">Hi '.$name.',</p>
																<p style="padding-bottom:20px;">You have recently requested to reset your Yahavi Password. Please enter following password reset code in the Reset code window.<br>Code is:'.$details['pass_code'].'</p>
																		<!-- <img alt="img" style="width:100%" src="https://s3.ap-south-1.amazonaws.com/yahavi-cdn-1/images/approve.gif" alt="approve gif"> -->
																		<p>Cheers,<br>Yahavi</p>
															</div>
														</td>
													</tr>
													<tr>
														<td align="center" bgcolor="#FF5A5" valign="top" style="display:block;margin:0 auto;  width:490px; height:90px; ">
																	<table cellpadding="5" cellspacing="0" width="490">
																		<tr>
																			<td>
																				<table   cellpadding="0"  cellspacing="0" width="100%"  style="margin-top:20px;">
																					<tr>
																						<td align="center" width="260" valign="bottom">
																							<a href="https://www.facebook.com/yahaviofficial" class="footer_inst"><img alt="img" src="https://s33.postimg.org/zcxmd31rj/Facebook_Round.png" width="21" height="21"></a> 

																							<a href="https://instagram.com/yahaviofficial" class="footer_inst"><img alt="img" src="https://s33.postimg.org/ia8hmm2pb/Instagram_round.png" width="21" height="21"></a>

																							<a href="https://www.twitter.com/yahaviofficial" class="footer_inst"><img alt="img" src="https://s33.postimg.org/x080zd9nj/Twitter_round.png" width="21" height="21"></a>

																							<a href="https://www.pinterest.com/yahaviofficial"><img alt="img" src="https://s33.postimg.org/rgdgo6i73/Pinterest_round.png" width="21" height="21"></a>

																							<a href="https://www.youtube.com/channel/UCaOBRRidjoJ8e5pKMQQFxwQ/feed" class="footer_inst"><img alt="img" src="https://s33.postimg.org/6mgkk1c1r/Youtube_round.png" width="21" height="21"></a>

																							<br/>
																							<a href="'.WEB_URL.'" style="color:white;text-decoration:none;font-size:12px;margin-left:15px; ">www.yahavi.com</a>
																						</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	</table> 
														</td>
													</tr>
												</table>
											</td>
										</tr>	
										<center>
														<table border="0" cellpadding="0" cellspacing="0" width="100%" id="canspamBarWrapper" style="background-color:#FFFFFF;">
															<tr>
																<td align="center" valign="top" style="padding-top:20px; padding-bottom:20px;">
																	<table border="0" cellpadding="0" cellspacing="0" id="canspamBar">
																		<tr>
																			<td align="center" valign="top" style="color:#606060; font-family:Helvetica, Arial, sans-serif; font-size:8px; line-height:150%; padding-right:20px; padding-bottom:5px; padding-left:20px; text-align:center;">
																				This email was sent to <a href="mailto:'.$details['email'].'" target="_blank" style="color:#404040 !important;">'.$details['email'].'</a>
																				<br/>
																				You have recieved this email because you have signed up at <a href="'.WEB_URL.'" target="_blank" style="color:#404040 !important;">Yahavi</a>
																				
																				<br>
																				Collective Artists Pvt. Ltd. · 212,Suryakiran Building,19,K.G. Marg · Delhi 110001 · India 


																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
														<style type="text/css">
															@media only screen and (max-width: 480px){
																table[id="canspamBar"] td{font-size:14px !important;}
																table[id="canspamBar"] td a{display:block !important; margin-top:10px !important;}
															}
														</style>
										</center>
							</body>
							</html>'
			));
			return Json::make('1','Link emailed')->response();
		}
		return Json::make('0','Email not Found')->withError(103)->response();
	}
	public function resetPassword(){
		$details=Input::post(array('email','password','pass_code'));
		$rules=array(
			'email'=>'required',
			'pass_code'=>'required',
			'password'=>'required'
		);
		if (!Validator::validate($details,$rules)) {
			return Json::make('0',Validator::error())->withError(103)->response();
		}
		$details['password']=password_hash($details['password'],PASSWORD_BCRYPT);
		if (Auth::resetPassword($details)) {
			return Json::make('1','Password reset')->response();
		}
		return Json::make('0','Invalid Code')->withError(108)->response();
	}
	public function changePassword(){
		$user=Auth::user();
		$details=Input::post(array('password','confirm_password'));
		$rules=array(
			'password'=>'required'
		);
		if (!Validator::validate($details,$rules)) {
			return Json::make('0',Validator::error())->withError(103)->response();
		}
		if($details['password']!=$details['confirm_password']){
			return Json::make('0','password and confirm_password  should be same')->response();
		}
		unset($details['confirm_password']);
		$details['email']=$user->email;
		$details['password']=password_hash($details['password'],PASSWORD_BCRYPT);
		if (Auth::changepassword($details)) {
			return Json::make('1','Password changed')->response();
		}
		return Json::make('0','Invalid Code')->withError(108)->response();
	}
}
?>