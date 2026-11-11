@extends('layouts.app')

@section('title', 'Đăng ký - AIStudy')

@section('content')

<div class="auth-page">

    <div class="auth-card">

        <div class="auth-header">

            <div class="auth-logo">
                AI
            </div>

            <h1>Tạo tài khoản</h1>

            <p>
                Bắt đầu hành trình học tập cá nhân hóa với AIStudy.
            </p>

        </div>


        @if($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            action="{{ route('register') }}"
            method="POST"
        >

            @csrf


            <div class="form-group">

                <label for="name">
                    Họ và tên
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    value="{{ old('name') }}"
                    placeholder="Nhập họ và tên"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email') }}"
                    placeholder="example@email.com"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Mật khẩu
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Tối thiểu 8 ký tự"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password_confirmation">
                    Xác nhận mật khẩu
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="Nhập lại mật khẩu"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-auth"
            >
                Tạo tài khoản
            </button>

        </form>


        <div class="auth-footer">

            <span>Đã có tài khoản?</span>

            <a href="{{ route('login') }}">
                Đăng nhập
            </a>

        </div>

    </div>

</div>

@endsection