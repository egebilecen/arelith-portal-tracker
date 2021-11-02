<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Arelith Portal Tracker</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse"
        data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <input name="search" class="form-control form-control-dark w-100" type="text" placeholder="Search player or character..." aria-label="Search player or character...">
    <div class="navbar-nav">
        <div class="nav-item text-nowrap">
            <!-- <a class="nav-link px-3" href="#">Sign out</a> -->
            <select name="search_type" class="form-select" style="width:125px;height:48px;border-radius:0;">
                <option value="character" selected="">Character</option>
                <option value="player">Player</option>
            </select>
        </div>
    </div>
    <div class="search-results">
        <h6>Search Results:</h6>
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover" id="search-results-table">
                <tbody></tbody>
            </table>
        </div>
    </div>
</header>
