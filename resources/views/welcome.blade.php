<x-layout title="Study smarter">
    <style>
        .welcome-page { padding-top: 8px; }
        .hero {
            position: relative;
            display: grid;
            min-height: 440px;
            overflow: hidden;
            align-items: center;
            grid-template-columns: 1.05fr .95fr;
            gap: 30px;
            padding: clamp(28px, 6vw, 66px);
            border: 1px solid #e9e8fb;
            border-radius: 24px;
            background: radial-gradient(ellipse at 82% 20%, rgba(223,220,255,.8), transparent 32%), linear-gradient(120deg,#fff 38%,#f3f1ff 100%);
            box-shadow: 0 22px 60px rgba(37,35,88,.07);
        }
        .hero-copy { position: relative; z-index: 1; }
        .hero-kicker { display: inline-flex; align-items: center; gap: 8px; margin: 0 0 22px !important; padding: 7px 11px; border: 1px solid #e2dfff; border-radius: 999px; color: #5145cd !important; background: rgba(255,255,255,.78); font-size: 11px; font-weight: 700; }
        .hero-kicker span { width: 7px; height: 7px; border-radius: 50%; background: #7569e8; }
        .hero h1 { max-width: 560px; margin: 0; font-size: clamp(38px,6vw,66px); line-height: 1.02; letter-spacing: -.06em; }
        .hero h1 em { color: #5145cd; font-style: normal; }
        .hero-description { max-width: 470px; margin: 20px 0 27px !important; color: #697386 !important; font-size: 15px; line-height: 1.75 !important; }
        .hero-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 11px; }
        .hero-button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; padding: 0 18px; border: 1px solid #5145cd; border-radius: 10px; color: white; background: #5145cd; font-size: 13px; font-weight: 680; transition: background .16s ease, transform .16s ease; }
        .hero-button:hover { transform: translateY(-1px); color: #fff; background: #4136b5; }
        .hero-button-secondary { border-color: #e2e4eb; color: #49546a; background: #fff; }
        .hero-button-secondary:hover { border-color: #c9c4ff; color: #5145cd; background: #faf9ff; }
        .hero-art { position: relative; display: grid; min-height: 300px; place-items: center; }
        .hero-orbit { position: absolute; width: min(320px, 80%); aspect-ratio: 1; border: 1px solid rgba(81,69,205,.12); border-radius: 50%; box-shadow: 0 0 0 28px rgba(81,69,205,.025), 0 0 0 58px rgba(81,69,205,.02); }
        .study-card { position: relative; z-index: 1; width: min(310px, 90%); padding: 22px; transform: rotate(-3deg); border: 1px solid rgba(226,228,240,.9); border-radius: 17px; background: rgba(255,255,255,.93); box-shadow: 0 20px 55px rgba(47,43,112,.15); }
        .study-card-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 20px; }
        .study-icon { display: grid; width: 38px; height: 38px; place-items: center; border-radius: 11px; color: #5145cd; background: #efedff; font-size: 18px; }
        .study-label { color: #9098a8; font-size: 10px; font-weight: 650; }
        .study-card h2 { margin: 0 0 6px; font-size: 18px; }
        .study-card > p { margin: 0 0 18px; font-size: 12px; }
        .study-line { height: 8px; margin: 9px 0; border-radius: 99px; background: #edf0f5; }
        .study-line:first-of-type { width: 88%; }
        .study-line:nth-of-type(2) { width: 70%; }
        .study-line:nth-of-type(3) { width: 78%; }
        .study-tag { display: inline-flex; margin-top: 9px; padding: 6px 9px; border-radius: 7px; color: #168257; background: #eaf8f0; font-size: 10px; font-weight: 680; }
        .floating-note { position: absolute; z-index: 2; right: -2%; bottom: 18%; display: flex; align-items: center; gap: 10px; padding: 12px 14px; transform: rotate(4deg); border: 1px solid #ececf4; border-radius: 12px; background: #fff; box-shadow: 0 12px 30px rgba(31,38,66,.1); color: #49546a; font-size: 11px; font-weight: 650; }
        .floating-note span { display: grid; width: 27px; height: 27px; place-items: center; border-radius: 8px; color: #b36a21; background: #fff3e2; }
        .feature-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 16px; margin-top: 24px; }
        .feature-card { padding: 22px; border: 1px solid #e6eaf0; border-radius: 15px; background: #fff; box-shadow: 0 8px 24px rgba(22,32,51,.035); }
        .feature-icon { display: grid; width: 38px; height: 38px; place-items: center; border-radius: 11px; color: #5145cd; background: #efedff; font-size: 17px; }
        .feature-card h2 { margin: 15px 0 7px; font-size: 15px; }
        .feature-card p { margin: 0; font-size: 12px; line-height: 1.7 !important; }
        .welcome-admin { margin: 22px 0 0; color: #8b93a2 !important; font-size: 11px; text-align: center; }
        .welcome-admin a { color: #778094; text-decoration: underline; text-underline-offset: 3px; }
        @media (max-width: 760px) {
            .welcome-page { padding-top: 0; }
            .hero { min-height: 0; grid-template-columns: 1fr; gap: 4px; border-radius: 18px; }
            .hero-art { min-height: 270px; }
            .floating-note { right: 0; bottom: 13%; }
            .feature-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="welcome-page">
        <section class="hero" aria-labelledby="welcome-heading">
            <div class="hero-copy">
                <p class="hero-kicker"><span></span> YOUR PERSONAL STUDY SPACE</p>
                <h1 id="welcome-heading">Study smarter.<br><em>Remember more.</em></h1>
                <p class="hero-description">A calmer place for every subject, note, and idea. Keep your learning organized so you can focus on what matters.</p>
                <div class="hero-actions">
                    @auth('web')
                        <a class="hero-button" href="{{ route('dashboard') }}">Open my workspace</a>
                    @else
                        <a class="hero-button" href="{{ route('register') }}">Get started for free</a>
                        <a class="hero-button hero-button-secondary" href="{{ route('login') }}">Sign in</a>
                    @endauth
                </div>
            </div>
            <div class="hero-art" aria-hidden="true">
                <div class="hero-orbit"></div>
                <div class="study-card">
                    <div class="study-card-top">
                        <span class="study-icon">✳</span>
                        <span class="study-label">YOUR SUBJECTS</span>
                    </div>
                    <h2>Biology</h2>
                    <p>Cell structure &amp; function</p>
                    <div class="study-line"></div>
                    <div class="study-line"></div>
                    <div class="study-line"></div>
                    <span class="study-tag">12 notes organized</span>
                </div>
                <div class="floating-note"><span>✎</span> New idea saved</div>
            </div>
        </section>

        <section class="feature-grid" aria-label="StudySpace features">
            <article class="feature-card">
                <span class="feature-icon" aria-hidden="true">▤</span>
                <h2>Organize by subject</h2>
                <p>Keep each class and topic neatly grouped, so your study materials are easy to find.</p>
            </article>
            <article class="feature-card">
                <span class="feature-icon" aria-hidden="true">✎</span>
                <h2>Notes that stay together</h2>
                <p>Write, update, and revisit your notes alongside the files that support your learning.</p>
            </article>
            <article class="feature-card">
                <span class="feature-icon" aria-hidden="true">⌁</span>
                <h2>Your space, your pace</h2>
                <p>Come back anytime and continue right where your curiosity takes you.</p>
            </article>
        </section>
        @guest('admin')
            <p class="welcome-admin"><a href="{{ route('admin.login') }}">Administrator sign in</a></p>
        @endguest
    </div>
</x-layout>
