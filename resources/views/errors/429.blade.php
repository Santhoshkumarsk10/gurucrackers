<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Too Many Requests | Guru Crackers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-amber-50 via-rose-50 to-orange-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-rose-100 p-8 text-center relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-36 h-36 bg-rose-200/40 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-12 -left-12 w-36 h-36 bg-amber-200/40 rounded-full blur-2xl"></div>

        <div class="relative z-10">
            <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-4xl shadow-inner border border-amber-200">
                <i class="fa-solid fa-hourglass-half animate-pulse"></i>
            </div>

            <span class="inline-block px-3 py-1 bg-amber-100/80 text-amber-800 text-xs font-black rounded-full uppercase tracking-wider mb-2">
                Rate Limit Exceeded (HTTP 429)
            </span>

            <h1 class="text-2xl font-black text-slate-800 mb-2">
                Slow Down / சற்று பொறுங்கள்
            </h1>

            <p class="text-sm text-slate-600 leading-relaxed mb-6">
                You have made too many requests in a short period. Please wait a minute and try again.
                <br>
                <span class="text-xs text-slate-500 font-medium mt-1 block">
                    குறுகிய நேரத்தில் அதிக கோரிக்கைகள் அனுப்பப்பட்டுள்ளன. 1 நிமிடம் கழித்து மீண்டும் முயற்சிக்கவும்.
                </span>
            </p>

            <div class="space-y-2.5">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 w-full py-3 px-5 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 text-white font-bold text-sm shadow-lg shadow-rose-500/25 hover:from-rose-700 hover:to-amber-700 transition-all">
                    <i class="fa-solid fa-house"></i> Return to Shop
                </a>
                <button onclick="window.location.reload()" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-all">
                    <i class="fa-solid fa-rotate-right"></i> Try Again Now
                </button>
            </div>
        </div>
    </div>
</body>
</html>
