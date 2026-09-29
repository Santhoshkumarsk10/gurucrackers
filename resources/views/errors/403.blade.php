<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden | Guru Crackers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-red-50 via-slate-50 to-orange-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-red-100 p-8 text-center relative overflow-hidden">
        <div class="relative z-10">
            <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-4xl shadow-inner border border-red-200">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <span class="inline-block px-3 py-1 bg-red-100 text-red-800 text-xs font-black rounded-full uppercase tracking-wider mb-2">
                HTTP 403 Forbidden
            </span>

            <h1 class="text-2xl font-black text-slate-800 mb-2">
                Access Denied / அனுமதி மறுக்கப்பட்டது
            </h1>

            <p class="text-sm text-slate-600 leading-relaxed mb-6">
                You do not have administrative permission to view this resource.
                <br>
                <span class="text-xs text-slate-500 font-medium mt-1 block">
                    இந்த பக்கத்தை அணுக உங்களுக்கு அனுமதி இல்லை.
                </span>
            </p>

            <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 w-full py-3 px-5 rounded-xl bg-slate-900 text-white font-bold text-sm shadow-lg hover:bg-slate-800 transition-all">
                <i class="fa-solid fa-house"></i> Return to Shop
            </a>
        </div>
    </div>
</body>
</html>
