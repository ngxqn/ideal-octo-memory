@extends('layouts.app')

@section('title', 'Giới thiệu - Morico Bakery')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/about.css') }}">
@endsection

@section('content')
    <!-- NỘI DUNG GIỚI THIỆU -->
    <main>
        <h1 class="page-title">Về Tiệm bánh Morico</h1>

        <div class="intro-header">
            <p>&ldquo;Morico &ndash; Hương vị truyền thống, phong cách hiện đại&rdquo;</p>
        </div>

        <div class="about-container">
            <div class="about-section">
                <img src="{{ asset('assets/image/about/banh-trung-thu.png') }}" alt="Giới thiệu Morico" class="about-image">
                <div class="about-text">
                    <p>
                        Với hơn một thập kỷ gìn giữ hương vị truyền thống, Morico mang đến những chiếc bánh Trung thu
                        được làm thủ công tinh tế &ndash; tươi ngon, an toàn và đậm đà hương vị đặc trưng.
                    </p>
                    <p>
                        Mỗi chiếc bánh không chỉ là món quà ngọt ngào, mà còn là lời chúc tròn đầy, ấm áp gửi đến người
                        thân yêu trong mùa trăng sum họp.
                    </p>
                </div>
            </div>

            <h2 class="main-section-title">Trọn vẹn vị yêu thương</h2>

            <div class="about-section reverse">
                <img src="{{ asset('assets/image/about/hopqua.png') }}" alt="Hộp quà Morico" class="about-image">
                <div class="about-text">
                    <p>
                        Bánh Trung thu Morico là biểu tượng của sự giao thoa tinh tế giữa truyền thống ẩm thực Việt Nam
                        và phong cách hiện đại, mang đến trải nghiệm thưởng thức trọn vẹn, đầy ý nghĩa cho mọi gia đình
                        trong mùa
                        trăng rằm. Morico không chỉ là một sản phẩm bánh, mà còn là món quà tinh thần, thể hiện sự quan
                        tâm, gắn
                        kết và sẻ chia yêu thương giữa người với người.
                    </p>
                </div>
            </div>

            <h2 class="main-section-title">Tinh xảo trong từng chi tiết</h2>

            <div class="about-section">
                <img src="{{ asset('assets/image/about/banhtrungthu.png') }}" alt="Bánh Trung Thu Morico" class="about-image">
                <div class="about-text">
                    <p>
                        Dòng bánh truyền thống của Morico mang đậm bản sắc văn hóa Việt Nam, là kết tinh của những giá
                        trị lâu đời, kết hợp cùng nguồn nguyên liệu tự nhiên tinh túy. Với quy trình sản xuất nghiêm
                        ngặt và bàn tay tài hoa của những nghệ nhân bánh, mỗi sản phẩm ra đời đều đạt đến sự hoàn hảo về
                        cả hương vị lẫn hình thức.
                    </p>
                </div>
            </div>
        </div>
    </main>
@endsection
