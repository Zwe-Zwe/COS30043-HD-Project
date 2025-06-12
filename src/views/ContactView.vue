<template>
  <div class="container py-5">
    <div class="row">
      <div class="col-lg-8 mx-auto">
        <h1 class="text-center mb-5">Contact Us</h1>
        
        <div class="row mb-5">
          <div class="col-md-4 mb-4 mb-md-0">
            <div class="contact-info-box text-center p-4">
              <i class="bi bi-geo-alt contact-icon"></i>
              <h4>Visit Us</h4>
              <p class="mb-0">123 Book Street</p>
              <p class="mb-0">Melbourne, VIC 3000</p>
              <p>Australia</p>
            </div>
          </div>
          
          <div class="col-md-4 mb-4 mb-md-0">
            <div class="contact-info-box text-center p-4">
              <i class="bi bi-telephone contact-icon"></i>
              <h4>Call Us</h4>
              <p class="mb-0">Customer Service:</p>
              <p class="mb-0">1800 BOOKS (26657)</p>
              <p>Mon-Fri: 9am-5pm</p>
            </div>
          </div>
          
          <div class="col-md-4">
            <div class="contact-info-box text-center p-4">
              <i class="bi bi-envelope contact-icon"></i>
              <h4>Email Us</h4>
              <p class="mb-0">Customer Service:</p>
              <p class="mb-0">support@booknest.com</p>
              <p>24/7 response</p>
            </div>
          </div>
        </div>
        
        <div class="card shadow-sm">
          <div class="card-header bg-light">
            <h3 class="mb-0">Send Us a Message</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitForm">
              <div class="alert alert-success" v-if="submitted">
                Thank you for your message. We'll get back to you shortly!
              </div>
              
              <div class="mb-3">
                <label for="name" class="form-label">Your Name</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="name" 
                  v-model="formData.name" 
                  required
                  :disabled="submitted || submitting"
                >
              </div>
              
              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  v-model="formData.email" 
                  required
                  :disabled="submitted || submitting"
                >
              </div>
              
              <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <select 
                  class="form-select" 
                  id="subject" 
                  v-model="formData.subject"
                  required
                  :disabled="submitted || submitting"
                >
                  <option value="">Select a subject</option>
                  <option value="order-inquiry">Order Inquiry</option>
                  <option value="product-question">Product Question</option>
                  <option value="returns-refunds">Returns & Refunds</option>
                  <option value="website-feedback">Website Feedback</option>
                  <option value="other">Other</option>
                </select>
              </div>
              
              <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea 
                  class="form-control" 
                  id="message" 
                  rows="5" 
                  v-model="formData.message" 
                  required
                  :disabled="submitted || submitting"
                ></textarea>
              </div>
              
              <button 
                type="submit" 
                class="btn btn-primary" 
                :disabled="submitted || submitting"
              >
                <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                {{ submitting ? 'Sending...' : 'Submit' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContactView',
  data() {
    return {
      formData: {
        name: '',
        email: '',
        subject: '',
        message: ''
      },
      submitting: false,
      submitted: false
    };
  },
  methods: {
    submitForm() {
      this.submitting = true;
      
      // Simulate API call
      setTimeout(() => {
        // Form submission would normally go to server
        console.log('Form submitted:', this.formData);
        this.submitting = false;
        this.submitted = true;
        
        // Reset form after successful submission
        this.formData = {
          name: '',
          email: '',
          subject: '',
          message: ''
        };
      }, 1500);
    }
  }
};
</script>

<style scoped>
.contact-info-box {
  border-radius: 8px;
  background-color: #f8f9fa;
  height: 100%;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.contact-info-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.contact-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  color: #0d6efd;
  display: block;
}

h4 {
  font-size: 1.25rem;
  margin-bottom: 1rem;
}
</style>
