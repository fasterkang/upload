**使用方法**

`use Upload\File;`

***

`$info = new File('fileUpload');`

`$sizeObj = new \Upload\Validation\Size(2, '文件大小不在范围内');`

`$extensionObj = new \Upload\Validation\Extension(array('png'),'文件类型错误');`

```$bool = $info->setName(uniqid().'_123')->setValidator($sizeObj)->setValidator($extensionObj) ->upload(__DIR__)```

​         