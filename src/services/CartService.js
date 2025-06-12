import { getUser, isLoggedIn } from "@/utils/auth";

class CartService {
  constructor() {
    this.baseUrl = "http://localhost/COS30043-HD-Project/api/cart.php";
    this.booksUrl = "http://localhost/COS30043-HD-Project/api/books.php";
  }

  // Fetch all books from JSON file via PHP wrapper
  async fetchBooks() {
    try {
      const response = await fetch(this.booksUrl);
      const data = await response.json();
      return data.books; // Access the books array from the JSON structure
    } catch (error) {
      console.error("Error fetching books:", error);
      return [];
    }
  }

  // Get cart for current user
  async getCart() {
    if (!isLoggedIn()) {
      // For non-logged in users, use localStorage
      const savedCart = localStorage.getItem("booknestCart");
      return savedCart ? JSON.parse(savedCart) : [];
    }

    // For logged in users, fetch from database
    const user = getUser();
    console.log("Getting cart for user:", user);

    try {
      const response = await fetch(`${this.baseUrl}?user_id=${user.id}`);
      const data = await response.json();

      console.log("Cart API response:", data);

      if (data.status === "success") {
        return data.items || [];
      } else {
        console.error("Error fetching cart:", data.message);
        return [];
      }
    } catch (error) {
      console.error("Error fetching cart:", error);
      return [];
    }
  }

  // Get cart with full book details
  async getCartWithDetails() {
    const cartItems = await this.getCart();
    console.log("Raw cart items:", cartItems);

    if (!cartItems || cartItems.length === 0) {
      return [];
    }

    // Get all books
    const books = await this.fetchBooks();
    console.log("Books loaded:", books.length);

    // Map cart items to full book details with quantities
    const detailedItems = cartItems
      .map((item) => {
        // For consistency, ensure we're working with integers
        const bookId = parseInt(item.book_json_id);
        console.log("Looking for book ID:", bookId);

        const book = books.find((b) => parseInt(b.id) === bookId);

        if (!book) {
          console.warn(`Book with ID ${bookId} not found`);
          return null;
        }

        return {
          ...book,
          quantity: parseInt(item.quantity),
        };
      })
      .filter((item) => item !== null);

    console.log("Detailed cart items:", detailedItems);
    return detailedItems;
  }

  // Save cart
  async saveCart(cartItems) {
    if (!isLoggedIn()) {
      // For non-logged in users, use localStorage
      localStorage.setItem("booknestCart", JSON.stringify(cartItems));
      return true;
    }

    // For logged in users, save to database
    const user = getUser();
    try {
      console.log("Saving cart for user:", user.id, "Items:", cartItems);

      // Make sure we're using book_json_id consistently
      const formattedItems = cartItems.map((item) => ({
        book_json_id: item.book_json_id || item.book_id,
        quantity: item.quantity,
      }));

      const response = await fetch(this.baseUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          user_id: user.id,
          items: formattedItems,
        }),
      });

      // Handle non-JSON responses
      const text = await response.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        console.error("Server returned non-JSON response:", text);
        return false;
      }

      return data.status === "success";
    } catch (error) {
      console.error("Error saving cart:", error);
      return false;
    }
  }

  // Add item to cart
  async addToCart(bookId, quantity = 1) {
    const cartItems = await this.getCart();
    console.log("Adding to cart:", bookId, "Current cart:", cartItems);

    // Check if item already exists
    const existingItemIndex = cartItems.findIndex(
      (item) => parseInt(item.book_json_id) === parseInt(bookId)
    );

    if (existingItemIndex >= 0) {
      // Update quantity
      cartItems[existingItemIndex].quantity =
        parseInt(cartItems[existingItemIndex].quantity) + quantity;
    } else {
      // Add new item
      cartItems.push({
        book_json_id: bookId,
        quantity: quantity,
      });
    }

    return this.saveCart(cartItems);
  }

  // Update item quantity
  async updateQuantity(bookId, quantity) {
    const cartItems = await this.getCart();

    // Find item
    const existingItemIndex = cartItems.findIndex(
      (item) => parseInt(item.book_id) === parseInt(bookId)
    );

    if (existingItemIndex >= 0) {
      // Update quantity
      cartItems[existingItemIndex].quantity = quantity;
      return this.saveCart(cartItems);
    }

    return false;
  }

  // Remove item from cart
  async removeFromCart(bookId) {
    if (!isLoggedIn()) {
      // For non-logged in users, use localStorage
      const cartItems = await this.getCart();
      const updatedCart = cartItems.filter(
        (item) => parseInt(item.book_id) !== parseInt(bookId)
      );
      localStorage.setItem("booknestCart", JSON.stringify(updatedCart));
      return true;
    }

    // For logged in users, delete from database
    const user = getUser();
    try {
      const response = await fetch(this.baseUrl, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          user_id: user.id,
          book_id: bookId,
        }),
      });
      const data = await response.json();
      return data.status === "success";
    } catch (error) {
      console.error("Error removing item from cart:", error);
      return false;
    }
  }

  // Remove multiple items from cart at once
  async removeMultipleItems(bookIds) {
    if (!isLoggedIn()) {
      // For non-logged in users, use localStorage
      const cartItems = await this.getCart();
      const updatedCart = cartItems.filter(
        (item) => !bookIds.includes(parseInt(item.book_id))
      );
      localStorage.setItem("booknestCart", JSON.stringify(updatedCart));
      return true;
    }

    // For logged in users, remove each item from database
    const user = getUser();
    try {
      for (const bookId of bookIds) {
        await fetch(this.baseUrl, {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            user_id: user.id,
            book_id: bookId,
          }),
        });
      }
      return true;
    } catch (error) {
      console.error("Error removing multiple items from cart:", error);
      return false;
    }
  }

  // Calculate total price
  async getCartTotal() {
    const cartWithDetails = await this.getCartWithDetails();
    return cartWithDetails.reduce((total, item) => {
      return total + item.price * item.quantity;
    }, 0);
  }

  // Get total number of items in cart
  async getCartCount() {
    const cartItems = await this.getCart();
    return cartItems.reduce((count, item) => {
      return count + parseInt(item.quantity);
    }, 0);
  }

  // Merge localStorage cart with database cart
  async mergeCartsAfterLogin() {
    if (!isLoggedIn()) return;

    // Get localStorage cart
    const localStorageCart = localStorage.getItem("booknestCart");
    if (!localStorageCart) return;

    const localCart = JSON.parse(localStorageCart);
    if (localCart.length === 0) return;

    // Get database cart
    const user = getUser();
    try {
      const response = await fetch(`${this.baseUrl}?user_id=${user.id}`);
      const data = await response.json();

      if (data.status !== "success") {
        console.error("Error fetching cart for merge:", data.message);
        return;
      }

      const dbCart = data.items;

      // Merge carts
      let mergedCart = [...dbCart];

      for (const localItem of localCart) {
        const existingIndex = mergedCart.findIndex(
          (item) => parseInt(item.book_id) === parseInt(localItem.book_id)
        );

        if (existingIndex >= 0) {
          // Update quantity
          mergedCart[existingIndex].quantity =
            parseInt(mergedCart[existingIndex].quantity) +
            parseInt(localItem.quantity);
        } else {
          // Add new item
          mergedCart.push(localItem);
        }
      }

      // Save merged cart
      await this.saveCart(mergedCart);

      // Clear localStorage cart
      localStorage.removeItem("booknestCart");
    } catch (error) {
      console.error("Error merging carts:", error);
    }
  }

  // Clear cart
  async clearCart() {
    if (!isLoggedIn()) {
      localStorage.removeItem("booknestCart");
      return true;
    }

    // For logged in users, clear database cart
    const user = getUser();
    try {
      const response = await fetch(this.baseUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          user_id: user.id,
          items: [],
        }),
      });
      const data = await response.json();
      return data.status === "success";
    } catch (error) {
      console.error("Error clearing cart:", error);
      return false;
    }
  }
}

export default new CartService();
