<x-app-layout title="Aviso Legal">
    <div class="max-w-4xl mx-auto px-5 sm:px-8 lg:px-10 py-16 md:py-24">
        <h1 class="text-4xl md:text-5xl font-black tracking-tight text-gray-900 mb-8">Aviso Legal</h1>
        
        <div class="prose prose-lg max-w-none text-gray-600 space-y-6 leading-relaxed">
            <p class="font-medium text-gray-900 italic text-xl mb-12">En cumplimiento del artículo 10 de la Ley 34/2002, del 11 de julio, de servicios de la Sociedad de la Información y Comercio Electrónico (LSSICE).</p>

            <section class="grid grid-cols-1 md:grid-cols-2 gap-8 my-12">
                <div class="bg-gray-50 p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h2 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-4">Titular de la Web</h2>
                    <p class="text-gray-900 font-bold text-lg mb-1">{{ $settings['legal_shop_name'] ?? '[NOMBRE_EMPRESA]' }}</p>
                    <p class="text-gray-600 leading-snug">{{ $settings['legal_owner'] ?? '[NOMBRE_DEL_AUTONOMO_O_SOCIEDAD]' }}</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h2 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-4">Identificación</h2>
                    <p class="text-gray-900 font-bold text-lg mb-1">NIF/CIF: {{ $settings['legal_nif'] ?? '[NUMERO_IDENTIFICACION]' }}</p>
                    <p class="text-gray-600 leading-snug">Domicilio Social: {{ $settings['legal_full_address'] ?? '[DIRECCION_COMPLETA]' }}</p>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">1. Objeto</h2>
                <p>Este sitio web ha sido creado para facilitar el conocimiento y el acceso a los productos que ofrece <strong>{{ $settings['legal_shop_name'] ?? '[NOMBRE_EMPRESA]' }}</strong>. El uso de este sitio web le atribuye la condición de usuario e implica la aceptación de todas las condiciones incluidas en este Aviso Legal.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">2. Condiciones de Uso</h2>
                <p>El usuario se compromete a hacer un uso adecuado de los contenidos y servicios de la web, absteniéndose de realizar actividades ilícitas o contrarias a la buena fe y al orden público.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">3. Exclusión de Responsabilidad</h2>
                <p><strong>{{ $settings['legal_shop_name'] ?? '[NOMBRE_EMPRESA]' }}</strong> no se hace responsable de los daños y perjuicios de cualquier naturaleza que pudieran ocasionar, a título enunciativo: errores u omisiones en los contenidos, falta de disponibilidad del portal o la transmisión de virus o programas maliciosos.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">4. Enlaces</h2>
                <p>En el caso de que en la página web se dispusiesen enlaces o hipervínculos hacía otros sitios de Internet, <strong>{{ $settings['legal_shop_name'] ?? '[NOMBRE_EMPRESA]' }}</strong> no ejercerá ningún tipo de control sobre dichos sitios y contenidos.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wider mb-4">5. Legislación Aplicable</h2>
                <p>Las presentes condiciones se rigen por la legislación española. Para cualquier litigio que pudiera surgir relacionado con el sitio web o la actividad que en él se desarrolla serán competentes los Juzgados de {{ $settings['legal_city'] ?? '[CIUDAD_PRINCIPAL]' }}.</p>
            </section>

            <section class="bg-gray-950 text-white p-10 rounded-[2.5rem] mt-16 shadow-2xl">
                <h2 class="text-lg font-bold mb-4">¿Necesitas contactar con el departamento legal?</h2>
                <p class="text-gray-400 mb-6">Estamos a tu disposición para cualquier consulta sobre nuestros datos o avisos legales.</p>
                <a href="mailto:{{ $settings['legal_email'] ?? '[EMAIL_CONTACTO]' }}" class="inline-block bg-white text-gray-950 px-8 py-3 rounded-full font-bold hover:bg-gray-200 transition-colors">Enviar Email</a>
            </section>
        </div>
    </div>
</x-app-layout>
