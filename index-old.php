<?php
session_start();
require_once 'db_connect.php';

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
    <meta property="og:description" content="Discover best tools and services to maximize your freelance income. Proven strategies and top-rated software.">
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
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    
    <!-- Custom Styles (minified) -->
    <style>.gradient-bg{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%)}.card-hover{transition:all .3s ease}.card-hover:hover{transform:translateY(-5px);box-shadow:0 20px 25px -5px rgba(0,0,0,.1),0 10px 10px -5px rgba(0,0,0,.04)}.price-badge{background:linear-gradient(45deg,#f093fb 0%,#f5576c 100%)}.category-badge{background:linear-gradient(45deg,#4facfe 0%,#00f2fe 100%)}.lazy-image{filter:blur(5px);transition:filter .3s}.lazy-image.loaded{filter:blur(0)}.skeleton{background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:loading 1.5s infinite}@keyframes loading{0%{background-position:200% 0}100%{background-position:-200% 0}}.ad-container{background:#f8f9fa;border:1px dashed #dee2e6;text-align:center;padding:1rem;margin:1rem 0}.affiliate-disclosure{background:#fff3cd;border:1px solid #ffeaa7;border-radius:.5rem;padding:.75rem;margin:1rem 0;font-size:.875rem}</style>
    
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
            <div class="absolute inset-0 bg-repeat" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.1\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"4\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
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

    <!-- Header Ad Banner -->
    <div id="headerAd" class="bg-gray-100 py-2">
        <div class="container mx-auto px-4 text-center">
            <!-- Ad will be loaded here -->
        </div>
    </div>