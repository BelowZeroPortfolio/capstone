<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jollibee - Online Ordering</title>
    <style>
        /* Reset and base styles */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }

        /* Header styles */
        header {
            background-color: #e3000f;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .logo { width: 100px; height: auto; }
        .search-bar {
            flex-grow: 1;
            margin: 0 1rem;
            max-width: 500px;
        }
        .search-bar input {
            width: 100%;
            padding: 0.5rem;
            border: none;
            border-radius: 5px;
        }

        /* Main container */
        .container {
            display: flex;
            padding: 1rem;
            position: relative;
        }
        .main-content { flex: 1; margin-right: 250px; } /* Adjust margin to accommodate sidebar width */
        .sidebar {
            width: 250px;
            position: absolute;
            right: 1rem;
            top: 1rem;
            background-color: #f8f8f8;
            padding: 1rem;
            border-radius: 5px;
            z-index: 10;
        }

        /* Store info */
        .store-info {
            background-color: #f8f8f8;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        /* Categories */
        .categories ul {
            list-style-type: none;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .categories li {
            background-color: #f0f0f0;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
        }

        /* Horizontal Slider */
        .horizontal-slider {
            width: 100%;
            overflow: hidden;
            position: relative;
            margin-bottom: 2rem;
        }
        .slider-container {
            display: flex;
            transition: transform 0.5s ease;
        }
        .slider-item {
            flex: 0 0 20%;
            padding: 0.5rem;
            box-sizing: border-box;
        }
        .slider-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
        .slider-item-content {
            text-align: center;
        }
        .slider-item-content h3 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        .slider-item-content p {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .slider-controls button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(227, 0, 15, 0.7);
            color: white;
            border: none;
            padding: 1rem 0.5rem;
            cursor: pointer;
            font-size: 1.5rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .slider-controls button:hover {
            background-color: rgba(227, 0, 15, 1);
        }
        .slider-prev {
            left: 10px;
        }
        .slider-next {
            right: 10px;
        }

        /* Sidebar */
        .cart-toggle {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .toggle-btn {
            background-color: #e3000f;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
        }
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background-color: #e3000f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 1rem;
        }

        /* Responsive design */
        @media (max-width: 1024px) {
            .slider-item {
                flex: 0 0 25%;
            }
        }
        @media (max-width: 768px) {
            header { flex-direction: column; align-items: stretch; }
            .logo { margin-bottom: 1rem; }
            .search-bar { margin: 1rem 0; max-width: none; }
            .main-content { margin-right: 0; margin-bottom: 1rem; }
            .sidebar {
                position: static;
                width: 100%;
                margin-top: 1rem;
            }
            .slider-item {
                flex: 0 0 33.33%;
            }
            .slider-controls button {
                padding: 0.5rem 0.25rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <img src="https://ih0.redbubble.net/image.415273697.8510/flat,1000x1000,075,f.jpg" alt="Jollibee Logo" class="logo">
        <div class="search-bar">
            <input type="text" placeholder="Search products">
        </div>
    </header>
    
    <div class="container">
        <div class="main-content">
            <div class="store-info">
                <h2>Jollibee (Sample Location) - Sample Street</h2>
                <p>Open: 9:00 AM - 10:00 PM</p>
            </div>
            
            <div class="categories">
                <h3>All categories</h3>
                <ul>
                    <li>Chickenjoy</li>
                    <li>Burgers</li>
                    <li>Jolly Spaghetti</li>
                    <li>Desserts</li>
                    <li>Beverages</li>
                </ul>
            </div>
            
            <div class="horizontal-slider">
                <div class="slider-container">
                    <div class="slider-item">
                        <img src="https://via.placeholder.com/150x150.png?text=Chickenjoy+Bucket" alt="Chickenjoy Bucket">
                        <div class="slider-item-content">
                            <h3>Family Bucket Meal</h3>
                            <p>8-pc Chickenjoy</p>
                            <p>₱599</p>
                        </div>
                    </div>
                    <div class="slider-item">
                        <img src="https://via.placeholder.com/150x150.png?text=Jolly+Spaghetti" alt="Jolly Spaghetti">
                        <div class="slider-item-content">
                            <h3>Jolly Spaghetti Pan</h3>
                            <p>Family-size Spaghetti</p>
                            <p>₱220</p>
                        </div>
                    </div>
                    <div class="slider-item">
                        <img src="https://via.placeholder.com/150x150.png?text=Yumburger" alt="Yumburger">
                        <div class="slider-item-content">
                            <h3>Yumburger Value Meal</h3>
                            <p>With fries and drink</p>
                            <p>₱89</p>
                        </div>
                    </div>
                    <div class="slider-item">
                        <img src="https://via.placeholder.com/150x150.png?text=Peach+Mango+Pie" alt="Peach Mango Pie">
                        <div class="slider-item-content">
                            <h3>Peach Mango Pie</h3>
                            <p>Sweet dessert pie</p>
                            <p>₱35</p>
                        </div>
                    </div>
                    <div class="slider-item">
                        <img src="https://via.placeholder.com/150x150.png?text=Chocolate+Sundae" alt="Chocolate Sundae">
                        <div class="slider-item-content">
                            <h3>Chocolate Sundae</h3>
                            <p>Creamy ice cream</p>
                            <p>₱39</p>
                        </div>
                    </div>
                    <div class="slider-item">
                        <img src="https://via.placeholder.com/150x150.png?text=Burger+Steak" alt="Burger Steak">
                        <div class="slider-item-content">
                            <h3>Burger Steak</h3>
                            <p>With mushroom gravy</p>
                            <p>₱55</p>
                        </div>
                    </div>
                    <div class="slider-item">
                        <img src="https://via.placeholder.com/150x150.png?text=Jolly+Hotdog" alt="Jolly Hotdog">
                        <div class="slider-item-content">
                            <h3>Jolly Hotdog</h3>
                            <p>Classic hotdog sandwich</p>
                            <p>₱50</p>
                        </div>
                    </div>
                    <div class="slider-item">
                        <img src="https://via.placeholder.com/150x150.png?text=Palabok+Fiesta" alt="Palabok Fiesta">
                        <div class="slider-item-content">
                            <h3>Palabok Fiesta</h3>
                            <p>Filipino noodle dish</p>
                            <p>₱120</p>
                        </div>
                    </div>
                </div>
                <div class="slider-controls">
                    <button class="slider-prev">&lt;</button>
                    <button class="slider-next">&gt;</button>
                </div>
            </div>
        </div>
        
        <div class="sidebar">
            <div class="cart-toggle">
                <button class="toggle-btn">Delivery</button>
                <button class="toggle-btn">Pick-up</button>
            </div>
            <h3>Your cart</h3>
            <p>Start adding items to your cart</p>
            <p>Subtotal: ₱0</p>
            <p>Total (incl. VAT): ₱0</p>
            <button class="checkout-btn">Go to checkout</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sliderContainer = document.querySelector('.slider-container');
            const sliderItems = document.querySelectorAll('.slider-item');
            const prevButton = document.querySelector('.slider-prev');
            const nextButton = document.querySelector('.slider-next');
            const itemsPerSlide = 3;
            const itemsPerView = 5;
            let currentIndex = 0;

            function updateSlider() {
                const itemWidth = sliderItems[0].offsetWidth;
                sliderContainer.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
                
                // Update button states
                prevButton.disabled = currentIndex === 0;
                nextButton.disabled = currentIndex >= sliderItems.length - itemsPerView;
                
                // Update button styles
                prevButton.style.opacity = prevButton.disabled ? '0.5' : '1';
                nextButton.style.opacity = nextButton.disabled ? '0.5' : '1';
            }

            prevButton.addEventListener('click', () => {
                if (currentIndex > 0) {
                    currentIndex = Math.max(currentIndex - itemsPerSlide, 0);
                    updateSlider();
                }
            });

            nextButton.addEventListener('click', () => {
                if (currentIndex < sliderItems.length - itemsPerView) {
                    currentIndex = Math.min(currentIndex + itemsPerSlide, sliderItems.length - itemsPerView);
                    updateSlider();
                }
            });

            // Initial update
            updateSlider();

            // Update on window resize
            window.addEventListener('resize', updateSlider);
        });
    </script>
</body>
</html>