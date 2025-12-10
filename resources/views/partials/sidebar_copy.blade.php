<nav class="dash-sidebar light-sidebar {{ empty($company_settings['site_transparent']) || $company_settings['site_transparent'] == 'on' ? 'transprent-bg' : '' }}" id="main-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header main-logo">
            <a href="{{ route('home') }}" class="b-brand">
                <img src="{{asset('images/1920 x 557.png')}}" alt="" class="logo logo-lg" />
            </a>
            <button type="button" class="sidebar-toggle-btn" id="sidebar-toggle-btn" title="Toggle Sidebar">
                <i class="fas fa-angle-left" id="toggle-icon"></i>
            </button>
        </div>

        <div class="search-container">
            <div class="form-group">
                <input type="text" id="menu-search" class="form-control" placeholder="Search menu..." autocomplete="off">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>

        @if(!empty($company_settings['category_wise_sidemenu']) && $company_settings['category_wise_sidemenu'] == 'on')
            <div class="tab-container">
                <div class="tab-sidemenu">
                    <ul class="dash-tab-link nav flex-column" role="tablist" id="dash-layout-submenus"></ul>
                </div>
                <div class="tab-link">
                    <div class="navbar-content">
                        <div class="tab-content" id="dash-layout-tab"></div>
                        <ul class="dash-navbar">
                            {!! getMenu() !!}
                            @stack('custom_side_menu')
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="navbar-content">
                <ul class="dash-navbar">
                    {!! getMenu() !!}
                    @stack('custom_side_menu')
                </ul>
            </div>
        @endif  
    </div>
</nav>


<style>
/* Search Container Styles */
.search-container {
    padding: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s ease;
}

.search-container .form-group {
    position: relative;
    margin-bottom: 0;
}

.search-container input {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    color: #333;
    padding-left: 35px;
    border-radius: 5px;
}

.search-container input::placeholder {
    color: #999;
}

.search-container input:focus {
    background: #ffffff;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    outline: none;
}

.search-container .search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    pointer-events: none;
}

/* Sidebar Toggle Button */
.sidebar-toggle-btn {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    color: #495057;
    width: 30px;
    height: 30px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 14px;
    z-index: 1001;
}

.sidebar-toggle-btn:hover {
    background: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
}

.sidebar-toggle-btn:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,123,255,.25);
}

.m-header.main-logo {
    position: relative;
    padding-right: 50px;
}


/* ---- Layout / z-index fixes ---- */
#main-sidebar {
  /* smooth width animation */
  transition: width .25s ease, left .25s ease;
  z-index: 1000 !important;
  position: relative;
}

/* Make sure the sidebar sits above main content while hovering */
body.sidebar-hover-open #main-sidebar,
.sidebar-hover-open #main-sidebar {
  z-index: 1200 !important;
}

/* ---- Main content margin sync (works for many admin templates) ---- */
body.sidebar-collapsed .pc-container,
body.sidebar-collapsed .pcoded-main-container,
body.sidebar-collapsed .pcoded-wrapper,
body.sidebar-collapsed .main-content {
  margin-left: 70px !important;
  transition: margin .25s ease;
}

body:not(.sidebar-collapsed) .pc-container,
body:not(.sidebar-collapsed) .pcoded-main-container,
body:not(.sidebar-collapsed) .pcoded-wrapper,
body:not(.sidebar-collapsed) .main-content {
  margin-left: 260px !important;
  transition: margin .25s ease;
}

/* In case some layouts use padding instead of margin */
body.sidebar-collapsed .content-wrapper {
  padding-left: 70px !important;
}

/* ---- Menu/icon alignment and spacing ---- */
/* Make links flex so icon + text never overlap */
.dash-link {
  display: flex;
  align-items: center;
  gap: 12px;              /* space between icon and text */
  white-space: nowrap;
}

/* Give icon fixed slot so text never overlays it (adjust width if your icons bigger) */
.dash-link .dash-icon,
.dash-link i.fas, 
.dash-link i.fa, 
.dash-link .icon {
  min-width: 36px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

/* ensure text container can be hidden/shown cleanly */
.dash-mtext,
.dash-caption {
  display: inline-block;
  vertical-align: middle;
  transition: opacity .18s ease, transform .18s ease;
}

/* collapsed state: icons centered, text hidden */
.sidebar-collapsed .dash-link {
  justify-content: center;
  padding: 12px 8px !important;
}

.sidebar-collapsed .dash-mtext,
.sidebar-collapsed .dash-caption {
  opacity: 0;
  transform: translateX(-6px);
  visibility: hidden;
  width: 0;
}

/* hover-open revert */
.sidebar-collapsed.sidebar-hover-open .dash-link {
  justify-content: flex-start;
  padding: 12px 18px !important;
}

.sidebar-collapsed.sidebar-hover-open .dash-mtext,
.sidebar-collapsed.sidebar-hover-open .dash-caption {
  opacity: 1 !important;
  transform: translateX(0) !important;
  visibility: visible !important;
  width: auto !important;
}

/* ---- Submenu styling ---- */
.dash-submenu {
  margin: 6px 0 12px 0;
  padding: 0;
  overflow: hidden;
  max-height: 0;
  transition: max-height .28s cubic-bezier(.2,.9,.2,1), opacity .2s;
  opacity: 0;
  margin-left: -20px;
}

/* submenu visible when parent has .open */
.dash-item.open > .dash-submenu {
  display: block; /* ensure it's part of flow */
  opacity: 1;
  max-height: 600px; /* large enough for many items; adjust if needed */
  padding-left: 6px;
}

/* submenu item spacing and indent so icon and text don't overlap */
.dash-submenu li,
.dash-submenu .dash-link {
  padding-left: 44px !important; /* give room for icon column */
  text-align: left !important;
}

/* prettier bullet / dot icons if used (optional) */
.dash-submenu li::marker {
  color: transparent;
}

/* subtle card / background for active parent (optional) */
.dash-item.open > .dash-link {
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  border-radius: 8px;
}

/* search input visible on hover */
.search-container {
  transition: opacity .18s ease, height .18s ease, visibility .18s;
}

/* avoid overlapping with main content due to scrollbars */
#main-sidebar {
  overflow-y: auto;
}

/* small responsive: ensure sidebar does not cover content on tiny screens */
@media (max-width: 768px) {
  body.sidebar-collapsed .pc-container,
  body.sidebar-collapsed .main-content {
    margin-left: 0 !important;
  }
}


/* ===== Sidebar as fixed column (prevent content push/jump) ===== */
/* ===========================
   BASE SIDEBAR LAYOUT
=========================== */
#main-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 260px;
    transition: width 0.28s ease;
    z-index: 1000 !important;
    overflow-y: auto;
    background: inherit;
}

/* COLLAPSED STATE (icon-only) */
body.sidebar-collapsed #main-sidebar {
    width: 70px;
}

/* HOVER-EXPAND FULL SIDEBAR */
body.sidebar-collapsed.sidebar-hover-open #main-sidebar {
    width: 260px;
}

/* Logo */
.m-header.main-logo {
    position: relative;
    padding-right: 50px;
}

/* Hide long logo on collapse */
body.sidebar-collapsed .logo-lg {
    display: none;
}

/* Search */
.search-container {
    padding: 15px;
    transition: opacity .2s;
}
body.sidebar-collapsed .search-container {
    opacity: 0;
    visibility: hidden;
    height: 0;
}
body.sidebar-collapsed.sidebar-hover-open .search-container {
    opacity: 1;
    visibility: visible;
    height: auto;
}

/* Menu link layout */
.dash-link {
    display: flex;
    align-items: center;
    gap: 12px;
    white-space: nowrap;
    padding: 12px 18px;
    transition: all .2s;
}

/* Icon alignment */
.dash-link i,
.dash-icon {
    min-width: 36px;
    display: flex;
    justify-content: center;
    font-size: 18px;
}

/* Hide text on collapse */
body.sidebar-collapsed .dash-mtext,
body.sidebar-collapsed .dash-caption {
    opacity: 0;
    width: 0;
    transform: translateX(-8px);
    visibility: hidden;
}

body.sidebar-collapsed.sidebar-hover-open .dash-mtext,
body.sidebar-collapsed.sidebar-hover-open .dash-caption {
    opacity: 1;
    width: auto;
    transform: translateX(0);
    visibility: visible;
}

/* Center icons when collapsed */
body.sidebar-collapsed .dash-link {
    justify-content: center;
}

/* Restore icon + text when hover */
body.sidebar-collapsed.sidebar-hover-open .dash-link {
    justify-content: flex-start;
}

/* ===========================
   SUBMENU (normal accordion)
=========================== */
.dash-submenu {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height .25s ease, opacity .2s;
}

.dash-item.open > .dash-submenu {
    max-height: 600px;
    opacity: 1;
}

/* indent submenu */
.dash-submenu .dash-link {
    padding-left: 50px !important;
}

/* Prevent submenu from floating — important for Option A */
body.sidebar-collapsed .dash-submenu {
    position: relative !important;
    left: 0 !important;
    background: inherit !important;
    box-shadow: none !important;
    display: block !important;
}

/* BUT hide submenu when collapsed UNLESS hover-expanded */
body.sidebar-collapsed .dash-submenu {
    display: none !important;
}
body.sidebar-collapsed.sidebar-hover-open .dash-item.open > .dash-submenu {
    display: block !important;
}

/* ===========================
   MAIN CONTENT SHIFT
=========================== */
.pc-container,
.pcoded-main-container,
.pcoded-wrapper,
.main-content,
.content-wrapper,
.dash-container {
    transition: margin-left .28s;
    margin-left: 260px;
}

body.sidebar-collapsed .pc-container,
body.sidebar-collapsed .pcoded-main-container,
body.sidebar-collapsed .pcoded-wrapper,
body.sidebar-collapsed .main-content,
body.sidebar-collapsed .content-wrapper,
body.sidebar-collapsed .dash-container {
    margin-left: 70px;
}

body.sidebar-collapsed.sidebar-hover-open .pc-container,
body.sidebar-collapsed.sidebar-hover-open .dash-container {
    margin-left: 260px;
}


/* hide submenu arrow in collapsed sidebar */
/* 1. Hide arrow only when sidebar is collapsed */
body.sidebar-collapsed .dash-arrow {
    opacity: 0;
    visibility: hidden;
    width: 0;
    margin: 0;
    padding: 0;
}

/* 2. When hovered open → show the arrow */
body.sidebar-collapsed.sidebar-hover-open .dash-arrow {
    opacity: 1;
    visibility: visible;
    width: auto;
}

/* 3. When sidebar is fully expanded → arrow must always show */
body:not(.sidebar-collapsed) .dash-arrow {
    opacity: 1;
    visibility: visible;
    width: auto;
}

/* center icon + remove leftover spacing */
body.sidebar-collapsed .dash-link {
    padding-left: 0 !important;
    padding-right: 0 !important;
    justify-content: center !important;
}

/* fix icon wrapper */
body.sidebar-collapsed .dash-micon {
    margin: 0 auto;
    padding: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* When hover expanded → restore normal padding */
body.sidebar-collapsed.sidebar-hover-open .dash-link {
    padding-left: 18px !important;
    padding-right: 18px !important;
    justify-content: flex-start !important;
}

/* Remove feather auto icons inside nested submenu items */
.dash-submenu .dash-item .dash-link i {
    display: none !important;
}

/* Level-1 submenu indent */
.dash-submenu > .dash-item > .dash-link {
    padding-left: 40px !important;
}

/* Level-2 submenu indent */
.dash-submenu .dash-submenu > .dash-item > .dash-link {
    padding-left: 60px !important;
}

/* Level-3 submenu indent */
.dash-submenu .dash-submenu .dash-submenu > .dash-item > .dash-link {
    padding-left: 80px !important;
}


</style>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const body = document.body;
    const sidebar = document.getElementById("main-sidebar");
    const toggleBtn = document.getElementById("sidebar-toggle-btn");
    const toggleIcon = document.getElementById("toggle-icon");
    const searchInput = document.getElementById("menu-search");

    let isSearching = false;

    /* Always start collapsed by default */
    body.classList.add("sidebar-collapsed");
    if (toggleIcon) toggleIcon.style.transform = "rotate(180deg)";
    localStorage.setItem("sidebarCollapsed", "true");

    /* Sidebar Toggle */
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const collapsed = body.classList.contains("sidebar-collapsed");
            body.classList.toggle("sidebar-collapsed");
            body.classList.remove("sidebar-hover-open");

            localStorage.setItem("sidebarCollapsed", !collapsed);

            if (toggleIcon) {
                toggleIcon.style.transition = "transform .25s ease";
                toggleIcon.style.transform = collapsed ? "rotate(0deg)" : "rotate(180deg)";
            }

            if (!collapsed) {
                document.querySelectorAll(".dash-item.open").forEach(item => {
                    item.classList.remove("open");
                    let sm = item.querySelector(":scope > .dash-submenu");
                    if (sm) sm.style.maxHeight = 0;
                });
            }

            setTimeout(() => window.dispatchEvent(new Event("resize")), 300);
        });
    }

    /* Hover Expand */
    if (sidebar) {
        sidebar.addEventListener("mouseenter", () => {
            if (body.classList.contains("sidebar-collapsed")) {
                body.classList.add("sidebar-hover-open");
            }
        });

        sidebar.addEventListener("mouseleave", () => {
            body.classList.remove("sidebar-hover-open");
        });
    }

    /* ==============================
       MULTI LEVEL SUBMENU HANDLING
       ============================== */
    document.querySelectorAll(".dash-item").forEach(item => {

        const link = item.querySelector(":scope > .dash-link");
        const submenu = item.querySelector(":scope > .dash-submenu");

        if (!link || !submenu) return;

        link.addEventListener("click", function (ev) {

            /* During search → disable toggle */
            if (isSearching) {
                ev.preventDefault();
                ev.stopPropagation();
                return;
            }

            /* If fully collapsed and NOT hover-expanded → block */
            if (body.classList.contains("sidebar-collapsed") &&
                !body.classList.contains("sidebar-hover-open")) {
                return;
            }

            // Prevent default and stop propagation to avoid conflicts
            ev.preventDefault();
            ev.stopPropagation();
            ev.stopImmediatePropagation();

            const isOpen = item.classList.contains("open");

            /* Close ONLY siblings INSIDE same parent */
            const parentUl = item.parentElement;
            parentUl.querySelectorAll(":scope > .dash-item.open").forEach(sib => {
                if (sib !== item) {
                    sib.classList.remove("open");
                    let sm = sib.querySelector(":scope > .dash-submenu");
                    if (sm) sm.style.maxHeight = 0;
                }
            });

            /* Toggle current */
            item.classList.toggle("open", !isOpen);

            if (!isOpen) {
                submenu.style.maxHeight = submenu.scrollHeight + "px";
            } else {
                submenu.style.maxHeight = 0;
            }
        }, true); // Use capture phase to run before other handlers
    });

    /* ==============================
       SEARCH SYSTEM
       ============================== */

    function normalize(s) {
        return (s || "").toLowerCase().trim();
    }

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const q = normalize(this.value);
            isSearching = q.length > 0;

            const items = document.querySelectorAll(".dash-item");

            items.forEach(item => {

                const textEl = item.querySelector(":scope > .dash-link .dash-mtext") ||
                               item.querySelector(":scope > .dash-link");
                const linkText = normalize(textEl ? textEl.textContent : "");

                const submenu = item.querySelector(":scope > .dash-submenu");

                let matchParent = linkText.includes(q);
                let matchChild = false;

                if (submenu) {
                    submenu.querySelectorAll("li").forEach(li => {
                        if (normalize(li.textContent).includes(q)) {
                            matchChild = true;
                        }
                    });
                }

                if (q === "") {
                    item.style.display = "";
                    item.classList.remove("open");
                    if (submenu) submenu.style.maxHeight = null;
                    return;
                }

                if (matchParent || matchChild) {
                    item.style.display = "";

                    if (matchChild && submenu) {
                        item.classList.add("open");
                        submenu.style.maxHeight = submenu.scrollHeight + "px";
                    }

                } else {
                    item.style.display = "none";
                    item.classList.remove("open");
                    if (submenu) submenu.style.maxHeight = 0;
                }
            });
        });
    }

    /* Restore open states */
    setTimeout(() => {
        document.querySelectorAll(".dash-item.open > .dash-submenu")
            .forEach(sm => sm.style.maxHeight = sm.scrollHeight + "px");
    }, 150);

});
</script>



