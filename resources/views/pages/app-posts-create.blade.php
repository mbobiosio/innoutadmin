@extends('layouts.contentLayoutMaster')
{{-- title --}}
@section('title','Form Layout')
@section('content')
<!-- Basic Vertical form layout section start -->
<section id="basic-vertical-layouts">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">{{ $edit ? 'Edit' : 'Create'}} Post</h4>
        </div>
        <div class="card-content">
          <div class="card-body">
            @if(session('success'))
              <div class="alert alert-success">
                  {{session('success')}}
              </div>
            @endif
            <form method="POST" action="{{ $edit ? route('posts.update', ['id'=> $post->id]) : route('posts.store')}}" enctype="multipart/form-data" class="form form-vertical">
              @csrf
              @if($edit)
                  {{ method_field("PUT") }}
              @endif
              <div class="form-body">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label for="title-vertical">Title</label>
                      <input type="text" id="title-vertical" class="form-control" name="title"
                      placeholder="Title" value="{{ $edit ? $post->title : old('title')}}">
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label for="body">Body</label>
                      <textarea class="form-control" id="body" rows="3" placeholder="Textarea" name="body">{{ $edit ? $post->body : old('body')}}</textarea>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label for="password-vertical">Tag</label>
                      <select class="form-control" id="basicSelect" name="tag">
                        @foreach ($tags as $tag)
                          <option @if($edit && $post->tag_id == $tag->id) {{'selected'}} @endif value="{{$tag->id}}">{{$tag->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label for="password-vertical">Cover Image</label>
                      <fieldset>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupFileAddon01">Upload</span>
                          </div>
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01" name="cover_image">
                            <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                          </div>
                        </div>
                      </fieldset>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-group">
                      <label for="status">Status</label>
                      <select class="form-control" id="status" name="status">
                        <option @if($edit && $post->status == "Published") {{'selected'}} @endif>Published</option>
                        <option @if($edit && $post->status == "Draft") {{'selected'}} @endif>Draft</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mr-1 mb-1">Submit</button>
                    <button type="reset" class="btn btn-light-secondary mr-1 mb-1">Reset</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- // Basic Vertical form layout section end -->
@endsection