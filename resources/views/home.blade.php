@extends('layouts.app')

@section('content')
    <div class="">
        <div class="row justify-content-center">
            <div class="col-12">

                <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap"
                    rel="stylesheet" />
                <link
                    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap"
                    rel="stylesheet" />
                <link
                    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
                    rel="stylesheet" />

                <div class="flex items-center justify-between mb-3 border-bottom border-sage-muted/20 pb-2">
                    <div class="items-center gap-3">
                        <h2 class="text-sage-900 dark:text-white text-xl font-bold leading-tight flex items-center gap-2 w-100"
                            style="font-size:18px;">
                            Inventory Dashboard
                        </h2>
                        <p class="text-sage-600 dark:text-sage-400 text-sm" style="color: #777777">Monitoring global
                            germplasm distribution and local storage health.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if (auth()->user()->hasRole(['super-admin', 'admin', 'manager']))
                            <a href="{{ route('report.reports') }}"
                                class="btn btn-sm btn-outline-primary font-bold">Report</a>
                            <a href="{{ route('lot-management.create') }}" class="btn btn-sm btn-primary box-radius"><i
                                    class="ri-add-line me-1"></i> New Lot/Batch</a>
                        @endif
                        <a href="{{ route('accessionform') }}" class="btn btn-sm btn-primary"><i
                                class="ri-add-line me-1"></i> New Accession</a>
                        <a href="{{ route('requests.create') }}" class="btn btn-sm btn-primary"><i
                                class="ri-add-line me-1"></i> New Request</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    <!-- Card 1 -->
                    <div
                        class="bg-white dark:bg-white/5 p-3 rounded-2xl border border-sage-muted/10 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between">
                            <div
                                class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center mb-3 text-primary">
                                <span class="material-symbols-outlined">genetics</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">
                                {{ number_format($totalAccessions) }}</h3>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Accessions</p>

                        <p class="text-sage-muted text-xs italic total-box-msg">Total germplasm entries</p>
                    </div>
                    <!-- Card 2 -->
                    <div
                        class="bg-white dark:bg-white/5 p-3 rounded-2xl border border-sage-muted/10 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between">
                            <div
                                class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center mb-3 text-primary">
                                <span class="material-symbols-outlined">eco</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">
                                {{ number_format($totalCrops) }}</h3>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Crops</p>
                        <p><span class="italic total-box-msg">Active: {{ $updatedCrops }}</span> <span  class="italic text-danger ms-5">Deactive: {{ $notUpdatedCrops }}</span></p>
                        <!--<p class="text-sage-muted text-xs italic total-box-msg">Crop master count</p>-->
                    </div>
                    <!-- Card 3 -->
                    
                    <!-- Card 4 -->
                    <div
                        class="bg-white dark:bg-white/5 p-3 rounded-2xl border border-sage-muted/10 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between">
                            <div
                                class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center mb-3 text-primary">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">
                                {{ number_format($totalLots) }}</h3>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total Lot/Batches</p>

                        <p class="text-sage-muted text-xs italic total-box-msg">Lot / Batch records</p>
                    </div>
                    <!-- Card 5 -->
                    <div
                        class="bg-white dark:bg-white/5 p-3 rounded-2xl border border-sage-muted/10 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between">
                            <div
                                class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center mb-3 text-primary">
                                <span class="material-symbols-outlined">apartment</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">
                                {{ number_format($totalWarehouses) }}</h3>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Warehouses</p>

                        <p class="text-sage-muted text-xs italic total-box-msg">Warehouse count</p>
                    </div>
                    <!-- Card 6 -->
                    <div
                        class="bg-white dark:bg-white/5 p-3 rounded-2xl border border-rose-500/20 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between">
                            <div class="flex justify-between items-start mb-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center text-rose-500">
                                    <span class="material-symbols-outlined">error</span>
                                </div>
                                <span
                                    class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Critical</span>
                            </div>
                            <h3 class="text-2xl font-bold text-rose-600 mt-1">14</h3>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Low Stock Alert</p>

                        <p class="text-sage-muted text-xs italic text-danger">Below minimum </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-9 col-lg-8 col-md-12 mt-4 mt-xl-0">
                        <!-- Storage Tank Status Section -->
                        <div class="mb-8 mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-lg font-bold text-slate-900 dark:text-slate-100" style="font-size:17px;">
                                    Storage Tank & Room Status</h4>
                                <a href="{{ route('storage-management.index') }}" class="view-all">View All Location</a>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                                <!--<div class="flex flex-col gap-4 rounded-xl p-4 border border-sage-200 dark:border-sage-800 bg-white dark:bg-sage-900 group cursor-pointer hover:border-primary transition-all">
        <div class="w-full aspect-video bg-sage-100 dark:bg-sage-800 rounded-lg overflow-hidden relative">
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        <div class="absolute bottom-2 left-2 text-white">
        <span class="bg-emerald-500 text-[10px] px-2 py-0.5 rounded-full font-bold">OPTIMAL</span>
        </div>
        <div class="w-full h-full bg-cover bg-center" data-alt="Laboratory cryo tank rows in clean room" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDOksLwrs65G8XxQBGucpIWdHUZKmXHU3NUlLcFtsz0e3m1oJUDeZ4ZPd1TpdHLJcsWtdYp_eL2c-zeUYN95Ig8KUJ4EgTxngBhAZAD96SVoMlQoXF_hyLGNb4RuubPJWd7vEBwneJfhG1LuMbPpklVqw33LuMOwMHe-4BZvunKbFirX4H0MqHb9YKLa2U06KDSpKF1pAdqIJHeglINTYySaUlPIGtJeWfTWsg0Ie-hflgorLeEc07CayjtEMfLWj39DVCpDZiyk1s');"></div>
        </div>
        <div class="flex flex-col">
        <p class="text-sage-900 dark:text-white text-base font-bold leading-tight">Cryo Tank Alpha-1</p>
        <div class="flex justify-between items-center mt-1">
        <p class="text-sage-500 dark:text-sage-400 text-xs font-medium">-196°C | Liquid Nitrogen</p>
        <p class="text-sage-900 dark:text-white text-xs font-bold">92% Full</p>
        </div>
        <div class="w-full bg-sage-100 dark:bg-sage-800 h-1 rounded-full mt-2">
        <div class="bg-primary h-full rounded-full" style="width: 92%"></div>
        </div>
        </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl p-4 border border-sage-200 dark:border-sage-800 bg-white dark:bg-sage-900 group cursor-pointer hover:border-primary transition-all">
        <div class="w-full aspect-video bg-sage-100 dark:bg-sage-800 rounded-lg overflow-hidden relative">
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        <div class="absolute bottom-2 left-2 text-white">
        <span class="bg-emerald-500 text-[10px] px-2 py-0.5 rounded-full font-bold">OPTIMAL</span>
        </div>
        <div class="w-full h-full bg-cover bg-center" data-alt="Indoor seed vault storage shelves" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuB7uJDW20a6SX7tQ8p_Tx8cNTf-4ZlOO7DFNP6-g2a2yhSOrXcDmkPudzHPpm9Z7yFLM92vxmxcXg_tQ3oBHiKfW4099gQNgpd1tH8vbOsuJQZW6Gk0X1uY_saD412Dri2IAcbvJN8O4lzHFGuK8zDo6h3dBA4b6ZE-FECVW8nxyPo7bCff0VD64xVjh03Yguy8-vN9tJ9fIbmMw57LWOJPenORVFqCJf6OTz6gWBIpy16_RsBE7kRgDaYVqPBw7M1BZ7GNgAvASks');"></div>
        </div>
        <div class="flex flex-col">
        <p class="text-sage-900 dark:text-white text-base font-bold leading-tight">Seed Vault Room 2</p>
        <div class="flex justify-between items-center mt-1">
        <p class="text-sage-500 dark:text-sage-400 text-xs font-medium">4°C | 25% Humidity</p>
        <p class="text-sage-900 dark:text-white text-xs font-bold">45% Full</p>
        </div>
        <div class="w-full bg-sage-100 dark:bg-sage-800 h-1 rounded-full mt-2">
        <div class="bg-sage-400 h-full rounded-full" style="width: 45%"></div>
        </div>
        </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl p-4 border border-sage-200 dark:border-sage-800 bg-white dark:bg-sage-900 group cursor-pointer hover:border-primary transition-all">
        <div class="w-full aspect-video bg-sage-100 dark:bg-sage-800 rounded-lg overflow-hidden relative">
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        <div class="absolute bottom-2 left-2 text-white">
        <span class="bg-amber-500 text-[10px] px-2 py-0.5 rounded-full font-bold text-sage-900">CHECK RE-ICE</span>
        </div>
        <div class="w-full h-full bg-cover bg-center" data-alt="Bio-sample freezer units in lab" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCRUFDrSspAFUnC59rmu1shTFWjBZZr95j_4ZWHz3BGlFMjf_q_TChpgUWC_cii6dcbdF_E4GGm0tY61S5Sw0Dr07moDU0toXT_sxZ5_Cj3RE8duxodIJcK4xuzD0y5CnTWILfYj8wwOYDbCHalZP7vB2yEyotOiGd5eP1A2P3K3Wo4cD1vchJ7dJL9nAyQ0egQqPICBNXZeqVuQ1PXVsfK3AOEptagfRQ4383UwgxgkHvlucYlirF2qdLQ8z0DegGHPIBl2PC-BDw');"></div>
        </div>
        <div class="flex flex-col">
        <p class="text-sage-900 dark:text-white text-base font-bold leading-tight">Cold Storage B-4</p>
        <div class="flex justify-between items-center mt-1">
        <p class="text-sage-500 dark:text-sage-400 text-xs font-medium">-20°C | Deep Freeze</p>
        <p class="text-sage-900 dark:text-white text-xs font-bold">78% Full</p>
        </div>
        <div class="w-full bg-sage-100 dark:bg-sage-800 h-1 rounded-full mt-2">
        <div class="bg-primary h-full rounded-full" style="width: 78%"></div>
        </div>
        </div>
        </div>

        <div class="flex flex-col gap-4 rounded-xl p-4 border border-sage-200 dark:border-sage-800 bg-white dark:bg-sage-900 group cursor-pointer hover:border-primary transition-all">
            <div class="w-full aspect-video bg-sage-100 dark:bg-sage-800 rounded-lg overflow-hidden relative">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute bottom-2 left-2 text-white">
            <span class="bg-emerald-500 text-[10px] px-2 py-0.5 rounded-full font-bold">OPTIMAL</span>
            </div>
            <div class="w-full h-full bg-cover bg-center" data-alt="Incubator chamber with glass door" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCNi8W53xt_Ozx7giDqjY1b2ORBJcC4ctXj_uUY8ZjRyzWUvEEXb3zE3xnUUJ4O2kRAiDV_PoUZ9h1lFyYajYcJU9aJs-HrjEFCIlo62M8vK2aXMepFfO2re4FMIpi3J_gjs56yFK6wUFOBLKVL6EMN1zxdry1bdn2V6CHkvcbvVmpQX3_h3IumXNMAO29rMI6mXKpkBXFbmhWAM_w1w1kLQiUCT0Kos9iKA9uG5bjcFQlp-tfbIJTcJvJCAvweNam4xQHiQ-1ZOQo');"></div>
            </div>
            <div class="flex flex-col">
            <p class="text-sage-900 dark:text-white text-base font-bold leading-tight">Incubator C-1</p>
            <div class="flex justify-between items-center mt-1">
            <p class="text-sage-500 dark:text-sage-400 text-xs font-medium">25°C | Germination</p>
            <p class="text-sage-900 dark:text-white text-xs font-bold">15% Full</p>
            </div>
            <div class="w-full bg-sage-100 dark:bg-sage-800 h-1 rounded-full mt-2">
            <div class="bg-sage-300 h-full rounded-full" style="width: 15%"></div>
            </div>
            </div>
        </div>-->
                                @forelse($storages as $storage)
                                    <div
                                        class="flex flex-col gap-4 rounded-xl p-3 border border-sage-200 dark:border-sage-800 bg-white dark:bg-sage-900 group cursor-pointer hover:border-primary transition-all">

                                        <div
                                            class="w-full aspect-video bg-sage-100 dark:bg-sage-800 rounded-lg overflow-hidden relative">

                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent">
                                            </div>

                                            <div class="absolute bottom-2 left-2 text-white">

                                                @if ($storage->usage_percentage > 90)
                                                    <span
                                                        class="bg-red-500 text-[10px] px-2 py-0.5 rounded-full font-bold">FULL</span>
                                                @elseif($storage->usage_percentage > 70)
                                                    <span
                                                        class="bg-amber-500 text-[10px] px-2 py-0.5 rounded-full font-bold">HIGH</span>
                                                @else
                                                    <span
                                                        class="bg-emerald-500 text-[10px] px-2 py-0.5 rounded-full font-bold">OPTIMAL</span>
                                                @endif

                                            </div>

                                            <div class="w-full h-full bg-cover bg-center"
                                                style="background-image:url('{{ $storage->image ? asset('storage/' . $storage->image) : asset('assets/images/storage-default.jpg') }}')">
                                            </div>

                                        </div>

                                        <div class="flex flex-col">

                                            <p class="text-sage-900 dark:text-white text-base font-bold leading-tight">
                                                {{ $storage->name }}
                                            </p>

                                            <div class="flex justify-between items-center mt-1">

                                                <p class="text-sage-500 dark:text-sage-400 text-xs font-medium">
                                                    {{ $storage->storageType->name ?? 'N/A' }} | {{ implode(', ', array_filter([
    //$storage->warehouse?->state?->state_name,
    $storage->warehouse?->district?->district_name,
    $storage->warehouse?->city?->city_village_name
])) }}
                                                </p>

                                                <p class="text-sage-900 dark:text-white text-xs font-bold">
                                                    {{ $storage->usage_percentage ?? 0 }}% Full
                                                </p>

                                            </div>

                                            <div class="w-full bg-sage-100 dark:bg-sage-800 h-1 rounded-full mt-2">

                                                <div class="
@if ($storage->usage_percentage > 90) bg-red-500
@elseif($storage->usage_percentage > 70) bg-amber-500
@else bg-primary @endif
h-full rounded-full"
                                                    style="width: {{ min($storage->usage_percentage ?? 0, 100) }}%">
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <div class="col-span-4 text-center text-slate-400 py-6">
                                        No storage locations found
                                    </div>
                                @endforelse



                            </div>
                        </div>

                        <!-- Data Table Section -->
                        <section
                            class="bg-white dark:bg-background-dark rounded-xl border border-sage-light dark:border-sage-deep/30 shadow-sm overflow-hidden">
                            <div
                                class="p-3 border-b border-sage-light dark:border-sage-deep/30 flex justify-between items-center">
                                <h4 class="text-lg font-bold text-slate-900 dark:text-slate-100" style="font-size:17px;">
                                    Recently Updated Germplasm(Accessions)</h4>

                                <a href="{{ route('accession.accession-list') }}" class="view-all">View All Records</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead
                                        class="bg-background-light dark:bg-sage-deep/10 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                                        <tr>
                                            <th class="px-3 py-2 font-semibold">Accession ID</th>
                                            <th class="px-3 py-2 font-semibold">Taxonomy</th>
                                            <th class="px-3 py-2 font-semibold">Quantity</th>
                                            <th class="px-3 py-2 font-semibold">Source</th>
                                            <th class="px-3 py-2 font-semibold">Duration</th>
                                            <th class="px-3 py-2 font-semibold">Created Date</th>
                                            <th class="px-3 py-2 font-semibold">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-sage-light dark:divide-sage-deep/30">
                                        <tr
                                            class="hover:bg-background-light/50 dark:hover:bg-sage-deep/5 transition-colors d-none">
                                            <td class="px-3 py-2 text-sm font-mono text-slate-900 dark:text-slate-100">
                                                ACC-4902-X</td>
                                            <td class="px-3 py-2 text-sm">
                                                <div class="italic">Triticum aestivum</div>
                                                <div class="text-[10px] text-slate-400 uppercase">Wheat / Poaceae</div>
                                            </td>
                                            <td class="px-3 py-2 text-sm">Central Anatolia, TR</td>
                                            <td class="px-3 py-2">
                                                <span
                                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase">Available</span>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-slate-500 dark:text-slate-400">14 Oct 2023
                                            </td>
                                            <td class="px-3 py-2">
                                                <button
                                                    class="material-symbols-outlined text-slate-400 hover:text-primary transition-colors">more_vert</button>
                                            </td>
                                        </tr>
                                        @forelse($recentAccessions as $accession)
                                            <tr
                                                class="hover:bg-background-light/50 dark:hover:bg-sage-deep/5 transition-colors">

                                                <td class="px-3 py-2 text-sm font-mono text-slate-900 dark:text-slate-100">
                                                    {{ $accession->accession_number }}
                                                </td>

                                                <td class="px-3 py-2 text-sm">
                                                    <div class="italic">{{ $accession->scientific_name }}</div>
                                                    <div class="text-[10px] text-slate-400 uppercase">
                                                        {{ $accession->crop->crop_name ?? '-' }}
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2 text-sm">
                                                    {{ $accession->quantity_show }} {{ $accession->unit }}
                                                </td>

                                                <td class="px-3 py-2 text-sm">
                                                    {{ $accession->source_location }}
                                                </td>

                                               <td class="px-3 py-2">
                                                    @php
                                                        $code = $accession->storageTime?->code;

                                                        $badgeClasses = [
                                                            'STS' => 'bg-success',
                                                            'MTS' => 'bg-info',
                                                            'LTS' => 'bg-danger',
                                                        ];

                                                        $class = $badgeClasses[$code] ?? 'bg-secondary';
                                                    @endphp

                                                    <span class="badge {{ $class }}">
                                                        {{ $code ?? 'N/A' }}
                                                    </span>
                                                </td>

                                                <td class="px-3 py-2 text-sm text-slate-500 dark:text-slate-400">
                                                    <!--{{ $accession->last_viability_check ? \Carbon\Carbon::parse($accession->last_viability_check)->format('d M Y') : 'Pending' }}<br>-->
                                                    <span>{{ $accession->updated_at->format('d M Y') }}</span>
                                                </td>

                                                <td class="px-3 py-2">
                                                    <!--<button
                                                        class="material-symbols-outlined text-slate-400 hover:text-primary transition-colors">
                                                        more_vert
                                                    </button>-->
                                                    <a class=""
                                                            data-id="{{ $accession->id }}" href="{{ route('accessions.show', $accession->id) }}">
                                                            <i class="ri-eye-line me-2"></i>Details
                                                        </a>
                                                </td>

                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-slate-400">
                                                    No records found
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-12 mt-4">
                        
                        
                        

                        <div
                            class="card card-height-80 bg-white dark:bg-background-dark rounded-xl border border-sage-light dark:border-sage-deep/30 shadow-sm overflow-hidden">
                            <div class="card-header align-items-center d-flex">
                                <div class="d-flex justify-content-between w-100">
                                    <h4 class="text-lg font-bold text-slate-900 dark:text-slate-100"
                                        style="font-size:17px;">Request</h4>
                                    <a href="{{ route('requests.index') }}" class="view-all">View All</a>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body p-2">

                                @forelse($latestRequests as $request)
                                    <div class="request-card mt-0 mb-2">

                                        <div class="d-flex justify-content-between">
                                            <div class="seed-name">
                                                Request-{{ $request->id }}
                                            </div>
                                            <div class="">
                                                @if ($request->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($request->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($request->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-6">
                                                <div>{{ $request->crop->crop_name ?? '-' }}
                                                </div>
                                            </div>

                                            <div class="col-6 text-end">
                                                <div class="qty">Qty. {{ $request->quantity }}G</div>
                                            </div>
                                            <div class="col-6 text-[12px] text-slate-400">
                                              {{ $request->request_date ? $request->request_date->format('d M Y') : '-' }}
                                            </div>
                                            <div class="col-6 text-end text-[12px] text-slate-400">
                                                Name: {{ $request->requester_name ?? '-' }}
                                            </div>
                                        </div>
                                    </div>

                                @empty

                                    <div class="text-center text-muted py-3">
                                        No requests found
                                    </div>
                                @endforelse

                            </div>
                        </div> <!-- .card-->

                        <div
                            class="card bg-white dark:bg-background-dark rounded-xl border border-sage-light dark:border-sage-deep/30 shadow-sm overflow-hidden">
                            <div class="card-header align-items-center d-flex">
                                <div class="d-flex justify-content-between w-100">
                                    <h4 class="text-lg font-bold text-slate-900 dark:text-slate-100"
                                        style="font-size:17px;">Update/Expire Date</h4>
                                    <a href="{{ route('expiry.report') }}" class="view-all">View All</a>
                                </div>

                            </div><!-- end card header -->

                            <div class="card-body p-2">
                                @forelse($expiringSoon as $accession)
                                @php
                                    $daysLeft = \Carbon\Carbon::today()->diffInDays($accession->expiry_date, false);
                                @endphp

                                <div class="seed-card mt-2">
                                    <div class="row">
                                        <div class="col-7">
                                            <div class="seed-name">
                                                {{ $accession->crop->crop_name ?? 'N/A' }}
                                            </div>
                                            <div class="entry">
                                                Entry: {{ $accession->created_at?->format('d M Y') }}
                                            </div>
                                        </div>

                                        <div class="col-5 text-end">
                                            <div class="code">
                                                {{ $accession->accession_name ?? $accession->accession_number }}
                                            </div>

                                            <div class="expire 
                                                {{ $daysLeft <= 3 ? 'text-danger fw-bold' : ($daysLeft <= 7 ? 'text-warning' : 'text-success') }}">
                                                
                                                Expire: {{ $accession->expiry_date?->format('d F Y') }}
                                                <br>
                                                <small>({{ $daysLeft }} days left)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <div class="text-center text-muted p-3">
                                    No accessions expiring in next 10 days
                                </div>
                            @endforelse
                            </div>
                        </div> <!-- .card-->
                    </div> <!-- .col-->
                </div>


                <div class="col d-none">

                    <div class="h-100">
                        <div class="row mb-3 pb-1">
                            <div class="col-12">
                                <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                    <div class="flex-grow-1">
                                        <h4 class="fs-16 mb-1">Good Morning, Mahesh!</h4>
                                        <p class="text-muted mb-0">Here's what's happening with your store today.</p>
                                    </div>

                                </div><!-- end card header -->
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->



                        <div class="row">
                            <div class="col-xl-4">
                                <div class="card card-height-100">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">title</h4>

                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        gfh
                                    </div>
                                </div> <!-- .card-->
                            </div> <!-- .col-->

                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Recent Orders</h4>

                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        dd
                                    </div>
                                </div> <!-- .card-->
                            </div> <!-- .col-->
                        </div> <!-- end row-->

                    </div> <!-- end .h-100-->

                </div> <!-- end col -->
                <div class="container d-none">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">{{ __('Dashboard') }}</div>

                                <div class="card-body">
                                    @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    {{ __('You are logged in!') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>



            <!-- Dashboard init -->
            <!--<script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>-->
        @endsection
