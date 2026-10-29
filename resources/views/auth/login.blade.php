<x-layout :title="$guard === 'admin' ? 'Admin sign in' : 'Sign in'">
    <style>
        .auth-wrap { width: min(940px, 100%); margin: 12px auto; }
        .auth-card {
            display: grid;
            min-height: 540px;
            overflow: hidden;
            grid-template-columns: .92fr 1.08fr;
            border: 1px solid #e6eaf0;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 24px 70px rgba(25, 31, 53, .09);
        }
        .auth-aside {
            position: relative;
            display: flex;
            overflow: hidden;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(28px, 5vw, 48px);
            color: #fff;
            background:
                radial-gradient(ellipse at 82% 10%, rgba(179, 168, 255, .52), transparent 35%),
                linear-gradient(145deg, #37318f 0%, #5145cd 53%, #7569e8 100%);
        }
        .auth-aside::after {
            position: absolute;
            right: -72px;
            bottom: -118px;
            width: 290px;
            height: 290px;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 50%;
            box-shadow: 0 0 0 26px rgba(255,255,255,.035), 0 0 0 54px rgba(255,255,255,.025);
            content: "";
        }
        .auth-eyebrow { margin: 0 0 20px; color: #d5d1ff !important; font-size: 11px; font-weight: 750; letter-spacing: .15em; text-transform: uppercase; }
        .auth-aside h2 { max-width: 330px; margin: 0; color: #fff !important; font-size: clamp(30px, 4vw, 42px); line-height: 1.1; letter-spacing: -.045em; }
        .auth-aside-copy { max-width: 300px; margin: 18px 0 0; color: #e1dfff !important; font-size: 14px; line-height: 1.75 !important; }
        .auth-quote { position: relative; z-index: 1; margin: 40px 0 0; color: #eeecff !important; font-size: 12px; line-height: 1.7 !important; }
        .auth-panel { display: flex; flex-direction: column; justify-content: center; padding: clamp(28px, 5vw, 58px); }
        .auth-panel h1 { margin: 0; font-size: 30px; letter-spacing: -.04em; }
        .auth-subtitle { margin: 9px 0 28px; color: #788195 !important; font-size: 13px; }
        .auth-form { display: grid; gap: 17px; }
        .auth-field { display: grid; gap: 7px; margin: 0 !important; color: #384258 !important; font-size: 12px !important; font-weight: 680 !important; }
        .auth-field input {
            width: 100%;
            min-height: 48px !important;
            margin: 0 !important;
            padding: 0 14px !important;
            border-color: #e0e4ec !important;
            border-radius: 10px !important;
            background: #fbfcfe !important;
            font-size: 13px;
            transition: border-color .16s ease, box-shadow .16s ease;
        }
        .auth-field input:focus { border-color: #8177ee !important; box-shadow: 0 0 0 4px rgba(81, 69, 205, .1); outline: none; }
        .auth-error { margin: 0; color: #bd3434 !important; font-size: 12px; font-weight: 500; }
        .auth-alert { margin: 0; padding: 12px 14px; border: 1px solid #ffd1d1; border-radius: 10px; color: #a33131; background: #fff5f5; font-size: 12px; }
        .auth-remember { display: flex; align-items: center; gap: 9px; margin: 0 !important; color: #596276 !important; font-size: 12px !important; font-weight: 520 !important; cursor: pointer; }
        .auth-remember input { width: 16px; height: 16px; margin: 0; accent-color: #5145cd; }
        .auth-submit { width: 100%; min-height: 48px; margin: 2px 0 0 !important; border-radius: 10px; font-size: 13px; }
        .auth-help { margin: 20px 0 0; color: #7c8495 !important; font-size: 12px; text-align: center; }
        .auth-help a { font-weight: 680; }
        .auth-divider { display: flex; align-items: center; gap: 12px; margin: 21px 0 0; color: #a1a7b3; font-size: 10px; }
        .auth-divider::before, .auth-divider::after { height: 1px; flex: 1; background: #eceef3; content: ""; }
        .auth-switch { display: flex; min-height: 43px; align-items: center; justify-content: center; margin-top: 12px; border: 1px solid #e5e7ee; border-radius: 10px; color: #4e5870; font-size: 12px; font-weight: 620; transition: border-color .16s ease, color .16s ease, background .16s ease; }
        .auth-switch:hover { border-color: #c9c4ff; color: #5145cd; background: #faf9ff; }

        @media (max-width: 680px) {
            .auth-wrap { margin: 0 auto; }
            .auth-card { min-height: auto; grid-template-columns: 1fr; border-radius: 18px; }
            .auth-aside { min-height: 195px; padding: 25px; }
            .auth-aside h2 { max-width: 450px; font-size: 29px; }
            .auth-aside-copy { max-width: 430px; margin-top: 10px; font-size: 12px; }
            .auth-quote { display: none; }
            .auth-panel { padding: 28px 24px 32px; }
            .auth-panel h1 { font-size: 27px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .auth-field input, .auth-switch { transition: none; }
        }
    </style>

    <div class="auth-wrap">
        <div class="auth-card">
            <aside class="auth-aside" aria-label="Welcome">
                <div>
                    <p class="auth-eyebrow">{{ $guard === 'admin' ? 'Administrator access' : 'Your learning space' }}</p>
                    <h2>{{ $guard === 'admin' ? 'Keep your community on track.' : 'Make every study session count.' }}</h2>
                    <p class="auth-aside-copy">
                        {{ $guard === 'admin'
                            ? 'Sign in to oversee users, organize learning materials, and keep the platform running smoothly.'
                            : 'Bring your subjects, notes, and study materials together in one calm, organized workspace.' }}
                    </p>
                </div>
                <p class="auth-quote">A little progress each day adds up to big results.</p>
            </aside>

            <section class="auth-panel" aria-labelledby="login-title">
                <h1 id="login-title">{{ $guard === 'admin' ? 'Welcome back, admin' : 'Welcome back' }}</h1>
                <p class="auth-subtitle">Enter your details to continue to your account.</p>

                @if (session('status'))
                    <p class="status-message" role="status">{{ session('status') }}</p>
                @endif

                @if ($errors->any())
                    <div class="auth-alert" role="alert" aria-live="polite">
                        <strong>We couldn't sign you in.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="auth-form" method="post" action="{{ $guard === 'admin' ? route('admin.login.store') : route('login.store') }}">
                    @csrf
                    <label class="auth-field" for="email">
                        Email address
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            maxlength="255"
                            autocomplete="username"
                            placeholder="you@example.com"
                            inputmode="email"
                            autofocus
                            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                            @if ($errors->has('email')) aria-describedby="email-error" @endif
                        >
                        @error('email') <span class="auth-error" id="email-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="auth-field" for="password">
                        Password
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            @if ($errors->has('password')) aria-describedby="password-error" @endif
                        >
                        @error('password') <span class="auth-error" id="password-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="auth-remember">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Keep me signed in</span>
                    </label>

                    <button class="auth-submit" type="submit">Sign in to {{ $guard === 'admin' ? 'admin panel' : 'your workspace' }}</button>
                </form>

                @if ($guard === 'web')
                    <p class="auth-help">New here? <a href="{{ route('register') }}">Create a free account</a></p>
                @endif

                <div class="auth-divider"><span>OR</span></div>
                <a class="auth-switch" href="{{ $guard === 'admin' ? route('login') : route('admin.login') }}">
                    Sign in as {{ $guard === 'admin' ? 'a user' : 'an administrator' }} instead
                </a>
            </section>
        </div>
    </div>
</x-layout>
