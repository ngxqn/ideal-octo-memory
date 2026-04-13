# Các Giả Định Thiết Kế Cơ Sở Dữ Liệu (DB Assumptions)

Trước khi tiến hành vẽ sơ đồ ERD chi tiết, danh sách dưới đây liệt kê các giả định logic nghiệp vụ (Business Logic Assumptions) để xử lý các luồng dữ liệu phức tạp trong hệ thống. File này đóng vai trò xác nhận (Confirm) các luật chơi với Product Owner (Giáo viên / Người ra đề).

---

## 1. Dữ Liệu Giá Cả (Pricing Storage) & Lịch Sử

**Vấn đề:** Giá nhập khẩu bình quân thay đổi liên tục sau mỗi kiện hàng nhập. Giá bán cũng biến động theo % lợi nhuận tại từng thời điểm. Nếu sửa giá hiện tại của sản phẩm, các đơn hàng / phiếu nhập đã phát sinh trong quá khứ có bị tính toán sai tiền?

**Giả định & Giải pháp (Snapshot Pattern):**
- Trong bảng `orders` (Đơn đặt hàng) / `order_details` (Chi tiết đơn hàng): Tại thời điểm khách hàng ấn "Đặt hàng", toàn bộ giá bán hiện hành lúc đó sẽ được **lưu cứng (snapshot)** vào dòng `order_details`. Giá này hoàn toàn độc lập với bảng `products`. Dù Admin có lên giá sản phẩm sau đó thì hóa đơn cũ của khách vẫn không bị ảnh hưởng.
- Trong bảng `goods_receipts` (Phiếu nhập) / `goods_receipt_details` (Chi tiết phiếu nhập): Giá nhập của lô hàng cũng được **lưu cứng** tương tự.
- Bảng `products` sẽ lưu: `base_price` (giá nhập bình quân hiện tại theo WAC), `profit_margin` (% lợi nhuận), và `sell_price` (giá bán tính từ giá bình quân). Các con số này chỉ mô tả **trạng thái hiện tại** của sản phẩm. `sell_price` được triển khai dưới dạng **GENERATED COLUMN** (MySQL Stored). **Application Layer không được `UPDATE sell_price` trực tiếp.** Lịch sử ghi nhận giá trị biến đổi Tồn Kho (WAC) sẽ được lưu vết kèm `unit_price` trên hệ thống bảng `inventory_logs`.

---

## 2. Cơ Chế Ledger & Quản Lý Tồn Kho (Inventory Ledger)

**Vấn đề:** Đảm bảo tính chính xác tuyệt đối của tồn kho hiện tại để không bán lố, đồng thời có khả năng truy xuất trạng thái tồn kho tại bất kỳ thời điểm nào trong quá khứ (Historical Inventory).

**Giả định & Giải pháp (Full Ledger Pattern):**

### 2.1. Phân định vai trò dữ liệu
Hệ thống sử dụng mô hình kết hợp giữa **Cache Hiệu Năng** và **Sổ Cái Ledger**:
- **Bảng `products.stock_quantity`** lưu tồn kho hiện tại để phục vụ kiểm tra realtime (operational stock).
- **Bảng `inventory_logs`** là sổ cái lịch sử, dùng để audit và tái cấu trúc tồn kho tại mọi thời điểm trong quá khứ (inventory ledger).

### 2.2. Quy ước "First Inbound"
Để đảm bảo Tồn kho luôn đi đôi với một Giá nhập nhất quán (nguồn gốc dữ liệu tài chính chính xác), hệ thống thống nhất triệt tiêu hoàn toàn khả năng gán trị số đầu kì ảo:
- Khi một sản phẩm được tạo mới, nó sẽ tự động bị giam ở: `stock_quantity = 0` và `base_price = 0`. DB không ghi thêm bất kỳ `inventory_logs` tạm nào.
- Thông số chỉ có thể được nhảy lên khi có phát sinh **Nhập Hàng Lần Đầu (First Goods Receipt)**. Phiếu nhập này đảm nhận việc tạo dòng Ledger đầu tiên và châm nhiên liệu (giá, tồn kho) cho Hệ thống WAC (Weighted Average Cost).

### 2.3. Nguyên tắc Đồng bộ & Transaction
- **Nguyên tắc cập nhật:** Mọi thao tác làm thay đổi tồn kho (Tạo đơn, Hủy đơn, Nhập hàng) bắt buộc phải cập nhật **đồng thời cả 2 nơi**: vừa `UPDATE stock_quantity` (Cache) vừa `INSERT` dòng mới vào `inventory_logs` (Ledger).
- **Tính nhất quán:** Việc cập nhật này phải được bọc trong một **Database Transaction**. Chúng ta không sử dụng `inventory_logs` để ghi đè ngược lại `stock_quantity` trong vận hành bình thường; ledger chỉ dùng để kiểm tra/đối soát hoặc audit khi cần.

### 2.4. Công thức tính Tồn kho tại thời điểm T
Để biết tồn kho sản phẩm X tại thời điểm T, hệ thống thực hiện truy vấn duy nhất trên Ledger:
```sql
SELECT SUM(change_amount)
FROM inventory_logs
WHERE product_id = X AND created_at <= T;
```
- **Lưu ý:** Kết quả `SUM` sẽ phản ánh đúng tổng lượng hàng tồn từ lúc khai sinh đến thời điểm T mà không cần cộng thêm bất kỳ tham số nào khác. Tương tự, nếu muốn tính Giá Vốn WAC, chúng ta sẽ lật lịch sử để tái hiện giá theo lượng nhập xuất.

### 2.5. Luồng Tồn Kho (Inventory Flow)
- **Khi Khách tạo đơn (`Pending`):** Trừ trực tiếp vào `products.stock_quantity` **ngay lập tức** và ghi `inventory_logs` (số âm) để giữ chỗ (reservation) hàng hóa, tránh bán lố.
- **Khi Đơn bị hủy (`Cancelled`):** Hệ thống tự động truy xuất lại số lượng trong đơn đó, cộng trả lại vào `products.stock_quantity` và sinh thêm một dòng `inventory_logs` mới (số dương) vào lịch sử để phản ánh việc hoàn kho.
- **Guard chống hủy nhiều lần (C1 — triển khai ở Application Layer khi migrate PHP):** Trước khi thực hiện bất kỳ logic hoàn kho nào, service bắt buộc phải: (1) Lock row với `SELECT ... FOR UPDATE` để tránh race condition, (2) Kiểm tra `orders.status` còn trong `['pending', 'confirmed']` — nếu đã là `cancelled` hoặc `delivered` thì ném exception, không thực hiện hoàn kho.
- **Lưu ý về Reservation Timeout:** Hệ thống **không xử lý timeout tự động hủy đơn**. Admin sẽ định kỳ kiểm tra và hủy các đơn `Pending` quá hạn thủ công thông qua Dashboard.

---

## 3. Quản Lý Địa Chỉ Khách Hàng (Address Handling)

**Vấn đề:** Khách hàng có 1 địa chỉ theo Account đăng ký, nhưng khi Check-out có quyền nhập địa chỉ mới cho *người nhận khác*. Nếu lưu trực tiếp ở bảng Account thì rác data, hoặc không biết đơn này gửi đi đâu.

**Giả định & Giải pháp:**
- Bảng `users` (Người dùng) lưu thông tin liên hệ và địa chỉ **mặc định**: `address` (số nhà, đường/phố), `commune` (phường/xã/đặc khu), `city` (tỉnh/thành).
- Bảng `orders` (Đơn đặt hàng) lưu **toàn bộ thông tin người nhận thực tế** dưới dạng Record cố định (Snapshot): `receiver_name`, `receiver_phone`, `shipping_address` (số nhà, đường/phố), `shipping_commune` (phường/xã/đặc khu), `shipping_city` (tỉnh/thành).
- Nếu khách chọn "Nhận tại địa chỉ mặc định", lấy từ `users` copy qua `orders`.
- Nếu khách nhập "Nhận sinh mới", lưu thẳng vào `orders` mà không cần ghi đè địa chỉ mặc định trong `users`.

---

## 4. Mô Hành Cập Nhật Đơn Hàng (Order State Machine)

- **Flow 1 Chiều:** `Pending` (Chưa xử lý) $\to$ `Confirmed` (Đã xác nhận) $\to$ `Delivered` (Thành công).
- **Hủy (Cancelled):** Từ `Pending` hoặc `Confirmed` đều có thể nhảy ra nhánh `Cancelled` (gây ra logic Hoàn tồn kho như trình bày ở trên). Nhưng từ `Delivered` và `Cancelled` thì KHÔNG đổi đi đâu được nữa.

---

## 5. Xử Lý Tương Tranh (Concurrency) đối với Giá Nhập Bình Quân

**Vấn đề:** Nếu nhiều admin cùng thao tác tạo 2 phiếu nhập cùng lúc, công thức chia trung bình giá trị % và tồn kho có thể bị Race Condition (Ghi đè sai lệch dữ liệu).

**Giả định & Giải pháp (Design Simplification):**
- Hệ thống **không xử lý Concurrent Updates** cho logic tính giá trung bình.
- Giả định rằng hệ thống cho đồ án chỉ có **1 Admin thao tác nhập hàng tại một thời điểm**.
- Đây là một quyết định đơn giản hóa có chủ đích (design simplification for assignment) để không phải áp dụng các cơ chế lock phức tạp (Pessimistic / Optimistic Locking) mà vẫn đáp ứng hoàn hảo yêu cầu tính giá nhập bình quân.

---

## 6. Chiến Lược Xóa Dữ Liệu (Soft Delete Behavior)

**Vấn đề:** Yêu cầu phân định "chưa phát sinh giao dịch thì xóa hẳn, có giao dịch thì ẩn". 

**Giả định & Giải pháp (Soft-Delete Check):**
- Cả `products` (Sản phẩm) và `categories` (Danh mục) đều có cột `is_hidden` (Boolean).
- **Hoạt động Độc lập (Không Cascade):** Trạng thái `is_hidden` của bảng Category và bảng Product hoạt động hoàn toàn độc lập, không tự động trigger ẩn lan truyền từ Category xuống Product trong Database.
- **Ranh giới kiểm tra (Delete Check):**
  - **Sản phẩm (`products`):** Bị coi là "đã phát sinh nghiệp vụ" NẾU có bất kỳ record nào trong `inventory_logs` với `reference_type != 'product_init'`.
  - **Danh mục (`categories`):** Bị coi là "đang được sử dụng" NẾU có ít nhất 1 Sản phẩm (`products`) **đang ở trạng thái hiển thị (`is_hidden = 0`)** đang tham chiếu tới nó.
- Khi Admin bấm Xóa:
  - **Đối với Sản phẩm (`products`):**
    - Chưa từng có Log giao dịch: Chạy lệnh `DELETE FROM ... WHERE id = X` (Xóa Cứng).
    - Đã có Log giao dịch: Chạy lệnh `UPDATE ... SET is_hidden = 1 WHERE id = X` (Ẩn đi).
  - **Đối với Danh mục (`categories`):**
    - Không còn sản phẩm nào tham chiếu tới: Chạy lệnh `DELETE FROM ... WHERE id = X` (Xóa Cứng).
    - Vẫn còn sản phẩm tham chiếu tới: Chạy lệnh `UPDATE ... SET is_hidden = 1 WHERE id = X` (Ẩn đi).
- **Điều Kiện Hiển Thị Frontend (End-User):**
  - Để một Sản Phẩm hiển thị ra ngoài cửa hàng cho khách thấy, nó phải thỏa mãn cả 2 điều kiện: `products.is_hidden = 0` **VÀ** `categories.is_hidden = 0` (Dựa trên câu lệnh `JOIN`).
- **Liên Đới Ẩn/Hiện Categories:**
  - Nếu một Category bị đổi thành Ẩn (`is_hidden = 1`), các Product bên trong cấu trúc Dữ liệu **KHÔNG bị đổi** cột `is_hidden`. Tuy nhiên, do điều kiện hiển thị bị rớt (`categories.is_hidden = 1`), các Product đó tự động bị **ẩn khỏi UI**.
  - Khi Category đó được hiển thị lại (Restore/Unhide), các Product bên trong sẽ tự động nhảy hiển thị lại với điều kiện bản thân status của `products.is_hidden` lúc đó phải là 0.

---

## 7. Lọc Theo Phường/Xã/Đặc Khu và Tỉnh/Thành Của Đơn Hàng

**Giả định & Giải pháp:**
- Trong `orders`, địa chỉ sẽ không phải 1 đoạn string gõ tay ngẫu nhiên hoàn toàn. `shipping_commune` (phường/xã/đặc khu) và `shipping_city` (tỉnh/thành) cần được lưu ở một cột text riêng biệt (hoặc ít nhất yêu cầu end-user nhập tách ô Input) thì Database mới `ORDER BY/GROUP BY` chính xác được khu vực giao hàng.

---

## 8. Quản Lý Giỏ Hàng (Cart Handling)

**Giả định & Giải pháp:**
- Khách hàng bắt buộc đăng nhập để dùng giỏ hàng, do đó giỏ hàng sẽ được lưu trực tiếp vào CSDL thay vì LocalStorage để duy trì quyền truy cập xuyên suốt các thiết bị.
- **Ràng buộc Active Cart:** Mỗi User (`users`) chỉ được phép có **tối đa 1 giỏ hàng (`carts`) đang active** tại một thời điểm (Ràng buộc UNIQUE KEY trên `user_id`).
- **Không ảnh hưởng tồn kho:** Sản phẩm nằm trong giỏ hàng (`cart_items`) hoàn toàn không làm trừ hay khóa tồn kho (`products.stock_quantity`). Tồn kho chỉ thực sự bị trừ khi Request biến thành `orders` (trạng thái Pending).
- Khi đặt hàng, dữ liệu từ `cart_items` sẽ được dọn đi và chép vào `order_details`.

---

## 9. Ràng Buộc Toàn Vẹn Dữ Liệu Tầng DB (DB-Level Constraints)

**Vấn đề:** Một số rule nghiệp vụ quan trọng không nên chỉ enforce ở Application Layer, vì nếu có bug code hoặc thao tác thẳng vào DB thì dữ liệu có thể rơi vào trạng thái bất hợp lệ.

**Giải pháp:** Sử dụng MySQL CHECK Constraints (hỗ trợ từ MySQL 8.0.16+) để tạo lớp bảo vệ thứ hai tầng DB:

| Bảng | Constraint | Rule |
|---|---|---|
| `products` | `chk_products_stock_non_negative` | `stock_quantity >= 0` |
| `cart_items` | `chk_cart_items_qty` | `quantity >= 1` |
| `order_details` | `chk_order_details_qty` | `quantity >= 1` |
| `order_details` | `chk_order_details_price` | `unit_price >= 0` (cho phép = 0 nếu khuyến mãi 100%) |
| `goods_receipt_details` | `chk_grd_qty` | `quantity >= 1` |
| `goods_receipt_details` | `chk_grd_price` | `import_price > 0` (giá nhập phải dương) |
| `inventory_logs` | `chk_inventory_change_nonzero` | `change_amount != 0` (log biến động phải có ý nghĩa) |
