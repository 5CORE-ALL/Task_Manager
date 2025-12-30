<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog Gallery</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css"> 
   <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
  <style>
    :root {
      --primary: #ff6f28;
      --primary-dark: #e05a1c;
      --secondary:  #ff9e53;
      --accent: #f72585;
      --dark: #1a1a2e;
      --light: #f8f9fa;
      --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      --gradient-hover: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%); 
      --card-shadow: 0 10px 20px rgba(255, 111, 40, 0.15);
      --hover-shadow: 0 15px 30px rgba(255, 111, 40, 0.25);
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
      min-height: 100vh;
      overflow-x: hidden;
    }
    
    /* Sidebar Styles */
    #sidebar {
      width: 280px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
      padding: 1rem;
      z-index: 1000;
      transition: transform 0.3s ease;
      overflow-y: auto;
    }
    
    .sidebar-header {
      padding: 1rem 0;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      margin-bottom: 1rem;
      text-align: center;
    }
    
   
   
    .sidebar-logo-img {
  width: 120px;
  height: 120px;
  margin: 0 auto 1rem;
  border-radius: 20px;
  object-fit: contain;
  display: block;
  border: 3px solid white;
}
    
    .sidebar-title {
      font-weight: 700;
      font-size: 1.4rem;
      background: var(--gradient);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin: 0;
    }
    
    .sidebar-subtitle {
      font-size: 0.9rem;
      color: #6c757d;
      margin-top: 0.5rem;
    }
    
    .sidebar-menu {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .sidebar-menu li {
      margin-bottom: 0.5rem;
    }
    
    .sidebar-menu a {
      display: block;
      padding: 0.8rem 1rem;
      text-decoration: none;
      color: var(--dark);
      border-radius: 10px;
      transition: all 0.3s ease;
      font-weight: 500;
    }
    
    .sidebar-menu a:hover, .sidebar-menu a.active {
      background: var(--gradient);
      color: white;
    }
    
    .sidebar-menu i {
      margin-right: 0.8rem;
      width: 20px;
      text-align: center;
    }
    
    .sidebar-category {
      padding: 1rem 0;
    }
    
    .sidebar-category-title {
      font-weight: 600;
      font-size: 1rem;
      color: var(--dark);
      margin-bottom: 1rem;
      padding: 0 1rem;
    }
    
    /* Main Content */
    #content {
      margin-left: 280px;
      transition: margin-left 0.3s ease;
      padding: 0 15px;
      min-height: 100vh;
    }
    
    /* Toggle Button */
    #sidebarToggle {
      display: none;
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 1100;
      background: var(--gradient);
      color: white;
      border: none;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      box-shadow: 0 4px 15px rgba(255, 111, 40, 0.3);
    }
    
    .navbar {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .navbar-brand {
      font-weight: 800;
      background: var(--gradient);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      font-size: 1.8rem;
      letter-spacing: -0.5px;
    }
    
    .custom-btn {
      background: var(--gradient);
      border: none;
      color: white;
      padding: 12px 28px;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 111, 40, 0.3);
      letter-spacing: 0.5px;
    }
    
    .custom-btn:hover {
      background: var(--gradient-hover);
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(255, 111, 40, 0.4);
    }
    
    .custom-btn:active {
      transform: translateY(0);
      box-shadow: 0 4px 15px rgba(255, 111, 40, 0.3);
    }
    
    .header-section {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 20px;
      padding: 2.5rem;
      margin: 2rem auto;
      box-shadow: var(--card-shadow);
      text-align: center;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.5);
    }
    
    .header-title {
      font-weight: 800;
      color: var(--dark);
      margin-bottom: 1rem;
      font-size: 2.5rem;
      background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    
    .header-subtitle {
      color: #6c757d;
      margin-bottom: 1.5rem;
      font-size: 1.1rem;
    }
    
    .blog-card {
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      height: 100%;
      border: none;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: var(--card-shadow);
      background: white;
    }
    
    .blog-card:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: var(--hover-shadow);
    }
    
    .blog-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
      opacity: 0;
      transition: opacity 0.4s ease;
      z-index: 1;
      border-radius: 20px;
    }
    
    .blog-card:hover::before {
      opacity: 1;
    }
    
    .read-icon {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 5rem;
      color: white;
      opacity: 0.8;
      z-index: 2;
      transition: all 0.4s ease;
      text-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }
    
    .blog-card:hover .read-icon {
      opacity: 1;
      transform: translate(-50%, -50%) scale(1.1);
    }
    
    .blog-fixed {
      height: 240px;
      object-fit: cover;
      width: 100%;
      transition: transform 0.5s ease;
    }
    
    .blog-card:hover .blog-fixed {
      transform: scale(1.1);
    }
    
    .card-body {
      padding: 1.8rem;
    }
    
    .card-title {
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 0.5rem;
      font-size: 1.2rem;
    }
    
    .blog-thumb-container {
      position: relative;
      overflow: hidden;
    }
    
    .blog-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: rgba(0, 0, 0, 0.7);
      color: white;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.8rem;
      z-index: 3;
      font-weight: 500;
    }
    
    /* Animation for cards */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .blog-item {
      animation: fadeIn 0.6s ease-out;
    }
    
    /* Filter buttons */
    .filter-buttons {
      margin-bottom: 1.5rem;
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      justify-content: center;
    }
    
    .filter-btn {
      background: white;
      border: 1px solid #ddd;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .filter-btn:hover, .filter-btn.active {
      background: var(--gradient);
      color: white;
      border-color: var(--primary);
    }
    
    /* Coming soon section */
    .coming-soon {
      text-align: center;
      padding: 4rem 2rem;
      background: rgba(255, 255, 255, 0.8);
      border-radius: 20px;
      margin: 2rem 0;
      box-shadow: var(--card-shadow);
    }
    
    .coming-soon i {
      font-size: 4rem;
      color: #ccc;
      margin-bottom: 1.5rem;
    }
    
    .coming-soon h3 {
      color: #6c757d;
      font-weight: 600;
    }
    
    /* Empty state */
    .empty-state {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 60vh;
      text-align: center;
      padding: 2rem;
    }
    
    .empty-state i {
      font-size: 4rem;
      color: #ddd;
      margin-bottom: 1.5rem;
    }
    
    .empty-state h3 {
      color: #6c757d;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    .empty-state p {
      color: #8a8a8a;
      max-width: 500px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
      #sidebar {
        transform: translateX(-100%);
      }
      
      #sidebar.active {
        transform: translateX(0);
      }
      
      #content {
        margin-left: 0;
      }
      
      #sidebarToggle {
        display: block;
      }
    }
/* Make overlay visually visible but non-blocking */
.blog-card::before {
  pointer-events: none !important; /* allow clicks to pass through */
}

/* Ensure card content (buttons, text) sits above overlay */
.blog-card .card-body {
  position: relative;
  z-index: 10;
}

/* Ensure the image and overlay stay below buttons */
.blog-thumb-container,
.blog-thumb-container img,
.blog-card::before {
  z-index: 1;
}

/* Make the entire card visually layered correctly */
.blog-card {
  position: relative;
  overflow: hidden;
  z-index: 1;
}

/* Optional: ensure hover opacity still works fine */
.blog-card:hover::before {
  opacity: 1;
  transition: opacity 0.4s ease;
}

  </style>
</head>
<body>

  <!-- Sidebar Toggle Button -->
  <button id="sidebarToggle" class="btn">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Sidebar -->
<div id="sidebar">
  <div class="sidebar-header">
    <img src="{{asset('images/1920 x 557.png')}}" alt="Task Manager Logo" class="sidebar-logo-img">
  </div>
  
  <div class="sidebar-category">
    <ul class="sidebar-menu">
      <li><a href="#" onclick="openform(1)"><i class="fas fa-plus"></i>Add Blog</a></li>
      <hr>
      <li><a href="#" data-category="task-manager"><i class="fas fa-tasks"></i>Task Manager</a></li>
      <li><a href="#" data-category="team-logger"><i class="fas fa-users"></i>Team Logger</a></li>
      <li><a href="#" data-category="barcode"><i class="fas fa-barcode"></i>GS1 Barcode Creation</a></li>
      <li><a href="#" data-category="blogging"><i class="fas fa-blog"></i>Blogging</a></li>
      <li><a href="#" data-category="seo"><i class="fas fa-search"></i>SEO Onpage</a></li>
      <li><a href="#" data-category="amazon"><i class="fas fa-shopping-cart"></i>Amazon</a></li>
      <li><a href="#" data-category="social-media"><i class="fas fa-share-alt"></i>Social Media</a></li>
    </ul>
  </div>
</div> <!-- Closing sidebar div -->

  <!-- Main Content -->
  <div id="content">
    <!-- Content will be dynamically loaded here -->
    <div class="empty-state">
      <i class="fas fa-blog"></i>
      <h3>Welcome to Task Manager Blog</h3>
      <p>Select a category from the sidebar to view blog posts.</p>
    </div>    
  </div>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      const sidebarToggle = document.getElementById('sidebarToggle');
      const content = document.getElementById('content');
      const menuLinks = document.querySelectorAll('.sidebar-menu a');
      
      // Remove active class from all menu items initially
      menuLinks.forEach(item => {
        item.classList.remove('active');
      });
      
      // Sidebar toggle functionality
      sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
      });
      
      // Close sidebar when clicking outside of it on mobile
      document.addEventListener('click', function(event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickInsideToggle = sidebarToggle.contains(event.target);
        
        if (window.innerWidth <= 992 && !isClickInsideSidebar && !isClickInsideToggle && sidebar.classList.contains('active')) {
          sidebar.classList.remove('active');
        }
      });
      
      // Function to load content based on category
      function loadContent(category) {
        let title = '';
        let contentHTML = '';
        
        switch(category) {
          case 'task-manager':
            title = 'Task Manager Blog';
            contentHTML = `
              <div class="container">
                <div class="header-section">
                  <h2 class="header-title">${title}</h2>
                </div>                
              </div>

              <div class="container mb-5">
                <div class="row g-4" id="blog-container">
                </div>
              </div>
            `;
            break;
            
          case 'team-logger':
            title = 'Team Logger Blog';
            contentHTML = `
              <div class="container">
                <div class="header-section">
                  <h2 class="header-title">${title}</h2>
                </div>
              </div>
              
              <div class="container mb-5">
                <div class="row g-4" id="blog-container">
                </div>
              </div>
            `;
            break;

            case 'barcode':
            title = 'GS1 Barcode Creation Blog';
            contentHTML = `
              <div class="container">
                <div class="header-section">
                  <h2 class="header-title">${title}</h2>
                </div>
              </div>
              
              <div class="container mb-5">
                <div class="row g-4" id="blog-container">
                </div>
              </div>
            `;
            break;

            case 'blogging':
            title = 'Blogging';
            contentHTML = `
              <div class="container">
                <div class="header-section">
                  <h2 class="header-title">${title}</h2>
                </div>
              </div>
              
              <div class="container mb-5">
                <div class="row g-4" id="blog-container">
                </div>
              </div>
            `;
            break;
            
         case 'seo':
            title = 'SEO Onpage Blog';
            contentHTML = `
              <div class="container">
                <div class="header-section">
                  <h2 class="header-title">${title}</h2>
                </div>
              </div>
              
              <div class="container mb-5">
                <div class="row g-4" id="blog-container">
                </div>
              </div>
            `;
            break;
            
         case 'amazon':
            title = 'Amazon Blog';
            contentHTML = `
              <div class="container">
                <div class="header-section">
                  <h2 class="header-title">${title}</h2>
                </div>
              </div>
              
              <div class="container mb-5">
                <div class="row g-4" id="blog-container">
                </div>
              </div>
            `;
            break;

            case 'social-media':
            title = 'Social Media Blog';
            contentHTML = `
              <div class="container">
                <div class="header-section">
                  <h2 class="header-title">${title}</h2>
                </div>
              </div>
              
              <div class="container mb-5">
                <div class="row g-4" id="blog-container">
                </div>
              </div>
            `;
            break;
            
            
            
          default:
             break;
        }
        
        // Update the content area
        content.innerHTML = contentHTML;

// Fetch and display dynamic blogs for this category
$.ajax({
    url: "{{ route('blogs.byCategory') }}",
    type: "GET",
    data: { menu_type: category },
    success: function (response) {
        if (response.blogs && response.blogs.length > 0) {
            let blogsHTML = '';
            response.blogs.forEach(blog => {
               blogsHTML += `
  <div class="col-md-4 blog-item">
    <div class="card shadow-sm blog-card">
      
      <!-- Featured Image & Read Icon -->
      <div class="blog-thumb-container">
        <a href="${blog.blog_url}" target="_blank" class="blog-link">
          <img class="blog-fixed" src="/storage/${blog.featured_image}" alt="${blog.title}">
          <div class="read-icon">
            <i class="fas fa-book-reader"></i>
          </div>
        </a>
      </div>

      <!-- Card Content -->
      <div class="card-body text-center">
        <h5 class="card-title">${blog.title}</h5>

        <!-- Action Buttons -->
        <div class="mt-3 d-flex justify-content-center gap-2">
          <button type="button" class="btn btn-sm btn-warning" onclick="event.stopPropagation(); editBlog(${blog.id});">
            <i class="fas fa-edit"></i> Edit
          </button>
          <button type="button" class="btn btn-sm btn-danger" onclick="event.stopPropagation(); deleteBlog(${blog.id});">
            <i class="fas fa-trash"></i> Delete
          </button>
        </div>
      </div>
    </div>
  </div>
`;

            });

            // Append dynamic blogs to the container
            $('#blog-container').append(blogsHTML);
        }
    },
    error: function () {
        console.warn("Could not load blogs for category:", category);
    }
});
      }
      
      // Menu click functionality
      menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          
          const category = this.getAttribute('data-category');
          
          // Update active states
          menuLinks.forEach(item => {
            item.classList.remove('active');
          });
          this.classList.add('active');
          
          // Load content
          loadContent(category);
          
          // Close sidebar on mobile after selection
          if (window.innerWidth <= 992) {
            sidebar.classList.remove('active');
          }
        });
      });
      
       loadContent('task-manager');
      document.querySelector('[data-category="task-manager"]').classList.add('active');
    });
  </script>


<script>
function openform(element) {
    $.confirm({
        title: '📝 Add Blog!',
        content: '' +
            '<form id="blogForm" enctype="multipart/form-data">' +
            '<div class="form-group">' +
                '<label>Blog URL</label>' +
                '<input type="url" class="form-control" name="blog_url" placeholder="https://example.com/blog" required><br>' +

                '<label>Featured Image</label>' +
                '<input type="file" class="form-control" name="featured_image" accept="image/*"><br>' +

                '<label>Title</label>' +
                '<input type="text" class="form-control" name="title" placeholder="e.g. Laravel Blog Post" required><br>' +

                '<label>Menu Type</label>' +
                 '<select id="menuTypeSelect" class="form-control" name="menu_type" required>' +
                     '<option value="">Select Category</option>' +
                 '</select>' +
            '</div>' +
            '</form>',
        buttons: {
            formSubmit: {
                text: 'Submit',
                btnClass: 'btn-blue',
                action: function () {
                    const form = this.$content.find('#blogForm')[0];
                    const formData = new FormData(form);

                    // Add CSRF token
                    formData.append('_token', '{{ csrf_token() }}');

                    // Send AJAX request with FormData (for image upload)
                    $.ajax({
                        url: "{{ route('blogs.store') }}",
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $.alert({
                                title: '✅ Success!',
                                content: response.message || 'Blog added successfully!',
                                boxWidth: '30%',
                                useBootstrap: false,
                                backgroundDismiss: true,
                                containerFluid: true,
                                onOpenBefore: function () {
                                    $('.jconfirm-overlay').css({
                                        'backdrop-filter': 'blur(5px)',
                                        '-webkit-backdrop-filter': 'blur(5px)',
                                        'background-color': 'rgba(0,0,0,0.5)'
                                    });
                                },
                                onDestroy: function () {
                                    location.reload();
                                }
                            });
                        },
                        error: function (xhr) {
                            $.alert({
                                title: '⚠️ Error!',
                                content: xhr.responseJSON?.message || 'Something went wrong. Please try again.',
                                type: 'red',
                                boxWidth: '30%',
                                useBootstrap: false
                            });
                        }
                    });

                    return false; // prevent auto close
                }
            },
            cancel: function () {
                location.reload();
            }
        },
        boxWidth: '35%',
        useBootstrap: false,
        backgroundDismiss: true,
        containerFluid: true,
        onOpenBefore: function () {
            $('.jconfirm-overlay').css({
                'backdrop-filter': 'blur(5px)',
                '-webkit-backdrop-filter': 'blur(5px)',
                'background-color': 'rgba(0,0,0,0.5)'
            });
        }
    });

    setTimeout(() => {
    const categories = document.querySelectorAll('.sidebar-menu a');
    const dropdown = document.querySelector('#menuTypeSelect');
    categories.forEach(item => {
        const cat = item.getAttribute('data-category');
        const name = item.textContent.trim();
        if (cat) {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = name;
            dropdown.appendChild(option);
        }
    });
}, 100);
}

function editBlog(id) {
    $.ajax({
        url: "{{ route('blogs.byCategory') }}",
        type: "GET",
        data: { blog_id: id },
        success: function (response) {
            const blog = response.blogs[0];

            $.confirm({
                title: '✏️ Edit Blog',
                content: `
                    <form id="editBlogForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Blog URL</label>
                            <input type="url" class="form-control" name="blog_url" value="${blog.blog_url}" required><br>
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" value="${blog.title}" required><br>
                            <label>Menu Type</label>
                            <input type="text" class="form-control" name="menu_type" value="${blog.menu_type}" required><br>
                            <label>Change Featured Image (optional)</label>
                            <input type="file" class="form-control" name="featured_image" accept="image/*"><br>
                            <img src="/storage/${blog.featured_image}" width="100%" style="border-radius:10px;">
                        </div>
                    </form>
                `,
                buttons: {
                    update: {
                        text: 'Update',
                        btnClass: 'btn-blue',
                        action: function () {
                            const form = this.$content.find('#editBlogForm')[0];
                            const formData = new FormData(form);
                            formData.append('_token', '{{ csrf_token() }}');

                            $.ajax({
                                url: `/blogs/update/${id}`,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function (res) {
                                    $.alert({
                                        title: '✅ Success!',
                                        content: res.message,
                                        boxWidth: '30%',
                                        useBootstrap: false,
                                        onDestroy: function () {
                                            location.reload();
                                        }
                                    });
                                },
                                error: function (xhr) {
                                    $.alert({
                                        title: '⚠️ Error',
                                        content: xhr.responseJSON?.message || 'Failed to update blog',
                                        type: 'red',
                                        boxWidth: '30%',
                                        useBootstrap: false
                                    });
                                }
                            });
                            return false;
                        }
                    },
                    cancel: function () {}
                },
                boxWidth: '35%',
                useBootstrap: false,
                backgroundDismiss: true
            });
        },
        error: function () {
            $.alert({
                title: '⚠️ Error',
                content: 'Unable to load blog details.',
                type: 'red'
            });
        }
    });
}

function deleteBlog(id) {
    $.confirm({
        title: '⚠️ Confirm Delete',
        content: 'Are you sure you want to delete this blog?',
        type: 'red',
        buttons: {
            confirm: {
                text: 'Yes, Delete',
                btnClass: 'btn-danger',
                action: function () {
                    $.ajax({
                        url: `/blogs/delete/${id}`,
                        type: "DELETE",
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (res) {
                            $.alert({
                                title: '🗑️ Deleted!',
                                content: res.message,
                                boxWidth: '30%',
                                useBootstrap: false,
                                onDestroy: function () {
                                    location.reload();
                                }
                            });
                        },
                        error: function (xhr) {
                            $.alert({
                                title: '⚠️ Error',
                                content: xhr.responseJSON?.message || 'Failed to delete blog',
                                type: 'red',
                                boxWidth: '30%',
                                useBootstrap: false
                            });
                        }
                    });
                }
            },
            cancel: function () {}
        },
        boxWidth: '30%',
        useBootstrap: false,
        backgroundDismiss: true
    });
}

</script>


 

</body>
</html>

