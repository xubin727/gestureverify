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
        $this->_Img(array('login')); // 多个接口通用，所以参数（来源页面）为多个，故以数组形式
    }
    
    /**
     * 登录界面用的划动验证图
     */
    public function actionLoginGvCode()
    {
        $this->_gvCode('login');
    }
    
    /**
     * 登录界面用的划动验证
     */
    public function actionLoginGvCheck()
    {
        $this->_gvCheck('login');
    }
    
    /**
     * 注册划动验证
     */
    public function actionRegGvCheck() {
        $this->_gvCheck('reg');
    }
    
    /**
     * 注册划动验证图
     */
    public function actionRegGvCode() {
        $this->_gvCode('reg');
    }
    
    /**
     * 注册发送验证码划动验证
     */
    public function actionRegSendGvCheck() {
        $this->_gvCheck('regsend');
    }
    
    /**
     * 注册发送验证码划动验证
     */
    public function actionRegSendGvCode() {
        $this->_gvCode('regsend');
    }
    
    /**
     * 找回密码划动验证图
     */
    public function actionRepwdGvCode() {
        $this->_gvCode('repwd');
    }
    
    /**
     * 忘记密码划动验证
     */
    public function actionRepwdGvCheck() {
        $this->_gvCheck('repwd');
    }
    
    /**
     * 找回密码发送验证码划动验证图
     */
    public function actionRepwdSendGvCode() {
        $this->_gvCode('repwdsend');
    }
    
    /**
     * 忘记密码发送验证码划动验证
     */
    public function actionRepwdSendGvCheck() {
        $this->_gvCheck('repwdsend');
    }
	
}


