<?php 
require_once __DIR__ . '/classes/Core.php';
$core = new Core();

$products = $core->readAll(
    "products p INNER JOIN categories c ON p.category_id = c.id",
    "1",
    "p.id, p.image, p.name AS product_name, p.price, c.name AS category_name"
);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Frontend Layout</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            /* ensures full viewport height */
        }

        /* Navbar */
        .navbar {
            background: #333;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        /* Mobile Menu */
        .menu-toggle {
            display: none;
            font-size: 1.8rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .navbar ul {
                display: none;
                flex-direction: column;
                background: #444;
                position: absolute;
                top: 60px;
                right: 0;
                width: 200px;
                padding: 1rem;
            }

            .navbar ul.active {
                display: flex;
            }

            .menu-toggle {
                display: block;
            }
        }

        /* Content */
        .content {
            flex: 1;
            /* pushes footer down */
            padding: 2rem;
            max-width: 900px;
            margin: auto;
            width: 100%;
        }

        section {
            margin-bottom: 3rem;
        }

        section h2 {
            margin-bottom: 1rem;
            color: #333;
        }

        /* Footer */
        .footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 1rem;
        }

        /* Scroll to top button */
        #scrollTopBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #ff730f;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            display: none;
            transition: 0.3s;
        }

        #scrollTopBtn:hover {
            background: #ff730f;
        }

        .grid-container {
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap:25px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">MyLogo</div>
        <span class="menu-toggle" onclick="toggleMenu()">☰</span>
        <ul id="menu" class="menu">
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <section>
            <div class="module-content">
                <div class="module-title">Click on grid items!</div>

                <div class="grid-container">
                    <?php foreach($products as $product) { ?>
                        <div class="grid-item">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['product_name']; ?>" height="250px" width="250px;">    
                            <h2><?php echo $product['product_name']; ?></h2> 
                            <h5><?php echo $product['category_name']; ?> </h5>
                            <h6><?php echo $product['price']; ?></h6> 
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
        <section id="home">
            <h2>Home</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Scroll down to see the scroll-to-top button.</p>
        </section>

        <section id="about">
            <h2>About</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ultricies, nunc in aliquam cursus, eros
                turpis vulputate libero.</p>
        </section>

        <section id="services">
            <h2>Services</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus nec magna at ipsum tincidunt posuere.
            </p>
        </section>

        <section id="contact">
            <h2>Contact</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur facilisis dui ut tincidunt dignissim.
            </p>
        </section>

        <section>
            <div class="module-content">
                <div class="module-title">Basic HTML Text Tags</div>

                <div class="text-example">
                    <strong>Bold text</strong> using &lt;strong&gt;
                </div>

                <div class="text-example">
                    <em>Italic text</em> using &lt;em&gt;
                </div>

                <div class="text-example">
                    <u>Underlined text</u> using &lt;u&gt;
                </div>

                <div class="text-example">
                    <mark>Highlighted text</mark> using &lt;mark&gt;
                </div>

                <div class="text-example">
                    <del>Deleted text</del> using &lt;del&gt;
                </div>

                <div class="text-example">
                    Line break below: <br> New line using &lt;br&gt;
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <div class="footer">© 2025 My Website. All rights reserved.</div>

    <!-- Scroll to Top Button -->
    <button id="scrollTopBtn" onclick="scrollToTop()">↑</button>

    <script>
        // Toggle mobile menu
        function toggleMenu() {
            let menus = document.getElementsByClassName("menu");
            menus[0].classList.toggle("active"); // toggle first element with class "menu"
        }

        // Scroll to top button
        let scrollBtn = document.getElementById("scrollTopBtn");
        window.onscroll = function () {
            if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                scrollBtn.style.display = "block";
            } else {
                scrollBtn.style.display = "none";
            }
        };
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>

</body>

</html>