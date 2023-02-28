<?php
    require_once(MAIN_PATH . "libs/request_util.php");
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php $__file = "main"; if(get_current_url_file() == $__file) echo "active"; ?>" aria-current="page" href="<?php echo $__file; ?>">
                    <span data-feather="home"></span>
                    Dashboard
                </a>
            </li>
        <?php
        /*
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <span data-feather="bar-chart-2"></span>
                    Statistics
                </a>
            </li>
        */
        ?>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>CHARACTERS QUICK ACCESS</span>
            <a class="link-secondary" href="javascript:void(0);" aria-label="Clean tracked characters" onclick="clear_character_quick_access()">
                <span data-feather="x-circle"></span>
            </a>
        </h6>
        <ul class="nav flex-column mb-2" id="quick-access-characters"></ul>
    </div>
</nav>