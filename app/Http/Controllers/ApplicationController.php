<?php

namespace App\Http\Controllers;

use App\User;
use App\Message;
use App\Email;
use Illuminate\Http\Request;
use App\Services\UserRequestService;
use Illuminate\Support\Facades\Auth;

use App\Mail\EmailNotification;
use Illuminate\Support\Facades\Mail;

use App\Post;
use App\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    protected $userRequestService;

    public function __construct(UserRequestService $userRequestService)
    {
        $this->userRequestService = $userRequestService;
    }

    //email App
    public function emailApplication(){
        $pageConfigs = ['isContentSidebar' => true, 'bodyCustomClass' => 'email-application'];

        $userRequests = $this->userRequestService->index();

        if(Auth::user()->role != "Admin")
        {
          return redirect()->back();
        }

        return view('pages.app-email', ['pageConfigs' => $pageConfigs, 'userRequests'=> $userRequests]);
    }

    //email App show
    public function emailApplicationShow($email){
      $pageConfigs = ['isContentSidebar' => true, 'bodyCustomClass' => 'email-application'];

      $email = decodeId($email);
      $userRequest = $this->userRequestService->find($email);
      $userRequest['requests'] = json_decode($userRequest->requests);

      return view('pages.app-email-show', ['pageConfigs' => $pageConfigs, 'userRequest'=> $userRequest]);
  }

    // chat App
    public function chatApplication(){
        $pageConfigs = ['isContentSidebar' => true, 'bodyCustomClass' => 'chat-application'];

        $clients = User::where('id','!=',auth()->user()->id)->get();

        return view('pages.app-chat', ['pageConfigs' => $pageConfigs, 'clients' => $clients]);
    }

    // chat App show
    public function chatApplicationShow($user){
      $pageConfigs = ['isContentSidebar' => true, 'bodyCustomClass' => 'chat-application'];

      $auth_user = Auth::user()->id;

      // check if user and auth already have a message if not create
      if(Message::where(['sender_id'=> $auth_user, 'receiver_id'=> $user])->first()){
          // return response("found");
          $message_info = Message::where(['sender_id'=> $auth_user, 'receiver_id'=> $user])->first();
      }else if(Message::where(['sender_id'=> $user, 'receiver_id'=> $auth_user])->first()){
        // return response("found1");
        $message_info = Message::where(['sender_id'=> $user, 'receiver_id'=> $auth_user])->first();
      }
      else {
        // return response("not found");
        $message_info = Message::create(
          [
            'sender_id' => auth()->user()->id,
            'receiver_id' => $user
          ]
        );
      }
      $clients = User::where('id','!=',auth()->user()->id)->get();
      $user = User::where('id', $user)->first();

      return view('pages.app-chat-show', ['pageConfigs' => $pageConfigs, 'clients' => $clients, 'user' => $user, 'message_info' => $message_info ]);
    }

    // Todo App
    public function todoApplication(){
        $pageConfigs = ['isContentSidebar' => true, 'bodyCustomClass' => 'todo-application'];
        return view('pages.app-todo', ['pageConfigs' => $pageConfigs]);
    }
    // calendar App
    public function calendarApplication(){
        $pageConfigs = ['bodyCustomClass' => 'calendar-application'];
        return view('pages.app-calendar', ['pageConfigs' => $pageConfigs]);
    }
    // calendar App
    public function kanbanApplication(){
      $pageConfigs = ['isMenuCollapsed' => true];
        return view('pages.app-kanban',['pageConfigs'=>$pageConfigs]);
    }
     // invoice App
     public function invoiceListApplication(){
       
      return view('pages.app-invoice-list');
  }
   // invoice App
   public function invoiceApplication(){
    return view('pages.app-invoice');
  }
   // invoice edit App
   public function invoiceEditApplication(){
    return view('pages.app-invoice-edit');
  }
  // invoice add App
  public function invoiceAddApplication(){
    return view('pages.app-invoice-add');
  }
  // invoice add App
  public function fileManagerApplication(){
    $pageConfigs = ['isContentSidebar' => true, 'bodyCustomClass' => 'file-manager-application'];
    return view('pages.app-file-manager',['pageConfigs' => $pageConfigs]);
  }
  // Send email 
  public function sendEmail(Request $request)
  {
    $email = Email::create([
        'from' => Auth::user()->id,
        'to' => $request->to,
        'subject' => $request->subject,
        'message' => $request->message,
      ]);

    retry(5, function() use ($email){
      Mail::to($email->to)->send(new EmailNotification($email));
    }, 100);

    return response("success");
  }

  /**
   * Change Request Status 
   */
  public function changeRequestStatus(Request $request, $id)
  {
      $data = [
        "status" => $request->get('status')
    ];

    $userRequest = $this->userRequestService->update($id, $data);

    return redirect()->back();
  }

  //datatable
  public function dataTable(){
    $pageConfigs = ['pageHeader' => true];
    $breadcrumbs = [
      ["link" => "/", "name" => "Home"],["name" => "All Posts"]
    ];

    $posts = Post::orderBy('id', 'desc')->get();
    return view('pages.app-posts-all',['pageConfigs'=>$pageConfigs,'breadcrumbs'=>$breadcrumbs, 'posts'=>$posts]);
  }

   //Form Layout
   public function formLayout(){

    $pageConfigs = ['pageHeader' => true];

    $breadcrumbs = [
      ["link" => "/", "name" => "Home"],["link" => "#", "name" => "Forms"],["name" => "Create Post"]
    ];

    $edit = false;

    $tags = Tag::orderBy('name', 'asc')->get();
    return view('pages.app-posts-create',['pageConfigs'=>$pageConfigs,'breadcrumbs'=>$breadcrumbs, 'edit'=>$edit, 'tags'=>$tags]);
  }

  public function storePost(Request $request)
  {
        $rules = [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image|required',
            'tag' => 'required',
            'status' => 'required'
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['title'] = $request->title;
        $data['body'] = $request->body;
        $data['slug'] = str::slug($request->title, '-');
        $data['tag_id'] = $request->tag;
        $data['status'] = $request->status;
        $data['cover_image'] = $request->cover_image->store('');
        $data['user_id'] = auth()->user()->id;
        $data['views'] = 1;

        $post = Post::create($data);

        $success = "Post Created";
        return redirect()->back()->with(['success' => $success]);
  }

  //Form Layout
  public function editPost($slug){

    $pageConfigs = ['pageHeader' => true];

    $breadcrumbs = [
      ["link" => "/", "name" => "Home"],["link" => "#", "name" => "Forms"],["name" => "Edit Post"]
    ];

    $edit = true;

    $tags = Tag::orderBy('name', 'asc')->get();

    $post = Post::where('slug', $slug)->first();
    return view('pages.app-posts-create',['pageConfigs'=>$pageConfigs,'breadcrumbs'=>$breadcrumbs, 'edit'=>$edit, 'tags'=>$tags, 'post'=>$post]);
  }

  public function updatePost(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'tag' => 'required',
            'status' => 'required',
            'cover_image' => 'image'
        ]);

        if($request->has('title')){
            $post->title = $request->title;
            $post->slug = str::slug($request->title, '-');
        }

        if($request->has('body')) {
            $post->body = $request->body;
        }
        
        if($request->has('tag')) {
            $post->tag_id = $request->tag;
        }

        if($request->has('status')) {
          $post->status = $request->status;
      }
        
        if($request->hasFile('cover_image')) {
            $post->cover_image = $request->cover_image->store('');
        }
        $post->save();

        $success = "Post Updated";
        return redirect()->back()->with(['success' => $success]);
    }

  public function destroyPost($id)
  {
      $post = Post::find($id);
      // Storage::delete($post->cover_image);
      $post->delete();

      $success = "Post Deleted";
      return redirect()->back()->with(['success' => $success]);
  }
}
