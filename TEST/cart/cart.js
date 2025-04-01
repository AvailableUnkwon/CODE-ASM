let cart_stg = JSON.parse(localStorage.getItem('cart')) || [];

function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart_stg));
}

function addToCart() {

    let product = this.closest('.product-items');
    let productName = product.querySelector('.product-name').textContent;
    let productPrice = product.querySelector('.product-price').textContent;
    let productImg = product.querySelector('img').src;

    let exit = null;

    for (let i = 0; i < cart_stg.length; i++) {
        if (cart_stg[i].name === productName) {
            exit = cart_stg[i];
            break;
        }
    }

    if (exit) {
        exit.quantity++;
    }
    else {
        cart_stg.push({
            img: productImg,
            name: productName,
            price: productPrice,
            quantity: 1
        });
    }

    saveCart();

    alert('Sản phẩm đã được thêm vào giỏ hàng!');
}

let buttons = document.querySelectorAll('.add-to-cart');

for (let i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener('click', addToCart);
}

function renderCart() {
    let cartContainer = document.getElementById("cart-items");
    let totalPrice = 0;
    cartContainer.innerHTML = "";
    for (let index = 0; index < cart_stg.length; index++) {
        let item = cart_stg[index];
        let itemPrice = parseInt(item.price.replace(/[^0-9]/g, "")); // Parse price as an integer
        let itemTotal = itemPrice * item.quantity;
        totalPrice += itemTotal;

        let productHTML = `
        <div class="cart-item">
            <img src="${item.img}" class="cart-item-img">
            <div>
               <p><strong>${item.name}</strong></p>
               <p>Price: ${itemPrice.toLocaleString()}đ</p>
            </div>
            <div class="quantity-controls">
                <button onclick="updateQuantity(${index}, -1)">-</button>
                <span>${item.quantity}</span>
                <button onclick="updateQuantity(${index}, +1)">+</button>
                <button class="remove-item" onclick="updateQuantity(${index}, 0)">Delete</button>
            </div>
            <p><strong>Total: ${itemTotal.toLocaleString()}đ</strong></p>
        </div>
        `;
        cartContainer.innerHTML += productHTML;
    }

    document.getElementById("totalPrice").innerText = totalPrice.toLocaleString(); 
}

function updateQuantity(index, change) {
    if (cart_stg[index].quantity + change > 0) {
        cart_stg[index].quantity += change;
    } else {
        cart_stg.splice(index, 1);
    }
    saveCart();
    renderCart();
}

function clearCart() {
    cart_stg = [];
    saveCart();
    document.getElementById("cart-items").innerHTML = "";
    document.getElementById("totalPrice").innerText = "0";
}

function backToShop() {
    window.location.href = "../main/main.html";
}
window.onload = renderCart;