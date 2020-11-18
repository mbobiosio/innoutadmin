@extends('layouts.contentLayoutMaster')
{{-- title --}}
@section('title','All Posts')

{{-- vendor style --}}
@section('vendor-styles')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/css/tables/datatable/datatables.min.css')}}">
@endsection
{{-- page-styles --}}

@section('content')
<!-- Zero configuration table -->
<section id="basic-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Zero configuration</h4>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <p class="card-text">DataTables has most features enabled by default, so all you need to do to
                            use it with your own tables is to call the construction function: $().DataTable();.</p>
                        <div class="table-responsive">
                            <table class="table zero-configuration">
                                <thead>
                                    <tr>
                                        <th>N</th>
                                        <th>Author</th>
                                        <th>Title</th>
                                        <th>Publish</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($posts) > 0)
                                        @foreach($posts as $post)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$post->user->name}}</td>
                                            <td>{{$post->title}}</td>
                                            @if ($post->status == "Published")
                                                <td class="text-success">Published</td>
                                            @else
                                                <td class="text-danger">Draft</td>
                                            @endif
                                            <td>
                                                <div class="dropdown">
                                                    <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu">
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="{{ route('posts.edit', ['slug'=> $post->slug]) }}"><i class="bx bx-edit-alt mr-1"></i> edit</a>
                                                        <form method="POST"  action=" {{ route('posts.delete', ['id'=> $post->id]) }}">
                                                            @csrf 
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <button class="dropdown-item" type="submit">
                                                            <i class="bx bx-trash mr-1"></i> delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                <b>No Post. Create New Post!</b>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>N</th>
                                        <th>Author</th>
                                        <th>Title</th>
                                        <th>Publish</th>
                                        <th>Actions</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/ Zero configuration table -->
@endsection
{{-- vendor scripts --}}
@section('vendor-scripts')
<script src="{{asset('vendors/js/tables/datatable/datatables.min.js')}}"></script>
<script src="{{asset('vendors/js/tables/datatable/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('vendors/js/tables/datatable/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('vendors/js/tables/datatable/buttons.html5.min.js')}}"></script>
<script src="{{asset('vendors/js/tables/datatable/buttons.print.min.js')}}"></script>
<script src="{{asset('vendors/js/tables/datatable/buttons.bootstrap.min.js')}}"></script>
<script src="{{asset('vendors/js/tables/datatable/pdfmake.min.js')}}"></script>
<script src="{{asset('vendors/js/tables/datatable/vfs_fonts.js')}}"></script>
@endsection
{{-- page scripts --}}
@section('page-scripts')
<script src="{{asset('js/scripts/datatables/datatable.js')}}"></script>
@endsection