@extends('components/layouts/commonMaster' )

@php
/* Display elements */
$contentNavbar = true;
$containerNav = ($containerNav ?? 'container-xxl');
$isNavbar = ($isNavbar ?? true);
$isMenu = ($isMenu ?? true);
$isFlex = ($isFlex ?? false);
$isFooter = ($isFooter ?? true);

$loggedAuth = null;
$loggedUser = null;
if (auth('admin')->check()) {
    $loggedAuth = 'admin';
    $loggedUser = Auth::guard('admin')->user();
} elseif (auth('organization')->check()) {
    $loggedAuth = 'organization';
    $loggedUser = Auth::guard('organization')->user();
}
// Now you have both:

/* HTML Classes */
$navbarDetached = 'navbar-detached';

/* Content classes */
$container = ($container ?? 'container-xxl');

@endphp

@section('layoutContent')
<div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
  <div class="layout-container">

      @if ($isMenu)
          @if($loggedAuth==="admin")
              @include('components.layouts.sections.menu.verticalMenu')
          @elseif($loggedAuth==="organization")
              @include('components.layouts.sections.menu.organizationSidebar')
          @endif
      @endif

    <!-- Layout page -->
    <div class="layout-page">
      <!-- BEGIN: Navbar-->
      @if ($isNavbar)
        @if($loggedAuth==="admin")
          @include('components/layouts/sections/navbar/navbar')
        @elseif($loggedAuth==="organization")
          @include('components/layouts/sections/navbar/organizationNavbar')
        @endif
      @endif
      <!-- END: Navbar-->

      <!-- Content wrapper -->
      <div class="content-wrapper">
        @props(['loggedAuth' => null, 'loggedUser' => null])
        <!-- Content -->
        @if ($isFlex)
        <div class="{{$container}} d-flex align-items-stretch flex-grow-1 p-0">
          @else
          <div class="{{$container}} flex-grow-1 container-p-y">
            @endif
            {{ $slot }}

          </div>
          <!-- / Content -->

          <!-- Footer -->
          {{-- @if ($isFooter)
          @include('components/layouts/sections/footer/footer')
          @endif --}}
          <!-- / Footer -->
          <div class="content-backdrop fade"></div>
        </div>
        <!--/ Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>

    @if ($isMenu)
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    @endif
    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
  </div>
  <!-- / Layout wrapper -->
  @endsection
