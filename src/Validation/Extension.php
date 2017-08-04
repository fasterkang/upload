<?php
// +----------------------------------------------------------------------
// | Author: fasterkang <sunkangYun@aliyun.com>
// +----------------------------------------------------------------------

namespace Upload\Validation;
use Upload\Validation\Validator as UploadValidator;

class Extension extends UploadValidator
{
    /**
     * @var array
     */
    private $extensions;

    /**
     * @var string
     */
    private $error;

    public function __construct($extensions, $error = null)
    {
        if (is_string($extensions))
        {
            $this->extensions = array($extensions);
        }
        elseif(is_array($extensions))
        {
            $this->extensions = $extensions;
        }

        if (!is_null($error))
        {
            $this->error = $error;
        }
    }

    public function validate(\Upload\File $file)
    {
        $flag = true;
        if (!in_array($file->getExtension(),$this->extensions))
        {
            $flag = false;
            if(isset($this->error))
            {
                $this->setMessage($this->error);
            }
            else
            {
                $this->setMessage('extension is not allowed');
            }
        }
        return $flag;
    }
}