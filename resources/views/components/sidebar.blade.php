@props(['division' => 'super-admin'])

<div class="deznav modern-production-sidebar">
    <div class="deznav-scroll modern-sidebar-scroll">
        @if($division == 'super-admin')
            @include('components.sidebar.super-admin')
        @elseif($division == 'director')
            @include('components.sidebar.director')
        @elseif($division == 'admin-finance')
            @include('components.sidebar.admin-finance')
        @elseif($division == 'marketing')
            @include('components.sidebar.marketing')
        @elseif($division == 'production')
            @include('components.sidebar.production')
        @else
            @include('components.sidebar.unified')
        @endif
        <div class="modern-sidebar-footer" style="padding: 20px; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 20px;">
            <p style="font-size: 12px; color: #fff; margin-bottom: 0;"><strong>Claretian ERP</strong><br>© 2026 All Rights Reserved</p>
        </div>
    </div>
</div>
