@extends('layouts.app')

@section('title', 'ورود')

<link rel="stylesheet" href="{{ asset('css/login.css') }}">

@section('content')
<div class="register-page">
    <div class="login-box">
        <h1>ورود</h1>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="user-box">
                <input type="text" name="login" value="{{ old('login') }}" required autofocus>
                <label>نام کاربری یا شماره تماس</label>
                @error('login')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="user-box">
                <input type="password" name="password" required>
                <label>رمز عبور</label>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button class="btn-submit" type="submit">
                ورود
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </button>
        </form>

        <p class="bottom-text">
            حساب کاربری ندارید؟
            <a class="a2" href="{{ route('register') }}">ثبت‌نام</a>
        </p>
    </div>
</div>
@endsection