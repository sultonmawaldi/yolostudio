@extends('layouts.app')

@section('title', 'Hasil Foto Anda')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <!-- ✦ Judul stylish dengan gradient & glow ✦ -->
            <h2 class="font-extrabold leading-tight
                   text-center
                   bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600
                   bg-clip-text text-transparent
                   drop-shadow-[0_4px_15px_rgba(56,189,248,0.3)]
                   font-[Playfair_Display]
                   title-glow scroll-fade"
                style="font-size: clamp(1.5rem, 5vw, 3.5rem); letter-spacing: 0.03em; line-height: 1.2;">
                <i class="fa-solid fa-star me-2"></i>
                Hasil Foto Anda
                <i class="fa-solid fa-star ms-2"></i>
            </h2>
            <!-- Garis -->
            <div class="mt-0 w-24 sm:w-32 md:w-44 h-[3px] mx-auto rounded-full 
             bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600 
             shadow-[0_0_12px_rgba(56,189,248,0.4)]
             scroll-fade"
                style="transition-delay: 0.1s;">
            </div>

            <!-- Aksen bawah lembut -->
            <div class="absolute left-1/2 -bottom-6 -translate-x-1/2 w-40 h-16 bg-blue-400/10 blur-2xl rounded-full scroll-fade"
                style="transition-delay: 0.15s;"></div>


            <!-- ✦ Subjudul / ucapan terima kasih ✦ -->
            <p class="text-gray-600 dark:text-gray-300 mt-5 text-base md:text-lg">
                Terima kasih telah berfoto bersama kami,
                <strong class="text-blue-500 dark:text-cyan-400">
                    {{ $transaction->user->name ?? ($transaction->appointment->name ?? 'Pelanggan') }}
                </strong>!
            </p>

            <!-- ✦ Info link aktif ✦ -->
            @if ($transaction->public_token_expires_at)
                <small class="text-gray-600 dark:text-gray-500 mt-1 block text-sm md:text-base">
                    Link aktif hingga
                    <span class="font-medium text-gray-700 dark:text-gray-200">
                        {{ $transaction->public_token_expires_at->translatedFormat('d F Y - H:i') }} WIB
                    </span>
                </small>
            @endif
        </div>

        @if ($transaction->photoResults->count())
            <div class="row g-3 justify-content-start photo-grid">
                @foreach ($transaction->photoResults as $photo)
                    @php
                        $photoUrl = asset('storage/' . $photo->file_path);
                        $downloadUrl = route('photo-results.download', [$photo->id, $transaction->public_token]);
                    @endphp

                    <div class="col-6 col-md-4 col-lg-3 fade-card">
                        <div class="photo-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                            <!-- Klik gambar → Lightbox -->
                            <a href="{{ $photoUrl }}" class="glightbox" data-gallery="photo-results"
                                data-touchswipe="true"
                                data-title='
                           <div style="display: flex; gap: 8px; margin-bottom:4px;">
                               <div style="flex: 0 0 70px; font-weight:500;">Kode : </div>
                               <div>{{ $transaction->transaction_code }}</div>
                           </div>
                           <div style="display: flex; gap: 8px;">
                               <div style="flex: 0 0 70px; font-weight:500;">Tanggal : </div>
                               <div>{{ $photo->created_at->translatedFormat('d F Y H:i') }} WIB</div>
                           </div>
                           '>
                                <div class="photo-wrapper shimmer">
                                    <img src="{{ $photoUrl }}" alt="Hasil Foto" loading="lazy"
                                        onload="this.parentElement.classList.remove('shimmer')">
                                </div>
                            </a>

                            <!-- Tombol unduh -->
                            <div class="card-body text-center p-2">
                                <a href="{{ $downloadUrl }}" class="btn btn-download no-loader w-100 rounded-pill"
                                    target="_blank" rel="noopener" download="{{ $photo->file_name }}">
                                    <i class="bi bi-cloud-arrow-down me-2"></i> Unduh Foto
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tombol unduh semua -->
            <div class="text-center mt-5">
                <a href="{{ route('photo-results.downloadAll', $transaction->public_token) }}"
                    class="btn btn-download-all rounded-pill px-4 py-2 no-loader" download>
                    <i class="bi bi-archive me-2"></i> Unduh Semua Foto (.zip)
                </a>
            </div>
        @else
            <p class="text-center text-muted fs-5 mt-5">
                Belum ada hasil foto yang diunggah
            </p>
        @endif

        <div class="text-center mt-5">
            <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.__glightboxInstance) return;
            window.__glightboxInstance = true;

            // ⭐ Inisialisasi GLightbox
            const lightbox = GLightbox({
                selector: ".glightbox",
                touchNavigation: true,
                loop: true,
                draggable: true,
                zoomable: true,
                openEffect: "zoom",
                closeEffect: "none",
                slideEffect: "slide",
                cssEasing: "ease-in-out",
            });

            const navbar = document.querySelector(".navbar, nav, header");

            function lockScroll() {
                document.body.classList.add("glightbox-open");
                if (navbar) navbar.style.paddingRight = "";
            }

            function unlockScroll() {
                document.body.classList.remove("glightbox-open");
                if (navbar) navbar.style.paddingRight = "";
            }

            let extraButtons = null;
            let currentImgUrl = null;

            // ⭐ Hapus tombol instant
            function removeExtraButtonsInstant() {
                if (extraButtons) {
                    extraButtons.remove();
                    extraButtons = null;
                }
            }

            // ⭐ Instant Close untuk tombol close GLightbox
            document.addEventListener("click", function(e) {
                if (e.target.closest(".gclose")) {
                    removeExtraButtonsInstant();
                }
            });

            // ⭐ FUNGSI UTAMA: Override backdrop agar close instan tanpa delay
            function overrideBackdropClose() {
                const overlay = document.querySelector(".goverlay");
                if (!overlay) return;

                // Clone → hapus event GLightbox bawaan → pasang event instant close
                const newOverlay = overlay.cloneNode(true);
                overlay.parentNode.replaceChild(newOverlay, overlay);

                newOverlay.addEventListener("click", () => {
                    removeExtraButtonsInstant();
                    unlockScroll();

                    const container = document.querySelector(".glightbox-container");
                    if (container) container.style.display = "none";

                    lightbox.destroy();
                });
            }

            // ⭐ Ketika GLightbox Dibuka
            lightbox.on("open", () => {
                lockScroll();

                // ➤ Pasang instant backdrop close
                setTimeout(overrideBackdropClose, 60);

                // ➤ Tombol tambahan
                extraButtons = document.createElement("div");
                extraButtons.className = "gl-extra-buttons";

                extraButtons.innerHTML = `
            <button class="gl-btn-download" title="Download">
                <i class="bi bi-download"></i>
            </button>
            <button class="gl-btn-share" title="Share">
                <i class="bi bi-share"></i>
            </button>
        `;

                document.body.appendChild(extraButtons);

                // Download
                extraButtons.querySelector(".gl-btn-download").onclick = () => {
                    const a = document.createElement("a");
                    a.href = currentImgUrl;
                    a.download = currentImgUrl.split('/').pop();
                    a.click();
                };

                // Share
                extraButtons.querySelector(".gl-btn-share").onclick = async () => {
                    if (navigator.share) {
                        await navigator.share({
                            title: "Foto Anda",
                            text: "Bagikan foto ini",
                            url: currentImgUrl,
                        });
                    } else {
                        alert("Perangkat tidak mendukung fitur Share.");
                    }
                };

                // ⭐ Swipe indicator mobile
                if (window.innerWidth <= 768) {
                    const indicator = document.createElement("div");
                    indicator.className = "swipe-indicator";
                    indicator.innerHTML = `
                <div class="swipe-inline">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Geser kanan / kiri untuk pindah foto</span>
                </div>
            `;
                    document.body.appendChild(indicator);

                    setTimeout(() => indicator.classList.add("show"), 200);
                    setTimeout(() => indicator.classList.remove("show"), 4000);
                    setTimeout(() => indicator.remove(), 4500);
                }
            });

            // ⭐ Ketika slide berubah
            lightbox.on("slide_changed", ({
                current
            }) => {
                const img = current?.slideNode?.querySelector(".gslide-media img");
                if (img) currentImgUrl = img.src;
            });

            // ⭐ Ketika GLightbox selesai ditutup
            lightbox.on("close", () => {
                unlockScroll();
                removeExtraButtonsInstant();
            });

            // ⭐ Fade card animation
            const fadeCards = document.querySelectorAll(".fade-card");
            const observer = new IntersectionObserver(
                entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add("fade-visible");
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1
                }
            );
            fadeCards.forEach(card => observer.observe(card));
        });
    </script>




    <style>
        /* 🌈 Judul */
        .text-gradient {
            background: linear-gradient(90deg, #007bff, #9c27b0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* 🌟 Cegah geser layout saat scrollbar hilang */
        html {
            scrollbar-gutter: stable;
        }

        body.glightbox-open {
            overflow: hidden;
            padding-right: 0 !important;
        }

        /* 🖼️ Kartu foto */
        .photo-card {
            background: #fff;
            border-radius: 1rem;
            transition: all 0.35s ease;
            display: flex;
            flex-direction: column;
        }

        .photo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
        }

        /* 🔲 Gambar */
        .photo-wrapper {
            width: 100%;
            aspect-ratio: 1 / 1;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .photo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .photo-wrapper img:hover {
            transform: scale(1.04);
        }

        /* 💾 Tombol unduh */
        .btn-download {
            background: transparent;
            color: #444;
            font-weight: 500;
            border: 1px solid #ccc;
            font-size: 0.9rem;
            padding: 7px 9px;
            transition: all 0.25s ease;
        }

        .btn-download:hover {
            background: #2d92e0;
            color: #ffffff;
        }

        /* 🌙 Dark mode */
        .dark .photo-card {
            background: #1e1e1e;
            box-shadow: 0 6px 15px rgba(255, 255, 255, 0.06);
        }

        .dark .photo-wrapper {
            background: #2a2a2a;
        }

        .dark .btn-download {
            color: #ddd;
            border-color: #555;
        }

        .dark .btn-download:hover {
            background: #333;
        }

        /* ✅ Swipe indicator (putih agar selalu terlihat) */
        .swipe-indicator {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(100%);
            background: rgba(255, 255, 255, 0.9);
            color: #000;
            padding: 10px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            opacity: 0;
            transition: all 0.4s ease;
            z-index: 2147483647;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(6px);
        }

        .swipe-indicator i {
            font-size: 16px;
            margin-right: 6px;
        }

        .swipe-indicator.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* 🌟 Animasi muncul kartu */
        .fade-card {
            opacity: 0;
            transform: translateY(25px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ✅ Perbaiki viewport shifting */
        .glightbox-container,
        .glightbox-clean .gcontainer,
        .glightbox-open .glightbox-container {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
        }

        html.glightbox-open,
        body.glightbox-open {
            overflow: hidden;
            position: relative;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* ✨ Swipe indicator */
        .swipe-indicator {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(100%);
            background: rgba(255, 255, 255, 0.9);
            color: #000;
            padding: 10px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            opacity: 0;
            transition: all 0.4s ease;
            z-index: 2147483647;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(6px);

            /* ✨ Agar ikon & teks sejajar horizontal */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .swipe-indicator.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ✨ Isi dalam indikator */
        .swipe-inline {
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        /* 🔄 Animasi ikon panah bergerak kanan–kiri */
        .swipe-indicator i {
            font-size: 16px;
            margin-right: 6px;
            animation: swipeMove 1.5s ease-in-out infinite;
        }

        @keyframes swipeMove {
            0% {
                transform: translateX(0);
                opacity: 1;
            }

            25% {
                transform: translateX(6px);
                opacity: 0.8;
            }

            50% {
                transform: translateX(0);
                opacity: 1;
            }

            75% {
                transform: translateX(-6px);
                opacity: 0.8;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Tombol Download & Share (versi kotak) */
        .gl-extra-buttons {
            position: fixed;
            /* tetap di atas, menempel dengan tombol close */
            top: 13px;
            right: 60px;
            /* sesuaikan posisi dengan tombol Close */
            display: flex;
            gap: 8px;
            z-index: 999999;
        }

        .gl-extra-buttons button {
            background: rgba(0, 0, 0, 0.55);
            /* semi-transparent */
            border: none;
            color: #fff;
            padding: 8px 10px;
            /* proporsional kotak */
            width: 38px;
            /* ukuran kotak */
            height: 38px;
            /* ukuran kotak */
            border-radius: 6px;
            /* kotak dengan sudut agak membulat */
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
            transition: background 0.2s, color 0.2s;
        }

        .gl-extra-buttons button:hover {
            background: rgba(255, 255, 255, 0.85);
            color: #000;
        }

        /* Smooth swipe tambahan */
        .gslide,
        .gslide-image,
        .gslide-media {
            transition: transform 0.35s ease-in-out !important;
        }

        /* Tombol Unduh Semua dengan Gradient */
        .btn-download-all {
            background: linear-gradient(90deg, #007bff, #00c6ff);
            color: #fff;
            font-weight: 600;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
        }

        .btn-download-all:hover {
            background: linear-gradient(90deg, #0056b3, #0099cc);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.35);
        }
    </style>
@endsection
