# CKFinder 3 GetFileInfo Plugin Sample

This sample plugin illustrates [how to create custom CKFinder commands](https://ckeditor.com/docs/ckfinder/ckfinder3-php/howto.html#howto_custom_commands).

If this plugin is enabled, you can call an additional `GetFileInfo` command that returns some very basic
information about a file, like the size and the last modification timestamp. This behavior can be simply altered to return any 
other information about the file (for example EXIF data for images or ID3 tags for mp3 files).

## Sample Request (HTTP GET Method)

Get basic information about the `foo.png` file located in the `sub1` directory of the `Files` resource type.

```
ckfinder.php?command=GetFileInfo&type=Files&currentFolder=/sub1/&fileName=foo.png
```

## Sample Response

```
{
    "resourceType": "Files",
    "currentFolder": {
        "path": "/sub1/",
        "url": "/ckfinder/userfiles/files/sub1/",
        "acl": 255
    },
    "type": "file",
    "path":"files\/sub1\/1.png",
    "timestamp":1425909932,
    "size":1336
}
```

The above response has also appended additional information about the resource type and current folder, which is a default behavior of CKFinder JSON responses. You can disable this by calling:

```php
$workingFolder->omitResponseInfo();
```

Another solution is to return any other type of [Response](http://symfony.com/doc/current/components/http_foundation/introduction.html#response) object directly from the `execute` method.

For more detailed information about commands, please refer to [Commands section](https://ckeditor.com/docs/ckfinder/ckfinder3-php/commands.html) of CKFinder 3 PHP connector documentation.
