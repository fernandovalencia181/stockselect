<x-app-layout title="Mantenimiento">
    <div class="min-h-[70vh] flex flex-col items-center justify-center px-5 text-center">
        {{-- Fondo sutil decorativo (Tonos neutros/ámbar indicando pausa) --}}
        <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute top-[20%] left-[10%] w-[300px] h-[300px] bg-amber-50 rounded-full blur-[120px] opacity-40"></div>
            <div class="absolute bottom-[20%] right-[10%] w-[300px] h-[300px] bg-gray-100 rounded-full blur-[120px] opacity-60"></div>
        </div>

        <div class="mb-6 md:mb-8 relative">
            <div class="w-20 h-20 md:w-24 md:h-24 mx-auto bg-gray-900 rounded-3xl flex items-center justify-center mb-6 shadow-xl transform rotate-12">
                <svg class="w-10 h-10 md:w-12 md:h-12 text-white transform -rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <span class="text-[60px] md:text-[80px] font-black text-gray-100 leading-none select-none tracking-tighter absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10">503</span>
        </div>

        <h1 class="text-3xl md:text-5xl font-black tracking-tight text-gray-900 mb-5 leading-tight">Volvemos enseguida.</h1>
        
        <p class="text-[15px] md:text-[17px] text-gray-500 max-w-sm md:max-w-md mx-auto leading-relaxed mb-12 font-medium">
            Estamos realizando algunas mejoras técnicas en la tienda para ofrecerte una mejor experiencia. Volveremos a estar online en unos minutos.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <button onclick="window.location.reload()" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-gray-900 hover:bg-gray-800 text-white font-bold py-4 px-10 rounded-2xl transition-all duration-300 shadow-xl active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Actualizar página
            </button>
        </div>
    </div>
</x-app-layout>
