<?php
use Xubin\GestureVerification\GVCode;

/**
 * 划动验证码
 */
class GestureController extends XAdminBase {
	
    /**
     * 资源图片显示
     */
	public function actionImg()
	{
	    $pic = $this->_getparam('pic', '');
	    
	    $gvcode = new GVCode('login');
	    
	    $gvcode->getSrcImg($pic, 'login');
	    
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证码
	 */
	public function actionLoginGvCode()
	{
	    $gvcode = new GVCode('login');
	    $gvcode->makeImg();
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证码验证
	 */
	public function actionLoginGvCheck()
	{
	    $tn  = new GVCode('login');
	    if($tn->check()){
	        echo "true";
	    }else{
	        echo "false";
	    }
	}
	
	/**
	 * 测试专用
	 */
	public function actionTest()
	{
	    $gvcode = new GVCode('');
	    $imgApiUrl = $this->createUrl('gesture/loginGvCode');
	    $checkApiUrl = $this->createUrl('gesture/loginGvCheck');
	    $srcImgApiUrl = $this->createUrl('gesture/img');
	    $html = $gvcode->getHtml($imgApiUrl, $checkApiUrl, $srcImgApiUrl);
	    
	    echo "<html><body>{$html}</body></html>";
	    exit;
	}

	/**
	 * 找回密码发送验证码
	 */
	public function actionRepwdGvCode() {
	    
	}

	/**
	 * 忘记密码
	 */
	public function actionRepwdGvCheck() {
	    
	}

	/**
	 * 注册发送验证码，找回密码发送验证码
	 */
	public function actionRegGvCheck() {
	    
	}

	/**
	 * 注册
	 */
	public function actionRegGvCode() {
	    
	}
	
}


