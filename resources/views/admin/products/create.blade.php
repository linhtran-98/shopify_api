@extends('admin.main')
@section('content')
    @include('admin.alert')
    <form action="{{route('product.store')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="title">Tên sản phẩm</label>
                <input type="text" required name="title" class="form-control" id="title" placeholder="Enter name...">
            </div>
            
            <div class="form-group">
                <label for="price">Giá</label>
                <input type="number" min="1000" max="100000000" required name="price" class="form-control" id="price">
            </div>
            
            <div class="form-group">
                <label for="image">Hình ảnh</label>
                <input type="file" name="image" class="form-control" id="image">
                <img src="" style="padding: 5px;" width="100px" alt="" id="product_img">
            </div>

            <div class="form-group">
                <label for="ckeditor_des">Chi tiết</label>
                <textarea class="form-control" required id="ckeditor_des" name="description"></textarea>
                <script>
                    CKEDITOR.replace( 'ckeditor_des' );
            </script>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="submit" class="btn font-weight-bold" style="background-image: linear-gradient(to right, rgb(251, 244, 251), rgb(129, 190, 231), rgb(226, 236, 137));">Tạo sản phẩm</button>
        </div>
    </form>
@endsection