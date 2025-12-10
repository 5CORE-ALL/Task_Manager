<nav class="dash-sidebar light-sidebar {{ empty($company_settings['site_transparent']) || $company_settings['site_transparent'] == 'on' ? 'transprent-bg' : '' }}" id="main-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header main-logo">
            <a href="{{ route('home') }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="{{asset('images/1920 x 557.png')}}" alt="" class="logo logo-lg" />
            </a>
            <!-- Sidebar collapse toggle button -->
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
              <ul class="dash-tab-link nav flex-column" role="tablist" id="dash-layout-submenus">
              </ul>
            </div>
            <div class="tab-link">
              <div class="navbar-content">
                <div class="tab-content" id="dash-layout-tab">
                </div>
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

/* Logo container adjustments */
.m-header.main-logo {
    position: relative;
    padding-right: 50px;
}

/* Sidebar Collapsed State */
.sidebar-collapsed #main-sidebar {
    width: 70px !important;
}

.sidebar-collapsed .search-container {
    display: none;
}

.sidebar-collapsed .dash-mtext,
.sidebar-collapsed .dash-caption,
.sidebar-collapsed .dash-badge,
.sidebar-collapsed .dash-arrow {
    display: none !important;
}

.sidebar-collapsed .dash-link {
    padding: 20px 25px;
    text-align: center;
}

.sidebar-collapsed .logo-lg {
    display: none;
}

.sidebar-collapsed .sidebar-toggle-btn {
    right: 20px;
}

.sidebar-collapsed #toggle-icon {
    transform: rotate(180deg);
}

/* Main content adjustment */
.sidebar-collapsed .dash-container {
    margin-left: 70px !important;
}

/* Submenu positioning when collapsed */
.sidebar-collapsed .dash-submenu {
    position: absolute !important;
    left: 70px !important;
    top: 0 !important;
    background: #1c232f !important;
    min-width: 200px !important;
    z-index: 1000 !important;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1) !important;
    display: none !important;
}

.sidebar-collapsed .dash-item:hover .dash-submenu {
    display: block !important;
}

/* Smooth transitions */
#main-sidebar {
    transition: width 0.3s ease;
}

/* Menu search functionality */
.dash-navbar li {
    transition: all 0.3s ease;
}

.dash-navbar li.hidden {
    display: none !important;
}

.dash-navbar li.search-expanded {
    background-color: rgba(0, 123, 255, 0.1);
    border-radius: 4px;
}

.dash-navbar li.search-expanded .collapse,
.dash-navbar li.search-expanded ul {
    display: block !important;
}

.dash-navbar li .search-match {
    background-color: rgba(255, 193, 7, 0.2);
    border-radius: 3px;
}

.no-results {
    padding: 15px;
    text-align: center;
    color: rgba(255,255,255,0.6);
    font-size: 14px;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .sidebar-toggle-btn {
        display: none;
    }
    
    .sidebar-collapsed .dash-container {
        margin-left: 0 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('menu-search');
    let noResultsElement = null;

    function createNoResultsElement() {
        const noResults = document.createElement('div');
        noResults.className = 'no-results';
        noResults.textContent = 'No menu items found';
        noResults.style.display = 'none';
        return noResults;
    }

    function filterMenu() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;
        
        // Get all menu containers (both regular and tabbed)
        const menuContainers = document.querySelectorAll('.dash-navbar');
        
        menuContainers.forEach(function(menuContainer) {
            const menuItems = menuContainer.querySelectorAll('li');
            
            menuItems.forEach(function(item) {
                let shouldShow = false;
                let hasMatchingChild = false;
                
                if (searchTerm === '') {
                    shouldShow = true;
                } else {
                    // Get only the direct text of parent menu (excluding child text)
                    let parentText = '';
                    for (let i = 0; i < item.childNodes.length; i++) {
                        const node = item.childNodes[i];
                        if (node.nodeType === Node.TEXT_NODE) {
                            parentText += node.textContent;
                        } else if (node.tagName && node.tagName !== 'UL' && !node.classList.contains('collapse') && !node.classList.contains('submenu')) {
                            parentText += node.textContent;
                        }
                    }
                    parentText = parentText.toLowerCase().trim();
                    
                    // Check if parent menu matches
                    if (parentText.includes(searchTerm)) {
                        shouldShow = true;
                    }
                    
                    // Check child/sub-menu items
                    const subMenus = item.querySelectorAll('ul li, .collapse li, .submenu li');
                    subMenus.forEach(function(subItem) {
                        const subText = subItem.textContent.toLowerCase();
                        if (subText.includes(searchTerm)) {
                            shouldShow = true;
                            hasMatchingChild = true;
                            
                            // Immediately expand parent menu
                            const parentCollapse = item.querySelector('.collapse');
                            if (parentCollapse) {
                                parentCollapse.classList.add('show');
                                parentCollapse.style.display = 'block';
                            }
                            
                            // Find and expand all nested collapse elements
                            const nestedCollapses = item.querySelectorAll('.collapse');
                            nestedCollapses.forEach(function(collapse) {
                                collapse.classList.add('show');
                                collapse.style.display = 'block';
                            });
                            
                            // Also try Bootstrap collapse classes
                            const collapseTargets = item.querySelectorAll('[data-bs-toggle="collapse"], [data-toggle="collapse"]');
                            collapseTargets.forEach(function(toggle) {
                                const targetId = toggle.getAttribute('data-bs-target') || toggle.getAttribute('data-target');
                                if (targetId) {
                                    const targetElement = document.querySelector(targetId);
                                    if (targetElement) {
                                        targetElement.classList.add('show');
                                        targetElement.style.display = 'block';
                                    }
                                }
                                // Set toggle state to expanded
                                toggle.setAttribute('aria-expanded', 'true');
                                toggle.classList.remove('collapsed');
                            });
                            
                            // Force show all parent ul elements
                            let currentElement = subItem.parentElement;
                            while (currentElement && currentElement !== item) {
                                if (currentElement.tagName === 'UL' || currentElement.classList.contains('collapse')) {
                                    currentElement.style.display = 'block';
                                    currentElement.classList.add('show');
                                }
                                currentElement = currentElement.parentElement;
                            }
                            
                            // Add 'active' class to parent for better visibility
                            item.classList.add('search-expanded');
                        }
                    });
                }
                
                if (shouldShow) {
                    item.classList.remove('hidden');
                    item.style.display = '';
                    visibleCount++;
                    
                    // If searching and has matching child, ensure parent is expanded
                    if (searchTerm !== '' && hasMatchingChild) {
                        const collapseElements = item.querySelectorAll('.collapse');
                        collapseElements.forEach(function(collapse) {
                            collapse.classList.add('show');
                            collapse.style.display = 'block';
                        });
                        
                        // Force show all UL elements within this item
                        const allULs = item.querySelectorAll('ul');
                        allULs.forEach(function(ul) {
                            ul.style.display = 'block';
                            ul.classList.add('show');
                        });
                        
                        // Try to trigger any existing collapse functionality
                        const toggleLinks = item.querySelectorAll('[data-bs-toggle="collapse"], [data-toggle="collapse"]');
                        toggleLinks.forEach(function(link) {
                            link.setAttribute('aria-expanded', 'true');
                            link.classList.remove('collapsed');
                        });
                    }
                } else {
                    item.classList.add('hidden');
                    item.style.display = 'none';
                    item.classList.remove('search-expanded');
                }
                
                // Handle sub-menu visibility when searching
                if (searchTerm !== '' && shouldShow) {
                    const subMenus = item.querySelectorAll('ul li, .collapse li, .submenu li');
                    subMenus.forEach(function(subItem) {
                        const subText = subItem.textContent.toLowerCase();
                        if (subText.includes(searchTerm)) {
                            subItem.style.display = '';
                            subItem.classList.remove('hidden');
                            subItem.classList.add('search-match');
                        } else if (hasMatchingChild) {
                            // Hide non-matching children when parent has matching children
                            subItem.style.display = 'none';
                            subItem.classList.add('hidden');
                            subItem.classList.remove('search-match');
                        }
                    });
                } else if (searchTerm === '') {
                    // Reset all sub-menus when search is cleared
                    const subMenus = item.querySelectorAll('ul li, .collapse li, .submenu li');
                    subMenus.forEach(function(subItem) {
                        subItem.style.display = '';
                        subItem.classList.remove('hidden', 'search-match');
                    });
                    
                    // Collapse all menus when search is cleared
                    const collapseElements = item.querySelectorAll('.collapse');
                    collapseElements.forEach(function(collapse) {
                        collapse.classList.remove('show');
                    });
                    
                    // Reset toggle states
                    const toggleLinks = item.querySelectorAll('[data-bs-toggle="collapse"], [data-toggle="collapse"]');
                    toggleLinks.forEach(function(link) {
                        link.setAttribute('aria-expanded', 'false');
                    });
                    
                    item.classList.remove('search-expanded');
                }
            });
        });

        // Handle "no results" message
        if (!noResultsElement) {
            noResultsElement = createNoResultsElement();
            const navbarContainer = document.querySelector('.dash-navbar');
            if (navbarContainer) {
                navbarContainer.parentNode.appendChild(noResultsElement);
            }
        }

        if (visibleCount === 0 && searchTerm !== '') {
            noResultsElement.style.display = 'block';
        } else {
            noResultsElement.style.display = 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterMenu);
        searchInput.addEventListener('keyup', filterMenu);
    }

    // Handle tab-based menu if category-wise sidemenu is enabled
    const tabLinks = document.querySelectorAll('.dash-tab-link a');
    if (tabLinks.length > 0) {
        tabLinks.forEach(function(tabLink) {
            tabLink.addEventListener('click', function() {
                setTimeout(function() {
                    // Re-run filter after tab content loads
                    if (searchInput && searchInput.value.trim() !== '') {
                        filterMenu();
                    }
                }, 100);
            });
        });
    }

    // ======================
    // SIDEBAR TOGGLE FUNCTIONALITY
    // ======================
    const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
    const toggleIcon = document.getElementById('toggle-icon');
    
    if (sidebarToggleBtn) {
        console.log('✅ Sidebar toggle button found and ready');
        
        // Load saved sidebar state from localStorage
        const savedSidebarState = localStorage.getItem('sidebarCollapsed');
        if (savedSidebarState === 'true') {
            document.body.classList.add('sidebar-collapsed');
            if (toggleIcon) {
                toggleIcon.style.transform = 'rotate(180deg)';
            }
            console.log('📱 Loaded collapsed state from localStorage');
        }
        
        // Add click event listener
        sidebarToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🖱️ Toggle button clicked!');
            
            // Toggle the sidebar-collapsed class on body
            const isCurrentlyCollapsed = document.body.classList.contains('sidebar-collapsed');
            
            if (isCurrentlyCollapsed) {
                document.body.classList.remove('sidebar-collapsed');
                console.log('📖 Sidebar expanded');
            } else {
                document.body.classList.add('sidebar-collapsed');
                console.log('📱 Sidebar collapsed');
            }
            
            const newState = document.body.classList.contains('sidebar-collapsed');
            
            // Save the state to localStorage
            localStorage.setItem('sidebarCollapsed', newState);
            
            // Rotate the icon with smooth transition
            if (toggleIcon) {
                toggleIcon.style.transition = 'transform 0.3s ease';
                toggleIcon.style.transform = newState ? 'rotate(180deg)' : 'rotate(0deg)';
            }
            
            // Update button tooltip
            this.title = newState ? 'Expand Sidebar' : 'Collapse Sidebar';
            
            // Trigger window resize to help responsive elements adjust
            setTimeout(function() {
                window.dispatchEvent(new Event('resize'));
            }, 300);
            
            console.log('💾 State saved:', newState);
        });
        
        // Add hover effect
        sidebarToggleBtn.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#e9ecef';
        });
        
        sidebarToggleBtn.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
    } else {
        console.error('❌ Sidebar toggle button NOT found! Check the HTML structure.');
    }
});
</script>
