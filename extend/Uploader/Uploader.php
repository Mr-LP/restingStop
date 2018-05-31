<?php
namespace Uploader;
class Uploader {
	/**
	 *ajaxupload 插件，图片上传
	 * @param string $button
	 * @param string $input
	 */
	public static function uploader($type,$button,$input,$imgPath) {
		$action = url('upload/Index/upload');
		// $action = "index.php?s=upload/Index/upload";
		$name = "file";
		$str = '';
		$str .= '<p class="m-top-sm" id="'.$button.'-wrap">';
		$imgExts = 'gif|jpeg|jpg|png|bmp';
		$imgExts = explode("|", $imgExts);
		// 文件后缀
		$imgPathExt = pathinfo($imgPath, PATHINFO_EXTENSION);
		if($imgPath && in_array(strtolower($imgPathExt), $imgExts)){
			$str .= "<img src='".$imgPath."' width='200'>";
		}
		$str .= '</p>';
		$str .= '<script type="text/javascript" src="ADMIN_PATH/js/ajaxupload.js"></script>';
		$str .= "<script type=\"text/javascript\">\r\n";
		$str .= "new AjaxUpload('#".$button."', {\r\n";
            // 提交目标url
        	$str .= "action: '".$action."',\r\n";
			// 服务端接收的名称
			$str .= "name: '".$name."',\r\n";
			$str .= "autoSubmit:true,\r\n";
			$str .= "onChange: function (file, extension) {\r\n";
				if($type == 'image'){
					$str .= "if (!new RegExp(/(jpg)|(jpeg)|(gif)|(png)/i).test(extension)) {\r\n";
					$str .= "alert('图片格式不支持，请选择jpg、jpeg、gif、png格式');return false;";//错误处理
					$str .= "}\r\n";
				}
			$str .= "},\r\n";
			$str .= "onSubmit: function (file, extension) {\r\n";
				$str .= "$('#".$button."').text('上传中');\r\n";//上传完成
			$str .= "},\r\n";
			$str .= "onComplete: function (file, response) {\r\n";
				$str .= "if(response){\r\n";
					$str .= "if(JSON.parse(jQuery(response).text()).status == 'success'){\r\n";
						$str .= "$('#".$button."').text('上传完成，点击重新选择');\r\n";//上传完成
						$str .= "$('#".$input."').val(JSON.parse(jQuery(response).text()).path);\r\n";
						if($type == 'image'){
							$str .= "if($('#".$button."-wrap')){\r\n";
									$str .= "$('#".$button."-wrap').html('<img src=\"'+JSON.parse(jQuery(response).text()).path+'\" width=200>');\r\n";
							$str .= "}else{\r\n";
							$str .= "$('#".$button."').after('<p class=\"m-top-sm\" id=\"".$button."-wrap\"><img src=\"'+JSON.parse(jQuery(response).text()).path+'\" width=200></p>');";
							$str .= "}\r\n";
						}
					$str .= "}\r\n";
				$str .= "}\r\n";
			$str .= "},\r\n";
		$str .= "});\r\n";
		$str .= '</script>';
		return $str;
	}
}