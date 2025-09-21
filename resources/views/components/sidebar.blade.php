<aside class="sidebar">
    <button type="button" class="sidebar-close-btn !mt-4">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> AI</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> CRM</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> eCommerce</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Cryptocurrency</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Investment</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-purple-600 w-auto"></i> LMS / Learning System</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> NFT & Gaming</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Medical</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-purple-600 w-auto"></i> Analytics</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-menu-group-title">Application</li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="mage:email" class="menu-icon"></iconify-icon>
                    <span>Email</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="bi:chat-dots" class="menu-icon"></iconify-icon>
                    <span>Chat</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="solar:calendar-outline" class="menu-icon"></iconify-icon>
                    <span>Calendar</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="material-symbols:map-outline" class="menu-icon"></iconify-icon>
                    <span>Kanban</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="hugeicons:invoice-03" class="menu-icon"></iconify-icon>
                    <span>Invoice</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> List</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Preview</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Add new</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Edit</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="hugeicons:ai-brain-03" class="menu-icon"></iconify-icon>
                    <span>Ai Application</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Text Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Code Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Image Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Voice Generator</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Video Generator</a>
                    </li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="hugeicons:bitcoin-circle" class="menu-icon"></iconify-icon>
                    <span>Crypto Currency</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Wallet</a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-menu-group-title">UI Elements</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:document-text-outline" class="menu-icon"></iconify-icon>
                    <span>Components</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Typography</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Colors</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Button</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-purple-600  dark:text-purple-400 w-auto"></i> Dropdown</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Alerts</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Card</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Carousel</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Avatars</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Progress bar</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Tab & Accordion</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Pagination</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Badges</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon dark:text-purple-400 w-auto"></i> Tooltip & Popover</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-cyan-600 w-auto"></i> Videos</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-[#7f27ff] w-auto"></i> Star Ratings</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-[#8252e9] w-auto"></i> Tags</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-[#e30a0a] w-auto"></i> List</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-yellow-400 w-auto"></i> Calendar</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-orange-500 w-auto"></i> Radio</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-pink-600 w-auto"></i> Switch</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Upload</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="heroicons:document" class="menu-icon"></iconify-icon>
                    <span>Forms</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Input Forms</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Input Layout</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Form Validation</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Form Wizard</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="mingcute:storage-line" class="menu-icon"></iconify-icon>
                    <span>Table</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Basic Table</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Data Table</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:pie-chart-outline" class="menu-icon"></iconify-icon>
                    <span>Chart</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Line Chart</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Column Chart</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Pie Chart</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="fe:vector" class="menu-icon"></iconify-icon>
                    <span>Widgets</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="flowbite:users-group-outline" class="menu-icon"></iconify-icon>
                    <span>Users</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Users List</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Users Grid</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Add User</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> View Profile</a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-menu-group-title">Application</li>

            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="simple-line-icons:vector" class="menu-icon"></iconify-icon>
                    <span>Authentication</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Sign In</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Sign Up</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Forgot Password</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="solar:gallery-wide-linear" class="menu-icon"></iconify-icon>
                    <span>Gallery</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="hugeicons:money-send-square" class="menu-icon"></iconify-icon>
                    <span>Pricing</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="mage:message-question-mark-round" class="menu-icon"></iconify-icon>
                    <span>FAQs.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="streamline:straight-face" class="menu-icon"></iconify-icon>
                    <span>404</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="octicon:info-24" class="menu-icon"></iconify-icon>
                    <span>Terms & Conditions</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="icon-park-outline:setting-two" class="menu-icon"></iconify-icon>
                    <span>Settings</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Company</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Notification</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Notification Alert</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Theme</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Currencies</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Languages</a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Payment Gateway</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</aside>