<?php
// +----------------------------------------------------------------------
// | Author: fasterkang <sunkangYun@aliyun.com>
// +----------------------------------------------------------------------
namespace Upload\Validation;
use Upload\Validation\Validator as UploadValidator;

class Size extends UploadValidator
{
    /**
     * @var integer
     */
    private $maxSize;

    /**
     * @var integer
     */
    private $minSize;

    /**
     * @var array
     */
    private $errors;


    public function __construct($maxSize, $errorMsg = null, $minSize = 0)
    {
        $this->maxSize = $maxSize * 1024 *1024;
        $this->minSize = $minSize * 1024 *1024;
        if (!is_null($errorMsg))
        {
            if (is_string($errorMsg))
            {
                $this->errors[] = $errorMsg;
            }
           elseif (is_array($errorMsg))
           {
               $this->errors = $errorMsg;
           }
        }
    }

    public function validate(\Upload\File $file)
    {
        $flag = true;
        if ($file->getFileSize() > $this->maxSize)
        {
            $flag = false;

            if (isset($this->errors[0]))
            {
                $maxError = $this->errors[0];
            }
            else
            {
                $maxError = 'file size is too large';
            }
            $this->setMessage($maxError);
        }
        elseif ($file->getFileSize() < $this->minSize)
        {
            $flag = false;

            if (isset($this->errors[1]))
            {
                $minError = $this->errors[1];
            }
            elseif (isset($this->errors[0]))
            {
                $minError = $this->errors[0];
            }
            else
            {
                $minError = 'file size is too small';
            }
            $this->setMessage($minError);
        }

        return $flag;
    }
}