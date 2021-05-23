<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Util\ScribbleUtil;
use Illuminate\Http\Request;

class ScribbleController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.scribble.index',[
            'title' => "Scribble",
            ]
        ); 
    }

    public function scribble(Request $request){
        return ScribbleUtil::scribble();
    }
    public function editScribble(Request $request){
        return ScribbleUtil::editScribble($request);
    }
    public function deleteScribble(Request $request){
        return ScribbleUtil::deleteScribble($request);
    }
    public function createScribble(Request $request){
        return ScribbleUtil::createScribble($request);
    }
    public function updateScribble(Request $request){
        return ScribbleUtil::updateScribble($request);
    }
    public function sortScribble(Request $request){
        return ScribbleUtil::sortScribble($request);
    }
    public function saveCategory(Request $request){
        return ScribbleUtil::saveCategory($request);
    }
    public function editCategory(Request $request){
        return ScribbleUtil::editCategory($request);
    }
}
