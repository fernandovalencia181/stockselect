<x-app-layout>
    <div class="max-w-6xl mx-auto py-8 sm:py-12 px-4 sm:px-6 lg:px-8 font-sans"
        x-data="{ 
            subtotal: @json($subtotal ?? 0),
            discountAmount: 0,
            couponCode: '',
            appliedCouponCode: '',
            isValidatingCoupon: false,
            couponMessage: '',
            couponStatus: '',
            customerName: '',
            customerPhone: '',
            customerEmail: '',
            showError: false,
            errorMessage: '',
            isSubmitting: false,

            get total() {
                return Math.max(0, (this.subtotal || 0) - (this.discountAmount || 0));
            },

            async applyCoupon() {
                if(!this.couponCode.trim()) return;
                this.isValidatingCoupon = true;
                this.couponMessage = '';
                
                try {
                    const response = await fetch('{{ route('checkout.validate-coupon') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            code: this.couponCode,
                            subtotal: this.subtotal,
                            email: this.customerEmail
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.discountAmount = data.discount;
                        this.appliedCouponCode = data.code;
                        this.couponStatus = 'success';
                        this.couponMessage = `¡Cupón aplicado! Ahorras ${this.discountAmount.toFixed(2)}€`;
                    } else {
                        this.couponStatus = 'error';
                        this.couponMessage = data.message || 'Cupón no válido';
                        this.discountAmount = 0;
                        this.appliedCouponCode = '';
                    }
                } catch (e) {
                    this.couponStatus = 'error';
                    this.couponMessage = 'Error al validar el cupón.';
                } finally {
                    this.isValidatingCoupon = false;
                }
            },

            removeCoupon() {
                this.appliedCouponCode = '';
                this.discountAmount = 0;
                this.couponCode = '';
                this.couponMessage = 'Cupón eliminado.';
                this.couponStatus = 'info';
                setTimeout(() => { if(this.couponStatus === 'info') this.couponMessage = ''; }, 3000);
            },

            triggerError(msg) {
                this.errorMessage = msg;
                this.showError = true;
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => { this.showError = false; }, 5000);
            },

            submitOrder() {
                if (this.isSubmitting) return;

                if (!this.customerName.trim()) {
                    this.triggerError('Por favor, introduce tu nombre.');
                    return;
                }

                if (!this.customerPhone.trim()) {
                    this.triggerError('Por favor, introduce tu teléfono de contacto para WhatsApp.');
                    return;
                }

                this.isSubmitting = true;
                this.$refs.checkoutForm.submit();
            }
        }">

        {{-- Alerta de Error Global --}}
        <div x-show="showError" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="max-w-4xl mx-auto mb-8 text-red-600 text-[14px] font-bold flex items-center gap-3 bg-red-50 p-5 rounded-2xl border border-red-100 shadow-sm"
             style="display: none;">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <span x-text="errorMessage"></span>
        </div>

        @if ($errors->any())
            <div class="max-w-4xl mx-auto mb-8 text-red-600 text-[14px] font-bold flex items-center gap-3 bg-red-50 p-5 rounded-2xl border border-red-100">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            {{-- COLUMNA IZQUIERDA: FORMULARIO DIRECTO (1 PASO) --}}
            <div class="lg:col-span-7 space-y-6">
                
                {{-- Encabezado --}}
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Finalizar Pedido</h1>
                    <p class="text-sm text-gray-500 mt-1">Completa tus datos de contacto y coordinaremos la entrega en Mollerussa directamente por WhatsApp.</p>
                </div>

                {{-- Tarjeta de Datos --}}
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                        <div class="w-10 h-10 bg-black text-white rounded-xl flex items-center justify-center font-bold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Tus Datos de Contacto</h2>
                            <p class="text-xs text-gray-400">Solo lo necesario para preparar tu pedido y hablar contigo.</p>
                        </div>
                    </div>

                    <form action="{{ route('checkout.store') }}" method="POST" x-ref="checkoutForm" class="space-y-5">
                        @csrf
                        <input type="hidden" name="shipping_method" value="local_pickup">
                        <input type="hidden" name="payment_method" value="whatsapp">
                        <input type="hidden" name="coupon_code" :value="appliedCouponCode">

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Nombre y Apellidos *</label>
                            <div class="relative">
                                <input type="text" name="customer_name" x-model="customerName" required
                                    placeholder="Ej: Marc García"
                                    value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                                    class="block w-full rounded-2xl border-gray-200 bg-gray-50/50 shadow-sm focus:border-black focus:ring-black text-[14px] py-4 pl-12 pr-4 transition-all">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Teléfono móvil (WhatsApp) *</label>
                            <div class="relative">
                                <input type="tel" name="customer_phone" x-model="customerPhone" required
                                    placeholder="Ej: 612 345 678"
                                    value="{{ old('customer_phone', auth()->user()->phone ?? '') }}"
                                    class="block w-full rounded-2xl border-gray-200 bg-gray-50/50 shadow-sm focus:border-black focus:ring-black text-[14px] py-4 pl-12 pr-4 transition-all">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none text-green-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500">Email (Opcional)</label>
                                <span class="text-[11px] text-gray-400">Para recibir el resumen en tu correo</span>
                            </div>
                            <div class="relative">
                                <input type="email" name="customer_email" x-model="customerEmail"
                                    placeholder="tu@email.com (opcional)"
                                    value="{{ old('customer_email', auth()->user()->email ?? '') }}"
                                    class="block w-full rounded-2xl border-gray-200 bg-gray-50/50 shadow-sm focus:border-black focus:ring-black text-[14px] py-4 pl-12 pr-4 transition-all">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Tarjeta de Información de Entrega & Pago --}}
                <div class="bg-gradient-to-br from-gray-900 to-black text-white p-6 sm:p-7 rounded-3xl shadow-md space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-white/10 text-amber-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <h3 class="font-extrabold text-sm uppercase tracking-wider text-white">¿Cómo funciona la entrega?</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-gray-300">
                        <div class="flex items-start gap-2.5 p-3 rounded-2xl bg-white/5 border border-white/10">
                            <span class="text-base">📍</span>
                            <div>
                                <strong class="text-white block font-bold">Entrega en mano</strong>
                                <span>Punto de encuentro en Mollerussa (Gratis).</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5 p-3 rounded-2xl bg-white/5 border border-white/10">
                            <span class="text-base">💶</span>
                            <div>
                                <strong class="text-white block font-bold">Pago en efectivo</strong>
                                <span>Pagas al recibir y comprobar tus prendas.</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-[11px] text-gray-400 pt-1 border-t border-white/10">
                        Al hacer clic en el botón, se guardará tu pedido y se abrirá WhatsApp con el mensaje listo para enviarnos.
                    </p>
                </div>

                {{-- Botón CTA Principal (Escritorio y Móvil) --}}
                <div>
                    <button type="button" @click="submitOrder()" :disabled="isSubmitting"
                        class="w-full bg-[#25D366] hover:bg-[#20bd5a] active:scale-[0.99] text-white font-extrabold py-5 px-6 rounded-2xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-3 text-base sm:text-lg cursor-pointer">
                        <template x-if="!isSubmitting">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span>Pedir por WhatsApp (<span x-text="total.toFixed(2) + ' €'"></span>)</span>
                            </div>
                        </template>
                        <template x-if="isSubmitting">
                            <div class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Abriendo WhatsApp...</span>
                            </div>
                        </template>
                    </button>
                    <p class="text-center text-[11px] text-gray-400 mt-2">
                        Pago 100% seguro en mano. Sin introducir datos bancarios.
                    </p>
                </div>

            </div>

            {{-- COLUMNA DERECHA: RESUMEN DEL PEDIDO --}}
            <div class="lg:col-span-5">
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 sticky top-24 space-y-6">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <h2 class="text-base font-extrabold text-gray-900">Tu Cesta</h2>
                        <a href="{{ route('cart.index') }}" class="text-xs text-gray-500 hover:text-black font-semibold underline transition">Editar cesta</a>
                    </div>

                    {{-- Lista de Productos --}}
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-1">
                        @foreach ($cart as $id => $item)
                            @php
                                $pm = \App\Models\Product::find($item['id']);
                            @endphp
                            <div class="flex items-center gap-3.5 py-2 border-b border-gray-50 last:border-0">
                                <div class="w-14 h-14 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                    @if($pm && !empty($pm->images))
                                        <img src="{{ Storage::url($pm->images[0]) }}" class="w-full h-full object-contain mix-blend-multiply">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $item['name'] }}</p>
                                    <p class="text-[11px] text-gray-400">Talla {{ $item['size'] ?? '-' }} &middot; Cantidad: {{ $item['quantity'] }}</p>
                                </div>
                                <p class="text-xs sm:text-sm font-extrabold text-gray-900">{{ number_format($item['price'] * $item['quantity'], 2) }}€</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Sección Cupón --}}
                    <div class="pt-4 border-t border-gray-100">
                        <div class="bg-gray-50 p-3.5 rounded-2xl">
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1.5 block">¿Tienes un cupón?</label>
                            
                            <template x-if="!appliedCouponCode">
                                <div class="flex gap-2">
                                    <input type="text" x-model="couponCode"
                                        placeholder="CÓDIGO"
                                        @keydown.enter.prevent="applyCoupon()"
                                        class="block w-full uppercase font-bold text-xs rounded-xl border-gray-200 bg-white focus:border-black focus:ring-black px-3 py-2.5">
                                    <button type="button" @click="applyCoupon()" :disabled="isValidatingCoupon"
                                        class="bg-black text-white text-xs font-bold px-4 py-2.5 rounded-xl hover:bg-gray-800 transition disabled:opacity-50 shrink-0">
                                        <span x-show="!isValidatingCoupon">Aplicar</span>
                                        <span x-show="isValidatingCoupon">...</span>
                                    </button>
                                </div>
                            </template>

                            <template x-if="appliedCouponCode">
                                <div class="flex items-center justify-between bg-green-50 text-green-700 p-2.5 rounded-xl border border-green-200">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="text-xs font-bold" x-text="appliedCouponCode"></span>
                                    </div>
                                    <button type="button" @click="removeCoupon()" class="text-green-700 hover:text-green-900 text-xs font-bold ml-2">Eliminar</button>
                                </div>
                            </template>

                            <template x-if="couponMessage">
                                <p class="text-[11px] font-semibold mt-1.5"
                                   :class="{
                                       'text-green-600': couponStatus === 'success',
                                       'text-red-500': couponStatus === 'error',
                                       'text-gray-500': couponStatus === 'info'
                                   }"
                                   x-text="couponMessage"></p>
                            </template>
                        </div>
                    </div>

                    {{-- Totales --}}
                    <div class="space-y-2.5 py-4 border-t border-gray-100 text-sm">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span class="font-bold text-gray-900" x-text="(subtotal || 0).toFixed(2) + ' €'"></span>
                        </div>
                        <div class="flex justify-between items-center text-gray-500">
                            <span>Entrega en mano</span>
                            <span class="font-bold text-green-600 bg-green-50 px-2.5 py-0.5 rounded-full text-xs">Gratis (Mollerussa)</span>
                        </div>

                        <template x-if="discountAmount > 0">
                            <div class="flex justify-between text-green-600 font-bold">
                                <span>Descuento</span>
                                <span x-text="'-' + discountAmount.toFixed(2) + ' €'"></span>
                            </div>
                        </template>

                        <div class="flex justify-between items-baseline pt-4 border-t border-gray-100">
                            <div>
                                <span class="text-base font-extrabold text-gray-900 block">Total a pagar</span>
                                <span class="text-[11px] text-gray-400 font-normal">En efectivo al recibir tu pedido</span>
                            </div>
                            <span class="text-2xl font-black text-gray-900" x-text="total.toFixed(2) + ' €'"></span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</x-app-layout>