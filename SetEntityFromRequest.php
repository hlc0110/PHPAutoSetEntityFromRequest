<?php
/**
 * 一个简单的请求参数绑定到类的属性上
 * 如果请求参数过多，手动挨个接收代码会很长
 * 注意：实体类的方法名是标准的getXxx or setXxx
 */
class RequestParamsBindEntity{
    private $bindObject;        // 需要绑定的实体类对象
    private $reflection;
    public function __construct($bindObject){
        $this->bindObject = $bindObject;
        $this->reflection = new ReflectionClass($this->bindObject);
    }

    /**
     * @throws ReflectionException
     */
    public function bindFromRequest(){
        $attrs = $this->reflection->getProperties();        // ReflectionProperty 数组
        foreach ($attrs as $attr){
            $requestName = $attr->getName();
            $setAttrMethod = 'set' . ucfirst($requestName);
            if(isset($_REQUEST[$requestName]) && $this->reflection->hasMethod($setAttrMethod)){
                ($this->reflection->getMethod($setAttrMethod))->invoke($this->bindObject, $this->getReqValue($requestName));
            }
        }
    }
    // 这里可以做一些过滤
    public function getReqValue($name){
        return $_REQUEST[$name];
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

$requestParmas = new RequestParmas();
$requestParamsBindEntity = new RequestParamsBindEntity($requestParmas);
$requestParamsBindEntity->bindFromRequest();

print_r($requestParmas);

// 使用接收的参数
$requestParmas->getUid();

?>
