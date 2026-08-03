<x-app-layout title="Guía de Tallas">
    <div class="max-w-5xl mx-auto px-5 sm:px-8 lg:px-10 py-16 md:py-24">
        <div class="text-center mb-16">
            <h2 class="text-sm font-black uppercase tracking-[0.3em] text-amber-500 mb-4">Ajuste Perfecto</h2>
            <h1 class="text-4xl md:text-6xl font-black tracking-tight text-gray-900 leading-tight">Guía de Tallas</h1>
            <p class="mt-6 text-lg text-gray-500 max-w-2xl mx-auto">Encuentra tu talla ideal para cada categoría de producto. Todas las medidas están expresadas en centímetros.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
            {{-- Calzado --}}
            <div class="bg-gray-50 rounded-[2.5rem] p-6 md:p-10 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col h-full">
                <div class="flex items-center gap-5 mb-10">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                        <svg class="w-7 h-7 text-gray-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 10.42 4.8-5.07"/><path d="M19 18h3"/><path d="M9.5 22 21.414 9.415A2 2 0 0 0 21.2 6.4l-5.61-4.208A1 1 0 0 0 14 3v2a2 2 0 0 1-1.394 1.906L8.677 8.053A1 1 0 0 0 8 9c-.155 6.393-2.082 9-4 9a2 2 0 0 0 0 4h14"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Zapatillas</h3>
                        <p class="text-[12px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Calzado Unisex (Adulto)</p>
                    </div>
                </div>
                <div class="overflow-x-auto -mx-2 px-2 scrollbar-hide">
                    <table class="w-full text-left text-[14px] min-w-[300px]">
                        <thead>
                            <tr class="text-gray-400 font-black uppercase tracking-widest text-[10px] border-b border-gray-200">
                                <th class="py-4 px-2">EU (Talla)</th>
                                <th class="py-4 px-2 text-right">Largo (CM)</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-900 font-medium">
                            @php
                                $shoeSizes = [
                                    '36' => '22.1', '36 2/3' => '22.5', '37 1/3' => '22.9', '38' => '23.3',
                                    '38 2/3' => '23.8', '39 1/3' => '24.2', '40' => '24.6', '40 2/3' => '25.0',
                                    '41 1/3' => '25.5', '42' => '25.9', '42 2/3' => '26.3', '43 1/3' => '26.7',
                                    '44' => '27.1', '44 2/3' => '27.6', '45 1/3' => '28.0', '46' => '28.4',
                                    '46 2/3' => '28.8', '47 1/3' => '29.3', '48' => '29.7'
                                ];
                            @endphp
                            @foreach($shoeSizes as $eu => $cm)
                                <tr class="border-b border-gray-100/50 hover:bg-white/50 transition-colors">
                                    <td class="py-3 px-2 font-bold">{{ $eu }}</td>
                                    <td class="py-3 px-2 text-right text-gray-500">{{ $cm }} cm</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Superior --}}
            <div class="bg-gray-50 rounded-[2.5rem] p-6 md:p-10 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col h-full">
                <div class="flex items-center gap-5 mb-10">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                        <svg class="w-7 h-7 text-gray-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Parte Superior</h3>
                        <p class="text-[12px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Camisetas, Sudaderas, Parkas</p>
                    </div>
                </div>
                <div class="overflow-x-auto -mx-2 px-2 scrollbar-hide text-[13px]">
                    <table class="w-full text-left min-w-[350px]">
                        <thead>
                            <tr class="text-gray-400 font-black uppercase tracking-widest text-[10px] border-b border-gray-200">
                                <th class="py-4 px-2">Filtro</th>
                                <th class="py-4 px-2">EU (Num)</th>
                                <th class="py-4 px-2">Pecho (cm)</th>
                                <th class="py-4 px-2 text-right">Cintura (cm)</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-900 font-medium">
                            @php
                                $upperSizes = [
                                    ['alpha' => 'XXS', 'numeric' => '32', 'chest' => '73 - 76', 'waist' => '57 - 60'],
                                    ['alpha' => 'XS', 'numeric' => '34', 'chest' => '77 - 82', 'waist' => '61 - 66'],
                                    ['alpha' => 'S', 'numeric' => '36', 'chest' => '83 - 88', 'waist' => '67 - 72'],
                                    ['alpha' => 'M', 'numeric' => '38', 'chest' => '89 - 94', 'waist' => '73 - 78'],
                                    ['alpha' => 'L', 'numeric' => '40', 'chest' => '95 - 101', 'waist' => '79 - 85'],
                                    ['alpha' => 'XL', 'numeric' => '42', 'chest' => '102 - 109', 'waist' => '86 - 94'],
                                    ['alpha' => '2XL', 'numeric' => '44', 'chest' => '110 - 118', 'waist' => '95 - 104'],
                                    ['alpha' => '3XL', 'numeric' => '46', 'chest' => '119 - 127', 'waist' => '105 - 114'],
                                ];
                            @endphp
                            @foreach($upperSizes as $s)
                                <tr class="border-b border-gray-100/50 hover:bg-white/50 transition-colors">
                                    <td class="py-3.5 px-2 font-bold">{{ $s['alpha'] }}</td>
                                    <td class="py-3.5 px-2 text-gray-400 font-bold">{{ $s['numeric'] }}</td>
                                    <td class="py-3.5 px-2 text-gray-500">{{ $s['chest'] }}</td>
                                    <td class="py-3.5 px-2 text-right text-gray-500">{{ $s['waist'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Inferior --}}
            <div class="bg-gray-50 rounded-[2.5rem] p-6 md:p-10 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col h-full">
                <div class="flex items-center gap-5 mb-10">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                        <svg class="w-7 h-7 text-gray-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 6h16"/><path d="M6 22a2 2 0 0 1-2-2V3c0-.6.4-1 1-1h14c.6 0 1 .4 1 1v17a2 2 0 0 1-2 2h-3l-3-10-3 10Z"/><path d="m6 11-2 1"/><path d="M9 8.5V6"/><path d="M15 6v2.5"/><path d="m20 12-2-1"/><path d="M4 18h6"/><path d="M14 18h6"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Parte Inferior</h3>
                        <p class="text-[12px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Pantalones, Bermudas, Tech Fleece</p>
                    </div>
                </div>
                <div class="overflow-x-auto -mx-2 px-2 scrollbar-hide text-[13px]">
                    <table class="w-full text-left min-w-[350px]">
                        <thead>
                            <tr class="text-gray-400 font-black uppercase tracking-widest text-[10px] border-b border-gray-200">
                                <th class="py-4 px-2">Filtro</th>
                                <th class="py-4 px-2">EU (Num)</th>
                                <th class="py-4 px-2">Cintura (cm)</th>
                                <th class="py-4 px-2 text-right">Cadera (cm)</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-900 font-medium">
                            @php
                                $lowerSizes = [
                                    ['alpha' => 'XXS', 'numeric' => '32', 'waist' => '57 - 60', 'hip' => '82 - 85'],
                                    ['alpha' => 'XS', 'numeric' => '34', 'waist' => '61 - 66', 'hip' => '86 - 91'],
                                    ['alpha' => 'S', 'numeric' => '36', 'waist' => '67 - 72', 'hip' => '92 - 97'],
                                    ['alpha' => 'M', 'numeric' => '38', 'waist' => '73 - 78', 'hip' => '98 - 103'],
                                    ['alpha' => 'L', 'numeric' => '40', 'waist' => '79 - 85', 'hip' => '104 - 110'],
                                    ['alpha' => 'XL', 'numeric' => '42', 'waist' => '86 - 94', 'hip' => '111 - 117'],
                                    ['alpha' => '2XL', 'numeric' => '44', 'waist' => '95 - 104', 'hip' => '118 - 125'],
                                    ['alpha' => '3XL', 'numeric' => '46', 'waist' => '105 - 114', 'hip' => '126 - 135'],
                                ];
                            @endphp
                            @foreach($lowerSizes as $s)
                                <tr class="border-b border-gray-100/50 hover:bg-white/50 transition-colors">
                                    <td class="py-3.5 px-2 font-bold">{{ $s['alpha'] }}</td>
                                    <td class="py-3.5 px-2 text-gray-400 font-bold">{{ $s['numeric'] }}</td>
                                    <td class="py-3.5 px-2 text-gray-500">{{ $s['waist'] }}</td>
                                    <td class="py-3.5 px-2 text-right text-gray-500">{{ $s['hip'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Otros --}}
            <div class="bg-black rounded-[2.5rem] p-6 md:p-10 border border-gray-800 shadow-xl self-start">
                <div class="flex items-center gap-5 mb-10">
                    <div class="w-14 h-14 bg-gray-900 rounded-2xl flex items-center justify-center shadow-sm">
                        <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="7"/><polyline points="12 9 12 12 13.5 13.5"/><path d="M16.51 17.35l-.35 3.83a2 2 0 0 1-2 1.82H9.84a2 2 0 0 1-2-1.82l-.35-3.83m.01-10.7l.35-3.83A2 2 0 0 1 9.84 1H14.16a2 2 0 0 1 2 1.82l.35 3.83"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-white tracking-tight">Accesorios</h3>
                        <p class="text-[12px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">Gorras, Calcetines, Bolsos</p>
                    </div>
                </div>
                <div class="bg-gray-900/50 rounded-2xl p-6 border border-gray-800">
                    <h4 class="text-white font-bold text-sm mb-2">Talla Única (One Size)</h4>
                    <p class="text-gray-400 text-xs leading-relaxed">
                        La mayoría de nuestros accesorios están diseñados para adaptarse a todos los usuarios mediante sistemas de ajuste (gorras) o materiales elásticos (calcetines).
                    </p>
                </div>
                <p class="mt-10 text-[11px] text-gray-500 font-medium leading-relaxed italic">
                    * Si tienes dudas específicas sobre un modelo, no dudes en contactarnos vía WhatsApp con el nombre del producto.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
