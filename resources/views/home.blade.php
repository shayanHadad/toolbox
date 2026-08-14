@extends('layouts.app')

@section('title', 'خانه | جعبه‌ابزار')

@section('content')

<section class="hero">

    <div class="container hero-content">

        <span class="hero-eyebrow"><span class="dot"></span> همیشه همراه شما </span>

        <h1><em>جعبه‌ابزار</em>، شامل هر خدماتی که نیاز داری</h1>

        <p>یافتن سریع خدمات مورد نیاز در هر زمان و هر مکان با کمک جعبه‌ابزار.</p>

        <form method="GET" action="{{ route('experts.index') }}" class="hero-search home-search">
            <div class="hs-input-wrap">
                <svg class="hs-search-icon" width="20px" viewBox="0 0 24 24" height="20px" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M15.5 14h-.79l-.28-.27A6.518 6.518 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
                </svg>
                <input
                    type="text"
                    name="q"
                    id="search-input"
                    placeholder="دنبال چه خدماتی هستی؟ مثلاً نظافت، تعمیر خودرو...">
            </div>

            <div class="hs-divider"></div>

            <div class="hs-actions">
                <button type="submit" formaction="{{ route('experts.index') }}">متخصص‌ها</button>
                <button type="submit" formaction="{{ route('companies.index') }}">شرکت‌ها</button>
            </div>
        </form>

        <div class="pegboard">
            <div class="pegboard-rail"></div>
            <div class="pegboard-list">
                <a class="peg-item" href="#work-categories">
                    <span class="peg-dot"></span>
                    <span class="peg-chip"><span class="tag">CATEGORIES</span> دسته بند‌ی‌ خدمات</span>
                </a>
                <a class="peg-item" href="#images">
                    <span class="peg-dot"></span>
                    <span class="peg-chip"><span class="tag">PICTURES</span> عکس‌های منتخب</span>
                </a>
                <a class="peg-item" href="#comments">
                    <span class="peg-dot"></span>
                    <span class="peg-chip"><span class="tag">COMMENTS</span> نظرات کاربران</span>
                </a>
            </div>
        </div>



        <div class="hero-glow-cards">


            <div class="hero-cta-row">

                <button type="button" class="spin-btn" onclick="location.href='{{ url('/experts') }}'">
                    <span>افراد متخصص </span>
                </button>

                <button type="button" class="spin-btn" onclick="location.href='{{ url('/companies') }}'">
                    <span>شرکت‌های خدماتی </span>
                </button>

            </div>
</section>

{{-- 7. WORK CATEGORIES --}}
<section class="categories" id="work-categories">
    <div class="container">

        <div class="section-head">
            <span class="hero-eyebrow"><span class="dot"></span> دسته بندی خدمات</span>
            <h2>دریافت خدمات حرفه‌ای هر کار توی بخش خودش</h2>
            <p>برای دسترسی راحت‌تر می‌تونید بخش مورد نظرتون رو انتخاب کنید.</p>
        </div>

        <div class="tc-grid">

            @foreach($categories as $cat)
            <div class="tool-card">
                <div class="tc-canvas" onclick="location.href='{{ url('categories/' . $cat->url) }}'">
                    @for($i = 0; $i < 25; $i++)
                        <div class="tc-tracker">
                </div>
                @endfor

                <a href="{{ url('categories/' . $cat->url) }}" class="tc-face">
                    <span class="tc-overlay"></span>
                    <span class="tc-body">
                        <span class="tc-dot"></span>
                        <span class="tc-title">{{ $cat['category_name'] }}</span>
                        <span class="tc-desc">دسترسی سریع به بهترین متخصص‌ها و شرکت‌های این حوزه</span>
                        <span class="tc-cta">مشاهده خدمات <i class="tc-arrow">←</i></span>
                    </span>
                </a>
            </div>
        </div>
        @endforeach

    </div>
    </div>
</section>

{{-- 8. SLIDESHOW --}}
<div class="section-head" style="margin-top: 100px;" id="images">
    <span class=" hero-eyebrow"><span class="dot"></span>گالری عکس‌ها</span>
</div>
<div class="slideshow" id="toolbox-slideshow" style="margin-top: 10px;">


    <div class="slide is-active">
        <div class="slide-media" style="background-image:url('{{ asset('images/slide1.jpeg') }}')"></div>
        <div class="slide-overlay"></div>
        <div class="slide-copy">
            <span>اسباب کشی</span>
            <h3> دیگه اسباب کشی کار سختی نیست!!! </h3>
            <p>با کمک کارمندان و افراد متخصص دیگه نگران اسباب‌کشی نباشید.</p>
        </div>
    </div>

    <div class="slide">
        <div class="slide-media" style="background-image:url('{{ asset('images/slide2.jpg') }}')"></div>
        <div class="slide-overlay"></div>
        <div class="slide-copy">
            <span>تعمیرات خانگی</span>
            <h3>میتونی برای تعمیر هر وسیله‌ای از ما کمک بخوای </h3>
            <p>متخصصین ما میتونن هرچیزی رو تعمیر کنن.</p>
        </div>
    </div>

    <div class="slide">
        <div class="slide-media" style="background-image:url('{{ asset('images/slide3.jpeg') }}')"></div>
        <div class="slide-overlay"></div>
        <div class="slide-copy">
            <span>تعمیرات ماشین</span>
            <h3> تعمیرات ماشین در محل.</h3>
            <p>حتی توی جاده هم اگر ماشین خراب بشه دیگه نیاز نیست نگران باشی.</p>
        </div>
    </div>

    <div class="slide-arrows">
        <button type="button" class="slide-arrow" data-dir="prev" aria-label="اسلاید قبلی">‹</button>
        <button type="button" class="slide-arrow" data-dir="next" aria-label="اسلاید بعدی">›</button>
    </div>

    <div class="slide-dots"></div>

</div>
</section>

{{-- 9. TESTIMONIALS (real data from orders table) --}}
<section class="testimonials" id="comments">
    <div class="container">

        <div class="section-head">
            <span class="hero-eyebrow"><span class="dot"></span> نظر کاربران</span>
            <h2>نظرات کسایی که از جعبه‌ابزار استفاده کردن</h2>
        </div>

        <div class="t-grid">

            @forelse ($comments as $order)
            <div class="t-card">
                <span class="t-quote-mark">”</span>
                <div class="t-stars">
                    @for($i = 0; $i < 5; $i++)
                        {{ $i < $order->rating ? '★' : '☆' }}
                        @endfor
                        </div>
                        <p class="t-text">{{ $order->comment }}</p>
                        <div class="t-person">
                            <span class="t-avatar" style="background-image:url('{{ asset('images/default-pfp.png') }}')"></span>
                            <span>
                                <span class="t-name">{{ $order->customer->first_name ?? 'کاربر' }} {{ $order->customer->last_name ?? '' }}</span><br>
                                <span class="t-role">{{ $order->provider->expertDetail->category->category_name ?? 'مشتری جعبه‌ابزار' }}</span>
                            </span>
                        </div>
                        @if($order->providerID)
                        <a href="{{ route('experts.show', $order->providerID) }}">
                            <div class="t-person">
                                <span class="t-avatar" style="background-image:url('{{ asset('images/expert.png') }}')"></span>
                                <span>
                                    <span class="t-name">{{ $order->provider->first_name ?? 'متخصص' }} {{ $order->provider->last_name ?? '' }}</span><br>
                                    <span class="t-role">{{ $order->provider->expertDetail->category->category_name ?? 'متخصص جعبه‌ابزار' }}</span>
                                </span>
                            </div>
                        </a>
                        @elseif($order->companyID)
                        <a href="{{ route('companies.show', $order->companyID) }}">
                            <div class="t-person">
                                <span class="t-avatar" style="background-image:url('{{ asset('images/company.png') }}')"></span>
                                <span>
                                    <span class="t-name">{{ $order->company->name ?? 'شرکت' }}</span><br>
                                    <span class="t-role">شرکت خدماتی</span>
                                </span>
                            </div>
                        </a>
                        @endif

                </div>
                @empty
                <p style="text-align:center;color:var(--text-light);grid-column:1/-1;">
                    هنوز نظری ثبت نشده.
                </p>
                @endforelse

            </div>
        </div>
</section>

{{-- 10. CLOSING CTA BAR --}}
<section class="cta-bar">
    <div class="container">
        <div class="cta-box">
            <h2>اگر سوالی داری میتونی با ما در ارتباط باشی.</h2>
            <div class="cta-links">
                @guest
                <a href="{{ url('/register') }}" class="btn btn-primary">ثبت‌نام </a>
                <a href="{{ url('/login') }}" class="btn btn-outline">ورود </a>
                <a href="{{ url('/contact') }}" class="btn btn-outline">تماس با ما</a>
                @else
                <a href="{{ url('/contact') }}" class="btn btn-primary">تماس با ما</a>
                @endguest
                <a href="{{ url('/about') }}" class="btn btn-outline">درباره ما</a>

            </div>
        </div>
    </div>
</section>

<script src="{{ asset('js/home.js') }}"></script>

@endsection