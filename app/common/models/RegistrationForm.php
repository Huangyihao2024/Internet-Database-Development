<?php
/**
 * 绿茵数据团队：common/models/RegistrationForm.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace common\models;
use Yii;
class RegistrationForm extends \yii\base\Model {
 public $verifyCode; public $username; public $email; public $password;
 public function rules() { return [
  [['username','email','password','verifyCode'],'required'], ['verifyCode','captcha','captchaAction'=>'site/captcha'], [['username','email'],'trim'],
  ['username','match','pattern'=>'/^[a-zA-Z0-9_]{3,32}$/','message'=>'用户名为3—32位字母、数字或下划线。'],
  ['email','email'],['email','string','max'=>255],['password','string','min'=>10,'max'=>128],
  [['username','email'],'unique','targetClass'=>User::class],
 ]; }
 public function attributeLabels() { return ['verifyCode'=>'验证码','username'=>'用户名','email'=>'邮箱','password'=>'密码（至少10位）']; }
 public function register() {
  if (!$this->validate()) return false;
  return Yii::$app->db->transaction(function(){
   $u=new User();$u->username=$this->username;$u->email=$this->email;$u->status=10;$u->role='member';
   $u->generateAuthKey();$u->setPassword($this->password);
   if (!$u->save()) throw new \RuntimeException('账户保存失败');
   return $u;
  });
 }
}
