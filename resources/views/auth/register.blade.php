<x-layout title="Create your account">
    <style>
        .register-wrap { width: min(900px, 100%); margin: 12px auto; }
        .register-card {
            display: grid;
            min-height: 510px;
            overflow: hidden;
            grid-template-columns: .9fr 1.1fr;
            border: 1px solid #e6eaf0;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 24px 70px rgba(25, 31, 53, .09);
        }
        .register-aside {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(28px, 5vw, 46px);
            color: #fff;
            background: radial-gradient(ellipse at 80% 12%, rgba(179,168,255,.5), transparent 36%), linear-gradient(145deg,#37318f,#5145cd 55%,#7569e8);
        }
        .register-aside p { color: #e1dfff !important; font-size: 13px; line-height: 1.75 !important; }
        .register-aside .register-kicker { color: #d5d1ff !important; font-size: 11px; font-weight: 750; letter-spacing: .15em; text-transform: uppercase; }
        .register-aside h2 { margin: 20px 0 0; color: #fff !important; font-size: clamp(30px,4vw,40px); line-height: 1.1; letter-spacing: -.045em; }
        .register-aside .register-note { margin: 45px 0 0; }
        .register-form-panel { display: flex; flex-direction: column; justify-content: center; padding: clamp(28px,5vw,52px); }
        .register-form-panel h1 { margin: 0; font-size: 29px; letter-spacing: -.04em; }
        .register-subtitle { margin: 9px 0 24px; color: #788195 !important; font-size: 13px; }
        .register-form { display: grid; gap: 14px; }
        .register-field { display: grid; gap: 7px; margin: 0 !important; color: #384258 !important; font-size: 12px !important; font-weight: 680 !important; }
        .register-field input { min-height: 45px !important; margin: 0 !important; padding: 0 13px !important; border-color: #e0e4ec !important; border-radius: 9px !important; background: #fbfcfe !important; font-size: 13px; }
        .register-field input:focus { border-color: #8177ee !important; box-shadow: 0 0 0 4px rgba(81,69,205,.1); outline: none; }
        .register-error { color: #bd3434 !important; font-size: 12px; font-weight: 500; }
        .register-submit { width: 100%; min-height: 47px; margin: 4px 0 0 !important; border-radius: 10px; font-size: 13px; }
        .register-login { margin: 18px 0 0; color: #7c8495 !important; font-size: 12px; text-align: center; }
        .register-login a { font-weight: 680; }
        .register-alert { margin: 0; padding: 11px 14px; border: 1px solid #ffd1d1; border-radius: 10px; color: #a33131; background: #fff5f5; font-size: 12px; }
        @media (max-width: 680px) {
            .register-wrap { margin: 0 auto; }
            .register-card { min-height: 0; grid-template-columns: 1fr; border-radius: 18px; }
            .register-aside { padding: 24px; }
            .register-aside h2 { margin-top: 12px; font-size: 29px; }
            .register-aside .register-note { margin-top: 14px; }
            .register-form-panel { padding: 27px 24px 32px; }
        }
    </style>

    <div class="register-wrap">
        <div class="register-card">
            <aside class="register-aside">
                <div>
                    <p class="register-kicker">A better way to study</p>
                    <h2>Build a space for your best ideas.</h2>
                    <p>Organize each subject, keep your notes close, and make it easier to pick up where you left off.</p>
                </div>
                <p class="register-note">Your next great study session starts here.</p>
            </aside>

            <section class="register-form-panel" aria-labelledby="register-title">
                <h1 id="register-title">Create your account</h1>
                <p class="register-subtitle">It only takes a minute to get started.</p>
                @if ($errors->any())
                    <div class="register-alert" role="alert">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <form class="register-form" method="post" action="{{ route('register.store') }}">
                    @csrf
                    <label class="register-field" for="name">
                        Full name
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="name" placeholder="Your name" autofocus>
                        @error('name') <span class="register-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="register-field" for="email">
                        Email address
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" placeholder="you@example.com" inputmode="email">
                        @error('email') <span class="register-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="register-field" for="password">
                        Password
                        <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="At least 8 characters">
                        @error('password') <span class="register-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="register-field" for="password_confirmation">
                        Confirm password
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" placeholder="Enter your password again">
                    </label>
                    <button class="register-submit" type="submit">Create account</button>
                </form>
                <p class="register-login">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
            </section>
        </div>
    </div>
</x-layout>
