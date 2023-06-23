<?php

/**
 * 一个简单的请求参数绑定到类的属性上
 * 如果请求参数过多，挨个获取有点累
 * 注意：实体类的方法名是标准的getXxx or setXxx
 */
class RequestParamsBindManager{
    private static $bindObject;        // 需要绑定的实体类
    private static $reflection;

    /**
     * 方式一
     * @throws Exception
     * @desc $obj = RequestParamsBindManager::bindFromObject(new Abc())
     */
    public static function bindFromObject($bindObject){
        try{
            self::$reflection = new ReflectionClass($bindObject);
            self::$bindObject = $bindObject;
            self::bind();
            return self::$bindObject;
        }catch (ReflectionException $exception){
            throw new Exception("解析类异常");
        }
    }

    /**
     * 方式二
     * @desc $obj = RequestParamsBindManager::bingFromClass(Abc::class)
     * @throws Exception
     */
    public static function bingFromClass($class){
        try {
            self::$reflection = new ReflectionClass($class);
            self::$bindObject = self::$reflection->newInstance();
            self::bind();
            return self::$bindObject;
        }catch (ReflectionException $exception){
            throw new Exception("解析类异常");
        }
    }

    // 这里可以做一些过滤
    public static function getReqValue($name){
        return $_REQUEST[$name];
    }

    protected static function bind(){
        try{
            $attrs = self::$reflection->getProperties();        // ReflectionProperty 数组
            foreach ($attrs as $attr){
                $requestName = $attr->getName();
                $setAttrMethod = 'set' . ucfirst($requestName);
                if(isset($_REQUEST[$requestName]) && self::$reflection->hasMethod($setAttrMethod)){
                    $method = self::$reflection->getMethod($setAttrMethod);
                    $method->invoke(self::$bindObject, self::getReqValue($requestName));
                }
            }
        }catch (ReflectionException $exception){
            throw new Exception("解析绑定request params失败");
        }
    }
}



class CrmSearchParams{
    private $nickname;
    private $age;
    private $gender;
    private $uid;
    private $note;
    private $addr;

    public function getNickname(){
        return $this->nickname;
    }

    public function setNickname($nickname){
        $this->nickname = $nickname;
        return $this;
    }

    private function setAge($age){$this->age = $age;}
    // .... 省略其他字段的get set方法

}

$_REQUEST['nickname'] = "php开发";
$_REQUEST['age'] = 18;
$obj1 = RequestParamsBindManager::bindFromObject(new CrmSearchParams());
print_r($obj1);

$obj2 = RequestParamsBindManager::bingFromClass(CrmSearchParams::class);
print_r($obj2);
