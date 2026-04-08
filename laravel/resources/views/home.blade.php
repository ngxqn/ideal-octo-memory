@extends('layouts.app')

@section('title', 'Trang chủ - Morico Bakery')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/home.css') }}">
@endsection

@section('content')
    <!-- BANNER -->
    <section class="banner">
        <img src="{{ asset('assets/image/home/banner.png') }}" alt="Quà Trung thu Morico" class="banner-img">
        <div class="banner-text">
            <h1>Quà Trung thu</h1>
            <a href="{{ route('products.index') }}" class="btn">Khám phá ngay</a>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="about">
        <h2>Về Tiệm bánh Morico</h2>
        <p>
            Với hơn một thập kỷ gìn giữ hương vị truyền thống, Morico mang đến những chiếc bánh Trung thu được làm thủ
            công
            tinh tế &ndash; tươi ngon, an toàn và đậm đà hương vị đặc trưng.
            <br><br>
            Mỗi chiếc bánh không chỉ là món quà ngọt ngào, mà còn là lời chúc tròn đầy, ấm áp gửi đến người thân yêu
            trong mùa
            trăng sum họp.
            <br><br>
            Mỗi chiếc bánh là một món quà &ndash; tròn vị yêu thương gửi gắm đến gia đình bạn.
        </p>
    </section>
@endsection
