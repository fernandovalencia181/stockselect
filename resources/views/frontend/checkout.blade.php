<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8 font-sans"
        x-data="{ 
            step: 1,
            maxStepReached: 1,
            shippingMethod: 'local_pickup', 
            subtotal: @json($subtotal ?? 0),
            shippingCost: 0,
            get total() {
                return Math.max(0, (this.subtotal || 0) - (this.discountAmount || 0));
            },
            couponCode: '',
            appliedCouponCode: '',
            discountAmount: 0,
            isValidatingCoupon: false,
            couponMessage: '',
            couponStatus: '', // 'success' or 'error'
            paymentMethod: 'whatsapp',
            showError: false,
            errorMessage: '',
            customerName: '',
            customerEmail: '',
            customerPhone: '',
            searchQuery: '',
            shippingStreet: '',
            shippingNumber: '',
            shippingFloor: '',
            shippingNotes: '',
            shippingCity: '',
            shippingZip: '',
            shippingProvince: '',
            searchResults: [],
            isSearching: false,
            acceptTerms: false,
            subscribeNewsletter: true,
            
            async fetchAddresses() {
                const q = (this.searchQuery || '').trim();
                if(q.length < 3) { this.searchResults = []; return; }
                this.isSearching = true;
                try {
                    const response = await fetch(`/address-search?q=${encodeURIComponent(q)}`);
                    if (!response.ok) throw new Error('Proxy Error');
                    const data = await response.json();
                    this.searchResults = data.features || [];
                } catch (e) {
                    console.error('Error:', e);
                    this.searchResults = [];
                } finally {
                    this.isSearching = false;
                }
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
                        this.couponMessage = `¡Cupón aplicado! Has ahorrado ${this.discountAmount.toFixed(2)}€`;
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

            selectAddress(feature) {
                const props = feature.properties || {};

                // Photon puede devolver la calle en 'street' o en 'name' dependiendo del tipo de resultado
                const street = props.street || props.name || '';
                this.shippingStreet = street;
                this.shippingNumber = props.housenumber || '';

                // Municipio: Photon usa 'city', 'town', 'village' o 'municipality'
                this.shippingCity = props.city || props.town || props.village || props.municipality || '';
                this.shippingZip  = props.postcode || '';

                // Provincia: en España 'county' es la provincia, 'state' es la comunidad autónoma
                this.shippingProvince = props.county || props.state || '';

                this.searchResults = [];

                // Mostrar dirección completa en el buscador como confirmación visual
                const parts = [street];
                if (props.housenumber) parts.push(props.housenumber);
                const city = this.shippingCity;
                if (city) parts.push(city);
                if (props.postcode) parts.push(props.postcode);
                this.searchQuery = parts.join(', ');
            },

            isSubmitting: false,

            goToStep(targetStep) {
                // Solo validamos si intentamos AVANZAR
                if (targetStep > this.step) {
                    // Si intentamos saltar más de un paso a la vez desde el Stepper
                    if (targetStep > this.maxStepReached + 1) return;

                    if (this.step === 1) {
                        if (!(this.customerName || '').trim()) { this.triggerError('Por favor, introduce tu nombre.'); return; }
                        if (!(this.customerEmail || '').trim() || !this.customerEmail.includes('@')) { this.triggerError('Email no válido.'); return; }
                        if (!(this.customerPhone || '').trim()) { this.triggerError('El teléfono es obligatorio.'); return; }
                    }
                }
                
                this.step = targetStep;
                if (this.step > this.maxStepReached) this.maxStepReached = this.step;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            triggerError(msg) {
                this.errorMessage = msg;
                this.showError = true;
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => { this.showError = false; }, 5000);
            },

            validateFinalForm() {
                if (!this.acceptTerms) {
                    this.triggerError('Debes aceptar los términos y condiciones para finalizar.');
                    return false;
                }
                return true;
            },

            submitOrder() {
                if (this.isSubmitting) return;
                if (!this.validateFinalForm()) return;
                this.isSubmitting = true;
                this.$refs.checkoutForm.submit();
            },

            init() {
                // Vigilante para el Email: Si cambia y hay un cupón puesto, re-validamos
                this.$watch('customerEmail', (val) => {
                    if (this.appliedCouponCode && val.includes('@')) {
                        this.couponCode = this.appliedCouponCode;
                        this.applyCoupon();
                    }
                });
            }
        }">

        {{-- Progress Stepper --}}
        <div class="mb-12 relative">
            <div class="flex items-center justify-between max-w-2xl mx-auto relative">
                {{-- Line background (Centered on circles) --}}
                <div class="absolute top-[20px] left-0 w-full h-0.5 bg-gray-100 -translate-y-1/2 z-0 hidden sm:block"></div>
                {{-- Line active (Centered on circles) --}}
                <div class="absolute top-[20px] left-0 h-0.5 bg-black -translate-y-1/2 z-0 transition-all duration-500 hidden sm:block"
                    :style="'width: ' + ((step - 1) * 50) + '%'"></div>

                {{-- Step 1 --}}
                <div class="relative z-10 flex flex-col items-center gap-2 group cursor-pointer" @click="goToStep(1)">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300"
                        :class="step >= 1 ? 'bg-black text-white' : 'bg-gray-100 text-gray-400'">1</div>
                    <span class="text-[11px] font-bold uppercase tracking-widest transition-colors duration-300"
                        :class="step >= 1 ? 'text-black' : 'text-gray-400'">Datos</span>
                </div>
                {{-- Step 2 --}}
                <div class="relative z-10 flex flex-col items-center gap-2 group" :class="maxStepReached >= 2 ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed'" @click="if(maxStepReached >= 2) goToStep(2)">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300"
                        :class="step >= 2 ? 'bg-black text-white' : 'bg-gray-100 text-gray-400'">2</div>
                    <span class="text-[11px] font-bold uppercase tracking-widest transition-colors duration-300"
                        :class="step >= 2 ? 'text-black' : 'text-gray-400'">Entrega</span>
                </div>
                {{-- Step 3 --}}
                <div class="relative z-10 flex flex-col items-center gap-2 group" :class="maxStepReached >= 3 ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed'" @click="if(maxStepReached >= 3) goToStep(3)">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300"
                        :class="step >= 3 ? 'bg-black text-white' : 'bg-gray-100 text-gray-400'">3</div>
                    <span class="text-[11px] font-bold uppercase tracking-widest transition-colors duration-300"
                        :class="step >= 3 ? 'text-black' : 'text-gray-400'">Pago</span>
                </div>
            </div>
        </div>

        {{-- Alerta de Error Global --}}
        <div x-show="showError" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="max-w-4xl mx-auto mb-8 text-red-600 text-[14px] font-bold flex items-center gap-3 bg-red-50 p-5 rounded-3xl border border-red-100 shadow-sm transition-all"
             style="display: none;">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <span x-text="errorMessage"></span>
        </div>

        <form x-ref="checkoutForm" action="{{ route('checkout.store') }}" method="POST"
            @keydown.enter.prevent="if(step < 3) { goToStep(step + 1) } else { submitOrder() }"
            @submit.prevent="if(step === 3) { submitOrder() }">
            @csrf
            
            <input type="hidden" name="coupon_code" :value="appliedCouponCode">
            <input type="hidden" name="discount_amount" :value="discountAmount">
            <input type="hidden" name="shipping_method" :value="shippingMethod">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                {{-- STEP 1: DATOS --}}
                <div class="lg:col-span-2 space-y-8" x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold text-gray-900">Datos Personales</h2>
                                <p class="text-sm text-gray-400">Introduce la información básica para tu pedido.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Nombre completo</label>
                                <input type="text" name="customer_name" x-model="customerName"
                                    class="block w-full rounded-2xl border-gray-100 bg-gray-50 shadow-sm focus:border-black focus:ring-black text-[15px] py-4 transition-all">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Email de contacto</label>
                                    <input type="email" name="customer_email" x-model="customerEmail"
                                        class="block w-full rounded-2xl border-gray-100 bg-gray-50 shadow-sm focus:border-black focus:ring-black text-[15px] py-4 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Teléfono (WhatsApp)</label>
                                    <input type="tel" name="customer_phone" x-model="customerPhone"
                                        class="block w-full rounded-2xl border-gray-100 bg-gray-50 shadow-sm focus:border-black focus:ring-black text-[15px] py-4 transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end">
                            <button type="button" @click="goToStep(2)" 
                                class="bg-black text-white font-bold py-4 px-10 rounded-2xl hover:bg-gray-800 transition active:scale-95 shadow-lg flex items-center gap-2">
                                <span>Configurar Entrega</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: ENTREGA --}}
                <div class="lg:col-span-2 space-y-8" x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold text-gray-900">Entrega en Mano</h2>
                                <p class="text-sm text-gray-400">Punto de encuentro y entrega en Mollerussa.</p>
                            </div>
                        </div>

                        <input type="hidden" name="shipping_method" value="local_pickup">

                        <div class="space-y-6">
                            <div class="p-6 border-2 border-black bg-gray-50/80 rounded-2xl">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-black text-white flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <span class="font-extrabold text-gray-900 text-base">Entrega Directa en Mollerussa</span>
                                            <span class="font-extrabold text-green-600 text-sm bg-green-50 px-2.5 py-0.5 rounded-full">Gratis (0,00 €)</span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-2 leading-relaxed">
                                            Te entregamos tus prendas en mano en <strong class="text-gray-900">Mollerussa (Lleida)</strong>. Tras enviar el pedido, te contactaremos directamente por WhatsApp para coordinar el lugar y la hora que mejor te convenga.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Preferencia de punto de encuentro o notas (Opcional)</label>
                                <textarea name="shipping_notes" x-model="shippingNotes" rows="3"
                                    placeholder="Ej: Prefiero por la tarde, cerca del centro o estación..."
                                    class="block w-full rounded-2xl border-gray-200 bg-gray-50 shadow-sm focus:border-black focus:ring-black text-[13px] py-4 px-5 transition-all"></textarea>
                            </div>
                        </div>

                        <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-between">
                            <button type="button" @click="goToStep(1)" class="text-gray-400 font-bold px-8 py-4 hover:text-black transition">← Volver a Datos</button>
                            <button type="button" @click="goToStep(3)"
                                class="bg-black text-white font-bold py-4 px-10 rounded-2xl hover:bg-gray-800 transition shadow-lg flex items-center justify-center gap-2">
                                <span>Ver Pago y Resumen</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: PAGO Y RESUMEN --}}
                <div class="lg:col-span-2 space-y-8" x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-extrabold text-gray-900">Método de Pago</h2>
                                <p class="text-sm text-gray-400">Selecciona cómo deseas abonar tu pedido.</p>
                            </div>
                        </div>

                        <input type="hidden" name="payment_method" value="whatsapp">

                        <div class="space-y-4">
                            <div class="p-6 border-2 border-black bg-gray-50/80 rounded-2xl relative overflow-hidden transition-all">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-[#25D366]/10 text-[#25D366] flex items-center justify-center shrink-0 border border-[#25D366]/20">
                                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <span class="font-extrabold text-gray-900 text-base">Pago en Efectivo (Contra Entrega)</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black text-white">Activo</span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed">
                                            Acordamos el punto de encuentro en <strong class="text-gray-900">Mollerussa</strong> o los detalles de la entrega vía WhatsApp. El pago se efectúa en <strong class="text-gray-900">efectivo</strong> al recibir tu prenda.
                                        </p>
                                        <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-200/60 text-[11px] text-gray-500 font-semibold flex-wrap">
                                            <span class="flex items-center gap-1">🤝 Trato directo</span>
                                            <span class="text-gray-300">&bull;</span>
                                            <span class="flex items-center gap-1">💶 Sin tarjeta requerida</span>
                                            <span class="text-gray-300">&bull;</span>
                                            <span class="flex items-center gap-1">⚡ Coordinación inmediata</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12 bg-gray-50 p-6 rounded-2xl border border-gray-100 relative">
                            <label class="flex items-start gap-3 cursor-pointer group mb-4 pb-4 border-b border-gray-100">
                                <input type="checkbox" name="subscribe_newsletter" x-model="subscribeNewsletter"
                                    class="mt-1 w-5 h-5 text-black border-gray-200 rounded focus:ring-black">
                                <div class="flex flex-col">
                                    <span class="text-[13px] font-bold text-gray-900 leading-tight">Suscribirme para recibir ofertas y novedades exclusivas</span>
                                    <span class="text-[11px] text-gray-400 mt-1">Recibe cupones descuento y noticias de Stock Select.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 cursor-pointer group mb-6">
                                <input type="checkbox" name="accept_terms" x-model="acceptTerms"
                                    class="mt-1 w-5 h-5 text-black border-gray-200 rounded focus:ring-black">
                                <span class="text-[12px] text-gray-500 leading-tight">
                                    He leído y acepto los <a href="{{ route('pages.terms') }}" target="_blank" class="font-bold underline text-black">Términos y Condiciones</a> y la <a href="{{ route('pages.privacy') }}" target="_blank" class="font-bold underline text-black">Política de Privacidad</a>.
                                </span>
                            </label>

                            <button type="button" @click="submitOrder()"
                                :disabled="isSubmitting"
                                class="w-full bg-[#128C7E] hover:bg-[#075E54] text-white font-extrabold py-5 rounded-2xl transition-all shadow-xl active:scale-[0.98] flex items-center justify-center gap-3 disabled:opacity-60 disabled:cursor-not-allowed disabled:scale-100">
                                {{-- Estado normal --}}
                                <template x-if="!isSubmitting">
                                    <span class="flex items-center gap-3 text-[15px] tracking-wide">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        Comprar por WhatsApp (Pago en efectivo)
                                    </span>
                                </template>
                                {{-- Estado enviando --}}
                                <template x-if="isSubmitting">
                                    <span class="flex items-center gap-3">
                                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Generando pedido...
                                    </span>
                                </template>
                            </button>
                        </div>
                        
                        <div class="mt-6 flex justify-center">
                            <button type="button" @click="goToStep(2)" class="text-gray-400 font-bold hover:text-black transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                <span>Volver al envío</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA RESUMEN --}}
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 p-6 rounded-3xl sticky top-6 border border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 mb-6">Tu Carrito</h2>

                        <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
                            @foreach($cart as $item)
                                @php $pm = \App\Models\Product::find($item['id']); @endphp
                                <div class="flex items-center gap-3 py-3 border-b border-gray-100 last:border-0">
                                    <div class="w-16 h-16 rounded-xl bg-white border border-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                        @if($pm && !empty($pm->images))
                                            <img src="{{ Storage::url($pm->images[0]) }}" class="w-full h-full object-contain mix-blend-multiply">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $item['name'] }}</p>
                                        <p class="text-[11px] text-gray-400">Talla {{ $item['size'] ?? '-' }} &middot; x{{ $item['quantity'] }}</p>
                                    </div>
                                    <p class="text-sm font-extrabold text-gray-900">{{ number_format($item['price'] * $item['quantity'], 2) }}€</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-3 py-4 border-t border-gray-200 text-sm">
                            <div class="flex justify-between text-gray-500">
                                <span>Subtotal</span>
                                <span class="font-bold text-gray-900" x-text="(subtotal || 0).toFixed(2) + ' €'"></span>
                            </div>
                            <div class="flex justify-between items-center text-gray-500">
                                <span>Entrega en mano</span>
                                <span class="font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full text-xs">Gratis (Mollerussa)</span>
                            </div>

                            <template x-if="discountAmount > 0">
                                <div class="flex justify-between text-green-600 font-bold animate-pulse">
                                    <span>Descuento</span>
                                    <span x-text="'-' + discountAmount.toFixed(2) + ' €'"></span>
                                </div>
                            </template>
                        </div>

                        {{-- Hint for Coupon (Steps 1 & 2) --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 text-center" x-show="step < 3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.1em]">
                                ¿Tienes un cupón? Podrás canjearlo en el paso 3
                            </p>
                        </div>

                        {{-- SECCIÓN CUPÓN REFINADA --}}
                        <div class="mt-4 pt-4 border-t border-gray-100" x-show="step === 3" x-transition>
                            <div class="bg-gray-100/50 p-4 rounded-2xl">
                                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2 block">¿Tienes un cupón?</label>
                                <div class="flex flex-col gap-2">
                                    <template x-if="!appliedCouponCode">
                                        <input type="text" x-model="couponCode" placeholder="Ingresa el código" 
                                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-black transition-all uppercase placeholder:normal-case h-[48px]"
                                            @keydown.enter.prevent="applyCoupon()"
                                            :disabled="isValidatingCoupon">
                                    </template>
                                    
                                    <template x-if="appliedCouponCode">
                                        <div class="w-full bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm font-bold text-green-700 h-[48px] flex items-center justify-between">
                                            <span x-text="appliedCouponCode"></span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </template>

                                    <button type="button" @click="appliedCouponCode ? removeCoupon() : applyCoupon()" 
                                        class="w-full rounded-xl text-sm font-bold transition h-[48px] flex items-center justify-center shadow-sm active:scale-95"
                                        :class="appliedCouponCode ? 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50' : 'bg-black text-white hover:bg-gray-800 disabled:bg-gray-300'"
                                        :disabled="isValidatingCoupon || (!couponCode.trim() && !appliedCouponCode)">
                                        <template x-if="!isValidatingCoupon">
                                            <span x-text="appliedCouponCode ? 'Quitar' : 'Aplicar'"></span>
                                        </template>
                                        <template x-if="isValidatingCoupon">
                                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </template>
                                    </button>

                                    <p x-show="couponMessage" 
                                       :class="{
                                           'text-green-600': couponStatus === 'success',
                                           'text-red-500': couponStatus === 'error',
                                           'text-gray-500': couponStatus === 'info'
                                       }" 
                                       class="text-[11px] font-bold ml-1" 
                                       x-text="couponMessage"></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between pt-4 border-t-2 border-dashed border-gray-200 mt-4">
                            <span class="text-lg font-extrabold text-gray-900">Total</span>
                            <span class="text-2xl font-black text-gray-900" x-text="total.toFixed(2) + ' €'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Floating WhatsApp --}}
    <a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '34601111111' }}" target="_blank" class="fixed shadow-2xl bottom-8 right-8 bg-[#25D366] text-white p-4 rounded-full hover:scale-110 transition-all z-50">
        <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>
</x-app-layout>