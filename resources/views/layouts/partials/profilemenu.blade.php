<li class="nav-item dropdown"><a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
    <div class="avatar avatar-md"><img class="avatar-img" src="{{ asset('img/avatars/8.jpg') }}" alt="user@email.com"><span class="avatar-status bg-success"></span></div>
  </a>
  <div class="dropdown-menu dropdown-menu-end pt-0">
    <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2" data-coreui-i18n="account">Account</div><a class="dropdown-item" href="#">
      <svg class="icon me-2">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-bell"></use>
      </svg><span data-coreui-i18n="updates">Updates</span><span class="badge badge-sm bg-info-gradient ms-2">42</span></a><a class="dropdown-item" href="#">
      <svg class="icon me-2">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-envelope-open"></use>
      </svg><span data-coreui-i18n="messages">Messages</span><span class="badge badge-sm badge-sm bg-success ms-2">42</span></a><a class="dropdown-item" href="#">
      <svg class="icon me-2">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-task"></use>
      </svg><span data-coreui-i18n="tasks">Tasks</span><span class="badge badge-sm bg-danger-gradient ms-2">42</span></a><a class="dropdown-item" href="#">
      <svg class="icon me-2">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-comment-square"></use>
      </svg><span data-coreui-i18n="comments">Comments</span><span class="badge badge-sm bg-warning-gradient ms-2">42</span></a>
    <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold my-2" data-coreui-i18n="settings">Settings</div>
    

    
    <a class="dropdown-item" href="/admin">
      <svg class="icon me-2">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-settings"></use>
      </svg>App Setting</a>

    
      <a class="dropdown-item" href="#">
      <svg class="icon me-2">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-user"></use>
      </svg><span data-coreui-i18n="profile">Profile</span></a>
      
      <a class="dropdown-item" href="#">
      <svg class="icon me-2">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-settings"></use>
      </svg><span data-coreui-i18n="settings">Settings</span></a><a class="dropdown-item" href="#">
      <svg class="icon me-2">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-credit-card"></use>
      </svg><span data-coreui-i18n="payments">Payments</span><span class="badge badge-sm bg-secondary-gradient text-dark ms-2">42</span></a><a class="dropdown-item" href="#">
      <svg class="icon me-2">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-file"></use>
      </svg><span data-coreui-i18n="projects">Projects</span><span class="badge badge-sm bg-primary-gradient ms-2">42</span></a>
    <div class="dropdown-divider"></div><a class="dropdown-item" href="#">
      <svg class="icon me-2">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-lock-locked"></use>
      </svg><span data-coreui-i18n="lockAccount">Lock Account</span></a>
    <form method="POST" action="{{ route('logout') }}" class="d-inline">
      @csrf
      <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
        <svg class="icon me-2">
          <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-account-logout"></use>
        </svg><span data-coreui-i18n="logout">Logout</span>
      </button>
    </form>
  </div>
</li>