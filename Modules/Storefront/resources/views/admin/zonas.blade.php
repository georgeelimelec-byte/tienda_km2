@extends('layouts.admin')

@section('title', 'Delivery')
@section('page-title', 'Zonas de Delivery')
@section('page-kicker', 'Modulo de tienda')

@section('topbar-actions')
    <span class="topbar-badge"><i class="fas fa-map-location-dot"></i> Tarifas por zona</span>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto w-full" x-data="{ modalOpen: false, editMode: false, currentZona: {} }">
        
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Zonas de Delivery</h2>
                <p class="text-gray-500 mt-1">Configura los distritos y tarifas que aparecerán en el checkout del cliente.</p>
            </div>
            <button @click="editMode = false; currentZona = {nombre:'', tarifa:0, estado:'Activo'}; modalOpen = true" class="bg-brand hover:bg-brand-dark text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-brand/30 transition-all flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nueva Zona
            </button>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl shadow-sm mb-6" role="alert">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre del Distrito / Zona</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tarifa (S/)</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($zonas as $zona)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ $zona->id_zona }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900">{{ $zona->nombre }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-brand">S/ {{ number_format($zona->tarifa, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $zona->estado == 'Activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $zona->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="editMode = true; currentZona = { id: {{ $zona->id_zona }}, nombre: '{{ addslashes($zona->nombre) }}', tarifa: {{ $zona->tarifa }}, estado: '{{ $zona->estado }}' }; modalOpen = true" class="text-brand hover:text-brand-dark mr-3 transition-colors">
                                    Editar
                                </button>
                                <form action="{{ route('admin.zonas.delete', $zona->id_zona) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro de eliminar esta zona?');">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 bg-gray-50/50">
                                No hay zonas configuradas. Los clientes no podran hacer pedidos si no tienen zonas disponibles.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Modal Alpine -->
    <div x-show="modalOpen" class="fixed inset-0 z-[200] overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modalOpen" @click="modalOpen = false" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <form :action="editMode ? '{{ url()->to('admin/zonas-delivery') }}/' + currentZona.id + '/update' : '{{ route('admin.zonas.store') }}'" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-brand/10 sm:mx-0 sm:h-12 sm:w-12">
                                <svg class="h-6 w-6 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-extrabold text-gray-900" x-text="editMode ? 'Editar Zona de Delivery' : 'Nueva Zona de Delivery'"></h3>
                                
                                <div class="mt-6 space-y-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre (Distrito/Zona) <span class="text-red-500">*</span></label>
                                        <input type="text" name="nombre" x-model="currentZona.nombre" required class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="Ej. Miraflores Centro">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tarifa S/ <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.1" name="tarifa" x-model="currentZona.tarifa" required class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all" placeholder="10.00">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                                        <select name="estado" x-model="currentZona.estado" class="w-full border-gray-200 rounded-xl px-4 py-2.5 bg-gray-50 focus:bg-white focus:border-brand focus:ring-brand outline-none transition-all">
                                            <option value="Activo">Activo</option>
                                            <option value="Inactivo">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" @click="modalOpen = false" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-5 py-2.5 rounded-xl font-bold transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-brand hover:bg-brand-dark text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-md shadow-brand/20">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
