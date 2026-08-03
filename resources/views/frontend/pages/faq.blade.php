<x-app-layout title="Preguntas Frecuentes">
    <div class="max-w-4xl mx-auto px-5 sm:px-8 lg:px-10 py-16 md:py-24" x-data="{ active: null }">
        <div class="text-center mb-16">
            <h2 class="text-sm font-black uppercase tracking-[0.3em] text-amber-500 mb-4">Soporte</h2>
            <h1 class="text-4xl md:text-6xl font-black tracking-tight text-gray-900 leading-tight">Preguntas Frecuentes</h1>
            <p class="mt-6 text-lg text-gray-500 max-w-2xl mx-auto">Todo lo que necesitas saber sobre tus pedidos, envíos y devoluciones en Stock Select.</p>
        </div>
        
        <div class="space-y-4">
            {{-- FAQ Item 1 --}}
            <div class="border border-gray-100 rounded-[2rem] overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
                <button @click="active = (active === 1 ? null : 1)" 
                        class="w-full flex items-center justify-between p-7 text-left focus:outline-none group">
                    <span class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors">¿Cuánto tarda en llegar mi pedido?</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300" :class="active === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="active === 1" x-collapse x-cloak>
                    <div class="px-7 pb-7 text-gray-600 leading-relaxed">
                        Los pedidos realizados antes de las 14:00h se envían el mismo día. El tiempo de entrega estándar es de 24 a 48 horas laborables dentro de la península. Recibirás un correo con el número de seguimiento en cuanto el paquete salga de nuestro almacén.
                    </div>
                </div>
            </div>

            {{-- FAQ Item 2 --}}
            <div class="border border-gray-100 rounded-[2rem] overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
                <button @click="active = (active === 2 ? null : 2)" 
                        class="w-full flex items-center justify-between p-7 text-left focus:outline-none group">
                    <span class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors">¿Los productos son originales?</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300" :class="active === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="active === 2" x-collapse x-cloak>
                    <div class="px-7 pb-7 text-gray-600 leading-relaxed">
                        Sí, garantizamos que el 100% de nuestros productos son originales y provienen directamente de distribuidores autorizados o de las propias marcas. Como outlet premium, solo trabajamos con stock auténtico.
                    </div>
                </div>
            </div>

            {{-- FAQ Item 3 --}}
            <div class="border border-gray-100 rounded-[2rem] overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
                <button @click="active = (active === 3 ? null : 3)" 
                        class="w-full flex items-center justify-between p-7 text-left focus:outline-none group">
                    <span class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors">¿Cómo puedo devolver un producto?</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300" :class="active === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="active === 3" x-collapse x-cloak>
                    <div class="px-7 pb-7 text-gray-600 leading-relaxed">
                        Tienes 14 días para solicitar una devolución. El producto debe estar sin usar y con su caja y etiquetas originales. Los gastos de envío de la devolución corren a cargo del cliente, a menos que el producto sea defectuoso.
                    </div>
                </div>
            </div>

            {{-- FAQ Item 4 --}}
            <div class="border border-gray-100 rounded-[2rem] overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
                <button @click="active = (active === 4 ? null : 4)" 
                        class="w-full flex items-center justify-between p-7 text-left focus:outline-none group">
                    <span class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors">¿Qué pasa si me equivoco de talla?</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-300" :class="active === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="active === 4" x-collapse x-cloak>
                    <div class="px-7 pb-7 text-gray-600 leading-relaxed">
                        Te recomendamos consultar nuestra <a href="{{ route('pages.size-guide') }}" class="text-gray-900 font-bold underline">Guía de Tallas</a> antes de comprar. Si aun así no te queda bien, puedes realizar un cambio (sujeto a disponibilidad de stock) o una devolución siguiendo nuestro proceso estándar.
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-20 bg-gray-50 rounded-[3rem] p-10 md:p-16 text-center border border-gray-100">
            <h3 class="text-2xl font-black text-gray-900 mb-4">¿Aún tienes dudas?</h3>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">Nuestro equipo de atención al cliente está disponible por WhatsApp para ayudarte en tiempo real.</p>
            <a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '34600000000' }}?text={{ urlencode($settings['whatsapp_default_message'] ?? '¡Hola! Tengo una duda.') }}" class="inline-flex items-center gap-3 bg-green-500 hover:bg-green-600 text-white px-10 py-5 rounded-2xl font-bold transition-all shadow-xl shadow-green-500/10">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Contactar ahora
            </a>
        </div>
    </div>
</x-app-layout>
