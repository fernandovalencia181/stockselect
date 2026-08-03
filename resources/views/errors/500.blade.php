<x-app-layout title="Error del Servidor">
    <div class="min-h-[70vh] flex flex-col items-center justify-center px-5 text-center">
        {{-- Fondo sutil decorativo (Rojo/Naranja para indicar error técnico pero elegante) --}}
        <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute top-[20%] left-[10%] w-[300px] h-[300px] bg-red-50 rounded-full blur-[120px] opacity-40"></div>
            <div class="absolute bottom-[20%] right-[10%] w-[300px] h-[300px] bg-amber-50 rounded-full blur-[120px] opacity-40"></div>
        </div>

        <div class="mb-6 md:mb-8 relative">
            <span class="text-[100px] md:text-[160px] font-black text-gray-100 leading-none select-none tracking-tighter">500</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="h-16 md:h-24 opacity-20 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        <h1 class="text-3xl md:text-5xl font-black tracking-tight text-gray-900 mb-5 leading-tight">Ups... algo se ha roto.</h1>
        
        <p class="text-[15px] md:text-[17px] text-gray-400 max-w-sm md:max-w-md mx-auto leading-relaxed mb-12 font-medium">
            Nuestros servidores están experimentando algunas dificultades técnicas momentáneas. Nuestro equipo ya ha sido avisado y lo está solucionando.
        </p>

        <div class="flex flex-col sm:flex-row items-center gap-4">
            <a href="{{ route('home') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-gray-900 hover:bg-gray-800 text-white font-bold py-4 px-10 rounded-2xl transition-all duration-300 shadow-xl active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Intentar de nuevo
            </a>
            
            <a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '34600000000' }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-white border border-gray-100 text-gray-900 font-bold py-4 px-10 rounded-2xl transition-all duration-300 shadow-sm hover:border-gray-200 active:scale-95">
                Reportar el fallo
            </a>
        </div>
    </div>
</x-app-layout>
