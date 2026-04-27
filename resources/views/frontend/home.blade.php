@extends('layouts.app')

@section('content')
    <div class="min-h-full bg-gray-50 dark:bg-gray-900">


        {{-- Hero Section --}}
        <section x-data="{
            slides: [
                { image: '/uploads/hero/slide1.webp', title: '#BikinKenanganDiYOLO', subtitle: 'Self Photo Studio & Photobox Kekinian ✨' },
                { image: '/uploads/hero/slide2.webp', title: 'Ciptakan Kenangan Unik', subtitle: 'Rasakan pengalaman studio profesional' },
                { image: '/uploads/hero/slide3.webp', title: 'Ekspresikan Dirimu!', subtitle: 'Ambil foto terbaik versi kamu 💙' },
            ],
            active: 0,
            interval: null,
            startX: 0,
            endX: 0,
            dragging: false,
            velocity: 0,
            lastMoveTime: 0,
            momentumFrame: null,
        
            next() { this.active = (this.active + 1) % this.slides.length },
            prev() { this.active = (this.active - 1 + this.slides.length) % this.slides.length },
        
            start() {
                this.stop();
                this.interval = setInterval(() => this.next(), 5000);
            },
            stop() {
                if (this.interval) clearInterval(this.interval);
            },
        
            // ✅ Swipe + Momentum
            initSwipe() {
                const area = this.$refs.slider;
        
                const handleStart = (x) => {
                    this.startX = x;
                    this.dragging = true;
                    this.stop();
                    this.lastMoveTime = Date.now();
                    if (this.momentumFrame) cancelAnimationFrame(this.momentumFrame);
                };
        
                const handleMove = (x) => {
                    if (!this.dragging) return;
                    const now = Date.now();
                    const delta = x - this.endX;
                    this.velocity = delta / (now - this.lastMoveTime);
                    this.lastMoveTime = now;
                    this.endX = x;
                };
        
                const handleEnd = () => {
                    if (!this.dragging) return;
                    const diff = this.endX - this.startX;
        
                    // Minimal distance for swipe
                    if (Math.abs(diff) > 60) {
                        diff < 0 ? this.next() : this.prev();
                    } else {
                        // ✅ Momentum Effect (soft inertia)
                        const applyMomentum = () => {
                            this.endX += this.velocity * 50;
                            this.velocity *= 0.9; // friction
                            if (Math.abs(this.velocity) > 0.1) {
                                this.momentumFrame = requestAnimationFrame(applyMomentum);
                            }
                        };
                        applyMomentum();
                    }
        
                    this.dragging = false;
                    this.start(); // resume autoplay
                };
        
                // ✅ Touch Events
                area.addEventListener('touchstart', e => handleStart(e.touches[0].clientX));
                area.addEventListener('touchmove', e => handleMove(e.touches[0].clientX));
                area.addEventListener('touchend', handleEnd);
        
                // ✅ Mouse Events (desktop drag)
                area.addEventListener('mousedown', e => handleStart(e.clientX));
                area.addEventListener('mousemove', e => handleMove(e.clientX));
                area.addEventListener('mouseup', handleEnd);
                area.addEventListener('mouseleave', handleEnd);
            }
        }" x-init="start();
        initSwipe()" @mouseenter="stop()" @mouseleave="start()" x-ref="slider"
            class="relative isolate overflow-hidden h-[90vh] flex items-center justify-center text-center select-none cursor-grab active:cursor-grabbing">

            {{-- Background Slides --}}
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="active === index" x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-1000" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute inset-0 bg-cover bg-center will-change-transform"
                    :style="`background-image: url('${slide.image}')`">
                    {{-- Overlay --}}
                    <div
                        class="absolute inset-0 bg-gradient-to-b 
                        from-white/70 via-sky-100/40 to-blue-200/60 
                        dark:from-gray-900/80 dark:via-gray-800/60 dark:to-gray-900/80">
                    </div>
                </div>
            </template>

            {{-- Floating Shapes --}}
            <div aria-hidden="true" class="absolute inset-0 -z-10">
                <div
                    class="absolute left-1/2 top-1/4 -translate-x-1/2 -translate-y-1/2 
                    w-[28rem] h-[28rem] sm:w-[36rem] sm:h-[36rem] lg:w-[42rem] lg:h-[42rem] 
                    bg-gradient-to-tr from-blue-300 via-sky-300 to-blue-200 
                    opacity-30 rounded-full blur-3xl animate-blob">
                </div>

                <div
                    class="absolute right-1/4 top-1/3 
                    w-[20rem] h-[20rem] sm:w-[28rem] sm:h-[28rem] lg:w-[34rem] lg:h-[34rem] 
                    bg-gradient-to-tr from-white via-blue-200 to-sky-300 
                    dark:from-gray-700 dark:via-gray-800 dark:to-gray-900
                    opacity-30 rounded-full blur-3xl animate-blob animation-delay-2000">
                </div>
            </div>

            {{-- Content --}}
            <div class="relative z-10 px-6 text-center sm:px-8 lg:px-12">
                <h1
                    class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl 
           font-extrabold tracking-tight 
           bg-gradient-to-r from-white via-sky-400 to-blue-600 
           bg-clip-text text-transparent animate-fadeInUp leading-tight drop-shadow-[0_2px_10px_rgba(255,255,255,0.4)]">
                    <span x-text="slides[active].title"></span>
                    <br class="hidden sm:block" />
                    <span
                        class="block mt-2 text-gray-900 dark:text-gray-100 
                        text-lg sm:text-2xl md:text-3xl font-semibold drop-shadow-[0_1px_8px_rgba(0,0,0,0.3)]"
                        x-text="slides[active].subtitle"></span>
                </h1>

                <p
                    class="mt-6 max-w-2xl mx-auto text-base sm:text-lg lg:text-xl 
                  text-gray-700 dark:text-gray-300 animate-fadeInUp delay-200">
                    Pilih jadwal, pilih paket, bayar, dan datang! Nikmati kemudahan booking studio dengan sistem online
                    yang praktis, modern, dan cepat.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 animate-fadeInUp delay-400">
                    <a href="/booking"
                        class="no-underline hover:no-underline rounded-full 
                      bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-600 
                      px-10 py-3 text-lg font-semibold text-white shadow-[0_0_20px_rgba(59,130,246,0.6)] 
                      hover:shadow-[0_0_35px_rgba(59,130,246,0.8)] 
                      transition-all duration-300 ease-out 
                      transform hover:-translate-y-2 hover:scale-110 
                      active:scale-125 active:shadow-[0_0_45px_rgba(59,130,246,1)]">
                        Booking Now
                    </a>
                </div>
            </div>

            {{-- Navigation Dots --}}
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex space-x-3 z-20">
                <template x-for="(slide, i) in slides" :key="i">
                    <button @click="active = i" :class="active === i ? 'bg-white w-6' : 'bg-white/40 w-3'"
                        class="h-3 rounded-full transition-all duration-500"></button>
                </template>
            </div>
        </section>











        <section id="features" class="py-16 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

                <!-- ✦ Layanan Foto Kami ✦ -->
                <div class="relative text-center mb-8 overflow-visible py-6">
                    <!-- Aura Cahaya -->
                    <div
                        class="absolute inset-0 -z-10 bg-gradient-radial from-blue-400/10 via-cyan-300/5 to-transparent blur-3xl animate-pulse-slow">
                    </div>

                    <!-- Section Title -->
                    <div class="text-center mb-2 overflow-visible relative px-2">
                        <!-- Judul -->
                        <h2 class="font-extrabold leading-tight text-center 
             bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600 
             bg-clip-text text-transparent drop-shadow-[0_3px_10px_rgba(56,189,248,0.25)]
             font-[Playfair_Display]
             title-glow scroll-fade"
                            style="font-size: clamp(1rem, 5vw, 3rem); letter-spacing: 0.03em;">
                            <i class="fas fa-star me-2"></i>
                            Layanan Foto Kami
                            <i class="fas fa-star ms-2"></i>
                        </h2>

                        <!-- Garis -->
                        <div class="mt-3 w-24 sm:w-32 md:w-44 h-[3px] mx-auto rounded-full 
             bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600 
             shadow-[0_0_12px_rgba(56,189,248,0.4)]
             scroll-fade"
                            style="transition-delay: 0.1s;">
                        </div>

                        <!-- Aksen bawah lembut -->
                        <div class="absolute left-1/2 -bottom-6 -translate-x-1/2 w-40 h-16 bg-blue-400/10 blur-2xl rounded-full scroll-fade"
                            style="transition-delay: 0.15s;"></div>
                    </div>
                </div>



                <!-- Swiper -->
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">

                        @foreach ($services as $service)
                            <div class="swiper-slide scroll-fade">
                                <a href="#{{ $service->slug }}"
                                    class="card-service block p-4 bg-white rounded-xl shadow hover:shadow-lg transition no-underline">

                                    <!-- Image -->
                                    <div class="w-full rounded-xl overflow-hidden mb-4 h-72 sm:h-80 md:h-96">
                                        <img src="{{ $service->image ? asset('uploads/images/service/' . $service->image) : asset('uploads/images/no-image.jpg') }}"
                                            alt="{{ $service->title }}" class="w-full h-full object-cover">
                                    </div>



                                    <!-- Title -->
                                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">
                                        {{ $service->title }}
                                    </h3>

                                </a>
                            </div>
                        @endforeach

                    </div>

                    <!-- Pagination -->
                    <div class="swiper-pagination mt-6"></div>
                </div>

            </div>
        </section>

        <!-- ===== STYLE ===== -->
        <style>
            /* Font Elegan */
            @import url("https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap");

            body {
                font-family: 'Poppins', sans-serif;
            }

            h2 {
                font-family: 'Playfair Display', serif;
            }

            /* ==========================================================
                                                                                                                                                                                                                                                                 ✦ Fade-in smooth ✦
                                                                                                                                                                                                                                                              ========================================================== */
            @keyframes fade-in {
                0% {
                    opacity: 0;
                    transform: translateY(15px) scale(0.96);
                }

                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .animate-fade-in {
                animation: fade-in 1.2s ease-out forwards;
            }

            /* ==========================================================
                                                                                                                                                                                                                                                                 ✦ Latar Corak Transparan ✦
                                                                                                                                                                                                                                                                 (tidak ganggu hover/transform)
                                                                                                                                                                                                                                                              ========================================================== */
            #features {
                position: relative;
                overflow: hidden;
            }

            #features::before {
                content: "";
                position: absolute;
                inset: 0;
                background: url("https://www.transparenttextures.com/patterns/scribble-light.png") repeat center;
                opacity: 10;
                /* cukup lembut agar terlihat di light & dark mode */
                pointer-events: none;
                z-index: 0;
            }

            #features>* {
                position: relative;
                z-index: 1;
            }

            /* ==========================================================
                                                                                                                                                                                                                                                                 ✦ Kartu Elegan ✦
                                                                                                                                                                                                                                                              ========================================================== */
            .card-service {
                background: rgba(255, 255, 255, 0.75);
                border-radius: 1.25rem;
                padding: 2rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                transition: all 0.6s cubic-bezier(0.22, 1, 0.36, 1);
                backdrop-filter: blur(10px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
                transform: translateZ(0);
                will-change: transform;
            }

            .dark .card-service {
                background: rgba(31, 41, 55, 0.75);
            }

            .card-service:hover {
                transform: translateY(-10px) scale(1.08);
                box-shadow: 0 20px 40px rgba(56, 189, 248, 0.25);
            }

            /* ==========================================================
                                                                                                                                                                                                                                                                 ✦ Swiper Settings ✦
                                                                                                                                                                                                                                                              ========================================================== */
            .swiper {
                overflow: hidden;
                padding: 2rem 0;
                will-change: transform;
            }

            .swiper-slide {
                transition: transform 0.9s cubic-bezier(0.36, 0.66, 0.04, 1);
                will-change: transform;
            }

            .swiper-slide-active .card-service {
                transform: scale(1.05);
                box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15);
            }

            /* ==========================================================
                                                                                                                                                                                                                                                                 ✦ Pagination ✦
                                                                                                                                                                                                                                                              ========================================================== */
            .swiper-pagination-bullet {
                background: rgba(59, 130, 246, 0.4);
                transition: all 0.4s ease;
            }

            .swiper-pagination-bullet-active {
                background: linear-gradient(to right, #60a5fa, #3b82f6);
                width: 20px;
                border-radius: 8px;
            }

            /* ==========================================================
                                                                                                                                                                                                                                                                 ✦ Fade-in Saat Scroll ✦
                                                                                                                                                                                                                                                              ========================================================== */
            .scroll-fade {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.8s ease-out, transform 0.8s ease-out;
            }

            .scroll-fade.show {
                opacity: 1;
                transform: translateY(0);
            }

            /* Stagger delay animasi */
            #features h2.scroll-fade {
                transition-delay: 0.1s;
            }

            #features .swiper-slide.scroll-fade:nth-child(1) {
                transition-delay: 0.15s;
            }

            #features .swiper-slide.scroll-fade:nth-child(2) {
                transition-delay: 0.25s;
            }

            #features .swiper-slide.scroll-fade:nth-child(3) {
                transition-delay: 0.35s;
            }

            #features .swiper-slide.scroll-fade:nth-child(4) {
                transition-delay: 0.45s;
            }
        </style>




        <!-- ===== SWIPER JS ===== -->
        <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
        <script>
            const swiper = new Swiper(".mySwiper", {
                slidesPerView: 1.2,
                spaceBetween: 24,
                centeredSlides: true,
                loop: true,
                loopedSlides: 1,
                speed: 1000,
                grabCursor: true,
                freeMode: true,
                freeModeMomentum: true,
                freeModeMomentumRatio: 0.5,
                freeModeSticky: false,
                slideToClickedSlide: true,
                autoplay: {
                    delay: 1000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                    dynamicBullets: true,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 1.5,
                        spaceBetween: 20
                    },
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 28
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 32
                    },
                },
            });

            // ==========================================================
            // Hover scale halus pada card tanpa memperbesar slide tengah
            // ==========================================================
            swiper.slides.forEach(slide => {
                const card = slide.querySelector('.card-service');
                if (!card) return;

                slide.addEventListener('mouseenter', () => {
                    card.style.transition = 'transform 0.5s cubic-bezier(0.22,1,0.36,1), box-shadow 0.5s';
                    card.style.transform = 'scale(1.12) translateY(-10px)';
                    card.style.boxShadow = '0 25px 45px rgba(56,189,248,0.25)';
                });

                slide.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                    card.style.transition = '';
                    card.style.boxShadow = '';
                });
            });
        </script>

        <style>
            /* Hover halus dengan kalcer vibes */
            .card-service {
                transition: transform 0.5s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.5s;
            }

            .card-service:hover {
                /* Opsional: bisa ditambah scale hover default juga */
            }
        </style>


        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const scrollItems = document.querySelectorAll('.scroll-fade');

                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            requestAnimationFrame(() => entry.target.classList.add('show'));
                            obs.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.15
                });

                scrollItems.forEach(el => observer.observe(el));
            });
        </script>





        {{-- Cabang Kami --}}
        <section
            class="relative py-20 bg-gradient-to-b 
    from-white via-sky-100 to-blue-200 
    dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 
    overflow-hidden">


            {{-- Background Pattern --}}
            <div aria-hidden="true" class="absolute inset-0"
                style="
            background-image: url('https://www.transparenttextures.com/patterns/diagonal-noise.png');
            opacity: 0.15;
            background-repeat: repeat;
            pointer-events: none;">
            </div>

            <div class="relative max-w-7xl mx-auto px-6 text-center">

                {{-- TITLE --}}
                <div class="relative text-center mb-8 py-6">
                    <div
                        class="absolute inset-0 -z-10 bg-gradient-radial 
                       from-blue-400/10 via-cyan-300/5 to-transparent 
                       blur-3xl animate-pulse-slow">
                    </div>

                    <h2 class="font-extrabold leading-tight 
                       bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600 
                       bg-clip-text text-transparent
                       drop-shadow-[0_3px_10px_rgba(56,189,248,0.25)]
                       font-[Playfair_Display]
                       scroll-fade"
                        style="font-size: clamp(1.2rem, 5vw, 3rem); letter-spacing: 0.03em;">
                        <i class="fa-solid fa-star me-2"></i>
                        Temukan Kami Di Kotamu
                        <i class="fa-solid fa-star ms-2"></i>
                    </h2>

                    <div class="mt-3 w-24 sm:w-32 md:w-44 h-[3px] mx-auto rounded-full
                       bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600
                       shadow-[0_0_12px_rgba(56,189,248,0.4)]
                       scroll-fade"
                        style="transition-delay: .1s;">
                    </div>
                </div>

                {{-- SUBTITLE --}}
                <p class="text-lg sm:text-xl text-gray-900 dark:text-white max-w-3xl mx-auto">
                    YOLO Studio hadir lebih dekat denganmu.
                    Pilih studio terdekat dan mulai sesi fotomu 💫
                </p>





                {{-- GRID STUDIO DINAMIS --}}
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 mt-16">

                    @foreach ($studios as $i => $studio)
                        <div class="group bg-white/70 dark:bg-white/10 
                           rounded-2xl p-8 backdrop-blur-lg shadow-lg 
                           transition transform hover:-translate-y-2 hover:shadow-2xl
                           scroll-fade"
                            style="transition-delay: {{ 0.25 + $i * 0.1 }}s;">

                            {{-- IMAGE --}}
                            <div class="mb-5">
                                <img src="{{ asset('uploads/studios/' . $studio->image) }}" alt="{{ $studio->name }}"
                                    class="rounded-xl w-full h-auto
                                transition-transform duration-500 group-hover:scale-105">
                            </div>

                            {{-- TITLE --}}
                            <h3
                                class="text-2xl font-bold mb-2 
                               text-blue-700 dark:text-blue-300">
                                {{ $studio->name }}
                            </h3>


                            {{-- LINK --}}
                            <a href="/studio"
                                class="inline-block mt-2 bg-blue-600 text-white 
          font-semibold px-6 py-2 rounded-full shadow 
          hover:bg-blue-500 transition transform hover:-translate-y-1
          no-underline">
                                Lihat Studio
                            </a>

                        </div>
                    @endforeach

                </div>
            </div>

            {{-- Floating Shapes --}}
            <div aria-hidden="true"
                class="absolute top-0 left-0 w-72 h-72 
               bg-sky-300 opacity-30 rounded-full 
               blur-3xl animate-blob">
            </div>

            <div aria-hidden="true"
                class="absolute bottom-0 right-0 w-72 h-72 
               bg-blue-400 opacity-30 rounded-full 
               blur-3xl animate-blob animation-delay-2000">
            </div>
        </section>


        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const scrollItems = document.querySelectorAll('.scroll-fade');

                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            requestAnimationFrame(() => entry.target.classList.add('show'));
                            obs.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.15
                });

                scrollItems.forEach(el => observer.observe(el));
            });
        </script>




        <style>
            @keyframes blob {

                0%,
                100% {
                    transform: translate(0, 0) scale(1);
                }

                33% {
                    transform: translate(30px, -20px) scale(1.1);
                }

                66% {
                    transform: translate(-20px, 20px) scale(0.9);
                }
            }

            .animate-blob {
                animation: blob 7s infinite;
            }

            .animation-delay-2000 {
                animation-delay: 2s;
            }
        </style>
    @endsection
