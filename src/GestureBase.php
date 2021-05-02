<?php
namespace Xubin\GestureVerification;


use Xubin\GestureVerification\GVCode;

/**
 * 划动验证码
 */
trait GestureBase {
	
    /**
     * 资源图片显示
     */
	public function actionImg()
	{
	    $pic = $_GET['pic'];
	    
	    $gvcode = new GVCode('login');
	    
	    $gvcode->getSrcImg($pic, 'login');
	    
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证图
	 */
	public function actionLoginGvCode()
	{
	    $gvcode = new GVCode('login');
	    $gvcode->makeImg();
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证
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
	 * 找回密码划动验证图
	 */
	public function actionRepwdGvCode() {
	    $this->forward('loginGvCode');
	}

	/**
	 * 忘记密码划动验证
	 */
	public function actionRepwdGvCheck() {
	    $this->forward('loginGvCheck');
	}
	
	/**
	 * 找回密码发送验证码划动验证图
	 */
	public function actionRepwdSendGvCode() {
	    $this->forward('loginGvCode');
	}
	
	/**
	 * 忘记密码发送验证码划动验证
	 */
	public function actionRepwdSendGvCheck() {
	    $this->forward('loginGvCheck');
	}

	/**
	 * 注册划动验证
	 */
	public function actionRegGvCheck() {
	    $this->forward('loginGvCheck');
	}

	/**
	 * 注册划动验证图
	 */
	public function actionRegGvCode() {
	    $this->forward('loginGvCode');
	}
	
	/**
	 * 注册发送验证码划动验证
	 */
	public function actionRegSendGvCheck() {
	    $this->forward('loginGvCheck');
	}
	
	/**
	 * 注册发送验证码划动验证
	 */
	public function actionRegSendGvCode() {
	    $this->forward('loginGvCode');
	}
	
}


