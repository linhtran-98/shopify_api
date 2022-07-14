<!DOCTYPE html>
<html lang="en">
<head>
<link rel="shortcut icon" type="image/png" href="./public/image/user_icon/android-chrome-512x512.png"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&public/display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css" integrity="sha512-IuO+tczf4J43RzbCMEFggCWW5JuX78IrCJRFFBoQEXNvGI6gkUw4OjuwMidiS4Lm9Q2lILzpJwZuMWuSEeT9UQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Shop Name</title>
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <h2 class="text-primary"><b>Enter your shop name</b></h2>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <form action="{{route('shopify')}}" method="GET">
          @csrf
            <div class="input-group mb-3">
              <input type="text" name="shop_name" maxlength="100" class="form-control" value="https://your-shop.myshopify.com">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-link"></span>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
</body>
</html>
