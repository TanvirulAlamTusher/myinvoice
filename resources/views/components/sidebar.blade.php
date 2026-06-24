<aside class="sidebar">

    <nav class="sidebar-menu">
         @can('dashboard.view')
        <a href="{{ url('/dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            📊 <span>Dashboard</span>
        </a>
        @endcan

@can('invoice.view')
        <a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            🧾 <span>Invoices</span>
        </a>
@endcan
@can('customer.view')
        <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
            👥 <span>Customers</span>
        </a>
@endcan
@can('product.view')
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
            📦 <span>Products</span>
        </a>
@endcan


        <!-- Product Setup Multi-level MENU -->
        <div class="menu-group {{ request()->routeIs('category.*', 'brands.*', 'product-units.*') ? 'open' : '' }}">
            <button type="button" class="sidebar-menu-toggle" aria-expanded="{{ request()->routeIs('category.*', 'brands.*', 'product-units.*') ? 'true' : 'false' }}">
                <span class="menu-toggle-content">
                    📦 <span>Product Setup</span>
                </span>
                <span class="menu-arrow">▼</span>
            </button>

            <div class="submenu">
                @can('category.view')
                <a href="{{ route('category.index') }}" class="{{ request()->routeIs('category.*') ? 'active' : '' }}">
                    🏷️ <span>Category</span>
                </a>
                @endcan
                @can('brand.view')
                <a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.*') ? 'active' : '' }}">
                    🔖 <span>Brand</span>
                </a>
                @endcan
                @can('unit.view')
                <a href="{{ route('product-units.index') }}" class="{{ request()->routeIs('product-units.*') ? 'active' : '' }}">
                    📏 <span>Units</span>
                </a>
               @endcan
            </div>
        </div>

        <hr>
   @can('user.view')
        <a href="{{ route('users.index') }}">
            👨‍💼 <span>User Management</span>
        </a>
        @endcan
         @can('role.permission.view')
     <a href="{{ route('roles.index') }}">
    👨‍💼 <span>Role & Permission</span>
</a>
@endcan


@can('settings.view')
        <!-- Settings Multi-level MENU -->
        <div class="menu-group {{ request()->routeIs('business-settings.*', 'invoice-settings.*') ? 'open' : '' }}">
            <button type="button" class="sidebar-menu-toggle" aria-expanded="{{ request()->routeIs('business-settings.*', 'invoice-settings.*') ? 'true' : 'false' }}">
                <span class="menu-toggle-content">
                    ⚙️ <span>Settings</span>
                </span>
                <span class="menu-arrow">▼</span>
            </button>

            <div class="submenu">
                @can('business.settings.view')
                <a href="{{ route('business-settings.index') }}" class="{{ request()->routeIs('business-settings.*') ? 'active' : '' }}">
                    🏢 <span>Business Settings</span>
                </a>
                @endcan
             
                @can('trash.view')
                 <a href="{{ route('invoices.trash') }}">
            🗑️ <span>Recycle Bin</span>
         </a>
         @endcan
            </div>
        </div>
        @endcan


    </nav>

    <!-- Logout -->
    <div class="logout-btn">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

</aside>
