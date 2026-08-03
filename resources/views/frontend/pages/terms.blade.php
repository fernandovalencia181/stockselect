<x-app-layout title="Términos y Condiciones">
    <div class="max-w-4xl mx-auto px-5 sm:px-8 lg:px-10 py-16 md:py-24">
        <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 mb-8">Términos y Condiciones</h1>
        
        <div class="prose prose-lg max-w-none text-gray-600 space-y-6 leading-relaxed">
            <p class="font-medium text-gray-900 italic">Última actualización: {{ date('d/m/Y') }}</p>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">1. Introducción</h2>
                <p>Bienvenido a <strong>{{ $settings['legal_shop_name'] ?? '[NOMBRE_EMPRESA]' }}</strong>. Al acceder y utilizar este sitio web, usted acepta cumplir y estar sujeto a los siguientes términos y condiciones de uso.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">2. Condiciones de Venta</h2>
                <p>Todos los productos están sujetos a disponibilidad de stock. Debido a que somos un outlet con stock limitado, la reserva de productos en el carrito no garantiza la compra hasta que el pago se haya completado correctamente.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">3. Precios y Pagos</h2>
                <p>Los precios incluyen el IVA aplicable. Nos reservamos el derecho de modificar los precios en cualquier momento sin previo aviso. Los pagos se realizan a través de métodos seguros y cifrados.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">4. Envíos y Devoluciones</h2>
                <p>Realizamos envíos en 24/48h laborales. El cliente tiene derecho a desistir de la compra en un plazo de 14 días naturales desde la recepción del producto, siempre que este se encuentre en perfecto estado y con sus etiquetas originales.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">5. Propiedad Intelectual</h2>
                <p>Todo el contenido de este sitio (imágenes, textos, logotipos) es propiedad de <strong>{{ $settings['legal_shop_name'] ?? '[NOMBRE_EMPRESA]' }}</strong> o sus licenciantes y está protegido por las leyes de propiedad intelectual.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">6. Limitación de Responsabilidad</h2>
                <p><strong>{{ $settings['legal_shop_name'] ?? '[NOMBRE_EMPRESA]' }}</strong> no será responsable de ningún daño indirecto o consecuente que surja del uso de los productos adquiridos en este sitio.</p>
            </section>

            <section class="bg-gray-50 p-8 rounded-3xl border border-gray-100 mt-12">
                <p class="text-sm">Para cualquier duda legal sobre estas condiciones, por favor contacte con nuestro equipo de atención al cliente.</p>
            </section>
        </div>
    </div>
</x-app-layout>
