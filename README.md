## Setup project tại source
- Ta cần nhập api & secret key của app vào file .env (Xem cách lấy cụ thể ở phần Setup project tại partner).

- Chạy ngrok cùng port với serve sau đó nhập ngrok url vào (NGROK_URL) tại .env

- Chạy php artisan serve & php artisan queue:work

## Setup project tại partner
Vì app không được phép install từ bên thứ 3 nên chúng ta cần thao tác từ giao diện partner của shopify.

[Đăng nhập vào partner của shopify](https://www.shopify.com/partners) với tài khoản: shopifytest4444@gmail.com, mật khẩu: 123456
<h2>Apps -> tên app (linhzz)</h2>
<h3>Ta cần cấu hình lại đường dẫn: App setup -> nhập ngrok url vào App URL và ngrok url/authen vào Allowed redirection URL(s)</h3>
<h3>Trở lại Overview -> Lấy api key và secret key nhập vào file .env (Cần khởi động lại serve khi thay đổi .env)</h3>
<h3>Sau đó tại Overview -> select store -> click vào store muốn install</h3>
<p>Trong quá trình load nếu bị lỗi 400 xin vui lòng reload lại trang web</p>