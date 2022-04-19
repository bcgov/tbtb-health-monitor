@if(isset($page) && $page == 'home')
<div class="col-md-4">
    Legend: <span class="badge bg-warning rounded-pill mr-4">Loading</span>&nbsp;<span class="badge bg-success rounded-pill mr-4">Running</span>&nbsp;<span class="badge bg-danger rounded-pill mr-4">Down</span>&nbsp;<span class="badge bg-primary rounded-pill mr-4">Paused</span>
</div>
@else
    <div class="col-md-4"></div>
@endif
<div class="col-md-8">
    <ul class="nav justify-content-end nav-pills">
        @if(Auth::check())
            <li class="nav-item">
                <a class="nav-link {{$page == 'home' ? 'active' : ''}}" aria-current="page" href="/">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{$page == 'services' ? 'active' : ''}}" href="/services">Services Control</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{$page == 'service_contacts' ? 'active' : ''}}" href="/service-contacts">Services Contacts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{$page == 'contacts' ? 'active' : ''}}" href="/accounts">Contacts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{$page == 'shots' ? 'active' : ''}}" href="/shots">Screenshots</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{$page == 'logs' ? 'active' : ''}}" href="/logs">Logs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout">Logout</a>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link" href="/login">Login</a>
            </li>
        @endif
    </ul>
</div>
