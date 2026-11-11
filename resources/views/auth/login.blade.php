@extends('layouts.app')

@section('title', 'Đăng nhập - AIStudy')

@section('content')

<div class="auth-page">

    <div class="auth-card">

        <div class="auth-header">
            <div class="auth-logo">AI</div>

            <h1>Chào mừng trở lại</h1>

            <p>
                Đăng nhập để tiếp tục hành trình học tập của bạn.
            </p>
        </div>


        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif


        <form
            action="{{ route('login') }}"
            method="POST"
        >

            @csrf


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
                    placeholder="Nhập email của bạn"
                    required
                    autofocus
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
                    placeholder="Nhập mật khẩu"
                    required
                >

            </div>


            <div class="auth-options">

                <label class="remember-me">

                    <input
                        type="checkbox"
                        name="remember"
                    >

                    <span>Ghi nhớ đăng nhập</span>

                </label>

            </div>


            <button
                type="submit"
                class="btn-auth"
            >
                Đăng nhập
            </button>

        </form>


        <div class="auth-footer">

            <span>Chưa có tài khoản?</span>

            <a href="{{ route('register') }}">
                Đăng ký ngay
            </a>

        </div>

    </div>

</div>

@endsection