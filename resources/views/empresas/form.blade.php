<div class="space-y-8">
    {{-- Main Identity Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-slate-50/50 p-8 rounded-3xl border border-slate-100/50">
        <div class="col-span-1 md:col-span-2">
            <label for="nombre" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Nombre de la Entidad <span class="text-error">*</span></label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                    <span class="material-symbols-outlined text-lg">corporate_fare</span>
                </div>
                <input type="text" name="nombre" id="nombre" 
                    class="w-full pl-12 pr-4 py-3.5 bg-white border-slate-200 rounded-2xl focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-on-surface shadow-sm @error('nombre') border-error @enderror" 
                    value="{{ old('nombre', $empresa->nombre ?? '') }}" 
                    placeholder="Ej. SafeSure Dominicana S.A."
                    required>
            </div>
            @error('nombre')
                <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                </p>
            @enderror
        </div>

        <div class="col-span-1">
            <label for="rnc" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">RNC / Identificación Fiscal</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                    <span class="material-symbols-outlined text-lg">fingerprint</span>
                </div>
                <input type="text" name="rnc" id="rnc" 
                    class="w-full pl-12 pr-4 py-3.5 bg-white border-slate-200 rounded-2xl focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-on-surface shadow-sm @error('rnc') border-error @enderror" 
                    value="{{ old('rnc', $empresa->rnc ?? '') }}"
                    placeholder="9-15 dígitos numéricos">
            </div>
            @error('rnc')
                <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                </p>
            @enderror
        </div>

        <div class="col-span-1">
            <label for="telefono" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Teléfono Central</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                    <span class="material-symbols-outlined text-lg">call</span>
                </div>
                <input type="text" name="telefono" id="telefono" 
                    class="w-full pl-12 pr-4 py-3.5 bg-white border-slate-200 rounded-2xl focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-on-surface shadow-sm @error('telefono') border-error @enderror" 
                    value="{{ old('telefono', $empresa->telefono ?? '') }}"
                    placeholder="809-000-0000">
            </div>
            @error('telefono')
                <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    {{-- Location Section --}}
    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-1">
                <label for="provincia_id" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Provincia</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                        <span class="material-symbols-outlined text-lg">map</span>
                    </div>
                    <select name="provincia_id" id="provincia_id" 
                        class="w-full pl-12 pr-4 py-3.5 bg-slate-50/30 border-slate-200 rounded-2xl focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-on-surface shadow-inner appearance-none @error('provincia_id') border-error @enderror">
                        <option value="">Seleccione Provincia</option>
                        @foreach($provincias as $p)
                            <option value="{{ $p->id }}" {{ old('provincia_id', $empresa->provincia_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('provincia_id')
                    <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="col-span-1">
                <label for="municipio_id" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Municipio / Sector</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                        <span class="material-symbols-outlined text-lg">location_city</span>
                    </div>
                    <select name="municipio_id" id="municipio_id" 
                        class="w-full pl-12 pr-4 py-3.5 bg-slate-50/30 border-slate-200 rounded-2xl focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-on-surface shadow-inner appearance-none @error('municipio_id') border-error @enderror">
                        <option value="">Seleccione Municipio</option>
                        @foreach($municipios as $m)
                            <option value="{{ $m->id }}" {{ old('municipio_id', $empresa->municipio_id) == $m->id ? 'selected' : '' }}>
                                {{ $m->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('municipio_id')
                    <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <div>
            <label for="direccion" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-1 px-1">Dirección Detallada (Calle, Edificio, etc.)</label>
            <div class="relative group">
                <div class="absolute top-4 left-4 flex items-start pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                    <span class="material-symbols-outlined text-lg">location_on</span>
                </div>
                <textarea name="direccion" id="direccion" rows="3" 
                    class="w-full pl-12 pr-4 py-3.5 bg-slate-50/30 border-slate-200 rounded-2xl focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-bold text-on-surface shadow-inner min-h-[100px] @error('direccion') border-error @enderror"
                    placeholder="Calle #, Sector, Edificio...">{{ old('direccion', $empresa->direccion ?? '') }}</textarea>
            </div>
            @error('direccion')
                <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    {{-- Map Picker Section --}}
    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
        <div class="pb-2">
            <label for="google_maps_url" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Enlace de Google Maps (Pegar para geocodificar)</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                    <span class="material-symbols-outlined text-lg">link</span>
                </div>
                <input type="text" name="google_maps_url" id="google_maps_url" 
                    class="w-full pl-12 pr-4 py-3.5 bg-slate-50/30 border-slate-200 rounded-2xl focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm font-bold text-on-surface shadow-inner @error('google_maps_url') border-error @enderror" 
                    value="{{ old('google_maps_url', $empresa->google_maps_url ?? '') }}" 
                    placeholder="Pegue aquí el link de Google Maps: https://www.google.com/maps/@18.4,-69.9...">
            </div>
            @error('google_maps_url')
                <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                </p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-2">
            <div class="col-span-1">
                <label for="latitude" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Latitud</label>
                <input type="text" name="latitude" id="latitude" readonly
                    class="w-full px-4 py-3.5 bg-slate-50 border-slate-200 rounded-2xl text-sm font-black text-secondary focus:ring-0 cursor-default" 
                    value="{{ old('latitude', $empresa->latitude ?? '') }}" 
                    placeholder="Capture desde el mapa">
            </div>
            <div class="col-span-1">
                <label for="longitude" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Longitud</label>
                <input type="text" name="longitude" id="longitude" readonly
                    class="w-full px-4 py-3.5 bg-slate-50 border-slate-200 rounded-2xl text-sm font-black text-secondary focus:ring-0 cursor-default" 
                    value="{{ old('longitude', $empresa->longitude ?? '') }}" 
                    placeholder="Capture desde el mapa">
            </div>
        </div>

        {{-- Map Container --}}
        <div class="relative rounded-3xl overflow-hidden border border-slate-100 shadow-inner group">
            <div id="map-picker" class="h-[350px] w-full z-10"></div>
            {{-- Map Overlay Info --}}
            <div class="absolute bottom-4 left-4 z-[999] bg-white/90 backdrop-blur-md px-4 py-2 rounded-xl border border-slate-100 shadow-xl pointer-events-none">
                <p class="text-[0.6rem] font-black text-slate-800 uppercase flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-secondary rounded-full animate-pulse"></span>
                    Selector de Precisión
                </p>
            </div>
        </div>
    </div>

    {{-- Categorization Section (Toggles) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <label class="relative flex items-center p-6 rounded-3xl border border-slate-100 bg-white shadow-sm cursor-pointer hover:border-blue-500/30 hover:shadow-md hover:shadow-blue-500/5 transition-all group overflow-hidden">
            <input type="checkbox" name="es_verificada" value="1" class="hidden peer" {{ old('es_verificada', $empresa->es_verificada ?? false) ? 'checked' : '' }}>
            <div class="absolute inset-y-0 left-0 w-1 bg-blue-500 opacity-0 peer-checked:opacity-100 transition-opacity"></div>
            
            <div class="flex-1 flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 peer-checked:bg-blue-100 peer-checked:text-blue-600 transition-all">
                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-extrabold text-on-surface group-hover:text-blue-600 transition-colors">Empresa Verificada</span>
                    <span class="text-[0.65rem] font-medium text-slate-400 mt-0.5">Autorizada para salida inmediata y despacho prioritario.</span>
                </div>
            </div>
            
            <div class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-all">
                <span class="material-symbols-outlined text-white text-xs scale-0 peer-checked:scale-100 transition-transform">check</span>
            </div>
        </label>

        <label class="relative flex items-center p-6 rounded-3xl border border-slate-100 bg-white shadow-sm cursor-pointer hover:border-purple-500/30 hover:shadow-md hover:shadow-purple-500/5 transition-all group overflow-hidden">
            <input type="checkbox" name="es_filial" value="1" class="hidden peer" {{ old('es_filial', $empresa->es_filial ?? false) ? 'checked' : '' }}>
            <div class="absolute inset-y-0 left-0 w-1 bg-purple-500 opacity-0 peer-checked:opacity-100 transition-opacity"></div>
            
            <div class="flex-1 flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 peer-checked:bg-purple-100 peer-checked:text-purple-600 transition-all">
                    <span class="material-symbols-outlined text-2xl">account_tree</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-extrabold text-on-surface group-hover:text-purple-600 transition-colors">Sucursal Filial</span>
                    <span class="text-[0.65rem] font-medium text-slate-400 mt-0.5">Corresponde a una entidad interna de la ARS.</span>
                </div>
            </div>
            
            <div class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center peer-checked:border-purple-500 peer-checked:bg-purple-500 transition-all">
                <span class="material-symbols-outlined text-white text-xs scale-0 peer-checked:scale-100 transition-transform">check</span>
            </div>
        </label>
    </div>
    
    {{-- Contact Section --}}
    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
        <h3 class="text-xs font-black text-primary uppercase tracking-[0.2em] flex items-center gap-2 mb-4 px-1">
            <span class="material-symbols-outlined text-lg">person_pin</span> Información del Contacto
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-1">
                <label for="contacto_nombre" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Nombre Completo</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                        <span class="material-symbols-outlined text-lg">person</span>
                    </div>
                    <input type="text" name="contacto_nombre" id="contacto_nombre" 
                        class="w-full pl-12 pr-4 py-3.5 bg-slate-50/30 border-slate-200 rounded-2xl focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-on-surface @error('contacto_nombre') border-error @enderror" 
                        value="{{ old('contacto_nombre', $empresa->contacto_nombre ?? '') }}" 
                        placeholder="Nombre de la persona enlace">
                </div>
                @error('contacto_nombre')
                    <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                    </p>
                @enderror
            </div>
            <div class="col-span-1">
                <label for="contacto_puesto" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Puesto / Cargo</label>
                <input type="text" name="contacto_puesto" id="contacto_puesto" 
                    class="w-full px-4 py-3.5 bg-slate-50/30 border-slate-200 rounded-2xl focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-on-surface" 
                    value="{{ old('contacto_puesto', $empresa->contacto_puesto ?? '') }}" 
                    placeholder="Ej. Gerente de RRHH">
            </div>
            <div class="col-span-1">
                <label for="contacto_telefono" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Teléfono Directo / Flota</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                        <span class="material-symbols-outlined text-lg">smartphone</span>
                    </div>
                    <input type="text" name="contacto_telefono" id="contacto_telefono" 
                        class="w-full pl-12 pr-4 py-3.5 bg-slate-50/30 border-slate-200 rounded-2xl focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-on-surface" 
                        value="{{ old('contacto_telefono', $empresa->contacto_telefono ?? '') }}" 
                        placeholder="8X9-000-0000">
                </div>
            </div>
            <div class="col-span-1">
                <label for="contacto_email" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Email de Contacto</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                        <span class="material-symbols-outlined text-lg">alternate_email</span>
                    </div>
                    <input type="email" name="contacto_email" id="contacto_email" 
                        class="w-full pl-12 pr-4 py-3.5 bg-slate-50/30 border-slate-200 rounded-2xl focus:ring-primary/20 focus:border-primary transition-all text-sm font-bold text-on-surface @error('contacto_email') border-error @enderror" 
                        value="{{ old('contacto_email', $empresa->contacto_email ?? '') }}" 
                        placeholder="email@empresa.com">
                </div>
                @error('contacto_email')
                    <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                    </p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Commission Section --}}
    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
        <h3 class="text-xs font-black text-amber-600 uppercase tracking-[0.2em] flex items-center gap-2 mb-4 px-1">
            <span class="material-symbols-outlined text-lg">payments</span> Esquema de Comisiones
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-1">
                <label for="comision_tipo" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Tipo de Comisión</label>
                <select name="comision_tipo" id="comision_tipo" 
                    class="w-full px-4 py-3.5 bg-slate-50/30 border-slate-200 rounded-2xl focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-bold text-on-surface shadow-inner appearance-none">
                    <option value="">No definido / Sin comisión</option>
                    <option value="Fixed" {{ old('comision_tipo', $empresa->comision_tipo ?? '') == 'Fixed' ? 'selected' : '' }}>Monto Fijo (RD$)</option>
                    <option value="Percentage" {{ old('comision_tipo', $empresa->comision_tipo ?? '') == 'Percentage' ? 'selected' : '' }}>Porcentaje (%)</option>
                </select>
            </div>
            <div class="col-span-1">
                <label for="comision_valor" class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Valor de la Comisión</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-amber-500 transition-colors">
                        <span class="material-symbols-outlined text-lg">token</span>
                    </div>
                    <input type="number" step="0.01" name="comision_valor" id="comision_valor" 
                        class="w-full pl-12 pr-4 py-3.5 bg-slate-50/30 border-slate-200 rounded-2xl focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-bold text-on-surface @error('comision_valor') border-error @enderror" 
                        value="{{ old('comision_valor', $empresa->comision_valor ?? '') }}" 
                        placeholder="0.00">
                </div>
                @error('comision_valor')
                    <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                    </p>
                @enderror
            </div>
        </div>
    </div>

    {{-- CRM Section --}}
    <div class="bg-indigo-50/50 p-8 rounded-[2.5rem] border border-indigo-100 shadow-sm space-y-6">
        <div class="flex items-center gap-3 border-b border-indigo-200/50 pb-4 mb-6">
            <span class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined">support_agent</span>
            </span>
            <div>
                <h4 class="text-sm font-black text-indigo-900 uppercase tracking-wider">Gestión y Seguimiento CRM</h4>
                <p class="text-[0.65rem] font-bold text-indigo-500/80">Asignación de responsables y estado comercial</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-1">
                <label for="promotor_id" class="block text-[0.65rem] font-black text-indigo-800 uppercase tracking-[0.2em] mb-2 px-1">Promotor Asignado (Usuario)</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-indigo-400 group-focus-within:text-indigo-600 transition-colors">
                        <span class="material-symbols-outlined text-lg">badge</span>
                    </div>
                    <select name="promotor_id" id="promotor_id" 
                        class="w-full pl-12 pr-4 py-3.5 bg-white border-indigo-200 rounded-2xl focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-indigo-950 shadow-sm appearance-none @error('promotor_id') border-error @enderror">
                        <option value="">Sin Asignar</option>
                        @foreach($promotores as $p)
                            <option value="{{ $p->id }}" {{ old('promotor_id', $empresa->promotor_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('promotor_id')
                    <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="col-span-1">
                <label for="estado_contacto" class="block text-[0.65rem] font-black text-indigo-800 uppercase tracking-[0.2em] mb-2 px-1">Estado del Contacto</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-indigo-400 group-focus-within:text-indigo-600 transition-colors">
                        <span class="material-symbols-outlined text-lg">flag</span>
                    </div>
                    <select name="estado_contacto" id="estado_contacto" 
                        class="w-full pl-12 pr-4 py-3.5 bg-white border-indigo-200 rounded-2xl focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-indigo-950 shadow-sm appearance-none @error('estado_contacto') border-error @enderror">
                        @foreach(['Nuevo', 'Contactado', 'En Negociación', 'Afiliada', 'No Contactar'] as $estado)
                            <option value="{{ $estado }}" {{ old('estado_contacto', $empresa->estado_contacto ?? 'Nuevo') === $estado ? 'selected' : '' }}>
                                {{ $estado }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('estado_contacto')
                    <p class="text-error text-[0.7rem] font-bold mt-2 px-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">error</span> {{ $message }}
                    </p>
                @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<style>
    .leaflet-container { font-family: inherit; }
    .leaflet-popup-content-wrapper { border-radius: 12px; }
    #map-picker { z-index: 1; }
    .leaflet-control-geocoder { border-radius: 12px !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important; border: 1px solid #e2e8f0 !important; }
    .leaflet-control-geocoder-form input { border-radius: 8px !important; font-size: 12px !important; font-weight: 700 !important; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize Tom Select for specific fields
    const config = {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: 'Escriba para buscar...',
    };

    const pSelect = new TomSelect('#provincia_id', config);
    const mSelect = new TomSelect('#municipio_id', config);
    const promotorSelect = new TomSelect('#promotor_id', config);

    // Dynamic Municipality loading
    pSelect.on('change', function(provinciaId) {
        if (!provinciaId) {
            mSelect.clear(); mSelect.clearOptions(); return;
        }
        mSelect.clear(); mSelect.clearOptions();
        fetch(`{{ url('municipios') }}/${provinciaId}`)
            .then(response => response.json())
            .then(data => {
                const options = data.map(m => ({ value: m.id, text: m.nombre }));
                mSelect.addOptions(options);
                mSelect.refreshOptions(false);
            });
    });

    // 2. Initialize Leaflet Map Picker
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    // Default location (Santo Domingo) or existing coordinates
    const initialLat = latInput.value || 18.4861;
    const initialLng = lngInput.value || -69.9312;
    const initialZoom = latInput.value ? 16 : 8;

    const map = L.map('map-picker').setView([initialLat, initialLng], initialZoom);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // 3. Add Geocoder Search
    const geocoder = L.Control.Geocoder.nominatim();
    const geocoderControl = L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: "Buscar dirección / lugar...",
        geocoder: geocoder
    }).on('markgeocode', function(e) {
        const { center, name } = e.geocode;
        map.setView(center, 16);
        updateLocation(center.lat, center.lng);
    }).addTo(map);

    let marker;
    
    // Auxiliary function to update inputs and marker
    function updateLocation(lat, lng) {
        latInput.value = lat.toFixed(8);
        lngInput.value = lng.toFixed(8);
        
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                updateLocation(pos.lat, pos.lng);
            });
        }
    }

    // Add marker if coordinates exist initially
    if (latInput.value && lngInput.value) {
        updateLocation(parseFloat(latInput.value), parseFloat(lngInput.value));
    }

    // 4. Handle Google Maps URL Parsing in UI
    const urlInput = document.getElementById('google_maps_url');
    urlInput.addEventListener('input', function() {
        const val = this.value;
        if (!val) return;

        // Try to find lat,long pattern in the URL
        const match = val.match(/(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/);
        if (match) {
            const lat = parseFloat(match[1]);
            const lng = parseFloat(match[2]);
            updateLocation(lat, lng);
            map.setView([lat, lng], 16);
        }
    });

    // Map Click Event
    map.on('click', function(e) {
        updateLocation(e.latlng.lat, e.latlng.lng);
    });

    // Fix map rendering issue in tabs or hidden containers if needed
    setTimeout(() => { map.invalidateSize(); }, 500);
});
</script>
@endpush
