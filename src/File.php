<?php
// +----------------------------------------------------------------------
// | Author: fasterkang <sunkangYun@aliyun.com>
// +----------------------------------------------------------------------
namespace Upload;
use Upload\Exception\UploadException;

class File extends \SplFileInfo
{
    /**
     * The file origin name
     * @var string
     */
    private $originalName;

    /**
     * The file origin minetype
     * @var string
     */
    private $originalMinetype;

    /**
     * The tmp file path
     * @var string
     */
    private $tmpFile;

    /**
     * The file size
     * @var integer
     */
    private $fileSize;

    /**
     * The file name without extension
     * @var string
     */
    private $name;

    /**
     * The file extension
     * @var string
     */
    private $extension;

    /**
     * if upload fail maybe there is response a errorcode
     * @var integer
     * @link http://www.php.net/manual/en/features.file-upload.errors.php
     */
    private $errorCode;

    /**
     * @var object
     */
    private $validators;


    /**
     * if upload fail maybe there is a errorMsg
     * @var string
     */
    private $errorMsg;

    public function __construct($name)
    {
        if (!isset($_FILES[$name]))
        {
            throw new \InvalidArgumentException("Cannot find file info by {$name}");
        }

        $this->originalName     = $_FILES[$name]['name'];
        $this->originalMinetype = $_FILES[$name]['type'];
        $this->tmpFile          = $_FILES[$name]['tmp_name'];
        $this->fileSize         = $_FILES[$name]['size'];
        $this->errorCode        = $_FILES[$name]['error'];
        parent::__construct($this->tmpFile);
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        if (empty($this->name))
        {
            $this->name = pathinfo($this->originalName,PATHINFO_FILENAME);
        }

        return $this->name;
    }

    /**
     * set file name without extension
     * @param $name
     * @return object
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * get file extension
     * @return mixed|string
     */
    public function getExtension()
    {
       if (empty($this->extension))
       {
           $this->extension = pathinfo($this->originalName, PATHINFO_EXTENSION);
       }

       return $this->extension;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    /**
     * @param $path without name and extension
     * @return bool
     */
    public function upload($path)
    {
        if (false === $this->validate())
        {
            return false;
        }

        if (empty($path))
        {
            throw new UploadException('upload path lose');
        }

        $fullpath = rtrim($path,'/').'/'.$this->getName().'.'.$this->getExtension();

        return move_uploaded_file($this->getPathname(), $fullpath);
    }

    /**
     * @return bool
     */
    protected function validate()
    {
        $this->isUploadOk();

        return $this->isValidatorOk();
    }

    /**
     * 设置验证对象
     * @param Validation\Validator $validator
     * @return $this
     */
    public function setValidator(\Upload\Validation\Validator $validator)
    {
        if ($validator)
        {
            $this->validators[] = $validator;
        }
        return $this;
    }

    /**
     * validate if base upload error
     */
    protected function isUploadOk()
    {
        switch ($this->errorCode)
        {
            case UPLOAD_ERR_OK:
                $message = '';
                break;
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }

        if (!empty($message))
        {
            throw new UploadException($message);
        }

    }

    /**
     * validators validate
     * @return bool
     */
    protected function isValidatorOk()
    {
        $flag = true;
        if (!empty($this->validators))
        {
            foreach($this->validators as $validator)
            {
                if (false === $validator->validate($this))
                {
                    $this->errorMsg = $validator->getMessage();
                    $flag = false;
                    break;
                }
            }
        }

        return $flag;
    }
}