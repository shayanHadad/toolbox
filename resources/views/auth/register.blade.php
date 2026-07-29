@extends('layouts.app')

@section('title', 'جعبه‌ابزار | ثبت‌نام')

<link rel="stylesheet" href="{{ asset('css/register.css') }}">


@section('content')

<div class="register-page">

    <div class="login-box">

        <h1>ثبت نام</h1>

        <form action="{{ route('register.store') }}" method="POST">

            @csrf

            {{-- نام کاربری --}}
            <div class="user-box">
                <input type="text" name="username" value="{{ old('username') }}" required>
                <label>نام کاربری</label>
                @error('username')
                <span class="text-danger">
                    {{ $message }}
                </span>
                @enderror
            </div>
            {{-- نام  --}}
            <div class="user-box">
                <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                <label>نام</label>
            </div>

            {{-- شماره موبایل --}}
            <div class="user-box">
                <input type="tel" name="contact_number" value="{{ old('contact_number') }}" required>
                <label>شماره موبایل</label>
                @error('contact_number')
                <span class="text-danger">
                    {{ $message }}
                </span>
                @enderror

            </div>

            {{-- نقش --}}
            <div class="user-box">

                <select name="role" required>

                    <option value="">انتخاب نقش</option>

                    <option value="1" {{ old('role') == 1 ? 'selected' : '' }}>
                        مشتری
                    </option>

                    <option value="2" {{ old('role') == 2 ? 'selected' : '' }}>
                        متخصص
                    </option>

                    <!-- <option value="3" {{ old('role') == 3 ? 'selected' : '' }}>
                            شرکت
                        </option> -->

                </select>

                @error('role')
                <span class="text-danger">{{ $message }}</span>
                @enderror

            </div>

            {{-- تاریخ تولد --}}
            <div class="user-box">
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}">

                @error('date_of_birth')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- رمز عبور --}}
            <div class="user-box">
                <input type="password" name="password" required>
                <label>رمز عبور</label>

                @error('password')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button class="btn-submit" type="submit">

                <span></span>
                <span></span>
                <span></span>
                <span></span>
                ثبت‌نام
            </button>

        </form>

        <div class="bottom-text">

            قبلاً ثبت نام کرده‌اید؟

            <a class="a2" href="{{ route('login') }}">
                ورود
            </a>

        </div>

    </div>

</div>

@endsection

<script src="{{ asset('js/register.js') }}"></script>