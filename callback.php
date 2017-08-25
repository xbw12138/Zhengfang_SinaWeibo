<?php
session_start();

include_once( 'config.php' );
include_once( 'saetv2.ex.class.php' );
include_once( 'mysql.php');
$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	}
}

	function userInsert(){
		//绑定用户
		global $user_message;
		$screen_name = $user_message['screen_name'];  
		$uid = $user_message['id'];
		$sql="INSERT INTO user(uid,screen_name)VALUES('$uid','$screen_name')";
		$result=mysql_query($sql);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	function userExist(){
		//查看用户是否存在
		global $user_message;
		$uid = $user_message['id'];  
		$sql="select 1 from user where uid =$uid limit 1";
		$result = mysql_query($sql);
		$data = mysql_num_rows($result);
		if($data){
			return true;
		}else{
			return false;
		}
	}
?>
<!DOCTYPE html>
<html>
<head> </head>
<body>
<div style="text-align:center;clear:both;">
<?php
if (isset($token)) {
	$_SESSION['token'] = $token;
	setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );

    $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
	$uid_get = $c->get_uid();
	$uid = $uid_get['uid'];
	$user_message = $c->show_user_by_id( $uid);//根据ID获取用户等基本信息
	if(!userExist()){
		userInsert();
	}
	header("refresh:2;url=http://nanopi.ecfun.cc");
	echo "授权成功，正在进入山东科技大学正方教务系统";
} else {
	echo "授权失败";
}
?>
</div>
</body>
</html>
