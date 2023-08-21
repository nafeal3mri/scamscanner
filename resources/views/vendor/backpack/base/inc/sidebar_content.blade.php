<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class='nav-icon la la-mobile'></i> Scans</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('link-app-request') }}'><i class='nav-icon la la-mobile'></i> Latest scans</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('string-lookup') }}'><i class='nav-icon la la-search'></i> String lookups</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('report-mistakes') }}'><i class='nav-icon la la-bug'></i> Report inaccurate scans</a></li>        
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class='nav-icon la la-search'></i> Domains</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('domain-list') }}'><i class='nav-icon la la-link'></i> Domain lists</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('domain-categor') }}'><i class='nav-icon la la-stream'></i> Domain categories</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sus-hosts') }}'><i class='nav-icon la la-server'></i> Sus hosts</a></li>
    </ul>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class='nav-icon la la-cog'></i> System</a>
    <ul class="nav-dropdown-items">
        {{-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('notification-center') }}'><i class='nav-icon la la-bell'></i> Notification center</a></li> --}}
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('scan-response-messages') }}'><i class='nav-icon la la-sms'></i> response messages</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sitemeta') }}'><i class='nav-icon la la-globe-asia'></i> Sitemetas</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('newsletters') }}'><i class='nav-icon la la-rss'></i> Newsletters</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('scan-progress-messages') }}'><i class='nav-icon la la-sms'></i> Scan progress messages</a></li>
        
        <hr>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('log') }}'><i class='nav-icon la la-terminal'></i> Logs</a></li>
        <li class='nav-item'><a class='nav-link' href='log-viewer'><i class='nav-icon la la-terminal'></i> Full Logs</a></li>
    </ul>
</li>
