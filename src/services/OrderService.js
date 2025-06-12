import { getUser } from "@/utils/auth";

class OrderService {
  constructor() {
    this.ordersUrl = "http://localhost/COS30043-HD-Project/api/orders.php";
    this.booksUrl = "http://localhost/COS30043-HD-Project/api/books.php";
  }

  /**
   * Create a new order
   * @param {Object} orderData Order details
   * @returns {Promise<Object>} Response with order ID
   */
  async createOrder(orderData) {
    try {
      const user = getUser();
      if (!user || !user.id) {
        throw new Error("User not authenticated");
      }

      // Add user ID to order data
      const orderWithUserId = {
        ...orderData,
        userId: user.id,
      };

      console.log("Creating order:", orderWithUserId);

      const response = await fetch(this.ordersUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(orderWithUserId),
      });

      // First check if we can parse the response as JSON
      const text = await response.text();
      let data;

      try {
        data = JSON.parse(text);
      } catch (error) {
        console.error("Server returned non-JSON response:", text);
        throw new Error("The server returned an invalid response");
      }

      if (data.status === "success") {
        return {
          success: true,
          orderId: data.orderId,
        };
      } else {
        throw new Error(data.message || "Failed to create order");
      }
    } catch (error) {
      console.error("Error creating order:", error);
      throw error;
    }
  }

  /**
   * Get book details for order items
   * @param {Array} orderItems List of order items
   * @param {Array} books List of books
   * @returns {Array} Enhanced order items with book details
   */
  async enhanceOrderItems(orderItems, books) {
    if (!orderItems || !books) return orderItems;

    return orderItems.map((item) => {
      const book = books.find(
        (b) => parseInt(b.id) === parseInt(item.book_json_id)
      );
      if (book) {
        return {
          ...item,
          title: book.title || "Unknown title",
          author: book.author || "Unknown author",
          imageLink: book.imageLink || null,
        };
      }
      return item;
    });
  }

  /**
   * Get order by ID
   * @param {number} orderId Order ID
   * @returns {Promise<Object>} Order details
   */
  async getOrderById(orderId) {
    try {
      const response = await fetch(`${this.ordersUrl}?id=${orderId}`);

      // Check if we can parse the response as JSON
      const text = await response.text();
      let data;

      try {
        data = JSON.parse(text);
      } catch (error) {
        console.error("Server returned non-JSON response:", text);
        throw new Error("The server returned an invalid response");
      }

      if (data.status === "success") {
        // Get book details for order items
        const books = await this.fetchBooks();

        // Add book details to each order item
        if (data.order.items && books) {
          data.order.items = await this.enhanceOrderItems(
            data.order.items,
            books
          );
        }

        return data.order;
      } else {
        throw new Error(data.message || "Failed to get order");
      }
    } catch (error) {
      console.error(`Error fetching order ${orderId}:`, error);
      throw error;
    }
  }

  /**
   * Get orders for current user
   * @returns {Promise<Array>} List of orders
   */
  async getUserOrders() {
    try {
      const user = getUser();
      if (!user || !user.id) {
        throw new Error("User not authenticated");
      }

      const response = await fetch(`${this.ordersUrl}?user_id=${user.id}`);

      // Check if we can parse the response as JSON
      const text = await response.text();
      let data;

      try {
        data = JSON.parse(text);
      } catch (error) {
        console.error("Server returned non-JSON response:", text);
        throw new Error("The server returned an invalid response");
      }

      if (data.status === "success") {
        // Get book details for order items
        const books = await this.fetchBooks();

        // Add book details to each order's items
        if (books) {
          for (let order of data.orders) {
            if (order.items) {
              order.items = await this.enhanceOrderItems(order.items, books);
            }
          }
        }

        return data.orders;
      } else {
        throw new Error(data.message || "Failed to get orders");
      }
    } catch (error) {
      console.error("Error fetching user orders:", error);
      throw error;
    }
  }

  /**
   * Fetch all books from JSON file via PHP wrapper
   * @private
   * @returns {Promise<Array>} List of books
   */
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
}

export default new OrderService();
