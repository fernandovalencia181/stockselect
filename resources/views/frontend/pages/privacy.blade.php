<x-app-layout title="Política de Privacidad">
    <div class="max-w-4xl mx-auto px-5 sm:px-8 lg:px-10 py-16 md:py-24">
        <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 mb-8">Política de Privacidad</h1>
        
        <div class="prose prose-lg max-w-none text-gray-600 space-y-6 leading-relaxed">
            <p class="font-medium text-gray-900 italic">Última actualización: {{ date('d/m/Y') }}</p>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">1. Responsable del Tratamiento</h2>
                <p>En <strong>{{ $settings['legal_shop_name'] ?? '[NOMBRE_EMPRESA]' }}</strong>, nos comprometemos a proteger su privacidad. Esta política explica cómo recopilamos, usamos y protegemos sus datos personales cuando utiliza nuestro sitio web.</p>
                <p>Datos de contacto: {{ $settings['legal_email'] ?? '[EMAIL_CONTACTO]' }} | {{ $settings['legal_address'] ?? '[DIRECCION_FISCAL]' }}</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">2. Datos que Recopilamos</h2>
                <p>Recopilamos información necesaria para procesar sus pedidos y mejorar su experiencia:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Datos Identificativos: Nombre, apellidos.</li>
                    <li>Datos de Contacto: Correo electrónico, número de teléfono.</li>
                    <li>Datos de Envío: Dirección de entrega.</li>
                    <li>Datos de Pago: Procesados de forma segura a través de pasarelas de pago cifradas (Stripe/PayPal).</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">3. Finalidad del Tratamiento</h2>
                <p>Sus datos se utilizan exclusivamente para:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Gestionar y enviar sus pedidos.</li>
                    <li>Comunicar actualizaciones sobre el estado de su compra.</li>
                    <li>Responder a consultas de soporte.</li>
                    <li>Cumplir con obligaciones legales y fiscales.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">4. Conservación de Datos</h2>
                <p>Mantendremos sus datos personales solo durante el tiempo necesario para cumplir con los fines para los que fueron recopilados, incluyendo el cumplimiento de requisitos legales (generalmente 5 años por normativas fiscales).</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">5. Sus Derechos</h2>
                <p>Usted tiene derecho a acceder, rectificar, suprimir o limitar el tratamiento de sus datos. Para ejercer estos derechos, puede contactarnos en <strong>{{ $settings['legal_email'] ?? '[EMAIL_CONTACTO]' }}</strong>.</p>
            </section>

            <section class="bg-gray-50 p-8 rounded-3xl border border-gray-100 mt-12">
                <p class="text-sm">Esta página tiene fines informativos y forma parte de nuestro compromiso con la transparencia según el RGPD (Reglamento General de Protección de Datos).</p>
            </section>
        </div>
    </div>
</x-app-layout>
