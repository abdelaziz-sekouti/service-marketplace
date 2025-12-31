    <?php
    session_start();
    require_once './includes/auth_manager.php';
    $authManager = new AuthManager();
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <!-- SEO Meta Tags -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Freelance Money Maker - Best Tools & Services to Boost Your Income 2025</title>
        <meta name="description" content="Discover proven freelance tools, software, and services to increase your income. Upwork, Fiverr, design tools, marketing automation - everything you need to make more money freelancing.">
        <meta name="keywords" content="freelance money maker, freelance tools, increase freelance income, best freelance software, Upwork premium, Fiverr pro, freelance automation, make money online, freelance success, remote work tools">
        <meta name="author" content="Freelance Money Maker">
        <meta name="robots" content="index, follow">

        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="Freelance Money Maker - Boost Your Income Today">
        <meta property="og:description" content="Discover the best tools and services to maximize your freelance income. Proven strategies and top-rated software.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://yourdomain.com">
        <meta property="og:image" content="https://yourdomain.com/images/og-image.jpg">
        <meta property="og:site_name" content="Freelance Money Maker">

        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Freelance Money Maker - Best Tools to Boost Income">
        <meta name="twitter:description" content="Discover proven tools and services to maximize your freelance income.">
        <meta name="twitter:image" content="https://yourdomain.com/images/twitter-image.jpg">

        <!-- Canonical URL -->
        <link rel="canonical" href="https://yourdomain.com">

        <!-- Preconnect for Performance -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://cdnjs.cloudflare.com">
        <link rel="dns-prefetch" href="//picsum.photos">

        <!-- Optimized Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            'primary': '#667eea',
                            'secondary': '#764ba2',
                            'accent': '#f093fb'
                        }
                    }
                }
            }
        </script>

        <!-- Font Awesome (deferred) -->
        <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        </noscript>

        <!-- Custom Styles (minified) -->
        <style>
            .gradient-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)
            }

            .card-hover {
                transition: all .3s ease
            }

            .card-hover:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, .1), 0 10px 10px -5px rgba(0, 0, 0, .04)
            }

            .price-badge {
                background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%)
            }

            .category-badge {
                background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%)
            }

            .lazy-image {
                filter: blur(5px);
                transition: filter .3s
            }

            .lazy-image.loaded {
                filter: blur(0)
            }

            .skeleton {
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: loading 1.5s infinite
            }

            @keyframes loading {
                0% {
                    background-position: 200% 0
                }

                100% {
                    background-position: -200% 0
                }
            }

            .ad-container {
                background: #f8f9fa;
                border: 1px dashed #dee2e6;
                text-align: center;
                padding: 1rem;
                margin: 1rem 0
            }

            .affiliate-disclosure {
                background: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: .5rem;
                padding: .75rem;
                margin: 1rem 0;
                font-size: .875rem
            }
        </style>

        <!-- Structured Data -->
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "WebSite",
                "name": "Freelance Money Maker",
                "description": "Discover the best tools and services to maximize your freelance income",
                "url": "https://yourdomain.com",
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": "https://yourdomain.com/?q={search_term_string}",
                    "query-input": "required name=search_term_string"
                },
                "mainEntity": {
                    "@type": "ItemList",
                    "name": "Freelance Tools and Services",
                    "description": "Curated list of tools and services to boost freelance income"
                }
            }
        </script>
    </head>

    <body class="bg-gray-50">
        <!-- Navigation Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo -->
                    <a href="index.php" class="text-2xl font-bold text-purple-600 flex items-center">
                        <i class="fas fa-coins mr-2"></i>
                        <span class="hidden sm:inline">Freelance Money Maker</span>
                    </a>

                    <!-- Desktop Navigation -->
                    <nav class="hidden lg:flex items-center space-x-8">
                        <a href="#tools" class="text-gray-700 hover:text-purple-600 font-medium transition">Tools</a>
                        <a href="#categories" class="text-gray-700 hover:text-purple-600 font-medium transition">Categories</a>
                        <a href="#about" class="text-gray-700 hover:text-purple-600 font-medium transition">About</a>
                        <a href="#contact" class="text-gray-700 hover:text-purple-600 font-medium transition">Contact</a>
                    </nav>

                    <!-- Right Side Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Cart -->
                        <a href="cart/cart.php" class="relative text-gray-700 hover:text-purple-600 transition">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <span id="cartCount" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                        </a>

                        <!-- User Actions -->
                        <?php if ($authManager->isLoggedIn()): ?>
                            <?php $user = $authManager->getCurrentUser(); ?>
                            <div class="relative group">
                                <button class="flex items-center space-x-2 text-gray-700 hover:text-purple-600 transition">
                                    <i class="fas fa-user-circle text-xl"></i>
                                    <span class="hidden sm:inline"><?php echo htmlspecialchars($user['first_name']); ?></span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <a href="auth/account.php" class="block px-4 py-2 text-gray-700 hover:bg-purple-50 hover:text-purple-600">
                                        <i class="fas fa-user mr-2"></i>My Account
                                    </a>
                                    <a href="auth/orders.php" class="block px-4 py-2 text-gray-700 hover:bg-purple-50 hover:text-purple-600">
                                        <i class="fas fa-shopping-bag mr-2"></i>My Orders
                                    </a>
                                    <hr class="my-1">
                                    <form method="POST" action="auth/logout.php" class="inline">
                                        <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="auth/login.php" class="text-gray-700 hover:text-purple-600 font-medium transition">
                                <i class="fas fa-sign-in-alt mr-1"></i>Login
                            </a>
                            <a href="auth/register.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                <i class="fas fa-user-plus mr-1"></i>Sign Up
                            </a>
                        <?php endif; ?>

                        <!-- Mobile Menu Toggle -->
                        <button id="mobileMenuToggle" class="lg:hidden text-gray-700 hover:text-purple-600">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <nav id="mobileMenu" class="hidden lg:hidden pb-4">
                    <a href="#tools" class="block py-2 text-gray-700 hover:text-purple-600 font-medium">Tools</a>
                    <a href="#categories" class="block py-2 text-gray-700 hover:text-purple-600 font-medium">Categories</a>
                    <a href="#about" class="block py-2 text-gray-700 hover:text-purple-600 font-medium">About</a>
                    <a href="#contact" class="block py-2 text-gray-700 hover:text-purple-600 font-medium">Contact</a>
                </nav>
            </div>
        </header>

        <!-- Hero Section with Optimized Images -->
        <section class="relative bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 text-white overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0 bg-repeat" style="background-image: url('data:image/svg+xml,%3Csvg width=\" 60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"4\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
            </div>

            <!-- Hero Content -->
            <div class="relative container mx-auto px-4 py-20 lg:py-32">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 mb-6">
                            <i class="fas fa-star text-yellow-400 mr-2"></i>
                            <span class="text-sm font-medium">Trusted by 10,000+ Freelancers</span>
                        </div>

                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                            Transform Your Freelance
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-400">
                                Income Today
                            </span>
                        </h1>

                        <p class="text-xl md:text-2xl mb-8 text-purple-100 leading-relaxed">
                            Discover proven tools, software, and services used by successful freelancers to increase productivity and maximize earnings.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-8">
                            <button onclick="scrollToListings()" class="bg-white text-purple-700 px-8 py-4 rounded-full font-semibold hover:bg-purple-50 transition duration-300 shadow-lg">
                                <i class="fas fa-rocket mr-2"></i>Browse Tools
                            </button>
                            <a href="auth/register.php" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-semibold hover:bg-white hover:text-purple-700 transition duration-300">
                                <i class="fas fa-user-plus mr-2"></i>Join Free
                            </a>
                        </div>

                        <!-- Stats -->
                        <div class="flex flex-col sm:flex-row gap-6 justify-center lg:justify-start">
                            <div class="flex items-center">
                                <i class="fas fa-tools text-2xl mr-3 text-yellow-400"></i>
                                <div>
                                    <div class="text-2xl font-bold">500+</div>
                                    <div class="text-purple-200 text-sm">Tools & Services</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-dollar-sign text-2xl mr-3 text-green-400"></i>
                                <div>
                                    <div class="text-2xl font-bold">$2M+</div>
                                    <div class="text-purple-200 text-sm">Income Generated</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Image -->
                    <div class="relative">
                        <img src="https://picsum.photos/seed/freelancerworkspace/600/400.jpg"
                            alt="Freelancer working with productivity tools"
                            class="rounded-lg shadow-2xl w-full h-auto">

                        <!-- Floating Cards -->
                        <div class="absolute -top-6 -right-6 bg-white rounded-lg shadow-lg p-4 transform rotate-3">
                            <div class="flex items-center space-x-3">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <i class="fas fa-chart-line text-green-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-800">+45% Growth</div>
                                    <div class="text-xs text-gray-600">Average increase</div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute -bottom-6 -left-6 bg-white rounded-lg shadow-lg p-4 transform -rotate-3">
                            <div class="flex items-center space-x-3">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <i class="fas fa-clock text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-800">Save 10+ hrs</div>
                                    <div class="text-xs text-gray-600">Per week</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Stats Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-12 pb-4">
            <div class="text-center">
                <div class="text-3xl font-bold" id="totalListings">0</div>
                <div class="text-blue-500">Active Tools</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold" id="totalClicks">0</div>
                <div class="text-blue-500">Clicks Tracked</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold" id="totalCategories">0</div>
                <div class="text-blue-500">Categories</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold">$0</div>
                <div class="text-blue-500">Avg. Price</div>
            </div>
        </div>
        <!-- Header Ad Banner -->
        <div id="headerAd" class="bg-gray-100 py-2">
            <div class="container mx-auto px-4 text-center">
                <!-- Ad will be loaded here -->
            </div>
        </div>


        </header>

        <!-- Hero Section with Optimized Images -->
        <section class="relative bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 text-white overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                
            </div>

            <!-- Hero Content -->

        </section>



        <!-- Header Ad Banner -->
        <div id="headerAd" class="bg-gray-100 py-2">
            <div class="container mx-auto px-4 text-center">
                <!-- Ad will be loaded here -->
            </div>
        </div>
        </div>
        </header>

        <!-- Search and Filter Section -->
        <section class="bg-white shadow-sm sticky top-0 z-10">
            <div class="container mx-auto px-4 py-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search for money-making tools..."
                                class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                aria-label="Search tools">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <select id="categoryFilter" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" aria-label="Filter by category">
                            <option value="">All Categories</option>
                        </select>
                        <select id="sortBy" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" aria-label="Sort by">
                            <option value="newest">Newest First</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="popular">Most Popular</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content with Sidebar -->
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Main Content -->
                <main class="flex-1" id="listings">
                    <!-- Affiliate Disclosure -->
                    <div class="affiliate-disclosure">
                        <h4 class="font-semibold text-yellow-800 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Affiliate Disclosure
                        </h4>
                        <p class="text-yellow-700 text-sm">
                            As an Amazon Associate and affiliate partner, we earn from qualifying purchases.
                            This means we may receive a commission when you click our links and make purchases,
                            at no extra cost to you. Our recommendations are based on thorough research and
                            personal experience.
                        </p>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-3xl font-bold text-gray-800">
                            <i class="fas fa-fire text-orange-500 mr-2"></i>Featured Money-Making Tools
                        </h2>
                        <div class="text-gray-600">
                            <span id="resultCount">0</span> results found
                        </div>
                    </div>

                    <!-- Listings Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="listingsGrid">
                        <!-- Listings will be loaded here -->
                    </div>

                    <!-- Content Ad (between listings) -->
                    <div id="contentAd" class="my-8">
                        <!-- Ad will be loaded here -->
                    </div>

                    <!-- Loading State -->
                    <div id="loadingState" class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 mb-4">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
                        </div>
                        <p class="text-gray-600">Loading amazing money-making tools...</p>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-12 hidden">
                        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No tools found matching your criteria.</p>
                    </div>
                </main>

                <!-- Sidebar -->
                <aside class="lg:w-80">
                    <!-- Sidebar Top Ad -->
                    <div id="sidebarTopAd" class="mb-6">
                        <!-- Ad will be loaded here -->
                    </div>

                    <!-- Popular Tools Widget -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>Popular This Week
                        </h3>
                        <div id="popularTools" class="space-y-3">
                            <!-- Popular tools will be loaded here -->
                        </div>
                    </div>

                    <!-- Categories Widget -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-tags text-blue-500 mr-2"></i>Categories
                        </h3>
                        <div id="categoryWidget" class="space-y-2">
                            <!-- Categories will be loaded here -->
                        </div>
                    </div>

                    <!-- Sidebar Bottom Ad -->
                    <div id="sidebarBottomAd" class="mb-6">
                        <!-- Ad will be loaded here -->
                    </div>

                    <!-- Newsletter Widget -->
                    <div class="bg-purple-100 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-envelope text-purple-600 mr-2"></i>Weekly Tips
                        </h3>
                        <p class="text-gray-700 mb-4 text-sm">Get exclusive freelance strategies and deals</p>
                        <form onsubmit="subscribeNewsletter(event)">
                            <input type="email" placeholder="Your email" required
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 mb-3">
                            <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg text-sm hover:bg-purple-700 transition duration-300">
                                Subscribe Free
                            </button>
                        </form>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Newsletter Section -->
        <section class="bg-purple-100 py-12">
            <div class="container mx-auto px-4 text-center">
                <h3 class="text-2xl font-bold mb-4">
                    <i class="fas fa-envelope mr-2"></i>Get Weekly Money-Making Tips
                </h3>
                <p class="text-gray-700 mb-6">Join 1,000+ freelancers getting exclusive deals and strategies</p>
                <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto" onsubmit="subscribeNewsletter(event)">
                    <input type="email" placeholder="Enter your email" required
                        class="flex-1 px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-purple-500"
                        aria-label="Email for newsletter">
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition duration-300">
                        Subscribe
                    </button>
                </form>
            </div>
        </section>

        <!-- Footer Ad Banner -->
        <div id="footerAd" class="bg-gray-100 py-2">
            <div class="container mx-auto px-4 text-center">
                <!-- Ad will be loaded here -->
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h4 class="text-xl font-bold mb-4">
                            <i class="fas fa-coins mr-2"></i>Freelance Money Maker
                        </h4>
                        <p class="text-gray-400">Your trusted source for freelance income optimization tools and strategies.</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">About Us</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Terms of Service</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Follow Us</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white" aria-label="Facebook"><i class="fab fa-facebook text-xl"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white" aria-label="Twitter"><i class="fab fa-twitter text-xl"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white" aria-label="LinkedIn"><i class="fab fa-linkedin text-xl"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white" aria-label="Instagram"><i class="fab fa-instagram text-xl"></i></a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; 2025 Freelance Money Maker. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Optimized JavaScript -->
        <script>
            // Lazy loading for images
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        img.classList.remove('lazy-image');
                        observer.unobserve(img);
                    }
                });
            });

            let allListings = [];
            let filteredListings = [];
            let adPlacements = {};

            // Load ads and listings
            async function initializeApp() {
                try {
                    // Load ads
                    const adsResponse = await fetch('api/ads.php');
                    const ads = await adsResponse.json();

                    // Organize ads by position
                    ads.forEach(ad => {
                        if (!adPlacements[ad.position]) {
                            adPlacements[ad.position] = [];
                        }
                        adPlacements[ad.position].push(ad);
                    });

                    // Display ads
                    displayAds();

                    // Load listings and stats
                    const [listingsResponse, statsResponse] = await Promise.all([
                        fetch('api/listings.php'),
                        fetch('api/stats.php')
                    ]);

                    allListings = await listingsResponse.json();
                    const stats = await statsResponse.json();

                    updateStats(stats);
                    populateCategories();
                    applyFilters();
                    loadPopularTools();

                    document.getElementById('loadingState').classList.add('hidden');

                    // Setup lazy loading for images
                    setTimeout(() => {
                        document.querySelectorAll('img[data-src]').forEach(img => {
                            imageObserver.observe(img);
                        });
                    }, 100);

                } catch (error) {
                    console.error('Error initializing app:', error);
                    document.getElementById('loadingState').classList.add('hidden');
                    document.getElementById('emptyState').classList.remove('hidden');
                }
            }

            // Display ads in various positions
            function displayAds() {
                // Header ad
                if (adPlacements.header && adPlacements.header.length > 0) {
                    const headerAd = document.getElementById('headerAd').querySelector('.text-center');
                    headerAd.innerHTML = createAdHTML(adPlacements.header[0]);
                }

                // Sidebar ads
                if (adPlacements.sidebar) {
                    const sidebarTopAd = document.getElementById('sidebarTopAd');
                    const sidebarBottomAd = document.getElementById('sidebarBottomAd');

                    if (adPlacements.sidebar.length > 0) {
                        sidebarTopAd.innerHTML = createAdHTML(adPlacements.sidebar[0]);
                    }
                    if (adPlacements.sidebar.length > 1) {
                        sidebarBottomAd.innerHTML = createAdHTML(adPlacements.sidebar[1]);
                    }
                }

                // Content ad
                if (adPlacements.between_content && adPlacements.between_content.length > 0) {
                    document.getElementById('contentAd').innerHTML = createAdHTML(adPlacements.between_content[0]);
                }

                // Footer ad
                if (adPlacements.footer && adPlacements.footer.length > 0) {
                    const footerAd = document.getElementById('footerAd').querySelector('.text-center');
                    footerAd.innerHTML = createAdHTML(adPlacements.footer[0]);
                }
            }

            // Create ad HTML
            function createAdHTML(ad) {
                return `
                <div class="ad-container" style="max-width: ${ad.dimensions.split('x')[0]}px; margin: 0 auto;">
                    <div class="text-xs text-gray-500 mb-1">Advertisement</div>
                    ${ad.ad_code || `<div class="bg-gray-200 p-4 rounded">Ad Space (${ad.dimensions})</div>`}
                </div>
            `;
            }

            // Load popular tools
            function loadPopularTools() {
                const popularListings = allListings
                    .sort((a, b) => (b.clicks || 0) - (a.clicks || 0))
                    .slice(0, 5);

                const container = document.getElementById('popularTools');
                container.innerHTML = popularListings.map(listing => `
                <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded cursor-pointer" onclick="scrollToListing(${listing.id})">
                    <img src="${listing.image || 'https://picsum.photos/seed/' + listing.id + '/50/50.jpg'}" 
                         alt="${listing.title}" class="w-10 h-10 rounded object-cover">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium line-clamp-1">${listing.title}</h4>
                        <p class="text-xs text-gray-500">$${listing.price || '0'}</p>
                    </div>
                </div>
            `).join('');
            }

            // Update stats with animation
            function updateStats(stats) {
                animateValue('totalListings', 0, stats.totalListings || 0, 1000);
                animateValue('totalClicks', 0, stats.totalClicks || 0, 1000);
                animateValue('totalCategories', 0, stats.totalCategories || 0, 1000);
            }

            // Animate counter
            function animateValue(id, start, end, duration) {
                const element = document.getElementById(id);
                const range = end - start;
                const increment = range / (duration / 16);
                let current = start;

                const timer = setInterval(() => {
                    current += increment;
                    if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                        element.textContent = end;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.round(current);
                    }
                }, 16);
            }

            // Populate category filter and widget
            function populateCategories() {
                const categories = [...new Set(allListings.map(l => l.category))];
                const select = document.getElementById('categoryFilter');
                const widget = document.getElementById('categoryWidget');

                categories.forEach(category => {
                    // Filter dropdown
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;
                    select.appendChild(option);

                    // Category widget
                    const count = allListings.filter(l => l.category === category).length;
                    const widgetItem = document.createElement('div');
                    widgetItem.innerHTML = `
                    <a href="#" onclick="filterByCategory('${category}')" class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                        <span class="text-sm">${category}</span>
                        <span class="text-xs text-gray-500">${count}</span>
                    </a>
                `;
                    widget.appendChild(widgetItem);
                });
            }

            // Filter by category
            function filterByCategory(category) {
                document.getElementById('categoryFilter').value = category;
                applyFilters();
                return false;
            }

            // Apply filters and sorting with debouncing
            let filterTimeout;

            function applyFilters() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(() => {
                    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                    const category = document.getElementById('categoryFilter').value;
                    const sortBy = document.getElementById('sortBy').value;

                    // Filter
                    filteredListings = allListings.filter(listing => {
                        const matchesSearch = !searchTerm ||
                            listing.title.toLowerCase().includes(searchTerm) ||
                            listing.description.toLowerCase().includes(searchTerm);
                        const matchesCategory = !category || listing.category === category;
                        return matchesSearch && matchesCategory;
                    });

                    // Sort
                    switch (sortBy) {
                        case 'price-low':
                            filteredListings.sort((a, b) => (a.price || 0) - (b.price || 0));
                            break;
                        case 'price-high':
                            filteredListings.sort((a, b) => (b.price || 0) - (a.price || 0));
                            break;
                        case 'popular':
                            filteredListings.sort((a, b) => (b.clicks || 0) - (a.clicks || 0));
                            break;
                        case 'newest':
                        default:
                            filteredListings.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    }

                    renderListings();
                }, 300);
            }

            // Render listings with lazy loading and affiliate tracking
            function renderListings() {
                const grid = document.getElementById('listingsGrid');
                const emptyState = document.getElementById('emptyState');

                document.getElementById('resultCount').textContent = filteredListings.length;

                if (filteredListings.length === 0) {
                    grid.innerHTML = '';
                    emptyState.classList.remove('hidden');
                    return;
                }

                emptyState.classList.add('hidden');
                grid.innerHTML = filteredListings.map((listing, index) => `
                <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover">
                    <div class="relative">
                        <img data-src="${listing.image || 'https://picsum.photos/seed/' + listing.id + '/400/250.jpg'}" 
                             alt="${listing.title}" class="w-full h-48 object-cover lazy-image skeleton"
                             loading="lazy">
                        ${listing.affiliate_link ? '<span class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded text-xs"><i class="fas fa-link mr-1"></i>Affiliate</span>' : ''}
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2 line-clamp-2">${listing.title}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">${listing.description}</p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="price-badge text-white px-3 py-1 rounded-full text-sm font-bold">
                                $${listing.price || '0'}
                            </span>
                            <span class="category-badge text-white px-3 py-1 rounded-full text-xs">
                                ${listing.category}
                            </span>
                        </div>
                        <div class="flex gap-2">
                            ${listing.affiliate_link ? `
                                <a href="api/affiliate_click.php?id=${listing.id}" target="_blank" 
                                   class="flex-1 bg-purple-600 text-white text-center py-2 rounded hover:bg-purple-700 transition duration-300"
                                   onclick="trackAffiliateClick(${listing.id}, event)">
                                    <i class="fas fa-external-link-alt mr-1"></i>Get Deal
                                </a>
                            ` : `
                                <button class="flex-1 bg-gray-400 text-white py-2 rounded cursor-not-allowed" disabled>
                                    Not Available
                                </button>
                            `}
                            <button onclick="viewDetails(${listing.id})" class="px-3 py-2 border border-purple-600 text-purple-600 rounded hover:bg-purple-50 transition duration-300">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
                ${index === 3 && adPlacements.between_content ? '<div class="col-span-full">' + createAdHTML(adPlacements.between_content[0]) + '</div>' : ''}
            `).join('');
            }

            // Track affiliate click
            function trackAffiliateClick(listingId, event) {
                // Add click tracking analytics
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'affiliate_click', {
                        'listing_id': listingId,
                        'category': allListings.find(l => l.id === listingId)?.category
                    });
                }
            }

            // View details
            function viewDetails(id) {
                const listing = allListings.find(l => l.id === id);
                if (listing) {
                    alert(`Details for ${listing.title}:\n\n${listing.description}\n\nPrice: $${listing.price || '0'}\nCategory: ${listing.category}`);
                }
            }

            // Scroll to specific listing
            function scrollToListing(id) {
                const element = document.querySelector(`[onclick*="${id}"]`);
                if (element) {
                    element.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            // Newsletter subscription
            function subscribeNewsletter(event) {
                event.preventDefault();
                const email = event.target.querySelector('input[type="email"]').value;

                // Track newsletter signup
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'newsletter_signup', {
                        'email': email
                    });
                }

                alert('Thank you for subscribing! You will receive weekly money-making tips at ' + email);
                event.target.reset();
            }

            // Scroll to listings
            function scrollToListings() {
                document.getElementById('listings').scrollIntoView({
                    behavior: 'smooth'
                });
            }

            // Event listeners with passive events for better performance
            document.getElementById('searchInput').addEventListener('input', applyFilters, {
                passive: true
            });
            document.getElementById('categoryFilter').addEventListener('change', applyFilters, {
                passive: true
            });
            document.getElementById('sortBy').addEventListener('change', applyFilters, {
                passive: true
            });

            // Initialize on DOMContentLoaded
            document.addEventListener('DOMContentLoaded', initializeApp);
        </script>
    </body>

    </html>