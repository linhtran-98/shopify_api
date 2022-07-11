@extends('admin.main')
@section('content')
    @include('admin.alert')
    <form action="{{ route('products.update', $product->product_id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="image">Trạng thái</label>
                <select name="status" class="form-control">
                    @php
                        $status = ['pending', 'approve', 'reject'];
                    @endphp
                    @foreach ($status as $item)
                        @if ($item == $product->status)
                            <option selected value="{{$item}}">{{$item}}</option>
                        @else
                            <option value="{{$item}}">{{$item}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="name">Tên sản phẩm</label>
                <input type="text" name="name" class="form-control" id="name" value="{{ $product->name }}" placeholder="Enter name...">
            </div>

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" class="form-control" id="title" value="{{ $product->title }}" placeholder="Enter title...">
            </div>
            
            <div class="form-group">
                <label for="price">Giá</label>
                <input type="number" name="price" value="{{ $product->price }}" class="form-control" id="price">
            </div>
            
            <div class="form-group">
                <label for="image">Hình ảnh</label>
                <input type="file" name="image"  class="form-control" id="image">
                <img class="mt-3" id="product_img" src="{{ asset('uploads/'.$product->image) }}" width="100px " alt="" srcset="">
                <input class="mt-3" hidden name="before_image" value="{{ $product->image }}">
            </div>

            <div class="form-group">
                <label for="ckeditor_des">Chi tiết</label>
                <textarea class="form-control" id="ckeditor_des" name="description">{{ $product->description }}</textarea>
            </div>

            <div class="form-group">
                <select name="category" id="" class="form-control">
                    @foreach ($category as $item => $cate)
                        @if ($cate->id == $product->category_id)
                            <option selected disabled value="{{$cate->id}}">{{$cate->name}}</option>
                        @endif
                            <option value="{{$cate->id}}">{{$cate->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="submit" class="btn" style="background-image: linear-gradient(to right, rgb(205, 240, 234), rgb(249, 249, 249), rgb(246, 198, 234), rgb(250, 244, 183));">Sửa sản phẩm</button>
        </div>
    </form>
@endsection