@extends('admin.main')
@section('content')
  <form action="{{route('product.update')}}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="card-body">
          <div class="form-group">
              <label for="title">Tên sản phẩm</label>
              <input value="{{$product->title}}" type="text" required name="title" class="form-control" id="title" placeholder="Enter name...">
          </div>
          
          <div class="form-group">
              <label for="price">Giá</label>
              <input value="{{$product->price}}" type="number" min="1000" max="100000000" required name="price" class="form-control" id="price">
          </div>
          
          <div class="form-group">
              <label for="image">Hình ảnh</label>
              <input type="file" name="image" class="form-control" id="image">
              <input value="{{$product->image}}" type="hidden" name="old_image" class="form-control" id="image">
              @if (!is_null($product->image))
                <td>
                  <img src="{{$product->image}}" style="padding: 5px;" width="100px" alt="" id="product_img">
                </td>
              @else
                <td>
                  <img src="{{asset('uploads/images.png')}}" style="padding: 5px;" width="100px" alt="" id="product_img">
                </td>
              @endif
          </div>
          <div class="form-group">
              <label for="ckeditor_des">Chi tiết</label>
              <textarea class="form-control" required id="ckeditor_des" name="description">{{$product->description}}</textarea>
              <script>
                  CKEDITOR.replace( 'ckeditor_des' );
          </script>
          </div>
      </div>
      <!-- /.card-body -->
      <div class="card-footer">
          <button type="submit" class="btn font-weight-bold" style="background-image: linear-gradient(to right, rgb(251, 244, 251), rgb(129, 190, 231), rgb(226, 236, 137));">Sửa sản phẩm</button>
      </div>
  </form>
@endsection