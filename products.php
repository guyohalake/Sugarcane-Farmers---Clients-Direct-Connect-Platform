<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: ../login.html");
    exit();
}
require_once '../config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - CaneLink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-container {
            display: flex;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .add-btn {
            background-color: #2E7D32;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 30px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
            background-color: #f5f5f5;
        }

        .product-price {
            font-size: 1.2em;
            font-weight: bold;
            color: #2E7D32;
            margin: 10px 0;
        }

        .product-quantity {
            color: #666;
            margin-bottom: 10px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }

        .edit-btn, .delete-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            flex: 1;
        }

        .edit-btn {
            background-color: #2196F3;
            color: white;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 10px;
        }

        .default-image {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
            height: 200px;
            border-radius: 5px;
        }

        .default-image i {
            font-size: 48px;
            color: #ccc;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .fixed-buttons {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="fixed-buttons">
        <button class="add-btn" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Add New Product
        </button>
       
    </div>

    <div class="dashboard-container">
        <?php include 'template.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1>Products Page</h1>
            </div>

            <div id="productsGrid" class="product-grid">
             
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add New Product</h2>
            <form id="productForm">
                <input type="hidden" id="productId" name="id">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required placeholder="Enter product name">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Price (KSH per KG)</label>
                    <input type="number" id="price" name="price" required placeholder="Enter price in KSH">
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" required>
                </div>
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <button type="submit" class="add-btn">
                    <i class="fas fa-save"></i> Save Product
                </button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('productModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        function loadProducts() {
            const grid = document.getElementById('productsGrid');
            grid.innerHTML = '<div class="loading">Loading products...</div>';
            
            fetch('get_products.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Products data:', data); // Debug line
                    grid.innerHTML = '';
                    
                    if (!Array.isArray(data) || data.length === 0) {
                        grid.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <h3>No Products Yet</h3>
                                <p>Click the "Add New Product" button to add your first product.</p>
                            </div>
                        `;
                        return;
                    }

                    data.forEach(product => {
                        const hasImage = product.image_url && product.image_url !== 'null';
                        const imageHtml = hasImage 
                            ? `<img src="../${product.image_url}" class="product-image" alt="${product.name}" onerror="this.onerror=null; this.src='../uploads/products/default.jpg';">`
                            : `<div class="default-image"><i class="fas fa-image"></i></div>`;

                        const card = `
                            <div class="product-card">
                                ${imageHtml}
                                <h3>${product.name}</h3>
                                <p>${product.description}</p>
                                <div class="product-price">KSH ${parseFloat(product.price).toLocaleString()} per KG</div>
                                <div class="product-quantity">Stock: ${product.quantity} KG</div>
                                <div class="product-actions">
                                    <button onclick="editProduct(${product.id})" class="edit-btn">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button onclick="deleteProduct(${product.id})" class="delete-btn">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        `;
                        grid.innerHTML += card;
                    });
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    grid.innerHTML = `
                        <div class="error-state">
                            <i class="fas fa-exclamation-circle"></i>
                            <h3>Error Loading Products</h3>
                            <p>Please try again later.</p>
                        </div>
                    `;
                });
        }

        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted'); // Debug line
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            fetch('add_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received'); // Debug line
                return response.json();
            })
            .then(data => {
                console.log('Data:', data); // Debug line
                if (data.success) {
                    alert('Product saved successfully!');
                    closeModal();
                    loadProducts();
                } else {
                    alert(data.message || 'Failed to save product');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving product. Please try again.');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save"></i> Save Product';
            });
        });

        // Load products when page loads
        document.addEventListener('DOMContentLoaded', loadProducts);

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('productModal')) {
                closeModal();
            }
        }

        // Add image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.innerHTML = `
                        <div style="margin-top: 10px;">
                            <img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
                        </div>
                    `;
                    const existingPreview = this.parentElement.querySelector('.preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    preview.className = 'preview';
                    this.parentElement.appendChild(preview);
                }.bind(this);
                reader.readAsDataURL(file);
            }
        });

        // Edit product function
        function editProduct(productId) {
            fetch(`get_product.php?id=${productId}`)
                .then(response => response.json())
                .then(product => {
                    document.getElementById('productId').value = product.id;
                    document.getElementById('name').value = product.name;
                    document.getElementById('description').value = product.description;
                    document.getElementById('price').value = product.price;
                    document.getElementById('quantity').value = product.quantity;
                    
                    document.getElementById('modalTitle').textContent = 'Edit Product';
                    document.getElementById('productModal').style.display = 'block';
                    
                    // If there's an existing image, show it
                    if (product.image_url) {
                        const preview = document.createElement('div');
                        preview.className = 'preview';
                        preview.innerHTML = `
                            <div style="margin-top: 10px;">
                                <img src="../${product.image_url}" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
                            </div>
                        `;
                        const existingPreview = document.querySelector('.preview');
                        if (existingPreview) {
                            existingPreview.remove();
                        }
                        document.querySelector('.form-group:last-child').appendChild(preview);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load product details');
                });
        }

        // Delete product function
        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                const formData = new FormData();
                formData.append('product_id', productId);
                
                fetch('delete_product.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product deleted successfully');
                        loadProducts(); // Reload the products list
                    } else {
                        alert(data.message || 'Failed to delete product');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting product');
                });
            }
        }

        // Update form submission to handle both add and edit
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const productId = document.getElementById('productId').value;
            const submitButton = this.querySelector('button[type="submit"]');
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            // Determine if we're adding or editing
            const url = productId ? 'edit_product.php' : 'add_product.php';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(productId ? 'Product updated successfully!' : 'Product added successfully!');
                    closeModal();
                    loadProducts();
                } else {
                    alert(data.message || 'Failed to save product');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving product');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save"></i> Save Product';
            });
        });

        function addToCart(product) {
            const quantity = prompt("Enter quantity:");
            if (quantity > 0) {
                const formData = new FormData();
                formData.append('product_id', product.id);
                formData.append('quantity', quantity);
                formData.append('price', product.price);
                formData.append('action', 'add');

                fetch('cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert("Product added to cart!");
                    location.reload(); // Reload the page to update the cart
                });
            } else {
                alert("Please enter a valid quantity.");
            }
        }
    </script>
</body>
</html> 