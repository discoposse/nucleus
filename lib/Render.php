<?hh

class Render {
  public static function go(:xhp $content, ?string $controller): void {
    print
      <x:frag>
        <omega:head />
        <body>
          {self::getNavbar($controller)}
          <div class="container">
            {self::getFlash()}
            {$content}
          </div>
        </body>
      </x:frag>;
  }

  private static function getNavbar(?string $controller): :nav {

    $nav_buttons = null;
    $login = <li><a href="/login">Login</a></li>;

    # User dropdown if there's an active session
    $user = null;
    if(Session::isActive()) {
      $user = Session::getUser();
    }

    if($user && !$user->isDisabled()) {
      $nav_buttons =
        <ul class="nav navbar-nav">
          <li class={$controller === 'DashboardController' ? 'active' : ''}>
            <a href="/dashboard">Dashboard</a>
          </li>
        </ul>;

      # Applicants can see the application portal
      if($user->isApplicant()) {
        $nav_buttons->appendChild(
          <li class={$controller === 'ApplyController' ? 'active' : ''}>
            <a href="/apply">Apply</a>
          </li>
        );
      }

      # Members can see the feedback portal
      if($user->isMember()) {
        $nav_buttons->appendChild(
          <li class={($controller === 'FeedbackListController' || $controller === 'FeedbackSingleController') ? 'active' : ''}>
            <a href="/feedback">Applicant Feedback</a>
          </li>
        );
      }

      # Admins and Reviewers can access the review portal
      if($user->isAdmin() || $user->isReviewer()) {
        $nav_buttons->appendChild(
          <li class={($controller === 'ReviewListController' || $controller === 'ReviewSingleController') ? 'active' : ''}>
            <a href="/review">Review</a>
          </li>
        );
      }

      # Admins and event admins can access the events portal
      if($user->isAdmin() || $user->isOfficer()) {
        $nav_buttons->appendChild(
          <li class={$controller === 'EventsAdminController' ? 'active' : ''}>
            <a href="/events/admin">Events</a>
          </li>
        );
        $nav_buttons->appendChild(
          <li class={$controller === 'NotifyController' ? 'active' : ''}>
            <a href="/notify">Send Notification</a>
          </li>
        );
      }

      # Admin only actions
      if($user->isAdmin()) {
        $nav_buttons->appendChild(
          <li class={$controller === 'MembersController' ? 'active' : ''}>
            <a href="/members">Members</a>
          </li>
        );
      }

      # Logout dropdown
      $login =
        <li>
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{$user->getUsername()} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="/login?action=logout">Logout</a></li>
          </ul>
        </li>;
    }

    return
      <nav class="navbar navbar-default navbar-static-top">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Omega</a>
          </div>
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            {$nav_buttons}
            <ul class="nav navbar-nav navbar-right">
              {$login}
            </ul>
          </div>
        </div>
      </nav>;
  }

  private static function getFlash(): ?:div {
    if(Flash::exists('error')) {
      return
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          {Flash::get('error')}
        </div>;
    } elseif (Flash::exists('success')) {
      return
        <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          {Flash::get('success')}
        </div>;
    }
    return null;
  }
}
