<?php
// +----------------------------------------------------------------------
// | Author: fasterkang <sunkangYun@aliyun.com>
// +----------------------------------------------------------------------
namespace Upload\Validation;

abstract class Validator
{
    /**
     * error msg
     * @var string
     */
    protected $errorMsg;

    /**
     * @param $msg
     * @return $this
     */
    public function setMessage($msg)
    {
        $this->errorMsg = $msg;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->errorMsg;
    }

    /**
     * validate
     * @return mixed
     */
    abstract public function validate(\Upload\File $file);

}