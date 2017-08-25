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
<html xmlns:wb="http://open.weibo.com/wb">
<head> 
<script src="http://tjs.sjs.sinajs.cn/open/api/js/wb.js" type="text/javascript" charset="utf-8"></script>
</head>
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
	$ms  = $c->user_timeline_by_id(); // done
	if( is_array( $ms['statuses'] ) ){
   		if(substr($ms['statuses'][0]['text'], 0, 30)=="正方教务管理系统外网"){
			header("refresh:2;url=http://nanopi.ecfun.cc");
			echo "<h2>授权成功，正在进入山东科技大学正方教务系统";
		}else{
			echo "<h2>您还没有分享，去分享<br>";
			echo '<center><wb:share-button appkey="1857277708" addition="number" type="button" ralateUid="5368201773" default_text="正方教务管理系统外网 http://sdust.ecfun.cc" pic="http%3A%2F%2Fwx2.sinaimg.cn%2Fmw690%2F653abd37ly1fisbdc88fzj21m00winbw.jpg"></wb:share-button></center><br>';
			echo "<h2>分享成功后重新退回首页，自动授权<br>";
		}
	}else{
		echo "<h2>未获取到微博内容／未关注，去关注";
	}
	
} else {
	echo "<h2>授权失败，退回首页重新授权";
}
?>
</div>
</body>
</html>
