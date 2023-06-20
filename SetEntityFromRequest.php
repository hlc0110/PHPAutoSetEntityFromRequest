<?php
/**
 * 一个简单的请求参数绑定到类的属性上
 * 如果请求参数过多，手动挨个接收代码会很长
 * 注意：实体类的方法名是标准的getXxx or setXxx
 */
class RequestParamsBindManager{
    private static $bindObject;        // 需要绑定的实体类
    private static $reflection;

    /**
     * @throws ReflectionException
     */
    public static function bind($bindObject){
        self::$reflection = new ReflectionClass($bindObject);
        self::$bindObject = $bindObject;

        $attrs = self::$reflection->getProperties();        // ReflectionProperty 数组
        foreach ($attrs as $attr){
            $requestName = $attr->getName();
            $setAttrMethod = 'set' . ucfirst($requestName);
            if(isset($_REQUEST[$requestName]) && self::$reflection->hasMethod($setAttrMethod)){
                $method = self::$reflection->getMethod($setAttrMethod);
                $method->invoke(self::$bindObject, self::getReqValue($requestName));
            }
        }
        return self::$bindObject;
    }
    // 这里可以做一些过滤
    public static function getReqValue($name){
        return $_REQUEST($name);
    }
}



/** 以下是业务代码 **/

/**
 * 请求参数实体类,需要接收的参数，定义成类的属性，提供set方法
 */
class RequestParmas{
    private $uid;
    private $nickname;

    public function getUid(){
        return $this->uid;
    }

    public function setUid($uid){
        $this->uid = $uid;
        return $this;
    }

    public function getNickname(){
        return $this->nickname;
    }

    public function setNickname($nickname){
        $this->nickname = $nickname;
        return $this;
    }
}

// 接收参数
$requestParmas = RequestParamsBindManager::bind(new RequestParmas());

print_r($requestParmas);

// 使用接收的参数
$requestParmas->getUid();
?>
