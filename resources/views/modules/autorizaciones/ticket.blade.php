@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#fcfdfe] p-4 md:p-8 lg:p-12 animate-in fade-in duration-1000">
    
    <!-- Header: Glass Console Style -->
    <div class="relative mb-12 flex flex-col md:flex-row md:items-end justify-between gap-8 pb-8 border-b border-slate-100">
        <div class="space-y-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-[#00346f] rounded-2xl flex items-center justify-center text-white shadow-[0_20px_40px_-10px_rgba(0,52,111,0.3)] rotate-3">
                    <i class="ph-fill ph-paper-plane-tilt text-2xl"></i>
                </div>
                <div class="h-6 w-px bg-slate-200"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-[#00346f]">Terminal 01 • Live</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tightest">
                Enlace de <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00346f] to-[#0060ac]">Carnetización</span>
            </h1>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden sm:flex -space-x-3">
                @foreach($misTickets->where('operador_id', '!=', null)->take(4) as $t)
                    <img title="{{ $t->operador->name }}" src="https://ui-avatars.com/api/?name={{ urlencode($t->operador->name) }}&background=00346f&color=fff" class="w-10 h-10 rounded-full border-4 border-white shadow-sm transition-transform hover:scale-110 hover:z-20">
                @endforeach
                @if($misTickets->where('operador_id', '!=', null)->count() > 4)
                    <div class="w-10 h-10 rounded-full bg-slate-100 border-4 border-white flex items-center justify-center text-[10px] font-black text-slate-400">
                        +{{ $misTickets->where('operador_id', '!=', null)->count() - 4 }}
                    </div>
                @endif
            </div>
            <a href="{{ route('portal') }}" class="flex items-center gap-3 px-8 py-4 bg-white border border-slate-200 text-slate-900 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-[#00346f] hover:text-white transition-all shadow-sm active:scale-95 group">
                <i class="ph-bold ph-squares-four text-lg group-hover:rotate-180 transition-transform duration-700"></i>
                Dashboard
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- Center Column: The Hub (8/12) -->
        <div class="lg:col-span-8 space-y-12">
            
            <!-- Quick Glance Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @php
                    $stats = [
                        ['label' => 'Total Hoy', 'value' => $misTickets->count(), 'icon' => 'ph-list-bullets', 'color' => '[#00346f]'],
                        ['label' => 'Pendientes', 'value' => $misTickets->where('operador_id', null)->count(), 'icon' => 'ph-clock-countdown', 'color' => 'amber'],
                        ['label' => 'En Proceso', 'value' => $misTickets->where('operador_id', '!=', null)->where('estado.nombre', '!=', 'Completado')->count(), 'icon' => 'ph-headset', 'color' => 'blue'],
                        ['label' => 'Finalizados', 'value' => $misTickets->where('estado.nombre', 'Completado')->count(), 'icon' => 'ph-check-circle', 'color' => 'emerald'],
                    ];
                @endphp
                @foreach($stats as $s)
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500">
                    <div class="w-10 h-10 bg-{{ $s['color'] }} text-{{ $s['color'] }} rounded-xl flex items-center justify-center mb-4 bg-opacity-10">
                        <i class="ph-bold {{ $s['icon'] }} text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $s['label'] }}</p>
                    <p class="text-2xl font-black text-slate-900 leading-none">{{ $s['value'] }}</p>
                </div>
                @endforeach
            </div>

            <!-- Active Monitoring List -->
            <div class="space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 px-2">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Monitor de Operaciones</h3>
                    
                    <!-- Buscador en Vivo -->
                    <form action="{{ route('autorizaciones.ticket.create') }}" method="GET" class="relative group w-full md:w-72">
                        <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-[#00346f] transition-colors"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar nombre o 00000-000000-00..." 
                               class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-[#00346f]/10 focus:border-[#00346f] font-bold text-xs text-slate-700 transition-all shadow-sm">
                    </form>
                </div>

                <div class="space-y-4">
                    @forelse($misTickets as $ticket)
                    <div class="group bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-[#00346f]/10 transition-all duration-700 flex flex-col md:flex-row md:items-center justify-between gap-8 relative overflow-hidden">
                        <!-- Progress Indicator Bar (Vertical on Hover) -->
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 transition-all duration-500 group-hover:w-2" style="background-color: {{ $ticket->estado->color }}; shadow: 0 0 10px {{ $ticket->estado->color }};"></div>

                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 rounded-3xl flex items-center justify-center font-black text-2xl transition-all group-hover:rotate-6 shadow-lg shadow-blue-100"
                                style="background-color: {{ $ticket->estado->color }}10; color: {{ $ticket->estado->color }}; shadow: 0 10px 20px -5px {{ $ticket->estado->color }}30;">
                                {{ substr($ticket->nombre, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-xl font-black text-slate-900 tracking-tight leading-none mb-3 group-hover:text-[#00346f] transition-colors">{{ $ticket->nombre }}</h4>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="text-[10px] font-black text-slate-400 bg-slate-50 px-3 py-1 rounded-full border border-slate-100 uppercase">PÓLIZA: {{ $ticket->poliza }}</span>
                                    <span class="text-[10px] font-black text-slate-300">•</span>
                                    <span class="text-[10px] font-bold text-slate-400 italic">Enviado {{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Lifecycle Stepper -->
                        <div class="flex items-center gap-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                            @php
                                $steps = [
                                    ['n' => 'Recibido', 'on' => true],
                                    ['n' => 'Asignado', 'on' => $ticket->operador_id != null],
                                    ['n' => 'Finalizado', 'on' => $ticket->estado->nombre == 'Completado']
                                ];
                            @endphp
                            @foreach($steps as $index => $step)
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full {{ $step['on'] ? 'bg-[#00346f] shadow-[0_0_8px_rgba(0,52,111,0.5)]' : 'bg-slate-200' }}"></div>
                                    <span class="text-[8px] font-black uppercase tracking-widest {{ $step['on'] ? 'text-slate-900' : 'text-slate-300' }}">{{ $step['n'] }}</span>
                                </div>
                                @if(!$loop->last)
                                    <div class="w-6 h-px bg-slate-200"></div>
                                @endif
                            @endforeach
                        </div>

                        <div class="flex flex-col md:items-end gap-3">
                            @if($ticket->operador_id)
                                <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-sm">
                                    <div class="w-6 h-6 rounded-full overflow-hidden border-2 border-blue-100">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->operador->name) }}&background=00346f&color=fff" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-[9px] font-black text-slate-900 uppercase tracking-widest">{{ $ticket->operador->name }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-500 rounded-xl border border-rose-100 animate-pulse">
                                    <i class="ph-bold ph-clock-countdown text-sm"></i>
                                    <span class="text-[9px] font-black uppercase tracking-widest">En Cola</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="py-24 text-center bg-white rounded-[40px] border-2 border-dashed border-slate-100">
                        <i class="ph-bold ph-planet text-6xl text-slate-100 mb-6 block"></i>
                        <h3 class="text-xl font-black text-slate-900">Sistema en Silencio</h3>
                        <p class="text-slate-400 font-medium max-w-xs mx-auto mt-2 italic text-sm">Todo está al día. Inicia una nueva derivación para activar el radar.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Command Unit (4/12) -->
        <div class="lg:col-span-4">
            <div class="sticky top-12 bg-white rounded-[48px] border border-slate-100 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.05)] overflow-hidden">
                <div class="p-10 bg-[#00346f] text-white relative overflow-hidden">
                    <!-- Tech Background Decoration -->
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <svg width="100%" height="100%"><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="white" stroke-width="0.5"/></pattern><rect width="100%" height="100%" fill="url(#grid)"/></svg>
                    </div>
                    <div class="relative z-10">
                        <h5 class="text-2xl font-black tracking-tight mb-2">Comando de Despacho</h5>
                        <p class="text-blue-100 text-xs font-bold leading-relaxed">Inicia un nuevo enlace de carnetización con el Call Center.</p>
                    </div>
                </div>

                <div class="p-10 space-y-8">
                    @if(session('success'))
                    <div class="p-6 rounded-[24px] bg-emerald-50 border border-emerald-100 flex items-center gap-4 text-emerald-700 animate-in bounce-in duration-500">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                            <i class="ph-fill ph-check-circle text-2xl"></i>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest leading-snug">{{ session('success') }}</p>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="p-6 rounded-[24px] bg-rose-50 border border-rose-100 flex items-center gap-4 text-rose-700 animate-in shake-x duration-500">
                        <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center">
                            <i class="ph-fill ph-warning-circle text-2xl"></i>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest leading-snug">{{ session('error') }}</p>
                    </div>
                    @endif

                    <form action="{{ route('autorizaciones.ticket.store') }}" method="POST" class="space-y-8">
                        @csrf
                        
                        <div class="space-y-3 group">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 ml-1">Número de Póliza</label>
                            <input type="text" name="poliza" value="{{ old('poliza') }}" required
                                class="w-full px-6 py-5 bg-slate-50 border-none rounded-[22px] outline-none ring-2 ring-transparent focus:ring-[#00346f]/20 focus:bg-white transition-all font-black text-slate-900 placeholder:text-slate-300 text-sm shadow-inner"
                                placeholder="Eje: 00000-000000-00" maxlength="50">
                        </div>

                        <div class="space-y-3 group">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 ml-1">Nombre del Afiliado</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" required
                                class="w-full px-6 py-5 bg-slate-50 border-none rounded-[22px] outline-none ring-2 ring-transparent focus:ring-[#00346f]/20 focus:bg-white transition-all font-black text-slate-900 placeholder:text-slate-300 text-sm shadow-inner"
                                placeholder="Escribe el nombre completo">
                        </div>

                        <div class="space-y-3 group">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 ml-1">Línea de Contacto</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" required
                                class="w-full px-6 py-5 bg-slate-50 border-none rounded-[22px] outline-none ring-2 ring-transparent focus:ring-[#00346f]/20 focus:bg-white transition-all font-black text-slate-900 placeholder:text-slate-300 text-sm shadow-inner"
                                placeholder="809-000-0000">
                        </div>

                        <div class="space-y-3 group">
                            <label class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 ml-1">Observaciones Críticas</label>
                            <textarea name="notas" required rows="4"
                                class="w-full p-6 bg-slate-50 border-none rounded-[22px] outline-none ring-2 ring-transparent focus:ring-[#00346f]/20 focus:bg-white transition-all font-bold text-slate-700 resize-none placeholder:text-slate-300 text-sm shadow-inner"
                                placeholder="Notas para el operador...">{{ old('notas') }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-6 bg-slate-900 text-white font-black uppercase tracking-[0.3em] text-[10px] rounded-[24px] hover:bg-[#00346f] transition-all shadow-2xl shadow-slate-900/20 active:scale-95 flex items-center justify-center gap-4 group">
                            Enviar Solicitud
                            <i class="ph-bold ph-paper-plane-tilt text-lg group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@800&display=swap');
    
    .tracking-tightest { letter-spacing: -0.05em; }
    
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #e2e8f0; }

    input:focus::placeholder, textarea:focus::placeholder {
        opacity: 0;
        transform: translateX(10px);
        transition: all 0.3s ease;
    }
</style>
@endsection
