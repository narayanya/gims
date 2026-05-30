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
                                    class="ri-add-line me-1"></i> New Arrival(Lot)</a>
                        
                            <a href="{{ route('accessionform') }}" class="btn btn-sm btn-primary"><i
                                class="ri-add-line me-1"></i> New Accession</a>
                        @endif
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
                            <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ $lowStockCount }}</h3>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Low Stock Alert</p>
                        <p class="text-sage-muted text-xs italic text-danger">Below {{ 10 }} units</p>
                    </div>
                </div>
                @php
                    $user = auth()->user();
                @endphp
                

                <div class="row">
                   
                    <div class="col-xl-9 col-lg-8 col-md-12 mt-4 mt-xl-0">
                        {{-- Today Transaction --}}
                        @if($user->hasRole(['super-admin','admin','manager', 'researcher', 'dispatcher']))
<div class="mt-4">
    <div class="flex items-center justify-between mb-3">
        <h4 class="text-lg font-bold text-slate-800 dark:text-slate-100">
            Today Transaction
        </h4>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">

        {{-- Add Accession --}}
        <div
            class="bg-white dark:bg-background-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-3 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs text-slate-500 mb-1">
                        Add Accession
                    </p>

                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                        {{ $todayAccessionCount }}
                    </h3>
                </div>

                <div
                    class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4" />
                    </svg>

                </div>

            </div>
        </div>

        {{-- Add Lot --}}
        <div
            class="bg-white dark:bg-background-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-3 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs text-slate-500 mb-1">
                        Add Lot
                    </p>

                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                        {{ $todayLotCount }}
                    </h3>
                </div>

                <div
                    class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M20 13V7a2 2 0 00-2-2h-3V3H9v2H6a2 2 0 00-2 2v6m16 0v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4m16 0H4" />
                    </svg>

                </div>

            </div>
        </div>

        {{-- New Request --}}
        <div
            class="bg-white dark:bg-background-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-3 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs text-slate-500 mb-1">
                        New Request
                    </p>

                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                        {{ $todayRequestCount }}
                    </h3>
                </div>

                <div
                    class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 10h8m-8 4h5m-7 6h10a2 2 0 002-2V6a2 2 0 00-2-2H8l-4 4v10a2 2 0 002 2z" />
                    </svg>

                </div>

            </div>
        </div>

        {{-- Dispatching --}}
        <div
            class="bg-white dark:bg-background-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-3 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs text-slate-500 mb-1">
                        Dispatching
                    </p>

                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                        {{ $todayDispatchCount }}
                    </h3>
                </div>

                <div
                    class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 17l-4-4m0 0l4-4m-4 4h16" />
                    </svg>

                </div>

            </div>
        </div>

        {{-- Inter Transfer --}}
        <div
            class="bg-white dark:bg-background-dark rounded-2xl border border-slate-200 dark:border-slate-700 p-3 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs text-slate-500 mb-1">
                        Inter Transfer
                    </p>

                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100">
                        {{ $todayTransferCount }}
                    </h3>
                </div>

                <div
                    class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg>

                </div>

            </div>
        </div>

    </div>

</div>
@endif

@if($user->hasRole(['super-admin','admin','manager']))
                        <!-- Storage Tank Status Section -->
                        <div class="mb-8 mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-lg font-bold text-slate-900 dark:text-slate-100" style="font-size:17px;">
                                    Storage Tank & Room Status</h4>
                                <a href="{{ route('storage-management.index') }}" class="view-all">View All Location</a>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                                                    {{ $storage->usage_percentage ?? 0 }}% Fill
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
                        @endif
                        <!-- Low Stock Alert Section -->
                        @if($lowStockAccessions->count())
                        <div class="mb-4 mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-lg font-bold text-rose-600" style="font-size:17px;">
                                    <span class="material-symbols-outlined align-middle text-rose-500" style="font-size:18px;">error</span>
                                    Low Stock Alert
                                    <span class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded-full ms-2">{{ $lowStockCount }} accession(s) below 10 units</span>
                                </h4>
                            </div>
                            <div class="bg-white rounded-xl border border-rose-200 shadow-sm overflow-hidden">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-rose-50 text-rose-700 text-xs uppercase tracking-wider">
                                        <tr>
                                            <th class="px-3 py-2">Accession No.</th>
                                            <th class="px-3 py-2">Crop</th>
                                            <th class="px-3 py-2">Accession Name</th>
                                            <th class="px-3 py-2 text-end">Available Qty</th>
                                            <th class="px-3 py-2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-rose-100">
                                        @foreach($lowStockAccessions as $ls)
                                        <tr class="hover:bg-rose-50 transition-colors">
                                            <td class="px-3 py-2 font-mono font-semibold text-slate-800">{{ $ls->accession_number }}</td>
                                            <td class="px-3 py-2 text-slate-600">{{ $ls->crop->crop_name ?? '—' }}</td>
                                            <td class="px-3 py-2 text-slate-600">{{ $ls->accession_name ?? '—' }}</td>
                                            <td class="px-3 py-2 text-end">
                                                <span class="font-bold {{ ($ls->total_available ?? 0) <= 5 ? 'text-red-600' : 'text-amber-600' }}">
                                                    {{ number_format($ls->total_available ?? 0, 2) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <a href="{{ route('accessions.show', $ls->id) }}" class="text-primary text-xs">
                                                    <i class="ri-eye-line me-1"></i>View
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        @if (auth()->user()->hasRole(['super-admin', 'admin', 'manager']))
                        <div class="row">
                            <div class="col-md-8">
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0 fw-bold">Monthly Transactions</h5>
                                </div>

                                <div style="position: relative; height:380px;">
                                    <canvas id="monthlyTransactionChart"></canvas>
                                </div>

                            </div>
                        </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card shadow-sm border-0 rounded-4 mb-4">
                                     <div class="card-header">
                                        <h5>Accessions by Crop</h5>
                                    </div>

                                    <div class="card-body">
                                        <div id="cropDonutChart"></div>
                                    </div>
                                </div>   
                            </div>
                        </div>
                        @endif

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                    <script>
                    const monthlyData = @json($monthlyData);

                    const labels = monthlyData.map(item => item.month);

                    new Chart(document.getElementById('monthlyTransactionChart'), {

                        type: 'bar',

                        data: {
                            labels: labels,

                            datasets: [

                                {
                        label: 'Incoming',
                        data: monthlyData.map(item => item.incoming),
                        backgroundColor: '#4F8EF7',
                        borderRadius: 10,
                        borderSkipped: false,
                        categoryPercentage: 0.5,
                        barPercentage: 0.6,
                        maxBarThickness: 22,
                    },

                    {
                        label: 'Dispatch',
                        data: monthlyData.map(item => item.dispatch),
                        backgroundColor: '#57C271',
                        borderRadius: 10,
                        borderSkipped: false,
                        categoryPercentage: 0.5,
                        barPercentage: 0.6,
                        maxBarThickness: 22,
                    },

                    {
                        label: 'Transfer',
                        data: monthlyData.map(item => item.transfer),
                        backgroundColor: '#F28C4B',
                        borderRadius: 10,
                        borderSkipped: false,
                        categoryPercentage: 0.5,
                        barPercentage: 0.6,
                        maxBarThickness: 22,
                    },

                    {
                        label: 'QC Entries',
                        data: monthlyData.map(item => item.qc),
                        backgroundColor: '#F2C94C',
                        borderRadius: 10,
                        borderSkipped: false,
                        categoryPercentage: 0.5,
                        barPercentage: 0.6,
                        maxBarThickness: 22,
                    }

                            ]
                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            interaction: {
                                mode: 'index',
                                intersect: false
                            },

                            layout: {
                        padding: {
                            left: 5,
                            right: 5
                        }
                    },

                            plugins: {

                                legend: {
                                    position: 'bottom',

                                    labels: {
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        padding: 25,
                                        font: {
                                            size: 14
                                        }
                                    }
                                },

                                tooltip: {

                                    backgroundColor: '#fff',
                                    titleColor: '#111',
                                    bodyColor: '#111',
                                    borderColor: '#ddd',
                                    borderWidth: 1,
                                    padding: 14,

                                    displayColors: true,

                                    callbacks: {
                                        label: function(context) {
                                            return `${context.dataset.label}   ${context.raw}`;
                                        }
                                    }
                                }
                            },

                            scales: {

                                x: {
                                    stacked: false,
                                    grid: {
                                        display: false
                                    },

                                    ticks: {
                                        color: '#666',
                                        font: {
                                            size: 13
                                        }
                                    }
                                },

                                y: {

                                    beginAtZero: true,

                                    grid: {
                                        borderDash: [5,5],
                                        color: '#e5e7eb'
                                    },

                                    ticks: {
                                        stepSize: 50,
                                        color: '#666',
                                        font: {
                                            size: 12
                                        }
                                    },

                                    border: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                    </script>

                    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>



                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0 fw-bold">
                                    Total Available Seed Quantity in Gram
                                </h5>
                            </div>

                            <div style="position: relative; height:350px;">
                                <canvas id="cropQuantityChart"></canvas>
                            </div>

                        </div>
                    </div>

                    <script>

                        const cropStockData = @json($cropStockChart);

                        const cropLabels = cropStockData.map(item => item.crop_name);

                        const cropQuantities = cropStockData.map(item => 
                            parseFloat(item.total_quantity) || 0
                        );


                        new Chart(document.getElementById('cropQuantityChart'), {

                            type: 'bar',

                            data: {
                                labels: cropLabels,

                                datasets: [{
                                    label: 'Available Quantity',

                                    data: cropQuantities,

                                    backgroundColor: [
                                        '#4F8EF7',
                                        '#57C271',
                                        '#F28C4B',
                                        '#F2C94C',
                                        '#9B51E0',
                                        '#EB5757'
                                    ],

                                    borderRadius: 10,
                                    borderSkipped: false,

                                    categoryPercentage: 0.6,
                                    barPercentage: 0.7,

                                    maxBarThickness: 32
                                }]
                            },

                            options: {

                                responsive: true,

                                maintainAspectRatio: false,

                                plugins: {

                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                        </script>

                        @if($user->hasRole(['super-admin','admin','manager']))
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
                                                    {{ number_format($accession->total_available ?? $accession->quantity_show ?? 0, 2) }}
                                                    {{ $accession->capacityUnit?->name ?? '' }}
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
                        
                        <section
    class="bg-white dark:bg-background-dark rounded-xl border border-sage-light dark:border-sage-deep/30 shadow-sm overflow-hidden mt-4">

    <div
        class="p-3 border-b border-sage-light dark:border-sage-deep/30 flex justify-between items-center">

        <h4 class="text-lg font-bold text-slate-900 dark:text-slate-100"
            style="font-size:17px;">
            Lot Inter Transfer
        </h4>

        <a href="{{ route('lot-transfer.index') }}" class="view-all">
            View All Records
        </a>
    </div>

    {{-- Horizontal Scroll --}}
    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-slate-300">

        <div class="flex gap-4 p-4 min-w-max">

            @forelse($lotTransfers as $transfer)

                <div
                    class="w-[340px] flex-shrink-0 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 shadow-sm hover:shadow-lg transition duration-300">

                    {{-- Header --}}
                    <div class="p-3 border-b border-slate-200 dark:border-slate-700">

                        <div class="flex justify-between items-start">
                            <div>
                                <h5 class="font-bold text-slate-800 dark:text-slate-100">
                                  {{ $transfer->lot->lot_number }}
                                </h5>

                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $transfer->created_at->format('d-m-Y') }}
                                </p>
                            </div>

                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                Qty {{ $transfer->quantity }}
                            </span>
                        </div>

                        <div class="mt-3">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                {{ $transfer->crop->crop_name ?? '-' }}
                            </p>

                            <p class="text-xs text-slate-500">
                                Accession:
                                {{ $transfer->accession->accession_number ?? '-' }}
                            </p>
                        </div>

                    </div>

                    {{-- Body --}}
                    <div class="p-2 space-y-4">

                        {{-- From --}}
                        <div
                            class="rounded-xl bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800 p-2">

                            <p class="text-xs font-bold text-red-600 mb-2">
                                Storage FROM
                            </p>

                            <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">

                                <p>
                                    {{ $transfer->fromStorage->name ?? '-' }}<i class="ri-arrow-right-line me-1 text-danger"></i> {{ $transfer->fromRack->name ?? '-' }}<i class="ri-arrow-right-line me-1 text-danger"></i> {{ $transfer->fromBin->name ?? '-' }}<i class="ri-arrow-right-line me-1 text-danger"></i> {{ $transfer->fromContainer->name ?? '-' }}
                                </p>

                            </div>
                        </div>

                        {{-- To --}}
                        <div class="rounded-xl bg-green-50 dark:bg-green-900/10 border border-green-100 dark:border-green-800 p-2 mt-2">
                            <p class="text-xs font-bold text-green-600 mb-2">
                                TO
                            </p>

                            <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                                <p>
                                    {{ $transfer->toStorage->name ?? '-' }}<i class="ri-arrow-right-line me-1 text-success"></i> {{ $transfer->toRack->name ?? '-' }}<i class="ri-arrow-right-line me-1 text-success"></i> {{ $transfer->toBin->name ?? '-' }}<i class="ri-arrow-right-line me-1 text-success"></i> {{ $transfer->toContainer->name ?? '-' }}
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

            @empty

                <div class="w-full text-center py-10 text-slate-500">
                    No transfer records found.
                </div>

            @endforelse

        </div>

    </div>

</section>
@endif
                    </div>

                    <div class="col-xl-3 col-lg-4 col-md-12 mt-4">
                        @if($user->hasRole(['super-admin','admin','manager', 'researcher', 'dispatcher']))
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
                        @endif
                        @if($user->hasRole(['super-admin','admin','manager']))
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
                        @endif
                        
                        @if($user->hasRole(['super-admin','admin','manager', 'researcher', 'dispatcher']))
                        <div class="card bg-white dark:bg-background-dark rounded-xl border border-sage-light dark:border-sage-deep/30 shadow-sm overflow-hidden mt-4">
                            <div class="card-header align-items-center d-flex">
                                <div class="d-flex justify-content-between w-100">
                                    <h4 class="text-lg font-bold text-slate-900 dark:text-slate-100"
                                        style="font-size:17px;">Dispatches</h4>
                                    <a href="" class="view-all">View All</a>
                                </div>

                            </div><!-- end card header -->

                            <div class="card-body p-2">
                                @forelse ($dispatchRequests as $dr)
                                    <div class="request-card mt-0 mb-2">

                                        <div class="d-flex justify-content-between">
                                            <div class="seed-name">
                                                Request-{{ $dr->id }}
                                            </div>
                                            <div class="">
                                                @if ($dr->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($dr->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($dr->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-6">
                                                <div>{{ $dr->crop->crop_name ?? '-' }}
                                                </div>
                                            </div>

                                            <div class="col-6 text-end">
                                                <div class="qty">Qty. {{ $dr->quantity }}G</div>
                                            </div>
                                            <div class="col-6 text-[12px] text-slate-400">
                                              {{ $dr->request_date ? $dr->request_date->format('d M Y') : '-' }}
                                            </div>
                                            <div class="col-6 text-end text-[12px] text-slate-400">
                                                {{ $dr->requester_name ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3">
                                        No dispatch requests found
                                    </div>
                                @endforelse
                                <H5 class="border-bottom pb-2">Dispatch Order</H5>
                                @forelse($recentDispatches as $dispatch)
                                    <div class="dispatch-card mt-2 border-bottom mb-2 pb-2">
                                        <div class="row">
                                            <div class="col-7">
                                         
                                                <div class="entry">
                                                    {{ $dispatch->created_at?->format('d M Y') }}
                                                </div>
                                                <div class="dispatch-number">
                                                    Dispt No.: {{ $dispatch->dispatch_number ?? 'N/A' }}
                                                </div>
                                            </div>

                                            <div class="col-5 text-end">
                                                <div class="code">
                                                    Acc.: {{ $dispatch->accession?->accession_number ?? 'N/A' }}
                                                </div>
                                                
                                                <div class="qty text-slate-600">
                                                    Qty: {{ number_format($dispatch->quantity, 2) }} {{ $dispatch->capacityUnit?->name ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>  
                                @empty
                                    <div class="text-center text-muted p-3">
                                        No recent dispatches found
                                    </div>
                                @endforelse
                            </div>
                        
                    </div> <!-- .col-->
                    @endif

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">

                            <p class="text-muted border-bottom mb-2 pb-2">
                                Most Requested Crop
                            </p>

                            <h4 class="fw-bold">
                                {{ $topCrop->crop_name ?? 'N/A' }}
                            </h4>

                            <div class="d-flex gap-2 mt-2">

                                <span class="badge bg-primary">
                                    {{ $topCrop->total_requests ?? 0 }} Requests
                                </span>

                                <span class="badge bg-success">
                                    {{ $topCrop->total_quantity ?? 0 }} Qty
                                </span>

                            </div>

                        </div>
                    </div>
                    @if (auth()->user()->hasRole(['super-admin', 'admin', 'manager']))
                    <div class="card shadow-sm border-0 rounded-4 ">
                        <div class="card-body">

                            <p class="text-muted border-bottom mb-2 pb-2">
                                Pending Seed Quality Samples  <span class="float-end mt-2 topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">{{ $pendingQCCount }}</span>
                            </p>

                            <table class="table align-middle">

                                <thead>
                                    <tr>
                                        <th>Lot No</th>
                                        <th>Crop</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($pendingQCSamples as $sample)

                                    <tr>
                                        <td>{{ $sample->lot_number }}</td>
                                        <td>{{ $sample->crop_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sample->created_at)->format('d M Y') }}</td>
                                    </tr>

                                    @endforeach

                                </tbody>

                            </table>

                            <small class="text-muted">
                                Samples awaiting quality control
                            </small>

                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">

                            <p class="text-muted border-bottom mb-2 pb-2">
                                Active Regeneration Cycles <span class="float-end mt-2 topbar-badge fs-10 translate-middle badge rounded-pill bg-success">{{ $activeRegenerationCount }}</span>
                            </p>

                            <table class="table align-middle">

                            <thead>
                                <tr>
                                    <th>Accession</th>
                                    <th>Crop</th>
                                    <th>Regeneration Date</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($activeRegenerationCycles as $item)

                                <tr>
                                    <td>{{ $item->accession_number }}</td>

                                    <td>{{ $item->crop_name }}</td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($item->recheck_date)->format('d M Y') }}
                                    </td>
                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                            <small class="text-muted">
                                Upcoming regeneration schedules
                            </small>

                        </div>
                    </div>
                    @endif
                    </div>

                </div>
            

                <!-- end col -->
            </div>
      
        <script>

document.addEventListener('DOMContentLoaded', function () {

    var cropLabels = @json($cropData->pluck('crop_name'));
    var cropCounts = @json($cropData->pluck('total'));

    var options = {
        series: cropCounts,
        chart: {
            type: 'donut',
            height: 350
        },
        labels: cropLabels
    };

    var chart = new ApexCharts(
        document.querySelector("#cropDonutChart"),
        options
    );

    chart.render();
});
</script>
            
  @endsection