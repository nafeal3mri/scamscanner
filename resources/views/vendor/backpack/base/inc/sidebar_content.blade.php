<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ __('base.Dashboard') }}</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class='nav-icon la la-mobile'></i> {{__("base.Scans")}}</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('link-app-request') }}'><i class='nav-icon la la-mobile'></i> {{__("base.Latest scans")}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('string-lookup') }}'><i class='nav-icon la la-search'></i> {{__("base.String lookups")}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('report-mistakes') }}'><i class='nav-icon la la-bug'></i> {{__("base.Reported scans")}}</a></li>        
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class='nav-icon la la-search'></i> {{__("base.Domains")}}</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('domain-list') }}'><i class='nav-icon la la-link'></i> {{__("base.Domain lists")}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('domain-categor') }}'><i class='nav-icon la la-stream'></i> {{__("base.Domain categories")}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sus-hosts') }}'><i class='nav-icon la la-server'></i> {{__("base.Sus hosts")}}</a></li>
    </ul>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class='nav-icon la la-cog'></i> {{__("base.System")}}</a>
    <ul class="nav-dropdown-items">
        {{-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('notification-center') }}'><i class='nav-icon la la-bell'></i> Notification center</a></li> --}}
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('scan-response-messages') }}'><i class='nav-icon la la-sms'></i> {{__("base.response messages")}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sitemeta') }}'><i class='nav-icon la la-globe-asia'></i> {{__("base.Sitemetas")}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('newsletters') }}'><i class='nav-icon la la-rss'></i> {{__("base.Newsletters")}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('scan-progress-messages') }}'><i class='nav-icon la la-sms'></i> {{__("base.Scan progress messages")}}</a></li>
        
        <hr>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('log') }}'><i class='nav-icon la la-terminal'></i> {{__("base.Logs")}}</a></li>
        <li class='nav-item'><a class='nav-link' href='log-viewer'><i class='nav-icon la la-terminal'></i> {{__("base.Full Logs")}}</a></li>
    </ul>
</li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-question"></i> Users</a></li>