<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M3aarf Youtube Course Scraper</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<header class="bg-white py-3 shadow-sm" style="border-top: 4px solid #4da3ff;">
    <div class="container d-flex align-items-center">
        <div class="rounded d-flex align-items-center justify-content-center"
             style="width: 34px; height: 34px; background-color: #d9534f;">
            <i class="fa-solid fa-play text-white" style="font-size: 15px;"></i>
        </div>
        <span class="fw-bold ms-3"
              style="font-size: 1.35rem; color: #d9534f; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">YouTube Course Scraper</span>
        <span class="mx-3" style="color: #e0e0e0; font-size: 1.4rem; font-weight: 300;">|</span>
        <span class="fw-bold" style="font-size: 1.15rem; color: #737373 !important;">أداة جمع الدورات التعليمية</span>
    </div>
</header>

<div class="pb-4">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="hero-section text-right">
        <h1>جمع الدورات التعليمية من يوتيوب</h1>
        <p class="mb-0">أدخل التصنيفات واضغط ابدأ - النظام سيجمع الدورات تلقائيًا باستخدام الذكاء الاصطناعي</p>
    </div>

    <div class="container form-container">
        <form action="{{ route(\App\Enum\RouteEnum::SCRAPPER->value) }}" method="POST">
            @csrf
            <div class="row">

                <div class="col-md-8 order-md-1">
                    <label class="mb-2 fw-bold text-muted">أدخل التصنيفات (كل تصنيف في سطر جديد)</label>
                    <div class="d-flex align-items-start gap-3">
                        <textarea name="categories" class="form-control textarea-custom flex-grow-1" rows="6"
                                  placeholder="التسويق&#10;البرمجة&#10;التصميم&#10;الهندسة&#10;إدارة الأعمال"
                                  required></textarea>
                        <div class="d-flex flex-column" style="min-width: 160px;">
                            <button type="submit" class="submit-btn mt-0" id="fetchBtn">
                                <i class="fa-solid fa-play ms-1"></i> ابدأ الجمع
                            </button>
                            <button type="button" class="btn btn-outline-danger w-100 mt-2 fw-bold" id="stopBtn">
                                <i class="fa-solid fa-stop ms-1"></i> إيقاف
                            </button>
                        </div>
                    </div>
                    <div id="loadingMsg" class="text-center text-danger mt-3 d-none fw-bold">
                        <i class="fa-solid fa-spinner fa-spin"></i>جاري البحث والجمع... قد يستغرق هذا بضع دقائق، سيتم تحديث الصفحة تلقائيًا.
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="container mb-4 text-right">
        <h2 class="section-title">الدورات المكتشفة</h2>
        <p class="section-subtitle ">
            تم العثور على {{ $totalCount }} دورة في {{ collect($categories)->count() }} تصنيفات
        </p>
    </div>

    <ul class="nav nav-pills category-pills justify-content-center mb-4">
        <li class="nav-item">
            <a class="nav-link {{ request('category', 'all') == 'all' ? 'active' : '' }}"
               href="{{ route(\App\Enum\RouteEnum::HOME->value, ['category' => 'all']) }}">
                الكل ({{ $totalCount }})
            </a>
        </li>
        @foreach($categories as $cat)
            @php
                $catCount = \App\Models\YoutubeEducationalPlaylist::query()->where('category', $cat)->count();
            @endphp
            <li class="nav-item">
                <a class="nav-link {{ request('category') == $cat ? 'active' : '' }}"
                   href="{{ route(\App\Enum\RouteEnum::HOME->value, ['category' => $cat]) }}">
                    {{ $cat }} ({{ $catCount }})
                </a>
            </li>
        @endforeach
    </ul>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @foreach($playlists as $playlist)
            <div class="col">
                <div class="course-card">
                    <div class="course-thumbnail" style="background-image: url('{{ $playlist->thumbnail }}');">
                        <div class="play-icon-badge">
                            <i class="fa-solid fa-play"></i>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <h3 class="course-title" title="{{ $playlist->title }}">{{ $playlist->title }}</h3>
                        <div class="channel-name">
                            <i class="fa-regular fa-user"></i> {{ $playlist->channel_name }}
                        </div>
                    </div>
                    <div class="card-footer-custom bg-white border-0">
                        <span class="category-tag">{{ $playlist->category }}</span>
                        <a href="https://www.youtube.com/playlist?list={{ $playlist->youtube_playlist_id }}"
                           target="_blank" class="text-decoration-none text-muted" style="font-size: 0.8rem;">
                            عرض الدورة <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4 pb-5 d-flex justify-content-center">
        {{ $playlists->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let pollInterval = null;
    /* AI Part */
    document.querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault();
        if (document.querySelector('textarea[name="categories"]').value.trim() !== '') {
            document.getElementById('fetchBtn').disabled = true;
            document.getElementById('loadingMsg').classList.remove('d-none');

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => response.json())
              .then(data => {
                  if(data.success) {
                      checkJobStatus();
                  }
              });
        }
    });

    function checkJobStatus() {
        pollInterval = setInterval(() => {
            fetch("{{ route('yt-playlist-generator.check-status') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.finished || data.stopped) {
                        clearInterval(pollInterval);
                        window.location.reload();
                    }
                });
        }, 2000);
    }

    document.getElementById('stopBtn').addEventListener('click', function() {
        fetch("{{ route('yt-playlist-generator.stop') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(() => {
             if(pollInterval) clearInterval(pollInterval);
             window.location.reload();
        });
    });
</script>
</body>
</html>
