@extends('layouts.app')

@section('title', 'Toolbox')

@section('content')

<section class="hero">

    <div class="container hero-content">

        <span class="hero-eyebrow"><span class="dot"></span> همیشه همراه شما   </span>

        <h1><em>جعبه‌ابزار</em>، شامل هر خدماتی که نیاز داری</h1>

        <p>یافتن سریع خدمات مورد نیاز در هر زمان و هر مکان با کمک جعبه‌ابزار.</p>

        <form class="hero-search" action="{{ url('/search') }}" method="GET">
            <input type="text" name="q" placeholder="دنبال چه خدماتی می‌گردی؟ مثلا تعمیرات خودرو">
            <button type="submit">جستجو</button>
        </form>

        <div class="pegboard">
            <div class="pegboard-rail"></div>
            <div class="pegboard-list">
                <a class="peg-item" href="#work-categories">
                    <span class="peg-dot"></span>
                    <span class="peg-chip"><span class="tag">CATEGORIES</span> دسته بند‌ی‌ خدمات</span>
                </a>
                <a class="peg-item" href="#toolbox-slideshow">
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
                            <div class="tc-tracker"></div>
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
<div class="slideshow" id="toolbox-slideshow">

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
            <h3>میتونی برای تعمیر هر وسیله‌ای از ما کمک بخوای  </h3>
            <p>متخصصین ما میتونن هرچیزی رو تعمیر کنن.</p>
        </div>
    </div>

    <div class="slide">
        <div class="slide-media" style="background-image:url('{{ asset('images/slide3.jpeg') }}')"></div>
        <div class="slide-overlay"></div>
        <div class="slide-copy">
            <span>تعمیرات ماشین</span>
            <h3> تعمیرات ماشین در محل.</h3>
            <p>حتی توی جاده هم اگر ماشین خراب بشه دیگه نیاز  نیست نگران باشی.</p>
        </div>
    </div>

    <div class="slide-arrows">
        <button type="button" class="slide-arrow" data-dir="prev" aria-label="اسلاید قبلی">‹</button>
        <button type="button" class="slide-arrow" data-dir="next" aria-label="اسلاید بعدی">›</button>
    </div>

    <div class="slide-dots"></div>

</div>
</section>

{{-- 9. TESTIMONIALS --}}
<section class="testimonials" id="comments">
    <div class="container">

        <div class="section-head">
            <span class="hero-eyebrow"><span class="dot"></span> نظر کاربران</span>
            <h2>نظرات کسایی که از جعبه‌ابزار استفاده کردن</h2>
        </div>

        <div class="t-grid">

            @php
                $testimonials = [
                    ['name' => 'سارا محمدی',   'role' => 'دانشجوی کارشناسی ارشد', 'stars' => 5, 'text' => 'برای تبدیل مقاله‌ها به PDF و برعکسش همیشه اینجا میام. سریع و بدون دردسره.', 'avatar' => 'https://picsum.photos/seed/av-sara/100/100'],
                    ['name' => 'امیر رضایی',   'role' => 'طراح گرافیک',          'stars' => 5, 'text' => 'ابزار فشرده‌سازی تصویرش خیلی به کارم میاد، کیفیت رو خراب نمی‌کنه و سریعه.', 'avatar' => 'https://picsum.photos/seed/av-amir/100/100'],
                    ['name' => 'نگار حسینی',   'role' => 'حسابدار',             'stars' => 4, 'text' => 'محاسبه‌گرهای مالیش دقیقن و رابط کاربری ساده‌ای داره. خیلی وقتمو گرفته.', 'avatar' => 'https://picsum.photos/seed/av-negar/100/100'],
                    ['name' => 'کیان اکبری',   'role' => 'توسعه‌دهنده وب',       'stars' => 5, 'text' => 'ابزارهای فرمت کد و JSON خیلی به دردم می‌خوره، هر روز ازش استفاده می‌کنم.', 'avatar' => 'https://picsum.photos/seed/av-kian/100/100'],
                    ['name' => 'مریم قاسمی',   'role' => 'کارمند اداری',         'stars' => 5, 'text' => 'ادغام فایل‌های PDF قبلاً کلی وقتمو می‌گرفت، الان چند ثانیه‌ست.', 'avatar' => 'https://picsum.photos/seed/av-maryam/100/100'],
                    ['name' => 'رضا طاهری',    'role' => 'فریلنسر',              'stars' => 4, 'text' => 'یه جعبه‌ابزار کامل که همه‌چی توش هست، دیگه دنبال سایت‌های دیگه نمی‌گردم.', 'avatar' => 'https://picsum.photos/seed/av-reza/100/100'],
                ];
            @endphp

            @foreach($testimonials as $t)
                <div class="t-card">
                    <span class="t-quote-mark">”</span>
                    <div class="t-stars">
                        @for($i = 0; $i < 5; $i++)
                            {{ $i < $t['stars'] ? '★' : '☆' }}
                        @endfor
                    </div>
                    <p class="t-text">{{ $t['text'] }}</p>
                    <div class="t-person">
                        <span class="t-avatar" style="background-image:url('{{ $t['avatar'] }}')"></span>
                        <span>
                            <span class="t-name">{{ $t['name'] }}</span><br>
                            <span class="t-role">{{ $t['role'] }}</span>
                        </span>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</section>

{{-- 10. CLOSING CTA BAR --}}
<section class="cta-bar">
    <div class="container">
        <div class="cta-box">
            <h2>اگر سوالی داری میتونی با ما در ارتباط باشی.</h2>
            <div class="cta-links">
                <a href="{{ url('/register') }}" class="btn btn-primary">ثبت‌نام </a>
                <a href="{{ url('/login') }}" class="btn btn-outline">ورود </a>
                <a href="{{ url('/about') }}" class="btn btn-outline">درباره ما</a>
                <a href="{{ url('/contact') }}" class="btn btn-outline">تماس با ما</a>
            </div>
        </div>
    </div>
</section>

{{-- Slideshow behavior — put this in your app.js, or keep inline via @push('scripts') if your layout supports it --}}
<script>
(function () {
  const root = document.getElementById('toolbox-slideshow');
  if (!root) return;

  const slides = [...root.querySelectorAll('.slide')];
  const dotsWrap = root.querySelector('.slide-dots');
  let index = 0;
  let timer;

  slides.forEach((_, i) => {
    const dot = document.createElement('button');
    dot.type = 'button';
    dot.className = 'slide-dot' + (i === 0 ? ' is-active' : '');
    dot.setAttribute('aria-label', 'برو به اسلاید ' + (i + 1));
    dot.addEventListener('click', () => go(i));
    dotsWrap.appendChild(dot);
  });

  const dots = [...dotsWrap.querySelectorAll('.slide-dot')];

  function go(i) {
    slides[index].classList.remove('is-active');
    dots[index].classList.remove('is-active');
    index = (i + slides.length) % slides.length;
    slides[index].classList.add('is-active');
    dots[index].classList.add('is-active');
    restart();
  }

  function restart() {
    clearInterval(timer);
    timer = setInterval(() => go(index + 1), 5000);
  }

  root.querySelector('[data-dir="next"]').addEventListener('click', () => go(index + 1));
  root.querySelector('[data-dir="prev"]').addEventListener('click', () => go(index - 1));
  root.addEventListener('mouseenter', () => clearInterval(timer));
  root.addEventListener('mouseleave', restart);

  restart();
})();
</script>

@endsection
