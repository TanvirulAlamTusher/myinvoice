<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --teal-deep:    #0d3d3a;
            --teal-mid:     #0f5c57;
            --teal-bright:  #0d9488;
            --teal-glow:    #2dd4bf;
        }

        body {
            font-family: ui-sans-serif, system-ui;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: var(--teal-deep);
            margin: 0;
        }

        .card {
            width: 420px;
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(45,212,191,.2);
            border-radius: 20px;
            padding: 40px;
            color: white;
        }

        .field { margin-bottom: 15px; }

        label {
            font-size: 12px;
            text-transform: uppercase;
            color: #2dd4bf;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 10px;
            border: 1px solid rgba(45,212,191,.3);
            background: rgba(0,0,0,.2);
            color: white;
        }

        .error {
            background: rgba(255,0,0,.1);
            border: 1px solid rgba(255,0,0,.3);
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 10px;
            font-size: 12px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg,#0f5c57,#0d9488,#2dd4bf);
            color: white;
            cursor: pointer;
        }

        a { color: #2dd4bf; text-decoration: none; }
    </style>
</head>

<body>

<div class="card">

    <h2 style="text-align:center;margin-bottom:20px;">Create Account</h2>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- NAME --}}
        <div class="field">
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>

        {{-- PHONE --}}
        <div class="field">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required>
        </div>

        {{-- EMAIL --}}
        <div class="field">
            <label>Email (optional)</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>

        {{-- PASSWORD --}}
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        {{-- CONFIRM PASSWORD --}}
        <div class="field">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" required>
        </div>

        <button type="submit">Register</button>
    </form>

    <p style="text-align:center;margin-top:15px;">
        Already have account? <a href="{{ route('login') }}">Login</a>
    </p>

</div>

</body>
</html>
