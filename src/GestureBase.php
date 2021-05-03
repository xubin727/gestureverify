<?php
namespace Xubin\GestureVerification;


/**
 * 划动验证码
 */
trait GestureBase {
	
    /**
     * 资源图片显示
     * @param string|array $fromPage
     */
	protected function _srcImg($fromPage)
	{
	    $pic = $_GET['pic'];
	    
	    $GvCode = new GvCode();
	    
	    $GvCode->getSrcImg($pic, $fromPage);
	    
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证图
	 * @param string|array $fromPage
	 * @param string|array $name
	 */
	public function _gvCode($name, $fromPage)
	{
	    $GvCode = new GvCode();
	    $GvCode->makeImg($name, $fromPage);
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证
	 * @param string $fromPage
	 * @param string|array $name
	 */
	public function _gvCheck($name, $fromPage)
	{
	    $tn  = new GvCode();
	    if($tn->check($name, $fromPage)){
	        echo "true";
	    } else {
	        echo "false";
	    }
	    exit;
	}
	
}


