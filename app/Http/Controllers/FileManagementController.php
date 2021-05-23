<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//use Response;


use App\Http\Controllers\Util\FileUtil;
use App\Http\Controllers\Util\DealUtil;


class FileManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        
    }

    public function getList(Request $request)
    {
        $dealId = $request->input('deal_id');        
        if (empty($dealId)) {
            $dealId = $request->route('deal_id');
        }
        $files = DealUtil::getFilesByDealId($dealId);        
        return view('pages.deal.file.list')
            ->with('files',$files)
            ->render();
    }

    public function uploadFile(Request $request)
    {
        if($request->hasfile('file') && $request->input('deal_id')) 
        {             
            return $path =  FileUtil::uploadFile('Deal.Pdf',  $request->input('deal_id'), $request->file('file'));
        }
        else
            return null;
    }

    public function downLoadFile(Request $request, $fileId)
    {
        return FileUtil::downLoadFile('Deal.Pdf', $fileId);
    }

    public function deleteFile(Request $request, $fileId)
    {
        return FileUtil::deleteFile('Deal.Pdf', $fileId);
    }
}
