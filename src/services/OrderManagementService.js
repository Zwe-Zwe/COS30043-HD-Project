import axios from "axios";

// Make sure to use the correct URL for your XAMPP environment
const API_URL = "http://localhost/COS30043-HD-Project/api";

class OrderManagementService {
  async updateOrderStatus(orderId, status) {
    try {
      console.log(`Updating order ${orderId} status to ${status}`);
      const formData = new FormData();
      formData.append("order_id", orderId);
      formData.append("status", status);

      const response = await axios.post(
        `${API_URL}/update_order_status.php`,
        formData
      );
      console.log("Status update response:", response.data);
      return response.data;
    } catch (error) {
      console.error("Error updating order status:", error);
      throw error;
    }
  }

  async deleteOrder(orderId) {
    try {
      console.log(`Deleting order ${orderId}`);
      const formData = new FormData();
      formData.append("order_id", orderId);

      const response = await axios.post(
        `${API_URL}/delete_order.php`,
        formData
      );
      console.log("Delete order response:", response.data);
      return response.data;
    } catch (error) {
      console.error("Error deleting order:", error);
      throw error;
    }
  }

  async addItemToOrder(orderId, bookId, quantity, price) {
    try {
      console.log(
        `Adding item to order ${orderId}: book #${bookId}, qty: ${quantity}, price: ${price}`
      );
      const formData = new FormData();
      formData.append("order_id", orderId);
      formData.append("book_json_id", bookId);
      formData.append("quantity", quantity);
      formData.append("price", price);

      const response = await axios.post(
        `${API_URL}/add_order_item.php`,
        formData
      );
      console.log("Add item response:", response.data);
      return response.data;
    } catch (error) {
      console.error("Error adding item to order:", error);
      throw error;
    }
  }

  async removeOrderItem(orderItemId) {
    try {
      console.log(`Removing item #${orderItemId}`);
      const formData = new FormData();
      formData.append("item_id", orderItemId);

      const response = await axios.post(
        `${API_URL}/remove_order_item.php`,
        formData
      );
      console.log("Remove item response:", response.data);
      return response.data;
    } catch (error) {
      console.error("Error removing order item:", error);
      throw error;
    }
  }

  async updateOrderItem(orderItemId, quantity) {
    try {
      console.log(`Updating item #${orderItemId} to quantity: ${quantity}`);
      const formData = new FormData();
      formData.append("item_id", orderItemId);
      formData.append("quantity", quantity);

      const response = await axios.post(
        `${API_URL}/update_order_item.php`,
        formData
      );
      console.log("Update item response:", response.data);
      return response.data;
    } catch (error) {
      console.error("Error updating order item:", error);
      throw error;
    }
  }

  async getAvailableBooks() {
    try {
      console.log("Fetching available books...");

      // Use Fetch API for better compatibility with current books.php
      const response = await fetch(`${API_URL}/books.php`);
      const data = await response.json();

      // Handle different response formats
      let booksArray;
      if (Array.isArray(data)) {
        booksArray = data; // Direct array response
      } else if (data.books && Array.isArray(data.books)) {
        booksArray = data.books; // Nested under 'books' property
      } else {
        // Try to find any array in the response that looks like books
        const possibleArrays = Object.values(data).filter(
          (val) =>
            Array.isArray(val) && val.length > 0 && typeof val[0] === "object"
        );
        booksArray = possibleArrays.length > 0 ? possibleArrays[0] : [];
      }

      console.log(`Loaded ${booksArray?.length || 0} books`);
      return booksArray || [];
    } catch (error) {
      console.error("Error fetching books:", error);
      return [];
    }
  }

  async updateOrderDetails(orderId, details) {
    try {
      console.log(`Updating order ${orderId} details:`, details);

      // Create form data
      const formData = new FormData();
      formData.append("order_id", orderId);

      // Add all details to formData
      Object.keys(details).forEach((key) => {
        if (details[key] !== null && details[key] !== undefined) {
          formData.append(key, details[key]);
        }
      });

      // Send the request with proper headers
      const response = await axios.post(
        `${API_URL}/update_order_details.php`,
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      console.log("Update order details response:", response.data);

      if (!response.data.success) {
        throw new Error(
          response.data.message || "Failed to update order details"
        );
      }

      return response.data;
    } catch (error) {
      console.error("Error in updateOrderDetails:", error);
      throw error;
    }
  }
}

export default new OrderManagementService();
