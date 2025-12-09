@include('layouts.head')


<!-- Layout wrapper -->
<div class="layout-wrapper">
    <!-- Content wrapper -->
    <div class="content-wrapper">
        @include('layouts.navbar')

        <!-- Content body -->
        <div class="content-body">
            <!-- Content -->
            <div class="content">
                    @yield('content')
            </div>
            <!-- ./ Content -->

        </div>
        <!-- ./ Content body -->
    </div>
    <!-- ./ Content wrapper -->
</div>
<!-- ./ Layout wrapper -->

@include('layouts.tail')
