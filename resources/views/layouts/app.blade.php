<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Inventory.corp</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0ea5e9', // Sky 500
                        secondary: '#334155', // Slate 700
                        accent: '#f59e0b', // Amber 500
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="text-slate-800 antialiased flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col transition-all duration-300">
        <div class="h-16 flex items-center px-6 border-b border-slate-800">
            <i class="ph-fill ph-buildings text-primary text-2xl mr-3"></i>
            <span class="text-white font-bold text-lg tracking-wide">MA KARYA</span>
        </div>
        
        <div class="flex-1 overflow-y-auto py-4">
            <nav class="px-3 space-y-1">
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Main Menu</p>
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary text-white hover:bg-primary' : '' }}">
                    <i class="ph ph-squares-four text-lg mr-3"></i> Dashboard
                </a>

                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Master Data</p>
                <a href="{{ route('items.index') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition-colors {{ request()->is('items*') ? 'bg-slate-800 text-white' : '' }}">
                    <i class="ph ph-box-arrow-down text-lg mr-3"></i> Barang & Stok
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition-colors {{ request()->is('categories*') ? 'bg-slate-800 text-white' : '' }}">
                    <i class="ph ph-tag text-lg mr-3"></i> Kategori Barang
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition-colors {{ request()->is('suppliers*') ? 'bg-slate-800 text-white' : '' }}">
                    <i class="ph ph-truck text-lg mr-3"></i> Supplier
                </a>
                <a href="{{ route('customers.index') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition-colors {{ request()->is('customers*') ? 'bg-slate-800 text-white' : '' }}">
                    <i class="ph ph-users text-lg mr-3"></i> Customer / Klien
                </a>

                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Transaksi</p>
                <a href="{{ route('sales-orders.index') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition-colors {{ request()->is('sales-orders*') ? 'bg-slate-800 text-white' : '' }}">
                    <i class="ph ph-shopping-cart text-lg mr-3"></i> Sales Order (Out)
                </a>
                <a href="{{ route('purchase-orders.index') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition-colors {{ request()->is('purchase-orders*') ? 'bg-slate-800 text-white' : '' }}">
                    <i class="ph ph-receipt text-lg mr-3"></i> Purchase Order (In)
                </a>
                
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Audit Trails</p>
                <a href="{{ route('stock-movements.index') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition-colors {{ request()->is('stock-movements*') ? 'bg-slate-800 text-white' : '' }}">
                    <i class="ph ph-clock-counter-clockwise text-lg mr-3"></i> Pergerakan Stok
                </a>
            </nav>
        </div>
        
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-sm font-bold text-white">
                    AD
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-white">Administrator</p>
                    <p class="text-xs text-slate-500">CV Ma Karya</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-10">
            <div class="flex items-center">
                <h1 class="text-xl font-semibold text-slate-800">@yield('header', 'Dashboard')</h1>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-slate-500 hover:text-primary transition-colors">
                    <i class="ph ph-bell text-xl"></i>
                </button>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-6">
            
            @if(session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-md shadow-sm flex items-start">
                <i class="ph-fill ph-check-circle text-emerald-500 text-xl mr-3 mt-0.5"></i>
                <div>
                    <h3 class="text-sm font-medium text-emerald-800">Sukses</h3>
                    <p class="text-sm text-emerald-700 mt-1">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md shadow-sm flex items-start">
                <i class="ph-fill ph-warning-circle text-red-500 text-xl mr-3 mt-0.5"></i>
                <div>
                    <h3 class="text-sm font-medium text-red-800">Gagal</h3>
                    <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            @yield('content')
            
        </div>
    </main>

</body>
</html>
