# StorageClient

StorageClient for Min/Storage

## Require

- PHP 8.\*+
- Composer
- Laravel

## Install

- install package composer

```
composer require Min/StorageClient
```

## Publish Config

```
php artisan vendor:publish <sub>tag=config
```

## Config ENV

```
STORAGE_SERVICE_CLIENT_URI=http://localhost:8001
STORAGE_SERVICE_CLIENT_ID=978b4967-7174-4d89-a7ad-bc288a63099b
STORAGE_SERVICE_CLIENT_SECRET=wtW2i7lb8U5QZSBWdGnO65clXiib7bZAAW8tCyib
```

## Config file

- add disk config filesystem

```
'msv' => [
            'driver' => 'msv',
            'uri' => env('STORAGE_SERVICE_CLIENT_URI'),
            'clientId' => env('STORAGE_SERVICE_CLIENT_ID'),
            'clientSecret' => env('STORAGE_SERVICE_CLIENT_SECRET'),
            'scope' => 'storage',
        ],
```

- client config

```
return [
    'token' => '/api/oauth/token',
    'option' => [
        'file' => [
            'list' => '/api/storage/client/file/list/',
            'get' => '/api/storage/client/file/get/',
            'upload' => '/api/storage/client/file/upload/',
            'rename' => '/api/storage/client/file/rename/',
            'move' => '/api/storage/client/file/move/',
            'update' => '/api/storage/client/file/update/',
            'delete' => '/api/storage/client/file/delete/',
            'forcedelete' => '/api/storage/client/file/forcedelete/',
        ],
        'trash' => [
            'list' => '/api/storage/client/trash/list/',
            'delete' => '/api/storage/client/trash/delete/',
            'clear' => '/api/storage/client/trash/clear/',
            'restore' => '/api/storage/client/trash/restore/',
        ],
        'folder' => [
            'list' => '/api/storage/client/folder/list/',
            'exists' => '/api/storage/client/folder/exists/',
            'create' => '/api/storage/client/folder/create/',
            'rename' => '/api/storage/client/folder/rename/',
            'getfile' => '/api/storage/client/folder/getfile/',
            'delete' => '/api/storage/client/folder/delete/',
        ],
    ],
    'config' => [
        'uri' => config('system.msv_client', env('STORAGE_SERVICE_CLIENT_URI')),
        'client_id' => config('system.msv_client_id', env('STORAGE_SERVICE_CLIENT_ID')),
        'client_secret' => config('system.msv_client_secret', env('STORAGE_SERVICE_CLIENT_SECRET')),
    ],
];

```

## Register Provider

- add provider in ./config/app.php

```
Min\StorageClient\Providers\StorageProvider::class,
```

## Use with Illuminate\Support\Facades\Storage

### use with file
- get file info
<sub>method getInfo($filepath) _
<sub>filepath is the path to the file _

```
    $filepath = "image/image1.jpg";
    $fileInfo =  Storage::disk('msv')->getInfo($filepath);
```
- uploadFile
method uploadFile($folder,$file)
_folder is the folder to save the file_
_file is the file upload from request_

```
use Illuminate\Http\Request;
public function store(Request $request)
{
    $file = $request->file('file');
    $folder = $request->folder;
    $fileInfo =  Storage::disk('msv')->uploadFile($folder, $file); //return file uploaded info
}
```
- updateFile
method updateFile($path,$file, $id)
_path is the path of file uploaded_
_file is the file upload from request_
_id is id file uploaded_

```
 $path  = "image/image1.jpg";
 $id_file =  Storage::disk('msv')->getId($path); //return id file uploaded
 $file = $request->file('file');
 $fileInfo = Storage::disk('msv')->updateFile($path, $file, $id);
```

- renameFile
method renameFile($path,$newname)
_path is the path of file uploaded_
_newname is the new name file change_
```
 $path  = "image/image1.jpg";
 $newname = "image2.jpg";
 $fileInfo = Storage::disk('msv')->renameFile($path, $newname);
```
- moveFile
method moveFile($path,$newfolder)
_path is the path of file uploaded_
_newfolder is the new folder file change_
```
 $path  = "image/image1.jpg";
 $newfolder = "images";
 $fileInfo = Storage::disk('msv')->moveFile($path, $newfolder);
```
```

- delete
method delete($id)
_id is the id of file uploaded_
```
$filepath = "image/image1.jpg";
$file_id =  Storage::disk('msv')->getId($filepath);
return  Storage::disk('msv')->delete($file_id);
```

- restore
method restore($id)
_id is the id of file uploaded_
```
$filepath = "image/image1.jpg";
$file_id =  Storage::disk('msv')->getId($filepath);
return  Storage::disk('msv')->restore($file_id);
```

- forceDelete
method forceDelete($id)
_id is the id of file uploaded_
```
$filepath = "image/image1.jpg";
$file_id =  Storage::disk('msv')->getId($filepath);
return  Storage::disk('msv')->forceDelete($file_id);
```

### use with folder

- list
method listFolder()
```
    $listFolder = Storage::disk('msv')->listFolder();
```

- exists
method forceDelete($folderName)
_folderName is the folder name need check_
```
$folderName = "image";
$FolderExists = Storage::disk('msv')->FolderExists($folderName);
```
- create
method createFolder($folderName)
_folderName is the folder name to save_
```
$folderName = "image";
$folderCreated = Storage::disk('msv')->createFolder($folderName);
```
- rename
method renameFolder($folderName, $newFolderName)
_folderName is the folder name need change_
_newFolderName is the new folder name will change_
```
$folderName = "image";
$newFolderName = "images";
$folderRename = Storage::disk('msv')->renameFolder($folderName, $newFolderName);
```
- getfile
method getfileFolder($folderName)
_folderName is the folder name need check_

```
$folderName = "image";
$folderRename = Storage::disk('msv')->getfileFolder($folderName);
```
- delete
method getfileFolder($folderName)
_folderName is the folder name need delete_
_is method can't undo_
```
$folderName = "image";
$folderRename = Storage::disk('msv')->getfileFolder($folderName);
```
