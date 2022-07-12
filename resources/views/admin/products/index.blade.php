@extends('admin.main')
@section('content')
@include('admin.alert')
<div class="card">
    <div class="card-body">
      <table class="table table-bordered">
        <thead>
          <tr class="text-center">
            <th>Hình ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Giá</th>
            <th colspan="3">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($products as $key => $value)
            <tr class="text-center">
              @if (!is_null($value->image))
                <td><a target="_blank" href="{{$value->image}}"><img src="{{$value->image}}" width="100px" alt=""></a></td>
              @else
                <td><a target="_blank" href="{{asset('uploads/images.png')}}"><img src="{{asset('uploads/images.png')}}" width="100px" alt="empty_image"></a></td>
              @endif
              <td class="text-left">{{ $value->title }}</td>
              <td>{{ $value->price }}</td>
              <td class="text-center"><a class="btn btn-block btn-outline-primary" href="{{route('product.edit', $value->id)}}">Sửa</a></td>
              <td class="text-center">
                <form action="{{route('product.delete')}}" method="post">
                  @csrf
                  <input type="hidden" name="product_id" value="{{$value->id}}">
                  <button type="submit" class="btn btn-block btn-outline-danger" onclick="return confirm('Do you want xóa sản phẩm này?')">Xóa</button>
                </form>
              </td>
            </tr>
            @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
      <div class="pagination pagination-sm m-0 float-right">
        {{-- {{ $products->appends($_GET)->links() }} --}}
      </div>
    </div>
  </div>
@endsection