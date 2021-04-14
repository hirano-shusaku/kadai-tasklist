<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

class TasksController extends Controller
{
    // getでtasks/にアクセスされた場合の「一覧表示処理」
    public function index()
    {
        $tasks = []; // 空のタスク追加＝タスクは存在する
       if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザのタスクを取得
            $tasks = $user->tasks()->paginate(10);
       }
       
        // タスク一覧ビューでそれを表示
        return view('tasks.index', [
            'tasks' => $tasks,
        ]);
       
    }

    // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {
        $task = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    // postでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
             'content' => 'required|max:255'
        ]);
        // 認証済みユーザ（閲覧者）のtaskとして作成（リクエストされた値をもとに作成）
        
       /*
        $request->user()->tasks()->create([
        'content' => '???',
        'status'  => '???',   // user_id以外のカラムに何を保存するかを指定する
        ]);
      */
        
         
        $task = new Task;
        $task->status = $request->status;    // 追加
        $task->user_id = \Auth::id();        //Auth::id() ※ログインユーザーのid取得)追加
        $task->content = $request->content;
        $task->save();
          

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    // getでtasks/（任意のid）にアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);        
if(\Auth::id()===($task->user_id)){   //ログインユーザーidとタスク登録idが同じ場合


        // タスク詳細ビューでそれを表示
        return view('tasks.show', [
            'task' => $task,
        ]);
}else{
    return redirect('/');
}
    }

    // getでtasks/（任意のid）/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        
if(\Auth::id()===($task->user_id)){   //ログインユーザーidとタスク登録idが同じ場合

        // タスク編集ビューでそれを表示
        return view('tasks.edit', [
            'task' => $task,
        ]);
}else{
    return redirect('/');
}        
    }

    // putまたはpatchでtasks/（任意のid）にアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
         // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        
if(\Auth::id()===($task->user_id)){   //ログインユーザーidとタスク登録idが同じ場合        
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
             'content' => 'required|max:255'
        ]);
        

        // タスクを更新
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
}else{
    return redirect('/');
}                
    }

    // deleteでtasks/（任意のid）にアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
if(\Auth::id()===($task->user_id)){   //ログインユーザーidとタスク登録idが同じ場合        

        // タスクを削除
        $task->delete();

        // トップページへリダイレクトさせる
        return redirect('/');
}else{
    return redirect('/');
}              
    }
}