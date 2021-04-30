<?php
namespace Xubin\GestureVerification;


class GVCode {
    
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
     *
     * @param string $name 这个名字必须使用当前浏览页地址的controller名称的小写。如果url中含有module，那么需要把module部分一起传递。如：mymodule/controller
     */
    public function __construct($name)
    {
        $this->name = str_replace('/', '_', $name);
        
        $this->checkDdos();
        
        if(!isset($_SESSION)){
            session_start();
        }
    }
    
    /**
     * 防DDOS攻击检查
     */
    public function checkDdos()
    {
        $name = '_' . $this->name . '_';
        if ($_SESSION['tn'.$name.'code_err_total'] > 10) {
            exit;
        }
    }
    
    /**
     * 检查请示是否从指定controller的页面来
     * @param string|array $contId
     */
    public function checkRefererFrom($contId)
    {
        if (!$contId) return ;
        
        if (is_string($contId)) {
            $contLst = array($contId);
        } else {
            $contLst = $contId;
        }
//         echo $contId;
        $allow = false;
        $refer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
        foreach ($contLst as $cont_id) { //var_dump($cont_id, $refer);exit;
            $cont_id = str_replace('/', '\/', $cont_id);
            if (preg_match('/^\/'.$cont_id.'/', $refer)) {
                $allow = true;
                break;
            }
        }
        
        if (!$allow) {
            exit;
        }
        
    }
    
    /**
     * 输出验证图片
     */
    public function makeImg()
    {
        $this->checkRefererFrom($name);
        
        $this->_init();
        $this->_createSlide();
        $this->_createBg();
        $this->_merge();
        $this->_imgout();
        $this->_destroy();
    }

    /**
     * 验证划动位置
     * @param number $offset
     * @return boolean
     */
    public function check($offset=0)
    {
        $this->checkRefererFrom($name);
        
        $name = '_' . $this->name . '_';
        $name2 = $this->name;
        
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
     * @return boolean
     */
    public function checkPassed()
    {
        $name = $this->name;
        return $_SESSION[$name . '_gv_code_checked'];
    }
    
    
    /**
     * 从会话中清除验证信息
     */
    public function removeGvCheck()
    {
        $name = $this->name;
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
    private function _getJs($imgApiUrl, $checkApiUrl)
    {
        $pathCss = __DIR__ . '/jscss/gv_code.js';
        
        $jsContnt = file_get_contents($pathCss);
        $jsContnt = str_replace( array('%imgApiUrl%', '%checkApiUrl%'), array($imgApiUrl, $checkApiUrl), $jsContnt );
        $jsContnt = preg_replace("/\/\*[\s\S]*\*\//U", '', $jsContnt); // 清除多行注释
        $jsContnt = preg_replace("/\/\/.*/", '', $jsContnt); // 清除单行行注释
        $jsContnt = preg_replace("/[\s\v]+/", ' ', $jsContnt); // 清除多余的空白字符
        
        return "<script type=\"text/javascript\">\n{$jsContnt}\n</script>";
    }
    
    /**
     * 获取所需的HTML代码
     * @param string $imgApiUrl
     * @param string $checkApiUrl
     * @param string $srcImgApiUrl
     * @return string
     */
    public function getHtml($imgApiUrl, $checkApiUrl, $srcImgApiUrl)
    {
        $js = $this->_getJs($imgApiUrl, $checkApiUrl);
        $css = $this->_getCss($srcImgApiUrl);
        
        return <<<s
{$js}{$css}
<!--form class="gv_code" onsubmit="return false;"><input type="submit" value="OK"></form-->
<script type="text/javascript">
GVCode.onsuccess(function(){
console.log('验证通过代码');
});
</script>
s;

    }
    
    /**
     * 获取img目录里的资源图片
     * @param string $pic
     * @param string $name
     */
    public function getSrcImg($pic, $name)
    {
        $this->checkRefererFrom($name);
        
        $pic = dirname(__FILE__) . '/img/' . $pic; //var_dump($pic);exit;
        
        header('Content-Type: image/' . pathinfo($pic, PATHINFO_EXTENSION ));
        
        if (file_exists($pic)) {
            readfile($pic);
        }
        exit;
    }
    
    

}

