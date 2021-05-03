<?php
namespace Xubin\GestureVerification;


class GvCode {
    
    protected $im = null;
    protected $im_fullbg = null;
    protected $im_bg = null;
    protected $im_slide = null;
    protected $bg_width = 240;
    protected $bg_height = 150;
    protected $mark_width = 50;
    protected $mark_height = 50;
    protected $bg_num = 6;
    protected $_x = 0;
    protected $_y = 0;
    protected $_fault = 3; //容错象素 越大体验越好，越小破解难道越高
    protected $name = '';
    
    /**
     * @param string $name 这个名字必须使用当前浏览页地址的controller名称的小写。如果url中含有module，那么需要把module部分一起传递。如：mymodule/controller
     */
    public function __construct($name='')
    {
        $this->name = str_replace('/', '_', $name);
        
        if(!isset($_SESSION)){
            session_start();
        }// print_r($_SESSION);
    }
    
    /**
     * 防DDOS攻击检查
     * @param string $name 当前的验证码名称。如：loign
     */
    protected function checkDdos($name)
    {
        $name = '_' . $name . '_';
        if ($_SESSION['tn'.$name.'code_err_total'] > 10) {
            exit;
        }
    }
    
    /**
     * 检查请示是否从指定controller的页面来
     * @param string|array $fromPage 请求来源controller的id，即referer
     */
    protected function checkRefererFrom($fromPage)
    { //echo __LINE__ . $contId;
        if (!$fromPage) return false;
        
        if (is_string($fromPage)) {
            $contLst = array($fromPage);
        } else {
            $contLst = $fromPage;
        }
        //print_r( $contLst);
        $allow = false;
        $refer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
        foreach ($contLst as $cont_id) { //var_dump($cont_id, $refer);//exit;
            $cont_id = str_replace('/', '\/', $cont_id);
            $match = preg_match('/^\/'.$cont_id.'/', $refer, $m); //var_dump( __LINE__, $m);
            if ($match) {
                $allow = true;
                break;
            }
        }
        
        if (!$allow) { // echo __LINE__;
            exit;
        }
        
        return true;
        
    }
    
    /**
     * 输出验证图片
     * @param string $name
     * @param string $fromPage 请求来源controller的id，即referer
     */
    public function makeImg($name, $fromPage)
    {
        $this->checkRefererFrom($fromPage);
        $this->name = $name;
        //echo __LINE__;
        $this->_init();
        $this->_createSlide();
        $this->_createBg();
        $this->_merge();
        $this->_imgout();
        $this->_destroy();
    }

    /**
     * 验证划动位置
     * @param string $name  当前的验证码名称。如：loign
     * @param string|array $fromPage 来源接口。多个接口通用，所以参数（来源页面）为多个，故以数组形式
     * @return boolean
     */
    public function check($name, $fromPage)
    { //var_dump($_SESSION,$name);exit;
        $this->checkRefererFrom($fromPage);
        $this->checkDdos($name);
        
        $name2 = $name;
        $name = '_' . $name . '_';
        
        if(!$_SESSION['tn'.$name.'code_r']){
            return false;
        }
        if(!$offset){
            $offset = $_REQUEST['tn_r'];
        }
        $ret = abs($_SESSION['tn'.$name.'code_r']-$offset)<=$this->_fault;
        if($ret){
            unset($_SESSION['tn'.$name.'code_r']);
            $_SESSION[$name2 . '_gv_code_checked'] = true;
        }else{
            $_SESSION['tn'.$name.'code_err_total']++; //防攻击用，总的重试次数进行限制
            $_SESSION['tn'.$name.'code_err']++;
            if($_SESSION['tn'.$name.'code_err']>3){//错误3次必须刷新
                unset($_SESSION['tn'.$name.'code_r']);
            }
            $_SESSION[$name2 . '_gv_code_checked'] = false;
        }
        return $ret;
    }
    
    
    /**
     * 检验是否验证通过
     * @param string $name  当前的验证码名称。如：loign
     * @return boolean
     */
    public function checkPassed($name)
    {
//         $name = $this->name;
        return $_SESSION[$name . '_gv_code_checked'];
    }
    
    
    /**
     * 从会话中清除验证信息
     * @param string $name  当前的验证码名称。如：loign
     */
    public function removeGvCheck($name)
    {
//         $name = $this->name;
        unset($_SESSION[$name . '_gv_code_checked']);
    }

    
    private function _init()
    {
        $bg = mt_rand(1,$this->bg_num);
        $file_bg = dirname(__FILE__).'/bg/'.$bg.'.png';
        $this->im_fullbg = imagecreatefrompng($file_bg);
        $this->im_bg = imagecreatetruecolor($this->bg_width, $this->bg_height);
        imagecopy($this->im_bg,$this->im_fullbg,0,0,0,0,$this->bg_width, $this->bg_height);
        $this->im_slide = imagecreatetruecolor($this->mark_width, $this->bg_height);
        $name = '_'.str_replace('/', '_', $this->name).'_';
        $_SESSION['tn'.$name.'code_r'] = $this->_x = mt_rand(50,$this->bg_width-$this->mark_width-1);
        $_SESSION['tn'.$name.'code_err'] = 0;
        if (!isset($_SESSION['tn'.$name.'code_err_total'])) $_SESSION['tn'.$name.'code_err_total'] = 0;
        $this->_y = mt_rand(0,$this->bg_height-$this->mark_height-1);
    }

    private function _destroy()
    {
        imagedestroy($this->im);
        imagedestroy($this->im_fullbg);
        imagedestroy($this->im_bg);
        imagedestroy($this->im_slide);
    }
    private function _imgout()
    {
        if(!$_GET['nowebp']&&function_exists('imagewebp')){//优先webp格式，超高压缩率
            $type = 'webp';
            $quality = 40;//图片质量 0-100
        }else{
            $type = 'png';
            $quality = 7;//图片质量 0-9
        }
        header('Content-Type: image/'.$type);
        $func = "image".$type;
        $func($this->im,null,$quality);
    }
    private function _merge()
    {
        $this->im = imagecreatetruecolor($this->bg_width, $this->bg_height*3);
        imagecopy($this->im, $this->im_bg,0, 0 , 0, 0, $this->bg_width, $this->bg_height);
        imagecopy($this->im, $this->im_slide,0, $this->bg_height , 0, 0, $this->mark_width, $this->bg_height);
        imagecopy($this->im, $this->im_fullbg,0, $this->bg_height*2 , 0, 0, $this->bg_width, $this->bg_height);
        imagecolortransparent($this->im,0);//16777215
    }

    private function _createBg()
    {
        $file_mark = dirname(__FILE__).'/img/mark.png';
        $im = imagecreatefrompng($file_mark);
        header('Content-Type: image/png');
        //imagealphablending( $im, true);
        imagecolortransparent($im,0);//16777215
        //imagepng($im);exit;
        imagecopy($this->im_bg, $im, $this->_x, $this->_y  , 0  , 0 , $this->mark_width, $this->mark_height);
        imagedestroy($im);
    }

    private function _createSlide()
    {
        $file_mark = dirname(__FILE__).'/img/mark2.png';
        $img_mark = imagecreatefrompng($file_mark);
        imagecopy($this->im_slide, $this->im_fullbg,0, $this->_y , $this->_x, $this->_y, $this->mark_width, $this->mark_height);
        imagecopy($this->im_slide, $img_mark,0, $this->_y , 0, 0, $this->mark_width, $this->mark_height);
        imagecolortransparent($this->im_slide,0);//16777215
        //header('Content-Type: image/png');
        //imagepng($this->im_slide);exit;
        imagedestroy($img_mark);
    }
    
    /**
     * 获取所需的css代码
     * @return string
     */
    private function _getCss($srcImgApiUrl)
    {
        $pathCss = __DIR__ . '/jscss/style.css';
        
        $cssContnt = file_get_contents($pathCss);
        $cssContnt = preg_replace("/\/\*[\s\S]*\*\//U", '', $cssContnt); // 清除多行注释
        $cssContnt = preg_replace("/[\s\v]+/", ' ', $cssContnt); // 清除多余的空白字符
        $cssContnt = preg_replace("/url\('img\/(.+?)'\)/U", "url('{$srcImgApiUrl}?pic=$1')", $cssContnt); // 修改资源图片地址
        $cssContnt = str_replace("}", "}\n", $cssContnt);
        
        return "<style>\n{$cssContnt}</style>";
    }
    
    /**
     * 获取所需的js代码
     * @return string
     */
    private function _getJs()
    {
        $pathCss = __DIR__ . '/jscss/gv_code.js';
        
        $jsContnt = file_get_contents($pathCss);
        $jsContnt = preg_replace("/\/\*[\s\S]*\*\//U", '', $jsContnt); // 清除多行注释
        $jsContnt = preg_replace("/\/\/.*/", '', $jsContnt); // 清除单行行注释
//         $jsContnt = preg_replace("/[\s\v]+/", ' ', $jsContnt); // 清除多余的空白字符
        
        return "<script type=\"text/javascript\">\n{$jsContnt}\n</script>";
    }
    
    /**
     * 获取所需的HTML代码
     * @param string $srcImgApiUrl
     * @return string
     */
    public function getHtml($srcImgApiUrl)
    {
        $js = $this->_getJs();
        $css = $this->_getCss($srcImgApiUrl);
        
        return $js . $css;
    }
    
    /**
     * 获取img目录里的资源图片
     * @param string $pic
     * @param string|array $fromName  请求来源controller的id，即referer
     */
    public function getSrcImg($pic, $fromName)
    {
        $this->checkRefererFrom($fromName);
        
        $pic = dirname(__FILE__) . '/img/' . $pic; //var_dump($pic);exit;
        
        header('Content-Type: image/' . pathinfo($pic, PATHINFO_EXTENSION ));
        
        if (file_exists($pic)) {
            readfile($pic);
        }
        exit;
    }
    
    

}

