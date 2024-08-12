<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index(){
        return view("api_docs");
    }

    public function store(Request $request){
        $status = Post::create($request->all());
        if($status){
            return response()->json(["status"=> "success","message"=> "Post Created Successfully","response"=>200]);
        }else{
            return response()->json(["status"=> "error","message"=> "Post creation failed","response"=>400]);
        }
    }
    public function allPost(){
        $data['records'] = Post::all();
        if(!empty($data['records'])){
            return response()->json(['status'=> 'success','message'=> 'Data Fetched Successfully','data'=>$data['records'],'status_code'=>200]);
        }else{
            return response()->json(['status'=> 'error','message'=> 'No Data Found','status_code'=>400]);
        }
    }
    public function show($id){
        $post = Post::find($id);
        if($post)
            return response()->json(['status'=>'success','message'=>"Single Item fetched successfully","data"=>$post,"status_code"=> 200]);
        else
            return response()->json(['status'=>'error','message'=>"No data found","status_code"=>400]);
    }
    public function update(Request $request, $id){
        $post = Post::find($id);
        if(!$post)
            return response()->json(["status"=> "error","message"=> "No Such data found","status_code"=>400]);
        $status = $post->update($request->all());
        if($status)
            return response()->json(["status"=> $status,"message"=> "Updated successfully","status_code"=>200]);
        else
            return response()->json(["status"=> $status,"message"=> "update failed","status_code"=>400]);
    }
}
