<aside class="h-screen w-64 fixed left-0 top-0 border-r-0 bg-slate-50 flex flex-col py-6 z-50">
    <div class="px-8 mb-10 flex items-center gap-3 group">
        <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white shadow-lg shadow-slate-900/20 group-hover:scale-110 transition-transform duration-300">
            <span class="material-symbols-outlined text-2xl">shield</span>
        </div>
        <div>
            <h1 class="font-black text-slate-900 text-lg leading-none tracking-tighter">Enterprise</h1>
            <p class="text-[0.625rem] font-bold text-slate-400 uppercase tracking-widest mt-1">Core System</p>
        </div>
    </div>

    <nav class="flex-1 space-y-1 px-4 overflow-y-auto custom-scrollbar">
        {{ $slot }}
    </nav>

    <div class="px-6 mt-auto pt-6 border-t border-slate-100">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-2 bg-white rounded-xl hover:bg-slate-100 transition-colors group">
            <img src="{{ Auth::user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-slate-200" alt="Avatar">
            <div class="overflow-hidden">
                <p class="text-[0.75rem] font-bold text-slate-900 truncate group-hover:text-primary">{{ Auth::user()->name }}</p>
                <p class="text-[0.6rem] text-slate-500 truncate italic">Mi Perfil</p>
            </div>
            <span class="material-symbols-outlined text-slate-300 text-sm ml-auto group-hover:translate-x-1 transition-transform">chevron_right</span>
        </a>
    </div>
</aside>
