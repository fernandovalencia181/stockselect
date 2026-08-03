<x-app-layout title="Página no encontrada">
    <div class="min-h-[70vh] flex flex-col items-center justify-center px-5 text-center">
        {{-- Fondo sutil decorativo --}}
        <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
            <div class="absolute top-[20%] left-[10%] w-[300px] h-[300px] bg-amber-50 rounded-full blur-[120px] opacity-40"></div>
            <div class="absolute bottom-[20%] right-[10%] w-[300px] h-[300px] bg-blue-50 rounded-full blur-[120px] opacity-40"></div>
        </div>

        <div class="mb-6 md:mb-8 relative">
            <span class="text-[100px] md:text-[160px] font-black text-gray-100 leading-none select-none tracking-tighter">404</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <img src="{{ asset('images/stock_select_logo-solo.png') }}" alt="Stock Select" class="h-16 md:h-24 opacity-10">
            </div>
        </div>

        <h1 class="text-3xl md:text-5xl font-black tracking-tight text-gray-900 mb-5 leading-tight">Ups... parece que te has perdido.</h1>
        
        <p class="text-[15px] md:text-[17px] text-gray-400 max-w-sm md:max-w-md mx-auto leading-relaxed mb-12 font-medium">
            La página que buscas no existe o ha sido movida. Pero no te preocupes, nuestra mejor selección te espera en el inicio.
        </p>

        <div class="flex flex-col sm:flex-row items-center gap-4">
            <a href="{{ route('home') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-gray-900 hover:bg-gray-800 text-white font-bold py-4 px-10 rounded-2xl transition-all duration-300 shadow-xl active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver a la tienda
            </a>
            
            <a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '34600000000' }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-white border border-gray-100 text-gray-900 font-bold py-4 px-10 rounded-2xl transition-all duration-300 shadow-sm hover:border-gray-200 active:scale-95">
                ¿Necesitas ayuda?
            </a>
        </div>
    </div>
</x-app-layout>
