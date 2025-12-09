<div class="navigation">
    <div class="navigation-header">
        <span>Navigation</span>
        <a href="#"><i class="ti-close"></i></a>
    </div>

    <div class="navigation-menu-body">
        <div class="text-light mx-5">
            <h3>Sistem Pendukung Keputusan</h3>
            <p>Smartphone Terbaik Metode AHP & SAW</p>
        </div>

        <ul>
            <li>
                <a class="{{ request()->routeIs('index') ? 'active' : '' }}" 
                   href="{{ route('index') }}">
                    <span class="nav-link-icon">
                        <i data-feather="pie-chart"></i> </span>
                    <span>Dashboard</span>
                </a>
            </li>


            <li>
                <a class="{{ request()->routeIs('criterias.*') ? 'active' : '' }}" 
                   href="{{ route('criterias.index') }}">
                    <span class="nav-link-icon">
                        <i data-feather="list"></i> </span>
                    <span>Kriteria</span>
                </a>
            </li>

            <li>
                <a class="{{ request()->routeIs('alternatives.*') ? 'active' : '' }}" 
                   href="{{ route('alternatives.index') }}">
                    <span class="nav-link-icon">
                       <i data-feather="smartphone"></i>
                    </span>
                    <span>Smartphone</span>
                </a>
            </li>

            <li>
                <a class="{{ request()->routeIs('ahp.*') ? 'active' : '' }}" 
                   href="{{ route('ahp.index') }}">
                    <span class="nav-link-icon">
                        <i data-feather="git-pull-request"></i> </span>
                    <span>Perhitungan AHP</span>
                </a>
            </li>

            <li>
                <a class="{{ request()->routeIs('saw.*') ? 'active' : '' }}" 
                   href="{{ route('saw.index') }}">
                    <span class="nav-link-icon">
                        <i data-feather="bar-chart-2"></i> </span>
                    <span>Perhitungan SAW</span>
                </a>
            </li>
        </ul>
    </div>
</div>