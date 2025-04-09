<?php
session_start();
require '../connect.php';

// Check if user is admin
if (!isset($_SESSION["username"]) || $_SESSION["username"] !== "Admin") {
    echo "<p style='color:red; text-align:center;'>You do not have permission to access this page!</p>";
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_product'])) {
    $id = $_POST['product_id'] ?? '';
    $category_name = trim($_POST['category_name']);
    $product_name = trim($_POST['product_name']);
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $description = trim($_POST['description']);
    $image_url = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($_FILES['image']['name']);
        $tempname = $_FILES['image']['tmp_name'];
        $target_dir = "../uploads/";
        $target_file = $target_dir . $image_name;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); 
        }

        if (move_uploaded_file($tempname, $target_file)) {
            $image_url = $image_name; 
        } else {
            echo "<script>alert('Failed to upload image!');</script>";
        }
    }

    // Ensure category exists or create it
    $stmt = $conn->prepare("SELECT category_id FROM categories WHERE category_name = ?");
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $stmt->bind_result($category_id);
    if (!$stmt->fetch()) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $category_id = $stmt->insert_id;
    }
    $stmt->close();

    if ($id) {
        // Update product
        $stmt = $conn->prepare("UPDATE products SET category_id=?, product_name=?, price=?, stock_quantity=?, description=?, image_url=? WHERE product_id=?");
        $stmt->bind_param("isdissi", $category_id, $product_name, $price, $stock_quantity, $description, $image_url, $id);
    } else {
        // Add new product
        $stmt = $conn->prepare("INSERT INTO products (category_id, product_name, price, stock_quantity, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdiss", $category_id, $product_name, $price, $stock_quantity, $description, $image_url);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Product saved successfully!'); window.location='product.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle delete product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully!'); window.location='product.php';</script>";
    } else {
        echo "<script>alert('Error deleting product: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Fetch products for display
$result = $conn->query("SELECT p.product_id, c.category_name, p.product_name, p.price, p.stock_quantity, p.description, p.image_url FROM products p JOIN categories c ON p.category_id = c.category_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="product.css">
</head>
<body>
    <div class="container">
        <h2>Product Management</h2>

        <!-- Form to add/update products -->
        <form action="product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="product_id">

            <label for="category_name">Category:</label>
            <input type="text" name="category_name" id="category_name" placeholder="Enter category" required>

            <label for="product_name">Product Name:</label>
            <input type="text" name="product_name" id="product_name" placeholder="Enter product name" required>

            <label for="price">Price:</label>
            <input type="text" name="price" id="price" placeholder="Enter price" required>

            <label for="stock_quantity">Stock Quantity:</label>
            <input type="text" name="stock_quantity" id="stock_quantity" placeholder="Enter stock quantity" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4" placeholder="Enter product description"></textarea>

            <label for="image">Image:</label>
            <input type="file" name="image" id="image" accept="image/*">

            <button type="submit" name="save_product" class="btn">Save Product</button>
        </form>

        <!-- Product list -->
        <?php if ($result->num_rows > 0): ?>
        <div id="product-list">
            <h3>Product List</h3>
            <div class="product-cards">
                <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="../uploads/<?= htmlspecialchars($product['image_url']) ?>" alt="Product Image">
                    <p>ID: <?= htmlspecialchars($product['product_id']) ?></p>
                    <p>Category: <?= htmlspecialchars($product['category_name']) ?></p>
                    <p>Product Name: <?= htmlspecialchars($product['product_name']) ?></p>
                    <p>Price: $<?= htmlspecialchars($product['price']) ?></p>
                    <p>Stock: <?= htmlspecialchars($product['stock_quantity']) ?></p>
                    <p>Description: <?= htmlspecialchars($product['description']) ?></p>
                    <button class="edit-btn" onclick="editProduct('<?= $product['product_id'] ?>', '<?= $product['category_name'] ?>', '<?= $product['product_name'] ?>', '<?= $product['price'] ?>', '<?= $product['stock_quantity'] ?>', '<?= $product['description'] ?>')">Edit</button>
                    <a href="product.php?delete=<?= htmlspecialchars($product['product_id']) ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php else: ?>
        <p>No products available.</p>
        <?php endif; ?>

    <div class="buttons">
        <a href="admin.php">Back</a>
    </div>

    <script>
        function editProduct(id, category, name, price, stock, description) {
            document.getElementById("product_id").value = id;
            document.getElementById("category_name").value = category;
            document.getElementById("product_name").value = name;
            document.getElementById("price").value = price;
            document.getElementById("stock_quantity").value = stock;
            document.getElementById("description").value = description;
        }
    </script>
</body>
</html>
