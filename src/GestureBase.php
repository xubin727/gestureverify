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
	protected function _Img($fromPage)
	{
	    $pic = $_GET['pic'];
	    
	    $GvCode = new GvCode();
	    
	    $GvCode->getSrcImg($pic, $fromPage);
	    
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证图
	 * @param string|array $name
	 */
	public function _gvCode($name)
	{
	    $GvCode = new GvCode();
	    $GvCode->makeImg($name);
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证
	 * @param string|array $name
	 */
	public function _gvCheck($name)
	{
	    $tn  = new GvCode();
	    if($tn->check($name)){
	        echo "true";
	    } else {
	        echo "false";
	    }
	    exit;
	}
	
}


