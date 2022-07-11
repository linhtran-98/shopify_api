<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- AdminLTE App -->
<script src="{{asset('/template/admin/dist/js/adminlte.min.js')}}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

<script>

  // $('#search').on('keyup',function(){
  //       $value = $(this).val();
  //       $.ajax({
  //           type: 'get',
  //           url: '{{ URL::to('search') }}',
  //           data: {
  //               'search': $value
  //           },
  //           success:function(data){
  //               $('#livesearch').html(data);
  //           }
  //       });
  //   })
  //   $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });

    $(function() {
        $('#image').change(function () {
        var reader = new FileReader();

        reader.onload = function (e) {
          // get loaded data and render thumbnail.
          document.getElementById("product_img").src = e.target.result;
        };
        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
      })
    });

  //   $(function() {
  //       $('#image_album').change(function () {
  //         var img = this.files;
  //         if(img.length === 1){
  //           var reader = new FileReader();
  //           reader.onload = function (e) {
  //             document.getElementById("album_product").src = e.target.result;
  //           };
  //           reader.readAsDataURL(img[0]);
  //         }
  //         else{
  //           for (let i = 0; i < img.length; i++) {
  //             var image = document.createElement('img');

  //             image.setAttribute('src', '');
  //             image.setAttribute('width', '100px');
  //             image.setAttribute('style', 'padding: 5px;');
  //             image.setAttribute('id', 'album_product'+i);
              
  //             var box = document.getElementById('box');
  //             box.appendChild(image);

  //             var reader = new FileReader();
  //             reader.onload = function (e) {
  //               // get loaded data and render thumbnail.
  //               document.getElementById("album_product"+i).src = e.target.result;
  //             };
  //             reader.readAsDataURL(img[i]);
  //           }
  //         }
  //     })
  //   });
</script>