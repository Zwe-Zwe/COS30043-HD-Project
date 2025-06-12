<template>
  <div class="container py-5">
    <h1 class="text-center mb-2">Frequently Asked Questions</h1>
    <p class="text-center lead mb-5">Find answers to common questions about BookNest</p>
    
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <!-- FAQ Categories -->
        <ul class="nav nav-pills mb-4 justify-content-center" id="faqTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button 
              class="nav-link active" 
              id="orders-tab" 
              data-bs-toggle="pill" 
              data-bs-target="#orders" 
              type="button" 
              role="tab" 
              aria-controls="orders" 
              aria-selected="true">
              Orders & Delivery
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button 
              class="nav-link" 
              id="returns-tab" 
              data-bs-toggle="pill" 
              data-bs-target="#returns" 
              type="button" 
              role="tab" 
              aria-controls="returns" 
              aria-selected="false">
              Returns & Refunds
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button 
              class="nav-link" 
              id="account-tab" 
              data-bs-toggle="pill" 
              data-bs-target="#account" 
              type="button" 
              role="tab" 
              aria-controls="account" 
              aria-selected="false">
              Account & Payment
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button 
              class="nav-link" 
              id="products-tab" 
              data-bs-toggle="pill" 
              data-bs-target="#products" 
              type="button" 
              role="tab" 
              aria-controls="products" 
              aria-selected="false">
              Products
            </button>
          </li>
        </ul>
        
        <!-- Search Box -->
        <div class="row justify-content-center mb-5">
          <div class="col-md-8">
            <div class="input-group">
              <input 
                type="text" 
                class="form-control form-control-lg" 
                placeholder="Search FAQs..." 
                v-model="searchQuery"
              >
              <button class="btn btn-primary" type="button">
                <i class="bi bi-search"></i>
              </button>
            </div>
          </div>
        </div>
        
        <!-- FAQ Content -->
        <div class="tab-content" id="faqTabsContent">
          <!-- Orders & Delivery Tab -->
          <div class="tab-pane fade show active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
            <div class="accordion" id="accordionOrders">
              <div class="accordion-item" v-for="(faq, index) in filteredFaqs.orders" :key="'order-'+index">
                <h2 class="accordion-header">
                  <button 
                    class="accordion-button collapsed" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    :data-bs-target="'#order-collapse-'+index" 
                    aria-expanded="false" 
                    :aria-controls="'order-collapse-'+index"
                  >
                    {{ faq.question }}
                  </button>
                </h2>
                <div 
                  :id="'order-collapse-'+index" 
                  class="accordion-collapse collapse" 
                  data-bs-parent="#accordionOrders"
                >
                  <div class="accordion-body" v-html="faq.answer"></div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Returns & Refunds Tab -->
          <div class="tab-pane fade" id="returns" role="tabpanel" aria-labelledby="returns-tab">
            <div class="accordion" id="accordionReturns">
              <div class="accordion-item" v-for="(faq, index) in filteredFaqs.returns" :key="'return-'+index">
                <h2 class="accordion-header">
                  <button 
                    class="accordion-button collapsed" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    :data-bs-target="'#return-collapse-'+index" 
                    aria-expanded="false" 
                    :aria-controls="'return-collapse-'+index"
                  >
                    {{ faq.question }}
                  </button>
                </h2>
                <div 
                  :id="'return-collapse-'+index" 
                  class="accordion-collapse collapse" 
                  data-bs-parent="#accordionReturns"
                >
                  <div class="accordion-body" v-html="faq.answer"></div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Account & Payment Tab -->
          <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-tab">
            <div class="accordion" id="accordionAccount">
              <div class="accordion-item" v-for="(faq, index) in filteredFaqs.account" :key="'account-'+index">
                <h2 class="accordion-header">
                  <button 
                    class="accordion-button collapsed" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    :data-bs-target="'#account-collapse-'+index" 
                    aria-expanded="false" 
                    :aria-controls="'account-collapse-'+index"
                  >
                    {{ faq.question }}
                  </button>
                </h2>
                <div 
                  :id="'account-collapse-'+index" 
                  class="accordion-collapse collapse" 
                  data-bs-parent="#accordionAccount"
                >
                  <div class="accordion-body" v-html="faq.answer"></div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Products Tab -->
          <div class="tab-pane fade" id="products" role="tabpanel" aria-labelledby="products-tab">
            <div class="accordion" id="accordionProducts">
              <div class="accordion-item" v-for="(faq, index) in filteredFaqs.products" :key="'product-'+index">
                <h2 class="accordion-header">
                  <button 
                    class="accordion-button collapsed" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    :data-bs-target="'#product-collapse-'+index" 
                    aria-expanded="false" 
                    :aria-controls="'product-collapse-'+index"
                  >
                    {{ faq.question }}
                  </button>
                </h2>
                <div 
                  :id="'product-collapse-'+index" 
                  class="accordion-collapse collapse" 
                  data-bs-parent="#accordionProducts"
                >
                  <div class="accordion-body" v-html="faq.answer"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Still Need Help Section -->
        <div class="text-center mt-5 pt-3 border-top">
          <h4>Still need help?</h4>
          <p>Our customer support team is here to assist you.</p>
          <router-link to="/contact" class="btn btn-primary">
            Contact Support
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FaqView',
  data() {
    return {
      searchQuery: '',
      faqs: {
        orders: [
          {
            question: "How do I track my order?",
            answer: "You can track your order by logging into your account and viewing your order history. Each order has a tracking number that you can use to follow its delivery status."
          },
          {
            question: "How long will it take to receive my order?",
            answer: "Standard delivery takes 3-5 business days. Express shipping takes 1-2 business days. Orders placed after 2pm may be processed the next business day."
          },
          {
            question: "Can I change my shipping address after placing an order?",
            answer: "If your order hasn't shipped yet, please contact customer service immediately to update the shipping address. Once an order has shipped, we can't change the delivery address."
          },
          {
            question: "Do you ship internationally?",
            answer: "Yes, we ship to select countries internationally. International shipping rates and delivery times vary by destination. Additional customs fees and taxes may apply."
          },
          {
            question: "Is free shipping available?",
            answer: "Yes! We offer free standard shipping on all orders over $35 within Australia."
          }
        ],
        returns: [
          {
            question: "How do I return an item?",
            answer: "To return an item, log in to your account, go to 'Order History,' select the relevant order, and click 'Return Items.' Follow the instructions to print a return label and prepare your package."
          },
          {
            question: "What is your return policy?",
            answer: "We accept returns within 30 days of delivery. Items must be in original condition, unused, and with original packaging. <a href='/shipping'>Read our full return policy</a>."
          },
          {
            question: "How long does it take to process a refund?",
            answer: "Once we receive your return, our team will inspect it within 1-2 business days. After approval, refunds typically take 5-7 business days to appear on your statement, depending on your bank's processing times."
          },
          {
            question: "Can I exchange an item instead of returning it?",
            answer: "Yes, exchanges are available for items within 30 days of purchase. Start a return process and select 'Exchange' as your option. You'll be able to select a replacement item."
          },
          {
            question: "Do I need to pay for return shipping?",
            answer: "Returns due to our error (wrong item shipped, defective product) are free. For other returns, a small shipping fee may apply, which will be deducted from your refund amount."
          }
        ],
        account: [
          {
            question: "How do I create an account?",
            answer: "You can create an account by clicking the 'Register' link at the top of our website. You'll need to provide your name, email address, and create a password."
          },
          {
            question: "I forgot my password. How can I reset it?",
            answer: "Click 'Login' at the top of our website, then select 'Forgot Password.' Enter the email address associated with your account, and we'll send you a link to reset your password."
          },
          {
            question: "What payment methods do you accept?",
            answer: "We accept Visa, Mastercard, American Express, PayPal, and Afterpay. All payments are securely processed using SSL encryption."
          },
          {
            question: "Is my payment information secure?",
            answer: "Yes, all payment data is encrypted using SSL technology. We don't store your full credit card details on our servers. Your security is our priority."
          },
          {
            question: "Can I update my account information?",
            answer: "Yes, you can update your personal information anytime by logging into your account and going to 'Account Settings.'"
          }
        ],
        products: [
          {
            question: "Do you sell eBooks?",
            answer: "Yes, we offer a wide selection of eBooks. You can filter for digital formats when browsing our catalog."
          },
          {
            question: "How do I access my eBook purchases?",
            answer: "After purchasing an eBook, you can access it in the 'My Library' section of your account. Our eBooks are compatible with most e-readers and can be downloaded in multiple formats."
          },
          {
            question: "Are pre-orders available?",
            answer: "Yes, we offer pre-orders for upcoming releases. Pre-ordered items will ship on or just before the release date, and your payment will be processed when the item ships."
          },
          {
            question: "Do you sell used books?",
            answer: "Currently, we only sell new books to ensure the highest quality for our customers."
          },
          {
            question: "How can I find out if a book is in stock?",
            answer: "In-stock status is displayed on each product page. Items listed as 'In Stock' are available to ship immediately. 'Out of Stock' items can be added to your wishlist to receive notifications when they're available."
          }
        ]
      }
    };
  },
  computed: {
    filteredFaqs() {
      if (!this.searchQuery) {
        return this.faqs;
      }
      
      const query = this.searchQuery.toLowerCase();
      
      const filterFaqsByQuery = (faqList) => {
        return faqList.filter(faq => 
          faq.question.toLowerCase().includes(query) || 
          faq.answer.toLowerCase().includes(query)
        );
      };
      
      return {
        orders: filterFaqsByQuery(this.faqs.orders),
        returns: filterFaqsByQuery(this.faqs.returns),
        account: filterFaqsByQuery(this.faqs.account),
        products: filterFaqsByQuery(this.faqs.products)
      };
    }
  }
};
</script>

<style scoped>
.nav-pills .nav-link {
  margin: 0 0.25rem;
  padding: 0.5rem 1rem;
}

.nav-pills .nav-link.active {
  background-color: #0d6efd;
}

.accordion-item {
  margin-bottom: 0.5rem;
  border-radius: 0.375rem;
  overflow: hidden;
}

.accordion-button:not(.collapsed) {
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
}

.accordion-button:focus {
  box-shadow: none;
}

@media (max-width: 767px) {
  .nav-pills {
    flex-wrap: wrap;
  }
  
  .nav-pills .nav-link {
    margin-bottom: 0.5rem;
    flex-basis: calc(50% - 0.5rem);
  }
}
</style>
