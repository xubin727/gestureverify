<?php
namespace Xubin\GestureVerification;


use Xubin\GestureVerification\GvCode;

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
	    
	    $GvCode = new GvCode('login');
	    
	    $GvCode->getSrcImg($pic, 'login');
	    
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证图
	 */
	public function actionLoginGvCode()
	{
	    $GvCode = new GvCode('login');
	    $GvCode->makeImg();
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证
	 */
	public function actionLoginGvCheck()
	{
	    $tn  = new GvCode('login');
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
	    $GvCode = new GvCode('');
	    $imgApiUrl = $this->createUrl('gesture/loginGvCode');
	    $checkApiUrl = $this->createUrl('gesture/loginGvCheck');
	    $srcImgApiUrl = $this->createUrl('gesture/img');
	    $html = $GvCode->getHtml($imgApiUrl, $checkApiUrl, $srcImgApiUrl);
	    
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


