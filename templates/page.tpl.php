<?php
  /**
   * @file
   * Returns the HTML for a single Drupal page.
   *
   * Complete documentation for this file is available online.
   * @see http://drupal.org/node/1728148
   */
?>

<header id="header">
  <div class="navbar">
    <div class="navbar-inner">
      <div class="container">

        <?php if ($logo || $site_name || $site_slogan): ?>
          <div id="logo-wrapper">
            <?php if ($logo): ?>
              <a class="brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
                <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>"/>
              </a>
            <?php endif; ?>
          
            <?php if ($site_name || $site_slogan): ?>
              <div id="name-and-slogan">
                <?php if ($site_name): ?>
                  <?php if ($title): ?>
                    <div id="site-name"><strong>
                      <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home">
                        <span><?php print $site_name; ?></span>
                      </a>
                    </strong></div>
                  <?php else: /* Use h1 when the content title is empty */ ?>
                    <h1 id="site-name">
                      <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home">
                        <span><?php print $site_name; ?></span>
                      </a>
                    </h1>
                  <?php endif; ?>
                <?php endif; ?>
            
                <?php if ($site_slogan): ?>
                  <div id="site-slogan"><?php print $site_slogan; ?></div>
                <?php endif; ?>
              </div> <!-- /#name-and-slogan -->
            <?php endif; ?>
          </div>
        
        <?php endif; ?>

        <div id="menu-icon">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
        </div>

        <div id="menu-wrapper">
          <?php if (!empty($primary_nav) || !empty($secondary_nav) || !empty($page['navigation'])): ?>
            <nav role="navigation" class="nav-collapse collapse">
              <?php if (!empty($primary_nav)): ?>
                <?php print render($primary_nav); ?>
              <?php endif; ?>
              <?php if (!empty($page['navigation'])): ?>
                <?php print render($page['navigation']); ?>
              <?php endif; ?>
            </nav>
          <?php endif; ?>
        </div>

        <?php print render($page['header']); ?>
        
      </div><!-- /container -->
    </div><!-- /navbar-inner -->
  </div><!-- /navbar -->

  <?php if ($breadcrumb): ?>
    <div id="breadcrumb-wrapper">
        <?php print $breadcrumb; ?>
    </div>
  <?php endif; ?>

</header><!-- /header -->

<section role="main">
  
  <div class="container">

    <div class="content-inner">
    <?php print render($page['highlighted']); ?>
    <a id="main-content"></a>
    <?php print render($title_prefix); ?>
    <?php if ($title): ?>
    <h1 class="page--title title" id="page-title"><?php print $title; ?></h1>
    <?php endif; ?>
    <?php print render($title_suffix); ?>
    <?php print $messages; ?>
    <?php print render($tabs); ?>
    <?php print render($page['help']); ?>
    <?php if ($action_links): ?>
    <ul class="action-links"><?php print render($action_links); ?></ul>
    <?php endif; ?>
    <?php print render($page['content']); ?>
    
    </div><!-- /content-inner -->
    
    <?php
      // Render the sidebars to see if there's anything in them.
      $sidebar_first  = render($page['sidebar_first']);
    ?>

    <?php if ($sidebar_first): ?>
      <aside role="menu">
        <?php print $sidebar_first; ?>
        <?php print $feed_icons; ?>
      </aside>
    <?php endif; ?>

  </div><!-- /container -->
</section><!-- /main -->

<!-- Footer -->
<footer id="footer">

  <div id="footerTop">
    <div class="container">
      <?php if ($page['footer_firstcolumn'] || $page['footer_secondcolumn'] || $page['footer_thirdcolumn']): ?>
        <div class="footer1">
          <?php print render($page['footer_firstcolumn']); ?>
        </div>
        <div class="footer2">
          <?php print render($page['footer_secondcolumn']); ?>
        </div>
        <div class="footer3">
          <?php print render($page['footer_thirdcolumn']); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div id="footerBottom">
    <div class="container">
      <div class="bottom1">
        <?php print render($page['bottom_firstcolumn']); ?>
      </div>
      <div class="bottom2">
        <?php print render($page['bottom_secondcolumn']); ?>
      </div>
    </div>
  </div>

</footer><!-- /footer -->
