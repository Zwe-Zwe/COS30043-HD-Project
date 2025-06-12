<template>
  <div class="container py-5">
    <div class="row">
      <div class="col-lg-10 mx-auto">
        <h1 class="text-center mb-4">Shipping & Returns</h1>
        <p class="text-center lead mb-5">Everything you need to know about our delivery and return policies.</p>
        
        <div class="row mb-5">
          <div class="col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Shipping Information</h3>
              </div>
              <div class="card-body">
                <h4>Delivery Options</h4>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead class="table-light">
                      <tr>
                        <th>Service</th>
                        <th>Delivery Time</th>
                        <th>Cost</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Standard Shipping</td>
                        <td>3-5 business days</td>
                        <td>$5.99</td>
                      </tr>
                      <tr>
                        <td>Express Shipping</td>
                        <td>1-2 business days</td>
                        <td>$9.99</td>
                      </tr>
                      <tr>
                        <td>Next Day Delivery</td>
                        <td>Next business day</td>
                        <td>$14.99</td>
                      </tr>
                      <tr>
                        <td>Free Shipping</td>
                        <td>5-7 business days</td>
                        <td>FREE on orders over $35</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                
                <h4 class="mt-4">Shipping Restrictions</h4>
                <p>We currently ship to all locations within Australia. International shipping is available to select countries. Please note that additional fees and taxes may apply for international orders.</p>
                
                <h4 class="mt-4">Tracking Your Order</h4>
                <p>Once your order ships, you'll receive a confirmation email with tracking information. You can also track your order by logging into your account and viewing your order history.</p>
              </div>
            </div>
          </div>
          
          <div class="col-md-6 mb-4">
            <div class="card h-100">
              <div class="card-header bg-success text-white">
                <h3 class="mb-0">Returns & Refunds</h3>
              </div>
              <div class="card-body">
                <h4>Return Policy</h4>
                <p>We want you to be completely satisfied with your purchase. If you're not, we make returns easy:</p>
                <ul>
                  <li>Return items within <strong>30 days</strong> of delivery for a full refund.</li>
                  <li>Items must be in original condition, unused, and with original packaging.</li>
                  <li>Digital products and gift cards are non-refundable.</li>
                  <li>Damaged or defective items can be returned at any time.</li>
                </ul>
                
                <h4 class="mt-4">How to Return an Item</h4>
                <div class="card mb-3">
                  <div class="card-body">
                    <ol>
                      <li>Log in to your account and go to 'Order History'.</li>
                      <li>Find your order and select 'Return Items'.</li>
                      <li>Follow the prompts to complete your return request.</li>
                      <li>Print the return label and attach it to your package.</li>
                      <li>Drop off the package at any postal service location.</li>
                    </ol>
                  </div>
                </div>
                
                <h4 class="mt-4">Refund Process</h4>
                <p>Once we receive your returned item, we'll inspect it to ensure it meets our return policy requirements. After approval, your refund will be issued to your original payment method within 5-7 business days.</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header bg-light">
            <h3 class="mb-0">Frequently Asked Questions</h3>
          </div>
          <div class="card-body">
            <div class="accordion" id="shippingAccordion">
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    How long will it take to receive my order?
                  </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#shippingAccordion">
                  <div class="accordion-body">
                    Delivery times depend on your selected shipping method and your location. Standard shipping typically takes 3-5 business days, while express shipping takes 1-2 business days.
                  </div>
                </div>
              </div>
              
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Do you ship internationally?
                  </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#shippingAccordion">
                  <div class="accordion-body">
                    Yes, we ship to select countries internationally. International shipping rates and delivery times vary by destination. Additional customs fees and taxes may apply.
                  </div>
                </div>
              </div>
              
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Will I be charged for return shipping?
                  </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#shippingAccordion">
                  <div class="accordion-body">
                    Standard returns are free of charge. We provide a prepaid return shipping label for your convenience. However, if the return is not due to our error or a defective product, a shipping fee may be deducted from your refund.
                  </div>
                </div>
              </div>
              
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    How long do refunds take to process?
                  </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#shippingAccordion">
                  <div class="accordion-body">
                    Once we receive your return, it takes 1-2 business days for inspection. After approval, refunds typically take 5-7 business days to appear in your account, depending on your payment provider's processing times.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="text-center mt-5">
          <h4>Still have questions?</h4>
          <router-link to="/contact" class="btn btn-primary mt-2">Contact Our Support Team</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ShippingView'
};
</script>

<style scoped>
h1 {
  color: #343a40;
}

.card {
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
  font-weight: 600;
}

.table th, .table td {
  vertical-align: middle;
}

.accordion-button:not(.collapsed) {
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
}

.accordion-button:focus {
  box-shadow: none;
}
</style>
