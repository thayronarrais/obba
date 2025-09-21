<aside class="sidebar">
    <button type="button" class="sidebar-close-btn !mt-4">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('site/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('site/logo.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('site/logo.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Painel de Controlo</span>
                </a>
            </li>
            <li class="sidebar-menu-group-title">Application</li>
            <li>
                <a href="{{ route('invoice.scan') }}">
                    
                    <iconify-icon icon="bi:qr-code" class="menu-icon"></iconify-icon>
                    <span>Ler código QR</span>
                </a>
            </li>
            <li>
                <a href="{{ route('invoice.create') }}">
                    <iconify-icon icon="bi:file-earmark-plus" class="menu-icon"></iconify-icon>
                    <span>Inserção manual</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="bi:cloud-upload" class="menu-icon"></iconify-icon>
                    <span>Carregar documentos</span>
                </a>
            </li>
            <li>
                <a href="{{ route('invoice.index') }}">
                    <iconify-icon icon="bi:list-ul" class="menu-icon"></iconify-icon>
                    <span>Listagem de documentos</span>
                </a>
            </li>
            
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="bi:receipt" class="menu-icon"></iconify-icon>
                    <span>Outras despesas</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Salários</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Quilometros</a>
                    </li>
                    
                </ul>
            </li>
            
        </ul>
    </div>
</aside>