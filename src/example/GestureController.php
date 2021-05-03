<?php
use Xubin\GestureVerification\GestureBase;

/**
 * 划动验证码
 */
class GestureController extends XAdminBase {
    
    use GestureBase;
    
    /**
     * 输出资源图片
     */
    public function actionImg()
    {
        $this->_srcImg(array('login'));
    }
    
    /**
     * 登录界面用的划动验证图
     */
    public function actionLoginGvCode()
    {
        $this->_gvCode('login', 'login');
    }
    
    /**
     * 登录界面用的划动验证
     */
    public function actionLoginGvCheck()
    {
        $this->_gvCheck('login', 'login');
    }
    
    /**
     * 注册划动验证
     */
    public function actionRegGvCheck() {
        $this->_gvCheck('reg', 'login');
    }
    
    /**
     * 注册划动验证图
     */
    public function actionRegGvCode() {
        $this->_gvCode('reg', 'login');
    }
    
    /**
     * 注册发送验证码划动验证
     */
    public function actionRegSendGvCheck() {
        $this->_gvCheck('regsend', 'login');
    }
    
    /**
     * 注册发送验证码划动验证
     */
    public function actionRegSendGvCode() {
        $this->_gvCode('regsend', 'login');
    }
    
    /**
     * 找回密码划动验证图
     */
    public function actionRepwdGvCode() {
        $this->_gvCode('repwd', 'login');
    }
    
    /**
     * 忘记密码划动验证
     */
    public function actionRepwdGvCheck() {
        $this->_gvCheck('repwd', 'login');
    }
    
    /**
     * 找回密码发送验证码划动验证图
     */
    public function actionRepwdSendGvCode() {
        $this->_gvCode('repwdsend', 'login');
    }
    
    /**
     * 忘记密码发送验证码划动验证
     */
    public function actionRepwdSendGvCheck() {
        $this->_gvCheck('repwdsend', 'login');
    }
	
}


