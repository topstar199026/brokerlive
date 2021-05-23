<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\FileManagement;

use App\Datas\FileType;

class FileUtil extends Controller
{
    private static function getRandomFileName($fileName)
    {
        return time() . '_' . $fileName;
    }

    private static function getStoragePath($type, $actionId)
    {
        /* Add s3 storage Path*/
        switch($type)
        {
            case 'Deal.Pdf':
                return 'files/'.Auth::user()->username.'/'.$actionId;
                break;
            case 'db.gz':
                return 'files/database';
                break;
            default:
                break;
        }
    }

    private static function getFileExt($fileName)
    {
        $_fileExt = explode('.', $fileName);
        $fileExt = strtolower($_fileExt[count($_fileExt) - 1]);
        return FileType::$extentions[$fileExt];
    }

    public static function uploadFile($type, $actionId, $file)
    {
        try
        {
            $filePath = Storage::disk('s3')->putFileAs(
                self::getStoragePath($type, $actionId), $file, self::getRandomFileName($file->getClientOriginalName())
            );
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileIcon = self::getFileExt($fileName);

            return self::saveFileManagement('new', null, Auth::id(), $actionId, $fileName, $fileSize, $filePath, $fileIcon);
        }
        catch(Exception $e)
        {
            return null;
        }

    }

    public static function dbUpload()
    {
        $filePath = Storage::disk('s3')->putFileAs(
            self::getStoragePath('db.gz', ''), storage_path().'/app/backup/db.gz', self::getRandomFileName('db.gz')
        );
        Storage::disk('local')->delete('backup/db.gz');
    }

    public static function dbList()
    {
        return Storage::disk('s3')->files('files/database');
    }

    public static function downLoadFile($type, $fileId)
    {
        $filePath = '';
        switch($type)
        {
            case 'Deal.Pdf':
                $filePath = FileManagement::find($fileId)->file_location;
                break;
        }
        return Storage::disk('s3')->download($filePath);
    }

    public static function deleteFile($type, $fileId)
    {
        $filePath = '';
        switch($type)
        {
            case 'Deal.Pdf':
                $filePath = FileManagement::find($fileId)->file_location;
                self::saveFileManagement('delete', $fileId);
                break;
        }

        return Storage::disk('s3')->delete($filePath);
    }

    public static function saveFileManagement($action, $id = null, $userId = null, $dealId = null, $fileName = null, $fileSize = null, $filePath = null, $fileIcon = null)
    {
        $fileManageMent = $id ? FileManagement::find($id) : new FileManagement;
        switch($action)
        {
            case 'delete':
                $fileManageMent->delete();
                return $fileManageMent;
                break;
        }
        $fileManageMent->user_id = $userId;
        $fileManageMent->deal_id = $dealId;
        $fileManageMent->file_name = $fileName;
        $fileManageMent->file_size = $fileSize;
        $fileManageMent->file_location = $filePath;
        $fileManageMent->file_icon = $fileIcon;
        $fileManageMent->date = date("Y-m-d H:i:s");

        $fileManageMent->save();

        return $fileManageMent;
    }
}
