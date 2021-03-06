<?php
namespace Common\Controller;
use Think\Controller;

class AppframeController extends Controller {

    function _initialize() {
        $this->assign("waitSecond", 3);
       	$time=time();
        $this->assign("js_debug",APP_DEBUG?"?v=$time":"");
        if(APP_DEBUG){
        }
    }

    /**
     * AJAX返回数据
     * @param int $error 是否产生错误信息 0没有错误信息 1有错误信息
     * @param string $msg 如果有错 msg为错误信息
     * @param array $data 返回的数据 多维数组
     * @return json 统一返回json数据
     */
    protected function majaxReturn($error=0,$msg=null,$data=array())
    {
        if ($error && !$msg) {
            $error = 1;
            $msg   = L('ajaxreturn_error_msg');
            $data  = array();
        }

        if (!$error && !is_array($data)) {
            $error = 1;
            $msg = L('ajaxreturn_error_msg');
            $data = array();
        }

        //APP返回
        $return = array(
            'error' => $error,
            'msg'   => $msg,
            'data'  => $data
        );

        $type = 'json';
        switch ($type) {
            case 'json':
                header('Content-Type: application/json');
                $return = json_encode($return);
                break;
            default:
                header('Content-Type: application/json');
                $return = json_encode($return);
                break;
        }

        echo $return;
        exit;
    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    protected function ajaxReturn($data, $type = '',$json_option=0) {
        
        $data['referer']=$data['url'] ? $data['url'] : "";
        $data['state']=$data['status'] ? "success" : "fail";
        
        if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
        switch (strtoupper($type)){
        	case 'JSON' :
        		// 返回JSON数据格式到客户端 包含状态信息
        		header('Content-Type:application/json; charset=utf-8');
        		exit(json_encode($data,$json_option));
        	case 'XML'  :
        		// 返回xml格式数据
        		header('Content-Type:text/xml; charset=utf-8');
        		exit(xml_encode($data));
        	case 'JSONP':
        		// 返回JSON数据格式到客户端 包含状态信息
        		header('Content-Type:application/json; charset=utf-8');
        		$handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
        		exit($handler.'('.json_encode($data,$json_option).');');
        	case 'EVAL' :
        		// 返回可执行的js脚本
        		header('Content-Type:text/html; charset=utf-8');
        		exit($data);
        	case 'AJAX_UPLOAD':
        		// 返回JSON数据格式到客户端 包含状态信息
        		header('Content-Type:text/html; charset=utf-8');
        		exit(json_encode($data,$json_option));
        	default :
        		// 用于扩展其他返回格式数据
        		Hook::listen('ajax_return',$data);
        }
        
    }
    
    //分页
    protected function page($Total_Size = 1, $Page_Size = 0, $Current_Page = 1, $listRows = 6, $PageParam = '', $PageLink = '', $Static = FALSE) {
    	import('Page');
    	if ($Page_Size == 0) {
    		$Page_Size = C("PAGE_LISTROWS");
    	}
    	if (empty($PageParam)) {
    		$PageParam = C("VAR_PAGE");
    	}
    	$Page = new \Page($Total_Size, $Page_Size, $Current_Page, $listRows, $PageParam, $PageLink, $Static);
    	$Page->SetPager('default', '{first}{prev}{liststart}{list}{listend}{next}{last}', array("listlong" => "9", "first" => "首页", "last" => "尾页", "prev" => "上一页", "next" => "下一页", "list" => "*", "disabledclass" => ""));
    	return $Page;
    }

    //空操作
    public function _empty() {
        $this->error('该页面不存在！');
    }
    
    /**
     * 检查操作频率
     * @param int $duration 距离最后一次操作的时长
     */
    protected function check_last_action($duration){
    	
    	$action=MODULE_NAME."-".CONTROLLER_NAME."-".ACTION_NAME;
    	$time=time();
    	
    	if(!empty($_SESSION['last_action']['action']) && $action==$_SESSION['last_action']['action']){
    		$mduration=$time-$_SESSION['last_action']['time'];
    		if($duration>$mduration){
    			$this->error("您的操作太过频繁，请稍后再试~~~");
    		}else{
    			$_SESSION['last_action']['time']=$time;
    		}
    	}else{
    		$_SESSION['last_action']['action']=$action;
    		$_SESSION['last_action']['time']=$time;
    	}
    }

    public function sendmail_bak($address=null, $name=null, $title=null, $body=null)
    {
        //发送邮件
        require(VENDOR_PATH."PHPMailer-5.2.14/class.phpmailer.php");
        require(VENDOR_PATH."PHPMailer-5.2.14/class.smtp.php");
        $mail = new \PHPMailer();
        $mail->IsSMTP();
        // $mail->SMTPDebug = 1;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.qq.com";
        $mail->Port = 465;
        $mail->Username = "250175411@qq.com";
        $mail->Password = "fzibvacpdtpfcafg";
        $mail->SetFrom("250175411@qq.com", "步知道");
        $mail->AddReplyTo("250175411@qq.com", "步知道");

        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";
        // $mail->AddAddress($address, $name);
        $mail->AddAddress('business@siyanhui.com', '似颜绘-商务合作');

        // 邮件主题
        $mail->Subject = $title;
        // 邮件内容
        $mail->Body = $body;
        $mail->AltBody = "text/html";
        $mail->IsHTML(true);
        $mail->Send();
    }

    public function sendmail($address=null, $name=null, $title=null, $body=null)
    {
        //发送邮件
        require(VENDOR_PATH."PHPMailer-5.2.14/class.phpmailer.php");
        require(VENDOR_PATH."PHPMailer-5.2.14/class.smtp.php");
        $mail = new \PHPMailer();
        $mail->IsSMTP();
        // $mail->SMTPDebug = 1;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = C('SP_MAIL_SMTP');
        $mail->Port = (int)C('SP_MAIL_SMTP_PORT');
        $mail->Username = C('SP_MAIL_LOGINNAME');
        $mail->Password = C('SP_MAIL_PASSWORD');
        $mail->SetFrom(C('SP_MAIL_ADDRESS'), C('SP_MAIL_SENDER'));
        $mail->AddReplyTo(C('SP_MAIL_ADDRESS'), C('SP_MAIL_SENDER'));

        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";
        // $mail->AddAddress($address, $name);
        $mail->AddAddress('business@siyanhui.com', '似颜绘-商务合作');

        // 邮件主题
        $mail->Subject = $title;
        // 邮件内容
        $mail->Body = $body;
        $mail->AltBody = "text/html";
        $mail->IsHTML(true);
        $mail->Send();
    }
}
