<x-app-layout>
    <div class="min-h-[80vh] flex items-center justify-center px-5 sm:px-8">
        <div class="max-w-xl w-full text-center">
            {{-- Icono Animado / Estático Premium --}}
            <div class="mb-10 inline-flex items-center justify-center w-20 h-20 bg-gray-900 rounded-full shadow-2xl shadow-gray-200">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 mb-6 leading-tight">
                Gracias por tu confianza.
            </h1>
            
            <p class="text-[16px] md:text-[18px] text-gray-500 font-medium mb-12 leading-relaxed">
                Tu pedido <span class="text-gray-900 font-bold">#{{ $order->id }}</span> ha sido procesado con éxito. Hemos enviado los detalles a <span class="text-gray-900 font-semibold">{{ $order->customer_email }}</span>.
            </p>

            <div class="bg-gray-50 rounded-[2.5rem] p-8 md:p-10 mb-12 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-left">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Total abonado</p>
                    <p class="text-3xl font-black text-gray-900">{{ number_format($order->total_amount, 2) }} €</p>
                </div>
                <div class="flex flex-col gap-3 w-full md:w-auto">
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gray-900 text-white font-semibold rounded-2xl hover:bg-gray-800 transition-all shadow-lg shadow-gray-200 active:scale-[0.98]">
                        Seguir comprando
                    </a>
                </div>
            </div>

            <p class="text-[13px] text-gray-400">
                ¿Tienes alguna duda sobre tu pedido? <a href="https://wa.me/34600000000" class="text-gray-900 font-bold hover:underline underline-offset-4">Contáctanos por WhatsApp</a>.
            </p>
        </div>
    </div>
</x-app-layout>
