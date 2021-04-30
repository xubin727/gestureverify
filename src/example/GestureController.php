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
	    
	    $gvcode = new GVCode('gesture');
	    
	    $gvcode->getSrcImg($pic);
	    
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证码
	 */
	public function actionLoginGvCode()
	{
	    $gvcode = new GVCode('gesture');
	    $gvcode->makeImg();
	    exit;
	}
	
	/**
	 * 登录界面用的划动验证码验证
	 */
	public function actionLoginGvCheck()
	{
	    $tn  = new GVCode('gesture');
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
		$mobile = ( int ) $this->_getparam ( 'mobile' );
		if (strlen ( $mobile ) != 11)
			exit ( json_encode ( array (
					'code' => 4000,
					'msg' => '手机号码不正确。'
			) ) );

		$rs = $this->_getJson ( UCAPI_SERVER . '/register/sendcode.json?mobile=' . $mobile );
		exit ( json_encode ( $rs ) );
	}

	/**
	 * 忘记密码
	 */
	public function actionRepwdGvCheck() {

		/* 找回密码 */
		// $request = 'register/sendcode.json?mobile=18833201916';
		// $request = 'register/verifysms.json?mobile=18833201916&code=200819';
		// $request = 'register/findpassword.json?mobile=18833201916&password=000000&verify_code=5679288829cd9';
		$msg = '';
		$code = '';
		if ($_POST) {
		    unset($_SESSION['sendcodemark']);
		    unset($_SESSION['sendcodenum']);
			if (11 != strlen ( ( int ) $this->_getparam ( 'mobile' ) ) || ! $this->_getparam ( 'code' ) || ! $this->_getparam ( 'password' ) || $this->_getparam ( 'password' ) != $this->_getparam ( 'repassword' )) {
				$msg = '请完整并正确填写各项信息。';
				$code = 2010;
			} else {
				$rs = $this->_getJson ( UCAPI_SERVER . '/register/verifysms.json', array (
						'mobile' => $this->_getparam ( 'mobile' ),
						'code' => $this->_getparam ( 'code' )
				) );

				if (2000 != $rs ['code']) {
					$msg = '密码找回失败，请联系管理员。';
					$this->renderPartial ( 'repwd', array_merge ( $_POST, $rs ) );
					exit ();
				}

				$rs = $this->_getJson ( UCAPI_SERVER . '/register/findpassword.json', array (
						'mobile' => $this->_getparam ( 'mobile' ),
						'password' => $this->_getparam ( 'password' ),
						'verify_code' => $rs ['data'] ['verify_code']
				) );
				extract ( ( array ) $rs );
				$msg = '2000' == $rs ['code'] ? '密码找回成功。' : '密码找回失败，请联系管理员。';
			}
		}

		$this->renderPartial ( 'repwd', array_merge ( $_POST, array (
				'code' => $code,
				'msg' => $msg
		) ) );
	}

	/**
	 * 注册发送验证码，找回密码发送验证码
	 */
	public function actionRegGvCheck() {
		$mobile = ( int ) $this->_getparam ( 'mobile' );
		if (strlen ( $mobile ) != 11) {
            exit ( json_encode ( array (
                'code' => 2010,
                'msg' => '手机号码不正确。'
            ) ) );
        }
        //         print_r($_SESSION);
//         if (empty($_SESSION['sendcodemark'])) {
            $mark = rand(100000, 999999);
            $_SESSION['sendcodemark'] = $mark;
            $_SESSION['sendcodenum'] = 1;
            
            setcookie('sendcodemark', $mark, time()+3600*8);
//         } else {
//             if (@$_COOKIE['sendcodemark'] != $_SESSION['sendcodemark'])  {// 检查唯一标识是否一致
//                 exit ( json_encode ( array (
//                     'code' => 2020,
//                     'msg' => '非法请求。'
//                 ) ) );
//             }
//         }
            
        if ($_SESSION['sendcodenum'] > 6) { // 限制最多发6次
            exit ( json_encode ( array (
                'code' => 2030,
                'msg' => '勿频繁发送验证码。'
            ) ) );

        } else { // 发送验证吗短信
            $_SESSION['sendcodenum']++;
            // @todo 发送手机验证码
//             $rs = $this->_getJson ( UCAPI_SERVER . '/register/sendmobile.json?mobile=' . $mobile );
            $accessKeyId = 'LTAI4GD6a4J17FVPyiWni2su';
            $accessSecret = 'wFzuPutL6DjyVTCLBmAAx3HbxgtYKd';
            AlibabaCloud::accessKeyClient($accessKeyId, $accessSecret)->regionId('cn-hangzhou')->asDefaultClient();
            $code  = $mark;
            try {
                $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $mobile,
                        'SignName' => "说明书在线验证码",
                        'TemplateCode' => "SMS_203716891",
                        'TemplateParam' => "{\"code\":\"{$code}\"}",
                    ],
                ])
                ->request();
//                 print_r($result->toArray());
                
                $rs = array (
                    'code' => 2000,
                    'msg' => '验证码已发送，将接收到的验证填入输入框中。' //.json_encode($result->toArray())."{\"code\":\"{$code}\"}"
                );
            } catch (ClientException $e) {
//                 echo $e->getErrorMessage() . PHP_EOL;
                $rs = array (
                    'code' => 2040,
                    'msg' => $e->getErrorMessage() . PHP_EOL
                );
            } catch (ServerException $e) {
//                 echo $e->getErrorMessage() . PHP_EOL;
                $rs = array (
                    'code' => 2050,
                    'msg' => $e->getErrorMessage() . PHP_EOL
                );
            }
        }

		
		exit ( json_encode ( $rs ) );
	}

	/**
	 * 注册
	 */
	public function actionRegGvCode() {
		/* 注册 */
		// $request = 'register/verifyregsms.json?mobile=18833201916&code=200819';
		// $request = 'register/andex.json?code=765638&mobile=18610541668&password=000000';
		$msg = '';
		$code = '';
		if ($_POST) {
		    // 验证码是否正确
		    $valiCode = $this->_getparam ( 'code' );
		    if (!$valiCode) {
		        // @todo 判断验证码是否正确
		        exit;
		    }
		    // 检查是否为空
		    $company = $this->_getparam('company');
		    $mobile = ( int ) $this->_getparam ( 'mobile' );
		    $password = $this->_getparam ( 'password' );
		    if (!$company || 11 != strlen ( $mobile ) || ! $password) {
				$msg = '请完整并正确填写各项信息。';
				$code = 2010;
			} elseif (!$this->_getparam ( 'acceptme' )) {
                $msg = '必须选同意“使用条款”。';
                $code = 2030;
            } else {
                
                // 公司是否已经存在
                if (AdminCompany::model()->exists('name=:company', array(':company'=>$company))){
                    $msg = '贵公司已经注册过了。';
                    $code = 2060;
                } else {
                    $com = new AdminCompany();
                    $com->name = $company;
                    $com->ctime = date('Y-m-d H:i:s');
                    $com->utime = date('Y-m-d H:i:s');
                    $com->save();
                    
                    // 手机是否已经注册过
                    if (UserModel::model()->exists('mobile=:mobile', array ( ':mobile' => $mobile))) {
                        $msg = '您的手机号码已经注册过了。';
                        $code = 2040;
                    } else {
                        $user = new UserModel ();
                        $user->company = $com->id;
                        $user->mobile = $mobile;
                        $secret = rand(10000, 99999);
                        $user->secret = $secret;
                        $password = strtolower ( md5($password) );
                        $user->password = strtolower ( md5 ( $password . strtolower ( $secret ) ) );
                        
                        if ($user->save()) {
                            $code = 2000;
                            $msg = '注册成功。';
                            
                            // 清除短信发送标识符和数量
                            unset($_SESSION['sendcodemark']);
                            unset($_SESSION['sendcodenum']);
                            
                            setcookie('sendcodemark', '', time()-3600);
                        } else {
                            $code = 2050;
                            $msg = '注册失败，请联系管理员。';
                        }
                    }
                }

			}
		}

        exit ( json_encode ( array (
				'code' => $code,
				'msg' => $msg
		) ) );
	}
}
