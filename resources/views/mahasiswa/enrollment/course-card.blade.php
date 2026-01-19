<div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden group hover:shadow-2xl hover:shadow-indigo-200/50 transition-all duration-700">
    <!-- Course Header -->
    <div class="px-8 py-6 bg-white border-b border-slate-50 flex flex-col lg:flex-row lg:items-center justify-between gap-6 transition-all group-hover:bg-slate-50/50">
        <div class="flex items-center gap-6">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-2xl shadow-inner group-hover:scale-110 transition-transform duration-500">
                {{ substr($course->nama_mk, 0, 1) }}
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight leading-tight group-hover:text-indigo-600 transition-colors">{{ $course->nama_mk }}</h3>
                <div class="flex items-center gap-3 mt-1.5">
                    <span class="text-xs font-mono font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $course->kode_mk }}</span>
                    <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $course->sks }} SKS</span>
                    <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                    @if($course->sifat === 'Wajib')
                        <span class="text-[10px] uppercase font-black text-rose-500 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-100 shadow-sm">Mata Kuliah Wajib</span>
                    @else
                        <span class="text-[10px] uppercase font-black text-blue-500 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-100 shadow-sm">Mata Kuliah Pilihan</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Class Selection -->
    <div class="p-8">
        @php $classes = $availableClasses->get($course->kode_mk) ?? collect(); @endphp
        
        @if($classes->isEmpty())
            <div class="py-8 bg-slate-50/50 rounded-2xl border-2 border-dashed border-slate-100 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-3 shadow-sm">
                    <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-slate-400 font-bold italic tracking-tight">Tidak ada jadwal kelas yang tersedia.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach($classes as $class)
                    @php 
                        $isSelected = in_array($class->id_perkuliahan, $existingDkbs); 
                        $dayColor = match($class->hari) {
                            'Senin' => 'blue', 'Selasa' => 'emerald', 'Rabu' => 'amber',
                            'Kamis' => 'purple', 'Jumat' => 'rose', default => 'slate'
                        };
                    @endphp
                    <label class="relative cursor-pointer block h-full">
                        <input type="radio" 
                               name="selections[{{ $course->kode_mk }}]" 
                               value="{{ $class->id_perkuliahan }}" 
                               class="peer sr-only"
                               data-sks="{{ $course->sks }}"
                               {{ (old("selections.{$course->kode_mk}") == $class->id_perkuliahan || $isSelected) ? 'checked' : '' }}
                               {{ $course->sifat === 'Wajib' ? 'required' : '' }}>
                        
                        <div class="h-full bg-white border border-slate-100 rounded-[1.5rem] p-5 transition-all duration-300 peer-checked:bg-indigo-50/50 peer-checked:border-indigo-600 peer-checked:ring-4 peer-checked:ring-indigo-100/50 hover:shadow-lg hover:border-slate-300 relative overflow-hidden flex flex-col">
                            <div class="flex items-center justify-between mb-4 relative z-10">
                                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-sm shadow-lg group-hover:scale-110 transition-transform">
                                    {{ $class->kelas }}
                                </div>
                                <span class="bg-{{ $dayColor }}-100 text-{{ $dayColor }}-700 text-[10px] font-black italic px-2.5 py-1 rounded-lg uppercase border border-{{ $dayColor }}-200 shadow-sm">
                                    {{ $class->hari }}
                                </span>
                            </div>
                            
                            <div class="space-y-3 relative z-10 grow">
                                <div class="flex items-center gap-3 text-slate-600">
                                    <div class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-bold tracking-tight italic">{{ substr($class->jam_mulai, 0, 5) }} - {{ substr($class->jam_berakhir, 0, 5) }}</span>
                                </div>
                                
                                <div class="flex items-center gap-3 text-slate-600">
                                    <div class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-bold tracking-tight italic">{{ $class->ruangan->nama_ruangan ?? 'N/A' }}</span>
                                </div>
                                
                                <div class="flex items-start gap-3 mt-4 pt-4 border-t border-slate-50">
                                    <div class="w-7 h-7 rounded-full overflow-hidden shrink-0 shadow-sm">
                                        <img src="https://api.dicebear.com/7.x/initials/svg?seed={{ urlencode($class->dosen->nama ?? 'DN') }}&backgroundColor=6366f1" alt="Dosen">
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400 leading-tight block truncate grow pt-1">{{ $class->dosen->nama ?? 'Dosen N/A' }}</span>
                                </div>
                            </div>

                            <!-- Selected Highlight Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/5 to-blue-600/5 opacity-0 peer-checked:opacity-100 transition-opacity duration-500"></div>
                            
                            <!-- Selection Mark -->
                            <div class="absolute bottom-4 right-4 translate-y-10 opacity-0 peer-checked:translate-y-0 peer-checked:opacity-100 transition-all duration-500 z-20">
                                <div class="w-8 h-8 bg-indigo-600 text-white rounded-xl shadow-lg flex items-center justify-center ring-4 ring-white">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            
            @if($course->sifat !== 'Wajib')
                <div class="mt-6 flex justify-end">
                    <button type="button" 
                            onclick="unselectCourse('{{ $course->kode_mk }}')"
                            class="text-xs font-black text-slate-400 hover:text-rose-500 transition-all flex items-center gap-2 group/btn">
                        <div class="w-6 h-6 rounded-lg bg-slate-50 flex items-center justify-center group-hover/btn:bg-rose-50 group-hover/btn:text-rose-500 transition-colors">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <span class="uppercase tracking-widest italic underline decoration-slate-200 group-hover/btn:decoration-rose-200">Batalkan Pilihan</span>
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>
