<!doctype html>
<html>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        define("INITIALIZING_DATABASE", 1);
        require_once("db_conn.php");

        $pdo->query("CREATE DATABASE IF NOT EXISTS tools_database");
        $pdo->query("USE tools_database");

        // Create Category Table
        $pdo->query("CREATE TABLE IF NOT EXISTS categories (
            category_id INT PRIMARY KEY AUTO_INCREMENT,
            category_name VARCHAR(50) UNIQUE NOT NULL
        )");

        // Create Users Table
        $pdo->query("CREATE TABLE IF NOT EXISTS users (
            user_id INT PRIMARY KEY AUTO_INCREMENT,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(50),
            last_name VARCHAR(50),
            address TEXT,
            city VARCHAR(50)
        )");

        // Create Tools Table
        $pdo->query("CREATE TABLE IF NOT EXISTS tools (
            tool_id INT PRIMARY KEY AUTO_INCREMENT,
            tool_name VARCHAR(255) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            brand VARCHAR(50),
            description LONGTEXT,
            durability VARCHAR(50),
            material VARCHAR(50),
            stock_quantity INT NOT NULL,
            category_id INT,
            image_name VARCHAR(255),
            FOREIGN KEY (category_id) REFERENCES categories(category_id)
        )");

        // Create Cart Table
        $pdo->query("CREATE TABLE IF NOT EXISTS carts (
            cart_id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT,
            active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id)
        )");

        // Create Cart Items Table
        $pdo->query("CREATE TABLE IF NOT EXISTS cart_items (
            cart_item_id INT PRIMARY KEY AUTO_INCREMENT,
            cart_id INT,
            tool_id INT,
            quantity INT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            FOREIGN KEY (cart_id) REFERENCES carts(cart_id),
            FOREIGN KEY (tool_id) REFERENCES tools(tool_id)
        )");

        $pdo->query(
            "INSERT IGNORE INTO categories (category_id, category_name) VALUES
            (1, 'Air Tools and Accessories'),
            (2, 'Cordless Power Tools'),
            (3, 'Corded Power Tools'),
            (4, 'Hydraulic Tools and Accessories'),
            (5, 'HVAC Tools')"
        );

        $pdo->query("INSERT IGNORE INTO tools (
                tool_id, 
                tool_name, 
                price, 
                brand, 
                description, 
                durability, 
                material, 
                stock_quantity, 
                category_id, 
                image_name
            ) VALUES
                (1, 'Air Angle Grinder', 129.99, 'XYZ Brand', 'An Air Angle Grinder is a versatile pneumatic tool designed for a range of tasks such as grinding, cutting, cleaning, and polishing. Powered by compressed air, it offers efficient performance and precise control.', 'High Durability', 'Metal', 15, 1, '1.jpg'),
                (2, 'Air Stapler', 79.99, 'ABC Tools', 'Air Staplers are pneumatic tools specifically engineered to effortlessly drive staples into diverse materials. Powered by compressed air, these versatile tools find applications in fastening tasks across woodworking, upholstery, and construction.', 'Medium Durability', 'Plastic and Metal', 20, 1, '2.jpg'),
                (3, 'Air Screwdriver', 49.99, 'EFG Manufacturing', 'Air Screwdrivers are industrial-grade tools powered by compressed air, engineered for repetitive screwdriving tasks. Known for their consistent and reliable performance, these pneumatic screwdrivers are essential in assembly lines and manufacturing processes, offering efficient and precise fastening capabilities.', 'High Durability', 'Steel', 25, 1, '3.jpg'),

                (4, 'Cordless Circular Saws', 159.99, 'LMN Tools', 'Cordless Circular Saws are powerful cutting tools that operate on rechargeable 18-volt batteries, providing portability and freedom from electrical outlets.', 'High Durability', 'Metal', 20, 2, '4.jpg'),
                (5, 'Cordless Impact Drivers', 99.99, 'OPQ Inc.', 'Cordless Impact Drivers are versatile power tools that operate on rechargeable batteries, typically 18V or similar. Designed for driving screws and bolts with high torque and speed.', 'Medium Durability', 'Plastic and Metal', 15, 2, '5.jpg'),
                (6, 'Cordless Angle Grinders', 79.99, 'RST Brands', 'Cordless Angle Grinders are portable power tools that run on rechargeable batteries, providing freedom from electrical outlets for cutting, grinding, and polishing tasks.', 'High Durability', 'Metal', 25, 2, '6.jpg'),

                (7, 'Drill', 89.99, 'UVW Tools', 'A drill is a versatile power tool used for creating holes in various materials or fastening objects together. It typically consists of a motor that drives a rotating drill bit.', 'High Durability', 'Metal', 18, 3, '7.jpg'),
                (8, 'Screwdriver', 29.99, 'XYZ Corp', 'A screwdriver is a manual or power tool designed for turning screws to fasten or loosen them. It consists of a handle and a shaft ending in a tip that fits into the head of a screw.', 'Medium Durability', 'Steel', 20, 3, '8.jpg'),
                (9, 'Breaker Hammer', 129.99, 'ABC Industries', 'A breaker hammer, also known as a demolition hammer or jackhammer, is a powerful and heavy-duty power tool used for breaking up and demolishing hard materials like concrete, asphalt, or rock.', 'High Durability', 'Metal', 15, 3, '9.jpg'),

                (10, 'Hydraulic Cutter', 169.99, 'EFG Tools', 'A hydraulic cutter is a powerful tool that uses hydraulic pressure to cut through various materials, including metal, plastic, and rubber.', 'High Durability', 'Metal', 12, 4, '10.jpg'),
                (11, 'Mini Jack', 89.99, 'LMN Enterprises', 'A mini jack, often referred to as a compact or portable hydraulic jack, is a small and lightweight lifting device commonly used for raising vehicles or objects in limited space.', 'Medium Durability', 'Steel', 18, 4, '11.jpg'),
                (12, 'Hydraulic Nut Splitter', 49.99, 'RST Tools', 'A hydraulic nut splitter is a specialized tool designed to safely and efficiently remove corroded or overtightened nuts.', 'High Durability', 'Metal', 25, 4, '12.jpg'),

                (13, 'Crimper', 34.99, 'UVW Industries', 'A crimper is a tool used to compress and deform metal, typically to join or terminate wires and cables securely.', 'Medium Durability', 'Steel', 20, 5, '13.jpg'),
                (14, 'Notcher', 59.99, 'OPQ Tools', 'A notcher is a tool designed to create precise notches or cuts in materials, often used in metalworking or woodworking.', 'High Durability', 'Metal', 15, 5, '14.jpg'),
                (15, 'Tube Piercing Plier', 19.99, 'EFG Supplies', 'Tube piercing pliers are specialized tools used for making holes or punctures in tubing, particularly in automotive and plumbing applications.', 'Medium Durability', 'Metal', 18, 5, '15.jpg')");

        echo "<h3>Database Initialized</h3>";
        header("Location: index.php?init=1");
    }

    ?>
    <form method="POST">
        <input type="submit" value="Initialize Database">
    </form>
</body>

</html>