<?php
define('API_KEY','361884250:AAG68qzLAzu-HKaR2BUJ71Iae7devL4t2Bs');
//----######------
function makereq($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
//##############=--API_REQ
function apiRequest($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }
  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }
  foreach ($parameters as $key => &$val) {
    // encoding to JSON array parameters, for example reply_markup
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = "https://api.telegram.org/bot".API_KEY."/".$method.'?'.http_build_query($parameters);
  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  return exec_curl_request($handle);
}
//----######------
//---------
$update = json_decode(file_get_contents('php://input'));
var_dump($update);
//=========
$chat_id = $update->message->chat->id;
$message_id = $update->message->message_id;
$from_id = $update->message->from->id;
$name = $update->message->from->first_name;
$username = $update->message->from->username;
$textmessage = isset($update->message->text)?$update->message->text:'';
$reply = $update->message->reply_to_message->forward_from->id;
$stickerid = $update->message->reply_to_message->sticker->file_id;
$photo = $update->message->photo;
$video = $update->message->video;
$sticker = $update->message->sticker;
$file = $update->message->document;
$music = $update->message->audio;
$voice = $update->message->voice;
$forward = $update->message->forward_from;
$admin = 294665580;
//-------
function SendMessage($ChatId, $TextMsg)
{
 makereq('sendMessage',[
'chat_id'=>$ChatId,
'text'=>$TextMsg,
'parse_mode'=>"MarkDown"
]);
}
function SendSticker($ChatId, $sticker_ID)
{
 makereq('sendSticker',[
'chat_id'=>$ChatId,
'sticker'=>$sticker_ID
]);
}
function Forward($KojaShe,$AzKoja,$KodomMSG)
{
makereq('ForwardMessage',[
'chat_id'=>$KojaShe,
'from_chat_id'=>$AzKoja,
'message_id'=>$KodomMSG
]);
}
function save($filename,$TXTdata)
	{
	$myfile = fopen($filename, "w") or die("Unable to open file!");
	fwrite($myfile, "$TXTdata");
	fclose($myfile);
	}
//===========

    $step = "";
    if (file_exists("data/users/$from_id/step.txt")) {
      $step = file_get_contents("data/users/$from_id/step.txt");
    }
    if ($textmessage == "/cancel") {
var_dump(makereq('sendMessage',[
        'chat_id'=>$update->message->chat->id,
        'text'=>"تمامی موارد کنسل شدند",
        'parse_mode'=>'MarkDown',
        'reply_markup'=>json_encode([
              'keyboard'=>[
              [
                ['text'=>"فروشگاه 🛒"],['text'=>"خرید الماس 💎"]
              ],
			  [
			    ['text'=>"👥پشتیبانی"],['text'=>"انتقال الماس ♻️"]
			  ],
			  [
			  ['text'=>"کد هدیه 🛍"],['text'=>"حساب کاربری 🔖"]
			  ]
            ],
            'resize_keyboard'=>true
        ])
    ]));
	save("data/users/$from_id/step.txt","none");
    }
	elseif ($textmessage == "بازگشت") {
var_dump(makereq('sendMessage',[
        'chat_id'=>$update->message->chat->id,
        'text'=>"تمامی موارد کنسل شدند",
        'parse_mode'=>'MarkDown',
        'reply_markup'=>json_encode([
            'keyboard'=>[
              [
                ['text'=>"فروشگاه 🛒"],['text'=>"خرید الماس 💎"]
              ],
			  [
			    ['text'=>"👥پشتیبانی"],['text'=>"انتقال الماس ♻️"]
			  ],
			  [
			  ['text'=>"کد هدیه 🛍"],['text'=>"حساب کاربری 🔖"]
			  ]
            ],
            'resize_keyboard'=>true
        ])
    ]));
	save("data/users/$from_id/step.txt","none");
    }
elseif ($textmessage == "انتقال الماس ♻️"){
sendMessage($chat_id,"`این بخش موقتا غیر فعال میباشد`");
} 
    elseif ($step == "useCode") {
      if (file_exists("data/codes/$textmessage.txt")) {
        $price = file_get_contents("data/codes/$textmessage.txt");
        $coin = file_get_contents("data/users/".$from_id."/coin.txt");
        settype($coin,"integer");
        $newcoin = $coin + $price;
        save("data/users/".$from_id."/coin.txt",$newcoin);
        unlink("data/codes/$textmessage.txt");
        save("data/users/$from_id/step.txt","none");
var_dump(makereq('sendMessage',[
        'chat_id'=>$update->message->chat->id,
        'text'=>"الماس های شما به مقدار `$price` افزایش یافت",
        'parse_mode'=>'MarkDown',
        'reply_markup'=>json_encode([
         'keyboard'=>[
              [
                ['text'=>"فروشگاه 🛒"],['text'=>"خرید الماس 💎"]
              ],
			  [
			    ['text'=>"👥پشتیبانی"],['text'=>"انتقال الماس ♻️"]
			  ],
			  [
			  ['text'=>"کد هدیه 🛍"],['text'=>"حساب کاربری 🔖"]
			  ]
            ],
            'resize_keyboard'=>true
        ])
    ]));
	}
      else {
        SendMessage($chat_id,"کد وارد شده نا معتبر است");
      }
    }
    elseif ($step == "settitle") {
      SendMessage($chat_id,"توضیحات محصول :‌ ");
      $count = file_get_contents("data/products/count.txt");
      save("data/products/$count.txt",$textmessage."(******)");
      save("data/products/$textmessage.txt",$count);
      save("data/users/$from_id/step.txt","setabout");
    }
    elseif ($step == "setabout") {
      SendMessage($chat_id,"لینک های خرید موفق : ");
      $count = file_get_contents("data/products/count.txt");
      $last= file_get_contents("data/products/$count.txt");
      save("data/products/$count.txt",$last.$textmessage."(******)");
      save("data/users/$from_id/step.txt","successLink");
    }
    elseif ($step == "successLink") {
      SendMessage($chat_id,"قیمت محصول :‌ ");
      $count = file_get_contents("data/products/count.txt");
      $last= file_get_contents("data/products/$count.txt");
      save("data/products/$count.txt",$last.$textmessage."(******)");
      save("data/users/$from_id/step.txt","setprice");
    }
    elseif ($step == "setprice") {

      $count = file_get_contents("data/products/count.txt");
      $last = file_get_contents("data/products/$count.txt");
      save("data/products/$count.txt",$last.$textmessage."");
      save("data/users/$from_id/step.txt","none");
      settype($count,"integer");
      $newcount = $count + 1;
      save("data/products/count.txt",$newcount);
      SendMessage($chat_id,"محصول شماره $newcount ثبت شد .");
    }
    elseif ($textmessage == "محصول جدید" && $from_id == $admin) {
      SendMessage($chat_id,"عنوان محصول : ");
      save("data/users/$from_id/step.txt","settitle");
    }

    elseif ($textmessage == "فروشگاه 🛒") {
      $keyboard = [];
      $count = file_get_contents("data/products/count.txt");
      $n = 0;
      $text = "";
      while ($n <= $count ) {
        $post = file_get_contents("data/products/$n.txt");
        $arrayPost = explode('(******)',$post);
        $n = $n + 1;
        array_push($keyboard,[$arrayPost['0']]);
      }
      json_encode($keyboard);

      var_dump(makereq('sendMessage',[
           'chat_id'=>$update->message->chat->id,
           'text'=>"محصولات : ",
     'parse_mode'=>'MarkDown',
           'reply_markup'=>json_encode([
               'keyboard'=>$keyboard,
               'resize_keyboard'=>true
           ])
       ]));

    }

	 elseif(strpos($textmessage,'/start') !== false) {
  $id = str_replace("/start ","",$textmessage);

  if (!file_exists("data/users/$from_id/coin.txt")) {
    mkdir("data/users/$from_id");
    save("data/users/$from_id/coin.txt","0");
    save("data/users/$from_id/step.txt","none");
    save("data/users/$from_id/chance.txt","0|0");
    $members = file_get_contents("Member.txt");
    save("Member.txt",$members."$from_id\n");
    SendMessage($chat_id,"ثبت نام با موفقیت انجام شد"); //mituni ino comment koni vase test has!

    if ($id != "") {
      if ($id != $from_id) {
          SendMessage($id,"یک نفر از طریق لینک شما وارد ربات شد");
          $coin = file_get_contents("data/users/$id/coin.txt");
          settype($coin,"integer");
          $newcoin = $coin + 1;
          save("data/users/$id/coin.txt",$newcoin);
      }
      else {
        SendMessage($chat_id,"شما قبلا درربات عضو بودید");
      }
    }
  }
var_dump(makereq('sendMessage',[
        'chat_id'=>$update->message->chat->id,
        'text'=>"سلام خوش امدید
		این ربات کاملا اتوماتیک بوده و پس از پرداخت فورا محصول خود را دریافت میکنید
		@NeroTeam > @NeroShopBot",
        'parse_mode'=>'MarkDown',
        'reply_markup'=>json_encode([
          'keyboard'=>[
              [
                ['text'=>"فروشگاه 🛒"],['text'=>"خرید الماس 💎"]
              ],
			  [
			    ['text'=>"👥پشتیبانی"],['text'=>"انتقال الماس ♻️"]
			  ],
			  [
			  ['text'=>"کد هدیه 🛍"],['text'=>"حساب کاربری 🔖"]
			  ]
            ],
            'resize_keyboard'=>true
        ])
    ]));
}
elseif ($textmessage == "پشتیبانی 👤"){
sendMessage($chat_id,"پشتیبانی انلاین در خدمت شماست ، قبل از پیام به نکات زیر توجه کنید :
- سوال در مورد محصولات نپرسید
- از گفتن سلام و احوال پرسی جدا خودداری کنید
- از تکرار سوال خود خودداری کنید
- درخواست تخفیف یا الماس رایگان نکنید
@NeroDevBot");
}
elseif($textmessage == "خرید الماس 💎"){
sendMessage($chat_id,"برای خرید از فروشگاه باید الماس داشته باشی قیمت هر 4 تا الماس هزار تومنه و الماس ها در پک های 20 تایی ، 40 تایی و 80 تایی موجود است
عدد زیر را در قسمت شناسه شما وارد کنید در غیر این صورت پرداخت ثبت نخواهد شد
$chat_id
[20 الماس (5 هزار تومان)](https://www.payping.ir/d/2REg)
[40 الماس (10 هزار تومان)](https://www.payping.ir/d/C78P)
[80 الماس (20 هزار تومان)](https://www.payping.ir/d/hvhj)");
}
elseif ($textmessage == "/panel" && $from_id == $admin){
var_dump(makereq('sendMessage',[
        'chat_id'=>$update->message->chat->id,
        'text'=>"پنل مدیریت باز شد :",
        'parse_mode'=>'MarkDown',
        'reply_markup'=>json_encode([
            'keyboard'=>[
              [
                ['text'=>"اهدای سکه"],['text'=>"کم کردن سکه"]
              ],
			  [
			    ['text'=>"محصول جدید"],['text'=>"حذف محصول"]
			  ]
            ],
            'resize_keyboard'=>true
        ])
    ]));  
	}
elseif ($textmessage == "اهدای سکه" && $from_id == $admin){
sendMessage($chat_id,"ادمین عزیز جهت اهدای سکه به کاربری از دستور زیر استفاده کنید
/addcoin USERID COIN");
}
 elseif ($textmessage == "کم کردن سکه" && $from_id == $admin){
sendMessage($chat_id,"ادمین عزیز جهت کم کردن سکه کاربری از دستور زیر استفاده کنید
/getcoin USERID COIN");
}
elseif ($textmessage == "حذف محصول" && $from_id == $admin){
sendMessage($chat_id,"ادمین عزیز جهت حذف یک محصول از دستور زیر استفاده کنید
/delpost PostId");
}
elseif (strpos($textmessage,"/getcoin") !== false && $from_id == $admin) {
  $text = explode(" ",$textmessage);
  if ($text['2'] != "" && $text['1'] != "") {
    $coin = file_get_contents("data/users/".$text['1']."/coin.txt");
    settype($coin,"integer");
    $newcoin = $coin - $text['2'];
    save("data/users/".$text['1']."/coin.txt",$newcoin);
    SendMessage($chat_id,"عملیات فوق با موفقیت انجام شد");
    SendMessage($text['1'],"ادمین از شما ".$text['2']." الماس کم کرد");
  }
  else {
    SendMessage($chat_id,"Syntax Error!");
  }
}
elseif (strpos($textmessage,"/delpost") !== false && $from_id == $admin) {
  $id = str_replace("/delpost ","",$textmessage);
  if (file_exists("data/products/$id.txt")) {
    $product = file_get_contents("data/products/$id.txt");
    $array = explode("(******)",$product);
    $title = $array['0'];
    unlink("data/products/$title.txt");
    unlink("data/products/$id.txt");
    SendMessage($chat_id,"محصول حذف شد");
  }
  else {
    SendMessage($chat_id,"محصول یافت نشد .");
  }
}
elseif (strpos($textmessage,"کد هدیه") !== false) {
  save("data/users/$from_id/step.txt","useCode");
var_dump(makereq('sendMessage',[
        'chat_id'=>$update->message->chat->id,
        'text'=>"کد موردنظر را ارسال نمایید",
        'parse_mode'=>'MarkDown',
        'reply_markup'=>json_encode([
            'keyboard'=>[
              [
                ['text'=>"بازگشت"]
              ]
            ],
            'resize_keyboard'=>true
        ])
    ]));  

}
elseif (strpos($textmessage,"/createcode") !== false && $from_id == $admin) {
  $text = explode(" ",$textmessage);
  $code = $text['1'];
  $value = $text['2'];
  save("data/codes/$code.txt",$value);
  SendMessage($chat_id,"کد با موفقیت ساخته شد .
  کد : $code
  مقدار : $value سکه");

}
elseif (strpos($textmessage,"/buy") !== false) {
  $id = str_replace("/buy","",$textmessage);
  if ($id == "") {
      SendMessage($chat_id,"محصول در سیستم موجود نمیباشد");
  }
  else {
    if (file_exists("data/products/$id.txt")) {
      $product = file_get_contents("data/products/$id.txt");
      $array = explode("(******)",$product);
      $price = $array['3'];
      $coin = file_get_contents("data/users/$from_id/coin.txt");
      if ($coin >= $price) {
        $coin = file_get_contents("data/users/".$from_id."/coin.txt");
        settype($coin,"integer");
        $newcoin = $coin - $price;
        save("data/users/".$from_id."/coin.txt",$newcoin);
        SendMessage($chat_id,"محصول مورد نظر خریداری شد");
        SendMessage($chat_id,"لینک دانلود محصول :
        ".$array['2']);
      }
      else {
        SendMessage($chat_id,"شما الماس کافی نداید");
      }
    }
    else {
      SendMessage($chat_id,"محصول در سیستم موجود نمیباشد");
    }
  }

}
elseif (strpos($textmessage,"/transfer") !== false) {
  $text = explode(" ",$textmessage);
if ($coin >= $text['2'] && $text['2'] >= 1) {
    $coin = file_get_contents("data/users/".$from_id."/coin.txt");
    settype($coin,"integer");
    if ($coin >= $text['2']) {
if ( $text['2'] > 1) {
      $newcoin = $coin - $text['2'];
      save("data/users/".$from_id."/coin.txt",$newcoin);

      $coin = file_get_contents("data/users/".$text['1']."/coin.txt");
      settype($coin,"integer");
      $newcoin = $coin + $text['2'];
      save("data/users/".$text['1']."/coin.txt",$newcoin);
      SendMessage($chat_id,"عملیات با موفقیت انجام شد");
    SendMessage($text['1'],"تعداد ".$text['2']." الماس به شما اضافه شد");
    else {
      SendMessage($chat_id,"موجودی شما کافی نمیباشد");
    }
  }
  else {
    SendMessage($chat_id,"Syntax Error!");
  }
}
else {
	SendMessage($chat_id,"مقدار انتقال میبایست بیشتر از 1 باشد");
}
} }

elseif (strpos($textmessage,"/addgem") !== false && $from_id == $admin) {
  $text = explode(" ",$textmessage);
  if ($text['2'] != "" && $text['1'] != "") {
    $coin = file_get_contents("data/users/".$text['1']."/coin.txt");
    settype($coin,"integer");
    $newcoin = $coin + $text['2'];
    save("data/users/".$text['1']."/coin.txt",$newcoin);
    SendMessage($chat_id,$text['2']."تعداد ".$text['2']." الماس به کاربر اضافه شد");
    SendMessage($text['1'],"تعداد ".$text['2']." الماس به شما اضافه شد");
  }
  else {
    SendMessage($chat_id,"Syntax Error!");
  }
}
elseif ($textmessage == "حساب من ✔️") {
  $coin = file_get_contents("data/users/$from_id/coin.txt");
  SendMessage($chat_id,"نام شما : $name
الماس ها : $coin
شناسه شما : $chat_id");
}
else {
  if (file_exists("data/products/$textmessage.txt")) {
    $id = file_get_contents("data/products/$textmessage.txt");
    $product = file_get_contents("data/products/$id.txt");
    $array = explode("(******)",$product);

    SendMessage($chat_id,"نام محصول :‌ ".$array['0']."

    توضیحات محصول :
    ".$array['1']."

    قیمت :‌ ".$array['3']." الماس

    خرید محصول با دستور /buy".$id);
  }
  else {
    SendMessage($chat_id,"`دستور مورد نظر یافت نشد`");
  }
}unlink ("error_log");


?>
